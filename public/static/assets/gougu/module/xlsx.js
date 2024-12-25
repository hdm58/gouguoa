layui.define(['jquery','layer','table'], function(exports) {
	var $ = layui.$,
	layer = layui.layer,
	table = layui.table;
	
   	var MOD_NAME = 'xlsx';
	var modFile = layui.cache.modules['xlsx'];
	var modPath = modFile.substr(0, modFile.lastIndexOf('.'));
	
    var tableXlsx=$.extend({},table);
	
    tableXlsx._render = tableXlsx.render;

	tableXlsx.excel = function(data,page_size,obj,filter,merge){
		//表头工具栏导出按钮
		$('[lay-id="'+obj.id+'"]').find('[lay-event="LAYTABLE_EXCEL"]').off().on('click',function(){
			if(data.count==0){
				layer.msg('暂无数据');
				return false;
			}
			else{
				let _page = parseInt(data.count/page_size);
				let page = data.count%page_size>0?(_page+1):_page;
				let pageHtml='<p style="padding:16px 10px 0; text-align:center; color:red">由于导出数据比较消耗服务器资源，建议使用搜索功能筛选好数据再导出</p><p style="padding:5px 10px 10px; text-align:center; color:red">如果点击导出后，没有立即导出文件，请耐心等待一下，30秒内请勿重复点击。</p><p style="padding:0 10px; text-align:center;">共查询到<strong> '+data.count+' </strong>条数据，每次最多导出<strong>'+page_size+'</strong>条，共<strong>'+page+'</strong>页，请点击下面的页码导出</p><div id="exportPage" class="layui-box layui-laypage" style="padding:10px 0; width:100%;text-align:center;">';
				for (i = 1; i <= page; i++) {
					pageHtml += '<a href="javascript:;" data-page="'+i+'">'+i+'</a>';
				}
				pageHtml+='</div>';							
				layer.open({
					  type: 1,
					  title: '导出数据',
					  area:['580px','240px'],
					  content: pageHtml,
					  success:function(res){
							var tableWhere = JSON.parse(JSON.stringify(obj.where));
							tableWhere.limit=page_size;										
							$('#exportPage').on('click','a',function(){
								tableWhere.page=$(this).data('page');
								let msg = layer.msg('正在导出数据...', {time:5000});
								$.ajax({
									url: obj.url,
									data: tableWhere,
									success:function(res){
										//table.exportFile(obj.id, res.data,'xls');
										//console.log(res.data);
										tableXlsx.xsls(res.data,obj.cols[0],obj.title,filter,merge);
										layer.close(msg);
									}
								});
							})
						}
					});
				return false;
			}
		});
	}
	
	tableXlsx.xsls = function(jsonData,field,name,filter,merge){
		let new_jsonData;
		//数据过滤
		if(filter.length>0){
			new_jsonData = jsonData.map((item, index) => {
				const newProps = {}; // 创建一个空对象，用于存放新增的属性和对应的计算值
				filter.forEach((obj) => {
				for (const [key, func] of Object.entries(obj)) {
					newProps[key] = func(item); // 调用函数计算属性值，并赋给新的属性
				}
			});
			return { ...item, ...newProps }; // 创建新对象，将原有的属性展开并新增 newProps 中的属性
			});
		}
		else{
			new_jsonData = jsonData;
		}
		console.log("===========打印初始化后的json数据============");
		console.log(new_jsonData);

		//需要显示的表格字段,及表头文字描述
		let field_array =[],customHeaders = [];
		for (var f=0; f<field.length;f++){
			if(!field[f]['ignoreExport']){
				field_array.push(field[f].field);
				customHeaders.push(field[f].title);
			}
		}
		console.log("===========打印需要显示的表格字段及表头文字描述============");
		console.log(field_array);
		console.log(customHeaders);
		
		const desiredOrder = field_array; // 按照这个顺序重新排序
		const desiredProps = field_array; // 只保留这些属性
		
		// 对 JSON 数据集进行排序和过滤
		const sortedAndFilteredData = new_jsonData.map(entry => {
			const sortedAndFilteredEntry = {};
			desiredOrder.forEach(key => {
				if (desiredProps.includes(key) && entry.hasOwnProperty(key)) {
					sortedAndFilteredEntry[key] = entry[key];
				}
			});
			return sortedAndFilteredEntry;
		});
		console.log("===========打印排序和过滤处理过的JSON 数据集============");
		console.log(sortedAndFilteredData);
		
		//设置表头
		const worksheet = XLSX.utils.json_to_sheet(sortedAndFilteredData);
		const headerRowIndex = 0;
		customHeaders.forEach((header, columnIndex) => {
			const address = XLSX.utils.encode_cell({ r: headerRowIndex, c: columnIndex });
			if (worksheet[address]) {
				worksheet[address].v = header;
			}
		});

		//设置需要合并的单元格
		var mergeCells = [];
		if(!isObjectEmpty(merge)){
			for (var i = 1; i < sortedAndFilteredData.length; i++) {
				if (sortedAndFilteredData[i][merge.target] === sortedAndFilteredData[i - 1][merge.target]) {
					// 数据相同，合并单元格
					let sons = getSonIndex(sortedAndFilteredData,merge);
					//console.log(sons);
					for(var m=0; m<sons.length;m++){
						mergeCells.push({
							s:{r: i, c: sons[m]},e:{r: i+1, c: sons[m]}
						});
					}          
				}
			}
		}
		console.log("===========打印需要合并的单元格集合============");
		console.log(mergeCells);
		if (mergeCells.length > 0) {
			worksheet['!merges'] = mergeCells;
		}
		const workbook = XLSX.utils.book_new();
		XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
		setTimeout(function(){
			layer.msg('导出完成');			
		},3000)		
		// Export to Excel file
		XLSX.writeFile(workbook, name+'.xlsx');		
	}
	
	//重写渲染方法
    tableXlsx.render=function(params){
		let is_excel = params.is_excel||false;
		let excel_limit = params.excel_limit||1000;
		let filter = params.xlsx_filter||[];
		let merge = params.xlsx_merge||{};
		if(is_excel){
			let toolbar = ['filter', {title:'导出EXCEL',layEvent: 'LAYTABLE_EXCEL',icon: 'layui-icon-export'}];
			if(!params.defaultToolbar){
				params.defaultToolbar = toolbar;
			}	
			else{
				let _toolbar = params.defaultToolbar;
				params.defaultToolbar = _toolbar.concat(toolbar);
			}
			if(typeof params.done === "function"){
				let _done = params.done;
				params.done = function(data, curr, count){
					let obj = this;
					_done(data, curr, count);
					if(!isObjectEmpty(merge)){
						tableMerge (obj);
					}
					tableXlsx.excel(data,excel_limit,obj,filter,merge);
				}
			}
			else{
				params.done = function(data){
					let obj = this;
					if(!isObjectEmpty(merge)){
						tableMerge (obj);
					}
					tableXlsx.excel(data,excel_limit,obj,filter,merge);
				}
			}
		}
		var init = tableXlsx._render(params);
		return init;
		//console.log(params);
    };
	
	function tableMerge (myTable) {
		var tableBox = $(myTable.elem).next().children('.layui-table-box'),
			$main = $(tableBox.children('.layui-table-body').children('table').children('tbody').children('tr').toArray().reverse()),
			$fixLeft = $(tableBox.children('.layui-table-fixed-l').children('.layui-table-body').children('table').children('tbody').children('tr').toArray().reverse()),
			$fixRight = $(tableBox.children('.layui-table-fixed-r').children('.layui-table-body').children('table').children('tbody').children('tr').toArray().reverse()),
			mergeRecord = {},cols = myTable.cols[0];

		for (let i = 0; i < cols.length; i++) {
			var item3 = cols[i], field=item3.field;
			if (item3.merge) {
				var mergeField = [field];
				if (item3.merge !== true) {
					if (typeof item3.merge == 'string') {
						mergeField = [item3.merge]
					} else {
						mergeField = item3.merge
					}
				}
				mergeRecord[i] = {mergeField: mergeField, rowspan:1}
			}
		}
		//console.log(myTable);
		$main.each(function (i) {
			for (var item in mergeRecord) {
				if (i==$main.length-1 || isMaster(i, item)) {
					$(this).children('[data-key$="-'+item+'"]').attr('rowspan', mergeRecord[item].rowspan).css('position','static');
					$fixLeft.eq(i).children('[data-key$="-'+item+'"]').attr('rowspan', mergeRecord[item].rowspan).css('position','static');
					$fixRight.eq(i).children('[data-key$="-'+item+'"]').attr('rowspan', mergeRecord[item].rowspan).css('position','static');
					mergeRecord[item].rowspan = 1;
				} else {
					$(this).children('[data-key$="-'+item+'"]').remove();
					$fixLeft.eq(i).children('[data-key$="-'+item+'"]').remove();
					$fixRight.eq(i).children('[data-key$="-'+item+'"]').remove();
					mergeRecord[item].rowspan +=1;
				}
			}
		})
		function isMaster (index, item) {
			var mergeField = mergeRecord[item].mergeField;
			var dataLength = layui.table.cache[myTable.id].length;
			for (var i=0; i<mergeField.length; i++) {

				if (layui.table.cache[myTable.id][dataLength-2-index][mergeField[i]]
					!== layui.table.cache[myTable.id][dataLength-1-index][mergeField[i]]) {
					return true;
				}
			}
			return false;
		}
    }
	
	function getSonIndex(targetData,mergeField){
		let index=[];
		index.push(Object.keys(targetData[0]).indexOf(mergeField.target));
		if(mergeField.son.length>0){
			for(var a=0; a<mergeField.son.length;a++){
				index.push(Object.keys(targetData[0]).indexOf(mergeField.son[a]));
			}
		}
		return index;
	}
	function isObjectEmpty(obj) {
		return JSON.stringify(obj) === '{}';
	}
	function loadScript() {
		if (typeof XLSX == 'undefined') {
			$.ajax({ //获取插件
				url: modPath + '/xlsx.full.min.js' ,
				dataType: 'script',
				cache: true,
				async: false
			});
		}
	}
	loadScript();
    exports(MOD_NAME, tableXlsx);
});
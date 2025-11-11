mbui.define(['tool','layer'], function (exports) {
	const tool = mbui.tool,layer = mbui.layer;
	let searchTimer;
	const dataTypes = {
		'property':{
			title:'选择固定资产',
			url:'/adm/api/get_property',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.title + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.title + '</div>\
									<div class="f12"><span class="text-gray">编号：</span>'+item.code+'<span class="text-gray" style="margin-left:12px;">分类：</span>'+item.cate+'</div>\
								</div>\
							</label>'
			}
		},
		'car':{
			title:'选择车辆信息',
			url:'/adm/api/get_car',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.title + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.title + '</div>\
									<div class="f12"><span class="text-gray">车牌号：</span>'+item.name+'<span class="text-gray" style="margin-left:12px;">座位数：</span>'+item.seats+'</div>\
								</div>\
							</label>'
			}
		},
		'room':{
			title:'选择会议室',
			url:'/adm/api/get_meeting_room',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.title + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.title + '</div>\
									<div class="f12"><span class="text-gray">设备：</span>'+item.device+'<span class="text-gray" style="margin-left:12px;">可容纳人数：</span>'+item.num+'</div>\
								</div>\
							</label>'
			}
		},
		'loan':{
			title:'选择抵消借支',
			url:'/finance/api/get_loan',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.title + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.title + '</div>\
									<div class="f12"><span class="text-gray">状态：</span>'+item.balance_name+'<span class="text-gray" style="margin-left:12px;">可抵消余额：</span>'+item.un_balance_cost+'</div>\
								</div>\
							</label>'
			}
		},
		'customer':{
			title:'选择客户',
			url:'/customer/api/get_customer',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.name + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.name + '</div>\
									<div class="f12"><span class="text-gray">所属员工：</span>'+item.belong_name+'<span class="text-gray" style="margin-left:12px;">所属部门：</span>'+item.belong_department+'</div>\
								</div>\
							</label>'
			}
		},
		'supplier':{
			title:'选择供应商',
			url:'/contract/api/get_supplier',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.title + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.title + '</div>\
									<div class="f12"><span class="text-gray">联系人：</span>'+item.contact_name+'<span class="text-gray" style="margin-left:12px;">联系电话：</span>'+item.contact_mobile+'</div>\
								</div>\
							</label>'
			}
		},
		'contract':{
			title:'选择销售合同',
			url:'/contract/api/get_contract',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.name + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.name + '</div>\
									<div class="f12"><span class="text-gray">合同编号：</span>'+item.code+'<span class="text-gray" style="margin-left:12px;">签订人：</span>'+item.sign_name+'</div>\
								</div>\
							</label>'
			}
		},
		'product':{
			title:'选择产品',
			url:'/contract/api/get_product',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.title + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.title + '</div>\
									<div class="f12"><span class="text-gray">编码：</span>'+item.code+'<span class="text-gray" style="margin-left:12px;">分类：</span>'+item.cate+'</div>\
								</div>\
							</label>'
			}
		},
		'purchase':{
			title:'选择采购合同',
			url:'/contract/api/get_purchase',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.name + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.name + '</div>\
									<div class="f12"><span class="text-gray">合同编号：</span>'+item.code+'<span class="text-gray" style="margin-left:12px;">签订人：</span>'+item.sign_name+'</div>\
								</div>\
							</label>'
			}
		},
		'purchased':{
			title:'选择采购物品',
			url:'/contract/api/get_purchased',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.title + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.title + '</div>\
									<div class="f12"><span class="text-gray">编码：</span>'+item.code+'<span class="text-gray" style="margin-left:12px;">分类：</span>'+item.cate+'</div>\
								</div>\
							</label>'
			}
		},
		'project':{
			title:'选择项目',
			url:'/project/api/get_project',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.name + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.name + '</div>\
									<div class="f12"><span class="text-gray">项目类型：</span>'+item.cate+'<span class="text-gray" style="margin-left:12px;">项目负责人：</span>'+item.director_name+'</div>\
								</div>\
							</label>'
			}
		},
		'task':{
			title:'选择任务',
			url:'/project/api/get_task',
			searchbar:'<div class="mbui-picker-search"><input type="search" class="search-input" placeholder="输入关键字..."></div>',
			template:function (item,types){
				return '<label class="mbui-picker-item" data-id="' + item.id + '" data-title="' + item.title + '">\
								<input class="mbui-input-'+types+'" name="radio_picker[]" type="'+types+'" value="' + item.id + '">\
								<div style="margin-left:8px;">\
									<div class="line-limit-1">' + item.title + '</div>\
									<div class="f12"><span class="text-gray">负责人：</span>'+item.director_name+'<span class="text-gray" style="margin-left:12px;">任务状态：</span>'+item.status_name+'</div>\
								</div>\
							</label>'
			}
		}
	};
	//html转义，防止XSS
	function escapeHtml(text) {
	  var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	  };
	  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}
	
	var LoadData= function () {
		this.config = {
			title: "请选择内容",
			url: "",
			searchbar:"",
			type: "radio",
			where: {},
			limit: 20,
			data:[],
			selectData:[],
			template: function (data) {
				return JSON.parse(data);
			},
			callback: function(){}
		};
		this.index = 0;
		this.loaded = 0;
		this.page = 1;
		this.count = 0;
		this.total = 0;
	};
	// 初始化
	LoadData.prototype.init = function (options,types) {
		var that = this;
		let opts={};
		if (types != "undefined") {
			opts = dataTypes[types];
		}
		$.extend(true,that.config,opts,options);
		//console.log(that.config);
		that.index = new Date().getTime();
		var $container = $('<div class="mbui-picker-selector"><header class="mbui-bar"><a class="mbui-bar-item left" href="javascript:;"><i class="mbui-bar-arrow-left"></i>关闭</a><a class="mbui-bar-item right text-blue" href="javascript:;">确认</a><h1 class="mbui-bar-title">'+that.config.title+'</h1></header></div>');
		$container.append(that.config.searchbar);
		$container.append('<div class="load-data-container lists" id="lists_'+that.index+'"><div id="container_'+that.index+'"></div><div class="load-data-none"><i class="iconfont icon-none"></i><br>暂无数据</div><div class="load-data-loading"><span>努力加载中</span></div><div class="load-data-end"><span>—— ● ——</span></div></div>');
		$('body').append($container);
		$('#root').hide();
		
		$container.find('.left').click(function () {
			$container.fadeOut(function () {
				$container.remove();
				$('#root').show();
			});
		});
		
		$container.on('input','.search-input',function() {
			 const key = $(this).val().trim();
			 // 清除上一次的定时器
			 clearTimeout(searchTimer);
			 // 设置新的延迟请求
			 searchTimer = setTimeout(() => {
				that.config.where['keywords']=key;
				that.page=1;
				that.ajax();
			 }, 400);
		});
		
		$container.on('click','.tags-search',function() {
			 const key = $(this).data('key');
			 const val = $(this).data('val');
			 $(this).addClass('active').siblings().removeClass('active');
			 // 清除上一次的定时器
			 clearTimeout(searchTimer);
			 // 设置新的延迟请求
			 searchTimer = setTimeout(() => {
				that.config.where[key]=val;
				that.page=1;
				that.ajax();
			 }, 400);
		});

		$container.find('.right').click(function () {
			let selected = $container.find('input:checked');
			if (selected.length == 0) {
				layer.msg('请选择数据');
				return false;
			}
			that.config.selectData.length=0;
			let ids=[];
			for (var m = 0; m < selected.length; m++) {
				let selected_item = $(selected[m]).parent();
				let id=selected_item.data('id');
				ids.push(id);
			}
			that.config.selectData = that.config.data.filter(item => ids.includes(item.id));
			that.config.callback(that.config.selectData);
			$container.fadeOut(function () {
				$container.remove();
				$('#root').show();
			});
		});
		
		$('#lists_'+that.index).scroll(function(){
			if ($(this).scrollTop() + $('body').height() >= $('#container_'+that.index).height()-60) {
				console.log($(this).scrollTop());
				console.log('<br>');
				console.log($('body').height());
				console.log('<br>');
				console.log($('#container_'+that.index).height());
				// 滚动到页面底部时加载更多数据
				if (that.total < that.count && that.loaded == 0){
					that.ajax();
				}
			}
		});
		that.ajax();
	};

	LoadData.prototype.ajax = function () {
		var that = this;
		var elem = $('#container_'+that.index);
		var container = elem.parent();
		//console.log(container.html());
		// 发送请求获取数据
		$.ajax({
			url: that.config.url + '?page=' + that.page + '&limit=' + that.config.limit,
			type: 'GET',
			data: that.config.where,
			beforeSend: function () {
				// 显示加载按钮
				that.loaded = 1;
				container.find('.load-data-loading').show();
				container.find('.load-data-end').hide();
			},
			success: function (res) {
				that.count=res.count;
				that.total+=res.data.length;
				container.find('.load-data-none').attr('class','load-data-none load-data-'+that.count);
				if(that.page==1){
					that.config.data.length=0;
					elem.html('');
				}
				if (res.count > 0) {
					that.page++;
					that.config.data.push(...res.data);
					$.each(res.data, function (index, item) {
						// 转义JSON对象中的字符串值,防止XSS
						for (var key in item) {
						  if (typeof item[key] === 'string') {
							item[key] = escapeHtml(item[key]);
						  }
						}
						// 创建列表项并添加到列表中
						var listItem = that.config.template(item,that.config.type);
						elem.append(listItem);
					});
					container.find('.load-data-end').show();
				}
				else{
					that.config.data.length=0;
					that.page=1;
				}	
			},
			complete: function () {
				that.loaded = 0;
				container.find('.load-data-loading').hide();
				container.find('.load-data-end').show();
			}
		});
	}
	
	//选择员工弹窗		
	$('body').on('click','.picker-data',function () {
		let that = $(this);
		let types = that.data('types');
		let type = that.data('type');
		let where = that.data('where');
		if (typeof(type) == "undefined" || type == '') {
			type = 1;
		}
		type = type == 2 ? 'checkbox' : 'radio';

		if (typeof(types) == "undefined" || types == '') {
			layer.msg('请设置【picker】的类型');
			return false;
		}

		if (typeof(where) == "undefined" || where == '') {
			map = {};
		}
		else{
			map = JSON.parse(where);
		}
		let picker = new LoadData();
		picker.init({
			type:type,
			where: map,
			callback:function(selectData){
				let ids=[],titles=[];
				for ( var i = 0; i <selectData.length; i++){
					ids.push(selectData[i].id);
					if(!selectData[i].title){
						titles.push(selectData[i].name);
					}else{
						titles.push(selectData[i].title);
					}
				}
				that.val(titles.join(','));
				that.next().val(ids.join(','));
			}
		},types);
	});

	// 导出loadData模块
	exports('dataPicker', function (options,types) {
		var dataPicker = new LoadData();
		dataPicker.init(options,types);
	});
});
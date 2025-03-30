mbui.define(['layer'], function (exports) {
var layer = mbui.layer;
var layPicker = {
	index: 0, // 递增的index，作为元素的唯一标识
	indexList: [], // 每个index的集合
	offset: {}, // 每个ul元素的滚动位置
	liHeight: 50, // 每个li的高度
	selectList: {}, // 存储每个被选中的值，li下标
	textField: 'name', // 参数名称-内容
	valueField: 'value',// 参数名称-值
	fieldList: {}, // 参数名称集
	dataList: {}, // 数据集合
	tempSearchList: {}, // 临时存储搜索前的主数据
	minMaxData: {}, // 最小最大限制数据
	init: function(data){
		layPicker.index ++;
		layPicker.indexList.push(layPicker.index);
		var key = ''+layPicker.index;
		// 参数名称替换
		layPicker.fieldList[key] = {
			textField: data.textField || layPicker.textField,
			valueField: data.valueField || layPicker.valueField,
		};
		// 滚动位置初始化
		layPicker.offset[key] = {
			start: {},
			move: {},
			end: {state: false},
		};
		// 日期选择器-获取对应值
		if(data.options){
			var minObj = lay_picker_date.getMinOrMaxDate(data.minDate);
			var maxObj = lay_picker_date.getMinOrMaxDate(data.maxDate);
			layPicker.minMaxData[key] = {};
			layPicker.minMaxData[key]['minObj'] = minObj;
			layPicker.minMaxData[key]['maxObj'] = maxObj;
			layPicker.minMaxData[key]['options'] = data.options;
			data.data = lay_picker_date.getData(data.options, key);
		}
		// 选中值初始化
		layPicker.selectList[key] = {};
		// 数据集
		layPicker.dataList[key] = data.data;
		// 渲染标签
		var html = layPicker.initBody(data, key);
		$('body').append(html);
		// 给绑定元素添加标识
		//data.elem.attr('readonly', true);
		data.elem.attr('lay-picker-id', key);

		// 不等于自定义html才需要执行这些操作
		if(!data.type || data.type != 3){
			// 初始化标签和值
			layPicker.initBox(data.data, key);
			layPicker.initSelect(key);
			// 日期选择器-赋值
			if(data.options){
				layPicker.setValue(key, lay_picker_date.getVoluation(data.options, layPicker.fieldList[key].valueField), data.options);
			}
			// 搜索监听
			if(data.search){
				layPicker.tempSearchList[key] = data.data[0];
				var temp_keyup;
				$('#lay-picker-input-'+key).on('keyup', function() {
				    var value = $(this).val();
					clearTimeout(temp_keyup);
					temp_keyup = setTimeout(function(){
						var tempArray = [];
						if(value){
							var arr = layPicker.tempSearchList[key];
							for(var i=0; i<arr.length; i++){
								var obj = arr[i];
								var text = layPicker.fieldList[key].textField;
								if(obj[text].indexOf(value) != -1){
									tempArray.push(obj);
								}
							}
						}else{
							tempArray = layPicker.tempSearchList[key];
						}
						layPicker.setData(key, -1, tempArray);
					}, 500)
				});
			}
			// 滑动-开始（手指放在页面时触发）
			$('.lay-picker-'+key).on('touchstart', '.lay-picker-list-wrap', function(e) {
				var touch = e.originalEvent.targetTouches[0];
				if(layPicker.tempInter){
					clearInterval(layPicker.tempInter);
					layPicker.tempInter = null;
					layPicker.offset[key].move.y = touch.clientY;
				}
				layPicker.offset[key].move.len = 0;
				layPicker.offset[key].end.state = true;
				layPicker.offset[key].start.y = touch.clientY;
				var ul = $(this).find('ul')[0];
				layPicker.offset[key].start.top = parseInt(ul.style.transform.split(",")[1].replace('px', '')) || 0;
				e.stopPropagation(); // 防止屏幕滑动
			});
			// 滑动-中（手指移动时触发）
			$('.lay-picker-'+key).on('touchmove', '.lay-picker-list-wrap', function(e) {
				var touch = e.originalEvent.targetTouches[0];
				layPicker.offset[key].move.y = touch.clientY;
				var y = layPicker.offset[key].move.y - layPicker.offset[key].start.y + layPicker.offset[key].start.top;
				$(this).find('ul').css('transform', 'translate3d(0px, ' + y + 'px, 0px)');
				layPicker.offset[key].end.y = y;
				e.stopPropagation();
				e.preventDefault();// 防止屏幕滑动
			});
			// 滑动-结束（手指离开页面时触发）
			$('.lay-picker-'+key).on('touchend', '.lay-picker-list-wrap', function(e) {
				layPicker.offset[key].move.state = true;
				var tempLen = 1;
				if(layPicker.offset[key].start.y > layPicker.offset[key].move.y){
					layPicker.offset[key].move.num = true;
					tempLen = layPicker.offset[key].start.y - layPicker.offset[key].move.y;
				}else{
					layPicker.offset[key].move.num = false;
					tempLen = layPicker.offset[key].move.y - layPicker.offset[key].start.y;
				}
				tempLen = tempLen / layPicker.liHeight;
				var _this = this;
				var ul = $(_this).find('ul')[0], ul2 = $(_this).find('ul');
				var tempNum = 1;
				layPicker.tempInter = setInterval(function(){
					tempNum ++;
					var tempTop = layPicker.offset[key].start.top;
					layPicker.offset[key].start.top = parseInt(ul.style.transform.split(",")[1].replace('px', '')) || 0;
					if(tempLen <= 3
						|| tempTop >= 0
						|| layPicker.offset[key].start.top >= 0
						|| ((ul.children.length - 1) * layPicker.liHeight) <= Math.abs(tempTop)
						|| ((ul.children.length - 1) * layPicker.liHeight) <= Math.abs(layPicker.offset[key].start.top)){
						tempNum = tempLen;
					}else{
						layPicker.offset[key].start.y = layPicker.offset[key].move.y;
						if(layPicker.offset[key].move.num){
							layPicker.offset[key].move.y = layPicker.offset[key].move.y - ((tempLen - tempNum) * layPicker.liHeight);
						}else{
							if(layPicker.offset[key].move.y < 0){
								layPicker.offset[key].move.y = layPicker.offset[key].move.y - ((tempLen - tempNum) * layPicker.liHeight);
							}else{
								layPicker.offset[key].move.y = layPicker.offset[key].move.y + ((tempLen - tempNum) * layPicker.liHeight);
							}
						}
						var y = layPicker.offset[key].move.y - layPicker.offset[key].start.y + layPicker.offset[key].start.top;
						ul2.css('transform', 'translate3d(0px, ' + y + 'px, 0px)');
						layPicker.offset[key].end.y = y;
					}
					// 在这前面的都是实现延迟继续滚动效果,这下面的则是原来的效果
					if(tempNum >= tempLen){
						clearInterval(layPicker.tempInter);
						layPicker.offset[key].end.state = false;
						var itemHeight = layPicker.liHeight;
						var sign = layPicker.offset[key].end.y >= 0 ? 1 : -1;
						var thisIndex = $(_this).attr("index");
						// 计算超出回弹
						var fieldIndex = Math.round(Math.abs(layPicker.offset[key].end.y) / itemHeight);
			            var len = sign * (fieldIndex * itemHeight);
			            if (len > 0) {
			                len = 0;
			                fieldIndex = 0;
			            } else if (len < -(ul.children.length - 1) * itemHeight) {
			                len = -(ul.children.length - 1) * itemHeight;
			                fieldIndex = ul.children.length - 1;
			            };
			            ul2.css('transform', 'translate3d(0px, ' + len + 'px, 0px)');
						layPicker.selectList[key][thisIndex] = fieldIndex;
						// 获取选中值
						var list = layPicker.dataList[key];
						var resultArray = [];
						for(var k in layPicker.selectList[key]){
							var arr = list[Number(k)]
							if(arr && arr.length > 0){
								var obj = arr[layPicker.selectList[key][k]]
								resultArray.push(obj);
							}
						}
						// 回调
						if(data.onSelect){
							data.onSelect(key, thisIndex, resultArray);
						}
						// 日期选择器-赋值day
						if(data.options){
							var valList = layPicker.setOptions(data.options, key, resultArray, thisIndex);
							layPicker.setValue(key, valList); // 赋值，以位置类推
						}
					}
				}, 100)
				e.stopPropagation();// 防止屏幕滑动
			});
		  	// 取消-点击
			$('.lay-picker-'+key).on("click", ".lay-picker-cancel-click", function(e){
				layPicker.hiden($(this).parents('.lay-picker').attr('lay-index'));
				if(data.onCancel){
					data.onCancel(key);
				}
		  	});
		  	// 确认-点击
			$('.lay-picker-'+key).on("click", ".lay-picker-confirm-click", function(e){
				layPicker.hiden($(this).parents('.lay-picker').attr('lay-index'));
				var list = layPicker.dataList[key];
				var v = '', resultArray = [];
				for(var k in layPicker.selectList[key]){
					var arr = list[Number(k)]
					if(arr && arr.length > 0){
						var obj = arr[layPicker.selectList[key][k]]
						if(data.options && (data.options == 'time' || data.options == 'timesecond' || data.options == 'datetime' || data.options == 'datetimesecond')){
							if(data.options == 'time' || data.options == 'timesecond'){
								v += obj[layPicker.fieldList[key].valueField] + ":";
							}else if((data.options == 'datetime' || data.options == 'datetimesecond') && Number(k) == 2){
								v += obj[layPicker.fieldList[key].valueField] + " ";
							}else if((data.options == 'datetime' || data.options == 'datetimesecond') && Number(k) > 2){
								v += obj[layPicker.fieldList[key].valueField] + ":";
							}else{
								v += obj[layPicker.fieldList[key].valueField] + "-";
							}
						}else{
							v += obj[layPicker.fieldList[key].valueField] + "-";
						}
						resultArray.push(obj);
					}
				}
				if(data.onConfirm){
					data.onConfirm(key, v.substring(0, v.length-1), resultArray);
				}
		  	});
		  	// 清除-点击
			$('.lay-picker-'+key).on("click", ".lay-picker-clear-click", function(e){
				if(data.onClear){
					data.onClear(key);
				}
		  	});
		}
		// 绑定元素-点击
		data.elem.on('click', function(e){
			layPicker.show($(this).attr('lay-picker-id'));
			if(data.onShow){
				data.onShow(key);
			}
		})
		// 遮罩-点击
		$('.lay-picker-'+key).on("click", ".lay-picker-shade", function(e){
			layPicker.hiden($(this).parents('.lay-picker').attr('lay-index'));
			if(data.onShade){
				data.onShade(key);
			}
	  	});
	  	// 成功回调
		if(data.onSuccess){
			data.onSuccess(key, '.lay-picker-'+key);
		}
	  	return key;
	},
	/** 重置值*/
	setOptions: function(options, key, resultArray, thisIndex){
		var valList = [];
		if(options == 'month' ||  options == 'date' || options == 'datetime' || options == 'datetimesecond'){
			if(thisIndex == 0){
				layPicker.setData(key, Number(thisIndex), lay_picker_date.getMonth(resultArray, key));
				if(options != 'month'){
					layPicker.setData(key, Number(thisIndex) + 1, lay_picker_date.getDay(resultArray, key));
				}
			}else if(thisIndex == 1 && options != 'month'){
				layPicker.setData(key, Number(thisIndex), lay_picker_date.getDay(resultArray, key));
			}
			if(options != 'month'){
				var tempDay = resultArray[2][layPicker.fieldList[key].valueField];
				var lastDay = lay_picker_date.getLastDay(resultArray);
				if(Number(tempDay) > lastDay){
					tempDay = lastDay;
				}
				valList = [
					{value: resultArray[0][layPicker.fieldList[key].valueField]},
					{value: resultArray[1][layPicker.fieldList[key].valueField]},
					{value: tempDay},
				];
				if(options == 'datetime' || options == 'datetimesecond'){
					if(thisIndex == 0){
						layPicker.setData(key, Number(thisIndex) + 2, lay_picker_date.getHours(resultArray, key));
						layPicker.setData(key, Number(thisIndex) + 3, lay_picker_date.getMinutes(resultArray, key));
					}else if(thisIndex == 1){
						layPicker.setData(key, Number(thisIndex) + 1, lay_picker_date.getHours(resultArray, key));
						layPicker.setData(key, Number(thisIndex) + 2, lay_picker_date.getMinutes(resultArray, key));
					}else if(thisIndex == 2){
						layPicker.setData(key, Number(thisIndex) , lay_picker_date.getHours(resultArray, key));
						layPicker.setData(key, Number(thisIndex) + 1, lay_picker_date.getMinutes(resultArray, key));
					}else if(thisIndex == 3){
						layPicker.setData(key, thisIndex, lay_picker_date.getMinutes(resultArray, key));
					}
					valList.push({value: resultArray[3][layPicker.fieldList[key].valueField]})
					valList.push({value: resultArray[4][layPicker.fieldList[key].valueField]})
					if(options == 'datetimesecond'){
						if(thisIndex == 0){
							layPicker.setData(key, Number(thisIndex) + 4, lay_picker_date.getSeconds(resultArray, key));
						}else if(thisIndex == 1){
							layPicker.setData(key, Number(thisIndex) + 3, lay_picker_date.getSeconds(resultArray, key));
						}else if(thisIndex == 2){
							layPicker.setData(key, Number(thisIndex) + 2, lay_picker_date.getSeconds(resultArray, key));
						}else if(thisIndex == 3){
							layPicker.setData(key, Number(thisIndex) + 1, lay_picker_date.getSeconds(resultArray, key));
						}else if(thisIndex == 4){
							layPicker.setData(key, Number(thisIndex), lay_picker_date.getSeconds(resultArray, key));
						}
						valList.push({value: resultArray[5][layPicker.fieldList[key].valueField]})
					}
				}
			}
		}
		if(options == 'time' || options == 'timesecond'){
			if(thisIndex == 0){
				layPicker.setData(key, thisIndex, lay_picker_date.getMinutes(resultArray, key, 1));
			}
			valList = [
				{value: resultArray[0][layPicker.fieldList[key].valueField]},
				{value: resultArray[1][layPicker.fieldList[key].valueField]},
			];
			if(options == 'timesecond'){
				if(thisIndex == 0){
					layPicker.setData(key, Number(thisIndex) + 1, lay_picker_date.getSeconds(resultArray, key, 1));
				}else if(thisIndex == 1){
					layPicker.setData(key, Number(thisIndex), lay_picker_date.getSeconds(resultArray, key, 1));
				}
				valList.push({value: resultArray[2][layPicker.fieldList[key].valueField]});
			}
		}
		return valList;
	},
	initBody: function(data, index){
		var s = '';
		s += '<div class="lay-picker lay-picker-'+index+'" lay-index="'+index+'" style="display: none;">';
		if(data.shade !== false){
			if(data.shade){
				s += '	<div class="lay-picker-shade" style="background: rgba(0, 0, 0, '+data.shade+');"></div>';
			}else{
				s += '	<div class="lay-picker-shade"></div>';
			}
		}
		var cancel = '取消',clear = '清空', confirm = '确定';
		if(data.btns && data.btns.length > 0){
			cancel = data.btns[0];
			clear = data.btns[1];
			confirm = data.btns[2];
		}
		if(data.type == 3){
			var radius = (data.radius?'border-radius: '+data.radius+'px '+data.radius+'px 0px 0;':'');
			s += '	<div class="lay-picker-container" style=";transform: translate3d(0px, 100%, 0px);'+radius+'">';
			s += '		<div class="lay-picker-content" style="overflow: auto;">';
			s += (data.content || '');
			s += '		</div>';
			s += '	</div>';
			s += '</div>';
		}else if(data.type == 2){
			var sumHeight = 350; // 高度超出出现滚动条
			sumHeight = (data.title) ? sumHeight += 80 : sumHeight;
			sumHeight = (data.search) ? sumHeight += 80 : sumHeight;
			var h = $(window).height();
			var radius = (data.radius?'border-radius: '+data.radius+'px '+data.radius+'px 0px 0;':'');
			s += '	<div class="lay-picker-container" style="transform: translate3d(0px, 100%, 0px);'+radius+''+(sumHeight > h ? 'overflow: auto;height: 100%;':'')+'">';
			if(data.title){
				s += '		<div class="lay-picker-header" style="'+radius+'">';
				s += '			<div class="lay-picker-title">'+(data.title || '')+'</div>';
				s += '		</div>';
			}
			if(data.search){
				s += '		<div class="lay-picker-search" style="'+radius+'">';
				s += '			<input type="text" id="lay-picker-input-'+index+'" placeholder="'+((typeof data.search === "string") ? data.search : '请输入搜索内容')+'"/>';
				s += '		</div>';
			}
			s += '		<div class="lay-picker-content" style="'+(data.title ? '' : 'margin: 30px 15px;')+'">';
			s += '			<div class="lay-picker-shadowup"></div>';
			s += '			<div class="lay-picker-shadowdown"></div>';
			s += '			<div class="lay-picker-box"></div>';
			s += '		</div>';
			s += '		<div class="lay-picker-bottom">';
			s += '			<span class="lay-picker-cancel-click lay-picker-bottom-btn lay-picker-bottom-btn-cancel">'+cancel+'</span>';
			s += '			<span class="lay-picker-clear-click lay-picker-bottom-btn lay-picker-bottom-btn-clear">'+clear+'</span>';
			s += '			<span class="lay-picker-confirm-click lay-picker-bottom-btn lay-picker-bottom-btn-confirm">'+confirm+'</span>';
			s += '		</div>';
			s += '	</div>';
			s += '</div>';
		}else{
			var sumHeight = 350; // 高度超出出现滚动条
			sumHeight = (data.search) ? sumHeight += 80 : sumHeight;
			var h = $(window).height();
			var radius = (data.radius?'border-radius: '+data.radius+'px '+data.radius+'px 0px 0;':'');
			s += '	<div class="lay-picker-container" style="transform: translate3d(0px, 100%, 0px);'+radius+''+(sumHeight > h ? 'overflow: auto;height: 100%;':'')+'">';
			s += '		<div class="lay-picker-header" style="'+radius+'">';
			s += '			<div class="lay-picker-title">'+(data.title || '')+'</div>';
			s += '			<div class="lay-picker-btn">';
			s += '				<span class="lay-picker-cancel lay-picker-cancel-click">'+cancel+'</span>';
			s += '				<span class="lay-picker-clear lay-picker-clear-click">'+clear+'</span>';
			s += '				<span class="lay-picker-confirm lay-picker-confirm-click">'+confirm+'</span>';
			s += '			</div>';
			s += '		</div>';
			if(data.search){
				s += '		<div class="lay-picker-search" style="'+radius+'">';
				s += '			<input type="text" id="lay-picker-input-'+index+'" placeholder="'+((typeof data.search === "string") ? data.search : '请输入搜索内容')+'"/>';
				s += '		</div>';
			}
			s += '		<div class="lay-picker-content">';
			s += '			<div class="lay-picker-shadowup"></div>';
			s += '			<div class="lay-picker-shadowdown"></div>';
			s += '			<div class="lay-picker-box"></div>';
			s += '		</div>';
			s += '	</div>';
			s += '</div>';
		}
		return s;
	},
	initBox: function(data, index){
		var len = 100 / data.length;
		var box = $('.lay-picker-'+index).find('.lay-picker-box');
		for(var i=0; i<data.length; i++){
			var div = $('<div index="'+i+'" class="lay-picker-list-wrap" style="width: '+len+'%;"></div>');
			var ul = $('<ul style="transform: translate3d(0px, 0, 0px);"></ul>');
			for(var j=0; j<data[i].length; j++){
				var li = $('<li>'+data[i][j][layPicker.fieldList[index].textField]+'</li>');
				ul.append(li);
			}
			div.append(ul);
			box.append(div);
		}
	},
	initSelect: function(index){
		var list = layPicker.dataList[index];
		for(var i=0; i<list.length; i++){
			layPicker.selectList[index][i+''] = 0;
		}
	},
	setData: function(index, i, data, trends){
		var t_index = (Number(i) + 1);
		var ul = $('.lay-picker-'+index).find('.lay-picker-box').find('[index='+t_index+']>ul');
		ul.empty();
		ul.css('transform', 'translate3d(0px, 0px, 0px)');
		for(var i=0; i<data.length; i++){
			var li = $('<li>'+data[i][layPicker.fieldList[index].textField]+'</li>');
			ul.append(li);
		}
		layPicker.selectList[index][t_index+''] = 0;
		layPicker.dataList[index][t_index] = data;

	},
	setDataTrends: function(index, i, data){
		var t_index = (Number(i) + 1);
		if(!data || data.length == 0){
			layPicker.deleteData(index, i, t_index);
			var len = 100 / layPicker.dataList[index].length;
			$('.lay-picker-'+index).find('.lay-picker-list-wrap').css('width', len+'%');
			return;
		}
		var data_len = t_index > layPicker.dataList[index].length-1;
		if(data_len){
			layPicker.dataList[index].push(data);
		}else{
			layPicker.dataList[index][t_index] = data;
			layPicker.deleteData(index, t_index, t_index+1);
		}
		var box = $('.lay-picker-'+index).find('.lay-picker-box');
		var len = 100 / layPicker.dataList[index].length;
		$('.lay-picker-'+index).find('.lay-picker-list-wrap').css('width', len+'%');
		if(data_len){
			var div = $('<div index="'+t_index+'" class="lay-picker-list-wrap" style="width: '+len+'%;"><ul style="transform: translate3d(0px, 0, 0px);"></ul></div>');
			box.append(div);
		}
		var ul = $('.lay-picker-'+index).find('.lay-picker-box').find('[index='+t_index+']>ul');
		ul.empty();
		ul.css('transform', 'translate3d(0px, 0px, 0px)');
		for(var i=0; i<data.length; i++){
			var li = $('<li>'+data[i][layPicker.fieldList[index].textField]+'</li>');
			ul.append(li);
		}
		layPicker.selectList[index][t_index+''] = 0;
	},
	deleteData: function(index, i, t_index){
		if(layPicker.dataList[index].length > Number(i)){
			for(var i=t_index; i<layPicker.dataList[index].length; i++){
				layPicker.dataList[index].splice(i, 1);
				$('.lay-picker-'+index).find('.lay-picker-box').find('[index='+i+']').remove();
				return layPicker.deleteData(index, i, t_index);
			}
		}
	},
	setValue: function(index, data, options){
		var list = layPicker.dataList[index];
		var box = $('.lay-picker-'+index).find('.lay-picker-box');
		for(var i=0; i<data.length; i++){
			data[i].value = Number(data[i].value) < 10 ? '0'+Number(data[i].value) : data[i].value;
		}
		if(!options && layPicker.minMaxData[index]){
			options = layPicker.minMaxData[index]['options'];
		}
		for(var i=0; i<data.length; i++){
			var arr = list[i];
			for(var j=0; j<arr.length; j++){
				if(arr[j][layPicker.fieldList[index].valueField] == data[i][layPicker.fieldList[index].valueField]){
					layPicker.selectList[index][i+''] = j;
					if(options){
						layPicker.setOptionsValue(options, index, data, i);
					}
					var y = j * layPicker.liHeight;
					box.find('[index='+i+']>ul').css('transform', 'translate3d(0px, -'+y+'px, 0px)');
				}
			}
		}
	},
	setOptionsValue: function(options, key, resultArray, thisIndex){
		if(options == 'month' || options == 'date' || options == 'datetime' || options == 'datetimesecond'){
			if(thisIndex == 0){
				layPicker.setData(key, Number(thisIndex), lay_picker_date.getMonth(resultArray, key));
			}else if(thisIndex == 1 && options != 'month'){
				layPicker.setData(key, Number(thisIndex), lay_picker_date.getDay(resultArray, key));
			}
			if(options == 'datetime' || options == 'datetimesecond'){
				if(thisIndex == 2){
					layPicker.setData(key, Number(thisIndex) , lay_picker_date.getHours(resultArray, key));
				}else if(thisIndex == 3){
					layPicker.setData(key, thisIndex, lay_picker_date.getMinutes(resultArray, key));
				}
				if(options == 'datetimesecond'){
					if(thisIndex == 4){
						layPicker.setData(key, Number(thisIndex), lay_picker_date.getSeconds(resultArray, key));
					}
				}
			}
		}
		if(options == 'time' || options == 'timesecond'){
			if(thisIndex == 0){
				layPicker.setData(key, thisIndex, lay_picker_date.getMinutes(resultArray, key, 1));
			}
			if(options == 'timesecond'){
				if(thisIndex == 1){
					layPicker.setData(key, Number(thisIndex), lay_picker_date.getSeconds(resultArray, key, 1));
				}
			}
		}
	},
	hiden: function(index){
		$('.lay-picker-'+index).find('.lay-picker-container').css('transform', 'translate3d(0px, 100%, 0px)');
		$('.lay-picker-'+index).find('.lay-picker-shade').fadeOut(200);
		setTimeout(function(){
			$('.lay-picker-'+index).css('display', 'none');
		}, 100)
	},
	show: function(index){
		$('.lay-picker-'+index).css('display', 'block');
		$('.lay-picker-'+index).find('.lay-picker-shade').fadeIn(200);
		setTimeout(function(){
			$('.lay-picker-'+index).find('.lay-picker-container').css('transform', 'translate3d(0px, 0px, 0px)');
		}, 10)
		$(".lay-picker-not-mobile").remove();
		if(!layPicker.isMobile()){
			$(".lay-picker-container").append("<div class='lay-picker-not-mobile' style='padding: 15px 0; color: #999;text-align: center;'>非手机模式无法滑动</div>")
		}
	},
	remove: function(index){
		layPicker.hiden(index);
		setTimeout(function(){
			$('.lay-picker-'+index).remove();
		}, 200)
		for(var i=0; i<layPicker.indexList.length; i++){
			if(layPicker.indexList[i] == index){
				layPicker.indexList.splice(i, 1);
				break;
			}
		}
	},
	removeAll: function(){
		if(layPicker.indexList.length > 0){
			for(var i=0; i<layPicker.indexList.length; i++){
				layPicker.hiden(layPicker.indexList[i]);
				var index = layPicker.indexList[i];
				layPicker.removeTime(index);
				layPicker.indexList.splice(i, 1);
				return layPicker.removeAll();
			}
		}
	},
	removeTime: function(index){
		setTimeout(function(){
			$('.lay-picker-'+index).remove();
		}, 200)
	},
	/** 判断是否是手机打开*/
	isMobile: function () {
	  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|DingTalk|Opera Mini/i.test(navigator.userAgent);
	},
}

/**
 * 日期对象
 */
var lay_picker_date = {
	getVoluation: function(value, key){
		var date_obj = lay_picker_date.getDateTime();
		var year_obj = {};
		year_obj[''+key] = date_obj.year;
		var month_obj = {};
		month_obj[''+key] = date_obj.month;
		var day_obj = {};
		day_obj[''+key] = date_obj.day;
		var hours_obj = {};
		hours_obj[''+key] = date_obj.hours;
		var minutes_obj = {};
		minutes_obj[''+key] = date_obj.minutes;
		var seconds_obj = {};
		seconds_obj[''+key] = date_obj.seconds;
		if(value == 'year'){
			return [year_obj];
		}else if(value == 'month'){
			return [year_obj, month_obj];
		}else if(value == 'date'){
			return [year_obj, month_obj, day_obj];
		}else if(value == 'time'){
			return [hours_obj, minutes_obj];
		}else if(value == 'timesecond'){
			return [hours_obj, minutes_obj, seconds_obj];
		}else if(value == 'datetime'){
			return [year_obj, month_obj, day_obj, hours_obj, minutes_obj];
		}else if(value == 'datetimesecond'){
			return [year_obj, month_obj, day_obj, hours_obj, minutes_obj, seconds_obj];
		}
	},
	getData: function(value, key){
		if(value == 'year'){
			var years = lay_picker_date.getYear(key);
			return [years];
		}else if(value == 'month'){
			var years = lay_picker_date.getYear(key);
			var date_obj = lay_picker_date.getDateTime();
			var months = lay_picker_date.getMonth(
				[
					{value: years[0].value},
					{value: date_obj.month},
				]
			, key);
			return [years, months];
		}else if(value == 'date'){
			var years = lay_picker_date.getYear(key);
			var date_obj = lay_picker_date.getDateTime();
			var months = lay_picker_date.getMonth(
				[
					{value: years[0].value},
					{value: date_obj.month},
				]
			, key);
			var days = lay_picker_date.getDay(
				[
					{value: years[0].value},
					{value: months[0].value},
					{value: date_obj.day},
				]
			, key);
			return [years, months, days];
		}else if(value == 'time'){
			var hourss = lay_picker_date.getHours(null, key, 1);
			var date_obj = lay_picker_date.getDateTime();
			var minutess = lay_picker_date.getMinutes(
				[
					{value: hourss[0].value},
					{value: date_obj.minutes},
				]
			, key, 1);
			return [hourss, minutess];
		}else if(value == 'timesecond'){
			var hourss = lay_picker_date.getHours(null, key, 1);
			var date_obj = lay_picker_date.getDateTime();
			var minutess = lay_picker_date.getMinutes(
				[
					{value: hourss[0].value},
					{value: date_obj.minutes},
				]
			, key, 1);
			var secondss = lay_picker_date.getSeconds(
				[
					{value: hourss[0].value},
					{value: minutess[0].value},
					{value: date_obj.seconds},
				]
			, key, 1);
			return [hourss, minutess, secondss];
		}else if(value == 'datetime'){
			var years = lay_picker_date.getYear(key);
			var date_obj = lay_picker_date.getDateTime();
			var months = lay_picker_date.getMonth(
				[
					{value: years[0].value},
					{value: date_obj.month},
				]
			, key);
			var days = lay_picker_date.getDay(
				[
					{value: years[0].value},
					{value: months[0].value},
					{value: date_obj.day},
				]
			, key);
			var hourss = lay_picker_date.getHours(
				[
					{value: years[0].value},
					{value: months[0].value},
					{value: days[0].value},
				], key);
			var minutess = lay_picker_date.getMinutes(
				[
					{value: years[0].value},
					{value: months[0].value},
					{value: days[0].value},
					{value: hourss[0].value},
				], key);
			return [years, months, days, hourss, minutess];
		}else if(value == 'datetimesecond'){
			var years = lay_picker_date.getYear(key);
			var date_obj = lay_picker_date.getDateTime();
			var months = lay_picker_date.getMonth(
				[
					{value: years[0].value},
					{value: date_obj.month},
				]
			, key);
			var days = lay_picker_date.getDay(
				[
					{value: years[0].value},
					{value: months[0].value},
					{value: date_obj.day},
				]
			, key);
			var hourss = lay_picker_date.getHours(
				[
					{value: years[0].value},
					{value: months[0].value},
					{value: days[0].value},
				], key);
			var minutess = lay_picker_date.getMinutes(
				[
					{value: years[0].value},
					{value: months[0].value},
					{value: days[0].value},
					{value: hourss[0].value},
				], key);
			var secondss = lay_picker_date.getSeconds(
				[
					{value: years[0].value},
					{value: months[0].value},
					{value: days[0].value},
					{value: hourss[0].value},
					{value: minutess[0].value},
				], key);
			return [years, months, days, hourss, minutess, secondss];
		}
	},
	getDateTime: function() {
	    var myDate = new Date();
	    var year = myDate.getFullYear(); //获取年
	    var month = myDate.getMonth() + 1; //获取月，默认从0开始，所以要加一
	    month = ('00' + month).slice(-2);
	    var day = ('00' + myDate.getDate()).slice(-2); //获取日
	    var hours = ('00' + myDate.getHours()).slice(-2); //获取小时
	    var minutes = ('00' + myDate.getMinutes()).slice(-2); //获取分
	    var seconds = ('00' + myDate.getSeconds()).slice(-2); //获取秒
		return {
			year: year,
			month: month,
			day: day,
			hours: hours,
			minutes: minutes,
			seconds: seconds,
		}
	},
	getYear: function(key){
		var arr = [],
			now = new Date();
		var minYear = 1900;
		var maxYear = now.getFullYear() + 60;
		if(key){
			var minObj = layPicker.minMaxData[key].minObj;
			if(minObj && minObj.ymd){
				minYear = Number(minObj.ymd[0]);
			}
			var maxObj = layPicker.minMaxData[key].maxObj;
			if(maxObj && maxObj.ymd){
				maxYear = Number(maxObj.ymd[0]);
			}
		}
		for(var i = minYear; i <= maxYear; i++) {
			arr.push({
				name: i + '年',
				value: i
			});
		}
		return arr;
	},
	getMonth: function(result, key) {
		var year = result[0].value;
		var arr = [];
		var minMonth = 1;
		var maxMonth = 12;
		if(key){
			var minObj = layPicker.minMaxData[key].minObj;
			if(minObj && minObj.ymd){
				if(year == minObj.ymd[0]){
					minMonth = Number(minObj.ymd[1]);
				}else{
					var d1 = Number(year+''+result[1].value);
					var d2 = Number(minObj.ymd[0]+''+minObj.ymd[1]);
					if(d1 <= d2){
						minMonth = Number(minObj.ymd[1]);
					}
				}
			}
			var maxObj = layPicker.minMaxData[key].maxObj;
			if(maxObj && maxObj.ymd){
				if(year == maxObj.ymd[0]){
					maxMonth = Number(maxObj.ymd[1]);
				}else{
					var d1 = Number(year+''+result[1].value);
					var d2 = Number(maxObj.ymd[0]+''+maxObj.ymd[1]);
					if(d1 >= d2){
						maxMonth = Number(maxObj.ymd[1]);
					}
				}
			}
		}
		for(var i = minMonth; i <= maxMonth; i++) {
			var code = ('00' + i).slice(-2);
			arr.push({
				name: i + '月',
				value: code
			});
		}
		return arr;
	},
	getDay: function(result, key) {
		var year = result[0].value,
			month = result[1].value;
		var thisDate = new Date(Number(year), Number(month), 0);
		var maxDay = thisDate.getDate();
		var minDay = 1;
		if(key){
			var minObj = layPicker.minMaxData[key].minObj;
			if(minObj && minObj.ymd){
				if(year == minObj.ymd[0] && month == minObj.ymd[1]){
					minDay = Number(minObj.ymd[2]);
				}else{
					var d1 = Number(year+''+month+''+result[1].value);
					var d2 = Number(minObj.ymd[0]+''+minObj.ymd[1]+''+minObj.ymd[2]);
					if(d1 <= d2){
						minDay = Number(minObj.ymd[2]);
					}
				}
			}
			var maxObj = layPicker.minMaxData[key].maxObj;
			if(maxObj && maxObj.ymd){
				if(year == maxObj.ymd[0] && month == maxObj.ymd[1]){
					maxDay = Number(maxObj.ymd[2]);
				}else{
					var d1 = Number(year+''+month+''+result[1].value);
					var d2 = Number(maxObj.ymd[0]+''+maxObj.ymd[1]+''+maxObj.ymd[2]);
					if(d1 >= d2){
						maxDay = Number(maxObj.ymd[2]);
					}
				}
			}
		}
		var arr = [];
		for(var i = minDay; i <= maxDay; i++) {
			var code = ('00' + i).slice(-2);
			arr.push({
				name: i + '日',
				value: code
			});
		}
		return arr;
	},
	getHours: function(result, key, type) {
		var arr = [];
		var minHour = 0;
		var maxHour = 23;
		if(key){
			if(type){
				var minObj = layPicker.minMaxData[key].minObj;
				if(minObj && minObj.hms){
					minHour = Number(minObj.hms[0]);
				}
				var maxObj = layPicker.minMaxData[key].maxObj;
				if(maxObj && maxObj.hms){
					maxHour = Number(maxObj.hms[0]);
				}
			}else{
				var year = result[0].value,
				month = result[1].value,
				day = result[2].value;
				var minObj = layPicker.minMaxData[key].minObj;
				if(minObj && minObj.hms){
					if(minObj && minObj.ymd){
						var d1 = Number(year+''+month+''+day+''+minObj.hms[0]);
						var d2 = Number(minObj.ymd[0]+''+minObj.ymd[1]+''+minObj.ymd[2]+''+minObj.hms[0]);
						if(d1 <= d2){
							minHour = Number(minObj.hms[0]);
						}
					}else{
						minHour = Number(minObj.hms[0]);
					}
				}
				var maxObj = layPicker.minMaxData[key].maxObj;
				if(maxObj && maxObj.hms){
					if(maxObj && maxObj.ymd){
						var d1 = Number(year+''+month+''+day+''+maxObj.hms[0]);
						var d2 = Number(maxObj.ymd[0]+''+maxObj.ymd[1]+''+maxObj.ymd[2]+''+maxObj.hms[0]);
						if(d1 >= d2){
							maxHour = Number(maxObj.hms[0]);
						}
					}else{
						maxHour = Number(maxObj.hms[0]);
					}
				}
			}
		}
		for(var i = minHour; i <= maxHour; i++) {
			var value = ('00' + i).slice(-2);
			arr.push({
				name: value + '时',
				value: value
			})
		}
		return arr;
	},
	getMinutes: function(result, key, type) {
		var arr = [];
		var minMinute = 0;
		var maxMinute = 59;
		if(key){
			if(type){
				var hours = result[0].value;
				var minObj = layPicker.minMaxData[key].minObj;
				if(minObj && minObj.hms){
					var d1 = Number(hours +''+ minObj.hms[1]);
					var d2 = Number(minObj.hms[0] +''+ minObj.hms[1]);
					if(d1 <= d2){
						minMinute = Number(minObj.hms[1]);
					}
				}
				var maxObj = layPicker.minMaxData[key].maxObj;
				if(maxObj && maxObj.hms){
					var d1 = Number(hours +''+ maxObj.hms[1]);
					var d2 = Number(maxObj.hms[0] +''+ maxObj.hms[1]);
					if(d1 >= d2){
						maxMinute = Number(maxObj.hms[1]);
					}
				}
			}else{
				var year = result[0].value,
				month = result[1].value,
				day = result[2].value;
				hours = result[3].value;
				var minObj = layPicker.minMaxData[key].minObj;
				if(minObj && minObj.hms){
					if(minObj && minObj.ymd){
						var d1 = Number(year+''+month+''+day+''+hours+''+minObj.hms[1]);
						var d2 = Number(minObj.ymd[0]+''+minObj.ymd[1]+''+minObj.ymd[2]+''+minObj.hms[0]+''+minObj.hms[1]);
						if(d1 <= d2){
							minMinute = Number(minObj.hms[1]);
						}
					}else{
						minMinute = Number(minObj.hms[1]);
					}
				}
				var maxObj = layPicker.minMaxData[key].maxObj;
				if(maxObj && maxObj.hms){
					if(maxObj && maxObj.ymd){
						var d1 = Number(year+''+month+''+day+''+hours+''+maxObj.hms[1]);
						var d2 = Number(maxObj.ymd[0]+''+maxObj.ymd[1]+''+maxObj.ymd[2]+''+maxObj.hms[0]+''+maxObj.hms[1]);
						if(d1 >= d2){
							maxMinute = Number(maxObj.hms[1]);
						}
					}else{
						maxMinute = Number(maxObj.hms[1]);
					}
				}
			}
		}
		for(var i = minMinute; i <= maxMinute; i++) {
			var value = ('00' + i).slice(-2);
			arr.push({
				name: value + '分',
				value: value
			})
		}
		return arr;
	},
	getSeconds: function(result, key, type) {
		var arr = [];
		var minSecond = 0;
		var maxSecond = 59
		if(key){
			if(type){
				var hours = result[0].value;
				var minute = result[1].value;
				var minObj = layPicker.minMaxData[key].minObj;
				if(minObj && minObj.hms && minObj.hms.length > 2){
					var d1 = Number(hours +''+ minute+''+ minObj.hms[2]);
					var d2 = Number(minObj.hms[0] +''+ minObj.hms[1]+''+ minObj.hms[2]);
					if(d1 <= d2){
						minSecond = Number(minObj.hms[2]);
					}
				}
				var maxObj = layPicker.minMaxData[key].maxObj;
				if(maxObj && maxObj.hms && maxObj.hms.length > 2){
					var d1 = Number(hours +''+ minute+''+ maxObj.hms[2]);
					var d2 = Number(maxObj.hms[0] +''+ maxObj.hms[1]+''+ maxObj.hms[2]);
					if(d1 >= d2){
						maxSecond = Number(maxObj.hms[2]);
					}
				}
			}else{
				var year = result[0].value,
				month = result[1].value,
				day = result[2].value;
				hours = result[3].value;
				minute = result[4].value;
				var minObj = layPicker.minMaxData[key].minObj;
				if(minObj && minObj.hms){
					if(minObj && minObj.ymd){
						var d1 = Number(year+''+month+''+day+''+hours+''+minute+''+minObj.hms[2]);
						var d2 = Number(minObj.ymd[0]+''+minObj.ymd[1]+''+minObj.ymd[2]+''+minObj.hms[0]+''+minObj.hms[1]+''+minObj.hms[2]);
						if(d1 <= d2){
							minSecond = Number(minObj.hms[2]);
						}
					}else{
						minSecond = Number(minObj.hms[2]);
					}
				}
				var maxObj = layPicker.minMaxData[key].maxObj;
				if(maxObj && maxObj.hms){
					if(maxObj && maxObj.ymd){
						var d1 = Number(year+''+month+''+day+''+hours+''+minute+''+maxObj.hms[2]);
						var d2 = Number(maxObj.ymd[0]+''+maxObj.ymd[1]+''+maxObj.ymd[2]+''+maxObj.hms[0]+''+maxObj.hms[1]+''+maxObj.hms[2]);
						if(d1 >= d2){
							maxSecond = Number(maxObj.hms[2]);
						}
					}else{
						maxSecond = Number(maxObj.hms[2]);
					}
				}
			}
		}
		for(var i = minSecond; i <= maxSecond; i++) {
			var value = ('00' + i).slice(-2);
			arr.push({
				name: value + '秒',
				value: value
			})
		}
		return arr;
	},
	getLastDay: function(result){
		var year = result[0].value,
			month = result[1].value;
		var thisDate = new Date(Number(year), Number(month), 0);
		return thisDate.getDate();
	},
	getMinOrMaxDate: function(date){
		if(!date){
			return;
		}
		var arr = date.split(" ");
		var obj = {};
		if(arr.length > 0){
			if(arr.length == 2){
				obj.ymd = arr[0].split("-");
				obj.hms = arr[1].split(":");
			}else if(arr.length == 1 && date.indexOf(":") == -1){
				obj.ymd = arr[0].split("-");
			}else{
				obj.hms = arr[0].split(":");
			}
		}
		return obj;
	},
}

$('body').on('click', '.tool-time', function () {
	let elem = $(this);
	let type = $(this).data('type');
	let min = $(this).data('min');
	let max = $(this).data('max');
	if (typeof(type) == "undefined" || type === '') {
		type = 'date';
	}
	if (typeof(min) == "undefined" || min === '') {
		minDate = '1900-1-1';
	}
	if (typeof(max) == "undefined" || max === '') {
		maxDate = '2099-1-1';
	}
	layPicker.init({
		elem: elem, // 绑定元素
		options: type, // 设置为日期选择器（日期选择器可设置：year、month、date、time、timesecond、datetime、datetimesecond），不设置默认取data作为选择值
		onSuccess: function(index, elem){ // 渲染成功回调
			layPicker.show(index);
			/*
			layPicker.setValue(index, [ // 赋值，以位置类推
				{value: ''},
				{value: ''},
				{value: ''}, 
			])
			*/
		},
		onClear: function(index){ // 停止滚动触发：index是当前对象的标识， i 是当前滑动的对象，result是前面的值集
			elem.val('');
			layPicker.remove(index);
		},
		onConfirm: function(index, val, result){ // 点击确认回调
			elem.val(val)
			layPicker.remove(index);
		},
		onCancel: function(index){ // 点击取消回调
			layPicker.remove(index);
		},
		onShade: function(index){ // 点击遮罩回调
			layPicker.remove(index);
		}
	});
	return false;
});

$('body').on('click', '.picker-oa', function () {
	let elem = $(this);
	let url = $(this).data('url');
	let field = $(this).data('field');
	if (typeof(url) == "undefined" || url === '') {
		return false;
	}
	if (typeof(field) == "undefined" || field === '') {
		field = 'title';
	}
	let loading;
	$.ajax({
		url: url,
		type: 'get',
		data:{limit:999999},
		beforeSend:function(){
			loading = layer.loading('加载中...');
		},
		success: function (e) {
			if (e.code == 0) {
				let pickerData = e.data;
				layPicker.init({
					elem: elem, // 绑定元素
					data: [pickerData],
					textField: field,
					valueField: 'id',
					search:true,
					onSuccess: function(index, elem){ // 渲染成功回调
						layPicker.show(index);
						layPicker.setValue(index, [ // 赋值，以位置类推
							{value: ''}
						])
					},
					onClear: function(index){
						elem.val('');
						elem.next().val('');
						layPicker.remove(index);
					},
					onConfirm: function(index, val, result){ // 点击确认回调
						if(result.length>0){
							elem.val(result[0][field]);
							elem.next().val(result[0].id);
						}
						layPicker.remove(index);
					},
					onCancel: function(index){ // 点击取消回调
						layPicker.remove(index);
					},
					onShade: function(index){ // 点击遮罩回调
						layPicker.remove(index);
					}
				});
			}
		},
		complete:function(){
			layer.close(loading);
		}
	})
	return false;
});

exports('picker', layPicker);
});

mbui.define(['tool','layer','picker','userPicker'],function(exports){
	let layer = mbui.layer,tool=mbui.tool,picker = mbui.picker,adminpicker = mbui.userPicker;
	const opts={
		"box":'editBox',//编辑容器id
		"id":0,//编辑容器id
		"url": '',
		"radio":{},
		"checbox":{},
		"menu":{},
		"callback":function(e){
			layer.msg(e.msg);
			if(e.code==0){
				setTimeout(function(){
					location.reload();
				},1000)
			}
		}
	};
	
	const obj = {
		//短文本
		text: function (that) {
			let me = this;
			let field = that.data('field');
			let text = that.data('text');
			text = typeof(text) == "undefined" ? that.text():text;
			
			layer.open({
				type: 1,
				title: '请输入内容',
				area: ['360px', '158px'],
				content: '<div><input class="mbui-input" id="oaEditText" value="' + text + '"/></div>',
				btnAlign: 'c',
				btn: ['保存','取消'],
				yes: function () {
					let val = $("#oaEditText").val();
					if (val != '') {
						let postData = {'id':me.sets.id,'scene':'oaedit'};
						postData[field] = val;
						tool.post(me.sets.url,postData,me.sets.callback);
					} else {
						layer.msg('请输入内容');
					}
				}
			})			
		},
		//长文本
		textarea: function (that) {
			let me = this;
			let field = that.data('field');
			let target = that.data('target');
			let textarea='';
			if (typeof(target) == "undefined") {
				textarea = that.text();
			}
			else{
				textarea = $('#'+target).text();
			}
			layer.open({
				type: 1,
				title: '请输入内容',
				area: ['600px', '320px'],
				content: '<div style="padding:5px;"><textarea class="mbui-textarea" id="oaEditTextarea" style="width: 100%; height: 200px;">' + textarea + '</textarea></div>',
				btnAlign: 'c',
				btn: ['保存','取消'],
				yes: function () {
					let val = $("#oaEditTextarea").val();
					if (val != '') {
						let postData = {'id':me.sets.id,'scene':'oaedit'};
						postData[field] = val;
						tool.post(me.sets.url,postData,me.sets.callback);
					} else {
						layer.msg('请输入内容');
					}
				}
			})			
		},
		//数字
		num: function (that) {
			let me = this;
			let field = that.data('field');
			let text = that.data('text');
			text = typeof(text) == "undefined" ? that.text():text;
			
			let min = that.data('min');
			min = typeof(min) == "undefined" ? 0:min;
			
			let max = that.data('max');
			max = typeof(max) == "undefined" ? 100:max;

			layer.open({
				type: 1,
				title: '请输入数字',
				area: ['200px', '158px'],
				content: '<div style="padding:5px;"><input class="mbui-input" oninput="this.value = this.value.replace(/[^0-9]/g,\'\')" id="oaEditNum" value="' + text + '"/></div>',
				btnAlign: 'c',
				btn: ['保存','取消'],
				yes: function () {
					let val = $("#oaEditNum").val();
					if (val != '') {
						if(min !='' && val<min){
							layer.msg('输入数字不能小于'+min);
							return false;
						}
						if(max !='' && val>max){
							layer.msg('输入数字不能大于'+max);
							return false;
						}
						let postData = {'id':me.sets.id,'scene':'oaedit'};
						postData[field] = val;
						tool.post(me.sets.url,postData,me.sets.callback);
					} else {
						layer.msg('请输入内容');
					}
				}
			})			
		},
		oadate: function (that) {
			let me = this;
			let type = that.data('type');
			let min = that.data('min');
			let max = that.data('max');
			if (typeof(type) == "undefined" || type === '') {
				type = 'date';
			}
			if (typeof(min) == "undefined" || min === '' || min === 1) {
				min = '1970-01-01 00:00:00';
			}
			if (typeof(max) == "undefined" || max === '' || max === 100) {
				max = '2099-12-31 23:59:59';
			}
			let field = that.data('field');
			let text = that.data('text');
			text = typeof(text) == "undefined" ? that.text():text;
			
			picker.init({
				elem: that, // 绑定元素
				options: type, // 设置为日期选择器（日期选择器可设置：year、month、date、time、timesecond、datetime、datetimesecond）
				onSuccess: function(index, elem){ // 渲染成功回调
					picker.show(index);
				},
				onClear: function(index){ // 停止滚动触发：index是当前对象的标识， i 是当前滑动的对象，result是前面的值集
					let postData = {'id':me.sets.id,'scene':'oaedit'};
					postData[field] = 0;
					tool.post(me.sets.url,postData,me.sets.callback);
					picker.remove(index);
				},
				onConfirm: function(index, val, result){ // 点击确认回调
					let postData = {'id':me.sets.id,'scene':'oaedit'};
					postData[field] = val;
					tool.post(me.sets.url,postData,me.sets.callback);
					picker.remove(index);
				},
				onCancel: function(index){ // 点击取消回调
					picker.remove(index);
				},
				onShade: function(index){ // 点击遮罩回调
					picker.remove(index);
				},
				minDate:min,
				maxDate:max
			})
		},
		adminpicker: function (that){
			let me = this;
			let field = that.data('field');
			let ids = that.data('ids');
			let names = that.data('names');
			let min = that.data('min');
			min = typeof(min) == "undefined" ? 1:min;

			adminpicker({
				type:min,
				callback:function(ids,names,dids,departments){
					let postData = {'id':me.sets.id,'scene':'oaedit'};
					postData[field] = ids.join(',');
					tool.post(me.sets.url,postData,me.sets.callback);
				}
			});
		},
		picker:function(that){
			let me = this;
			let field = that.data('field');
			let api = that.data('api');
			if(typeof(api) == "undefined"){
				layer.msg('相关API参数未完善');
				return false;
			}	
			let min = that.data('min');
			min = typeof(min) == "undefined" ? 1:min;
			let loading;
			$.ajax({
				url: api,
				type: 'get',
				beforeSend:function(){
					loading = layer.loading('加载中...');
				},
				success: function (e) {
					if (e.code == 0) {
						data = e.data;
						if(min==1){
							me.radio(that,data);
						}
						else{
							me.checkbox(that,data);
						}
					}
				},
				complete:function(){
					layer.close(loading);
				}
			})
		},
		radio:function(that,data){
			let me = this;
			let field = that.data('field');
			let text = that.data('text');
			text = typeof(text) == "undefined" ? that.text():text;
			
			let arrayidx = that.data('array');
			if(typeof(arrayidx) != "undefined" && arrayidx!=''){
				data = me.sets.radio[arrayidx];
			}
			if (data.length == 0) {
				layer.msg('无可选择的内容');
				return false;
			}
			let len = data.length;
			let editHtml = '<div class="mbui-layer-radio">'
			while (len--) {
				if (data[len].id == text) {
					editHtml+='<div class="mbui-radio-checkbox"><label for="list_radio_'+data[len].id+'">'+data[len].title+'<input class="mbui-radio" type="radio" name="layer_radio" checked value="'+data[len].id+'" id="list_radio_'+data[len].id+'" /></label></div>';
				}
				else{
					editHtml+='<div class="mbui-radio-checkbox"><label for="list_radio_'+data[len].id+'">'+data[len].title+'<input class="mbui-radio" type="radio" name="layer_radio" value="'+data[len].id+'" id="list_radio_'+data[len].id+'" /></label></div>';
				}
			}
			editHtml+='</div>';
			layer.open({
				type:5,
				content:editHtml,
				btn:['确定','取消'],
				yes:function(index){
					let val = $('input[name="layer_radio"]:checked').val();
					let postData = {'id':me.sets.id,'scene':'oaedit'};
					postData[field] = val;
					tool.post(me.sets.url,postData,me.sets.callback);
				}
			});
		},
		checkbox:function(that){
			let me = this;
			let field = that.data('field');
			let text = that.data('text');
			text = typeof(text) == "undefined" ? that.text():text;
			
			let selected_array = text+''.split(',');
			let arrayidx = that.data('array');
			let data = me.sets.checkbox[arrayidx];
			if (data.length == 0) {
				layer.msg('无可选择的内容');
				return false;
			}
			let len = data.length;
			let editHtml = '<div class="mbui-layer-checkbox">'
			while (len--) {
				if (selected_array.includes(data[len].id)) {
					editHtml+='<div class="mbui-radio-checkbox"><label for="list_checkbox_'+data[len].id+'">'+data[len].title+'<input class="mbui-checkbox" type="checkbox" name="layer_checkbox" checked value="'+data[len].id+'" id="list_radio_'+data[len].id+'" /></label></div>';
				}
				else{
					editHtml+='<div class="mbui-radio-checkbox"><label for="list_checkbox_'+data[len].id+'">'+data[len].title+'<input class="mbui-checkbox" type="checkbox" name="layer_checkbox" value="'+data[len].id+'" id="list_checkbox_'+data[len].id+'" /></label></div>';
				}
			}
			editHtml+='</div>';
			layer.open({
				type:5,
				content:editHtml,
				btn:['确定','取消'],
				yes:function(index){
					let checkedValues = $('input[name="layer_checkbox"]:checked').map(function(){
						return $(this).val();
					}).get();
					let postData = {'id':me.sets.id,'scene':'oaedit'};
					postData[field] = checkedValues.join(',');
					tool.post(me.sets.url,postData,me.sets.callback);
				}
			});
		},
		menu: function (that) {
			let me = this;
			let arrayidx = that.data('array');
			let data = me.sets.menu[arrayidx];
			if (data.length == 0) {
				layer.msg('无可选择的内容');
				return false;
			}
			let len = data.length;
			let editHtml = ''
			while (len--) {
				let item = $('#edit_'+data[len].field);
				let types = item.data('types');
				let text = item.data('text');
				text = typeof(text) == "undefined" ? item.text():text;
				
				let arrays = item.data('array');
				arrays = typeof(arrays) == "undefined" ? '':arrays;
				
				let api = item.data('api');
				api = typeof(api) == "undefined" ? '':api;
				
				let min = item.data('min');
				min = typeof(min) == "undefined" ? 1:min;

				let max = item.data('max');
				max = typeof(max) == "undefined" ? 100:max;
				
				editHtml+='<a href="javascript:void(0);" class="actionsheet-btn" data-field="'+data[len].field+'" data-types="'+types+'" data-array="'+arrays+'" data-text="'+text+'" data-min="'+min+'" data-max="'+max+'" data-api="'+api+'">'+data[len].title+'</a>';
			}
			layer.open({
				type:4,
				content: editHtml,
				btn:['取消'],
				success:function(elem,index){
					$(elem).on('click','.actionsheet-btn',function(){
						let thatmenu = $(this);
						let types = thatmenu.data('types');
						layer.close(index);
						switch (types) {
							case "num":
								me.num(thatmenu);
								break;
							case "oadate":
								me.oadate(thatmenu);
								break;
							case "textarea":
								me.textarea(thatmenu);
								break;
							case "adminpicker":
								me.adminpicker(thatmenu);
								break;
							case "picker":
								me.picker(thatmenu);
								break;
							case "radio":
								me.radio(thatmenu,[]);
								break;
							case "checkbox":
								me.checkbox(thatmenu,[]);
								break;
							default:
								me.text(thatmenu);
						}
					})
				}
			});
		},
		init: function (options) {
			this.sets = $.extend({}, opts, options);
			let me = this;
			let editBox = $('#'+me.sets.box);
			editBox.on('click','.click-edit',function(){
				let that = $(this);
				let types = that.data('types');
				let role = that.data('role');
				role = typeof(role) == "undefined" ? 1:role;
				if(role==0){
					layer.msg('你无权限操作');
					return false;
				}
				switch (types) {
					case "num":
						me.num(that);
						break;
					case "oadate":
						me.oadate(that);
						break;
					case "textarea":
						me.textarea(that);
						break;
					case "adminpicker":
						me.adminpicker(that);
						break;
					case "picker":
						me.picker(that);
						break;
					case "radio":
						me.radio(that,[]);
						break;
					case "checkbox":
						me.checkbox(that,[]);
						break;
					case "menu":
						me.menu(that);
						break;
					default:
						me.text(that);
				}
			})
		}
	}
	//输出接口
	exports('oaEdit', obj);
});   
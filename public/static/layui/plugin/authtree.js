layui.define(['jquery', 'form'], function(exports){
	$ = layui.jquery;
	form = layui.form;

	obj = {
		// 渲染 + 绑定事件
		/**
		 * 渲染DOM并绑定事件
		 * @param  {[type]} dst       [目标ID，如：#test1]
		 * @param  {[type]} trees     [数据，格式：{}]
		 * @param  {[type]} inputname [上传表单名]
		 * @param  {[type]} layfilter [lay-filter的值]
		 * @param  {[type]} openall [默认展开全部]
		 * @param  {[type]} opengrade [展开的节点级别]
		 * @param  {[type]} checkboxrelated [多选是否关联节点]
		 * @return {[type]} selecttype   [checkbox or radio]
		 */
		render: function(dst, trees, opt){
			var inputname = opt.inputname ? opt.inputname : 'menuids[]';
			var layfilter = opt.layfilter ? opt.layfilter : 'checkauth';
			var openall = opt.openall ? opt.openall : false;
			var selecttype = opt.selecttype ? opt.selecttype : 'checkbox';
			var checkboxrelated = opt.checkboxrelated ? opt.checkboxrelated : 0;
			var opengrade = opt.opengrade ? opt.opengrade : 0;
			$(dst).html(obj.renderAuth(trees, 0, {inputname: inputname, layfilter: layfilter, openall: openall,selecttype:selecttype,opengrade:opengrade,checkboxrelated:checkboxrelated}));
			form.render();
			
			$(dst).find('.auth-single:first').unbind('click').on('click', '.layui-form-checkbox', function(){
				if(checkboxrelated==1){
					return;
				}
				var elem = $(this).prev();
				var checked = elem.is(':checked');
				var childs = elem.parent().next().find('input[type="checkbox"]').prop('checked', checked);
				if(checked){
					/*查找child的前边一个元素，并将里边的checkbox选中状态改为true。*/
					elem.parents('.auth-child').prev().find('input[type="checkbox"]').prop('checked', true);
				}
				/*console.log(childs);*/
				form.render('checkbox');
			});
			
			/*
			$(dst).find('.auth-single:first').unbind('click').on('click', '.layui-form-radio', function(){
				obj.getRadio(dst);
			});
			*/

			/*动态绑定展开事件*/
			$(dst).unbind('click').on('click', '.auth-icon', function(){
				var origin = $(this);
				var child = origin.parent().parent().find('.auth-child:first');
				if(origin.is('.active')){
					/*收起*/
					origin.removeClass('active').html('&#xe623;');
					child.slideUp('fast');
				} else {
					/*展开*/
					origin.addClass('active').html('&#xe625;');
					child.slideDown('fast');
				}
				return false;
			})
			return obj;
		},
		// 递归创建格式
		renderAuth: function(tree, dept, opt){
			var inputname = opt.inputname;
			var layfilter = opt.layfilter;
			var openall = opt.openall;
			var opengrade = opt.opengrade;
			var selecttype = opt.selecttype;
			var str = '<div class="auth-single">';
			layui.each(tree, function(index, item){
				var hasChild = item.list ? 1 : 0;
				// 注意：递归调用时，this的环境会改变！
				var append = hasChild ? obj.renderAuth(item.list, dept+1, opt) : '';
				// '+new Array(dept * 4).join('&nbsp;')+'
				if(dept<=opengrade){
					openall=true;
				}
				str += '<div><div class="auth-status" dept="'+dept+'"> '+(hasChild?'<i class="layui-icon auth-icon '+(openall?'active':'')+'" style="cursor:pointer;">'+(openall?'&#xe625;':'&#xe623;')+'</i>':'<i class="layui-icon auth-leaf" style="opacity:0;">&#xe626;</i>')+(dept > 0 ? '<span style="color:#aaa;">├─ </span>':'')+'<input type="'+selecttype+'" name="'+inputname+'" title="'+item.name+'" value="'+item.value+'" lay-skin="primary" lay-filter="'+layfilter+'" '+(item.checked?'checked="checked"':'')+'> </div> <div class="auth-child" style="'+(openall?'':'display:none;')+'padding-left:40px;"> '+append+'</div></div>'
			});
			str += '</div>';
			return str;
		},
		// 获取选中叶子结点
		getLeaf: function(dst){
			var leafs = $(dst).find('.auth-leaf').parent().find('input[type="checkbox"]:checked');
			var data = [];
			leafs.each(function(index, item){
				// console.log(item);
				data.push(item.value);
			});
			// console.log(data);
			return data;
		},
		// 获取所有选中的数据
		getAll: function(dst){
			var inputs = $(dst).find('input[type="checkbox"]');
			var data = [];
			inputs.each(function(index, item){
				data.push(item.value);
			});
			// console.log(data);
			return data;
		},
		// 获取所有选中的数据
		getChecked: function(dst){
			var inputs = $(dst).find('input[type="checkbox"]:checked');
			var val = [],title=[];
			inputs.each(function(index, item){
				val.push(item.value);
				title.push($(item).attr('title'));
			});
			res={val:val,title:title};
			// console.log(data);
			return res;
		},
		// 获取未选中数据
		getNotChecked: function(dst){
			var inputs = $(dst).find('input[type="checkbox"]:not(:checked)');
			var val = [],title=[];
			inputs.each(function(index, item){
				val.push(item.value);
				title.push($(item).attr('title'));
			});
			res={val:val,title:title};
			// console.log(data);
			return res;
		},
		// 获取所有选中的radio数据
		getRadio: function(dst){
			var radios=$(dst).find('input[type="radio"]:checked');
			var res={val:'',title:''};
			var val = radios.val();
			var title=radios.attr('title');
			if(radios){
				res={val:val,title:title};
			}				
			return res;
		}
	}
	exports('authtree', obj);
});
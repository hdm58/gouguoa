mbui.define(['layer'], function (exports) {
	let layer = mbui.layer;
	var form = function (options) {
		// 默认配置选项
		var settings = $.extend({
			target: '#formBox',
			verify: {
				required: function (value) {
					if (!/[\S]+/.test(value)) {
						return '必填项不能为空';
					}
				},
				phone: function (value) {
					var EXP = /^1\d{10}$/;
					if (value && !EXP.test(value)) {
						return '手机号格式不正确';
					}
				},
				email: function (value) {
					var EXP = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					if (value && !EXP.test(value)) {
						return '邮箱格式不正确';
					}
				},
				url: function (value) {
					var EXP = /^(#|(http(s?)):\/\/|\/\/)[^\s]+\.[^\s]+$/;
					if (value && !EXP.test(value)) {
						return '链接格式不正确';
					}
				},
				number: function (value) {
					if (value && isNaN(value)) {
						return '只能填写数字';
					}
				},
				date: function (value) {
					var EXP = /^(\d{4})[-\/](\d{1}|0\d{1}|1[0-2])([-\/](\d{1}|0\d{1}|[1-2][0-9]|3[0-1]))*$/;
					if (value && !EXP.test(value)) {
						return '日期格式不正确';
					}
				},
				identity: function (value) {
					var EXP = /(^\d{15}$)|(^\d{17}(x|X|\d)$)/;
					if (value && !EXP.test(value)) {
						return '身份证号格式不正确';
					}
				}
			},
			errorClass: 'mbui-form-danger',
			successClass: 'mbui-form-success',
			errorMessageContainer: null, // 可以指定错误信息容器，例如：'div#error-message'
			submit: function (data) {
				console.log(data);
			}
		}, options);

		var formBox = $(settings.target);
		formBox.on('submit', function (event) {
			event.preventDefault();
			var isValid = true; //校验判断标识
			var field = {};  // 字段集合
			formBox.find('input,select,textarea').each(function () {
				if (isValid == false) {
					return false;
				}
				var $this = $(this),
					val = $this.val(),
					rule = $this.data('verify') || '',
					rules = rule.split('|'),
					errMsg = '';
				$this.removeClass(settings.errorClass); // 移除警示样式

				for (var i = 0; i < rules.length; i++) {
					var ruleName = rules[i],
						ruleFunc = settings.verify[ruleName];
					if (ruleFunc) {
						errMsg = ruleFunc(val);
						if (errMsg) {
							// 获取自定义必填项提示文本
							if (ruleName === 'required') {
								errMsg = $this.data('errortips') || errMsg;
							}
							isValid = false;
							break;
						}
					}
				}
				if (!isValid) {
					layer.error(errMsg);
					$this.removeClass(settings.successClass).addClass(settings.errorClass).focus();
					if (settings.errorMessageContainer) {
						$(settings.errorMessageContainer).text(errors.join('<br/>'));
					}
				} else {
					$this.removeClass(settings.errorClass).addClass(settings.successClass);
				}
			});
			//校验radio是否选中
			formBox.find('[data-verify="radio"]').each(function () {
				if (isValid == false) {
					return false;
				}
				var $this = $(this),errMsg = '至少要有一个单选按钮被选中';
				if ($this.find('[type="radio"]').is(':checked')) {
					console.log('至少有一个单选按钮被选中');
				} else {
					errMsg = $this.data('errortips') || errMsg;
					isValid = false;
				}
				if (!isValid) {
					layer.error(errMsg);
				}
			});
			formBox.find('[data-verify="checkbox"]').each(function () {
				if (isValid == false) {
					return false;
				}
				var $this = $(this),errMsg = '至少要有一个多选按钮被选中';
				if ($this.find('[type="checkbox"]').is(':checked')) {
					console.log('至少要有一个多选按钮被选中');
				} else {
					errMsg = $this.data('errortips') || errMsg;
					isValid = false;
				}
				if (!isValid) {
					layer.error(errMsg);
				}
			});
			if (isValid) {
				// 如果所有验证都通过，则提交表单
				settings.submit(form.getValue(settings.target));
			}
		});
	};

	// 取值
	form.getValue = function (itemForm) {
		var nameIndex = {} // 数组 name 索引
			, field = {}
			, fieldElem = $(itemForm).find('input,select,textarea') // 获取所有表单域

		$.each(fieldElem, function (_, item) {
			var othis = $(this)
				, init_name; // 初始 name

			item.name = (item.name || '').replace(/^\s*|\s*&/, '');
			if (!item.name) return;

			// 用于支持数组 name
			if (/^.*\[\]$/.test(item.name)) {
				var key = item.name.match(/^(.*)\[\]$/g)[0];
				nameIndex[key] = nameIndex[key] | 0;
				init_name = item.name.replace(/^(.*)\[\]$/, '$1[' + (nameIndex[key]++) + ']');
			}
			// 复选框和单选框未选中，不记录字段     
			if (/^(checkbox|radio)$/.test(item.type) && !item.checked) return;
			field[init_name || item.name] = item.value;
		});
		return field;
	};
	// 输出接口
	exports('form', form);
});
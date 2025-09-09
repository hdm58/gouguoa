mbui.define([], function (exports) {
	var doc = document, query = 'querySelectorAll', claname = 'getElementsByClassName', S = function (s) {
		return doc[query](s);
	};

	var template = {
		warning: function (text) {
			//警告提示
			return `<div class="mbui-toptips mbui-toptips-warning fixed" id="${classs}${index}">
						<i class="mbui-toptips-warning-icon"></i><span>${text}</span>
					</div>`;
		},
		error: function (text) {
			//失败提示
			return `<div class="mbui-toptips mbui-toptips-error fixed" id="${classs}${index}">
						 <i class="mbui-toptips-error-icon"></i><span>${text}</span>
					</div>`;
		},
		toast: function (text) {
			//轻提示
			return `<div class="mbui-toast" id="${classs}${index}">
						<div class="mbui-toast-backdrop"></div>
						<div class="mbui-toast-dialog">
							<div class="mbui-toast-content">
								<span>${text}</span>
							</div>
						</div>
					</div>`;
		},
		loading: function (text) {
			//轻提示
			return `<div class="mbui-toast mbui-toast-loading" id="${classs}${index}">
						<div class="mbui-toast-backdrop"></div>
						<div class="mbui-toast-dialog">
							<div class="mbui-toast-content">
								<span>
									<div class="mbui-toast-loading_icon">
										<div class="loading-icon-leaf loading-icon-leaf_0"></div>
										<div class="loading-icon-leaf loading-icon-leaf_1"></div>
										<div class="loading-icon-leaf loading-icon-leaf_2"></div>
										<div class="loading-icon-leaf loading-icon-leaf_3"></div>
										<div class="loading-icon-leaf loading-icon-leaf_4"></div>
										<div class="loading-icon-leaf loading-icon-leaf_5"></div>
										<div class="loading-icon-leaf loading-icon-leaf_6"></div>
										<div class="loading-icon-leaf loading-icon-leaf_7"></div>
										<div class="loading-icon-leaf loading-icon-leaf_8"></div>
										<div class="loading-icon-leaf loading-icon-leaf_9"></div>
										<div class="loading-icon-leaf loading-icon-leaf_10"></div>
										<div class="loading-icon-leaf loading-icon-leaf_11"></div>
									</div>
									${text}
								</span>
							</div>
						</div>
					</div>`;
		},
		success: function (text) {
			//轻提示
			return `<div class="mbui-toast mbui-toast-success" id="${classs}${index}">
						<div class="mbui-toast-backdrop"></div>
						<div class="mbui-toast-dialog">
							<div class="mbui-toast-content">
								<span><i class="mbui-toast-success-icon"></i>${text}</span>
							</div>
						</div>
					</div>`;
		}
	}

	//默认配置
	var config = {
		type: 1,
		shade: true,
		shadeClose: false,
		fixed: true
	};

	var ready = {
		extend: function (obj) {
			var newobj = JSON.parse(JSON.stringify(config));
			for (var i in obj) {
				newobj[i] = obj[i];
			}
			return newobj;
		},
		timer: {}, end: {}
	};

	//点触事件
	ready.touch = function (elem, fn) {
		elem.addEventListener('click', function (e) {
			fn.call(this, e);
		}, false);
	};

	var index = 0, classs = ['mbui-layer'], Layer = function (options) {
		var that = this;
		that.config = ready.extend(options);
		that.view();
	};

	Layer.prototype.view = function () {
		var that = this, config = that.config, layerbox = doc.createElement('div');

		that.id = layerbox.id = classs[0] + index;
		layerbox.setAttribute('class', classs[0] + ' ' + classs[0] + (config.type || 0));
		layerbox.setAttribute('index', index);

		//标题区域
		var title = (function () {
			var titype = typeof config.title === 'object';
			return config.title
				? '<div class="modal-header" style="' + (titype ? config.title[1] : '') + '">' + (titype ? config.title[0] : config.title) + '</div>' : '';
		}());

		//按钮区域
		var button = (function () {
			typeof config.btn === 'string' && (config.btn = [config.btn]);
			var btns = (config.btn || []).length, btndom;
			if (btns === 0 || !config.btn) {
				return '';
			}
			btndom = '<button type="button" types="1" class="btn btn-block active">' + config.btn[0] + '</button>'
			if (btns === 2) {
				btndom = '<a href="javascript:void(0);" types="0" class="btn">' + config.btn[1] + '</a><button type="button" types="1" class="btn active">' + config.btn[0] + '</button>';
			}
			return '<div class="modal-footer clearfix">' + btndom + '</div>';
		}());

		if (!config.fixed) {
			config.top = config.hasOwnProperty('top') ? config.top : 100;
			config.style = config.style || '';
			config.style += ' top:' + (doc.body.scrollTop + config.top) + 'px';
		}

		if (config.type === 0) {
			config.shade = false;
			let content = template[config.template];
			layerbox.innerHTML = content(config.content);
		}

		if (config.type === 2) {
			config.content = '<div class="form-hairlines"><input type="text" id="prompt' + index + '" class="mbui-input" placeholder="请输入内容"></div>';
		}
		
		if (config.type === 3) {
			config.className = 'modal-view top';
			config.content = '<div style="padding:0 16px 16px;"><textarea rows="5" id="textarea' + index + '" class="mbui-textarea" placeholder="请输入内容"></textarea></div>';
		}

		if (config.type === 4) {
			config.className = 'modal-actionsheet bottom';
		}

		if (config.type === 5) {
			config.className = 'modal-view top';
		}

		if (config.type > 0) {
			layerbox.innerHTML = (config.shade ? '<div ' + (typeof config.shade === 'string' ? 'style="' + config.shade + '"' : '') + ' class="mbui-layershade"></div>' : '')
				+ '<div class="mbui-layermain" ' + (!config.fixed ? 'style="position:static;"' : '') + '>'
				+ '<div class="modal-dialog">'
				+ '<div class="modal-content ' + (config.className ? config.className : '') + '" ' + (config.style ? 'style="' + config.style + '"' : '') + '>'
				+ title
				+ '<div class="modal-body">' + config.content + '</div>'
				+ button
				+ '</div>'
				+ '</div>'
				+ '</div>';
		}

		document.body.appendChild(layerbox);
		var elem = that.elem = S('#' + that.id)[0];
		config.success && config.success(elem,index);
		that.index = index++;
		that.action(config, elem);
	};

	Layer.prototype.action = function (config, elem) {
		var that = this;
		//自动关闭
		if (config.time && config.time > 0) {
			ready.timer[that.index] = setTimeout(function () {
				layer.close(that.index);
			}, config.time * 1000);
		}

		//确认取消
		var btn = function () {
			var type = this.getAttribute('types');
			if (type == 0) {
				config.no && config.no();
				layer.close(that.index);
			} else {
				config.yes ? config.yes(that.index) : layer.close(that.index);
			}
		};

		if (config.btn) {
			var btns = elem[claname]('modal-footer')[0].children, btnlen = btns.length;
			for (var ii = 0; ii < btnlen; ii++) {
				ready.touch(btns[ii], btn);
			}
		}

		//点遮罩关闭
		if (config.shade && config.shadeClose) {
			var shade = elem[claname]('mbui-layershade')[0];
			ready.touch(shade, function () {
				layer.close(that.index, config.end);
			});
		}
		config.end && (ready.end[that.index] = config.end);
	};

	var layer = {
		v: '1.0',
		index: index,
		//核心方法
		open: function (options) {
			var o = new Layer(options || {});
			return o.index;
		},
		alert: function (txt) {
			layer.open({
				type: 1,
				title: '提示',
				content: txt,
				btn: ['确定']
			});
		},
		confirm: function (txt, fun) {
			layer.open({
				type: 1,
				title: '温馨提示',
				content: txt,
				btn: ['确定', '取消'],
				yes: function (index) {
					fun ? fun(index) : layer.close(index);
				}
			});
		},
		prompt: function (title,fun,txt) {
			layer.open({
				type: 2,
				title: title,
				content: txt,
				btn: ['确定', '取消'],
				yes: function (index) {
					let val = $('#prompt' + index).val();
					fun ? fun(val,index) : layer.close(index);
				}
			});
		},
		textarea: function (title,fun,txt) {
			layer.open({
				type: 3,
				title: title,
				content: txt,
				btn: ['确定', '取消'],
				yes: function (index) {
					let val = $('#textarea' + index).val();
					fun ? fun(val,index) : layer.close(index);
				}
			});
		},
		msg: function (txt = '', time = 3, template = 'toast') {
			let msg = layer.open({
				type: 0,
				content: txt,
				template: template,
				time: time
			});
			return msg;
		},
		success: function (txt) {
			layer.msg(txt, 3, 'success');
		},
		warning: function (txt) {
			layer.msg(txt, 3, 'warning');
		},
		error: function (txt) {
			layer.msg(txt, 3, 'error');
		},
		loading: function (txt) {
			let loading = layer.msg(txt, 3, 'loading');
			return loading;
		},
		photo: function (url) {
			var $image = $('<div class="layer-image-photo"><img src="' + url + '"></div>');
			$('body').append($image);
			$image.fadeIn();
			$image.click(function () {
				$(this).fadeOut(function () {
					$(this).remove();
				});
			});
		},
		close: function (index) {
			var ibox = S('#' + classs[0] + index)[0];
			if (!ibox) return;
			ibox.innerHTML = '';
			doc.body.removeChild(ibox);
			clearTimeout(ready.timer[index]);
			delete ready.timer[index];
			typeof ready.end[index] === 'function' && ready.end[index]();
			delete ready.end[index];
		},
		//关闭所有layer层
		closeAll: function () {
			var boxs = doc[claname](classs[0]);
			for (var i = 0, len = boxs.length; i < len; i++) {
				layer.close((boxs[0].getAttribute('index') | 0));
			}
		}
	};
	// 输出接口
	exports('layer', layer);
});
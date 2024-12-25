mbui.define(['layer'], function (exports) {
	var layer = mbui.layer;
	var userSelector = function () {
		this.config = {
			type:1,
			url: "/api/index/get_employee",
			where: {},
			callback: function(){}
		};
		this.loaded = 0;
	};
	// 初始化
	userSelector.prototype.init = function (options) {
		var that = this;
		$.extend(true, that.config, options);
		that.select();
	};
	
	userSelector.prototype.select = function () {
		var that = this;
		var types = that.config.type == 2 ? 'checkbox' : 'radio';
		var users = [];
		var alphabet = [];
		var userGroups = {};
		var selector = $('.user-selector');
		if (selector.length > 0) {
			return false;
		}
		let loading;
		$.ajax({
			url: that.config.url,
			type: 'get',
			data: { did: 0 },
			beforeSend:function(){
				loading = layer.loading('加载中...');
			},
			success: function (e) {
				if (e.code == 0) {
					users = e.data;
					bulidSelector();
				}
			},
			complete:function(){
				layer.close(loading);
			}
		})

		function bulidSelector() {
			// 将用户账号按照首字母分组
			for (var i = 0; i < users.length; i++) {
				var user = users[i]['username'];
				var firstLetter = user.charAt(0).toUpperCase();
				if (!userGroups[firstLetter]) {
					userGroups[firstLetter] = [];
					alphabet.push(firstLetter);
				}
				userGroups[firstLetter].push(users[i]);
			}
			alphabet.sort();
			var $container = $('<div class="user-selector"><header class="mbui-bar"><a class="mbui-bar-item left" href="javascript:;"><i class="mbui-bar-arrow-left"></i>返回</a><a class="mbui-bar-item right" href="javascript:;">确认</a><h1 class="mbui-bar_title">选择员工</h1></header></div>');
			var $letters = $('<div class="letters"></div>');
			var $userList = $('<div class="contacts mbui-' + types + '" data-toggle="buttons"></div>');

			var item = '';
			for (var j = 0; j < alphabet.length; j++) {
				var $letterLink = $('<a href="#' + alphabet[j] + '">' + alphabet[j] + '</a>');
				$letters.append($letterLink);

				item += '<div class="mbui-contacts-title" id="' + alphabet[j] + '">' + alphabet[j] + '</div>';
				var userData = userGroups[alphabet[j]];
				for (var k = 0; k < userData.length; k++) {
					item += '<label class="mbui-contacts-item border-top border-bottom btn" data-id="' + userData[k]['id'] + '" data-did="' + userData[k]['did'] + '" data-name="' + userData[k]['name'] + '" data-department="' + userData[k]['department'] + '">\
						<input type="'+ types + '" value="' + userData[k]['id'] + '">\
						<span class="mbui-'+ types + '_ft"></span>\
						<i class="mbui-'+ types + '_hd"><img src="' + userData[k]['thumb'] + '" width="32" height="32" align="mbui" style="border-radius:6px;"></i>\
						<span class="mbui-'+ types + '_bd">' + userData[k]['name'] + '<span class="department">『' + userData[k]['department'] + '』</span></span>\
					</label>';
				}
			}
			$userList.append(item);
			$container.append($letters).append($userList);
			$('body').append($container);

			// 点击字母滚动到对应的字母开头的用户位置
			$letters.on('click', 'a', function (e) {
				e.preventDefault();
				var letter = $(this).attr('href').substring(1);
				var $letterSection = $('#' + letter);
				var scrollTop = $letterSection.offset().top - $userList.offset().top + $userList.scrollTop();
				$userList.animate({ scrollTop: scrollTop }, 500);
			});

			$container.find('.left').click(function () {
				$container.fadeOut(function () {
					$container.remove();
				});
			});

			$container.find('.right').click(function () {
				let selected = $container.find('.active');
				if (selected.length == 0) {
					layer.msg('请选择员工');
					return false;
				}
				let ids = [], names = [], dids = [],departments=[];
				for (var m = 0; m < selected.length; m++) {
					ids.push($(selected[m]).data('id'));
					names.push($(selected[m]).data('name'));
					dids.push($(selected[m]).data('dids'));
					departments.push($(selected[m]).data('departments'));
				}
				that.config.callback(ids,names,dids,departments);
				$container.fadeOut(function () {
					$container.remove();
				});
			});
		}
	};
	
	//选择员工弹窗		
	$('body').on('click','.picker-admin',function () {
		let that = $(this);
		let type = that.data('type');
		if (typeof(type) == "undefined" || type == '') {
			type = 1;
		}
		let ids=that.next().val()+'',names = that.val()+'';
		let picker = new userSelector();
		picker.init({
			type:type,
			callback:function(ids,names,dids,departments){
				that.val(names.join(','));
				that.next().val(ids.join(','));
			}
		});
	});
	// 导出userPicker模块
	exports('userPicker', function (options) {
		var userPicker = new userSelector();
		userPicker.init(options);
	});
});


// BUTTON PUBLIC CLASS DEFINITION
// ==============================
var Button = function (element, options) {
    this.$element  = $(element)
    this.options   = $.extend({}, Button.DEFAULTS, options)
    this.isLoading = false
}
Button.DEFAULTS = {
    loadingText: 'loading...'
}

Button.prototype.setState = function (state) {
    var d    = 'disabled'
    var $el  = this.$element
    var val  = $el.is('input') ? 'val' : 'html'
    var data = $el.data()

    state += 'Text'

    if (data.resetText == null) $el.data('resetText', $el[val]())

    // push to event loop to allow forms to submit
    setTimeout($.proxy(function () {
      $el[val](data[state] == null ? this.options[state] : data[state])

      if (state == 'loadingText') {
        this.isLoading = true
        $el.addClass(d).attr(d, d)
      } else if (this.isLoading) {
        this.isLoading = false
        $el.removeClass(d).removeAttr(d)
      }
    }, this), 0)
}

Button.prototype.toggle = function () {
	var changed = true
	var $parent = this.$element.closest('[data-toggle="buttons"]')

	if ($parent.length) {
	  var $input = this.$element.find('input')
	  if ($input.prop('type') == 'radio') {
		if ($input.prop('checked')) changed = false
		$parent.find('.active').removeClass('active')
		this.$element.addClass('active')
	  } else if ($input.prop('type') == 'checkbox') {
		if (($input.prop('checked')) !== this.$element.hasClass('active')) changed = false
		this.$element.toggleClass('active')
	  }
	  $input.prop('checked', this.$element.hasClass('active'))
	  if (changed) $input.trigger('change')
	} else {
	  this.$element.attr('aria-pressed', !this.$element.hasClass('active'))
	  this.$element.toggleClass('active')
	}
}


// BUTTON PLUGIN DEFINITION
// ========================

function Plugin(option) {
	return this.each(function () {
	  var $this   = $(this)
	  var data    = $this.data('bs.button')
	  var options = typeof option == 'object' && option

	  if (!data) $this.data('bs.button', (data = new Button(this, options)))

	  if (option == 'toggle') data.toggle()
	  else if (option) data.setState(option)
	})
}

$.fn.button = Plugin
// BUTTON DATA-API
// ===============

$(document).on('click.bs.button.data-api', '[data-toggle^="button"]', function (e) {
      var $btn = $(e.target)
      if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
      Plugin.call($btn, 'toggle')
      if (!($(e.target).is('input[type="radio"]') || $(e.target).is('input[type="checkbox"]'))) e.preventDefault()
    }).on('focus.bs.button.data-api blur.bs.button.data-api', '[data-toggle^="button"]', function (e) {
      $(e.target).closest('.btn').toggleClass('focus', /^focus(in)?$/.test(e.type))
})
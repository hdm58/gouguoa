mbui.define(['tool'], function (exports) {
	let tool = mbui.tool;
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
	
	var LoadData = function () {
		this.config = {
			elem: "#listBox",
			url: "",
			where: {},
			limit: 10,
			scroll: 1,
			template: function (data) {
				return JSON.parse(data);
			}
		};
		this.loaded = 0;
		this.page = 1;
		this.count = 0;
		this.total = 0;
	};
	// 初始化
	LoadData.prototype.init = function (options) {
		var that = this;
		$.extend(true, that.config, options);
		var elem = $(that.config.elem);
		elem.html('<div class="load-data-container"></div><div class="load-data-none"><i class="iconfont icon-none"></i><br>暂无数据</div><div class="load-data-loading"><span>努力加载中</span></div><div class="load-data-end"><span>—— ● ——</span></div>');
		
		if(that.config.scroll==2){
			$('#root').scroll(function(){
				if ($(this).scrollTop() + $('#root').height() >= $('#app').height()-10) {					
					// 滚动到页面底部时加载更多数据
					if (that.total < that.count && that.loaded == 0){
						that.ajax();
					}
				}
			});
		}
		else{
			// 监听滚动事件
			$(window).scroll(function () {
				if ($(window).scrollTop() + $(window).height() >= $(document).height()-10) {
					// 滚动到页面底部时加载更多数据
					if (that.total < that.count && that.loaded == 0){
						that.ajax();
					}
				}
			});
		}
		that.ajax();
	};

	LoadData.prototype.ajax = function () {
		var that = this;
		var elem = $(that.config.elem);
		// 发送请求获取数据
		$.ajax({
			url: that.config.url + '?page=' + that.page + '&limit=' + that.config.limit,
			type: 'GET',
			data: that.config.where,
			beforeSend: function () {
				// 显示加载按钮
				that.loaded = 1;
				elem.find('.load-data-loading').show();
				elem.find('.load-data-end').hide();
			},
			success: function (res) {
				that.count=res.count;
				that.total+=res.data.length;
				elem.find('.load-data-none').addClass('load-data-'+that.count);
				if (res.count > 0) {
					that.page++;
					$.each(res.data, function (index, item) {
						// 转义JSON对象中的字符串值,防止XSS
						for (var key in item) {
						  if (typeof item[key] === 'string') {
							item[key] = escapeHtml(item[key]);
						  }
						}
						// 创建列表项并添加到列表中
						var listItem = that.config.template(item);
						elem.find('.load-data-container').append(listItem);
					});
				}
			},
			complete: function () {
				that.loaded = 0;
				elem.find('.load-data-loading').hide();
				elem.find('.load-data-end').show();
			}
		});
	}

	// 导出loadData模块
	exports('loadData', function (options) {
		var loadData = new LoadData();
		loadData.init(options);
	});
});
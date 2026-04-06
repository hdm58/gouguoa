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
			seachBar: "#listSearch",
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
		elem.html('<div class="load-data-container"></div><div class="load-data-none"><i class="iconfont icon-none"></i><br>暂无数据</div><div class="load-data-loading"><span>努力加载中</span></div><div class="load-data-end"><span>—————— 底 ● 线 ——————</span></div>');
		
		if(that.config.scroll==2){
			//容器监听滚动事件
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
			// 页面监听滚动事件
			$(window).scroll(function () {
				if ($(window).scrollTop() + $(window).height() >= $(document).height()-10) {
					// 滚动到页面底部时加载更多数据
					if (that.total < that.count && that.loaded == 0){
						that.ajax();
					}
				}
			});
		}
		//回车搜索
		$(that.config.seachBar).on('keyup','[type="search"]',function(event){
			if (event.keyCode === 13) {
				$(this).blur();
				that.page=1;
				that.ajax();
			}
		})
		//清空时
		$(that.config.seachBar).on('input','[type="search"]', function() {
			var val = $(this).val();			
			// 如果值为空，说明可能点击了 X 或者手动删除了
			if (val === '') {
				$(this).blur();
				that.page=1;
				that.ajax();
			}
		});
		//点击搜索重置按钮
		$(that.config.seachBar).on('click','.search-reset',function(){
			$(that.config.seachBar).find('input').val('');
			that.page=1;
			that.ajax();
		})
		that.ajax();
	};

	LoadData.prototype.reload = function () {
		var that = this;
		that.page=1;
		that.ajax();
	};

	LoadData.prototype.ajax = function () {
		var that = this;
		var elem = $(that.config.elem),seachBar = $(that.config.seachBar),map = {page:that.page,limit:that.config.limit};
		var keys = seachBar.find('.search-key');
		keys.each(function(){
			let key=$(this).attr('name');
			let val=$(this).val();
			if(val!=''){
				map[key]=val;
			}
		});
		var maps = $.extend({}, that.config.where, map);
		// 发送请求获取数据
		$.ajax({
			url: that.config.url,
			type: 'GET',
			data: maps,
			beforeSend: function () {
				// 显示加载按钮
				that.loaded = 1;
				elem.find('.load-data-loading').show();
				elem.find('.load-data-end').hide();
			},
			success: function (res) {
				if(res.count === 'undefined'){
					that.count=res.data.length;
					that.total=res.data.length;
				}
				else{
					that.count=res.count;
					that.total+=res.data.length;
				}
				if(that.page==1){
					elem.find('.load-data-container').html('');
				}
				if(that.count>=that.total && that.count>0){
					elem.find('.load-data-end').show();
				}
				elem.find('.load-data-none').addClass('load-data-'+that.count);
				if (res.data.length > 0) {
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
			}
		});
	}

	// 导出loadData模块
	exports('loadData', function (options) {
		var loadData = new LoadData();
		loadData.init(options);
		return loadData;
	});
});
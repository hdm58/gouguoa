layui.define([], function (exports) {
	var MOD_NAME = 'excel';
	var modFile = layui.cache.modules['excel'];
	var modPath = modFile.substr(0, modFile.lastIndexOf('.'));
	var settings = {
		name: '表格'
	};
	var excel = {
		init: function (tableId, options) {
			loadScript();
			var opts = $.extend({}, settings, options);
			$('#'+tableId).table2excel({
				name: opts.name,
				filename: opts.name + new Date().getTime() + ".xls",
				exclude: ".noExl",
				exclude_img: false,
				exclude_links: false,
				exclude_inputs: false,
				preserveColors:true
			});
		}
	}
	function loadScript() {
		if (typeof table2excel == 'undefined') {
			$.ajax({ //获取插件
				url: modPath + '/table2excel.js' ,
				dataType: 'script',
				cache: true,
				async: false
			});
		}
	}
	loadScript();
	exports(MOD_NAME, excel);
});

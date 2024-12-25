mbui.define([], function (exports) {
  var Tab = function () {
    this.config = {
      elem: null,
      trigger: 'click',
      ontab: null
    };
  };

  // 初始化Tab
  Tab.prototype.init = function (options) {
    var that = this;
    $.extend(true, that.config, options);
    var elem = $(that.config.elem);
    var tabs = elem.find('.mbui-tab-title li');
    var contents = elem.find('.mbui-tab-content .mbui-tab-item');

    tabs.on(that.config.trigger, function () {
      var index = $(this).index();
      tabs.removeClass('active');
      contents.removeClass('mbui-tab-show');
      $(this).addClass('active');
      contents.eq(index).addClass('mbui-tab-show');

      // 执行回调函数
      if (typeof that.config.ontab === 'function') {
        that.config.ontab(index, $(this));
      }
    });
  };

  // 导出Tab模块
  exports('tab', function (options) {
    var tab = new Tab();
    tab.init(options);
  });
});
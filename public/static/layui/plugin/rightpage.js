layui.define(['layer'], function(exports){
    var layer = layui.layer;
    var obj = {
		callback:null,
        open: function (content='',width='88%',callback) {
			if(callback && typeof callback === 'function'){
				this.callback = callback;
			}
            layer.open({
                type: 2,
                title: '',
                offset: ['0', '100%'],
                skin: 'layui-anim layui-anim-rl layui-layer-admin-right',
                closeBtn: 0,
                content: content,
                area: [width, '100%'],
				success:function(obj,index){
					if($('#rightPopup'+index).length<1){
						var btn='<div id="rightPopup'+index+'" class="right-popup-close" title="关闭">关闭</div>';
						obj.append(btn);
						$('#rightPopup'+index).click(function(){
							let op_width = $('.layui-anim-rl').outerWidth();
							$('.layui-anim-rl').animate({left:'+='+op_width+'px'}, 200, 'linear', function () {
								$('.layui-anim-rl').remove()
								$('.layui-layer-shade').remove()
							})
						})
					}
				}
            })
        },
		success:function(){
			$('.right-popup-close').click();
			var d = this;
			setTimeout(function() {
				d.timer = null;
				d.callback && d.callback();
			}, 300)
		},
		close: function(){
			$('.right-popup-close').click();
		}
    };
    exports('rightpage', obj);
});

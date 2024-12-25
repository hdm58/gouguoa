mbui.define([], function (exports) {
	$('body').append('<div class="mbui-fixed-bar">\
			<div class="fixed-bar-box">\
				<button class="fixed-bar-btn"><span class="menutoggle"><span></span></span></button>\
			</div>\
			<div class="fixed-bar-item">\
				<a href="/qiye/index/index">工作台</a>\
				<a href="/qiye/customer/index">客户<br>管理</a>\
				<a href="/qiye/contract/index">合同<br>管理</a>\
				<a href="/qiye/project/index">项目<br>管理</a>\
				<a href="/qiye/msg/index">消息<br>通知</a>\
			</div>\
		</div>');
	$('.fixed-bar-btn').click(function () {
		$(this).toggleClass('active');
		return $('.fixed-bar-item').toggleClass('open');
	});
	$('.fixed-bar-item').on('click','a',function(e){
		e.preventDefault();
		let href=$(this).attr('href');
		location.replace(href);
	})	
	
	var startX, startY, moveX, moveY;
	var browserWidth = $(window).width();
	var browserHeight = $(window).height();
    var $button = $('.mbui-fixed-bar');
	
    $button.on('touchstart', function(e){
		startX = e.touches[0].pageX - parseInt($button.css('left'));
		startY = e.touches[0].pageY - parseInt($button.css('top'));
		$button.on('touchmove', function(e){
			e.preventDefault();
			moveX = e.touches[0].pageX - startX;
			moveY = e.touches[0].pageY - startY;
			if(moveY>100 && moveY<browserHeight-206){
				$button.css({
					top: moveY + 'px'
				});
			}
			if(moveX>8 && moveX<browserWidth-166){
				$button.css({
					left: moveX + 'px'
				});
			}
		});
		$button.on('touchend', function(){
			$button.off('touchmove').off('touchend');
		});
    });
	
	// 导出Tab模块
	exports('fixedbar', function () {

	});
});
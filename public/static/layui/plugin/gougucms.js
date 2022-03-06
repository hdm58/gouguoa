layui.define(['element'], function(exports){
	var MOD_NAME = 'tab';
    var element = layui.element;
    var tab = {
		//在这里给active绑定几项事件，后面可通过active调用这些事件
		tabAdd: function(url,id,name) {
			//新增一个Tab项 传入三个参数，分别对应其标题，tab页面的地址，还有一个规定的id，是标签中data-id的属性值
			//关于tabAdd的方法所传入的参数可看layui的开发文档中基础方法部分
			element.tabAdd('gougu-admin-tab', {
				title: '<span class="gougu-tab-active"></span>'+name,
				content: '<iframe id="'+id+'" data-frameid="'+id+'" src="'+url+'" frameborder="0" align="left" width="100%" height="100%" scrolling="yes"></iframe>',
				id: id //规定好的id
			});
			this.tabChange(id);
			var thetabs = $('.layui-tab-title').find('li');
			if(thetabs.length>10){
				layer.tips('点击LOGO快速关闭打开的TAB',$('[ittab-home]'));
			}
			// FrameWH();  //计算ifram层的大小
		},
		tabChange: function(id) {
			//根据传入的id传入到指定的tab项，并滚动定位
			element.tabChange('gougu-admin-tab', id);
			
			var $tabTitle = $('.layui-tab-title');
			var autoLeft = 0;
			$tabTitle.children("li").each(function() {
				if ($(this).hasClass('layui-this')) {
					return false;
				} else {
					autoLeft += $(this).outerWidth();
				}
			});
			$tabTitle.animate({
				scrollLeft: autoLeft - $tabTitle.width() / 3
			}, 200);
		}, 
		tabDelete: function (id) {
			element.tabDelete('gougu-admin-tab', id);//删除
		},
		tabDeleteAll: function (ids) {
			//删除所有
			$.each(ids, function (i,item) {
				//ids是一个数组，里面存放了多个id，调用tabDelete方法分别删除
				element.tabDelete('gougu-admin-tab', item);
			})
		},
		//子页面打开新的窗口
		sonAdd: function(url,name) {
			var id=new Date().getTime();
			this.tabAdd(url,id,name);
		},
		//子页面关闭窗口
		sonDelete: function(id) {
			$('.layui-tab .layui-tab-title .layui-this i').click();//框架页面删除tab
		},
		tabRoll: function(d) {
			var $tabTitle = $('.layui-tab-title');
			var left = $tabTitle.scrollLeft();
			if ('left' === d) {
				$tabTitle.animate({
					scrollLeft: left - 360
				}, 200);
			} else {
				$tabTitle.animate({
					scrollLeft: left + 360
				}, 200);
			}
		}		
    };
	layui.tab=tab;	
	
	//关闭全部tab，只保留首页
	$("[ittab-home]").on('click', function(){
		var thetabs = $('.layui-tab-title').find('li'),ids=[];
		var thisid=$('.layui-this').attr('lay-id');
		for(var i=0;i<thetabs.length;i++){
			var id=thetabs.eq(i).attr('lay-id');
			if(id !=thisid){
				ids.push(id);	
			}					
		}
		if(ids.length > 0){
	    	for (var a = 0; a < ids.length; a++) {
	    		if(ids[a] != 0){
	    			element.tabDelete('gougu-admin-tab', ids[a]);
	    		}
	    	}
	    }
		return false;
	})	
	
	//左右滚动菜单
	$("#right_button").click(function() {
		tab.tabRoll("right");
	})
	
	$("#left_button").click(function() {
		tab.tabRoll("left");
	})

    //当点击有menu-active属性的标签时，即左侧菜单栏中内容 ，触发点击事件
    $('body').on('click', 'a.menu-active', function() {
        var that = $(this);
		var src=that.data("src"),id=that.data("id"),title=that.data("title");
		if(src=='' || src=='/'){
			return false;
		}
        //这时会判断右侧.layui-tab-title属性下的有lay-id属性的li的数目，即已经打开的tab项数目
		$('.site-menu-active').removeClass('layui-this');
		that.addClass('layui-this');
        if ($(".layui-tab-title li[lay-id]").length <= 0) {
            //如果比零小，则直接打开新的tab项
            tab.tabAdd(src, id,title);
        } else {
            //否则判断该tab项是否以及存在
            var isData = false; //初始化一个标志，为false说明未打开该tab项 为true则说明已有
            $.each($(".layui-tab-title li[lay-id]"), function () {
                //如果点击左侧菜单栏所传入的id 在右侧tab项中的lay-id属性可以找到，则说明该tab项已经打开
                if ($(this).attr("lay-id") == id) {
                    isData = true;
					$('[data-frameid="'+id+'"]').attr('src',src);
					//最后不管是否新增tab，最后都转到要打开的选项页面上
					tab.tabChange(id);
                }
            })
            if (isData == false) {
                //标志为false 新增一个tab项
                tab.tabAdd(src, id,title);
            }
        }
    });


	//右上角刷新
	$("[ittab-refresh]").on('click', function(){
		if($(this).hasClass("refreshThis")){
			$("[ittab-loading]").show();
			$(this).removeClass("refreshThis");
			var iframe = $(".layui-tab-item.layui-show").find("iframe")[0];
			if(iframe){
				var src = parent.document.getElementById(iframe.id).contentWindow.location.href ? parent.document.getElementById(iframe.id).contentWindow.location.href : iframe.src;
				document.getElementById(iframe.id).src=src;
			}
			setTimeout(function(){
				$("[ittab-loading]").hide();
			},500)
			setTimeout(function(){
				$("[ittab-refresh]").attr("class","refreshThis");
			},1000)
		}else{
			layer.tips("每秒只可刷新一次",this, {
			  tips: 1
			});
		}
		return false;
	})

	//清除缓存
	$("[ittab-del-cache]").on('click', function(e){
		var that = $(this);
		if(that.attr('class') === 'clearThis'){
			layer.tips('正在努力清理中...',this);
			return false;
		}
		layer.tips('正在清理系统缓存...',this);
		that.attr('class','clearThis');
		$.ajax({
		  	url:"/api/index/cache_clear",
		  	success:function(res){
		  		if(res.code == 1){
		  			setTimeout(function(){
		  				that.attr('class','');
		  				layer.tips(res.msg,that);
		  			},1000)
		  		} else {
		  			layer.tips(res.msg,that);
		  		}
		  	}
		})
	})
	

    function FrameWH() {
        var h = $(window).height() -41- 10 - 60 -10-44 -10;
        $("iframe").css("height",h+"px");
    }

    $(window).resize(function () {
        //FrameWH();
    })
	

    exports('gougucms', {});
});  
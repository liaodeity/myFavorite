/**
 * @desc 2014-7迭代
 * @desc 2015-02-09
 */
/*cookie核心*/

jQuery.cookie = function(name, value, options) {
	if (typeof value != 'undefined') {
		options = options || {};
		if (value === null) {
			value = '';
			options.expires = -1;
		}
		var expires = '';
		if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
			var date;
			if (typeof options.expires == 'number') {
				date = new Date();
				date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
			} else {
				date = options.expires;
			}
			expires = '; expires=' + date.toUTCString();
		}
		var path = options.path ? '; path=' + options.path : '';
		var domain = options.domain ? '; domain=' + options.domain : '';
		var secure = options.secure ? '; secure' : '';
		document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	} else { // only name given, get cookie
		var cookieValue = null;
		if (document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for (var i = 0; i < cookies.length; i++) {
				var cookie = jQuery.trim(cookies[i]);
				if (cookie.substring(0, name.length + 1) == (name + '=')) {
					cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
					break;
				}
			}
		}
		return cookieValue;
	}
};
//图片延迟加载
(function($) {
    $.fn.picLoad = function(options) {
	    //默认配置
		var defaults = {
			checkShow :       false  //是否在页面加载完后马上执行判断显示
        };
		var options = $.extend(defaults, options);

        var failurelimit = 1;
        var elements = this;
        //Ipad Iphone iPod 的特殊处理，因为他们无法触发scroll事件
        //此时提前加载所有图片
        var naviUA = navigator.userAgent;
        //验证是否该显示图片
        var checkToShow = function(){
            var counter = 0;
            elements.each(function() {
                if (!$.belowthefold(this) ) {//&& !$.rightoffold(this) 左右判断问题
                        $.toshowimg(this);
                } else {
                    if (counter++ > failurelimit) {
                        return false;
                    }
                }
            });

            var temp = $.grep(elements, function(element) {
                return !element.loaded;
            });
            elements = $(temp);
       }
       if( naviUA.match(/iPhone|iPad|iPod/i) ) {
       		elements.each(function() {
       			$.toshowimg(this);
       		});
       }else{
	        $(window).bind("scroll", function() {
	            checkToShow();
	        });
	        $(window).bind("resize", function() {
	            checkToShow();
	        });
       }

        //马上进行判断
        if(options.checkShow){
            checkToShow();
        }
    };
    //显示图片
    $.toshowimg = function(element) {
        if (!element.loaded && $(element).attr("data-lazyload-src")) {
            $(element).attr("src", $(element).attr("data-lazyload-src"));
        }
        element.loaded = true;
    }
    //判断到了图片位置
    $.belowthefold = function(element) {
        var fold = $(window).height() + $(window).scrollTop();
        return fold <= $(element).offset().top-1000;// 提前加载400像素加载
    };
    $.rightoffold = function(element) {
        var fold = $(window).width() + $(window).scrollLeft();
        return fold <= $(element).offset().left;
    };
   
})(jQuery);

//图片滚动
(function($) {
	//插件更改日志：1如果只有一张的时候，则不轮播，并且不显示上、下一张的按钮
	$.fn.scrollShow = function(options){
	    //默认配置
		var defaults = {				
			vertical:       false  //垂直滚动
			,speed: 		800//滚动速度，越大越慢
			,auto:			false//自动
			,pausetm:		5000//自动的时候停止时间
            ,preId:         false //前滚的选择符 如 #prebtn
            ,nextId:        false //后滚的选择符 如 #nextbtn
            ,items:         false //滚动的元素 如 #scroll li
            ,itemparent:     false //滚动元素的外层 #scroll ul
            ,itemSpace:     200//宽度或者高度
            ,overstop:      true //鼠标放上去的时候停止滚动
            ,playbutton:    false//点击播放选择符 如 #playbtn
            ,playclass:     false//播放的样式 如 palystyle
            ,pauseclass:    false//暂停的样式 如 pausestyle
            ,callback:      function(){} //回调函数，滚动停止后
            
        };        
		var options = $.extend(defaults, options);
        if(!options.items || !options.itemparent )return;
        var callback = options.callback;
		var timeout = null;      
        var moveObj1 = null;
        var moveObj2 = null;
        var playlock =true;
        var scrolllock = false;//防止多次点击，滚动的时候加锁
        if(options.vertical){
              moveObj1 = {marginTop: -options.itemSpace};
              moveObj2 = {marginTop: 0};        
        }else{
              moveObj1 = {marginLeft: -options.itemSpace};
              moveObj2 = {marginLeft: 0};
          
        }
        //只有一张图的时候的处理 by suhy
        if($(options.items).length<=1&&options.nextId !='.package-pic-news .package-pic-news-btn'){
            $(options.preId+','+options.nextId).hide();
        	return false;
        }
        
        
        if(options.playbutton){
			$(options.playbutton).click(function() {
				if(timeout){
					$(options.playbutton).removeClass();
					$(options.playbutton).addClass(options.playclass);
					clearInterval(timeout);
					timeout = null;
					playlock =false;
					if(options.auto)options.auto=false;
				}else{
					$(options.playbutton).removeClass();
					$(options.playbutton).addClass(options.pauseclass);
					timeout = setInterval(___next,options.pausetm);
					playlock =true;
				}
			});
		}
		
        //往前滚
		function ___pre(){
			//加锁
			if(scrolllock)return;
			scrolllock = true;

			//将最后一个孩子节点移动到最前面了，防止右移动的时候出现空白
			callback($(options.items + ":last-child"));
			$(options.items + ':last-child').prependTo(options.itemparent);
			// 将移动过来的节点，推到可视范围外
			$(options.items + ":first-child").css(moveObj1).animate(moveObj2,options.speed,function(){
				scrolllock = false;
			});

		};
		//往后滚
		function ___next(){
			//加锁
			if(scrolllock)return;
			scrolllock = true;
			callback($(options.items + ":nth-child(2)"));
			//第一步 将第一个元素慢慢移动到界外   第二步 将第一个元素加到最后
			$(options.items + ":first-child").animate(moveObj1,options.speed,function(){
				setTimeout(function(){$(options.items + ":first-child").css(moveObj1).appendTo(options.itemparent).css(moveObj2);               scrolllock = false;},200);

			});
		};

		//设置前后按钮的点击事件
		if(options.preId){
			$(options.preId).click(function(){
				if(timeout)clearInterval(timeout);
				___pre();
				if(options.playbutton){
				if(playlock){
					if(timeout)clearInterval(timeout);
					timeout = setInterval(___next,options.pausetm);
				}else{
					if(timeout)clearInterval(timeout);
					timeout = null;
					options.auto=false;
				}
				}else{
				if(options.auto)timeout = setInterval(___next,options.pausetm);
				}
			});
		}
		if(options.nextId){
			$(options.nextId).click(function(){
				if(timeout)clearInterval(timeout);
				___next();
				if(options.playbutton){
				if(playlock){
					if(timeout)clearInterval(timeout);
					timeout = setInterval(___next,options.pausetm);
				}else{
					if(timeout)clearInterval(timeout);
					timeout = null;
					options.auto=false;
				}
				}else{
				if(options.auto)timeout = setInterval(___next,options.pausetm);
				}
			});
		}

		//如果是自动运行
		if(options.auto){;
			timeout = setInterval(___next,options.pausetm);
			//鼠标放上去是否停止滚动
			if(options.overstop){
				//设置鼠标在上面的时候去掉计时器
				$(options.itemparent).hover(function(){
					if(timeout)clearInterval(timeout);
				},function(){
					if(timeout)clearInterval(timeout);
					timeout = setInterval(___next,options.pausetm);
				});
			}
		};
	};
	$.fn.scrollShow2 = function(options){
	    //默认配置
		var defaults = {				
			vertical:       false  //垂直滚动
			,itemNum:		1// 滚动元素的个数
			,speed: 		800//滚动速度，越大越慢
			,auto:			false//自动
			,pausetm:		5000//自动的时候停止时间
            ,preId:         false //前滚的选择符 如 #prebtn
            ,nextId:        false //后滚的选择符 如 #nextbtn
            ,items:         false //滚动的元素 如 #scroll li
            ,itemparent:     false //滚动元素的外层 #scroll ul
            ,itemSpace:     200//宽度或者高度
            ,overstop:      true //鼠标放上去的时候停止滚动
            ,playbutton:    false//点击播放选择符 如 #playbtn
            ,playclass:     false//播放的样式 如 palystyle
            ,pauseclass:    false//暂停的样式 如 pausestyle
            ,callback:      function(){} //回调函数，滚动停止后
            
        };        
		var options = $.extend(defaults, options);
        if(!options.items || !options.itemparent )return;
        var callback = options.callback;
		var timeout = null;      
        var moveObj1 = null;
        var moveObj2 = null;
        var playlock =true;
        var scrolllock = false;//防止多次点击，滚动的时候加锁
        if(options.vertical){
              moveObj1 = {marginTop: -options.itemSpace};
              moveObj2 = {marginTop: 0};        
        }else{
              moveObj1 = {marginLeft: -options.itemSpace};
              moveObj2 = {marginLeft: 0};
          
        }
        //只有一张图的时候的处理 by suhy
        if($(options.items).length<=1&&options.nextId !='.package-pic-news .package-pic-news-btn'){
            $(options.preId+','+options.nextId).hide();
        	return false;
        }
        
        
        if(options.playbutton){
			$(options.playbutton).click(function() {
				if(timeout){
					$(options.playbutton).removeClass();
					$(options.playbutton).addClass(options.playclass);
					clearInterval(timeout);
					timeout = null;
					playlock =false;
					if(options.auto)options.auto=false;
				}else{
					$(options.playbutton).removeClass();
					$(options.playbutton).addClass(options.pauseclass);
					timeout = setInterval(___next,options.pausetm);
					playlock =true;
				}
			});
		}
		
        //往前滚
		function ___pre(){
			//加锁
			if(scrolllock)return;
			scrolllock = true;

			//将最后一个孩子节点移动到最前面了，防止右移动的时候出现空白
			callback($(options.items + ":last-child"));
			$(options.items + ':last-child').prependTo(options.itemparent);
			// 将移动过来的节点，推到可视范围外
			$(options.items + ":first-child").css(moveObj1).animate(moveObj2,options.speed,function(){
				scrolllock = false;
			});

		};
		//往后滚
		function ___next(){
			//加锁
			if(scrolllock)return;
			scrolllock = true;
			callback($(options.items + ":nth-child(2)"));
			//第一步 将第一个元素慢慢移动到界外   第二步 将第一个元素加到最后
			var filterStr = options.itemNum>1 ? ':lt('+(options.itemNum)+')' : ':first-child';
			$(options.items + ":first-child").animate(moveObj1,options.speed,function(){
				setTimeout(function(){
					$(options.items + filterStr).appendTo(options.itemparent).css(moveObj2);
					console.log($(options.items + filterStr));
					//alert(1);
					scrolllock = false;
				},200);
			});
		};

		//设置前后按钮的点击事件
		if(options.preId){
			$(options.preId).click(function(){
				if(timeout)clearInterval(timeout);
				___pre();
				if(options.playbutton){
				if(playlock){
					if(timeout)clearInterval(timeout);
					timeout = setInterval(___next,options.pausetm);
				}else{
					if(timeout)clearInterval(timeout);
					timeout = null;
					options.auto=false;
				}
				}else{
				if(options.auto)timeout = setInterval(___next,options.pausetm);
				}
			});
		}
		if(options.nextId){
			$(options.nextId).click(function(){
				if(timeout)clearInterval(timeout);
				___next();
				if(options.playbutton){
				if(playlock){
					if(timeout)clearInterval(timeout);
					timeout = setInterval(___next,options.pausetm);
				}else{
					if(timeout)clearInterval(timeout);
					timeout = null;
					options.auto=false;
				}
				}else{
				if(options.auto)timeout = setInterval(___next,options.pausetm);
				}
			});
		}

		//如果是自动运行
		if(options.auto){;
			timeout = setInterval(___next,options.pausetm);
			//鼠标放上去是否停止滚动
			if(options.overstop){
				//设置鼠标在上面的时候去掉计时器
				$(options.itemparent).hover(function(){
					if(timeout)clearInterval(timeout);
				},function(){
					if(timeout)clearInterval(timeout);
					timeout = setInterval(___next,options.pausetm);
				});
			}
		};
	};
})(jQuery);
//N1 平板 效果
(function($) {
		var n1ad = 	$('.sidebar .n1-pad-entrance-wrap');
		n1ad.live('mouseenter',function(){
			$(this).addClass('n1-pad-entrance-hover');
		});
		
		n1ad.mouseleave(function(){
			$(this).removeClass('n1-pad-entrance-hover');
		});
		
})(jQuery);

//标签切换
(function($){
	$.fn.lazyNavi=function(nor,act,tm,flag,event_type){
		var navi_over  = '';
		if(event_type){
			$(this).children().click(function(){
				var self = this;
				navi_over = setTimeout(function(){
					if($(self) == null || $(self).attr("rel") == null) return;
					if($(self).hasClass(nor) || !$(self).hasClass(act)){
						if(act) {
							act_class = '.'+act;
						} else {
							act_class = '[class="'+act+'"]';
						}
						var act_navi = $(self).siblings(act_class);
						if(act) {
							act_navi.removeClass(act);
						}
						if(nor) {
							act_navi.addClass(nor);
						}
						var rel_div = act_navi.attr("rel");
						$("#"+rel_div).hide();
						var now_div =$(self).attr("rel");
						if(nor) {
							$(self).removeClass(nor);
						}
						if(act) {
							$(self).addClass(act);
						}
						if(flag) {
							$("#"+now_div).getLazyArea();
						}
					}
				},tm);
			});
		}else{
			$(this).children().mouseover(function(){
				var self = this;
				navi_over = setTimeout(function(){
					if($(self) == null || $(self).attr("rel") == null) return;
					if($(self).hasClass(nor) || !$(self).hasClass(act)){
						
						//2015新版 下边线滑动效果   add by  huangjl 
						var line_obj = $(self).parent().next('.line');
						if(line_obj != null){
								var li_inner_width 		= $(self).innerWidth();
								var self_index 			= $(self).index();
								var left_new = 0;
								var prev_li	 = $(self).siblings('li:lt('+self_index+')');
									prev_li.each(function(i){
									   left_new+=$(this).outerWidth(true);
									 });
								if(!line_obj.is(":animated")){
									line_obj.width(li_inner_width).animate({left:left_new+'px'});
								}
						}
												
						if(act) {
							act_class = '.'+act;
						} else {
							act_class = '[class="'+act+'"]';
						}
						var act_navi = $(self).siblings(act_class);
						if(act) {
							act_navi.removeClass(act);
						}
						if(nor) {
							act_navi.addClass(nor);
						}
						var rel_div = act_navi.attr("rel");
						$("#"+rel_div).hide();
						var now_div =$(self).attr("rel");
						if(nor) {
							$(self).removeClass(nor);
						}
						if(act) {
							$(self).addClass(act);
						}
						if(flag) {
							$("#"+now_div).getLazyArea();
						}
						$("#"+now_div).show();
						if(act=='category-item-active'){
							$(self).removeClass('category-item-hover');  
						}
						if(act_navi.parent().attr('id')=='side-news-tab'){
							var self_index = $(self).index();
							if(self_index == 1){
								$('.side-news-head h3').html('<a href="http://zdc.zol.com.cn/">ZDC互联网消费调研</a>').addClass('side-news-head-zdc');
							}else{
								$('.side-news-head h3').html('<a href="http://bbs.zol.com.cn/vip/">Z神通自媒体联盟</a>').removeClass('side-news-head-zdc');
							}
						}
					}
				},tm);
			});
		}
		$(this).children().mouseout(function(){
			if(navi_over) {
				clearTimeout(navi_over);
			}
		});

	};
	$.fn.autoNavi=function(nor,act,tm,flag,clear_auto,clear_auto_btn,prevBtn,nextBtn,no_auto){
		var self = this;
		//初始化
		var init=function(){
			if(act) {
				act_class = '.'+act;
			} else {
				act_class = '[class="'+act+'"]';
			}
			act_navi = $(self).children(act_class);
			if(act) {
				act_navi.removeClass(act);
			}
			if(nor) {
				act_navi.addClass(nor);
			}
			rel_div = act_navi.attr("rel");
			if(!rel_div) {
				return;
			}
			$("#"+rel_div).hide();
		};
		var ZshenTongChange = function(next_navi){
			var self_index 			= next_navi.index();
			if(clear_auto=='.side-news'){
				if(self_index == 1){
					$('.side-news-head h3').html('<a href="http://zdc.zol.com.cn/">ZDC互联网消费调研</a>').addClass('side-news-head-zdc');
				}else{
					$('.side-news-head h3').html('<a href="http://bbs.zol.com.cn/vip/">Z神通自媒体联盟</a>').removeClass('side-news-head-zdc');
				}
			}
		}
		
		//2015新版 处理下边线一定效果   add by  huangjl 
		var move_line = function(next_navi){
			var line_obj = $(self).next('.line');
			if(line_obj == null) return false;
			var li_inner_width 		= next_navi.innerWidth();
			var self_index 			= next_navi.index();
			var left_new = 0;
			var prev_li	 = next_navi.siblings('li:lt('+self_index+')');
				prev_li.each(function(i){
				   left_new+=$(this).outerWidth(true);
				 });
			if(!line_obj.is(":animated")){
				line_obj.width(li_inner_width).animate({left:left_new+'px'});
			}
			ZshenTongChange(next_navi);
		}
		// 防止多次点击
		var lock = false;
		
		function navi_auto(){
			init();
			while(1) {
				var next_navi = act_navi.next();
				if(next_navi.html() == null) {
					next_navi = $(self).children().eq(0);
				}
				act_navi = next_navi;
				if(next_navi.attr("rel")) {
					break;
				}
			}
			//2015新版 下边线滑动效果   add by  huangjl 
			move_line(next_navi);
			
			if(nor) {
				next_navi.removeClass(nor);
			}
			if(act) {
				next_navi.addClass(act);
			}
			var next_div = next_navi.attr("rel");
			if(flag) {
				$("#"+next_div).getLazyArea();
			}
			$("#"+next_div).show();
		};
        
		if(!no_auto){
		var auto_navi = setInterval(navi_auto,tm);
		function navi_over(){clearInterval(auto_navi)};
		function navi_out(){
			if(auto_navi){clearInterval(auto_navi);}
			auto_navi = setInterval(navi_auto,tm);
		}
		if(clear_auto) {
			$(clear_auto).hover(function(){navi_over()},function(){navi_out()});
			if(clear_auto_btn){
				$(clear_auto_btn).toggle(
				function(){navi_over();$(this).addClass('stop');$(clear_auto).unbind()},
				function(){navi_out();$(this).removeClass('stop');$(clear_auto).hover(function(){navi_over()},function(){navi_out()});}
				);
			}
		}
		}

		var prev_tab=function(){
			init();
			//往回走
			var next_navi = act_navi.prev();
			if(next_navi.html() == null) {
				next_navi = $(self).children(":last");
			}
			act_navi = next_navi;
			if(nor) {
				next_navi.removeClass(nor);
			}
			if(act) {
				next_navi.addClass(act);
			}
			var next_div = next_navi.attr("rel");
			if(flag) {
				$("#"+next_div).getLazyArea();
			}
			$("#"+next_div).show();
			//2015新版 下边线滑动效果   add by  huangjl 
			move_line(next_navi);
			setTimeout(function(){lock = false;},500);
		};
		
		var next_tab=function(){
			init();
			//往后走
			var next_navi = act_navi.next();
			if(next_navi.html() == null) {
				next_navi = $(self).children(":first");
			}
			act_navi = next_navi;

			if(nor) {
				next_navi.removeClass(nor);
			}
			if(act) {
				next_navi.addClass(act);
			}
			var next_div = next_navi.attr("rel");
			if(flag) {
				$("#"+next_div).getLazyArea();
			}
			$("#"+next_div).show();
			
			//2015新版 下边线滑动效果   add by  huangjl 
			move_line(next_navi);
			setTimeout(function(){lock = false;},500);
		};
		if(prevBtn){
			$(prevBtn).click(function() {
				if(lock)return false;
				lock = true;
				if(auto_navi){clearInterval(auto_navi);}
				prev_tab();
				if(!no_auto){
				auto_navi = setInterval(navi_auto,tm);
				}
			});
		};

		if(nextBtn){
			$(nextBtn).click(function() {
				if(lock)return false;
				lock = true;
				if(auto_navi){clearInterval(auto_navi);}
				next_tab();
				if(!no_auto){
				auto_navi = setInterval(navi_auto,tm);
				}
			});
		};

	}
		$.fn.getLazyArea=function(){
			var lazyarea = $(this).children('textarea');
			if(lazyarea.length == 1) {
				lazyarea.hide();
				var lazyhtml = lazyarea.val();
				$(this).html(lazyhtml);
			}
		};
		
		
})(jQuery);
//区块点击跟踪
function trackUrlClick(event,att,url,id){
	/*http://pvtest.zol.com.cn/images/pvevents.gif
	 * ?t=时间戳
	 * &ip_ck=读ip_ck
	 * 
	 * &event=zol_www_toutiaodiv_click
	 * &att=mobile
	 * &url=点击url
	 * &id=链接区块位置(可相对，也可绝对)
	 * 
	 */
	var nowTime = new Date().getTime();
	var ipck	= $.cookie('ip_ck');
	
	var eventdiv= 'zol_www_'+event+'_click';
	var baseUrl = 'http://pvtest.zol.com.cn/images/pvevents.gif?';
	baseUrl += 't='+nowTime;
	baseUrl += '&event='+eventdiv;
	baseUrl += '&ip_ck='+ipck;
	baseUrl += '&att='+att;
	//baseUrl += '&url='+url;
	baseUrl += '&url=http://www.zol.com.cn/';
	$.getJSON(baseUrl+"&jsoncallback=?", function(data){});

}

/* 统计 */
function zol_niux_tongji(event,url) {
	var pv_stat_src = "http://pvtest.zol.com.cn/images/pvevents.gif?t=" + new Date().getTime() + "&event=" + event + "&ip_ck=" + readck("ip_ck") + "&url=" + url;
	var imgObj = new Image();
	imgObj.src = pv_stat_src;
}
(function($){
	//添加收藏夹
	var add_favorite = function(e){
		e=e || window.event;
		var d = new Date();
		var url=document.URL;
		var title=document.title;
		var img_obj = new Image();
		img_obj.src = 'http://pvsite.zol.com.cn/images/pvhit0001.gif?t='+d.getTime()+'&zolfavorite';
		document.body.appendChild(img_obj);
		try{
			var ua = navigator.userAgent;
		    ua = ua.toLocaleLowerCase();
			if(document.all || ua.match(/msie/) != null || ua.match(/trident/) != null){
				window.external.AddFavorite(url,title);
			}else if(window.sidebar){
				window.sidebar.addPanel(title,url,"");
			}else{
				alert("您使用的浏览器不支持此操作 ，请您使用Ctrl+D进行添加" );
			}
		}catch(e){
			alert("您使用的浏览器不支持此操作 ，请您使用Ctrl+D进行添加" );
		}
		e.preventDefault();
	}
   $("#addFav").click(function(e){
   		add_favorite(e);
   });
   
	/*图片后加载*/
	$('.wrapper img').picLoad({checkShow:true});
	//广告图片加载
	$('.adSpace img').picLoad({checkShow:true});
	// 底部二维码懒加载
	$('.dimension-code img').picLoad({checkShow:true});
	
	//首页焦点图自动切换 20150923
	$('#zol-focus-tab').autoNavi('','active',8000,1,'#scroll-stop','','#prevBtn','#nextBtn');
	$('#scroll-stop .focus2015').hover(
			function(){
				$('.focus2015 .prev,.focus2015 .next').show();
			},
			function(){
				$('.focus2015 .prev,.focus2015 .next').hide();
			}
			
	);

	//焦点图回切
	$("#prevBtn").hover(
		function(){
			$(this).addClass("slide-btn-hover");
		},
		function(){
			$(this).removeClass("slide-btn-hover");
		}
	);
	//焦点图后切
	$("#nextBtn").hover(
		function(){
			$(this).addClass("slide-btn-hover");
		},
		function(){
			$(this).removeClass("slide-btn-hover");
		}
	);
	
	//Z爆款切换效果,方案二,定义插件
	$.fn.wicketTrigger = function(options){
		//默认配置  左右滚动
		var defaults = {
			preId		:       false, 	//前滚的选择符 如 #prebtn
	        nextId		:       false, 	//后滚的选择符 如 #nextbtn
	        
            items		:     	false, 	// 滚动的元素 如 #scroll ul
            itemparent	:     	false, 	// 滚动元素的外层 div
            itembtnsF	:     	false, 	// 滚动元素的长条按钮群的父元素，如多个按钮span，被div包起来，那么父元素就是那个div

            itemSpace	:     	200, 	// 宽度
            speed		:		5000,	//1000表示1秒钟,表示5s中切换一次图片
            timeInterval:		1000,	//一张图切换的时间（切换速度），越大，则切换过程越慢

            curStyle	:    	'current', 
            overstop	:      	true, // 鼠标放上去的时候停止滚动
            callback	:      	function(){alert(123);} // 回调函数，滚动停止后
            
        };
        var options = $.extend(defaults, options);
        if(!options.items || !options.itemparent )return;
        var callback = options.callback;
        var timer = null;
        var flag = null;
    	//定义一组图片的ul宽度
	    var oW = $(options.itemparent).width();	
	    var obj_list = $(options.items);
		var span_btns = $(options.itembtnsF).children();
		var span_num = span_btns.length;
		//var span_curr_btn =  $(options.itembtnsF).children().find('span[class="'+options.curStyle+'"]');
		
		var wrap = $(options.itemparent+','+options.preId+','+options.nextId); // 窗口或其他
		
        // 初始化位置
		function __init(){
			i=0;
		   	obj_list.offset({right:0});
		   	obj_list.animate({left:0});
		   	timer =	setInterval(autoRun,options.speed);
		}
		// 将指定的元素运动至指定位置
		function toThis(obj,dis){
				$(obj).animate({left:-dis},options.timeInterval);
		}
		// 按钮跟随内容 保持索引
		function btn2Ul(index){
			span_btns.removeClass(''+options.curStyle+'');
			span_btns.eq(index).addClass(''+options.curStyle+'');
		}
		function autoRun(){
			var index = $(options.itembtnsF).find('.'+options.curStyle).index();
			i = index+1;
	    	if(obj_list.is(":animated")){
				return '';
			}
	    	var aWidth = obj_list.width();
	        if(aWidth < (options.itemSpace+10)){clearInterval(timer);}
	        if(i == span_num) i = 0;
			toThis(obj_list,oW*i);
			btn2Ul(i);
			//i++;
		}
        __init();
		//hover按钮切换
		var  over = null;
		span_btns.mouseover(function(){
			if(timer)clearInterval(timer);
			var self = this;
			over = setTimeout(function(){
				if(obj_list.is(":animated")){
					return '';
				}
			    $(self).siblings().removeClass(options.curStyle);
			    $(self).addClass(''+options.curStyle+'');
			   	flag = $(self).index();
			   	toThis(obj_list,oW*flag);
			},50);
	    });
		span_btns.mouseout(function(){
			if(over) {
				clearTimeout(over);
			}
			if(timer)clearInterval(timer);
			timer =	setInterval(autoRun,options.speed);
	    });
		
		//移入 移除 控制是否自动滚动
		wrap.mouseover(function(e){
			clearInterval(timer);
		});
		wrap.mouseout(function(){
			if(timer)clearInterval(timer);
			timer =	setInterval(autoRun,options.speed);
		});
		//播放下一组
		function __next(){
			var btnCurIndex = $(options.itembtnsF).find('.'+options.curStyle).index();
			var i = Number(btnCurIndex)+1;
			if(i > (span_num-1)){i = 0;}
			setTimeout(function(){
				if(obj_list.is(":animated")){
					return '';
				}
		    	var aWidth = obj_list.width();
		        if(aWidth < (options.itemSpace+10)){clearInterval(timer);}
				toThis(obj_list,oW*i);
				btn2Ul(i);
				i++;
			},30);
		}
		//播放上一组
		function __pre(){
			var btnCurIndex = $(options.itembtnsF).find('.'+options.curStyle).index();
			var i = Number(btnCurIndex)-1;
			if(i < 0)i = span_num-1;
			setTimeout(function(){
				if(obj_list.is(":animated")){
					return '';
				}
		    	var aWidth = obj_list.width();
		        if(aWidth < (options.itemSpace+10)){clearInterval(timer);}
				toThis(obj_list,oW*i);
				btn2Ul(i);
				i++;
			},30);
		}
		if(options.preId&&options.nextId){
			//点击上一个按钮
			$(options.preId).click(function(){
				__pre();
			});
			//点击下一个按钮
			$(options.nextId).click(function(){
				__next();
			});
		}
	}//定义wicketTrigger插件结束
	
	//Z爆款切换调用，插件不是十分稳定,可以使用非插件模式
	var paramObj_bk0212 = {
		items		:     	'.zbk-tabs ul', 		// 滚动的元素 如 #scroll ul
		itemparent	:     	'.zbk-tabs', 		// 滚动元素的外层 div
		itembtnsF	:     	'.tab-news-icon-bg .tab-news-icon',	// 滚动元素的长条按钮群的父元素，如多个按钮span，被div包起来，那么父元素就是那个div
		itemSpace	:     	338, 					// 宽度
		speed		:		4000,					//1000表示1秒钟
		curStyle	:    	'current', 				//当前选中按钮的样式名称，不需要“.”
		callback	:     	function(){} // 回调函数，滚动停止后
	};
	$(document).wicketTrigger(paramObj_bk0212);
   //爆款切换-结束E
   
   	//Z神通自媒体联盟
	var _zstTab = $("#side-news-tab");
	if( _zstTab.length>0 ){
		_zstTab.autoNavi('','active',5000,1,'.side-news','','','');
	}
	// Z神通显示“点击阅读更多”
	$('.without-viewmore').hover(function(){
			$(this).find('div.viewmore').show();
			$(this).find('div.viewmore').width('218');
		},
		function(){
			$(this).find('div.viewmore').hide();
		}
	);
	//头条文字滚动
	var paramObj_toutiao = {
		itemSpace:26//每次移动的高度或的宽度
		,items:"#scroll-toutiao ul li"//滚动元素的选择符
		,itemparent:"#scroll-toutiao ul" //滚动元素父节点的选择符
		,vertical:true  //垂直滚动：true，左右滚动：false；默认false
		,speed:800      //切换时候的持续时间，数字越大表示越慢
		,pausetm:3000   //滚动后的停留时间 默认 5s
		,auto:true      //是否自动滚动   默认false
		,overstop:true  //鼠标放上去是否停留 默认 true
	};
	$(document).scrollShow(paramObj_toutiao);
	
	//产品报价浮出 (nor,act,tm,flag,event_type)
	$('#productNav').lazyNavi("","category-item-active",300,1,0);
	$('#productNav .category-item').on({
		'mouseenter' : function(){
			if($(this).hasClass('category-item-active')){
				return ;
			}else{
				$(this).addClass('category-item-hover');
			}
		},
		'mouseleave': function(){
			$(this).removeClass('category-item-hover');
		},
		'click': function(){
			$(this).removeClass('category-item-hover');
		}
	});
	$('.category-item a').click(function(event){
		event.stopPropagation();
	});
	//产品报价消失
	$('#productNav').bind('mouseleave', function(){
		$(this).find('.subcatebox').hide();
		$(this).find('.category-item').removeClass('category-item-active','category-item-hover');
	});
	//锚点-版块导航跟随&回到顶部
	var win = $(window),
	guideNav = $('.guide-nav-list a'),//版块导航
	guideNavItem = $('.page-anchor'), //版块位置
	guideTimeout = false,
	getOffset = function(idx){
		return parseInt( guideNavItem.eq(idx).offset().top ) -100;
	},
	setCurPos = function(){
		var st = win.scrollTop();
		if(st>getOffset(5)){
			guideNav.eq(5).addClass("cur").siblings().removeClass("cur");
		}else if(st>getOffset(4)){
			guideNav.eq(4).addClass("cur").siblings().removeClass("cur");
		}else if(st>getOffset(3)){
			guideNav.eq(3).addClass("cur").siblings().removeClass("cur");
		}else if(st>getOffset(2)){
			guideNav.eq(2).addClass("cur").siblings().removeClass("cur");
		}else if(st>getOffset(1)){
			guideNav.eq(1).addClass("cur").siblings().removeClass("cur");
		}else{
			guideNav.eq(0).addClass("cur").siblings().removeClass("cur");
		}
	};	
	setCurPos();
	win.scroll(function(){
		/*if (guideTimeout) clearTimeout(guideTimeout);
		guideTimeout = setTimeout(setCurPos, 0);*/
		setCurPos();
	});
	//二维码
	var _qrmark = $(".quick-mark");
	if($.cookie("zol_index_qrclose")){
		//_qrmark.hide();
	}
	_qrmark.hover(
		function(){
			_qrmark.addClass("quick-mark-hover");
		},
		function(){
			_qrmark.removeClass("quick-mark-hover");
		}
	);
	// 二维码的隐藏
	$(".quick-mark-close").click(function(){
		_qrmark.hide();
		//$.cookie('zol_index_qrclose',1,{expires:7, path:'/', domain:'www.zol.com.cn'});
	});
	
	//页面右下侧导航的隐藏
	$('#showGuideNav').click(function(){
		$(this).parent().toggleClass('guide-nav-off');
	});
	var guessLikeStatisticsLock = true;
    $(window).scroll(function(){
    	var sT=$(this).scrollTop();
		if(sT >= 850) {
			$('#guideWidget').show();
		}
		if(sT < 850){
			$('#guideWidget').hide();
		}
		// 滚动至"猜你喜欢"栏目时，触发统计
		if(sT >= 700 && sT <= 1500 && guessLikeStatisticsLock && $('#J_YouLike').length>0 && $('#J_YouLike').find('.text-item li').length>1){
			var event = 'pc_home_page_article_rec_show';//&
			switch(ourRule){
				case 'baifendian':
					event += '&plan_des=baifendian-0-0-0';break;
				case 'zol':
					if(typeof(window.zol_plan_des)=='undefined' || !window.zol_plan_des){
						window.zol_plan_des = '0-0-0-0';
					}
					event += '&plan_des=zol-'+window.zol_plan_des;break;
				case 'baidu':
					event += '&plan_des=baidu-0-0-0';break;
				default: 
					event += '&plan_des=zol-0-0-0';break;
			}
			zol_niux_tongji(event,'http://www.zol.com.cn/');
			guessLikeStatisticsLock = false;
		}
   });
    
  //区块点击跟踪
	$(".cate-news-item a").click(function(){
		var _self   = $(this);
		var event	= 'toutiaodivb';
		var att     = _self.attr('data-click');
	    var url		= _self.attr('href'); 
	    if(att){
			trackUrlClick(event,att,url);
		}
	});
	//客户端点击跟踪
	 $('.client-btns a').click(function(){
		 var className = $(this).attr('class');
		 var event = 'mainpage_navbtn_click_'+className;
         zol_niux_tongji(event,'http://www.zol.com.cn/');    
     });
	 
	 
	//首页右下角弹窗关闭点击跟踪
	 $('#J_Right_Bottom_Layer_Close').click(function(){
		 var className = $(this).attr('class');
		 var event = 'mainpage_TodayPopbtn_click_'+className;
         zol_niux_tongji(event,'http://www.zol.com.cn/');    
     });
})(jQuery);

//给右下角的导航模块加点击统计
(function($){
	// 二屏时，右下的二维码,
	$('.quick-mark-pic,a.feedback').click(function(){
		 var className = $(this).attr('class');
		 var event = 'mainpage_guideWidget_click_'+className;
		 zol_niux_tongji(event,'http://www.zol.com.cn/');    
    });
	//意见反馈
	$('a.feedback').click(function(){
		 var className = $(this).attr('class');
		 var event = 'mainpage_guideWidget_click_'+className;
		 zol_niux_tongji(event,'http://www.zol.com.cn/');    
	});
	//回到顶部 gotop
	$('.gotop').click(function(){
		 var className = $(this).attr('class');
		 var event = 'mainpage_guideWidget_click_'+className;
		 zol_niux_tongji(event,'http://www.zol.com.cn/');  
	});
	// 焦点图的左右按钮统计
	$('#prevBtn,#nextBtn').click(function(){
		 var event = 'zol_www_picture_button';
		 zol_niux_tongji(event,'http://www.zol.com.cn/');  
	});
	
})(jQuery);

//右下角弹层 v2版
(function($){
	$(document).scroll(function(){
		var dialogToTOp = $("body").scrollTop();
		if(dialogToTOp >= 1474){
			execDialog();
		}
	});
	
	// 当滚动到电商区块时，才执行右下角弹窗  (肖总需求)
	function execDialog(){
		var isIE6 = !-[1,] && !window.XMLHttpRequest;
		$(document).on('click', '#J_Right_Bottom_Layer_Close', function(){
			var now_date = new Date().getTime(); //日期对象
				now_date = now_date + 86400; // 24小时
				$.cookie('zol_index_today_best_close1','today_yes',{expires:new Date(now_date),domain:'zol.com.cn'});
				if(!isIE6){
					$(this).parent('.today-hot-layer').animate({height: 0}, 580, function(){
					}).animate({width: 0}, 580, function(){
						$(this).hide()
						$(this).clearQueue()
					})  
				}else{
					$(this).parent('.today-hot-layer').hide();
				}
		})
		// suhy 记住用户点击关闭  
		$('.you-like-close,#J_Right_Bottom_Layer_Close').live('click',function(){
			var now_date = new Date().getTime(); //日期对象
			now_date = now_date + 86400000;// 6h    24*3600 000 改成24h   by suhy
			$.cookie('zol_index_today_best_close1','today_yes',{expires:new Date(now_date),domain:'zol.com.cn'});
			if(!isIE6){
				$(this).parent('.today-hot-layer').animate({height: 0}, 580, function(){
				}).animate({width: 0}, 580, function(){
					$(this).hide()
					$(this).clearQueue();
				})  
			}else{
				$(this).parent('.today-hot-layer').hide();
			}
		});
		if($.cookie('zol_index_today_best_close1') == 'today_yes'){
			$('#J_Right_Bottom_Layer_Close').parent('.today-hot-layer').hide()
		}else{
			// 正常弹窗的高度275
			var dialogHeight = 275;
			if($('.today-hot-news').eq(0).find('a').length <= 3){
				dialogHeight = dialogHeight-80;
			}else{
				dialogHeight = dialogHeight-1;
			}
			
			//3秒后展开（显示）
			setTimeout(function(){
				if(!isIE6){
					$('.today-hot-layer').animate({width: 308}, 1000, function(){
						 
					}).animate({height: dialogHeight}, 1000, function(){   //  height: 275
						$(this).clearQueue()
					}) 
				 }else{
					 $('.today-hot-layer').css({width: 308,height: dialogHeight}); //height:  275
				 }
			}, 500);
		}
	}
	
})(jQuery);

//搜索处理
$(function(){
	var searchTrigger,
	act = "search_all",
	search_arr = new Array("search_all","search_pro","search_ask","search_xiazai","search_article","search_bbs","search_pic","search_video","search_koubei"),
	searchBox = $("#keyword"),
	searchForm = $("#search_frm"),
	hide_c = $('#hide_c');
    hide_p = $('#hide_p');
	var search_url =new Array();
	search_url['search_all']="http://search.zol.com.cn/s/all.php";
	search_url['search_article']="http://search.zol.com.cn/s/article_more.php";
	search_url['search_pro']="http://detail.zol.com.cn/index.php";
	search_url['search_ask']="http://ask.zol.com.cn/new/search.php?bbs=all";
	search_url['search_xiazai']="http://xiazai.zol.com.cn/search.php";
	search_url['search_bbs']="http://bbs.zol.com.cn/index.php";
	search_url['search_soft']="http://xiazai.zol.com.cn/search.php";
	search_url['search_game']="http://youxi.zol.com.cn/index.php";
	
	search_url['search_pic']="http://search.zol.com.cn/s/pic.php";
	search_url['search_video']="http://search.zol.com.cn/s/video.php";
	search_url['search_koubei']="http://search.zol.com.cn/s/koubei.php";
	
	var tip_arr = new Array();
	tip_arr['search_all']='请输入您要查找的产品名称';
	tip_arr['search_article']='请输入关键词或文章标题';
	//tip_arr['search_pro']='联想 VIBE Z';
	tip_arr['search_pro']='请输入您要查找的产品名称';
	tip_arr['search_ask']='请输入您要查找的问题 ';
	tip_arr['search_xiazai']='请输入软件名称 ';
	tip_arr['search_bbs']='请输入版块名称或帖子标题 ';
	tip_arr['search_soft']='请输入软件名称 ';
	tip_arr['search_game']='请输游戏名称 ';
	
	tip_arr['search_pic']='请输入图片名称';
	tip_arr['search_video']='请输入视频名称 ';
	tip_arr['search_koubei']='请输入产品名称 ';
	
	//产品默认搜索配合
	var beginTime	= new Date('2014/10/31').getTime();
	var endTime 	= new Date('2014/11/28').getTime();
	var nowTime 	= new Date().getTime();
	if(nowTime>=beginTime && nowTime<=endTime){
		var adPro = '联想笋尖S90';
		tip_arr['search_pro']= adPro;
		searchForm.submit(function(){
			if(searchBox.attr('data-source')=='pro' && searchBox.val()==adPro){
				window.open('http://detail.zol.com.cn/cell_phone/index390214.shtml');
				return false;
			}
		});
	}
	searchForm.submit(function(){
		var att = searchBox.attr('data-source');
		var tip_arr_key = 'search_'+att;
		if(searchBox.attr('data-source')=='all' && searchBox.val()==tip_arr[tip_arr_key]){
			window.open('http://search.zol.com.cn/s/');
			return false;
		}
	});
	
	//搜索切换
	$(".search-type").hover(
	function(){
		$(this).addClass("search-type-on");
	},
	function(){
		$(this).removeClass("search-type-on");
	}
	);
	searchBox.val(tip_arr[act]);//添加默认显示值
	//取出当前选中的值为默认值
	searchVal=searchBox.val();
	var searchSource = act.replace('search_','');
	$("ul.search-type li").click(function(){
		var self = $(this);
		//20150212-add by suhy
		self.siblings('li').removeClass('active');
		self.addClass('active');
		
		var self_id=$(this).attr('id');

		if ('search_game' == self_id) {
			hide_c.val('Se');
			searchBox.attr('name','keyword');
		} else if ('search_pro' == self_id) {
			hide_c.val('SearchList');
			searchBox.attr('name','keyword');
		}else if('search_bbs' == self_id){
			hide_c.val('search');
			searchBox.attr('name','kword');
		} else {
			searchBox.attr('name','keyword');
		}
	
		if('search_bbs' == self_id){
			hide_p.attr('name','a').val('do');
		}else{
			hide_p.attr('name','').val('');
		}

		var self_val = self.html();
		$('.search-type span').html(self_val);
		var searcheValNow = searchBox.val();

		if(searcheValNow=='' || searcheValNow==tip_arr['search_all'] || searcheValNow==tip_arr['search_article'] || 
		searcheValNow==tip_arr['search_pro'] || searcheValNow==tip_arr['search_ask'] || searcheValNow==tip_arr['search_xiazai'] ||
		searcheValNow==tip_arr['search_bbs'] || searcheValNow==tip_arr['search_soft'] || searcheValNow==tip_arr['search_game'] ||
		 searcheValNow==tip_arr['search_pic'] || searcheValNow==tip_arr['search_video'] || searcheValNow==tip_arr['search_koubei']
		){
			searchBox.val(tip_arr[self_id]);
			searchVal=tip_arr[self_id];
		}
		$(".search-type").removeClass("search-type-on");
		searchForm.attr('action',search_url[self_id]);
		searchSource = self_id.replace('search_','');
		
		searchBox.attr('data-source',searchSource);
	});
	
	searchBox.zsuggest({offsetX:-11, offsetY:7, width: 388, source: searchSource, isSuggest: true});
	// 搜索框值处理
	searchBox.bind("focus",function(){
		if (this.value == searchVal) {
			this.value = "";
		}
	});

	searchBox.bind("blur",function(){
		if (this.value == "") {
			this.value = searchVal;
		}
	});
	//默认搜索类型
	searchBox.attr('data-source',searchSource);
	
	//搜索框边框变色
	var searchTimer = null;
	var _searchWrapBox = $(".search-box");
	
	_searchWrapBox.on('hover',function(event){
		var _self = $(this);
		if(event.type=='mouseenter'){
			if(searchTimer) clearTimeout(searchTimer);
			_self.addClass('search-box-hover');
		}else{
			searchTimer = setTimeout(function(){
				_self.removeClass('search-box-hover');
			},200);
		}
	}); 
	$("body").on('hover','#zSearchSuggest',function(event){
		var _self = $(this);
		if(event.type=='mouseenter'){ 
			if(searchTimer) clearTimeout(searchTimer);
			
			_searchWrapBox.addClass('search-box-hover');
		}else{
			searchTimer = setTimeout(function(){
				_searchWrapBox.removeClass('search-box-hover');
			},200);
		}
	});

	
	$('#keyword').on('focus',function(){
		$('.search-keyword').css({borderColor:"#008EE1"});
		$(this).addClass('input-focus');
	}).on('blur',function(){
		$('.search-keyword').css({borderColor:"#ccc"});
		if(in_array($(this).val(),tip_arr)){
			$(this).removeClass('input-focus');
		}
	});
	
});
// 定义一个全局的函数
function in_array(search,array){
    for(var i in array){
        if(array[i]==search){
            return true;
        }
    }
    return false;
}

//全国行情切换，弹窗
(function($){
	//配置区
	//数组(对应城市二级域名)
	var province_city = new Array();
	province_city['beijingshi'] = 'beijing';
	province_city['tianjinshi'] = 'tianjin';
	province_city['liaoningsheng'] = 'shenyang';
	province_city['jilinsheng'] = 'changchun';
	province_city['heilongjiangsheng'] = 'haerbin';
	province_city['hebeisheng'] = 'shijiazhuang';
	province_city['shanxisheng'] = 'taiyuan';
	province_city['shanxisheng_datongshi'] = 'datong';

	province_city['shanghaishi'] = 'shanghai';
	province_city['zhejiangsheng'] = 'hangzhou';
	province_city['zhejiangsheng_ningboshi'] = 'ningbo';
	province_city['zhejiangsheng_wenzhoushi'] = 'wenzhou';
	province_city['jiangsusheng'] = 'nanjing';
	province_city['shandongsheng'] = 'jinan';
	province_city['shandongsheng_yantaishi'] = 'yantai';
	province_city['shandongsheng_qingdaoshi'] = 'qingdao';
	province_city['fujiansheng'] = 'fuzhou';
	province_city['fujiansheng_xiamenshi'] = 'xiamen';
	province_city['anhuisheng'] = 'hefei';
	province_city['anhuisheng_anqingshi'] = 'anqing';
	province_city['jiangxisheng'] = 'nanchang';
	province_city['fujiansheng_quanzhoushi'] = 'quanzhou';

	province_city['guangdongsheng_shenzhenshi'] = 'sz';
	province_city['guangdongsheng'] = 'gz';
	province_city['guangdongsheng_foshanshi'] = 'foshan';
	province_city['guangdongsheng_dongshi'] = 'dongguan';
	province_city['hunansheng'] = 'changsha';
	province_city['guangxi'] = 'nanning';
	province_city['guizhousheng'] = 'guizhou';

	province_city['shan3xisheng'] = 'xian';
	province_city['sichuansheng'] = 'chengdu';
	province_city['zhongqingshi'] = 'chongqing';//chongqing
	province_city['henansheng'] = 'zhengzhou';
	province_city['gansusheng'] = 'lanzhou';
	province_city['hubeisheng'] ='wuhan';
	province_city['yunnansheng'] = 'kunming';
	province_city['neimenggu'] = 'huhehaote';
	province_city['xinjiang'] = 'wulumuqi';

	//名称数组
	var provinces = new Array();
	provinces['beijingshi'] = '北京';
	provinces['tianjinshi'] = '天津';
	provinces['liaoningsheng'] = '辽宁';
	provinces['jilinsheng'] = '吉林';
	provinces['heilongjiangsheng'] = '黑龙江';
	provinces['hebeisheng'] = '河北';
	provinces['shanxisheng'] = '山西';
	provinces['shanxisheng_datongshi'] = '大同';

	provinces['shanghaishi'] = '上海';
	provinces['zhejiangsheng'] = '浙江';
	provinces['zhejiangsheng_ningboshi'] = '宁波';
	provinces['zhejiangsheng_wenzhoushi'] = '温州';
	provinces['jiangsusheng'] = '江苏';
	provinces['shandongsheng'] = '山东';
	provinces['shandongsheng_yantaishi'] = '烟台';
	provinces['shandongsheng_qingdaoshi'] = '青岛';
	provinces['fujiansheng'] = '福建';
	provinces['fujiansheng_xiamenshi'] = '厦门';
	provinces['anhuisheng'] = '安徽';
	provinces['anhuisheng_anqingshi'] = '安庆';
	provinces['jiangxisheng'] = '江西';
	provinces['fujiansheng_quanzhoushi'] = '泉州';

	provinces['guangdongsheng_shenzhenshi'] = '深圳';
	provinces['guangdongsheng'] = '广东';
	provinces['guangdongsheng_foshanshi'] = '佛山';
	provinces['guangdongsheng_dongshi'] = '东莞';
	provinces['hunansheng'] = '湖南';
	provinces['guangxi'] = '广西';
	provinces['guizhousheng'] = '贵州';

	provinces['shan3xisheng'] = '陕西';
	provinces['sichuansheng'] = '四川';
	provinces['zhongqingshi'] = '重庆';
	provinces['henansheng'] = '河南';
	provinces['gansusheng'] = '甘肃';
	provinces['hubeisheng'] = '湖北';
	provinces['yunnansheng'] = '云南';
	provinces['neimenggu'] = '内蒙古';
	provinces['xinjiang'] = '新疆';


	//有静态文件的市级数组
	var citys = new Array();
	citys['ningboshi'] = '宁波';
	citys['wenzhoushi'] = '温州';
	citys['yantaishi'] = '烟台';
	citys['qingdaoshi'] = '青岛';
	citys['xiamenshi'] = '厦门';
	citys['anqingshi'] = '安庆';
	citys['shenzhenshi'] = '深圳';
	citys['foshanshi'] = '佛山';
	citys['dongshi'] = '东莞';
	citys['datongshi'] = '大同';

	//省的市级数组 显示名称用
	var provinces_city = new Array();
	provinces_city['shenyangshi'] = '沈阳';
	provinces_city['changchunshi'] = '长春';
	provinces_city['haerbinshi'] = '哈尔滨';
	provinces_city['shijiazhuangshi'] = '石家庄';
	provinces_city['taiyuanshi'] = '太原';

	provinces_city['hangzhoushi'] = '杭州';
	provinces_city['nanjingshi'] = '南京';
	provinces_city['jinanshi'] = '济南';
	provinces_city['fuzhoushi'] = '福州';
	provinces_city['hefeishi'] = '合肥';
	provinces_city['nanchangshi'] = '南昌';
	provinces_city['quanzhoushi'] = '泉州';

	provinces_city['guangzhoushi'] = '广州';
	provinces_city['changshashi'] = '长沙';
	provinces_city['nanningshi'] = '南宁';
	provinces_city['guiyangshi'] = '贵阳';
	provinces_city['guizhoushi'] = '贵阳';

	provinces_city['xianshi'] = '西安';
	provinces_city['chengdushi'] = '成都';
	provinces_city['zhengzhoushi'] = '郑州';
	provinces_city['lanzhoushi'] = '兰州';
	provinces_city['wuhanshi'] = '武汉';
	provinces_city['kunmingshi'] = '昆明';
	provinces_city['huhehaoteshi'] = '呼和浩特';
	provinces_city['wulumuqishi'] = '乌鲁木齐';

	//就近显示城市名 key为local值 函数setNaviLastCity专用
	var city_name = new Array();
	city_name['beijingshi'] = '北京';
	city_name['tianjinshi'] = '天津';
	city_name['liaoningsheng'] = '沈阳';
	city_name['jilinsheng'] = '长春';
	city_name['heilongjiangsheng'] = '哈尔滨';
	city_name['hebeisheng'] = '石家庄';
	city_name['shanxisheng'] = '太原';
	city_name['shanxisheng_datongshi'] = '大同';

	city_name['shanghaishi'] = '上海';
	city_name['zhejiangsheng'] = '杭州';
	city_name['zhejiangsheng_ningboshi'] = '宁波';
	city_name['jiangsusheng'] = '南京';
	city_name['shandongsheng'] = '济南';
	city_name['shandongsheng_yantaishi'] = '烟台';
	city_name['shandongsheng_qingdaoshi'] = '青岛';
	city_name['fujiansheng'] = '福州';
	city_name['fujiansheng_xiamenshi'] = '厦门';
	city_name['anhuisheng'] = '合肥';
	city_name['anhuisheng_anqingshi'] = '安庆';
	city_name['jiangxisheng'] = '南昌';
	city_name['fujiansheng_quanzhoushi'] = '泉州';

	city_name['guangdongsheng_shenzhenshi'] = '深圳';
	city_name['guangdongsheng'] = '广州';
	city_name['guangdongsheng_foshanshi'] = '佛山';
	city_name['guangdongsheng_dongshi'] = '东莞';
	city_name['hunansheng'] = '长沙';
	city_name['guangxi'] = '南宁';
	city_name['guizhousheng'] = '贵阳';

	city_name['shan3xisheng'] = '西安';
	city_name['sichuansheng'] = '成都';
	city_name['zhongqingshi'] = '重庆';
	city_name['henansheng'] = '郑州';
	city_name['gansusheng'] = '兰州';
	city_name['hubeisheng'] = '武汉';
	city_name['yunnansheng'] = '昆明';
	city_name['neimenggu'] = '呼和浩特';
	city_name['xinjiang'] = '乌鲁木齐';

	//底部已存在切换层的省份
	//provinces_exist = new Array('shan3xisheng','sichuansheng','guangdongsheng','hebeisheng');
	provinces_exist = new Array();
	var timeOutAjaxId = null,
	timeOutFloatWindow=null,
	thisCityId = null,
	local_city_name='',
	local='',
	loadFlag = 0,//首屏行情资讯加载标志;
	isIE6 = !-[1,] && !window.XMLHttpRequest;

	//焦点行情切换
	$(".other-citys").hover(
	function(){
		$(this).addClass("other-citys-on");
	},
	function(){
		$(this).removeClass("other-citys-on");
	}
	);

	//焦点城市切换模块
	$(".market-city a").mouseenter(
	function(){
		var self = $(this);
		thisCityId=self.attr('city');
		timeOutAjaxId=setTimeout(function(){
			$('#local_info').load("./statics/all_price_article_2014/locale_city_"+thisCityId+"_2015.html",function() {
				$(".market-city a").removeClass("active");
		self.addClass("active");
			});
		},200);

	});

	$(".market-city a").mouseleave(
	function(){
		if (timeOutAjaxId)clearTimeout(timeOutAjaxId);
	});
	
	//20150212 地区是河北省（石家庄）-二导航屏蔽z爆款，显示降价提醒
	var setSecondNav = function(local){
		if(local=='hebeisheng'){
			// $(".navswitc-bk-tab").remove();
			// 无论如何都显示Z爆款，暂时隐藏降价提醒
			$(".navswitc-jj").remove();
		} else {
			$(".navswitc-jj").remove();
		}
	}
	
	//设置行情地区第一项以及其它存在的切换页
	var setNaviFirstCity = function(local){
		var firstItem = $('#productTab');//地区选择第一项
		var cityTitle = $('#city_title');
		$("#local_info").load("./statics/all_price_article_2014/locale_city_"+local+"_2015.html",function() {
			getLocalFloat(local);
		});
		$(".other-citys").removeClass("other-citys-on");
		//如果是其它选项卡，就显示到其它
 
		for(i=0;i<provinces_exist.length;i++){
           if(local == provinces_exist[i])firstItem=$('#_'+local)
         }
		firstItem.html('' == local_city_name ? provinces[local] : local_city_name);
		cityTitle.text('' == local_city_name ? provinces[local]+'值得买' : local_city_name+'值得买');
		firstItem.attr('city',local);
		if ('beijing' == province_city[local]) {
			firstItem.attr('href','http://price.zol.com.cn/');
			cityTitle.attr('href','http://price.zol.com.cn/');
		}else{
			firstItem.attr('href','http://' + province_city[local] + '.zol.com.cn/');
			cityTitle.attr('href','http://' + province_city[local] + '.zol.com.cn/');
		}
		$(".market-city a").removeClass("active");
		firstItem.addClass("active");

	};

	//获取本地行情
	var getLocalInfo = function(){
		//默认未有省时 增加
		if (provinces[local] == undefined) {
			if (provinces[local+'sheng'] != undefined) {
				local = local+'sheng';
			} else {
				local = 'beijingshi';
			}
		}
		setNaviFirstCity(local);
		$(".city").click(function(){
			var cityId = $(this).attr("city");
			setNaviFirstCity(cityId);
			//设置地区cookie
			var now_date = new Date().getTime(); //日期对象
			now_date = now_date + 30*86400*1000;
			$.cookie('z_mp_new',cityId,{expires:new Date(now_date),domain:'zol.com.cn'});
		});
		//setSecondNav(local);
	};

	//获取地理位置
	var getIpArea=function(){
		$.getScript("http://intf.zol.com.cn/intf_ip/get_ip2py.php?jsname=locale_ip_city&jsshen=locale_ip_province", function(){
			//默认显示
			if (typeof locale_ip_province !== 'undefined' && locale_ip_province != '') {
				local = locale_ip_province;
			} else {
				local = 'beijingshi';
			}
			//citys中才显示市级
			if (typeof locale_ip_city !== 'undefined' && citys[locale_ip_city] != undefined) {
				local = local + '_' + locale_ip_city;
			} else {
				//省会显示市名称,不显示省名称,但读取省的静态文件
				if (typeof locale_ip_city !== 'undefined' && provinces_city[locale_ip_city] != undefined) {
					local_city_name = provinces_city[locale_ip_city];
				}
			}
			getLocalInfo();
			priceIpShow();
		});
	};

	//首屏根据IP显示行情资讯
	var priceIpShow = function(){
		if(loadFlag)return;
		//自动加载首屏行情区块
		$("#pro-price-tab").load('./statics/all_price_article_2014/locale_city_right_'+local+'_2015.html',function() {});
		loadFlag = 1;
	};

	//右下角行情浮动窗口
	var downAnimOptions = null
	var getLocalFloat = function(local){
		var timestamp = Date.parse(new Date());
		//展示浮动的城市
		if ('guangdongsheng_shenzhenshi' == local || 'shanghaishi' == local) {
		 
			$('.today-hot-layer').hide();//优先展示行情弹窗
	 
			localFloatSrc = 'statics/all_price_article_2014/locale_'+local+'_float_2014.html?' + Date.parse(new Date());
		} else if(0==local.indexOf('guangdongsheng')) {
			
			$('.today-hot-layer').hide();//优先展示行情弹窗
			localFloatSrc = 'statics/all_price_article_2014/locale_guangdongsheng_float_2014.html?' + Date.parse(new Date());
		} else if('fujiansheng' == local) {
			$('.today-hot-layer').hide();//优先展示行情弹窗
			localFloatSrc = 'statics/all_price_article_2014/locale_'+local+'_float_2014.html?' + Date.parse(new Date());
		} else {
			if (!timeOutFloatWindow) clearTimeout(timeOutFloatWindow);
			closeMarketWindow();
			return;
		}
		
		var currentTop = $('#_cityMarketWindow').position().top;
		var currentH = $('#_cityMarketWindow').height();
		downAnimOptions = {'bottom': -268};
	
		$("#_cityMarketWindow").load(localFloatSrc,function() {
			//消除
			if (timeOutFloatWindow) {
				clearTimeout(timeOutFloatWindow);
				timeOutFloatWindow = null;
			}
			if(!isIE6){
				$('#_cityMarketWindow').css(downAnimOptions).hide();
				$('#_cityMarketWindow').show().animate({'bottom': 0}, 800)
			} else {
				$('#_cityMarketWindow').hide()
				$('#_cityMarketWindow').addClass('ie6fixed').show()
			}
			timeOutFloatWindow=setTimeout(function(){//到点关闭
				closeMarketWindow()
			},30000);
		});
	}

	//浮动行情窗口关闭过程
	var closeMarketWindow=function(){
		$('#_cityMarketWindow').animate(downAnimOptions, 800, function(){
			if(isIE6) {
				$(this).removeClass('ie6fixed');
			}
			$(this).hide();
		})
	}

	//右侧浮动行情窗口关闭
	$('#_closeMarketWindow').live('click',function(){
		if (!timeOutFloatWindow) clearTimeout(timeOutFloatWindow);
		closeMarketWindow();
	})

	local = $.cookie('z_mp_new');//收cookie
	if(local){
		getLocalInfo();//获取本地行情
		priceIpShow();//首屏右侧行情资讯显示
	}else{
		getIpArea();//获取地理位置
	}
	
})(jQuery);

//各种切换执行
(function($){
	//导航-CBSi科技站群下拉
	$("#group-site").hover(function(){
		$(this).addClass('group-site-hover');
	},function(){
		$(this).removeClass('group-site-hover');
	});
	//导航-手机客户端入口
	var clientEnterBody = $("#client-enter .client-enter-body");
	$("#client-enter").hover(function(){
		clientEnterBody.show();
		$(this).addClass('client-enter-hover');
	},function(){
		clientEnterBody.hide();
		$(this).removeClass('client-enter-hover');
	});
	//二导航
	$('.nav-switch').lazyNavi("","current",200,1);
	$('.switc').lazyNavi("","active",200,1);
	//二导航自动显示
	var _navSwitchCate = $("#navswitc-cate-tab");
	var navcateTimer = null;
	
	/*去掉自动回滚 mod by  huangjl*/
	/*$(".new-launch, .cut-price, .z-bk-list").mouseleave(function(){
			_navSwitchCate.mouseover();
	});*/
	isIE6 = !-[1,] && !window.XMLHttpRequest;
	if(isIE6){
		$("#navswitc-cate").on('hover',function(event){
			if(event.type=='mouseenter'){
				$(this).addClass('category-nav-hover'); 
			}else{
				$(this).removeClass('category-nav-hover');
			}
		});
	}
	
})(jQuery);
$(function(){
	
	
});
 
//顶部导航
(function($){
    __publicNav = {
            getNotice:function(num){if(num > 0){$('#sitenav-personal-msg').show();}},
            trim:function(str){return str.replace(/(^\s+|\s+$)/g,'');},
            callback:function(){ return true; }    //回调函数
    }
    
    if (!userid){
    	//点击登录框以外，则隐藏登陆框
    	$(document).click(function(e){ 
    		e = window.event || e; // 兼容IE7
    		obj = $(e.srcElement || e.target);
    		if (obj.parents('#sitenavLoginBox').length == 1||obj.attr('class') == 'sitenav-login-form'||obj.attr('class') == 'sitenav-login-link') {
    			//do nothing.
    		} else {
    			$('.sitenav-login-form').hide();
    		}
    	});
    	//双击登录框，则把登陆框隐藏
    	$('#sitenavLoginBox').on('dblclick',function(e){
    		e = window.event || e;
    		obj = e.srcElement || e.target ;
    		if($(obj).attr('id') == 'sitenavLoginBox'){
    			$('.sitenav-login-form').hide();
    		}
    	});
    	var lockscript = true;
        //登陆框的显示和隐藏
        $('.sitenav-login-link').on('click', function(){
            if ($('.sitenav-login-form').css('display') == 'none') {
            	// 给iframe添加src  by suhy 20151105
            	var logInIframe = $('#userInfo .sitenav-login-form iframe');
            	if(logInIframe.length > 0){
                	var logInIframeSrc = logInIframe.attr('src');
                	if(!(logInIframeSrc.length > 1)){
                		logInIframe.attr('src','http://service.zol.com.cn/user/siteLogin.php?type=small&callback=userLoginCallback&backurl='+location.href);
                	}
            	}
                $('.sitenav-login-form').show();
                $('#userName').focus();
            } else {
                $('.sitenav-login-form').hide();
            }
            if(lockscript){
	            var script=document.createElement("script");  
	            script.type="text/javascript";  
	            script.src="http://service.zol.com.cn/user/js/login2014/md5.js";  
	            document.getElementsByTagName('body')[0].appendChild(script); 
	            lockscript = false;
            }
        });
        
        //提醒消息清除
        $('#userName').on('keyup', function(event){
            var val = $(this).val();
            if (val) {
                $(this).siblings('label[for="userName"]').hide();
            } else {
                $(this).siblings('label[for="userName"]').show();
            }
            if (event.keyCode==13) {
                return false;
            }
            $('#sitenav-mes-tip').removeClass('sitenav-error-tip').html('帐号登录');
        })
        
        $('#passWord').on('keyup', function(event){
            var val = $(this).val();
            $('#sitenav-mes-tip').removeClass('sitenav-error-tip').html('帐号登录');
            if (val) {
                $(this).siblings('label[for="passWord"]').hide();
            } else {
                $(this).siblings('label[for="passWord"]').show();
            }
            if (event.keyCode==13) {
                return false;
            }
            $('#sitenav-mes-tip').removeClass('sitenav-error-tip').html('帐号登录');
        })
        //输入框获取焦点时，在其边框有颜色样式
        $('input#userName,input#passWord').focus(function(){
        	$(this).css({cursor:"text"});
        	$(this).siblings('label').css({cursor:"text"});
        	$(this).parent('div').siblings('div').removeClass('sitenav-focus');
        	$(this).parent('div').siblings('div').removeClass('sitenav-error');
        	$(this).parent('div').addClass('sitenav-focus');;
        });
        //登录，防止多次点击登录提交，加上锁
        var  lock = true;
         
        $('#sitenav-login-button').click(function(){
            var userid      = $('#userName').val(),
                password    = $('#passWord').val(),
                autoLogin   = '';
            if ($('#autoLogin').is(":checked")) {
                autoLogin = 1;
            }
            userid   = __publicNav.trim(userid);
            password = __publicNav.trim(password);
            if(!lock)  return false;
            $('#passWord').attr('mds',CryptoJS.MD5(password+"zol"));
            password = $('#passWord').attr('mds');
            if(userid && password){
            	lock = false;
                $.ajax({
                    type   : "POST",
                    url    : "http://dynamic.zol.com.cn/channel/userLoginIndex.php",
                    async  : false,
                    data   : {
                    	userid  : userid,
                    	password: password,
                        isAuto  : autoLogin,
                        from    : 220
                    },
                    dataType : "jsonp",
                    success : function(json){
                        if(json.code < 1){
                        	var shopUrl = 'http://login.zol.com/index.php?callback=?&c=Default&a=APILogin&act=signin&username='+json.shopUserId+'&check='+json.shopStr;
                            $.getJSON(
                                shopUrl,
                                function(d){
                                    setTimeout(function(){
                                        if(typeof(__publicNavRefresh)!='undefined' && __publicNavRefresh){
                                        	window.location.reload(true);
                                        }
                                        __publicNav.loginSuccess(userid); 
                                    },1000);
                            });
                            window.location.reload(true);
                        }else{
                            $('#sitenav-mes-tip').addClass('sitenav-error-tip').html(json.message);
                            $('div.sitenav-username').removeClass('sitenav-focus');
                			$('div.sitenav-password').removeClass('sitenav-focus');
                            switch(json.subCode){
                        		case 2://账号密码错误
	                        		$('div.sitenav-username').addClass('sitenav-error');
	                        		$('div.sitenav-password').addClass('sitenav-error');
	                        		break;
                            	case 3://用户名不存在
                            		$('div.sitenav-username').addClass('sitenav-error');
                            		break;
                            	default:
                            		//todo 
                            		$('#sitenav-mes-tip').addClass('sitenav-error-tip').html('请输入正确的用户名和密码。');
                            }
                        }
                        lock = true;
                    },
                    error : function(json){    
				        lock = true;
                    }
                });
            }else{
            	$('#sitenav-mes-tip').addClass('sitenav-error-tip').html('请将用户名和密码填写完整。');
            }
        });
        
        //未登录时，按enter login（回车登录）
        $('.sitenav-login-form input').on('keyup', function(event){ 
           if(event.keyCode == 13){
               $("#sitenav-login-button").trigger("click");
               return false;
           }
        });
    }// if (!userid)-----括号结束
    
})(jQuery);
//ZOL首页电商模块
(function($){
	//Z团-特价模块图片切换
	var paramObj_tejia = {
		itemSpace:300//每次移动的高度或的宽度
		,items:".zTehui-scroll .scroll-data ul li"//滚动元素的选择符
		,itemparent:".zTehui-scroll .scroll-data ul" //滚动元素父节点的选择符
		,preId:  ".zTehui-scroll .prev" //前滚的选择符 如 #prebtn
        ,nextId: ".zTehui-scroll .next" //后滚的选择符 如 #nextbtn
		,vertical:false  //垂直滚动：true，左右滚动：false；默认false
		,speed:800      //切换时候的持续时间，数字越大表示越慢 
		,pausetm:3000   //滚动后的停留时间 默认 5s
		,auto:true      //是否自动滚动   默认false
		,overstop:true  //鼠标放上去是否停留 默认 true
	};
	$(document).scrollShow(paramObj_tejia);
	
	//Z+商城的商品轮播
	var paramObj_goods = {
		items		:     	'#zPlusArea .good-item ul', 		// 滚动的元素 如 #scroll ul(里边包含着很多的li哦)
		itemparent	:     	'#zPlusArea div.goods-tabs', 		// 滚动元素的外层 div
		itembtnsF	:     	'#zPlusArea div.tab-goods-icon',	// 滚动元素的长条按钮群的父元素，如多个按钮span，被div包起来，那么父元素就是那个div
		itemSpace	:     	360, 					// 宽度
		speed		:		4000,					//1000表示1秒钟
		curStyle	:    	'current', 				//当前选中按钮的样式名称，不需要“.” 
		callback	:     	function(){} // 回调函数，滚动停止后
	};
	$(document).wicketTrigger(paramObj_goods);
	//Z神通下方商品随机展示一个,goodsDataArr这个数组由PHP写在html中
	var goodsIndex = Math.floor(Math.random()*(goodsDataArr.length));
	var str = '\
		<a href="'+goodsDataArr[goodsIndex][1]+'" class="pic"><img width="60" height="45" src="'+goodsDataArr[goodsIndex][0]+'" alt=""></a>\
		<h3 class="title"><a href="'+goodsDataArr[goodsIndex][1]+'">'+goodsDataArr[goodsIndex][2]+'<br/>'+goodsDataArr[goodsIndex][3]+'</a></h3>\
		<span class="price">&yen;'+goodsDataArr[goodsIndex][4]+'</span>\
		<i class="ico"></i>';
	$('.pboi-intel-2015-product').html(str);
	//免费试用大焦点图改为轮播图 20150403 add by suhy
	$('.trial-focus ul').autoNavi('','active',5000,1,'.trial-focus','','#trialPrevBtn','#trialNextBtn');
	//免费试用焦点图回切，显示按钮
	$("#trialPrevBtn").hover(
		function(){
			$(this).addClass("slide-btn-hover");
		},
		function(){
			$(this).removeClass("slide-btn-hover");
		}
	);
	//免费试用焦点图后切，显示按钮
	$("#trialNextBtn").hover(
		function(){
			$(this).addClass("slide-btn-hover");
		},
		function(){
			$(this).removeClass("slide-btn-hover");
		}
	);
	// add by suhy
	//通栏版猜你喜欢,ajax请求数据
	var ip_ck = $.cookie("ip_ck");
	if($('#J_YouLike').length>0 && ourRule && ourRule == 'zol'){
		$.ajax({
			type: "get",
			url: "http://dynamic.zol.com.cn/channel/mainpage/guess_you_like.php",
			data: {ip_ck:ip_ck,type:1},
			dataType:'jsonp',
			cache:true,
	    	jsonp: "callback",
	    	jsonpCallback:"show_like_new",
			//async  : false,
			success: function(dataStr){
				var data = eval(dataStr);
				//猜你喜欢如果因为接口没有数据，则将容器隐藏
				if(data.length < 12){ 
					$('#J_YouLike').hide();
					zolData = false; 
				}else{
					// 如果有数据，则先填充数据，再从隐藏状态改为显示状态
					var ghtml = guess_like_html_zol(data);
					if(!ghtml)return '';
					$('.you-like .you-like-list-box').html(ghtml);
					$('#J_YouLike').fadeIn('2000');
					//首页ZOL规则猜你喜欢加载的 统计
					var event = 'pc_home_page_article_rec_load&plan_des=zol-'+data[0]['plan_des'];
					window.zol_plan_des = data[0]['plan_des'];
					zol_niux_tongji(event,'http://www.zol.com.cn/');
					//点击的统计
					$('#J_YouLike').on('click','a',function(){
						var event = 'pc_home_page_article_rec_cilck&plan_des=zol-'+data[0]['plan_des'];
						zol_niux_tongji(event,'http://www.zol.com.cn/');
					});
					//猜你喜欢hover短标题上滑特效
					if(typeof isSetHoverEvent == 'undefined'){
						var isSetHoverEvent = true;
						$('#J_YouLike .pic-item').hover(function(){
							$(this).addClass('current');
						},function(){
							$(this).removeClass('current');
						});
					}
					//发送统计的命中情况
					/*var event	= 'www_vlike_middle';
					var att     = hitNum;
				    var url		= 'www.zol.com.cn'; 
				    if(att){
						trackUrlClick(event,att,url);
					}*/
				}	
				
			},
			error: function(){
				//alert('请求失败');
			}
		});//通栏版猜你喜欢ajax结束括号
	}//通栏版猜你喜欢是否存在结束括号
	else if(typeof ourRule != 'undefined' && ourRule == 'baidu' && $('.you-like .you-like-list-box li a').length>1){
		$('#J_YouLike').fadeIn('2000');
		// 展示统计
		//首页ZOL规则猜你喜欢加载的 统计
		var event = 'pc_home_page_article_rec_load&plan_des=baidu-0-0-0-0';
		zol_niux_tongji(event,'http://www.zol.com.cn/');
		//点击的统计
		$('#J_YouLike').on('click','a',function(){
			var event = 'pc_home_page_article_rec_cilck&plan_des=baidu-0-0-0-0';
			zol_niux_tongji(event,'http://www.zol.com.cn/');
		});
	}
	if(typeof isSetHoverEvent == 'undefined'){
		var isSetHoverEvent = true;
		$('#J_YouLike .pic-item').hover(function(){
			$(this).addClass('current');
		},function(){
			$(this).removeClass('current');
		});
	}
	//猜你喜欢js特效,点击关闭
	$('#J_YouLikeClose').click(function(){
		$('#J_YouLike').slideUp("slow");
	});
	
	// 点击换一换
	var curIdx = 0; 
	var locked = false,locked_2 = false;
	$(document).delegate("#J_YouLikeSwitch", "click",function(){
	//}
	//$('#J_YouLikeSwitch').click(function(){
		// 发送统计数据
		var event	= 'vlike_change';
		var att		= '点击1次（通栏版换一换）';
	    var url		= 'www.zol.com.cn'; 
	    if(att){
			trackUrlClick(event,att,url);
		}
		if(locked){return false;}
		locked = true;
		$('#J_YouLike .you-like-list-box').find('.you-like-list').hide();
		$('#J_YouLike .you-like-list-box').append('<div class="you-like-loading" style="width:100px; height:100px; margin:-5px auto 0; background:url(http://icon.zol-img.com.cn/products/product2011/loading_circle_new.gif) no-repeat 0 0"></div>');
		var page = 3;
		curIdx++;
		if(curIdx == page){
			curIdx = 0;
		}
		setTimeout(function(){
			$('#J_YouLike .you-like-list-box').find('.you-like-loading').remove()
			$('#J_YouLike .you-like-list-box').find('.you-like-list').eq(curIdx).show().siblings().hide();
			locked = false;
		},400)
	});
	
	// 底部二手数据hover效果
	$('.flea-wrap .flea-list li').hover(
			function(){
				$(this).addClass('hover');
			},
			function(){
				$(this).removeClass('hover');
			}
	);
	// 二手数据图片就后加载
	$('.flea-list img').picLoad({checkShow:true});
	// 互动区块图片后加载
	$('.bbs-wrap img').picLoad({checkShow:true});
	// 判断用户地区,显示不同的N1 20150520 suhy
	if(false){
		$.ajax({
			type: "get",
			url: "http://dynamic.zol.com.cn/channel/mainpage/user_position.php?callback=?",
			//data: {ip_ck:ip_ck,type:2},
			dataType:'jsonp',
			success: function(data){
				//alert(n1Json['default'].length);
				//alert(data.provinceId+'_'+data.cityId);
				// 接口的json中的键如果是“255_-1” 则表示省id为255的省内的所有市的地区都显示该键下的数据内容
				var patt1=new RegExp("-1");
				var provinceIdArr = new Array();
				// 当前用户的provinceId
				var curProvinceId = Number(data.provinceId);
				for(x in n1Json){
					if(patt1.test(x)){
						var idArr = x.split('_');
						provinceIdArr.push(Number(idArr[0]));
					}
				}
				//alert(curProvinceId);
				//alert(data.provinceId+'_'+data.cityId);
				// n1Json是一个全局变量，php输出在这个页面上 。provinceId cityId  && n1Json[data.provinceId+'_'+data.cityId] != undefined
				if(n1Json[data.provinceId+'_'+data.cityId] && n1Json[data.provinceId+'_'+data.cityId] != null){
					var num = n1Json[data.provinceId+'_'+data.cityId].length;
					//alert(num);
					// 如果有多种广告，则随机取一种展示
					if(num >=1 )num -= 1;
					num = GetRandomNum(0,num);   
					// 存在对特殊地域的处理需求
					var dataObj = n1Json[data.provinceId+'_'+data.cityId][num];
					showN1PositionAd(dataObj);
					//alert(dataObj['showCount']);
					isNeedStatistics(dataObj);
					//alert('if');
				}else if(in_array(curProvinceId,provinceIdArr)){
					var num = n1Json[curProvinceId+'_-1'].length;
					if(num >=1 ) num -= 1;
					num = GetRandomNum(0,num);
					// 存在对特殊地域的处理需求,包含对某个省内的所有市做出判断
					var dataObj = n1Json[curProvinceId+'_-1'][num];
					showN1PositionAd(dataObj);
					isNeedStatistics(dataObj);
					//alert('else if(1)');
				}else if(n1Json['default'][0]){
					var num = n1Json['default'].length;
					if(num >=1 ) num -= 1;
					num = GetRandomNum(0,num);
					var dataObj = n1Json['default'][num];
					showN1PositionAd(dataObj);
					isNeedStatistics(dataObj);
					//alert('else if(2)');
				}else{
					$('.n1-pad-entrance-wrap').html('');
					$('.n1-pad-entrance-wrap').addClass('zhengzhouTry');
					$('.n1-pad-entrance-wrap').css({background:"url(http://icon.zol-img.com.cn/mainpage/20150210/N1-gif20150604.gif) no-repeat bottom"});
					$('.n1-pad-entrance-wrap').attr('href','http://city.zol.com/saas/bid/nemo.action');
					//alert('else');
				}
			},
			error: function(){
				//alert('请求失败[3]');
			}
	
		});
	}
	// 展现flash
	function showN1PositionAd(dataObj){
		//alert(swfUrl);
		switch(Number(dataObj.type)){
			case 1:
				$('.n1-pad-entrance-wrap').html('');
				$('.n1-pad-entrance-wrap').css({background:"url("+dataObj.img+") no-repeat bottom"});
				$('.n1-pad-entrance-wrap').attr('href',dataObj.url);
				isNeedStatistics(dataObj);
				break;
			case 2:
				$('#n1Ad0901').addClass('n1-pad-entrance-wrap');
				$('#n1Ad0901').html('<object type="application/x-shockwave-flash" id="_AD4263" data="'+dataObj.img+'" width="220" height="400"><param name="wmode" value="opaque"></object>');
				//isNeedStatistics(dataObj);
				break;
		}
		
	}
	// 判断是否需要加统计
	function isNeedStatistics(dataObj){
		if(!dataObj) return false;
		// $dataObj<=>n1Json[data.provinceId+'_'+data.cityId][num]
		//是否有需求进行“显示”统计，发送统计的url请求
		if(dataObj['showCount'] && dataObj['showCount'] != null){
			$.getScript(dataObj['showCount']);
		}
		//是否有需求进行“点击”统计，发送统计的url请求
		if(dataObj['clickCount'] && dataObj['clickCount'] != null){
			$('.n1-pad-entrance-wrap').click(function(){
				$.getScript(dataObj['clickCount']);
			});
		}
	}
	// 获取随机数
	function GetRandomNum(Min,Max){  
		// Max 为0的情况，只有一个广告
		if(Max <= 0){
			putIntoCookie(0);
			return 0;
		}
		var historyAdIndex = $.cookie("zol_index_right_n1_position");
		if(historyAdIndex){
			var historyAdIndexArr = historyAdIndex.split('-');
		}else{
			putIntoCookie(0);
			return 0;
		}
		if(Max <= 1){
			// Max 为1的情况，只有2个广告浏览过的 非0即1
			if(historyAdIndex == null || historyAdIndex == ''){
				var Range = Max - Min;   
				var Rand = Math.random(); 
				var resNumber = Min + Math.round(Rand * Range);
				putIntoCookie(resNumber);
				
				return resNumber;
			}
			var resIndex = historyAdIndexArr[0] == 0 ? 1 : 0;
			putIntoCookie(resIndex);
			return resIndex;
		}
		if(historyAdIndexArr.length == Max+1){
			var now_date = new Date().getTime(); //日期对象  毫秒级时间戳
			now_date = now_date + 86400000;// 24h    24*3600 000 改成24h   by suhy
			$.cookie('zol_index_right_n1_position','',{expires:new Date(now_date),domain:'zol.com.cn'});
			historyAdIndex = '';historyAdIndexArr = new Array();
		}
		
		// 将该地区的广告索引放入数组中
		var adIndexArr = new Array();
		for(var i=0;i<=Max;i++){
			adIndexArr[i] = i;
		}
		// 排除掉用户历史访问过的广告索引
		if(historyAdIndex != ''){
			// 1.剩下的大于2    获取可用的广告索引
			var allowAdIndexArr = new Array();
			for(var i=0,j=adIndexArr.length,a=0;i<j;i++){
				if(in_array(adIndexArr[i],historyAdIndexArr)){
					continue;
				}else{
					allowAdIndexArr[a] = adIndexArr[i];
					a++;
				}
			}
			Min = 0;
			Max = allowAdIndexArr.length-1;
			if(Max<=0) {
				putIntoCookie(allowAdIndexArr[0]);
				return allowAdIndexArr[0];
			}
		}else{
			//alert(historyAdIndex != '');
		}
		
		var Range = Max - Min;   
		var Rand = Math.random(); 
		var resNumber = Min + Math.round(Rand * Range);
		// 如果经过了上面的if也就是historyAdIndex != ''满足，则执行下面
		if(allowAdIndexArr){
			putIntoCookie(allowAdIndexArr[resNumber]);
			return allowAdIndexArr[resNumber];  
		}else{
			putIntoCookie(resNumber);
			return resNumber;  
		}
	}
	// 将用户刚刚浏览过的广告的索引记录进cookie中
	function putIntoCookie(adIndex){
		var historyAdIndex = $.cookie("zol_index_right_n1_position");
		if(historyAdIndex) var historyAdIndexArr = historyAdIndex.split('-'); 
		if(historyAdIndex && !in_array(adIndex,historyAdIndexArr)){
			adIndex = historyAdIndex + '-' + adIndex;
		}else if(historyAdIndex==null || historyAdIndex==''){
			adIndex = adIndex;
		}
		var now_date = new Date().getTime(); //日期对象  毫秒级时间戳
		now_date = now_date + 86400000;// 24h    24*3600 000 改成24h   by suhy
		$.cookie('zol_index_right_n1_position',adIndex,{expires:new Date(now_date),domain:'zol.com.cn'});
		return ;
	}
	// 右侧小边栏,即将到底部时。固定住  by suhy
	var cuffPoint = 7218;
	$(document).scroll(function(){
		var value1 = $('body').scrollTop();
		
		if(typeof(ourRule) != 'undefined'){
			cuffPoint = 7533;//7533=>7333
		}
		
		if(value1 >= cuffPoint){
			$('#guideWidget').css({bottom:'268px'});
		}else{
			$('#guideWidget').css({bottom:'60px'});
		}
	});
	/*// 首页如果有大屏的广告，则将二维码隐藏 by suhy 20150618
	$('.quick-mark').hide();
	var timer_quick = setInterval(function(){
		//console.log(12311);
		if($('#bgzy2,#bgad-right').length <= 0){
			$('.quick-mark').show();
		}else{
			$('.quick-mark').hide();
		}
	},1500);
	// 关闭定时器
	setTimeout(function(){
		clearInterval(timer_quick);
	},7000);*/
	// 添加个人登录后的未读消息的回调
	if(userid){
		$('<script src="http://my.zol.com.cn/public_msg_index.php?callback=__publicNav.getNotice"></script>').insertAfter('body');
	}
	// 春节期间，实时新闻滚动
	var paramObj_news = {
		itemNum:1//一次滚动元素的个数
		,itemSpace:32//每次移动的高度或的宽度  (32*3)如果滚动多个的话 请联系 苏汉宇
		,items:".newyear2016-news-time ul li"//滚动元素的选择符
		,itemparent:".newyear2016-news-time ul" //滚动元素父节点的选择符
		,vertical:true  //垂直滚动：true，左右滚动：false；默认false
		,speed:800      //切换时候的持续时间，数字越大表示越慢
		,pausetm:5000   //滚动后的停留时间 默认 5s
		,auto:true      //是否自动滚动   默认false
		,overstop:true  //鼠标放上去是否停留 默认 true
	};
	$(document).scrollShow(paramObj_news);

	
	
})(jQuery);

// ZOL大事件 renxq 20151125
$(function(){
	$( "#datepicker" ).datepicker();
	$( "#datepicker" ).datepicker('option', {
	    monthNames :  ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'], 
	    dayNamesMin : ['日', '一', '二', '三', '四', '五', '六'] ,
	    dateFormat: 'yy-mm-dd',
	    onSelect :function(dateText, inst){
//	    	var currentDay = $('#datepicker').find('.ui-clicked').html(); 
//	    	if(currentDay < 10 ){
//	    		currentDay = '0'+currentDay;
//	        }
//	    	var currentDate = inst.currentYear + '-' + (inst.currentMonth + 1) + '-' + currentDay;
//	    	
//	    	console.log(typeof dateText);
//	    	console.log(dateText);
//	    	console.log(typeof currentDate);
//	    	console.log(currentDate);
//	    	if(currentDate == dateText){
//	    		return false;
//	    	} else {
	    		var url="http://dynamic.zol.com.cn/channel/calendar/calendar_public_api.php?callback=?&mainpage=1&date="+dateText+"&t=2?"+new Date();
		        getAjaxContentCalendar(url);
//	    	}
	    },
	    onChangeMonthYear:function(year,month, inst){
	        if(month < 10 ){
	            month = '0'+month+'-01';
	        }else{
	            month = month+'-01';
	        }
	        var month = year+'-'+month;
	        var url="http://dynamic.zol.com.cn/channel/calendar/calendar_public_api.php?callback=?&mainpage=1&date="+month+"&type=uhksjbc"+"&t=3?"+new Date();
	        getAjaxContentCalendar(url);
	    } 
	});  
	initDatePicker(calendarData,now,0);
	// 初始化事件数据
	getAjaxContentCalendar("http://dynamic.zol.com.cn/channel/calendar/calendar_public_api.php?callback=?&mainpage=1&date="+now);
	//console.log("http://dynamic.zol.com.cn/channel/calendar/calendar_public_api.php?callback=?&date="+now);

	//日历重大事件交互
	function initDatePicker(data,now,clickDate){
	    $("#datepicker .ui-datepicker-calendar tbody").children('tr').find("td").each(function(){
	        var month = $(this).attr('data-month')-(-1);
	        if(month<10){
	            month = '0'+month;
	        }
	        var year  = $(this).attr('data-year');
	        var day   = $(this).find('a').html();
	        if(day<10){
	            day = '0'+day;
	        }
	        var date  = year+'-'+month+'-'+day;
	        for(var k in data){
	            if(data[k] == date){
	            	if(clickDate == date){
	            		$(this).find('a').addClass('ui-lived');
		            	$(this).find('a').addClass('ui-clicked');
		            } else {
		            	$(this).find('a').addClass('ui-lived');
		            }
	            }
	        }  
	        if(clickDate == date){
            	$(this).find('a').addClass('ui-lived');
            	$(this).find('a').addClass('ui-clicked');
            }
	    })
	}

	var _ulList 			= $('#ul_list'),				
		_calendarList		= $('#zhibo-20151120');
	calendar();
	function calendar(){
		_calendarList.on('click','.zhibo-popup-close',function(){
			$(this).parent().hide();
		});
		_calendarList.on('click','#ul_list li',function(){
			var id = $(this).attr('rel');
			$('#'+id).show().siblings('.zhibo-popup-20151120').hide();
		});
	}

	function getAjaxContentCalendar(url){
	    if(url == 'undefined'){
	        return false;
	    }
	    $.ajax({
	        type: "GET",
	        url: url,
	        cache:false,
	        dataType:"jsonp",
	        success: function(data){
	            if(data!=0){
	            	_calendarList.find('#calendarShowDiv').html(data.htmlStr);
	            	if($('#datepicker').find('tr').length == 5){
	    	        	$('.zhibo-list-20151120').addClass('zhibo-list-20151120-4');
	    	        }
	    	        if($('#datepicker').find('tr').length == 7){
	    	        	$('.zhibo-list-20151120').addClass('zhibo-list-20151120-6');
	    	        }
	            }
	            initDatePicker($.parseJSON(data.dateArr),data.now,data.clickDate);
	        }
	    })  
	}
});
//猜你喜欢优化，拼接html,值针对ZOL规则 by suhy
function guess_like_html_zol(data){
	//将36条数据分成12条一组
	var tOofset = 0;
	// 一共有多少组
	var pageTotal = typeof(data[0]['dataPageMax'])=='undefined' ? 1 : Number(data[0]['dataPageMax']);
	var step1Data = new Array();
	var offset1 = 12;
	var offset = 4;
	var resArr1 = new Array();
	for(var index1=0,j=0;j<pageTotal;j++){
		tOofset1 = index1*offset1;
		step1Data[index1] = data.slice(tOofset1,tOofset1+12);
		resArr1[j] = new Array();
		// 将12条数据进行分组(3组)
		for(var index=0,i=0;i<3;i++){
			tOofset = index*offset;
			resArr1[j][index] = step1Data[index1].slice(tOofset,tOofset+4);
			index++;
		}
		
		index1++;
	}
	if(typeof(ourRule)=='undefined'){
		return '';
	}
	// 为了适应不同的数据源的键
	if(ourRule == 'zol'){
		var img = 'pic_src',name='title',name2='short_title'; //short_title/  title
		var mark = '';
	}else{
		// 百分点数据来源
		var img = 'img',name='name',name2='name';
		var mark = '?bfdvlike=yes'; 
	}

	var ghtml = '',style1='';
	// 分组后进行拼接html
	for(var a=0;a<pageTotal;a++){
		var resArr = new Array();
		resArr = resArr1[a];
		//console.log(resArr);
		style1 = a<=0 ? 'style="display:block;"' : 'style="display:none;"';
		ghtml += '<div class="you-like-list" '+style1+'>';
		for(var i=0,max=resArr.length;i<max;i++){
			ghtml += '\
				<div class="you-like-item clearfix">\
			        <a class="pic-item" href="'+resArr[i][0]['url']+mark+'" title="'+resArr[i][0][name]+'">\
			            <img src="'+resArr[i][0][img]+'" width="100" height="75">\
			            <span>'+resArr[i][0][name2]+'</span>\
			        </a>\
			        <ul class="text-item">\
			            <li><a href="'+resArr[i][1]['url']+mark+'" title="'+resArr[i][1][name]+'">'+resArr[i][1][name2]+'</a></li>\
			            <li><a href="'+resArr[i][2]['url']+mark+'" title="'+resArr[i][2][name]+'">'+resArr[i][2][name2]+'</a></li>\
			            <li><a href="'+resArr[i][3]['url']+mark+'" title="'+resArr[i][3][name]+'">'+resArr[i][3][name2]+'</a></li>\
			        </ul>\
			    </div>';
		}
		ghtml += '</div>';
	}
	return ghtml;
	//$('.you-like .you-like-list-box').html(ghtml);
}

//二期猜你喜欢，拼接html,现改为只展示12条数据  百分点正在用20160107
function guess_like_html(data){
	//将36条数据分成12条一组
	var tOofset = 0;
	var step1Data = new Array();
	var offset1 = 12;
	var offset = 4;
	var resArr1 = new Array();
	for(var index1=0,j=0;j<3;j++){
		tOofset1 = index1*offset1;
		step1Data[index1] = data.slice(tOofset1,tOofset1+12);
		resArr1[j] = new Array();
		// 将12条数据进行分组(3组)
		for(var index=0,i=0;i<3;i++){
			tOofset = index*offset;
			resArr1[j][index] = step1Data[index1].slice(tOofset,tOofset+4);
			index++;
		}
		index1++;
	}

	// 为了适应不同的数据源的键
	if(data[0]['dataSource'] == 'zol'){
		var img = 'pic_src',name='title',name2='title';  // short_title
		var mark = '';
	}else{
		// 百分点数据来源
		var img = 'img',name='name',name2='name';
		var mark = '?bfdvlike=yes'; 
	}

	var ghtml = '',style1='';
	// 分组后进行拼接html
	for(var a=0;a<3;a++){
		var resArr = new Array();
		resArr = resArr1[a];
		//console.log(resArr);
		style1 = a<=0 ? 'style="display:block;"' : 'style="display:none;"';
		ghtml += '<div class="you-like-list" '+style1+'>';
		for(var i=0,max=resArr.length;i<max;i++){
			ghtml += '\
				<div class="you-like-item clearfix">\
			        <a class="pic-item" href="'+resArr[i][0]['url']+mark+'" title="'+resArr[i][0][name]+'">\
			            <img src="'+resArr[i][0][img]+'" width="100" height="75">\
			            <span>'+resArr[i][0][name2]+'</span>\
			        </a>\
			        <ul class="text-item">\
			            <li><a href="'+resArr[i][1]['url']+mark+'" title="'+resArr[i][1][name]+'">'+resArr[i][1][name2]+'</a></li>\
			            <li><a href="'+resArr[i][2]['url']+mark+'" title="'+resArr[i][2][name]+'">'+resArr[i][2][name2]+'</a></li>\
			            <li><a href="'+resArr[i][3]['url']+mark+'" title="'+resArr[i][3][name]+'">'+resArr[i][3][name2]+'</a></li>\
			        </ul>\
			    </div>';
		}
		ghtml += '</div>';
	}
	return ghtml;
	//$('.you-like .you-like-list-box').html(ghtml);
}



//百分点代码：首页
var ip_ck = $.cookie("ip_ck");
window["_BFD"] = window["_BFD"] || {};
_BFD.BFD_INFO = {
	"user_id" : userid, //网站当前用户id，如果未登录就为0或空字符串
	
	"page_type" : "homepage" //当前页面全称，请勿修改
};

window["_BFD"] = window["_BFD"] || {};
_BFD.client_id = "Czgc_pc";
_BFD.BFD_USER = {	
	"user_id" : userid, //网站当前用户id，如果未登录就为0或空字符串		
	"user_cookie" : ip_ck //网站当前用户的cookie，不管是否登录都需要传		
};
_BFD.script = document.createElement("script");
_BFD.script.type = "text/javascript";
_BFD.script.async = true;
_BFD.script.charset = "utf-8";
_BFD.script.src = (('https:' == document.location.protocol?'https://ssl-static1':'http://static1')+'.bfdcdn.com/service/zhongguancun_pc/zgc_pc.js');
document.getElementsByTagName("head")[0].appendChild(_BFD.script);

//猜你喜欢 百分点部分
if(typeof ourRule != 'undefined' && ourRule == 'baifendian'){
	_BFD.hpRecVAV =  function(data,req_id,banner_id){
		//百分点没有图片的问题 suhy
		var needImg = [0,4,8,12,16,20,24,28,32];
		var a = new Array();
		var data1 = data.slice(0,36);
		data = data1;
		//console.log(data1);
		for(var i=0;i<=32;i+=4){
			if(!data[i].img || data[i].img == ''){
				// 获取合适的单元（索引）
				var newIndex = get_suitable_index(i,data);
				//alert(data[i].img);
				a = data[i];
				data[i] = data[newIndex];
				data[newIndex] = a;
			}
		}
		var ghtml = guess_like_html(data);
		$('.you-like .you-like-list-box').html(ghtml);
		$('#J_YouLike').fadeIn('2000');
		//猜你喜欢hover短标题上滑特效
//		if(typeof isSetHoverEvent == 'undefined'){
//			var isSetHoverEvent = true;
//			$('#J_YouLike .pic-item').hover(function(){
//				$(this).addClass('current');
//			},function(){
//				$(this).removeClass('current');
//			});
//		}
		//百分点规则猜你喜欢加载的 统计 
		var event = 'pc_home_page_article_rec_load&plan_des=baifendian-0-0-0';
		zol_niux_tongji(event,'http://www.zol.com.cn/');
		//点击的统计
		$('#J_YouLike').on('click','a',function(){
			var event = 'pc_home_page_article_rec_cilck&plan_des=baifendian-0-0-0';
			zol_niux_tongji(event,'http://www.zol.com.cn/');
		});
		// 如果百分点没有数据则不展示“猜你喜欢栏目”
		if(data.length <= 0) $('#J_YouLike').remove();
		
		_BFD.showBind(data,"hpRecVAV",req_id,banner_id)//此处是推荐返回时百分点绑定事件的方法，请不要修改。
	}
}

function get_suitable_index(index,data){
	var needImg = [0,4,8,12,16,20,24,28,32];
	var newIndex = index + 1;
	if(newIndex >= 35) return 1;
	if(newIndex <= 35 && data[newIndex].img != '' && $.inArray(newIndex,needImg)!=-1){
		return newIndex;
	}else{
		return newIndex+1;
		//alert(index);
		return get_suitable_index(newIndex,data); // 用递归的话 会导致堆栈溢出
	}
}




/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/08/31 by supdes
*/
var screenW=parseInt(screen.width);

$(function(){
	var topadvs_time=null,W,topadv_index=0;
	//$(window).resize(function(){topadvs_int();});
	function topadvs_int(){
		if(screenW>640)screenW=640;
		W = screenW,H=parseInt(W*0.4647);
		o(bblank).css('height:'+(H-65)+'px');
		o(topadvs_ico).css('top:'+(H-5)+'px');
		$(".topadvs_main").css({"margin-left":-0.88*W+"px"});
		zeai.listEach('.topadvs_li',function(obj){//zeai.tag(resizeBNstr,'div')
			obj.style.width = 0.9*W+'px';
			obj.style.height= 0.9*H+'px';
			$(obj).css({"margin-left":0.01*W+"px","margin-right":0.01*W+"px"});
		});
		var topadvs_html='';
		$(".topadvs_main").find("div").each(function(index, element) { $(this).attr("bj",index);$(".topadvs_ico").append('<span class="topadvs_ico_li"></span>');});						
		$(".topadvs_main").append($(".topadvs_main").html());	
		$(".topadvs_li:last").prependTo(".topadvs_main");
		topadv_index=parseInt($(".topadvs_li:eq(1)").attr("bj"));
		$(".topadvs_ico_li").removeClass("topadvs_ico_li_on");
		$(".topadvs_ico_li:eq("+topadv_index+")").addClass("topadvs_ico_li_on");			
		topadvs_touch();
		topadvs_play();
	}	
	//向左
	function topadv_goleft(){
		var tt = arguments[0] ? arguments[0] :300;
		clearTimeout(topadvs_time);
		$(".topadvs_li:eq(0)").stop().animate({marginLeft:-W+"px"},tt,"swing",function(){			
			$(".topadvs_li:first").insertAfter($(".topadvs_li:last"));
			topadv_index=parseInt($(".topadvs_li:eq(1)").attr("bj"));
			$(".topadvs_ico_li").removeClass("topadvs_ico_li_on");
			$(".topadvs_ico_li:eq("+topadv_index+")").addClass("topadvs_ico_li_on");
			$(".topadvs_li:last").css({"margin-left":0.01*W+"px"});
			topadvs_play();
		});	
	}
	//向右
	function topadv_goright(){	
		var tt = arguments[0] ? arguments[0] :300;
		//最后一个移动到最前面，然后
		clearTimeout(topadvs_time);
		$(".topadvs_li:last").prependTo(".topadvs_main").css("margin-left","-"+0.91*W+"px");		
		$(".topadvs_li:eq(0)").stop().animate({marginLeft:0.01*W+"px"},tt,"swing",function(){
			topadv_index=parseInt($(".topadvs_li:eq(1)").attr("bj"));
			$(".topadvs_ico_li").removeClass("topadvs_ico_li_on");
			$(".topadvs_ico_li:eq("+topadv_index+")").addClass("topadvs_ico_li_on");
			topadvs_play();
		});	
	}		
	function topadvs_play(){topadvs_time=setTimeout(function(){ topadv_goleft();},4000); }		
	//触摸
	function topadvs_touch() {
		var _start = 0, _end = 0, _content = document.getElementById("topadvs");
		_content.addEventListener("touchstart", touchStart, false);
		_content.addEventListener("touchmove", touchMove, false);
		_content.addEventListener("touchend", touchEnd, false);
		function touchStart(event) {
			var touch = event.targetTouches[0];
			_start = touch.pageX;
		}
		function touchMove(event) {
			var touch = event.targetTouches[0];
			_end = (_start - touch.pageX);
		}
		function touchEnd(event) {
			if (_end < -60) {
				topadv_goright(100);
				_end=0;
			}else if(_end > 60){
				topadv_goleft(100);
				_end=0;
			}
		}
	}	
	topadvs_int();
});
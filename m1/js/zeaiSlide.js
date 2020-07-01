/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/05/15 by supdes
*/
var zeaiSlide={
    index:1,
    run:true,
    load:false,
    init:function () {getudata(zeaiSlide.index);},
    animateMove:function (el,val) {
        if(!this.run){return;}
        this.run=false;
        el.css({"transform":"translate3d("+doc_width*val+"px,"+zeaiSlide.top_val*2.2+"px,0px)","transition-duration":"0.3s"});
        var moveTime=setTimeout(function () {
            el.remove();
            var ind_el=o("#ind-"+zeaiSlide.index);
            zeaiSlide.index++;
			slidegz.removeClass('ed');
			getudata(zeaiSlide.index);
            zeaiSlide.run=true;
        },300);
    },
    animateReset:function (el) {
        el.css({"transform":"translate3d(0px,0px,0px)","transition-duration":"0.3s"});
        var resetTime=setTimeout(function () {
            el.css("transition-duration","0s");
        },500);
    },
    activeEl:function (el) {el.style.zIndex=2;},
    clearLocation:function () {this.left_val=0;}
};
function getudata(pp){
	zeai.ajax({url:HOST+'/m1/index_more_big'+zeai.ajxext+'submitok=ajax_getdata&t='+t+'&p='+pp},function(e){
		if(e=='end'){zeai.msg('已经没有会员了');return;}
		var str='<div style="z-index:1" id="ind-'+pp+'" class="zeaiC">'+e+'</div>';
		o('mbox').append(str);
		if(pp==1){
			zeaiSlide.activeEl(o("ind-"+pp));
		}else{
			pp--;
			zeaiSlide.activeEl(o("ind-"+pp));
		}
		slidehi.show();
	});
}
$("#zeaiSlideObj").on("touchstart",function(e) {
	zeaiSlide.left_val=0;
    if(!zeaiSlide.load || !zeaiSlide.run){return;}
    var ev = e || window.event;
    zeaiSlide._x_start=ev.touches[0].pageX;
    zeaiSlide._y_start=ev.touches[0].pageY;
    var act_el=$("#ind-"+(zeaiSlide.index-1).toString(10));
});
$("#zeaiSlideObj").on("touchmove",function(e) {
    if(!zeaiSlide.load || !zeaiSlide.run){return;}
    var ev = e || window.event;
    zeaiSlide._x_move=ev.touches[0].pageX;
    zeaiSlide._y_move=ev.touches[0].pageY;
    var act_el=$("#ind-"+(zeaiSlide.index-1).toString(10));
    zeaiSlide.top_val=parseFloat(zeaiSlide._y_move)-parseFloat(zeaiSlide._y_start);
    zeaiSlide.left_val=parseFloat(zeaiSlide._x_move)-parseFloat(zeaiSlide._x_start);
	var jdz_diff_=Math.abs(zeaiSlide.left_val),bfb,top_bfb,top_diff;
	jdz_diff = jdz_diff_/100;
	if(jdz_diff>1){
		bfb=1;
	}else{
		jdz_diff = jdz_diff/0.1;
		jdz_diff = 0.1-0.1/jdz_diff;
		jdz_diff = 0.9+jdz_diff;
		jdz_diff = jdz_diff.toFixed(2);
		bfb=jdz_diff;
		top_diff=jdz_diff_;
		if(top_diff>30){
			top_bfb = 0;
		}else{
			top_bfb = parseInt(30-top_diff);;
		}
		o('ind-'+zeaiSlide.index).css('top:'+top_bfb+'px;transform:scale('+bfb+')');
	}
	act_el.css({"transform":"translate3d("+zeaiSlide.left_val+"px,"+zeaiSlide.top_val+"px,0px)","transition-duration":"0s"});
	var b=zeai.tag(o("ind-"+(zeaiSlide.index-1)),'i')[0];
	b_diff=jdz_diff_;
	b_diff=(b_diff>100)?100:b_diff;
	b_bfb=b_diff/100;
	b_bfb=b_bfb.toFixed(2);
	b.style.opacity=b_bfb;
});
$("#zeaiSlideObj").on("touchend",function(e) {
    if(!zeaiSlide.load || !zeaiSlide.run){return;}
    var ev = e || window.event;
    zeaiSlide._x_end=ev.changedTouches[0].pageX;
    zeaiSlide._y_end=ev.changedTouches[0].pageY;
    var act_el=$("#ind-"+(zeaiSlide.index-1).toString(10));
	var zeaiTBdiff=zeaiSlide._y_end-zeaiSlide._y_start;
	if(zeaiTBdiff>100 || zeaiTBdiff<-100){
		var uid=zeai.tag(o("ind-"+(zeaiSlide.index-1)),'i')[0].getAttribute("uid"),submitok;
		if(zeaiTBdiff>100){
			submitok='ajax_gz0';
		}else if(zeaiTBdiff<-100){
			submitok='ajax_gz1';
		}
		zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:submitok,uid:uid}},function(e){rs=zeai.jsoneval(e);zeai.msg(rs.msg);
			(zeaiTBdiff<-100 && slidegz.addClass('ed')) || (zeaiTBdiff>100 && slidegz.removeClass('ed'))
		});
	}
    if(zeaiSlide.left_val>0 && zeaiSlide.left_val>doc_width/2-doc_width/4.5){
        zeaiSlide.animateMove(act_el,1);
    }else if(zeaiSlide.left_val<0 && zeaiSlide.left_val<-doc_width/2+doc_width/4.5){
        zeaiSlide.animateMove(act_el,-1);
    }else {
        zeaiSlide.animateReset(act_el);
    }
});

function slidulink(uid){ZeaiM.page.load(HOST+'/m1/u'+zeai.ajxext+'uid='+uid,ZEAI_MAIN,'u');}
var doc_width=$(document).width(),doc_height=$(document).height();
$(function (){zeaiSlide.load=true;});
zeaiSlide.init();
zeaiSlide.index++;
zeaiLoadBack=['nav'];
setTimeout(function(){	getudata(zeaiSlide.index);},200);
slidepass.onclick=function(){
	console.log(zeaiSlide.index);
	var curobj=o('ind-'+(zeaiSlide.index-1)),act_el=$("#ind-"+(zeaiSlide.index-1).toString(10));
	this.addClass('btn_s_b_s');setTimeout(function(){slidepass.removeClass('btn_s_b_s')},300);
	o('ind-'+zeaiSlide.index).css('top:0px;transform:scale(1)');
	act_el.css({"transform":"translate3d(-250px,20px,0px)","transition-duration":"0s"});
	zeaiSlide.animateMove(act_el,-1);
}
slidegz.onclick=function(){
	this.addClass('btn_s_b_s');setTimeout(function(){slidegz.removeClass('btn_s_b_s')},300);
	var uid=zeai.tag(o("ind-"+(zeaiSlide.index-1)),'i')[0].getAttribute("uid");
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_gz',uid:uid}},function(e){rs=zeai.jsoneval(e);zeai.msg(rs.msg);(rs.flag==1 && slidegz.addClass('ed')) || (rs.flag==0 && slidegz.removeClass('ed'))});
}
if(!zeai.empty(o('slidehi'))){
	slidehi.onclick=function(){
		this.addClass('btn_s_b_s');setTimeout(function(){slidehi.removeClass('btn_s_b_s')},300);
		var uid=zeai.tag(o("ind-"+(zeaiSlide.index-1)),'i')[0].getAttribute("uid");
		zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_senddzh',uid:uid}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				slidehi.hide();
				index_more_big0_100_0.html('<i class="ico hi">&#xe6bd;</i>招呼已发送');index_more_big0_100_0.show();setTimeout(function(){index_more_big0_100_0.hide();},2100);
			}else if(rs.flag=='nodata'){
				nodata('u') ;
			}else{
				zeai.msg(rs.msg);
			}
		});
	}
}

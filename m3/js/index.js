/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/08/30 by supdes
*/
var zeai_iMarquee = function (id, tag, heigh, speed, delay) {
	var me = this;
	me.EL = document.getElementById(id);
	me.PA = 0;
	me.TI = null;
	me.LH = heigh;
	me.SP = speed;
	me.DY = delay;
	me.exec = function () {
		if (me.PA) return;
		me.EL.scrollTop += 2;
		if (me.EL.scrollTop % me.LH <= 1) {
			clearInterval(me.TI);
			me.EL.appendChild(me.EL.getElementsByTagName(tag)[0]);
			me.EL.scrollTop = 0;
			setTimeout(me.start, me.DY * 1000);
		}
	};
	me.start = function () {if (me.EL.scrollHeight - me.EL.offsetHeight >= me.LH) me.TI = setInterval(me.exec, me.SP);};
	//me.EL.onmouseover = function () { me.PA = 1 };
	//me.EL.onmouseout = function () { me.PA = 0 };
	setTimeout(me.start, me.DY * 1000);
};
function zeai_iMarqueeFn(){
	zeai.ajax({url:'m3/index'+zeai.ajxext+'ie=ajax_iMarquee'},function(e){
		if(!zeai.empty(e)){
			iMarquee.html(e);
			setTimeout(function(){new zeai_iMarquee("iMarquee","li",30,50,1.5);},100);
		}
	});
}
var WTFarr = [];
function waterfall_load(mainobj,e,p){
	var columns = 2;
	var div = zeai.addtag('div');div.id='p'+p;div.append(e);
	var newlist = zeai.tag(div,'a');
	mainobj.append(div);
	var l = newlist.length,m = 0;
	zeai.listEach(newlist,function(obj,i){
		minobj_L = (i % 2 == 0)?'50%':'0px';
		obj.css('transform:translate('+minobj_L+','+WTFarr[0]+'px)');
		var img=obj.firstChild;
		img.onload=function(){m++;if (m >= l)imgInit();}
		img.onerror=function(){this.src='res/photo_m'+obj.getAttribute("sex")+'.png';}
		
	});
	function imgInit(){
		zeai.listEach(newlist,function(obj,i){
			var w=obj.offsetWidth;
			var h=obj.offsetHeight;
			var minobj_L;
			var aHeight = h;
			var minHeight = WTFarr[0],index = 0,WTFarrL=WTFarr.length;
			for (var j = 0; j < WTFarrL; j++) {if (minHeight > WTFarr[j]){index = j;}}
			minobj_L = (index > 0)?w+'px':'0px';
			obj.css('transform:translate('+minobj_L+','+WTFarr[index]+'px)');
			WTFarr[index] = WTFarr[index] + aHeight;
			obj.firstChild.onclick=zeaiM_link;
		});
	}
}

function waterfall(mainobj){
	var columns = 2,ulist=zeai.tag(mainobj,'a');
	zeai.listEach(ulist,function(obj,i){
		var w=obj.offsetWidth,h=obj.offsetHeight,boxheight=h;
		var minobj_L;
		if (i<columns){
			obj.css('transform:translate('+w*i+'px,0px)');
			WTFarr.push(boxheight);
		}else{
			//找到数组最小高和索引
			var minHeight = WTFarr[0],index = 0,WTFarrL=WTFarr.length;
			for (var j = 0; j < WTFarrL; j++) {if (minHeight > WTFarr[j])index = j;}
			//设置下一行的第一个盒子位置top值就是最小列高 
			minobj_L = (index > 0)?w+'px':'0px';
			obj.css('transform:translate('+minobj_L+','+WTFarr[index]+'px)');
			//改最小列高 最小列高 = 当前自己高 + 拼接过来的高
			WTFarr[index] = WTFarr[index] + boxheight;
		}
		obj.firstChild.onclick=zeaiM_link;
	});
}
function zeaiM_link(){
	var uid=this.parentNode.getAttribute("uid");
	if(!zeai.ifint(uid)){
		var url=this.parentNode.getAttribute("url");
		if(!zeai.empty(url))zeai.openurl_(url);
	}else{
		page({g:'m1/u'+zeai.ajxext+'uid='+this.parentNode.getAttribute("uid"),l:'u'});
	}
}
function indexOnscroll(){
	var t = parseInt(o(main).scrollTop);
	var cH= parseInt(o(main).clientHeight);
	var  H= parseInt(o(main).scrollHeight);
	if (H-t-cH <428 && t>400){//t+cH==H
		if (p > totalP){
			return false;
		}else{
			zeai.ajax({url:'m3/index'+zeai.ajxext+'ie='+ie,data:{submitok:'ajax_ulist',totalP:totalP,p:p}},function(e){
				if (e == 'end'){
					zeai.msg(0);zeai.msg('已达末页，全部加载结束');return false;
				}else{
					waterfall_load(indexUlist,e,p);
					p++;
				}
		});}
	}
	backtopFn(o(main));
}

function indexLoad(indexUlist) {
	var adom=zeai.tag(indexUlist,'a'),aL=adom.length,s=0;
	zeai.listEach(adom,function(obj,i){
		obj.firstChild.onload=function(){s++;if (s >= aL)indexInit(indexUlist);}
		obj.firstChild.onerror=function(){this.src='res/photo_m'+obj.getAttribute("sex")+'.png';}
	});		
}
function indexInit(indexUlist){
	WTFarr = [];p=2,waterfall(indexUlist);
}

window.onresize = function(){
	if(ifbanner==1){
		screenW=parseInt(screen.width);
		if(screenW>640)screenW=640;
		W = screenW,H=parseInt(W*0.4647);
		o(topadvs).css('width:'+W+'px');
		o(topadvs_ico).css('width:'+W+'px');
		o(bblank).css('height:'+(H-65)+'px');
		o(topadvs_ico).css('top:'+(H-5)+'px');
		$(".topadvs_main").css({"margin-left":-0.88*W+"px"});
		zeai.listEach('.topadvs_li',function(obj){//zeai.tag(resizeBNstr,'div')
			obj.style.width = 0.9*W+'px';
			obj.style.height= 0.9*H+'px';
			$(obj).css({"margin-left":0.01*W+"px","margin-right":0.01*W+"px"});
		});
	}
	//indexInit(indexUlist);
	if(iModuleU==2){
		iu_btn2_1.click();
	}else{
		iu_btn1.click();
	}
}
window.onload = function () {
	iso.onclick=function(){page({g:'m1/search'+zeai.ajxext+'a=2',l:'search'});}
	pushindex_btn.onclick=function(){page({g:'m1/my_push_index'+zeai.extname,l:'my_push_index'});}
	if(mobkind()=='android'){zeai.listEach(zeai.tag(tabmenuIndex,'li'),function(obj){obj.style.lineHeight = '52px';});}
	if(mobkind()=='ios'){
		var content = o('main');
		content.addEventListener('touchstart', function(event) {
			this.allowUp = (this.scrollTop > 0);
			this.allowDown = (this.scrollTop < this.scrollHeight - this.clientHeight);
			this.slideBeginY = event.pageY;
		});
		content.addEventListener('touchmove', function(event) {
			var up = (event.pageY > this.slideBeginY),
			down = (event.pageY < this.slideBeginY);
			this.slideBeginY = event.pageY;
			if ((up && this.allowUp) || (down && this.allowDown)) {event.stopPropagation();}else {event.preventDefault();}
		});			 
	}
}
btmKefuBtn.onclick=function(){
	ZeaiM.div({obj:btmKefuBox,w:260,h:280});
}
function iu_btn2Fn(){	
	if(browser=='wx'){
		wx.getLocation({
			type: 'wgs84',
			success: function (res) {
				var latitude = res.latitude,longitude= res.longitude;
				zeai.ajax({url:'m3/index'+zeai.extname,data:{ie:'ajax_gps_save',latitude:latitude,longitude:longitude}},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1)iu_btn2.click;
				});
			},
			cancel: function (res) {ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:iu_btn1});zeai.msg('您已拒绝授权定位，将无法使用附近的人',{time:4});},
			fail: function (res) {
				zeai.msg('请开启手机本身的GPS定位后并同意授权',{time:4});
			}
		});
	}else if(browser=='app'){
		plus.geolocation.getCurrentPosition(function(p){
			zeai.ajax({url:'m3/index'+zeai.extname,data:{ie:'ajax_gps_save',latitude:p.coords.latitude,longitude:p.coords.longitude}},function(e){
				rs=zeai.jsoneval(e);
				if(rs.flag==1){iu_btn2.click;}else{
					zeai.msg('定位失败:' + rs.msg,{time:4});
				}
			});	
		}, function(e){
			zeai.msg('获取定位失败:' + e.message,{time:4});
		});
	}else{
		zeai.msg('请使用微信打开并同意获取自己的定位',{time:4});
	}
}

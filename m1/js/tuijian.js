/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/10/20 by supdes
*/
var WTFarr = [];
function tjInit(zeai_cn){
	WTFarr = [];p=2,waterfall(zeai_cn);
	function waterfall(mainobj){
		var columns = 2,ulist=zeai.tag(mainobj,'a');
		zeai.listEach(ulist,function(obj,i){
			var w=obj.offsetWidth,h=obj.offsetHeight,boxheight=h;
			var minobj_L;
			if (i<columns){
				obj.css('transform:translate('+w*i+'px,0px)');
				WTFarr.push(boxheight);
			}else{
				var minHeight = WTFarr[0],index = 0,WTFarrL=WTFarr.length;
				for (var j = 0; j < WTFarrL; j++) {if (minHeight > WTFarr[j])index = j;}
				minobj_L = (index > 0)?w+'px':'0px';
				obj.css('transform:translate('+minobj_L+','+WTFarr[index]+'px)');
				WTFarr[index] = WTFarr[index] + boxheight;
			}
			obj.firstChild.onclick=zeaiM_link;
		});
	}
}
function tjOnscrollFn(){
	var t = parseInt(o(main).scrollTop),
	cH= parseInt(o(main).clientHeight),
	H= parseInt(o(main).scrollHeight);
	C=parseInt(H-t-cH);
	if (C <100 && t>200){
		if (p > totalP){
			return false;
		}else{
			zeai.ajax({url:HOST+'/m1/tuijian'+zeai.ajxext+'zeai_cn='+zeai_cn+'&requrl='+requrl,data:{submitok:'ajax_ulist',totalP:totalP,p:p}},function(e){
			if (e == 'end'){zeai.msg(0);zeai.msg('已达末页，全部加载结束');return false;}else{waterfall_load(ulist,e,p);p++;}
		});}
	}
	backtopFn(o(main));
}
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
function zeaiM_link(){page({g:HOST+'/m1/u'+zeai.ajxext+'uid='+this.parentNode.getAttribute("uid"),l:'u'});}
btmKefuBtn.onclick=function(){ZeaiM.div({obj:btmKefuBox,w:260,h:280});}
function tjbtnFn(){ZeaiM.div_up({fobj:ZEAI_MAIN,obj:diybox,h:360});}
/**
* Copyright (C)2001-2099 Zeai.cn V6.0 All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
function zeaiplay(zeai_cn) {
	var v = o('zeaiVbox'+zeai_cn),img=o('img'+zeai_cn);
	zeai.msg('视频加载中...');
	ZeaiPC.div({obj:v,w:400,h:430,fn:function(){v.hide();}});
	v.style.width='90%';v.style.height='90%';v.style.position='absolute';v.style.left='20px';v.style.top='15px';
	img=img.src;img=img.replace('.jpg','.mp4');
	v.src=img;v.show();v.play();
	v.addEventListener("contextmenu", function(event){event.preventDefault();});
}
//zeai.listEach(zeai.tag(o('my_trend_box'),'a'),function(obj){if(obj.className=='aQING'){console.log(obj);obj.remove();}});
function my_trendInit(){
	zeai.listEach(zeai.tag(o('my_trend_box'),'button'),function(obj){
		obj.onclick=function(){
			var clsid=this.getAttribute("clsid");
			zeai.alertplus({title:'确定要删除么？',content:'如果要删除请点击【确定】',title1:'取消',title2:'确定',
				fn1:function(){zeai.alertplus(0);},
				fn2:function(){zeai.alertplus(0);
					zeai.ajax({url:PCHOST+'/my_trend'+zeai.ajxext+'submitok=ajax_del&id='+clsid},function(e){rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				}
			});
		}
	});
}
var trend_pic_Slist=[],localIds=[],supdes;
function trend_btn_saveFn(){
	var Lth=arrLength(trend_pic_Slist);
	if(Lth>4){zeai.msg('最多只能传4张照片，请删减');return false;}
	var C=zeai.clearhtml(content.value);
	content.value = C.substring(0,140);
	if (zeai.empty(C) || C.length > 140){zeai.msg('亲，该说点什么吧～',content);return false;}
	zeai.msg("请稍后，正在保存/推送给你粉丝",{time:60});
	zeai.ajax({url:PCHOST+'/my_trend'+zeai.ajxext+'submitok=ajax_addupdate',form:trendbox},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);parent.zeai.msg(rs.msg);
		if(rs.flag==1){
			setTimeout(function(){parent.location.reload(true);},1000);
			//parent.supdes.click();
		}
	});
}
function contentFn(){
	var ic = this.value.length;
	if (ic>140){
		this.value = this.value.substring(0,140);
		zeai.msg(0);zeai.msg('最多140字');
		ic = 140;
		return false;
	}
	inpttext.html(ic);
}
function btnpicFn(){
	var Lth=arrLength(trend_pic_Slist);
	if(Lth>=4){zeai.msg('最多只能传4张哦');return;}
	zeai.photoUp({
		btnadd:ul,
		url:PCHOST+"/my_trend"+zeai.extname,
		submitok:"ajax_photo_up",
		multiple:4,
		li:function(e){
			addli(up2+e._s);
		},
		end:function(e){
			zeai.msg(0);
		}
	});
}
function addli(url){
	var li = document.createElement('li');
	var img = document.createElement('img');
	var b = document.createElement('b');
	li.appendChild(img);li.appendChild(b);ul.appendChild(li);
	img.src = url;
	trend_pic_Slist.push(url);
	morelist.value = arrReset(trend_pic_Slist).join(",");
	b.onclick = function (){
		li.parentNode.removeChild(li);
		trend_pic_Slist = trend_pic_Slist.remove(url);
		morelist.value  = arrReset(trend_pic_Slist).join(",");
	}
	img.onclick = function (){
		parent.ZeaiPC.piczoom(url.replace('_s.','_b.'));
	}
}

function arrLength(ARR){
	var l=0;
	for(var k=0;k<ARR.length;k++) {if(typeof(ARR[k]) == "string")l++;}
	return l;
}
function arrReset(ARR){
	var l=[];
	for(var k=0;k<ARR.length;k++) {if(typeof(ARR[k]) == "string")l.push(ARR[k]);	}
	return l;
}

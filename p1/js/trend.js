/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/24 by supdes
*/var supdes;
function trendSetList(){
	zeai.listEach(zeai.tag(list,'dl'),function(dl){
		trendSetList_li(dl);
	});
}
function zeaiplay(zeai_cn) {
	var v = o('zeaiVbox'+zeai_cn),img=o('img'+zeai_cn);
	zeai.msg('视频加载中...');
	ZeaiPC.div({obj:v,w:400,h:430,fn:function(){v.hide();}});
	v.style.width='90%';v.style.height='90%';v.style.position='absolute';v.style.left='20px';v.style.top='15px';
	img=img.src;img=img.replace('.jpg','.mp4');
	v.src=img;v.show();v.play();
	v.addEventListener("contextmenu", function(event){event.preventDefault();});
}

function trendSetList_li(dl){
	var dt = dl.children[0],dd = dl.children[1],gz=dt.lastChild;
	var p=zeai.tag(dl,'p')[0],uid=dt.getAttribute("uid"),p,plist=[],img;
	gz.onclick = function (){
		var self=this;
		zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_gz',uid:uid}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				self.addClass('ed');self.html('<i class="ico">&#xe6b1;</i> 已关注');
			}else{
				self.removeClass('ed');self.html('<i class="ico">&#xe620;</i> 加关注');
			}
			zeai.msg(rs.msg);
		});
	}
	var span=zeai.tag(dt,'span');
	span=span[0];
	photo_m=span.getAttribute("value");
	span.style.backgroundImage='url('+photo_m+')';
	//piclist
	if(!zeai.empty(p)){
		pic=zeai.tag(p,'span');
		zeai.listEach(pic,function(obj){
			var psrc=obj.getAttribute("value");
			obj.style.backgroundImage='url('+psrc+')';
			obj.onclick=function(){
				ZeaiPC.piczoom(psrc.replace('_s.','_b.'));
			}
		});
	}
	var ddh1=zeai.tag(dd,'h1');
	//content pic
	if(ddh1){
		ddh1=ddh1[0];ddh1str=ddh1.innerHTML;
		ddh1img=zeai.tag(ddh1,'img');
		ddh1img=ddh1img[0];
		if(ddh1img){
			imgurl=ddh1img.src;
			if (imgurl.indexOf('_m.') != -1){	
				imgurl=imgurl.replace('_m.','_b.');
			}
			if(ddh1img.className=='photo_m' && ddh1str.indexOf('上传了头像') != -1){
				(function(photo_m){ddh1img.onclick=function(){
					ZeaiPC.piczoom(photo_m.replace('_s.','_b.'));
				}})(photo_m);
				photo_m=photo_m.replace('_s.','_b.');
				ddh1img.src=photo_m;
			}else{
				if(imgurl.indexOf('_b.') != -1){
					(function(imgurl){ddh1img.onclick=function(){
						ZeaiPC.piczoom(imgurl);
					}})(imgurl);
				}else{
					(function(imgurl){ddh1img.onclick=function(){
						ZeaiPC.piczoom(img.src.replace('_s.','_b.'));
					}})(imgurl);
				}
			}
		}
	}
	//agree
	var agreeDIV = dd.getElementsByTagName("div")[0].children;
	var agree = agreeDIV[1],chat = agreeDIV[2],agreenum = agreeDIV[3];
	if (agree.className != 'ed'){
		agree.onclick = function (){
			var tid = this.getAttribute("tid");
			zeai.ajax({url:PCHOST+'/trend'+zeai.extname,data:{submitok:'ajax_agree',tid:tid}},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){
					agree.className    = 'ed';
					agree.onclick      = function (){}
					agreenum.html(parseInt(agreenum.innerHTML)+1);
					if (rs.num == 1){
						var j  = document.createElement('div');j.className = 'j';dd.appendChild(j);
						var em = document.createElement('em');dd.appendChild(em);
					}else{
						var em  = dd.getElementsByTagName("em")[0];
					}
					em.insertAdjacentHTML('afterbegin',rs.C);
				}else if(rs.flag=='nologin'){
					zeai.msg(0);zeai.msg(rs.msg,{time:2});
					setTimeout(function(){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));},2000);
				}else{zeai.msg(rs.msg);}
			});
		}
	}
	//chat
	chat.onclick = function (){
		var tid = this.getAttribute("tid");
		zeai.ajax({url:PCHOST+'/trend'+zeai.ajxext+'submitok=ajax_chklogin'},function(e){rs=zeai.jsoneval(e);
			if(rs.flag=='nologin'){
				zeai.msg(0);zeai.msg(rs.msg,{time:2});
				setTimeout(function(){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));},2000);
			}else{
				supdes=ZeaiPC.iframe({url:PCHOST+'/trend'+zeai.ajxext+'submitok=trend_bbs_add&tid='+tid,w:500,h:300});
			}
		});
	}
}
if(!zeai.empty(o('list')))trendSetList();
function trendbbs_btn_saveFn(){
	var C=zeai.clearhtml(content.value);
	content.value = C.substring(0,140);
	if (zeai.empty(C) || C.length > 140){zeai.msg('亲，该说点什么吧～',content);return false;}
	zeai.ajax({url:PCHOST+'/trend'+zeai.extname,form:trendZ_eA_I____cn_bbsbox},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){
			setTimeout(function(){parent.location.reload(true);},1000);
		}else if(rs.flag=='nologin'){
			setTimeout(function(){if(rs.flag=='nologin'){parent.zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}},1000);
		}else{setTimeout(function(){parent.supdes.click();},1000);}
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
function trend_add(){
	supdes=ZeaiPC.iframe({url:PCHOST+'/my_trend.php?submitok=add',w:666,h:400});
}

function delmyFn(){
	var id = parseInt(this.getAttribute("clsid"));
	ZeaiM.confirmUp({title:'真的要删除么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax('m1/trend'+zeai.ajxext+'submitok=delmy&id='+id,function(e){var rs=zeai.jsoneval(e);
			if (rs.flag == 1){
				o('dl'+id).remove();
			}else{zeai.msg(rs.msg);}
		});
	}});
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
//up
function addli(url){
	var li = document.createElement('li');
	var img = document.createElement('img');
	var b = document.createElement('b');
	li.appendChild(img);li.appendChild(b);ul.appendChild(li);
	if(browser=='wx'){
		var local=url[0],url=url[1];
		localIds.push(local);
		img.src = local;
	}else{
		img.src = url;
	}
	trend_pic_Slist.push(url);
	morelist.value = arrReset(trend_pic_Slist).join(",");
	b.onclick = function (){
		li.parentNode.removeChild(li);
		trend_pic_Slist = trend_pic_Slist.remove(url);
		morelist.value  = arrReset(trend_pic_Slist).join(",");
		if(browser=='wx'){
			localIds = localIds.remove(url);
			localIds = arrReset(localIds);
		}
	}
	img.onclick = function (){
		if(browser=='wx'){
			ZeaiM.piczoom({browser:browser,b:local,list:localIds});
		}else{
			ZeaiM.piczoom({browser:browser,b:url.replace('_s.','_b.')});
		}
	}
}
function arrLength(ARR){
	var l=0;
	for(var k=0;k<ARR.length;k++) {
		if(typeof(ARR[k]) == "string")l++;
	}
	return l;
}
function arrReset(ARR){
	var l=[];
	for(var k=0;k<ARR.length;k++) {
		if(typeof(ARR[k]) == "string")l.push(ARR[k]);
	}
	return l;
}
function trend_btn_saveFn(){
	var Lth=arrLength(trend_pic_Slist);
	if(Lth>6){zeai.msg('最多只能传6张照片，请删减');return false;}
	var C=clearHtml(content.value);
	if (zeai.empty(C) || C.length > 140){zeai.msg('亲，该说点什么吧～',content);return false;}
	zeai.msg("请稍后，正在保存");
	content.value = C.substring(0,140);
	
	//alert('save:'+morelist.value);
	
	zeai.ajax({url:'m1/trend.php?submitok=addupdate',form:trendbox},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){
			setTimeout(function(){location.reload(true);},1000);
		}
	});
}
function btnpicFn(){
	var Lth=arrLength(trend_pic_Slist);
	if(Lth>=6){zeai.msg('最多只能传6张哦');return;}
	photoUp({
		onclick:false,
		btnadd:ul,
		url:"m1/trend.php",
		submitokBef:"ajax_photo_",
		multiple:6,
		wxtmp:true,
		li:function(e){
			if(browser=='wx'){
				addli(e);
			}else{
				addli(up2+e._s);
			}
		},
		_:function(e){
			zeai.msg(0);
			//if(browser=='wx'){
				//trend_pic_Slist = e;
				//morelist.value = e.join(",");
			//}
		}
	});
}
//upend


//trendSetList
function trendSetList(){
	zeai.listEach(zeai.tag(list,'dl'),function(dl){
		trendSetList_li(dl);
	});
}
function trendSetList_li(dl){
	var dt = dl.children[0],dd = dl.children[1];//dt=zeai.tag(dl,'dt')[0],
	var p=zeai.tag(dl,'p')[0],uid=dt.getAttribute("uid"),p,plist=[],img;
	//u href
	dt.onclick=function(){ZeaiM.page.load('m1/u.php?uid='+uid,ZEAI_MAIN,'u');}
	//piclist
	if(!zeai.empty(p)){
		pic=zeai.tag(p,'img');
		for(var k=0;k<pic.length;k++) {
			img=pic[k];
			plist.push(img.src.replace('_s.','_b.'));
			(function(img){img.onclick=function(){ZeaiM.piczoom({browser:browser,b:img.src.replace('_s.','_b.'),list:plist});}})(img);
		}
	}
	var ddh1=zeai.tag(dd,'h1');
	//content pic
	if(ddh1){
		ddh1=ddh1[0];
		ddh1img=zeai.tag(ddh1,'img');
		ddh1img=ddh1img[0];
		if(ddh1img){
			imgurl=ddh1img.src;
			if (imgurl.indexOf('_m.') != -1){	
				imgurl=imgurl.replace('_m.','_b.');
			}
			if(ddh1img.className=='photo_v'){
			}else{
				(function(imgurl){ddh1img.onclick=function(){ZeaiM.piczoom({browser:browser,b:imgurl,list:[imgurl]});}})(imgurl);
			}
		}
	}
	//h1 a
	var ddA=zeai.tag(ddh1,'a');
	if(ddA.length==1){
		var Aobj=ddA[0],href=Aobj.href;
/*		if (href.indexOf('/dating/detail.php') !== -1){
			var hrefarr = href.split('?id=');
			var Aid=hrefarr[hrefarr.length-1];
			Aobj.removeAttribute("href");
			Aobj.onclick=function(){zeai.openurl('../?z=dating&e=detail&a='+Aid);}
		}
*/		
		//约会
		if (href.indexOf('/dating_detail.php?fid=') !== -1){
			var fid=returnId(href,'/dating_detail.php?fid=');
			Aobj.onclick=function(){zeai.openurl('../?z=dating&e=detail&a='+fid);}
		}else if(href.indexOf('/dating/') !== -1){
			var fid=returnId(href,'/dating/');
			Aobj.removeAttribute("href");
			if(zeai.ifint(fid)){
				Aobj.onclick=function(){zeai.openurl('../?z=dating&e=detail&a='+fid);}
			}else{
				Aobj.onclick=function(){zeai.openurl('../?z=dating');}
			}
		}
	}
	//delmy
	if(zeai.tag(dl,'a')[0] && zeai.tag(dl,'a')[0].className=='delmy'){
		zeai.tag(dl,'a')[0].onclick=delmyFn;
	}
	//agree
	var agreeDIV = dd.getElementsByTagName("div")[0].children;
	var agree = agreeDIV[1],chat = agreeDIV[2],agreenum = agreeDIV[3];
	if (agree.className != 'ed'){
		agree.onclick = function (){
			var tid = this.getAttribute("tid");
			zeai.ajax({url:'m1/trend'+zeai.extname,data:{submitok:'ajax_agree',tid:tid}},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){
					agree.className    = 'ed';
					agree.onclick      = function (){}
					agreenum.innerHTML = parseInt(agreenum.innerHTML)+1;
					var img = document.createElement('img');img.src = rs.imgurl;if (!zeai.empty(img.classname))img.className = rs.classname;img.setAttribute('uid',rs.uid);
					if (rs.num == 1){
						var j  = document.createElement('div');j.className = 'j';dd.appendChild(j);
						var em = document.createElement('em');dd.appendChild(em);
					}else{
						var em  = dd.getElementsByTagName("em")[0];
					}
					em.insertBefore(img,em.firstChild);
					agreeHref();
				}else{zeai.msg(rs.msg);}
			});
		}
	}
	//agree list href
	agreeHref();
	function agreeHref(){
		var EM  = dd.getElementsByTagName("em")[0];
		if (!zeai.empty(EM)){
			EM = EM.children;
			for (var m=0;m<EM.length;m++){(function(m){
				EM[m].onclick=function(){
					ZeaiM.page.load('m1/u.php?uid='+EM[m].getAttribute("uid"),ZEAI_MAIN,'u');
				}
			})(m);}
		}
	}
	//chat
	chat.onclick = function (){
		if(!zeai.empty(o('trendbbs'))){o('trendbbs').parentNode.remove();}
		var bbs=zeai.addtag('div');bbs.class('trend_bbsadd');
		var textarea=zeai.addtag('textarea');textarea.class('textarea');textarea.id='trendbbs';textarea.setAttribute("placeholder","请文明发言");
		var btn=zeai.addtag('button');btn.class('btn size2 HONG');btn.type='button';btn.html('评论');
		bbs.append(textarea);bbs.append(btn);
		dd.insertBefore(bbs,chat.parentNode.nextSibling);
		//
		textarea.onblur=function(){
			setTimeout(function(){window.scrollTo(0,0)},200);
		}
		//
		btn.onclick =function (){
			textarea.value = zeai.clearhtml(textarea.value);
			var fid=chat.previousElementSibling.getAttribute("tid");
			zeai.ajax({url:'m1/trend'+zeai.ajxext+'submitok=trend_bbs_add',data:{fid:fid,content:textarea.value}},function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){
					dd.lastChild.insertAdjacentHTML('afterbegin',html_decode(rs.C));
					bbs.remove();
				}
			});
		}
	}
}
//分页
function trendOnscroll(){
	var t = parseInt(o('main').scrollTop);
	var cH= parseInt(o('main').clientHeight);
	var  H= parseInt(o('main').scrollHeight);
	if (H-t-cH <128 && t>100){//t+cH==H
		if (p > totalP){
			o(main).onscroll = null;
			zeai.msg('已达末页，全部加载结束');
		}else{
			var postjson = {submitok:'ajax_ulist',totalP:totalP,p:p};
			//if(!zeai.empty(cs))Object.assign(postjson,tojson(cs.split('&')));
			zeai.ajax({'url':'m1/trend'+zeai.extname,data:postjson},function(e){
			if (e == 'end'){
				zeai.msg(0);zeai.msg('已达末页，全部加载结束');
			}else{
				o('main').append('<div id="p'+p+'">'+e+'</div>');
				var dllist=zeai.tag(o('p'+p),'dl'),l;
				l=dllist.length;
				for(var k=0;k<l;k++){trendSetList_li(dllist[k]);}
				p++;
			}
		});}
	}
	backtopFn(o('main'));
}
topminibox.addEventListener('touchmove', function(e){e.preventDefault();});
nav.addEventListener('touchmove', function(e){e.preventDefault();});
function zeaiplay(zeai_cn) {var v = o('zeaiVbox'+zeai_cn);zeai.msg('视频加载中...');v.play();}

function clearHtml(sTxt) {
	var c=sTxt;
	c = c.replace(/<script.*?>.*?<\/scrip[^>]*>/ig,"");
	c = c.replace(/<[^>]*?javascript:[^>]*>/ig,"");
	c = c.replace(/<style.*?>.*?<\/styl[^>]*>/ig,"");
	c = c.replace(/<(\w[^>]*) style="([^"]*)"([^>]*)/ig, "<$1$3");
	//c = c.replace(/<img.*?src=([^ |>]*)[^>]*>/ig,"<img src=user/$1>");
	c = c.replace(/<\/?(code|h\d)[^>]*>/ig,'<br>');
	c = c.replace(/<\/?(a|sohu|form|input|select|textarea|iframe|SUB|SUP|table|tr|th|td|tbody|module|OPTION|onload|div|center)(\s[^>]*)?>/ig,"");
	c = c.replace(/<\?xml[^>]*>/ig,'');
	c = c.replace(/<\!--.*?-->/ig,'');
	c = c.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onclick="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onclick=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onerror="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onload="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onmouseover="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<\\?\?xml[^>]*>/ig, "");
	c = c.replace(/<\/?\w+:[^>]*>/ig, "");
	c = c.replace(/<a.*?href="([^"]*)"[^>]*>/ig,"<a href=\"$1\">");
	c=c.replace(/<br>/g,"");
	c=c.replace(/\'/g,"");
	c=c.replace(/\"/g,"");
	c=c.replace(/\r/g,"");
	c=c.replace(/\n/g,""); 
	sTxt = c;
	return sTxt;
}
function trend_uFn(uid){ZeaiM.page.load('m1/u.php?uid='+uid,ZEAI_MAIN,'u');}
function returnId(href,str){
	var hrefarr = href.split(str);
	var newhref=hrefarr[hrefarr.length-1];
	newhref=newhref.replace('/','');
	var id=newhref.replace('.html','');
	return id;
}	

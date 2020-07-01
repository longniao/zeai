/*
* Copyright (C) Zeai.cn V6.0 All rights reserved.
* E-Mail：supdes@qq.com
* Url http://www.zeai.cn
*     http://www.yzlove.com
*/
var load8='<img src='+HOST+'/res/load8.gif class="load8">',PCHOST=HOST+'/p1';
var ZeaiPC={
	day:function(Y,M){
		M=M-1;
		M=(M>12 || M<0)?1:M;
		var baseMonthsDay = [31,28,31,30,31,30,31,31,30,31,30,31];
		var thisMonthDays = [];
		function isRunYear(fullYear){
			return (fullYear % 4 == 0 && (fullYear % 100 != 0 || fullYear % 400 == 0));
		}
		function getThisMonthDays(days){
			var arr = [];
			for(var i=1;i <= days;i++){arr.push(i);}
			return arr;
		}
		if(isRunYear(Y) && M == 1){
			thisMonthDays = getThisMonthDays(baseMonthsDay[M] + 1);
		}else{
			thisMonthDays = getThisMonthDays(baseMonthsDay[M]);
		}
		return thisMonthDays;
	},
	piczoom:function(url){
		if (url==0){pz_close_fn();return false;}
		var pzM = zeai.addtag('div');pzM.class('piczoom mask alpha0_100');
		var pzClose=zeai.addtag('div');pzClose.className='closeFadeDown close';pzClose.html('<i class="ico">&#xe604;</i>');pzClose.title='关闭';
		var pzPIC = zeai.addtag('img');pzPIC.src=url;pzPIC.className='fadeInUp';pzPIC.style.cursor='zoom-out';
		pzPIC.onload=function(){
			pzM.append(pzPIC);
			pzM.onclick=function(){pz_close_fn();}
			document.body.append(pzM);
			var w=pzPIC.offsetWidth,h=pzPIC.offsetHeight;
			iL = parseInt(zeai.bodyW()/2 - w/2);
			iT = parseInt(zeai.bodyH()/2 - h/2);
			pzPIC.style.left = parseInt(iL)+'px';
			pzPIC.style.top  = parseInt(iT)+'px';
			//
			pzM.append(pzClose);
			pzClose.style.left = parseInt(iL+w-30)+'px';
			pzClose.style.top  = parseInt(iT-30)+'px';
		}
		function pz_close_fn(){
			pzM.removeClass('alpha0_100');pzM.addClass('alpha100_0');
			pzPIC.removeClass('fadeInUp');pzPIC.addClass('fadeInDown');
			pzClose.removeClass('closeFadeDown');pzClose.addClass('closeFadeUp');
			setTimeout(function(){
				if (!zeai.empty(pzM)){pzM.remove();}
			},200);
		}
	},
	iframe:function(json){
		if (json==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		//var idstr=json.obj.id;
		var H=zeai.bodyH(),W=zeai.bodyW();
		var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(W - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(H - 30):json.h;
		var div_mask  = zeai.addtag('div');div_mask.class('mask1 alpha0_100');
		var div_box = zeai.addtag('div');div_box.class('small_big div_box2');
		var div_close=zeai.addtag('div');div_close.className='div_close2';div_close.html('<i class="ico">&#xe65b;</i>');div_close.title='关闭';
		var iframe = zeai.addtag('iframe');iframe.className='div_ifrmae';
		iframe.src = json.url;
		iframe.frameBorder=0;iframe.style.backgroundColor="#fff";
		iframe.style.width  = width+'px';
		iframe.style.height = height+'px';
		div_box.append(div_close);div_box.append(iframe);div_mask.append(div_box);
		div_mask.onclick = function(){div_close_fn();}
		div_box.onclick = function(e){e.cancelBubble = true;}
		div_close.onclick = function(){div_close_fn();}
		var L=parseInt((W-width)/2),T=parseInt((H-height)/2);
		T=(T<0)?0:T;
		div_box.style.width  = width+'px';div_box.style.height = height+'px';div_box.style.left = L + 'px';
		div_box.style.top  = T + 'px';
		div_mask.show();
		document.body.append(div_mask);
		document.documentElement.style.overflowY = 'hidden';
			root.style.overflow = 'hidden';
			root.style.borderRight = widthBar +'px solid transparent';
		function div_close_fn(){
			div_box.removeClass('small_big');div_box.addClass('big_small');div_mask.removeClass('alpha0_100');div_mask.addClass('alpha100_0');
			setTimeout(function(){div_mask.remove();},200);
			document.documentElement.style.overflowY = 'scroll';
			root.style.overflow = '';
			root.style.borderRight = '';			
		}
		return div_close;
	},
	chat:function(uid){
		zeai.ajax({js:1,url:PCHOST+'/chat'+zeai.ajxext+'uid='+uid+'&submitok=ajax_ifchat'},function(e){rs=zeai.jsoneval(e);
			switch (rs.flag) {
				case 'nolevel':
					zeai.alertplus({'title':'请升级会员','content':rs.msg,'title1':'取消','title2':'去升级','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);zeai.openurl(PCHOST+'/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));}
					});
				break;
				case '1':ZeaiPC.iframe({url:PCHOST+'/chat'+zeai.ajxext+'uid='+uid,w:850,h:550});break;
				case 'nodata':
					zeai.alertplus({'title':'----- 请完善个人资料 -----','content':rs.msg,'title1':'取消','title2':'去完善','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_info'+zeai.extname);}
					});
				break;
				case 'nophoto':
					zeai.alertplus({'title':'----- 请先上传头像 -----','content':rs.msg,'title1':'取消','title2':'去上传','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_info'+zeai.extname);}
					});
				break;
				case 'nocert':
					zeai.alertplus({'title':'-------- 诚信认证 --------','content':rs.msg,'title1':'取消','title2':'去认证','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_cert'+zeai.extname);}
					});
				break;
				default:zeai.msg(rs.msg);break;
			}
		});
	},
	no:function(rs){
		switch (rs.flag) {
			case 'nolevel':
				zeai.alertplus({'title':'请升级会员','content':rs.msg,'title1':'取消','title2':'去升级','fn1':function(){zeai.alertplus(0);},
					'fn2':function(){zeai.alertplus(0);zeai.openurl(PCHOST+'/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));}
				});
			break;
			case '1':ZeaiPC.iframe({url:PCHOST+'/chat'+zeai.ajxext+'uid='+uid,w:850,h:550});break;
			case 'nodata':
				zeai.alertplus({'title':'----- 请完善个人资料 -----','content':rs.msg,'title1':'取消','title2':'去完善','fn1':function(){zeai.alertplus(0);},
					'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_info'+zeai.extname);}
				});
			break;
			case 'nophoto':
				zeai.alertplus({'title':'----- 请先上传头像 -----','content':rs.msg,'title1':'取消','title2':'去上传','fn1':function(){zeai.alertplus(0);},
					'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_info'+zeai.extname);}
				});
			break;
			case 'nocert':
				zeai.alertplus({'title':'-------- 诚信认证 --------','content':rs.msg,'title1':'取消','title2':'去认证','fn1':function(){zeai.alertplus(0);},
					'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_cert'+zeai.extname);}
				});
			break;
			default:zeai.msg(rs.msg);break;
		}
	},
	hi:function(json){
		var uid=json.uid,hi=json.btnobj,edstr=json.edstr;
		zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_senddzh',uid:uid}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				hi.addClass('ed');hi.html(edstr);hi.onclick=null;
				tips0_100_0.html('<i class="ico hi">&#xe6bd;</i>招呼已发送');tips0_100_0.show();setTimeout(function(){tips0_100_0.hide()},2100);
			}else if(rs.flag=='nodata'){
				nodata();
			}else{
				zeai.msg(rs.msg);
			}
		});
	},
	tabmenu:{
		init:function(json){
			var showbox=json.showbox;
			var elem=zeai.tag(json.obj,'li');
			var ZminiEtabAmenuI_cn=zeai.tag(json.obj,'b')[0];
			zeai.listEach(elem,function(li){
				if (li.hasClass('ed')){
					var span = li.getElementsByTagName("span")[0];
					var iW = span.offsetWidth;
					var iL = span.offsetLeft;
					ZminiEtabAmenuI_cn.css('width:'+iW+'px;transform:translate('+iL+'px)');
				}
				var url=li.getAttribute("data");
				if (!zeai.empty(url)){
					li.onclick = function(){
						ZeaiPC.tabmenu.onclk({obj:json.obj,li:li,kind:json.kind});
						if (!zeai.empty(showbox)){
							var url=this.getAttribute("data");
							zeai.ajax({'url':url},function(e){
								//ZeaiM.dojsFunc(e,function(e){o(showbox).html(e);ZeaiM.eval(e);});
								o(showbox).html(e);
							});
						}
						if(!zeai.empty(json.click))json.click(li);
					}
				}
			});
		},
		onclk:function(json){
			var ZminiEtabAmenuI_cn=zeai.tag(json.obj,'b')[0];
			zeai.listEach(zeai.tag(json.obj,'li'),function(li){li.removeClass('ed');});
			json.li.addClass('ed');
			var span = json.li.getElementsByTagName("span")[0],iL,iW;
			if(json.kind=='block'){
				iL = json.li.offsetLeft;
				iW = json.li.offsetWidth;
			}else{
				iL = span.offsetLeft;
				iW = span.offsetWidth;
				
				console.log(iL);
			}
			ZminiEtabAmenuI_cn.css('width:'+iW+'px;transform:translate('+iL+'px)');
		}
	},
	div:function(json){
		if (json==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		//var idstr=json.obj.id;
		var H=zeai.bodyH(),W=zeai.bodyW();
		var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(W - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(H - 30):json.h;
		var div_mask  = zeai.addtag('div');div_mask.class('mask1 alpha0_100');
		var div_box = zeai.addtag('div');div_box.class('small_big div_box2');
		var div_close=zeai.addtag('div');div_close.className='div_close2';div_close.html('<i class="ico">&#xe65b;</i>');div_close.title='关闭';
		div_box.append(div_close);div_box.append(json.obj);div_mask.append(div_box);
		div_mask.onclick = function(){div_close_fn();}
		div_box.onclick = function(e){e.cancelBubble = true;}
		div_close.onclick = function(){div_close_fn();}
		var L=parseInt((W-width)/2),T=parseInt((H-height)/2);
		T=(T<0)?0:T;
		div_box.style.width  = width+'px';div_box.style.height = height+'px';div_box.style.left = L + 'px';
		div_box.style.top  = T + 'px';
		div_mask.show();
		document.body.append(div_mask);json.obj.show();
		div_mask.addEventListener("contextmenu", function(event){event.preventDefault();});
		function div_close_fn(){
			var zeai_CN_vvid=json.obj.id;
			if(zeai_CN_vvid.indexOf("zeaiVbox") != -1){json.obj.pause();}
			if (typeof(json.fn) == "function"){json.fn();}
			document.body.appendChild(json.obj);json.obj.hide();
			div_box.removeClass('small_big');div_box.addClass('big_small');div_mask.removeClass('alpha0_100');div_mask.addClass('alpha100_0');
			setTimeout(function(){div_mask.remove();},200);
		}
		return div_close;
	}
}
function photoUp(json) {
	var btnobj=json.btnobj;
	if(!zeai.empty(o(btnobj))){
		btnobj.onclick=function(){up();}
	}else{up()}
	function up(){	
		zeai.up({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitok,"ajaxLoading":0,"multiple":json.multiple,
		"fn":function(e){var rs=zeai.jsoneval(e);json._(rs);},
		"fnli":function(e){var rs=zeai.jsoneval(e);json.li(rs);}
		});
	}
}
function gift_ajaxdata(gid,box,uid){
	zeai.ajax({js:1,url:PCHOST+'/u'+zeai.extname,data:{submitok:'ajax_gift_div',gid:gid,uid:uid}},function(e){rs=zeai.jsoneval(e);
		//if(rs.flag=='nologin'){zeai.openurl('m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}
		var div=zeai.div({obj:box,title:'送礼物给【'+rs.nickname+'】',w:300,h:290});
		var em=zeai.tag(box,'em')[0];em=em.children;
		em[0].src       = rs.picurl;
		em[1].innerHTML = rs.title;
		em[2].innerHTML = rs.price + lovebstr;
		var a=zeai.tag(box,'a'),tipbox;
		if(box.id=='box_gift_index'){
			a[0].onclick = function (){div.click();}
			tipbox=tips0_100_02;
		}else{
			a[0].onclick = function (){div.click();ZeaiPC.iframe({url:HOST+'/p1/gift'+zeai.ajxext+'uid='+uid,w:600,h:500});}
			tipbox=tips0_100_0;
		}
		a[1].onclick = function (){
			zeai.ajax({url:PCHOST+'/u'+zeai.extname,data:{submitok:'ajax_gift_send',gid:rs.id,uid:uid}},function(e2){rs2=zeai.jsoneval(e2);
				if(rs2.flag==1){
					div.click();
					tipbox.html('<i class="ico hi">&#xe69a;</i>礼物已送出');tipbox.show();setTimeout(function(){tipbox.hide()},2100);
					if(!zeai.empty(rs2.C) && !zeai.empty(o('gift'))){
						o('gift').html('<li gid="0" uid="'+uid+'"><i class="ico">&#xe69a;</i></li>');
						o('gift').append(rs2.C);
						setgift(o('gift'),box_gift,uid);
					}
				}else if(rs2.flag=='noloveb'){
					zeai.alertplus({title:rs2.title,content:rs2.msg,title1:'取消','title2':'去充值','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);
						zeai.openurl_(PCHOST+'/my_loveb'+zeai.ajxext+'a=cz&jumpurl='+encodeURIComponent(rs2.jumpurl));
						}
					});
				}else{
					zeai.msg(rs2.msg);
				}
			});
		}
	});
}
function nodata(){
	zeai.alertplus({'title':'请完善个人资料','content':rs.msg,'title1':'取消','title2':'去完善','fn1':function(){zeai.alertplus(0);},
		'fn2':function(){zeai.alertplus(0);zeai.openurl(PCHOST+'/my_info'+zeai.extname);}
	});
}
function setgift(giftbtn,box,uid){
	if(!zeai.empty(giftbtn)){giftbtn.onclick = function(){gift_ajaxdata(0,box,uid);}}
	var li,id,rs,gid,div;
	zeai.listEach(zeai.tag(gift,'li'),function(li){li.onclick = function(){gift_ajaxdata(li.getAttribute("gid"),box,uid);}});
}


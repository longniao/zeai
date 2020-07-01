/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2020/03/13 by supdes
*/
var ScrollTop;
var ZeaiM={
	piczoom:function(url){
		if (url==0){pz_close_fn();return false;}
		if(zeai.ifjson(url)){
			var json=url;
			if (json.browser=='wx'){
				wx.previewImage({current:json.b,urls:json.list});
			}else{
				ZeaiM.piczoom(json.b);
			}
			return;
		}
		var pzM = zeai.addtag('div');pzM.class('piczoom mask alpha0_100');
		var pzPIC = zeai.addtag('img');pzPIC.src=url;pzPIC.className='fadeInUp';
		pzPIC.onload=function(){
			pzM.append(pzPIC);
			pzM.onclick=function(){pz_close_fn();}
			document.body.append(pzM);
			var w=pzPIC.offsetWidth,h=pzPIC.offsetHeight;
			iL = parseInt(zeai.bodyW()/2 - w/2);
			iT = parseInt(zeai.bodyH()/2 - h/2);
			pzPIC.style.left = parseInt(iL)+'px';
			pzPIC.style.left = 0+'px';
			pzPIC.style.top  = parseInt(iT)+'px';
		}
		function pz_close_fn(){
			pzM.removeClass('alpha0_100');pzM.addClass('alpha100_0');
			pzPIC.removeClass('fadeInUp');pzPIC.addClass('fadeInDown');
			setTimeout(function(){
				if (!zeai.empty(pzM)){pzM.remove();}
			},150);
		}
	},
	fade:function(json){
		if (!zeai.empty(json.arr)){
		for(var pg=0;pg<json.arr.length;pg++){
			(function(pg){
				if (zeai.ifint(json.delay)){setTimeout(set,json.delay);}else{set();}
				function set(){o(json.arr[pg]).css('transform:translate('+json.num+')');}
			})(pg);
		}}
	},
	confirmUp:function(json){
		var confirm_mask  = zeai.addtag('div');
		var confirm_box   = zeai.addtag('div');
		var confirm_title = zeai.addtag('div');
		var confirm_ok     = zeai.addtag('div');
		var confirm_cancel = zeai.addtag('div');
		confirm_mask.id  = 'confirmUp_mask';confirm_mask.className = 'mask alpha0_100';
		confirm_box.className = 'confirmUp confirmUpAnm1';
		confirm_cancel.html(json.cancel);
		confirm_mask.onclick = function(){close();}
		confirm_box.onclick = function(e){e.cancelBubble = true;}
		confirm_ok.onclick = function(){confirm_mask.hide();
			confirm_mask.remove();
			if (typeof(json.okfn) == "function"){json.okfn();}else if(typeof(json.okfn) == "string"){zeai.openurl(json.okfn);
			}else if(typeof(okfn) == "object"){json.okfn.submit();}
		}
		confirm_cancel.onclick = function(){close();}
		confirm_box.appendChild(confirm_title);
		confirm_box.appendChild(confirm_ok);confirm_box.appendChild(confirm_cancel);
		confirm_mask.appendChild(confirm_box);
		document.body.appendChild(confirm_mask);
		confirm_title.html(json.title);
		confirm_ok.html(json.ok);
		confirm_mask.show();
		function close(){
			confirm_box.removeClass('confirmUpAnm1');confirm_box.addClass('confirmUpAnm2');confirm_mask.removeClass('alpha0_100');confirm_mask.addClass('alpha100_0');
			setTimeout(function(){confirm_mask.remove();},400);
		}
	},
	div_up:function(json){
		if (json==0){close();return false;}
		if (typeof(json) != "object")return false;
		var fobj=json.fobj/*,vname=json.obj.id*/;
		var H=zeai.bodyH(),W=zeai.bodyW();
		//var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(zeai.bodyW() - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(zeai.bodyH() - 30):json.h;
		var div_up_mask  = zeai.addtag('div');
		var div_up_box   = zeai.addtag('div');
		var div_up_close = zeai.addtag('div');div_up_close.html('<i class="ico">&#xe65b;</i>');div_up_close.className = 'div_up_close div_upAnm1';div_up_close.id='div_up_close';
		div_up_mask.id  = 'div_up_mask';div_up_mask.className = 'mask1 alpha0_100';
		div_up_box.className = 'div_up_box div_upAnm1';
		div_up_mask.onclick = function(){close();}
		div_up_box.onclick = function(e){e.cancelBubble = true;}
		div_up_box.appendChild(json.obj);json.obj.show();
		div_up_mask.appendChild(div_up_close);
		div_up_box.style.height = height+'px';
		div_up_close.style.bottom =  parseInt(height+10)+'px';
		if (typeof(fobj) == "object"){
			div_up_mask.append(div_up_box);
			fobj.append(div_up_mask);
		}else{
			div_up_mask.append(div_up_box);
			document.body.append(div_up_mask);
		}
		div_up_mask.show();
		div_up_close.onclick=function(){close()}
		div_up_mask.addEventListener('touchmove',function(e) {e.preventDefault();});
		function close(){
			if(zeai.empty(json.removeobj)){
				if (typeof(fobj) == "object"){
					fobj.appendChild(json.obj);
				}else{
					document.body.append(json.obj);
				}
				json.obj.hide();
			}
			div_up_box.removeClass('div_upAnm1');div_up_box.addClass('div_upAnm2');
			div_up_close.removeClass('div_upAnm1');div_up_close.addClass('div_upAnm2');
			setTimeout(function(){div_up_mask.removeClass('alpha0_100');div_up_mask.addClass('alpha100_0');},100);
			setTimeout(function(){div_up_mask.remove();},300);
		}
	},
	div:function(json){
		if (json==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		var fobj=json.fobj/*,vname=json.obj.id*/;
		var H=zeai.bodyH(),W=zeai.bodyW();
		var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(zeai.bodyW() - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(zeai.bodyH() - 30):json.h;
		var div_mask  = zeai.addtag('div');div_mask.class('mask1 alpha0_100');/*div_mask.id = 'div_mask'+vname;*/
		if (typeof(fobj) == "object")H=H+fobj.scrollTop*2;
		
		var div_boxm   = zeai.addtag('div');div_boxm.class('big_normal');div_boxm.id = 'div_boxm';
		var div_closem = zeai.addtag('div');div_closem.class('div_closem');div_closem.id='div_closem';
		var i = zeai.addtag('i');div_closem.appendChild(i)
		
		div_mask.onclick = function(){div_closem_fn();}
		div_closem.onclick = function(){div_closem_fn();}
		div_boxm.appendChild(json.obj);json.obj.show();
		div_boxm.append(div_closem);div_mask.append(div_boxm);
		div_boxm.style.width  = width+'px';
		div_boxm.style.height = height+'px';
		div_boxm.style.left = parseInt((W-width)/2) + 'px';
		div_boxm.style.top = parseInt((H-height)/2-30) + 'px';
		div_mask.show();
		
		div_boxm.onclick = function(e){e.cancelBubble = true;}
		div_boxm.addEventListener('touchmove',function(e) {e.cancelBubble = true;});
		div_mask.addEventListener('touchmove',function(e) {e.preventDefault();});
		if (typeof(fobj) == "object"){
			div_mask.style.height = fobj.scrollHeight+'px';
			fobj.append(div_mask);
		}else{
			document.body.append(div_mask);
		}
		function div_closem_fn(){
			if(zeai.empty(json.removeobj)){
				if (typeof(fobj) == "object"){
					fobj.appendChild(json.obj);
				}else{
					document.body.appendChild(json.obj);
				}
				json.obj.hide();
			}
			div_boxm.class('big_small');div_mask.removeClass('alpha0_100');div_mask.addClass('alpha100_0');
			setTimeout(function(){div_mask.remove();},200);
		}
		return div_closem;
	},
	div_pic:function (json){
		if (json==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		var H=zeai.bodyH(),W=zeai.bodyW();
		var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(W - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(H - 30):json.h;
		var div_mask  = zeai.addtag('div');div_mask.class('mask1 alpha0_100');
		var div_box = zeai.addtag('div');div_box.class('bounce div_pic');
		var div_close=zeai.addtag('div');div_close.className='div_pic_close';div_close.html('<i class="ico">&#xe65b;</i>');div_close.title='关闭';
		div_box.append(div_close);div_box.append(json.obj.innerHTML);div_mask.append(div_box);
		div_mask.onclick = function(){div_close_fn();}
		div_box.onclick = function(e){e.cancelBubble = true;}
		div_close.onclick = function(){div_close_fn();}
		var L=parseInt((W-width)/2),T=parseInt((H-height)/2);
		T=(T<0)?0:T;
		div_box.style.width  = width+'px';div_box.style.height = height+'px';
		div_box.style.left = L + 'px';
		div_box.style.top  = T + 'px';
		div_mask.show();
		document.body.append(div_mask);
		function div_close_fn(){
			if (typeof(json.fn) == "function"){json.fn();}
			div_box.removeClass('small_big');div_box.addClass('big_small');div_mask.removeClass('alpha0_100');div_mask.addClass('alpha100_0');
			setTimeout(function(){if(!zeai.empty(o(div_mask))){o(div_mask).remove();}},200);
		}
		return div_close;
	},
	up_wx:function(json){
		var multiple=(!zeai.ifint(json.multiple) || zeai.empty(json.multiple))?1:json.multiple;
		wx.chooseImage({
			count:multiple,
			sizeType:['compressed'],
			success: function (res) {
				var localIds = res.localIds,serverIds=[];
				var i = 0, length = localIds.length;
				function wxupload() {
					wx.uploadImage({
					localId:localIds[i],
					isShowProgressTips:1,
					success: function (res) {
						i++;serverIds.push(res.serverId);
						zeai.msg(0);
						zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在上传...　'+i+' / '+length,{time:99})
						if (i < length) {
							setTimeout(wxupload,300);
						}else{
							zeai.msg(0);zeai.msg('正在保存中...',{time:30});
							var postjson = {submitok:json.submitok,serverIds:serverIds};
							zeai.ajax({"url":json.url,"ajaxLoading":json.ajaxLoading,"data":postjson},function(e){if(typeof(json.fn)=="function")json.fn(e);});
						}
					},fail: function (res) {alert(JSON.stringify(res));}});
				}
				wxupload();
			}
		});
	},
	up_wx_tmp:function(json){
		var multiple=(!zeai.ifint(json.multiple) || zeai.empty(json.multiple))?1:json.multiple;
		wx.chooseImage({
			count:multiple,
			sizeType:['compressed'],
			success: function (res) {
				var localIds = res.localIds,serverIds=[];
				var i = 0, length = localIds.length;
				function wxupload() {
					wx.uploadImage({
					localId:localIds[i],
					isShowProgressTips:1,
					success: function (res) {
						
						if(typeof(json.fnli)=="function")json.fnli([localIds[i],res.serverId]);
						
						i++;serverIds.push(res.serverId);
						zeai.msg(0);
						zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在上传...　'+i+' / '+length,{time:99})
						if (i < length) {
							setTimeout(wxupload,300);
						}else{
							zeai.msg(0);zeai.msg('正在保存中...',{time:30});
							//var postjson = {submitok:json.submitok,serverIds:serverIds};
							
							if(typeof(json.fn)=="function")json.fn(serverIds);
							
						}
					},fail: function (res) {alert(JSON.stringify(res));}});
				}
				wxupload();
			}
		});
	},
	divBtmMod:function(json){
		ScrollTop=zeai.getScrollTop();
		if (json.flag==0 || json==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		var M = zeai.addtag('div');M.id = 'divBtmMod';M.class('mask alpha0_100');
		var obj = zeai.addtag('div');obj.class('divBtmMod fadeInUp');obj.id='divBtmMod_obj';
		var em  = zeai.addtag('em');
		var h3  = zeai.addtag('h3');h3.html(json.title);
		var cancel = zeai.addtag('button');cancel.type='button';cancel.class('divBtmCancel');cancel.html('取消');
		var form  = zeai.addtag('div');form.class('form');
		if (json.kind=='checkbox'){
			checkbox_div_list_create(json.objstr,json.value,eval(json.objstr+'_ARR'),form);
		}else if(json.kind=='radio'){
			radio_div_list_create(json.objstr,json.value,eval(json.objstr+'_ARR'),form);
		}else if(json.kind=='rz_mob'){
			var placeholder = json.title+'号码';
			var input = zeai.addtag('input');input.pattern='[0-9]*';input.id='input_mob';input.type='text';input.placeholder='请输入'+placeholder,input.maxLength=11;input.value=json.value;input.class('rz_mob_input');
			form.append(input);
			var verifybox = zeai.addtag('div');verifybox.class('verifybox');
			var verify = zeai.addtag('input');verify.pattern='[0-9]*';verify.type='text';verify.placeholder='请输入短信验证码',verify.id='verify';verify.maxLength=4;verify.class('rz_mob_verify');
			var yzmbtn = zeai.addtag('a');yzmbtn.class('yzmbtn');yzmbtn.id='yzmbtn';yzmbtn.html('获取验证码');
			verifybox.append(verify);verifybox.append(yzmbtn);form.append(verifybox);
			zeai.verify({mob:input,verify:verify,yzmbtn:yzmbtn,url:json.url+'&submitok=ajax_Zeai_mob_verify_get',sec:120});
		}else if(json.kind=='textarea'){
			var input = zeai.addtag('textarea');input.type='textarea';input.class('textarea');input.placeholder='请输入'+json.title+'（10~1000字）',input.id='divBtmC';input.value=json.value;form.append(input);
		}else{
			var placeholder = (!zeai.empty(json.placeholder))?json.placeholder:'请输入'+json.title;
			var maxLength=(!zeai.empty(json.maxLength))?json.maxLength:50;
			switch (json.objstr) {
				case 'mob':maxLength=11;break;
				case 'truename':maxLength=12;break;
				case 'identitynum':maxLength=18;break;
				case 'aboutus':maxLength=1000;break;
			}
			var input = zeai.addtag('input');input.type='text';input.placeholder=placeholder,input.maxLength=maxLength;input.id='divBtmC';input.value=json.value;form.append(input);
		}
		var save = zeai.addtag('button');save.html('确定');save.type='button';save.class('divBtmSave');
		em.append(h3);em.append(cancel);form.append(save);obj.append(em);obj.append(form);
		obj.onclick = function(e){e.cancelBubble = true;}
 		save.onclick=function(){
			if(typeof(json.fn) == "function"){
				if (json.kind=='checkbox'){
					json.fn(zeai.form.checkbox_div_list_get(json.objstr+'[]'));
				}else if(json.kind=='radio'){
					var rc=json.fn(radio_div_list_get_id(json.objstr));
					if(!zeai.empty(rc) && rc.flag==0){zeai.msg(rc.msg);return false;}
				}else{
					if(json.kind=='rz_mob'){
						var rc=json.fn({mob:input.value,verify:verify.value});
						if(!zeai.empty(rc) && rc.flag==0)zeai.msg(rc.msg);return false;
					}else{
						switch (json.objstr) {
							case 'mob':if(!zeai.ifmob(input.value)){zeai.msg('请输入正确的【手机号码】');return false;}break;
							case 'identitynum':if(!zeai.ifsfz(input.value)){zeai.msg('请输入正确的【身份证号码】');return false;}break;
							case 'aboutus':if(zeai.str_len(input.value)>1000){zeai.msg('【自我介绍】字太多请控制在10~1000');return false;}break;
						}
						if(zeai.empty(input.value)){
							zeai.msg('请输入【'+json.title+'】');return false;
						}
						json.fn(input.value);
					}
				}
				div_close_fn();
			}
		}
 		cancel.onclick=function(){div_close_fn();}
 		M.onclick=function(){div_close_fn();}
		if (json.kind=='input' ||json.kind=='textarea' || zeai.empty(json.kind)){
			input.onblur=function(){
				if(typeof(json.fn) == "function"){zeai.setScrollTop(ScrollTop);}
			}
			setTimeout(function(){o('divBtmC').focus();},300);
		}
		M.append(obj);M.show();
		M.addEventListener('touchmove',function(e) {e.preventDefault();});
		if (typeof(fobj) == "object"){
			M.style.height = fobj.scrollHeight+'px';
			fobj.append(M);
		}else{
			document.body.append(M);
		}
		function div_close_fn(){
			o('divBtmMod').removeClass('alpha0_100');o('divBtmMod').addClass('alpha100_0');
			divBtmMod_obj.removeClass('fadeInUp');divBtmMod_obj.addClass('fadeInDown');
			setTimeout(function(){
				if (!zeai.empty(o('divBtmMod'))){
					o('divBtmMod').remove();
				}
			},150);
			zeai.setScrollTop(ScrollTop);
		}
	},
	divMod:function(kind,obj,url){
		var objstr=obj.id;
		obj.onclick = function(){
			var span = obj.getElementsByTagName("span")[0];
			var h4   = obj.getElementsByTagName("dt")[0];
			var defV = obj.getAttribute("data"),title=h4.innerHTML;
			title = title.replace(/<b.*?>.*?<\/b[^>]*>/ig,"");title = title.replace(/　/ig,'');
			switch (kind) {
				case 'input':
					ZeaiM.divBtmMod({objstr:objstr,title:title,value:defV,fn:function(inputV){
						inputV=zeai.clearhtmlall(inputV);
						span.html(inputV);span.class('ed');obj.setAttribute("data",inputV)
						FORM[objstr]=inputV;
					}});
				break;
				case 'textarea':
					ZeaiM.divBtmMod({objstr:objstr,title:title,value:defV,kind:'textarea',fn:function(inputV){
						inputV=zeai.clearhtmlall(inputV);
						span.html(inputV);span.class('ed');obj.setAttribute("data",inputV)
						FORM[objstr]=inputV;
					}});
				break;
				case 'range':
					ios_select2_range(title,eval(objstr+'_ARR1'),eval(objstr+'_ARR2'),defV,function(obj1,obj2){
						var i,v,i1=obj1.i,i2=obj2.i,v1=obj1.v,v2 = obj2.v;
						if (parseInt(i1) > parseInt(i2) && parseInt(i2)!= 0){i=i1,i1=i2,i2=i;v=v1,v1=v2,v2=v;}
						var list  = i1 + ',' + i2,title = v1 + '～' + v2;
						zeai.ajax({'url':url,'data':{"submitok":'ajax_'+objstr,"i1":i1,"i2":i2}},function(e){rs=zeai.jsoneval(e);
							if (rs.flag==1){span.html(title);obj.setAttribute("data",list)}
							//zeai.msg(rs.msg,{"time":0.5});
						});
					},',');
				break;
				case 'birthday':
					ios_select_area(title,yearData, monthData, dateData,defV,function(obj1,obj2,obj3){
						var birthday = obj1.i + '-' + obj2.i + '-' + obj3.i;
						span.html(birthday);span.class('ed');obj.setAttribute("data",birthday);
						FORM[objstr]=birthday;
					},'-');
				break;
				case 'select':
					ios_select1_normal(title,eval(objstr+'_ARR'),defV,function(obj1){
						var sid = obj1.i,sv=obj1.v;
						span.html(sv);span.class('ed');obj.setAttribute("data",sid);
						FORM[objstr]=sid;
					},',');
				break;
				case 'checkbox':
					ZeaiM.divBtmMod({"objstr":objstr,"title":title,"value":defV,"kind":"checkbox",fn:function(e){
						span.html(checkbox_div_list_get_listTitle(objstr,e));span.class('ed');obj.setAttribute("data",e);
						FORM[objstr]=e;
					}});
				break;
				case 'radio':
					ZeaiM.divBtmMod({objstr:objstr,title:title,value:defV,kind:'radio',fn:function(e){
						if(!zeai.ifint(e)){return {flag:0,msg:'请选择【'+title+'】'};}
						span.html(radio_div_list_get_title(e,eval(objstr + '_ARR')));span.class('ed');obj.setAttribute("data",e);
						FORM[objstr]=e;
					}});
				break;
				case 'rz_mob':
					ZeaiM.divBtmMod({objstr:objstr,title:title,value:defV,kind:'rz_mob',url:url,fn:function(e){
						if(!zeai.ifmob(e.mob)){return {flag:0,msg:'请输入正确的【手机号码】'};}
						if(!zeai.ifint(e.verify) || zeai.str_len(e.verify)!=4 ){return {flag:0,msg:'请输入正确的【短信验证码】'};}
						zeai.ajax({url:url+'&submitok=ajax_Zeai_mob_verify_chk',data:{mob:e.mob,verify:e.verify}},function(s){var rs=zeai.jsoneval(s);
							if(rs.flag==1){ZeaiM.divBtmMod(0);span.html(e.mob);span.class('ed');obj.setAttribute("data",e.mob);FORM[objstr]=e.mob;}else{zeai.msg(rs.msg);}
						});
					}});
				break;
			}	
		}
	}
	
}

function photoUp(json) {
	var btnobj=json.btnobj;
	if(!zeai.empty(o(btnobj))){
		btnobj.onclick=function(){up();}
	}else{up()}
	function up(){	
		if(browser=='h5'){
			zeai.up({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitokBef+"up_h5","ajaxLoading":0,"multiple":json.multiple,
			"fn":function(e){var rs=zeai.jsoneval(e);json.end(rs);},
			"li":function(e){var rs=zeai.jsoneval(e);json.li(rs);}
			});
		}else if(browser=='wx'){
			if(json.wxtmp){
				ZeaiM.up_wx_tmp({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitokBef+"up_wx","ajaxLoading":0,"multiple":json.multiple,
				"fn":function(e){json.end(e);},
				"fnli":function(e){json.li(e);}
				});
			}else{
				ZeaiM.up_wx({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitokBef+"up_wx","ajaxLoading":0,"multiple":json.multiple,
				"fn":function(e){var rs=zeai.jsoneval(e);json.end(rs);}
				});
			}
		}
	}
}

function backtopFn(obj){
    var t = (zeai.empty(obj))?zeai.getScrollTop():obj.scrollTop;
	var backtop=o('backtop'); 
    if(t<88){
		if (backtop.style.display == 'block' && backtop.hasClass('fadeInUp')){
			backtop.class('big_small ');
			setTimeout(backtop_close,200);
			function backtop_close(){backtop.hide();}				
		}
	}else{
		if (backtop.style.display == 'none' || backtop.style.display == ''){
			backtop.class('fadeInUp');
			backtop.show()
		}
	}
	if(zeai.empty(obj)){
		btmTopBtn.onclick=function(){zeai.setScrollTop(0);}
	}else{
		btmTopBtn.onclick=function(){obj.scrollTop=0;}
	}
}
function ifX(){
	var isIPhoneX = /iphone/gi.test(window.navigator.userAgent) && window.devicePixelRatio && window.devicePixelRatio === 3 && window.screen.width === 375 && window.screen.height === 812;
	var isIPhoneXSMax = /iphone/gi.test(window.navigator.userAgent) && window.devicePixelRatio && window.devicePixelRatio === 3 && window.screen.width === 414 && window.screen.height === 896;
	var isIPhoneXR = /iphone/gi.test(window.navigator.userAgent) && window.devicePixelRatio && window.devicePixelRatio === 2 && window.screen.width === 414 && window.screen.height === 896;
	if(isIPhoneX || isIPhoneXSMax || isIPhoneXR){return true;}else{return false;}
}
function mobkind(){
	var u = navigator.userAgent,app = navigator.appVersion;
	var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1;
	var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
	if(isAndroid){
		return 'android';
	}else if(isiOS){
		return 'ios';
	}
}
function is_h5app(){
	var u = navigator.userAgent;
	var isapp = u.indexOf('Html5Plus') > -1;
	if(isapp){return true;}else{return false;}
}
function html_decode(str){           
  str = str.replace(/&amp;/g, '&'); 
  str = str.replace(/&lt;/g, '<');
  str = str.replace(/&gt;/g, '>');
  str = str.replace(/&quot;/g, "'");  
  str = str.replace(/&#039;/g, "'");  
  return str;  
}
function zeaiOnscroll(){
	var t = document.documentElement.scrollTop||document.body.scrollTop,cH= parseInt(window.innerHeight),H= parseInt(document.body.scrollHeight);//cH= parseInt(document.body.clientHeight)//t+cH==H
	if ((H-t-cH) <150){if (p > ZEAI_totalP){return false;}else{Object.assign(zeaiOnscroll_json.data,{p:p});zeai.ajax(zeaiOnscroll_json,function(e){if (e == 'end'){zeai.msg(0);zeai.msg('已达末页，全部加载结束');return false;}else{ZEAI_list.append(e);p++;}});}}
	backtopFn();
}



function radio_div_list_create(objstr,defvalue,ARR,fobj,clsname){
	var ARR     = arguments[2] ? arguments[2]:'';
	var clsname = arguments[4] ? arguments[4]:'RCW W50_';
	ARR = (zeai.empty(ARR))?eval(objstr+'_ARR'):ARR;
	var chkbox = zeai.addtag('ul');chkbox.id=objstr+'_box';chkbox.name=objstr;chkbox.className=clsname;
	var defv = defvalue.split(',');
	for(var k=0;k<ARR.length;k++){
		var id    = ARR[k].i;
		var text  = ARR[k].v;
		var li = zeai.addtag('li');
		var checkbox  = zeai.addtag('input');
		var label = zeai.addtag('label');
		var i = zeai.addtag('i');i.className = 'i2';
		var b = zeai.addtag('b');b.innerHTML = text;b.className = 'S16';
		checkbox.type  ="radio";checkbox.className = 'radioskin';checkbox.id = objstr+id;checkbox.name = objstr;checkbox.value = id;checkbox.checked = defv.in_array(id);
		label.setAttribute("for",checkbox.id);label.className = 'radioskin-label';label.append(i);label.append(b);
		li.append(checkbox);
		li.append(label);
		chkbox.append(li);
		fobj.append(chkbox);
	}
}
function radio_div_list_get_id(objname){
	var list = [];
	var obj = document.getElementsByName(objname);
	for(var k = 0;k<obj.length;k++){
		if (obj[k].checked)return obj[k].value;
	}
}
function radio_div_list_get_title(id,objstr_ARR){for( var key in objstr_ARR){if (objstr_ARR[key].i == id)return objstr_ARR[key].v;}}
function checkbox_div_list_create(objstr,defvalue,ARR,fobj,clsname){
	var ARR     = arguments[2] ? arguments[2]:'';
	var clsname = arguments[4] ? arguments[4]:'RCW';
	ARR = (zeai.empty(ARR))?eval(objstr+'_ARR'):ARR;
	var chkbox = zeai.addtag('ul');chkbox.id=objstr+'_box';chkbox.name=objstr;chkbox.className=clsname;
	var defv = defvalue.split(',');
	for(var k=0;k<ARR.length;k++){
		var id    = ARR[k].i;
		var text  = ARR[k].v;
		var li = zeai.addtag('li');
		var checkbox  = zeai.addtag('input');
		var label = zeai.addtag('label');
		var i = zeai.addtag('i');i.className = 'i2';
		var b = zeai.addtag('b');b.innerHTML = text;b.className = 'S16';
		checkbox.type  ="checkbox";checkbox.className = 'checkskin';checkbox.id = objstr+id;checkbox.name = objstr+'[]';checkbox.value = id;checkbox.checked = defv.in_array(id);
		checkbox.onclick = function (){if (zeai.form.ifcheckbox(objstr+'[]') > checkboxMaxNum){zeai.msg('最多选择 '+checkboxMaxNum+' 项');return false;}};
		label.setAttribute("for",checkbox.id);label.className = 'checkskin-label';label.append(i);label.append(b);
		li.append(checkbox);
		li.append(label);
		chkbox.append(li);
		fobj.append(chkbox);
	}
}
function checkbox_div_list_get_title(id,objstr_ARR){for( var key in objstr_ARR){if (objstr_ARR[key].i == id)return objstr_ARR[key].v;}}
function checkbox_div_list_get_listTitle(objstr,list){
	var list = list.split(',');
	var list_text = [];
	for( var key in list){
		if (typeof list[key] == 'string'){
			list_text.push(checkbox_div_list_get_title(list[key],eval(objstr + '_ARR')));
		}
	}
	return list_text.join(",");	
}
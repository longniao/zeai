/******************************************
WWW.ZEAI.CN 作者: 郭余林　QQ:797311 (supdes)
未经本人同意，请不要删除版权，否则将追究法律责任
*****************************************/
var o = function (id){if(typeof(id) == "string")return document.getElementById(id);return id;}
o.prototype = Element.prototype;o.prototype.constructor = o;
o.prototype.hide = function (){this.style.display = 'none';}
o.prototype.show = function (s){var b;if (s==''){b='';}else if(s=='inline-block'){b='inline-block';}else{b='block';}this.style.display = b;}
o.prototype.html = function (str){this.innerHTML=str;}
o.prototype.class = function (clsname){this.className = clsname;}
o.prototype.hasClass = function (cls){return !!this.className.match( new RegExp( "(\\s|^)" + cls + "(\\s|$)") );}
o.prototype.removeClass = function (cls){if (this.hasClass(cls)){var reg = new RegExp("(\\s|^)" + cls + "(\\s|$)");this.className = this.className.replace(reg, " ");}}
o.prototype.addClass = function (cls){if (!this.hasClass(cls))this.className += " " + cls;}
o.prototype.append = function (C){if(typeof(C) == "string" ){this.insertAdjacentHTML('beforeEnd',C);return this.lastChild;}else if(typeof(C) == "object" ){this.appendChild(C);}}
o.prototype.remove = function (){if(!zeai.empty(this.parentNode))this.parentNode.removeChild(this);}
o.prototype.css = function (C){this.style.cssText = C;}
Array.prototype.in_array = function(str) {if(typeof str == 'string' || typeof str == 'number'){for(var i in this) {if(this[i] == str) {return true;}}}return false;}
Array.prototype.delRepeat= function(){var hash=[],arr=[];for (var i = 0; i < this.length; i++){hash[this[i]]!=null;if(!hash[this[i]]){arr.push(this[i]);hash[this[i]]=true;}}return arr;}//去除重复
Array.prototype.ifRepeat = function () {var hash = {};for (var i in this) {if (hash[this[i]]){return this[i]/*true*/;}hash[this[i]] = true;}return false;}//是否重复
Array.prototype.remove   = function(a){var newarr = Array();for (key in this){if (this[key] == a)continue;newarr.push(this[key]);}return newarr;}//删除元素
var zeai = {
	extname:'.php',ajxext:'.php?',
	//tpsL:"<div class='tipsbox'><img src='/images/loadingData.gif'>",
	//tpsR:"</div>",
    post_flag:false,//防止ajax重复提交
	empty:function(str){if (str==''||str=='NAN'||str==0||str===null||str==='null'||str===undefined||str==='undefined'||str=='undefined'){return true;}else{return false;}},
	ifint:function(str,n1,n2){var n1 = arguments[1] ? arguments[1]:'0-9';var n2 = arguments[2] ? arguments[2]:'1,9';var ss= eval("/^\s*["+n1+"]{"+n2+"}\s*$/");	if(!ss.test(str) || this.empty(str)){return false;}else{return true;}},
	goback:function(){window.history.go(-1);},
	ifnum:function (o){var pattern = /^\d+(\.\d+)?$/;if(pattern.test(o)){return true;}else{return false;}},//数字
	ifmob:function(num){var partten = /^1[3,4,5,6,7,8,9]\d{9}$/;if(partten.test(num)){return true;}else{return false;}},
	is_mobile:function(){var userAgentInfo = navigator.userAgent;var Agents = ["Android", "iPhone","SymbianOS", "Windows Phone","iPad", "iPod"];var flag = false;for (var v = 0; v < Agents.length; v++) {if (userAgentInfo.indexOf(Agents[v]) > 0) {flag = true;break;}}return flag;},
	ifsfz:function (card){var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;if(reg.test(card) === false){return  false;}else{return  true;}},
	ifjson:function ifjson(obj){var isjson = typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;return isjson;},
	str_len:function(str){var byteCount=0;for(var i=0;i<str.length;i++){byteCount=(str.charCodeAt(i)<=256)?byteCount+1:byteCount+2;}return byteCount;},
	openurl:function (url) {window.location.href=url;},openurl_:function openurl_(url) {window.open(url,'_blank')},
	/*
	setcookie:function (name,value){var Days = 86400*365;var exp = new Date();exp.setTime(exp.getTime() + Days*1000);document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString() + ";path=/" + ";domain="+jsdomain;},
	getcookie:function (name){var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));if(arr != null) return unescape(arr[2]); return null;},
	*/
	showSwitch:function (idarr){idarr = idarr.split(',');if (o(idarr[0]).style.display == 'block'){for(var i=0;i<idarr.length;i++){o(idarr[i]).hide();}}else{for(var i=0;i<idarr.length;i++){o(idarr[i]).show();}}},
	addtag:function (tag){return document.createElement(tag);},
	back:function (url){
		url=(zeai.empty(url))?HOST:url;
		document.referrer === ''?window.location.href = url:window.history.go(-1);
	},
	tag:function (obj,tag){return obj.getElementsByTagName(tag);},
	ajax:function(url,fn,bfb){
		if (zeai.post_flag)return;
		zeai.post_flag = true;var formData = null,js=1,loading=1;
		if(window.XMLHttpRequest) {	var xmlHttp = new XMLHttpRequest();}else if(window.ActiveXObject) {
		try{var xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");}	catch(e){} 
		try{var xmlHttp = new ActiveX0bject("Msxml2.XMLHTTP");}	catch(e){}	
		if(!xmlHttp){ window.alert("No Create XML"); return false;}}
		if(typeof(url) == "object"){
			formData = (!zeai.empty(url.form))?new FormData(url.form):new FormData();
			if (!zeai.empty(url.data)){var PARAMS = url.data;for (var x in PARAMS){formData.append(x,PARAMS[x]);}}
			js=(url.js == 1)?1:0,loading=(url.ajaxLoading==0)?0:1,url=url.url;
		}
		if (loading==1)zeai.loading();
		xmlHttp.open('POST',encodeURI(url),true);//true异
		xmlHttp.onreadystatechange = function(){
			var netflag = xmlHttp.status;
			if (xmlHttp.readyState == 4 && netflag == 200){/* && !zeai.empty(xmlHttp.responseText)*/
				if (loading==1)zeai.loading(0);zeai.post_flag = false;
				var s_s = xmlHttp.responseText;
				if (!zeai.empty(s_s)){
					if (js == 1){
						var ls=zeai.jsoneval(s_s);
						if (ls.flag=='nologin'){
							zeai.openurl(HOST+'/?z=login&jumpurl='+encodeURIComponent(ls.jumpurl));return;
						}else if(ls.flag=='nologin_tg'){
							zeai.msg(ls.msg);
							var tguid=(zeai.ifint(ls.tguid))?ls.tguid:'';
							setTimeout(function(){zeai.openurl(HOST+'/m1/tg_login.php?loginkind=shop&tguid='+tguid+'&jumpurl='+encodeURIComponent(ls.jumpurl));},1000);
							return;
						}
					}
				}
				if(typeof(fn) == "function")fn(s_s);
			}else{
				if (netflag != 200){var rs=zeai.jsoneval(JSON_ERROR);
					if (loading==1)zeai.loading(0);
					if (netflag != 0){zeai.msg('（ZEAIERROR:'+netflag+'）'+rs.msg);}
				}
				zeai.post_flag = false;
			}
		}
		if(typeof(bfb) == "function"){
			xmlHttp.upload.onprogress = function (ev){
				var percent = 0; 
				if(ev.lengthComputable) { 
					percent = parseInt(100 * ev.loaded/ev.total);
				} 
				bfb(percent);
			}
		} 
		xmlHttp.send(formData);
	},
	post:function(url,PARAMS,target){
		var temp = this.addtag("form");
		temp.action = url;temp.method = "post";
		temp.style.display = "none";
		if(target=='_blank')temp.target="_blank";
		for (var x in PARAMS) {
			var opt = this.addtag("textarea");
			opt.name = x;
			opt.value = PARAMS[x];
			temp.appendChild(opt); 
		}
		document.body.appendChild(temp);
		temp.submit();
		return temp;
	},
	copy:function(str,fn){
		var input = document.createElement("textarea");
		input.value = str;
		document.body.appendChild(input);
		input.select();
		input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
		document.body.removeChild(input);
		if(typeof(fn) == "function"){fn();}
	},
	up:function (json){
		var pic;if(!zeai.empty(o('pic'))){pic=o('pic');}else{pic=zeai.addtag('input');pic.id='pic';pic.type='file';pic.accept="image/gif,image/jpeg,image/png";pic.hide();document.body.append(pic);}
		if (json.multiple>0)pic.multiple=true;
		pic.click();
		//zeai.msg(0);zeai.msg('载入中...');
		pic.onchange = function(){
			if (json.multiple>0){
				var FILES   = this.files;
				var Flength = FILES.length;
				var endLth=Flength+curnum;
				if (endLth>maxnum){//json.multiple
					picNull();zeai.msg(0);zeai.msg('最多只能上传'+maxnum+'张哦!');
				}else{
					var filename,ftype;
					for(var i=0;i<Flength;i++) {
						if (FILES[i]['size'] > json.upMaxMB*1024000){zeai.msg(0);zeai.msg('图片【'+FILES[i]['name']+'】太大，已超过'+json.upMaxMB+'M，请重新选择');picNull();return false;}
						filename = FILES[i]['name'].toLowerCase();ftype = filename.substring(filename.lastIndexOf("."),filename.length);
						if ((ftype != '.jpg')&&(ftype != '.jpeg')&&(ftype != '.gif')&&(ftype != '.png')){picNull();zeai.msg('只能上传jpg/gif/png格式图片（当前为：'+ftype+'）,请重新选择!');return false;}
					}
					var j=0;
					function photo_up(){
						var jsstr=(Flength>1)?'第 '+(j+1)+' 张':'';
						zeai.msg(0);zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在上传'+jsstr,{animation:"off",time:99})
						var postjson = {"file":FILES[j]};Object.assign(postjson,json);
						zeai.ajax({"url":json.url,"ajaxLoading":json.ajaxLoading,"data":postjson},function(e){var rs=zeai.jsoneval(e);
							if (rs.flag == 1){
								j++;
								if(typeof(json.li)=="function"){json.li(e);}
								if (j < Flength){
									zeai.msg(0);setTimeout(photo_up,300);
								}else{
									picNull();if(typeof(json.fn)=="function")json.fn(e);
								}
							}else{
								picNull();if(typeof(json.fn)=="function")json.fn(e);
								//zeai.msg('【'+FILES[j]['name']+'】上传出错，请联系原作者QQ：797311');
							}
						});
					}
					photo_up();
				}
			}else{
				var FILES = pic.files[0];
				if (FILES['size'] > json.upMaxMB*1024000){zeai.msg(0);zeai.msg('图片【'+FILES['name']+'】太大，已超过'+json.upMaxMB+'M，请重新选择');picNull();return false;}
				var filename = FILES['name'].toLowerCase();ftype = filename.substring(filename.lastIndexOf("."),filename.length);
				if ((ftype != '.jpg')&&(ftype != '.jpeg')&&(ftype != '.gif')&&(ftype != '.png')){picNull();zeai.msg('只能上传jpg/gif/png格式图片（当前为：'+ftype+'）,请重新选择!');return false;}
				zeai.msg(0);zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在上传',{time:99})
				var postjson = {"file":FILES};Object.assign(postjson,json);
				zeai.ajax({"url":json.url,"ajaxLoading":json.ajaxLoading,"data":postjson},function(e){picNull();if(typeof(json.fn)=="function")json.fn(e);});
			}
			function picNull(){zeai.msg(0);pic.remove();}
		}
	},
	photoUp:function (json){
		var btnobj=json.btnobj;
		if(!zeai.empty(o(btnobj))){
			btnobj.onclick=function(){up();}
		}else{up()}
		function up(){	
			zeai.up({url:json.url,upMaxMB:json.upMaxMB,"submitok":json.submitok,ajaxLoading:0,multiple:json.multiple,
			fn:function(e){var rs=zeai.jsoneval(e);json.end(rs);},
			li:function(e){var rs=zeai.jsoneval(e);json.li(rs);}
			});
		}
	},
	jsoneval:function(rs){return eval('('+decodeURI(rs)+')');},
	form:{
		ifradio:function(objname){
			var f = false;
			var obj = document.getElementsByName(objname);
			for(var k = 0;k<obj.length;k++){
				if (obj[k].checked){f=true;break;}
			}
			return f;
		},
		ifcheckbox:function(objname){
			var n = 0;
			var obj = document.getElementsByName(objname);
			for(var k = 0;k<obj.length;k++){
				if (obj[k].checked)n++;
			}
			return n;
		},
		checkbox_div_list_get:function(objname){
			var list = [];
			var obj = document.getElementsByName(objname);
			for(var k = 0;k<obj.length;k++){
				if (obj[k].checked)list.push(obj[k].value);
			}
			return list.join(',');
		},
	},
	html_decode:function(str){
		str = str.replace(/&amp;/g, '&'); 
		str = str.replace(/&lt;/g, '<');
		str = str.replace(/&gt;/g, '>');
		str = str.replace(/&quot;/g, "'");  
		str = str.replace(/&#039;/g, "'");
		return str;  
	},
	bodyW:function (){return document.body.clientWidth;},
	bodyH:function (){return document.documentElement.clientHeight;},
	alert:function(title,formid){
		//title,formid = function或string.url或back或close或input.object.focus
		var formid = arguments[1] ? arguments[1]:'';
		if (this.empty(o('alert_mask'))){
			var alert_mask  = this.addtag('div');
			var alert_box   = this.addtag('div');
			var alert_title = this.addtag('div');
			var alert_close = this.addtag('div');
			alert_mask.id  = 'alert_mask';alert_mask.className = 'mask alpha0_100';
			alert_box.id   = 'alert_box';alert_box.className = 'small_big';
			alert_title.id = 'alert_title';alert_title.className = 'alert_title';
			alert_close.id = 'alert_close';alert_close.className = 'btn size3 LAN2';alert_close.innerHTML = '确定';
			alert_mask.onclick = function(){alert_close_fn();}
			alert_close.onclick = function(){alert_close_fn();}
			alert_box.onclick = function(e){e.cancelBubble = true;}
			alert_box.appendChild(alert_title);
			alert_box.appendChild(alert_close);
			alert_mask.appendChild(alert_box);
			document.body.appendChild(alert_mask);//parent.
			var cW = alert_box.offsetWidth,cH = alert_box.offsetHeight;
			alert_box.style.left = parseInt((this.bodyW() - cW)/2) + 'px';
			alert_box.style.top  = parseInt((this.bodyH() - cH)/2) + 'px';
		}else{
			var alert_mask  = o('alert_mask');//parent.
			var alert_title = o('alert_title');//parent.
		}
		alert_title.innerHTML = title;
		alert_mask.show();
		function alert_close_fn(){
			alert_mask.hide();
			if (typeof(formid) == "function"){formid();}else if(typeof(formid) == "object"){
				formid.focus();
			}else if(typeof(formid) == "string" && !zeai.empty(formid)){	
				if (formid == 'close' || formid == '0'){	
					window.opener=null;window.open('','_self');window.close();
				}else if(formid == 'back' ||formid == '-1'){
					window.history.back(-1);
				}else{
					zeai.openurl(formid);
				}
			}
		}
	},
	alertplus:function(json){
		var M,B,btn,btn1,btn2;
		if (json == 0){if (!this.empty(o('alertpro_mask'))){
			M=o('alertpro_mask');M.removeClass('alpha0_100');M.addClass('alpha100_0');
			B=o('alertpro_box');B.removeClass('big_normal');B.addClass('big_small');
			setTimeout(function(){M.remove();},400);return;
		}}
		if (this.empty(o('alertpro_mask'))){
			M = this.addtag('div');M.id = 'alertpro_mask';M.className = 'alertpro mask alpha0_100';
			B = this.addtag('div');B.id = 'alertpro_box';B.className = 'box big_normal';
			B.append('<h1>'+json.title+'</h1><h3>'+json.content+'</h3><ul><li>'+json.title1+'</li><li>'+json.title2+'</li></ul>');
			btn=B.getElementsByTagName("li");
			btn1=btn[0];btn2=btn[1];
			if (typeof(json.fn1)=="function")btn1.onclick=json.fn1;
			if (typeof(json.fn2)=="function")btn2.onclick=json.fn2;
			M.append(B);document.body.append(M);
			
			B.onclick = function(e){e.cancelBubble = true;}
			
		}else{
			M = o('alertpro_mask');
		}
		M.onclick = function(){alertpro_mask_close_fn();}
		function alertpro_mask_close_fn(){
			if (!zeai.empty(o('alertpro_mask')))o('alertpro_mask').remove();
		}
	},
	confirm:function(title,fn){
		//title,fn = function或string.url或form.object
		//if (this.empty(o('confirm_mask'))){
			var confirm_mask  = this.addtag('div');
			var confirm_box   = this.addtag('div');
			var confirm_title = this.addtag('div');
			var confirm_ok     = this.addtag('div');
			var confirm_cancel = this.addtag('div');
			confirm_mask.id  = 'confirm_mask';confirm_mask.className = 'mask alpha0_100';
			confirm_box.id   = 'confirm_box';confirm_box.className = 'small_big';
			confirm_title.id = 'confirm_title';confirm_title.className = 'confirm_title';
			confirm_ok.id = 'confirm_ok';confirm_ok.className = 'btn size3 HONG';confirm_ok.innerHTML = '确定';
			confirm_cancel.id = 'confirm_cancel';confirm_cancel.className = 'btn size3 BAI';confirm_cancel.innerHTML = '取消';
			//confirm_mask.onclick = function(){this.hide();}
			confirm_mask.onclick = function(){confirm_close_fn();}
			confirm_box.onclick = function(e){e.cancelBubble = true;}
			confirm_ok.onclick = function(){
				//confirm_mask.hide();
				confirm_close_fn();
				if (typeof(fn) == "function"){
					fn();
				}else if(typeof(fn) == "string"){this.openurl(fn);
				}else if(typeof(fn) == "object"){fn.submit();}
			}
			//confirm_cancel.onclick = function(){confirm_mask.hide();}
			confirm_cancel.onclick = function(){confirm_close_fn();}
			confirm_box.appendChild(confirm_title);
			confirm_box.appendChild(confirm_ok);confirm_box.appendChild(confirm_cancel);
			confirm_mask.appendChild(confirm_box);
			document.body.appendChild(confirm_mask);
			var cW = confirm_box.offsetWidth,cH = confirm_box.offsetHeight;
			confirm_box.style.left = parseInt((this.bodyW() - cW)/2) + 'px';
			confirm_box.style.top  = parseInt((this.bodyH() - cH)/2-50) + 'px';
		//}else{
		//	var confirm_mask  = o('confirm_mask');
		//	var confirm_title = o('confirm_title');
		//}
		confirm_title.innerHTML = title;
		confirm_mask.show();
		function confirm_close_fn(){
			if (!zeai.empty(o('confirm_mask')))o('confirm_mask').remove();
		}
	},
	loading:function(fn){
		if (fn == 0){if (!this.empty(o('loading_mask')))o('loading_mask').remove();return;}
		var l1 = this.addtag('div');l1.class('l1');
		var l2 = this.addtag('div');l2.class('l2');
		var l3 = this.addtag('div');l3.class('l3');
		var loading = this.addtag('div');loading.class('loading');
		var loading_mask = this.addtag('div');loading_mask.id = 'loading_mask';loading_mask.className = 'mask0';
		loading.appendChild(l1);
		loading.appendChild(l2);
		loading.appendChild(l3);
		loading_mask.appendChild(loading);
		document.body.appendChild(loading_mask);
		loading_mask.show();
	},
	msg:function(title,fn){
		//title,fn = function 或 {time:6,mask:'on,off',color:'#333',focus:pwd,flag:'hide',animation:'off'} 或 input.obj(focus)
		if (title == 0){
			if (!this.empty(o('msg_mask')))o('msg_mask').parentNode.removeChild(o('msg_mask'));
			clearTimeout(zeai.tips_zeai_timeoutname);return;
			return;
		}
		var msg_mask  = this.addtag('div');msg_mask.id = 'msg_mask';msg_mask.className = 'mask0 alpha0_100';
		var msg_box   = this.addtag('div');msg_box.id  = 'msg_box';msg_box.className = 'small_big';//msg_box
		var msg_title = this.addtag('div');msg_title.className = 'title';
		msg_box.appendChild(msg_title);msg_mask.appendChild(msg_box);document.body.appendChild(msg_mask);
		msg_title.innerHTML = title;
		var cW = msg_box.offsetWidth,cH = msg_box.offsetHeight;
		var top=parseInt((this.bodyH() - cH)/2-50) + 'px';
		msg_box.style.left = parseInt((this.bodyW() - cW)/2) + 'px';
		msg_box.style.top  = top;
		var time = 2,color='',mask=0,top=0;
		if(zeai.ifjson(fn)){//typeof(fn) == "object"
			time  = (this.ifnum(fn.time))?fn.time:time;
			color = (!this.empty(fn.color))?fn.color:'';
			if(fn.top>0)msg_box.style.top  = parseInt(fn.top)+'px';
			mask  = fn.mask;
			if (time > 5 && fn.animation!='off'){msg_box.className = 'breath';}else{msg_box.className = 'small_big';}//msg_box
			if (mask == 'on' || mask == 1){msg_mask.removeClass('mask0');msg_mask.addClass = 'mask';}else{msg_mask.removeClass('mask');msg_mask.addClass('mask0');}
			if (!this.empty(color))msg_box.style.backgroundColor = color;
		}
		msg_mask.show();
		zeai.tips_zeai_timeoutname = setTimeout(function(){
			msg_box.removeClass('small_big');msg_box.addClass('alpha100_0');//msg_box.addClass('big_small');
			if (mask == 'on' || mask == 1){
				msg_mask.removeClass('alpha0_100');
				//msg_mask.addClass('alpha100_0');
				msg_mask.className = 'mask alpha100_0';
			}
			setTimeout(function(){
				if (typeof(fn) == "function"){fn();}else if(zeai.ifjson(fn)){
					if(typeof(fn.focus) == "object"){fn.focus.focus();}
				}else{	
					if (!zeai.empty(fn))fn.focus();
				}
				if (!zeai.empty(o('msg_mask')))o('msg_mask').remove();
			},400);
		},time*1000);
	},
	tips_zeai_timeoutname:'www+zeai+cn____www+yzlove+com',
	tips:function(title,obj,fn){
		//title,obj,{direction:'left,right,top,bottom',time:4,color:'#009',flag:'hide'}
		if (!zeai.empty(o('tips_box'))){
			o('tips_box').parentNode.removeChild(o('tips_box'));
			clearTimeout(zeai.tips_zeai_timeoutname);return;
		}
		var L = obj.offsetLeft;
		var T = obj.offsetTop;
		var H = obj.offsetHeight;
		var W = obj.offsetWidth;
		var parentt = obj.offsetParent;parentt.style.position = 'relative';
		//if (!this.empty(o('tips_box')))o('tips_box').remove();
		var tips_box   = this.addtag('div');tips_box.id = 'tips_box';tips_box.className = '';
		var tips_title = this.addtag('div');tips_title.className = 'title';
		var tips_jt    = this.addtag('div');tips_jt.className = 'tips_jt';
		tips_box.appendChild(tips_title);tips_box.appendChild(tips_jt);parentt.appendChild(tips_box);
		tips_box.onclick = function(){this.remove();}
		tips_box.style.width = parseInt(zeai.str_len(title)/2*12+10) + 'px';
		tips_title.innerHTML = title;// +  parseInt(zeai.str_len(title)/2*12+20);
		var direction='top',time  = 3,color = '#FD5328',flag='show';
		var boxW = tips_box.offsetWidth,boxH = tips_box.offsetHeight;
		if(typeof(fn) == "object"){
			direction = (!this.empty(fn.direction))?fn.direction:direction;
			time = (this.ifint(fn.time))?parseInt(fn.time):time;
			color = (!this.empty(fn.color))?fn.color:color;
			flag = (this.empty(fn.flag))?'show':fn.flag;
		}
		if (flag == 'hide'){tips_box.remove();return;}
		tips_box.style.backgroundColor = color;
		switch (direction) {
			case 'top':
				tips_box.style.left = parseInt(L-(boxW-W)/2+4) + 'px';
				tips_box.style.top  = parseInt(T-boxH-10) + 'px';
				tips_box.className = 'top';/* animattime_fast small_big*/
				tips_jt.style.borderTopColor = color;
			break;
			case 'right':
				tips_box.style.left = parseInt(L+W+10) + 'px';
				tips_box.style.top  = parseInt(T-(boxH-H)/2) + 'px';
				tips_box.className = 'right';
				tips_jt.style.borderBottomColor = color;
			break;
			case 'bottom':
				tips_box.style.left = parseInt(L-(boxW-W)/2-4) + 'px';
				tips_box.style.top  = parseInt(T+H+10) + 'px';
				tips_box.className = 'tips_bottom';
				tips_jt.style.borderBottomColor = color;
			break;
			case 'left':
				tips_box.style.left = parseInt(L-boxW-10) + 'px';
				tips_box.style.top  = parseInt(T-(boxH-H)/2) + 'px';
				tips_box.className = 'left';
				tips_jt.style.borderBottomColor = color;
			break;
		}
		tips_box.show();
		zeai.tips_zeai_timeoutname = setTimeout(function(){if (!zeai.empty(o('tips_box')))o('tips_box').parentNode.removeChild(o('tips_box'));},time*1000)
	},
	iframe:function(title,url,width,height){
		//title,url,400,300
		width  = (width == 'auto' || zeai.empty(width))?parseInt(this.bodyW()):width;
		height = (height == 'auto'|| zeai.empty(height))?parseInt(this.bodyH()):height;
		if (title == 0){iframe_close_fn();return;}
		var iframe_mask  = this.addtag('div');
		var iframe_box   = this.addtag('div');
		var iframe_title = this.addtag('div');iframe_title.className = 'iframe_title';
		var iframe_close = this.addtag('div');iframe_close.className = 'iframe_close';
		var iframe_iframe = this.addtag('iframe');iframe_iframe.className = 'iframe_iframe';iframe_iframe.id='iframe_iframe';
		iframe_mask.id = 'iframe_mask';iframe_mask.className = 'mask alpha0_100';
		iframe_box.id = 'iframe_box';
		iframe_box.className = 'small_big';
		iframe_box.style.width  = width+'px';
		iframe_box.style.height = height+'px';
		iframe_box.style.left = parseInt((this.bodyW() - width)/2) + 'px';
		iframe_box.style.top  = parseInt((this.bodyH() - height)/2) + 'px';
		iframe_iframe.frameBorder=0;
		iframe_iframe.style.width  = width+'px';
		iframe_iframe.style.height = parseInt(height - 43)+'px';
		iframe_iframe.src = url;
		iframe_close.html('✖');
		iframe_close.setAttribute("title","关闭当前窗口")
		iframe_mask.onclick = function(){iframe_close_fn();}
		iframe_box.onclick = function(e){e.cancelBubble = true;}
		iframe_close.onclick = function(){iframe_close_fn();}
		iframe_box.appendChild(iframe_title);
		iframe_box.appendChild(iframe_close);
		iframe_box.appendChild(iframe_iframe);
		iframe_mask.appendChild(iframe_box);
		document.body.appendChild(iframe_mask);
		iframe_title.html(decodeURIComponent(title));
		function iframe_close_fn(){o('iframe_mask').parentNode.removeChild(o('iframe_mask'));}
		//this.drag(iframe_title,iframe_box);
	},
	div:function(json){
		if (json==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		var fobj=json.fobj/*,vname=json.obj.id*/;
		var H=this.bodyH(),W=this.bodyW();
		var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(this.bodyW() - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(this.bodyH() - 30):json.h;
		var div_mask  = this.addtag('div');div_mask.class('mask alpha0_100');/*div_mask.id = 'div_mask'+vname;*/
		//if (typeof(fobj) == "object")H=H+fobj.scrollTop*2;
		var div_box   = this.addtag('div');div_box.class('big_normal');div_box.id = 'div_box';
		var div_title = this.addtag('div');div_title.class('div_title');
		var div_close = this.addtag('div');div_close.class('div_close');div_close.id='div_close';
		var div_div = this.addtag('div');div_div.class('div_div');
		div_mask.onclick = function(){div_close_fn();}
		div_box.onclick = function(e){e.cancelBubble = true;}
		div_close.onclick = function(){div_close_fn();}
		div_box.append(div_title);div_box.append(div_close);div_box.append(div_div);div_mask.append(div_box);
		div_box.style.width  = width+'px';div_box.style.height = height+'px';div_box.style.left = parseInt((W-width)/2) + 'px';
		div_box.style.top  = parseInt((H-height)/2) + 'px';
		div_div.style.width  = width+'px';div_div.style.height = parseInt(height - 43)+'px';div_div.appendChild(json.obj);json.obj.show();
		div_title.html(json.title);
		div_mask.show();
		div_div.addEventListener('touchmove',function(e) {e.cancelBubble = true;});
		div_mask.addEventListener('touchmove',function(e) {e.preventDefault();});
		if (typeof(fobj) == "object"){
			div_mask.style.height = fobj.scrollHeight+'px';
			fobj.append(div_mask);
		}else{
			document.body.append(div_mask);
		}
		function div_close_fn(){
			if(zeai.empty(json.removeobj)){
				if (typeof(fobj) == "object"){
					fobj.appendChild(json.obj);
				}else{
					document.body.appendChild(json.obj);
				}
			}
			json.obj.hide();
			div_box.class('big_small');div_mask.class('alpha100_0');
			setTimeout(function(){div_mask.remove();},200);
		}
		return div_close;
	},
	drag:function(obj,box){
		obj.onmousedown = function (e) {
			var d = document;
			var page = {
				event: function (evt) {
					var ev = evt || window.event;return ev;
				},pageX: function (evt) {
					var e = this.event(evt);
					return e.pageX || (e.clientX + document.body.scrollLeft - document.body.clientLeft);
				},pageY: function (evt) {
					var e = this.event(evt);
					return e.pageY || (e.clientY + document.body.scrollTop - document.body.clientTop);
				},layerX: function (evt) {
					var e = this.event(evt);
					return e.layerX || e.offsetX;
				},layerY: function (evt) {
					var e = this.event(evt);
					return e.layerY || e.offsetY;
				}
			}             
			var x = page.layerX(e);var y = page.layerY(e);        
			if (obj.setCapture) {obj.setCapture();}else if(window.captureEvents) {window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);}
			d.onmousemove = function (e) {                    
				var tx = page.pageX(e) - x,ty = page.pageY(e) - y;
				var boxW = parseInt(box.style.width),boxH = parseInt(box.style.height);
				var bodyW = zeai.bodyW(),bodyH = zeai.bodyH();;
				if (tx<0)tx = 0;if (ty<0)ty = 0;
				var maxLeftW = bodyW-boxW-10;
				var maxBottomH = bodyH-boxH-10;
				if (tx>maxLeftW)tx = maxLeftW;
				if (ty>maxBottomH)ty = maxBottomH;
				box.style.left = tx + "px";
				box.style.top = ty + "px";
			}
			d.onmouseup = function () {
				if (obj.releaseCapture) {obj.releaseCapture();}else if (window.releaseEvents) {window.releaseEvents(Event.MOUSEMOVE | Event.MOUSEUP);}d.onmousemove = null;d.onmouseup = null;
			}
		}
	},
	getDomain:function(){
		var url = window.location.host;
		var TLDs = ["vip","biz","link", "cc", "cn", "co", "com", "do","edu", "gov",  "hk", "id", "im", "in", "info", "is", "it",  "jp", "kr",  "la",  "me", "mobi", "my",  "tel", "to", "travel", "tv", "tw", "us"].join();
			url = url.replace(/.*?:\/\//g, "");
			url = url.replace(/www./g, "");
			var parts = url.split('/');
			url = parts[0];
			var parts = url.split('.');
			if (parts[0] === 'www' && parts[1] !== 'com')parts.shift()
			var ln = parts.length, i = ln, minLength = parts[parts.length-1].length, part;
			while(part = parts[--i]){
				if (i === 0                    // 'yzlove.com' (last remaining must be the SLD)
					|| i < ln-2                // TLDs only span 2 levels
					|| part.length < minLength // 'www.cn.com' (valid TLD as second-level domain)
					|| TLDs.indexOf(part) < 0  // officialy not a TLD
				){var actual_domain = part;break;}
			}
			var tid ;
			if(typeof parts[ln-1] != 'undefined' && TLDs.indexOf(parts[ln-1]) >= 0){tid = '.'+parts[ln-1];}
			if(typeof parts[ln-2] != 'undefined' && TLDs.indexOf(parts[ln-2]) >= 0){tid = '.'+parts[ln-2]+tid;}
			if(typeof tid != 'undefined')
				actual_domain = actual_domain+tid;
			else
				actual_domain = actual_domain+'.com';
			return actual_domain;
	},
	getScrollTop:function(){
		var scrolltop = 0; 
		if (document.documentElement && document.documentElement.scrollTop) { 
			scrolltop = document.documentElement.scrollTop; 
		}else if (document.body) {
			scrolltop = document.body.scrollTop;
		}
		return scrolltop;
	},
	setScrollTop:function(n){
		document.body.scrollTop = n;document.documentElement.scrollTop = n;
	},
	listEach:function(element,fn){
		var list;zeaiifbreak = false;
		if (typeof(element) == "object"){list = element;}else if(typeof(element) == "string"){list = document.querySelectorAll(element);}
		if (!zeai.empty(list)){for(var x=0;x<list.length;x++){if (zeaiifbreak)return false;(function(x){fn(list[x],x);})(x);}}
	},
	ready:function(fn){
		if(document.addEventListener){
			document.addEventListener('DOMContentLoaded',function(){document.removeEventListener('DOMContentLoaded',arguments.callee,false);fn();},false)
		}else if(document.attachEvent){
			document.attachEvent('onreadystatechange',function(){if(document.readyState=='complete'){document.detachEvent('onreadystatechange',arguments.callee);fn();}});
		}
	},
	mask:function(json){
		var Mid='M'+json.son.id;
		if(zeai.empty(o(Mid))){
			var M=zeai.addtag('div');M.id=Mid;M.class('mask1 alpha0_100');document.body.append(M);
			M.append(json.son);
			json.son.show();
			if(zeai.empty(json.click))M.onclick=mask_close_fn;
			//M.addEventListener('touchmove', function(e){e.preventDefault();});
			if(zeai.empty(json.cancelBubble))json.son.onclick = function(e){e.cancelBubble = true;}
		}
		function mask_close_fn(){
			if(!zeai.empty(json.fobj)){json.fobj.append(json.son);}else{document.body.append(json.son);}
			M.removeClass('alpha0_100');M.addClass('alpha100_0');
			setTimeout(function(){if (!zeai.empty(o(Mid))){o(Mid).remove();}},400);
			json.son.hide();
			if (typeof(json.close) == "function")json.close(o(Mid));
		}
	},
	clearhtml:function(sTxt){
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
		c=c.replace(/\n/g,"");c = c.replace(/\s*/g,"");
		return c;
	},
	clearhtmlall:function(str) {
		if (!zeai.empty(str)){
			str=zeai.clearhtml(str);
			var tem = str
			.replace(/&lt;/g,"<")
			.replace(/&gt;/g,">")
			.replace(/&quot;/g,"\"")
			.replace(/&apos;/g,"'")
			.replace(/&amp;/g,"&")
			.replace(/&nbsp;/g,'').replace(/<\/?.+?\/?>/g,'').replace(/<[^>]+>/g,"");
			return tem;
		}
	},
	verify:function(json){
		json.yzmbtn.onclick = function(){
			if (zeai.ifmob(json.mob.value)){
				if (!this.hasClass('disabled')){
					json.yzmbtn.addClass('disabled');
					zeai.ajax({url:json.url,data:{mob:json.mob.value}},function(e){var rs=zeai.jsoneval(e);
						if (rs.flag == 1){zeai.msg(rs.msg,{time:4});json.verify.value='';yzmtimeFn(json.sec);
						}else{zeai.msg(rs.msg,json.mob);json.yzmbtn.removeClass('disabled');}
					});
				}
			}else{
				zeai.msg('请输入【手机号码】',json.mob);return false;
			}
		}
		function yzmtimeFn(countdown) {
			if (countdown == 0) {
				json.yzmbtn.removeClass('disabled');json.yzmbtn.html('<font>重新获取</font>');return false;
			} else {
				json.yzmbtn.addClass('disabled');json.yzmbtn.html('<b>'+countdown + "S</b>后重新发送");countdown--;
			} 
			cleandsj=setTimeout(function(){yzmtimeFn(countdown)},1000);
		}
	}
}
var zeaiifbreak = false,curnum=0;
var JSON_ERROR = '{"flag":"zeai_error","msg":"网络故障～请重试或联系管理员"}';
/**************************************************************************************************************************/
//var dmain = zeai.getDomain();jsdomain = '.'+dmain;document.domain = dmain;
var getJsUrl = document.scripts;getJsUrl = getJsUrl[getJsUrl.length - 1].src.substring(0, getJsUrl[getJsUrl.length - 1].src.lastIndexOf("/") + 1),ZEAI_MAIN='main',zeaiext = '3mp42cnzeaimp3ds348w8w8w8z4e4a4i4c8n8';
var HOST = getJsUrl.substr(0,getJsUrl.length-5);
var checkboxMaxNum=3;maxnum=8;
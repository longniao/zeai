var msgtitle = '温馨提示：';var ajxpath  = '/ajax/',zeaiext = '3mp42cnzeaimp3ds348w8w8w8z4e4a4i4c8n8',ajxext   = '.php?',ajxext_=rtrim(ajxext);
var dmain = getDomain(window.location.host);jsdomain = '.'+dmain;document.domain = dmain;
var getJsUrl = document.scripts;getJsUrl = getJsUrl[getJsUrl.length - 1].src.substring(0, getJsUrl[getJsUrl.length - 1].src.lastIndexOf("/") + 1);
var self_2domain = getJsUrl.substr(0,getJsUrl.length-4);
function getid(o){	if(typeof(o) == "string")return document.getElementById(o);return o;}
function ifnum(o){	var pattern = /^\d+(\.\d+)?$/;if(pattern.test(o)){return true;}else{return false;}}//数字
function o(o){	if(typeof(o) == "string")return document.getElementById(o);return o;}
function ifint(str,member,length){var zeai = (!empty(member) && !empty(length))?eval("/^\s*["+member+"]{"+length+"}\s*$/"):eval("/^\s*[0-9]{1,9}\s*$/");if(!zeai.test(str) || empty(str)){return false;}else{return true;}}//ifint("20","0-9","1,2")
function empty(str){if (str == '' || str == 0 || str === null || str === undefined){return true;	}else{return false;}}
function openlinks(url) {/*window.open(str,'_self')*/window.location.href=url;}function openlinks2(url) {window.open(url,'_blank')}
function openurl(url) {/*window.open(str,'_self')*/window.location.href=url;}function openurl_(url) {window.open(url,'_blank')}
function confirm_url(title,url){ZEAI_win_confirm(title,function (){openurl(url);});}
function SendData(url){var lastJsObj = null;var headID = document.getElementsByTagName("head")[0];var newScript = document.createElement("script");if ( lastJsObj != null ){headID.removeChild(lastJsObj);}newScript.type = "text/javascript";newScript.charset = "utf-8";encodestr = encodeURI(url);newScript.src = encodestr;lastJsObj = newScript;headID.appendChild(newScript);}
function showhidden(id,type) {	var mode = (type== 1)?'block':'none';getid(id).style.display = mode;} 
function display(id,type){var mode = (type== 1)?'block':'none';if (typeof(id) == "string"){o(id).style.display = mode;}else{id.style.display = mode;}}
function in_array(v,arr) {if(typeof v == 'string' || typeof v == 'number'){for(var i in arr) {if(arr[i] == v) {return true;}}}return false;}
function str_len(str){var byteCount=0;for(var i=0;i<str.length;i++){byteCount=(str.charCodeAt(i)<=256)?byteCount+1:byteCount+2;}return byteCount;}
function ifmob(num){var partten = /^1[3,4,5,7,8]\d{9}$/;if(partten.test(num)){return true;}else{return false;}}
function ifsfz(card){var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;if(reg.test(card) === false){return  false;}else{return  true;}}
function setfocus(idd){getid(idd).focus();}
function rtrim(s){	return s.substring(0,s.length-1);}
function getcookie(name){var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));if(arr != null) return unescape(arr[2]); return null;}
function setcookie(name,value){var Days = 86400*365;var exp   = new Date();exp.setTime(exp.getTime() + Days*1000);document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString() + ";path=/" + ";domain="+jsdomain;}
function jsoneval(rs){return eval('('+decodeURI(rs)+')');}
function loading(url,type){
	/*
	var loadstr = '<img src="/images/load8.gif">';
	if (empty(type)){
		getid('list').innerHTML = "<div class='blank'>"+loadstr+"</div>";
	}else if(type == 'body'){
		document.body.innerHTML = "<div class='blank' style='margin-top:200px'>"+loadstr+"</div>";
	}else{
		getid(type).innerHTML = "<div class='blank'>"+loadstr+"</div>";
	}
	*/
	openlinks(url);
}
var ADDTIME = new Date().getTime();
var tipsL = "<div class='tips_box'><div class='tpsL'><img src='"+self_2domain+"/images/loadingData.gif'></div><div class='tpsR'>";var tipsR = "</div></div>";
//
function Zeai_createXML() {
	if(window.XMLHttpRequest) {	var xmlHttp = new XMLHttpRequest();
	} else if(window.ActiveXObject) { 		 
		try {	var xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");}	catch(e) {} 
		try{	var xmlHttp = new ActiveX0bject("Msxml2.XMLHTTP");}	catch(e) {}	
		if(!xmlHttp){ window.alert("No Create XML"); return false; }
	}
	return xmlHttp;
}
function Zeai_POST(url,PARAMS) {
    var temp = document.createElement("form");
    temp.action = url;
    temp.method = "post";
    temp.style.display = "none";
    for (var x in PARAMS) {
        var opt = document.createElement("textarea");
        opt.name = x;
        opt.value = PARAMS[x];
        temp.appendChild(opt); 
    }
    document.body.appendChild(temp);
    temp.submit();
    return temp;
}
function XML_ajax(url,fn,form,bfb){
	if(window.XMLHttpRequest) {	var xmlHttp = new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		try {	var xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");}	catch(e) {} 
		try{	var xmlHttp = new ActiveX0bject("Msxml2.XMLHTTP");}	catch(e) {}	
		if(!xmlHttp){ window.alert("No Create XML"); return false; }}
	xmlHttp.open('POST',encodeURI(url),true);//是否异
	xmlHttp.onreadystatechange = function(){if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
	if(typeof(fn) == "string" && !empty(fn)){
		eval(fn+'("'+xmlHttp.responseText+'")');
	}else if(fn){
		fn(xmlHttp.responseText);
	}}}
	if (!empty(bfb)){}
	form = (!empty(form))?form:null;
	xmlHttp.send(form);
}

function ZEAI_chat(uid){
	XML_ajax(self_2domain+'/login'+ajxext+'submitok=ajax_chklogin',function(e){rs=jsoneval(e);
		if (rs.flag == 'nologin'){
			ZEAI_win_alert(rs.msg,rs.jumpurl);
		}else{
			XML_ajax(self_2domain+'/msg/index'+ajxext+'submitok=ajax_ifchat&uid='+uid,function(e){rs=jsoneval(e);
			//console.log(rs);
			switch (rs.flag){
				case -1:ZEAI_win_alert(rs.msg);break;
				case -2:ZEAI_win_alert(rs.msg);break;
				case  0:loading(self_2domain+'/msg/show'+ajxext+'uid='+uid,'body');break;
				case 'nocookdata':ZEAI_win_alert(rs.msg,rs.jumpurl);break;
				default://1,3
					var div,a1,a2,a3;
					ZEAI_win_div('auto',
						function(){
							var divbox = getid('content_div').children;
							if (divbox.length == 0){
								div = document.createElement('div');div.className = 'box_vip';
								a1  = document.createElement('a');a1.href = self_2domain+'/my/vip_2'+ajxext_;a1.innerHTML = rs.text1;
								a2  = document.createElement('a');a2.href = 'javascript:;';a2.innerHTML = rs.text2;
								a3  = document.createElement('a');a3.href = 'javascript:;';a3.innerHTML = rs.text3;
								div.appendChild(a1);div.appendChild(a2);div.appendChild(a3);
								getid('content_div').appendChild(div);
							}else{a2 = divbox[0].children[1];a3= divbox[0].children[2];}
						},
						rs.title,
						function(){
							a2.onclick = function(){XML_ajax(self_2domain+'/msg/index'+ajxext+'submitok=ajax_add_msglist&uid='+uid,function(e){rs=jsoneval(e);ZEAI_winclose_div();ZEAI_win_alert(rs.msg,rs.jumpurl);});}
							a3.onclick = function(){ZEAI_winclose_div();}
						}
					);
				break;
			}
		});
		}
	});
}
//
function chk_radio(objname){
 	var f = false;
	var obj = document.getElementsByName(objname);
	for(var k = 0;k<obj.length;k++){
		if (obj[k].checked){f=true;break;}
	}
	return f;
}
function chk_checkbox(objname){
 	var n = 0;
	var obj = document.getElementsByName(objname+'[]');
	for(var k = 0;k<obj.length;k++){
		if (obj[k].checked)n++;
	}
	return n;
}

function is_mobile(){var userAgentInfo = navigator.userAgent;var Agents = ["Android", "iPhone","SymbianOS", "Windows Phone","iPad", "iPod"];var flag = false;for (var v = 0; v < Agents.length; v++) {if (userAgentInfo.indexOf(Agents[v]) > 0) {flag = true;break;}}return flag;}

function getDomain(url){
	var TLDs = ["biz", "cc", "cn", "co", "com", "do","edu", "gov",  "hk", "id", "im", "in", "info", "is", "it",  "jp", "kr",  "la",  "me", "mobi", "my",  "tel", "to", "travel", "tv", "tw", "us"].join()
    url = url.replace(/.*?:\/\//g, "");
    url = url.replace(/www./g, "");
    var parts = url.split('/');
    url = parts[0];
    var parts = url.split('.');
    if (parts[0] === 'www' && parts[1] !== 'com'){
        parts.shift()
    }
    var ln = parts.length
      , i = ln
      , minLength = parts[parts.length-1].length
      , part
    while(part = parts[--i]){
        if (i === 0                    // 'yzlove.com' (last remaining must be the SLD)
            || i < ln-2                // TLDs only span 2 levels
            || part.length < minLength // 'www.cn.com' (valid TLD as second-level domain)
            || TLDs.indexOf(part) < 0  // officialy not a TLD
        ){
            var actual_domain = part;
            break;
            //return part
        }
    }
    var tid ;
    if(typeof parts[ln-1] != 'undefined' && TLDs.indexOf(parts[ln-1]) >= 0){tid = '.'+parts[ln-1];}
    if(typeof parts[ln-2] != 'undefined' && TLDs.indexOf(parts[ln-2]) >= 0){tid = '.'+parts[ln-2]+tid;}
    if(typeof tid != 'undefined')
        actual_domain = actual_domain+tid;
    else
        actual_domain = actual_domain+'.com';
    return actual_domain;
}
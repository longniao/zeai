/***************************************************
Copyright (C) 2016
未经本人同意，不得转载
作 者:郭余林 QQ:797311 (supdes)
***************************************************/
var ifsupP;
var processHash = function () {
	var hashStr = location.hash.replace("#","");
	if (hashStr){
		var tmplist = (empty(localStorage.tmplist))?getid('tmplist').value:localStorage.tmplist;
		getid('list').innerHTML = tmplist;
		(ifsupP==1 && setA(1)) || (ifsupP==2 && setA(2)) || (ifsupP==3 && setA(3));
	}else{
		var list = getid('list').innerHTML;
		getid('tmplist').value = list;
		localStorage.tmplist   = list;
		localStorage.p         = 1;
		(ifsupP==1 && setA(1)) || (ifsupP==2 && setA(2)) || (ifsupP==3 && setA(3));
	}
}
function get_ajax_list(p){
	showhidden('loading',1);
	getid("loading").innerHTML = "<div class='llrrbox'><div class='ll'><img src='/images/load.gif'></div><div class='rr'>努力加载中...</div></div>";
	var xmlHttp = Zeai_createXML();
	xmlHttp.open("GET",ajax_url+"&p="+p,true); 
	xmlHttp.onreadystatechange = function(){			
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200){					
			var returndate = xmlHttp.responseText;
			if (returndate == 'end'){
				showhidden('loading',1);
				getid("loading").innerHTML = "已达末页，加载结束";
			}else{
				getid('tmplist').value  += returndate;
				getid('list').innerHTML += returndate;
				localStorage.tmplist    += returndate;
				getid("loading").innerHTML = "上滑加载更多";
				location.hash = "#" + p;
			}
		}
	};
	xmlHttp.send(null);
}
window.onload = processHash;window.onhashchange = processHash;
function getScrollTop() { 
var scrollTop = 0; 
if (document.documentElement && document.documentElement.scrollTop) { 
scrollTop = document.documentElement.scrollTop; 
}else if (document.body) {scrollTop = document.body.scrollTop;}
return scrollTop;} 
function getClientHeight(){ 
var clientHeight = 0; 
if (document.body.clientHeight && document.documentElement.clientHeight) { 
clientHeight = Math.min(document.body.clientHeight, document.documentElement.clientHeight);}else{clientHeight = Math.max(document.body.clientHeight, document.documentElement.clientHeight);} 
return clientHeight;} 
function getScrollHeight() {return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);} 
window.onscroll = function () { 
	if (getScrollTop() + getClientHeight() == getScrollHeight()) { 
		var i = (empty(localStorage.p))?getid('p').value:localStorage.p;
		if (i >= totalpage){
			getid("loading").innerHTML = "已达末页，加载结束";
		}else{
			i++;getid("p").value = i;localStorage.p = i;
			get_ajax_list(i);
		}
	}
}
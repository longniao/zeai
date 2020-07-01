$(function(){
	$("#leftMain").load("left.php?menuid=1",function() {
		$("#rightMain").attr('src','welcome.php');
	});
})
function LAfun(menuid,targetUrl,LBid) {
	$("#leftMain").load("left.php?menuid="+menuid, function() {
		if (targetUrl != ''){
			$("#rightMain").attr('src', targetUrl);
			if (!zeai.empty(LBid))$('#LB'+LBid).addClass("ed");
		}
	});
	if (!zeai.empty(o('top_menu'))){
		var list_objj = o('top_menu').getElementsByTagName('a');  
		zeai.listEach(list_objj,function(obj){obj.className = '';});
	}
	o('LA'+menuid).class("ed");
}
function LBfun(menuid,targetUrl) {
	if (targetUrl == 'exit.php')openlinks('exit.php');
	$("#rightMain").attr('src', targetUrl);
	$('.sub_menu').removeClass("ed");
	$('#LB'+menuid).addClass("ed");
	if__www_zeai_cn__video=false;
}
function pageB(B) {
	$('.sub_menu').removeClass("ed");
	$('#LB'+B).addClass("ed");
}
function pageABC(A,B,url) {
	//A
	var listA = o('top_menu').getElementsByTagName('a');  
	zeai.listEach(listA,function(obj){obj.className = '';});
	o('LA'+A).class("ed");
	//B
	$("#leftMain").load("left.php?menuid="+A, function() {
		if (url != ''){
			$("#rightMain").attr('src',url);
			$('#LB'+B).addClass("ed");
		}
	});
}
/*crm*/
function CAfun(menuid,targetUrl) {
	$("#leftMain").load("crm/left.php?menuid="+menuid, function() {
		if (targetUrl != ''){
			$("#rightMain").attr('src', targetUrl);
			$('#CBfun'+menuid).addClass("ed");
		}
	});
	if (!zeai.empty(o('top_menu'))){
		var list_objj = o('top_menu').getElementsByTagName('a');  
		zeai.listEach(list_objj,function(obj){obj.className = '';});
	}
	o('CA'+menuid).class("ed");
}
function CBfun(menuid,targetUrl) {
	if (targetUrl == 'exit.php')openlinks('exit.php');
	$("#rightMain").attr('src', targetUrl);
	$('.sub_menu').removeClass("ed");
	$('#CB'+menuid).addClass("ed");
	if__www_zeai_cn__video=false;
}
/*top 3ico*/
if (!zeai.empty(o('top_menu'))){
	var list_objj = o('top_menu').getElementsByTagName('li');  
	list_objj[0].onclick = function(){
		if (o('leftMain').className == 'off'){
			o('leftMain').class('');
			o('rightMain').class('');
			o('siteName').class('');
			this.class('');
			this.setAttribute('tips-title','左侧缩进');
			sz('bottom');
		}else{
			o('leftMain').class('off');
			o('rightMain').class('off');
			o('siteName').class('off');
			this.class('ed');
			this.setAttribute('tips-title','左侧展开');
			sz('right');
		}
	}
	list_objj[1].onclick = function(){
	}
	list_objj[2].onclick = function(){
		right.location.reload(true); 
	}
}
function sz(direction){
	var tv = o('sz').getAttribute("tips-title");
	var td = (zeai.empty(direction))?o('sz').getAttribute("tips-direction"):direction;
	if (!zeai.empty(tv)){
		o('sz').onmouseover = function(){zeai.tips(tv,o('sz'),{time:60,direction:td})}
		o('sz').onmouseout = function(){zeai.tips(tv,o('sz'),{flag:'hide'})}
	}
}sz();
crmo.onclick = function(){
	zeai.msg('正在切换《红娘管理CRM》系统..',{time:1});
	setTimeout(function(){
		love.hide();crm.show();
		zeai.msg('择爱CRM系统切换',{flag:'hide'});
		siteName.html('择爱CRM 1.0');
		CAfun(1,'crm/welcome.php');
	},1000);
}
loveo.onclick = function(){
	zeai.msg('正在切换《交友管理》系统..',{time:1});
	setTimeout(function(){
		love.show();crm.hide();
		zeai.msg('择爱交友系统切换',{flag:'hide'});
		siteName.html('择爱网V6.0');
		LAfun(1,'welcome.php');
	},1000);
}
top_refresh.onmouseover = function(){
	var tv = this.getAttribute("tips-title");
	var td = this.getAttribute("tips-direction");
	zeai.tips(tv,this,{time:60,direction:td})
}
top_refresh.onmouseout = function(){
	var tv = this.getAttribute("tips-title");
	var td = this.getAttribute("tips-direction");
	zeai.tips(tv,this,{flag:'hide'})
}


top_index.onmouseover = function(){
	var tv = this.getAttribute("tips-title");
	var td = this.getAttribute("tips-direction");
	zeai.tips(tv,this,{time:60,direction:td})
}
top_index.onmouseout = function(){
	var tv = this.getAttribute("tips-title");
	var td = this.getAttribute("tips-direction");
	zeai.tips(tv,this,{flag:'hide'})
}
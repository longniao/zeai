$(function(){
	$("#leftMain").load("left.php?menuid=1",function() {
		//$("#rightMain").attr('src','welcome.php');
		o("rightMain").src='welcome.php';
	});
})
function LAfun(menuid,targetUrl,LBid) {
	$("#leftMain").load("left.php?menuid="+menuid, function() {
		if (targetUrl != ''){
			//$("#rightMain").attr('src', targetUrl);
			o("rightMain").src=targetUrl;
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
	//$("#rightMain").attr('src', targetUrl);
	o("rightMain").src=targetUrl;
	$('.sub_menu').removeClass("ed");
	$('#LB'+menuid).addClass("ed");
	if__www_zeai_cn__video=false;
	if(targetUrl!='welcome.php' && LA1.className=='ed'){
		o('LB1').style.backgroundColor='#393D47';//深色
	}	
}
function pageB(B) {
	$('.sub_menu').removeClass("ed");
	$('#LB'+B).addClass("ed");
}
function pageCRM_B(B) {
	$('.sub_menu').removeClass("ed");
	$('#CB'+B).addClass("ed");
}
function pageABC(A,B,url) {
	//A
	var listA = o('top_menu').getElementsByTagName('a');  
	zeai.listEach(listA,function(obj){obj.className = '';});
	o('LA'+A).class("ed");
	//B
	$("#leftMain").load("left.php?menuid="+A, function() {
		if (url != ''){
			//$("#rightMain").attr('src',url);
			o("rightMain").src=url;
			$('#LB'+B).addClass("ed");
		}
	});
}
/*crm*/
function CAfun(menuid,targetUrl,CBid) {
	$("#leftMain").load("crm_left.php?menuid="+menuid, function() {
		if (targetUrl != ''){
			//$("#rightMain").attr('src', targetUrl);
			o("rightMain").src=targetUrl;
			//$('#CBfun'+menuid).addClass("ed");
			if (!zeai.empty(CBid))$('#CB'+CBid).addClass("ed");
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
	//$("#rightMain").attr('src', targetUrl);
	o("rightMain").src=targetUrl;
	$('.sub_menu').removeClass("ed");
	$('#CB'+menuid).addClass("ed");
	if__www_zeai_cn__video=false;
	if(targetUrl!='crm_welcome.php' && CA1.className=='ed'){
		o('CB1').style.backgroundColor='#393D47';//深色
	}	
}
/*top 3ico*/
if (!zeai.empty(o('top_menu'))){
	var list_objj = o('top_menu').getElementsByTagName('li');  
	list_objj[1].onclick = function(){
		right.location.reload(true); 
	}
	list_objj[0].onclick = function(){
		
	}
}
function crmoFn(){
	zeai.msg('正在切换《线下CRM管理》系统...',{time:1});
	setTimeout(function(){
		love.hide();crm.show();
		zeai.msg('《线下CRM管理》系统切换成功！',{flag:'hide'});
		siteName.html('线下CRM管理');
		top_index.hide();
		top_refresh.addClass('borderleft1');
		CAfun(1,'crm_welcome.php');
	},1000);
}

function loveoFn(){
	zeai.msg('正在切换《主后台》系统...',{time:1});
	setTimeout(function(){
		love.show();crm.hide();
		zeai.msg('《主后台》切换成功！',{flag:'hide'});
		siteName.html(admSiteName);
		top_index.show();
		top_refresh.removeClass('borderleft1');
		LAfun(1,'welcome.php');
	},1000);
}


zeai.listEach('.tips',function(obj){
	var tv = obj.getAttribute("tips-title");
	var td = obj.getAttribute("tips-direction");
	if (!zeai.empty(tv)){
		obj.onmouseover = function(){zeai.tips(tv,obj,{direction:td,color:'#333',time:8})}
		obj.onmouseout = function(){zeai.tips(tv,obj,{flag:'hide'})}
	}
});

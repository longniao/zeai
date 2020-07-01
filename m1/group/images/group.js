function clear2bx(sTxt) {
	//var c=sTxt.replace(/\r\n/ig,"");
	//c = c.replace(/\n/ig, "");
	//c = c.replace(/\r/ig, "");
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
	//c = c.replace(/<center>\s*<center>/ig, '<center>');
	//c = c.replace(/<\/center>\s*<\/center>/ig, '</center>');
	//c = c.replace(/<center>/ig, '<center>');
	//c = c.replace(/<\/center>/ig, '</center>');
	c=c.replace(/\'/g,"’");
	c=c.replace(/\"/g,"”");
	//c=c.replace(/</g,"《").replace(/>/g,"》");
	sTxt = c;
	return sTxt;
}
if (!empty(o('content')))o('content').onclick = function (){
	XML_ajax('/login'+ajxext+'submitok=ajax_chklogin',function (e){rs=jsoneval(e);if (rs.flag == 0){ZEAI_alert('请先登录后再发表','/login.php');}});
	o('content').value = clear2bx(o('content').value);
}
function chkform_party(){
	XML_ajax('/login'+ajxext+'submitok=ajax_chklogin',function (e){rs=jsoneval(e);if (rs.flag == 0){ZEAI_alert('请先登录后再发表','/login.php');}});	
	if(str_len(o('content').value)<1 || str_len(o('content').value)>1000){
		ZEAI_alert('内容请控制在1~1000字节！','content');
		return false;
	}else{o('content').value = clear2bx(o('content').value);}
	o('ZEAI_form_detail').submit();
	return false;
}
function in315(){
	XML_ajax('/login'+ajxext+'submitok=ajax_chklogin',function (e){rs=jsoneval(e);
		if (rs.flag == 0){ZEAI_loginreg('login');}else{ZEAI_iframe(400,530,'举报中心','/315.php');}
	});
}
function chkform_detail(){
	XML_ajax('/login'+ajxext+'submitok=ajax_chklogin',function (e){rs=jsoneval(e);if (rs.flag == 0){ZEAI_alert('请先登录后再发表','/login.php');}});	
	if (empty(o('bkid').value)){ZEAI_alert('请选择版块/分类！','bkid');return false;}
	if (empty(o('title').value)){ZEAI_alert('请输入标题！','title');return false;}
	if(str_len(o('content').value)<5 || str_len(o('content').value)>5000){
		ZEAI_alert('内容请控制在5~5000字节！','content');return false;
	}else{o('content').value = clear2bx(o('content').value);}
	o('ZEAI_form_detail').submit();
	return false;
}

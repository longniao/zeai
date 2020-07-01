/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/23 by supdes
*/
if(!zeai.empty(o('ifcert')))o('ifcert').onclick=function(){
	zeai.ajax({url:PCHOST+'/user'+zeai.ajxext+'submitok=ajax_ifcert'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag=='nologin'){
			zeai.msg(0);zeai.msg(rs.msg);
			setTimeout(function(){if(rs.flag=='nologin'){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}},1500);
		}else if(rs.flag=='nocert'){
			zeai.msg(0);zeai.msg(rs.msg,{time:2});
			setTimeout(function(){zeai.openurl(PCHOST+'/my_cert'+zeai.extname);},2000);
		}else{
			zeai.openurl(PCHOST+'/user'+zeai.ajxext+'t=3&ifcert=1');
		}
	});
}
if(!zeai.empty(o('ifnear')))o('ifnear').onclick=function(){
	zeai.ajax({url:PCHOST+'/user'+zeai.ajxext+'submitok=ajax_ifnear'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag=='nologin'){
			zeai.msg(0);zeai.msg(rs.msg);
			setTimeout(function(){if(rs.flag=='nologin'){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}},1000);
		}else if(rs.flag=='nogps'){
			zeai.msg(0);zeai.msg(rs.msg,{time:2});
			setTimeout(function(){zeai.openurl(PCHOST+'/my_info'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));},2000);
		}else{
			zeai.openurl(PCHOST+'/user'+zeai.ajxext+'t=3&ifnear=1');
		}
	});
}
if(!zeai.empty(o('ifmatch')))o('ifmatch').onclick=function(){
	if(this.checked==true){
	zeai.ajax({url:PCHOST+'/user'+zeai.ajxext+'submitok=ajax_pp'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag=='nologin'){
			zeai.msg(0);zeai.msg(rs.msg);
			setTimeout(function(){if(rs.flag=='nologin'){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}},1000);
		}else if(rs.flag=='nomate'){
			zeai.msg(0);zeai.msg(rs.msg,{time:2});
			setTimeout(function(){zeai.openurl(PCHOST+'/my_info'+zeai.ajxext+'t=4&jumpurl='+encodeURIComponent(rs.jumpurl));},2000);
		}
	});
	}
}
var age1_ARR = age_ARR,age2_ARR = age_ARR,heigh1_ARR = heigh_ARR,heigh2_ARR = heigh_ARR;
ZEAI_area('so_area_',true,'请选择　所在地区');
ZEAI_select('so','sex',true);
ZEAI_select('so','age1',true);ZEAI_select('so','age2',true);
ZEAI_select('so','heigh1',true);ZEAI_select('so','heigh2',true,'不限');
ZEAI_select('so','job',true,'不限');
ZEAI_select('so','edu',true,'不限');
ZEAI_select('so','love',true,'不限');
ZEAI_select('so','house',true,'不限');
ZEAI_select('so','pay',true,'不限');
if(!zeai.empty(o('ulist'))){
zeai.listEach(zeai.tag(ulist,'p'),function(obj){
	var psrc=obj.getAttribute("value");
	obj.style.backgroundImage='url('+psrc+')';
		
});}
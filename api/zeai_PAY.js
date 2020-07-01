/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:7144100,797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/11/01 by supdes
*/
function zeai_PAY(json){
	var jsonurl={url:HOST+'/api/zeai_PAY'+zeai.extname,js:1,data:{submitok:'ajax_pay',money:json.money,paykind:json.paykind,kind:json.kind,tmpid:json.tmpid,tg_uid:json.tg_uid,title:json.title,return_url:json.return_url,jumpurl:json.jumpurl,orderid:json.orderid,oid:json.oid,iflogin:json.iflogin}};
	zeai.msg('正在支付..');
	zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);
		if (rs.flag==1){
			if (rs.trade_type=='H5'){
				zeai.openurl(rs.redirect_url);
			}else{
				function jsApiCall(){
					WeixinJSBridge.invoke('getBrandWCPayRequest',rs.jsApiParameters,function(res){
						//WeixinJSBridge.log(res.err_msg);
						if(res.err_msg == "get_brand_wcpay_request:ok"){
							zeai.msg("支付成功");
							setTimeout(function(){zeai.openurl(rs.return_url);},1000);
						}else{
						   zeai.msg("支付失败,请返回重新支付");
						   //alert(JSON.stringify(res));
						}				
					});
				}
				if (typeof WeixinJSBridge == "undefined"){
					if( document.addEventListener ){
						document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
					}else if (document.attachEvent){
						document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
						document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
					}
				}else{jsApiCall();}
			}
		}else if(rs.flag=='nologin'){
			zeai.openurl(HOST+'/m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));
		}else{
			zeai.msg(rs.msg);	
		}
	});
}
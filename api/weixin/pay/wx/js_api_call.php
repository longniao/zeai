<?php
require_once '../../../../sub/init.php';
$currfields = "money,openid";
require_once ZEAI.'m1/my_chk_u.php';
$data_money = $row['money'];
$data_openid = $row['openid'];

require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_pay.php';

$money     = abs(round($money,2));





//PHP调试输出
/*echo '$wx_gzh_appid = '.$_ZEAI['wx_gzh_appid'].'<br>';
echo '$wx_gzh_appsecret = '.$_ZEAI['wx_gzh_appsecret'].'<br>';
echo '$wxpay_mchid = '.$_PAY['wxpay_mchid'].'<br>';
echo '$wxpay_key = '.$_PAY['wxpay_key'].'<br>';

echo '$money = '.$money.'<br>';
exit;

*/




function show_txt_exit($txt_str,$headmeta){	 echo '<!doctype html><html><head><meta charset="utf-8">'.$headmeta.'</head><body style="text-align:center;padding:10px;line-height:180%;"><br><br><br>'.$txt_str.'</body><html>';}

//

if ($kind == 1){//升级
	
	
	
}elseif($kind == 2){
	
/*	if ($data_money<$money2)json_exit(array('flag'=>0,'msg'=>'余额不足'.$money2.'元'));
	if(strlen($data_openid)<10)json_exit(array('flag'=>0,'msg'=>'获取openid信息失败'));
	
	
	$money2 = abs(round($money*$loveBzk,2));
	
	$orderid    = 'MLOVEB-'.$data_uid.'-'.date("YmdHis");
	$ordertitle = $_ZEAI['LoveB'].'充值';
	$kind    = 2;
	$pay_id  = 21;
	if ($data_uid == 8){
		$money2 = 0.02;
	}
	
	
	*/
	
}elseif($kind == 3){
	
	
}




/*	$db->query("INSERT INTO ".__TBL_PAY__."  (orderid,openid,unionid,kind,uid,title,money,addtime) VALUES ('$orderid','$data_openid','$data_unionid',$kind,$data_uid,'$product_title',$money,$ADDTIME)");
	$pay_id = $db->insert_id();	
	if($submitok == 'loveb'){
		$money = $money*$LovebBuy;
	}*/
$money2 = 2;
$total_fee = $money2*100;//分
include_once("WxPayPubHelper/WxPayPubHelper.php");	
$jsApi = new JsApi_pub();	
$unifiedOrder = new UnifiedOrder_pub();	





$unifiedOrder->setParameter("openid",'otAKmww6MKYBJ2Bwhuo1q_Z64MzE');//商品描述	
$unifiedOrder->setParameter("out_trade_no",'MLOVE'.ADDTIME);//商户订单号 
$unifiedOrder->setParameter("total_fee",1);//总金额
$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型	
$unifiedOrder->setParameter("body","LOVEB");//商品描述
$unifiedOrder->setParameter("attach",23);//附加数据	
$unifiedOrder->setParameter("product_id",8);//商品ID







$prepay_id = $unifiedOrder->getPrepayId();
$jsApi->setPrepayId($prepay_id);
$jsApiParameters = $jsApi->getParameters();


//
$curpage = 'js_api_call';
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>微信安全支付</title>
    
    <span id="sign"></span>

	<script>
		function jsApiCall(){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
					//alert(<?php echo json_encode($jsApiParameters);; ?>);
					//sign.innerHTML = '<?php echo json_encode($jsApiParameters); ?>';
					WeixinJSBridge.log(res.err_msg);
					if(res.err_msg == "get_brand_wcpay_request:ok"){
						//window.location.href='<?php echo $return_okurl; ?>';
					}else{
                       //返回跳转到订单详情页面
                       //alert("支付失败,请重新支付!");
					   alert(JSON.stringify(res));
                       //window.location.href="<?php echo $return_nourl; ?>";                         
                   }				
				}
			);
		}
		//function callpay(){
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    jsApiCall();				
			}
		//}	
	</script>
</head>
<body>
</br></br></br></br>
<div align="center">
<button style="width:210px; height:30px; background-color:#45C01A; cursor: pointer;border:0px;color:white;  font-size:16px;border-radius:1px" type="button" onClick="callpay()" >正在付款,请稍后……</button>
</div>    
</body>
</html>
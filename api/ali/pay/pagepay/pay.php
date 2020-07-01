<?php
header("Content-type: text/html; charset=utf-8");
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'buildermodel/AlipayTradePagePayContentBuilder.php';
require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../config.php';
if (!empty($_POST['orderid'])&& trim($_POST['orderid'])!=""){
	$orderid = strip_tags($_POST['orderid']);
	$kind    = intval($_POST['kind']);
	$paytime = intval($_POST['paytime']);
	$money_list_id = intval($_POST['money_list_id']);
	$orderid_title = strip_tags($_POST['orderid_title']);
	$money    = floatval($_POST['money']);$money=($money>9999)?9999:$money;
	$paymoney = floatval($_POST['paymoney']);$paymoney=($paymoney>9999)?9999:$paymoney;
	//
    $out_trade_no = $orderid;//商户订单号
    $subject      = $orderid_title; //订单名称
    $total_amount = $paymoney; //付款金额
    $body = $orderid_title; //商品描述，可空
    $timeout_express="1m";  //超时时间
    $payRequestBuilder = new AlipayTradePagePayContentBuilder();
    $payRequestBuilder->setBody($body);
    $payRequestBuilder->setSubject($subject);
    $payRequestBuilder->setOutTradeNo($out_trade_no);
    $payRequestBuilder->setTotalAmount($total_amount);
    $payRequestBuilder->setTimeExpress($timeout_express);
    $payResponse = new AlipayTradeService($config);
    $result=$payResponse->PagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);
    //return ;
}


//gyl_debug2($config['notify_url']);
//
//
//function gyl_debug2($C){
//	$C =  $C.PHP_EOL.PHP_EOL;
//	$p = fopen("../../../../up/debug/debug.txt","a");			
//	fwrite($p,$C);
//	fclose($p);
//}
//

//
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../../../../sub/init.php';
require_once ZEAI.'sub/conn.php';
if(iflogin() && ifint($cook_uid)){
	$rowpay = $db->ROW(__TBL_PAY__,"flag","orderid='$orderid'",'num');
	if ($rowpay){
		if ($rowpay[0] == 1)alert('该订单已支付完成，您无需重复支付了');	
	}else{
		$db->query("INSERT INTO ".__TBL_PAY__."(orderid,kind,uid,title,money,paymoney,addtime,money_list_id,paytime,bz) VALUES ('$orderid',$kind,$cook_uid,'$orderid_title',$money,$paymoney,".ADDTIME.",$money_list_id,$paytime,'PC支付宝')");
	}
}
?>
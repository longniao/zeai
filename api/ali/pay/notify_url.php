<?php
require_once("config.php");
require_once 'wappay/service/AlipayTradeService.php';
$arr=$_POST;
$alipaySevice = new AlipayTradeService($config); 
$alipaySevice->writeLog(var_export($_POST,true));
$result = $alipaySevice->check($arr);
/* 实际验证过程建议商户添加以下校验。
1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
4、验证app_id是否为该商户本身。
*/
if($result) {//验证成功
	$trade_status = $_POST['trade_status'];//交易状态
	//$out_trade_no = $_POST['out_trade_no'];//商户订单号
	//$trade_no     = $_POST['trade_no'];//支付宝交易号
	//$total_amount = $_POST['total_amount'];//总金额
	//$buyer_logon_id = $_POST['buyer_logon_id'];//支付人支付宝
	$orderid      = $_POST['out_trade_no'];
	$pay_trade_no = $_POST['trade_no'];
	$pay_money    = $_POST['total_amount'];
	if($_POST['trade_status'] == 'TRADE_FINISHED'){}else if ($_POST['trade_status'] == 'TRADE_SUCCESS'){
		require_once '../../../sub/init.php';
		require_once ZEAI."sub/conn.php";
		require_once ZEAI.'cache/config_vip.php';
		require_once ZEAI.'cache/config_pay.php';
		require_once ZEAI.'sub/ZEAIpay_notify_url.php';
    }
	echo "success";		//请不要修改或删除
}else{
	echo "fail"; //验证失败//请不要修改或删除
}
?>
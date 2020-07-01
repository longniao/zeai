<?php
require_once '../../../sub/init.php';
require_once ZEAI."sub/conn.php";
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_pay.php';
include_once("WxPayPubHelper/WxPayPubHelper.php");
function FromXml($xml){	
	if(!$xml){exit("xml数据异常！");}
	$arr_val = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
	return $arr_val;
}
$notify = new Notify_pub();
$xml = file_get_contents("php://input");
$notify->saveData($xml);	
if($notify->checkSign() == FALSE){
	$notify->setReturnParameter("return_code","FAIL");//返回状态码
	$notify->setReturnParameter("return_msg","ZEAI SIGN FAIL");//返回信息签名失败
}else{
	$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
}
$returnXml = $notify->returnXml();echo $returnXml;
if($notify->checkSign() == TRUE){
	if ($notify->data["return_code"] == "FAIL") {
		//【通信出错】
	}elseif($notify->data["result_code"] == "FAIL"){
		//业务出错
	}else{
		//支付成功
		$data_ok = FromXml($xml);
		//此处应该更新一下订单状态，商户自行增删操作
		/*
		$data_ok['appid']="wxe9f8bac77c360e5a";   					//商用公众号 AppID
		$data_ok['bank_type']="CFT";    							//付款银行
		$data_ok['cash_fee']="1";      								//现金支付金额
		$data_ok['fee_type']="CNY";   								//人民币
		$data_ok['is_subscribe']="Y";  								//是否关注公众账号  Y-关注，N-未关注
		$data_ok['mch_id']="1268050901";  							//商户号
		$data_ok['openid']="o8UNHxH0gFrsRn9Tc6dMKPYaQ7nU";  		//用户在商户appid下的唯一标识
		$data_ok['out_trade_no']="ld20150908192218_";    			//商户订单号
		$data_ok['time_end']="20150908192230";   					//支付完成时间
		$data_ok['total_fee']="1";     								//订单总金额，单位为分
		$data_ok['transaction_id']="1001920605201509080822073810";  //微信支付订单号			
		*/
		$orderid      = $data_ok['out_trade_no'];
		$pay_trade_no = $data_ok['transaction_id'];
		$pay_money    = intval($data_ok['cash_fee'])/100;//单位转成元
		require_once ZEAI.'sub/ZEAIpay_notify_url.php';
	}		
}
?>
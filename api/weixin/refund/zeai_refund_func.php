<?php
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2020/03/28 by supdes
*/
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_pay.php';
require_once ZEAI.'sub/conn.php';
require_once "lib/WxPay.Api.php";
require_once "WxPay.Config.php";
function refund($Ppaymoney,$transaction_id) {
	$total_fee  = $Ppaymoney*100;
	$refund_fee = $total_fee;
	$input = new WxPayRefund();
	$input->SetTransaction_id($transaction_id);
	$input->SetTotal_fee($total_fee);
	$input->SetRefund_fee($refund_fee);
	$config = new WxPayConfig();
	$input->SetOut_refund_no("sdkphp".date("YmdHis"));
	$input->SetOp_user_id($config->GetMerchantId());
	$ret=WxPayApi::refund($config, $input);
	if($ret['result_code']=='SUCCESS' && $ret['return_code']=='SUCCESS'){
		return true;
	}else{
		return false;
	}
}?>
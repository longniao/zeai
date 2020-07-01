<?php
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'./../../../cache/config_pay.php';
$config = array (	
'app_id' => $_PAY['alipay_appid'],//应用ID,您的APPID。
//商户私钥，您的原始格式RSA私钥
'merchant_private_key' => $_PAY['alipay_key1'],

//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
'alipay_public_key' => $_PAY['alipay_key2'],

//异步通知地址
'notify_url' => "http://www.zeai.cn/api/ali/pay/notify_url.php",//请不要更改，否则有问题
//同步跳转
'return_url' => "http://www.zeai.cn/api/ali/pay/return_url.php",//请不要更改，否则有问题

//编码格式
'charset' => "UTF-8",
//签名方式
'sign_type'=>"RSA2",//RSA2
//支付宝网关
'gatewayUrl' => "https://openapi.alipay.com/gateway.do");//请不要更改，否则有问题
$config['notify_url'] = urldecode($_POST['notify_url']);
$config['return_url'] = urldecode($_POST['return_url']);
?>
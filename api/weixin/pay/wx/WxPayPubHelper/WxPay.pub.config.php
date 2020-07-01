<?php
//
define('APPID_',$_ZEAI['wx_gzh_appid']);
define('APPSECRET_',$_ZEAI['wx_gzh_appsecret']);
define('MCHID_',$_PAY['wxpay_mchid']);
define('KEY_', $_PAY['wxpay_key']);
define('JS_API_CALL_URL_', urlencode(HOST.'/api/weixin/pay/wx/js_api_call.php'));
define('NOTIFY_URL_',HOST.'/api/weixin/pay/wx/notify_url.php');
class WxPayConf_pub{
	const MCHID = MCHID_;
	const KEY = KEY_;
	const APPID = APPID_;
	const APPSECRET = APPSECRET_;
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = JS_API_CALL_URL_;
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = '/cacert/apiclient_cert.pem';
	const SSLKEY_PATH = '/cacert/apiclient_key.pem';
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = NOTIFY_URL_;
	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}	
?>
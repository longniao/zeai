<?php
define('APPID_',$_ZEAI['wx_gzh_appid']);
define('APPSECRET_',$_ZEAI['wx_gzh_appsecret']);
define('MCHID_',$_PAY['wxpay_mchid']);
define('KEY_', $_PAY['wxpay_key']);
define('NOTIFY_URL_',HOST.'/api/weixin/pay/refund/notify_url.php');
require_once "lib/WxPay.Config.Interface.php";
class WxPayConfig extends WxPayConfigInterface{
	public function GetAppId(){return APPID_;}
	public function GetMerchantId(){return MCHID_;}
	public function GetNotifyUrl(){return NOTIFY_URL_;}
	public function GetSignType(){return "HMAC-SHA256";}
	public function GetProxy(&$proxyHost, &$proxyPort){$proxyHost = "0.0.0.0";$proxyPort = 0;}
	public function GetReportLevenl(){return 1;}
	public function GetKey(){return KEY_;}
	public function GetAppSecret(){return APPSECRET_;}
	public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath){
		global $_ZEAI;
		$a=explode('/',$_ZEAI['adm2']);$a=$a[3];
		$sslCertPath = ZEAI.$a.'/cert/apiclient_cert.pem';
		$sslKeyPath = ZEAI.$a.'/cert/apiclient_key.pem';
	}
}
<?php
class JSSDK {
	private $appId;
	private $appSecret;
	private $appurl;
	public function __construct($appId,$appSecret,$appurl) { 
		$this->appId = $appId;
		$this->appSecret = $appSecret;
		$this->appurl = $appurl;
	}
	public function getSignPackage() {
		$jsapiTicket = $this->getJsApiTicket();
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url = str_replace("/index.php","/",$url);
		$timestamp = time();
		$nonceStr = $this->createNonceStr();
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		$signPackage = array("appId" => $this->appId,"nonceStr"  => $nonceStr,"timestamp" => $timestamp,"url" => $url,"signature" => $signature,"rawString" => $string );
		return $signPackage;
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) { $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);}
		return $str;
	}
	
	private function getJsApiTicket() {
		$data = json_decode(file_get_contents($this->appurl."cache/wxdata/ticket.json"));
		if ($data->expire_time < time()) {
			$accessToken = wx_get_access_token();
			// 如果是企业号用以下 URL 获取 ticket　　　$url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode(get_contents($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$data->expire_time  = time() + 7000;
				$data->jsapi_ticket = $ticket;
				$fp = fopen($this->appurl."cache/wxdata/ticket.json", "w");
				fwrite($fp, json_encode($data));
				fclose($fp);
			}
		} else {
			$ticket = $data->jsapi_ticket;
		}
		return $ticket;
	}
}
//
$jssdk = new JSSDK($_ZEAI['wx_gzh_appid'],$_ZEAI['wx_gzh_appsecret'],ZEAI);
$signPackage = $jssdk->GetSignPackage();
?>
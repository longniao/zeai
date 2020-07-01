<?php
define('ZEAIPHP',substr(dirname(__FILE__),0,-18));
require_once ZEAIPHP.'sub/init.php';
refresh_access_token();
function refresh_access_token(){
	global $_ZEAI;
	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$_ZEAI['wx_gzh_appid']."&secret=".$_ZEAI['wx_gzh_appsecret'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$data = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($data,true);	
	$data_str = $data;
	$data_str['expire_time'] = time()+3600;
	$data_str = json_encode($data_str);
	$dst = fopen(ZEAI."cache/wxdata/access_token.json","w+");
	fwrite($dst,$data_str);
	fclose($dst);
}
?>
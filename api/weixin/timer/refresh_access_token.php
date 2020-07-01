<?php
require_once 'C:/weblist/0752jiayuan.com/www/sub/init.php';
require_once ZEAI.'sub/conn.php';
$rowc = get_db_content("wxapi_appid,wxapi_appsecret");
$_ZEAI['wxapi_appid']     = $rowc['wxapi_appid'];
$_ZEAI['wxapi_appsecret'] = $rowc['wxapi_appsecret'];
refresh_access_token();
function refresh_access_token(){
	global $_ZEAI;
	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$_ZEAI['wxapi_appid']."&secret=".$_ZEAI['wxapi_appsecret'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$data = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($data,true);	
	$data_str = $data;
	$data_str['expire_time'] = time()+7000;
	$data_str = json_encode($data_str);
	$dst = fopen(ZEAI."up/wxdata/access_token.json","w+");
	fwrite($dst,$data_str);
	fclose($dst);
}
?>
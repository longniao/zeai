<?php 
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:7144100,797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/07/18 by supdes
*/
function base64EncodeImage ($image_file,$ifheader=true) {
    $base64_image = '';
    $image_info = getimagesize($image_file);
    $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
	if($ifheader){
		$base64_image = 'data:'.$image_info['mime'].';base64,'.chunk_split(base64_encode($image_data));
	}else{
		$base64_image = chunk_split(base64_encode($image_data));
	}
    return $base64_image;
}
function msectime() {
	list($msec, $sec) = explode(' ', microtime());
	$msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
	return $msectime;
}

function Zeai_RZ_face_id_card($name,$idcard,$photo) {
	global $_SMS;
	$appid        = $_SMS['rz_appId'];
	$app_security = $_SMS['rz_appSecurity'];
	$timestamp    = msectime();
	$sign = md5($appid.'&'.$timestamp.'&'.$app_security);
	$base64 = base64EncodeImage($photo);
	$post_data= array();
	$post_data["appid"]        = $appid;
	$post_data["timestamp"]    = $timestamp;
	$post_data["app_security"] = $app_security;
	$post_data["sign"]     = $sign;
	$post_data["name"]     = $name;
	$post_data["idcard"]   = $idcard;
	$post_data["image"]    = $base64;
	$ret = Zeai_POST_stream('https://api.shumaidata.com/v2/face_id_card/compare',$post_data);
	$ret = json_decode($ret,true);
	if($ret['code']==200){
		if($ret['data']['score']>0.45 && $ret['data']['incorrect']==100){
			$score = $ret['data']['score'];
			$flag=1;
		}else{
			$flag=2;
		}
		$msg = $ret['data']['msg'];
	}elseif($ret['code']==603){
		$flag=0;
		$msg='接口余额不足请联系客服';
	}else{
		$flag=0;
		$msg=$ret['msg'];
	}
	return array('flag'=>$flag,'msg'=>$msg,'ret'=>$ret);
}
function Zeai_RZ_mob3($name,$idcard,$mobile) {
	global $_SMS;
	$appid        = $_SMS['rz_appId'];
	$app_security = $_SMS['rz_appSecurity'];
	$timestamp    = msectime();
	$sign = md5($appid.'&'.$timestamp.'&'.$app_security);
	$post_data= array();
	$post_data["appid"]        = $appid;
	$post_data["timestamp"]    = $timestamp;
	$post_data["app_security"] = $app_security;
	$post_data["sign"]     = $sign;
	$post_data["name"]     = $name;
	$post_data["idcard"]   = $idcard;
	$post_data["mobile"]   = $mobile;
	$ret = Zeai_POST_stream('https://api.shumaidata.com/v2/mobile_three/check',$post_data);//var_dump($ret);exit;
	$ret = json_decode($ret,true);
	if($ret['code']==200){
		if($ret['data']['result']==0){
			$flag=1;
		}elseif($ret['data']['result']==1){
			$flag=2;
		}elseif($ret['data']['result']==2){	
			$flag=3;
		}
		$msg = $ret['data']['desc'];
	}else{
		$flag=0;
		$msg=$ret['msg'];
	}
	return array('flag'=>$flag,'msg'=>$msg,'ret'=>$ret);
}
?>
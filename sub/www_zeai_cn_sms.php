<?php
function request_post($url = '', $post_data = array()) {
    if (empty($url) || empty($post_data)) {return false;}
    $o = "";
    foreach ( $post_data as $k => $v ){$o.= "$k=" . urlencode( $v ). "&" ;}
    $post_data = substr($o,0,-1);
    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Content-Encoding: utf-8'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    return $data;
}
function Zeai_sendsms_authcode($mobile,$str,$mbkind='authcode') {
	global $_SMS;
	$sid      = $_SMS['sms_sid'];
	$apikey   = $_SMS['sms_apikey'];
	if ($mbkind == 'authcode'){
		$tplid = $_SMS['sms_tplid_authcode'];
	}elseif($mbkind == 'findpass'){
		$tplid = $_SMS['sms_tplid_findpass'];
	}else{exit;}
	$svr_rest = "http://api.rcscloud.cn:8030/rcsapi/rest";// rest请求地址  或使用IP：121.14.114.153
	$content  = "@1@=".$str;// 参数值，多个参数以“||”隔开 如:@1@=HY001||@2@=3281
	$sign     = md5($sid.$apikey.$tplid.$mobile.$content);// 签名认证 Md5(sid+apikey+tplid+mobile+content) 
	$svr_url  = $svr_rest."/sms/sendtplsms.json";// 服务器接口路径
	$post_data= array();
	$post_data["sign"]      = $sign;
	$post_data["sid"]       = $sid;
	$post_data["tplid"]     = $tplid;
	$post_data["mobile"]    = $mobile;
	$post_data["content"]   = $content;
	$json_arr = json_decode(request_post($svr_url, $post_data));
	return $json_arr->code;
}
function sms_error($rtn,$kind='rcscloud'){
	if($kind=='rcscloud'){
		switch ($rtn) {
			case 1001:$c = '账号被关闭';break;
			case 1003:$c = '单次发送号码过多';break;
			case 1005:$c = '账号签名参数有误';break;
			case 1006:$c = 'IP签权失败';break;
			case 1007:$c = '余额不足';break;
			case 1014:$c = '未找到对应id短信模板';break;
			case 1015:$c = '对应id短信模板不可用';break;
			case 1022:$c = '同一个手机号码在单位时间内超过发送次数';break;
			default:$c = '其它异常，请参阅【常见返回码】';break;
		}
	}
	return $c;
}
?>

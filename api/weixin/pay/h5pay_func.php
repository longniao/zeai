<?php 
class www_zeai_cn_h5pay_class{
	public $mch_appid = APPID_;
	public $mchid = MCHID_;
	//public $check_name = "NO_CHECK"; //FORCE_CHECK针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
	public $desc = SITENAME_;
	public $keys= KEY_;
	public function siteip(){
		$headers = array('HTTP_X_REAL_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
		foreach ($headers as $h){
			$ip = $_SERVER[$h];
			if ( isset($ip) && strcasecmp($ip, 'unknown') ){
				break;
			}
		}
		if( $ip ){
			list($ip) = explode(', ', $ip, 2);
		}
		return $ip;
	}
	//输出xml字符
	public function ToXml($arr){
		if(!is_array($arr) || count($arr) <= 0)exit("数组数据异常！");    	
    	$xml = "<xml>";
    	foreach ($arr as $key=>$val){$xml.="<".$key.">".$val."</".$key.">";}
        $xml.="</xml>";
        return $xml; 
	}
    //将xml转为array
	public function FromXml($xml){
		if(!$xml){exit("xml数据异常！");}        
        //libxml_disable_entity_loader(true);//禁止引用外部xml实体
        $arr_val = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $arr_val;
	}		
	//生成签名
	public function MakeSign($data){		
		ksort($data);//排序参数
		$buff = "";
		foreach ($data as $k => $v){if($k != "sign" && $v != "" && !is_array($v)){$buff .= $k . "=" . $v . "&";}}		
		$string = trim($buff, "&");	
		$string = $string . "&key=".$this->keys;//加入KEY
		$string = md5($string);  //MD5加密		
		$result = strtoupper($string);//所有字符转为大写
		return $result;
	}
	public function get_rand_str($length){
		$possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";$str = "";
		while(strlen($str) < $length) $str .= substr($possible, (rand() % strlen($possible)), 1);
		return($str);	
	}
	public function curl_post_ssl($vars,$second=30){
		//echo getcwd().'/cert/apiclient_cert.pem';exit;
		//$url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		$url= 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);	
		//curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		//curl_setopt($ch,CURLOPT_SSLCERT,CERTPATH_.'apiclient_cert.pem');
		//curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		//curl_setopt($ch,CURLOPT_SSLKEY,CERTPATH_.'apiclient_key.pem');	
		//curl_setopt($ch,CURLOPT_CAINFO,CERTPATH_.'rootca.pem'); 
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		//echo curl_errno($ch);
		curl_close($ch);
		return $data;
	}
	public function reback_err($errid,$err_money=100){
		switch($errid){
		   case "NOAUTH": return "对不起，您的API权限不足。"; break;
		   case "NO_AUTH": return "产品权限验证失败,检查微信支付平台公众号向粉丝打款功能模块是否开启(产品中心 > 企业付款到零钱)"; break;
		   case "AMOUNT_LIMIT": return "付款金额不能小于最低限额,每次付款金额必须不小于1元"; break;
		   case "PARAM_ERROR": return "对不起，请求参数出错。参数缺失，或参数格式出错，参数不合法等。"; break;
		   case "OPENID_ERROR": return "对不起，收款账户信息（OPENID）验证失败。"; break;
		   case "NOTENOUGH": return "对不起，公司账户余额不足¥".$err_money."元，无法完成本次支付请求。"; break;
		   case "SYSTEMERROR": return "系统繁忙，请稍后再试。<br>可能原因：网络卡顿延时，或真实姓名校验出错！"; break;
		   case "NAME_MISMATCH": return "对不起，姓名校验出错，填写正确的用户真实姓名。"; break;
		   case "SIGN_ERROR": return "对不起，签名错误。"; break;
		   case "XML_ERROR": return "对不起，提交数据不合法。"; break;
		   case "FATAL_ERROR": return "对不起，证书出错。"; break;
		}	
	}
}
?>
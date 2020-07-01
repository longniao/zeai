<?php
require_once '../../sub/init.php';
$wx = new Zeai_weixin_menu();
$wx->app_menu();
class Zeai_weixin_menu {	
	//获得凭证接口
	//返回数组，access_token 和  time 有效期 
	public function access_token() {
		global $_ZEAI;
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$_ZEAI['wx_gzh_appid']."&secret=".$_ZEAI['wx_gzh_appsecret'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$data = curl_exec($ch);		
		return json_decode($data,1);
	}
   //创建自定义菜单
	function app_menu() {
		global $_ZEAI;			
        $access_token = $this -> access_token();
		$data ='{
		     "button":[
						{"type":"view","name":"V6.0版即将上线，敬请期待！","url":"http://www.zeai.cn/v6.html"}

					 ]
		}';	
		//{"type":"view","name":"推广赚钱","url":"'.$_ZEAI['m_2domain'].'/my/tgewm.php"},
		//{"type":"click","name":"测试","key":"T001"}
		$ch_url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token['access_token'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$ch_url);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
		///print_r($data);
		$data = curl_exec($ch);
		
	}
}

/*
		$data ='{
		     "button":[{"type":"view","name":"sssssss","url":"http://www.zeai.cn"}]
		}';	

*/
?>
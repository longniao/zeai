<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';

//$redirect_uri = urlencode(HOST.'/m1/subscribe.php');
function wx_authorized_get_uinfo($token,$openid){
	$data = del_emoji(get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=".$token."&openid=".$openid."&lang=zh_CN"));
	$data = json_decode($data,1);
	return $data;
}

function wx_authorized_login(){
	global $_ZEAI;
	$code=$_GET['code'];
	if(empty($code)){
		header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$_ZEAI['wx_gzh_appid']."&redirect_uri=".urlencode(HOST.$_SERVER['REQUEST_URI'])."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect");
	}else{
		$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$_ZEAI['wx_gzh_appid']."&secret=".$_ZEAI['wx_gzh_appsecret']."&code=".$code."&grant_type=authorization_code";
		$data = get_contents($url);
		$data = json_decode($data,true);
		$openid  = $data['openid'];
		$u_token = $data['access_token'];
		$Uinfo   = wx_authorized_get_uinfo($u_token,$openid);
		
		echo'<font style="font-size:18px">';
		echo encode_json($Uinfo);
		echo '</font>';
		exit;
		
/*		$openid = $data['openid'];
		if (str_len($openid) > 10 ){
			setcookie('cook_openid',$openid,time()+720000,'/',$_ZEAI['CookDomain']);
			return $openid;
		}else{
			setcookie('cook_openid','',time()+720000,'/',$_ZEAI['CookDomain']);
			return '';
		}
		
*/		
	}
}

wx_authorized_login();




exit;



//$openid = wx_get_openid();
if (!empty($openid)){
	$gznext = false;
	$row = $db->ROW(__TBL_USER__,"id,uname,pwd,openid,subscribe,sex,grade,nickname,birthday","openid='$openid' AND openid<>'' AND flag=1",'name');
	if ($row){
		$data_uid     = intval($row['id']);
		$data_uname   = dataIO($row['uname'],'out');
		$data_pwd     = dataIO($row['pwd'],'out');
		$data_openid  = dataIO($row['openid'],'out');
		$data_subscribe= intval($row['subscribe']);
		$data_sex     = intval($row['sex']);
		$data_grade   = intval($row['grade']);
		$data_nickname= dataIO($row['nickname'],'out');
		$data_birthday= $row['birthday'];
		setcookie("cook_uid",$data_uid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_uname",$data_uname,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_pwd",$data_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_openid",$data_openid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_subscribe",$data_subscribe,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_sex",$data_sex,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_grade",$data_grade,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_nickname",$data_nickname,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_birthday",$data_birthday,time()+720000,"/",$_ZEAI['CookDomain']);
		if ($data_subscribe == 1){
			header("Location: ".urldecode($jumpurl));
		}else{
			$gznext = true;
		}
	}else{
		$gznext = true;
	}
}
if ($gznext){
	$headertitle = '我的';$nav = 'my';
	require_once ZEAI.'m1/header.php';
	$mini_backT = '';
	$mini_title = '请先关注公众号';
	require_once ZEAI.'m1/top_mini.php';
	?>
	<style>
	.boxC{width:100%;background-color:#fff;text-align:left;padding:0 0 20px;margin-top:60px}
	.boxC img,.boxC em{width:80%;margin:0 auto;display:block}
	.boxC em{font-size:16px;line-height:150%}
	.boxC em font{color:#06BC07}
	</style>
	</head>
	<body>
	<div class="boxC"><img src="<?php echo $_ZEAI['up2'];?>/p/img/subscribe.jpg"><em>长按以上二维码图片不松开，在弹出菜单中选择 “<font>识别图中二维码</font>” 添加公众号！</em></div>
	<?php
	require_once ZEAI.'m1/footer.php';	
	echo '</body></html>';
}
?>
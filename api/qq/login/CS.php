<?php
require_once '../../../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_login.php';
function qq_authorized_login(){
	global $_ZEAI,$db,$_LOGIN,$jumpurl;
	$code=$_GET['code'];
	if(empty($code)){
		header("Location: https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=".$_LOGIN['qq_login_appid']."&redirect_uri=".urlencode(HOST."/api/qq/login/CS.php")."&scope=get_user_info&state=zeai");
	}else{
		//getaccess_token
		$url="https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=".$_LOGIN['qq_login_appid']. "&redirect_uri=" . urlencode(HOST."/api/qq/login/CS.php") . "&client_secret=" .$_LOGIN['qq_login_appkey']. "&code=" . $code;
		$data = get_contents($url);
		$params = array();
		$data = parse_str($data,$params);
		$access_token = $params["access_token"];
		//getopenid
		$data = get_contents("https://graph.qq.com/oauth2.0/me?access_token=".$access_token);
		if (strpos($data, "callback") !== false){
			$lpos = strpos($data,"(");
			$rpos = strrpos($data,")");
			$data = substr($data, $lpos + 1, $rpos - $lpos -1);
		}
		$data   = json_decode($data);
		$openid = $data->openid;
		//
		if (str_len($openid)>10){
			//echo "SELECT id,uname,nickname,pwd,sex,photo_s,grade,birthday FROM ".__TBL_USER__." WHERE loginkey='$openid' AND (flag=1 || flag=-2)";exit;
			$rt = $db->query("SELECT id,uname,nickname,pwd,sex,photo_s,grade,birthday FROM ".__TBL_USER__." WHERE loginkey='$openid' AND (flag=1 || flag=-2)");
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt);
				setcookie("cook_uid",$row[0],null,"/",$_ZEAI['CookDomain']);
				setcookie("cook_pwd",$row[3],null,"/",$_ZEAI['CookDomain']);
				setcookie("cook_sex",$row[4],null,"/",$_ZEAI['CookDomain']);
				setcookie("cook_photo_s",$row[5],null,"/",$_ZEAI['CookDomain']);
				setcookie("cook_uname",dataIO($row[1],'out'),null,"/",$_ZEAI['CookDomain']);
				setcookie("cook_nickname",urldecode(dataIO($row[2],'out')),null,"/",$_ZEAI['CookDomain']);
				setcookie("cook_grade",$row[6],null,"/",$_ZEAI['CookDomain']);
				setcookie("cook_birthday",$row[7],null,"/",$_ZEAI['CookDomain']);
				$uid = $row[0];
				$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",endip='$loginip',logincount=logincount+1 WHERE id=".$uid);
				$chkflag = 1;
				$jumpurl = urldecode($jumpurl);
				$jumpurl = (empty($jumpurl) || strpos($jumpurl,'login.php') !== false  )?HOST.'/?z=my':$jumpurl;
				header("Location: ".$jumpurl);
			}else{
				//getuinfo
				$info = del_emoji(get_contents("https://graph.qq.com/user/get_user_info?". "access_token=" . $access_token. "&oauth_consumer_key=" . $_LOGIN['qq_login_appid']. "&openid=" . $openid. "&format=json"));
				$D = json_decode($info);
				$nickname=dataIO($D->nickname);
				$TMP = array();$gender = ($D->gender=='女')?2:1;
				$TMP['regkind']='qq';
				$TMP['openid']=$openid;
				$TMP['nickname']=dataIO($D->nickname,'in');
				$TMP['sex']=$gender;
				$TMP['province']=$D->province;
				$TMP['city']=$D->city;
				$TMP['photo_s']=$D->figureurl_qq_2;
				$TMP = encode_json($TMP);
				//
 				$row = $db->ROW(__TBL_TMP__,"id","c='$TMP'");
				if ($row){
					$tmpid= $row[0];
				}else{
					$db->query("INSERT INTO ".__TBL_TMP__."(c,addtime) VALUES ('$TMP',".ADDTIME.")");
					$tmpid = $db->insert_id();
				}
				header("Location: ".HOST."/".MP."/login.php?tmpid=".$tmpid);
				//callmsg($nickname.'，您第一次来吧，请先花1分钟注册一下哦~',HOST.'/'.MP.'/reg.php?regkind=qq&tmpid='.$tmpid);
			}
		}
	}
}
function qq_bind(){
	global $_ZEAI,$db,$_LOGIN,$jumpurl,$cook_uid;
	$code=$_GET['code'];
	if(empty($code)){
		header("Location: https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=".$_LOGIN['qq_login_appid']."&redirect_uri=".urlencode(HOST.$_SERVER['REQUEST_URI'])."&scope=get_user_info&state=zeai");
	}else{
		//getaccess_token
		$url="https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=".$_LOGIN['qq_login_appid']. "&redirect_uri=".urlencode(HOST.$_SERVER['REQUEST_URI'])."&client_secret=" .$_LOGIN['qq_login_appkey']. "&code=" . $code;
		$data = get_contents($url);
		$params = array();
		$data = parse_str($data,$params);
		$access_token = $params["access_token"];
		//getopenid
		$data = get_contents("https://graph.qq.com/oauth2.0/me?access_token=".$access_token);
		if (strpos($data, "callback") !== false){
			$lpos = strpos($data,"(");
			$rpos = strrpos($data,")");
			$data = substr($data, $lpos + 1, $rpos - $lpos -1);
		}
		$data   = json_decode($data);
		$openid = $data->openid;
		//
		if (str_len($openid)>10){
			$db->query("UPDATE ".__TBL_USER__." SET loginkey='$openid' WHERE id=".$cook_uid);
			header("Location: ".HOST."/p1/my_set.php?t=1&flag=1");
		}
	}
}

if($submitok=='qq_bind'){
	if(!iflogin())header("Location: ".HOST.'/p1/login.php');
qq_bind();
}else{qq_authorized_login();}
?>
<?php
require_once '../../../sub/init.php';
require_once ZEAI.'sub/conn.php';

function wx_authorized_login(){
	global $_ZEAI,$db,$tguid,$jumpurl;
	$code=$_GET['code'];
	if(empty($code)){
		if(is_mobile() && is_weixin()){
			header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$_ZEAI['wx_gzh_appid']."&redirect_uri=".urlencode(HOST.$_SERVER['REQUEST_URI'])."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect");
		}else{
			require_once ZEAI.'cache/config_login.php';
			$state = md5('作者QQ797311郭余林'.ADDTIME);
			header("Location: https://open.weixin.qq.com/connect/qrconnect?appid=".$_LOGIN['wx_open_appid']."&redirect_uri=".HOST.$_SERVER['REQUEST_URI']."&response_type=code&scope=snsapi_login&state=".$state."#wechat_redirect");
		}
	}else{
		if(is_mobile() && is_weixin()){
			$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$_ZEAI['wx_gzh_appid']."&secret=".$_ZEAI['wx_gzh_appsecret']."&code=".$code."&grant_type=authorization_code";
		}else{
			require_once ZEAI.'cache/config_login.php';
			$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$_LOGIN['wx_open_appid']."&secret=".$_LOGIN['wx_open_appsecret']."&code=".$code."&grant_type=authorization_code";
		}
		$data = get_contents($url);
		$data = json_decode($data,true);
		$openid  = $data['openid'];
		$unionid = $data['unionid'];
		$u_token = $data['access_token'];
		if(is_mobile() && is_weixin()){
			$SQL = "(  openid='$openid' AND openid<>'' OR unionid='$unionid' AND unionid<>''  )";
		}else{
			$SQL = "unionid='$unionid' AND unionid<>''";
		}
		if (str_len($openid)>20 || str_len($unionid)>20){
			$rt = $db->query("SELECT id,uname,nickname,pwd,sex,photo_s,grade,birthday FROM ".__TBL_USER__." WHERE ".$SQL." AND (flag=1 || flag=-2)");
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt,'num');
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
				$jumpurl = (empty($jumpurl) || strpos($jumpurl,'login.php') !== false  )?mHref('my'):$jumpurl;
				header("Location: ".$jumpurl);
			}else{
				$Uinfo = del_emoji(get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=".$u_token."&openid=".$openid."&lang=zh_CN"));
				$TMP = json_decode($Uinfo,true);
				$TMP['regkind']='weixin';
				$nickname = dataIO($TMP['nickname']);
				$TMP = encode_json($TMP);$TMP = str_replace("'","",$TMP);
 				$row = $db->ROW(__TBL_TMP__,"id","c='$TMP'","num");
				if ($row){
					$tmpid= $row[0];
				}else{
					$TMP = str_replace("'","",$TMP);
					$db->query("INSERT INTO ".__TBL_TMP__."(c,addtime) VALUES ('$TMP',".ADDTIME.")");
					$tmpid = $db->insert_id();
				}
				$jumpurl = urldecode($jumpurl);
				if(is_mobile() && is_weixin()){
					header("Location: ".HOST.'/m1/login.php?tguid='.$tguid.'&tmpid='.$tmpid.'&jumpurl='.$jumpurl);
				}else{
					header("Location: ".HOST.'/p1/login.php?tguid='.$tguid.'&tmpid='.$tmpid.'&jumpurl='.$jumpurl);
				}
			}
		}else{callmsg($openid);}
	}
}
wx_authorized_login();
?>
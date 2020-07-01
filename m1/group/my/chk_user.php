<?php 
require_once ZEAI.'sub/conn.php';
if(!check_wxweb()){echo "请在微信中打开";exit;}
if(isset($cook_openid) && !empty($cook_openid) && isset($cook_unionid) && !empty($cook_unionid) && isset($cook_subscribe) && !empty($cook_subscribe) ){
	$server_openid    = $cook_openid;
	$server_unionid   = $cook_unionid;
	$server_subscribe = $cook_subscribe;
}else{
	$token            = get_wx_access_token();
	$server_openid    = get_wx_openid();
	$wxuinfo          = get_wx_userinfo($token,$server_openid);
	$server_unionid   = $wxuinfo['unionid'];
	$server_subscribe = $wxuinfo['subscribe'];
	setcookie("cook_openid",$server_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
	setcookie('cook_unionid',$server_unionid,time()+7200000,'/',$_ZEAI['CookDomain']);
	setcookie('cook_subscribe',$server_subscribe,time()+7200000,'/',$_ZEAI['CookDomain']);
}
if (empty($server_openid)){
	echo "获取微信参数失败";exit;
}else{
	//没关注直接跳转关注
	if ($server_subscribe != 1){header("Location: ".HOST."/login.php");exit;}
	//已关注下一步
	$currfields = (empty($currfields))?"":",".$currfields;
	$rt = $db->query("SELECT id,password,sex,openid,unionid,grade,nickname,flag,subscribe,myinfobfb,birthday".$currfields." FROM ".__TBL_USER__." WHERE openid='".$server_openid."' OR unionid='".$server_unionid."'");
	if ($db->num_rows($rt)) {
		$row = $db->fetch_array($rt,'name');
		$data_uid     = intval($row['id']);
		$data_openid  = $row['openid'];
		$data_unionid = $row['unionid'];
		$data_nickname= dataIO($row['nickname'],'out');
		$data_grade   = intval($row['grade']);
		$data_flag    = $row['flag'];
		$data_sex     = intval($row['sex']);
 		$data_pwd     = $row['password'];
		$data_subscribe= $row['subscribe'];
		$data_myinfobfb= $row['myinfobfb'];
		$data_birthday = $row['birthday'];
		if ($data_subscribe != 1){$db->query("UPDATE ".__TBL_USER__." SET subscribe=1 WHERE id=".$data_uid);}
		if($data_flag != 1){echo '账户已被锁定！';exit;}
		//只PC登录,更新openid
		if (str_len($data_openid) < 10)$db->query("UPDATE ".__TBL_USER__."  SET openid ='".$server_openid."'  WHERE id=".$data_uid);
		if (str_len($data_unionid) < 10)$db->query("UPDATE ".__TBL_USER__." SET unionid='".$server_unionid."' WHERE id=".$data_uid);
		//pc mob 都正常
		setcookie("cook_uid",$data_uid,time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_nickname",$data_nickname,time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_sex",$data_sex,time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_openid",$data_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_unionid",$data_unionid,time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_pwd",$data_pwd,time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_grade",$data_grade,time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_birthday",$data_birthday,time()+7200000,"/",$_ZEAI['CookDomain']);
		$cook_uid = (!ifint($cook_uid))?$data_uid:$cook_uid;
	}else{
		//已关注，PC或微信 库没记录的老粉丝
		$token      = get_wx_access_token();
		$wxuinfo    = get_wx_userinfo($token,$server_openid);
		$ip         = getip();
		$RegLoveb = abs(intval($_ZEAI['RegLoveb']));
		$db->query("INSERT INTO ".__TBL_USER__." (openid,unionid,password,loveb,regtime,endtime,regip,endip,refresh_time,subscribe,regkind) VALUES ('".$server_openid."','".$server_unionid."','eb72c92a54_www.zeai.cn_d5a330112',".$RegLoveb.",$ADDTIME,$ADDTIME,'$ip','$ip',$ADDTIME,1,3)");
		$uid = intval($db->insert_id());
		if ($RegLoveb > 0)$db->AddHistoryList($uid,'新用户注册',$RegLoveb);
		//
		$dbname   = (!empty($wxuinfo['headimgurl']))?get_userlogo_my($wxuinfo['headimgurl'],$uid):'';
		$photo_s  = setpath_s_my($dbname);
		$nickname = urlencode($wxuinfo['nickname']);
		$sex      = intval($wxuinfo['sex']);
		$province = $wxuinfo['province'];
		$city     = $wxuinfo['city'];
		$areatitle= $province.' '.$city;
		$sex = (empty($sex))?1:$sex;
		$db->query("UPDATE ".__TBL_USER__." SET nickname='$nickname',sex=".$sex.",photo_s='$photo_s',photo_f=0,areatitle='$areatitle' WHERE id=".$uid);
		$arr=array('msgType'=>'text','contentStr'=>urldecode($nickname).'你好，感谢您关注【'.$_ZEAI['SiteName'].'】，请从底部公众号菜单进入体验！');
		//
		setcookie("cook_uid",$uid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_nickname",$nickname,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_openid",$server_openid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_unionid",$cook_unionid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_subscribe",$server_subscribe,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_pwd",'eb72c92a54_www.zeai.cn_d5a330112',time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_birthday",'',time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_grade",1,time()+720000,"/",$_ZEAI['CookDomain']);
		$cook_uid = (!ifint($cook_uid))?$uid:$cook_uid;
		//header("Location: ../subscribe.php");
	}
}

function get_userlogo_my($headimgurl,$uid){
	global $_ZEAI;
	$file      = get_contents($headimgurl);
	$dbpicname = setphotodbname_my($_ZEAI['UpPath'].'/m','',$uid);
	if (!up_send_wx_my($file,$dbpicname,$_ZEAI['ifwaterimg'],$_ZEAI['UpSsize'],$_ZEAI['UpBsize'],$_ZEAI['UpMsize'])){
		return '请联系原作者QQ797311';
	}else{
		return $dbpicname;
	}
}
function setphotodbname_my($photodir,$pre='',$uid=''){
	global $ADDTIME;
	$ftype = 'jpg';
	$pre = (!empty($pre))?$pre:'';
	$dbdir = $photodir.'/'.date('Y').'/'.date('m').'/';
	if (!empty($uid)){
		$dbname = $dbdir.$pre.$uid.'.'.$ftype;
	}else{
		$dbname = $dbdir.$pre.$ADDTIME.cdnumletters(3).'.'.$ftype;
	}
	return $dbname;
}
function up_send_wx_my($file,$dbname,$waterimg=0,$smallsize='',$bigsize='',$middlesize=''){  
	global $_ZEAI;
	$tmp_name = $file;
	$url  = $_ZEAI['up2'].'/up.php?filename='.$dbname;
	$url .= "&waterimg=$waterimg&bigsize=$bigsize&smallsize=$smallsize&middlesize=$middlesize";
	if(!empty($tmp_name)){
		$ret = Zeai_POST_stream($url,$tmp_name);
		//$ret = json_decode($ret, true);
		//return $ret;
		return true;  
	}else{  
		return false;  
	}  
}
function setpath_s_my($ds){
	$ds_len = strlen($ds);
	$ds_r   = substr($ds,-4);
	$s = substr($ds,0,$ds_len-4)."_s".$ds_r;
	return $s;
}
?>
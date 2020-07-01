<?php
//if (!is_mobile())exit('请用手机打开');
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
if($TG_set['force_weixin']==1 && !is_weixin() && !is_h5app())exit('请在微信中打开');
if(is_weixin()){
	if(isset($cook_tg_openid) && !empty($cook_tg_openid) ){
		$server_tg_openid = $cook_tg_openid;
	}else{
		$server_tg_openid = wx_get_openid(0);
		setcookie("cook_tg_openid",$server_tg_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
	}
	if(!empty($server_tg_openid)){
		$rowtg = $db->ROW(__TBL_TG_USER__,"id,uname,mob,pwd,uid","openid<>'' AND openid='$server_tg_openid'","name");
		if ($rowtg){
			$cook_tg_uid   = $rowtg['id'];
			$uidd          = intval($rowtg['uid']);
			$cook_tg_uname = $rowtg['uname'];
			$cook_tg_mob   = $rowtg['mob'];
			$cook_tg_pwd   = $rowtg['pwd'];
			if(!ifint($uidd) && ifint($cook_uid) )$db->query("UPDATE ".__TBL_TG_USER__." SET uid=".$cook_uid." WHERE id=".$cook_tg_uid);
			setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_mob",$cook_tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_openid",$server_tg_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
		}
	}
	$LNSQL=" AND id=".$cook_tg_uid." AND pwd='".$cook_tg_pwd."'";
}else{
	$LNSQL=" AND id=".$cook_tg_uid." AND pwd='".$cook_tg_pwd."'";
}
if (!ifint($cook_tg_uid)){header("Location: tg_login.php");exit;}

$currfields = (empty($currfields))?"":",".$currfields;
$rt = $db->query("SELECT id,subscribe,flag".$currfields." FROM ".__TBL_TG_USER__." WHERE 1=1".$LNSQL);
if ($db->num_rows($rt)) {
	
	$row = $db->fetch_array($rt,'name');
	$data_tg_uid = intval($row['id']);
	$data_tg_subscribe = intval($row['subscribe']);
	$data_flag = $row['flag'];
	//if($TG_set['force_subscribe']==1 && $data_tg_subscribe==0 && is_weixin()){header("Location: tg_subscribe.php");exit;}

	switch ($data_flag) {
		case 0:header("Location: tg_reg.php?submitok=flag0");break;
		case -1:header("Location: tg_reg.php?submitok=flag_1");break;
		case 2:
		//if($TG_set['active_price']>0){
			header("Location: tg_reg.php?submitok=flag2");
		//}
		break;
	}
		
}else{
	header("Location: tg_login.php");
}
?>
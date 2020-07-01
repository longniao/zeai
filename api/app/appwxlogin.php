<?php
require_once '../../sub/init.php';
require_once ZEAI.'sub/conn.php';
if (str_len($unionid)>20 && $submitok=="ajax_appwxlogin"){
	$SQL = "unionid='$unionid' AND unionid<>''";
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
		echo json_encode(array('status'=>'OK','msg'=>'登录成功','uid'=>$uid));
	}else{
		$APPTMP['unionid']=$unionid;
		$APPTMP['regkind']='app';
		$APPTMP = encode_json($APPTMP);
		$row = $db->ROW(__TBL_TMP__,"id","c='$APPTMP'","num");
		if ($row){
			$tmpid= $row[0];
		}else{
			$db->query("INSERT INTO ".__TBL_TMP__."(c,addtime) VALUES ('$APPTMP',".ADDTIME.")");
			$tmpid = $db->insert_id();
		}
		$jumpurl = urldecode($jumpurl);
		echo json_encode(array('status'=>'regedit','msg'=>'未注册会员，请先注册','url'=>HOST.'/m1/login.php?tguid='.$tguid.'&tmpid='.$tmpid.'&jumpurl='.$jumpurl));
	}
}else{
	echo json_encode(array('status'=>'error','msg'=>'未会员身份标识'));
}


?>
<?php
$jmpurl = HOST."/p1/login.php";
if ( !ifint($cook_uid) || empty($cook_uid) || !isset($cook_uid) )header("Location: $jmpurl");
require_once ZEAI.'sub/conn.php';
$currfields = (empty($currfields))?"":",".$currfields;
$rt = $db->query("SELECT id".$currfields." FROM ".__TBL_USER__." WHERE id=".$cook_uid." AND (flag=1 OR flag=-2) AND pwd='".$cook_pwd."'");
if ($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'name');
}else{
	ZEclearcookAI_CN();
	header("Location: $jmpurl");
}
$_Style['list_bg1']       = '#ffffff';//id=1d
$_Style['list_bg2']       = '#ffffff';//id=2
$_Style['list_overbg']    = '#fcfcfc';//MouseOver
$_Style['list_selectbg']  = '#f5f5f5';//Selected
if ($drname == 'my' && $flname != 'index.php' && $flname != 'my_info.php' && $flname != 'photo_s_cut.php' &&( empty($cook_nickname) || empty($cook_birthday) || $cook_birthday=='0000-00-00'   )){
	//header("Location: myinfo.php");
	callmsg('亲！请先完善基本资料～',HOST.'/p1/my_info.php');
}

$up2=$_ZEAI['up2'].'/';
//$_ZEAI['loveBrate']=100;

?>
<?php
require_once"../../sub/init.php";
require_once ZEAI.'sub/conn.php';
$loginip=getip();
$rt = $db->query("SELECT id FROM ".__TBL_IP__." WHERE ipurl='$loginip'");
if($db->num_rows($rt))exit($json_error);
switch ($submitok) {
	case 'ajax_chklogin':echo chklogin2();break;
}


function chklogin2($uid=''){
	global $db,$cook_uid;
	if(!ifint($uid) && !empty($uid))exit($json_error);
	if(!ifint($cook_uid))exit(json_encode(array('flag'=>'nologin','msg'=>'亲！请您先登录～','jumpurl'=>HOST."/?z=my")));
	if(!$db->NUM($cook_uid))exit(json_encode(array('flag'=>'nologin','msg'=>'亲！请您先登录～','jumpurl'=>HOST."/?z=my")));
	return json_encode(array('flag'=>'oklogin','jumpurl'=>HOST."/?z=my"));
}

?>
<?php
/***************************************************
www.esyyw.com V6.0 作者: 李林　QQ:721688068 (supdes)
***************************************************/
require_once '../sub/init.php';
if (!ifint($uu) || str_len($pp) != 32)exit;
require_once ZEAI.'sub/conn.php';
$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id=".$uu." AND password='$pp'");
if (!$db->num_rows($rt)){exit;}
$zipnames = ZEAI.'up/p/data/zeaiup.zip';
$jumpurl = $_ZEAI['adm2']."/backup.php";
switch ($submitok){
	case "bakup":
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){ 
			@set_time_limit(0);
			require_once ZEAI."sub/Zeai_zip.php";
			$z = new PclZip($zipnames);
			$v_list = $z->create(ZEAI.'up/p',PCLZIP_OPT_REMOVE_PATH,ZEAI.'up/p');
			if ($v_list == 0){
				die("Error : ".$z->errorInfo(true));
			}
			alert_adm("操作成功!",$jumpurl);
		}
	break;
	case "delup":
		if (file_exists($zipnames)){
			unlink($zipnames);
			alert_adm("删除成功!",$jumpurl);
		}else{
			alert_adm("暂没有发现备份集!",$jumpurl);
		}
	break;
}
?>
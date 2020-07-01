<?php
require_once '../../../sub/init.php';
define('ZEAI2',realpath(dirname(__FILE__).'/../../').DIRECTORY_SEPARATOR);
require_once ZEAI2."chkUadm.php";
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once 'JSON.php';
$tmp_name = $_FILES["imgFile"]['tmp_name'];
if(PHP_VERSION < 6 && !get_magic_quotes_gpc()) {
	$tmp_name=stripslashes($tmp_name);
}
$json = new Services_JSON();
if (is_uploaded_file($tmp_name)){
	$file = $_FILES["imgFile"];
	if ($dir=='media'){
		$extend = pathinfo($_FILES["imgFile"]['name']); 
		$extend = strtolower($extend["extension"]); 
		$dbname = setVideoDBname('editor','adm_'.$session_uid.'_',$extend);
		if (!up_send($file,$dbname,'WWW','ZEAI','CN','supdesQQ797311','video')){
			alert_adm('zeai_movepic_err_QQ797311',"-1");
		}else{
			echo $json->encode(array('error'=>0,'url'=>$_ZEAI['up2'].'/'.$dbname));
		}
	}else{
		$dbpicname = setphotodbname('editor',$file['tmp_name'],'adm_'.$_SESSION["admuid"].'_');
		if (!up_send($file,$dbpicname,$_UP['ifwaterimg'],$_UP['upBsize']))alert_adm('zeai_movepic_err_QQ797311',"-1");
		$file_url = urlencode($_ZEAI['up2'].'/'.$dbpicname);
		echo $json->encode(array('error'=>0,'url'=>dataIO($file_url,'out')));
	}
	exit;
}
exit;?>
<?php
/**************************************************
QQ:797311 (supdes) Zeai.cn V6
**************************************************/
error_reporting(E_ALL & ~E_NOTICE);
if (!ini_get('register_globals')){extract($_POST);extract($_GET);extract($_SERVER);extract($_FILES);extract($_COOKIE);}
define('ZEAI',substr(dirname(__FILE__),0,-3));
$_ZEAI = array();require_once ZEAI.'cache/config.php';
$HOSTtmp = substr(dirname(dirname(__FILE__)),strlen($_SERVER['DOCUMENT_ROOT'])+1,50);$HOSTtmp_fg = !empty($HOSTtmp)?'/':'';
$http = (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on")?'http:':'https:';
define('HOST',$http.'//'.$_SERVER['HTTP_HOST'].$HOSTtmp_fg.$HOSTtmp);
require_once ZEAI.'sub/func.php';
if(PHP_VERSION < 6 && !get_magic_quotes_gpc()) {
	$_GET = daddslashes($_GET,1,true);
	$_POST = daddslashes($_POST,1,true);
	$_COOKIE = daddslashes($_COOKIE,1,true);
	$_REQUEST = daddslashes($_REQUEST,1,true);
	$_SESSION = daddslashes($_SESSION,1,true);
	$_SERVER = daddslashes($_SERVER);
	$_FILES = daddslashes($_FILES);
}
const JSON_ERROR = '{"flag":"zeai_error","msg":"网络故障或会员已隐藏"}';
const HEADMETA = '<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, minimum-scale=1, viewport-fit=cover, user-scalable=no"><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black">';$rtn = $dbvar.'_cn__ch'.'k_u';
define('SELF',$_SERVER['PHP_SELF']);
$_ZEAI['ver'] = '6.8.3';
define('ADDTIME',time());
header("content-Type: text/html; charset=utf-8");
$CookDomain=substr(HOST,strpos(HOST,'.'),99);
$_ZEAI['CookDomain']=str_replace('http://', '', $CookDomain);
$navarr = json_decode($_ZEAI['nav'],true);
?>
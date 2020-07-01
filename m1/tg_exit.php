<?php
require_once "../sub/init.php";
setcookie("cook_tg_uid","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_mob","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_uname","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_pwd","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_kind","",null,"/",$_ZEAI['CookDomain']);
setcookie("Temp_regyzmrenum","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_openid","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_subscribe","",null,"/",$_ZEAI['CookDomain']);
header("Location: tg_index.php");
?>
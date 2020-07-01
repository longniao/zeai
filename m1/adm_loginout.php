<?php
require_once "../sub/init.php";
setcookie("cook_admauthoritylist","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_admid","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_admuname","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_admtruename","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_admpwd","",null,"/",$_ZEAI['CookDomain']);
header("Location: adm_login.php");
?>
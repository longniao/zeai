<?php
require_once "sub/init.php";
setcookie("cook_uid","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_uid","",null,"/",'');
setcookie("cook_uname","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_pwd","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_pwd","",null,"/",'');
setcookie("cook_sex","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_grade","",null,"/",$_ZEAI['CookDomain']);  
setcookie("cook_mob","",null,"/",$_ZEAI['CookDomain']);  
setcookie("cook_nickname","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_openid","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_openid","",null,"/",'');
setcookie("cook_unionid","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_subscribe","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_photo_s","",null,"/",$_ZEAI['CookDomain']); 
setcookie("cook_birthday","",null,"/",$_ZEAI['CookDomain']); 
setcookie('cook_my_bounce'.YmdHis(ADDTIME,'d').'my_vip',"",null,"/",$_ZEAI['CookDomain']); 
setcookie('cook_my_bounce'.YmdHis(ADDTIME,'d').'my_info',"",null,"/",$_ZEAI['CookDomain']); 
setcookie('cook_my_bounce'.YmdHis(ADDTIME,'d').'my_rz',"",null,"/",$_ZEAI['CookDomain']); 
setcookie('cook_index_bounce'.YmdHis(ADDTIME,'d'),"",null,"/",$_ZEAI['CookDomain']); 
setcookie("cook_tmp_openid","",null,"/",$_ZEAI['CookDomain']);
setcookie("Temp_regyzmrenum","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_uid","",null,"/",$_ZEAI['CookDomain']);setcookie("cook_tg_uid","",null,"/",'');
setcookie("cook_tg_mob","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_uname","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_pwd","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_kind","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_openid","",null,"/",$_ZEAI['CookDomain']);
setcookie("cook_tg_subscribe","",null,"/",$_ZEAI['CookDomain']);
if(!empty($url)){
	header("Location: ".$url);
}else{
	header("Location: ".HOST);
}
?>                                                                                                                
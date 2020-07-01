<?php
require_once '../sub/init.php';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
//
$mini_backT = '我的';
$mini_title = '我的相册';
require_once ZEAI.'m1/top_mini.php';
$t = (ifint($t,'1-4','1'))?$t:1;
?>
<style>
.my_photo{width:100%;position:fixed;top:95px;bottom:0;background-color:#fff;overflow:auto;overflow-x:hidden}
</style>
<i class='ico goback' id='ZEAIGOBACK-my_photo'>&#xe602;</i>



<div class="my_photo" id="my_photo_submain">


我的相册




</div>


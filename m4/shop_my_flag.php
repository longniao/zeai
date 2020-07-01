<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$currfieldstg = "title,shopgradetitle";
$rowtg = shop_chk();
if($rowtg){
	$cook_tg_uid      = $rowtg['id'];
	$cook_tg_shopflag = $rowtg['shopflag'];
	$cook_tg_title  = dataIO($rowtg['title'],'out');
	$cook_tg_shopgradetitle  = dataIO($rowtg['shopgradetitle'],'out');
}else{
	header("Location: shop_my_apply.php");
}
if($cook_tg_shopflag==1){
	header("Location: shop_my.php");
}elseif($cook_tg_shopflag==2){
	header("Location: shop_my_vip.php");
}elseif($cook_tg_shopflag==0){
	$flagstr='审核';
	$flagtitle='等待审核';
	$flagico='<i class="ico" style="font-size:80px;color:#30CEF5;margin-bottom:20px">&#xe634;</i>';
}elseif($cook_tg_shopflag==-1){
	$flagstr='锁定';
	$flagtitle='已被冻结';
	$flagico='<i class="ico" style="font-size:80px;color:#1E9FFF;margin-bottom:20px">&#xe61e;</i>';
}
$nav = 'shop_my';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $flagtitle;?></title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<style>.shop_flag{margin:70px auto 150px auto}</style>
<body>
<?php 
$url=HOST.'/m4/shop_index.php';
$mini_title = '<i class="ico goback" onClick="zeai.back(\''.$url.'\');">&#xe602;</i>';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');
?>
<style>
.tg_reg_kefu{margin-top:50px}
.tg_reg_kefu img{width:25%;margin:10px auto;display:block;padding:10px;border:#eee 1px solid}
.tg_reg_kefu font{color:#999}
.tg_reg_kefu a{margin-top:10px;display:block;color:#666}
.tg_reg_kefu .ico{margin-right:4px;}
</style>
<div class="shop_flag">
    <?php echo $flagico;?>
    <h4 class="B">您的帐号“ID：<?php echo $cook_tg_uid;?>”<?php echo $flagstr;?>中，请耐心等待...</h4><br>
    <h5><?php echo $_SHOP['title'];?>名称：<?php echo $cook_tg_title;?>【<?php echo $cook_tg_shopgradetitle;?>】</h5>
    <br><br>
    <h3><?php echo $title;?></h3>
    <a href="shop_index.php" class="btn size4 HONG3 yuan" style="width:60%">进入<?php echo $_SHOP['title'];?>首页</a>
    
    <div class="tg_reg_kefu">
    <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><br><?php }?>
    <?php if (!empty($kf_tel)){?>
        <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
    <?php }else{?>
        <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
    <?php }?>
    </div>
</div>
<?php require_once ZEAI.'m4/shop_bottom.php';?>
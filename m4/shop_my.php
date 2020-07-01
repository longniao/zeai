<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
require_once ZEAI.'cache/config_reg.php';$TG_set = json_decode($_REG['TG_set'],true);
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$currfields   = "photo_s,mob,uname,nickname";
$currfieldstg = "mob,title,photo_s,shopgrade,shopgradetitle,money,tgallmoney";
//
$rowtg = shop_chk();
if($rowtg){
	$cook_tg_uid=$rowtg['id'];
	$photo_s = $rowtg['photo_s'];
	$title     = dataIO($rowtg['title'],'out');
	$mob       = $rowtg['mob'];
	$uname     = $rowtg['uname'];
	$shopgrade = $rowtg['shopgrade'];
	$shopflag  = $rowtg['shopflag'];
	$money     = $rowtg['money'];
	$tgallmoney=str_replace(".00","",$rowtg['tgallmoney']);
	$title       = (empty($title))?$uname:$title;
	$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m.jpg';
	if($shopflag==2){
		$shopgradetitle = '<a href="shop_my_vip.php" class="hui">去入驻</a>';
		$title = (empty($nickname))?$uname:$nickanme;
	}else{
		if($shopflag==2){
			$shopgradetitle = '<a href="shop_my_vip.php" class="hui">去激活</a>';
		}elseif($shopflag==0){
			$shopgradetitle = '<a href="shop_my_apply.php" class="flag0">审核中</a>';
		}elseif($shopflag==-2){
			$shopgradetitle = '<a href="shop_my_apply.php" class="flag_2">'.$_SHOP['title'].'隐藏中</a>';
		}elseif($shopflag==-1){
			$shopgradetitle = '<a href="shop_my_flag.php" class="flag_1">'.$_SHOP['title'].'锁定中</a>';
		}elseif($flag==-1){
			header("Location: shop_my_flag.php");
		}else{
			$shopgradetitle = '<a href="shop_my_vip.php">'.$rowtg['shopgradetitle'].'</a>';
		}
	}
	$mob = substr($mob,0,3).'****'.substr($mob,7,4).'（ID:'.$cook_tg_uid.'）';
	//
	$tipnum = $db->COUNT(__TBL_TIP__,"new=1 AND kind=6 AND tg_uid=".$cook_tg_uid);
	$tipnum_str = ($tipnum>0)?'<b></b>':'';
	$favnum = $db->COUNT(__TBL_SHOP_FAV__,"tg_uid=".$cook_tg_uid);
	if ($shopflag == 1){
		$yuyuenum    = $db->COUNT(__TBL_SHOP_YUYUE__,"flag=0 AND cid=".$cook_tg_uid);
		$orderadmnum = $db->COUNT(__TBL_SHOP_ORDER__,"(flag=0 OR flag=1 OR flag=2 OR flag=5 OR flag=7 OR flag=9) AND cid=".$cook_tg_uid);
		$codenum     = $db->COUNT(__TBL_SHOP_ORDER__,"flag=2 AND hdcode<>'' AND cid=".$cook_tg_uid);
		$yuyuenum_str=($yuyuenum>0)?'<b>'.$yuyuenum.'</b>':'';
		$orderadmnum_str=($orderadmnum>0)?'<b>'.$orderadmnum.'</b>':'';
		$codenum_str=($codenum>0)?'<b>'.$codenum.'</b>':'';
	}
}else{
	header("Location: ".HOST."/m1/tg_login.php?loginkind=shop&jumpurl=".urlencode(HOST.'/m4/shop_my.php'));
}
if(ifint($cook_tg_uid)){
	$num0 = $db->COUNT(__TBL_SHOP_ORDER__,"flag=0 AND tg_uid=".$cook_tg_uid);
	$num1 = $db->COUNT(__TBL_SHOP_ORDER__,"flag=1 AND tg_uid=".$cook_tg_uid);
	$num2 = $db->COUNT(__TBL_SHOP_ORDER__,"flag=2 AND tg_uid=".$cook_tg_uid);
	$num3 = $db->COUNT(__TBL_SHOP_ORDER__,"flag=3 AND tg_uid=".$cook_tg_uid);
}
$num0_str=($num0>0)?'<b>'.$num0.'</b>':'';
$num1_str=($num1>0)?'<b>'.$num1.'</b>':'';
$num2_str=($num2>0)?'<b>'.$num2.'</b>':'';
$num3_str=($num2>0)?'<b>'.$num3.'</b>':'';
$nav = 'shop_my';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的<?php echo $_SHOP['title'];?></title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop_my.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<div class="my_top">
	<div class="set">
    	<a href="shop_my_set.php" class="ico2">&#xe6e8;<b></b></a>
    	<a href="shop_my_tip.php" class="ico2">&#xe787;<?php echo $tipnum_str;?></a>
    </div>
	<p>
    	<img src="<?php echo $photo_s_url;?>" onClick="zeai.openurl('<?php if ($shopflag == 1){echo HOST.'/m4/shop_detail.php?id='.$cook_tg_uid;}else{echo 'shop_my_apply.php';}?>')">
    	<?php echo $shopgradetitle;?>
    </p>
	<em onClick="zeai.openurl('shop_my_apply.php')">
    	<h2><?php echo $title;?></h2>
    	<h5><?php echo $mob;?></h5>
    </em>
    <div class="jt ico">&#xe601;</div>
</div>
<div class="clear"></div>
<div class="myb">
	<a href="shop_my_money.php"><b><?php echo str_replace(".00","",number_format($money,2));?></b><span>我的余额</span></a>
	<?php if(@in_array('tg',$navarr)){?><a href="<?php echo HOST;?>/m1/tg_my.php"><b><?php echo intval($tgallmoney);?></b><span>推广奖励</span></a><?php }?>
	<a href="shop_my_fav.php"><b><?php echo $favnum;?></b><span>我的收藏</span></a>
</div>
<?php if (!empty($_SHOP['my_banner'])){
	$bnurl=(!empty($_SHOP['my_banner_url']))?$_SHOP['my_banner_url']:'javascript:;';
	?><a href="<?php echo $bnurl;?>"><img src="<?php echo $_ZEAI['up2'].'/'.$_SHOP['my_banner'];?>" class="mybanner"></a><?php }?>
<div class="myorder">
	<h1>我的订单<a href="shop_my_order.php">全部订单<i class="ico">&#xe601;</i></a></h1>
    <em>
        <a href="shop_my_order.php?f=0"><i class="ico2">&#xe649;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],0,'v2'));?></span><?php echo $num0_str;?></a>
        <a href="shop_my_order.php?f=1"><i class="ico2">&#xe615;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],1,'v2'));?></span><?php echo $num1_str;?></a>
        <a href="shop_my_order.php?f=2"><i class="ico2">&#xe68e;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],2,'v2'));?></span><?php echo $num2_str;?></a>
        <a href="shop_my_order.php?f=3"><i class="ico2">&#xe628;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],3,'v2'));?></span></a>
    </em>
</div>
<div class="mymenu">
    <ul>
    	<?php if(@in_array('tg',$navarr)){?>
        <a href="<?php echo HOST;?>/m1/tg_my.php"><i class="ico2 share">&#xe61d;</i><h4><?php echo $TG_set['navtitle'];?></h4><span>分享身边资源获得奖励</span></a>
        <?php }?>
        <a href="shop_my_set.php"><i class="ico2 address">&#xe61f;</i><h4>收货地址</h4><span id="TG_U_num"></span></a>
        <a href="shop_my_yuyue.php"><i class="ico2 yuyue">&#xe626;</i><h4>我的预约</h4><?php echo $tgtipnum_str;?></a>
    </ul>
    <?php if ($shopflag == 1){?>
    <ul>
        <a href="shop_my_shop_adm.php"><i class="ico2 shop">&#xe60e;</i><h4><?php echo $_SHOP['title'];?>设置</h4></a>
        <a href="shop_my_goods.php"><i class="ico2 product">&#xe621;</i><h4>商品管理</h4></a>
        <a href="shop_my_order.php?ifadm=1"><i class="ico order">&#xe656;</i><h4>订单管理</h4><?php echo $orderadmnum_str;?></a>
        <a href="shop_my_yuyue_adm.php"><i class="ico2 yuyueadm">&#xe68d;</i><h4>到店预约</h4><?php echo $yuyuenum_str;?></a>
        <a href="shop_my_hdcode_adm.php"><i class="ico hdcode"> &#xe6c3;</i><h4>订单核销</h4><?php echo $codenum_str;?></a>
    </ul>
    <?php }?>
</div>
<div class="myblank"><button type="button" onClick="zeai.openurl('<?php echo HOST;?>/loginout.php?url=<?php echo HOST;?>/m4/shop_index.php')" class="btn size4 BAI yuan my_exit">退出当前帐号</button></div>
<?php if ($shopflag == 1 && $orderadmnum>0){?><a href="shop_my_order.php?ifadm=1&new=1&ifadm_new=1" class="btmKefuBtn loop_s_b_s" style="bottom:60px;background-color:#9266F9"><span>未完成<br><?php echo strip_tags($orderadmnum_str);?>笔</span></a><?php }?>
<?php require_once ZEAI.'m4/shop_bottom.php';?>
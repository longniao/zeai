<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
$t = (ifint($t,'1-2','1'))?$t:1;
if($submitok == 'ajax_touch_del'){
	if(!ifint($tid))exit(JSON_ERROR);
	$db->query("DELETE FROM ".__TBL_SHOP_FAV__." WHERE tg_uid=".$cook_tg_uid." AND id=".$tid);
	json_exit(array('flag'=>1));
}
//
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
function rows_ulist($rows,$p) {
	global $_ZEAI,$t;
	$O = '';
	if($t==1){
		$id     = $rows['id'];
		$title  = dataIO($rows['title'],'out');
		$new    = $rows['new'];
		$tg_uid = $rows['tg_uid'];
		$addtime_str = date_str($rows['addtime']);
		$photo_s  = $rows['photo_s'];
		$new_str  = ($new == 1)?'<b></b>':'';
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'m'):HOST.'/res/tg_my_u2.png';
		$img_str     = '<img src="'.$photo_s_url.'">';
		$O .= '<dl onClick="zeai.openurl(\''.HOST.'/m4/shop_detail.php?id='.$tg_uid.'\')">';
		$O .= '<dt tid="'.$id.'">'.$img_str.'</dt>';
		$O .= '<dd><h4>'.$title.'</h4></dd>';
		$O .= '<span>'.$addtime_str.'</span>';
		$O .= '<strong>取消</strong>';
		$O .= '</dl>';
	}elseif($t==2){
		$id          = $rows['id'];
		$pid         = $rows['pid'];
		$photo_s     = $rows['path_s'];
		$title       = dataIO($rows['title'],'out');
		$new         = $rows['new'];
		$addtime     = $rows['addtime'];
		$addtime_str = date_str($rows['addtime']);
		$new_str     = ($new == 1)?'<b></b>':'';
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/noP.gif';
		$img_str     = '<img src="'.$photo_s_url.'">';
		$O .= '<dl onClick="zeai.openurl(\''.HOST.'/m4/shop_goods_detail.php?id='.$pid.'\')">';
		$O .= '<dt tid="'.$id.'">'.$img_str.'</dt>';
		$O .= '<dd><h4>'.$title.'</h4></dd>';
		$O .= '<span>'.$addtime_str.'</span>';
		$O .= '<strong>取消</strong>';
		$O .= '</dl>';
	}
	return $O;
}
$_ZEAI['pagesize'] = 8;
if($t ==1){
	$ZEAI_SQL   = "U.id=b.favid AND b.tg_uid=".$cook_tg_uid." AND b.kind=1";
	$ZEAI_SELECT="SELECT U.id AS tg_uid,b.id,b.new,b.addtime,U.title,U.photo_s FROM ".__TBL_TG_USER__." U,".__TBL_SHOP_FAV__." b WHERE ".$ZEAI_SQL." ORDER BY b.id DESC";
	$ZEAI_total_SQL = "SELECT COUNT(*) FROM ".__TBL_TG_USER__." U,".__TBL_SHOP_FAV__." b WHERE ".$ZEAI_SQL;
}elseif($t == 2){
	$ZEAI_SQL   = "P.id=F.favid AND F.tg_uid=".$cook_tg_uid." AND F.kind=2";
	$ZEAI_SELECT="SELECT P.id AS pid,F.id,P.path_s,P.title,F.addtime,F.new FROM ".__TBL_TG_PRODUCT__." P,".__TBL_SHOP_FAV__." F WHERE ".$ZEAI_SQL." ORDER BY F.id DESC";
	$ZEAI_total_SQL = "SELECT COUNT(*) FROM ".__TBL_TG_PRODUCT__." P,".__TBL_SHOP_FAV__." F WHERE ".$ZEAI_SQL;
}
if($submitok=='ZEAI_list'){exit(Zeai_ajax_list_fn($ZEAI_totalP,$p));}
$ZEAI_total = $db->query($ZEAI_total_SQL);
$ZEAI_total = $db->fetch_array($ZEAI_total);
$ZEAI_total = $ZEAI_total[0];
$ZEAI_totalP= ceil($ZEAI_total/$_ZEAI['pagesize']);?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php 
$url=HOST.'/m4/shop_my.php';
$mini_title = '<i class="ico goback" onClick="zeai.openurl(\''.$url.'\');">&#xe602;</i>';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<style>
.shop_my_fav_tab{width:70%;position:fixed;top:-5px;left:25%;z-index:2}
.shop_my_fav_tab a:after{width:18%;}
.shop_tip dl dt img{width:50px;height:50px;border-radius:25px;object-fit:cover;-webkit-object-fit:cover}
.shop_tip dl dd h4{line-height:50px;margin:auto 0 0 10px;font-size:16px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.shop_tip dl span{top:27px}
</style>
<div class="tab flex tabtop shop_my_fav_tab">
    <a href="<?php echo SELF;?>?t=1&nciaezwww=<?php echo $nciaezwww;?>"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>><?php echo $p_str;?>收藏的店铺</a>
    <a href="<?php echo SELF;?>?t=2&nciaezwww=<?php echo $nciaezwww;?>"<?php echo ($t==2)?' class="ed"':'';?>><?php echo $kind_str;?>收藏的商品</a>
</div>
<div class="shop_tip" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div>
<div class="shop_msg_yd" id="msg_yd"><img src="<?php echo HOST;?>/res/m4/img/msg_yd.png"></div>
<script src="<?php echo HOST;?>/res/m4/js/shop.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
<?php
if ($ZEAI_total > $_ZEAI['pagesize'] && $t != 2){?>
var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2;
zeaiOnscroll_json={url:'shop_my_fav'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,t:<?php echo $t;?>}};
document.body.onscroll = zeaiOnscroll;
<?php }?>
shop_touchDelFn(ZEAI_list,'shop_my_fav'+zeai.ajxext+'submitok=ajax_touch_del');var nodatatips="<?php echo $nodatatips;?>";
if(zeai.empty(sessionStorage.msg_yd)){zeai.mask({son:msg_yd,cancelBubble:'off',close:function(){sessionStorage.msg_yd='wwwZEAIcn_shop';}});}
</script>
</body>
</html>

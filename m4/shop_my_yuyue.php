<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
$t = (ifint($t,'1-2','1'))?$t:1;
//
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
function rows_ulist($rows,$p) {
	global $_ZEAI,$t;
	$O = '';
	$id     = $rows['id'];
	$title  = dataIO($rows['title'],'out');
	$cid    = $rows['cid'];
	$addtime_str = date_str($rows['addtime']);
	$photo_s  = $rows['photo_s'];
	$flag     = $rows['flag'];$flag_str=($flag==1)?'已处理':'未处理';
	$new_str  = ($rows['flag'] == 0)?'<b></b>':'';
	$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'m'):HOST.'/res/tg_my_u2.png';
	$img_str     = '<img src="'.$photo_s_url.'">';
	$O .= '<dl onClick="zeai.openurl(\''.HOST.'/m4/shop_detail.php?id='.$cid.'\')">';
	$O .= '<dt tid="'.$id.'">'.$img_str.$new_str.'</dt>';
	$O .= '<dd><h4>'.$title.'</h4></dd>';
	$O .= '<span><i class="ico">&#xe634;</i> '.$addtime_str.'</span>';
	$O .= '<div class="flag'.$flag.'">'.$flag_str.'</div>';
	$O .= '</dl>';
	return $O;
}
$_ZEAI['pagesize'] = 8;
if($t ==1){
	$cook_tg_uid = intval($cook_tg_uid);
	$cook_uid    = intval($cook_uid);
	$ZEAI_SQL   = "U.id=b.cid AND (b.tg_uid=".$cook_tg_uid." OR (b.uid=".$cook_uid." AND b.uid<>0) )";
	$ZEAI_SELECT="SELECT b.id,b.cid,b.addtime,b.flag,U.title,U.photo_s FROM ".__TBL_TG_USER__." U,".__TBL_SHOP_YUYUE__." b WHERE ".$ZEAI_SQL." ORDER BY b.id DESC";
	$ZEAI_total_SQL = "SELECT COUNT(*) FROM ".__TBL_TG_USER__." U,".__TBL_SHOP_YUYUE__." b WHERE ".$ZEAI_SQL;
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
$mini_title = '<i class="ico goback" onClick="zeai.back();">&#xe602;</i>我的预约';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<style>
.shop_tip dl dt img{width:40px;height:40px;border-radius:25px;margin-top:5px;object-fit:cover;-webkit-object-fit:cover}
.shop_tip dl dd h4{line-height:50px;margin:auto 0 0 10px;font-size:16px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.shop_tip dl .flag1,.shop_tip dl .flag0{color:#999;font-size:12px;position:absolute;right:15px;top:35px}
.shop_tip dl .flag0{color:#f00}
.shop_tip dl .flag1{color:#090}
</style>
<div class="shop_tip" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div>
<script>
<?php
if ($ZEAI_total > $_ZEAI['pagesize'] && $t != 2){?>
var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2;
zeaiOnscroll_json={url:'shop_my_fav'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,t:<?php echo $t;?>}};
document.body.onscroll = zeaiOnscroll;
<?php }?>
</script>
</body>
</html>

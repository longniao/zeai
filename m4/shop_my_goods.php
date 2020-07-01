<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
if($rowtg['shopflag']==0 || $rowtg['shopflag']==-1 || $rowtg['shopflag']==2)header("Location: shop_my_flag.php");
$t = (ifint($t,'1-2','1'))?$t:1;
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
if($submitok=='top_update'){
	$id=intval($id);$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET px=".ADDTIME." WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok=='hide_update'){
	$id=intval($id);$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET flag=2 WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok=='show_update'){
	$id=intval($id);$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET flag=1 WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok=='del_update'){
	$id=intval($id);$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET flag=-1 WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}
function rows_ulist($rows,$p) {
	global $_ZEAI,$t;
	$O = '';
	$id      = $rows['id'];
	$price   = str_replace(".00","",$rows['price']);
	$path_s  = $rows['path_s'];
	$click   = $rows['click'];
	$stock   = $rows['stock'];
	$tgbfb1  = $rows['tgbfb1'];
	$tgbfb2  = $rows['tgbfb2'];
	$title   = dataIO($rows['title'],'out');
	$addtime_str = YmdHis($rows['addtime'],'Ymd');
	$stock_str=($stock>0)?'库存'.$stock:'<font class="Cf00"><i class="ico2">&#xe604;</i> 库存不足</font>';
	$path_s_url = (!empty($path_s))?$_ZEAI['up2'].'/'.$path_s:HOST.'/res/noP.gif';
	$img_str    = '<img src="'.$path_s_url.'">';
	$price_str=($price>0)?'<font class="Cf00">￥'.number_format($price).'</font>':'<font class="C090">免费</font>';
	$href=HOST.'/m4/shop_goods_detail.php?id='.$id;
	$xjstr = ($t==1)?'下架':'';
	if($tgbfb1>0 || $tgbfb2>0)$tgbfb_str='<span class="tgbfb_str">分销</span>';
	$O .= '<dl>';
	$O .= '<dt onClick="zeai.openurl(\''.$href.'\');">'.$img_str.$tgbfb_str.'</dt>';
	$O .= '<dd onClick="zeai.openurl(\''.$href.'\');"><h4>'.$title.'<br>'.$price_str.'</h4><h5>浏览'.$click.'　'.$stock_str.'</h5></dd>';
	$O .= '<div><span>'.$addtime_str.'</span><em>';
	if($t==1)$O .= '<button class="btn size2 BAI" onClick="shop_my_goodsFn('.$id.',\'top\')">置顶</button>';
	$O .= '<button class="btn size2 BAI" onClick="shop_my_goodsFn('.$id.',\'mod\')">编辑</button>';
	if($t==1){
		$O .= '<button class="btn size2 BAI" onClick="shop_my_goodsFn('.$id.',\'hide\')">下架</button>';
	}elseif($t==2){
		$O .= '<button class="btn size2 BAI" onClick="shop_my_goodsFn('.$id.',\'show\')">上架</button>';
	}
	$O .= '<button class="btn size2 BAI" onClick="shop_my_goodsFn('.$id.',\'del\')">删除</button>';
	$O .= '</em></div>';
	$O .= '</dl>';
	return $O;
}
$_ZEAI['pagesize'] = 10;
if($t ==1){
	$ZEAI_SQL = "flag=1 AND tg_uid=".$cook_tg_uid;
}elseif($t ==2){
	$ZEAI_SQL = "flag=2 AND tg_uid=".$cook_tg_uid;
}
$ZEAI_SELECT="SELECT id,title,price,path_s,addtime,click,stock,tgbfb1,tgbfb2 FROM ".__TBL_TG_PRODUCT__." WHERE ".$ZEAI_SQL." ORDER BY px DESC,id DESC";
$ZEAI_total_SQL = "SELECT COUNT(*) FROM ".__TBL_TG_PRODUCT__." WHERE ".$ZEAI_SQL;
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
$mini_title = '<i class="ico goback" onClick="zeai.openurl(\'shop_my.php\');">&#xe602;</i>';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<style>.HONG3{width:50%;left:25%;position:fixed;bottom:10px;display:block;z-index:8;}</style>
<div class="tab flex tabtop shop_my_goods_tab">
    <a href="<?php echo SELF;?>?t=1&nciaezwww=<?php echo $nciaezwww;?>"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>><?php echo $p_str;?>商品管理</a>
    <a href="<?php echo SELF;?>?t=2&nciaezwww=<?php echo $nciaezwww;?>"<?php echo ($t==2)?' class="ed"':'';?>><?php echo $kind_str;?>已下架</a>
</div>
<button type="button" class="btn size4 HONG3 yuan" onClick="zeai.openurl('shop_my_goods_addmod.php');"><i class="ico">&#xe620;</i> 发布新商品</button>
<div class="shop_my_goods" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div>
<script src="<?php echo HOST;?>/res/m4/js/shop.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php if ($ZEAI_total > $_ZEAI['pagesize'] && $t != 2){?>
<script>
var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2;
zeaiOnscroll_json={url:'shop_my_fav'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,p:p,t:<?php echo $t;?>}};
document.body.onscroll = zeaiOnscroll;
</script>
<?php }?>
</body>
</html>
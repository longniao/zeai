<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
$key=trimhtml($key);
function rows_ulist($rows,$p) {
	global $_ZEAI,$db,$key;
	$cid    = $rows['id'];
	$cname  = $rows['title'];
	$path_s = $rows['photo_s'];
	$piclist= $rows['piclist'];
	$price  = $rows['price'];
	$price=str_replace(".00","",$price);
	$shopkind = $rows['shopkind'];$shopkindtitle=shopkindtitle($shopkind);
	$kind     = $rows['kind'];
	$areatitle= $rows['areatitle'];
	$ar=explode(' ',$areatitle);
	$area=$ar[2];
	if(!empty($area))$area=$area.'-';
	if(!empty($path_s)){
		$path_s_url2 = $_ZEAI['up2'].'/'.smb($path_s,'m');
	}else{
		$path_s_url2=HOST."/res/noP.gif?".$_ZEAI['cache_str'];
	}
	$path_s_str = '<img src="'.$path_s_url2.'">';
	switch ($kind) {
		case 1:$kind_str='个人';break;
		case 2:$kind_str='公司';break;
		case 3:$kind_str='机构';break;
	}
	$pnum = $db->COUNT(__TBL_TG_PRODUCT__,"tg_uid=".$cid);
	$O = '<a href="shop_detail.php?id='.$cid.'">';
	$O.= '	<dl>';
	$O.= '		<dt>'.$path_s_str.'</dt>';
	$O.= '		<dd><h2><font class="f'.$kind.'">'.$kind_str.'</font><span>'.str_replace($key,'<div style="display:inline;color:red">'.$key.'</div>',$cname).'</span></h2><h5>'.$area.$shopkindtitle.'<font>商品数<b>'.$pnum.'</b></font></h5></dd>';
	$O.= '	</dl>';
	if(!empty($piclist)){
		$ARR=explode(',',$piclist);
		if(count($ARR) >= 0){
			$ln=0;
			$O.= '<ul class="flex">';
			foreach ($ARR as $V) {if($ln>=3)continue;$O.='<img src="'.$_ZEAI['up2'].'/'.smb($V,'b').'">';$ln++;}
			$O.='</ul>';
		}
	}
	$O.= '<div class="clear"></div></a>';
	return $O;
}
$_ZEAI['pagesize']=8;
$ZEAI_SQL  = "photo_s<>'' AND title<>'' AND shopflag=1";
$ZEAI_SQL .= (ifint($k))?" AND shopkind=".$k:"";
if(!empty($key)){
	if (ifint($key)){
		$ZEAI_SQL .= " AND (id=$key) ";
	}else{
		$ZEAI_SQL .= " AND ( ( title LIKE '%".$key."%' ) OR ( nickname LIKE '%".$key."%' ) OR ( uname LIKE '%".$key."%' ) )";
	}
}
$ZEAI_SELECT="SELECT id,title,photo_s,piclist,shopkind,kind,areatitle FROM ".__TBL_TG_USER__." WHERE ".$ZEAI_SQL." ORDER BY px DESC,id DESC";
$ZEAI_total_SQL = "SELECT COUNT(*) FROM ".__TBL_TG_USER__." WHERE ".$ZEAI_SQL;
if($submitok=='ZEAI_list'){exit(Zeai_ajax_list_fn($ZEAI_totalP,$p));}
$ZEAI_total = $db->query($ZEAI_total_SQL);
$ZEAI_total = $db->fetch_array($ZEAI_total);
$ZEAI_total = $ZEAI_total[0];
$ZEAI_totalP= ceil($ZEAI_total/$_ZEAI['pagesize']);
$nav = 'shop_shop';
$bodytitle = (ifint($k))?shopkindtitle($k).'-':'';
//
$fixedA  ='shop_my_apply.php';
$fixedStr='商家<br>入驻';
if(ifint($cook_tg_uid)){
	$rowtg = $db->ROW(__TBL_TG_USER__,"id","shopflag=1 AND id=".$cook_tg_uid);
	if ($rowtg){
		$fixedA  ='shop_my_goods_addmod.php';
		$fixedStr='发布<br>商品';
	}
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $bodytitle;?>商家展示</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head><body>
<?php
$share_str=array();
if(!empty($_SHOP['kindarr'])){
	$kindarr = json_decode($_SHOP['kindarr'],true);
	if (count($kindarr) >= 1 && is_array($kindarr)){
		echo '<div class="shop_shop_nav"><ul id="shop_shop_nav">';
		$clsstr=(!ifint($k))?' class="ed"':'';
		echo '<a href="shop_shop.php"'.$clsstr.'>全部</a>';
		foreach ($kindarr as $V) {
			$clsstr=($k==$V['i'])?' class="ed"':'';
			echo '<a id="k'.$V['i'].'" href="shop_shop.php?k='.$V['i'].'" '.$clsstr.'">'.$V['v'].'</a>';
			$share_str[]=$V['v'];
		}
		echo '</ul></div>';
	}
}
$share_str = (is_array($share_str))?implode(',',$share_str):'';
?>
<script>
<?php if (ifint($k)){?>
var k=<?php echo $k;?>;
window.onload=function(){
	var obj=o('k'+k);var fobj=o('shop_shop_nav'),
	obj50=parseInt(obj.offsetWidth/2);
	obj_left = obj.offsetLeft;
	screenW=parseInt(screen.width);
	mL=parseInt(screenW/2);
	fobj.scrollLeft = obj_left-mL+obj50;
}
<?php }?>
</script>
<div class="shop_shop" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div>
<a href="<?php echo $fixedA;?>" id="btmApplyBtn" class="btmKefuBtn loop_s_b_s"><span><?php echo $fixedStr;?></span></a>
<script>
<?php
if ($ZEAI_total > $_ZEAI['pagesize']){?>
var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2;
zeaiOnscroll_json={url:'shop_shop'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,k:<?php echo intval($k);?>,key:'<?php echo $key;?>'}};
document.body.onscroll = zeaiOnscroll;
<?php }?>
</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr:'<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList:['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']});
	var FX_title = '<?php echo $bodytitle;?>商家展示',
	FX_desc  = '<?php echo $share_str.'【'.$_ZEAI['siteName'].'】';?>',
	FX_link  = '<?php echo HOST; ?>/m4/shop_shop.php',
	FX_imgurl= '<?php echo HOST; ?>/res/m4/img/share_shop_index.png?<?php echo $_ZEAI['cache_str'];?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:FX_title,desc:FX_desc,link:FX_link,imgUrl:FX_imgurl});
		wx.onMenuShareTimeline({title:FX_title,link:FX_link,imgUrl:FX_imgurl});
	});
	</script>
<?php }?>
<?php require_once ZEAI.'m4/shop_bottom.php';?>
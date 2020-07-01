<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$key=trimhtml($key);
//
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
$_ZEAI['pagesize']=6;
function rows_ulist($rows,$p) {
	global $db,$_ZEAI,$key;
	$pid    = $rows['id'];
	$ptitle = trimhtml(dataIO($rows['title'],'out'));
	$path_s = $rows['path_s'];
	$price  = $rows['price'];
	$price=str_replace(".00","",$price);
	$click  = $rows['click'];
	$fahuokind = $rows['fahuokind'];
	$fahuokind_str=($fahuokind==2)?'<span class="fahuokind2">线下</span>':'';
	$url = trimhtml(dataIO($rows['url'],'out'));
	$url = (!empty($url))?$url:'shop_goods_detail.php?id='.$pid;
	if(!empty($path_s)){
		$path_s_url2 = $_ZEAI['up2'].'/'.smb($path_s,'b');
	}else{
		$path_s_url2=HOST."/res/noP.gif?".$_ZEAI['cache_str'];
	}
	$onum = $db->COUNT(__TBL_SHOP_ORDER__,"pid=".$pid);
	$onum_str=($onum>0)?'<span class="onum">销量 '.$onum.'</span>':'';
	$path_s_str = '<img src="'.$path_s_url2.'" class="p'.$p.'">';
	$O = '<a href="'.$url.'">';
	$O.= '<p>'.$path_s_str.$onum_str.'</p>';
	$O.= '<h2>'.$fahuokind_str.str_replace($key,'<div style="display:inline;color:red">'.$key.'</div>',$ptitle).'</h2>';
	$O.= '<em><font>'.number_format($price).'</font><i><span class="ico">&#xe643;</span> '.$click.'</i></em>';
	$O.= '</a>';
	return $O;
}
$ZEAI_SQL  = "P.flag=1 AND P.path_s<>'' AND P.tg_uid=C.id AND C.shopflag=1";
$ZEAI_SQL .= (ifint($k))?" AND C.shopkind=".$k:"";
if(!empty($key)){
	if (ifint($key)){
		$ZEAI_SQL .= " AND (P.id=$key) ";
	}else{
		$ZEAI_SQL .= " AND P.title LIKE '%".$key."%' ";
	}
}
$ZEAI_SELECT="SELECT P.id,P.title,P.path_s,P.price,P.click,P.fahuokind,P.url FROM ".__TBL_TG_PRODUCT__." P,".__TBL_TG_USER__." C WHERE ".$ZEAI_SQL." ORDER BY P.px DESC,P.id DESC";
$ZEAI_total_SQL = "SELECT COUNT(*) FROM ".__TBL_TG_PRODUCT__." P,".__TBL_TG_USER__." C WHERE ".$ZEAI_SQL;
if($submitok=='ZEAI_list'){exit(Zeai_ajax_list_fn($ZEAI_totalP,$p));}
$ZEAI_total = $db->query($ZEAI_total_SQL);
$ZEAI_total = $db->fetch_array($ZEAI_total);
$ZEAI_total = $ZEAI_total[0];
$ZEAI_totalP= ceil($ZEAI_total/$_ZEAI['pagesize']);
$nav = 'shop_goods';
$bodytitle = (ifint($k))?shopkindtitle($k).'-':'';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $bodytitle;?>商品展示</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php
$share_str=array();
if(!empty($_SHOP['kindarr'])){
	$kindarr = json_decode($_SHOP['kindarr'],true);
	if (count($kindarr) >= 1 && is_array($kindarr)){
		echo '<div class="shop_shop_nav"><ul id="shop_shop_nav">';
		$clsstr=(!ifint($k))?' class="ed"':'';
		echo '<a href="shop_goods.php"'.$clsstr.'>全部</a>';
		foreach ($kindarr as $V) {
			$clsstr=($k==$V['i'])?' class="ed"':'';
			echo '<a id="k'.$V['i'].'" href="shop_goods.php?k='.$V['i'].'" '.$clsstr.'">'.$V['v'].'</a>';
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

<div class="shop_goods"><div class="list" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div></div>

<script>
<?php
if ($ZEAI_total > $_ZEAI['pagesize']){?>
var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2;
zeaiOnscroll_json={url:'shop_goods'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,k:<?php echo intval($k);?>,key:'<?php echo $key;?>'}};
document.body.onscroll = zeaiOnscroll;
<?php }?>
</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr:'<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList:['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']});
	var FX_title = '<?php echo $bodytitle;?>商品展示',
	FX_desc  = '<?php echo $share_str.'【'.$_ZEAI['siteName'].'】';?>',
	FX_link  = '<?php echo HOST; ?>/m4/shop_goods.php',
	FX_imgurl= '<?php echo HOST; ?>/res/m4/img/share_shop_goods.jpg?<?php echo $_ZEAI['cache_str'];?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:FX_title,desc:FX_desc,link:FX_link,imgUrl:FX_imgurl});
		wx.onMenuShareTimeline({title:FX_title,link:FX_link,imgUrl:FX_imgurl});
	});
	</script>
<?php }?>
<?php require_once ZEAI.'m4/shop_bottom.php';?>

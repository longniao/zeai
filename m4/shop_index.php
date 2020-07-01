<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$up2 = $_ZEAI['up2'].'/';
$nav = 'shop_index';
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
$_ZEAI['pagesize']=6;
$ZEAI_SQL = "flag=1 AND path_s<>''";
$ZEAI_SELECT="SELECT id,title,path_s,price,click,fahuokind,url FROM ".__TBL_TG_PRODUCT__." WHERE ".$ZEAI_SQL." ORDER BY px DESC,id DESC";
if($submitok=='ZEAI_list'){exit(Zeai_ajax_list_fn($ZEAI_totalP,$p));}
$ZEAI_total = $db->COUNT(__TBL_TG_PRODUCT__,$ZEAI_SQL);
$ZEAI_totalP= ceil($ZEAI_total/$_ZEAI['pagesize']);
//banner
if (!empty($_SHOP['mBN_path1_s']) || !empty($_SHOP['mBN_path2_s']) || !empty($_SHOP['mBN_path3_s']) ){
	$ifbanner = true;
	$path1_s = $_SHOP['mBN_path1_s'];
	$path2_s = $_SHOP['mBN_path2_s'];
	$path3_s = $_SHOP['mBN_path3_s'];
	$path1_b = smb($path1_s,'b');
	$path2_b = smb($path2_s,'b');
	$path3_b = smb($path3_s,'b');
	$path1_url = (empty($_SHOP['mBN_path1_url']))?'javascript:;':$_SHOP['mBN_path1_url'];
	$path2_url = (empty($_SHOP['mBN_path2_url']))?'javascript:;':$_SHOP['mBN_path2_url'];
	$path3_url = (empty($_SHOP['mBN_path3_url']))?'javascript:;':$_SHOP['mBN_path3_url'];
	$banner3 = "";$upurl = $_ZEAI['up2']."/";$bnum=0;
	if (!empty($path1_s))$banner3.='<div class="topadvs_li" bj="0"><a href="'.$path1_url.'" target="_self"><img src="'.$up2.$path1_b.'"></a></div>';
	if (!empty($path2_s))$banner3.='<div class="topadvs_li" bj="1"><a href="'.$path2_url.'" target="_self"><img src="'.$up2.$path2_b.'"></a></div>';
	if (!empty($path3_s))$banner3.='<div class="topadvs_li" bj="2"><a href="'.$path3_url.'" target="_self"><img src="'.$up2.$path3_b.'"></a></div>';
}else{$ifbanner = false;}
//
$fixedA  ='shop_my_apply.php';
$fixedStr='商家<br>入驻';
if(ifint($cook_tg_uid)){
	$tipnum = $db->COUNT(__TBL_TIP__,"new=1 AND kind=6 AND tg_uid=".$cook_tg_uid);
	$tipnum_str = ($tipnum>0)?'<b>'.$tipnum.'</b>':'';
	$rowtg = $db->ROW(__TBL_TG_USER__,"id","shopflag=1 AND id=".$cook_tg_uid);
	if ($rowtg){$fixedA  ='shop_my_goods_addmod.php';$fixedStr='发布<br>商品';}
}?><!doctype html><html><head><meta charset="utf-8">
<title><?php echo $_SHOP['title'];?> <?php echo $_ZEAI['siteName'];?></title>
<?php echo HEADMETA;?>
<link rel="shortcut icon" href="<?php echo HOST;?>/favicon.ico" type="image/x-icon" />
<meta name="generator" content="Zeai.cn SHOP2.0" />
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop_index.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/shop_index.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head><body>
<div class="header">
	<a href="<?php echo HOST;?>" title="<?php echo $_SHOP['title'];?>"><img src="<?php echo $up2.$_SHOP['logo'];?>?<?php echo $_ZEAI['cache_str'];?>"></a>
	<a href="shop_search.php"><i class="ico">&#xe6c4;</i><span>输入店铺或商品名称</span></a>
	<a href="shop_my_tip.php" class="ico">&#xe676;<?php echo $tipnum_str;?></a>
</div>
<?php if ($ifbanner){ ?>
<div id="topadvs" class="topadvs">
	<div class="topadvs_main"><?php echo $banner3; ?></div>
	<div class="topadvs_ico" id="topadvs_ico"></div>
</div>
<?php }
//topnav
$share_str=array();
foreach ($navtop as $V){if($V['f']==0 || empty($V['t']) || empty($V['img']) || empty($V['url']))continue;$nn++;$newnavtop[]=$V;}
if (count($newnavtop) >= 1 && is_array($newnavtop)){echo '<div class="shop_navtop">';foreach ($newnavtop as $V){
    $title=urldecode($V['t']);$share_str[]=$title;
    $var=urldecode($V['var']);
    $img=$V['img'];
    $url=urldecode($V['url']);
    $imgok=$_ZEAI['up2'].'/'.$img;?>
    <a href="<?php echo $url;?>"<?php if($nav==$var)echo' class="ed"';?>><img src="<?php echo $imgok;?>"><font><?php echo $title;?></font></a>
<?php }echo '<div class="clear"></div></div>';}
$share_str = (is_array($share_str))?implode(',',$share_str):'';
?>
<div class="clear"></div>
<h1 class="h1">推荐商家<a href="shop_shop.php">查看更多<i class="ico">&#xe601;</i></a></h1>
<div class="cbox">
	<?php 
	$rtP = $db->query("SELECT id,title,photo_s,shopkind FROM ".__TBL_TG_USER__." WHERE photo_s<>'' AND shopflag=1 ORDER BY px DESC,id DESC LIMIT 10");
	$totalP = $db->num_rows($rtP);
	if($totalP > 0){
		echo '<ul>';	
		for($j=1;$j<=$totalP;$j++){
			$rowsP = $db->fetch_array($rtP,'name');
			if(!$rowsP) break;
			$cid    = $rowsP['id'];
			$ptitle = $rowsP['title'];
			$path_s = $rowsP['photo_s'];
			$shopkind= $rowsP['shopkind'];
			$shopkindtitle= shopkindtitle($shopkind);
			$path_s_url2 = $_ZEAI['up2'].'/'.smb($path_s,'m');
			$path_s_str = '<img src="'.$path_s_url2.'" class="b">';?>
			<a href="shop_detail.php?id=<?php echo $cid;?>">
				<?php echo $path_s_str;?>
				<h5><?php echo $ptitle; ?></h5>
				<span class="kind kind<?php echo $shopkind;?>"><?php echo $shopkindtitle; ?></span>
				<em><font><?php echo $price; ?></font><i><span class="ico">&#xe643;</span> <?php echo $cid; ?></i></em>
			</a><?php
		}
		echo '</ul>';
	}else{echo $nodatatips;}?>
</div>
<h1 class="h1">推荐商品</h1>
<div class="listbox"><div class="list" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div></div>
<div style="height:150px"></div>
<a href="<?php echo $fixedA;?>" id="btmApplyBtn" class="btmKefuBtn loop_s_b_s"><span><?php echo $fixedStr;?></span></a>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr:'<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList:['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']});
	var FX_title = '<?php echo $_SHOP['title'];?>首页',
	FX_desc  = '<?php echo $share_str.'【'.$_ZEAI['siteName'].'】';?>',
	FX_link  = '<?php echo HOST; ?>/m4/shop_index.php',
	FX_imgurl= '<?php echo HOST; ?>/res/m4/img/share_shop_index.png';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:FX_title,desc:FX_desc,link:FX_link,imgUrl:FX_imgurl});
		wx.onMenuShareTimeline({title:FX_title,link:FX_link,imgUrl:FX_imgurl});
	});
	</script>
<?php }?>
<?php if ($ifbanner){ ?><script src="<?php echo HOST;?>/res/jq183.js"></script><script src="<?php echo HOST;?>/res/m4/js/shop_zeai_banner4.js?<?php echo $_ZEAI['cache_str'];?>"></script><?php }?>
<?php if ($ZEAI_total > $_ZEAI['pagesize']){?><script>var ifmore=true,ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2,ie;document.body.onscroll = indexOnscroll;</script><?php }
require_once ZEAI.'m4/shop_bottom.php';
function rows_ulist($rows,$p) {
	global $db,$_ZEAI;
	$pid    = $rows['id'];
	$ptitle = trimhtml(dataIO($rows['title'],'out'));
	$path_s = $rows['path_s'];
	$price  = $rows['price'];
	$url    = trimhtml(dataIO($rows['url'],'out'));
	$fahuokind = $rows['fahuokind'];
	$fahuokind_str=($fahuokind==2)?'<span class="fahuokind2">线下</span>':'';
	$price=number_format($price,2);
	$price=str_replace(".00","",$price);
	$click  = $rows['click'];
	if(!empty($path_s)){
		$path_s_url2 = $_ZEAI['up2'].'/'.smb($path_s,'b');
	}else{
		$path_s_url2=HOST."/res/noP.gif?".$_ZEAI['cache_str'];
	}
	$onum = $db->COUNT(__TBL_SHOP_ORDER__,"pid=".$pid);
	$onum_str=($onum>0)?'<span class="onum">销量 '.$onum.'</span>':'';
	$path_s_str = '<img src="'.$path_s_url2.'" class="p'.$p.'">';
	$url=(!empty($url))?$url:'shop_goods_detail.php?id='.$pid;
	$O = '<a href="'.$url.'">';
	$O.= '<p>'.$path_s_str.$onum_str.'</p>';
	$O.= '<h2>'.$fahuokind_str.$ptitle.'</h2>';
	$O.= '<em><font>'.$price.'</font><i><span class="ico">&#xe643;</span> '.$click.'</i></em>';
	$O.= '</a>';
	return $O;
}
?>
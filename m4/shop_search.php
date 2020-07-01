<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
$cook_uid=intval($cook_uid);$cook_tg_uid  =intval($cook_tg_uid);
if($submitok == 'ajax_key_save' && !empty($sk) && !empty($key)){
	$key=trimhtml($key);$sk == trimhtml($sk);
	$row = $db->ROW(__TBL_SHOP_SEARCH__,"id","tg_uid=".$cook_tg_uid." AND kind='".$sk."' AND title='$key'");
	if ($row){
		$id=$row[0];
		$db->query("UPDATE ".__TBL_SHOP_SEARCH__." SET endtime=".ADDTIME." WHERE id=".$id);
	}else{
		$kind =dataIO($sk,'in',20);$title=dataIO($key,'in',50);
		if(!empty($title))$db->query("INSERT INTO ".__TBL_SHOP_SEARCH__."  (tg_uid,uid,kind,endtime,title) VALUES ($cook_tg_uid,$cook_uid,'$kind',".ADDTIME.",'$title')");
	}
	json_exit(array('flag'=>1));
}elseif($submitok=='ajax_kind_history_my'){
	exit(get_kind_history($sk,1));
}elseif($submitok=='ajax_kind_history_hot'){
	exit(get_kind_history($sk,0));
}elseif($submitok=='ajax_clear_my'){
	$sk == trimhtml($sk);
	if(!empty($sk))$db->query("DELETE FROM ".__TBL_SHOP_SEARCH__." WHERE tg_uid=".$cook_tg_uid." AND kind='$sk'");
	exit;
}
function get_kind_history($sk,$ifmy) {
	global $db,$cook_uid,$cook_tg_uid;
	$sk == trimhtml($sk);
	if($ifmy==1){
		$SQL = "(tg_uid=".$cook_tg_uid.") ";
		$SQL.=(!empty($sk))?" AND kind='$sk'":"";
		$SELECTSQL="SELECT id,kind,title FROM ".__TBL_SHOP_SEARCH__." WHERE ".$SQL." ORDER BY id DESC";
	}else{
		$SQL = " ";
		$SQL.=(!empty($sk))?" WHERE kind='$sk'":"";
		$SELECTSQL="SELECT COUNT(title) AS tnum,kind,title FROM ".__TBL_SHOP_SEARCH__.$SQL." GROUP BY title ORDER BY tnum DESC,endtime DESC";
	}
	$echo='';
	$rt=$db->query($SELECTSQL);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'num');
			if(!$rows) break;
			$kind  = $rows[1];
			$title = dataIO($rows[2],'out');
			switch ($kind) {
				case 'goods':$url  = 'shop_goods.php';break;
				default:$url = 'shop_shop.php';break;
			}
			$echo .='<a href="'.$url.'?key='.$title.'">'.$title.'</a>';
		}
	}
	return $echo;
}
$nav = 'shop_index';?>
<!doctype html><html><head><meta charset="utf-8">
<title>搜索 <?php echo $_ZEAI['siteName'];?></title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<div class="shop_search">
	<form id="zeai_cn__form_search" action="shop_shop.php">
    	<div class="sokind" id="sokind"><span>店铺</span><i class="ico2 off">&#xe6aa;</i></div>
        <input type="text" name="key" id="key" class="input" placeholder="请输入店铺名称"><button type="button" id="sobtn"><i class="ico">&#xe6c4;</i></button>
        <ul id="soul"><li class="ed" value="shop">店铺</li><li value="goods">商品</li></ul>
        <input type="hidden" name="sk" id="sk" value="shop">
    </form>
</div>
<div class="shop_search_hh shop_search_history">
	<h1>历史搜索<a id="shop_search_clearbtn">清除记录</a></h1>
	<ul id="hismy"><?php echo get_kind_history('shop',1);?></ul>
</div>
<div class="shop_search_hh shop_search_hot">
	<h1>热门搜索</h1>
    <ul id="hishot"><?php echo get_kind_history('shop',0);?></ul>
</div>
<div class="mask0" id="Zeai_search_mask0"></div>
<script src="<?php echo HOST;?>/res/m4/js/shop_search.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php require_once ZEAI.'m4/shop_bottom.php';?>
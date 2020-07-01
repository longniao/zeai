<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
//$currfields="sex,grade,nickname,photo_s,photo_f";
//require_once ZEAI.'my_chk_u.php';
//$chk_u_jumpurl=HOST.'/?z=store';
require_once ZEAI.'sub/conn.php';


require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/config_reg.php';$TG_set = json_decode($_REG['TG_set'],true);


//
$kind = (ifint($kind,'1-3','1'))?$kind:1;
switch ($kind) {
	case 1:$kind_str=$TG_set['navtitle'];break;
	case 2:$kind_str='联盟商家';break;
	case 3:$kind_str='组织机构';break;
	default:$kind_str='全民红娘';break;
}
$_ZEAI['pagesize']= 8;
$SQL = " flag=1 ";
if(ifint($kind))$SQL.=" AND kind=".$kind;
$RTSQL = "SELECT id,uname,nickname,title,areatitle,kind,photo_s FROM ".__TBL_TG_USER__." WHERE ".$SQL." ORDER BY px DESC,id DESC";
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时没有 ".$kind_str."</div>";
if ($submitok == 'ajax_ulist'){
	exit(ajax_ulist_fn($totalP,$p));
}
/***********************正文开始***************************/
$headertitle = $kind_str.' - ';
require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	</script>
	<?php
}
?>
<link href="<?php echo HOST;?>/m1/css/store.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<?php if($_ZEAI['mob_mbkind']==3){?>
<style>.top_mini{background:#FF6F6F}</style>
<?php }?>
<?php
$mini_title = '联盟圈';
$mini_ext   = 'id="store_title"';
$mini_class = 'top_mini top_mini_index huadong';
require_once ZEAI.'m1/top_mini.php';
$nav = 'home';
?>
<main id="main" class="submain store huadong">
	<div class="storenav">
    	<a href="<?php echo HOST;?>/?z=store&kind=1" class="kind1<?php echo ($kind == 1 || empty($kind))?' ed1':'';?>"><i class="ico kind1">&#xe605;</i><span><?php echo $TG_set['navtitle'];?></span></a>
    	<a href="<?php echo HOST;?>/?z=store&kind=2" class="kind2<?php echo ($kind == 2)?' ed2':'';?>"><i class="ico2 kind2">&#xe71a;</i><span>联盟商家</span></a>
    	<a href="<?php echo HOST;?>/?z=store&kind=3" class="kind3<?php echo ($kind == 3)?' ed3':'';?>"><i class="ico2 kind3">&#xe62c;</i><span>组织机构</span></a>
    </div>
    <div id="list" class="list">
		<?php
        $total = $db->COUNT(__TBL_TG_USER__,$SQL);
		$totalP = ceil($total/$_ZEAI['pagesize']);
        echo ajax_ulist_fn($totalP,1);
		?>
    </div>
</main>
<?php if ($total > $_ZEAI['pagesize']){?>
<script>
	var totalP = parseInt(<?php echo $totalP; ?>),p=2,kind=<?php echo $kind;?>;
	zeai.ready(function(){o('main').onscroll = storeOnscroll;	});
</script>
<?php }?>
<?php
if($_ZEAI['mob_mbkind']==3){
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}

?>
<script src="<?php echo HOST;?>/m1/js/store.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>

<?php if(is_weixin()){?>
	var share_title = '<?php echo $kind_str;?> - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>',
	share_desc  = '<?php echo $_INDEX['indexContent'];?>',
	share_link  = '<?php echo HOST; ?>/?z=store',
	share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
		wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
	});
<?php }?>


<?php if (ifint($a)){
	$row = $db->ROW(__TBL_TG_USER__,"kind","id=".$a);
	if ($row){$kind= $row[0];}
	?>
	page({g:HOST+'/m1/store_detail.php?e=store_kind<?php echo $kind;?>&a='+<?php echo $a;?>,l:'store_kind<?php echo $kind;?>'});
<?php }elseif(ifint($id)){?>
	page({g:HOST+'/m1/store_detail.php?id='+<?php echo $id;?>,l:'product_detail'});
<?php }?>


</script>
<?php
function ajax_ulist_fn($totalP,$p) {
	global $_ZEAI,$db,$nodatatips,$RTSQL;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);

	if($totalP==0 && $p == 1)return $nodatatips;
	if ($p > $totalP)exit("end");
	
	$LIMIT = ($p == 1)?$_ZEAI['pagesize']:$_ZEAI['limit'];
	$RTSQL.=" LIMIT ".$LIMIT;
	$rt = $db->query($RTSQL);
	$total = $db->num_rows($rt);
	
	if ($p == 1){
		if ($total <= 0)return $nodatatips;
		$fort= $total;
	}else{
		if ($total <= 0)exit("end");
		$fort= $_ZEAI['pagesize'];
		$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);
	}
	$rows_ulist='';
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_ulist .= rows_ulist($rows);
	}
	return $rows_ulist;
}
function rows_ulist($rows) {
	global $_ZEAI,$db,$cook_uid;
	$id            = $rows['id'];
	$tg_uid        = $id;
	$photo_s       = $rows['photo_s'];
	$areatitle     = $rows['areatitle'];
	$area_s_title  = explode(' ',$areatitle);$area_s_title = $area_s_title[1];
	$title         = dataIO($rows['title'],'out');
	$uname         = dataIO($rows['uname'],'out');
	$nickname      = dataIO($rows['nickname'],'out');
	$kind          = $rows['kind'];
	$grade         = $rows['grade'];
	$photo_s_url   = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'m'):HOST.'/res/noP.gif';
	$photo_s_str = (!empty($photo_s))?'<img src="'.$photo_s_url.'">':'<img src="'.$photo_s_url.'" class="no">';
	$area_str      = (empty($area_s_title))?'':$area_s_title.'-';
	$unum = $db->COUNT(__TBL_USER__,"tguid=".$tg_uid);
	switch ($kind) {
		case 1:
			$kind_str='公益红娘';
			$href = ' onclick="storedetail('.$id.');"';
			$title = (empty($nickname))?'ID:'.$tg_uid:$nickname;
		break;
		case 2:
			$kind_str='商户';
			$href = ' onclick="storedetail2('.$id.');"';
			$title = (empty($title))?'ID:'.$tg_uid:$title;
		break;
		case 3:
			$kind_str='机构';
			$href = ' onclick="storedetail2('.$id.');"';
			$title = (empty($title))?'ID:'.$tg_uid:$title;
		break;
	}
	$rt2=$db->query("SELECT sex,photo_s,photo_f,id FROM ".__TBL_USER__." WHERE tguid=".$tg_uid." ORDER BY id DESC LIMIT 6");
	$total2 = $db->num_rows($rt2);
	if ($total2 == 0) {
		$ubox =  '<div class="ubox">Ta还没有推荐单身团会员！</div>';
	} else {
		$ubox = '<div class="ubox"><a '.$href.'>';
		for($ii=1;$ii<=$total2;$ii++) {
			$rows2 = $db->fetch_array($rt2,'name');
			if(!$rows2) break;
			$sex      = $rows2['sex'];
			$photo_s2  = $rows2['photo_s'];
			$photo_f  = $rows2['photo_f'];
			$photo_s2_url = (!empty($photo_s2) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_s'.$sex.'.png';
			$ubox .= '<span><img src="'.$photo_s2_url.'" alit='.$rows2['id'].'></span>';
		}
		$ubox .= '<i class="ico">&#xe601;</i></a></div>';
	}
	$echo  ='<dl>';
	$echo .='<dt><a '.$href.'>'.$photo_s_str.'</a></dt>';
	$echo .='<dd>';
		$echo .='<h3><font class="f'.$kind.'">'.$kind_str.'</font><span><a '.$href.'>'.$title.'</a></span></h3>';
		$echo .='<h4>'.$area_str.'单身团<font>'.$unum.'</font>人</h4>';
		$echo .= $ubox;
	$echo .='</dd>';
	$echo .='</dl>';
	return $echo;
}
?>
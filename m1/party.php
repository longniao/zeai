<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
$nodatatips = "<div class='nodatatips' style='margin:0 auto'><br><br><i class='ico'>&#xe651;</i>暂时木有内容～～</div>";
/***********************主体入口***************************/
$_ZEAI['pagesize']= 3;
$RTSQL = "SELECT id,title,hdtime,address,num_n,num_r,rmb_n,rmb_r,flag,jzbmtime,bmnum,signnum,bbsnum,path_s FROM ".__TBL_PARTY__." WHERE flag>0 AND path_s<>'' ORDER BY px DESC";
if ($submitok == 'ajax_list'){
	exit(ajax_list_fn($totalP,$p));
}
/*********************** BODY 开始***************************/
$headertitle = '交友活动 - ';
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
		jsApiList: ['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	//
	var share_title = '交友活动 - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>',
	share_desc  = '线下实名交友活动_相亲大会！',
	share_link  = '<?php echo HOST; ?>/?z=party',
	share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
		wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
	});
	</script>
<?php }?>
<link href="m1/css/party.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php if($_ZEAI['mob_mbkind']==3){?>
<style>
.top_mini{background:#FF6F6F}
#backtop a{background:#FF6F6F}
</style>
<?php }?>
<?php
$mini_title = '交友活动';
$mini_class = 'top_mini huadong';
$mini_ext = 'id="topminibox"';
require_once ZEAI.'m1/top_mini.php';
$nav = 'party';
?>
<main id="main" class="main huadong party">
    <!--主BOX-->
    <div id="list" class="list">
		<?php
        $total = $db->COUNT(__TBL_PARTY__," flag>0 ");
		$totalP = ceil($total/$_ZEAI['pagesize']);
        echo ajax_list_fn($totalP,1);
		?>
    </div>
    <?php require_once ZEAI.'m1/footer.php';?>
</main>
<?php if ($total > $_ZEAI['pagesize']){?>
	<script>
        var totalP = parseInt(<?php echo $totalP; ?>),p=2,t='<?php echo $t; ?>';
        zeai.ready(function(){o('main').onscroll = listOnscroll;});
    </script>
<?php }
if($_ZEAI['mob_mbkind']==3){
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}
?>
<script src="m1/js/party.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php
if (!empty($_GZH['wx_gzh_ewm']) && ifint($cook_uid)){wx_endurl('您刚刚浏览的页面【交友活动】',HOST.'/?z=party');}
function ajax_list_fn($totalP,$p) {
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
	$rows_list='';
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_list .= rows_list($rows);
	}
	return $rows_list;
}
function rows_list($rows) {
	global $_ZEAI,$db;
	$fid   = $rows['id'];
	$bmnum    = $rows['bmnum'];
	$path_s   = $rows['path_s'];
	$jzbmtime = $rows['jzbmtime'];
	$flag     = $rows['flag'];
	$hdtime  = $rows['hdtime'];
	$address = dataIO($rows['address'],'out');
	$title = dataIO($rows['title'],'out');
	$num_n = $rows['num_n'];
	$num_r = $rows['num_r'];
	$path_b=getpath_smb($path_s,'b');
	$path_b_url=$_ZEAI['up2'].'/'.$path_b;
	//
	$echo = '<a href=\''.wHref('party',$fid).'\' clsid="'.$fid.'">';
	$echo .= '<div class="pic"><img src="'.$path_b_url.'"><div class="djs">'.party_djs($flag,$jzbmtime).'</div></div>';
	$echo .= '<h3>'.$title.'</h3>';
	//$echo .= '<h6><i class="ico time">&#xe634;</i> '.$hdtime.'</h6>';
	$echo .= '<h6><i class="ico nowrap">&#xe614;</i> '.$address.'</h6>';
	$echo .= '<dl><dt>已报名'.$bmnum.'人</dt><dd>'. party_getBmUlist($fid).'<b class="ico">&#xe636;</b></dd></dl>';
	$echo .= '</a>';
	return $echo;
}
function party_getBmUlist($fid) {
	global $db,$_ZEAI;
	$rt=$db->query("SELECT U.sex,U.photo_s,U.photo_f FROM ".__TBL_PARTY_USER__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC LIMIT 4");
	$echo = '';
	WHILE ($rows = $db->fetch_array($rt,'num')){
		$sex      = $rows[0];
		$photo_s  = $rows[1];
		$photo_f  = $rows[2];
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
		$echo.='<img src="'.$photo_s_url.'"'.$sexbg.'>';
	}
	return $echo;
}
function party_djs($flag,$jzbmtime) {
	$d1  = ADDTIME;
	$d2  = $jzbmtime;
	$totals  = ($d2-$d1);
	$day     = intval( $totals/86400 );
	$hour    = intval(($totals % 86400)/3600);
	$hourmod = ($totals % 86400)/3600 - $hour;
	$minute  = intval($hourmod*60);
	if ($flag >= 2)$totals = -1;
	if (($totals) > 0) {
		$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> 截止报名还剩</span>';
		if ($day > 0){
			$outtime = $tmp."<span class=timestyle>$day</span>天";
		} else {
			$outtime = $tmp;
		}
		$outtime .= "<span class=timestyle>$hour</span>小时<span class=timestyle>$minute</span>分钟";
	} else {
		$outtime = '　报名已经结束';
	}
	$outtime = '<font>'.$outtime.'</font>';
	return $outtime;
}
?>
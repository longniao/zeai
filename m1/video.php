<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
$chk_u_jumpurl=HOST.'/?z=video';require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/udata.php';
//
if($submitok== 'ajax_agree'){
	if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'视频不存在'));
	$db->query("UPDATE ".__TBL_VIDEO__." SET agree=agree+1 WHERE id=".$fid);
	json_exit(array('flag'=>1,'msg'=>'点赞成功'));
}
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有视频～～</div>";
$_ZEAI['pagesize']= 6;
$_ZEAI['limit']   = 99999;
$SQL   = " a.flag=1 AND ";
$RTSQL = "SELECT a.id,a.uid,a.path_s,a.addtime,a.agree,b.sex,b.grade,b.nickname,b.photo_s,b.photo_f,b.birthday,b.pay,b.edu FROM ".__TBL_VIDEO__." a,".__TBL_USER__." b WHERE a.flag=1 AND a.uid=b.id AND b.flag=1 ORDER BY a.id DESC";
if ($submitok == 'ajax_list'){
	exit(ajax_list_fn($totalP,$p));
}
/***********************正文开始***************************/
$headertitle = '会员视频 - ';
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
	var share_title = '会员视频 - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>',
	share_desc  = '我在这个网站发布了个人视频，来看看啊,别忘了给我点赞^_^',
	share_link  = '<?php echo HOST; ?>/?z=video',
	share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
		wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
	});
	</script>
<?php }
$mini_title = '　　　视频<a id="btn_add" class="ico">&#xe620;</a>';
$mini_class = 'top_mini huadong';
$mini_ext = 'id="topminibox"';

$nav = 'trend';
?>
<link href="m1/css/video.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php if($_ZEAI['mob_mbkind']==3){?>
<style>.top_mini{background:#FF6F6F}</style>
<?php }?>
<?php 
require_once ZEAI.'m1/top_mini.php';
?>
<main id="main" class="main huadong">
    <!--主BOX-->
    <div id="list" class="listbox">
		<?php
        $total = $db->COUNT(__TBL_VIDEO__," flag = 1");
		$totalP = ceil($total/$_ZEAI['pagesize']);
        echo ajax_list_fn($totalP,1);
		?>
    </div>
</main>

<script>
	var totalP = parseInt(<?php echo $totalP; ?>),p=2;
	window.onload = function () {
		<?php if ($total > $_ZEAI['pagesize']){?>
		o('main').onscroll = listOnscroll;
		<?php }?>
		init();
		//setTimeout(function(){setList(list);},200);
	}
</script>

<?php 
if($_ZEAI['mob_mbkind']==3){
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}
?>
<script src="m1/js/video.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
zeaiLoadBack=['nav','topminibox'];
var browser='<?php echo (is_weixin())?'wx':'h5';?>';
<?php if (!empty($_GZH['wx_gzh_ewm']) && ifint($cook_uid)){wx_endurl('您刚刚浏览的页面【视频】',HOST.'/?z=video');}?>
btn_add.onclick=function(){ZeaiM.page.load('m1/my_info.php?a=video',ZEAI_MAIN,'my_info');}


</script>
<?php
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
	$id            = $rows['id'];
	$uid           = $rows['uid'];
	$path_s        = $rows['path_s'];
	$addtime_str   = date_str($rows['addtime']);
	$sex           = $rows['sex'];
	$grade         = $rows['grade'];
	$nickname      = dataIO($rows['nickname'],'out');
	$photo_s       = $rows['photo_s'];
	$photo_f       = $rows['photo_f'];
	$birthday      = $rows['birthday'];
	$pay           = $rows['pay'];
	$heigh         = $rows['heigh'];
	$edu           = $rows['edu'];
	$agree         = intval($rows['agree']);
	$dst_s         = $_ZEAI['up2'].'/'.$path_s;
	$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
	$ifh4 = ($birthday == '0000-00-00' && empty($pay) && empty($edu))?false:true;
	if ($ifh4){
		$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
		$edu_str      = (empty($edu))?'':udata('edu',$edu).' ';
		$pay_str      = (empty($pay))?'':udata('pay',$pay);
	}
	$echo  = '<li id="'.$id.'">';
	$echo .= '<p value="'.$dst_s.'" ></p>';
	$echo .= '<i class="play ico">&#xe600;</i>';
	
	$echo .= '<dt uid="'.$uid.'" class="uA">';
		$echo .= '<img src="'.$photo_s_url.'">';
		$echo .= '<em><h3>'.$nickname.'</h3>';
		if ($ifh4){
			$echo .= '<h4>'.$birthday_str.$edu_str.$pay_str.'</h4>';
		}
		$echo .= '</em>';
	$echo .= '</dt>';
	$echo .= '<span class="agree" vid="'.$id.'"><i class="ico">&#xe652;</i> <font>'.$agree.'</font></span>';
	$echo .= '</li>';
	return $echo;
}
?>
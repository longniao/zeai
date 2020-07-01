<?php
$parARR = array('index','mate','VIP');
if(in_array($_GET['e'],$parARR)){require_once '../sub/init.php';}
$nav = 'home';
require_once ZEAI.'sub/conn.php';


require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/udata.php';
//
$_ZEAI['pagesize']= 6;
$SQL = " flag=1 AND photo_s<>'' AND photo_f=1 AND dataflag=1 ";
$ORDER = " ORDER BY refresh_time DESC ";
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员～～</div>";
switch ($e) {
	//首页
	case 'index':
		
		
	//匹配
	break;case 'mate':
		$currfields = "mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areatitle,mate_love,mate_house";
		$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index2';
		require_once ZEAI.'my_chk_u.php';
		$mate_age1      = intval($row['mate_age1']);
		$mate_age2      = intval($row['mate_age2']);
		$mate_heigh1    = intval($row['mate_heigh1']);
		$mate_heigh2    = intval($row['mate_heigh2']);
		$mate_pay       = intval($row['mate_pay']);
		$mate_edu       = intval($row['mate_edu']);
		$mate_areaid    = $row['mate_areaid'];
		$mate_love      = intval($row['mate_love']);
		$mate_house     = intval($row['mate_house']);
		if( empty($mate_age1) && empty($mate_age2) && empty($mate_heigh1) && empty($mate_heigh2) && empty($mate_pay) && empty($mate_edu) && empty($mate_areaid) && empty($mate_love) && empty($mate_house)  ){
			json_exit(array('flag'=>'nomate','title'=>'择友条件未设置','msg'=>'只有自己设置完择友条件才能使用'));
		}
		//生成SQL语句
		$SQL .= " AND id<>".$cook_uid;
		$mate_areaid = explode(',',$mate_areaid);
		if (count($mate_areaid) > 0){
			$m1 = $mate_areaid[0];
			$m2 = $mate_areaid[1];
			$m3 = $mate_areaid[2];
		}
		$SQL .= ($cook_sex == 2)?" AND sex=1 ":" AND sex=2 ";
		$areaid = '';
		if (ifint($m1) && ifint($m2) && ifint($m3)){
			$areaid = $m1.','.$m2.','.$m3;
		}elseif(ifint($m1) && ifint($m2)){
			$areaid = $m1.','.$m2;
		}elseif(ifint($m1)){
			$areaid = $m1;
		}
		if (!empty($areaid))$SQL .= " AND areaid LIKE '%".$areaid."%' ";
		if (ifint($mate_age1))$SQL .= " AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= '$mate_age1' ) ";
		if (ifint($mate_age2))$SQL .= " AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <= '$mate_age2' ) ";
		if (ifint($mate_heigh1))$SQL .= " AND ( heigh >= '$mate_heigh1' ) ";
		if (ifint($mate_heigh2))$SQL .= " AND ( heigh <= '$mate_heigh2' ) ";
		if (ifint($mate_weigh1))$SQL .= " AND ( heigh >= '$mate_weigh1' ) ";
		if (ifint($mate_weigh2))$SQL .= " AND ( heigh <= '$mate_weigh2' ) ";
		if (ifint($mate_pay))$SQL .= " AND pay>='$mate_pay' ";
		if (ifint($mate_edu))$SQL .= " AND edu>='$mate_edu' ";
		if (ifint($mate_love))$SQL .= " AND love='$mate_love' ";
		if (ifint($mate_house))$SQL .= " AND house='$mate_house' ";
		//SQL语句结束
	//VIP
	break;case 'VIP':
		$SQL  .=" AND grade>1 ";
		$ORDER = "ORDER BY grade DESC,refresh_time DESC";
	break;
}
if(in_array($_GET['e'],$parARR)){
	$rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");$row = $db->fetch_array($rt,'num');
	$total = $row[0];$totalP= ceil($total/$_ZEAI['pagesize']);
	if($submitok == 'ajax_ulist')exit(ajax_ulist_fn($totalP,$p));
	?>
    <div class="index-ubox" id="indexUlist"><?php echo ajax_ulist_fn($totalP,1);?></div>
    <script>
    var e='<?php echo $e; ?>';
    <?php
    if ($total > $_ZEAI['pagesize']){?>
        var ifmore=true,totalP = parseInt(<?php echo $totalP; ?>),p;
        indexUlist.onscroll = indexOnscroll;	
    <?php }else{?>
        var ifmore=false;
    <?php }?>
    indexLoad(indexUlist);
	<?php if(is_weixin()){?>
	var share_title = '<?php echo dataIO($_INDEX['indexTitle'],'out'); ?>';
	var share_desc  = '<?php echo dataIO($_INDEX['indexContent'],'out'); ?>';
	var share_link  = '<?php echo HOST; ?>/?z=index2';
	var share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
		wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
	});
	<?php }?>
    </script>
<?php exit;}
//
if(ifint($cook_uid)){
	if (!empty($cook_photo_s )){
		$photo_s_url = $_ZEAI['up2'].'/'.getpath_smb($cook_photo_s,'m');
		$photo_s_str = '<p style="background-image:url(\''.$photo_s_url.'\');"></p>';
	}else{
		$photo_s_str = '<i class="i'.$cook_sex.'"></i>';
	}
}else{
	$photo_s_str = '<p style="background-image:url(\''.HOST.'/res/noPindex.png\');"></p>';
	$cook_nickname = '点击登录';
}
$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); $kf_mob=dataIO($_ZEAI['kf_mob'],'out');
$nickname_str = (!empty($cook_nickname))?$cook_nickname:$cook_uname;
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $_INDEX['indexTitle'];?></title>
<meta name="Keywords" content="<?php echo $_INDEX['indexKeywords'];?>">
<meta name="Description" content="<?php echo $_INDEX['indexContent'];?>">
<?php echo HEADMETA; ?>
<meta name="x5-orientation" content="portrait">
<link href="res/www_esyyw_cn.css" rel="stylesheet" type="text/css" />
<link href="m1/css/m1.css" rel="stylesheet" type="text/css" />
<script src="res/www_esyyw_cn.js"></script>
<script src="m1/js/m1.js"></script>
<?php if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="res/jweixin-1.2.0.js"></script>
	<script>wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr: '<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','startRecord','stopRecord','onVoiceRecordEnd','uploadVoice','downloadVoice','playVoice','pauseVoice']});</script>
<?php }?>
<link href="m1/css/index.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="topnav" class="index-topnav">
	<div class="index-so">
    	<div class="logo" id="Ilogo"><img src="../up/p/img/m_logo.gif"></div>
    	<div class="sobox" id="indexso"><i class="ico">&#xe6c4;</i><em>会员搜索</em></div>
        <div class="kefu" id="Ikefubtn"><i class="ico">&#xe64b;</i></div>
    </div>
    <div class="tabmenu tabmenu_3 tabmenuBAI" id="tabmenu_index">
        <li<?php echo ($e == '' || $e=='index')?' class="ed"':''; ?> data="m1/index2.php?e=index" id="index_btn"><span>推荐</span></li>
        <li<?php echo ($e == 'mate')?' class="ed"':''; ?> id="match_btn"><span>匹配</span></li>
        <li<?php echo ($e == 'VIP')?' class="ed"':''; ?> data="m1/index2.php?e=VIP" id="VIP_btn"><span>VIP</span></li>
        <i></i>
        <div id="pushindex_btn">申请显示</div>
    </div>
</div>

<div id="Ikefu">
    <span class="photo" id="I_photo_s"><?php echo $photo_s_str; ?></span>
    <h3 id="indexNkname"><?php echo $nickname_str;?></h3>
    <ul id="Irbtn">
    <a>谁看过我</a>
    <a>我的关注</a>
    <a>关于我们</a>
    </ul>
    <em>
        <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
		<?php if (!empty($kf_tel)){?><a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a><?php }?>
        <?php if (!empty($kf_mob)){?><a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
    </em>
</div>

<div id="main" class="indexmain huadong"></div>
<script src="<?php echo HOST;?>/m1/js/index.js"></script>
<script>
	<?php if (!empty($e)){?>ZeaiM.page.load('m1/<?php echo $e;?>'+zeai.ajxext+'a=<?php echo $a;?>&i=<?php echo $i;?>',ZEAI_MAIN,'<?php echo $e;?>');<?php }?>
	setTimeout(function(){if(zeai.empty(main.innerHTML))index_btn.click();},1000);
</script>
<?php
require_once ZEAI.'m1/bottom.php';
function ajax_ulist_fn($totalP,$p) {
	global $_ZEAI,$SQL,$ORDER,$db,$nodatatips;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);
	
	if($totalP==0 && $p == 1)return $nodatatips;
	if ($p > $totalP)exit("end");
	
	$LIMIT = ($p == 1)?$_ZEAI['pagesize']:$_ZEAI['limit'];
	$rt = $db->query("SELECT id,sex,grade,nickname,photo_s,photo_f,birthday,pay,job,RZ FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER." LIMIT ".$LIMIT);
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
	global $_ZEAI,$cook_uid; 
	$uid           = $rows['id'];
	$sex           = $rows['sex'];
	$grade         = $rows['grade'];
	$nickname      = dataIO($rows['nickname'],'out');
	$photo_s       = $rows['photo_s'];
	$photo_f       = $rows['photo_f'];
	$age = getage($rows['birthday']);
	$RZ  = $rows['RZ'];
	$pay = $rows['pay'];
	$age_str = ($age>18)?$age.'岁':'';
	
	if(empty($pay) && empty($age)){
		$ifdata = false;
	}else{
		$pay_str = udata('pay',$pay).'/月';
		$job_str = udata('job',$rows['job']);
		$ifdata = true;
	}
	$gzclass='';$gz_str='关注';
	if(ifint($cook_uid) && gzflag($uid,$cook_uid) == 1){
		$gzclass=' class="ed"';$gz_str='已关注';
	}
	
	$photo_s_url   = (!empty($photo_s) )?$_ZEAI['up2'].'/'.getpath_smb($photo_s,'b'):HOST.'/res/photo_m'.$sex.'.png';
	$echo .= '<a uid="'.$uid.'" sex="'.$sex.'" class="small_big"><img src="'.$photo_s_url.'" />';
	
	$echo .= '<div class="uinfo">';
		$echo.= '<div class="nik"><span>'.$nickname.uicon($sex.$grade).'</span><font>'.$age_str.'</font></div>';
		if($ifdata)$echo.= '<div class="data"><span>'.$pay_str.'</span><span>'.$job_str.'</span></div>';
		$echo.= '<div class="fineW"></div>';
		$echo.= '<div class="gzhi"><span><i'.$gzclass.'></i><h5>'.$gz_str.'</h5></span><span class="fineH"></span><span><i></i><h6>打招呼</h6></span></div>';
	$echo.= '</div>';
	
	if (!empty($RZ))$echo.= '<div class="payrz">'.RZ_html($RZ,'s','color').'</div>';
	
	$echo.= '</a>';
	return $echo;
}


/*if(ifint($cook_uid) ){
	$row = $db->NUM($cook_uid,"openid");
	if ($row){
		$data_openid = $row[0];
		if(is_weixin() && empty($data_openid)){
			$data_openid = wx_get_openid();
			if(str_len($data_openid)>20)$db->query("UPDATE ".__TBL_USER__." SET openid='$data_openid' WHERE id=".$cook_uid);
		}
	}
}
*/?>

<script>
	<?php
	$if_other_login_subscribe = openid_chk();
	if ($if_other_login_subscribe){
		
		?>
		zeai.msg('当前微信号已被其它帐号绑定，请退出使用微信登录');
		<?php
	}
	?>
</script>
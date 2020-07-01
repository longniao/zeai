<?php
$parARR = array('index','mate','VIP');
if(in_array($_GET['e'],$parARR)){require_once '../sub/init.php';}
$nav = 'home';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';

$if_other_login_subscribe = openid_chk();


$_ZEAI['pagesize']= 6;
$SQL = " flag=1 AND photo_s<>'' AND photo_f=1 AND dataflag=1 ";
$ORDER = " ORDER BY refresh_time DESC ";
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员～～</div>";
switch ($e) {
	//首页
	case 'index':
	//匹配
	break;case 'mate':
		$currfields = "mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2";
		$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index';
		require_once ZEAI.'my_chk_u.php';
		$mate_age1      = intval($row['mate_age1']);
		$mate_age2      = intval($row['mate_age2']);
		$mate_heigh1    = intval($row['mate_heigh1']);
		$mate_heigh2    = intval($row['mate_heigh2']);
		$mate_pay       = $row['mate_pay'];
		$mate_edu       = $row['mate_edu'];
		$mate_areaid    = $row['mate_areaid'];
		$mate_love      = $row['mate_love'];
		$mate_car       = $row['mate_car'];
		$mate_house     = $row['mate_house'];
		//
		$mate_weigh1      = intval($row['mate_weigh1']);
		$mate_weigh2      = intval($row['mate_weigh2']);
		$mate_job         = $row['mate_job'];
		$mate_child       = $row['mate_child'];
		$mate_marrytime   = $row['mate_marrytime'];
		$mate_companykind = $row['mate_companykind'];
		$mate_smoking     = $row['mate_smoking'];
		$mate_drink       = $row['mate_drink'];
		$mate_areaid2     = $row['mate_areaid2'];
		if( empty($mate_age1) && empty($mate_age2) && empty($mate_heigh1) && empty($mate_heigh2) && empty($mate_pay) && empty($mate_edu) && empty($mate_areaid) && empty($mate_love) && empty($mate_house) && empty($mate_weigh1) && empty($mate_weigh2) && empty($mate_job) && empty($mate_child) && empty($mate_marrytime) && empty($mate_companykind) && empty($mate_smoking) && empty($mate_drink) && empty($mate_areaid2) ){
			json_exit(array('flag'=>'nomate','title'=>'择友条件未设置','msg'=>'只有自己设置完择友条件才能使用'));
		}
		//生成SQL语句
		$SQL .= " AND id<>".$cook_uid;
		$SQL .= ($cook_sex == 2)?" AND sex=1 ":" AND sex=2 ";
		$SQL .= mate_diy_SQL();
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
	var share_link  = '<?php echo HOST; ?>/?z=index';
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
<link href="res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="m1/css/m1.css" rel="stylesheet" type="text/css" />
<script src="res/www_zeai_cn.js"></script>
<script src="m1/js/m1.js"></script>
<?php if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="res/jweixin-1.2.0.js"></script>
	<script>wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr: '<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','startRecord','stopRecord','onVoiceRecordEnd','uploadVoice','downloadVoice','playVoice','pauseVoice']});</script>
<?php }?>
<link href="m2/css/index.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="topnav" class="index-topnav">
	<div class="index-so">
    	<div class="logo" id="Ilogo"><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo'];?>"></div>
    	<div class="sobox" id="indexso"><i class="ico">&#xe6c4;</i><em>会员搜索</em></div>
        <div class="kefu" id="Ikefubtn"><i class="ico">&#xe64b;</i></div>
    </div>
    <div class="tabmenu tabmenu_3 tabmenuBAI" id="tabmenu_index">
        <li<?php echo ($e == '' || $e=='index')?' class="ed"':''; ?> data="m2/index.php?e=index" id="index_btn"><span>推荐</span></li>
        <li<?php echo ($e == 'mate')?' class="ed"':''; ?> id="match_btn"><span>匹配</span></li>
        <li<?php echo ($e == 'VIP')?' class="ed"':''; ?> data="m2/index.php?e=VIP" id="VIP_btn"><span>VIP</span></li>
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

<?php if (ifint($cook_uid)){?>
    <div id='index_tips0_100_0' class='tips0_100_0 alpha0_100_0'></div>
    <!-- -->
    <div id="main_chat_daylooknumHelp" class="helpDiv">
        <ul>
        <?php
        //$urole = json_decode($_ZEAI['urole']);
		$urolenew = json_decode($_ZEAI['urole'],true);
		$newarr=array();foreach($urolenew as $RV){if($RV['f']==1){$newarr[]=$RV;}else{continue;}}
		$newarr=encode_json($newarr);
		$urole = json_decode($newarr);
		
		$chat_daylooknum = json_decode($_VIP['chat_daylooknum']);
		$chat_loveb      = json_decode($_VIP['chat_loveb']);
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_daylooknum->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> 人/天':' 无权聊天';
            $ifmy = ($cook_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
            $outA .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outA;
        ?>
        </ul>
        <a class="btn size3 HUANG Mcenter block center" onClick="ZeaiM.page.load('m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent('<?php echo HOST.'/?z=index&e=u&a='.$uid;?>'),'main','my_vip');">我要升级会员</a>
    </div>
    <div id="main_chat_lovebHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_loveb->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> '.$_ZEAI['loveB'].'/人':' 免费聊天';
            if($cook_grade==$grade){
                $ifmy = '　<font class="Cf00">（我）</font>';
                $myclkB=$num;
            }else{
                $ifmy = '';
            }
            $outI .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outI;
        ?>
        </ul>
        <a class="btn size3 HONG W50_" onClick="clickloveb('chat','main')">单次<?php echo $myclkB;?>解锁</a>
        <a class="btn size3 HUANG W50_" onClick="ZeaiM.page.load('m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent('<?php echo HOST.'/?z=index&e=u&a='.$uid;?>'),'main','my_vip');">升级会员</a>
    </div>
<?php }?>
<!-- -->
<script src="<?php echo HOST;?>/m2/js/index.js"></script>
<script>
var browser='<?php echo (is_weixin())?'wx':'h5';?>';
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
	global $_ZEAI,$db,$cook_uid; 
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
	
	$ifhiher=false;$hiclass='';$hi_str='打招呼';
	if(ifint($cook_uid)){
		$ifhiher = ($db->COUNT(__TBL_TIP__," senduid=".$cook_uid." AND uid=".$uid." AND kind=3") > 0)?true:false;
	}
	if($ifhiher){
		$hiclass=' class="ed"';$hi_str='聊天';
	}
	
	$photo_s_url   = (!empty($photo_s) )?$_ZEAI['up2'].'/'.getpath_smb($photo_s,'b'):HOST.'/res/photo_m'.$sex.'.png';
	$echo .= '<a uid="'.$uid.'" sex="'.$sex.'" class="small_big"><img src="'.$photo_s_url.'" />';
	
	$echo .= '<div class="uinfo">';
		$echo.= '<div class="nik"><span>'.$nickname.uicon($sex.$grade).'</span><font>'.$age_str.'</font></div>';
		if($ifdata)$echo.= '<div class="data"><span>'.$pay_str.'</span><span>'.$job_str.'</span></div>';
		$echo.= '<div class="fineW"></div>';
		$echo.= '<div class="gzhi"><span><i'.$gzclass.'></i><h5>'.$gz_str.'</h5></span><span class="fineH"></span><span><i'.$hiclass.'></i><h6>'.$hi_str.'</h6></span></div>';
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
	
	if ($if_other_login_subscribe){
		
		?>
		zeai.msg('当前微信号已被其它帐号绑定，请退出使用微信登录');
		<?php
	}
	?>
</script>
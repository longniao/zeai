<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
$nodatatips = "<div class='nodatatips' style='margin:0 auto'><br><br><span class='ico'>&#xe651;</span>暂时木有会员～～</div>";
/***********************主体入口***************************/
$chk_u_jumpurl=HOST.'/m1/index_more_big.php';
$currfields = "sex,latitude,longitude";
require_once ZEAI.'my_chk_u.php';
setcookie("cook_sex",$row['sex'],null,"/",$_ZEAI['CookDomain']);
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/udata.php';

$_ZEAI['pagesize']= 1;$_ZEAI['limit']=99999;
$fields="id,sex,grade,uname,nickname,photo_s,birthday,heigh,areatitle";
$SQL   = " flag=1 AND photo_ifshow=1 AND photo_s<>'' AND photo_f=1 AND dataflag=1 AND id<>".intval($cook_uid);
$ORDER = " ORDER BY refresh_time DESC ";

if($_INDEX['iModuleU']==1){
	$SQL .= ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
}

switch ($t) {
	//index 推荐
	default:
		$mt  = '更多优质会员';
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
		//
	break;
	case 'fj':
		$data_latitude = $row['latitude'];
		$data_longitude= $row['longitude'];
		$mt  = '离我最近的会员';
		//
		if(!empty($data_latitude) && !empty($data_longitude) && is_weixin()){
			$SQL  .= " AND longitude<>'' AND latitude<>'' ";
			$RTSQL = "SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,heigh,pay,ROUND(6378.138*2*ASIN(SQRT(POW(SIN((".$data_latitude."*PI()/180-latitude*PI()/180)/2),2)+COS(".$data_latitude."*PI()/180)*COS(latitude*PI()/180)*POW(SIN((".$data_longitude."*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY distance";
		}else{
			$RTSQL = "SELECT id FROM ".__TBL_USER__." WHERE 1=2";
			$nodatatips = "<div class='nodatatips' style='margin:0 auto'><br><br><span class='ico'>&#xe651;</span>请使用微信打开并设置定位～～</div>";
		}
	break;
	case 'vip':
		$mt  = 'VIP会员';
		$SQL.=" AND grade>1 ";
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
	break;
	case 'sex1':
		$mt  = '优质男会员';
		$SQL.=" AND sex=1 ";
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
	break;
	case 'sex2':
		$mt  = '优质女会员';
		$SQL.=" AND sex=2 ";
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
	break;
	case 'pp':
		$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2"," id=".$cook_uid);
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
		//生成SQL语句
		$SQL .= " AND id<>".$cook_uid;
		$SQL .= ($cook_sex == 2)?" AND sex=1 ":" AND sex=2 ";
		$SQL .= mate_diy_SQL();
		//SQL语句结束
		$mt  = '配匹我的会员';
		//$mini_R = '<a href="javascript:;" id="index_more_ppbtn" onClick=\'page({g:"m1/my_info"+zeai.ajxext+"a=mate&href=mate",y:"index_more_ulist",l:"my_info"});\'>重设条件</a>';
		
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
	break;
}
$nav = 'home';
$total = $db->COUNT(__TBL_USER__,$SQL);
$totalP= ceil($total/$_ZEAI['pagesize']);
if($submitok == 'ajax_getdata')exit(ajax_ulist_fn($totalP,$p,$t));
$headertitle = $mt.' - ';
require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug: false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	</script>
<?php }?>
<link href="<?php echo HOST;?>/m1/css/index_more_big.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<main id='main' class='main huadong'>
<div class="masktop"></div>
<div class="zeaiSlide" id="zeaiSlideObj">
    <?php if ($total > 0){?>
    	<div id="mbox" class="mbox"></div>
    	<div class="slidbtn">
		<span class="pass" id="slidepass"><i class="ico">&#xe604;</i></span>
		<span class="hi fadeInUp" id="slidehi"><i class="ico">&#xe8ca;</i></span>
		<span class="gz" id="slidegz"><i class="ico">&#xe62f;</i></span>
        </div>
     <?php }else{echo $nodatatips;}?>
</div>
</main>
<div class="index_more_big_yd" id="index_more_big_yd"><img src="<?php echo HOST;?>/m1/img/index_more_big_yd.png"></div>
<div id='index_more_big0_100_0' class='tips0_100_0 alpha0_100_0'></div>
<?php
require_once ZEAI.'m1/bottom.php';
?><script>var t='<?php echo $t;?>';
if(zeai.empty(sessionStorage.index_more_big_yd)){
	zeai.mask({fobj:main,son:index_more_big_yd,cancelBubble:'off',close:function(){
		sessionStorage.index_more_big_yd='WWWzeaiCN';
	}});
}
</script>
<?php if ($total > 0){?>
<script src="<?php echo HOST;?>/m1/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo HOST;?>/m1/js/zeaiSlide.js"></script>
<?php }?>
<?php
if (!empty($_GZH['wx_gzh_ewm']) && ifint($cook_uid)){wx_endurl('您刚刚浏览的页面【大照片滑动预览】',HOST.'/m1/index_more_big.php');}
function ajax_ulist_fn($totalP,$p,$t) {
	global $_ZEAI,$db,$nodatatips,$RTSQL;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);
	if($totalP==0 && $p == 1)return $nodatatips;
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
	for($n=1;$n<=$fort;$n++){
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_ulist .= rows_ulist($rows,$t);
	}
	return $rows_ulist;
}
function rows_ulist($rows,$t) {
	global $_ZEAI; 
	$uid           = $rows['id'];
	$sex           = $rows['sex'];
	$grade         = $rows['grade'];
	$uname     = dataIO($rows['uname'],'out');
	$nickname  = dataIO($rows['nickname'],'out');
	$photo_s       = $rows['photo_s'];
	$birthday  = $rows['birthday'];
	$areatitle= dataIO($rows['areatitle'],'out');
	$birthday = getage($birthday);$birthday=($birthday>=18)?$birthday.'岁 ':'';
	$heigh    = (!empty($rows['heigh']))?$rows['heigh'].'cm ':'';
	$nickname = (empty($nickname))?$uname:$nickname;
	$photo_b_url   = $_ZEAI['up2'].'/'.getpath_smb($photo_s,'b');
	$echo .= '<a onClick="slidulink('.$uid.')" style=\'background-image:url("'.$photo_b_url.'")\'></a>';/*<img src="'.$photo_b_url.'" class="m" />*/
	$echo .= '<div class="uinfo">';
		$echo.= '<span class="nik">'.uicon($sex.$grade).$nickname.'</span>';
		if($t=='fj'){
			$areatitle_str = str_replace("不限","",$areatitle);
			$distance = $rows['distance'];
			if ($distance<1000){
				$distance_str  = $distance.'m';
			}else{
				$distance_str  = intval($distance/1000).'km';
			}
			$echo.= '<div class="data"><span>'.$birthday.$heigh.$areatitle.'</span><span><font class="ico">&#xe614;</font>'.$distance_str.'</span></div>';
		}else{
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$echo.= '<span class="data">'.$birthday.$heigh.$areatitle.'</span>';	
		}
	$echo.= '</div>';
	$echo.= '<i class="ico" uid="'.$uid.'">&#xe604;</i>';
	return $echo;
}
?>
<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
//$currfields = "sex,birthday,loveb,photo_f,photo_s,heigh,pay,refresh_time,dataflag";
$currfields = "sex,latitude,longitude";
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/udata.php';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员～～</div>";
$_ZEAI['pagesize']= 8;
$SQL  = " flag=1 AND dataflag=1 AND kind<>4 ";/*(kind=1 OR kind=3) AND 不强制照片  AND photo_f=1*/
if($_INDEX['iModuleU']==1){
	$SQL .= ($row['sex']==2)?" AND sex=1 ":" AND sex=2 ";
}
setcookie("cook_sex",$row['sex'],null,"/",$_ZEAI['CookDomain']);
$fields="id,sex,grade,nickname,photo_s,photo_f,birthday,pay,job,RZ,photo_ifshow";
$ORDER = " ORDER BY refresh_time DESC ";
$cs  = "t=".$t;

switch ($t) {
	//index 推荐
	default:
		$mt  = '更多优质会员';
		$SQL.= " AND photo_s<>'' AND photo_f=1 ";
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
		//
		$rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");
		$row   = $db->fetch_array($rt,'num');
		$total = $row[0];
		$totalP= ceil($total/$_ZEAI['pagesize']);
	break;
	case 'fj':
		$data_latitude = $row['latitude'];
		$data_longitude= $row['longitude'];
		$mt  = '<i class="ico" style="display:inline-block;color:#49BEF5;font-size:19px;margin-right:4px">&#xe614;</i>离我最近的会员';
		//
		if(!empty($data_latitude) && !empty($data_longitude) && is_weixin()){
			$SQL  .= " AND longitude<>'' ";
			$RTSQL = "SELECT id,nickname,sex,grade,photo_s,photo_ifshow,photo_f,areatitle,birthday,job,heigh,pay,ROUND(6378.138*2*ASIN(SQRT(POW(SIN((".$data_latitude."*PI()/180-latitude*PI()/180)/2),2)+COS(".$data_latitude."*PI()/180)*COS(latitude*PI()/180)*POW(SIN((".$data_longitude."*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY distance";
		}else{
			json_exit(array('flag'=>0,'msg'=>'请使用微信打开并同意获取自己的定位'));
		}
		//
		$rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");
		$row   = $db->fetch_array($rt,'num');
		$total = $row[0];
		$totalP= ceil($total/$_ZEAI['pagesize']);
	break;
	case 'vip':
		$mt  = 'VIP会员';
		$SQL.=" AND grade>1 ";
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
		//
		$rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");
		$row   = $db->fetch_array($rt,'num');
		$total = $row[0];
		$totalP= ceil($total/$_ZEAI['pagesize']);
	break;
	case 'sex1':
		$mt  = '优质男会员';
		$SQL.=" AND sex=1 ";
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
		//
		$rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");
		$row   = $db->fetch_array($rt,'num');
		$total = $row[0];
		$totalP= ceil($total/$_ZEAI['pagesize']);
	break;
	case 'sex2':
		$mt  = '优质女会员';
		$SQL.=" AND sex=2 ";
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
		//
		$rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");
		$row   = $db->fetch_array($rt,'num');
		$total = $row[0];
		$totalP= ceil($total/$_ZEAI['pagesize']);
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
		$mini_R = '<a href="javascript:;" id="index_more_ppbtn" onClick=\'page({g:"m1/my_info"+zeai.ajxext+"a=mate&href=mate",y:"index_more_ulist",l:"my_info"});\'>重设条件</a>';
		
		$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
		//
		$rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");
		$row   = $db->fetch_array($rt,'num');
		$total = $row[0];
		$totalP= ceil($total/$_ZEAI['pagesize']);
	break;
}

//
if($submitok == 'ajax_ulist')exit(ajax_ulist_fn($totalP,$p,$t));
//


$mini_title= $mt;
$mini_title .= '<i class="ico goback" id="ZEAIGOBACK-index_more_ulist">&#xe602;</i>';
$mini_class = 'top_mini top_miniBAI';
require_once ZEAI.'m1/top_mini.php';
?>
<link href="m1/css/search.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="res/select3_ajax.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="m1/js/index_more.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<div class="submain search_ulist huadong" id="index_moreUlist"><?php echo ajax_ulist_fn($totalP,1,$t);?></div><script>
var scs='<?php echo $cs; ?>';
<?php
if ($total > $_ZEAI['pagesize']){?>
	var ifmore_i=true,totalP_i = parseInt(<?php echo $totalP; ?>),i_so;o('index_moreUlist').onscroll = index_moreOnscroll;	
<?php }else{?>
	var ifmore_i=false;
<?php }?>
index_moreInit();//index_moreLoad();

</script>
    
<?php
/*******************************************/
function ajax_ulist_fn($totalP,$p,$t) {
	global $_ZEAI,$db,$nodatatips,$RTSQL,$cook_grade;
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
	//
	$switch = json_decode($_ZEAI['switch'],true);
	$blurclass = '';$lockstr = '';$ifblur=0;
	if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
		$blurclass = ' blur';$lockstr = '<i class="ico lockico">&#xe61e;</i><div class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</div>';
		$ifblur=1;
	}
	//
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_ulist .= rows_ulist($rows,$t,$ifblur,$lockstr,$blurclass);
	}
	return $rows_ulist;
}
function rows_ulist($rows,$t,$ifblur=0,$lockstr='',$blurclass='') {
	global $_ZEAI; 
	$uid      = $rows['id'];
	$sex      = $rows['sex'];
	$grade    = $rows['grade'];
	$nickname = dataIO($rows['nickname'],'out');
	$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
	$photo_s  = $rows['photo_s'];
	$photo_f  = $rows['photo_f'];
	$job      = $rows['job'];
	$pay      = $rows['pay'];
	$age = getage($rows['birthday']);
	$RZ  = $rows['RZ'];
	$age_str = ($age>18)?$age.'岁':'';
	$pay_str = (ifint($pay))?udata('pay',$pay):'';
	$job_str = udata('job',$job);
	$photo_ifshow = $rows['photo_ifshow'];
	if($ifblur==1){
		$photo_m = 'blur';
		if(empty($photo_s) || $photo_f==0)$lockstr='';
	}else{
		$photo_m = 'm';
	}
	//
	if($photo_ifshow==0 && $ifblur==0){
		$lockstr = '';
		$photo_m_url='res/photo_m'.$sex.'_hide.png';
	}else{
		$photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.getpath_smb($photo_s,$photo_m):HOST.'/res/photo_m'.$sex.'.png';	
	}
	//
	$echo .= '<a uid="'.$uid.'" sex="'.$sex.'">';
	$vipj = ($grade>1)?'<img src="m1/img/vipj.png?1">':'';
	$echo .= '<em class="small_big'.$blurclass.'" style=\'background-image:url("'.$photo_m_url.'");\'>';
		if (!empty($RZ))$echo.= '<div class="payrz">'.RZ_html($RZ,'s','color').'</div>';
	$echo .= $lockstr.$vipj.'</em>';
	$echo .= '<div class="uinfo">';
		$echo.= '<div class="nik"><span>'.uicon($sex.$grade).$nickname.'</span><font>'.$age_str.'</font></div>';
		if($t=='fj'){
			$areatitle=$rows['areatitle'];
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			$areatitle_str = str_replace("不限","",$areatitle_str);
			$distance = $rows['distance'];
			if ($distance<1000){
				$distance_str  = $distance.'m';
			}else{
				$distance_str  = intval($distance/1000).'km';
			}
			$echo.= '<div class="data"><span>'.$areatitle.'</span><span><i class="ico">&#xe614;</i>'.$distance_str.'</span></div>';
		}else{
			$echo.= '<div class="data"><span>'.$pay_str.'</span><span>'.$job_str.'</span></div>';			
		}
	$echo.= '</div>';
	$echo.= '</a>';
	return $echo;
}
?>
<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_tjdiy.php';
function tjdiy_info($id,$k) {
	global $_TJDIY;
	$ARR = json_decode($_TJDIY['tjdiy'],true);
	if (count($ARR) >= 1 && is_array($ARR)){
		foreach ($ARR as $V) {
			if($V['id']==$id){
				switch ($k) {
					case 'title':return $V['title'];break;
					case 'par':return $V['par'];break;
				}			
			}
		}
	}
	return false;
}	

$_ZEAI['pagesize']= 6;$_ZEAI['limit']=999999;
$SQL = " flag=1 AND photo_s<>'' AND photo_f=1 AND dataflag=1 AND kind<>4 AND nickname<>''";
$ORDER = " ORDER BY refresh_time DESC,id DESC";
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员～～</div>";
if(ifint($cook_uid) && !empty($cook_sex) && $zeai_cn!='diy' ){
	$SQL .= ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
}
$FLD = "id,sex,grade,nickname,photo_s,photo_f,birthday,pay,job,RZ,photo_ifshow";
$parARR = array('rz','vip','new','diy');
$zeai_cn = (!in_array($zeai_cn,$parARR))?'rz':$zeai_cn;
switch ($zeai_cn) {
	case 'rz':
		$tt = '认证专区';
		$SQL .= " AND (FIND_IN_SET('identity',RZ) OR FIND_IN_SET('photo',RZ)) ";
	break;
	case 'vip':
		$tt = 'VIP专区';
		$SQL .= " AND grade>1 ";
	break;
	case 'new':
		$tt = '最新注册';
		$ORDER = " ORDER BY id DESC";
	break;
	
	case 'diy':
		if(ifint($diyid)){
			$tt=tjdiy_info($diyid,'title');
		}
		$tt=(!empty($tt))?$tt:'红娘推荐';
		if (!empty($areaid2))$SQL  .= " AND area2id LIKE '%".$areaid2."%' ";
		if (ifint($sex))$SQL       .= " AND sex=$sex ";
		if (!empty($areaid))$SQL   .= " AND areaid LIKE '%".$areaid."%' ";
		if (ifint($age1))$SQL      .= " AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= $age1 ) ";
		if (ifint($age2))$SQL      .= " AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <= $age2 ) ";
		if (ifint($pay))$SQL       .= " AND pay>=$pay ";
		if (ifint($edu))$SQL       .= " AND edu>=$edu ";
		if (ifint($job))$SQL       .= " AND job=$job ";
		if (ifint($love))$SQL      .= " AND love=$love ";
		if (ifint($child))$SQL     .= " AND child=$child ";
		if (ifint($marrytype))$SQL .= " AND marrytype=$marrytype ";
		if (ifint($marrytime))$SQL .= " AND marrytime=$marrytime ";
		if (ifint($heigh1))$SQL    .= " AND ( heigh >= $heigh1 ) ";
		if (ifint($heigh2))$SQL    .= " AND ( heigh <= $heigh2 ) ";
		if (ifint($weigh1))$SQL    .= " AND ( heigh >= $weigh1 ) ";
		if (ifint($weigh2))$SQL    .= " AND ( weigh <= $weigh2 ) ";
		if (ifint($car))$SQL       .= " AND car>=$car ";
		if (ifint($house))$SQL     .= " AND house=$house ";
		if (ifint($smoking))$SQL     .= " AND smoking=$smoking ";
		if (ifint($drink))$SQL       .= " AND drink=$drink ";
		if (ifint($companykind))$SQL .= " AND companykind=$companykind ";
		if (ifint($ifmob))$SQL       .= " AND mob<>'' ";
		if (ifint($ifdata))$SQL       .= " AND myinfobfb>10 ";
		if (ifint($ifbz))$SQL       .= " AND bz<>'' ";
		if ($ifadmid==1)$SQL .= " AND admid>0 ";
		if ($iftguid==1)$SQL .= " AND tguid>0 ";
		if($ifdata50 == 1)$SQL  .= " AND myinfobfb>50 ";
		if($ifparent == 1)$SQL  .= " AND parent>1 ";
		if ($photo_s == 1)$SQL  .= " AND photo_s<>'' ";
		if ($grade2 == 1)$SQL   .= " AND grade>1 ";
		if (ifint($grade))$SQL  .= " AND grade=".$grade;
		$requrl=str_replace("/?","",$_SERVER['REQUEST_URI']);
	break;
}
$SELECT = "SELECT ".$FLD." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
$total  = $db->COUNT(__TBL_USER__,$SQL);
$totalP = ceil($total/$_ZEAI['pagesize']);
if($submitok == 'ajax_ulist')exit(ajax_ulist_fn($totalP,$p));
$nav = 'tj';
/***********************开始***************************/
$headertitle = '推荐会员 - ';
require_once ZEAI.'m1/header.php';?>
<link href="<?php echo HOST;?>/m1/css/tuijian.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tjnavbox .tjnav a i{background-image:url('<?php echo HOST;?>/m1/img/ico_tuijian.png?<?php echo $_ZEAI['cache_str'];?>')}
<?php if($_ZEAI['mob_mbkind']==3){
	$linec='#FF6F6F';
	?>
.tjnavbox,#backtop a,#btmKefuBtn{background-color:#FF6F6F}
<?php }else{
	$linec='#E83191';
	?>
.tjnavbox,#backtop a,#btmKefuBtn{background-color:#E83191}
<?php }?>
</style>
<div class="submaintj submain2 huadong" id="main">
    <div class="tjnavbox">
    	<h2><?php echo $tt;?></h2>
        <div class="tjnav">
            <a href="<?php echo HOST;?>/?z=tuijian"<?php echo ($zeai_cn == 'rz')?' class="ed"':'';?>><i class="tj_rz"></i><span>认证专区</span></a>
            <a href="<?php echo HOST;?>/?z=tuijian&zeai_cn=vip"><i class="tj_vip"></i><span>VIP专区</span></a>
            <a href="<?php echo HOST;?>/?z=tuijian&zeai_cn=new"><i class="tj_new"></i><span>最新注册</span></a>
            <a id="tjbtna"><i class="tj_hn"></i><span>红娘推荐</span></a>
            <div class="diybtn" id="tjbtn">
                <i class="ico off">&#xe60b;</i>
            </div>
        </div>
    </div>
    <div id="ulist" class="tj-ubox"><?php echo ajax_ulist_fn($totalP,1);?></div>
</div>

<?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');?>
<a href="javascript:;" id="btmKefuBtn"><i class="ico">&#xe6a6;</i>客服</a>
<div id="btmKefuBox" class="my-subscribe_box"><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><h3>长按二维码添加客服微信<br />
<?php if (!empty($kf_tel)){?><a href="tel:<?php echo $kf_tel;?>" class="C666"><i class="ico S18" style="display:inline-block">&#xe60e;</i> <?php echo $kf_tel;?></a><?php }else{if (!empty($kf_mob)){?><a href="tel:<?php echo $kf_mob;?>" class="C666"><i class="ico S18" style="display:inline-block">&#xe60e;</i> <?php echo $kf_mob;?></a><?php }}?>
</h3></div>
<div id="diybox" class="diybox">
    <div class="s1box">
		<div class="linebox"><div class="line W50"></div><div class="title BAI S18 B" style="color:<?php echo $linec;?>">红娘推荐</div></div>
        <ul>
			<?php
            for($i=1;$i<=$_TJDIY['tjdiy_num'];$i++){
                $title=tjdiy_info($i,'title');
                $par=tjdiy_info($i,'par');
				if(empty($title)||empty($par))continue;?>
				<a href="<?php echo HOST;?>/?z=tuijian&zeai_cn=diy&diyid=<?php echo $i;?>&<?php echo $par;?>"><h4><?php echo $title;?></h4></a>
            <?php }?>
        </ul>
    </div>
</div>
<script src="<?php echo HOST;?>/m1/js/tuijian.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var zeai_cn='<?php echo $zeai_cn; ?>',requrl='<?php echo $requrl;?>';
<?php
if ($total > $_ZEAI['pagesize']){?>
	var ifmore=true,totalP = parseInt(<?php echo $totalP; ?>),p=1;
	main.onscroll = tjOnscrollFn;	
<?php }else{?>
	var ifmore=false;
<?php }?>
window.onload = function () {setTimeout(function(){tjInit(ulist);},50);}
zeaiLoadBack=['nav'];
tjbtn.onclick=tjbtnFn;
tjbtna.onclick=tjbtnFn;
</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST;?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	var share_title = '推荐会员 - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>',
	share_desc  = '推荐会员，来看看啊^_^',
	share_link  = '<?php echo HOST; ?>/?z=tuijian',
	share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
		wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
	});
	</script>
<?php }
if($_ZEAI['mob_mbkind']==3){
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}
function ajax_ulist_fn($totalP,$p) {
	global $_ZEAI,$db,$nodatatips,$SELECT;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);
	if($totalP==0 && $p == 1)return $nodatatips;
	if ($p > $totalP)exit("end");
	$LIMIT = ($p == 1)?$_ZEAI['pagesize']:$_ZEAI['limit'];
	$rt = $db->query($SELECT." LIMIT ".$LIMIT);
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
	global $_ZEAI,$_INDEX,$cook_uid,$cook_sex,$cook_grade,$zeai_cn;
	//
	$switch = json_decode($_ZEAI['switch'],true);
	$blurclass = '';$lockstr = '';$ifblur=0;
	if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
		$lockstr = '<i class="ico lockico">&#xe61e;</i><div class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</div>';
		$ifblur=1;
		$photo_b = 'blur';
	}else{
		$photo_b = ($_INDEX['waterfall_photo']=='b')?'b':'m';
	}
	//
	$uid           = $rows['id'];
	$sex           = $rows['sex'];
	$grade         = $rows['grade'];
	$nickname      = dataIO($rows['nickname'],'out');
	$photo_s       = $rows['photo_s'];
	$photo_f       = $rows['photo_f'];
	$age = getage($rows['birthday']);
	$RZ  = $rows['RZ'];
	$pay = $rows['pay'];
	$photo_ifshow = $rows['photo_ifshow'];
	$age_str = ($age>18)?$age.'岁':'';
	if(empty($pay) && empty($age)){
		$ifdata = false;
	}else{
		$pay_str = udata('pay',$pay).'/月';
		$job_str = udata('job',$rows['job']);
		$ifdata = true;
	}
	//
	if($photo_ifshow==0 && $ifblur==0){
		$lockstr = '';
		$photo_b_url=HOST.'/res/photo_m'.$sex.'_hide.png';
	}else{
		$photo_b_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.smb($photo_s,$photo_b):'res/photo_m'.$sex.'.png';	
	}
	//
	$echo .= '<a uid="'.$uid.'" sex="'.$sex.'"><img class="small_big" src="'.$photo_b_url.'" />';
	$echo .= '<div class="uinfo">';
	$echo .= '<div class="nik"><span>'.uicon($sex.$grade).$nickname.'</span><font>'.$age_str.'</font></div>';
	if($ifdata)$echo.= '<div class="data"><span>'.$pay_str.'</span><span>'.$job_str.'</span></div>';
	$echo .= '</div>';
	if($zeai_cn=='rz'){
		$echo.= '<img src="'.HOST.'/m1/img/rzj.png" class="rzj">';
	}else{
		$echo.= ($grade>1)?'<img src="'.HOST.'/m3/img/vipj.png" class="vipj">':'';
	}
	if (!empty($RZ))$echo.= '<div class="payrz">'.RZ_html($RZ,'s','color').'</div>';
	$echo.= $lockstr.'</a>';
	return $echo;
}
?>

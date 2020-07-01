<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if(is_mobile())header("Location: ".HOST);
if(!ifint($cook_uid) && empty($areakey) && empty($submitok))header("Location: ".HOST."/p1/login.php?jumpurl=".Href('user'));
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_index.php';
$nav='user';
if($submitok=='ajax_ifcert'){
	if(!ifint($cook_uid)||!iflogin()){
		json_exit(array('flag'=>'nologin','msg'=>'请您先登录','jumpurl'=>Href('user')));	
	}else{nocert($cook_uid);}
	json_exit(array('flag'=>1));
}elseif($submitok=='ajax_ifnear'){	
	if(!ifint($cook_uid)||!iflogin()){
		json_exit(array('flag'=>'nologin','msg'=>'请您先登录','jumpurl'=>Href('user')));	
	}else{
		$row = $db->NAME("W.W.W.z.e.a.i.C..N","id,latitude","(latitude='' OR longitude='') AND id=".$cook_uid);
		if ($row)json_exit(array('flag'=>'nogps','msg'=>'请您先设置自己的位置才能使用','jumpurl'=>Href('user')));	
	}
	json_exit(array('flag'=>1));
}elseif($submitok=='ajax_pp'){	
	if(!ifint($cook_uid)||!iflogin()){
		json_exit(array('flag'=>'nologin','msg'=>'请您先登录','jumpurl'=>Href('user')));	
	}else{
		$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2"," id=".$cook_uid,"name");
		$rq_age1      = intval($row['mate_age1']);
		$rq_age2      = intval($row['mate_age2']);
		$rq_heigh1    = intval($row['mate_heigh1']);
		$rq_heigh2    = intval($row['mate_heigh2']);
		$rq_pay       = $row['mate_pay'];
		$rq_edu       = $row['mate_edu'];
		$rq_areaid    = $row['mate_areaid'];
		$rq_areatitle = $row['mate_areatitle'];
		$rq_love      = $row['mate_love'];
		$rq_car       = $row['mate_car'];
		$rq_house     = $row['mate_house'];
		//
		$rq_weigh1      = intval($row['mate_weigh1']);
		$rq_weigh2      = intval($row['mate_weigh2']);
		$rq_job         = $row['mate_job'];
		$rq_child       = $row['mate_child'];
		$rq_marrytime   = $row['mate_marrytime'];
		$rq_companykind = $row['mate_companykind'];
		$rq_smoking     = $row['mate_smoking'];
		$rq_drink       = $row['mate_drink'];
		$rq_areaid2     = $row['mate_areaid2'];
		$rq_areatitle2  = $row['mate_areatitle2'];
		if( empty($rq_age1) && empty($rq_age2) && empty($rq_heigh1) && empty($rq_heigh2) && empty($rq_pay) && empty($rq_edu) && empty($rq_areaid) && empty($rq_love) && empty($rq_house) && empty($rq_weigh1) && empty($rq_weigh2) && empty($rq_job) && empty($rq_child) && empty($rq_marrytime) && empty($rq_companykind) && empty($rq_smoking) && empty($rq_drink) && empty($rq_areaid2)  ){
			json_exit(array('flag'=>'nomate','msg'=>'只有自己设置完择偶条件才能使用','jumpurl'=>Href('user')));
		}
	}
	json_exit(array('flag'=>1));
}
require_once ZEAI.'cache/udata.php';
$htmltitle='会员展示_'.$_ZEAI['siteName'];

$seo_area_li=false;
if(!empty($areakey)){
	$seo_area_li=seo_area_out('li',$areakey);
	if($seo_area_li){
		$htmltitle = $seo_area_li['htmltitle'];
		$area_url  = $seo_area_li['url'];
		//$par = parse_url($area_url,PHP_URL_QUERY );获取?后部分
		$par = $area_url;
		parse_str($par,$parARR);
		$m1=$parARR['m1'];
		$m2=$parARR['m2'];
		$m3=$parARR['m3'];
		$t =$parARR['t'];
		$Keywords='<meta name="keywords" content="'.$htmltitle.'">';
		$seo_area_out=seo_area_out();
	}
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $htmltitle;?></title>
<?php echo $Keywords;?>
<link href="<?php echo HOST;?>/rex/www_esyyw_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/rex/www_esyyw_cn.js"></script>
<script src="<?php echo HOST;?>/cache/udata.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
<link href="<?php echo HOST;?>/p1/css/user.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="user S5 fadeInL">
	<h1>搜索意中人</h1>
    <form action="<?php echo HOST;?>/p1/user.php" method="get" name="zeai_cnForm">
    <div class="sobox">
        <div class="so">
            <h4>年龄</h4><ul class="age" id="so_age1"><span></span><li><div class="msk"></div></li></ul>
            <h4 class="l0">～</h4>
            <ul class="age" id="so_age2"><span></span><li><div class="msk"></div></li></ul>
            <h4>性别</h4><ul id="so_sex"><span></span><li><div class="msk"></div></li></ul>
            <h4>地区</h4><ul class="area" id="so_area_"><span></span><li><div class="msk"></div><dl><dd></dd></dl></li></ul>
            <h4>婚况</h4><ul id="so_love"><span></span><li><div class="msk"></div></li></ul>
            <div class="checkbox"><input type="checkbox" name="ifphoto" id="ifphoto" class="checkskin" value="1"<?php echo ($ifphoto == 1)?' checked':'';?>><label for="ifphoto" class="checkskin-label"><i></i><b class="W100 S14 C666">有照片</b></label></div>
            <div class="checkbox"><input type="checkbox" name="ifmatch" id="ifmatch" class="checkskin" value="1"<?php echo ($ifmatch == 1)?' checked':'';?>><label for="ifmatch" class="checkskin-label"><i></i><b class="W100 S14 C666">匹配自己</b></label></div>
        </div>
	</div>
    <div class="sobox">
        <div class="so">
            <h4>身高</h4><ul class="heigh" id="so_heigh1"><span></span><li><div class="msk"></div></li></ul>
            <h4 class="l0">～</h4>
            <ul class="heigh" id="so_heigh2"><span></span><li><div class="msk"></div></li></ul>
            <h4>学历</h4><ul id="so_edu"><span></span><li><div class="msk"></div></li></ul>
            <h4>住房</h4><ul class="house" id="so_house"><span></span><li><div class="msk"></div></li></ul>
            <h4>月薪</h4><ul id="so_pay"><span></span><li><div class="msk"></div></li></ul>
            <h4>职业</h4><ul class="job" id="so_job"><span></span><li><div class="msk"></div></li></ul>
        </div>
    </div>
    <div class="sobox">
        <input type="hidden" name="form_mate_sex" id="sex" value="<?php echo $form_mate_sex;?>">
        <input type="hidden" name="form_mate_age1" id="age1" value="<?php echo $form_mate_age1;?>">
        <input type="hidden" name="form_mate_age2" id="age2" value="<?php echo $form_mate_age2;?>">
        <input type="hidden" name="form_mate_heigh1" id="heigh1" value="<?php echo $form_mate_heigh1;?>">
        <input type="hidden" name="form_mate_heigh2" id="heigh2" value="<?php echo $form_mate_heigh2;?>">
        <input type="hidden" name="m1" id="so_area_area1id" value="<?php echo $m1;?>">
        <input type="hidden" name="m2" id="so_area_area2id" value="<?php echo $m2;?>">
        <input type="hidden" name="m3" id="so_area_area3id" value="<?php echo $m3;?>">
        <input type="hidden" name="areatitle" id="areatitle" value="<?php echo $areatitle;?>">
        <input type="hidden" name="form_mate_job" id="job" value="<?php echo $form_mate_job;?>">
        <input type="hidden" name="form_mate_edu" id="edu" value="<?php echo $form_mate_edu;?>">
        <input type="hidden" name="form_mate_love" id="love" value="<?php echo $form_mate_love;?>">
        <input type="hidden" name="form_mate_house" id="house" value="<?php echo $form_mate_house;?>">
        <input type="hidden" name="form_mate_pay" id="pay" value="<?php echo $form_mate_pay;?>">
        <input type="hidden" name="t" value="1">
        <input type="hidden" name="areakey" id="areakey" value="<?php echo $areakey;?>">
        <button type="submit" class="btn size4 HONG"><i class="ico">&#xe6c4;</i> 开始筛选</button>
	</div>
    </form>
    <div class="sonickname"><form action="<?php echo HOST;?>/p1/user.php" method="get" name="zeai_cnForm"><input name="k" type="text" class="input" maxlength="30" placeholder="按会员昵称/UID"><button type="submit" class="btn size3 HONG" title="开始搜索"><i class="ico">&#xe6c4;</i></button><input type="hidden" name="t" value="2"></form></div>
</div>
<div class="list S5 fadeInR" id="ulist">
	<div class="sokind">
    	<?php
		if ($seo_area_li){echo $seo_area_out;}else{?>
        <a href="<?php echo Href('user');?>"<?php echo ($t != 3)?' class="ed"':'';?>>默认排序</a>
        <a href="<?php echo HOST;?>/p1/user.php?t=3&ifvip=1"<?php echo ($ifvip == 1)?' class="ed"':'';?>>VIP会员</a>
        <a href="javascript:;"<?php echo ($ifcert == 1)?' class="ed"':'';?> id="ifcert">实名会员</a>
        <a href="javascript:;"<?php echo ($ifnear == 1)?' class="ed"':'';?> id="ifnear"><i class="ico">&#xe614;</i> 附近</a>
        <a href="<?php echo HOST;?>/p1/user.php?t=3&ifnew=1"<?php echo ($ifnew == 1)?' class="ed"':'';?>>新会员</a>
        <a href="<?php echo HOST;?>/p1/user.php?t=3&ifclick=1"<?php echo ($ifclick == 1)?' class="ed"':'';?>>人气榜</a>
        <a href="<?php echo HOST;?>/p1/user.php?t=3&ifendtime=1"<?php echo ($ifendtime == 1)?' class="ed"':'';?>>最近登录</a>
        <?php }?>
    </div>
	<div class="clear"></div>
	<?php 
	$SQL="flag=1 AND dataflag=1 AND kind<>4 ";$ORDER="ORDER BY refresh_time DESC,id DESC";$field="id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,pay,love,edu,RZ,heigh,photo_ifshow";
	switch ($t) {
		case 1:
			if ($form_mate_sex == 1)$SQL .= " AND sex=1 ";
			if ($form_mate_sex == 2)$SQL .= " AND sex=2 ";
			if ($ifphoto == 1)$SQL  .= " AND photo_s<>'' AND photo_f=1 ";
			if ($ifmatch == 1 && ifint($cook_uid)){
				$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2"," id=".$cook_uid,"name");
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
				if( empty($mate_age1) && empty($mate_age2) && empty($mate_heigh1) && empty($mate_heigh2) && empty($mate_pay) && empty($mate_edu) && empty($mate_areaid) && empty($mate_love) && empty($mate_house) && empty($mate_weigh1) && empty($mate_weigh2) && empty($mate_job) && empty($mate_child) && empty($mate_marrytime) && empty($mate_companykind) && empty($mate_smoking) && empty($mate_drink) && empty($mate_areaid2)  ){
					alert('只有自己设置完择偶条件才能使用',HOST.'/p1/my_info.php?t=4');
				}
				//生成SQL
				$SQL .= " AND id<>".$cook_uid;
				$SQL .= ($cook_sex == 2)?" AND sex=1 ":" AND sex=2 ";
				$SQL .= mate_diy_SQL();
			}
			if (ifint($form_mate_age1))$SQL .= " AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= '$form_mate_age1' ) ";
			if (ifint($form_mate_age2))$SQL .= " AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <= '$form_mate_age2' ) ";
			if (ifint($form_mate_heigh1))$SQL .= " AND ( heigh >= '$form_mate_heigh1' ) ";
			if (ifint($form_mate_heigh2))$SQL .= " AND ( heigh <= '$form_mate_heigh2' ) ";
			$areaid = '';
			if (ifint($m1) && ifint($m2) && ifint($m3)){
				$areaid = $m1.','.$m2.','.$m3;
			}elseif(ifint($m1) && ifint($m2)){
				$areaid = $m1.','.$m2;
			}elseif(ifint($m1)){
				$areaid = $m1;
			}
			if (!empty($areaid))$SQL .= " AND areaid LIKE '%".$areaid."%' ";//seo
			if (ifint($form_mate_pay))$SQL .= " AND pay='$form_mate_pay' ";
			if (ifint($form_mate_job))$SQL .= " AND job='$form_mate_job' ";
			if (ifint($form_mate_edu))$SQL .= " AND edu='$form_mate_edu' ";
			if (ifint($form_mate_love))$SQL .= " AND love='$form_mate_love' ";
			if (ifint($form_mate_house))$SQL .= " AND house='$form_mate_house' ";
			$RTSQL = "SELECT ".$field." FROM ".__TBL_USER__." b WHERE ".$SQL." ".$ORDER." LIMIT ".$_ZEAI['limit'];
		break;
		case 2:
			if (!empty($k)){
				$k = trimhtml(dataIO($k,'in'));
				if (ifint($k)){
					$SQL .= " AND id=".$k;
				}else{
					$SQL .= " AND nickname LIKE '%".$k."%' ";
				}
			}
			$RTSQL = "SELECT ".$field." FROM ".__TBL_USER__." b WHERE ".$SQL." ".$ORDER." LIMIT ".$_ZEAI['limit'];
		break;
		case 3:
			if ($ifvip == 1)$ORDER  = " ORDER BY grade DESC,refresh_time DESC";
			if ($ifcert == 1){
				if(ifint($cook_uid)){
					$row = $db->NAME("W.W.W.z.e.a.i.C..N","id","RZ='' AND id=".$cook_uid);
					if ($row)alert('只有自己认证了才能看别人的哦',HOST.'/p1/my_cert.php');
				}else{
					alert('请您先登录',HOST.'/p1/login.php');
				}
				$ORDER = " AND RZ<>''";
			}
			if ($ifnew == 1)$ORDER  = " ORDER BY id DESC";
			if ($ifclick == 1)$ORDER  = " ORDER BY click DESC,id DESC";
			if ($ifendtime == 1)$ORDER  = " ORDER BY endtime DESC";
			$RTSQL = "SELECT $field FROM ".__TBL_USER__." b WHERE ".$SQL." ".$ORDER." LIMIT ".$_ZEAI['limit'];
			if ($ifnear == 1 && ifint($cook_uid)){
				$row = $db->NAME($cook_uid,"latitude,longitude","latitude<>'' AND longitude<>'' AND id=".$cook_uid);
				if ($row){
					$data_latitude  = $row['latitude'];
					$data_longitude = $row['longitude'];
					$RTSQL = "SELECT ".$field.",ROUND(6378.138*2*ASIN(SQRT(POW(SIN((".$data_latitude."*PI()/180-latitude*PI()/180)/2),2)+COS(".$data_latitude."*PI()/180)*COS(latitude*PI()/180)*POW(SIN((".$data_longitude."*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance,latitude,longitude FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY distance,refresh_time DESC LIMIT ".$_ZEAI['limit'];;
				}else{
					alert('请您先设置自己的位置',HOST.'/p1/my_info.php');
				}
			}
		break;
		default:
			//if(ifint($cook_uid && !empty($cook_sex))){
			//	$SQL .= ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
			//}
			$RTSQL = "SELECT ".$field." FROM ".__TBL_USER__." b WHERE ".$SQL." AND photo_s<>'' AND photo_f=1 ".$ORDER." LIMIT ".$_ZEAI['limit'];
		break;
	}
	//echo $RTSQL;
	$rt=$db->query($RTSQL);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		$page_skin='4_yuan';$pagemode=4;$pagesize=20;$page_color='#E83191';require_once ZEAI.'sub/page.php';
		//
		$switch = json_decode($_ZEAI['switch'],true);
		$lockstr = '';$ifblur=0;
		if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
			$lockstr = '<i class="ico lockico">&#xe61e;</i><span class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</span>';
			$ifblur=1;
		}
		//
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows)break;
			$uid      = $rows['id'];
			$nickname = dataIO($rows['nickname'],'out');
			$sex      = $rows['sex'];
			$love     = $rows['love'];
			$grade    = $rows['grade'];
			$photo_s  = $rows['photo_s'];
			$photo_f  = $rows['photo_f'];
			$areatitle= $rows['areatitle'];
			$birthday = $rows['birthday'];
			$job      = $rows['job'];
			$pay      = $rows['pay'];
			$edu      = $rows['edu'];
			$RZ       = $rows['RZ'];
			$heigh    = $rows['heigh'];
			$photo_ifshow = $rows['photo_ifshow'];
			$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
			//
			$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
			$job_str      = (empty($job))?'':udata('job',$job).' ';
			$edu_str      = (empty($edu))?'':udata('edu',$edu).' ';
			$pay_str      = (empty($pay))?'':udata('pay',$pay).'/月'.' ';
			$love_str     = (empty($love))?'':udata('love',$love).' ';
			$heigh_str    = ($heigh>140)?$heigh.'cm ':'';

			$aARR = explode(' ',$areatitle);
			if($seo_area_li){
				$areatitle_str = (empty($aARR[2]))?'':$aARR[2];
			}else{
				$areatitle_str = (empty($aARR[1]))?'':$aARR[1];
			}
			$areatitle_str  = str_replace("不限","",$areatitle_str);
			//
			if($ifblur==1){
				$photo_m = 'blur';
			}else{
				$photo_m = 'm';
			}
			//
			if($photo_ifshow==0 && $ifblur==0){
				$lockstr = '';
				$photo_m_url=HOST.'/res/photo_m'.$sex.'_hide.png';
			}else{
				$photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.smb($photo_s,$photo_m):HOST.'/res/photo_m'.$sex.'.png';	
			}
			//
			$sexbg      = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':' class="m"';
			$echo .= '<li>';
			$uhref = Href('u',$uid);
			$echo .= '<a href="'.$uhref.'" class="mbox" target="_blank">';
            $echo .= '<p value="'.$photo_m_url.'"'.$sexbg.'>'.$lockstr.'</p>';
			$echo .= '<em><span>'.$love_str.$edu_str.'</span><span>'.$job_str.'</span><span>'.$pay_str.'</span><span>'.$areatitle.'</span></em>';
			$echo .= '<b>联系Ta</b>';
			$echo .= '</a>';
			$echo .= '<a href="'.$uhref.'" target="_blank"><h4>'.uicon($sex.$grade).$nickname.'</h4></a>';
			if($t==3 && !empty($rows['distance'])){
				$distance = $rows['distance'];
				if ($distance<1000){
					$distance_str  = $distance.'m';
				}else{
					$distance_str  = intval($distance/1000).'km';
				}
				$echo.= '<h5>'.$areatitle.'<span class="FR"><i class="ico">&#xe614;</i> '.$distance_str.'</span></h5>';
			}else{
				$echo .= '<h5>'.$birthday_str.$heigh_str.$job_str.$areatitle_str.'</h5>';
			}
			if (!empty($RZ))$echo.= '<div class="payrz">'.RZ_html($RZ,'s','color').'</div>';
			$echo .= '</li>';
		}
		echo $echo;
	}else{
		echo '<br>'.nodatatips('没有找到符合条件的会员！<br>不要为幸福设置太高的门槛，也不要以貌取人，以财取人，真正的幸福是建立在双方共同的价值观和爱情观的基础上，建立在两个人一起奋斗，互相鼓励，共同创造美好生活的过程。');
	}
	?>
</div>
<?php if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox zeaipagebox">'.$pagelist.'</div>';?>
<div class="clear"></div>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/p1/js/esyyw_birthday.js"></script>
<script src="<?php echo HOST;?>/p1/js/user.js"></script>
<?php
require_once ZEAI.'p1/bottom.php';
ob_end_flush();
?>

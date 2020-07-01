<?php
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$currfields = "photo_s,photo_f,weixin_pic,RZ";
require_once 'my_chkuser.php';
$data_photo_s=$row['photo_s'];
$data_photo_f=$row['photo_f'];
$data_weixin_pic=$row['weixin_pic'];
$data_RZ=$row['RZ'];$RZarr=explode(',',$data_RZ);
require_once ZEAI.'cache/udata.php';$sex_ARR = json_decode($_UDATA['sex'],true);$extifshow = json_decode($_UDATA['extifshow'],true);
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_vip.php';

$up2 = $_ZEAI['up2']."/";
header("Cache-control: private");
$t = (ifint($t,'1-5','1'))?$t:1;
if($submitok == 'ajax_photo_s_up'){
	if (@in_array('photo',$RZarr) && !empty($data_photo_s) && $data_photo_f==1)json_exit(array('flag'=>0,'msg'=>'【'.rz_data_info('photo','title').'】已认证不可更改'));
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_m_');
		if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		if (!ifpic($up2.$dbname))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		json_exit(array('flag'=>1,'msg'=>'上传成功','tmpphoto'=>$dbname));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}elseif($submitok == 'ajax_weixin_pic_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('photo',$file['tmp_name'],$cook_uid.'_wxpic_');
		if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$newpic = $_ZEAI['up2']."/".$dbname;
		if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		$db->query("UPDATE ".__TBL_USER__." SET weixin_pic='$dbname' WHERE id=".$cook_uid);
		@up_send_userdel($data_weixin_pic);
		json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}elseif($submitok == 'modupdate'){
	$setsql = "";$ifnext = true;
	switch ($t) {
		case 1:
			$sex        = intval($sex);$sex=(!ifint($sex))?1:$sex;
			$nickname   = trimhtml(dataIO($nickname,'in',50));
			$areaid     = dataIO($areaid,'in',50);
			$areatitle  = dataIO($areatitle,'in',100);
			$birthday   = (!ifdate($birthday))?'0000-00-00':$birthday;
			$love       = intval($love);
			$heigh      = intval($heigh);
			$edu        = intval($edu);
			$pay        = intval($pay);
			$house      = intval($house);
			$car        = intval($car);
			$job        = intval($job);
			$marrytime  = intval($marrytime);
			$tag        = (is_array($tag))?implode(",",$tag):'';
			$setsql .= ",sex='$sex',marrytime='$marrytime',nickname='$nickname',birthday='$birthday',areaid='$areaid',areatitle='$areatitle',love='$love',heigh='$heigh',edu='$edu',pay='$pay',job='$job',house='$house',car='$car',tag='$tag'";	
		break;
		case 2:
			$aboutus = dataIO($aboutus,'in',1000);
			$setsql .= ",aboutus='$aboutus'";	
		break;
		case 3:
			$marrytype  = intval($marrytype);
			$child      = intval($child);
			$blood      = intval($blood);
			$weigh      = intval($weigh);
			$area2id    = dataIO($area2id,'in',50);
			$area2title = dataIO($area2title,'in',100);
			$nation     = intval($nation);
			$setsql .= ",marrytype='$marrytype',child='$child',blood='$blood',weigh='$weigh',area2id='$area2id',area2title='$area2title',nation='$nation'";	
			$sql = array();
			foreach ($extifshow as $V) {
				$fieldname = $V['f'];
				switch ($V['s']) {
					case 1://text
						$sql[] = "$fieldname='".dataIO($$fieldname,'in')."'";
					break;
					case 2://select
						$sql[] = "$fieldname=".intval($$fieldname)."";
					break;
					case 3://checkbox
						$fieldvalue = (is_array($$fieldname))?implode(',',$$fieldname):'';
						$sql[] = "$fieldname = '".$fieldvalue."'";
					break;
				}
			}
			$sqlExt  = (is_array($sql))?','.implode(',',$sql):'';
			$setsql .= $sqlExt;	
		break;
		case 4:
			$mate_age1      = intval($mate_age1);
			$mate_age2      = intval($mate_age2);
			$mate_heigh1    = intval($mate_heigh1);
			$mate_heigh2    = intval($mate_heigh2);
			$mate_weigh1    = intval($mate_weigh1);
			$mate_weigh2    = intval($mate_weigh2);
			$mate_areaid    = dataIO($mate_areaid,'in',50);
			$mate_areatitle = dataIO($mate_areatitle,'in',100);
			$mate_areaid2    = dataIO($mate_areaid2,'in',50);
			$mate_areatitle2 = dataIO($mate_areatitle2,'in',100);
			$mate_pay       = dataIO((is_array($mate_pay))?implode(",",$mate_pay):$mate_pay,'in',50);
			$mate_edu       = dataIO((is_array($mate_edu))?implode(",",$mate_edu):$mate_edu,'in',50);
			$mate_love      = dataIO((is_array($mate_love))?implode(",",$mate_love):$mate_love,'in',50);
			$mate_house     = dataIO((is_array($mate_house))?implode(",",$mate_house):$mate_house,'in',50);
			$mate_car     = dataIO((is_array($mate_car))?implode(",",$mate_car):$mate_car,'in',50);
			$mate_child     = dataIO((is_array($mate_child))?implode(",",$mate_child):$mate_child,'in',50);
			$mate_marrytime = dataIO((is_array($mate_marrytime))?implode(",",$mate_marrytime):$mate_marrytime,'in',50);
			$mate_companykind = dataIO((is_array($mate_companykind))?implode(",",$mate_companykind):$mate_companykind,'in',50);
			$mate_smoking     = dataIO((is_array($mate_smoking))?implode(",",$mate_smoking):$mate_smoking,'in',50);
			$mate_drink       = dataIO((is_array($mate_drink))?implode(",",$mate_drink):$mate_drink,'in',50);
			$mate_job         = dataIO((is_array($mate_job))?implode(",",$mate_job):$mate_job,'in',50);
			$mate_areaid = str_replace(",,,","",$mate_areaid);
			$mate_areaid = str_replace(",,","",$mate_areaid);
			$mate_areaid2 = str_replace(",,,","",$mate_areaid2);
			$mate_areaid2 = str_replace(",,","",$mate_areaid2);
			$setsql .= ",mate_age1=$mate_age1,mate_age2=$mate_age2,mate_heigh1=$mate_heigh1,mate_heigh2=$mate_heigh2,mate_weigh1=$mate_weigh1,mate_weigh2=$mate_weigh2,mate_pay='$mate_pay',mate_edu='$mate_edu',mate_areaid='$mate_areaid',mate_areatitle='$mate_areatitle',mate_areaid2='$mate_areaid2',mate_areatitle2='$mate_areatitle2',mate_love='$mate_love',mate_house='$mate_house',mate_car='$mate_car',mate_child='$mate_child',mate_marrytime='$mate_marrytime',mate_companykind='$mate_companykind',mate_smoking='$mate_smoking',mate_drink='$mate_drink',mate_job='$mate_job'";	
		break;
		case 5:
			$address = dataIO($address,'in',100);
			$qq      = dataIO($qq,'in',15);
			$weixin  = dataIO($weixin,'in',50);
			$email   = dataIO($email,'in',50);
			$mob     =(!ifmob($mob))?0:$mob;
			if ($mob != $mob_old){
				$row = $db->ROW(__TBL_USER__,'nickname',"mob='$mob' AND mob<>'' AND FIND_IN_SET('mob',RZ)");
				if($row){json_exit(array('flag'=>1,'msg'=>'“手机”已被【'.dataIO($row[0],'out').'】占用'));}else{$setsql .= ",mob='$mob'";}
			}
			if ($email != $email_old){
				$row = $db->ROW(__TBL_USER__,'nickname',"email='$email' AND email<>'' AND FIND_IN_SET('email',RZ)");
				if($row){json_exit(array('flag'=>1,'msg'=>'“邮箱”已被【'.dataIO($row[0],'out').'】占用'));}else{$setsql .= ",email='$email'";}
			}
			if ($qq != $qq_old){
				$row = $db->ROW(__TBL_USER__,'nickname',"qq='$qq' AND qq<>'' AND FIND_IN_SET('qq',RZ)");
				if($row){json_exit(array('flag'=>1,'msg'=>'“QQ”已被【'.dataIO($row[0],'out').'】占用'));}else{$setsql .= ",qq='$qq'";}
			}
			$setsql .= ",address='$address',weixin='$weixin'";	
		break;
		default:exit;break;
	}
	//if (!$ifnext){alert($varmsg,"-1");exit;}
	//
	$switch = json_decode($_ZEAI['switch'],true);
	$shFlag = $switch['sh']['moddata_'.$cook_grade];
	$dataflag = ($shFlag == 1)?1:0;
	//
	$db->query("UPDATE ".__TBL_USER__." SET dataflag=".$dataflag.$setsql." WHERE id=".$cook_uid);
	if ($t == 1){
		setcookie("cook_nickname",$nickname,null,"/",$_ZEAI['CookDomain']);
		setcookie("cook_sex",$sex,null,"/",$_ZEAI['CookDomain']);
		setcookie("cook_birthday",$birthday,null,"/",$_ZEAI['CookDomain']);
	}
	json_exit(array('flag'=>1,'msg'=>'修改成功','jumpurl'=>$jumpurl));
	//callmsg("修改成功！","$SELF?t=$tt");
}else{
	$t = (ifint($t,'1-6','1'))?$t:1;
	$currfields = "myinfobfb,dataflag";
	switch ($t) {
		case 1:
			$mini_title  = '基本资料';
			$currfields .= ",uname,truename,nickname,sex,grade,photo_s,photo_f,RZ,photo_s,birthday,areaid,areatitle,love,heigh,edu,pay,house,car,tag,job,marrytime";
		break;
		case 2:
			$mini_title  = '个人独白';
			$currfields .= ",aboutus";
		break;
		case 3:
			$mini_title  = '详细资料';
			$currfields .= ",weigh,nation,area2id,area2title,child,blood,marrytype";
			if (@count($extifshow) == 0 || !is_array($extifshow))alert('暂无详细资料！','back');
			foreach ($extifshow as $ev){$evARR[] = $ev['f'];}
			$currfields .= ",".implode(",",$evARR);
		break;
		case 4:
			$mini_title  = '择偶要求';
			$currfields .= ",mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2";
			set_data_ed_bfb($cook_uid);
		break;
		case 5:
			$mini_title  = '联系方式';
			$currfields .= ",mob,address,weixin,weixin_pic,qq,email,RZ";
		break;
	}
	set_data_ed_bfb($cook_uid);
	$row = $db->NAME($cook_uid,$currfields);
	$myinfobfb = intval($row['myinfobfb']);
	$dataflag  = intval($row['dataflag']);
	switch ($t) {
		case 1:
			$photo_s = $row['photo_s'];
			$uname   = dataIO($row['uname'],'out');
			$nickname   = dataIO($row['nickname'],'out');
			$sex        = $row['sex'];
			$birthday   = $row['birthday'];
			$birthday   = (!ifdate($birthday))?'':$birthday;
			$areaid     = dataIO($row['areaid'],'out');
			$areatitle  = dataIO($row['areatitle'],'out');
			$heigh      = $row['heigh'];
			$love       = $row['love'];
			$edu        = $row['edu'];
			$pay        = $row['pay'];
			$house      = $row['house'];
			$car        = $row['car'];
			$marrytime  = $row['marrytime'];
			$job        = $row['job'];
			$tag        = $row['tag'];
			//
			$birthday_  = ($birthday == '0000-00-00')?(YmdHis($ADDTIME,'Y')-25).'-01'.'-15':$birthday;
			$areaid     = explode(',',$areaid);
			$a1 = $areaid[0];$a2 = $areaid[1];$a3 = $areaid[2];$a4 = $areaid[3];
			if(!empty($photo_s)){
				$photo_s_url = $up2.$photo_s;
				$photo_b_url = getpath_smb($photo_s_url,'b');
				//$photo_s_str = '<img class="photo_s" src="'.$photo_s_url.'">';
				$photo_s_str = '<div class="photo_s" style=\'background-image:url("'.$photo_s_url.'")\'><span>更换头像</span></div>';
			}else{
				$photo_s_str = '<p class="icoadd" title="上传头像"><i class="ico">&#xe620;</i></p>';
			}
		break;
		case 2:
			$aboutus = dataIO($row['aboutus'],'wx');
		break;
		case 3:
			$row2 = $row;
			$nation     = $row['nation'];
			$marrytype  = $row['marrytype'];
			$child      = $row['child'];
			$blood      = $row['blood'];
			$weigh      = $row['weigh'];
			$area2id    = dataIO($row['area2id'],'out');
			$area2title = dataIO($row['area2title'],'out');
			$area2id    = explode(',',$area2id);
			$a11 = $area2id[0];$a22 = $area2id[1];$a33 = $area2id[2];$a44 = $area2id[3];
		break;
		case 4:
			$mate_age1      = intval($row['mate_age1']);
			$mate_age2      = intval($row['mate_age2']);
			$mate_heigh1    = intval($row['mate_heigh1']);
			$mate_heigh2    = intval($row['mate_heigh2']);
			$mate_weigh1    = intval($row['mate_weigh1']);
			$mate_weigh2    = intval($row['mate_weigh2']);
			$mate_pay       = $row['mate_pay'];
			$mate_edu       = $row['mate_edu'];
			$mate_areaid    = $row['mate_areaid'];
			$mate_areatitle = $row['mate_areatitle'];
			$mate_love      = $row['mate_love'];
			$mate_car       = $row['mate_car'];
			$mate_house     = $row['mate_house'];
			$mate_weigh1      = intval($row['mate_weigh1']);
			$mate_weigh2      = intval($row['mate_weigh2']);
			$mate_job         = $row['mate_job'];
			$mate_child       = $row['mate_child'];
			$mate_marrytime   = $row['mate_marrytime'];
			$mate_companykind = $row['mate_companykind'];
			$mate_smoking     = $row['mate_smoking'];
			$mate_drink       = $row['mate_drink'];
			$mate_areaid2     = $row['mate_areaid2'];
			$mate_areatitle2  = $row['mate_areatitle2'];
			$mate_areaid    = explode(',',$mate_areaid);
			$mate_areaid2   = explode(',',$mate_areaid2);
			$m1 = $mate_areaid[0];$m2 = $mate_areaid[1];$m3 = $mate_areaid[2];$m4 = $mate_areaid[3];
			$h1 = $mate_areaid2[0];$h2 = $mate_areaid2[1];$h3 = $mate_areaid2[2];$h4 = $mate_areaid2[3];
		break;
		case 5:
			$address    = dataIO($row['address'],'out');
			$weixin     = dataIO($row['weixin'],'out');
			$qq         = dataIO($row['qq'],'out');
			$email      = dataIO($row['email'],'out');
			$mob        = dataIO($row['mob'],'out');
			$weixin_pic = $row['weixin_pic'];
			$RZ = explode(',',$row['RZ']);
			if(!empty($weixin_pic)){
				$weixin_pic_url = $up2.$weixin_pic;
				$weixin_pic_str = '<div class="photo_s" style=\'border-radius:0;background-image:url("'.$weixin_pic_url.'")\'><span>更换二维码</span></div>';
			}else{
				$weixin_pic_str = '<p class="icoadd" title="上传微信二维码"><i class="ico">&#xe620;</i></p>';
			}
		break;
	}
}
switch ($dataflag){
	case 0:$dataflag_str = '审核中';$datafloag_sty='flag0';break;
	case 1:$dataflag_str = '审核通过';$datafloag_sty='flag1';break;
	case 2:$dataflag_str = '审核未通过，请修改后重新提交';$datafloag_sty='flag2';break;
}
$zeai_cn_menu = 'my_info';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>个人资料 - <?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/p1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/my.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/my_info.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/p1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/date_input.js"></script>
<?php if($t == 4){$mate_diy = explode(',',$_ZEAI['mate_diy']);?>
<script>
var nulltext = '不限';
var mate_areaid_ARR1 = areaARR1,
mate_areaid_ARR2 = areaARR2,
mate_areaid_ARR3 = areaARR3,
mate_areaid_ARR4 = areaARR4;
var mate_areaid2_ARR1 = areaARRhj1,
mate_areaid2_ARR2 = areaARRhj2,
mate_areaid2_ARR3 = areaARRhj3,
mate_areaid2_ARR4 = areaARRhj4;
var
mate_age_ARR  = age_ARR,
mate_age_ARR1 = age_ARR,
mate_age_ARR2 = age_ARR,
mate_heigh_ARR  = heigh_ARR,
mate_heigh_ARR1 = heigh_ARR,
mate_heigh_ARR2 = heigh_ARR,
mate_weigh_ARR  = weigh_ARR,
mate_weigh_ARR1 = weigh_ARR,
mate_weigh_ARR2 = weigh_ARR,
mate_pay_ARR = pay_ARR,
mate_edu_ARR = edu_ARR,
mate_love_ARR = love_ARR,
mate_job_ARR = job_ARR,
mate_house_ARR = house_ARR,
mate_child_ARR = child_ARR,
mate_marrytime_ARR = marrytime_ARR,
mate_companykind_ARR = companykind_ARR,
mate_smoking_ARR = smoking_ARR,
mate_drink_ARR = drink_ARR,
mate_car_ARR = car_ARR;
var ifage=false,ifheigh=false,ifweigh=false;
<?php if (in_array('age',$mate_diy)){?>ifage=true;<?php }?>
<?php if (in_array('heigh',$mate_diy)){?>ifheigh=true;<?php }?>
<?php if (in_array('weigh',$mate_diy)){?>ifweigh=true;<?php }?>
</script>
<?php }?>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main">
	<div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div>
	<div class="mainR">
        <div class="zeai_my_box myRM">
        	<h1>个人资料<font class="<?php echo $datafloag_sty;?>"><?php echo $dataflag_str;?></font></h1>
            <div class="tab">
            	<a href="<?php echo SELF;?>?t=1"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>>基本资料</a>
            	<a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>个人独白</a>
            	<a href="<?php echo SELF;?>?t=3"<?php echo ($t==3)?' class="ed"':'';?>>详细资料</a>
            	<a href="<?php echo SELF;?>?t=4"<?php echo ($t==4)?' class="ed"':'';?>>择偶条件</a>
            	<a href="<?php echo SELF;?>?t=5"<?php echo ($t==5)?' class="ed"':'';?>>联系方式</a>
            </div>
            <div id="my_info_bfb" class="my_info_bfb<?php if ($myinfobfb >= 90){echo ' my_info_bfb_nb';}?>">
                <i id="my_info_bfbbar"></i><span>当前资料完整度<?php echo $myinfobfb;?>％</span>
            </div> 
            <form id="ZeaiCnForm">
             <!-- start C -->
            <div class="myRC">
                <!-- 基本资料 -->
                <?php if ($t == 1){ ?>
                <dl class="picadd"><dt><font class="Cf00 S16">*</font> 头像照片</dt><dd>
                    <div id="photo_s" class="W100"><?php echo $photo_s_str; ?></div>
                    <?php if ($data_photo_f == 0 && !empty($data_photo_s)){?>　<font class="blue">头像审核中</font><?php }?>
                </dd></dl>
                <dl><dt>地图坐标</dt><dd>
                    <div class="mapbtnbox">
                        <a href="javaacript:;" id="Zeai_map_btn"><i class="ico" title="看看在您附近的都有哪些朋友。">&#xe614;</i><span>标注位置</span></a>
                        <span class="tips">设置成功后，可以查看您附近的人</span>
                    </div>
                </dd></dl>        
                <dl><dt><font class="Cf00 S16">*</font> 性　　别</dt><dd>
                    <?php if (empty($sex)){ ?>
                    <script>zeai_cn__CreateFormItem('select','sex','<?php echo $sex; ?>');</script><span class="tips">只有一次修改机会哦</span>
                    <?php }else{echo udata('sex',$sex)."<input name='sex' id='sex' type='hidden' value='".$sex."'>";}?>
                </dd></dl>        
                <dl><dt><font class="Cf00 S16">*</font> 昵　　称</dt><dd>
                    <?php if (empty($nickname)){ ?>
                    <input name="nickname" type="text" class="input W150" id="nickname" value="<?php echo $nickname; ?>" maxlength="20"><span class="tips">只有一次修改机会哦</span>
                    <?php }else{echo $nickname."<input name='nickname' id='nickname' type='hidden' value='".$nickname."'>";}?>
                </dd></dl>
                <dl><dt><font class="Cf00 S16">*</font> 生　　日</dt><dd>
                    <?php if (empty($birthday) || $birthday == '0000-00-00'){ ?>
                    <input name="birthday" id="birthday" type="text" readonly class="input W150" value="<?php echo $birthday; ?>" size="10" maxlength="10" onfocus="this.select()" onClick="fPopCalendar(event,this,this)"><span class="tips">只有一次修改机会哦</span>
                     <?php }else{
                         echo $birthday."<input name='birthday' id='birthday' type='hidden' value='".$birthday."'>";
                         if ($H2 == 2)echo " <span class='S12 C090'>（已认证）</span>";
                    }?>
                </dd></dl>
                <dl><dt><font class="Cf00 S16">*</font> 婚姻状况</dt><dd>
                    <?php if (empty($love)){ ?>
                    <script>zeai_cn__CreateFormItem('select','love','<?php echo $love; ?>');</script><span class="tips">只有一次修改机会哦</span>
                    <?php }else{echo udata('love',$love)."<input name='love' id='love' type='hidden' value='".$love."'>";
						if(in_array('love',$RZarr))echo "　<span class='S12 C090'>（已认证）</span>";
					}?>
                </dd></dl>
                <dl><dt><font class="Cf00 S16">*</font> 学　　历</dt><dd>
                <?php if (!in_array('edu',$RZarr)){ ?>
                <script>zeai_cn__CreateFormItem('radio','edu','<?php echo $edu; ?>');</script>
                <?php }else{echo udata('edu',$edu)."　<span class='S12 C090'>（已认证）</span>　<input name='edu' id='edu' type='hidden' value='".$edu."'>";}?>
                </dd></dl>
                <!--<hr>-->
                <dl><dt><font class="Cf00 S16">*</font> 月 收 入</dt><dd>
                    <?php if (!in_array('pay',$RZarr)){ ?>
                    <script>zeai_cn__CreateFormItem('radio','pay','<?php echo $pay; ?>');</script>
                    <?php }else{echo udata('pay',$pay)."　<span class='S12 C090'>（已认证）</span>　<a href='javascript:;' class='mod' id='mod5'></a><input name='pay' id='pay' type='hidden' value='".$pay."'>";}?>
                </dd></dl>
                <dl><dt><font class="Cf00 S16">*</font> 身　　高</dt><dd><script>zeai_cn__CreateFormItem('select','heigh','<?php echo $heigh; ?>');</script></dd></dl>
                <dl><dt><font class="Cf00 S16">*</font> 职　　业</dt><dd><script>zeai_cn__CreateFormItem('select','job','<?php echo $job; ?>');</script></dd></dl>
                <dl><dt><font class="Cf00 S16">*</font> 工作地区</dt><dd><script>LevelMenu4('a1|a2|a3|a4|请选择|<?php echo $a1; ?>|<?php echo $a2; ?>|<?php echo $a3; ?>|<?php echo $a4; ?>|areaid|areatitle|<?php echo $areatitle;?>',' class="SW SW_area"');</script></dd></dl>
                
                
                
                <dl><dt>住房情况</dt><dd>
					<?php if (!in_array('house',$RZarr)){ ?>
                    <script>zeai_cn__CreateFormItem('radio','house','<?php echo $house; ?>');</script>
                    <?php }else{echo udata('house',$house)."　<span class='S12 C090'>（已认证）</span>　<input name='house' id='house' type='hidden' value='".$house."'>";}?>
                </dd></dl>
                <dl><dt>买车情况</dt><dd>
					<?php if (!in_array('car',$RZarr)){ ?>
                    <script>zeai_cn__CreateFormItem('radio','car','<?php echo $car; ?>');</script>
                    <?php }else{echo udata('car',$car)."　<span class='S12 C090'>（已认证）</span>　<input name='car' id='house' type='hidden' value='".$car."'>";}?>
                </dd></dl>
                
                <dl><dt>期望结婚时间</dt><dd><script>zeai_cn__CreateFormItem('radio','marrytime','<?php echo $marrytime; ?>');</script></dd></dl>
                <!--<hr>-->
                <dl><dt>我的标签</dt><dd><script>zeai_cn__CreateFormItem('checkbox','tag','<?php echo $tag; ?>','',eval('tag<?php echo $sex; ?>_ARR'));</script></dd></dl>
                <input type="button" value="修改并保存" class="btn size4 HONG" style="margin:20px 0 10px 0" onclick="chkform(1)">
                <div class="C8d">温馨提示：完善所有资料，系统将奖励<?php echo $_ZEAI['loveB']; ?></div>
                <input name="t" type="hidden" value="<?php echo $t; ?>" />
                <input name="submitok" type="hidden" value="modupdate" />
                <input name="jumpurl" id="jumpurl" type="hidden" value="<?php echo $jumpurl; ?>" />
                <!-- 个人独白 -->
                <?php }elseif ($t == 2){ ?>
                    <br><br>
                    <dl><dt style="margin-left:40px">个人独白</dt><dd><textarea style="resize:vertical" name="aboutus" class="textarea" id="aboutus" placeholder="简短个人介绍，三观等（全部人工审核，切勿填写联系方式 否则资料无法通过。）"><?php echo $aboutus; ?></textarea></dd></dl>
                    <input name="submitok" type="hidden" value="modupdate" />
                    <input name="t" type="hidden" value="<?php echo $t; ?>" />
                    <button type="button" class="btn size4 HONG" style="margin:20px 0 10px 0" onclick="chkform(2)">修改并保存</button> 
                <!-- 详细资料 -->
                <?php }elseif ($t == 3){ ?>
                    <dl><dt>嫁娶形式</dt><dd><script>zeai_cn__CreateFormItem('radio','marrytype','<?php echo $marrytype; ?>');</script></dd></dl>
                    <dl><dt>有无小孩</dt><dd><script>zeai_cn__CreateFormItem('radio','child','<?php echo $child; ?>');</script></dd></dl>
                    <dl><dt>体　　重</dt><dd><script>zeai_cn__CreateFormItem('select','weigh','<?php echo $weigh; ?>');</script></dd></dl>
                    <dl><dt>血　　型</dt><dd><script>zeai_cn__CreateFormItem('radio','blood','<?php echo $blood; ?>');</script></dd></dl>
                    <dl><dt>户籍地区</dt><dd><script>LevelMenu4('a11|a22|a33|a44|请选择|<?php echo $a11; ?>|<?php echo $a22; ?>|<?php echo $a33; ?>|<?php echo $a44; ?>|area2id|area2title|<?php echo $area2title;?>',' class="SW SW_area"','hj');</script></dd></dl>
                    <dl><dt>民　　族</dt><dd><script>zeai_cn__CreateFormItem('select','nation','<?php echo $nation; ?>');</script></dd></dl>
                    <?php
                    if (@count($extifshow) >= 1 && is_array($extifshow)){
                        foreach ($extifshow as $V) {
                            $fcls = '';
                            switch ($V['s']) {
                                case 1:$Fkind = 'text';$fcls=' class="input W300"';break;
                                case 2:$Fkind = 'select';break;
                                case 3:$Fkind = 'checkbox';break;
                                case 4:$Fkind = 'range';break;
                            }
                            $F = $V['f'];
                            ?>
                            <dl><dt><?php echo $V['t'];?></dt><dd><script>zeai_cn__CreateFormItem('<?php echo $Fkind;?>','<?php echo $F;?>','<?php echo dataIO($row2[$F],'out'); ?>','<?php echo $fcls;?>');</script></dd></dl>
                        <?php
                        }
                    }
                    ?>
                    <tr><td colspan="2" class="center">
                    <input name="submitok" type="hidden" value="modupdate" />
                    <input name="t" type="hidden" value="<?php echo $t; ?>" />
                    <button type="button" class="btn size4 HONG" style="margin:20px 0 10px 0" onclick="chkform(3)">修改并保存</button> 
                <!-- 择偶要求 -->
                <?php }elseif ($t == 4){?><br>
                    <?php
                    if (count($mate_diy) >= 1 && is_array($mate_diy)){
                        foreach ($mate_diy as $k=>$V) {
                            $cook_tmp1 = 'mate_'.$V;
                            //$cook_tmp2 = 'mate_'.$V.'_str';
                            $cook_mate_data = $$cook_tmp1;
                            //$cook_mate_str  = $$cook_tmp2;
                            $ext = mate_diy_par($V,'ext');
                            ?>
                            <dl><dt><?php echo mate_diy_par($V);?></dt><dd>
                            <?php 
                            switch ($ext) {
                                case 'radio':?><script>zeai_cn__CreateFormItem('select','<?php echo $cook_tmp1;?>','<?php echo $cook_mate_data; ?>','',<?php echo $cook_tmp1.'_ARR';?>);</script><?php ;break;
                                case 'checkbox':?><script>zeai_cn__CreateFormItem('checkbox','<?php echo $cook_tmp1;?>','<?php echo $cook_mate_data; ?>','',<?php echo $cook_tmp1.'_ARR';?>);</script><?php break;
                                case 'radiorange':
                                    $cook_tmp1_1 = 'mate_'.$V.'1';
                                    $cook_mate_data_1 = $$cook_tmp1_1;
                                    $cook_tmp1_2 = 'mate_'.$V.'2';
                                    $cook_mate_data_2 = $$cook_tmp1_2;
                                    ?>
                                    <script>zeai_cn__CreateFormItem('select','<?php echo $cook_tmp1_1;?>','<?php echo $cook_mate_data_1; ?>','',<?php echo $cook_tmp1.'_ARR';?>);</script>
                                     ～ 
                                    <script>zeai_cn__CreateFormItem('select','<?php echo $cook_tmp1_2;?>','<?php echo $cook_mate_data_2; ?>','',<?php echo $cook_tmp1.'_ARR';?>);</script>
                                    <?php break;
                                case 'area':
                                    if($V=='areaid2'){
                                        $idlist='h1|h2|h3|h4';	
                                        $deflist= $h1.'|'.$h2.'|'.$h3.'|'.$h4;
                                        $iputhienT = 'mate_areatitle2';
                                        $hj='hj';
                                    }else{
                                        $idlist='m1|m2|m3|m4';
                                        $deflist= $m1.'|'.$m2.'|'.$m3.'|'.$m4;
                                        $iputhienT = 'mate_areatitle';
                                        $hj='';
                                    }?>
									<script>LevelMenu4('<?php echo $idlist;?>|'+nulltext+'|<?php echo $deflist;?>|<?php echo $cook_tmp1;?>|<?php echo $iputhienT;?>|<?php echo $$iputhienT;?>',' class="SW SW_area"','<?php echo $hj;?>');</script><?php break;
                            }
                            ?>
                            </dd></dl>
                    <?php }}?>
                    <input name="submitok" type="hidden" value="modupdate" />
                    <input name="t" type="hidden" value="<?php echo $t; ?>" />
                    <input name="jumpurl" type="hidden" value="<?php echo $jumpurl; ?>" />
                    <button type="button" class="btn size4 HONG" style="margin:20px 0 10px 0" onclick="chkform(4)">修改并保存</button> 
        
                <!-- 联系方式 -->
                <?php }elseif ($t == 5){ ?>
                    <style>
					.myRC dl{margin:30px auto}
					.myRC dl .ewmysbox{position:relative}
					.myRC dl.picadd .ewmys{position:absolute;left:300px;top:30px}
                    </style>
                    <dl><dt><font class="Cf00 S16">*</font> 手　机</dt><dd><input name="mob" type="text" class="input W300" id="mob" value="<?php echo $mob; ?>" maxlength="11"<?php echo (in_array('mob',$RZ) && ifmob($mob))?' readonly':'';?>><span class="tips">可隐藏，<a href="my_set.php?t=2" class="a09f">隐私设置</a></span><?php echo (in_array('mob',$RZ) && ifmob($mob))?'　　<font class="C090"><i class="ico">&#xe60d;</i> 已认证</font>':'';?></dd></dl>
                    
                    <dl><dt><font class="Cf00 S16">*</font> 微信号</dt><dd><input name="weixin" type="text" class="input W300" id="weixin" value="<?php echo $weixin; ?>" maxlength="30"<?php echo (in_array('weixin',$RZ) && !empty($weixin))?' readonly':'';?>><?php if ($_VIP['hideprivacy'] == 1){?><span class="tips">可隐藏，<a href="my_set.php?t=2" class="a09f">隐私设置</a></span><?php }?></dd></dl>
                    
                    <dl class="picadd"><dt>微信二维码</dt><dd class="ewmysbox">
                        <div id="ewmys" class="W100"><?php echo $weixin_pic_str; ?></div>
                        <?php if ($_VIP['hideprivacy'] == 1){?><span class="tips ewmys">可隐藏，<a href="my_set.php?t=2" class="a09f">隐私设置</a></span><?php }?>
                    </dd></dl>
                    
                    <dl><dt>QQ</dt><dd><input name="qq" type="text" class="input W300" id="qq" value="<?php echo $qq; ?>" maxlength="12"<?php echo (in_array('qq',$RZ) && !empty($qq))?' readonly':'';?>><?php if ($_VIP['hideprivacy'] == 1){?><span class="tips">可隐藏，<a href="my_set.php?t=2" class="a09f">隐私设置</a></span><?php }?><?php echo (in_array('qq',$RZ) && !empty($qq))?'　　<font class="C090"><i class="ico">&#xe60d;</i> 已认证</font>':'';?></dd></dl>
                    <dl><dt>邮　箱</dt><dd><input name="email" type="text" class="input W300" id="email" value="<?php echo $email; ?>" maxlength="50"<?php echo (in_array('email',$RZ) && !empty($email))?' readonly':'';?>><?php if ($_VIP['hideprivacy'] == 1){?><span class="tips">可隐藏，<a href="my_set.php?t=2" class="a09f">隐私设置</a></span><?php }?><?php echo (in_array('email',$RZ) && !empty($email))?'　　<font class="C090"><i class="ico">&#xe60d;</i> 已认证</font>':'';?></dd></dl>
                    <dl><dt>地　址</dt><dd><input name="address" type="text" class="input W300" id="address" value="<?php echo $address; ?>" maxlength="50"><span class="tips">保密，身份验证之用</span></dd></dl>
                    <input name="submitok" type="hidden" value="modupdate" />
                    <input name="t" type="hidden" value="<?php echo $t; ?>" />
                    <input name="qq_old" type="hidden" value="<?php echo $mob; ?>" />
                    <input name="mob_old" type="hidden" value="<?php echo $mob; ?>" />
                    <input name="email_old" type="hidden" value="<?php echo $email; ?>" />
                    <input name="jumpurl" type="hidden" value="<?php echo $jumpurl; ?>" />
                    <button type="button" class="btn size4 HONG" style="margin:20px 0 10px 0" onclick="chkform(5)">修改并保存</button> 
                <?php }?>
            </div>
            <!-- end C -->
            </form>
        
        </div>
    </div>
</div>
<script>var uid=<?php echo $cook_uid;?>,zeaimap,data_myinfobfb=<?php echo $myinfobfb;?>,upMaxMB = <?php echo $_UP['upMaxMB']; ?>,up2='<?php echo $up2;?>';</script>
<script src="js/my_info.js"></script>
<?php require_once ZEAI.'p1/bottom.php';?>
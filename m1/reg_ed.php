<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
if (!is_mobile())exit('请用手机打开');
header("Cache-control: private");
require_once ZEAI.'sub/conn.php';

$loginurl = HOST.'/m1/login.php';
if(ifint($cook_uid) && str_len($cook_pwd)>15){
	$row = $db->ROW(__TBL_USER__,"birthday,love","id=".$cook_uid." AND pwd='".$cook_pwd."'  ",'name');
	if($row){
		//exit('测试新版，请稍后再来1');
		if( $row['birthday']=='0000-00-00' || !ifint($row['love']) ){
			//exit('测试新版，请稍后再来4');
			header("Location: reg_alone.php");
		}
	}else{
		//exit('测试新版，请稍后再来2');
		ZEclearcookAI_CN();
		header("Location: ".$loginurl);
	}
}else{
	//exit('测试新版，请稍后再来3');
	ZEclearcookAI_CN();
	header("Location: ".$loginurl);
}

require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';

if($submitok == 'ajax_photo_s_up_h5' || $submitok == 'ajax_photo_s_up_wx'){
	$data_photo_s='';
	$row = $db->ROW(__TBL_USER__,"photo_s","id=".$cook_uid);
	if ($row){$data_photo_s=$row[0];}
}
$switch=json_decode($_ZEAI['switch'],true);
$switchdataflag = ($switch['sh']['moddata_'.$data_grade] == 1)?1:0;
switch ($submitok) {
	case 'ajax_chkdata':
		$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areaid,mate_love,mate_house,aboutus,photo_s,nickname,car,house,subscribe,openid","id=".$cook_uid,"name");
		if ($row){
			$mate_age1      = intval($row['mate_age1']);
			$mate_age2      = intval($row['mate_age2']);
			$mate_heigh1    = intval($row['mate_heigh1']);
			$mate_heigh2    = intval($row['mate_heigh2']);
			$mate_pay       = intval($row['mate_pay']);
			$mate_edu       = intval($row['mate_edu']);
			$mate_areaid    = $row['mate_areaid'];
			$mate_love      = intval($row['mate_love']);
			$mate_house     = intval($row['mate_house']);
			$photo_s        = $row['photo_s'];
			$aboutus        = dataIO($row['aboutus'],'wx');
			$nickname       = dataIO($row['nickname'],'out');
			$car   = intval($row['car']);
			$house = intval($row['house']);
			$subscribe = $row['subscribe'];
			$openid    = $row['openid'];
		}
		if(empty($photo_s) )json_exit(array('flag'=>0,'msg'=>'请上传头像照片','obj'=>'photo_s'));
		if(str_len($nickname) < 2 || str_len($nickname)>40)json_exit(array('flag'=>0,'msg'=>'请输入网名/昵称(2-20字)','obj'=>'nickname'));
		if(!ifint($house))json_exit(array('flag'=>0,'msg'=>'请选择住房情况','obj'=>'house'));
		if(!ifint($car))json_exit(array('flag'=>0,'msg'=>'请选择购车情况','obj'=>'car'));
		if(!ifint($mate_age1) || !ifint($mate_age2) || !ifint($mate_heigh1) || !ifint($mate_heigh2) || !ifint($mate_pay) || !ifint($mate_edu) || empty($mate_areaid) || !ifint($mate_love)  )json_exit(array('flag'=>0,'msg'=>'择偶要求每项（必填）','obj'=>'mate'));
		if(str_len($aboutus) < 10 || str_len($aboutus)>1000)json_exit(array('flag'=>0,'msg'=>'请输入自我介绍(10-500字)','obj'=>'aboutus'));
		$flag  =($_REG['reg_flag']==1)?1:0;
		$db->query("UPDATE ".__TBL_USER__." SET flag=$flag WHERE id=".$cook_uid);
		//以下为通知
		if ($subscribe==1 && !empty($openid)){
			$C = urlencode(dataIO($nickname.' 您好，恭喜您注册成功！<br><br>→<a href="'.HOST.'">【点此进入缘分大厅】</a><br><br>　','wx'));
			@wx_kf_sent($openid,$C,'text');
		}
		json_exit(array('flag'=>1,'msg'=>'保存成功'));
	break;
	case 'ajax_photo_s_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('m',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$shFlag = $switch['sh']['photom_'.$data_grade];
			$photo_f  = ($shFlag == 1)?1:0;
			$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
			set_data_ed_bfb($cook_uid);
			$path_b = getpath_smb($data_photo_s,'b');
			if(!empty($data_photo_s))@up_send_userdel($data_photo_s.'|'.getpath_smb($data_photo_s,'m').'|'.$path_b.'|'.getpath_smb($data_photo_s,'blur'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));}
	break;
	case 'ajax_photo_s_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_uinfo_logo($url,$cook_uid);$_s = setpath_s($dbname);
				}
				$newphoto_s = $_ZEAI['up2']."/".$_s;
				if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				$shFlag = $switch['sh']['photom_'.$data_grade];
				$photo_f = ($shFlag == 1)?1:0;
				$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
				set_data_ed_bfb($cook_uid);
				$path_b = getpath_smb($data_photo_s,'b');
				if(!empty($data_photo_s))@up_send_userdel($data_photo_s.'|'.getpath_smb($data_photo_s,'m').'|'.$path_b.'|'.getpath_smb($data_photo_s,'blur'));
				json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
			}
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));}
	break;
	case 'ajax_nickname':
		Dmod("nickname='".dataIO($value,'in')."',dataflag=".$switchdataflag);
		setcookie("cook_nickname",urldecode(dataIO($value,'out')),null,"/",$_ZEAI['CookDomain']);
		jsonOutAndBfb(1);
	break;
	case 'ajax_aboutus':
		Dmod("aboutus='".dataIO($value,'in',2000)."',dataflag=".$switchdataflag);
		jsonOutAndBfb(1);
	break;
	case 'ajax_house':if (ifint($value))Dmod("house=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_car':if (ifint($value))Dmod("car=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_mate_age':Dmod("mate_age1=".intval($i1));Dmod("mate_age2=".intval($i2));jsonOutAndBfb(1);break;
	case 'ajax_mate_heigh':Dmod("mate_heigh1=".intval($i1));Dmod("mate_heigh2=".intval($i2));jsonOutAndBfb(1);break;
	case 'ajax_mate_pay':Dmod("mate_pay=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_mate_edu':Dmod("mate_edu=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_mate_love':Dmod("mate_love=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_mate_house':Dmod("mate_house=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_mate_area':Dmod("mate_areaid='".dataIO($areaid,'in',50)."',mate_areatitle='".dataIO($areatitle,'in',50)."'");jsonOutAndBfb(1);break;
}
/********************************************************开始********************************************************/
$reg_loveb = abs(intval($_REG['reg_loveb']));
	$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areaid,mate_areatitle,mate_love,mate_house,aboutus,uname,nickname,birthday,areaid,areatitle,area2id,area2title,love,heigh,weigh,edu,job,pay,house,car,nation,area2id,area2title,child,blood,tag,marrytype,truename,identitynum,mob,address,weixin,weixin_pic,qq,email","id=".$cook_uid,"name");
	if ($row){
		$mate_age1      = intval($row['mate_age1']);
		$mate_age2      = intval($row['mate_age2']);
		$mate_heigh1    = intval($row['mate_heigh1']);
		$mate_heigh2    = intval($row['mate_heigh2']);
		$mate_pay       = intval($row['mate_pay']);
		$mate_edu       = intval($row['mate_edu']);
		$mate_areaid    = $row['mate_areaid'];
		$mate_areatitle = $row['mate_areatitle'];
		$mate_love      = intval($row['mate_love']);
		$mate_house     = intval($row['mate_house']);
		$mate_age       = $mate_age1.','.$mate_age2;
		$mate_age_str   = mateset_out($mate_age1,$mate_age2,'岁');
		$mate_heigh     = $mate_heigh1.','.$mate_heigh2;
		$mate_heigh_str = mateset_out($mate_heigh1,$mate_heigh2,'厘米');
		$mate_age_str = str_replace("不限","",$mate_age_str);
		$mate_heigh_str = str_replace("不限","",$mate_heigh_str);
		$mate_areatitle_str = (!empty($mate_areatitle))?$mate_areatitle:'';
		$mate_pay_str = (ifint($mate_pay))?udata('pay',$mate_pay):'';
		$mate_edu_str = (ifint($mate_edu))?udata('edu',$mate_edu):'';
		$mate_love_str = (ifint($mate_love))?udata('love',$mate_love):'';
		$mate_house_str = (ifint($mate_house))?udata('house',$mate_house):'';
		//
		$aboutus    = dataIO($row['aboutus'],'wx');
		//$aboutus=(empty($aboutus))?'自我介绍（10~500字）':$aboutus;
		$birthday   = $row['birthday'];
		$uname      = dataIO($row['uname'],'out');
		$nickname   = dataIO($row['nickname'],'out');
		$nickname   = urldecode($nickname);
		$areaid     = $row['areaid'];
		$areatitle  = $row['areatitle'];
		$area2id     = $row['area2id'];
		$area2title  = $row['area2title'];
		$heigh      = $row['heigh'];
		$weigh      = $row['weigh'];
		$love       = $row['love'];
		$edu        = $row['edu'];
		$job        = $row['job'];
		$pay        = $row['pay'];
		$house      = $row['house'];
		$car        = $row['car'];
		$nation     = $row['nation'];
		$area2id    = $row['area2id'];
		$area2title = $row['area2title'];
		$child      = $row['child'];
		$blood      = $row['blood'];
		$tag        = $row['tag'];
		$marrytype  = $row['marrytype'];
		//truename,mob,address,weixin,qq,email
		$truename   = dataIO($row['truename'],'out');
		$identitynum= dataIO($row['identitynum'],'out');
		$address    = dataIO($row['address'],'out');
		$weixin     = dataIO($row['weixin'],'out');
		$weixin_pic = $row['weixin_pic'];
		$qq         = dataIO($row['qq'],'out');
		$email      = dataIO($row['email'],'out');
		$mob        = dataIO($row['mob'],'out');
	}

?>
<?php
//$nickname = (empty($cook_nickname))?$cook_uname:$cook_nickname;
$headertitle = '个人资料-';require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="<?php echo HOST;?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug: false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems']
	});
	</script>
<?php }?>
<link href="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.css" rel="stylesheet" type="text/css" />
<link href="css/reg_ed.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/iscroll.js"></script>
<script src="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.js"></script>
<script src="<?php echo HOST;?>/res/zeai_ios_select/separate/select_mini.js"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/birthday.js"></script>
<script>
var Sbindbox='';
var upMaxMB = <?php echo intval($_UP['upMaxMB']); ?>,browser='<?php echo (is_weixin())?'wx':'h5';?>';
</script>
</head>
<body>
<div class="reg_ed">
	
	<div class="success">
        <i class="ico">&#xe60d;</i>
        <h2 class="B"><?php echo $uname;?>，请完善资料</h2>
        <?php if ($reg_loveb >0){?>
			<?php if ($t == 'success'){?>
            <h5 class="C999">奖励您<?php echo $_ZEAI['loveB'];?><font class="Chong"><?php echo $reg_loveb;?></font>个</h5>
            <?php }?>
        <?php }?>
        <h5 class="C999">完善资料，上传照片，交友成功率提升300%</h5>
    </div>
    
    
    <br><div class="linebox"><div class="line W50"></div><div class="title S18 C999 BAI B">设置头像</div></div>
    <div class="icoadd" id="photo_s"><i class="ico">&#xe620;</i><h5>点击上传</h5></div>
    
    <div id="nophoto_sBox">
        
        <div class="linebox"><div class="line W50"></div><div class="title S14 C999 BAI">头像审核标准</div></div>
        <div class="reg_p">
            <li><img src="img/reg_p/1.jpg"><i class="ico dui">&#xe60d;</i>真实居中</li>
            <li><img src="img/reg_p/2.jpg"><i class="ico dui">&#xe60d;</i>上半身照</li>
            <li><img src="img/reg_p/3.jpg"><i class="ico cuo">&#xe62c;</i>模糊不清</li>
            <li><img src="img/reg_p/4.jpg"><i class="ico cuo">&#xe62c;</i>过于暴露</li>
            <li><img src="img/reg_p/5.jpg"><i class="ico cuo">&#xe62c;</i>P图过度</li>
            <div class="clear"></div>
        </div>
    </div>
    <div id="mateBox">
        <div class="modlist">
            <div class="linebox"><div class="line W50"></div><div class="title S18 C999 BAI B">昵称/资产</div></div>
            <ul>
                <li id="nickname" class="ipt" data="<?php echo $nickname;?>"><h4>网名/昵称</h4><span><?php echo $nickname;?></span></li>
                <li id="house" class="slect" data="<?php echo $house;?>"><h4>住房情况</h4><span><?php echo udata('house',$house);?></span></li>
                <li id="car" class="slect" data="<?php echo $car;?>"><h4>购车情况</h4><span><?php echo udata('car',$car);?></span></li>
            </ul>
            <!--择友要求-->
            <br><div class="linebox"><div class="line W50"></div><div class="title S18 C999 BAI B">择偶要求</div></div>
            <ul>
                <li id="mate_age" class="rang" data="<?php echo $mate_age;?>"><h4>年龄区间</h4><span><?php echo $mate_age_str;?></span></li>
                <li id="mate_heigh" class="rang" data="<?php echo $mate_heigh;?>"><h4>身高区间</h4><span><?php echo $mate_heigh_str;?></span></li>
                <li id="mate_pay" class="slect" data="<?php echo $mate_pay;?>"><h4>最低月收入</h4><span><?php echo $mate_pay_str;?></span></li>
                <li id="mate_edu" class="slect" data="<?php echo $mate_edu;?>"><h4>最低学历</h4><span><?php echo $mate_edu_str;?></span></li>
                <li id="mate_area" class="aread" data="<?php echo $mate_areaid;?>"><h4>地区要求</h4><span><?php echo $mate_areatitle_str;?></span></li>
                <li id="mate_love" class="slect" data="<?php echo $mate_love;?>"><h4>婚姻要求</h4><span><?php echo $mate_love_str;?></span></li>
                <li id="mate_house" class="slect" data="<?php echo $mate_house;?>"><h4>住房要求</h4><span><?php echo $mate_house_str;?></span></li>
            </ul>
            <!--自我介绍-->
            <br><div class="linebox"><div class="line W50"></div><div class="title S18 C999 BAI B">自我介绍</div></div>
            <ul class="textarea2">
                <textarea class="textarea" id="aboutus" placeholder="自我介绍（10~500字）"><?php echo $aboutus;?></textarea>
            </ul>
    
        </div>
        <button type="button" class="btn size4 HONG B" onClick="my_info_save();">保存资料，开启幸福之旅</button>
    </div>
    <br><br>
	<script src="js/reg_ed.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</div>
</body>
</html>
<?php 
function Dmod($field){
	global $db,$cook_uid;
	$db->UPDATE($cook_uid,$field,__TBL_USER__,"id=".$cook_uid);
}
function jsonOutAndBfb($update){
	global $cook_uid;
	if($update==1)set_data_ed_bfb($cook_uid);
	json_exit(array('flag'=>1,'msg'=>'保存成功'));
}
?>
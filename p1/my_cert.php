<?php
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
if (ini_get('session.auto_start') == 0)session_start();
$currfields = 'grade,openid,photo_s,photo_f,RZ,mob,truename,identitynum,weixin,qq,email';
require_once 'my_chkuser.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'sub/liamiaez.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
$data_grade  = $row['grade'];
$data_RZ=$row['RZ'];$RZarr=explode(',',$data_RZ);
$data_mob=$row['mob'];
$data_photo_s= $row['photo_s'];
$data_myinfobfb= $row['myinfobfb'];
$data_dataflag= $row['dataflag'];
$urole = json_decode($_ZEAI['urole']);
$switch=json_decode($_ZEAI['switch'],true);
$data_truename= dataIO($row['truename'],'out');
$data_identitynum= dataIO($row['identitynum'],'out');
$data_weixin= dataIO($row['weixin'],'out');
$data_qq= dataIO($row['qq'],'out');
$data_email= dataIO($row['email'],'out');
switch ($t) {
	case 'mob':$t_str = '手机认证';break;
	case 'identity':$t_str = '身份认证';break;
	case 'edu':$t_str = '学历认证';break;
	case 'car':$t_str = '汽车认证';break;
	case 'house':$t_str = '房产认证';break;
	case 'weixin':$t_str = '微信认证';break;
	case 'qq':$t_str = 'QQ认证';break;
	case 'email':$t_str = '邮箱认证';break;
	case 'sesame':$t_str = '芝麻信用';break;
	case 'police':$t_str = '公安认证';break;
	default:$t_str = '诚信认证';break;
}
/********************* ajax update **********************/
switch ($submitok) {
	case 'ajax_cert_mob_getyzm':
		if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		if($data_mob==$mob && in_array('mob',$RZarr))json_exit(array('flag'=>0,'msg'=>'请输入其它手机号码'));
		if ($Temp_regyzmrenum > $_SMS['sms_yzmnum'] )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
	  $_SESSION['Zeai_cn__mobyzm'] = cdstr(4);
		//sms
		$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__mobyzm']);
		if ($rtn == 0){
			setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,null,"/",$_ZEAI['CookDomain']);  
			$chkflag = 1;
			$content = '验证码发送成功，请注意查收';
		}else{
			$chkflag = 0;
			$content = "发送失败,错误码：$rtn";
		}
		//sms end
		$_SESSION['Zeai_cn__mob'] = $mob;
		json_exit(array('flag'=>$chkflag,'msg'=>$content));
	case 'ajax_cert_mob_addupdate':
		if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		$verify = intval($verify);
		if ($_SESSION['Zeai_cn__mobyzm'] != $verify || empty($_SESSION['Zeai_cn__mobyzm']))json_exit(array('flag'=>0,'msg'=>'短信验证码不正确，请重新获取'));
		if ($_SESSION['Zeai_cn__mob'] != $mob)json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));doRZ('mob',$mob);
		unset($_SESSION["Zeai_cn__mobyzm"]);
		unset($_SESSION["Zeai_cn__mob"]);
		json_exit(array('flag'=>1,'msg'=>'手机认证成功！'));
	break;
	case 'ajax_sfz_h5_up':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_RZ_');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_cert_identity_update':
		$truename=dataIO($truename,'in',12);
		$identitynum=dataIO($identitynum,'in',18);
		$sfz1=dataIO($sfz1,'in',50);
		$sfz2=dataIO($sfz2,'in',50);
		$row = $db->ROW(__TBL_RZ__,"id,flag,path_b,path_b2","uid=".$cook_uid." AND rzid='identity'","num");
		if ($row){
			$id = $row[0];$flag = $row[1];$path_b = $row[2];$path_b2 = $row[3];
			if ($flag==0){;
				sfzRenameUpdate($sfz1,$sfz2);
				$sfz1 = str_replace('tmp','rz',$sfz1);$sfz2 = str_replace('tmp','rz',$sfz2);
				$db->query("UPDATE ".__TBL_USER__." SET truename='$truename',identitynum='$identitynum' WHERE id=".$cook_uid);
				$db->query("UPDATE ".__TBL_RZ__." SET path_b='$sfz1',path_b2='$sfz2' WHERE id=".$id);
				@up_send_userdel($path_b.'|'.$path_b2);
			}
		}else{
			sfzRenameUpdate($sfz1,$sfz2);
			$sfz1 = str_replace('tmp','rz',$sfz1);$sfz2 = str_replace('tmp','rz',$sfz2);
			$db->query("INSERT INTO ".__TBL_RZ__."(uid,rzid,path_b,path_b2,addtime) VALUES ($cook_uid,'identity','$sfz1','$sfz2',".ADDTIME.")");
			$db->query("UPDATE ".__TBL_USER__." SET truename='$truename',identitynum='$identitynum' WHERE id=".$cook_uid);
		}
		json_exit(array('flag'=>1,'msg'=>'提交成功，请等待审核'));
	break;
	case 'ajax_cert_edu_update':
		ajax_cert_fn('edu',$path_b);
	break;
	case 'ajax_cert_car_update':
		ajax_cert_fn('car',$path_b);
	break;
	case 'ajax_cert_house_update':
		ajax_cert_fn('house',$path_b);
	break;
	case 'ajax_cert_love_update':
		ajax_cert_fn('love',$path_b);
	break;
	case 'ajax_cert_pay_update':
		ajax_cert_fn('pay',$path_b);
	break;
	/*************** QQ **************/
	case 'ajax_cert_qq2_yzm_get':
		$qq = $value;
		if(!ifint($qq,'0-9','5,11'))json_exit(array('flag'=>0,'msg'=>'请输入正确的QQ号'));
		if($data_qq==$qq && in_array('qq',$RZarr))json_exit(array('flag'=>0,'msg'=>'请输入其它QQ号'));
		if ($Temp_regyzmrenum > $_SMS['sms_yzmnum'] )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
		$_SESSION['Zeai_cn__mobyzm'] = cdstr(4);
		//
		$title='【邮箱验证码：'.$_SESSION['Zeai_cn__mobyzm'].'】'.$_ZEAI['siteName'];
		$email=$qq.'@qq.com';
		$ret = sendemail($email,$title,$title);
		//$ret['msg'] = '验证码已'.$ret['msg'].'您的QQ邮箱';
		setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,null,"/",$_ZEAI['CookDomain']);  
		//
		$_SESSION['Zeai_cn__mob'] = $qq;
		//json_exit($ret);
		json_exit(array('flag'=>1,'msg'=>'验证码已发送至'.$email.'邮箱，请查收'));
	break;
	case 'ajax_cert_weixin_update':
		$weixin=dataIO($weixin2,'in',50);
		if(str_len($weixin)>50 || str_len($weixin)<2)json_exit(array('flag'=>0,'msg'=>'请输入正确的微信号'));
		//
		$RZ = explode(',',$data_RZ);
		if (empty($RZ) || count($RZ)<=0 || empty($data_RZ)){
			$db->query("UPDATE ".__TBL_USER__." SET RZ='weixin' WHERE id=".$cook_uid);
		}else{
			if (!in_array('weixin',$RZ)){
				$RZ[]='weixin';
				$list = implode(',',$RZ);
				$db->query("UPDATE ".__TBL_USER__." SET RZ='$list' WHERE id=".$cook_uid);
			}
		}
		$db->query("UPDATE ".__TBL_USER__." SET weixin='$weixin' WHERE id=".$cook_uid);
		jsonOutAndBfb(1);
		json_exit(array('flag'=>1,'msg'=>'微信认证成功'));
	break;
	case 'ajax_cert_qq2_yzm_submit':
		$qq = $value;
		if(!ifint($qq,'0-9','5,11'))json_exit(array('flag'=>0,'msg'=>'QQ号不正确'));
		if(!ifint($verify,'0-9','4'))json_exit(array('flag'=>0,'msg'=>'验证码不正确，请检查QQ邮箱'));
		$verify = intval($verify);
		if ($_SESSION['Zeai_cn__mobyzm'] != $verify || empty($_SESSION['Zeai_cn__mobyzm']))json_exit(array('flag'=>0,'msg'=>'验证码不正确，请重新获取'));
		if ($_SESSION['Zeai_cn__mob'] != $qq)json_exit(array('flag'=>0,'msg'=>'QQ号码异常，请重新获取'));doRZ('qq',$qq);
		unset($_SESSION["Zeai_cn__mobyzm"]);unset($_SESSION["Zeai_cn__mob"]);
		json_exit(array('flag'=>1,'msg'=>'QQ认证成功！'));
	break;
	/****************** email ***************/
	case 'ajax_cert_email2_yzm_get':
		$email = $value;
		if(!ifemail($email))json_exit(array('flag'=>0,'msg'=>'请输入正确的邮箱'));
		if($data_email==$email && in_array('email',$RZarr))json_exit(array('flag'=>0,'msg'=>'请输入其它邮箱'));
		if ($Temp_regyzmrenum > $_SMS['sms_yzmnum'] )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
		$_SESSION['Zeai_cn__mobyzm'] = cdstr(4);
		//
		$title='【邮箱验证码：'.$_SESSION['Zeai_cn__mobyzm'].'】'.$_ZEAI['siteName'];
		$ret = sendemail($email,$title,$title);
		$ret['msg'] = '验证码已'.$ret['msg'].'您的QQ邮箱';
		setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,null,"/",$_ZEAI['CookDomain']);  
		//
		$_SESSION['Zeai_cn__mob'] = $email;
		json_exit(array('flag'=>1,'msg'=>'验证码已发送至邮箱，请查收'));
	
	break;
	case 'ajax_cert_email2_yzm_submit':
		$email = $value;
		if(!ifemail($email))json_exit(array('flag'=>0,'msg'=>'邮箱不正确'));
		if(!ifint($verify,'0-9','4'))json_exit(array('flag'=>0,'msg'=>'验证码不正确，请到邮箱检查'));
		$verify = intval($verify);
		if ($_SESSION['Zeai_cn__mobyzm'] != $verify || empty($_SESSION['Zeai_cn__mobyzm']))json_exit(array('flag'=>0,'msg'=>'验证码不正确，请重新获取'));
		if ($_SESSION['Zeai_cn__mob'] != $email)json_exit(array('flag'=>0,'msg'=>'邮箱异常，请重新获取'));doRZ('email',$email);
		unset($_SESSION["Zeai_cn__mobyzm"]);unset($_SESSION["Zeai_cn__mob"]);
		json_exit(array('flag'=>1,'msg'=>'邮箱认证成功！'));
	break;
}
function RZchkFlag($objstr,$RZarr) {
	global $cook_uid,$db;
	if (!in_array($objstr,$RZarr)){
		$doing = $db->COUNT(__TBL_RZ__,"uid=".$cook_uid." AND rzid='$objstr' AND flag=0");
		if ($doing>0)return '<h5>审核中</h5>';
	}else{
		return '';
	}
}
$zeai_cn_menu = 'my_cert';
$rz_dataARR   = explode(',',$_ZEAI['rz_data']);
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $t_str;?> - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_cert.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<script>
var upMaxMB = <?php echo $_UP['upMaxMB']; ?>;
var up2='<?php echo $_ZEAI['up2'];?>/';
</script>
<script src="js/my_cert.js"></script>
</head>
<body>
<?php
if(!empty($submitok))echo '<style>body{background-color:#fff;}</style>';
switch ($submitok) {case 'mob'://手机认证
	$ifMobRz = (ifmob($data_mob) && strstr($data_RZ,'mob'))?true:false;
	if (ifmob($data_mob) && strstr($data_RZ,'mob')){
		$flagstr='<span class="flag1">已认证</span>';
		$ifRz=true;
		$placeholder='输入其它手机重新认证';	
	}else{
		$flagstr='<span class="flag0">未认证</span>';
		$ifRz=false;
		$placeholder='请输入手机号码';	
	}?>
    <div class="my_cert_one">
        <h1>手机认证<?php
            if ($flagstr != ''){echo $flagstr;}
            if ($ifRz){echo '<font>'.substr($data_mob,0,3).'****'.substr($data_mob,-4,4).'</font>';}
        ?></h1>
        <dl><dt><i class="ico">&#xe627;</i></dt><dd>
            <input name="mob_rz" type="text" class="input_login" id="mob_rz" placeholder="<?php echo $placeholder;?>" autocomplete="off" maxlength="11" pattern="[0-9]*" oninput="chkinputmob()">
            <span class="ico" onClick="mob_rz.value='';this.hide();chkinputmob();" id="reset_rz">&#xe65b;</span>
        </dd></dl>
        <dl><dt><i class="ico">&#xe6c3;</i></dt><dd>
            <input name="verify" type="text" id="verify" maxlength="4" pattern="[0-9]*" placeholder="请输入验证码" autocomplete="off" class="input_login" />
            <button type="button" id="yzmbtn">获取验证码</button>
        </dd></dl>       
        <input type="button" value="确定" class="btn size4 HONG W85_ B hui" id="submitbtn_rz" onClick="chkform_verify()">
        <script src="js/my_cert.js"></script>
        <script>yzmbtn.onclick = yzmbtnFn;</script>
	</div>
    
    
<?php exit;break;case 'photo'://人脸?>
	<style>
		.my_cert_one h1{margin-bottom:10px}
		.my_cert_one img{width:60%;border:#ADD2F0 1px solid;padding:10px;margin:30px auto 20px auto;border-radius:5px}
		.my_cert_one .zeai_rz_ico{margin-top:30px;font-size:16px}
		.my_cert_one .zeai_rz_ico .ico{vertical-align:middle;color:#0068AF;font-size:20px}
		.my_cert_one .zeai_rz_ico span{vertical-align:middle;display:inline-block}
    </style>
	<div class="my_cert_one">
		<h1>自助<?php echo rz_data_info('photo','title');?></h1>
		<img src="<?php echo HOST;?>/sub/creat_ewm.php?url=<?php echo HOST;?>/?z=my&e=my_info&a=cert&i=photo">
		<h4>请用手机微信扫码使用该认证功能</h4>
        <div class="zeai_rz_ico"><i class="ico">&#xe60d;</i> <span>公安库权威验证</span>　<i class="ico">&#xe60d;</i> <span>真人校验 · 人脸识别</span></div>
	</div>
<?php exit;break;case 'identity'://身份认证
	if($_SMS['rz_mobile3']==1){
	?>
	<style>
		.my_cert_one h1{margin-bottom:10px}
		.my_cert_one img{width:60%;border:#ddd 1px solid;padding:10px;margin:30px auto 20px auto;border-radius:5px}
		.my_cert_one .zeai_rz_ico{margin-top:30px;font-size:16px}
		.my_cert_one .zeai_rz_ico .ico{vertical-align:middle;color:#FF0000;font-size:20px}
		.my_cert_one .zeai_rz_ico span{vertical-align:middle;display:inline-block}
    </style>
	<div class="my_cert_one">
		<h1>自助<?php echo rz_data_info('identity','title');?></h1>
		<img src="<?php echo HOST;?>/sub/creat_ewm.php?url=<?php echo HOST;?>/?z=my&e=my_info&a=cert&i=identity">
		<h4>请用手机微信扫码使用该认证功能</h4>
        <div class="zeai_rz_ico"><i class="ico">&#xe60d;</i> <span>通信三网联合</span>　<i class="ico">&#xe60d;</i> <span>反失信库</span>　<i class="ico">&#xe60d;</i> <span>反欺诈库</span></div>
	</div>
	<?php
	}else{
	$row = $db->ROW(__TBL_RZ__,"path_b,path_b2,flag","uid=".$cook_uid." AND rzid='identity'","num");
	if ($row){
		$path_b  = $row[0];
		$path_b2 = $row[1];
		$flag    = $row[2];
		$path_b_url  = (!empty($path_b ))?$_ZEAI['up2'].'/'.$path_b:HOST.'/res/sfz1.jpg';
		$path_b2_url = (!empty($path_b2 ))?$_ZEAI['up2'].'/'.$path_b2:HOST.'/res/sfz2.jpg';
		$flagstr=($flag==1)?'<span class="flag1">已认证</span>':'<span class="flag2">审核中</span>';
	}else{
		$path_b_url = HOST.'/res/sfz1.jpg';
		$path_b2_url= HOST.'/res/sfz2.jpg';
		$flag=0;
		$flagstr='<span class="flag0">未认证</span>';
	}
	?>
	<style>
		.my_cert_one h1{margin-bottom:10px}
		.my_cert_one dl{width:85%;margin:0px auto;}
		.my_cert_one dl:first-child{margin:0px auto}
		.my_cert_one .btn{margin-top:0}
		.my_cert_one dl dd .input_login{margin-top:0}
		.linebox+ h5{text-align:left;font-size:12px;line-height:150%}
		.my_cert_one .btn{margin:10px auto}
    </style>
    <div class="my_cert_one">
        <h1>身份认证<?php if ($flagstr != ''){echo $flagstr;}?></h1>
        <form id="ZEAIFORM" action="<?php echo SELF; ?>" method="post">
        <dl><dt></dt><dd><input name="truename" type="text" class="input_login" id="truename" placeholder="请填写您的真实姓名" autocomplete="off" maxlength="8" value="<?php echo $data_truename;?>"></dd></dl>
        <dl><dt></dt><dd><input name="identitynum" type="text" id="identitynum" maxlength="18" pattern="[0-9]*" placeholder="请填写您的身份证号" autocomplete="off" class="input_login"  value="<?php echo $data_identitynum;?>"/></dd></dl>
        <div class="sfzbox">
            <div<?php if ($flag != 1){?> onClick="<?php echo (!is_weixin())?'sfz_up(\'h5\',this,\'sfz1\');':'sfz_up(\'wx\',this,\'sfz1\');';?>"<?php }?>><img src="<?php echo $path_b_url;?>"><?php if ($flag != 1){?><span>点击上传国徽面✚</span><?php }?></div>
            <div<?php if ($flag != 1){?> onClick="<?php echo (!is_weixin())?'sfz_up(\'h5\',this,\'sfz2\');':'sfz_up(\'wx\',this,\'sfz2\');';?>"<?php }?>><img src="<?php echo $path_b2_url;?>"><?php if ($flag != 1){?><span>点击上传人像面✚</span><?php }?></div>
        </div>
        <input type="button" value="保存并提交" class="btn size4 HONG W85_ B<?php echo ($flag == 1)?' HUI':'';?>"<?php if ($flag != 1){?> onClick="chkform_identity()"<?php }?>>
        <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
        <h5 class="C666">
            <b>●</b><?php echo $_ZEAI['siteName'];?>作为一个真实、严肃的婚恋平台、我们要求用户必须完成身份证实名认证，保证会员真实100%。<br>
            <b>●</b>您上传的任何身份证图片等资料，仅供审核使用且TA人无法看到，此外，我们会对图片进行安全处理，敬请放心。
        </h5>
        <input name="submitok" type="hidden" value="ajax_cert_identity_update" />
        <input name="sfz1" id="sfz1" type="hidden" value="" />
        <input name="sfz2" id="sfz2" type="hidden" value="" />
        </form>
	</div>
<?php }exit;break;case 'weixin':
	if (in_array('weixin',$RZarr) && !empty($data_weixin)){
		$flagstr='<span class="flag1">已认证</span>';
		$placeholder='输入其它微信号重新认证';
		$ifRz=true;
	}else{
		$flagstr='<span class="flag0">未认证</span>';
		$placeholder='请填写您的微信号';	
		$ifRz=false;
	}
	?>
    <div class="my_cert_one">
        <h1>微信认证<?php
            if ($flagstr != ''){echo $flagstr;}
            if ($ifRz){echo '<font>'.substr($data_weixin,0,2).'***'.substr($data_weixin,-2,2).'</font>';}
            ?>
        </h1>
        <form id="ZEAIFORM">
        <dl><dt><i class="ico">&#xe607;</i></dt><dd>
            <input name="weixin2" type="text" class="input_login" id="weixin2" placeholder="<?php echo $placeholder;?>" autocomplete="off" maxlength="50" value="<?php echo $data_weixin;?>">
        </dd></dl>
        <input name="submitok" type="hidden" value="ajax_cert_weixin_update" />
        <input type="button" value="保存并提交" class="btn size4 HONG W85_ B" id="submitbtn_weixin" onClick="chkform_weixin()">
        
        <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
        <h5 class="C666">
            <b>●</b>微信认证成功后，将点亮微信图标。<br><br>
        </h5>
        </form>
	</div>
<?php exit;break;case 'qq':
	if (in_array('qq',$RZarr) && ifint($data_qq,'0-9','5,11')){
		$flagstr='<span class="flag1">已认证</span>';
		$placeholder='输入其它QQ号重新认证';
		$ifRz=true;
	}else{
		$flagstr='<span class="flag0">未认证</span>';
		$placeholder='请填写您的QQ号';	
		$ifRz=false;
	}
	?>
    <div class="my_cert_one">
        <h1>QQ认证<?php
            if ($flagstr != ''){echo $flagstr;}
            if ($ifRz){echo '<font>'.substr($data_qq,0,2).'***'.substr($data_qq,-2,2).'</font>';}
            ?>
        </h1>
        <form id="ZEAIFORM">
        <dl><dt><i class="ico">&#xe630;</i></dt><dd>
            <input name="qq2" type="text" class="input_login" id="qq2" placeholder="<?php echo $placeholder;?>" autocomplete="off" maxlength="11" pattern="[0-9]*" value="<?php echo $data_qq;?>">
        </dd></dl>
        <dl><dt><i class="ico">&#xe6c3;</i></dt><dd>
            <input name="verify" type="text" id="verify" maxlength="4" pattern="[0-9]*" placeholder="填写此QQ邮箱收到的验证码" autocomplete="off" class="input_login" />
            <button type="button" id="yzmbtn">获取验证码</button>
        </dd></dl>       
        <input type="button" value="保存并提交" class="btn size4 HONG W85_ B" id="submitbtn_qq2">
        
        <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
        <h5 class="C666">
            <b>●</b>QQ认证成功后，将点亮QQ图标。<br><br>
        </h5>
        </form>
        <script>yzmInit({"obj":qq2,"btn":yzmbtn,"S":120,"T":'请输入您的QQ号',"url":PCHOST+'/my_cert'+zeai.extname,"form":ZEAIFORM});</script>
	</div>
<?php exit;break;case 'email':
	$RZarr=explode(',',$data_RZ);
	if (in_array('email',$RZarr) && ifemail($data_email)){
		$flagstr='<span class="flag1">已认证</span>';
		$placeholder='输入其它邮箱重新认证';
		$ifRz=true;
	}else{
		$flagstr='<span class="flag0">未认证</span>';
		$placeholder='请填写您的邮箱';	
		$ifRz=false;
	}
	?>
    <div class="my_cert_one">
    <h1>邮箱认证<?php
		if ($flagstr != ''){echo $flagstr;}
		if ($ifRz){echo '<font>'.substr($data_email,0,4).'****'.substr($data_email,-4,4).'</font>';}
		?>
	</h1>
    <form id="ZEAIFORM">
    <dl><dt><i class="ico">&#xe641;</i></dt><dd>
        <input name="email2" type="text" class="input_login" id="email2" placeholder="<?php echo $placeholder;?>" autocomplete="off" maxlength="50" value="<?php echo $data_email;?>">
    </dd></dl>
    <dl><dt><i class="ico">&#xe6c3;</i></dt><dd>
        <input name="verify" type="text" id="verify" maxlength="4" pattern="[0-9]*" placeholder="填写此邮箱收到的验证码" autocomplete="off" class="input_login" />
        <button type="button" id="yzmbtn">获取验证码</button>
    </dd></dl>       
    <input type="button" value="保存并提交" class="btn size4 HONG W85_ B" id="submitbtn_email2">
    
    <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
    <h5 class="C666">
        <b>●</b>邮箱认证成功后，将点亮邮箱图标。<br><br>
    </h5>
    </form>
	<script>yzmInit({"obj":email2,"btn":yzmbtn,"S":120,"T":'请输入您的邮箱',"url":PCHOST+'/my_cert'+zeai.extname,"form":ZEAIFORM});</script>
    </div>
<?php exit;break;case 'sesame':?>
    <div class="my_cert_one">
    <h1>芝麻信用<span class="flag0">未认证</span></h1>
    
    <br><br><br>
    如需此功能，请联系客服进行定制
    
    </div>
<?php exit;break;case 'police':?>
    <div class="my_cert_one">
    <h1>公安认证<span class="flag0">未认证</span></h1>
    
    <br><br><br>
    如需此功能，请联系客服进行定制
    
    </div>
<?php exit;break;}$i=$submitok;if ($i == 'edu'||$i == 'car'||$i == 'house'||$i == 'love'||$i == 'pay'){
	switch ($i) {
		case 'edu':$objtitle  = '学历';$objtitle2 = '学历证书';break;
		case 'car':$objtitle  = '汽车';$objtitle2 = '行驶证';break;
		case 'house':$objtitle= '房产';$objtitle2 = '房产证';break;
		case 'love':$objtitle= '婚况';$objtitle2 = '单身证明或离婚证';break;
		case 'pay':$objtitle= '收入';$objtitle2 = '收入证明或收入流水';break;
	}
	$row = $db->ROW(__TBL_RZ__,"path_b,flag","uid=".$cook_uid." AND rzid='".$i."'","num");
	if ($row){
		$path_b  = $row[0];
		$flag    = $row[1];
		$path_b_url  = (!empty($path_b ))?$_ZEAI['up2'].'/'.$path_b:HOST.'/res/sfz1.jpg';
		$flagstr=($flag==1)?'<span class="flag1">已认证</span>':'<span class="flag2">审核中</span>';
	}else{
		$path_b_url = HOST.'/res/cert_'.$i.'.jpg';
		$flag=0;
		$flagstr='<span class="flag0">未认证</span>';
	}
	?>
    <div class="my_cert_one">
        <h1><?php echo $objtitle;?>认证<?php if ($flagstr != ''){echo $flagstr;}?></h1>
        <div class="certbox">
            <div<?php if ($flag != 1){?> onClick="<?php echo (!is_weixin())?'sfz_up(\'h5\',this,\'path_b\');':'sfz_up(\'wx\',this,\'path_b\');';?>"<?php }?>><img src="<?php echo $path_b_url;?>"<?php echo ($i == 'car')?' style="height:30%;"':'';?>><?php if ($flag != 1){?><span>点击上传<?php echo $objtitle2;?>✚</span><?php }?></div>
        </div>
        <input type="button" value="保存并提交" class="btn size4 HONG W85_ B<?php echo ($flag == 1)?' HUI':'';?>"<?php if ($flag != 1){?> onClick="chkform_i('<?php echo $i;?>')"<?php }?>>
        <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
        <h5 class="C666">
            <b>●</b>请上传清晰的<?php echo $objtitle2;?>，如上图示例<br>
            <b>●</b><?php echo $objtitle;?>认证成功后，点亮<?php echo $objtitle;?>图标。<br>
        </h5>
        <form id="ZEAIFORM" action="<?php echo SELF; ?>" method="post">
        <input name="submitok" type="hidden" value="ajax_cert_<?php echo $i;?>_update" />
        <input name="path_b" id="path_b" type="hidden" value="" />
        </form>
    </div>
<?php exit;}?>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1><?php echo $t_str;
		if (count($RZarr) >= 1 && is_array($RZarr)){
			echo '<b>当前星级：</b>';
			echo RZ_star($data_RZ);
		}
		?></h1>
        <div class="tab">
            <a href="<?php echo SELF;?>"<?php echo ($t=='')?' class="ed"':'';?>>我的认证</a>
        </div>
         <!-- start C -->
        <div class="myRC">
            <ul class="my_cert fadeInR" id="my_certbox">
            
				<?php
                if (count($rz_dataARR) >= 1 && is_array($rz_dataARR)){
                    foreach ($rz_dataARR as $k=>$V) {
                    ?>
                    <li id="<?php echo $V;?>" class="rz<?php echo (in_array($V,$RZarr))?' ed':'';?>"><i class="ico <?php echo $V;?>"><?php echo rz_data_info($V,'ico');?></i><h3><?php echo rz_data_info($V,'title');?></h3><?php echo RZchkFlag($V,$RZarr);;?></li>
                <?php }}?>
                
                <!--提示开始-->
                <div class="clear"></div>
                <div class="tipsbox">
                    <div class="tipst">温馨提示：</div>
                    <div class="tipsc">
                        ● 认证项目越多诚信值越高，同时点亮对应的认证图标，有专门的认证会员推荐版块<br>
                        ● 以诚相待，自己未认证不能查看对方认证信息<br>
                        ● 每成功认证一项，诚信星级加一，并且点亮认证图标<br>
                        ● 认证项目越多，人气越高，受众率越高，交友成功率也越高哦
                    </div>
                </div>
                <!--提示结束-->                
            </ul>
        </div>
        <!-- end C -->
</div></div></div>
<!--<script src="js/my_cert.js"></script>
--><script>var supdes='v6.0';my_certFn();</script>
<?php require_once ZEAI.'p1/bottom.php';
function doRZ($objstr,$v='') {
	global $data_RZ,$cook_uid,$db;
	$RZ = explode(',',$data_RZ);
	if (ifmob($v) && $objstr=='mob')$sql=",mob='$v'";
	if (ifint($v,'0-9','5,11') && $objstr=='qq')$sql=",qq='$v'";
	if (ifemail($v) && $objstr=='email')$sql=",email='$v'";
	if (empty($RZ) || count($RZ)<=0 || empty($data_RZ)){
		$db->query("UPDATE ".__TBL_USER__." SET RZ='$objstr'".$sql." WHERE id=".$cook_uid);
	}else{
		if (!in_array($objstr,$RZ)){
			$RZ[]=$objstr;
			$list = implode(',',$RZ);
			$db->query("UPDATE ".__TBL_USER__." SET RZ='$list'".$sql." WHERE id=".$cook_uid);
		}else{
			$db->query("UPDATE ".__TBL_USER__." SET ".ltrim($sql,',')." WHERE id=".$cook_uid);
		}
	}
}
function sfzRenameUpdate($sfz1,$sfz2) {
	u_pic_reTmpDir_send($sfz1,'rz');u_pic_reTmpDir_send($sfz2,'rz');
}
function ajax_cert_fn($objstr,$path_b) {
	global $db,$cook_uid;
	$path_b=dataIO($path_b,'in',50);
	$row = $db->ROW(__TBL_RZ__,"id,flag,path_b","uid=".$cook_uid." AND rzid='".$objstr."'","num");
	if ($row){
		$id = $row[0];$flag = $row[1];$old_path_b = $row[2];
		if ($flag==0){;
			u_pic_reTmpDir_send($path_b,'rz');
			$path_b = str_replace('tmp','rz',$path_b);
			$db->query("UPDATE ".__TBL_RZ__." SET path_b='$path_b' WHERE id=".$id);
			@up_send_userdel($old_path_b);
		}
	}else{
		u_pic_reTmpDir_send($path_b,'rz');
		$path_b = str_replace('tmp','rz',$path_b);
		$db->query("INSERT INTO ".__TBL_RZ__."(uid,rzid,path_b,addtime) VALUES ($cook_uid,'".$objstr."','$path_b',".ADDTIME.")");
	}
	json_exit(array('flag'=>1,'msg'=>'提交成功，请等待审核'));
}
function jsonOutAndBfb($update){
	global $cook_uid;
	if($update==1)set_data_ed_bfb($cook_uid);
	//json_exit(array('flag'=>1,'msg'=>'保存成功'));
}

?>
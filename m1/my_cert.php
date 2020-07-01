<?php
require_once '../sub/init.php';
if (ini_get('session.auto_start') == 0)session_start();
$currfields = 'mob,weixin,qq,email,RZ,truename,identitynum,photo_s,openid,subscribe,tguid';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'sub/liamiaez.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
require_once ZEAI.'cache/config_pay.php';
require_once ZEAI.'sub/TGfun.php';
$data_mob=(!ifmob($row['mob']))?'':$row['mob'];
$data_weixin=$row['weixin'];
$data_qq=$row['qq'];
$data_email=$row['email'];
$data_RZ=$row['RZ'];$RZarr=explode(',',$data_RZ);
$data_truename=dataIO($row['truename'],'out');
$data_identitynum=dataIO($row['identitynum'],'out');
$data_photo_s=$row['photo_s'];
$data_openid=$row['openid'];
$data_subscribe=$row['subscribe'];
$data_tguid=$row['tguid'];
switch ($submitok) {
	case 'chkh5finish':
		if(!ifint($payid))json_exit(array('flag'=>0,'msg'=>'订单ID错误，请返回重新重认'));
		$payflag=0;
		$rowpay = $db->ROW(__TBL_PAY__,"flag","id=".$payid,'num');
		if ($rowpay)$payflag= $rowpay[0];
		if ($payflag==1){
			json_exit(array('flag'=>1,'msg'=>'支付成功'));	
		}else{
			json_exit(array('flag'=>0,'msg'=>'支付失败，请重新认证'));	
		}
	break;
	case 'ajax_mob':
		if (strstr($data_RZ,'mob'))json_exit(array('flag'=>0,'msg'=>'已认证不可更改'));
		if (!empty($value))Dmod("mob='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);
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
	case 'ajax_sfz_app_up':
		$file=$_FILES['file'];
		$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_RZ_');
		if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$newpic = $_ZEAI['up2']."/".$dbname;
		if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));	
		exit;
	break;
	case 'ajax_sfz_wx_up':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_up('tmp',$url,$cook_uid.'_RZ_','B');
				}
				$newpic = $_ZEAI['up2']."/".$dbname;
				if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
		}
	break;	
	case 'ajax_cert_identity_update':
		$truename=dataIO($truename,'in',12);
		$identitynum=dataIO($identitynum,'in',18);
		$sfz1=dataIO($sfz1,'in',80);
		$sfz2=dataIO($sfz2,'in',80);
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
			if (ifpic($_ZEAI['up2']."/".$sfz1)){
				$db->query("INSERT INTO ".__TBL_RZ__."(uid,rzid,path_b,path_b2,addtime) VALUES ($cook_uid,'identity','$sfz1','$sfz2',".ADDTIME.")");
				$db->query("UPDATE ".__TBL_USER__." SET truename='$truename',identitynum='$identitynum' WHERE id=".$cook_uid);
			}
		}
		json_exit(array('flag'=>1,'msg'=>'提交成功，请等待审核'));
	break;
	case 'ajax_cert_weixin_update':
		$weixin=dataIO($weixin2,'in',30);
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
	/****************** email *****************/
	case 'ajax_cert_email2_yzm_get':
		$email = $value;
		if(!ifemail($email))json_exit(array('flag'=>0,'msg'=>'请输入正确的邮箱'));
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
		
	case 'ajax_cert_mob_getyzm':
		if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		if ($db->ROW(__TBL_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)"))json_exit(array('flag'=>0,'msg'=>'此手机号码已被注册，请更换'));
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
			$content = "错误码：$rtn"."-".sms_error($rtn);
		}
		//sms end
		$_SESSION['Zeai_cn__mob'] = $mob;
		json_exit(array('flag'=>$chkflag,'msg'=>$content));
	break;
	case 'ajax_cert_mob_addupdate':
		if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		$verify = intval($verify);
		if ($_SESSION['Zeai_cn__mobyzm'] != $verify || empty($_SESSION['Zeai_cn__mobyzm']))json_exit(array('flag'=>0,'msg'=>'短信验证码不正确，请重新获取'));
		if ($_SESSION['Zeai_cn__mob'] != $mob)json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
		doRZ('mob',$mob);
		unset($_SESSION["Zeai_cn__mobyzm"]);
		unset($_SESSION["Zeai_cn__mob"]);
		json_exit(array('flag'=>1,'msg'=>'手机认证成功！'));
	break;
	
	case 'ajax_cert_identity_mob3_update':
		if($_SMS['rz_mobile3']!=1)json_exit(array('flag'=>0,'msg'=>'自助实名已关闭'));
		$truename=trim($truename);
		$truename=dataIO($truename,'in',12);$identitynum=dataIO($identitynum,'in',18);
		if(empty($truename) || !ifsfz($identitynum))json_exit(array('flag'=>0,'msg'=>'【姓名】或【身份证】不正确'));
		if(!in_array('mob',$RZarr) || !ifmob($data_mob))json_exit(array('flag'=>0,'msg'=>'请返回先进行【手机认证】'));
		$RZstr='identity';
		if($_SMS['rz_price']>0){
			if($_SMS['rz_price']!=$money || empty($orderid))json_exit(array('flag'=>0,'msg'=>'请求不合法'));
			rz_pay($orderid,$kind,$money,$paykind,'实名认证',$rzkind);
		}else{
			start_net_verify($truename,$identitynum,$data_mob,$RZstr);
		}
	break;
	case 'ajax_cert_identity_mob3_update_payok':
		$RZstr='identity';
		start_net_verify($truename,$identitynum,$data_mob,$RZstr);
	break;
	case 'ajax_cert_photo_update':
		$truename=trim($truename);
		$truename=dataIO($truename,'in',12);$identitynum=dataIO($identitynum,'in',18);
		if(empty($truename) || !ifsfz($identitynum))json_exit(array('flag'=>0,'msg'=>'【姓名】或【身份证】不正确'));
		if (!ifpic($_ZEAI['up2']."/".$formphoto))json_exit(array('flag'=>0,'msg'=>'请上传照片'));		
		$RZstr='photo';
		if($_SMS['rz_price']>0){
			if($_SMS['rz_price']!=$money || empty($orderid))json_exit(array('flag'=>0,'msg'=>'请求不合法'));
			rz_pay($orderid,$kind,$money,$paykind,'真人认证',$rzkind);
		}else{
			start_net_verify($truename,$identitynum,$data_mob,$RZstr,$formphoto);
		}
	break;
	case 'ajax_cert_photo_update_payok':
		$RZstr='photo';
		start_net_verify($truename,$identitynum,$data_mob,$RZstr,$formphoto);
	break;
	case 'ajax_facephoto_up_h5':
		if (ifpostpic($file['tmp_name'])){
			/*
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);
			if (!ifpic($_ZEAI['up2']."/".$_s))json_exit(array('flag'=>0,'msg'=>'H5图片无效'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
			*/
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_RZ_');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$dbname));
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));}
	break;
	case 'ajax_facephoto_up_wx':
		if (str_len($serverIds) > 15){
			/*
			$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$serverIds;
			$dbname = wx_get_uinfo_logo_tmp($url,$cook_uid);$_s = setpath_s($dbname);
			if (!ifpic($_ZEAI['up2']."/".$_s))json_exit(array('flag'=>0,'msg'=>'WX图片无效'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
			*/
			$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$serverIds;
			$dbname = wx_get_up('tmp',$url,$cook_uid,'B');
			if (!ifpic($_ZEAI['up2']."/".$dbname))json_exit(array('flag'=>0,'msg'=>'WX图片无效'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$dbname));
			
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));}
	break;
	case 'ajax_facephoto_up_app':
			/*
			$file=$_FILES['file'];
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);
			if (!ifpic($_ZEAI['up2']."/".$_s))json_exit(array('flag'=>0,'msg'=>'app图片无效'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
			*/
			$file=$_FILES['file'];
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			if (!ifpic($_ZEAI['up2']."/".$dbname))json_exit(array('flag'=>0,'msg'=>'app图片无效'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$dbname));
	break;
}


//
$a  = (empty($a))?'mob':$i;
$curpage = 'my_cert_'.$i;
if($i=='identity'){
	if(!ifmob($data_mob))json_exit(array('flag'=>0,'msg'=>'请先进行【手机认证】'));
	if($_SMS['rz_mobile3']==1 && !in_array('mob',$RZarr))json_exit(array('flag'=>0,'msg'=>'请先进行【手机认证】'));
}elseif($i=='photo'){
	//if(!in_array('identity',$RZarr))json_exit(array('flag'=>0,'msg'=>'请先进行【实名认证】'));
}
/*************Main Start*************/
$mini_title='认证中心';
$mini_backT='返回';
if($_SMS['rz_mobile3']==1 && ($i=='identity' || $i=='photo')){
	$mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$curpage.'">&#xe602;</i>'.$mini_title;
    $mini_class = 'top_mini top_miniBAI';
}else{
	if($_ZEAI['mob_mbkind']==3){
		$mini_class = 'top_mini top_mini_my_info';
	}
}
require_once ZEAI.'m1/top_mini.php';
?>
<script>
var curpage = '<?php echo $curpage;?>';
var submain = '<?php echo $curpage;?>_submain';
var upMaxMB = <?php echo $_UP['upMaxMB']; ?>;
var up2='<?php echo $_ZEAI['up2'];?>/';

/*处理jump模式，认证成功返回bug*/
var jumpi = o('my_info_certbtn').getAttribute("data");
jumpi=jumpi.replace('&i=<?php echo $i;?>','');
o('my_info_certbtn').setAttribute('data',jumpi)
</script>
<style>
.submain{background-color:#fff}
.my_cert h1{width:85%;font-weight:bold;text-align:left;font-size:30px;margin:0 auto 10px auto}
.my_cert h1 font{font-size:14px;color:#999;font-weight:normal;display:inline;margin-left:10px}
.my_cert dl{width:85%;margin:20px auto;height:50px;border-bottom:#eee 1px solid;line-height:50px;text-align:left;overflow:hidden}
.my_cert dl:first-child{margin:25px auto}
.my_cert dl dt{width:10%;float:left}
.my_cert dl dd{width:90%;float:left;position:relative}
.my_cert dl dt i{font-size:24px;display:inline-block;color:#aaa;width:100%}
.my_cert dl dd span{font-size:26px;color:#ccc;position:absolute;right:8px;top:2px}.my_cert #reset_rz{font-size:20px;right:11px;display:none}
.my_cert dl dd .input_login{width:95%;border:0;padding:0;margin:0;font-size:24px;height:30px;line-height:30px;box-sizing:border-box;margin-top:10px;background-color:#fff}
.my_cert dl dd .input_login:-webkit-autofill{-webkit-box-shadow:0 0 0px 1000px white inset !important}
.my_cert .btn{display:block;margin:40px auto;-webkit-appearance:none;border-radius:32px}
.my_cert .hui{filter:alpha(opacity=30);-moz-opacity:0.3;opacity:0.3}
.my_cert #yzmbtn{position:absolute;right:15px;top:10px;display:block;line-height:24px;border:0;padding-left:15px;color:#5FB878;background-color:#fff}
.my_cert #yzmbtn font{color:#f00}
/*identity edu*/
.my_cert .sfzbox,.my_cert .certbox{margin:25px auto;clear:both;overflow:auto}
.my_cert .sfzbox div{float:left;width:50%}
.my_cert .sfzbox div img{width:80%;height:26vw;display:block;margin:0 auto}
.my_cert .sfzbox div:first-child img{padding-left:20px}
.my_cert .sfzbox div:last-child img{padding-right:20px}
.my_cert .sfzbox div span{display:block;text-align:center;margin:10px auto;color:#999}
.my_cert .flagstr{width:45px;font-size:12px;height:16px;text-align:center;font-weight:normal;margin-left:10px;line-height:16px;display:inline-block;border-radius:2px;background-color:#f70;color:#fff}
.my_cert span.LV{background-color:#3EB94E}
.my_cert span.HUI{background-color:#aaa}
/*edu*/
.my_cert .certbox div img{width:80vw;height:50vw;display:block;margin:0 auto}
.my_cert .certbox div span{display:block;text-align:center;margin:10px auto;color:#999}
.my_cert .certbox +.btn{margin-top:0}
</style>
<?php if($_SMS['rz_mobile3']!=1 || $i!='identity' && $i!='photo'){?>
<i class='ico goback' id='ZEAIGOBACK-<?php echo $curpage;?>'>&#xe602;</i>
<?php }?>
<div class="submain my_cert" id="<?php echo $curpage;?>_submain">

<?php switch ($i){case 'mob':
/*************************************手机认证*************************************/
	$ifMobRz = (ifmob($data_mob) && strstr($data_RZ,'mob'))?true:false;
	if (ifmob($data_mob) && strstr($data_RZ,'mob')){
		$flagstr='<span class="flagstr LV">已认证</span>';
		$ifRz=true;
		$placeholder='输入其它手机重新认证';	
	}else{
		$flagstr='<span class="flagstr HUI">未认证</span>';
		$ifRz=false;
		$placeholder='请输入手机号码';	
	}
	?>
    <h1><?php echo rz_data_info($i,'title');?><?php
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
	<script>
    if (!zeai.empty(o('yzmbtn'))){
        yzmbtn.onclick = function(){
            var mob=o('mob_rz');
            if (zeai.ifmob(mob.value)){
                if (!this.hasClass('disabled')){
                    mob_rz.disabled = true;
                    yzmbtn.addClass('disabled');
                    zeai.ajax(HOST+"/m1/my_cert"+zeai.ajxext+"submitok=ajax_cert_mob_getyzm&mob="+mob.value,function(e){var rs=zeai.jsoneval(e);
                        zeai.msg(rs.msg);
                        if (rs.flag == 1){
                            yzmtimeFn(120);
                        }else{
                            mob_rz.disabled = false;
                            yzmbtn.removeClass('disabled');
                        }
                    });
                }
            }else{
                zeai.msg('请输入手机号码');
                return false;
            }
        }
    }
    </script>
    <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
    <?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
    <h5>遇到问题？请联系客服帮忙。
    <?php if (!empty($kf_tel)){?><br>电话：<a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a><?php }?>
    <?php if (!empty($kf_mob)){?><br>手机：<a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
    <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
    </h5>
<?php break;case 'photo':
/*************************************人脸识别*************************************/
	$orderid = 'RZ-'.$cook_uid.'-'.date("YmdHis");
	?>
	<style>
		.my_cert dl{width:85%;margin:10px auto;font-size:16px}
		.my_cert dl:first-child{margin:20px auto 10px auto}
		.my_cert dl dt{width:30%;}
		.my_cert dl dd{width:70%;}
	</style>
	<div class="rz_photobox">
		<form id="ZEAIFORM" action="<?php echo SELF; ?>" method="post" class="form">
			<div class="rz_photo" id="rz_photo" <?php if(is_h5app()){ echo 'onClick="rz_photo_goup()"';}?>><img src="<?php echo HOST;?>/m1/img/rz_photo.jpg" ></div>
            <h4>请自拍本人正面照片</h4>
			<dl><dt>真实姓名</dt><dd><input name="truename" type="text" class="input_login" id="truename" value="<?php echo $data_truename;?>" placeholder="请填写您的真实姓名" autocomplete="off" maxlength="8" onBlur="zeai.setScrollTop(0);"></dd></dl>
			<dl><dt>身份证号</dt><dd><input name="identitynum" type="text" id="identitynum" maxlength="18" value="<?php echo $data_identitynum;?>"  placeholder="请填写您的身份证号" autocomplete="off" class="input_login" onBlur="zeai.setScrollTop(0);" /></dd></dl>
			<dl style="border:0;"><dt>认证费用</dt><dd><?php echo ($_SMS['rz_price']>0)?'<font class="S12">￥</font><font class="S18">'.$_SMS['rz_price'].'</font>':'<font class="C090">免费</font>';?></dd></dl>
			<input type="hidden" name="formphoto" id="formphoto">
            <input type="button" value="开始认证" class="btn size4 W85_ B" id="rz_photo_btn">
		</form>
		<div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
		<h5 class="C666">
			<b>●</b><?php echo $_ZEAI['siteName'];?>作为一个真实、严肃平台、我们要求用户必须完成身份证实名认证，保证会员真实可信<br>
			<b>●</b>通过认证后，点亮【真人认证】图标，更能获得异性关注，大大提高交友成功率<br>
			<b>●</b>通过上传的照片与公安库进行校验，确保信息真实有效<br>
			<b>●</b>认证后的照片将用于个人头像显示，身份证和姓名不公开，严格保护您的隐私安全<br>
			<?php $kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');
			if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
			<center>
			<?php if (!empty($kf_tel)){?>
				<a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
			<?php }else{?>
				<?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
			<?php }?>
			</center>
		</h5>
		<br><br><br><br><br><br><br><br>
	</div>
	<script>
	var browser='<?php if(is_h5app()){ echo 'app';}else{ echo (is_weixin())?'wx':'h5';}?>',upMaxMB = <?php echo $_UP['upMaxMB']; ?>,up2='<?php echo $_ZEAI['up2'];?>/';
	<?php if(is_h5app()){ ?>
	function rz_photo_goup(){
		app_uploads({url:HOST+"/m1/my_cert"+zeai.extname+'?submitok=ajax_facephoto_up_app',num:1,sbj:1},function(e){
			var rs=zeai.jsoneval(e);
			if (rs.flag == 1){				
				var s=rs._s;
				rz_photo.html('<img src='+up2+s.replace('_s.','_b.')+'>');
				formphoto.value=s;
			}
		});
	}
	<?php }else{?>
	photoUp({
		btnobj:rz_photo,
		url:HOST+"/m1/my_cert"+zeai.extname,
		submitokBef:"ajax_facephoto_",
		_:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){
				var s=rs._s;
				rz_photo.html('<img src='+up2+rs._s.replace('_s.','_b.')+'>');
				formphoto.value=rs._s;
			}
		}
	});
	<?php }?>
	//rz_photo_btn.show();
	rz_photo_btn.onclick=function(){
		if(zeai.empty(formphoto.value)){
			zeai.msg('请自拍上传本人正面照片');
		}else{
			zeai.confirm('通过连接到公安库进行实名真人校验<br><font class="S12">（姓名+身份证号+自拍照必须同一个人才能验证通过）</font><?php if($_SMS['rz_price']>0){echo '<br>请认真核对信息，提交一次验证将扣费一次，如果填写有误或不是本人导致验证失败，认证费不退哦~~';}?>',function(){
				chkform_photo({formphoto:formphoto.value,truename:truename.value,identitynum:identitynum.value,submitok:'ajax_cert_photo_update',ZEAIGOBACK:o('ZEAIGOBACK-my_cert_photo'),money:<?php echo $_SMS['rz_price'];?>,paykind:'wxpay',kind:7,orderid:'<?php echo $orderid;?>'});
			});
		}
	}
	<?php if ($href=='h5finish'){?>
		var postjson_payok2=zeai.jsoneval(sessionStorage.postjson_payok),payid=postjson_payok2.payid;
		zeai.ajax({url:HOST+'/m1/my_cert'+zeai.ajxext+'submitok=chkh5finish&payid='+payid},function(e){var rs=zeai.jsoneval(e);
			if (rs.flag == 1){
				zeai.msg('正在真人验证',{time:8});
				zeai.ajax({url:HOST+'/m1/my_cert'+zeai.extname,data:postjson_payok2},function(e){var rs=zeai.jsoneval(e);
					zeai.msg(0);
					if (rs.flag == 1){
						zeai.msg(rs.msg,{time:3});setTimeout(function(){zeai.openurl('<?php echo mHref('cert');?>');},3000);
					}else{
						zeai.msg(rs.msg,{time:3});
					}
				});
			}else{
				zeai.msg(rs.msg,{time:3});setTimeout(function(){zeai.openurl('<?php echo mHref('cert');?>');},3000);
			}
		});
	<?php }?>
	
	</script>
<?php break;case 'identity':
/*************************************身份认证*************************************/
	//运营商
	if($_SMS['rz_mobile3']==1){
		$orderid = 'RZ-'.$cook_uid.'-'.date("YmdHis");
		?>
		<style>
            .my_cert dl{width:85%;margin:10px auto;font-size:16px}
			.my_cert dl:first-child{margin:20px auto 10px auto}
			.my_cert dl dt{width:30%;}
			.my_cert dl dd{width:70%;}
        </style>
        <div class="mob3box">
            <form id="ZEAIFORM" action="<?php echo SELF; ?>" method="post" class="form">
                <dl><dt>本人手机</dt><dd><input name="brmob" type="text" id="brmob" maxlength="11" pattern="[0-9]*" placeholder="请填写您本人手机号" autocomplete="off" disabled class="input_login" value="<?php echo $data_mob;?>"   onBlur="zeai.setScrollTop(0);" /></dd></dl>
                <dl><dt>真实姓名</dt><dd><input value="<?php echo $data_truename;?>" onBlur="zeai.setScrollTop(0);" name="truename" type="text" class="input_login" id="truename" placeholder="请填写您的真实姓名" autocomplete="off" maxlength="8"></dd></dl>
                <dl><dt>身份证号</dt><dd><input value="<?php echo $data_identitynum;?>" onBlur="zeai.setScrollTop(0);" name="identitynum" type="text" id="identitynum" maxlength="18" placeholder="请填写您的身份证号" autocomplete="off" class="input_login" /></dd></dl>
                <dl style="border:0;margin-bottom:0px"><dt>认证费用</dt><dd><?php echo ($_SMS['rz_price']>0)?'<font class="S12">￥</font><font class="S18">'.$_SMS['rz_price'].'</font>':'<font class="C090">免费</font>';?></dd></dl>
    			<input type="button" value="开始认证" class="btn size4 W85_ B" id="mob3_btn">
            </form>
            <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
            <h5 class="C666">
                <b>●</b><?php echo $_ZEAI['siteName'];?>作为一个真实、严肃平台、我们要求用户必须完成身份证实名认证，保证会员真实可信<br>
                <b>●</b>通过认证后，点亮【实名认证】图标，更能获得异性关注，大大提高交友成功率<br>
                <b>●</b>通过电信+移动+联通三网联合实名校验，确保信息真实有效<br>
                <b>●</b>您提交的信息仅用于本站诚信认证服务，不公开，将严格保护您的隐私安全<br>
                <?php $kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');
                if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
                <center>
                <?php if (!empty($kf_tel)){?>
                    <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
                <?php }else{?>
                    <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
                <?php }?>
                </center>
            </h5>
            <br><br><br>
		</div>
        <script>
		mob3_btn.onclick=function(){
			zeai.confirm('通过电信+移动+联通三网联合实名校验<br><font class="S12">（姓名+手机+身份证号必须同一个人才能验证通过）</font><?php if($_SMS['rz_price']>0){echo '<br>请认真核对信息，提交一次验证将扣费一次，如果填写有误或不是本人导致验证失败，认证费不退哦~~';}?>',function(){
				chkform_identity_mob3({truename:truename.value,identitynum:identitynum.value,submitok:'ajax_cert_identity_mob3_update',ZEAIGOBACK:o('ZEAIGOBACK-my_cert_identity'),money:<?php echo $_SMS['rz_price'];?>,paykind:'wxpay',kind:7,orderid:'<?php echo $orderid;?>'});
			});
		}
		<?php if ($href=='h5finish'){?>
			var postjson_payok2=zeai.jsoneval(sessionStorage.postjson_payok),payid=postjson_payok2.payid;
			//var redirect_url2=postjson_payok2.redirect_url;
			//redirect_url2 = redirect_url2.replace('i=identity&href=h5finish','');
			zeai.ajax({url:'m1/my_cert'+zeai.ajxext+'submitok=chkh5finish&payid='+payid},function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){
					zeai.msg('正在实名验证',{time:8});
					zeai.ajax({url:'m1/my_cert'+zeai.extname,data:postjson_payok2},function(e){var rs=zeai.jsoneval(e);
						zeai.msg(0);
						if (rs.flag == 1){
							zeai.msg(rs.msg,{time:3});setTimeout(function(){zeai.openurl('<?php echo mHref('cert');?>');},3000);
						}else{
							zeai.msg(rs.msg,{time:3});
						}
					});
				}else{
					zeai.msg(rs.msg,{time:3});setTimeout(function(){zeai.openurl('<?php echo mHref('cert');?>');},3000);
				}
			});
		<?php }?>
        </script>
		<?php
	//传统
	}else{
	$row = $db->ROW(__TBL_RZ__,"path_b,path_b2,flag","uid=".$cook_uid." AND rzid='identity'","num");
	if ($row){
		$path_b  = $row[0];
		$path_b2 = $row[1];
		$flag    = $row[2];
		$path_b_url  = (!empty($path_b ))?$_ZEAI['up2'].'/'.$path_b:'../res/sfz1.jpg';
		$path_b2_url = (!empty($path_b2 ))?$_ZEAI['up2'].'/'.$path_b2:'../res/sfz2.jpg';
		$flagstr=($flag==1)?'<span class="flagstr LV">已认证</span>':'<span class="flagstr">审核中</span>';
	}else{
		$path_b_url = '../res/sfz1.jpg';
		$path_b2_url= '../res/sfz2.jpg';
		$flag=0;
		$flagstr='<span class="flagstr HUI">未认证</span>';
	}
	?>
	<style>
		.my_cert dl{width:85%;margin:10px auto;}
		.my_cert .btn{margin-top:0}
    </style>
    <h1><?php echo rz_data_info($i,'title');?><?php if ($flagstr != ''){echo $flagstr;}?></h1>
    <form id="ZEAIFORM" action="<?php echo SELF; ?>" method="post">
    <dl><dt></dt><dd><input name="truename" type="text" class="input_login" id="truename" placeholder="请填写您的真实姓名" autocomplete="off" maxlength="8"></dd></dl>
    <dl><dt></dt><dd><input name="identitynum" type="text" id="identitynum" maxlength="18" pattern="[0-9]*" placeholder="请填写您的身份证号" autocomplete="off" class="input_login" /></dd></dl>
    <div class="sfzbox">
    	<div<?php if ($flag != 1){?> onClick="<?php if(is_h5app()){echo 'sfz_up(\'app\',this,\'sfz1\');';}else{ echo (!is_weixin())?'sfz_up(\'h5\',this,\'sfz1\');':'sfz_up(\'wx\',this,\'sfz1\');';}?>"<?php }?>><img src="<?php echo $path_b_url;?>"><?php if ($flag != 1){?><span>点击上传国徽面✚</span><?php }?></div>
    	<div<?php if ($flag != 1){?> onClick="<?php  if(is_h5app()){echo 'sfz_up(\'app\',this,\'sfz2\');';}else{ echo (!is_weixin())?'sfz_up(\'h5\',this,\'sfz2\');':'sfz_up(\'wx\',this,\'sfz2\');';}?>"<?php }?>><img src="<?php echo $path_b2_url;?>"><?php if ($flag != 1){?><span>点击上传人像面✚</span><?php }?></div>
    </div>
    <input type="button" value="保存并提交" class="btn size4 HONG W85_ B<?php echo ($flag == 1)?' HUI':'';?>"<?php if ($flag != 1){?> onClick="chkform_identity()"<?php }?>>
    <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
    <h5 class="C666">
        <b>●</b><?php echo $_ZEAI['siteName'];?>作为一个真实、严肃的婚恋平台、我们要求用户必须完成身份证实名认证，保证会员真实100%。<br>
        <b>●</b>您上传的任何身份证图片等资料，仅供审核使用且TA人无法看到，此外，我们会对图片进行安全处理，敬请放心。
        <?php $kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');
		if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
    </h5>
	<input name="submitok" type="hidden" value="ajax_cert_identity_update" />
	<input name="sfz1" id="sfz1" type="hidden" value="" />
	<input name="sfz2" id="sfz2" type="hidden" value="" />
    </form>

<?php }break;case 'weixin':
/************************************* weixin 认证*************************************/
	//$RZarr=explode(',',$data_RZ);
	if (in_array('weixin',$RZarr) && !empty($data_weixin)){
		$flagstr='<span class="flagstr LV">已认证</span>';
		$placeholder='输入其它微信号重新认证';
		$ifRz=true;
	}else{
		$flagstr='<span class="flagstr HUI">未认证</span>';
		$placeholder=' 请填写您的微信号';	
		$ifRz=false;
	}
	?>
    <h1>微信认证<?php
		if ($flagstr != ''){echo $flagstr;}
		if ($ifRz){echo '<font>'.substr($data_weixin,0,2).'***'.substr($data_weixin,-2,2).'</font>';}
		?>
	</h1>
    <form id="ZEAIFORM">
    <dl><dt><i class="ico">&#xe607;</i></dt><dd>
        <input name="weixin2" type="text" class="input_login" id="weixin2" placeholder="<?php echo $placeholder;?>" autocomplete="off" maxlength="30">
    </dd></dl>
	<input name="submitok" type="hidden" value="ajax_cert_weixin_update" />
    <input type="button" value="保存并提交" class="btn size4 HONG W85_ B" id="submitbtn_weixin" onClick="chkform_weixin()">
    
    <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
    <h5 class="C666"><b>●</b>微信认证成功后，将点亮微信图标。<br><br></h5>
    </form>
<?php break;case 'qq':
/************************************* QQ 认证*************************************/
	$RZarr=explode(',',$data_RZ);
	if (in_array('qq',$RZarr) && ifint($data_qq,'0-9','5,11')){
		$flagstr='<span class="flagstr LV">已认证</span>';
		$placeholder='输入其它QQ号重新认证';
		$ifRz=true;
	}else{
		$flagstr='<span class="flagstr HUI">未认证</span>';
		$placeholder='请填写您的QQ号';	
		$ifRz=false;
	}
	?>
    <h1>QQ认证<?php
		if ($flagstr != ''){echo $flagstr;}
		if ($ifRz){echo '<font>'.substr($data_qq,0,2).'***'.substr($data_qq,-2,2).'</font>';}
		?>
	</h1>
    <form id="ZEAIFORM">
    <dl><dt><i class="ico">&#xe630;</i></dt><dd>
        <input name="qq2" type="text" class="input_login" id="qq2" placeholder="<?php echo $placeholder;?>" autocomplete="off" maxlength="11" pattern="[0-9]*">
    </dd></dl>
    <dl><dt><i class="ico">&#xe6c3;</i></dt><dd>
        <input name="verify" type="text" id="verify" maxlength="4" pattern="[0-9]*" placeholder="填写此QQ邮箱收到的验证码" autocomplete="off" class="input_login" />
        <button type="button" id="yzmbtn">获取验证码</button>
    </dd></dl>       
    <input type="button" value="保存并提交" class="btn size4 HONG W85_ B" id="submitbtn_qq2">
    
    <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
    <h5 class="C666">
        <b>●</b>QQ认证成功后，将点亮QQ图标。<br><br>
        遇到问题？请联系客服帮忙。
        <?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
		<?php if (!empty($kf_tel)){?><br>电话：<a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a><?php }?>
        <?php if (!empty($kf_mob)){?><br>手机：<a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
        <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
    </h5>
    </form>
	<script>yzmInit({"obj":qq2,"btn":yzmbtn,"S":120,"T":'请输入您的QQ号',"url":'m1/my_cert'+zeai.extname,"form":ZEAIFORM,"goback":o('ZEAIGOBACK-my_cert_qq')});</script>
<?php break;case 'email':
/************************************* Email 认证*************************************/
	$RZarr=explode(',',$data_RZ);
	if (in_array('email',$RZarr) && ifemail($data_email)){
		$flagstr='<span class="flagstr LV">已认证</span>';
		$placeholder='输入其它邮箱重新认证';
		$ifRz=true;
	}else{
		$flagstr='<span class="flagstr HUI">未认证</span>';
		$placeholder='请填写您的邮箱';	
		$ifRz=false;
	}
	?>
    <h1>邮箱认证<?php
		if ($flagstr != ''){echo $flagstr;}
		if ($ifRz){echo '<font>'.substr($data_email,0,4).'****'.substr($data_email,-4,4).'</font>';}
		?>
	</h1>
    <form id="ZEAIFORM">
    <dl><dt><i class="ico">&#xe641;</i></dt><dd>
        <input name="email2" type="text" class="input_login" id="email2" placeholder="<?php echo $placeholder;?>" autocomplete="off" maxlength="50">
    </dd></dl>
    <dl><dt><i class="ico">&#xe6c3;</i></dt><dd>
        <input name="verify" type="text" id="verify" maxlength="4" pattern="[0-9]*" placeholder="填写此邮箱收到的验证码" autocomplete="off" class="input_login" />
        <button type="button" id="yzmbtn">获取验证码</button>
    </dd></dl>       
    <input type="button" value="保存并提交" class="btn size4 HONG W85_ B" id="submitbtn_email2">
    
    <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
    <h5 class="C666">
        <b>●</b>邮箱认证成功后，将点亮邮箱图标。<br><br>
        遇到问题？请联系客服帮忙。
        <?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
		<?php if (!empty($kf_tel)){?><br>电话：<a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a><?php }?>
        <?php if (!empty($kf_mob)){?><br>手机：<a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
        <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
    </h5>
    </form>
	<script>yzmInit({"obj":email2,"btn":yzmbtn,"S":120,"T":'请输入您的邮箱',"url":'m1/my_cert'+zeai.extname,"form":ZEAIFORM,"goback":o('ZEAIGOBACK-my_cert_email')});</script>
    
<?php break;case 'sesame':?>

	<div class='nodatatips'><i class='ico'>&#xe651;</i>开发中～～</div>

<?php break;case 'police':?>

	<div class='nodatatips'><i class='ico'>&#xe651;</i>开发中～～</div>

<?php break;}?>

<?php
if ($i == 'edu'||$i == 'car'||$i == 'house'||$i == 'love'||$i == 'pay'){
/*************************************学历，汽车，房产，认证*************************************/
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
		$path_b_url  = (!empty($path_b ))?$_ZEAI['up2'].'/'.$path_b:'../res/sfz1.jpg';
		$flagstr=($flag==1)?'<span class="flagstr LV">已认证</span>':'<span class="flagstr">审核中</span>';
	}else{
		$path_b_url = '../res/cert_'.$i.'.jpg';
		$flag=0;
		$flagstr='<span class="flagstr HUI">未认证</span>';
	}
	?>
    <h1><?php echo $objtitle;?>认证<?php if ($flagstr != ''){echo $flagstr;}?></h1>
    <div class="certbox">
    	<div<?php if ($flag != 1){?> onClick="<?php  if(is_h5app()){echo 'sfz_up(\'app\',this,\'path_b\');';}else{ echo (!is_weixin())?'sfz_up(\'h5\',this,\'path_b\');':'sfz_up(\'wx\',this,\'path_b\');';}?>"<?php }?>><img src="<?php echo $path_b_url;?>"<?php echo ($i == 'car')?' style="height:30vw;"':'';?>><?php if ($flag != 1){?><span>点击上传<?php echo $objtitle2;?>✚</span><?php }?></div>
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

<?php }?>
</div>



<script src="<?php echo HOST;?>/m1/js/my_cert.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php
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
	global $db,$cook_uid,$_ZEAI;
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
		if (ifpic($_ZEAI['up2']."/".$path_b)){
			$db->query("INSERT INTO ".__TBL_RZ__."(uid,rzid,path_b,addtime) VALUES ($cook_uid,'".$objstr."','$path_b',".ADDTIME.")");
		}
	}
	json_exit(array('flag'=>1,'msg'=>'提交成功，请等待审核'));
}
function jsonOutAndBfb($update){
	global $cook_uid;
	if($update==1)set_data_ed_bfb($cook_uid);
	//json_exit(array('flag'=>1,'msg'=>'保存成功'));
}
//rz_pay($orderid,$kind,$money,$paykind,'实名认证');
function rz_pay($orderid,$kind,$money,$paykind,$orderid_title,$rzkind) {
	global $_ZEAI,$_PAY,$db,$cook_uid,$cook_openid;
	$orderid = 'RZ-'.$cook_uid.'-'.date("YmdHis");
	if ($kind != 7)json_exit(array('flag'=>0,'msg'=>'kind!=7，请联系管理员'));//kind=7认证
	$paymoney = abs(round($money,2));
	$return_url = mHref('cert');
	$jump_url   = $return_url;
	if(str_len($orderid) <10 )json_exit(array('flag'=>0,'msg'=>'订单号异常~'));
	if ($paykind=='wxpay' || $paykind=='alipay'){
		$rowpay = $db->ROW(__TBL_PAY__,"flag","orderid='$orderid'",'num');
		//if ($rowpay){
		//	if ($rowpay[0] == 1)json_exit(array('flag'=>0,'msg'=>'该订单已完成，请返回重新操作'));	
		//}else{
			$money_list_id = intval($grade);
			$paytime       = 0;
			$db->query("INSERT INTO ".__TBL_PAY__."(orderid,kind,uid,title,money,paymoney,addtime,money_list_id,paytime) VALUES ('$orderid',$kind,$cook_uid,'$orderid_title',$money,$paymoney,".ADDTIME.",$money_list_id,$paytime)");
			$payid = $db->insert_id();
		//}
		/*====================测试支付会员ID=====================*/
		//if ($cook_uid == 8){$paymoney = 0.01;}
	}
	if ($paykind=='wxpay' ){
		$total_fee = $paymoney*100;//分
		include_once(ZEAI."api/weixin/pay/WxPayPubHelper/WxPayPubHelper.php");
		//微信内部
		if (is_weixin()){
			if(str_len($cook_openid) < 10)json_exit(array('flag'=>0,'msg'=>'请点击【我的】重新登录'));
			$jsApi = new JsApi_pub();	
			$unifiedOrder = new UnifiedOrder_pub();	
			$unifiedOrder->setParameter("openid",$cook_openid);
			$unifiedOrder->setParameter("out_trade_no",$orderid);
			$unifiedOrder->setParameter("total_fee",$total_fee);
			$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);
			$unifiedOrder->setParameter("trade_type","JSAPI");
			$unifiedOrder->setParameter("body",$orderid_title);
			$unifiedOrder->setParameter("attach",$payid);
			$prepay_id = $unifiedOrder->getPrepayId();
			$jsApi->setPrepayId($prepay_id);
			$jsApiParameters = $jsApi->getParameters();
			$jsApiParameters = json_decode($jsApiParameters,true);
			$db->query("UPDATE ".__TBL_PAY__." SET bz='手机微信支付(内部JSAPI)' WHERE id=".$payid);
			json_exit(array('flag'=>1,'jump_url'=>$jump_url,'redirect_url'=>$return_url,'trade_type'=>'JSAPI','msg'=>'jsapi调起支付','jsApiParameters'=>$jsApiParameters));
		//H5外部
		}else{
			if(!is_mobile()){exit("请用手机操作");}
			require_once ZEAI.'api/weixin/pay/h5pay_func.php';
			$H5PAY = new www_zeai_cn_h5pay_class();
			$pay_data=array(
				'trade_type'=>"MWEB",
				'appid'=>APPID_,
				'mch_id'=>MCHID_,
				'nonce_str'=>$H5PAY->get_rand_str(32),
				'out_trade_no'=>$orderid,
				'body'=>$orderid_title,
				'total_fee'=>$total_fee,
				'notify_url'=>NOTIFY_URL_,
				'spbill_create_ip'=>$H5PAY->siteip()
			);
			$pay_data['sign'] = $H5PAY->MakeSign($pay_data);
			$pay_vars     = $H5PAY->ToXml($pay_data);
			$re_data      = $H5PAY->curl_post_ssl($pay_vars);
			$wxpay_arr    = $H5PAY->FromXml($re_data);
			if($wxpay_arr['return_code']=="SUCCESS" && $wxpay_arr['result_code']=="SUCCESS"){
				$pay_url  = $wxpay_arr['mweb_url'];
				$pay_url .= '&redirect_url='.urlencode($return_url.'&i='.$rzkind.'&href=h5finish');//成功跳转url
				$db->query("UPDATE ".__TBL_PAY__." SET bz='手机微信支付(外部WAP/H5)' WHERE id=".$payid);
				json_exit(array('flag'=>1,'trade_type'=>'H5','msg'=>'H5调起支付','payid'=>$payid,'redirect_url'=>$pay_url));
			}else{
				json_exit(array('flag'=>0,'trade_type'=>'H5','msg'=>'H5调起支付失败【'.$wxpay_arr['err_code'].'】','redirect_url'=>$redirect_url));
			}
		}
	}
}
function start_net_verify($truename,$identitynum,$data_mob,$RZstr,$formphoto='') {
	global $db,$RZarr,$data_RZ,$cook_uid,$data_openid,$data_subscribe;
	require_once ZEAI.'api/zeai_RZ.php';
	if($RZstr=='identity'){
		$retarr = Zeai_RZ_mob3($truename,$identitynum,$data_mob);
		$orderid_title='实名认证';
		start_net_verifyUpdate($retarr,$truename,$identitynum,$data_mob,$RZstr,$formphoto,$orderid_title);
	}elseif($RZstr=='photo'){
		if(@file_exists(ZEAI.'up'.DIRECTORY_SEPARATOR.$formphoto)){
			$retarr = Zeai_RZ_face_id_card($truename,$identitynum,ZEAI.'up'.DIRECTORY_SEPARATOR.smb($formphoto,'b'));
			$orderid_title='真人认证';
			start_net_verifyUpdate($retarr,$truename,$identitynum,$data_mob,$RZstr,$formphoto,$orderid_title);
		}else{
			json_exit(array('flag'=>0,'msg'=>'请检查照片格式是否为jpg/png/gif格式'));
		}
	}
}
function start_net_verifyUpdate($retarr,$truename,$identitynum,$data_mob,$RZstr,$formphoto,$orderid_title) {
	global $db,$RZarr,$data_RZ,$cook_uid,$data_openid,$data_subscribe,$data_tguid,$_SMS;
	if($retarr['flag']==1){
		$SQL="";
		if (empty($RZarr) || count($RZarr)<=0 || empty($data_RZ)){
			$SQL .= ",RZ='$RZstr'";
		}else{
			if (!in_array($RZstr,$RZarr)){
				$RZarr[]=$RZstr;
				$list = implode(',',$RZarr);
				$SQL  .= ",RZ='$list'";
			}
		}
		if($RZstr=='photo' && @file_exists(ZEAI.'up'.DIRECTORY_SEPARATOR.$formphoto)){
			u_pic_reTmpDir_send($formphoto,'rz');
			$formphoto = str_replace('tmp','rz',$formphoto);
			$row = $db->ROW(__TBL_RZ__,"path_b","uid=".$cook_uid." AND rzid='photo'","name");
			if (!$row){
				$db->query("INSERT INTO ".__TBL_RZ__."(uid,rzid,flag,path_b,addtime) VALUES ($cook_uid,'photo',1,'$formphoto',".ADDTIME.")");	
			}else{
				if(empty($row['path_b'])){
					$sql = ",path_b='$formphoto'";
				}else{
					$sql = ",path_b2='$formphoto'";
				}
				$db->query("UPDATE ".__TBL_RZ__." SET flag=1".$sql." WHERE uid=".$cook_uid." AND rzid='photo'");
			}
		}
		$birthday = getSFZbirthday($identitynum);
		if(!empty($birthday))$SQL  .= ",birthday='$birthday'";
		if(!empty($truename))$SQL  .= ",truename='$truename'";
		if(ifsfz($identitynum))$SQL  .= ",identitynum='$identitynum'";
		if(!empty($SQL))$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.$SQL." WHERE id=".$cook_uid);
		//站内通知
		$C = $truename.'您好，恭喜你'.$orderid_title.'成功!';
		$db->SendTip($cook_uid,'恭喜你，'.$orderid_title.'成功!',dataIO($C,'in'),'sys');
		//微信模版通知
		if (!empty($data_openid) && $data_subscribe==1){
			$keyword1 = '恭喜你'.$orderid_title.'成功!';
			$keyword3 = urlencode($_ZEAI['siteName']);
			//$url      = urlencode(mHref('my'));
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		if(ifint($data_tguid))TG($data_tguid,$cook_uid,'rz',$_SMS['rz_price']);
		json_exit(array('flag'=>1,'msg'=>'恭喜你认证成功'));
	}else{
		if($RZstr=='photo' && @file_exists(ZEAI.'up'.DIRECTORY_SEPARATOR.$formphoto)){
			u_pic_reTmpDir_send($formphoto,'rz');
			$formphoto = str_replace('tmp','rz',$formphoto);
			$row = $db->ROW(__TBL_RZ__,"path_b","uid=".$cook_uid." AND rzid='photo'","name");
			if (!$row){
				$db->query("INSERT INTO ".__TBL_RZ__."(uid,rzid,flag,path_b,addtime) VALUES ($cook_uid,'photo',0,'$formphoto',".ADDTIME.")");	
			}else{
				if(empty($row['path_b'])){
					$sql = ",path_b='$formphoto'";
				}else{
					$sql = ",path_b2='$formphoto'";
				}
				$db->query("UPDATE ".__TBL_RZ__." SET flag=0".$sql." WHERE uid=".$cook_uid." AND rzid='photo'");
			}
		}
		json_exit(array('flag'=>0,'msg'=>'最终认证结果：'.$retarr['msg']));
	}
}
?>
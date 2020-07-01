<?php
require_once '../sub/init.php';
if (@ini_get('session.auto_start') == 0)session_start();
!function_exists('zeai_alone') && exit('forbidden');
$currfields = "";
if(!ifint($cook_tg_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来','jumpurl'=>HOST.'/m1/tg_my.php'));
$currfields = "nickname,photo_s,areaid,areatitle,kind,RZ,mob,job,email,weixin,qq,company_apply_flag,title,tel,address,worktime,content,bank_name,bank_name_kaihu,bank_truename,bank_card,alipay_truename,alipay_username";
require_once 'tg_chkuser.php';
$data_photo_s=$row['photo_s'];
$data_kind=$row['kind'];
$data_areaid     = $row['areaid'];
$data_areatitle  = $row['areatitle'];
$data_RZ  = $row['RZ'];
$data_mob = $row['mob'];
$data_job = dataIO($row['job'],'out');
$data_email = dataIO($row['email'],'out');
$data_weixin= dataIO($row['weixin'],'out');
$data_qq    = dataIO($row['qq'],'out');
$data_title = dataIO($row['title'],'out');
$data_company_apply_flag= $row['company_apply_flag'];
$data_company_apply_kind= $row['company_apply_kind'];
$data_tel = dataIO($row['tel'],'out');
$data_address = dataIO($row['address'],'out');
$data_worktime = dataIO($row['worktime'],'out');
$data_content = dataIO($row['content'],'out');
$data_nickname = dataIO($row['nickname'],'out');
//
$data_bank_name       = dataIO($row['bank_name'],'out');
$data_bank_name_kaihu = dataIO($row['bank_name_kaihu'],'out');
$data_bank_truename   = dataIO($row['bank_truename'],'out');
$data_bank_card       = dataIO($row['bank_card'],'out');
$data_alipay_truename = dataIO($row['alipay_truename'],'out');
$data_alipay_username = dataIO($row['alipay_username'],'out');

switch ($data_kind) {
	case 1:$kind_str='个人';break;
	case 2:$kind_str='商户';break;
	case 3:$kind_str='机构';break;
}

require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
$photo_m_str = (!empty($data_photo_s ))?'<img src="'.$_ZEAI['up2'].'/'.smb($data_photo_s,'m').'"><span>上传头像</span>':'<i class="ico">&#xe620;</i><h5>上传头像</h5>';



switch ($submitok) {
	case 'ajax_photo_s_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tg',$file['tmp_name'],$cook_tg_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$db->query("UPDATE ".__TBL_TG_USER__." SET photo_s='$_s' WHERE id=".$cook_tg_uid);
			$path_b = smb($data_photo_s,'b');
			if(!empty($data_photo_s))@up_send_userdel($data_photo_s.'|'.smb($data_photo_s,'m').'|'.$path_b.'|'.smb($data_photo_s,'blur'),'tg_userdelpic');
			json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>smb($newphoto_s,'m')));
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));}
	break;
	case 'ajax_photo_s_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_uinfo_logo($url,$cook_tg_uid);$_s = setpath_s($dbname);
				}
				$newphoto_s = $_ZEAI['up2']."/".$_s;
				if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				$db->query("UPDATE ".__TBL_TG_USER__." SET photo_s='$_s' WHERE id=".$cook_tg_uid);
				$path_b = smb($data_photo_s,'b');
				if(!empty($data_photo_s))@up_send_userdel($data_photo_s.'|'.smb($data_photo_s,'m').'|'.$path_b.'|'.smb($data_photo_s,'blur'),'tg_userdelpic');
				json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>smb($newphoto_s,'m')));
			}
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));}
	break;
	case 'ajax_modupdate23':
		$title      = dataIO($title,'in',200);
		$content    = dataIO($content,'in',50000);
		$tel        = dataIO($tel,'in',100);
		$worktime   = dataIO($worktime,'in',200);
		$address    = dataIO($address,'in',100);
		//
		$job        = dataIO($job,'in',200);
		$areaid     = dataIO($areaid,'in',100);
		$areatitle  = dataIO($areatitle,'in',100);
		$qq         = dataIO($qq,'in',15);
		$weixin     = dataIO($weixin,'in',50);
		$email      = dataIO($email,'in',50);
		//
		$setsql  = "title='$title',content='$content',tel='$tel',worktime='$worktime',address='$address',areaid='$areaid',areatitle='$areatitle'";
		$setsql .= ",weixin='$weixin',qq='$qq',job='$job',email='$email'";
		$db->query("UPDATE ".__TBL_TG_USER__." SET ".$setsql." WHERE id=".$cook_tg_uid);
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case 'ajax_modupdate':
		$job        = dataIO($job,'in',200);
		$areaid     = dataIO($areaid,'in',100);
		$areatitle  = dataIO($areatitle,'in',100);
		$qq         = dataIO($qq,'in',15);
		$weixin     = dataIO($weixin,'in',50);
		$nickname   = dataIO($nickname,'in',30);
		$email      = dataIO($email,'in',50);
		$mob        = trimhtml($mob);
		//
		$setsql = "nickname='$nickname',weixin='$weixin',qq='$qq',job='$job',areaid='$areaid',areatitle='$areatitle',email='$email'";
		if ($mob != $mob_old && ifmob($mob)){
			$row = $db->ROW(__TBL_TG_USER__,'id',"mob='$mob' AND mob<>'' AND FIND_IN_SET('mob',RZ)","num");
			if($row){json_exit(array('flag'=>0,'msg'=>'“手机”已被【ID:'.$row[0].'】占用'));}else{$setsql .= ",mob='$mob'";}
		}else{
			$setsql .= ",mob='$mob'";
		}
		$db->query("UPDATE ".__TBL_TG_USER__." SET ".$setsql." WHERE id=".$cook_tg_uid);
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case 'ajax_apply':
		switch ($i) {
			case 2:$i_str='商户';break;
			case 3:$i_str='机构';break;
		}
		$company_apply_kind=$i;

		if (str_len($title) > 20 || str_len($title)<3)json_exit(array('flag'=>0,'msg'=>"请输入".$i_str."名称"));
		if (str_len($tel) > 20 || str_len($tel)<8)json_exit(array('flag'=>0,'msg'=>'请输入联系电话'));
		if($i==2){
			if (str_len($worktime) > 200 || str_len($worktime)<2)json_exit(array('flag'=>0,'msg'=>'请输入营业时间'));
		}
		if (empty($areaid))json_exit(array('flag'=>0,'msg'=>'请选择地区'));
		if (str_len($content) > 2000 || str_len($content)<5)json_exit(array('flag'=>0,'msg'=>"请输入".$i_str."简介"));

		$title   = dataIO($title,'in',200);
		$content = dataIO($content,'in',500);
		$tel     = dataIO($tel,'in',100);
		$worktime= dataIO($worktime,'in',200);
		$address = dataIO($address,'in',100);
		$areaid     = dataIO($areaid,'in',100);
		$areatitle  = dataIO($areatitle,'in',100);
		$company_apply_flag=1;
		if($TG_set['company_ifsh']==1){
			$company_apply_flag=2;
		}
		$setsql = "company_apply_flag=$company_apply_flag,company_apply_kind=$company_apply_kind,title='$title',content='$content',tel='$tel',worktime='$worktime',address='$address',areaid='$areaid',areatitle='$areatitle'";
		$db->query("UPDATE ".__TBL_TG_USER__." SET ".$setsql." WHERE id=".$cook_tg_uid);
		json_exit(array('flag'=>1,'msg'=>'申请成功，请等待我们审核'));
	break;
	case 'tg_my_set_bank_update':
		$bank_name       = dataIO($bank_name,'in',100);
		$bank_name_kaihu = dataIO($bank_name_kaihu,'in',200);
		$bank_truename   = dataIO($bank_truename,'in',50);
		$bank_card       = dataIO($bank_card,'in',50);
		$alipay_truename = dataIO($alipay_truename,'in',50);
		$alipay_username = dataIO($alipay_username,'in',100);
		$setsql = "bank_name='$bank_name',bank_name_kaihu='$bank_name_kaihu',bank_truename='$bank_truename',bank_card='$bank_card',alipay_truename='$alipay_truename',alipay_username='$alipay_username'";
		$db->query("UPDATE ".__TBL_TG_USER__." SET ".$setsql." WHERE id=".$cook_tg_uid);
		json_exit(array('flag'=>1,'msg'=>'设置成功'));
	break;
	case 'tg_my_set_modpass_update':
		if (str_len($form_password1)<6 || str_len($form_password1)>20)json_exit(array('flag'=>0,'msg'=>'“新密码”请在20字节以内。'));
		if (str_len($form_password2)<6 || str_len($form_password2)>20)json_exit(array('flag'=>0,'msg'=>'“确认新密码”请在20字节以内。'));
		if ($form_password1 <> $form_password2)json_exit(array('flag'=>0,'msg'=>'两次密码输入不一样，请重试！'));
		$password = trimm($form_password1);
		$password = md5($password);
		$old_password = md5($old_password);
		$rt = $db->query("SELECT id FROM ".__TBL_TG_USER__." WHERE id=".$cook_tg_uid." AND pwd='$old_password'");
		if(!$db->num_rows($rt))json_exit(array('flag'=>0,'msg'=>'旧密码验证错误，提交失败'));
		$db->query("UPDATE ".__TBL_TG_USER__." SET pwd='$password' WHERE id=".$cook_tg_uid);
		setcookie("cook_tg_pwd",$password,time()+720000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'新密码设置成功'));
	break;
	case 'tg_my_set_mobcert_update':
		if (!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请输入正确的手机号码'));
		$row = $db->ROW(__TBL_TG_USER__,'id',"mob='".$mob."' AND FIND_IN_SET('mob',RZ) ");
		if($row)json_exit(array('flag'=>0,'msg'=>'此手机号码已被注册,请重新输入'));
		//验证码处理
		$verify = intval($verify);
		if (empty($_SESSION['Zeai_cn__verify'])){
			json_exit(array('flag'=>0,'msg'=>'短信验证码错误，请重新获取'));
		}else{
			if ($_SESSION['Zeai_cn__verify'] != $verify){
				json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
			}
			if ($_SESSION['Zeai_cn__mob'] != $mob && ifmob($mob)){
				unset($_SESSION["Zeai_cn__verify"]);
				unset($_SESSION["Zeai_cn__mob"]);
				json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
			}
		}
		$db->query("UPDATE ".__TBL_TG_USER__." SET mob='$mob',RZ='mob' WHERE id=".$cook_tg_uid);
		setcookie("cook_tg_mob",$mob,time()+720000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'新手机认证成功'));
	break;
}
$RZarr = explode(',',$data_RZ);

$rz_str = (@in_array('mob',$RZarr) && ifmob($data_mob))?'<div style="border-bottom:#dedede 1px solid;">'.$data_mob." <i class='ico S18' style='color:#45C01A'>&#xe60d;</i> <font style='color:#45C01A' class='S14'>已认证</font></div>":'';
?>
<style>
.submainX{width:100%;max-width:640px;bottom:0;overflow:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;box-sizing:border-box;position:absolute;top:44px;text-align:left}
.submainX{background-color:#fff;padding:30px;line-height:200%;font-size:16px}
.submainX img{max-width:100%}
.submainX dl{box-sizing:border-box;clear:both;overflow:auto;padding:5px 0}
.submainX dl dt,.submainX dl dd{font-size:16px;line-height:50px}
.submainX dl dt{width:15%;float:left}
.submainX dl dd{width:85%;float:right}
.submainX .input{width:100%;border:0;border-bottom:#dedede 1px solid;font-size:16px}
.submainX .icoadd{border:#dedede 2px solid;height:100px;margin:0 auto;position:relative;text-align:center;border-radius:5px;overflow:hidden}
.submainX .icoadd,.icoadd img{width:100px;height:100px;display:block;object-fit:cover;-webkit-object-fit:cover}
.submainX .icoadd i.ico{line-height:60px;font-size:50px;text-align:center;color:#aaa;border-radius:2px;margin-top:10px}
.submainX .icoadd h5{position:absolute;width:100%;bottom:8px;text-align:center;color:#999}
.submainX .icoadd span{position:absolute;width:100%;left:0;bottom:0;text-align:center;color:#fff;background-color:rgba(0,0,0,0.6);font-size:14px}
.submainX .size4{width:80%;left:10%;position:fixed;bottom:10px;display:block;z-index:8}
.submainX .size4:hover{background-color:#F7564D;filter:alpha(opacity=100);-moz-opacity:1;opacity:1}
</style>
<?php if ($a == 'data1'){
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-tg_my_info">&#xe602;</i>修改资料';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>

    <link href="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo HOST;?>/res/iscroll.js"></script>
    <script src="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.js"></script>
    <script src="<?php echo HOST;?>/res/zeai_ios_select/separate/select_mini.js"></script>
    <script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
    <style>
    ::-webkit-input-placeholder{font-size:14px}
    .ios-select-widget-box.olay {background-color:rgba(0,0,0,.4)}
    .ios-select-widget-box.olay > div {width:100%;top:auto;bottom:0;left:0;border-radius:0;padding-bottom:58px}
    </style>
    <div class="submainX">
        <div class="icoadd" id="photo_s"><?php echo $photo_m_str;?></div>
        <form id="WWW_z_e_A__i_Cn">
        <dl><dt>手机</dt><dd>
        <?php
		if(in_array('mob',$RZarr) && ifmob($data_mob)){
			echo $rz_str;
		}else{
		?>
        <input name="mob" type="text" class="input " placeholder="请输入手机号码" value="<?php echo $data_mob;?>" autocomplete="off" maxlength="11"<?php echo (in_array('mob',$RZarr) && ifmob($data_mob))?' readonly':'';?>>		<?php }?>
        </dd></dl>
        <dl><dt>昵称</dt><dd><input name="nickname" type="text" class="input " placeholder="请输入昵称" autocomplete="off" maxlength="20" value="<?php echo $data_nickname;?>" onBlur="rettop();" /></dd></dl>
        <dl><dt>微信</dt><dd><input name="weixin" type="text" class="input " placeholder="请输入微信号码" autocomplete="off" maxlength="20" value="<?php echo $data_weixin;?>" onBlur="rettop();" /></dd></dl>
        <dl><dt>QQ</dt><dd><input name="qq" type="text" class="input " placeholder="请输入QQ号码" id="form_password2" autocomplete="off" maxlength="12" value="<?php echo $data_qq;?>" onBlur="rettop();"></dd></dl>
        <dl><dt>职位</dt><dd><input name="job" type="text" class="input " placeholder="请输入职位或身份介绍" autocomplete="off" maxlength="100" value="<?php echo $data_job;?>" onBlur="rettop();"></dd></dl>
        <dl><dt>地区</dt><dd><input id="areaid_" type="text" class="input " data="<?php echo $data_areaid;?>" value="<?php echo $data_areatitle;?>" autocomplete="off" readonly></dd></dl>
        <dl><dt>邮箱</dt><dd><input name="email" type="text" class="input "  placeholder="请输入邮箱" autocomplete="off" maxlength="100" value="<?php echo $data_email;?>" onBlur="rettop();"></dd></dl>
        <br><br><br><br>
        <input name="mob_old" type="hidden" value="<?php echo $data_mob; ?>" />
        <input type="hidden" name="areaid" id="areaid" value="<?php echo $data_areaid;?>">
        <input type="hidden" name="areatitle" id="areatitle" value="<?php echo $data_areatitle;?>">
		
        </form>
    </div>
    <button type="button" class="btn size4 HONG4 B yuan" onClick="my_info_save();" style="width:80%;left:10%;position:fixed;bottom:10px;display:block;z-index:8">保存</button>
    <script>
	function rettop(){zeai.setScrollTop(0);}
	Sbindbox='';
	photoUp({
		btnobj:photo_s,
		url:"tg_my_info"+zeai.extname,
		submitokBef:"ajax_photo_s_",
		_:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){
				photo_s.html('<img src='+rs.photo_s+'><span>重新上传</span>');
				photo_s_id.src=rs.photo_s;
			}
		}
	});
	areaid_.onclick=function(){
		var obj = o('areaid_'),defV = obj.getAttribute("data");
		ios_select_area('地区',areaARR1,areaARR2,areaARR3,defV,function(obj1,obj2,obj3){
			o('areaid').value    = obj1.i + ',' + obj2.i + ',' + obj3.i;
			o('areatitle').value = obj1.v + ' ' + obj2.v + ' ' + obj3.v;
			obj.value=o('areatitle').value;
		},',');
	}
	function my_info_save(){
		zeai.ajax({url:'tg_my_info'+zeai.ajxext+'submitok=ajax_modupdate',form:WWW_z_e_A__i_Cn},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg,{time:2});
			if(rs.flag==1){
				setTimeout(function(){o('ZEAIGOBACK-tg_my_info').click();},1000);
			}
		});
	}
    </script>   
<?php }elseif($a == 'data2' || $a == 'data3'){
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-tg_my_info">&#xe602;</i>修改'.$kind_str.'信息';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>

    <link href="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo HOST;?>/res/iscroll.js"></script>
    <script src="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.js"></script>
    <script src="<?php echo HOST;?>/res/zeai_ios_select/separate/select_mini.js"></script>
    <script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
    <style>
	.submainX dl dt{width:25%;float:left}
	.submainX dl dd{width:75%;float:right}
    ::-webkit-input-placeholder{font-size:14px}
    .ios-select-widget-box.olay {background-color:rgba(0,0,0,.4)}
    .ios-select-widget-box.olay > div {width:100%;top:auto;bottom:0;left:0;border-radius:0;padding-bottom:58px}
    </style>
    <div class="submainX">
        <div class="icoadd" id="photo_s"><?php echo $photo_m_str;?></div>
        
        
        <form id="WWW_z_e_A__i_Cn">
        
        <div id="kind23box">
        	<div class="linebox" style="margin-top:5px"><div class="line"></div><div class="title S18 BAI B" style="color:#F7564D"><?php echo $kind_str;?>信息</div></div>
            
            <dl><dt><?php echo $kind_str;?>名称</dt><dd><input name="title" type="text" class="input " placeholder="请输入<?php echo $kind_str;?>名称" autocomplete="off" maxlength="20" value="<?php echo $data_title;?>" /></dd></dl>
            <dl><dt>联系电话</dt><dd><input name="tel" type="text" class="input " placeholder="请输入联系电话" id="form_password2" autocomplete="off" maxlength="12" value="<?php echo $data_tel;?>" onBlur="rettop();"></dd></dl>
            <?php if ($data_kind==2){?>
            <dl><dt>营业时间</dt><dd><input name="worktime" type="text" class="input " placeholder="例：7*24小时全天营业" autocomplete="off" maxlength="100" value="<?php echo $data_worktime;?>" onBlur="rettop();"></dd></dl>
            <?php }else{ ?>
            <input type="hidden" name="worktime" id="worktime" value="<?php echo $data_worktime;?>">
            <?php }?>
            <dl><dt>详细地址</dt><dd><input name="address" type="text" class="input "  placeholder="请输入详细地址" autocomplete="off" maxlength="100" value="<?php echo $data_address;?>" onBlur="rettop();"></dd></dl>
            <dl><dt>所在地区</dt><dd><input id="areaid_" type="text" class="input " data="<?php echo $data_areaid;?>" value="<?php echo $data_areatitle;?>" autocomplete="off" readonly></dd></dl>
            <div class="linebox" style="margin-top:5px"><div class="line"></div><div class="title S18 BAI B" style="color:#F7564D"><?php echo $kind_str;?>简介</div></div>
            <textarea name="content" id="content" class="textarea" style="width:100%;height:100px;padding:10px" placeholder="请填写<?php echo $k_str;?>简介，1000个字以内" onBlur="rettop();"><?php echo $data_content;?></textarea>
		</div>
		
        <div class="linebox" style="margin-top:5px"><div class="line"></div><div class="title S18 BAI B" style="color:#F7564D">负责人信息</div></div>
        <div id="kind1box">
            <dl><dt>微信</dt><dd><input name="weixin" type="text" class="input " placeholder="请输入微信号码" autocomplete="off" maxlength="20" value="<?php echo $data_weixin;?>" onBlur="rettop();" /></dd></dl>
            <dl><dt>QQ</dt><dd><input name="qq" type="text" class="input " placeholder="请输入QQ号码" id="form_password2" autocomplete="off" maxlength="12" value="<?php echo $data_qq;?>" onBlur="rettop();"></dd></dl>
            <dl><dt>职位</dt><dd><input name="job" type="text" class="input " placeholder="请输入职位或身份介绍" autocomplete="off" maxlength="100" value="<?php echo $data_job;?>" onBlur="rettop();"></dd></dl>
            <dl><dt>邮箱</dt><dd><input name="email" type="text" class="input "  placeholder="请输入邮箱" autocomplete="off" maxlength="100" value="<?php echo $data_email;?>" onBlur="rettop();"></dd></dl>
        </div>
        
        
        
        <br><br><br><br>
        <input name="mob_old" type="hidden" value="<?php echo $data_mob; ?>" />
        <input type="hidden" name="areaid" id="areaid" value="<?php echo $data_areaid;?>">
        <input type="hidden" name="areatitle" id="areatitle" value="<?php echo $data_areatitle;?>">
		
        </form>
    </div>
    <button type="button" class="btn size4 HONG4 B yuan" onClick="my_info_save();" style="width:80%;left:10%;position:fixed;bottom:10px;display:block;z-index:8">保存</button>
    <script>
	function rettop(){zeai.setScrollTop(0);}
	Sbindbox='';
	photoUp({
		btnobj:photo_s,
		url:"tg_my_info"+zeai.extname,
		submitokBef:"ajax_photo_s_",
		_:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){
				photo_s.html('<img src='+rs.photo_s+'><span>重新上传</span>');
				photo_s_id.src=rs.photo_s;
			}
		}
	});
	areaid_.onclick=function(){
		var obj = o('areaid_'),defV = obj.getAttribute("data");
		ios_select_area('地区',areaARR1,areaARR2,areaARR3,defV,function(obj1,obj2,obj3){
			o('areaid').value    = obj1.i + ',' + obj2.i + ',' + obj3.i;
			o('areatitle').value = obj1.v + ' ' + obj2.v + ' ' + obj3.v;
			obj.value=o('areatitle').value;
		},',');
	}
	function my_info_save(){
		zeai.ajax({url:'tg_my_info'+zeai.ajxext+'submitok=ajax_modupdate23',form:WWW_z_e_A__i_Cn},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg,{time:2});
			if(rs.flag==1){
				setTimeout(function(){o('ZEAIGOBACK-tg_my_info').click();},1000);
			}
		});
	}
    </script>   
    
<?php }elseif($a == 'apply'){
	$k_str=($i==2)?'商户':'机构';
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-tg_my_info">&#xe602;</i>'.$k_str.'合作';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';
	?>
    <div class="submainX">
		<?php 
        if($data_company_apply_flag==2){
			echo '<div style="text-align:center"><br><br><i class="ico" style="font-size:50px;color:#F7564D">&#xe634;</i><br>【'.$data_title.'】<br>您的申请正在审核中，请耐心等待</div>';
			exit;
        }elseif($data_company_apply_flag==1){
			$k_applay_str=($data_company_apply_kind==2)?'商户':'机构';
			echo '<div style="text-align:center"><br><br><i class="ico" style="font-size:50px;color:#5CC66D">&#xe60d;</i><br>【'.$data_title.'】<br>您已经参与'.$k_applay_str.'合作，请不要重复申请</div>';
			exit;
		}
        ?>
        <link href="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo HOST;?>/res/iscroll.js"></script>
        <script src="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.js"></script>
        <script src="<?php echo HOST;?>/res/zeai_ios_select/separate/select_mini.js"></script>
        <script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
        <style>
		.submainX dl dt{width:25%;float:left}
		.submainX dl dd{width:75%;float:right}
        ::-webkit-input-placeholder{font-size:14px}
        .ios-select-widget-box.olay {background-color:rgba(0,0,0,.4)}
        .ios-select-widget-box.olay > div {width:100%;top:auto;bottom:0;left:0;border-radius:0;padding-bottom:58px}
        </style>

        <div class="icoadd" id="photo_s"><?php echo $photo_m_str;?></div>
        <form id="WWW_z_e_A__i_Cn">
        
        <dl><dt><?php echo $k_str;?>名称</dt><dd><input name="title" type="text" class="input " placeholder="请输入<?php echo $k_str;?>名称" autocomplete="off" maxlength="20" value="<?php echo $data_title;?>" /></dd></dl>
        <dl><dt>联系电话</dt><dd><input name="tel" type="text" class="input " placeholder="请输入联系电话" id="form_password2" autocomplete="off" maxlength="12" value="<?php echo $data_tel;?>"></dd></dl>
        
        <?php if ($i==2){?>
        <dl><dt>营业时间</dt><dd><input name="worktime" type="text" class="input " placeholder="例：7*24小时全天营业" autocomplete="off" maxlength="100" value="<?php echo $data_worktime;?>"></dd></dl>
        <?php }else{ ?>
        <input type="hidden" name="worktime" id="worktime" value="<?php echo $data_worktime;?>">
        <?php }?>
        
        <dl><dt>详细地址</dt><dd><input name="address" type="text" class="input "  placeholder="请输入详细地址" autocomplete="off" maxlength="100" value="<?php echo $data_address;?>"></dd></dl>
        <dl><dt>所在地区</dt><dd><input id="areaid_" type="text" class="input " data="<?php echo $data_areaid;?>" value="<?php echo $data_areatitle;?>" autocomplete="off" readonly></dd></dl>
        
        <div class="linebox" style="margin-top:5px"><div class="line"></div><div class="title S18 BAI B" style="color:#F7564D"><?php echo $k_str;?>简介</div></div>
        <textarea name="content" id="content" class="textarea" style="width:100%;height:100px" placeholder="请填写<?php echo $k_str;?>简介，1000个字以内"><?php echo $data_content;?></textarea>
        
        <br><br><br><br>
        <input type="hidden" name="areaid" id="areaid" value="<?php echo $data_areaid;?>">
        <input type="hidden" name="areatitle" id="areatitle" value="<?php echo $data_areatitle;?>">
        <input type="hidden" name="i" id="areatitle" value="<?php echo $i;?>">
		<button type="button" class="btn size4 HONG4 B yuan" onClick="my_info_save();">提交申请</button>
        </form>
		<script>
		Sbindbox='';
		photoUp({
			btnobj:photo_s,
			url:"tg_my_info"+zeai.extname,
			submitokBef:"ajax_photo_s_",
			_:function(rs){
				zeai.msg(0);zeai.msg(rs.msg);
				if (rs.flag == 1){
					photo_s.html('<img src='+rs.photo_s+'><span>重新上传</span>');
					photo_s_id.src=rs.photo_s;
				}
			}
		});
		areaid_.onclick=function(){
			var obj = o('areaid_'),defV = obj.getAttribute("data");
			ios_select_area('地区',areaARR1,areaARR2,areaARR3,defV,function(obj1,obj2,obj3){
				o('areaid').value    = obj1.i + ',' + obj2.i + ',' + obj3.i;
				o('areatitle').value = obj1.v + ' ' + obj2.v + ' ' + obj3.v;
				obj.value=o('areatitle').value;
			},',');
		}
		function my_info_save(){
			zeai.ajax({url:'tg_my_info'+zeai.ajxext+'submitok=ajax_apply',form:WWW_z_e_A__i_Cn},function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg,{time:2});
				if(rs.flag==1){
					setTimeout(function(){o('ZEAIGOBACK-tg_my_info').click();},2000);
				}
			});
		}
        </script>   

    </div>
<?php }elseif($a == 'tg_my_set'){/////////账户与安全/////////////
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-tg_my_set">&#xe602;</i>账户与安全';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
	<style>
	.submainX .menu{margin-top:10px}
	.submainX .menu ul li i{width:30px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
	.submainX .menu i.bank{color:#00A2EA;padding-left:2px}
	.submainX .menu i.modpass{color:#1478F0;font-size:24px}
	.submainX .menu i.mobcert{color:#f60;font-size:24px}
    </style>
	<div class="submainX TG" style="padding:0">
        <div class="menu">
            <ul>
                <li onclick="page({g:'tg_my_info.php?i=tg_my_set_bank',y:'tg_my_set',l:'tg_my_set_bank'})"><i class="ico bank">&#xe655;</i><h4>收款账号</h4></li>
                <li onclick="page({g:'tg_my_info.php?i=tg_my_set_modpass',y:'tg_my_set',l:'tg_my_set_modpass'})"><i class="ico modpass">&#xe619;</i><h4>修改密码</h4></li>
                <li onclick="page({g:'tg_my_info.php?i=tg_my_set_mobcert',y:'tg_my_set',l:'tg_my_set_mobcert'})"><i class="ico mobcert">&#xe6ec;</i><h4>手机认证</h4></li>
            </ul>
        </div>
    </div>
<?php }elseif($i == 'tg_my_set_bank'){//收款账号
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-tg_my_set_bank">&#xe602;</i>收款账号';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
	<style>
	.submainX dl dt{width:30%;float:left}
	.submainX dl dd{width:70%;float:right}
    </style>
	<div class="submainX">
        <form id="www_z_e_A__i_Cn">
        	<div class="linebox" style="margin-top:0px"><div class="line "></div><div class="title S18 BAI B" style="color:#F7564D">支付宝</div></div>
            <dl><dt>支付宝账号</dt><dd><input name="alipay_username" type="text" class="input " placeholder="请输入支付宝账号"  autocomplete="off" maxlength="100" value="<?php echo $data_alipay_username;?>"></dd></dl>
            <dl><dt>支付宝姓名</dt><dd><input name="alipay_truename" type="text" class="input "  placeholder="请输入支付宝姓名" autocomplete="off" maxlength="12" value="<?php echo $data_alipay_truename;?>"></dd></dl>
		
            <div class="linebox" style="margin-top:5px"><div class="line "></div><div class="title S18 BAI B" style="color:#F7564D">银行卡</div></div>
            <dl><dt>银行名称</dt><dd><input name="bank_name" type="text" class="input " placeholder="请输入银行名称" autocomplete="off" maxlength="50" value="<?php echo $data_bank_name;?>" /></dd></dl>
            <dl><dt>开户行名称</dt><dd><input name="bank_name_kaihu" type="text" class="input " placeholder="请输入开户行名称" autocomplete="off" maxlength="50" value="<?php echo $data_bank_name_kaihu;?>"></dd></dl>
            <dl><dt>银行卡号</dt><dd><input name="bank_card" type="text" class="input " placeholder="请输入银行卡号" autocomplete="off" maxlength="50" value="<?php echo $data_bank_card;?>"></dd></dl>
            <dl><dt>卡号姓名</dt><dd><input name="bank_truename" type="text" class="input "  placeholder="请输入卡号姓名" autocomplete="off" maxlength="12" value="<?php echo $data_bank_truename;?>"></dd></dl>
        <br><br><br><br>
		<button type="button" class="btn size4 HONG4 B yuan" onClick="my_info_save();">保存</button>
        </form>
    </div>
	<script>
        function my_info_save(){
            zeai.ajax({url:'tg_my_info'+zeai.ajxext+'submitok=tg_my_set_bank_update',form:www_z_e_A__i_Cn},function(e){rs=zeai.jsoneval(e);
                zeai.msg(0);zeai.msg(rs.msg);
                if(rs.flag==1)setTimeout(function(){o('ZEAIGOBACK-tg_my_set_bank').click();},1000);
            });
        }
    </script>    
<?php }elseif($i == 'tg_my_set_modpass'){//修改密码
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-tg_my_set_modpass">&#xe602;</i>修改密码';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
	<style>
	.submainX dl dt{width:30%;float:left}
	.submainX dl dd{width:70%;float:right}
    </style>
	<div class="submainX">
        <form id="www_z_e_A__i_Cn">
            <dl><dt>输入旧密码</dt><dd><input name="old_password" type="password" class="input W100_"   id="old_password" placeholder="请输入旧密码" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()"></dd></dl>
            <dl><dt>输入新密码</dt><dd><input name="form_password1" type="password" class="input W100_" id="form_password1" placeholder="请输入新密码(6~20长度字符)" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()" /></dd></dl>
            <dl><dt>确认新密码</dt><dd><input name="form_password2" type="password" class="input W100_" id="form_password2" placeholder="请再输一次新密码" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()"></dd></dl>
			<button type="button" class="btn size4 HONG4 B yuan" onClick="my_info_save();">保存</button>
        </form>
    </div>
	<script>
		function my_set_modpasstop(){zeai.setScrollTop(0);}
        function my_info_save(){
			if(zeai.empty(o('old_password').value) || zeai.str_len(o('old_password').value)<6){
				zeai.msg('请输入旧密码6~20个字节内');
				return false;
			}
			if(zeai.empty(o('form_password1').value)){
				zeai.msg('请输入新密码6~20个字节内！');
				return false;
			}
			if(zeai.str_len(o('form_password1').value)>20 || zeai.str_len(o('form_password1').value)<6){
				zeai.msg('新密码请控制在6~20个字节内！');
				return false;
			}
			if(zeai.empty(o('form_password2').value)){
				zeai.msg('请再输入一次新密码');
				return false;
			}
			if(zeai.str_len(o('form_password2').value)>20 || zeai.str_len(o('form_password2').value)<6){
				zeai.msg('新密码请在6~20个字节内！');
				return false;
			}
			if(o('form_password1').value!=o('form_password2').value) {
				zeai.msg('两次密码不一致');
				return false;		
			}
            zeai.ajax({url:'tg_my_info'+zeai.ajxext+'submitok=tg_my_set_modpass_update',form:www_z_e_A__i_Cn},function(e){rs=zeai.jsoneval(e);
                zeai.msg(0);zeai.msg(rs.msg);
                if(rs.flag==1)setTimeout(function(){o('ZEAIGOBACK-tg_my_set_modpass').click();},1000);
            });
        }
    </script> 
<?php }elseif($i == 'tg_my_set_mobcert'){//手机认证
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-tg_my_set_mobcert">&#xe602;</i>手机认证';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
	
	$rz_str = (@in_array('mob',$RZarr) && ifmob($data_mob))?'<div>'.$data_mob." <i class='ico S18' style='color:#45C01A'>&#xe60d;</i> <font style='color:#45C01A' class='S14'>已认证</font></div>":'';
    require_once ZEAI.'m1/top_mini.php';?>
	<style>
	.submainX{padding:0}
	.submainX dl dt{width:30%;float:left}
	.submainX dl dd{width:70%;float:right}
	.submainX .tg_mobcert_str{text-align:center}
	.submainX .tg_mobcert_str div{border:0;font-size:18px}
    </style>
    <link href="css/tg_loginreg.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
	<div class="submainX tgreg">
        <form id="WWW__ZEAI_CN_form" style="margin:20px;padding-top:0">
        	<div class="tg_mobcert_str"><?php echo $rz_str;?></div>
            <dl style="padding-top:0px;margin-top:0"><dt><i class="ico">&#xe627;</i></dt><dd><input name="mob" type="text" class="input_login" id="mob" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*"></dd></dl>
            <dl><dt><i class="ico">&#xe6c3;</i></dt><dd class="yzmF">
            <input name="verify" id="verify" type="text" required class="input_login" maxlength="4" placeholder="输入手机短信验证码" autocomplete="off" /><a href="javascript:;" class="yzmbtn" id="yzmbtn">获取验证码</a>
            </dd></dl>
            <button type="button" class="btn size4 HONG4 B yuan" onClick="my_info_save();" style="width:80%;left:10%;background-color:#F7564D;color:#fff">开始认证</button>
        </form>
    </div>
	<script>
		if (!zeai.empty(o('yzmbtn'))){
			yzmbtn.onclick = function(){
				if (zeai.ifmob(o('mob').value)){
					if (!this.hasClass('disabled')){
						yzmbtn.addClass('disabled');
						zeai.ajax({'url':'tg_reg'+zeai.extname,'data':{'submitok':'ajax_reg_verify',mob:o('mob').value}},function(e){
							var rs=zeai.jsoneval(e);
							if (rs.flag == 1){
								zeai.msg(rs.msg,{time:5});
								o('verify').value='';
								yzmtimeFn(120);
							}else{
								zeai.msg(rs.msg,mob);
								yzmbtn.removeClass('disabled');
							}
						});
					}
				}else{
					zeai.msg('请输入手机号码',mob);
					return false;
				}
			}
		}
        function my_info_save(){
			var mobV = o('mob').value,verifyV=o('verify').value;
			if(!zeai.ifmob(mobV)){zeai.msg('请输入正确手机号',o('mob'));return false;}
			if(!zeai.ifint(verifyV) || zeai.str_len(verifyV)!=4 ){zeai.msg('请输入【手机验证码】',o('verify'));return false;}
            zeai.ajax({url:'tg_my_info'+zeai.ajxext+'submitok=tg_my_set_mobcert_update',form:WWW__ZEAI_CN_form},function(e){rs=zeai.jsoneval(e);
                zeai.msg(0);zeai.msg(rs.msg);
                if(rs.flag==1)setTimeout(function(){o('ZEAIGOBACK-tg_my_set_mobcert').click();},1000);
            });
        }
    </script>    
	<script src="js/tg_reg.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<?php
require_once '../sub/init.php';
$currfieldstg="kuaidi_truename,kuaidi_address,kuaidi_mob,bank_name,bank_name_kaihu,bank_truename,bank_card,alipay_truename,alipay_username";
require_once ZEAI.'m4/shop_chk_u.php';
if($submitok=='shop_my_set_address_update'){
	if(empty($truename))json_exit(array('flag'=>0,'msg'=>'请输入【收件人姓名】'));
	if(empty($address))json_exit(array('flag'=>0,'msg'=>'请输入【收件人详细地址】'));
	if(empty($mob))json_exit(array('flag'=>0,'msg'=>'请输入【收件人电话】'));
	$truename = dataIO($truename,'in',200);
	$address  = dataIO($address,'in',200);
	$mob      = dataIO($mob,'in',200);
	$db->query("UPDATE ".__TBL_TG_USER__." SET kuaidi_truename='$truename',kuaidi_address='$address',kuaidi_mob='$mob' WHERE id=".$cook_tg_uid);	
	json_exit(array('flag'=>1,'msg'=>'设置成功'));
}elseif($submitok=='shop_my_set_modpass_update'){
	if (str_len($old_password)<6 || str_len($old_password)>20)json_exit(array('flag'=>0,'msg'=>'【旧密码】长度请控制在6-20字节以内。'));
	if (str_len($form_password1)<6 || str_len($form_password1)>20)json_exit(array('flag'=>0,'msg'=>'【新密码】长度请控制在6-20字节以内。'));
	if (str_len($form_password2)<6 || str_len($form_password2)>20)json_exit(array('flag'=>0,'msg'=>'【确认新密码】长度请控制在6-20字节以内。'));
	if ($form_password1 <> $form_password2)json_exit(array('flag'=>0,'msg'=>'两次密码输入不一样，请重试！'));
	$password     = md5(trimm($form_password1));
	$old_password = md5($old_password);
	$rt = $db->query("SELECT id FROM ".__TBL_TG_USER__." WHERE id=".$cook_tg_uid." AND pwd='$old_password'");
	if(!$db->num_rows($rt))json_exit(array('flag'=>0,'msg'=>'【旧密码】不正确，修改失败！'));
	$db->query("UPDATE ".__TBL_TG_USER__." SET pwd='$password' WHERE id=".$cook_tg_uid);
	setcookie("cook_tg_pwd",$password,time()+720000,"/",$_ZEAI['CookDomain']);
	json_exit(array('flag'=>1,'msg'=>'修改成功！'));
}elseif($submitok=='shop_my_set_bank_update'){
	$bank_name       = dataIO($bank_name,'in',100);
	$bank_name_kaihu = dataIO($bank_name_kaihu,'in',200);
	$bank_truename   = dataIO($bank_truename,'in',50);
	$bank_card       = dataIO($bank_card,'in',50);
	$alipay_truename = dataIO($alipay_truename,'in',50);
	$alipay_username = dataIO($alipay_username,'in',100);
	$setsql = "bank_name='$bank_name',bank_name_kaihu='$bank_name_kaihu',bank_truename='$bank_truename',bank_card='$bank_card',alipay_truename='$alipay_truename',alipay_username='$alipay_username'";
	$db->query("UPDATE ".__TBL_TG_USER__." SET ".$setsql." WHERE id=".$cook_tg_uid);
	json_exit(array('flag'=>1,'msg'=>'修改成功'));
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>\
<style>
.btmboxinput dl dt{width:25%;font-size:14px}
.btmboxinput dl dd{width:75%}
::-webkit-input-placeholder {font-size:14px}
</style>
</head>
<body>
<?php 
$url=HOST.'/m4/shop_index.php';
$mini_title = '<i class="ico goback" onClick="zeai.back(\''.$url.'\');">&#xe602;</i>设置';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
$kuaidi_truename = dataIO($rowtg['kuaidi_truename'],'out');
$kuaidi_address  = dataIO($rowtg['kuaidi_address'],'out');
$kuaidi_mob      = dataIO($rowtg['kuaidi_mob'],'out');
$bank_name       = dataIO($rowtg['bank_name'],'out');
$bank_name_kaihu = dataIO($rowtg['bank_name_kaihu'],'out');
$bank_truename   = dataIO($rowtg['bank_truename'],'out');
$bank_card       = dataIO($rowtg['bank_card'],'out');
$alipay_truename = dataIO($rowtg['alipay_truename'],'out');
$alipay_username = dataIO($rowtg['alipay_username'],'out');?>
<div class="modlist">
	<ul>
		<li class="tborder" id="shop_my_set_addressbtn"><h4>收货地址</h4><span></span></li>
		<li id="shop_my_set_modpassbtn"><h4>修改密码</h4><span></span></li>
		<li id="shop_my_set_bankbtn"><h4>提现账号</h4><span></span></li>
	</ul>
</div>
<div id="shop_my_set_addressbox" class="btmboxinput">
	<h1>收货信息</h1>
	<form id="Zeai__cn_form">
	<input type="text" name="address" id="address" class="input" placeholder="请输入收件人详细【地址】" maxlength="100" value="<?php echo $kuaidi_address;?>" onBlur="zeai.setScrollTop(0);" autocomplete="off" >
	<input type="text" name="truename" id="truename" class="input" placeholder="请输入收件人【姓名】" maxlength="100" value="<?php echo $kuaidi_truename;?>" onBlur="zeai.setScrollTop(0);" autocomplete="off" >
	<input type="text" name="mob" id="mob" class="input" placeholder="请输入收件人【电话】" maxlength="100" value="<?php echo $kuaidi_mob;?>" onBlur="zeai.setScrollTop(0);" autocomplete="off" >
	<input type="hidden" name="submitok" value="shop_my_set_address_update">
	<button type="button" class="btn size4 HONG3 yuan" id="shop_my_set_addressbox_btn">确定并保存</button>
	</form>
</div>

<div id="shop_my_set_modpassbox" class="btmboxinput">
	<h1>修改密码</h1>
	<form id="Zeai__cn_formP">
	<input type="text" name="old_password" id="old_password" class="input" placeholder="请输入旧密码" maxlength="20" onBlur="zeai.setScrollTop(0);" autocomplete="off" >
	<input type="text" name="form_password1" id="form_password1" class="input" placeholder="请输入新密码" maxlength="20" onBlur="zeai.setScrollTop(0);" autocomplete="off" >
	<input type="text" name="form_password2" id="form_password2" class="input" placeholder="再输一次新密码" maxlength="20" onBlur="zeai.setScrollTop(0);" autocomplete="off" >
	<input type="hidden" name="submitok" value="shop_my_set_modpass_update">
	<button type="button" class="btn size4 HONG3 yuan" id="shop_my_set_modpassbox_btn">确定并保存</button>
	</form>
</div>

<div id="shop_my_set_bankbox" class="btmboxinput">
	<h1 style="margin-bottom:20px">收款账号<span>以下只做备用，只有在没有微信的情况才会启用进行人工转账</span></h1>
	<form id="Zeai__cn_formB">
	<dl><dt class="Clan">支付宝账号</dt><dd><input name="alipay_username" type="text" class="input " placeholder="请输入【支付宝账号】"  autocomplete="off" maxlength="100" value="<?php echo $alipay_username;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
	<dl style="margin-bottom:20px"><dt class="Clan">支付宝姓名</dt><dd><input name="alipay_truename" type="text" class="input "  placeholder="请输入【支付宝姓名】" autocomplete="off" maxlength="12" value="<?php echo $alipay_truename;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
	<div class="br"></div>
	<dl><dt>银行名称</dt><dd><input name="bank_name" type="text" class="input " placeholder="请输入银行名称" autocomplete="off" maxlength="50" value="<?php echo $bank_name;?>" onBlur="zeai.setScrollTop(0);" /></dd></dl>
	<dl><dt>开户行名称</dt><dd><input name="bank_name_kaihu" type="text" class="input " placeholder="请输入开户行名称" autocomplete="off" maxlength="100" value="<?php echo $bank_name_kaihu;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
	<dl><dt>银行卡号</dt><dd><input name="bank_card" type="text" class="input " placeholder="请输入银行卡号" autocomplete="off" maxlength="50" value="<?php echo $bank_card;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
	<dl><dt>卡号姓名</dt><dd><input name="bank_truename" type="text" class="input "  placeholder="请输入卡号姓名" autocomplete="off" maxlength="12" value="<?php echo $bank_truename;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
	<input type="hidden" name="submitok" value="shop_my_set_bank_update">
	<button type="button" class="btn size4 HONG3 yuan" id="shop_my_set_bankbox_btn">确定并保存</button>
	</form>
</div>
<script>
//address
shop_my_set_addressbtn.onclick=function(){ZeaiM.div_up({obj:shop_my_set_addressbox,h:320});};
shop_my_set_addressbox_btn.onclick=function(){
	if(zeai.empty(address.value)){zeai.msg('请输入【收件人详细地址】');	return false;}
	if(zeai.empty(truename.value)){zeai.msg('请输入【收件人姓名】');return false;}
	if(zeai.empty(mob.value)){zeai.msg('请输入【收件人电话】');	return false;}
	zeai.ajax({url:'shop_my_set'+zeai.extname,form:Zeai__cn_form},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);if(rs.flag==1){setTimeout("div_up_close.click()",1000);}
	});
}
//modpass
shop_my_set_modpassbtn.onclick=function(){ZeaiM.div_up({obj:shop_my_set_modpassbox,h:320});};
shop_my_set_modpassbox_btn.onclick=function(){
	if(zeai.empty(o('old_password').value) || zeai.str_len(o('old_password').value)<6){zeai.msg('请输入【旧密码】长度6~20个字节内');	return false;}
	if(zeai.str_len(o('form_password1').value)>20 || zeai.str_len(o('form_password1').value)<6){zeai.msg('请输入【新密码】长度6~20个字节内');return false;}
	if(zeai.str_len(o('form_password2').value)>20 || zeai.str_len(o('form_password2').value)<6){zeai.msg('再输一次【新密码】长度6~20个字节内');	return false;}
	if(o('form_password1').value!=o('form_password2').value){zeai.msg('【新密码】两次输入不一致，请检查');return false;}
	zeai.ajax({url:'shop_my_set'+zeai.extname,form:Zeai__cn_formP},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);if(rs.flag==1){setTimeout("div_up_close.click()",1000);}
	});
}
//bank
shop_my_set_bankbtn.onclick=function(){ZeaiM.div_up({obj:shop_my_set_bankbox,h:510});};
shop_my_set_bankbox_btn.onclick=function(){zeai.ajax({url:'shop_my_set'+zeai.extname,form:Zeai__cn_formB},function(e){rs=zeai.jsoneval(e);zeai.msg(0);zeai.msg(rs.msg);if(rs.flag==1){setTimeout("div_up_close.click()",1000);}});}
</script>
</body>
</html>
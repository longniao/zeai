<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if($session_kind == 'crm'){
	if(!in_array('crm_u_add',$QXARR))exit(noauth('暂无【会员录入】权限'));
}else{
	if(!in_array('u_add',$QXARR))exit(noauth('暂无【会员录入】权限'));
}
require_once ZEAI.'cache/udata.php';$sex_ARR = json_decode($_UDATA['sex'],true);
header("Cache-control: private");
if ($submitok == "ajax_user_chkusername") {
	$chkflag = array('flag'=>1);
	if (str_len($uname) > 20 || str_len($uname)<3)$chkflag = array('flag'=>0,'msg'=>'请输入正确的用户名');
	$row = $db->NUM('yzlove.com___zeai.cn','id',"uname='".$uname."'");
	if($row)$chkflag = array('flag'=>0,'msg'=>'此用户名已被占用,请重新输入');
	json_exit($chkflag);
}elseif($submitok == "addupdate") {
	if (str_len($uname) > 20 || str_len($uname)<3)alert_adm("请输入正确的用户名",-1);
	if (str_len($pwd) > 20 || str_len($pwd)<6)alert_adm("请输入正确的密码",-1);
	if (!ifint($kind))alert_adm("请输入选择会员类型",-1);
	$sex = (empty($sex))?1:intval($sex);
	$uname    = dataIO($uname,'in');
	$nickname = dataIO($nickname,'in');
	$pwd      = md5(trim($pwd));
	$kind = intval($kind);
	$birthday = (!ifdate($birthday))?'0000-00-00':$birthday;
	$row = $db->NUM('yzlove.com___zeai.cn','id',"uname='".$uname."'");if($row)alert_adm("此用户名已被占用,请重新输入",-1);
	$flag   = 1;$dataflag=1;
	$admid  = $session_uid;
	$admname = $session_truename;
	$agentid = intval($session_agentid);
	$agenttitle = $session_agenttitle;
	$crm_ukind  = intval($crm_ukind);
	$bz = dataIO($bz,'in',500);
	$db->query("INSERT INTO ".__TBL_USER__." (agentid,agenttitle,kind,uname,nickname,pwd,regtime,endtime,refresh_time,regkind,sex,birthday,dataflag,admid,admname,flag,crm_ukind,bz,crm_ubz) VALUES ($agentid,'$agenttitle',$kind,'".$uname."','".$nickname."','".$pwd."',".ADDTIME.",".ADDTIME.",".ADDTIME.",9,$sex,'".$birthday."',$dataflag,'$admid','$admname',$flag,$crm_ukind,'$bz','$bz')");
	$uid = intval($db->insert_id());
	AddLog('录入新会员【'.$nickname.'（uid:'.$uid.'）】');
	alert_adm("录入成功，请继续完善资料".$cc,"u_mod_data.php?submitok=mod&uid=$uid");
}
$birthday = date("Y")-20-cdstr(1);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<script>
function chkform(){
	if(zeai.empty(uname.value) || zeai.str_len(uname.value)<3 || zeai.str_len(uname.value)>20){
		zeai.msg('请输入正确的会员登录用户名',uname);
		return false;
	}
	zeai.ajax('u_add'+zeai.ajxext+'submitok=ajax_user_chkusername&uname='+uname.value,function(e){var rs = zeai.jsoneval(e);
		if (rs.flag == 0)zeai.msg(rs.msg,uname);
	});
	if(zeai.empty(pwd.value) || zeai.str_len(pwd.value)<6 || zeai.str_len(pwd.value)>20){
		zeai.msg('请输入登录密码',pwd);
		return false;
	}
	if(zeai.empty(nickname.value) || zeai.str_len(nickname.value)<2 || zeai.str_len(nickname.value)>40){
		zeai.msg('请输入正确的昵称',nickname);
		return false;
	}
	if(!zeai.form.ifradio('sex')){
		zeai.msg('请选择会员性别');
		return false;
	}
	if(zeai.empty(birthday.value)){
		zeai.msg('请选择出生年月',birthday);
		return false;
	}
	if(!zeai.form.ifradio('kind')){
		zeai.msg('请选择会员类型');
		return false;
	}
}
</script>
<style>input:-webkit-autofill{-webkit-box-shadow:0 0 0px 1000px white inset !important}
.table {min-width:1000px}
</style>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<body>
<div class="navbox">
<a class='ed'>录入新用户</a>
<a href="import.php">数据导入</a>


<div class="clear"></div></div>
<div class="fixedblank"></div>
<form name="GYLform" id="GYLform" action="<?php echo $SELF; ?>" method="post" onsubmit="return chkform();">

<table class="table Mtop50">


	<?php
	if(ifint($session_agentid)){
		?>
		<tr><td class="tdL">所属门店</td><td class="tdR">
		<input name="agentid" type="hidden" value="<?php echo $session_agentid;?>" /><?php echo $session_agenttitle;?>
		</td></tr>
		<?php
	}
	?>
    

<tr><td class="tdL">录入/认领</td><td class="tdR"><?php echo $session_truename.'（ID:'.$session_uid.'）';?>
　<span class="tips">录入或认领后，如果有二维码，前端会员点【找红娘】将出现录入认领人二维码</span>
</td></tr>

<tr><td class="tdL"><font class="Cf00">*</font> 线上登录用户名</td><td class="tdR"><input name="uname" id="uname" type="text" class="input size2 W200" value="ht_<?php echo cdstr(5); ?>" size="20" maxlength="20"   autocomplete="off" /><span class="tips">3~15位英文字母或加数字组合；如：zeai，zeai_123，线上会员可以登录前台</span></td></tr>

<tr><td class="tdL"><font class="Cf00">*</font> 线上登录密码</td><td class="tdR C8d"><input name="pwd" id="pwd" type="text" class="input size2 W200"  size="20" maxlength="20"   autocomplete="off" /><span class="tips">6~20位英文字母或加数字组合</span></td></tr>

<tr><td class="tdL"><font class="Cf00">*</font> 生　　日</td><td class="tdR"><input name="birthday" id="birthday" type="text" readonly class="input size2 W100 hand" value="<?php echo $birthday; ?>-01-15" size="10" maxlength="10"></td></tr>

<tr><td class="tdL"><font class="Cf00">*</font> 性　　别</td><td class="tdR">
<?php foreach ($sex_ARR as $v) {?>
    <input type="radio" name="sex" value="<?php echo $v['i'];?>" id="sex_<?php echo $v['i'];?>" class="radioskin"><label for="sex_<?php echo $v['i'];?>" class="radioskin-label"><i class="i2"></i><b class="W30"><?php echo $v['v'];?></b></label>　
<?php }?>
</td></tr>
<tr><td class="tdL"><font class="Cf00">*</font> 昵称网名</td><td class="tdR"><input name="nickname" id="nickname" type="text" class="input size2 W200" size="40" maxlength="20"   autocomplete="off" /><span class="tips">40字节以内，将会显在个人资料上面</span></td></tr>


<tr><td class="tdL"><font class="Cf00">*</font> 会员类型</td><td class="tdR" style="padding:20px 10px">
<input type="radio" name="kind" value="1" id="kind_1" class="radioskin"<?php echo ($kind == 1)?' checked':'';?>><label for="kind_1" class="radioskin-label"><i class="i2"></i><b class="W120">线上会员</b></label><span class="tips">会员可以登录前台网站，可以自主联系互动，和会员自己注册效果一样</span><br><br>
<input type="radio" name="kind" value="2" id="kind_2" class="radioskin"<?php echo ($kind == 2)?' checked':'';?>><label for="kind_2" class="radioskin-label"><i class="i2"></i><b class="W120">线下会员</b></label><span class="tips">内部会员，网站前台只展示，别的会员联系必须通过红娘，不可登录互动，后台/CRM人工管理服务</span><br><br>
<input type="radio" name="kind" value="3" id="kind_3" class="radioskin"<?php echo ($kind == 3)?' checked':'';?>><label for="kind_3" class="radioskin-label"><i class="i2"></i><b class="W120">均可(线上+线下)</b></label><span class="tips">都可以，如果线上会员申请红娘委托之后，将自动变为这种类型，后台/CRM人工管理服务</span><br><br>
<input type="radio" name="kind" value="4" id="kind_4" class="radioskin"<?php echo ($kind == 4)?' checked':'';?>><label for="kind_4" class="radioskin-label"><i class="i2"></i><b class="W120">机器人</b></label>
<span class="tips">虚拟会员，主要用来自动发私信和打招呼给新注册会员的，也就是虚拟会员（一般不要选）</span>
</td></tr>



<tr><td class="tdL">客户分类</td><td class="tdR"><script>zeai_cn__CreateFormItem('select','crm_ukind','<?php echo $crm_ukind; ?>','class="size2 W200"',crm_ukind_ARR);</script></td></tr>
<tr><td class="tdL">备　　注</td><td class="tdR"><textarea name="bz" rows="5" class="W98_" placeholder="备注(500字节内，没有请留空)"></textarea></td></tr>


</table><input name="submitok" type="hidden" value="addupdate" />
<style>
@-moz-document url-prefix() {.savebtnbox{bottom:50px}}
</style>
<br><br><br><br><div class="savebtnbox"><button type="submit" class="btn size3 HUANG3">　下一步　</button></div>

</form>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);laydate.render({elem: '#birthday'});</script>
<?php require_once 'bottomadm.php';?>
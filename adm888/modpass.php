<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';

//if($session_kind == 'adm'){
	if(!in_array('modpass',$QXARR))exit(noauth('权限不足'));
//}

if ($submitok == "mod") {
	if (str_len($form_password1)<6 || str_len($form_password1)>20)alert_adm("“新密码”请控制在6~20字节以内。","-1");
	if (str_len($form_password2)<6 || str_len($form_password2)>20)alert_adm("“确认新密码”请控制在6~20字节以内。","-1");
	if ($form_password1 <> $form_password2)alert_adm("两次密码输入不一样，请重试！","-1");
	$pwd = md5(trim($form_password1));
	$old_password = md5($old_password);
	$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id='".$_SESSION['admuid']."' AND password='$old_password'");
	if(!$db->num_rows($rt)){
		alert_adm("旧密码验证错误，修改失败！","-1");
	}else{
		$db->query("UPDATE ".__TBL_ADMIN__." SET password='$pwd' WHERE id='".$_SESSION['admuid']."'");
		AddLog('修改后台密码');
		$_SESSION["admpwd"]   = $pwd;
		alert_adm("恭喜!修改成功。","-1");
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<div class="navbox"><a class="ed">密码修改</a><div class="clear"></div></div>
<table class="table Mtop150 W700">
<script>
function chkform(){
	if(zeai.empty(o('old_password').value)){
		zeai.msg('请输入旧密码！',old_password);
		return false;
	}
	if(zeai.empty(o('form_password1').value)){
		zeai.msg('请输入新密码！',form_password1);
		return false;
	}
	if(zeai.str_len(o('form_password1').value)>20 || zeai.str_len(o('form_password1').value)<6){
		zeai.msg('新密码请控制在6~20个字节内！',form_password1);
		return false;
	}
	if(zeai.empty(o('form_password2').value)){
		zeai.msg('请再输入一次新密码',form_password2);
		return false;
	}
	if(zeai.str_len(o('form_password2').value)>20 || zeai.str_len(o('form_password2').value)<6){
		zeai.msg('确认新密码请控制在6~20个字节内！',form_password2);
		return false;
	}
	if(o('form_password1').value != o('form_password2').value) {
		zeai.msg('两次密码不一致，请重试',form_password2);
		return false;		
	}
}
</script>
<form action="<?php echo SELF; ?>" method="post" onSubmit="return chkform()">
<tr>
<td height="20" colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT">后台密码修改：</td>
</tr>
<tr>
<td class="tdL">旧密码：</td>
<td class="tdR"><input name="old_password" type="password" class="input size2" id="old_password" size="40" maxlength="20" required />
</td>
</tr>
<tr>
<td class="tdL">新密码：</td>
<td class="tdR"><input name="form_password1" type="password" class="input size2" id="form_password1"size="40" maxlength="20" required /><span class="tips">长度6~20位</span></td>
</tr>
<tr>
<td class="tdL">确认新密码：</td>
<td class="tdR">
<input name="form_password2" type="password" class="input size2" id="form_password2" size="40" maxlength="20" required /><span class="tips">长度6~20位</span>
<input name="submitok" type="hidden" value="mod" />
</td>
</tr>
<tr>
<td class="tdL">&nbsp;</td>
<td class="tdR"><input class="btn size3 HUANG" type="submit" value="保存修改" /></td>
</tr>
</form>
</table>
<?php require_once 'bottomadm.php';?>
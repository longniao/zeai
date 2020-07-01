<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('u_mod_pass',$QXARR))exit(noauth());
header("Cache-control: private");
if($submitok == "modupdate") {
	if (str_len($pwd) > 20 || str_len($pwd)<6)alert_adm("密码长度必须6~20",-1);
	if (!ifint($uid)){
		alert_adm("用户ID不合法",'back');
	}else{
		$uid = intval($uid);
		$row = $db->ROW(__TBL_USER__,'nickname',"id=".$uid);
		if(!$row){
			alert_adm("用户ID不存在",-1);
		}else{
			$nickname = dataIO($row[0],'out');
			$pwd      = md5($pwd);
			$db->query("UPDATE ".__TBL_USER__." SET pwd='$pwd' WHERE id=".$uid);
			AddLog('重置会员密码【'.$nickname.'（uid:'.$uid.'）】');		}
	}
	alert_adm("【".$nickname."】密码重置成功",'back');
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
<div class="navbox">
    <a class='ed'>重置会员密码</a>
<div class="clear"></div></div>
<script>
function chkform(){
	var uidv = o('uid').value
	var pwdv = o('pwd').value
	if(!zeai.ifint(uidv)){
		zeai.alert('请输入正确的会员ID号',uid);
		return false;
	}
	if(zeai.empty(pwdv)){
		zeai.alert('请输入密码',pwd);
		return false;
	}
	//zeai.post('u_mod_pass'+zeai.ajxext+'submitok=ajax_get_uinfo&uid='+uidv+'&pwd='+pwdv);
}
</script>
<table class="table W700 Mtop150">
<form name="GYLform" id="GYLform" action="<?php echo $SELF; ?>" method="post" onSubmit="return chkform();">
<tr>
<td height="20" colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT">重置会员<?php echo $title; ?>登录密码：</td>
</tr>
<tr>
<td class="tdL">会员ID号</td>
<td class="tdR"><input name="uid" type="text" required class="size2 W100" id="uid" value="<?php echo $uid; ?>" size="40" maxlength="20" />
</td>
</tr>
<tr>
<td class="tdL">新密码</td>
<td class="tdR C8d"><input name="pwd" type="pwd" class="size2 W300" id="pwd"size="40" maxlength="20" required /> 6~20位</td>
</tr>
<tr>
<td class="tdL">&nbsp;</td>
<td class="tdR"><input class="btn size3 HUANG3" type="submit" value="保存" />
<input name="submitok" type="hidden" value="modupdate" /></td>
</tr>
</form>
</table>
<?php require_once 'bottomadm.php';?>
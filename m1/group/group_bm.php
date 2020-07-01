<?php 
require_once "../../sub/init.php";
if ( !preg_match("/^[0-9]{1,8}$/",$mainid) && !empty($mainid))callmsg("不存在～","-1");
if ( !ifint($cook_uid)){header("Location: ".HOST."/?z=my");exit;}
require_once ZEAI.'sub/conn.php';
$rtD=$db->query("SELECT title FROM ".__TBL_GROUP_CLUB__." WHERE id=".$fid);
if ($db->num_rows($rtD)){
	$rowD = $db->fetch_array($rtD);
	$title  = dataIO($rowD[0],'out');
}else{
	callmsg("Forbidden","-1");
}
$rtD=$db->query("SELECT mob FROM ".__TBL_USER__." WHERE id=".$cook_uid);
if ($db->num_rows($rtD)){
	$rowD = $db->fetch_array($rtD);
	$mob  = $rowD[0];
}else{
	header("Location: ".HOST."/?z=my");exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>活动报名</title>
<?php echo $headmeta; ?>
<script src="www_zeai_cn.js?1"></script>
<link href="group.css" rel="stylesheet" type="text/css" />
</head>
<script language="javascript">
function chk(){
if(document.yzloveform.tel.value==""){
alert('请输入电话／手机！');
document.yzloveform.tel.focus();
return false;
}}
</script>
<body>
<?php require_once 'top_mini.php';?>
<table class="partybm">
<form method="post" action="group_partyshow.php" name=yzloveform onsubmit="return chk()">
<tr>
<td height="40" colspan="2" align="center" bgcolor="#F4DCEE" style="font-size:14px;font-weight:bold">参加：<?php echo $title; ?>交友活动</td>
</tr>
<tr>
  <td height="40" colspan="2" align="left" bgcolor="#FDF2F9" style="padding: 10px;"><font color="#FF6C96">请留下你的联系方式如电话和手机，我们会很快与您取得联系，确定报名人数和通知你参加活动，此电话手机我们只做为活动通知之用，绝对保密，不会公开，请放心填写。</font></td>
</tr>
<tr>
<td width="70" height="50" align="right">联系方式</td>
<td>
<label>
<input name="tel" type="text" class="input" value="<?php echo $mob;?>" maxlength="100" />
</label>    </td>
</tr>
<tr>
<td height="60" colspan="2" align="center"><input type="submit" name="Submit" value="开始报名" class="btn2" <?php if (!ifint($cook_uid)){echo "disabled='disabled'";} ?>><input name="submitok" type="hidden" value="bmupdate"><input name="mainid" type="hidden" value="<?php echo $mainid;?>">
  <input name="fid" type="hidden" value="<?php echo $fid;?>"><input name="mbkind" type="hidden" value="<?php echo $mbkind;?>"></td>
</tr>
</form>
</table>
</body>
</html>

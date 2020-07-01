<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if ( !preg_match("/^[0-9]{1,8}$/",$fid) || empty($fid))callmsg("参数不正确！","-1");
if ($submitok == 'modupdate'){
	if ( !preg_match("/^[0-9]{1,8}$/",$fid) || $fid == 0 )callmsg("Forbidden1!","-1");
	$db->query("UPDATE ".__TBL_DATING_USER__." SET content='$content' WHERE id=".$fid);
	AddLog('【约会审核】修改约会报名信息，报名id:'.$fid);
	header("Location: dating_user_mod.php?fid=".$fid);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<?php
if ( !preg_match("/^[0-9]{1,8}$/",$fid) || $fid == 0 ){
	callmsg("Forbidden!","-1");
} else {
	$rt = $db->query("SELECT * FROM ".__TBL_DATING_USER__." WHERE id=".$fid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
	} else {
		callmsg("该信息不存在或已被删除！","./");
		exit;
	}
}
?>
<br>
<table width="480" border="0" align="center" cellpadding="8" cellspacing="0" bgcolor="#FFFFFF" style="color:#666;">
<form action="" method="post" name=www_yzlove_com>
  <tr>
    <td width="50" align="right" valign="top" class="S14">内容:</td>
    <td width="387" align="left"><textarea name="content" id="content" style="WIDTH: 370px; HEIGHT: 96px"><?php echo dataIO($row['content'],'out');?></textarea></td>
  </tr>
  <tr>
    <td width="50" align="right"><input name="submitok" type="hidden" value="modupdate" />
      <input name="fid" type="hidden" value="<?php echo $fid; ?>" /></td>
    <td height="50" align="left"><input type="submit" class="btn size3" value=" 修 改 "></td>
  </tr>
</form>
</table>
      <table width="480" height="47" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr>
          <td align="center">&nbsp;</td>
        </tr>
</table>
</body>
</html>
<?php
require_once '../sub/init.php';
require_once "chkUadm.php";
if ( !preg_match("/^[0-9]{1,9}$/",$classid) )callmsg("Forbidden!","-1");
if ($submitok == "modupdate") {
	if (str_len($content)>30000 || str_len($content)<1) {
		echo '<center><br><br><br><br>回复内容过多或过少，请控制在1~20000字节以内！<br><br><a href=group_wz_bbs_mod.php?classid='.$classid.'><b>返 回</b></a></center>';
		exit;
	}
	$content = dataIO($content,'in');
	$db->query("UPDATE ".__TBL_GROUP_WZ_BBS__." SET content='$content' WHERE id='$classid'");
}
$rt = $db->query("SELECT content FROM ".__TBL_GROUP_WZ_BBS__." WHERE id=".$classid);
if($db->num_rows($rt)) {$row = $db->fetch_array($rt);}
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
<br>
<table width="500" height="63" border="0" align="center" cellpadding="5" cellspacing="0" bgcolor="#dddddd" >
<form action="<?php echo $SELF; ?>" method="post" name="supdes">
<tr>
<td align="center" valign="top" bgcolor="#FFFFFF">
<textarea name="content" cols="120" rows="15" style="width:520px"><?php echo dataIO($row['content'],'out');?></textarea>
<br>
<input name="submitok" type="hidden" value="modupdate">
<input name="classid" type="hidden" value="<?php echo $classid; ?>">
</td>
</tr>
<tr>
  <td height="60" align="center" valign="top" bgcolor="#FFFFFF"><input name="提交" type="submit" class="btn size3" value=" 保存 "></td>
</tr>
</form>
</table>
<br>
<br>
<?php require_once "bottomadm.php";?>

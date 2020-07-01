<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
if (!ifint($classid))callmsg("forbidden","-1");
$chkflag = 0;
if ($submitok == "modupdate") {
	$content = dataIO($content,'in');
	if (str_len($content)>1000 || str_len($content)<1) {
		$chkflag = 0;
		echo '<link href="css/main.css" rel="stylesheet" type="text/css">';
		echo '<center class="S14"><br><br><br><br>内容请控制在1~1000字节以内！<br><br><a href=party_bbs_mod.php?classid='.$classid.' class="aHUI">返回</a></center>';exit;
	}else{
		$db->query("UPDATE ".__TBL_TREND__." SET content='$content' WHERE id='$classid'");
		AddLog('【交友圈审核】主题内容修改->主题id:'.$classid);
		$chkflag = 1;
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href="css/main.css" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js"></script>
<?php if ($chkflag == 1){ ?>
<script>
var bbsid=parent.document.getElementById('trend<?php echo $classid; ?>');
bbsid.innerHTML = '● <?php echo $content; ?>';
bbsid.className = 'Cf00 S14';
parent.zeai.iframe(0);
</script>
<?php }?>
</head>
<body>
<?php 
$rt = $db->query("SELECT content FROM ".__TBL_TREND__." WHERE id=".$classid);
if($db->num_rows($rt)) {$row = $db->fetch_array($rt);
?>
<table width="500" border="0" align="center" cellpadding="5" cellspacing="1">
<form action="" method="post" name=myform>
<tr>
<td align="center" valign="top" style="padding:20px 0 0">
<textarea name="content" rows="10" id=textarea style="width:450px"><?php echo dataIO($row['content'],'out');?></textarea>
<input name="submitok" type="hidden" value="modupdate">
<input name="classid" type="hidden" value="<?php echo $classid; ?>">
</td>
</tr>
<tr>
<td align="center" valign="top">
<input name="提交" type="submit" class="btn size3" value="修改并保存"></td>
</tr>
</form>
</table>
<?php }require_once 'bottomadm.php';?>
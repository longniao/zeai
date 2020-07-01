<?php
require_once '../sub/init.php';
require_once "chkUadm.php";
if ( !preg_match("/^[0-9]{1,8}$/",$classid) || empty($classid))callmsg("参数不正确！","-1");
if ($submitok=="modupdate") {
	if ( strlen($title)<6 || strlen($title)>100 )callmsg("标题请控制在6~100个字节以内","-1");
	if ( strlen($content)>60000 )callmsg("详细内容请控制在10~50000字节以内","-1");
	$db->query("UPDATE ".__TBL_GROUP_WZ__." SET title='$title',content='$content' WHERE id='$classid'");
	header("Location: group_wz_mod.php?classid=".$classid);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<!-- editor -->
<link rel="stylesheet" href="editor/themes/default/default.css" />
<script charset="utf-8" src="editor/kindeditor.js"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js"></script>
<script>
var editor;
KindEditor.ready(function(K){
  editor=K.create('textarea[name="content"]',{
	resizeType : 1,
	minWidth : 400,
	allowPreviewEmoticons : true,
	allowImageUpload : true,
	afterBlur:function(){this.sync();},
	items :[
		'forecolor', 'bold','removeformat','image', '|','plainpaste','wordpaste', 'unlink', '|','clearhtml', '|','preview','fullscreen']
  });
});
</script>
<!--editor end -->

</head>
<script>
function chkform(){
	if(document.MYFORM.title.value.length<1 || document.MYFORM.title.value.length>200)
	{
	alert('名称请控制 1~100 字节!');
	document.MYFORM.title.focus();
	return false;
	}
	if(document.MYFORM.content.value.length<1 || document.MYFORM.content.value.length>60000)
	{
	alert('详细请控制 1~50000 字节!');
	oEditor.focus();
	return false;
	}
}
</script>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<?php
$rt = $db->query("SELECT bkid,title,content FROM ".__TBL_GROUP_WZ__." WHERE id='$classid'");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
	$bkid = $row[0];
	$title = $row[1];
	$content = dataIO($row[2],'out');
} else {
	callmsg("不存在或已被删除！","-1");
	exit;
}
?>
<br>
<form action="group_wz_mod.php" method="post" name="MYFORM" onSubmit="return chkform()" >
 <table width="750" border="0" align="center" cellpadding="5" cellspacing="0">
       
          <tr>
            <td width="48" align="right"><font color="6699CC"><b>标题</b>：</font></td>
            <td width="580" valign="top"><input name="title" type="text" class="input size2" value="<?php echo stripslashes($title);?>" size="80" maxlength="200"></td>
            <td width="92" align="right" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="3" align="center"></td>
          </tr>
          <tr>
            <td colspan="3"><textarea name="content" id="content" class="textarea_k" style="width:100%;height:400px" ><?php echo $content; ?></textarea></td>
          </tr>
          <tr>
            <td colspan="3" align="center"><input name="submitok" type="hidden" value="modupdate">
<input name="classid" type="hidden" value="<?php echo $classid;?>">
<input type="submit" name="Submit" value=" 保存 " class="btn size3"></td>
          </tr>
        
</table>
</form>
      <br>
      <br>
<?php require_once "bottomadm.php";?>

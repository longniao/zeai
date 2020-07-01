<?php
require_once '../sub/init.php';
require_once "chkUadm.php";
switch ($submitok){
	case "addupdate":
		if ( str_len(trimm($form_title)) < 1 )alert_adm("请输入名称1~50字节","-1");
		$form_title = dataIO(trimm($form_title),'in');
		$rt = $db->query("SELECT id FROM ".__TBL_GROUP_TOTAL__." WHERE title='$form_title'");
		$total = $db->num_rows($rt);
		if ($total > 0)alert_adm("已经存在该圈子分类，添加失败!","-1");
		$addtime  = date("Y-m-d H:i:s");
		$db->query("INSERT INTO ".__TBL_GROUP_TOTAL__." (title,flag) VALUES ('$form_title',1)");
	break;
	case "delupdate":
		if ( !preg_match("/^[0-9]{1,6}$/",$classid) )alert_adm("Forbidden","-1");
		if (!empty($bknum))alert_adm("请选清除它的圈子！","-1");
		$db->query("DELETE FROM ".__TBL_GROUP_TOTAL__." WHERE id='$classid'");
		break;
	case "modupdate":
		if ( str_len(trimm($form_title)) <1 )alert_adm("请输入名称1~50字节","-1");
		$form_title = trimm($form_title);
		if ( !preg_match("/^[0-1]{1}$/",abs($ifflag)) )alert_adm("状态必须为数字","-1");
		if ( !preg_match("/^[0-9]{1,9}$/",$form_px) )alert_adm("排序必须为数字","-1");
		if ( !preg_match("/^[0-9]{1,6}$/",$classid) )alert_adm("Forbidden","-1");
		$rt = $db->query("SELECT title,flag FROM ".__TBL_GROUP_TOTAL__." WHERE id='$classid'");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			$row = $db->fetch_array($rt);
			$oldtitle = $row[0];
			$oldflag = $row[1];
		} else {
			alert_adm("Forbidden!","-1");
		}
		//if ( ($oldtitle == $form_title) && ($oldflag == $ifflag) )alert_adm("已经存在该圈子分类，修改失败!","-1");
		$db->query("UPDATE ".__TBL_GROUP_TOTAL__." SET title='$form_title',px='$form_px',flag='$ifflag' WHERE id='$classid'");
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>

<body>
<div class="navbox">

    <a href="group_total.php" class="ed">分类<?php echo '<b>'.$db->COUNT(__TBL_GROUP_TOTAL__).'</b>';?></a>
    <a href="group_list.php">圈子管理</a>
    <a href="group_photo.php">圈子相册</a>
    <a href="group_wz.php">主题文章管理</a>
    <a href="group_wz_bbs.php">文章评论</a>
    <a href="group_club.php">活动管理</a>
    <a href="group_club_photo.php">活动照片</a>
    <a href="group_club_bbs.php">活动评论</a>

<div class="clear"></div></div>

<div class="fixedblank"></div>



<?php if ($submitok == "add") {?>
<br>
<br>
<table width="500" height="116" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#dddddd">
  <form action="<?php echo $SELF;?>" method="post">
    <tr>
      <td width="127" align="right" bgcolor="#f8f8f8" class="B S14"><font color="#999999">圈子分类名称：</font></td>
      <td width="346" align="left" bgcolor="#FFFFFF"><font color="#666666">
        <input name="form_title" type="text" class="input size2" id="form_title" size="30" maxlength="20">
      </font></td>
    </tr>
    <tr>
      <td align="right" bgcolor="#f8f8f8">&nbsp;</td>
      <td align="left" bgcolor="#FFFFFF"><input type="submit" name="Submit" value=" 确认并保存 " class="btn size3">
          <font color="#666666">
          <input name="submitok" type="hidden" value="addupdate">
        </font></td>
    </tr>
  </form>
</table>
<br>
<br>
<?php }else{?>
<?php
$rt = $db->query("SELECT * FROM ".__TBL_GROUP_TOTAL__." ORDER BY px DESC,id DESC");
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	if ($submitok !== "add")echo "Sorry! 暂无圈子分类。　　　　　<a href=".$_SERVER['PHP_SELF']."?submitok=add><b>点此添加</b></a>";
} else {    
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
<table width="98%" height="30" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:10px">
  <tr>
    <td width="12%" align="left"><input type="button"  value="添加圈子分类" onClick="window.open('group_total.php?submitok=add','_self')" class="btn"></td>
    <td align="right" style="padding-right:5px;"><table width="95%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="34" align="right"></td>
      </tr>
    </table></td>
  </tr>
</table>
<table class="tablelist" style="margin-top:10px;margin-bottom:20px">
  <tr bgcolor="#FFFFFF">
    <th width="50" height="20" align="center" >ID</td>
    <th width="100" align="center" >状态</td>
    <th width="260" align="center" >分类名称 　/ 　排序</td>
    <th width="100" align="center" >圈子数量</td>
    <th align="center" >&nbsp;</td>
    <th width="35" align="center" >删除</td>
  </tr>
<?php
//For循环开始输出
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$bg="bgcolor=#ffffff";
		$overbg="#f8f8f8";
		$outbg="#ffffff";
?>
<form action="group_total.php?p=<?php echo $p; ?>" method=post>
  <tr <?php echo $bg;?> onMouseOver="this.style.background='<?php echo $overbg; ?>'" onMouseOut="this.style.background='<?php echo $outbg; ?>'">
    <td width="50" height="26" align="center"><font color="#666666"><?php echo $rows['id'];?></font></td>
    <td width="100" align="center" >
<select name="ifflag" class="select size2 W80">
<option value="1" style="color:green;" <?php if ($rows['flag'] == 1)echo "selected";?>>正常</option>
<option value="-1" style="color:blue;" <?php if ($rows['flag'] == -1)echo "selected";?>>隐藏</option>
</select>	  </td>
    <td width="260" align="center" ><input name="form_title" type="text" class="input size2 W100" id="form_title" value="<?php echo $rows['title']; ?>" size="20" maxlength="20">
      <input name="form_px" type="text" class="input size2 W80" id="form_px" value="<?php echo $rows['px']; ?>" size="9" maxlength="9">
      <input type="submit" name="Submit" value="修改" class="btn"><input type="hidden" name="submitok" value="modupdate" >
      <input type="hidden" name="classid" value="<?php echo $rows['id']; ?>" ></td>
    <td width="100" align="center" ><font face="Verdana"><?php echo $rows['bknum'];?></font></td>
    <td align="center" >&nbsp;</td>
    <td width="35" align="center" ><a href="group_total.php?submitok=delupdate&classid=<?php echo $rows['id']; ?>&bknum=<?php echo $rows['bknum'];?>" onClick="return confirm('请 慎 重 ！\n\n★确认删除？\n\n请先删除它下面的所有圈子。')"><img src="images/delx.gif" alt="删除" width="10" height="10" border="0"></a></td>
  </tr>
</form>
<?php } ?>
</table>
<table width="98%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
    <td align="right"><?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></td>
  </tr>
</table>

<?php }} ?><br><br>
<?php
require_once "bottomadm.php";
?>

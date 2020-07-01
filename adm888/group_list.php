<?php
require_once '../sub/init.php';
require_once "chkUadm.php";
if(!in_array('group',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
if ($submitok == "modupdate") {
	if ( !preg_match("/^[0-9]{1,6}$/",$classid) )alert_adm("Forbidden","-1");
	if (empty($qkind))alert_adm("请选择圈子分类!","-1");
	$totalid = explode(",",$qkind);
	$totaltitle = $totalid[1];
	$totalid = $totalid[0];
	if ( !preg_match("/^[0-9]{1,6}$/",$totalid) || empty($totalid) )alert_adm("Forbidden!","-1");
	if ( !preg_match("/^[0-9]{1,6}$/",$oldtotalid) || empty($oldtotalid) )alert_adm("Forbidden!","-1");
	if (str_len($totaltitle)<1 || str_len($totaltitle)>50)alert_adm("Forbidden!","-1");
	$title = trimm($title);
	if (str_len($title)<1 || str_len($title)>50)alert_adm("圈子名称请控制在1~50字节！","-1");
	if (str_len($content)<10 || str_len($content)>8000)alert_adm("圈子介绍请控制在10~8000字节！","-1");
	if ( !preg_match("/^[1-5]{1}$/",$qgrade) )alert_adm("Forbidden","-1");
	if ( !preg_match("/^[0-9]{1,9}$/",$qloveb) )alert_adm("Forbidden","-1");
	if (!is_numeric($ifflag)){
		alert_adm("Forbidden","-1");
	} else {
		$flag = $ifflag;
	}
	if ( !preg_match("/^[0-9]{1,9}$/",$px) )alert_adm("Forbidden","-1");
	if ( !preg_match("/^[0-9]{1,9}$/",$form_userid) ) {
		alert_adm("Forbidden","-1");
	} else {
		$userid = $form_userid;
	}
	$rt = $db->query("SELECT nickname,sex,grade,photo_s FROM ".__TBL_USER__." WHERE id='$userid'");
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$nicknamesexgradephoto_s = $row[0]."|".$row[1]."|".$row[2]."|".$row[3];
	} else {
		alert_adm("圈主ID不存在!","-1");
	}
	$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET totalid='$totalid',totaltitle='$totaltitle',title='$title',content='$content',qgrade='$qgrade',qloveb='$qloveb',flag='$flag',px='$px',userid='$userid',ifin='$ifin' WHERE id='$classid'");
	$db->query("UPDATE ".__TBL_GROUP_TOTAL__." SET bknum=bknum+1 WHERE id='$totalid'");
	$db->query("UPDATE ".__TBL_GROUP_TOTAL__." SET bknum=bknum-1 WHERE id='$oldtotalid'");
	header("Location: group_list.php?p=".$p);
} elseif ($submitok == "delupdate") {
	if ( !preg_match("/^[0-9]{1,9}$/",$classid) )alert_adm("Forbidden","-1");
	if ( !preg_match("/^[0-9]{1,6}$/",$totalid) )alert_adm("Forbidden","-1");
	$db->query("DELETE FROM ".__TBL_GROUP_CLUB_BBS__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_CLUB_USER__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_CLUB__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_PHOTO_KIND__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_WZ_BBS__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_WZ__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_BK__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_USER__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_LINKS__." WHERE mainid='$classid'");
	$db->query("DELETE FROM ".__TBL_GROUP_MAIN__." WHERE id='$classid'");
	$db->query("UPDATE ".__TBL_GROUP_TOTAL__." SET bknum=bknum-1 WHERE id='$totalid'");
	$mainid = $classid;
	$rt = $db->query("SELECT path_s,path_b FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE mainid='$mainid'");
	$total = $db->num_rows($rt);
	if ($total > 0 ) {
		for($i=1;$i<=$total;$i++) {
			$row = $db->fetch_array($rt);
			if(!$row) break;
			$path1= $row[0];
			$path2= $row[1];
			up_send_admindel($path1.'|'.$path2);
		}
	}
	$rt = $db->query("SELECT path_s,path_b FROM ".__TBL_GROUP_PHOTO__." WHERE mainid='$mainid'");
	$total = $db->num_rows($rt);
	if ($total > 0 ) {
		for($i=1;$i<=$total;$i++) {
			$row = $db->fetch_array($rt);
			if(!$row) break;
			$path1= $row[0];
			$path2= $row[1];
			up_send_admindel($path1.'|'.$path2);
		}
	}
	$db->query("DELETE FROM ".__TBL_GROUP_PHOTO__." WHERE mainid='$mainid'");
	$db->query("DELETE FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE mainid='$mainid'");
	header("Location: group_list.php?p=".$p);
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
    <a href="group_total.php">分类</a>
    <a href="<?php echo $SELF; ?>" class="ed">圈子管理<?php echo '<b>'.$db->COUNT(__TBL_GROUP_MAIN__).'</b>';?></a>
    <a href="group_photo.php">圈子相册</a>
    <a href="group_wz.php">主题文章管理</a>
    <a href="group_wz_bbs.php">文章评论</a>
    <a href="group_club.php">活动管理</a>
    <a href="group_club_photo.php">活动照片</a>
    <a href="group_club_bbs.php">活动评论</a>
    <div class="Rsobox">
      <form name="form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按圈子名称搜索">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>     
    </div>
<div class="clear"></div></div>

<div class="fixedblank"></div>



<?php
$SQL = "";
$Skeyword = dataIO($Skeyword,'in');
if (!empty($Skeyword))$SQL   .= " AND (( title LIKE '%".$Skeyword."%' )) ";
$rt = $db->query("SELECT * FROM ".__TBL_GROUP_MAIN__." WHERE 1=1 ".$SQL." ORDER BY px DESC,id DESC");
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodatatips'><span>Sorry!</span> 　...暂无信息...</div>";
} else {    
	$page_skin=2;$pagemode=3;$pagesize=5;require_once ZEAI.'sub/page.php';
?>

<table class="tablelist" style="margin-bottom:20px">
  <tr bgcolor="#FFFFFF">
    <th width="70" height="20" align="center" bgcolor="#FFFFFF">圈子ID</td>
    <th width="180" align="center" bgcolor="#FFFFFF">类别/浏览文章权限</td>
    <th width="60" align="center" bgcolor="#FFFFFF">状态</td>
    <th width="60" align="center" bgcolor="#FFFFFF">等级</td>
    <th align="left" bgcolor="#FFFFFF">圈子名称 / 排序 / 说明</td>
    <th width="80" align="center" bgcolor="#FFFFFF">圈主</td>
    <th width="60" align="center" bgcolor="#FFFFFF">圈子财富</td>
    <th width="50" align="center" bgcolor="#FFFFFF">&nbsp;</td>
    <th width="60" align="center" bgcolor="#FFFFFF">成员</td>
    <th width="60" align="center" bgcolor="#FFFFFF">主题</td>
    <th width="60" align="center" bgcolor="#FFFFFF">回帖</td>
    <th width="60" align="center" bgcolor="#FFFFFF">人气</td>
    <th width="70" align="center" bgcolor="#FFFFFF">创建时间</td>
    <th width="30" align="center" bgcolor="#FFFFFF">删除</td>
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
<form action="group_list.php?p=<?php echo $p; ?>" method=post>
  <tr <?php echo $bg;?> onMouseOver="this.style.background='<?php echo $overbg; ?>'" onMouseOut="this.style.background='<?php echo $outbg; ?>'">
    <td width="70" height="26" align="center" class="f14">
<a href="<?php echo Href('group',$rows['id']); ?>" target="_blank"><img src="images/zoom.gif"><?php echo $rows['id'];?></a></td>
    <td width="180" align="left" >
      <select name="qkind" id="qkind" class="W150" style="margin-bottom:5px">
<?php
$rt2=$db->query("SELECT id,title FROM ".__TBL_GROUP_TOTAL__." ORDER BY px DESC,id DESC");
$total2 = $db->num_rows($rt2);
if ($total2 <= 0) {
	echo "暂无";
} else {
?>
<?php
	for($j=0;$j<$total2;$j++) {
		$rows2 = $db->fetch_array($rt2);
		if(!$rows2) break;
		if ( $rows2[0] == $rows['totalid'] ) {
			echo "<option value=".$rows2[0].",".$rows2[1]." selected>".$rows2[1]."</option>";
		} else {
			echo "<option value=".$rows2[0].",".$rows2[1].">".$rows2[1]."</option>";
		}
	}
}
?>
</select><br>
<input name="ifin" type="radio" value="1" <?php if ($rows['ifin'] == 1)echo "checked";?> class="radio">不限<input name="ifin" type="radio" value="0" <?php if ($rows['ifin'] == 0)echo "checked";?> class="radio">圈内成员</td>
<td width="60" align="center" ><select name="ifflag" style="font-size:9pt;">
<option value="0" style="color:red;" <?php if ($rows['flag'] == 0)echo "selected";?>>未审</option>
<option value="1" style="color:green;" <?php if ($rows['flag'] == 1)echo "selected";?>>正常</option>
<option value="-1" style="color:blue;" <?php if ($rows['flag'] == -1)echo "selected";?>>隐藏</option>
</select></td>
    <td width="60" align="center" ><select name="qgrade">
      <option value="1" <?php if ($rows['qgrade'] == 1)echo "selected";?>>1</option>
      <option value="2" <?php if ($rows['qgrade'] == 2)echo "selected";?>>2</option>
      <option value="3" <?php if ($rows['qgrade'] == 3)echo "selected";?>>3</option>
      <option value="4" <?php if ($rows['qgrade'] == 4)echo "selected";?>>4</option>
      <option value="5" <?php if ($rows['qgrade'] == 5)echo "selected";?>>5</option>
    </select></td>
    <td align="left" style="padding:20px 0"><input name="title" type="text" class="input" value="<?php echo stripslashes($rows['title']); ?>" size="20" maxlength="50" style="color:#000000;font-weight:bold;"> 
    <input name="px" type="text" class="input" id="px" value="<?php echo $rows['px']; ?>" size="5" maxlength="9"><br>
      
      <textarea name="content" cols="30" rows="4" id="content" class="W300" style="margin-top:5px"><?php echo stripslashes($rows['content'])?></textarea>
      <input type="hidden" name="classid" value="<?php echo $rows['id']; ?>" >
	  <input type="hidden" name="oldtotalid" value="<?php echo $rows['totalid']; ?>" ><input type="hidden" name="submitok" value="modupdate" >	  </td>
    <td width="80" align="center" valign="top" style="padding:20px 0">
    <input name="form_userid" type="text" class="input" id="form_userid" value="<?php echo $rows['userid']; ?>" size="8" maxlength="9" style="margin-bottom:5px">
      <br>
<a href="<?php echo Href('u',$rows['userid']); ?>" class=u333333 target=_blank>
<?php

$rtD=$db->query("SELECT sex,grade,nickname FROM ".__TBL_USER__." WHERE id=".$rows['userid']);
if ($db->num_rows($rtD)){
	$rowD = $db->fetch_array($rtD);
	$sexD  = $rowD[0];
	$gradeD  = $rowD[1];
	$nicknameD  = dataIO($rowD[2],'out');
	echo uicon($sexD.$gradeD);
	echo $nicknameD;
}



?></a><br>
<?php if(!empty($rows['jjpmprice'])){?>
<table width="100%" height="36" border="0" cellpadding="0" cellspacing="1" bgcolor="ffcc00">
  <tr>
    <td height="34" align="center" bgcolor="ffffcc" style="color:#FF6600"><img src="images/loveb.gif" width="12" height="11">正在竞价:<br>
        <font color="#FF0000"><?php echo $rows['jjpmprice']; ?></font> 个</td>
  </tr>
</table>
<?php }?></td>
    <td width="60" align="center" valign="top"  style="padding:20px 0">
      <input name="qloveb" type="text" class="input" id="qloveb" value="<?php echo $rows['qloveb']; ?>" size="5" maxlength="8"></td>
    <td width="50" align="center" valign="top"  style="padding:20px 0">
      <input type="submit" name="Submit" value="修改" class="btn">
    </td>
    <td width="60" align="center" valign="middle" ><font color="#FF0000"><?php echo $rows['allusrnum'];?></font></td>
    <td width="60" align="center" valign="middle" ><font color="#FF0000"><?php echo $rows['wznum'];?></font></td>
    <td width="60" align="center" valign="middle" ><font color="#FF0000"><?php echo $rows['bbsnum'];?></font></td>
    <td width="60" align="center" valign="middle" ><font color="#FF0000"><?php echo $rows['click'];?></font></td>
    <td width="70" align="center" valign="middle"  style="font-size:10px"><?php
echo $rows['addtime'];
?></td>
    <td width="30" align="center" valign="middle" ><a href="group_list.php?submitok=delupdate&classid=<?php echo $rows['id']; ?>&totalid=<?php echo $rows['totalid']; ?>&p=<?php echo $p; ?>" onClick="return confirm('请 慎 重 ！\n\n★确认删除？\n\n此操作将联动删除帖子和照片。建议修改。')"><img src="images/delx.gif" alt="删除" border="0"></a></td>
  </tr>
</form>
<?php } ?>
</table>
<table width="98%" height="30" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="12%">&nbsp;</td>
    <td align="right"><?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
  </tr>
</table><br>
<br>
<br>

<?php } ?>
<?php
require_once "bottomadm.php";
?>
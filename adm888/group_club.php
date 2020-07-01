<?php
require_once '../sub/init.php';
require_once "chkUadm.php";
require_once ZEAI.'sub/zeai_up_func.php';
switch ($submitok){
	case "√批量审核":
		$tmeplist = $list;
		if (empty($tmeplist))alert_adm("请选择您要审核的信息！","-1");
		if (!is_array($tmeplist))alert_adm("Forbidden!","-1");
		if (count($tmeplist) >= 1){
			foreach ($tmeplist as $value) {
				$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET flag=1 WHERE id='$value'");
			}
		}
	break;
	case "delupdate":
		if ( !preg_match("/^[0-9]{1,9}$/",$clubid) )alert_adm("Forbidden!","-1");
		$db->query("DELETE FROM ".__TBL_GROUP_CLUB_BBS__." WHERE clubid='$clubid'");
		$db->query("DELETE FROM ".__TBL_GROUP_CLUB__." WHERE id='$clubid'");
		$rt = $db->query("SELECT path_s,path_b FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE clubid='$clubid'");
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
		$db->query("DELETE FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE clubid='$clubid'");
		header("Location: group_club.php?p=".$p);
	break;
	case "jh0":
		if ( !preg_match("/^[0-9]{1,10}$/",$classid) )alert_adm("Forbidden!","-1");
		$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET ifjh=0 WHERE id='$classid'");
		header("Location: group_club.php?p=".$p);
	break;
	case "jh1":
		if ( !preg_match("/^[0-9]{1,10}$/",$classid) )alert_adm("Forbidden!","-1");
		$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET ifjh=1 WHERE id='$classid'");
		header("Location: group_club.php?p=".$p);
	break;
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
<style>
.zoompic{vertical-align:middle;width:20px;height:20px}
</style>
<body>
<div class="navbox">


    <a href="group_total.php">分类</a>
    <a href="group_list.php">圈子管理</a>
    <a href="group_photo.php">圈子相册</a>
    <a href="group_wz.php">主题文章管理</a>
    <a href="group_wz_bbs.php">文章评论</a>
    <a href="group_club.php" class="ed">活动管理<?php echo '<b>'.$db->COUNT(__TBL_GROUP_CLUB__).'</b>';?></a>
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
$tmpsql="";
$Skeyword = trimm($Skeyword);
if (!empty($Skeyword)){
	$tmpsql = " WHERE title LIKE '%".dataIO($Skeyword,'in')."%' ";
}
$rt = $db->query("SELECT id,mainid,maintitle,title,flag,addtime,bmnum,gbooknum,ifjh FROM ".__TBL_GROUP_CLUB__.$tmpsql." ORDER BY id DESC");
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodatatips'><span>Sorry!</span> 　...暂无信息...</div>";
} else {    

	$page_skin=2;$pagemode=3;$pagesize=5;require_once ZEAI.'sub/page.php';
?>
<form name="FORM" method="post" action="<?php echo $SELF; ?>">
<script>
var checkflag = "false";
var bg='';
var bg1      = '<?php echo $_Style['list_bg']; ?>';
var bg2      = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/list.js"></script>
<table class="tablelist" style="margin-top:10px;margin-bottom:20px">
  <tr bgcolor="#FFFFFF">
    <th width="70" height="20" align="left" >&nbsp;</td>
    <th width="60" align="center" >精华</td>
    <th width="170" align="center" >来自圈子</td>
    <th align="left" >　　活动名称</td>
    <th width="56" align="center" >&nbsp;</td>
    <th width="88" align="center" >活动状态</td>
    <th width="52" align="center" >  报名人数</td>
    <th width="32" align="center" >留言</td>
    <th width="100" align="center" >日期</td>
    <th width="14" align="center" >删</td>
  </tr>
<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$id = $rows['id'];
		$tmpnickname = explode("|",$rows['nicknamesexgradephoto_s']);
		$nickname = $tmpnickname[0];
		$grade = $tmpnickname[1].$tmpnickname[2];
?>
<tr <?php echo tr_mouse($i,$id);?>>
    <td width="70"><input type="checkbox" name=list[] value="<?php echo $id; ?>"  id="chk<?php echo $id; ?>" > <label for="chk<?php echo $id; ?>"><?php echo $id; ?></label></td>
    <td width="60" align="center">
<?php if ($rows['ifjh'] == 1) {?><a href="group_club.php?classid=<?php echo $rows['id']; ?>&submitok=jh0&p=<?php echo $p; ?>" class="uFF5494"><img src="images/jh.gif" width="15" height="15" hspace="3" border="0" align="absmiddle"> 取消</a><?php } else {  ?><a href="group_club.php?classid=<?php echo $rows['id']; ?>&submitok=jh1&p=<?php echo $p; ?>"><font color="#009900">设为精华</font></a><?php }?></td>
<td width="170" align="center" style="padding:10px 0">
<?php echo "<a href=".Href('group',$rows['mainid'])." class=uFF5494 target=_blank>".htmlout(stripslashes($rows['maintitle']))."</a>";?></td>
    <td><a href="<?php echo Href('group_party',$rows['id']); ?>" target="_blank" class=u000000><img src="images/zoom.png" class="zoompic"> <?php echo str_replace($_SESSION['adminkeyword'],"<font color=red><b>".$_SESSION['adminkeyword']."</b></font>",stripslashes($rows['title'])); ?></a>      <?php if ($rows['flag'] == 1)echo "<img src=images/new2.gif hspace=6 border=0 align=absmiddle>"; ?></td>
    <td width="56"><a href="javascript:zeai.iframe('修改【<?php echo htmlout(stripslashes($rows['maintitle'])); ?>】','group_club_mod.php?classid=<?php echo $rows['id'];?>',800,700);"><img src="images/mod.gif" width="53" height="24" hspace="5" vspace="5" border="0" align="absmiddle"></a></td>
    <td width="88" align="center"><?php 
switch ($rows['flag']){ 
	case 0:
		echo "<font color=red>还未审核</font>";
	break;
	case 1:
		echo "<font color=0066CC>正在报名中</font>";
	break;
	case 2:
		echo "<font color=ff6600>活动进行中</font>";
	break;
	case 3:
		echo "<font color=349933>圆满结束</font>";
	break;
}
?></td>
    <td align="center" ><font color="#FF0000"><?php echo $rows['bmnum'];?></font> 人</td>
    <td align="center" ><font color="#FF0000"><?php echo $rows['gbooknum'];?></font></td>
    <td width="100" align="center">
<?php
$d1 = strtotime($rows['addtime']);
$d2 = $ADDTIME;
if (($d2-$d1) < 86400 )echo "<b><font color=red>";
echo $rows['addtime'];
?></td>
    <td width="14" align="center"><a href="group_club.php?submitok=delupdate&clubid=<?php echo $rows['id']; ?>&p=<?php echo $p; ?>" onClick="return confirm('请 慎 重 ！\n\n★你真的要删除吗？')"><img src="images/delx.gif" width="10" height="10" border="0"></a></td>
  </tr>
<?php } ?>
</table>

<table class="table0 W98_ Mtop10 Mbottom20">
  <tr>
    <td width="300" align="left" class="list_page">
     <label for="chkall"><input type="checkbox" name="chkall" value="" id="chkall" class="checkbox" onclick="chkformall(this.form)"><span id="chkalltext">全选</span></label>　
    <input type="submit" name="submitok" value="√批量审核" class="btn size2" accesskey="d" onClick="return confirm('确认批量审核？')" />
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    </td>
    <td align="right" class="list_page"><?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
  </tr>
</table>

</form>
<?php } ?>
<?php require_once "bottomadm.php";

function tr_mouse($i,$id,$mode=''){
	global $_Style;
	$returnstr = '';
	$bg='';
	$bg1     = $_Style['list_bg'];//id=1d
	$bg2     = $_Style['list_bg'];//id=2
	$overbg  = $_Style['list_overbg'];//MouseOver
	$selectbg= $_Style['list_selectbg'];//Selected
	if ($i % 2 == 0){
		$bg=$bg1;
	} else {
		$bg=$bg2;
	}
	$returnstr .= ' bgcolor='.$bg.' onmouseover="this.style.backgroundColor=\''.$overbg.'\'" onmouseout="this.style.backgroundColor=\''.$bg.'\'" ';
	if (empty($mode)){
		$returnstr .= " onclick=\"chkbox(".$i.",".$id.")\" id=\"tr".$i."\"";
	}
	return $returnstr;
}

?>

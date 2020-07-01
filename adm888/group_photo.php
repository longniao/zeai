<?php
require_once '../sub/init.php';
require_once "chkUadm.php";
require_once ZEAI.'sub/zeai_up_func.php';
if ($submitok == "批量删除"){
	$tmeplist = $list;
	if (empty($tmeplist))alert_adm("请选择您要删除的信息！","-1");
	if (!is_array($tmeplist))alert_adm("Forbidden!","-1");
	if (count($tmeplist) >= 1){
		foreach ($tmeplist as $value) {
			if ( !preg_match("/^[0-9]{1,10}$/",$value) ){echo $value;exit;}
			$rt = $db->query("SELECT mainid,path_s,path_b,ifmain FROM ".__TBL_GROUP_PHOTO__." WHERE id=".$value);
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt);
				$mainid = $row[0];
				$path1    = $row[1];
				$path2    = $row[2];
				$ifmain   = $row[3];
				up_send_admindel($path1.'|'.$path2);
				if ($ifmain==1)$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET picurl_s='' WHERE id='$mainid'");
			}
			$db->query("DELETE FROM ".__TBL_GROUP_PHOTO__." WHERE id=".$value);
		}
		header("Location: $SELF?p=".$p);
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
<style>
.photo120X90 img{max-width:120px;max-height:90px}
</style>
<body>
<div class="navbox">


    <a href="group_total.php">分类</a>
    <a href="group_list.php">圈子管理</a>
    <a href="group_photo.php" class="ed">圈子相册<?php echo '<b>'.$db->COUNT(__TBL_GROUP_PHOTO__).'</b>';?></a>
    <a href="group_wz.php">主题文章管理</a>
    <a href="group_wz_bbs.php">文章评论</a>
    <a href="group_club.php">活动管理</a>
    <a href="group_club_photo.php">活动照片</a>
    <a href="group_club_bbs.php">活动评论</a>




<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php 
$rt=$db->query("SELECT * FROM ".__TBL_GROUP_PHOTO__." ORDER BY id DESC LIMIT ".$_ZEAI['limit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodatatips'><span>Sorry!</span> 　...暂无信息...</div>";
} else {    
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
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

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
	$rows = $db->fetch_array($rt);
	if(!$rows) break;
	$id = $rows['id'];
?>
    <td align="center" valign="top" bgcolor="#FFFFFF" style="padding-top:10px;">
    <table width="130" border="0" cellpadding="2" cellspacing="0" style="border:#dddddd 1px solid;" <?php echo tr_mouse($i,$id);?>>
      <tr>
        <td height="110" colspan="2" align="center" <?php echo  $bg; ?>>
<div class=photo120X90>
<a href="###" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".$rows['path_b']; ?>')" ><img src=<?php echo $_ZEAI['up2']."/";?>/<?php echo  $rows['path_s']; ?> alt="放大照片" border="0"></a></div></td>
      </tr>
      <tr>
        <td height="18" colspan="2" align="center"  <?php echo  $bg; ?>><font color="#666666"><?php echo  $rows['title']; ?></font></td>
      </tr>
      <tr>
        <td height="18" colspan="2" align="center" class="time"><?php echo $rows['addtime']; ?></td>
      </tr>
      <tr>
        <td colspan="2" <?php echo  $bg; ?>>来自俱乐部：<b><a href="<?php echo Href('group',$rows['mainid']); ?>" target="_blank"><img src="images/zoom.gif" hspace="5" border="0" align="absmiddle" ><font color="#FF0000"><u><?php echo $rows['mainid']; ?></u></font></a></b></td>
      <tr>
        <td width="60" height="5" align="right" valign="bottom" bgcolor="#efefef" style="color:#ff0000" title="主照片"><?php if ($rows['ifmain'] == 1){echo '★★★';}?></td>
        <td width="90" align="right" bgcolor="#efefef"><input type=checkbox name=list[] value="<?php echo $rows['id']; ?>" id="chk<?php echo $rows['id']; ?>" style="border:0px;vertical-align: middle;"><label for="chk<?php echo $rows['id']; ?>"><font color="#999999">选择</font></label></td>
      </tr>
    </table></td>
    <?php if ($i % 6 == 0) {?>
  </tr>
  <tr>
    <?php } ?>
    <?php 	} ?>
  </tr>
</table>

<table class="table0 W95_ Mtop30 Mbottom20">
  <tr>
    <td width="300" align="left" class="list_page">
    <label for="chkall"><input type="checkbox" name="chkall" value="" id="chkall" class="checkbox" onclick="chkformall(this.form)"><span id="chkalltext">全选</span></label>　
    <input type="submit" name="submitok" value="批量删除" class="btn size2" onClick="return confirm('确认删除？')" />
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    </td>
    <td align="right" class="list_page"><?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></td>
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
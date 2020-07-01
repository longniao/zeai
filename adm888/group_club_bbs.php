<?php
require_once '../sub/init.php';
require_once "chkUadm.php";
if ($submitok=="批量删除"){
	$tmeplist = $list;
	if (empty($tmeplist))callmsg("请选择您要删除的信息！","-1");
	if (!is_array($tmeplist))callmsg("Forbidden!","-1");
	if (count($tmeplist) >= 1){
		foreach ($tmeplist as $value) {
			/*
			$rt = $db->query("SELECT fid,flag FROM ".__TBL_GROUP_WZ_BBS__." WHERE id='$value'");
			if(!$db->num_rows($rt)){
				callmsg("该评论不存在或已被删除！","-1");
			} else {
				$row = $db->fetch_array($rt);
				$fid = $row[0];
				$flag = $row[1];
			}
			*/
			//if ($flag == 0)callmsg("不要二次删除，请检查！","-1");
			//$db->query("UPDATE ".__TBL_GROUP_WZ__." SET hfnum=hfnum-1 WHERE id='$fid'");
			//$db->query("DELETE FROM ".__TBL_GROUP_WZ_BBS__." WHERE id='$value'");
			$db->query("UPDATE ".__TBL_GROUP_CLUB_BBS__." SET flag=0,content='' WHERE id='$value'");
		}
	}
}elseif ($submitok=="flag1"){
	if ( !strip_tags("/^[0-9]{1,10}$/",$classid) )callmsg("Forbidden!","-1");
	/*
	$rt = $db->query("SELECT fid FROM ".__TBL_GROUP_WZ_BBS__." WHERE id='$classid'");
	if(!$db->num_rows($rt)){
		callmsg("该评论不存在或已被删除！","-1");
	} else {
		$row = $db->fetch_array($rt);
		$fid = $row[0];
		$db->query("UPDATE ".__TBL_GROUP_WZ__." SET hfnum=hfnum+1 WHERE id='$fid'");
	}
	*/
	$db->query("UPDATE ".__TBL_GROUP_CLUB_BBS__." SET flag=1 WHERE id='$classid'");
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
.listC {TABLE-LAYOUT:fixed;WORD-BREAK: break-all;}
.listC img{max-width:150px;max-height:150px}
</style>
<body>
<div class="navbox">


    <a href="group_total.php">分类</a>
    <a href="group_list.php">圈子管理</a>
    <a href="group_photo.php">圈子相册</a>
    <a href="group_wz.php">主题文章管理</a>
    <a href="group_wz_bbs.php">文章评论</a>
    <a href="group_club.php">活动管理</a>
    <a href="group_club_photo.php">活动照片</a>
    <a href="group_club_bbs.php" class="ed">活动评论<?php echo '<b>'.$db->COUNT(__TBL_GROUP_CLUB_BBS__).'</b>';?></a>


    <div class="Rsobox">
      <form name="form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input" placeholder="按内容搜索">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btnLAN" />
      </form>     
    </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php 
$tmpsql="";
$Skeyword = trimm($Skeyword);
if (!empty($Skeyword)){
	$tmpsql = " WHERE content LIKE '%".dataIO($Skeyword,'in')."%' ";
}
$rt    = $db->query("SELECT * FROM ".__TBL_GROUP_CLUB_BBS__.$tmpsql." ORDER BY id DESC LIMIT ".$_ZEAI['limit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodatatips'><span>Sorry!</span> 　...暂无信息...</div>";
} else {    
	$page_skin=2;$pagemode=3;$pagesize=5;require_once ZEAI.'sub/page.php';

?>
<form name="FORM" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="tablelist" style="margin-top:20px;margin-bottom:20px">
<script>
var checkflag = "false";
var bg='';
var bg1      = '<?php echo $_Style['list_bg']; ?>';
var bg2      = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/list.js"></script>
  <tr bgcolor="#FFFFFF">
    <th width="70" height="20" align="left">&nbsp;</td>
    <th width="70" align="center">来自圈子</td>
    <th width="70" align="center">来自主题</td>
    <th align="center">评论内容</td>
    <th width="54" align="center">状态</td>
    <th width="120" align="center">作者</td>
    <th width="70" align="center">发表时间</td>
    <th width="70" align="center">&nbsp;</td>
  </tr>
<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$tmpnickname = explode("|",$rows['nicknamesexgrade']);
		$nickname = $tmpnickname[0];
		$grade = $tmpnickname[1].$tmpnickname[2];
		$id = $rows['id'];
?>
<tr <?php echo tr_mouse($i,$id);?>>
    <td width="70"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="chk<?php echo $id;?>" class="checkbox" onclick="chkbox(<?php echo $i;?>,<?php echo $id;?>)"><?php echo $id; ?></td>
    <td width="70" align="center"><a href="<?php echo Href('group',$rows['mainid']); ?>" target="_blank" class=u000000><img src="images/zoom.gif" width="13" height="13" hspace="3" border="0" align="absmiddle"><?php echo $rows['mainid']; ?></a></td>
    <td width="70" align="center"><a href="<?php echo Href('group_party',$rows['clubid']); ?>" target="_blank" class=u000000><img src="images/zoom.gif" width="13" height="13" hspace="3" border="0" align="absmiddle"><?php echo $rows['clubid']; ?></a></td>
    <td>
      
      
  <div class="listC S14">
  <?php echo str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",dataIO($rows['content'],'out')); ?>
  </div>
      
</td>
    <td width="54" align="center"><?php if ($rows['flag'] == 0){?><a href="group_club_bbs.php?submitok=flag1&classid=<?php echo $rows['id']; ?>"><font color="#0000FF"><u>已删除</u></font></a><?php }else{?><font color="#006600">正常</font><?php }?></td>
    <td width="120" align="center">
<a href="<?php echo Href('u',$rows['userid']); ?>" class=u333333 target=_blank>
<?php echo uicon($grade); ?>
<?php echo $nickname; ?></a></td>
    <td width="70" align="center" style="font-size:11px">
<?php
echo $rows['addtime'];
?></td>
    <td width="70" align="center"><a href="javascript:zeai.iframe('修改活动【<?php echo $rows['clubid'];?>】的评论','group_club_bbs_mod.php?classid=<?php echo $rows['id'];?>',600,400);"><img src="images/mod.gif" hspace="5" vspace="5" border="0"></a></td>
</tr>
<?php } ?>



</table>
<table class="table0 W98_ Mtop10 Mbottom20">
  <tr>
    <td width="300" align="left" class="list_page">
    <label for="chkall"><input type="checkbox" name="chkall" value="" id="chkall" class="checkbox" onclick="chkformall(this.form)"><span id="chkalltext">全选</span></label>　
    <input type="submit" name="submitok" value="批量删除" class="btn size2" accesskey="d" onClick="return confirm('确认删除？')" />
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    </td>
    <td align="right" class="list_page"><?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
  </tr>
</table></form>
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
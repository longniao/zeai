<?php
require_once '../sub/init.php';
require_once "chkUadm.php";

//圈子
$_ZEAI['group2']   = HOST."/m1/group";
$_ZEAI['GroupWZbbsAdd']   = 0;//发表主题或评论，检查个人资料是否通过审核，如果未审，则不能发表，0为不检查
$_ZEAI['GroupWZLoveb']    = 100;//发表主题帖增圈子财富，0为不加币
$_ZEAI['GroupBBSLoveb']   = 10;//发表评论增圈子财富，0为不加币
$_ZEAI['GroupMailLoveb']  = 10000;//圈主给成员群发站内留言花费爱豆，0为免费发布(免费会导致大量数据库垃圾，性能下降，严重会可能会崩溃)

switch ($submitok){
	case "批量审核":
		$tmeplist = $list;
		if (empty($tmeplist))callmsg("请选择您要审核的信息！","-1");
		if (!is_array($tmeplist))callmsg("Forbidden!","-1");
		if (count($tmeplist) >= 1){
			foreach ($tmeplist as $value) {
				$rtson = $db->query("SELECT userid,mainid,maintitle,title,flag FROM ".__TBL_GROUP_WZ__." WHERE id=".$value);
				if($db->num_rows($rtson)){
					$rowson = $db->fetch_array($rtson);
					$userid     = $rowson[0];
					$mainid     = $rowson[1];
					$maintitle  = $rowson[2];
					$title  = $rowson[3];
					$flag   = $rowson[4];
				}else{exit;}
				
				if ($flag == 0 ){
					$addloveb = $_ZEAI['GroupWZLoveb'];
					$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET qloveb=qloveb+".$addloveb." WHERE id=".$mainid);
					//动始
					/*
					$clsid = $value;
					$content = "在【".$maintitle."】圈子发表了一篇名为“".$title."”的帖子，<a href=".$_ZEAI['group_2domain']."/read".$clsid.".html target=_blank  class=uc03>点击查看</a>";
					$db->query("INSERT INTO ".__TBL_FRIEND_NEWS__."  (userid,content,addtime) VALUES ($userid,'$content',$ADDTIME)");
					*/
					$db->query("UPDATE ".__TBL_GROUP_WZ__." SET flag=1 WHERE id=".$value);
				}
				//动束
			}
		}
	break;
	case "批量删除":
		$tmeplist = $list;
		if (empty($tmeplist))callmsg("请选择您要删除的信息！","-1");
		if (!is_array($tmeplist))callmsg("Forbidden!","-1");
		if (count($tmeplist) >= 1){
			foreach ($tmeplist as $value) {
				$rtson = $db->query("SELECT mainid FROM ".__TBL_GROUP_WZ__." WHERE id=".$value);
				if($db->num_rows($rtson)){
					$rowson = $db->fetch_array($rtson);
					$mainid = $rowson[0];
				}else{exit('value');}
				$rtson = $db->query("SELECT qloveb FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid);
				if($db->num_rows($rtson)){
					$rowson = $db->fetch_array($rtson);
					$data_qloveb = $rowson[0];
				}else{$data_qloveb=0;}
				$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_WZ_BBS__." WHERE fid=".$value);
				$row = $db->fetch_array($rt);
				$tmpcnt = $row[0];
				if ( $data_qloveb > $_ZEAI['GroupWZLoveb'] ){
					$endqloveb = intval($data_qloveb - $_ZEAI['GroupWZLoveb']);
				}else{
					$endqloveb = 0;
				}
				$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET wznum=wznum-1,bbsnum=bbsnum-".$tmpcnt.",qloveb=".$endqloveb." WHERE id=".$mainid);
				$db->query("DELETE FROM ".__TBL_GROUP_WZ_BBS__." WHERE fid='$value'");
				$db->query("DELETE FROM ".__TBL_GROUP_WZ__." WHERE id='$value'");
			}
		}
		header("Location: group_wz.php");
	break;
	case "jh0":
		if ( !preg_match("/^[0-9]{1,10}$/",$classid) )callmsg("Forbidden!","-1");
		$db->query("UPDATE ".__TBL_GROUP_WZ__." SET ifjh=0 WHERE id='$classid'");
		header("Location: group_wz.php?p=".$p);
	break;
	case "jh1":
		if ( !preg_match("/^[0-9]{1,10}$/",$classid) )callmsg("Forbidden!","-1");
		$db->query("UPDATE ".__TBL_GROUP_WZ__." SET ifjh=1 WHERE id='$classid'");
		/*
		$rt = $db->query("SELECT userid,title FROM ".__TBL_GROUP_WZ__." WHERE id='$classid'");
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$memberid = $row[0];
			$wztitle = $row[1];
			$wztitle = badstr(strip_tags(stripslashes($wztitle)));
		} else {
			callmsg("Forbidden!","-1");
		}
		$rt = $db->query("SELECT id FROM ".__TBL_MAIN__." WHERE id='$memberid'");
		if(!$db->num_rows($rt)){
			callmsg("Forbidden!","-1");
		}
		$Uid = $memberid;
		$temploveb = abs(intval($_ZEAI['GroupWzJhLoveB']));
		$db->query("UPDATE ".__TBL_MAIN__." SET loveb=loveb+$temploveb WHERE id='$memberid'");
		require_once ZEAI.'sub/func_dbcls.php';$Zeaidbcls = new zeai_cn__dbcls;
		$Zeaidbcls->LovebHistory($Uid,'圈子文章［'.$wztitle.'］被管理员推荐奖励',$temploveb);
		*/
		header("Location: group_wz.php?p=".$p);
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
    <a href="group_wz.php" class="ed">主题文章管理<?php echo '<b>'.$db->COUNT(__TBL_GROUP_WZ__).'</b>';?></a>
    <a href="group_wz_bbs.php">文章评论</a>
    <a href="group_club.php">活动管理</a>
    <a href="group_club_photo.php">活动照片</a>
    <a href="group_club_bbs.php">活动评论</a>

    <div class="Rsobox">
      <form name="form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按内容搜索">
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
$rt    = $db->query("SELECT id,mainid,maintitle,bkid,bktitle,title,bbsnum,click,iftop,ifjh,flag,addtime,userid,nicknamesexgrade FROM ".__TBL_GROUP_WZ__.$tmpsql." ORDER BY id DESC LIMIT ".$_ZEAI['limit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodatatips'><span>Sorry!</span> 　...暂无信息...</div>";
} else {    
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
<form name="FORM" method="post" action="<?php echo SELF; ?>">
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
    <th width="70" height="20" align="left">&nbsp;</td>
    <th width="60" align="center">精华</td>
    <th width="120" align="center">来自圈子</td>
    <th width="120" align="center">子版块</td>
    <th align="center">文章标题</td>
    <th width="40" align="center">&nbsp;</td>
    <th width="40" align="center">状态</td>
    <th width="100" align="center">作者</td>
    <th width="66" align="center">评论/浏览</td>
    <th width="70" align="center">日期</td>
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
    <td width="70"><input type=checkbox name=list[] value="<?php echo $rows['id']; ?>"  id="chk<?php echo $rows['id']; ?>" onclick="chkbox(<?php echo $i; ?>,<?php echo $id; ?>)"> <label for="chk<?php echo $rows['id']; ?>"><?php echo $rows['id']; ?></label></td>
    <td width="60" align="center">
<?php if ($rows['ifjh'] == 1) {?><a href="group_wz.php?classid=<?php echo $rows['id']; ?>&submitok=jh0&p=<?php echo $p; ?>" class="uFF5494"><img src="images/jh.gif" width="15" height="15" hspace="3" border="0" align="absmiddle"> 取消</a><?php } else {  ?><a href="group_wz.php?classid=<?php echo $rows['id']; ?>&submitok=jh1&p=<?php echo $p; ?>"><font color="#009900">设为精华</font></a><?php }?></td>
<td width="120" align="center">
<?php echo "<a href=".Href('group',$rows['mainid'])." target=_blank>".htmlout(stripslashes($rows['maintitle']))."</a>";?></td>
    <td width="120" align="center"><?php echo htmlout(stripslashes($rows['bktitle']));?></td>
    <td align="left"><img src="images/zoom.png" class="zoompic"> <a href="<?php echo Href('group_wz',$rows['id']); ?>" target="_blank" class=u000000>
    
	<?php echo str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",dataIO($rows['title'],'out')); ?>
    
    </a></td>
    <td width="40" align="center"><a href="javascript:zeai.iframe('修改【<?php echo dataIO($rows['title'],'out');?>】','group_wz_mod.php?classid=<?php echo $rows['id'];?>',800,600);" ><img src="images/mod.gif" width="53" height="24" border="0"></a></td>
    <td width="40" align="center"><?php if ($rows['flag']==0){echo "<font color=#ff0000>未审</font>";}else{echo "<font color=#009900>已审</font>";} ?></td>
    <td width="100" align="center">
<a href="<?php echo Href('u',$rows['userid']); ?>" class=u333333 target=_blank>
<?php echo uicon($grade) ?>
<?php echo $nickname; ?></a></td>
    <td align="center" ><font color="#FF0000"><?php echo $rows['bbsnum']; ?></font> <font color="#999999">/</font> <font color="#FF0000"><?php echo $rows['click']; ?></font></td>
    <td width="70" align="center" style="font-size:12px;color:#999">
<?php
echo $rows['addtime'];
?></td>
    </tr>
<?php } ?>
</table>
<table class="table0 W98_ Mtop20 Mbottom20">
  <tr>
    <td width="300" align="left" class="list_page">
    <label for="chkall"><input type="checkbox" name="chkall" value="" id="chkall" class="checkbox" onclick="chkformall(this.form)"><span id="chkalltext">全选</span></label>　
    <input type="submit" name="submitok" value="批量删除" class="btn size2" onClick="return confirm('确认删除？')" />
    <input type="submit" name="submitok" value="批量审核" class="btn size2" onClick="return confirm('确认审核？')" />
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
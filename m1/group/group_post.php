<?php
require_once '../../sub/init.php';
require_once 'group_init.php';
header("Cache-control: private");
if ( !preg_match("/^[0-9]{1,8}$/",$mainid) || empty($mainid))callmsg("圈子不存在","-1");
if ( !preg_match("/^[0-9]{1,8}$/",$cook_uid) || empty($cook_uid))header("Location: ".HOST."/?z=my");
if ( (!preg_match("/^[0-9]{1,8}$/",$fid) || empty($fid)) && ($submitok == "mod" || $submitok == "modupdate") )callmsg("该帖子不存在或已被删除！","-1");
require_once ZEAI.'sub/conn.php';
$rt = $db->query("SELECT mbkind,title,ifin2,userid,userid1,userid2,userid3,qgrade FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid." AND flag=1");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'all');
	$mbkind = $row['mbkind'];
	$maintitle = dataIO($row['title'],'out');
	$ifin2 = $row['ifin2'];
	$userid_main = $row['userid'];
	$userid1_main = $row['userid1'];
	$userid2_main = $row['userid2'];
	$userid3_main = $row['userid3'];
	$qgrade_main  = $row['qgrade'];
	$userid_bk="NO";
	if (ifint($bkid)) {
		$rtbk = $db->query("SELECT userid,title FROM ".__TBL_GROUP_BK__." WHERE id=".$bkid);
		if($db->num_rows($rtbk)){
			$rowbk = $db->fetch_array($rtbk);
			$userid_bk = $rowbk[0];
			$bktitle   = $rowbk[1];
			if (!ifint($userid_bk))$userid_bk="NO";
		} else {
			callmsg("版块验证失败!","-1");
		}
	}
	if ($userid_main == $cook_uid || $userid1_main == $cook_uid || $userid2_main == $cook_uid || $userid3_main == $cook_uid || $cook_grade == 10 || $userid_bk == $cook_uid) {
		$authority_main = "OK";
	} else {
		$authority_main = "NO";
	}
} else {NoUserInfo();}
//
$rt = $db->query("SELECT id FROM ".__TBL_GROUP_BK__." WHERE mainid=".$mainid);
if(!$db->num_rows($rt)){
	if ($userid_main == $cook_uid){
		callmsg("还未创建圈子版块","my/group.php?submitok=bk&mainid=".$mainid);
	}else{
		callmsg("还未创建版块，发表失败！","-1");
	}
}
//
if ($submitok == "addupdate" || $submitok == "modupdate") {
	$tmpbk   = explode(",",$bkid);
	$bkid    = $tmpbk[0];
	$bktitle = $tmpbk[1];
	if (!ifint($bkid))callmsg("请选择圈子版块/分类!","-1");
	if (str_len($title)>100 || empty($title))callmsg("标题请控制在80个字节以内","-1");
	if (str_len($content)>5000 || str_len($content)<5)callmsg("内容请控制在5~5000字节","-1");
	if ($_ZEAI['GroupWZbbsAdd'] == 1){
		$ifzl = " AND flagmod=1 ";
	}else{
		$ifzl = "";
	}
	$rt = $db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$cook_uid." AND pwd='$cook_pwd' AND flag>0".$ifzl);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$nicknamesexgrade = $row[0]."|".$row[1]."|".$row[2];
		$data_grade = $row[2];
	} else {
		callmsg("资料不完整或未审核，请联系客服","-1");
	}
	if ($ifin2 == 0 && $submitok == "addupdate") {
		$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_USER__." WHERE userid=".$cook_uid." AND mainid=".$mainid." AND flag=1");
		$row = $db->fetch_array($rt);
		if ($row[0] <= 0)callmsg("请先加入本圈子","group_main.php?mainid=".$mainid);
	}
}
$addtime = YmdHis($ADDTIME);
if ($submitok == "addupdate") {
	//查黑
	$truebbs = 1;
	if (gzflag($cook_uid,$userid_main) == -1)$truebbs = 0;//有黑
	//$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_FRIEND__." WHERE flag=-1 AND userid=".$cook_uid." AND senduserid=".$userid_main);
	//$row = $db->fetch_array($rt);
	//if ($row[0] > 0)$truebbs = 0;//有黑
	if ($truebbs == 1) {
		$endtime = $addtime;
		$endnicknamesexgrade = $nicknamesexgrade;
		$addloveb = $_ZEAI['GroupWZLoveb'];
		$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET wznum=wznum+1,qloveb=qloveb+".$addloveb." WHERE id=".$mainid);
		//$flag = ($data_grade > 2 )?1:0;
		$flag = 1;
		$db->query("INSERT INTO ".__TBL_GROUP_WZ__."  (mainid,maintitle,bkid,bktitle,title,content,endtime,flag,enduserid,endnicknamesexgrade,addtime,userid,nicknamesexgrade) VALUES ('$mainid','$maintitle','$bkid','$bktitle','$title','$content','$endtime',$flag,'$cook_uid','$endnicknamesexgrade','$addtime','$cook_uid','$nicknamesexgrade')");
		//动始
		/*
		if ($flag == 1){
			$clsid   = $db->insert_id();
			$content = "在【".$maintitle."】圈子发表了一篇名为“".$title."”的帖子，<a href=".$_ZEAI['group2']."/read".$clsid.".html target=_blank  class=uc03>点击查看</a>";
			$db->query("INSERT INTO ".__TBL_FRIEND_NEWS__."  (userid,content,addtime) VALUES ($cook_uid,'$content',$ADDTIME)");
		}
		*/
		//动束
	}//黑结束
	header("Location: group_article.php?mainid=".$mainid."&bkid=".$bkid."&bktitle=".urlencode($bktitle));
} elseif ($submitok == "modupdate") {
	$title   = dataIO($title,'in');
	$content = dataIO($content,'in');
	$content = $content."<p align=right style=font-size:12px;color:#999999;>该帖于 ".$addtime." 被修改过。</p>";
	$db->query("UPDATE ".__TBL_GROUP_WZ__." SET mainid='$mainid',maintitle='$maintitle',bkid='$bkid',bktitle='$bktitle',title='$title',content='$content' WHERE flag=1 AND id=".$fid);
	header("Location: group_article.php?mainid=".$mainid."&bkid=".$bkid."&bktitle=".urlencode($bktitle));
}
if ($submitok == "mod") {
	if ( !preg_match("/^[0-9]{1,8}$/",$fid) || empty($fid))callmsg("请求错误，该帖子不存在或已被删除！","-1");
	$rt = $db->query("SELECT bkid,title,content,userid FROM ".__TBL_GROUP_WZ__." WHERE flag=1 AND id=".$fid);
	if($db->num_rows($rt)) {
		$row_wz = $db->fetch_array($rt);
		$userid_wz = $row_wz[3];
		if ( ($userid_wz !== $cook_uid) && $authority_main == "NO" )callmsg("请求错误，没有操作权限!","-1");
		$bkid_wz = $row_wz[0];
		$title_wz = dataIO($row_wz[1],'out');
		$content_wz = dataIO($row_wz[2],'out');
	} else {
		callmsg("请求错误，该帖子不存在或已被删除","-1");
		exit;
	}
}
//
$mini_show  = true;$mini_title = (empty($bktitle))?$maintitle:$bktitle;$nav='trend';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php if ($submitok == "mod") {echo "修改帖子"; }else{echo "发表帖子";} ?></title>
<?php echo $headmeta; ?>
<script src="www_zeai_cn.js?1"></script>
<link href="group.css" rel="stylesheet" type="text/css" />

</head>
<body>
<?php require_once 'top_mini.php';?>
<div class="post">
<?php if ($submitok == "mod") {?>
<form action="<?php echo $SELF; ?>" method="post" id="ZEAI_form_detail"  onSubmit="return chkform_detail()" class="form">
<table class="table0">
<tr>
  <td  align="left">
    <li><select name="bkid" id="bkid" class="select">
      <?php
$rt2=$db->query("SELECT id,title FROM ".__TBL_GROUP_BK__." WHERE mainid='$mainid' ORDER BY px DESC,id DESC");
$total2 = $db->num_rows($rt2);
if ($total2 <= 0) {
	echo "暂无";
} else {
?><option value="">选择主题分类</option>
<?php
	for($j=0;$j<$total2;$j++) {
		$rows2 = $db->fetch_array($rt2);
		if(!$rows2) break;
		if ($rows2[0] == $bkid_wz) {
			$tempselect = " selected ";
		} else {
			$tempselect = "";
		}
		echo "<option value=".$rows2[0].",".stripslashes($rows2[1]).$tempselect.">".stripslashes($rows2[1])."</option>";
	}
}
?></select></li>
    <li><input name="title" id="title" type="text" class="input" maxlength="60"  value="<?php echo $title_wz; ?>"></li>
	<li><textarea name="content" id="content" class="textarea textarea_k" ><?php echo $content_wz; ?></textarea></li>
    <input name="submitok" type="hidden" value="modupdate">
    <input name="mainid" type="hidden" value="<?php echo $mainid; ?>">
    <input name="fid" type="hidden" value="<?php echo $fid; ?>">
    <li><input type="submit" name="Submit" value="保存" class="btn2HUANG"></li>
    </td>
</tr>
</table>
</form>
<?php }else{  ?>


<form action="<?php echo $SELF; ?>" method="post" id="ZEAI_form_detail"  onSubmit="return chkform_detail()" class="form">
<table class="table0">
<tr>
<td align="left">
<li><select name="bkid" id="bkid" class="select" style="width:100%;line-height:36px;height:36px;box-sizing:border-box">
<?php
$rt2=$db->query("SELECT id,title FROM ".__TBL_GROUP_BK__." WHERE mainid='$mainid' ORDER BY px DESC,id DESC");
$total2 = $db->num_rows($rt2);
if ($total2 <= 0) {
echo "暂无";
} else {
?>
<option value="">选择主题分类</option>
<?php
for($j=0;$j<$total2;$j++) {
    $rows2 = $db->fetch_array($rt2);
    if(!$rows2) break;
    if ($rows2[0] == $bkid) {
        $tempselect = " selected ";
    } else {
        $tempselect = "";
    }
    echo "<option value=".$rows2[0].",".stripslashes($rows2[1]).$tempselect.">".stripslashes($rows2[1])."</option>";
}
}
?></select></li>
    <li><input name="title" type="text" class="input" id="title" maxlength="60" placeholder="标题" style="width:100%;line-height:36px;height:36px;box-sizing:border-box"></li>
    <li><textarea name="content" id="content" class="textarea textarea_k" placeholder="主题内容"></textarea></li>
    <li><input name="submitok" type="hidden" value="addupdate"></li>
    <li><input type="submit" value=" 开始发表 " class="btn2HUANG" /></td></li>
    <input name="mainid" type="hidden" value="<?php echo $mainid; ?>">
</tr>
</table>
</form>
<?php } ?>
</div>
<script src="group.js"></script>
<?php require_once 'bottom.php';?>
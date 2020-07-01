<?php
require_once '../../sub/init.php';
if ( !preg_match("/^[0-9]{1,8}$/",$cook_uid) || empty($cook_uid))header("Location: ".HOST."/?z=my");
require_once ZEAI.'sub/conn.php';
$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$cook_uid." AND pwd='$cook_pwd' AND flag>0");
if(!$db->num_rows($rt) > 0)header("Location: ".HOST."/?z=my");
if ( !preg_match("/^[0-9]{1,8}$/",$bkid) && !empty($bkid))callmsg("Forbidden!","-1");
if ( !preg_match("/^[0-9]{1,8}$/",$bbsid) && !empty($bbsid))callmsg("Forbidden!","-1");
if ($submitok == "bbsdelupdate"){
	$rt = $db->query("SELECT fid FROM ".__TBL_GROUP_WZ_BBS__." WHERE id=".$bbsid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$fid = $row[0];
	} else {
		callmsg("请求错误，该信息不存在或已被删除！","-1");
	}
} elseif ($submitok == "bbsclubdelupdate"){
	$rt = $db->query("SELECT clubid FROM ".__TBL_GROUP_CLUB_BBS__." WHERE id=".$bbsid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$fid = $row[0];
	} else {
		callmsg("请求错误，该信息不存在或已被删除！","-1");
	}
}
if ( !preg_match("/^[0-9]{1,8}$/",$fid) || empty($fid))callmsg("请求错误，该信息不存在或已被删除！","-1");
if ($submitok == "bbsclubdelupdate"){
	$rt = $db->query("SELECT mainid FROM ".__TBL_GROUP_CLUB__." WHERE flag>0 AND id=".$fid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$mainid = $row[0];
	} else {
		$tmperror = "OK";
	}
} else {
	$rt = $db->query("SELECT mainid,bkid,bktitle FROM ".__TBL_GROUP_WZ__." WHERE id=".$fid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$mainid = $row[0];
		$bkid = $row[1];
		$bktitle = $row[2];
	} else {
		$tmperror = "OK";
	}
}
if ($tmperror == "OK") {
	echo "&nbsp;&nbsp;<font color='#999999' style='font-size:12px'>".$_ZEAI['m_sitename']."( <a href=".HOST.">".HOST."</a> )提示：</FONT><BR><BR>&nbsp;&nbsp;<font color='#FF0000' style='font-size:12px'>请求错误，该信息不存在或已被删除！</FONT><BR><BR><p align=center><input onclick='history.back();' type='button' value='返回'></p>";
	exit;
}
$rt = $db->query("SELECT qloveb,userid,userid1,userid2,userid3 FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid." AND flag=1");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
	$data_qloveb = $row[0];
	$userid_main = $row[1];
	$userid1_main = $row[2];
	$userid2_main = $row[3];
	$userid3_main = $row[4];
	
} else {
	echo "&nbsp;&nbsp;<font color='#999999' style='font-size:12px'>".$_ZEAI['m_sitename']."( <a href=".HOST.">".HOST."</a> )提示：</FONT><BR><BR>&nbsp;&nbsp;<font color='#FF0000' style='font-size:12px'>请求错误，该群组不存在或已被锁定或已被删除！</FONT><BR><BR><p align=center><input onclick='history.back();' type='button' value='返回'></p>";exit;
}
if ($submitok !== "bbsclubdelupdate"){
	$rt = $db->query("SELECT userid FROM ".__TBL_GROUP_BK__." WHERE id=".$bkid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$userid_bk = $row[0];
	} else {
		callmsg("版块验证失败!","-1");
	}
}
if ($userid_main == $cook_uid || $userid1_main == $cook_uid || $userid2_main == $cook_uid || $userid3_main == $cook_uid || $cook_grade == 10 || $userid_bk == $cook_uid) {
	$authority_main = "OK";
} else {
	$authority_main = "NO";
}
if ( $authority_main == "OK" ) {
	switch ($submitok){ 
		case 'iftop1':
			$db->query("UPDATE ".__TBL_GROUP_WZ__." SET iftop=1 WHERE id=".$fid);
			header("Location: group_read.php?fid=".$fid);
		break;
		case 'iftop0':
			$db->query("UPDATE ".__TBL_GROUP_WZ__." SET iftop=0 WHERE id=".$fid);
			header("Location: group_read.php?fid=".$fid);
		break;
		case 'ifjh1':
			$db->query("UPDATE ".__TBL_GROUP_WZ__." SET ifjh=1 WHERE  id=".$fid);
			header("Location: group_read.php?fid=".$fid);
		break;
		case 'ifjh0':
			$db->query("UPDATE ".__TBL_GROUP_WZ__." SET ifjh=0 WHERE id=".$fid);
			header("Location: group_read.php?fid=".$fid);
		break;
		case 'delupdate':
			$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_WZ_BBS__." WHERE fid=".$fid);
			$row = $db->fetch_array($rt);
			$tmpcnt = $row[0];
			$db->query("DELETE FROM ".__TBL_GROUP_WZ_BBS__." WHERE fid=".$fid);
			$db->query("DELETE FROM ".__TBL_GROUP_WZ__." WHERE id=".$fid);
			$deloveb = $_ZEAI['GroupWZLoveb'];
			if ( $data_qloveb > $_ZEAI['GroupWZLoveb'] ){
				$endqloveb = intval($data_qloveb - $_ZEAI['GroupWZLoveb']);
			}else{
				$endqloveb = 0;
			}
			$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET wznum=wznum-1,bbsnum=bbsnum-".$tmpcnt.",qloveb=".$endqloveb." WHERE id=".$mainid);
			header("Location: group_article.php?mainid=".$mainid."&bkid=".$bkid."&bktitle=".urlencode($bktitle));
		break;
		case 'flag1':
			$db->query("UPDATE ".__TBL_GROUP_WZ__." SET flag=1 WHERE id=".$fid);
			header("Location: group_read.php?fid=".$fid);
		break;
		case 'bbsdelupdate':
			if ( $data_qloveb > $_ZEAI['GroupBBSLoveb'] ){
				$endqloveb = intval($data_qloveb - $_ZEAI['GroupBBSLoveb']);
			}else{
				$endqloveb = 0;
			}
			$db->query("UPDATE ".__TBL_GROUP_WZ_BBS__." SET flag=0,content='' WHERE id=".$bbsid);
			$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET qloveb=".$endqloveb." WHERE id=".$mainid);
			header("Location: read.php?fid=".$fid."&p=".$p);
		break;
		case 'bbsclubdelupdate':
			$db->query("UPDATE ".__TBL_GROUP_CLUB_BBS__." SET flag=0,content='' WHERE id=".$bbsid);
			header("Location: group_partyshow.php?fid=".$fid."&p=".$p);
		break;	
	}
}
callmsg("请求错误，没有操作权限!","-1");
?>
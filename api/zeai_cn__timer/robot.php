<?php
define('ZEAI_PHPV6',substr(dirname(__FILE__),0,-18));
require_once ZEAI_PHPV6.'sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
$robot=json_decode($_ZEAI['robot'],true);
$zeaitime=ADDTIME-intval($robot['hour'])*3600;
if((@!in_array('robot',$navarr)))exit;
$rt=$db->query("SELECT id,sex,areaid,openid,subscribe FROM ".__TBL_USER__." WHERE (kind=1 OR kind=3) AND refresh_time>".$zeaitime." ORDER BY id DESC LIMIT 50");
$total = $db->num_rows($rt);
if ($total > 0) {
	for($i=1;$i<=$total;$i++) {
		$rows = $db->fetch_array($rt,'num');
		if(!$rows) break;
		$uid  = $rows[0];$sex = $rows[1];$areaid = $rows[2];$openid = $rows[3];$subscribe = $rows[4];
		if($robot['flag']['hi']==1 && in_array('hi',$navarr)){
			$hinum = $db->COUNT(__TBL_TIP__," uid=".$uid." AND kind=3 AND ifrobot=1");
			$robot_hinum = intval($robot['hinum']);
			if($robot_hinum>$hinum){robot_timer_hi($uid,$sex,$areaid,$openid,$subscribe);}
		}
		if($robot['flag']['chat']==1 && !empty($robot['chatC'])){
			$chatnum = $db->COUNT(__TBL_MSG__," uid=".$uid." AND ifrobot=1");
			$robot_chatnum = intval($robot['chatnum']);
			if($robot_chatnum>$chatnum){robot_timer_chat($uid,$sex,$areaid,$openid,$subscribe);}
		}
		if($robot['flag']['view']==1){
			$viewnum = $db->COUNT(__TBL_CLICKHISTORY__," uid=".$uid." AND ifrobot=1");
			$robot_viewnum = intval($robot['viewnum']);
			if($robot_viewnum>$viewnum){robot_timer_view($uid,$sex,$areaid,$openid,$subscribe);}
		}
	}
}
function robot_timer_view($uid,$sex,$areaid,$openid,$subscribe) {
	global $db,$robot;
	$SQL = ($sex==1)?" AND sex=2":" AND sex=1";
	if($robot['areasame']==1){
		$areaid=explode(',',$areaid);
		$area2id=$areaid[1];
		if (ifint($area2id))$SQL .= " AND areaid LIKE '%".$area2id."%' ";
	}
	$row = $db->ROW(__TBL_USER__,"id,nickname","kind=4 ".$SQL." ORDER BY rand() LIMIT 1","name");
	if ($row){
		$send_uid = $row['id'];$send_nickname=dataIO($nickname,'out');
		$ifview = ($db->COUNT(__TBL_CLICKHISTORY__," senduid=".$send_uid." AND uid=".$uid) > 0)?true:false;
		if(!$ifview){
			if( !empty($openid) && $subscribe==1 ){
				$row2 = $db->ROW(__TBL_CLICKHISTORY__,"addtime","senduid=".$send_uid." AND uid=".$uid,"name");
				if($row2){
					$endtime = $row2[0];
					$difftime = ADDTIME - $endtime;
					if ($difftime > 60){
						$nickname = urlencode($send_nickname);
						$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人看了你的资料");
						$remark = urlencode("点击进入查看");
						$content= urlencode($send_nickname."刚刚浏览了你的资料");
						@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$content.'&nickname='.$nickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('u',$send_uid)));
					}
				}
			}
			$db->query("INSERT INTO ".__TBL_CLICKHISTORY__."  (uid,senduid,addtime,ifrobot) VALUES ($uid,$send_uid,".ADDTIME.",1)");
		}
	}
}
function robot_timer_chat($uid,$sex,$areaid,$openid,$subscribe) {
	global $db,$robot;
	$chatC = str_replace("<br><br>","<br>",dataIO($robot['chatC'],'out'));
	$chatC = explode('<br>',$chatC);
	$chatC = $chatC[array_rand($chatC)];
	$SQL = ($sex==1)?" AND sex=2":" AND sex=1";
	if($robot['areasame']==1){
		$areaid=explode(',',$areaid);
		$area2id=$areaid[1];
		if (ifint($area2id))$SQL .= " AND areaid LIKE '%".$area2id."%' ";
	}
	$row = $db->ROW(__TBL_USER__,"id,nickname","kind=4 ".$SQL." ORDER BY rand() LIMIT 1","name");
	if ($row){
		$send_uid = $row['id'];$send_nickname=dataIO($nickname,'out');
		$ifchat = ($db->COUNT(__TBL_MSG__," senduid=".$send_uid." AND uid=".$uid) > 0)?true:false;
		if(!$ifchat){
			if( !empty($openid) && $subscribe==1 ){
				$row2 = $db->ROW(__TBL_MSG__,"addtime","senduid=".$send_uid." AND uid=".$uid." AND ifdel=0","name");
				if($row2){
					$endtime = $row2[0];
					$difftime = ADDTIME - $endtime;
					if ($difftime > 60){
						$nickname = urlencode($send_nickname);
						$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人给你留言");
						$remark = urlencode("点击进入查看");
						$content= urlencode($chatC);
						@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$content.'&nickname='.$nickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('my_chat')));
					}
					
				}
			}
			$db->query("INSERT INTO ".__TBL_MSG__." (uid,senduid,content,addtime,ifrobot) VALUES ($uid,$send_uid,'$chatC',".ADDTIME.",1)");
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum+1 WHERE id=".$uid);
		}
	}
}

function robot_timer_hi($uid,$sex,$areaid,$openid,$subscribe) {
	global $db,$robot;
	$SQL = ($sex==1)?" AND sex=2":" AND sex=1";
	if($robot['areasame']==1){
		$areaid=explode(',',$areaid);
		$area2id=$areaid[1];
		if (ifint($area2id))$SQL .= " AND areaid LIKE '%".$area2id."%' ";
	}
	$row = $db->ROW(__TBL_USER__,"id,birthday,heigh,pay,edu,areaid","kind=4 ".$SQL." ORDER BY rand() LIMIT 1","name");
	if ($row){
		$send_uid = $row['id'];
		$ifhiher = ($db->COUNT(__TBL_TIP__," senduid=".$send_uid." AND uid=".$uid." AND kind=3") > 0)?true:false;
		if(!$ifhiher){
			$send_birthday = $row['birthday'];
			$send_heigh    = $row['heigh'];
			$send_pay      = $row['pay'];
			$send_edu      = $row['edu'];
			$dzh_content = dzh_getcontent($send_birthday,$send_heigh,$send_pay,$send_edu);
			if( !empty($openid) && $subscribe==1 ){
				$row2 = $db->ROW(__TBL_TIP__,"addtime","kind=3 AND senduid=".$send_uid." AND uid=".$uid,"name");
				if($row2){
					$endtime = $row2[0];
					$difftime = ADDTIME - $endtime;
					if ($difftime > 60){
						$nickname = urlencode($send_nickname);
						$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人给你打了一个招呼");
						$remark = urlencode("点击进入查看");
						$content= urlencode($dzh_content);
						@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$content.'&nickname='.$nickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('my_tz')));
					}
				}
			}
			$db->query("INSERT INTO ".__TBL_TIP__."  (uid,senduid,content,kind,addtime,ifrobot) VALUES ($uid,$send_uid,'$dzh_content',3,".ADDTIME.",1)");
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum+1 WHERE id=".$uid);
		}
	}
}
?>
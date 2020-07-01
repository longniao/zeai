<?php
require_once '../../../sub/init.php';
require_once '../group_init.php';
header("Cache-control: private");
$currfields = "grade,if2";
$chk_u_jumpurl=HOST.'/m1/group/my/group.php?submitok=group_mainphoto&mainid='.$mainid;require_once ZEAI.'my_chk_u.php';

$data_grade = $row['grade'];
$data_if2   = $row['if2'];
//require_once 'upload_super.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';


switch ($submitok) {
	case 'ajax_picurl_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('group',$file['tmp_name'],$mainid.'_');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET picurl_s='$dbname' WHERE userid=".$cook_uid." AND id=".$mainid);
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_picurl_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_up('tmp',$url,$cook_uid.'_315_','B');
				}
				$newpic = $_ZEAI['up2']."/".$dbname;
				if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET picurl_s='$dbname' WHERE userid=".$cook_uid." AND id=".$mainid);
				json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
		}
	break;
}




if (empty($submitok) || $submitok == 'list' || $submitok == 'add' || $submitok == 'addupdate'){

}else{
	if (!ifint($mainid)){
		callmsg("Forbidden","-1");
	}else{
		$rtD=$db->query("SELECT title,userid,picurl_s FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$cook_uid." AND id=".$mainid);
		if ($db->num_rows($rtD)){
			$rowD = $db->fetch_array($rtD);
			$maintitle  = $rowD[0];
			$mainuserid = $rowD[1];
			$picurl_s   = $rowD[2];
			if ($mainuserid != $cook_uid)callmsg("用户验证错误，操作失败！","-1");
		}else{
			callmsg("Forbidden","-1");
		}
	}
	if ($data_grade <2  )callmsg("只有VIP会员才有权限!",HOST."?z=my&e=my_vip");
}

if ($submitok == 'up_party_photo'){
	if (str_len($serverIds) < 60)callmsg("请先选择照片！","-1");
	if ($cook_serverIds == $serverIds)callmsg("请不要重复发布！","-1");
	if (!ifint($mainid) || !ifint($clubid))callmsg("mainid_clubid_error","-1");
	$data_photo_num = $db->COUNT(__TBL_GROUP_CLUB_PHOTO__,"mainid=".$mainid." AND clubid=".$clubid);
	if ($data_photo_num >= 50)callmsg("上传数目已达极限","-1");
	//
	$rtD=$db->query("SELECT picurl_s FROM ".__TBL_GROUP_CLUB__." WHERE flag>0 AND id=".$clubid);
	if ($db->num_rows($rtD)){
		$rowD = $db->fetch_array($rtD);
		$picurl_s = $rowD[0];
	}else{callmsg("Forbidden","-1");}
	//
	$sARR = explode(',',$serverIds);
	if (count($sARR) >= 1){
		$path_more = '';
		foreach ($sARR as $value) {
			$dbpicname = setphotodbname('group','weixin',$cook_uid.'_');
			$file      = get_wx_datastream($value);
			if (!up_send_wx($file,$dbpicname,0,'150*150',$_ZEAI['UpBsize']))callmsg("上传图片失败","");
			$path_s = setpath_s($dbpicname);
			$path_b = setpath_b($dbpicname);
			$tmppicurl = $_ZEAI['up2']."/".$path_s;
			if (!ifpic($tmppicurl))continue;
			//
			$addtime = YmdHis($ADDTIME);
			$db->query("INSERT INTO ".__TBL_GROUP_CLUB_PHOTO__." (mainid,clubid,path_s,path_b,addtime) VALUES ('$mainid','$clubid','$path_s','$path_b','$addtime')");
			$tmpphotoid = $db->insert_id();
			if (empty($picurl_s)) {
				$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET picurl_s='$path_s' WHERE id=".$clubid);
				$db->query("UPDATE ".__TBL_GROUP_CLUB_PHOTO__." SET ifmain=1 WHERE id=".$tmpphotoid);
			}
		}
		setcookie("cook_serverIds",$serverIds,$ADDTIME+7200000,"/",$_ZEAI['CookDomain']);
		header("Location: ".$SELF."?submitok=party&submitson=photo&mainid=".$mainid."&clubid=".$clubid."&p=".$p);
	}
}elseif($submitok == 'party_phpto_del_update'){
	$id = intval($id);
	if ($row= $db->ROWNUM($id,"mainid,clubid,path_s,path_b,ifmain","id=".$id." AND mainid=".$mainid." AND clubid=".$clubid,__TBL_GROUP_CLUB_PHOTO__)){
		$mainid   = $row[0];
		$clubid   = $row[1];
		$path1    = $row[2];
		$path2    = $row[3];
		$ifmain   = $row[4];
		up_send_userdel($path1.'|'.$path2);
		$db->query("DELETE FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE id=".$id);
		if ($ifmain==1)$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET picurl_s='' WHERE id=".$clubid);
	}
	//$res = up_send_userdel($path_s.'|'.$path_b);echo 'retn("'.$res.'")';
	exit;
}
switch ($submitok) {
	case 'up_group_mainphoto':
		if (str_len($serverIds) < 10)callmsg("请先上传图片！","-1");
		if ($cook_serverIds == $serverIds)callmsg("请不要重复发布！","-1");
		require_once ZEAI.'sub/upload_super.php';
		$file      = get_wx_datastream($serverIds);
		$dbpicname = setphotodbname('group','weixin',$cook_uid.'_');
		if (!up_send_wx($file,$dbpicname,$_ZEAI['ifwaterimg'],'240*180',$_ZEAI['UpBsize']))callmsg("上传图片失败","");
		$path_s = setpath_s($dbpicname);
		$tmppicurl = $_ZEAI['up2']."/".$path_s;
		if (!ifpic($tmppicurl))callmsg("图片格式错误","-1");
		$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET picurl_s='$path_s' WHERE userid=".$cook_uid." AND id=".$mainid);
		$rt = $db->query("SELECT id FROM ".__TBL_GROUP_PHOTO__." WHERE path_s='$picurl_s' AND mainid=".$mainid);
		if(!$db->num_rows($rt)){
			$picurl_b = getpath_b($picurl_s);
			up_send_userdel($picurl_s.'|'.$picurl_b);
		}
		setcookie("cook_serverIds",$serverIds,$ADDTIME+7200000,"/",$_ZEAI['CookDomain']);
		header("Location: ".$SELF."?submitok=group_mainphoto&mainid=".$mainid);
	break;
	case 'add':
		$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$cook_uid);
		$rowss = $db->fetch_array($rtt);
		$tmpgroupcount = $rowss[0];
		if ($tmpgroupcount > 0 )callmsg("您已经创建了圈子，请不要重复操作。","-1");
		$LMcnt = 2;$OTitle = '正在创建圈子';
	break;
	case 'addupdate':
		$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$cook_uid);
		$rowss = $db->fetch_array($rtt);
		$tmpgroupcount = $rowss[0];
		if ($tmpgroupcount >= 1)callmsg("●您已经创建了圈子，请不要重复操作。","-1");
		if (empty($qkind))callmsg("请选择圈子分类!","-1");
		$totalid = explode(",",$qkind);
		$totaltitle = $totalid[1];
		$totalid = $totalid[0];
		if ( !preg_match("/^[0-9]{1,6}$/",$totalid) || empty($totalid) )callmsg("Forbidden5!","-1");
		if (str_len($totaltitle)<1 || str_len($totaltitle)>50)callmsg("Forbidden6!","-1");
		$title = trimm($title);
		if (str_len($title)<1 || str_len($title)>50)callmsg("圈子名称请控制在1~50字节！","-1");
		if (str_len($content)<20 || str_len($content)>10000)callmsg("圈子介绍请控制在10~10000字节！","-1");
		if ( !preg_match("/^[0-2]{1}$/",$ifopen) )callmsg("Forbidden1","-1");
		if ( !preg_match("/^[0-1]{1}$/",$ifin) )callmsg("Forbidden4","-1");
		if ( !preg_match("/^[0-1]{1}$/",$ifin2) )callmsg("Forbidden4","-1");
		$areaid    = dataIO($areaid,'in',50);
		$areatitle = dataIO($areatitle,'in',100);
		$addtime   = YmdHis($ADDTIME);
		$db->query("INSERT INTO ".__TBL_GROUP_MAIN__." (totalid,totaltitle,title,content,ifopen,ifin,ifin2,areaid,areatitle,addtime,userid) VALUES ($totalid,'$totaltitle','$title','$content',$ifopen,$ifin,$ifin2,'$areaid','$areatitle','$addtime',$cook_uid)");
		$mainid = $db->insert_id();
		$db->query("UPDATE ".__TBL_GROUP_TOTAL__." SET bknum=bknum+1 WHERE id=".$totalid);
		$db->query("INSERT INTO ".__TBL_GROUP_USER__."  (userid,mainid,flag,addtime) VALUES ($cook_uid,$mainid,1,'$addtime')");
		$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET allusrnum=allusrnum+1 WHERE id=".$mainid);
		header("Location: ".$SELF."?submitok=bk&mainid=".$mainid);
	break;
	case 'bk':
		if ($submitson == "addupdate") {
			if (str_len($title)<1 || str_len($title)>50)callmsg("版块名称请控制在1~50字节！","-1");
			if (str_len($content)<10 || str_len($content)>400)callmsg("版块介绍请控制在10~400字节！","-1");
			$addtime = YmdHis($ADDTIME);
			$db->query("INSERT INTO ".__TBL_GROUP_BK__." (mainid,title,content,addtime) VALUES ('$mainid','$title','$content','$addtime')");
			header("Location: ".$SELF."?submitok=bk&mainid=".$mainid);
		} elseif ($submitson == "modupdate") {
			if ( !preg_match("/^[0-9]{1,9}$/",$bkid) || empty($bkid) )callmsg("Forbidden1!","-1");
			if ( !preg_match("/^[0-9]{1,9}$/",$px) && !empty($px) )callmsg("Forbidden1!","-1");
			if ( !preg_match("/^[0-9]{1,9}$/",$userid) )callmsg("版主ID号格式不对，请检查!","-1");
			$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE flag=1 AND id=".$userid);
			if(!$db->num_rows($rt)){
				$userid = 0;
			}
			//是否圈内成员
			$rt = $db->query("SELECT id,flag FROM ".__TBL_GROUP_USER__." WHERE userid='$userid' AND mainid=".$mainid);
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt,'all');
				if ($row[1] == 0){
					$db->query("UPDATE ".__TBL_GROUP_USER__." SET flag=1 WHERE id=".$row[0]);
				}
			}else{
				//还没加入，自动加
				$addtime = YmdHis($ADDTIME);
				$db->query("INSERT INTO ".__TBL_GROUP_USER__."  (userid,mainid,flag,addtime) VALUES ($userid,$mainid,1,'$addtime')");
				$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET allusrnum=allusrnum+1 WHERE id=".$mainid);
			}
			$db->query("UPDATE ".__TBL_GROUP_BK__." SET title='$title',content='$content',px='$px',userid='$userid' WHERE id=".$bkid);
			$db->query("UPDATE ".__TBL_GROUP_WZ__." SET bktitle='$title' WHERE bkid=".$bkid);
			callmsg("修改成功!",$SELF."?submitok=bk&mainid=".$mainid."&p=".$p);
		} elseif ($submitson == "delupdate") {
			if ( !preg_match("/^[0-9]{1,9}$/",$bkid) || empty($bkid) )callmsg("Forbidden1!","-1");
			$rt = $db->query("SELECT id FROM ".__TBL_GROUP_WZ__." WHERE bkid=".$bkid);
			if($db->num_rows($rt)){
				$total = $db->num_rows($rt);
				$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET wznum=wznum-$total WHERE id=".$mainid);
				for($j=1;$j<=$total;$j++) {
					$row = $db->fetch_array($rt,'all');
					$fid = $row[0];
						$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_WZ_BBS__." WHERE fid=".$fid);
						if($db->num_rows($rt)){
							$row = $db->fetch_array($rt,'all');
							$tmpbbsnum = intval($row[0]);
							$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET bbsnum=bbsnum-$tmpbbsnum WHERE  id=".$mainid);
		
						}
					$db->query("DELETE FROM ".__TBL_GROUP_WZ_BBS__." WHERE fid='$fid'");
				}
			}
			$db->query("DELETE FROM ".__TBL_GROUP_WZ__." WHERE bkid=".$bkid);
			$db->query("DELETE FROM ".__TBL_GROUP_BK__." WHERE id=".$bkid);
			header("Location: ".$SELF."?submitok=bk&mainid=".$mainid);
		}
		$OTitle2 = '论坛版块管理';
	break;
	case 'modupdate':
		if (!ifint($mainid))callmsg("Forbidden","-1");
		if ( !preg_match("/^[0-2]{1}$/",$ifopen) )callmsg("Forbidden!","-1");
		if ( !preg_match("/^[0-1]{1}$/",$ifin) )callmsg("Forbidden!","-1");
		if ( !preg_match("/^[0-1]{1}$/",$ifin2) )callmsg("Forbidden!","-1");
		if (empty($form_userid1))$form_userid1 = 0;
		if (empty($form_userid2))$form_userid2 = 0;
		if (empty($form_userid3))$form_userid3 = 0;
		if ($form_userid1 == $cook_uid || $form_userid2 == $cook_uid || $form_userid3 == $cook_uid)callmsg("副会长不能是自己!","-1");
		$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET ifopen=$ifopen,flag=$flag,ifin=$ifin,ifin2=$ifin2,userid1=$form_userid1,userid2=$form_userid2,userid3=$form_userid3 WHERE userid=".$cook_uid." AND id=".$mainid);
		$addtime = YmdHis($ADDTIME);
		if ( preg_match("/^[0-9]{1,9}$/",$form_userid1) && !empty($form_userid1) ) {
			$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$form_userid1);
			if(!$db->num_rows($rt)){
				callmsg("副会长1不存在!","-1");
			}else{
				//是否圈内成员
				$rt = $db->query("SELECT id,flag FROM ".__TBL_GROUP_USER__." WHERE userid='$form_userid1' AND mainid=".$mainid);
				if($db->num_rows($rt)){
					$row = $db->fetch_array($rt,'all');
					if ($row[1] == 0){
						$db->query("UPDATE ".__TBL_GROUP_USER__." SET flag=1 WHERE id=".$row[0]);
					}
				}else{
					//还没加入，自动加
					$db->query("INSERT INTO ".__TBL_GROUP_USER__."  (userid,mainid,flag,addtime) VALUES ($form_userid1,$mainid,1,'$addtime')");
					$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET allusrnum=allusrnum+1 WHERE id=".$mainid);
				}
			}
		}
		if ( preg_match("/^[0-9]{1,9}$/",$form_userid2) && !empty($form_userid2) ) {
			$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$form_userid2);
			if(!$db->num_rows($rt)){
				callmsg("副会长2不存在!","-1");
			}else{
				//是否圈内成员
				$rt = $db->query("SELECT id,flag FROM ".__TBL_GROUP_USER__." WHERE userid='$form_userid2' AND mainid=".$mainid);
				if($db->num_rows($rt)){
					$row = $db->fetch_array($rt,'all');
					if ($row[1] == 0){
						$db->query("UPDATE ".__TBL_GROUP_USER__." SET flag=1 WHERE id=".$row[0]);
					}
				}else{
					//还没加入，自动加
					$db->query("INSERT INTO ".__TBL_GROUP_USER__."  (userid,mainid,flag,addtime) VALUES ($form_userid2,$mainid,1,'$addtime')");
					$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET allusrnum=allusrnum+1 WHERE id=".$mainid);
				}
			}
		}
		if ( preg_match("/^[0-9]{1,9}$/",$form_userid3) && !empty($form_userid3) ) {
			$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$form_userid3);
			if(!$db->num_rows($rt)){
				callmsg("副会长3不存在!","-1");
			}else{
				//是否圈内成员
				$rt = $db->query("SELECT id,flag FROM ".__TBL_GROUP_USER__." WHERE userid='$form_userid3' AND mainid=".$mainid);
				if($db->num_rows($rt)){
					$row = $db->fetch_array($rt,'all');
					if ($row[1] == 0){
						$db->query("UPDATE ".__TBL_GROUP_USER__." SET flag=1 WHERE id=".$row[0]);
					}
				}else{
					//还没加入，自动加
					$db->query("INSERT INTO ".__TBL_GROUP_USER__."  (userid,mainid,flag,addtime) VALUES ($form_userid3,$mainid,1,'$addtime')");
					$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET allusrnum=allusrnum+1 WHERE id=".$mainid);
				}
			}	
		}
		callmsg("设置成功！",$SELF);
	break;
	case 'user':
		$OTitle2 = '圈子成员管理';
		$submitson = (empty($submitson))?'list':$submitson;
		if ($submitson == "list") {
			$rt = $db->query("SELECT userid,userid1,userid2,userid3 FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$cook_uid." AND id=".$mainid);
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt,'all');
				$userid_main = $row['userid'];
				$userid1_main = $row['userid1'];
				$userid2_main = $row['userid2'];
				$userid3_main = $row['userid3'];
			}
		}elseif ($submitson == "flag") {
			if ( !preg_match("/^[0-9]{1,9}$/",$classid) || empty($classid))callmsg("Forbidden","-1");
			if (!ifint($mainid))callmsg("Forbidden","-1");
			$db->query("UPDATE ".__TBL_GROUP_USER__." SET flag=1 WHERE id=".$classid);
			header("Location: ".$SELF."?submitok=user&submitson=list&mainid=".$mainid."&p=".$p);
		}elseif ($submitson == "delupdate") {
			if ( !preg_match("/^[0-9]{1,9}$/",$classid) || empty($classid))callmsg("Forbidden","-1");
			$rt = $db->query("SELECT userid FROM ".__TBL_GROUP_USER__." WHERE id=".$classid);
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt,'all');
				$Uid = $row[0];
				if ($Uid == $cook_uid)callmsg("创始人不能踢除！","-1");
			} else {
				callmsg("请求错误，没有操作权限!","-1");
			}
			$rt = $db->query("SELECT userid1,userid2,userid3 FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid);
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt,'all');
				$userid1_main = $row['userid1'];
				$userid2_main = $row['userid2'];
				$userid3_main = $row['userid3'];
			}
			if ($Uid == $userid1_main){
				$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET userid1=0 WHERE id=".$mainid);
			}
			if($Uid == $userid2_main){
				$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET userid2=0 WHERE id=".$mainid);
			}
			if($Uid == $userid3_main){
				$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET userid3=0 WHERE id=".$mainid);
			}
			$rt = $db->query("SELECT id FROM ".__TBL_GROUP_BK__." WHERE userid=".$Uid." AND mainid=".$mainid);
			if($db->num_rows($rt)){
				$db->query("UPDATE ".__TBL_GROUP_BK__." SET userid=0 WHERE mainid=".$mainid);
			}
			$db->query("DELETE FROM ".__TBL_GROUP_USER__." WHERE id=".$classid);
			$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET allusrnum=allusrnum-1 WHERE id=".$mainid);
			header("Location: ".$SELF."?submitok=user&submitson=list&mainid=".$mainid."&p=".$p);
		}
	break;
	case 'party':
		$submitson = (empty($submitson))?'list':$submitson;
		
		if ($submitson == "addupdate") {
			if ( str_len($title)<6 || str_len($title)>100 )callmsg("活动名称请控制在6~100个字节以内","-1");
			if ( str_len($kind)<2 || str_len($kind)>100 )callmsg("活动类型请控制在2~100个字节以内","-1");
			if ( str_len($hdtime)<2 || str_len($hdtime)>100 )callmsg("活动时间请控制在2~100个字节以内","-1");
			if ( !preg_match("/^[0-9]{4}$/",$year8))callmsg("请输入正确格式的截止报名时间“年”如：2010","-1");
			if ( !preg_match("/^[0-9]{2}$/",$month8))callmsg("请输入正确格式的截止报名时间“月”如：09","-1");
			if ( !preg_match("/^[0-9]{2}$/",$day8))callmsg("请输入正确格式的截止报名时间“日”如：09","-1");
			if ( !preg_match("/^[0-9]{2}$/",$hour8))callmsg("请输入正确格式的截止报名时间的“时”如：18","-1");
			if ( !preg_match("/^[0-9]{2}$/",$minute8))callmsg("请输入正确格式的截止报名时间“分”如：30","-1");
			$jzbmtime1 = $year8.'-'.$month8.'-'.$day8;
			$jzbmtime2 = ' '.$hour8.':'.$minute8.':00';
			if ($hour8 <0 || $hour8 >23 || $minute8<0 || $minute8>59)callmsg("$jzbmtime2请输入正确格式的截止报名时间“时”和“分”如：18:30","-1");
			if ( !preg_match('/(^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$)/', $jzbmtime1) )callmsg("请输入正确的格式的截止报名时间$jzbmtime1","-1");
			$jzbmtime = $jzbmtime1.$jzbmtime2;
			if ( str_len($address)<2 || str_len($address)>150 )callmsg("活动地点请控制在2~150个字节以内","-1");
			if ( str_len($jtlx)<2 || str_len($jtlx)>100 )callmsg("交通路线请控制在2~100个字节以内","-1");
			if ( !preg_match("/^[0-9]{1,5}$/",$num_n))callmsg("“男士”人数限定必须是5位数字以内","-1");
			if ( !preg_match("/^[0-9]{1,5}$/",$num_r))callmsg("“女士”人数限定必须是5位数字以内","-1");
			if ( !preg_match("/^[0-9]{1,5}$/",$rmb_n))callmsg("“男士”活动费用必须是5位数字以内","-1");
			if ( !preg_match("/^[0-9]{1,5}$/",$rmb_r))callmsg("“女士”活动费用必须是5位数字以内","-1");
			if ( str_len($tbsm)>500 )callmsg("特别说明请控制在500字节以内","-1");
			if ( str_len($content)<10 || str_len($content)>20000 )callmsg("活动详细说明请控制在10~20000字节以内","-1");
			$addtime = YmdHis($ADDTIME);
			$db->query("INSERT INTO ".__TBL_GROUP_CLUB__." (mainid,maintitle,title,kind,hdtime,address,jtlx,num_n,num_r,rmb_n,rmb_r,tbsm,content,jzbmtime,addtime) VALUES ('$mainid','$maintitle','$title','$kind','$hdtime','$address','$jtlx','$num_n','$num_r','$rmb_n','$rmb_r','$tbsm','$content','$jzbmtime','$addtime')");
			header("Location: ".$SELF."?submitok=party&submitson=list&mainid=".$mainid);
		} elseif ($submitson == "mod") {
			$OTitle2 = '交友活动修改';
		} elseif ($submitson == "modupdate") {
			if ( !preg_match("/^[0-9]{1,8}$/",$fid) || empty($fid))callmsg("活动不存在或已被删除!","-1");
			if ( str_len($title)<6 || str_len($title)>100 )callmsg("活动名称请控制在6~100个字节以内","-1");
			if ( str_len($kind)<2 || str_len($kind)>100 )callmsg("活动类型请控制在2~100个字节以内","-1");
			if ( str_len($hdtime)<2 || str_len($hdtime)>100 )callmsg("活动时间请控制在2~100个字节以内","-1");
			if ( !preg_match("/^[0-9]{4}$/",$year8))callmsg("请输入正确格式的截止报名时间“年”如：2008","-1");
			if ( !preg_match("/^[0-9]{2}$/",$month8))callmsg("请输入正确格式的截止报名时间“月”如：08","-1");
			if ( !preg_match("/^[0-9]{2}$/",$day8))callmsg("请输入正确格式的截止报名时间“日”如：08","-1");
			if ( !preg_match("/^[0-9]{2}$/",$hour8))callmsg("请输入正确格式的截止报名时间的“时”如：18","-1");
			if ( !preg_match("/^[0-9]{2}$/",$minute8))callmsg("请输入正确格式的截止报名时间“分”如：30","-1");
			$jzbmtime1 = $year8.'-'.$month8.'-'.$day8;
			$jzbmtime2 = ' '.$hour8.':'.$minute8.':00';
			if ($hour8 <0 || $hour8 >24 || $minute8<0 || $minute8>59)callmsg("$jzbmtime2请输入正确格式的截止报名时间“时”和“分”如：18:30","-1");
			if ( !preg_match('/(^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$)/', $jzbmtime1) )callmsg("请输入正确的格式的截止报名时间$jzbmtime1","-1");
			$jzbmtime = $jzbmtime1.$jzbmtime2;
			if ( str_len($address)<2 || str_len($address)>100 )callmsg("活动地点请控制在2~100个字节以内","-1");
			if ( str_len($jtlx)<2 || str_len($jtlx)>100 )callmsg("交通路线请控制在2~100个字节以内","-1");
			if ( !preg_match("/^[0-9]{1,5}$/",$num_n))callmsg("“男士”人数限定必须是5位数字以内","-1");
			if ( !preg_match("/^[0-9]{1,5}$/",$num_r))callmsg("“女士”人数限定必须是5位数字以内","-1");
			if ( !preg_match("/^[0-9]{1,5}$/",$rmb_n))callmsg("“男士”活动费用必须是5位数字以内","-1");
			if ( !preg_match("/^[0-9]{1,5}$/",$rmb_r))callmsg("“女士”活动费用必须是5位数字以内","-1");
			if ( str_len($tbsm)>500 )callmsg("特别说明请控制在500字节以内","-1");
			if ( str_len($content)<10 || str_len($content)>20000 )callmsg("活动详细说明请控制在10~20000字节以内","-1");
			$rtD=$db->query("SELECT flag FROM ".__TBL_GROUP_CLUB__." WHERE flag=0 AND id=".$fid);
			if (!$db->num_rows($rtD)){
				callmsg("Forbidden","-1");
			}
			$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET mainid='$mainid',maintitle='$maintitle',title='$title',kind='$kind',hdtime='$hdtime',address='$address',jtlx='$jtlx',num_n='$num_n',num_r='$num_r',rmb_n='$rmb_n',rmb_r='$rmb_r',tbsm='$tbsm',content='$content',jzbmtime='$jzbmtime' WHERE id='$fid'");
			header("Location: ".$SELF."?submitok=party&submitson=list&mainid=".$mainid."&p=".$p);
		
		
		} elseif ($submitson == "photo") {
			if ( !preg_match("/^[0-9]{1,5}$/",$clubid) || empty($clubid) )callmsg("Forbidden!","-1");
			$rtD=$db->query("SELECT title FROM ".__TBL_GROUP_CLUB__." WHERE flag>0 AND id=".$clubid);
			if ($db->num_rows($rtD)){
				$rowD = $db->fetch_array($rtD);
				$clubtitle = $rowD[0];
				$OTitle3 = '交友活动：'.$clubtitle;
			}else{callmsg("Forbidden","-1");}
			$OTitle2 = '活动照片管理';
		} elseif ($submitson == "photo_up") {
			if ( !preg_match("/^[0-9]{1,5}$/",$clubid) || empty($clubid) )callmsg("Forbidden","-1");
			$rtD=$db->query("SELECT title FROM ".__TBL_GROUP_CLUB__." WHERE flag>0 AND id=".$clubid);
			if ($db->num_rows($rtD)){
				$rowD = $db->fetch_array($rtD);
				$clubtitle = $rowD[0];
				$OTitle3 = '交友活动：'.$clubtitle;
			}else{callmsg("Forbidden","-1");}
			$OTitle2 = '活动照片管理';
		} elseif ($submitson == "photo_setmain") {
			if ( !preg_match("/^[0-9]{1,10}$/",$classid) || empty($classid))callmsg("error1","-1");
			$rt = $db->query("SELECT mainid,clubid,path_s,ifmain FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE id=".$classid);
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt,'all');
				$mainid   = $row[0];
				$clubid   = $row[1];
				$path1    = $row[2];
				$ifmain   = $row[3];
			} else {
				callmsg("Forbidden!","-1");
			}
			$db->query("UPDATE ".__TBL_GROUP_CLUB_PHOTO__." SET ifmain=0 WHERE clubid=".$clubid);
			$db->query("UPDATE ".__TBL_GROUP_CLUB_PHOTO__." SET ifmain=1 WHERE id=".$classid);
			$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET picurl_s='$path1' WHERE id=".$clubid);
			header("Location: ".$SELF."?&submitok=party&&submitson=photo&mainid=".$mainid."&clubid=".$clubid."&p=".$p);
		} elseif ($submitson == "user") {
			if ( !preg_match("/^[0-9]{1,5}$/",$clubid) || empty($clubid) )callmsg("Forbidden1","-1");
			$rtD=$db->query("SELECT title FROM ".__TBL_GROUP_CLUB__." WHERE id=".$clubid);
			if ($db->num_rows($rtD)){
				$rowD = $db->fetch_array($rtD);
				$clubtitle = $rowD[0];
				$OTitle3 = '交友活动：'.$clubtitle;
			}else{callmsg("Forbidden2","-1");}
			$OTitle2 = '活动报名会员管理';
		} elseif ($submitson == "user_flagupdate") {
			if ( !preg_match("/^[0-9]{1,9}$/",$clubid) || empty($clubid))callmsg("Forbidden!","-1");
			$addtime = YmdHis($ADDTIME);
			$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET flag=3,jzbmtime='$addtime' WHERE id=".$clubid);
			header("Location: ".$SELF."?&submitok=party&mainid=".$mainid."&clubid=".$clubid);
		} elseif ($submitson == "flag1") {
			if ( !preg_match("/^[0-9]{1,9}$/",$classid) || empty($classid))callmsg("Forbidden!","-1");
			$db->query("UPDATE ".__TBL_GROUP_CLUB_USER__." SET flag=1 WHERE  id=".$classid);
			header("Location: ".$SELF."?&submitok=party&submitson=user&mainid=".$mainid."&clubid=".$clubid."&p=".$p);
		} elseif ($submitson == "flag0") {
			if ( !preg_match("/^[0-9]{1,9}$/",$classid) || empty($classid))callmsg("Forbidden!","-1");
			$rt = $db->query("SELECT clubid FROM ".__TBL_GROUP_CLUB_USER__." WHERE id=".$classid);
			if($db->num_rows($rt)){
				$row = $db->fetch_array($rt,'all');
				$clubid = $row[0];
			} else {
				callmsg("Forbidden!","-1");
			}
			$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET bmnum=bmnum-1 WHERE id='$clubid'");
			$db->query("DELETE FROM ".__TBL_GROUP_CLUB_USER__." WHERE id=".$classid);
			header("Location: ".$SELF."?&submitok=party&submitson=user&mainid=".$mainid."&clubid=".$clubid."&p=".$p);
		}
	break;
}
$mini_show  = true;$mini_title = '我的圈子';$nav = $nav='trend';
//if($submitson=="photo" || $submitok == 'group_mainphoto') {
//	require_once ZEAI."weixin/jssdk.php";	
//}
?>
<!doctype html>
<html><head>
<meta charset="utf-8">
<title></title>
<?php echo $headmeta; ?>
<link href="../group.css?2" rel="stylesheet" type="text/css" />
<script src="../www_zeai_cn.js"></script>
<?php if($submitson=="photo" || $submitok == 'group_mainphoto') {?>

	<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
    <script src="<?php echo HOST;?>/m1/js/m1.js"></script>
	<?php if (is_weixin()){
        require_once ZEAI."api/weixin/jssdk.php";?>
        <script src="<?php echo HOST;?>/res/jweixin-1.2.0.js"></script>
        <script>wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr: '<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','startRecord','stopRecord','onVoiceRecordEnd','uploadVoice','downloadVoice','playVoice','pauseVoice']});</script>
    <?php }?>

<?php }?>
</head>
<body>
<?php
switch ($submitok) {
	case 'party':
		if ($submitson == 'photo'){
			$mini_url = 'group.php?submitok=party&mainid='.$mainid;
		}elseif($submitson == 'user'){
			$mini_url = 'group.php?submitok=party&mainid='.$mainid;
		}elseif($submitson == 'add'){
			$mini_url = 'group.php?submitok=party&mainid='.$mainid;
		}else{
			$mini_url = 'group.php';
		}
	break;
	case 'mod':
		$mini_url = 'group.php';
	break;
	case 'add':
		$mini_url = 'group.php';
	break;
	case 'bk':
		$mini_url = 'group.php';
	break;
	case 'user':
		$mini_url = 'group.php';
	break;
	case 'group_mainphoto':
		$mini_url = 'group.php';
	break;
	default:$mini_url = './';break;
}
require_once '../top_mini.php';?>
<main class="main">
<?php if (empty($submitok)){ ?>
	<em class="my_group">
		<?php
        $rt=$db->query("SELECT id,title,qloveb,flag,picurl_s,addtime,jjpmprice FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$cook_uid." ORDER BY id DESC");
        $total = $db->num_rows($rt);
        if($total>0){
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'all');
                if(!$rows) break;
                ?>
                <table class="table0 tableli" style="margin:20px auto">
                <tr>
                <td height="70" align="center" valign="middle" bgcolor="#f8f8f8" class="C666"><a href="<?php echo $_ZEAI['group2'];?>/group_main.php?mainid=<?php echo $rows['id'] ?>" style='margin-bottom:5px;font:24px "Microsoft Yahei";' class="a000" >【<b><?php echo  $rows['title']; ?></b>】</a><br />
                当前状态：<?php
                if ($rows['flag']==0){echo " (<font color=red>审核中</font>)";} elseif ($rows['flag'] == -1){?> 
                (<a href="<?php echo $SELF; ?>?submitok=mod&mainid=<?php echo $rows['id'];?>"><font color=blue>隐藏中</font></a>)
                <?php }else{echo "正常";}?>　　会长：<?php echo uicon($cook_sex.$cook_grade); ?> <a href="<?php echo HOST.'/?z=index&e=u&a='.$cook_uid; ?>"><?php echo $cook_nickname;?></a></td>
                </tr>
                <tr>
                <td height="80" align="center" valign="middle" style="padding:30px 0">
                <a href="<?php echo $SELF; ?>?submitok=mod&mainid=<?php echo $rows['id'];?>" class="btn size2 BAI yuan" style="font-size:14px">基本设置</a>&nbsp;&nbsp;
                <a href="<?php echo $SELF; ?>?submitok=group_mainphoto&mainid=<?php echo $rows['id'];?>"  class="btn size2 BAI yuan" style="font-size:14px">圈子主图</a>&nbsp;&nbsp;
                <a href="<?php echo $SELF; ?>?submitok=bk&mainid=<?php echo $rows['id'];?>"  class="btn size2 BAI yuan" style="font-size:14px">圈子版块</a><br><br>
                <a href="<?php echo $SELF; ?>?submitok=party&mainid=<?php echo $rows['id'];?>"  class="btn size2 BAI yuan" style="font-size:14px">交友活动</a>&nbsp;&nbsp;
                <a href="<?php echo $_ZEAI['group2'].'/group_article.php?mainid='.$rows['id']; ?>" class="btn size2 BAI yuan" style="font-size:14px" onclick="return confirm('当你点开帖子详细内容页面后，可看见操作按钮，如“删除、修改、置顶等等”')">帖子管理</a>&nbsp;
                <a href="<?php echo $SELF; ?>?submitok=user&mainid=<?php echo $rows['id'];?>" class="btn size2 BAI yuan" style="font-size:14px">圈子成员</a>
                </td>
                </tr>
                </table>
        <?php }}else{
			echo "<div class='nodatatips W150'><h4><br>您暂时还没有圈子</h4><a href='".$SELF."?submitok=add' style='margin:20px 0 10px 0;display:inline-block' class='aLAN' target='_blank'>点此创建</a></div>";
		}?>
	</em>
<!-- 创建圈子 -->
<?php }elseif($submitok == 'add'){?>
  
<?php if ($data_grade <2  ) {
echo "<div class='nodatatips W150'><h4><br>只有VIP会员才可以创建</h4><a href='".HOST."?z=my&e=my_vip' style='margin:20px 0 10px 0;display:inline-block' class='aLV' >我要升级会员</a><br><br></div>";
} else {?>
<style>
.main .my_group_add{width:100%}
.main .my_group_add .table0 td{font-size:12px;padding:10px 0}
.main .my_group_add .table0 tr td:first-child{width:70px;padding-right:5px}
.main .my_group_add .table0 tr td:last-child{padding-left:5px}

.addtipstop{line-height:200%;color:#FF6C96;padding:20px 20px 0}
.SW2{width:30%;font-size:12px}
#content{width:90%;margin-top:5px;font-size:12px;padding:3%}
.groupadd{width:100%}
.groupadd tr td{border-bottom:#e4e4e4 1px solid}
.groupadd tr td:last-child{color:#999}
.groupadd tr:last-child td{border:0}
</style>


<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/cache/areadata.js"></script>
<script src="<?php echo HOST;?>/res/select3.js"></script>


<script src="../inputColor.js"></script>
<script>
function chkform(){
	if (empty(o('qkind').value)){ZEAI_win_alert('请选择圈子所属分类。','qkind');	return false;}
	if(  str_len(o('title').value) < 1 || str_len(o('title').value) > 50  ){ZEAI_win_alert("圈子标题必须是1～50个字节以内！","title");return false;}
	if (empty(o('m1').value)){ZEAI_win_alert('请选择所在地区。','m1');return false;}
	if (empty(o('m2').value)){ZEAI_win_alert('请选择所在地区。','m2');return false;}
	if(str_len(o('content').value)<20 || str_len(o('content').value)>10000){ZEAI_win_alert('内容长度要在20-1000字节之间','content');return false;}
	//if (!o('agree').checked){ZEAI_win_alert("开通圈子必须遵守之条款！","agree");return false;}
	var m1 = get_option('m1','v');
	var m2 = get_option('m2','v');
	var m3 = get_option('m3','v');
	var m1t = get_option('m1','t');
	var m2t = get_option('m2','t');
	var m3t = get_option('m3','t');
	m1t = (nulltext == m1t)?'':m1t;
	m2t = (nulltext == m2t)?'':' '+m2t;
	m3t = (nulltext == m3t)?'':' '+m3t;
	m1 = (m1 == 0)?'':m1;
	m2 = (m2 == 0)?'':','+m2;
	m3 = (m3 == 0)?'':','+m3;
	var areaid = m1 + m2 + m3;
	areaid = (areaid == '0,0,0')?'':areaid;
	var areatitle = m1t + m2t + m3t;
	o('areaid').value = areaid;
	o('areatitle').value = areatitle;
}
</script>
<em class="my_group_add">
<div class="addtipstop"><?php echo $_ZEAI['SiteName']; ?>开通兴趣圈子栏目，你可以按你的兴趣爱好创建一个圈子，邀请志同道合的朋友加入，在圈子里你们能够讨论共同关心的话题；分享图片、思想、文字、共同创业等。现在就来创建吧！</div>
<table class="table0 groupadd">
<form action="<?php echo $SELF; ?>" method="post" name="zeai_cn..FORM" id="GYLform" onsubmit="return chkform();">
<tr>
<td height="40" align="right">会　　长</td>
<td align="left"><?php echo $cook_nickname; ?> (ID:<?php echo $cook_uid; ?>)</td>
</tr>
<tr>
<td height="40" align="right">圈子分类 </td>
<td align="left">
<select name="qkind" id="qkind" class="select W150 S12">
<?php
if ($cook_grade == 10) {
	$rt2=$db->query("SELECT id,title FROM ".__TBL_GROUP_TOTAL__." WHERE flag=1 ORDER BY px DESC,id DESC");
} else {
	$rt2=$db->query("SELECT id,title FROM ".__TBL_GROUP_TOTAL__." WHERE flag=1 AND title<>'官方圈子' ORDER BY px DESC,id DESC");
}
$total2 = $db->num_rows($rt2);
if ($total2 <= 0) {
	echo "暂无";
} else {
?>
<option value="">选择分类</option>
<?php
	for($j=0;$j<$total2;$j++) {
		$rows2 = $db->fetch_array($rt2);
		if(!$rows2) break;
		echo "<option value=".$rows2[0].",".$rows2[1].">".$rows2[1]."</option>";
	}
}
?></select></td>
      </tr>
      <tr>
        <td height="40" align="right">圈子名称 </td>
        <td align="left">
          <input name="title" id="title" type="text" class="input W150" size="58" maxlength="20" /></td>
      </tr>
      <tr>
        <td height="40" align="right">开放加入</td>
        <td align="left">
        
<input type="radio" name="ifopen" id="ifopen0" class="radioskin" value="0" ><label for="ifopen0" class="radioskin-label"><i class="i1"></i><b class="W50">关闭</b></label>
<input type="radio" name="ifopen" id="ifopen1" class="radioskin" value="1" checked><label for="ifopen1" class="radioskin-label"><i class="i1"></i><b class="W50">开放</b></label>
<input type="radio" name="ifopen" id="ifopen2" class="radioskin" value="2" ><label for="ifopen2" class="radioskin-label"><i class="i1"></i><b class="W50">需要审核</b></label>

</td>
      </tr>
      <tr>
        <td height="40" align="right">看　　帖</td>
        <td align="left">
          
<input type="radio" name="ifin" id="ifin0" class="radioskin" value="0" checked><label for="ifin0" class="radioskin-label"><i class="i1"></i><b class="W50">所有会员</b></label>
<input type="radio" name="ifin" id="ifin1" class="radioskin" value="1" ><label for="ifin0" class="radioskin-label"><i class="i1"></i><b class="W50">圈内成员</b></label>
          
          </td>
      </tr>
      <tr>
        <td height="40" align="right">发　　帖</td>
        <td align="left">

<input type="radio" name="ifin2" id="ifin20" class="radioskin" value="1" checked><label for="ifin20" class="radioskin-label"><i class="i1"></i><b class="W50">所有会员</b></label>
<input type="radio" name="ifin2" id="ifin21" class="radioskin" value="0" ><label for="ifin21" class="radioskin-label"><i class="i1"></i><b class="W50">圈内成员</b></label>




</td>
      </tr>
      <tr>
        <td height="40" align="right">所在地区</td>
        <td align="left">
		<script>LevelMenu3('m1|m2|m3|'+nulltext+'|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>','class="select SW2"');</script></td>
      </tr>
      <tr>
        <td align="right" valign="top">圈子介绍</td>
        <td align="left">
<textarea name="content" cols="90" rows="8" id="content" placeholder="请认真填写圈子详细介绍(20～1000字节)，圈子一旦建立将无法修改，建好以后若需修改请联系客服。"></textarea></td>
      </tr>
      <tr>
        <td height="7" align="center">&nbsp;</td>
        <td height="60" align="left" valign="top">
        <input name="submitok" type="hidden"  value="addupdate" />
         <input name="areaid" id="areaid" type="hidden" value="" />
         <input name="areatitle" id="areatitle" type="hidden" value="" />
        <input type="submit" class="btn2" value="提交申请" <?php if ($data_grade == 1)echo "disabled='disabled'"; ?> /></td>
      </tr>
    </form>
  </table>	
</em>
<script>input("GYLform");</script>
<?php }?>


<!--  版块 -->
<?php } elseif ($submitok == 'bk'){  ?>
<style>
.groupbk{width:100%}
#content{width:90%;margin-top:5px;font-size:12px;padding:3%}
.groupbk tr td{font-size:12px;padding:10px 0;border-bottom:#e4e4e4 1px solid}
.groupbk tr td:first-child{width:70px;line-height:40px;padding-right:5px}
.groupbk tr td:last-child{color:#999;padding-left:5px}
.groupbk tr:last-child td{border:0}

.bk_box{width:100%;background-color:#fff;border-top:#e4e4e4 1px solid;border-bottom:#e4e4e4 1px solid;margin:20px auto;text-align:left;padding-top:10px}
.tbbklist{width:100%;border-bottom:#f0f0f0 20px solid}
.tbbklist tr td{font-size:12px;padding:10px 0;border-bottom:#e4e4e4 1px solid}
.tbbklist tr td:first-child{width:70px;line-height:40px;padding-right:5px}
.tbbklist tr td:last-child{color:#999;padding-left:5px}
.tbbklist tr:last-child td{border:0}
a.preview{position:absolute;top:6px;right:15px;width:70px;line-height:32px;border-radius:2px;color:#fff;font-size:14px;background-color:rgba(0,0,0,0.2);z-index:10}
a.preview:hover{background-color:rgba(0,0,0,0.1)}
</style>
<?php if ($submitson == "add") {?>
<br>
<br>
<script>
function zeai5_0_form(){
	if(  str_len(o('title').value) < 1 || str_len(o('title').value) > 50  ){
		ZEAI_win_alert("版块名称长度1～25个字节之间！");
		o("title").focus();
		return false;
	}
	if(str_len(o('content').value)<10 || str_len(o('content').value)>400){
		ZEAI_win_alert('说明长度要在10～200字节之间');o('content').focus();return false;
	}
}
</script>
<table class="table0 groupbk">
    <form action="<?php echo $SELF;?>" method="post" onsubmit="return zeai5_0_form();">
    <tr>
    <td width="75" align="right" valign="top" bgcolor="#FFFFFF">版块名称</td>
    <td align="left" bgcolor="#FFFFFF"><font color="#666666">
    <input name="title" type="text" class="input W150" id="title" size="30" maxlength="20">
    </font></td>
    </tr>
    <tr>
    <td width="75" align="right" valign="top" bgcolor="#FFFFFF">版块说明</td>
    <td align="left" bgcolor="#FFFFFF"><font color="#666666">
    <textarea name="content" cols="70" rows="6" id="content"></textarea>
    <br />
    </font></td>
    </tr>
    <tr>
    <td width="75" align="right" bgcolor="#FFFFFF"><input name="submitson" type="hidden" value="addupdate">
    <input name="mainid" type="hidden" value="<?php echo $mainid; ?>"><input name="submitok" type="hidden" value="<?php echo $submitok; ?>"></td>
    <td align="left" bgcolor="#FFFFFF"><input type="submit" name="Submit" value=" 保存 " class="btn2"></td>
    </tr>
    </form>
</table>
<br>
<br>
<?php } else {?>
<a href="<?php echo $SELF; ?>?submitok=bk&submitson=add&mainid=<?php echo $mainid;?>" class="preview" id="preview">增加版块</a>
<?php
$rt = $db->query("SELECT * FROM ".__TBL_GROUP_BK__." WHERE mainid='$mainid' ORDER BY px DESC,id DESC");
$total = $db->num_rows($rt);
if ($total <= 0 ) {?>
  <div class="nodatatips W150"><br>..暂无版块..<br><a href="<?php echo $SELF; ?>?submitok=bk&submitson=add&mainid=<?php echo $mainid;?>" class="aLAN">点此增加</a><br><br></div>
<?php } else {?>
    <div class="bk_box">
	<?php
    for($i=1;$i<=$total;$i++) {
        $rows = $db->fetch_array($rt,'all');
        if(!$rows) break;
        ?>
        <table class="table0 tbbklist" >
        <form action="<?php echo $SELF; ?>?p=<?php echo $p; ?>" method=post>
        <tr>
        <td width="70" align="right" class="td">
        版块名称
        <input type="hidden" name="submitson" value="modupdate" />
        <input type="hidden" name="bkid" value="<?php echo $rows['id']; ?>" />
        <input type="hidden" name="submitok" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="mainid" value="<?php echo $mainid; ?>" /></td>
        <td height="26" align="left" valign="top" class="td"><input name="title" type="text" class="input W150" id="title" value="<?php echo stripslashes($rows['title']); ?>" size="18" maxlength="50" style="margin-bottom:2px" /> <a href="<?php echo $SELF; ?>?submitok=bk&submitson=delupdate&bkid=<?php echo $rows['id']; ?>&mainid=<?php echo $mainid; ?>" class="u666" onClick="return confirm('请 慎 重 ！\n\n★确认删除？\n\n此操作将联动删除该分类下的所有帖子。建议修改。')"><img src="../images/del.png" width="18" height="20" align="right" style="margin-right:10px"></a></td>
        </tr>
        <tr>
          <td width="70" align="right" class="td">版主ID号</td>
          <td height="26" align="left" valign="top" class="td"><input name="userid" type="text" class="input" id="userid" value="<?php echo $rows['userid']; ?>" size="6" maxlength="9">
          <?php
        if (!empty($rows['userid'])){
        $rtD=$db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$rows['userid']);
        if ($db->num_rows($rtD)){
        $rowD = $db->fetch_array($rtD);
        $nickname = dataIO($rowD[0],'out');
        $sex    = $rowD[1];
        $grade  = $rowD[2];
        }
		
        echo uicon($sex.$grade)." <a href=".HOST."/?z=index&e=u&a=".$rows['userid']." class=u666>".$nickname."</a>";
        }?></td>
          </tr>
        <tr>
          <td width="70" height="60" align="right" class="td">版块说明</td>
          <td height="60" align="left" valign="top" class="td"><textarea name="content" cols="20" rows="4" id="content" style="overflow-y:auto"><?php echo stripslashes($rows['content'])?></textarea></td>
          </tr>
        <tr>
          <td width="70" align="right" class="td">排　　序</td>
          <td height="26" align="left" valign="top" class="td"><input name="px" type="text" class="input W50" id="px" value="<?php echo $rows['px']; ?>" size="3" maxlength="5" onKeyPress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;"> <font class="C999 S12">越大版块越靠前。请填写正整数</font></td>
          </tr>
        <tr>
          <td colspan="2" align="center" class="td"><input type="submit" name="submit" value="修改当前" class="btn2" />
          　</td>
          </tr>
        </form>
        </table>
    <?php } ?>
</div>
<?php }} ?>


<!-- 基本设置 -->
	<?php } elseif ($submitok == 'mod'){  ?>
    <em class="my_group_mod">
<?php 
$rt = $db->query("SELECT * FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$cook_uid." AND id=".$mainid);
$total = $db->num_rows($rt);
if($total > 0){
$row = $db->fetch_array($rt,'all');
?><br />
<br />
  <table class="table0">
    <form action="<?php echo $SELF; ?>" method="post" name="nc.aiez.www" id="FORM">
      <tr bgcolor="#ECFAFF">
        <td width="90" align="right" bgcolor="#F8F8F8">圈子状态</td>
        <td align="left" bgcolor="#FFFFFF"><select name="flag" class="select W80">
            <option value="1" <?php if ($row['flag'] == 1)echo "selected";?> style="color:#009900">正常</option>
            <option value="-1" <?php if ($row['flag'] == -1)echo "selected";?> style="color:#0000FF">隐藏</option>
          </select></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td align="right" bgcolor="#F8F8F8">新成员加入</td>
        <td align="left" bgcolor="#FFFFFF">
	

<input type="radio" name="ifopen" id="ifopen0" class="radioskin" value="0"  <?php if ($row['ifopen'] == 0)echo "checked";?>><label for="ifopen0" class="radioskin-label"><i class="i1"></i><b class="W50">关闭</b></label>
<input type="radio" name="ifopen" id="ifopen1" class="radioskin" value="1"  <?php if ($row['ifopen'] == 1)echo "checked";?>><label for="ifopen1" class="radioskin-label"><i class="i1"></i><b class="W50">开放</b></label>
<input type="radio" name="ifopen" id="ifopen2" class="radioskin" value="2"  <?php if ($row['ifopen'] == 2)echo "checked";?>><label for="ifopen2" class="radioskin-label"><i class="i1"></i><b class="W50">需要审核</b></label>



　<a href="#" onclick="alert(' ● 选“关闭”，将拒绝任何新成员加入。\n\n ● 选“开放”，别人可以直接加入你的圈子，不需要通过你的验证。\n\n ● 点“需要审核”，别人想成为你的成员。必须经过你的验证审核。');" class="yellow">帮助</a></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td align="right" bgcolor="#F8F8F8">看　　帖</td>
        <td align="left" bgcolor="#FFFFFF">
        
<input type="radio" name="ifin" id="ifin1" class="radioskin" value="1"  <?php if ($row['ifin'] == 1)echo "checked";?>><label for="ifin1" class="radioskin-label"><i class="i1"></i><b class="W50">所有会员</b></label>
<input type="radio" name="ifin" id="ifin0" class="radioskin" value="0"  <?php if ($row['ifin'] == 0)echo "checked";?>><label for="ifin0" class="radioskin-label"><i class="i1"></i><b class="W50">圈内成员</b></label>
          
          
          　<a href="#" onclick="alert(' ● 点“所有会员”即完全开放，所有人都可以在你的圈子内看帖子。\n\n ● 点“圈内成员”只有你圈子内成员才能看帖子。');" class="yellow">帮助</a></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td align="right" bgcolor="#F8F8F8">发　　帖</td>
        <td align="left" bgcolor="#FFFFFF">
        
  
<input type="radio" name="ifin2" id="ifin21" class="radioskin" value="1"  <?php if ($row['ifin2'] == 1)echo "checked";?>><label for="ifin21" class="radioskin-label"><i class="i1"></i><b class="W50">所有会员</b></label>
<input type="radio" name="ifin2" id="ifin20" class="radioskin" value="0"  <?php if ($row['ifin2'] == 0)echo "checked";?>><label for="ifin20" class="radioskin-label"><i class="i1"></i><b class="W50">圈内成员</b></label>
  
  　<a href="#" onclick="alert(' ● 点“所有会员”即完全开放，所有人都可以在你的圈子内发帖。\n\n ● 点“圈内成员”只有你圈子内成员才能发帖。');" class="yellow">帮助</a></td>
      </tr>
      <tr bgcolor="#ECFAFF">
        <td height="3" align="right" bgcolor="#F8F8F8">副会长①ID号</td>
        <td height="0" align="left" bgcolor="#FFFFFF"><input name="form_userid1" type="text" class="input" id="form_userid1" value="<?php echo $row['userid1']; ?>" size="8" maxlength="9" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;">
<?php
if (!empty($row['userid1'])){
	$rtD=$db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$row['userid1']);
	if ($db->num_rows($rtD)){
		$rowD = $db->fetch_array($rtD);
		$nickname1  = $rowD[0];
		$sex1  = $rowD[1];
		$grade1  = $rowD[2];
	}
echo uicon($sex1.$grade1);
echo " <a href=".HOST."/?z=index&e=u&a=".$row['userid1']." class=u666>".$nickname1."</a>";
} else {
	
	
	
echo " <font color=#999999>(暂无)</font>";
}
?>　<a href="#" onclick="alert('● 在左边的框中填入Ta的ID号，不是用户名也不是昵称哟。\n\n● 删除此副会长请填0。');" class="yellow">帮助</a></td>
      </tr>
      <tr bgcolor="#ECFAFF">
        <td height="3" align="right" bgcolor="#F8F8F8">副会长②ID号</td>
        <td height="0" align="left" bgcolor="#FFFFFF"><input name="form_userid2" type="text" class="input" id="form_userid2" value="<?php echo $row['userid2']; ?>" size="8" maxlength="9" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;">
            <?php
if (!empty($row['userid2'])){
	$rtD=$db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$row['userid2']);
	if ($db->num_rows($rtD)){
		$rowD = $db->fetch_array($rtD);
		$nickname2  = $rowD[0];
		$sex2  = $rowD[1];
		$grade2  = $rowD[2];
	}
echo uicon($sex2.$grade2);
echo "<a href=".HOST."/?z=index&e=u&a=".$row['userid2']." class=u666>".$nickname2."</a>";
} else {
echo " <font color=#999999>(暂无)</font>";
}
?>　<a href="#" onclick="alert('● 在左边的框中填入Ta的ID号，不是用户名也不是昵称哟。\n\n● 删除此副会长请填0。');" class="yellow">帮助</a></td>
      </tr>
      <tr bgcolor="#ECFAFF">
        <td height="7" align="right" bgcolor="#F8F8F8">副会长③ID号</td>
        <td height="0" align="left" bgcolor="#FFFFFF"><input name="form_userid3" type="text" class="input" id="form_userid3" value="<?php echo $row['userid3']; ?>" size="8" maxlength="9" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;">
            <?php
if (!empty($row['userid3'])){
	$rtD=$db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$row['userid3']);
	if ($db->num_rows($rtD)){
		$rowD = $db->fetch_array($rtD);
		$nickname3  = $rowD[0];
		$sex3  = $rowD[1];
		$grade3  = $rowD[2];
	}
echo uicon($sex3.$grade3);
echo "<a href=".HOST."/?z=index&e=u&a=".$row['userid3']." class=u666>".$nickname3."</a>";
} else {
echo " <font color=#999999>(暂无)</font>";
}
?>　<a href="#" onclick="alert('● 在左边的框中填入Ta的ID号，不是用户名也不是昵称哟。\n\n● 删除此副会长请填0。');" class="yellow">帮助</a></td>
      </tr>
      <tr bgcolor="#ECFAFF">
        <td height="7" align="center" bgcolor="#F8F8F8"><input type="hidden" value="<?php echo $mainid; ?>" name="mainid" />
            <input type="hidden" value="modupdate" name="submitok" /></td>
        <td height="7" align="left" bgcolor="#FFFFFF"><input type="submit" class="btn2" value=" 保存 "></td>
      </tr>
    </form>
  </table><br><br><br>
<?php
} else {
	callmsg("Forbidden!","-1");
}
?>
	</em>

<!-- 交友活动 -->
<?php } elseif ($submitok == 'party'){  ?>
<style>
#content{width:90%;margin-top:5px;font-size:12px;padding:3%}
.formtbl{width:100%;background-color:#fff;border-top:#e4e4e4 1px solid;border-bottom:#e4e4e4 1px solid;margin:10px auto 20px auto;text-align:left;padding-top:10px}
.formtbl tr td{font-size:12px;padding:10px 0;border-bottom:#e4e4e4 1px solid}
.formtbl tr td:first-child{width:70px;line-height:40px;padding-right:5px}
.formtbl tr td:last-child{color:#999;padding-left:5px}
.formtbl tr:last-child td{border:0}
.timebox input{margin-bottom:5px}
a.preview{position:absolute;top:6px;right:15px;width:70px;line-height:32px;border-radius:2px;color:#fff;font-size:14px;background-color:rgba(0,0,0,0.2);z-index:10}
a.preview:hover{background-color:rgba(0,0,0,0.1)}
</style>
<a href="<?php echo $SELF; ?>?submitok=party&submitson=add&mainid=<?php echo $mainid;?>" class="preview" id="preview">新增活动</a>

	<!--活动发布 -->
	<?php if ($submitson=="add") { ?>
	<script>
    function chkform(){
        if(document.FORM.title.value.length<6 || document.FORM.title.value.length>100){
            ZEAI_win_alert('活动名称请控制 6~100 字节!','title');
            return false;
        }
        if(document.FORM.kind.value.length<2 || document.FORM.kind.value.length>100){
            ZEAI_win_alert('活动类型请控制 2~100 字节!','kind');
            return false;
        }	
        if(document.FORM.hdtime.value.length<2 || document.FORM.hdtime.value.length>100){
            ZEAI_win_alert('活动时间请控制 2~100 字节!','hdtime');
            return false;
        }	
        if(document.FORM.year8.value.length !== 4){
            ZEAI_win_alert('请输入正确格式的截止报名时间“年”，必须是有效的4位数字，如：2017','year8',600);
            return false;
        }		
    
        if(document.FORM.month8.value.length !== 2){
            ZEAI_win_alert('请输入正确格式的截止报名时间“月”，必须是有效的2位数字，如：08','month8',600);
            document.FORM.month8.focus();
            return false;
        }	
    
        if(document.FORM.day8.value.length !== 2){
            ZEAI_win_alert('请输入正确格式的截止报名时间“日”，必须是有效的2位数字，如：08','day8',600);
            return false;
        }		
        if(document.FORM.hour8.value.length !== 2){
            ZEAI_win_alert('请输入正确格式的截止报名时间的“时”，必须是有效的2位数字，如：18','hour8',600);
            return false;
        }
        if(document.FORM.minute8.value.length !== 2){
            ZEAI_win_alert('请输入正确格式的截止报名时间“分”，必须是有效的2位数字，如：30','minute8',600);
            return false;
        }
        if(document.FORM.address.value.length<2 || document.FORM.address.value.length>100){
            ZEAI_win_alert('活动地点请控制 2~100 字节!','address');
            return false;
        }	
        if(document.FORM.jtlx.value.length<2 || document.FORM.jtlx.value.length>100){
            ZEAI_win_alert('交通路线请控制 2~100 字节!','jtlx');
            return false;
        }	
        if(document.FORM.num_n.value.length<1 || document.FORM.num_n.value.length>5){
            ZEAI_win_alert('“男士”人数限定请控制 1~5 字节!','num_n');
            return false;
        }	
        if(document.FORM.num_r.value.length<1 || document.FORM.num_r.value.length>5){
            ZEAI_win_alert('“女士”人数限定请控制 1~5 字节!','num_r');
            return false;
        }
            
        if(document.FORM.tbsm.value.length<1 || document.FORM.tbsm.value.length>500){
            ZEAI_win_alert('特别说明请控制 1~500 字节!','tbsm');
            return false;
        }	
        
        if(str_len(o('content').value)<20 || str_len(o('content').value)>10000){
            ZEAI_win_alert('活动详细说明请控制在20~10000字节！','content');return false;
        }else{o('content').value = clear2bx(o('content').value);}	
    }
    </script>

  <h2>交友活动</h2>
	<table class="table0 formtbl">
    <form action="<?php echo $SELF; ?>" method="post" name="FORM"  onSubmit="return chkform()" onClick="clear2bx(o('content').value)">
          <tr >
            <td align="right">发 起 人</td>
            <td align="left" ><?php echo uicon($cook_sex.$cook_grade); ?>
            <font face="Verdana, Arial, Helvetica, sans-serif"><?php echo $cook_nickname; ?>，ID号：<?php echo $cook_uid; ?></td>
          </tr>
          <tr >
            <td align="right">所属圈子</td>
            <td align="left"><a href="<?php echo $_ZEAI['group2'];?>/<?php echo $mainid; ?>" class="uc03"><?php echo $maintitle; ?></a></td>
          </tr>
          <tr>
            <td align="right">活动名称</td>
            <td align="left" valign="top"><input name="title" id="title" type="text" class="input W90_" size="64" maxlength="100"></td>
          </tr>


          <tr>
            <td align="right">活动类型</td>
            <td align="left" valign="top"><input name="kind" id="kind" type="text" class="input" size="20" maxlength="100"> 交友，征婚，旅游等等</td>
          </tr>
          <tr>
            <td align="right" valign="top" style="padding-top:10px">活动时间</td>
            <td align="left" valign="top" style="color:#888"><input name="hdtime" type="text" class="input W90_" id="hdtime" size="64" maxlength="100">
              <br>活动具体时间段，请标注清楚。<br>
              <font color="#FF0000">格式：</font><?php echo date("Y"); ?>年8月19日 下午13：00 — 17：00<br></td>
          </tr>
          <tr>
            <td align="right" valign="top" ><font color="#FF0000">截止时间</font></td>
            <td align="left" valign="top" class="timebox">
              <input name="year8" type="text" class="input W50" id="year8" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo date('Y') ?>" size="4" maxlength="4">
            年
            <input name="month8" type="text" class="input W50" id="month8" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" size="2" maxlength="2" >
            月
            <input name="day8" type="text" class="input W50" id="day8" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" size="2" maxlength="2">
            日<br>
            <input name="hour8" type="text" class="input W50" id="hour8" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="00" size="2" maxlength="2">
            时
            <input name="minute8" type="text" class="input W50" id="minute8" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="00" size="2" maxlength="2">
            分<div class="tips">请填写大于现在1天以上的日期，否则审核不予通过</div></td>
          </tr>
          <tr>
            <td align="right" >活动地点</td>
            <td align="left" valign="top"><input name="address" id="address" type="text" class="input W90_" size="64" maxlength="100"></td>
          </tr>
          <tr>
            <td align="right">交通路线</td>
            <td align="left" valign="top"><input name="jtlx" id="jtlx" type="text" class="input W90_" size="64" maxlength="100"></td>
          </tr>
          <tr>
            <td align="right">人数限定</td>
            <td align="left" valign="top">男
              <input name="num_n" id="num_n" type="text" size="3" maxlength="5" class="input W50" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
              人 ，女
              <input name="num_r" id="num_r" type="text" size="3" maxlength="5" class="input W50" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
            人</td>
          </tr>
          <tr>
            <td align="right">活动费用</td>
            <td align="left" valign="top">男
              <input name="rmb_n" id="rmb_n" type="text" class="input W50" value="0" size="3" maxlength="5" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
              元 ，女
              <input name="rmb_r" id="rmb_r" type="text" class="input W50" value="0" size="3" maxlength="5" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
            元</td>
          </tr>
          <tr>
            <td align="right" valign="top">特别说明</td>
            <td align="left" valign="top"><textarea name="tbsm" cols="65" rows="4" id="tbsm" class="W90_"></textarea></td>
          </tr>
          <tr>
            <td colspan="2" align="center"><textarea name="content" id="content" style="height:200px" class="W90_" placeholder="活动详细说明"></textarea></td>
          </tr>
          <tr>
            <td colspan="2" align="center"><input name="submitson" type="hidden" value="addupdate" />
              <input name="mainid" type="hidden" value="<?php echo $mainid; ?>" />
			   <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
              <input name="maintitle" type="hidden" value="<?php echo $maintitle; ?>" />
              <input type="submit" name="submit" value=" 确认发布 " class="btn2" /></td>
          </tr>
        </form>
    </table>    
    
    <!--活动修改 -->
	<?php } elseif ($submitson=="mod") {?>






	<!-- 活动报名会员管理 -->
	<?php }elseif($submitson=="user") {?>
		<?php 
		$rt = $db->query("SELECT title,flag,jzbmtime FROM ".__TBL_GROUP_CLUB__." WHERE id=".$clubid);
		if($db->num_rows($rt)){
			$row      = $db->fetch_array($rt,'all');
			$clubtitle= dataIO($row['title'],'out');
			$mainflag = $row['flag'];
			?>
  <h2>【<?php echo $clubtitle; ?>】报名管理</h2>
			<em class="my_group_party">
				<table align="center" class="table0 W90_" style="border:#e7e7e7 1px solid;">
				<form method="post" action="<?php echo $SELF; ?>">
				<tr>
				<td height="70" align="center" class="S12 C999">
				<?php
				$d1  = $ADDTIME;
				$d2  = strtotime($row['jzbmtime']);
				$totals  = ($d2-$d1);
				$day     = intval( $totals/86400 );
				$hour    = intval(($totals % 86400)/3600);
				$hourmod = ($totals % 86400)/3600 - $hour;
				$minute  = intval($hourmod*60);
				if ($row['flag'] >2)$totals = -1;
				echo '<br><br>截止报名时间：'.$row['jzbmtime'].'　'.getweek(date_format3($row['jzbmtime'],'%Y-%m-%d')).'<br>';
				if (($totals) > 0) {
					if ($day > 0){
						$outtime = "报名还有 <span class=timestyle>$day</span> 天 ";
					} else {
						$outtime = "报名还有 ";
					}
					$outtime .= "<span class=timestyle>$hour</span> 小时 <span class=timestyle>$minute</span> 分";
				} else {
					$outtime = "<font color=#999999><b>已经结束</b></font>";
					if ($row['flag'] == 1)$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET flag=3 WHERE id=".$clubid);
					$mainflag = 3;
				}
				echo '<span class=timestyletext>'.$outtime.'</span>';
				//if ($mainflag == 0){
				//	echo '<br><br><a href='.$SELF.'?mainid='.$mainid.'&submitok=party&submitson=mod&fid='.$clubid.' class="uc03 B">修改截止报名时间</a><br><br>';
				//} else {
				//    echo '<br><br><font color=#999999>此状态修改截止报名时间请联系客服</font><br><br>';
				//}
				?>
				</td>
				</tr>
				<tr>
				  <td height="70" align="center">
				<input name="submitson" type="hidden" value="user_flagupdate">
				<input name="mainid" type="hidden" value="<?php echo $mainid; ?>">
				<input name="submitok" type="hidden" value="<?php echo $submitok; ?>">
				<input name="clubid" type="hidden" value="<?php echo $clubid;?>">
				<input type="submit" value="结束此活动" class="<?php echo ($mainflag == 3)?'btn2HUI':'btn2HUANG';?>" onClick="return confirm('您真的要结束此活动吗')" <?php if ($mainflag == 3)echo "disabled='disabled'";?>>
				<br><br></td>
				  </tr>
				</form>
				</table>                
			</em>
			<?php 
			$rt=$db->query("SELECT a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,b.userid,b.addtime,b.flag,b.tel,b.id FROM ".__TBL_USER__." a,".__TBL_GROUP_CLUB_USER__." b WHERE a.id=b.userid AND b.clubid=".$clubid." ORDER BY b.id DESC");
			$total = $db->num_rows($rt);
			?>
  <h2>报名会员(<?php echo $total; ?>)</h2>
			<em class="user my_group_party_user">
				<?php 
				if ($total > 0) {
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt);
						if(!$rows) break;
						$nickname = urldecode(dataIO($rows[0],'out'));
						$sex      = $rows[1];
						$grade    = $rows[2];
						$photo_s  = $rows[3];
						$photo_f  = $rows[4];
						$uid      = $rows[5];
						$addtime  = $rows[6];
						$flag     = $rows[7];
						$tel      = dataIO($rows[8],'out');
						$id       = $rows[9];
						$href = HOST.'/?z=index&e=u&a='.$uid;
						$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
						$img_str     = '<img src="'.$photo_s_url.'" class="imgbdr'.$sex.'">';
						$addtime_str = YmdHis(strtotime($addtime),'YmdHi');
						?>
						<li><a href="<?php echo $href; ?>"><?php echo $img_str; ?><h4><i class="s<?php echo $sex.$grade; ?>"></i><?php echo $nickname; ?></h4></a>
							<h4><?php echo $addtime_str; ?></h4>
							<h4><a href="tel:<?php echo $tel; ?>"><font class="S14"><?php echo $tel; ?></font></a></h4>
							<?php if ($mainflag == 1) {?>
							<h4>
<?php if ($flag == 0){ ?><a href="<?php echo $SELF; ?>?mainid=<?php echo $mainid; ?>&clubid=<?php echo $clubid; ?>&classid=<?php echo $id; ?>&submitok=party&submitson=flag1&p=<?php echo $p; ?>" class="aHUANG" onClick="return confirm('确认审核【<?php echo $nickname; ?>】？')">审核</a>　<?php } ?>
<a href=<?php echo $SELF; ?>?mainid=<?php echo $mainid; ?>&clubid=<?php echo $clubid; ?>&classid=<?php echo $id; ?>&submitok=party&submitson=flag0&p=<?php echo $p; ?> class="aHUI" onClick="return confirm('确认踢除【<?php echo $nickname; ?>】？')">踢除</a>
							</h4>
							<?php }?>
						</li>
				<?php }}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
			</em>
  <?php } else {echo '<div class="nodatatipsS">暂时还没有人报名</div>';}?>
  <!-- 活动照片 -->
  <?php }elseif($submitson=="photo") {?>
  <h2>活动照片</h2>
	 <em>
		<div class="picli">
			<i class="add"></i>
			<?php 
			$rt=$db->query("SELECT * FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE mainid=".$mainid." AND clubid=".$clubid." ORDER BY ifmain DESC,id DESC");
			$total = $db->num_rows($rt);
			if ($total > 0) {
				$pic_list = '';
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'all');
					if(!$rows) break;
					$id     = $rows['id'];
					$path_s = $rows['path_s'];
					$path_b = $rows['path_b'];
					$dst_s    = $_ZEAI['up2'].'/'.$path_s;
					$dst_b    = $_ZEAI['up2'].'/'.$path_b;
					$pic_list .= '"'.$dst_b.'",';
					//$flagstr = ($flag == 0)?'<span>审核中</span>':'';
					$list_str  = '';
					$liid      = 'li'.$id;
					$list_str .= '<i id="'.$liid.'">';
					$list_str .= '<a href="javascript:;" onclick="picview(\''.$dst_b.'\')"><img src="'.$dst_s.'">'.$flagstr.'</a>';
					$list_str .= '<a href="javascript:;" class="del" onclick="ZEAI_win_confirm(\'确认删除么？\',\'XML_del('.$id.')\')"></a>';
					$list_str .= '</i>';
					echo $list_str;
						
				}
				$pic_list = rtrim($pic_list,',');
			}?>
		</div>
	 </em>
  <?php }else{  ?>
  <!--交友活动列表 -->
		<h2>交友活动</h2>
		<em class="my_group_party">
			<?php
			$rt=$db->query("SELECT id,title,num_n,num_r,flag,jzbmtime,bmnum FROM ".__TBL_GROUP_CLUB__." WHERE mainid=".$mainid." ORDER BY id DESC");
			$total = $db->num_rows($rt);
			if ($total > 0) {
				for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$id    = $rows['id'];
				$title = dataIO($rows['title'],'out');
				$flag  = $rows['flag'];
				$bmnum = $rows['bmnum'];
				$href  = $_ZEAI['group2'].'/group_partyshow.php?fid='.$id;
				switch ($flag){ 
					case 0:
						$flag_str = "<font color=red>（审核中）</font>";
					break;
					case 1:
						$flag_str = "<font color=#0066CC>（报名中）</font>";
					break;
					case 2:
						$flag_str = "<font color=#ff6600>（进行中）</font>";
					break;
					case 3:
						$flag_str = "<font color=#349933>（圆满结束）</font>";
					break;
				}
				?>
				<li>
				<img src="<?php echo $_ZEAI['group2']; ?>/images/qzlist.gif"> <a href="<?php echo $href; ?>"><h3><?php echo $title; ?></h3>
				<?php echo $flag_str; ?><?php if ($flag == 1){echo "<img src=".$_ZEAI['group2']."/images/new2.gif hspace=6>";}?></a>
				<br>
				<?php if ($flag > 0) {?>
				<a href="<?php echo $SELF; ?>?submitok=party&submitson=user&mainid=<?php echo $mainid; ?>&clubid=<?php echo $id;?>" class="aHUI">报名人数(<font class="Cf00"><?php echo $bmnum; ?></font>)管理</a>
				<a href="<?php echo $SELF; ?>?submitok=party&submitson=photo&mainid=<?php echo $mainid; ?>&clubid=<?php echo $id;?>" class="aHUI">上传照片</a>
				<?php }?>
				</li>
			<?php }}else{?>
            <div class="nodatatips W150"><br>..暂无活动..<br><br><a href="<?php echo $SELF; ?>?mainid=<?php echo $mainid;?>&submitok=party&submitson=add" class="aLAN">＋点此发布新活动</a><br><br></div>   
			<?php }?>
		</em>
	<?php }?>
<!-- 交友活动结束 -->
  
<!-- 圈子成员 -->
<?php } elseif ($submitok == 'user'){  ?>
<style>
.user_box{width:100%;background-color:#fff;border-top:#e4e4e4 1px solid;border-bottom:#e4e4e4 1px solid;margin:20px auto;text-align:left;padding-top:10px}
.user_box .table0{width:100%}
.photo_sx img{display:block}
a.photo_sx {width:60px;height:60px;display:block;margin:0 auto;text-align:center;border:#fff 2px solid;border-radius:50%}
a.photo_sx img{width:60px;height:60px;border-radius:50%;margin:0 auto}
a.photo_sx.flag0{border-color:#fc0}
#keyword{width:100px;height:28px;margin-right:5px}
</style>
<div class="user_box">
<?php if ($submitson == 'list') {
$kSQL = "";
$keyword = trimm($keyword);
if (!empty($keyword))$kSQL = " AND (a.nickname LIKE '%".$keyword."%' OR b.userid='$keyword') ";
$rt=$db->query("SELECT a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,b.id,b.userid,b.flag,b.addtime FROM ".__TBL_USER__." a,".__TBL_GROUP_USER__." b WHERE a.id=b.userid AND b.mainid=".$mainid.$kSQL." ORDER BY b.flag,b.id DESC");
$total = $db->num_rows($rt);
?>
    <table width="table0" style="border-bottom:#e7e7e7 1px solid;margin:0 auto 0 auto;color:#666;width:90%">
    <tr>
    <td height="50" align="left" style="font-size:14px"><?php echo $maintitle; ?><span class="S12">(共<font class="Cf00"><?php echo $total; ?></font>名)</span></td>
    <td width="180" align="right"><form action="<?php echo $SELF; ?>" method="get"><input name="submitok" type="hidden" value="<?php echo $submitok; ?>" /><input name="mainid" type="hidden" value="<?php echo $mainid; ?>" />
    <input name="keyword" type="text" id="keyword" size="20" maxlength="15" placeholder="输入昵称ID号" class="input" /><input type="submit" value="搜索" class="btn">
    </form></td>
    </tr>
    </table>
<?php
if($total>0){
	$pagesize=24;
	require_once '../page.php';
	if ($p<1)$p=1;
	$mypage=new zeaipage($total,$pagesize);
	$pagelist = $mypage->pagebar();
	$db->data_seek($rt,($p-1)*$pagesize);?>
	<table class="table0">
	<tr>
<?php
	function getauthority($str) {
	global $db,$mainid,$userid_main,$userid1_main,$userid2_main,$userid3_main;
	$rtauthority = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_BK__." WHERE mainid=".$mainid." AND userid=".$str);
	$rowauthority = $db->fetch_array($rtauthority);
	$tmpcntauthority = $rowauthority[0];
	if ($str == $userid_main) {
	return "1|<font class='red B'>会长(创始人)</font>";
	} elseif ($str == $userid1_main || $str == $userid2_main || $str == $userid3_main) {
	return "2|<font class='red'>副会长</font>";
	} elseif ($tmpcntauthority >0 ) {
	return "3|<font color=ff6600>论坛版主</font>";
	}else{return false;}}
	for($j=1;$j<=$pagesize;$j++) {
		$rows = $db->fetch_array($rt,'all');
		if(!$rows) break;
		$nickname = dataIO($rows[0],'out');
		$sex      = $rows[1];
		$grade    = $rows[2];
		$photo_s  = $rows[3];
		$photo_f  = $rows[4];
		$flag     = $rows[5];
		$uid      = $rows[6];
		$href        = HOST.'/?z=index&e=u&a='.$id;
		$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		$img_str     = '<img src="'.$photo_s_url.'" class="imgbdr'.$sex.'">';
		$flagcls = ($flag == 1)?"":" flag0";
		?>
        <td align="center" valign="top" style="padding-top:10px">
            <table class="table0">
                <tr><td align="center"><a href="<?php echo $href ?>" class="photo_sx<?php echo $flagcls; ?>"><?php echo $img_str; ?></a></td></tr>
                <tr><td align="center"><?php echo uicon($sex.$grade); ?> <a href="<?php echo $href; ?>" class="sexico<?php echo $sex; ?>"><?php echo $nickname; ?></a></td></tr>
                <tr><td align="center">&nbsp;<?php
                $authorityArr = getauthority($uid);
                if ($authorityArr){
                    $authorityArr = explode('|',$authorityArr);
                    $authoritygrade = $authorityArr[0];
                    $authoritytext  = $authorityArr[1];
                    echo $authoritytext; 
                }?></td></tr>
                <tr><td height="30" align="center">
                <?php if ($rows['flag'] == 0){?>
                <a href="<?php echo $SELF; ?>?submitok=user&mainid=<?php echo $mainid; ?>&classid=<?php echo $rows['id'] ; ?>&submitson=flag&p=<?php echo $p; ?>" onclick="return confirm('确认通过验证 “<?php echo $nickname; ?>”？')" class="Cf00 S12">通过验证</a>
                <?php }?>
                <?php if ($mainuserid != $Uid){ ?>
                　 <a href="<?php echo $SELF; ?>?submitok=user&mainid=<?php echo $mainid; ?>&classid=<?php echo $rows['id'] ; ?>&submitson=delupdate&p=<?php echo $p; ?>" onclick="return confirm('确认踢除 “<?php echo $nickname; ?>”吗？')" ><img src="../images/del.png" style="display:inline" width="18px" height="20px"></a>
                <?php }?></td></tr>
                <tr><td height="30" align="center" valign="top" style="font-size:11px;color:#999"><?php echo $rows['addtime']; ?></td></tr>
            </table>
        </td>
<?php if ($j % 3 == 0) {?>
</tr>
<tr>
<?php	} ?>
<?php } ?>
</tr>
</table>
<div class="page" style="margin-bottom:0"><?php echo $pagelist; ?></div>
<div class="tipsbox">
<div class="tipst">温馨提示：</div>
<div class="tipsc">黄框代表别人申请加入你圈子的新成员，正等待你的验证通过。</div>
</div>
<?php } else {?>
<br /><br />
<font color="#999999">...暂无会员...</font><br />
<br /><br /><br />
<?php }?>
<?php }?>
</div>
    
<!-- 圈子成员结束 -->
<!-- 圈子主图 -->
<?php } elseif ($submitok == 'group_mainphoto'){
	
	

	if (empty($picurl_s)){
		$picurl_b_url = '<i class="ico">&#xe620;</i>';
	}else{
		$picurl_s_url = $_ZEAI['up2'].'/'.$picurl_s;
		$picurl_b_url = getpath_b($picurl_s_url);
		$picurl_b_url = '<img src="'.$picurl_b_url.'">';
	}
	?>
<link href="<?php echo HOST; ?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css" />
	<style>
    .icoadd{width:100px;height:100px;display:block;margin:20px auto}
	.icoadd img{width:100px;height:100px;display:block}
    .icoadd{line-height:100px;border:#dedede 1px solid;font-size:70px;text-align:center;color:#aaa}
	.icoadd i.ico{display:inline-block;color:#aaa}
    </style>
    
    <div class="group_mainphoto">
        <div class="icoadd" id="jubaopic"><?php echo $picurl_b_url;?></div>
    </div>
        
    <script>
    var browser='<?php echo (is_weixin())?'wx':'h5';?>',upMaxMB = <?php echo $_UP['upMaxMB']; ?>,up2='<?php echo $_ZEAI['up2'];?>/';
    photoUp({
        btnobj:jubaopic,
        url:"group.php?mainid=<?php echo $mainid;?>",
        submitokBef:"ajax_picurl_",
        _:function(rs){
            zeai.msg(0);zeai.msg(rs.msg);
            if (rs.flag == 1){
                jubaopic.html('<img src='+up2+rs.dbname+'>');
               // picurl.value=rs.dbname;
            }
        }
    });
    </script>   
    
    
     
<!-- 圈子主图结束 -->
<?php }?>
</main>
<?php if($submitson=="photo") {?>
<script>
function picview(url) {
	wx.previewImage({
		current: url,
		urls: [<?php echo $pic_list; ?>]
	});
}
var ifphoto = true,mainid=<?php echo $mainid; ?>,clubid=<?php echo $clubid; ?>;
</script>
<script src="../group_party_photo.js"></script>
<script src="../win_confirm.js"></script>
<?php }else{?>
<script>
var ifphoto = false;
</script>
<?php }?>
<?php require_once '../bottom.php';?>
<script>
function clear2bx(sTxt) {
	//var c=sTxt.replace(/\r\n/ig,"");
	//c = c.replace(/\n/ig, "");
	//c = c.replace(/\r/ig, "");
	var c=sTxt;
	c = c.replace(/<script.*?>.*?<\/scrip[^>]*>/ig,"");
	c = c.replace(/<[^>]*?javascript:[^>]*>/ig,"");
	c = c.replace(/<style.*?>.*?<\/styl[^>]*>/ig,"");
	c = c.replace(/<(\w[^>]*) style="([^"]*)"([^>]*)/ig, "<$1$3");
	//c = c.replace(/<img.*?src=([^ |>]*)[^>]*>/ig,"<img src=user/$1>");
	c = c.replace(/<\/?(code|h\d)[^>]*>/ig,'<br>');
	c = c.replace(/<\/?(a|sohu|form|input|select|textarea|iframe|SUB|SUP|table|tr|th|td|tbody|module|OPTION|onload|div|center)(\s[^>]*)?>/ig,"");
	c = c.replace(/<\?xml[^>]*>/ig,'');
	c = c.replace(/<\!--.*?-->/ig,'');
	c = c.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onclick="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onclick=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onerror="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onload="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onmouseover="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<\\?\?xml[^>]*>/ig, "");
	c = c.replace(/<\/?\w+:[^>]*>/ig, "");
	c = c.replace(/<a.*?href="([^"]*)"[^>]*>/ig,"<a href=\"$1\">");
	//c = c.replace(/<center>\s*<center>/ig, '<center>');
	//c = c.replace(/<\/center>\s*<\/center>/ig, '</center>');
	//c = c.replace(/<center>/ig, '<center>');
	//c = c.replace(/<\/center>/ig, '</center>');
	//c=c.replace(/\'/g,"’");
	//c=c.replace(/\"/g,"”");
	//c=c.replace(/</g,"《").replace(/>/g,"》");
	sTxt = c;
	return sTxt;
}
</script>

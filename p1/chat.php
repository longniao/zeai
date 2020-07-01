<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
$chk_u_jumpurl=Href('u',$uid);
if(!ifint($cook_uid))json_exit(array('flag'=>'nologin','jumpurl'=>$chk_u_jumpurl));
if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'自己不能和自己发消息'));
if(!ifint($uid))json_exit(array('flag'=>0,'msg'=>'会员不存在'));
if(!in_array('chat',$navarr))json_exit(array('flag'=>0,'msg'=>'联天功能已关闭'));
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/udata.php';
//
$fields = "uname,nickname,sex,grade,photo_s,photo_f,birthday,heigh,areatitle,openid";
$row = $db->NAME($uid,$fields);
if ($row){
	$uname    = dataIO($row['uname'],'out');
	$nickname = dataIO($row['nickname'],'out');
	$sex      = $row['sex'];
	$grade    = $row['grade'];$Ugrade=$grade;
	$photo_s  = $row['photo_s'];
	$photo_f  = $row['photo_f'];
	$openid   = $row['openid'];
	$areatitle= dataIO($row['areatitle'],'out');
	$heigh    = (!empty($row['heigh']))?$row['heigh'].'cm':'';
	$birthday = $row['birthday'];
	$birthday = getage($birthday);$birthday=($birthday>=18)?$birthday.'岁':'';
	$nickname = (empty($nickname))?$uname:$nickname;
	$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
	$photo_s_str = (!empty($photo_s) && $photo_f==1)?"<img src='".$_ZEAI['up2']."/".$photo_s."'>":"<img src='".HOST."/res/photo_m".$sex.".png' class='sexbg".$sex."'>";
	$sex_str = ($sex == 1)?'他':'她';

}else{json_exit(array('flag'=>0,'msg'=>'会员不存在或已被锁定'));}
$row = $db->NAME($cook_uid,"sex,grade,photo_s,photo_f,nickname,RZ,myinfobfb,photo_f,sex");
$cook_sex      = $row['sex'];
$cook_grade    = $row['grade'];
$cook_photo_s  = $row['photo_s'];
$cook_photo_f  = $row['photo_f'];
$cook_nickname = dataIO($row['nickname'],'out');
$cook_RZ = $row['RZ'];$cook_RZarr = explode(',',$cook_RZ);
$cook_myinfobfb = intval($row['myinfobfb']);
$cook_sex       = intval($row['sex']);
$cook_photo_f   = intval($row['photo_f']);
//if($cook_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能聊天＾_＾'));
//聊天/查看联系方式
$chatContact_data = explode(',',$_VIP['chatContact_data']);
if(count($chatContact_data)>0 && is_array($chatContact_data)){
	foreach ($chatContact_data as $V){
		switch ($V) {
			case 'rz_mob':if(!in_array('mob',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('mob','title').'】<br>＾_＾'));break;
			case 'rz_identity':if(!in_array('identity',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％'));break;
			case 'rz_photo':if(!in_array('photo',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％'));break;
			case 'bfb':$config_bfb = intval($_VIP['chatContact_bfb_num']);if($cook_myinfobfb < $config_bfb)json_exit(array('flag'=>'nodata','msg'=>'请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％'));break;
			case 'sex':if($sex==$cook_sex)json_exit(array('flag'=>0,'msg'=>'同性不能查看＾_＾'));break;
			case 'photo':if($cook_photo_f!=1)json_exit(array('flag'=>'nophoto','msg'=>'请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％'));break;
		}
	}
}
if($submitok=='ajax_ifchat'){
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','jumpurl'=>$chk_u_jumpurl));
	//检查拉黑
	if (gzflag($uid,$cook_uid) == -1)json_exit(array('flag'=>0,'msg'=>'你已将对方拉黑了,还聊天？'));
	if (gzflag($cook_uid,$uid) == -1)json_exit(array('flag'=>0,'msg'=>'对方觉得你不太适合Ta，请求失败'));
	//
	//nolevel($uid,$cook_uid,'chat',$chk_u_jumpurl);
	json_exit(array('flag'=>1,'msg'=>'成功'));
}elseif($submitok == 'ajax_lockopen'){
	nolevel($uid,$cook_uid,'chat',$chk_u_jumpurl);
	noucount_clickloveb($uid,$cook_uid,'chat');
	json_exit(array('flag'=>1,'msg'=>'解锁成功'));
}elseif($submitok == 'ajax_clickloveb'){
	$row = $db->NAME($cook_uid,"loveb,grade");
	if ($row){
		$data_loveb = $row['loveb'];
		$data_grade = $row['grade'];
	}else{json_exit(array('flag'=>'nologin','jumpurl'=>$chk_u_jumpurl));}
	
	$ARR = json_decode($_VIP[$kind.'_loveb'],true);
	$my_clickloveb = $ARR[$data_grade];
	if ($my_clickloveb>0){
		$total = $db->COUNT(__TBL_UCOUNT__,"FIND_IN_SET($uid,listed) AND kind='".$kind."' AND uid=".$cook_uid);
		//新人
		if($total==0){
			if ($data_loveb<$my_clickloveb)json_exit(array('flag'=>'noloveb','jumpurl'=>$chk_u_jumpurl,'title'=>'余额不足','msg'=>'您的'.$_ZEAI['loveB'].'账户余额不足'.$my_clickloveb.'个'));
			$today = YmdHis(ADDTIME,'Ymd');
			//如果今天有记录
			$row = $db->ROW(__TBL_UCOUNT__,"id,listed","kind='".$kind."' AND date='".$today."' AND uid=".$cook_uid,"num");
			if ($row){
				$ctid = $row[0];$listed = explode(',',$row[1]);
				$ARR2 = json_decode($_VIP[$kind.'_daylooknum'],true);
				$Mymaxnum = $ARR2[$data_grade];
				//当前个数和Mymaxnum比
				if(count($listed)>=$Mymaxnum){
					json_exit(array('flag'=>0,'msg'=>'今天解锁人数已达上限'.$Mymaxnum));
				}else{
					$listed[] = $uid;
					$newlist  = implode(",",$listed);
					$db->query("UPDATE ".__TBL_UCOUNT__." SET listed='".$newlist."' WHERE id=".$ctid);
				}
			}else{
				$db->query("INSERT INTO ".__TBL_UCOUNT__."(uid,listed,kind,date) VALUES ($cook_uid,$uid,'$kind','$today')");
			}
			//查看按次扣费loveb库操作
			$endnum = $data_loveb-$my_clickloveb;
			$db->query("UPDATE ".__TBL_USER__." SET loveb=$endnum WHERE id=".$cook_uid);
			//爱豆清单入库
			$strdb=($kind=='contact')?'联系方式':'聊天看信';
			if($kind=='contact'){
				$strdb='联系方式';
				$dbkind=5;
			}elseif($kind=='chat'){
				$strdb='聊天看信';
				$dbkind=4;
			}
			$db->AddLovebRmbList($cook_uid,'解锁'.$strdb.'uid:'.$uid,-$my_clickloveb,'loveb',$dbkind);		
		}
	}
	json_exit(array('flag'=>1,'msg'=>'单次解锁成功'));
}elseif($submitok == 'ajax_uinfo'){
	?>
    <div class="defuinfo">
        <a href="<?php echo $chk_u_jumpurl;?>" target="_blank">
        <p><?php echo $photo_s_str;?></p>
        <span><h4><?php echo uicon($sex.$grade).$nickname;?></h4><?php echo $birthday.' '.$heigh.' '.$areatitle;?></span>
        </a>
    </div>
    <h2><?php echo $sex_str;?>的相册</h2>
    <div class="defphoto" id="libox">
		<?php
		$rt=$db->query("SELECT path_s FROM ".__TBL_PHOTO__." WHERE uid=".$uid." AND flag=1 ORDER BY id DESC LIMIT 12");
		$photo_total = $db->num_rows($rt);
		if ($photo_total>0) {
			for($i=0;$i<$photo_total;$i++) {
				$rows = $db->fetch_array($rt,'num');
				if(!$rows) break;
				$path_s = $rows[0];
				$dst_s  = $_ZEAI['up2']."/".$path_s;
				?>
				<li onClick="parent.ZeaiPC.piczoom('<?php echo getpath_smb($dst_s,'b');?>')"><img src="<?php echo $dst_s;?>"></li>
				<?php
			}
		}else{echo '<div class="nophoto"><i class="ico">&#xe61f;</i>还没有上传相册</em>';}
		?>
    </div>
	<?php
exit;}elseif($submitok == 'ajax_time'){
	echo '<div class="timeulist">';
	$sql_ = " (uid=".$cook_uid." OR senduid=".$cook_uid.") AND ifdel<>".$cook_uid;
	$SQL = "SELECT id,uid,senduid,senduid AS senduid2,t,content,addtime FROM ".__TBL_MSG__." A,(SELECT MAX(id) AS max_id FROM ".__TBL_MSG__." WHERE ".$sql_." GROUP BY senduid) B WHERE A.id=B.max_id AND ".$sql_." ORDER BY A.id DESC";
	$rt=$db->query($SQL);
	while($tmprows = $db->fetch_array($rt,'name')){if($tmprows['senduid'] == $cook_uid){$tmprows['senduid'] = $tmprows['uid'];}$arr[]=$tmprows;}
	if($arr){
		$list = msg_reset($arr);
		foreach ($list as $rows) {
			$senduid = $rows['senduid'];
			$senduid2 = $rows['senduid2'];
			$t       = $rows['t'];
			$content = dataIO($rows['content'],'out');
			$addtime_str = date_str($rows['addtime']);
			//主表信息
			$row = $db->NUM($senduid,"sex,grade,nickname,photo_s,photo_f,birthday,areatitle,heigh,uname");
			$sex      = $row[0];
			$grade    = $row[1];
			$nickname = dataIO($row[2],'out');
			$photo_s  = $row[3];
			$photo_f  = $row[4];
			$birthday  = $row[5];
			$areatitle = $row[6];
			$heigh     = $row[7];
			$uname     = dataIO($row[8],'out');
			$birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁';
			$heigh_str     = (empty($heigh))?'':' '.$heigh.'cm';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1];
			$areatitle_str = (empty($areatitle))?'':' '.$areatitle;
			$nickname = (empty($nickname))?$uname:$nickname;
			//
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
			$imgbdr      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			//列表红点
			$inum        = $db->COUNT(__TBL_MSG__," new=1 AND ifdel=0 AND senduid=".$senduid." AND uid=".$cook_uid);
			$inum_str    = ($inum>0)?'<i class="new">'.$inum.'</i>':'';
			//锁
			$MsgFlag = lockU($senduid);
			if ($MsgFlag){
				if($kind == 2){$content = '[语音]';}elseif(strstr($content,"[/img]")){$content = preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=".HOST.'/res/bq/'."\\1.gif>",$content);}
			}else{
				if($senduid2==$cook_uid){
					if($kind == 2){$content = '[语音]';}elseif(strstr($content,"[/img]")){$content = preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=".HOST.'/res/bq/'."\\1.gif>",$content);}
				}else{
					$content = $birthday_str.$heigh_str.' '.$areatitle_str;
				}
			}
		?>
		<dl onClick="zeai.openurl('chat.php?uid=<?php echo $senduid;?>')">
			<dt><img src="<?php echo $photo_s_url; ?>"<?php echo $imgbdr; ?>></dt>
			<dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $content; ?></h6></dd>
			<span><?php echo $addtime_str; ?></span><?php echo $inum_str; ?>
		</dl>
	<?php }}else{echo $nodatatips;}
	echo '</div>';
exit;}elseif($submitok == 'ajax_gz'){
	echo '<div class="timeulist">';
	$rt=$db->query("SELECT a.id,a.uid,a.px,b.uname,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.heigh FROM ".__TBL_GZ__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.senduid=".$cook_uid." AND a.flag=1 ORDER BY a.px DESC");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows)break;
			$uid2     = $rows['uid'];
			$addtime_str = date_str($rows['px']);
			$sex      = $rows['sex'];
			$grade    = $rows['grade'];
			$nickname = dataIO($rows['nickname'],'out');
			$photo_s  = $rows['photo_s'];
			$photo_f  = $rows['photo_f'];
			$birthday  = $rows['birthday'];
			$areatitle = $rows['areatitle'];
			$heigh     = $rows['heigh'];
			$uname     = dataIO($rows['uname'],'out');
			$birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁';
			$heigh_str     = (empty($heigh))?'':' '.$heigh.'cm';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1];
			$areatitle_str = (empty($areatitle))?'':' '.$areatitle;
			$nickname = (empty($nickname))?$uname:$nickname;
			//
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
			$imgbdr      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			$content = $birthday_str.$heigh_str.' '.$areatitle_str;
		?>
		<dl onClick="zeai.openurl('chat.php?uid=<?php echo $uid2;?>')">
			<dt><img src="<?php echo $photo_s_url; ?>"<?php echo $imgbdr; ?>></dt>
			<dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $content; ?></h6></dd>
			<span><?php echo $addtime_str; ?></span>
		</dl>
	<?php }}else{echo $nodatatips;}
	echo '</div>';
	exit;
}
$cook_tipnum   = $db->COUNT(__TBL_MSG__,"new=1 AND ifdel=0 AND uid=".$cook_uid);
$chat_duifangfree = json_decode($_VIP['chat_duifangfree'],true);
$ifchatlock = lockU($uid);//是否已解锁

/******************************************/
$cook_photo_s_str = (!empty($cook_photo_s) && $cook_photo_f==1)?"<img src='".$_ZEAI['up2']."/".$cook_photo_s."'>":"<img src='".HOST."/res/photo_m".$cook_sex.".png' class='sexbg".$cook_sex."'>";

//$ifchatlock = $db->COUNT(__TBL_MSG__," new=1 AND ifdel=0 AND senduid=".$cook_uid." AND uid=".$uid);//红点
switch ($submitok) {
	case 'ajax_add':
		if (!empty($content)){
			$content = dataIO($content,'in');
			if (gzflag($cook_uid,$uid) == -1){$db->query("INSERT INTO ".__TBL_MSG__." (uid,senduid,content,addtime,ifdel) VALUES ($uid,$cook_uid,'$content',".ADDTIME.",1)");exit;}
			//
			$sendwx = false;
			$SQL = " WHERE (uid=".$uid." AND senduid=".$cook_uid.") OR (senduid=".$uid." AND uid=".$cook_uid." AND ifdel=0) ";
			$rtt = $db->query("SELECT addtime FROM ".__TBL_MSG__.$SQL." ORDER BY id DESC LIMIT 1");
			if ($db->num_rows($rtt)){
				$roww = $db->fetch_array($rtt,'num');
				$endtime = $roww[0];
				$difftime = ADDTIME - $endtime;
				if ($difftime > 60 ){
					$sendwx = true;
				}
			}else{$sendwx = true;}
			if ($sendwx){
				$nickname = urlencode($cook_nickname);
				//$url = $_ZEAI['m_2domain']."/msg";
				//$url = (empty($cook_uid))?$_ZEAI['m_2domain']."/my/msg.php":$_ZEAI['m_2domain']."/msg/show.php?uid=".$cook_uid;
				
				$content2=(!lockU($cook_uid))?'聊天内容进入查看':$content;
				
				$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人给你留言");
				$remark = urlencode("点击进入查看");
				$mbcontent = (strstr($content,"[/img]"))?'[表情]':$content2;
				
				@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$mbcontent.'&nickname='.$nickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('my_chat')));
			}
			$db->query("INSERT INTO ".__TBL_MSG__." (uid,senduid,content,addtime) VALUES ($uid,$cook_uid,'$content',".ADDTIME.")");
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum+1 WHERE id=".$uid);
		}
		json_exit(array('flag'=>1));
	break;
	case 'ajax_chat_audio':
		if (str_len($sid) > 20){
			require_once ZEAI.'sub/zeai_up_func.php';
			$dbname   = setVideoDBname('v',$cook_uid,'amr');
			$file     = get_wx_datastream($sid);
			$difftime = ceil($difftime/1000);
			if (up_send_stream($file,$dbname,'www','zeai','cn','SupDes','audio')){
				//$content = dataIO($content,'in');
				$_s      = substr($dbname,0,str_len($dbname)-4);
				$content = $sid.'|'.$_s.'|'.$difftime.'|'.ADDTIME;
				if (gzflag($cook_uid,$uid) == -1){$db->query("INSERT INTO ".__TBL_MSG__." (uid,senduid,t,content,addtime,ifdel) VALUES ($uid,$cook_uid,2,'$content',".ADDTIME.",1)");exit;}
				//
				$db->query("INSERT INTO ".__TBL_MSG__." (uid,senduid,t,content,addtime) VALUES ($uid,$cook_uid,2,'$content',".ADDTIME.")");
				$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum+1 WHERE id=".$uid);
				//
				$SQL = " WHERE (uid=".$uid." AND senduid=".$cook_uid.") OR (senduid=".$uid." AND uid=".$cook_uid." AND ifdel=0) ";
				$rtt = $db->query("SELECT addtime FROM ".__TBL_MSG__.$SQL." ORDER BY id DESC LIMIT 1");
				if ($db->num_rows($rtt)){
					$roww = $db->fetch_array($rtt,'num');
					$endtime = $roww[0];
					$difftime = ADDTIME - $endtime;
					if ($difftime > 60 ){
						$content2 = '语音消息';
						$nickname = urlencode($cook_nickname);
						$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人给你留言");
						$remark = urlencode("点击进入查看");
						$content2 = urlencode($content2);
						@wx_mb_sent('mbbh=OPENTM202119578&openid='.$openid.'&content='.$content2.'&nickname='.$nickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('my_chat')));
					}
				}
				//
				echo $content;ob_end_flush();exit;
			}
		}
		exit;
	break;
	case 'ajax_getmess':
		$C = get_mess_list($uid);
		echo $C;exit;
	break;
	case 'ajax_chk_flag':
		$rtD=$db->query("SELECT id FROM ".__TBL_MSG__." WHERE new=1 AND uid=".$cook_uid." AND senduid=".$uid." LIMIT 1");
		if ($db->num_rows($rtD)){
			$flag = 1;
		}else{$flag = 0;}
		echo $flag;exit;
	break;
	case 'ajax_getmess_one':
		$C = get_mess_list_one($uid);
		echo $C;exit;
	break;
	case 'ajax_getMsgMore':
		echo ajax_getMsgMore($uid,$p);exit;
	break;
}

function get_mess_list_one($uid){ 
	global $db,$cook_uid,$photo_s_str,$cook_photo_s_str,$ifchatlock,$chat_duifangfree,$Ugrade;$C = "";
	$SQL = " WHERE (senduid=".$uid." AND uid=".$cook_uid.") AND new=1 AND ifdel=0";
	$rt=$db->query("SELECT t,content,addtime FROM ".__TBL_MSG__.$SQL." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if ($total > 0){
		while($tmprows = $db->fetch_array($rt,'num')){
			if(!$ifchatlock && $chat_duifangfree[$Ugrade]!=1){
				$tmprows[1] = '<span class=\'lock\' onClick=\'javascript:lockopen('.$uid.')\' title=\'点击解锁\'>此条信息已隐藏<br>您与TA的聊天通道还没打开，<a><i class=\'ico\'>&#xe61e;</i>解锁</a></span>';
			}
			$arr[]=$tmprows;
		}
		if($ifchatlock){
			$arr = array_reverse($arr);
			$db->query("UPDATE ".__TBL_MSG__." SET new=0 WHERE senduid=".$uid." AND uid=".$cook_uid." AND new=1 AND ifdel=0");
			$endnum = ($data_tipnum >= $total)?($data_tipnum - $total):0;
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=$endnum WHERE id=".$cook_uid);
			$C=encode_json($arr);
		}else{
			$C='';
		}
	}
	return $C;
}
//初次载入
function get_mess_list($uid){ 
	global $db,$cook_uid,$photo_s_str,$cook_photo_s_str,$data_tipnum,$ifchatlock,$chat_duifangfree,$Ugrade;$C = "";
	$SQL      = " (uid=".$uid." AND senduid=".$cook_uid.") OR (senduid=".$uid." AND uid=".$cook_uid.") ";
	$totalnum = $db->COUNT(__TBL_MSG__,$SQL);
	$ifmore   = ($totalnum > 10)?1:0;
	$rt=$db->query("SELECT senduid,t,content,addtime,ifdel,uid FROM ".__TBL_MSG__." WHERE ".$SQL." ORDER BY id DESC LIMIT 10");
	$total     = $db->num_rows($rt);
	if ($total == 0) {
		$C = '<div class="firsttips">聊天涉及钱财问题，请保持警惕，避免上当受骗。<br>若发现可疑行为，请及时举报。</div>';//$C = "<div class='nodatatipsS'>..暂无信息..</div>";
	} else {
		while($tmprows = $db->fetch_array($rt,'num')){$arr[]=$tmprows;}
		$arr = array_reverse($arr);
		foreach ($arr as $rows) {
			$senduid  = $rows[0];
			$t         = $rows[1];
			$content   = dataIO($rows[2],'out');
			$addtime   = $rows[3];
			$ifdel     = $rows[4];/////////////////
			$uid____   = $rows[5];
			if ($senduid == $cook_uid){
				$ifmy = 1;$Uphoto = $cook_photo_s_str;
			}else{
				$ifmy = 0;$Uphoto = $photo_s_str;
				if(!$ifchatlock && $chat_duifangfree[$Ugrade]!=1){
					$content = '<span class="lock" onClick="javascript:lockopen('.$senduid.')" title="点击解锁">此条信息已隐藏<br>您与TA的聊天通道还没打开，<a><i class="ico">&#xe61e;</i>解锁</a></span>';
				}
			}
			///////////
			if ($ifdel == $cook_uid)continue;
			//////////
			$difftime  = $addtime - $lasttime;
			if ($difftime > 60)$C .= "<span class='time'>".date("Y/m/d H:i",$addtime)."</span>";
			$ifmy = ($ifmy == 1)?" class='my'":"";
			switch ($t) {
				case 1:break;
				case 2:
					$a        = explode('|',$content);
					$sid      = $a[0];
					$path_s   = $_ZEAI['up2'].'/'.$a[1] .'.mp3';
					$difftime = $a[2];
					$content  = "<em voiceId=\"".$sid."\" src=\"".$path_s."\" t=\"".$addtime."\"><div class=\"voiceIcon\"></div><div class=\"voiceSec\">".$difftime."\"</div></em>";				
				break;
			}
			$C .= "<dl".$ifmy."><dt>".$Uphoto."</dt><dd>".$content."</dd></dl>";
			$lasttime = $addtime;
		}
		$newnum = $db->COUNT(__TBL_MSG__,"new=1 AND senduid=".$uid." AND uid=".$cook_uid." AND ifdel=0");
		if ($newnum > 0 && ($ifchatlock || $chat_duifangfree[$Ugrade]==1)){
			$db->query("UPDATE ".__TBL_MSG__." SET new=0 WHERE new=1 AND senduid=".$uid." AND uid=".$cook_uid." AND ifdel=0");
 			$endnum = ($data_tipnum >= $newnum)?($data_tipnum - $newnum):0;
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=$endnum WHERE id=".$cook_uid);
		}
	}
	return $C."|GYL-SUPDES|".$ifmore;
}
function ajax_getMsgMore($uid,$p){ 
	global $db,$cook_uid,$photo_s_str,$cook_photo_s_str,$ifchatlock,$chat_duifangfree,$Ugrade;
	$pagesize = 10;$p = intval($p);$C = "";
	$SQL = " (uid=".$uid." AND senduid=".$cook_uid.") OR (senduid=".$uid." AND uid=".$cook_uid." AND ifdel=0) ";
	$total = $db->COUNT(__TBL_MSG__,$SQL);
	$totalpage = ceil($total/$pagesize);
	$LIMIT     = " LIMIT ".($p*$pagesize).",".$pagesize;
	$ifmore    = ($p < $totalpage)?1:0;
	if ($p <= $totalpage){
		$rt=$db->query("SELECT senduid,t,content,addtime FROM ".__TBL_MSG__." WHERE ".$SQL." ORDER BY id DESC".$LIMIT);
		$total = $db->num_rows($rt);
		if ($total > 0) {
			while($tmprows = $db->fetch_array($rt,'num')){$arr[]=$tmprows;}
			$arr = array_reverse($arr);
			foreach ($arr as $rows) {
				$senduid  = $rows[0];
				$t         = $rows[1];
				$content   = $rows[2];
				$addtime   = $rows[3];
				$difftime  = $addtime - $lasttime;
				if ($difftime > 60)$C .= "<span class='time'>".date("Y/m/d H:i",$addtime)."</span>";
				if ($senduid == $cook_uid){
					$ifmy = 1;$Uphoto = $cook_photo_s_str;
				}else{
					$ifmy = 0;$Uphoto = $photo_s_str;
					if(!$ifchatlock && $chat_duifangfree[$Ugrade]!=1){
						$content = '<span class="lock" onClick="javascript:lockopen('.$senduid.')" title="点击解锁">此条信息已隐藏<br>您与TA的聊天通道还没打开，<a><i class="ico">&#xe61e;</i>解锁</a></span>';
					}
				}
				$ifmy = ($ifmy == 1)?" class='my'":"";
				switch ($t) {
					case 1:break;
					case 2:
						$a        = explode('|',$content);
						$sid      = $a[0];
						$path_s   = $_ZEAI['up2'].'/'.$a[1] .'.mp3';
						$difftime = $a[2];
						$content  = "<em voiceId=\"".$sid."\" src=\"".$path_s."\" t=\"".$addtime."\"><div class=\"voiceIcon\"></div><div class=\"voiceSec\">".$difftime."\"</div></em>";				
					break;
				}
				$C .= "<dl".$ifmy."><dt>".$Uphoto."</dt><dd>".$content."</dd></dl>";
				$lasttime = $addtime;
			}
		}
	}
	return $C."|GYL-SUPDES|".$ifmore;
}




/***************************************/


?>
<!doctype html><html><head><meta charset="utf-8">
<title></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/chat.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
</head>
<body scroll="no"><!-- ondragstart="window.event.returnValue=false" oncontextmenu="window.event.returnValue=false"-->
<div class="chatbox" id="chatbox">
	<div class="L">
		<div class="Ltop tabmenu" id="my_chat_lnav">
			<li id="btn_chat" title="当前聊天" data="chat.php?submitok=ajax_uinfo&uid=<?php echo $uid;?>" class="ed"><span><i class="ico">&#xe64a;</i></span></li>
			<li id="btn_time" title="最近联系" data="chat.php?submitok=ajax_time&uid=<?php echo $uid;?>"><span><i class="ico">&#xe634;</i><font><?php echo $cook_tipnum;?></font></span></li>
			<li id="btn_gz" title="我关注的人" data="chat.php?submitok=ajax_gz&uid=<?php echo $uid;?>"><span><i class="ico">&#xe64d;</i></span></li>
			<b></b>
		</div>
        <div class="Luinfo" id="Luinfo"></div>
    </div>
	<div class="R">
    	<div class="Rtop">
            <?php if (!$ifchatlock){?>
            	<?php if ($chat_duifangfree[$Ugrade]==1){?>
					<span>对方是【<?php echo utitle($Ugrade);?>】，你与Ta的聊天通信不受任何限制！</span>
                <?php }else{ ?>
					<span>聊天解锁之后，双方通信不受任何限制哦！</span> <b onClick="lockopen(<?php echo $uid;?>)">点击解锁</b>
                <?php }?>
            <?php }else{?>
            <span>您已解锁，聊天通信不受任何限制！</span>
            <?php }?>
        	<?php if(@in_array('gift',$navarr)){?><span class="giftbtn hishan" id="giftbtn" title="送Ta礼物"><i class="ico">&#xe69a;</i></span><?php }?>
        </div>
    	<div class="Rmsg" id="msg"></div>
        <div class="Rwrite" id="Rwrite">
        	<span class="bqbtn" id="bqbtn" title="选择表情"><i class="ico">&#xe6e2;</i></span>
        	<div name="content" class="content" id="content" onclick="iput();" contentEditable="true" oninput="iput();" tabindex="0" hidefocus="true" onblur="onblurFN();"></div>
            <a href="javascript:;" class="sendbtn" id="sendbtn" onclick="<?php if (!$ifchatlock){echo 'lockopen';}else{echo 'msg_send';}?>(<?php echo $uid; ?>);">发送</a>
            <div id="bq">
                <div id="bqlist">
                <img src="<?php echo HOST;?>/res/bq/1.gif">
                <img src="<?php echo HOST;?>/res/bq/2.gif">
                <img src="<?php echo HOST;?>/res/bq/3.gif">
                <img src="<?php echo HOST;?>/res/bq/4.gif">
                <img src="<?php echo HOST;?>/res/bq/5.gif">
                <img src="<?php echo HOST;?>/res/bq/6.gif">
                <img src="<?php echo HOST;?>/res/bq/7.gif">
                <img src="<?php echo HOST;?>/res/bq/8.gif">
                <img src="<?php echo HOST;?>/res/bq/9.gif">
                <img src="<?php echo HOST;?>/res/bq/10.gif">
                <img src="<?php echo HOST;?>/res/bq/11.gif">
                <img src="<?php echo HOST;?>/res/bq/12.gif">
                <img src="<?php echo HOST;?>/res/bq/13.gif">
                <img src="<?php echo HOST;?>/res/bq/14.gif">
                <img src="<?php echo HOST;?>/res/bq/15.gif">
                <img src="<?php echo HOST;?>/res/bq/16.gif">
                <img src="<?php echo HOST;?>/res/bq/17.gif">
                <img src="<?php echo HOST;?>/res/bq/18.gif">
                <img src="<?php echo HOST;?>/res/bq/19.gif">
                <img src="<?php echo HOST;?>/res/bq/20.gif">
                <img src="<?php echo HOST;?>/res/bq/21.gif">
                <img src="<?php echo HOST;?>/res/bq/22.gif">
                <img src="<?php echo HOST;?>/res/bq/23.gif">
                <img src="<?php echo HOST;?>/res/bq/24.gif">
                <img src="<?php echo HOST;?>/res/bq/25.gif">
                <img src="<?php echo HOST;?>/res/bq/27.gif">
                <img src="<?php echo HOST;?>/res/bq/28.gif">
                <img src="<?php echo HOST;?>/res/bq/29.gif">
                <img src="<?php echo HOST;?>/res/bq/30.gif">
                <img src="<?php echo HOST;?>/res/bq/31.gif">
                <img src="<?php echo HOST;?>/res/bq/32.gif">
                <img src="<?php echo HOST;?>/res/bq/33.gif">
                <img src="<?php echo HOST;?>/res/bq/34.gif">
                <img src="<?php echo HOST;?>/res/bq/35.gif">
                <img src="<?php echo HOST;?>/res/bq/36.gif">
                <img src="<?php echo HOST;?>/res/bq/37.gif">
                <img src="<?php echo HOST;?>/res/bq/38.gif">
                <img src="<?php echo HOST;?>/res/bq/39.gif">
                <img src="<?php echo HOST;?>/res/bq/40.gif">
                <img src="<?php echo HOST;?>/res/bq/41.gif">
                <img src="<?php echo HOST;?>/res/bq/42.gif">
                <img src="<?php echo HOST;?>/res/bq/43.gif">
                <img src="<?php echo HOST;?>/res/bq/44.gif">
                <img src="<?php echo HOST;?>/res/bq/45.gif">
                <img src="<?php echo HOST;?>/res/bq/46.gif">
                <img src="<?php echo HOST;?>/res/bq/47.gif">
                <img src="<?php echo HOST;?>/res/bq/48.gif">
                <img src="<?php echo HOST;?>/res/bq/49.gif">
                <img src="<?php echo HOST;?>/res/bq/50.gif">
                <img src="<?php echo HOST;?>/res/bq/51.gif">
                <img src="<?php echo HOST;?>/res/bq/52.gif">
                </div>
            </div>            
        </div>
    </div>
</div>

    <div class="top"><div class='title'><a href="javascript:;" id="loadmore">查看历史消息</a></div></div>
    <input type="hidden" id="ifopen" value="0">
    <input type="hidden" id="p" value="1">
    <input type="hidden" id="starttime" value="0">
	<?php
    //$urole = json_decode($_ZEAI['urole']);
	$urolenew = json_decode($_ZEAI['urole'],true);
	$newarr=array();foreach($urolenew as $RV){if($RV['f']==1){$newarr[]=$RV;}else{continue;}}
	$newarr=encode_json($newarr);
	$urole = json_decode($newarr);
	
    $chat_daylooknum = json_decode($_VIP['chat_daylooknum']);
    $chat_loveb      = json_decode($_VIP['chat_loveb']);
    ?>
    <div id="chat_daylooknumHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_daylooknum->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> 人/天':' 无权聊天';
            $ifmy = ($cook_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
            $outA .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outA;
        ?>
        </ul>
        <button type="button" class="W100_ btn size3 HUANG Mcenter block center chatlock" onClick="zeai.openurl_('<?php echo HOST;?>/p1/my_vip.php?jumpurl='+encodeURIComponent(jumpurl))"><i class="ico vipbtn" style="color:#fff;font-size:18px;margin-right:4px">&#xe6ab;</i>我要升级会员</button>
    </div>
    
    <div id="chat_lovebHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_loveb->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> '.$_ZEAI['loveB'].'/人':' 免费聊天';
            if($cook_grade==$grade){
                $ifmy = '　<font class="Cf00">（我）</font>';
                $myclkB=$num;
            }else{
                $ifmy = '';
            }
            $outI .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outI;
        ?>
        </ul>
        <a class="btn size3 HONG W50_ chatlock" onClick="clickloveb(uid,'chat')">单次<?php echo $myclkB;?>解锁</a>
        <a class="btn size3 HUANG W50_ chatlock" onClick="zeai.openurl_('<?php echo HOST;?>/p1/my_vip.php?jumpurl='+encodeURIComponent(jumpurl))">升级会员</a>
    </div>
    
    <div id="chat_levelHelp" class="helpDiv levelHelp">
    	<i class="ico sorry">&#xe61f;</i><br>
    	您只能互动会员级别比你低的或同级会员<br>您当前【<?php echo utitle($cook_grade);?>】<br>请至少升级到【<?php echo utitle($Ugrade);?>】<br><br>
        <button type="button" class="W100_ btn size3 HUANG Mcenter block center chatlock" onClick="zeai.openurl_('<?php echo HOST;?>/p1/my_vip.php?jumpurl='+encodeURIComponent(jumpurl))"><i class="ico vipbtn">&#xe6ab;</i><span>立即升级</span></button>
    </div>
    <div id="box_gift" class="box_gift">
        <em><img><h3></h3><h6></h6></em>
        <a href="javascript:;">看看其他礼物</a>
        <a href="javascript:;">确认赠送</a>
    </div>
    <div id='tips0_100_0' class='tips0_100_0 alpha0_100_0'></div>
<script src="../res/jquery-1.7.2.min.js"></script>
<script src="js/chat.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var cook_uid= '<?php echo $cook_uid; ?>',
cook_photo_s     = '<?php echo $cook_photo_s; ?>',
uid              = '<?php echo $uid; ?>',
photo_s_str      = "<?php echo $photo_s_str ; ?>",
cook_photo_s_str = "<?php echo $cook_photo_s_str ; ?>",
up2       = "<?php echo $_ZEAI['up2']; ?>",
nickname = '<?php echo trimhtml($nickname); ?>',
lovebstr = '<?php echo $_ZEAI['loveB']; ?>',
jumpurl  = '<?php echo $chk_u_jumpurl;?>';
var ifchat=<?php echo (!$ifchatlock)?0:1;?>
//
$(function(){msgAudioLiPlay();});
o('bqbtn').onclick = bqbtnFn;
o('msg').onclick = msgFn;
o('loadmore').onclick = function(){ajax_getMsgMore(uid);}
domm();
ajax_getmess(uid);
var chatdsq= setInterval("ajax_chk_flag(uid)",2000);
btm0     = setInterval("scrollTobtm()",200);
setTimeout("delbtm0()",1000);
cleandsj = chatdsq;
ZeaiPC.tabmenu.init({obj:my_chat_lnav,showbox:Luinfo,kind:'block'});
setTimeout(function(){btn_chat.click();},100);
if(!zeai.empty(giftbtn)){giftbtn.onclick = function(){gift_ajaxdata(0,box_gift,uid);}}
var widthBar = 17;
var root = document.documentElement;	
if (typeof window.innerWidth == 'number'){	widthBar = window.innerWidth - root.clientWidth;}
</script>
</body>
</html>
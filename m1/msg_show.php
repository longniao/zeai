<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');

if($submitok== 'ajax_get_audio_url'){
		if(empty($audio_url)){
			json_exit(array('flag'=>0,'msg'=>'获取文件参数失败'));
		}else{
			$audio_url=str_replace($_ZEAI['up2'],"",$audio_url);
			
			if(substr($audio_url,0,1)=="/"){
				$audio_url=substr($audio_url,1);
			}
			if(file_exists(ZEAI.'up/'.$audio_url)){
				json_exit(array('flag'=>1,'msg'=>'存在可用','url'=>$_ZEAI['up2']."/".$audio_url));
			}else{
				$audio_url=str_replace("v/","tmp/",$audio_url);
				$audio_url=str_replace(".mp3",".amr",$audio_url);
				if(file_exists(ZEAI.'up/'.$audio_url)){
					json_exit(array('flag'=>1,'msg'=>'存在可用','url'=>$_ZEAI['up2']."/".$audio_url));
				}else{
					json_exit(array('flag'=>0,'msg'=>'文件不存在'.$audio_url));
				}
			}			
		}
exit;	
}


$uid = (ifint($uid))?$uid:$a;$ii=$i;
if (!ifint($uid))json_exit(array('flag'=>0,'msg'=>'会员不存在或已被锁定'.$uid));
if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'自己不能和自己发消息'));
$currfields = "sex,photo_s,photo_f,tipnum,grade,RZ,myinfobfb";
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=u&a='.$uid;require_once ZEAI.'my_chk_u.php';
//检查拉黑
if (gzflag($uid,$cook_uid) == -1)json_exit(array('flag'=>0,'msg'=>'你已将对方拉黑了,还聊天？'));
if (gzflag($cook_uid,$uid) == -1)json_exit(array('flag'=>0,'msg'=>'对方觉得你不太适合Ta，请求失败'));
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'cache/config_vip.php';
//
$cook_photo_s = $row['photo_s'];
$cook_photo_f = $row['photo_f'];
$data_tipnum  = intval($row['tipnum']);
$data_msglist = $row['msglist'];
$data_sex = $row['sex'];
$data_grade = $row['grade'];
$cook_RZ = $row['RZ'];$cook_RZarr = explode(',',$cook_RZ);
$cook_myinfobfb = intval($row['myinfobfb']);
$row = $db->NUM($uid,"sex,photo_s,photo_f,nickname,openid,grade");
if ($row){
	$sex      = $row[0];
	$photo_s  = $row[1];
	$photo_f  = $row[2];
	$nickname = urldecode($row[3]);
	$openid   = $row[4];
	$grade    = $row[5];
}else{exit(JSON_ERROR);}
//聊天/查看联系方式
$chatContact_data = explode(',',$_VIP['chatContact_data']);
if(count($chatContact_data)>0 && is_array($chatContact_data)){
	foreach ($chatContact_data as $V){
		switch ($V) {
			case 'rz_mob':if(!in_array('mob',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('mob','title').'】<br>＾_＾'));break;
			case 'rz_identity':if(!in_array('identity',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％'));break;
			case 'rz_photo':if(!in_array('photo',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％'));break;
			case 'bfb':$config_bfb = intval($_VIP['chatContact_bfb_num']);if($cook_myinfobfb < $config_bfb)json_exit(array('flag'=>'nodata','msg'=>'请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％'));break;
			case 'sex':if($data_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能聊天＾_＾'));break;
			case 'photo':if($cook_photo_f!=1)json_exit(array('flag'=>'nophoto','msg'=>'请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％'));break;
		}
	}
}
//
$chat_duifangfree = json_decode($_VIP['chat_duifangfree'],true);
if($chat_duifangfree[$grade]!=1){
	nolevel($uid,$cook_uid,'chat',$chk_u_jumpurl);
	noucount_clickloveb($uid,$cook_uid,'chat');
}

$photo_s_str      = (!empty($photo_s) && $photo_f==1)?"<img src='".$_ZEAI['up2']."/".$photo_s."'>":"<img src='res/photo_m".$sex.".png' class='imgbdr".$sex."'>";
$cook_photo_s_str = (!empty($cook_photo_s) && $cook_photo_f==1)?"<img src='".$_ZEAI['up2']."/".$cook_photo_s."'>":"<img src='res/photo_m".$cook_sex.".png' class='imgbdr".$cook_sex."'>";

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
				$mbnickname = urlencode($cook_nickname.' UID:'.$cook_uid);
				$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人给你留言");
				$remark = urlencode("缘份就在一瞬间，赶快去看看~");
				$mbcontent = (strstr($content,"[/img]"))?'[表情]':$content;
				if($data_grade<2){
					$mbcontent='*************';
				}
				@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$mbcontent.'&nickname='.$mbnickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(HOST."/?z=msg&e=sx"));
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
						//$url = $_ZEAI['m_2domain']."/msg/show.php?uid=".$cook_uid;
						//$url = HOST."/msg";
						$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人给你留言");
						$remark = urlencode("点击进入查看");
						$content2 = urlencode($content2);
						@wx_mb_sent('mbbh=OPENTM202119578&openid='.$openid.'&content='.$content2.'&nickname='.$nickname.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
				}
				//
				echo $content;ob_end_flush();exit;
			}
		}
		exit;
	break;
	case 'ajax_app_audio':
		if ($difftime>0){
			require_once ZEAI.'sub/zeai_up_func.php';
			$dbname   = setVideoDBname('v',$cook_uid,'amr');
			$file=$_FILES['file'];			
			$difftime = ceil($difftime/1000);
			if (up_send($file,$dbname,'www','zeai','cn','SupDes','audio')){
				$_s      = substr($dbname,0,str_len($dbname)-4);
				$content = 'app|'.$_s.'|'.$difftime.'|'.ADDTIME;
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
						@wx_mb_sent('mbbh=OPENTM202119578&openid='.$openid.'&content='.$content2.'&nickname='.$nickname.'&first='.$first.'&remark='.$remark.'&url='.$url);
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
	global $db,$cook_uid,$photo_s_str,$cook_photo_s_str;$C = "";
	$SQL = " WHERE (senduid=".$uid." AND uid=".$cook_uid.") AND new=1 AND ifdel=0";
	$rt=$db->query("SELECT t,content,addtime FROM ".__TBL_MSG__.$SQL." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if ($total > 0){
		while($tmprows = $db->fetch_array($rt,'num')){$arr[]=$tmprows;}
		$arr = array_reverse($arr);
		$db->query("UPDATE ".__TBL_MSG__." SET new=0 WHERE senduid=".$uid." AND uid=".$cook_uid." AND new=1 AND ifdel=0");
		$endnum = ($data_tipnum >= $total)?($data_tipnum - $total):0;
		$db->query("UPDATE ".__TBL_USER__." SET tipnum=$endnum WHERE id=".$cook_uid);
		$C=encode_json($arr);
	}
	return $C;
}
//初次载入
function get_mess_list($uid){ 
	global $db,$cook_uid,$photo_s_str,$cook_photo_s_str,$data_tipnum;$C = "";
	$SQL      = " (uid=".$uid." AND senduid=".$cook_uid.") OR (senduid=".$uid." AND uid=".$cook_uid.") ";
	$totalnum = $db->COUNT(__TBL_MSG__,$SQL);
	$ifmore   = ($totalnum > 10)?1:0;
	$rt=$db->query("SELECT senduid,t,content,addtime,ifdel,uid FROM ".__TBL_MSG__." WHERE ".$SQL." ORDER BY id DESC LIMIT 10");
	$total     = $db->num_rows($rt);
	if ($total == 0) {
		$C = "";//$C = "<div class='nodatatipsS'>..暂无信息..</div>";
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
				//$href = '../u/umain.php?uid='.$cook_uid;
				$photo_s_href='';
			}else{
				$ifmy = 0;$Uphoto = $photo_s_str;
				//$href = '../u/umain.php?uid='.$senduid;
				$photo_s_href=' onclick="bk(-1);"';
			}
			$href='javascript:;';
			///////////
			if ($ifdel == $cook_uid)continue;
			//////////
			$difftime  = $addtime - $lasttime;
			if ($difftime > 60)$C .= "<span>".date("Y/m/d H:i",$addtime)."</span>";
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
			$C .= "<dl".$ifmy."><dt".$photo_s_href."><a href='".$href."'>".$Uphoto."</a></dt><dd>".$content.$ifdel_str."</dd></dl>";
			$lasttime = $addtime;
		}
		$newnum = $db->COUNT(__TBL_MSG__,"new=1 AND senduid=".$uid." AND uid=".$cook_uid." AND ifdel=0");
		if ($newnum > 0){
			$db->query("UPDATE ".__TBL_MSG__." SET new=0 WHERE new=1 AND senduid=".$uid." AND uid=".$cook_uid." AND ifdel=0");
 			$endnum = ($data_tipnum >= $newnum)?($data_tipnum - $newnum):0;
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=$endnum WHERE id=".$cook_uid);
		}
	}
	return $C."|GYL-SUPDES|".$ifmore;
}
function ajax_getMsgMore($uid,$p){ 
	global $db,$cook_uid,$photo_s_str,$cook_photo_s_str;
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
				if ($difftime > 60)$C .= "<span>".date("Y/m/d H:i",$addtime)."</span>";
				if ($senduid == $cook_uid){
					$ifmy = 1;$Uphoto = $cook_photo_s_str;
					$href = '../u/umain.php?uid='.$senduid;
				}else{
					$ifmy = 0;$Uphoto = $photo_s_str;
					$href = '../u/umain.php?uid='.$cook_uid;
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
				$C .= "<dl".$ifmy."><dt><a href='".$href."'>".$Uphoto."</a></dt><dd>".$content."</dd></dl>";
				$lasttime = $addtime;
			}
		}
	}
	return $C."|GYL-SUPDES|".$ifmore;
}
//
//$backurl = (empty($_SERVER['HTTP_REFERER']))?'openlinks("../")':'javascript:window.history.back(-1);';
//$mini_title = '　';
?>
<link href="<?php echo HOST;?>/m1/css/msg_show.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style>body{left:0;right:0;height:100%;position:absolute}</style>
<i class='ico goback Ugoback' id='ZEAIGOBACK-msg_show' style="z-index:999">&#xe602;</i>
<div class="top"><div class='title'><a href="javascript:;" id="loadmore">查看历史消息</a></div></div>
<input type="hidden" id="ifopen" value="0">
<input type="hidden" id="p" value="1">
<input type="hidden" id="starttime" value="0">
<div class="submain msg_show" id="msgmask">
    <div id="msg"></div>
    <div id="write">
        <table><tr>
        <td width="80"><i id="audiobtn" class="textbtn"></i><i id="bqbtn"></i></td>
        <td><div name="content" id="content" onclick="iput();" contentEditable="true" oninput="iput();" tabindex="0" hidefocus="true" onblur="onblurFN();"></div><div id="startRecord" contenteditable="false"></div></td>
        <td width="50"><a href="javascript:;" id="sendbtn" onclick="msg_send(<?php echo $uid; ?>);">发送</a></td>
        </tr></table>
    </div>
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
        </div>
    </div>
</div>
<script src="<?php echo HOST;?>/res/jquery-1.7.2.min.js"></script>
<script src="<?php echo HOST;?>/m1/js/msg_show.js?v=5"></script>
<script>
msgmask.addEventListener('touchmove',function(e) {e.preventDefault();});
msg.addEventListener('touchmove',function(e) {e.cancelBubble = true;});
var cook_uid= '<?php echo $cook_uid; ?>',
cook_photo_s     = '<?php echo $cook_photo_s; ?>',
uid              = '<?php echo $uid; ?>',
photo_s_str      = "<?php echo $photo_s_str ; ?>",
cook_photo_s_str = "<?php echo $cook_photo_s_str ; ?>",
up2       = "<?php echo $_ZEAI['up2']; ?>",
nickname = '<?php echo $nickname; ?>',
lovebstr = '<?php echo $_ZEAI['loveB']; ?>';
//
var H = $(window).height();
H = (H - 70);
var iflocal = true;
var bqH = 145;
$(function(){msgAudioLiPlay();});

if(browser=='wx'){
	wxRecord();
}else{
	audiobtn.onclick=function(){
		if(is_h5app()) {
			appRecord({url:HOST+'/m1/msg_show.php?submitok=ajax_app_audio&uid='+uid},function(e){ajax_app_audio(e);});
		}else{
			zeai.msg('只有在微信或APP中打开才支持语音聊天');
		}
	}
}
o('bqbtn').onclick = bqbtnFn;
o('msg').onclick = msgFn;
o('loadmore').onclick = function(){ajax_getMsgMore(uid);}
domm();
ajax_getmess(uid);
var chatdsq= setInterval("ajax_chk_flag(uid)",2000);
btm0     = setInterval("scrollTobtm()",200);
setTimeout("delbtm0()",1000);
cleandsj = chatdsq;
//newdian
setTimeout(function(){	zeai.ajax({loading:0,url:HOST+'/m1/msg'+zeai.ajxext+'submitok=ajax_getnewdian'},function(e){rs=zeai.jsoneval(e);newdian(rs);});	},500);

//document.addEventListener( "plusready", function(){var pp3 = plus.navigator.checkPermission('RECORD');}, false );
</script>

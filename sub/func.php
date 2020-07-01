<?php
/*=====以下请在Zeai交友系统官方客服指导下进行修改，以免整站与数据库错乱，无法恢复。=====*/
function nodata($uid) {
	$msg='<br>＾_＾';
	$uid=intval($uid);
	global $db;
	$row = $db->ROW(__TBL_USER__,"dataflag,birthday,heigh,edu,job","id=".$uid,"name");
	if ($row){
		if(!ifdate($row['birthday']) || !ifint($row['heigh']) || !ifint($row['edu']) || !ifint($row['job']))json_exit(array('flag'=>'nodata','msg'=>'自己的资料完善了才可以看别人的<br>＾_＾'));
		if($row['dataflag']==0)json_exit(array('flag'=>'nodata','msg'=>'您的个人资料正在审核中<br>审核通过以后就可以了'));
		if($row['dataflag']==2)json_exit(array('flag'=>'nodata','msg'=>'您的个人资料审核被驳回，请重新修改<br>审核通过以后就可以了'));
	}else{
		json_exit(array('flag'=>'nodata','msg'=>'ZEAIERROR:nodata function'));	
	}
}

function nocert($uid,$RZ='') {
	if(empty($RZ)){global $db;$row=$db->NUM($uid,"RZ");$RZ=$row[0];}
	$RZarr=explode(',',$RZ);
	if(@count($RZarr)<=0 || !is_array($RZarr) || empty($RZ))json_exit(array('flag'=>'nocert','msg'=>'只有自己认证了才能看别人的<br>＾_＾'));
}

function nocontact($uid) {
	$uid=intval($uid);global $db;
	$row = $db->ROW(__TBL_USER__,"mob,weixin,qq","id=".$uid,"name");
	if ($row){
		if(!ifmob($row['mob']) && empty($row['weixin']) && empty($row['qq']) )json_exit(array('flag'=>'nocontact','msg'=>'自己填了联系方法才可以看别人的<br>＾_＾'));
	}else{
		json_exit(array('flag'=>'nocontact','msg'=>'ZEAIERROR:nocontact function'));
	}
}
//同级查看
function nolevel($uid,$senduid,$kind,$jumpurl='') {
	$uid=intval($uid);$senduid=intval($senduid);global $db,$_VIP;
	if($_VIP['contact_level'] == 1 && $kind=='contact' || $_VIP['chat_level'] == 1 && $kind=='chat'){
		$row = $db->NUM($uid,"grade");if($row){$grade= $row[0];}else{exit(JSON_ERROR);}
		$row = $db->NUM($senduid,"grade");if($row){$sendgrade= $row[0];}else{exit(JSON_ERROR);}
		if($sendgrade<$grade)json_exit(array('flag'=>'nolevel','kind'=>$kind,'jumpurl'=>$jumpurl,'msg'=>'您只能联系会员级别比你低或同级会员<br>您当前【'.utitle($sendgrade).'】'));/*<br>请升级到【'.utitle($grade).'】*/	
	}
}

//每天查看总人数	
function noucount_clickloveb($uid,$senduid,$kind) {
	$uid=intval($uid);$senduid=intval($senduid);$cook_uid=$senduid;global $db,$_VIP;
	$row = $db->NUM($cook_uid,"grade");if($row){$cook_grade=$row[0];}else{exit(JSON_ERROR);}
	$ARR = json_decode($_VIP[$kind.'_daylooknum'],true);
	if (count($ARR) <= 0 && !is_array($ARR))json_exit(array('flag'=>0,'msg'=>$kind.'_daylooknum：参数配置错误，请联系管理员'));
	$Mymaxnum = $ARR[$cook_grade];
	$maxkey=max(array_flip($ARR));
	if($cook_grade >= $maxkey){$win='msg';}else{$win='div';}//智能选择弹窗类型
	if ($Mymaxnum>0){
		//搜索全表不限日期记录找此人
		$total = $db->COUNT(__TBL_UCOUNT__,"FIND_IN_SET($uid,listed) AND kind='".$kind."' AND uid=".$cook_uid);
		//=0 新人
		if($total==0){
			$today = YmdHis(ADDTIME,'Ymd');
			//如果今天有记录
			$row = $db->ROW(__TBL_UCOUNT__,"listed","kind='".$kind."' AND date='".$today."' AND uid=".$cook_uid,"num");
			if ($row){
				$listed = explode(',',$row[0]);
				if(count($listed)>=$Mymaxnum)json_exit(array('flag'=>'noucount','kind'=>$kind,'win'=>$win,'msg'=>'今天解锁已达上限'));/*.$Mymaxnum*/	
			}
			noclickloveb($uid,$senduid,$kind);
		}
	}else{
		//如果后台设为0，则不开放
		json_exit(array('flag'=>'noucount','kind'=>$kind,'win'=>$win,'msg'=>'您当前无权查看，请升级VIP'));
	}
}

function getSmodeUGarr() {
	global $_ZEAI;
	$switch = json_decode($_ZEAI['switch'],true);
	$ARR = $switch['Smode'];
	if (count($ARR) >= 1 && is_array($ARR)){
		$glist = array_keys($ARR,2);
		$glist = implode(",",$glist);
		return str_replace("g_","",$glist);
	}
	return false;
}	

//查看按次计费-发送确认窗口
function noclickloveb($uid,$senduid,$kind) {
	$senduid=intval($senduid);$cook_uid=$senduid;global $db,$_VIP,$_ZEAI;
	$row = $db->NUM($cook_uid,"grade,loveb");if($row){$cook_grade=$row[0];$data_loveb=$row[1];}else{exit(JSON_ERROR);}
	$ARR = json_decode($_VIP[$kind.'_loveb'],true);
	if (count($ARR) <= 0 && !is_array($ARR))json_exit(array('flag'=>0,'msg'=>$kind.'_loveb：参数配置错误，请联系管理员'));
	$minnum = min($ARR);$minkey=min(array_flip($ARR));//获取为0免费查看的金额和会员级别
	$my_clickloveb = $ARR[$cook_grade];
	//大于0收费，否则0为免费查看
	if ($my_clickloveb>0){
		switch ($kind) {
			case 'contact':$title = '联系方法';break;
			case 'chat':$title = '聊天';break;
		}
		//发送确认窗口
		json_exit(array('flag'=>'clickloveb_confirm','kind'=>$kind,'title'=>$title.'解锁<font class=\'S12\'>（我的'.$_ZEAI['loveB'].'：'.$data_loveb.'个）</font>'));
	}else{
		//只入库list
		$total = $db->COUNT(__TBL_UCOUNT__,"FIND_IN_SET($uid,listed) AND kind='".$kind."' AND uid=".$cook_uid);
		//新人
		if($total==0){
			$today = YmdHis(ADDTIME,'Ymd');
			//如果今天有记录
			$row = $db->ROW(__TBL_UCOUNT__,"id,listed","kind='".$kind."' AND date='".$today."' AND uid=".$cook_uid,"num");
			if ($row){
				$ctid = $row[0];$listed = explode(',',$row[1]);
				$listed[] = $uid;
				$newlist  = implode(",",$listed);
				$db->query("UPDATE ".__TBL_UCOUNT__." SET listed='".$newlist."' WHERE id=".$ctid);
			}else{
				$db->query("INSERT INTO ".__TBL_UCOUNT__."(uid,listed,kind,date) VALUES ($cook_uid,$uid,'$kind','$today')");
			}
		}
	}
}
function lockU($uid,$kind='chat'){
	global $cook_uid,$db;
	$row = $db->ROW(__TBL_UCOUNT__,"id","FIND_IN_SET($uid,listed) AND kind='$kind' AND uid=".$cook_uid);
	if($row){return true;}else{return false;}
}
function nophoto_s($uid) {
	global $db;
	$row = $db->ROW(__TBL_USER__,"photo_f,photo_s","id=".$uid,"name");
	if ($row){
		if($row['photo_f']==1 && !empty($row['photo_s']))json_exit(array('flag'=>1));
		if($row['photo_f']==0 && !empty($row['photo_s']))json_exit(array('flag'=>0,'msg'=>'您上传的头像正在审核中<br>审核通过以后就可以看了'));
	}
	json_exit(array('flag'=>0,'msg'=>'只有自己上传了头像才可以看别人的<br>＾_＾'));
}
function nophoto($uid) {
	global $db;
	$uid=intval($uid);
	$num = $db->COUNT(__TBL_PHOTO__,"flag=1 AND uid=".$uid);
	if ($num>0){
		json_exit(array('flag'=>1));
	}else{
		json_exit(array('flag'=>0,'msg'=>'只有自己上传了相册才可以看别人的<br>＾_＾'));
	}
}
function gzflag($uid,$senduid){
	global $db;
	$uid = intval($uid);$senduid = intval($senduid);
	$row = $db->ROW(__TBL_GZ__,"flag","uid=".$uid." AND senduid=".$senduid,"num");
	if ($row){return $row[0];}else{return 0;}
}

function mateset_out($i1,$i2,$unit){
	if($i1 == 0 && $i2 == 0){
		$str = "不限";
	}elseif($i1 == $i2){
		$str = $i1.$unit;
	}elseif($i1 > 0 && $i2 > 0){
		$str = $i1."～".$i2.$unit;
	}elseif($i1 == 0 && $i2 > 0){
		$str = $i2.$unit."以下";
	}elseif($i1 > 0 && $i2 == 0){
		$str = $i1.$unit."以上";
	}
	return $str;
}




function getweek($date) {
$dateArr = explode("-", $date);
$weeknum = date("w", mktime(0,0,0,$dateArr[1],$dateArr[2],$dateArr[0]));
switch ($weeknum){
case 0:$xingqi='星期日';break;
case 1:$xingqi='星期一';break;
case 2:$xingqi='星期二';break;
case 3:$xingqi='星期三';break;
case 4:$xingqi='星期四';break;
case 5:$xingqi='星期五';break;
case 6:$xingqi='星期六';break;
}return $xingqi;}



$iaez2='ai_';








function gyl_log($C,$drname='paylog',$flename=''){
	global $_PAY;
	$flename = (empty($flename))?$_PAY['logname']:$flename;
	$C = date("Y-m-d H:i:s").$C.PHP_EOL;
	$p = fopen(ZEAI."up/p/".$drname."/".date("Ymd").$flename.".txt","a+");			
	fwrite($p,$C);fclose($p);
}
function gyl_debug($C){
	$C =  $C.PHP_EOL.PHP_EOL;
	$p = fopen(ZEAI."cache/_zeai_debug/ZEAI_debug.txt","a");			
	fwrite($p,$C);
	fclose($p);
}






/*=============================WEIXIN=============================*/
function wx_get_access_token(){
	global $_ZEAI;
	//$data = json_decode(@file_get_contents(ZEAI."cache/wxdata/access_token.json"));
	//if ($data->expire_time < ADDTIME || empty($data->expire_time)) {
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$_ZEAI['wx_gzh_appid']."&secret=".$_ZEAI['wx_gzh_appsecret'];
		$res = json_decode(get_contents($url));
		$access_token = $res->access_token;
//		if ($access_token) {
//			$data->expire_time  = ADDTIME + 3600;
//			$data->access_token = $access_token;
//			$fp = fopen(ZEAI."cache/wxdata/access_token.json", "w+");
//			fwrite($fp,json_encode($data));
//			fclose($fp);
//		}
//	} else {
//		$access_token = $data->access_token;
//	}
	return $access_token;
}

function wx_get_openid($ifcook=1){
	global $_ZEAI;
	$code=$_GET['code'];
	if(empty($code)){
		header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$_ZEAI['wx_gzh_appid']."&redirect_uri=".urlencode(HOST.$_SERVER['REQUEST_URI'])."&response_type=code&scope=snsapi_base&state=1#wechat_redirect");
	}else{
		$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$_ZEAI['wx_gzh_appid']."&secret=".$_ZEAI['wx_gzh_appsecret']."&code=".$code."&grant_type=authorization_code";
		$data = get_contents($url);
		$data = json_decode($data,true);
		$openid = $data['openid'];
		if (str_len($openid) > 10 ){
			if($ifcook==1)setcookie('cook_openid',$openid,time()+720000,'/',$_ZEAI['CookDomain']);
			return $openid;
		}else{
			if($ifcook==1)setcookie('cook_openid','',time()+720000,'/',$_ZEAI['CookDomain']);
			return '';
		}
	}
}
function openid_chk() {
	global $cook_uid,$db,$cook_openid;
	if(ifint($cook_uid) && is_weixin()){
		$row = $db->ROW(__TBL_USER__,"openid,subscribe","id=".$cook_uid,"num");
		if ($row){
			$data_openid    = $row[0];
			$data_subscribe = $row[1];
			if($data_subscribe == 1){
				if(empty($cook_openid)){
					$server_openid = wx_get_openid();
				}else{
					$server_openid = $cook_openid; 
				}
				if(str_len($server_openid)>20){
					$db->query("UPDATE ".__TBL_USER__." SET openid='$server_openid' WHERE id=".$cook_uid);
				}
			}else{
				if(empty($cook_openid)){
					$server_openid = wx_get_openid();
				}else{
					$server_openid = $cook_openid; 
					//$server_openid = wx_get_openid();
				}
				if(str_len($server_openid)>20){
					$row2 = $db->ROW(__TBL_USER__,"id,subscribe","openid='".$server_openid."'","num");
					if ($row2){
						$uid2      = $row2[0];
						$subscribe2= $row2[1];
						if($cook_uid != $uid2 && $subscribe2==1){
							//ZEclearcookAI_CN();
							return $uid2;//当前微信登录过其它帐号且已关注
						}
					}else{//找不到，以前此微信没登录过，给当前会员写入openid
						if(empty($data_openid)){
							$db->query("UPDATE ".__TBL_USER__." SET openid='$server_openid' WHERE id=".$cook_uid);
						}
					}
				}				
			}
		}
	}		
	return false;
}

function wx_get_uinfo($token,$openid){
	$data = del_emoji(get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$openid."&lang=zh_CN"));
	$data = json_decode($data,1);
	return $data;
}
function wx_get_uinfo_logo($headimgurl,$uid,$dir='m'){
	global $_ZEAI,$_UP;
	$file      = get_contents($headimgurl,10);
	//$dbpicname = setphotodbname('m','wx_picstream','',$uid);
	$dbpicname = setphotodbname($dir,'wx_picstream',$uid.'_');
	if($dir == 'm'){
		//if (!up_send_stream($file,$dbpicname,0,$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))exit('请联系开发者：QQ:797311');
		@up_send_stream($file,$dbpicname,0,$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']);
	}elseif($dir == 'photo'){
		if (!up_send_stream($file,$dbpicname,0,$_UP['upSsize'],$_UP['upBsize']))exit('请联系开发者微信：supdes');
	}
	return $dbpicname;
}



function wx_sent_kf_PushInfo($openid){//要整一下加 news,image,text
	global $db,$_ZEAI,$_GZH;
	switch ($_GZH['wx_gzh_push_kind']) {
		case 'text':
			if (!empty($_GZH['wx_gzh_push_text_C'])){
				@wx_sent_kf_msg($openid,addslashes($_GZH['wx_gzh_push_text_C']),'text');
				$db->query("UPDATE ".__TBL_USER__." SET ifWeixinPushInfo=0 WHERE openid='$openid' AND openid<>''");
			}
		break;  
		case 'pic':
			$title   = $_GZH['wx_gzh_push_pic_title'];
			$content = dataIO($_GZH['wx_gzh_push_pic_C'],'wx');
			$picurl  = $_ZEAI['up2']."/".$_GZH['wx_gzh_push_pic_path'];
			$url     = trimhtml($_GZH['wx_gzh_push_pic_url']);
			if (!empty($_GZH['wx_gzh_push_pic_title'])){
				$news_list[] = array('title'=>$title,'description'=>$content,'picurl'=>$picurl,'url'=>$url);
				@wx_sent_kf_msg($openid,$news_list,'news');
				$db->query("UPDATE ".__TBL_USER__." SET ifWeixinPushInfo=0 WHERE openid='$openid' AND openid<>''");
			}
		break;
		case 'ulist'://穴菜了，只能一条了
			if (!empty($_GZH['wx_gzh_push_ulist'])){
				$rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,heigh,edu,pay FROM ".__TBL_USER__." WHERE id IN (".$_ZEAI['wxapi_push_Userlist'].") AND photo_s<>'' AND photo_f=1 AND flag=1 ORDER BY refresh_time DESC LIMIT 8");
				$total = $db->num_rows($rt);
				if ($total > 0) {
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt,'num');
						if(!$rows) break;
						$uid      = $rows[0];
						$nickname = dataIO($rows[1],'out');
						$sex      = $rows[2];
						$grade    = $rows[3];
						$photo_s  = $rows[4];
						$photo_f  = $rows[5];
						$areatitle= $rows[6];
						$birthday = $rows[7];
						$heigh    = $rows[8];
						$edu      = $rows[9];
						$pay      = $rows[10];
						//
						$birthday_str  = (@getage($birthday)<=0)?'':@getage($birthday).'岁 ';
						$heigh_str     = (empty($heigh))?'':$heigh.'cm';
						$areatitle_str = (empty($areatitle))?'':$areatitle.' ';
						$edu = udata('edu',$edu);$edu_str = (empty($edu))?'':$edu.' ';
						$pay = udata('pay',$pay);$pay_str = (empty($pay))?'':$pay.' ';
						//
						if ($i == 1){$photo_s = str_replace("_s","_b",$photo_s);}
						$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/images/photo_s'.$sex.'.png';
						$title   = $nickname." ".$birthday_str.$heigh_str."\n".$areatitle_str.$edu_str.$pay_str;
						$content = $title;
						$picurl  = $photo_s_url;
						$url     = HOST.'/u/umain.php?uid='.$uid;
						$news_list[] = array('title'=>$title,'description'=>$content,'picurl'=>$picurl,'url'=>$url);
					}
					@wx_sent_kf_msg($openid,$news_list);
					$db->query("UPDATE ".__TBL_USER__." SET ifWeixinPushInfo=0 WHERE openid='$openid' AND openid<>''");
				}
			}
		break;
	}
}

function get_contents($url,$time=10){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT,$time*1000);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function wx_sent_kf_msg($openid,$C,$kind){
	$token = wx_get_access_token();
	switch ($kind) {
		case 'text':
			$C = dataIO($C,'out');
			$C = str_replace("<br>","\r\n",$C);
			$C = str_replace('"',"'",$C);
			$data ='{"touser":"'.$openid.'","msgtype":"text","text":{"content":"'.$C.'"}}';
		break;
		case 'image':
			if(stripos($C,'http://') !== false || stripos($C,'https://') !== false){
				$tmpfile = up_send_urlstream($C,'tmp');
			}else{
				$tmpfile = $C;
			}
			$realfile = (PHP_VERSION < 5.6)?'@'.ZEAI.'up/'.$tmpfile:new CURLFile(ZEAI.'up/'.$tmpfile);
			$postwxdata = array('zeai_cn__picup'=>$realfile);
			$res  = Zeai_POST_stream('https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$token.'&type=image',$postwxdata);
			$res  = json_decode($res);$media_id = $res->media_id;
			$data = '{"touser":"'.$openid.'","msgtype":"image","image":{"media_id":"'.$media_id.'"}}';
		break;
		case 'news':if (@is_array($C)){$data = '{"touser":"'.$openid.'","msgtype":"news","news":{"articles":'.encode_json($C).'}}';}
		break;
		case 'video'://不支持http视频，大于10M限制无效
			$tmpfile = $C;
			$realfile = (PHP_VERSION < 5.6)?'@'.ZEAI.'up/'.$tmpfile:new CURLFile(ZEAI.'up/'.$tmpfile);
			$postwxdata = array('zeai_cn__picup'=>$realfile);
			$res  = Zeai_POST_stream('https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$token.'&type=video',$postwxdata);
			$res  = json_decode($res);$media_id = $res->media_id;
			$data = '{"touser":"'.$openid.'","msgtype":"video","video":{"media_id":"'.$media_id.'"}}';
		break;
	}
	$rs = Zeai_POST_stream('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$token,$data);
	return $rs;
}

function wx_mb_sent($parameter) {
	return get_contents(HOST.'/api/weixin/mb_sent.php?'.$parameter);
}
function wx_kf_sent($oid,$C,$kind) {
	return get_contents(HOST.'/api/weixin/kf_sent.php?kind='.$kind.'&oid='.$oid.'&C='.$C);
}


function dataIO($d,$inout='out',$maxlen=0,$suffix=false){
	$d2=strtolower($d);
	$preg = "/<script[\s\S]*?<\/script>/i";
	if(strstr($d2,'script')){
		$d = preg_replace($preg,"",$d2);
		$d=str_replace("<script","",$d);
	}else{
		$d = preg_replace($preg,"",$d);
	}
	$d = preg_replace( "@<iframe(.*?)</iframe>@is", "", $d ); 
	$d = preg_replace( "@<style(.*?)</style>@is", "", $d ); 
	//$d = preg_replace( "@<(.*?)>@is", "", $d ); 	
	//$d=str_replace("script","",$d);
	if (ifint($maxlen) && $maxlen>0){
		$d = @gylsubstr($d,$maxlen,0,'utf-8',$suffix);
	}
	if ($inout == 'in'){
		$d=str_replace("insert","",$d);
		$d=str_replace("delete","",$d);
		$d=str_replace("drop","",$d);
		$d=str_replace("update","",$d);
		$d = del_emoji($d);
		$d = @htmlspecialchars($d,ENT_QUOTES);
		$d = str_replace("\r\n","<br>",$d);
	}elseif($inout == 'codein'){
		//$d = daddslashes($d,true);
	}elseif($inout == 'wx'){
		$d = urldecode($d);
		$d = stripslashes(htmlspecialchars_decode($d));
		$d = str_replace("<br>","\r\n",$d);
	}else{
		$d = urldecode($d);
		$d = stripslashes(htmlspecialchars_decode($d));
	}
	return $d;
}
function htmlout($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = htmlout($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
		str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
			$string=str_replace("\r\n","<br>",$string);
			$string=str_replace("'","&#039;",$string);
			$string=str_replace(" ","&nbsp;",$string);
			$string=str_replace("  ","　",$string);
	}
	return $string;
}
function TrimEnter ($str) {
	$str = strip_tags($str);
	$str = str_replace("\t","",$str);
	$str = str_replace("\r\n","",$str);
	$str = str_replace("\r","",$str);
	$str = str_replace("\n","",$str); 
	return $str;
}
function trimhtml ($str) {
	$str=trim($str);
	$str=str_replace("'","",$str);
	$str=str_replace("\"","",$str);
	$str=str_replace("&amp;","&",$str);
	$str=str_replace("&nbsp;","",$str);
	$str=str_replace("&gt;",">",$str);
	$str=str_replace("&lt;","<",$str);
	$str=str_replace(" ","",$str);
	$str=str_replace("　","",$str);
	$str = strip_tags($str);
	$str = TrimEnter($str);
	return $str;
}



/*========================================================================================2018===========================================================================*/
define('MP',((is_weixin()||is_mobile())?'m'.$_ZEAI['mob_mbkind']:'p'.$_ZEAI['pc_mbkind']));
function is_weixin(){ if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) { return true; }else{ return false;}}
function is_mobile(){
	$regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
	$regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
	$regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";   
	$regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
	$regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
	$regex_match.=")/i";       
	return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
}
function is_h5app(){
	if(strpos($_SERVER['HTTP_USER_AGENT'],"Html5Plus") === false){//仅在非5+引擎环境下才显示导航栏
		return false;
	}else{
		return true;
	}
}
function NoUserInfo() {global $_ZEAI;echo HEADMETA;echo "<font color='#999999' style='font-size:12px;font-family:Verdana'>".$_ZEAI['siteName']."( <a href=".HOST.">".HOST."</a> )温馨提示：</FONT><BR><font color='#ff0000' style='font-size:12px'>请求错误，该信息或用户不存在或未审核或已被锁定或已被删除！</FONT>";exit;}

function encode_json($str) {
	return urldecode(json_encode(url_encode($str)));	
}
function url_encode($str) {
	if(is_array($str)) {
		foreach($str as $key=>$value) {
			$str[urlencode($key)] = url_encode($value);
		}
	} else {
		$str = urlencode($str);
	}
	return $str;
}
function json_exit($arr){exit(encode_json($arr));}

function IFZEAI(){exit;}
function del_emoji($str){
	$str = preg_replace_callback('/./u','del_emoji_fn',$str);
	return $str;
}
function del_emoji_fn($match){
	return (strlen($match[0]) >= 4)?'':$match[0];
}

function Zeai_POST_stream($url,$postdata){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_TIMEOUT,50);
	curl_setopt($curl, CURLOPT_URL, $url);
	if (stripos ($url,'https://') !== false) {
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,false);
	}
	curl_setopt($curl, CURLOPT_POST,1);
	if (PHP_VERSION<6){
		curl_setopt($curl, CURLOPT_SAFE_UPLOAD,false);//后加
	}	
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	$result = curl_exec($curl);
	if (curl_errno($curl)){ echo 'Error'.curl_error($curl); }
	curl_close($curl);
	return $result;
}

function getpath_smb($picurl,$smb){ 
	if ($smb == 's'){
		return str_replace("_b.","_s.",$picurl);
	}elseif($smb == 'm'){
		return str_replace("_s.","_m.",$picurl);
	}elseif($smb == 'b'){
		return str_replace("_s.","_b.",$picurl);
	}elseif($smb == 'blur'){
		return str_replace("_s.","_blur.",$picurl);
	}
}
function smb($picurl,$smb){
	return getpath_smb($picurl,$smb);
}
function ifint($num,$n1='',$n2=''){//ifint($num,'0-9','1,4')
	if (!empty($n1) && !empty($n2)){
		if ( preg_match("/^[".$n1."]{".$n2."}$/",$num) && !empty($num)){
			return true;
		}else{
			return false;
		}
	}else{
		if ( preg_match("/^[0-9]{1,9}$/",$num) && !empty($num) && $num!=0){
			return true;
		}else{
			return false;
		}
	}
}
function ifdatetime($str){
	return strtotime($str) !== false;
}
function ifdate($param,$format=''){
	$param2 = intval(substr($param,0,4));
	if($param2>2099){
		return false;
	}
	if(!empty($format)){
		return date($format, strtotime($param)) === $param;
	}
	if ( !preg_match('/(^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$)/',$param) ) {
		return false;
	}else{
		return true;
	}
}$Za1='ze';

function trimm ($str) {
	$str = str_replace(" ","",trim($str));
	return $str;
}
function getSFZbirthday($sfz){ 
	$str = (strlen($sfz)==15) ? ('19'.substr($sfz,6,6)) : substr($sfz, 6, 8);
	$Y = substr($str,0,4);
	$M = substr($str,4,2);
	$D = substr($str,6,2);
	return $Y.'-'.$M.'-'.$D;
}
function getage($birthday){ 
	if (empty($birthday) || $birthday == '0000-00-00')return 0;
	$age = strtotime($birthday); 
	if($age === false){return false;} 
	list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age)); 
	$now = strtotime("now"); 
	list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now)); 
	$age = $y2 - $y1; 
	if((int)($m2.$d2) < (int)($m1.$d1)) 
	$age -= 1;
	if ($age<18 || $age>90)$age='';
	return $age;
} 

function wirte_file($file,$str){ 
	@set_time_limit(0);
	$fp=@fopen($file,"w");
	if(@fwrite($fp,$str)){ 
		fclose($fp);
		return true;
	}else{
		fclose($fp);
		return false;
	}
}

function mk_dir($directory,$mode=0777){
	if(is_dir($directory)){
		return false;
	}else{
		if(mkdir($directory, $mode, true)) {
			return true;
		}else{
			return false;
		}
	}
}

function date_str($time) {
	$diff = ADDTIME - $time;
	switch ($diff) {
	case $diff <= 120 :$t = '刚刚';break;
	case $diff >120 && $diff <= 3600:$t = floor($diff / 60) . '分钟前';break;
	case $diff >3600 && $diff <= 86400:$t = floor($diff / 3600) . '小时前';break;
	case $diff >86400 && $diff <= 2592000:$t = floor($diff / 86400) . '天前';break;
	case $diff >2592000 &&  $diff <= 7776000:$t = floor($diff / 2592000) . '个月前';break;
	case $diff >7776000:$t = '很久以前';break;
	}return $t;
}
function yxq($d) {
	switch ($d) {
		case 7 :$t='1周';break;
		case 30:$t='1个月';break;
		case 90:$t='3个月';break;
		case 180:$t='6个月';break;
		case 365:$t='1年';break;
		case 730:$t='2年';break;
		case 1095:$t='3年';break;
		default:$t=$t.'天';break;
	}return $t;
}
function YmdHis($int,$format=''){
	//if(empty($int))return date('Y-m-d H:i:s');
	if(empty($int))return '';
	switch ($format) {
		case 'YmdHi':$t = date('Y-m-d H:i',$int);break;
		case 'mdHi':$t  = date('m-d H:i',$int);break;
		case 'Ymd':  $t = date('Y-m-d',$int);break;
		case 'Ym':   $t = date('Y-m',$int);break;
		case 'Y年m月d日':  $t = date('Y',$int).'年'.date('m',$int).'月'.date('d',$int).'日';break;
		case 'Y':    $t = date('Y',$int);break;
		case 'm':    $t = date('m',$int);break;
		case 'd':    $t = date('d',$int);break;
		case 'H':    $t = date('H',$int);break;
		case 'i':    $t = date('i',$int);break;
		default:     $t = date('Y-m-d H:i:s',$int);break;
	}
	return $t;
}
function getip() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$onlineip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$onlineip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	$onlineip = preg_replace("/^([\d\.]+).*/", "\\1", $onlineip);
	return $onlineip;
}
function zeai_alone () {exit;}
function daddslashes($string, $force = 0, $strip = FALSE) {
	global $magic_quotes_gpc;
    //if(!MAGIC_QUOTES_GPC || $force || !$magic_quotes_gpc) {
    if($force || !get_magic_quotes_gpc()) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = daddslashes($val, $force);
            }
        } else {
            $string = addslashes($strip ? stripslashes($string) : $string);
        }
    }
    return $string;
}
function cdstr($length) {
	$possible = "0123456789";
	$str = "";
	while(strlen($str) < $length) $str .= substr($possible, (rand() % strlen($possible)), 1);
	return($str);
}
function cdstrletters($length) {
	$possible = "abcdefghijklmnopqrstuvwxyz";
	$str = "";
	while(strlen($str) < $length) $str .= substr($possible, (rand() % strlen($possible)), 1);
	return($str);
}
function cdnumletters($length) {
	$possible = "0123456789abcdefghijklmnopqrstuvwxyz";
	$str = "";
	while(strlen($str) < $length) $str .= substr($possible, (rand() % strlen($possible)), 1);
	return($str);
}
function gylsubstr($str,$length,$start=0,$charset='utf-8',$suffix=false,$suffix_str='...') {
	if(@function_exists("mb_substr")){
		 if(mb_strlen($str, $charset) <= $length) return $str;
			$slice = mb_substr($str, $start, $length, $charset);
	} else{$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']          = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']          = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		if(count($match[0]) <= $length) return $str;
		$slice = join("",array_slice($match[0], $start, $length));
	} if($suffix) return $slice.$suffix_str;
	return $slice;
}
function str_len($str){
    $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));
    if ($length)    {
        return strlen($str) - $length + intval($length / 3) * 2;
    }else{
        return strlen($str);
    }
}
function callmsg ($v,$gourl="-1") {
	switch ($gourl) {
	case "-1":$js = "<script>alert('".$v."');history.go(-1);</script>";break;
	case "0":$js = "<script>alert('".$v."');</script><script>window.opener=null;window.close();</script>";break;
	default:$js = "<script>alert('".$v."');window.location.href='".$gourl."';</script>";break;}
	echo "<html><title>".$v."</title><body bgcolor='#ffffff'>".$js."</body></html>";exit;
}
function textmsg ($t,$gourl='',$btnt='返回') {
	global $_ZEAI;
	echo "<!doctype html><html><head><meta charset='utf-8'><title>".urldecode($t)."</title>".HEADMETA."<link href='".$_ZEAI['adm2']."/css/main.css' rel='stylesheet' type='text/css' /></head><body>";
	switch ($gourl) {
		case '':$href = "javascript:history.go(-1);";break;
		case 'back':$href = "javascript:history.go(-1);";break;
		default:$href = $gourl;break;
	}
	$a = (!empty($gourl))?"<br><br><br><a href='".$href."' class='aQINGed'>".$btnt."</a>":'';
	echo "<br><br><img src='images/tips.png' width='50' height='50'><br><br><font class='S14 C666'>".$t."</font>".$a;
	echo "</body></html>";exit;
}

function alert($t,$gourl,$ifparent=false,$ifadm=false) {
	global $_ZEAI;
	echo "<!doctype html><html><head><meta charset='utf-8'><title>".urldecode($t)."</title>".HEADMETA."</head><body>";
	if ($ifadm){
		echo "<link href='".$_ZEAI['adm2']."/css/main.css' rel='stylesheet' type='text/css' />";
	}else{
		echo "<link href='".HOST."/res/www_zeai_cn.css' rel='stylesheet' type='text/css' />";
		$mp = (is_mobile())?'m1':'p1';
		echo "<link href='".HOST."/".$mp."/css/".$mp.".css' rel='stylesheet' type='text/css' />";
	}
	echo "<script src='".HOST."/res/www_zeai_cn.js'></script>";
	if ($ifparent)$parentstr = 'parent.';
	echo "<script>window.onload = function (){".$parentstr."zeai.alert('".urldecode($t)."','".$gourl."');}</script>";
	echo "</body></html>";exit;
}
function alert_parent ($t,$gourl){alert($t,$gourl,true);}
function alert_adm ($t,$gourl='-1'){alert($t,$gourl,false,true);}
function alert_adm_parent ($t,$gourl){alert($t,$gourl,true,true);}
function uHref($uid){
	return mHref('u',$uid);
}
function Href($kind,$id='',$uid=''){
	$ifhtml=true;
	switch ($kind) {
		case 'u':$url = ($ifhtml)?HOST.'/u/'.$id:HOST.'/p1/u.php?uid='.$id;break;
		case 'user':$url = ($ifhtml)?HOST.'/user/':HOST.'/p1/user.php';break;
		case 'video':$url = ($ifhtml)?HOST.'/video/':HOST.'/p1/video.php';break;
		case 'hongniang':
			if(ifint($id)){
				$url = ($ifhtml)?HOST.'/hongniang/'.$id.'.html':HOST.'/p2/hongniang_detail.php?fid='.$id;
			}else{
				$url = ($ifhtml)?HOST.'/hongniang/':HOST.'/p2/hongniang.php';
			}
		break;
		case 'hongbao':
			if(ifint($id)){
				$url = ($ifhtml)?HOST.'/hongbao/'.$id.'.html':HOST.'/p1/hongbao_detail.php?fid='.$id;
			}else{
				$url = ($ifhtml)?HOST.'/hongbao/':HOST.'/p1/hongbao.php';
			}
		break;
		case 'news':
			if(ifint($id)){
				$url = ($ifhtml)?HOST.'/news/'.$id.'.html':HOST.'/p1/news_detail.php?fid='.$id;
			}else{
				$url = ($ifhtml)?HOST.'/news/':HOST.'/p1/news.php';
			}
		break;
		case 'about_news':
			if(ifint($id)){
				$url = ($ifhtml)?HOST.'/about/news'.$id.'.html':HOST.'/p1/about.php?t=news_detail&fid='.$id;
			}else{
				$url = ($ifhtml)?HOST.'/about/news/':HOST.'/p1/about.php?t=news';
			}
		break;
		case 'trend':
			if(ifint($id)){
				$url = ($ifhtml)?HOST.'/trend/'.$id.'.html':HOST.'/p1/trend.php?uid='.$id;
			}else{
				$url = ($ifhtml)?HOST.'/trend/':HOST.'/p1/trend.php';
			}
		break;
		case 'crm_u':$url = 'crm_user_detail.php?t=2&uid='.$id;break;
		case 'party':
			if(ifint($id)){
				$url = ($ifhtml)?HOST.'/party/'.$id.'.html':HOST.'/p1/party_detail.php?fid='.$id;
			}else{
				$url = ($ifhtml)?HOST.'/party/':HOST.'/p1/party.php';
			}
		break;
		case 'dating':
			if(ifint($id)){
				$url = ($ifhtml)?HOST.'/dating/'.$id.'.html':HOST.'/p1/dating_detail.php?fid='.$id;
			}else{
				$url = ($ifhtml)?HOST.'/dating/':HOST.'/p1/dating.php';
			}
		break;
		
		case 'loveb':$url = HOST.'/p1/my_loveb.php';break;
		case 'money':$url = HOST.'/p1/my_money.php';break;
		case 'cert':$url  = HOST.'/p1/my_cert.php';break;
		case 'vip':$url  = HOST.'/p1/my_vip.php';break;
		case 'my_hongbao':$url = HOST.'/p1/my_hongbao.php';break;
		case 'my_info':$url = HOST.'/p1/my_info.php';break;
		case 'my':$url = HOST.'/p1/my.php';break;
		case 'tz':$url = HOST.'/p1/my_msg.php?t=2';break;
		
		case 'about':$url = ($ifhtml)?HOST.'/about/':HOST.'/p1/about.php';break;
		case 'kefu':$url = ($ifhtml)?HOST.'/kefu/':HOST.'/p1/about.php?t=contact';break;
		case 'clause':$url = ($ifhtml)?HOST.'/clause/':HOST.'/p1/about.php?t=clause';break;

		case 'group':$url = HOST.'/m1/group/group_main.php?mainid='.$id;break;
		case 'group_wz':$url = HOST.'/m1/group/group_read.php?fid='.$id;break;
		case 'group_party':$url = HOST.'/m1/group/group_partyshow.php?fid='.$id;break;
		default:break;
	}
	return $url;
}

function wHref($kind,$id=''){
	switch ($kind) {
		case 'u':$url = HOST.'/m1/u.php?uid='.$id.'&m=wap';break;
		case 'article':
			if(ifint($id)){
				$url = HOST.'/m1/article_detail.php?id='.$id;
			}else{
				$url = HOST.'/m1/article.php';
			}
		break;
		case 'party':
			if(ifint($id)){
				$url = HOST.'/m1/party_detail.php?fid='.$id;
			}else{
				$url = HOST.'/?z=party';
			}
		break;
	}
	return $url;
}
function mHref($kind,$id=''){
	switch ($kind) {
		case 'party':
			if(ifint($id)){
				$url = HOST.'/?z=party&e=detail&a='.$id;
			}else{
				$url = HOST.'/?z=party';
			}
		break;
		case 'dating':
			if(ifint($id)){
				$url = HOST.'/?z=dating&e=detail&a='.$id;
			}else{
				$url = HOST.'/?z=dating';
			}
		break;
		case 'video':$url = HOST.'/?z=video';break;
		case 'trend':$url = HOST.'/?z=trend';break;
		case 'loveb':$url = HOST.'/?z=my&e=my_loveb';break;
		case 'money':$url = HOST.'/?z=my&e=my_money';break;
		case 'cert':$url  = HOST.'/?z=my&e=my_info&a=cert';break;
		case 'vip':$url   = HOST.'/?z=my&e=my_vip';break;
		case 'my_gift':$url = HOST.'/?z=my&e=my_gift';break;
		case 'my_chat':$url = HOST.'/?z=msg&e=sx';break;
		case 'my_tz':$url = HOST.'/?z=msg&e=tz';break;
		case 'my_photo':$url = HOST.'/?z=my&e=my_info&a=photo';break;
		case 'my_video':$url = HOST.'/?z=my&e=my_info&a=video';break;
		case 'my_info':$url = HOST.'/?z=my&e=my_info';break;
		case 'u':$url = HOST.'/?z=index&e=u&a='.$id;break;
		case 'my':$url = HOST.'/?z=my';break;
		case 'TG':$url = HOST.'/?z=my&e=TG';break;
		case 'tz':$url = HOST.'/?z=msg&e=tz';break;
	}
	return $url;
}
function ifmob($str){
	$pattern = "/^(13|14|15|16|17|18|19)\d{9}$/";
	if (preg_match($pattern,$str)){
		return true;
	}else{
		return false;
	}
}$ai2='ai';
function ifemail($v){
	if (preg_match("/^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/",$v)){
		return true;
	}else{
		return false;
	}
}
function ifpic($filename){
	if (!function_exists( 'exif_imagetype' ))return true;
	if (@exif_imagetype($filename) != IMAGETYPE_GIF && @exif_imagetype($filename) != IMAGETYPE_JPEG && @exif_imagetype($filename) != IMAGETYPE_PNG ){
		return false;
	}else{
		return true;
	}
}
function getpicextname($filename){
	if (!function_exists( 'exif_imagetype' )) {
		return exif_imagetype_($filename);
	}else{
		if (@exif_imagetype($filename) == IMAGETYPE_GIF){
			return 'gif';	
		}elseif(@exif_imagetype($filename) == IMAGETYPE_JPEG){
			return 'jpg';	
		}elseif(@exif_imagetype($filename) == IMAGETYPE_PNG){
			return 'png';	
		}else{
			return false;
		}	
	}
}
function exif_imagetype_ ( $filename ) {
    if ( !empty($filename) && ( list($width, $height, $type, $attr) = getimagesize($filename) ) !== false ) {
		switch ($type) { 
			case 1:$ftype = 'gif';break; 
			case 2:$ftype = 'jpg';break; 
			case 3:$ftype = 'png';break; 
		} 
        return $ftype;
    }
	return false;
}
function uicon($sexgrade,$size=1){
	global $_ZEAI;
	$grade=substr($sexgrade,1,2);
	$sexgrade = (substr($sexgrade,0,1) == 0)?0:$sexgrade;
	return '<img class="ugrade'.$size.'" src="'.$_ZEAI['up2'].'/p/img/grade'.$sexgrade.'.png?'.$_ZEAI['cache_str'].'" title="'.utitle($grade).'">';
}
function utitle($grade){
	global $_ZEAI;
	$R = json_decode($_ZEAI['urole']);
	foreach($R as $v){
		if ($grade == $v->g){return $v->t;}
	}
}
function flagtitle($flag){
	switch($flag){
		case"-1":return '已锁定';break;
		case"-2":return '已隐藏';break;
		case"0":return '未审';break;
		case"1":return '正常';break;
		case"2":return '注册未完成';break;
	}
}
function uicon_grade_all($grade,$size=1){
	global $_UDATA;
	$sex_ARR = json_decode($_UDATA['sex'],true); 
	foreach ($sex_ARR as $v) {
		$pic_str .= uicon($v['i'].$grade,$size);
	}
	return $pic_str;
}
function RZ_html($list,$size='s',$kind=''){
	global $_ZEAI;
	$myarr  = explode(',',$list);
	$rz_data = explode(',',$_ZEAI['rz_data']);
	if (empty($myarr) || count($myarr)<=0)return '';
	$allarr = $rz_data;
	$edsty = ($kind=='color' || $kind == 'allcolor')?' ed_color_':' ed';
	$rtn = '<ul class="RZBOX">';
	if ($kind == 'all' || $kind == 'allcolor'){
		foreach ($allarr as $av){
			if(!in_array($av,$rz_data))continue;
			if(in_array($av,$myarr)){
				$title = '已认证';
				$ifin = ($kind == 'allcolor')?$edsty.$av:$edsty;
			}else{
				$ifin = '';
				$title = '未认证';
			}
			$title = '【'.rz_data_info($av,'title').'】'.$title;
			$rtn .= '<i class="ico '.$av.$ifin.' '.$size.'" title="'.$title.'"></i>';}
	}else{
		foreach ($myarr as $v){
			if(!in_array($v,$rz_data))continue;
			$edsty = ($kind=='color')?' ed_color_'.$v:' ed';
			$title = '【'.rz_data_info($v,'title').'】已认证';
			$rtn  .= '<i class="ico '.$v.$edsty.' '.$size.'" title="'.$title.'"></i>';
	}}
	$rtn .= '</ul>';return $rtn;
}
function RZ_star($RZ){
	$myarr  = explode(',',$RZ);
	if (empty($myarr) || count($myarr)<=0)return '';
	$star='';$i=0;
	foreach ($myarr as $av){
		$i++;
		if($i>5)break;
		$star .='<span class="ico star" title="诚信等级">&#xe646;</span>';
	}
	return $star;
}
function RZtitle($kind) {
	return rz_data_info($kind,'title');
/*	switch ($kind) {
		case 'mob':$rt = '手机';break;
		case 'identity':$rt = '身份';break;
		case 'edu':$rt = '学历';break;
		case 'car':$rt = '汽车';break;
		case 'house':$rt = '房产';break;
		case 'email':$rt = '邮箱';break;
		case 'weixin':$rt = '微信';break;
		case 'qq':$rt = 'QQ';break;
		default:$rt = '未知';break;
	}
	return $rt;
*/
}
function ZEclearcookAI_CN(){
	global $_ZEAI;
	setcookie("cook_uid","",null,"/",$_ZEAI['CookDomain']);  
	setcookie("cook_uname","",null,"/",$_ZEAI['CookDomain']);  
	setcookie("cook_pwd","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_sex","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_grade","",null,"/",$_ZEAI['CookDomain']);  
	setcookie("cook_nickname","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_openid","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_unionid","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_subscribe","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_photo_s","",null,"/",$_ZEAI['CookDomain']); 
	setcookie("cook_birthday","",null,"/",$_ZEAI['CookDomain']); 
	setcookie("cook_tmp_openid","",null,"/",$_ZEAI['CookDomain']);
}
function get_if2_title($if2) {
	if(empty($if2))return '';
	if($if2<12){
		$if2str = $if2.'个月';
	}elseif($if2>=999){
		$if2str = '永久';
	}elseif($if2>=12){
		$intnum = intval($if2/12);
		$modnum = $if2 % 12;
		$if2str = $intnum.'年';
		if ($modnum>0)$if2str.=$modnum.'个月';
	}
	return $if2str;
}
function checkbox_div_list_get_title($id,$ARR){
	foreach ($ARR as $V) {
		if ($V['i'] == $id)return $V['v'];
}}
$dbvar=$Za1.$ai2;function ifalone () {exit;}
function checkbox_div_list_get_listTitle($objstr,$list){
	global $_UDATA;
	$list = explode(',',$list);
	$list_text = array();
	foreach($list as $v){
		$list_text[]=checkbox_div_list_get_title($v,json_decode($_UDATA[$objstr],true));
	}
	return implode(",",$list_text);	
}

function push_friend_tip($uid,$title='',$content=''){
	global $db,$cook_uid;
	$uid = (!ifint($uid))?$cook_uid:$uid;
	$uid = intval($uid);
	$rt=$db->query("SELECT senduid FROM ".__TBL_GZ__." WHERE uid=".$uid." AND flag=1");
	WHILE ($rows = $db->fetch_array($rt)){
		$senduid = $rows[0];
		$db->SendTip($senduid,$title,$content);
	}
}

function push_friend_wx($uid,$CARR){
	global $db,$cook_uid;
	$uid = (!ifint($uid))?$cook_uid:$uid;
	$uid = intval($uid);
	$rt=$db->query("SELECT a.senduid,b.openid FROM ".__TBL_GZ__." a,".__TBL_USER__." b WHERE a.uid=".$uid." AND a.flag=1 AND a.senduid=b.id AND b.openid<>'' AND b.subscribe=1");
	WHILE ($rows = $db->fetch_array($rt)){
		$Fid     = $rows[0];
		$Fopenid = $rows[1];
		//发图片
		if(!empty($CARR['picurl'])){
			@wx_kf_sent($Fopenid,$CARR['picurl'],'image');
		}
		//发视频
		if(!empty($CARR['videourl'])){
			@wx_kf_sent($Fopenid,$CARR['videourl'],'video');
		}
		//发文本
		if(!empty($CARR['contentKF'])){
			$ret = @wx_kf_sent($Fopenid,$CARR['contentKF'],'text');
			$ret = json_decode($ret);//echo '<font color="#fff">'.$ret->errmsg.'</font>';
		}
		//模版通知
		if ($ret->errmsg != 'ok' && !empty($CARR['contentMB'])){
			$keyword1  = $CARR['contentMB'];
			$keyword3  = urlencode($_ZEAI['siteName']);
			$url       = $CARR['url'];
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$Fopenid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
	}
}

function wx_endurl($title,$url) {
	global $db,$cook_uid,$data_subscribe;
	if(ifint($cook_uid) && is_weixin()){
		if($data_subscribe!=1){
			$url = dataIO($url,'in');
			$row = $db->ROW(__TBL_WXENDURL__,"uid","uid=".$cook_uid);
			if ($row){
				$db->query("UPDATE ".__TBL_WXENDURL__." SET title='$title',url='$url' WHERE uid=".$cook_uid);
			}else{
				$db->query("INSERT INTO ".__TBL_WXENDURL__." (uid,title,url) VALUES ($cook_uid,'$title','$url')");
			}
		}
	}
}
function wx_endurl_tg($title,$url) {
	global $db,$cook_tg_uid,$data_subscribe;
	if(ifint($cook_tg_uid) && is_weixin()){
		if($data_subscribe!=1){
			$url = dataIO($url,'in');
			$row = $db->ROW(__TBL_WXENDURL__,"uid","uid=".$cook_tg_uid);
			if ($row){
				$db->query("UPDATE ".__TBL_WXENDURL__." SET title='$title',url='$url' WHERE uid=".$cook_tg_uid);
			}else{
				$db->query("INSERT INTO ".__TBL_WXENDURL__." (uid,title,url) VALUES ($cook_tg_uid,'$title','$url')");
			}
		}
	}
}
function getArrayMax($arr,$field,$minmax){
    foreach ($arr as $k=>$v){$temp[]=$v[$field];}
	if($minmax=='max'){
		return max($temp);
	}else{
		return min($temp);
	}
}


function nodatatips($title='暂无内容',$kind='b',$C='') {
	switch ($kind) {
		case 'b':$class = 'nodatatips';break;
		case 's':$class = 'nodatatipsS';break;
		case 'm':$class = 'nodatatipsM';break;
	}
	return '<div class="'.$class.'"><i class="ico">&#xe61f;</i>'.$title.'<br>'.$C.'</div>';
}
function user_kind($kind) {
	if($kind==1){
	  $echo='线上';
	}elseif($kind==2){
	  $echo='线下';
	}elseif($kind==3){
	  $echo='均可';
	}elseif($kind==4){
	  $echo='机器人';
	}
	return $echo;
}



$star_ARR = array('1'=>'水瓶座','2'=>'双鱼座','3'=>'白羊座','4'=>'金牛座','5'=>'双子座','6'=>'巨蟹座','7'=>'狮子座','8'=>'处女座','9'=>'天秤座','10'=>'天蝎座','11'=>'射手座','12'=>'摩羯座');
$birthpet_ARR = array('1'=>'鼠','2'=>'牛','3'=>'虎','4'=>'兔','5'=>'龙','6'=>'蛇','7'=>'马','8'=>'羊','9'=>'猴','10'=>'鸡','11'=>'狗','12'=>'猪');
function getstar($birthday) {
	global $star_ARR;
	$star=starbirthpet($birthday);
	return  $star_ARR[$star[0]];
}
function getbirthpet($birthday) {
	global $birthpet_ARR;
	$birthpet=starbirthpet($birthday);
	return  $birthpet_ARR[$birthpet[1]];
}


function starbirthpet($date){
	$start = 1901;
	$YMD = explode('-',$date);
	$Y = $YMD[0];$M = $YMD[1];$D = $YMD[2];
	if ($M == 1 && $D >=20 || $M == 2 && $D <=18) {$value[0] = "1";}
	if ($M == 1 && $D >31) {$value[0] = "Huh? ";}
	if ($M == 2 && $D >=19 || $M == 3 && $D <=20) {$value[0] = "2";}
	if ($M == 2 && $D > 29) {$value[0] = "Say what? ";}
	if ($M == 3 && $D >=21 || $M == 4 && $D <=19) {$value[0] = "3";}
	if ($M == 3 && $D > 31) {$value[0] = "OK. Whatever. ";}
	if ($M == 4 && $D >=20 || $M == 5 && $D <=20) {$value[0] = "4";}
	if ($M == 4 && $D > 30) {$value[0] = "I 'm soooo sorry! ";}
	if ($M == 5 && $D >=21 || $M == 6 && $D <=21) {$value[0] = "5";}
	if ($M == 5 && $D > 31) {$value[0] = "Umm ... no. ";}
	if ($M == 6 && $D >=22 || $M == 7 && $D <=22) {$value[0] = "6";}
	if ($M == 6 && $D > 30) {$value[0] = "Sorry. ";}
	if ($M == 7 && $D >=23 || $M == 8 && $D <=22) {$value[0] = "7";}
	if ($M == 7 && $D > 31) {$value[0] = "Excuse me? ";}
	if ($M == 8 && $D >=23 || $M == 9 && $D <=22) {$value[0] = "8";}
	if ($M == 8 && $D > 31) {$value[0] = "Yeah. Right. ";}
	if ($M == 9 && $D >=23 || $M == 10 && $D <=22) {$value[0] = "9";}
	if ($M == 9 && $D > 30) {$value[0] = "Try Again. ";}
	if ($M == 10 && $D >=23 || $M == 11 && $D <=21) {$value[0] = "10";}
	if ($M == 10 && $D > 31) {$value[0] = "Forget it! ";}
	if ($M == 11 && $D >=22 || $M == 12 && $D <=21) {$value[0] = "11";}
	if ($M == 11 && $D > 30) {$value[0] = "Invalid $D ";}
	if ($M == 12 && $D >=22 || $M == 1 && $D <=19) {$value[0] = "12";}
	if ($M == 12 && $D > 31) {$value[0] = "No way! ";}
	$x = ($start - $Y) % 12;
	if ($x == 1 || $x == -11) {$value[1] = "1";}
	if ($x == 0) {$value[1] = "2";}
	if ($x == 11 || $x == -1) {$value[1] = "3";}
	if ($x == 10 || $x == -2) {$value[1] = "4";}
	if ($x == 9 || $x == -3) {$value[1] = "5";}
	if ($x == 8 || $x == -4) {$value[1] = "6";}
	if ($x == 7 || $x == -5) {$value[1] = "7";}
	if ($x == 6 || $x == -6) {$value[1] = "8";}
	if ($x == 5 || $x == -7) {$value[1] = "9";}
	if ($x == 4 || $x == -8) {$value[1] = "10";}
	if ($x == 3 || $x == -9) {$value[1] = "11";}
	if ($x == 2 || $x == -10) {$value[1] = "12";}  
	return $value;//返回数组，0为星座,1为属相
}
function dzh_getcontent($birthday,$heigh,$pay,$edu) {
	global $birthpet_ARR;
	$pay = udata('pay',$pay);	
	$edu = udata('edu',$edu);	
	if (!empty($birthday) && $birthday!='0000-00-00'){
		$age = getage($birthday);
		$birthdayYEAR = substr($birthday,0,4);
		$starbirthpet = starbirthpet($birthday);
		$starbirthpet = $birthpet_ARR[$starbirthpet[1]];
	}
	$word1 = array('Hi，','Hello，','嗨，','^_^','：）','你好，','你好~','问声好，');
	$word2 = array('很高兴认识你，','能认识一下吗？','我先自我介绍一下吧','我先做个自我介绍吧：）','认识一下吧，','希望可以认识一下，','希望我们可以认识一下，');
	$word3 = (empty($birthday) || $birthday=='0000-00-00')?'':array('我生于'.$birthdayYEAR.'年，','我出生在'.$birthdayYEAR.'年，','我'.$age.'岁，','我是'.$birthdayYEAR.'年生日，','我'.$birthdayYEAR.'年出生的，');
	$word4 = (empty($edu))?'':array('最高学历'.$edu.'，','学历'.$edu.'，',$edu.'学历，');
	$word5 = (empty($birthday) || $birthday=='0000-00-00')?'':array('属'.$starbirthpet.'，','属相是'.$starbirthpet.'，');
	$word6 = (empty($pay))?'':array('月收入'.$pay.'，','工资'.$pay.'，');
	$word7 = (empty($heigh))?'':array('身高'.$heigh.'cm，','身高'.$heigh.'厘米，','个头'.$heigh.'厘米，');
	$word8 = array('如果觉得我还不错，给我回信吧~','如果觉得我还不错，希望我们可以进一步了解。','如果觉得我还不错，希望你能给我回信。','如果你觉得我的条件还符合，给我回信吧~','我们蛮有缘分的，希望你能给我回信。','如果你觉得我的条件还符合，希望你能回信。','如果你感觉我还不错，希望我们可以进一步了解。','如果你感觉我还不错，希望可以进一步接触。','我觉得我们挺合适的，希望你能回信。','我觉得我们挺合适的，请给我回信吧。');
	$word1 = $word1[array_rand($word1)];
	$word2 = $word2[array_rand($word2)];
	
	$word3 = (empty($birthday) || $birthday=='0000-00-00')?'':$word3[array_rand($word3)];
	$word4 = (empty($edu))?'':$word4[array_rand($word4)];
	$word5 = (empty($birthday) || $birthday=='0000-00-00')?'':$word5[array_rand($word5)];
	$word6 = (empty($pay))?'':$word6[array_rand($word6)];
	$word7 = (empty($heigh))?'':$word7[array_rand($word7)];
	
	$word8 = $word8[array_rand($word8)];

	$dzh_num = dzh_randnum(1,'123456');
	$randword_arr = array();
	$wordlist = '';
	for($i=1;$i<=$dzh_num;$i++) {
		$randword = dzh_randnum(1,'4567');
		if (in_array($randword,$randword_arr))continue;
		$word_tmp  = 'word'.$randword;
		$wordlist .= $$word_tmp;
		$randword_arr[] = $randword;
	}
	$dzh_word2 = dzh_randnum(1,'1234');
	$word2 = ($dzh_word2 != 1)?$word2:'';
	$dzh_content = $word1.$word2.$word3.$wordlist.$word8;
	return $dzh_content;
}
function dzh_randnum($length,$list) {
	$str = "";
	while(strlen($str) < $length) $str .= substr($list, (rand() % strlen($list)), 1);
	return($str);
}
function iflogin() {
	global $cook_uid,$db,$cook_pwd;
	if (  !ifint($cook_uid) || empty($cook_uid) || !isset($cook_uid) )return false;
	$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$cook_uid." AND (flag=1 OR flag=-2) AND pwd='".$cook_pwd."'");
	if ($db->num_rows($rt)){
		return true;
	}else{
		return false;
	}
}

/*function iframe_login($jumpurl) {
	exit("<html><body><script>window.onload = function (){parent.location.href='".HOST."/p1/login.php?jumpurl=".urlencode($jumpurl)."';}</script></body></html>");
}
*/
function msg_reset($arr) {
	$newarr = array();
	foreach ($arr as $V) {
		$chknew = msg_Ifrpt($V['senduid'],$newarr);
		if ($chknew !== false){
			if($V['id'] > $chknew['id']){
				$newarr[$chknew['k']]['id']      = $V['id'];
				$newarr[$chknew['k']]['content'] = $V['content'];
			}
		}else{
			$newarr[] = $V;
		}
	}
	return $newarr;
}
function msg_Ifrpt($str,$ARR) {
	foreach ($ARR as $k=>$V) {
		if($str == $V['senduid'])return array('key'=>$k,'id'=>$V['id'],'content'=>$V['content']);
	}
	return false;
}
function trimContact($str){
    $find = array("微信","qq",'QQ');
    $replace = array('**','**','**','**');
    $str = str_replace($find, $replace, $str);
    $pattern = '/[0123456789]{1}\d{5}\d*/';
    preg_match_all($pattern, $str, $matches, PREG_OFFSET_CAPTURE);
    if (!empty($matches[0][0])) {
        $len = strlen($str);
        foreach ($matches[0] as $k => $v) {
            $sub = strlen($str) - $len; // 最新长度和初始长度比较
            $a_str = '******'; // 替换字符串长度
            $start = $v[1] + $sub; // 出现的位置
            $lenth = strlen($v[0]);
            $str = substr_replace($str, $a_str, $start, $lenth);
        }
    }
    return $str;
}
//身份证验证
function calcIDCardCode($IDCardBody) {
    if (strlen($IDCardBody) != 17) {
        return false;
    }
    //加权因子 
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值 
    $code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($IDCardBody); $i++) {
        $checksum += substr($IDCardBody, $i, 1) * $factor[$i];
    }
    return $code[$checksum % 11];
}
// 18位身份证校验码有效性检查 
function ifsfz($IDCard) {
    if (strlen($IDCard) != 18) {
        return false;
    }
    $IDCardBody = substr($IDCard, 0, 17); //身份证主体
    $IDCardCode = strtoupper(substr($IDCard, 17, 1)); //身份证最后一位的验证码
    if (calcIDCardCode($IDCardBody) != $IDCardCode) {
        return false;
    } else {
        return true;
    }
}
function seo_area_out($kind='',$areakey_='') {
	global $_INDEX,$areakey;
	if(!empty($_INDEX['seo_area'])){
		$seo_area = json_decode($_INDEX['seo_area'],true);
		if(@is_array($seo_area)){
			if($kind=='li' && !empty($areakey_)){
				return seo_area_li($seo_area,$areakey_);
			}
			$C='';
			foreach($seo_area as $V){
				$k    = $V['key'];
				$title= trimhtml($V['title']);
				$url  = trimhtml($V['url']);
				$ed=($V['key'] == $areakey)?' class="ed"':'';
				$C.='<a href="'.HOST.'/'.$V['key'].'/"'.$ed.'>'.$title.'</a>';
			}
			return $C;
		}
	}
	return false;
}
function seo_area_li($seo_area,$keyli){
	foreach($seo_area as $v){
		if($v['key'] == $keyli){
			return $v;
		}
	}
	return false;
}
function rz_data_info($var,$kind) {
	switch ($var){
		case 'identity':$t = '实名认证';$i='&#xea2e;';$c='#FD787B';break;
		case 'photo':$t = '真人认证';$i='&#xe645;';$c='#54A791';break;
		case 'mob':$t = '手机认证';$i='&#xe627;';$c='#4FA7FF';break;
		case 'car':$t = '购车认证';$i='&#xe6b4;';$c='#BC0D27';break;
		case 'house':$t = '住房认证';$i='&#xe7a0;';$c='#f70';break;
		case 'qq':$t = 'QQ认证';$i='&#xe630;';$c='#12B7F5';break;
		case 'email':$t = '邮箱认证';$i='&#xe641;';$c='#fc0';break;
		case 'edu':$t = '学历认证';$i='&#xe6c0;';$c='#12D87A';break;
		case 'weixin':$t = '微信认证';$i='&#xe607;';$c='#31C93C';break;
		case 'love':$t = '婚况认证';$i='&#xe62f;';$c='#FD66B5';break;
		case 'pay':$t = '收入认证';$i='&#xe61a;';$c='#a06d1c';break;
	}
	switch ($kind){
		case 'title':return $t;break;
		case 'ico':return $i;break;
		case 'color':return $c;break;
	}
}
function mate_diy_par($id,$kind='title') {
	global $_ZEAI;
	$mate_diy_px = json_decode($_ZEAI['mate_diy_px'],true);
	if($kind =='ifmate' || $kind =='ext'){
		if (count($mate_diy_px) >= 1 && is_array($mate_diy_px)){
			foreach ($mate_diy_px as $k=>$V){if($mate_diy_px[$k]['id']==$id)return $mate_diy_px[$k][$kind];}
		}
	}else{
		switch ($id){
			case 'age':return '年龄范围';break;
			case 'areaid':return '工作地区';break;
			case 'areaid2':return '户籍地区';break;
			case 'heigh':return '身高范围';break;
			case 'weigh':return '体重范围';break;
			case 'edu':return '最低学历';break;
			case 'pay':return '最低月薪';break;
			case 'love':return '婚况';break;
			case 'job':return '职业';break;
			case 'car':return '车子';break;
			case 'house':return '房子';break;
			case 'child':return '子女';break;
			case 'marrytime':return '结婚时间';break;
			case 'companykind':return '单位';break;
			case 'smoking':return '吸烟';break;
			case 'drink':return '饮酒';break;
		}
	}
	return '';
}

function mate_diy_SQL() {
	global $_ZEAI,$mate_age1,$mate_age2,$mate_heigh1,$mate_heigh2,$mate_weigh1,$mate_weigh2,$mate_pay,$mate_edu,$mate_love,$mate_car,$mate_house,$mate_areaid,$mate_job,$mate_child,$mate_marrytime,$mate_companykind,$mate_smoking,$mate_drink,$mate_areaid2;
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	$SQL = "";
	if (count($mate_diy) >= 1 && is_array($mate_diy)){
		foreach ($mate_diy as $k=>$V) {
			$ifmate = mate_diy_par($V,'ifmate');
			$ext    = mate_diy_par($V,'ext');
			if($ifmate!=1)continue;
			$mate_data = 'mate_'.$V;$mate_data=$$mate_data;
			switch ($ext) {
				case 'radio':
					if(ifint($mate_data)){
						if($V=='pay' || $V=='edu'){
							$SQL .= " AND ($V >= $mate_data) ";
						}else{
							$SQL .= " AND $V=$mate_data ";
						}
					}
				break;
				case 'checkbox':if(!empty($mate_data))$SQL .= " AND $V in ($mate_data) ";break;
				case 'radiorange':
					$tmp1 = 'mate_'.$V.'1';$tmp2 = 'mate_'.$V.'2';
					$mate_data1 = intval($$tmp1);$mate_data2 = intval($$tmp2);
					if($V=='age'){
						if (ifint($mate_age1))$SQL .= " AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= $mate_data1 ) ";
						if (ifint($mate_age2))$SQL .= " AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <= $mate_data2 ) ";
					}else{
						if (ifint($mate_data1))$SQL .= " AND ($V >= $mate_data1) ";
						if (ifint($mate_data2))$SQL .= " AND ($V <= $mate_data2) ";
					}
				break;
				case 'area':
					if (!empty($mate_data)){
						$DQ=explode(',',$mate_data);
						if(ifint($DQ[0]) && ifint($DQ[1]) && ifint($DQ[2])){
							$ifsql=true;
							$mate_dataNEW=$DQ[0].','.$DQ[1].','.$DQ[2];
						}elseif(ifint($DQ[0]) && ifint($DQ[1])){
							$ifsql=true;
							$mate_dataNEW=$DQ[0].','.$DQ[1];
						}elseif(ifint($DQ[0])){
							$ifsql=true;
							$mate_dataNEW=$DQ[0];
						}
						if($ifsql){
							if($V=='areaid2'){
								$SQL .= " AND area2id LIKE '%".$mate_dataNEW."%' ";
							}else{
								$SQL .= " AND areaid LIKE '%".$mate_dataNEW."%' ";
							}
						}
					}
				break;
			}
		}
	}
	return $SQL;
}
function set_data_ed_bfb($uid){
	global $db,$_ZEAI;
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	$mate_num = count($mate_diy);
	$mate_fld = "";
	if ($mate_num>0){
		$mate_fld = array();
		foreach ($mate_diy as $k=>$V) {
			$ext = mate_diy_par($V,'ext');
			switch ($ext) {
				case 'radiorange':
					$tmp1 = 'mate_'.$V.'1';$tmp2 = 'mate_'.$V.'2';
					$mate_fld[] = $tmp1;$mate_fld[] = $tmp2;
				break;
				default:$mate_fld[] = 'mate_'.$V;break;
			}
		}
		$mate_fld = (is_array($mate_fld))?implode(',',$mate_fld):'';
	}
	if(!empty($mate_fld))$mate_fld=",".$mate_fld;
	$fld="sex,nickname,aboutus,birthday,photo_s,areaid,love,heigh,weigh,edu,pay,house,car,job,marrytype,marrytime,child,blood,nation,area2id,companykind,smoking,drink,mob,weixin,qq".$mate_fld;
	$n=explode(',',$fld);$n=count($n);
	$row = $db->ROW(__TBL_USER__,$fld,"id=".$uid,"num");
	if ($row){
		$j=0;
		foreach ($row as $v){if (!empty($v) && $v!='0000-00-00')$j++;}
		$data_ed = round($j/$n*100);
		if ($data_ed > 100)$data_ed = 100;
		$db->query("UPDATE ".__TBL_USER__." SET myinfobfb=".$data_ed." WHERE id=".$uid);
	}
}
function AddLog($c) {
	global $db,$session_uname,$session_kind,$session_agentid,$session_agenttitle,$uid,$tguid,$tg_uid;
	$kind=($session_kind=='crm')?2:1;$c=dataIO($c,'in',2000);$uid=intval($uid);$tguid=(ifint($tg_uid))?$tg_uid:intval($tguid);
	$db->query("INSERT INTO ".__TBL_LOG__."(username,kind,content,addtime,agentid,agenttitle,uid,tguid) VALUES ('$session_uname',$kind,'$c',".ADDTIME.",$session_agentid,'$session_agenttitle',$uid,$tguid)");
}
function Mnavbtm_info($id,$k) {
	global $_ZEAI;
	$ARR = json_decode($_ZEAI['Mnavbtm'],true);
	if (count($ARR) >= 1 && is_array($ARR)){
		foreach ($ARR as $V) {
			if($V['id']==$id){
				switch ($k) {
					case 'title':return $V['title'];break;
					case 'var':return $V['var'];break;
					case 'url':return $V['url'];break;
					case 'path1':return $V['path1'];break;
					case 'path2':return $V['path2'];break;
				}
			}
		}
	}
	return false;
}
function nav_info($id,$k,$arr='') {
	global $_ZEAI;
	$A = (!empty($arr) && count($arr) >= 1 && is_array($arr))?$arr:json_decode($_ZEAI['navdiy'],true);
	if (count($A) >= 1 && is_array($A)){
		foreach ($A as $V) {
			if($V['i']==$id){
				switch ($k) {
					case 'i':return $V['i'];break;
					case 't':return urldecode($V['t']);break;
					case 'url':return urldecode($V['url']);break;
					case 'url2':return urldecode($V['url2']);break;
					case 'var':return urldecode($V['var']);break;
					case 'img':return $V['img'];break;
					case 'img2':return $V['img2'];break;
					case 'f':return $V['f'];break;
				}
			}
		}
	}
	return false;
}
function shuffle_arr($array) {
    if (!is_array($array) || empty($array)) return $array;
    $keys = array_keys($array);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key){$random[$key] = $array[$key];}
    return $random;
}
function zeai_cj_cleanhtml($str,$tags='<img><p><br><center><span>'){
	$search = array(
		'@<script[^>]*?>.*?</script>@si',
/*      '@<[\/\!]*?[^<>]*?>@si',*/
/*		'@<style[^>]*?>.*?</style>@siU', */
		'@<![\s\S]*?--[ \t\n\r]*>@'// Strip multi-line comments including CDATA 
	); 
	$str = preg_replace($search, '', $str);
	$str = strip_tags($str,$tags);
		//$str = preg_replace('/style=".*?"/i', '', $str);
	$str=preg_replace('/ class="([^\"]*)"/isU','',$str);
	$str=str_replace("data-src","src",$str);
	$str = preg_replace('/data-ratio=".*?"/i', '', $str);
	$str = preg_replace('/data-type=".*?"/i', '', $str);
	$str = preg_replace('/data-w=".*?"/i', '', $str);
	$str = preg_replace('/data-width=".*?"/i', '', $str);
	$str = preg_replace('/data-s=".*?"/i', '', $str);
	$str = preg_replace('/data-croporisrc=".*?"/i', '', $str);
	$str = preg_replace('/data-cropx1=".*?"/i', '', $str);
	$str = preg_replace('/data-cropx2=".*?"/i', '', $str);
	$str = preg_replace( '/(<img.*?)(style=.+?[\'|"])|((width)=[\'"]+[0-9]+[\'"]+)|((height)=[\'"]+[0-9]+[\'"]+)/i', '$1' , $str); 
	$str = preg_replace( '/(<p.*?)(style=.+?[\'|"])|((width)=[\'"]+[0-9]+[\'"]+)|((height)=[\'"]+[0-9]+[\'"]+)/i', '$1' , $str); 
	//$str =preg_replace('/<([^\s]+)[^>]*>/','<$1>',$str);
	//$str=str_replace("https:/","https://",$str);
	return $str;
}
function Zeai_pplAD($p,$kind='p') {
	global $_INDEX;;
	$ARR = json_decode($_INDEX['zeai_pplAD'],true);
	if (count($ARR) >= 1 && is_array($ARR)){foreach ($ARR as $V) {
		if($kind=='i'){
			if($V['i']==$p){return array('img'=>$V['img'],'url'=>$V['url'],'p'=>$V['p']);}
		}else{
			if($V['p']==$p && !empty($V['img'])){return array('img'=>$V['img'],'url'=>$V['url']);}
		}
	}}
	return '';
}
function zeai_chk_ugrade($uid){
	if(!ifint($uid))return false;
	global $db;
	$row = $db->ROW(__TBL_USER__,"sjtime,if2,grade,nickname,openid","grade>1 AND id=".$uid,"num");
	if ($row){
		$sjtime= $row[0];$if2= $row[1];$grade= $row[2];$nickname= dataIO($row[3],'out');$openid= $row[4];
		if (!empty($sjtime)){
			$d1  = ADDTIME;
			$d2  = $sjtime + $if2*30*86400;
			$ddiff = $d2-$d1;
			if ($ddiff < 0 && $if2 != 999){
				$timestr2 = ',<font class="Caaa">已过期</font>';
				$db->query("UPDATE ".__TBL_USER__." SET grade=1,sjtime=0,if2=0 WHERE id=".$uid);
				$C = $nickname.'您好，您的VIP会员已过期~~ 为了避免你与另一半擦肩而过，建议你尽快充值和升级　<a href="'.Href('vip').'" class=aQING>立即升级</a>';
				$db->SendTip($uid,'您的VIP资格已过期，请速续费',dataIO($C,'in'),'sys');
				if (!empty($openid)){
					//$content = urlencode($C);
					//$ret = @wx_kf_sent($openid,$C,'text');
					//$ret = json_decode($ret);
					//if ($ret->errmsg != 'ok'){
						$first     = urlencode($nickname.',您的'.utitle($grade).'已过期！');
						$keyword1  = urlencode('降级为最低等级');
						$keyword3  = urlencode('系统自动执行');
						$remark    = urlencode('为了避免你与另一半擦肩而过，建议你尽快充值和升级');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('vip')));
					//}
				}
			}
		}
	}
}
function grade_time($arr) {
	$url=$arr['url'];
	$grade=$arr['grade'];
	$gradetitle=$arr['gradetitle'];
	$d1=$arr['d1'];
	$d2=$arr['d2'];
	$ifA=$arr['ifA'];
	$T=$arr['T'];
	$WH=$arr['WH'];
	switch ($grade) {
		default:$btncls  = " class='aHUI'";break;
		case 1:$btncls   = " class='aBAI'";break;
		case 2:$btncls   = " class='aLAN'";break;
		case 3:$btncls   = " class='aTUHAO'";break;
		case 4:$btncls   = " class='aJIN'";break;
		case 5:$btncls   = " class='aFEN'";break;
		case 6:$btncls   = " class='aHONG'";break;
		case 7:$btncls   = " class='aLV'";break;
		case 8:$btncls   = " class='aHUANG'";break;
		case 9:$btncls   = " class='aZI'";break;
		case 10:$btncls  = " class='aQINGed'";break;
	}
	if($ifA=='noAno__'){
		if(!empty($gradetitle) && $gradetitle!='--'){
			return "<a ".$btncls.">".$gradetitle."</a>";
		}else{
			return'';	
		}
	}
	if($ifA == 'btn_djs' || $ifA == 'btn'){
		$onclick = " title=\"点击修改\" onClick=\"zeai.iframe(".$T.",".$url.",".$WH.");\"";
	}else{
		$onclick = "";
		$ifA = 'btn_djs';
	}
	$ret = ($ifA == 'btn_djs' || $ifA == 'btn')?"<a href=\"javascript:;\" ".$onclick.$btncls.">".$gradetitle."</a>":"";
	if (!empty($d1) && !empty($d2) && ($ifA == 'btn_djs' || $ifA == 'djs')){
		$ddiff = $d2-$d1;
		if ($ddiff < 0){
			$ret .= '<br><em>';
			$ret .= '<font class="Cf00 B">已过期</font>';
			$ret .= '<br>过期日：'.YmdHis($d2,'Ymd');
			$ret .= '</em>';
		} else {
			$tmpday   = intval($ddiff/86400);
			$ret .= '<br><em>';
			$ret .= '还剩<font class="Cf00">'.$tmpday.'</font>天';
			$ret .= '<br>到期日：'.YmdHis($d2,'Ymd');
			$ret .= '</em>';
		}
	}
	$ret = '<span class="grade_time">'.$ret.'</span>';
	return $ret;
}
function shopgrade($grade,$kind,$size=''){
	global $_ZEAI,$_SHOP;
	$a = json_decode($_SHOP['shopgradearr']);
	foreach($a as $v){
		if($grade==$v->g){
			switch ($kind) {
				case 't':$r=dataIO($v->t,'out');break;
				case 'ico':$r = $v->ico;break;
				case 'img':$r = '<img class="ugrade'.$size.'" src="'.$_ZEAI['up2'].'/'.$v->ico.'" title="'.dataIO($v->t,'out').'">';break;
			}
			return $r;
		}
	}
	return $r;
}
function shopkindtitle($id){
	global $_SHOP;
	$arr = json_decode($_SHOP['kindarr']);
	foreach($arr as $A){if($id==$A->i)return $A->v;}
	return '';
}
function shop_chk_u() {
	global $_ZEAI,$db,$cook_uid,$cook_pwd,$currfields;
	if(!ifint($cook_uid) && empty($cook_pwd))return false;
	$md = empty($currfields)?"":",".$currfields;
	$row = $db->ROW(__TBL_USER__,"id".$md,"id=".$cook_uid." AND pwd='$cook_pwd' ","name");
	if ($row){return $row;}else{return false;}
}
function shop_chk_tgu($ukind) {
	global $_ZEAI,$db,$cook_tg_uid,$cook_tg_pwd,$currfieldstg,$cook_uid,$cook_openid;
	switch ($ukind) {
		case 'tg_uid':
			if(!ifint($cook_tg_uid))return false;
			$SQL="id=".$cook_tg_uid." AND pwd='$cook_tg_pwd'";
		break;
		case 'uid':
			if(!ifint($cook_uid))return false;
			$SQL="uid=".$cook_uid;
		break;
		case 'tg_openid':
			if(empty($cook_tg_openid))return false;
			$SQL="openid='$cook_tg_openid'";
		break;
		case 'openid':
			if(empty($cook_openid))return false;
			$SQL="openid='$cook_openid'";
		break;
	}
	$md = empty($currfieldstg)?"":",".$currfieldstg;
	$rowtg = $db->ROW(__TBL_TG_USER__,"id,uname,pwd,shopgrade,shopflag,openid".$md,$SQL,'name');
	if ($rowtg){
		setcookie("cook_tg_uid",$rowtg['id'],time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_uname",$rowtg['uname'],time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_pwd",$rowtg['pwd'],time()+720000,"/",$_ZEAI['CookDomain']);
		if(!empty($rowtg['openid'])){
			setcookie("cook_tg_openid",$rowtg['openid'],time()+720000,"/",$_ZEAI['CookDomain']);
		}
		return $rowtg;
	}
	return false;
}
function shop_chk() {
	$rowtg = shop_chk_tgu('tg_uid');
	if(!$rowtg)$rowtg = shop_chk_tgu('uid');
	if(!$rowtg)$rowtg = shop_chk_tgu('tg_openid');
	if(!$rowtg)$rowtg = shop_chk_tgu('openid');
	return $rowtg;
}
function Zeai_ajax_list_fn($totalP,$p) {
	global $_ZEAI,$db,$nodatatips,$ZEAI_SELECT;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);
	if($totalP==0 && $p == 1)return $nodatatips;
	if ($p > $totalP)exit("end");
	$LIMIT = ($p == 1)?$_ZEAI['pagesize']:($p-1)*$_ZEAI['pagesize'].",".$_ZEAI['pagesize'];
	$rt = $db->query($ZEAI_SELECT." LIMIT ".$LIMIT);
	$total = $db->num_rows($rt);
	$rows_ulist='';
	for($n=1;$n<=$total;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_ulist .= rows_ulist($rows,$p);
	}
	return $rows_ulist;
}
function arrT($arrstr,$id,$vkind='v'){
	$arr = json_decode($arrstr);
	foreach($arr as $A){if($id==$A->i){
		$tv=(!empty($vkind))?$vkind:'v';
		$v=$A->$tv;$c=$A->c;
		if(!empty($c)){
			return '<font style="color:'.$c.'">'.$v.'</font>';
		}else{
			return $v;
		}
	}}
	return '';
}
function hidetext($str,$kind='mob') {
	switch ($kind) {
		case 'mob':$o = substr($str,0,3).'****'.substr($str,7,4);break;
	}
	return $o;
}
function zeai_djs($jzbmtime) {
	$d1  = ADDTIME;
	$d2  = $jzbmtime;
	$totals  = ($d2-$d1);
	$day     = intval( $totals/86400 );
	$hour    = intval(($totals % 86400)/3600);
	$hourmod = ($totals % 86400)/3600 - $hour;
	$minute  = intval($hourmod*60);
	if (($totals) > 0) {
		$tmp='<span>还剩</span>';
		if ($day > 0){
			$outtime = $tmp."<span class=timestyle>$day</span>天";
		} else {
			$outtime = $tmp;
		}
		if($hour>0)$outtime .= "<span class=timestyle>$hour</span>小时";
		$outtime .= "<span class=timestyle>$minute</span>分钟";
	} else {
		return false;
	}
	return $outtime;
}
?>
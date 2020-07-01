<?php 
/***************************************************
作者: 郭余林　QQ:797311 (supdes)
***************************************************/
function up_send_admindel($dbpicname){
	global $_ZEAI;
	$url = $_ZEAI['up2'].'/up.php';
	$data = array (
		'submitok' => 'admindelpic',
		'uu' => $_SESSION["adminid"],
		'pp' => $_SESSION["password"],
		'dbpicname' => $dbpicname
	);
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );
	print_r($return);
}
function up_send_userdel($dbpicname){
	global $_ZEAI,$cook_uid,$cook_pwd;
	$url = $_ZEAI['up2'].'/up.php';
	$data = array (
		'submitok' => 'userdelpic',
		'uid' => $cook_uid,
		'password' => $cook_pwd,
		'dbpicname' => $dbpicname
	);
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );
	//$ret = json_decode($return, true);return $ret['V'];
}
function user_rename_pic_send($dbpicname1,$newdir){
	global $_ZEAI,$cook_uid,$cook_pwd;
	$url = $_ZEAI['up2'].'/up.php';
	$data = array (
		'submitok' => 'user_rename_pic',
		'uid' => $cook_uid,
		'password' => $cook_pwd,
		'dbpicname1' => $dbpicname1,
		'newdir' => $newdir
	);
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );
	//print_r($return);	
}

function up_send_ewm($dbname,$ewmurl){
	global $_ZEAI;
	$url = $_ZEAI['up2'].'/up.php';
	$data = array (
		'submitok' => 'ewm_create',
		'dbname' => $dbname,
		'ewmurl' => $ewmurl
	);
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );
	//print_r($return);	
	//return $return;
}
function setewmdbname($photodir,$pre='',$uid=''){
	global $ADDTIME;
	$pre = (!empty($pre))?$pre:'';
	$dbdir = $photodir.'/'.date('Y').'/'.date('m').'/';
	if (!empty($uid)){
		$dbname = $dbdir.$pre.$uid.'.png';
	}else{
		$dbname = $dbdir.$pre.$ADDTIME.cdnumletters(3).'.png';
	}
	return $dbname;
}

function up_send_wx($file,$dbname,$waterimg=0,$smallsize='',$bigsize='',$middlesize='',$filetype='photo'){
	global $_ZEAI;
	if(!empty($file)){
		if($filetype == 'audio'){$dbname= str_replace('v/','tmp/',$dbname);}
		$url  = $_ZEAI['up2'].'/up.php?filename='.$dbname.'&filetype='.$filetype;
		$url .= "&waterimg=$waterimg&bigsize=$bigsize&smallsize=$smallsize";
		$ret = Zeai_POST_stream($url,$file);
		$ret = json_decode($ret,true);
		return true;
	}else{  
		return false;  
	}  
}

function get_wx_datastream($serverIds){
	$server_token = get_wx_access_token();
	if (empty($server_token))callmsg("网络不给力,请重试","");
	$datastream = get_contents("http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$server_token."&media_id=".$serverIds);
	return $datastream;
}
function setvideodbname($videodir,$tmp_name,$pre='',$uid='',$extname=''){  
	global $ADDTIME;
	$pre = (!empty($pre))?$pre:'';
	$dbdir = $videodir.'/'.date('Y').'/'.date('m').'/';
	if (!empty($uid)){
		$dbname = $dbdir.$pre.$uid.'.'.$extname;
	}else{
		$dbname = $dbdir.$pre.$ADDTIME.cdnumletters(3).'.'.$extname;
	}
	return $dbname;
}
function up_send($file,$dbname,$waterimg=0,$smallsize='',$bigsize='',$middlesize='',$filetype='photo'){
	global $_ZEAI;
	if ($filetype == 'photo'){
		$maxMB = $_ZEAI['UpMaxMB'];
		$title = '照片';
	}elseif($filetype == 'video'){
		$maxMB = $_ZEAI['UpVmaxMB'];
		$title = '视频';
		$dbname= str_replace('v/','tmp/',$dbname);
	}elseif($filetype == 'audio'){
		$maxMB = $_ZEAI['UpAmaxMB'];
		$title = '语音';
		$dbname= str_replace('v/','tmp/',$dbname);
	}
	$max_file_size=1024000*$maxMB;
	if($max_file_size < $file["size"])callmsg($title.'太大，不得超过'.$maxMB.'M，请检查!',"-1");
	$tmp_name = $file["tmp_name"];
	$url  = $_ZEAI['up2'].'/up.php?filename='.$dbname.'&filetype='.$filetype;
	$url .= "&waterimg=$waterimg&bigsize=$bigsize&smallsize=$smallsize&middlesize=$middlesize";
	if(file_exists($tmp_name)){
		$tmp_name = file_get_contents($tmp_name);
		if(!empty($tmp_name)){
			$ret = Zeai_POST_stream($url,$tmp_name);
			$ret = json_decode($ret, true);
			//return $ret;
			return true;
		}else{
			return false;
		}
	}else{  
		return false;
	}  
}  
function setphotodbname($photodir,$tmp_name,$pre='',$uid=''){  
	global $ADDTIME;
	if ($tmp_name == 'weixin'){
		$ftype = 'jpg';
	}else{
		if (!ifpic($tmp_name)){
			callmsg('照片类型不符，请检查',"-1");
		}else{
			$ftype = getpicextname($tmp_name);
		}
	}
	$pre = (!empty($pre))?$pre:'';
	$dbdir = $photodir.'/'.date('Y').'/'.date('m').'/';
	if (!empty($uid)){
		$dbname = $dbdir.$pre.$uid.'.'.$ftype;
	}else{
		$dbname = $dbdir.$pre.$ADDTIME.cdnumletters(3).'.'.$ftype;
	}
	return $dbname;
}
function setpath_s($ds){
	$ds_len = strlen($ds);
	$ds_r   = substr($ds,-4);
	$s = substr($ds,0,$ds_len-4)."_s".$ds_r;
	return $s;
}
function setpath_b($ds){
	$ds_len = strlen($ds);
	$ds_r   = substr($ds,-4);
	$b = substr($ds,0,$ds_len-4)."_b".$ds_r;
	return $b;
}
function ifpostpic($file){
	if($_SERVER['REQUEST_METHOD']=='POST' && is_uploaded_file($file)){ 
		return true;
	}else{
		return false;	
	}
}
/***************************************************
作者: 郭余林　QQ:797311 (supdes)
***************************************************/
?>
<?php 
/***************************************************
版权所有@作者: 郭余林　QQ:797311 (supdes)
***************************************************/
$upurl = $_ZEAI['up2'].'/up.php';
function up_adm_rename_pic($dbpicname1,$dbpicname2){
	global $_ZEAI,$upurl;
	$data = array (
		'submitok' => 'adm_rename_pic',
		'uu' => $_SESSION["admuid"],
		'pp' => $_SESSION["admpwd"],
		'dbpicname1' => $dbpicname1,
		'dbpicname2' => $dbpicname2
	);
	Zeai_POST_stream($upurl,$data);
}
function up_check_file_exists($dbfile){
	global $_ZEAI,$upurl;
	$data = array (
		'submitok' => 'adminfileexists',
		'uu' => $_SESSION["admuid"],
		'pp' => $_SESSION["admpwd"],
		'dbfile' => $dbfile
	);
	return Zeai_POST_stream($upurl,$data);
}
function up_send_admindel($dbpicname){
	global $_ZEAI,$upurl;
	$data = array (
		'submitok' => 'admindelpic',
		'uu' => $_SESSION["admuid"],
		'pp' => $_SESSION["admpwd"],
		'dbpicname' => $dbpicname
	);
	Zeai_POST_stream($upurl,$data);
}
function up_send_userdel($dbname,$submitok='userdelpic'){
	global $_ZEAI,$cook_uid,$cook_pwd,$upurl,$cook_tg_uid,$cook_tg_pwd;
	if($submitok=='tg_userdelpic'){
		$uid=$cook_tg_uid;
		$pwd=$cook_tg_pwd;
	}else{
		$uid=$cook_uid;
		$pwd=$cook_pwd;
	}
	$data = array (
		'submitok' => $submitok,
		'uid' => $uid,
		'pwd' => $pwd,
		'dbpicname' => $dbname
	);
	Zeai_POST_stream($upurl,$data);
}
function user_rename_pic_send($dbpicname1,$newdir){
	global $_ZEAI,$cook_uid,$cook_pwd,$upurl;
	$data = array (
		'submitok' => 'user_rename_pic',
		'uid' => $cook_uid,
		'pwd' => $cook_pwd,
		'dbpicname1' => $dbpicname1,
		'newdir' => $newdir
	);
	Zeai_POST_stream($upurl,$data);
}
function u_pic_reTmpDir_send($dbname,$newdir,$submitok='u_pic_reTmpDir'){
	global $cook_uid,$cook_pwd,$upurl,$cook_tg_uid,$cook_tg_pwd;
	if($submitok=='u_pic_reTmpDir_tg'){
		$uid=$cook_tg_uid;
		$pwd=$cook_tg_pwd;
	}elseif($submitok=='u_pic_reTmpDir_guest'){
	}else{
		$uid=$cook_uid;
		$pwd=$cook_pwd;
	}
	$data = array (
		'submitok' => $submitok,
		'uid' => $uid,
		'pwd' => $pwd,
		'dbname' => $dbname,
		'newdir' => $newdir
	);
	Zeai_POST_stream($upurl,$data);
}
function adm_pic_reTmpDir_send($dbname,$newdir){
	global $upurl;
	$data = array (
		'submitok' => 'adm_pic_reTmpDir',
		'uu' => $_SESSION["admuid"],
		'pp' => $_SESSION["admpwd"],
		'dbname' => $dbname,
		'newdir' => $newdir
	);
	Zeai_POST_stream($upurl,$data);
}
function adm_u_pic_reTmpDir_send($dbname,$newdir){
	global $upurl,$cook_admid,$cook_admpwd;
	$data = array (
		'submitok' => 'adm_pic_reTmpDir',
		'uu' => $cook_admid,
		'pp' => $cook_admpwd,
		'dbname' => $dbname,
		'newdir' => $newdir
	);
	Zeai_POST_stream($upurl,$data);
}



function up_send_ewm($dbname,$ewmurl){
	global $_ZEAI,$upurl;
	$data = array (
		'submitok' => 'ewm_create',
		'dbname' => $dbname,
		'ewmurl' => $ewmurl
	);
	Zeai_POST_stream($upurl,$data);
}
function setewmdbname($photodir,$pre='',$uid=''){
	$pre = (!empty($pre))?$pre:'';
	$dbdir = $photodir.'/'.date('Y').'/'.date('m').'/';
	if (!empty($uid)){
		$dbname = $dbdir.$pre.$uid.'.png';
	}else{
		$dbname = $dbdir.$pre.ADDTIME.cdnumletters(3).'.png';
	}
	return $dbname;
}

function up_send_stream($file,$dbname,$waterimg=0,$smallsize='',$bigsize='',$middlesize='',$filetype='photo'){
	global $_ZEAI,$upurl;
	if(!empty($file)){
			if($filetype == 'audio'){$dbname= str_replace('v/','tmp/',$dbname);}
		$url  = $upurl.'?filename='.$dbname.'&filetype='.$filetype;
		$url .= "&waterimg=$waterimg&bigsize=$bigsize&smallsize=$smallsize&middlesize=$middlesize";
		$ret = Zeai_POST_stream($url,$file);
		$ret = json_decode($ret,true);
		return true;
	}else{  
		return false;  
	}  
}




function wx_get_up($dir,$url,$uid,$mode='SB'){
	global $_ZEAI,$_UP;
	$file   = get_contents($url,10);
	$dbname = setphotodbname($dir,'wx_picstream',$uid);
	if ($mode=='B'){
		@up_send_stream($file,$dbname,0,$_UP['upBsize']);
	}elseif($mode=='SB'){
		@up_send_stream($file,$dbname,$_UP['waterimg'],$_UP['upSsize'],$_UP['upBsize']);
	}elseif($mode=='SMB'){
		@up_send_stream($file,$dbname,$_UP['waterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']);
	}
	return $dbname;
}
function wx_get_uinfo_logo_tmp($headimgurl,$uid){
	global $_ZEAI,$_UP;
	$file      = get_contents($headimgurl,10);
	$dbpicname = setphotodbname('tmp','wx_picstream',$uid.'_');
	@up_send_stream($file,$dbpicname,0,$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']);
	return $dbpicname;
}

function up_send_urlstream($url,$pathinfo='tmp'){
	global $_UP;
	$ext=explode('.',$url);$ext=end($ext);
	$file   = get_contents($url,10);
	$dbname = setphotodbname($pathinfo,'stream13301457728'.$ext,'zeai_stream');
	$ZEAIup = up_send_stream($file,$dbname,0,$_UP['upBsize']);
	if(!$ZEAIup)return false;
	return $dbname;
}

function get_wx_datastream($serverIds){
	$server_token = wx_get_access_token();
	if (empty($server_token))json_exit(array('flag'=>0,'msg'=>'网络不给力,请重试'));
	$datastream = get_contents("http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$server_token."&media_id=".$serverIds);
	return $datastream;
}

function setVideoDBname($videodir,$uid,$extname){  
	return 'p/'.$videodir.'/'.date('Y').'/'.date('m').'/'.$uid.ADDTIME.cdnumletters(3).'.'.$extname;
}



function up_send($file,$dbname,$waterimg=0,$smallsize='',$bigsize='',$middlesize='',$filetype='photo'){
	global $_ZEAI,$_UP,$upurl;
	if ($filetype == 'photo'){
		$maxMB = $_UP['upMaxMB'];
		$title = '照片';
	}elseif($filetype == 'video'){
		$maxMB = $_UP['upVMaxMB'];
		$title = '视频';
		//$dbname= str_replace('v/','tmp/',$dbname);
	}elseif($filetype == 'audio'){
		$maxMB = $_UP['upAmaxMB'];
		$title = '语音';
		//$dbname= str_replace('v/','tmp/',$dbname);
	}
	$max_file_size=1024000*$maxMB;
	if($max_file_size < $file["size"])json_exit(array('flag'=>0,'msg'=>$title.'太大，不得超过'.$maxMB.'M，请检查'));

	$tmp_name = $file["tmp_name"];
	$url  = $upurl.'?filename='.$dbname.'&filetype='.$filetype;
	$url .= "&waterimg=$waterimg&bigsize=$bigsize&smallsize=$smallsize&middlesize=$middlesize";
	if(file_exists($tmp_name)){
		$tmp_content = file_get_contents($tmp_name);
		if(!empty($tmp_content)){
			$ret = Zeai_POST_stream($url,$tmp_content);
			//$ret = json_decode($ret,true);
			return $ret;
		}else{
			return false;
		}
	}else{  
		return false;
	}  
}





function setphotodbname($photodir,$tmp_name,$pre='',$uid=''){  
	if ($tmp_name == 'wx_picstream'){
		$ftype = 'jpg';
	}elseif(stripos($tmp_name,'stream') !== false){
		$ftype = explode('13301457728',$tmp_name);
		$ftype = $ftype[1];
	}else{
		if (!ifpic($tmp_name)){
			return false;
		}else{
			$ftype = getpicextname($tmp_name);
		}
	}
	//$pre = (!empty($pre))?$pre:'';
	$dbdir = 'p/'.$photodir.'/'.date('Y').'/'.date('m').'/';
	if (!empty($uid)){
		$dbname = $dbdir.$pre.$uid.'.'.$ftype;
	}else{
		$dbname = $dbdir.$pre.ADDTIME.cdnumletters(3).'.'.$ftype;
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
	if(is_uploaded_file($file)){ 
		return true;
	}else{
		return false;	
	}
}
//批量list
function tmp_piclist_modupdate($form,$data,$dir,$iflist='list') {
	if($iflist=='alone'){
		//提交空，数据库有，删老
		if(empty($form) && !empty($data)){
			@up_send_admindel($data.'|'.smb($data,'m').'|'.smb($data,'b'));
		//提交有，数据库无
		}elseif(!empty($form) && empty($data)){
			//上新
			adm_pic_reTmpDir_send($form,$dir);
			adm_pic_reTmpDir_send(smb($form,'m'),$dir);
			adm_pic_reTmpDir_send(smb($form,'b'),$dir);
			@up_send_admindel(smb($form,'blur'));
			$form = str_replace('tmp',$dir,$form);
		//提交有，数据库有
		}elseif(!empty($form) && !empty($data)){
			//有改动
			if($form != $data){
				//删老
				@up_send_admindel($data.'|'.smb($data,'m').'|'.smb($data,'b'));
				//上新
				adm_pic_reTmpDir_send($form,$dir);
				adm_pic_reTmpDir_send(smb($form,'m'),$dir);
				adm_pic_reTmpDir_send(smb($form,'b'),$dir);
				@up_send_admindel(smb($form,'blur'));
				$form = str_replace('tmp',$dir,$form);
			}
		}
	}elseif($iflist=='list'){
		//提交空，数据库有，删老
		if(empty($form) && !empty($data)){
			$ARR=explode(',',$data);
			foreach ($ARR as $S){@up_send_admindel($S.'|'.smb($S,'m').'|'.smb($S,'b'));}
		//提交有，数据库无
		}elseif(!empty($form) && empty($data)){
			//上新
			$ARR=explode(',',$form);
			$form=array();
			foreach ($ARR as $V) {
				adm_pic_reTmpDir_send($V,$dir);
				@adm_pic_reTmpDir_send(smb($V,'m'),$dir);
				adm_pic_reTmpDir_send(smb($V,'b'),$dir);
				@up_send_admindel(smb($V,'blur'));
				$_s = str_replace('tmp',$dir,$V);
				$form[] = $_s;
			}
			$form = implode(',',$form);
		//提交有，数据库有
		}elseif(!empty($form) && !empty($data)){
			//有改动
			if($form != $data){
				$ARR = explode(',',$form);
				$form = array();
				//循环新列表
				foreach ($ARR as $V) {
					//新上传，上新
					if(strstr($V,'/tmp/')){
						adm_pic_reTmpDir_send($V,$dir);
						adm_pic_reTmpDir_send(smb($V,'m'),$dir);
						adm_pic_reTmpDir_send(smb($V,'b'),$dir);
						@up_send_admindel(smb($V,'blur'));
						$_s = str_replace('tmp',$dir,$V);
						$form[]=$_s;
					//老图，直接赋值
					}else{$form[]=$V;}
				}
				$form = implode(',',$form);
				//循环老库，处理多图被删除的部分
				$ARR2=explode(',',$data);
				foreach ($ARR2 as $V2) {
					//不在新列表，删之
					if(!in_array($V2,$ARR))@up_send_admindel($V2.'|'.smb($V2,'m').'|'.smb($V2,'b'));
				}
			}
		}
	}
	return $form;
}
/***************************************************
作者: 郭余林　QQ:797311 (supdes)
***************************************************/
?>
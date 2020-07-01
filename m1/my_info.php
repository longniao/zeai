<?php
require_once '../sub/init.php';
$currfields = 'grade,openid,photo_s,photo_f,RZ,myinfobfb,dataflag,weixin_pic,nickname';
$a=($a=='mate')?'data':$a;

$$rtn='json';$chk_u_jumpurl=HOST.'/?z=my&e=my_info&a='.$a.'&i='.$i;require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/udata.php';$extifshow = json_decode($_UDATA['extifshow'],true);
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'sub/zeai_up_func.php';
$data_grade  = $row['grade'];
$data_RZ=$row['RZ'];$RZarr=explode(',',$data_RZ);
$data_photo_s= $row['photo_s'];
$data_myinfobfb= $row['myinfobfb'];
$data_dataflag= $row['dataflag'];
$data_nickname= $row['nickname'];
$urole = json_decode($_ZEAI['urole']);
$video_num=json_decode($_VIP['video_num']);
$photo_num=json_decode($_VIP['photo_num']);
$switch=json_decode($_ZEAI['switch'],true);
$data_weixin_pic=$row['weixin_pic'];
$switchdataflag = ($switch['sh']['moddata_'.$data_grade] == 1)?1:0;
//
switch ($submitok) {
	case 'ajax_photo_num':
		$totalnum = $db->COUNT(__TBL_PHOTO__,"uid=".$cook_uid);
		json_exit(array('flag'=>1,'totalnum'=>$totalnum));
	break;
	case 'ajax_weixin_pic_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('photo',$file['tmp_name'],$cook_uid.'_wxpic_');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$db->query("UPDATE ".__TBL_USER__." SET weixin_pic='$dbname' WHERE id=".$cook_uid);
			@up_send_userdel($data_weixin_pic);
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_weixin_pic_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_up('weixin',$url,$cook_uid.'_315_','B');
				}
				$newpic = $_ZEAI['up2']."/".$dbname;
				if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				$db->query("UPDATE ".__TBL_USER__." SET weixin_pic='$dbname' WHERE id=".$cook_uid);
				@up_send_userdel($data_weixin_pic);
				json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
		}
	break;
	case 'ajax_weixin_pic_up_app':
		$file=$_FILES['file'];
			$dbname = setphotodbname('photo',$file['tmp_name'],$cook_uid.'_wxpic_');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$db->query("UPDATE ".__TBL_USER__." SET weixin_pic='$dbname' WHERE id=".$cook_uid);
			@up_send_userdel($data_weixin_pic);
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
	break;
	case 'ajax_nickname':
		/*if (!empty($value))*/Dmod("nickname='".dataIO($value,'in')."',dataflag=".$switchdataflag);
		setcookie("cook_nickname",urldecode(dataIO($value,'out')),null,"/",$_ZEAI['CookDomain']);
		jsonOutAndBfb(1);
	break;
	case 'ajax_aboutus':
		/*if (str_len($value) > 1)*/Dmod("aboutus='".dataIO($value,'in',2000)."',dataflag=".$switchdataflag);
		jsonOutAndBfb(1);
	break;
	case 'ajax_areaid':
		if (!empty($areaid))Dmod("areaid='".dataIO($areaid,'in')."'");
		if (!empty($areatitle))Dmod("areatitle='".dataIO($areatitle,'in')."'");
		jsonOutAndBfb(1);break;
	case 'ajax_area2id':
		if (!empty($areaid))Dmod("area2id='".dataIO($areaid,'in')."'");
		if (!empty($areatitle))Dmod("area2title='".dataIO($areatitle,'in')."'");
		jsonOutAndBfb(1);break;
	case 'ajax_birthday':if (ifdate($value))Dmod("birthday='".$value."'");jsonOutAndBfb(1);break;
	case 'ajax_love':if (ifint($value))Dmod("love=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_edu':if (ifint($value))Dmod("edu=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_pay':if (ifint($value))Dmod("pay=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_house':if (ifint($value))Dmod("house=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_car':if (ifint($value))Dmod("car=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_heigh':if (ifint($value))Dmod("heigh=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_weigh':if (ifint($value))Dmod("weigh=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_job':if (ifint($value))Dmod("job=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_marrytype':if (ifint($value))Dmod("marrytype=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_marrytime':if (ifint($value))Dmod("marrytime=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_blood':if (ifint($value))Dmod("blood=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_child':if (ifint($value))Dmod("child=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_nation':if (ifint($value))Dmod("nation=".intval($value));jsonOutAndBfb(1);break;
	case 'ajax_tag'.$cook_sex:if (!empty($value))Dmod("tag='".$value."'");jsonOutAndBfb(0);break;
	case 'ajax_truename':if (!empty($value))Dmod("truename='".dataIO($value,'in',12)."'");jsonOutAndBfb(0);break;
	case 'ajax_identitynum':if (!empty($value))Dmod("identitynum='".dataIO($value,'in',20)."'");jsonOutAndBfb(0);break;
	case 'ajax_address':if (!empty($value))Dmod("address='".dataIO($value,'in',100)."'");jsonOutAndBfb(1);break;
	case 'ajax_weixin':
		/*if (!empty($value))*/Dmod("weixin='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);
	break;
	case 'ajax_qq':
		if (strstr($data_RZ,'qq'))json_exit(array('flag'=>0,'msg'=>'已认证不可更改'));
		/*if (!empty($value))*/Dmod("qq='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);
	break;
	case 'ajax_email':
		if (strstr($data_RZ,'email'))json_exit(array('flag'=>0,'msg'=>'已认证不可更改'));
		if (!empty($value))Dmod("email='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);
	break;
	case 'ajax_mob':
		if (strstr($data_RZ,'mob'))json_exit(array('flag'=>0,'msg'=>'已认证不可更改'));
		if (!ifmob($value))json_exit(array('flag'=>0,'msg'=>'请输入正确的手机号'));
		$row = $db->ROW(__TBL_USER__,'id',"uname='".$value."' OR (mob='$value' AND FIND_IN_SET('mob',RZ)) ");
		if($row)json_exit(array('flag'=>0,'msg'=>'此手机已被占用,请重新输入'));
		Dmod("mob='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);
	break;
	//my.php新
	case 'ajax_photo_s_up_h5':
		if (@in_array('photo',$RZarr))json_exit(array('flag'=>0,'msg'=>'【'.rz_data_info('photo','title').'】已认证不可更改'));
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('m',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$shFlag = $switch['sh']['photom_'.$data_grade];
			$photo_f  = ($shFlag == 1)?1:0;
			$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
			$path_b = getpath_smb($data_photo_s,'b');
			@up_send_userdel($data_photo_s.'|'.getpath_smb($data_photo_s,'m').'|'.$path_b.'|'.str_replace("_b.","_blue.",$path_b));
			json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));}
	break;
	case 'ajax_photo_s_up_wx':
		if (@in_array('photo',$RZarr))json_exit(array('flag'=>0,'msg'=>'【'.rz_data_info('photo','title').'】已认证不可更改'));
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_uinfo_logo($url,$cook_uid);$_s = setpath_s($dbname);
				}
				$newphoto_s = $_ZEAI['up2']."/".$_s;
				if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				$shFlag = $switch['sh']['photom_'.$data_grade];
				$photo_f = ($shFlag == 1)?1:0;
				$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
				$path_b = getpath_smb($data_photo_s,'b');
				@up_send_userdel($data_photo_s.'|'.getpath_smb($data_photo_s,'m').'|'.$path_b.'|'.str_replace("_b.","_blue.",$path_b));
				json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
			}
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));}
	break;
	
	case 'ajax_photo_s_up_app':
		$file=$_FILES['file'];
		if (@in_array('photo',$RZarr))json_exit(array('flag'=>0,'msg'=>'【'.rz_data_info('photo','title').'】已认证不可更改'));
		//if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('m',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$shFlag = $switch['sh']['photom_'.$data_grade];
			$photo_f  = ($shFlag == 1)?1:0;
			$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
			$path_b = getpath_smb($data_photo_s,'b');
			@up_send_userdel($data_photo_s.'|'.getpath_smb($data_photo_s,'m').'|'.$path_b.'|'.str_replace("_b.","_blue.",$path_b));
			json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
		//}else{json_exit(array('flag'=>0,'msg'=>'zeai_app_name_error','path'=>$file['tmp_name']));}
		exit;
	break;
	case 'ajax_photo_up_app':
		$file=$_FILES['file'];
		$chkok=chkVPmaxNumFlag('photo');
		if ($chkok){
			$dbname = setphotodbname('photo',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s = setpath_s($dbname);
			$newpic = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));

			$flag  = ($switch['sh']['photo_'.$data_grade] == 1)?1:0;

			$db->query("INSERT INTO ".__TBL_PHOTO__." (uid,path_s,flag,addtime) VALUES ($cook_uid,'$_s',$flag,".ADDTIME.")");
			json_exit(array('flag'=>1,'msg'=>'上传成功'));//,'data_num'=>$data_num
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error','path'=>$file['tmp_name'],'chkok'=>$chkok));
		}
		exit;
	break;
	case 'ajax_video_up_app':
		$file=$_FILES['file'];
		$f_name=$_FILES['file']['name'];
		$extname=substr($f_name,-3);
		$chkok=chkVPmaxNumFlag('video');
		if ($chkok){
			$dbname = setVideoDBname('v',$cook_uid.'_',$extname);
			if (!up_send($file,$dbname,'WWW','ZEAI','CN','supdesQQ797311','video')){
				json_exit(array('flag'=>0,'msg'=>'zeai_move_video_error'));
			}else{
				$_s = str_replace('.'.$extname,'.jpg',$dbname);
				$flag  = ($switch['sh']['video_'.$data_grade] == 1)?1:0;
				$db->query("INSERT INTO ".__TBL_VIDEO__." (uid,path_s,flag,addtime) VALUES ($cook_uid,'$_s',$flag,".ADDTIME.")");
				json_exit(array('flag'=>1,'msg'=>'上传成功','ps'=>$dbname,'tp'=>$extname));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	
//	case 'ajax_mate_age':Dmod("mate_age1=".intval($i1));Dmod("mate_age2=".intval($i2));jsonOutAndBfb(1);break;
//	case 'ajax_mate_heigh':Dmod("mate_heigh1=".intval($i1));Dmod("mate_heigh2=".intval($i2));jsonOutAndBfb(1);break;
//	case 'ajax_mate_pay':Dmod("mate_pay=".intval($value));jsonOutAndBfb(1);break;
//	case 'ajax_mate_edu':Dmod("mate_edu=".intval($value));jsonOutAndBfb(1);break;
//	case 'ajax_mate_love':Dmod("mate_love=".intval($value));jsonOutAndBfb(1);break;
//	case 'ajax_mate_house':Dmod("mate_house=".intval($value));jsonOutAndBfb(1);break;
//	case 'ajax_mate_area':Dmod("mate_areaid='".dataIO($areaid,'in',50)."',mate_areatitle='".dataIO($areatitle,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_age':Dmod("mate_age1=".intval($i1));Dmod("mate_age2=".intval($i2));jsonOutAndBfb(1);break;
	case 'ajax_mate_heigh':Dmod("mate_heigh1=".intval($i1));Dmod("mate_heigh2=".intval($i2));jsonOutAndBfb(1);break;
	case 'ajax_mate_weigh':Dmod("mate_weigh1=".intval($i1));Dmod("mate_weigh2=".intval($i2));jsonOutAndBfb(1);break;
	case 'ajax_mate_pay':Dmod("mate_pay='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_edu':Dmod("mate_edu='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_love':Dmod("mate_love='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_car':Dmod("mate_car='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_house':Dmod("mate_house='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_child':Dmod("mate_child='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_marrytime':Dmod("mate_marrytime='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_companykind':Dmod("mate_companykind='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_smoking':Dmod("mate_smoking='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_drink':Dmod("mate_drink='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_job':Dmod("mate_job='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_areaid':Dmod("mate_areaid='".dataIO($areaid,'in',50)."',mate_areatitle='".dataIO($areatitle,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_areaid2':Dmod("mate_areaid2='".dataIO($areaid,'in',50)."',mate_areatitle2='".dataIO($areatitle,'in',50)."'");jsonOutAndBfb(1);break;
//	
	case 'ajax_photo_up_h5':
		$chkok=chkVPmaxNumFlag('photo');
		if (ifpostpic($file['tmp_name'])  && $chkok){
			$dbname = setphotodbname('photo',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s = setpath_s($dbname);
			$newpic = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			
			$flag  = ($switch['sh']['photo_'.$data_grade] == 1)?1:0;
			
			$db->query("INSERT INTO ".__TBL_PHOTO__." (uid,path_s,flag,addtime) VALUES ($cook_uid,'$_s',$flag,".ADDTIME.")");
			json_exit(array('flag'=>1,'msg'=>'上传成功'));//,'data_num'=>$data_num
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_photo_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$curTotalN = count($serverIds);
			$chkok  = chkVPmaxNumFlag('photo',($curTotalN-1));
			if ($curTotalN >= 1 && $chkok){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_up('photo',$url,$cook_uid.'_','SB');
					$_s = setpath_s($dbname);
					$newpic = $_ZEAI['up2']."/".$_s;
					if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
					$flag  = ($switch['sh']['photo_'.$data_grade] == 1)?1:0;
					$db->query("INSERT INTO ".__TBL_PHOTO__." (uid,path_s,flag,addtime) VALUES ($cook_uid,'$_s',$flag,".ADDTIME.")");
				}
				json_exit(array('flag'=>1,'msg'=>'上传成功'));//,'data_num'=>$data_num
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
		}
	break;
	case 'ajax_photo_del':
		if (ifint($id)){
			$row = $db->ROW(__TBL_PHOTO__,"path_s","uid=".$cook_uid." AND id=".$id,"num");
			if ($row){
				$path_s = $row[0];$path_b = getpath_smb($path_s,'b');
				@up_send_userdel($path_s.'|'.$path_b);
				$db->query("DELETE FROM ".__TBL_PHOTO__." WHERE id=".$id);
				json_exit(array('flag'=>1));
			}
		}
	break;
	case 'ajax_video_up':
		$chkok=chkVPmaxNumFlag('video');
		if (ifpostpic($file['tmp_name'])  && $chkok){
			$dbname = setVideoDBname('v',$cook_uid.'_',$extname);
			if (!up_send($file,$dbname,'WWW','ZEAI','CN','supdesQQ797311','video')){
				json_exit(array('flag'=>0,'msg'=>'zeai_move_video_error'));
			}else{
				$_s = str_replace('.'.$extname,'.jpg',$dbname);
				$flag  = ($switch['sh']['video_'.$data_grade] == 1)?1:0;
				$db->query("INSERT INTO ".__TBL_VIDEO__." (uid,path_s,flag,addtime) VALUES ($cook_uid,'$_s',$flag,".ADDTIME.")");
				json_exit(array('flag'=>1,'msg'=>'上传成功'));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_video_del':
		if (ifint($id)){
			$row = $db->ROW(__TBL_VIDEO__,"path_s","uid=".$cook_uid." AND id=".$id,"num");
			if ($row){
				$path_s = $row[0];$path_b = str_replace('.jpg','.mp4',$path_s);
				@up_send_userdel($path_s.'|'.$path_b);
				$db->query("DELETE FROM ".__TBL_VIDEO__." WHERE id=".$id);
				json_exit(array('flag'=>1));
			}
		}
	break;
	/*ExtData*/
	case 'ajax_'.substr($submitok,5,50):
		$objstr = substr($submitok,5,50);
		if (!empty($value) && in_extData($objstr)){
			$sql="";
			if(in_extData($objstr,'subkind') == 1)$sql=",dataflag=".$switchdataflag;
			Dmod("$objstr='".dataIO($value,'in',100)."'".$sql);
		}
		if(in_extData($objstr)){
			jsonOutAndBfb(0);
		}
	break;
}
function chkVPmaxNumFlag($type,$curTotalN=0){
	global $db,$_VIP,$cook_uid,$data_grade;
	if ($type=='video'){
		$cfgARR=$_VIP['video_num'];
		$tbname=__TBL_VIDEO__;
		$dw='个';
	}elseif($type=='photo'){
		$cfgARR=$_VIP['photo_num'];
		$tbname=__TBL_PHOTO__;
		$dw='张';
	}
	$NUM=json_decode($cfgARR,true);$cfgMaxnum = $NUM[$data_grade];
	$data_num = $db->COUNT($tbname,"uid=".$cook_uid);
	if (($curTotalN+$data_num)>=$cfgMaxnum){json_exit(array('flag'=>0,'msg'=>utitle($data_grade).'最多上传'.$cfgMaxnum.$dw));}else{
		return true;
	}
}

//
$data_openid = $row['openid'];
$data_photo_f= $row['photo_f'];
$switch = json_decode($_ZEAI['switch'],true);
$urole  = json_decode($_ZEAI['urole']);
$tg = json_decode($_REG['tg'],true);
$a  = (empty($a))?'data':$a;
$curpage = 'my_info';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
if (empty($data_photo_s)){
	$photo_s_url=HOST.'/res/photo_s'.$cook_sex.'.png';
}else{
	$photo_s_url=$_ZEAI['up2'].'/'.$data_photo_s;
}
//
/*************Ajax Start*************/
if ($submitok == 'ajax_data'){
/**************************基本资料*****************************/
	if (@count($extifshow) > 0 || is_array($extifshow)){
		foreach ($extifshow as $ev){$evARR[] = $ev['f'];}
		$EXTfields = ",".implode(",",$evARR);
	}
	$row = $db->NAME($cook_uid,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2,aboutus,nickname,birthday,areaid,areatitle,area2id,area2title,love,heigh,weigh,edu,job,pay,house,car,nation,child,blood,tag,marrytype,marrytime,truename,identitynum,mob,address,weixin,weixin_pic,qq,email".$EXTfields);
	if ($row){
		$mate_age1      = intval($row['mate_age1']);
		$mate_age2      = intval($row['mate_age2']);
		$mate_heigh1    = intval($row['mate_heigh1']);
		$mate_heigh2    = intval($row['mate_heigh2']);
		$mate_pay       = $row['mate_pay'];
		$mate_edu       = $row['mate_edu'];
		$mate_areaid    = $row['mate_areaid'];
		$mate_areatitle = $row['mate_areatitle'];
		$mate_love      = $row['mate_love'];
		$mate_car       = $row['mate_car'];
		$mate_house     = $row['mate_house'];
		$mate_weigh1      = intval($row['mate_weigh1']);
		$mate_weigh2      = intval($row['mate_weigh2']);
		$mate_job         = $row['mate_job'];
		$mate_child       = $row['mate_child'];
		$mate_marrytime   = $row['mate_marrytime'];
		$mate_companykind = $row['mate_companykind'];
		$mate_smoking     = $row['mate_smoking'];
		$mate_drink       = $row['mate_drink'];
		$mate_areaid2     = $row['mate_areaid2'];
		$mate_areatitle2  = $row['mate_areatitle2'];
		//
		$mate_age       = $mate_age1.','.$mate_age2;
		$mate_age_str   = mateset_out($mate_age1,$mate_age2,'岁');
		$mate_age_str = str_replace("不限","",$mate_age_str);
		$mate_heigh     = $mate_heigh1.','.$mate_heigh2;
		$mate_heigh_str = mateset_out($mate_heigh1,$mate_heigh2,'cm');
		$mate_heigh_str = str_replace("不限","",$mate_heigh_str);
		$mate_weigh     = $mate_weigh1.','.$mate_weigh2;
		$mate_weigh_str = mateset_out($mate_weigh1,$mate_weigh2,'kg');
		$mate_weigh_str = str_replace("不限","",$mate_weigh_str);
		$mate_areaid_str  = (!empty($mate_areatitle))?$mate_areatitle:'';
		$mate_areaid2_str = (!empty($mate_areatitle2))?$mate_areatitle2:'';
		$mate_pay_str   = udata('pay',$mate_pay);
		$mate_edu_str   = udata('edu',$mate_edu);
		$mate_love_str  = udata('love',$mate_love);
		$mate_car_str   = udata('car',$mate_car);
		$mate_house_str = udata('house',$mate_house);
		$mate_job_str         = udata('job',$mate_job);
		$mate_child_str       = udata('child',$mate_child);
		$mate_marrytime_str   = udata('marrytime',$mate_marrytime);
		$mate_companykind_str = udata('companykind',$mate_companykind);
		$mate_smoking_str     = udata('smoking',$mate_smoking);
		$mate_drink_str       = udata('drink',$mate_drink);
		$mate_age = ($mate_age != '0,0')?$mate_age:'23,40';
		$mate_heigh = ($mate_heigh != '0,0')?$mate_heigh:'160,175';
		//
		$aboutus    = dataIO($row['aboutus'],'wx');
		//$aboutus=(empty($aboutus))?'自我介绍（10~500字）':$aboutus;
		$birthday   = $row['birthday'];
		$nickname   = dataIO($row['nickname'],'out');
		$nickname   = urldecode($nickname);
		$areaid     = $row['areaid'];
		$areatitle  = $row['areatitle'];
		$area2id     = $row['area2id'];
		$area2title  = $row['area2title'];
		$heigh      = $row['heigh'];
		$weigh      = $row['weigh'];
		$love       = $row['love'];
		$edu        = $row['edu'];
		$job        = $row['job'];
		$pay        = $row['pay'];
		$house      = $row['house'];
		$car        = $row['car'];
		$nation     = $row['nation'];
		$child      = $row['child'];
		$blood      = $row['blood'];
		$tag        = $row['tag'];
		$marrytype  = $row['marrytype'];
		$marrytime  = $row['marrytime'];
		//truename,mob,address,weixin,qq,email
		$truename   = dataIO($row['truename'],'out');
		$identitynum= dataIO($row['identitynum'],'out');
		$address    = dataIO($row['address'],'out');
		$weixin     = dataIO($row['weixin'],'out');
		$weixin_pic = $row['weixin_pic'];
		$qq         = dataIO($row['qq'],'out');
		$email      = dataIO($row['email'],'out');
		$mob        = dataIO($row['mob'],'out');
		$mob=(!ifmob($mob))?'':$mob;
	}
	$row_ext = $row;
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	?>
	<style>
	.modlist ul li h4 b{color:#f00;font-size:18px;line-height:40px;display:inline-block;margin-right:5px}.RCW li{width:50%}
	</style>
    <div id="my_info_bfb" class="my_info_bfb<?php if ($data_myinfobfb >= 90){echo ' my_info_bfb_nb';}?>">
        <i id="my_info_bfbbar"></i><span>当前资料完整度<?php echo $data_myinfobfb;?>％</span>
    </div> 
	<script>
    var bfbbgW=my_info_bfb.offsetWidth,data_myinfobfb=<?php echo $data_myinfobfb;?>;
	bfbbgW = bfbbgW/100;
	var newbfbW= parseInt(bfbbgW*data_myinfobfb);
	my_info_bfbbar.style.width=newbfbW+'px';
    </script>
    <div class="modlist ">
    	<!--基本资料-->
    	<div class="lineSsquare"><div class="line BAI"></div><div class="title BAI ">基本<br>资料</div></div>
        <ul>
            <li onClick="<?php if(is_h5app()){ echo 'photo_s_app();';}else{ echo (!is_weixin())?'photo_s_h5(this);':'photo_s_wx(this);';}?>"><h4><b class="Cf00">*</b>头　　像<?php if (empty($data_photo_s)){?><font>（无相片会员首页不显示）</font><?php }?></h4><span><img src="<?php echo $photo_s_url;?>" class="sexbg<?php echo $cook_sex;?>"></span></li>
            <li id="nickname" class="<?php echo (str_len($data_nickname)<2)?'ipt':'none';?>" data="<?php echo $nickname;?>"><h4><b class="Cf00">*</b>昵　　称</h4><span><?php echo $nickname;?></span></li>
            <li id="birthday" class="<?php echo ($birthday=='0000-00-00' || empty($birthday))?'bthdy':'none';?>" data="<?php echo ($birthday=='0000-00-00')?'1992-01-15':$birthday;?>"><h4><b class="Cf00">*</b>生　　日</h4><span><?php echo str_replace("0000-00-00","",$birthday);?></span></li>
            <li id="areaid"><h4><b class="Cf00">*</b>工作地区</h4><span id="areatitle"><?php echo $areatitle;?></span></li>
            <li id="love" class="<?php echo (empty($love) || !in_array('love',$RZarr))?'slect':'none';?>" data="<?php echo $love;?>"><h4><b class="Cf00">*</b>婚姻状况</h4><span><?php echo udata('love',$love);if(in_array('love',$RZarr))echo "（已认证）";?></span></li>
            <li id="heigh" class="slect" data="<?php echo $heigh;?>"><h4><b class="Cf00">*</b>身　　高</h4><span><?php echo str_replace("0厘米","",udata('heigh',$heigh));;?></span></li>
            <li id="edu" class="<?php echo (in_array('edu',$RZarr))?'none':'slect';?>" data="<?php echo $edu;?>"><h4><b class="Cf00">*</b>学　　历</h4><span><?php echo udata('edu',$edu);if(in_array('edu',$RZarr))echo "（已认证）";?></span></li>
            <li id="pay" class="<?php echo (in_array('pay',$RZarr))?'none':'slect';?>" data="<?php echo $pay;?>"><h4><b class="Cf00">*</b>月 收 入</h4><span><?php echo udata('pay',$pay);if(in_array('pay',$RZarr))echo "（已认证）";?></span></li>
            <li id="job" class="slect" data="<?php echo $job;?>"><h4><b class="Cf00">*</b>职　　业</h4><span><?php echo udata('job',$job);?></span></li>
            <li id="house" class="<?php echo (in_array('house',$RZarr))?'none':'slect';?>" data="<?php echo $house;?>"><h4><b class="Cf00">*</b>住房情况</h4><span><?php echo udata('house',$house);if(in_array('house',$RZarr))echo "（已认证）";?></span></li>
            <li id="car" class="<?php echo (in_array('car',$RZarr))?'none':'slect';?>" data="<?php echo $car;?>"><h4><b class="Cf00">*</b>购车情况</h4><span><?php echo udata('car',$car);if(in_array('car',$RZarr))echo "（已认证）";?></span></li>
            <li id="marrytime" class="slect" data="<?php echo $marrytime;?>"><h4><b class="Cf00">*</b>期望结婚时间</h4><span><?php echo udata('marrytime',$marrytime);?></span></li>
        </ul>
        <!--择友要求-->
    	<br><div class="lineSsquare"><div class="line"></div><div class="title BAI S14">择偶<br>要求</div></div>
        <ul>
			<?php
            if (count($mate_diy) >= 1 && is_array($mate_diy)){
                foreach ($mate_diy as $k=>$V) {
					$cook_tmp1 = 'mate_'.$V;
					$cook_tmp2 = 'mate_'.$V.'_str';
					$cook_mate_data = $$cook_tmp1;
					$cook_mate_str  = $$cook_tmp2;
                    $ext = mate_diy_par($V,'ext');
                    switch ($ext) {
                        case 'radio':$class     = 'slect';break;
                        case 'checkbox':$class  = 'chckbox';break;
                        case 'radiorange':$class  = 'rang';break;
                        case 'area':$class   = 'aread';break;
                    }
				?>
                <li id="mate_<?php echo $V;?>" class="<?php echo $class;?>" data="<?php echo $cook_mate_data;?>"><h4><b class="Cf00">*</b><?php echo mate_diy_par($V);?></h4><span><?php echo $cook_mate_str;?></span></li>
            <?php }}?>
        </ul>
        <!--联系方式-->
        <br><div class="lineSsquare M"><div class="line"></div><div class="title BAI">联系<br>方式</div></div>
        <ul>
            <li id="mob" class="<?php echo (in_array('mob',$RZarr) && ifmob($mob))?'none':'ipt';?>" data="<?php echo $mob;?>"><h4><b class="Cf00">*</b>手　机</h4><span><?php echo $mob;if(in_array('mob',$RZarr))echo "（已认证）";?></span></li>
            <li id="weixin" class="<?php echo (in_array('weixin',$RZarr))?'none':'ipt';?>" data="<?php echo $weixin;?>"><h4><b class="Cf00">*</b>微信号</h4><span><?php echo $weixin;if(in_array('weixin',$RZarr))echo "（已点亮）";?></span></li>
            <li id="weixin_pic" <?php if(is_h5app()){?> onclick="weixin_pic_app_func()"<?php } ?>><h4>微信二维码</h4><span></span>
                <div class="wxpic" id="wxpic">
                <?php if (!empty($weixin_pic)){?>
                    <img src="<?php echo $_ZEAI['up2'].'/'.$weixin_pic;?>">
                <?php }else{ ?>
                    <p class="icoadd"><i class="ico">&#xe620;</i></p>
                 <?php }?>
                </div>
            </li>
            <li id="qq" class="<?php echo (strstr($data_RZ,'qq'))?'none':'ipt';?>" data="<?php echo $qq;?>"><h4>QQ号码</h4><span><?php echo $qq;?></span></li>
            <li style="display:none" id="email" class="<?php echo (strstr($data_RZ,'email'))?'none':'ipt';?>" data="<?php echo $email;?>"><h4>邮箱</h4><span><?php echo $email;?></span></li>
			<li style="display:none" id="truename" class="ipt" data="<?php echo $truename;?>"><h4>真实姓名<font>（替您保密，身份验证之用）</font></h4><span style="width:70px"><?php echo $truename;?></span></li>
			<li style="display:none" id="identitynum" class="ipt" data="<?php echo $identitynum;?>"><h4>身份证号<font>（替您保密，身份验证之用）</font></h4><span style="width:70px"><?php echo $identitynum;?></span></li>
			<li style="display:none" id="address" class="ipt" data="<?php echo $address;?>"><h4>地址<font>（替您保密，身份验证之用）</font></h4><span style="width:100px"><?php echo $address;?></span></li>
		</ul>
		<!--详细资料-->
        <br><div class="lineSsquare M"><div class="line"></div><div class="title BAI">详细<br>资料</div></div><ul>
        <ul class="textarea2">
            <li><h4>自我介绍</h4></li>
			<textarea class="textarea" id="aboutus" placeholder="自我介绍（10~500字）"><?php echo $aboutus;?></textarea>
		</ul>
        <ul>
            <li id="area2id"><h4>户籍地区</h4><span id="area2title"><?php echo $area2title;?></span></li>
            <li id="weigh" class="slect" data="<?php echo $weigh;?>"><h4>体重</h4><span><?php echo($weigh>0)?udata('weigh',$weigh):'';?></span></li>
            <li id="marrytype" class="slect" data="<?php echo $marrytype;?>"><h4>嫁娶形式</h4><span><?php echo udata('marrytype',$marrytype);?></span></li>
            <li id="child" class="slect" data="<?php echo $child;?>"><h4>子女情况</h4><span><?php echo udata('child',$child);?></span></li>
            <li id="blood" class="slect" data="<?php echo $blood;?>"><h4>血型</h4><span><?php echo udata('blood',$blood);?></span></li>
            <li id="tag<?php echo $cook_sex;?>" class="chckbox" data="<?php echo $tag;?>"><h4>我的标签</h4><span><?php echo checkbox_div_list_get_listTitle('tag'.$cook_sex,$tag);?></span></li>
            <li id="nation" class="slect" data="<?php echo $nation;?>"><h4>民族</h4><span><?php echo udata('nation',$nation);?></span></li>
		</ul>
        <?php if (@count($extifshow) > 0 || is_array($extifshow)){?>
        
        <?php
		foreach ($extifshow as $V) {
			$objstr = $V['f'];
			$data   = dataIO($row_ext[$objstr],'out');
			switch ($V['s']) {
				case 1:$Fkind = 'ipt';$span=$data;break;
				case 2:$Fkind = 'slect';$span=udata($V['f'],$data);break;
				case 3:$Fkind = 'chckbox';$span=checkbox_div_list_get_listTitle($V['f'],$data);break;
			}
			?>
            <li id="<?php echo $objstr;?>" class="<?php echo $Fkind;?>" data="<?php echo $data; ?>"><h4><?php echo $V['t'];?></h4><span><?php echo $span;?></span></li>
        <?php }?></ul><?php }?>
        
        <script>
        areaid.onclick=function(){
            ZeaiM.div_up({obj:areabox,h:360});
            ZEAI_area({areaid:'<?php echo $areaid;?>',areatitle:'<?php echo $areatitle;?>',ul:areabox.children[0],str:'job',end:function(z,e){
                areatitle.html(e);
                zeai.ajax({url:HOST+'/m1/my_info'+zeai.extname,data:{submitok:'ajax_areaid',areaid:z,areatitle:e}});
            }});
        }
        area2id.onclick=function(){
            ZeaiM.div_up({obj:areabox2,h:360});
            ZEAI_area({areaid:'<?php echo $area2id;?>',areatitle:'<?php echo $area2title;?>',ul:areabox2.children[0],str:'hj',datastr:'hj',end:function(z,e){
                area2title.html(e);
                zeai.ajax({url:HOST+'/m1/my_info'+zeai.extname,data:{submitok:'ajax_area2id',areaid:z,areatitle:e}});
            }});
        }
		
		<?php if (in_array('areaid',$mate_diy)){?>mate_areaid.setAttribute('data',defarea1);<?php }?>
		<?php if (in_array('areaid2',$mate_diy)){?>mate_areaid2.setAttribute('data',defarea2);<?php }?>
        </script>
        <br><br>
        <button type="button" class="btn size4 HONG B W85_" onClick="my_info_save();">保存资料</button>
        <br><br><br><br>
    </div>
    
    <?php
	if($data_dataflag!=1){
		switch ($data_dataflag) {
			case 2:$dtflag  = '<b class="flag2">审核被驳回，请重新修改</b>';break;
			case 0:$dtflag = '<b class="flag0">审核中</b>';break;
		}?>
		<style>
		#mini_title_myinfo b{font-size:12px;margin-left:5px;padding:2px 8px;font-weight:normal;border-radius:2px}
		#mini_title_myinfo b.flag2{background-color:#aaa}
		#mini_title_myinfo b.flag0{background-color:#f70}
        </style>
	<?php }?>
    <script src="<?php echo HOST;?>/m1/js/my_info.js?<?php echo $_ZEAI['cache_str'];?>"></script>
	<script>
	o('mini_title_myinfo').html('我的资料<?php echo $dtflag;?>');
	my_info_data({"aboutus":aboutus,"modlist":document.querySelector('.modlist').getElementsByTagName("li")});
	var href='<?php echo $href;?>';
	if(href=='contact'){my_info_submain.scrollTop=1090;}
	if(href=='mate'){my_info_submain.scrollTop=590;}
	<?php if(is_h5app()){ ?>
	function weixin_pic_app_func(){
		app_uploads({url:HOST+"/m1/my_info.php?submitok=ajax_weixin_pic_up_app",num:1},function(e){var rs=zeai.jsoneval(e);if (rs.flag == 1){wxpic.html('<img src='+up2+rs.dbname+'>');}  });}
	<?php }else{ ?>
	photoUp({
		btnobj:weixin_pic,
		url:HOST+"/m1/my_info"+zeai.extname,
		submitokBef:"ajax_weixin_pic_",
		_:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){wxpic.html('<img src='+up2+rs.dbname+'>');}
		}
	});<?php }?>
    </script>
<?php exit;}elseif($submitok == 'ajax_cert'){
/**************************诚信认证*****************************/
//$data_RZ='mob,identity,edu,car,house';
$RZarr=explode(',',$data_RZ);
function RZchkFlag($objstr,$RZarr) {
	global $cook_uid,$db;
	if (!in_array($objstr,$RZarr)){
		$doing = $db->COUNT(__TBL_RZ__,"uid=".$cook_uid." AND rzid='$objstr' AND flag=0");
		if ($doing>0)return '<h5>审核中</h5>';
	}else{
		return '';
	}
}
$rz_dataARR    = explode(',',$_ZEAI['rz_data']);
?>
	<ul class="my_cert " id="my_certbox">
    	<?php
		if (count($rz_dataARR) >= 1 && is_array($rz_dataARR)){
			foreach ($rz_dataARR as $k=>$V) {
			?>
        	<li id="<?php echo $V;?>" class="rz<?php echo (in_array($V,$RZarr))?' ed':'';?>"><i class="ico <?php echo $V;?>"><?php echo rz_data_info($V,'ico');?></i><h3><?php echo rz_data_info($V,'title');?></h3><?php echo RZchkFlag($V,$RZarr);?></li>
        <?php }}?>
        <div class="clear"></div>
        <div class="linebox"><div class="line W50"></div><div class="title S14 BAI" style="background-color:#fff">认证说明</div></div>
        <h5>认证项目越多诚信值越高，同时点亮对应的认证图标。</h5>
    </ul>
    <script>
	o('mini_title_myinfo').html('诚信认证');
	zeai.listEach(zeai.tag(my_certbox,'li'),function(obj){
		obj.onclick=function(){ZeaiM.page.load(HOST+'/m1/my_cert'+zeai.ajxext+'i='+obj.id,'my_info','my_cert_'+obj.id);}
	});
	<?php if (!empty($i)){?>
	ZeaiM.page.load(HOST+'/m1/my_cert'+zeai.ajxext+'i=<?php echo $i;?>&href=<?php echo $href;?>','my_info','my_cert_<?php echo $i;?>');
	<?php }?>
    </script>

<?php exit;}elseif($submitok == 'ajax_photo'){
/**************************我的相册*****************************/?>
	<div class="picli fadeInL" id="picli">
        <i class="add" id="btnadd"></i>
        <?php
		$rt=$db->query("SELECT id,path_s,flag FROM ".__TBL_PHOTO__." WHERE uid=".$cook_uid." ORDER BY id DESC");
		$total = $db->num_rows($rt);
		$data_photo_num = $total;
        if ($total > 0) {
			$pic_list = array();
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt);
                if(!$rows) break;
                $id     = $rows[0];
                $path_s = $rows[1];
                $flag   = $rows[2];
                $dst_s = $_ZEAI['up2'].'/'.$path_s;
                $dst_b = getpath_smb($dst_s,'b');
				$pic_list[]= $dst_b;
				$flagstr   = ($flag == 0)?'<span>审核中</span>':'';
                $list_str  = '';
                $list_str .= '<i id="li'.$id.'">';
                $list_str .= '<img src="'.$dst_s.'">'.$flagstr.'<b></b>';
                $list_str .= '</i>';
                echo $list_str;
            }
			$pic_list = encode_json($pic_list);
        }
		$PcfgMaxnum = $photo_num->$data_grade;
        ?>
    </div>
    <div id="p_vipHelp" class="helpDiv">
        <ul><?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
			$ifmy = ($data_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
            $out1 .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i> <font class="Cf00">'.$photo_num->$grade.'</font> 张'.$ifmy.'</li>';
        }echo $out1;
        ?></ul>
        <a class="btn size3 HUANG Mcenter block center" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.extname,'<?php echo $curpage;?>','my_vip');">我要升级会员</a>
    </div>
	<script src="<?php echo HOST;?>/m1/js/my_info.js?<?php echo $_ZEAI['cache_str'];?>"></script>
    <script>
	o('mini_title_myinfo').html('个人相册 (<font id="data_num"><?php echo $data_photo_num;?></font>)');
	<?php if ($data_photo_num>0){?>var pic_list=<?php echo $pic_list;?>;<?php }?>
	var data_photo_num=<?php echo $data_photo_num;?>;
	var PcfgMaxnum=<?php echo $PcfgMaxnum;?>,maxnum=PcfgMaxnum,curnum=data_photo_num;
	btnadd.onclick=function(){
		if ( data_photo_num >= PcfgMaxnum ){
			zeai.div({fobj:my_info_submain,obj:p_vipHelp,title:GradeName+'最多只能上传 '+PcfgMaxnum+' 张照片',w:300,h:300});	
		}else{
			<?php if(is_h5app()){?>
			app_uploads({url:HOST+"/m1/my_info.php?submitok=ajax_photo_up_app",num:1},function(){if(o("my_info_photobtn"))o("my_info_photobtn").click();});
			<?php }else{
				$multiple=$PcfgMaxnum-$data_photo_num;
				$multiple=($multiple<=0)?0:$multiple;
			?>
			photo_up({
				onclick:false,
				btnadd:btnadd,
				url:HOST+"/m1/my_info"+zeai.extname,
				submitokBef:"ajax_photo_",
				multiple:<?php echo $multiple;?>,//PcfgMaxnum
				_:function(rs){
					zeai.msg(0);zeai.msg(rs.msg);o('my_info_photobtn').click();
				}
			});
			<?php }?>
		}
	}
	zeai.listEach(picli.getElementsByTagName("i"),function(obj){
		if (zeai.empty(obj.className)){
			var view=obj.getElementsByTagName("img")[0],del =obj.getElementsByTagName("b")[0];
			view.onclick=function(){photoView(this.src.replace('_s','_b'));	}
			del.onclick=function(){
				zeai.alertplus({'title':'确认要删除么？','content':'','title1':'取消','title2':'删除',
					'fn1':function(){zeai.alertplus(0);},
					'fn2':function(){zeai.alertplus(0);PVdel(obj.id.replace('li',''),'ajax_photo_del');}
				});
			}
		}
	});
    </script>

<?php exit;}elseif($submitok == 'ajax_video'){
/**************************我的视频*****************************/?>
	<div class="videoli fadeInL" id="videoli">
        <i class="add" id="btnadd"></i>
        <?php
		$rt=$db->query("SELECT id,path_s,flag,addtime FROM ".__TBL_VIDEO__." WHERE uid=".$cook_uid." ORDER BY id DESC");
		$total = $db->num_rows($rt);
		$data_num = $total;
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt);
                if(!$rows) break;
                $id     = $rows[0];
                $path_s = $rows[1];
                $flag   = $rows[2];
                $addtime= $rows[3];
				if ((ADDTIME - $addtime) > 300){
					$dst_s = $_ZEAI['up2'].'/'.$path_s;
					$dst_b = str_replace('.jpg','.mp4',$dst_s);
					$cvs     = '<video class="zeaiVbox" id="zeaiVbox'.$id.'"><source src="'.$dst_b.'">您微信版本太低，请升级</video><div class="play ico">&#xe600;</div>';
					$flagstr = $cvs;
					if(is_h5app()){
						$vplay   = 'javascript:app_VideoPlayer("'.$dst_b.'");';
					}else{
						$vplay = 'javascript:zeaiplay("'.$id.'")';
					}
					$ifdel = true;
				}else{
					$dst_s = HOST.'/res/videomaking.gif';	
					$dst_b = '';
					$vplay = 'javascript:;';
					$ifdel = false;
				}
				if($flag == 0){
					$flagstr = '<span>审核中</span>';
					$vplay = 'javascript:;';
				}
                $list_str  = '';
				$liid      = 'li'.$id;
                $list_str .= '<i id="'.$liid.'">';
                $list_str .= '<a href='.$vplay.'><img src="'.$dst_s.'">'.$flagstr.'</a>';
				if ($ifdel)$list_str .= '<a href="javascript:;" class="del"></a>';
                $list_str .= '</i>';
                echo $list_str;
            }
        }
		$VcfgMaxnum = $video_num->$data_grade;
        ?>
    </div>
    <div id="v_vipHelp" class="helpDiv">
        <ul><?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
			$ifmy = ($data_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
            $out1 .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i> <font class="Cf00">'.$video_num->$grade.'</font> 个'.$ifmy.'</li>';
        }echo $out1;
        ?></ul>
        <a class="btn size3 HUANG Mcenter block center" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.extname,'<?php echo $curpage;?>','my_vip');">我要升级会员</a>
    </div>
	<script src="<?php echo HOST;?>/m1/js/my_info.js"></script>
    <script>
	o('mini_title_myinfo').html('我的视频 (<font id="data_num"><?php echo $data_num;?></font>)');
	var data_video_num=<?php echo $data_num;?>;
	var VcfgMaxnum=<?php echo $VcfgMaxnum;?>;
	btnadd.onclick=function(){
		if ( data_video_num >= VcfgMaxnum ){
			zeai.div({fobj:my_info_submain,obj:v_vipHelp,title:GradeName+'最多只能上传 '+VcfgMaxnum+' 个视频',w:300,h:300});	
		}else{
			<?php if(is_h5app()){ ?>
			app_video_uploads({url:HOST+"/m1/my_info.php?submitok=ajax_video_up_app"},function(){if(o("my_info_videobtn"))o("my_info_videobtn").click();});
			<?php }else{?>
			video_up({url:HOST+"/m1/my_info"+zeai.extname,submitok:"ajax_video_up"});
			<?php }?>
		}
	}
	zeai.listEach(videoli.getElementsByTagName("i"),function(obj){
		if (zeai.empty(obj.className)){
			var Aarr=obj.getElementsByTagName("a");
			var play=Aarr[0],del =Aarr[1];
			play.onclick=function(){/*photoView(this.src.replace('_s','_b'));*/	}
			if(!zeai.empty(o(del)))del.onclick=function(){
				zeai.alertplus({'title':'确认要删除么？','content':'','title1':'取消','title2':'删除',
					'fn1':function(){zeai.alertplus(0);},
					'fn2':function(){zeai.alertplus(0);PVdel(obj.id.replace('li',''),'ajax_video_del');}
				});
			}
		}
	});
    </script>
<?php
exit;}
/*************Ajax End*************/



/*************Main Start*************/
$mini_title = '<span id="mini_title_myinfo"></span>';
$mini_class = 'top_mini top_mini_my_info';
require_once ZEAI.'m1/top_mini.php';
$tabmenu_num=(@in_array('video',$navarr))?4:3;
?>
<link href="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/m1/css/my_info.css?s<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style>
::-webkit-input-placeholder{font-size:14px}
.ios-select-widget-box.olay {background-color:rgba(0,0,0,.4)}
.ios-select-widget-box.olay > div {width:100%;top:auto;bottom:0;left:0;border-radius:0;padding-bottom:58px}
#weixin_pic .icoadd,#weixin_pic img{width:40px;height:40px;display:block;margin:5px -30px 0}
#weixin_pic .icoadd{line-height:40px;border:#dedede 1px solid;font-size:30px;text-align:center;color:#aaa;display:inline-block;margin:4px -30px 0}
#weixin_pic:after{content:''}
#weixin_pic .wxpic{position:absolute;right:40px;top:0}
#tabmenu_my_info li span{font-size:17px;padding:0 11px 0 12px;line-height:37px;font-weight:bold}
#tabmenu_my_info em{width:90%;padding:0 5%;position:absolute;z-index:2}
#tabmenu_my_info i{bottom:7px;background-color:#fff;height:30px;border-radius:3px;z-index:1/*;background-color:rgba(0,0,0, 0.2)*/}
#tabmenu_my_info li.ed span{color:#f70}
.tabmenu{z-index:0}
.lineSsquare .title{color:#FD66B5}
.modlist button.size4{border-radius:30px}
<?php if($_ZEAI['mob_mbkind']==3){?>
#tabmenu_my_info,.top_mini_my_info{background:#FF6F6F}
#my_info_bfbbar{background:#ffcece}
#my_info_bfb span{color:#FF6F6F}
.lineSsquare .title{color:#FF6F6F}
.modlist button.size4{background:#FF6F6F}
.HONG{background-color:#FF6F6F}
.divBtmMod .divBtmSave,.ios-select-widget-box header.iosselect-header a.sure{background-color:#FF6F6F}
<?php }?>
</style>
<i class='ico goback' id='ZEAIGOBACK-<?php echo $curpage;?>'>&#xe602;</i>
<div class="tabmenu tabmenu_<?php echo $tabmenu_num;?>" id="tabmenu_my_info">
	<em>
    <li<?php echo ($a == 'data')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_data&href=<?php echo $href;?>" id="<?php echo $curpage;?>_databtn"><span>资料</span></li>
    <li<?php echo ($a == 'cert')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_cert&i=<?php echo $i;?>&href=<?php echo $href;?>" id="<?php echo $curpage;?>_certbtn"><span>认证</span></li>
    <li<?php echo ($a == 'photo')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_photo" id="<?php echo $curpage;?>_photobtn"><span>相册</span></li>
	<?php if(@in_array('video',$navarr)){?><li<?php echo ($a == 'video')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_video" id="<?php echo $curpage;?>_videobtn"><span>视频</span></li><?php }?>
    </em>
	<i></i>
</div>
<div class="submain2 <?php echo $curpage;?>" id="<?php echo $curpage;?>_submain"></div>
<?php
/*************Main End****<script src="res/zeai_ios_select/separate/zepto.js?212ss"></script>*********/?>
<script src="<?php echo HOST;?>/res/iscroll.js"></script>
<script src="<?php echo HOST;?>/res/zeai_ios_select/separate/iosSelect.js"></script>
<script src="<?php echo HOST;?>/res/zeai_ios_select/separate/select_mini.js"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/birthday.js"></script>
<script src="<?php echo HOST;?>/m1/js/zeai_div_area.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
Sbindbox='';
var curpage = '<?php echo $curpage;?>',
submain = '<?php echo $curpage;?>_submain',
upMaxMB = <?php echo $_UP['upMaxMB']; ?>,
upVMaxMB = <?php echo $_UP['upVMaxMB']; ?>,
browser='<?php echo (is_weixin())?'wx':'h5';?>',
up2='<?php echo $_ZEAI['up2'];?>/',
GradeName = '<?php echo utitle($data_grade); ?>';
ZeaiM.tabmenu.init({obj:tabmenu_my_info,showbox:'<?php echo $curpage;?>_submain'});
setTimeout(function(){o('<?php echo $curpage;?>_<?php echo $a;?>btn').click();},200);
if(mobkind()=='android'){
  <?php echo $curpage;?>_databtn.style.lineHeight = '40px';
  <?php echo $curpage;?>_certbtn.style.lineHeight = '40px';
  <?php echo $curpage;?>_photobtn.style.lineHeight = '40px';
  <?php if(@in_array('video',$navarr)){?><?php echo $curpage;?>_videobtn.style.lineHeight = '40px';<?php }?>
}
</script>
<?php
function in_extData($objstr,$chkkind='') {
	global $_UDATA;
	$return=false;
	$extifshow = json_decode($_UDATA['extifshow'],true);
	if (@count($extifshow) >= 1 && is_array($extifshow)){
		foreach ($extifshow as $V) {
			if($V['f']==$objstr && $chkkind=='subkind'){
				return $V['s'];
			}else{
				if ($V['f']==$objstr)return true;
			}
		}
	}
	return $return;
}
function Dmod($field){
	global $db,$cook_uid;
	$db->UPDATE($cook_uid,$field);
}
function jsonOutAndBfb($update){
	global $cook_uid;
	if($update==1)set_data_ed_bfb($cook_uid);
	json_exit(array('flag'=>1,'msg'=>'保存成功'));
}
?>
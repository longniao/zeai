<?php 
/***************************************************
作者: 郭余林　QQ:797311 (supdes)
***************************************************/
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_up.php';
require_once 'photo_blur.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
switch ($_POST['submitok']) {
	case 'adminfileexists':
		echo (file_exists(ZEAI2.$dbfile))?true:false;exit;
	break;
	case 'admindelpic':
		if (!ifint($uu) || str_len($pp) != 32)exit;
		$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id=".$uu." AND password='$pp'");
		if (!$db->num_rows($rt)){exit;}else{
			if (!empty($dbpicname)){
				$dbpicnameARR = explode('|',$dbpicname);
				if (count($dbpicnameARR) >=1){
					foreach($dbpicnameARR as $valuep){if (@file_exists(ZEAI2.$valuep))@unlink(ZEAI2.$valuep);}
				}else{
					if (@file_exists(ZEAI2.$dbpicname))@unlink(ZEAI2.$dbpicname);
				}
			}
		}
	break;
	case 'adm_rename_pic':
		if (!ifint($uu) || str_len($pp) != 32)exit;
		$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id=".$uu." AND password='$pp'");
		if (!$db->num_rows($rt)){exit;}else{
			if (!empty($dbpicname1) && !empty($dbpicname2)){
				if (@file_exists($dbpicname1))rename(ZEAI2.$dbpicname1,ZEAI2.$dbpicname2);
			}
		}
	break;
	case 'userdelpic':
		if (!ifint($uid) || str_len($pwd) != 32)exit;
		$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$uid." AND pwd='$pwd'");
		if (!$db->num_rows($rt)){
			exit;
		}else{
			if (!empty($dbpicname)){
				$dbpicnameARR = explode('|',$dbpicname);
				if (count($dbpicnameARR) >=1){
					foreach($dbpicnameARR as $valuep){
						if (@file_exists(ZEAI2.$valuep))@unlink(ZEAI2.$valuep);
					}
				}else{
					if (@file_exists(ZEAI2.$dbpicname))@unlink(ZEAI2.$dbpicname);
				}
			}
		}
	break;
	case 'tg_userdelpic':
		if (!ifint($uid) || str_len($pwd) != 32)exit;
		$rt = $db->query("SELECT id FROM ".__TBL_TG_USER__." WHERE id=".$uid." AND pwd='$pwd'");
		if (!$db->num_rows($rt)){
			exit;
		}else{
			if (!empty($dbpicname)){
				$dbpicnameARR = explode('|',$dbpicname);
				if (count($dbpicnameARR) >=1){
					foreach($dbpicnameARR as $valuep){
						if (@file_exists(ZEAI2.$valuep))@unlink(ZEAI2.$valuep);
					}
				}else{
					if (@file_exists(ZEAI2.$dbpicname))@unlink(ZEAI2.$dbpicname);
				}
			}
		}
	break;
	case 'guest_userdelpic':
		if (!empty($dbpicname)){
			$dbpicnameARR = explode('|',$dbpicname);
			foreach($dbpicnameARR as $valuep){if (@file_exists(ZEAI2.$valuep) && strstr($valuep,'/tmp/') )@unlink(ZEAI2.$valuep);}
		}
	break;
	case 'hndelpic':
		if (!ifint($hid) || str_len($pwd) != 32)exit;
		$rt = $db->query("SELECT id FROM ".__TBL_HN__." WHERE id=".$hid." AND pwd='$pwd'");
		if (!$db->num_rows($rt)){exit;}else{
			if (!empty($dbpicname)){
				$dbpicnameARR = explode('|',$dbpicname);
				if (count($dbpicnameARR) >=1){
					foreach($dbpicnameARR as $valuep){if (@file_exists(ZEAI2.$valuep))@unlink(ZEAI2.$valuep);}
				}else{
					if (@file_exists(ZEAI2.$dbpicname))@unlink(ZEAI2.$dbpicname);
				}
			}
		}
	break;
	case 'u_pic_reTmpDir':
		if (!ifint($uid) || str_len($pwd) != 32)exit;
		$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$uid." AND pwd='$pwd'");
		if (!$db->num_rows($rt)){exit;}else{
			if (!empty($dbname) && !empty($newdir) ){
				$dst_new = str_replace('tmp',$newdir,$dbname);
				$start   = ZEAI2.$dbname;
				$dst_new = mk_fdatedir($dst_new);
				if (@file_exists($start))rename($start,$dst_new);
			}
		}
	break;
	case 'u_pic_reTmpDir_tg':
		if (!ifint($uid) || str_len($pwd) != 32)exit;
		$rt = $db->query("SELECT id FROM ".__TBL_TG_USER__." WHERE id=".$uid." AND pwd='$pwd'");
		if (!$db->num_rows($rt)){exit;}else{
			if (!empty($dbname) && !empty($newdir) ){
				$dst_new = str_replace('tmp',$newdir,$dbname);
				$start   = ZEAI2.$dbname;
				$dst_new = mk_fdatedir($dst_new);
				if (@file_exists($start))rename($start,$dst_new);
			}
		}
	break;
	case 'u_pic_reTmpDir_guest':
		if (!empty($dbname) && !empty($newdir) && strstr($dbname,'/tmp/') ){
			$dst_new = str_replace('tmp',$newdir,$dbname);
			$start   = ZEAI2.$dbname;
			$dst_new = mk_fdatedir($dst_new);
			if (@file_exists($start))rename($start,$dst_new);
		}
	break;
	case 'adm_pic_reTmpDir':
		if (!ifint($uu) || str_len($pp) != 32)exit;
		$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id=".$uu." AND password='$pp'");
		if (!$db->num_rows($rt)){exit;}else{
			if (!empty($dbname) && !empty($newdir) ){
				$dst_new = str_replace('tmp',$newdir,$dbname);
				$start   = ZEAI2.$dbname;
				$dst_new = mk_fdatedir($dst_new);
				if (@file_exists($start))rename($start,$dst_new);
			}
		}
	break;
	case 'ewm_create':
		$destination = mk_fdatedir($dbname);
		if(!file_exists($destination)){
			require_once ZEAI.'sub/ewm.php';
			$ecc="L";// L-smallest, M, Q, H-best 
			$size="6";
			QRcode::png($ewmurl,$destination,$ecc,$size,2);
		}
	break;
	default:
		$filename  = $_GET['filename'];
		$waterimg  = $_GET['waterimg'];
		$bigsize   = $_GET['bigsize'];
		$smallsize = $_GET['smallsize'];
		$middlesize= $_GET['middlesize'];
		if ($filetype == 'photo' || empty($filetype)){
			$ret = up_receive($filename,$waterimg,$bigsize,$smallsize,$middlesize);
		}elseif($filetype == 'video' || $filetype == 'audio'){
			$ret = receive_video($filename);
		}
		echo $ret;
	break;
}
function get_ext($file_name){
    $arr = explode('.', $file_name);
    return array_pop($arr);
}
function receive_video($filename){
    $streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';  
    if(empty($streamData)){  
        $streamData = file_get_contents('php://input');  
    }
    if($streamData != ''){
		mk_fdatedir($filename);
		$destination = ZEAI2.$filename;//DIRECTORY_SEPARATOR
		$ret = @file_put_contents($destination,$streamData,true);

		//截图
        $extname = get_ext($destination);
        $_img = str_replace('.'.$extname,'.jpg',$destination);
        $cmd = 'ffmpeg -i '. $destination .' -ss 1 -vframes 1 ' . $_img;
        exec($cmd);

		$ret = true;
	}else{
		$ret = false;
	}
	return $ret;
}
function up_receive($filename,$waterimg=0,$bigsize='',$smallsize='',$middlesize=''){
    $streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';  
    if(empty($streamData)){  
        $streamData = file_get_contents('php://input');  
    }
	$ret = false;
    if($streamData != ''){
		$destination = mk_fdatedir($filename);
        $ret = file_put_contents($destination, $streamData, true);
		if (!empty($bigsize) && !empty($smallsize)){
			$bigsize   = explode('*',$bigsize);$W = $bigsize[0];$H = $bigsize[1];
			$smallsize = explode('*',$smallsize);$w = $smallsize[0];$h = $smallsize[1];
			if(ifint($w,'0-9',"1,4") && ifint($h,'0-9',"1,4") && ifint($W,'0-9',"1,4") && ifint($H,'0-9',"1,4")){
				$newDS = resizeBS($destination,$w,$h,$W,$H);
				if ($waterimg == 1)watermark($newDS);
				$ret = true;
			}
		}elseif( !empty($smallsize) && empty($bigsize) ){//up_send($file,$dbpicname,'100*100')
			$smallsize = explode('*',$smallsize);$w = $smallsize[0];$h = $smallsize[1];
			if(ifint($w,'0-9',"1,4") && ifint($h,'0-9',"1,4")){
				$newDS = resize($destination,$w,$h);
				if ($waterimg == 1)watermark($newDS);
				$ret = true;
			}
		}
		if (!empty($middlesize)){
			$middlesize = explode('*',$middlesize);
			$dM1 = dirname($destination);
			$dM2 = basename($destination);
			$dM2 = str_replace(".","_b.",$dM2);
			$dM  = $dM1.'/'.$dM2;
			resizeM($dM,$middlesize[0],$middlesize[1]);
			$ret = true;
		}
    }else{  
        $ret = false;
    }
	return  $ret;
}
function mk_fdatedir($filename){
	$fdir = ZEAI2.dirname($filename);
	mk_dir($fdir);
	return ZEAI2.$filename;
}
function watermark($destination){
	global $_UP;
	$ftype = extname($destination);
	$waterimg = ZEAI2.$_UP['waterimg'];
	$dinfo=getimagesize($destination);
	switch ($dinfo[2]) { 
		case 1:$im =imagecreatefromgif($destination);break; 
		case 2:$im =imagecreatefromjpeg($destination);break; 
		case 3:$im =imagecreatefrompng($destination);break; 
	}
	$nimage=imagecreatetruecolor($dinfo[0],$dinfo[1]); 
	$white=imagecolorallocate($nimage,255,255,255); 
	$black=imagecolorallocate($nimage,0,0,0); 
	$red=imagecolorallocate($nimage,255,0,0); 
	imagefill($nimage,0,0,$white); 
	imagecopy($nimage,$im,0,0,0,0,$dinfo[0],$dinfo[1]); 
	$simage1 =imagecreatefrompng($waterimg); 
	imagecopy($nimage,$simage1,$dinfo[0]-160,$dinfo[1]-70,0,0,160,70); 
	imagedestroy($simage1); 
	switch ($dinfo[2]) { 
		case 1:imagegif($nimage,$destination);break; 
		case 2:imagejpeg($nimage,$destination,85);break; 
		case 3:imagepng($nimage,$destination);break; 
	} 
	imagedestroy($nimage); 
	imagedestroy($im); 
}
function extname($destination){
	$dinfo = getimagesize($destination);
	switch ($dinfo[2]) { 
		case 1:$ftype = 'gif';break; 
		case 2:$ftype = 'jpg';break; 
		case 3:$ftype = 'png';break; 
		default:return '照片类型不符';exit;break; 
	} 
	return $ftype;
}
function resizeBS($destination,$ifSW=100,$ifSH=100,$ifBW=800,$ifBH=800){
	$ftype = strtolower(extname($destination));
	$RESIZEw=$ifSW;
	$RESIZEh=(empty($ifSH))?$ifSW:$ifSH;
	$RESIZEw2=$ifBW;
	$RESIZEh2=(empty($ifBH))?$ifBW:$ifBH;
	$dinfo=getimagesize($destination); 
	switch ($dinfo[2]) { 
		case 1:$im =imagecreatefromgif($destination);break; 
		case 2:$im =imagecreatefromjpeg($destination);break; 
		case 3:$im =imagecreatefrompng($destination);break; 
	}
	piczoom($im,$RESIZEw2,$RESIZEh2,$destination,$ftype);
	//
	$W = picinfo($destination,'w');
	$H = picinfo($destination,'h');
	$ds1 = dirname($destination);
	$ds2 = basename($destination);
	$ds2_len = strlen($ds2);
	$ds2_r = substr($ds2,-4);
	$smallname = substr($ds2,0,$ds2_len-4)."_s".$ds2_r;
	$bigname   = substr($ds2,0,$ds2_len-4)."_b".$ds2_r;
	$DSsmall   = $ds1."/".$smallname;
	$DSbig     = $ds1."/".$bigname;
	if ($W > $RESIZEw || $H > $RESIZEh){
		piczoom($im,$RESIZEw,$RESIZEh,$DSsmall,$ftype);
	}else{
		@copy($destination,$DSsmall);
	}
	imagedestroy($im);
	if (@file_exists($DSbig))@unlink($DSbig);
	rename($destination,$DSbig);
	return $DSbig;
}
function resizeM($destination,$RESIZEw,$RESIZEh){
	$W = picinfo($destination,'w');
	$H = picinfo($destination,'h');
	$ds1 = dirname($destination);
	$ds2 = basename($destination);
	$ds2_len = strlen($ds2);
	$ds2_r = substr($ds2,-4);
	$ds2 = str_replace("_b","",$ds2);
	$ds2 = substr($ds2,0,$ds2_len-6)."_m".$ds2_r;
	$dsm = $ds1."/".$ds2;
	if ($W > $RESIZEw || $H > $RESIZEh){
		$ftype = strtolower(extname($destination));
		$dinfo=getimagesize($destination); 
		switch ($dinfo[2]) {
			case 1:$im =imagecreatefromgif($destination);break; 
			case 2:$im =imagecreatefromjpeg($destination);break; 
			case 3:$im =imagecreatefrompng($destination);break; 
		}
		piczoom($im,$RESIZEw,$RESIZEh,$dsm,$ftype);
		imagedestroy($im);
	}else{
		@copy($destination,$dsm);
	}
	//
	$newdst = str_replace("_b.","_blur.",$destination);
	zeaiPicBlur($destination,$newdst);
	//
	return $dsm;
}
function resize($destination,$RESIZEw,$RESIZEh){
	$W = picinfo($destination,'w');
	$H = picinfo($destination,'h');
	if ($W > $RESIZEw || $H > $RESIZEh){
		$ftype = strtolower(extname($destination));
		$dinfo=getimagesize($destination); 
		switch ($dinfo[2]) {
			case 1:$im =imagecreatefromgif($destination);break; 
			case 2:$im =imagecreatefromjpeg($destination);break; 
			case 3:$im =imagecreatefrompng($destination);break; 
		}
		piczoom($im,$RESIZEw,$RESIZEh,$destination,$ftype);
		imagedestroy($im);
	}
	return $destination;
}
function picinfo($destination,$wd){
	$dinfo = getimagesize($destination);
	$o = ($wd == 'w')?$dinfo[0]:$dinfo[1];
	return $o;
}
function piczoom($im,$maxwidth,$maxheight,$name,$ftype){
	$width  = imagesx($im);
	$height = imagesy($im);
	if(($maxwidth && $width >= $maxwidth) || ($maxheight && $height >= $maxheight)){
		if($maxwidth && $width > $maxwidth){
			$widthratio = $maxwidth/$width;
			$RESIZEWIDTH=true;
		}
		if($maxheight && $height >= $maxheight){
			$heightratio = $maxheight/$height;
			$RESIZEHEIGHT=true;
		}
		if($RESIZEWIDTH && $RESIZEHEIGHT){
			if($widthratio < $heightratio){
				$ratio = $widthratio;
			}else{
				$ratio = $heightratio;
			}
		}elseif($RESIZEWIDTH){
			$ratio = $widthratio;
		}elseif($RESIZEHEIGHT){
			$ratio = $heightratio;
		}
		$newwidth = $width * $ratio;
		$newheight = $height * $ratio;
		if(function_exists("imagecopyresampled")){
			$newim = @imagecreatetruecolor($newwidth, $newheight);
			@imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}else{
			$newim = @imagecreate($newwidth, $newheight);
			@imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
		if ($ftype == "jpg"){
			@imagejpeg($newim,$name,85);
		} elseif($ftype == "gif"){
			@imagegif($newim,$name);
		} elseif($ftype == "png"){
			@imagepng($newim,$name);
		}
		@imagedestroy ($newim);
	}
}
?>
<?php
/***************************************************
www.esyyw.com V6.0 作者: 李林　QQ:721688068 (supdes)
***************************************************/
require_once '../sub/init.php';
if (!ifint($uid) || str_len($pwd) < 10)json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_uid_pwd1'));
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once ZEAI.'sub/conn.php';
$rt = $db->query("SELECT photo_s FROM ".__TBL_TG_USER__." WHERE id=".$uid." AND pwd='$pwd'");
if (!$db->num_rows($rt)){
	json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_uid_pwd2'));
}else{
	$row = $db->fetch_array($rt,'name');
	$photo_s = $row['photo_s'];
}

gyl_debug('uid='.$uid);
//
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);



$tg=json_decode($_REG['tg'],true);
if($browser == 'wx'){
	if(str_len($token) < 50)json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_token'));
	$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
	$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"www_ZEAI_cn_tghb_'.$uid.'"}}}';
	$ticket      = Zeai_POST_stream($ticket_url,$ticket_data);
	$T           = json_decode($ticket,true);
	$qrcode_url  = 'http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
	$tgbg = $TG_set['wxbgpic'];
}else{
	$tgbg = $TG_set['wapbgpic'];
	$qrcode_url = HOST.'/sub/creat_ewm.php?url='.HOST.'/m1/reg.php?tguid='.$uid;
}
//
$dbdir  = 'p/tgewm/'.date('Y').'/'.date('m').'/';
mk_dir(ZEAI2.$dbdir);
$dbname = $dbdir.$uid.'_'.cdstrletters(3).'.jpg';
$DST    = ZEAI2.$dbname;
$bg     = ZEAI2.$tgbg;
$bgnew  = ZEAI2.$dbdir.$uid.'_bg.jpg';
$logo   = (!empty($photo_s))?ZEAI2.getpath_smb($photo_s,'m'):ZEAI2.'p/tgewm/www_zeai_cn.jpg';
//
$im=imagecreatefrom($qrcode_url);
imagejpeg($im,$DST,90);
imagedestroy($im);
picsize($DST,300,300);
//ewmphoto_s
$newlogo = ZEAI2.'p/tgewm/'.$uid.'.jpg';;
@copy($logo,$newlogo);
picsizeZFX($newlogo,64,64);
watermark($DST,$newlogo,'ewmphoto_s');
//
@copy($bg,$bgnew);
watermark($bgnew,$DST,'big');
if (@file_exists($DST))unlink($DST);
@rename($bgnew,$DST);

//bgphoto_s
@copy($logo,$newlogo);
picsizeZFX($newlogo,150,150);
$yuanim = yuan_img($newlogo);
imagepng($yuanim,$newlogo);
imagedestroy($yuanim);
watermark($DST,$newlogo,'bgphoto_s');
if (@file_exists($newlogo))unlink($newlogo);

//$db->query("UPDATE ".__TBL_USER__." SET tgpic='$dbname' WHERE id=".$uid);
json_exit(array('flag'=>1,'tgpic'=>$dbname));

function picsizeZFX($filename,$width=200,$height=200){
	//获取原图像$filename的宽度$width_orig和高度$height_orig
	list($width_orig,$height_orig) = getimagesize($filename);
	//根据参数$width和$height值，换算出等比例缩放的高度和宽度
//	if ($width && ($width_orig<$height_orig)){
//		$width = ($height/$height_orig)*$width_orig;
//	}else{
//		$height = ($width / $width_orig)*$height_orig;
//	}
	$image_p = imagecreatetruecolor($width, $height);
	$image=imagecreatefrom($filename);
	imagecopyresampled($image_p,$image,0,0,0,0,$width,$height,$width_orig,$height_orig);
	//将缩放后的图片$image_p保存，100(质量最佳，文件最大)
	imagejpeg($image_p,$filename,90);
	imagedestroy($image_p);
	imagedestroy($image);
}

function picsize($filename,$width=200,$height=200){
	//获取原图像$filename的宽度$width_orig和高度$height_orig
	list($width_orig,$height_orig) = getimagesize($filename);
	//根据参数$width和$height值，换算出等比例缩放的高度和宽度
	if ($width && ($width_orig<$height_orig)){
		$width = ($height/$height_orig)*$width_orig;
	}else{
		$height = ($width / $width_orig)*$height_orig;
	}
	$image_p = imagecreatetruecolor($width, $height);
	$image=imagecreatefrom($filename);
	imagecopyresampled($image_p,$image,0,0,0,0,$width,$height,$width_orig,$height_orig);
	//将缩放后的图片$image_p保存，100(质量最佳，文件最大)
	imagejpeg($image_p,$filename,90);
	imagedestroy($image_p);
	imagedestroy($image);
}
function watermark($destination,$waterimg,$ifbig='small'){
	$dinfo=getimagesize($destination);
	$im=imagecreatefrom($destination);
	$nimage=imagecreatetruecolor($dinfo[0],$dinfo[1]); 
	$white=imagecolorallocate($nimage,255,255,255); 
	$black=imagecolorallocate($nimage,0,0,0); 
	$red=imagecolorallocate($nimage,255,0,0); 
	imagefill($nimage,0,0,$white); 
	imagecopy($nimage,$im,0,0,0,0,$dinfo[0],$dinfo[1]); 
	$simage1=imagecreatefrom($waterimg);
	if ($ifbig == 'big'){
		imagecopy($nimage,$simage1, 225,720,0,0,300,300); 
	}elseif($ifbig == 'ewmphoto_s'){
		imagecopy($nimage,$simage1, 118, 118, 0, 0, 64, 64); 
	}elseif($ifbig == 'bgphoto_s'){
		imagecopy($nimage,$simage1, 300,313, 0, 0, 150, 150); 
	}
	imagedestroy($simage1); 
	imagejpeg($nimage,$destination);
	imagedestroy($nimage); 
	imagedestroy($im); 
}
function mk_fdatedir($filename){
	$fdir = ZEAI2.dirname($filename);
	mk_dir($fdir);
	return ZEAI2.$filename;
}
function imagecreatefrom($file) {
	$extname=getpicextname($file);
	if($extname=='png'){
		$im = imagecreatefrompng($file); 
	}elseif($extname=='jpg'){
		$im = imagecreatefromjpeg($file); 
	}elseif($extname=='gif'){
		$im = imagecreatefromgif($file); 
	}
	return $im;
}
function yuan_img($imgpath) {
	$ext     = pathinfo($imgpath);
	$src_img = null;
	switch ($ext['extension']) {
	case 'jpg':
		$src_img = imagecreatefromjpeg($imgpath);
		break;
	case 'png':
		$src_img = imagecreatefrompng($imgpath);
		break;
	}
	$wh  = getimagesize($imgpath);
	$w   = $wh[0];
	$h   = $wh[1];
	$w   = min($w, $h);
	$h   = $w;
	$img = imagecreatetruecolor($w, $h);
	imagesavealpha($img, true);
	//最后一个参数127为全透明
	$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
	imagefill($img, 0, 0, $bg);
	$r   = $w / 2;//圆半径
	$y_x = $r; //圆心X坐标
	$y_y = $r; //圆心Y坐标
	for ($x = 0; $x < $w; $x++) {
		for ($y = 0; $y < $h; $y++) {
			$rgbColor = imagecolorat($src_img, $x, $y);
			if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
				imagesetpixel($img, $x, $y, $rgbColor);
			}
		}
	}
	return $img;
}

?>
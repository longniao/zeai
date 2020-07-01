<?php
require_once 'init.php';
session_start();
header("Content-type: image/PNG");
$imgWidth = 70;
$imgHeight = 42;
$authimg = imagecreate($imgWidth,$imgHeight);
$bgColor = ImageColorAllocate($authimg,255,255,255);
$fontfile = gyl_yzm(1).".ttf";
$white=imagecolorallocate($authimg,255,255,255);
imagearc($authimg, 150, 8, 20, 20, 75, 170, $white);
imagearc($authimg, 100, 7,50, 22, 75, 175, $white);
imageline($authimg,20,20,180,30,$white);
imageline($authimg,20,18,170,50,$white);
imageline($authimg,25,50,80,50,$white);
	$noise_num = 10;
	$line_num  = 10;
imagecolorallocate($authimg,0xff,0xff,0xff);
$rectangle_color=imagecolorallocate($authimg,0xAA,0xAA,0xAA);
	$noise_color=imagecolorallocate($authimg,0x33,0x33,0x33);
$font_color=imagecolorallocate($authimg,0x00,0x00,0x00);
	$line_color=imagecolorallocate($authimg,0x00,0x00,0x00);

	for($i=0;$i<$noise_num;$i++){
		imagesetpixel($authimg,mt_rand(0,$imgWidth),mt_rand(0,$imgHeight),$noise_color);
	}
	for($i=0;$i<$line_num;$i++){
		imageline($authimg,mt_rand(0,$imgWidth),mt_rand(0,$imgHeight),mt_rand(0,$imgWidth),mt_rand(0,$imgHeight),$line_color);
	}
$str = gyl_yzm(4);
$angle = gyl_zf(1).gyl_angle(1);

$_SESSION['ZEAI_CN__YZM'] = $str;
$str = iconv("GB2312","UTF-8",$str);
imagettftext($authimg, 18, $angle, 0, 28, $font_color, ZEAI.'sub/'.$fontfile, $str);
imagepng($authimg);
imagedestroy($authimg);
function gyl_zf($length) {
	$possible = "+-";
	$str = "";
	while(strlen($str) < $length) $str .= substr($possible, (rand() % strlen($possible)), 1);
	return($str);
}
function gyl_angle($length) {
	$possible = "1357";
	$str = "";
	while(strlen($str) < $length) $str .= substr($possible, (rand() % strlen($possible)), 1);
	return($str);
}
function gyl_yzm($length) {
	$possible = "0123456789";
	$str = "";
	while(strlen($str) < $length) $str .= substr($possible, (rand() % strlen($possible)), 1);
	return($str);
}
?>
<?php 
/******************************************
Copyright(C)2019 郭余林
作者:郭余林　QQ:797311 (supdes)
*******************************************/
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
if (!ifint($uu) || !ifint($uid) || empty($tmpphoto) || str_len($pp) != 32 || substr($return_url,11,4) != 'zeai')exit;
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_up.php';
require_once 'photo_blur.php';
$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id=".$uu." AND password='$pp'");
if (!$db->num_rows($rt))exit;
$row = $db->ROW(__TBL_ADMIN__,"username,kind,agentid,agenttitle","id=".$uu." AND password='$pp'");
if ($row){
	$username=$row[0];$kind=$row[1];$agentid=$row[2];$agenttitle=$row[3];$tguid=(ifint($tg_uid))?$tg_uid:intval($tguid);
	$kind=($kind=='crm')?2:1;$uid=intval($uid);
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$c='裁切会员【'.$nickname.'（uid:'.$uid.'）】头像';
	$db->query("INSERT INTO ".__TBL_LOG__."  (username,kind,content,addtime,agentid,agenttitle,uid,tguid) VALUES ('$username',$kind,'$c',".ADDTIME.",$agentid,'$agenttitle',$uid,$tguid)");
	
}else{exit;}

$row = $db->ROW(__TBL_USER__,"photo_s","id=".$uid);
if ($row){$path_s=$row[0];}else{exit;}
$Pic = ZEAI2.$tmpphoto;
if (!ifpic($Pic)){
	exit('照片类型不符，只能是.jpg/.gif/.png格式，请检查');
}else{
	$ftype = getpicextname($Pic);
}
$photo_new    = str_replace('tmp','m',$tmpphoto);
$filename_dst = substr(basename($photo_new),0,-4);
$dbpath       = dirname($photo_new).'/';
$filepath     = ZEAI2.$dbpath;
@mk_dir($filepath);
$savename1 = $filename_dst.'_'.ADDTIME."_s.".$ftype;
$savename2 = $filename_dst.'_'.ADDTIME."_m.".$ftype;
$savename3 = $filename_dst.'_'.ADDTIME."_b.".$ftype;
$dstname1  = $filepath.$savename1;
$dstname2  = $filepath.$savename2;
$dstname3  = $filepath.$savename3;
$dbpicname = $dbpath.$savename1;
//
$bfb=2;
//
$Msize = explode('*',$_UP['upMsize']);
$cut_box_w = 600*$bfb;$cut_box_h = 450*$bfb;/////////////
$photo_s_w = $Msize[0]*$bfb;$photo_s_h = $Msize[1]*$bfb;//////////////
$maxL = intval(($cut_box_w-$photo_s_w)/2);
$maxT = intval(($cut_box_h-$photo_s_h)/2);
$a = explode('px',$left);$left = $a[0];$a = explode('px',$top);$top = $a[0];

$left=$left*$bfb;//////////
$top=$top*$bfb;//////////////
//最终left,top
$x = $left;$y = $top;
//最终宽度，高度
$w = $width*$bfb;$h = $height*$bfb;
//画出原始大图
if ($ftype == 'gif'){
	$im_dst = imagecreatefromgif($Pic);
}elseif($ftype == 'jpg' || $ftype == 'jpeg'){
	$im_dst = imagecreatefromjpeg($Pic);
} else {
	$im_dst = imagecreatefrompng($Pic);
}
//原始图的宽,高
$Width  = imagesx($im_dst);$Height = imagesy($im_dst);
//缩略，如果放大了
if ( ($Width < $w) && ($Height < $h) ){
	if ($h < $w){$t = $h;}else {$t = $w;}
	$im = @makebig2($im_dst,$t,$t,$ftype);
} elseif ( ($Width > $w) && ($Height > $h) ) {
	if ($h > $w){$t = $h;}else {$t = $w;}
	$im = @makexiao2($im_dst,$t,$t,$ftype);
}else{
	$im = $im_dst;
}
switch ($turn) {
	case 1:$im=imagerotate($im,-90,0);break;
	case 2:$im=imagerotate($im,180,0);break;
	case 3:$im=imagerotate($im,90,0);break;
}
//操作后角度和宽,高
$w=imagesx($im);
$h=imagesy($im);
//获取框内图
if ($ftype == 'gif'){
	$newim = imagecreate($cut_box_w, $cut_box_h);
	imagecopyresized($newim, $im, $x, $y, 0, 0, $w, $h, $w, $h);
	$endim = imagecreate($photo_s_w, $photo_s_h);
	imagecopyresized($endim, $newim, 0, 0, $maxL, $maxT, $photo_s_w, $photo_s_h, $photo_s_w, $photo_s_h);
} else {
	$newim = imagecreatetruecolor($cut_box_w, $cut_box_h);//创建 600x600
	imagecopyresampled($newim, $im, $x, $y, 0, 0, $w, $h, $w, $h);//把原图拷到600x600画布中
	$endim = imagecreatetruecolor($photo_s_w, $photo_s_h);//创建 200x248的画布
	imagecopyresampled($endim, $newim, 0, 0, $maxL, $maxT, $photo_s_w, $photo_s_h, $photo_s_w, $photo_s_h);
}
imagedestroy($im);
imagedestroy($newim);
if ($j == 'GYL_supdes__www.zeai_cn'){
	//s
	$im_s = @makexiao2($endim,$photo_s_w/3,$photo_s_h/3,$ftype);
	@imagejpeg($im_s,$dstname1,85);
	@imagedestroy ($im_s);
	//m
	@imagejpeg($endim,$dstname2,95);
	@imagedestroy ($endim);
	//b
	@copy($Pic,$dstname3);
	
	if(@file_exists($Pic) && $ifm==1)unlink($Pic);
	//blur
	$newdst = str_replace("_b.","_blur.",$dstname3);
	zeaiPicBlur($dstname3,$newdst);
	//
	$path_m = getpath_smb($path_s,'m');$path_b = getpath_smb($path_s,'b');$path_blur = getpath_smb($path_s,'blur');
	if (@file_exists(ZEAI2.$path_s))@unlink(ZEAI2.$path_s);
	if (@file_exists(ZEAI2.$path_m))@unlink(ZEAI2.$path_m);
	if (@file_exists(ZEAI2.$path_b))@unlink(ZEAI2.$path_b);
	if (@file_exists(ZEAI2.$path_blur))@unlink(ZEAI2.$path_blur);
	//
	$db->query("UPDATE ".__TBL_USER__." SET photo_s='$dbpicname',photo_f=1 WHERE id=".$uid);
}else{exit;}
function makebig2($im,$maxwidth,$maxheight,$ftype){
	$width = imagesx($im);
	$height = imagesy($im);
	if(($maxwidth && $width < $maxwidth) || ($maxheight && $height < $maxheight)){
		if($maxwidth && $width < $maxwidth){
			$widthratio = $maxwidth/$width;
			$RESIZEWIDTH=true;
		}
		if($maxheight && $height < $maxheight){
			$heightratio = $maxheight/$height;
			$RESIZEHEIGHT=true;
		}
		if($RESIZEWIDTH && $RESIZEHEIGHT){
			if($widthratio > $heightratio){
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
		if ($ftype == 'gif' || $ftype == 'GIF'){
			$newim = imagecreate($newwidth, $newheight);
			imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}else{
			$newim = imagecreatetruecolor($newwidth, $newheight);
			imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
	}
	return $newim;
}
function makexiao2($im,$maxwidth,$maxheight,$ftype){
	$width  = imagesx($im);
	$height = imagesy($im);
	if(($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight)){
		if($maxwidth && $width > $maxwidth){
			$widthratio = $maxwidth/$width;
			$RESIZEWIDTH=true;
		}
		if($maxheight && $height > $maxheight){
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
		if ($ftype == 'gif' || $ftype == 'GIF'){
			$newim = imagecreate($newwidth, $newheight);
			imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}else{
			$newim = imagecreatetruecolor($newwidth, $newheight);
			imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
	}else{
		$newim = $im;
	}
	return $newim;
}
//Guyulin by 2018-10-15
?>
<style>
.sussesstips{width:300px;margin:0 auto;padding-top:100px;font-size:24px;text-align:center}
a.aLAN{display:block;border-radius:18px;width:100px;line-height:36px;font-size:16px;text-decoration:none;font-family:'Microsoft YaHei','宋体','SimSun';margin:0 auto}
a.aLAN{background-color:#e3f4ff;border:#84cdff solid 1px;color:#2484dd;}a.aLAN:hover{background-color:#cfebff}
</style>
<?php if ($j == 'GYL_supdes__www.zeai_cn'){echo '<div class="sussesstips"><img src="'.$_ZEAI['adm2'].'/images/sussess.png"><br><br>设置成功！<br><br><img src="'.$_m.'"><br><br><a class="aLAN" href="javascript:window.parent.location.reload(),window.parent.zeai.iframe(0);">关闭</a></div>';exit;}?>
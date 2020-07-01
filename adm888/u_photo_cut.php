<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_up.php';
if(!ifint($id)){echo "forbidden";exit;}
if ($ifm == 1){
	$uid = $id;
	if (empty($tmpphoto)){
		$row = $db->ROW(__TBL_USER__,'photo_s',"id=".$uid);
		$path_s = getpath_smb($row[0],'b');
	}else{
		$path_s = $tmpphoto;
	}
}else{
	$row = $db->ROW(__TBL_PHOTO__,"uid,path_s","id=".$id);
	if(!$row){
		echo "ID不存在";exit;
	}else{
		$uid    = intval($row[0]);
		$path_s = getpath_smb($row[1],'b');
		$row = $db->NUM('supdes','id',"id=".$uid);
		if(!$row){echo "会员不存在";exit;}
	}
}
$defpicurl  = getpath_smb($_ZEAI['up2'].'/'.$path_s.'?'.ADDTIME,'b');
$Msize = explode('*',$_UP['upMsize']);
$cut_box_w  = 600;$cut_box_h = 450;
$photo_s_w  = $Msize[0];$photo_s_h = $Msize[1];
$photo_s_w_ = $photo_s_w+4;$photo_s_h_ = $photo_s_h+4;;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href="css/photo_s_cut_adm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.pbox{width:<?php echo $cut_box_w; ?>px}
.canvas{height:<?php echo $cut_box_h; ?>px}
.cut_box{width:<?php echo $cut_box_w; ?>px;height:<?php echo $cut_box_h; ?>px}
.cut_box ul{width:100%;height:<?php echo ($cut_box_h - $photo_s_h_)/2; ?>px}
.cut_box ul:nth-child(2){height:<?php echo $photo_s_h_; ?>px}
.cut_box ul:nth-child(2) li{width:<?php echo ($cut_box_w - $photo_s_w_)/2; ?>px}
.cut_box ul:nth-child(2) li:nth-child(2){width:<?php echo $photo_s_w_; ?>px}
</style>
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var cut_box_w = <?php echo $cut_box_w; ?>;
var cut_box_h = <?php echo $cut_box_h; ?>;
var photo_s_w = <?php echo $photo_s_w; ?>;
var photo_s_h = <?php echo $photo_s_h; ?>;
</script>
</head>
<body>
<?php if ($submitok == 'www___zeai__cn_inphotocut'){ ?>
<div class="pbox">
    <div class="canvas">
        <div class="cut_box" onmousedown="grasp();" onmouseup="free();">
            <ul></ul><ul><li></li><li ondblclick="javascript:supdessubmit();"></li><li></li></ul><ul></ul>
        </div>
        <div id="source_div"><img src="<?php echo $defpicurl; ?>" id="show_img"></div>
        <div class="cutbtn">
            <h3>
                <a href="javascript:change_size(1);" class="cutbt1" title="放大"></a>
                <a href="javascript:change_size(-1);" class="cutbt2" title="缩小"></a>
                <a href="javascript:turn(1);" class="cutbt3" title="向右旋转"></a>
                <a href="javascript:turn(-1);" class="cutbt4" title="向左旋转"></a>
                <div class="clear"></div>
            </h3>
            <a href="javascript:supdessubmit();" class="cutbt5">裁切并保存头像</a>
        </div>
    </div>
	<h2>将需要部分拖到红色框内，以裁切出满意的主图，如果是小分辨率显示屏，调整后可双击图片进行保存</h2>
    <script src="js/photo_s_cut_adm.js"></script>
    <script>document.onmousemove=move_it;document.onmouseup=free;</script>
	<form action="<?php echo $_ZEAI['up2']; ?>/tucotohpiaez_photo_s_adm.php" name="myform" method="post">
	<input type="hidden" name="width">
	<input type="hidden" name="height">
	<input type="hidden" name="left">
	<input type="hidden" name="top">
	<input type="hidden" name="turn">
	<input type="hidden" name="tmpphoto" value="<?php echo $path_s; ?>">
	<input type="hidden" name="return_url" value="http://www.zeai.cn" >
	<input type="hidden" name="j" value="GYL_supdes__www.zeai_cn">
    <input type="hidden" name="uid" value="<?php echo $uid; ?>">
    <input type="hidden" name="ifm" value="<?php echo $ifm; ?>">
    <input type="hidden" name="uu" value="<?php echo $_SESSION['admuid']; ?>">
    <input type="hidden" name="pp" value="<?php echo $_SESSION['admpwd']; ?>">
	</form>
</div>
<div class="clear"></div>
<iframe src="" name="run_fra" id="run_fra" width=0 height=0></iframe>
<?php }?>
<?php require_once 'bottomadm.php';?>
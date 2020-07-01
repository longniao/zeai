<?php
require_once '../sub/init.php';
//define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(ifint($uid)){
	$url=Href('u',$uid);
}elseif(ifint($cid)){
	$url=HOST.'/m4/shop_detail.php?id='.$cid;
}?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.urlbox{display:block;margin:10px auto;width:90%;font-size:14px;word-wrap:break-word}
.u_ewm_img{width:180px;height:180px;display:block;padding:2px;border-radius:3px;border:#dedede 1px solid;margin:0 0 5px}
</style>
</head>
<body>
<table class="table0 Mtop20">
<tr>
<td align="center"><img src="<?php echo HOST;?>/sub/creat_ewm.php?url=<?php echo $url;?>" class="u_ewm_img"></td>
</tr>
<tr>
<td align="center"><i class="ico picmiddle S18" style="color:#45C01A">&#xe607;</i> <span class="picmiddle S14">打开微信扫码进入</span></td>
</tr>
<?php if ($ifshowurl == 1){?>
<tr>
<td align="center"><div class="urlbox"><?php echo $url;?></div><button type="button" class="btn size3 LV2"  onclick="zeai.copy('<?php echo $url;?>',function(){zeai.msg('复制成功');})"><i class="ico2">&#xe616;</i> 复制链接网址</button></td>
</tr>
<?php }?>
</table>
<?php require_once 'bottomadm.php';?>
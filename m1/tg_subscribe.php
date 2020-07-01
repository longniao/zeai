<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
if (!empty($cook_tg_openid) && $cook_tg_subscribe==1)header("Location: tg_my.php");
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_wxgzh.php';
$TG_set = json_decode($_REG['TG_set'],true);
$headertitle = '我的';
require_once ZEAI.'m1/header.php';
//
if (is_weixin()){
	$token = wx_get_access_token();
	if(str_len($token) < 50)json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_token'));
	$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
	$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"www_ZEAI_cn_tghn_'.$cook_tg_uid.'"}}}';
	$ticket      = Zeai_POST_stream($ticket_url,$ticket_data);
	$T           = json_decode($ticket,true);
	$qrcode_url  = 'http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
}else{
	$qrcode_url  = $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm'];
}
?>
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/m1/css/TG2.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style>
body{background-color:#fff}
.boxC{width:100%;background-color:#fff;text-align:left;padding:40px 20px;margin-top:60px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.boxC img,.boxC em{width:60%;margin:0 auto;display:block}
.boxC img{padding:2px;border:#dedede 1px solid}
.boxC em{width:70%;font-size:16px;line-height:150%;margin-top:10px}
.boxC em font{color:#06BC07}
.top_miniTG{background:#F7564D}
.boxC .size4{width:60%;margin:20px auto 0 auto;display:block;text-align:center}
</style>
<?php
$mini_backT = '';
$mini_title = '请先关注公众号';
$mini_class = 'top_mini top_miniTG';
require_once ZEAI.'m1/top_mini.php';
?>
<div class="boxC">
<img src="<?php echo $qrcode_url; ?>">
<em>长按二维码关注公众号，获取帐号消息通知等全功能体验！</em>
<a href="<?php echo HOST;?>/m1/tg_index.php" class="btn size4 HONG4 yuan">进入<?php echo $TG_set['navtitle'];?></a>
</div>
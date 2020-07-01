<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!ifint($id))alert_adm("ID不正确","back");
$token = wx_get_access_token();
$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"hn_'.$id.'"}}}';
$ticket = Zeai_POST_stream($ticket_url,$ticket_data);
$T = json_decode($ticket,true);
$qrcode_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<body>
<table class="table0 Mtop20">
<tr>
<td align="center"><img src="<?php echo $qrcode_url;?>" width="360" height="360" style="padding:10px;border:#ddd 1px solid"></td>
</tr>
<tr>
  <td height="50" align="center" class="S14">可以将此二维码印制到红娘名片上面，有人要扫码关注即在此红娘名下</td>
</tr>
</table>
<?php require_once 'bottomadm.php';?>
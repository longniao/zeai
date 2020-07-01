<?php
require_once '../../../sub/init.php';
require_once ZEAI.'my/chkuser.php';
$jumpurl = ($kind == 3)?$_ZEAI['hn_2domain'].'/my_vip.php':$_ZEAI['www_2domain'].'/my/account.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Refresh" Content="5; url=<?php echo $return_okurl; ?>"> 
<title>微信支付成功</title>
<link href="../../../css/my.css" rel="stylesheet" type="text/css">
<style>
.table0{width:600px;background-color:#fff;border:#ccc 1px solid;box-shadow:3px 3px 15px rgba(0,0,0,0.1);margin:50px auto}
.table0 td{padding:10px}
.enter{font-size:16px;display:block;margin:0 auto;line-height:40px;text-align:center;width:140px;background-color:#f70;color:#fff;border-radius:1px}
.enter:hover{background-color:#f30;color:#fff}
</style>
<script src="/js/www_zeai_cn.js"></script>
</head>
<body>
<?php require_once ZEAI.'my/my_top.php'; ?>
<table class="table0">
<tr>
<td width="588" height="290" align="center" class="center"><table width="340" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td width="64" height="60" align="right"><img src="../../../images/sussess.png" width="64" height="64"></td>
<td width="235" align="left" class="S24 C090">恭喜你，支付成功！</td>
</tr>
</table>
<br>
<table width="414" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td width="91" height="40" align="right">订单编号：</td>
<td width="313" align="left" class="S16"><?php echo $orderid;?></td>
</tr>
<tr>
<td height="40" align="right">支付金额：</td>
<td align="left" class="S16"><span style="color:#EE5A4E;font-family:Arial">¥</span> <font style="color:#EE5A4E;font-size:30px;font-family:Arial"><?php echo $money; ?></font></td>
</tr>
</table></td>
</tr>
<tr>
<td height="90" align="center" valign="top" class="center"><a href="<?php echo $jumpurl; ?>" class="enter">进入管理中心</a></td>
</tr>
</table>
<?php require_once ZEAI.'bottom.php';?>
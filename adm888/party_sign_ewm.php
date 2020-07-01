<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
if(!ifint($fid))exit("forbidden");
$row = $db->ROW(__TBL_PARTY__,"flag","id=".$fid,"num");
if (!$row)exit('活动不存在或已被删除。');
$flag = $row[0];
if($flag>2){exit('活动已结束。');}
if($flag!=2){exit('<br><br><center>活动还没开始哦，只有活动状态<font color=#ff6600>【进行中】</font>才可以签到</center>');}
$timee=ADDTIME+10;
$db->query("UPDATE ".__TBL_PARTY__." SET refresh_time=".$timee." WHERE id=".$fid);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="10">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<style>
.signewm{margin:30px auto}
.signewm img{width:300px;height:300px;border:#ccc 1px solid;padding:15px}
.signewm h1{color:#f00;margin-top:20px;font-weight:bold}
</style>
<body>
<div class="signewm">
<img src="../sub/creat_ewm.php?url=<?php echo HOST."/m1/party_sign.php?submitok=ewm&fid=".$fid;?>&time=<?php echo $timee;?>">
<h1>请打开微信现场签到扫码</h1>
</div>
<?php require_once 'bottomadm.php';?>
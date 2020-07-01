<?php require_once "sub/init.php";
require_once ZEAI."sub/conn.php";
$mainid = ($mainid-797311)/3;if ( !preg_match("/^[0-9]{1,8}$/",$mainid) || empty($mainid))callmsg("Forbidden!","-1");
$rt = $db->query("SELECT qloveb,jjpmprice FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid." AND flag=1");
if($db->num_rows($rt)){
$row = $db->fetch_array($rt);
$qloveb = $row[0];
$jjpmprice = $row[1];
} else {
echo "&nbsp;&nbsp;<font color='#999999' style='font-size:12px'>".$_ZEAI['SiteName']."( <a href=".HOST.">".HOST."</a> )提示：</FONT><BR><BR>&nbsp;&nbsp;<font color='#FF0000' style='font-size:12px'>请求错误！</FONT><BR><BR><p align=center><input onclick='history.back();' type='button' value='返回'></p>";
exit;
}$jjpmprice = abs($jjpmprice);
if ($qloveb < $jjpmprice) {
$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET jjpmprice=0 WHERE id=".$mainid);
} else {
$jjpm_yzlove = 'groupyzlove'.$mainid;
if ($_COOKIE["$jjpm_yzlove"] != 'OK')$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET qloveb=qloveb-$jjpmprice WHERE id=".$mainid);
setcookie("$jjpm_yzlove",'OK');
}header("Location: ".$_ZEAI['group2']."/group_main.php?mainid=".$mainid);ob_end_flush();
?>

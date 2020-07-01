<?php 
require_once '../../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'sub/zeai_up_func.php';
$ret = wx_sent_kf_msg($oid,$C,$kind);
echo $ret;
?>
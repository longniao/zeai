<?php 
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
require_once '../sub/init.php';
if($submitok == 'getopenid' && ifint($uid)){
	$openid = wx_get_openid();
	if (str_len($openid) >10){
		require_once ZEAI.'sub/conn.php';
		$row = $db->ROW(__TBL_USER__,"id","openid='$openid'");
		if ($row){
			alert_adm('当前微信号已被其它帐号绑定，请使用其它微信扫码');
		}else{
			$db->query("UPDATE ".__TBL_USER__." SET openid='$openid' WHERE id=".$uid);
			alert_adm('绑定成功','-1');
		}
	}
}
?>


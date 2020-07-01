<?php
define('ZEAI_PHPV6',substr(dirname(__FILE__),0,-18));
require_once ZEAI_PHPV6.'sub/init.php';
require_once ZEAI.'sub/conn.php';
$rt=$db->query("SELECT id FROM ".__TBL_USER__." WHERE grade>1");
$total = $db->num_rows($rt);
if ($total > 0) {
	for($i=1;$i<=$total;$i++) {
		$rows = $db->fetch_array($rt,'num');
		if(!$rows) break;
		zeai_chk_ugrade($rows[0]);
	}
}
?>
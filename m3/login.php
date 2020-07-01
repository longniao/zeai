<?php 
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
header("Location: ".HOST."/m1/login.php?tmpid=".$tmpid);
?>
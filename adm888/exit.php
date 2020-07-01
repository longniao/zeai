<?php
session_start();
$jmpurl = "login.php";
unset($_SESSION['admuid']);
unset($_SESSION['admpwd']);
unset($_SESSION['admuname']);
unset($_SESSION['truename']);
unset($_SESSION['kind']);
unset($_SESSION['title']);
unset($_SESSION['crmkind']);
unset($_SESSION['agentid']);
unset($_SESSION['agenttitle']);
unset($_SESSION['agent_areaid']);
header("Location: $jmpurl");
?>
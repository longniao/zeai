<?php
require_once 'sub/init.php';
$FNAME = (empty($z))?'index':$z;
$FARR  = array('index','tg','tuijian','login','u','trend','store','dating','party','party_detail','video','index_more_big','hongniang','news','msg','my');
$FNAME = (!in_array($FNAME,$FARR))?'index':$FNAME;
if($z=='login'){
	if(is_mobile()){
		header("Location: ".HOST."/m1/login.php?jumpurl=".urlencode($jumpurl));
	}else{
		header("Location: ".HOST."/p1/login.php?jumpurl=".urlencode($jumpurl));
	}
}else{
	if(is_mobile()){
		if($FNAME=='index'){
			require_once ZEAI.'m'.$_ZEAI['mob_mbkind'].'/index.php';
		}else{
			require_once ZEAI.'/m1/'.$FNAME.'.php';
		}
	}else{
		switch ($FNAME) {
			case 'my':$zeaiurl=HOST."/p1/my.php";break;
			case 'index':require_once ZEAI.'/p1/'.$FNAME.'.php';exit;break;
		}
		header("Location: ".$zeaiurl);
	}
}
?>
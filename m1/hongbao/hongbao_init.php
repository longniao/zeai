<?php
$headmeta = HEADMETA;
$SELF    = $_SERVER['PHP_SELF'];
$ADDTIME = strtotime("now");
//红包
$_ZEAI['HB_refundtime']  = 3;//红包超过三天没领退款到余额,讨红包超过此天数才能再次发布一个

function tr_mouse($i,$id,$mode=''){
	global $_Style;
	$returnstr = '';
	$bg='';
	$bg1     = $_Style['list_bg1'];
	$bg2     = $_Style['list_bg2'];
	$overbg  = $_Style['list_overbg'];
	$selectbg= $_Style['list_selectbg'];
	if ($i % 2 == 0){
		$bg=$bg1;
	} else {
		$bg=$bg2;
	}
	$returnstr .= ' bgcolor='.$bg.' onmouseover="this.style.backgroundColor=\''.$overbg.'\'" onmouseout="this.style.backgroundColor=\''.$bg.'\'" ';
	if (empty($mode)){
		$returnstr .= " onclick=\"chkbox(".$i.",".$id.")\" id=\"tr".$i."\"";
	}
	return $returnstr;
}

function getpath_b($path_s){ 
	$str = str_replace("_s","_b",$path_s);
	return $str;
}
function getpath_s($path_b){ 
	$str = str_replace("_b","_s",$path_b);
	return $str;
}
function getpath_m($path_s){ 
	$str = str_replace("_s","_m",$path_s);
	return $str;
}
function geturl($ifall=true){
	if ($ifall){
		if (isset($_SERVER['argv'])){
			$uri = $_SERVER['PHP_SELF'] .(empty($_SERVER['argv'])?'':('?'. $_SERVER['argv'][0]));
		}else{
			$uri = $_SERVER['PHP_SELF'] .(empty($_SERVER['QUERY_STRING'])?'':('?'. $_SERVER['QUERY_STRING']));
		}
	}else{
		if (isset($_SERVER['argv'])){
			$uri = (empty($_SERVER['argv'])?'':($_SERVER['argv'][0]));
		}else{
			$uri = (empty($_SERVER['QUERY_STRING'])?'':($_SERVER['QUERY_STRING']));
		}
	}
	return $_SERVER['REQUEST_URI'] = $uri;
}
?>
<?php
$_ZEAI['SiteName'] = $_ZEAI['siteName'];
$_ZEAI['user_2domain'] = HOST."/u";
$_ZEAI['group2'] = HOST."/m1/group";
$headmeta = HEADMETA;

$SELF    = $_SERVER['PHP_SELF'];
$ADDTIME = strtotime("now");
//圈子
$_ZEAI['group2']   = HOST."/m1/group";
$_ZEAI['GroupWZbbsAdd']   = 0;//发表主题或评论，检查个人资料是否通过审核，如果未审，则不能发表，0为不检查
$_ZEAI['GroupWZLoveb']    = 100;//发表主题帖增圈子财富，0为不加币
$_ZEAI['GroupBBSLoveb']   = 10;//发表评论增圈子财富，0为不加币
$_ZEAI['GroupMailLoveb']  = 10000;//圈主给成员群发站内留言花费爱豆，0为免费发布(免费会导致大量数据库垃圾，性能下降，严重会可能会崩溃)

$_ZEAI['UpBsize'] = $_UP['upBsize'];
$_ZEAI['ifwaterimg']=$_UP['ifwaterimg'];


function date_format3($string, $format='%b %e, %Y', $default_date=null){
    if (substr(PHP_OS,0,3) == 'WIN') {
           $_win_from = array ('%e',  '%T',       '%D');
           $_win_to   = array ('%#d', '%H:%M:%S', '%m/%d/%y');
           $format = str_replace($_win_from, $_win_to, $format);
    }
    if($string != '') {
        return strftime($format, smarty_make_timestamp($string));
    } elseif (isset($default_date) && $default_date != '') {
        return strftime($format, smarty_make_timestamp($default_date));
    } else {
        return;
    }
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

?>
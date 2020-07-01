<?php
require_once '../sub/init.php';
$currfields = 'grade,openid,photo_s,photo_f,RZ,myinfobfb,dataflag,weixin_pic';
$a=($a=='mate')?'data':$a;

$$rtn='json';$chk_u_jumpurl=HOST.'/?z=my&e=my_info&a='.$a.'&i='.$i;require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/udata.php';$extifshow = json_decode($_UDATA['extifshow'],true);
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'sub/zeai_up_func.php';
//
switch ($submitok) {
	case 'ajax_fximg':		
		$dbpicname = setVideoDBname('tmp','fx_'.$upuid,'png');
		$file_temp = substr($imgdata,22); //百度一下就可以知道base64前面一段需要清除掉才能用。
		$file = base64_decode($file_temp);
		up_send_stream($file,$dbpicname,0,$_UP['upBsize']);		
		json_exit(array('flag'=>1,'msg'=>'上传成功','path'=>$_ZEAI['up2'].'/'.$dbpicname));
	break;
	default:json_exit(array('flag'=>0,'msg'=>'无参数'));
	break;
}
?>
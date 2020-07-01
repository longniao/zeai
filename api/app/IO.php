<?php
require_once '../../sub/init.php';
require_once ZEAI.'sub/conn.php';
if($submitok=="ajax_geticonum"){
	$ifuid = (ifint($uid) && $uid>0)?true:false;
	if($ifuid){
		$cxuid=$uid;
	}else{
		$iflogin = (ifint($cook_uid) && (str_len($cook_pwd)==32 || str_len($cook_pwd)==16))?true:false;
		if($iflogin){$cxuid=$cook_uid;}
	}
		
	if($cxuid>0){
		$num_tz0 = $db->COUNT(__TBL_TIP__,"new=1 AND uid=".$cxuid);
		$num_sx0 = $db->COUNT(__TBL_MSG__,"new=1 AND ifdel=0 AND uid=".$cxuid);
		$znum = $num_tz0+$num_sx0;		
		$num_tz = $db->COUNT(__TBL_TIP__,"new=1 and push_status=1 AND uid=".$cxuid);
		$num_sx = $db->COUNT(__TBL_MSG__,"new=1 and push_status=1 AND ifdel=0 AND uid=".$cxuid);
		$tipnum = $num_tz+$num_sx;
		$db->query("UPDATE ".__TBL_TIP__." SET push_status=0 WHERE new=1 and push_status=1 AND uid=".$cxuid);
		$db->query("UPDATE ".__TBL_MSG__." SET push_status=0 WHERE new=1 and push_status=1 AND ifdel=0 AND uid=".$cxuid);
		echo json_encode(array('status'=>'OK','msg'=>'Success','num_tz'=>intval($num_tz),'num_sx'=>intval($num_sx),'tipnum'=>intval($tipnum),'znum'=>intval($znum)));
	}else{
		echo json_encode(array('status'=>'OK','msg'=>'You are not recorded','num_tz'=>0,'num_sx'=>0,'tipnum'=>0,'znum'=>0));
	}
}else{
	echo json_encode(array('status'=>'OK','msg'=>'Missing Parameters','num_tz'=>0,'num_sx'=>0,'tipnum'=>0,'znum'=>0));
}
exit;
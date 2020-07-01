<?php
!function_exists('zeai_alone') && exit('forbidden');
require_once ZEAI.'sub/conn.php';
$loginurl = HOST.'/m1/login.php';
$iflogin = (ifint($cook_uid) && (str_len($cook_pwd)==32 || str_len($cook_pwd)==16))?true:false;
if ($iflogin){
	$currfields = (empty($currfields))?"":",".$currfields;
	$row = $db->ROW(__TBL_USER__,"flag,kind".$currfields,"id=".$cook_uid." AND pwd='".$cook_pwd."'  ",'name');
	if ($row){
		$iflogin = true;
		if ($row['flag']==2){
			if(is_mobile()){
				if($zeai_cn__chk_u=='json'){
					json_exit(array('flag'=>'nologin','msg'=>'请先登录后再操作','jumpurl'=>$loginurl));
				}else{
					require_once ZEAI.'cache/config_reg.php';
					$jmpurl=($_REG['reg_style']==1)?'reg_alone':'reg_diy';
					header("Location: ".HOST."/m1/".$jmpurl.".php");
				}
			}else{
				callmsg('您的帐号还没有注册成功',HOST);
			}
		//}elseif($row['flag']!=1){
		}elseif($row['flag']==0){
			ZEclearcookAI_CN();
			$flagstr = '您已注册成功，请等待我们审核';
			if(strpos($submitok,'ajax_') !== false){
				json_exit(array('flag'=>'nologin','msg'=>$flagstr));
			}else{
				callmsg($flagstr,HOST);
			}
		}elseif($row['flag']==-1){
			ZEclearcookAI_CN();
			$flagstr = '您的帐号UID：'.$cook_uid.'已被锁定或注销，请联系管理员';
			if(strpos($submitok,'ajax_') !== false){
				json_exit(array('flag'=>'nologin','msg'=>$flagstr));
			}else{
				callmsg($flagstr,HOST);
			}
		}
		if ($row['kind']==2){
			if($zeai_cn__chk_u=='json'){
				json_exit(array('flag'=>0,'msg'=>'您是线下会员，不能线上操作'));
			}else{
				callmsg('您是线下会员，不能线上操作',HOST);
			}
		}
	}else{
		$iflogin = false;
		ZEclearcookAI_CN();
	}
}
if (!$iflogin){
	if($zeai_cn__chk_u=='json' && $m!='wap'){
		json_exit(array('flag'=>'nologin','msg'=>'请先登录后再操作','jumpurl'=>$chk_u_jumpurl));
	}else{
		if( is_weixin() && is_mobile()){
			$cook_openid = wx_get_openid();
			$row = $db->ROW(__TBL_USER__,"id,uname,nickname,pwd,sex,photo_s,grade,birthday"," openid<>'' AND openid='".$cook_openid."'","num");
			if ($row){
				$loginip=getip();
				$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",endip='$loginip',logincount=logincount+1 WHERE id=".$row[0]);
				setcookie("cook_uid",$row[0],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_uname",dataIO($row[1],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_nickname",dataIO($row[2],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_pwd",$row[3],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_sex",$row[4],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_photo_s",$row[5],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_grade",$row[6],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_birthday",$row[7],time()+720000,"/",$_ZEAI['CookDomain']);
				$jumpurl = urldecode(HOST.$_SERVER['REQUEST_URI']);
				$jumpurl = (empty($jumpurl))?HOST.'/?z=my':$jumpurl;
				header("Location: ".$jumpurl);
			}else{
				if(!empty($cook_openid)){
					require_once ZEAI.'cache/config_reg.php';
					$jmpurl=($_REG['reg_style']==1)?'reg_alone':'reg_diy';
					header("Location: ".HOST."/m1/".$jmpurl.".php");
					//header("Location: ".$loginurl."?jumpurl=".urlencode($_SERVER['REQUEST_URI']));
				}
			}
		}else{
			header("Location: ".$loginurl."?jumpurl=".urlencode($_SERVER['REQUEST_URI']));
		}
	}
}
?>
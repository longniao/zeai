<?php 
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_shop.php';
$TG_set = json_decode($_REG['TG_set'],true);
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$rowtg  = shop_chk();
if($rowtg){
	$cook_tg_uid   = $rowtg['id'];
	$cook_tg_flag  = $rowtg['flag'];
	$cook_tg_shopgrade = intval($rowtg['shopgrade']);
	$cook_tg_shopflag  = intval($rowtg['shopflag']);
	/*	
	if($cook_tg_shopflag==0 || $cook_tg_shopflag==-1){
		header("Location: shop_my_flag.php");
	}elseif($cook_tg_flag==2){
		header("Location: shop_my_vip.php");
	}
	*/	
	
}else{
	/*
	$currfields = 'uname,pwd,mob,RZ,openid,subscribe,weixin,qq,aboutus,areaid,areatitle,nickname,photo_s,tguid';
	$ifadd=false;
	$rowU = shop_chk_u();
	if($rowU){
		$mob= $rowU['mob'];
		$uname=$rowU['uname'];
		$pwd  = $rowU['pwd'];
		$openid    =$rowU['openid'];
		$subscribe =$rowU['subscribe'];
		$RZ      = $rowU['RZ'];$RZarr = explode(',',$RZ);
		$weixin  = $rowU['weixin'];
		$qq      = $rowU['qq'];
		$aboutus = $rowU['aboutus'];
		$areaid  = $rowU['areaid'];
		$areatitle = $rowU['areatitle'];
		$nickname  = $rowU['nickname'];
		$photo_s   = $rowU['photo_s'];
		$U_tguid   = intval($rowU['tguid']);
		if(ifmob($mob) && in_array('mob',$RZarr)){
			$rowtg2 = $db->ROW(__TBL_TG_USER__,"id,uname,mob,pwd","mob='$mob' AND FIND_IN_SET('mob',RZ)","name");
			if($rowtg2){
				$cook_tg_uid   = $rowtg2['id'];
				$cook_tg_uname = $rowtg2['uname'];
				$cook_tg_mob   = $rowtg2['mob'];
				$cook_tg_pwd   = $rowtg2['pwd'];
				setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_mob",$cook_tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
			}else{
				$ifadd=true;
			}
		}else{
			$ifadd=true;
		}
		//
		if($ifadd){
			$flag   = ($TG_set['regflag'] == 1)?0:1;
			$row2   = $db->ROW(__TBL_TG_ROLE__,"grade,title","shopgrade=0 AND ifdefault=1","num");
			$grade  = $row2[0];
			$gradetitle = $row2[1];
			$sjtime = ADDTIME;
			$ip     =getip();
			$kind   = 1;
			if($TG_set['active_price']>0)$flag=2;
			//
			$row2 = $db->ROW(__TBL_TG_USER__,"id","uname='$uname'");
			if($row2)$uname=$cook_uid;
			//
			if(!empty($photo_s)){
				$dbdir  = 'p/tg/'.date('Y').'/'.date('m').'/';
				@mk_dir(ZEAI.'/up/'.$dbdir);
				//
				$old_s = $photo_s;
				$old_m = smb($photo_s,'m');
				$old_b = smb($photo_s,'b');
				$old_blur = smb($photo_s,'blur');
				//
				$oldDST_s = ZEAI.'/up/'.$old_s;
				$oldDST_m = ZEAI.'/up/'.$old_m;
				$oldDST_b = ZEAI.'/up/'.$old_b;
				//$oldDST_blur = ZEAI.'/up/'.$old_blur;
				//
				$newDST_s = ZEAI.'/up/'.$dbdir.basename($oldDST_s);
				$newDST_m = ZEAI.'/up/'.$dbdir.basename($oldDST_m);
				$newDST_b = ZEAI.'/up/'.$dbdir.basename($oldDST_b);
				//$newDST_blur = ZEAI.'/up/'.$dbdir.basename($oldDST_blur);
				//
				@copy($oldDST_s,$newDST_s);
				@copy($oldDST_m,$newDST_m);
				@copy($oldDST_b,$newDST_b);
				//@copy($oldDST_blur,$newDST_blur);
				$new_photo_s = $dbdir.basename($oldDST_s);
			}
			//
			$db->query("INSERT INTO ".__TBL_TG_USER__." (tguid,uid,uname,nickname,flag,pwd,grade,gradetitle,regtime,endtime,regip,endip,kind,openid,subscribe,qq,weixin,content,areaid,areatitle,photo_s) VALUES ($U_tguid,$cook_uid,'$uname','$nickname',$flag,'".$pwd."',$grade,'$gradetitle',".ADDTIME.",".ADDTIME.",'$ip','$ip',$kind,'$openid','$subscribe','$qq','$weixin','$aboutus','$areaid','$areatitle','$new_photo_s')");
			$cook_tg_uid = intval($db->insert_id());
			if(ifmob($mob) && in_array('mob',$RZarr)){
				$db->query("UPDATE ".__TBL_TG_USER__." SET mob='$mob',RZ='mob' WHERE id=".$cook_tg_uid);
			}
			$cook_tg_pwd = $pwd;
			$cook_tg_openid= $openid;
			setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_openid",$cook_tg_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
		}
	}else{
		header("Location: ".HOST."/m1/tg_login.php?loginkind=shop&jumpurl=".urlencode(HOST.'/m4/shop_my.php'));
	}
	*/
}
$nav = 'shop_my';
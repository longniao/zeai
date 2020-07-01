<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
require_once ZEAI.'sub/conn.php';
if(!empty($t) && !ifint($cook_uid)){header("Location: ".HOST."/?z=my");}
if (empty($cook_tmp_openid) && empty($t) && is_weixin()){
	$cook_tmp_openid=wx_get_openid(0);
	setcookie('cook_tmp_openid',$cook_tmp_openid,time()+720000,'/',$_ZEAI['CookDomain']);
}
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
require_once ZEAI.'sub/TGfun.php';
if (!is_mobile())exit('请用手机打开');
if (ini_get('session.auto_start') == 0)session_start();
header("Cache-control: private");
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
$ip=getip();
$rt = $db->query("SELECT id FROM ".__TBL_IP__." WHERE ipurl='$ip'");
if($db->num_rows($rt))exit(JSON_ERROR);
$reg_loveb = abs(intval($_REG['reg_loveb']));
$reg_grade = (ifint($_REG['reg_grade']) && $_REG['reg_grade']<=10)?intval($_REG['reg_grade']):1;
$reg_if2   = 999;
$flag  =($_REG['reg_flag']==1)?1:0;

if(str_len($cook_tmp_openid) >15){
	$row = $db->ROW(__TBL_USER__,"id,uname,nickname,pwd,sex,photo_s,grade,birthday,flag,love","openid='$cook_tmp_openid'","name");
	if ($row){
		if($row['flag']==1){
			setcookie("cook_uid",$row['id'],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_uname",dataIO($row['uname'],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_nickname",dataIO($row['nickname'],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_pwd",$row['pwd'],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_sex",$row['sex'],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_photo_s",$row['photo_s'],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_grade",$row['grade'],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_birthday",$row['birthday'],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_openid",$cook_tmp_openid,time()+720000,"/",$_ZEAI['CookDomain']);
			if(!empty($submitok)){
				json_exit(array('flag'=>'logined','msg'=>'您微信已经注册过，请不要重复注册'));
			}else{
				//callmsg('您微信已经注册，自动登录中',HOST);
				header("Location: ".HOST."/?z=my");
			}
		}elseif($row['flag']==2){
			setcookie("cook_uid",$row['id'],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_pwd",$row['pwd'],time()+720000,"/",$_ZEAI['CookDomain']);
			//header("Location: reg_diy.php");
		}
	}
}
$ifreg2=false;
if (ifint($cook_uid) && !empty($cook_pwd)){
	$fld=$_REG['reg_data'];
	$reg_dataARR = explode(',',$_REG['reg_data']);
	if(in_array('mate',$reg_dataARR))$fld=str_replace("mate","mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2",$fld);
	if(in_array('kefu',$reg_dataARR))$fld=str_replace("kefu","click",$fld);
	if(!empty($fld))$fld=','.$fld;
	$row = $db->ROW(__TBL_USER__,"flag,sex,mob,uname,areatitle".$fld,"id=".$cook_uid." AND pwd='".$cook_pwd."'  ",'name');
	if ($row){
		if ($row['flag']==1){
			if(!empty($submitok)){
				json_exit(array('flag'=>'logined','msg'=>'您已经注册过，请不要重复注册'));
			}else{
				header("Location: ".HOST."/?z=my");
			}
		}elseif($row['flag']==0){
			if(!empty($submitok)){
				json_exit(array('flag'=>0,'msg'=>'请等待审核'));	
			}else{
				callmsg('请等待审核',HOST);
			}
		}
		$ifreg2=true;
		if($_REG['reg_kind']==1 || $_REG['reg_kind']==3){
			if(!ifmob($row['mob'])){
				$ifreg2=false;	
			}
			$cook_uname=dataIO($row['mob'],'out');
		}else{
			
			if( strstr(dataIO($row['uname'],'out'),'wxgz_') ){
				$ifreg2=false;
			}			
			$cook_uname=dataIO($row['uname'],'out');
		}
		$cook_sex=$row['sex'];
		$cook_photo_s=$row['photo_s'];
		$cook_birthday=str_replace("0000-00-00","",$row['birthday']);
		$cook_love = intval($row['love']);
		$cook_pay  = intval($row['pay']);
		$cook_heigh = intval($row['heigh']);
		$cook_edu = intval($row['edu']);
		$cook_house = intval($row['house']);
		$cook_car = intval($row['car']);
		$cook_weigh = intval($row['weigh']);
		$cook_job = intval($row['job']);
		$cook_child = intval($row['child']);
		$cook_areaid=dataIO($row['areaid'],'out');
		$cook_areatitle=dataIO($row['areatitle'],'out');
		$cook_area2id=dataIO($row['area2id'],'out');
		$cook_area2title=dataIO($row['area2title'],'out');
		$cook_parent = intval($row['parent']);
		$cook_nickname=dataIO($row['nickname'],'out');
		$cook_weixin=dataIO($row['weixin'],'out');
		$cook_photo_s=dataIO($row['photo_s'],'out');
		$cook_truename=dataIO($row['truename'],'out');
		$cook_aboutus=dataIO($row['aboutus'],'out');
		$cook_marrytime = intval($row['marrytime']);
		$cook_identitynum =dataIO($row['identitynum'],'out');
		$cook_click       =intval($row['click']);
		//
		$cook_mate_age1      = intval($row['mate_age1']);
		$cook_mate_age2      = intval($row['mate_age2']);
		$cook_mate_heigh1    = intval($row['mate_heigh1']);
		$cook_mate_heigh2    = intval($row['mate_heigh2']);
		$cook_mate_pay       = $row['mate_pay'];
		$cook_mate_edu       = $row['mate_edu'];
		$cook_mate_areaid    = $row['mate_areaid'];
		$cook_mate_areatitle = $row['mate_areatitle'];
		$cook_mate_love      = $row['mate_love'];
		$cook_mate_car       = $row['mate_car'];
		$cook_mate_house     = $row['mate_house'];
		$cook_mate_weigh1      = intval($row['mate_weigh1']);
		$cook_mate_weigh2      = intval($row['mate_weigh2']);
		$cook_mate_job         = $row['mate_job'];
		$cook_mate_child       = $row['mate_child'];
		$cook_mate_marrytime   = $row['mate_marrytime'];
		$cook_mate_companykind = $row['mate_companykind'];
		$cook_mate_smoking     = $row['mate_smoking'];
		$cook_mate_drink       = $row['mate_drink'];
		$cook_mate_areaid2     = $row['mate_areaid2'];
		$cook_mate_areatitle2  = $row['mate_areatitle2'];
		//
		$cook_mate_age       = $cook_mate_age1.','.$cook_mate_age2;
		$cook_mate_age_str   = mateset_out($cook_mate_age1,$cook_mate_age2,'岁');
		$cook_mate_age_str = str_replace("不限","",$cook_mate_age_str);
		$cook_mate_heigh     = $cook_mate_heigh1.','.$cook_mate_heigh2;
		$cook_mate_heigh_str = mateset_out($cook_mate_heigh1,$cook_mate_heigh2,'cm');
		$cook_mate_heigh_str = str_replace("不限","",$cook_mate_heigh_str);
		$cook_mate_weigh     = $cook_mate_weigh1.','.$cook_mate_weigh2;
		$cook_mate_weigh_str = mateset_out($cook_mate_weigh1,$cook_mate_weigh2,'kg');
		$cook_mate_weigh_str = str_replace("不限","",$cook_mate_weigh_str);
		$cook_mate_areaid_str  = (!empty($cook_mate_areatitle))?$cook_mate_areatitle:'';
		$cook_mate_areaid2_str = (!empty($cook_mate_areatitle2))?$cook_mate_areatitle2:'';
		$cook_mate_pay_str   = udata('pay',$cook_mate_pay);
		$cook_mate_edu_str   = udata('edu',$cook_mate_edu);
		$cook_mate_love_str  = udata('love',$cook_mate_love);
		$cook_mate_car_str   = udata('car',$cook_mate_car);
		$cook_mate_house_str = udata('house',$cook_mate_house);
		$cook_mate_job_str         = udata('job',$cook_mate_job);
		$cook_mate_child_str       = udata('child',$cook_mate_child);
		$cook_mate_marrytime_str   = udata('marrytime',$cook_mate_marrytime);
		$cook_mate_companykind_str = udata('companykind',$cook_mate_companykind);
		$cook_mate_smoking_str     = udata('smoking',$cook_mate_smoking);
		$cook_mate_drink_str       = udata('drink',$cook_mate_drink);
		$cook_mate_age = ($cook_mate_age != '0,0')?$cook_mate_age:'23,40';
		$cook_mate_heigh = ($cook_mate_heigh != '0,0')?$cook_mate_heigh:'160,175';
	}else{
		if ($row['flag']==1)json_exit(array('flag'=>'logined','msg'=>'请不要重复提交'));
	}
}
switch ($submitok) {
	case 'ajax_get_verify':
		if ($_REG['reg_kind']==2)exit(JSON_ERROR);
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}else{
			if ($db->ROW(__TBL_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)"))json_exit(array('flag'=>0,'msg'=>'此手机号码已被注册，请更换'));
		}
		//有用户名reg_kind=3
		if ( ($Temp_regyzmrenum > $_SMS['sms_yzmnum']) && $_SMS['sms_yzmnum']>0 )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
		$_SESSION['Zeai_cn__mobyzm'] = cdstr(4);
		//sms
		$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__mobyzm']);
		if ($rtn == 0){
			setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,time()+720000,"/",$_ZEAI['CookDomain']);  
			$chkflag = 1;
			$content = '验证码发送成功，请注意查收';
		}else{
			$chkflag = 0;
			$content = "错误码：$rtn"."-".sms_error($rtn);
		}
		//sms end
		$_SESSION['Zeai_cn__mob'] = $mob;
		json_exit(array('flag'=>$chkflag,'msg'=>$content));
	break;
	case 'ajax_uname_addupdate':
		if ($_REG['reg_flag']==3)json_exit(array('flag'=>0,'msg'=>'本站已关闭【新用户注册】'));
		$REG=json_decode($REG,true);
		$pwd = md5(trim($REG['pwd']));
		/**************** 注册数据 ******************/
		if ($_REG['reg_kind']==1 || $_REG['reg_kind']==3){
			
			if (empty($_SESSION['Zeai_cn__mobyzm'])){
				json_exit(array('flag'=>0,'msg'=>'短信验证码错误，请重新获取'));
			}else{
				if ($_SESSION['Zeai_cn__mobyzm'] != $verify){
					json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
				}
				if ($_SESSION['Zeai_cn__mob'] != $mob && ifmob($mob)){
					unset($_SESSION["Zeai_cn__mob"]);
					json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
				}
			}
			$mob = $REG['mob'];
			if (!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请输入正确的手机号码'));
			$row = $db->ROW(__TBL_USER__,'id,uname,nickname,pwd,sex,photo_s,grade,birthday',"uname='".$mob."' OR (mob='$mob' AND FIND_IN_SET('mob',RZ)) ","name");
			if($row){
				if($row['pwd']==$pwd){
					setcookie("cook_uid",$row['id'],time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_pwd",$row['pwd'],time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_sex",$row['sex'],time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_photo_s",$row['photo_s'],time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_uname",dataIO($row['uname'],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_nickname",dataIO($row['nickname'],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_grade",$row['grade'],time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_birthday",$row['birthday'],time()+720000,"/",$_ZEAI['CookDomain']);
					$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",endip='$loginip',logincount=logincount+1 WHERE id=".$row['id']);
					unset($_SESSION["Zeai_cn__mobyzm"]);
					unset($_SESSION["Zeai_cn__mob"]);
					json_exit(array('flag'=>'logined','msg'=>'登录成功'));
				}else{
					json_exit(array('flag'=>0,'msg'=>'此手机已被注册,请重新输入'));	
				}
			}
			//
		}
		if($_REG['reg_kind']==2 || $_REG['reg_kind']==3){
			$uname = trimhtml(dataIO($REG['uname'],'in'));
			if (str_len($uname) > 20 || str_len($uname) < 3)json_exit(array('flag'=>0,'msg'=>'请输入正确的用户名（3~20位字母或加数字组合）'));
			if (ifmob($uname) || ifint($uname))json_exit(array('flag'=>0,'msg'=>'登录用户名不能是手机号码和纯数字'));
			if (str_len($uname) > 20 || str_len($uname)<3 || !preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u',$uname) )json_exit(array('flag'=>0,'msg'=>'请输入正确的用户名（3~20位字母或加数字组合）'));
			$row = $db->ROW(__TBL_USER__,'id',"uname='".$uname."' OR (mob='$uname' AND FIND_IN_SET('mob',RZ)) ");
			if($row)json_exit(array('flag'=>0,'msg'=>'此用户名已被注册,请重新输入'));
		}
		switch ($_REG['reg_kind']){
			case 1:$uname = 'u'.cdstr(5);break;
			case 2:$mob = 0;break;
		}
		//
		$regkind = 6;
		$tguid   = intval($tguid);
		$subscribe = intval($subscribe);
		//$pwd     = md5($pwd);
		$flag    = 2;
		/**************** 入库 ******************/
		//先关注，后注册
		if (ifint($cook_uid) && !empty($cook_pwd)){
			$row = $db->ROW(__TBL_USER__,"id","id=".$cook_uid." AND pwd='".$cook_pwd."'  ",'name');
			if($row){
				$SQL = "pwd='$pwd',sex=0,photo_s='',photo_f=0,nickname=''";
				if(($_REG['reg_kind']==1 || $_REG['reg_kind']==3)){
					$SQL .= ",mob='$mob',RZ='mob'";
				}else{
					$SQL .= ",uname='$uname'";
				}
				$db->query("UPDATE ".__TBL_USER__." SET ".$SQL." WHERE id=".$cook_uid);
				setcookie("cook_pwd",$pwd,time()+720000,"/",$_ZEAI['CookDomain']);
				json_exit(array('flag'=>1,'msg'=>'注册成功'));
			}
		}
		//gyl_debug('subscribe='.$subscribe);
		//
		$db->query("INSERT INTO ".__TBL_USER__." (subscribe,flag,uname,pwd,grade,if2,regtime,endtime,regip,endip,refresh_time,regkind,tguid,openid) VALUES ($subscribe,$flag,'".$uname."','".$pwd."',$reg_grade,$reg_if2,".ADDTIME.",".ADDTIME.",'$ip','$ip',".ADDTIME.",$regkind,$tguid,'$cook_tmp_openid')");
		$uid = intval($db->insert_id());
		if(ifmob($mob) && ($_REG['reg_kind']==1 || $_REG['reg_kind']==3)){
			$db->query("UPDATE ".__TBL_USER__." SET mob='$mob',RZ='mob' WHERE id=".$uid);
		}
		/**************** 第三方 ******************/
		if(ifint($tmpid)){
			$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid,"num");
			if ($row){
				$c        = json_decode($row[0],true);
				$regkind  = $c['regkind'];
				/**** QQ ******/
				if($regkind=='qq'){
					$regkind  = 4;
					$loginkey = $c['openid'];
					$nickname = dataIO($c['nickname'],'in',20);
					//$dbname = (!empty($c['photo_s']))?wx_get_uinfo_logo($c['photo_s'],$uid):'';
					//$photo_s=setpath_s($dbname);
					$db->query("UPDATE ".__TBL_USER__." SET regkind=$regkind,loginkey='$loginkey',nickname='$nickname' WHERE id=".$uid);//,photo_s='$photo_s'
				/**** weixin ****/
				}elseif($regkind=='weixin'){
					$regkind=3;
					$openid   = $c['openid'];
					$nickname = dataIO($c['nickname'],'in',20);
					$unionid  = $c['unionid'];
					//$dbname = (!empty($c['headimgurl']))?wx_get_uinfo_logo($c['headimgurl'],$uid):'';
					//$photo_s=setpath_s($dbname);
					$db->query("UPDATE ".__TBL_USER__." SET regkind=$regkind,openid='$openid',unionid='$unionid',nickname='$nickname' WHERE id=".$uid);//,photo_s='$photo_s'
				}elseif($regkind=='app'){
					$regkind=8;
					$unionid  = $c['unionid'];
					$db->query("UPDATE ".__TBL_USER__." SET regkind=$regkind,unionid='$unionid' WHERE id=".$uid);//,photo_s='$photo_s'
				}
			}
			$db->query("DELETE FROM ".__TBL_TMP__." WHERE id=".$tmpid);
		}
		setcookie("cook_uid",$uid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_pwd",$pwd,time()+720000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'注册成功'));
	break;
	case 'ajax_chk_mate':
		if (ifint($cook_uid) && !empty($cook_pwd)){
			$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2","id=".$cook_uid." AND pwd='".$cook_pwd."' ",'name');
			if ($row){
				$mate_diy = explode(',',$_ZEAI['mate_diy']);
				$mate_age1      = intval($row['mate_age1']);
				$mate_age2      = intval($row['mate_age2']);
				$mate_heigh1    = intval($row['mate_heigh1']);
				$mate_heigh2    = intval($row['mate_heigh2']);
				$mate_weigh1      = intval($row['mate_weigh1']);
				$mate_weigh2      = intval($row['mate_weigh2']);
				$mate_pay       = $row['mate_pay'];
				$mate_edu       = $row['mate_edu'];
				$mate_areaid    = $row['mate_areaid'];
				$mate_love      = $row['mate_love'];
				$mate_car       = $row['mate_car'];
				$mate_house     = $row['mate_house'];
				$mate_job         = $row['mate_job'];
				$mate_child       = $row['mate_child'];
				$mate_marrytime   = $row['mate_marrytime'];
				$mate_companykind = $row['mate_companykind'];
				$mate_smoking     = $row['mate_smoking'];
				$mate_drink       = $row['mate_drink'];
				$mate_areaid2     = $row['mate_areaid2'];
				//
				$mate_age         = $mate_age1.','.$mate_age2;
				$mate_heigh       = $mate_heigh1.','.$mate_heigh2;
				$mate_weigh       = $mate_weigh1.','.$mate_weigh2;
				$mate_age = str_replace("0,0","",$mate_age);
				$mate_heigh = str_replace("0,0","",$mate_heigh);
				$mate_weigh = str_replace("0,0","",$mate_weigh);
				foreach ($mate_diy as $vv) {
					$tmp8 = 'mate_'.$vv;
					$mate_data = $$tmp8;
					if( empty($mate_data) )json_exit(array('flag'=>0,'msg'=>'请选择【择偶要求】*每项必填*'));
				}
				json_exit(array('flag'=>1,'msg'=>'下一步'));
			}
			json_exit(array('flag'=>0,'msg'=>'请先登录'));
		}else{
			json_exit(array('flag'=>1,'msg'=>'请先登录'));
		}
	break;
	case 'ajax_next':
		//if(empty($cook_sex))json_exit(array('flag'=>1,'msg'=>'请先注册','url'=>'sex'));
		if(empty($_REG['reg_data'])){
			$row = $db->ROW(__TBL_USER__,"tguid,sex,uname,tguid,flag,sex,grade,birthday,nickname".$fld,"id=".$cook_uid." AND pwd='".$cook_pwd."' ",'name');
			$tguid = $row['tguid'];
			$flag  = $row['flag'];
			$uname =dataIO($row['uname'],'out');
			$grade = intval($row['grade']);
			$sex   = intval($row['sex']);
			$birthday=str_replace("0000-00-00","",$row['birthday']);
			$nickname=dataIO($row['nickname'],'out');
			if($flag==1)json_exit(array('flag'=>1,'msg'=>'注册成功','url'=>'my'));
			if(empty($sex))json_exit(array('flag'=>1,'msg'=>'注册成功','url'=>'sex'));
			reg_addupdate();
			json_exit(array('flag'=>1,'msg'=>'请先注册','url'=>'my'));	
			
		}
		if (ifint($cook_uid) && !empty($cook_pwd)){
			$fld=$_REG['reg_data'];
			$reg_dataARR = explode(',',$_REG['reg_data']);
			if(in_array('mate',$reg_dataARR))$fld=str_replace("mate","mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2",$fld);
			if(in_array('kefu',$reg_dataARR))$fld=str_replace("kefu","click",$fld);
			
			
			if(!empty($fld))$fld=','.$fld;
			$row = $db->ROW(__TBL_USER__,"uname,tguid,flag,sex,grade".$fld,"id=".$cook_uid." AND pwd='".$cook_pwd."' ",'name');
			if ($row){
				$tguid = $row['tguid'];
				$flag  = $row['flag'];
				$uname =dataIO($row['uname'],'out');
				$grade = intval($row['grade']);
				$sex   = intval($row['sex']);
				if($flag==1)json_exit(array('flag'=>1,'msg'=>'注册成功','url'=>'my'));
				if(empty($sex))json_exit(array('flag'=>1,'msg'=>'注册成功','url'=>'sex'));
				//
				$birthday=str_replace("0000-00-00","",$row['birthday']);
				$love = intval($row['love']);
				$pay  = intval($row['pay']);
				$heigh = intval($row['heigh']);
				$edu = intval($row['edu']);
				$house = intval($row['house']);
				$car = intval($row['car']);
				$weigh = intval($row['weigh']);
				$job = intval($row['job']);
				$child  = intval($row['child']);
				$parent = intval($row['parent']);
				$areaid=dataIO($row['areaid'],'out');
				$area2id=dataIO($row['area2id'],'out');
				$nickname=dataIO($row['nickname'],'out');
				$weixin=dataIO($row['weixin'],'out');
				$photo_s=dataIO($row['photo_s'],'out');
				$truename=dataIO($row['truename'],'out');
				$aboutus=dataIO($row['aboutus'],'out');
				$marrytime = intval($row['marrytime']);
				$identitynum =dataIO($row['identitynum'],'out');
				$kefu = intval($row['click']);
				if (count($reg_dataARR) >= 1 && is_array($reg_dataARR)){
					foreach ($reg_dataARR as $k=>$V) {
						if($V=='mate'){
							$mate_diy = explode(',',$_ZEAI['mate_diy']);
							if (count($mate_diy) >= 1 && is_array($mate_diy)){
								$mate_age1      = intval($row['mate_age1']);
								$mate_age2      = intval($row['mate_age2']);
								$mate_heigh1    = intval($row['mate_heigh1']);
								$mate_heigh2    = intval($row['mate_heigh2']);
								$mate_weigh1      = intval($row['mate_weigh1']);
								$mate_weigh2      = intval($row['mate_weigh2']);
								$mate_pay       = $row['mate_pay'];
								$mate_edu       = $row['mate_edu'];
								$mate_areaid    = $row['mate_areaid'];
								$mate_love      = $row['mate_love'];
								$mate_car       = $row['mate_car'];
								$mate_house     = $row['mate_house'];
								$mate_job         = $row['mate_job'];
								$mate_child       = $row['mate_child'];
								$mate_marrytime   = $row['mate_marrytime'];
								$mate_companykind = $row['mate_companykind'];
								$mate_smoking     = $row['mate_smoking'];
								$mate_drink       = $row['mate_drink'];
								$mate_areaid2     = $row['mate_areaid2'];
								//
								$mate_age         = $mate_age1.','.$mate_age2;
								$mate_heigh       = $mate_heigh1.','.$mate_heigh2;
								$mate_weigh       = $mate_weigh1.','.$mate_weigh2;
								$mate_age = str_replace("0,0","",$mate_age);
								$mate_heigh = str_replace("0,0","",$mate_heigh);
								$mate_weigh = str_replace("0,0","",$mate_weigh);
								foreach ($mate_diy as $vv) {
									$tmp8 = 'mate_'.$vv;
									$mate_data = $$tmp8;
									if( empty($mate_data) )json_exit(array('flag'=>1,'msg'=>'请先注册','url'=>$V));
								}
							}
						}elseif($V=='kefu'){
							if( $kefu<=0 )json_exit(array('flag'=>1,'msg'=>'请先加客服微信','url'=>$V));
						}else{
							if(empty($$V))json_exit(array('flag'=>1,'msg'=>'请先注册','url'=>$V));
						}
					}
				}
				//
				reg_addupdate();
				json_exit(array('flag'=>1,'msg'=>'请先注册','url'=>'my'));
			}else{
				json_exit(array('flag'=>0,'msg'=>'请先注册'));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'请先注册'));
		}
	break;
	case 'ajax_data_save':
		if (empty($f) || empty($v))json_exit(array('flag'=>2,'msg'=>'请输入或选择内容'));
		//if (empty($f) || empty($v))exit(JSON_ERROR);
		if (!ifint($cook_uid) || empty($cook_pwd))json_exit(array('flag'=>0,'msg'=>'请先登录'));
		$v=urldecode($v);$t=urldecode($t);
		if($f=='areaid')$SQL=",areatitle='".$t."'";
		if($f=='area2id')$SQL=",area2title='".$t."'";
		if($f=='sex'){setcookie("cook_sex",$v,time()+720000,"/",$_ZEAI['CookDomain']);}
		//if($f=='kefu')$SQL=",click=".$t;
		$db->query("UPDATE ".__TBL_USER__." SET $f='$v'".$SQL." WHERE id=".$cook_uid);
		set_data_ed_bfb($cook_uid);
		json_exit(array('flag'=>1,'msg'=>'修改成功'));	
	break;
	case "ajax_photo_s_up_app":
			$file=$_FILES['file'];
			$dbname = setphotodbname('m',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$shFlag = $switch['sh']['photom_'.$data_grade];
			$photo_f  = ($shFlag == 1)?1:0;
			$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
			set_data_ed_bfb($cook_uid);
			$path_b = getpath_smb($cook_photo_s,'b');
			if(!empty($cook_photo_s))@up_send_userdel($cook_photo_s.'|'.getpath_smb($cook_photo_s,'m').'|'.$path_b.'|'.getpath_smb($cook_photo_s,'blur'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
	break;
	case 'ajax_photo_s_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('m',$file['tmp_name'],$cook_uid.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$shFlag = $switch['sh']['photom_'.$data_grade];
			$photo_f  = ($shFlag == 1)?1:0;
			$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
			set_data_ed_bfb($cook_uid);
			$path_b = getpath_smb($cook_photo_s,'b');
			if(!empty($cook_photo_s))@up_send_userdel($cook_photo_s.'|'.getpath_smb($cook_photo_s,'m').'|'.$path_b.'|'.getpath_smb($cook_photo_s,'blur'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));}
	break;
	case 'ajax_photo_s_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_uinfo_logo($url,$cook_uid);$_s = setpath_s($dbname);
				}
				$newphoto_s = $_ZEAI['up2']."/".$_s;
				if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				$shFlag = $switch['sh']['photom_'.$data_grade];
				$photo_f = ($shFlag == 1)?1:0;
				$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
				set_data_ed_bfb($cook_uid);
				$path_b = getpath_smb($cook_photo_s,'b');
				if(!empty($cook_photo_s))@up_send_userdel($cook_photo_s.'|'.getpath_smb($cook_photo_s,'m').'|'.$path_b.'|'.getpath_smb($cook_photo_s,'blur'));
				json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
			}
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));}
	break;
	//case 'ajax_mate_area':Dmod("mate_areaid='".dataIO($areaid,'in',50)."',mate_areatitle='".dataIO($areatitle,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_age':Dmod("mate_age1=".intval($i1));Dmod("mate_age2=".intval($i2));jsonOutAndBfb(1);break;
	case 'ajax_mate_heigh':Dmod("mate_heigh1=".intval($i1));Dmod("mate_heigh2=".intval($i2));jsonOutAndBfb(1);break;
	case 'ajax_mate_weigh':Dmod("mate_weigh1=".intval($i1));Dmod("mate_weigh2=".intval($i2));jsonOutAndBfb(1);break;
	case 'ajax_mate_pay':Dmod("mate_pay='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_edu':Dmod("mate_edu='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_love':Dmod("mate_love='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_car':Dmod("mate_car='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_house':Dmod("mate_house='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_child':Dmod("mate_child='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_marrytime':Dmod("mate_marrytime='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_companykind':Dmod("mate_companykind='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_smoking':Dmod("mate_smoking='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_drink':Dmod("mate_drink='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_job':Dmod("mate_job='".dataIO($value,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_areaid':Dmod("mate_areaid='".dataIO($areaid,'in',50)."',mate_areatitle='".dataIO($areatitle,'in',50)."'");jsonOutAndBfb(1);break;
	case 'ajax_mate_areaid2':Dmod("mate_areaid2='".dataIO($areaid,'in',50)."',mate_areatitle2='".dataIO($areatitle,'in',50)."'");jsonOutAndBfb(1);break;
}
/********************************************************开始********************************************************/
//$t = (!empty($t))?$t:'sex';
$cARR   = array('#FCEFF4','#F4F4FF','#FBF7E4','#FFF7F7','#FFF7FF','#ECFBFF','#F0F8EF');
$randbg = $cARR[array_rand($cARR)];
$sexARR = json_decode($_UDATA['sex']);
$headertitle = '新用户注册-';$nav = 'my';
$_UDATA['parent']='[{"i":"1","v":"本人征婚","v2":"您"},{"i":"2","v":"父母帮子女征婚","v2":"子女"},{"i":"3","v":"我帮亲友征婚","v2":"亲友"}]';
$chenghu=(ifint($cook_parent))?arrT($_UDATA['parent'],$cook_parent,'v2'):'您';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $headertitle;?><?php echo $_ZEAI['siteName'];?></title>
<?php echo HEADMETA; ?>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/m1/css/m1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/m1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/zeai_ios_select/separate/select.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/birthday.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/reg_diy.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>parent_ARR=<?php echo $_UDATA['parent'];?></script>
<link href="<?php echo HOST;?>/m1/css/reg_diy.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php
if (is_weixin()){
    require_once ZEAI."api/weixin/jssdk.php";
    ?>
    <script src="<?php echo HOST;?>/res/jweixin-1.2.0.js"></script>
    <script>
    wx.config({debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
    });
    </script>
<?php }?>
<script>
var Sbindbox='',jumpurl = '<?php echo urlencode($jumpurl);?>',t='<?php echo $t;?>',upMaxMB = <?php echo intval($_UP['upMaxMB']); ?>,browser='<?php echo (is_weixin())?'wx':'h5';?>',reg_kind=<?php echo $_REG['reg_kind']?>;
function rettop(){zeai.setScrollTop(0);}
</script>
</head>
<body>
<?php
if(!empty($t)){
	?>
	<i class="ico goback" <?php echo $none;?> onClick="history.back(-1)">&#xe602;</i>
	<?php
}
if ($_REG['reg_flag']==3){?>
	<div class="submain" style="background-color:#fff">
	<div class="nodataSorry"><i class="ico">&#xe61f;</i><font>新用户注册已关闭</font></div>
	</div>
<?php exit;}?>

<?php
if($t == 'sex'){
	$sexARR = json_decode($_UDATA['sex']);
	?>
    <div class="submain <?php echo $t;?> " style="background-color:<?php echo $randbg;?>">
        <h1>请选择性别：</h1>
        <em>
        <?php
        if (count($sexARR) >= 1 && is_array($sexARR)){
            foreach ($sexARR as $V) {
                switch ($V->i) {
                    case 1:$ico='&#xe60c;';$ename  = 'Man';break;
                    case 2:$ico='&#xe95d;';$ename  = 'Woman';break;
                    default:$ico='&#xe61f;';$ename = 'zeai.cn';break;
                }
                echo '<div class="sex'.$V->i.'" onclick="sex('.$V->i.');"><i class="ico sexico">'.$ico.'</i><h2>'.$V->v.'士</h2><h4>'.$ename.'</h4></div>';
            }
        }
        ?>
        </em>
    </div>
	<script>function sex(sex){reg_diy_data_save('sex',sex);}</script>
<?php }elseif($t == 'heigh'){
	$heighARR = json_decode($_UDATA['heigh'],true);
	?>
	<style>
	.heigh ul{text-align:left;padding:20px}
    .heigh ul li{display:inline-block;margin:5px 2%;width:16%;line-height:40px;color:#999;font-size:18px;font-family:Arial;border:#eee 1px solid;background-color:#fff;border-radius:5px}
	.heigh ul li.ed,.heigh ul li:hover{background-color:#E83191;color:#fff}
	.heigh ul li b{color:#E83191}
    </style>
	<div class="submain <?php echo $t;?>" style="background-color:#fff">
    	<h1><?php echo $chenghu;?>的身高(<?php echo $heighARR['dw'];?>)：</h1>
		<ul id="heighbox">
        	<?php 
				for($h=$heighARR['start'];$h<=$heighARR['end'];$h++) {
					$id    = $rows[0];
					$title = $rows[1];
					$cls=($h==$cook_heigh)?' class="ed"':'';
					if($h % 10 == 0){
						echo '<br>';
						echo '<li onClick="heigh('.$h.')" '.$cls.'><b>'.$h.'</b></li>';
					}else{
						echo '<li onClick="heigh('.$h.')" '.$cls.'>'.$h.'</li>';	
					}
				}
			?>
        </ul>
	</div>
	<script>function heigh(heigh){reg_diy_data_save('heigh',heigh,'');}</script>
<?php }elseif($t == 'areaid____'){ ?>
	<style>.areaid{background-color:<?php echo $randbg;?>}</style>
	<div class="submain <?php echo $t;?>" id="<?php echo $t;?>">
    	<h1><?php echo $chenghu;?>的工作地区在哪里？</h1>
		<script>
            Sbindbox = 'areaid';
            ios_select_next('dq',areaARR1,areaARR2,areaARR3,'<?php echo $cook_areaid;?>',function(obj1,obj2,obj3){
                var areaid    = obj1.i + ',' + obj2.i + ',' + obj3.i;
                var areatitle = obj1.v + ' ' + obj2.v + ' ' + obj3.v;
				reg_diy_data_save('areaid',areaid,areatitle);
				//ZeaiM.page.load('reg_alone'+zeai.ajxext+'t=birthday','reg_<?php echo $t;?>','reg_birthday');
            },',');
        </script>
	</div>
<?php }elseif($t == 'areaid'){ ?>
	<style>
	.areabox{width:94%;position:relative;margin:0 auto;text-align:left;display:block}
	.areabox .ul{position:relative;box-sizing:border-box}
	.areabox .ul li{width:100%;position:absolute;left:0;top:0;z-index:5;padding:10px 0 0 0;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
	.areabox .ul li a{display:block;text-align:center;color:#666;font-size:16px;float:left;width:21%;height:35px;line-height:33px;margin:8px 2%;border:#ffdcea 1px solid;border-radius:3px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
	.areabox .ul li a:hover,.areabox .ul li a.ed{background-color:#E83191;color:#fff;border-color:#E83191}
	.areabox .ul dl{margin:0 0 15px;clear:both;overflow:hidden;position:relative}
	.areabox .ul dt{float:left;line-height:32px;font-size:16px;text-align:center;color:#999;padding:0 10px;margin:0 8px;border-bottom:0px;margin-top:30px}
	.areabox .ul dt.ed{background:#fff;border:#eee 1px solid;border-bottom:0px;border-bottom:#fff 1px solid;color:#E83191;border-radius:3px}
	.areabox .ul dl dd{width:100%;height:25px;border-bottom:#eee 1px solid;position:absolute;bottom:0;left:0;z-index:-1}
    </style>
	<div class="submain <?php echo $t;?>" id="<?php echo $t;?>">
    	<h1><?php echo $chenghu;?>的工作地区在哪里？</h1>
        <div id="areabox" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
        <script src="<?php echo HOST;?>/m1/js/zeai_div_area.js?<?php echo $_ZEAI['cache_str'];?>"></script>
        <script>
            ZEAI_area({areaid:'<?php echo $cook_areaid;?>',areatitle:'<?php echo $cook_areatitle;?>',ul:areabox.children[0],str:'job',end:function(z,e){
                reg_diy_data_save('areaid',z,e);
            }});
        </script>
	</div>
<?php }elseif($t == 'area2id'){ ?>
	<style>
	.areabox{width:94%;position:relative;margin:0 auto;text-align:left;display:block}
	.areabox .ul{position:relative;box-sizing:border-box}
	.areabox .ul li{width:100%;position:absolute;left:0;top:0;z-index:5;padding:10px 0 0 0;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
	.areabox .ul li a{display:block;text-align:center;color:#666;font-size:16px;float:left;width:21%;height:35px;line-height:33px;margin:8px 2%;border:#ffdcea 1px solid;border-radius:3px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
	.areabox .ul li a:hover,.areabox .ul li a.ed{background-color:#E83191;color:#fff;border-color:#E83191}
	.areabox .ul dl{margin:0 0 15px;clear:both;overflow:hidden;position:relative}
	.areabox .ul dt{float:left;line-height:32px;font-size:16px;text-align:center;color:#999;padding:0 10px;margin:0 8px;border-bottom:0px;margin-top:30px}
	.areabox .ul dt.ed{background:#fff;border:#eee 1px solid;border-bottom:0px;border-bottom:#fff 1px solid;color:#E83191;border-radius:3px}
	.areabox .ul dl dd{width:100%;height:25px;border-bottom:#eee 1px solid;position:absolute;bottom:0;left:0;z-index:-1}
    </style>
	<div class="submain <?php echo $t;?>" id="<?php echo $t;?>">
    	<h1><?php echo $chenghu;?>的户籍地区是？</h1>
        <div id="areabox" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
        <script src="<?php echo HOST;?>/m1/js/zeai_div_area.js?<?php echo $_ZEAI['cache_str'];?>"></script>
        <script>ZEAI_area({areaid:'<?php echo $cook_area2id;?>',areatitle:'<?php echo $cook_area2title;?>',ul:areabox.children[0],str:'hj',datastr:'hj',end:function(z,e){reg_diy_data_save('area2id',z,e);}});</script>
	</div>
<?php }elseif($t == 'birthday'){ ?>
	<style>.birthday{background-color:<?php echo $randbg;?>}</style>
    <div class="submain <?php echo $t;?>" id="<?php echo $t;?>">
    	<h1><?php echo $chenghu;?>的生日是哪一天呢？</h1>
        <div class="vtphoto" id="vtphoto"></div>
		<script>
        var now = new Date();
        var nowYear = now.getFullYear();
        function formatYear (nowYear) {
            var arr = [];
            for (var i = nowYear - 70; i <= nowYear - 18; i++) {
                var istr = (i<10)?'0'+i:''+i;
                arr.push({
                    'i':istr + '',
                    'v':istr + '年'
                });
            }
            return arr;
        }
        function formatMonth () {
            var arr = [];
            for (var i = 1; i <= 12; i++) {
                var istr = (i<10)?'0'+i:''+i;
                arr.push({
                    'i':istr + '',
                    'v':istr + '月'
                });
            }
            return arr;
        }
        function formatDate (count) {
            var arr = [];
            for (var i = 1; i <= count; i++) {
                var istr = (i<10)?'0'+i:''+i;
                arr.push({
                    'i':istr + '',
                    'v':istr + '日'
                });
            }
            return arr;
        }
        var yearData = formatYear(nowYear);
        var monthData = function () {return formatMonth();};
        var dateData = function (year, month) {
            if (/^01|03|05|07|08|1|3|5|7|8|10|12$/.test(month)) {
                return formatDate(31);
            }else if (/^04|06|09|4|6|9|11$/.test(month)) {
                return formatDate(30);
            }else if (/^02|2$/.test(month)) {
                if (year % 4 === 0 && year % 100 !==0 || year % 400 === 0) {
                    return formatDate(29);
                }else {
                    return formatDate(28);
                }
            }else {
                throw new Error('month is illegal');
            }
        };
        //
        Sbindbox = 'birthday';
        ios_select_next('sr',yearData, monthData, dateData,'<?php echo (empty($cook_birthday))?'1995-05-20':$cook_birthday;?>',function(obj1,obj2,obj3){
            var birthdayv = obj1.i + '-' + obj2.i + '-' + obj3.i;
			reg_diy_data_save('birthday',birthdayv,'');
        },'-');
        </script>
	</div>
<?php }elseif($t == 'love'){?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center}
	 .<?php echo $t;?> ul li.size4{border-color:#ffdcea}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>选择<?php echo $chenghu;?>的婚况</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_love;?>);</script>
	</div>
<?php }elseif($t == 'job'){ ?>
    <style>
	 .<?php echo $t;?>{padding-left:20px;padding-right:20px}
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{float:left;width:29%;margin:10px 2%}
	 .<?php echo $t;?> ul li.size4{font-size:16px;border-color:#ffdcea}
	 .<?php echo $t;?> i.ico{line-height:140px;}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>请问<?php echo $chenghu;?>的职业？</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_job;?>);</script>
	</div>
<?php }elseif($t == 'parent'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:20px auto;text-align:center}
	 .<?php echo $t;?> ul li.size4{border-color:#ffdcea}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>请问您是替谁征婚？</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_parent;?>);</script>
	</div>
<?php }elseif($t == 'edu'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center}
	 .<?php echo $t;?> ul li.size4{border-color:#ffdcea}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>选择<?php echo $chenghu;?>的学历</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_edu;?>);</script>
	</div>
<?php }elseif($t == 'pay'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center}
	 .<?php echo $t;?> ul li.size4{border-color:#ffdcea}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>选择<?php echo $chenghu;?>的月收入</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_pay;?>);</script>
	</div>
<?php }elseif($t == 'marrytime'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center}
	 .<?php echo $t;?> ul li.size4{border-color:#ffdcea}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>期望结婚时间</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_marrytime;?>);</script>
	</div>
<?php }elseif($t == 'house'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center}
	 .<?php echo $t;?> ul li.size4{border-color:#ffdcea}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>选择<?php echo $chenghu;?>的购房情况</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_house;?>);</script>
	</div>
<?php }elseif($t == 'car'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center}
	 .<?php echo $t;?> ul li.size4{border-color:#ffdcea}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>选择<?php echo $chenghu;?>的购车情况</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_car;?>);</script>
	</div>
<?php }elseif($t == 'child'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center}
	 .<?php echo $t;?> ul li.size4{border-color:#ffdcea}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1><?php echo $chenghu;?>的子女情况</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="li<?php echo $t;?>"></ul>
		<script>reg_alone_udata(<?php echo $cook_sex;?>,'<?php echo $t;?>',<?php echo $cook_child;?>);</script>
	</div>
<?php }elseif($t == 'weigh'){
	$weighARR = json_decode($_UDATA['weigh'],true);
	?>
	<style>
	.<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	.<?php echo $t;?> ul{text-align:left;padding:20px}
    .<?php echo $t;?> ul li{display:inline-block;margin:5px 2%;width:16%;line-height:40px;color:#999;font-size:18px;font-family:Arial;border:#eee 1px solid;background-color:#fff;border-radius:5px}
	.<?php echo $t;?> ul li:hover,.<?php echo $t;?> ul li.ed{background-color:#E83191;color:#fff}
	.<?php echo $t;?> ul li b{color:#E83191}
    </style>
	<div class="submain <?php echo $t;?>">
    	<h1><?php echo $chenghu;?>的体重(<?php echo $weighARR['dw'];?>)：</h1>
		<ul id="weighbox">
        	<?php 
				for($h=$weighARR['start'];$h<=$weighARR['end'];$h++) {
					$id    = $rows[0];
					$title = $rows[1];
					$cls=($h==$cook_weigh)?' class="ed"':'';
					if($h % 10 == 0){
						echo '<br>';
						echo '<li onClick="weigh('.$h.')" '.$cls.'><b>'.$h.'</b></li>';
					}else{
						echo '<li onClick="weigh('.$h.')" '.$cls.'>'.$h.'</li>';	
					}
				}
			?>
        </ul>
	</div>
	<script>function weigh(weigh){reg_diy_data_save('weigh',weigh,'');}</script>
<?php }elseif($t == 'weixin'){ ?>
    <div class="submain <?php echo $t;?> reg_text">
    	<h1>请填写您的微信号</h1>
		<dl><dt><i class="ico">&#xe607;</i></dt><dd><input name="wxx" type="text" class="input_login" id="wxx" placeholder="请输入微信号" value="<?php echo $cook_weixin;?>" autocomplete="off" maxlength="40"></dd></dl>
        <button type="button" class="btn size4 HONG ed" onClick="wx();">下一步</button>
		<script>function wx(){reg_diy_data_save('weixin',wxx.value,'');}</script>
	</div>
<?php }elseif($t == 'truename'){ ?>
    <div class="submain <?php echo $t;?> reg_text">
    	<h1>请填写您的真实姓名</h1>
		<dl><dt><i class="ico2">&#xe69d;</i></dt><dd><input name="truenamee" type="text" class="input_login" id="truenamee" placeholder="请输入真实姓名" value="<?php echo $cook_truename;?>" autocomplete="off" maxlength="40"></dd></dl>
        <button type="button" class="btn size4 HONG ed" onClick="truenamefn();">下一步</button>
		<script>function truenamefn(){reg_diy_data_save('truename',truenamee.value,'');}</script>
	</div>
<?php }elseif($t == 'nickname'){ ?>
    <div class="submain <?php echo $t;?> reg_text">
    	<h1>请填写您的网名/昵称</h1>
		<dl><dt><i class="ico">&#xe64d;</i></dt><dd><input name="nicknamee" type="text" class="input_login" id="nicknamee" placeholder="请输入昵称" autocomplete="off" maxlength="40" value="<?php echo $cook_nickname;?>"></dd></dl>
        <button type="button" class="btn size4 HONG ed" onClick="nicknamefn();">下一步</button>
		<script>function nicknamefn(){reg_diy_data_save('nickname',nicknamee.value,'');}</script>
	</div>
<?php }elseif($t == 'identitynum'){ ?>
    <div class="submain <?php echo $t;?> reg_text">
        <h1>请填写您身份证号码</h1>
        <dl><dt><i class="ico">&#xe64d;</i></dt><dd><input name="identitynum" type="text" class="input_login" id="identitynum" placeholder="请输入身份证号码" autocomplete="off" maxlength="20" value="<?php echo $cook_identitynum;?>"></dd></dl>
        <button type="button" class="btn size4 HONG ed" onClick="identitynumfn();">下一步</button>
        <script>function identitynumfn(){
			if(!zeai.ifsfz(identitynum.value)){
				zeai.msg('请填写您身份证号码');	
			}else{
				reg_diy_data_save('identitynum',identitynum.value,'');
			}
		}</script>
    </div>
<?php }elseif($t == 'kefu'){
	$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');
	?>
    <style>
	.kefu .kefu{margin-top:60px}
	.kefu .kefu img{width:30%;margin:10px auto;display:block;padding:10px;border:#eee 1px solid}
	.kefu .kefu font{color:#999}
	.kefu .kefu a{margin-top:10px;display:block;color:#666}
	.kefu .kefu .ico{margin-right:4px;}
    </style>
    <div class="submain <?php echo $t;?> reg_text">
        <h1>请加客服/红娘审核资料</h1>
        <div class="kefu">
        <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><br><?php }?>
        <?php if (!empty($kf_tel)){?>
            <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
        <?php }else{?>
            <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
        <?php }?>
        </div>        
        <button type="button" class="btn size4 HONG ed" onClick="kefufn();">加好了，完成注册</button>
        <script>function kefufn(){reg_diy_data_save('click',1,'');}</script>
    </div>
<?php }elseif($t == 'photo_s'){
	$photo_s_str = (!empty($cook_photo_s))?'<img src="'.$_ZEAI['up2'].'/'.smb($cook_photo_s,'m').'">':'<i class="ico">&#xe620;</i><h5>点击上传</h5>';
	?>
    <div class="submain <?php echo $t;?>">
    	<h1>上传<?php echo $chenghu;?>的形象照片</h1>
        <div class="icoadd" id="photo_sbtn" <?php if(is_h5app()){ ?> onClick="rz_photo_goup()" <?php }?>><?php echo $photo_s_str;?></div>
        <h6>上传照片，交友成功率提升300%</h6>
        <div id="nophoto_sBox">
            <div class="linebox"><div class="line W50"></div><div class="title S14 C999 BAI">头像审核标准</div></div>
            <div class="reg_p">
                <li><img src="img/reg_p/1.jpg"><i class="ico dui">&#xe60d;</i>真实居中</li>
                <li><img src="img/reg_p/2.jpg"><i class="ico dui">&#xe60d;</i>上半身照</li>
                <li><img src="img/reg_p/3.jpg"><i class="ico cuo">&#xe62c;</i>模糊不清</li>
                <li><img src="img/reg_p/4.jpg"><i class="ico cuo">&#xe62c;</i>过于暴露</li>
                <li><img src="img/reg_p/5.jpg"><i class="ico cuo">&#xe62c;</i>P图过度</li>
                <div class="clear"></div>
            </div>
        </div>
	</div>
	<script>
	<?php if(is_h5app()){ ?>
	function rz_photo_goup(){		
		app_uploads({url:app_Domain+"/m1/reg_diy"+zeai.extname+'?submitok=ajax_photo_s_up_app',num:1},function(e){					
			var rs=zeai.jsoneval(e);
			if (rs.flag == 1){				
				photo_sbtn.html('<img src='+rs.photo_s+'>');
				setTimeout(function(){regnext('<?php echo $t;?>');},2000);
			}
		});
	}
	<?php }else{?>
    photoUp({
        btnobj:photo_sbtn,
        url:"reg_diy"+zeai.extname,
        submitokBef:"ajax_photo_s_",
        _:function(rs){
            zeai.msg(0);zeai.msg(rs.msg);
            if (rs.flag == 1){
                photo_sbtn.html('<img src='+rs.photo_s+'>');
                setTimeout(function(){regnext('<?php echo $t;?>');},2000);
            }
        }
    });<?php }?>
    </script>
<?php }elseif($t == 'aboutus'){ ?>
    <div class="submain <?php echo $t;?> reg_text">
    	<h1><?php echo $chenghu;?>自我介绍一下吧^_^</h1>
        <textarea class="textarea" id="aboutuss" placeholder="简短个人介绍，三观等（全部人工审核，切勿填写联系方式 否则资料无法通过。）"><?php echo $cook_aboutus;?></textarea>
        <button type="button" class="btn size4 HONG ed" onClick="aboutusfn();">下一步</button>
		<script>function aboutusfn(){reg_diy_data_save('aboutus',aboutuss.value,'');}</script>
	</div>
<?php }elseif($t == 'mate'){
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	?>
	<style>
    #mate .ios-select-widget-box.olay {background-color:rgba(0,0,0,.4)}
    #mate .ios-select-widget-box.olay > div {width:100%;top:auto;bottom:0;left:0;border-radius:0;padding-bottom:58px;background-color:#fff}
	#mate .ios-select-widget-box.olay > div h2{font-size:18px;text-align:left;float:left;padding-left:3%;font-weight:bold;}
	.mate{padding:20px 0 0}
	.mate .size4{margin-top:20px}
	.modlist ul li{width:100%;text-align:left}
	.RCW li{width:50%}
    </style>
    <div id="mate">
    <div class="submain <?php echo $t;?> reg_text modlist">
    	<h1>设置<?php echo $chenghu;?>的择偶要求</h1><br>
        <ul>
			<?php
            if (count($mate_diy) >= 1 && is_array($mate_diy)){
                foreach ($mate_diy as $k=>$V) {
					$cook_tmp1 = 'cook_mate_'.$V;
					$cook_tmp2 = 'cook_mate_'.$V.'_str';
					$cook_mate_data = $$cook_tmp1;
					$cook_mate_str  = $$cook_tmp2;
                    $ext = mate_diy_par($V,'ext');
                    switch ($ext) {
                        case 'radio':$class     = 'slect';break;
                        case 'checkbox':$class  = 'chckbox';break;
                        case 'radiorange':$class  = 'rang';break;
                        case 'area':$class   = 'aread';break;
                    }
				?>
                <li id="mate_<?php echo $V;?>" class="<?php echo $class;?>" data="<?php echo $cook_mate_data;?>"><h4><?php echo mate_diy_par($V);?> <b class="Cf00">*</b></h4><span><?php echo $cook_mate_str;?></span></li>
            <?php }}?>
        </ul>
        <button type="button" class="btn size4 HONG ed" onClick="matefn();">下一步</button>
	</div>
    </div>
	<script>
	Sbindbox = 'mate';
	<?php if (in_array('areaid',$mate_diy)){?>mate_areaid.setAttribute('data',defarea1);<?php }?>
	<?php if (in_array('areaid2',$mate_diy)){?>mate_areaid2.setAttribute('data',defarea2);<?php }?>
    my_info_data({"modlist":document.querySelector('.modlist').getElementsByTagName("li")});
    function matefn(){
		zeai.ajax({url:'reg_diy'+zeai.ajxext+'submitok=ajax_chk_mate'},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){regnext(<?php echo $t;?>);}else{zeai.msg(0);zeai.msg(rs.msg);}
		});
	}
    </script>
<?php }else{?>
    <?php if ($ifback==1){?><a href="javascript:history.back(-1)" class="ico goback Ugoback">&#xe602;</a><?php }?>
    <div class="submain reg_diy huadong" id="main">
        <div class="banner"><img src="../res/reg_banner<?php echo ($_ZEAI['mob_mbkind']==3)?3:'';?>.jpg?<?php echo $_ZEAI['cache_str'];?>"></div>
        <div<?php echo (!$ifreg2)?' style="display:none"':'';?> class="ifreg2" id="ifreg2">
            <?php 
                switch ($cook_sex) {
                    case 1:$ico='&#xe60c;';break;
                    case 2:$ico='&#xe95d;';break;
                    default:$ico='&#xe61f;';$ename = 'zeai.cn';break;
                }
			?>
           <div class="sex<?php echo $cook_sex;?>"><i class="ico sexico"><?php echo $ico;?></i><h4><?php echo $cook_uname;?><font class="S14 C999">（UID:<?php echo $cook_uid;?>）</font></h4></div>
            <br>您上次注册未完成<br>开启幸福之旅只差一步
            <input type="button" value="继续注册" class="btn size5 HONG B ed" onClick="regnext();">
        </div>
        <form id="WWW-ZEAI-CN-form"<?php echo ($ifreg2)?' style="display:none"':'';?>>
            <?php if ($_REG['reg_kind'] == 1 || $_REG['reg_kind'] == 3){?>
                <dl><dt><i class="ico">&#xe627;</i></dt><dd><input name="mob" type="text" class="input_login" id="mob" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*" onBlur="rettop();"></dd></dl>
                <dl><dt><i class="ico">&#xe6c3;</i></dt><dd class="yzmF">
                <input name="verify" id="verify" type="text" required class="input_login" maxlength="4" pattern="[0-9]*" placeholder="输入手机验证码" autocomplete="off" onBlur="rettop();" /><a href="javascript:yzmbtnFn();" class="yzmbtn" id="yzmbtn">获取验证码</a>
                </dd></dl>
            <?php }if($_REG['reg_kind'] == 2 || $_REG['reg_kind'] == 3){ ?>
                <dl><dt><i class="ico">&#xe645;</i></dt><dd><input name="uname" type="text" class="input_login" id="uname" placeholder="请输入登录用户名" autocomplete="off" maxlength="20" onBlur="rettop();"></dd></dl>
            <?php }?>
            <dl><dt><i class="ico pass">&#xe61e;</i></dt><dd><input name="pwd" type="password" class="input_login" id="pwd" placeholder="请输入登录密码" autocomplete="off" maxlength="20" onBlur="rettop();"></dd></dl>
            <div class="clause<?php echo ($_ZEAI['mob_mbkind']==3)?' mbkind3':'';?>"><input type="checkbox" name="clause" id="clause" class="checkskin " value="1" checked><label for="clause" class="checkskin-label"><i></i><b class="C666">已阅读和同意　<a href="javascript:agreeDeclara();" class="tiaose">注册协议</a></b></label></div>
            <input type="button" value="下一步" class="btn size5 <?php echo ($_ZEAI['mob_mbkind']==3)?'mbkind3btn':'';?> B ed" onClick="regbtnFn();">
            <a href="login.php?subscribe=<?php echo $subscribe;?>&tguid=<?php echo $tguid;?>" class="S16">已有帐号，这边登录<i class="ico">&#xe601;</i></a>
        </form>
        <input type="hidden" name="tguid" id="tguid" value="<?php echo $tguid;?>">
        <input type="hidden" name="tmpid" id="tmpid" value="<?php echo $tmpid;?>">
        <input type="hidden" name="subscribe" id="subscribe" value="<?php echo $subscribe;?>">
        
    </div>
<?php }?>

    <div id="Zeai_cn__PageBox"></div>
    </body>
    </html>
<?php
function Dmod($field){
	global $db,$cook_uid;
	$db->UPDATE($cook_uid,$field,__TBL_USER__,"id=".$cook_uid);
}
function jsonOutAndBfb($update){
	global $cook_uid;
	if($update==1)set_data_ed_bfb($cook_uid);
	json_exit(array('flag'=>1,'msg'=>'保存成功'));
}
function reg_addupdate() {
	global $switch,$TG_set,$_REG,$db,$cook_uid,$reg_loveb,$uname,$_ZEAI,$tguid,$navarr,$sex,$photo_s,$nickname,$grade,$birthday;
	$flag  =($_REG['reg_flag']==1)?1:0;
	$switchdataflag = ($switch['sh']['moddata_'.$grade] == 1)?1:0;
	$db->query("UPDATE ".__TBL_USER__." SET flag=$flag,dataflag=".$switchdataflag." WHERE id=".$cook_uid);
	set_data_ed_bfb($cook_uid);
	/**************** 清单通知 ******************/
	if ($reg_loveb > 0){
		$row = $db->NUM($cook_uid,'loveb');
		if ($row){
			if($row[0] == 0){
				$db->query("UPDATE ".__TBL_USER__." SET loveb=loveb+$reg_loveb WHERE id=".$cook_uid);
				//Love币清单
				$db->AddLovebRmbList($cook_uid,'新用户注册',$reg_loveb,'loveb',6);		
				//站内消息
				$C = $uname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！　　<a href='.Href('loveb').' class=aQING>查看详情</a>';
				$db->SendTip($cook_uid,'您有一笔'.$_ZEAI['loveB'].'到账！',dataIO($C,'in'),'sys');
			}
		}
	}
	if(ifint($tguid) && in_array('tg',$navarr))TG($tguid,$cook_uid,'reg');
	$rt=$db->query("SELECT openid,subscribe FROM ".__TBL_USER__." WHERE ifadm=1");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$openid    = $rows['openid'];
			$subscribe = $rows['subscribe'];
			//微信模版审核通过提醒
			if (!empty($openid) && $subscribe==1){
				$first     = urlencode('【'.$nickname.' uid:'.$cook_uid.'】新用户注册成功！');
				$keyword1  = '新用户注册';
				$keyword3  = urlencode($_ZEAI['siteName']);
				$remark    = '请过5分钟后（可能正在完善资料中）进入后台进行资料审核。';
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url=');
			}
		}
	}
	setcookie("cook_uname",$uname,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_sex",$sex,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_photo_s",$photo_s,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_nickname",$nickname,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_grade",$grade,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_birthday",$birthday,time()+720000,"/",$_ZEAI['CookDomain']);
}
?>
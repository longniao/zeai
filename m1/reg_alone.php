<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
require_once ZEAI.'sub/conn.php';
if (empty($cook_tmp_openid) && empty($t) && is_weixin()){
	$cook_tmp_openid=wx_get_openid(0);
	setcookie('cook_tmp_openid',$cook_tmp_openid,time()+720000,'/',$_ZEAI['CookDomain']);
}
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
		}elseif($row['flag']==2){
			setcookie("cook_uid",$row['id'],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_pwd",$row['pwd'],time()+720000,"/",$_ZEAI['CookDomain']);
			if( $row['birthday']!='0000-00-00' || ifint($row['love']) ){
				//exit('测试新版，请稍后再来A');
				header("Location: reg_ed.php");
			}
		}
	}
}
if (ifint($cook_uid) && !empty($cook_pwd) && $t!= 'success'){
	$row = $db->ROW(__TBL_USER__,"flag,birthday,love","id=".$cook_uid." AND pwd='".$cook_pwd."'  ",'name');
	if (empty($t)){
		if ($row['flag']==1){
			//exit('测试新版，请稍后再来C');
			header("Location: ../?z=my");
		}elseif($row['flag']==2 && $row['birthday']!='0000-00-00' && !ifint($row['love'])   ){
			//exit('测试新版，请稍后再来B');
			header("Location: reg_ed.php");
		}elseif($row['flag']==0){
			if(!empty($submitok)){
				json_exit(array('flag'=>0,'msg'=>'请等待审核'));	
			}else{
				callmsg('请等待审核',HOST);
			}
		}
	}else{
		if ($row['flag']==1)json_exit(array('flag'=>'logined','msg'=>'请不要重复提交'));	
	}
}
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_reg.php';$TG_set = json_decode($_REG['TG_set'],true);//$tg=json_decode($_REG['tg'],true);
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
$flag  =($_REG['gzflag2']==1)?2:$flag;

/*if($submitok == 'ajax_photo_s_up_h5' || $submitok == 'ajax_photo_s_up_wx'){
	$data_photo_s='';
	$row = $db->ROW(__TBL_USER__,"photo_s","id=".$cook_uid);
	if ($row){$data_photo_s=$row[0];}
}
*/
switch ($submitok) {
	/*	case 'ajax_photo_s_up_h5':
			if (ifpostpic($file['tmp_name'])){
				$dbname = setphotodbname('m',$file['tmp_name'],$cook_uid.'_');
				if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
				$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
				if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				$shFlag = $switch['sh']['photom_'.$reg_grade];
				$photo_f  = ($shFlag == 1)?1:0;
				$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
				set_data_ed_bfb($cook_uid);
				$path_b = getpath_smb($data_photo_s,'b');
				if(!empty($data_photo_s))@up_send_userdel($data_photo_s.'|'.getpath_smb($data_photo_s,'m').'|'.$path_b.'|'.getpath_smb($data_photo_s,'blur'));
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
					$shFlag = $switch['sh']['photom_'.$reg_grade];
					$photo_f = ($shFlag == 1)?1:0;
					$db->query("UPDATE ".__TBL_USER__." SET photo_s='$_s',photo_f=".$photo_f." WHERE id=".$cook_uid);
					set_data_ed_bfb($cook_uid);
					$path_b = getpath_smb($data_photo_s,'b');
					if(!empty($data_photo_s))@up_send_userdel($data_photo_s.'|'.getpath_smb($data_photo_s,'m').'|'.$path_b.'|'.getpath_smb($data_photo_s,'blur'));
					json_exit(array('flag'=>1,'msg'=>'上传成功','photo_s'=>getpath_smb($newphoto_s,'m')));
				}
			}else{json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));}
		break;
	*/
	case 'ajax_reg_uname_addupdate'://reg_kind=2
		if ($_REG['reg_kind']==1)exit(JSON_ERROR);
		if ($_REG['reg_flag']==3)json_exit(array('flag'=>0,'msg'=>'本站已关闭新会员注册'));
		
		$REG=json_decode($REG,true);
		/**************** 注册数据 ******************/
		$uname     = dataIO($REG['uname'],'in');
		$pwd       = $REG['pwd'];
		$sex       = intval($REG['sex']);
		$areaid    = dataIO($REG['areaid'],'in');
		$areatitle = dataIO($REG['areatitle'],'in');
		$birthday  = dataIO($REG['birthday'],'in');
		$edu       = intval($REG['edu']);
		$heigh     = intval($REG['heigh']);
		$job       = intval($REG['job']);
		$love      = intval($REG['love']);
		$pay       = intval($REG['pay']);
		$weixin = dataIO($REG['weixin'],'in',40);
		$tmpid     = intval($REG['tmpid']);
		$regkind   = 6;
		$tguid       = intval($tguid);
		if ($sex==0 || $pay==0 || empty($areaid) || empty($birthday) )json_exit(array('flag'=>0,'msg'=>'注册数据遗漏'));
		
		$uname = dataIO($uname,'in');$pwd = dataIO($pwd,'in');
		$chkflag = 1;
		if (str_len($uname) > 20 || str_len($uname) < 3) {$content="请输入正确的登录帐号";$chkflag=0;}
		if (str_len($pwd) > 20 || str_len($pwd) < 6) {$content="密码长度必须在6~20字节";$chkflag=0;}
		if ($chkflag == 0)json_exit(array('flag'=>0,'msg'=>$content));
		chk_uname($uname,$pwd);
		$pwd=md5(trim($pwd));
		/**************** 入库 ******************/
		$subscribe=0;
		if(str_len($cook_tmp_openid) >15){
			$row = $db->ROW(__TBL_USER__,"id,subscribe","openid='$cook_tmp_openid'","num");
			if ($row){
				$uid= $row[0];$subscribe= $row[1];
				$db->query("UPDATE ".__TBL_USER__." SET weixin='$weixin',flag='$flag',uname='$uname',pwd='$pwd',sex='$sex',areaid='$areaid',areatitle='$areatitle',birthday='$birthday',edu='$edu',heigh='$heigh',job='$job',love='$love',pay='$pay' WHERE id=".$uid);
			}else{
				$db->query("INSERT INTO ".__TBL_USER__." (openid,weixin,flag,uname,pwd,sex,grade,if2,areaid,areatitle,birthday,edu,heigh,job,love,pay,loveb,regtime,endtime,regip,endip,refresh_time,regkind,tguid) VALUES ('$cook_tmp_openid','$weixin',$flag,'".$uname."','".$pwd."',$sex,$reg_grade,$reg_if2,'".$areaid."','".$areatitle."','".$birthday."',".$edu.",".$heigh.",".$job.",".$love.",".$pay.",".$reg_loveb.",".ADDTIME.",".ADDTIME.",'$ip','$ip',".ADDTIME.",$regkind,$tguid)");
				$uid = intval($db->insert_id());
			}
		}else{
			$db->query("INSERT INTO ".__TBL_USER__." (weixin,flag,uname,pwd,sex,grade,if2,areaid,areatitle,birthday,edu,heigh,job,love,pay,loveb,regtime,endtime,regip,endip,refresh_time,regkind,tguid) VALUES ('$weixin',$flag,'".$uname."','".$pwd."',$sex,$reg_grade,$reg_if2,'".$areaid."','".$areatitle."','".$birthday."',".$edu.",".$heigh.",".$job.",".$love.",".$pay.",".$reg_loveb.",".ADDTIME.",".ADDTIME.",'$ip','$ip',".ADDTIME.",$regkind,$tguid)");
			$uid = intval($db->insert_id());
		}
		addupdate($uid,2,$uname,$pwd,$sex,$reg_grade,$birthday);
	break;
	case 'ajax_reg_yzm':
		if ($_REG['reg_kind']==2)exit(JSON_ERROR);
		
		$REG=json_decode($REG,true);
		$mob   = $REG['mob'];
		$uname = dataIO($REG['uname'],'in');
		
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}else{
			if ($db->ROW(__TBL_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)"))json_exit(array('flag'=>0,'msg'=>'此手机号码已被注册，请更换'));
		}
		//有用户名reg_kind=3
		if ($_REG['reg_kind']==3){
			if (ifmob($uname) || ifint($uname))json_exit(array('flag'=>0,'msg'=>'登录用户名不能是手机号码和纯数字'));
			if (str_len($uname) > 20 || str_len($uname)<3 )json_exit(array('flag'=>0,'msg'=>'请输入3-15个字符用户名'));
			$row = $db->ROW(__TBL_USER__,'id',"uname='".$uname."' OR (mob='$uname' AND FIND_IN_SET('mob',RZ)) ");
			if($row)json_exit(array('flag'=>0,'msg'=>'此用户名已被注册,请重新输入'.$uname));
		}
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
			$content = "发送失败,错误码：$rtn";
		}
		//sms end
		$_SESSION['Zeai_cn__mob'] = $mob;
		json_exit(array('flag'=>$chkflag,'msg'=>$content,'reg_kind'=>$_REG['reg_kind']));
	break;
	case 'ajax_reg_yzm_addupdate'://1,3
		if ($_REG['reg_kind']==2)exit(JSON_ERROR);
		if ($_REG['reg_flag']==3)json_exit(array('flag'=>0,'msg'=>'本站已关闭新会员注册'));
		$verify = intval($verify);
		if (empty($_SESSION['Zeai_cn__mobyzm'])){
			json_exit(array('flag'=>0,'msg'=>'短信验证码错误，请重新获取'));
		}else{
			if ($_SESSION['Zeai_cn__mobyzm'] != $verify){
				json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));///////////////////////////.$_SESSION['Zeai_cn__mobyzm']
			}
			if ($_SESSION['Zeai_cn__mob'] != $mob && ifmob($mob)){
				unset($_SESSION["Zeai_cn__mob"]);
				json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
			}
		}
		/**************** 注册数据 ******************/
		$REG=json_decode($REG,true);
		$mob       = $REG['mob'];
		$pwd       = $REG['pwd'];
		$sex       = intval($REG['sex']);
		$areaid    = dataIO($REG['areaid'],'in');
		$areatitle = dataIO($REG['areatitle'],'in');
		$birthday  = dataIO($REG['birthday'],'in');
		$edu       = intval($REG['edu']);
		$heigh     = intval($REG['heigh']);
		$job       = intval($REG['job']);
		$love      = intval($REG['love']);
		$pay       = intval($REG['pay']);
		$weixin    = dataIO($REG['weixin'],'in',40);
		$tmpid     = intval($REG['tmpid']);
		$regkind   = 6;
		$tguid       = intval($tguid);
		if (!ifmob($mob) || $sex==0 || $pay==0 || empty($areaid) || empty($pwd) || empty($birthday) )json_exit(array('flag'=>0,'msg'=>'注册数据遗漏'));
		if (str_len($pwd) > 20 || str_len($pwd) < 6)json_exit(array('flag'=>0,'msg'=>'密码长度必须在6~20字节'));
		//
		//有用户名reg_kind=3
		if ($_REG['reg_kind']==3){
			$uname = $REG['uname'];
			if (ifmob($uname) || ifint($uname))json_exit(array('flag'=>0,'msg'=>'登录用户名不能是手机号码和纯数字'));
			if (str_len($uname) > 20 || str_len($uname)<3 )json_exit(array('flag'=>0,'msg'=>'请输入3-15个字符用户名'));
			$row = $db->ROW(__TBL_USER__,'id',"uname='".$uname."' OR (mob='$uname' AND FIND_IN_SET('mob',RZ)) ");
			if($row)json_exit(array('flag'=>0,'msg'=>'此用户名已被注册,请重新输入'));
		//reg_kind=1
		}else{
			$uname='reg'.cdstr(6);
		}
		$pwd=md5(trim($pwd));
		/**************** 入库 ******************/
		$subscribe=0;
		if(str_len($cook_tmp_openid) >15){
			$row = $db->ROW(__TBL_USER__,"id,subscribe","openid='$cook_tmp_openid'","num");
			if ($row){
				$uid = $row[0];$subscribe = $row[1];
				$db->query("UPDATE ".__TBL_USER__." SET weixin='$weixin',flag='$flag',mob='$mob',RZ='mob',uname='$uname',pwd='$pwd',sex='$sex',areaid='$areaid',areatitle='$areatitle',birthday='$birthday',edu='$edu',heigh='$heigh',job='$job',love='$love',pay='$pay' WHERE id=".$uid);
			}else{
				$db->query("INSERT INTO ".__TBL_USER__." (openid,weixin,flag,uname,mob,RZ,pwd,sex,grade,if2,areaid,areatitle,birthday,edu,heigh,job,love,pay,loveb,regtime,endtime,regip,endip,refresh_time,regkind,tguid) VALUES ('$cook_tmp_openid','$weixin',$flag,'".$uname."','".$mob."','mob','".$pwd."',$sex,$reg_grade,$reg_if2,'".$areaid."','".$areatitle."','".$birthday."',".$edu.",".$heigh.",".$job.",".$love.",".$pay.",".$reg_loveb.",".ADDTIME.",".ADDTIME.",'$ip','$ip',".ADDTIME.",$regkind,$tguid)");
				$uid = intval($db->insert_id());
			}
		}else{
			$db->query("INSERT INTO ".__TBL_USER__." (weixin,flag,uname,mob,RZ,pwd,sex,grade,if2,areaid,areatitle,birthday,edu,heigh,job,love,pay,loveb,regtime,endtime,regip,endip,refresh_time,regkind,tguid) VALUES ('$weixin',$flag,'".$uname."','".$mob."','mob','".$pwd."',$sex,$reg_grade,$reg_if2,'".$areaid."','".$areatitle."','".$birthday."',".$edu.",".$heigh.",".$job.",".$love.",".$pay.",".$reg_loveb.",".ADDTIME.",".ADDTIME.",'$ip','$ip',".ADDTIME.",$regkind,$tguid)");
			$uid = intval($db->insert_id());
		}
		addupdate($uid,1,$uname,$pwd,$sex,$reg_grade,$birthday);
	break;
}
function addupdate($uid,$reg_kind,$uname,$pwd,$sex,$reg_grade,$birthday){
	global $_ZEAI,$tmpid,$db,$reg_loveb,$cook_tmp_openid,$subscribe,$tguid,$TG_set,$navarr;//$tg;
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
				$nickname = dataIO($c['nickname'],'in');
				//$dbname = (!empty($c['photo_s']))?wx_get_uinfo_logo($c['photo_s'],$uid):'';
				//$photo_s=setpath_s($dbname);
				$db->query("UPDATE ".__TBL_USER__." SET regkind=$regkind,loginkey='$loginkey',nickname='$nickname' WHERE id=".$uid);//,photo_s='$photo_s'
			/**** weixin ****/
			}elseif($regkind=='weixin'){
				$regkind=3;
				$openid   = $c['openid'];
				$nickname = dataIO($c['nickname'],'in');
				$unionid  = $c['unionid'];
				//$dbname = (!empty($c['headimgurl']))?wx_get_uinfo_logo($c['headimgurl'],$uid):'';
				//$photo_s=setpath_s($dbname);
				$db->query("UPDATE ".__TBL_USER__." SET regkind=$regkind,openid='$openid',unionid='$unionid',nickname='$nickname' WHERE id=".$uid);//,photo_s='$photo_s'
			}
		}
		$db->query("DELETE FROM ".__TBL_TMP__." WHERE id=".$tmpid);
	}
	/**************** 清单通知 ******************/
	if ($reg_loveb > 0 && $subscribe==0){
		//Love币清单
		$db->AddLovebRmbList($uid,'新用户注册',$reg_loveb,'loveb',6);		
		//站内消息
		$C = $uname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！　　<a href='.Href('loveb').' class=aQING>查看详情</a>';
		$db->SendTip($uid,'您有一笔'.$_ZEAI['loveB'].'到账！',dataIO($C,'in'),'sys');
	}
	setcookie("cook_uid",$uid,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_uname",$uname,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_pwd",$pwd,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_sex",$sex,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_photo_s",$photo_s,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_nickname",dataIO($nickname,'out'),time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_grade",$reg_grade,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_birthday",$birthday,time()+720000,"/",$_ZEAI['CookDomain']);
	set_data_ed_bfb($uid);
	//if(ifint($tguid) && $tg['flag'] == 1){
	if(ifint($tguid) && in_array('tg',$navarr)){
		$db->query("UPDATE ".__TBL_USER__." SET tguid=".$tguid." WHERE id=".$uid);
		TG($tguid,$uid,'reg');
	}
	json_exit(array('flag'=>1,'reg_kind'=>$reg_kind));
}
function chk_uname($uname,$pwd){
	global $db,$_REG;
	if($_REG['reg_kind'] == 1){
		if (!ifmob($uname))json_exit(array('flag'=>0,'msg'=>'请输入正确的手机号码'));
		$unamestr = '手机号码';
	}elseif($_REG['reg_kind'] == 2){
		if (ifmob($uname) || ifint($uname))json_exit(array('flag'=>0,'msg'=>'登录用户名不能是手机号码和纯数字'));
		if (str_len($uname) > 20 || str_len($uname)<3 || !preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u',$uname) )json_exit(array('flag'=>0,'msg'=>'请输入正确的用户名（3~15位字母或加数字组合）'));
		$unamestr = '用户名';
	}
	$row = $db->ROW(__TBL_USER__,'id',"uname='".$uname."' OR (mob='$uname' AND FIND_IN_SET('mob',RZ)) ");
	if($row)json_exit(array('flag'=>0,'msg'=>'此'.$unamestr.'已被注册,请重新输入'));
	if (str_len($pwd) > 20 || str_len($pwd)<6 )json_exit(array('flag'=>0,'msg'=>'请输入正确的密码(长度6~20)'));
}
/********************************************************开始********************************************************/
$t = (!empty($t))?$t:'sex';
$cARR   = array('#FCEFF4','#F4F4FF','#FBF7E4','#FFF7F7','#FFF7FF','#ECFBFF','#F0F8EF');
$randbg = $cARR[array_rand($cARR)];
if ($t == "success"){
	$none=' style="display:none"';
}
if ($t != "sex" ){
	?>
    <i class="ico goback" id="ZEAIGOBACK-reg_<?php echo $t;?>" <?php echo $none;?>>&#xe602;</i>
    <?php
}
if ($_REG['reg_flag']==3){?>
	<div class="submain" style="background-color:#fff">
	<div class="nodataSorry"><i class="ico">&#xe61f;</i><font>新会员注册已关闭</font></div>
	</div>
<?php exit;}?>



<?php if($t == 'area'){ ?>
	<style>
    .area{background-color:<?php echo $randbg;?>}
    </style>
	<div class="submain <?php echo $t;?>">
    	<h1>您的工作地区在哪里？</h1>
		<script>
			if (zeai.empty(REG['sex']))zeai.msg('资料遗漏，请返回重选');
            Sbindbox = 'reg_area';
            ios_select_next('dq',areaARR1,areaARR2,areaARR3,REG['areaid'],function(obj1,obj2,obj3){
                var areaid    = obj1.i + ',' + obj2.i + ',' + obj3.i;
                var areatitle = obj1.v + ' ' + obj2.v + ' ' + obj3.v;
                REG['areaid'] = areaid;
                REG['areatitle'] = areatitle;
				ZeaiM.page.load('reg_alone'+zeai.ajxext+'t=birthday','reg_<?php echo $t;?>','reg_birthday');
            },',');
        </script>
	</div>
<?php }elseif($t == 'birthday'){ ?>
	<style>
    .birthday{background-color:<?php echo $randbg;?>}
    </style>
    <div class="submain <?php echo $t;?>">
    	<h1>您的生日是哪一天呢？</h1>
        <div class="vtphoto" id="vtphoto"></div>
		<script>
        if (zeai.empty(REG['sex']) || zeai.empty(REG['areaid'])){zeai.msg('资料遗漏，请返回重选');}
        switch (REG['sex']) {
            case 1:ico='&#xe60c;';cls='sex1';break;
            case 2:ico='&#xe95d;';cls='sex2';break;
            default:ico='&#xe61f;';cls='sex'+REG['sex'];break;
        }
        vtphoto.append('<div class="'+cls+'"><i class="ico">'+ico+'</i></div>');
        //
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
        Sbindbox = 'reg_birthday';
		if (zeai.empty(REG['birthday']))REG['birthday']='1995-01-15';
        ios_select_next('sr',yearData, monthData, dateData,REG['birthday'],function(obj1,obj2,obj3){
            REG['birthday'] = obj1.i + '-' + obj2.i + '-' + obj3.i;
            ZeaiM.page.load('reg_alone'+zeai.ajxext+'t=heigh','reg_<?php echo $t;?>','reg_heigh');
        },'-');
        </script>
	</div>
<?php }elseif($t == 'heigh'){ ?>
	<style>
    .heigh{background-color:<?php echo $randbg;?>}
	#num {text-align:center;margin:20px;font-size:20px}
	#ruler-container {position:relative;overflow:hidden;width:19rem;height:5rem;border:2px solid #57C0FF;margin:0 auto;-webkit-user-select:none;}
	#triangle {width:0;height:0;margin:0 auto;border-top:1rem solid #57C0FF;border-left:1rem solid transparent;border-right:1rem solid transparent;}
	#ruler{-webkit-transition:transform .2s ease-out;transition:transform .2s ease-out}
	#ruler ul {transform:translateX(10rem);height:4rem;width:65rem;position:relative}
	#ruler ul li{height:100%;width:6.5rem;background:url(img/ruler.png)left top no-repeat;background-size:100px auto;float:left;text-align:right}
	#ruler ul li span {position:relative;top:2rem;right:-.6rem;font-size:16px}
	.heigh button{display:block;margin:30px auto}
    </style>
    <div class="submain <?php echo $t;?>">
    	<h1>选择您的身高 <font class="S14 middle">(按住标尺左右滑动)</font></h1>
        <h2 id="num">0</h2>
        <div id="ruler-container">
            <div id="triangle"></div>
            <div id="ruler" data-offset="0">
                <ul id="ruler-ul">
                    <li><span>130</span></li>
                    <li><span>140</span></li>
                    <li><span>150</span></li>
                    <li><span>160</span></li>
                    <li><span>170</span></li>
                    <li><span>180</span></li>
                    <li><span>190</span></li>
                    <li><span>200</span></li>
                    <li><span>210</span></li>
                    <li><span>220</span></li>
                </ul>
            </div>
        </div>
        <button type="button" class="btn size4 W85_ HONG yuan" onClick="ZeaiM.page.load('reg_alone'+zeai.ajxext+'t=edu','reg_<?php echo $t;?>','reg_edu');">下一步</button>
		<script>
		(function rulerSelect(json){
			var defv = (zeai.empty(json.value))?(json.min+json.max)/2:json.value;
			REG['heigh']=defv;
			o('num').html(defv+'cm');
			var Min =json.min,Max =json.max;
			new IScroll('#ruler-container',{
				hScrollbar:false,
				vScroll:true,
				bounce:true,
				click:true,
				hideScrollbar:true
			});
			var rulerUl = o('ruler-ul');
			var num     = o('num');
			var ruler   = o('ruler');
			var offsetX = 0;
			var moveX = 0;
			var moveBefore = 0;
			var unit = 0.64;
			var aaa=-(defv-Min)*unit-0.75;
			ruler.style = "transform:translateX(" + aaa + "rem)";
			ruler.addEventListener('touchstart', function (event) {
				offsetX = event.touches[0].clientX;//手指按下时坐标
				moveBefore = 0; //第一次滑动的距离为0
			});
			rulerUl.addEventListener('touchmove', function (event) {
				var move = event.touches[0].clientX;//获取滑动时手指的动态坐标
				var offset = ruler.dataset.offset;//上一次计算出的刻度尺移动距离
				offset = parseFloat(offset);
				var tempMove = 0;
				var len = 0;
				tempMove = move - offsetX; //相对于手指按下时的距离，除以10是因为要将px转换为rem单位
				tempMove /= 16;
				len = offset + (tempMove - moveBefore);//两次滑动间距离
				len = parseFloat(len);
				var start = -(65.3-Math.abs(aaa));
				var end   = Math.abs(aaa);
				if (len >start && len <end){
					moveX = tempMove;//将结果保存下来，下一次滑动时取出参与计算
					ruler.dataset.offset = len;
					moveBefore = moveX;
					ruler.style = "transform:translateX(" + (len+aaa) + "rem)";
					var heigh = defv+Math.round(-(len / unit));
					if (heigh>Max)heigh=Max;
					if (heigh<Min)heigh=Min;
					num.innerText =heigh+'cm';
					REG['heigh']=heigh;
				}
			}, false);
		})({'value':REG['heigh'],'min':120,'max':220})
        </script>
    </div>
<?php }elseif($t == 'edu'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center/*line-height:44px;font-size:18px;text-align:center;border-bottom:#ddd 1px solid*/}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>您的学历</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="<?php echo $t;?>"></ul>
		<script>reg_alone_udata('<?php echo $t;?>','love');</script>
	</div>
<?php }elseif($t == 'love'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{display:block;margin:10px auto;text-align:center}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>请问您的婚姻状况？</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="<?php echo $t;?>"></ul>
		<script>reg_alone_udata('<?php echo $t;?>','job');</script>
	</div>
<?php }elseif($t == 'job'){ ?>
    <style>
	 .<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	 .<?php echo $t;?> ul li{float:left;width:29%;margin:10px 2%}
	 .<?php echo $t;?> i.ico{line-height:140px;}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>请问您的职业？</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="<?php echo $t;?>"></ul>
		<script>reg_alone_udata('<?php echo $t;?>','pay');</script>
	</div>
<?php }elseif($t == 'pay'){ ?>
    <style>
	.<?php echo $t;?>{background-color:<?php echo $randbg;?>}
	.<?php echo $t;?> ul li{float:left;width:40%;margin:10px 5%}
	.<?php echo $t;?> i.ico{line-height:140px;}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>请问您的月收入？</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>"></div>
        <ul id="<?php echo $t;?>"></ul>
		<script>reg_alone_udata('<?php echo $t;?>','end');</script>
	</div>
<?php }elseif($t == 'end'){
	?>
    <style>
	.<?php echo $t;?>{background-color:#fff}
	.submain h1{width:85%;font-weight:bold;text-align:left;font-size:26px;margin:0 auto 20px auto}
	</style>
    <div class="submain <?php echo $t;?>">
    	<h1>登录帐号/联系方式</h1>
        <div class="vtphoto" id="vtphoto<?php echo $t;?>" style="display:none"></div>
        <form id="ZEAI_form_reg">
        
        <?php if($_REG['reg_kind'] == 1 || $_REG['reg_kind'] == 3){?>
			<dl><dt><i class="ico">&#xe627;</i></dt><dd><input name="mob" type="text" class="input_login" id="mob" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*" ></dd></dl>
			<?php if($_REG['reg_kind'] == 3){//用户名?>
				<dl><dt><i class="ico">&#xe645;</i></dt><dd><input name="uname" type="text" class="input_login" id="uname" placeholder="请输入3-15个字符登录用户名" autocomplete="off" maxlength="20" ></dd></dl>
			<?php }?>
			<input type="hidden" name="submitok" value="ajax_reg_yzm">
        <?php }elseif($_REG['reg_kind'] == 2){ ?>
            <dl><dt><i class="ico">&#xe645;</i></dt><dd><input name="uname" type="text" class="input_login" id="uname" placeholder="请输入3-15个字符登录用户名" autocomplete="off" maxlength="20"  ></dd></dl>
            <input type="hidden" name="submitok" value="ajax_reg_uname_addupdate">
        <?php }?>
        
            <dl><dt><i class="ico" style="font-size:26px">&#xe61e;</i></dt><dd><input name="pwd" type="password" class="input_login" id="pwd" placeholder="请设置登录密码(长度6~20)" autocomplete="off" maxlength="20" onBlur="rettop();"></dd></dl>
            <?php if ($_REG['reg_force_wx'] == 1){?>
            <dl><dt> <i class="ico" style="font-size:20px;margin-left:1px">&#xe607;</i></dt><dd><input name="weixin" type="text" class="input_login" id="weixin" placeholder="请输入微信号" autocomplete="off" maxlength="30" onBlur="rettop();" ></dd></dl>
            <?php }?>
            <input type="button" value="下一步" class="btn size4 HONG W85_ B" id="submitbtn_reg" onclick="chkform();rettop();">
        </form>
		<script>
		var reg_kind=<?php echo $_REG['reg_kind'];?>,reg_force_wx=<?php echo $_REG['reg_force_wx'];?>;
		function rettop(){zeai.setScrollTop(0);}
        </script>
	</div>
<?php }elseif($t == 'yzm'){
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}
		$mobstr = substr($mob,0,3).'****'.substr($mob,-4,4);
	?>


    <style>
	 .yzm{background-color:#fff}
	</style>
    <div class="submain <?php echo $t;?>">
    
    
        <h1>输入短信验证码</h1>
        <h3>验证码已发送至<?php echo $mobstr;?>，请在下方输入框内输入4位数字验证码<!--  　请输：<font class="Cf00 S18"><?php echo $_SESSION['Zeai_cn__mobyzm'];?></font>，现在是演示，实际使用将发送到手机短信  --></h3>

        <div class="yzmnumbox" id="yzmnumbox">
        	<div class="yzmerrstrbox"><span id="yzmerrstr">验证码错误</span></div>
            <input type="text" maxlength='4' id="verify" pattern="[0-9]*" autocomplete="off" ><ul id="yzmul"><li></li><li></li><li></li><li></li></ul>
        </div>
        <button type="button" id="yzmbtn">120s后重新发送</button>
		<script>
			if (!zeai.empty(o('yzmbtn'))){
				yzmbtn.onclick = function(){
					if (zeai.ifmob(REG['mob'])){
						if (!this.hasClass('disabled')){
							yzmbtn.addClass('disabled');
							var uname=(!zeai.empty(REG['uname']))?REG['uname']:'';
							zeai.ajax({'url':'reg_alone'+zeai.extname,'data':{'submitok':'ajax_reg_yzm','mob':REG['mob'],uname:uname,REG:JSON.stringify(REG)}},function(e){
								var rs=zeai.jsoneval(e);
								zeai.msg(rs.msg);
								if (rs.flag == 1){
									yzmtimeFn(120);
								}else{
									yzmbtn.removeClass('disabled');
								}
							});
						}
					}else{
						zeai.msg('请输入手机号码',{mask:0});
						return false;
					}
				}
				yzmtimeFn(120);
			}
			function yzmtimeFn(countdown) { 
				if (countdown == 0) {
					yzmbtn.removeClass('disabled');
					yzmbtn.html('<font>重新获取验证码</font>'); 
					return false;
				} else { 
					if (!zeai.empty(o('yzmbtn'))){
						yzmbtn.addClass('disabled');
						yzmbtn.html(countdown + "s后重新发送"); 
						countdown--; 
					}
				} 
				cleandsj=setTimeout(function(){yzmtimeFn(countdown)},1000);
			}
			verify.oninput=function(){
				if(zeai.str_len(this.value) == 4){
					this.blur();
					zeai.ajax({url:'reg_alone'+zeai.extname,data:{tguid:sessionStorage.tguid,'submitok':'ajax_reg_yzm_addupdate','verify':verify.value,'REG':JSON.stringify(REG)}},function(e){var rs=zeai.jsoneval(e);
						if (rs.flag == 1){
							//ZeaiM.page.load({'url':'reg_alone'+zeai.extname,data:{t:'success'}},'reg_<?php echo $t;?>','reg_success');
							zeai.openurl('reg_ed'+zeai.ajxext+'t=success');
						}else{
							yzmerrstr.html(rs.msg);
							yzmerrstr.show();yzmerrstr.addClass('shakeLR');setTimeout(function(){yzmerrstr.removeClass('shakeLR');},200);
							yzmnumbox.addClass('shakeLR_loop');setTimeout(function(){yzmnumbox.removeClass('shakeLR_loop');},200);
							verify.value='';verify.focus();setTimeout(function(){yzmerrstr.hide();},2000);
						}
					});
				}
			}
			zeai.setScrollTop(0);
        </script>
        
        
	</div>

<?php }else{?>
	<?php
	$sexARR = json_decode($_UDATA['sex']);
	$headertitle = '新会员注册-';$nav = 'my';
	?>
    
    

<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $headertitle;?><?php echo $_ZEAI['siteName'];?></title>
<?php echo HEADMETA; ?>
<meta name="x5-orientation" content="portrait">
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/m1/css/m1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/m1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php
/*if (is_weixin()){
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
<?php }*/?>
<script src="<?php echo HOST;?>/res/zeai_ios_select/separate/select.js"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var jumpurl='<?php echo urlencode($jumpurl);?>',tmpid='<?php echo $tmpid;?>',t='<?php echo $t;?>';sessionStorage.tguid='<?php echo $tguid;?>';
var upMaxMB = <?php echo intval($_UP['upMaxMB']); ?>,browser='<?php echo (is_weixin())?'wx':'h5';?>';
</script>
<script src="js/reg_alone.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="css/reg_alone.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style>
.sex{background-color:<?php echo $randbg;?>;padding:50px 0 0}
.sex i.ico{line-height:140px;}
.sex em{text-align:center;padding:0 0}
.sex h2{font-size:18px}
.sex h4{color:#999;font-size:14px}
.sex em div{margin:30px auto}
</style>
</head>
<body>
<?php if ($ifback==1){?><a href="login.php?tmpid=<?php echo $tmpid;?>&tguid=<?php echo $tguid;?>" class="ico gobackA">&#xe602;</a><?php }?>
    <div class="submain <?php echo $t;?> huadong" id="main">
        <h1>您是男神还是女神？</h1>
        <em>
        <?php
        if (count($sexARR) >= 1 && is_array($sexARR)){
            foreach ($sexARR as $V) {
                switch ($V->i) {
                    case 1:$ico='&#xe60c;';$ename  = 'Man';break;
                    case 2:$ico='&#xe95d;';$ename  = 'Woman';break;
                    default:$ico='&#xe61f;';$ename = 'zeai.cn';break;
                }
                echo '<div class="sex'.$V->i.'" onclick="sex('.$V->i.');"><i class="ico">'.$ico.'</i><h2>'.$V->v.'神</h2><h4>'.$ename.'</h4></div>';
            }
        }
        ?>
        </em>
    </div>
	<script>
	function sex(sex){REG['sex'] = sex;ZeaiM.page.load('reg_alone'+zeai.ajxext+'t=area','main','reg_area');}
	reg();
    </script>
    <div id="Zeai_cn__PageBox"></div>
</body>
</html>
<?php }?>
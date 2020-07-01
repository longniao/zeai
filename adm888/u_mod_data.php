<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if($session_kind == 'crm'){
	if(!in_array('crm_user_mod',$QXARR))exit(noauth());
}
require_once ZEAI.'cache/udata.php';$sex_ARR = json_decode($_UDATA['sex'],true);$extifshow = json_decode($_UDATA['extifshow'],true);
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
header("Cache-control: private");
if (!ifint($uid))alert_adm("forbidden","-1");
$rz_dataARR = explode(',',$_ZEAI['rz_data']);

if($submitok == 'ajax_set'){
	if(empty($objname))exit(JSON_ERROR);
	if($v==1){
		$v_str='设置成功';
	}else{
		$v=0;
		$v_str='设置成功';
	}
	switch ($objname) {
		case 'mob_ifshow':$sql="mob_ifshow=".$v;break;
		case 'qq_ifshow':$sql="qq_ifshow=".$v;break;
		case 'weixin_ifshow':$sql="weixin_ifshow=".$v;break;
		case 'weixin_pic_ifshow':$sql="weixin_pic_ifshow=".$v;break;
		case 'email_ifshow':$sql="email_ifshow=".$v;break;
	}
	$db->query("UPDATE ".__TBL_USER__." SET ".$sql." WHERE id=".$uid);
	json_exit(array('flag'=>1,'msg'=>$v_str));
}
/******************************* 修改 ******************************/
if ($submitok == 'rzpicDel'){
	if(empty($picid))alert_adm("forbidden","-1");
	$picid = explode('_',$picid);
	$V=$picid[0];$i=$picid[1];
	$row = $db->ROW(__TBL_RZ__,"path_b,path_b2","uid=".$uid." AND rzid='$V'","name");
	if ($row){
		$path_b = $row['path_b'];$path_b2 = $row['path_b2'];
		if($i==1){
			@up_send_admindel($path_b);
			$db->query("UPDATE ".__TBL_RZ__." SET path_b='' WHERE uid=".$uid." AND rzid='$V'");
		}elseif($i==2){
			@up_send_admindel($path_b2);
			$db->query("UPDATE ".__TBL_RZ__." SET path_b2='' WHERE uid=".$uid." AND rzid='$V'");
		}
		$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
		AddLog('删除【'.$nickname.'（uid:'.$uid.'）】认证图片->'.rz_data_info($V,'title'));
	}
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok == 'modupdate'){
	$setsql = "";$ifnext = true;
	switch ($t) {
		case 1:
			$ifadm     = intval($ifadm);
			$kind       = intval($kind);
			$flag       = intval($flag);
			$uname   = dataIO($uname,'in',40);
			$truename   = dataIO($truename,'in',12);
			$nickname   = dataIO(trimhtml($nickname),'in',50);
			$openid     = dataIO($openid,'in',32);
			$union      = dataIO($union,'in',32);
			$sex        = intval($sex);$sex=(!ifint($sex))?1:$sex;
			$areaid     = dataIO($areaid,'in',50);
			$areatitle  = dataIO($areatitle,'in',100);
			$area2id    = dataIO($area2id,'in',50);
			$area2title = dataIO($area2title,'in',100);
			$birthday   = (!ifdate($birthday))?'0000-00-00':$birthday;
			$aboutus    = dataIO(trimhtml($aboutus),'in',1000);
			$love       = intval($love);
			$heigh      = intval($heigh);
			$weigh      = intval($weigh);
			$marrytype  = intval($marrytype);
			$marrytime  = intval($marrytime);
			$edu        = intval($edu);
			$pay        = intval($pay);
			$house      = intval($house);
			$car        = intval($car);
			$nation     = intval($nation);
			$child      = intval($child);
			$blood      = intval($blood);
			$job        = intval($job);
			$tag        = (is_array($tag))?implode(",",$tag):'';
			$identitynum= dataIO($identitynum,'in',20);
			$pwd   = dataIO($pwd,'in',20);
			$tguid      = intval($tguid);
			$tgpic      = dataIO($tgpic,'in',50);
			$photo_ifshow = intval($photo_ifshow);
			$xqk_ifshow = intval($xqk_ifshow);
			$parent  = intval($parent);
			$agentid = intval($agentid);
			$admid   = intval($admid);
			$hnid    = intval($hnid);
			$hnid2   = intval($hnid2);
			$subscribe   = intval($subscribe);
			//
			$setsql .= "subscribe='$subscribe',parent='$parent',photo_ifshow='$photo_ifshow',xqk_ifshow='$xqk_ifshow',flag='$flag',nickname='$nickname',ifadm='$ifadm',kind='$kind',sex='$sex',aboutus='$aboutus',birthday='$birthday',areaid='$areaid',areatitle='$areatitle',area2id='$area2id',area2title='$area2title',love='$love',heigh='$heigh',weigh='$weigh',edu='$edu',pay='$pay',house='$house',car='$car',nation='$nation',job='$job',child='$child',blood='$blood',tag='$tag',truename='$truename',identitynum='$identitynum',tguid='$tguid',tgpic='$tgpic',task_flag='$task_flag',marrytype='$marrytype',marrytime='$marrytime'";	
			//
			if($agentid>0){
				$row3 = $db->ROW(__TBL_CRM_AGENT__,"title","id=".$agentid,"num");
				if ($row3)$setsql .= ",agentid=$agentid,agenttitle='".$row3[0]."'";
			}else{$setsql .= ",agentid=0,agenttitle=''";}
			if($admid>0){
				$row3 = $db->ROW(__TBL_CRM_HN__,"truename","id=".$admid,"num");
				if ($row3)$setsql .= ",admid=$admid,admname='".$row3[0]."'";
			}else{$setsql .= ",admid=0,admname=''";}
			if($hnid>0){
				$row3 = $db->ROW(__TBL_CRM_HN__,"truename","id=".$hnid,"num");
				if ($row3)$setsql .= ",hnid=$hnid,hnname='".$row3[0]."'";
			}else{$setsql .= ",hnid=0,hnname=''";}
			if($hnid2>0){
				$row3 = $db->ROW(__TBL_CRM_HN__,"truename","id=".$hnid2,"num");
				if ($row3)$setsql .= ",hnid2=$hnid2,hnname2='".$row3[0]."'";
			}else{$setsql .= ",hnid2=0,hnname2=''";}
			//			
			if ($openid != $openid_old){
				$row = $db->NUM('supdes','nickname',"openid='$openid' AND openid<>''");
				if($row){$varmsg.="“openid”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",openid='$openid'";}
			}
			if ($unionid != $unionid_old){
				$row = $db->NUM('supdes','nickname',"unionid='$unionid' AND unionid<>''");
				if($row){$varmsg.="“unionid”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",unionid='$unionid'";}
			}
			if ($loginkey != $loginkey_old){
				$row = $db->NUM('supdes','nickname',"loginkey='$loginkey' AND loginkey<>''");
				if($row){$varmsg.="“loginkey”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",loginkey='$loginkey'";}
			}
			
			
			if ($uname != $username_old){
				$row = $db->NUM('supdes','nickname',"uname='$uname' AND uname<>''");
				if($row){$varmsg.="“用户名”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",uname='$uname'";}
			}
			if ($email != $email_old){
				$row = $db->NUM('supdes','nickname',"email='$email' AND email<>''");
				if($row){$varmsg.="“Email”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",email='$email'";}
			}
			if (!empty($pwd) && str_len($pwd) <= 20 && str_len($pwd) >= 6){
				$pwd = md5(trimm($pwd));
				$setsql  .= ",pwd='$pwd'";
			}
			//
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
			$tguid=0;
			AddLog('修改会员【'.$nickname.'（uid:'.$uid.'）】基本资料');
		break;
		case 2:
			$sql = array();
			foreach ($extifshow as $V) {
				$fieldname = $V['f'];
				switch ($V['s']) {
					case 1://text
						$sql[] = "$fieldname='".dataIO($$fieldname,'in')."'";
					break;
					case 2://select
						$sql[] = "$fieldname=".intval($$fieldname)."";
					break;
					case 3://checkbox
						$fieldvalue = (is_array($$fieldname))?implode(',',$$fieldname):'';
						$sql[] = "$fieldname = '".$fieldvalue."'";
					break;
				}
			}
			$setsql = (is_array($sql))?implode(',',$sql):'';
			//
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
			AddLog('修改会员【'.$nickname.'（uid:'.$uid.'）】详细资料');
		break;
		case 3:
			$address    = dataIO($address,'in',100);
			$qq         = dataIO($qq,'in',15);
			$weixin     = dataIO($weixin,'in',50);
			$email      = dataIO($email,'in',50);
			$mob        =(!ifmob($mob))?0:$mob;
			$setsql .= "address='$address',weixin='$weixin',qq='$qq',email='$email'";
			//
			if ($mob != $mob_old && ifmob($mob)){
				$row = $db->ROW(__TBL_USER__,'nickname',"mob='$mob' AND mob<>'' AND FIND_IN_SET('mob',RZ)","num");
				if($row){$varmsg.="“手机”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",mob='$mob'";}
			}else{
				$setsql .= ",mob='$mob'";
			}
			//
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
			AddLog('修改会员【'.$nickname.'（uid:'.$uid.'）】联系方法');
		break;
		case 4:
			$mate_age1      = intval($mate_age1);
			$mate_age2      = intval($mate_age2);
			$mate_heigh1    = intval($mate_heigh1);
			$mate_heigh2    = intval($mate_heigh2);
			$mate_weigh1    = intval($mate_weigh1);
			$mate_weigh2    = intval($mate_weigh2);
			$mate_areaid    = dataIO($mate_areaid,'in',50);
			$mate_areatitle = dataIO($mate_areatitle,'in',100);
			$mate_areaid2    = dataIO($mate_areaid2,'in',50);
			$mate_areatitle2 = dataIO($mate_areatitle2,'in',100);
			$mate_pay       = dataIO((is_array($mate_pay))?implode(",",$mate_pay):$mate_pay,'in',50);
			$mate_edu       = dataIO((is_array($mate_edu))?implode(",",$mate_edu):$mate_edu,'in',50);
			$mate_love      = dataIO((is_array($mate_love))?implode(",",$mate_love):$mate_love,'in',50);
			$mate_house     = dataIO((is_array($mate_house))?implode(",",$mate_house):$mate_house,'in',50);
			$mate_car     = dataIO((is_array($mate_car))?implode(",",$mate_car):$mate_car,'in',50);
			$mate_child     = dataIO((is_array($mate_child))?implode(",",$mate_child):$mate_child,'in',50);
			$mate_marrytime = dataIO((is_array($mate_marrytime))?implode(",",$mate_marrytime):$mate_marrytime,'in',50);
			$mate_companykind = dataIO((is_array($mate_companykind))?implode(",",$mate_companykind):$mate_companykind,'in',50);
			$mate_smoking     = dataIO((is_array($mate_smoking))?implode(",",$mate_smoking):$mate_smoking,'in',50);
			$mate_drink       = dataIO((is_array($mate_drink))?implode(",",$mate_drink):$mate_drink,'in',50);
			$mate_job         = dataIO((is_array($mate_job))?implode(",",$mate_job):$mate_job,'in',50);
			$mate_areaid = str_replace(",,,","",$mate_areaid);
			$mate_areaid = str_replace(",,","",$mate_areaid);
			$mate_areaid2 = str_replace(",,,","",$mate_areaid2);
			$mate_areaid2 = str_replace(",,","",$mate_areaid2);
			$mate_other = dataIO($mate_other,'in',500);
			$setsql .= "mate_other='$mate_other',mate_age1=$mate_age1,mate_age2=$mate_age2,mate_heigh1=$mate_heigh1,mate_heigh2=$mate_heigh2,mate_weigh1=$mate_weigh1,mate_weigh2=$mate_weigh2,mate_pay='$mate_pay',mate_edu='$mate_edu',mate_areaid='$mate_areaid',mate_areatitle='$mate_areatitle',mate_areaid2='$mate_areaid2',mate_areatitle2='$mate_areatitle2',mate_love='$mate_love',mate_house='$mate_house',mate_car='$mate_car',mate_child='$mate_child',mate_marrytime='$mate_marrytime',mate_companykind='$mate_companykind',mate_smoking='$mate_smoking',mate_drink='$mate_drink',mate_job='$mate_job'";	
			//
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
			AddLog('修改会员【'.$nickname.'（uid:'.$uid.'）】择偶要求');
		break;
		case 6:
			if (count($rz_dataARR) >= 1 && is_array($rz_dataARR)){
				$ifadm = $session_truename.' (ID:'.$session_uid.')';
				foreach ($rz_dataARR as $k=>$V) {
					$bz = 'bz_'.$V;$bz = $$bz;
					$oldbz = 'oldbz_'.$V;$oldbz = $$oldbz;
					$FILES1 = $_FILES['pic_'.$V.'1'];
					$FILES2 = $_FILES['pic_'.$V.'2'];
					if(!empty($bz) || !empty($FILES1['tmp_name']) || !empty($FILES2['tmp_name'])){
						$row = $db->ROW(__TBL_RZ__,"id","uid=".$uid." AND rzid='$V'");
						if (!$row){
							$db->query("INSERT INTO ".__TBL_RZ__."  (uid,rzid,flag,addtime,ifadm) VALUES ($uid,'$V',0,".ADDTIME.",'".$ifadm."')");	
						}
					}
					$sql = "";
					if (!empty($FILES1['tmp_name'])){
						$dbname1 = setphotodbname('rz',$FILES1['tmp_name'],$uid.'_RZ_');
						@up_send($FILES1,$dbname1,0,'3000*3000',$uid.'_RZ_');
						$sql = ",path_b='$dbname1'";
					}
					//
					if (!empty($FILES2['tmp_name'])){
						$dbname2 = setphotodbname('rz',$FILES2['tmp_name'],$uid.'_RZ_');
						@up_send($FILES2,$dbname2,0,'3000*3000',$uid.'_RZ_');
						$sql .= ",path_b2='$dbname2'";
					}
					if(!empty($bz) && $oldbz!=$bz)$sql .= ",bz='$bz'";
					if(!empty($sql))$db->query("UPDATE ".__TBL_RZ__." SET ifadm='".$ifadm."',flag=1".$sql." WHERE uid=".$uid." AND rzid='$V'");
				}
				$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
				AddLog('修改【'.$nickname.'（uid:'.$uid.'）】认证资料');
			}
			alert_adm("<b class=\"S16\">操作成功！</b>",SELF."?submitok=mod&uid=$uid&t=$t&iframenav=$iframenav&ifmini=$ifmini");
			exit;
		break;
		default:exit;break;
	}
	if (!$ifnext){alert_adm($varmsg,'back');exit;}
	$db->query("UPDATE ".__TBL_USER__." SET ".$setsql." WHERE id=".$uid);//,dataflag=1
	set_data_ed_bfb($uid);
	alert_adm("修改成功！",SELF."?submitok=mod&uid=$uid&t=$t&iframenav=$iframenav&ifmini=$ifmini");
	
/******************************* 显示 ******************************/
}elseif($submitok == 'mod' || $submitok == 'photo'){
	if ( !ifint($uid))alert_adm("会员ID号不正确","back");
	$t = (ifint($t,'1-6','1'))?$t:1;
	$currfields  = "nickname,sex,grade,photo_s,photo_f,RZ,myinfobfb,regtime,regip,endtime,endip,click";
	$currfields .= ",agentid,admid,hnid,hnid2";
	switch ($t) {
		case 1:
			$mini_title  = '基本资料';
			$currfields .= ",subscribe,parent,photo_ifshow,xqk_ifshow,flag,ifadm,kind,uname,truename,openid,unionid,loginkey,identitynum,pwd,longitude,latitude,tguid,tgpic,task_flag,aboutus,birthday,areaid,areatitle,area2id,area2title,love,heigh,weigh,edu,pay,house,car,nation,child,blood,tag,marrytype,marrytime,job";
			set_data_ed_bfb($uid);
		break;
		case 2:
			$mini_title  = '详细资料';
			if (@count($extifshow) == 0 || !is_array($extifshow))alert_adm('暂无详细资料，请去<a class="blue" onClick=parent.pageABC(5,1,"udata3.php")>【添加字段】</a>并更新数据缓存！','back');
			foreach ($extifshow as $ev){$evARR[] = $ev['f'];}
			$currfields .= ",".implode(",",$evARR);
		break;
		case 3:
			$mini_title  = '联系方法';
			$currfields .= ",mob,address,weixin,weixin_pic,qq,email,mob_ifshow,qq_ifshow,weixin_ifshow,weixin_pic_ifshow,email_ifshow";
			set_data_ed_bfb($uid);
		break;
		case 4:
			$mini_title  = '择偶要求';
			$currfields .= ",mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2,mate_other";
		break;
		case 6:
			$mini_title  = '线下认证资料';
			$currfields .= ",mob,weixin,qq,email";
		break;
	}
	$row = $db->ROW(__TBL_USER__,$currfields,"id=".$uid);
	if(!$row)alert_adm("ID号输入有误或不存在此会员","back");
	switch ($t) {
		case 1:
			$ifadm   = $row['ifadm'];
			$kind    = $row['kind'];
			$flag    = $row['flag'];
			$photo_ifshow = $row['photo_ifshow'];
			$xqk_ifshow = $row['xqk_ifshow'];
			$uname      = dataIO($row['uname'],'out');
			$truename   = trimhtml(dataIO($row['truename'],'out'));
			$openid     = dataIO($row['openid'],'out');
			$unionid    = dataIO($row['unionid'],'out');
			$loginkey   = dataIO($row['loginkey'],'out');
			$identitynum= dataIO($row['identitynum'],'out');
			$pwd   = dataIO($row['pwd'],'out');
			//		
			$aboutus    = trimhtml(dataIO($row['aboutus'],'out'));
			$birthday   = $row['birthday'];
			$birthday   = (!ifdate($birthday))?'':$birthday;
			$areaid     = dataIO($row['areaid'],'out');
			$areatitle  = dataIO($row['areatitle'],'out');
			$area2id    = dataIO($row['area2id'],'out');
			$area2title = dataIO($row['area2title'],'out');
			$heigh      = $row['heigh'];
			$weigh      = $row['weigh'];
			$love       = $row['love'];
			$edu        = $row['edu'];
			$pay        = $row['pay'];
			$house      = $row['house'];
			$car        = $row['car'];
			$nation     = $row['nation'];
			$marrytype  = $row['marrytype'];
			$marrytime  = $row['marrytime'];
			$job        = $row['job'];
			$child      = $row['child'];
			$blood      = $row['blood'];
			$tag        = $row['tag'];
			$longitude  = $row['longitude'];
			$latitude   = $row['latitude'];
			$tguid      = $row['tguid'];
			$tgpic      = $row['tgpic'];
			$task_flag  = $row['task_flag'];
			$parent  = $row['parent'];
			$subscribe  = $row['subscribe'];
			//
			$birthday_  = ($birthday == '0000-00-00')?(YmdHis($ADDTIME,'Y')-25).'-01'.'-15':$birthday;
			$areaid     = explode(',',$areaid);
			$a1 = $areaid[0];$a2 = $areaid[1];$a3 = $areaid[2];$a4 = $areaid[3];
			$area2id    = explode(',',$area2id);
			$a11 = $area2id[0];$a22 = $area2id[1];$a33 = $area2id[2];$a44 = $area2id[3];
		break;
		case 2:
			$row2 = $row;
		break;
		case 3:
			$agentid = intval($row['agentid']);
			$admid   = intval($row['admid']);
			$hnid    = intval($row['hnid']);
			$hnid2   = intval($row['hnid2']);
			if(!crm_ifcontact($agentid,$admid,$hnid,$hnid2) && $session_kind == 'crm'){
				exit(noauth('暂无联系方法查看权限'));
			}
			$address    = dataIO($row['address'],'out');
			$weixin     = dataIO($row['weixin'],'out');
			$qq         = dataIO($row['qq'],'out');
			$email      = dataIO($row['email'],'out');
			$mob        = dataIO($row['mob'],'out');
			$weixin_pic = $row['weixin_pic'];
			$data_mob_ifshow        = $row['mob_ifshow'];
			$data_qq_ifshow         = $row['qq_ifshow'];
			$data_weixin_ifshow     = $row['weixin_ifshow'];
			$data_weixin_pic_ifshow = $row['weixin_pic_ifshow'];
			$data_email_ifshow      = $row['email_ifshow'];
			
		break;
		case 4:
			$mate_age1      = intval($row['mate_age1']);
			$mate_age2      = intval($row['mate_age2']);
			$mate_heigh1    = intval($row['mate_heigh1']);
			$mate_heigh2    = intval($row['mate_heigh2']);
			$mate_weigh1    = intval($row['mate_weigh1']);
			$mate_weigh2    = intval($row['mate_weigh2']);
			$mate_pay       = $row['mate_pay'];
			$mate_edu       = $row['mate_edu'];
			$mate_areaid    = $row['mate_areaid'];
			$mate_areatitle = $row['mate_areatitle'];
			$mate_love      = $row['mate_love'];
			$mate_car       = $row['mate_car'];
			$mate_house     = $row['mate_house'];
			$mate_weigh1      = intval($row['mate_weigh1']);
			$mate_weigh2      = intval($row['mate_weigh2']);
			$mate_job         = $row['mate_job'];
			$mate_child       = $row['mate_child'];
			$mate_marrytime   = $row['mate_marrytime'];
			$mate_companykind = $row['mate_companykind'];
			$mate_smoking     = $row['mate_smoking'];
			$mate_drink       = $row['mate_drink'];
			$mate_areaid2     = $row['mate_areaid2'];
			$mate_areatitle2  = $row['mate_areatitle2'];
			$mate_other       = dataIO($row['mate_other'],'out');
			$mate_areaid    = explode(',',$mate_areaid);
			$mate_areaid2   = explode(',',$mate_areaid2);
			$m1 = $mate_areaid[0];$m2 = $mate_areaid[1];$m3 = $mate_areaid[2];$m4 = $mate_areaid[3];
			$h1 = $mate_areaid2[0];$h2 = $mate_areaid2[1];$h3 = $mate_areaid2[2];$h4 = $mate_areaid2[3];
		break;
		case 6:
			$qq    = dataIO($row['qq'],'out');
			$email = dataIO($row['email'],'out');
			$mob   = dataIO($row['mob'],'out');
		break;
	}
	
	
	
	
	
	
	$agentid = intval($row['agentid']);
	$admid   = intval($row['admid']);
	$hnid    = intval($row['hnid']);
	$hnid2   = intval($row['hnid2']);
	$nickname = dataIO($row['nickname'],'out');
	$sex      = $row['sex'];
	$grade    = $row['grade'];
	$photo_s  = $row['photo_s'];
	$photo_f  = $row['photo_f'];
	$RZ = $row['RZ'];$RZarr = explode(',',$RZ);
	$myinfobfb= $row['myinfobfb'];
	$regtime    = YmdHis($row['regtime']);
	$regip      = $row['regip'];
	$endtime    = YmdHis($row['endtime']);
	$endip      = $row['endip'];
	$click      = $row['click'];
	switch ($grade) {
		case 1:$gradestyle   = " class='aHUI'";break;
		case 2:$gradestyle   = " class='aLAN'";break;
		case 3:$gradestyle   = " class='aZI'";break;
		case 4:$gradestyle   = " class='aHUANG'";break;
		case 5:$gradestyle   = " class='aJIN'";break;
		case 6:$gradestyle   = " class='aHONG'";break;
		case 7:$gradestyle   = " class='aLV'";break;
		case 8:$gradestyle   = " class='aHEI'";break;
		case 9:$gradestyle   = " class='aQING'";break;
		case 10:$gradestyle  = " class='aQINGed'";break;
	}
	//
	if($session_kind=='crm'){
		$uhref = Href('crm_u',$uid);
	}else{
		$uhref = Href('u',$uid);
	}
	if(empty($row['nickname'])){
		if(empty($row['truename'])){
			$title = $mob;
		}else{
			$title = dataIO($row['truename'],'out');
		}
	}else{
		$title = $nickname;
	}
	$mob = (!empty($mob))?$mob:'';
	if(!empty($photo_s)){
		$photo_m_url = getpath_smb($_ZEAI['up2'].'/'.$photo_s,'m');
		$photo_b_url = getpath_smb($_ZEAI['up2'].'/'.$photo_s,'b');
		$photo_m_str = '<img src="'.$photo_m_url.'?'.ADDTIME.'">';
	}else{
		$photo_m_url = HOST.'/res/photo_m'.$sex.'.png';
		$photo_m_str = '<img src="'.$photo_m_url.'">';
	}	
	
	$photo_fstr = ($photo_f == 0 && !empty($photo_s))?'<span>审核中</span>':'';
}elseif($submitok == 'zeai_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbpicname = setphotodbname('tmp',$file['tmp_name'],'',$uid);/*$tmp*/
		if (!up_send($file,$dbpicname,$_UP['ifwaterimg'],$_UP['upBsize']))alert_adm("上传图片失败","");
		if (!ifpic($_ZEAI['up2']."/".$dbpicname))alert_adm("图片格式错误","-1");
		json_exit(array('flag'=>1,'tmpphoto'=>$dbpicname));
	}else{
		json_exit(array('flag'=>0));
	}
}elseif($submitok == 'del_photo_s_update'){
	$rt = $db->query("SELECT photo_s,nickname FROM ".__TBL_USER__." WHERE id=".$uid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$path_s = $row[0];$nickname= $row[1];
		if (!empty($path_s)){
			$path_m = getpath_smb($path_s,'m');$path_b = getpath_smb($path_s,'b');$path_blur = str_replace("_b.","_blur.",$path_b);
			@up_send_admindel($path_s.'|'.$path_m.'|'.$path_b.'|'.$path_blur);
		}
		$db->query("UPDATE ".__TBL_USER__." SET photo_s='',photo_f=0 WHERE id=".$uid);
		//
		$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
		AddLog('删除会员【'.$nickname.'（uid:'.$uid.'）】头像');
	}
	header("Location: ".SELF."?submitok=mod&uid=".$uid);
}elseif($submitok == 'ajax_photo_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbpicname = setphotodbname('photo',$file['tmp_name'],$uid.'_');
		$_s = setpath_s($dbpicname);
		if (!up_send($file,$dbpicname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize']))alert_adm("图片写入失败","");
		if (!ifpic($_ZEAI['up2']."/".$_s))alert_adm("图片格式错误","-1");
		$db->query("INSERT INTO ".__TBL_PHOTO__." (uid,path_s,flag,addtime) VALUES ($uid,'$_s',1,".ADDTIME.")");
		//
		$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
		AddLog('上传会员【'.$nickname.'（uid:'.$uid.'）】相册');
		json_exit(array('flag'=>1,'tmpphoto'=>$dbpicname));
	}else{
		json_exit(array('flag'=>0));
	}
}elseif($submitok == 'ajax_photo_del'){
	$row = $db->ROW(__TBL_PHOTO__,"path_s","id=".$id);
	if ($row){
		$path_s = $row[0];
		if (!empty($path_s)){
			$path_b = getpath_smb($path_s,'b');
			@up_send_admindel($path_s.'|'.$path_b);
			$db->query("DELETE FROM ".__TBL_PHOTO__." WHERE id=".$id);
			//
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
			AddLog('删除会员【'.$nickname.'（uid:'.$uid.'）】相册');
		}
	}exit;
}elseif($submitok == 'ajax_tgpic_up'){
//	if (ifpostpic($file['tmp_name'])){
//		$dbpicname = setphotodbname('tgewm',$file['tmp_name'],$uid.'_');
//		if (!up_send($file,$dbpicname,0,'1200*1200'))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
//		if (!ifpic($_ZEAI['up2']."/".$dbpicname))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
//		$db->query("UPDATE ".__TBL_USER__." SET tgpic='$dbpicname' WHERE id=".$uid);
//		json_exit(array('flag'=>1,'tmpphoto'=>$_ZEAI['up2']."/".$dbpicname));
//	}else{
//		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
//	}
}elseif($submitok == 'ajax_tgpic_del'){
	$row = $db->NUM($uid,"tgpic");
	if ($row){$path_s = $row[0];
		if (!empty($path_s)){
			@up_send_admindel($path_s);
			$db->query("UPDATE ".__TBL_USER__." SET tgpic='' WHERE id=".$uid);
		}
	}
	json_exit(array('flag'=>1,'msg'=>'删除成功！'));
}elseif($submitok == 'ajax_weixin_pic_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbpicname = setphotodbname('photo',$file['tmp_name'],$uid.'_wxpic_');
		if (!up_send($file,$dbpicname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		if (!ifpic($_ZEAI['up2']."/".$dbpicname))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		$db->query("UPDATE ".__TBL_USER__." SET weixin_pic='$dbpicname' WHERE id=".$uid);
		//
		$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
		AddLog('上传会员【'.$nickname.'（uid:'.$uid.'）】微信二维码');
		json_exit(array('flag'=>1,'tmpphoto'=>$_ZEAI['up2']."/".$dbpicname));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}elseif($submitok == 'ajax_weixin_pic_del'){
	$row = $db->NUM($uid,"weixin_pic");
	if ($row){$path_s = $row[0];
		if (!empty($path_s)){
			@up_send_admindel($path_s);
			$db->query("UPDATE ".__TBL_USER__." SET weixin_pic='' WHERE id=".$uid);
			//
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
			AddLog('删除会员【'.$nickname.'（uid:'.$uid.'）】微信二维码');
		}
	}
	json_exit(array('flag'=>1,'msg'=>'删除成功！'));
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php if($t == 4){?>
	<script>
	var nulltext = '不限';
	var mate_areaid_ARR1 = areaARR1,
	mate_areaid_ARR2 = areaARR2,
	mate_areaid_ARR3 = areaARR3,
	mate_areaid_ARR4 = areaARR4;
	var mate_areaid2_ARR1 = areaARRhj1,
	mate_areaid2_ARR2 = areaARRhj2,
	mate_areaid2_ARR3 = areaARRhj3,
	mate_areaid2_ARR4 = areaARRhj4;
	var
	mate_age_ARR  = age_ARR,
	mate_age_ARR1 = age_ARR,
	mate_age_ARR2 = age_ARR,
	mate_heigh_ARR  = heigh_ARR,
	mate_heigh_ARR1 = heigh_ARR,
	mate_heigh_ARR2 = heigh_ARR,
	mate_weigh_ARR  = weigh_ARR,
	mate_weigh_ARR1 = weigh_ARR,
	mate_weigh_ARR2 = weigh_ARR,
	mate_pay_ARR = pay_ARR,
	mate_edu_ARR = edu_ARR,
	mate_love_ARR = love_ARR,
	mate_job_ARR = job_ARR,
	mate_house_ARR = house_ARR,
	mate_child_ARR = child_ARR,
	mate_marrytime_ARR = marrytime_ARR,
	mate_companykind_ARR = companykind_ARR,
	mate_smoking_ARR = smoking_ARR,
	mate_drink_ARR = drink_ARR,
	mate_car_ARR = car_ARR;
    </script>
<?php }?>
<script>
function cut(id,nkname,p) {zeai.iframe('裁切【'+nkname+'】主头像','u_photo_cut.php?ifm=1&id='+uid+'&submitok=www___zeai__cn_inphotocut'+'&p='+p,650,560);}
var upMaxMB = <?php echo $_UP['upMaxMB']; ?>;
var uid = '<?php echo $uid; ?>';
function up_m(id,nkname) {
	var pic = o('pic');
	pic.click();
	pic.onchange = function(){
		var FILES = pic.files[0];
		if (FILES['size'] > upMaxMB*1024000){pic.value='';zeai.alert('图片【'+FILES['name']+'】太大，已超过'+upMaxMB+'M，请重新选择');return false;}
		var filename = FILES['name'].toLowerCase();
		var ftype    = filename.substring(filename.lastIndexOf("."),filename.length);
		if ((ftype != '.jpg')&&(ftype != '.jpeg')&&(ftype != '.gif')&&(ftype != '.png')){pic.value='';zeai.alert('只能上传 .jpg 或 .gif 格式的图片,请重新选择!');return false;}
		setTimeout(zeai.msg('<img src="images/loadingData.gif" class="picmiddle">图片【'+FILES['name']+'】正在上传中',{time:30}),300);
		//POST
		var postjson = {"submitok":"zeai_up","file":FILES,"uid":uid};
		zeai.ajax({url:'u_mod_data'+zeai.ajxext,data:postjson},function(e){var rs=zeai.jsoneval(e);
			pic.value='';
			zeai.msg('',{flag:'hide'});
			if (rs.flag == 1){
				zeai.iframe('裁切【'+nkname+'】主头像','u_photo_cut.php?ifm=1&id='+uid+'&submitok=www___zeai__cn_inphotocut&tmpphoto='+rs.tmpphoto,720,720);
			}else{
				zeai.alert('上传图片出错，请联系原作者QQ：797311');
			}
		});
	}
}
checkboxMaxNum=88;
</script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
/*uidsobox*/
.uidsobox{margin:100px auto 0 auto;border:#eee 1px solid;width:700px;line-height:100px;background-color:#f8f8f8}
.uidsobox .input{height:30px;line-height:30px}
/*table*/
.table td{padding:8px;border:1px solid #eee}
.table .tdL{color:#999}
a.noUW200 img{max-width:200px;display:block;cursor:zoom-in;}
.sexRW li{width:45%}
.SW{width:150px}
.table .tdL{width:100px}
.table .tdR{min-width:250px}
.table .tbodyT{font-size:18px}
.rzboxx li{width:250px;max-height:100px;float:left;text-align:center}
.rzboxx li input{width:88%;margin-top:20px;padding:0 1px;color:#999}
.rzboxx li textarea{width:90%;float:right}
</style>
<body>
<?php if ($iframenav != 1){?>
<div class="navbox">
    <?php if ($ifmini != 1){?><a style="display:none" href="<?php echo SELF; ?>?uid=<?php echo $uid; ?>"<?php echo (empty($t))?' class="ed"':''; ?>>按会员ID号查询</a><?php }?>
    <a href="<?php echo SELF; ?>?submitok=mod&ifmini=<?php echo $ifmini;?>&t=1&uid=<?php echo $uid; ?>"<?php echo ($t == 1)?' class="ed"':''; ?>>基本信息/资料</a>
    <a href="<?php echo SELF; ?>?submitok=mod&ifmini=<?php echo $ifmini;?>&t=2&uid=<?php echo $uid; ?>"<?php echo ($t == 2)?' class="ed"':''; ?>>详细资料</a>
    <a href="<?php echo SELF; ?>?submitok=mod&ifmini=<?php echo $ifmini;?>&t=3&uid=<?php echo $uid; ?>"<?php echo ($t == 3)?' class="ed"':''; ?>>联系方法</a>
    <a href="<?php echo SELF; ?>?submitok=mod&ifmini=<?php echo $ifmini;?>&t=4&uid=<?php echo $uid; ?>"<?php echo ($t == 4)?' class="ed"':''; ?>>择偶要求</a>
    <a href="<?php echo SELF; ?>?submitok=photo&ifmini=<?php echo $ifmini;?>&t=5&uid=<?php echo $uid; ?>"<?php echo ($t == 5)?' class="ed"':''; ?>>个人相册</a>
    <a href="<?php echo SELF; ?>?submitok=rz&ifmini=<?php echo $ifmini;?>&t=6&uid=<?php echo $uid; ?>"<?php echo ($t == 6)?' class="ed"':''; ?>>认证资料</a>
<div class="clear"></div></div><div class="fixedblank"></div>
<?php }?>
<?php if (empty($submitok)){ ?>
<div class="uidsobox S16">
    <form action="<?php echo SELF; ?>" method="post">
    请输入会员ID号
    <input name="uid" type="text" class="size2 W150" id="uid" size="8" maxlength="9" value="<?php echo $uid; ?>"> 
    <input type="submit" name="Submit" value="提交" class="btn size2">
    <input name="submitok" type="hidden" value="mod" /></td>
    </form>
    </div>
<?php }else{?>

<form action="<?php echo SELF; ?>" method="post" id="supdesQQ797311_form" name="www_zeai.cn_FORM" onsubmit="return chkform();" enctype="multipart/form-data">
<table class="table0 W1200 Mtop30 Mbottom50 size2">
<tr>
<td width="220" align="right" valign="top" bgcolor="#ffffff"><table class="table0 " style="border:#eee 1px solid">
<tr>
<td align="center" style="padding:6px"><a href="javascript:;" class="noUW200 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo $photo_b_url; ?>');"><?php echo $photo_m_str; ?></a></td>
</tr>
<tr>
<td height="40" align="center" class="S14"><?php echo uicon($sex.$grade); ?> <a href="<?php echo $uhref; ?>"><?php echo $title; ?></a></td>
</tr>
<tr><td height="40" align="center" valign="top" class="S14 C999">UID：<?php echo $uid; ?></td></tr>



<tr>
  <td height="40" align="center" valign="top" class="S14 C999">资料完整度：<?php echo $myinfobfb; ?>%</td>
</tr>
<tr>
<td height="10" align="center" valign="bottom" class="S14 C999"><?php
if(ifint($admid)){
	$row = $db->ROW(__TBL_ADMIN__,"truename","id=".$admid);
	if ($row){
		$adm_truename=dataIO($row[0],'out');
		echo '录入/认领：'.dataIO($row[0],'out').'('.$admid.')';
	}
}
?></td>
</tr>
<tr>
<td height="10" align="center" valign="bottom" onClick="zeai.openurl('cert_hand.php?submitok=show&memberid=<?php echo $uid; ?>&ifmini=1')" title="手动强制认证" class="hand"><?php echo RZ_html($RZ,'s','all');?></td>
</tr>
<?php if(!empty($photo_s)){?>
<tr><td height="50" align="center" valign="bottom"><a href="javascript:cut(<?php echo $uid; ?>,'<?php echo $title; ?>','<?php echo $p; ?>');" class="aBAI">裁切形象照</a></td></tr>
<tr><td height="50" align="center" valign="bottom"><a href="#" class="aBAI" id="del_photo_s">删除形象照</a></td></tr>
<script>
del_photo_s.onclick=function(){
	zeai.confirm('确认删除形象照？',function(){zeai.post('u_mod_data'+zeai.extname,{submitok:'del_photo_s_update',uid:<?php echo $uid; ?>});	});
}
</script>
<?php }?>


<?php 
$row = $db->COUNT(__TBL_PHOTO__,"uid=".$uid);
if($row){
?>
<tr><td height="50" align="center" valign="bottom"><a href="photo.php?memberid=<?php echo $uid; ?>&ifmini=1" class="aBAI">从相册设置形象照</a></td></tr>
<?php }?>
<tr><td height="50" align="center" valign="bottom">
<a href="javascript:up_m(<?php echo $uid; ?>,'<?php echo $title; ?>');" class="aBAI">管理员上传形象照</a>
<input id="pic" type="file" style="display:none;" />
</td></tr>
<?php if ($session_kind == 'adm'){?>
<tr><td height="50" align="center" valign="bottom">
<a href="photo_m.php?uid=<?php echo $uid; ?>&submitok=updateendtime&p=<?php echo $p;?>&ifmini=1" title="置顶排名" class="aBAI"><img src="images/ding.gif" class="middle" style="margin-bottom:3px"><font style="margin-bottom:4px">排名置顶</font></a>
</td></tr>
<tr>
<td height="50" align="center" valign="bottom"><a href="cert_hand.php?submitok=show&memberid=<?php echo $uid; ?>&ifmini=1" class="aBAI">手动点亮图标认证</a></td>
</tr>

<tr>
<td height="50" align="center" valign="bottom"><a href="u_mod_pass.php?uid=<?php echo $uid; ?>&title=【<?php echo $title; ?>】" class="aBAI">修改登录密码</a></td>
</tr>
<?php }?>
<tr>
  <td height="50" align="center" valign="bottom" style="border-bottom:#dedede 1px solid">
  
  

  <table class="table0 W80_ Mtop30">
    <tr><td height="30">人气：<?php echo $click; ?></td></tr>
    <tr><td height="30">注册：<?php echo $regtime; ?></td></tr>
    <tr><td height="30">注册IP：<?php echo $regip; ?></td></tr>
    <tr><td height="30">最近：<?php echo $endtime; ?></td></tr>
    <tr><td height="30">最近IP：<?php echo $endip; ?></td></tr>
</table>  

<?php if (!empty($longitude)){ ?>
<table class="table0 W80_ ">
    <tr>
      <td height="30">经度：<?php echo $longitude; ?></td>
      <td width="50" rowspan="2" align="left" valign="middle">
      <a href="http://map.qq.com/?type=marker&isopeninfowin=1&markertype=1&pointx=<?php echo $longitude; ?>&pointy=<?php echo $latitude; ?>&name=当前位置&addr=当前位置&ref=myapp" target="_blank"><img src="images/gps.gif" style="display:inline"></a>
      </td>
    </tr>
    <tr>
      <td height="30">纬度：<?php echo $latitude; ?></td>
      </tr>
  </table>
<?php }?><br><br>
  
  </td>
</tr>

</table>

</td>
<td align="left" valign="top" style="padding:0">
<?php if ($submitok == 'photo'){ ?>
<!--个人相册-->
<style>
.picli{padding:20px}
.picli li{width:100px;height:100px;line-height:100px;border:#eee 1px solid;box-sizing:border-box;cursor:pointer;float:left;margin:0 59px 50px 0;text-align:center;position:relative}
.picli li:nth-child(6n){margin-right:0}
.picli li.add,.picli li i{background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.picli li.add{background-size:150px 100px;background-repeat:no-repeat;background-position:-2px -2px;border:#dedede 2px dashed}
.picli li:hover{background-color:#f5f7f9}
.picli li img{vertical-align:middle;margin-top:-5px;max-width:98px;max-height:98px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;cursor:zoom-in}
.picli li:hover .img{cursor:zoom-in}
.picli li i{width:20px;height:20px;top:-10px;right:-10px;position:absolute;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3)}
.picli li i:hover{background-position:-100px top;cursor:pointer}
.picli #picmore{display:none}
</style>
<table class="table W98_">
    <tr><td height="20" class="tbodyT"><center>个人相册</center></td></tr>
    <tr>
    <td height="300" valign="top">
    <div class="picli">
    	<li class="add" id="add"><input id="picmore" type="file" accept="image/gif,image/jpeg,image/png" multiple /></li>
		<?php 
        $rt=$db->query("SELECT id,path_s FROM ".__TBL_PHOTO__." WHERE uid=".$uid." ORDER BY id DESC");
        $total = $db->num_rows($rt);
        if ($total>0) {
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt);
                if(!$rows) break;
				$id = $rows[0];$path_s = $rows[1];
                $dst_s  = $_ZEAI['up2'].'/'.$path_s;
                $dst_b  = getpath_smb($dst_s,'b');
                ?>
                <li class="li"><img id="<?php echo $id;?>" src="<?php echo $dst_s;?>"><i></i></li>
            <?php
            }
            ?>
            <script>
                zeai.listEach('.li',function(obj){
                    var img = obj.children[0];
                    var i   = obj.children[1];
                    img.onclick = function(){parent.piczoom(img.src.replace('_s','_b'));}
					i.onclick = function(){
						zeai.confirm('亲~~确认删除么？',function(){
							obj.parentNode.removeChild(obj);
							zeai.ajax('u_mod_data'+zeai.ajxext+'submitok=ajax_photo_del&uid='+uid+'&id='+img.id);
						});
					}
                });
            </script>
        <?php }?>
        <script>
		add.onclick=function(){
			var picmore = o('picmore');
			picmore.click();
			picmore.onchange = function(){
				var FILES   = this.files;
				var Flength = FILES.length;
				if (Flength>10){
					zeai.alert('一次最多只能10张，否则会卡~~');
				}else{
					var filename,ftype;
					for(i=0;i<Flength;i++) {
						if (FILES[i]['size'] > upMaxMB*1024000){zeai.alert('图片【'+FILES[i]['name']+'】太大，已超过'+upMaxMB+'M，请重新选择');picmore.value='';return false;}
						filename = FILES[i]['name'].toLowerCase();ftype = filename.substring(filename.lastIndexOf("."),filename.length);
						if ((ftype != '.jpg')&&(ftype != '.gif')&&(ftype != '.png')){picmore.value='';zeai.alert('只能上传jpg/gif/png格式图片,请重新选择!');return false;}
					}
					var j=0;
					function photo_up(){
						parent.zeai.msg('<img src="images/loadingData.gif" class="picmiddle">正在上传第 '+(j+1)+' 张 -->'+FILES[j]['name'],{animation:'off',time:30})
						var postjson = {"submitok":"ajax_photo_up","file":FILES[j],"uid":uid};
						zeai.ajax({url:'u_mod_data'+zeai.ajxext,data:postjson},function(e){var rs=zeai.jsoneval(e);
							if (rs.flag == 1){
								j++;
								if (j < Flength){
									parent.zeai.msg(0);
									setTimeout(photo_up,300);
								}else{picmore.value='';parent.zeai.msg(0);parent.zeai.msg('全部上传成功，正在刷新数据...');location.reload(true);}
							}else{
								zeai.alert('【'+FILES[j]['name']+'】上传出错，请联系原作者QQ：797311');
							}
						});
					}
					photo_up();
				}
			}
		}
        </script>
    </div>
    </td>
    </tr>
</table>
<!--个人相册 结束-->
<?php }else{ ?>
<!--基本资料，详细资料，择偶要求-->

    <table class="table W98_ size2">
    <tr><td height="20" colspan="4" class="tbodyT"><center><?php echo $mini_title; ?></center></td></tr>
    <!-- 基本资料 -->
    <?php if ($t == 1){ ?>
	<script>function chkform(){}</script>
    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>>
        <td class="tdL">是否管理员</td>
        <td colspan="3" class="tdR">
        <input type="checkbox" name="ifadm" id="ifadm" class="switch" value="1"<?php echo ($ifadm == 1)?' checked':'';?>><label for="ifadm" class="switch-label"><i></i><b>是</b><b>否</b></label>　<span class="tips2 S12">选“是”手机端会员主页将有管理功能【生成相亲卡】【隐藏】【封号】【置顶】【置底】新会员注册审核微信通知提醒功能(需关注公众号)</span>      
        </td>
    </tr>
 
     <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>>
        <td class="tdL">公众号关注</td>
        <td colspan="3" class="tdR">
        <input type="checkbox" name="subscribe" id="subscribe" class="switch" value="1"<?php echo ($subscribe == 1)?' checked':'';?>><label for="subscribe" class="switch-label"><i></i><b>已关注</b><b>未关注</b></label>
		<span class="tips S12">如果已关注或公众号老粉丝可以设置为<font style="color:#5EB87B">已关注</font></span>
		</td>
    </tr>
     
      

    <tr>
      <td class="tdL">会员类型</td>
      <td colspan="3" class="tdR" style="line-height:200%">
      
<input type="radio" name="kind" value="1" id="kind_1" class="radioskin"<?php echo ($kind == 1)?' checked':'';?>><label for="kind_1" class="radioskin-label"><i class="i1"></i><b class="W120">线上会员</b></label><span class="tips S12">会员自主联系互动，和会员自己注册效果一样</span><br>
<input type="radio" name="kind" value="2" id="kind_2" class="radioskin"<?php echo ($kind == 2)?' checked':'';?>><label for="kind_2" class="radioskin-label"><i class="i1"></i><b class="W120">线下会员</b></label><span class="tips S12">内部会员，网站前台只展示，别的会员联系必须通过红娘，不可登录互动，后台/CRM人工管理服务</span><br>
<input type="radio" name="kind" value="3" id="kind_3" class="radioskin"<?php echo ($kind == 3)?' checked':'';?>><label for="kind_3" class="radioskin-label"><i class="i1"></i><b class="W120">均可(线上+线下)</b></label><span class="tips S12">都可以，如果线上会员申请红娘委托之后，将自动变为这种类型，后台/CRM人工管理服务</span><br>

<input type="radio" name="kind" value="4" id="kind_4" class="radioskin"<?php echo ($kind == 4)?' checked':'';?>><label for="kind_4" class="radioskin-label"><i class="i1"></i><b class="W120">机器人</b></label><span class="tips S12">虚拟人，用来自动发私信和打招呼给新注会员的，也就是虚拟会员</span>
      
      </td>
      
    <tr>
    <td class="tdL">会员状态</td>
    <td colspan="3" class="tdR">
        <input type="radio" name="flag" value="1" id="flag1" class="radioskin"<?php echo ($flag == 1)?' checked':'';?>><label for="flag1" class="radioskin-label"><i class="i1"></i><b class="W50">正常</b></label>
        <input type="radio" name="flag" value="-2" id="flag_2" class="radioskin"<?php echo ($flag == -2)?' checked':'';?>><label for="flag_2" class="radioskin-label"><i class="i1"></i><b class="W50">隐藏</b></label>
        <input type="radio" name="flag" value="-1" id="flag_1" class="radioskin"<?php echo ($flag == -1)?' checked':'';?>><label for="flag_1" class="radioskin-label"><i class="i1"></i><b class="W50">锁定</b></label>
        <input type="radio" name="flag" value="0" id="flag0" class="radioskin"<?php echo ($flag == 0)?' checked':'';?>><label for="flag0" class="radioskin-label"><i class="i1"></i><b class="W50">未审</b></label>
        <input type="radio" name="flag" value="2" id="flag2" class="radioskin"<?php echo ($flag == 2)?' checked':'';?>><label for="flag2" class="radioskin-label"><i class="i1"></i><b class="W100">注册未完成</b></label>
        <span class="FR" style="display:inline-block;margin-right:20px">头像：<input type="checkbox" name="photo_ifshow" id="photo_ifshow" class="switch" value="1"<?php echo ($photo_ifshow == 1)?' checked':'';?>><label for="photo_ifshow" class="switch-label"><i></i><b>显示</b><b>隐藏</b></label></span>
        <span class="FR" style="display:inline-block;margin-right:20px">手机个人主页相亲卡海报推广：<input type="checkbox" name="xqk_ifshow" id="xqk_ifshow" class="switch" value="1"<?php echo ($xqk_ifshow == 1)?' checked':'';?>><label for="xqk_ifshow" class="switch-label"><i></i><b>显示</b><b>隐藏</b></label></span>
    </td>
    </tr>
    <tr>
    <td class="tdL">征婚对象</td>
    <td colspan="3" class="tdR">
        <input type="radio" name="parent" value="1" id="parent1" class="radioskin"<?php echo ($parent == 1)?' checked':'';?>><label for="parent1" class="radioskin-label"><i class="i1"></i><b class="W100">本人征婚</b></label>
        <input type="radio" name="parent" value="2" id="parent2" class="radioskin"<?php echo ($parent == 2)?' checked':'';?>><label for="parent2" class="radioskin-label"><i class="i1"></i><b class="W100">父母帮征婚</b></label>
        <input type="radio" name="parent" value="3" id="parent3" class="radioskin"<?php echo ($parent == 3)?' checked':'';?>><label for="parent3" class="radioskin-label"><i class="i1"></i><b class="W100">亲友帮征婚</b></label>
    </td>
    </tr>

    <tr>
      <td class="tdL">登录用户名</td>
      <td class="tdR"><input name="uname" type="text" class="W150" id="uname" value="<?php echo $uname;?>" maxlength="100" /></td>
      <td class="tdL">网名昵称</td>
      <td class="tdR"><input name="nickname" type="text" class="W150" id="nickname" value="<?php echo $nickname; ?>" maxlength="20" required></td>
      </tr>
    <tr>
    <td class="tdL">姓　　名</td>
    <td class="tdR"><input name="truename" type="text" class="W150" id="truename" value="<?php echo $truename;?>" maxlength="12" /></td>
    <td class="tdL">身份证号</td>
    <td class="tdR"><input name="identitynum" type="text" class="W300" id="identitynum" value="<?php echo $identitynum; ?>" maxlength="18" pattern="^([0-9]){7,18}(x|X)?$"></td>
    </tr>

      <tr>
        <td class="tdL">性　　别</td>
        <td class="tdR"><script>zeai_cn__CreateFormItem('radio','sex','<?php echo $sex; ?>',' class="RCW sexRW"');</script></td>
        <td class="tdL">出生年月</td>
        <td class="tdR"><input name="birthday" id="birthday" type="text" readonly class="W100 hand" value="<?php echo $birthday; ?>" size="10" maxlength="10"></td>
      </tr>
    <tr>
    <td class="tdL">身　　高</td>
    <td class="tdR"><script>zeai_cn__CreateFormItem('select','heigh','<?php echo $heigh; ?>');</script></td>
    <td class="tdL">体　　重</td>
    <td class="tdR"><script>zeai_cn__CreateFormItem('select','weigh','<?php echo $weigh; ?>');</script></td>
    </tr>
    <tr>
      <td class="tdL">职　　业</td>
      <td class="tdR"><script>zeai_cn__CreateFormItem('select','job','<?php echo $job; ?>');</script></td>
      <td class="tdL">学　　历</td>
      <td class="tdR"><script>zeai_cn__CreateFormItem('radio','edu','<?php echo $edu; ?>');</script></td>
    </tr>
      
    <tr>
      <td class="tdL">工作地区</td>
      <td colspan="3" class="tdR">
        <script>LevelMenu4('a1|a2|a3|a4|请选择|<?php echo $a1; ?>|<?php echo $a2; ?>|<?php echo $a3; ?>|<?php echo $a4; ?>|areaid|areatitle|<?php echo $areatitle;?>');</script>
        </td>
    </tr>
    <tr>
      <td class="tdL">户籍地区</td>
      <td colspan="3" class="tdR">
        <script>LevelMenu4('a11|a22|a33|a44|请选择|<?php echo $a11; ?>|<?php echo $a22; ?>|<?php echo $a33; ?>|<?php echo $a44; ?>|area2id|area2title|<?php echo $area2title;?>','','hj');</script>
        </td>
    </tr>
    
    
    <tr>
    <td class="tdL">婚姻状况</td>
    <td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('radio','love','<?php echo $love; ?>');</script></td>
    </tr>
    <tr>
    <td class="tdL">子女情况</td>
    <td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('radio','child','<?php echo $child; ?>');</script></td>
    </tr>
    <tr>
      <td class="tdL">期望结婚时间</td>
      <td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('radio','marrytime','<?php echo $marrytime; ?>');</script></td>
    </tr>
    <tr>
    <td class="tdL">月 收 入</td>
    <td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('radio','pay','<?php echo $pay; ?>');</script></td>
    </tr>
    <tr>
    <td class="tdL">住房情况</td>
    <td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('radio','house','<?php echo $house; ?>');</script></td>
    </tr>
    <tr>
    <td class="tdL">买车情况</td>
    <td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('radio','car','<?php echo $car; ?>');</script></td>
    </tr>
    <tr>
    <td class="tdL">自我介绍</td>
    <td colspan="3" class="tdR"><textarea name="aboutus" rows="5" class="W98_" id="aboutus" placeholder="填写个人独白(20~1000字节)"><?php echo $aboutus; ?></textarea></td>
    </tr>
    <tr>
    <td class="tdL">血　　型</td>
    <td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('radio','blood','<?php echo $blood; ?>');</script></td>
    </tr>
    <tr>
    <td class="tdL">我的标签</td>
    <td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('checkbox','tag','<?php echo $tag; ?>','',eval('tag<?php echo $sex; ?>_ARR'));</script></td>
    </tr>
    <tr>
    <td class="tdL">民　　族</td>
    <td class="tdR"><script>zeai_cn__CreateFormItem('select','nation','<?php echo $nation; ?>');</script></td>
    <td class="tdL">嫁娶形式</td>
    <td class="tdR"><script>zeai_cn__CreateFormItem('radio','marrytype','<?php echo $marrytype; ?>');</script></td>
    </tr>
<!--    <tr>
      <td height="80" colspan="4" align="center"><input class="btn size3" type="submit" value="保存并修改" /></td>
    </tr>
-->    
    
    
    
    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>>
      <td colspan="4" class="tbodyT"><span class="tips">更多线下会员服务功能请移步【线下CRM管理】</span></td>
    </tr>
<!---->
<?php
if($crm_agentid=='无'){
	$agentid=0;
}else{
	$crm_agentid = intval($crm_agentid);
	$agentid     = ($crm_agentid>0)?$crm_agentid:$agentid;
}
?>
    <tr<?php echo (!in_array('crm',$QXARR))?' style="display:none"':'';?>>
        <td class="tdL">所属门店</td>
        <td colspan="3" class="tdR">
            <?php
			$rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
			$total2 = $db->num_rows($rt2);
			?>
			<select name="agentid" class="W300 size2"  onChange="zeai.openurl('<?php echo SELF;?>?submitok=mod&ifmini=<?php echo $ifmini;?>&t=1&uid=<?php echo $uid;?>&crm_agentid='+this.value+'&ifbottom=1')">
			<option value="无">请选择门店</option>
			<?php
				for($j=0;$j<$total2;$j++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$clss=($agentid==$rows2[0])?' selected':'';
					?>
					<option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[1],'out');?></option>
					<?php
				}
			?>
			</select><span class="tips">此为硬改会员主表，不触发其他任何关联</span>
        </td>
    </tr>
    <tr<?php echo (!in_array('crm',$QXARR))?' style="display:none"':'';?>>
        <td class="tdL">录入/认领</td>
        <td colspan="3" class="tdR">
            <?php
			$rt2=$db->query("SELECT id,username,truename FROM ".__TBL_CRM_HN__." WHERE flag=1 AND agentid=$agentid ORDER BY px DESC,id DESC");
			$total2 = $db->num_rows($rt2);
			?>
			<select name="admid" class="W300 size2">
			<option value="">请选择录入/认领人</option>
			<?php
				for($j=0;$j<$total2;$j++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$clss=($admid==$rows2[0])?' selected':'';
					?>
					<option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[2],'out').'　｜　'.dataIO($rows2[1],'out').'　｜　ID:'.dataIO($rows2[0],'out');?></option>
					<?php
				}
			?>
			</select><span class="tips">此为硬改会员主表，不触发其他任何关联</span>
        </td>
    </tr>
    <tr<?php echo (!in_array('crm',$QXARR))?' style="display:none"':'';?>>
        <td class="tdL">售前</td>
        <td colspan="3" class="tdR">
            <?php
			$rt2=$db->query("SELECT id,username,truename  FROM ".__TBL_CRM_HN__." WHERE flag=1 AND FIND_IN_SET('sq',crmkind) AND agentid=$agentid ORDER BY px DESC,id DESC");
			$total2 = $db->num_rows($rt2);
			?>
			<select name="hnid" class="W300 size2">
			<option value="">请选择售前</option>
			<?php
				for($j=0;$j<$total2;$j++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$clss=($hnid==$rows2[0])?' selected':'';
					?>
					<option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[2],'out').'　｜　'.dataIO($rows2[1],'out').'　｜　ID:'.dataIO($rows2[0],'out');?></option>
					<?php
				}
			?>
			</select><span class="tips">此为硬改会员主表，不触发其他任何关联</span>
        </td>
    </tr>
    <tr<?php echo (!in_array('crm',$QXARR))?' style="display:none"':'';?>>
        <td class="tdL">售后</td>
        <td colspan="3" class="tdR">
            <?php
			
			$rt2=$db->query("SELECT id,username,truename  FROM ".__TBL_CRM_HN__." WHERE flag=1 AND FIND_IN_SET('sh',crmkind) AND agentid=$agentid ORDER BY px DESC,id DESC");
			$total2 = $db->num_rows($rt2);
			?>
			<select name="hnid2" class="W300 size2">
			<option value="">请选择售后</option>
			<?php
				for($j=0;$j<$total2;$j++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$clss=($hnid2==$rows2[0])?' selected':'';
					?>
					<option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[2],'out').'　｜　'.dataIO($rows2[1],'out').'　｜　ID:'.dataIO($rows2[0],'out');?></option>
					<?php
				}
			?>
			</select><span class="tips">此为硬改会员主表，不触发其他任何关联</span>
        </td>
    </tr>
    <?php if (ifint($crm_agentid)){?><script>setTimeout(function(){zeai.setScrollTop(9999);},500);</script><?php }?>
<!---->

    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>><td colspan="4" class="tbodyT">线上登录密钥/密码：</td></tr>
    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>><td class="tdL">登录密码</td><td colspan="3" class="tdR"><input name="pwd" type="text" class="W300" id="pwd"/><span class="tips">不修改或没有请留空</span></td></tr>
    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>>
      <td class="tdL">openid</td>
      <td colspan="3" class="tdR"><input name="openid" type="text" class="W300" id="openid" value="<?php echo $openid;?>" maxlength="100" /><span class="tips">微信登录用的，有值就已经绑定成功了，勿动</span></td>
      </tr>
    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>>
      <td class="tdL">unionid</td>
      <td colspan="3" class="tdR"><input name="unionid" type="text" class="W300" id="unionid" value="<?php echo $unionid;?>" maxlength="100" /><span class="tips">微信多端通行证登录用的，有值就已经绑定成功了，勿动</span></td>
      </tr>
    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>>
      <td class="tdL">loginkey</td>
      <td colspan="3" class="tdR"><input name="loginkey" type="text" class="W300" id="unionid" value="<?php echo $loginkey;?>" maxlength="100" /><span class="tips">QQ登录用的，有值就已经绑定成功了，勿动</span></td>
      </tr>
    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>>
    <td colspan="4" class="tbodyT">推广分销信息：</td>
    </tr>
    
    
    <tr style="display:none">
    <td class="tdL">推广海报</td>
    <td colspan="3" class="tdR">
    
    <div class="picli60">
        <li class="add" id="add"<?php echo (!empty($tgpic))?' style="display:none"':' style="display:block"'?>><input id="tgpic" type="file" style="display:none" accept="image/gif,image/jpeg,image/png" multiple /></li>
        <li id="tgpicshow"<?php echo (!empty($tgpic))?' style="display:block"':' style="display:none"'?>><img src="<?php echo (!empty($tgpic))?$_ZEAI['up2'].'/'.$tgpic:'';?>"><i></i></li>
    </div>
	<script>
		tgpicshow.children[0].onclick = function(){parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$tgpic;?>');}
		tgpicshow.children[1].onclick = function(){
			zeai.confirm('亲~~确认删除么？',function(){
				tgpicshow.hide();add.show();tgpicshow.children[0].src = '';
				zeai.ajax('u_mod_data'+zeai.ajxext+'submitok=ajax_tgpic_del&uid='+uid);
			});
		}
		if (!zeai.empty(o('add')))add.onclick=function(){
			zeai.up({"url":"u_mod_data"+zeai.extname,"upMaxMB":upMaxMB,"submitok":"ajax_tgpic_up","uid":uid,"fn":function(e){var rs=zeai.jsoneval(e);	
				if (rs.flag == 1){
					tgpic.value='';zeai.msg(0);zeai.msg('上传成功！');
					var img = tgpicshow.children[0];img.src=rs.tmpphoto;
					img.onclick = function(){parent.piczoom(rs.tmpphoto);}
					tgpicshow.show();add.hide();
				}else{zeai.alert('上传出错，请联系扬州交友网开发者客服QQ7144100');}
			}});
		}
    </script>

    </td>
    </tr>
    
    
    <tr<?php echo ($session_kind == 'crm')?' style="display:none"':'';?>>
    <td class="tdL">推荐人ID</td>
    <td colspan="3" class="tdR"><input name="tguid" type="text" class="W150" id="tguid" value="<?php echo $tguid; ?>">
<?php if (!empty($tguid)){
	$tgrow = $db->ROW(__TBL_TG_USER__,"nickname,title,kind","id=".$tguid,"name");
	if ($tgrow){
		$nickname_tg = dataIO($tgrow['nickname'],'out');
		$title_tg    = $tgrow['title'];
		$kind_tg     = $tgrow['kind'];
		if($kind_tg==2 || $kind_tg == 3){
			$nickname_tg=$title_tg;
		}
		echo $nickname_tg.'（ID:'.$tguid.'）';
	}
}
?>
    </td>
    </tr>


    <input name="submitok" type="hidden" value="modupdate" />
    <input name="uid" type="hidden" value="<?php echo $uid; ?>" />
    <input name="username_old" type="hidden" value="<?php echo $uname; ?>" />
    <input name="openid_old" type="hidden" value="<?php echo $openid; ?>" />
    <input name="unionid_old" type="hidden" value="<?php echo $unionid; ?>" />
    <input name="loginkey_old" type="hidden" value="<?php echo $loginkey; ?>" />
    <input name="t" type="hidden" value="<?php echo $t; ?>" />
    <input name="ifmini" type="hidden" value="<?php echo $ifmini; ?>" />
    <input name="iframenav" type="hidden" value="<?php echo $iframenav; ?>" />
    
    <!-- 详细资料 -->
    <?php }elseif ($t == 2){ ?>
		<script>
        function chkform(){}
        </script>
        <?php
		if (@count($extifshow) >= 1 && is_array($extifshow)){
			foreach ($extifshow as $V) {
				switch ($V['s']) {
					case 1:$Fkind = 'text';break;
					case 2:$Fkind = 'select';break;
					case 3:$Fkind = 'checkbox';break;
					case 4:$Fkind = 'range';break;
				}
				$F = $V['f'];
				?>
				<tr><td class="tdL"><?php echo $V['t'];?></td><td colspan="3" class="tdR"><script>zeai_cn__CreateFormItem('<?php echo $Fkind;?>','<?php echo $F;?>','<?php echo dataIO($row2[$F],'out'); ?>');</script></td></tr>
			<?php
			}
		}
		?>
        <tr><td colspan="4" class="center">
        <input name="submitok" type="hidden" value="modupdate" />
        <input name="uid" type="hidden" value="<?php echo $uid; ?>" />
        <!--<input class="btn size3" type="submit" value="保存并修改" accesskey="s" />-->
        <input name="t" type="hidden" value="<?php echo $t; ?>" />
    <input name="ifmini" type="hidden" value="<?php echo $ifmini; ?>" />
    <input name="iframenav" type="hidden" value="<?php echo $iframenav; ?>" />
        </td></tr>
    <!--联系方法-->
    <?php }elseif ($t == 3){?>
<script>function chkform(){//QQ:7144100
}
</script>
    <tr>
      <td class="tdL">手　　机</td>
      <td colspan="3" class="tdR">
      <input name="mob" type="text" class="W300" id="mob" value="<?php echo $mob; ?>" maxlength="11">
      <input type="checkbox" name="mob_ifshow" id="mob_ifshow" class="switch" value="1"<?php echo ($data_mob_ifshow == 1)?' checked':'';?>><label for="mob_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label>
      </td>
      </tr>
    <tr>
      <td class="tdL">微 信 号</td>
      <td colspan="3" class="tdR">
        <input name="weixin" type="text" class="W300" id="weixin" value="<?php echo $weixin; ?>">
        <input type="checkbox" name="weixin_ifshow" id="weixin_ifshow" class="switch" value="1"<?php echo ($data_weixin_ifshow == 1)?' checked':'';?>><label for="weixin_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label>
      </td>
      </tr>
    <tr>
      <td class="tdL">微信二维码</td>
      <td colspan="3" class="tdR">
      
    <div class="picli60">
        <li class="add" id="add"<?php echo (!empty($weixin_pic))?' style="display:none"':' style="display:block"'?>></li>
        <li id="weixin_picshow"<?php echo (!empty($weixin_pic))?' style="display:block"':' style="display:none"'?>><img src="<?php echo (!empty($weixin_pic))?$_ZEAI['up2'].'/'.$weixin_pic:'';?>"><i></i></li>
    </div>
	<input type="checkbox" name="weixin_pic_ifshow" id="weixin_pic_ifshow" class="switch" value="1"<?php echo ($data_weixin_pic_ifshow == 1)?' checked':'';?>><label for="weixin_pic_ifshow" class="switch-label" style="margin-top:17px;margin-left:243px"><i></i><b>公开</b><b>保密</b></label>
      </td>
      </tr>
    <tr>
      <td class="tdL">QQ</td>
      <td colspan="3" class="tdR">
      <input name="qq" type="text" class="W300" id="qq" value="<?php echo $qq; ?>">
        <input type="checkbox" name="qq_ifshow" id="qq_ifshow" class="switch" value="1"<?php echo ($data_qq_ifshow == 1)?' checked':'';?>><label for="qq_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label>
      </td>
      </tr>
    <tr>
      <td class="tdL">邮　　箱</td>
      <td colspan="3" class="tdR">
        <input name="email" type="text" class="W300" id="email" value="<?php echo $email; ?>">
        <input type="checkbox" name="email_ifshow" id="email_ifshow" class="switch" value="1"<?php echo ($data_email_ifshow== 1)?' checked':'';?>><label for="email_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label>
      </td>
      </tr>
    <tr>
    <td class="tdL">地　　址</td>
    <td colspan="3" class="tdR"><input name="address" type="text" class="W300" id="address" value="<?php echo $address; ?>" maxlength="50"></td>
    </tr>

         <input name="submitok" type="hidden" value="modupdate" />
        <input name="uid" type="hidden" value="<?php echo $uid; ?>" />
        <input name="mob_old" type="hidden" value="<?php echo $mob; ?>" />
        <input name="email_old" type="hidden" value="<?php echo $email; ?>" />
        <input name="t" type="hidden" value="<?php echo $t; ?>" />
    <input name="ifmini" type="hidden" value="<?php echo $ifmini; ?>" />
    <input name="iframenav" type="hidden" value="<?php echo $iframenav; ?>" />
	<script>
		weixin_picshow.children[0].onclick = function(){parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$weixin_pic;?>');}
		weixin_picshow.children[1].onclick = function(){
			zeai.confirm('亲~~确认删除么？',function(){
				weixin_picshow.hide();add.show();weixin_picshow.children[0].src = '';
				zeai.ajax('u_mod_data'+zeai.ajxext+'submitok=ajax_weixin_pic_del&uid='+uid);
			});
		}
		if (!zeai.empty(o('add')))add.onclick=function(){
			zeai.up({"url":"u_mod_data"+zeai.extname,"upMaxMB":upMaxMB,"submitok":"ajax_weixin_pic_up","uid":uid,"fn":function(e){var rs=zeai.jsoneval(e);	
				if (rs.flag == 1){
					//tgpic.value='';zeai.msg(0);
					zeai.msg('上传成功！');
					var img = weixin_picshow.children[0];img.src=rs.tmpphoto;
					img.onclick = function(){parent.piczoom(rs.tmpphoto);}
					weixin_picshow.show();add.hide();
				}else{zeai.alert('上传出错，请联系扬州交友网开发者客服QQ7144100');}
			}});
		}
		zeai.listEach('.switch',function(obj){
			var objname = obj.name;
			obj.onclick = function(){
				var v=(obj.checked)?1:0
				zeai.ajax({url:'u_mod_data'+zeai.ajxext+'submitok=ajax_set&objname='+objname+'&v='+v+'&uid=<?php echo $uid; ?>'},function(e){var rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
				});
			}
		});
		
    </script>
    
    
    
    
    <!-- 择偶要求 -->
	<?php }elseif ($t == 4){
		$mate_diy = explode(',',$_ZEAI['mate_diy']);
		?>
		<script>
		function chkform(){
			<?php if (in_array('age',$mate_diy)){?>
			if (mate_age1.value > mate_age2.value && (!zeai.empty(mate_age1.value) && !zeai.empty(mate_age2.value)) ){
				zeai.msg('年龄请选择一个正确的区间（左小右大）',mate_age1);	
				return false;
			}
			<?php }?>
			<?php if (in_array('heigh',$mate_diy)){?>
			if (mate_heigh1.value > mate_heigh2.value && (!zeai.empty(mate_heigh1.value) && !zeai.empty(mate_heigh2.value)) ){
				zeai.msg('身高请选择一个正确的区间（左小右大）',mate_heigh1);	
				return false;
			}
			<?php }?>
			<?php if (in_array('weigh',$mate_diy)){?>
			if (mate_weigh1.value > mate_weigh2.value && (!zeai.empty(mate_weigh1.value) && !zeai.empty(mate_weigh2.value)) ){
				zeai.msg('体重请选择一个正确的区间（左小右大）',mate_weigh1);	
				return false;
			}
			<?php }?>
		}</script>
        <?php
        if (count($mate_diy) >= 1 && is_array($mate_diy)){
            foreach ($mate_diy as $k=>$V) {
                $cook_tmp1 = 'mate_'.$V;
                //$cook_tmp2 = 'mate_'.$V.'_str';
                $cook_mate_data = $$cook_tmp1;
               // $cook_mate_str  = $$cook_tmp2;
                $ext = mate_diy_par($V,'ext');
				?>
				<tr><td class="tdL"><?php echo mate_diy_par($V);?></td><td colspan="3" class="tdR S16">
				<?php 
                switch ($ext) {
                    case 'radio':?><script>zeai_cn__CreateFormItem('select','<?php echo $cook_tmp1;?>','<?php echo $cook_mate_data; ?>','',<?php echo $cook_tmp1.'_ARR';?>);</script><?php ;break;
                    case 'checkbox':?><script>zeai_cn__CreateFormItem('checkbox','<?php echo $cook_tmp1;?>','<?php echo $cook_mate_data; ?>','',<?php echo $cook_tmp1.'_ARR';?>);</script><?php break;
                    case 'radiorange':
						$cook_tmp1_1 = 'mate_'.$V.'1';
						$cook_mate_data_1 = $$cook_tmp1_1;
						$cook_tmp1_2 = 'mate_'.$V.'2';
						$cook_mate_data_2 = $$cook_tmp1_2;
						?>
						<script>zeai_cn__CreateFormItem('select','<?php echo $cook_tmp1_1;?>','<?php echo $cook_mate_data_1; ?>','',<?php echo $cook_tmp1.'_ARR';?>);</script>
                         ～ 
						<script>zeai_cn__CreateFormItem('select','<?php echo $cook_tmp1_2;?>','<?php echo $cook_mate_data_2; ?>','',<?php echo $cook_tmp1.'_ARR';?>);</script>
						<?php break;
                    case 'area':
						if($V=='areaid2'){
							$idlist='h1|h2|h3|h4';	
							$deflist= $h1.'|'.$h2.'|'.$h3.'|'.$h4;
							$iputhienT = 'mate_areatitle2';
							$hj='hj';
						}else{
							$idlist='m1|m2|m3|m4';
							$deflist= $m1.'|'.$m2.'|'.$m3.'|'.$m4;
							$iputhienT = 'mate_areatitle';
							$hj='';
						}
						
						?><script>LevelMenu4('<?php echo $idlist;?>|'+nulltext+'|<?php echo $deflist;?>|<?php echo $cook_tmp1;?>|<?php echo $iputhienT;?>|<?php echo $$iputhienT;?>','','<?php echo $hj;?>');</script><?php break;
                }
				?>
				</td></tr>
        <?php }}?>
				<tr><td class="tdL">其他要求</td><td colspan="3" class="tdR S16"><textarea name="mate_other" rows="5" class="W98_" id="aboutus" placeholder="填写其他要求(500字节以内)"><?php echo $mate_other; ?></textarea></td></tr>
        
    <!--认证资料-->
    <?php
	}elseif ($t == 6){
		if (count($rz_dataARR) >= 1 && is_array($rz_dataARR)){foreach ($rz_dataARR as $k=>$V) {?>
		<tr><td class="tdL"><?php echo rz_data_info($V,'title');?></td><td colspan="3" class="tdR ">
        <div class="rzboxx">
            <?php $rzdata = RZ_get_tableinfo($uid,$V);if(is_array($rzdata)){$p1 = $rzdata['p1'];$p2 = $rzdata['p2'];$bz = dataIO($rzdata['bz'],'out');}else{$p1 = '';$p2 = '';$bz = '';}?>
            <li>
				<?php if (!empty($p1)) {?>
                    <a class="pic60"><img  src="<?php echo $_ZEAI['up2']."/".$p1; ?>" class="zoom" align="absmiddle" alt="点击放大显示" title="点击放大显示" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".smb($p1,'b'); ?>')"></a>
                    <br>
                    <a href="javascript:;" onClick="rzpicDel('<?php echo $V;?>_1')" class="btn size1" >删除</a>
                <?php }else{ 
                    echo "<input name='pic_".$V."1' type='file' class='input size2' accept='image/gif,image/jpeg,image/png' />";
                }?>        
            </li>
            <li>
				<?php if (!empty($p2)) {?>
                    <a class="pic60"><img  src="<?php echo $_ZEAI['up2']."/".$p2; ?>" class="zoom " align="absmiddle"  title="点击放大显示" alt="点击放大显示" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".smb($p2,'b'); ?>')"></a>
                    <br>
                    <a href="javascript:;" onClick="rzpicDel('<?php echo $V;?>_2')" class="btn size1" >删除</a>
                <?php }else{ 
                    echo "<input name='pic_".$V."2' type='file' class='input size2'accept='image/gif,image/jpeg,image/png' />";
                }?>        
            </li>
            <li style="width:300px"><textarea name="bz_<?php echo $V; ?>" rows="3" class="W98_" placeholder="备注信息(500字节以内)"><?php echo $bz; ?></textarea><input name="oldbz_<?php echo $V; ?>" type="hidden" value="<?php echo $bz; ?>" /></li>
		</div>
        </td></tr>
	<?php }}}?>
    </table>
    <script>
	function rzpicDel(picid){
		zeai.confirm('确认要删除么？',function(){
			zeai.ajax({url:'u_mod_data'+zeai.extname,data:{submitok:'rzpicDel',picid:picid,uid:<?php echo $uid;?>,t:<?php echo $t;?>}},function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){zeai.msg(rs.msg);setTimeout(function(){location.reload(true);},1000);}else{zeai.alert(rs.msg);}
			});
		});
	}
	function chkform(){
		<?php if ($t == 6){?>
		zeai.confirm('<b class="S16">确定提交么？</b><br>此操作只是记录和内部存档（会员认证审核列表将显示），不点亮真实认证图标<br>如需点亮请点击：<a href="cert_hand.php?submitok=mod&memberid=<?php echo $uid;?>" class="blue B">手动强制认证</a><br>　',function(){o(supdesQQ797311_form).submit();});
		return false;
		<?php }?>
	}</script>
    <input name="submitok" type="hidden" value="modupdate" />
    <input name="uid" type="hidden" value="<?php echo $uid; ?>" />
    <input name="t" type="hidden" value="<?php echo $t; ?>" />
    <input name="ifmini" type="hidden" value="<?php echo $ifmini; ?>" />
    <input name="iframenav" type="hidden" value="<?php echo $iframenav; ?>" />
<!--基本资料，详细资料，择偶要求 结束-->
<?php }?>
<!--  -->
</td>
</tr>
</table>
<?php if ($submitok != 'photo'){?>
<br><br><br><br>
<style>
@-moz-document url-prefix() {.savebtnbox{bottom:50px}}
</style>
<div class="savebtnbox"><button type="submit" class="btn size3 HUANG3">确认并保存</button></div>
<?php }?>
</form>
<?php }?>
<?php if($t == 1){?><script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);laydate.render({elem: '#birthday'});</script><?php }?>
<?php
function RZ_get_tableinfo($uid,$rzid) {
	global $db;
	$row = $db->ROW(__TBL_RZ__,"path_b,path_b2,bz","uid=".$uid." AND rzid='$rzid'");
	if ($row){
		return array('p1'=>$row[0],'p2'=>$row[1],'bz'=>$row[2]);
	}else{
		return '';
	}
}
require_once 'bottomadm.php';?>
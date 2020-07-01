<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
if (!ifint($uid)){
	if(!empty($submitok)){
		json_exit(array('flag'=>0,'msg'=>'会员不存在/已设置隐藏/已服务成功'));
	}else{
		echo nodatatips('会员不存在/已设置隐藏/已服务成功','s');exit;
	}
}
if(is_mobile())header("Location: ".wHref('u',$uid));
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';
$chk_u_jumpurl=Href('u',$uid);
$up2 = $_ZEAI['up2']."/";
//游客浏览个人主页
if(empty($submitok) && $_VIP['YKviewU'] != 1){
	if(!iflogin())header("Location: ".HOST.'/p1/login.php?jumpurl='.Href('u',$uid));
	$row = $db->NAME($cook_uid,"RZ,myinfobfb,photo_f,sex");
	$cook_RZ = $row['RZ'];$cook_RZarr = explode(',',$cook_RZ);
	$cook_myinfobfb = intval($row['myinfobfb']);
	$cook_sex       = intval($row['sex']);
	$cook_photo_f   = intval($row['photo_f']);
	//会员浏览个人主页
	$viewhomepage_data = explode(',',$_VIP['viewhomepage_data']);
	if(count($viewhomepage_data)>0 && is_array($viewhomepage_data)){
		foreach ($viewhomepage_data as $V){
			switch ($V) {
				case 'rz_mob':if(!in_array('mob',$cook_RZarr))alert('请您先进行【'.rz_data_info('mob','title').'】',Href('cert'));break;
				case 'rz_identity':if(!in_array('identity',$cook_RZarr))alert('请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％',Href('cert'));break;
				case 'rz_photo':if(!in_array('photo',$cook_RZarr))alert('请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％',Href('cert'));break;
				case 'bfb':$config_bfb = intval($_VIP['viewhomepage_bfb_num']);if($cook_myinfobfb < $config_bfb)alert('请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％',Href('my_info'));break;
				case 'sex':$row0 = $db->NUM($uid,"sex");if($row0[0]==$cook_sex && $cook_uid<>$uid)alert('同性不能浏览会员主页＾_＾','-1');break;
				case 'photo':if($cook_photo_f!=1)alert('请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％',Href('my_info'));break;
			}
		}
	}
}
if(!empty($submitok)){
	$currfields = "nickname,grade,loveb,birthday,heigh,pay,edu,RZ,myinfobfb,photo_f,sex";
	$$rtn='json';
	require_once ZEAI.'my_chk_u.php';
	$data_loveb = $row['loveb'];
	$data_grade = $row['grade'];
	$cook_grade = $data_grade;
	$cook_sex   = $row['sex'];
	$cook_RZ = $row['RZ'];$cook_RZarr = explode(',',$cook_RZ);
	$cook_myinfobfb = intval($row['myinfobfb']);
	$cook_sex       = intval($row['sex']);
	$cook_photo_f   = intval($row['photo_f']);
	//检查拉黑
	if (gzflag($cook_uid,$uid) == -1)json_exit(array('flag'=>0,'msg'=>'对方觉得你不太适合Ta，请求失败'));
}
switch ($submitok) {
	case 'ajax_inblack':
		if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'你想干嘛，将自己拉黑？'));
		$returngz = gzflag($uid,$cook_uid);
		$F = 1;$C = '拉黑成功！';
		if($returngz == 0){
			$db->query("INSERT INTO ".__TBL_GZ__."(uid,senduid,flag) VALUES ($uid,$cook_uid,-1)");
		}elseif($returngz == -1){
			$db->query("DELETE FROM ".__TBL_GZ__." WHERE uid=".$uid." AND senduid=".$cook_uid);
			$F = 0;$C = '已取消拉黑！';
		}elseif($returngz == 1){
			$db->query("UPDATE ".__TBL_GZ__." SET flag=-1 WHERE uid=".$uid." AND senduid=".$cook_uid);
		}
		json_exit(array('flag'=>$F,'msg'=>$C));
	break;
	case 'ajax_gz':
		if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'亲！关注自己有意义么'));
		$rowtx = $db->NUM($uid,"sex");if ($rowtx){$sex= $rowtx[0];}else{exit(JSON_ERROR);}
		if($cook_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能操作＾_＾'));
		$F = 1;$C = '关注成功！';
		if (gzflag($cook_uid,$uid) != -1){
			$returngz = gzflag($uid,$cook_uid);
			if ($returngz == -1){
				$db->query("UPDATE ".__TBL_GZ__." SET flag=1 WHERE uid=".$uid." AND senduid=".$cook_uid);
			}elseif($returngz == 1){
				$db->query("DELETE FROM ".__TBL_GZ__." WHERE uid=".$uid." AND senduid=".$cook_uid);
				$F = 0;
				$C = '取消成功！';
			}elseif($returngz == 0){
				$db->query("INSERT INTO ".__TBL_GZ__."(uid,senduid,px) VALUES ($uid,$cook_uid,".ADDTIME.")");
			}
		}
		json_exit(array('flag'=>$F,'msg'=>$C));
	break;
	case 'ajax_agree':
		$db->query("UPDATE ".__TBL_USER__." SET agree=agree+1 WHERE id=".$uid);
		json_exit(array('flag'=>1,'msg'=>'点赞成功'));
	break;
	case 'ajax_gift_div':
		if(!ifint($gid) && $gid!=0)exit(JSON_ERROR);
		$SQL = ($gid == 0)?" ORDER BY rand() LIMIT 1":" WHERE id=".$gid;
		$rt=$db->query("SELECT id,title,price,picurl FROM ".__TBL_GIFT__.$SQL);
		if ($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'name');
			$row['title']  = dataIO($row['title'],'out');
			$row['picurl'] = $up2.$row['picurl'];
			$G = $row;
		}else{json_exit(array('flag'=>0));}
		$U = $db->NAME($uid,"uname,nickname,sex");
		if ($U){
			$U['nickname'] = (!empty($U['nickname']))?dataIO($U['nickname'],'out'):dataIO($U['uname'],'out');
		}else{json_exit(array('flag'=>0));}
		$GU = array_merge($G,$U);
		json_exit($GU);
	break;
	case 'ajax_senddzh':
		if ($uid == $cook_uid){exit(json_encode(array('flag'=>'metome','msg'=>'亲！跟自己打招呼有意义么')));}
		$rowtx = $db->NUM($uid,"sex,nickname,openid,subscribe");
		if ($rowtx){
			$sex= $rowtx[0];$nickname = trimhtml(dataIO($rowtx[1],'out'));$openid = $rowtx[2];$subscribe = $rowtx[3];
		}else{exit(JSON_ERROR);}
		//招招呼前提条件
		$hi_data = explode(',',$_VIP['hi_data']);
		if(count($hi_data)>0 && is_array($hi_data)){
			function hi_ifsex() {
				global $cook_sex,$hi_data;
				$hi_ifsexall = (in_array('mysex1',$hi_data)&&in_array('mysex2',$hi_data))?true:false;
				if(!$hi_ifsexall){
					if($cook_sex==1){
						if(!in_array('mysex1',$hi_data))json_exit(array('flag'=>0,'msg'=>'男性不能打招呼＾_＾'));
					}elseif($cook_sex==2){
						if(!in_array('mysex2',$hi_data))json_exit(array('flag'=>0,'msg'=>'女性不能打招呼＾_＾'));
					}
				}
			}
			foreach ($hi_data as $V){
				switch ($V) {
					case 'rz_mob':if(!in_array('mob',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('mob','title').'】<br>＾_＾'));break;
					case 'rz_identity':if(!in_array('identity',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％'));break;
					case 'rz_photo':if(!in_array('photo',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％'));break;
					case 'bfb':$config_bfb = intval($_VIP['hi_bfb_num']);if($cook_myinfobfb < $config_bfb)json_exit(array('flag'=>'nodata','msg'=>'请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％'));break;
					case 'sex':if($sex==$cook_sex && $cook_uid<>$uid)json_exit(array('flag'=>0,'msg'=>'同性不能打招呼＾_＾'));break;
					case 'photo':if($cook_photo_f!=1)json_exit(array('flag'=>'nophoto','msg'=>'请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％'));break;
					case 'mysex1':hi_ifsex();break;//发送方为男
					case 'mysex2':hi_ifsex();break;//发送方为女
					case 'vip':if($data_grade<2)json_exit(array('flag'=>'nolevel','msg'=>'只有VIP会员才可以打招呼＾_＾','jumpurl'=>Href('u',$uid)));break;
				}
			}
		}
		//招招呼前提条件结束
		$ifhiher = ($db->COUNT(__TBL_TIP__," senduid=".$cook_uid." AND uid=".$uid." AND kind=3") > 0)?true:false;
		if(!$ifhiher){
			nodata($cook_uid);
			$data_nickname = trimhtml(dataIO($row['nickname'],'out'));$data_nickname = (empty($data_nickname))?'uid:'.$cook_uid:$data_nickname;
			$data_birthday = $row['birthday'];
			$data_heigh    = $row['heigh'];
			$data_pay      = $row['pay'];
			$data_edu      = $row['edu'];
			$dzh_content = dzh_getcontent($data_birthday,$data_heigh,$data_pay,$data_edu);
			$db->query("INSERT INTO ".__TBL_TIP__."  (uid,senduid,content,kind,addtime) VALUES ($uid,$cook_uid,'$dzh_content',3,".ADDTIME.")");
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum+1 WHERE id=".$uid);
			//微信模版
			if (!empty($openid) && $subscribe==1){
				$first  = urlencode($nickname."您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人给你打招呼");
				$remark = urlencode("点击进入查看");
				@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$dzh_content.'&nickname='.$data_nickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('tz')));
			}
		}
		json_exit(array('flag'=>1));
	break;
	case 'ajax_gift_send':
		if(!ifint($gid))exit(JSON_ERROR);
		if ($cook_uid == $uid)json_exit(array('flag'=>0,'msg'=>'不能给自己送礼物哦～'));
		
		$rowtx = $db->NUM($uid,"sex");if ($rowtx){$sex= $rowtx[0];}else{exit(JSON_ERROR);}
		if($cook_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能操作＾_＾'));
		
		$G = $db->ROW(__TBL_GIFT__,"title,price","id=".$gid,'num');
		if ($G){
			$title = dataIO($G[0],'out');
			$price = intval($G[1]);
			if ($price > $data_loveb){
				json_exit(array('flag'=>'noloveb','title'=>$_ZEAI['loveB'].'账户余额不足','msg'=>'您账户余额：'.$data_loveb.'<br>当前礼物价值'.$price.$_ZEAI['loveB'].'，请充值','jumpurl'=>$chk_u_jumpurl));
			} else {
				$db->query("INSERT INTO ".__TBL_GIFT_USER__." (gid,uid,senduid,addtime) VALUES ($gid,$uid,$cook_uid,".ADDTIME.")");
				$guid = $db->insert_id();
				
				$content = '给你送了个（'.$title.'）请查收';
				$db->query("INSERT INTO ".__TBL_TIP__."  (uid,senduid,content,remark,kind,addtime) VALUES ($uid,$cook_uid,'$content',$guid,2,".ADDTIME.")");
				$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum+1 WHERE id=".$uid);
				//
				$endnum=$data_loveb-$price;
				$db->query("UPDATE ".__TBL_USER__." SET loveb=$endnum WHERE id=".$cook_uid);
				//爱豆清单入库
				$db->AddLovebRmbList($cook_uid,'赠送礼物uid:'.$uid,-$price,'loveb',9);		
				//
				$U = $db->NUM($uid,"nickname,openid");
				$nickname = dataIO($U[0],'out');$openid = $U[1];
				//weixin_mb爱豆到账提醒
				if (!empty($openid)){
					$F = urlencode($nickname."您好，".$cook_nickname."送了个礼物给你(".YmdHis(ADDTIME).")");
					$C = urlencode('礼物【'.$title.'】，价值'.$price.$_ZEAI['loveB']);
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$openid.'&num='.$price.'&first='.$F.'&content='.$C.'&url='.urlencode(mHref('my_gift')));
				}
				//
				$SQLgift2 = ",(SELECT MAX(id) AS max_id FROM ".__TBL_GIFT_USER__." WHERE uid=".$uid." GROUP BY gid) C";
				$rt=$db->query("SELECT B.id,B.picurl FROM ".__TBL_GIFT_USER__." A,".__TBL_GIFT__." B ".$SQLgift2." WHERE A.id=max_id AND A.uid=".$uid." AND A.gid=B.id GROUP BY A.gid ORDER BY A.id DESC LIMIT 30");
				$total = $db->num_rows($rt);
				if ($total>0){
					$gstr='';
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt,'num');
						if(!$rows) break;
						$gid    = $rows[0];
						$picurl = $_ZEAI['up2'].'/'.$rows[1];
						//$gnum   = $rows[2];
						$gnum = $db->COUNT(__TBL_GIFT_USER__,"uid=".$uid." AND gid=".$gid);
						$gnum_str = ($gnum > 0)?'<b>X'.$gnum.'</b>':'';
						$gstr .= '<li gid=\''.$gid.'\'><img src=\''.$picurl.'\'>'.$gnum_str.'</li>';
					}
				}
				//
				json_exit(array('flag'=>1,'C'=>$gstr));
			}
		}else{exit(JSON_ERROR);}
	break;
	case 'ajax_u_contact':
		if ($cook_uid == $uid)json_exit(array('flag'=>0,'msg'=>'亲，不能操作本人哦^_^'));
		//聊天/查看联系方式
		$chatContact_data = explode(',',$_VIP['chatContact_data']);
		if(count($chatContact_data)>0 && is_array($chatContact_data)){
			foreach ($chatContact_data as $V){
				switch ($V) {
					case 'rz_mob':if(!in_array('mob',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('mob','title').'】<br>＾_＾'));break;
					case 'rz_identity':if(!in_array('identity',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％'));break;
					case 'rz_photo':if(!in_array('photo',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％'));break;
					case 'bfb':$config_bfb = intval($_VIP['chatContact_bfb_num']);if($cook_myinfobfb < $config_bfb)json_exit(array('flag'=>'nodata','msg'=>'请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％'));break;
					case 'sex':$row0 = $db->NUM($uid,"sex");if($row0[0]==$cook_sex)json_exit(array('flag'=>0,'msg'=>'同性不能查看＾_＾'));break;
					case 'photo':if($cook_photo_f!=1)json_exit(array('flag'=>'nophoto','msg'=>'请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％'));break;
				}
			}
		}
		if(lockU($uid,'contact'))json_exit(array('flag'=>1,'msg'=>'联系方法解锁成功','C'=>contact_out($uid)));
		nocontact($cook_uid);
		nolevel($uid,$cook_uid,'contact',$chk_u_jumpurl);
		noucount_clickloveb($uid,$cook_uid,'contact');
		json_exit(array('flag'=>1,'msg'=>'联系方法解锁成功','C'=>contact_out($uid)));
	break;
	case 'ajax_clickloveb':
		$ARR = json_decode($_VIP[$kind.'_loveb'],true);
		$my_clickloveb = $ARR[$data_grade];
		if ($my_clickloveb>0){
			$total = $db->COUNT(__TBL_UCOUNT__,"FIND_IN_SET($uid,listed) AND kind='".$kind."' AND uid=".$cook_uid);
			//新人
			if($total==0){
				if ($data_loveb<$my_clickloveb)json_exit(array('flag'=>'noloveb','jumpurl'=>$chk_u_jumpurl,'title'=>'余额不足','msg'=>'您的'.$_ZEAI['loveB'].'账户余额不足'.$my_clickloveb.'个'));
				$today = YmdHis(ADDTIME,'Ymd');
				//如果今天有记录
				$row = $db->ROW(__TBL_UCOUNT__,"id,listed","kind='".$kind."' AND date='".$today."' AND uid=".$cook_uid,"num");
				if ($row){
					$ctid = $row[0];$listed = explode(',',$row[1]);
					$ARR2 = json_decode($_VIP[$kind.'_daylooknum'],true);
					$Mymaxnum = $ARR2[$data_grade];
					//当前个数和Mymaxnum比
					if(count($listed)>=$Mymaxnum){
						json_exit(array('flag'=>0,'msg'=>'今天解锁人数已达上限'.$Mymaxnum));
					}else{
						$listed[] = $uid;
						$newlist  = implode(",",$listed);
						$db->query("UPDATE ".__TBL_UCOUNT__." SET listed='".$newlist."' WHERE id=".$ctid);
					}
				}else{
					$db->query("INSERT INTO ".__TBL_UCOUNT__."(uid,listed,kind,date) VALUES ($cook_uid,$uid,'$kind','$today')");
				}
				//查看按次扣费loveb库操作
				$endnum = $data_loveb-$my_clickloveb;
				$db->query("UPDATE ".__TBL_USER__." SET loveb=$endnum WHERE id=".$cook_uid);
				//爱豆清单入库
				$strdb=($kind=='contact')?'联系方式':'聊天看信';
				if($kind=='contact'){
					$strdb='联系方式';
					$dbkind=5;
				}elseif($kind=='chat'){
					$strdb='聊天看信';
					$dbkind=4;
				}
				$db->AddLovebRmbList($cook_uid,'解锁'.$strdb.'uid:'.$uid,-$my_clickloveb,'loveb',$dbkind);		
			}
		}
		$C = contact_out($uid);
		json_exit(array('flag'=>1,'C'=>$C));
	break;
}
function contact_out($uid) {
	global $db,$up2;
	$C='';
	$row = $db->ROW(__TBL_USER__,"mob,weixin,weixin_pic,qq,email,mob_ifshow,qq_ifshow,weixin_pic_ifshow,weixin_ifshow,email_ifshow","kind<>2 AND id=".$uid,"name");
	if ($row){
		$weixin     = dataIO($row['weixin'],'out');
		$weixin_pic = dataIO($row['weixin_pic'],'out');
		$qq         = dataIO($row['qq'],'out');
		$email      = dataIO($row['email'],'out');
		$mob        = dataIO($row['mob'],'out');
		//
		$mob_ifshow        = $row['mob_ifshow'];
		$qq_ifshow         = $row['qq_ifshow'];
		$weixin_pic_ifshow = $row['weixin_pic_ifshow'];
		$weixin_ifshow     = $row['weixin_ifshow'];
		$email_ifshow	   = $row['email_ifshow'];	
		$weixin_str     =($weixin_ifshow==1)?$weixin:' 已设置保密';
		//$weixin_pic_str =($weixin_pic_ifshow==1)?$weixin_pic:' 已设置保密';
		$mob_str        =($mob_ifshow==1)?$mob:' 已设置保密';
		$qq_str         =($qq_ifshow==1)?$qq:' 已设置保密';
		$email_str      =($email_ifshow==1)?$email:' 已设置保密';
		//
		$weixin_str =(!empty($weixin_str))?$weixin_str:' -未填-';
		$qq_str     =(!empty($qq_str))?$qq_str:' -未填-';
		$email_str  =(!empty($email_str))?$email_str:' -未填-';
		$mob_str    =(!empty($mob_str))?$mob_str:' -未填-';

		$C   = '<li><i class=\'ico\' style=\'background-color:#31C93C\'>&#xe607;</i> 微信：'.$weixin_str.'</li>';
		if (!empty($weixin_pic) && $weixin_pic_ifshow==1){
			$C  .= '<li><div class=\'wxpic\'>';
			$C .='微信二维码：点击放大→　';
			$C .= '<img src=\''.$up2.$weixin_pic.'\' onClick=ZeaiPC.piczoom(\''.$up2.$weixin_pic.'\');>';
			$C .= '</div></li>';
		}
		$C .= '<li><i class=\'ico\' style=\'background-color:#51B7EC\'>&#xe612;</i> QQ：'.$qq_str.'</li>';
		$C .= '<li><i class=\'ico2\' style=\'background-color:#c7c0de\'>&#xe682;</i> 邮箱：'.$email_str.'</li>';
		$C .= '<li><i class=\'ico2\' style=\'background-color:#FFBA57\'>&#xe68a;</i> 手机：'.$mob_str.'</li>';
	}
	return $C;
}
$switch = json_decode($_ZEAI['switch'],true);
$extifshow = json_decode($_UDATA['extifshow'],true);
$mate_diy = explode(',',$_ZEAI['mate_diy']);
$fields  = "id,uname,nickname,sex,grade,photo_s,photo_f,RZ,myinfobfb,regtime,endtime,endip,click,agree,kind,dataflag,photo_ifshow";
$fields .= ",aboutus,birthday,areatitle,love,heigh,weigh,edu,pay,house,car,nation,area2title,child,blood,tag,marrytype,marrytime,job,hnid";
if (@count($extifshow) >0 && is_array($extifshow)){foreach ($extifshow as $ev){$evARR[] = $ev['f'];}$fields .= ",".implode(",",$evARR);}
$fields .= ",mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2,mate_other";
$row = $db->ROW(__TBL_USER__,$fields,"(flag=1 OR flag=-2) AND id=".$uid);
if ($row){
	$row_ext    = $row;
	$uname      = dataIO($row['uname'],'out');
	$nickname   = dataIO($row['nickname'],'out');
	$sex        = $row['sex'];
	$grade      = $row['grade'];$Ugrade=$grade;
	$Smode_g    = 'g_'.$grade;
	$photo_s    = $row['photo_s'];
	$photo_f    = $row['photo_f'];
	$RZ = $row['RZ'];
	$myinfobfb  = $row['myinfobfb'];
	$regtime    = YmdHis($row['regtime'],'YmdHi');
	$endtime    = YmdHis($row['endtime'],'YmdHi');
	$endip      = $row['endip'];$regip1='/((?:\d+\.){3})\d+/';$endip = preg_replace($regip1,"\\1*",$endip);
	$click      = $row['click'];
	$agree      = $row['agree'];
	$Ukind      = $row['kind'];
	$dataflag   = $row['dataflag'];
	$photo_ifshow   = $row['photo_ifshow'];
	$aboutus    = dataIO($row['aboutus'],'out');$aboutusALL=$aboutus;
	$aboutus    = (empty($aboutus))?'这个家伙很懒，什么都没留下^_^':$aboutus;
	$aboutus    =($dataflag==1)?$aboutus:'审核中';
	$aboutusLen = str_len($aboutus);
	$aboutus = gylsubstr($aboutus,92,0,"utf-8",true);
	$birthday   = $row['birthday'];
	$birthday   = (!ifdate($birthday))?'':$birthday;
	$areatitle  = dataIO($row['areatitle'],'out');
	$heigh      = $row['heigh'];
	$love       = $row['love'];
	$house       = $row['house'];
	$edu        = $row['edu'];
	$pay        = $row['pay'];
	$job        = $row['job'];
	$area2title = dataIO($row['area2title'],'out');
	$weigh      = $row['weigh'];
	$car        = $row['car'];
	$nation     = $row['nation'];
	$marrytype  = $row['marrytype'];
	$marrytime  = $row['marrytime'];
	$child      = $row['child'];
	$blood      = $row['blood'];
	$tag        = $row['tag'];
	$hnid       = $row['hnid'];
	//$hnname     = dataIO($row['hnname'],'out');
	//
	$mate_age1      = intval($row['mate_age1']);
	$mate_age2      = intval($row['mate_age2']);
	$mate_heigh1    = intval($row['mate_heigh1']);
	$mate_heigh2    = intval($row['mate_heigh2']);
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
	$ifmate = ( !empty($mate_age1) || !empty($mate_age2) || !empty($mate_heigh1) || !empty($mate_heigh2) || !empty($mate_pay) || !empty($mate_edu) || !empty($mate_areaid) || !empty($mate_areatitle) || !empty($mate_love) || !empty($mate_house) || !empty($mate_job) || !empty($mate_child) || !empty($mate_marrytime) || !empty($mate_companykind) || !empty($mate_smoking) || !empty($mate_drink) || !empty($mate_areaid2)  || !empty($mate_areatitle2) || !empty($mate_other)  )?true:false;
	if($ifmate){
		$mate_age       = $mate_age1.','.$mate_age2;
		$mate_age_str   = mateset_out($mate_age1,$mate_age2,'岁');
		$mate_age_str = str_replace("不限","",$mate_age_str);
		$mate_heigh     = $mate_heigh1.','.$mate_heigh2;
		$mate_heigh_str = mateset_out($mate_heigh1,$mate_heigh2,'cm');
		$mate_heigh_str = str_replace("不限","",$mate_heigh_str);
		$mate_weigh     = $mate_weigh1.','.$mate_weigh2;
		$mate_weigh_str = mateset_out($mate_weigh1,$mate_weigh2,'kg');
		$mate_weigh_str = str_replace("不限","",$mate_weigh_str);
		$mate_areaid_str  = (!empty($mate_areatitle))?$mate_areatitle:'';
		$mate_areaid2_str = (!empty($mate_areatitle2))?$mate_areatitle2:'';
		$mate_pay_str   = udata('pay',$mate_pay);
		$mate_edu_str   = udata('edu',$mate_edu);
		$mate_love_str  = udata('love',$mate_love);
		$mate_car_str   = udata('car',$mate_car);
		$mate_house_str = udata('house',$mate_house);
		$mate_job_str         = udata('job',$mate_job);
		$mate_child_str       = udata('child',$mate_child);
		$mate_marrytime_str   = udata('marrytime',$mate_marrytime);
		$mate_companykind_str = udata('companykind',$mate_companykind);
		$mate_smoking_str     = udata('smoking',$mate_smoking);
		$mate_drink_str       = udata('drink',$mate_drink);
		if (count($mate_diy) >= 1 && is_array($mate_diy)){
			$mate_fld = array();$mate_li_out='';
			foreach ($mate_diy as $k=>$V) {
				$ext = mate_diy_par($V,'ext');
				$tmpD = 'mate_'.$V;
				$tmpS = 'mate_'.$V.'_str';
				$mate_data = $$tmpD;
				$mate_str  = $$tmpS;
				if(!empty($mate_data) && $mate_data!='0,0'){
					switch ($ext) {
						case 'checkbox':
							$mate_str_=explode(',',$mate_str);
							$mate_strN=count($mate_str_);
							if($mate_strN>1){
								$matesonli='';
								foreach ($mate_str_ as $ks=>$Vs) {$matesonli.='【'.$Vs.'】';}
								$mate_str = $matesonli;
							}
						break;
						default:break;
					}
					$mate_li_out.='<li><font>'.mate_diy_par($V).'：</font>'.$mate_str.'</li>';
				}
			}
			$mate_li_out .= (!empty($mate_other))?'<li><font>其他要求：</font>'.$mate_other.'</li>':'';
		}
	}
	$mate_areaid    = explode(',',$mate_areaid);
	$RZarr          = explode(',',$RZ);
}else{
	if(!empty($submitok)){
		json_exit(array('flag'=>0,'msg'=>'会员不存在或已经服务成功'));
	}else{
		echo '<link href="'.HOST.'/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />';
		echo nodatatips('无法访问，可能原因：','b','1．会员不存在<br>2．已服务成功<br>3．注册未完成<br>4．已设置隐藏<br>5．已冻结帐号<br><br><a class="btn size3 HONG yuan" href="'.HOST.'">进入 '.$_ZEAI['siteName'].'</a>');exit;
	}
}
$db->query("UPDATE ".__TBL_USER__." SET click=click+1 WHERE id=".$uid);
$nickname_str = (!empty($nickname))?$nickname:$uname;

if (!empty($photo_s) && $photo_f == 1){
	$photo_s_url = $up2.$photo_s;
	$photo_m_url = smb($photo_s_url,'m');
	$photo_b_url = smb($photo_s_url,'b');
	$photo_m_str = '<img src="'.$photo_m_url.'" class="photo_m zoom" id="photo_m">';
	$photo_ewm_s_url = $photo_s_url;
}else{
	$photo_m_str = '<img src="'.HOST.'/res/photo_b'.$sex.'.png" class="photo_m">';
	$photo_ewm_s_url = HOST.'/res/photo_s'.$sex.'.png';
}
//
$lockstr = '';$ifblur=0;
if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
	if (!empty($photo_s) && $photo_f == 1){
		$photo_s_url = smb($photo_s_url,'blur');
		$photo_m_url = $photo_s_url;
		$photo_b_url = $photo_s_url;
		$photo_ewm_s_url = $photo_s_url;
		$photo_m_str = '<img src="'.$photo_m_url.'" class="photo_m zoom" id="photo_m">';
	}
	$ifblur=1;
	$lockstr = '<i class="ico lockico">&#xe61e;</i><span class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</span>';
	$lockstr2 = '<div class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</div>';
}
//
if($photo_ifshow==0 && $ifblur==0){
	$photo_s_url = smb($photo_s_url,'blur');
	$photo_m_url = $photo_s_url;
	$photo_b_url = $photo_s_url;
	$photo_ewm_s_url = $photo_s_url;
	$photo_m_str = '<img src="'.$photo_m_url.'" class="photo_m zoom" id="photo_m" style="object-fit:cover;-webkit-object-fit:cover">';
	$lockstr = '';
	$lockstr2 ='';
}
//
$sex_str = ($sex == 1)?'他':'她';
$gzclass='';$gz_str='<i class="ico">&#xe70f;</i>关注';
$inblackclass = '';$inblack_str = '拉黑';
$ifhiher=false;
if (ifint($cook_uid)){
	$row = $db->NUM($cook_uid,"sex,grade,areaid,areatitle");
	if ($row){
		$cook_sex  = $row[0];
		$cook_grade= $row[1];
		$areaid    = $row[2];
		$robot=json_decode($_ZEAI['robot'],true);
		if($Ukind==4 && $robot['areaupdate']==1 && !empty($areaid)){
			$areatitle= $row[3];
			$db->query("UPDATE ".__TBL_USER__." SET areaid='$areaid',areatitle='$areatitle' WHERE id=".$uid);
		}
	}
	//if($cook_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能浏览＾_＾'));
	$gzflag = gzflag($uid,$cook_uid);
	if ($gzflag == 1){
		$gzclass=' class="ed"';
		$gz_str='<i class="ico">&#xe62f;</i>已关注';
	}
	if ($gzflag == -1){
		$inblackclass = 'class="ed"';$inblack_str = '已拉黑';
	}
	$inblackclass = ($gzflag == -1)?' class="ed"':'';
	$ifhiher = ($db->COUNT(__TBL_TIP__," senduid=".$cook_uid." AND uid=".$uid." AND kind=3") > 0)?true:false;
}
$ifhiher_str=($ifhiher)?'<i class="ico">&#xe628;</i><span>已打招呼</span>':'<i class="ico">&#xe628;</i><span>打招呼</span>';
$ifhiherclass=($ifhiher)?' class="ed"':'';

//访问记录
$shuaxin_homeclick = 'homeclick'.$uid.$cook_uid;
if ( $cook_uid != $uid && ifint($cook_uid) && $_COOKIE["$shuaxin_homeclick"] != 'wwwZEAIcn') {
	$vnum = $db->COUNT(__TBL_CLICKHISTORY__,"uid=".$uid." AND senduid=".$cook_uid);
	if ($vnum == 0){
		$db->query("INSERT INTO ".__TBL_CLICKHISTORY__."  (uid,senduid,addtime) VALUES ($uid,$cook_uid,".ADDTIME.")");
	}else{
		$db->query("UPDATE ".__TBL_CLICKHISTORY__." SET addtime=".ADDTIME." WHERE uid=".$uid." AND senduid=".$cook_uid);
	}
	setcookie("$shuaxin_homeclick",'wwwZEAIcn',time()+7200000,"/",$_ZEAI['CookDomain']);
}

//联系方法
//$urole = json_decode($_ZEAI['urole']);
$urolenew = json_decode($_ZEAI['urole'],true);
$newarr=array();foreach($urolenew as $RV){if($RV['f']==1){$newarr[]=$RV;}else{continue;}}
$newarr=encode_json($newarr);
$urole = json_decode($newarr);

$contact_daylooknum = json_decode($_VIP['contact_daylooknum']);
$contact_loveb   = json_decode($_VIP['contact_loveb']);
$ARR = json_decode($_VIP['contact_daylooknum'],true);
$ifShowContact=false;
if (count($ARR) >= 1 && is_array($ARR) && max($ARR)>0 ){
	$ifShowContact=true;
}
if($switch['Smode'][$Smode_g] == 1 && $Ukind!=2){
	$ifShowContact=true;
}else{
	$ifShowContact=false;	
}

$nav='index';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $nickname_str;?>个人主页 - <?php echo $areatitle.'征婚交友 - '.$_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/u.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="main1 S5 fadeInL">
	<p><?php echo $photo_m_str;?><span id="Ublack"<?php echo $inblackclass; ?>><?php echo $inblack_str;?></span><span id="Fn315">举报</span><span id="agree" title="点赞"><i class="ico">&#xe652;</i> <font><?php echo $agree;?></font></span></p>
    <?php
	$rt=$db->query("SELECT path_s FROM ".__TBL_PHOTO__." WHERE uid=".$uid." AND flag=1 ORDER BY id DESC LIMIT 50");
	$photo_total = $db->num_rows($rt);
	if (!empty($photo_s) || $photo_total>0){
		$ifphoto=1;
	?>
    <div class="photou">
    	<i class="ico" id="pre" title="上一张">&#xe602;</i>
        <div class="photobox">
        	<?php if($ifblur==1)echo $lockstr2;?>
        	<div class="libox S5" id="libox">
				<?php
				if (!empty($photo_s) && $photo_f==1){
				?>
                <li class="ed" src="<?php echo $photo_m_url; ?>"><span>1<font>/</font><?php echo $photo_total+1;?></span></li>
                <?php }
                if ($photo_total>0) {
					if (!empty($photo_s)){$i1=2;$total=$photo_total+1;}else{$i1=1;$total=$photo_total;}
                    for($i=$i1;$i<=$total;$i++) {
                        $rows = $db->fetch_array($rt,'num');
                        if(!$rows) break;
                        $path_s = $rows[0];
                        $dst_s  = $up2.$path_s;
						if($photo_ifshow==0)$dst_s=HOST.'/res/photo_m'.$sex.'_hide.png';
						echo '<li src="'.$dst_s.'"><span>'.$i.'<font>/</font>'.$total.'</span></li>';
                    }
                }
                ?>
			</div>
        </div>
        <i class="ico" id="next" title="下一张">&#xe601;</i>
	</div>
    <?php }else{$ifphoto=0;}?>
	<div class="uinfo">
	<h2><?php echo uicon($sex.$grade,2).gylsubstr($nickname_str,14,0,"utf-8",true);?><font class="S14 C999">（UID：<?php echo $uid;?>）</font><span class="click" title="人气"><i class="ico">&#xe643;</i><b><?php echo $click;?></b></span><div title="<?php echo strip_tags($gz_str);?>" id="gz"<?php echo $gzclass; ?>><?php echo $gz_str;?></div>
    </h2>
	<div class="cert" onClick="zeai.openurl(PCHOST+'/my_cert.php')"><?php echo RZ_html($RZ,'m','allcolor');?></div>
    <div class="udata">
    	<?php $birthday = getage($birthday);$birthday_str=($birthday>=18)?$birthday.'岁':'';?>
    	<?php if (!empty($birthday_str)){?><li><?php echo $birthday_str; ?></li><?php }?>
        <?php if (ifint($love)){?><li><?php echo udata('love',$love);?></li><?php }?>
    	<?php if (ifint($heigh)){?><li><?php echo udata('heigh',$heigh);?></li><?php }?>
    	<?php if (ifint($pay)){?><li><?php echo udata('pay',$pay);?></li><?php }?>
    	<?php if (ifint($edu)){?><li><?php echo udata('edu',$edu);?></li><?php }?>
    	<?php if (!empty($areatitle)){?><li><?php echo $areatitle;?></li><?php }?>
    	<?php if (ifint($job)){?><li><?php echo udata('job',$job);?></li><?php }?>
    	<?php if (ifint($house)){?><li><?php echo udata('house',$house);?></li><?php }?>
    	<?php $marrytime_str=udata('marrytime',$marrytime);if (!empty($marrytime) && $marrytime_str!='不限'){?><li>期望<?php echo $marrytime_str;?>结婚</li><?php }?>
        <?php
/*		if (!empty($tag)){
			$tagstr = checkbox_div_list_get_listTitle('tag'.$sex,$tag);
			$tagstr = explode(',',$tagstr);
			foreach ($tagstr as $v){echo '<li>'.$v.'</li> ';}
		}
*/		?>
    </div>
    
    <div class="clear"></div>
    <?php if (!empty($aboutus)){?>
    <em><font style="color:#E83191">个人独白：</font><?php echo trimhtml($aboutus);
    if($aboutusLen>184){echo '<a href="javascript:showaboutus();">全部<i class="ico">&#xe601;</i></a>';}
    ?></em>
    <?php }?>
    </div>
    <div class="ubtn">
        <?php if($switch['Smode'][$Smode_g] == 1 && $Ukind!=2 && in_array('chat',$navarr)){?><li id="chat"><i class="ico">&#xe676;</i><span>发私信</span></li><?php }?>
        <?php if (in_array('hi',$navarr)){?><li<?php echo $ifhiherclass;?> id="hi"><?php echo $ifhiher_str;?></li><?php }?>
        <?php if($ifShowContact && in_array('contact',$navarr)){?><li id="mycontact"><i class="ico">&#xe60e;</i><span>联系Ta</span></li><?php }?>
      	<?php if(@in_array('hn',$navarr)){?><li><i class="ico">&#xe621;</i><span><a href="<?php echo Href('hongniang');?>">红娘牵线</a></span></li><?php }?>
    </div>
    <div class="logininfo2">
    	<?php if ($cook_grade > 1){?>
    	注册时间：<?php echo $regtime;?>　　最后登录时间：<?php echo $endtime;?>　　最后地点：<a href="javascipt:;" class="endip" id="ip" value="<?php echo $endip; ?>"><i class="ico" title="百度查询IP所在地">&#xe614;</i></a>
        <?php }else{ ?>
    	注册时间：<a href="<?php echo HOST; ?>/p1/my_vip.php">VIP会员可见</a>　　最后登录时间：<a href="<?php echo HOST; ?>/p1/my_vip.php">VIP会员可见</a>　　最后地点：<a href="<?php echo HOST; ?>/p1/my_vip.php">VIP会员可见</a>
        <?php }?>
    </div>    
	<div class="u2wm">
   		<img src="<?php echo HOST;?>/p1/img/uewm.png" id="uewm">
        <img src="<?php echo $photo_ewm_s_url;?>" class="ewms">
        <span>微信扫一扫 手机关注<?php echo $sex_str;?></span>
    </div>
    <?php if(@in_array('hn',$navarr)){?>
    <div class="hn">
    	<h1>专属红娘</h1>
        <?php if (ifint($hnid)){
			$row = $db->ROW(__TBL_CRM_HN__,"truename,path_s","id=".$hnid);
			if ($row){
				$truename = dataIO($row['truename'],'out',7);
				$path_s   = $row['path_s'];
				$path_s_url = (!empty($path_s))?$up2.'/'.getpath_smb($path_s,'b'):HOST.'/p1/img/hn0.jpg';
				$unum = $db->COUNT(__TBL_USER__,"hnid=".$hnid);
			}
			?>
            <a href="<?php echo Href('hongniang',$hnid); ?>" target="_blank">
            <div class="p" style="background:url('<?php echo $path_s_url; ?>')center top/100% auto no-repeat"></div>
            <em><h2><?php echo $truename;?></h2><span>已服务会员：<?php echo $unum;?>人</span></em>
            </a>
        <?php }else{ ?>
		<div class="p" style="background:url('<?php echo HOST; ?>/p1/img/hn0.jpg')center top/100% auto no-repeat"></div>
    	<a href="<?php echo Href('hongniang'); ?>" class="hnnav">进入红娘大厅</a>
        <?php }?>
        <div class="clear"></div>
    </div>
    <?php }?>
</div>
<div class="main2 fadeInR">
	<div class="L S5">
    	<h1>详细资料</h1>
        <?php if (!empty($car)){?><dl><dt>买车情况：</dt><dd><?php echo udata('car',$car);?></dd></dl><?php }?>
        <?php if (!empty($weigh)){?><dl><dt>体　　重：</dt><dd><?php echo udata('weigh',$weigh);?></dd></dl><?php }?>
        <?php if (!empty($marrytype)){?><dl><dt>嫁娶形式：</dt><dd><?php echo udata('marrytype',$marrytype);?></dd></dl><?php }?>
        <?php if (!empty($child)){?><dl><dt>子女情况：</dt><dd><?php echo udata('child',$child);?></dd></dl><?php }?>
        <?php if (!empty($blood)){?><dl><dt>血　　型：</dt><dd><?php echo udata('blood',$blood);?></dd></dl><?php }?>
        <?php if (!empty($tag)){?><dl><dt>我的标签：</dt><dd><?php echo checkbox_div_list_get_listTitle('tag'.$sex,$tag);?></dd></dl><?php }?>
        <?php if (!empty($area2title)){?><dl><dt>户籍地区：</dt><dd><?php echo $area2title;?></dd></dl><?php }?>
        <?php if (!empty($nation)){?><dl><dt>民　　族：</dt><dd><?php echo udata('nation',$nation);?></dd></dl><?php }?>
        <?php
		if (@count($extifshow) > 0 || is_array($extifshow)){
        foreach ($extifshow as $V) {
            $data = dataIO($row_ext[$V['f']],'out');
            switch ($V['s']) {
                case 1:$Fkind = 'ipt';$span=$data;break;
                case 2:$Fkind = 'slect';$span=udata($V['f'],$data);break;
                case 3:$Fkind = 'chckbox';$span=checkbox_div_list_get_listTitle($V['f'],$data);break;
            }
            if (!empty($span)){
                $showul=true;
            ?>
            <dl><dt><?php echo $V['t'];?>：</dt><dd><?php echo $span;?></dd></dl>
        <?php }}}?>
        <div class="clear"></div>
        <h1 class="brtop">择偶要求</h1>
        <div class="umate"><?php echo  $mate_li_out;?></div>
        <h1 class="brtop">看过<?php echo $sex_str;?>的人</h1>
        <div class="view">
            <?php
			$rt=$db->query("SELECT a.senduid,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_CLICKHISTORY__." a,".__TBL_USER__." U WHERE a.uid=".$uid." AND a.senduid=U.id AND U.flag=1 AND U.kind<>4 ORDER BY a.addtime DESC LIMIT 6");
			$total = $db->num_rows($rt);
			if ($total > 0) {
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows) break;
					$senduid  = $rows['senduid'];
					$nickname = dataIO($rows['nickname'],'out');
					$nickname = (empty($nickname))?'uid:'.$senduid:$nickname;
					$sex      = $rows['sex'];
					$grade    = $rows['grade'];
					$photo_s  = $rows['photo_s'];
					$photo_f  = $rows['photo_f'];
					$photo_ifshow = $rows['photo_ifshow'];
					//
					if($photo_ifshow==0){
						$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
					}else{
						$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';	
					}
					//
					$sexbg      = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':'class="m"';
					echo '<li><a href="'.Href('u',$senduid).'"><img src="'.$photo_s_url.'"'.$sexbg.'><span>'.uicon($sex.$grade).$nickname.'</span></a></li>';
				}
			}
			?>
        </div>
        <div class="clear"></div>
        <h1 class="brtop">关注<?php echo $sex_str;?>的人</h1>
        <div class="view">
            <?php
			$rt=$db->query("SELECT a.senduid,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_GZ__." a,".__TBL_USER__." U WHERE a.uid=".$uid." AND a.senduid=U.id AND U.flag=1 AND U.kind<>4 ORDER BY a.px DESC LIMIT 6");
			$total = $db->num_rows($rt);
			if ($total > 0) {
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows) break;
					$senduid  = $rows['senduid'];
					$nickname = dataIO($rows['nickname'],'out');
					$sex      = $rows['sex'];
					$grade    = $rows['grade'];
					$photo_s  = $rows['photo_s'];
					$photo_f  = $rows['photo_f'];
					$photo_ifshow = $rows['photo_ifshow'];
					//
					if($photo_ifshow==0){
						$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
					}else{
						$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';	
					}
					//
					//$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					$sexbg      = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':'class="m"';
					echo '<li><a href="'.Href('u',$senduid).'"><img src="'.$photo_s_url.'"'.$sexbg.'><span>'.uicon($sex.$grade).$nickname.'</span></a></li>';
				}
			}
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="R S5">
    	<?php if(@in_array('gift',$navarr)){?>
    	<h1>收到礼物(<?php echo $db->COUNT(__TBL_GIFT_USER__,"uid=".$uid); ?>)</h1>
        <div class="ugift">
            <ul id="gift">
            <li gid="0" uid="<?php echo $uid; ?>"><i class="ico">&#xe69a;</i></li>
            <?php 
            $SQLgift2 = ",(SELECT MAX(id) AS max_id FROM ".__TBL_GIFT_USER__." WHERE uid=".$uid." GROUP BY gid) C";
            $rt=$db->query("SELECT B.id,B.picurl FROM ".__TBL_GIFT_USER__." A,".__TBL_GIFT__." B ".$SQLgift2." WHERE 1=1 AND A.id=max_id AND A.uid=".$uid." AND A.gid=B.id GROUP BY A.gid ORDER BY A.id DESC LIMIT 20");
            $total = $db->num_rows($rt);
            if ($total==0){
                echo '<em>缘分，从送第一份礼物开始认识！<span>开始送礼</span></em>';
            }else{
                for($i=1;$i<=$total;$i++) {
                    $rows = $db->fetch_array($rt,'num');
                    if(!$rows) break;
                    $gid    = $rows[0];
                    $picurl = $_ZEAI['up2'].'/'.$rows[1];
                    $gnum = $db->COUNT(__TBL_GIFT_USER__,"uid=".$uid." AND gid=".$gid);
                    $gnum_str = ($gnum > 0)?'<b>X'.$gnum.'</b>':'';
                    echo '<li gid="'.$gid.'"><img src="'.$picurl.'">'.$gnum_str.'</li>';
                }
            }
            ?>
            <div class="clear"></div>
            </ul>
            <div class="clear"></div>
        </div>
        <?php }?>
		<h1>你可能喜欢</h1>
    	<div class="ulist">
    	<?php
		if(ifint($cook_uid && !empty($cook_sex))){
			$SQL .= ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
		}
		$ORDER = (empty($ORDER))?"ORDER BY refresh_time DESC":$ORDER;
		$rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,pay,love,heigh,photo_ifshow FROM ".__TBL_USER__." b WHERE kind<>4 AND flag=1 AND dataflag=1 AND photo_f=1 ".$SQL." ".$ORDER." LIMIT 6");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows)break;
				$uid2      = $rows['id'];
				$nickname = dataIO($rows['nickname'],'out');
				$sex      = $rows['sex'];
				$love     = $rows['love'];
				$grade    = $rows['grade'];
				$photo_s  = $rows['photo_s'];
				$photo_f  = $rows['photo_f'];
				$areatitle= $rows['areatitle'];
				$birthday = $rows['birthday'];
				$job      = $rows['job'];
				$pay      = $rows['pay'];
				$heigh    = $rows['heigh'];
				$photo_ifshow = $rows['photo_ifshow'];
				$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
				//
				$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
				$heigh_str    = ($heigh<=0)?'':$heigh.'cm ';
				$job_str      = (empty($job))?'':udata('job',$job).' ';
				$pay_str      = (empty($pay))?'':udata('pay',$pay).' ';
				$love_str      = (empty($love))?'':udata('love',$love).' ';
				//
				if($ifblur==1){
					$photo_m = 'blur';
				}else{
					$photo_m = 'm';
				}
				//
				if($photo_ifshow==0 && $ifblur==0){
					$lockstr = '';
					$photo_m_url=HOST.'/res/photo_m'.$sex.'_hide.png';
				}else{
					$photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.smb($photo_s,$photo_m):HOST.'/res/photo_m'.$sex.'.png';	
				}
				//
				$sexbg      = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':' class="m"';
				$echo .= '<li>';
				$uhref = Href('u',$uid2);
				$echo .= '<a href="'.$uhref.'" class="mbox">';
				$echo .= '<img src="'.$photo_m_url.'"'.$sexbg.'>'.$lockstr;
				$echo .= '<em><span>'.$love_str.'</span><span>'.$job_str.'</span><span>'.$pay_str.'</span></em>';
				$echo .= '<b>联系Ta</b>';
				$echo .= '</a>';
				$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
				$echo .= '<h4>'.$nickname.'</h4>';
				$echo .= '<h5>'.$birthday_str.$heigh_str.$areatitle.'</h5>';
				$echo .= '</li>';
			}
			echo $echo;
		}else{
			echo nodatatips('暂时没有会员','s');
		}
		?>
        </div>
        
    </div>
    <div class="clear"></div>
</div>
<div id="aboutusALL"><?php echo $aboutusALL;?></div>
<div id='tips0_100_0' class='tips0_100_0 alpha0_100_0'></div>
<div id="box_gift" class="box_gift">
    <em><img><h3></h3><h6></h6></em>
    <a href="javascript:;">看看其他礼物</a>
    <a href="javascript:;">确认赠送</a>
</div>

<?php if ($ifShowContact){?>
    <div id="contactbox" class="contactbox"></div>
	<?php if ( ifint($cook_uid)){?>
    <div id="u_contact_daylooknumHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $contact_daylooknum->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> 人/天':' 无权查看';
            $ifmy = ($cook_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
            $outZ .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outZ;
        ?>
        </ul>
        <button type="button" class="W100_ btn size3 HUANG Mcenter block center chatlock" onClick="zeai.openurl_('<?php echo HOST;?>/p1/my_vip.php?jumpurl='+encodeURIComponent(jumpurl))"><i class="ico vipbtn" style="color:#fff;font-size:18px;margin-right:4px">&#xe6ab;</i>我要升级会员</button>
    </div>
    <div id="u_contact_lovebHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $contact_loveb->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> '.$_ZEAI['loveB'].'/人':' 免费查看';
            if($cook_grade==$grade){
                $ifmy = '　<font class="Cf00">（我）</font>';
                $myclkB=$num;
            }else{
                $ifmy = '';
            }
            $outE .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outE;
        ?>
        </ul>
        <a class="btn size3 HONG W50_ chatlock" onClick="clickloveb(<?php echo $uid;?>,'contact')">单次<?php echo $myclkB;?>解锁</a>
        <a class="btn size3 HUANG W50_ chatlock" onClick="zeai.openurl_('<?php echo HOST;?>/p1/my_vip.php?jumpurl='+encodeURIComponent(jumpurl))">升级会员</a>
    </div>
    <?php }?>
<?php }//联系方法结束?>

    <div id="contact_levelHelp" class="helpDiv levelHelp">
    	<i class="ico sorry">&#xe61f;</i><br>
    	您只能互动会员级别比你低的或同级会员<br>您当前【<?php echo utitle($cook_grade);?>】<br>请至少升级到【<?php echo utitle($Ugrade);?>】<br><br>
        <button type="button" class="W100_ btn size3 HUANG Mcenter block center chatlock" onClick="zeai.openurl_('<?php echo HOST;?>/p1/my_vip.php?jumpurl='+encodeURIComponent(jumpurl))"><i class="ico vipbtn">&#xe6ab;</i><span>立即升级</span></button>
    </div>

<script>var uid=<?php echo $uid;?>,lovebstr='<?php echo $_ZEAI['loveB'];?>',ifphoto=<?php echo $ifphoto;?>,uhref='<?php echo mHref('u',$uid);?>',jumpurl = '<?php echo $chk_u_jumpurl;?>';localStorage.uid=<?php echo $uid;?>;</script>
<script src="<?php echo HOST;?>/p1/js/u.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
<?php if (!$ifhiher && in_array('hi',$navarr)){?>hi.onclick=hiFn;<?php }?>
<?php if ($ifShowContact && in_array('contact',$navarr)){?>mycontact.onclick=mycontactFn;<?php }?>
<?php if(@in_array('gift',$navarr)){?>setgift(gift,box_gift,uid);<?php }?>
</script>
<?php require_once ZEAI.'p1/bottom.php';?>

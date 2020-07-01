<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_index.php';
$uid = (ifint($uid))?$uid:$a;$ii=$i;
if (!ifint($uid)){
	if($m=='wap'){
		alert('会员不存在或已经服务成功','back');
	}else{
		json_exit(array('flag'=>0,'msg'=>'会员不存在或已经服务成功'));	
	}
}
$share_u_link=wHref('u',$uid);
if(!empty($submitok) || $_VIP['YKviewU'] != 1){
	if($submitok=='ajax_tgxqk'){
		if(!empty($cook_openid) && is_weixin()){
			$rowtg = $db->ROW(__TBL_TG_USER__,"id,uname,mob,pwd,uid","flag=1 AND openid='$cook_openid'","name");
			if ($rowtg){
				$cook_tg_uid   = $rowtg['id'];
				$uidd          = intval($rowtg['uid']);
				$cook_tg_uname = $rowtg['uname'];
				$cook_tg_pwd   = $rowtg['pwd'];
				if(!ifint($uidd) && ifint($cook_uid) )$db->query("UPDATE ".__TBL_TG_USER__." SET uid=".$cook_uid." WHERE id=".$cook_tg_uid);
				setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_openid",$cook_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
				json_exit(array('flag'=>1));
			}
		}elseif(ifint($cook_uid) && !empty($cook_pwd)){
			$row = $db->ROW(__TBL_USER__,"id,uname,pwd","id=".$cook_uid." AND pwd='$cook_pwd'");
			if(!$row)json_exit(array('flag'=>0));
			//
			$rowtg = $db->ROW(__TBL_TG_USER__,"id,uname,pwd","flag=1 AND uid=".$cook_uid,"name");
			if ($rowtg){
				$cook_tg_uid   = $rowtg['id'];
				$cook_tg_uname = $rowtg['uname'];
				$cook_tg_pwd   = $rowtg['pwd'];
				setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
				json_exit(array('flag'=>1));
			}
		}
		if(ifint($cook_tg_uid) && !empty($cook_tg_pwd)){
			$rowtg = $db->ROW(__TBL_TG_USER__,"id","flag=1 AND id=".$cook_tg_uid." AND pwd='".$cook_tg_pwd."'","name");
			if($rowtg)json_exit(array('flag'=>1));
		}
		json_exit(array('flag'=>0));
	}
	if(ifint($cook_uid))zeai_chk_ugrade($cook_uid);
	//
	$currfields = "nickname,grade,loveb,birthday,heigh,pay,edu,RZ,myinfobfb,photo_f,ifadm";
	$$rtn='json';$chk_u_jumpurl=$share_u_link;require_once ZEAI.'my_chk_u.php';
	$data_loveb = $row['loveb'];
	$data_grade = $row['grade'];
	$cook_RZ = $row['RZ'];$cook_RZarr = explode(',',$cook_RZ);
	$cook_myinfobfb = intval($row['myinfobfb']);
	$cook_photo_f   = intval($row['photo_f']);
	$cook_ifadm     = intval($row['ifadm']);
	//
	if($submitok=='ajax_sendchcit'){
		$row = $db->ROW(__TBL_USER__,"openid,subscribe,chatHiContact_iftips","(kind=1 OR kind=3) AND id=".$uid,"name");
		if(!$row)json_exit(array('flag'=>0,'msg'=>'此用户无法发送'));
		$openid=$row['openid'];$subscribe=$row['subscribe'];$chatHiContact_iftips=$row['chatHiContact_iftips'];
		$content="用户【".$mbnickname." UID:".$cook_uid."】觉得你不错，对你有意，由于你没有诚信认证，Ta无法联系你，缘份就在一瞬间，好就别错过啦，赶快去认证！";
		if (!empty($openid) && $subscribe==1 && $cook_uid!=$uid && $chatHiContact_iftips==1){
			$mbnickname = urlencode($cook_nickname);
			$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 收到访客认证请求！");
			$remark = urlencode($content);
			$mbcontent = '通知';
			@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$mbcontent.'&nickname='.$mbnickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('cert')));
		}
		if($chatHiContact_iftips==1){
			$db->query("UPDATE ".__TBL_USER__." SET chatHiContact_iftips=0 WHERE chatHiContact_iftips=1 AND id=".$uid);
			$db->SendTip($uid,'收到访客认证请求',dataIO($content.' <a href='.Href('cert').' class=aQING>去认证</a>','in',1000),'sys');
		}
		json_exit(array('flag'=>1,'msg'=>'提醒成功'));
	}	
	//adm
	if($submitok=='ajax_adm' && $cook_ifadm==1){
		switch ($kind) {
			case 'top':
				$db->query("UPDATE ".__TBL_USER__." SET refresh_time=".ADDTIME." WHERE id=".$uid);
				json_exit(array('flag'=>1,'msg'=>'会员【置顶】成功!'));
			break;
			case 'bottom':
				$limt = 31;
				$rowa = $db->ROW(__TBL_USER__,"refresh_time,id","photo_s<>'' AND photo_f=1 ORDER BY refresh_time DESC LIMIT $limt,1");
				$refresh_time = abs(intval($rowa[0]-cdstr(5)));
				$db->query("UPDATE ".__TBL_USER__." SET refresh_time=".$refresh_time." WHERE id=".$uid);
				json_exit(array('flag'=>1,'msg'=>'会员【置底】成功!'));
			break;
			case 'flag_2':
				$db->query("UPDATE ".__TBL_USER__." SET flag=-2 WHERE id=".$uid);
				json_exit(array('flag'=>1,'msg'=>'会员【隐藏】成功!'));
			break;
			case 'flag_1':
				$db->query("UPDATE ".__TBL_USER__." SET flag=-1 WHERE id=".$uid);
				json_exit(array('flag'=>1,'msg'=>'会员【锁定】成功!'));
			break;
		}
	}
	//检查拉黑
	if (gzflag($cook_uid,$uid) == -1)json_exit(array('flag'=>0,'msg'=>'对方觉得你不太适合Ta，请求失败'));
	if($submitok=='ajax_photo_s_zoom')nophoto_s($cook_uid);
	if($submitok=='ajax_Plist_zoom')nophoto($cook_uid);
}
switch ($submitok) {
	case 'ajax_photo_s_zoom':
		nophoto_s($cook_uid);
	break;
	case 'ajax_Plist_zoom':
		nophoto($cook_uid);
	break;
	case 'ajax_gz1':
		if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'亲！操作自己有意义么'));
		$rowtx = $db->NUM($uid,"sex");if ($rowtx){$sex= $rowtx[0];}else{exit(JSON_ERROR);}
		if($cook_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能操作＾_＾'));
		if (gzflag($cook_uid,$uid) != -1){
			$returngz = gzflag($uid,$cook_uid);
			if ($returngz == -1){
				$db->query("UPDATE ".__TBL_GZ__." SET flag=1 WHERE uid=".$uid." AND senduid=".$cook_uid);
			}elseif($returngz == 0){
				$db->query("INSERT INTO ".__TBL_GZ__."(uid,senduid,px) VALUES ($uid,$cook_uid,".ADDTIME.")");
			}
		}
		json_exit(array('flag'=>1,'msg'=>'关注成功！'));
	break;
	case 'ajax_gz0':
		if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'亲！操作自己有意义么'));
		$rowtx = $db->NUM($uid,"sex");if ($rowtx){$sex= $rowtx[0];}else{exit(JSON_ERROR);}
		if($cook_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能操作＾_＾'));
		//
		$returngz = gzflag($uid,$cook_uid);
		if ($returngz == 1)$db->query("DELETE FROM ".__TBL_GZ__." WHERE uid=".$uid." AND senduid=".$cook_uid);
		json_exit(array('flag'=>1,'msg'=>'取消成功！'));
	break;
	case 'ajax_gz':
		if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'亲！关注自己有意义么'));
		$rowtx = $db->NUM($uid,"sex");if ($rowtx){$sex= $rowtx[0];}else{exit(JSON_ERROR);}
		if($cook_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能操作＾_＾'));
		
		//
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
		json_exit(array('flag'=>1));
	break;
	case 'ajax_senddzh':
		if ($uid == $cook_uid)json_exit(array('flag'=>'metome','msg'=>'亲！跟自己打招呼有意义么'));
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
					case 'sex':$row0 = $db->NUM($uid,"sex");if($row0[0]==$cook_sex && $cook_uid<>$uid)json_exit(array('flag'=>0,'msg'=>'同性不能打招呼＾_＾'));break;
					case 'photo':if($cook_photo_f!=1)json_exit(array('flag'=>'nophoto','msg'=>'请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％'));break;
					case 'mysex1':hi_ifsex();break;//发送方为男
					case 'mysex2':hi_ifsex();break;//发送方为女
					case 'vip':if($data_grade<2)json_exit(array('flag'=>'nolevel','msg'=>'只有VIP会员才可以打招呼＾_＾'));break;
				}
			}
		}
		//招招呼前提条件结束
		$rowtx = $db->NUM($uid,"sex,nickname,openid,subscribe");
		if ($rowtx){
			$sex= $rowtx[0];$nickname = trimhtml(dataIO($rowtx[1],'out'));$openid = $rowtx[2];$subscribe = $rowtx[3];
		}else{exit(JSON_ERROR);}
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
			
							//站内消息
							//$C = $nickname.'您好，有人给你打招呼！　<a href="'.Href('tz').'" class="aQING" target="_blank">进入查看</a>';
							//$db->SendTip($uid,"有人给你打招呼",dataIO($C,'in'),'sys');
				
			//微信模版
			if (!empty($openid) && $subscribe==1){
				$first  = urlencode($nickname."您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 有人给你打招呼");
				$remark = urlencode("点击进入查看");
				@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$dzh_content.'&nickname='.$data_nickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('tz')));
			}
			
		}
		json_exit(array('flag'=>1));
	break;
	case 'ajax_gift_div':
		if(!ifint($gid) && $gid!=0)exit(JSON_ERROR);
		$SQL = ($gid == 0)?" ORDER BY rand() LIMIT 1":" WHERE id=".$gid;
		$rt=$db->query("SELECT id,title,price,picurl FROM ".__TBL_GIFT__.$SQL);
		if ($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'name');
			$row['title']  = dataIO($row['title'],'out');
			$row['picurl'] = $_ZEAI['up2'].'/'.$row['picurl'];
			$G = $row;
		}else{json_exit(array('flag'=>0));}
		$U = $db->NAME($uid,"nickname,sex");
		if ($U){
			$U['nickname'] = dataIO($U['nickname'],'out');
		}else{json_exit(array('flag'=>0));}
		$GU = array_merge($G,$U);
		json_exit($GU);
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
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$openid.'&num='.$price.'&first='.$F.'&content='.$C.'&url='.urlencode(HOST.'/?z=my&e=my_gift'));
				}
				//
				//$rt=$db->query("SELECT b.id,b.picurl,COUNT(*) AS num FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b WHERE a.uid=".$uid." AND a.gid=b.id GROUP BY a.gid ORDER BY a.id DESC LIMIT 3");
				$SQLgift2 = ",(SELECT MAX(id) AS max_id FROM ".__TBL_GIFT_USER__." WHERE uid=".$uid." GROUP BY gid) C";
				$rt=$db->query("SELECT B.id,B.picurl FROM ".__TBL_GIFT_USER__." A,".__TBL_GIFT__." B ".$SQLgift2." WHERE A.id=max_id AND A.uid=".$uid." AND A.gid=B.id GROUP BY A.gid ORDER BY A.id DESC LIMIT 3");
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
}
if ($uid != $cook_uid && $cook_ifadm!=1 && $_VIP['YKviewU'] != 1){
	//会员浏览个人主页
	$viewhomepage_data = explode(',',$_VIP['viewhomepage_data']);
	if(count($viewhomepage_data)>0 && is_array($viewhomepage_data)){
		foreach ($viewhomepage_data as $V){
			switch ($V) {
				case 'rz_mob':
					if(!in_array('mob',$cook_RZarr)){
						if($m=='wap'){
							alert('请您先进行【'.rz_data_info('mob','title').'】<br>＾_＾',mHref('cert'));
						}else{
							json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('mob','title').'】<br>＾_＾'));
						}
					}
				break;
				case 'rz_identity':
					if(!in_array('identity',$cook_RZarr)){
						if($m=='wap'){
							alert('请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％',mHref('cert'));
						}else{
							json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％'));
						}
					}
				break;
				case 'rz_photo':
					if(!in_array('photo',$cook_RZarr)){
						if($m=='wap'){
							alert('请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％',mHref('cert'));
						}else{
							json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％'));
						}
					}
				break;
				case 'bfb':
					$config_bfb = intval($_VIP['viewhomepage_bfb_num']);
					if($cook_myinfobfb < $config_bfb){
						if($m=='wap'){
							alert('请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％',mHref('my_info'));
						}else{
							json_exit(array('flag'=>'nodata','msg'=>'请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％'));
						}
					}
				break;
				case 'sex':
					$row0 = $db->NUM($uid,"sex");
					if($row0[0]==$cook_sex && $cook_uid<>$uid){
						if($m=='wap'){
							alert('同性不能浏览会员主页＾_＾',HOST);
						}else{
							json_exit(array('flag'=>0,'msg'=>'同性不能浏览会员主页＾_＾'));	
						}
					}
				break;
				case 'photo':
					if($cook_photo_f!=1){
						if($m=='wap'){
							alert('请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％',mHref('my_info'));
						}else{
							json_exit(array('flag'=>'nophoto','msg'=>'请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％'));	
						}
					}
				break;
			}
		}
	}
}
$switch = json_decode($_ZEAI['switch'],true);
$extifshow = json_decode($_UDATA['extifshow'],true);
$mate_diy = explode(',',$_ZEAI['mate_diy']);
$fields  = "ifadm,chatHiContact_iftips,flag,dataflag,id,uname,nickname,sex,grade,photo_s,photo_f,RZ,myinfobfb,kind,mob,mob_ifshow,weixin,weixin_pic,qq,photo_ifshow,xqk_ifshow,parent,regtime,endtime,admid,ifViewPush,openid,subscribe";//regtime,endtime,regip,endip,click
$fields .= ",aboutus,birthday,areatitle,love,heigh,weigh,edu,pay,house,car,nation,area2title,child,blood,tag,marrytype,marrytime,job";
if (@count($extifshow) >0 && is_array($extifshow)){foreach ($extifshow as $ev){$evARR[] = $ev['f'];}$fields .= ",".implode(",",$evARR);}
$fields .= ",mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2,mate_other";
$row = $db->ROW(__TBL_USER__,$fields,"(flag=1 OR flag=-2) AND id=".$uid);
if ($row){
	$Uifadm     = $row['ifadm'];
	$chatHiContact_iftips=$row['chatHiContact_iftips'];
	$flag       = $row['flag'];
	$openid     = $row['openid'];
	$subscribe  = $row['subscribe'];
	$ifViewPush = intval($row['ifViewPush']);
	$uname      = trimhtml(dataIO($row['uname'],'out'));
	$nickname   = trimhtml(dataIO($row['nickname'],'out'));
	$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
	$sex        = $row['sex'];
	$dataflag   = $row['dataflag'];
	$grade      = $row['grade'];
	$admid      = intval($row['admid']);
	$Smode_g    = 'g_'.$grade;
	$photo_s    = $row['photo_s'];
	$photo_f    = $row['photo_f'];
	$RZ = $row['RZ'];
	$myinfobfb  = $row['myinfobfb'];
	$regtime    = YmdHis($row['regtime']);
	//$regip      = $row['regip'];
	$endtime    = YmdHis($row['endtime']);
	//$endip      = $row['endip'];
	//$click      = $row['click'];
	$mob      = trimhtml(dataIO($row['mob'],'out'));
	$weixin   = trimhtml(dataIO($row['weixin'],'out'));
	$qq   = trimhtml(dataIO($row['qq'],'out'));
	$weixin_pic = $row['weixin_pic'];
	$mob_ifshow = $row['mob_ifshow'];
	$ifcontact=(!empty($weixin)  || !empty($qq) || !empty($weixin_pic) || (ifmob($mob) && $mob_ifshow==1)  )?true:false;//ifmob($mob) || 
	$Ukind      = $row['kind'];
	$aboutus    = dataIO($row['aboutus'],'out');
	$aboutus    =($dataflag==1)?$aboutus:'审核中';
	$birthday   = $row['birthday'];
	$birthday   = (!ifdate($birthday))?'':$birthday;
	$areatitle  = dataIO($row['areatitle'],'out');
	$area2title  = dataIO($row['area2title'],'out');
	$heigh      = $row['heigh'];
	$weigh      = $row['weigh'];
	$love       = $row['love'];
	$child      = $row['child'];
	$marrytime  = $row['marrytime'];
	$edu        = $row['edu'];
	$pay        = $row['pay'];
	$job        = $row['job'];
	$house      = $row['house'];
	$car        = $row['car'];
	$photo_ifshow= $row['photo_ifshow'];
	$xqk_ifshow= $row['xqk_ifshow'];
	$parent     = $row['parent'];
	if($Uifadm==1){
		$parent_str = '<div class="bangadm">工作人员</div>';
	}else{
		switch ($parent) {
			case 2:$parent_str = '<div class="bang">父母帮征婚</div>';break;
			case 3:$parent_str = '<div class="bang">亲友帮征婚</div>';break;
			default:$parent_str='<div class="bang">本人征婚</div>';break;
		}
	}
	if($Ukind==2)$parent_str='<div class="bang">线下会员</div>';
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
					$mate_li_out.='<li>'.$mate_str.'</li>';
				}
			}
		}
		$mate_li_out .= (!empty($mate_other))?'<li>'.$mate_other.'</li>':'';
	}
	$mate_areaid    = explode(',',$mate_areaid);
	$RZarr=explode(',',$RZ);
}else{
	if($m=='wap'){
		alert('会员不存在或已经服务成功','-1');
	}else{
		json_exit(array('flag'=>0,'msg'=>'会员不存在或已经服务成功'));
	}
}
$db->query("UPDATE ".__TBL_USER__." SET click=click+1 WHERE id=".$uid);
$nickname_str = (!empty($nickname))?$nickname:$uname;
if (!empty($photo_s) && $photo_f == 1){
	$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
	$photo_m_url = smb($photo_s_url,'m');
	$photo_b_url = smb($photo_s_url,'b');
	$photo_blur_url = smb($photo_s_url,'blur');
	$photo_s_str = '<img src="'.$photo_s_url.'">';
	$photo_m_str = '<img src="'.$photo_m_url.'">';
	$Parr = @getimagesize($photo_m_url);$w = $Parr[0];$h = $Parr[1];
}else{
	$photo_s_url = HOST.'/res/photo_m'.$sex.'.png';
	$photo_s_str = '<img src="'.$photo_s_url.'">';
	$photo_blur_url = HOST.'/m1/img/photo_blur.jpg';
	$photo_m_str = '<img src="'.HOST.'/res/photo_m'.$sex.'.png">';
}
$sex_str = ($sex == 1)?'他':'她';
$sex_str2 = ($sex == 1)?'男':'女';
$gzclass='';$gz_str='关注';
$inblackclass = '';
$ifhiher=true;
if (ifint($cook_uid)){
	$row = $db->ROW(__TBL_USER__,"sex,grade,areaid,areatitle,ifadm","id=".$cook_uid);
	if ($row){
		$cook_sex  = $row[0];
		$cook_grade= $row[1];
		$areaid    = $row[2];
		$robot=json_decode($_ZEAI['robot'],true);
		if($Ukind==4 && $robot['areaupdate']==1 && !empty($areaid)){
			$areatitle= $row[3];
			$db->query("UPDATE ".__TBL_USER__." SET areaid='$areaid',areatitle='$areatitle' WHERE id=".$uid);
		}
		$ifadm = $row[4];
	}
	//if($cook_sex==$sex)json_exit(array('flag'=>0,'msg'=>'同性不能浏览＾_＾'));
	if (gzflag($uid,$cook_uid) == 1){
		$gzclass='class="ed"';
		$gz_str='已关注';
	}
	$inblackclass = (gzflag($uid,$cook_uid) == -1)?' class="ed"':'';
	$ifhiher = ($db->COUNT(__TBL_TIP__," senduid=".$cook_uid." AND uid=".$uid." AND kind=3") > 0)?false:true;
}
//
$ifblur=0;$lockstr='';
if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
	$photo_s_url = smb($photo_s_url,'blur');
	$photo_m_url = $photo_s_url;
	$photo_b_url = $photo_s_url;
	$photo_s_str = '<img src="'.$photo_s_url.'" style="width:100%;height:100%">';
	$photo_m_str = '<img src="'.$photo_m_url.'">';
	$ifblur=1;
	$lockstr = '<div class="lockstr"><i class="ico lockico">&#xe61e;</i>'.dataIO($switch['grade1LockBlurT'],'out').'</div>';
}
if($photo_ifshow==0 && $ifblur==0){
	$photo_s_url = smb($photo_s_url,'blur');
	$photo_m_url =HOST.'/res/photo_m'.$sex.'_hide.png';
	$photo_b_url = $photo_s_url;
	$photo_s_str = '<img src="'.$photo_s_url.'" style="width:100%;height:100%">';
	$photo_m_str = '<img src="'.$photo_m_url.'">';
	$lockstr = '';
}
//
$area_s_title = explode(' ',$areatitle);
$area_s_title1 = $area_s_title[1];
$area_s_title2 = $area_s_title[2];
$area_s_title  = $area_s_title1.$area_s_title2;

$area_s_title2 = explode(' ',$area2title);
$area_s_title2 = $area_s_title2[2].$area_s_title2[3];
if (str_len($aboutus)>70){
	$aboutusall= $aboutus;
	$aboutus   = gylsubstr($aboutus,35,0,'utf-8',true).'　<font class="C666">查看更多</font>';
	$ifabutall = true;
}else{
	$ifabutall = false;
}
//扫黑除恶
$chcis=$switch['chatHiContact_ifshow'];$chcis_if=true;
$chcit=intval($switch['chatHiContact_iftips']);
if(!empty($chcis) && @is_array($chcis) && @count($chcis)>0){
	if(@in_array('mob',$chcis) && !in_array('mob',$RZarr))$chcis_if=false;
	if(@in_array('identity',$chcis) && !in_array('identity',$RZarr))$chcis_if=false;
	if(@in_array('photo',$chcis) && !in_array('photo',$RZarr))$chcis_if=false;
}
//访问记录
$shuaxin_homeclick = 'homeclick'.$uid.$cook_uid;
if ( $cook_uid != $uid && ifint($cook_uid) && $_COOKIE["$shuaxin_homeclick"] != 'wwwZEAIcn' && $ifadm!=1) {
	$vnum = $db->COUNT(__TBL_CLICKHISTORY__,"uid=".$uid." AND senduid=".$cook_uid);
	if ($vnum == 0 && $admid<>$cook_uid){
		$db->query("INSERT INTO ".__TBL_CLICKHISTORY__."  (uid,senduid,addtime) VALUES ($uid,$cook_uid,".ADDTIME.")");
		$ifViewPushsex_=true;
		if($_VIP['ifViewPushsex']==1 && $sex==2){
			$ifViewPushsex_=false;
		}elseif($_VIP['ifViewPushsex']==2 && $sex==1){
			$ifViewPushsex_=false;
		}
		if ($ifViewPush==1 && !empty($openid) && $subscribe==1 && $ifViewPushsex_ && $cook_sex!=$sex){
			$mbnickname = urlencode($cook_nickname);
			$first  = urlencode("您好，在 ".date("Y-m-d H:i:s",ADDTIME)." 收到新访客！");
			$remark = urlencode("会员【".$mbnickname." UID:".$cook_uid."】正在浏览您的个人资料，缘份就在一瞬间，赶快看看TA的资料，好就别错过啦！");
			$mbcontent = '通知';
			@wx_mb_sent('mbbh=ZEAI_MSG_CHAT&openid='.$openid.'&content='.$mbcontent.'&nickname='.$mbnickname.'&first='.$first.'&remark='.$remark.'&url='.urlencode(wHref('u',$cook_uid)));
		}
	}else{
		$db->query("UPDATE ".__TBL_CLICKHISTORY__." SET addtime=".ADDTIME." WHERE uid=".$uid." AND senduid=".$cook_uid);
	}
	setcookie("$shuaxin_homeclick",'wwwZEAIcn',time()+7200000,"/",$_ZEAI['CookDomain']);
}
$sext=($sex==1)?'【帅哥求脱单】':'【美女求脱单】';
$areatitleArr = explode(' ',$areatitle);
$areatitle2=$areatitleArr[1];
$areatitle3=$areatitleArr[2];
$areatitle3=(empty($areatitle3))?$areatitle2:$areatitle3;
$age=getage($birthday);
$age_str=($age>0)?$age.'岁':'';
$share_u_title = $sext.$nickname.'.'.$age_str.'.'.$areatitle3;
$share_u_desc  = trimhtml($_INDEX['indexContent']);	
if($m=='wap'){
	if(!is_mobile())header("Location: ".Href('u',$uid));
	$headertitle = $share_u_title;
	require_once ZEAI.'m1/header.php';
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<?php echo '<script>ZEAI_MAIN="u";</script><div id="u" class="wap_u huadong">';
	$uback=' onclick="zeai.back()"';?>
    <script>
	wx.config({debug:false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
    </script>
<?php }?>
<link href="<?php echo HOST;?>/m1/css/u.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php if($_ZEAI['mob_mbkind']==3){?><link href="<?php echo HOST;?>/m3/css/u.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" /><?php }?>
<style>.u .bg{background:url("<?php echo $photo_blur_url; ?>")top center / 100% no-repeat;background-size:cover}</style>
<i class='ico goback Ugoback' id='ZEAIGOBACK-u'<?php echo $uback;?>>&#xe602;</i>
<?php
if ($flag == -2){?>
	<?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
    <div class="photo_hidebox">
        <img src="<?php echo HOST.'/res/photo_m'.$sex.'_hide.png';?>" class="photo_m_hide" />
        <h4>UID：<?php echo $uid;?></h4>
        <h3>此用户已隐藏，加客服联系Ta</h3>
        <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>" class="ewm"><font>长按或扫码加客服微信</font><?php }?>
        <?php if (!empty($kf_tel)){?><a href="tel:<?php echo $kf_tel;?>"><i class="ico">&#xe60e;</i> <?php echo $kf_tel;?></a><?php }else{?>
        <?php if (!empty($kf_mob)){?><a href="tel:<?php echo $kf_mob;?>"><i class="ico">&#xe60e;</i> <?php echo $kf_mob;?></a><?php }}?>
    </div>
<?php exit;}?>
<div class="Ublack" id="Ublack"<?php if ($cook_uid == $uid && ifint($cook_uid) && $ifmy==1)echo' style="display:none"';?>><i class="ico">&#xe609;</i></div>
<div id="loop_TB"><a href="#top" class="photo_s"><i class="ico loop_topbtm2">&#xe60a;</i><p class="loop_topbtm"><?php echo $photo_s_str; ?></p></a></div>
<?php if ($cook_uid == $uid && ifint($cook_uid) && $ifmy==1){?>
<div class="myedit" onClick="page({g:HOST+'/m1/my_info'+zeai.extname,y:'u',l:'my_info'})">编辑资料</div>
<?php }?>
<?php if ($ifadm == 1 && ifint($cook_uid)){?>
<div class="u_ifadmbox">
    <div class="btn size3 HONG2" style="width:21%" onClick="ZeaiM.page.load(HOST+'/m1/my_card.php?uid=<?php echo $uid;?>','u','my_card');">相亲卡</div><button type="button" class="btn size3 LV" onclick="admuFn('top',<?php echo $uid;?>)">置顶</button><button type="button" class="btn size3 QING2" onclick="admuFn('bottom',<?php echo $uid;?>)">置底</button><button type="button" class="btn size3 LAN" onclick="admuFn('flag_2',<?php echo $uid;?>)">隐藏</button><button type="button" class="btn size3 HUI" onclick="admuFn('flag_1',<?php echo $uid;?>)">封号</button>
	<?php if (!empty($mob)){?><div class="admtel"><a href="tel:<?php echo $mob;?>"><i class="ico S18" style="display:inline-block">&#xe60e;</i> <?php echo $mob;?></a></div><?php }?>
	<?php if (!empty($weixin)){?><div class="admwx"><i class="ico S18" style="display:inline-block">&#xe607;</i> <?php echo $weixin;?></div><?php }?>
	<?php if (!empty($weixin_pic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$weixin_pic;?>" onClick="ZeaiM.piczoom('<?php echo $_ZEAI['up2'].'/'.$weixin_pic;?>');"><?php }?>
</div>
<?php }?>
<div class="submain u" id="Ubox">
	<div class="bg"></div>
    <em id="rtbox"><a<?php echo $inblackclass; ?>>拉黑</a><a>举报</a></em>
    <header>
        <div class="photo_m" id="photo_m"><?php echo $photo_m_str.$parent_str;?></div>
        <div id="gz"<?php echo $gzclass; ?>><?php echo $gz_str;?></div>
        <div class="h2 "><?php echo uicon($sex.$grade,2).$nickname; ?> <font class="S12">（<?php echo utitle($grade);?>）</font></div>
        <div id="ucert"><?php echo RZ_html($RZ,'m','allcolor');?></div>
    </header>
    <?php
	if ($chcit == 1 && !$chcis_if && $Ukind!=2 && $cook_uid != $uid && ifint($cook_uid)){//$chatHiContact_iftips==1
		$chcit_listr=(@in_array('mob',$chcis) && !in_array('mob',$RZarr))?'【'.RZtitle('mob').'】':'';
		if(empty($chcit_listr))$chcit_listr.=(@in_array('identity',$chcis) && !in_array('identity',$RZarr))?'【'.RZtitle('identity').'】':'';
		if(empty($chcit_listr))$chcit_listr.=(@in_array('photo',$chcis) && !in_array('photo',$RZarr))?'【'.RZtitle('photo').'】':'';?>
        <div class="chcit">此用户没有完成<?php echo $chcit_listr;?>请谨慎联系　<button id="chcit">提醒对方认证</button></div><?php
	}
    if(@in_array('gift',$navarr) && $Ukind!=2){?>
    <div class="ugift ubox">
    	<?php $gifnum=$db->COUNT(__TBL_GIFT_USER__,"uid=".$uid); ?>
        <ul id="gift">
        <li gid="0" uid="<?php echo $uid; ?>"><i class="ico">&#xe69a;</i><?php if ($gifnum>0){?><font>收到<?php echo $gifnum;?>个</font><?php }?></li>
        <?php 
		$SQLgift2 = ",(SELECT MAX(id) AS max_id FROM ".__TBL_GIFT_USER__." WHERE uid=".$uid." GROUP BY gid) C";
        $rt=$db->query("SELECT B.id,B.picurl FROM ".__TBL_GIFT_USER__." A,".__TBL_GIFT__." B ".$SQLgift2." WHERE A.id=max_id AND A.uid=".$uid." AND A.gid=B.id GROUP BY A.gid ORDER BY A.id DESC LIMIT 3");
        $total = $db->num_rows($rt);
        if ($total==0){
            echo '<em>缘分，从送第一份礼物开始认识！</em>';
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
    </div>
    <div class="clear"></div>
    <?php }?>
    
	<?php
    //相册
    $rt=$db->query("SELECT path_s FROM ".__TBL_PHOTO__." WHERE uid=".$uid." AND flag=1 ORDER BY id DESC LIMIT 8");
    $photo_total = $db->num_rows($rt);
    if ($photo_total>0) {
        echo '<div class="Plist" id="Plist"><div class="box">';
		$pic_list = array();
        for($i=1;$i<=$photo_total;$i++) {
            $rows = $db->fetch_array($rt);
            if(!$rows) break;
            $path_s   = $rows[0];
            $dst_s    = $_ZEAI['up2'].'/'.$path_s;
            $dst_b    = smb($dst_s,'b');
            $pic_list[]= $dst_b;
			$blursty=($ifblur==1)?' class="blur"':'';
			if($photo_ifshow==0)$dst_s=HOST.'/res/photo_m'.$sex.'_hide.png';
            echo '<img src="'.$dst_s.'"'.$blursty.'>';
        }
        $pic_list = encode_json($pic_list);
        echo '</div>';
		if($ifblur==1)echo $lockstr;
        echo '</div>';
    }
    ?>
    <div class="ubox udata2">
		<h3 id="mydata">个人资料<font>（UID:<?php echo $uid; ?>）</font></h3>
        <ul>
            <li><?php echo $sex_str2;?></li>
            <?php if (!empty($birthday) && $birthday!='0000-00-00'){?><li><?php echo getage($birthday);?>岁</li><?php }?>
            <?php if (!empty($birthday) && $birthday!='0000-00-00'){?>
                <li>属<?php echo getbirthpet($birthday);?></li>
            	<li><?php echo getstar($birthday);?></li>
            <?php }?>
            <?php if (!empty($love)){?><li><?php echo udata('love',$love);?><?php if (!empty($child)){?>(<?php echo udata('child',$child);?>)<?php }?></li><?php }?>
        	<?php if (!empty($areatitle)){?><li>在<?php echo $area_s_title;?>工作</li><?php }?>
            <?php if (!empty($area2title)){?><li>户籍：<?php echo $area_s_title2;?></li><?php }?>
			<?php if (!empty($heigh)){?> <li><?php echo udata('heigh',$heigh);?></span></li><?php }?>
			<?php if (!empty($weigh)){?> <li><?php echo udata('weigh',$weigh);?></span></li><?php }?>
            <?php if (!empty($edu)){?><li>学历<?php echo udata('edu',$edu);?></li><?php }?>
            <?php if (!empty($pay)){?><li>月收入<?php echo udata('pay',$pay);?></li><?php }?>
            <?php if (!empty($job)){?><li><?php echo udata('job',$job);?></li><?php }?>
            <?php if (!empty($house)){?><li><?php echo udata('house',$house);?></li><?php }?>
            <?php if (!empty($car)){?><li><?php echo udata('car',$car);?></li><?php }?>
            <?php $marrytime_str=udata('marrytime',$marrytime);if (!empty($marrytime) && $marrytime_str!='不限'){?><li>期望<?php echo $marrytime_str;?>结婚</li><?php }?>
            <li class="more" id="mydatamore">查看更多</li>
        </ul>
    </div>
    <?php if($Ukind==2){?>
    <div class="uaboutus ubox" onclick="hnFn();">
		<h3>联系方式</h3><div class="contacthnC"><a href="javascript:;">点击联系专属红娘为您牵线</a></div>
    </div>
	<?php }?>
    
    
    
    
    <?php if($cook_uid==0.8){?>
    <?php 
	$_ZEAI['smsPAYnum']=0.01;
	?>
	<style>
    #smsBox{display:none;padding:0;width:100%;overflow:hidden}
	#smsBox .smsTips{font-size:14px;widht:90%;margin:20px auto}
	#smsBox img{width:100%;display:block;margin:0 auto}
    </style>
    <div class="uaboutus ubox" onclick="smsFn();">
		<h3>给Ta发送手机短信</h3><div class="contacthnC"><a href="javascript:;">点击联系专属红娘为您牵线</a></div>
    </div>
    <div id="smsBox" class="my-subscribe_box">
    	<img src="/res/smsTips.jpg" />
    	<div class="smsTips">支付￥<?php echo $_ZEAI['smsPAYnum'];?>元，我将为您人工发送短信</div>
        <button type="button" class="W90_ yuan btn size3 HONG2" id="smsPAYbtn">开始支付</button>
    </div>
	<script>
	function smsFn(){
		ZeaiM.div({obj:smsBox,w:280,h:300});
	}
	smsPAYbtn.onclick = function(){
		var money = <?php echo $_ZEAI['smsPAYnum'];?>,return_url='<?php echo $share_u_link;?>',jumpurl='<?php echo $share_u_link;?>'
		zeai_PAY({money:money,paykind:'wxpay',kind:10,tmpid:<?php echo $uid;?>,title:'【发送手机短信】',return_url:return_url,jumpurl:jumpurl});
	}
    </script>
    <script src="<?php echo HOST;?>/api/zeai_PAY.js?<?php echo $_ZEAI['cache_str'];?>"></script>
	<?php }?>
     
     
     
    
	<?php
	$urolenew = json_decode($_ZEAI['urole'],true);
	$newarr=array();foreach($urolenew as $RV){if($RV['f']==1){$newarr[]=$RV;}else{continue;}}
	$newarr=encode_json($newarr);
	$urole = json_decode($newarr);
	$contact_daylooknum = json_decode($_VIP['contact_daylooknum']);
	$contact_loveb   = json_decode($_VIP['contact_loveb']);
	$chat_daylooknum = json_decode($_VIP['chat_daylooknum']);
	$chat_loveb      = json_decode($_VIP['chat_loveb']);
	//联系方法
	$ARR = json_decode($_VIP['contact_daylooknum'],true);
	$ifShowContact=false;
	if (count($ARR) >= 1 && is_array($ARR) && max($ARR)>0 ){
		$ifShowContact=true;
	}
	if($switch['Smode'][$Smode_g] == 1 && $Ukind!=2 ){// && $chcis_if
		$ifShowContact=true;
	}else{
		$ifShowContact=false;
	}
	if($ifShowContact && $ifcontact && in_array('contact',$navarr)){?>
        <div class="contact ubox">
            <h3 id="mycontact">联系方法</h3>
            <ul id="mycontactmore">
                <?php if (ifmob($mob) && $mob_ifshow){?><li><i class="ico mob">&#xe627;</i><h5><?php echo substr($mob,0,3).'*****'.substr($mob,-3,3);?><br>点击查看</h5></li><?php }?>
                <?php if (!empty($weixin)){?><li><i class="ico weixin">&#xe607;</i><h5><?php echo substr($weixin,0,1).'****'.substr($weixin,-2,2);?><br>点击查看</h5></li><?php }?>
                <?php if (!empty($qq)){?><li><i class="ico qq">&#xe612;</i><h5><?php echo substr($qq,0,1).'****'.substr($qq,-2,2);?><br>点击查看</h5></li><?php }?>
                <?php if (!empty($weixin_pic)){?><li><i class="ico weixin_pic">&#xe611;</i><h5>微信二维码<br>点击查看</h5></li><?php }?>
            </ul>
        </div>
        
        <?php if ( ifint($cook_uid)){?>
        <div id="u_contact_daylooknumHelp" class="helpDiv">
            <ul>
            <?php
            foreach ($urole as $uv) {
                $grade = $uv->g;
                $title = $uv->t;
                $num   = $contact_daylooknum->$grade;
                $num_str = ($num>0)?' 每天能看<font class="Cf00">'.$num.'</font>人':' 无权查看';
				$ifmy = ($cook_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
                $outZ .= '<li>'.uicon($cook_sex.$grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
            }echo $outZ;
            ?>
            </ul>
            <a class="btn size3 HUANG Mcenter block center" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent('<?php echo HOST.'/?z=index&e=u&a='.$uid;?>'),'u','my_vip');">升级VIP，畅通无极限</a>
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
                $outE .= '<li>'.uicon($cook_sex.$grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
            }echo $outE;
			//单次$myclkB解锁
            ?>
            </ul>
            <a class="btn size3 HONG W50_" onClick="clickloveb('contact','u')">立即解锁</a>
            <a class="btn size3 HUANG W50_" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent('<?php echo HOST.'/?z=index&e=u&a='.$uid;?>'),'u','my_vip');">升级VIP会员</a>
        </div>
        <?php }?>
    <?php }	
	//联系方法结束?>
    
    <?php if($ifmate){?>
    <div class="ubox umate">
		<h3>择偶条件</h3>
        <ul><?php echo  str_replace("不限","",$mate_li_out);;?></ul>
    </div>
    <?php }?>

    <?php if (str_len($aboutus)>1){ ?>
    <div class="uaboutus ubox" id="btn_aboutus">
		<h3>自我介绍</h3><div class="aboutusC"><?php echo $aboutus; ?></div>
    </div>
    <?php }if($Ukind!=2){?>

    <div class="uaboutus ubox" <?php if ($cook_grade <=1){?>onclick="uvip();"<?php }?>>
		<h3>最近登录</h3><div class="aboutusC" style="width:100%">
        <?php if ($cook_grade > 1 && ifint($cook_uid)){?>
    	注册会员时间：<?php echo $regtime;?><br>
        最近登录时间：<?php echo $endtime;?>
        <?php }else{ ?>
    	注册会员时间：VIP会员可见 <i class="ico">&#xe62d;</i> <span style="color:#FF6F6F">点击开通VIP</span><br>
        最近登录时间：VIP会员可见 <i class="ico">&#xe62d;</i> <span style="color:#FF6F6F">点击开通VIP</span>
        <?php }?>  
        </div>
    </div>
	<?php
	}
	if(ifint($cook_uid)){
		$uroleVIP = json_decode($_ZEAI['urole'],true);
		rsort($uroleVIP);$maxkey=max($uroleVIP);$maxg= $maxkey['g'];	
	}
	if($cook_grade<$maxg && $Ukind!=2){// && $chcis_if?>
    <div class="ubox" id="btn_btmvip">
		<div class="vipbox">
        	<span class="vipico ico">&#xe621;</span>
        	<span class="viptitle">自由沟通-飞速脱单</span>
        	<span class="vipc">相亲成功率提升300%</span>
            <span class="vipbtn" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent('<?php echo HOST.'/?z=index&e=u&a='.$uid;?>'),'u','my_vip');">立即开通VIP</span>
        </div>
    </div>
	<?php }?>

	<?php 
    $rowf = $db->ROW(__TBL_CRM_HN__,"ifwebshow,path_s","flag=1 AND ewm<>'' AND id=".$admid,'name');
	if ($rowf){
		$ifwebshow = $rowf['ifwebshow'];
		$path_s = $rowf['path_s'];
		$path_s_url=(!empty($path_s))?'<img src="'.$_ZEAI['up2'].'/'.$path_s.'">':'&#xe621;';
		if($ifwebshow==0){
			$admid=0;
			$path_s_url='&#xe621;';
		}
	}else{
		$admid=0;
		$path_s_url='&#xe621;';
	}
	$SQL = ($admid>0)?" AND id=".$admid:" ORDER BY rand() LIMIT 1";
	$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');
	$rowf = $db->ROW(__TBL_CRM_HN__,"id,truename,ewm,mob,path_s","ifwebshow=1 AND flag=1 AND ewm<>'' ".$SQL,'name');
	if ($rowf){
		$hn_id         = $rowf['id'];
		$hn_truename   = '【'.dataIO($rowf['truename'],'out').'】';
		$hn_ewm        = $rowf['ewm'];
		$hn_mob        = $rowf['mob'];
		$path_s        = $rowf['path_s'];
		$path_s_url    =(!empty($path_s))?$_ZEAI['up2'].'/'.$path_s:HOST.'/res/photo_m.png';
		$hn_ewm_url    =(!empty($hn_ewm))?$_ZEAI['up2'].'/'.$hn_ewm:HOST.'/res/noP.gif';
	}else{
		$path_s = '';
		$path_s_url=HOST.'/res/photo_m.png';
		$hn_ewm_url=$_ZEAI['up2'].'/'.$kf_wxpic;
		$hn_mob    = $_ZEAI['kf_mob'];
	}
	if($Ukind==2){
		$path_s_url1   =(!empty($path_s))?'<img src="'.$_ZEAI['up2'].'/'.$path_s.'">':'&#xe621;';
	?>
    <div class="ubox offlineubox">
    <div class="offline" onclick="hnFn();">
        <span class="vipico ico"><?php echo $path_s_url1;?></span>
        <span class="viptitle">线下优质会员</span>
        <span class="vipc">享受专属红娘1对1牵线</span>
        <span class="vipbtn">联系红娘</span>
    </div></div>
    <?php }?>
    
    <!-- -->
    <?php if ( ifint($cook_uid)){?>
    <div id="u_chat_daylooknumHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_daylooknum->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> 人/天':' 无权聊天';
            $ifmy = ($cook_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
            //$outA .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
            $outA .= '<li>'.uicon($cook_sex.$grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outA;
        ?>
        </ul>
        <a class="btn size3 HUANG Mcenter block center" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent('<?php echo HOST.'/?z=index&e=u&a='.$uid;?>'),'u','my_vip');">升级VIP会员</a>
    </div>
    <div id="u_chat_lovebHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_loveb->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> '.$_ZEAI['loveB'].'/人':' 免费聊天';
            if($cook_grade==$grade){
                $ifmy = '　<font class="Cf00">（我）</font>';
                $myclkB=$num;
            }else{
                $ifmy = '';
            }
            $outI .= '<li>'.uicon($cook_sex.$grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outI;
		//单次$myclkB;解锁
        ?>
        </ul>
        <a class="btn size3 HONG W50_" onClick="clickloveb('chat','u')">立即解锁</a>
        <a class="btn size3 HUANG W50_" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent('<?php echo HOST.'/?z=index&e=u&a='.$uid;?>'),'u','my_vip');">升级VIP会员</a>
    </div>
    <?php }?>
    
    <div id="box_gift" class="box_gift">
        <em><img><h3></h3><h6></h6></em>
        <a href="javascript:;">看看其他礼物</a>
        <a href="javascript:;">确认赠送</a>
    </div>
	<?php if(@in_array('tg',$navarr) && $xqk_ifshow==1){?><div id="tgxqk" class="loop_s_b_s">推荐<br />朋友</div><?php }?>
</div>
<div class="btmbox">
    <?php if($switch['Smode'][$Smode_g] == 1 && $Ukind!=2 && in_array('chat',$navarr)){// && $chcis_if?><a id="chat"><i class="ico">&#xe623;</i></a><?php }?>
    <?php if ($ifhiher && in_array('hi',$navarr)&&$Ukind!=2){// && $chcis_if?><a id="hi"><i class="ico">&#xe8ca;</i></a><?php }?>
    <?php if(@in_array('hn',$navarr) && $Ukind!=2){?><a id="hn"><i class="ico">&#xe605;</i></a><?php }?>
</div>
<div id='tips0_100_0' class='tips0_100_0 alpha0_100_0'></div>
<div id="hnbox">
    <div class="u_hninfo">
         <div class="hntop">专属红娘<img src="<?php echo $path_s_url;?>" /></div>
        <div class="hnewmbox">
            <div class="hnewm"><img src="<?php echo $hn_ewm_url; ?>"><h5 class="C999">长按关注<?php echo $hn_truename;?>红娘微信</h5></div>
            <?php if (!empty($hn_mob)){ ?><a href="tel:<?php echo $hn_mob; ?>" class="hntel S18"><i class="ico">&#xe60e;</i><?php echo $hn_mob; ?></a><?php }?>
        </div>
	</div>
</div>
<script src="<?php echo HOST;?>/m1/js/u.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var uid=<?php echo $uid;?>,browser='<?php echo (is_weixin())?'wx':'h5';?>',lovebstr='<?php echo $_ZEAI['loveB'];?>';
sessionStorage.uid=<?php echo $uid;?>;
mydata.onclick=mydataFn;
mydatamore.onclick=mydataFn;
function uvip(){ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent('<?php echo HOST.'/?z=index&e=u&a='.$uid;?>'),'u','my_vip');}
<?php if (str_len($aboutus)>5){ ?>btn_aboutus.onclick=aboutusFn;<?php }?>
Ublack.onclick=UblackFn;
<?php if ($photo_total > 0 && $ifblur==0){?>
PlistFn(<?php echo $pic_list;?>);
<?php }if (!empty($photo_s) && $photo_f == 1){?>
var photo_b = '<?php echo $photo_b_url;?>';
photo_m.onclick=photo_mFn;
<?php }if (!empty($ii)){?>ZeaiM.page.load(HOST+'/m1/u_data'+zeai.ajxext+'uid=<?php echo $uid;?>&i=<?php echo $ii;?>','u','<?php echo $ii;?>');<?php }?>
gz.onclick=gzFn;
rtbox.children[0].onclick = inBlackFn;
rtbox.children[1].onclick = Fn315;
Ubox.onscroll=loop_TBfn1;
loop_TB.onclick=loop_TBfn2;
<?php if ($ifShowContact && $ifcontact && in_array('contact',$navarr)){?>mycontact.onclick=mycontactFn;mycontactmore.onclick=mycontactFn;<?php }?>
<?php if ($ifhiher && in_array('hi',$navarr)&&$Ukind!=2){//&& $chcis_if?>hi.onclick=hiFn;<?php }?>
<?php if($switch['Smode'][$Smode_g] == 1 && $Ukind!=2 && in_array('chat',$navarr)){// && $chcis_if?>
o('chat').onclick=function(){chatFn('u');}
<?php }?>
<?php if(@in_array('gift',$navarr)&&$Ukind!=2){?>setgift(gift,Ubox,box_gift);<?php }?>
<?php if (!empty($RZ)){?>ucert.onclick=ucertFn;<?php }?>
<?php if(@in_array('hn',$navarr) && $Ukind!=2){?>hn.onclick=hnFn;<?php }?>
if(!zeai.empty(o('chcit')))o('chcit').onclick=chcitFn;
<?php if (is_weixin()){
	$share_u_title = $share_u_title.'.'.$_ZEAI['siteName'];?>
	var share_u_title = '<?php echo $share_u_title; ?>',share_u_desc  = '<?php echo $share_u_desc; ?>',share_u_link  = '<?php echo $share_u_link; ?>',share_u_imgurl= '<?php echo $photo_s_url; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_u_title,desc:share_u_desc,link:share_u_link,imgUrl:share_u_imgurl});
		wx.onMenuShareTimeline({title:share_u_title,desc:share_u_desc,link:share_u_link,imgUrl:share_u_imgurl});
	});
<?php }?>
<?php if(@in_array('tg',$navarr) && $xqk_ifshow==1){?>tgxqk.onclick = tgxqkFn;<?php }?>
</script>
<?php if($m=='wap'){?></div><div id="Zeai_cn__PageBox"></div><div id="blankpage"></div></body></html>
<?php }//蹦图
$bounce=json_decode($_ZEAI['bounce'],true);
if($bounce['flag']['vipdatarz'] == 1 && ifint($cook_uid) && $cook_grade<=1 && !empty($bounce['vip_picurl']) && $Ukind!=2  ){
	$bouncev = '';
	$bounceTip = 'cook_my_bounce'.YmdHis(ADDTIME,'d');
	if($cook_grade<=1){ /*&& $_COOKIE[$bounceTip.'my_vip'] != 'my_vip'*/
		$bouncev=HOST.'/m1/my_vip';$url=HOST.'/m1/my_vip.php';$pageid='my_vip';$picurl=$_ZEAI['up2']."/".$bounce['vip_picurl'];
	}
	if(!empty($bouncev)){
		//$bounceTip = $bounceTip.$bouncev;
		//if($_COOKIE[$bounceTip] != $bouncev){
		//	setcookie($bounceTip,$bouncev,null,"/",$_ZEAI['CookDomain']);
			?>
			<script>var u_divclose;setTimeout(function(){u_divclose=ZeaiM.div_pic({fobj:o('u'),obj:u_bounce_box,w:320,h:360});},1000);</script>
			<div id="u_bounce_box" class="bounce_box bounce"><img style="width:100%;display:block" src="<?php echo $picurl;?>" onClick="u_divclose.click();ZeaiM.page.load('<?php echo $url;?>','u','<?php echo $pageid;?>');"></div>
			<?php
		//}
	}
}
//蹦图结束
ob_end_flush();  
?>
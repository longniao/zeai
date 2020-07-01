<?php
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_shop.php';
$TG_set = json_decode($_REG['TG_set'],true);
require_once ZEAI.'sub/TGfun.php';//$tg=json_decode($_REG['tg'],true);

//kind：1:升级,2:loveb充值,3:余额充值,4:提现
$rowpay = $db->ROW(__TBL_PAY__,"id,uid,kind,title,money,bz,money_list_id AS sj_grade,paytime AS sj_if2,tg_uid,paymoney","orderid='$orderid' AND flag=0 LIMIT 1",'num');
if ($rowpay){
	$payid      = $rowpay[0];
	$uid        = intval($rowpay[1]);
	$kind       = $rowpay[2];
	$orderid_title = $rowpay[3];
	$money      = $rowpay[4];
	$bz         = $rowpay[5];
	$sj_grade   = $rowpay[6];$fid = $rowpay[6];//活动detail
	$sj_if2     = $rowpay[7];
	$tg_uid     = $rowpay[8];
	$data_paymoney = $rowpay[9];
}else{exit;}
if($pay_money!=$data_paymoney)exit;
//
if($kind==5 || $kind==6 || $kind==11 || $kind==12){
	$maintbl=__TBL_TG_USER__;
	$mainfld="uname,mob,loveb,money,openid,grade,tguid,subscribe";
	$uid = $tg_uid;
}else{
	$maintbl=__TBL_USER__;
	$mainfld="nickname,loveb,money,openid,grade,if2,tguid,subscribe,sjtime,sex";
}
if(ifint($uid)){
	$row = $db->ROW($maintbl,$mainfld,"id=".$uid,"name");
	if(!$row){exit;}else{
		if($kind==5 || $kind==6 || $kind==11 || $kind==12){
			$data_uname = dataIO($row['uname'],'out');
			$data_mob = dataIO($row['mob'],'out');
			$data_nickname = (empty($data_uname))?$data_mob:$data_uname;
			$data_loveb = intval($row['loveb']);
			$data_money = $row['money'];
			$data_grade = $row['grade'];
			
		}else{
			$data_nickname = dataIO($row['nickname'],'out');
			$data_loveb = intval($row['loveb']);
			$data_money = $row['money'];
			$data_grade = $row['grade'];
			$data_if2   = $row['if2'];
			$data_sex   = $row['sex'];
		}
		$tguid      = $row['tguid'];
		$data_openid= $row['openid'];
		$data_subscribe= $row['subscribe'];
		$data_sjtime = $row['sjtime'];
	}
}
//
switch ($kind){
	//1:升级
	case 1:
		$grade  = intval($sj_grade);$if2 = intval($sj_if2);
		
		
/*		$sj_rmb1 = json_decode($_VIP['sj_rmb1'],true);
		$sj_rmb2 = json_decode($_VIP['sj_rmb2'],true);
		$price = ($data_sex==2)?$sj_rmb2[$grade.'_'.$if2]:$sj_rmb1[$grade.'_'.$if2];		
		if($pay_money!=$price)exit;
*/		
		
		
		if($data_grade==$grade){
			$d1  = ADDTIME;
			$d2  = $data_sjtime + $data_if2*30*86400;
			$ddiff = $d2-$d1;
			if ($ddiff < 0 && $data_if2 != 999){//过
				$SQL .= "if2=$if2,sjtime=".ADDTIME;
			}else{
				$if2new = $data_if2+$if2;
				$SQL .= "if2=$if2new";
			}
		}else{
			$SQL = "grade=$grade,if2=$if2,sjtime=".ADDTIME;
		}
		//
		$sj_loveb = json_decode($_VIP['sj_loveb'],true);
		$sj_loveb = intval($sj_loveb[$grade]);
		$sj_loveb_str = '';
		if($sj_loveb>0){
			$SQL .= ",loveb=loveb+$sj_loveb";
			$sj_loveb_str = '（赠送：'.$sj_loveb.$_ZEAI['loveB'].'）';
		}
		//
		$db->query("UPDATE ".__TBL_USER__." SET ".$SQL." WHERE grade<=$grade AND id=".$uid);
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',money_list_id=0,paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		if($sj_loveb>0){
			$db->AddLovebRmbList($uid,$orderid_title.$sj_loveb_str,$sj_loveb,'loveb',2);	
		}
		//站内通知
		$C = $data_nickname.'您好，恭喜你'.$orderid_title.'成功'.$sj_loveb_str.'　　<a href='.Href('my').' class=aQING>查看详情</a>';
		$db->SendTip($uid,'恭喜你，'.$orderid_title.'成功!',dataIO($C,'in'),'sys');
		//微信模版通知
		if (!empty($data_openid) && $data_subscribe==1){
			$keyword1 = '恭喜你'.$orderid_title.'成功!';
			$keyword3 = urlencode($_ZEAI['siteName']);
			$url      = urlencode(mHref('my'));
			$remark   = $sj_loveb_str;
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		//写支付日志
		gyl_log('｜'.$bz.' -> uid：'.$uid.'｜'.$orderid_title.'｜ ¥'.$pay_money);
		if(ifint($tguid) && @in_array('tg',$navarr) ){TG($tguid,$uid,'vip',$pay_money);}
	break;
	
	//loveb充值
	case 2:
		$addloveb   = $money*abs(intval($_ZEAI['loveBrate']));
		$endloveb   = $addloveb+$data_loveb;
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		$db->query("UPDATE ".__TBL_USER__." SET loveb=$endloveb WHERE id=".$uid);
		//爱豆清单入库
		$db->AddLovebRmbList($uid,$orderid_title,$addloveb,'loveb',2);	
		//爱豆站内消息
		$C = $data_nickname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！　　<a href='.Href('loveb').' class=aQING>查看详情</a>';
		$db->SendTip($uid,$orderid_title,dataIO($C,'in'),'sys');
		//爱豆到账提醒(微信模版)
		if (!empty($data_openid) && $data_subscribe==1){
			$first   = urlencode($data_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
			$content = urlencode($orderid_title);
			$url     = urlencode(mHref('loveb'));
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$addloveb.'&first='.$first.'&content='.$content.'&url='.$url);
		}
		//写支付日志
		gyl_log('｜'.$bz.' -> uid：'.$uid.'｜'.$orderid_title.'｜ ¥'.$pay_money.'｜充值'.$_ZEAI['loveB'].$addloveb.'个');
		if(ifint($tguid) && @in_array('tg',$navarr)){TG($tguid,$uid,'cz',$pay_money);}
		//
	break;
	
	//余额充值
	case 3:
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		$db->query("UPDATE ".__TBL_USER__." SET money=money+$money WHERE id=".$uid);
		//写支付日志
		gyl_log('｜'.$bz.' -> uid：'.$uid.'｜'.$orderid_title.'｜ ¥'.$money.'(实际：'.$pay_money.')');
		//余额清单入库
		$db->AddLovebRmbList($uid,$orderid_title,$money,'money',3);	
		//站内消息
		$C = $data_nickname.'您好，您有一笔资金到账！　　<a href='.Href('money').' class=aQING>查看详情</a>';
		$db->SendTip($uid,$orderid_title.'成功',dataIO($C,'in'),'sys');
		//到账提醒
		if (!empty($data_openid) && $data_subscribe==1){
			$first   = urlencode($data_nickname."您好，您有一笔资金到账！");
			$content = urlencode($orderid_title.'成功~~');
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$money.'&first='.$first.'&content='.$content.'&url='.urlencode(mHref('money')));
		}
		if(ifint($tguid) && @in_array('tg',$navarr)){TG($tguid,$uid,'cz',$pay_money);}
	break;
	//活动报名
	case 4:
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		$SQL="";
		if(ifint($uid))$SQL=" AND uid=".$uid;//游客报名
		if(ifint($fid))$db->query("UPDATE ".__TBL_PARTY_USER__." SET ifpay=1 WHERE fid=".$fid.$SQL);
		
		//写支付日志
		gyl_log($orderid.'｜'.$bz.' -> uid：'.$uid.'｜'.$orderid_title.'｜ ¥'.$money.'(实际：'.$pay_money.')');
		
		if(ifint($uid)){
			//站内通知
			$C = $data_nickname.'您好，恭喜你'.$orderid_title.'交纳成功!';//　　<a href='.HOST.'/party/detail.php?fid='.$fid.' class=aQING>查看详情</a>
			$db->SendTip($uid,'恭喜你，'.$orderid_title.'成功!',dataIO($C,'in'),'sys');
			//微信模版通知
			if (!empty($data_openid) && $data_subscribe==1){
				$keyword1 = '恭喜你'.$orderid_title.'交纳成功!';
				$keyword3 = urlencode($_ZEAI['siteName']);
				$url      = urlencode(wHref('party',$fid));
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
		}
	break;
	//5 全民红娘升级tg
	case 5:
		$grade  = intval($sj_grade);//$if2 = intval($sj_if2);
		$SQL = "grade=$grade,sjtime=".ADDTIME;

		$db->query("UPDATE ".__TBL_TG_USER__." SET ".$SQL." WHERE grade<=$grade AND id=".$tg_uid);
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',money_list_id=0,paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		
		//站内通知
		$C = $data_nickname.'您好，恭喜你'.$orderid_title.'成功!';//　　<a href='.Href('my').' class=aQING>查看详情</a>
		$db->SendTip($tg_uid,'恭喜你，'.$orderid_title.'成功!',dataIO($C,'in'),'tg');
		//微信模版通知
		if (!empty($data_openid) && $data_subscribe==1){
			$keyword1 = '恭喜你'.$orderid_title.'成功!';
			$keyword3 = urlencode($_ZEAI['siteName']);
			//$url      = urlencode(mHref('my'));
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		//写支付日志
		gyl_log('｜'.$bz.' -> tg_uid：'.$tg_uid.'｜'.$orderid_title.'｜ ¥'.$pay_money);
		
		if(ifint($tguid) && @in_array('tg',$navarr)){TG($tguid,$tg_uid,'tg_vip',$pay_money);}
	break;
	//6推广注册激活
	case 6:
		$flag = ($TG_set['regflag'] == 1)?0:1;
		
		$db->query("UPDATE ".__TBL_TG_USER__." SET flag=$flag WHERE id=".$tg_uid);
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',money_list_id=0,paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		
		//站内通知
		$C = $data_nickname.'您好，恭喜你'.$orderid_title.'成功!';//　　<a href='.Href('my').' class=aQING>查看详情</a>
		$db->SendTip($tg_uid,'恭喜你，'.$orderid_title.'成功!',dataIO($C,'in'),'tg');

		//微信模版通知
		if (!empty($data_openid) && $data_subscribe==1){
			$keyword1 = '恭喜你'.$orderid_title.'成功!';
			$keyword3 = urlencode($_ZEAI['siteName']);
			//$url      = urlencode(mHref('my'));
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		//写支付日志
		gyl_log('｜'.$bz.' -> tguid：'.$tguid.'｜tg_uid：'.$tg_uid.'｜'.$orderid_title.'｜ ¥'.$pay_money);
		
		if(ifint($tguid) && @in_array('tg',$navarr)){TG($tguid,$tg_uid,'tg_regactive',$pay_money);}
	break;
	//认证
	case 7:
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',money_list_id=0,paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		gyl_log('｜'.$bz.' -> uid：'.$uid.'｜'.$orderid_title.'｜ ¥'.$pay_money);
	break;
	//文章打赏
	case 8:
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		$row = $db->ROW(__TBL_NEWS__,"ulist","id=".$fid,"name");
		if ($row){
			$ulist = $row['ulist'];
			if(empty($ulist)){
				$newulist[]=$uid;
			}else{
				$newulist=explode(',',$ulist);
				$newulist[]=$uid;
			}
			$newulist=(is_array($newulist))?implode(',',$newulist):'';
			$db->query("UPDATE ".__TBL_NEWS__." SET ulist='$newulist' WHERE id=".$fid);
		}
		gyl_log('｜'.$bz.' -> uid：'.$uid.'｜'.$orderid_title.'｜ ¥'.$pay_money);
	break;
	//注销
	case 9:
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		if ($_VIP['hidedel'] == 1 && $_VIP['hidedel_rmb']>0)$db->query("UPDATE ".__TBL_USER__." SET flag=-1 WHERE id=".$uid);
	break;
	//发短信
	case 10:
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		$rowU  = $db->ROW(__TBL_USER__,"nickname,mob,weixin,sex","id=".$fid,'num');$nickname= dataIO($rowU[0],'out');$mob= dataIO($rowU[1],'out');$weixin= dataIO($rowU[2],'out');$sex=$rowU[3];$sex=($sex==2)?'女':'男';
		$rowMY = $db->ROW(__TBL_USER__,"nickname,mob,weixin,sex","id=".$uid,'num');$nicknameMY= dataIO($rowMY[0],'out');$mobMY= dataIO($rowMY[1],'out');$weixinMY= dataIO($rowMY[2],'out');$sexMY=$rowMY[3];$sexMY=($sexMY==2)?'女':'男';
		$rt=$db->query("SELECT openid FROM ".__TBL_USER__." WHERE ifadm=1");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$openid = $rows['openid'];
				//微信模版审核通过提醒
				if (!empty($openid)){
					$first     = urlencode('【['.$sexMY.']'.$nicknameMY.' uid:'.$uid.'　手机:'.$mobMY.'】->【['.$sex.']'.$nickname.' uid:'.$fid.'　手机:'.$mob.'】');
					$keyword1  = '请求发送手机短信';
					$keyword3  = urlencode($_ZEAI['siteName']);
					$remark    = urlencode($mobMY.'->'.$mob);
					@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url=');
				}
			}
		}
		gyl_log('｜'.$bz.' -> uid：'.$uid.'｜'.$orderid_title.'｜ ¥'.$pay_money);
	break;
	//入駐
	case 11:
		$grade  = intval($fid);
		$tg_uid = intval($tg_uid);
		$row = $db->ROW(__TBL_TG_ROLE__,"yxq,title","grade=0 AND shopgrade=".$grade,"name");
		if($row){
			$R_yxq    = intval($row['yxq']);
			$R_sjtime2=ADDTIME+$R_yxq*86400;
			$R_title  = dataIO($row['title'],'out');
			//
			$rowc = $db->ROW(__TBL_TG_USER__,"shopgrade,sjtime,sjtime2,title,openid,subscribe,shopflag","id=".$tg_uid,"name");
			if ($rowc){
				$shopgrade =$rowc['shopgrade'];
				$sjtime    =$rowc['sjtime'];
				$sjtime2   =$rowc['sjtime2'];
				$ctitle    =dataIO($rowc['title'],'out');
				$openid    =$rowc['openid'];
				$subscribe =$rowc['subscribe'];
				$shopflag  =$rowc['shopflag'];
		
				$shopflag = ($shopflag == 2)?1:$shopflag;
				$shopflag = ($_SHOP['regflag'] == 1)?1:0;
				$SQL  =",shopgrade=$grade,shopgradetitle='$R_title',shopflag=$shopflag";
				
				if($grade>$shopgrade){
					$db->query("UPDATE ".__TBL_TG_USER__." SET sjtime=".ADDTIME.",sjtime2=$R_sjtime2".$SQL." WHERE id=".$tg_uid);
				}elseif($grade==$shopgrade){
					$R_sjtime2 = $sjtime2+$R_yxq*86400;
					$db->query("UPDATE ".__TBL_TG_USER__." SET sjtime2=$R_sjtime2".$SQL." WHERE id=".$tg_uid);
				}
				//站内通知
				$C = '尊敬的【'.$ctitle.'】恭喜'.$orderid_title.'等级升级入驻成功　　<a href='.HOST.'/m4/shop_my_tip.php class=aQING>查看详情</a>';
				$db->SendTip($tg_uid,'尊敬的【'.$ctitle.'】恭喜'.$orderid_title.'等级升级入驻成功!',dataIO($C,'in'),'shop');
				//微信通知
				if (!empty($openid) && $subscribe==1){
					$keyword1 = urlencode('尊敬的【'.$ctitle.'】恭喜'.$orderid_title.'等级升级入驻成功!');
					$keyword3 = urlencode($_ZEAI['siteName']);
					$url      = urlencode(HOST.'/m4/shop_my_tip.php');
					$remark   = urlencode($R_title.' / '.yxq($R_yxq));
					@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
				}
			}
		}
		//
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',money_list_id=0,paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		//支付日志
		//if(ifint($tguid) && @in_array('tg',$navarr) ){TG($tguid,$uid,'vip',$pay_money);}
		gyl_log('｜'.$bz.' -> uid：'.$uid.'｜ -> tg_uid：'.$tg_uid.'｜'.$orderid_title.'｜ ¥'.$pay_money);
	break;
	//商品购买
	case 12:
		$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=1,paytime=".ADDTIME." WHERE id=".$fid);
		$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',money_list_id=0,paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
		//通知买家//
		//站内
		$C = '恭喜你【'.$orderid_title.'】购买成功!';//　　<a href='.Href('my').' class=aQING>查看详情</a>
		$db->SendTip($tg_uid,'恭喜你【'.$orderid_title.'】购买成功!',dataIO($C,'in'),'shop');
		//微信
		if (!empty($data_openid) && $data_subscribe==1){
			$keyword1 = '恭喜你【'.$orderid_title.'】购买成功!';
			$keyword3 = urlencode($_ZEAI['siteName']);
			$url = HOST.'/m4/shop_my_order.php';
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		//通知卖家//
		$row = $db->ROW(__TBL_SHOP_ORDER__,"cid","id=".$fid,"num");
		$cid = $row[0];
		if(ifint($cid)){
			$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe,title","id=".$cid,"num");
			$openid    = $row[0];
			$subscribe = $row[1];
			$cname     = dataIO($row[2],'out');
			//站内
			$C = '客户（ID:'.$tg_uid.'）已对商品【'.$orderid_title.'】->【付款成功】';//　　<a href='.Href('my').' class=aQING>查看详情</a>
			$db->SendTip($cid,'【'.$orderid_title.'】客户【付款成功】',dataIO($C,'in'),'shop');
			//微信
			if (!empty($openid) && $subscribe==1){
				$keyword1 = '客户（ID:'.$tg_uid.'）已对商品【'.$orderid_title.'】->【付款成功】';
				$keyword3 = urlencode($_ZEAI['siteName']);
				$url      = urlencode(HOST.'/m4/shop_my_order.php?f=1&ifadm=1');
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
		}
		//写支付日志
		gyl_log('｜'.$bz.' -> tg_uid：'.$tg_uid.'｜'.$orderid_title.'｜ ¥'.$pay_money);
		//if(ifint($tguid) && @in_array('tg',$navarr)){TG($tguid,$tg_uid,'tg_regactive',$pay_money);}
	break;
}
?>
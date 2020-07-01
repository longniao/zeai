<?php
/**************************************************
QQ:797311 (supdes) Zeai.cn V6.0 微信号：supdes
**************************************************/
function TG($tguid,$uid,$kind='reg',$ifadm=0) {
	global $db,$TG_set,$_ZEAI,$navarr;
	if(@!in_array('tg',$navarr))return false;
	
	//被推荐的推广员
	if($kind=='tg_vip' || $kind=='tg_regactive' || $kind=='tg_reg' ){
		$row = $db->ROW(__TBL_TG_USER__,"nickname,mob","id=".$uid,"name");
		if (!$row)return false;
		//if($row['tgflag']==1 && $kind=='tg_regactive')return false;
		$nickname = dataIO($row['nickname'],'out');
		$mob      = $row['mob'];
		$nickname = (empty($nickname))?substr($mob,0,3).'****'.substr($mob,7,4):$nickname;
	//被推荐的单身用户
	}else{
		$row = $db->ROW(__TBL_USER__,"sex,mob,nickname,tgflag","id=".$uid,"name");
		if (!$row)return false;
		if($row['tgflag']==1 && $kind=='reg')return false;
		$sex      = $row['sex'];
		$mob      = $row['mob'];
		$nickname = dataIO($row['nickname'],'out');
		$nickname = (empty($nickname))?substr($mob,0,3).'****'.substr($mob,7,4):$nickname;
	}
	//获取推广员tguid
	$tgrow = $db->ROW(__TBL_TG_USER__,"nickname,openid,subscribe,tguid,grade,uid,kind,title,mob","id=".$tguid,"name");
	if (!$tgrow)return false;
	$tgopenid   = $tgrow['openid'];
	$tgsubscribe= $tgrow['subscribe'];
	$tgnickname = dataIO($tgrow['nickname'],'out');
	$tggrade    = $tgrow['grade'];
	$tguid_uid  = $tgrow['uid'];
	$tgkind     = $tgrow['kind'];
	$tgtitle    = dataIO($tgrow['title'],'out');
	$tgmob      = $tgrow['mob'];
	if($tgkind==2 || $tgkind==3)$tgnickname = $tgtitle;
	$tgnickname = (!empty($tgnickname))?$tgnickname:substr($tgmob,0,3).'****'.substr($tgmob,7,4);
	$ROLE = gettgroleinfo($tggrade);
	//推广员上级tguid2
	$tguid2 = $tgrow['tguid'];
	if($kind!='tg_vip' && $kind!='tg_regactive' && $kind!='tg_reg' ){
		//如果推广员无上级，检查对应单身用户库有没有上级
		if(!ifint($tguid2) && ifint($tguid_uid)){
			$row2 = $db->ROW(__TBL_USER__,"uname,nickname,openid,subscribe,tguid","id=".$tguid_uid,'name');
			if ($row2)$tguid2 = $row2['tguid'];
		}
	}
	if(ifint($tguid2)){
		$tgrow2 = $db->ROW(__TBL_TG_USER__,"uname,nickname,openid,subscribe,tguid,kind,title,grade,mob","id=".$tguid2,"name");
		if ($tgrow2){
			$tgopenid2   = $tgrow2['openid'];
			$tgsubscribe2= $tgrow2['subscribe'];
			$tggrade2    = $tgrow2['grade'];
			$tgnickname2 = dataIO($tgrow2['nickname'],'out');
			$tgkind2     = $tgrow2['kind'];
			$tgmob2      = $tgrow2['mob'];
			$tgtitle2    = dataIO($tgrow2['title'],'out');
			if($tgkind2==2 || $tgkind2==3)$tgnickname2 = $tgtitle2;
			$tgnickname2 = (!empty($tgnickname2))?$tgnickname2:$tgmob2;
			$ROLE2 = gettgroleinfo($tggrade2);
		}else{
			$tguid2='';
		}
	}
	//
	$priceT      = '元';
	$reward_kind = 'money';
	$TGmsgurl    = HOST.'/m1/tg_my.php';
	//********************************** 直接奖励 *********************************/
	switch ($kind) {
		case 'reg':
			if($TG_set['reward_flag']==1 || $ifadm==1){
				//奖一级
				if($sex==1){
					$price1 = $ROLE['reg_sex1_num1'];
				}else{
					$price1 = $ROLE['reg_sex2_num1'];
				}
				if ($price1 > 0){
					$reward_str1 = '奖励您'.$price1.$priceT.' 　好样的，再接再励~~';
					if ($reward_kind == 'loveb'){
						//
					}elseif($reward_kind == 'money'){
						$SQL      = "money=money+$price1,tgallmoney=tgallmoney+$price1";
						$qdkind   = 'money';
						$pricename='资金';
						$wxurl    = $TGmsgurl;
					}
					$db->query("UPDATE ".__TBL_TG_USER__." SET ".$SQL." WHERE id=".$tguid);
					$db->AddLovebRmbList($tguid,'推荐'.$nickname.'（UID:'.$uid.'）注册奖励',$price1,$qdkind,8,'tg');
					$SQLtgflag = ",tgflag=1";
					if (!empty($tgopenid) && $tgsubscribe==1){
						//到账模版通知
						$first   = urlencode($tgnickname."您好，您有一笔".$pricename."到账！");
						if($ifadm==1){
							$C = urlencode('推荐'.$nickname.'（UID:'.$uid.'）审核成功，特此奖励');
						}else{
							$C = urlencode('推荐用户'.$nickname.'（UID:'.$uid.'）注册成功，好样的，再接再励哦~~');
						}
						@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid.'&num='.$price1.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					}
				}
				//奖二级
				if($sex==1){
					$price2 = $ROLE2['reg_sex1_num2'];
				}else{
					$price2 = $ROLE2['reg_sex2_num2'];
				}
				if ($price2 > 0 && !empty($tguid2)){
					$reward_str2 = '奖励您'.$price2.$priceT.' 　好样的，再接再励~~';
					if ($reward_kind == 'loveb'){
						//
					}elseif($reward_kind == 'money'){
						$SQL       = "money=money+$price2,tgallmoney=tgallmoney+$price2";
						$qdkind    = 'money';
						$pricename ='资金';
						$wxurl     = $TGmsgurl;
					}
					$db->query("UPDATE ".__TBL_TG_USER__." SET ".$SQL." WHERE id=".$tguid2);
					$db->AddLovebRmbList($tguid2,'小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐【'.$nickname.'（UID:'.$uid.'）】注册奖励',$price2,$qdkind,8,'tg');
					if (!empty($tgopenid2) && $tgsubscribe2==1){
						//到账模版通知
						$first   = urlencode($tgnickname2."您好，您有一笔".$pricename."到账！");
						if($ifadm==1){
							$C = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户【'.$nickname.'（UID:'.$uid.'）】审核成功，特此奖励~~');
						}else{
							$C = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户【'.$nickname.'（UID:'.$uid.'）】已进入注册流程，好样的，再接再励哦~~');
						}
						@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid2.'&num='.$price2.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					}
				}
			}
			//$db->query("UPDATE ".__TBL_USER__." SET tguid=$tguid".$SQLtgflag." WHERE id=".$uid);
			
			/************ 一级推荐人发通知 ***********/
				if($ifadm==1){
					$SQLtgflag = ",tgflag=1";
					$Ct = $tgnickname.'恭喜您推荐用户【'.$nickname.'（UID:'.$uid.'）】审核成功！'.$reward_str1;
					$TipT = '推荐审核成功【'.$nickname.'（UID:'.$uid.'）】';
				}else{
					$Ct = $tgnickname.'您好，恭喜您推荐用户【'.$nickname.'（UID:'.$uid.'）】正在进入注册流程，可以协助其完成注册，注册成功会有奖金哦！'.$reward_str1;
					$TipT = '推荐新用户【'.$nickname.'（UID:'.$uid.'）】注册';
				}
				
				//微信客服消息
				$C  = $Ct.'　　<a href="'.$TGmsgurl.'">进入查看</a>';
				@wx_kf_sent($tgopenid,urlencode($C),'text');
				
				//站内消息
				$C  = $Ct;//.'　　<a href='.$TGmsgurl.' class=aQING>进入查看</a>'
				$db->SendTip($tguid,$TipT,dataIO($C,'in'),'tg');
			/************ 二级推荐人发通知 ***********/
			if(!empty($tguid2)){
				if($ifadm==1){
					$Ct = $tgnickname2.'恭喜您小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户【'.$nickname.'（UID:'.$uid.'）】审核成功！'.$reward_str2;
					$TipT = '小伙伴推荐审核成功【'.$nickname.'（ID:'.$uid.'）】';
				}else{
					$Ct = $tgnickname2.'您好，恭喜您小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户【'.$nickname.'（UID:'.$uid.'）】已进入注册流程！'.$reward_str2;
					$TipT = '小伙伴推荐新用户【'.$nickname.'（ID:'.$uid.'）】已进入注册流程';
				}
				
				//微信客服消息
				$C  = $Ct.'　　<a href="'.$TGmsgurl.'">进入查看</a>';
				@wx_kf_sent($tgopenid2,urlencode($C),'text');
				
				//站内消息
				$C  = $Ct;//.'　　<a href="'.Href('my').'" class=aQING>进入查看</a>'
				$db->SendTip($tguid2,$TipT,dataIO($C,'in'),'tg');
			}
			$db->query("UPDATE ".__TBL_USER__." SET tguid=$tguid".$SQLtgflag." WHERE id=".$uid);
		break;
		case 'cz':
			if($sex==1){
				$price1 = $ROLE['cz_sex1_num1'];
				$price2 = $ROLE2['cz_sex1_num2'];
			}else{
				$price1 = $ROLE['cz_sex2_num1'];
				$price2 = $ROLE2['cz_sex2_num2'];
			}
			//奖1
			if($price1>0 && $price1<=100){
				$price1 = round(($price1/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price1,tgallmoney=tgallmoney+$price1 WHERE id=".$tguid);
				$db->AddLovebRmbList($tguid,'推荐用户'.$nickname.'（UID:'.$uid.'）充值提成奖励',$price1,'money',9,'tg');
				$Ct = '推荐用户【'.$nickname.'（UID:'.$uid.'）】充值提成奖励，奖励'.$price1.'元';
				if (!empty($tgopenid) && $tgsubscribe==1){
					//到账模版通知
					$first = urlencode($tgnickname."您好，您有一笔资金到账！");
					$C     = urlencode('推荐用户'.$nickname.'（UID:'.$uid.'）充值提成奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid.'&num='.$price1.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.Href('money').' class=aQING>查看账户</a>'
				$db->SendTip($tguid,'推荐用户【'.$nickname.'（UID:'.$uid.'）】充值提成奖励',dataIO($C,'in'),'tg');
			}
			//奖2
			if($price2>0 && $price2<=100 && ifint($tguid2)){
				$price2 = round(($price2/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price2,tgallmoney=tgallmoney+$price2 WHERE id=".$tguid2);
				$db->AddLovebRmbList($tguid2,'推荐用户'.$nickname.'（UID:'.$uid.'）充值提成奖励',$price2,'money',10,'tg');
				$Ct = '小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户【'.$nickname.'（UID:'.$uid.'）】充值提成奖励，奖励'.$price2.'元';
				if (!empty($tgopenid2) && $tgsubscribe2==1){
					//到账模版通知
					$first = urlencode($tgnickname2."您好，您有一笔资金到账！");
					$C     = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户'.$nickname.'（UID:'.$uid.'）充值提成奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid2.'&num='.$price2.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid2,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.Href('money').' class=aQING>查看账户</a>'
				$db->SendTip($tguid2,'小伙伴用户推荐的【'.$nickname.'（UID:'.$uid.'）】充值提成奖励',dataIO($C,'in'),'tg');
			}			
		break;
		case 'vip':
			if($sex==1){
				$price1 = $ROLE['vip_sex1_num1'];
				$price2 = $ROLE2['vip_sex1_num2'];
			}else{
				$price1 = $ROLE['vip_sex2_num1'];
				$price2 = $ROLE2['vip_sex2_num2'];
			}
			//奖1
			if($price1>0 && $price1<=100){
				$price1 = round(($price1/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price1,tgallmoney=tgallmoney+$price1 WHERE id=".$tguid);
				$db->AddLovebRmbList($tguid,'推荐用户'.$nickname.'（UID:'.$uid.'）升级VIP提成奖励',$price1,'money',9,'tg');
				$Ct = '推荐用户【'.$nickname.'（UID:'.$uid.'）】升级VIP提成奖励，奖励'.$price1.'元';
				if (!empty($tgopenid) && $tgsubscribe==1){
					//到账模版通知
					$first = urlencode($tgnickname."您好，您有一笔资金到账！");
					$C     = urlencode('推荐用户'.$nickname.'（UID:'.$uid.'）升级VIP奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid.'&num='.$price1.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.$TGmsgurl.' class=aQING>查看账户</a>'
				$db->SendTip($tguid,'推荐用户【'.$nickname.'（UID:'.$uid.'）】升级VIP提成奖励',dataIO($C,'in'),'tg');
			}
			//奖2
			if($price2>0 && $price2<=100 && ifint($tguid2)){
				$price2 = round(($price2/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price2,tgallmoney=tgallmoney+$price2 WHERE id=".$tguid2);
				$db->AddLovebRmbList($tguid2,'推荐用户'.$nickname.'（UID:'.$uid.'）升级VIP提成奖励',$price2,'money',10,'tg');
				$Ct = '小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户【'.$nickname.'（UID:'.$uid.'）】升级VIP提成奖励，奖励'.$price2.'元';
				if (!empty($tgopenid2) && $tgsubscribe2==1){
					//到账模版通知
					$first = urlencode($tgnickname2."您好，您有一笔资金到账！");
					$C     = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户'.$nickname.'（UID:'.$uid.'）升级VIP提成奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid2.'&num='.$price2.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid2,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.Href('money').' class=aQING>查看账户</a>'
				$db->SendTip($tguid2,'小伙伴用户推荐的【'.$nickname.'（ID:'.$uid.'）】升级VIP提成奖励',dataIO($C,'in'),'tg');
			}			
		break;
		case 'rz':
			if($sex==1){
				$price1 = $ROLE['rz_sex1_num1'];
				$price2 = $ROLE2['rz_sex1_num2'];
			}else{
				$price1 = $ROLE['rz_sex2_num1'];
				$price2 = $ROLE2['rz_sex2_num2'];
			}
			//奖1
			if($price1>0 && $price1<=100){
				$price1 = round(($price1/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price1,tgallmoney=tgallmoney+$price1 WHERE id=".$tguid);
				$db->AddLovebRmbList($tguid,'推荐用户'.$nickname.'（UID:'.$uid.'）认证提成奖励',$price1,'money',9,'tg');
				$Ct = '推荐用户【'.$nickname.'（UID:'.$uid.'）】认证提成奖励，奖励'.$price1.'元';
				if (!empty($tgopenid) && $tgsubscribe==1){
					//到账模版通知
					$first = urlencode($tgnickname."您好，您有一笔资金到账！");
					$C     = urlencode('推荐用户'.$nickname.'（UID:'.$uid.'）认证提成奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid.'&num='.$price1.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.Href('money').' class=aQING>查看账户</a>'
				$db->SendTip($tguid,'推荐用户【'.$nickname.'（UID:'.$uid.'）】认证提成奖励',dataIO($C,'in'),'tg');
			}
			//奖2
			if($price2>0 && $price2<=100 && ifint($tguid2)){
				$price2 = round(($price2/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price2,tgallmoney=tgallmoney+$price2 WHERE id=".$tguid2);
				$db->AddLovebRmbList($tguid2,'推荐用户'.$nickname.'（UID:'.$uid.'）认证提成奖励',$price2,'money',10,'tg');
				$Ct = '小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户【'.$nickname.'（UID:'.$uid.'）】认证提成奖励，奖励'.$price2.'元';
				if (!empty($tgopenid2) && $tgsubscribe2==1){
					//到账模版通知
					$first = urlencode($tgnickname2."您好，您有一笔资金到账！");
					$C     = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐用户'.$nickname.'（UID:'.$uid.'）认证提成奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid2.'&num='.$price2.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid2,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.Href('money').' class=aQING>查看账户</a>'
				$db->SendTip($tguid2,'小伙伴用户推荐的【'.$nickname.'（UID:'.$uid.'）】认证提成奖励',dataIO($C,'in'),'tg');
			}			
		break;
		case 'tg_vip':
			$price1 = $ROLE['union_num1'];
			$price2 = $ROLE2['union_num2'];
			//奖1
			if($price1>0 && $price1<=100){
				$price1 = round(($price1/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price1,tgallmoney=tgallmoney+$price1 WHERE id=".$tguid);
				$db->AddLovebRmbList($tguid,'推荐合伙人'.$nickname.'（ID:'.$uid.'）升级等级奖励',$price1,'money',9,'tg');
				$Ct = '推荐合伙人【'.$nickname.'（ID:'.$uid.'）】升级等级提成奖励，奖励'.$price1.'元';
				if (!empty($tgopenid) && $tgsubscribe==1){
					//到账模版通知
					$first = urlencode($tgnickname."您好，您有一笔资金到账！");
					$C     = urlencode('推荐合伙人'.$nickname.'（ID:'.$uid.'）升级等级奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid.'&num='.$price1.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.$TGmsgurl.' class=aQING>查看账户</a>'
				$db->SendTip($tguid,'推荐合伙人【'.$nickname.'（ID:'.$uid.'）】升级等级奖励',dataIO($C,'in'),'tg');
			}
			//奖2
			if($price2>0 && $price2<=100 && ifint($tguid2)){
				$price2 = round(($price2/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price2,tgallmoney=tgallmoney+$price2 WHERE id=".$tguid2);
				$db->AddLovebRmbList($tguid2,'推荐合伙人'.$nickname.'（ID:'.$uid.'）升级等级奖励',$price2,'money',10,'tg');
				$Ct = '小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人【'.$nickname.'（ID:'.$uid.'）】升级等级奖励，奖励'.$price2.'元';
				if (!empty($tgopenid2) && $tgsubscribe2==1){
					//到账模版通知
					$first = urlencode($tgnickname2."您好，您有一笔资金到账！");
					$C     = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人'.$nickname.'（ID:'.$uid.'）升级等级奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid2.'&num='.$price2.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid2,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.Href('money').' class=aQING>查看账户</a>'
				$db->SendTip($tguid2,'小伙伴用户推荐的【'.$nickname.'（ID:'.$uid.'）】升级等级奖励',dataIO($C,'in'),'tg');
			}			
		break;
		case 'tg_regactive':
			if($TG_set['active_price']<=0)return false;	
			$price1 = $ROLE['union_num1'];
			$price2 = $ROLE2['union_num2'];
			//奖1
			if($price1>0 && $price1<=100){
				$price1 = round(($price1/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price1,tgallmoney=tgallmoney+$price1 WHERE id=".$tguid);
				$db->AddLovebRmbList($tguid,'推荐合伙人'.$nickname.'（ID:'.$uid.'）激活帐号奖励',$price1,'money',9,'tg');
				$Ct = '推荐合伙人【'.$nickname.'（ID:'.$uid.'）】激活帐号奖励，奖励'.$price1.'元';
				if (!empty($tgopenid) && $tgsubscribe==1){
					//到账模版通知
					$first = urlencode($tgnickname."您好，您有一笔资金到账！");
					$C     = urlencode('推荐合伙人'.$nickname.'（ID:'.$uid.'）激活帐号奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid.'&num='.$price1.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.$TGmsgurl.' class=aQING>查看账户</a>'
				$db->SendTip($tguid,'推荐合伙人【'.$nickname.'（ID:'.$uid.'）】激活帐号奖励',dataIO($C,'in'),'tg');
			}
			//奖2
			if($price2>0 && $price2<=100 && ifint($tguid2)){
				$price2 = round(($price2/100)*$ifadm,2);
				$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price2,tgallmoney=tgallmoney+$price2 WHERE id=".$tguid2);
				$db->AddLovebRmbList($tguid2,'推荐合伙人'.$nickname.'（ID:'.$uid.'）激活帐号奖励',$price2,'money',10,'tg');
				$Ct = '小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人【'.$nickname.'（ID:'.$uid.'）】激活帐号奖励，奖励'.$price2.'元';
				if (!empty($tgopenid2) && $tgsubscribe2==1){
					//到账模版通知
					$first = urlencode($tgnickname2."您好，您有一笔资金到账！");
					$C     = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人'.$nickname.'（ID:'.$uid.'）激活帐号奖励~~');
					$wxurl = $TGmsgurl;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid2.'&num='.$price2.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					//微信客服消息
					$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
					@wx_kf_sent($tgopenid2,urlencode($C),'text');
				}
				//站内消息
				$C  = $Ct;//.'　　<a href='.Href('money').' class=aQING>查看账户</a>'
				$db->SendTip($tguid2,'小伙伴用户推荐的【'.$nickname.'（ID:'.$uid.'）】激活帐号奖励',dataIO($C,'in'),'tg');
			}
			$db->query("UPDATE ".__TBL_TG_USER__." SET tgflag=1 WHERE id=".$uid);
		break;
		case 'tg_reg':
			if($TG_set['reward_flag']==1 || $ifadm==1){
				$price1 = $ROLE['union_reg_num1'];
				$price2 = $ROLE2['union_reg_num2'];
				//奖1
				if ($price1 > 0){
					$reward_str1 = '奖励您'.$price1.$priceT.' 　好样的，再接再励~~';
					if ($reward_kind == 'loveb'){
						//
					}elseif($reward_kind == 'money'){
						$SQL      = "money=money+$price1,tgallmoney=tgallmoney+$price1";
						$qdkind   = 'money';
						$pricename='资金';
						$wxurl    = $TGmsgurl;
					}
					$db->query("UPDATE ".__TBL_TG_USER__." SET ".$SQL." WHERE id=".$tguid);
					$db->AddLovebRmbList($tguid,'推荐合伙人'.$nickname.'（ID:'.$uid.'）注册奖励',$price1,$qdkind,8,'tg');
					$SQLtgflag = ",tgflag=1,tgmoney=$price1";
					
					if (!empty($tgopenid) && $tgsubscribe==1){
						//到账模版通知
						$first   = urlencode($tgnickname."您好，您有一笔".$pricename."到账！");
						if($ifadm==1){
							$C = urlencode('推荐合伙人'.$nickname.'（ID:'.$uid.'）审核成功，特此奖励');
						}else{
							$C = urlencode('推荐合伙人'.$nickname.'（ID:'.$uid.'）注册成功，好样的，再接再励哦~~');
						}
						@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid.'&num='.$price1.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					}
				}
				//奖2
				if ($price2 > 0 && !empty($tguid2)){
					$reward_str2 = '奖励您'.$price2.$priceT.' 　好样的，再接再励~~';
					if ($reward_kind == 'loveb'){
						//
					}elseif($reward_kind == 'money'){
						$SQL       = "money=money+$price2,tgallmoney=tgallmoney+$price2";
						$qdkind    = 'money';
						$pricename ='资金';
						$wxurl     = $TGmsgurl;
					}
					$db->query("UPDATE ".__TBL_TG_USER__." SET ".$SQL." WHERE id=".$tguid2);
					$db->AddLovebRmbList($tguid2,'小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人【'.$nickname.'（ID:'.$uid.'）】注册奖励',$price2,$qdkind,8,'tg');
					if (!empty($tgopenid2) && $tgsubscribe2==1){
						//到账模版通知
						$first   = urlencode($tgnickname2."您好，您有一笔".$pricename."到账！");
						if($ifadm==1){
							$C = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人【'.$nickname.'（ID:'.$uid.'）】审核成功，特此奖励~~');
						}else{
							$C = urlencode('小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人【'.$nickname.'（ID:'.$uid.'）】注册成功，好样的，再接再励哦~~');
						}
						@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid2.'&num='.$price2.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
					}
				}
			}
			//$db->query("UPDATE ".__TBL_TG_USER__." SET tguid=$tguid".$SQLtgflag." WHERE id=".$uid);
			/************ 一级推荐人发通知 ***********/
				if($ifadm==1){
					//$SQLtgflag = ",tgflag=1";
					$price1=floatval($price1);
					$SQLtgflag = ",tgflag=1,tgmoney=$price1";
					$Ct = $tgnickname.'恭喜您推荐合伙人【'.$nickname.'（ID:'.$uid.'）】审核成功！'.$reward_str1;
					$TipT = '推荐合伙人审核成功【'.$nickname.'（ID:'.$uid.'）】';
				}else{
					$Ct = $tgnickname.'您好，恭喜您推荐合伙人【'.$nickname.'（ID:'.$uid.'）】已注册！'.$reward_str1;
					$TipT = '推荐合伙人【'.$nickname.'（ID:'.$uid.'）】已注册！';
				}
				
				//微信客服消息
				$C  = $Ct.'　　<a href="'.$TGmsgurl.'">进入查看</a>';
				@wx_kf_sent($tgopenid,urlencode($C),'text');
				
				//站内消息
				$C  = $Ct;//.'　　<a href='.$TGmsgurl.' class=aQING>进入查看</a>'
				$db->SendTip($tguid,$TipT,dataIO($C,'in'),'tg');
			/************ 二级推荐人发通知 ***********/
			if(!empty($tguid2)){
				if($ifadm==1){
					$Ct = $tgnickname2.'恭喜您小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人【'.$nickname.'（ID:'.$uid.'）】审核成功！'.$reward_str2;
					$TipT = '小伙伴推荐合伙人审核成功【'.$nickname.'（ID:'.$uid.'）】';
				}else{
					$Ct = $tgnickname2.'您好，恭喜您小伙伴【'.$tgnickname.'（ID:'.$tguid.'）】推荐合伙人【'.$nickname.'（ID:'.$uid.'）】已注册！'.$reward_str2;
					$TipT = '小伙伴推荐合伙人【'.$nickname.'（ID:'.$uid.'）】已注册！';
				}
				
				//微信客服消息
				$C  = $Ct.'　　<a href="'.$TGmsgurl.'">进入查看</a>';
				@wx_kf_sent($tgopenid2,urlencode($C),'text');
				
				//站内消息
				$C  = $Ct;//.'　　<a href="'.Href('my').'" class=aQING>进入查看</a>'
				$db->SendTip($tguid2,$TipT,dataIO($C,'in'),'tg');
			}
			$db->query("UPDATE ".__TBL_TG_USER__." SET tguid=$tguid".$SQLtgflag." WHERE id=".$uid);
		break;
	}
}
function gettgroleinfo($G) {
	global $db;
	$row_role=$db->ROW(__TBL_TG_ROLE__,"reward_kind,reg_money_sex1_num1,reg_money_sex1_num2,reg_money_sex2_num1,reg_money_sex2_num2,cz_sex1_num1,cz_sex1_num2,cz_sex2_num1,cz_sex2_num2,vip_sex1_num1,vip_sex1_num2,vip_sex2_num1,vip_sex2_num2,rz_sex1_num1,rz_sex1_num2,rz_sex2_num1,rz_sex2_num2,union_num1,union_num2,union_reg_num1,union_reg_num2","grade=".$G,"name");
	$reward_kind = $row_role['reward_kind'];
	if($reward_kind=='loveb'){//loveb
		return false;//开发中
	}elseif($reward_kind=='money'){//元
		//新单身注册
		$ROLE['reg_sex1_num1'] = floatval($row_role['reg_money_sex1_num1']);
		$ROLE['reg_sex1_num2'] = floatval($row_role['reg_money_sex1_num2']);
		$ROLE['reg_sex2_num1'] = floatval($row_role['reg_money_sex2_num1']);
		$ROLE['reg_sex2_num2'] = floatval($row_role['reg_money_sex2_num2']);
		//单身在线充值
		$ROLE['cz_sex1_num1']  = intval($row_role['cz_sex1_num1']);
		$ROLE['cz_sex1_num2']  = intval($row_role['cz_sex1_num2']);
		$ROLE['cz_sex2_num1']  = intval($row_role['cz_sex2_num1']);
		$ROLE['cz_sex2_num2']  = intval($row_role['cz_sex2_num2']);
		//单身升级VIP
		$ROLE['vip_sex1_num1'] = intval($row_role['vip_sex1_num1']);
		$ROLE['vip_sex1_num2'] = intval($row_role['vip_sex1_num2']);
		$ROLE['vip_sex2_num1'] = intval($row_role['vip_sex2_num1']);
		$ROLE['vip_sex2_num2'] = intval($row_role['vip_sex2_num2']);
		//单身认证
		$ROLE['rz_sex1_num1'] = intval($row_role['rz_sex1_num1']);
		$ROLE['rz_sex1_num2'] = intval($row_role['rz_sex1_num2']);
		$ROLE['rz_sex2_num1'] = intval($row_role['rz_sex2_num1']);
		$ROLE['rz_sex2_num2'] = intval($row_role['rz_sex2_num2']);
		//新合伙人注册奖励
		$ROLE['union_reg_num1'] = floatval($row_role['union_reg_num1']);
		$ROLE['union_reg_num2'] = floatval($row_role['union_reg_num2']);
		//合伙人激活帐号/升级
		$ROLE['union_num1'] = intval($row_role['union_num1']);
		$ROLE['union_num2'] = intval($row_role['union_num2']);
		//
		//$ROLE['priceT'] = '元';
		//$ROLE['reward_kind'] = $row_role['reward_kind'];;
	}
	return $ROLE;
}
?>
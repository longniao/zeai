<?php
/**************************************************
QQ:797311 (supdes) Zeai.cn V6.0 微信号：supdes
**************************************************/
function TG_shop($oid,$kind='product') {
	global $db,$_SHOP,$_ZEAI,$navarr;
	if(@!in_array('tg',$navarr))return false;
	$row = $db->ROW(__TBL_SHOP_ORDER__,"flag,cid,pid,tg_uid,orderprice,tgbfb1,tgbfb2","(tgbfb1>0 OR tgbfb2>0) AND orderprice>0 AND flag=3 AND id=".$oid,"num");
	if (!$row)return false;
	$flag= $row[0];$cid= $row[1];$pid = $row[2];$tg_uid= $row[3];$orderprice= $row[4];$tgbfb1= $row[5];$tgbfb2= $row[6];
	$price1=0;$price2=0;
	if($tgbfb1>0)$price1=round($orderprice*($tgbfb1/100),2);
	if($tgbfb2>0)$price2=round($orderprice*($tgbfb2/100),2);
	if($price1==0 && $price2==0)return false;
	//
	$row = $db->ROW(__TBL_TG_PRODUCT__,"title,fahuokind","id=".$pid,"num");
	$ptitle = trimhtml(dataIO($row[0],'out'));
	//
	$row = $db->ROW(__TBL_TG_USER__,"tguid,nickname","id=".$tg_uid,"name");
	if($row){$tguid1=$row['tguid'];$nickname = trimhtml(dataIO($row['nickname'],'out'));}else{return false;}
	//推广员1
	if(ifint($tguid1) && $price1>0){
		$tgrow = $db->ROW(__TBL_TG_USER__,"nickname,openid,subscribe,tguid,mob","id=".$tguid1,"name");
		if ($tgrow){
			$tgopenid   = $tgrow['openid'];
			$tgsubscribe= $tgrow['subscribe'];
			$tgnickname = trimhtml(dataIO($tgrow['nickname'],'out'));
			$tgmob      = $tgrow['mob'];
			$tgnickname = (!empty($tgnickname))?$tgnickname:substr($tgmob,0,3).'****'.substr($tgmob,7,4);
			$tguid2     = $tgrow['tguid'];
			//
			$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price1,tgallmoney=tgallmoney+$price1 WHERE id=".$tguid1);
			//
			$TGmsgurl = HOST.'/m1/tg_my.php';
			$Ct       = '推荐【'.$nickname.'（ID:'.$tg_uid.'）】购买商品【'.$ptitle.'】，奖励'.$price1.'元';
			$db->AddLovebRmbList($tguid1,$Ct,$price1,'money',9,'tg');
			//
			if (!empty($tgopenid) && $tgsubscribe==1){
				$first = urlencode($tgnickname."您好，您有一笔资金到账！");
				$wxurl = $TGmsgurl;
				$C     = urlencode($Ct);
				@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid.'&num='.$price1.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
				$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
				@wx_kf_sent($tgopenid,urlencode($C),'text');
			}
			$db->SendTip($tguid1,$Ct,dataIO($Ct,'in'),'tg');
		}
	}
	//推广员2
	if(ifint($tguid2) && $price2>0){
		$tgrow = $db->ROW(__TBL_TG_USER__,"nickname,openid,subscribe,mob","id=".$tguid2,"name");
		if ($tgrow){
			$tgopenid2   = $tgrow['openid'];
			$tgsubscribe2= $tgrow['subscribe'];
			$tgnickname2 = trimhtml(dataIO($tgrow['nickname'],'out'));
			$tgmob2      = $tgrow['mob'];
			$tgnickname2 = (!empty($tgnickname2))?$tgnickname2:substr($tgmob2,0,3).'****'.substr($tgmob2,7,4);
			//
			$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$price2,tgallmoney=tgallmoney+$price2 WHERE id=".$tguid2);
			//
			$TGmsgurl = HOST.'/m1/tg_my.php';
			$Ct       = '合伙人推荐【'.$nickname.'（ID:'.$tg_uid.'）】购买商品【'.$ptitle.'】，奖励'.$price2.'元';
			$db->AddLovebRmbList($tguid2,$Ct,$price2,'money',9,'tg');
			//
			if (!empty($tgopenid2) && $tgsubscribe2==1){
				$first = urlencode($tgnickname2."您好，您有一笔资金到账！");
				$wxurl = $TGmsgurl;
				$C     = urlencode($Ct);
				@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$tgopenid2.'&num='.$price2.'&first='.$first.'&content='.$C.'&url='.urlencode($wxurl));
				$C = $Ct.'　　<a href="'.$wxurl.'">进入查看</a>';
				@wx_kf_sent($tgopenid2,urlencode($C),'text');
			}
			$db->SendTip($tguid2,$Ct,dataIO($Ct,'in'),'tg');
		}
	}
}
?>
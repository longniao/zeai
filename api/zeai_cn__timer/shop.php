<?php
define('ZEAI_PHPV6',substr(dirname(__FILE__),0,-18));
require_once ZEAI_PHPV6.'sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
require_once ZEAI.'sub/TGfun_shop.php';
$rt=$db->query("SELECT id,flag,pid,tuihuotime,fahuotime,tuikuantime,cid,pid,tg_uid,orderprice,orderid,tgbfb1,tgbfb2 FROM ".__TBL_SHOP_ORDER__." WHERE flag=2 OR flag=5 OR flag=7");
$total = $db->num_rows($rt);
if ($total > 0) {
	for($i=1;$i<=$total;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$oid=$rows['id'];
		$pid=$rows['pid'];
		$flag=$rows['flag'];
		$tuihuotime=$rows['tuihuotime'];
		$fahuotime=$rows['fahuotime'];
		$tuikuantime=$rows['tuikuantime'];
		$cid   =intval($rows['cid']);
		$tg_uid=intval($rows['tg_uid']);
		$orderprice=$rows['orderprice'];
		$orderid=$rows['orderid'];
		$tgbfb1=$rows['tgbfb1'];
		$tgbfb2=$rows['tgbfb2'];
		//商品信息
		$row    = $db->ROW(__TBL_TG_PRODUCT__,"title,fahuokind","id=".$pid,"num");
		$ptitle = dataIO($row[0],'out');
		$fahuokind=($row[1]==2)?2:1;
		switch ($flag) {
			case 2://等待买家确认收货
				$d2 = $fahuotime+intval($_SHOP['qrshday'])*86400;
			break;
			case 5://买家申请退货中
				$autoday=($fahuokind==2)?$_SHOP['hdday']:$_SHOP['thday'];
				$d2 = $tuihuotime+intval($autoday)*86400;
			break;
			case 7://买家申请退款中
				$d2 = $tuikuantime+intval($_SHOP['tkday'])*86400;
			break;
		}
		$totals  = ($d2-ADDTIME);
		if($totals<=0){
			//买家信息
			$row = $db->ROW(__TBL_TG_USER__,"nickname,openid,subscribe","id=".$tg_uid,"num");
			if($row){
				$tg_nickname = dataIO($row[0],'out');
				$tg_openid   = $row[1];
				$tg_subscribe= $row[2];
			}else{continue;}
			//卖家信息
			$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe,title","id=".$cid,"num");
			if($row){
				$openid = $row[0];$subscribe = $row[1];$cname = dataIO($row[2],'out');
			}else{continue;}
			//
			switch ($flag) {
				case 2://等待买家确认收货
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=3,endtime=".ADDTIME." WHERE id=".$oid);
					//入账
					if($orderprice>0){
						if($tgbfb1>0)$tgbfb1_money=round($orderprice*($tgbfb1/100),2);
						if($tgbfb2>0)$tgbfb2_money=round($orderprice*($tgbfb2/100),2);
						$orderprice=$orderprice-($tgbfb1_money+$tgbfb2_money);
						if($orderprice>0){
							$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$orderprice WHERE id=".$cid);
							$db->AddLovebRmbList($cid,'【'.$ptitle.'】自动确认，来自买家（'.$tg_nickname.' ID:'.$tg_uid.'）',$orderprice,'money',16,'tg');
							$tmstr='　账户余额到账￥'.$orderprice;
							TG_shop($oid);
						}
					}
					/**********通知卖家**********/
					$C = '【'.$ptitle.'】自动确认收货成功！'.$tmstr.'->来自买家（'.$tg_nickname.' ID:'.$tg_uid.'）';$db->SendTip($cid,'【'.$ptitle.'】自动确认收货成功',dataIO($C,'in'),'shop');
					if (!empty($openid) && $subscribe==1){
						$keyword1 = urlencode('【'.$ptitle.'】自动确认收货成功！'.$tmstr.'->来自买家（'.$tg_nickname.' ID:'.$tg_uid.'）');$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php?f=3&ifadm=1');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					/**********通知买家**********/
					$C = '您购买的【'.$ptitle.'】已自动确认收货成功！';$db->SendTip($tg_uid,'【'.$ptitle.'】自动确认收货成功',dataIO($C,'in'),'shop');
					if (!empty($tg_openid) && $tg_subscribe==1){
						$keyword1 = urlencode('您下单的【'.$ptitle.'】已自动确认收货成功！');$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php?f=3');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$tg_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
				break;
				case 5://买家申请退货中
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=6,tuihuoendtime=".ADDTIME." WHERE id=".$oid);
					$zjtui=false;
					if($orderprice>0){
						$row = $db->ROW(__TBL_PAY__,"id,paymoney,trade_no","flag=1 AND trade_no<>'' AND paymoney>0 AND orderid='$orderid'","num");
						if ($row){
							$payid   = $row[0];
							$paymoney= $row[1];
							$transaction_id= $row[2];
							require_once ZEAI.'api/weixin/pay/refund/zeai_refund_func.php';
							$ret=refund($paymoney,$transaction_id);
							if($ret){
								$db->query("UPDATE ".__TBL_PAY__." SET flag=-2 WHERE id=".$payid);
								notice_auto('tuihuo');
							}else{
								$zjtui=true;
							}
						}else{
							$zjtui=true;
						}
					}
					if($orderprice<=0 || $zjtui)notice_auto('tuihuo');
				break;
				case 7://买家申请退款中
					if($orderprice>0){	
						$row = $db->ROW(__TBL_PAY__,"id,paymoney,trade_no","flag=1 AND trade_no<>'' AND paymoney>0 AND orderid='$orderid'","num");
						if ($row){
							$payid = $row[0];$paymoney= $row[1];$transaction_id= $row[2];
							if($orderprice==$paymoney){
								require_once ZEAI.'api/weixin/pay/refund/zeai_refund_func.php';
								$ret=refund($paymoney,$transaction_id);
								if($ret){
									$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=8,tuikuanendtime=".ADDTIME." WHERE id=".$oid);
									$db->query("UPDATE ".__TBL_PAY__." SET flag=-2 WHERE id=".$payid);
									notice_auto('tuikuan');
								}
							}
						}
					}
				break;
			}
		}
	}
}
function notice_auto($kind) {
	global $db,$_ZEAI,$ptitle,$tg_uid,$tg_nickname,$tg_openid,$tg_subscribe,$cid,$openid,$subscribe,$cname,$fahuokind;
	if($kind=='tuihuo'){
		$fahuokind_str=($fahuokind==2)?'退款':'退货';
		/************通知买家************/
		$C = '您购买的【'.$ptitle.'】已自动确认'.$fahuokind_str.'成功';$db->SendTip($tg_uid,'【'.$ptitle.'】自动'.$fahuokind_str.'成功',$C,'shop');
		if (!empty($tg_openid) && $tg_subscribe==1){
			$keyword1 = urlencode($C);$keyword3 = urlencode($_ZEAI['siteName']);
			$url      = urlencode(HOST.'/m4/shop_my_order.php?f=6');
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$tg_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		/************通知卖家************/
		$C = '自动确认'.$fahuokind_str.'成功->【'.$ptitle.'】';$db->SendTip($cid,'【'.$ptitle.'】自动'.$fahuokind_str.'成功',$C,'shop');
		if (!empty($openid) && $subscribe==1){
			$keyword1 = urlencode($C);$keyword3 = urlencode($_ZEAI['siteName']);
			$url      = urlencode(HOST.'/m4/shop_my_order.php?f=6&ifadm=1');
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
	}elseif($kind=='tuikuan'){
		/************通知买家************/
		$C = '您购买的【'.$ptitle.'】已自动确认退款成功';$db->SendTip($tg_uid,'【'.$ptitle.'】自动退款成功',$C,'shop');
		if (!empty($tg_openid) && $tg_subscribe==1){
			$keyword1 = urlencode($C);$keyword3 = urlencode($_ZEAI['siteName']);
			$url      = urlencode(HOST.'/m4/shop_my_order.php?f=8');
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$tg_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		/************通知卖家************/
		$C = '自动确认退款成功->【'.$ptitle.'】';$db->SendTip($cid,'【'.$ptitle.'】自动退款成功',$C,'shop');
		if (!empty($openid) && $subscribe==1){
			$keyword1 = urlencode($C);$keyword3 = urlencode($_ZEAI['siteName']);
			$url      = urlencode(HOST.'/m4/shop_my_order.php?f=8&ifadm=1');
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
	}
}

$rt=$db->query("SELECT id,title,shopgrade,sjtime2,openid,subscribe FROM ".__TBL_TG_USER__." WHERE sjtime2>0 AND shopflag<>2");
$total = $db->num_rows($rt);
if ($total > 0) {
	for($i=1;$i<=$total;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$cid      = $rows['id'];
		$cname    = dataIO($rows['title'],'out');
		$shopgrade= $rows['shopgrade'];
		$openid   = $rows['openid'];
		$subscribe= $rows['subscribe'];
		$d1   = ADDTIME;
		$d2   = $rows['sjtime2'];
		$diff = $d2-$d1;
		if ($diff < 0){
			$db->query("UPDATE ".__TBL_TG_USER__." SET shopgrade=1,shopflag=2,sjtime=0,sjtime2=0 WHERE id=".$cid);
			$shopgrade_t=shopgrade($shopgrade,'t');
			$url=HOST.'/m4/shop_my_vip.php';
			$C = $cname.'，您的'.$_SHOP['title'].'【'.$shopgrade_t.'】服务期限已过期~~ 为了避免业务受影响，请尽快充值和升级　<a href="'.$url.'" class=aQING>立即升级</a>';
			$db->SendTip($cid,'您的【'.$shopgrade_t.'】资格已过期，请速续费',dataIO($C,'in'),'shop');
			if (!empty($openid) && $subscribe==1){
				$first     = urlencode($nickname.',您的【'.$shopgrade_t.'】已过期！');
				$keyword1  = urlencode($_SHOP['title'].'功能暂时冻结');
				$keyword3  = urlencode('系统自动执行');
				$remark    = urlencode('为了避免业务受影响，请尽快充值和升级');
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.urlencode($url));
			}
		}
	}
}
?>
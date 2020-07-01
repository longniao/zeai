<?php
require_once '../sub/init.php';
$currfieldstg="qhdz,qhbz";
require_once ZEAI.'m4/shop_chk_u.php';
require_once ZEAI.'sub/TGfun_shop.php';
$c_qhdz = dataIO($rowtg['qhdz'],'out');
$c_qhbz = dataIO($rowtg['qhbz'],'out');
$c_qhbz_str=(!empty($c_qhbz))?'（'.$c_qhbz.'）':'';
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
$_ZEAI['pagesize'] = 10;
if($ifadm==1){
	$ZEAI_SQL = "cid=".$cook_tg_uid;
	if($rowtg['shopflag']==0 || $rowtg['shopflag']==-1 || $rowtg['shopflag']==2)header("Location: shop_my_flag.php");
}else{
	$ZEAI_SQL = "tg_uid=".$cook_tg_uid;
}
$SQLNUM=$ZEAI_SQL;
switch ($submitok) {
	case 'ajax_order_cancel'://买家取消订单
		if(!ifint($oid))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		if($ifadm==1)json_exit(array('flag'=>0,'msg'=>'亲，只有【买家】才可以操作哦'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"flag","id=".$oid,"num");
		if ($row){
			$flag = $row[0];
			if($flag==0)$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=4,canceltime=".ADDTIME." WHERE tg_uid=".$cook_tg_uid." AND id=".$oid);
			json_exit(array('flag'=>1,'msg'=>'操作成功'));
		}else{
			json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		}
	break;
	case 'ajax_getwlinfo'://物流信息
		if(!ifint($oid))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"orderid,cid,kuaidi_name,kuaidi_code","id=".$oid,"num");
		if ($row){
			$orderid= $row[0];
			$cid    = intval($row[1]);
			$kdname = dataIO($row[2],'out');
			$kdcode = dataIO($row[3],'out');
			if(ifint($cid)){
				$row = $db->ROW(__TBL_TG_USER__,"mob,tel","id=".$cid,"num");
				$mob=dataIO($row[0],'out');
				$tel=dataIO($row[1],'out');
				$mob=(!empty($mob))?$mob:$tel;
			}
			json_exit(array('flag'=>1,'orderid'=>$orderid,'kdname'=>$kdname,'kdcode'=>$kdcode,'mjtel'=>$mob));
		}else{
			json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		}
	break;
	case 'ajax_order_flag3OK'://买家确认收货
		if(!ifint($oid))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		if($ifadm==1)json_exit(array('flag'=>0,'msg'=>'亲，只有【买家】才可以操作哦'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"flag,cid,pid,tg_uid,orderprice,tgbfb1,tgbfb2","tg_uid=".$cook_tg_uid." AND id=".$oid,"num");
		if ($row){
			$flag= $row[0];$cid= $row[1];$pid = $row[2];$tg_uid= $row[3];$orderprice= $row[4];$tgbfb1= $row[5];$tgbfb2= $row[6];
			//
			$row = $db->ROW(__TBL_TG_PRODUCT__,"title,fahuokind","id=".$pid,"num");
			$ptitle = dataIO($row[0],'out');
			$fahuokind=($row[1]==2)?2:1;
			$fahuokind_str=($fahuokind==2)?'取货核销':'确认收货';
			//
			if($flag==2){
				$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=3,endtime=".ADDTIME." WHERE tg_uid=".$cook_tg_uid." AND id=".$oid);
				//通卖
				if(ifint($cid)){
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe,title","id=".$cid,"num");
					$openid    = $row[0];
					$subscribe = $row[1];
					$cname     = dataIO($row[2],'out');
					//入账
					if($orderprice>0){
						$tgbfb1_money=0;$tgbfb2_money=0;
						if($tgbfb1>0)$tgbfb1_money=round($orderprice*($tgbfb1/100),2);
						if($tgbfb2>0)$tgbfb2_money=round($orderprice*($tgbfb2/100),2);
						$orderprice=$orderprice-($tgbfb1_money+$tgbfb2_money);
						if($orderprice>0){
							$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$orderprice WHERE id=".$cid);
							$db->AddLovebRmbList($cid,'出售商品【'.$ptitle.'】买家（ID:'.$tg_uid.'）',$orderprice,'money',16,'tg');
							$tmstr='　账户余额到账￥'.$orderprice;
							TG_shop($oid);
						}
					}
					//站内
					$C = '买家（ID:'.$tg_uid.'）已对商品【'.$ptitle.'】->【'.$fahuokind_str.'】成功'.$tmstr;//　　<a href='.Href('my').' class=aQING>查看详情</a>
					$db->SendTip($cid,'【'.$ptitle.'】买家【'.$fahuokind_str.'】成功',dataIO($C,'in'),'shop');
					//微信
					if (!empty($openid) && $subscribe==1){
						$keyword1 = urlencode('买家（ID:'.$tg_uid.'）已对商品【'.$ptitle.'】->【'.$fahuokind_str.'】成功。'.$tmstr);
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php?f=3&ifadm=1');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
				}
			}
			json_exit(array('flag'=>1,'msg'=>'确认成功，祝您购物愉快!'));
		}else{
			json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		}
	break;
	case 'ajax_fahuo_update'://卖家发货
		if(!ifint($oid))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		if($ifadm!=1)json_exit(array('flag'=>0,'msg'=>'亲，只有【卖家】才可以操作哦'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"flag,tg_uid,pid","id=".$oid." AND cid=".$cook_tg_uid,"num");
		if ($row){
			$flag = $row[0];$tg_uid = $row[1];$pid = $row[2];
			if($flag==1){
				$row = $db->ROW(__TBL_TG_PRODUCT__,"title,fahuokind","id=".$pid,"num");
				$ptitle = dataIO($row[0],'out');
				$fahuokind = ($row[1]==2)?2:1;
				if($fahuokind==2){
					if(empty($c_qhdz))json_exit(array('flag'=>'noaddress','msg'=>'请先设置【取货地址】'));
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=2,fahuotime=".ADDTIME." WHERE cid=".$cook_tg_uid." AND id=".$oid);
				}else{
					if(empty($kuaidi_name))json_exit(array('flag'=>0,'msg'=>'请输入【快递名称】'));
					if(empty($kuaidi_code))json_exit(array('flag'=>0,'msg'=>'请输入【快递运单号】'));
					$kuaidi_name = dataIO($kuaidi_name,'in',50);
					$kuaidi_code = dataIO($kuaidi_code,'in',50);
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=2,fahuotime=".ADDTIME.",kuaidi_name='$kuaidi_name',kuaidi_code='$kuaidi_code' WHERE cid=".$cook_tg_uid." AND id=".$oid);
				}
				//通知买家
				if(ifint($tg_uid)){
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe","id=".$tg_uid,"num");
					$openid    = $row[0];
					$subscribe = $row[1];
					if($fahuokind==2){
						$C = '您购买的商品【'.$ptitle.'】卖家货已备好，请在'.$_SHOP['hdday'].'天内到店取货，取货地址：'.$c_qhdz.$c_qhbz_str.'，超时不取将自动确认核销，取货后请立即【核销确认】';
					}else{
						$C = '您购买的商品【'.$ptitle.'】卖家已发货，【'.$kuaidi_name.'（运单号：'.$kuaidi_code.'）】，请签收后及时【确认收货】';
					}
					//站内
					//$C = '您购买的商品【'.$ptitle.'】卖家已发货，【'.$kuaidi_name.'（运单号：'.$kuaidi_code.'）】，请签收后及时【确认收货】';
					$db->SendTip($tg_uid,'【'.$ptitle.'】卖家已发货',dataIO($C,'in'),'shop');
					//微信
					if (!empty($openid) && $subscribe==1){
						$keyword1 = urlencode($C);
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php?f=2');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
				}
			}
			json_exit(array('flag'=>1,'msg'=>'操作成功'));
		}else{
			json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		}
	break;
	case 'ajax_yuyue_adm_order'://商品预约完成
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		if($ifadm!=1)json_exit(array('flag'=>0,'msg'=>'亲，只有【卖家】才可以操作哦'));
		$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=10,yuyueendtime=".ADDTIME." WHERE flag=9 AND cid=".$cook_tg_uid." AND id=".$id);
		$row = $db->ROW(__TBL_SHOP_ORDER__,"flag,tg_uid,pid","id=".$id,"num");
		if ($row){
			$flag = $row[0];$tg_uid = $row[1];$pid = $row[2];
			if($flag==10 && ifint($tg_uid)){
				//通知买家
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe","id=".$tg_uid,"num");
					$openid    = $row[0];
					$subscribe = $row[1];
					//
					$row = $db->ROW(__TBL_TG_PRODUCT__,"title","id=".$pid,"num");
					$ptitle = dataIO($row[0],'out');
					//站内
					$C = '您预约的【'.$ptitle.'】卖家已经处理成功';
					$db->SendTip($tg_uid,'【'.$ptitle.'】预约处理成功',dataIO($C,'in'),'shop');
					//微信
					if (!empty($openid) && $subscribe==1){
						$keyword1 = urlencode($C);
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'处理成功'));
	break;
	case 'ajax_order_tuikuan'://买家退款
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"orderid,cid","flag=1 AND id=".$id,"num");
		if ($row){
			$orderid=$row[0];$cid=intval($row[1]);
			$row = $db->ROW(__TBL_PAY__,"id,flag,paymoney,title","orderid='$orderid'","num");
			if ($row){
				$payid   = $row[0];
				$payflag = $row[1];
				$paymoney= $row[2];
				$ptitle  = $row[3];
				if($paymoney>0 && $payflag==1){
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=7,tuikuantime=".ADDTIME." WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
					//获取买家信息
					$row      = $db->ROW(__TBL_TG_USER__,"nickname","id=".$cook_tg_uid,"num");
					$nickname = $row[0];
					$nickname = (empty($nickname))?$cook_tg_uid:$nickname;
					//通知卖家处理
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe","id=".$cid,"num");
					$openid    = $row[0];
					$subscribe = $row[1];
					$url=HOST.'/m4/shop_my_order.php?ifadm=1&f=7';
					//站内
					$C = '来自：'.$nickname.'退款申请->【'.$ptitle.'】，请速去处理';
					$db->SendTip($cid,'【'.$ptitle.'】退款申请',$C.'　<a href="'.$url.'" class="btn size2 HONG">点击进入处理</a>','shop');
					//微信
					if (!empty($openid) && $subscribe==1){
						$keyword1 = urlencode($C);
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url       = urlencode($url);
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					json_exit(array('flag'=>1,'msg'=>'申请退款成功，等待卖家处理'));
				}
			}
		}
		json_exit(array('flag'=>0,'msg'=>'亲，订单异常，请联系管理员'));
	break;
	case 'ajax_order_tuikuan_cancel'://买家撤消退款申请
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"orderid,cid","flag=7 AND id=".$id,"num");
		if ($row){
			$orderid=$row[0];$cid=intval($row[1]);
			$row = $db->ROW(__TBL_PAY__,"id,flag,paymoney,title","orderid='$orderid'","num");
			if ($row){
				$payid   = $row[0];
				$payflag = $row[1];
				$paymoney= $row[2];
				$ptitle  = $row[3];
				if($paymoney>0 && $payflag==1){
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=1 WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
					//获取买家信息
					$row      = $db->ROW(__TBL_TG_USER__,"nickname","id=".$cook_tg_uid,"num");
					$nickname = $row[0];
					$nickname = (empty($nickname))?$cook_tg_uid:$nickname;
					//通知卖家处理
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe","id=".$cid,"num");
					$openid    = $row[0];
					$subscribe = $row[1];
					$url=HOST.'/m4/shop_my_order.php?ifadm=1&f=1';
					//站内
					$C = '来自：'.$nickname.'退款申请已撤消->【'.$ptitle.'】，请速去发货吧';
					$db->SendTip($cid,'【'.$nickname.'】退款申请已撤消',$C.'　<a href="'.$url.'" class="btn size2 HONG">点击进入发货</a>','shop');
					//微信
					if (!empty($openid) && $subscribe==1){
						$keyword1 = urlencode($C);
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url       = urlencode($url);
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					//
					json_exit(array('flag'=>1,'msg'=>'撤消成功，等待卖家发货'));
				}
			}
		}
		json_exit(array('flag'=>0,'msg'=>'此订单已处理过了，请不要重复提交'));
	break;
	case 'ajax_order_tuikuan_adm'://卖家同意退款
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"orderid,orderprice,tg_uid,pid","flag=7 AND cid=".$cook_tg_uid." AND id=".$id,"num");
		if ($row){
			$orderid=$row[0];$orderprice=$row[1];$tg_uid=$row[2];$pid=$row[3];
			$row = $db->ROW(__TBL_PAY__,"id,paymoney,trade_no","flag=1 AND trade_no<>'' AND paymoney>0 AND orderid='$orderid'","num");
			if ($row){
				$payid   = $row[0];
				$paymoney= $row[1];
				$transaction_id= $row[2];
				if($paymoney>0 && $orderprice==$paymoney){
					//商品信息
					$row    = $db->ROW(__TBL_TG_PRODUCT__,"title","id=".$pid,"num");
					$ptitle = dataIO($row[0],'out');
					require_once ZEAI.'api/weixin/pay/refund/zeai_refund_func.php';
					$ret=refund($paymoney,$transaction_id);
					if($ret){
						$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=8,tuikuanendtime=".ADDTIME." WHERE id=".$id);
						$db->query("UPDATE ".__TBL_PAY__." SET flag=-2 WHERE id=".$payid);
						notice_tuikuan_tg_uid($tg_uid,$ptitle);
					}else{
						json_exit(array('flag'=>1,'msg'=>'退款操作失败，请联系管理员检查支付参数配置问题'));
					}
				}
			}
		}
		json_exit(array('flag'=>0,'msg'=>'订单状态异常，请联系管理员'));
	break;
	case 'ajax_order_tuihuo'://买家退货
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"pid,cid","flag=2 AND id=".$id,"num");
		if ($row){
			$pid=$row[0];$cid=intval($row[1]);
			//商品信息
			$row    = $db->ROW(__TBL_TG_PRODUCT__,"title,fahuokind","id=".$pid,"num");
			$ptitle = dataIO($row[0],'out');
			$fahuokind=$row[1];
			$fahuokind_str=($fahuokind==2)?'退款':'退货';
			//
			$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=5,tuihuotime=".ADDTIME." WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
			//获取买家信息
			$row      = $db->ROW(__TBL_TG_USER__,"nickname","id=".$cook_tg_uid,"num");
			$nickname = $row[0];
			$nickname = (empty($nickname))?$cook_tg_uid:$nickname;
			//通知卖家处理
			$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe","id=".$cid,"num");
			$openid    = $row[0];
			$subscribe = $row[1];
			$url=HOST.'/m4/shop_my_order.php?ifadm=1&f=5';
			//站内
			$C = '来自：'.$nickname.$fahuokind_str.'申请->【'.$ptitle.'】，请速去处理，好好沟通哦';
			$db->SendTip($cid,'【'.$ptitle.'】'.$fahuokind_str.'申请',$C.'　<a href="'.$url.'" class="btn size2 HONG">点击进入处理</a>','shop');
			//微信
			if (!empty($openid) && $subscribe==1){
				$keyword1 = urlencode($C);
				$keyword3 = urlencode($_ZEAI['siteName']);
				$url      = urlencode($url);
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
			json_exit(array('flag'=>1,'msg'=>'申请'.$fahuokind_str.'成功，等待卖家处理'));
		}
		json_exit(array('flag'=>0,'msg'=>'亲，订单异常，请联系管理员'));
	break;
	case 'ajax_order_tuihuo_cancel'://买家撤消退货申请
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"pid,cid","tg_uid=".$cook_tg_uid." AND flag=5 AND id=".$id,"num");
		if ($row){
			$pid=$row[0];$cid=$row[1];
			//商品信息
			$row    = $db->ROW(__TBL_TG_PRODUCT__,"title,fahuokind","id=".$pid,"num");
			$ptitle = dataIO($row[0],'out');
			$fahuokind=($row[1]==2)?2:1;
			$fahuokind_str=($fahuokind==2)?'退款':'退货';
			//
			$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=2 WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
			//获取买家信息
			$row      = $db->ROW(__TBL_TG_USER__,"nickname","id=".$cook_tg_uid,"num");
			$nickname = $row[0];
			$nickname = (empty($nickname))?$cook_tg_uid:$nickname;
			//通知卖家处理
			$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe","id=".$cid,"num");
			$openid    = $row[0];
			$subscribe = $row[1];
			$url=HOST.'/m4/shop_my_order.php?ifadm=1&f=2';
			//站内
			$C = $nickname.$fahuokind_str.'申请已撤消->【'.$ptitle.'】';
			$db->SendTip($cid,'【'.$nickname.'】'.$fahuokind_str.'申请已撤消',$C.'　<a href="'.$url.'" class="btn size2 HONG">进去看看</a>','shop');
			//微信
			if (!empty($openid) && $subscribe==1){
				$keyword1 = urlencode($C);
				$keyword3 = urlencode($_ZEAI['siteName']);
				$url       = urlencode($url);
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
			//
			json_exit(array('flag'=>1,'msg'=>'撤消成功'));
		}
		json_exit(array('flag'=>0,'msg'=>'此订单已处理过了，请不要重复提交'));
	break;
	case 'ajax_order_tuihuo_adm'://卖家同意退货
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"orderid,orderprice,tg_uid,pid","flag=5 AND cid=".$cook_tg_uid." AND id=".$id,"num");
		if ($row){
			$orderid=$row[0];$orderprice=$row[1];$tg_uid=$row[2];$pid=$row[3];
			//商品信息
			$row    = $db->ROW(__TBL_TG_PRODUCT__,"title,fahuokind","id=".$pid,"num");
			$ptitle = dataIO($row[0],'out');
			$fahuokind=($row[1]==2)?2:1;
			//
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
						$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=6,tuihuoendtime=".ADDTIME." WHERE id=".$id);
						$db->query("UPDATE ".__TBL_PAY__." SET flag=-2 WHERE id=".$payid);
						notice_tuihuo_tg_uid($tg_uid,$ptitle,$fahuokind);
					}else{
						json_exit(array('flag'=>1,'msg'=>'退款操作失败，请联系管理员检查支付参数配置问题'));
					}
				}else{
					$zjtui=true;
				}
			}
			if($orderprice<=0 || $zjtui){
				$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=6,tuihuoendtime=".ADDTIME." WHERE id=".$id);
				notice_tuihuo_tg_uid($tg_uid,$ptitle,$fahuokind);
			}
		}
		json_exit(array('flag'=>0,'msg'=>'订单状态异常，请联系管理员'));
	break;
	case 'shop_my_order_hd'://买家获取码
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$row = $db->ROW(__TBL_SHOP_ORDER__,"pid,hdcode,num,orderprice","flag=2 AND tg_uid=".$cook_tg_uid." AND id=".$id,"num");
		if ($row){
			$pid=$row[0];$hdcode=$row[1];$num=$row[2];$orderprice=number_format($row[3]);
			//商品信息
			$row = $db->ROW(__TBL_TG_PRODUCT__,"title","fahuokind=2 AND id=".$pid,"num");
			if($row){
				$ptitle = trimhtml(dataIO($row[0],'out'));
				if(empty($hdcode)){
					$hdcode=cdstr(8);
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET hdcode='$hdcode' WHERE id=".$id);
				}
				json_exit(array('flag'=>1,'hdcode'=>$hdcode,'hdcode'=>$hdcode,'ptitle'=>$ptitle,'num'=>$num,'orderprice'=>'￥'.$orderprice));
			}
		}
		json_exit(array('flag'=>0,'msg'=>'订单异常'));
	break;
	case 'shop_my_order_hdAdmFn'://卖家验证核销
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$cid=$cook_tg_uid;
		$row = $db->ROW(__TBL_SHOP_ORDER__,"pid,hdcode,num,orderprice,tg_uid,tgbfb1,tgbfb2","flag=2 AND cid=".$cid." AND id=".$id,"num");
		if ($row){
			$pid=$row[0];$hdcode=$row[1];$num=$row[2];$orderprice=$row[3];$tg_uid=intval($row[4]);$tgbfb1= $row[5];$tgbfb2= $row[6];
			//商品信息
			$row = $db->ROW(__TBL_TG_PRODUCT__,"title","fahuokind=2 AND id=".$pid,"num");
			if($row){
				$ptitle = trimhtml(dataIO($row[0],'out'));
				if(empty($hdcode)){
					json_exit(array('flag'=>0,'msg'=>'买家核销码还未生成，请向买家索要'));
				}else{
					if($form_hdcode!=$hdcode)json_exit(array('flag'=>0,'msg'=>'核销码验证错误，请检查'));
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=3,endtime=".ADDTIME." WHERE cid=".$cid." AND id=".$id);
					//卖家信息
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe,title","id=".$cid,"num");
					$c_openid    = $row[0];
					$c_subscribe = $row[1];
					$c_name      = dataIO($row[2],'out');
					//买家信息
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe,nickname","id=".$tg_uid,"num");
					$tg_openid    = $row[0];
					$tg_subscribe = $row[1];
					$tg_nickname  = dataIO($row[2],'out');
					/*****通卖****/
					//入账
					if($orderprice>0){
						$tgbfb1_money=0;$tgbfb2_money=0;
						if($tgbfb1>0)$tgbfb1_money=round($orderprice*($tgbfb1/100),2);
						if($tgbfb2>0)$tgbfb2_money=round($orderprice*($tgbfb2/100),2);
						$orderprice=$orderprice-($tgbfb1_money+$tgbfb2_money);
						if($orderprice>0){
							$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$orderprice WHERE id=".$cid);
							$db->AddLovebRmbList($cid,'出售商品【'.$ptitle.'】买家（'.$tg_nickname.'，ID:'.$tg_uid.'）',$orderprice,'money',16,'tg');
							$tmstr='　账户余额到账￥'.$orderprice;
							TG_shop($id);
						}
					}
					//站内
					$C = '商品【'.$ptitle.'】->【核销】成功'.$tmstr.'，买家（'.$tg_nickname.'，ID:'.$tg_uid.'）';$db->SendTip($cid,'【'.$ptitle.'】【核销】成功',dataIO($C,'in'),'shop');
					//微信
					if (!empty($c_openid) && $c_subscribe==1){
						$keyword1 = urlencode('商品【'.$ptitle.'】->【核销】成功！买家（'.$tg_nickname.'，ID:'.$tg_uid.'）'.$tmstr);
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php?f=3&ifadm=1');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$c_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					/*****通买****/
					//站内
					$C = '商品【'.$ptitle.'】->【核销】成功，卖家（'.$c_name.'）';$db->SendTip($tg_uid,'【'.$ptitle.'】【核销】成功',dataIO($C,'in'),'shop');
					//微信
					if (!empty($tg_openid) && $tg_subscribe==1){
						$keyword1 = urlencode('商品【'.$ptitle.'】->【核销】成功！卖家（'.$c_name.'）');
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php?f=3');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$tg_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					json_exit(array('flag'=>1,'msg'=>'核销成功'));
				}
			}
		}
		json_exit(array('flag'=>0,'msg'=>'订单异常'));
	break;
	case 'ajax_order_count':
		$jumpli=array(3,4,6,8,10);
		for($i=0;$i<=10;$i++) {
			if(!in_array($i,$jumpli)){
				$num_arr[]=$db->COUNT(__TBL_SHOP_ORDER__,"flag=$i AND ".$SQLNUM);
			}else{
				$num_arr[]=0;
			}
		}
		json_exit(array('flag'=>1,'num_arr'=>$num_arr));
	break;
}
function notice_tuikuan_tg_uid($tg_uid,$ptitle) {
	global $db,$_ZEAI;
	/**通知买家**/
	$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe","id=".$tg_uid,"num");$openid = $row[0];$subscribe = $row[1];
	$C = '退款成功->【'.$ptitle.'】';$db->SendTip($tg_uid,'【'.$ptitle.'】退款成功',$C,'shop');
	$url=HOST.'/m4/shop_my_order.php?f=8';
	//微信
	if (!empty($openid) && $subscribe==1){
		$keyword1 = urlencode($C);
		$keyword3 = urlencode($_ZEAI['siteName']);
		@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.urlencode($url));
	}
	json_exit(array('flag'=>1,'msg'=>'退款成功','url'=>$url.'&ifadm=1'));
}
function notice_tuihuo_tg_uid($tg_uid,$ptitle,$fahuokind) {
	global $db,$_ZEAI;
	$fahuokind_str=($fahuokind==2)?'退款':'退货';
	/**通知买家**/
	$row    = $db->ROW(__TBL_TG_USER__,"openid,subscribe","id=".$tg_uid,"num");
	$openid = $row[0];$subscribe = $row[1];
	//站内
	$C = $fahuokind_str.'成功->【'.$ptitle.'】';
	$db->SendTip($tg_uid,'【'.$ptitle.'】'.$fahuokind_str.'成功',$C,'shop');
	//微信
	if (!empty($openid) && $subscribe==1){
		$keyword1 = urlencode($C);
		$keyword3 = urlencode($_ZEAI['siteName']);
		$url      = urlencode(HOST.'/m4/shop_my_order.php?f=6');
		@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
	}
	json_exit(array('flag'=>1,'msg'=>$fahuokind_str.'成功'));
}
function rows_ulist($rows,$p) {
	global $_ZEAI,$t,$db,$_SHOP,$ifadm;
	$O = '';
	$id     = $rows['id'];
	$flag   = $rows['flag'];
	$cid    = $rows['cid'];
	$pid    = $rows['pid'];
	$tg_uid = $rows['tg_uid'];
	$price      = $rows['price'];
	$num        = $rows['num'];
	$orderid    = $rows['orderid'];
	$orderkind  = $rows['orderkind'];
	$orderprice = $rows['orderprice'];
	$address  = trimhtml(dataIO($rows['address'],'out'));
	$truename = trimhtml(dataIO($rows['truename'],'out'));
	$mob      = trimhtml(dataIO($rows['mob'],'out'));
	$bz       = trimhtml(dataIO($rows['bz'],'out'));
	$addtime_str = YmdHis($rows['addtime'],'YmdHi');
	$tuihuotime = $rows['tuihuotime'];
	$fahuotime  = $rows['fahuotime'];
	$tuikuantime= $rows['tuikuantime'];
	$price_str=($orderprice>0)?'实付金额：<font class="C666">¥'.str_replace(".00","",number_format($orderprice,2)).'</font>':'<font class="C090">免费</font>';
	$href=HOST.'/m4/shop_goods_detail.php?id='.$pid;
	//
	$row  = $db->ROW(__TBL_TG_PRODUCT__,"title,path_s,fahuokind","id=".$pid,"name");
	$path_s= $row['path_s'];
	$title = dataIO($row['title'],'out');
	$fahuokind=($row['fahuokind']==2)?2:1;
	$path_s_url = (!empty($path_s))?$_ZEAI['up2'].'/'.$path_s:HOST.'/res/noP.gif';
	$img_str    = '<img src="'.$path_s_url.'">';
	//
	$O .= '<ul>';
	$O .= '<div class="shop">';
	if($ifadm==1){
		$row  = $db->ROW(__TBL_TG_USER__,"uname,nickname,photo_s,mob","id=".$tg_uid,"name");
		$uname    = $row['uname'];
		$photo_s  = $row['photo_s'];
		$nickname  = dataIO($row['nickname'],'out');
		$mob2      = dataIO($row['mob'],'out');
		$nickname  = (empty($nickname))?$uname:$nickname;
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m.jpg';
		$O .= '<img src="'.$photo_s_url.'"><span>'.$nickname.'（ID:'.$tg_uid.'）</span>';
	}else{
		$row  = $db->ROW(__TBL_TG_USER__,"title,photo_s,qhdz,qhbz,tel,qhlongitude,qhlatitude","id=".$cid,"name");
		$photo_s= $row['photo_s'];
		$cname  = dataIO($row['title'],'out');
		$qhdz = dataIO($row['qhdz'],'out');
		$qhbz = dataIO($row['qhbz'],'out');
		$mob  = dataIO($row['tel'],'out');
		$qhlongitude= $row['qhlongitude'];
		$qhlatitude = $row['qhlatitude'];
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/noP.gif';
		$hrefc=HOST.'/m4/shop_detail.php?id='.$cid;
		$O .= '<img src="'.$photo_s_url.'" onClick="zeai.openurl(\''.$hrefc.'\');"><span>'.$cname.'</span>';//<a href="tel:'.$ctel.'"><i class="ico">&#xe60e;</i></a>
	}
	$flagstr=($ifadm==1)?'v':'v3';
	$O .= '<span>'.arrT($_SHOP['orderflag'],$flag,$flagstr).$orderkind_str.'</span>';
	$O .= '</div>';
	if($flag==2){//等待买家确认收货
		if($fahuokind==2){
			$qrshday = $_SHOP['hdday'];
			$qrshday_str='自动确认核销';
		}else{
			$qrshday = $_SHOP['qrshday'];
			$qrshday_str='自动确认收货';
		}
		$djs_str=zeai_djs($fahuotime+intval($qrshday)*86400);
		if($djs_str){
			$O .= '<div class="djs">';
			$O .= '<i class="ico">&#xe634;</i>'.$djs_str.$qrshday_str;//604800
			$O .= '</div>';
		}
	}elseif($flag==5){//买家申请退货中
		if($fahuokind==2){
			$fahuokind_str='退款';
			$autoday=$_SHOP['hdday'];
		}else{
			$fahuokind_str='退货';
			$autoday=$_SHOP['thday'];
		}
		$djs_str=zeai_djs($tuihuotime+intval($autoday)*86400);
		if($djs_str){
			$O .= '<div class="djs djs5">';
			$O .= '<i class="ico">&#xe634;</i>'.$djs_str.' 自动确认'.$fahuokind_str.$autoday;//604800
			$O .= '</div>';
		}
	}elseif($flag==7){//买家申请退款中
		$djs_str=zeai_djs($tuikuantime+intval($_SHOP['tkday'])*86400);
		if($djs_str){
			$O .= '<div class="djs djs7">';
			$O .= '<i class="ico">&#xe634;</i>'.$djs_str.' 自动确认退款';//604800
			$O .= '</div>';
		}
	}
	if($ifadm==1){// && $flag==1
		if($fahuokind==2){
			$sjrstr .= (!empty($mob2))?'电话：'.$mob2:'\r\n';
			$sjrstr .= (!empty($bz))?'备注：'.$bz:'';
			if(!empty($mob2) || !empty($bz)){
				$O .= '<div class="shouhuoinfo">';
				$O .= '<li>';
				$O .= (!empty($mob2))?'电话：<b><a href="tel:'.$mob2.'">'.$mob2.'</a></b>':'';
				$O .= '<a class="btn size1 BAI" onclick="zeai.copy(\''.$sjrstr.'\',function(){zeai.msg(\'复制成功\');})">复制文本</a></li>';
				$O .= (!empty($bz))?'<li>备注：<b>'.$bz.'</b></li>':'';
				$O .= '</div>';
			}
		}else{
			$sjrstr  = '地址：'.$address.'\r\n';
			$sjrstr .= '电话：'.$mob.'\r\n';
			$sjrstr .= '联系人：'.$truename.'\r\n';
			$sjrstr .= (!empty($bz))?'备注：'.$bz:'';
			$O .= '<div class="shouhuoinfo">';
			$O .= '<li>地址：<b>'.$address.'</b></li>';
			$O .= '<li>电话：<b><a href="tel:'.$mob.'">'.$mob.'</a>（'.$truename.'）</b><a class="btn size1 BAI" onclick="zeai.copy(\''.$sjrstr.'\',function(){zeai.msg(\'复制成功\');})">复制文本</a></li>';
			$O .= (!empty($bz))?'<li>备注：<b>'.$bz.'</b></li>':'';
			$O .= '</div>';
		}
	}else{
		if($fahuokind==2){
			$sjrstr  = '取货地址：'.$qhdz.'\r\n';
			$sjrstr .= '卖家电话：'.$mob.'\r\n';
			$sjrstr .= (!empty($qhbz))?'备注：'.$qhbz:'';
			if(!empty($qhlongitude) && !empty($qhlatitude)){
				$onclick=' onclick="openmap('.$qhlongitude.','.$qhlatitude.',\''.$cname.'\',\''.$qhdz.'\')"';
			}else{
				$onclick="";	
			}
			$O .= '<div class="shouhuoinfo">';
			$O .= '<li>取货地址：<b>'.$qhdz.'</b> <i class="ico S16 C09f"'.$onclick.'>&#xe614;</i></li>';
			$O .= '<li>卖家电话：<b><a href="tel:'.$mob.'">'.$mob.'</a></b><a class="btn size1 BAI" onclick="zeai.copy(\''.$sjrstr.'\',function(){zeai.msg(\'复制成功\');})">复制文本</a></li>';
			$O .= (!empty($qhbz))?'<li>取货备注：<b>'.$qhbz.'</b></li>':'';
			$O .= '</div>';
		}
	}
	$fahuokind_str=($fahuokind==2)?'<div class="fahuokind">线下取货</div>':'';
	$O .= '<dl>';
	$O .= '<dt onClick="zeai.openurl(\''.$href.'\');">'.$img_str.$fahuokind_str.'</dt>';
	$O .= '<dd onClick="zeai.openurl(\''.$href.'\');"><h4>'.$title.'</h4><h5>订单号：'.$orderid.'<br>共<font class="C666">'.$num.'</font>件商品　'.$price_str.'</h5></dd>';
	$O .= '<div class="clear"></div></dl>';
	$O .= '<div class="time"><span>'.$addtime_str.'</span><em>';
	switch ($flag) {
		case 0://未付款
			if($ifadm!=1){
				$b = '<button class="btn size2 BAI" onClick="shop_my_order_cancel('.$id.')">取消订单</button>';
				if($orderprice>0)$b.='<button class="btn size2 LV2" onClick="shop_my_order_payFn('.$id.')"><i class="ico">&#xe6b7;</i> 立即付款</button>';
			}
		break;
		case 1://已付款
			if($ifadm==1){
				if($fahuokind==2){
					$b = '<button class="btn size2 LAN" onClick="shop_my_order_fahuoBoxFn('.$id.',2)">开始发货</button>';
				}else{
					$b = '<button class="btn size2 LAN" onClick="shop_my_order_fahuoBoxFn('.$id.',1)">开始发货</button>';
				}
			}else{
				$b = '<button class="btn size2 BAI" onClick="shop_my_order_tuikuanFn('.$id.')">我要退款</button>';
			}
		break;
		case 2://已发货	
			if($ifadm==1){
				if($fahuokind==2){
					$b = '<button class="btn size2 M5" style="background-color:#f60" onClick="shop_my_order_hdAdmFn('.$id.')">开始核销</button>';
				}else{
					$b = '<button class="btn size2 BAI M5" onClick="shop_my_order_wuliufoFn('.$id.')">查看物流</button>';
				}
			}else{
				$orderprice_str=($orderprice>0)?' 费用￥'.number_format($orderprice).'将打给卖家':'';
				if($fahuokind==2){
					$b.='<button class="btn size2 BAI M5" onClick="shop_my_order_tuihuoFn('.$id.',\'确定【退款】么，没得商量了？会影响您的信誉哦\')">退款</button>';
					$b.='<button class="btn size2 BAI M5" style="border-color:#FF6600;color:#FF6600" onClick="shop_my_order_hdFn('.$id.',\'提供核销码给卖家，由卖家来核销确认\')">核销码</button>';
					$title='确定【已收货立即核销】么？'.$orderprice_str;
					$b.='<button class="btn size2 HONG M5" style="background-color:#f60" onClick="shop_my_order_flag3Fn('.$id.',\''.$title.'\')">确认收货</button>';
				}else{
					$b.='<button class="btn size2 BAI M5" onClick="shop_my_order_tuihuoFn('.$id.',\'确定【退货】么，没得商量了？会影响您的信誉哦\')">退货</button>';
					$b.= '<button class="btn size2 BAI M5" onClick="shop_my_order_wuliufoFn('.$id.')">物流</button>';
					$title='确定【确认收货】么？'.$orderprice_str;
					$b.='<button class="btn size2 HONG M5" style="background-color:#f60" onClick="shop_my_order_flag3Fn('.$id.',\''.$title.'\')">我要确认收货</button>';
				}
			}
		break;
		case 3:$b = '';break;//交易已完成
		case 4:$b = '订单已关闭';break;
		case 5://买家申请退货中
			$fahuokind_str=($fahuokind==2)?'退款':'退货';
			if($ifadm==1){
				$b = '<button class="btn size2 LAN" style="background-color:#A56BA7" onClick="shop_my_order_tuihuoAdmFn('.$id.',\'确定同意【'.$fahuokind_str.'】么？费用将自动返还买家\')">同意'.$fahuokind_str.'</button>';
			}else{
				$b = '<button class="btn size2 LAN" style="background-color:#A56BA7" onClick="shop_my_order_tuihuo_cancelFn('.$id.',\'确定 撤消【'.$fahuokind_str.'申请】么？\')">撤消'.$fahuokind_str.'申请</button>';
			}
		break;
		case 6:$b = '退货完成';break;
		case 7://买家申请退款中
			if($ifadm==1){
				$b.='<button class="btn size2 LAN" onClick="shop_my_order_tuikuanAdmFn('.$id.')">同意退款</button>';
			}else{
				$b.='<button class="btn size2 LAN" style="background-color:#5f77f0" onClick="shop_my_order_tuikuan_cancelFn('.$id.')">撤消退款申请</button>';
			}
		break;
		case 8:
			
		break;
		case 9://预约待处理
			if($ifadm==1){
				$b = '<button class="btn size2 HONG" onClick="yuyue_adm_orderFn('.$id.')">点击处理</button>';
			}
		break;
		case 10://预约完成
		
		break;
	}
	$O .= $b;
	$O .= '</em></div>';
	$O .= '<div class="clear"></div></ul>';
	return $O;
}
$ZEAI_SQL .= ( $f=='0' || ifint($f) )?" AND flag=".$f:"";
if($ifadm_new==1)$ZEAI_SQL .= " AND (flag=0 OR flag=1 OR flag=2 OR flag=5 OR flag=7 OR flag=9) ";
$key = trimhtml($key);
if(!empty($key)){
	if($ifadm==1){
		$ZEAI_SQL = "cid=".$cook_tg_uid;
	}else{
		$ZEAI_SQL = "tg_uid=".$cook_tg_uid;
	}
	$rt = $db->query("SELECT id FROM ".__TBL_TG_PRODUCT__." WHERE title LIKE '%".$key."%'");
	while($tmprows = $db->fetch_array($rt,'name')){$parr[]=$tmprows['id'];}
	if(is_array($parr) && count($parr)>0){
		$plist = implode(',',$parr);
		$plistSQL = " OR pid IN($plist) ";
	}
	$ZEAI_SQL .= " AND (orderid='$key' ".$plistSQL.") ";
}
$ZEAI_SELECT= "SELECT id,cid,pid,tg_uid,price,flag,orderid,orderprice,addtime,num,address,truename,mob,orderkind,bz,tuihuotime,fahuotime,tuikuantime FROM ".__TBL_SHOP_ORDER__." WHERE ".$ZEAI_SQL." ORDER BY id DESC";
$ZEAI_total_SQL = "SELECT COUNT(*) FROM ".__TBL_SHOP_ORDER__." WHERE ".$ZEAI_SQL;
if($submitok=='ZEAI_list'){exit(Zeai_ajax_list_fn($ZEAI_totalP,$p));}
$ZEAI_total = $db->query($ZEAI_total_SQL);
$ZEAI_total = $db->fetch_array($ZEAI_total);
$ZEAI_total = $ZEAI_total[0];
$ZEAI_totalP= ceil($ZEAI_total/$_ZEAI['pagesize']);?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/shop.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php 
$url=HOST.'/m4/shop_my.php';
$mini_title = '<i class="ico goback" onClick="zeai.openurl(\''.$url.'\');">&#xe602;</i>　我的订单<span class="ico orderso off" id="orderso">&#xe6c4;</span>';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<div class="order_search" id="order_search">
	<form id="zeai_cn__form_search" action="shop_my_order.php">
        <input type="text" name="key" id="key" class="input" placeholder="请输入商品名称/订单号"><button type="button" id="sobtn"><i class="ico">&#xe6c4;</i></button>
        <input type="hidden" name="ifadm" value="<?php echo $ifadm;?>">
    </form>
</div>
<div class="shop_my_order_tab" id="shop_my_order_tab">
    <em>
        <a href="shop_my_order.php?f=0&ifadm=<?php echo $ifadm;?>"<?php echo ($f == '0')?' class="ed"':'';?>><i class="ico2">&#xe649;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],0,'v2'));?></span></a>
        <a href="shop_my_order.php?f=1&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 1)?' class="ed"':'';?>><i class="ico2">&#xe615;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],1,'v2'));?></span></a>
        <a href="shop_my_order.php?f=2&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 2)?' class="ed"':'';?>><i class="ico2">&#xe68e;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],2,'v2'));?></span></a>
        <a href="shop_my_order.php?f=3&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 3)?' class="ed"':'';?>><i class="ico2">&#xe628;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],3,'v2'));?></span></a>
        
        <div class="tabmore" id="tabmore">
        <a href="shop_my_order.php?f=4&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 4)?' class="ed"':'';?>><i class="ico" style="font-size:34px;line-height:34px">&#xe65b;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],4,'v2'));?></span></a>
        <a href="shop_my_order.php?f=5&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 5)?' class="ed"':'';?>><i class="ico2">&#xe6cf;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],5,'v2'));?></span></a>
        <a href="shop_my_order.php?f=6&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 6)?' class="ed"':'';?>><i class="ico2">&#xe6cf;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],6,'v2'));?></span></a>
        <a href="shop_my_order.php?f=7&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 7)?' class="ed"':'';?>><i class="ico2">&#xe767;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],7,'v2'));?></span></a>
        <a href="shop_my_order.php?f=8&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 8)?' class="ed"':'';?>><i class="ico2">&#xe767;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],8,'v2'));?></span></a>
        <a href="shop_my_order.php?f=9&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 9)?' class="ed"':'';?>><i class="ico2">&#xe61e;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],9,'v2'));?></span></a>
        <a href="shop_my_order.php?f=10&ifadm=<?php echo $ifadm;?>"<?php echo ($f == 10)?' class="ed"':'';?>><i class="ico2">&#xe61e;</i><span><?php echo strip_tags(arrT($_SHOP['orderflag'],10,'v2'));?></span></a>
        </div>
        <div class="clear"></div>
    </em>
    <i class="ico off tabmorebtn" onClick="morebtn(this);">&#xe60b;</i>
</div>
<div class="shop_my_order" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div>
<script src="<?php echo HOST;?>/api/zeai_PAY.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var ifadm=<?php echo intval($ifadm);?>,ifadm_new=<?php echo intval($ifadm_new);?>;
<?php
if ($ZEAI_total > $_ZEAI['pagesize'] && $t != 2){?>
var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2;
zeaiOnscroll_json={url:'shop_my_order'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,f:<?php echo intval($f);?>,ifadm:ifadm,ifadm_new:ifadm_new}};
document.body.onscroll = zeaiOnscroll;
<?php }?>
</script>
<div id="shop_my_order_fahuoBox" class="shop_my_order_fahuoBox">
    <h1>填写物流信息</h1>
    <form id="wwwyzlovecom_form">
    <input type="text" name="kuaidi_name" id="kuaidi_name" class="input" placeholder="请输入快递名称" onBlur="zeai.setScrollTop(0);" maxlength="50" autocomplete="off" >
    <input type="text" name="kuaidi_code" id="kuaidi_code" class="input" placeholder="请输入快递单号" onBlur="zeai.setScrollTop(0);" maxlength="50" autocomplete="off" >
    <input type="hidden" name="submitok" value="ajax_fahuo_update">
    <input type="hidden" name="ifadm" value="<?php echo $ifadm;?>">
    <input type="hidden" name="oid" id="oid" value="z+e+a+i__c+n">
    <button type="button" class="btn size4 HONG3 yuan" id="shop_my_order_fahuoBtn" onClick="shop_my_order_fahuoFn();">开始发货</button>
    </form>
</div>
<div id="shop_my_order_wuliufoBox" class="shop_my_order_wlinfo" style="display:none">
	<h1>订单号<br><b id="orderid">--</b></h1>
    <li><i class="ico2 i1">&#xe68e;</i>快递名称：<span id="kdname">--</span></li>
    <li><i class="ico2 i2">&#xe628;</i>快递单号：<span id="kdcode" onclick="zeai.copy(this.innerHTML,function(){zeai.msg('快递单号复制成功');})">--</span></li>
    <li><i class="ico2 i3">&#xe7c1;</i>卖家电话：<span id="mjtel">--</span></li>
</div>
<script>
setTimeout(function(){
	zeai.ajax({url:'shop_my_order'+zeai.extname,data:{submitok:'ajax_order_count',ifadm:ifadm}},function(e){var rs=zeai.jsoneval(e);
		if(rs.flag==1){
			zeai.listEach(zeai.tag(shop_my_order_tab,'a'),function(a,n){
				b=parseInt(num_arr(rs.num_arr,n));
				if(b>0)a.append('<b>'+b+'</b>');
			});
		}
	});	
},200);
function num_arr(arr,n){var l=arr.length;for(var k=0;k<l;k++){if(k==n)return arr[k];}}
function morebtn(btnobj){
	if(btnobj.hasClass('off')){
		btnobj.removeClass('off');
		btnobj.addClass('on');
		btnobj.html('&#xe60a;');
		tabmore.show();
	}else{
		btnobj.removeClass('on');
		btnobj.addClass('off');
		btnobj.html('&#xe60b;');
		tabmore.hide();
	}
}
orderso.onclick=function(){
	if(orderso.hasClass('off')){
		orderso.removeClass('off');
		orderso.addClass('on');
		order_search.show();
	}else{
		orderso.removeClass('on');
		orderso.addClass('off');
		order_search.hide();
	}
}
sobtn.onclick=function(){
	if(zeai.empty(key.value)){
		zeai.msg('请输入商品名称/订单号');
		return false;
	}else{
		zeai_cn__form_search.submit();
	}
}
</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr:'<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList:['getLocation','openLocation']});
	function openmap (lng,lat,title,address){
		var newgps=b_t(lng,lat);
		lng=parseFloat(newgps[0]);lat=parseFloat(newgps[1]);
		wx.openLocation({
			latitude:lat,
			longitude:lng,
			name:title,
			address:address,
			scale:14,
			infoUrl:'http://weixin.qq.com'
		});
	}
	function b_t(lng,lat) {
		if (lng == null || lng == '' || lat == null || lat == '')return [lng, lat];
		var x_pi = 3.14159265358979324;
		var x = parseFloat(lng) - 0.0065;
		var y = parseFloat(lat) - 0.006;
		var z = Math.sqrt(x * x + y * y) - 0.00002 * Math.sin(y * x_pi);
		var theta = Math.atan2(y, x) - 0.000003 * Math.cos(x * x_pi);
		var lng = (z * Math.cos(theta)).toFixed(7);
		var lat = (z * Math.sin(theta)).toFixed(7);
		return [lng,lat];
	}	
	</script>
<?php }else{?>
	<script>function openmap (lng,lat,title,address){
		zeai.openurl('http://api.map.baidu.com/marker?location='+lat+','+lng+'&title='+address+'&content='+title+'&output=html');
	}</script>
<?php }?>
<div id="backtop"><a href="#top" id="btmTopBtn"><i class="ico">&#xe60a;</i>顶部</a></div>
</body>
</html>
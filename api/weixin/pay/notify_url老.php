<?php
require_once '../../../sub/init.php';
require_once ZEAI."sub/conn.php";
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_pay.php';
include_once("WxPayPubHelper/WxPayPubHelper.php");
function FromXml($xml){	
	if(!$xml){exit("xml数据异常！");}
	$arr_val = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
	return $arr_val;
}
$notify = new Notify_pub();
$xml = file_get_contents("php://input");
$notify->saveData($xml);	
if($notify->checkSign() == FALSE){
	$notify->setReturnParameter("return_code","FAIL");//返回状态码
	$notify->setReturnParameter("return_msg","ZEAI SIGN FAIL");//返回信息签名失败
}else{
	$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
}
$returnXml = $notify->returnXml();echo $returnXml;

if($notify->checkSign() == TRUE){
	if ($notify->data["return_code"] == "FAIL") {
		//【通信出错】
	}elseif($notify->data["result_code"] == "FAIL"){
		//业务出错
	}else{
		//支付成功
		$data_ok = FromXml($xml);
		
		gyl_debug(json_encode($data_ok));
		
		
		//此处应该更新一下订单状态，商户自行增删操作
		/*
		$data_ok['appid']="wxe9f8bac77c360e5a";   					//商用公众号 AppID
		$data_ok['bank_type']="CFT";    							//付款银行
		$data_ok['cash_fee']="1";      								//现金支付金额
		$data_ok['fee_type']="CNY";   								//人民币
		$data_ok['is_subscribe']="Y";  								//是否关注公众账号  Y-关注，N-未关注
		$data_ok['mch_id']="1268050901";  							//商户号
		$data_ok['openid']="o8UNHxH0gFrsRn9Tc6dMKPYaQ7nU";  		//用户在商户appid下的唯一标识
		$data_ok['out_trade_no']="ld20150908192218_";    			//商户订单号
		$data_ok['time_end']="20150908192230";   					//支付完成时间
		$data_ok['total_fee']="1";     								//订单总金额，单位为分
		$data_ok['transaction_id']="1001920605201509080822073810";  //微信支付订单号			
		*/
		//在此得下面开始处理订单支付完成后的信息++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		$orderid      = $data_ok['out_trade_no'];
		$pay_trade_no = $data_ok['transaction_id'];
		$pay_money    = intval($data_ok['cash_fee'])/100;//单位转成元
		//kind：1:升级,2:loveb充值,3:余额充值,4:提现	
		
		$rowpay = $db->ROW(__TBL_PAY__,"id,uid,kind,title,openid,money","orderid='$orderid' AND flag=0 LIMIT 1",'num');
		if ($rowpay){
			$payid      = $rowpay[0];
			$uid        = intval($rowpay[1]);
			$kind       = $rowpay[2];
			$orderid_title = $rowpay[3];
			$openid     = $rowpay[4];
			$money      = $rowpay[5];
		}else{exit;}
		switch ($kind){
			//1:升级
			case 1:
			
			
			
			
			break;
			
			//loveb充值
			case 2:
				$row = $db->NAME($uid,'nickname,loveb');
				if(!$row){exit;}else{
					$data_nickname = dataIO($row['nickname'],'out');
					$addloveb   = $money*100;
					$endloveb   = $addloveb+$row['loveb'];
				}
				$db->query("UPDATE ".__TBL_PAY__." SET trade_no='$pay_trade_no',paytime='".ADDTIME."',flag=1,paymoney='$pay_money' WHERE id=".$payid);
				$db->query("UPDATE ".__TBL_USER__." SET loveb=$endloveb WHERE id=".$uid);
				//爱豆清单入库
				$db->AddLovebRmbList($uid,$orderid_title,$addloveb,'loveb',2);	
				//爱豆站内消息
				$C = $data_nickname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！<br><br><br><a href='.HOST.'/?z=my&e=my_loveb class=aHUI>查看详情</a>';
				$db->SendTip($uid,$orderid_title,dataIO($C,'in'),'sys');
				//爱豆到账提醒(微信模版)
				if (!empty($openid)){
					$first   = urlencode($data_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
					$content = urlencode($orderid_title);
					$url     = urlencode(HOST.'/?z=my&e=my_loveb');
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$openid.'&num='.$addloveb.'&first='.$first.'&content='.$content.'&url='.$url);
				}
				//写支付日志
				gyl_log(' 微信JSAPI支付 -> uid：'.$uid.'｜'.$orderid_title.'｜ ¥'.$pay_money.'｜充值'.$_ZEAI['loveB'].$addloveb.'个');
			break;
			
			
			//余额充值
			case 3:
			
			break;
		}
			/*			
			if($kind == 'sj'){
				$arr   = substr($paykind,2);
				$arr   = explode('_',$arr);
				$grade = intval($arr[0]);$if2 = intval($arr[1]);
				$rt_dd    = $db->query("SELECT id,uid,title,openid FROM ".__TBL_PAY__." WHERE orderid='$orderid' AND flag=0 LIMIT 1");
				$total_dd = $db->num_rows($rt_dd);
				if($total_dd==1){
					$row_dd     = $db->fetch_array($rt_dd);
					$pay_id     = $row_dd[0];
					$uid        = intval($row_dd[1]);
					$pay_title  = $row_dd[2];
					$openid     = $row_dd[3];
					$pay_money  = intval($data_ok['cash_fee'])/100; //单位转成元
					//$pay_money  = sprintf("%.2f",$pay_money);       //格式化金额	
					//
					$row = $db->ROW($uid,'nickname');
					if(!$row){
						exit;
					}else{
						$nickname = strip_tags($row['nickname']);
					}
					$db->query("UPDATE ".__TBL_PAY__." SET money=".$pay_money.",trade_no='$pay_trade_no',pay_time='".$ADDTIME."',flag=1 WHERE id=".$pay_id);
					$db->query("UPDATE ".__TBL_USER__." SET if2=".$if2.",sjtime=".$ADDTIME.",grade=".$grade." WHERE id=".$uid);
					//消息通知
					$tip_title   = $pay_title.'，升级成功！';
					$tip_content = $tip_title.'<br><a href="/my/vip.php" class="blue FR">进入查看</a>';
					$db->SendTip($uid,$tip_title,$tip_content);
					//微信通知
					$keyword1  = urlencode($nickname.",恭喜你会员升级成功！");
					$keyword3  = urlencode('客服中心');
					$url       = $_ZEAI['m_2domain']."/my/vip.php";
					wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					//写日志
					gyl_debug('　会员ID号：'.$uid.'，'.$pay_title.'支付了 ¥'.$pay_money.' 元(升级)');
					//
					exit;
				}
				
				
			//红娘升级
			}elseif($kind == 'hn'){
				$arr   = substr($paykind,2);$arr = explode('_',$arr);
				$grade = intval($arr[0]);$if2 = intval($arr[1]);
				$rt_dd = $db->query("SELECT id,uid,title,openid FROM ".__TBL_PAY__." WHERE orderid='$orderid' AND flag=0 LIMIT 1");
				$total_dd = $db->num_rows($rt_dd);
				if($total_dd==1){
					$row_dd     = $db->fetch_array($rt_dd);
					$pay_id     = $row_dd[0];
					$hid        = intval($row_dd[1]);
					$pay_title  = $row_dd[2];
					$openid     = $row_dd[3];
					$pay_money= intval($data_ok['total_fee'])/100; //单位转成元
					//
					$db->query("UPDATE ".__TBL_PAY__." SET money=".$pay_money.",trade_no='$pay_trade_no',pay_time='".$ADDTIME."',flag=1 WHERE id=".$pay_id);
					$db->query("UPDATE ".__TBL_HN__." SET if2=".$if2.",sjtime=".$ADDTIME.",grade=".$grade." WHERE id=".$hid);
					//消息通知
					$tip_title   = $pay_title.'，升级成功！';
					$tip_content = $tip_title.'<br><a href='.$_ZEAI['hn_2domain'].'"/my_vip.php" class="blue FR">进入查看</a>';
					$db->SendTip($uid,$tip_title,$tip_content,4); 
					//写日志
					gyl_debug('　红娘ID号：'.$hid.'，'.$pay_title.'支付了 ¥'.$pay_money.' 元(红娘升级)');exit;
				}
			//红包充值
			}elseif($kind == 'hb'){
				$rt_dd = $db->query("SELECT id,uid,title,openid FROM ".__TBL_PAY__." WHERE orderid='$orderid' AND flag=0 LIMIT 1");
				$total_dd = $db->num_rows($rt_dd);
				if($total_dd==1){
					$row_dd     = $db->fetch_array($rt_dd);
					$pay_id     = $row_dd[0];
					$uid        = intval($row_dd[1]);
					$pay_title  = $row_dd[2];
					$openid     = $row_dd[3];
					//
					$row = $db->ROW($uid,'nickname,money');
					if(!$row){
						exit;
					}else{
						$nickname = strip_tags($row['nickname']);
						$pay_money= intval($data_ok['cash_fee'])/100; //单位转成元
						$addmoney = $pay_money;
						$endmoney = $addmoney+$row['money'];
					}
					//
					$db->query("UPDATE ".__TBL_PAY__." SET money=".$pay_money.",trade_no='$pay_trade_no',pay_time='".$ADDTIME."',flag=1 WHERE id=".$pay_id);
					$db->query("UPDATE ".__TBL_USER__." SET money=$endmoney WHERE id=".$uid);
					//写清单
					$db->AddHistoryList($uid,$pay_title,$addmoney,1);
					//消息通知
					$tip_title   = '人民币充值成功';
					$tip_content = '成功充值人民币'.$addmoney.'元'.'<br><a href="/my/money.php" class="blue FR">查看详情</a>';
					$db->SendTip($uid,$tip_title,$tip_content);
					//微信通知
					//weixin_mb
					if (!empty($openid)){
						$first  = $nickname."您好，您的".$_ZEAI['LoveB']."账户有变动：";
						$remark = "成功充值人民币，查看详情";
						$url    = $_ZEAI['m_2domain']."/my/money.php";
						wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$openid.'&money='.$addmoney.'&money_total='.$endmoney.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					//写日志
					gyl_debug('　会员ID号：'.$uid.'，'.$pay_title.'支付了 ¥'.$pay_money.' 元(红包充值'.$addloveb.'个)');
					//
					exit;
				}
			}*/
		//在此得上面开始处理订单支付完成后的信息+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++			
	}		
}
?>
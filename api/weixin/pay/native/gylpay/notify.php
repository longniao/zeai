<?php
require_once '../../../sub/init.php';
require_once ZEAI.'sub/conn.php';
//require_once ZEAI.'my/chkuser.php';
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
//require_once 'log.php';
//初始化日志
//$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
//$log = Log::Init($logHandler, 15);
class PayNotifyCallBack extends WxPayNotify {
	//查询订单
	public function Queryorder($transaction_id)	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		//Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS"){
			return true;
		}
		return false;
	}
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)	{
		global $db,$ADDTIME,$_ZEAI;
		//Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		//
				$pay_trade_no = $data['transaction_id'];
				$orderid      = $data['out_trade_no'];
				$pay_money    = $data['total_fee']/100;
				$pay_money    = sprintf("%.2f",$pay_money);
				$Oarr     = explode('__',$orderid);
				$paykind  = $Oarr[1];
				$kind     = substr($paykind,0,2);
				//会员充值
				if ($kind == 'cz'){
					$rt_dd = $db->query("SELECT id,uid,title,openid,money FROM ".__TBL_PAY__." WHERE orderid='$orderid' AND flag=0 LIMIT 1");
					$total_dd = $db->num_rows($rt_dd);
					if($total_dd==1){
						$row_dd     = $db->fetch_array($rt_dd);
						$pay_id     = $row_dd[0];
						$uid        = intval($row_dd[1]);
						$pay_title  = $row_dd[2];
						$openid     = $row_dd[3];
						$pay_money_old = $row_dd[4];
						//
						$row = $db->ROW($uid,'nickname,loveb,grade');
						if(!$row){
							exit;
						}else{
							$nickname   = dataIO($row['nickname'],'out');
							$data_grade = $row['grade'];
							$addloveb = $pay_money_old*100;
							$endloveb = $addloveb+$row['loveb'];
						}
						//
						$db->query("UPDATE ".__TBL_PAY__." SET money=".$pay_money.",trade_no='$pay_trade_no',pay_time='".$ADDTIME."',flag=1 WHERE id=".$pay_id);
						$db->query("UPDATE ".__TBL_USER__." SET loveb=$endloveb WHERE id=".$uid);
						//写清单
						$db->AddHistoryList($uid,$pay_title,$addloveb);
						//消息通知
						$tip_title   = $_ZEAI['LoveB'].'充值成功';
						$tip_content = '成功充值'.$_ZEAI['LoveB'].$addloveb.'个'.'<br><a href="/my/account.php" class="blue FR">查看详情</a>';
						$db->SendTip($uid,$tip_title,$tip_content);
						//微信通知
						//weixin_mb
						if (!empty($openid)){
							$first  = $nickname."您好，您的".$_ZEAI['LoveB']."账户有变动：";
							$remark = "在线充值".$_ZEAI['LoveB']."，查看详情";
							$url    = $_ZEAI['m_2domain']."/my/account.php";
							@wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$openid.'&money='.$addloveb.'&money_total='.$endloveb.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
						}
						//写日志
						gyl_loger('　会员ID号：'.$uid.'，'.$pay_title.'支付了 ¥'.$pay_money.' 元(充值'.$addloveb.'个)');
						//
						exit;
					}
				//会员升级
				}elseif($kind == 'sj'){
					$arr   = substr($paykind,2);$arr   = explode('_',$arr);
					$grade = intval($arr[0]);$if2 = intval($arr[1]);
					$rt_dd = $db->query("SELECT id,uid,title,openid FROM ".__TBL_PAY__." WHERE orderid='$orderid' AND flag=0 LIMIT 1");
					$total_dd = $db->num_rows($rt_dd);
					if($total_dd==1){
						$row_dd     = $db->fetch_array($rt_dd);
						$pay_id     = $row_dd[0];
						$uid        = intval($row_dd[1]);
						$pay_title  = $row_dd[2];
						$openid     = $row_dd[3];
						//
						$row = $db->ROWNAME($uid,'nickname');
						if(!$row){exit;}else{$nickname = strip_tags($row['nickname']);}
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
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
						//写日志
						gyl_loger('　会员ID号：'.$uid.'，'.$pay_title.'支付了 ¥'.$pay_money.' 元(升级)');exit;
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
						//
						$db->query("UPDATE ".__TBL_PAY__." SET money=".$pay_money.",trade_no='$pay_trade_no',pay_time='".$ADDTIME."',flag=1 WHERE id=".$pay_id);
						$db->query("UPDATE ".__TBL_HN__." SET if2=".$if2.",sjtime=".$ADDTIME.",grade=".$grade." WHERE id=".$hid);
						//消息通知
						$tip_title   = $pay_title.'，升级成功！';
						$tip_content = $tip_title.'<br><a href='.$_ZEAI['hn_2domain'].'"/my_vip.php" class="blue FR">进入查看</a>';
						$db->SendTip($uid,$tip_title,$tip_content,4); 
						//写日志
						gyl_loger('　红娘ID号：'.$hid.'，'.$pay_title.'支付了 ¥'.$pay_money.' 元(红娘升级)');exit;
					}
				//红包
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
							@wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$openid.'&money='.$addmoney.'&money_total='.$endmoney.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
						}
						//写日志
						gyl_loger('　会员ID号：'.$uid.'，'.$pay_title.'支付了 ¥'.$pay_money.' 元(红包充值)');
						//
						exit;
					}
					
				}
		//
		return true;
	}
}
//Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
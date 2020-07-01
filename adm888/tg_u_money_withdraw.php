<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_pay.php';
if(!in_array('u_tg',$QXARR))exit(noauth());

define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
$CERTPATH = ZEAI2.'cert'.DIRECTORY_SEPARATOR;
define('APPID_', $_ZEAI['wx_gzh_appid']);
define('SITENAME_',$_ZEAI['siteName'].'-会员提现');
define('SITEIP_',SiteIP());
define('MCHID_',$_PAY['wxpay_mchid']);
define('KEY_', $_PAY['wxpay_key']);
define('CERTPATH_',$CERTPATH);



class WxPay_class{
	public $mch_appid = APPID_;
	public $mchid = MCHID_;
	public $check_name = "NO_CHECK"; //FORCE_CHECK针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
	public $desc = SITENAME_;
	public $spbill_create_ip = SITEIP_;
	public $keys= KEY_;
	//输出xml字符
	public function ToXml($arr){
		if(!is_array($arr) || count($arr) <= 0)exit("数组数据异常！");    	
    	$xml = "<xml>";
    	foreach ($arr as $key=>$val){$xml.="<".$key.">".$val."</".$key.">";}
        $xml.="</xml>";
        return $xml; 
	}
    //将xml转为array
	public function FromXml($xml){
		if(!$xml){exit("xml数据异常！");}        
        //libxml_disable_entity_loader(true);//禁止引用外部xml实体
        $arr_val = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $arr_val;
	}		
	//生成签名
	public function MakeSign($data){		
		ksort($data);//排序参数
		$buff = "";
		foreach ($data as $k => $v){if($k != "sign" && $v != "" && !is_array($v)){$buff .= $k . "=" . $v . "&";}}		
		$string = trim($buff, "&");	
		$string = $string . "&key=".$this->keys;//加入KEY
		$string = md5($string);  //MD5加密		
		$result = strtoupper($string);//所有字符转为大写
		return $result;
	}
	public function get_rand_str($length){
		$possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";$str = "";
		while(strlen($str) < $length) $str .= substr($possible, (rand() % strlen($possible)), 1);
		return($str);	
	}
	public function curl_post_ssl($vars,$second=30){
		//echo getcwd().'/cert/apiclient_cert.pem';exit;
		$url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);	
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		//curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/cert/apiclient_cert.pem');
		curl_setopt($ch,CURLOPT_SSLCERT,CERTPATH_.'apiclient_cert.pem');
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY,CERTPATH_.'apiclient_key.pem');	
		//curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/cert/apiclient_key.pem');	
		//curl_setopt($ch,CURLOPT_CAINFO,getcwd().'/cert/rootca.pem'); 
		curl_setopt($ch,CURLOPT_CAINFO,CERTPATH_.'rootca.pem'); 
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		//echo curl_errno($ch);
		curl_close($ch);
		return $data;
	}
	public function reback_err($errid,$err_money=100){
		switch($errid){
		   case "NOAUTH": return "对不起，您的API权限不足。"; break;
		   case "NO_AUTH": return "产品权限验证失败,检查微信支付平台公众号向粉丝打款功能模块是否开启（登录微信支付商户平台，点击顶部【产品中心】 -> 企业付款到零钱）"; break;
		   case "AMOUNT_LIMIT": return "付款金额不能小于最低限额,每次付款金额必须不小于1元"; break;
		   case "PARAM_ERROR": return "对不起，请求参数出错。参数缺失，或参数格式出错，参数不合法等。"; break;
		   case "OPENID_ERROR": return "对不起，收款账户信息（OPENID）验证失败。"; break;
		   case "NOTENOUGH": return "对不起，公司账户余额不足¥".$err_money."元，无法完成本次支付请求。请登录微信支付商户平台，点击项部【交易中心】 -> 再点击左侧菜单【充值】"; break;
		   case "SYSTEMERROR": return "系统繁忙，请稍后再试。<br>可能原因：网络卡顿延时，或真实姓名校验出错！"; break;
		   case "NAME_MISMATCH": return "对不起，姓名校验出错，填写正确的用户真实姓名。"; break;
		   case "SIGN_ERROR": return "对不起，签名错误。"; break;
		   case "XML_ERROR": return "对不起，提交数据不合法。"; break;
		   case "FATAL_ERROR": return "对不起，证书出错。"; break;
		   case "CA_ERROR": return "证书出错，请登录微信支付商户平台下载证书传到后台cert目录下"; break;
		}	
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
</head>
<style>
td, th {
	font-size: 16px;
}
.table {
	width: 90%;
	min-width: 400px;
}
.tdL {
	width: 120px;
	height: 20px;
	background: #f9f9f9;
	text-align: right;
	color: #666;
	line-height: 20px;
}
.tdR {
	text-align: left;
	line-height: 20px;
}
.tdL:hover, .tdR:hover {
	background: #f9f9f9
}
.tdR:hover .input, .tdR:hover textarea, .tdR:hover select {
	background: #fff
}
#zezhao_div {
	width: 100%;
	height: 100%;
	background: #000;
	opacity: 0.8;
	filter: alpha(opacity=80);
	-moz-opacity: 0.8;
	position: fixed;
	z-index: 99999;
	display: none;
}
#zezhao_txt {
	width: 100%;
	height: 100%;
	position: fixed;
	z-index: 100001;
	text-align: center;
	color: #FFF;
	display: none;
}
</style>
<script>
function jsshow_loading(){	
	document.getElementById("zezhao_div").style.display="block";
	document.getElementById("zezhao_txt").style.display="block";
}
function jsclose_loading(){
    document.getElementById("zezhao_div").style.display="none";
	document.getElementById("zezhao_txt").style.display="none";
}
function close_reload(){
	jsclose_loading();
	parent.location.reload(true);
	//parent.document.getElementById("rightMain").src=parent.document.getElementById("rightMain").src;
	//parent.ZEAI_winclose();	
}
</script>
<body>
<div id="zezhao_div"></div>
<div id="zezhao_txt"><img src="images/load2.gif" width="32" height="32" style="margin-top:100px;"><br><br>支付处理中，请不要刷或关闭本窗口……</div>
<?php 
if($submitok=="gopayfor"){
	if ( !ifint($id) || !ifint($tg_uid))alert("forbidden","-1");
	$rt = $db->query("SELECT tg_uid,flag,orderid,money_list_id,paymoney FROM ".__TBL_PAY__." WHERE kind=-1 AND id='$id'");
	if ($db->num_rows($rt)) {
		$rows   = $db->fetch_array($rt,"name");
		$tg_uid = $rows['tg_uid'];
		$row = $db->ROW(__TBL_TG_USER__,"uname,mob,openid,photo_s,money","id=".$tg_uid,"name");
		if ($row){
			$data_uname    = dataIO($row['uname'],'out');
			$data_mob      = dataIO($row['mob'],'out');
			$data_openid   = $row['openid'];
			$data_photo_s  = $row['photo_s'];
			$data_money    = $row['money'];
		}
		
		$orderid    = $rows['orderid'];
		$orderid    = str_replace("_","",$orderid);
		$money_list_id    = $rows['money_list_id'];
		$flag       = $rows['flag'];
		$pay_money  = $rows['paymoney'];
		$out_money  = $rows['paymoney'];
		$money      = $pay_money*100;
		
		$truename   = $data_truename;
		$data_money = $data_money;
		//$pay_money  = 0.01;
		if ($flag == 1){
		?>
			<script>jsclose_loading();</script>
            <table  width="500" border="0" cellspacing="10" cellpadding="0" align="center" style="margin-top:50px;">
            <tr>
            <td class="Cf00" align="center"><img src="images/sorry.png"><br>
            <br>
            ☆★ 亲，该订单已处理过了，请不要重复处理哦 ★☆</td>
            </tr>
            </table>
		<?php
		}elseif($flag == 0){
			$wxpay = new WxPay_class();
			$pay_data=array(
				'mch_appid'=>$wxpay->mch_appid,
				'mchid'=>$wxpay->mchid,
				'nonce_str'=>$wxpay->get_rand_str(32),
				'partner_trade_no'=>$orderid,
				'openid'=>$data_openid,
				'check_name'=>"NO_CHECK",
				're_user_name'=>$truename,
				'amount'=>$money,
				'desc'=>$wxpay->desc,
				'spbill_create_ip'=>$wxpay->spbill_create_ip
			);
			$pay_data['sign'] = $wxpay->MakeSign($pay_data);
			$pay_vars=$wxpay->ToXml($pay_data);
			echo '<br><br>';
			$re_data = $wxpay->curl_post_ssl($pay_vars);
			
			
			
			$wxpay_arr = $wxpay->FromXml($re_data);
			$payment_time = strtotime($wxpay_arr['payment_time']);

			if($wxpay_arr['return_code']=="SUCCESS" && $wxpay_arr['result_code']=="SUCCESS"){
				$db->query("UPDATE ".__TBL_PAY__." SET trade_no='".$wxpay_arr['payment_no']."',paytime='$payment_time',flag=1 WHERE id=".$id);
				//==================提现成功后的代码操作
				//修改清单
				$content = "申请提现<span style=\"color:#090;font-size:12px\">（打款成功）</span>";
				$db->query("UPDATE ".__TBL_MONEY_LIST__." SET content='$content' WHERE id=".$money_list_id);
				//资金变动后微信通知
				/*
				$first = urlencode("您的账户资金有变动");
				$remark = "提现成功，查看详情";
				$url = $_ZEAI['m_2domain']."/my/money.php";
				wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$openid.'&money=-¥'.$money.'元&money_total=¥'.$data_money.'元&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
				*/
				AddLog('【提现打款】推广员/商家【'.$data_uname.'（ID:'.$tg_uid.'）】->提现打款成功，金额：￥'.$pay_money);
				?>
				<table width="500" border="0" cellspacing="10" cellpadding="0" style="margin-top:50px;" align="center">
				  <tr>
					<td rowspan="2" align="right" valign="top" nowrap="nowrap"><img src="images/sussess.png"></td>
					<td align="left" valign="middle" nowrap="nowrap" style="color:#090; font-family:'微软雅黑'; font-size:24px;">恭喜您，付款成功！</td>
				  </tr>
				  <tr>
					<td align="left" valign="middle" nowrap="nowrap">成功支付：<span class="Cf00">¥<?php echo $pay_money;?></span> 元</td>
				  </tr>
				</table>
				<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:50px;">
				  <tr>
					<td align="center" valign="middle" nowrap="nowrap"><input name="" type="button" value="确定返回" class="btn size3 HONG2 Mtop20"  onClick="close_reload()"></td>
				  </tr>
				</table>
				<?php
			}else{
				?>
				<table width="500" border="0" cellspacing="10" cellpadding="0" style="margin-top:50px;" align="center">
				<tr>
				<td rowspan="2" align="right" valign="top" nowrap="nowrap"><img src="images/close.png"></td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#F30; font-family:'微软雅黑'; font-size:24px;">付款失败！信息如下：</td>
				</tr>
				<tr>
				<td align="left" valign="middle"><?php echo $wxpay->reback_err($wxpay_arr['err_code'],$pay_money);?></td>
				</tr>
				</table>
				<?php
			}
		}
	}



}else{
	if ( !ifint($id))callmsg("forbidden","-1");
	$rt = $db->query("SELECT id,tg_uid,paymoney,flag,orderid,addtime FROM ".__TBL_PAY__." WHERE id='$id'","name");
	if ($db->num_rows($rt)){
		$rows = $db->fetch_array($rt,'name');
		$tg_uid = $rows['tg_uid'];
		$row = $db->ROW(__TBL_TG_USER__,"title,uname,mob,openid,photo_s,money","id=".$tg_uid,"name");
		if ($row){
			$data_uname    = dataIO($row['uname'],'out');
			$data_mob      = dataIO($row['mob'],'out');
			$data_openid   = $row['openid'];
			$data_photo_s  = $row['photo_s'];
			$data_money    = $row['money'];
			
			$title    = dataIO($row['title'],'out');
			$title    = (!empty($title))?$title:$data_uname;
			$nickname = $title.'　ID：'.$tg_uid;

		}
		$pay_money  = $rows['paymoney'];
		$addtime    = $rows['addtime'];
		$mob        = $data_mob;
		$photo_s    = $data_photo_s;
		$path_s_url = $_ZEAI['up2'].'/'.$photo_s;
		
		
		if($rows['flag'] == 1){
	?>
            <table width="500" border="0" cellspacing="10" cellpadding="0" style="margin-top:50px;" align="center">
            <tr>
            <td rowspan="2" align="right" valign="top" nowrap="nowrap"><img src="images/close.png"></td>
            <td align="left" valign="middle" nowrap="nowrap" style="color:#F30; font-family:'微软雅黑'; font-size:24px;">亲，付款失败了！信息如下：</td>
            </tr>
            <tr>
            <td align="left" valign="middle">订单已处理或不存，请确认再处理！</td>
            </tr>
            </table>
   <?php }elseif($rows['flag'] == 0){?>
            <table width="500"  border="0" cellspacing="10" cellpadding="0" align="center">
            <tr>
            <td align="center" style="color:#00c250">☆★ 请对此用户进行自动打款（从微信商户平台扣款）★☆</td>
            </tr>
            </table>
            <table class="table" style="margin-top:10px;">
            <tr>
            <td class="tdL">照片</td>
            <td class="tdR"><?php if (!empty($photo_s)) {?>
            <a href="javascript:;" class="yespic"><img src="<?php echo $path_s_url; ?>"></a>
            <?php } else {?>
            <a href="javascript:;" class="nopic">无图</a>
            <?php }?></td>
            </tr>
<!--            <tr>
            <td class="tdL">会员姓名</td>
            <td class="tdR"><?php echo $truename;?></td>
            </tr> -->
            <tr>
            <td class="tdL">用户帐号</td>
            <td class="tdR"><?php echo $nickname;?></td>
            </tr>
            <tr>
            <td class="tdL">手机号码</td>
            <td class="tdR"><?php if (!empty($mob)){?><i class="ico S18" style="color:#4FA7FF">&#xe627;</i><?php echo $mob; }?></td>
            </tr>
            <tr>
            <td class="tdL">微信openid</td>
            <td class="tdR"><?php echo empty($data_openid)?'<font class="C00f">openid为空，请改为人工转账【手动打款】</font>':$data_openid;?></td>
            </tr>
            <tr>
            <td class="tdL">打款金额</td>
            <td class="tdR Cf00">¥<?php echo $pay_money; ?></td>
            </tr>
            <tr>
            <td class="tdL">申请日期</td>
            <td class="tdR"><?php echo YmdHis($addtime);?></td>
            </tr>
            </table>
            <table width="100%" border="0" cellspacing="10" cellpadding="0">
            <tr>
            <td class="Cf00" align="center">
            </td>
            </tr>
            </table>
            <form action="<?php echo SELF;?>" method="post" target="_self" onSubmit="jsshow_loading();">
            <input id="orderid" name="orderid" type="hidden" value="<?php echo $orderid;?>">
            <input id="openid" name="openid" type="hidden" value="<?php echo $data_openid;?>">
            <input id="truename" name="truename" type="hidden" value="<?php echo $truename;?>">
            <input id="money" name="money" type="hidden" value="<?php echo $pay_money; ?>">
            <input id="id" name="id" type="hidden" value="<?php echo $id;?>">
            <input id="uid" name="tg_uid" type="hidden" value="<?php echo $tg_uid;?>">
            <input id="submitok" name="submitok" type="hidden" value="gopayfor">
            <br><br><br><br><div class="savebtnbox"><button type="submit" id="submit_add" class="btn size3 LV2" onClick="return confirm('确定打款吗？')"><i class="ico">&#xe6b7;</i> 开始打款</button></div>
            </form><?php
		}
	}
}


function SiteIP() {
	  $my_curl = curl_init ();
	  curl_setopt ( $my_curl, CURLOPT_URL, "ns1.dnspod.net:6666" );
	  curl_setopt ( $my_curl, CURLOPT_RETURNTRANSFER, 1 );
	  $ip = curl_exec ( $my_curl );
	  curl_close ( $my_curl );
	  return $ip;
}
function SiteIP2(){    
	if(isset($_SERVER)){    
		if($_SERVER['SERVER_ADDR']){    
			$server_ip=$_SERVER['SERVER_ADDR'];    
		}else{    
			$server_ip=$_SERVER['LOCAL_ADDR'];    
		}    
	  }else{    
		$server_ip = getenv('SERVER_ADDR');    
	}    
	return $server_ip;    
}
?>
<?php require_once 'bottomadm.php';?>
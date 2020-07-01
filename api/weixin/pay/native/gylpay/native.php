<?php
require_once '../../../../../sub/init.php';
require_once ZEAI.'p1/my_chkuser.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_pay.php';
$orderid = strip_tags($_POST['orderid']);
if(str_len($orderid)<10 || str_len($return_okurl)<10 || str_len($notify_url)<10 || str_len($return_okurl)<10 ){alert("获取支付参数信息失败,请返回","-1");}
$kind    = intval($_POST['kind']);
$paytime = intval($_POST['paytime']);
$money_list_id = intval($_POST['money_list_id']);
$orderid_title = strip_tags($_POST['orderid_title']);
$money    = floatval($_POST['money']);$money=($money>9999)?9999:$money;
$paymoney = floatval($_POST['paymoney']);$paymoney=($paymoney>9999)?9999:$paymoney;
$notify_url   = dataIO($notify_url,'out');
$return_okurl = dataIO($return_okurl,'out');
//
$rt_dd    = $db->query("SELECT flag FROM ".__TBL_PAY__." WHERE orderid='$orderid'");
$total_dd = $db->num_rows($rt_dd);
if($total_dd==1){
	$row_dd = $db->fetch_array($rt_dd,'name');	
	if($row_dd['flag']==1){alert("该订单已支付完成，您无需重复支付哦！","-1");}
}else{
	$db->query("INSERT INTO ".__TBL_PAY__."(orderid,kind,uid,title,money,paymoney,addtime,money_list_id,paytime,bz) VALUES ('$orderid',$kind,$cook_uid,'$orderid_title',$money,$paymoney,".ADDTIME.",$money_list_id,$paytime,'PC微信')");
	$payid = $db->insert_id();
}
//
$total_fee = $paymoney*100;
//ini_set('date.timezone','Asia/Shanghai');
require_once "../lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
//require_once 'log.php';
$notify = new NativePay();
$input = new WxPayUnifiedOrder();
$input->SetBody($orderid_title);
$input->SetAttach($orderid_title);
//$num = WxPayConfig::MCHID.date("YmdHis");
$input->SetOut_trade_no($orderid);
$input->SetTotal_fee($total_fee);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($orderid_title);
$input->SetNotify_url($notify_url);
$input->SetTrade_type("NATIVE");
$input->SetProduct_id($payid);
$result = $notify->GetPayUrl($input);
$url2 = $result["code_url"];
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
<title></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<style>
body{background-color:#fff}
.table0{width:100%;border-collapse:collapse}
.table0 tr:first-child{border-bottom:#F0F0F0 1px solid}
.table0 td{padding:5px}
.table0 #myDiv{line-height:24px;font-size:16px;display:inline}
.table0 #timer{line-height:24px;font-size:16px;color:#f00;display:inline}
.ewmbox{width:210px;border:#dedede 1px solid;clear:both;overflow:auto;padding:10px;margin:20px auto 0 auto;border-radius:6px}
.ewmbox img.ewm{width:210px;height:210px;display:block;margin:0 auto}
.ewm{width:100%}
.ewmtips{width:210px;height:50px;line-height:50px;background-color:#46C01B;margin-top:10px;color:#fff}
</style>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<meta name="generator" content="Zeai.cn V6.0" />
</head>
<body>
<table class="table0 ">
<tr>
<td width="50" height="70" align="left" style="padding:0 0 0 30px"><i class="ico" style="color:#45C01A;font-size:35px;line-height:35px">&#xe6b7;</i></td>
<td align="left" class="S16"><?php echo dataIO($orderid_title,'out');?></td>
<td width="200" align="right" class="S18" style="padding-right:30px">实付金额 <span style="color:#EE5A4E;font-family:Arial">¥</span> <font style="color:#EE5A4E;font-size:24px;font-family:Arial"><?php echo $paymoney; ?></font></td>
</tr>
<tr>
  <td colspan="3" align="center" class="center">
  <div class="ewmbox">
        <img alt="扫码支付" class="ewm" src="qrcode.php?data=<?php echo urlencode($url2);?>" />
        <div class="ewmtips">请用手机微信进行扫码支付</div>
  </div>
  </td>
</tr>
<tr>
  <td height="50" colspan="3" align="center" valign="top" class="center"><div id="myDiv"></div>　<div id="timer">0</div>s</td>
</tr>
</table>
<script>  
var myIntval=setInterval(function(){load()},1000);  
function load(){  
	o("timer").innerHTML=parseInt(o("timer").innerHTML)+1; 
	var xmlhttp;    
	if (window.XMLHttpRequest){    
		// code for IE7+, Firefox, Chrome, Opera, Safari    
		xmlhttp=new XMLHttpRequest();    
	}else{    
		// code for IE6, IE5    
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");    
	}    
	xmlhttp.onreadystatechange=function(){    
		if (xmlhttp.readyState==4 && xmlhttp.status==200){    
			trade_state=xmlhttp.responseText;
			console.log(trade_state);
			if(trade_state=='SUCCESS'){  
				o("myDiv").innerHTML='支付成功！';  
				//alert(transaction_id);  
				clearInterval(myIntval);
				zeai.msg('恭喜你，支付成功！',{time:4});
				setTimeout(function(){parent.zeai.openurl('<?php echo $return_okurl;?>');},4000);
			}else if(trade_state=='REFUND'){  
				o("myDiv").innerHTML='转入退款'; 
				clearInterval(myIntval); 
			}else if(trade_state=='NOTPAY'){  
				o("myDiv").innerHTML='请扫码支付';  
			}else if(trade_state=='CLOSED'){  
				o("myDiv").innerHTML='已关闭';  
				clearInterval(myIntval);
			}else if(trade_state=='REVOKED'){  
				o("myDiv").innerHTML='已撤销';  
				clearInterval(myIntval);
			}else if(trade_state=='USERPAYING'){  
				o("myDiv").innerHTML='用户支付中';  
			}else if(trade_state=='PAYERROR'){  
				o("myDiv").innerHTML='支付失败'; 
				clearInterval(myIntval); 
			} 
		}
	}    
	//orderquery.php 文件返回订单状态，通过订单状态确定支付状态  
	xmlhttp.open("POST","orderquery.php",false);    
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");    
	xmlhttp.send("out_trade_no=<?php echo $orderid;?>");  
}  
</script>
</body>
</html>
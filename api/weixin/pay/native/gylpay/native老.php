<?php
require_once '../../../sub/init.php';
$currfields = "openid,grade";
require_once ZEAI.'my/chkuser.php';
$data_openid = intval($row['openid']);
$data_grade  = $row['grade'];

/*充值Love币折扣*/
$LovebBuy  = $_ZEAI['LovebBuy'.$data_grade];

function show_txt_exit($txt_str,$headmeta){	 echo '<!doctype html><html><head><meta charset="utf-8">'.$headmeta.'</head><body style="text-align:center;padding:10px;line-height:180%;"><br><br><br>'.$txt_str.'</body><html>';}
if ($submitok == 'sj'){
	$gradeif2 = $grade.'_'.$if2;
	if ($if2 < 12){
		$p_title = $if2.'个月';
	}elseif($if2 == 12){
		$p_title = '1年';
	}elseif($if2 == 999){
		$p_title = '永久';
	}else{exit;}
	$orderid  = 'pc__sj'.$gradeif2.'__'.$data_uid.'__'.date("YmdHis");
	$tmpmoney = 'VipRMB'.$gradeif2;
	$money = abs(round($_ZEAI[$tmpmoney],2));
	$tmpgradename  = 'Grade'.$grade.'Name';
	$product_title = $_ZEAI[$tmpgradename].'在线升级('.$p_title.')';
	$kind = 1;
}elseif($submitok == 'loveb'){
	$orderid = 'pc__cz'.$num.'__'.$cook_uid.'__'.date("YmdHis");
	$product_title = '充值'.$_ZEAI['LoveB'];
	$kind  = 2;
	$money = abs(round($num,2));
}elseif($submitok == 'hnsj'){
	$gradeif2 = $grade.'_'.$if2;
	if ($if2 < 12){
		$p_title = $if2.'个月';
	}elseif($if2 == 12){
		$p_title = '1年';
	}elseif($if2 == 999){
		$p_title = '永久';
	}else{exit;}
	$orderid = 'pc__hn'.$gradeif2.'__'.$data_hid.'__'.date("YmdHis");
	$tmpmoney = 'hnVipRMB'.$gradeif2;
	$money = abs(round($_ZEAI[$tmpmoney],2));
	$tmpgradename  = 'hnGrade'.$grade.'Name';
	$product_title = $_ZEAI[$tmpgradename].'在线升级('.$p_title.')';
	$kind = 3;
}elseif($submitok == 'hb'){
	$orderid = 'pc__hb'.$num.'__'.$cook_uid.'__'.date("YmdHis");
	$product_title = '红包余额充值';
	$kind  = 4;
	//$money = abs(round($num,2));
	$money = abs(intval($num));
}else{exit;}
if(str_len($orderid)<10 || str_len($return_okurl)<10 ){show_txt_exit("获取支付参数信息失败！请返回!",$headmeta);exit;}
$rt_dd         = $db->query("SELECT flag FROM ".__TBL_PAY__." WHERE orderid='$orderid'");
$total_dd      = $db->num_rows($rt_dd);
if($total_dd==1){
	$row_dd = $db->fetch_array($rt_dd);	
	if($row_dd['flag']==1){	show_txt_exit('该订单已支付完成，您无需重复支付哦！',$headmeta);exit;}
}else{
	//if ($kind == 3){
	//	$db->query("INSERT INTO ".__TBL_PAY__."  (orderid,kind,uid,title,money,addtime) VALUES ('$orderid',$kind,'$cook_hid','$product_title','$money',$ADDTIME)");
	//}else{
		$db->query("INSERT INTO ".__TBL_PAY__."  (orderid,openid,kind,uid,title,money,addtime) VALUES ('$orderid','$data_openid',$kind,'$cook_uid','$product_title','$money',$ADDTIME)");
		
		if($submitok == 'loveb'){
			$money = $money*$LovebBuy;
		}
		
	//}
	$pay_id = $db->insert_id();	
}
if ($cook_uid == 8)$money = 0.1;
$total_fee = $money*100;


//ini_set('date.timezone','Asia/Shanghai');
require_once "../lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
//require_once 'log.php';
$notify = new NativePay();
$input = new WxPayUnifiedOrder();
$input->SetBody($product_title);
$input->SetAttach($product_title);
//$num = WxPayConfig::MCHID.date("YmdHis");
$input->SetOut_trade_no($orderid);
$input->SetTotal_fee($total_fee);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($product_title);
$input->SetNotify_url($_ZEAI['www_2domain']."/api/wxpay/gylpay/notify.php");
$input->SetTrade_type("NATIVE");
$input->SetProduct_id($pay_id);
$result = $notify->GetPayUrl($input);
$url2 = $result["code_url"];
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
<title><?php echo $_ZEAI['SiteName']; ?> - 会员在线支付</title>
<link href="../../../css/my.css" rel="stylesheet" type="text/css">
<style>
.table0{width:600px;background-color:#fff;border:#ccc 1px solid;box-shadow:3px 3px 15px rgba(0,0,0,0.1);margin:50 auto;border-collapse:collapse}
.table0 tr:first-child{background-color:#f8f8f8;border-bottom:#F0F0F0 1px solid}
.table0 td{padding:10px}
.table0 #myDiv{line-height:24px;font-size:16px;display:inline}
.table0 #timer{line-height:24px;font-size:16px;color:#f00;display:inline}
.ewmbox{width:240px;border:#dedede 1px solid;clear:both;overflow:auto;padding:10px;margin:20px auto 0 auto}
.ewm{width:100%}
.ewmtips{width:240px;height:80px;background-color:#466084;margin-top:10px}
.ewmtips img{margin-top:14px;display:inline-block}
</style>
<script src="/js/www_zeai_cn.js"></script>
</head>
<body>
<?php require_once ZEAI.'my/my_top.php'; ?>
<table class="table0 Mtop50">
<tr>
<td width="230" height="100" align="right"><img src="../../../images/WePayLogo.gif" ></td>
<td align="right" class="S18" style="padding-right:18px">应付金额：<span style="color:#EE5A4E;font-family:Arial">¥</span> <font style="color:#EE5A4E;font-size:30px;font-family:Arial"><?php echo $money; ?></font></td>
</tr>
<tr>
  <td height="300" colspan="2" align="center" class="center">
  <div class="ewmbox">
        <img alt="扫码支付" class="ewm" src="qrcode.php?data=<?php echo urlencode($url2);?>" />
        <div class="ewmtips"><img src="../../../images/WePayTips.gif"></div>
  </div>
  </td>
</tr>
<tr>
  <td height="80" colspan="2" align="center" valign="top" class="center"><div id="myDiv"></div>　<div id="timer">0</div></td>
</tr>
</table>
<script>  
var myIntval=setInterval(function(){load()},1000);  
function load(){  
	document.getElementById("timer").innerHTML=parseInt(document.getElementById("timer").innerHTML)+1; 
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
			if(trade_state=='SUCCESS'){  
				document.getElementById("myDiv").innerHTML='支付成功';  
				//alert(transaction_id);  
				clearInterval(myIntval);  
				setTimeout("location.href='success.php?kind=<?php echo $kind; ?>&money=<?php echo $money; ?>&orderid=<?php echo $orderid;?>&return_okurl=<?php echo $return_okurl; ?>'",1000);  
			}else if(trade_state=='REFUND'){  
				document.getElementById("myDiv").innerHTML='转入退款'; 
				clearInterval(myIntval); 
			}else if(trade_state=='NOTPAY'){  
				document.getElementById("myDiv").innerHTML='请扫码支付';  
			}else if(trade_state=='CLOSED'){  
				document.getElementById("myDiv").innerHTML='已关闭';  
				clearInterval(myIntval);
			}else if(trade_state=='REVOKED'){  
				document.getElementById("myDiv").innerHTML='已撤销';  
				clearInterval(myIntval);
			}else if(trade_state=='USERPAYING'){  
				document.getElementById("myDiv").innerHTML='用户支付中';  
			}else if(trade_state=='PAYERROR'){  
				document.getElementById("myDiv").innerHTML='支付失败'; 
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
<?php require_once ZEAI.'bottom.php';?>
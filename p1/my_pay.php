<?php
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$chk_u_jumpurl=HOST.'/p1/my_money.php';
require_once ZEAI.'sub/conn.php';
if(!iflogin() || !ifint($cook_uid))exit("<html><body><script>window.onload = function (){parent.location.href='".HOST."/p1/login.php';}</script></body></html>");

$currfields = "money,openid,subscribe,grade,if2,sjtime,sex";
require_once 'my_chkuser.php';
$data_money = $row['money'];
$data_grade = $row['grade'];
$data_if2   = $row['if2'];
$data_sex   = $row['sex'];
$data_sjtime= $row['sjtime'];
$data_openid = $row['openid'];
$data_subscribe = $row['subscribe'];
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_pay.php';
$loveb_buy = json_decode($_VIP['loveb_buy'],true);
$loveBzk   = $loveb_buy[$data_grade];

if (ifint($grade) && ifint($if2) && $if2<=999 && $kind==1){
	if ($data_grade>$grade){
		json_exit(array('flag'=>0,'msg'=>'亲，只能升级不能降级哦'));	
	}
	require_once ZEAI.'cache/config_vip.php';
	$sj_rmb1 = json_decode($_VIP['sj_rmb1'],true);
	$sj_rmb2 = json_decode($_VIP['sj_rmb2'],true);
	$money = ($data_sex==2)?$sj_rmb2[$grade.'_'.$if2]:$sj_rmb1[$grade.'_'.$if2];
	//$money  = $sj_rmb[$grade.'_'.$if2];
	$paymoney = $money;
}else{
	$money = abs(round($money,2));
	$grade=0;
	$if2=0;
}


//kind，1:会员升级,2:loveb充值,3:余额充值,3:余额充值，4活动报名费
if($submitok == 'ajax_pay_money_loveb'){
	if ($kind == 1){
		$orderid_title = utitle($grade).'在线升级('.get_if2_title($if2).')';
		$return_url = Href('my');
		$jump_url   = $jumpurl;
	}elseif($kind == 2){
		$paymoney = abs(round($money*$loveBzk,2));
		$orderid_title = utitle($data_grade).$_ZEAI['loveB'].'充值';
		$return_url = Href('loveb');
	}elseif($kind == 3){
		$paymoney = $money;
		$orderid_title = utitle($data_grade).'余额充值';
		$return_url = Href('money');
	}elseif($kind == 4){
		$paymoney = $money;
		$orderid_title = utitle($data_grade).'活动报名费';
		$jump_url   = "detail";
	}
	
	if(!empty($jumpurl)){
		$return_url = urldecode($jumpurl);
	}
	
	if ($paykind=='wxpay' || $paykind=='alipay'){
		$money_list_id = intval($grade);
		$paytime       = intval($if2);
		if($kind == 4){//活动报名费
			$tmparrp=explode('/party_detail.php?fid=',urldecode($return_url));
			$fid=intval($tmparrp[1]);
			if(ifint($fid)){
				$money_list_id=$fid;
			}
		}
		/*====================测试支付会员ID=====================*/
		//if ($cook_uid == 8 ){$paymoney = 0.01;}
	}
	switch ($paykind) {
		case 'rmbpay':
			if ($data_money<$paymoney)json_exit(array('flag'=>0,'msg'=>'余额不足'.$paymoney.'元'));
			if($kind == 1){
				$grade  = intval($grade);$if2 = intval($if2);
				$endnum = $data_money-$paymoney;
				if($data_grade==$grade){
					$if2 = $data_if2+$if2;
					$SQL = ",if2=$if2";
				}else{
					$SQL = ",grade=$grade,if2=$if2,sjtime=".ADDTIME;
				}
				$db->query("UPDATE ".__TBL_USER__." SET money=$endnum".$SQL." WHERE grade<=$grade AND id=".$cook_uid);
				//余额清单入库
				$db->AddLovebRmbList($cook_uid,$orderid_title.'余额支付',-$paymoney,'money',5);	
				//余额站内消息
				$C = $data_nickname.'您好，您的余额账户有变动！　<a href='.Href('money').' class=aQING>查看详情</a>';
				$db->SendTip($cook_uid,$orderid_title.'余额支付',dataIO($C,'in'),'sys');
				//人民币-账户资金变动提醒
				if (!empty($data_openid) && $data_subscribe==1){
					$first  = urlencode($nickname."您好，您的余额账户有变动：");
					$remark = urlencode("会员升级余额支付");
					$endnum = $data_money-$paymoney;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$paymoney.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('money')));
				}
				json_exit(array('flag'=>1,'msg'=>utitle($grade).'升级成功','jump_url'=>$jump_url));
			}elseif($kind == 4){//活动报名费
				$endnum = $data_money-$paymoney;
				$db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$cook_uid);
				//余额清单入库
				$db->AddLovebRmbList($cook_uid,$orderid_title.'余额支付',-$paymoney,'money',6);	
				//余额站内消息
				$C = $data_nickname.'您好，您的余额账户有变动！　<a href='.Href('money').' class=aQING>查看详情</a>';
				$db->SendTip($cook_uid,$orderid_title.'余额支付',dataIO($C,'in'),'sys');
				//
				if(ifint($fid)){
					$db->query("UPDATE ".__TBL_PARTY_USER__." SET ifpay=1 WHERE fid=".$fid." AND uid=".$cook_uid);
				}
				//站内通知
				$C = $data_nickname.'您好，恭喜你'.$orderid_title.'交纳成功!　　<a href='.Href('party',$fid).' class=aQING>查看详情</a>';
				$db->SendTip($cook_uid,'恭喜你，'.$orderid_title.'成功!',dataIO($C,'in'),'sys');
				
				//人民币-账户资金变动提醒
				if (!empty($data_openid) && $data_subscribe==1){
					$first  = urlencode($nickname."您好，您的余额账户有变动：");
					$remark = urlencode("活动报名费余额支付");
					$endnum = $data_money-$paymoney;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$paymoney.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('money')));
				}
				
				
				
				$tmparrp=explode('/party_detail.php?fid=',urldecode($return_url));
				$fid=intval($tmparrp[1]);
				if(ifint($fid)){
					$db->query("UPDATE ".__TBL_PARTY_USER__." SET ifpay=1 WHERE fid=".$fid." AND uid=".$cook_uid);
				}
				$jump_url   = Href('party',$fid);


				json_exit(array('flag'=>1,'msg'=>'交费成功','jump_url'=>$jump_url));
			}else{
				$addloveb = $money*$_ZEAI['loveBrate'];
				$db->query("UPDATE ".__TBL_USER__." SET money=money-$paymoney,loveb=loveb+$addloveb WHERE money>=$paymoney AND id=".$cook_uid);
				//爱豆清单入库
				$db->AddLovebRmbList($cook_uid,'余额兑换爱豆',$addloveb,'loveb',3);		
				//余额清单入库
				$db->AddLovebRmbList($cook_uid,'余额兑换爱豆',-$paymoney,'money',4);		
				//爱豆到账提醒
				if (!empty($data_openid) && $data_subscribe==1){
					$first   = urlencode($cook_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
					$content = urlencode($orderid_title);
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$addloveb.'&first='.$first.'&content='.$content.'&url='.mHref('loveb'));
				}
				//人民币-账户资金变动提醒
				if (!empty($data_openid) && $data_subscribe==1){
					$first  = urlencode($nickname."您好，您的余额账户有资金变动：");
					$remark = urlencode("余额兑换爱豆");
					$endnum = $data_money-$paymoney;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$paymoney.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.mHref('money'));
				}
				//站内消息2
				$C = $data_nickname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！　<a href='.Href('loveb').' class=aQING>查看详情</a>';
				$db->SendTip($cook_uid,'爱豆兑换成功',dataIO($C,'in'),'sys');
				json_exit(array('flag'=>1,'msg'=>'充值成功','jumpurl'=>$return_url));
			}
		break;
		case 'wxpay':
			$return_url = urlencode($return_url);
			$notify_url = urlencode(HOST.'/api/weixin/pay/notify_url.php');
			json_exit(array('flag'=>1,
			'kind'=>$kind,
			'money'=>$money,
			'money_list_id'=>$money_list_id,
			'paytime'=>$paytime,
			'orderid'=>$orderid,
			'paymoney'=>$paymoney,
			'orderid_title'=>$orderid_title,
			'notify_url'=>$notify_url,
			'return_okurl'=>$return_url,
			'return_nourl'=>$return_url
			));
		break;
		case 'alipay':
			$return_url = urlencode($return_url);
			$notify_url = urlencode(HOST.'/api/ali/pay/notify_url.php');
			json_exit(array('flag'=>1,
			'kind'=>$kind,
			'money'=>$money,
			'money_list_id'=>$money_list_id,
			'paytime'=>$paytime,
			'orderid'=>$orderid,
			'paymoney'=>$paymoney,
			'orderid_title'=>$orderid_title,
			'notify_url'=>$notify_url,
			'return_url'=>$return_url
			));
		break;
	} 
	exit;
}
$ifrmb = ($data_money >= $money)?true:false;
$rmbboxClsname = ($data_money >= $money )?' class="on"':' class="off"';
//
$curpage = 'my_pay';
$mini_backT = '';
$mini_title = '请选择支付方式';
$ifalipay    = (empty($_PAY['alipay_appid']))?false:true;
$ifweixinpay = (empty($_PAY['wxpay_mchid']))?false:true;
?>
<!doctype html><html><head><meta charset="utf-8">
<title></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_loveb.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<style>
body{background-color:#fff}
.my_pay{}
.my_pay .table{width:100%;border-collapse:collapse;margin:0 auto;box-sizing:border-box;background-color:#fff;margin:0 0 22px 0}
.my_pay .table td{padding:10px 8px;border-bottom: #eee 1px solid;font-size:16px}
.my_pay .table em{font-size:12px;display:block}
.my_pay .table tr.on td{color:#000}
.my_pay .table tr.on td em{color:#f70;}
.my_pay .table tr.off td{color:#999}
.my_pay .table tr.off td em{color:#999;}
.my_pay .table tr.off td .ico{filter:gray;-webkit-filter:grayscale(1);filter:alpha(opacity=40);-moz-opacity:0.4;opacity:0.4}
.my_pay .checkskin-label i.i3{border-radius:30px}
.my_pay .payinfo{text-align:left;background-color:#fff;padding:20px 0 20px 30px;border-bottom:1px #f0f0f0 solid;line-height:150%;color:#666;font-size:16px}
.my_pay .payinfo h1{font-size:24px;color:#333;font-weight:bold;margin:10px 0}
.my_pay .payinfo span{margin-right:40px}
</style>
</head>
<body>
<div class="my_pay" id="my_pay_submain">
    <div class="payinfo">
        <h1>
			<?php
			if ($kind == 1){
				$orderid_title = utitle($grade).'在线升级('.get_if2_title($if2).')';
				$orderid       = 'PGRADE-'.$cook_uid.'-'.date("YmdHis");
			}elseif($kind == 2){
				$orderid_title = utitle($data_grade).$_ZEAI['loveB'].'充值';
				$orderid       = 'PLOVEB-'.$cook_uid.'-'.date("YmdHis");
			}elseif($kind == 3){
				$orderid_title = utitle($data_grade).'余额充值';
				$orderid       = 'PMONEY-'.$cook_uid.'-'.date("YmdHis");
			}elseif($kind == 4){
				$orderid_title = utitle($data_grade).'活动报名费';
				$orderid       = 'PPARTY-'.$cook_uid.'-'.date("YmdHis");
			}
			echo $orderid_title;?>
        </h1>
        订单金额：<?php echo $money;?>元　　
        <?php if ($kind == 2){?>
        <span class="FR">实付金额：<font class="Cf60"><?php echo $money*$loveBzk;?>元</font></span>
        <?php }?>
    </div>
    <table class="table">
    <?php if ($kind !=3){?>
      <tr id="rmbbox"<?php echo $rmbboxClsname;?>>
        <td width="70" align="right"><i class="ico" style="color:#EE5A4E;font-size:40px">&#xe61a;</i></td>
        <td align="left">余额支付
        	<?php if ($ifrmb){?>
        	<em>可用余额 <?php echo str_replace(".00","",$data_money);?>元</em>
            <?php }else{ ?>
            <em>账户余额不足</em>
            <?php }?>
        </td>
        <td width="70" align="left">
            <input type="checkbox" id="rmbpay" name="rmbpay" class="checkskin payli"<?php echo (!$ifrmb)?' disabled':'';?>>
            <label for="rmbpay" class="checkskin-label"><i class="i3"></i></label>
        </td>
      </tr>
      <?php }?>
      <tr<?php echo (!$ifweixinpay)?' style="display:none;"':'';?>>
        <td width="50" align="right"><i class="ico" style="color:#45C01A;font-size:35px">&#xe6b7;</i></td>
        <td align="left">微信支付<em class="C999">通过微信零钱钱包支付</em></td>
        <td width="45" align="left">
            <input type="checkbox" id="wxpay" name="wxpay" class="checkskin payli" checked>
            <label for="wxpay" class="checkskin-label"><i class="i3"></i></label>
        </td>
      </tr>
      <tr<?php echo (!$ifalipay)?' style="display:none;"':'';?>>
        <td width="50" align="right" style="padding-right:10px"><i class="ico" style="color:#02AAF0;font-size:35px">&#xe655;</i></td>
        <td align="left">支付宝支付<em class="C999">推荐有支付宝账户的用户使用</em></td>
        <td width="45" align="left">
            <input type="checkbox" id="alipay" name="alipay" class="checkskin payli">
            <label for="alipay" class="checkskin-label"><i class="i3"></i></label>
        </td>
      </tr>
    </table>
	<input type="hidden" name="paykind" id="paykind" value="wxpay">
    <button type="button" class="btn size4 LV2 W90_" id="btn">立即支付</button>
</div>
<script>
var kind  = <?php echo $kind;?>;
var money = <?php echo $money;?>;
var data_money = <?php echo $data_money;?>;
var orderid = '<?php echo $orderid;?>';
var jumpurl = '<?php echo urldecode($jumpurl);?>';
<?php if ($kind !=3){?>
//RMB
if (data_money >= money){
	rmbpay.onclick = function(){
		paykind.value = 'rmbpay';
		payListener(this);
	}
}
<?php }?>
var jsonurl={'url':PCHOST+'/my_pay'+zeai.extname,'js':1,'data':{grade:<?php echo $grade;?>,if2:<?php echo $if2;?>,submitok:'ajax_pay_money_loveb','money':money,'paykind':paykind.value,'kind':kind,'orderid':orderid,jumpurl:jumpurl}},WX,ZFB;
//WX
wxpay.onclick = function(){
	paykind.value = 'wxpay';
	payListener(this);
	zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);if (rs.flag==1){WX=rs;}else{zeai.msg(rs.msg);}});
}
if(wxpay.checked==true){
	setTimeout(function(){zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);if (rs.flag==1){WX=rs;}else{zeai.msg(rs.msg);}});},500);
}
function ZeaiV6_pay_init(){}
//ZFB
alipay.onclick = function(){
	paykind.value = 'alipay';
	payListener(this);
	var jsonurl={'url':PCHOST+'/my_pay'+zeai.extname,'js':1,'data':{grade:<?php echo $grade;?>,if2:<?php echo $if2;?>,'submitok':'ajax_pay_money_loveb','money':money,'paykind':paykind.value,'kind':kind,'orderid':orderid,'jumpurl':jumpurl}};
	zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);
		if (rs.flag==1){ZFB=rs;}else{zeai.msg(rs.msg);	}
	});
}
function payListener(curdom){
	var i=0;
	zeai.listEach('.payli',function(obj){
		if (curdom != obj)obj.checked = false;
		if (obj.checked == true)i++;
	});
	if (i>0){
		btn.removeClass('HUI');btn.addClass('LV2');
	}else{
		btn.removeClass('LV2');
		if (!btn.hasClass('HUI'));btn.addClass('HUI');
	}
}
//BTN
btn.onclick = function(){
	var i=0;
	zeai.listEach('.payli',function(obj){if (obj.checked == true){
		i++;
		paykind.value = obj.name;
	}});
	if (i==0 || paykind.value!='rmbpay' && paykind.value!='wxpay' && paykind.value!='alipay'){
		zeai.msg('亲~ 请选择支付方式');
	}else{
		var jsonurl={'url':PCHOST+'/my_pay'+zeai.extname,'js':1,'data':{grade:<?php echo $grade;?>,if2:<?php echo $if2;?>,'submitok':'ajax_pay_money_loveb','money':money,'paykind':paykind.value,'kind':kind,'orderid':orderid,'jumpurl':jumpurl}};
		if (paykind.value=='rmbpay'){
			zeai.msg('正在余额支付..',{time:20});
			zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);	
				if (rs.flag==1){
					zeai.msg(rs.msg,{time:2});
					setTimeout(function(){parent.zeai.openurl(rs.jump_url);},2000);
				}else{
					zeai.msg(rs.msg);	
				}
			});
		}else if(paykind.value=='wxpay'){
			zeai.post('<?php echo HOST;?>/api/weixin/pay/native/gylpay/native'+zeai.extname,WX);	
		}else if(paykind.value=='alipay'){
			zeai.alertplus({title:'请您在新打开的页面完成付款',content:'支付完成前请不要关闭此页面，如果支付失败请重支付',title1:'重新支付',title2:'完成支付',
				fn1:function(){zeai.alertplus(0);parent.supdes.click();},
				fn2:function(){zeai.alertplus(0);parent.supdes.click();}
			});
			zeai.post('<?php echo HOST;?>/api/ali/pay/pagepay/pay'+zeai.extname,ZFB,'_blank');	
		}
	}
}
</script>
</body>
</html>
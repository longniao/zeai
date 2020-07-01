<?php
require_once '../sub/init.php';
if (!ifint($kind,'1-4','1'))exit(JSON_ERROR);
$currfields = "money,openid,grade,if2,sjtime";
require_once ZEAI.'my_chk_u.php';
$data_money = $row['money'];
$data_grade = $row['grade'];
$data_if2   = $row['if2'];
$data_sjtime= $row['sjtime'];
$data_openid = $row['openid'];
if(empty($data_openid) && is_weixin())$data_openid=$cook_openid;
if($submitok=='update_my_str'){
	$sjtime = $data_sjtime;
	$if2    = $data_if2;
	if ($if2 > 0){
		$timestr1 = get_if2_title($if2);
		if (!empty($sjtime)){
			$d1  = ADDTIME;
			$d2  = $sjtime + $if2*30*86400;
			$ddiff = $d2-$d1;
			$tmpday   = intval($ddiff/86400);
			$timestr2 = ',还剩<font class="Cf00">'.$tmpday.'</font>天';
			$timestr2 = ($if2 >= 999)?'':$timestr2;
		}
		$timestr = (!empty($timestr1))?'('.$timestr1.$timestr2.')':'';
	}
	$c=uicon($cook_sex.$data_grade).'<font class="middle">'.utitle($data_grade).$timestr.'</font>';
	//json_exit(array('flag'=>1,'timestr'=>urldecode($c)));
	exit($c);
}


require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_pay.php';
$loveb_buy = json_decode($_VIP['loveb_buy'],true);
$loveBzk   = $loveb_buy[$data_grade];

if (ifint($grade) && ifint($if2) && $if2<=999 && $kind==1){
	if ($data_grade>$grade){
		json_exit(array('flag'=>0,'msg'=>'亲，只能升级不能降级哦'));	
	}//elseif($data_grade==$grade && $data_if2>=$if2){
		//延长服务期限，续费
	//}
	require_once ZEAI.'cache/config_vip.php';
	$sj_rmb1 = json_decode($_VIP['sj_rmb1'],true);
	$sj_rmb2 = json_decode($_VIP['sj_rmb2'],true);
	$money = ($cook_sex==2)?$sj_rmb2[$grade.'_'.$if2]:$sj_rmb1[$grade.'_'.$if2];
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
		$return_url = HOST.'/?z=my';
		$jump_url   = "main";//m1/my_vip.php
	}elseif($kind == 2){
		$paymoney = abs(round($money*$loveBzk,2));
		$orderid_title = utitle($data_grade).$_ZEAI['loveB'].'充值';
		$return_url = HOST.'/?z=my&e=my_loveb';
		$jump_url   = "m1/my_loveb.php?a=ye";
	}elseif($kind == 3){
		$paymoney = $money;
		$orderid_title = utitle($data_grade).'余额充值';
		$return_url = HOST.'/?z=my&e=my_money';
		$jump_url = "m1/my_money.php?a=ye";
	}elseif($kind == 4){
		$paymoney = $money;
		$orderid_title = utitle($data_grade).'活动报名费';
		//$return_url = HOST.'/?z=party&e=detail&a=11';见jumpurl
		$jump_url   = "detail";//m1/party_detail.php
	}
	if(!empty($jumpurl))$return_url = urldecode($jumpurl);
	/*====================测试支付会员ID=====================*/
	//if ($cook_uid == 8){$paymoney = 0.01;$money = 0.01;}

	if(str_len($orderid) <10 )json_exit(array('flag'=>0,'msg'=>'订单号异常~'));
	if ($paykind=='wxpay' || $paykind=='alipay'){
		$rowpay = $db->ROW(__TBL_PAY__,"flag","orderid='$orderid'",'num');
		if ($rowpay){
			if ($rowpay[0] == 1)json_exit(array('flag'=>0,'msg'=>'该订单已支付完成，您无需重复支付了'));	
		}else{
			$money_list_id = intval($grade);
			$paytime       = intval($if2);
			if($kind == 4){//活动报名费
				$tmparrp=explode('fid=',urldecode($return_url));
				$fid=intval($tmparrp[1]);
				if(ifint($fid))$money_list_id=$fid;
			}
			$db->query("INSERT INTO ".__TBL_PAY__."(orderid,kind,uid,title,money,paymoney,addtime,money_list_id,paytime) VALUES ('$orderid',$kind,$cook_uid,'$orderid_title',$money,$paymoney,".ADDTIME.",$money_list_id,$paytime)");
			$payid = $db->insert_id();
		}
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
				setcookie("cook_grade",$grade,time()+720000,"/",$_ZEAI['CookDomain']);
				//余额清单入库
				$db->AddLovebRmbList($cook_uid,$orderid_title.'余额支付',-$paymoney,'money',5);	
				//余额站内消息
				$C = $data_nickname.'您好，您的余额账户有变动！　<a href='.Href('money').' class=aQING>查看详情</a>';
				$db->SendTip($cook_uid,$orderid_title.'余额支付',dataIO($C,'in'),'sys');
				//人民币-账户资金变动提醒
				if (!empty($data_openid)){
					$first  = urlencode($nickname."您好，您的余额账户有变动：");
					$remark = urlencode("会员升级余额支付");
					$endnum = $data_money-$paymoney;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$paymoney.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.urlencode(HOST.'/?z=my&e=my_money'));
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
				$tmparrp=explode('fid=',urldecode($return_url));
				$fid=intval($tmparrp[1]);
				if(ifint($fid))$db->query("UPDATE ".__TBL_PARTY_USER__." SET ifpay=1 WHERE fid=".$fid." AND uid=".$cook_uid);
				//站内通知
				$C = $data_nickname.'您好，恭喜你'.$orderid_title.'交纳成功!　<a href='.Href('party',$fid).' class=aQING>查看详情</a>';
				$db->SendTip($cook_uid,'恭喜你，'.$orderid_title.'成功!',dataIO($C,'in'),'sys');
				
				//人民币-账户资金变动提醒
				if (!empty($data_openid)){
					$first  = urlencode($nickname."您好，您的余额账户有变动：");
					$remark = urlencode("活动报名费余额支付");
					$endnum = $data_money-$paymoney;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$paymoney.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.urlencode(HOST.'/?z=my&e=my_money'));
				}
				json_exit(array('flag'=>1,'msg'=>'交费成功','jump_url'=>$jump_url));
			}else{
				$addloveb = $money*$_ZEAI['loveBrate'];
				$db->query("UPDATE ".__TBL_USER__." SET money=money-$paymoney,loveb=loveb+$addloveb WHERE money>=$paymoney AND id=".$cook_uid);
				//爱豆清单入库
				$db->AddLovebRmbList($cook_uid,'余额兑换'.$_ZEAI['loveB'],$addloveb,'loveb',3);		
				//余额清单入库
				$db->AddLovebRmbList($cook_uid,'余额兑换'.$_ZEAI['loveB'],-$paymoney,'money',4);		
				//爱豆到账提醒
				if (!empty($data_openid)){
					$first   = urlencode($cook_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
					$content = urlencode($orderid_title);
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$addloveb.'&first='.$first.'&content='.$content.'&url='.HOST.'/?z=my&e=my_loveb');
				}
				//人民币-账户资金变动提醒
				if (!empty($data_openid)){
					$first  = urlencode($nickname."您好，您的余额账户有变动：");
					$remark = urlencode("余额兑换".$_ZEAI['loveB']);
					$endnum = $data_money-$paymoney;
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$paymoney.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.HOST.'/?z=my&e=my_money');
				}
				//站内消息2
				$C = $data_nickname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！　<a href='.Href('loveb').' class=aQING>查看详情</a>';
				$db->SendTip($cook_uid,$_ZEAI['loveB'].'兑换成功',dataIO($C,'in'),'sys');
				json_exit(array('flag'=>1,'msg'=>'充值成功','jump_url'=>$jump_url));
			}
		break;
		case 'wxpay':
			$total_fee = $paymoney*100;//分
			include_once(ZEAI."api/weixin/pay/WxPayPubHelper/WxPayPubHelper.php");
			//微信内部
			if (is_weixin()){
				if(str_len($data_openid) < 10)json_exit(array('flag'=>0,'msg'=>'获取OPENID失败，请点击【我的】重新获取'));
				$jsApi = new JsApi_pub();	
				$unifiedOrder = new UnifiedOrder_pub();	
				$unifiedOrder->setParameter("openid",$data_openid);
				$unifiedOrder->setParameter("out_trade_no",$orderid);//商户订单号 
				$unifiedOrder->setParameter("total_fee",$total_fee);//总金额
				$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址
				$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型	
				$unifiedOrder->setParameter("body",$orderid_title);//商品描述
				$unifiedOrder->setParameter("attach",$payid);//附加数据	
				$prepay_id = $unifiedOrder->getPrepayId();
				$jsApi->setPrepayId($prepay_id);
				$jsApiParameters = $jsApi->getParameters();
				$jsApiParameters = json_decode($jsApiParameters,true);
				$db->query("UPDATE ".__TBL_PAY__." SET bz='手机微信支付(内部JSAPI)' WHERE id=".$payid);
				json_exit(array('flag'=>1,'jump_url'=>$jump_url,'trade_type'=>'JSAPI','msg'=>'jsapi调起支付','jsApiParameters'=>$jsApiParameters));
			//H5外部
			}else{
				if(!is_mobile()){exit("请用手机操作");}
				require_once ZEAI.'api/weixin/pay/h5pay_func.php';
				$H5PAY = new www_zeai_cn_h5pay_class();
				$pay_data=array(
					'trade_type'=>"MWEB",
					'appid'=>APPID_,
					'mch_id'=>MCHID_,
					'nonce_str'=>$H5PAY->get_rand_str(32),
					'out_trade_no'=>$orderid,
					'body'=>$orderid_title,
					'total_fee'=>$total_fee,
					'notify_url'=>NOTIFY_URL_,
					'spbill_create_ip'=>$H5PAY->siteip()
				);
				$pay_data['sign'] = $H5PAY->MakeSign($pay_data);//var_dump($pay_data);exit;
				$pay_vars     = $H5PAY->ToXml($pay_data);
				$re_data      = $H5PAY->curl_post_ssl($pay_vars);
				$wxpay_arr    = $H5PAY->FromXml($re_data);
				if($wxpay_arr['return_code']=="SUCCESS" && $wxpay_arr['result_code']=="SUCCESS"){
					$pay_url  = $wxpay_arr['mweb_url'];
					$pay_url .= '&redirect_url='.urlencode($return_url);//成功跳转url
					$db->query("UPDATE ".__TBL_PAY__." SET bz='手机微信支付(外部WAP/H5)' WHERE id=".$payid);
					json_exit(array('flag'=>1,'trade_type'=>'H5','msg'=>'H5调起支付','redirect_url'=>$pay_url));
				}else{
					json_exit(array('flag'=>0,'trade_type'=>'H5','msg'=>'商户平台H5支付没开通或参数不正确','redirect_url'=>$redirect_url));
				}
			}
		break;
		case 'alipay':
			if(!is_mobile()){exit("请用手机操作");}
			$bzstr = (is_weixin())?'(微信内部)':'(WAP/H5)';
			$db->query("UPDATE ".__TBL_PAY__." SET bz='手机支付宝支付".$bzstr."' WHERE id=".$payid);
			$return_url = urlencode($return_url);
			$notify_url = urlencode(HOST.'/api/ali/pay/notify_url.php');
			json_exit(array('flag'=>1,'WIDout_trade_no'=>$orderid,'WIDtotal_amount'=>$paymoney,'notify_url'=>$notify_url,'return_url'=>$return_url,'WIDsubject'=>$orderid_title,'WIDbody'=>$orderid_title));
		break;
	} 
	exit;
}
$ifrmb = ($data_money >= $money)?true:false;
$rmbboxClsname = ($data_money >= $money )?' class="on"':' class="off"';
//
$curpage = 'my_pay';
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$curpage.'">&#xe602;</i>请选择支付方式';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '';
require_once ZEAI.'m1/top_mini.php';
$ifalipay    = (empty($_PAY['alipay_appid']))?false:true;
$ifweixinpay = (empty($_PAY['wxpay_mchid']))?false:true;
?>
<style>
.my_pay{top:44px;background-color:#fff}
/**/
.my_pay .table{width:100%;border-collapse:collapse;margin:0 auto;box-sizing:border-box;background-color:#fff;margin:0 0 30px 0}
.my_pay .table td{padding:20px 8px;border-bottom: #eee 1px solid;font-size:16px}
.my_pay .table em{font-size:12px;display:block}
.my_pay .table tr.on td{color:#000}
.my_pay .table tr.on td em{color:#f70;}
.my_pay .table tr.off td{color:#999}
.my_pay .table tr.off td em{color:#999;}
.my_pay .table tr.off td .ico{filter:gray;-webkit-filter:grayscale(1);filter:alpha(opacity=40);-moz-opacity:0.4;opacity:0.4}
.my_pay .checkskin-label i.i3{border-radius:30px}
.my_pay .payinfo{text-align:left;background-color:#fff;padding:20px 30px;border-bottom:15px #f0f0f0 solid;line-height:200%;color:#666;font-size:16px}
</style>
<!--<i class='ico goback' id='ZEAIGOBACK-<?php echo $curpage;?>'>&#xe602;</i>
--><div class="submain2 <?php echo $curpage;?>" id="<?php echo $curpage;?>_submain">
    <div class="payinfo">
        支付内容：
			<?php
			if ($kind == 1){
				$orderid_title = utitle($grade).'在线升级('.get_if2_title($if2).')';
				$orderid       = 'MGRADE-'.$data_uid.'-'.date("YmdHis");
			}elseif($kind == 2){
				$orderid_title = utitle($data_grade).$_ZEAI['loveB'].'充值';
				$orderid       = 'MLOVEB-'.$data_uid.'-'.date("YmdHis");
			}elseif($kind == 3){
				$orderid_title = utitle($data_grade).'余额充值';
				$orderid       = 'MMONEY-'.$data_uid.'-'.date("YmdHis");
			}elseif($kind == 4){
				$orderid_title = utitle($data_grade).'活动报名费';
				$orderid       = 'PARTY-'.$data_uid.'-'.date("YmdHis");
			}
			echo $orderid_title;?><br>
        订单金额：<?php echo $money;?>元<br>
        <?php if ($kind == 2){?>
        实付金额：<font class="Cf60"><?php echo $money*$loveBzk;?>元</font><br>
        <?php }?>
    </div>
    <table class="table">
    <?php if ($kind !=3){?>
      <tr id="rmbbox"<?php echo $rmbboxClsname;?>>
        <td width="50" align="right"><i class="ico" style="color:#EE5A4E;font-size:40px">&#xe61a;</i></td>
        <td align="left"><b class="S18">余额支付</b>
        	<?php if ($ifrmb){?>
        	<em>可用余额 <?php echo str_replace(".00","",$data_money);?>元</em>
            <?php }else{ ?>
            <em>账户余额不足</em>
            <?php }?>
        </td>
        <td width="45" align="left">
            <input type="checkbox" id="rmbpay" name="rmbpay" class="checkskin payli"<?php echo (!$ifrmb)?' disabled':'';?>>
            <label for="rmbpay" class="checkskin-label"><i class="i3"></i></label>
        </td>
      </tr>
      <?php }?>
      <tr<?php echo (!$ifweixinpay)?' style="display:none;"':'';?>>
        <td width="50" align="right"><i class="ico" style="color:#45C01A;font-size:35px">&#xe6b7;</i></td>
        <td align="left"><b class="S18">微信支付</b><em class="C999">通过微信零钱钱包支付</em></td>
        <td width="45" align="left">
            <input type="checkbox" id="wxpay" name="wxpay" class="checkskin payli" checked>
            <label for="wxpay" class="checkskin-label"><i class="i3"></i></label>
        </td>
      </tr>
	<?php if (!is_weixin()){?>
      <tr<?php echo (!$ifalipay)?' style="display:none;"':'';?>>
        <td width="50" align="right" style="padding-right:10px"><i class="ico" style="color:#02AAF0;font-size:35px">&#xe655;</i></td>
        <td align="left"><b class="S18">支付宝支付</b><em class="C999">推荐有支付宝账户的用户使用</em></td>
        <td width="45" align="left">
            <input type="checkbox" id="alipay" name="alipay" class="checkskin payli">
            <label for="alipay" class="checkskin-label"><i class="i3"></i></label>
        </td>
      </tr>
	<?php }?>
    </table>
	<input type="hidden" name="paykind" id="paykind" value="">
    <button type="button" class="btn size4 LV2 W90_ " id="btn">立即支付</button>
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
//WX
wxpay.onclick = function(){
	paykind.value = 'wxpay';
	payListener(this);
}
<?php if (!is_weixin()){?>
//ZFB
alipay.onclick = function(){
	paykind.value = 'alipay';
	payListener(this);
}
<?php }?>
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
		var jsonurl={'url':HOST+'/m1/my_pay'+zeai.extname,'js':1,'data':{grade:<?php echo $grade;?>,if2:<?php echo $if2;?>,'submitok':'ajax_pay_money_loveb','money':money,'paykind':paykind.value,'kind':kind,'orderid':orderid,'jumpurl':jumpurl}};
		if (paykind.value=='rmbpay'){
			//zeai.msg('正在余额支付..',{time:20});
			zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);	
				if (rs.flag==1){
					zeai.msg(rs.msg);
					setTimeout(function(){payJump(kind,rs.jump_url);},1000);
				}else{
					zeai.msg(rs.msg);	
				}
			});
		}else if(paykind.value=='wxpay'){
			zeai.msg('正在微信支付..');
			zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);
				if (rs.flag==1){
					if (rs.trade_type=='H5'){
						zeai.openurl(rs.redirect_url);
					}else{
						function jsApiCall(){
							WeixinJSBridge.invoke('getBrandWCPayRequest',rs.jsApiParameters,function(res){
								//WeixinJSBridge.log(res.err_msg);
								if(res.err_msg == "get_brand_wcpay_request:ok"){
									zeai.msg("支付成功");
									payJump(kind,rs.jump_url);
								}else{
								   zeai.msg("支付失败,请返回上一步重新支付~~");
								   //alert(JSON.stringify(res));
								}				
							});
						}
						if (typeof WeixinJSBridge == "undefined"){
							if( document.addEventListener ){
								document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
							}else if (document.attachEvent){
								document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
								document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
							}
						}else{
							jsApiCall();
						}
					}
				}else{
					zeai.msg(rs.msg);	
				}
			});
		<?php if (!is_weixin()){?>
		}else if(paykind.value=='alipay'){
			zeai.msg('正在支付宝支付..',{time:20});
			zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);	
				if (rs.flag==1){
					delete rs.flag;zeai.post(HOST+'/api/ali/pay/wappay/pay'+zeai.extname,rs);	
				}else{
					zeai.msg(rs.msg);	
				}
			});
		<?php }?>
		}
	}
}
function payJump(k,jump_url){
	if(!zeai.empty(jumpurl)){
		zeai.openurl(jumpurl);return;
	}
	switch (k) {
		case 1:
			if(jump_url=='main'){//更新my首页图标和等级字符
				zeai.ajax({url:HOST+'/m1/my_pay'+zeai.extname,js:0,data:{kind:1,submitok:'update_my_str'}},function(e){//rs=zeai.jsoneval(e);
					if(rs.flag==1 && !zeai.empty(o('my-vipinfo'))){o('my-vipinfo').html(e);}
					ZeaiM.page.jump('main');
				});
			}
		break;
		case 2:
			o('ZEAIGOBACK-<?php echo $curpage;?>').click();
			o('my_loveb_yebtn').click();
		break;
		case 3:
			ZeaiM.page.jump(jump_url,'my_money');
		break;
		case 4:
			//ZeaiM.page.jump(jump_url,'my_party');//估计要跳转到活动报名页
		break;
	}
}
</script>
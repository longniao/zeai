<?php 
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:7144100,797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/11/11 by supdes
*/
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
if($submitok=='ajax_pay'){
	if(!iflogin() && $iflogin==1)json_exit(array('flag'=>'nologin','msg'=>'请您先登录','jumpurl'=>$jumpurl));
	if (is_weixin() && empty($cook_openid) && $iflogin==1){
		json_exit(array('flag'=>'nologin','msg'=>'请您先登录','jumpurl'=>$jumpurl));
	}
	require_once ZEAI.'cache/config_vip.php';
	require_once ZEAI.'cache/config_pay.php';
	$paymoney = abs(round($money,2));
	$orderid_title = $title;
	$cook_uid=(!ifint($cook_uid))?$cook_tg_uid:$cook_uid;
	$orderid=(!empty($oid) && $oid!='undefined')?$oid:'WX-'.$cook_uid.'-'.date("YmdHis");
	$tmpid=intval($tmpid);$cook_uid=intval($cook_uid);
	$tg_uid=intval($tg_uid);
	$db->query("INSERT INTO ".__TBL_PAY__."(orderid,kind,uid,title,money,paymoney,money_list_id,addtime,tg_uid) VALUES ('$orderid',$kind,$cook_uid,'$orderid_title',$money,$money,$tmpid,".ADDTIME.",$tg_uid)");
	$payid = $db->insert_id();
	$cook_openid=(empty($cook_openid))?$cook_tg_openid:$cook_openid;
	if ($paykind=='wxpay' ){
		$total_fee = $paymoney*100;//分
		include_once(ZEAI."api/weixin/pay/WxPayPubHelper/WxPayPubHelper.php");
		//微信内部
		if (is_weixin()){
			if(str_len($cook_openid) < 10)json_exit(array('flag'=>0,'msg'=>'不好意思，OPENID丢了'));
			$jsApi = new JsApi_pub();	
			$unifiedOrder = new UnifiedOrder_pub();
			$unifiedOrder->setParameter("openid",$cook_openid);
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
			json_exit(array('flag'=>1,'jumpurl'=>$jumpurl,'return_url'=>$return_url,'trade_type'=>'JSAPI','msg'=>'jsapi调起支付','jsApiParameters'=>$jsApiParameters));
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
			$pay_data['sign'] = $H5PAY->MakeSign($pay_data);
			$pay_vars     = $H5PAY->ToXml($pay_data);
			$re_data      = $H5PAY->curl_post_ssl($pay_vars);
			$wxpay_arr    = $H5PAY->FromXml($re_data);
			if($wxpay_arr['return_code']=="SUCCESS" && $wxpay_arr['result_code']=="SUCCESS"){
				$pay_url  = $wxpay_arr['mweb_url'];
				$pay_url .= '&redirect_url='.urlencode($return_url);//成功跳转url
				$db->query("UPDATE ".__TBL_PAY__." SET bz='手机微信支付(外部WAP/H5)' WHERE id=".$payid);
				json_exit(array('flag'=>1,'trade_type'=>'H5','msg'=>'H5调起支付','redirect_url'=>$pay_url));
			}else{
				json_exit(array('flag'=>0,'trade_type'=>'H5','msg'=>'商户平台【H5支付没开通】或参数不正确','return_url'=>$return_url));
			}
		}
	}
	exit;
}
?>
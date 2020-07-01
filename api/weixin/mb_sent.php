<?php 
require_once '../../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_wxgzh.php';
switch($mbbh){
	case "ZEAI_ACCOUNT_CHANGE":
		/*
		
		帐户资金变动提醒OPENTM207679800
		{{first.DATA}}
		变动时间：{{keyword1.DATA}}
		变动金额：{{keyword2.DATA}}
		帐户余额：{{keyword3.DATA}}
		{{remark.DATA}}
		
		②账户资金变动提醒OPENTM415437054
		{{first.DATA}}
		交易类型：{{keyword1.DATA}}
		交易金额：{{keyword2.DATA}}
		交易时间：{{keyword3.DATA}}
		账户余额：{{keyword4.DATA}}
		{{remark.DATA}}		
		*/
		$first  = (!empty($first))?dataIO($first,'out'):'您好，您的帐户有变动：';
		$remark = (!empty($remark))?dataIO($remark,'out'):'';//查看详情
		$keyword1= (empty($time))?YmdHis(ADDTIME):YmdHis($time);
		$keyword2 = urldecode($num);
		$keyword3 = urldecode($endnum);
		if ($keyword2>0){
			$keyword2 = '+'.$keyword2;
			$color = '#ff0000';
		}else{
			$color = '#0000ff';
		}
		if(!empty($_GZH['wx_gzh_mb_loveb'])){
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$_GZH['wx_gzh_mb_loveb'],
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>$keyword1,'color'=>'#173177'),
					'keyword2'=>array('value'=>$keyword2,'color'=>$color),
					'keyword3'=>array('value'=>$keyword3,'color'=>'#173177'),
					'remark'=>array('value'=>$remark,'color'=>'#173177')
				)
			);
		}elseif(!empty($_GZH['wx_gzh_mb_loveb2'])){
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$_GZH['wx_gzh_mb_loveb2'],
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>'账户充值/扣除','color'=>'#173177'),
					'keyword2'=>array('value'=>$keyword2,'color'=>$color),
					'keyword3'=>array('value'=>$keyword1,'color'=>'#173177'),
					'keyword4'=>array('value'=>$keyword3,'color'=>'#173177'),
					'remark'=>array('value'=>$remark,'color'=>'#173177')
				)
			);
		}
	break;
	case "ZEAI_ACCOUNT_IN":
		$first   = (!empty($first))?urldecode($first):'您有一笔资金到账！';
		$remark  = (!empty($remark))?urldecode($remark):'';//查看详情
		$keyword1= urldecode($num);
		if ($keyword1>0){
			$keyword1 = '+'.$keyword1;
			$color = '#ff0000';
		}else{
			$color = '#0000ff';
		}		
		$keyword2= (empty($time))?YmdHis(ADDTIME):YmdHis($time);
		$keyword3= urldecode($content);
		if(!empty($_GZH['wx_gzh_mb_productpay'])){//到账提醒 OPENTM400265867
			/*
			到账金额：{{keyword1.DATA}}
			到账时间：{{keyword2.DATA}}
			到账详情：{{keyword3.DATA}}
			*/
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$_GZH['wx_gzh_mb_productpay'],
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>$keyword1,'color'=>$color),
					'keyword2'=>array('value'=>$keyword2,'color'=>'#173177'),
					'keyword3'=>array('value'=>$keyword3,'color'=>'#173177'),
					'remark'=>array('value'=>$remark,'color'=>'#173177')
				)
			);
		}elseif(!empty($_GZH['wx_gzh_mb_productpay2'])){//收益到账提醒 OPENTM204602735
			/*
			到帐时间：{{keyword1.DATA}}
			到账金额：{{keyword2.DATA}}
			收益产品：{{keyword3.DATA}}
			*/
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$_GZH['wx_gzh_mb_productpay2'],
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>$keyword2,'color'=>'#173177'),
					'keyword2'=>array('value'=>$keyword1,'color'=>$color),
					'keyword3'=>array('value'=>$keyword3,'color'=>'#173177'),
					'remark'=>array('value'=>$remark,'color'=>'#173177')
				)
			);
		}elseif(!empty($_GZH['wx_gzh_mb_productpay3'])){//充值到账提醒 OPENTM417991932
			/*
			充值账户：{{keyword1.DATA}}
			充值金额：{{keyword2.DATA}}
			充值方式：{{keyword3.DATA}}
			充值时间：{{keyword4.DATA}}
			*/
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$_GZH['wx_gzh_mb_productpay3'],
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>'个人帐户','color'=>'#173177'),
					'keyword2'=>array('value'=>$keyword1,'color'=>$color),
					'keyword3'=>array('value'=>$keyword3,'color'=>'#173177'),
					'keyword4'=>array('value'=>$keyword2,'color'=>'#173177'),
					'remark'=>array('value'=>$remark,'color'=>'#173177')
				)
			);
		}
	break;
	case "ZEAI_ADMIN_INFO"://后台操作提醒OPENTM207104826
		/*
		{{first.DATA}}
		执行动作：{{keyword1.DATA}}
		执行时间：{{keyword2.DATA}}
		执行人：{{keyword3.DATA}}
		{{remark.DATA}}
		*/
		$template_id = $_GZH['wx_gzh_mb_adminfo'];
		$first   = (!empty($first))?dataIO($first,'out'):'系统客服消息！';
		$remark  = (!empty($remark))?dataIO($remark,'out'):'请及时查看';
		$keyword1= urldecode($keyword1);
		$keyword2= (empty($time))?YmdHis(ADDTIME):YmdHis($time);
		$keyword3= (empty($keyword3))?$_ZEAI['siteName']:urldecode($keyword3);
		$msg_data=array(
			'touser'=>$openid,
			'template_id'=>$template_id,
			'url'=>urldecode($url),
			'data'=>array(
				'first'=>array('value'=>$first,'color'=>'#173177'),
				'keyword1'=>array('value'=>$keyword1,'color'=>'#173177'),
				'keyword2'=>array('value'=>$keyword2,'color'=>'#173177'),
				'keyword3'=>array('value'=>$keyword3,'color'=>'#173177'),
				'remark'=>array('value'=>$remark,'color'=>'#173177')
			)
		);
	break;
	case "ZEAI_MSG_CHAT":
		$first    = (!empty($first))?urldecode($first):'您有新的消息！';
		$remark   = (!empty($remark))?urldecode($remark):'请注意查收！';
		$nickname = urldecode($nickname); 
		$content  = urldecode($content);
		if(!empty($_GZH['wx_gzh_mb_msgchat'])){//用户咨询提醒OPENTM202119578
			/*
			用户名称：{{keyword1.DATA}}
			咨询内容：{{keyword2.DATA}}
			*/
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$_GZH['wx_gzh_mb_msgchat'],
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>$nickname,'color'=>'#173177'),
					'keyword2'=>array('value'=>$content,'color'=>'#173177'),
					'remark'=>array('value'=>$remark,'color'=>'#ff0000')
				)
			);
		}elseif(!empty($_GZH['wx_gzh_mb_msgchat2'])){//新咨询通知 OPENTM401760085
			/*
			姓名：{{keyword1.DATA}}
			电话：{{keyword2.DATA}}
			编号：{{keyword3.DATA}}			
			*/
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$_GZH['wx_gzh_mb_msgchat2'],
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>$nickname,'color'=>'#173177'),
					'keyword2'=>array('value'=>'点击进入咨询','color'=>'#173177'),
					'keyword3'=>array('value'=>$content,'color'=>'#173177'),
					'remark'=>array('value'=>$remark,'color'=>'#ff0000')
				)
			);
		}
	break;
	
	case "ZEAI_DATA_CHECK"://会员资料审核提醒OPENTM201057607
	/*
	{{first.DATA}}
	审核结果：{{keyword1.DATA}}
	原因：{{keyword2.DATA}}
	{{remark.DATA}}
	*/
	$first    = (!empty($first))?urldecode($first):'您提交的会员资料已完成审核。';
	$remark   = (!empty($remark))?urldecode($remark):'点击进入查看！';
	$template_id = $_GZH['wx_gzh_mb_udata'];
	$keyword1  = urldecode($keyword1); 
	$keyword2  = urldecode($keyword2);
	if ($keyword1 == '已通过'){
		$color = '#009900';
	}else{
		$color = '#0000ff';
	}		
	$msg_data=array(
		'touser'=>$openid,
		'template_id'=>$template_id,
		'url'=>urldecode($url),
		'data'=>array(
			'first'=>array('value'=>$first,'color'=>'#173177'),
			'keyword1'=>array('value'=>$keyword1,'color'=>$color),
			'keyword2'=>array('value'=>$keyword2,'color'=>'#173177'),
			'remark'=>array('value'=>$remark,'color'=>'#173177')
		)
	);
	break;
	case "ZEAI_HONOR_CHECK":
		/*
		{{first.DATA}}
		认证详情：{{keyword1.DATA}}
		认证结果：{{keyword2.DATA}}
		{{remark.DATA}}
		*/
		$template_id = $_GZH['wx_gzh_mb_honor'];
		$first   = (!empty($first))?urldecode($first):'认证审核完成';
		$remark  = (!empty($remark))?urldecode($remark):'';
		$key1    = urldecode($keyword1);
		$key2    = urldecode($keyword2);
		$color = ($flag == 1)?'#009900':'#0000ff';
		if(!empty($_GZH['wx_gzh_mb_honor'])){//认证通知OPENTM204559869
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$template_id,
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>$key1,'color'=>'#173177'),
					'keyword2'=>array('value'=>$key2,'color'=>$color),
					'remark'=>array('value'=>$remark,'color'=>'#173177')
				)
			);
		}elseif(!empty($_GZH['wx_gzh_mb_honor2'])){//身份认证结果通知 OPENTM415975057
			/*	
			认证状态：{{keyword1.DATA}}
			认证类型：{{keyword2.DATA}}
			认证信息：{{keyword3.DATA}}
			处理时间：{{keyword4.DATA}}	
			*/		
			$msg_data=array(
				'touser'=>$openid,
				'template_id'=>$_GZH['wx_gzh_mb_honor2'],
				'url'=>urldecode($url),
				'data'=>array(
					'first'=>array('value'=>$first,'color'=>'#173177'),
					'keyword1'=>array('value'=>$key2,'color'=>'#173177'),
					'keyword2'=>array('value'=>'诚信认证','color'=>$color),
					'keyword3'=>array('value'=>$key1,'color'=>$color),
					'keyword4'=>array('value'=>YmdHis(ADDTIME),'color'=>$color),
					'remark'=>array('value'=>$remark,'color'=>'#173177')
				)
			);
		
		}
	break;
}
function sent_msg($template_data,$ac_token){
	$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$ac_token;
	$data=$template_data;
	$jsdata=json_encode($data);
	$ch = curl_init();
	$headers = array('Accept-Charset: utf-8');
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)');
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsdata);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$d = curl_exec($ch);
	return $d;
}
echo sent_msg($msg_data,wx_get_access_token());
?>
<?php
if (ini_get('session.auto_start') == 0)session_start();
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
//if (ifint($cook_tg_uid)){header("Location: tg_my.php");exit;}
if (!is_mobile())exit('请用手机打开');
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_shop.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
$TG_set = json_decode($_REG['TG_set'],true);
require_once ZEAI.'sub/TGfun.php';

//微信自动进入
if(is_weixin() && empty($submitok)){
	if(isset($cook_tg_openid) && !empty($cook_tg_openid) ){
		$server_tg_openid = $cook_tg_openid;
	}else{
		$server_tg_openid = wx_get_openid(0);
		setcookie("cook_tg_openid",$server_tg_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
	}
	$row = $db->ROW(__TBL_TG_USER__,"id,uname,flag,mob,kind,pwd,subscribe","openid<>'' AND openid='".$server_tg_openid."'","num");
	if ($row){
		$tg_uid = $row[0];$tg_uname = $row[1];$flag = $row[2];$tg_mob = $row[3];$tg_kind = $row[4];$tg_pwd = $row[5];$tg_subscribe = $row[6];
		if ($flag==-1)alert('您的帐号已被锁定','back');
		setcookie("cook_tg_uid",$tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_mob",$tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_uname",$tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_pwd",$tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_kind",$tg_kind,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_subscribe",$tg_subscribe,time()+720000,"/",$_ZEAI['CookDomain']);
		if($loginkind=='shop'){
			header("Location: ".HOST."/m4/shop_my.php");
		}else{
			switch ($k) {
				case 2:header("Location: ".HOST."/m4/shop_my.php");break;
				case 3:header("Location: ".HOST."/m4/shop_my.php");break;
				default:header("Location: tg_my.php");break;
			}	
		}
	}
}
//微信自动进入END


if(@!in_array('tg',$navarr))exit("<div style='font-size:30px;text-align:center;margin:50px'>推广功能暂未开启</div>");
/*if (empty($cook_tg_openid) && empty($kind) && is_weixin()){
	$cook_tg_openid=wx_get_openid(0);
	setcookie('cook_tg_openid',$cook_tg_openid,time()+720000,'/',$_ZEAI['CookDomain']);
}
*/
/******************************************************/
//kind，1:会员升级,2:loveb充值,3:余额充值,3:余额充值，4活动报名费，5推广升级，6推广激活，7认证
if($submitok == 'ajax_pay_jh'){
	if($TG_set['active_price']==0 && ifint($cook_tg_uid)){
		$flag = ($TG_set['regflag'] == 1)?0:1;
		$db->query("UPDATE ".__TBL_TG_USER__." SET flag=$flag WHERE id=".$cook_tg_uid);
		json_exit(array('flag'=>'success','msg'=>'激活成功'));
	}
	if ($kind != 6)json_exit(array('flag'=>0,'msg'=>'kind!=6，请联系管理员'));//kind=6推广激活
	require_once ZEAI.'cache/config_vip.php';
	require_once ZEAI.'cache/config_pay.php';
	$paymoney = abs(round($money,2));

	$orderid_title = '推广激活';
	$return_url = HOST.'/m1/tg_my.php';
	$jump_url   = HOST.'/m1/tg_my.php';
	
	if(str_len($orderid) <10 )json_exit(array('flag'=>0,'msg'=>'订单号异常~'));
	if ($paykind=='wxpay' || $paykind=='alipay'){
		$rowpay = $db->ROW(__TBL_PAY__,"flag","orderid='$orderid'",'num');
		if ($rowpay){
			if ($rowpay[0] == 1)json_exit(array('flag'=>0,'msg'=>'该订单已支付完成，您无需重复支付了'));	
		}else{
			$money_list_id = intval($grade);
			$paytime       = 0;
			$db->query("INSERT INTO ".__TBL_PAY__."(orderid,kind,tg_uid,title,money,paymoney,addtime,money_list_id,paytime) VALUES ('$orderid',$kind,$cook_tg_uid,'$orderid_title',$money,$paymoney,".ADDTIME.",$money_list_id,$paytime)");
			$payid = $db->insert_id();
		}
		/*====================测试支付会员ID=====================*/
		//if ($cook_uid == 8){$paymoney = 0.01;}
	}
	if ($paykind=='wxpay' ){
		$total_fee = $paymoney*100;//分
		include_once(ZEAI."api/weixin/pay/WxPayPubHelper/WxPayPubHelper.php");
		//微信内部
		if (is_weixin()){
			if(str_len($cook_tg_openid) < 10)json_exit(array('flag'=>0,'msg'=>'获取OPENID异常'));
			$jsApi = new JsApi_pub();	
			$unifiedOrder = new UnifiedOrder_pub();	
			$unifiedOrder->setParameter("openid",$cook_tg_openid);
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
			json_exit(array('flag'=>1,'jump_url'=>$jump_url,'redirect_url'=>$return_url,'trade_type'=>'JSAPI','msg'=>'jsapi调起支付','jsApiParameters'=>$jsApiParameters));
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
				json_exit(array('flag'=>0,'trade_type'=>'H5','msg'=>'商户平台H5支付没开通或参数不正确','redirect_url'=>$redirect_url));
			}
		}
	}
	exit;
}
/******************************************************/

/*************AJAX页面开始*************/
switch ($submitok) {
	case 'ajax_reg_chk':
		//手机
		if ($TG_set['regkind'] == 1){
			chk_uname($mob,$pwd);
			//验证码处理
			$verify = intval($verify);
			if (empty($_SESSION['Zeai_cn__verify'])){
				json_exit(array('flag'=>0,'msg'=>'短信验证码错误，请重新获取'));
			}else{
				if ($_SESSION['Zeai_cn__verify'] != $verify){
					json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
				}
				if ($_SESSION['Zeai_cn__mob'] != $mob && ifmob($mob)){
					unset($_SESSION["Zeai_cn__verify"]);
					unset($_SESSION["Zeai_cn__mob"]);
					json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
				}
			}
			$SQL = "mob='$mob',RZ='mob'";
		//用户名
		}elseif($TG_set['regkind'] == 2){
			$uname = trimhtml($uname);
			chk_uname($uname,$pwd);
			$SQL = "uname='$uname'";
		}
		/**************** 入库 ******************/
		$uname  = dataIO($uname,'in',20);
		$nickname=trimhtml($nickname);
		$title   =trimhtml($title);
		$SQL .= ",nickname='$nickname'";
		switch ($k) {
			case 1:
				$flag = ($TG_set['regflag'] == 1)?0:1;
				$flag = ($TG_set['active_price'] >0)?2:$flag;//需要交费
				$SQL .= ",flag=".$flag;
				if($flag==1){
					$row    = $db->ROW(__TBL_TG_ROLE__,"grade,title","shopgrade=0 AND ifdefault=1","num");
					$grade  = $row[0];$gradetitle = $row[1];
					$sjtime = ADDTIME;
					$SQL   .= ",grade=".$grade.",gradetitle='".$gradetitle."'";
				}
			break;
			case 2:
				//$shopflag = ($_SHOP['regflag'] == 1)?1:0;
				//$shopflag = ($_SHOP['regifpay'] == 1)?2:$shopflag;//需要交费
				$SQL .= ",title='$title',nickname='$title'";
				$nickname=$title;
			break;
			case 3:break;
			default:json_exit(array('flag'=>0,'msg'=>'k异常'));break;
		}
		$pwd    = md5($pwd);
		$ip     =getip();
		$kind   = ($k==2)?2:1;
		$tguid  = intval($tguid);
		$subscribe = intval($subscribe);
		$db->query("INSERT INTO ".__TBL_TG_USER__." (subscribe,pwd,regtime,endtime,regip,endip,kind,openid,tguid) VALUES ($subscribe,'".$pwd."',".ADDTIME.",".ADDTIME.",'$ip','$ip',$kind,'$cook_tg_openid',$tguid)");
		$tg_uid = intval($db->insert_id());
		if(is_weixin() && !empty($cook_tg_openid)){
			$rowu = $db->ROW(__TBL_USER__,"id,subscribe","openid='$cook_tg_openid'","num");
			if ($rowu){
				$uid       = intval($rowu[0]);
				$subscribe = intval($rowu[1]);
				$SQL .= ",uid=".$uid;
				if($subscribe==1)$SQL .= ",subscribe=1";
			}
		}
		$db->query("UPDATE ".__TBL_TG_USER__." SET ".$SQL." WHERE id=".$tg_uid);
		setcookie("cook_tg_uid",$tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_mob",$mob,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_uname",$uname,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_pwd",$pwd,time()+720000,"/",$_ZEAI['CookDomain']);
		$nickname=(empty($nickname))?$mob:$nickname;
		if ($TG_set['regkind'] == 1){
			unset($_SESSION["Zeai_cn__verify"]);
			unset($_SESSION["Zeai_cn__mob"]);
		}
		if(ifint($tguid))TG($tguid,$tg_uid,'tg_reg');
		$jumpurl=(!empty($jumpurl))?$jumpurl:'tg_reg.php?submitok=success&k='.$k.'&nickname='.$nickname.'&loginkind='.$loginkind.'&title='.$title;
		json_exit(array('flag'=>1,'jumpurl'=>$jumpurl));
	break;
	case 'ajax_reg_verify':
		if ($TG_set['regkind'] != 1)exit(JSON_ERROR);
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}else{
			if ($db->ROW(__TBL_TG_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)"))json_exit(array('flag'=>0,'msg'=>'此手机号码已被注册，请更换'));
		}
		if ($Temp_regyzmrenum > $_SMS['sms_yzmnum'] && $_SMS['sms_yzmnum']>0 )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
		$_SESSION['Zeai_cn__verify'] = cdstr(4);
		//sms
		$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__verify']);
		if ($rtn == 0){
			setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,time()+720000,"/",$_ZEAI['CookDomain']);  
			$chkflag = 1;
			$content = '验证码发送成功，请注意查收';
		}else{
			$chkflag = 0;
			$content = "发送失败,错误码：$rtn";
		}
		//sms end
		$_SESSION['Zeai_cn__mob'] = $mob;
		json_exit(array('flag'=>$chkflag,'msg'=>$content));
	break;
	case 'ajax_reg_verify_chk':
		if ($_SESSION['Zeai_cn__verify'] != $verify)json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
		json_exit(array('flag'=>1));
	break;
}
function chk_uname($uname,$pwd){
	global $db,$TG_set;
	if ($TG_set['regkind'] == 1){
		if (!ifmob($uname))json_exit(array('flag'=>0,'msg'=>'请输入正确的手机号码'));
		$unamestr = '手机号码';
	}elseif($TG_set['regkind'] == 2){
		if (str_len($uname) > 20 || str_len($uname) < 3)json_exit(array('flag'=>0,'msg'=>'请输入正确的登录帐号(3~20字符)'));
		if (ifmob($uname) || ifint($uname))json_exit(array('flag'=>0,'msg'=>'登录用户名不能是手机号码和纯数字'));
		if (str_len($uname) > 20 || str_len($uname)<3 || !preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u',$uname) )json_exit(array('flag'=>0,'msg'=>'请输入正确的用户名（3~15位字母或加数字组合）'));
		$unamestr = '用户名';
	}
	$row = $db->ROW(__TBL_TG_USER__,'id',"uname='".$uname."' OR (mob='$uname' AND FIND_IN_SET('mob',RZ)) ");
	if($row)json_exit(array('flag'=>0,'msg'=>'此'.$unamestr.'已被注册,请重新输入'));
	if (str_len($pwd) > 20 || str_len($pwd)<6 )json_exit(array('flag'=>0,'msg'=>'请输入正确的密码(长度6~20)'));
}
$headertitle = $TG_set['tgytitle'].'注册-';$nav = '';require_once ZEAI.'m1/header.php';?>
<link href="<?php echo HOST;?>/m1/css/tg_loginreg.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<?php 
$mini_title_str='';	
if($submitok == 'success' || $submitok == 'flag2')$mini_title_str='';
$mini_title .= '<a href="javascript:history.back(-1);" class="ico goback">&#xe602;</a>'.$mini_title_str;
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');
if ($submitok == 'success'){?>
    <div class="tgregsuccess">
        <br><br>
        <i class="ico">&#xe60d;</i>
        <h2><font class="S16"><?php echo $nickname;?>，ID：<?php echo $cook_tg_uid;?></font><br><br><font class="B S18">恭喜您帐号注册成功</font></h2>
        <br><br>
		<?php switch ($k){case 1:?>
            <a href="tg_my.php" class="btn size5 HONG4 W80_ yuan">进入<?php echo $TG_set['navtitle'];?></a>
		<?php break;case 2://跳到入驻页面?>
        	<a href="<?php echo HOST;?>/m4/shop_my_apply.php" class="btn size5 HONG4 W80_ yuan">下一步</a>
        <?php break;case 3:?>
        	<a href="<?php echo HOST;?>/m4/shop_index.php" class="btn size5 HONG4 W80_ yuan">进入<?php echo $_SHOP['title'];?></a>
        <?php break;}?>
    </div>
<?php }elseif($submitok == 'flag0'){
	$row = $db->ROW(__TBL_TG_USER__,"nickname,mob,kind,flag","id=".$cook_tg_uid,"num");
	if ($row){
		$uname= $row[0];
		$mob  = $row[1];
		$kind = $row[2];
		$flag = $row[3];
	}
	if($flag==1)header("Location: tg_my.php");
	$uname = (empty($uname))?$mob:$uname;
	switch ($kind) {
		case 1:$kind_str='个人';break;
		case 2:$kind_str='商户';break;
		case 3:$kind_str='机构';break;
	}
	
	?>
    <style>
	.tg_reg_kefu{margin-top:90px}
	.tg_reg_kefu img{width:25%;margin:10px auto;display:block;padding:10px;border:#eee 1px solid}
	.tg_reg_kefu font{color:#999}
	.tg_reg_kefu a{margin-top:10px;display:block;color:#666}
	.tg_reg_kefu .ico{margin-right:4px;}
    </style>
    <div class="submain" style="text-align:center">
        <i class="ico" style="font-size:60px;color:#F7564D;margin-bottom:20px">&#xe634;</i>
        <h4 class="B">您的帐号“<?php echo $uname;?>，ID：<?php echo $cook_tg_uid;?>”审核中<br><br>请耐心等待...</h4><br>
        <h5 style="display:none">主体类型：【<?php echo $kind_str;?>】</h5>
        <br><br>
        <h3><?php echo $title;?></h3>
        <a href="tg_index.php" class="btn size4 HONG4 yuan" style="width:60%">进入<?php echo $TG_set['navtitle'];?>首页</a>
        <div class="tg_reg_kefu">
        <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><br><?php }?>
        <?php if (!empty($kf_tel)){?>
            <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
        <?php }else{?>
            <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
        <?php }?>
        </div>
    </div>
<?php }elseif($submitok == 'flag_1'){
	$row = $db->ROW(__TBL_TG_USER__,"nickname,mob,kind,flag","id=".$cook_tg_uid,"num");
	if ($row){
		$uname= $row[0];
		$mob  = $row[1];
		$kind = $row[2];
		$flag = $row[3];
	}
	if($flag==1)header("Location: tg_my.php");
	$uname = (empty($uname))?$mob:$uname;
	switch ($kind) {
		case 1:$kind_str='个人';break;
		case 2:$kind_str='公司';break;
		case 3:$kind_str='机构';break;
	}
	?>
    <div class="submain" style="text-align:center">
    
    <i class="ico" style="font-size:60px;color:#247AF2">&#xe61f;</i>
    <h4 class="B" style="color:#247AF2">您的帐号“<?php echo $uname;?>，ID：<?php echo $cook_tg_uid;?>”已被锁定</h4><br>
    <h5 style="display:none">主体类型：【<?php echo $kind_str;?>】</h5>
    <br><br>
    <h3><?php echo $title;?></h3>
    <a href="tg_index.php" class="btn size4 HONG4 yuan" style="width:60%">进入<?php echo $TG_set['navtitle'];?></a>
	</div>
<?php }elseif($submitok == 'flag2'){
	$row = $db->ROW(__TBL_TG_USER__,"nickname,mob,flag","id=".$cook_tg_uid,"num");
	if ($row){
		$uname= $row[0];
		$mob  = $row[1];
		$flag = $row[2];
	}
	if($flag==1)header("Location: tg_my.php");
	$uname = (empty($uname))?$mob:$uname;
	?>
    <div class="submain" style="text-align:center">
    <i class="ico2" style="font-size:80px;color:#FF6A00">&#xe69d;</i><br><br>
    <h3 class="B" style="color:#FF6A00">激活推广帐号</h3><br>
    <h4>你的帐号“<?php echo $uname;?>，ID:<?php echo $cook_tg_uid;?>”还没激活</h4><br>
    <?php if ($TG_set['active_price']>0){?><h4>请支付 <b class="Cf00 S24" style="font-family:Arial">¥<?php echo $TG_set['active_price'];?></b> 元激活</h4><br><?php }?>
    <br><br>
    <h3><?php echo $title;?></h3>
    <a class="btn size4 HONG4 yuan" style="width:60%" onClick="tg_jh(<?php echo $TG_set['active_price'];?>);"><?php echo ($TG_set['active_price']>0)?'立即支付':'开始激活';?></a>
	</div>
	<script>
    <?php
    $orderid = 'TG-'.$cook_tg_uid.'-'.date("YmdHis");
    ?>
    var kind = 6,orderid = '<?php echo $orderid;?>';
    function tg_jh(money){
        var jsonurl={'url':HOST+'/m1/tg_reg'+zeai.extname,'js':1,'data':{'submitok':'ajax_pay_jh','money':money,'paykind':'wxpay','kind':kind  ,'orderid':orderid}};
    <?php //if (is_weixin()){?>
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
                                setTimeout(function(){zeai.openurl(rs.redirect_url);},1000);
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
			}else if(rs.flag=='success'){
				 zeai.msg(rs.msg);
				setTimeout(function(){zeai.openurl('tg_my'+zeai.extname);},1000);
            }else{
                zeai.msg(rs.msg);	
            }
        });
    <?php //}else{ ?>
        //zeai.msg('请在微信中使用');
    <?php //}?>
    }
    </script>    
    
<?php }else{
	if (ifint($cook_tg_uid)){
		$row = $db->ROW(__TBL_TG_USER__,"nickname","id=".$cook_tg_uid." AND pwd='".$cook_tg_pwd."'","num");
		if ($row){?>
        <div class="tgreg" style="text-align:center;margin-top:60px">
        <form>
        <i class="ico" style="font-size:60px;color:#45C01A;margin-bottom:20px">&#xe60d;</i>
        <h4 class="B">您的帐号“<?php echo dataIO($row[0],'out');?>，ID：<?php echo $cook_tg_uid;?>”已登录</h4><br>
        <h3>请选择下方按钮进入</h3><br><br>
        <a href="<?php echo HOST;?>/m4/shop_index.php" class="btn size5 BAI W80_ yuan" style="width:60%">进入<?php echo $_SHOP['title'];?></a><br><br>
        <a href="tg_my.php" class="btn size5 BAI W80_ yuan" style="width:60%">进入<?php echo $TG_set['navtitle'];?></a>
        </form>
        </div>
		<?php 
		exit;
		}
	}?>
<div class="tgreg fadeInL">
    <h1>用户注册</h1>
    <div class="linebox" ><div class="line W50"></div><div class="title BAI C666">输入 <?php echo $TG_set['tgytitle'].'/'.$_SHOP['title'];?>/买家 帐号注册</div></div>
    <form id="WWW__ZEAI_CN_form" method="post">
    <?php if ($TG_set['regkind'] == 1){?>
        <dl><dt><i class="ico">&#xe627;</i></dt><dd><input name="mob" type="text" class="input_login" id="mob" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*"></dd></dl>
        <dl><dt><i class="ico">&#xe6c3;</i></dt><dd class="yzmF">
        <input name="verify" id="verify" type="text" required class="input_login" maxlength="4" placeholder="输入手机短信验证码" autocomplete="off" /><a href="javascript:;" class="yzmbtn" id="yzmbtn">获取验证码</a>
        </dd></dl>
    <?php }elseif($TG_set['regkind'] == 2){ ?>
        <dl><dt><i class="ico">&#xe645;</i></dt><dd><input name="uname" type="text" class="input_login" id="uname" placeholder="请输入登录用户名" autocomplete="off" maxlength="20"></dd></dl>
    <?php }?>
    <dl><dt><i class="ico pass">&#xe61e;</i></dt><dd><input name="pwd" type="password" class="input_login" id="pwd" placeholder="请输入登录密码" autocomplete="off" maxlength="20"></dd></dl>
    <?php if ($k==2){?>
        <dl><dt><i class="ico">&#xe63c;</i></dt><dd><input name="title" type="text" class="input_login" id="title" placeholder="请输入商户、企业名称" autocomplete="off" maxlength="30"></dd></dl>
    <?php }else{?>
        <dl><dt><i class="ico">&#xe64d;</i></dt><dd><input name="nickname" type="text" class="input_login" id="nickname" placeholder="请输入网名/昵称" autocomplete="off" maxlength="20"></dd></dl>
    <?php }?>
    <input type="button" value="立即注册" class="btn size4 HONG2 B ed" id="regbtn">
    <input type="hidden" name="k" id="k" value="<?php echo $k;?>">
    <input type="hidden" name="tguid" id="tguid" value="<?php echo $tguid;?>">
    <input type="hidden" name="subscribe" id="subscribe" value="<?php echo $subscribe;?>">
    <input type="hidden" name="loginkind" id="loginkind" value="<?php echo $loginkind;?>">
    <input type="hidden" name="jumpurl" id="jumpurl" value="<?php echo $jumpurl;?>">
    <input type="hidden" name="submitok" value="ajax_reg_chk">
    </form>
    <div class="areg"><a href="tg_login.php?subscribe=<?php echo $subscribe;?>&tguid=<?php echo $tguid;?>&loginkind=<?php echo $loginkind;?>&jumpurl=<?php echo $jumpurl;?>" class="S16">已有帐号，这边登录<i class="ico">&#xe601;</i></a></div>
    <div class="kefu">
    <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><br><?php }?>
    <?php if (!empty($kf_tel)){?>
        <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
    <?php }else{?>
        <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
    <?php }?>
    </div>
	<script>var regkind=<?php echo $TG_set['regkind'];?>,kind_str='商户、企业';</script>
	<script src="<?php echo HOST;?>/m1/js/tg_reg.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</div>
<?php }?>
</body></html>
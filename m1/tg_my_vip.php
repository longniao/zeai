<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
if(!ifint($cook_tg_uid))json_exit(array('flag'=>'nologin_tg','msg'=>'请先登录后再来','jumpurl'=>HOST.'/m1/tg_my.php'));
$currfields = "photo_s,grade,kind,openid,subscribe";
require_once 'tg_chkuser.php';
$data_photo_s=$row['photo_s'];
$data_grade=$row['grade'];
$data_kind =$row['kind'];
$data_openid    =$row['openid'];
$data_subscribe =$row['subscribe'];
function tg_utitle($grade) {
	global $db;
	$row_role=$db->ROW(__TBL_TG_ROLE__,"title","shopgrade=0 AND grade=".$grade,"name");
	return $row_role['title'];
}
/******************************************************/
//kind，1:用户升级,2:loveb充值,3:余额充值,3:余额充值，4活动报名费，5推广升级，6激活
if($submitok == 'ajax_pay_money_loveb'){
	$grade = intval($grade);
	$return_url = HOST.'/m1/tg_my.php';
	$jump_url   = HOST.'/m1/tg_my.php';
	//
	$row_role=$db->ROW(__TBL_TG_ROLE__,"vip_tj_minUnum,price,title","shopgrade=0 AND grade=".$grade,"name");
	$vip_tj_minUnum=$row_role['vip_tj_minUnum'];$price=$row_role['price'];$title = trimhtml(dataIO($row_role['title'],'out'));
	if($vip_tj_minUnum>0){
		$totalnum1 = $db->COUNT(__TBL_TG_USER__,"tgflag=1 AND tguid=".$cook_tg_uid);
		$totalnum2 = $db->COUNT(__TBL_USER__,"tgflag=1 AND tguid=".$cook_tg_uid);
		$totalnum=$totalnum1+$totalnum2;
		if($totalnum<$vip_tj_minUnum)json_exit(array('flag'=>0,'msg'=>'您当前发展的小伙伴(单身+合伙人)不足'.$vip_tj_minUnum.'人，加油'));
	}
	if($price!=$money)json_exit(array('flag'=>0,'msg'=>'请联系管理员'));
	if($money==0){
		$db->query("UPDATE ".__TBL_TG_USER__." SET grade=$grade,sjtime=".ADDTIME." WHERE grade<=$grade AND id=".$cook_tg_uid);
		//站内通知
		$C = '恭喜您【'.$title.'】升级成功!';
		$db->SendTip($cook_tg_uid,'恭喜你【'.$title.'】升级成功!',dataIO($C,'in'),'tg');
		//微信通知
		if (!empty($data_openid) && $data_subscribe==1){
			$keyword1 = urlencode('恭喜你【'.$title.'】升级成功');
			$keyword3 = urlencode($_ZEAI['siteName']);
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		json_exit(array('flag'=>8,'jump_url'=>$jump_url,'msg'=>'恭喜你【'.$title.'】免费升级成功！'));
	}
	if ($kind != 5)json_exit(array('flag'=>0,'msg'=>'kind!=5，请联系管理员'));//kind=5全民红娘升级
	require_once ZEAI.'cache/config_vip.php';
	require_once ZEAI.'cache/config_pay.php';
	if ($data_grade>$grade){
		json_exit(array('flag'=>0,'msg'=>'亲，只能升级不能降级哦'));	
	}
	$paymoney = abs(round($money,2));
	$orderid_title = tg_utitle($grade).'升级';
	if(str_len($orderid) <10 )json_exit(array('flag'=>0,'msg'=>'订单号异常~'));
	if ($paykind=='wxpay' || $paykind=='alipay'){
		$rowpay = $db->ROW(__TBL_PAY__,"flag","orderid='$orderid'",'num');
		if ($rowpay){
			if ($rowpay[0] == 1)json_exit(array('flag'=>0,'msg'=>'该订单已支付完成，您无需重复支付了'));	
		}else{
			$money_list_id = intval($grade);
			$paytime       = 0;
			$db->query("INSERT INTO ".__TBL_PAY__."(orderid,kind,title,money,paymoney,addtime,money_list_id,paytime,tg_uid) VALUES ('$orderid',$kind,'$orderid_title',$money,$paymoney,".ADDTIME.",$money_list_id,$paytime,$cook_tg_uid)");
			$payid = $db->insert_id();
		}
		/*====================测试支付用户ID=====================*/
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
				json_exit(array('flag'=>0,'trade_type'=>'H5','msg'=>'H5调起支付失败【'.$wxpay_arr['err_code'].'】','redirect_url'=>$redirect_url));
			}
		}
		
	}
	exit;
}
/******************************************************/
switch ($data_kind) {
	case 1:$kind_str='个人';break;
	case 2:$kind_str='商户';break;
	case 3:$kind_str='机构';break;
}
$row_role=$db->ROW(__TBL_TG_ROLE__,"title,logo","shopgrade=0 AND grade=".$data_grade);
$data_gradetitle=$row_role['title'];
$data_logo=$row_role['logo'];
$photo_m_url = (!empty($data_photo_s ))?$_ZEAI['up2'].'/'.smb($data_photo_s,'m'):HOST.'/res/tg_my_u'.$data_kind.'.png';
if(!empty($data_logo)){
	$gradeico_str='<img src="'.$_ZEAI['up2'].'/'.$data_logo.'">';
}else{
	$gradeico_str='<img src="'.HOST.'/res/tg_ico.svg">';
}
/*
switch ($submitok) {
	case 'ajax_modupdate23':
		$title      = dataIO($title,'in',200);
		$content    = dataIO($content,'in',50000);
		$tel        = dataIO($tel,'in',100);
		$worktime   = dataIO($worktime,'in',200);
		$address    = dataIO($address,'in',100);
		//
		$job        = dataIO($job,'in',200);
		$areaid     = dataIO($areaid,'in',100);
		$areatitle  = dataIO($areatitle,'in',100);
		$qq         = dataIO($qq,'in',15);
		$weixin     = dataIO($weixin,'in',50);
		$email      = dataIO($email,'in',50);
		//
		$setsql  = "title='$title',content='$content',tel='$tel',worktime='$worktime',address='$address',areaid='$areaid',areatitle='$areatitle'";
		$setsql .= ",weixin='$weixin',qq='$qq',job='$job',email='$email'";
		$db->query("UPDATE ".__TBL_TG_USER__." SET ".$setsql." WHERE id=".$cook_tg_uid);
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
}
*/
$RZarr = explode(',',$data_RZ);
?>
	<style>
	.TG_vip .topbg{width:100%;height:260px;position:relative}
	.TG_vip .topbg{background:url('../../res/tg_bg_vip.jpg') center;background-size:100%}
	
	.TG_vip .tg_title{height:50px}
	.TG_vip .photo_s{position:relative;left:32px;text-align:left}
	.TG_vip .photo_s em{text-align:left;padding-left:55px;color:#fff}
	.TG_vip .photo_s em h4{font-size:16px}
	.TG_vip .photo_s em span{font-size:16px}
	.TG_vip .photo_s em span img{width:20px;vertical-align: middle;margin-right:3px}

	.TG_vip .topbg .ul{width:92%;left:4%;text-align:left;position:absolute;top:130px}
	.TG_vip .topbg .ul li{width:100%;padding:20px 20px 0 20px;margin-bottom:20px;background-color:#fff;border-radius:12px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;position:relative}
	.TG_vip .topbg .ul li .title img{width:30px;display:block;float:left;margin-right:10px}
	.TG_vip .topbg .ul li .title h3{color:#F7564D;font-size:18px;font-weight:bold}
	.TG_vip .topbg .ul li .pricebox{margin-top:15px;color:#999;font-size:15px;line-height:20px;clear:both;overflow:auto}
	.TG_vip .topbg .ul li .pricebox .price{float:left;margin-right:20px}
	.TG_vip .topbg .ul li .pricebox .price b{color:#F7564D;font-family:Arial;font-size:20px}
    .TG_vip .topbg .ul li .pricebox .price2{float:left;font-size:13px;text-decoration:line-through}
	.TG_vip .topbg .ul li .condition{color:#666;font-size:14px;margin-top:8px}
	.TG_vip .topbg .ul li .condition font{color:#F7564D;margin:0 5px}
	.TG_vip .topbg .ul li button{position:absolute;right:15px;top:15px}
	.TG_vip .topbg .ul li .title2{color:#666;border-top:#eee 1px solid;border-bottom:#eee 1px solid;margin:10px 0;padding:10px 0}
	
	.TG_vip .topbg .ul li .morebtn{border:#eee 1px solid;border-bottom:0;border-radius:10px 10px 0 0 ;display:block;width:30%;height:22px;line-height:22px;font-size:18px;color:#999;margin:0 auto;text-align:center;background-color:#fafafa}
	
	.TG_vip .detaill{width:100%;height:5px;overflow:hidden;-webkit-transition:all .5s;-moz-transition:all .5s;transition:all .5s;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
	.TG_vip .detaill dl{width:100%;height:30px;border-bottom:#eee 0px solid}
	.TG_vip .detaill dl dt,.TG_vip .detaill dl dd{height:30px;line-height:30px}
	.TG_vip .detaill dl dt{float:left;width:38%;color:#F7564D;overflow:hidden}
	.TG_vip .detaill dl dd{float:right;width:62%;overflow:hidden}
    </style>
	<div class="TG TG_vip">
    	<i class="ico goback Ugoback" id="ZEAIGOBACK-tg_my_vip">&#xe602;</i>
        <div class="topbg">
            <div class="tg_title">多重奖励机制 <i class="ico">&#xe62d;</i> 收益翻翻</div>
            <div class="photo_s">
                <em>
                    <h4><?php echo $cook_tg_uname.'<font class="S12">（ID:'.$cook_tg_uid.'）</font>';?></h4>
                    <span>当前等级：<?php echo $gradeico_str.$data_gradetitle;?></span>
                </em>
                 <a class="size3 btn HUANG2 yuan" style="display:none">查看等级对比<i class="ico S14">&#xe601;</i></a>
            </div>
            <div class="ul">
				<?php 
                $rt=$db->query("SELECT * FROM ".__TBL_TG_ROLE__." WHERE flag=1 AND shopgrade=0 AND grade>".$data_grade." ORDER BY px DESC,id DESC");
                $total = $db->num_rows($rt);
                if ($total == 0) {
                    echo "<li style='min-height:200px;line-height:200px;text-align:center;font-size:16px;color:#F7564D'>暂时无需升级</li>";
                } else {
                    for($i=1;$i<=$total;$i++) {
                        $rows = $db->fetch_array($rt,'all');
                        if(!$rows) break;
                        $id    = $rows['id'];
                        $title = dataIO($rows['title'],'out');
                        $title2 = dataIO($rows['title2'],'out');
                        $vip_tj_minUnum = $rows['vip_tj_minUnum'];
						$logo = $rows['logo'];
						$grade= intval($rows['grade']);
						if(!empty($logo)){
							$ico_url=$_ZEAI['up2'].'/'.$logo;
						}else{
							$ico_url=HOST.'/res/tg_ico.svg';
						}
						$reward_kind = $rows['reward_kind'];
						if($reward_kind=='loveb'){
							$reg_sex1_num1  = intval($rows['reg_loveb_sex1_num1']);
							$reg_sex1_num2  = intval($rows['reg_loveb_sex1_num2']);
							$reg_sex2_num1  = intval($rows['reg_loveb_sex2_num1']);
							$reg_sex2_num2  = intval($rows['reg_loveb_sex2_num2']);
							$dw_str=$_ZEAI['loveB'];
						}elseif($reward_kind=='money'){
							$reg_sex1_num1  = floatval($rows['reg_money_sex1_num1']);
							$reg_sex1_num2  = floatval($rows['reg_money_sex1_num2']);
							$reg_sex2_num1  = floatval($rows['reg_money_sex2_num1']);
							$reg_sex2_num2  = floatval($rows['reg_money_sex2_num2']);
							$dw_str='元';
						}
						$cz_sex1_num1  = intval($rows['cz_sex1_num1']);
						$cz_sex1_num2  = intval($rows['cz_sex1_num2']);
						$cz_sex2_num1  = intval($rows['cz_sex2_num1']);
						$cz_sex2_num2  = intval($rows['cz_sex2_num2']);
			
						$vip_sex1_num1 = intval($rows['vip_sex1_num1']);
						$vip_sex1_num2 = intval($rows['vip_sex1_num2']);
						$vip_sex2_num1 = intval($rows['vip_sex2_num1']);
						$vip_sex2_num2 = intval($rows['vip_sex2_num2']);
						
						$rz_sex1_num1 = intval($rows['rz_sex1_num1']);
						$rz_sex1_num2 = intval($rows['rz_sex1_num2']);
						$rz_sex2_num1 = intval($rows['rz_sex2_num1']);
						$rz_sex2_num2 = intval($rows['rz_sex2_num2']);
						
						$union_reg_num1  = floatval($rows['union_reg_num1']);
						$union_reg_num2  = floatval($rows['union_reg_num2']);
						$union_num1 = intval($rows['union_num1']);
						$union_num2 = intval($rows['union_num2']);
						
						$tx_min_price     = intval($rows['tx_min_price']);
						$tx_daymax_price  = intval($rows['tx_daymax_price']);
						$tx_sxf_bfb       = intval($rows['tx_sxf_bfb']);
						
						$push_kind = dataIO($rows['push_kind'],'out');
						$push_month_apply_num  = intval($rows['push_month_apply_num']);
						$push_month_push_num   = intval($rows['push_month_push_num']);
						
						switch ($data_kind) {
							case 2:$kind_str='商户';break;
							case 3:$kind_str='机构';break;
						}
						$push_kind             = dataIO($rows['push_kind'],'out');
						$push_month_apply_num  = intval($rows['push_month_apply_num']);
						$push_month_push_num   = intval($rows['push_month_push_num']);
						
						$push_kindARR=explode(',',$push_kind);
						$push_kind_str = '';
						if (count($push_kindARR) >= 1 && is_array($push_kindARR)){
							foreach ($push_kindARR as $k=>$V) {
								switch ($V) {
									case 'tips':$push_kind_str .= '<dl><dt></dt><dd><i class="ico S16" style="color:#45C01A">&#xe60d;</i> 站内信通知</dd></dl>';break;
									case 'wxkefu':$push_kind_str .= '<dl><dt></dt><dd><i class="ico S16" style="color:#45C01A">&#xe60d;</i> 公众号消息群发</dd></dl>';break;
									case 'wxkumy':$push_kind_str .= '<dl><dt></dt><dd><i class="ico S16" style="color:#45C01A">&#xe60d;</i> 公众号消息群发(触发式)</dd></dl>';break;
									case 'poster':$push_kind_str .= '<dl><dt></dt><dd><i class="ico S16" style="color:#45C01A">&#xe60d;</i> 弹出海报</dd></dl>';break;
								}
							}
						}
					?>
                    <li>
                        <div class="title"><img src="<?php echo $ico_url;?>"><h3><?php echo $title;?></h3></div>
                        <div class="pricebox">
                            <div class="price"><?php if ($rows['price']==0){echo '<font class="C090 S16">免费</font>';}else{?>现价 ￥<b><?php echo $rows['price'];?></b><?php }?></div>
                            <div class="price2">原价: ￥<?php echo $rows['price2'];?></div>
                        </div>
                        <?php if ($vip_tj_minUnum>0){?>
                        <div class="condition">需要推广满<font><?php echo $vip_tj_minUnum;?></font>个用户（单身+合伙人）可升级</div>
                        <?php }?>
                        <?php if (!empty($title2)){?><div class="title2"><?php echo $title2;?></div><?php }?>
                        <button type="button" class="btn size3 HONG4 yuan" onClick="tg_grade(<?php echo $grade;?>,<?php echo $rows['price'];?>);">立即升级</button>
                    
                        <div class="linebox" style="margin-top:5px"><div class="line W50"></div><div class="title S14 BAI" style="color:#999"><?php echo $title;?>特权/奖励比例</div></div>
                        
                        <div class="detaill">
                            <dl><dt>单身用户注册</dt><dd>直接奖：男<?php echo $reg_sex1_num1;?><?php echo $dw_str;?>　女<?php echo $reg_sex2_num1;?><?php echo $dw_str;?></dd></dl>
                            <dl><dt></dt><dd>团队奖：男<?php echo $reg_sex1_num2;?><?php echo $dw_str;?>　女<?php echo $reg_sex2_num2;?><?php echo $dw_str;?></dd></dl>
                           
                            <dl><dt>单身用户充值</dt><dd>直接奖：男<?php echo ($cz_sex1_num1>0)?$cz_sex1_num1.'%':'无';?>　女<?php echo ($cz_sex2_num1>0)?$cz_sex2_num1.'%':'无';?></dd></dl>
                            <dl><dt></dt><dd>团队奖：男<?php echo ($cz_sex1_num2>0)?$cz_sex1_num2.'%':'无';?>　女<?php echo ($cz_sex2_num2>0)?$cz_sex2_num2.'%':'无';?></dd></dl>
                            
                            <dl><dt>单身用户开通VIP</dt><dd>直接奖：男<?php echo ($vip_sex1_num1>0)?$vip_sex1_num1.'%':'无';?>　女<?php echo ($vip_sex2_num1>0)?$vip_sex2_num1.'%':'无';?></dd></dl>
                            <dl><dt></dt><dd>团队奖：男<?php echo ($vip_sex1_num2>0)?$vip_sex1_num2.'%':'无';?>　女<?php echo ($vip_sex2_num2>0)?$vip_sex2_num2.'%':'无';?></dd></dl>


                            <dl><dt>单身用户认证</dt><dd>直接奖：男<?php echo ($rz_sex1_num1>0)?$rz_sex1_num1.'%':'无';?>　女<?php echo ($rz_sex2_num1>0)?$rz_sex2_num1.'%':'无';?></dd></dl>
                            <dl><dt></dt><dd>团队奖：男<?php echo ($rz_sex1_num2>0)?$rz_sex1_num2.'%':'无';?>　女<?php echo ($rz_sex2_num2>0)?$rz_sex2_num2.'%':'无';?></dd></dl>

							<?php if ($union_reg_num1>0 || $union_reg_num2>0){?>
                             <dl><dt>合伙人注册</dt><dd><?php if ($union_reg_num1>0){?>直接奖：<?php echo $union_reg_num1.$dw_str; }?>　<?php if ($union_reg_num2>0){?>团队奖：<?php echo $union_reg_num2.$dw_str;}?></dd></dl>
                            <?php }?>
                            
                            <?php if ($union_num1>0 || $union_num2>0){?>
                            <dl><dt>合伙人激活/升级</dt><dd><?php if ($union_num1>0){?>直接奖：<?php echo $union_num1;?>%<?php }?>　<?php if ($union_num2>0){?>团队奖：<?php echo $union_num2;?>%<?php }?></dd></dl>
                            <?php }?>
                            
                            <dl><dt>最小提现金额</dt><dd><?php echo $tx_min_price;?>元</dd></dl>
                            <dl><dt>每天最多提现</dt><dd><?php echo $tx_daymax_price;?>元</dd></dl>
                            <dl><dt>提现扣除手续费</dt><dd><?php echo $tx_sxf_bfb;?>%</dd></dl>
                            

                            <dl><dt></dt><dd></dd></dl>
                            <div class="clear"></div>
                        </div>
                        
                        <i class="ico off morebtn" onClick="morebtn(this);">&#xe60b;</i>
                        <div class="clear"></div>
                    </li>
                    <?php
                    }
                }
                ?>
                <div class="clear"></div>
            </div>
        </div>


    </div>
<script>
<?php
$orderid = 'TG-'.$cook_tg_uid.'-'.date("YmdHis");
?>
var kind = 5,orderid = '<?php echo $orderid;?>';
function morebtn(btnobj){
	var H = btnobj.previousElementSibling.scrollHeight;
	if(btnobj.hasClass('off')){
		btnobj.removeClass('off');
		btnobj.addClass('on');
		btnobj.html('&#xe60a;');
		btnobj.previousElementSibling.style.height=H+'px';//auto
	}else{
		btnobj.removeClass('on');
		btnobj.addClass('off');
		btnobj.html('&#xe60b;');
		btnobj.previousElementSibling.style.height='5px';
	}
}
function tg_grade(grade,money){
	var jsonurl={'url':'tg_my_vip'+zeai.extname,'js':1,'data':{grade:grade,'submitok':'ajax_pay_money_loveb','money':money,'paykind':'wxpay','kind':kind  ,'orderid':orderid}};
<?php //if (is_weixin()){?>
	zeai.msg('正在升级..');
	zeai.ajax(jsonurl,function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);
		if (rs.flag==8){
			zeai.msg(rs.msg);
			setTimeout(function(){zeai.openurl(rs.jump_url);},1000);
		}else if(rs.flag==1){
			if (rs.trade_type=='H5'){
				zeai.openurl(rs.redirect_url);
			}else{
				function jsApiCall(){
					WeixinJSBridge.invoke('getBrandWCPayRequest',rs.jsApiParameters,function(res){
						//WeixinJSBridge.log(res.err_msg);
						if(res.err_msg == "get_brand_wcpay_request:ok"){
							zeai.msg("支付成功");
							setTimeout(function(){zeai.openurl(rs.jump_url);},1000);
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
			zeai.msg(rs.msg,{time:3});
		}
	});
<?php //}else{ ?>
	//zeai.msg('请在微信中使用');
<?php //}?>
}
</script>
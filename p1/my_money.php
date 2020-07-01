<?php
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
//$chk_u_jumpurl=HOST.'/p1/my_money.php';
$currfields = 'money,grade,subscribe,openid,nickname';
require_once 'my_chkuser.php';
$data_money     = $row['money'];
$data_grade     = $row['grade'];
$data_subscribe = $row['subscribe'];
$data_openid    = $row['openid'];
$data_nickname  = dataIO($row['nickname'],'out');
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_wxgzh.php';
require_once ZEAI.'cache/udata.php';
$switch = json_decode($_ZEAI['switch'],true);$ifrmbtx_minnum=abs(intval($switch['ifrmbtx_minnum']));
$urole = json_decode($_ZEAI['urole']);
$tg = json_decode($_REG['tg'],true);
$t = (ifint($t,'1-4','1'))?$t:1;

if($submitok == 'ajax_binding'){
	if (str_len($data_openid) >10 && $data_subscribe==1){
		json_exit(array('flag'=>1,'msg'=>'已成功绑定【'.$data_nickname.'(ID：'.$cook_uid.')】'));
	}
	json_exit(array('flag'=>0));
}elseif($submitok == 'ajax_get_ewm'){
	$token = wx_get_access_token();
	$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
	$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"bd_'.$cook_uid.'"}}}';
	$ticket = Zeai_POST_stream($ticket_url,$ticket_data);
	$T = json_decode($ticket,true);
	$qrcode_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
	json_exit(array('flag'=>1,'ewm'=>$qrcode_url));
}elseif($submitok == 'ajax_tx_update'){
	if($switch['ifrmbtx'] != 1)json_exit(array('flag'=>0,'msg'=>'提现功能暂时已关闭'));
	
	if(!ifint($tx_money,"0-9","1,5"))json_exit(array('flag'=>0,'msg'=>'提现的金额必须是正整数'));
	if($tx_money<$ifrmbtx_minnum)json_exit(array('flag'=>0,'msg'=>'提现金额必须大于'.$ifrmbtx_minnum.'元'));
	$tx_money = abs(intval($tx_money));
	if($tx_money > $data_money)json_exit(array('flag'=>0,'msg'=>'账户余额不足'.$tx_money));
	$endnum  = $data_money-$tx_money;
	$orderid = "TX_".$cook_uid."_".$tx_money."_".date("Ymdhis");
	$db->query("UPDATE ".__TBL_USER__." SET money=".$endnum." WHERE money>=".$tx_money." AND id=".$cook_uid);
	//写清单
	$title   = "申请提现(手机)";
	$content = "申请提现<span class=\"Cf60 S12\">（等待处理）</span>";
	$money_list_id = $db->AddLovebRmbList($cook_uid,$content,-$tx_money,'money',2);
	$paymoney = intval($switch['ifrmbtx_num']*$tx_money);
	//生成提现记录
	$db->query("INSERT INTO ".__TBL_PAY__." (uid,orderid,money_list_id,kind,title,money,paymoney,addtime) VALUES ($cook_uid,'$orderid',$money_list_id,-1,'$title','$tx_money','$paymoney',".ADDTIME.")");
	//站内消息
	$C = $cook_nickname.'您好，您的余额账户资金有变动！　　<a href='.Href('money').' class=aQING>查看详情</a>';
	$db->SendTip($cook_uid,'余额提现',dataIO($C,'in'),'sys');
	//账户资金变动提醒
	if (!empty($data_openid)){
		$first  = urlencode($cook_nickname."您好，您的余额账户资金有变动：");
		$remark = urlencode("申请提现扣除");
		@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$tx_money.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('money')));
	}
	//
	json_exit(array('flag'=>1,'msg'=>'提现申请已提交，请等待处理'));
}

$zeai_cn_menu = 'my_money';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的余额 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_loveb.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<script src="js/my_money.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>我的余额</h1>
        <div class="tab">
			<?php
            if ($t==2){
				if(!empty($i))$TGsql = " AND content LIKE '%".$i."%' ";
				$rt = $db->query("SELECT content,num,endnum,addtime FROM ".__TBL_MONEY_LIST__." WHERE uid=".$cook_uid.$TGsql." ORDER BY id DESC");
				$total = $db->num_rows($rt);
			}
            ?>
            <a href="<?php echo SELF;?>?t=1"<?php echo ($t==1)?' class="ed"':'';?>>我的余额</a>
            <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>余额明细</a>
            <a href="<?php echo SELF;?>?t=3"<?php echo ($t==3)?' class="ed"':'';?>>在线充值</a>
            <?php if($switch['ifrmbtx'] == 1){ ?>
            <a href="<?php echo SELF;?>?t=4"<?php echo ($t==4)?' class="ed"':'';?>>余额提现</a>
            <?php }?>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="my_loveb my_money">
				<?php
				//我的余额
				if($t==1){
				?>
				<div class="ye">
                    <div class="boxx">
                        <div class="dt"><i class="ico">&#xe635;</i></div>
                        <div class="dd">
                        	<span class="LoveB"><?php echo $data_money; ?><font>元</font></span>
                            <br>
                        	<a class="btn size3 BAI" href="<?php echo SELF;?>?t=3">充值</a>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="loveBdetail">
						● 余额就是现金，和人民币等额<br>
                        <?php if(in_array('hb',$navarr)){?>● 余额可用来发红包<br><?php }?>
                        ● 可用来兑换<?php echo $_ZEAI['loveB'];?>（<?php echo $_ZEAI['loveB'];?>账户充值选余额支付），<?php echo $_ZEAI['loveBrate'].$_ZEAI['loveB']; ?>=1元<br>
                        <?php if($switch['ifrmbtx'] == 1){ ?>● 可以提现至您的微信钱包<br><?php }?>
                        <button class="btnA" onClick="supdes=zeai.div({obj:my_money_getTip,title:'如何获取余额？',w:450,h:220});">如何获取余额？</button>
                    </div>
                </div>
                <?php
				/*************helpDiv Start*************/?>
                <div id="my_money_getTip" class="helpDiv my_loveb_getTip">
					<?php if(in_array('hb',$navarr)){?><dl><dt>向别人讨红包</dt><dd><button onClick="zeai.openurl('my_hongbao.php?t=4')">我要讨红包</button></dd></dl><?php }?>
                    <dl><dt>在线充值</dt><dd><button onClick="zeai.openurl('<?php echo SELF;?>?t=3')">我要充值</button></dd></dl>
                    <?php if(in_array('tg',$navarr)){?>
                    <dl><dt>推荐分享新会员注册奖励</dt><dd><button onClick="zeai.msg('请用手机微信扫描网站底部二维码进入使用',{time:3});setTimeout(function(){supdes.click();zeai.setScrollTop(2000);},2000);">我要推荐</button></dd></dl>
                    <?php }?>
                </div>
				<?php /*************helpDiv End*************/
				//明细
				}elseif($t==2){
					if($total>0){$page_skin=2;$pagemode=4;$pagesize=11;$page_color='#E83191';require_once ZEAI.'sub/page.php';
					?>
                    <table class="tablelist">
                    <tr>
                    <td width="200" class="list_title">结算时间</td>
                    <td class="list_title">结算项目</td>
                    <td width="150" class="list_title center">加减</td>
                    <td width="80" class="list_title center">账户余额(元)</td>
                    </tr>
                    <?php
                    for($i=1;$i<=$pagesize;$i++) {
                        $rows = $db->fetch_array($rt,'num');
                        if(!$rows) break;
                        $content  = $rows[0];
                        $num      = $rows[1];
                        $endnum   = $rows[2];
                        $addtime  = YmdHis($rows[3]);
                        if ($num<0){
                            $numstyle = " C00f";
                        }else{
                            $numstyle = " Cf00";
                            $num = '+'.$num;
                        }
                    ?>
                    <tr>
                    <td width="160" class="C8d S12"><?php echo $addtime;?></td>
                    <td class="S12 C666"><?php echo $content;?></td>
                    <td width="150" class="center"><font class="<?php echo $numstyle; ?>"><?php echo $num;?></font></td>
                    <td width="80" class="center C8d S12"><?php echo $endnum; ?></td>
                    </tr>
                    <?php } ?>
                    </table>
                    <?php
					if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox mypagebox">'.$pagelist.'</div>';
					}else{echo nodatatips('暂无明细内容');}
				}elseif($t==3){?>
                    <form id="zeaiFORM" class="cz">
                    <dl>
                        <dt>充值数量</dt>
                        <dd id="numlist">
                            <em rmb="10">10元<div class="ibox"><h4></h4></div></em>
                            <em rmb="50" class="ed">50元<div class="ibox"><h4></h4></div></em>
                            <em rmb="100">100元<div class="ibox"><h4></h4></div></em>
                            <em rmb="500">500元<div class="ibox"><h4></h4></div></em>
                            <em rmb="1000">1000元<div class="ibox"><h4></h4></div></em>
                            <em rmb="2000">2000元<div class="ibox"><h4></h4></div></em>
                            <em rmb="5000">5000元<div class="ibox"><h4></h4></div></em>
                        </dd>
                    </dl>
                    <dl style="border:0"><dt>应付金额</dt><dd><span id="price"></span><span id="pricetitle"></span></dd></dl>
                    <div style="text-align:center;margin:20px auto 50px auto"><button type="button" class="btn size4 LV2 W300" id="my_money_nextbtn">下一步</button></div>
                    <input type="hidden" id="jumpurl" value="<?php echo $jumpurl;?>">
                    <input type="hidden" id="money" value="0">
                    <input type="hidden" id="kind" value="3">
              		</form>
             		<script>
                    var czlist=numlist.getElementsByTagName("em");
					my_money_Fn(czlist);
					o('my_money_nextbtn').onclick=my_money_nextbtnFn;
                    </script>
				<?php	
				}elseif($t==4 && $switch['ifrmbtx'] == 1){?>
                    <div class="tx">
                        <h3>请选择提现金额<br><span class="S14 C999">当前可提现余额：<font class="Cf00">￥<?php echo $data_money; ?>元</font></span></h3>
                        <form id="z_e_a_i__c_n__tx_form">
                        <ul><li><input type="radio" name="tx_money" id="tx_money1" class="radioskin" value="<?php echo $ifrmbtx_minnum;?>"><label for="tx_money1" class="radioskin-label"><i></i><b class="W80 S18"><?php echo $ifrmbtx_minnum;?>元</b></label></li></ul>
                        <ul><li><input type="radio" name="tx_money" id="tx_money2" class="radioskin" value="100" checked><label for="tx_money2" class="radioskin-label"><i></i><b class="W80 S18">100元</b></label></li></ul>
                        <ul><li><input type="radio" name="tx_money" id="tx_money3" class="radioskin" value="200"><label for="tx_money3" class="radioskin-label"><i></i><b class="W80 S18">200元</b></label></li></ul>
                        <ul><li><input type="radio" name="tx_money" id="tx_money4" class="radioskin" value="500"><label for="tx_money4" class="radioskin-label"><i></i><b class="W80 S18">500元</b></label></li></ul>
                        <ul><li><input type="radio" name="tx_money" id="tx_money5" class="radioskin" value="1000"><label for="tx_money5" class="radioskin-label"><i></i><b class="W80 S18">1000元</b></label></li></ul>
                        <input type="hidden" name="submitok" value="ajax_tx_update">
                        </form>
                       <?php if ($switch['ifrmbtx_num'] < 1){ ?>
                       <h5>提现将收取<?php echo (1-$switch['ifrmbtx_num'])*100; ?>%的手续费</h5>
                       <?php }?>
                       <div style="text-align:center;margin:30px auto"><button type="button" class="btn size4 HONG2 W200" id="my_money_txbtn">下一步</button></div>
                       
                        <div class="linebox">
                            <div class="line"></div>
                            <div class="title BAI S14">温馨提醒</div>
                        </div>
                        <?php if ($switch['ifrmbtx_num'] < 1){ ?>
                        <h6>为了补贴网站运营和服务器成本，我们将扣除部分手续费，实际到账<?php echo $switch['ifrmbtx_num']*100; ?>％，有小数将取整</h6>
                        <?php }?>
                        <h6>提现款项将直接打入绑定者的微信零钱钱包</h6>
                        <script>my_money_txbtn.onclick = my_money_txbtnFn;</script>
                    </div>
				<?php	
				}
            	?>
        	</div>
        </div>
        <!-- end C -->
</div></div></div>

<?php
if ($t==4 && $switch['ifrmbtx'] == 1 && !empty($_GZH['wx_gzh_ewm']) && ($data_subscribe==0 || empty($data_openid))){?>
    <div id="subscribe_box_my_money_tx" class="my-subscribe_box"><img id="Z__e_A___I_c____N">
    <h3>请用微信扫码关注公众号开通提现功能<br>提现款项将直接打入绑定者的微信零钱钱包</h3>
    </div>
<?php }?>
<script>var jumpurl  = '<?php echo $jumpurl;?>';</script>
<?php
require_once ZEAI.'p1/bottom.php';ob_end_flush();
?>
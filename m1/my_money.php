<?php
require_once '../sub/init.php';
$currfields = 'money,grade,openid,subscribe';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_wxgzh.php';
require_once ZEAI.'cache/udata.php';

$data_money    = $row['money'];
$data_grade    = $row['grade'];
$data_openid   = $row['openid'];
$data_subscribe= $row['subscribe'];
$switch = json_decode($_ZEAI['switch'],true);$ifrmbtx_minnum=abs(intval($switch['ifrmbtx_minnum']));
//
$curpage = 'my_money';

$urole = json_decode($_ZEAI['urole']);
$tg = json_decode($_REG['tg'],true);
$a = (empty($a))?'ye':$a;


/*************Ajax Start*************/
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时没有内容</div>";
if ($submitok == 'ajax_ye'){ ?>
    <div class="ye fadeInL">
        <div class="box">
            <div class="dt"><i class="ico">&#xe635;</i></div>
            <div class="dd"><span class="money"><?php echo $data_money; ?><font>元</font></span></div>
        </div>
        <div class="moneydetail">
            ● 余额可用来发红包<br>
            ● 可用来兑换<?php echo $_ZEAI['loveB'];?>（<?php echo $_ZEAI['loveB'];?>账户充值选余额支付）<br>
            <?php if($switch['ifrmbtx'] == 1){ ?>● 可以提现至您的微信钱包<?php }?><br><br><br>
            <button class="btnA" onClick="ZeaiM.page.load('m1/my_loveb_getTip.php?backPage=my_money','<?php echo $curpage;?>','my_money_getTip');">如何获得余额？</button>
        </div>
    </div>    
<?php
exit;}elseif($submitok == 'ajax_mx'){
	$p = intval($p);if ($p<1)$p=1;$SQL="";$totalP = intval($totalP);
	if ($p > $totalP)exit($nodatatips);
	if ($p == 1)$SQL = " LIMIT ".$_ZEAI['pagesize'];
	if(!empty($i))$TGsql = " AND content LIKE '%".$i."%' ";
	$rt = $db->query("SELECT id,content,num,addtime FROM ".__TBL_MONEY_LIST__." WHERE uid=".$cook_uid.$TGsql." ORDER BY id DESC ".$SQL);
	$total = $db->num_rows($rt);
	if ($total <= 0)exit($nodatatips);
	if ($p == 1){
		echo '<div class="mx" id="mx">';
		$fort= $total;
	}else{
		$fort= $_ZEAI['pagesize'];
		$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);
	}
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$id       = $rows['id'];
		$content  = dataIO($rows['content'],'out');
		$num      = $rows['num'];
		$addtime  = YmdHis($rows['addtime']);
		if ($num<0){$numstyle = "fs";}else{$numstyle = "zs";$num = '+'.$num;}
		echo '<dl><dt>'.$content.'<font>'.$addtime.'</font></dt><dd class="'.$numstyle.'">'.$num.'</dd></dl>';
	}
	if ($p == 1)echo '</div>';
exit;}elseif($submitok == 'ajax_cz'){ ?>

	<style>
    .my_money .cz{background:#fff;padding:10px 0 20px;text-align:left;margin:20px 0}
    .my_money .cz dl{width:100%;margin:0 auto;clear:both;overflow:auto;border-bottom:#ddd 0.5px solid;padding:10px 0}
    .my_money .cz dl:last-child{border-bottom:0}
    .my_money .cz dl dt,.my_money .cz dl dd{float:left;display:block;line-height:30px}/*;background-color:#fc0*/
    .my_money .cz dl dt,.my_money .cz dl dd{line-height:40px}/*;background-color:#fc0*/
    .my_money .cz dl dt{width:20%;margin:0 5%;font-size:16px;color:#666;text-align:right}
    .my_money .cz dl dd{width:70%;text-align:left}
    .my_money .cz dl dd em{cursor:pointer;float:left;line-height:28px;padding:0 10px;margin:5px 10px 10px 0;border:#cfcfcf 1px solid;color:#666;background-color:#f5f5f5;text-align:center;font-size:16px;position:relative;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-user-select:none;-moz-user-select: none;}
    .my_money .cz dl dd em.ed{color:#A1C655;border:#A1C655 1px solid;background-color:#fff}
    .my_money .cz dl dd em .ibox {width:0;height:0;border-width:7px;border-style:solid;border-color:transparent #95C057 #95C057 transparent;position:absolute;bottom:0;right:0;display:none}
    .my_money .cz dl dd em .ibox h4{background-image:url('res/xgz.png');background-repeat:no-repeat;position:absolute;bottom:-7px;right:-8px;width:14px;height:14px;background-position:left right;background-size:14px 14px;}
    .my_money .cz dl dd em.ed .ibox{display:block}
    .my_money .cz dl dd span:first-child{color:#f60;font-size:16px}
    .my_money .cz dl dd span:last-child{color:#999;font-size:12px}
    .my_money .cz .tips{width:90%;font-size:14px;color:#999;padding:10px 0 ;line-height:200%;box-sizing:border-box;margin:0 auto;text-align:center}
    </style>
    <form id="zeaiFORM" class="cz">
    <dl>
        <dt>充值金额</dt>
        <dd id="numlist">
            <em rmb="10">10元<div class="ibox"><h4></h4></div></em>
            <em rmb="50" class="ed">50元<div class="ibox"><h4></h4></div></em>
            <em rmb="100">100元<div class="ibox"><h4></h4></div></em>
            <em rmb="500">500元<div class="ibox"><h4></h4></div></em>
            <em rmb="1000">1000元<div class="ibox"><h4></h4></div></em>
            <em rmb="2000">2000元<div class="ibox"><h4></h4></div></em>
        </dd>
    </dl>
    <dl><dt>应付金额</dt><dd><span id="price"></span><span id="pricetitle"></span></dd></dl>
    <div style="text-align:center;margin:20px auto"><button type="button" class="btn size4 LV2 W90_" id="nextbtn">下一步</button></div>
    
    <input type="hidden" id="return_okurl" value="<?php echo HOST.'/?z=my&e=my_money&a=ye';?>">
    <input type="hidden" id="return_nourl" value="<?php echo HOST.'/?z=my&e=my_money&a=cz';?>">
    <input type="hidden" id="money" value="0">
    <input type="hidden" id="kind" value="3">
    </form>
	<script>
	var moneyzk=1;
	var czlist  = numlist.getElementsByTagName("em");
    zeai.listEach(czlist,function(obj){
		if (obj.hasClass('ed')){priceInit(obj);}
		obj.onclick=function(){priceInit(obj);cleardom(obj);}
	});
	function priceInit(obj){
		var rmb=obj.getAttribute("rmb");
		o('money').value = rmb;
		price.html(rmb*moneyzk+'元');
		var tt =(moneyzk < 1)?'　('+moneyzk*10+'折优惠)':'';
		pricetitle.html(tt);
	}
	function cleardom(curdom){
		zeai.listEach(czlist,function(obj){obj.removeClass('ed');});
		curdom.addClass('ed');
	}
	nextbtn.onclick=function(){
		if (o('money').value<=0){zeai.msg('请选择');return false;}
		ZeaiM.page.load({url:'m1/my_pay.php',data:{kind:3,money:o('money').value,return_okurl:o('return_okurl').value,return_nourl:o('return_nourl').value}},'<?php echo $curpage;?>','my_pay');
	}
    </script>
<?php exit;}elseif($submitok == 'ajax_tx' && $switch['ifrmbtx'] == 1){?>

<style>
/*mx*/
.my_money .tx{padding:10px 0;margin-bottom:40px}
.my_money .tx h3{margin:25px 0 10px 0}
.my_money .tx ul{border-bottom:#eee 1px solid;cursor:pointer;padding:5px 15px;box-sizing:border-box}
.my_money .tx ul li{text-align:left;width:130px;padding:10px 0;margin:0 auto}
.my_money .tx ul:hover{background-color:#f8f8f8}
.my_money .tx h5{color:#999;padding:20px 0 5px 0}
.my_money .tx h6{color:#999;margin:10px auto 10px auto;text-align:left;width:85%;}
</style>
	<div class="tx">
		<h3>请选择提现金额<br><span class="S14 C999">当前可提现余额：<font class="Cf00">￥<?php echo $data_money; ?>元</font></span></h3>
        <form id="z_e_a_i__c_n__tx_form" method="post">
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
       <div style="text-align:center;margin:20px auto"><button type="button" class="btn size4 LV2 W90_" id="txbtn">下一步</button></div>
        <div class="linebox">
            <div class="line"></div>
            <div class="title BAI S14">温馨提醒</div>
        </div>
        <h6>为了补贴网站运营和服务器成本，我们将扣除部分手续费，实际到账<?php echo $switch['ifrmbtx_num']*100; ?>％，有小数的将取整处理</h6>
		<script>
        txbtn.onclick = function(){
            zeai.confirm('您真的要提现么？',function (){zeai.ajax({'url':'m1/my_money'+zeai.extname,'js':1,'form':z_e_a_i__c_n__tx_form},function(e){rs=zeai.jsoneval(e);
				if (rs.flag==1){
					zeai.msg(rs.msg);
					<?php echo $curpage;?>_yebtn.click();
				}else{
					zeai.msg(rs.msg);
				}
			});});
			
        }
		<?php
		if (!empty($_GZH['wx_gzh_ewm']) && ($data_subscribe!=1)){
			@wx_endurl('您刚刚浏览的页面【余额提现】',HOST.'/?z=my&e=my_money&a=tx');
		?>
		setTimeout(function(){ZeaiM.div({obj:o('subscribe_box_my_money_tx'),w:260,h:280});},500);
		<?php }?>
        </script>
    </div>
<?php
exit;}elseif($submitok == 'ajax_tx_update' && $switch['ifrmbtx'] == 1){
	if(!ifint($tx_money,"0-9","1,9"))json_exit(array('flag'=>0,'msg'=>'提现的金额必须是正整数'));
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
	$C = $cook_nickname.'您好，您的余额账户资金有变动！　<a href='.Href('money').' class=aQING>查看详情</a>';
	$db->SendTip($cook_uid,'余额提现',dataIO($C,'in'),'sys');
	
	//账户资金变动提醒
	if (!empty($data_openid)){
		$first  = urlencode($cook_nickname."您好，您的余额账户资金有变动：");
		$remark = urlencode("申请提现扣除");
		@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$tx_money.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.urlencode(HOST.'/?z=my&e=my_money'));
	}
	//
	json_exit(array('flag'=>1,'msg'=>'提现申请已提交，请等待处理'));
exit;}
/*************Ajax End*************/



/*************Main start*************/
$mini_backT = '';$mini_title = '余额账户';$mini_class='top_mini top_miniMoney';
require_once ZEAI.'m1/top_mini.php';
?>
<link href="m1/css/my_money.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php
$totalnum = $db->COUNT(__TBL_MONEY_LIST__,"uid=".$cook_uid);
$totalP = ceil($totalnum/$_ZEAI['pagesize']);
if ($totalnum > $_ZEAI['pagesize']){ ?>
	<script>
	var submain = '<?php echo $curpage;?>_submain';
	var totalP  = parseInt(<?php echo $totalP; ?>);
	var p=1,i='<?php echo $i; ?>';
	o(submain).onscroll = function(){
		if(!zeai.empty(o('mx'))){
			var t = parseInt(o(submain).scrollTop);
			var cH= parseInt(o(submain).clientHeight);
			var  H= parseInt(o(submain).scrollHeight);
			if (zeai.empty(o('loading_btm')))o(mx).append('<div id="loading_btm"></div>');
			if (H-t-cH <20){//t+cH==H
				var loading_btm = o('loading_btm');
				if (p >= totalP){
					loading_btm.html('已达末页，全部加载结束');
				}else{
					p++;
					o('loading_btm').html('<i class="ico">&#xe502;</i>');
					zeai.ajax({'url':'<?php echo SELF;?>','data':{submitok:'ajax_mx',totalP:totalP,p:p,i:i}},function(e){
						if (e == 'end'){
							o('loading_btm').html('已达末页，全部加载结束');
						}else{
							loading_btm.remove();
							o(mx).append(e+'<div id="loading_btm"></div>');
						}
					});
				}
			}
		}
	}
	</script>
	<?php
}
?>
<i class='ico goback' id='ZEAIGOBACK-<?php echo $curpage;?>'>&#xe602;</i>
<div id="subscribe_box_my_money_tx" class="my-subscribe_box"><img src="<?php echo $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm']; ?>">
<h3>
<?php if (!is_weixin()){?>请用微信打开使用提现功能<br><?php }?>
长按或微信扫码关注公众号<br>
<?php if (is_weixin()){?>关注成功才能打款给您的微信零钱<?php }?>
</h3>
</div>


<div class="tabmenu tabmenuMoney tabmenu_<?php echo ($switch['ifrmbtx'] == 1)?4:3;?>" id="tabmenu_my_money">
    <li<?php echo ($a == 'ye')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_ye" id="<?php echo $curpage;?>_yebtn"><span>余额</span></li>
    <li<?php echo ($a == 'mx')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_mx&totalP=<?php echo $totalP;?>&i=<?php echo $i;?>" id="<?php echo $curpage;?>_mxbtn"><span>明细</span></li>
    <li<?php echo ($a == 'cz')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_cz" id="<?php echo $curpage;?>_czbtn"><span>充值</span></li>
    <?php if($switch['ifrmbtx'] == 1){ ?>
	<li<?php echo ($a == 'tx')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_tx" id="<?php echo $curpage;?>_txbtn"><span>提现</span></li>
    <?php }?>
	<i></i>
</div>
<div class="submain2 <?php echo $curpage;?>" id="<?php echo $curpage;?>_submain"></div>
<?php
/*************Main End*************/?>
<script>
ZeaiM.tabmenu.init({obj:tabmenu_my_money,showbox:'<?php echo $curpage;?>_submain'});
setTimeout(function(){<?php echo $curpage;?>_<?php echo $a;?>btn.click();},400);
</script>

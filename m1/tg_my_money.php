<?php
require_once '../sub/init.php';
$currfields = 'money,grade,openid,subscribe';
require_once 'tg_chkuser.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_wxgzh.php';
$TG_set = json_decode($_REG['TG_set'],true);

$data_money    = $row['money'];
$data_grade    = $row['grade'];
$data_openid   = $row['openid'];
$data_subscribe= $row['subscribe'];

$cook_tg_uname = (empty($cook_tg_uname))?$cook_tg_mob:$cook_tg_uname;

$switch = json_decode($_ZEAI['switch'],true);

//调出组局部参数
$row_role=$db->ROW(__TBL_TG_ROLE__,"reward_kind,tx_min_price,tx_daymax_price,tx_sxf_bfb","grade=".$data_grade,"name");
$reward_kind  = $row_role['reward_kind'];
$tx_min_price    = $row_role['tx_min_price'];
$tx_daymax_price = $row_role['tx_daymax_price'];
$tx_sxf_bfb      = $row_role['tx_sxf_bfb'];


if($reward_kind=='loveb')json_exit(array('flag'=>0,'msg'=>'请返回到'.$_ZEAI['loveB'].'管理'));
//
$curpage = 'tg_my_money';



/*************Ajax Start*************/
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时没有内容</div>";
if ($submitok == 'ajax_ye'){ ?>
    <div class="ye fadeInL">
        <div class="box">
            <div class="dt"><i class="ico">&#xe635;</i></div>
            <div class="dd"><span class="money"><?php echo $data_money; ?><font>元</font></span></div>
        </div>
        <?php if($switch['ifrmbtx'] == 1){ ?>
        <div class="moneydetail">
            可以提现至您的微信钱包<br><br><br>
        </div>
        <?php }?>
    </div>    
<?php
exit;}elseif($submitok == 'ajax_mx'){
	$p = intval($p);if ($p<1)$p=1;$SQL="";$totalP = intval($totalP);
	if ($p > $totalP)exit($nodatatips);
	if ($p == 1)$SQL = " LIMIT ".$_ZEAI['pagesize'];
	if($i=='tx'){
		$TGsql = " AND kind=2 ";	
	}elseif($i=='sy'){
		$TGsql = " AND kind<>2 ";	
	}
	$rt = $db->query("SELECT id,content,num,addtime FROM ".__TBL_MONEY_LIST__." WHERE tg_uid=".$cook_tg_uid.$TGsql." ORDER BY id DESC ".$SQL);
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
		$num      = str_replace(".00","",$rows['num']);;
		$addtime  = YmdHis($rows['addtime']);
		if ($num<0){$numstyle = "fs";}else{$numstyle = "zs";$num = '+'.$num;}
		echo '<dl><dt>'.$content.'<font>'.$addtime.'</font></dt><dd class="'.$numstyle.'">'.$num.'</dd></dl>';
	}
	if ($p == 1)echo '</div>';

exit;}elseif($submitok == 'ajax_tx' && $switch['ifrmbtx'] == 1){?>
    <style>
    .tg_my_money .tx{padding:10px 0;margin-bottom:40px}
    .tg_my_money .tx h3{margin:25px 0 10px 0}
    .tg_my_money .tx ul{border-bottom:#eee 1px solid;cursor:pointer;padding:5px 15px;box-sizing:border-box}
    .tg_my_money .tx ul li{text-align:left;width:130px;padding:10px 0;margin:0 auto}
    .tg_my_money .tx ul:hover{background-color:#f8f8f8}
    .tg_my_money .tx h5{color:#999;padding:20px 0 5px 0}
    .tg_my_money .tx h6{color:#999;margin:10px auto 10px auto;text-align:left;width:85%;}
    </style>
	<div class="tx">
		<h3>请选择提现金额<br><span class="S14 C999">当前账户余额：<font class="Cf00">￥<?php echo str_replace(".00","",$data_money);; ?>元</font></span></h3>
        <form id="z_e_a_i__c_n__tx_form" method="post">
        <ul><li><input type="radio" name="tx_money" id="tx_money1" class="radioskin" value="<?php echo $tx_min_price;?>"><label for="tx_money1" class="radioskin-label"><i></i><b class="W80 S18"><?php echo $tx_min_price;?>元</b></label></li></ul>
        <ul><li><input type="radio" name="tx_money" id="tx_money2" class="radioskin" value="100" checked><label for="tx_money2" class="radioskin-label"><i></i><b class="W80 S18">100元</b></label></li></ul>
        <ul><li><input type="radio" name="tx_money" id="tx_money3" class="radioskin" value="200"><label for="tx_money3" class="radioskin-label"><i></i><b class="W80 S18">200元</b></label></li></ul>
        <ul><li><input type="radio" name="tx_money" id="tx_money4" class="radioskin" value="500"><label for="tx_money4" class="radioskin-label"><i></i><b class="W80 S18">500元</b></label></li></ul>
        <ul><li><input type="radio" name="tx_money" id="tx_money5" class="radioskin" value="1000"><label for="tx_money5" class="radioskin-label"><i></i><b class="W80 S18">1000元</b></label></li></ul>
        <input type="hidden" name="submitok" value="ajax_tx_update">
        </form>
       <?php if ($tx_sxf_bfb>0){ ?>
       <h5>提现将收取<?php echo $tx_sxf_bfb; ?>%的手续费</h5>
       <?php }?>
       <div style="text-align:center;margin:20px auto"><button type="button" class="btn size4 <?php if (!empty($_GZH['wx_gzh_ewm']) && ($data_subscribe!=1)){echo 'HUI';}else{echo 'HONG4';};?> W80_ yuan" id="txbtn"<?php if (!empty($_GZH['wx_gzh_ewm']) && ($data_subscribe!=1))echo ' disabled';?>>立即提现</button></div>
        <div class="linebox">
            <div class="line"></div>
            <div class="title BAI S14">温馨提醒</div>
        </div>
        <h6>为了补贴网站运营和服务器成本，我们将扣除部分手续费<?php echo $tx_sxf_bfb; ?>％，实际到账<?php echo (100-$tx_sxf_bfb); ?>％，有小数的将取整处理</h6>
		<script>
        txbtn.onclick = function(){
            zeai.confirm('您真的要提现么？',function (){zeai.ajax({'url':'tg_my_money'+zeai.extname,'js':1,'form':z_e_a_i__c_n__tx_form},function(e){rs=zeai.jsoneval(e);
				if (rs.flag==1){
					zeai.msg(rs.msg);
					tg_tx_allmoney.html(parseInt(tg_tx_allmoney.innerHTML)+parseInt(rs.tx_money));
					<?php echo $curpage;?>_yebtn.click();
				}else{
					zeai.msg(rs.msg);
				}
			});});
			
        }
		<?php
		if ($data_subscribe != 1){
			@wx_endurl_tg('您刚刚浏览的页面【'.$TG_set['navtitle'].'-余额提现】',HOST.'/m1/tg_my.php?e=tg_my_money');
			?>
			//setTimeout(function(){ZeaiM.div({obj:o('subscribe_box_my_money_tx'),w:260,h:280});},400);
			setTimeout(function(){
				zeai.ajax({url:HOST+'/m1/tg_my_money'+zeai.ajxext+'submitok=ajax_tg_gzh_ewm'},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1){
						tghn_gzh_ewm.src=rs.qrcode_url;
						tghn_gzh_ewm.onload=function(){ZeaiM.div({obj:o('subscribe_box_my_money_tx'),w:260,h:280});}
					}
				});
			},500);

		<?php }?>
        </script>
    </div>

<?php
exit;}elseif($submitok == 'ajax_tg_gzh_ewm'){
	
		$token = wx_get_access_token();
		if(str_len($token) < 50)json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_token'));
		$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
		$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"www_ZEAI_cn_tghn_'.$cook_tg_uid.'"}}}';
		$ticket      = Zeai_POST_stream($ticket_url,$ticket_data);
		$T           = json_decode($ticket,true);
		$qrcode_url  = 'http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
		$dbdir  = 'p/tmp/'.date('Y').'/'.date('m').'/';
		@mk_dir(ZEAI.'/up/'.$dbdir);
		$dbname = $dbdir.$cook_tg_uid.'_'.cdstrletters(3).'.jpg';
		$DST    = ZEAI.'/up/'.$dbname;
		$im=imagecreatefromjpeg($qrcode_url);
		imagejpeg($im,$DST,90);
		imagedestroy($im);
		json_exit(array('flag'=>1,'qrcode_url'=>$_ZEAI['up2'].'/'.$dbname));
	

exit;}elseif($submitok == 'ajax_tx_update' && $switch['ifrmbtx'] == 1){
	
//$tx_min_price    = $row_role['tx_min_price'];
//$tx_daymax_price = $row_role['tx_daymax_price'];
//$tx_sxf_bfb      = $row_role['tx_sxf_bfb'];
	if(!ifint($tx_money,"0-9","1,9"))json_exit(array('flag'=>0,'msg'=>'提现的金额必须是正整数'));
	if($tx_money<$tx_min_price)json_exit(array('flag'=>0,'msg'=>'提现金额必须大于'.$tx_min_price.'元'));
	$tx_money = abs(floatval($tx_money));
	if($tx_money > $data_money)json_exit(array('flag'=>0,'msg'=>'账户余额不足'.$tx_money));
	if($tx_daymax_price>0 && $tx_money>$tx_daymax_price)json_exit(array('flag'=>0,'msg'=>'每天最多提现'.$tx_daymax_price));
	

	$rt=$db->query("SELECT SUM(money) AS txallmoney FROM ".__TBL_PAY__." WHERE kind=-1 AND tg_uid=".$cook_tg_uid);
	$row=$db->fetch_array($rt,'name');
	$txallmoney=abs($row['txallmoney']);
	
	if($tx_daymax_price>0 && ($txallmoney >= $tx_daymax_price))json_exit(array('flag'=>0,'msg'=>'每天最多提现'.$tx_daymax_price));
	
	$endnum  = $data_money-$tx_money;
	$orderid = "TXTG_".$cook_tg_uid."_".$tx_money."_".date("Ymdhis");
	$db->query("UPDATE ".__TBL_TG_USER__." SET money=".$endnum." WHERE money>=".$tx_money." AND id=".$cook_tg_uid);
	//写清单
	$title   = "推广申请提现(手机)";
	$content = "推广申请提现<span class=\"Cf60 S12\">（等待处理）</span>";
	$money_list_id = $db->AddLovebRmbList($cook_tg_uid,$content,-$tx_money,'money',2,'tg');
	
	$paymoney = floatval((1-$tx_sxf_bfb/100)*$tx_money);
	//生成提现记录
	$db->query("INSERT INTO ".__TBL_PAY__." (tg_uid,orderid,money_list_id,kind,title,money,paymoney,addtime) VALUES ($cook_tg_uid,'$orderid',$money_list_id,-1,'$title','$tx_money','$paymoney',".ADDTIME.")");
	
	//站内消息
	$C = $cook_tg_uname.'您好，您的推广余额账户资金有变动！';
	$db->SendTip($cook_tg_uid,'推广余额提现',dataIO($C,'in'),'tg');
	
	//账户资金变动提醒
	if (!empty($data_openid) && $data_subscribe==1){
		$first  = urlencode($cook_tg_uname."您好，您的推广余额账户资金有变动：");
		$remark = urlencode("申请推广提现扣除");
		@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num=-'.$tx_money.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url=');
	}
	//
	json_exit(array('flag'=>1,'msg'=>'提现申请已提交，请等待处理','tx_money'=>$tx_money));
exit;}
/*************Ajax End*************/



/*************Main start*************/
?>
<style>
.tabmenuMoney{background:#F7564D}
.top_miniMoney{background:#F7564D}
.submain2{background-color:#fff;-webkit-overflow-scrolling:touch}
/*my_money*/
.tg_my_money .ye{padding:1px}
.tg_my_money .ye .box{width:80%;margin:50px auto 30px auto}
.tg_my_money .ye .box .dt{width:40%;float:left;border-right:#eee 1px solid}
.tg_my_money .ye .box .dt i{color:#EE5A4E;font-size:70px;margin:0 0 -5px 0}
.tg_my_money .ye .box .dd{width:50%;float:right;text-align:left}
.tg_my_money .ye .money{color:#EE5A4E;font:normal 36px/36px Arial;display:block}
.tg_my_money .ye .money font{color:#8d8d8d;font-size:20px;padding-left:3px}
.tg_my_money .ye .money:before{content:'当前余额';font-size:15px;display:block;color:#999;font-family:'Microsoft YaHei','SimSun','宋体'}
.tg_my_money .ye .moneydetail{margin:0 auto;color:#8d8d8d;line-height:200%;text-align:left;display:inline-block}
.tg_my_money .ye .btnA{border-radius:20px;height:40px;line-height:40px;color:#fff;width:100%;font-size:16px;cursor:pointer;border:0;margin:0 auto;display:block;text-align:center;background-color:#EE5A4E;margin-bottom:30px}
.tg_my_money .mx{padding:10px 0}
.tg_my_money .mx dl{width:100%;padding:5px 15px;box-sizing:border-box;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;clear:both;overflow:auto;border-bottom:#f8f8f8 1px solid}
.tg_my_money .mx dl:last-child{border-bottom:0}
.tg_my_money .mx dl dt,.tg_my_money .mx dl dd{float:left;display:block}
.tg_my_money .mx dl:hover{background-color:#f8f8f8}
.tg_my_money .mx dl dt{width:70%;font-size:14px;text-align:left;line-height:150%}
.tg_my_money .mx dl dt font{color:#999;font-size:12px;display:block}
.tg_my_money .mx dl dd{width:28%;text-align:right;font-size:16px;line-height:39px}
.tg_my_money .mx dl dd.zs{color:#EE5A4E}
.tg_my_money .mx dl dd.fs{color:#097AFE}
</style>
<?php
$mini_backT = '';$mini_title = '余额账户';$mini_class='top_mini top_miniMoney';
require_once ZEAI.'m1/top_mini.php';

$totalnum = $db->COUNT(__TBL_MONEY_LIST__,"tg_uid=".$cook_tg_uid);
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
<div id="subscribe_box_my_money_tx" class="my-subscribe_box"><font class="S16 B">微信扫码关注公众号</font><img id="tghn_gzh_ewm" src="<?php echo HOST.'/res/loadingData.gif'; ?>">
<h3>
<?php if (!is_weixin()){?>请用微信打开使用提现功能<br><?php }?>
<?php if (is_weixin()){?>关注成功才能打款给您的微信零钱<?php }?>
</h3>
</div>


<div class="tabmenu tabmenuMoney tabmenu_<?php echo ($switch['ifrmbtx'] == 1)?3:2;?>" id="tabmenu_my_money">
    <li<?php echo ($a == 'ye')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_ye" id="<?php echo $curpage;?>_yebtn"><span>账户余额</span></li>
    <li<?php echo ($a == 'mx')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_mx&totalP=<?php echo $totalP;?>&i=<?php echo $i;?>" id="<?php echo $curpage;?>_mxbtn"><span>账户明细</span></li>
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

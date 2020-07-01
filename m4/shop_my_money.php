<?php
require_once '../sub/init.php';
$currfieldstg="money,openid,subscribe";
require_once ZEAI.'m4/shop_chk_u.php';
$cook_tg_money = floatval($rowtg['money']);
$switch = json_decode($_ZEAI['switch'],true);
$t = (ifint($t,'1-2','1'))?$t:1;
//
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
function rows_ulist($rows,$p) {
	global $_ZEAI;
	$id       = $rows['id'];
	$content  = dataIO($rows['content'],'out');
	$num      = str_replace(".00","",$rows['num']);;
	$addtime  = YmdHis($rows['addtime']);
	if ($num<0){$numstyle = "fs";}else{$numstyle = "zs";$num = '+'.$num;}
	$O = '<dl><dt>'.$content.'<font>'.$addtime.'</font></dt><dd class="'.$numstyle.'">'.$num.'</dd></dl>';
	return $O;
}
$_ZEAI['pagesize'] = 8;
if($t != 2){
	$ZEAI_SQL = "tg_uid=".$cook_tg_uid;
	if($nciaezwww=='tx'){
		$ZEAI_SQL .= " AND kind=2 ";	
	}elseif($nciaezwww=='sy'){
		$ZEAI_SQL .= " AND kind<>2 ";	
	}
	$ZEAI_SELECT="SELECT id,content,num,addtime FROM ".__TBL_MONEY_LIST__." WHERE  ".$ZEAI_SQL." ORDER BY id DESC";
	if($submitok=='ZEAI_list'){exit(Zeai_ajax_list_fn($ZEAI_totalP,$p));}
	$ZEAI_total = $db->COUNT(__TBL_MONEY_LIST__,$ZEAI_SQL);
	$ZEAI_totalP= ceil($ZEAI_total/$_ZEAI['pagesize']);
}else{
	$rowrole = $db->ROW(__TBL_TG_ROLE__,"tx_daymax_price,tx_sxf_bfb","grade=0 AND shopgrade=".$cook_tg_shopgrade,"num");
	if ($rowrole){
		$tx_daymax_price = urlencode(dataIO($rowrole[0],'out'));
		$tx_sxf_bfb      = abs(floatval($rowrole[1]));
	}
}
if($submitok == 'ajax_tx_update' && $switch['ifrmbtx'] == 1){
	if(!ifint($tx_money,"0-9","1,9"))json_exit(array('flag'=>0,'msg'=>'提现的金额必须是正整数'));
	$tx_money = abs(floatval($tx_money));
	if($tx_money > $cook_tg_money)json_exit(array('flag'=>0,'msg'=>'账户余额不足'.$tx_money.'元'));
	if($tx_daymax_price>0 && $tx_money>$tx_daymax_price)json_exit(array('flag'=>0,'msg'=>'每天最多提现'.$tx_daymax_price));
	$rt=$db->query("SELECT SUM(money) AS txallmoney FROM ".__TBL_PAY__." WHERE kind=-1 AND tg_uid=".$cook_tg_uid);
	$row=$db->fetch_array($rt,'name');
	$txallmoney=abs($row['txallmoney']);
	if($tx_daymax_price>0 && ($txallmoney >= $tx_daymax_price))json_exit(array('flag'=>0,'msg'=>'每天最多提现'.$tx_daymax_price));
	$endnum  = $cook_tg_money-$tx_money;
	$db->query("UPDATE ".__TBL_TG_USER__." SET money=".$endnum." WHERE money>=".$tx_money." AND id=".$cook_tg_uid);
	//写清单
	$title   = "商家申请提现(手机)";
	$content = "商家申请提现<span class=\"Cf60 S12\">（等待处理）</span>";
	$money_list_id = $db->AddLovebRmbList($cook_tg_uid,$content,-$tx_money,'money',2,'tg');
	//生成提现记录
	$paymoney = floatval((1-$tx_sxf_bfb/100)*$tx_money);
	$orderid  = "TXSHOP_".$cook_tg_uid."_".$tx_money."_".date("Ymdhi");
	$db->query("INSERT INTO ".__TBL_PAY__." (tg_uid,orderid,money_list_id,kind,title,money,paymoney,addtime) VALUES ($cook_tg_uid,'$orderid',$money_list_id,-1,'$title','$tx_money','$paymoney',".ADDTIME.")");
	
	//站内消息
	$C = $cook_tg_uname.'您好，您的余额账户资金有变动！';
	$db->SendTip($cook_tg_uid,'商家余额提现',dataIO($C,'in'),'shop');
	
	//账户资金变动提醒
	if (!empty($rowtg['openid']) && $rowtg['subscribe']==1){
		$first  = urlencode($cook_tg_uname."您好，您的商家余额账户资金有变动：");
		$remark = urlencode("申请提现扣除");
		@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$rowtg['openid'].'&num=-'.$tx_money.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url=');
	}
	//
	json_exit(array('flag'=>1,'msg'=>'提现申请已提交，请等待处理','tx_money'=>$tx_money));
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php 
$url=HOST.'/m4/shop_my.php';
$mini_title = '<i class="ico goback" onClick="zeai.openurl(\''.$url.'\');">&#xe602;</i>我的账户';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<div class="shop_money_ye">
    <div class="box">
        <div class="dt"><i class="ico">&#xe635;</i></div>
        <div class="dd"><span class="money"><?php echo str_replace(".00","",number_format($cook_tg_money,2)); ?><font>元</font></span></div>
    </div>
    <?php if($switch['ifrmbtx'] == 1){ ?><div class="moneydetail">可以提现至您的微信钱包</div><?php }?>
</div>
<div class="tab flex shop_money_tab">
    <a href="<?php echo SELF;?>?t=1&nciaezwww=<?php echo $nciaezwww;?>"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>><?php echo $p_str;?>余额明细</a>
    <a href="<?php echo SELF;?>?t=2&nciaezwww=<?php echo $nciaezwww;?>"<?php echo ($t==2)?' class="ed"':'';?>><?php echo $kind_str;?>余额提现</a>
</div>
<?php if ($t == 1){?>
    <div class="shop_money_mx" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div>
    <script>
    <?php
    if ($ZEAI_total > $_ZEAI['pagesize'] && $t != 2){?>
    var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2,nciaezwww='<?php echo $nciaezwww;?>';
    zeaiOnscroll_json={url:'shop_my_money'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,nciaezwww:nciaezwww}};
    document.body.onscroll = zeaiOnscroll;
    <?php }?>
    </script>
<?php }elseif($t==2){
	if (!$rowrole){echo '<div class="nodatatips"><i class="ico">&#xe61f;</i>不好意思，找不到相关套餐</div>';exit;}?>
	<div class="shop_money_tx">
        <form id="www_z_e_a_i__c_n__tx_form" method="post">
		<?php 
		$ARR=explode(',', $_SHOP['tx_num_list']);
		if (count($ARR) >= 1 && is_array($ARR) && !empty($_SHOP['tx_num_list'])){
			$n=1;
			foreach ($ARR as $V){
				$ed=($n==1)?' checked':'';
				?>
                <ul><li><input type="radio" name="tx_money" id="tx_money<?php echo $V;?>" class="radioskin" value="<?php echo $V;?>"<?php echo $ed;?>><label for="tx_money<?php echo $V;?>" class="radioskin-label"><i></i><b class="W80 S18"><?php echo $V;?>元</b></label></li></ul>
				<?php
				$n++;
			}
		}else{
			echo '<div class="nodatatips"><i class="ico">&#xe61f;</i>提现金额列表未设置，请稍等</div>';exit;
		}
        ?>
        <input type="hidden" name="t" value="2">
        <input type="hidden" name="submitok" value="ajax_tx_update">
        </form>
       <?php if ($tx_sxf_bfb>0){$tx_sxf_bfb_str='提现将收取'.$tx_sxf_bfb.'%的手续费';?>
		<h6>为了补贴网站运营和服务器成本，我们将扣除部分手续费<?php echo $tx_sxf_bfb; ?>％，实际到账<?php echo (100-$tx_sxf_bfb); ?>％，有小数的将取整处理</h6>
        <?php }?>
        <button type="button" class="btn size4 HONG3 yuan" id="txbtn">立即提现</button>
    </div>
    <script>
	txbtn.onclick = function(){
		zeai.confirm('确定提现么？',function (){zeai.ajax({'url':'shop_my_money'+zeai.extname,js:1,form:www_z_e_a_i__c_n__tx_form},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if (rs.flag==1)setTimeout(function(){location.reload(true);},1000);
		});});
	}
    </script>
<?php }?>
<div id="backtop"><a href="#top" id="btmTopBtn"><i class="ico">&#xe60a;</i>顶部</a></div>
</body>
</html>

<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
if(ifint($oid)){
	$row = $db->ROW(__TBL_SHOP_ORDER__,"pid,price,flag,orderid,tg_uid,orderprice,orderkind","id=".$oid,"num");
	if ($row){
		$pid   = $row[0];
		$price = $row[1];
		$flag  = $row[2];//$flag=1;
		$orderid  = $row[3];
		$cid   = $row[4];
		$orderprice = $row[5];//$orderprice = str_replace(".00","",$orderprice);
		$orderkind  = $row[6];
		$ifpay=($flag == 0 && $_SHOP['orderkind']==2 && $orderprice>0)?true:false;
		$ifpay_str=($ifpay)?'请及时付款':'请等待店家处理';
		$flag_str = ($flag==0)?'下单成功　'.$ifpay_str:'恭喜您购买成功';
		$flag_str=($orderkind == 1)?'恭喜您预约成功':$flag_str;
	}else{
		alert('亲，参数好像跑路了，请重试','back');
	}
	$row = $db->ROW(__TBL_TG_PRODUCT__,"title","id=".$pid,"num");
	if ($row){
		$title_=$row[0];
		$title = dataIO($title_,'out');
	}
}else{
	alert('亲，参数好像跑路了，请重试','back');
}
if($submitok == 'ajax_pay'){
	if($flag!=0 || $price<=0)json_exit(array('flag'=>0,'msg'=>'当前订单状态无需支付'));
	$return_url= HOST.'/m4/shop_my_order.php?oid='.$oid.'&ifadm='.$ifadm;$jumpurl = $return_url;
	json_exit(array('flag'=>1,'money'=>$orderprice,'orderid'=>$orderid,'oid'=>$oid,'cid'=>$cid,'title'=>$title,'return_url'=>$return_url,'jumpurl'=>$jumpurl));
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
$mini_title = '<i class="ico goback" onClick="zeai.back();">&#xe602;</i>我的订单';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
?>
<div class="shop_goods_detail_pay">
	<i class="ico flag">&#xe60d;</i>
    <h4 class="title"><?php echo $title;?></h4>
    <h4 class="textsuccess"><?php echo $flag_str;?></h4>
    <div class="button">
    	<?php if ($ifpay){?>
        <a class="btn size5 BAI paybtn" id="paybtn"><i class="ico">&#xe6b7;</i> 立即付款</a>
        <?php }?>
        <a href="shop_my_order.php" class="btn size5 BAI">查看订单</a>
        <a href="shop_index.php" class="btn size5 BAI">继续逛逛</a>
	</div>
</div>
<script src="<?php echo HOST;?>/api/zeai_PAY.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php if ($ifpay){?>
<script>
paybtn.onclick=function(){
	zeai.ajax({url:'shop_goods_detail_pay'+zeai.ajxext+'submitok=ajax_pay',data:{oid:<?php echo $oid;?>}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			zeai_PAY({money:rs.money,paykind:'wxpay',kind:12,oid:rs.orderid,tmpid:rs.oid,tg_uid:rs.cid,title:decodeURIComponent(rs.title),return_url:rs.return_url,jumpurl:rs.jumpurl});
		}else{zeai.msg(rs.msg);}
	});
}
</script>
<?php }?>
</body>
</html>

<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
require_once ZEAI.'sub/TGfun_shop.php';
if ($submitok=='mod'){
	$hdcode=trimhtml($hdcode);
	if(empty($hdcode))json_exit(array('flag'=>0,'msg'=>'请输入买家提供的【核销码】'));
	$row = $db->ROW(__TBL_SHOP_ORDER__,"id","hdcode='".$hdcode."' AND cid=".$cook_tg_uid,"name");/*flag=2 AND */
	if ($row)json_exit(array('flag'=>1,'url'=>HOST.'/m4/shop_my_hdcode_adm.php?hdcode='.$hdcode));
	json_exit(array('flag'=>0,'msg'=>'【核销码】不存在或已核销，请重输入'));
}elseif($submitok=='modok'){
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		$form_hdcode=trimhtml($form_hdcode);
		$cid=intval($cook_tg_uid);
		$row = $db->ROW(__TBL_SHOP_ORDER__,"pid,hdcode,num,orderprice,tg_uid,tgbfb1,tgbfb2","flag=2 AND cid=".$cid." AND id=".$id." AND hdcode='".$form_hdcode."'","num");
		if ($row){
			$pid=$row[0];$hdcode=$row[1];$num=$row[2];$orderprice=$row[3];$tg_uid=intval($row[4]);$tgbfb1= $row[5];$tgbfb2= $row[6];
			//商品信息
			$row = $db->ROW(__TBL_TG_PRODUCT__,"title","fahuokind=2 AND id=".$pid,"num");
			if($row){
				$ptitle = trimhtml(dataIO($row[0],'out'));
				if(empty($hdcode)){
					json_exit(array('flag'=>0,'msg'=>'买家核销码还未生成，请向买家索要'));
				}else{
					if($form_hdcode!=$hdcode)json_exit(array('flag'=>0,'msg'=>'核销码验证错误，请检查'));
					$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET flag=3,endtime=".ADDTIME." WHERE cid=".$cid." AND id=".$id);
					//卖家信息
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe,title","id=".$cid,"num");
					$c_openid    = $row[0];
					$c_subscribe = $row[1];
					$c_name      = dataIO($row[2],'out');
					//买家信息
					$row = $db->ROW(__TBL_TG_USER__,"openid,subscribe,nickname","id=".$tg_uid,"num");
					$tg_openid    = $row[0];
					$tg_subscribe = $row[1];
					$tg_nickname  = dataIO($row[2],'out');
					/*****通卖****/
					//入账
					if($orderprice>0){
						$tgbfb1_money=0;$tgbfb2_money=0;
						if($tgbfb1>0)$tgbfb1_money=round($orderprice*($tgbfb1/100),2);
						if($tgbfb2>0)$tgbfb2_money=round($orderprice*($tgbfb2/100),2);
						$orderprice=$orderprice-($tgbfb1_money+$tgbfb2_money);
						if($orderprice>0){
							$db->query("UPDATE ".__TBL_TG_USER__." SET money=money+$orderprice WHERE id=".$cid);
							$db->AddLovebRmbList($cid,'出售商品【'.$ptitle.'】买家（'.$tg_nickname.'，ID:'.$tg_uid.'）',$orderprice,'money',16,'tg');
							$tmstr='　账户余额到账￥'.$orderprice;
							TG_shop($id);
						}
					}
					//站内
					$C = '商品【'.$ptitle.'】->【核销】成功'.$tmstr.'，买家（'.$tg_nickname.'，ID:'.$tg_uid.'）';$db->SendTip($cid,'【'.$ptitle.'】【核销】成功',dataIO($C,'in'),'shop');
					//微信
					if (!empty($c_openid) && $c_subscribe==1){
						$keyword1 = urlencode('商品【'.$ptitle.'】->【核销】成功！买家（'.$tg_nickname.'，ID:'.$tg_uid.'）'.$tmstr);
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php?f=3&ifadm=1');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$c_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					/*****通买****/
					//站内
					$C = '商品【'.$ptitle.'】->【核销】成功，卖家（'.$c_name.'）';$db->SendTip($tg_uid,'【'.$ptitle.'】【核销】成功',dataIO($C,'in'),'shop');
					//微信
					if (!empty($tg_openid) && $tg_subscribe==1){
						$keyword1 = urlencode('商品【'.$ptitle.'】->【核销】成功！卖家（'.$c_name.'）');
						$keyword3 = urlencode($_ZEAI['siteName']);
						$url      = urlencode(HOST.'/m4/shop_my_order.php?f=3');
						@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$tg_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					json_exit(array('flag'=>1,'msg'=>'核销成功'));
				}
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'【核销码】不存在或已核销'));
		}
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
$mini_title = '<i class="ico goback" onClick="zeai.openurl(\''.HOST.'/m4/shop_my.php'.'\');">&#xe602;</i>订单核销';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<style>
.shop_hdcode_adm {margin-top:70px;padding-bottom:30px}
.shop_hdcode_adm .input{text-align:center;border-radius:2px 0 0 2px;line-height:40px;height:40px;font-size:18px;border-right:0;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.shop_hdcode_adm button{line-height:40px;height:40px;border-radius:0 2px 2px 0}

.shop_hdcode{width:100%;margin:0 auto;border-top:#f5f5f5 12px solid;padding:20px 8%;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.shop_hdcode li{font-size:16px;color:#999;line-height:44px;height:44px;text-align:left;border-bottom:#eee 1px solid}
.shop_hdcode li b{color:#333;font-weight:normal}
.shop_hdcode button{display:block;width:100%;margin:20px auto}

.shop_hdcode .hdcoded i.ico{font-size:60px;color:#07C160;display:block;margin:20px auto 10px auto;line-height:60px}
.shop_hdcode .hdcoded center{color:#000;font-size:18px;font-weight:bold;margin-bottom:20px}

</style>
<form id="ZEAI_CN_FORM" class="shop_hdcode_adm">
	<input type="text" name="hdcode" id="hdcode" value="<?php echo $hdcode;?>" class="input" maxlength="8" placeholder="输入买家提供的核销码" pattern="[0-9]*" autocomplete="off"><button type="button" class="btn size3 HONG" id="hdbtn">确定</button>
    <input type="hidden" name="submitok" value="mod">
</form>
<?php if (!empty($hdcode)){
	$hdcode=trimhtml($hdcode);
	$row = $db->ROW(__TBL_SHOP_ORDER__,"id,pid,num,orderprice,orderid,flag","hdcode='".$hdcode."' AND cid=".$cook_tg_uid,"name");
	if ($row){
		$oid=$row['id'];$pid=$row['pid'];$num=$row['num'];$orderprice=$row['orderprice'];$orderid=$row['orderid'];$flag=$row['flag'];
		$row = $db->ROW(__TBL_TG_PRODUCT__,"title","id=".$pid,"num");
		if($row)$ptitle = trimhtml(dataIO($row[0],'out'));
		$flag_str=($flag==3)?'已核销':'未核销';
	}else{
		alert('【核销码】不正确，请重输入','back');
	}
	?>
    <div class="shop_hdcode">
    	<?php if ($flag==2){?>
            <li>状态：<b><?php echo $flag_str;?></b></li>
            <li>单号：<b><?php echo $orderid;?></b></li>
            <li>商品：<b><?php echo $ptitle;?></b></li>
            <li>数量：<b><?php echo $num;?></b></li>
            <li>总价：<b>￥<?php echo $orderprice;?></b></li>
            <form id="ZEAI_CN_FORMok">
            <input type="hidden" name="id" value="<?php echo $oid;?>">
            <input type="hidden" name="form_hdcode" value="<?php echo $hdcode;?>">
            <input type="hidden" name="submitok" value="modok">
            <button type="button" class="btn size4 HONG B" id="hdbtnok">开始核销</button>
            </form>
			<script>
            hdbtnok.onclick=function(){
                zeai.ajax({url:'shop_my_hdcode_adm'+zeai.extname,form:ZEAI_CN_FORMok},function(e){var rs=zeai.jsoneval(e);
                    zeai.msg(0);zeai.msg(rs.msg);
                    if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
                });
            }
            </script>
        <?php }else{ ?>
        	<div class="hdcoded">
                <i class="ico">&#xe60d;</i>
                <center><?php echo $flag_str;?></center>
                <li>单号：<b><?php echo $orderid;?></b></li>
                <li>商品：<b><?php echo $ptitle;?></b></li>
                <li>数量：<b><?php echo $num;?></b></li>
                <li>总价：<b>￥<?php echo $orderprice;?></b></li>
            </div>
        <?php }?>
    </div>
<?php }?>
<script>
hdbtn.onclick=function(){
	if(zeai.empty(hdcode.value)){zeai.msg('请输入买家提供的【核销码】');return false;}
	zeai.ajax({url:'shop_my_hdcode_adm'+zeai.extname,form:ZEAI_CN_FORM},function(e){var rs=zeai.jsoneval(e);
		if(rs.flag==1){zeai.openurl(rs.url);}else{hdcode.value='';zeai.msg(0);zeai.msg(rs.msg);}
	});
}
</script>
</body>
</html>
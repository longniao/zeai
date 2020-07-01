<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$currfieldstg = "sjtime,sjtime2,shopgradetitle,photo_s,content,piclist,shopkind,weixin_ewm";
//openid
if( is_weixin() && is_mobile() && empty($cook_tg_openid)){
	$cook_tg_openid = wx_get_openid();
	setcookie("cook_tg_openid",$cook_tg_openid,time()+720000,"/",$_ZEAI['CookDomain']);
}
//chk
$rowtg = shop_chk();
if($rowtg){
	$cook_tg_uid    = $rowtg['id'];
	$cook_tg_shopgrade= $rowtg['shopgrade'];
	$cook_tg_shopflag = $rowtg['shopflag'];
	$cook_tg_sjtime  = $rowtg['sjtime'];
	$cook_tg_sjtime2 = $rowtg['sjtime2'];
	$cook_tg_shopgradetitle = dataIO($rowtg['shopgradetitle'],'out');
	$cook_tg_photo_s = $rowtg['photo_s'];
	if($cook_tg_shopflag==0 || $cook_tg_shopflag==-1)header("Location: shop_my_flag.php");
	//
	$cook_tg_shopkind = $rowtg['shopkind'];
	$cook_tg_content  = $rowtg['content'];
	$cook_tg_piclist  = $rowtg['piclist'];
	$cook_tg_weixin_ewm = $rowtg['weixin_ewm'];
	if(empty($cook_tg_shopkind) || empty($cook_tg_content) || empty($cook_tg_piclist) || empty($cook_tg_weixin_ewm))header("Location: shop_my_apply.php");
}else{header("Location: shop_my_apply.php");}
//chk end
if($submitok=='ajax_pay'){
	if(ifint($grade) && $grade<10){
		$row = $db->ROW(__TBL_TG_ROLE__,"title,price","grade=0 AND shopgrade=".$grade,"num");
		if ($row){
			$title = urlencode(dataIO($row[0],'out'));
			$price = abs(floatval($row[1]));
			$return_url= HOST.'/m4/shop_my.php';
			if($price==0)freeshopgrade($grade);//免费直升
			$jumpurl   = $return_url;
			$orderid = 'SHOP-'.$cook_tg_uid.'-'.date("YmdHis");
			json_exit(array('flag'=>1,'cid'=>$cook_tg_uid,'orderid'=>$orderid,'title'=>$title,'money'=>$price,'return_url'=>$return_url,'jumpurl'=>$jumpurl,'msg'=>'正在调起支付...'));
		}else{
			json_exit(array('flag'=>0,'msg'=>'不好意思，找不到相关套餐'));
		}
	}else{
		json_exit(array('flag'=>0,'msg'=>'不好意思，参数好像跑路了'));
	}
}elseif($submitok=='ajax_free'){
	if($_SHOP['regifpay']==1)json_exit(array('flag'=>0,'msg'=>'不好意思，必须要交费'));
	$return_url= HOST.'/m4/shop_my.php';
	freeshopgrade(0);
}
function freeshopgrade($grade) {
	global $db,$_ZEAI,$cook_tg_uid,$return_url,$_SHOP;
	if(empty($grade)){
		$row   = $db->ROW(__TBL_TG_ROLE__,"shopgrade","grade=0 AND ifdefault=1","num");
		$grade = $row[0];
	}
	$row = $db->ROW(__TBL_TG_ROLE__,"yxq,title","grade=0 AND shopgrade=".$grade,"name");
	if($row){
		$R_yxq    = intval($row['yxq']);
		$R_sjtime2=ADDTIME+$R_yxq*86400;
		$R_title  = dataIO($row['title'],'out');
		//
		$rowc = $db->ROW(__TBL_TG_USER__,"shopgrade,sjtime,sjtime2,title,openid,subscribe,shopflag","id=".$cook_tg_uid,"name");
		if ($rowc){
			$shopgrade =$rowc['shopgrade'];
			$sjtime    =$rowc['sjtime'];
			$sjtime2   =$rowc['sjtime2'];
			$ctitle    =dataIO($rowc['title'],'out');
			$openid    =$rowc['openid'];
			$subscribe =$rowc['subscribe'];
			$shopflag  =$rowc['shopflag'];
			$shopflag = ($shopflag == 2)?1:$shopflag;
			$shopflag = ($_SHOP['regflag'] == 1)?1:0;
			$SQL  =",shopgrade=$grade,shopgradetitle='$R_title',shopflag=$shopflag";
			if($grade>$shopgrade){
				$db->query("UPDATE ".__TBL_TG_USER__." SET sjtime=".ADDTIME.",sjtime2=$R_sjtime2".$SQL." WHERE id=".$cook_tg_uid);
			}elseif($grade==$shopgrade){
				$sjtime    = ($sjtime==0)?ADDTIME:$sjtime;
				$sjtime2   = ($sjtime2==0)?ADDTIME:$sjtime2;
				$R_sjtime2 = $sjtime2+$R_yxq*86400;
				$db->query("UPDATE ".__TBL_TG_USER__." SET sjtime=$sjtime,sjtime2=$R_sjtime2".$SQL." WHERE id=".$cook_tg_uid);
			}
			//站内通知
			$C = '尊敬的【'.$ctitle.'】'.$R_title.'开通成功　　<a href='.HOST.'/m4/shop_my_tip.php class=aQING>查看详情</a>';
			$db->SendTip($cook_tg_uid,'尊敬的【'.$ctitle.'】'.$R_title.'开通成功!',dataIO($C,'in'),'shop');
			//微信通知
			if (!empty($openid) && $subscribe==1){
				$keyword1 = urlencode('尊敬的【'.$ctitle.'】，'.$R_title.'开通成功!');
				$keyword3 = urlencode($_ZEAI['siteName']);
				$url      = urlencode(HOST.'/m4/shop_my_tip.php');
				$remark   = urlencode($R_title.' / '.yxq($R_yxq));
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
		}
	}
	json_exit(array('flag'=>'success','url'=>$return_url,'msg'=>$R_title.'开通成功'));
}
if($submitok=='ajax_getvipauth'){
	if(!ifint($grade) || $grade>10 || $grade<1)json_exit(array('flag'=>0,'msg'=>'请选择服务套餐'));
	$C = '';
	$row = $db->ROW(__TBL_TG_ROLE__,"productmaxnum,tx_daymax_price,tx_sxf_bfb,content","grade=0 AND shopgrade=".$grade);
	if ($row){
		$productmaxnum   = $row[0];
		$tx_daymax_price = $row[1];
		$tx_sxf_bfb      = $row[2];
		$content         = dataIO($row[3],'out');
	}
	$C.= '<dl><dt><i class=ico style=\'font-size:36px\'>&#xe6ab;</i></dt><dd><h6>专属尊贵标识</h6></dd></dl>';
	$C.= '<dl><dt><i class=ico>&#xe647;</i></dt><dd><h6>商品发布总数<b>'.$productmaxnum.'</b></h6></dd></dl>';
	$C.= '<dl><dt><i class=ico>&#xe639;</i></dt><dd><h6>日提现额度<b>￥'.$tx_daymax_price.'</b></h6></dd></dl>';
	$C.= '<dl><dt><i class=ico>&#xe63a;</i></dt><dd><h6>提现手续费<b>'.$tx_sxf_bfb.'％</b></h6></dd></dl>';
	json_exit(array('flag'=>1,'C'=>$C,'vipC'=>$content));
}
$urole = json_decode($_SHOP['shopgradearr'],true);
$nav = 'shop_my';
$photo_s_url = (!empty($cook_tg_photo_s))?$_ZEAI['up2'].'/'.$cook_tg_photo_s:HOST.'/res/photo_m.jpg';
$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop_vip.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php 
$url=HOST.'/m4/shop_index.php';
$mini_title = '<i class="ico goback" onClick="zeai.back(\''.$url.'\');">&#xe602;</i>服务套餐';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
if($_SHOP['regifpay']==0){
	echo '<div class="shop_vip">';
	if($cook_tg_shopflag==1){?>
        <div class="shop_my_vip_self" style="border:0;padding-bottom:0">
            <div class="logobox"><img src="<?php echo $photo_s_url;?>" class="logo"></div>
            <h3>您当前级别【<?php echo shopgrade($cook_tg_shopgrade,'img',1);?><?php echo $cook_tg_shopgradetitle;?>】</h3>
            <h5><?php echo YmdHis($cook_tg_sjtime,'Ymd');?> ~ <?php echo YmdHis($cook_tg_sjtime2,'Ymd');?></h5>
        </div>
    <?php }else{
			$row  = $db->ROW(__TBL_TG_ROLE__,"shopgrade,title","grade=0 AND ifdefault=1","num");
			$cook_tg_shopgrade = $row[0];$cook_tg_shopgradetitle = $row[1];
			echo shopgrade($cook_tg_shopgrade,'img',4);?><br>
        您还没有激活开通<?php echo $_SHOP['title'];?>服务<br>请点击下方按钮免费开通
		<button type="button" class="btn size4 HONG B yuan" id="vipbtnfree" style="width:80%">免费开通 <?php echo $cook_tg_shopgradetitle;?>服务</button><br>
    <?php }?>
        <div class="viplist">
            <h1><font id="gradename1"><?php echo shopgrade($cook_tg_shopgrade,'t');?>特权</font></h1>
            <div class="CC auth">
                <ul id="vipauth"></ul>
                <div class="clear"></div>
            </div>
        </div>
        <div class="viplist">
            <h1><font id="gradename2"><?php echo shopgrade($cook_tg_shopgrade,'t');?>套餐详情</font></h1>
            <div class="CC" id="vipC"></div>
        </div>
        <div class="shop_btm_kefu">
            <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><br><?php }?>
            <?php if (!empty($kf_tel)){?>
                <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
            <?php }else{?>
                <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
            <?php }?>
        </div>
        <script src="<?php echo HOST;?>/res/m4/js/shop_vip.js?<?php echo $_ZEAI['cache_str'];?>"></script>
		<script>setTimeout(function(){getvipauth(<?php echo $cook_tg_shopgrade;?>);},200);</script>
<?php echo '</div>';exit;}?>



<div class="shop_vip">
    <?php
	if($cook_tg_shopflag==1){?>
        <div class="shop_my_vip_self">
        	<div class="logobox"><img src="<?php echo $photo_s_url;?>" class="logo"></div>
        	<h3>您当前级别【<?php echo shopgrade($cook_tg_shopgrade,'img',1);?><?php echo $cook_tg_shopgradetitle;?>】</h3>
            <h5><?php echo YmdHis($cook_tg_sjtime,'Ymd');?> ~ <?php echo YmdHis($cook_tg_sjtime2,'Ymd');?></h5>
        </div><?php	
	}
	$maxg=0;
    $rt=$db->query("SELECT * FROM ".__TBL_TG_ROLE__." WHERE grade=0 AND shopgrade>=1 AND flag=1 ORDER BY px DESC,id DESC");
    $total = $db->num_rows($rt);
    if ($total > 0){
        for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'name');
            if(!$rows) break;
            $id = $rows['id'];
            $title = trimhtml(dataIO($rows['title'],'out'));
            $title2 = trimhtml(dataIO($rows['title2'],'out'));
            $shopgrade = intval($rows['shopgrade']);
			if($cook_tg_sjtime>0 && $cook_tg_shopgrade>=$shopgrade )continue;
            $logo  = $rows['logo'];
            $ifdefault = $rows['ifdefault'];
            $price  = floatval($rows['price']);
            $price2  = floatval($rows['price2']);
            $flag   = $rows['flag'];
            $yxq=intval($rows['yxq']);
            $logo_url = (!empty($logo))?$_ZEAI['up2'].'/'.$logo:HOST.'/res/noP.gif';
            if($price<=0){
                $price_str = '免费';
            }else{
                $price_str = round($price/$yxq,2);
                if($price_str==0){
                    $price_str = '日均'.round(($price*100)/$yxq,2).'分';
                }else{
                    $price_str = '日均'.$price_str.'元';
                }
            }
            if($c_shopgrade > $shopgrade)continue;
			if($shopgrade>=$maxg){$maxg=$shopgrade;$maxmoney=$price;}
			?>
            <table class="table">
                <tr>
                <td width="50" align="left" valign="middle"><img class="gico" src="<?php echo $logo_url;?>"></td>
                <td align="left"><b><?php echo $title; ?></b><em class="C333"><div class="C999 S14"><?php echo $title2;?></div><?php echo yxq($yxq);?>　<?php echo $price_str;?></em></td>
                <td width="100" align="left" class="vippricebox <?php echo ($maxg == $shopgrade)?' on':'off';?>" id="vipprice<?php echo $shopgrade;?>"><?php echo '<font class="S12">￥</font>'.$price;?> <div class="yj">原价￥<?php echo $price2;?></div></td>
                <td width="40" align="left"><input onClick="vipFn(<?php echo $shopgrade;?>,encodeURIComponent('<?php echo $title; ?>'))" type="checkbox" id="vip<?php echo $shopgrade;?>" name="vip<?php echo $shopgrade;?>" class="checkskin vipli"<?php echo ($maxg == $shopgrade)?' checked':'';?>><label for="vip<?php echo $shopgrade;?>" class="checkskin-label"><i class="i3"></i></label> </td>
                </tr>
            </table><?php
		}
		if($maxg>0){
			$maxg2=$maxg;
			?>
            <button type="button" class="btn size4 HONG W100_ B yuan" id="vipbtn">立即开通 <?php echo shopgrade($maxg,'t');?>服务</button>
        <?php }else{
			$maxg2=$maxg;
			$maxg=$cook_tg_shopgrade;
		}
		?>
        <div class="viplist"<?php echo ($maxg2 == 0)?' style="border:0"':'';?>>
            <h1<?php echo ($maxg2 == 0)?' style="padding-top:0"':'';?>><font id="gradename1"><?php echo shopgrade($maxg,'t');?>特权</font></h1>
            <div class="CC auth">
                <ul id="vipauth"></ul>
                <div class="clear"></div>
            </div>
        </div>
        <div class="viplist">
            <h1><font id="gradename2"><?php echo shopgrade($maxg,'t');?>套餐详情</font></h1>
            <div class="CC" id="vipC"></div>
        </div>
    <?php }else{?>
        <i class="ico" style="font-size:80px;color:#FF6F6F;margin-bottom:20px">&#xe634;</i>
        <div class="norole"><h4>升级服务暂时关闭</h4><br><font class="C999">了解详情请联系客服</font></div>
        <?php ?>
    <?php }?>
    <div class="shop_btm_kefu">
        <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><br><?php }?>
        <?php if (!empty($kf_tel)){?>
            <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
        <?php }else{?>
            <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
        <?php }?>
    </div>
</div>
<?php if ($total > 0){?>
<input type="hidden" id="grade" value="<?php echo $maxg;?>">
<script src="<?php echo HOST;?>/api/zeai_PAY.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/shop_vip.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script><?php if($maxg>0){?>setTimeout(function(){getvipauth(<?php echo $maxg;?>);},200);<?php }?></script>
<?php }?>
<?php require_once ZEAI.'m4/shop_bottom.php';?>
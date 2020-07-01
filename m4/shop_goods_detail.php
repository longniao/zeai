<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$up2 = $_ZEAI['up2'].'/';
$nav = 'shop_index';
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
if (!ifint($id))alert('信息不存在','shop_index.php');
$rt = $db->query("SELECT * FROM ".__TBL_TG_PRODUCT__." WHERE flag=1 AND id=".$id);
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'name');
	$id      = $row['id'];
	$tg_uid  = $row['tg_uid'];
	$path_s  = $row['path_s'];
	$piclist = $row['piclist'];
	$click   = intval($row['click']);
	$stock   = intval($row['stock']);
	$limitnum  = intval($row['limitnum']);
	$kindtitle = dataIO($row['kindtitle'],'out');
	$title     = dataIO($row['title'],'out');$ptitle=$title;
	$unit      = dataIO($row['unit'],'out');
	$content   = dataIO($row['content'],'out');
	$addtime   = $row['addtime'];
	$tgbfb1    = intval($row['tgbfb1']);
	$tgbfb2    = intval($row['tgbfb2']);
	$price   = str_replace(".00","",$row['price']);
	$price2  = str_replace(".00","",$row['price2']);
	$fahuokind = $row['fahuokind'];
	$fahuokind = ($fahuokind==2)?2:1;
	$path_s_url = (!empty($path_s))?$_ZEAI['up2'].'/'.$path_s:HOST.'/res/noP.gif?'.$_ZEAI['cache_str'];
	$path_b_url = (!empty($path_s))?$_ZEAI['up2'].'/'.smb($path_s,'b'):HOST.'/res/noP.gif?'.$_ZEAI['cache_str'];
	$unit=(!empty($unit))?'<font>'.$unit.'</font>':'';
	$rowC = $db->ROW(__TBL_TG_USER__,"weixin_ewm,photo_s,uname,title,kind,openid,subscribe,areatitle,address,tel,weixin,qq,qhdz,qhbz,qhlongitude,qhlatitude","id=".$tg_uid);
	if ($rowC){
		$photo_s     = $rowC['photo_s'];
		$cname       = dataIO($rowC['title'],'out');
		$kind        = $rowC['kind'];
		$weixin_ewm  = $rowC['weixin_ewm'];
		$openid    = $rowC['openid'];
		$subscribe = $rowC['subscribe'];
		$areatitle = $rowC['areatitle'];
		$address     = dataIO($rowC['address'],'out');
		$weixin      = dataIO($rowC['weixin'],'out');
		$qq          = dataIO($rowC['qq'],'out');
		$tel = dataIO($rowC['tel'],'out');
		$qhdz = dataIO($rowC['qhdz'],'out');
		$qhbz = dataIO($rowC['qhbz'],'out');
		$qhlongitude = $rowC['qhlongitude'];
		$qhlatitude  = $rowC['qhlatitude'];
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'b'):HOST.'/res/noP.gif?'.$_ZEAI['cache_str'];
		switch ($kind) {
			case 1:$kind_str='个人';break;
			case 2:$kind_str='公司';$p_str='商品';break;
			case 3:$kind_str='机构';$p_str='服务';break;
		}
	}else{
		alert('此'.$_SHOP['title'].'信息不存在','shop_index.php');
	}
	$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET click=click+1 WHERE id=".$id);
}else{
	alert('信息不存在或已下架','shop_index.php');
}
if(ifint($cook_tg_uid)){
	$rowtg = $db->ROW(__TBL_TG_USER__,"kuaidi_truename,kuaidi_address,kuaidi_mob,openid,subscribe,nickname,flag,shopflag","id=".$cook_tg_uid);
	if ($rowtg){
		$kuaidi_truename = dataIO($rowtg['kuaidi_truename'],'out');
		$kuaidi_address  = dataIO($rowtg['kuaidi_address'],'out');
		$kuaidi_mob      = dataIO($rowtg['kuaidi_mob'],'out');
		$cook_tg_openid  = $rowtg['openid'];
		$cook_tg_subscribe= $rowtg['subscribe'];
		$cook_tg_nickname = dataIO($rowtg['nickname'],'out');
		$cook_tg_shopflag = $rowtg['shopflag'];
		$cook_tg_flag = $rowtg['flag'];
	}
}
$jumpurl=urlencode(HOST.'/m4/shop_goods_detail.php?id='.$id.'&tguid='.$tguid);
switch ($submitok) {
	case 'ajax_gz':
		if(!ifint($cook_tg_uid))json_exit(array('flag'=>'nologin_tg','tguid'=>$tguid,'msg'=>'请登录后再操作','jumpurl'=>$jumpurl));
		$F = 1;$C = '收藏成功！';
		$row = $db->ROW(__TBL_SHOP_FAV__,"id","favid=".$id." AND kind=2 AND tg_uid=".$cook_tg_uid,"num");
		if($row){
			$db->query("DELETE FROM ".__TBL_SHOP_FAV__." WHERE favid=".$id."  AND kind=2 AND tg_uid=".$cook_tg_uid);
			$F = 0;
			$C = '取消成功！';
			$gzclass='';
			$gz_str='<i class="ico">&#xe620;</i> 收藏';
		}else{
			$db->query("INSERT INTO ".__TBL_SHOP_FAV__."(favid,tg_uid,addtime,kind) VALUES ($id,$cook_tg_uid,".ADDTIME.",2)");
			$gzclass=' ed';
			$gz_str='<i class="ico">&#xe604;</i> 取消收藏';
		}
		json_exit(array('flag'=>$F,'msg'=>$C));
	break;
	case 'ajax_buy':
		if(!ifint($cook_tg_uid))json_exit(array('flag'=>'nologin_tg','tguid'=>$tguid,'msg'=>'请登录后再操作','jumpurl'=>$jumpurl));
		json_exit(array('flag'=>1,'msg'=>'已登录'));
	break;
	case 'ajax_buy_addupdate':
		if(!ifint($cook_tg_uid))json_exit(array('flag'=>'nologin_tg','tguid'=>$tguid,'msg'=>'请登录后再操作','jumpurl'=>$jumpurl));
		$rowtg = $db->ROW(__TBL_TG_USER__,"id","id=".$cook_tg_uid." AND pwd='$cook_tg_pwd'");
		if (!$rowtg){
			setcookie("cook_tg_uid","",null,"/",$_ZEAI['CookDomain']);setcookie("cook_tg_uid","",null,"/",'');
			setcookie("cook_tg_pwd","",null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_openid","",null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_subscribe","",null,"/",$_ZEAI['CookDomain']);
			json_exit(array('flag'=>'nologin_tg','tguid'=>$tguid,'msg'=>'请登录后再操作','jumpurl'=>$jumpurl));
		}
		//
		if($cook_tg_flag==-1 && $cook_tg_shopflag==-1)json_exit(array('flag'=>0,'msg'=>'帐号锁定中，下单失败'));
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
		if ($fahuokind != 2){
			if(empty($form_address))json_exit(array('flag'=>0,'msg'=>'请输入【收件人详细地址】'));
			if(empty($form_truename))json_exit(array('flag'=>0,'msg'=>'请输入【收件人姓名】'));
			if(empty($form_mob))json_exit(array('flag'=>0,'msg'=>'请输入【收件人电话】'));
		}
		if(!ifint($form_num))json_exit(array('flag'=>0,'msg'=>'请输入【购买数量】'));
		if($stock<=0)json_exit(array('flag'=>0,'msg'=>'此商品库存不足'));
		if($form_num>$limitnum && $limitnum>0)json_exit(array('flag'=>0,'msg'=>'亲，当前商品限购'.$limitnum.'件哦'));
		$salenum = $db->COUNT(__TBL_SHOP_ORDER__,"pid=".$id." AND tg_uid=".$cook_tg_uid);
		if($salenum>=$limitnum && $salenum>0 && $limitnum>0)json_exit(array('flag'=>0,'msg'=>'亲，当前商品限购'.$limitnum.'件哦'));
		$cid     = $tg_uid;
		$tg_uid  = $cook_tg_uid;
		$pid     = $id;
		$num     = intval($form_num);
		$orderprice = $price*$num;
		$paytime = 0;$yuyuetime = 0;
		if($_SHOP['orderkind']==1){
			$flag = 9;
			$flag_str='预约';
			$yuyuetime = ADDTIME;
		}else{
			if($orderprice==0){
				$flag = 1;
				$flag_str='购买';
				$paytime = ADDTIME;
			}else{
				$flag = 0;
				$flag_str='下单';
			}
		}
		$truename = dataIO($form_truename,'in',20);
		$address  = dataIO($form_address,'in',100);
		$mob      = dataIO($form_mob,'in',20);
		$bz       = dataIO($form_bz,'in',200);
		$orderid  = 'SHOP-'.$cid.'-'.$pid.'-'.date("YmdHi");
		$orderkind= intval($_SHOP['orderkind']);
		$db->query("INSERT INTO ".__TBL_SHOP_ORDER__."  (orderid,cid,tg_uid,pid,price,num,orderprice,flag,address,truename,mob,addtime,paytime,yuyuetime,orderkind,bz,tgbfb1,tgbfb2) VALUES ('$orderid','$cid','$cook_tg_uid','$pid','$price','$num','$orderprice',$flag,'$address','$truename','$mob',".ADDTIME.",$paytime,$yuyuetime,$orderkind,'$bz',$tgbfb1,$tgbfb2)");
		$oid=$db->insert_id();
		//更新收件信息
		$db->query("UPDATE ".__TBL_TG_USER__." SET kuaidi_truename='$truename',kuaidi_address='$address',kuaidi_mob='$mob' WHERE id=".$cook_tg_uid);
		$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET stock=stock-1 WHERE stock>0 AND id=".$pid);
		//下单通知
		//站内通知
		$C = '【'.$title.'】'.$flag_str.'成功!';//　　<a href='.Href('my').' class=aQING>查看详情</a>
		$db->SendTip($cook_tg_uid,'【'.$title.'】'.$flag_str.'成功!',dataIO($C,'in'),'shop');
		//微信模版通知
		if (!empty($cook_tg_openid) && $cook_tg_subscribe==1){
			$keyword1 = '【'.$title.'】'.$flag_str.'成功!';
			$keyword3 = urlencode($_ZEAI['siteName']);
			$url = urlencode(HOST.'/m4/shop_my_order.php');
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$cook_tg_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		//通知卖家
		if(ifint($cid)){
			//站内
			$C = $cook_tg_nickname.'（ID:'.$cook_tg_uid.'）成功'.$flag_str.'->【'.$ptitle.'】';//　　<a href='.Href('my').' class=aQING>查看详情</a>
			$db->SendTip($cid,'成功'.$flag_str.'->【'.$ptitle.'】',dataIO($C,'in'),'shop');
			//微信
			if (!empty($openid) && $subscribe==1){
				$keyword1 = $cook_tg_nickname.'（ID:'.$tg_uid.'）成功'.$flag_str.'->【'.$ptitle.'】';
				$keyword3 = urlencode($_ZEAI['siteName']);
				$url      = urlencode(HOST.'/m4/shop_my_order.php?ifadm=1');
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
		}
		json_exit(array('flag'=>1,'msg'=>$flag_str.'成功','oid'=>$oid));
	break;
}
if($_SHOP['orderkind']==1){
	$buy_btn='预约购买';	
}else{
	$buy_btn=($stock >0)?'立即购买':'库存不足';
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $title;?></title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop_detail.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<style>
.linebox .line:before{height:2px}
.linebox .W50:before{left:7%;width:86%}
</style>
</head>
<body>
<i class="ico goback Ugoback" onClick="zeai.back();">&#xe602;</i>
<div class="shop_goods_detail">
    <div class="banner">
        <img src="<?php echo $path_b_url;?>">
        <img src="<?php echo $photo_s_url;?>" class="logo">
        <div id="goodsshare"><div class="loop_s_b_s"><i class="ico">&#xe615;</i> 分享</div></div>
    </div>
    <div class="ptitle">
        <a href="shop_detail.php?id=<?php echo $tg_uid;?>" class="h4"><font class="f<?php echo $kind;?>"><?php echo $kind_str;?></font><span><?php echo $cname;?></span><em><span class="ico">&#xe643;</span> <span><?php echo $click; ?></span></em></a>
        <h2><?php echo $title;?></h2>
        <div class="pricebox">
            <div class="price">￥<b><?php echo str_replace(".00","",number_format($price,2));?></b></div>
            <div class="price2"><?php echo $unit;?><span>原价:￥<?php echo number_format($price2);?></span></div>
            <?php
			$gzclass='';
			$gz_str='<i class="ico">&#xe620;</i> 收藏';
			if(ifint($cook_tg_uid)){
				$row = $db->ROW(__TBL_SHOP_FAV__,"id","favid=".$id." AND kind=2 AND tg_uid=".$cook_tg_uid,"num");
				if($row[0]){
					$gzclass=' ed';
					$gz_str='<i class="ico">&#xe604;</i> 取消收藏';
				}else{
					$gzclass='';
					$gz_str='<i class="ico">&#xe620;</i> 收藏';
				}
			}
			echo '<button id="shop_gzbtn" onclick="shop_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button>';
			?>
        </div>
        <div class="clear"></div>
        <?php if ($fahuokind == 2){?>
        <div class="fahuokind">该商品发货类型为【线下到店取货】，卖家发货后，请在<?php echo $_SHOP['hdday'];?>天内到店取货后立即核单确认</div>
        <?php }?>
        <div class="flex onum"><li>发货：<?php echo ($fahuokind == 2)?'线下取货':'快递物流';?></li><li>销量：<?php echo $db->COUNT(__TBL_SHOP_ORDER__,"pid=".$id);?></li><li><?php echo $areatitle;?></li></div>
        <div class="address">
        	<?php if ($fahuokind == 2){?>
				<?php if (!empty($qhdz)){?><a><i class="ico S16">&#xe614;</i><?php echo $qhdz;
                if(!empty($qhlongitude) && !empty($qhlatitude)){?>
                <span onclick="openmap(<?php echo $qhlongitude;?>,<?php echo $qhlatitude;?>,'<?php echo $cname;?>','<?php echo $qhdz;?>')"><i class="ico icomap">&#xe614;</i><font>地图直达</font></span>
                <?php }?></a>
                <?php }if (!empty($qhbz)){?><a><i class="ico qhdz">&#xe694;</i><?php echo $qhbz;?></a><?php }?>
            <?php }else{ ?>
				<?php if (!empty($address)){?><a><i class="ico S16" style="margin-left:-2px">&#xe614;</i><?php echo $address;?></a><?php }?>
                <?php if (!empty($tel)){?><a href="tel:<?php echo $tel;?>"><i class="ico">&#xe60e;</i><?php echo $tel;?></a><?php }?>
                <?php if (!empty($weixin)){?><a><i class="ico">&#xe607;</i><?php echo $weixin;?></a><?php }?>
                <?php if (!empty($qq)){?><a><i class="ico">&#xe612;</i><?php echo $qq;?></a><?php }?>
                <?php if ($kind == 2 && !empty($worktime)){?><a><i class="ico">&#xe634;</i><?php echo $worktime;?></a><?php }?>
            <?php }?>
        </div>
    </div>
    <div class="linebox"><div class="line W50"></div><div class="title BAI S14 C999">商品详情</div></div>
    <div class="C"><?php
		if(empty($content))$content=$nodatatips;
		echo $content;
		if(!empty($piclist)){
			$ARR=explode(',',$piclist);
			$ln=count($ARR);
			if($ln>=0){
				$picli='<div class="piclist">';
				foreach ($ARR as $V) {$picli.='<img src="'.$_ZEAI['up2'].'/'.smb($V,'b').'">';}
				$picli.='</div>';
				echo $picli;
			}
		}
	?></div>
    <div class="clear"></div>
</div>
<?php $weixin_ewm_url=(empty($weixin_ewm))?HOST.'/res/noP.gif':$_ZEAI['up2'].'/'.smb($weixin_ewm,'b');?>
<div id="shop_weixin_ewmBox" class="my-subscribe_box" style="display:none"><img src="<?php echo $weixin_ewm_url;?>"><h3>长按二维码添加客服微信</h3></div>
<div class="shop_detail_btm">
	<a href="shop_index.php"><i class="ico2">&#xe74e;</i><span>首页</span></a>
	<a href="shop_detail.php?id=<?php echo $tg_uid;?>"><i class="ico2">&#xe7d5;</i><span>进店</span></a>
	<a onClick="ZeaiM.div({obj:shop_weixin_ewmBox,w:260,h:260});"><i class="ico2">&#xe784;</i><span>客服</span></a>
	<a id="shop_buy_btn"<?php echo ($stock >0)?'':' class="hui"';?>><?php echo $buy_btn;?></a>
</div>
<div class="buybox" id="buybox">
    <h1>确认订单信息<?php echo ($fahuokind==2)?'<font style="color:#F7564D">（线下取货）</font>':'';?></h1>
    <form id="Zeai__cn_formP">
    <div class="br"></div>
    <?php if ($fahuokind != 2){?>
	<dl><dt>收件地址</dt><dd><input type="text" name="form_address" id="form_address" class="input" placeholder="请输入收件人详细【地址】" maxlength="100" value="<?php echo $kuaidi_address;?>" onBlur="zeai.setScrollTop(0);" autocomplete="off" ></dd></dl>
	<dl><dt>收件人</dt><dd><input type="text" name="form_truename" id="form_truename" class="input" placeholder="请输入收件人【姓名】" maxlength="20" value="<?php echo $kuaidi_truename;?>" onBlur="zeai.setScrollTop(0);" autocomplete="off" ></dd></dl>
	<dl><dt>联系电话</dt><dd><input type="text" name="form_mob" id="form_mob" class="input" placeholder="请输入收件人【电话】" pattern="[0-9]*" maxlength="20" value="<?php echo $kuaidi_mob;?>" onBlur="zeai.setScrollTop(0);" autocomplete="off" ></dd></dl>
    <?php }?>
	<dl><dt>购买数量</dt><dd><input type="text" name="form_num" id="form_num" class="input" placeholder="请输入【购买数量】" maxlength="5" value="1" pattern="[0-9]*" onBlur="zeai.setScrollTop(0);" autocomplete="off" ></dd></dl>
	<dl><dt>备注</dt><dd><input type="text" name="form_bz" id="form_bz" class="input" placeholder="备注留言 (100字以内)" maxlength="100" value="<?php echo $form_bz;?>" onBlur="zeai.setScrollTop(0);" autocomplete="off" ></dd></dl>
	<input type="hidden" name="submitok" value="ajax_buy_addupdate">
	<input type="hidden" name="id" value="<?php echo $id;?>">
    <input type="hidden" name="tguid" value="<?php echo $tguid;?>">
	</form>
    <div class="buybtm"><img src="<?php echo $path_s_url;?>"><span>￥<b><?php echo str_replace(".00","",number_format($price,2));?></b></span><button type="button" class="btn size4 HONG3 " id="shop_my_buybox_btn">提交订单</button></div>
</div>
<script src="<?php echo HOST;?>/res/html2canvas.js"></script>    
<script src="<?php echo HOST;?>/res/html2canvas_img.js"></script>
<div id="goodssharebox">
	<li><i class="ico2" onClick="copy('【<?php echo $title;?>】非常靠谱，强烈推荐给您。<?php echo HOST; ?>/m4/shop_goods_detail.php?id=<?php echo $id;?>&tguid=<?php echo $cook_tg_uid;?>');">&#xe616;</i><span>复制链接</span></li>
	<li id="zeai_haibaobtn"><i class="ico2">&#xea3b;</i><span>生成海报</span></li>
	<li id="wxshare"><i class="ico">&#xe607;</i><span>微信分享</span></li>
</div>
<div id="share_mask" class="mask1"></div>
<div id="share_box"><img src="<?php echo HOST;?>/res/shareico.png"></div>
<div id="card_detail">
	<div class="cardbox">
		<div class="pcard" id="cardcontent">
        	<img src="<?php echo $path_b_url;?>" class="pic">
            <h3 class="title"><?php echo $title;?></h3>
            <div class="titleinfo"><h5>￥<b><?php echo str_replace(".00","",number_format($price,2));?></b></h5></div>
            <img src="<?php echo HOST.'/sub/creat_ewm.php?url='.HOST; ?>/m4/shop_goods_detail.php?id=<?php echo $id;?>&tguid=<?php echo $cook_tg_uid;?>" class="card_ewm">
            <h6>识别二维码浏览商品详情</h6>
		</div>
	</div>
	<div class="cardbox_view" id="cardbox_view" ></div>
</div>
<script>var id=<?php echo $id;?>,stock=<?php echo intval($stock);?>,tguid=<?php echo intval($tguid);?>,fahuokind=<?php echo intval($fahuokind);?>;</script>
<script src="<?php echo HOST;?>/res/m4/js/shop_goods_detail.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr:'<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList:['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','getLocation','openLocation']});
	var FX_title = '<?php echo $ptitle;?>',
	FX_desc  = '<?php echo trimhtml($content).'【'.$_ZEAI['siteName'].'】';?>',
	FX_link  = '<?php echo HOST; ?>/m4/shop_goods_detail.php?id=<?php echo $id;?>&tguid=<?php echo $cook_tg_uid;?>',
	FX_imgurl= '<?php echo $path_b_url; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:FX_title,desc:FX_desc,link:FX_link,imgUrl:FX_imgurl});
		wx.onMenuShareTimeline({title:FX_title,link:FX_link,imgUrl:FX_imgurl});
	});
	function openmap (lng,lat,title,address){
		var newgps=b_t(lng,lat);
		lng=parseFloat(newgps[0]);lat=parseFloat(newgps[1]);
		wx.openLocation({
			latitude:lat,
			longitude:lng,
			name:title,
			address:address,
			scale:14,
			infoUrl:'http://weixin.qq.com'
		});
	}
	function b_t(lng,lat) {
		if (lng == null || lng == '' || lat == null || lat == '')return [lng, lat];
		var x_pi = 3.14159265358979324;
		var x = parseFloat(lng) - 0.0065;
		var y = parseFloat(lat) - 0.006;
		var z = Math.sqrt(x * x + y * y) - 0.00002 * Math.sin(y * x_pi);
		var theta = Math.atan2(y, x) - 0.000003 * Math.cos(x * x_pi);
		var lng = (z * Math.cos(theta)).toFixed(7);
		var lat = (z * Math.sin(theta)).toFixed(7);
		return [lng,lat];
	}	
	</script>
<?php }else{?>
	<script>function openmap (lng,lat,title,address){
		zeai.openurl('http://api.map.baidu.com/marker?location='+lat+','+lng+'&title='+address+'&content='+title+'&output=html');
	}</script>
<?php }?>
</body>
</html>
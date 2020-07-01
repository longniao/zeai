<?php
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$currfields = "loveb,subscribe";
require_once 'my_chkuser.php';
require_once ZEAI.'cache/config_vip.php';

$cook_openid=$row['openid'];
$cook_subscribe=$row['subscribe'];
if($submitok == 'ajax_duihuan'){
	$tmeplist = explode(',',$fidlist);
	if(empty($tmeplist) || !is_array($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要兑换的礼物'.$fidlist));
	if(count($tmeplist)>=1){
		$price = 0;
		$giftidlist = '';
		foreach($tmeplist as $v){
			if(!ifint($v))json_exit(array('flag'=>0,'msg'=>'ZEAIERR:ifint(v)'));
			$row = $db->ROW(__TBL_GIFT_USER__,"gid","uid=".$cook_uid." AND id=".$v,'num');
			if (!$row){exit(JSON_ERROR);}else{$gid = $row[0];}
			$row = $db->ROW(__TBL_GIFT__,"price","id=".$gid,'num');
			if (!$row){exit(JSON_ERROR);}else{
				$price = $price + $row[0];
				$giftidlist = $gid.','.$giftidlist;
			}
			$db->query("UPDATE ".__TBL_GIFT_USER__." SET ifdel=1 WHERE ifdel=0 AND uid=".$cook_uid." AND id=".$v);
		}
	}
	if($_VIP['gift_dhkind']=='loveb'){
		$addloveb = intval($price*$_VIP['gift_dhloveb_num']);
		$db->query("UPDATE ".__TBL_USER__." SET loveb=loveb+".$addloveb." WHERE id=".$cook_uid);
		//爱豆清单入库
		$db->AddLovebRmbList($cook_uid,'礼物兑换'.$_ZEAI['loveB'],$addloveb,'loveb',3);
		//爱豆站内消息
		$C = $cook_nickname.'您好，您有一笔'.$_ZEAI['loveB'].'到账(礼物兑换)！　'.$addloveb.' 　　<a href='.Href('loveb').' class=aQING>查看</a>';
		$db->SendTip($cook_uid,'礼物兑换'.$_ZEAI['loveB'].'，到账'.$addloveb,dataIO($C,'in'),'sys');
		//爱豆到账提醒
		if (!empty($cook_openid)){
			$first   = urlencode($cook_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
			$content = urlencode('礼物兑换'.$_ZEAI['loveB']);
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$cook_openid.'&num='.$addloveb.'&first='.$first.'&content='.$content.'&url='.HOST.'/?z=my&e=my_loveb');
		}
	}else{
		$addmoney = intval($price/abs(intval($_ZEAI['loveBrate'])));
		//写日志
		$giftidlist = rtrim($giftidlist,',');
		gyl_log('｜'.$bz.' -> uid：'.$cook_uid.'｜pc礼物兑换 ¥'.$addmoney.' 元(礼物ID列表:'.$giftidlist.')'.'｜ ¥'.$addmoney);
		//
		$db->query("UPDATE ".__TBL_USER__." SET money=money+".$addmoney." WHERE id=".$cook_uid);
		//余额清单入库
		$db->AddLovebRmbList($cook_uid,'礼物兑换',$addmoney,'money',11);	
		//余额站内消息
		$C = $cook_nickname.'您好，您有一笔资金到账(礼物兑换)！　¥'.$addmoney.' 元　　<a href='.Href('money').' class=aQING>查看余额</a>';
		$db->SendTip($cook_uid,'礼物兑换，余额到账¥'.$addmoney.' 元',dataIO($C,'in'),'sys');
		//余额到账提醒
		if (!empty($cook_openid)){
			$first   = urlencode($cook_nickname."您好，您有一笔资金到账！");
			$content = urlencode('礼物兑换');
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$cook_openid.'&num='.$addmoney.'&first='.$first.'&content='.$content.'&url='.urlencode(Href('money')));
		}
	}
	json_exit(array('flag'=>1,'msg'=>'恭喜您，礼物兑换成功'));
}
$t = (ifint($t,'1-3','1'))?$t:1;
$zeai_cn_menu = 'my_gift';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的礼物 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_gift.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>我的礼物</h1>
        <div class="tab">
			<?php
            if ($t == 2) {
                $SQL = "SELECT a.id,a.uid,a.new,b.id AS gid,b.title,b.picurl,c.sex,c.grade,c.nickname AS sendnickname,b.price FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b,".__TBL_USER__." c WHERE a.senduid=".$cook_uid." AND a.gid=b.id AND a.uid=c.id  ORDER BY a.id DESC";
				$rt=$db->query($SQL);$total = $db->num_rows($rt);
				$total2 = $total;
            }elseif($t == 3){
                $SQL = "SELECT a.id,a.senduid,a.new,b.id AS gid,b.title,b.picurl,c.sex,c.grade,c.nickname AS sendnickname,b.price FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b,".__TBL_USER__." c WHERE a.uid=".$cook_uid." AND a.gid=b.id AND a.senduid=c.id AND a.ifdel=1 ORDER BY a.id DESC";
				$rt=$db->query($SQL);$total = $db->num_rows($rt);
				$total3 = $total;
            }else{
				$db->query("UPDATE ".__TBL_GIFT_USER__." SET new=0 WHERE uid=".$cook_uid." AND new=1");
                $SQL = "SELECT a.id,a.senduid,a.new,b.id AS gid,b.title,b.picurl,c.sex,c.grade,c.nickname AS sendnickname,b.price FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b,".__TBL_USER__." c WHERE a.uid=".$cook_uid." AND a.gid=b.id AND a.senduid=c.id AND a.ifdel=0 ORDER BY a.id DESC";
				$rt=$db->query($SQL);$total = $db->num_rows($rt);
				$total1 = $total;
            }
            ?>
            <a href="<?php echo SELF;?>?t=1"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>>我收到的<?php echo ($total1>0)?' ('.$total1.')':'';?></a>
            <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>我送出的<?php echo ($total2>0)?' ('.$total2.')':'';?></a>
            <a href="<?php echo SELF;?>?t=3"<?php echo ($t==3)?' class="ed"':'';?>>我兑换的<?php echo ($total3>0)?' ('.$total3.')':'';?></a>
            <?php if ($t == 1){?>
            <div class="tab_Rbox btnbox">
            	<li><input type="checkbox" id="selectall_mygift" class="checkskin" value="4"><label for="selectall_mygift" class="checkskin-label"><i class="i2"></i><b class="W50">全选</b></label></li>
                <li><button type="button" id="btn_duihuan" class="btn size3 HONG<?php echo ($total1==0)?' disabled':'';?>">兑换</button></li>
            </div>
            <?php }?>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="my_gift" id="my_gift_box">
				<?php
                if($total>0){
                    $page_skin=2;$pagemode=4;$pagesize=20;$page_color='#E83191';require_once ZEAI.'sub/page.php';
					echo '<ul>';
                    for($i=1;$i<=$pagesize;$i++) {
                        $rows = $db->fetch_array($rt,'num');
                        if(!$rows)break;
						$id      = $rows[0];
						$UID     = $rows[1];
						$new     = $rows[2];
						$gid     = $rows[3];
						$title   = dataIO($rows[4],'out');
						$picurl  = $_ZEAI['up2'].'/'.$rows[5];
						$sex   = $rows[6];
						$grade = $rows[7];
						$sendnickname= dataIO($rows[8],'out');
						$price = $rows[9];
						?>
                        <li id='li<?php echo $id;?>' gid='<?php echo $gid;?>' uid='<?php echo $UID;?>'>
                            <p><img src="<?php echo $picurl; ?>"></p>
                            <h2><?php echo $title; ?></h2>
                            <h5><i class="ico">&#xe618;</i><i><?php echo $price; ?></i></h5>
                            <?php if ($t == 1){ ?>
                                <h4><a><?php echo uicon($sex.$grade).$sendnickname; ?></a></h4>
                                <div class="my_gift_dh"><input type="checkbox" name="list[]" id="gift<?php echo $id; ?>" class="checkskin " value="<?php echo $id; ?>"><label  for="gift<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i><b class="W30">兑换</b></label></div>
                            <?php }?>
                        </li>
                    <?php
						if ($i % 5 == 0){echo '</ul>';if ($i != $total)echo '<ul>';}
					}
                    if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo nodatatips('暂无礼物');}
				
				if($_VIP['gift_dhkind']=='loveb'){
					$bl = ($_VIP['gift_dhloveb_num']==1)?'原价':floatval($_VIP['gift_dhloveb_num']*10).'折';
					$dhstr ='将以'.$bl.'充值到您的'.$_ZEAI['loveB'].'账户';
					$dhstr_=$_ZEAI['loveB'];
					$url_  = 'my_loveb.php';
				}else{
					$dhstr ='将以'.$_ZEAI['loveBrate'].$_ZEAI['loveB'].'=1元 充值到您的余额账户';
					$dhstr_='余额';
					$url_  = 'my_money.php';
				}
				
            	?>
                <!--提示开始-->
                	<div class="clear"></div>
                    <div class="tipsbox">
                        <div class="tipst">温馨提示：</div>
                        <div class="tipsc">
                            ● 兑换成功后，请到<?php echo $dhstr_;?>账户查看　<a href="<?php echo $url_;?>" class="btn size1 BAI">查看<?php echo $dhstr_;?>账户</a>
                        </div>
                    </div>
                <!--提示结束-->                
        	</div>
        </div>
        <!-- end C -->
</div></div></div>
<?php 
$dhstr = ($_VIP['gift_dhkind']=='loveb')?$_ZEAI['loveB']:'余额';
if($_VIP['gift_dhkind']=='loveb'){
	$bl = ($_VIP['gift_dhloveb_num']==1)?'原价':floatval($_VIP['gift_dhloveb_num']*10).'折';
	$dhstr = '将以'.$bl.'充值到您的'.$_ZEAI['loveB'].'账户';
}else{
	$dhstr ='将以'.$_ZEAI['loveBrate'].$_ZEAI['loveB'].'=1元 充值到您的余额账户';
}
?>
<script>var dhstr='<?php echo $dhstr;?>';</script>
<script src="js/my_gift.js"></script>
<script>
<?php if ($t == 1){?>
var loveB='<?php echo $_ZEAI['loveB']; ?>',loveBrate=<?php echo abs(intval($_ZEAI['loveBrate']));?>;
selectall_mygift.onclick=selectall_mygiftFn;
btn_duihuan.onclick = btn_duihuanFn;
<?php }?>
</script>
<?php require_once ZEAI.'p1/bottom.php';ob_end_flush();?>
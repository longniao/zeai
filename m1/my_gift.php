<?php
require_once '../sub/init.php';
$currfields = "money,openid";
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
$data_money  = intval($row['money']);
$data_openid = $row['openid'];
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无内容～～</div>";
if($submitok == 'ajax_in' || $submitok == 'ajax_out' || $submitok == 'ajax_dh'){
	if($submitok == 'ajax_in'){
		$t=1;
	}elseif($submitok == 'ajax_out'){
		$t=2;
	}elseif($submitok == 'ajax_dh'){
		$t=3;
	}
	if ($t == 2) {
		$SQL = "SELECT a.id,a.uid,a.new,b.id AS gid,b.title,b.picurl,c.sex,c.grade,c.nickname AS sendnickname,b.price FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b,".__TBL_USER__." c WHERE a.senduid=".$cook_uid." AND a.gid=b.id AND a.uid=c.id  ORDER BY a.id DESC";
	}elseif($t == 3){
		$SQL = "SELECT a.id,a.senduid,a.new,b.id AS gid,b.title,b.picurl,c.sex,c.grade,c.nickname AS sendnickname,b.price FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b,".__TBL_USER__." c WHERE a.uid=".$cook_uid." AND a.gid=b.id AND a.senduid=c.id AND a.ifdel=1 ORDER BY a.id DESC";
	}else{
		$db->query("UPDATE ".__TBL_GIFT_USER__." SET new=0 WHERE uid=".$cook_uid." AND new=1");
		$SQL = "SELECT a.id,a.senduid,a.new,b.id AS gid,b.title,b.picurl,c.sex,c.grade,c.nickname AS sendnickname,b.price FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b,".__TBL_USER__." c WHERE a.uid=".$cook_uid." AND a.gid=b.id AND a.senduid=c.id AND a.ifdel=0 ORDER BY a.id DESC";
	}
	$rt=$db->query($SQL);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		echo '<ul>';
		for($i=1;$i<=$total;$i++) {
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
		if ($i % 3 == 0){echo '</ul>';if ($i != $total)echo '<ul>';}
	?>
    <?php }}else{echo $nodatatips;}?>
	<script>zeai.listEach('.my_gift_dh',function(e){e.onclick = function(){mygift_showflagFn();}	});</script>
<?php
exit;}elseif($submitok == 'ajax_duihuan'){
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
		if (!empty($data_openid)){
			$first   = urlencode($cook_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
			$content = urlencode('礼物兑换'.$_ZEAI['loveB']);
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$addloveb.'&first='.$first.'&content='.$content.'&url='.HOST.'/?z=my&e=my_loveb');
		}
	}else{
		$addmoney = intval($price/abs(intval($_ZEAI['loveBrate'])));
		if(!empty($_VIP['gift_dhmoney_num']))$addmoney = floatval($addmoney*$_VIP['gift_dhmoney_num']);
		//写日志
		$giftidlist = rtrim($giftidlist,',');
		gyl_log('｜'.$bz.' -> uid：'.$cook_uid.'｜mob礼物兑换 ¥'.$addmoney.' 元(礼物ID列表:'.$giftidlist.')'.'｜ ¥'.$addmoney);
		//
		$db->query("UPDATE ".__TBL_USER__." SET money=money+".$addmoney." WHERE id=".$cook_uid);
		//余额清单入库
		$db->AddLovebRmbList($cook_uid,'礼物兑换余额',$addmoney,'money',11);	
		//余额站内消息
		$C = $cook_nickname.'您好，您有一笔资金到账(礼物兑换)！　¥'.$addmoney.' 元　　<a href='.Href('money').' class=aQING>查看余额</a>';
		$db->SendTip($cook_uid,'礼物兑换，余额到账¥'.$addmoney.' 元',dataIO($C,'in'),'sys');
		//余额到账提醒
		if (!empty($data_openid)){
			$first   = urlencode($cook_nickname."您好，您有一笔资金到账！");
			$content = urlencode('礼物兑换');
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$addmoney.'&first='.$first.'&content='.$content.'&url='.HOST.'/?z=my&e=my_money');
		}
	}
	json_exit(array('flag'=>1,'msg'=>'恭喜您，礼物兑换成功'));
}
$a = (empty($a))?'in':$a;
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-my_gift">&#xe602;</i>我的礼物';
$mini_class = 'top_mini top_miniBAI';?>
<?php if($_ZEAI['mob_mbkind']==3){?>
<style>
.tabmenuBAI li.ed span{color:#FF6F6F}
#tabmenu_my_gift i{background:#FF6F6F}
</style>
<?php }?>
<?php
require_once ZEAI.'m1/top_mini.php';
?>
<link href="m1/css/my_gift.css" rel="stylesheet" type="text/css" />
<div class="tabmenu tabmenu_3 tabmenuBAI" id="tabmenu_my_gift">
    <li<?php echo ($e == 'in')?' class="ed"':''; ?> data="m1/my_gift.php?submitok=ajax_in" id="my_gift_inbtn"><span>我收的</span></li>
    <li<?php echo ($e == 'out')?' class="ed"':''; ?> data="m1/my_gift.php?submitok=ajax_out" id="my_gift_outbtn"><span>我送的</span></li>
    <li<?php echo ($e == 'dh')?' class="ed"':''; ?> data="m1/my_gift.php?submitok=ajax_dh" id="my_gift_dhbtn"><span>我兑的</span></li>
	<i></i>
</div>
<div class="submain my_gift" id="my_gift_box"></div>
<div class="btnbox" id="my_gift_btnbox">
	<div class="shadow"></div>
    <li><input type="checkbox" id="selectall_mygift" class="checkskin" value="4"><label for="selectall_mygift" class="checkskin-label"><i class="i1"></i><b class="W50">全选</b></label></li>
    <li id="btn_duihuan">兑换</li>
</div>
<script>
<?php 
$dhstr = ($_VIP['gift_dhkind']=='loveb')?$_ZEAI['loveB']:'余额';
if($_VIP['gift_dhkind']=='loveb'){
	$bl = ($_VIP['gift_dhloveb_num']==1)?'原价':floatval($_VIP['gift_dhloveb_num']*10).'折';
	$dhstr = '将以'.$bl.'充值到您的'.$_ZEAI['loveB'].'账户';
}else{
	$dhstr ='将以'.$_ZEAI['loveBrate'].$_ZEAI['loveB'].'=1元 充值到您的余额账户';
}
?>
var loveB='<?php echo $_ZEAI['loveB']; ?>',loveBrate=<?php echo abs(intval($_ZEAI['loveBrate']));?>,dhstr='<?php echo $dhstr;?>';
ZeaiM.tabmenu.init({obj:tabmenu_my_gift,showbox:'my_gift_box',click:function(li){
	if(li.id=='my_gift_inbtn'){
		my_gift_btnbox.show();my_gift_box.style.bottom='44px';
	}else if(li.id=='my_gift_outbtn'){
		my_gift_btnbox.hide();my_gift_box.style.bottom=0;
	};
}});
setTimeout(function(){my_gift_<?php echo $a;?>btn.click();},100);
btn_duihuan.onclick = btn_duihuanFn;
selectall_mygift.onclick=selectall_mygiftFn;
</script>
<script src="m1/js/my_gift.js"></script>

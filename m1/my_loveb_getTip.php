<?php
require_once '../sub/init.php';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/udata.php';
//
$tg = json_decode($_REG['tg'],true);

if ($backPage=='my_loveb'){
	$mini_title = '如何获得'.$_ZEAI['loveB'].'？';
	$curpage = 'my_loveb_getTip';
	$urole = json_decode($_ZEAI['urole']);
	$contact_loveb = json_decode($_VIP['contact_loveb']);
	$chat_loveb    = json_decode($_VIP['chat_loveb']);
	$task_loveb    = json_decode($_VIP['task_loveb'],true);
}elseif($backPage=='my_money'){
	$mini_title = '如何获得余额？';
	$curpage = 'my_money_getTip';
	$switch  = json_decode($_ZEAI['switch'],true);
}
//
if($backPage=='my_money'){
	$mini_ext='style="background-color:#EE5A4E"';
}
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$curpage.'">&#xe602;</i>'.$mini_title;
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '';
require_once ZEAI.'m1/top_mini.php';
?>
<style>
.submain{background-color:#fff}
.my_loveb_getTip dl,
.my_money_getTip dl{width:100%;padding:0 20px;border-bottom:#eee 1px solid;box-sizing:border-box}
.my_loveb_getTip dl dt,
.my_loveb_getTip dl dd,
.my_money_getTip dl dt,
.my_money_getTip dl dd{display:inline-block;line-height:50px}
.my_loveb_getTip dl dt,
.my_money_getTip dl dt{width:75%;font-size:14px;text-align:left;color:#8d8d8d}
.my_loveb_getTip dl dt font,
.my_money_getTip dl dt font{color:#E75385}
.my_loveb_getTip dl dd,
.my_money_getTip dl dd{width:25%;text-align:right}
.my_loveb_getTip dl dd button,
.my_money_getTip dl dd button{border:#A1C655 1px solid;background-color:#fff;color:#A1C655;line-height:26px;width:66px;border-radius:2px}
</style>
<!--<i class='ico goback' id='ZEAIGOBACK-<?php echo $curpage;?>'>&#xe602;</i>-->
<div class="submain <?php echo $curpage;?>">

<?php if ($backPage=='my_loveb'){?>

    <dl><dt>每日签到<?php echo $_ZEAI['loveB']; ?>随机送</dt><dd><button onClick="zeai.msg('请到“我的”首页左上侧签到~'),ZeaiM.page.jump(ZEAI_MAIN);">我要签到</button></dd></dl>
    <dl><dt>在线充值(1元=<?php echo $_ZEAI['loveBrate'];?>个)</dt><dd><button onClick="<?php if (!empty($backPage)){?>ZeaiM.page.back('<?php echo $backPage;?>','<?php echo $curpage;?>');my_loveb_czbtn.click();<?php }else{ ?>ZeaiM.page.jump('m1/my_loveb.php?a=cz','my_loveb');<?php }?>">我要充值</button></dd></dl>
    
    
    <dl><dt>上传头像随机奖励</dt><dd><button onClick="ZeaiM.page.load('m1/my_info.php','<?php echo $curpage;?>','my_info');">我要上传</button></dd></dl>
    <dl><dt>上传相册随机奖励</dt><dd><button onClick="ZeaiM.page.load('m1/my_info.php?a=photo','<?php echo $curpage;?>','my_info');">我要上传</button></dd></dl>
    <?php if(@in_array('video',$navarr)){?><dl><dt>上传视频随机奖励</dt><dd><button onClick="ZeaiM.page.load('m1/my_info.php?a=video','<?php echo $curpage;?>','my_info');">我要上传</button></dd></dl><?php }?>
    
<?php }elseif($backPage=='my_money'){?>
    
    <?php if ($switch['ifhb']==1){?><dl><dt>讨红包或抢红包</dt><dd><button onClick="zeai.openurl('m1/hongbao/my/hongbao.php?t=4')">我要讨</button></dd></dl><?php }?>
    <dl><dt>在线充值</dt><dd><button onClick="<?php if (!empty($backPage)){?>ZeaiM.page.back('<?php echo $backPage;?>','<?php echo $curpage;?>');my_money_czbtn.click();<?php }else{ ?>ZeaiM.page.jump('m1/my_money.php?a=cz','my_money');<?php }?>">我要充值</button></dd></dl>
	<?php if(in_array('tg',$navarr)){?>
	<dl><dt>推荐分享新会员注册奖励</dt><dd><button onClick="wytj();">我要推荐</button></dd></dl>
    
        <script>
		//ZeaiM.page.load('m1/TG.php','<?php echo $curpage;?>','TG');
        function wytj(){
			//ZeaiM.page.jump(ZEAI_MAIN);
			//ZeaiM.page.load('m1/TG.php',ZEAI_MAIN,'TG');
			zeai.openurl('<?php echo HOST;?>/m1/tg_my.php');
		}
        </script>    
    
	<?php }?>
    
<?php }?>
</div>
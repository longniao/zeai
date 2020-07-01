<?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
<div class="footer">
    <div class="linebox">
        <div class="line"></div>
        <div class="title">我是有底线的</div>
    </div>
	<?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><?php }?>
	<div class="copyright">
    <?php if (!empty($kf_tel)){?>电话：<a href="tel:<?php echo $kf_tel;?>" class="C999"><?php echo $kf_tel;?></a><?php }else{?>
		<?php if (!empty($kf_mob)){?>手机：<a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
    <?php }?>
    <br><a href="http://www.zeai.cn" class="zeai">&copy;<?php echo date('Y').' '.$_ZEAI['siteName'];?>V<?php echo $_ZEAI['ver'];?></a></div><!-- href="http://www.zeai.cn" -->
</div>
<?php 
if(ifint($cook_tg_uid)){
	$tipnum = $db->COUNT(__TBL_TIP__,"new=1 AND kind=5 AND tg_uid=".$cook_tg_uid);
	$tipnum_str = ($tipnum>0)?'<b id="tg_num_btm">'.$tipnum.'</b>':'';
}
?>
<div id="Zeai_cn__PageBox"></div><div id="blankpage"></div>
<div class="TG_BtmBM huadong" id="nav">
	<a href="tg_index.php"<?php echo ($nav=='tg_index')?' class="ed"':'';?>><i class="ico">&#xe7a0;</i><span>首页</span></a>
	<a class="ljtg" onclick="page({g:'tg_my.php?submitok=TG_HELP&request=www_zeai_cn__ajax',l:'TG_HELP'})"><i class="ico">&#xe616;</i><span>帮助</span></a>
	<a class="syb" onclick="page({g:'tg_my.php?submitok=TG_MSG&request=www_zeai_cn__ajax',l:'TG_MSG'})"><i class="ico">&#xe657;<?php echo $tipnum_str;?></i><span>通知</span></a>
	<a class="ljtg" href="../"><i class="ico">&#xe62f;</i><span>相亲</span></a>
	<a href="tg_my.php"<?php echo ($nav=='tg_my')?' class="ed"':'';?>><i class="ico">&#xe645;</i><span>我的</span></a>
</div>
</body></html>
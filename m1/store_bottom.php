<?php 

?>
<div id="Zeai_cn__PageBox"></div><div id="blankpage"></div>
<div class="store_btm">
	<a href="tg_index.php"<?php echo ($nav=='tg_index')?' class="ed"':'';?>><i class="ico">&#xe7a0;</i><span>首页</span></a>
	<a class="ljtg" onclick="page({g:'tg_my.php?submitok=TG_HELP&request=www_zeai_cn__ajax',l:'TG_HELP'})"><i class="ico">&#xe616;</i><span>帮助</span></a>
	<a class="ljtg" onClick="page({g:'tg_my_ewm.php',l:'tg_my_ewm'});"><i class="ico">&#xe615;</i><span>立即推广</span></a>
	<a class="syb" onclick="page({g:'tg_my.php?submitok=TG_MSG&request=www_zeai_cn__ajax',l:'TG_MSG'})"><i class="ico">&#xe657;<?php echo $tipnum_str;?></i><span>通知</span></a>
	<a href="tg_my.php"<?php echo ($nav=='tg_my')?' class="ed"':'';?>><i class="ico">&#xe645;</i><span>我的</span></a>
</div>
</body></html>
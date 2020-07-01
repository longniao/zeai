<?php !function_exists('IFZEAI') && exit('Forbidden!');
$viewnum = $db->COUNT(__TBL_CLICKHISTORY__,"new=1 AND uid=".$cook_uid);
if(@in_array('gift',$navarr)){
	$giftnum = $db->COUNT(__TBL_GIFT_USER__,"new=1 AND uid=".$cook_uid);
}
if($iftipnum!=1){$tipnum  = $db->NUM($cook_uid,"tipnum","id=".$cook_uid." AND tipnum>0");$tipnum=$tipnum[0];}
$left_sxnum_str  =($tipnum>0)?'<b>'.$tipnum.'</b>':'';
$left_viewnum_str=($viewnum>0)?'<b>'.$viewnum.'</b>':'';
$left_giftnum_str=($giftnum>0)?'<b>'.$giftnum.'</b>':'';
?>
<a href="my.php" class="tbody">个人中心首页</a>
<h4 class="vip" onClick="zeai.openurl('my_vip.php')" style="display:none"><i class="ico" style="color:#A18264">&#xe6ab;</i>VIP升级<b></b></h4>
<h4><i class="ico" style="color:#30CEF5">&#xe623;</i>互动</h4>
<a href="my_msg.php"<?php echo ($zeai_cn_menu == 'my_msg')?" class='ed firsttop'":""; ?>><em>私信通知<?php echo $left_sxnum_str;?></em></a>
<a href="my_browse.php"<?php echo ($zeai_cn_menu == 'my_browse')?" class='ed '":""; ?>><em>谁看过我<?php echo $left_viewnum_str;?></em></a>
<a href="my_follow.php"<?php echo ($zeai_cn_menu == 'my_follow')?" class='ed '":""; ?>><em>我关注的</em></a>
<?php if(@in_array('gift',$navarr)){?><a href="my_gift.php"<?php echo ($zeai_cn_menu == 'my_gift')?" class='ed '":""; ?>><em>我的礼物<?php echo $left_giftnum_str;?></em></a><?php }?>
<?php if(@in_array('hn',$navarr)){?><a href="my_hongniang.php"<?php echo ($zeai_cn_menu == 'my_hongniang')?" class='ed '":""; ?>><em>我的红娘</em></a><?php }?>
<h4><i class="ico" style="color:#FEA2C8">&#xe618;</i>账户</h4>
<a href="my_loveb.php"<?php echo ($zeai_cn_menu == 'my_loveb')?" class='ed '":""; ?>><em>我的<?php echo $_ZEAI['loveB'];?></em></a>
<a href="my_money.php"<?php echo ($zeai_cn_menu == 'my_money')?" class='ed '":""; ?>><em>我的余额</em></a>
<h4><i class="ico" style="color:#FFAE00">&#xe63e;</i>资料</h4>
<a href="my_info.php"<?php echo ($zeai_cn_menu == 'my_info')?" class='ed firsttop'":""; ?>><em>个人资料</em></a>
<a href="my_cert.php"<?php echo ($zeai_cn_menu == 'my_cert')?" class='ed'":""; ?>><em>诚信认证</em></a>
<a href="my_photo.php"<?php echo ($zeai_cn_menu == 'my_photo')?" class='ed'":""; ?>><em>个人相册</em></a>
<?php if(@in_array('video',$navarr)){?><a href="my_video.php"<?php echo ($zeai_cn_menu == 'my_video')?" class='ed'":""; ?>><em>个人视频</em></a><?php }?>
<?php if(@in_array('hb',$navarr) || @in_array('trend',$navarr) || @in_array('dating',$navarr)){?>
    <h4><i class="ico" style="color:#5EB87B;font-size:19px">&#xe647;</i>应用</h4>
	<?php if(@in_array('hb',$navarr)){?><a href="my_hongbao.php"<?php echo ($zeai_cn_menu == 'my_hongbao')?" class='ed'":""; ?>><em>我的红包</em></a><?php }?>
    <?php if(@in_array('trend',$navarr)){?><a href="my_trend.php"<?php echo ($zeai_cn_menu == 'my_trend')?" class='ed '":""; ?>><em>交友圈</em></a><?php }?>
    <?php if(@in_array('dating',$navarr)){?><a href="my_dating.php"<?php echo ($zeai_cn_menu == 'my_dating')?" class='ed '":""; ?>><em>我的约会</em></a><?php }?>
<?php }?>
<h4><i class="ico" style="color:#A884E9">&#xe649;</i>设置</h4>
<a href="my_set.php?t=1"<?php echo ($zeai_cn_menu == 'my_set1')?" class='ed'":""; ?>><em>帐号设置</em></a>
<a href="my_set.php?t=2"<?php echo ($zeai_cn_menu == 'my_set2')?" class='ed'":""; ?>><em>隐私设置</em></a>
<a href="my_set.php?t=3"<?php echo ($zeai_cn_menu == 'my_set3')?" class='ed'":""; ?>><em>密码设置</em></a>

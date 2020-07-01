<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
require_once ZEAI.'sub/conn.php';
$t = (!empty($a))?$a:$t;
$BK=(!empty($a))?'about':'about_contact';

$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
?>
<style>
.submainX{width:100%;max-width:640px;bottom:0;overflow:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;box-sizing:border-box;position:absolute;top:44px;text-align:left}
.submainX{background-color:#fff;padding:30px;line-height:200%;font-size:16px}
.submainX img{max-width:100%}

.submainX dl{padding:10px 0;border-bottom:#f0f0f0 1px solid;box-sizing:border-box;clear:both;overflow:auto}
.submainX dl dt,.submainX dl dd{font-size:18px}
.submainX dl dt{width:20%;float:left;color:#999}
.submainX dl dd{width:75%;float:right}
.submainX .wxpic{text-align:center;padding:20px 0 50px}
.submainX .wxpic img{width:50%;display:block;margin:10px auto 10px auto;padding:10px;border:#eee 1px solid}
</style>
<?php if ($t == 'us'){
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-about_us">&#xe602;</i>关于我们';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
    <div class="submainX"><?php $row = $db->ROW(__TBL_NEWS__,"content","id=2");echo ($row)?dataIO($row[0],'out'):$nodatatips;?></div>
<?php }elseif($t == 'contact'){
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$BK.'">&#xe602;</i>联系我们';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';
	$kf_mob   = dataIO($_ZEAI['kf_mob'],'out');
	$kf_tel   = dataIO($_ZEAI['kf_tel'],'out');
	$kf_qq    = dataIO($_ZEAI['kf_qq'],'out');
	$kf_wx    = dataIO($_ZEAI['kf_wx'],'out'); 
	$kf_wxpic = dataIO($_ZEAI['kf_wxpic'],'out'); 
	$kf_address = dataIO($_ZEAI['kf_address'],'out'); 
	?>
    <div class="submainX">
    	<dl><dt>手机</dt><dd><a href="tel:<?php echo $kf_mob;?>"><i class="ico S18" style="display:inline-block">&#xe60e;</i> <?php echo $kf_mob;?></a></dd></dl>
    	<dl><dt>电话</dt><dd><a href="tel:<?php echo $kf_tel;?>"><i class="ico S18" style="display:inline-block">&#xe60e;</i> <?php echo $kf_tel;?></a></dd></dl>
    	<dl><dt>地址</dt><dd><?php echo $kf_address;?></dd></dl>
    	<dl><dt>QQ</dt><dd><?php echo $kf_qq;?></dd></dl>
    	<dl><dt>微信号</dt><dd><?php echo $kf_wx;?></dd></dl>
    	<?php if (!empty($kf_wxpic)){?>
        <div class="clear"></div>
        <div class="wxpic">
        <img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font class="C999">长按或扫描二维码加客服微信</font>
        </div>
		<?php }?>
    </div>
<?php }elseif($t == 'declara'){
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-about_declara">&#xe602;</i>会员条款/免责声明';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
	<div class="submainX"><?php $row = $db->ROW(__TBL_NEWS__,"content","id=1");echo ($row)?dataIO($row[0],'out'):$nodatatips;?></div>
<?php }?>
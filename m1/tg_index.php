<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
//if (!ifint($cook_tg_uid)){header("Location: tg_login.php");exit;}
if (!is_mobile())exit('请用手机打开');
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
if($TG_set['force_weixin']==1 && !is_weixin() && !is_h5app())exit('请在微信中打开');
/*************AJAX页面开始*************/
switch ($submitok) {
	case 'ajax_login_uname_chk':
		json_exit(array('flag'=>$chkflag,'msg'=>$content,'jumpurl'=>'tg_my.php'));
	break;
}
$headertitle = $TG_set['navtitle'].'-'.$_ZEAI['siteName'];$nav = 'tg_index';require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="<?php echo HOST;?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug: false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	</script>
	<?php
}
?>
<link href="css/TG2.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">

<?php if(@!in_array('tg',$navarr))exit("<div class='nodatatips'><i class='ico'>&#xe61f;</i>推广功能暂未开启</div>");?>

<style>
.top_miniI{background:#F7564D}
.top_miniI h1{color:#fff}
</style>
<?php 
$mini_class = 'top_mini top_miniI';
$mini_backT = '';
$mini_title = $TG_set['navtitle'];
require_once ZEAI.'m1/top_mini.php';
?>
<div id='main' class='submain TG_BANG huadong'>
	<?php
	$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe61f;</i>～～暂无内容～～<a class=\"btn size4 yuan TG_BANGabtn\" onClick=\"page({g:'tg_my_ewm.php',l:'tg_my_ewm'});\">我要推广</a><div>通过我分享的分享二维码海报加入即可成为我的成员，发展的我团队，让收益迅速暴增</div></div>";
	$mini_backT = '返回';
	$mini_title = "<i class='ico goback' id='ZEAIGOBACK-TG_BANG'>&#xe602;</i>";
	$mini_class = 'top_mini top_miniBAI';
	require_once ZEAI.'m1/top_mini.php';
	?>
	<div class="linebox" style="margin-top:5px"><div class="line W50"></div><div class="title S24 BAI B" style="color:#000">收益榜</div></div>
    <ul><li>排名</li><li>分享会员</li><li>总收益(元)</li></ul>
    <?php
    $rt=$db->query("SELECT id,nickname,mob,grade,money FROM ".__TBL_TG_USER__." ORDER BY money DESC LIMIT 5");
    $total = $db->num_rows($rt);
    if ($total <= 0) {
        echo $nodatatips;
    }else{?>
        <?php
        for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'name');
            if(!$rows)break;
            $uid      = $rows['id'];
            $uname    = dataIO($rows['nickname'],'out');
            $mob      = dataIO($rows['mob'],'out');
            $sex      = $rows['sex'];
            $grade    = $rows['grade'];
            $tgallmoney  = $rows['money'];
            //
            $price = $tgallmoney;
            $uname = (empty($uname))?$mob:$uname;
            //
            $row_role=$db->ROW(__TBL_TG_ROLE__,"logo","grade=".$grade,"name");
            $data_logo  =$row_role['logo'];
            if(!empty($data_logo)){
                $gradeico_str='<img src="'.$_ZEAI['up2'].'/'.$data_logo.'" class="logo">';
            }else{
                $gradeico_str='<img src="'.HOST.'/res/tg_ico.svg" class="logo">';
            }
            //
            if ($i == 1){
                $ico = '<i class="ico i1">&#xe638;</i>';
            }elseif($i == 2){
                $ico = '<i class="ico i2">&#xe638;</i>';
            }elseif($i == 3){
                $ico = '<i class="ico i3">&#xe638;</i>';
            }else{
                $ico = $i;
            }
            ?>
            <dl>
                <ul class="C">
                    <li><?php echo $ico;?></li>
                    <li><span><?php echo $gradeico_str;?><?php echo $uname;?></span></li>
                    <li><?php echo $price;?></li>
                </ul>
            </dl>
			<?php
		}
		echo "<a class='btn size4 yuan TG_BANGabtn' style='width:66%' onClick=\"page({g:'tg_my_ewm.php',l:'tg_my_ewm'});\">立即推广</a>";
	}?>
	<br>
	<div class="linebox" style="margin-top:5px"><div class="line W50"></div><div class="title S24 BAI B" style="color:#000">3步开启收益</div></div>
	<style>
    .TG_BANG .step{width:90%;margin:30px auto;text-align:left;position:relative}
	.TG_BANG .step i{float:left;width:60px;height:60px;line-height:60px;border-radius:30px;font-size:36px;color:#fff;text-align:center}
	.TG_BANG .step div{float:right;width:(100% - 80px);width:-webkit-calc(100% - 80px)}
	.TG_BANG .step div h3{font-size:16px;margin-top:4px}
	.TG_BANG .step div h4{font-size:14px;color:#999;margin-top:5px;line-height:180%}
	.TG_BANG .step .reg{background-color:#D861B9;}
	.TG_BANG .step .share{background-color:#31C93C}
	.TG_BANG .step .money{background-color:#EE5A4E;}
    </style>
    <div class="step">
    	<i class="ico2 reg">&#xea3a;</i>
        <div>
            <h3>① 成为全民<?php echo $TG_set['tgytitle'];?></h3>
            <h4>填写手机号注册帐号审核资料，激活帐号</h4>
        </div>
    </div>
    <div class="clear"></div>
    <div class="step">
    	<i class="ico share">&#xe607;</i>
        <div>
            <h3>② 推荐朋友</h3>
            <h4>通过微信、QQ、网站等方式推广聚集身边的单身资源注册成为会员</h4>
        </div>
    </div>
    <div class="clear"></div>
    <div class="step">
    	<i class="ico money">&#xe635;</i>
        <div>
            <h3>③ 获得奖励</h3>
            <h4>通过分享推广码注册成为会员获得收益会员充值、开通VIP等也产生收益。同时还可以获得团队成员的奖励。</h4>
        </div>
    </div>

</div>

<script>zeaiLoadBack=['nav'];</script>
<script src="js/TG2.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php require_once ZEAI.'m1/tg_bottom.php';	?>
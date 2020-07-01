<?php
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$currfields = 'grade,if2,sex';
require_once 'my_chkuser.php';
$data_sex = $row['sex'];
$data_if2 = $row['if2'];
$data_grade = $row['grade'];
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/udata.php';
$urole  = json_decode($_ZEAI['urole'],true);
$newarr=array();foreach($urole as $RV){if($RV['f']==1 && $RV['g']>1){$newarr[]=$RV;}else{continue;}}$urole=$newarr;
if(count($urole)<=0){
	$ifpay=false;
}else{
	$ifpay=true;
}
$sj_rmb1 = json_decode($_VIP['sj_rmb1'],true);
$sj_rmb2 = json_decode($_VIP['sj_rmb2'],true);
function getvipauth($grade){
	global $_ZEAI,$_VIP,$navarr;
	$switch          =json_decode($_ZEAI['switch'],true);
	$chat_loveb      = json_decode($_VIP['chat_loveb'],true);
	$chat_daylooknum = json_decode($_VIP['chat_daylooknum'],true);
	$contact_loveb      = json_decode($_VIP['contact_loveb'],true);
	$contact_daylooknum = json_decode($_VIP['contact_daylooknum'],true);
	$loveb_buy          = json_decode($_VIP['loveb_buy'],true);
	$photo_num          = json_decode($_VIP['photo_num'],true);
	$video_num          = json_decode($_VIP['video_num'],true);
	$sj_rmb             = json_decode($_VIP['sj_rmb'],true);
	$chat_duifangfree   = json_decode($_VIP['chat_duifangfree'],true);
	$C = '';
	if($switch['Smode']['g_'.$grade] == 1){
		$s1 = '<h6>专属VIP标识</h6>';
		$s2 = '<h6>线上会员自己联系</h6>';
		$C.= '<dl><dt><i class=ico>&#xe63f;</i></dt><dd>'.$s1.$s2.'</dd></dl>';
		if($chat_duifangfree[$grade]==1 && @in_array('chat',$navarr)){
			$s1 = '<h6>专属对方绿色通道<b>(新)</b></h6>';
			$s2 = '<h6>发信对方免费看</h6>';
			$C.= '<dl><dt><i class=ico>&#xe652;</i></dt><dd>'.$s1.$s2.'</dd></dl>';
		}
		if($loveb_buy[$grade]<1){
			$l = '<h6>在线充值'.$_ZEAI['loveB'].'</h6><h6><b>'.($loveb_buy[$grade]*10).'折</b></h6>';	
			$C.= '<dl><dt><i class=ico>&#xe618;</i></dt><dd>'.$l.'</dd></dl>';
		}
		if($contact_daylooknum[$grade]>0 && @in_array('contact',$navarr)){
			$s11 = '<h6>每天看联系方式<b>'.$contact_daylooknum[$grade].'</b>人</h6>';
			$s22 = ($contact_loveb[$grade]==0)?'<h6>查看免费</h6>':'<h6>联系解锁<b>'.$contact_loveb[$grade].$_ZEAI['loveB'].'</b>/人</h6>';
			$C.= '<dl><dt><i class=ico>&#xe607;</i></dt><dd>'.$s11.$s22.'</dd></dl>';
		}
		if($chat_daylooknum[$grade]>0 && @in_array('chat',$navarr)){
			$s111 = '<h6>看信解锁<b>'.$chat_daylooknum[$grade].'</b>人/天</h6>';
			$s222 = ($chat_loveb[$grade]==0)?'<h6>看信免费</h6>':'<h6>聊天解锁<b>'.$chat_loveb[$grade].$_ZEAI['loveB'].'</b>/人</h6>';
			$C.= '<dl><dt><i class=ico>&#xe623;</i></dt><dd>'.$s111.$s222.'</dd></dl>';
		}
	}else{
		$s1 = '<h6>专属VIP标识</h6>';
		$s2 = '<h6>线下红娘1对1牵线</h6>';
		$C.= '<dl><dt><i class=ico>&#xe621;</i></dt><dd>'.$s1.$s2.'</dd></dl>';
	}
	if($switch['sh']['moddata_'.$grade] == 1 || $switch['sh']['photom_'.$grade] == 1){
		$c1=($switch['sh']['moddata_'.$grade] == 1)?'<h6>修改资料无需审核</h6>':'';
		$c2=($switch['sh']['photom_'.$grade] == 1)?'<h6>上传头像无需审核</h6>':'';
		$C.= '<dl><dt><i class=ico>&#xe65c;</i></dt><dd>'.$c1.$c2.'</dd></dl>';
	}
	if(@in_array('video',$navarr)){
		$c11=($switch['sh']['video_'.$grade] == 1)?'<h6>上传视频无需审核</h6>':'';
		$c22='<h6>视频容量</h6><h6><b>'.$video_num[$grade].'</b>个</h6>';
		$C.= '<dl><dt><i class=ico>&#xe600;</i></dt><dd>'.$c11.$c22.'</dd></dl>';
	}
	$c111=($switch['sh']['photo_'.$grade] == 1)?'<h6>上传相册无需审核</h6>':'';
	$c222='<h6>相册容量</h6><h6><b>'.$photo_num[$grade].'</b>张</h6>';
	$C.= '<dl><dt><i class=ico>&#xe608;</i></dt><dd>'.$c111.$c222.'</dd></dl>';

	return $C;
}

$zeai_cn_menu = '';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>VIP会员 - <?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/p1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/my.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/my_vip.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/p1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <?php if (!$ifpay){?><h1>VIP会员</h1><?php }?>
         <!-- start C -->
        <div class="myRC">
			<div class="vip">
            	<?php if ($ifpay){?>
				   <?php
                   rsort($urole);$maxkey=max($urole);$maxg= $maxkey['g'];$maxif2= $maxkey['if2'];
                    foreach($urole as $RV){
                        $g = $RV['g'];
                        $if2 = $RV['if2'];
                        if($g<=1)continue;
                        $pic_url = $_ZEAI['up2'].'/'.'p/img/grade'.$cook_sex.$g.'.png?1';
                        $price = ($data_sex==2)?$sj_rmb2[$g.'_'.$if2]:$sj_rmb1[$g.'_'.$if2];
                        $maxif2=$if2;
                        ?>
                            <div class="li S5">
                                <em>
                                    <p><img class="gico" src="<?php echo $pic_url;?>"></p>
                                    <h2><?php echo utitle($g); ?> <?php echo get_if2_title($if2);?></h2>
                                    <h3><?php echo round($price/($if2*30),2);?>元/天</h3>
                                    <h4>优惠价 <?php echo $price;?> 元</h4>
                                    <button type="button" class="button" onClick="my_vip_nextbtnFn(<?php echo $g;?>,<?php echo $if2;?>)">立即开通</button>
                                </em>
                                <div class="CC">
                                    <div class="piclinebox"><img src="img/my_vip_Ticoleft.png"><font>尊享特权</font><img src="img/my_vip_Ticoright.png"></div>
                                    <div class="authli">
                                    <?php echo getvipauth($g);?>
                                    </div>
                                </div>
                            </div>            
                    <?php }?>
                <?php }else{ ?> 
                    <?php echo '<br><br>'.nodatatips('在线支付已关闭<br><br><a href="'.Href('kefu').'" class="nohnbtn btn size3 HONG">联系客服</a>');?>
                <?php }?>
        	</div>
        </div>
        <!--提示开始-->
        <div class="clear"></div><br><br>
        <div class="tipsbox">
            <div class="tipst">温馨提示：</div>
            <div class="tipsc">
                ● 系统定义1个月=30天，永久=999个月 计算费用和服务期限，了解更多，请联系客服<br> 
            </div>
        </div><br>
        <!--提示结束-->      
        <!-- end C -->
</div></div></div>
<?php if ($ifpay){?>
<script>
var data_grade=<?php echo $data_grade;?>,data_if2=<?php echo $data_if2;?>,maxg=<?php echo $maxg;?>,maxif2=<?php echo $maxif2;?>;
function my_vip_nextbtnFn(grade,if2){
	if(data_grade==maxg && data_if2==maxif2){
		zeai.msg('土豪，您已经是最顶级会员了，无需要再升级了',{time:3});return false;
	}
	if (data_grade>grade){
		zeai.msg('亲，只能升级不能降级哦');
	}else{
		supdes=ZeaiPC.iframe({url:PCHOST+'/my_pay'+zeai.ajxext+'kind=1&grade='+grade+'&if2='+if2+'&jumpurl=<?php echo $jumpurl;?>',w:500,h:450})
	}
}
</script>
<?php }?>
<?php require_once ZEAI.'p1/bottom.php';?>
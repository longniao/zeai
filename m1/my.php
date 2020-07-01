<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
//$currfields = 'sex,photo_s,photo_f,loveb,money,RZ,if2,sjtime,sign_time,grade,subscribe,myinfobfb,openid,ifWeixinPushInfo,uname,nickname';
require_once ZEAI.'my_chk_u.php';
$row = $db->ROW(__TBL_USER__,"sex,photo_s,photo_f,loveb,money,RZ,if2,sjtime,sign_time,grade,subscribe,myinfobfb,openid,ifWeixinPushInfo,uname,nickname,weixin,mob","id=".$cook_uid,"name");
if($submitok=='ajax_tipnum_tb'){
	$tipnum1 = $db->COUNT(__TBL_MSG__,"new=1 AND ifdel=0 AND uid=".$cook_uid);
	$tipnum2 = $db->COUNT(__TBL_TIP__,"new=1 AND uid=".$cook_uid);
	$tipnum = $tipnum1+$tipnum2;
	$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",tipnum=".$tipnum.",chatHiContact_iftips=1 WHERE id=".$cook_uid);
	exit;
}elseif($submitok=='ajax_getmyinfobfb'){
	$data_nickname  = dataIO($row['nickname'],'out');
	$data_weixin    = dataIO($row['weixin'],'out');
	$data_mob       = dataIO($row['mob'],'out');
	$data_myinfobfb = intval($row['myinfobfb']);
	if(!ifmob($data_mob))json_exit(array('flag'=>0,'msg'=>'请输入手机号码（*必填）','obj'=>'mob'));
	if(str_len($data_weixin)<4)json_exit(array('flag'=>0,'msg'=>'请输入微信号（*必填）','obj'=>'weixin'));
	if(str_len($data_nickname)<2)json_exit(array('flag'=>0,'msg'=>'请输入昵称2~20字符（*必填）','obj'=>'nickname'));
	json_exit(array('flag'=>1,'msg'=>'保存成功','myinfobfb'=>intval($row['myinfobfb'])));
exit;}
$switch = json_decode($_ZEAI['switch'],true);
$if_other_login_subscribe = openid_chk();
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_wxgzh.php';
require_once ZEAI.'cache/config_vip.php';
$TG_set = json_decode($_REG['TG_set'],true);
$viewlist = json_decode($_VIP['viewlist'],true);
$data_sex = $row['sex'];
$data_photo_s = $row['photo_s'];
$data_grade   = intval($row['grade']);
$data_openid  = $row['openid'];
$data_subscribe= $row['subscribe'];
$cook_nickname= dataIO($row['nickname'],'out');
$cook_uname= dataIO($row['uname'],'out');
setcookie("cook_grade",$data_grade,time()+720000,"/",$_ZEAI['CookDomain']);
setcookie("cook_photo_s",$data_photo_s,time()+720000,"/",$_ZEAI['CookDomain']);
//
$nickname      = (!empty($cook_nickname))?$cook_nickname:$cook_uname;
$uname      = (!empty($cook_uname))?$cook_uname:'';
$photo_s       = $data_photo_s;
$photo_f       = $row['photo_f'];
$photo_m       = getpath_smb($photo_s,'m');
$data_loveb    = $row['loveb'];
$data_money    = $row['money'];
$RZ            = $row['RZ'];$RZarr=explode(',',$RZ);
$if2           = $row['if2'];
$sjtime        = $row['sjtime'];
$data_myinfobfb= $row['myinfobfb'];
$ifWeixinPushInfo = $row['ifWeixinPushInfo'];
if($data_sex!=$cook_sex){
	setcookie("cook_sex",$data_sex,time()+720000,"/",$_ZEAI['CookDomain']);
}
//$data_bfbstr=($data_myinfobfb>=100)?'<font>'.$data_myinfobfb.'</font>':$data_myinfobfb;
$data_bfbstr=($data_myinfobfb>=100)?'<font id="my_bfbbar">'.$data_myinfobfb.'</font>':'<span id="my_bfbbar">'.$data_myinfobfb.'</span>';
//
$photo_m_url = (!empty($photo_s ))?'<img src="'.$_ZEAI['up2'].'/'.$photo_m.'">':'<i class="i'.$cook_sex.'"></i>';
if ($if2 > 0){
	$timestr1 = get_if2_title($if2);
}
if (!empty($sjtime)){
	$d1  = ADDTIME;
	$d2  = $sjtime + $if2*30*86400;
	$ddiff = $d2-$d1;
	if ($ddiff < 0 && $if2 != 999){
		$timestr2 = ',<font class="Caaa">已过期</font>';
		$db->query("UPDATE ".__TBL_USER__." SET grade=1,sjtime=0,if2=0 WHERE id=".$cook_uid);
		$url = Href('vip');
		//站内消息
		$C = $cook_nickname.'您好，您的VIP会员已过期~~ 为了避免你与另一半擦肩而过，建议你尽快充值和升级　<a href="'.$url.'" class=aQING>立即升级</a>';
		$db->SendTip($uid,'您的VIP资格已过期，请速续费',dataIO($C,'in'),'sys');
		if (!empty($data_openid)){
			//微信客服通知
			$content = urlencode($C);
			$ret = @wx_kf_sent($data_openid,$C,'text');
			$ret = json_decode($ret);
			//微信模版通知
			if ($ret->errmsg != 'ok'){
				$first     = urlencode($cook_nickname.',你的'.utitle($data_grade).'已过期！');
				$keyword1  = urlencode('降级为最低等级');
				$keyword3  = urlencode('系统自动执行');
				$remark    = urlencode('请尽快进入升级,以免数据丢失');
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('vip')));
			}
		}
	} else {
		$tmpday   = intval($ddiff/86400);
		$timestr2 = ',还剩<font class="Cf00">'.$tmpday.'</font>天';
	}
	$timestr2 = ($if2 >= 999)?'':$timestr2;
}
$timestr = (!empty($timestr1))?'('.$timestr1.$timestr2.')':'';
$sign_time  = $row['sign_time'];
if($sign_time==0){
	$signflag = 1;
}elseif($sign_time>0){
	$old_time = YmdHis($sign_time,"Ymd");
	$now_time = YmdHis(ADDTIME,"Ymd");
	if($now_time>$old_time){
		$signflag = 1;
	}else{
		$signflag = 0;
	}
}else{$signflag = 1;}
if(ifint($cook_uid)){
	$gznum = $db->COUNT(__TBL_GZ__,"flag=1 AND senduid=".$cook_uid);
	$fsnum = $db->COUNT(__TBL_GZ__,"flag=1 AND uid=".$cook_uid);
}
//$clicknum = $db->COUNT(__TBL_CLICKHISTORY__,"new=1 AND uid=".$cook_uid);
//
$headertitle = '我的-';$nav = 'my';
require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug: false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','startRecord','stopRecord','onVoiceRecordEnd','uploadVoice','downloadVoice','playVoice','pauseVoice']
	});
	</script>
<?php }?>
<link href="m1/css/my_index.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php if($_ZEAI['mob_mbkind']==3){?>
<link href="m3/css/my_index.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php }?>
<main id='main' class='main huadong'>
    <div class="my-topbox">
        <div class="C">
            <em id="supdes">
                <span id="my_photo_s"><?php echo $photo_m_url; ?></span>
                <?php if ($photo_f != 1 && !empty($photo_s)){ ?><div class="photo_f">审核中</div><?php }?>
                <i class="ico photoico">&#xe608;</i>
            </em>
            <h2 onclick="page({g:'m1/my_info.php',l:'my_info'})"><?php echo $nickname; ?><font class="S12"> (UID:<?php echo $cook_uid; ?>)</font></h2>
            <a class="sign" id="sign"><i class="ico">&#xe602;</i><h3 id="signstr"><?php if ($signflag == 1){echo '今日<br>签到';}else{echo '已经<br>签到';}?></h3></a>
            <a class="myinfo" onclick="page({g:'m1/my_info.php',l:'my_info'})"><h3>资料<br><?php echo $data_bfbstr;?>%</h3><i class="ico">&#xe601;</i></a>
            <div onclick="page({g:'m1/my_info.php?a=cert',l:'my_info'})"><?php echo RZ_html($RZ,'m','allcolor');?></div>
			<?php if ($signflag == 1){?><div class="signbox" id="signbox"><div class="sign_gif rotate"></div></div><?php }?>
        </div>
        <?php if($_ZEAI['mob_mbkind']!=3){?>
        <div class="topnav">
            <dl onclick="page({g:'m1/my_follow.php',l:'my_follow'})"><dt class="ico">&#xe617;</dt><dd>关注<b id="my_gz"><?php echo $gznum;?></b></dd></dl>
            <dl onclick="page({g:'m1/my_fans.php',l:'my_fans'})"><dt class="ico">&#xe603;</dt><dd>粉丝<b id="my_fs"><?php echo $fsnum;?></b></dd></dl>
            <dl onclick="page({g:'m1/my_loveb.php',l:'my_loveb'})"><dt class="ico">&#xe618;</dt><dd><?php echo $_ZEAI['loveB'];?><b id="my_loveb_num"><?php echo $data_loveb;?></b></dd></dl>
            <dl onclick="page({g:'m1/my_money.php',l:'my_money'})"><dt class="ico">&#xe61a;</dt><dd>余额<b id="my-money"><?php echo $data_money;?></b></dd></dl>
        </div>
        <?php }?>
    </div>
    <div class="clear"></div>
    
    <?php if($_ZEAI['mob_mbkind']==3){?>
        <div class="topnav">
            <dl onclick="page({g:'m1/my_follow.php',l:'my_follow'})"><dt class="ico">&#xe617;</dt><dd><b id="my_gz"><?php echo $gznum;?></b>关注</dd></dl>
            <dl onclick="page({g:'m1/my_fans.php',l:'my_fans'})"><dt class="ico">&#xe603;</dt><dd><b id="my_fs"><?php echo $fsnum;?></b>粉丝</dd></dl>
            <dl onclick="page({g:'m1/my_loveb.php',l:'my_loveb'})"><dt class="ico">&#xe618;</dt><dd><b id="my_loveb_num"><?php echo $data_loveb;?></b><?php echo $_ZEAI['loveB'];?></dd></dl>
            <dl onclick="page({g:'m1/my_money.php',l:'my_money'})"><dt class="ico">&#xe61a;</dt><dd><b id="my-money"><?php echo $data_money;?></b>余额</dd></dl>
        </div>
    <?php }?>    
    
    <div class="my_Iviewuser" onclick="<?php if ($viewlist[$data_grade]!=1){?>ZeaiM.page.load('m1/my_vip'+zeai.extname,ZEAI_MAIN,'my_vip');<?php }else{ ?>page({g:'m1/my_viewuser.php',l:'my_viewuser'});<?php }?>">
    	<div class="ivubox">
		<?php 
		$vwnum = $db->COUNT(__TBL_CLICKHISTORY__,"uid=".$cook_uid);
		$rt=$db->query("SELECT a.senduid,a.new,a.addtime,b.sex,b.photo_s,b.photo_f FROM ".__TBL_CLICKHISTORY__." a,".__TBL_USER__." b WHERE a.senduid=b.id AND a.uid=".$cook_uid." ORDER BY a.addtime DESC LIMIT 8");
		$total = $db->num_rows($rt);
		if ($total == 0) {
			echo '<i class="ico">&#xe61f;</i>';
		} else {
			$viewblurClass = ($viewlist[$data_grade]!=1)?' class="blur"':'';
			for($ii=1;$ii<=$total;$ii++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$senduid = $rows['senduid'];
				$sex     = $rows['sex'];
				$new     = $rows['new'];
				$new_str = ($new == 1)?'<b></b>':'';
				$photo_s  = $rows['photo_s'];
				$photo_f  = $rows['photo_f'];
				$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_s'.$sex.'.png';
				echo '<span><img src="'.$photo_s_url.'"'.$viewblurClass.'></span>';
			}
		}
		if ($viewlist[$data_grade]!=1){
			$urole = json_decode($_ZEAI['urole']);
			if (count($urole) >= 1 && is_array($urole)){
				foreach ($urole as $uc) {
					$cgrade = $uc->g;
					if($viewlist[$cgrade] == 1){
						$maxgradeT=utitle($cgrade);
						break;
					}
				}
			}
			$maxgradeT = ($cook_grade==1)?'点击开通VIP查看':'点击升级'.$maxgradeT.'查看';
		}
        ?>
        </div>
    	<h5>最近有<b><?php echo $vwnum;?></b>人看过你<?php if ($viewlist[$data_grade]!=1){?>，<font style="color:#FF6F6F"><?php echo $maxgradeT;?></font><?php }?></h5>
    </div>

    <div class="my-menu">
        <ul>
            <li onclick="ZeaiM.page.load('m1/my_vip'+zeai.extname,ZEAI_MAIN,'my_vip');"><i class="ico vip">&#xe63f;</i><h2>开通VIP</h2><span id="my-vipinfo"><?php echo uicon($cook_sex.$data_grade).'<font class="middle">'.utitle($data_grade).$timestr.'</font>'; ?></span></li>
            <li onclick="page({g:'m1/my_viewuser.php',l:'my_viewuser'})" style="display:none"><i class="ico viewu">&#xe67a;</i><h2>谁看过我</h2></li>
            <?php if(@in_array('xqcard',$navarr)){?><li onclick="ZeaiM.page.load('m1/my_card.php',ZEAI_MAIN,'my_card');uid=<?php echo $cook_uid;?>;"><i class="ico2 xqk">&#xe64f;</i><h2>我的相亲卡</h2></li><?php }?>
            <?php if(@in_array('tg',$navarr)){?><li onclick="zeai.openurl('<?php echo HOST;?>/m1/tg_my.php');"><i class="ico tg">&#xe60f;</i><h2><?php echo $TG_set['navtitle'];?></h2><span>分享身边单身资源获得奖励</span></li><?php }?>
            <?php if(@in_array('hn',$navarr)){?><li style="display:none"><i class="ico hongniang">&#xe621;</i><h2>我的红娘</h2></li><?php }?>
            <?php if(@in_array('gift',$navarr)){?><li onclick="page({g:'m1/my_gift.php',l:'my_gift'})"><i class="ico gift">&#xe624;</i><h2>我的礼物</h2></li><?php }?>
        </ul>
        <ul>
            <li onclick="page({g:'m1/my_info.php',l:'my_info'})"><i class="ico data">&#xe61c;</i><h2>个人资料</h2></li>
            <li onclick="page({g:'m1/my_info.php?a=cert',l:'my_info'})"><i class="ico rz">&#xe613;</i><h2>诚信认证</h2><b style="left:130px"></b></li>
            <?php if(@in_array('video',$navarr)){?><li onclick="page({g:'m1/my_info.php?a=video',l:'my_info'})"><i class="ico video">&#xe77d;</i><h2>我的视频</h2></li><?php }?>
            <li onclick="page({g:'m1/my_info.php?a=photo',l:'my_info'})"><i class="ico photo">&#xe62a;</i><h2>我的相册</h2></li>
        </ul>
		<?php if(@in_array('dating',$navarr) || @in_array('hb',$navarr) || @in_array('trend',$navarr) || @in_array('group',$navarr)){?>
        <ul>
			<?php if(@in_array('trend',$navarr)){?><li onclick="zeai.openurl('./?z=trend&submitok=my')"><i class="ico trend">&#xe633;</i><h2>我的交友圈</h2></li><?php }?>
            <?php if(@in_array('hb',$navarr)){?><li onclick="zeai.openurl('m1/hongbao/my/hongbao.php')"><i class="ico hongbao">&#xe64c;</i><h2>我的红包</h2></li><?php }?>
            <?php if(@in_array('dating',$navarr)){?><li onclick="zeai.openurl('./?z=dating&submitok=my')"><i class="ico dating">&#xe653;</i><h2>我的约会</h2></li><?php }?>
            <?php if(@in_array('group',$navarr)){?><li onclick="zeai.openurl('m1/group/my/group.php')"><i class="ico group">&#xe6f6;</i><h2>我的圈子</h2></li><?php }?>
        </ul>
        <?php }?>
        <ul>
            <li onclick="page({g:'m1/about.php?t=contact',l:'about_contact'})"><i class="ico kefu">&#xe861;</i><h2>客服中心</h2></li>
            <li onclick="page({g:'m1/about.php?t=us',l:'about_us'})"><i class="ico about">&#xe610;</i><h2>关于我们</h2></li>
            <li onclick="page({g:'m1/my_set.php',l:'my_set'})"><i class="ico set">&#xe6e8;</i><h2>设置</h2><b style="left:100px"></b></li>
        </ul>
    </div>
    <button type="button" onClick="zeai.openurl('<?php echo HOST;?>/loginout.php')" class="btn size4 <?php echo ($_ZEAI['mob_mbkind']==3)?'HONG2':'HONG';?> W90_">退出当前帐号</button>
	<?php
	require_once ZEAI.'m1/footer.php';
	if (is_weixin() && !empty($_GZH['wx_gzh_ewm']) && $data_subscribe!=1){
		@wx_endurl('您刚刚浏览的页面【我的】',HOST.'/?z=my');
		?>
        
		<?php
		if (ifint($if_other_login_subscribe)){
			?>
			<script>zeai.msg('当前微信号已被其它帐号（UID：<?php echo $if_other_login_subscribe;?>）绑定，请退出使用微信登录',{time:5});/*setScrollTop(0);*/o('main').scrollTop=1000;</script>
			<?php
		}else{?>
            <div id="subscribe_box" class="my-subscribe_box"><img src="<?php echo $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm']; ?>?<?php echo $_ZEAI['cache_str'];?>"><h3>长按二维码关注公众号<br>获取帐号消息通知等全功能体验<br>关注成功之后将不再弹出</h3></div>
            <script>setTimeout(function(){ZeaiM.div({obj:subscribe_box,w:260,h:300});},500);</script>
			<?php
		}?>
        
    <?php }else{
		//蹦图提醒
		$bounce=json_decode($_ZEAI['bounce'],true);
		if($bounce['flag']['vipdatarz'] == 1 && (!empty($cook_openid) || ifint($cook_uid))){
			$bouncev = '';
			$bounceTip = 'cook_my_bounce'.YmdHis(ADDTIME,'d');
//			if(empty($bouncev) && $data_grade<=1 && $_COOKIE[$bounceTip.'my_vip'] != 'my_vip'){
//				$bouncev='my_vip';$url='m1/my_vip.php';$pageid='my_vip';$picurl=$_ZEAI['up2']."/".$bounce['vip_picurl'];
//			}
			if(empty($bouncev) && ($data_myinfobfb<60 || empty($data_photo_s)  ) && $_COOKIE[$bounceTip.'my_info'] != 'my_info' && !empty($bounce['my_info_picurl']) ){
				$bouncev='my_info';$url='m1/my_info.php';$pageid='my_info';$picurl=$_ZEAI['up2']."/".$bounce['my_info_picurl'];
			}
			if(empty($bouncev) && empty($RZ) && $_COOKIE[$bounceTip.'my_rz'] != 'my_rz'  && !empty($bounce['rz_picurl']) ){
				$bouncev='my_rz';$url='m1/my_info.php?a=cert';$pageid='my_info';$picurl=$_ZEAI['up2']."/".$bounce['rz_picurl'];
			}
			if(!empty($bouncev)){
				$bounceTip = $bounceTip.$bouncev;
				if($_COOKIE[$bounceTip] != $bouncev){
					setcookie($bounceTip,$bouncev,null,"/",$_ZEAI['CookDomain']);
					?>
					<script>var my_divclose;setTimeout(function(){my_divclose=ZeaiM.div_pic({fobj:main,obj:my_bounce_box,w:320,h:360});},1500);</script>
					<div id="my_bounce_box" class="bounce_box bounce"><img style="width:100%;display:block" src="<?php echo $picurl;?>" onClick="my_divclose.click();if(zeai.empty(o('my_info')) && zeai.empty(o('my_rz'))  )page({g:'<?php echo $url;?>',l:'<?php echo $pageid;?>'});"></div>
					<?php
				}
			}
		}
	}?>
</main>
<div id="mask_sign" class='alpha0_100'><div class="gif rotate" id="mask_gif"></div></div>
<div id="signokbox" class="scale">
	<div class="signok">
    	<h1>好手气</h1>
    	<div class="hr"></div>
        <h4>恭喜你获得<font id="randloveb">0</font>个<?php echo $_ZEAI['loveB'];?></h4>
    </div>
</div>
<div id="areabox" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
<div id="areabox2" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
<div id="mate_areaidbox" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
<script>
<?php
$ifforce = false;
if ($switch['force']['data'] == 1 && $data_myinfobfb<50){
	$e = 'my_info';
	echo 'zeai.msg("请先完善资料");';
	$ifforce = true;
}
if ($switch['force']['photom'] == 1 && empty($data_photo_s) && !$ifforce){
	$e = 'my_info';
	echo 'zeai.msg("请先上传形象照片");';
	$ifforce = true;
}
if ($switch['force']['mob'] == 1 && !in_array('mob',$RZarr)  && !$ifforce ){
	$e = 'my_info';
	$a = 'cert';
	$i = 'mob';
	echo 'zeai.msg("请先认证手机");';
	$ifforce = true;
}
if ($switch['force']['cert'] == 1 && !in_array('identity',$RZarr)  && !$ifforce ){
	$e = 'my_info';
	$a = 'cert';
	echo 'zeai.msg("请先身份认证");';
}
?>
zeaiLoadBack=['nav'];
<?php if (!empty($e)){?>
ZeaiM.page.load('m1/<?php echo $e;?>'+zeai.ajxext+'a=<?php echo $a;?>&i=<?php echo $i;?>&href=<?php echo $href;?>',ZEAI_MAIN,'<?php echo $e;?>');
<?php }?>
var browser='<?php echo (is_weixin())?'wx':'h5';?>',upMaxMB = <?php echo $_UP['upMaxMB']; ?>,uid=<?php echo $cook_uid;?>;
supdes.onclick=function(){page({g:'m1/u'+zeai.ajxext+'ifmy=1&uid=<?php echo $cook_uid; ?>',l:'u'})}
o('sign').onclick = function(){sign('my'+zeai.extname);}
o('mask_sign').onclick = function(){ZEAI_signclose();}
o('signokbox').onclick = function(){ZEAI_signclose();}
function ZEAI_signclose(){zeai.showSwitch('mask_sign,signokbox');}
function sign(url){
	zeai.ajax({'url':'m1/my_vip'+zeai.extname,'data':{submitok:'ajax_sign'}},function(e){rs=zeai.jsoneval(e);
		if (rs.flag==1){
			zeai.showSwitch('mask_sign,signokbox,mask_gif');
			randloveb.html(rs.num);
			if(!zeai.empty(o('my_loveb_num')))o('my_loveb_num').html(parseInt(o('my_loveb_num').innerHTML)+parseInt(rs.num));
			signstr.html('已经<br>签到');
		}else{zeai.msg(rs.msg);}
		if (!zeai.empty(o('signbox')))o('signbox').remove();
	});	
}
setTimeout(function(){	zeai.ajax({loading:0,url:'m1/my'+zeai.ajxext+'submitok=ajax_tipnum_tb'});},1000);
</script>
<?php
if ($ifWeixinPushInfo == 1)wx_sent_kf_PushInfo($data_openid);
if($_ZEAI['mob_mbkind']==3){
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}
$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME." WHERE id=".$cook_uid);
ob_end_flush();
?>
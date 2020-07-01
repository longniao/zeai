<?php
require_once '../sub/init.php';
//$currfields  = 'photo_s,photo_f,if2,sjtime,sign_time,grade,sex,openid,money';

$$rtn='json';$chk_u_jumpurl=$jumpurl;
$currfields = 'sex,grade,if2';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/udata.php';
$data_sex = $row['sex'];
$data_if2 = $row['if2'];
$data_grade = intval($row['grade']);
//
$switch          =json_decode($_ZEAI['switch'],true);
$urole           = json_decode($_ZEAI['urole'],true);
$chat_loveb      = json_decode($_VIP['chat_loveb'],true);
$chat_daylooknum = json_decode($_VIP['chat_daylooknum'],true);
$contact_loveb      = json_decode($_VIP['contact_loveb'],true);
$contact_daylooknum = json_decode($_VIP['contact_daylooknum'],true);
$loveb_buy          = json_decode($_VIP['loveb_buy'],true);
$photo_num          = json_decode($_VIP['photo_num'],true);
$video_num          = json_decode($_VIP['video_num'],true);
$sj_rmb1            = json_decode($_VIP['sj_rmb1'],true);
$sj_rmb2            = json_decode($_VIP['sj_rmb2'],true);
$vipC               = json_decode($_VIP['vipC'],true);
$chat_duifangfree   = json_decode($_VIP['chat_duifangfree'],true);

$curpage = 'my_vip';
if ($submitok=='ajax_sign'){
	$row = $db->ROW(__TBL_USER__,"sign_time,openid,subscribe","id=".$cook_uid,"name");
	$sign_time  = $row['sign_time'];
	$data_openid= $row['openid'];
	$data_subscribe= $row['subscribe'];
	$old_time = YmdHis($sign_time,"Ymd");
	$now_time = YmdHis(ADDTIME,"Ymd");
	if($now_time==$old_time)json_exit(array('flag'=>0,'msg'=>'~您今天已领过了~　请明天再来'));
	if($now_time>$old_time || $sign_time==0){
		$endip     = getip();
		$arry      = json_decode($_VIP['sign_numlist'],true);;
		$randloveb = $arry[array_rand($arry,1)];
		$db->query("UPDATE ".__TBL_USER__." SET loveb=loveb+$randloveb,sign_time=".ADDTIME.",logincount=logincount+1,endtime=".ADDTIME.",endip='$endip' WHERE id=".$cook_uid);
		$db->AddLovebRmbList($cook_uid,'每日签到',$randloveb,'loveb',7);		
		//站内消息
		$C = $cook_nickname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！　<a href='.Href('loveb').' class=aHUI>查看详情</a>';
		$db->SendTip($cook_uid,'每日签到',dataIO($C,'in'),'sys');
		//爱豆到账提醒
		if (!empty($data_openid) && $data_subscribe==1){
			$F = urlencode($cook_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
			$C = urlencode('每日签到，恭喜你，再接再励哦~~');
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$randloveb.'&first='.$F.'&content='.$C.'&url='.HOST.'/?z=my&e=my_loveb');
		}
		$chkflag = 1;
	}else{
		$chkflag = 0;
	}
	json_exit(array('flag'=>$chkflag,'num'=>$randloveb,'msg'=>'~您今天已领过了~　请明天再来'));
}elseif($submitok=='ajax_getvipauth'){
	if(!ifint($grade) || $grade>10 || $grade<1)json_exit(array('flag'=>0,'msg'=>'请选择要升级的会员'));
	$C = '';
	if($switch['Smode']['g_'.$grade] == 1){
		$s1 = '<h6>专属VIP标识</h6>';
		$s2 = '<h6>线上会员自主联系</h6>';
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
			$s111 = '<h6>每天看信/聊天<b>'.$chat_daylooknum[$grade].'</b>人</h6>';
			$s222 = ($chat_loveb[$grade]==0)?'<h6>看信免费</h6>':'<h6>聊天解锁<b>'.$chat_loveb[$grade].$_ZEAI['loveB'].'</b>/人</h6>';
			$C.= '<dl><dt><i class=ico>&#xe623;</i></dt><dd>'.$s111.$s222.'</dd></dl>';
		}
	}else{
		$s1 = '<h6>专属VIP标识</h6>';
		$s2 = '<h6>线下红娘1对1牵线</h6>';
		$C.= '<dl><dt><i class=ico>&#xe621;</i></dt><dd>'.$s1.$s2.'</dd></dl>';
	}
	if($switch['sh']['moddata_'.$grade] == 1 || $switch['sh']['photom_'.$grade] == 1){
		$c1=($switch['sh']['moddata_'.$grade] == 1)?'<h6>修改资料无需审核</h6>':'<br>';
		$c2=($switch['sh']['photom_'.$grade] == 1)?'<h6>上传头像无需审核</h6>':'<br>';
		$C.= '<dl><dt><i class=ico>&#xe65c;</i></dt><dd>'.$c1.$c2.'</dd></dl>';
	}
	if(@in_array('video',$navarr)){
		$c11=($switch['sh']['video_'.$grade] == 1)?'<h6>上传视频无需审核</h6>':'<br>';
		$c22='<h6>视频容量<b>'.$video_num[$grade].'</b>个</h6>';
		$C.= '<dl><dt><i class=ico>&#xe600;</i></dt><dd>'.$c11.$c22.'</dd></dl>';
	}
	$c111=($switch['sh']['photo_'.$grade] == 1)?'<h6>上传相册无需审核</h6>':'<br>';
	$c222='<h6>相册容量<b>'.$photo_num[$grade].'</b>张</h6>';
	$C.= '<dl><dt><i class=ico>&#xe608;</i></dt><dd>'.$c111.$c222.'</dd></dl>';
	$vipC = $vipC[$grade];
	$vipC=str_replace("%","％",$vipC);
	json_exit(array('flag'=>1,'C'=>$C,'vipC'=>$vipC));
}
$vipC     = json_decode($_VIP['vipC'],true);
$safetips = dataIO($_VIP['safetips'],'out');
//
$mini_backT = '我的';
$t = (ifint($t,'1-4','1'))?$t:1;

$newarr=array();foreach($urole as $RV){if($RV['f']==1 && $RV['g']>1){$newarr[]=$RV;}else{continue;}}
$vipclosestr='在线支付已关闭';
if(count($newarr)<=0){
	$ifpay = false;
}else{
	$ifpay=true;
	$urole=$newarr;
	rsort($urole);$maxkey=max($urole);$maxg= $maxkey['g'];$maxif2= $maxkey['if2'];
	if($data_grade>=$maxg && $data_if2>=$maxif2){$ifpay = false;$vipclosestr='土豪，您已经是最顶级VIP，无需升级！';}
}
?>
<link href="<?php echo HOST;?>/m1/css/my_vip.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<i class='ico goback gobackVip' id='ZEAIGOBACK-my_vip'>&#xe602;</i>
<div class="submain my_vip">
    <div class="vipheader"><img src="<?php echo HOST;?>/m1/img/zeai_vip.jpg?<?php echo $_ZEAI['cache_str'];?>zeai_cnV6_7"></div>
    <?php if ($ifpay){?>
    <div class="viplist">
    	<div class="clear"></div>
        <h1><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoleft.png">购买VIP服务<img src="<?php echo HOST;?>/m1/img/my_vip_Ticoright.png"></h1>
        <?php
        foreach($urole as $RV){
            $g = $RV['g'];$f = $RV['f'];
            $if2 = $RV['if2'];
            if($g<=1 || $f!=1)continue;
            if($cook_grade > $g )continue;
            $pic_url = $_ZEAI['up2'].'/'.'p/img/grade'.$cook_sex.$g.'.png?1';
			$price = ($data_sex==2)?$sj_rmb2[$g.'_'.$if2]:$sj_rmb1[$g.'_'.$if2];
            ?>
            <table class="table">
              <tr>
                <td width="50" align="left" valign="middle"><img class="gico" src="<?php echo $pic_url;?>"></td>
                <td align="left"><b><?php echo utitle($g); ?></b><em class="C999"><?php echo get_if2_title($if2);?>　日均<?php echo round($price/($if2*30),2);?>元 </em></td>
                <td width="80" align="left" class="vippricebox <?php echo ($maxg == $g)?' on':'off';?>" id="vipprice<?php echo $g;?>"><?php echo '<font class="S12">￥</font>'.$price;?></td>
                <td width="40" align="left"><input onClick="vipFn(<?php echo $g;?>,<?php echo $if2;?>,encodeURIComponent('<?php echo utitle($g); ?>'))" type="checkbox" id="vip<?php echo $g;?>" name="vip<?php echo $g;?>" class="checkskin vipli"<?php echo ($maxg == $g)?' checked':'';?>><label for="vip<?php echo $g;?>" class="checkskin-label"><i class="i3"></i></label> </td>
            </tr>
            </table>
        <?php }?>
        <div class="clear"></div>
        <button type="button" class="btn size4 HONG W100_ B" id="vipbtn">立即开通</button><br>
    </div>
    <div class="viplist">
    	<h1><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoleft.png"><font id="gradename"><?php echo utitle($maxg);?>尊享特权</font><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoright.png"></h1>
        <div class="CC auth">
        	<ul id="vipauth"></ul>
    		<div class="clear"></div>
    	</div>
    </div>
    <div class="viplist">
    	<h1><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoleft.png"><font id="gradename2"><?php echo utitle($maxg);?>套餐详情</font><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoright.png"></h1>
        <div class="CC" id="vipC"></div>
    </div>
    <?php }?>
    <div class="clear"></div>
    <div class="viplist">
    	<h1><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoleft.png"><font>联系客服</font><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoright.png"></h1>
        <div class="vipkefu">
        <?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
        <h5>
        <?php if ($ifpay){?>
            <div style="text-align:left">系统定义1个月=30天，永久=999个月 计算费用和服务期限，了解更多，请联系客服</div>
        <?php }else{ ?>
        	<div style="color:#333;font-size:16px;margin:20px auto"><?php echo $vipclosestr;?></div>
        <?php }?>
        <?php if (!empty($kf_tel)){?><br><center class="S16 C666" style="margin-bottom:5px">电话：<a href="tel:<?php echo $kf_tel;?>" class="C666"><?php echo $kf_tel;?></a></center><?php }?>
        <?php if (!empty($kf_mob)){?><center class="S16 C666">手机：<a href="tel:<?php echo $kf_mob;?>" class="C666"><?php echo $kf_mob;?></a></center><?php }?>
        <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码或扫码加客服微信</font><?php }?>
        </h5>
        </div>
    </div>
    <div class="viplist">
    	<h1><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoleft.png"><font>安全交友提示</font><img src="<?php echo HOST;?>/m1/img/my_vip_Ticoright.png"></h1>
    	<div class="CC"><?php echo $safetips;?></div>
    </div>
    <div class="clear"></div>
<br><br>
</div>
<?php if ($ifpay){?>
<input type="hidden" name="grade" id="grade" value="<?php echo $maxg;?>">
<input type="hidden" name="if2" id="if2" value="<?php echo $maxif2;?>">
<input type="button" value="立即升级" id="VipBtn1">
<script src="<?php echo HOST;?>/m1/js/my_vip.js"></script>
<script>
var maxg=<?php echo $maxg;?>;
vipbtn.onclick=function(){
	if (!zeai.ifint(grade.value) || !zeai.ifint(if2.value)){zeai.msg('请选择VIP会员类型');return false;}
	ZeaiM.page.load({url:HOST+'/m1/my_pay.php',data:{kind:1,grade:grade.value,if2:if2.value,jumpurl:'<?php echo $jumpurl;?>'}},'<?php echo $curpage;?>','my_pay');
}
setTimeout(function(){getvipauth(<?php echo $maxg;?>);},600);
<?php }?>
setTimeout(function(){	o('ZEAIGOBACK-my_vip').style.left = '15px';},500);
</script>
<?php
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$currfields = "uname,nickname,sex,grade,photo_s,photo_f,loveb,money,if2,sjtime,sign_time,refresh_time,click,RZ,dataflag,logincount,myinfobfb,mate_age1,mate_age2,mate_areaid,mate_areatitle,openid,subscribe,regkind,mate_age1,mate_age2,mate_areaid,pay,birthday,heigh";
require_once 'my_chkuser.php';
$data_openid= $row['openid'];
$data_subscribe= $row['subscribe'];
require_once ZEAI.'cache/config_vip.php';
if($submitok=='ajax_tipnum_tb'){
	$tipnum1 = $db->COUNT(__TBL_MSG__,"new=1 AND ifdel=0 AND uid=".$cook_uid);
	$tipnum2 = $db->COUNT(__TBL_TIP__,"new=1 AND uid=".$cook_uid);
	$tipnum = $tipnum1+$tipnum2;
	$db->query("UPDATE ".__TBL_USER__." SET tipnum=".$tipnum." WHERE id=".$cook_uid);exit;
}elseif($submitok=='ajax_sign'){
	$sign_time  = $row['sign_time'];
	$old_time = YmdHis($sign_time,"Ymd");
	$now_time = YmdHis(ADDTIME,"Ymd");
	if($now_time>$old_time || $sign_time==0){
		$endip     = getip();
		$arry      = json_decode($_VIP['sign_numlist'],true);;
		$randloveb = $arry[array_rand($arry,1)];
		$db->query("UPDATE ".__TBL_USER__." SET loveb=loveb+$randloveb,sign_time=".ADDTIME.",logincount=logincount+1,endtime=".ADDTIME.",endip='$endip' WHERE id=".$cook_uid);
		$db->AddLovebRmbList($cook_uid,'每日签到',$randloveb,'loveb',7);		
		//站内消息
		$C = $cook_nickname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！　<a href='.Href('loveb').' class=aQING>查看详情</a>';
		$db->SendTip($cook_uid,'每日签到',dataIO($C,'in'),'sys');
		//爱豆到账提醒
		if (!empty($data_openid) && $data_subscribe==1){
			$F = urlencode($cook_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
			$C = urlencode('每日签到，恭喜你，再接再励哦~~');
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$randloveb.'&first='.$F.'&content='.$C.'&url='.mHref('loveb'));
		}
		$chkflag = 1;
	}else{
		$chkflag = 0;
	}
	json_exit(array('flag'=>$chkflag,'num'=>$randloveb,'msg'=>'~您今天已领过了~　请明天再来'));
}
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_up.php';
$up2 = $_ZEAI['up2']."/";
//
$sex         = $row['sex'];
$mate_age1   = $row['mate_age1'];
$mate_age2   = $row['mate_age2'];
$mate_areaid = $row['mate_areaid'];
$mate_areatitle = $row['mate_areatitle'];
$mate_areaid = explode(',',$mate_areaid);
if (count($mate_areaid) > 0){
	$m1 = $mate_areaid[0];
	$m2 = $mate_areaid[1];
	$m3 = $mate_areaid[2];
}
$SQL = "";
$SQL .= ($sex == 2)?" AND sex=1 ":" AND sex=2 ";
$areaid = '';
if (ifint($m1) && ifint($m2) && ifint($m3)){
	$areaid = $m1.','.$m2.','.$m3;
}elseif(ifint($m1) && ifint($m2)){
	$areaid = $m1.','.$m2;
}elseif(ifint($m1)){
	$areaid = $m1;
}
if (!empty($areaid))$SQL   .= " AND areaid LIKE '%".$areaid."%' ";
if (ifint($mate_age1))$SQL .= " AND ( YEAR(NOW()) - YEAR(birthday) >= '$mate_age1' ) ";
if (ifint($mate_age2))$SQL .= " AND ( YEAR(NOW()) - YEAR(birthday) <= '$mate_age2' ) ";
//
if ($submitok == 'ajax_getmate_ulist'){
	$SQL .= "AND id NOT IN (SELECT uid FROM ".__TBL_TIP__." WHERE senduid=".$cook_uid." AND kind=3)";
	$rt=$db->query("SELECT id,nickname,photo_s,birthday,uname FROM ".__TBL_USER__." WHERE id<>".$cook_uid." AND photo_s<>'' AND photo_f=1 AND flag=1 ".$SQL." ORDER BY rand() LIMIT 12");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
        $rows = $db->fetch_array($rt,'num');
		if(!$rows) break;
		$id       = $rows[0];
		$nickname = dataIO($rows[1],'out');
		$photo_s  = $rows[2];
		$birthday = $rows[3];
		$uname    = dataIO($rows[4],'out');
		$birthday_str = (getage($birthday)<=0)?'':' '.getage($birthday).'岁';
		$phref    = Href('u',$id);
		$photo_s_url = $up2.$photo_s;
		$nickname = (!empty($nickname))?$nickname:$uname;
		?>
        <li>
        <a href="<?php echo $phref; ?>" target="_blank">
            <p class="img" style="background:#f0f0f0 url('<?php echo $photo_s_url; ?>')center top/100% auto no-repeat"></p>
            <h5><?php echo $nickname.$birthday_str; ?></h5>
        </a>
        <a href="javascript:;" value="<?php echo $id; ?>">打招呼</a>
        </li>
	<?php }
		echo '|WWWzeaiCN|1';
	}else{echo nodatatips('暂时没有符合你要求的会员');}
	exit;
}
$uname    = dataIO($row['uname'],'out');
$nickname = trimhtml(dataIO($row['nickname'],'out'));
$grade    = $row['grade'];
$photo_s  = $row['photo_s'];$data_photo_s=$photo_s;
$photo_f  = $row['photo_f'];
$loveb    = $row['loveb'];
$money    = $row['money'];
$if2      = $row['if2'];
$sjtime   = $row['sjtime'];
$sign_time= $row['sign_time'];
$refresh_time= $row['refresh_time'];
$click    = $row['click'];
$identityproof= $row['identityproof'];
$dataflag  = $row['dataflag'];
$logincount= $row['logincount'];
$myinfobfb = $row['myinfobfb'];
$mate_age1 = $row['mate_age1'];
$mate_age2 = $row['mate_age2'];
$mate_areatitle = $row['mate_areatitle'];
$openid = $row['openid'];
$regkind = $row['regkind'];
$birthday = $row['birthday'];
$pay = $row['pay'];
$heigh = $row['heigh'];

$RZ = $row['RZ'];
$data_photo_m_url = (!empty($photo_s) )?$up2.getpath_smb($photo_s,'m'):HOST.'/p1/img/photo_m'.$sex.'hui.png';
//if(empty($photo_s)){
//	$sexbg16 = ($sex==1)?'#f0f0f0':'#f0f0f0';
//}
$uname_str = (empty($nickname))?$uname:$nickname;
$mate_sex = ($sex == 1)?'女':'男';
$matetips  = '你正在寻找';
$matetips .= $mate_sex.'朋友';
if (!empty($mate_areatitle)){
	$matetips .= ' 居住在 '.$mate_areatitle;
}
if (!empty($mate_age1) && !empty($mate_age2)){
	$matetips .= ' , 年龄为 '.$mate_age1.'-'.$mate_age2.' 岁 的'.$mate_sex.'士';
}elseif(empty($mate_age1) && !empty($mate_age2)){
	$matetips .= ' , 年龄小于 '.$mate_age2.' 岁 的'.$mate_sex.'士';
}elseif(!empty($mate_age1) && empty($mate_age2)){
	$matetips .= ' , 年龄大于 '.$mate_age1.' 岁 的'.$mate_sex.'士';
}
//
if ($if2 > 0){
	$timestr1 = get_if2_title($if2);
}
if (!empty($sjtime)){
	$d1  = ADDTIME;
	$d2  = $sjtime + $if2*30*86400;
	$ddiff = $d2-$d1;
	if ($ddiff < 0 && $if2 != 999){
		$timestr2 = ',<font class="Caaa">已过期</font>';
		$db->query("UPDATE ".__TBL_USER__." SET grade=1,sjtime=0 WHERE id=".$cook_uid);
		//站内消息
		$C = $cook_nickname.'您好，您的VIP会员已过期~~ 请尽快进入升级，以免数据丢失，<a href='.Href('vip').' class=aQING>立即升级</a>';
		$db->SendTip($uid,'您的VIP资格已过期，请速续费',dataIO($C,'in'),'sys');
		if (!empty($data_openid) && $data_subscribe==1){
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
$timestr2 = '';
$timestr = (!empty($timestr1))?'('.$timestr1.$timestr2.')':'';
//
if ($photo_f == 1 && $photo_s<>'' && !empty($heigh) && !empty($pay) && $birthday<>'0000-00-00'){
	$mc = $db->COUNT(__TBL_USER__,"birthday<>'0000-00-00' AND heigh>0 AND flag=1 AND dataflag=1 AND refresh_time>".$refresh_time);
	$mc = $mc+1;
}else{
	$mc = 0;
}
//
switch ($dataflag){
	case 0:$dataflag_str = '审核中';break;
	case 1:$dataflag_str = '正常';break;
	case 2:$dataflag_str = '审核未通过';break;
}
//
$fans    = $db->COUNT(__TBL_GZ__," uid=".$cook_uid." AND flag=1");
$mygznum = $db->COUNT(__TBL_GZ__," senduid=".$cook_uid." AND flag=1");
//
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
//
$nav='my';$zeai_cn_menu='my';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的个人中心 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php'; ?>
<div class="main fadeInL">
	<div class="mainL"><?php require_once ZEAI.'p1/my_left.php'; ?></div>
	<div class="mainR">
        <div class="photobox">
            <div class="photoboxL" id="photo_s">
                <div style="background:#f0f0f0 url('<?php echo $data_photo_m_url; ?>')center center/100% auto no-repeat" class="m"></div>
				<a class="photobtn"><span><i class="ico S18">&#xe620;</i> 上传头像</span></a>
                <?php
				if (empty($photo_s)){
					echo '<div class="pmask">设置头像</div>';
				}elseif($photo_f == 0 && !empty($photo_s)){
					echo '<div class="pmask">审核中</div>';
				}?>
            </div>
            <div class="photoboxR">
            	<a href="<?php echo Href('u',$cook_uid); ?>" target="_blank" class="uhome"><i class="ico">&#xe7a0;</i>预览主页</a>
            	<a href="javascript:;" class="qdbtn" id="sign"><i class="ico">&#xe648;</i><span id="signstr"><?php if ($signflag == 1){echo '签到';}else{echo '已签到';}?></span></a>
            </div>
            <div class="photobox1">
                <h2><?php echo $uname_str; ?></h2>
                <li>UID：<?php echo $cook_uid; ?><a href="my_vip.php" title="VIP会员升级">VIP升级<i class="ico">&#xe6ab;</i></a></li>
                <li>会员等级：<?php echo uicon($sex.$grade,2).'<font style="vertical-align:middle">'.utitle($grade).$timestr.'</font>'; ?></li>
                <li>当前关注：<font onclick="zeai.openurl('my_follow.php')"><?php echo $mygznum; ?></font> 人</li>
            </div>
            <div class="photobox2">
                <li onClick="zeai.openurl('my_cert.php');">诚信认证：<?php echo RZ_html($RZ,'m','all');?></li>
                <li>当前排名：
                <?php if ($mc >0){?>
                	<a href="my_push_index.php">第 <font><?php echo $mc; ?></font> 名</a>
                <?php }else{ ?>
                	<font style="vertical-align:middle">无，</font><a href="my_push_index.php" class="btn size1 BAI">申请排名</a>
                <?php }?>
                </li>
                <li>个人资料：<?php echo $myinfobfb;?>% <a href="my_info.php" class="edit" title="修改资料">✎</a></li>
                <li>当前状态：<font><?php echo $dataflag_str; ?></font></li>
            </div>
            <div class="photobox3">
                <li><?php echo $_ZEAI['loveB']; ?>：<a href="my_loveb.php?" title="进入<?php echo $_ZEAI['loveB']; ?>账户"><?php echo $loveb; ?></a></li>
                <li>余额：<a href="my_money.php" title="进入余额账户"><font><?php echo $money; ?></font></a></li>
                <li>人气指数：<font><?php echo $click; ?></font></li>
                <li>我的粉丝：<a href="my_follow.php?t=2" title="查看我的粉丝"><font><?php echo $fans; ?></font></a> 人</li>
            </div>
            
        </div>
        
        <div class="box match">
        	<dl><dt>今日速配 <span><?php echo $matetips; ?></span> <a href="my_info.php?t=4" class="edit">✎</a> </dt><dd><a href="javascript:;" id="rematchlist">换一组 <i class="ico">&#xe642;</i></a></dd></dl>
            <ul id="list1">
			<?php
			$SQL .= "AND id NOT IN (SELECT uid FROM ".__TBL_TIP__." WHERE senduid=".$cook_uid." AND kind=3)";
            $rt=$db->query("SELECT id,nickname,photo_s,birthday,uname,photo_ifshow FROM ".__TBL_USER__." WHERE id<>".$cook_uid." AND photo_s<>'' AND photo_f=1 AND flag=1 ".$SQL." ORDER BY refresh_time DESC LIMIT 12");
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'num');
                if(!$rows) break;
                $id       = $rows[0];
                $nickname = urldecode(dataIO($rows[1],'out'));
                $photo_s  = $rows[2];
				$birthday = $rows[3];
				$uname    = dataIO($rows[4],'out');
				$photo_ifshow = $rows[5];
				$birthday_str = (getage($birthday)<=0)?'':' '.getage($birthday).'岁';
                $phref    = Href('u',$id);
				$photo_s_url = $up2.$photo_s;
				if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
				$nickname = (!empty($nickname))?$nickname:$uname;
				
                ?>
                <li>
                <a href="<?php echo $phref; ?>" target="_blank">
                	<p class="img" style="background:#f0f0f0 url('<?php echo $photo_s_url; ?>')center center/100% auto no-repeat"></p>
                    <h5><?php echo $nickname.$birthday_str; ?></h5>
                </a>
                <a href="javascript:;" value="<?php echo $id; ?>"<?php echo (!in_array('hi',$navarr))?' style="display:none"':'';?>>打招呼</a>
                </li>
			<?php }}else{echo nodatatips('暂时没有符合你要求的会员');}?>
            </ul>
        </div>
        <div class="box match">
            <dl><dt>谁看过我</dt><dd><a href="my_browse.php">更多</a></dd></dl>
            <ul id="list2">
			<?php
			$rt=$db->query("SELECT b.id,b.sex,b.nickname,b.photo_s,b.photo_f,b.birthday,b.photo_ifshow FROM ".__TBL_CLICKHISTORY__." a,".__TBL_USER__." b WHERE b.flag=1 AND b.sex<>0 AND a.senduid=b.id AND a.uid=".$cook_uid." ORDER BY a.addtime DESC LIMIT 6");
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt);
                if(!$rows) break;
                $id       = $rows[0];
                $sex      = $rows[1];
                $nickname = dataIO($rows[2],'out');
				$nickname = (empty($nickname))?'uid:'.$id:$nickname;
                $photo_s  = $rows[3];
                $photo_f  = $rows[4];
				$birthday = $rows[5];
				$photo_ifshow = $rows[6];
				$birthday_str = (getage($birthday)<=0)?'':' '.getage($birthday).'岁';
                $phref    = Href('u',$id);
				$photo_s_url = (!empty($photo_s) && $photo_f==1)?$up2.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
				if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
				if(empty($photo_s) || $photo_f==0){
					$sexbg16 = ($sex==1)?'#E6F4FE':'#FCEFF4';
				}
				$ifhi     = ($db->COUNT(__TBL_TIP__," senduid=".$cook_uid." AND uid=".$id." AND kind=3") > 0)?1:0;$hichattitle = ($ifhi == 1)?'聊天':'打招呼';
				$ifhi_cls = ($ifhi == 1)?' class="chat" ':'';
                ?>
                <li>
                <a href="<?php echo $phref; ?>" target="_blank">
                    <p class="img" style="background:<?php echo $sexbg16;?> url('<?php echo $photo_s_url; ?>')center center/100% auto no-repeat"></p>
                    <h5><?php echo $nickname.$birthday_str; ?></h5>
                </a>
                <a href="javascript:;" value="<?php echo $id; ?>" ifhi="<?php echo $ifhi; ?>"<?php echo $ifhi_cls; ?><?php echo (!in_array('hi',$navarr))?' style="display:none"':'';?>><?php echo $hichattitle; ?></a>
                </li>
			<?php }}else{echo nodatatips('暂时没有人看过你');}?>
            </ul>
            <div class="clear"></div>
        </div>
        
    </div>
</div>
<div id='tips0_100_0' class='tips0_100_0 alpha0_100_0'></div>
<div id="mask_sign" class='alpha0_100'><div class="gif rotate" id="mask_gif"></div></div>
<div id="signokbox" class="scale">
	<div class="signok">
    	<h1>已签到</h1>
    	<div class="hr"></div>
        <h4>恭喜你获得<font id="randloveb">0</font>个<?php echo $_ZEAI['loveB'];?></h4>
    </div>
</div>
<script>var uid=<?php echo $cook_uid;?>,zeaimap,upMaxMB = <?php echo $_UP['upMaxMB']; ?>,up2='<?php echo $up2;?>';</script>
<script src="js/my.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php require_once ZEAI.'p1/bottom.php';?>
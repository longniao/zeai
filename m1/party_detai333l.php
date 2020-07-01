<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'活动参数错误'));
require_once ZEAI.'sub/conn.php';
if($submitok == 'ajax_detail_bbsadd' || $submitok == 'ajax_detail_bm' ){
	$currfields = "sex,grade,if2,qq,mob,weixin";
	$$rtn='json';$chk_u_jumpurl=HOST.'/?z=party&e=detail&a='.$clsid;
	require_once ZEAI.'my_chk_u.php';
}


$rt = $db->query("SELECT * FROM ".__TBL_PARTY__." WHERE flag>0 AND path_s<>'' AND id=".$fid);
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'name');
	$path_s   = $row['path_s'];
	$pathlist = $row['pathlist'];
	$jzbmtime = $row['jzbmtime'];
	$flag     = $row['flag'];
	$ifpay = $row['ifpay'];
	$bmnum = $row['bmnum'];
	$signnum = $row['signnum'];
	$bbsnum  = $row['bbsnum'];
	$hdtime= dataIO($row['hdtime'],'out');
	$address = dataIO($row['address'],'out');
	$title = dataIO($row['title'],'out');
	$num_n = $row['num_n'];
	$num_r = $row['num_r'];
	$rmb_n = $row['rmb_n'];
	$rmb_r = $row['rmb_r'];
	$content  = dataIO($row['content'],'out');
	$path_s_url = $_ZEAI['up2'].'/'.$path_s;
	$path_b_url = getpath_smb($path_s_url,'b');
	$num_n_str=($num_n==0)?'<b>不限</b>':'<b>'.$num_n.'</b>人';
	$num_r_str=($num_r==0)?'<b>不限</b>':'<b>'.$num_r.'</b>人';
	$rmb_n_str=($rmb_n==0)?'<b class="C090">免费</b>':'<b>'.$rmb_n.'</b>元';
	$rmb_r_str=($rmb_r==0)?'<b class="C090">免费</b>':'<b>'.$rmb_r.'</b>元';	
	
	$signum_str=($signnum>0)?'<b>'.$signnum.'</b>':'';	
	$bbsnum_str=($bbsnum>0)?'<b>'.$bbsnum.'</b>':'';	
} else {json_exit(array('flag'=>0,'msg'=>'活动不存在'));}

//
/*********************** 详情 detail ***************************/
//
?>
<link href="m1/css/party_detail.css" rel="stylesheet" type="text/css" />
<i class="ico Ugoback" id="ZEAIGOBACK-party_detail">&#xe602;</i>
<div class="submain party_detail">
	<div class="banner">
        <img src="<?php echo $path_b_url;?>" class="banner">
        <div class="djs"><font><span class="jzbmT"><i class="ico"></i> 截止报名还剩</span><span class="timestyle">9</span>天<span class="timestyle">6</span>小时<span class="timestyle">10</span>分钟</font></div>        
    </div>
    <div class="titleinfo">
        <h3><?php echo $title;?></h3>
        <h5><i class="ico timee">&#xe634;</i><?php echo $hdtime;?></h5>    
        <h5><i class="ico address">&#xe614;</i><?php echo $address;?></h5>
    </div>
    
    <div class="bmrs2">
    	<dl><dt><?php echo $num_n_str;?></dt><dd>男士</dd></dl>
    	<dl><dt><?php echo $num_r_str;?></dt><dd>女士</dd></dl>
    	<dl><dt><?php echo $rmb_n_str;?></dt><dd>男士</dd></dl>
    	<dl><dt><?php echo $rmb_r_str;?></dt><dd>女士</dd></dl>
    </div>
	<div class="Cbox">
        <div class="tabmenu tabmenu_3 tabmenuParty" id="party_detail_nav">
            <li id="party_detail1btn" class="ed"><span>活动详情</span></li>
            <li id="party_detail2btn"><span>现场签到<?php echo $signum_str;?></span></li>
            <li id="party_detail3btn"><span>评论<?php echo $bbsnum_str;?></span></li>
            <i></i>
        </div>
    	<div class="C">
        	<div class="C1 fadeInL" id="party_detail_C1">
				<?php echo $content;?>
                <div class="party_kefu">
                    <div class="linebox"><div class="line"></div><div class="title S14 BAI">联系我们</div></div><br>
                    遇到问题？请联系客服帮忙。
                    <?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
                    <?php if (!empty($kf_tel)){?><br>电话：<a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a><?php }?>
                    <?php if (!empty($kf_mob)){?><br>手机：<a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
                    <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
                </div>
            </div>
        	<div class="C2 fadeInL" id="party_detail_C2">
      			
                
            	<ul class="signlist"><?php echo party_getSignUlist($fid)?></ul>
                <br><br><div class="linebox" style="z-index:0"><div class="line BAI W50"></div><div class="title S12 BAI">温馨提醒</div></div>
                <center>【参加活动时，请携带本人身份证到现场签到】</center><br>
            </div>
        	<div class="C3 fadeInL" id="party_detail_C3">2222222222222222222</div>
        </div>
    </div>
</div>
<div class="party_detailBtmBM" id="BtmNav">
    <dl class="uli">
        <dt>已报名<font><?php echo $bmnum;?></font>人</dt>
        <dd><?php echo party_getBmUlist($fid).'<b class="ico">&#xe636;</b>';?></dd>
    </dl>
	<div class="bmbtn" id="bmbtn"><i class="ico">&#xe65c;</i>我要报名</div>
</div>







<script src="m1/js/party_detail.js"></script>
<script>
//party_btn_detailBM.onclick=party_btn_detailBMfn;

	ZeaiM.tabmenu.init({obj:party_detail_nav});
	setTimeout(function(){party_detail1btn.click();},200);
	
	party_detail1btn.onclick=party_detail1btnFn;
	party_detail2btn.onclick=party_detail2btnFn;
	party_detail3btn.onclick=party_detail3btnFn;
	

//history.length
//
//alert(document.referrer);


<?php if (is_weixin()){//分享?>
	var share_party_detail_title = '<?php echo strip_tags(TrimEnter($title)); ?>_<?php echo $_ZEAI['siteName'];?>';
	var share_party_detail_desc  = '<?php echo strip_tags(TrimEnter(dataIO($content,'out',50))); ?>';
	var share_party_detail_link  = '<?php echo HOST; ?>?z=party&e=detail&a=<?php echo $fid; ?>';
	var share_party_detail_imgurl= '<?php echo $path_s_url; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_party_detail_party_detail_title,desc:share_party_detail_desc,link:share_party_detail_link,imgUrl:share_party_detail_imgurl});
		wx.onMenuShareTimeline({title:share_party_detail_title,link:share_party_detail_link,imgUrl:share_party_detail_imgurl});
	});
<?php }?>


<?php if (is_weixin() && is_mobile()){?>
	if(ifX()){BtmNav.style.paddingBottom='34px';bmbtn.style.bottom='39px';}
<?php }?>
</script>

<?php
if (!empty($_GZH['wx_gzh_ewm']) && ifint($cook_uid)){wx_endurl('您刚刚浏览的页面【交友活动-】',HOST.'/?z=party&e=detail&a='.$fid);}
function ajax_list_fn($totalP,$p) {
	global $_ZEAI,$db,$nodatatips,$RTSQL;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);
	if($totalP==0 && $p == 1)return $nodatatips;
	if ($p > $totalP)exit("end");
	$LIMIT = ($p == 1)?$_ZEAI['pagesize']:$_ZEAI['limit'];
	$RTSQL.=" LIMIT ".$LIMIT;
	$rt = $db->query($RTSQL);
	$total = $db->num_rows($rt);
	if ($p == 1){
		if ($total <= 0)return $nodatatips;
		$fort= $total;
	}else{
		if ($total <= 0)exit("end");
		$fort= $_ZEAI['pagesize'];
		$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);
	}
	$rows_list='';
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_list .= rows_list($rows);
	}
	return $rows_list;
}
function rows_list($rows) {
	global $_ZEAI,$db;
	$fid   = $rows['id'];
	$bmnum    = $rows['bmnum'];
	$path_s   = $rows['path_s'];
	$jzbmtime = $rows['jzbmtime'];
	$flag     = $rows['flag'];
	$hdtime  = $rows['hdtime'];
	$address = dataIO($rows['address'],'out');
	$title = dataIO($rows['title'],'out');
	$num_n = $rows['num_n'];
	$num_r = $rows['num_r'];
	$path_b=getpath_smb($path_s,'b');
	$path_b_url=$_ZEAI['up2'].'/'.$path_b;
	//
	$echo = '<li clsid="'.$fid.'">';
	$echo .= '<div class="pic"><img src="'.$path_b_url.'"><div class="djs">'.party_djs($flag,$jzbmtime).'</div></div>';
	$echo .= '<h3>'.$title.'</h3>';
	$echo .= '<h6><i class="ico time">&#xe634;</i> '.$hdtime.'</h6>';
	$echo .= '<h6><i class="ico nowrap">&#xe614;</i> '.$address.'</h6>';
	$echo .= '<dl><dt>已报名'.$bmnum.'人</dt><dd>'. party_getBmUlist($fid).'<b class="ico">&#xe636;</b></dd></dl>';
	$echo .= '</li>';
	return $echo;
}
function party_getBmUlist($fid) {
	global $db,$_ZEAI;
	$rt=$db->query("SELECT U.sex,U.photo_s,U.photo_f FROM ".__TBL_PARTY_USER__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC LIMIT 3");
	$echo = '';
	WHILE ($rows = $db->fetch_array($rt,'num')){
		$sex      = $rows[0];
		$photo_s  = $rows[1];
		$photo_f  = $rows[2];
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
		$echo.='<img src="'.$photo_s_url.'"'.$sexbg.'>';
	}
	return $echo;
}
function party_getSignUlist($fid) {
	global $db,$_ZEAI;
	$rt=$db->query("SELECT a.uid,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f FROM ".__TBL_PARTY_SIGN__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC");
	$echo = '';
	WHILE ($rows = $db->fetch_array($rt,'name')){
		$uid      = $rows['uid'];
		$sex      = $rows['sex'];
		$photo_s  = $rows['photo_s'];
		$photo_f  = $rows['photo_f'];
		$grade    = $rows['grade'];
		$nickname = dataIO($rows['nickname'],'out');
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
		$echo .= '<li onClick="PTuA('.$uid.');">';
		$echo .='<img src="'.$photo_s_url.'"'.$sexbg.'>';
		$echo .= '<span>'.uicon($sex.$grade).$nickname.'</span>';
		$echo .= '</li>';
	}
	return $echo;
}


function party_djs($flag,$jzbmtime) {
	$d1  = ADDTIME;
	$d2  = $jzbmtime;
	$totals  = ($d2-$d1);
	$day     = intval( $totals/86400 );
	$hour    = intval(($totals % 86400)/3600);
	$hourmod = ($totals % 86400)/3600 - $hour;
	$minute  = intval($hourmod*60);
	if ($flag >= 2)$totals = -1;
	if (($totals) > 0) {
		$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> 截止报名还剩</span>';
		if ($day > 0){
			$outtime = $tmp."<span class=timestyle>$day</span>天";
		} else {
			$outtime = $tmp;
		}
		$outtime .= "<span class=timestyle>$hour</span>小时<span class=timestyle>$minute</span>分钟";
	} else {
		$outtime = '　报名已经结束';
	}
	$outtime = '<font>'.$outtime.'</font>';
	return $outtime;
}
?>
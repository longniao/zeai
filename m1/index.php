<?php
//$parARR = array('index','mate','VIP');
//if(in_array($_GET['e'],$parARR)){require_once '../sub/init.php';}
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
$nav = 'home';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/udata.php';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员</div>";
$if_other_login_subscribe = openid_chk();
$switch = json_decode($_ZEAI['switch'],true);
switch ($submitok) {
	case 'ajax_tj':exit(getUlist($SQL));break;
	case 'ajax_gps_save':
		if(!empty($latitude) && !empty($longitude) && ifint($cook_uid)){
			$db->query("UPDATE ".__TBL_USER__." SET latitude='$latitude',longitude='$longitude' WHERE id=".$cook_uid);
			exit(getGpsUlist($latitude,$longitude));
		}else{
			exit($nodatatips);
		}
	break;
	case 'ajax_fj':
		if(!ifint($cook_uid)){
			exit('nologin');
		}else{
			$row = $db->ROW(__TBL_USER__,"latitude,longitude","longitude<>'' AND id=".$cook_uid);
			if ($row){
				$data_latitude = $row[0];
				$data_longitude= $row[1];
			}else{
				exit('nogpsdata');
			}
			exit(getGpsUlist($data_latitude,$data_longitude));
		}
	break;
	case 'ajax_vip':exit(getUlist(" AND grade>1"));break;
	case 'ajax_sex1':exit(getUlist(" AND sex=1"));break;
	case 'ajax_sex2':exit(getUlist(" AND sex=2"));break;
	case 'ajax_pp':
	if(!ifint($cook_uid))exit('nologin');
	$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2"," flag=1 AND id=".$cook_uid,"name");
	if (!$row)exit('noauth');
	$mate_age1      = intval($row['mate_age1']);
	$mate_age2      = intval($row['mate_age2']);
	$mate_heigh1    = intval($row['mate_heigh1']);
	$mate_heigh2    = intval($row['mate_heigh2']);
	$mate_pay       = $row['mate_pay'];
	$mate_edu       = $row['mate_edu'];
	$mate_areaid    = $row['mate_areaid'];
	$mate_love      = $row['mate_love'];
	$mate_car       = $row['mate_car'];
	$mate_house     = $row['mate_house'];
	//
	$mate_weigh1      = intval($row['mate_weigh1']);
	$mate_weigh2      = intval($row['mate_weigh2']);
	$mate_job         = $row['mate_job'];
	$mate_child       = $row['mate_child'];
	$mate_marrytime   = $row['mate_marrytime'];
	$mate_companykind = $row['mate_companykind'];
	$mate_smoking     = $row['mate_smoking'];
	$mate_drink       = $row['mate_drink'];
	$mate_areaid2     = $row['mate_areaid2'];
	if( empty($mate_age1) && empty($mate_age2) && empty($mate_heigh1) && empty($mate_heigh2) && empty($mate_pay) && empty($mate_edu) && empty($mate_areaid) && empty($mate_love) && empty($mate_house) && empty($mate_weigh1) && empty($mate_weigh2) && empty($mate_job) && empty($mate_child) && empty($mate_marrytime) && empty($mate_companykind) && empty($mate_smoking) && empty($mate_drink) && empty($mate_areaid2)  ){
		exit('nomate');
	}
	//生成SQL语句
	$SQL .= " AND id<>".$cook_uid;
	$SQL .= ($cook_sex == 2)?" AND sex=1 ":" AND sex=2 ";
	$SQL .= mate_diy_SQL();
	//SQL语句结束
	exit(getUlist($SQL));
	break;
	case 'ajax_iMarquee':
		function iMarqueeFn($rows){
			global $_ZEAI;
			$sex      = $rows['sex'];
			$nickname = trimhtml(dataIO($rows['nickname'],'out'));
			$content  = trimhtml(dataIO($rows['content'],'out'));
			$photo_s  = $rows['photo_s'];
			$photo_f  = $rows['photo_f'];
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_s'.$sex.'.png';
			$nickname=($rows['ifnickname']!='NO')?$nickname:'';
			return '<li><img src="'.$photo_s_url.'"><font>'.$nickname.$content.'</font></li>';
		}
		$c = '';
		$rt = $db->query("SELECT b.sex,b.nickname,uid,senduid FROM ".__TBL_QIANXIAN__." a,".__TBL_USER__." b WHERE a.flag=2 AND a.senduid=b.id ORDER BY a.id DESC LIMIT 5 ");
		$total = $db->num_rows($rt);
		if ($total>0){
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$uid = $rows['uid'];
				$senduid = $rows['senduid'];
				$uid = '*'.substr($uid,1,3).'*'.substr($uid,4,1).'*';
				$senduid = '*'.substr($senduid,1,3).'*'.substr($senduid,4,1).'*';
				$rows['content'] = '恭喜会员'.$uid.'与'.$senduid.'牵线成功';
				$rows['photo_f'] = 1;
				$rows['ifnickname'] = 'NO';
				$c .= iMarqueeFn($rows);
			}
		}
		$rt=$db->query("SELECT a.uid,a.num,a.content,b.nickname,b.photo_s FROM ".__TBL_MONEY_LIST__." a,".__TBL_TG_USER__." b WHERE a.content LIKE '%奖励%' AND a.kind=8 AND a.tg_uid=b.id AND b.flag=1 ORDER BY a.id DESC LIMIT 3");
		$total = $db->num_rows($rt);
		if ($total>0){
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$rows['content'] = '推广注册奖励'.$rows['num'].'元';
				$rows['photo_f'] = 1;
				$c .= iMarqueeFn($rows);
			}
		}
		$rt = $db->query("SELECT a.kind,a.money,b.sex,b.sex,b.nickname,b.photo_s,b.photo_f FROM ".__TBL_PAY__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.flag=1 ORDER BY a.id DESC LIMIT 3");
		$total = $db->num_rows($rt);
		if ($total>0){
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				switch ($rows['kind']) {
					case 1:$tt  = '升级了VIP会员';break;
					case 2:$tt  = '充值了爱豆';break;
					case 3:$tt  = '充值了余额';break;
					case 4:$tt  = '充值了活动报名费';break;
					case 7:$tt  = '进行了【真人认证】';break;
					case -1:$tt  = '提现了'.$rows['money'].'元';break;
				}
				$rows['content'] = ''.$tt;
				$c .= iMarqueeFn($rows);
			}
		}
		
		$rt=$db->query("SELECT a.uid,a.content,b.sex,b.nickname,b.photo_s,b.photo_f FROM ".__TBL_TREND__." a,".__TBL_USER__." b WHERE a.uid=b.id AND b.flag=1 AND a.flag=1 ORDER BY a.id DESC LIMIT 3");
		$total = $db->num_rows($rt);
		if ($total>0){
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				//$rows['content'] = '发布了动态';
				$rows['content'] = trimhtml(dataIO($rows['content'],'out'));
				$c .= iMarqueeFn($rows);
			}
		}
		
		$rt=$db->query("SELECT id,sex,nickname,photo_s,photo_f FROM ".__TBL_USER__." WHERE flag=1 ORDER BY id DESC LIMIT 3");
		$total = $db->num_rows($rt);
		if ($total>0){
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$rows['content'] = '注册了'.$_ZEAI['siteName'];
				$c .= iMarqueeFn($rows);
			}
		}
		exit($c);
	break;
}
  
//kefu
if(ifint($cook_uid)){
	if (!empty($cook_photo_s )){
		$photo_s_url = $_ZEAI['up2'].'/'.getpath_smb($cook_photo_s,'m');
		$photo_s_str = '<p style="background-image:url(\''.$photo_s_url.'\');"></p>';
	}else{
		$photo_s_str = '<i class="i'.$cook_sex.'"></i>';
	}
}else{
	$photo_s_str = '<p style="background-image:url(\''.HOST.'/res/noPindex.png\');"></p>';
	$cook_nickname = '点击登录';
}
$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); $kf_mob=dataIO($_ZEAI['kf_mob'],'out');
$nickname_str = (!empty($cook_nickname))?$cook_nickname:$cook_uname;
//
if (!empty($_INDEX['mBN_path1_s']) || !empty($_INDEX['mBN_path2_s']) || !empty($_INDEX['mBN_path3_s']) ){
	$ifbanner = true;
	$path1_s = $_INDEX['mBN_path1_s'];
	$path2_s = $_INDEX['mBN_path2_s'];
	$path3_s = $_INDEX['mBN_path3_s'];
	$path1_b = getpath_smb($path1_s,'b');
	$path2_b = getpath_smb($path2_s,'b');
	$path3_b = getpath_smb($path3_s,'b');
	$path1_url = $_INDEX['mBN_path1_url'];
	$path2_url = $_INDEX['mBN_path2_url'];
	$path3_url = $_INDEX['mBN_path3_url'];
	$banner = "";
	$upurl = $_ZEAI['up2']."/";
	if (!empty($path1_s)){
		if(!empty($path1_url)){
			$banner .= "<li><a href='".$path1_url."' target='_blank'><img src='".$upurl.$path1_b."'></a></li>";
		}else{
			$banner .= "<li><img src='".$upurl.$path1_b."'></li>";
		}
	}
	if (!empty($path2_s)){
		if(!empty($path2_url)){
			$banner .= "<li><a href='".$path2_url."' target='_blank'><img src='".$upurl.$path2_b."'></a></li>";
		}else{
			$banner .= "<li><img src='".$upurl.$path2_b."'></li>";
		}
	}
	if (!empty($path3_s)){
		if(!empty($path3_url)){
			$banner .= "<li><a href='".$path3_url."' target='_blank'><img src='".$upurl.$path3_b."'></a></li>";
		}else{
			$banner .= "<li><img src='".$upurl.$path3_b."'></li>";
		}
	}
	$bnclass = "";
}else{$ifbanner = false;}
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $_INDEX['indexTitle'];?></title>
<meta name="Keywords" content="<?php echo $_INDEX['indexKeywords'];?>">
<meta name="Description" content="<?php echo $_INDEX['indexContent'];?>">
<?php echo HEADMETA; ?>
<meta name="x5-orientation" content="portrait">
<link href="res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="m1/css/m1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="m1/css/index.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<meta name="generator" content="Zeai.cn V6" />
<script src="res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="m1/js/m1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="res/jweixin-1.2.0.js"></script>
	<script>wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr: '<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList: ['getLocation','chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','startRecord','stopRecord','onVoiceRecordEnd','uploadVoice','downloadVoice','playVoice','pauseVoice']});
    //
	var share_title = '<?php echo dataIO($_INDEX['indexTitle'],'out'); ?>';
	var share_desc  = '<?php echo dataIO($_INDEX['indexContent'],'out'); ?>';
	var share_link  = '<?php echo HOST; ?>/?z=index';
	var share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
		wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
	});
    </script>
<?php }?>
</head>
<body>
<div class="itop huadong" id="itop">
    <div class="logo" id="Ilogo"><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo'];?>?<?php echo $_ZEAI['cache_str'];?>"></div>
    <div class="title"><?php echo $_ZEAI['siteName'];?></div>
    <div class="ikefu" id="Ikefubtn"><i class="ico">&#xe64b;</i></div>
</div>

<div id="main" class="main huadong">
    <div class="bannerbox">
        <div class="iBanner">
		<?php if ($ifbanner){ ?>
			<script src="res/zeai_banner.js"></script>
            <div id="banner"<?php echo $bnclass; ?>>
                <?php if ($ifbanner){ ?>
                <div id="pic_box"><?php echo $banner; ?></div>
                <div class="focus_box"><div id="focus_dot"></div></div>
                <?php }else{echo $banner;}?>
            </div>
        <?php }?>
        </div>
        <div class="iso" id="iso"><li>女友<i class="ico">&#xe60b;</i></li><li>20~25<i class="ico">&#xe60b;</i></li><li>地区<i class="ico">&#xe60b;</i></li><li>职业<i class="ico">&#xe60b;</i></li><li><i class="ico">&#xe6c4;</i>搜索</li></div>    
	</div>
    <?php 
	if($_ZEAI['navkind']==2){
		$navdiy = json_decode($_ZEAI['navdiy'],true);$nn=0;
		foreach ($navdiy as $V){if($V['f']==0 || empty($V['t']) || empty($V['img']) || empty($V['url']))continue;$nn++;$newnavidy[]=$V;}
		if($nn % 4==0){$inavAW=25;}elseif($nn % 3==0){$inavAW=33;}elseif($nn % 2==0){$inavAW=50;}
		if (count($newnavidy) >= 1 && is_array($newnavidy)){echo '<style>.inavdiy a{width:'.$inavAW.'%}</style><div class="inavdiy">';foreach ($newnavidy as $V){?><a href="<?php echo dataIO($V['url'],'out');?>"><img src="<?php echo $_ZEAI['up2'].'/'.$V['img'];?>"><span><?php echo dataIO($V['t'],'out');?></span></a><?php }echo '</div>';}?>
	 <?php }else{	
		$newnavarr=array();
		if(@in_array('trend',$navarr))$newnavarr[]='trend';
		if(@in_array('dating',$navarr))$newnavarr[]='dating';
		if(@in_array('video',$navarr))$newnavarr[]='video';
		if(@in_array('group',$navarr))$newnavarr[]='group';
		if(@in_array('hb',$navarr))$newnavarr[]='hb';
		if(@in_array('hn',$navarr))$newnavarr[]='hn';
		if(@in_array('party',$navarr))$newnavarr[]='party';
		if(@in_array('article',$navarr))$newnavarr[]='article';
		//if(@in_array('store',$navarr))$newnavarr[]='store';
		if(@in_array('shop',$navarr))$newnavarr[]='shop';
		if( @in_array('dating',$navarr) || @in_array('video',$navarr) || @in_array('group',$navarr) || @in_array('hb',$navarr) || @in_array('party',$navarr) || @in_array('store',$navarr)  ){
		$navnum = count($newnavarr);
		$inavAW = ($navnum==3 || $navnum==6)?33:25;
		?>
		<style>.inav a{width:<?php echo $inavAW;?>%}</style>
        <div class="inav">
            <?php if(@in_array('dating',$navarr)){?><a href="./?z=dating"><i class="ico dating">&#xe72e;</i><span>约会</span></a><?php }?>
            <?php if(@in_array('video',$navarr)){?><a href="./?z=video"><i class="ico video">&#xe668;</i><span>视频</span></a><?php }?>
			<?php if(@in_array('shop',$navarr)){?><a href="<?php echo HOST;?>/m4/shop_index.php"><i class="ico2 shop">&#xe71a;</i><span>商家</span></a><?php }?>
            <?php if(@in_array('group',$navarr)){?><a href="./m1/group"><i class="ico group">&#xe637;</i><span>圈子</span></a><?php }?>
            <?php if(@in_array('hb',$navarr)){?><a href="./m1/hongbao"><i class="ico hb">&#xe66b;</i><span>红包</span></a><?php }?>
            <?php if(@in_array('trend',$navarr)){?><a href="./?z=trend"><i class="ico trend">&#xe63b;</i><span>交友圈</span></a><?php }?>
            <?php if(@in_array('hn',$navarr)){?><a onClick="page({g:'m1/hongniang.php',l:'hongniang'})"><i class="ico hn">&#xe621;</i><span>红娘服务</span></a><?php }?>
            <?php if(@in_array('party',$navarr)){?><a href="./?z=party"><i class="ico party">&#xe776;</i><span>交友活动</span></a><?php }?>
            <?php if(@in_array('article',$navarr)){?><a href="<?php echo HOST;?>/m1/article.php"><i class="ico article">&#xe63c;</i><span>婚恋学堂</span></a><?php }?>
        </div>
    <?php }}?>
    
	<div class="iunav">
		<div class="tabmenu tabmenu_4" id="tabmenuIndex">
        	<?php if ($_INDEX['iModuleU'] == 2){?>
                <li id="iu_btn2_1" class="ed"><span>推荐</span></li>
                <li id="iu_btn2_2"><span>男生</span></li>
                <li id="iu_btn2_3"><span>女生</span></li>
                <li id="iu_btn2_4"><span>匹配</span></li>
            <?php }else{ ?>
                <li id="iu_btn1" class="ed"><span>推荐</span></li>
                <li id="iu_btn2"><span>附近</span></li>
                <li id="iu_btn3"><span>VIP</span></li>
                <li id="iu_btn4"><span>匹配</span></li>
            <?php }?>
			<i></i>
		</div>
    	<div class="more" id="pushindex_btn"><a>置顶首页</a></div>
    </div>
    <div class="iubox">
    	<div id="iubox"></div>
        <a class="more" id="indexmore">更多优质会员<i class="ico">&#xe601;</i></a>
    </div>
	<?php
	//交友活动
	if(@in_array('party',$navarr)){
		$rt=$db->query("SELECT id,title,address,flag,jzbmtime,path_s FROM ".__TBL_PARTY__." WHERE flag>0 AND path_s<>'' ORDER BY px DESC LIMIT 2");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			echo '<div class="iparty"><h3 id="partymore"><span>交友活动</span></h3>';
			for($j=1;$j<=$total;$j++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows)break;
				$fid   = $rows['id'];
				$path_s   = $rows['path_s'];
				$jzbmtime = $rows['jzbmtime'];
				$flag     = $rows['flag'];
				$address = dataIO($rows['address'],'out');
				$title = dataIO($rows['title'],'out');
				$path_b=getpath_smb($path_s,'b');
				$path_b_url=$_ZEAI['up2'].'/'.$path_b;
				?>
				<a class="li" href="<?php echo wHref('party',$fid);?>">
					<img src="<?php echo $path_b_url;?>">
					<em>
						<h4><?php echo $title;?></h4>
						<div class="djs"><?php echo party_djs($flag,$jzbmtime);?></div>
						<h6>地点：<?php echo $address;?></h6>
					</em>
					<div class="clear"></div>
				</a>
				<?php
			}
			echo '</div>';
		}
	}
	?>
    
    <?php
	//婚恋学堂
	if(@in_array('article',$navarr)){
	echo '<div class="clear"></div>';
    $rt2=$db->query("SELECT id,title FROM ".__TBL_NEWS_KIND__." WHERE id>1 ORDER BY px DESC,id DESC LIMIT 4");
    $total2 = $db->num_rows($rt2);
    if ($total2 > 0) {
        ?>
        <div class="iarticle">
            <h3 id="articlemore"><span>婚恋学堂</span></h3>
            <div class="kind" id="iarticlekind">
				<?php
				echo '<a class="ed">最新文章</a>';
                for($j=0;$j<$total2;$j++) {
                    $rows2 = $db->fetch_array($rt2,'num');
                    if(!$rows2) break;
                    $kindid   = $rows2[0];
                    $kindtitle=dataIO($rows2[1],'out');
                    //$clss=($j==0)?' class="ed"':'';
                    echo '<a href=\'m1/article.php?kind='.$kindid.'&kindtitle='.$kindtitle.'\' '.$clss.' kindid="'.$kindid.'">'.$kindtitle.'</a>';
					//if($j==0)$kind=$kindid;
                }?>
        	</div>
        <?php
		$SQL = (ifint($kind))?" AND kind=".$kind:'';
        $rt2=$db->query("SELECT id,title,kindtitle,path_s,addtime FROM ".__TBL_NEWS__." WHERE id>2 AND flag=1 AND path_s<>'' ".$SQL." ORDER BY px DESC,id DESC LIMIT 3");
        $total2 = $db->num_rows($rt2);
        if ($total2 > 0) {
            for($j=0;$j<$total2;$j++) {
                $rows2 = $db->fetch_array($rt2,'name');
                if(!$rows2) break;
                $id   = $rows2['id'];
                $title=dataIO($rows2['title'],'out');
                $path_s    = $rows2['path_s'];
                $addtime   = YmdHis($rows2['addtime'],'Ymd');
                $kindtitle = dataIO($rows2['kindtitle'],'out');
                //$path_b = getpath_smb($path_s,'b');
                //$path_b_url=$_ZEAI['up2'].'/'.$path_b;
                $path_s_url=$_ZEAI['up2'].'/'.$path_s;
                ?>
                <a class="li" href="<?php echo wHref('article',$id);?>">
                    <img src="<?php echo $path_s_url;?>">
                    <em>
                        <h4><?php echo $title;?></h4>
                        <div><span><?php echo $kindtitle;?></span><font><?php echo $addtime;?></font></div>
                    </em>
                    <div class="clear"></div>
                </a>
    <?php }}?>
    	</div>
    <?php }
	echo '<div class="clear"></div>';
	}?>
<!---->
	<?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
    <div class="ibottom">
        <div class="linebox"><div class="line"></div><div class="title BAI S14 C999">扫一扫缘分到</div></div>
        <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><?php }?>
        <div class="copyright">
        <?php if (!empty($kf_tel)){?>
        	<i class="ico">&#xe60e;</i> <a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a>
		<?php }else{?>
            <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i> <a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
        <?php }?>
        	<a href="http://www.zeai.cn" class="zeai">&copy;<?php echo date('Y').' '.$_ZEAI['siteName'];?>V<?php echo $_ZEAI['ver'];?></a>
        </div>
        <div class="hui"></div>
    </div>
    
    <div id="Ikefu">
        <span class="photo" id="I_photo_s"><?php echo $photo_s_str; ?></span>
        <h3 id="indexNkname"><?php echo $nickname_str;?></h3>
        <ul id="Irbtn">
        <a>谁看过我</a>
        <a>我的关注</a>
        <a>关于我们</a>
        </ul>
        <em>
            <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
            <?php if (!empty($kf_tel)){?><a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a><?php }?>
            <?php if (!empty($kf_mob)){?><a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
        </em>
    </div>


</div>
<!-- -->
<a href="javascript:;" id="btmKefuBtn"><i class="ico">&#xe6a6;</i>客服</a>
<div id="btmKefuBox" class="my-subscribe_box"><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><h3>长按二维码添加客服微信<br>注册VIP享受红娘一对一牵线</h3></div>
<?php if ($_INDEX['iMarquee'] == 1){?>
<div id="iMarquee" class="iMarquee huadong fadeInR">
    <li><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo'];?>?<?php echo $_ZEAI['cache_str'];?>"><font>欢迎来到<?php echo $_ZEAI['siteName'];?></font></li>
</div>
<?php }?>
<!-- -->
<script src="m1/js/index.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var browser='<?php if(is_h5app()){echo'app';}else{echo (is_weixin())?'wx':'h5';}?>',HOST='<?php echo HOST;?>',indexT,iModuleU_bigmore=<?php echo intval($_INDEX['iModuleU_bigmore']);?>;
<?php if (!empty($e)){?>ZeaiM.page.load('m1/<?php echo $e;?>'+zeai.ajxext+'a=<?php echo $a;?>&i=<?php echo $i;?>',ZEAI_MAIN,'<?php echo $e;?>');<?php }?>
ZeaiM.tabmenu.init({obj:tabmenuIndex,showbox:iubox});
<?php if ($_INDEX['iModuleU'] == 2){?>
	setTimeout(function(){iu_btn2_1.click();},200);
	iu_btn2_1.onclick=iu_btn1Fn;
	iu_btn2_2.onclick=iu_btn2_2Fn;
	iu_btn2_3.onclick=iu_btn3_3Fn;
	iu_btn2_4.onclick=iu_btn4Fn;
<?php }else{ ?>
	setTimeout(function(){iu_btn1.click();},200);
	iu_btn1.onclick=iu_btn1Fn;
	iu_btn2.onclick=iu_btn2Fn;
	iu_btn3.onclick=iu_btn3Fn;
	iu_btn4.onclick=iu_btn4Fn;
<?php }?>
indexmore.onclick=indexmoreFn;
<?php if ($ifbanner){ ?>iBannerFn();<?php }?>
<?php if ($if_other_login_subscribe){?>zeai.msg('当前微信号已被其它帐号绑定，请退出使用微信登录');<?php }?>
<?php if ($_INDEX['iMarquee'] == 1){?>
	zeaiLoadBack=['nav','itop','iMarquee'];
	setTimeout(function(){zeai_iMarqueeFn();},2000);
<?php }else{?>
	zeaiLoadBack=['nav','itop'];
<?php }?>
</script>
<?php
//蹦图提醒
$bounce=json_decode($_ZEAI['bounce'],true);
if($bounce['flag']['indexgg'] == 1){
	$bouncev = '';
	$bounceTip = 'cook_index_bounce'.YmdHis(ADDTIME,'d');
	if($_COOKIE[$bounceTip] != 'indexgg'){
		$bouncev ='indexgg';
		$picurl  = $_ZEAI['up2']."/".$bounce['indexgg_picurl'];
		$url     = $bounce['indexgg_url'];
	}
	if(!empty($bouncev)){
		if($_COOKIE[$bounceTip] != $bouncev){
			setcookie($bounceTip,$bouncev,null,"/",$_ZEAI['CookDomain']);
			?>
			<style>.div_pic img{width:100%;display:block;margin:0 auto}</style>
			<script>var index_divclose;setTimeout(function(){index_divclose=ZeaiM.div_pic({fobj:main,obj:index_bounce_box,w:320,h:360});},1500);</script>
			<div id="index_bounce_box" class="bounce_box bounce"><img src="<?php echo $picurl;?>" onClick="zeai.openurl('<?php echo $url;?>');"></div>
			<?php
		}
	}
}
//蹦图结束
require_once ZEAI.'m1/bottom.php';
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
		//$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> </span>';
		$tmp='<span class="jzbmT">报名还剩</span>';
		if ($day > 0){
			$outtime = $tmp."<span class=timestyle>$day</span>天";
		} else {
			$outtime = $tmp;
		}
		$outtime .= "<span class=timestyle>$hour</span>时<span class=timestyle>$minute</span>分";
	} else {
		$outtime = '　报名已经结束';
	}
	$outtime = '<font>'.$outtime.'</font>';
	return $outtime;
}
function getGpsUlist($data_latitude,$data_longitude) {
	global $nodatatips,$db,$_ZEAI,$cook_uid,$cook_sex;
	$SQL = " longitude<>'' AND flag=1 AND dataflag=1 ";
	if(ifint($cook_uid && !empty($cook_sex))){
		$SQL .= ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
	}
	$SQL = "SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,heigh,ROUND(6378.138*2*ASIN(SQRT(POW(SIN((".$data_latitude."*PI()/180-latitude*PI()/180)/2),2)+COS(".$data_latitude."*PI()/180)*COS(latitude*PI()/180)*POW(SIN((".$data_longitude."*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY distance LIMIT 10";
	$rt=$db->query($SQL);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows)break;
			$uid      = $rows['id'];
			$nickname = urldecode(dataIO($rows['nickname'],'out'));
			$sex      = $rows['sex'];
			$grade    = $rows['grade'];
			$photo_s  = $rows['photo_s'];
			$photo_f  = $rows['photo_f'];
			$areatitle= $rows['areatitle'];
			$birthday = $rows['birthday'];
			$job      = $rows['job'];
			$heigh    = $rows['heigh'];
			$distance = $rows['distance'];
			if ($distance<1000){
				$distance_str  = $distance.'m';
			}else{
				$distance_str  = intval($distance/1000).'km';
			}
			//
			$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
			$heigh_str    = ($heigh<=0)?'':$heigh.'cm ';
			$job_str      = (empty($job))?'':udata('job',$job).' ';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			$areatitle_str  = str_replace("不限","",$areatitle_str);
			$photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.getpath_smb($photo_s,'m'):'res/photo_m'.$sex.'.png';
			$sexbg      = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':'';
			
			$echo .= '<dl class="fadeInL" onClick="ulink('.$uid.')">';
			$echo .= '<img src="'.$photo_m_url.'"'.$sexbg.' class="m">';
			$echo .= '<dd>';
			$echo .= '<h4>'.$nickname.uicon($sex.$grade).'</h4>';
			$echo .= '<font>'.$birthday_str.$heigh_str.$job_str.$areatitle_str.'</font>';
			$echo .= '</dd>';
			$echo .= '<span><i class="ico">&#xe614;</i>'.$distance_str.'</span>';
			$echo .= '</dl>';
		}
		return $echo;
	}else{
		return $nodatatips;
	}
}
function getUlist($SQL) {
	global $nodatatips,$db,$_ZEAI,$_INDEX,$cook_uid,$cook_sex,$cook_grade;
	if(ifint($cook_uid && !empty($cook_sex)) && $_INDEX['iModuleU']==1){
		$SQL .= ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
	}
	$rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,photo_ifshow FROM ".__TBL_USER__." b WHERE flag=1 AND dataflag=1 AND photo_f=1 ".$SQL." ORDER BY refresh_time DESC LIMIT ".$_INDEX['iModuleU_num']);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		$switch = json_decode($_ZEAI['switch'],true);
		$blurclass = '';$lockstr = '';$ifblur=0;
		if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
			$lockstr = '<i class="ico lockico">&#xe61e;</i><div class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</div>';
			$ifblur=1;
		}
		$echo .= '<ul>';
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows)break;
			$uid      = $rows['id'];
			$nickname = urldecode(dataIO($rows['nickname'],'out'));
			$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
			$sex      = $rows['sex'];
			$grade    = $rows['grade'];
			$photo_s  = $rows['photo_s'];
			$photo_f  = $rows['photo_f'];
			$areatitle= $rows['areatitle'];
			$birthday = $rows['birthday'];
			$job      = $rows['job'];
			$photo_ifshow = $rows['photo_ifshow'];
			//
			$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
			$job_str      = (empty($job))?'':udata('job',$job).' ';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			$areatitle_str = str_replace("不限","",$areatitle_str);
			if($ifblur==1){
				$photo_m = 'blur';
			}else{
				$photo_m = 'm';
			}
			if($photo_ifshow==0 && $ifblur==0){
				$lockstr = '';
				$photo_m_url='res/photo_m'.$sex.'_hide.png';
			}else{
				$photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.getpath_smb($photo_s,$photo_m):'res/photo_m'.$sex.'.png';	
			}
			$echo .= '<li class="fadeInL" onClick="ulink('.$uid.','.$ifblur.')">';
			$vipj = ($grade>1)?'<img src="m1/img/vipj.png">':'';
			$echo .= '<p class="m'.$blurclass.'" style=\'background-image:url("'.$photo_m_url.'");\'>'.$vipj.'</p>';
			$echo .= '<h4>'.uicon($sex.$grade).$nickname.'</h4>';
			$echo .= '<font>'.$birthday_str.$job_str.$areatitle_str.'</font>';
			$echo .= $lockstr.'</li>';
			if ($i % 2 == 0){$echo .= '</ul>';if ($i != $total)$echo .= '<ul>';}
		}
		return $echo;
	}else{
		return $nodatatips;
	}
} 
ob_end_flush();  
?>

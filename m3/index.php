<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
if($submitok=='ajax_private'){
	$row = $db->ROW(__TBL_NEWS__,"content","id=1");
	$c=($row)?$row[0]:"<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容</div>";
	json_exit(array('flag'=>1,'c'=>$c));
}
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';
$up2 = $_ZEAI['up2'].'/';
$nav='home';
$parARR = array('index','near','vip','match','sex1','sex2');
$_ZEAI['pagesize']= 6;$_ZEAI['limit']=999999;
$SQL = " flag=1 AND photo_s<>'' AND photo_f=1 AND dataflag=1 AND kind<>4 AND nickname<>''";
$ORDER = " ORDER BY refresh_time DESC,id DESC";
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员～～</div>";
if(ifint($cook_uid && !empty($cook_sex)) && $_INDEX['iModuleU']==1){
	$SQL .= ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
}
$FLD = "id,sex,grade,nickname,photo_s,photo_f,birthday,pay,job,RZ,photo_ifshow";
switch ($ie) {
	case 'ajax_gps_save':
		if(!empty($latitude) && !empty($longitude) && ifint($cook_uid)){
			$db->query("UPDATE ".__TBL_USER__." SET latitude='$latitude',longitude='$longitude' WHERE id=".$cook_uid);
			json_exit(array('flag'=>1));
		}else{
			json_exit(array('flag'=>0));
		}
	break;
	case 'index':
		$SELECT = "SELECT ".$FLD." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
	break;
	case 'near':
		if(!ifint($cook_uid)){?><script>zeai.openurl('m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(HOST));</script><?php
			exit;
		}else{
			$row = $db->ROW(__TBL_USER__,"latitude,longitude","latitude<>'' AND longitude<>'' AND id=".$cook_uid,"num");
			if ($row){
				$data_latitude = $row[0];
				$data_longitude= $row[1];
			}else{?><script>iu_btn2Fn();</script><?php
				exit("<div class='nodatatips'><i class='ico'>&#xe614;</i>请开启手机GPS定位后并同意授权</div>".$data_latitude);
			}
			$SQL .= " AND longitude<>'' ";
			$SELECT = "SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,heigh,photo_ifshow,ROUND(6378.138*2*ASIN(SQRT(POW(SIN((".$data_latitude."*PI()/180-latitude*PI()/180)/2),2)+COS(".$data_latitude."*PI()/180)*COS(latitude*PI()/180)*POW(SIN((".$data_longitude."*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY distance,id DESC";
		}
	break;
	case 'vip':
		$SQL .= " AND grade>1";
		$SELECT = "SELECT ".$FLD." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
	break;
	case 'match':
		if(!ifint($cook_uid)){?><script>zeai.openurl('m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(HOST));</script><?php
			exit;
		}else{
			$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2","(flag=1 OR flag=-2) AND id=".$cook_uid,"name");
			if (!$row)exit($nodatatips);
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
				exit($nodatatips);
			}
			//生成SQL语句
			$SQL .= " AND id<>".$cook_uid;
			$SQL .= mate_diy_SQL();
			$SQL .= ($cook_sex == 2)?" AND sex=1 ":" AND sex=2 ";
			$SELECT = "SELECT ".$FLD." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
		}
	break;
	case 'sex1':
		$SQL .= " AND sex=1";
		$SELECT = "SELECT ".$FLD." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
	break;
	case 'sex2':
		$SQL .= " AND sex=2";
		$SELECT = "SELECT ".$FLD." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
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
		//	
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
					case 2:$tt  = '充值了'.$_ZEAI['loveB'];break;
					case 3:$tt  = '充值了余额';break;
					case 4:$tt  = '充值了活动报名费';break;
					case 7:$tt  = '进行了【认证】';break;
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
//banner
if (!empty($_INDEX['mBN_path1_s']) || !empty($_INDEX['mBN_path2_s']) || !empty($_INDEX['mBN_path3_s']) ){
	$ifbanner = true;
	$path1_s = $_INDEX['mBN_path1_s'];
	$path2_s = $_INDEX['mBN_path2_s'];
	$path3_s = $_INDEX['mBN_path3_s'];
	$path1_b = smb($path1_s,'b');
	$path2_b = smb($path2_s,'b');
	$path3_b = smb($path3_s,'b');
	$path1_url = (empty($_INDEX['mBN_path1_url']))?'javascript:;':$_INDEX['mBN_path1_url'];
	$path2_url = (empty($_INDEX['mBN_path2_url']))?'javascript:;':$_INDEX['mBN_path2_url'];
	$path3_url = (empty($_INDEX['mBN_path3_url']))?'javascript:;':$_INDEX['mBN_path3_url'];
	$banner3 = "";$upurl = $_ZEAI['up2']."/";$bnum=0;
	if (!empty($path1_s))$banner3.='<div class="topadvs_li" bj="0"><a href="'.$path1_url.'" target="_self"><img src="'.$up2.$path1_b.'" style="max-height:170px"></a></div>';
	if (!empty($path2_s))$banner3.='<div class="topadvs_li" bj="1"><a href="'.$path2_url.'" target="_self"><img src="'.$up2.$path2_b.'" style="max-height:170px"></a></div>';
	if (!empty($path3_s))$banner3.='<div class="topadvs_li" bj="2"><a href="'.$path3_url.'" target="_self"><img src="'.$up2.$path3_b.'" style="max-height:170px"></a></div>';
}else{$ifbanner = false;}
if(in_array($_GET['ie'],$parARR)){
		$total  = $db->COUNT(__TBL_USER__,$SQL);
		$totalP= ceil($total/$_ZEAI['pagesize']);
		if($submitok == 'ajax_ulist')exit(ajax_ulist_fn($totalP,$p));
	?>
    <div class="index-ubox" id="indexUlist"><?php echo ajax_ulist_fn($totalP,1);?></div>
    <script>
    var ie='<?php echo $ie; ?>';
    <?php
    if ($total > $_ZEAI['pagesize']){?>
        var ifmore=true,totalP = parseInt(<?php echo $totalP; ?>),p;
        main.onscroll = indexOnscroll;	
    <?php }else{?>
        var ifmore=false;
    <?php }?>
    indexLoad(indexUlist);
    </script>
<?php exit;}?>

<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $_INDEX['indexTitle'];?></title>
<meta name="Keywords" content="<?php echo $_INDEX['indexKeywords'];?>">
<meta name="Description" content="<?php echo $_INDEX['indexContent'];?>">
<?php echo HEADMETA; ?>
<meta name="x5-orientation" content="portrait">
<link href="res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="m1/css/m1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="m3/css/index.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
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
	var share_imgUrl= '<?php echo $up2.$_ZEAI['logo']; ?>?<?php echo $_ZEAI['cache_str'];?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
		wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
	});
    </script>
<?php }?>
</head>
<body>
<div class="itop huadong" id="itop">
    <a href="<?php echo HOST;?>" class="logo" title="<?php echo $_ZEAI['siteName'];?>"><img src="<?php echo $up2.$_ZEAI['logo'];?>?<?php echo $_ZEAI['cache_str'];?>"></a>
    <h1><?php echo $_ZEAI['siteName'];?></h1>
    <div class="iso" id="iso"><i class="ico">&#xe66c;</i><span>会员筛选</span></div>
</div>
<div id="main" class="indexmain huadong">
	<?php if ($ifbanner){ ?>
    <div id="topadvs" class="topadvs">
        <div class="topadvs_main"><?php echo $banner3; ?></div>
        <div class="ihu"></div>
        <div class="topadvs_ico" id="topadvs_ico"></div>
    </div>
	<div id="bblank"></div>
	<?php }
	if($_ZEAI['navkind']==2){
		$navdiy = json_decode($_ZEAI['navdiy'],true);$nn=0;
		foreach ($navdiy as $V){if($V['f']==0 || empty($V['t']) || empty($V['img']) || empty($V['url']))continue;$nn++;$newnavidy[]=$V;}
		if($nn % 4==0){$inavAW=25;}elseif($nn % 3==0){$inavAW=33;}elseif($nn % 2==0){$inavAW=50;}
		if (count($newnavidy) >= 1 && is_array($newnavidy)){echo '<style>.inav3diy a{width:'.$inavAW.'%}</style><div class="inav3diy">';foreach ($newnavidy as $V){?><a href="<?php echo dataIO($V['url'],'out');?>"><img src="<?php echo $_ZEAI['up2'].'/'.$V['img'];?>"><span><?php echo dataIO($V['t'],'out');?></span></a><?php }echo '</div>';}?>
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
		if( @in_array('dating',$navarr) || @in_array('video',$navarr) || @in_array('group',$navarr) || @in_array('hb',$navarr) || @in_array('party',$navarr) || @in_array('store',$navarr) || @in_array('shop',$navarr)  ){
			$navnum = count($newnavarr);
			$inavAW = ($navnum==3 || $navnum==6)?33:25;?>
			<style>.inav3 a{width:<?php echo $inavAW;?>%}</style>
			<div class="inav3">
				<?php if(@in_array('dating',$navarr)){?><a href="./?z=dating"><i class="dating"></i><span>约会</span></a><?php }?>
				<?php if(@in_array('video',$navarr)){?><a href="./?z=video"><i class="video"></i><span>视频</span></a><?php }?>
				<?php if(@in_array('shop',$navarr)){?><a href="<?php echo HOST;?>/m4/shop_index.php"><i class="ico2 shop"></i><span>商盟</span></a><?php }?>
				<?php if(@in_array('group',$navarr)){?><a href="./m1/group"><i class="group"></i><span>圈子</span></a><?php }?>
				<?php if(@in_array('hb',$navarr)){?><a href="./m1/hongbao"><i class="hb"></i><span>红包</span></a><?php }?>
				<?php if(@in_array('trend',$navarr)){?><a href="./?z=trend"><i class="trend"></i><span>交友圈</span></a><?php }?>
				<?php if(@in_array('hn',$navarr)){?><a onClick="page({g:'m1/hongniang.php',l:'hongniang'})"><i class="hn"></i><span>红娘服务</span></a><?php }?>
				<?php if(@in_array('party',$navarr)){?><a href="./?z=party"><i class="party"></i><span>交友活动</span></a><?php }?>
				<?php if(@in_array('article',$navarr)){?><a href="<?php echo HOST;?>/m1/article.php"><i class="article"></i><span>婚恋学堂</span></a><?php }?>
			</div>
    <?php }}
    if ($_INDEX['iMarquee'] == 1){?>
    <div class="iMarqueeBox">
        <h5><i></i><span>公告</span></h5>
        <div id="iMarquee" class="iMarquee">
            <li><img src="<?php echo $up2.$_ZEAI['logo'];?>?<?php echo $_ZEAI['cache_str'];?>"><font>欢迎来到<?php echo $_ZEAI['siteName'];?></font></li>
        </div>
    </div>
    <?php }?>
    <div class="iunav">
        <div class="tabmenu tabmenu_4" id="tabmenuIndex">
            <em>
            <?php if ($_INDEX['iModuleU'] == 2){?>
                <li id="iu_btn2_1" class="ed" data="m3/index.php?ie=index"><span>推荐</span></li>
                <li id="iu_btn2_2" data="m3/index.php?ie=sex1"><span>男生</span></li>
                <li id="iu_btn2_3" data="m3/index.php?ie=sex2"><span>女生</span></li>
                <li id="iu_btn2_4" data="m3/index.php?ie=match"><span>匹配</span></li>
            <?php }else{ ?>
                <li id="iu_btn1" class="ed" data="m3/index.php?ie=index" style="width:27%"><span>推荐</span></li>
                <li id="iu_btn2" data="m3/index.php?ie=near" style="width:27%"><span>附近</span></li>
                <li id="iu_btn3" data="m3/index.php?ie=vip" style="width:18%"><span class="vip ico2">&#xe655;</span></li>
                <li id="iu_btn4" data="m3/index.php?ie=match" style="width:27%"><span>匹配</span></li>
            <?php }?>
            </em>
            <i></i>
        </div>
        <div class="more" id="pushindex_btn"><a>置顶首页</a></div>
    </div>
    <div id="iList" style="clear:both;"></div>
</div>
<!-- -->
<?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); $kf_mob=dataIO($_ZEAI['kf_mob'],'out');?>
<a href="javascript:;" id="btmKefuBtn"><i class="ico">&#xe6a6;</i>客服</a>
<div id="btmKefuBox" class="my-subscribe_box"><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><h3>长按二维码添加客服微信<br>注册VIP享受红娘人工牵线</h3></div>
<script src="m3/js/index.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var ifbanner=<?php echo intval($ifbanner);?>,browser='<?php if(is_h5app()){echo'app';}else{echo (is_weixin())?'wx':'h5';}?>',HOST='<?php echo HOST;?>',iModuleU=<?php echo $_INDEX['iModuleU'];?>;
<?php if (!empty($e)){?>ZeaiM.page.load('m1/<?php echo $e;?>'+zeai.ajxext+'a=<?php echo $a;?>&i=<?php echo $i;?>',ZEAI_MAIN,'<?php echo $e;?>');<?php }?>
ZeaiM.tabmenu.init({obj:tabmenuIndex,showbox:o('iList')});
<?php if ($_INDEX['iModuleU'] == 2){?>
	setTimeout(function(){iu_btn2_1.click();},500);
<?php }else{ ?>
	setTimeout(function(){iu_btn1.click();},500);
<?php }
if ($_INDEX['iMarquee'] == 1){?>
	zeaiLoadBack=['nav','itop'];
	setTimeout(function(){zeai_iMarqueeFn();},2000);
<?php }?>
</script>


<?php if ($_INDEX['index_private'] == 1){?>
<div class="index_private" id="index_private">
    <div class="C">
        <h1>《个人隐私与用户协议》提示</h1><em id="index_privateC"></em>
        <div class="agreebox"><button class="btn size4 FL" type="button" onClick="index_privateFn(0)">不同意</button>
        <button class="btn size4 FR" type="button" onClick="index_privateFn(1)">同意</button></div>
    </div>
</div>
<script>
if(zeai.empty(localStorage.index_private)){
	setTimeout(function(){zeai.ajax({url:HOST+'/m3/index'+zeai.extname,data:{submitok:'ajax_private'}},function(e){var rs=zeai.jsoneval(e);
		if(rs.flag==1){zeai.mask({son:index_private,cancelBubble:'off'});index_privateC.html(html_decode(rs.c));}
	});},500);
	function index_privateFn(k){if(k==1){localStorage.index_private='WWWzeaiCN_private';}o('Mindex_private').parentNode.removeChild(o('Mindex_private'));}
}
</script>
<?php 
$bounce['flag']['indexgg']==0;//阻止崩图
}?>

<?php if ($ifbanner){ ?><script src="m3/js/jq183.js"></script><script src="m3/js/zeai_banner3.js?<?php echo $_ZEAI['cache_str'];?>"></script><?php }?>
<?php
//蹦图
$bounce=json_decode($_ZEAI['bounce'],true);
if($bounce['flag']['indexgg'] == 1){
	$bouncev = '';
	$bounceTip = 'cook_index_bounce'.YmdHis(ADDTIME,'d');
	if($_COOKIE[$bounceTip] != 'indexgg'){
		$bouncev ='indexgg';
		$picurl  = $up2.$bounce['indexgg_picurl'];
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
require_once ZEAI.'m3/bottom.php';
function ajax_ulist_fn($totalP,$p) {
	global $_ZEAI,$db,$nodatatips,$SELECT,$ie;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);
	if($totalP==0 && $p == 1)return $nodatatips;
	if ($p > $totalP)exit("end");
	$LIMIT = ($p == 1)?$_ZEAI['pagesize']:$_ZEAI['limit'];
	$rt = $db->query($SELECT." LIMIT ".$LIMIT);
	$total = $db->num_rows($rt);
	if ($p == 1){
		if ($total <= 0)return $nodatatips;
		$fort= $total;
	}else{
		if ($total <= 0)exit("end");
		$fort= $_ZEAI['pagesize'];
		$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);
	}
	$rows_ulist='';
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		if($ie=='index'){
		$srt=($p-1)*$_ZEAI['pagesize'];
		$AD=Zeai_pplAD($srt+$n);
		if(!empty($AD)){
			$img=$_ZEAI['up2'].'/'.$AD['img'];$url=urldecode($AD['url']);
			$rows_ulist.='<a uid="0" url="'.$url.'" class="pplad"><img class="small_big" src="'.$img.'"><div class="uinfo"></div></a>';
		}}
/*		if($ie=='near'){
			$distance = intval($rows['distance']);
			if($distance>100000)continue;
		}
*/		$rows_ulist .= rows_ulist($rows);
	}
	return $rows_ulist;
}	
function rows_ulist($rows) {
	global $_ZEAI,$_INDEX,$cook_uid,$cook_sex,$cook_grade,$ie;
	//
	$switch = json_decode($_ZEAI['switch'],true);
	$blurclass = '';$lockstr = '';$ifblur=0;
	if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
		$lockstr = '<i class="ico lockico">&#xe61e;</i><div class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</div>';
		$ifblur=1;
		$photo_b = 'blur';
	}else{
		$photo_b = ($_INDEX['waterfall_photo']=='b')?'b':'m';
	}
	//
	$uid           = $rows['id'];
	$sex           = $rows['sex'];
	$grade         = $rows['grade'];
	$nickname      = dataIO($rows['nickname'],'out');
	$photo_s       = $rows['photo_s'];
	$photo_f       = $rows['photo_f'];
	$age = getage($rows['birthday']);
	$RZ  = $rows['RZ'];
	$pay = $rows['pay'];
	$photo_ifshow = $rows['photo_ifshow'];
	$age_str = ($age>18)?$age.'岁':'';
	if(empty($pay) && empty($age)){
		$ifdata = false;
	}else{
		$pay_str = udata('pay',$pay).'/月';
		$job_str = udata('job',$rows['job']);
		$ifdata = true;
	}
	//
	if($photo_ifshow==0 && $ifblur==0){
		$lockstr = '';
		$photo_b_url='res/photo_m'.$sex.'_hide.png';
	}else{
		$photo_b_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.smb($photo_s,$photo_b):'res/photo_m'.$sex.'.png';	
	}
	//
	$echo .= '<a uid="'.$uid.'" sex="'.$sex.'" ><img class="small_big" src="'.$photo_b_url.'" />';/*class="small_big"*/
	$echo .= '<div class="uinfo">';
	$echo .= '<div class="nik"><span>'.uicon($sex.$grade).$nickname.'</span><font>'.$age_str.'</font></div>';
	if($ie=='near'){
		$areatitle= $rows['areatitle'];
		$distance = $rows['distance'];
		if ($distance<1000){
			$distance_str  = $distance.'m';
		}else{
			$distance_str  = intval($distance/1000).'km';
		}
		$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].'-'.$aARR[2];
		$areatitle_str = (empty($areatitle))?'':$areatitle;
		$areatitle_str  = str_replace("不限","",$areatitle_str);
		$echo.= '<div class="data"><span>'.$areatitle_str.'</span><span><i class="ico">&#xe614;</i>'.$distance_str.'</span></div>';
	}else{
		if($ifdata)$echo.= '<div class="data"><span>'.$pay_str.'</span><span>'.$job_str.'</span></div>';
	}
	$echo .= '</div>';
	$echo.= ($grade>1)?'<img src="m3/img/vipj.png" class="vipj">':'';
	if (!empty($RZ))$echo.= '<div class="payrz">'.RZ_html($RZ,'s','color').'</div>';
	$echo.= $lockstr.'</a>';
	return $echo;
}
ob_end_flush();  
?>
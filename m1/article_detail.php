<?php 
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if(!is_mobile())header("Location: ".Href('news',$id));
require_once ZEAI.'sub/conn.php';
/***********************主体入口***************************/
$id = intval($id);
$WZ = json_decode($_ZEAI['WZ'],true);
//
if($submitok== 'ajax_bbs_add'){
	if ($WZ['ifbbs'] != 1)json_exit(array('flag'=>0,'msg'=>'评论已关闭'));
	if(!iflogin())json_exit(array('flag'=>'nologin','msg'=>'请您先登录','jumpurl'=>wHref('article',$id)));
	if (empty($content))json_exit(array('flag'=>0,'msg'=>'请输入内容'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发表'));
	$content = dataIO($content,'in',1000);
	$flag     = ($WZ['bbs_ifsh']==1)?0:1;
	$flag_str = ($flag==0)?'，请等待我们审核':'';
	$db->query("INSERT INTO ".__TBL_NEWS_BBS__." (uid,fid,content,addtime,flag) VALUES ($cook_uid,$id,'$content',".ADDTIME.",$flag)");
	setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
	json_exit(array('flag'=>1,'msg'=>'发表成功'.$flag_str));
}elseif($submitok== 'ajax_iflogin'){
	if(!iflogin()){
		json_exit(array('flag'=>'nologin','msg'=>'请您先登录','jumpurl'=>wHref('article',$id)));
	}else{
		json_exit(array('flag'=>1));
	}
}elseif($submitok== 'ajax_agree'){
	$db->query("UPDATE ".__TBL_NEWS_BBS__." SET agree=agree+1 WHERE id=".$id);
	json_exit(array('flag'=>1,'msg'=>'点赞成功'));
}

$row = $db->ROW(__TBL_NEWS__,"kindtitle,title,content,addtime,click,ulist,path_s","flag=1 AND id=".$id,"name");
if ($row){
	$title     = trimhtml(dataIO($row['title'],'out'));
	$kindtitle = dataIO($row['kindtitle'],'out');
	$content   = dataIO($row['content'],'out');
	$content_wx   = trimhtml($content);
	$addtime   = $row['addtime'];
	$click     = $row['click'];
	$ulist     = $row['ulist'];
	$path_s    = $row['path_s'];
	$path_s_url = $_ZEAI['up2'].'/'.$path_s;
	$db->query("UPDATE ".__TBL_NEWS__." SET click=click+1 WHERE id=".$id);
}
$headertitle = $title.'-'.$kindtitle.'-';?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $headertitle;?></title>
<?php echo HEADMETA; ?>
<meta name="x5-orientation" content="portrait">
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/m1/css/m1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/m1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/m1/css/article.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php if($_ZEAI['mob_mbkind']==3){?><style>#backtop a,#btmKefuBtn{background-color:#FF6F6F}</style><?php }?>
</head>
<body>
<div class="article_detail">
	<?php if ($row){?>
	<div class="T">
		<h2><?php echo $title;?></h2>
		<h6><a href="<?php echo HOST;?>" class="logo" title="<?php echo $_ZEAI['siteName'];?>"><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo'];?>?<?php echo $_ZEAI['cache_str'];?>"><?php echo $_ZEAI['siteName'];?></a><span><?php echo date_str($addtime);?>　　阅读<?php echo $click;?></span></h6>
	</div>
    <?php if ($WZ['iftjU'] == 1){?>
    <ul class="detaiuser">
        <?php
		if(ifint($cook_uid && !empty($cook_sex))){
			$SQLwz = ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
		}
        $rt=$db->query("SELECT id,nickname,sex,photo_s,photo_f,birthday FROM ".__TBL_USER__." WHERE birthday<>'0000-00-00' AND  photo_s<>'' AND nickname<>'' AND dataflag=1 AND flag=1".$SQLwz." ORDER BY admtjtime DESC,refresh_time DESC LIMIT 5");
        $total = $db->num_rows($rt);
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'name');
            if(!$rows) break;
            $uid      = $rows['id'];
            $nickname = dataIO($rows['nickname'],'out');
            $sex      = $rows['sex'];
            $photo_s  = $rows['photo_s'];
            $photo_f  = $rows['photo_f'];
            $birthday = $rows['birthday'];
            $birthday_str = (getage($birthday)<=0)?'':' '.getage($birthday).'岁';
            $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
            $img_str = '<img src="'.$photo_s_url.'">';?>
            <li><a href="<?php echo wHref('u',$uid);?>"><?php echo $img_str; ?><h5><?php echo $nickname.'<br>'.$birthday_str; ?></h5></a></li>
        <?php }}?>
        <div class="clear"></div>
		<?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); $kf_mob=dataIO($_ZEAI['kf_mob'],'out');?>
        <div id="btmKefuBox" class="my-subscribe_box"><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><h3>长按二维码添加客服微信<br>注册VIP享受红娘人工牵线</h3></div>
        <div class="tj">缘分就在一瞬间<button type="button" class="btn size2 yuan" onclick="ZeaiM.div({obj:btmKefuBox,w:260,h:280});">联系客服牵线</button></div>
    </ul>
    <?php }?>
	<div class="C"><?php echo $content;?></div>
    <?php }else{echo"<div class='nodatatips' style='margin:0 auto'><br><br><i class='ico'>&#xe651;</i>暂时木有内容～～</div>";}?>
    
    <?php
	if ($WZ['ifpay'] == 1){
		if(!empty($ulist)){
			$ulistarr=explode(',',$ulist);
			$ulistnum=count($ulistarr);
		}
		$ifpay_num=explode(',',$WZ['ifpay_num']);
		?>
        <div class="detail_agree">
            第一次接受赞赏，亲，看着给啊
            <div class="button" id="agreebtn" aid="<?php echo $id;?>"><i class="ico">&#xe652;</i><font><?php echo $ulistnum;?></font><font>赞赏</font></div>
            <?php if ($ulistnum>0){?>
            <div class="ulist">
				<?php 
				$rt=$db->query("SELECT id,sex,photo_s,photo_f,photo_ifshow FROM ".__TBL_USER__." WHERE id IN (".$ulist.") AND flag=1 ORDER BY refresh_time DESC");
				$total = $db->num_rows($rt);
				if ($total > 0) {
					for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows) break;
					$uid3      = $rows['id'];
					$sex3      = $rows['sex'];
					$photo_s3  = $rows['photo_s'];
					$photo_f3  = $rows['photo_f'];
					$photo_ifshow3 = $rows['photo_ifshow'];
					$photo_s_url3= (!empty($photo_s3) && $photo_f3==1)?$_ZEAI['up2'].'/'.$photo_s3:HOST.'/res/photo_m'.$sex3.'.png';
					if($photo_ifshow3==0)$photo_s_url3=HOST.'/res/photo_m'.$sex3.'_hide.png';?>
            		<a href="<?php echo wHref('u',$uid3);?>"><img src="<?php echo $photo_s_url3;?>" ></a>
                <?php }}?>
            </div>
			<?php }?>
        </div>
        <div id="detail_agree_pay">
        	<div class="ul"><?php if (count($ifpay_num) >= 1 && is_array($ifpay_num)){foreach ($ifpay_num as $V) {?><li><b><?php echo $V;?></b>元</li><?php }}?></div>
            <img src="<?php echo HOST;?>/res/dsbg.png">
    	</div>
    <?php }?>
	<?php if ($WZ['iftjWZ'] == 1){?>
	<div class="detail_tj">
        <dl>
        <dt>推荐文章<a href="<?php echo wHref('article');?>">更多</a></dt>
            <?php 
            $rt2=$db->query("SELECT id,title,kindtitle,path_s,addtime FROM ".__TBL_NEWS__." WHERE id<>$id AND flag=1 ORDER BY px DESC,id DESC LIMIT 4");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {
                for($j=0;$j<$total2;$j++) {
                    $rows2 = $db->fetch_array($rt2,'name');
                    if(!$rows2) break;
                    $idd   = $rows2['id'];
                    $titlee=dataIO($rows2['title'],'out');?>
            <dd><a href="<?php echo wHref('article',$idd);?>"><?php echo $titlee;?></a></dd>
            <?php }}else{echo "<div class='nodatatips' style='margin:0 auto'><i class='ico'>&#xe651;</i>暂无推荐～～</div>";}?>
        </dl>
	</div>
    <?php }?>
    
	<?php if ($WZ['ifbbs'] == 1){?>
	<div class="detail_bbs">
        <dl>
        <dt>最新评论</dt>
            <?php 
            $rt2=$db->query("SELECT a.id,a.content,a.uid,a.addtime,a.agree,u.nickname,u.sex,u.grade,u.photo_s,u.photo_f,u.photo_ifshow,areatitle FROM ".__TBL_NEWS_BBS__." a,".__TBL_USER__." u WHERE a.fid=$id AND a.flag=1 AND a.uid=u.id ORDER BY a.id DESC");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {
                for($j=0;$j<$total2;$j++) {
                    $rows2 = $db->fetch_array($rt2,'name');
                    if(!$rows2) break;
                    $idd   = $rows2['id'];
                    $uid   = $rows2['uid'];
                    $agree = intval($rows2['agree']);
                    $addtime_str = date_str($rows2['addtime']);
                    $content=dataIO($rows2['content'],'out');
					//
					$sex      = $rows2['sex'];
					$photo_s  = $rows2['photo_s'];
					$photo_f  = $rows2['photo_f'];
					$photo_ifshow = $rows2['photo_ifshow'];
					$grade    = $rows2['grade'];
					$nickname = dataIO($rows2['nickname'],'out');
					$areatitle = dataIO($rows2['areatitle'],'out');
					$areatitle = explode(' ',$areatitle);
					$areatitle  = $areatitle[1]. $areatitle[2];
					$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
					$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
					if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
					?>
            		<dd>
            			<div class="nick"><a href="<?php echo wHref('u',$uid);?>"><img src="<?php echo $photo_s_url;?>" class="m"></a><b><?php echo uicon($sex.$grade).'<font>'.$nickname.'</font>';?></b><span clsid="<?php echo $idd;?>" class="agree"><font><?php echo $agree;?></font><i class="ico">&#xe652;</i></span></div>
                        <div class="content"><?php echo $content;?></div>
            			<div class="time"><?php echo $areatitle;?> · <?php echo $addtime_str;?></div>
            		</dd>
            <?php }}else{echo "<div class='nodatatips' style='margin:0 auto'><i class='ico'>&#xe651;</i>暂时木有评论～～</div>";}?>
        </dl>
	</div>
	<?php $cook_photo_s_url = (!empty($cook_photo_s))?$_ZEAI['up2'].'/'.$cook_photo_s:HOST.'/res/photo_m'.$cook_sex.'.png';?>
    <div class="detail_bbs_add">
        <img src="<?php echo $cook_photo_s_url;?>" onClick="zeai.openurl(HOST+'/?z=my')">
        <span id="bbsaddbtn"><font class="ico2">&#xe63f;</font><font>写评论</font></span>
        <i class="ico" id="bbsaddbtn2">&#xe644;<?php echo ($total2>0)?'<b>'.$total2.'</b>':'';?></i>
        <i class="ico" id="share">&#xe60f;</i>
    </div>
    <div id="share_mask" class="mask1"></div>
    <div id="share_box"><img src="<?php echo HOST;?>/res/shareico.png"></div>
    <form id="zeai_wz_bbs_form" class="detail_bbs_form">
    	<textarea class="textarea" name="content" id="content" placeholder="我想说两句...请文明发言~~"></textarea>
        <input type="hidden" name="id" value="<?php echo $id;?>">
        <input type="hidden" name="submitok" value="ajax_bbs_add">
    	<button type="button" class="btn size3 HONG2" id="bbsaddbtnsave">提交评论</button>
    </form>
	<?php }?>
</div>
<div id="Zeai_cn__PageBox"></div><div id="blankpage"></div>
<div id="backtop"><a href="#top" id="btmTopBtn"><i class="ico">&#xe60a;</i>顶部</a></div>
<?php if ($WZ['ifpay'] == 1){?><script src="<?php echo HOST;?>/api/zeai_PAY.js?<?php echo $_ZEAI['cache_str'];?>"></script><?php }?>
<script>var ifbbs=<?php echo intval($WZ['ifbbs']);?>,ifpay=<?php echo intval($WZ['ifpay']);?>,id=<?php echo intval($id);?>,title='<?php echo trimhtml($title);?>',return_url='<?php echo wHref('article',$id);?>',jumpurl='<?php echo wHref('article',$id);?>';</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	var share_article_detail_title = '<?php echo $title; ?>',
	share_article_detail_desc  = '<?php echo $content_wx; ?>',
	share_article_detail_link  = '<?php echo wHref('article',$id);?>',
	share_article_detail_imgurl= '<?php echo $path_s_url; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_article_detail_title,desc:share_article_detail_desc,link:share_article_detail_link,imgUrl:share_article_detail_imgurl});
		wx.onMenuShareTimeline({title:share_article_detail_title,link:share_article_detail_link,imgUrl:share_article_detail_imgurl});
	});
	</script>
<?php }?>
<script src="<?php echo HOST;?>/m1/js/article.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</body>
</html>
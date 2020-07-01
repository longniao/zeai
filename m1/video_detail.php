<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if (!ifint($fid))alert('参数错误','back');
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'m1/header.php';
$db->query("UPDATE ".__TBL_VIDEO__." SET click=click+1 WHERE id=".$fid);
$rtV = $db->query("SELECT a.id,a.uid,a.path_s,a.agree,a.click,b.sex,b.grade,b.nickname,b.photo_s,b.photo_f FROM ".__TBL_VIDEO__." a,".__TBL_USER__." b WHERE a.flag=1 AND a.uid=b.id AND b.flag=1 AND a.id=".$fid);
if($db->num_rows($rtV)){
	$rowV = $db->fetch_array($rtV,'name');
	$id            = $rowV['id'];
	$uid           = $rowV['uid'];
	$path_s        = $rowV['path_s'];
	$sex           = $rowV['sex'];
	$grade         = $rowV['grade'];
	$photo_s       = $rowV['photo_s'];
	$nickname      = trimhtml(dataIO($rowV['nickname'],'out'));
	$photo_f       = $rowV['photo_f'];
	$agree         = intval($rowV['agree']);
	$click         = intval($rowV['click']);
	$dst_s         = explode(".",$path_s);
	$dst_s         = $dst_s[0].".mp4";
	$dst_s         = $_ZEAI['up2'].'/'.$dst_s;
	$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
}
$rtGZ = "SELECT COUNT(*) FROM ".__TBL_GZ__." WHERE flag=1 AND senduid=a.uid AND uid=".$cook_uid;
$gzflag = ($db->COUNT(__TBL_GZ__,"flag=1 AND senduid=".$cook_uid." AND uid=".$uid))?'ed':'';
$nav='video';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
?>
<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
<script>
wx.config({debug:false,
	appId: '<?php echo $signPackage["appId"];?>',
	timestamp: <?php echo $signPackage["timestamp"];?>,
	nonceStr: '<?php echo $signPackage["nonceStr"];?>',
	signature: '<?php echo $signPackage["signature"];?>',
	jsApiList: ['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
});
var share_title = '<?php echo $nickname;?>的视频',
share_desc  = '我在这个网站发布了个人视频，来看看啊,别忘了给我点赞^_^ - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>',
share_link  = '<?php echo HOST; ?>/m1/video_detail.php?fid=<?php echo $id;?>',
share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
wx.ready(function () {
	wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
	wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
});
</script>
<?php }?>
<link href="<?php echo HOST; ?>/m1/css/video_detail.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<div class="main">
    <div class="back" onClick="window.history.go(-1)"><em class="ico S24">&#xe602;</em></div>
    <div class="rightArea">
    	<li><a href="<?php echo wHref('u',$uid);?>"><img src="<?php echo $photo_s_url;?>"/></a><span class="ico <?php echo $gzflag;?>" id="gz" uid="<?php echo $uid;?>">&#xe620;</span></li>
        <li id="agree" vid="<?php echo $id;?>"><em class="ico S24">&#xe652;</em><p><?php echo $agree;?></p></li>
        <li><em class="ico S24">&#xe643;</em><p><?php echo $click;?></p></li>
        <!--<li><em class="ico S24">&#xe615;</em></li>-->
    </div>
<div style="position:fixed;width: 100%;height: 100%;z-index:99" onclick="videoAutoPlay()"></div>
<video id="video" autoplay poster="<?php echo $_ZEAI['up2'].'/'.$path_s;?>" src="<?php echo $dst_s;?>" playsinline="true" webkit-playsinline="true" x-webkit-airplay="allow" airplay="allow" x5-video-player-type="h5" x5-video-player-fullscreen="true" x5-video-orientation="portrait" style="z-index: 1;position:fixed ;width:100%;height:100%">
<h5>您的系统不支持Video标签</h5>
</video>
</div>
<script>
o('agree').onclick = function (){
	var self=this;
	var em = self.children[0];
	zeai.ajax({url:HOST+'/m1/video'+zeai.extname,js:1,data:{submitok:'ajax_agree',fid:self.getAttribute("vid")}},function(e){var rs=zeai.jsoneval(e);
		if(rs.flag==1){
			if(!em.hasClass('ed')){
				em.addClass('ed');
				var p=self.lastChild;
				p.html(parseInt(p.innerHTML)+1);
				zeai.msg(rs.msg);
			}
		}
	});
}
o('gz').onclick=function(){
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_gz',uid:this.getAttribute("uid")}},function(e){
	var rs=zeai.jsoneval(e);zeai.msg(rs.msg);(rs.flag==1 && o('gz').addClass('ed')) || (rs.flag==0 && o('gz').removeClass('ed'))});
}
//function clickHandler(){alert("播放")o('video').play();}
function videoAutoPlay(){
	function playy(){o('video').play();}
	document.addEventListener("WeixinJSBridgeReady", function () {playy();}, false);
	document.addEventListener('YixinJSBridgeReady', function() {playy();}, false);
	o('video').play();
}
videoAutoPlay();
</script>
</body>
</html>

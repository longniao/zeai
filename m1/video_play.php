<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'m1/header.php';
if (!ifint($fid) && empty($vurl)){alert('参数错误','back');}
if(ifint($fid) && $fid>0){
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
}else{
	$dst_s=urldecode($vurl);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>播放视频</title>
</head>
<body>
<script>
document.addEventListener( "plusready", app_createVideoPlayer, false );
function app_createVideoPlayer(){
	createVideoPlayer0('<?php echo $dst_s;?>',1,1);
}
var player=null;
function createVideoPlayer0(video_src) {
	var autoplay = arguments[2] ? arguments[2]:0;
	var FullScreen = arguments[3] ? arguments[3]:0;
	var vplay_obj=plus.webview.currentWebview();
	var direction=0;
	if(!player){
		player = plus.video.createVideoPlayer('videoplayer', {
			src:video_src,
			top:'auto',
			left:'auto',
			width:plus.screen.resolutionWidth+2,
			height:plus.screen.resolutionHeight-23,
			position: 'static',
			controls:true,
			direction:direction
		});
	}else{
		player.setStyles({src:src,direction:direction});
	}
	vplay_obj.append(player);
	if(autoplay==1){player.play();player.requestFullScreen(direction);}

	player.addEventListener('fullscreenchange', function(e){
		if(!e.detail.fullScreen){
			player.stop();
			vplay_obj.close();
		}
	}, false);
	player.addEventListener('ended', function(e){
		player.exitFullScreen();
		player.stop();
		vplay_obj.close();
		
	}, false);
}

</script>
</body>
</html>
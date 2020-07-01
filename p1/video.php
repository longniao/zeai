<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if(is_mobile())header("Location: ".mHref('video'));
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_up.php';
$nav='video';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>会员视频_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/video.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="main video fadeInL">
	<div class="videoL">
    	<div class="box S5" style="margin-bottom:0">
        	<h1>会员视频</h1>
            <div class="videolist" id="list">
				<?php
				$rt=$db->query("SELECT a.id,a.path_s,a.flag,a.addtime,a.uid,U.sex,U.grade,U.nickname FROM ".__TBL_VIDEO__." a,".__TBL_USER__." U WHERE a.flag=1 AND a.uid=U.id ORDER BY a.id DESC");
                $total = $db->num_rows($rt);
				$data_photo_num = $total;
                if($total>0){
					$page_skin='4_yuan';$pagemode=4;$pagesize=12;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=0;$i<$pagesize;$i++) {
                        $rows = $db->fetch_array($rt,'num');
                        if(!$rows)break;
						$id     = $rows[0];
						$path_s = $rows[1];
						$flag   = $rows[2];
						$addtime= $rows[3];
						$uid     = $rows[4];
						$sex     = $rows[5];
						$grade   = $rows[6];
						$nickname=  dataIO($rows[7],'out');
						$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
						
						if ((ADDTIME - $addtime) > 300){
							$dst_s = $_ZEAI['up2'].'/'.$path_s;
							$cvs   = '<video class="zeaiVbox" id="zeaiVbox'.$id.'" controls="controls" controlslist="nodownload"><source>您浏览器版本太低，请升级</video><span class="play ico">&#xe600;</span>';
							$vplay   = 'zeaiplay("'.$id.'")';
							$content = '<em class="vbox"><strong onclick='.$vplay.'>'.$cvs.'</strong></em>';
						}else{
							$dst_s = HOST.'/res/videomaking.gif';	
							$dst_b = '';
							$vplay = 'javascript:;';
							$content = '';
						}
						$addtime= date_str($rows[3]);
						$path_s_url = $_ZEAI['up2'].'/'.$path_s;
						?>
                        <li value='<?php echo $id;?>'>
                            <p value="<?php echo $dst_s;?>" id="ZEAI_<?php echo $id;?>"><?php echo $content;?></p>
                            <h4><a href="<?php echo Href('u',$uid);?>" target="_blank"><?php echo uicon($sex.$grade).$nickname; ?></a></h4>
                            <h5><?php echo $addtime; ?></h5>
                        </li>
                    	<?php
					}
                    if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo '<br><br><br><br>'.nodatatips('暂时还没有人上传视频<br><br><a onclick="video_add();"class="size2 btn HONG">我要上传</a>');}
            	?>
			</div>
        </div>
	</div>
<?php 
if(iflogin()){
	$upstr='video_add();';
}else{
	$upstr="zeai.openurl('".HOST."/p1/login.php?jumpurl=".Href('video')."');";
}
?>
	<div class="videoR">
        <div class="box S5 addbox">
			<h1>上传视频</h1>
            <div>
                <a href="javascript:;" class="ed" onclick="<?php echo $upstr;?>"><i class="ico2">&#xea39;</i>　我要上传</a>
                <a href="<?php echo HOST;?>/p1/my_video.php">管理我的视频</a>
            </div>
		</div>
        <div class="box S5 U">
			<h1>推荐会员</h1>
            <div class="ulist" id="ulist">
            <?php
			$echo='';
            if(ifint($cook_uid && !empty($cook_sex))){
                $SQLu = ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
            }
            $ORDER = (empty($ORDER))?"ORDER BY refresh_time DESC":$ORDER;
            $rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,pay,love,heigh,photo_ifshow FROM ".__TBL_USER__." b WHERE kind<>4 AND flag=1 AND dataflag=1 AND photo_f=1 ".$SQLu." ".$ORDER." LIMIT 6");
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($i=1;$i<=$total;$i++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows)break;
                    $uid2      = $rows['id'];
                    $nickname = dataIO($rows['nickname'],'out');
                    $sex      = $rows['sex'];
                    $love     = $rows['love'];
                    $grade    = $rows['grade'];
                    $photo_s  = $rows['photo_s'];
                    $photo_f  = $rows['photo_f'];
                    $areatitle= $rows['areatitle'];
                    $birthday = $rows['birthday'];
                    $job      = $rows['job'];
                    $pay      = $rows['pay'];
                    $heigh    = $rows['heigh'];
                    $photo_ifshow = $rows['photo_ifshow'];
                    $nickname = (empty($nickname))?'uid:'.$uid:$nickname;
                    //
                    $birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
                    $heigh_str    = ($heigh<=0)?'':$heigh.'cm ';
                    $job_str      = (empty($job))?'':udata('job',$job).' ';
                    $pay_str      = (empty($pay))?'':udata('pay',$pay).' ';
                    $love_str      = (empty($love))?'':udata('love',$love).' ';
                    $photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.getpath_smb($photo_s,'m'):HOST.'/res/photo_m'.$sex.'.png';
					if($photo_ifshow==0)$photo_m_url=HOST.'/res/photo_m'.$sex.'_hide.png';
                    $sexbg      = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':' class="m"';
                    $echo .= '<li>';
                    $uhref = Href('u',$uid2);
                    $echo .= '<a href="'.$uhref.'" class="mbox">';
                    $echo .= '<p value="'.$photo_m_url.'"'.$sexbg.'></p>';
                    $echo .= '<em><span>'.$love_str.'</span><span>'.$job_str.'</span><span>'.$pay_str.'</span></em>';
                    $echo .= '<b>联系Ta</b>';
                    $echo .= '</a>';
                    $aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
                    $echo .= '<h4>'.uicon($sex.$grade).$nickname.'</h4>';
                    $echo .= '<h5>'.$birthday_str.$heigh_str.$areatitle.'</h5>';
                    $echo .= '</li>';
                }
                echo $echo;
            }else{
                echo nodatatips('暂时没有会员','s');
            }
            ?>
			<div class="clear"></div>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>
<script>var upVMaxMB = <?php echo $_UP['upVMaxMB']; ?>,up2='<?php echo $_ZEAI['up2'].'/';?>';</script>
<script src="<?php echo HOST;?>/p1/js/video.js"></script>
<?php require_once ZEAI.'p1/bottom.php';
ob_end_flush();
?>
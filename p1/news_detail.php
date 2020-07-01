<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if(is_mobile())header("Location: ".wHref('article',$fid));
require_once ZEAI.'sub/conn.php';
if(!ifint($fid))alert('暂无内容','-1');
$row = $db->ROW(__TBL_NEWS__,"kind,kindtitle,title,content,addtime,click,path_s","flag=1 AND id=".$fid,"name");
if ($row){
	$title     = trimhtml(dataIO($row['title'],'out'));
	$kindtitle = dataIO($row['kindtitle'],'out');
	$content   = dataIO($row['content'],'out');
	$addtime   = YmdHis($row['addtime'],'Ymd');
	$kind     = $row['kind'];
	$click     = $row['click'];
	$path_s    = $row['path_s'];
	$path_s_url = $_ZEAI['up2'].'/'.$path_s;
	$db->query("UPDATE ".__TBL_NEWS__." SET click=click+1 WHERE id=".$fid);
}else{alert('暂无内容','-1');}
?>
<?php
require_once ZEAI.'cache/udata.php';
$nav='news';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $title;?>_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/news.css?1" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="main news">
	<div class="newsL">
    	<div class="box S5" style="margin-bottom:0">
            <div class="news_detail">
                <div class="T">
                    <h2><?php echo $title;?></h2>
                    <h6><?php if ($kind>1){?>文章分类：<a href="<?php echo HOST.'/p1/news.php?t='.$kind;?>"><?php echo $kindtitle;?></a>　　<?php }?>发布时间：<?php echo $addtime;?>　　阅读：<i class="ico">&#xe643;</i> <?php echo $click;?></h6>
                </div>
                <div class="C">
                    <?php echo $content;?>
                    <div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a></div>
                </div>
                <div class="copy"><div class="linebox"><div class="line W50"></div><div class="title S12 C999 BAI">内容如有涉及侵权，请联系我们删除</div></div></div>
                <div class="clear"></div>
			</div>
        </div>
	</div>
	<div class="newsR">
        <div class="box S5">
			<h1>推荐会员</h1>
            <div class="ulist" id="ulist">
            <?php
			$echo='';
            if(ifint($cook_uid && !empty($cook_sex))){
                $SQLu = ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
            }
            $ORDER = (empty($ORDER))?"ORDER BY refresh_time DESC":$ORDER;
            $rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,pay,love,heigh,photo_ifshow FROM ".__TBL_USER__." b WHERE kind<>4 AND flag=1 AND dataflag=1 AND photo_f=1 ".$SQLu." ".$ORDER." LIMIT 8");
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
                    $photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.smb($photo_s,'m'):HOST.'/res/photo_m'.$sex.'.png';
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
<script src="<?php echo HOST;?>/p1/js/news.js"></script>
<?php require_once ZEAI.'p1/bottom.php';
ob_end_flush();
?>
<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if(is_mobile())header("Location: ".wHref('article'));
$t = (ifint($t,'1-7','1'))?$t:'';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
$nav='news';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>婚恋学堂_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/rex/www_esyyw_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/news.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/rex/www_esyyw_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="main news fadeInL">
	<div class="newsL">
    	<div class="box S5" style="margin-bottom:0">
        	<h1>婚恋学堂</h1>
            <div class="newskind">
				<?php
                $rt2=$db->query("SELECT id,title FROM ".__TBL_NEWS_KIND__." WHERE id>1 ORDER BY px DESC,id DESC");
                $total2 = $db->num_rows($rt2);
                if ($total2 > 0) {
                    $clss1=(empty($t))?' class="ed" ':'';
                    echo '<a href="'.HOST.'/p1/news.php"'.$clss1.'>全部分类</a>';
                    for($j=0;$j<$total2;$j++) {
                        $rows2 = $db->fetch_array($rt2,'num');
                        if(!$rows2) break;
                        $kindid   = $rows2[0];
                        $kindtitle=dataIO($rows2[1],'out');
                        $clss=($kindid==$t)?' class="ed"':'';
						if($kindid==$t){$clss=' class="ed"';$kindtitle2=$kindtitle;}else{
							$clss='';
						}
                        echo '<a href="'.HOST.'/p1/news.php?t='.$kindid.'" '.$clss.'>'.$kindtitle.'</a>';
                }}?>
			</div>
            <div class="newslist" id="list">
				<?php
                $SQL = (ifint($t))?" AND kind=".$t:'';
                $rt=$db->query("SELECT id,title,kind,kindtitle,path_s,addtime,content,click FROM ".__TBL_NEWS__." WHERE flag=1 AND id>2 AND path_s<>'' ".$SQL." ORDER BY px DESC,id DESC");
                $total = $db->num_rows($rt);
                if ($total > 0) {
                    $page_skin='4_yuan';$pagemode=4;$pagesize=8;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=1;$i<=$pagesize;$i++) {
                        $rows2 = $db->fetch_array($rt,'name');
                        if(!$rows2)break;
                        $id   = $rows2['id'];
                        $click = $rows2['click'];
                        $kind = $rows2['kind'];
                        $title  = dataIO($rows2['title'],'out');
                        $content= trimhtml(dataIO($rows2['content'],'out'));
						$content=gylsubstr($content,80,0,"utf-8",true);
                        $path_s    = $rows2['path_s'];
                        $addtime   = YmdHis($rows2['addtime'],'Ymd');
                        $kindtitle = dataIO($rows2['kindtitle'],'out');
                        $path_s_url=$_ZEAI['up2'].'/'.getpath_smb($path_s,'b');
                        $echo .= '<li onClick="dtlwzFn('.$id.')">';
                        $echo .= '<a href="'.Href('news',$id).'" target=_blank><p value="'.$path_s_url.'"></p></a>';
                        $echo .= '<em>';
                        $echo .= '<h4><a href="'.Href('news',$id).'" target=_blank>'.$title.'</a></h4>';
                        $echo .= '<h5>'.$content.'</h5>';
                        $echo .= '<a href="'.HOST.'/p1/news.php?t='.$kind.'" class="kind"><span>来自：</span>'.$kindtitle.'</a><font>发布时间：'.$addtime.'</font><span class="click" title="人气"><i class="ico">&#xe643;</i> <b>'.$click.'</b></span>';
                        $echo .= '</em>';
                        $echo .= '</li>';
                    }
                    echo $echo;
                    if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox zeaipagebox">'.$pagelist.'</div>';
                }else{echo '<br><br><br><br>'.nodatatips('暂无【'.$kindtitle2.'】内容');}?>
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
            $rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,pay,love,heigh,photo_ifshow FROM ".__TBL_USER__." b WHERE kind<>4 AND flag=1 AND dataflag=1 AND photo_f=1 ".$SQLu." ".$ORDER." LIMIT 10");
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
<script src="<?php echo HOST;?>/p1/js/news.js"></script>
<?php require_once ZEAI.'p1/bottom.php';
ob_end_flush();
?>
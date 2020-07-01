<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if(is_mobile())header("Location: ".wHref('party'));
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
$nav='party';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>征婚交友活动_相亲大会_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/cache/udata.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
<link href="<?php echo HOST;?>/p1/css/party.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="party fadeInL">
	<div class="partyL">
		<?php 
		$SQL = "";
		if (!empty($k)){
			$k=trimhtml($k);
			$k = dataIO($k,'in');
			$SQL = " AND title LIKE '%".$k."%'  ";
		}
        $rt=$db->query("SELECT id,title,hdtime,address,num_n,num_r,rmb_n,rmb_r,flag,jzbmtime,bmnum,signnum,bbsnum,path_s FROM ".__TBL_PARTY__." WHERE flag>0 AND path_s<>''".$SQL." ORDER BY px DESC");
        $total = $db->num_rows($rt);
        if($total>0){
            $page_skin='4_yuan';$pagemode=4;$pagesize=4;$page_color='#E83191';require_once ZEAI.'sub/page.php';
            for($i=0;$i<$pagesize;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows)break;
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
                $flagcls = ($flag==1)?'bmed':'bm';
                $flagstr = ($flag==1)?'<i class="ico">&#xe60e;</i> 立即报名':'查看报名';
				$phref=Href('party',$fid);
                ?>
                <li class="S5">
                    <a href="<?php echo $phref;?>" class="pic" target="_blank" style="background-image:url('<?php echo $path_b_url;?>')"><div class="djs"><?php echo party_djs($flag,$jzbmtime);?></div><div class="ybm">已报名<?php echo $bmnum;?>人</div></a>
                    <h2><a href="<?php echo $phref;?>"><?php echo $title;?></a></h2>
                    <h6><i class="ico time">&#xe634;</i><?php echo $hdtime;?></h6>
                    <h6><i class="ico nowrap">&#xe614;</i><?php echo $address;?></h6>
                    <a href="<?php echo $phref;?>" class="<?php echo $flagcls;?>"><?php echo $flagstr;?></a>
                </li>
                <?php 
            }
        }else{echo nodatatips('暂无活动');}
        ?>
    </div>
    <div class="partyR">
		<?php if (($p== 1 || empty($p)) && empty($k)   ){?>
		<div class="pRbox S5">
			<h1>活动动态<b></b></h1>
			<?php 
			$rt=$db->query("SELECT a.uid,a.addtime,b.title,b.id AS fid,U.sex,U.nickname,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_PARTY_USER__." a,".__TBL_PARTY__." b,".__TBL_USER__." U WHERE a.fid=b.id AND a.uid=U.id ORDER BY a.id DESC LIMIT 6");
			$total = $db->num_rows($rt);
			if($total>0){
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					$uid      = $rows['uid'];
					$nickname = dataIO($rows['nickname'],'out');
					$sex      = $rows['sex'];
					$fid      = $rows['fid'];
					$photo_s  = $rows['photo_s'];
					$photo_ifshow = $rows['photo_ifshow'];
					$addtime  = YmdHis($rows['addtime'],'mdHi');
					$title    = dataIO($rows['title'],'out');
					$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
					$sexbg       = (empty($photo_s))?' class="sexbg'.$sex.'"':'';
					$uhref        = Href('u',$uid);
					$fhref        = Href('party',$fid);
					?>
                    <ul>
                        <li class="t"><?php echo $addtime;?></li>
                        <li class="m"><a href="<?php echo $uhref;?>"><img src="<?php echo $photo_s_url;?>"<?php echo $sexbg;?>></a></li>
                        <li class="c"><?php echo $nickname;?><font> 报名了</font><a href="<?php echo $fhref;?>" target="_blank" class="h3"><?php echo $title;?></a></li>
                    </ul>
            		<?php
					if($i!=$total)echo '<div class="tline"><i></i></div>';
				}?>
            <?php }?>
		</div>
        <div class="pRbox">
        	<h1>优质会员</h1>
            <div class="ulist">
            <?php
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
                    $echo .= '<img src="'.$photo_m_url.'"'.$sexbg.'>';
                    $echo .= '<em><span>'.$love_str.'</span><span>'.$job_str.'</span><span>'.$pay_str.'</span></em>';
                    $echo .= '<b>联系Ta</b>';
                    $echo .= '</a>';
                    $aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
                    $echo .= '<h4>'.$nickname.'</h4>';
                    $echo .= '<h5>'.$birthday_str.$heigh_str.$areatitle.'</h5>';
                    $echo .= '</li>';
                }
                echo $echo;
            }else{
                echo nodatatips('暂时没有会员','s');
            }
            ?>
			</div>
        </div>
        <?php }?>
        <div class="pRbox" style="min-height:100px">
        	<h1>活动查询</h1>
            <form method="get" action="<?php echo HOST.'/p1/party.php';?>" name="ZEAI.cn.form1" id="GYLform1" class="sobox">
            <input name="k" type="text" class="input" maxlength="30" placeholder="请输入活动名称"><button type="submit" class="btn size3 HONG"><i class="ico">&#xe6c4;</i> 搜索</button>
            </form>
		</div>

	</div>
    <?php if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox zeaipagebox">'.$pagelist.'</div>'; ?>
</div>
<div class="clear"></div>
<?php require_once ZEAI.'p1/bottom.php';
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
		$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> 离截止报名还剩</span>';
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
ob_end_flush();
?>

<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
$nav='hongniang';$up2 = $_ZEAI['up2']."/";
?>
<!doctype html><html><head><meta charset="utf-8">
<title>红娘线下人工服务_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p2/css/p2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p2/css/hongniang.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p2/top.php';?>
<div class="hnbanner" style="background-image:url(<?php echo HOST;?>/p2/img/hnbanner.jpg)"></div>
<div class="promisebox">
    <div class="promise">
        <li><i class="ico">&#xe605;</i><span>线下人工服务</span><font>定制高端猎婚　打造一流服务</font></li>
        <li><i class="ico2">&#xe69d;</i><span>百分百实名验证</span><font>资料严格审核　安全隐私保护</font></li>
        <li><i class="ico2">&#xe601;</i><span>地区会员共享</span><font>线下优质资源　实名认证会员</font></li>
        <li><i class="ico2">&#xe678;</i><span>实体相亲安全保障</span><font>各色相亲会　高端约会场所</font></li>
    </div>
</div>
<div class="hnlistbox">
    <div class="hnlist">
        <h1>专业红娘顾问</h1>
        <h6>一对一会员服务，提供恋爱、婚姻、个人提升等全方位情感咨询服务</h6>
        <ul>
            <?php
            $rt=$db->query("SELECT id,sex,truename,path_s,title,pj_good,pj_bad FROM ".__TBL_CRM_HN__." HN WHERE ifwebshow=1 AND flag=1 AND path_s<>'' ORDER BY px DESC LIMIT 16");
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($j=1;$j<=$total;$j++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows)break;
                    $id       = $rows['id'];
                    $sex      = $rows['sex'];
                    $truename = dataIO($rows['truename'],'out',7);
                    $path_s   = $rows['path_s'];
                    $title    = trimhtml(dataIO($rows['title'],'out'));
                    $path_s_url = (!empty($path_s))?$up2.'/'.getpath_smb($path_s,'b'):HOST.'/res/photo_m'.$sex.'.png';
                    $pj_good  = intval($rows['pj_good']);
                    $pj_bad   = intval($rows['pj_bad']);
					$pj_good = ($pj_good==0)?1:$pj_good;
                    $pjbfb    = 100;
                    if ($pj_good>0 || $pj_bad>0){
                        $pj_ = $pj_good+$pj_bad;
                        $pj_ = $pj_good/$pj_;
                        $pjbfb = round($pj_,2)*100;
                    }
                    $unum = $db->COUNT(__TBL_USER__,"hnid=".$id);
                    ?>
                    <li>
                        <span class="bfb"><?php echo $pjbfb;?>%<font>好评</font></span>
                        <a href="<?php echo Href('hongniang',$id);?>" style="background-image:url('<?php echo $path_s_url;?>')" class="m"></a>
                        <h2><?php echo $truename;?></h2>
                        <span class="title"><?php echo $title;?></span>
                        <span class="num">已牵线<font><?php echo $unum; ?></font>人</span>
                        <a href="<?php echo Href('hongniang',$id);?>">委托牵线</a>
                    </li>
                    
                    <?php
                }
            }else{echo nodatatips('暂无红娘');}?>
            <div class="clear"></div>
        </ul>
    </div>
</div>

<div class="clear"></div>
<div class="fwlcbox">
	<div class="fwlc">
        <h1>红娘服务流程</h1>
        <h6>拥有庞大的专业红娘团队 受过婚恋心理培训专业红娘</h6>
        <img src="<?php echo HOST;?>/p2/img/fwlc.png">
    </div>
</div>
<div class="clear"></div>
<div class="utbox">
    <h1>优质线下会员</h1>
    <div class="kind">
    	<a class="ed">正在牵线</a><a>喜结良缘</a>
    </div>
	<div class="list" id="ulist">
		<?php 
        $SQL="flag=1 AND dataflag=1 ";$ORDER="ORDER BY refresh_time DESC";$field="id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,pay,love,RZ,heigh,b.photo_ifshow";
        $RTSQL = "SELECT ".$field." FROM ".__TBL_USER__." b WHERE ".$SQL." AND photo_s<>'' AND photo_f=1 ".$ORDER." LIMIT 10";
        $rt=$db->query($RTSQL);
        $total = $db->num_rows($rt);
        if ($total > 0) {
            $page_skin='4_yuan';$pagemode=4;$pagesize=20;$page_color='#E83191';require_once ZEAI.'sub/page.php';
            for($i=1;$i<=$pagesize;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows)break;
                $uid      = $rows['id'];
                $nickname = dataIO($rows['nickname'],'out');
                $sex      = $rows['sex'];
                $love     = $rows['love'];
                $grade    = $rows['grade'];
                $photo_s  = $rows['photo_s'];
                $photo_f  = $rows['photo_f'];
                $photo_ifshow  = $rows['photo_ifshow'];
                $areatitle= $rows['areatitle'];
                $birthday = $rows['birthday'];
                $job      = $rows['job'];
                $pay      = $rows['pay'];
                $RZ       = $rows['RZ'];
                $heigh    = $rows['heigh'];
                $nickname = (empty($nickname))?'uid:'.$uid:$nickname;
                //
                $birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
                $job_str      = (empty($job))?'':udata('job',$job).' ';
                $pay_str      = (empty($pay))?'':udata('pay',$pay).'/月'.' ';
                $love_str     = (empty($love))?'':udata('love',$love).' ';
                $heigh_str    = ($heigh>140)?$heigh.'cm ':'';
    
                $aARR = explode(' ',$areatitle);
                $areatitle_str = (empty($aARR[1]))?'':$aARR[1];
                $areatitle_str  = str_replace("不限","",$areatitle_str);
                $photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2']."/".getpath_smb($photo_s,'m'):HOST.'/res/photo_m'.$sex.'.png';
				if($photo_ifshow==0)$photo_m_url=HOST.'/res/photo_m'.$sex.'_hide.png';
				
                $sexbg      = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':' class="m"';
                $echo .= '<li>';
                $uhref = Href('u',$uid);
                $echo .= '<a href="'.$uhref.'" class="mbox" target="_blank">';
                $echo .= '<p value="'.$photo_m_url.'"'.$sexbg.'></p>';
                $echo .= '<em><span>'.$love_str.'</span><span>'.$job_str.'</span><span>'.$pay_str.'</span><span>'.$areatitle.'</span></em>';
                $echo .= '<b>联系Ta</b>';
                $echo .= '</a>';
                $echo .= '<a href="'.$uhref.'" target="_blank"><h4>'.$nickname.'</h4></a>';
				$echo .= '<h5>'.$birthday_str.$heigh_str.$job_str.$areatitle_str.'</h5>';
                $echo .= '</li>';
            }
            echo $echo;
        }else{
            echo '<br>'.nodatatips('没有符合条件的会员');
        }
        ?>    
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<div class="contactbox">
    <div class="contact"><i class="ico2">&#xe68a;</i><span>客服热线：<?php echo $_ZEAI['kf_tel']; ?></span></div>
</div>
<div class="clear"></div>
<script>
if(!zeai.empty(o('ulist'))){
zeai.listEach(zeai.tag(ulist,'p'),function(obj){
	var psrc=obj.getAttribute("value");
	obj.style.backgroundImage='url('+psrc+')';
		
});}
</script>
<?php require_once ZEAI.'p1/bottom.php';
ob_end_flush();
?>
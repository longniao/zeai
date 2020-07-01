<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/udata.php';
$up2 = $_ZEAI['up2']."/";


$submitok = ($submitok=='ajax_unav2_1')?'ajax_unav1':$submitok;
$submitok = ($submitok=='ajax_unav2_4')?'ajax_unav3':$submitok;

switch ($submitok) {
	case 'ajax_unav1':
		exit(getUlist($unavSQL,"ORDER BY RAND()"));
	break;
	case 'ajax_unav2':
		if(!ifint($cook_uid))exit('nologin');
		$SQL = " AND id<>$cook_uid";
		$row = $db->NUM($cook_uid,"areaid");
		if ($row){
			$data_areaid= $row[0];
			if(!empty($data_areaid)){
				$areaid = explode(',',$data_areaid);
				if (count($areaid) > 0){
					$m1 = $areaid[0];
					$m2 = $areaid[1];
					$m3 = $areaid[2];
				}
				$areaid = '';
				if (ifint($m1) && ifint($m2) && ifint($m3)){
					$areaid = $m1.','.$m2.','.$m3;
				}elseif(ifint($m1) && ifint($m2)){
					$areaid = $m1.','.$m2;
				}elseif(ifint($m1)){
					$areaid = $m1;
				}
				if (!empty($areaid))$SQL .= " AND areaid LIKE '%".$areaid."%' ";
			}
		}else{exit('nologin');}
		exit(getUlist($SQL,"ORDER BY RAND()"));
	break;
	case 'ajax_unav3':
		if(!ifint($cook_uid))exit('nologin');
		$row = $db->ROW(__TBL_USER__,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areatitle,mate_love,mate_house"," id=".$cook_uid);
		if(!$row)exit('nologin');
		$mate_age1      = intval($row['mate_age1']);
		$mate_age2      = intval($row['mate_age2']);
		$mate_heigh1    = intval($row['mate_heigh1']);
		$mate_heigh2    = intval($row['mate_heigh2']);
		$mate_pay       = intval($row['mate_pay']);
		$mate_edu       = intval($row['mate_edu']);
		$mate_areaid    = $row['mate_areaid'];
		$mate_love      = intval($row['mate_love']);
		$mate_house     = intval($row['mate_house']);
		//生成SQL语句
		$SQL .= " AND id<>".$cook_uid;
		$mate_areaid = explode(',',$mate_areaid);
		if (count($mate_areaid) > 0){
			$m1 = $mate_areaid[0];
			$m2 = $mate_areaid[1];
			$m3 = $mate_areaid[2];
		}
		$SQL .= ($cook_sex == 2)?" AND sex=1 ":" AND sex=2 ";
		$areaid = '';
		if (ifint($m1) && ifint($m2) && ifint($m3)){
			$areaid = $m1.','.$m2.','.$m3;
		}elseif(ifint($m1) && ifint($m2)){
			$areaid = $m1.','.$m2;
		}elseif(ifint($m1)){
			$areaid = $m1;
		}
		if (!empty($areaid))$SQL .= " AND areaid LIKE '%".$areaid."%' ";
		if (ifint($mate_age1))$SQL .= " AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= '$mate_age1' ) ";
		if (ifint($mate_age2))$SQL .= " AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <= '$mate_age2' ) ";
		if (ifint($mate_heigh1))$SQL .= " AND ( heigh >= '$mate_heigh1' ) ";
		if (ifint($mate_heigh2))$SQL .= " AND ( heigh <= '$mate_heigh2' ) ";
		if (ifint($mate_weigh1))$SQL .= " AND ( heigh >= '$mate_weigh1' ) ";
		if (ifint($mate_weigh2))$SQL .= " AND ( heigh <= '$mate_weigh2' ) ";
		if (ifint($mate_pay))$SQL .= " AND pay>='$mate_pay' ";
		if (ifint($mate_edu))$SQL .= " AND edu>='$mate_edu' ";
		if (ifint($mate_love))$SQL .= " AND love='$mate_love' ";
		if (ifint($mate_house))$SQL .= " AND house='$mate_house' ";
		//SQL语句结束
		exit(getUlist($SQL,"ORDER BY RAND()"));
	break;
	case 'ajax_unav4':
		exit(getUlist(" AND grade>1","ORDER BY RAND()"));
	break;
	case 'ajax_unav5':
		exit(getUlist(" AND kind=2","ORDER BY RAND()"));
	break;
	case 'ajax_unav2_2':
		exit(getUlist(" AND sex=1","ORDER BY RAND()"));
	break;
	case 'ajax_unav2_3':
		exit(getUlist(" AND sex=2","ORDER BY RAND()"));
	break;
}
//banner
if (!empty($_INDEX['pcBN_path1_s']) || !empty($_INDEX['pcBN_path2_s']) || !empty($_INDEX['pcBN_path3_s']) ){
	$ifbanner = true;
	$path1_s = $_INDEX['pcBN_path1_s'];
	$path2_s = $_INDEX['pcBN_path2_s'];
	$path3_s = $_INDEX['pcBN_path3_s'];
	$path1_b = $up2.getpath_smb($path1_s,'b');
	$path2_b = $up2.getpath_smb($path2_s,'b');
	$path3_b = $up2.getpath_smb($path3_s,'b');
	$path1_url = (!empty($_INDEX['pcBN_path1_url']))?' href="'.$_INDEX['pcBN_path1_url'].'" target="_blank"':'';
	$path2_url = (!empty($_INDEX['pcBN_path2_url']))?' href="'.$_INDEX['pcBN_path2_url'].'" target="_blank"':'';
	$path3_url = (!empty($_INDEX['pcBN_path3_url']))?' href="'.$_INDEX['pcBN_path3_url'].'" target="_blank"':'';
	$banner = "";
	if (!empty($path1_s)){
		$banner .= "<li><a ".$path1_url." style=\"background: url('".$path1_b."') no-repeat center top;\"></a></li>";
	}
	if (!empty($path2_s)){
		$banner .= "<li><a ".$path2_url." style=\"background: url('".$path2_b."') no-repeat center top;\"></a></li>";
	}
	if (!empty($path3_s)){
		$banner .= "<li><a ".$path3_url." style=\"background: url('".$path3_b."') no-repeat center top;\"></a></li>";
	}
	$bnclass = "";
}else{$ifbanner = false;}
//$ifbanner = false;
$nav='index';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $_INDEX['indexTitle'];?></title>
<meta name="Keywords" content="<?php echo $_INDEX['indexKeywords'];?>">
<meta name="Description" content="<?php echo $_INDEX['indexContent'];?>">
<meta name="generator" content="Zeai.cn V6.0.9" />
<link href="res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="p1/css/p1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="p1/css/index.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<script src="res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="p1/js/p1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="bannerbox">
    <div class="iBanner">
    <?php if ($ifbanner){ ?>
        <div id="banner"<?php echo $bnclass; ?>>
            <div id="pic_box"><?php echo $banner; ?></div>
            <div class="focus_box"><div id="focus_dot"></div></div>
			<div id="prev"></div><div id="next"></div>
        </div>
    <?php }?>
    </div>
    <div class="zeai_reg">
    	<?php if (ifint($cook_uid)){
			$Hi = (!empty($cook_nickname))?$cook_nickname:$cook_uname;
			$h=Ymdhis(ADDTIME,'H');if($h<11)$htitle = "早上好";else if($h<13)$htitle = "中午好";else if($h<18)$htitle ="下午好";else $htitle = "晚上好";
			?>
        <div class="loginbox ">
            <div class="p"><a href="<?php echo Href('u',$cook_uid);?>" target="_blank" title="我的个人主页"><?php echo $photo_str_index; ?></a></div>
            <h4><?php echo $Hi; ?>，<?php echo $htitle; ?></h4>
            <h5>欢迎来到<?php echo $_ZEAI['siteName']; ?></h5>
        	<a href="<?php echo HOST;?>/p1/my.php" class="btn size3 HONG3">进入个人中心</a>
            <a href="<?php echo HOST;?>/p1/my_info.php" class="a666">完善资料</a>|<a href="<?php echo HOST;?>/p1/my_info.php" class="a666">上传照片</a>|<a href="<?php echo HOST;?>/loginout.php" class="a666">退出登录</a>      
        </div>
        <?php }else{ ?>
    	<div class="regbox ">
			<div class="gyl">1分钟快速注册，幸福一辈子...</div>
            <form method="post" id="ZEAIFORM" action="p1/reg.php" class="reg">
                <div class="dl">
                    <div class="dt">性别</div>
                    <div class="dd">
                    <input type="radio" name="sex" id="sex1" class="radioskin" value="1" ><label for="sex1" class="radioskin-label"><i class="i2"></i><b class="W50 S14">男</b></label>
                    <input type="radio" name="sex" id="sex2" class="radioskin" value="2" checked><label for="sex2" class="radioskin-label"><i class="i2"></i><b class="W30 S14">女</b></label>
                    </div>
                </div>
                <div class="dl">
                	<div class="dt">生日</div>
                    <div class="dd reg2">
                        <ul class="birthday" id="birthday_">
                            <span>请选择　生日</span>
                            <li>
                                <div class="msk"></div>
                                <div class="Ybox" id="birthday_Ybox"></div>
                                <div class="Mbox" id="birthday_Mbox"></div>
                                <div class="Dbox" id="birthday_Dbox"></div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="dl">
                	<div class="dt">地区</div>
                    <div class="dd">
                    	<ul id="reg_area_" class="area"><span></span><li><div class="msk"></div><dl><dd></dd></dl></li></ul>
                    </div>
				</div>
                <div class="dl">
                	<div class="dt">婚况</div>
                    <div class="dd">
                    	<ul id="reg_love" class="love"><span></span><li></li></ul>
                    </div>
                </div>
                <div class="clear"></div>
                <button type="submit" id="indexRegBtn">快速注册</button>
                <input type="hidden" name="birthday" id="birthday" value="">
                <input type="hidden" name="love" id="love" value="">
                <input type="hidden" name="a1" id="reg_area_area1id" value="">
                <input type="hidden" name="a2" id="reg_area_area2id" value="">
                <input type="hidden" name="a3" id="reg_area_area3id" value="">
    		</form>
    	</div>
        <?php }?>
    </div>
</div>
<input type="hidden" name="areatitle" id="areatitle" value="">
<div class="sobox">
	<div class="so">
    <form action="<?php echo HOST;?>/p1/user.php" method="get" name="zeai_cnForm">
        <h2>我要找</h2>
        <ul id="so_sex"><span></span><li></li></ul>
        <h4>年龄</h4>
        <ul class="age" id="so_age1"><span></span><li><div class="msk"></div></li></ul>
        <h4 class="l0">～</h4>
        <ul class="age" id="so_age2"><span></span><li><div class="msk"></div></li></ul>
        <h4>地区</h4><ul class="area" id="so_area_"><span></span><li><div class="msk"></div><dl><dd></dd></dl></li></ul>
        <div class="checkbox"><input type="checkbox" name="ifphoto" id="ifphoto" class="checkskin" value="1" checked><label for="ifphoto" class="checkskin-label"><i></i><b class="W100 S14">有照片</b></label></div>
        <button type="submit" id="submit"><i class="ico">&#xe6c4;</i>搜索</button>
        <input type="hidden" name="form_mate_sex" id="sex" value="2">
        <input type="hidden" name="form_mate_age1" id="age1" value="20">
        <input type="hidden" name="form_mate_age2" id="age2" value="30">
        <input type="hidden" name="m1" id="so_area_area1id" value="">
        <input type="hidden" name="m2" id="so_area_area2id" value="">
        <input type="hidden" name="m3" id="so_area_area3id" value="">
        <input type="hidden" name="t" value="1">
        <div class="news"><div class="dt" title="本站公告"><i class="ico">&#xe654;</i></div><div class="dd"><?php 
        $rt=$db->query("SELECT id,title FROM ".__TBL_NEWS__." WHERE kind=1 AND id>2 ORDER BY px DESC LIMIT 2");
        if (!$db->num_rows($rt)){
            echo "<center class='C999'>暂无公告</center>";
        } else {
            $total = $db->num_rows($rt);
            for($j=1;$j<=$total;$j++) {
                $rows = $db->fetch_array($rt,'num');
                if(!$rows) break;
                $id=$rows[0];
                $title=gylsubstr(stripslashes(trimhtml($rows[1])),18);
                $href = Href('about_news',$id);
                echo '<a href="'.$href.'" target="_blank">'.$title.'</a>';
        }}?></div>
        </div>            
    </form>
	</div>
</div>
<div class="iubox S5">
	<h1><i class="ico">&#xe63e;</i><b>推荐会员</b></h1>
	<ul class="unav">
    	<?php if ($_INDEX['iModuleU_pc'] == 2){?>
            <li class="ed" id="unavbtn2_1">今日之星<b></b></li>
            <li id="unavbtn2_2">优质男会员<b></b></li>
            <li id="unavbtn2_3">优质女会员<b></b></li>
            <li id="unavbtn2_4">匹配我的<b></b></li>
        <?php }else{ ?>
            <li class="ed" id="unavbtn1">今日之星<b></b></li>
            <li id="unavbtn2">同城会员<b></b></li>
            <li id="unavbtn3">匹配我的<b></b></li>
            <li id="unavbtn4">VIP会员<b></b></li>
            <li id="unavbtn5">线下会员<b></b></li>
        <?php }?>
        <li id="unavbtn5"><a href="<?php echo Href('user');?>">查看更多</a></li>
    </ul>
	<a href="p1/my_push_index.php" class="apply">置顶首页</a>
    <div class="clear"></div>
    <ul class="list" id="ulist"><?php echo getUlist('');?></ul>
</div>
<?php if(@in_array('party',$navarr) || @in_array('dating',$navarr)){?>
<div class="party-dating-box">
    <div class="ptdt partybox S5">
		<?php if(@in_array('party',$navarr)){?>
        <h1><i class="ico">&#xe776;</i><b>交友活动</b></h1>
        <a href="<?php echo Href('party');?>" class="more">更多</a>
        <div class="clear"></div>
        <ul class="list">
            <?php
            //交友活动
            $rt=$db->query("SELECT id,title,address,flag,hdtime,jzbmtime,path_s,bmnum FROM ".__TBL_PARTY__." WHERE flag>0 AND path_s<>'' ORDER BY px DESC LIMIT 2");
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($j=1;$j<=$total;$j++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows)break;
                    $fid   = $rows['id'];
                    $path_s   = $rows['path_s'];
					$hdtime   = dataIO($rows['hdtime'],'out');
                    $jzbmtime = $rows['jzbmtime'];
                    $flag     = $rows['flag'];
                    $bmnum    = $rows['bmnum'];
                    $address = dataIO($rows['address'],'out');
                    $title = dataIO($rows['title'],'out');
                    $path_b=getpath_smb($path_s,'b');
                    $path_b_url=$_ZEAI['up2'].'/'.$path_b;
					$flagstr = ($flag==1)?'<i class="ico">&#xe60e;</i> 我要报名':'<i class="ico">&#xe60e;</i> 查看活动';
					$flagcls = ($flag==1)?'':' off';
					$pthref=Href('party',$fid);;
                    ?>
                    <li>
                        <a href="<?php echo $pthref;?>"><p style="background-image:url('<?php echo $path_b_url;?>');"></p></a>
                        <em>
                            <h4><a href="<?php echo $pthref;?>"><?php echo $title;?></a></h4>
                            <div class="djs"><?php echo party_djs($flag,$jzbmtime);?></div>
                            <h6 class="content">时间：<?php echo $hdtime;?></h6>
                            <h6 class="address">地点：<?php echo $address;?></h6>
                            <?php echo '<dl><dd>'. party_getBmUlist($fid).'<b class="ico">&#xe636;</b><span>已报名<font class="Cf00"> '.$bmnum.' </font>人</span></dd></dl>';;?>
                            <a href="<?php echo $pthref;?>" class="btn<?php echo $flagcls;?>"><?php echo $flagstr;?></a>
                        </em>
                    </li>
                    <?php
                }
            }else{echo nodatatips('暂无活动');}?>
        </ul>
        <?php }?>
    </div>
    <div class="ptdt datingbox S5">
    	<?php if(@in_array('dating',$navarr)){?>
        <h1><i class="ico">&#xe72e;</i><b>浪漫约会</b></h1>
        <a href="<?php echo Href('dating');?>" class="more">更多</a>
        <div class="clear"></div>
        <ul class="list">
            <?php
            //约会
            $rt=$db->query("SELECT a.id,a.uid,a.title,a.flag,a.yhtime,b.sex,b.photo_s,b.photo_f,b.photo_ifshow FROM ".__TBL_DATING__." a,".__TBL_USER__." b WHERE a.uid=b.id AND b.flag=1 AND a.flag>=1 ORDER BY a.yhtime DESC,a.px DESC LIMIT 5");
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($j=1;$j<=$total;$j++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows)break;
					$fid    = $rows['id'];
					$uid   = $rows['uid'];
					$title = dataIO($rows['title'],'out');
					$yhtime  = $rows['yhtime'];
					$sex  = $rows['sex'];
					$photo_f  = $rows['photo_f'];
					$photo_s  = $rows['photo_s'];
					$flag    = $rows['flag'];
					$photo_ifshow = $rows['photo_ifshow'];
					//
					$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_s'.$sex.'.png';
					if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
					$flagstr = ($flag==1)?'报名':'详情';
					$dthref=Href('dating',$fid);;
                    ?>
                    <li>
                        <a href="<?php echo $dthref;?>"><img src="<?php echo $photo_s_url;?>"></a>
                        <em>
                            <h4><a href="<?php echo $dthref;?>"><?php echo $title;?></a></h4>
                            <div class="djs"><?php echo party_djs($flag,$yhtime,'报名还剩','已经结束');?></div>
                            <a href="<?php echo $dthref;?>" class="dtbtn"><?php echo $flagstr;?></a>
                        </em>
                        <div class="clear"></div>
                    </li>
                    <?php
                }
            }else{echo nodatatips('暂无约会','s');}?>
        </ul>
        <?php }?>
    </div>
    <div class="clear"></div>
</div>
<?php }
if(@in_array('article',$navarr)){?>
<div class="article S5">
	<div class="ptdt">
        <h1><i class="ico">&#xe63d;</i><b>婚恋学堂</b></h1>
        <a href="<?php echo Href('news');?>" class="more">更多</a>
        <ul class="artnav">
			<?php
            $rt2=$db->query("SELECT id,title FROM ".__TBL_NEWS_KIND__." WHERE id>1 ORDER BY px DESC,id DESC LIMIT 8");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {
				echo '<a kindid="0" class="ed">热文推荐<b></b></a>';
				for($j=0;$j<$total2;$j++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$kindid   = $rows2[0];
					$kindtitle=dataIO($rows2[1],'out');
					echo '<a href="p1/news.php?t='.$kindid.'">'.$kindtitle.'<b></b></a>';
					if($j==0)$kind=$kindid;
				}
			}?>
        </ul>
        <ul class="list1">
        <?php
		//$SQL = (ifint($kind))?" AND kind=".$kind:'';
        $rt2=$db->query("SELECT id,title,kindtitle,path_s,addtime FROM ".__TBL_NEWS__." WHERE id>2 AND kind<>1 AND flag=1 ".$SQL." ORDER BY px DESC,id DESC LIMIT 15");
        $total2 = $db->num_rows($rt2);
        if ($total2 > 0) {
            for($j=1;$j<=$total2;$j++) {
                $rows2 = $db->fetch_array($rt2,'name');
                if(!$rows2) break;
                $id   = $rows2['id'];
                $title=trimhtml(dataIO($rows2['title'],'out'));
                $path_s    = $rows2['path_s'];
                $addtime   = YmdHis($rows2['addtime'],'Ymd');
                $kindtitle = dataIO($rows2['kindtitle'],'out');
                $path_b = getpath_smb($path_s,'b');
                $path_b_url=$_ZEAI['up2'].'/'.$path_b;
                $path_s_url=$up2.'/'.$path_s;
				$li3cls = ($j <4)?' class="li3"':'';
				$newshref=Href('news',$id);
                ?>
                <li<?php echo $li3cls;?>>
                	<?php if ($j <4){?>
                		<a href="<?php echo $newshref;?>" style="background:url('<?php echo $path_b_url;?>')center top/100% 100%  no-repeat ;">
                        	<span><?php echo $title;?></span>
                        </a>
                    <?php }else{?>
                    	<a href="<?php echo $newshref;?>"><?php echo $title;?></a>
					<?php }?>
                </li>
		<?php }}else{echo nodatatips('暂无文章');}?>
        </ul>
        <ul class="list2">
			<h2>人气排行榜</h2>
			<?php
            //$SQL = (ifint($kind))?" AND kind=".$kind:'';
            $rt2=$db->query("SELECT id,title,click FROM ".__TBL_NEWS__." WHERE id>2 AND kind<>1 AND flag=1 ".$SQL." ORDER BY click DESC,id DESC LIMIT 7");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {
                for($j=1;$j<=$total2;$j++) {
                    $rows2 = $db->fetch_array($rt2,'name');
                    if(!$rows2) break;
                    $id   = $rows2['id'];
                    $click= $rows2['click'];
                    $title=trimhtml(dataIO($rows2['title'],'out'));
					$newshref=Href('news',$id);
                    ?>
                    <li<?php echo $li3cls;?>>
                         <a href="<?php echo $newshref;?>"><b><?php echo $j;?></b><em><?php echo $title;?></em><span><?php echo $click;?></span><i class="ico">&#xe643;</i></a>
                    </li>
            <?php }}else{echo nodatatips('暂无文章','s');}?>
		</ul>        
    </div>
</div>
<?php }
if(@in_array('hn',$navarr)){?>
<div class="hongniang S5">
	<div class="ptdt">
        <h1><i class="ico">&#xe605;</i><b>红娘顾问</b></h1>
        <a href="<?php echo Href('hongniang');?>" class="more">更多</a>
 		<div class="clear"></div>
        <ul class="list">
            <?php
            //红娘
            $rt=$db->query("SELECT id,sex,truename,path_s,title,pj_good,pj_bad FROM ".__TBL_CRM_HN__." HN WHERE ifwebshow=1 AND flag=1 AND path_s<>'' ORDER BY px DESC LIMIT 5");
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
					$pjbfb    = 100;
					if ($pj_good>0){
						$pj_ = $pj_good+$pj_bad;
						$pj_ = $pj_good/$pj_;
						$pjbfb = round($pj_,2)*100;
					}
					$unum = $db->COUNT(__TBL_USER__,"hnid=".$id);
					$hnhref=Href('hongniang',$id);
                    ?>
                    <li onClick="zeai.openurl('<?php echo $hnhref;?>')">
                        <a href="<?php echo $hnhref;?>"><p style="background-image:url('<?php echo $path_s_url;?>');"></p></a>
                        <h3><?php echo $truename;?></h3>
                        <h5><?php echo $title;?></h5>
                        <em></em>
                        <a href="<?php echo $hnhref;?>"><b>委托Ta牵线</b></a>
                        <font>名下会员：<?php echo $unum; ?>人</font>
                        <span>好评率：<?php echo $pjbfb; ?>%</span>
                    </li>
                    <?php
                }
            }else{echo nodatatips('暂无红娘');}?>
        </ul>
    </div>
</div>
<div class="clear"></div>
<?php }
$seo_area = json_decode($_INDEX['seo_area'],true);
if(@is_array($seo_area) && @count($seo_area)>0){echo '<div class="seoarea">'.seo_area_out().'</div>';}
?>
<script src="cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="res/zeai_banner.js"></script>
<script src="p1/js/Zeai_birthday.js"></script>
<script>var iModuleU_pc=<?php echo $_INDEX['iModuleU_pc'];?></script>
<script src="p1/js/index.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script><?php if ($ifbanner){ ?>iBannerFn(parseInt(document.body.scrollWidth));<?php }?></script>
<?php
require_once ZEAI.'p1/bottom.php';
function getUlist($SQL,$ORDER="") {
	global $nodatatips,$db,$_ZEAI,$cook_uid,$cook_sex,$up2,$_INDEX;
	if(ifint($cook_uid && !empty($cook_sex)) && $_INDEX['iModuleU_pc'] == 1){
		$SQL .= ($cook_sex==2)?" AND sex=1 ":" AND sex=2 ";
	}
	$ORDER = (empty($ORDER))?"ORDER BY refresh_time DESC,id DESC":$ORDER;
	$rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,pay,love,photo_ifshow FROM ".__TBL_USER__." b WHERE kind<>4 AND flag=1 AND dataflag=1 AND photo_f=1 ".$SQL." ".$ORDER." LIMIT ".$_INDEX['iModuleU_pc_num']);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		$switch = json_decode($_ZEAI['switch'],true);
		$lockstr = '';$ifblur=0;
		if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
			$lockstr = '<i class="ico lockico">&#xe61e;</i><div class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</div>';
			$ifblur=1;
		}
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows)break;
			$uid      = $rows['id'];
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
			$photo_ifshow = $rows['photo_ifshow'];
			$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
			//
			$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
			$job_str      = (empty($job))?'':udata('job',$job).' ';
			$pay_str      = (empty($pay))?'':udata('pay',$pay).' ';
			$love_str      = (empty($love))?'':udata('love',$love).' ';
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			$areatitle_str  = str_replace("不限","",$areatitle_str);
			//
			if($ifblur==1){
				$photo_m = 'blur';
			}else{
				$photo_m = 'm';
			}
			//
			if($photo_ifshow==0 && $ifblur==0){
				$lockstr = '';
				$photo_m_url='res/photo_m'.$sex.'_hide.png';
			}else{
				$photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.smb($photo_s,$photo_m):'res/photo_m'.$sex.'.png';	
			}
			//
			$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			$echo .= '<li>';
			$uhref = Href('u',$uid);
			$echo .= '<a href="'.$uhref.'" class="mbox">';
			$echo .= '<p style="background-image:url(\''.$photo_m_url.'\');"'.$sexbg.'>'.$lockstr.'</p>';
			$echo .= '<em><span class="grade sexbg'.$sex.'">'.uicon($sex.$grade,4).'</span><span>'.$love_str.$pay_str.'</span></em>';
			$echo .= '<b>联系Ta</b>';
			$echo .= '</a>';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$echo .= '<h4>'.uicon($sex.$grade).$nickname.'</h4>';
			$echo .= '<h5>'.$birthday_str.$job_str.$areatitle.'</h5>';
			$echo .= '</li>';
		}
		return $echo;
	}else{
		return nodatatips('暂时没有会员');
	}
} 
function party_djs($flag,$jzbmtime,$jzbmtitle='离截止报名还剩',$jzbmtitle2='报名已经结束') {
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
		$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> '.$jzbmtitle.'</span>';
		if ($day > 0){
			$outtime = $tmp."<span class=timestyle>$day</span>天";
		} else {
			$outtime = $tmp;
		}
		$outtime .= "<span class=timestyle>$hour</span>时<span class=timestyle>$minute</span>分";
	} else {
		$outtime = '<b>'.$jzbmtitle2.'</b>';
	}
	$outtime = '<font>'.$outtime.'</font>';
	return $outtime;
}
function party_getBmUlist($fid) {
	global $db,$_ZEAI;
	$rt=$db->query("SELECT U.sex,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_PARTY_USER__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC LIMIT 4");
	$echo = '';
	WHILE ($rows = $db->fetch_array($rt,'num')){
		$sex      = $rows[0];
		$photo_s  = $rows[1];
		$photo_f  = $rows[2];
		$photo_ifshow = $rows[3];
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
		if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
		$echo.='<img src="'.$photo_s_url.'"'.$sexbg.'>';
	}
	return $echo;
}
?>
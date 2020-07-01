<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
$t = (ifint($t,'1-7','1'))?$t:0;
switch ($t){ 
	case 1:$tbody = "喝茶小叙";break;
	case 2:$tbody = "共进晚餐";break;
	case 3:$tbody = "相约出游";break;
	case 4:$tbody = "看电影";break;
	case 5:$tbody = "欢唱K歌";break;
}
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
$nav='dating';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $tbody; ?>约会_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/rex/www_esyyw_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/dating.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/rex/www_esyyw_cn.js"></script>
<script src="<?php echo HOST;?>/cache/udata.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
<script src="<?php echo HOST;?>/cache/areadata.js"></script>
<script src="<?php echo HOST;?>/res/select3.js"></script>
<script>var nulltext = '请选择';</script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="main dating fadeInL">
	<div class="datingL">
    	<div class="list box S5" style="margin-bottom:0">
            <div class="datingkind">
            <div class="kind">
                <?php if ($t == 6 || $t == 7 || $t == 8){ ?>
                <a href="###" class="ed"><i class="ico2">&#xe609;</i><span>约会搜索</span></a>
                <?php }else{ ?>
                <a href="<?php echo HOST;?>/p1/dating.php"<?php echo ($t==0 || empty($t))?' class="ed"':''; ?>><i class="ico2">&#xe609;</i><span>全部约会</span></a>
                <?php }?>
                <a href="<?php echo HOST;?>/p1/dating.php?t=1"<?php echo ($t==1)?' class="ed"':''; ?>><i class="ico2">&#xe60e;</i><span>喝茶小叙</span></a>
                <a href="<?php echo HOST;?>/p1/dating.php?t=2"<?php echo ($t==2)?' class="ed"':''; ?>><i class="ico2">&#xe6a9;</i><span>共进晚餐</span></a>
                <a href="<?php echo HOST;?>/p1/dating.php?t=3"<?php echo ($t==3)?' class="ed"':''; ?>><i class="ico2">&#xe656;</i><span>相约出游</span></a>
                <a href="<?php echo HOST;?>/p1/dating.php?t=4"<?php echo ($t==4)?' class="ed"':''; ?>><i class="ico2">&#xe600;</i><span>看电影</span></a>
                <a href="<?php echo HOST;?>/p1/dating.php?t=5"<?php echo ($t==5)?' class="ed"':''; ?>><i class="ico2">&#xe739;</i><span>欢唱K歌</span></a>
                <div class="clear"></div>
            </div>
			</div>
            <div class="clear"></div>
		<?php 
		$tsql = "";
		$tmpsort   = " ORDER BY b.flag,b.px DESC,b.id DESC ";
		switch ($t){ 
			case 1:$tsql = " b.datingkind=1 AND ";break;
			case 2:$tsql = " b.datingkind=2 AND ";break;
			case 3:$tsql = " b.datingkind=3 AND ";break;
			case 4:$tsql = " b.datingkind=4 AND ";break;
			case 5:$tsql = " b.datingkind=5 AND ";break;
			case 6:$k  = trimhtml($k);$tsql = " b.title LIKE '%".$k."%' AND ";break;
			case 7:
				$tsql .= (ifint($Ssex,'1-3','1'))?" a.sex=$Ssex AND ":'';
				if (ifint($Syhtime,'1-3','1')){
					if ($Syhtime == 1){
						$Syhtime2 = 86400*3;
					} elseif ($Syhtime == 2){
						$Syhtime2 = 86400*7;
					} elseif ($Syhtime == 3){
						$Syhtime2 = 86400*30;
					}
					$tsql .= " ( b.yhtime < (UNIX_TIMESTAMP()+".$Syhtime2.") AND b.yhtime>UNIX_TIMESTAMP() ) AND ";
				}
				$tsql .= (ifint($Sdatingkind,'1-5','1'))?" b.datingkind=$Sdatingkind AND ":'';
				$tsql .= (ifint($Sprice,'1-4','1'))?" b.price=$Sprice AND ":'';
				$tsql .= (ifint($Smaidian,'1-3','1'))?" b.maidian=$Smaidian AND ":'';
				$areaid = '';
				if (!empty($w1) && !empty($w2) && !empty($w3)){
					$areaid = $w1.','.$w2.','.$w3;
				}elseif(!empty($w1) && !empty($w2)){
					$areaid = $w1.','.$w2;
				}elseif(!empty($w1)){
					$areaid = $w1;
				}
				if (!empty($areaid))$tsql .= " b.areaid LIKE '%".$areaid."%' AND ";
				//
			break;
			case 8:$tsql = " a.id=".$uid." AND ";break;
		}
		$rt=$db->query("SELECT a.sex,a.grade,a.nickname,a.photo_s,a.photo_f,a.birthday,a.areatitle,a.photo_ifshow,b.id,b.uid,b.datingkind,b.title,b.price,b.yhtime,b.maidian,b.content,b.bmnum,b.click,b.flag,b.sex AS mate_sex FROM ".__TBL_USER__." a,".__TBL_DATING__." b WHERE ".$tsql." b.flag>0 AND b.uid=a.id AND a.flag=1 ".$tmpsort);
		$total = $db->num_rows($rt);
		if ($total > 0) {
            $page_skin='4_yuan';$pagemode=4;$pagesize=5;$page_color='#E83191';require_once ZEAI.'sub/page.php';
			for($i=1;$i<=$pagesize;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows)break;
				$uid      = $rows['uid'];
				$nickname = dataIO($rows['nickname'],'out');
				$sex2     = $rows['sex'];
				$grade    = $rows['grade'];
				$photo_s  = $rows['photo_s'];
				$photo_f  = $rows['photo_f'];
				$areatitle= $rows['areatitle'];
				$birthday = $rows['birthday'];
				$photo_ifshow = $rows['photo_ifshow'];
				$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
				$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
				$aARR = explode(' ',$areatitle);
				$areatitle_str = (empty($aARR[1]))?'':$aARR[1].$aARR[2];
				$areatitle_str  = str_replace("不限","",$areatitle_str);
				//
				$fid        = $rows['id'];
				$datingkind = $rows['datingkind'];
				$title      = dataIO($rows['title'],'out');
				$price      = $rows['price'];
				$yhtime     = $rows['yhtime'];
				$maidian    = $rows['maidian'];
				$content    = trimhtml(dataIO($rows['content'],'out'));
				$content    = gylsubstr($content,40,0,'utf-8',true);
				$bmnum      = $rows['bmnum'];
				$click      = $rows['click'];
				$flag       = $rows['flag'];
				$mate_sex   = $rows['mate_sex'];
				//
				$photo_m     = getpath_smb($photo_s,'m');
				$photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_m:HOST.'/res/photo_m'.$sex2.'.png';
				if($photo_ifshow==0)$photo_m_url=HOST.'/res/photo_m'.$sex2.'_hide.png';
				$acls = '';$atitle = '查看约会';
				if ($flag == 1){
					$d1  = ADDTIME;
					$d2  = $yhtime;
					$totals  = ($d2-$d1);
					$day     = intval( $totals/86400 );
					$hour    = intval(($totals % 86400)/3600);
					$hourmod = ($totals % 86400)/3600 - $hour;
					$minute  = intval($hourmod*60);
					if (($totals) > 0) {
						if ($day > 0){
							$outtime = "报名还有<span>$day</span>天";
						} else {
							$outtime = "还有<font>$hour</font>小时<font>$minute</font> 分";
						}
						$acls = ' class="ed"';
						$atitle = '<i class="ico2">&#xe613;</i> 我要报名';
					} else {
						$outtime = "约会已结束";
						$db->query("UPDATE ".__TBL_DATING__." SET flag=2 WHERE id=".$fid);
					}
				} else {
					$outtime = "约会已结束";
				}
				$showhref = Href('dating',$fid);
		?>
        <dl>
        	<dt><a href="<?php echo Href('u',$uid); ?>" target="_blank">
            	<p style="background-image:url('<?php echo $photo_m_url;?>')" class="<?php echo 'sexbg'.$sex2;?>"></p>
                <div class="uinfo">
                    <h5><?php echo uicon($sex2.$grade).$nickname; ?></h5>
                    <h5><?php echo $birthday_str.' '.$areatitle_str; ?></h5>
            	</div>
            </a></dt>
            <dd>
                <h2><a href="<?php echo $showhref; ?>"><?php echo $title; ?></a></h2>
                <li><font>约会类型：</font> <?php
	switch ($datingkind){ 
	case 1:echo "喝茶小叙";break;
	case 2:echo "共进晚餐";break;
	case 3:echo "相约出游";break;
	case 4:echo "看电影";break;
	case 5:echo "欢唱K歌";break;
	case 6:echo "其他";break;
	default:echo "约会类型不限";break;
	}?></li>
                <li><font>约会对象：</font> <?php if ($mate_sex >0){echo udata('sex',$mate_sex).'性';}else{echo'不限';} ?></li>
                <li><font>费用预算：</font> <?php
	switch ($price){ 
	case 1:echo "100元以下";break;
	case 2:echo "100～300元";break;
	case 3:echo "300--500元";break;
	case 4:echo "500元以上";break;
	default :echo "约会费用不限";break;
	}?></li>
                <li><font>谁来买单：</font> <?php
	switch ($maidian){ 
	case 1:echo "我来买单";break;
	case 2:echo "应约人买单";break;
	case 3:echo "AA制";break;
	default :echo "谁买单无所谓";break;
	}?></li>
                <li><font>见面时间：</font> <?php echo YmdHis($yhtime,'YmdHi').'　'.getweek(YmdHis($yhtime,'Ymd'));?></li>
                <li style="line-height:150%;margin-top:5px"><font>约会详情：</font><?php echo $content; ?></li>
                <?php if ($bmnum > 0){?>
                <div class="bmlist"><?php echo '<span>已报名<font class="Cf00"> '.$bmnum.' </font>人</span>'.dating_getBmUlist($fid);?></div>
                <?php }?>
            </dd>
            <em>
				<li><?php echo $outtime; ?></li>
				<li>有<font><?php echo $bmnum ?></font>人报名</li>
				<li><font><?php echo $click ?></font>人想约</li>
				<li><a href="<?php echo $showhref; ?>"<?php echo $acls;?>><?php echo $atitle;?></a></li>
            </em>
        </dl>
		<?php
		}if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox zeaipagebox">'.$pagelist.'</div>';?>
        <?php }else{echo '<br><br><br><br>'.nodatatips('暂时还没有人发布约会<br><br><a onclick="dating_add();"class="size2 btn HONG">我要发布</a>');}?>
        </div>
	</div>
	<div class="datingR">
        <div class="box S5">
			<h1>发布约会</h1>
            <div class="addbox">
                <a href="javascript:;" class="ed" onclick="dating_add();"><i class="ico2">&#xe613;</i>　我要发布</a>
                <a href="<?php echo HOST;?>/p1/my_dating.php?t=1">管理我的约会</a>
                <a href="<?php echo HOST;?>/p1/my_dating.php?t=2">我参加的约会</a>
            </div>
		</div>
        <div class="so box S5">
			<h1>约会搜索</h1>
            <form method="get" action="<?php echo HOST;?>/p1/dating.php" name="YZLOVE_com.form" id="GYLform7" class="form">
                <dl><dt>发 起 人</dt><dd><script>zeai_cn__CreateFormItem('select','sex','<?php echo $sex; ?>','class="select SW2"');</script></dd></dl>
                <dl><dt>约会类型</dt><dd><select name="Sdatingkind" class="SW select">
                    <option value="0"<?php echo (empty($Skind))?' selected':''; ?>>请选择</option>
                    <option value="1"<?php echo ($Sdatingkind == 1)?' selected':''; ?>>喝茶小叙</option>
                    <option value="2"<?php echo ($Sdatingkind == 2)?' selected':''; ?>>共进晚餐</option>
                    <option value="3"<?php echo ($Sdatingkind == 3)?' selected':''; ?>>相约出游</option>
                    <option value="4"<?php echo ($Sdatingkind == 4)?' selected':''; ?>>看电影</option>
                    <option value="5"<?php echo ($Sdatingkind == 5)?' selected':''; ?>>欢唱K歌</option>
                    </select></dd></dl>
                <dl><dt>约会时间</dt><dd><select name="Syhtime" class="SW select">
                    <option value="0"<?php echo (empty($Syhtime))?' selected':''; ?>>请选择</option>
                    <option value="1"<?php echo ($Syhtime == 1)?' selected':''; ?>>3天内</option>
                    <option value="2"<?php echo ($Syhtime == 2)?' selected':''; ?>>1周内</option>
                    <option value="3"<?php echo ($Syhtime == 3)?' selected':''; ?>>1月内</option>
                    </select></dd></dl>
                <dl><dt>约会城市</dt><dd class="area"><script>LevelMenu3('w1|w2|w3|'+nulltext+'|<?php echo $w1; ?>|<?php echo $w2; ?>|<?php echo $w3; ?>','class="select SW2"');</script></dd></dl>
                <dl><dt>费用预算</dt><dd><select name="Sprice" class="SW select">
                    <option value="0"<?php echo (empty($Sprice))?' selected':''; ?>>请选择</option>
                    <option value="1"<?php echo ($Sprice == 1)?' selected':''; ?>>100元以下</option>
                    <option value="2"<?php echo ($Sprice == 2)?' selected':''; ?>>100--300元</option>
                    <option value="3"<?php echo ($Sprice == 3)?' selected':''; ?>>300--500元</option>
                    <option value="4"<?php echo ($Sprice == 4)?' selected':''; ?>>500元以上</option>
                    </select></dd></dl>
                <dl><dt>谁来买单</dt><dd><select name="Smaidian" class="SW select"> 
                    <option value=0<?php echo (empty($Smaidian))?' selected':''; ?>>请选择</option>
                    <option value=1<?php echo ($Smaidian == 1)?' selected':''; ?>>我买单</option>
                    <option value=2<?php echo ($Smaidian == 2)?' selected':''; ?>>应约人买单</option>
                    <option value=3<?php echo ($Smaidian == 3)?' selected':''; ?>>AA制</option>
                    </select></dd></dl>
                <dl><dt><input type="hidden" name="t" value="7" />&nbsp;</dt><dd><button type="submit" class="btn size3 HONG3 W100_"><i class="ico">&#xe6c4;</i> 搜索约会</button></dd></dl>
            </form>
		</div>
        <div class="box S5">
			<h1>约会安全</h1>
            <div class="safetips">　声明：网络约会有风险，同城约会行为属网友个人行为，本站对双方交友过程中发生的任何纠纷不承担责任，请确定后再赴约。<br><br>　<?php echo $_ZEAI['siteName'];?>是一个公众交友平台，网站信息无法保证百分百真实，如果被骗，请与警方联系，且与本站无关，希望大家谨防各类骗局。<br><br></div>
		</div>
	</div>
</div>
<div class="clear"></div>
<script>function dating_add(){supdes=ZeaiPC.iframe({url:PCHOST+'/my_dating'+zeai.ajxext+'submitok=add',w:900,h:550});}</script>
<?php require_once ZEAI.'p1/bottom.php';
function dating_getBmUlist($fid) {
	global $db,$_ZEAI;
	$rt=$db->query("SELECT U.sex,U.photo_s,U.photo_f FROM ".__TBL_DATING_USER__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC LIMIT 6");
	$echo = '';
	WHILE ($rows = $db->fetch_array($rt,'num')){
		$sex      = $rows[0];
		$photo_s  = $rows[1];
		$photo_f  = $rows[2];
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
		$echo.='<img src="'.$photo_s_url.'"'.$sexbg.'>';
	}
	return $echo;
}
ob_end_flush();
?>
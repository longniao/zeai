<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
$t = (ifint($t,'1-7','1'))?$t:'';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
$t = (ifint($t,'1-2','1'))?$t:0;
$nav='hongbao';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>红包_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/hongbao.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="main hongbao fadeInL">
	<div class="hongbaoL">
    	<div class="box S5" style="margin-bottom:0">
			<h1>红包大厅</h1>
            <div class="hongbaokind">
                <a href="<?php echo Href('hongbao');?>"<?php echo (empty($t))?' class="ed"':''; ?>>全部红包</a>
                <a href="<?php echo HOST;?>/p1/hongbao.php?t=1"<?php echo ($t==1)?' class="ed"':''; ?>>抢红包</a>
                <a href="<?php echo HOST;?>/p1/hongbao.php?t=2"<?php echo ($t==2)?' class="ed"':''; ?>>讨红包</a>
                <a href="<?php echo HOST;?>/p1/hongbao.php?t=<?php echo $t; ?>&s=1"<?php echo ($s==1)?' class="ed"':''; ?>>男士</a>
                <a href="<?php echo HOST;?>/p1/hongbao.php?t=<?php echo $t; ?>&s=2"<?php echo ($s==2)?' class="ed"':''; ?>>女士</a>
			</div>
            <div class="hongbaolist" id="list">
			<?php 
			$SQL = "";
			if ($t == 1){
				$SQL .= " AND (a.kind=1 OR a.kind=2) ";
			}elseif($t == 2){
				$SQL .= " AND a.kind=3 ";
			}
			if ($s == 1){
				$SQL .= " AND b.sex=1 ";
			}elseif($s == 2){
				$SQL .= " AND b.sex=2 ";
			}
			//
            $rt=$db->query("SELECT a.id,a.uid,a.kind,a.money,a.content,a.addtime,a.flag,a.click,b.sex,b.grade,b.nickname,b.photo_s,b.photo_f,b.photo_ifshow FROM ".__TBL_HONGBAO__." a,".__TBL_USER__." b WHERE a.flag>0 AND a.uid=b.id AND b.flag=1 ".$SQL." ORDER BY a.id DESC");
			$total = $db->num_rows($rt);
			if ($total > 0) {
				$page_skin='4_yuan';$pagemode=4;$pagesize=8;$page_color='#EF5459';require_once ZEAI.'sub/page.php';
				for($i=1;$i<=$pagesize;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows)break;
					$rows_ulist .= rows_ulist($rows);
				}
				echo $rows_ulist;
			}else{echo '<br><br><br><br>'.nodatatips('暂时还没人发红包<br><br><a onclick="hongbao_add(\'out\')" class="size2 btn HONG2">＋我要发红包</a>');}
			if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox zeaipagebox">'.$pagelist.'</div>';
            ?>
			</div>
        </div>
	</div>
	<div class="hongbaoR">
        <div class="box S5 addbox">
			<h1>发布红包</h1>
            <div>
                <a href="javascript:;" class="ed" onclick="hongbao_add('out');"><i class="ico">&#xe64c;</i>　我要发红包</a><a href="javascript:;" class="ed2" onclick="hongbao_add('in');"><i class="ico">&#xe64c;</i>　我要讨红包</a>
            </div>
		</div>
        <div class="box S5 U">
			<h1>红包讯息</h1>
            <div class="newlist">
            <?php
            $rt=$db->query("SELECT a.id,a.uid,a.kind,a.money,a.addtime,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.photo_ifshow FROM ".__TBL_HONGBAO_USER__." a,".__TBL_USER__." b WHERE a.uid=b.id ORDER BY a.id DESC LIMIT 11");
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt);
                if(!$rows) break;
                $id       = $rows[0];
                $uid      = $rows[1];
                $kind     = $rows[2];
                $money    = $rows[3];
                $addtime  = date_str($rows[4]);
                $nickname = trimhtml(dataIO($rows[5],'out'));
                $sex      = $rows[6];
				$grade    = $rows[7];
                $photo_s  = $rows[8];
                $photo_f  = $rows[9];
                $photo_ifshow = $rows[10];
				$umoney_str = ($kind == 3)?'打赏出去<font>'.$money.'</font>元':'抢到<font>'.$money.'</font>元';
                $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
				if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
                $sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
                $img_str     = '<img src="'.$photo_s_url.'" '.$sexbg.'>';
                ?>
                <a href="<?php echo Href('u',$uid); ?>" target="_blank"><?php echo $img_str; ?><em><h3><?php echo $nickname; ?></h3><h4><?php echo $umoney_str; ?></h4><h4><?php echo $addtime; ?></h4></em></a>
            <?php }}else{echo nodatatips('暂无信息');}?>

			<div class="clear"></div>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>
<script src="<?php echo HOST;?>/p1/js/hongbao.js"></script>
<script src="<?php echo HOST;?>/p1/js/my_hongbao.js"></script>
<?php require_once ZEAI.'p1/bottom.php';
function rows_ulist($rows) {
	global $_ZEAI,$db,$cook_uid;
	$id            = $rows['id'];
	$uid           = $rows['uid'];
	$money         = $rows['money'];
	$kind          = $rows['kind'];
	$content       = dataIO($rows['content'],'out');
	$addtime_str   = date_str($rows['addtime']);
	if ($kind == 3){
		$money_str = ($money == 0)?'多少不限，随意就好':'至少'.$money.'元以上吧';
	}else{
		$money_str = $money.'元';
	}
	switch ($kind){case 1:$kind_str = "运气红包";break;case 2:$kind_str = "定额红包";break;case 3:$kind_str = "讨红包";break;}
	$kind_cls = ($kind == 3)?' class="kind3"':'';
	//
	$sex           = $rows['sex'];
	$grade         = $rows['grade'];
	$nickname      = urldecode(dataIO($rows['nickname'],'out'));
	$photo_s       = $rows['photo_s'];
	$photo_f       = $rows['photo_f'];
	$photo_ifshow  = $rows['photo_ifshow'];
	
	$sexbg         = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
	$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
	if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
	if (ifint($cook_uid,'0-9','1,8')){$ifgz = gzflag($uid,$cook_uid);}else{$ifgz = 0;}
	$gzcls   = ($ifgz == 1)?' class="ed"':'';$gztitle = ($ifgz == 1)?'<i class="ico">&#xe6b1;</i> 已关注':'<i class="ico">&#xe620;</i> 加关注';
	$echo .='<dl id="dl'.$id.'">';
	$echo .='<dt uid="'.$uid.'"><a href="'.Href('u',$uid).'" target="_blank" '.$sexbg.'><p value="'.$photo_s_url.'"></p></a><a href="javascript:;"'.$gzcls.'>'.$gztitle.'</a></dt>';
	$echo .='<dd>';
	$echo .='	<h3><font'.$kind_cls.'>'.$kind_str.'</font>'.$money_str.'<span>'.$addtime_str.'</span></h3>';
	$echo .='	<h2>'.$content.'</h2>';
	$echo .='	<div class="zj"></div>';
	$echo .='	<a href="'.Href('hongbao',$id).'"><i class="ico2">&#xe6c3;</i> 去看看</a>';
	$echo .='</dd></dl>';
	return $echo;
}
ob_end_flush();
?>
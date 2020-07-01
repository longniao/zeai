<?php
require_once '../../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once 'hongbao_init.php';

if ($submitok == 'ajax_getlist'){
	//
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
	$p = intval($p);$SQL="";if ($p<1)$p=1;$totalpage = intval($totalpage);
	$rt=$db->query("SELECT a.id,a.uid,a.kind,a.money,a.content,a.addtime,a.flag,a.click,b.sex,b.grade,b.nickname,b.photo_s,b.photo_f FROM ".__TBL_HONGBAO__." a,".__TBL_USER__." b WHERE a.flag>0 AND a.uid=b.id AND b.flag=1 ".$SQL." ORDER BY a.id DESC");
	$total = $db->num_rows($rt);
	//
	if ($total <= 0 || $p > $totalpage)exit("end");
	if ($p <= $totalpage){$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);}else{exit("end");}
	for($i=1;$i<=$_ZEAI['pagesize'];$i++) {
		$rows = $db->fetch_array($rt,"name");
		if(!$rows)break;
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
		$href          = 'detail.php?fid='.$id;
		switch ($kind){case 1:$kind_str = "运气红包";break;case 2:$kind_str = "定额红包";break;case 3:$kind_str = "讨红包";break;}
		$kind_cls = ($kind == 3)?' class="kind3"':'';
		//
		$sex           = $rows['sex'];
		$grade         = $rows['grade'];
		$nickname      = urldecode(dataIO($rows['nickname'],'out'));
		$photo_s       = $rows['photo_s'];
		$photo_f       = $rows['photo_f'];
		$uhref         = HOST.'/?z=index&e=u&a='.$uid;
		$imgbdr        = (empty($photo_s) || $photo_f==0)?' class="imgbdr'.$sex.'"':'';
		$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		//
		$gzcls   = ($sex == 1)?' class="lan"':' class="hong"';
		?>
		<dl>
			<dt uid='<?php echo $uid; ?>'><a href="<?php echo $uhref; ?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $imgbdr; ?>><font<?php echo $gzcls; ?>><?php echo $nickname; ?></font></a></dt>
			<dd>
				<h2><font<?php echo $kind_cls; ?>><?php echo $kind_str; ?></font><?php echo $money_str; ?><span><?php echo $addtime_str; ?></span></h2>
				<h1><?php echo $content; ?></h1>
				<a href="<?php echo $href; ?>">去看看</a>
			</dd>
		</dl>
	<?php }
	exit;
}
if ($s == 1){
	$s_str = ' - 男士';
}elseif($s == 2){
	$s_str = ' - 女士';
}else{
	$s_str = '';
}
if ($t == 1){
	$mini_title = '抢红包';
}elseif($t == 2){
	$mini_title = '讨红包';
}else{
	$mini_title = '全部红包';
}
$mini_show  = true;$mini_title = $mini_title.$s_str;$nav='trend';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>抢红包 - <?php echo $_ZEAI['siteName']; ?></title>
<?php echo $headmeta; ?>
<link href="hongbao.css" rel="stylesheet" type="text/css" />
<script src="www_zeai_cn.js"></script>
</head>
<body>
<?php require_once 'top_mini.php';?>
<a href="my/hongbao.php?t=3" class="hfbtn" id="hfbtn">我要发红包</a>
<div class="hbtabmenu_5">
    <a href="./"<?php echo (empty($t))?' class="ed"':''; ?>>全部</a>
    <a href="./?t=1"<?php echo ($t==1)?' class="ed"':''; ?>>抢红包</a>
    <a href="./?t=2"<?php echo ($t==2)?' class="ed"':''; ?>>讨红包</a>
    <a href="./?t=<?php echo $t; ?>&s=1"<?php echo ($s==1)?' class="ed"':''; ?>>男士</a>
    <a href="./?t=<?php echo $t; ?>&s=2"<?php echo ($s==2)?' class="ed"':''; ?>>女士</a>
</div>
<main>
    <em class="index_list" id="list">
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
			$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_HONGBAO__." a,".__TBL_USER__." b WHERE a.uid=b.id AND b.flag=1 ".$SQL);
			$roww = $db->fetch_array($rtt,'num');
			$totalnum = $roww[0];
            $totalpage = ceil($totalnum/$_ZEAI['pagesize']);
            $rt=$db->query("SELECT a.id,a.uid,a.kind,a.money,a.content,a.addtime,a.flag,a.click,b.sex,b.grade,b.nickname,b.photo_s,b.photo_f FROM ".__TBL_HONGBAO__." a,".__TBL_USER__." b WHERE a.flag>0 AND a.uid=b.id AND b.flag=1 ".$SQL." ORDER BY a.id DESC LIMIT ".$_ZEAI['pagesize']);
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($i=1;$i<=$total;$i++) {
                    $rows = $db->fetch_array($rt,"name");
                    if(!$rows)break;
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
					$href          = 'detail.php?fid='.$id;
					switch ($kind){case 1:$kind_str = "运气红包";break;case 2:$kind_str = "定额红包";break;case 3:$kind_str = "讨红包";break;}
					$kind_cls = ($kind == 3)?' class="kind3"':'';
                    //
                    $sex           = $rows['sex'];
                    $grade         = $rows['grade'];
                    $nickname      = urldecode(dataIO($rows['nickname'],'out'));
                    $photo_s       = $rows['photo_s'];
                    $photo_f       = $rows['photo_f'];
                    $uhref         = HOST.'/?z=index&e=u&a='.$uid;
                    $imgbdr        = (empty($photo_s) || $photo_f==0)?' class="imgbdr'.$sex.'"':'';
                    $photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
                    //
					$gzcls   = ($sex == 1)?' class="lan"':' class="hong"';
                    ?>
                    <dl>
                        <dt uid='<?php echo $uid; ?>'><a href="<?php echo $uhref; ?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $imgbdr; ?>><font<?php echo $gzcls; ?>><?php echo $nickname; ?></font></a></dt>
                        <dd>
                            <h2><font<?php echo $kind_cls; ?>><?php echo $kind_str; ?></font><?php echo $money_str; ?><span><?php echo $addtime_str; ?></span></h2>
                            <h1><?php echo $content; ?></h1>
                            <a href="<?php echo $href; ?>">去看看</a>
                        </dd>
                    </dl>
            <?php }}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
    </em>
</main>
<?php if ($totalnum > $_ZEAI['pagesize']){ ?>
	<div id="loading"></div>
	<input type="hidden" id="p" value="1" /><input type="hidden" id="tmplist" />
	<script>
	var totalpage = parseInt(<?php echo $totalpage; ?>);
	var ajax_url  = 'index'+ajxext+'submitok=ajax_getlist'+'&totalpage='+totalpage+'&<?php echo geturl(false); ?>';
	</script>
	<script src="loading_data.js"></script>
<?php }?>
<?php require_once 'bottom.php';?>
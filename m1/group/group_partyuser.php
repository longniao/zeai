<?php
require_once '../../sub/init.php';
require_once 'group_init.php';
$_ZEAI['pagesize'] = 12;
//
if ( !preg_match("/^[0-9]{1,8}$/",$mainid) && !empty($mainid))callmsg("圈子不存在～","-1");
if ( !ifint($fid))callmsg("该信息不存在或已被删除","-1");
require_once ZEAI.'sub/conn.php';
$rt = $db->query("SELECT mainid,title,flag,bmnum FROM ".__TBL_GROUP_CLUB__." WHERE flag>0 AND id=".$fid);
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'all');
	$mainid = $row['mainid'];
	$title = dataIO($row['title'],'out');
	$flag = $row['flag'];
	$bmnum = $row['bmnum'];
} else {NoUserInfo();}
$rt = $db->query("SELECT mbkind,title,userid FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid." AND flag=1");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
	$mbkind = $row[0];
	$maintitle = dataIO($row[1],'out');
	$userid_main = $row[2];
} else {NoUserInfo();}
//
if($submitok=="get_ajax_list"){
	$p     = intval($p);$SQL="";if ($p<1)$p=1;$totalpage = intval($totalpage);
	$rt=$db->query("SELECT a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,b.userid,b.addtime,b.flag FROM ".__TBL_USER__." a,".__TBL_GROUP_CLUB_USER__." b WHERE a.id=b.userid AND b.clubid=".$fid." ORDER BY b.id DESC");
	$total = $db->num_rows($rt);
	//
	if ($total <= 0 || $p > $totalpage)exit("end");
	if ($p <= $totalpage){$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);}else{exit("end");}
	for($i=1;$i<=$_ZEAI['pagesize'];$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$nickname = urldecode(dataIO($rows[0],'out'));
		$sex      = $rows[1];
		$grade    = $rows[2];
		$photo_s  = $rows[3];
		$photo_f  = $rows[4];
		$uid      = $rows[5];
		$addtime  = $rows[6];
		$flag     = $rows[7];
		$href = HOST.'/?z=index&e=u&a='.$uid;
		$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		$img_str     = '<img src="'.$photo_s_url.'" class="imgbdr'.$sex.'">';
		$addtime_str = YmdHis(strtotime($addtime),'YmdHi');
		$flag_str = ($flag == 0)?'未审':'';
		?>
		<li><a href="<?php echo $href; ?>"><?php echo $img_str; ?>
			<h4><i class="s<?php echo $sex.$grade; ?>"></i><?php echo $nickname; ?></h4>
			<h4><?php echo $addtime_str; ?></h4>
			<h4><?php echo $flag_str; ?></h4>
			</a>
		</li>
	<?php
	}exit;
}
//
$totalnum   = $db->COUNT(__TBL_GROUP_CLUB_USER__," clubid=$fid");
$totalpage  = ceil($totalnum/$_ZEAI['pagesize']);
$rt=$db->query("SELECT a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,b.userid,b.addtime,b.flag FROM ".__TBL_USER__." a,".__TBL_GROUP_CLUB_USER__." b WHERE a.id=b.userid AND b.clubid=".$fid." ORDER BY b.id DESC LIMIT ".$_ZEAI['pagesize']);
$total = $db->num_rows($rt);
//
$mini_url = 'group_partyshow.php?fid='.$fid;
$mini_show  = true;$mini_title = '当前已报名('.$bmnum.')';$nav='trend';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title; ?> 报名人员</title>
<?php echo $headmeta; ?>
<script src="www_zeai_cn.js?1"></script>
<link href="group.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require_once 'top_mini.php';?>
<main id="list" class="user">
	<?php 
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt);
			if(!$rows) break;
			$nickname = urldecode(dataIO($rows[0],'out'));
			$sex      = $rows[1];
			$grade    = $rows[2];
            $photo_s  = $rows[3];
            $photo_f  = $rows[4];
			$uid      = $rows[5];
			$addtime  = $rows[6];
			$flag     = $rows[7];
			$href = HOST.'/?z=index&e=u&a='.$uid;
            $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
            $img_str     = '<img src="'.$photo_s_url.'" class="imgbdr'.$sex.'">';
			$addtime_str = YmdHis(strtotime($addtime),'YmdHi');
			$flag_str = ($flag == 0)?'未审':'&nbsp;';
			?>
            <li><a href="<?php echo $href; ?>"><?php echo $img_str; ?>
                <h4><i class="s<?php echo $sex.$grade; ?>"></i><?php echo $nickname; ?></h4>
                <h4><?php echo $addtime_str; ?></h4>
                <h4><?php echo $flag_str; ?></h4>
                </a>
            </li>
	<?php }}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
</main>
<?php if ($totalnum > $_ZEAI['pagesize']){ ?>
<div id="loading"></div>
	<input type="hidden" id="p" value="1" /><input type="hidden" id="tmplist" />
	<script>
	var totalpage = parseInt(<?php echo $totalpage; ?>);
	var ajax_url  = 'group_partyuser'+ajxext+'submitok=get_ajax_list'+'&totalpage='+totalpage+'&mainid='+<?php echo $mainid; ?>+'&fid='+<?php echo $fid; ?>;
	</script>
	<script src="/js/loading_data.js"></script>
<?php }?>
<?php require_once 'bottom.php';
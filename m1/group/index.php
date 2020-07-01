<?php
require_once '../../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once 'group_init.php';
$_ZEAI['pagesize'] = 8;
if($submitok == 'get_ajax_list1'){
	$p     = intval($p);if ($p<1)$p=1;$totalpage = intval($totalpage);
	$ksql2="";
	$kid = intval($kid);
	if (!empty($kid))$ksql2 = " b.totalid='$kid' AND ";
	$deforderby = " ORDER BY b.jjpmprice DESC,b.px DESC ";
	$rt=$db->query("SELECT a.nickname,a.sex,a.grade,b.id,b.title,b.allusrnum,b.wznum,b.picurl_s,b.userid,b.jjpmprice FROM ".__TBL_USER__." a,".__TBL_GROUP_MAIN__." b WHERE ".$ksql2." a.id=b.userid AND b.flag=1 ".$deforderby);
	$total = $db->num_rows($rt);
	//
	if ($total <= 0 || $p > $totalpage)exit("end");
	if ($p <= $totalpage){
		$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);
	}else{
		exit("end");
	}
	for($i=1;$i<=$_ZEAI['pagesize'];$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$Uid = $rows[8];
		$Unickname = dataIO($rows[0],'out');
		$Usex      = $rows[1];
		$Ugrade    = $rows[2];
		$id = $rows[3];
		$title = dataIO($rows[4],'out');
		$allusrnum = $rows[5];
		$wznum = $rows[6];
		$picurl_s = $rows[7];
		$jjpmprice = $rows[9];
		$mainid = $id*3+797311;
		if ($jjpmprice > 0){
			$href = $_ZEAI['group2'].'/biddinggroup.php?mainid='.$mainid;
		} else {
			$href = $_ZEAI['group2'].'/group_main.php?mainid='.$id;
		}
		if (empty($picurl_s)){
			$picurl_s = 'images/noqzphoto.jpg';
		}else{
			$picurl_s = $_ZEAI['up2'].'/'.$picurl_s;
		}
		$Uhref =HOST.'/?z=index&e=u&a='.$Uid;
		$sexcolor = ($Usex==1)?' class="lan"':' class="hong"';
		
		
		?>
		<li>
			<div class="liP"><a href="<?php echo $href; ?>"><img src=<?php echo $picurl_s; ?> title="点击进入"></a></div>
			<div class="liT"><a href="<?php echo $href;?>"><?php echo $title; ?></a></div>
			<div class="liB">成员<span><?php echo $allusrnum; ?></span>人　主题<span><?php echo $wznum; ?></span>篇</div>
			<div class="liB">会长：<a href="<?php echo $Uhref; ?>" <?php echo $sexcolor; ?>><?php echo $Unickname; ?></a></div>
			<div class="clear"></div>
		</li>
	<?php
	}
	exit;
}
$nav='trend';
$mini_show = true;
$mini_title = '圈子';
$t = 1;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_ZEAI['SiteName']; ?>_圈子</title>
<?php echo $headmeta; ?>
<link href="group.css" rel="stylesheet" type="text/css" />
<script src="www_zeai_cn.js"></script>
<script>
var t = <?php echo $t;?>;
</script>
<style>
.addgroup{position:absolute;top:6px;right:15px;width:80px;line-height:32px;border-radius:2px;color:#fff;font-size:14px;background-color:rgba(0,0,0,0.3);z-index:10;font-weight:bold}
.addgroup:hover{background-color:rgba(0,0,0,0.1)}
</style>
</head>
<body>
<a href="my/group.php?submitok=add" class="addgroup">我要创建</a>
<header class="top_mini"><div class='title'><div class='h1'><?php echo $mini_title;?></div></div></header>
<div class="gkind">
<a href="./"<?php echo (empty($kid))?' class="ed"':''; ?>><font>全部类别</font></a>
<?php 
$rtk=$db->query("SELECT id,title,bknum FROM ".__TBL_GROUP_TOTAL__." WHERE flag=1 ORDER BY px DESC");
if (!$db->num_rows($rtk)){
    echo '暂无';
} else {
    $totalk = $db->num_rows($rtk);
    for($ik=1;$ik<=$totalk;$ik++) {
        $rowsk = $db->fetch_array($rtk);
        if(!$rowsk) break;
        $Aclass=($kid==$rowsk[0])?' class="ed"':'';?>
        <a href="./?kid=<?php echo $rowsk[0]; ?>"<?php echo $Aclass; ?>><font><?php echo $rowsk[1]; ?></font> (<span<?php echo ($rowsk[2]>0)?' class=Cf00':' class=C999'; ?>><?php echo $rowsk[2]; ?></span>)</a>
<?php }}?>
<div class="clear"></div>
</div>
<!-- -->

<main id="list" class="list">
<?php if($t == 1){  ?>
  <?php 
    $ksql1="";$ksql2="";
	$kid = intval($kid);
	if (!empty($kid))$ksql1 = " AND totalid='$kid' ";
	$totalnum   = $db->COUNT(__TBL_GROUP_MAIN__," flag=1 ".$ksql1);
	$totalpage  = ceil($totalnum/$_ZEAI['pagesize']);
	if (!empty($kid))$ksql2 = " b.totalid='$kid' AND ";
	$deforderby = " ORDER BY b.jjpmprice DESC,b.px DESC ";
	$rt=$db->query("SELECT a.nickname,a.sex,a.grade,b.id,b.title,b.allusrnum,b.wznum,b.picurl_s,b.userid,b.jjpmprice FROM ".__TBL_USER__." a,".__TBL_GROUP_MAIN__." b WHERE ".$ksql2." a.id=b.userid AND b.flag=1".$deforderby." LIMIT ".$_ZEAI['pagesize']);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt);
			if(!$rows) break;
			$Uid = $rows[8];
			$Unickname = dataIO($rows[0],'out');
			$Usex      = $rows[1];
			$Ugrade    = $rows[2];
			$id = $rows[3];
			$title = dataIO($rows[4],'out');
			$allusrnum = $rows[5];
			$wznum = $rows[6];
			$picurl_s = $rows[7];
			$jjpmprice = $rows[9];
			$mainid = $id*3+797311;
			if ($jjpmprice > 0){
				$href = $_ZEAI['group2'].'/biddinggroup.php?mainid='.$mainid;
			} else {
				$href = $_ZEAI['group2'].'/group_main.php?mainid='.$id;
			}
			if (empty($picurl_s)){
				$picurl_s = 'images/noqzphoto.jpg';
			}else{
				$picurl_s = $_ZEAI['up2'].'/'.$picurl_s;
			}
			$Uhref =HOST.'/?z=index&e=u&a='.$Uid;;
			$sexcolor = ($Usex==1)?' class="lan"':' class="hong"';
			?>
            <li>
                <div class="liP"><a href="<?php echo $href; ?>"><img src=<?php echo $picurl_s; ?> title="点击进入"></a></div>
                <div class="liT"><a href="<?php echo $href;?>"><?php echo $title; ?></a></div>
                <div class="liB">成员<span><?php echo $allusrnum; ?></span>人　主题<span><?php echo $wznum; ?></span>篇</div>
                <div class="liB">会长：<a href="<?php echo $Uhref; ?>" <?php echo $sexcolor; ?>><?php echo $Unickname; ?></a></div>
                <div class="clear"></div>
            </li>
	<?php }}else{echo "<div class='nodatatips W150'>暂时还没有圈子<br><br><a href='my/group.php?submitok=add' class='aLAN'>我要创建</a></div>";}?>
<?php }?>

</main>
<?php if ($totalnum > $_ZEAI['pagesize']){ ?>
<div id="loading"></div>
	<input type="hidden" id="p" value="1" /><input type="hidden" id="tmplist" />
	<script>
	var totalpage = parseInt(<?php echo $totalpage; ?>);
	var ajax_url  = 'index'+ajxext+'submitok=get_ajax_list<?php echo $t; ?>'+'&totalpage='+totalpage+'&kid='+<?php echo $kid; ?>;
	</script>
	<script src="loading_data.js"></script>
<?php }?>
<script src="group.js"></script>
<?php require_once 'bottom.php';?>
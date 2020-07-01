<?php
require_once '../../sub/init.php';
require_once 'group_init.php';
if (!ifint($mainid))callmsg("圈子不存在～","-1");
require_once ZEAI.'sub/conn.php';
//
$rt = $db->query("SELECT a.nickname,a.sex,a.grade,b.mbkind,b.title,b.userid,b.userid1,b.userid2,b.userid3 FROM ".__TBL_USER__." a,".__TBL_GROUP_MAIN__." b WHERE a.id=b.userid AND b.id=".$mainid." AND b.flag=1");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
	$nickname = $row[0];
	$sex = $row[1];
	$grade = $row[2];
	$mbkind = $row[3];
	$maintitle = dataIO($row[4],'out');
	$userid = $row[5];
	$userid1 = $row[6];
	$userid2 = $row[7];
	$userid3 = $row[8];
	$userid_main = $userid;
	if ($userid == $cook_uid || $userid1 == $cook_uid || $userid2 == $cook_uid || $userid3 == $cook_uid) {
		$authority_main = "OK";
	} else {
		$authority_main = "NO";
	}
} else {NoUserInfo();}
//
if($submitok=="get_ajax_list"){
	$p     = intval($p);$SQL="";if ($p<1)$p=1;$totalpage = intval($totalpage);
$rt=$db->query("SELECT id,title,flag FROM ".__TBL_GROUP_CLUB__." WHERE mainid=".$mainid." AND flag>0 ORDER BY flag,id DESC");
	$total = $db->num_rows($rt);
	//
	if ($total <= 0 || $p > $totalpage)exit("end");
	if ($p <= $totalpage){$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);}else{exit("end");}
	for($i=1;$i<=$_ZEAI['pagesize'];$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id    = $rows['id'];
		$title = dataIO($rows['title'],'out');
		$flag  = $rows['flag'];
		$href  = 'group_partyshow.php?fid='.$id;
		$flag_cls = ($flag > 1)?' class="off"':'';
		if ($flag > 1){
			$flag_cls =' class="off"';
			$btntitle = '查看详情';
		}else{
			$flag_cls ='';
			$btntitle = '进入报名';
		}
		?>
		<a href="<?php echo $href; ?>"><img src="images/qzlist.gif"> <h3><?php echo $title; ?></h3>
		<?php if ($flag == 1)echo "<img src=images/new2.gif hspace=6>";?>
		<span<?php echo $flag_cls; ?>><?php echo $btntitle; ?></span>
		</a>
	<?php
	}exit;
}
//
$totalnum   = $db->COUNT(__TBL_GROUP_CLUB__," mainid=$mainid AND flag>0");
$totalpage  = ceil($totalnum/$_ZEAI['pagesize']);
$rt=$db->query("SELECT id,title,flag FROM ".__TBL_GROUP_CLUB__." WHERE mainid=".$mainid." AND flag>0 ORDER BY flag,id DESC LIMIT ".$_ZEAI['pagesize']);
$total = $db->num_rows($rt);
//
$mini_url = 'group_main.php?mainid='.$mainid;
$mini_show  = true;$mini_title = '圈子活动('.$totalnum.')';$nav='trend';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $maintitle; ?></title>
<?php echo $headmeta; ?>
<script src="www_zeai_cn.js?1"></script>
<link href="group.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require_once 'top_mini.php';?>
<div class="main">
    <em class="party" id="list">
        <?php 
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows) break;
                $id    = $rows['id'];
                $title = dataIO($rows['title'],'out');
                $flag  = $rows['flag'];
                $href  = 'group_partyshow.php?fid='.$id;
				$flag_cls = ($flag > 1)?' class="off"':'';
				if ($flag > 1){
					$flag_cls =' class="off"';
					$btntitle = '查看详情';
				}else{
					$flag_cls ='';
					$btntitle = '进入报名';
				}
                ?>
                <a href="<?php echo $href; ?>"><h3><img src="images/qzlist.gif"> <?php echo $title; ?><?php if ($flag == 1)echo "<img src=images/new2.gif hspace=6>";?></h3>
                <span<?php echo $flag_cls; ?>><?php echo $btntitle; ?></span>
                </a>
        <?php }}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
    </em>
</div>
<?php if ($totalnum > $_ZEAI['pagesize']){ ?>
<div id="loading"></div>
	<input type="hidden" id="p" value="1" /><input type="hidden" id="tmplist" />
	<script>
	var totalpage = parseInt(<?php echo $totalpage; ?>);
	var ajax_url  = 'group_party'+ajxext+'submitok=get_ajax_list'+'&totalpage='+totalpage+'&mainid='+<?php echo $mainid; ?>;
	</script>
	<script src="/js/loading_data.js"></script>
<?php }?>
<?php require_once 'bottom.php';
?>
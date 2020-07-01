<?php
require_once '../../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once 'group_init.php';

if (!ifint($mainid))callmsg("该圈子不存在或已被锁定或删除！","-1");
if ($submitok == "loginupdate") {
	if (!ifint($cook_uid))header("Location: ".HOST."/m1/login.php");
	$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id='$cook_uid' AND pwd='$cook_pwd' AND flag=1");
	if(!$db->num_rows($rt) > 0){
		callmsg("Forbidden","-1");
		exit;
	}
	$rt = $db->query("SELECT userid,ifopen FROM ".__TBL_GROUP_MAIN__." WHERE id='$mainid' AND flag=1");
	if(!$db->num_rows($rt)){
		callmsg("请求错误，该圈子不存在","-1",500);
	}else{
		$row = $db->fetch_array($rt);
		$userid_main = $row[0];
		$ifopen = $row[1];
	}
	if ($ifopen == 0)callmsg("Sorry! 该圈子已关闭新成员加入！","-1");
	//查黑
	$truebbs = 1;
	if (gzflag($cook_uid,$userid_main) == -1)$truebbs = 0;//有黑
	//$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_FRIEND__." WHERE flag=-1 AND userid=".$cook_uid." AND senduserid=".$userid_main);
	//$row = $db->fetch_array($rt);
	//if ($row[0] > 0)$truebbs = 0;//有黑
	if ($truebbs == 1) {
		$rt = $db->query("SELECT flag FROM ".__TBL_GROUP_USER__." WHERE userid='$cook_uid' AND mainid='$mainid'");
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			if ($row[0] == 1){
				callmsg("请不要重复加入～","-1",400);
			}else{
				callmsg("已申请，请等待会长验证！","-1");
			}
		}
		$flag = ($ifopen==1)?1:0;
		$addtime = YmdHis($ADDTIME);
		$db->query("INSERT INTO ".__TBL_GROUP_USER__."  (userid,mainid,flag,addtime) VALUES ($cook_uid,$mainid,$flag,'$addtime')");
		$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET allusrnum=allusrnum+1 WHERE id='$mainid'");
	}//黑结束
	if ($flag == 1){
		header("Location: group_main.php?mainid=".$mainid);
	}else{
		callmsg("加入成功，请等待圈主的验证！","group_main.php?mainid=".$mainid);
	}
	exit;
}
$rt = $db->query("SELECT a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,b.* FROM ".__TBL_USER__." a,".__TBL_GROUP_MAIN__." b WHERE a.id=b.userid AND b.id=".$mainid." AND b.flag=1");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'all');
	$mbkind = $row['mbkind'];
	$totalid = $row['totalid'];
	$totaltitle = $row['totaltitle'];
	$title = dataIO($row['title'],'out');
	$maintitle = $title;
	$content = dataIO($row['content'],'out',52);
	$qgrade = $row['qgrade'];
	$qloveb = $row['qloveb'];
	$ifopen = $row['ifopen'];
	$useropen = $row['useropen'];
	$ifin = $row['ifin'];
	$ifin2 = $row['ifin2'];
	$areatitle = dataIO($row['areatitle'],'out');
	$allusrnum = $row['allusrnum'];
	$wznum = $row['wznum'];
	$bbsnum = $row['bbsnum'];
	$photonum = $row['photonum'];
	$picurl_s = $row['picurl_s'];
	$picurl_b = str_replace("_s","_b",$picurl_s);
	$click = $row['click'];
	$addtime = $row['addtime'];
	$userid = $row['userid'];
	$userid_main = $userid;
	$nickname = dataIO($row[0],'out');
	$sex      = $row[1];
	$grade    = $row[2];
	$photo_s  = $row[3];
	$photo_f  = $row[4];
	$userid1 = $row['userid1'];
	$userid2 = $row['userid2'];
	$userid3 = $row['userid3'];
	if (!empty($userid1)){
		$rtD=$db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$userid1);
		if ($db->num_rows($rtD)){
			$rowD = $db->fetch_array($rtD);
			$nickname1  = dataIO($rowD[0],'out');
			$sex1  = $rowD[1];
			$grade1  = $rowD[2];
		}
	}
	if (!empty($userid2)){
		$rtD=$db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$userid2);
		if ($db->num_rows($rtD)){
			$rowD = $db->fetch_array($rtD);
			$nickname2  = dataIO($rowD[0],out);
			$sex2  = $rowD[1];
			$grade2  = $rowD[2];
		}
	}
	if (!empty($userid3)){
		$rtD=$db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$userid3);
		if ($db->num_rows($rtD)){
			$rowD = $db->fetch_array($rtD);
			$nickname3  = dataIO($rowD[0],out);
			$sex3  = $rowD[1];
			$grade3  = $rowD[2];
		}
	}
	if ($userid == $cook_uid || $userid1 == $cook_uid || $userid2 == $cook_uid || $userid3 == $cook_uid || $cook_grade == 10) {
		$authority_main = "OK";
	} else {
		$authority_main = "NO";
	}
	if(!empty($picurl_s)){
		$photo_s_url = $_ZEAI['up2'].'/'.$picurl_s;
		$photo_b_url = $_ZEAI['up2'].'/'.$picurl_b;
		$photo_s_str = '<img src="'.$photo_s_url.'">';
	}else{
		$photo_s_url = 'images/nophoto_s.jpg';
		$photo_b_url = 'images/nophoto_b.jpg';
		$photo_s_str = '<img src="'.$photo_s_url.'">';
	}
} else {
	NoUserInfo();
}
$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET click=click+1 WHERE id=".$mainid);

$nav='trend';
require_once ZEAI."api/weixin/jssdk.php";	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
<?php echo $headmeta; ?>
<link href="group.css" rel="stylesheet" type="text/css" />
<style>
.info #photo_b{position:absolute;top:-15px;;left:-5%}
<?php if (!empty($picurl_s)){ ?>
.info #photo_b{width:110%;height:290px;background:url("<?php echo $photo_b_url; ?>?<?php echo $ADDTIME; ?>") center 30% no-repeat;filter:blur(10px);-webkit-filter:blur(10px)}
<?php }else{ ?>
.info #photo_b{width:110%;height:290px;background:url("<?php echo $photo_b_url; ?>?<?php echo $ADDTIME; ?>") center no-repeat;background-size:100% 100%;filter:blur(5px);-webkit-filter:blur(5px)}
<?php }?>
</style>

<script src="www_zeai_cn.js?1"></script>
<script src="jweixin-1.0.0.js"></script>
<script>
var appId     = '<?php echo $signPackage["appId"];?>';
var timestamp = <?php echo $signPackage["timestamp"];?>;
var nonceStr  = '<?php echo $signPackage["nonceStr"];?>';
var signature = '<?php echo $signPackage["signature"];?>';
var submitok;
</script>
</head>
<body>
<div class="info">
	<div id="photo_b"></div>
	<div class="photo_s"><?php echo $photo_s_str; ?></div>
	<em>
        <div class="title"><?php echo $title; ?></div>
        <div class="star">
			<?php 
            for($i=1;$i<=$qgrade;$i++) {echo "<font class='ed'>★</font>";}
            for($i=1;$i<=(5-$qgrade);$i++) {echo "<font>★</font>";}
            ?>        
            <span>创建时间：<?php echo YmdHis(strtotime($addtime),'Ymd'); ?></span>
        </div>
		<div class="moreinfo">
地区<font><?php echo $areatitle; ?></font><br>
类别<font><?php echo $totaltitle; ?></font><br>
圈子成员<font><?php echo $allusrnum; ?></font>人<br>
访问量<font><?php echo $click; ?></font>人次<br>
主题总数<font><?php echo $wznum; ?></font>篇<br>
帖子总数<font><?php echo $bbsnum; ?></font>篇<br>
        </div>
        <div class="boss">
        	<li>会　长<a href=<?php echo HOST.'/?z=index&e=u&a='.$userid; ?> class="sexico<?php echo $sex; ?>"><?php echo $nickname; ?></a></li>
        	<li>副会长<?php if (!empty($userid1)){ ?><a href=<?php echo HOST.'/?z=index&e=u&a='.$userid1; ?> class="sexico<?php echo $sex1; ?>"><?php echo $nickname1; ?></a><?php }?></li>
        	<li>副会长<?php if (!empty($userid2)){ ?><a href=<?php echo HOST.'/?z=index&e=u&a='.$userid2; ?> class="sexico<?php echo $sex2; ?>"><?php echo $nickname2; ?></a><?php }?></li>
        	<li>副会长<?php if (!empty($userid3)){ ?><a href=<?php echo HOST.'/?z=index&e=u&a='.$userid3; ?> class="sexico<?php echo $sex3; ?>"><?php echo $nickname3; ?></a><?php }?></li>
        </div>
        <a href="group_main.php?submitok=loginupdate&mainid=<?php echo $mainid;?>" class="join">加入圈子</a>
    </em>

</div>
<!-- -->
<?php 
$rt=$db->query("SELECT id,title,num_n,num_r,flag,jzbmtime,bmnum FROM ".__TBL_GROUP_CLUB__." WHERE mainid=".$mainid." AND flag=1 ORDER BY id DESC");
$total = $db->num_rows($rt);
if ($total > 0) {
?>
<div class="main">
    <h2>圈子活动<a href="group_party.php?mainid=<?php echo $mainid;?>">MORE</a></h2>
    <em class="party">
		<?php
            for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'name');
            if(!$rows) break;
            $id    = $rows['id'];
			$title = dataIO($rows['title'],'out');
			$flag  = $rows['flag'];
			$href    = 'group_partyshow.php?fid='.$id;
            ?>
            <a href="<?php echo $href; ?>"><img src="images/qzlist.gif"> <h3><?php echo $title; ?></h3><?php if ($flag == 1)echo "<img src=images/new2.gif hspace=6>";?>
            <span>进入报名</span>
            </a>
        <?php }?>
	</em>
    <!-- -->
</div>
<?php }?>
<!-- -->
<div class="main">
    <h2>他们都加入了...<a href="group_user.php?mainid=<?php echo $mainid;?>">MORE</a></h2>
    <em class="ulist">
		<?php
        $rt=$db->query("SELECT b.id,b.userid,a.nickname,a.sex,a.photo_s,a.photo_f FROM ".__TBL_USER__." a,".__TBL_GROUP_USER__." b WHERE a.id=b.userid AND b.mainid=".$mainid." AND b.flag=1 ORDER BY b.id DESC LIMIT 4");
        $total = $db->num_rows($rt);
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt);
            if(!$rows) break;
            $id       = $rows[0];
            $uid      = $rows[1];
            $nickname = urldecode(dataIO($rows[2],'out'));
            $sex      = $rows[3];
            $photo_s  = $rows[4];
            $photo_f  = $rows[5];
            $href    = HOST.'/?z=index&e=u&a='.$uid;
            $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
            $imgbdr  = (empty($photo_s) || $photo_f==0)?' class="imgbdr'.$sex.'"':'';
            $img_str = '<img src="'.$photo_s_url.'" class="imgbdr'.$sex.'">';
            ?>
            <li><a href="<?php echo $href; ?>"><?php echo $img_str; ?><h5><?php echo $nickname; ?></h5></a></li>
        <?php }}else{echo "<div class='nodatatips W150'><br>暂无成员<br><a href=group_main.php?submitok=loginupdate&mainid=$mainid class='aHUANGed'>我要加入</a><br><br></div>";}?>
	</em>
    <!-- -->
</div>
<!-- -->
<div class="main">
	<h2>圈子话题<a href="group_article.php?mainid=<?php echo $mainid; ?>">MORE</a></h2>
	<em>
        <div class="bkbox"><font class="C666">讨论版块</font>　<?php
        $rtbklist = $db->query("SELECT id,title FROM ".__TBL_GROUP_BK__." WHERE mainid=".$mainid." ORDER BY px DESC,id DESC");
        $totalrtbklist = $db->num_rows($rtbklist);
        if($totalrtbklist>0){
        for($s=1;$s<=$totalrtbklist;$s++) {
        $rowsrtbklist = $db->fetch_array($rtbklist);
        echo "<a href=group_article.php?mainid=".$mainid."&bkid=".$rowsrtbklist[0]."&bktitle=".urlencode($rowsrtbklist[1])." class='tiaose'>".$rowsrtbklist[1]."</a>";
        if ($s != $totalrtbklist)echo '　|　';
        }} else {echo "..暂无版块..";}?>
        </div>
<!-- 列表 -->
<?php
$rt=$db->query("SELECT id,bkid,bktitle,title,bbsnum,click,iftop,ifjh,flag,endtime,enduserid,endnicknamesexgrade,addtime,userid,nicknamesexgrade FROM ".__TBL_GROUP_WZ__." WHERE mainid=".$mainid." ORDER BY iftop DESC,endtime DESC LIMIT 20");
$total = $db->num_rows($rt);
if($total>0){
?>
<div class="listorderby">
    <dt><a href="group_post.php?mainid=<?php echo $mainid; ?>&bkid=<?php echo $bkid;?>&bktitle=<?php echo $bktitle;?>" class="add">发表话题</a></dt>
    <dd><?php $listorderby = "group_article.php?mainid=$mainid&bkid=$bkid&bktitle=$bktitle&listtype=";?>
    <a href="<?php echo $listorderby; ?>1">精华</a>
    <a href="<?php echo $listorderby; ?>2">固顶</a>
    <a href="<?php echo $listorderby; ?>3">人气</a>
    <a href="<?php echo $listorderby; ?>4">新帖</a>
    <a href="<?php echo $listorderby; ?>5">回复</a>
    </dd>
</div>
<?php
for($i=1;$i<=$total;$i++) {
$rows = $db->fetch_array($rt,'all');
if(!$rows) break;
$wztitle = dataIO($rows['title'],'out');
?>
<table class="tablelist">
<tr><td height="30">
<?php if ($rows['iftop'] == 1)echo "<img src=images/ding.gif alt=固顶帖> ";
$userid_bk="NO";
if ($ifin == 0) {
	$rtbk = $db->query("SELECT userid FROM ".__TBL_GROUP_BK__." WHERE id=".$rows['bkid']);
	if($db->num_rows($rtbk)){
		$rowbk = $db->fetch_array($rtbk);
		$userid_bk = $rowbk[0];
		if (!ifint($userid_bk))$userid_bk="NO";
	} else {
		callmsg("版块验证失败!","-1");
	}
	if ($authority_main == "OK" || $userid_bk == $cook_uid) {
		echo "<a href=group_read.php?fid=".$rows['id'].">".$wztitle."</a>";
	} else {
		if ( ifint($cook_uid)){
			$rt2 = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_USER__." WHERE userid='$cook_uid' AND mainid=".$mainid." AND flag=1");
			$row2 = $db->fetch_array($rt2);
			if ($row2[0] == 1){
				echo "<a href=group_read.php?fid=".$rows['id'].">".$wztitle."</a>";
			} else {
				echo $wztitle;
			}
		}else{
			echo $wztitle;
		}
	}
} elseif ($ifin == 1) {
	echo "<a href=group_read.php?fid=".$rows['id'].">".$wztitle."</a>";
}
if ($ifin == 0)echo " <a href=group_main.php?submitok=loginupdate&mainid=".$mainid."><img src=images/m.gif alt=只有本圈子内成员才可以查看！点击加入本圈子。></a>";
$d1 = strtotime($rows['addtime']);
$d2 = $ADDTIME;
if (($d2-$d1) < 86400 )echo " <img src=images/new.gif alt=当天发表>";
if ($rows['ifjh'] == 1)echo " <img src=images/jh.gif alt=精华帖>";?>
</td></tr>
<tr><td height="20" align="right" valign="top" class="S12 C999">
<?php
echo '　'.YmdHis(strtotime($rows['endtime']),'YmdHi').'　';
$tmpnicknameend = explode("|",$rows['endnicknamesexgrade']);
$tmpgradeend = $tmpnicknameend[1].$tmpnicknameend[2];
echo uicon($tmpgradeend)."<a href=".HOST.'/?z=index&e=u&a='.$rows['enduserid']." class='C999'>".dataIO($tmpnicknameend[0],'out')."</a>";
?>　<font class="Cf00"><?php echo $rows['bbsnum'];?></font> / <font class="Cf00"><?php echo $rows['click'];?></font></td></tr>
</table>
<?php
}echo "<a href='group_article.php?mainid=".$mainid."' class='btmmore'>查看更多</a>";} else {echo "<div class='nodatatips W150'><br>暂无话题<br><a href=group_post.php?mainid=$mainid class='aHUANGed'>发表新话题</a><br><br></div>";}?>
<!--列表结束 -->

  </em>
</div>
<!-- -->
<div class="main">
    <h2>友情链接</h2>
    <em class="links">
		<?php
        $rt=$db->query("SELECT a.omainid,b.title FROM ".__TBL_GROUP_LINKS__." a,".__TBL_GROUP_MAIN__." b WHERE a.mainid=".$mainid." AND a.omainid=b.id ORDER BY a.px DESC,a.id DESC");
        $total = $db->num_rows($rt);
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt);
            if(!$rows) break;
            $id    = $rows[0];
            $title = dataIO($rows[1],'out');
            $href  = 'group_main.php?mainid='.$id;
            ?><a href="<?php echo $href; ?>" >【<?php echo $title; ?>】</a>
        <?php }}else{echo "<div class='nodatatipsS'>暂无链接</div>";}?>
	</em>
    <!-- -->
</div>
<script src="group.js"></script>
<?php require_once 'bottom.php';?>
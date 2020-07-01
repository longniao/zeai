<?php
require_once '../../sub/init.php';
require_once 'group_init.php';
if (!ifint($mainid))callmsg("圈子不存在～","-1");
$bkid = intval($bkid);
require_once ZEAI.'sub/conn.php';
//
$rt = $db->query("SELECT title,ifin,userid,userid1,userid2,userid3 FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid." AND flag=1");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'name');
	$maintitle    = dataIO($row['title'],'out');
	$ifin         = $row['ifin'];
	$userid_main  = $row['userid'];
	$userid1_main = $row['userid1'];
	$userid2_main = $row['userid2'];
	$userid3_main = $row['userid3'];
	if ($userid == $cook_uid || $userid1 == $cook_uid || $userid2 == $cook_uid || $userid3 == $cook_uid || $cook_grade == 10) {
		$authority_main = "OK";
	} else {
		$authority_main = "NO";
	}
} else {NoUserInfo();}
//
if($submitok=="get_ajax_list"){
	$p     = intval($p);$SQL="";if ($p<1)$p=1;$totalpage = intval($totalpage);
	//
	if (!empty($bkid)){
		$tmpbkid = " bkid='$bkid' ";
	}else{
		$tmpbkid = " 1=1 ";
	}
	if (!empty($keyword)) {
		$tmpkeyword = dataIO(trimm($keyword),'in');
		$tmpkeyword = " title LIKE '%".$tmpkeyword."%'";
	} else {
		$tmpkeyword = " 1=1 ";
	}
	switch ($listtype){ 
		case 1:
			$tmplisttype = " AND ifjh=1 ORDER BY iftop DESC,endtime DESC";
		break;
		case 2:
			$tmplisttype = " AND iftop=1 ORDER BY endtime DESC";
		break;
		case 3:
			$tmplisttype = " ORDER BY click DESC";
		break;
		case 4:
			$tmplisttype = " ORDER BY addtime DESC";
		break;
		case 5:
			$tmplisttype = " ORDER BY bbsnum DESC";
		break;
		default:
			$tmplisttype = " ORDER BY iftop DESC,endtime DESC";
		break;
	}
	$rt=$db->query("SELECT id,bkid,bktitle,title,bbsnum,click,iftop,ifjh,flag,endtime,enduserid,endnicknamesexgrade,addtime,userid,nicknamesexgrade FROM ".__TBL_GROUP_WZ__." WHERE mainid=".$mainid." AND ".$tmpbkid." AND ".$tmpkeyword.$tmplisttype);
	$total = $db->num_rows($rt);
	//
	if ($total <= 0 || $p > $totalpage)exit("end");
	if ($p <= $totalpage){$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);}else{exit("end");}
	for($i=1;$i<=$_ZEAI['pagesize'];$i++) {
		$rows = $db->fetch_array($rt,'name');
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
        echo uicon($tmpgradeend)."<a href=".HOST."/?z=index&e=u&a=".$rows['enduserid']." class='C999'>".$tmpnicknameend[0]."</a>";
        ?>　<font class="Cf00"><?php echo $rows['bbsnum'];?></font> / <font class="Cf00"><?php echo $rows['click'];?></font></td></tr>
        </table>

	<?php
	}exit;
}
//

if (!empty($bkid)){
	$tmpbkid = " bkid='$bkid' ";
}else{
	$tmpbkid = " 1=1 ";
}
$totalnum   = $db->COUNT(__TBL_GROUP_WZ__," mainid=$mainid AND ".$tmpbkid);
$totalpage  = ceil($totalnum/$_ZEAI['pagesize']);
if (!empty($keyword)) {
	$tmpkeyword = dataIO(trimm($keyword),'in');
	$tmpkeyword = " title LIKE '%".$tmpkeyword."%'";
} else {
	$tmpkeyword = " 1=1 ";
}
switch ($listtype){ 
	case 1:
		$tmplisttype = " AND ifjh=1 ORDER BY iftop DESC,endtime DESC";
	break;
	case 2:
		$tmplisttype = " AND iftop=1 ORDER BY endtime DESC";
	break;
	case 3:
		$tmplisttype = " ORDER BY click DESC";
	break;
	case 4:
		$tmplisttype = " ORDER BY addtime DESC";
	break;
	case 5:
		$tmplisttype = " ORDER BY bbsnum DESC";
	break;
	default:
		$tmplisttype = " ORDER BY iftop DESC,endtime DESC";
	break;
}
$rt=$db->query("SELECT id,bkid,bktitle,title,bbsnum,click,iftop,ifjh,flag,endtime,enduserid,endnicknamesexgrade,addtime,userid,nicknamesexgrade FROM ".__TBL_GROUP_WZ__." WHERE mainid=".$mainid." AND ".$tmpbkid." AND ".$tmpkeyword.$tmplisttype." LIMIT ".$_ZEAI['pagesize']);
$total = $db->num_rows($rt);
//
$mini_url = 'group_main.php?mainid='.$mainid;
$mini_show  = true;$mini_title = '圈子话题('.$total.')';$nav='trend';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $bktitle; ?> <?php echo $maintitle; ?></title>
<?php echo $headmeta; ?>
<script src="www_zeai_cn.js?1"></script>
<link href="group.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require_once 'top_mini.php';?>
<main id="list" class="wz_list">
	<?php if (!empty($bkid)) {
	$rtbk = $db->query("SELECT content,userid FROM ".__TBL_GROUP_BK__." WHERE id=".$bkid);
	if($db->num_rows($rtbk)){
		$rowbk = $db->fetch_array($rtbk);
		$bkUid = $rowbk[1];
		if (!empty($bkUid)){
			$rtD=$db->query("SELECT nickname,sex,grade,photo_s,photo_f FROM ".__TBL_USER__." WHERE id=".$bkUid);
			if ($db->num_rows($rtD)){
				$rowD = $db->fetch_array($rtD);
				$bkUnickname = dataIO($rowD[0],'out');
				$bkUsex      = $rowD[1];
				$bkUgrade    = $rowD[2];
				$bkUphoto_s  = $rowD[3];
				$bkUphoto_f  = $rowD[4];
			}else{
				callmsg("Forbidden","-1");
			}
		}
		$bkcontent = htmlout(stripslashes($rowbk[0]));
		$bkUhref = HOST.'/?z=index&e=u&a='.$bkUid;
	} else {callmsg("forbidden","-1");}
	?>
	<div class="bkbox">
        <table class="table0" ><tr><td width="70" height="60" align="left">
        <div class="bk_boss">
        <?php
        if (!empty($bkUid)) {
            $photo_s_url = (!empty($bkUphoto_s) && $bkUphoto_f==1)?$_ZEAI['up2'].'/'.$bkUphoto_s:HOST.'/res/photo_m'.$bkUsex.'.png';
            echo '<a href='.$href.'><img src="'.$photo_s_url.'" class="imgbdr'.$bkUsex.'"></a>';
        } else {echo '暂无版主';}?>
        </div>
        </td>
        <td align="left" valign="top" class="S12 C666">
        <font class="S16 tiaose"><?php echo $bktitle; ?></font>
        <?php
        if (!empty($bkUid)) {?>　版主：<?php echo uicon($bkUsex.$bkUgrade); ?> <a href=<?php echo $bkUhref; ?> class="sexico<?php echo $bkUsex; ?>"><?php echo $bkUnickname; ?></a>
        <?php
        }else{echo '　版主：无';}
        ?>
        <br>版块说明：<?php echo $bkcontent; ?>
        </td></tr></table>
    </div>
    <?php }?>
    
	<?php if ($total > 0) {?>
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
			$rows = $db->fetch_array($rt,'name');
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
            echo uicon($tmpgradeend)."<a href=".HOST."/?z=index&e=u&a=".$rows['enduserid']." class='C999'>".dataIO($tmpnicknameend[0],'out')."</a>";
            ?>　<font class="Cf00"><?php echo $rows['bbsnum'];?></font> / <font class="Cf00"><?php echo $rows['click'];?></font></td></tr>
            </table>
	<?php }}else{echo "<div class='nodatatips W150'><br>暂无话题<br><a href=group_post.php?mainid=$mainid&bkid=".$bkid."&bktitle=$bktitle class='aHUANGed'>发表新话题</a><br><br></div>";}?>
</main>
<?php if ($totalnum > $_ZEAI['pagesize']){ ?>
<div id="loading"></div>
	<input type="hidden" id="p" value="1" /><input type="hidden" id="tmplist" />
	<script>
	var totalpage = parseInt(<?php echo $totalpage; ?>);
	var ajax_url  = 'group_article'+ajxext+'submitok=get_ajax_list'+'&totalpage='+totalpage+'&mainid=<?php echo $mainid; ?>&bkid=<?php echo $bkid; ?>&listtype=<?php echo $listtype; ?>';
	</script>
	<script src="/js/loading_data.js"></script>
<?php }?>
<?php require_once 'bottom.php';?>
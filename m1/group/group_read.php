<?php
require_once '../../sub/init.php';
require_once 'group_init.php';
if ( !preg_match("/^[0-9]{1,8}$/",$mainid) && !empty($mainid))callmsg("该圈子不存在","-1");
if ( !preg_match("/^[0-9]{1,8}$/",$fid) || empty($fid))callmsg("该信息不存在","-1");
if ( !preg_match("/^[0-9]{1,8}$/",$bkid) && !empty($bkid))callmsg("该版块不存在!","-1");
require_once ZEAI.'sub/conn.php';
//
if ($submitok == "addupdate") {
	$content = dataIO($content,'in',10000);
	if (str_len($content)>10000 || str_len($content)<1)callmsg("回复内容请控制在1~5000字","-1");
	if ($Temp_groupbbs == $content)callmsg("请不要重复发帖","-1");
	$rt = $db->query("SELECT userid,ifin2 FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid." AND flag=1");
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$userid_main = $row[0];
		$ifin2 = $row[1];
	} else {NoUserInfo();}
	//主表
	if (!ifint($cook_uid)){header("Location: ".HOST."/?z=my");exit;}
	if ($_ZEAI['GroupWZbbsAdd'] == 1){
		$ifzl = " AND flagmod=1 ";
	}else{
		$ifzl = "";
	}
	$rt = $db->query("SELECT nickname,sex,grade FROM ".__TBL_USER__." WHERE id=".$cook_uid." AND pwd='$cook_pwd' AND flag>0".$ifzl);
	if($db->num_rows($rt) > 0){
		$row = $db->fetch_array($rt);
		$nicknamesexgrade = $row[0]."|".$row[1]."|".$row[2];
	} else {
		callmsg("资料不完整或未审核","-1");
		exit;
	}
	//查黑
	if (gzflag($cook_uid,$userid_main) == -1)$truebbs = 0;//有黑
	$truebbs = 1;
	//$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_FRIEND__." WHERE flag=-1 AND userid=".$cook_uid." AND senduserid=".$userid_main);
	//$row = $db->fetch_array($rt);
	//if ($row[0] > 0)$truebbs = 0;//有黑
	if ($truebbs == 1) {
		//成员
		if ($ifin2 == 0) {
			$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_USER__." WHERE userid=".$cook_uid." AND mainid=".$mainid." AND flag=1");
			$row = $db->fetch_array($rt);
			if ($row[0] <= 0)callmsg("请先“加入本圈子”",$_ZEAI['group2']."/group_main?mainid=".$mainid);
		}
		$addtime = YmdHis($ADDTIME);
		$addloveb = $_ZEAI['GroupBBSLoveb'];
		$db->query("INSERT INTO ".__TBL_GROUP_WZ_BBS__." (mainid,fid,content,addtime,userid,nicknamesexgrade) VALUES ('$mainid','$fid','$content','$addtime','$cook_uid','$nicknamesexgrade')");
		$db->query("UPDATE ".__TBL_GROUP_MAIN__." SET bbsnum=bbsnum+1,qloveb=qloveb+".$addloveb." WHERE id=".$mainid);
		$db->query("UPDATE ".__TBL_GROUP_WZ__." SET bbsnum=bbsnum+1,endtime='$addtime',enduserid='$cook_uid',endnicknamesexgrade='$nicknamesexgrade' WHERE id=".$fid);
	}//黑结束
	setcookie("Temp_groupbbs",$content,null,"/",$_ZEAI['CookDomain']);
	header("Location: group_read.php?fid=".$fid."&p=".$redirectpage."#bbs");
}
//
$rt = $db->query("SELECT a.photo_s,a.photo_f,b.userid,b.nicknamesexgrade,b.mainid,b.maintitle,b.bkid,b.bktitle,b.title,b.content,b.bbsnum,b.click,b.iftop,b.ifjh,b.addtime FROM ".__TBL_USER__." a,".__TBL_GROUP_WZ__." b WHERE a.id=b.userid AND a.flag=1 AND b.flag=1 AND b.id=".$fid);
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'all');
	$mainid    = $row['mainid'];
	$maintitle = dataIO($row['maintitle'],'out');
	$bkid     = $row['bkid'];
	$bktitle  = dataIO($row['bktitle'],'out');
	$title    = dataIO($row['title'],'out');
	$content  = dataIO($row['content'],'out');
	$bbsnum = $row['bbsnum'];
	$click = $row['click'];
	$iftop = $row['iftop'];
	$ifjh = $row['ifjh'];
	$addtime = $row['addtime'];
	$userid = $row['userid'];
	$nicknamesexgrade = $row['nicknamesexgrade'];
	if (!empty($nicknamesexgrade)){
	$tmpnickname = explode("|",$nicknamesexgrade);
	$nickname = dataIO($tmpnickname[0],'out');
	$sex = $tmpnickname[1];
	$grade = $tmpnickname[2];
	$photo_s  = $row[0];
	$photo_f  = $row[1];
	//$photo_pass= $row[2];
	$href = HOST.'/?z=index&e=u&a='.$userid;
	}
} else {NoUserInfo();}
//
$rt = $db->query("SELECT title,ifin,userid,userid1,userid2,userid3 FROM ".__TBL_GROUP_MAIN__." WHERE id=".$mainid." AND flag=1");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'name');
	$maintitle = dataIO($row['title'],'out');
	$ifin        = $row['ifin'];
	$userid_main = $row['userid'];
	$userid1_main = $row['userid1'];
	$userid2_main = $row['userid2'];
	$userid3_main = $row['userid3'];
} else {NoUserInfo();}
//
$userid_bk="NO";$bkid=intval($bkid);
if (ifint($cook_uid)){
	$rtbk = $db->query("SELECT userid FROM ".__TBL_GROUP_BK__." WHERE id='$bkid'");
	if($db->num_rows($rtbk)){
		$rowbk = $db->fetch_array($rtbk);
		$userid_bk = $rowbk[0];
		if ( !preg_match("/^[0-9]{1,8}$/",$userid_bk) || empty($userid_bk))$userid_bk="NO";
	} else {
		callmsg("版块验证失败!","-1");
	}
}
if ($userid_main == $cook_uid || $userid1_main == $cook_uid || $userid2_main == $cook_uid || $userid3_main == $cook_uid || $cook_grade == 10 || $userid_bk == $cook_uid) {
	$authority_main = "OK";
} else {
	$authority_main = "NO";
}
if ( ($ifin == 0) && ($authority_main == "NO")) {
	$cook_uid = intval($cook_uid);
	$rt2 = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_USER__." WHERE userid='$cook_uid' AND mainid=".$mainid." AND flag=1");
	$row2 = $db->fetch_array($rt2);
	if ($row2[0] != 1)callmsg("圈子成员可以查看”！","group_main.php?mainid=".$mainid);
}
$db->query("UPDATE ".__TBL_GROUP_WZ__." SET click=click+1 WHERE id=".$fid);
//
$mini_url = 'group_article.php?mainid='.$mainid;
$mini_show  = true;$mini_title = $bktitle;$nav='trend';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
<?php echo $headmeta; ?>
<script src="www_zeai_cn.js?1"></script>
<link href="group.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require_once 'top_mini.php';?>
<div class="read">
    <a href="#content" class="hfbtn" id="hfbtn">回复</a>
    <div class="title">
        <h1><?php if ($iftop == 1)echo "<img src=images/ding.gif  alt=固顶帖>";?> 
<?php if ($ifjh == 1)echo "<img src=images/jh.gif alt=精华帖>";?> 	
<?php if ($ifin == 0)echo " <img src=images/m.gif border=0 alt=只有本圈子内成员才可以查看！>";?>
<?php echo $title;
$d1 = strtotime($addtime);
$d2 = $ADDTIME;
if (($d2-$d1) < 86400 )echo " <img src=images/new.gif border=0 alt=当天发表>";
?></h1>
        <span>阅读<?php echo $click; ?>　回帖<?php echo $bbsnum; ?></span>
        <div class="modbox">
<?php if ($authority_main == "OK"){ ?><a href="group_readoperate.php?fid=<?php echo $fid;?>&submitok=delupdate" class=tiaose onClick="return confirm('确认删除？')">删除</a><?php }?>
<?php if ( ($userid == $cook_uid) || $authority_main == "OK" ){ ?><a href="group_post.php?&submitok=mod&mainid=<?php echo $mainid; ?>&fid=<?php echo $fid; ?>&bkid=<?php echo $bkid;?>&bktitle=<?php echo $bktitle;?>" class=tiaose>修改</a><?php }?>
<?php if ($authority_main == "OK"){ ?><?php if ($iftop == 0) {?><a href="group_readoperate.php?fid=<?php echo $fid;?>&submitok=iftop1" class=tiaose  onClick="return confirm('确认固顶？')">固顶</a><?php } else {?><a href="group_readoperate.php?fid=<?php echo $fid;?>&submitok=iftop0" class=tiaose onClick="return confirm('确认取消固顶？')">取消固顶</a><?php }?><?php }?>
<?php if ($authority_main == "OK"){ ?><?php if ($ifjh == 0) {?><a href="group_readoperate.php?fid=<?php echo $fid;?>&submitok=ifjh1" class=tiaose onClick="return confirm('确认精华？')">精华</a><?php } else {?><a href="group_readoperate.php?fid=<?php echo $fid;?>&submitok=ifjh0" class=tiaose onClick="return confirm('确认取消精华？')">取消精华</a><?php }?><?php }?>
        </div>
    </div>
    <?php if ($p == 1 || empty($p)) {?>
	<li>
    	<dt>
        <table class="table0">
        <tr>
        <td width="50" align="left">
        <a href="<?php echo $href; ?>"><?php
			$sexcolor = ($sex==1)?' class="lan"':' class="hong"';
            $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
            echo '<a href='.$href.'><img src="'.$photo_s_url.'" class="m imgbdr'.$sex.'"></a>';?></a>
        </td>
        <td align="left" valign="top" class="nickname"><a href="<?php echo $href; ?>"<?php echo $sexcolor; ?>><?php echo uicon($sex.$grade); ?><?php echo $nickname; ?></a><?php echo YmdHis(strtotime($addtime),'YmdHi'); ?></td>
        <td width="25" align="right" valign="top" class="tdlou">楼主</td>
        </tr>
        </table>
		</dt>
		<dd><?php echo $content; ?></dd>
    </li>
    <?php }else{if ($total > $pagesize){ echo '<div class="page">'.$pagelist.'</div>'; }}?>
    <!-- -->
    <main id="list">
        <?php 
		//
		$rt=$db->query("SELECT a.photo_s,a.photo_f,b.* FROM ".__TBL_USER__." a,".__TBL_GROUP_WZ_BBS__." b WHERE a.id=b.userid AND b.fid=".$fid." ORDER BY b.id");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			$page_stylesize = 1;require_once 'page.php';$pagesize=20;if ($p<1 || empty($p))$p=1;$mypage=new zeaipage($total,$pagesize);$pagelist = $mypage->pagebar();
			$pagemax = ceil($total / $pagesize);
			if ($total % $pagesize == 0){
				$redirectpage = $pagemax+1;
			} else {
				$redirectpage = $pagemax;
			}
			$db->data_seek($rt,($p-1)*$pagesize);
		}
        if ($total > 0) {
            for($i=1;$i<=$pagesize;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows) break;
                $nicknamesexgrade = explode("|",$rows['nicknamesexgrade']);
                $uid = $rows['userid'];
                $nickname = dataIO($nicknamesexgrade[0],'out');
                $sex = $nicknamesexgrade[1];
                $grade = $nicknamesexgrade[2];
                $photo_s  = $rows['photo_s'];
                $photo_f  = $rows['photo_f'];
                $addtime  = $rows['addtime'];
                $content  = dataIO($rows['content'],'out');
                $content  = ($rows['flag'] == 1)?dataIO($rows['content'],'out'):'<font class="C999">该帖已被冻结或删除！</font>';
                $href = HOST.'/?z=index&e=u&a='.$uid;
                $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
                $img_str     = '<img src="'.$photo_s_url.'" class="m imgbdr'.$sex.'">';
                $addtime_str = YmdHis(strtotime($addtime),'YmdHi');
                $n = ($p == 1)?$i:$i+$pagesize*($p-1);
				
				
                ?>
                <li>
                    <dt>
                    <table class="table0"><tr>
                    <td width="50" align="left">
                    <a href="<?php echo $href; ?>"><?php
                        $sexcolor = ($sex==1)?' class="lan"':' class="hong"';
                        $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
                        echo '<a href='.$href.'><img src="'.$photo_s_url.'" class="m imgbdr'.$sex.'"></a>';?></a>
                    </td>
                    <td align="left" valign="top" class="nickname"><a href="<?php echo $href; ?>"<?php echo $sexcolor; ?>><?php echo uicon($sex.$grade).$nickname; ?></a><?php echo YmdHis(strtotime($addtime),'YmdHi'); ?></td>
                    <td width="80" align="right" valign="top" class="tdlou">[<?php echo $n;?>楼]　<a href="#top" class="tiaose">TOP</a></td>
                    </tr></table>
                    </dt>
                    <dd><?php echo $content; ?></dd>
                </li>
        <?php }}else{echo "<div class='nodatatipsS'>暂无回帖</div>";}?>
        <?php if ($total > $pagesize){ echo '<div class="page">'.$pagelist.'</div>'; } ?>
    </main>
    <!-- --><a name=#bbs></a>
    <form method="post" action="<?php echo $SELF; ?>" name="zeaicn.form" id="ZEAI_form_detail" class="bmform" onsubmit="return chkform_detail();">
    <textarea id="content" name="content" placeholder="我想说两句...请文明发言~~" class="content"></textarea>
    <input type="submit" class="btn2HUANG" value="开始发表" />
    <input name="submitok" type="hidden" value="addupdate">
    <input name="mainid" type="hidden" value="<?php echo $mainid; ?>">
    <input name="bkid" type="hidden" value="<?php echo $bkid; ?>">
    <input name="fid" type="hidden" value="<?php echo $fid; ?>">
    <input name="bktitle" type="hidden" value="<?php echo $bktitle; ?>">
    <input type="hidden" name="redirectpage" value="<?php echo $redirectpage; ?>">
    </form>
    <!-- -->
</div>
<script src="group.js"></script>
<?php require_once 'bottom.php';?>
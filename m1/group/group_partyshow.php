<?php
require_once '../../sub/init.php';
require_once 'group_init.php';
if ( !preg_match("/^[0-9]{1,8}$/",$mainid) && !empty($mainid))callmsg("该圈子不存在","-1");
if ( !preg_match("/^[0-9]{1,8}$/",$fid) || empty($fid))callmsg("该信息不存在","-1");
require_once ZEAI.'sub/conn.php';
//
if ($submitok == "addupdate" || $submitok == "bmupdate") {
	if ( (str_len($content)>1000 || str_len($content)<1) && ($submitok == "addupdate") )callmsg("留言请控制在1~1000字节以内","-1");
	if ( (str_len($tel)>200 || str_len($tel)<8) && ($submitok == "bmupdate") )callmsg("请留下你的手机／电话","group_bm.php?mainid=".$mainid."&fid=".$fid."&mbkind=".$mbkind);
	if (!ifint($cook_uid)){header("Location: ".HOST."/?z=my");exit;}
	$rt = $db->query("SELECT nickname,sex,grade,photo_s FROM ".__TBL_USER__." WHERE id='$cook_uid' AND pwd='$cook_pwd' AND flag=1");
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$nicknamesexgrade = $row[0]."|".$row[1]."|".$row[2];
	} else {
		header("Location: ".HOST."/?z=my");exit;
	}
	$addtime = YmdHis($ADDTIME);
	if ($submitok == "addupdate") {
		$content = dataIO($content,'in',1000);
		$db->query("INSERT INTO ".__TBL_GROUP_CLUB_BBS__." (mainid,clubid,content,addtime,userid,nicknamesexgrade) VALUES ('$mainid','$fid','$content','$addtime','$cook_uid','$nicknamesexgrade')");
		$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET gbooknum=gbooknum+1 WHERE id=".$fid);
		header("Location: group_partyshow.php?fid=".$fid."&p=".$redirectpage."#bbs");
	} elseif ($submitok == "bmupdate") {
		$rt = $db->query("SELECT flag FROM ".__TBL_GROUP_CLUB__." WHERE flag>0 AND id=".$fid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$flag = $row[0];
		} else {NoUserInfo();}
		if ($flag == 1) {
			$rt = $db->query("SELECT COUNT(*) FROM ".__TBL_GROUP_CLUB_USER__." WHERE clubid=".$fid." AND userid=".$cook_uid);
			$row = $db->fetch_array($rt);
			$tmpcnt = $row[0];
			if ($tmpcnt > 0)callmsg("请不要重复报名～","-1");
			$tel = dataIO($tel,'in');
			$db->query("INSERT INTO ".__TBL_GROUP_CLUB_USER__." (userid,mainid,clubid,addtime,tel) VALUES ('$cook_uid','$mainid','$fid','$addtime','$tel')");
			$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET bmnum=bmnum+1 WHERE id=".$fid);
		} else {
			callmsg("本活动已截止报名!","-1");
		}
		callmsg("报名成功!","group_partyshow.php?fid=".$fid);
	}
}
//

if ($p == 1 || empty($p)){
	$rt = $db->query("SELECT * FROM ".__TBL_GROUP_CLUB__." WHERE flag>0 AND id=".$fid);
}else{
	$rt = $db->query("SELECT mainid,title,gbooknum FROM ".__TBL_GROUP_CLUB__." WHERE flag>0 AND id=".$fid);
}
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'all');
	$mainid = $row['mainid'];
	$title = htmlout(stripslashes($row['title']));
	if ($p == 1 || empty($p)){
		$kind = htmlout(stripslashes($row['kind']));
		$hdtime = htmlout(stripslashes($row['hdtime']));
		$address = htmlout(stripslashes($row['address']));
		$jtlx = htmlout(stripslashes($row['jtlx']));
		$num_n = $row['num_n'];
		$num_r = $row['num_r'];
		$rmb_n = $row['rmb_n'];
		$rmb_r = $row['rmb_r'];
		$tbsm = dataIO($row['tbsm'],'out');
		$content = dataIO($row['content'],'out');
		$flag = $row['flag'];
		$click = $row['click'];
		$jzbmtime = $row['jzbmtime'];
		$addtime = $row['addtime'];
		$bmnum = $row['bmnum'];
	}
	$gbooknum = $row['gbooknum'];
} else {NoUserInfo();}
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
$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET click=click+1 WHERE id=".$fid);
//
$mini_url = 'group_party.php?mainid='.$mainid;
$mini_show  = true;$mini_title = $maintitle;$nav='trend';
//require_once ZEAI."api/weixin/jssdk.php";	
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
<form id="bmform" action="group_bm.php" method=get>
<div class="read">
    <div class="title">
        <h1><?php echo $title;?></h1>
        <span>人气<?php echo $click; ?>　留言<?php echo $gbooknum; ?>　<a href="#content" class="tiaose">我要留言</a></span>
        
    </div>
<?php if ($p == 1 || empty($p)) {
$d1  = $ADDTIME;
$d2  = strtotime($jzbmtime);
$totals  = ($d2-$d1);
$day     = intval( $totals/86400 );
$hour    = intval(($totals % 86400)/3600);
$hourmod = ($totals % 86400)/3600 - $hour;
$minute  = intval($hourmod*60);
if ($flag >2)$totals = -1;
?>
	<li style="border:0">
    	<dt>
<table class="table0 partytd">
<tr class=tdbg>
<td width="60" height="30" align="left"><font class=tiaose>发 起 人</font></td>
<td align="left"><?php echo "<i class='s".$sex.$grade."'></i><a href=".HOST."/?z=index&e=u&a=".$userid.">".$nickname."</a>";?></td>
</tr>
<tr>
<td height="30" align="left"><font class=tiaose>状　　态</font></td>
<td align="left"><?php 
switch ($flag){ 
case 0:
echo "<font color=red>审核中</font>";
break;
case 1:
echo "<font color=0066CC>正在报名...</font>";
break;
case 2:
echo '<font color=#ff6600>活动进行中</font>';
break;
case 3:
echo "<font color=#349933>圆满结束</font>";
break;
}
?>　<a href="#bmlist" class="aHUI">已报名<font class="Cf00"><?php echo $bmnum; ?></font>人</a></td>
</tr>
<tr class=tdbg>
<td height="30" align="left" valign="top">&nbsp;</td>
<td align="left" class="C999">
<style type="text/css"> 
.timestyle {color:#f00;font-size:18px;font-weight:bold}
.timestyletext {color:#999}
</style>
<?php 
if ($totals > 0) {
$outtime = "";
if ($day > 0){
$outtime .= "报名还有 <span class=timestyle>$day</span> 天 ";
} else {
$outtime .= "报名还有 ";
}
$outtime .= "<span class=timestyle>$hour</span> 小时 <span class=timestyle>$minute</span> 分";
$outtime .= "　<input type='submit' value='我要报名' class='btn'>";

} else {
$outtime = "<font color=#999999><b>报名已经结束</b></font>";
if ($flag == 1)$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET flag=3 WHERE id=".$fid);
$mainflag = 3;
}
echo '<span class=timestyletext> '.$outtime.'</span>';
echo '<br>截止报名日期到：'.$jzbmtime.'　'.getweek(YmdHis($jzbmtime,'Ymd'));
?></td>
</tr>
<tr>
<td height="30" align="left"><font class=tiaose>类　　型</font></td>
<td align="left"><?php echo $kind; ?></td>
</tr>
<tr class=tdbg>
<td height="30" align="left"><font class=tiaose>时　　间</font></td>
<td align="left"><?php echo $hdtime; ?></td>
</tr>
<tr>
<td height="30" align="left"><font class=tiaose>地　　点</font></td>
<td align="left"><?php echo $address; ?></td>
</tr>
<tr class=tdbg>
<td height="30" align="left"><font class=tiaose>交通路线</font></td>
<td align="left"><?php echo $jtlx; ?></td>
</tr>
<tr>
<td height="30" align="left"><font class=tiaose>邀请人数</font></td>
<td align="left">男士 <?php if ($num_n == 0){echo '不限';}else{echo '<b><font color=#FF0000>'.$num_n.'</font></b> 人';}?> ， 女士 <?php if ($num_r == 0){echo '不限';}else{echo '<b><font color=#FF0000>'.$num_r.'</font></b> 人';}?></td>
</tr>
<tr class=tdbg>
<td height="30" align="left"><font class=tiaose>费　　用</font></td>
<td align="left">男士 <?php if ($rmb_n == 0){echo '免费或AA制';}else{echo '<b><font color=#FF0000>'.$rmb_n.'</font></b> 元';}?>
， 女士 <?php if ($rmb_r == 0){echo '免费或AA制';}else{echo '<b><font color=#FF0000>'.$rmb_r.'</font></b> 元';}?></td>
</tr>
<tr>
<td height="30" align="left"><font class=tiaose>特别说明</font></td>
<td align="left"><?php echo $tbsm; ?></td>
</tr>
</table>
<input name="mainid" type="hidden" value="<?php echo $mainid;?>">
<input name="fid" type="hidden" value="<?php echo $fid;?>"><input name="mbkind" type="hidden" value="<?php echo $mbkind;?>">
<input name="submitok" type="hidden" value="bmupdate">
        </dt>
		<dd>
        <!--  -->
		<?php
        $rt=$db->query("SELECT path_s,path_b FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE clubid='$fid' ORDER BY id DESC LIMIT 5");
        $partytotal = $db->num_rows($rt);
        if($partytotal>0){
			echo '<div class="party_show_photo">';	
			for($j=1;$j<=$partytotal;$j++) {
				$rows = $db->fetch_array($rt,'all');
				if(!$rows) break;
				$id     = $rows['id'];
				$path_s = $rows['path_s'];
				$path_b = $rows['path_b'];
				$dst_s    = $_ZEAI['up2'].'/'.$path_s;
				$dst_b    = $_ZEAI['up2'].'/'.$path_b;
				$pic_list .= '"'.$dst_b.'",';
				//$flagstr = ($flag == 0)?'<span>审核中</span>':'';
				$list_str  = '';
				$list_str .= '<img src="'.$dst_s.'" onclick="picview(\''.$dst_b.'\')">';
				echo $list_str;
			}
			$pic_list = rtrim($pic_list,',');
			echo '</div>';
		}
		?>
        <!--  -->
		<?php echo $content; ?>
        </dd>
    </li>
    <?php }?>
</div>
<!-- -->
<div class="main">
	<h2>当前报名<font class="Cf00"><input id="bmlist" type="text" class="maodian" value="<?php echo $bmnum; ?>" readonly></font>人<a href="javascript:o('bmform').submit();">我要报名</a></h2>
    <em class="ulist">
		<?php
        $rt=$db->query("SELECT b.id,b.userid,a.nickname,a.sex,a.photo_s,a.photo_f FROM ".__TBL_USER__." a,".__TBL_GROUP_CLUB_USER__." b WHERE a.id=b.userid AND b.clubid=".$fid." ORDER BY b.id DESC LIMIT 8");
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
        <?php }echo "<a href='group_partyuser.php?fid=".$fid."' class='btmmore btmmore_party'>查看更多</a>";}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
	</em>
    </div>
</div>
</form>
<!-- -->
<div class="main">
	<h2>活动留言<a href="#content">我要留言</a></h2>
    <div class="read">
        <!-- -->
        <main id="list">
            <?php 
            //
            $rt=$db->query("SELECT a.photo_s,a.photo_f,b.* FROM ".__TBL_USER__." a,".__TBL_GROUP_CLUB_BBS__." b WHERE a.id=b.userid AND b.clubid=".$fid." ORDER BY b.id");
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
                    $img_str     = '<img src="'.$photo_s_url.'" class="imgbdr'.$sex.'">';
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
                        <td align="left" valign="top" class="nickname"><a href="<?php echo $href; ?>"<?php echo $sexcolor; ?>><i class="s<?php echo $sex.$grade; ?>"></i><?php echo $nickname; ?></a><?php echo YmdHis(strtotime($addtime),'YmdHi'); ?></td>
                        <td width="80" align="right" valign="top" class="tdlou">[<?php echo $n;?>楼]　<a href="#top" class="tiaose">TOP</a></td>
                        </tr></table>
                        </dt>
                        <dd><?php echo $content; ?></dd>
                    </li>
            <?php }}else{echo "<div class='nodatatipsS'>暂无回帖</div>";}?>
            <?php if ($total > $pagesize){ echo '<div class="page">'.$pagelist.'</div>'; } ?>
        </main>
        <!-- --><a name=#bbs></a>
        <form method="post" action="<?php echo $SELF; ?>" name="zeaicn.form" id="ZEAI_form_detail" class="bmform" onsubmit="return chkform_party();">
        <textarea id="content" name="content" placeholder="我想说两句...请文明发言~~" class="content"></textarea>
        <input type="submit" class="btn2HUANG" value="开始留言" />
        <input name="submitok" type="hidden" value="addupdate">
        <input name="mainid" type="hidden" value="<?php echo $mainid; ?>">
        <input name="fid" type="hidden" value="<?php echo $fid; ?>">
        <input type="hidden" name="redirectpage" value="<?php echo $redirectpage; ?>">    
        </form>
        <!-- -->
    </div>
</div>
<?php require_once 'bottom.php';?>
<script>
function picview(url) {
	wx.previewImage({
		current: url,
		urls: [<?php echo $pic_list; ?>]
	});
}
</script>
<script src="group.js"></script>

<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
if ( !ifint($uid))callmsg("forbidden","-1");
$rt = $db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,heigh,regtime,tgflag FROM ".__TBL_USER__." WHERE tguid=".$uid." ORDER BY id DESC");
$total = $db->num_rows($rt);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/js/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<style>
.main{background:#fff;padding:20px 0;border-top:#e4e4e4 1px solid;border-bottom:#e4e4e4 1px solid;margin:20px 0 20px 0;clear:both;overflow:auto}
.main .blank{padding-top:50px}
.main dl{width:94%;padding:0 3%;height:70px;overflow:hidden;border-bottom:#eee 1px solid;position:relative;-webkit-overflow-scrolling:touch;-webkit-user-select:none;-moz-user-select:none;cursor:pointer}
.main dl:hover{background-color:#f8f8f8}
.main dl:last-child{border-bottom:0}
.main dl dt,.main dl dd{float:left;display:block;line-height:30px;margin-top:10px}
.main dl dt{width:50px;font-size:14px;text-align:left}
.main dl dt img{width:50px;height:50px;border-radius:25px;display:block}
.main dl dd{width:-webkit-calc(95% - 50px);margin:10px 0 0 5%;text-align:left;line-height:normal;position:relative}
.main dl dd h2,.main dl dd h3{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal}
.main dl dd h2{font-size:16px;margin-top:3px}
.main dl dd h3{font-size:12px;color:#aaa;margin-top:6px}
.main dl .time{position:absolute;right:10px;top:15px;font-size:12px;color:#999}
.tabmenu li:hover{border-color:#e4e4e4;color:#8d8d8d}

.main dl .tgflag{position:absolute;right:10px;top:35px;text-align:center}
.main dl .tgflag div{color:#999;font-size:12px}
.main dl .tgflag div font{color:#f00;font-size:16px}
.main dl .tgflag span{display:inline-block;padding:2px 5px;color:#fff;font-size:12px;background-color:#aaa;border-radius:1px}

</style>
<body>
<div class="navbox">
<a href="<?php echo $SELF; ?>?uid=<?php echo $uid;?>" class="ed">推荐会员（<?php echo $total; ?>人）</a>
<div class="clear"></div></div>
<?php
if ($total <= 0 ) {
	echo "<div class='nodatatipsS' style='margin:120px auto'><br>...暂无信息...<br><br></div>";exit;
} else {
	$page_skin = 1;$pagesize=12;require_once ZEAI.'sub/page.php';
?>
<form name="FORM" method="post" action="<?php echo $SELF; ?>">
<div class="main">
	<?php
	if ($total > 0) {
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt);
			if(!$rows)break;
			$uid     = $rows[0];
			//
			$nickname = dataIO($rows[1],'out');
			$sex      = $rows[2];
			$grade    = $rows[3];
			$photo_s  = $rows[4];
			$photo_f  = $rows[5];
			$areatitle= $rows[6];
			$birthday = $rows[7];
			$heigh    = $rows[8];
			$regtime  = YmdHis($rows[9]);
            $tgflag   = $rows[10];
			//
			$birthday_str  = (@getage($birthday)<=0)?'':@getage($birthday).'岁，';
			$heigh_str     = (empty($heigh))?'':$heigh.'cm，';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			//
			$href        = HOST.'/u/umain.php?uid='.$uid;
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/images/photo_s'.$sex.'.png';
			$imgbdr      = (empty($photo_s) || $photo_f==0)?' class="imgbdr'.$sex.'"':'';
            $tgflag_str  = ($tgflag == 1)?'<div><font>'.$_ZEAI['money_tg'].'元</font></div>':'<span>待验证</span>';
	?>
    <dl onClick="openurl_('<?php echo $href; ?>')">
        <dt><img src="<?php echo $photo_s_url; ?>"<?php echo $imgbdr; ?>></dt>
        <dd><h2><?php echo uicon($sex.$grade) ?> <?php echo $nickname; ?></h2><h3><?php echo $birthday_str.$heigh_str; ?><?php echo $areatitle_str; ?></h3></dd>
        <div class="time"><?php echo $regtime; ?></div>
        <div class="tgflag"><?php echo $tgflag_str; ?></div>
    </dl>
	<?php }}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
</div>
<table class="table0" style="width:95%;">
  <tr>
    <td align="right" class="list_page"><?php echo '<b>'.$pagelist.'</b>';echo "　".$pagelistinfo;?></td>
  </tr>
</table>
</form>
<?php }?>
<br><br>
<?php require_once 'bottomadm.php';?>
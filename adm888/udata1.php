<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('udata',$QXARR))exit(noauth());
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<style>
#tmp input{margin-right:10px}
#tmp .tr{margin-bottom:10px}
.jsonlist{border-radius:2px;display:inline-block;background-color:#aaa;padding:2px 7px;margin:3px 10px 3px 0}
</style>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js"></script>
<body>
<div class="navbox">
<a href="udata1.php"<?php echo ($kind == 1 || empty($kind))?' class="ed"':'';?>>内置字段(不可更改)</a>
<a href="udata2.php"<?php echo ($kind == 2)?' class="ed"':'';?>>基本字段(改子选项)</a>
<a href="udata3.php">详细资料字段</a>

<div class="clear"></div></div><div class="fixedblank"></div>
<?php
$rt = $db->query("SELECT id,fieldname,title,subjsonstr,flag,px,subkind,bz FROM ".__TBL_UDATA__." WHERE kind=1 ORDER BY px DESC,id");
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	if ($submitok !== "add")echo "<div class='nodatatips'>... 暂无内容 ...></div>";
} else {    
	$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
?>
<table class="tablelist Mtop20">
<tr>
	<th width="50">ID</th>
	<th width="80">变量</th>
<th width="80">类型</th>
<th width="120" align="right" class="Pright10">字段名称</th>
	<th>子选项</th>
<th width="1">&nbsp;</th>
<?php if (($t == 2)){?><?php }?>
</tr>
<?php
for($i=1;$i<=$pagesize;$i++) {
	$rows = $db->fetch_array($rt);
	if(!$rows) break;
	$id = $rows['id'];
	$title = dataIO($rows['title'],'out');
	$fieldname = dataIO($rows['fieldname'],'out');
	$jsonstr= dataIO($rows['subjsonstr'],'out');
	$flag = $rows['flag'];
	$px = intval($rows['px']);
	$subkind = intval($rows['subkind']);
	$bz = dataIO($rows['bz'],'out');
?>
<tr>
<td width="50" height="40"><?php echo $id;?></td>
<td width="80"><?php echo $fieldname;?></td>
<td width="80">
<?php
switch ($subkind) {
	case 1:echo'文本输入';break;
	case 2:echo'单选';break;
	case 3:echo'多选';break;
	case 4:echo'区间';break;
	default:echo '特殊';break;
}
?>
</td>
<td width="120" align="right" class="Pright10 S14"><?php echo $title.$bz;?></td>
<td class="Cfff"><?php
$a = json_decode($jsonstr);
for($j=0;$j<@count($a);$j++) {
	echo '<span class="jsonlist">'.$a[$j]->i.'：';
	echo $a[$j]->v;
	echo '</span>';
}
?></td>
<td width="1">&nbsp;</td>
<?php if (($t == 2)){?><?php }?>
</tr>
<?php } ?>
<?php if ($total > $pagesize){?>
<tfoot><tr>
<td colspan="6" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
</tr></tfoot>
<?php } ?>
</table>
<?php } ?>
<?php require_once 'bottomadm.php';?>
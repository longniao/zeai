<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
switch ($submitok) {
	case"删除":
		$tmeplist = $list;
		if(empty($tmeplist))callmsg("请选择您要删除的信息！","-1");
		if(!is_array($tmeplist))callmsg("Forbidden!","-1");
		if(count($tmeplist)>=1){
		  foreach($tmeplist as $value){
			if(!ifint($value))callmsg("forbidden","-1");
			$db->query("DELETE FROM ".__TBL_LOVEB_LIST__." WHERE type=1 AND id='$value'");
		  }
		}
		header("Location: $SELF?p=$p&kind=".$kind);
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<div class="navbox"><a href="<?php echo $SELF; ?>" class="ed">人民币清单（总数：<font color="#FF0000"><?php
$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_LOVEB_LIST__." WHERE type=1");
$rowss = $db->fetch_array($rtt);
echo $rowss[0];
?></font>）</a><div class="clear"></div></div>
<table class="table0 Mbottom10">
<tr>
<td align="left" class="S14">&nbsp;</td>
<td width="210" align="right" class="S14"><form name="form2" method="get" action="<?php echo $SELF; ?>">
  <input name="kuid" type="text" id="kuid" size="20" maxlength="8" value="<?php echo $kuid; ?>" class="input" placeholder="按会员ID号筛选">
  <input type="submit" value="搜索" class="btnLAN" />
  </form>
</td>
<td width="210" align="right" class="S14">

<form name="form1" method="get" action="<?php echo $SELF; ?>">
  <input name="keyword" type="text" id="keyword" size="20" maxlength="25" value="<?php echo $keyword; ?>" class="input" placeholder="按内容搜索">
  <input type="submit" value="搜索" class="btnLAN" />
</form>
</td>
</tr>
</table>
<?php
$SQL = " WHERE a.uid=b.id ";
if (!empty($keyword))$SQL .= " AND ( a.content LIKE '%".trimm($keyword)."%' ) ";
if (ifint($kuid))$SQL .= " AND a.uid =".$kuid;
$rt = $db->query("SELECT a.*,b.sex,b.grade,b.nickname FROM ".__TBL_LOVEB_LIST__." a,".__TBL_USER__." b ".$SQL." AND a.type=1 ORDER BY a.id DESC LIMIT ".$_ZEAI['limit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodatatips'><span>Sorry!</span> 　...暂无信息...</div>";
} else {    
	$pagesize=15;
	if ($p<1)$p=1;
	require_once ZEAI.'sub/pageadmin.php';
	$mypage=new gylpage($total,$pagesize);
	$pagelist = $mypage->pagebar(1);
	$pagelistinfo = $mypage->limit2();
	$db->data_seek($rt,($p-1)*$pagesize);
?>
<form name="FORM" method="post" action="<?php echo $SELF; ?>">
<script>
var checkflag = "false";
var bg='';
var bg1      = '<?php echo $_Style['list_bg1']; ?>';
var bg2      = '<?php echo $_Style['list_bg2']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/zeai_tablelist.js" type="text/javascript"></script>
<table class="tablelist">
<tr>
<td width="90" height="20" align="left" class="list_title">ID</td>
<td width="150" align="left" class="list_title">会员</td>
<td width="180" align="left" class="list_title">结算时间</td>
<td align="left" class="list_title">结算项目</td>
<td width="100" align="center" class="list_title">加减</td>
<td width="100" align="center" class="list_title">余额</td>
</tr>
<?php
for($i=1;$i<=$pagesize;$i++) {
	$rows = $db->fetch_array($rt);
	if(!$rows) break;
	$id = $rows['id'];
	$uid = $rows['uid'];
	$content = dataIO($rows['content'],'out');
	$content = str_replace($keyword,"<font color=red><b>".$keyword."</b></font>",$content);
	$num = $rows['num'];
	$endnum  = $rows['endnum'];
	if ($num<0){
		$numstyle = " C00f";
	}else{
		$numstyle = " Cf00";
		$num = '+'.$num;
	}
	$addtime = YmdHis($rows['addtime']);
	//
	$sex      = $rows['sex'];
	$grade    = $rows['grade'];
	$nickname = dataIO($rows['nickname'],'out');
	if (empty($nickname))$nickname = '未填';
	$href = $_ZEAI['user_2domain'].'/'.$uid;
?>
<tr <?php echo tr_mouse($i,$id);?>>
<td width="90" align="left" class="list_td C999"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="chk<?php echo $id;?>" class="checkbox" onclick="chkbox(<?php echo $i;?>,<?php echo $id;?>)"><?php echo $id; ?></td>
<td width="150" class="list_td" style="padding:10px 0"><?php echo get_user_grade_icon($sex.$grade); ?> <a href=<?php echo $href; ?> class="sexico<?php echo $sex; ?>" target="_blank"><?php echo $nickname; ?></a>
</td>
<td width="180" align="left" class="list_td"><?php echo $addtime;?></td>
<td align="left" class="list_td"><?php echo $content; ?></td>
<td width="100" align="center" class="list_td <?php echo $numstyle; ?>"><?php echo $num;?></td>
<td width="100" align="center" class="list_td C999"><?php echo $endnum; ?></td>
</tr>
<?php } ?>
</table>
<!-- 公司结束 -->
<table class="table0 Mtop10 Mbottom20">
  <tr>
    <td width="300" align="left" class="list_page">
    <label for="chkall"><input type="checkbox" name="chkall" value="" id="chkall" class="checkbox" onclick="chkformall(this.form)"><span id="chkalltext">全选</span></label>　
    <input type="submit" name="submitok" value="删除" class="btnLAN" accesskey="d" onClick="return confirm('确认删除？')" />
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    </td>
    <td align="right" class="list_page"><?php echo '<b>'.$pagelist.'</b>';echo "　".$pagelistinfo;?></td>
  </tr>
</table>
</form>
<?php }?>
<?php require_once 'bottomadm.php';?>
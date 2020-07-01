<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if ( !ifint($uid) && !ifint($tg_uid))callmsg("forbidden","-1");
$parameter = "uid=".$uid."&tg_uid=".$tg_uid."&kind=".$kind."&sort=".$sort."&date1=".$date1."&date2=".$date2."&Skeyword=".$Skeyword;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<body>
<?php
$SQL = "1=1";
$Skeyword = trimhtml($Skeyword);
if(ifint($uid)){
	$SQL .= " AND uid=".$uid;
}elseif(ifint($tg_uid)){
	$SQL .= " AND tg_uid=".$tg_uid;
}
switch ($sort) {
	case 'num0':$ORDER = " ORDER BY num ";break;
	case 'num1':$ORDER = " ORDER BY num DESC ";break;
	case 'id0':$ORDER  = " ORDER BY id ";break;
	case 'id1':$ORDER  = " ORDER BY id DESC ";break;
	default:$ORDER     = " ORDER BY id DESC ";break;
}
if ($kind == 8910){
	$SQL .= " AND (kind=8 OR kind=9 OR kind=10)";
}else{
	if (ifint($kind))$SQL .= " AND kind=".$kind;
}
if (!empty($Skeyword))$SQL .= " AND ( content LIKE '%".trimm($Skeyword)."%' ) ";
if (!empty($date1))$SQL .= " AND (addtime >= ".strtotime($date1.'00:00:01').") ";
if (!empty($date2))$SQL .= " AND (addtime <= ".strtotime($date2.'23:59:59').") ";
$rt = $db->query("SELECT content,num,endnum,addtime FROM ".__TBL_MONEY_LIST__." WHERE ".$SQL.$ORDER);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
	if (!empty($SQL))echo "<br><br><a class='aQINGed' href='javascript:history.back(-1)'>重新筛选</a>";
    echo "</div>";
} else {
	$page_skin = 1;$pagesize=11;require_once ZEAI.'sub/page.php';
	$sorthref = SELF."?uid=".$uid."&tg_uid=".$tg_uid."&kind=".$kind."&parameter=".$parameter."&sort=";
?>
<table class="table0 W95_ Mtop10">
    <tr>
    <td align="left" class="S14">
      <form name="www.yzlove.com.v6.0..QQ7144100" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按结算项目模糊查询" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="date1" value="<?php echo $date1;?>" />
        <input type="hidden" name="date2" value="<?php echo $date2;?>" />
        <input type="hidden" name="uid" value="<?php echo $uid;?>" />
        <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
        <input type="hidden" name="sort" value="<?php echo $sort;?>" />
        <input type="hidden" name="kind" value="<?php echo $kind;?>" />
        <input type="submit" value="搜索" class="btn size2 HONG2" />
        </form>   
    </td>
    <td align="right" class="S14">
        <form name="www.zeai.cn.v6.0..QQ797311" method="get" action="<?php echo $SELF; ?>">
        <input name="date1" type="text" id="date1" maxlength="25" class="input size2 W100" placeholder="起始时间" value="<?php echo $date1; ?>" autocomplete="off"> ～ 
        <input name="date2" type="text" id="date2" maxlength="25" class="input size2 W100" placeholder="结束时间" value="<?php echo $date2; ?>" autocomplete="off">
        <input type="hidden" name="Skeyword" value="<?php echo $Skeyword;?>" />
        <input type="hidden" name="uid" value="<?php echo $uid;?>" />
        <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
        <input type="hidden" name="sort" value="<?php echo $sort;?>" />
        <input type="hidden" name="kind" value="<?php echo $kind;?>" />
        <input type="submit" value="搜索" class="btn size2 HONG2" />
        </form>   
    </td>
    </tr>
</table>
<table class="tablelist W95_ Mtop10 ">
<tr>
<th width="140">结算时间
<div class="sort">
	<a href="<?php echo $sorthref."id0";?>" <?php echo($sort == 'id0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."id1";?>" <?php echo($sort == 'id1' || empty($sort))?' class="ed"':''; ?>></a>
</div></th>
<th align="left">结算项目</th>
<th width="80">
加减(元)
<div class="sort">
	<a href="<?php echo $sorthref."num0";?>" <?php echo($sort == 'num0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."num1";?>" <?php echo($sort == 'num1')?' class="ed"':''; ?>></a>
</div></th>
<th width="70">总余额(元)</th>
</tr>
<?php
for($i=1;$i<=$pagesize;$i++) {
	$rows = $db->fetch_array($rt,'name');
	if(!$rows) break;
	$id = $rows['id'];
	$content = dataIO($rows['content'],'out');
	$content = str_replace($Skeyword,"<b class='Cf00'>".$Skeyword."</b>",$content);
	$num = $rows['num'];
	$addtime = $rows['addtime'];
	$endnum  = $rows['endnum'];
	if ($num<0){
		$numstyle = " C00f";
	}else{
		$numstyle = " Cf00";
		$num = '+'.$num;
	}
?>
<tr>
<td width="140" height="30" align="left" class="lineH150 C999"><?php echo YmdHis($addtime);?></td>
<td align="left"><?php echo $content;?></td>
<td width="80" class="<?php echo $numstyle; ?>"><?php echo $num;?></td>
<td width="70"><?php echo $endnum; ?></td>
</tr>
<?php } ?>
<?php if ($total > $pagesize){?>
<tfoot>
<tr>
<td colspan="4"><?php if ($total > $pagesize)echo '<div class="pagebox">'.$pagelist.'</div>'; ?></div></td>
</tr>
</tfoot>
<?php }?>
</table>
<?php }?>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem:'#date1',type: 'date'});
laydate.render({elem:'#date2',type: 'date'});
</script>
</body>
</html>
<?php ob_end_flush();?>
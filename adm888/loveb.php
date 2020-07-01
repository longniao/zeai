<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('loveb_qd',$QXARR))exit(noauth());
switch ($submitok) {
	case"alldel":
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $value){$v=intval($value);
				$row2 = $db->ROW(__TBL_LOVEB_LIST__,"uid,num","id=".$v,'num');$uid= $row2[0];$num= $row2[1];
				$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
				$db->query("DELETE FROM ".__TBL_LOVEB_LIST__." WHERE id=".$v);
				AddLog('批量删除会员【'.$nickname.'（uid:'.$uid.'）】'.$_ZEAI['loveB'].'清单，清单金额：'.$num);
			}
			$list = (is_array($tmeplist))?implode(',',$tmeplist):'';$uid=0;
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:900px;margin:0 20px 50px 20px}
.table0{min-width:900px;width:98%;margin:0 20px 20px 20px}
.table0 form{float:left;}
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
</style>
<?php
?>
<body>
<?php if (!ifint($uid)){?>
<div class="navbox">
    <a href="loveb.php" class="ed"><?php echo $_ZEAI['loveB']; ?>账户清单<?php echo '<b>'.$db->COUNT(__TBL_LOVEB_LIST__).'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0">
    <tr>
    <td align="left" class="border0 S14">
    <form name="ZEAI_CN__form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按UID/用户名/昵称" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>
    <form name="ZEAI_CN__form2" method="get" action="<?php echo $SELF; ?>" style="margin-left:30px">
        <input name="Skeyword2" type="text" id="Skeyword2" maxlength="25" class="input size2 W150" placeholder="按结算项目内容" value="<?php echo $Skeyword2; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>  
    </td>
    </tr>
</table>
<?php }else{echo '<br>';}?>

<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL = " AND (b.id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL = " AND ( ( b.uname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}
if(!empty($Skeyword2)){
	$SQL = " AND ( a.content LIKE '%".$Skeyword2."%' )";
}
if (ifint($uid)){
	$SQL = " AND (a.uid=$uid) ";
	$_ADM['admPageSize']=8;
}
switch ($sort) {
	default:$SORT = " ORDER BY id DESC ";break;
}
$rt = $db->query("SELECT a.*,b.sex,b.grade,b.nickname FROM ".__TBL_LOVEB_LIST__." a,".__TBL_USER__." b WHERE a.uid=b.id ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合";
	if (!ifint($uid))echo"<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a>";
	echo "</div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo $SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70">ID</th>
    <th width="220" align="left">会员</th>
    <th width="160">结算时间</th>
	<th align="left" >结算项目</th>
    <th width="100">加减(个)</th>
    <th width="70">余额(个)</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $rows['uid'];
		$uname = strip_tags($rows['uname']);
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		if(empty($rows['nickname'])){
			$nickname = $uname;
		}
		$content = dataIO($rows['content'],'out');
		$content = str_replace($Skeyword2,"<font color=red><b>".$Skeyword2."</b></font>",$content);
		$num = $rows['num'];
		$endnum  = $rows['endnum'];
		if ($num<0){
			$numstyle = " Clan";
		}else{
			$numstyle = " C090";
			$num = '+'.$num;
		}
		$addtime = YmdHis($rows['addtime']);
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="70" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="220" align="left"><a href="<?php echo Href('u',$uid);?>" target="_blank">
        <?php
		echo uicon($sex.$grade);
		echo '<span class="middle">';
		echo $nickname."　<font class='S12 C999'>(uid：".$uid.")</font>";
		echo '</span>';
		?>
        </a></td>
      <td width="160" align="left" class="S12"><?php echo $addtime;?></td>
      <td align="left"><?php echo $content; ?></td>
	<td width="100" class="<?php echo $numstyle; ?>"><?php echo $num;?></td>
      <td width="70"><?php echo $endnum; ?></td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="7">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);">发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
</form>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';

o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'loveb'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
</script>
<script src="js/zeai_tablelist.js"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
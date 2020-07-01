<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('shop',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_shop.php';
switch ($submitok) {
	case"alldel":
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $value){$v=intval($value);
				$row2 = $db->ROW(__TBL_TIP__,"tg_uid,content","id=".$v,'num');$tg_uid= $row2[0];$content= $row2[1];
				$row2 = $db->ROW(__TBL_TG_USER__,"title","id=".$tg_uid,'num');$title= $row2[0];
				AddLog('批量删除'.$_SHOP['title'].'【'.$title.'（ID:'.$tg_uid.'）】消息通知(站内信)，内容->'.$content);
				$db->query("DELETE FROM ".__TBL_TIP__." WHERE kind=6 AND id=".$v);
			}
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
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:900px;margin:0 20px 50px 20px}
.table0{min-width:900px;width:98%;margin:0 20px 20px 20px}
.table0 form{float:left;}
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
</style>
<?php
$SQL = "a.kind=6 AND b.ifshop=1 AND a.tg_uid=b.id";
$Skey = trimhtml($Skey);
if (ifint($Skey)){
	$SQL .= " AND (b.id=$Skey) ";
}elseif(!empty($Skey)){
	$SQL .= " AND ( b.title LIKE '%".$Skey."%' )";
}
if(!empty($Skey2)){
	$SQL .= " AND ( a.content LIKE '%".$Skey2."%' )";
}
switch ($sort) {
	default:$SORT = " ORDER BY id DESC ";break;
}
//统计个数
$rtt   = $db->query("SELECT COUNT(*) FROM ".__TBL_TIP__." a,".__TBL_TG_USER__." b WHERE  ".$SQL);
$roww  = $db->fetch_array($rtt);
$cntnum=$roww[0];
?>
<body>
<div class="navbox">
    <a class="ed"><?php echo $_SHOP['title'];?>站内消息通知管理<?php echo '<b>'.$cntnum.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0">
    <tr>
    <td align="left" class="border0 S14">
    <form name="ZEAI_CN__form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skey" type="text" id="Skey" maxlength="25" class="input size2 W150" placeholder="按<?php echo $_SHOP['title'];?>名称/ID搜索" value="<?php echo $Skey; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>
    <form name="ZEAI_CN__form2" method="get" action="<?php echo $SELF; ?>" style="margin-left:30px">
        <input name="Skey2" type="text" id="Skey2" maxlength="25" class="input size2 W150" placeholder="按通知内容" value="<?php echo $Skey2; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>  
    </td>
    </tr>
</table>

<?php
$rt = $db->query("SELECT a.*,b.title AS cname FROM ".__TBL_TIP__." a,".__TBL_TG_USER__." b WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合";
	if (!ifint($uid))echo"<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a>";
	echo "</div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo $SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70">ID</th>
    <th width="50">&nbsp;</th>
    <th width="180" align="left">发送方</th>
    <th width="50" align="left">&nbsp;</th>
    <th width="200" align="left">接收方</th>
    <th>内容</th>
	<th width="150">时间</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$tg_uid = $rows['tg_uid'];
		$cname = strip_tags($rows['cname']);
		$cname = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$cname);
		$new = $rows['new'];
		$senduid = $rows['senduid'];
		$new = $rows['new'];
		$content = dataIO($rows['content'],'out');
		$content = strip_tags($content);
		$content = str_replace($Skey2,"<font color=red><b>".$Skey2."</b></font>",$content);
		$addtime = YmdHis($rows['addtime']);
		$new_str = ($new == 1)?'<img src="images/new.gif">':'';
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" tg_uid="<?php echo $tg_uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="70" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
      <td width="50" class="C999"><?php echo $new_str; ?></td>
        <td width="180" align="left">系统</td>
        <td width="50" align="left"><img src="images/d2.gif"></td>
      <td width="200" align="left"><?php echo $cname;?>（ID：<?php echo $tg_uid;?>）</td>
      <td align="left" class="S12"><?php echo $content; ?></td>
      <td width="150"><?php echo $addtime;?></td>
      </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="8">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnsendSHOP" value="" class="btn size2 disabled action" >发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
</form>
<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
o('btnsendSHOP').onclick = function() {sendTipFnTGU(this);}
function sendTipFnTGU(btnobj){
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要发送的用户');
		return false;
	}
	var arr = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){
		if (arr[key].checked){
			ulist.push(arr[key].getAttribute("tg_uid"));
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的用户');
	}else{
		zeai.iframe('发送消息','u_tip.php?kind=TG&ifshop=1&ulist='+ulist,600,500);
	}
}
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'shop_tip_list'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
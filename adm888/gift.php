<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('gift',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
switch ($submitok) {
	case 'addupdate':
		$title = dataIO($title,'in',20);
		$price = intval($price);
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_uploaded_file($_FILES["pic"]['tmp_name']) ){ 
			$FILES = $_FILES["pic"];
			if (!empty($FILES)){
				$dbpicname = setphotodbname('gift',$FILES['tmp_name'],$_SESSION["admuid"].'_');
				if ($dbpicname){
					if (!up_send($FILES,$dbpicname,0,$_UP['upSsize']))continue;
					$tmppicurl = $_ZEAI['up2']."/".$dbpicname;
					if (!ifpic($tmppicurl))continue;
					$db->query("INSERT INTO ".__TBL_GIFT__." (title,price,picurl,px) VALUES ('$title',$price,'$dbpicname',".ADDTIME.")");
					AddLog('【礼物管理】新增礼物->【'.$title.'】');
				}
			}
		}
		header("Location: ".SELF);
	break;
	case 'modupdate':
		if(!ifint($classid))alert_adm("forbidden","-1");
		$title = dataIO($title,'in',20);
		$price = intval($price);
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_uploaded_file($_FILES['pic']['tmp_name']) ){ 
			$FILES = $_FILES['pic'];
			if (!empty($FILES)){
				$dbpicname = setphotodbname('gift',$FILES['tmp_name'],$_SESSION["admuid"].'_');
				if ($dbpicname){
					if (!up_send($FILES,$dbpicname,0,$_UP['upSsize']))continue;
					$tmppicurl = $_ZEAI['up2']."/".$dbpicname;
					if (!ifpic($tmppicurl))continue;
					$db->query("UPDATE ".__TBL_GIFT__." SET picurl='$dbpicname' WHERE id=".$classid);
				}
			}
		}
		$db->query("UPDATE ".__TBL_GIFT__." SET title='$title',price='$price' WHERE id=".$classid);
		AddLog('【礼物管理】修改礼物内容->【'.$title.'】');
		header("Location: ".SELF."?submitok=list&classid=$classid&p=$p");
	break;
	
	case "delpicupdate":
		if ( !ifint($classid) )callmsg("forbidden.","-1");
		$rt = $db->query("SELECT picurl FROM ".__TBL_GIFT__." WHERE id=".$classid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'num');
			$picurl = $row[0];
			up_send_admindel($picurl);
		}
		$db->query("UPDATE ".__TBL_GIFT__." SET picurl='' WHERE id=".$classid);
		AddLog('【礼物管理】删除图标->【id:'.$classid.'】');
		header("Location: ".SELF."?submitok=mod&classid=$classid&p=$p");
	break;
	
	case"alldel":
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $value){$v=intval($value);
				$db->query("DELETE FROM ".__TBL_GIFT__." WHERE id=".$v);
				AddLog('【礼物管理】删除礼物->【id:'.$v.'】');
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case 'ding':
		if ( !ifint($clsid))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_GIFT__." SET px=".ADDTIME." WHERE id=".$clsid);
		AddLog('【礼物管理】置顶礼物->【id:'.$clsid.'】');
		header("Location: ".SELF);
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script>
function chkform(){
	if(  zeai.str_len(o('title').value) < 2 || zeai.str_len(o('title').value) > 20  ){
		zeai.msg('礼物名称必须在2～20个字节以内！',o('title'));
		return false;
	}
	if( !zeai.ifint(o('price').value,"0-9","1,7") ){
		zeai.msg('礼物价格请填正整数',o('price'));
		return false;
	}
}
</script>

</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">

<style>
.tablelist{min-width:900px;margin:0 20px 50px 20px}
.table0{min-width:900px;width:98%;margin:0 20px 20px 20px}
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
.giftpic img{max-width:60px;max-height:60px}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}

</style>
<?php
?>
<body>
<div class="navbox">
    <a class="ed">礼物管理<?php echo '<b>'.$db->COUNT(__TBL_GIFT__).'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0">
    <tr>
    <td align="left" class="border0 S14">
<button type="button" class="btn size2" onClick="zeai.openurl('gift.php?submitok=add')"><i class="ico add">&#xe620;</i>新增礼物</button>
    </td>
    <td width="300" align="right" class="border0 S14"><form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按礼物名称搜索" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form></td>
    </tr>
</table>


<!-- add -->
<?php if ($submitok == "add") {?>
<form action="<?php echo SELF; ?>" method="post" name="FORM" onSubmit="return chkform()" enctype="multipart/form-data">
<table class="table W700 Mtop100 Mbottom50">

<tr>
<td height="20" colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT">增加礼物：</td>
</tr>
<tr><td class="tdL">礼物名称</td><td class="tdR"><input name="title" type="text" class="input size2 W200" id="title" value="" maxlength="20"><span class="tips">10个字以内</span></td></tr>
<tr><td class="tdL">礼物价格</td><td class="tdR"><input name="price" type="text" class="input size2 W50" id="price" value="" maxlength="7"><span class="tips"><?php echo $_ZEAI['loveB']; ?></span></td></tr>
<tr><td class="tdL">礼物图片</td><td class="tdR"><input name="pic" type="file" id="pic" size="40"  class="input size2 W200"><span class="tips">.gif、.jpg、.png 格式</span></td></tr>
<tr>
  <td class="tdL"><input name="submitok" type="hidden" value="addupdate" /></td>
  <td class="tdR">
  <input class="btn size3 HUANG3" type="submit" value="保存"  />
  </td>
</tr>
</table>
</form>
<!-- mod -->
<?php }elseif ($submitok == "mod") {
	if ( !ifint($classid))alert_adm("forbidden","-1");
	$rt = $db->query("SELECT id,title,price,picurl FROM ".__TBL_GIFT__." WHERE id='$classid'");
	if($db->num_rows($rt)) {
		$row = $db->fetch_array($rt);
		$id = $row['id'];
		$title = dataIO($row['title'],'out');
		$price = $row['price'];
		$picurl = $row['picurl'];
		$picurl_url = $_ZEAI['up2'].'/'.$picurl;
	} else {
		alert_adm("forbidden","-1");
	}
?>
<form action="<?php echo SELF; ?>" method="post" name="FORM" onSubmit="return chkform()" enctype="multipart/form-data">
<table class="table W700 Mtop100 Mbottom50">
<tr>
<td height="20" colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT">修改礼物：</td>
</tr>
<tr><td class="tdL">礼物名称</td><td class="tdR"><input name="title" type="text" class="input size2 W200" id="title" maxlength="20" value="<?php echo $title; ?>"><span class="tips">10个字以内</span></td></tr>
<tr><td class="tdL">礼物价格</td><td class="tdR"><input name="price" type="text" class="input size2 W50" id="price" maxlength="7" value="<?php echo $price; ?>"><span class="tips"><?php echo $_ZEAI['loveB']; ?></span></td></tr>
<tr><td class="tdL">礼物图片</td><td class="tdR">


<?php if (!empty($picurl)) {?>
	<img src="<?php echo $picurl_url; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $picurl_url; ?>')">　<a href="<?php echo SELF;?>?submitok=delpicupdate&classid=<?php echo $id; ?>&p=<?php echo $p; ?>" onClick="return confirm('确认删除？')"><img src="images/delx.gif" alt="删除"></a>　
<?php }else{ 
	echo "<input name='pic' id='pic' type='file' size='40' class='input W200' /><span class='tips'>.gif、.jpg、.png 格式</span>";
}?>


</td></tr>
<tr>
  <td class="tdL"><input name="submitok" type="hidden" value="modupdate" />
  <input name="classid" type="hidden" value="<?php echo $classid; ?>" />
  <input name="p" type="hidden" value="<?php echo $p; ?>" /></td>
  <td class="tdR">
  <input class="btn size3 HUANG3" type="submit" value="保存" />
  </td>
</tr>
</table>
</form>
<!-- list -->
<?php } else {?>





<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
if (!empty($Skeyword))$SQL = " WHERE title LIKE '%".trimm(dataIO($Skeyword,'in'))."%' ";

$rt = $db->query("SELECT * FROM ".__TBL_GIFT__.$SQL." ORDER BY px DESC");
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo $SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70">ID</th>
    <th width="60" align="left">置顶</th>
    <th width="100" align="left">礼物</th>
    <th width="150" align="left">名称</th>
    <th>价格(<?php echo $_ZEAI['loveB']; ?>)</th>
	<th width="80">修改</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];
		$title = dataIO($rows['title'],'out');
		$title = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$title);
		$price = $rows['price'];
		$picurl = $_ZEAI['up2'].'/'.$rows['picurl'];
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="70" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="60" align="left"><a href="<?php echo SELF; ?>?clsid=<?php echo $id; ?>&submitok=ding" title="置顶"><img src="images/ding.gif" title="置顶" /></a></td>
        <td width="100" align="left" class="giftpic"><img src="<?php echo $picurl; ?>"></td>
        <td width="150" align="left" class="S16"><?php echo $title; ?></td>
      <td align="left" class="S12"><img src="images/loveb.gif"><?php echo $price; ?></td>
      <td width="80"><a class="edit tips" href="<?php echo SELF; ?>?classid=<?php echo $id; ?>&submitok=mod&p=<?php echo $p;?>">✎</a></td>
      </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="7">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);" style="display:none">发送消息</button>
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
		url:'gift'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
</script>
<script src="js/zeai_tablelist.js"></script>
<?php }?>

<?php }?>







<br><br><br>
<?php require_once 'bottomadm.php';?>
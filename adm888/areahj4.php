<?php
require_once '../sub/init.php';
if (!ifint($fid))alert_adm_parent('forbidden','back');
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('area',$QXARR))exit(noauth());

if ($submitok == 'ajax_addupdate'){
	if (str_len($title) >50 || empty($title) )json_exit(array('flag'=>0,'msg'=>'请输入地区名称','focus'=>'title'));
	$title = dataIO($title,'in',50);
}
switch ($submitok){
	case "ajax_addupdate":
		$db->query("INSERT INTO ".__TBL_AREAHJ4__." (title,fid,px) VALUES ('$title',$fid,".ADDTIME.")");
		json_exit(array('flag'=>1));
	break;
	case "ajax_modupdate":
		$db->query("UPDATE ".__TBL_AREAHJ4__." SET title='$title' WHERE id=".$id);
		json_exit(array('flag'=>1));
	break;
	case "ajax_delupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		//if ($db->COUNT(__TBL_AREAHJ4__,"fid=".$fid) <= 1)json_exit(array('flag'=>0,'msg'=>'亲，不能删光啊，至少要留一个啊'));
		$db->query("DELETE FROM ".__TBL_AREAHJ4__." WHERE id=".$id);
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case "ding":
		if (!ifint($id))alert_adm_parent('forbidden','back');
		$db->query("UPDATE ".__TBL_AREAHJ4__." SET px=".ADDTIME." WHERE id=".$id);
		header("Location: ".SELF."?fid=".$fid);
	break;
	case "sort_1"://一级上移
		if (!ifint($id))alert_adm_parent('forbidden','back');
		$row = $db->ROW(__TBL_AREAHJ4__,"px","id=".$id,"num");
		if ($row){$px=$row[0];}else{exit;}
		$row = $db->ROW(__TBL_AREAHJ4__,"id,px","px>".$px." AND fid=".$fid." ORDER BY px LIMIT 1","num");
		if ($row){
			$tmp_id = $row[0];
			$tmp_px = $row[1];
			$db->query("UPDATE ".__TBL_AREAHJ4__." SET px=".$tmp_px." WHERE id=".$id);
			$db->query("UPDATE ".__TBL_AREAHJ4__." SET px=".$px." WHERE id=".$tmp_id);
		}
		header("Location: ".SELF."?fid=".$fid);
	break;
	case "sort_0"://一级下移
		if (!ifint($id))alert_adm_parent('forbidden','back');
		$row = $db->ROW(__TBL_AREAHJ4__,"px","id=".$id,"num");
		if ($row){$px=$row[0];}else{exit;}
		$row = $db->ROW(__TBL_AREAHJ4__,"id,px","px<".$px." AND fid=".$fid." ORDER BY px DESC LIMIT 1","num");
		if ($row){
			$tmp_id = $row[0];
			$tmp_px = $row[1];
			$db->query("UPDATE ".__TBL_AREAHJ4__." SET px=".$tmp_px." WHERE id=".$id);
			$db->query("UPDATE ".__TBL_AREAHJ4__." SET px=".$px." WHERE id=".$tmp_id);
		}
		header("Location: ".SELF."?fid=".$fid);
	break;
}
$row3   = $db->ROW(__TBL_AREAHJ3__,"fid,title","id=".$fid,"num");
$id2    = $row3[0];
$title3 = dataIO($row3[1],'out');
//
$row2   = $db->ROW(__TBL_AREAHJ2__,"fid,title","id=".$id2,"num");
$id1    = $row2[0];
$title2 = dataIO($row2[1],'out');
//
$row1   = $db->ROW(__TBL_AREAHJ1__,"title","id=".$id1,"num");
$title1 = dataIO($row1[0],'out');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<!--ADD-->
<?php if ($submitok == "add") {?>
	<table class="table W90_ Mtop20">
	<form id="ZEAIFORM" name="ZEAIFORM" method="post">
	<tr>
	<td class="tdL">四级地区名称</td>
	<td class="tdR"><input id="title" name="title" type="text" class="W150 size2" size="30" maxlength="50" placeholder="如：XXX镇，XXX乡" autoComplete="off"></td>
	</tr>
	<tr>
	<td class="tdL">&nbsp;</td>
	<td class="tdR">
	<input name="submitok" type="hidden" value="ajax_addupdate">
	<input name="fid" type="hidden" value="<?php echo $fid;?>">
	<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
	</td>
	</tr>
	</form>
	</table>
<!--LIST-->
<?php }else{?>
	<div class="navbox">
		<a href="areahj1.php">地区管理</a>
		<a href="areahj2.php?fid=<?php echo $id1;?>">【<?php echo $title1;?>】</a>
        <a href="areahj3.php?fid=<?php echo $id2;?>">【<?php echo $title2;?>】</a>
		<a class="ed" href="#top">【<?php echo $title3;?>】下属四级地区管理<b><?php echo $db->COUNT(__TBL_AREAHJ4__,"fid=".$fid);?></b></a>
	</div>
	<div class="fixedblank"></div>
	<?php
	$rt = $db->query("SELECT id,title FROM ".__TBL_AREAHJ4__." WHERE fid=".$fid." ORDER BY px DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodatatips'>暂无内容<br><br><a class='btn size2 HUANG' onClick=\"zeai.iframe('新增【".$title1."】-【".$title2."】-【".$title3."】下属四级地区','".SELF."?submitok=add&fid=".$fid."',550,300)\">新增【".$title3."】下属地区</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;$pagemode=3;require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="12%" align="left"><button type="button" class="btn" onClick="zeai.iframe('新增【<?php echo $title1;?>】-【<?php echo $title2;?>】-【<?php echo $title3;?>】下属四级地区','<?php echo $SELF;?>?submitok=add&fid=<?php echo $fid;?>',550,300)">新增【<?php echo $title1;?>】-【<?php echo $title2;?>】-【<?php echo $title3;?>】下属四级地区</button></td>
		<td align="right"><button type="button" class="btn" id="cache">更新缓存</button></td>
	  </tr>
	</table>
	<table class="tablelist">
	<tr>
		<th width="60">ID</th>
		<th width="50" align="center">置顶</th>
		<th width="30" align="center">上移</th>
		<th width="30" align="center">下移</th>
		<th width="20" align="center">&nbsp;</th>
		<th width="200">四级地区名称</th>
		<th width="20">&nbsp;</th>
		<th>&nbsp;</th>
	<th width="20">&nbsp;</th>
		<th width="30">删除</th>
	</tr>
	<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'num');
		if(!$rows) break;
		$id = $rows[0];
		$title = dataIO($rows[1],'out');
	?>
	<tr>
		<td width="60" height="40"><?php echo $id;?></td>
		<td width="50" height="40" align="center"><a href="<?php echo SELF;?>?submitok=ding&id=<?php echo $id;?>&fid=<?php echo $fid;?>"><img src="images/zd.gif" title="置顶"></a></td>
		<td width="30" height="40" align="center" title="上移"><a class="S14 Caaa" href="<?php echo SELF;?>?submitok=sort_1&id=<?php echo $id; ?>&fid=<?php echo $fid;?>" title="上移">↑</a></td>
		<td width="30" height="40" align="center" title="下移"><a class="S14 Caaa" href="<?php echo SELF;?>?submitok=sort_0&id=<?php echo $id; ?>&fid=<?php echo $fid;?>" title="下移">↓</a></td>
		<td width="20" height="40" align="center">&nbsp;</td>
		<td width="200">
		<input id="title<?php echo $id;?>" type="text" class="W150 size1" maxlength="100" value="<?php echo $title;?>">
		<button value="<?php echo $id;?>" type="button" class="modbtn btn size1 HUANG">修改</button>
		</td>
		<td width="20" class="S14">&nbsp;</td>
		<td>&nbsp;</td>
		<td width="20">&nbsp;</td>
		<td width="30"><a value="<?php echo $id; ?>" name="<?php echo $title;?>" class="del del">✖</a></td>
	</tr>
	<?php } ?>
	<?php if ($total > $pagesize){?>
	<tfoot><tr><td colspan="10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td></tr></tfoot>
	<?php } ?>
	</table>
	<?php } ?>
<?php } ?>
<script>
<?php if ($submitok == "add") {?>
	save.onclick = function(){
		zeai.ajax({url:'areahj4'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
			if (rs.flag == 1){
				parent.location.reload(true);
			}else if(rs.flag == 0){
				parent.zeai.msg(rs.msg,o(rs.focus));
			}else{
				parent.zeai.msg(rs.msg);
			}		
		});
	}
<?php }else{ ?>
	zeai.listEach('.modbtn',function(obj){
		var id = parseInt(obj.value);
		var C = o('title'+id);
		obj.onclick = function(){
			var V = C.value;
			zeai.ajax('areahj4'+zeai.ajxext+'submitok=ajax_modupdate&id='+id+'&title='+V+'&fid=<?php echo $fid;?>',function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		}
	});
	zeai.listEach('.del',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var title = obj.getAttribute("name");
		obj.onclick = function(){
			zeai.confirm('您真的要删除【'+title+'】么？',function(){
				zeai.ajax('areahj4'+zeai.ajxext+'submitok=ajax_delupdate&id='+id+'&fid=<?php echo $fid;?>',function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	if(!zeai.empty(o('cache')))o('cache').onclick = function(){
		zeai.msg('正在更新中',{time:30});
		var postjson = {submitok:'cache_area',uu:'<?php echo $session_uid;?>',pp:'<?php echo $session_pwd;?>'};
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:postjson},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.alert(rs.msg);
		});
	}
<?php } ?>
</script>
<?php require_once 'bottomadm.php';?>
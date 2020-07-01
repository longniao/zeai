<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('area',$QXARR))exit(noauth());
if ($submitok == 'ajax_addupdate'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'请输入正确的地区ID号','focus'=>'id'));$id = abs(intval($id));
	if (str_len($title) >50 || empty($title) )json_exit(array('flag'=>0,'msg'=>'请输入地区名称','focus'=>'title'));
	$title = dataIO($title,'in',50);
}
if (!empty($submitok)){
	AddLog('【基础设置】->【户籍地区】修改');
}
switch ($submitok){
	case "ajax_addupdate":
		$row = $db->NUM('zeai.area',"title","id='$id'",__TBL_AREAHJ1__);
		if ($row)json_exit(array('flag'=>0,'msg'=>'地区【'.dataIO($row[0],'out').'】　ID：<font class=Cfc0>'.$id.'</font>　出现重复，请重试','focus'=>'id'));
		$db->query("INSERT INTO ".__TBL_AREAHJ1__." (id,title,px) VALUES ($id,'$title',".ADDTIME.")");
		json_exit(array('flag'=>1));
	break;
	case "ajax_modupdate":
		/*
		$classid = intval($classid);
		$oldid = intval($oldid);
		if ($id != $oldid){
			if ($db->NUM('zeai.area',"id","id=".$id." AND id<>".$oldid,__TBL_AREAHJ1__)){
				json_exit(array('flag'=>0,'msg'=>'地区ID【'.$id.'】出现重复，请重试','focus'=>'id'));	
			}else{
				$SQL = ",id=".$id;
			}
		}else{
			$SQL = "";	
		}
		$db->query("UPDATE ".__TBL_AREAHJ1__." SET title='$title'".$SQL." WHERE id=".$classid);
		*/
		$db->query("UPDATE ".__TBL_AREAHJ1__." SET title='$title' WHERE id=".$id);
		json_exit(array('flag'=>1));
	break;
	case "ajax_delupdate1":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		if ($db->COUNT(__TBL_AREAHJ1__) <= 1)json_exit(array('flag'=>0,'msg'=>'亲，不能删光啊，至少要留一个啊'));	
		//查2删3
		$rt=$db->query("SELECT id FROM ".__TBL_AREAHJ2__." WHERE fid=".$id);
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'num');
				if(!$rows)break;
				$id2 = $rows[0];
				//查3删4
				$rt3=$db->query("SELECT id FROM ".__TBL_AREAHJ3__." WHERE fid=".$id2);
				$total3 = $db->num_rows($rt3);
				if ($total3 > 0) {
					for($i3=1;$i3<=$total3;$i3++) {
						$rows3 = $db->fetch_array($rt3,'all');
						if(!$rows3) break;
						$id3 = $rows3[0];
						$db->query("DELETE FROM ".__TBL_AREAHJ4__." WHERE fid=".$id3);
					}
				}		
				$db->query("DELETE FROM ".__TBL_AREAHJ3__." WHERE fid=".$id2);
			}
		}
		$db->query("DELETE FROM ".__TBL_AREAHJ2__." WHERE fid=".$id);
		$db->query("DELETE FROM ".__TBL_AREAHJ1__." WHERE id=".$id);
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case "ding":
		if (!ifint($id))alert_adm_parent('forbidden','back');
		$db->query("UPDATE ".__TBL_AREAHJ1__." SET px=".ADDTIME." WHERE id=".$id);
		header("Location: ".SELF);
	break;
	case "sort_1"://一级上移
		if (!ifint($id))alert_adm_parent('forbidden','back');
		$row = $db->NUM('zeai.area',"px","id=".$id,__TBL_AREAHJ1__);
		if ($row){$px=$row[0];}else{exit;}
		$row = $db->NUM('zeai.area',"id,px","px>".$px." ORDER BY px LIMIT 1",__TBL_AREAHJ1__);
		if ($row){
			$tmp_id = $row[0];
			$tmp_px = $row[1];
			$db->query("UPDATE ".__TBL_AREAHJ1__." SET px=".$tmp_px." WHERE id=".$id);
			$db->query("UPDATE ".__TBL_AREAHJ1__." SET px=".$px." WHERE id=".$tmp_id);
		}
		header("Location: ".SELF);
	break;
	case "sort_0"://一级下移
		if (!ifint($id))alert_adm_parent('forbidden','back');
		$row = $db->NUM('zeai.area',"px","id=".$id,__TBL_AREAHJ1__);
		if ($row){$px=$row[0];}else{exit;}
		$row = $db->NUM('zeai.area',"id,px","px<".$px." ORDER BY px DESC LIMIT 1",__TBL_AREAHJ1__);
		if ($row){
			$tmp_id = $row[0];
			$tmp_px = $row[1];
			$db->query("UPDATE ".__TBL_AREAHJ1__." SET px=".$tmp_px." WHERE id=".$id);
			$db->query("UPDATE ".__TBL_AREAHJ1__." SET px=".$px." WHERE id=".$tmp_id);
		}
		header("Location: ".SELF);
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<!--ADD-->
<?php if ($submitok == "add") {?>
	<table class="table W90_ Mtop20">
	<form id="ZEAIFORM" name="ZEAIFORM" method="post">
	<tr>
	<td class="tdL">一级地区ID</td>
	<td class="tdR"><input id="id" name="id" type="text" class="W100 size2" size="30" maxlength="7" placeholder="必须为正整数"><span class="tips">例：110000</span></td>
	</tr>
	<tr>
	<td class="tdL">一级地区名称</td>
	<td class="tdR"><input id="title" name="title" type="text" class="W100 size2" size="30" maxlength="50" placeholder="如：江苏"></td>
	</tr>
	<tr>
	<td class="tdL">&nbsp;</td>
	<td class="tdR">
	<input name="submitok" type="hidden" value="ajax_addupdate">
	<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
	</td>
	</tr>
	</form>
	</table>
<!--LIST-->
<?php }else{?>
	<div class="navbox">
	<a class="ed" href="#top">户籍地区管理<b><?php echo $db->COUNT(__TBL_AREAHJ1__);?></b></a>
	</div>
	<div class="fixedblank"></div>
	<?php
	$rt = $db->query("SELECT id,title FROM ".__TBL_AREAHJ1__." ORDER BY px DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodatatips'>... 暂无内容 ...　　　<a class='btn size2 HUANG' onClick=\"zeai.iframe('新增一级地区','".$SELF."?submitok=add',550,300)\">新增一级地区</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;$pagemode=3;require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="12%" align="left"><button type="button" class="btn" onClick="zeai.iframe('新增一级地区','<?php echo $SELF;?>?submitok=add',550,300)">新增一级地区</button></td>
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
		<th width="120">一级地区名称</th>
		<th width="20">&nbsp;</th>
		<th>下属二级地区（点击下方地区进入管理）</th>
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
		<td width="50" height="40" align="center"><a href="<?php echo SELF;?>?submitok=ding&id=<?php echo $id;?>"><img src="images/zd.gif" title="置顶"></a></td>
		<td width="30" height="40" align="center" title="上移"><a class="S14 Caaa" href="<?php echo $SELF;?>?submitok=sort_1&id=<?php echo $id; ?>" title="上移">↑</a></td>
		<td width="30" height="40" align="center" title="下移"><a class="S14 Caaa" href="<?php echo $SELF;?>?submitok=sort_0&id=<?php echo $id; ?>" title="下移">↓</a></td>
		<td width="20" height="40" align="center">&nbsp;</td>
		<td width="120">
		<input id="title<?php echo $id;?>" type="text" class="W75 size1" maxlength="100" value="<?php echo $title;?>">
		<button value="<?php echo $id;?>" type="button" class="modbtn btn size1 HUANG">修改</button>
		</td>
		<td width="20" class="S14">&nbsp;</td>
		<td>
			<?php
			$rt2=$db->query("SELECT id,title FROM ".__TBL_AREAHJ2__." WHERE fid=".$id." ORDER BY px DESC");
			$total2 = $db->num_rows($rt2);
			if ($total2 > 0) {
				for($i2=1;$i2<=$total2;$i2++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$id2    = $rows2[0];
					$title2 = $rows2[1];
					echo '<a class="li" href="areahj2.php?fid='.$id.'" title="管理二级">'.$title2.'</a>';
				}
			}
			echo '<a class="btn size1" href="areahj2.php?fid='.$id.'" title="管理二级">管理二级</a>';
			?>
		</td>
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
		zeai.ajax({url:'areahj1'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
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
			zeai.ajax('areahj1'+zeai.ajxext+'submitok=ajax_modupdate&id='+id+'&title='+V,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		}
	});
	zeai.listEach('.del',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var title = obj.getAttribute("name");
		obj.onclick = function(){
			zeai.confirm('将同步删除【'+title+'】下属二级和三级地区，真的要删除么？',function(){
				zeai.ajax('areahj1'+zeai.ajxext+'submitok=ajax_delupdate1&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	if(!zeai.empty(o('cache')))cache.onclick = function(){
		zeai.msg('正在更新中',{time:30});
		var postjson = {submitok:'cache_area',uu:'<?php echo $session_uid;?>',pp:'<?php echo $session_pwd;?>'};
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:postjson},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.alert(rs.msg);
		});
	}
<?php } ?>
</script>
<?php require_once 'bottomadm.php';?>
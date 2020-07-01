<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('news_kind',$QXARR))exit(noauth());
//检查性别库 结束
if ($submitok == 'ajax_addupdate' || $submitok == 'ajax_modupdate'){
	if (empty($title) )json_exit(array('flag'=>0,'msg'=>'请输入分类名称','focus'=>'title'));
	if (str_len($title) >200)json_exit(array('flag'=>0,'msg'=>'亲，分类名称【'.$title.'】这么长有意义么？ 请不要超过20字节','focus'=>'title'));
	$title = dataIO($title,'in',200);
}
switch ($submitok){
	case "ajax_addupdate":
		$db->query("INSERT INTO ".__TBL_NEWS_KIND__." (title,px) VALUES ('$title',".ADDTIME.")");
		AddLog('【文章管理】新建文章分类->【'.$title.'】');
		json_exit(array('flag'=>1));
	break;
	case "ajax_modupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$db->query("UPDATE ".__TBL_NEWS_KIND__." SET title='$title' WHERE id=".$id);
		AddLog('【文章管理】修改文章分类名称->【'.$title.'】');
		json_exit(array('flag'=>1));
	break;
	case "ajax_delupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		//$db->query("DELETE FROM ".__TBL_NEWS__." WHERE kind=".$id);
		$db->query("DELETE FROM ".__TBL_NEWS_KIND__." WHERE id=".$id);
		AddLog('【文章管理】删除文章分类->【id:'.$id.'】');
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($id))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_NEWS_KIND__." SET px=".ADDTIME." WHERE id=".$id);
		AddLog('【文章管理】置顶文章分类->【id:'.$id.'】');
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
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<!--ADD-->
<?php if ($submitok == "add") {?>
	<table class="table W90_ Mtop50">
	<form id="ZEAIFORM" name="ZEAIFORM" method="post" >
	<tr>
	<td class="tdL">分类名称</td>
	<td class="tdR"><input id="title" name="title" type="text" class="W200 size2" size="30" maxlength="200"></td>
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
<!--MOD-->
<?php
}else if($submitok == "mod"){
	$row = $db->ROW(__TBL_NEWS_KIND__,"title","id>1 AND id=".$id,"name");
	if ($row){
		$title = dataIO($row['title'],'out');
	}else{exit('forbidden');}
	?>
	<table class="table W90_ Mtop20">
		<form name="ZEAIFORM" id="ZEAIFORM" method="post" enctype="multipart/form-data">
		<tr>
		<td class="tdL">分类名称</td>
		<td class="tdR"><input name="title" type="text" class="W200 size2" id="title" size="30" maxlength="100" value="<?php echo $title;?>"></td>
		</tr>
        <tr>
		<td class="tdL">&nbsp;</td>
		<td class="tdR">
			<input name="submitok" type="hidden" value="ajax_modupdate">
			<input name="id" type="hidden" value="<?php echo $id;?>">
		<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
		</td>
		</tr>
		</form>
</table>
<!--LIST-->
<?php }else{?>
<div class="navbox">
    <a href="news.php">文章管理</a>
    <a href="news_kind.php" class="ed">文章分类<?php echo '<b>'.$db->COUNT(__TBL_NEWS_KIND__," id>1").'</b>';?></a>
    <a href="news_bbs.php">文章评论</a>
    </div>
	<div class="fixedblank"></div>
	<?php
	$rt = $db->query("SELECT id,title FROM ".__TBL_NEWS_KIND__." WHERE id>1 ORDER BY px DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodataico'><i></i>暂无分类<br><a class='btn size2 HUANG' onClick=\"zeai.iframe('新增分类','".SELF."?submitok=add',500,300)\">新增分类</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="200" align="left"><button type="button" class="btn tips" onClick="zeai.iframe('新增分类','<?php echo SELF;?>?submitok=add',500,300)" ><i class="ico addico">&#xe620;</i>新增分类</button></td>
		<td align="left">&nbsp;</td>
	  </tr>
	</table>
	<table class="tablelist">
	<tr>
        <th width="60" align="center">ID</th>
        <th width="60" align="center">置顶</th>
        <th width="150">分类名称</th>
        <th width="120">文章数量</th>
        <th align="center">&nbsp;</th>
        <th width="80" class="center">修改</th>
        <th width="80" class="center">删除</th>
	</tr>
	<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$id = $rows['id'];
		$title = dataIO($rows['title'],'out');
		$wznum = $db->COUNT(__TBL_NEWS__,"kind=".$id);
	?>
	<tr>
	<td width="60" height="40" align="center"><?php echo $id;?></td>
    <td width="60" height="40" align="center"><a href="<?php echo "news_kind.php?id=".$id; ?>&submitok=ding"><img src="images/ding.gif" border="0" title="置顶" /></a></td>
	<td width="150" class="S16"><?php echo $title;?></td>
	<td width="120" class="S14"><a href="news.php?kind=<?php echo $id;?>" class="aHUI"><?php echo($wznum>0)?'<font class="Cf00 S14">'.$wznum.'</font>':0;?></a></td>
	<td align="center">&nbsp;</td>
	<td width="80" class="center"><a value="<?php echo $id;?>" class="editico" title="<?php echo $title;?>" onClick="zeai.iframe('修改【<?php echo $title;?>】分类','<?php echo $SELF;?>?submitok=mod&id=<?php echo $id;?>',500,300)"></a></td>
	<td width="80" class="center"><a value="<?php echo $id; ?>" wznum="<?php echo $wznum;?>" class="delico" title="<?php echo $title;?>"></a></td>
	</tr>
	<?php } ?>
	<?php if ($total > $pagesize){?>
	<tfoot><tr>
	<td colspan="7" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
	</tr></tfoot>
	<?php } ?>
	</table>
	<?php } ?>
<?php } ?>

<?php require_once 'bottomadm.php';?>

<script>
<?php if ($submitok == "add" || $submitok == "mod") {?>
	save.onclick = function(){
		zeai.ajax({url:'news_kind'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
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
	zeai.listEach('.delico',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var wznum = parseInt(obj.getAttribute("wznum"));
		var title = obj.getAttribute("title");
		var tips = (wznum>0)?'当前分类包含 '+wznum+' 篇文章':'';
		obj.onclick = function(){
			zeai.confirm('<font color="red">请慎重！</font><br>'+tips+'<br>真的要删除【'+title+'】么？<br>删除后不可恢复',function(){
				zeai.ajax('news_kind'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	
<?php } ?>
</script>
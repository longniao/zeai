<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('udata',$QXARR))exit(noauth());
if ($submitok == 'ajax_addupdate' || $submitok == 'ajax_modupdate'){
	if (str_len($fieldname) >50 || empty($fieldname) )exit(json_encode(array('flag'=>0,'msg'=>'变量名错误','focus'=>'fieldname')));
	if (str_len($title) >50 || empty($title) )exit(json_encode(array('flag'=>0,'msg'=>'字段名称错误','focus'=>'title')));
	$fieldname = dataIO($fieldname,'in',50);$title = dataIO($title,'in',50);$subkind = intval($subkind);$jsonstr = dataIO($jsonstr,'codein',3000);
}
switch ($submitok){
	case "ajax_addupdate":
		$fieldname=trimhtml($fieldname);$title=trimhtml($title);
		if ($db->ROW(__TBL_UDATA__,"id","fieldname='$fieldname'",'num'))exit(json_encode(array('flag'=>0,'msg'=>'变量名【'.$fieldname.'】出现重复，请更换','focus'=>'fieldname')));
		if ($db->ROW(__TBL_UDATA__,"id","title='$title'",'num'))exit(json_encode(array('flag'=>0,'msg'=>'字段名称【'.$title.'】出现重复，请重试','focus'=>'title')));
		//
		$rt = $db->query("desc ".__TBL_USER__);
		WHILE ($rows = $db->fetch_array($rt)){
			if ($fieldname == $rows[0])exit(json_encode(array('flag'=>0,'msg'=>'变量名【'.$fieldname.'】在会员主表出现重复，请更换','focus'=>'fieldname')));
		}
		$db->query("ALTER TABLE ".__TBL_USER__." ADD `".$fieldname."` VARCHAR(100) DEFAULT '';");
		//
		$db->query("INSERT INTO ".__TBL_UDATA__." (fieldname,title,subjsonstr,kind,subkind) VALUES ('$fieldname','$title','$jsonstr',3,$subkind)");
		AddLog('增加【资料属性】->详细资料字段【'.$title.'】，字段变量：'.$fieldname);
		exit(json_encode(array('flag'=>1,'msg'=>'新增成功')));
	break;
	case "ajax_modupdate":
		$fieldname=trimhtml($fieldname);$title=trimhtml($title);
		if(!ifint($id))exit(JSON_ERROR);
		if ($title != $oldtitle ){if ($db->ROW(__TBL_UDATA__,"id","title='$title'","num"))exit(json_encode(array('flag'=>0,'msg'=>'字段名称出现重复，请重试','focus'=>'title')));}
		if ($fieldname != $oldfieldname ){
			if ($db->ROW(__TBL_UDATA__,"id","fieldname='$fieldname'","num"))exit(json_encode(array('flag'=>0,'msg'=>'变量名出现重复，请重试','focus'=>'fieldname')));
			//
			$rt = $db->query("desc ".__TBL_USER__);
			WHILE ($rows = $db->fetch_array($rt)){
				if ($fieldname == $rows[0])exit(json_encode(array('flag'=>0,'msg'=>'变量名【'.$fieldname.'】在会员主表出现重复，请更换','focus'=>'fieldname')));
			}
			$db->query("ALTER TABLE ".__TBL_USER__." CHANGE `".$oldfieldname."` `".$fieldname."` VARCHAR(100) NULL;");
		}
		$db->query("UPDATE ".__TBL_UDATA__." SET fieldname='".$fieldname."',title='".$title."',subkind=".$subkind.",subjsonstr='".$jsonstr."' WHERE kind=3 AND id=".$id);
		AddLog('修改【资料属性】->详细资料字段【'.$title.'】，字段变量：'.$fieldname);
		exit(json_encode(array('flag'=>1,'msg'=>'修改成功')));
	break;
	case "ajax_delupdate":
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);
		$row = $db->NUM('zeai-udata',"fieldname,title","id=".$id,__TBL_UDATA__);
		if ($row){$fieldname= $row[0];$title= $row[1];}else{exit(JSON_ERROR);}
		$db->query("DELETE FROM ".__TBL_UDATA__." WHERE kind=3 AND id=".$id);
		@$db->query("ALTER TABLE ".__TBL_USER__." DROP `".$fieldname."`;");
		//callmsg('删除成功',SELF);
		AddLog('删除【资料属性】->详细资料字段【'.$title.'】，字段变量：'.$fieldname);
		exit(json_encode(array('flag'=>1,'msg'=>'删除成功')));
	break;
	case "ajax_pxupdate":
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);$px = intval($px);
		$db->query("UPDATE ".__TBL_UDATA__." SET px=".$px." WHERE id=".$id);
		//
		$row2 = $db->ROW(__TBL_UDATA__,"title,fieldname","id=".$id,'num');$title= $row2[0];$fieldname= $row2[1];
		AddLog('修改排序【资料属性】->详细资料字段【'.$title.'】，字段变量：'.$fieldname.'，新排序值：'.$px);
		//
		exit(json_encode(array('flag'=>1)));
	break;
	case "ajax_flagupdate":
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);$flag = ($flag == 1)?$flag:0;
		$db->query("UPDATE ".__TBL_UDATA__." SET flag=".$flag." WHERE id=".$id);
		//
		$row2 = $db->ROW(__TBL_UDATA__,"title,fieldname","id=".$id,'num');$title= $row2[0];$fieldname= $row2[1];
		$flag_str=($flag==1)?'开启':'隐藏';
		AddLog('修改显示状态【资料属性】->详细资料字段【'.$title.'】，字段变量：'.$fieldname.'，新状态：'.$flag_str);
		//
		exit(json_encode(array('flag'=>1)));
	break;
}
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
.jsonlistbox{width:500px;overflow:hidden;display:inline-block;float:left}
</style>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<!--ADD-->
<?php if ($submitok == "add") {?>
<table class="table W90_ Mtop10 Mbottom50">
  <form action="<?php echo SELF;?>" name="ZEAIFORM" id="ZEAIFORM" method="post">
    <tr>
      <td class="tdL">变量名</td>
      <td class="tdR"><input name="fieldname" type="text" class="W150 size2" id="fieldname" size="30" maxlength="20"><span class="tips">必须英文半角字符</span></td>
    </tr>
    <tr>
      <td class="tdL">字段名称</td>
      <td class="tdR"><input name="title" type="text" class="W150 size2" id="title" size="30" maxlength="20"><span class="tips">中文名称，显示到前台</span></td>
    </tr>
  <tr>
      <td class="tdL" style="border-top:#E1E6EB 2px dotted">子选项显示类型</td>
      <td class="tdR" style="border-top:#E1E6EB 2px dotted">
<input type="radio" name="subkind" id="subkind1" class="radioskin" value="1"><label for="subkind1" class="radioskin-label"><i class="i1"></i><b class="W50 S14">文本</b></label>
<input type="radio" name="subkind" id="subkind2" class="radioskin" value="2" checked><label for="subkind2" class="radioskin-label"><i class="i1"></i><b class="W50 S14">单选</b></label>
<input type="radio" name="subkind" id="subkind3" class="radioskin" value="3"><label for="subkind3" class="radioskin-label"><i class="i1"></i><b class="W150 S14">多选 <font class="S12 C999">(会员最多选<script>document.write(checkboxMaxNum);</script>项)</font></b></label>
    </td>
    </tr>
    <tr id="subbox">
      <td class="tdL">子选项</td>
      <td class="tdR S12"><div id="tmp"></div><div><button id="add" type="button" class="btn size1 LAN2">增加</button></div></td>
    </tr>
	<tr>
      <td class="tdL"style="border-top:#E1E6EB 2px dotted">&nbsp;</td>
      <td class="tdR"style="border-top:#E1E6EB 2px dotted">
			<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
			<input name="submitok" type="hidden" value="addupdate"><!--新增/修改后必须重新生成缓存才能生效-->
		</td>
    </tr>
  </form>
</table>
<!--MOD-->
<?php
}else if($submitok == "mod"){
	$row = $db->NAME("zeai-udata","id,fieldname,title,subjsonstr,subkind","id=".$id,__TBL_UDATA__);
	if ($row){
		$id = $row['id'];
		$title = dataIO($row['title'],'out');
		$fieldname = dataIO($row['fieldname'],'out');
		$jsonstr = dataIO($row['subjsonstr'],'out');
		$subkind = $row['subkind'];
	}else{exit('forbidden');}
?>
<table class="table W90_ Mtop10 Mbottom50">
	<form action="<?php echo SELF;?>" name="ZEAIFORM" id="ZEAIFORM" method="post">
    <?php if ($fieldname == 'companykind' || $fieldname == 'drink' || $fieldname == 'smoking'){?>
        <input type="hidden" name="fieldname" id="fieldname" value="<?php echo $fieldname;?>">
        <input type="hidden" name="title" id="title" value="<?php echo $title;?>">
        <tr>
        <td class="tdL">变量名</td>
        <td class="tdR"><?php echo $fieldname;?></td>
        </tr>
        <tr>
        <td class="tdL">字段名称</td>
        <td class="tdR"><?php echo $title;?></td>
        </tr>
    <?php }else{ ?>
        <tr>
        <td class="tdL">变量名</td>
        <td class="tdR"><input name="fieldname" type="text" class="W150 size2" id="fieldname" size="30" maxlength="20" value="<?php echo $fieldname;?>" <?php echo ($iflock)?' readonly':'';?>><span class="tips">必须英文半角字符</span></td>
        </tr>
        <tr>
        <td class="tdL">字段名称</td>
        <td class="tdR"><input name="title" type="text" class="W150 size2" id="title" size="30" maxlength="20" value="<?php echo $title;?>"><span class="tips">中文名称，显示到前台</span></td>
        </tr>
    <?php }?>
      <tr>
          <td class="tdL" style="border-top:#E1E6EB 2px dotted">子选项显示类型</td>
          <td class="tdR" style="border-top:#E1E6EB 2px dotted">
    <input type="radio" name="subkind" id="subkind1" class="radioskin" value="1"<?php echo ($subkind == 1)?' checked':'';?>><label for="subkind1" class="radioskin-label"><i class="i1"></i><b class="W50 S14">文本</b></label>
    <input type="radio" name="subkind" id="subkind2" class="radioskin" value="2"<?php echo ($subkind == 2)?' checked':'';?>><label for="subkind2" class="radioskin-label"><i class="i1"></i><b class="W50 S14">单选</b></label>
    <input type="radio" name="subkind" id="subkind3" class="radioskin" value="3"<?php echo ($subkind == 3)?' checked':'';?>><label for="subkind3" class="radioskin-label"><i class="i1"></i><b class="W150 S14">多选 <font class="S12 C999">(会员最多选<script>document.write(checkboxMaxNum);</script>项)</font></b></label>
        </td>
        </tr>

	<tr id="subbox"<?php if ($subkind == 1)echo ' style="display:none;"';?>>
	<td class="tdL">子选项</td>
	<td class="tdR S12">
	<div id="tmp">
	<?php
	$a = json_decode($jsonstr);
	for($j=0;$j<@count($a);$j++) {
		$id2    = $a[$j]->i;
		$value2 = $a[$j]->v;
	?>
	<div class="tr">ID <input type="text" class="W50 size1" maxlength="5" placeholder="数字" value="<?php echo $id2;?>">名称 <input type="text" class="W150 size1" maxlength="50" value="<?php echo $value2;?>"><button type="button" class="btn size1">删除</button></div>
	<?php }?>
	</div>
	<div><button id="add" type="button" class="btn size1 LAN2">增加</button></div>
	</td>
	</tr>
	<input id="oldfieldname" type="hidden" value="<?php echo $fieldname;?>">
	<input id="oldtitle" type="hidden" value="<?php echo $title;?>">
	</form>
</table>
<br><br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">确认并保存</button></div>

<!--LIST-->
<?php }else{?>

<div class="navbox">
<a href="udata1.php">内置字段(不可更改)</a>
<a href="udata2.php">基本字段(改子选项)</a>
<a href="udata3.php" class="ed">详细资料字段</a>
<a href="udata_mate.php">择偶要求DIY</a>

<div class="clear"></div></div><div class="fixedblank"></div>
<?php
$rt = $db->query("SELECT id,fieldname,title,subjsonstr,flag,px,subkind FROM ".__TBL_UDATA__." WHERE kind=3 ORDER BY px DESC,id");
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	if ($submitok !== "add")echo "<div class='nodatatips'>... 暂无内容 ...　　　<a class='btn size2 HUANG' onClick=\"zeai.iframe('新增会员资料字段','".SELF."?submitok=add',550,500)\">新增字段</a></div>";
} else {    
	$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
?>
<table class="table0 W98_ Mbottom10 Mtop10">
  <tr>
    <td width="12%" align="left"><button type="button" class="btn" onClick="zeai.iframe('新增会员资料字段','<?php echo SELF;?>?submitok=add',550,500)"><i class="ico addico">&#xe620;</i> 新增扩展字段</button></td>
    <td  align="right"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">新增或修改后请及时更新【高速缓存】后才能生效<img src="images/d2.gif"></font></td>
    <td width="140" align="right"><button type="button" class="btn" id="cache"><i class="ico">&#xe642;</i> 更新高速缓存</button></td>
  </tr>
</table>

<table class="tablelist">
<tr>
	<th width="50">ID</th>
	<th width="120">排序(数字值大靠前)</th>
<th width="100">变量</th>
	<th width="80">类型</th>
<th width="120" align="right" class="Pright10">字段名称</th>
	<th>子选项</th>
<th width="100" align="center">编辑子选项</th>
	<th width="100" class="center">状态</th>
<th width="60" align="center" class="center">删除</th>
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
?>
<tr>
<td width="50" height="40"><?php echo $id;?></td>
<td width="120">
<input id="px<?php echo $id;?>" type="text" class="W50 size1" maxlength="4" value="<?php echo $px;?>">
<button value="<?php echo $id;?>" type="button" class="pxbtn btn size1 HUANG">修改</button>
</td>
<td width="100"><?php echo $fieldname;?></td>
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
<td width="120" align="right" class="Pright10 S14"><?php echo $title;?></td>
<td class="Cfff"><div class="jsonlistbox"><?php
$a = json_decode($jsonstr);
for($j=0;$j<@count($a);$j++) {
	echo '<span class="jsonlist">'.$a[$j]->i.'：';
	echo $a[$j]->v;
	echo '</span>';
}
?></div><a class="FL aHUI" style="margin-top:26px;display:none">more..</a></td>
<td width="100" align="center"><?php if ($subkind != 1){?><a value="<?php echo $id;?>" class="editico" onClick="zeai.iframe('修改【<?php echo $title;?>】字段','<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>',600,500)"></a><?php }?></td>
<td width="100" class="center"><input type="checkbox" id="flag<?php echo $id;?>" class="switch" value="<?php echo $flag;?>"<?php echo ($flag == 1)?' checked':'';?>><label value="<?php echo $id;?>" for="flag<?php echo $id;?>" class="switch-label"><i></i><b>启用</b><b>隐藏</b></label></td>
<td width="60" align="center" class="center">
  <?php if ($fieldname != 'companykind' && $fieldname != 'drink' && $fieldname != 'smoking'){?>
  <a value=<?php echo $id; ?> class="delico"></a>
  <?php }?></td>
</tr>
<?php } ?>
<?php if ($total > $pagesize){?>
<tfoot><tr>
<td colspan="9" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
</tr></tfoot>
<?php } ?>
</table>
<?php }} ?>

<script>
<?php if ($submitok == "add") {?>
	var n = 0;
<?php }elseif($submitok == "mod"){ ?>
	var n  =parseInt(<?php echo $j;?>);
	var idV=parseInt(<?php echo $id;?>);
	var oldfieldnameV = o('oldfieldname').value;
	var oldtitleV = o('oldtitle').value;
	zeai.listEach('.tr',function(obj){
		obj.children[2].onclick=function(){this.parentNode.parentNode.removeChild(this.parentNode);}
	});
<?php }?>




<?php if ($submitok == "add" || $submitok == "mod") {?>
	var submitok = 'ajax_<?php echo $submitok;?>update';
	
	subkind1.onclick = function(){subbox.hide();}
	subkind2.onclick = function(){subbox.show('');}
	subkind3.onclick = function(){subbox.show('');}

	add.onclick = function(){
		n++;
		var text1 = document.createTextNode('ID '),text2 = document.createTextNode('名称 ');
		var IDH = zeai.addtag('input');IDH.value = n;IDH.className = 'W50 size1';IDH.maxLength = 4;
		var Namee = zeai.addtag('input');Namee.className = 'W150 size1';Namee.maxLength = 50;
		var Btn = zeai.addtag('button');Btn.className = 'btn size1';Btn.html('删除');Btn.onclick=function(){this.parentNode.parentNode.removeChild(this.parentNode);}
		var tr = zeai.addtag('div');tr.className = 'tr';
		tr.appendChild(text1);tr.appendChild(IDH);tr.appendChild(text2);tr.appendChild(Namee);tr.appendChild(Btn);tmp.appendChild(tr);
	}
	save.onclick = function(){
		var fieldnameV = o('fieldname').value;
		var titleV   = o('title').value;
		var subkindV;
		if(zeai.empty(fieldnameV) || zeai.str_len(fieldnameV)>50){
			parent.zeai.msg('请输入变量名(1-50字节)',fieldname);
			return false;
		}
		if(zeai.empty(titleV) || zeai.str_len(titleV)>50){
			parent.zeai.msg('请输入字段名称(1-50字节)',title);
			return false;
		}
		zeai.listEach(document.getElementsByName('subkind'),function(obj){
			if (obj.checked){subkindV=obj.value;}
		});
		//子选项
		var postjson = {"submitok":submitok,"fieldname":fieldnameV,"title":titleV,"subkind":subkindV};
		if (submitok == 'ajax_modupdate')Object.assign(postjson,{"id":idV,"oldfieldname":oldfieldnameV,"oldtitle":oldtitleV});
		if (subkind2.checked || subkind3.checked){
			var idARR = [],jsonarr=[];
			zeai.listEach('.tr',function(obj){
				var subid   = obj.children[0];
				var subname = obj.children[1];
				if (!zeai.ifint(subid.value)){parent.zeai.msg('请输入ID数字',subid);zeaiifbreak=true;return false;}
				if (zeai.empty(subname.value)){parent.zeai.msg('请输入子项名称',subname);zeaiifbreak=true;return false;}
				idARR.push(subid.value);
				jsonarr.push({"i":subid.value,"v":subname.value});
			});
			if (!zeaiifbreak){
				var repeat = idARR.ifRepeat();
				if (repeat){
					parent.zeai.msg('ID数字有重复“'+repeat+'”');
					return false;
				}
			}else{return false;}
			if (idARR.length <= 0 && !zeaiifbreak){
				parent.zeai.msg('请点一下【增加】小按钮，谢谢');
				return false;
			}
			var jsonstr = JSON.stringify(jsonarr);
			Object.assign(postjson,{"jsonstr":jsonstr});
		}
		//POST
		zeai.ajax({url:'udata3.php',data:postjson},function(e){var rs=zeai.jsoneval(e);
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
	zeai.listEach('.pxbtn',function(obj){
		var id = parseInt(obj.value);
		var pxobj = o('px'+id);
		obj.onclick = function(){
			var pxV = pxobj.value;
			zeai.ajax('udata3'+zeai.ajxext+'submitok=ajax_pxupdate&id='+id+'&px='+pxV,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		}
	});
	zeai.listEach('.switch-label',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var chkobj = o('flag'+id);
		obj.onclick = function(){
			var chkV = chkobj.checked;
			var flag = (chkobj.checked)?0:1;
			zeai.ajax('udata3'+zeai.ajxext+'submitok=ajax_flagupdate&id='+id+'&flag='+flag);
		}
	});
	zeai.listEach('.delico',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		obj.onclick = function(){
			zeai.confirm('真的要删除么？（删除后不可恢复，建议先隐藏，需要时再开）',function(){
				zeai.ajax('udata3'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	zeai.listEach('.jsonlistbox',function(obj){
		var objH = obj.offsetHeight;
		if (objH > 52){
			obj.style.height = '52px';
			var a = obj.nextElementSibling;
			a.show();
			a.onclick = function(){obj.style.height = 'auto';a.hide();}
			obj.onclick = function(){obj.style.height = 'auto';a.hide();}
		}
	});
	cache.onclick = function(){
		zeai.msg('正在更新中',{time:20});
		var postjson = {submitok:'cache_udata',uu:'<?php echo $session_uid;?>',pp:'<?php echo $session_pwd;?>'};
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:postjson},function(e){var rs=zeai.jsoneval(e);
			zeai.msg('',{flag:'hide'});zeai.alert(rs.msg);
		});
	}
<?php } ?>
</script>
<?php require_once 'bottomadm.php';?>
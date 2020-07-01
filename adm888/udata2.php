<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('udata',$QXARR))exit(noauth());
if ($submitok == 'ajax_modupdate'){
	if (str_len($fieldname) >50 || empty($fieldname) )json_exit(array('flag'=>0,'msg'=>'变量名错误','focus'=>'fieldname'));
	if (str_len($title) >50 || empty($title) )json_exit(array('flag'=>0,'msg'=>'字段名称错误','focus'=>'title'));
	$fieldname = dataIO($fieldname,'in',50);$title = dataIO($title,'in',50);$jsonstr = dataIO($jsonstr,'codein',3000);
}
switch ($submitok){
	case "ajax_modupdate":
		$fieldname=trimhtml($fieldname);$title=trimhtml($title);
		if(!ifint($id))json_exit(array('flag'=>0));
		if ($fieldname != $oldfieldname ){if ($db->ROW(__TBL_UDATA__,"id","fieldname='$fieldname'"))json_exit(array('flag'=>0,'msg'=>'变量名出现重复，请重试','focus'=>'fieldname'));}
		if ($title != $oldtitle ){if ($db->ROW(__TBL_UDATA__,"id","title='$title'"))json_exit(array('flag'=>0,'msg'=>'字段名称出现重复，请重试','focus'=>'title'));}
		$db->query("UPDATE ".__TBL_UDATA__." SET fieldname='".$fieldname."',title='".$title."',subjsonstr='".$jsonstr."' WHERE kind=2 AND id=".$id);
		AddLog('修改【资料属性】->基本字段【'.$title.'】，字段ID：'.$id);
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case "ajax_pxupdate":
		/*
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);$px = intval($px);
		$db->query("UPDATE ".__TBL_UDATA__." SET px=".$px." WHERE id=".$id);
		json_exit(array('flag'=>1));
		*/
	break;
	case "ajax_flagupdate":
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);$flag = ($flag == 1)?$flag:0;
		$db->query("UPDATE ".__TBL_UDATA__." SET flag=".$flag." WHERE id=".$id);
		AddLog('修改【资料属性】->基本字段状态，字段ID：'.$id);
		json_exit(array('flag'=>1));
	break;
}
$kind = (ifint($kind,'1-4','1'))?$kind:1;
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
.jsonlistbox{width:700px;overflow:hidden;display:inline-block;float:left}
</style>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<!--MOD-->
<?php
if($submitok == "mod"){
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
	<tr>
	<td class="tdL">变量名</td>
	<td class="tdR"><?php echo $fieldname;?><input name="fieldname" type="hidden" class="W150 size2" id="fieldname" size="30" maxlength="20" value="<?php echo $fieldname;?>"></td>
	</tr>
	<tr>
	<td class="tdL">字段名称</td>
	<td class="tdR"><?php echo $title;?><input name="title" type="hidden" class="W150 size2" id="title" size="30" maxlength="20" disabled value="<?php echo $title;?>"></td>
	</tr>
    <?php if ($subkind == 2 || $subkind == 3){?>
	<tr>
        <td class="tdL">子选项</td>
        <td class="tdR S12">
        <div id="tmp">
        <?php
        $a = json_decode($jsonstr);
        if (is_array($a) && count($a)>0){
            for($j=0;$j<count($a);$j++) {
                $id2    = $a[$j]->i;
                $value2 = $a[$j]->v;
            ?>
            <div class="tr">ID <input type="text" class="W50 size1" maxlength="5" placeholder="数字" value="<?php echo $id2;?>">名称 <input type="text" class="W150 size1" maxlength="50" value="<?php echo $value2;?>"><button type="button" class="btn size1">删除</button></div>
            <?php }
        }
        ?>
        </div>
        <div><button id="add" type="button" class="btn size1 LAN2">增加</button></div>
        </td>
	</tr>
    <?php }elseif($subkind == 4){
		$qj = json_decode($jsonstr,true);
	?>
	<tr>
        <td class="tdL">区间范围</td>
        <td class="tdR">
			<input name="qj1" type="text" class="W50 size2" id="qj1" maxlength="7" value="<?php echo $qj['start'];?>"> ～ <input name="qj2" type="text" class="W50 size2" id="qj2" maxlength="7" value="<?php echo $qj['end'];?>">　<input name="dw" type="text" class="W50 size2" id="dw" maxlength="8" value="<?php echo $qj['dw'];?>" placeholder="单位">
        </td>
	</tr>
	<?php }?>    
    
	<input id="oldfieldname" type="hidden" value="<?php echo $fieldname;?>">
	<input id="oldtitle" type="hidden" value="<?php echo $title;?>">
	</form>
</table>
<br><br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">确认并保存</button></div>

<!--MOD END-->
<!--LIST-->
<?php }else{?>
<div class="navbox">
<a href="udata1.php">内置字段(不可更改)</a>
<a href="udata2.php" class="ed">基本字段(改子选项)</a>
<a href="udata3.php">详细资料字段</a>
<div class="clear"></div></div><div class="fixedblank"></div>
<?php
$rt = $db->query("SELECT id,fieldname,title,subjsonstr,flag,px,subkind,bz FROM ".__TBL_UDATA__." WHERE kind=2 ORDER BY px DESC,id");
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	if ($submitok !== "add")echo "<div class='nodatatips'>... 暂无内容 ...</div>";
} else {    
	$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
?>

<table class="table0 W98_ Mbottom10 Mtop10">
<tr>
<td width="50%" align="left">
<img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">修改后请及时更新【高速缓存】后才能生效</font>　<button type="button" class="btn" id="cache"><i class="ico">&#xe642;</i> 更新高速缓存</button>
</td>
<td align="right"></td>
</tr>
</table>

<table class="tablelist Mtop20">
<tr>
	<th width="50">ID</th>
	<th width="80">变量</th>
	<th width="50">类型</th>
<th width="120" align="right" class="Pright10">字段名称</th>
	<th>子选项</th>
<th width="80" class="center">编辑子选项</th>
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
<td width="50">
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
<td>
<?php
$a = json_decode($jsonstr,true);
if ($subkind == 4){
	echo '<font class="S14">'.$a['start'].$a['dw'].' ～ '.$a['end'].$a['dw'].'</font>';
}else{
	?>
    <div class="jsonlistbox Cfff">
    <?php
    for($j=0;$j<@count($a);$j++) {
        echo '<span class="jsonlist">'.$a[$j]['i'].'：';
        echo $a[$j]['v'];
        echo '</span>';
    }?>
    </div><a class="FL aHUI" style="margin-top:26px;display:none">more..</a>
<?php }?>
</td>
<td width="80" class="center">
<?php if ($subkind == 2 || $subkind == 3 || $subkind == 4){
	$iframesize = ($subkind == 4)?'600,300':'600,500';
?>
<a value="<?php echo $id;?>" class="editico" onClick="zeai.iframe('修改【<?php echo $title;?>】字段','<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>',<?php echo $iframesize;?>)"></a>
<?php }?>
</td>
<?php if (($t == 2)){?><?php }?>
</tr>
<?php } ?>
<?php if ($total > $pagesize){?>
<tfoot><tr>
<td colspan="6"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
</tr></tfoot>
<?php } ?>
</table>
<?php }} ?>
<!--LIST END-->

<script>
<?php if ($submitok == "mod") {?>
	var idV = parseInt(<?php echo $id;?>);
	var oldfieldnameV = o('oldfieldname').value;
	var oldtitleV = o('oldtitle').value;
	var submitok  = 'ajax_<?php echo $submitok;?>update';
	var subkind   = <?php echo $subkind;?>
	
	if (subkind == 2 || subkind == 3){//单选和多选
		var n  =parseInt(<?php echo $j;?>);
		zeai.listEach('.tr',function(obj){
			obj.children[2].onclick=function(){this.parentNode.parentNode.removeChild(this.parentNode);}
		});
		add.onclick = function(){
			n++;
			var text1 = document.createTextNode('ID '),text2 = document.createTextNode('名称 ');
			var IDH = zeai.addtag('input');IDH.value = n;IDH.className = 'W50 size1';IDH.maxLength = 4;
			var Namee = zeai.addtag('input');Namee.className = 'W150 size1';Namee.maxLength = 50;
			var Btn = zeai.addtag('button');Btn.className = 'btn size1';Btn.html('删除');Btn.onclick=function(){this.parentNode.parentNode.removeChild(this.parentNode);}
			var tr = zeai.addtag('div');tr.className = 'tr';
			tr.appendChild(text1);tr.appendChild(IDH);tr.appendChild(text2);tr.appendChild(Namee);tr.appendChild(Btn);tmp.appendChild(tr);
		}
	}
	save.onclick = function(){
		var fieldnameV = o('fieldname').value;
		var titleV   = o('title').value;
		if(zeai.empty(fieldnameV) || zeai.str_len(fieldnameV)>50){
			parent.zeai.msg('请输入变量名(1-50字节)',fieldname);
			return false;
		}
		if(zeai.empty(titleV) || zeai.str_len(titleV)>50){
			parent.zeai.msg('请输入字段名称(1-50字节)',title);
			return false;
		}
		if (subkind == 2 || subkind == 3){//单选和多选
			//子选项
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
			//子选项结束
			var jsonstr = JSON.stringify(jsonarr);
		}
		
		if (subkind == 4){//区间
			if(!zeai.ifint(qj1.value)){
				parent.zeai.msg('请输入起始区间范围(正整数)',qj1);
				return false;
			}
			if(!zeai.ifint(qj2.value)){
				parent.zeai.msg('请输入结束区间范围(正整数)',qj2);
				return false;
			}
			if(zeai.empty(dw.value)){
				parent.zeai.msg('请输入单位名称',dw);
				return false;
			}
			var jsonstr = JSON.stringify({"start":qj1.value,"end":qj2.value,"dw":dw.value});
		}
		var postjson = {"submitok":submitok,"fieldname":fieldnameV,"title":titleV,"jsonstr":jsonstr};
		if (submitok == 'ajax_modupdate')Object.assign(postjson,{"id":idV,"oldfieldname":oldfieldnameV,"oldtitle":oldtitleV});
		//POST
		zeai.ajax({url:'udata2.php',data:postjson},function(e){var rs=zeai.jsoneval(e);
			if (rs.flag == 1){
				parent.location.reload(true); 
			}else if(rs.flag == 0){
				parent.zeai.msg(rs.msg,o(rs.focus));
				return false;
			}		
		});
		return false;
	}
<?php }else{ ?>
	/*
	zeai.listEach('.pxbtn',function(obj){
		var id = parseInt(obj.value);
		var pxobj = o('px'+id);
		obj.onclick = function(){
			var pxV = pxobj.value;
			zeai.ajax('udata2'+zeai.ajxext+'submitok=ajax_pxupdate&id='+id+'&px='+pxV,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}		
			});
		}
	});
	*/
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
		var postjson = {submitok:'cache_udata',uu:'<?php echo $_SESSION['admuid'];?>',pp:'<?php echo $_SESSION['admpwd'];?>'};
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:postjson},function(e){var rs=zeai.jsoneval(e);
			zeai.msg('',{flag:'hide'});zeai.alert(rs.msg);
		});
	}
<?php } ?>
</script>
<?php require_once 'bottomadm.php';?>
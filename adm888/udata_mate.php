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
		exit(json_encode(array('flag'=>1,'msg'=>'修改成功')));
	break;
	case "ajax_delupdate":
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);
		$row = $db->NUM('zeai-udata',"fieldname","id=".$id,__TBL_UDATA__);
		if ($row){$fieldname= $row[0];}else{exit(JSON_ERROR);}
		$db->query("DELETE FROM ".__TBL_UDATA__." WHERE kind=3 AND id=".$id);
		@$db->query("ALTER TABLE ".__TBL_USER__." DROP `".$fieldname."`;");
		//callmsg('删除成功',SELF);
		exit(json_encode(array('flag'=>1,'msg'=>'删除成功')));
	break;
	case "ajax_pxupdate":
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);$px = intval($px);
		$db->query("UPDATE ".__TBL_UDATA__." SET px=".$px." WHERE id=".$id);
		exit(json_encode(array('flag'=>1)));
	break;
	case "ajax_flagupdate":
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);$flag = ($flag == 1)?$flag:0;
		$db->query("UPDATE ".__TBL_UDATA__." SET flag=".$flag." WHERE id=".$id);
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
<link href="css/main.css" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js"></script>
<body>
<div class="navbox">
<a href="udata1.php">内置字段(不可更改)</a>
<a href="udata2.php">基本字段(改子选项)</a>
<a href="udata3.php">详细资料字段</a>
<a href="udata_mate.php" class="ed">择偶要求DIY</a>

<div class="clear"></div></div><div class="fixedblank"></div>
<form name="ZEAIFORM" id="ZEAIFORM" method="post">
<style>.table.cols1 .tdL{width:160px}</style>
<table class="table size2 cols1" style="width:1230px;margin:10px 0 0 20px">
	<tr><th colspan="4" align="left" style="border:0">择偶要求DIY</th></tr>

    <tr>
      <td class="tdL">择偶要求字段选项</td>
      <td class="tdR">
<?php 
$mate_diy    = explode(',',$_ZEAI['mate_diy']);
$mate_diy_px = json_decode($_ZEAI['mate_diy_px'],true);
?>
<div>
    <style>
    .stepbox .stepli li{width:300px;height:60px;float:left;padding:5px 10px;margin:10px;border:#ddd 1px solid;cursor:move;background-color:#f8f8f8;border-radius:3px}
    .stepbox .steppx{width:30px;display:inline-block;font-size:12px;color:#999}
	 .stepbox .rach{padding-left:30px}
    </style>
    <dd class="stepbox" id="stepbox">
        <div class="stepli">
            <?php
            $n=1;
			$disable_ext=explode(',','age,heigh,weigh,edu,pay,areaid,areaid2');
            if (count($mate_diy_px) >= 1 && is_array($mate_diy_px)){
                foreach ($mate_diy_px as $k=>$V) {
                    $V      = $mate_diy_px[$k]['id'];
                    $ifmate = $mate_diy_px[$k]['ifmate'];
                    $ext    = $mate_diy_px[$k]['ext'];
                    switch ($ext) {
                        case 'radio':$kind_str     = '单选';break;
                        case 'checkbox':$kind_str  = '多选';break;
                        case 'range':$kind_str     = '范围';break;
                        case 'radiorange':$kind_str = '单选范围';break;
                        case 'area':$kind_str      = '地区联动';break;
                    }
                    ?>
                    <li>
                        <font class="steppx"><?php echo $n;?>．</font><input type="checkbox" name="mate_diy[]" id="id_<?php echo $V;?>" class="checkskin mate_diy" value="<?php echo $V;?>"<?php echo (in_array($V,$mate_diy))?' checked':'';?>><label for="id_<?php echo $V;?>" class="checkskin-label"><i class="i1"></i><b class="W150 S16">【<?php echo mate_diy_par($V);?>】</b></label>
                        <input type="checkbox" name="ifmate_<?php echo $V;?>" id="ifmate_<?php echo $V;?>" class="checkskin " value="1"<?php echo ($ifmate == 1)?' checked':'';?>><label for="ifmate_<?php echo $V;?>" class="checkskin-label"><i class="i1"></i><b class="W50 S12 ">参与匹配</b></label>
						<div class="rach">
                        <?php if (in_array($V,$disable_ext)){?>
                        	<font class="S12 C999"><?php echo $kind_str;?></font></b>
                            <input id="ext_<?php echo $V;?>" type="hidden" value="<?php echo $ext;?>">
                        <?php }else{ ?>
                            <input type="radio" name="ext_<?php echo $V;?>" id="ext_<?php echo $V;?>_radio" class="radioskin" value="radio"<?php echo ($ext == 'radio')?' checked':'';?>><label for="ext_<?php echo $V;?>_radio" class="radioskin-label"><i class="i1"></i><b class="W30 S12">单选</b></label>
                            <input type="radio" name="ext_<?php echo $V;?>" id="ext_<?php echo $V;?>_checkbox" class="radioskin" value="checkbox"<?php echo ($ext == 'checkbox')?' checked':'';?>><label for="ext_<?php echo $V;?>_checkbox" class="radioskin-label"><i class="i1"></i><b class="W30 S12">多选</b></label>　 
                        <?php }?>
                        </div>
                    </li>
                    <?php
                    $n++;
                }
            }
            ?>
        </div>
        <div class="clear"></div>
    </dd>
    <font class="S12 C999">可以按住不放拖动项目调整前后顺序（最终调用排序：从左到右从上到下），选中请打勾，选中将出现在个人资料或注册等页面，否则不显示</font><br>
    <font class="S12 C999">前面大项是总开关，后面【参与匹配】是系统自动实际参与匹配的字段，如首页会员【匹配】</font>
</div>
</td></tr></table>
<input name="submitok" type="hidden" value="cache_mate_diy">
<input name="uu" type="hidden" value="<?php echo $session_uid;?>">
<input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
<input name="mate_diy_px" id="mate_diy_px" type="hidden" value="<?php echo $mate_diy_px;?>">
</form>
<br><br><br><br><br><br><br>
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
<script src="js/Sortable1.6.1.js"></script>
<script>
function drag_init(){
	(function (){
		[].forEach.call(stepbox.getElementsByClassName('stepli'), function (el){
			Sortable.create(el, {
				group: 'zeai_reg',
				animation:150
			});
		});
	})();
}
drag_init();
save.onclick = function(){
	var DATAPX=[],id,ifmate,ext1,ext2,ext,li;
	zeai.listEach('.mate_diy',function(obj){
		id=obj.value;
		if(!zeai.empty(o('ext_'+id))){
			ext=o('ext_'+id).value;
		}else{
			ext1=(o('ext_'+id+'_radio').checked)?o('ext_'+id+'_radio').value:'';
			ext2=(o('ext_'+id+'_checkbox').checked)?o('ext_'+id+'_checkbox').value:'';
			ext=(!zeai.empty(ext1))?ext1:ext2;
		}
		ifmate=(o('ifmate_'+id).checked)?1:0;
		li= '{"id":"'+id+'","ifmate":"'+ifmate+'","ext":"'+ext+'"}';
		DATAPX.push(li);
	});
	mate_diy_px.value='['+DATAPX.join(",")+']';
	zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.alert(rs.msg);
	});
}
</script>
<?php require_once 'bottomadm.php';?>
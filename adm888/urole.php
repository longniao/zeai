<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('urole',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_vip.php';
$switch = json_decode($_ZEAI['switch'],true);
//检查性别库
$ifsex = true;
$row = $db->ROW(__TBL_UDATA__,"subjsonstr","fieldname='sex'","num");
if ($row){$sex_ARR = json_decode($row[0],true);if(!is_array($sex_ARR) || count($sex_ARR)==0)$ifsex = false;}else{$ifsex = false;}
if (!$ifsex)alert_adm('性别出错请联系开发者QQ797311','close');
//检查性别库 结束
if ($submitok == 'ajax_addupdate' || $submitok == 'ajax_modupdate'){
	if (empty($title) )json_exit(array('flag'=>0,'msg'=>'请输入会员组名称','focus'=>'title'));
	if (str_len($title) >20)json_exit(array('flag'=>0,'msg'=>'亲，会员组名称【'.$title.'】这么长有意义么？ 请不要超过20字节','focus'=>'title'));
	$title = dataIO($title,'in',50);
	if (!ifint($grade) )json_exit(array('flag'=>0,'msg'=>'请输入权重等级 1~10','focus'=>'grade'));
	if ($grade > 10)$grade = 10;
	if ($grade == 0)$grade = 1;
	if (!ifint($if2) )json_exit(array('flag'=>0,'msg'=>'请选择VIP有效期限1个月,3个月。。。','focus'=>'if2_1'));
	if ($if2 > 999)$if2 = 999;
	$sj_rmb1=abs(intval($sj_rmb1));
	$sj_rmb2=abs(intval($sj_rmb2));
	if (!ifint($sj_rmb1) && $grade>1)json_exit(array('flag'=>0,'msg'=>'请输入服务价格（正整数）【男】','focus'=>'sj_rmb'));
	if (!ifint($sj_rmb2) && $grade>1)json_exit(array('flag'=>0,'msg'=>'请输入服务价格（正整数）【女】','focus'=>'sj_rmb'));
	if ($switch_Smode!=1 && $switch_Smode!=2)json_exit(array('flag'=>0,'msg'=>'请选择会员服务模式【线上服务】还是【线下服务】','focus'=>'grade'));
}
switch ($submitok){
	case "ajax_addupdate":
		if ($db->ROW(__TBL_ROLE__,"id","kind=1 AND title='$title'"))json_exit(array('flag'=>0,'msg'=>'会员组名称【'.$title.'】出现重复，请重试','focus'=>'title'));
		$grade = abs(intval($grade));
		if ($grade>10 || $grade==0)json_exit(array('flag'=>0,'msg'=>'请重输入权重1~10','focus'=>'grade'));
		if ($db->ROW(__TBL_ROLE__,"id","kind=1 AND grade=".$grade))json_exit(array('flag'=>0,'msg'=>'权重【'.$grade.'】出现重复，请重输','focus'=>'grade'));
		//
		foreach ($sex_ARR as $k=>$v) {
			$file = $_FILES["pic".$k];
			if (empty($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'请给当前会员组【'.$v['v'].'】上传一个牛逼点的图标吧'));
			if (getpicextname($file['tmp_name']) != 'png' || !ifpic($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'当前会员组【'.$v['v'].'】必须是png透明格式图片'));
			@up_send($file,'p/img/grade'.$v['i'].$grade.'.png',0,$_UP['upMsize']);
		}
		//
		$db->query("INSERT INTO ".__TBL_ROLE__." (title,grade,if2,kind) VALUES ('$title',$grade,$if2,1)");
		AddLog('新增会员组【'.$title.'，权重：'.$grade.'，时长：'.get_if2_title($if2).'】');
		json_exit(array('flag'=>1,'msg'=>'新增成功'));
	break;
	case "ajax_modupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$grade = abs(intval($grade));
		if ($grade != $oldgrade){
			if ($db->ROW(__TBL_ROLE__,"id","kind=1 AND grade=".$grade))json_exit(array('flag'=>0,'msg'=>'权重【'.$grade.'】出现重复，请重输','focus'=>'grade'));
		}
		//
		foreach ($sex_ARR as $k=>$v) {
			$file = $_FILES["pic".$k];
			if (!up_check_file_exists('p/img/grade'.$v['i'].$oldgrade.'.png') && empty($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'请给当前会员组【'.$v['v'].'】上传一个牛逼点的图标吧'));//老图不存在，新的为空
			if (!empty($file['tmp_name'])){
				if (getpicextname($file['tmp_name']) != 'png' || !ifpic($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'当前会员组【'.$v['v'].'】必须是png透明格式图片'));
				@up_send($file,'p/img/grade'.$v['i'].$grade.'.png',0,$_UP['upMsize']);
			}
		}
		if ($grade != $oldgrade){//等级有变化
			//foreach ($sex_ARR as $v){@up_send_admindel('p/img/grade'.$v['i'].$oldgrade.'.png');}//删除老图
			foreach ($sex_ARR as $v){@up_adm_rename_pic('p/img/grade'.$v['i'].$oldgrade.'.png','p/img/grade'.$v['i'].$grade.'.png');}
			$db->query("UPDATE ".__TBL_USER__." SET grade=".$grade." WHERE grade=".$oldgrade);
		}
		//
		$row2 = $db->ROW(__TBL_ROLE__,"title,grade,if2","id=".$id,'num');$oldtitle= $row2[0];$oldgrade= $row2[1];$oldif2= $row2[2];
		AddLog('修改会员组，原组：【'.$oldtitle.'，权重：'.$oldgrade.'，时长：'.get_if2_title($oldif2).'】->新组：【'.$title.'，权重：'.$grade.'，时长：'.get_if2_title($if2).'】');
		//
		$db->query("UPDATE ".__TBL_ROLE__." SET grade=".$grade.",title='$title',if2=".$if2." WHERE id=".$id);
		json_exit(array('flag'=>1));
	break;
	case "delpicupdate":
		if (!ifint($id))alert_adm_parent('forbidden','back');
		@up_send_admindel('p/img/grade'.$sg.'.png');
		//
		$row2 = $db->ROW(__TBL_ROLE__,"title,grade,if2","id=".$id,'num');$title= $row2[0];$grade= $row2[1];$if2= $row2[2];
		AddLog('删除会员组【'.$title.'，权重：'.$grade.'，时长：'.get_if2_title($if2).'】图标');
		//
		header("Location: ".SELF."?submitok=mod&id=".$id);
	break;
	case "ajax_delupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		//不能删光，必须要留一个
		$rolenum = $db->COUNT(__TBL_ROLE__,"kind=1");
		if ($rolenum <= 1){
			$db->query("UPDATE ".__TBL_ROLE__." SET ifdefault=1");
			json_exit(array('flag'=>0,'msg'=>'亲，不能删光啊，至少要留一个啊'));	
		}
		//删除图标，获取是否默认会员组标记
		$row = $db->ROW(__TBL_ROLE__,"grade,ifdefault,title,if2","id=".$id,'num');
		if ($row){
			$grade=$row[0];$ifdefault=$row[1];$title=$row[2];$if2=$row[3];
			foreach ($sex_ARR as $v){@up_send_admindel('p/img/grade'.$v['i'].$grade.'.png');}
		}else{json_exit(array('flag'=>0,'msg'=>'forbidden'));}
		//删除会员组
		$db->query("DELETE FROM ".__TBL_ROLE__." WHERE id=".$id);
		//更新降级会员表
		$rolenum = $db->COUNT(__TBL_USER__,"grade=".$grade);
		if ($rolenum > 0){
			$row = $db->ROW(__TBL_ROLE__,"grade","kind=1 ORDER BY grade LIMIT 1","num");
			$newgrade  = $row[0];
			$db->query("UPDATE ".__TBL_USER__." SET grade=".$newgrade." WHERE grade=".$grade);
		}
		//如果当前删除的是默认组，则将最低权重设为默认组
		//获取最小权重roleid
		if ($ifdefault == 1){
			$row = $db->ROW(__TBL_ROLE__,"id","kind=1 ORDER BY grade LIMIT 1","num");
			$db->query("UPDATE ".__TBL_ROLE__." SET ifdefault=1 WHERE id=".$row[0]);
		}
		AddLog('删除会员组【'.$title.'，权重：'.$grade.'，时长：'.get_if2_title($if2).'】');
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case "ajax_defaultupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$db->query("UPDATE ".__TBL_ROLE__." SET ifdefault=0 WHERE kind=1");
		$db->query("UPDATE ".__TBL_ROLE__." SET ifdefault=1 WHERE kind=1 AND id=".$id);
		json_exit(array('flag'=>1));
	break;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$urole  = json_decode($_ZEAI['urole'],true);
$sj_rmb1 = json_decode($_VIP['sj_rmb1'],true);
$sj_rmb2 = json_decode($_VIP['sj_rmb2'],true);
$contact_loveb = json_decode($_VIP['contact_loveb'],true);
$contact_daylooknum = json_decode($_VIP['contact_daylooknum'],true);
$chat_loveb = json_decode($_VIP['chat_loveb'],true);
$chat_daylooknum = json_decode($_VIP['chat_daylooknum'],true);
$photo_num = json_decode($_VIP['photo_num'],true);
$video_num = json_decode($_VIP['video_num'],true);
$loveb_buy = json_decode($_VIP['loveb_buy'],true);
$vipC      = json_decode($_VIP['vipC'],true);
$chat_duifangfree = json_decode($_VIP['chat_duifangfree'],true);
$sj_loveb = json_decode($_VIP['sj_loveb'],true);
$trend_addflag = json_decode($_VIP['trend_addflag'],true);
$trend_bbsflag = json_decode($_VIP['trend_bbsflag'],true);
$viewlist = json_decode($_VIP['viewlist'],true);
$qianxian_num = json_decode($_VIP['qianxian_num'],true);
$meet_num = json_decode($_VIP['meet_num'],true);
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
td.tdLbgHUI{background-color:#eee}
</style>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<body>
<div class="navbox">
<a href="urole.php" <?php echo ($submitok != 'safetips')?' class="ed"':'';?>>会员组/VIP套餐设置</a>
<a href="urole.php?submitok=safetips"<?php echo ($submitok == 'safetips')?' class="ed"':'';?>>安全交友提示</a>
</div>
<div class="fixedblank"></div>
<?php if ($submitok == "safetips") {?>
	<form id="ZEAI_CN___FORM">
	<table class="table W1200 Mtop20  size2 cols2" style="margin:20px 0 0 20px">
    <tr><th align="left" colspan="2">安全交友提示</th></tr>
	<tr>
	<td class="tdL">提醒内容</td>
	<td class="tdR"><textarea name="safetips" id="safetips" placeholder="会出现在相关页面提醒" rows="6" class="W100_ S14"><?php echo dataIO($_VIP['safetips'],'wx');?></textarea>
    </td>
	</tr>
	<tr>
	  <td colspan="2" align="center">
        <input name="submitok" id="submitok" type="hidden" value="ajax_mod_vip_safetips">
		<input name="uu" id="uu" type="hidden" value="<?php echo $session_uid;?>">
		<input name="pp" id="pp" type="hidden" value="<?php echo $session_pwd;?>">
		<button type="button" class="btn size3 HUANG3" onClick="safetipsFn();">确认并保存</button>
      </td>
	  </tr>
	</table>
	</form>
    <script>
	function safetipsFn(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAI_CN___FORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}
		});
	}
    </script>
<?php exit;}?>
<!--ADD-->
<?php if ($submitok == "add" || $submitok == "mod") {
	if($submitok == "mod"){
		$row = $db->ROW(__TBL_ROLE__,"title,grade,if2","kind=1 AND id=".$id,"name");
		if ($row){
			$title = dataIO($row['title'],'out');
			$grade = $row['grade'];
			$if2   = $row['if2'];
		}else{exit('forbidden');}
	}?>
    <style>
.table.cols2 .tdR1{width:450px}    
.table.cols2 .tdR2{width:390px}    
    
    </style>
	<form id="ZEAIFORM" name="ZEAIFORM" method="post" enctype="multipart/form-data">
	<table class="table W1200 Mtop20  size2 cols2" style="margin:20px 0 0 20px">
    <tr><th align="left" colspan="4" style="border:0"><?php echo ($submitok == 'add')?'新增':'修改';?>会员组</th></tr>
	<tr>
	<td class="tdL">会员组名称</td>
	<td align="left" class="tdR1"><input id="title" name="title" type="text" class="W300 size2" size="30" maxlength="20" placeholder="如：钻石会员，至尊会员，贵宾包月会员等" value="<?php echo $title;?>"></td>
	<td class="tdL"> 权重等级</td>
	<td align="left" class="tdR2"><input name="grade" type="text" class="W50 size2" id="grade" maxlength="2" value="<?php echo $grade;?>"> <span class="tips S12 C999">填1~10，数字越大级别越高，每个会员组权重不要相同</span></td>
	</tr>

	<?php
    $icopath1 = 'p/img/grade1'.$grade.'.png?'.ADDTIME;
    $pic_str1 = (up_check_file_exists($icopath1))?true:false;
    $pic_url1 = $_ZEAI['up2'].'/'.$icopath1;
	//
    $icopath2 = 'p/img/grade2'.$grade.'.png?'.ADDTIME;
    $pic_str2 = (up_check_file_exists($icopath2))?true:false;
    $pic_url2 = $_ZEAI['up2'].'/'.$icopath2;
    ?>
    <tr>
    <td class="tdL">尊享图标【男】</td>
    <td class="tdR">
		<?php if (up_check_file_exists('p/img/grade1'.$grade.'.png')){?>    
            <a class="pic60" onClick="parent.piczoom('<?php echo $pic_url1; ?>')"><img src="<?php echo $pic_url1; ?>"></a>　
            <a class="btn size1" onClick="parent.zeai.confirm('确认删除【男】图标重新上传么？',function(){zeai.openurl('urole'+zeai.ajxext+'submitok=delpicupdate&id=<?php echo $id; ?>&sg=1<?php echo $grade; ?>');})">删除</a>
            <span class="tips S12">删除后可更换</span>　  
		<?php }else{echo "<input name='pic0' type='file' size='50' class='Caaa size2 W200' /><span class='tips S12'>必须为透明png格式，正方形，宽高60px</span>";}?>  
    </td>
    <td class="tdL">尊享图标【女】</td>
    <td class="tdR">
		<?php if (up_check_file_exists('p/img/grade2'.$grade.'.png')){?>    
            <a class="pic60" onClick="parent.piczoom('<?php echo $pic_url2; ?>')"><img src="<?php echo $pic_url2; ?>"></a>　
            <a class="btn size1" onClick="parent.zeai.confirm('确认删除【女】图标重新上传么？',function(){zeai.openurl('urole'+zeai.ajxext+'submitok=delpicupdate&id=<?php echo $id; ?>&sg=2<?php echo $grade; ?>');})">删除</a>
            <span class="tips S12">删除后可更换</span
		><?php }else{echo "<input name='pic1' type='file' size='50' class='Caaa size2 W200' /><span class='tips S12'>必须为透明png格式，正方形，宽高60px</span>";}?>  
    </td>
    </tr>

    <tr>
    <td class="tdL">服务价格【男】</td>
    <td class="tdR">
        <input name="sj_rmb1" id="sj_rmb1" type="text" class="W80 size2" maxlength="5" value="<?php echo $sj_rmb1[$grade.'_'.$if2];?>"> 元
    </td>
    <td class="tdL">服务价格【女】</td>
    <td class="tdR"><input name="sj_rmb2" id="sj_rmb2" type="text" class="W80 size2" maxlength="5" value="<?php echo $sj_rmb2[$grade.'_'.$if2];?>"> 元</td>
    </tr>
    
    <tr>
    <td class="tdL">VIP有效期限</td>
    <td colspan="3" class="tdR">
        <input type="radio" name="if2" id="if2_1" class="radioskin" value="1"<?php echo ($if2 == 1)?' checked':'';?>><label for="if2_1" class="radioskin-label"><i class="i1"></i><b class="W50">1个月</b></label>
        <input type="radio" name="if2" id="if2_3" class="radioskin" value="3"<?php echo ($if2 == 3)?' checked':'';?>><label for="if2_3" class="radioskin-label"><i class="i1"></i><b class="W50">3个月</b></label>
        <input type="radio" name="if2" id="if2_6" class="radioskin" value="6"<?php echo ($if2 == 6)?' checked':'';?>><label for="if2_6" class="radioskin-label"><i class="i1"></i><b class="W50">6个月</b></label>
        <input type="radio" name="if2" id="if2_12" class="radioskin" value="12"<?php echo ($if2 == 12)?' checked':'';?>><label for="if2_12" class="radioskin-label"><i class="i1"></i><b class="W50">12个月</b></label>
        <input type="radio" name="if2" id="if2_999" class="radioskin" value="999"<?php echo ($if2 == 999)?' checked':'';?>><label for="if2_999" class="radioskin-label"><i class="i1"></i><b class="W50">永久</b></label>
    </td>
    </tr>
    
    
	<tr>
	<td class="tdL">在线充值爱豆折扣</td>
	<td class="tdR"><input name="loveb_buy" type="text" class="W80 size2" maxlength="4" value="<?php echo $loveb_buy[$grade];?>"><span class="tips S12">1为原价，0.9对应9折</span></td>
	<td class="tdL">修改基本资料</td>
	<td class="tdR">
        <input type="radio" name="switch_sh_moddata" id="switch_sh_moddata1" class="radioskin" value="0"<?php echo ($switch['sh']['moddata_'.$grade] == 0)?' checked':'';?>>
        <label for="switch_sh_moddata1" class="radioskin-label"><i class="i1"></i><b class="W80">需要审核</b></label>
        
        <input type="radio" name="switch_sh_moddata" id="switch_sh_moddata2" class="radioskin" value="1"<?php echo ($switch['sh']['moddata_'.$grade] == 1)?' checked':'';?>>
        <label for="switch_sh_moddata2" class="radioskin-label"><i class="i1"></i><b class="W80">直接通过</b></label>
    </td>
	</tr>
	<tr>
	  <td class="tdL tdLbgHUI">每天看联系方式总人数</td>
	  <td class="tdR"><input name="contact_daylooknum" type="text" class="W80 size2" maxlength="5" value="<?php echo $contact_daylooknum[$grade];?>"> 人　　　　<span class="tips S12">填0为不能查看，填0下面按次将失效</span></td>
	  <td class="tdL">上传头像照片</td>
	  <td class="tdR">
<input type="radio" name="switch_sh_photom" id="switch_photom1<?php echo $grade;?>" class="radioskin" value="0"<?php echo ($switch['sh']['photom_'.$grade] == 0)?' checked':'';?>>
<label for="switch_photom1<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80">需要审核</b></label>
<input type="radio" name="switch_sh_photom" id="switch_photom2<?php echo $grade;?>" class="radioskin" value="1"<?php echo ($switch['sh']['photom_'.$grade] == 1)?' checked':'';?>>
<label for="switch_photom2<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80">直接通过</b></label>      
      
      </td>
	  </tr>
	<tr>
	  <td class="tdL tdLbgHUI">查看联系方按次计费</td>
	  <td class="tdR"><input name="contact_loveb" type="text" class="W80 size2" maxlength="5" value="<?php echo $contact_loveb[$grade];?>"> <?php echo $_ZEAI['loveB'];?>/人 　  <span class="tips S12">填0为免费查看，但不超过上面每天总人数</span></td>
	  <td class="tdL">上传个人视频</td>
	  <td class="tdR">
<input type="radio" name="switch_sh_video" id="switch_video1<?php echo $grade;?>" class="radioskin" value="0"<?php echo ($switch['sh']['video_'.$grade] == 0)?' checked':'';?>>
<label for="switch_video1<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80">需要审核</b></label>
<input type="radio" name="switch_sh_video" id="switch_video2<?php echo $grade;?>" class="radioskin" value="1"<?php echo ($switch['sh']['video_'.$grade] == 1)?' checked':'';?>>
<label for="switch_video2<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80">直接通过</b></label>      
      </td>
	  </tr>
	<tr>
	  <td class="tdL ">每天看信解锁总人数</td>
	  <td class="tdR"><input name="chat_daylooknum" type="text" class="W80 size2" maxlength="5" value="<?php echo $chat_daylooknum[$grade];?>"> 人　　　　<span class="tips S12">填0为不能解锁，填0下面按次将失效</span></td>
	  <td class="tdL">上传个人相册</td>
	  <td class="tdR">
<input type="radio" name="switch_sh_photo" id="switch_photo1<?php echo $grade;?>" class="radioskin" value="0"<?php echo ($switch['sh']['photo_'.$grade] == 0)?' checked':'';?>>
<label for="switch_photo1<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80">需要审核</b></label>
<input type="radio" name="switch_sh_photo" id="switch_photo2<?php echo $grade;?>" class="radioskin" value="1"<?php echo ($switch['sh']['photo_'.$grade] == 1)?' checked':'';?>>
<label for="switch_photo2<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80">直接通过</b></label>      
      </td>
	  </tr>
	<tr>
	  <td class="tdL ">看信解锁按次计费</td>
	  <td class="tdR"><input name="chat_loveb" type="text" class="W80 size2" maxlength="5" value="<?php echo $chat_loveb[$grade];?>"> <?php echo $_ZEAI['loveB'];?>/人 　 <span class="tips S12">填0为免费解锁，但不超过上面每天总人数</span></td>
	  <td class="tdL">相册容量</td>
	  <td class="tdR"><input name="photo_num" type="text" class="W80 size2" maxlength="5" value="<?php echo $photo_num[$grade];?>"> 张</td>
	  </tr>
        
        <tr>
        <td rowspan="2" class="tdL ">对方看信绿色通道</td>
        <td rowspan="2" class="tdR lineH150"><input type="checkbox" class="switch" name="chat_duifangfree" id="chat_duifangfree" value="1"<?php echo ($chat_duifangfree[$grade]==1)?' checked':'';?>><label for="chat_duifangfree" class="switch-label"><i></i><b>开启</b><b>关闭</b></label><span class="tips2 S12" style="display:inline-block">
        开启后，此会员组发出的信对方任何会员都可以不限量直接查看，无需解锁<br>
        开启后，对方任何会员都可以可直接给此会员组不限量写信看信，无需解锁
        </span></td>
        <td class="tdL">视频容量</td>
        <td class="tdR"><input name="video_num" type="text" class="W80 size2" maxlength="5" value="<?php echo $video_num[$grade];?>">
个</td>
        </tr>
        <tr>
          <td class="tdL">升级会员送爱豆</td>
          <td class="tdR"><input name="sj_loveb" type="text" class="W80 size2" maxlength="7" value="<?php echo $sj_loveb[$grade];?>"> <?php echo $_ZEAI['loveB'];?><span class="tips S12 FR">在线支付后升级到此级别赠送爱豆，填0为不送</span></td>
        </tr>     
      
      <tr>
	  <td rowspan="3" valign="top" class="tdL tdLbgHUI">会员服务模式</td>
	  <td rowspan="3" valign="top" class="tdR lineH150" style="padding-top:12px;">
      
        <input type="radio" name="switch_Smode" id="switch_Smode1" class="radioskin" value="1"<?php echo ($switch['Smode']['g_'.$grade] == 1)?' checked':'';?>>
        <label for="switch_Smode1" class="radioskin-label"><i class="i1"></i><b class="W100">线上自助服务</b></label>
        
        <input type="radio" name="switch_Smode" id="switch_Smode2" class="radioskin" value="2"<?php echo ($switch['Smode']['g_'.$grade] == 2)?' checked':'';?>>
        <label for="switch_Smode2" class="radioskin-label"><i class="i1"></i><b class="W100">线下人工服务</b></label>
        <br>
      <span class="tips2 S12" style="margin-top:5px;display:inline-block">
		　1．选择【线上自助服务】，此会员组主页将显示联系方式和私信聊天入口<br>
		　2．选择【线下人工服务】，此会员组主页将隐藏联系方式和私信聊天入口<br>　　　联系唯一途径就是寻求官方红娘来牵线<br>
      </span>
      </td>
	  <td valign="top" class="tdL">浏览谁看过我列表</td>
	  <td valign="top" class="tdR">
<input type="checkbox" class="switch" name="viewlist" id="viewlist" value="1"<?php echo ($viewlist[$grade]==1)?' checked':'';?>><label for="viewlist" class="switch-label"><i></i><b>开启</b><b>关闭</b></label><span class="tips S12 FR">开启后，此会员组可以查看访客列表，关闭提示升级</span>      
      </td>
	  </tr>
      <tr>
        <td valign="top" class="tdL">交友圈主题发布</td>
        <td valign="top" class="tdR">
        <input type="radio" name="trend_addflag" id="trend_addflag0" class="radioskin" value="0"<?php echo ($trend_addflag[$grade] == 0)?' checked':'';?>>
        <label for="trend_addflag0" class="radioskin-label"><i class="i1"></i><b class="W80 S14">需要审核</b></label>
        <input type="radio" name="trend_addflag" id="trend_addflag1" class="radioskin" value="1"<?php echo ($trend_addflag[$grade] == 1)?' checked':'';?>>
        <label for="trend_addflag1" class="radioskin-label"><i class="i1"></i><b class="W80 S14">直接通过</b></label>
        </td>
      </tr>
      <tr>
        <td valign="top" class="tdL">交友圈评论发布</td>
        <td valign="top" class="tdR">
        <input type="radio" name="trend_bbsflag" id="trend_bbsflag0" class="radioskin" value="0"<?php echo ($trend_bbsflag[$grade] == 0)?' checked':'';?>>
        <label for="trend_bbsflag0" class="radioskin-label"><i class="i1"></i><b class="W80 S14">需要审核</b></label>
        <input type="radio" name="trend_bbsflag" id="trend_bbsflag1" class="radioskin" value="1"<?php echo ($trend_bbsflag[$grade] == 1)?' checked':'';?>>
        <label for="trend_bbsflag1" class="radioskin-label"><i class="i1"></i><b class="W80 S14">直接通过</b></label>
        </td>
      </tr>      
      <tr>
	  <td rowspan="2" valign="top" class="tdL"><br>
	    线下服务或套餐详情</td>
	  <td rowspan="2" valign="top" class="tdR lineH150"><textarea name="vipC" id="vipC" placeholder="如：&#13;&#10;&#13;&#10;可以享受专属红娘1对1牵线服务10次&#13;&#10;可以享受2次线下单身交友派对" rows="6" class="W100_ S14">
<?php echo dataIO($vipC[$grade],'wx');?>
</textarea><span class="tips2 S12">如果要加红特出显示，可以用b标签来特出，如&lt;b&gt;我是红色&lt;/b&gt; <font style="color:#FD66B5">我是红色</font><br>
支持简单的标准HTML代码，请在官方指导下进行修改</span>
</td>
	  <td height="10" valign="top" class="tdL">牵线次数</td>
	  <td valign="top" class="tdR"><input name="qianxian_num" type="text" class="W80 size2" maxlength="5" value="<?php echo $qianxian_num[$grade];?>"> 次
      <div class="S12 C999 lineH150">主要应用于红娘少或没有红娘简单服务模式，没有多级权限分配等复杂流程，交友管理后台操作，此次数只做为显示参照，无其它用途</div>
      </td>
	  </tr>
      <tr>
        <td valign="top" class="tdL">约见次数</td>
        <td valign="top" class="tdR"><input name="meet_num" type="text" class="W80 size2" maxlength="5" value="<?php echo $meet_num[$grade];?>"> 次
        <div class="S12 C999 lineH150">主要应用于大公司红娘多的服务模式，此功能主要在CRM中体验</div>
        </td>
      </tr>
      </table>
    
        <?php if ($submitok == "add") {?>
        	<input name="submitok" id="submitok" type="hidden" value="ajax_addupdate">
        <?php }elseif($submitok == "mod"){ ?>
            <input name="submitok" id="submitok" type="hidden" value="ajax_modupdate">
            <input name="oldgrade" type="hidden" value="<?php echo $grade;?>">
            <input name="id" type="hidden" value="<?php echo $id;?>">
        <?php }?>
		<input name="uu" id="uu" type="hidden" value="<?php echo $session_uid;?>">
		<input name="pp" id="pp" type="hidden" value="<?php echo $session_pwd;?>">
    </form>
<br><br><br><br><br><br><br>
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
<!--MOD-->
<?php }else{?>
<!--LIST-->
	<?php
	$rt = $db->query("SELECT id,title,authoritylist,grade,if2,ifdefault,flag FROM ".__TBL_ROLE__." WHERE kind=1 ORDER BY grade");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodatatips'>... 暂无 ...　　　<a class='btn size2 HUANG' onClick=\"zeai.openurl('".SELF."?submitok=add')\">新增会员组</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="120" align="left"><button type="button" class="btn " onClick="zeai.openurl('<?php echo SELF;?>?submitok=add')">新增会员组</button></td>
		<td align="left"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">系统默认权重1为普通会员，2~10为VIP会员</font></td>
	  </tr>
	</table>
	<table class="tablelist">
	<tr>
        <th width="5">&nbsp;</th>
        <th width="60" align="center">尊享图标</th>
        <th width="20" align="center">&nbsp;</th>
        <th width="200">会员组名称</th>
        <th width="60">服务类型</th>
        <th width="80">时长</th>
        <th width="100">服务价格（元）</th>
        <th width="59" align="center">权重</th>
        <th width="59">会员数量</th>
        <th width="200" align="center">会员组权限</th>
        <th>&nbsp;</th>
        <th width="100">默认注册会员组</th>
        <th width="100" align="center">VIP升级列表显示</th>
        <th width="49" class="center">修改</th>
        <th width="30" class="center">删除</th>
	</tr>
	<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$id = $rows['id'];
		$title = dataIO($rows['title'],'out');
		$grade = intval($rows['grade']);
		$if2   = intval($rows['if2']);
		$ifdefault = $rows['ifdefault'];
		$flag = $rows['flag'];
		$unum = $db->COUNT(__TBL_USER__,"grade=".$grade);
	?>
	<tr>
	<td width="5" height="60">&nbsp;</td>
    <td width="60" height="40" align="center">
    	<?php
		foreach ($sex_ARR as $v) {
			$icopath = 'p/img/grade'.$v['i'].$grade.'.png';
			$pic_url = $_ZEAI['up2'].'/'.$icopath;
			$pic_str = (up_check_file_exists($icopath))?'<img width="30" height="30" src="'.$pic_url.'?'.ADDTIME.'">':'';
		?><a onClick="piczoom('<?php echo $pic_url; ?>')"><?php echo $pic_str; ?></a><?php }?>
    </td>
	<td width="20" align="center">&nbsp;</td>
	<td width="200" class="S14"><?php echo $title;?></td>
	<td width="60" class="S14"><?php echo ($switch['Smode']['g_'.$grade] == 1)?'线上':'线下';?></td>
	<td width="80" class="S14"><?php if($grade>1)echo get_if2_title($if2);?></td>
	<td width="100" class="S14 C666">
    <?php if ($grade>1){
	$rmb1 = abs(intval($sj_rmb1[$grade.'_'.$if2]));
	$rmb2 = abs(intval($sj_rmb2[$grade.'_'.$if2]));
	?>
	男：<?php echo ($rmb1>0)?'<font class="S12 Cf00">￥</font><font class="Cf00">'.$rmb1.'</font>':'免费';?><br>
	女：<?php echo ($rmb2>0)?'<font class="S12 Cf00">￥</font><font class="Cf00">'.$rmb2.'</font>':'免费';?>
    <?php }else{ ?>
    免费
    <?php }?>
    </td>
	<td width="59" align="center" class="S14"><?php echo $grade;?></td>
	<td width="59" class="S14"><span class="numdian"><?php echo $unum;?></span></td>
	<td width="200" align="center">
    <a class="aLAN" href="javascript:;" onClick="zeai.openurl('<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>')">设置权限</a></td>
	<td>&nbsp;</td>
<td width="100">
  <?php if ($ifdefault == 1){?>
  <a class="aQINGed not-allowed tips" title="<?php echo $title;?>" tips-title="默认第一次注册的会员组，如果更改请到左侧【注册选项】设置">默认组</a>
  <?php }else{ ?>
  <a tips-title="默认第一次注册的会员组" title="<?php echo $title;?>" value="<?php echo $id; ?>" class="aQING tips" style="display:none">设为默认组</a>
  <?php }?>
</td>
<td width="100" align="center">
<?php if ($grade>1){?>
<input type="checkbox" id="flag<?php echo $id;?>" class="switch" value="<?php echo $flag;?>"<?php echo ($flag == 1)?' checked':'';?>><label value="<?php echo $id;?>" for="flag<?php echo $id;?>" class="switch-label"><i></i><b>显示</b><b>隐藏</b></label>
<?php }?>
</td>
	<td width="49" class="center"><a value="<?php echo $id;?>" class="editico" title="修改<?php echo $title;?>" onClick="zeai.openurl('<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>')"></a></td>
	<td width="30" class="center"><a value="<?php echo $id; ?>" unum="<?php echo $unum;?>" class="delico" title="<?php echo $title;?>"></a></td>
	</tr>
	<?php } ?>
	<?php if ($total > $pagesize){?>
	<tfoot><tr>
	<td colspan="15" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
	</tr></tfoot>
	<?php } ?>
	</table>
	<?php } ?>
<?php } ?>
<script>
<?php if ($submitok == "add" || $submitok == "mod") {?>
	save.onclick = function(){
		zeai.msg('正在保存',{time:30});
		zeai.ajax({url:'urole'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);
			if (rs.flag == 1){
				zeai.msg(0);zeai.msg('正在生成高速缓存',{time:30});
				//urolecache();
				//location.reload(true);
				o('submitok').value='cache_config_vip';
				zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if (rs.flag == 1){
						setTimeout(function(){zeai.openurl('urole'+zeai.extname)},1000);
					}
				});
			}else if(rs.flag == 0){
				zeai.msg(rs.msg,o(rs.focus));
			}else{
				zeai.msg(rs.msg);
			}
		});
	}
<?php }else{ ?>
	zeai.listEach('.switch-label',function(obj){
		obj.onclick = function(){
			var id = parseInt(obj.getAttribute("value"));
			var chkobj  = o('flag'+id);
			setTimeout(function(){
				var chkV = chkobj.checked;
				var flag = (chkobj.checked)?1:0;
				var postjson = {submitok:'cache_config_urole_flag',id:id,flag:flag,uu:'<?php echo $session_uid;?>',pp:'<?php echo $session_pwd;?>'};
				zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:postjson},function(e){var rs=zeai.jsoneval(e);
					zeai.msg(rs.msg);
					if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}
				});
			},300);
		}
	});

	zeai.listEach('.delico',function(obj){
		obj.onclick = function(){
			var id = parseInt(obj.getAttribute("value"));
			var unum = parseInt(obj.getAttribute("unum"));
			var title = obj.getAttribute("title");
			var tips = (unum>0)?'当前会员组包含 '+unum+' 个会员，删除后这些会员将被降级为权重最低会员组。':'';
			zeai.confirm('<font color="red">请慎重！</font><br>'+tips+'真的要删除【'+title+'】么？<br>删除后请重设收费和审核机制！',function(){
				zeai.ajax('urole'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){urolecache();location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	
	zeai.listEach('.aQING',function(obj){
		obj.onclick = function(){
			var id = parseInt(obj.getAttribute("value"));
			var title = obj.getAttribute("title");
			zeai.confirm('<font color="red">请慎重！</font><br>'+'真的要将【'+title+'】设为默认会员组么？',function(){
				zeai.ajax('urole'+zeai.ajxext+'submitok=ajax_defaultupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){urolecache();location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	
<?php } ?>
	function urolecache(){
		var postjson = {submitok:'cache_config_roleinfo',uu:'<?php echo $session_uid;?>',pp:'<?php echo $session_pwd;?>'};
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:postjson});
	}
</script>
<?php require_once 'bottomadm.php';?>
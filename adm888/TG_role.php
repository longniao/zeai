<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('u_tg',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';

if ($submitok == 'ajax_addupdate' || $submitok == 'ajax_modupdate'){
	if (empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【等级】名称','focus'=>'title'));
	if (str_len($title) >500)json_exit(array('flag'=>0,'msg'=>'【等级标题】请不要超过500字节','focus'=>'title'));
	if (!ifint($grade) )json_exit(array('flag'=>0,'msg'=>'请输入权重等级 1~10','focus'=>'grade'));
	$grade = abs(intval($grade));
	if ($grade > 10)$grade = 10;if ($grade == 0)$grade = 1;
	$title  = dataIO($title,'out',100);
	$title2 = dataIO($title2,'out',500);
	$vip_tj_minUnum  = intval($vip_tj_minUnum);

	$price  = floatval($price);
	$price2 = floatval($price2);
	//
	$reward_kind  = (empty($reward_kind))?'loveb':$reward_kind;
			
	$reg_loveb_sex1_num1  = intval($reg_loveb_sex1_num1);
	$reg_loveb_sex1_num2  = intval($reg_loveb_sex1_num2);
	$reg_loveb_sex2_num1  = intval($reg_loveb_sex2_num1);
	$reg_loveb_sex2_num2  = intval($reg_loveb_sex2_num2);
	
	$reg_money_sex1_num1  = floatval($reg_money_sex1_num1);
	$reg_money_sex1_num2  = floatval($reg_money_sex1_num2);
	$reg_money_sex2_num1  = floatval($reg_money_sex2_num1);
	$reg_money_sex2_num2  = floatval($reg_money_sex2_num2);
	
	$cz_sex1_num1  = intval($cz_sex1_num1);
	$cz_sex1_num2  = intval($cz_sex1_num2);
	$cz_sex2_num1  = intval($cz_sex2_num1);
	$cz_sex2_num2  = intval($cz_sex2_num2);
	
	$vip_sex1_num1  = intval($vip_sex1_num1);
	$vip_sex1_num2  = intval($vip_sex1_num2);
	$vip_sex2_num1  = intval($vip_sex2_num1);
	$vip_sex2_num2  = intval($vip_sex2_num2);
	
	$rz_sex1_num1  = intval($rz_sex1_num1);
	$rz_sex1_num2  = intval($rz_sex1_num2);
	$rz_sex2_num1  = intval($rz_sex2_num1);
	$rz_sex2_num2  = intval($rz_sex2_num2);

	$union_reg_num1  = floatval($union_reg_num1);
	$union_reg_num2  = floatval($union_reg_num2);
	$union_num1  = intval($union_num1);
	$union_num2  = intval($union_num2);

	$tx_min_price     = intval($tx_min_price);
	$tx_daymax_price  = intval($tx_daymax_price);
	$tx_sxf_bfb       = intval($tx_sxf_bfb);
	
	$push_kind = (@is_array($push_kind))?implode(',',$push_kind):'';
	$push_month_apply_num  = intval($push_month_apply_num);
	$push_month_push_num   = intval($push_month_push_num);
	
	$content = dataIO($content,'out');
	$bz      = dataIO($bz,'out');
	$shopgrade = 0;
	
}
switch ($submitok){
	case "ajax_addupdate":
		if ($db->ROW(__TBL_TG_ROLE__,"id","shopgrade=0 AND title='$title'"))json_exit(array('flag'=>0,'msg'=>'等级名称【'.$title.'】出现重复，请重试','focus'=>'title'));
		if ($grade>10 || $grade==0)json_exit(array('flag'=>0,'msg'=>'请重输入权重1~10','focus'=>'grade'));
		if ($db->ROW(__TBL_TG_ROLE__,"id","shopgrade=0 AND grade=".$grade))json_exit(array('flag'=>0,'msg'=>'权重【'.$grade.'】出现重复，请重输','focus'=>'grade'));
		//
		$file = $_FILES["pic0"];
		if (!empty($file['tmp_name'])){
			if (empty($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'请给当前等级上传一个牛逼点的图标吧'));
			if (getpicextname($file['tmp_name']) != 'png' || !ifpic($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'当前等级必须是png透明格式图片'));
			$dbname = setphotodbname('tg',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			//
			$logo = $dbname;
		}
		
		$db->query("INSERT INTO ".__TBL_TG_ROLE__." (union_reg_num1,union_reg_num2,title,title2,vip_tj_minUnum,grade,logo,price,price2,reward_kind,reg_loveb_sex1_num1,reg_loveb_sex1_num2,reg_loveb_sex2_num1,reg_loveb_sex2_num2,reg_money_sex1_num1,reg_money_sex1_num2,reg_money_sex2_num1,reg_money_sex2_num2,cz_sex1_num1,cz_sex1_num2,cz_sex2_num1,cz_sex2_num2,vip_sex1_num1,vip_sex1_num2,vip_sex2_num1,vip_sex2_num2,rz_sex1_num1,rz_sex1_num2,rz_sex2_num1,rz_sex2_num2,union_num1,union_num2,tx_min_price,tx_daymax_price,tx_sxf_bfb,push_kind,push_month_apply_num,push_month_push_num,content,bz
) VALUES ('$union_reg_num1','$union_reg_num2','$title','$title2','$vip_tj_minUnum','$grade','$logo','$price','$price2','$reward_kind','$reg_loveb_sex1_num1','$reg_loveb_sex1_num2','$reg_loveb_sex2_num1','$reg_loveb_sex2_num2','$reg_money_sex1_num1','$reg_money_sex1_num2','$reg_money_sex2_num1','$reg_money_sex2_num2','$cz_sex1_num1','$cz_sex1_num2','$cz_sex2_num1','$cz_sex2_num2','$vip_sex1_num1','$vip_sex1_num2','$vip_sex2_num1','$vip_sex2_num2','$rz_sex1_num1','$rz_sex1_num2','$rz_sex2_num1','$rz_sex2_num2','$union_num1','$union_num2','$tx_min_price','$tx_daymax_price','$tx_sxf_bfb','$push_kind','$push_month_apply_num','$push_month_push_num','$content','$bz')");

		AddLog('【推广套餐】->新增【'.$title.'】');
		json_exit(array('flag'=>1,'msg'=>'新增成功'));
	break;
	case "ajax_modupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$grade = abs(intval($grade));
		if ($grade != $oldgrade){
			if ($db->ROW(__TBL_TG_ROLE__,"id","shopgrade=0 AND grade=".$grade))json_exit(array('flag'=>0,'msg'=>'权重【'.$grade.'】出现重复，请重输','focus'=>'grade'));
		}
		//
		$file = $_FILES["pic0"];
		if (!empty($file['tmp_name'])){
			//
			$file = $_FILES["pic0"];
			if (empty($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'请给当前等级上传一个牛逼点的图标吧'));
			if (getpicextname($file['tmp_name']) != 'png' || !ifpic($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'当前等级必须是png透明格式图片'));
			$dbname = setphotodbname('tg',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			//
			$SQL  = ",logo='$dbname'";
		}
		
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET union_reg_num1='$union_reg_num1',union_reg_num2='$union_reg_num2',title='$title',title2='$title2',vip_tj_minUnum='$vip_tj_minUnum',grade='$grade'".$SQL.",price='$price',price2='$price2',reward_kind='$reward_kind',reg_loveb_sex1_num1='$reg_loveb_sex1_num1',reg_loveb_sex1_num2='$reg_loveb_sex1_num2',reg_loveb_sex2_num1='$reg_loveb_sex2_num1',reg_loveb_sex2_num2='$reg_loveb_sex2_num2',reg_money_sex1_num1='$reg_money_sex1_num1',reg_money_sex1_num2='$reg_money_sex1_num2',reg_money_sex2_num1='$reg_money_sex2_num1',reg_money_sex2_num2='$reg_money_sex2_num2',cz_sex1_num1='$cz_sex1_num1',cz_sex1_num2='$cz_sex1_num2',cz_sex2_num1='$cz_sex2_num1',cz_sex2_num2='$cz_sex2_num2',vip_sex1_num1='$vip_sex1_num1',vip_sex1_num2='$vip_sex1_num2',vip_sex2_num1='$vip_sex2_num1',vip_sex2_num2='$vip_sex2_num2',rz_sex1_num1='$rz_sex1_num1',rz_sex1_num2='$rz_sex1_num2',rz_sex2_num1='$rz_sex2_num1',rz_sex2_num2='$rz_sex2_num2',union_num1='$union_num1',union_num2='$union_num2',tx_min_price='$tx_min_price',tx_daymax_price='$tx_daymax_price',tx_sxf_bfb='$tx_sxf_bfb',push_kind='$push_kind',push_month_apply_num='$push_month_apply_num',push_month_push_num='$push_month_push_num',content='$content',bz='$bz',shopgrade=0 WHERE id=".$id);
		
		$db->query("UPDATE ".__TBL_TG_USER__." SET gradetitle='$title' WHERE grade=".$grade);
		AddLog('【推广套餐】->修改【'.$title.'】');
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case "delpicupdate":
		if (!ifint($id))alert_adm_parent('forbidden','back');
		$row  = $db->ROW(__TBL_TG_ROLE__,"logo","id=".$id,"num");
		$logo = $row[0];
		@up_send_admindel($logo);
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET logo='' WHERE id=".$id);
		AddLog('【推广套餐】->删除图标【id:'.$id.'】');
		header("Location: ".SELF."?submitok=mod&id=".$id);
	break;
	case "ajax_delupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		//不能删光，必须要留一个
		$rolenum = $db->COUNT(__TBL_TG_ROLE__,"shopgrade=0");
		if ($rolenum <= 1){
			$db->query("UPDATE ".__TBL_TG_ROLE__." SET ifdefault=1 WHERE shopgrade=0");
			json_exit(array('flag'=>0,'msg'=>'亲，不能删光啊，至少要留一个啊'));	
		}
		//删除图标，获取是否默认等级标记
		$row = $db->ROW(__TBL_TG_ROLE__,"grade,ifdefault,logo","shopgrade=0 AND id=".$id,'num');
		if ($row){
			$grade=$row[0];$ifdefault=$row[1];$logo=$row[2];
			@up_send_admindel($logo);
		}else{json_exit(array('flag'=>0,'msg'=>'forbidden'));}
		//删除等级
		$db->query("DELETE FROM ".__TBL_TG_ROLE__." WHERE shopgrade=0 AND id=".$id);
		//更新降级推广员表
		
/*		
		$rolenum = $db->COUNT(__TBL_USER__,"grade=".$grade);
		if ($rolenum > 0){
			$row = $db->ROW(__TBL_TG_ROLE__,"grade","ORDER BY grade LIMIT 1","num");
			$newgrade  = $row[0];
			$db->query("UPDATE ".__TBL_USER__." SET grade=".$newgrade." WHERE grade=".$grade);
		}
		
*/		
		//如果当前删除的是默认组，则将最低权重设为默认组
		//获取最小权重roleid
		if ($ifdefault == 1){
			$row = $db->ROW(__TBL_TG_ROLE__,"id","WHERE shopgrade=0 AND ORDER BY grade LIMIT 1","num");
			$db->query("UPDATE ".__TBL_TG_ROLE__." SET ifdefault=1 WHERE shopgrade=0 AND id=".$row[0]);
		}
		AddLog('【推广套餐】->删除【id:'.$id.'】');
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case "ajax_defaultupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET ifdefault=0 WHERE shopgrade=0");
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET ifdefault=1 WHERE id=".$id);
		AddLog('【推广套餐】->设默认组【id:'.$id.'】');
		json_exit(array('flag'=>1));
	break;
	case "ajax_flagupdate":
		if(!ifint($id))exit(JSON_ERROR);
		$id = intval($id);$flag = ($flag == 1)?$flag:0;
		if($flag == 1){
			$msg='开启成功';
		}else{
			$msg='关闭成功';
		}
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET flag=".$flag." WHERE id=".$id);
		AddLog('【推广套餐】->状态修改【id:'.$id.'】');
		exit(json_encode(array('flag'=>1,'msg'=>$msg)));
	break;
	case"ding":
		if(!ifint($id))exit(JSON_ERROR);
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET px=".ADDTIME." WHERE id=".$id);
		AddLog('【推广套餐】->置顶【id:'.$id.'】');
		header("Location: ".SELF);
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
td.tdLbgHUI{background-color:#eee}
img.m{width:60px;height:60px;display:block;margin:5px 0;object-fit:cover;-webkit-object-fit:cover}
</style>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<div class="navbox">
<a href="TG_role.php" <?php echo ($submitok != 'safetips')?' class="ed"':'';?>>推广员等级套餐</a>
</div>
<div class="fixedblank"></div>

<!--ADD-->
<?php if ($submitok == "add" || $submitok == "mod") {
	if($submitok == "mod"){
		$row = $db->ROW(__TBL_TG_ROLE__,"*","id=".$id,"name");
		if ($row){
			$id    = $row['id'];
			$title = dataIO($row['title'],'out');
			$title2 = dataIO($row['title2'],'out');
			$grade  = $row['grade'];
			$vip_tj_minUnum  = intval($row['vip_tj_minUnum']);
			$logo   = $row['logo'];
			$price  = $row['price'];
			$price2  = $row['price2'];
			$reward_kind  = $row['reward_kind'];
			
			$reg_loveb_sex1_num1  = intval($row['reg_loveb_sex1_num1']);
			$reg_loveb_sex1_num2  = intval($row['reg_loveb_sex1_num2']);
			$reg_loveb_sex2_num1  = intval($row['reg_loveb_sex2_num1']);
			$reg_loveb_sex2_num2  = intval($row['reg_loveb_sex2_num2']);
			
			$reg_money_sex1_num1  = floatval($row['reg_money_sex1_num1']);
			$reg_money_sex1_num2  = floatval($row['reg_money_sex1_num2']);
			$reg_money_sex2_num1  = floatval($row['reg_money_sex2_num1']);
			$reg_money_sex2_num2  = floatval($row['reg_money_sex2_num2']);
			
			$cz_sex1_num1  = intval($row['cz_sex1_num1']);
			$cz_sex1_num2  = intval($row['cz_sex1_num2']);
			$cz_sex2_num1  = intval($row['cz_sex2_num1']);
			$cz_sex2_num2  = intval($row['cz_sex2_num2']);
			
			$vip_sex1_num1  = intval($row['vip_sex1_num1']);
			$vip_sex1_num2  = intval($row['vip_sex1_num2']);
			$vip_sex2_num1  = intval($row['vip_sex2_num1']);
			$vip_sex2_num2  = intval($row['vip_sex2_num2']);
			
			$rz_sex1_num1  = intval($row['rz_sex1_num1']);
			$rz_sex1_num2  = intval($row['rz_sex1_num2']);
			$rz_sex2_num1  = intval($row['rz_sex2_num1']);
			$rz_sex2_num2  = intval($row['rz_sex2_num2']);
			
			$union_reg_num1  = floatval($row['union_reg_num1']);
			$union_reg_num2  = floatval($row['union_reg_num2']);
			$union_num1  = intval($row['union_num1']);
			$union_num2  = intval($row['union_num2']);

			$tx_min_price     = intval($row['tx_min_price']);
			$tx_daymax_price  = intval($row['tx_daymax_price']);
			$tx_sxf_bfb       = intval($row['tx_sxf_bfb']);
			
			$push_kind = dataIO($row['push_kind'],'out');
			$push_month_apply_num  = intval($row['push_month_apply_num']);
			$push_month_push_num   = intval($row['push_month_push_num']);
			
			$content = dataIO($row['content'],'out');
			$bz      = dataIO($row['bz'],'out');

			$logo_url = (!empty($logo))?$_ZEAI['up2'].'/'.$logo:HOST.'/res/noP.gif';
		}else{exit('forbidden');}
	}
	?>
    <style>.table.cols2 .tdL{width:160px}</style>
	<form id="ZEAIFORM" name="ZEAIFORM" method="post" enctype="multipart/form-data">
	<table width="1192" class="table Mtop20  size2 cols2" style="width:1111px;margin:20px 0 100px 20px">
    <tr><th align="center" colspan="2"><?php echo ($submitok == 'add')?'新增':'修改';?>推广员套餐</th></tr>
    
	<tr>
	<td width="156" class="tdL">等级套餐名称</td>
	<td align="left" class="tdR"><input id="title" name="title" type="text" class="W300 size2" maxlength="250" value="<?php echo $title;?>">　
    <span class="tips S12">如：公益红娘，资深红娘，高级红娘，金牌红娘，钻石红娘等</span>
    </td>
	</tr>
    
	<tr>
	<td width="156" class="tdL">权重等级</td>
	<td align="left" class="tdR"><input name="grade" class="W80 size2" id="grade"  type="number" min="1" maxlength="2" value="<?php echo $grade;?>"> <span class="tips S12">填1~10，数字越大级别越高，每个等级权重不要相同，1代表普通级别不参与升级</span>
    </td>
	</tr>
	
    <tr>
    <td class="tdL">等级标识</td>
    <td class="tdR">
		<?php if (!empty($logo)){?>    
            <a class="pic60" onClick="parent.piczoom('<?php echo $pic_url; ?>')"><img src="<?php echo $logo_url; ?>" class="m"></a>　
            <a class="btn size1" onClick="parent.zeai.confirm('确认删除图标重新上传么？',function(){zeai.openurl('TG_role'+zeai.ajxext+'submitok=delpicupdate&id=<?php echo $id; ?>');})">删除</a>
            <span class="tips S12">删除后可更换</span>　  
		<?php }else{echo "<input name='pic0' type='file' size='50' class='Caaa size2 W200' />";}?>  
        <span class='tips S12'>必须为透明png格式，正方形，宽高60*60像数</span>
    </td>
    </tr>
	<tr>
	<td width="156" class="tdL">升级价格</td>
	<td align="left" class="tdR">
    
    原价：<input id="price2" name="price2" type="text" class="W50 size2" size="30" maxlength="5" value="<?php echo $price2;?>"> 元　　　现价：<input id="price" name="price" type="text" class="W50 size2" size="30" maxlength="5" value="<?php echo $price;?>"> 元
    <span class="tips S12">实际支付以现价为准</span>
    </td>
	</tr>
    
	<tr>
	<td width="156" class="tdL">等级标题</td>
	<td align="left" class="tdR">
    <input id="title2" name="title2" type="text" class="W300 size2" maxlength="20" value="<?php echo $title2;?>">
    <span class="tips S12">用于推广员升级等级显示标题，为空则显示等级名称</span>
    </td>
	</tr>
    
	<tr>
	<td width="156" class="tdL">升级条件</td>
	<td align="left" class="tdR">
    推广满：<input id="vip_tj_minUnum" name="vip_tj_minUnum" type="text" class="W50 size2" size="30" maxlength="5" value="<?php echo $vip_tj_minUnum;?>"> 个会员可升级
    <span class="tips S12">填0不限</span>
    </td>
	</tr>

	<tr>
	  <td class="tdL tdLbgHUI">单身用户注册</td>
	  <td class="tdR">
	    
	    <table class="table0" cellpadding="4" style="margin:0">
	      
          
	      <tr class="bottomborder" style="display:none">
	        <td width="90" rowspan="2" align="left" class="S14" style="border-right:#eee 1px solid">　<input type="radio" name="reward_kind" id="reward_kindloveb" class="radioskin" value="loveb"<?php echo ($reward_kind == 'loveb')?' checked':'';?>><label for="reward_kindloveb" class="radioskin-label"><i class="i1"></i><b class="W50"><?php echo $_ZEAI['loveB'];?></b></label></td>
	        <td height="50" align="left" class="S14">
            
	         　男　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="reg_loveb_sex1_num1" id="reg_loveb_sex1_num1" type="text" class="W50" maxlength="5" value="<?php echo $reg_loveb_sex1_num1;?>"> <?php echo $_ZEAI['loveB'];?>/人　
	         　团队奖(上级)：<input name="reg_loveb_sex1_num2" id="reg_loveb_sex1_num2" type="text" class="W50" maxlength="5" value="<?php echo $reg_loveb_sex1_num2;?>"> <?php echo $_ZEAI['loveB'];?>/人
            <span class="tips S12">至少100，整数，填0不送</span>
            
            </td>
          </tr>
	      <tr class="bottomborder" style="display:none">
	        <td height="50" align="left" class="S14">
            
	          　女　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="reg_loveb_sex2_num1" id="reg_loveb_sex2_num1" type="text" class="W50" maxlength="5" value="<?php echo $reg_loveb_sex2_num1;?>"> <?php echo $_ZEAI['loveB'];?>/人　　
	          团队奖(上级)：<input name="reg_loveb_sex2_num2" id="reg_loveb_sex2_num2" type="text" class="W50" maxlength="5" value="<?php echo $reg_loveb_sex2_num2;?>"> <?php echo $_ZEAI['loveB'];?>/人
            <span class="tips S12">至少100，整数，填0不送</span>
            
            
            </td>
          </tr>
          
          
          
	      <tr>
	        <td width="90" height="50" rowspan="2" align="left" class="S14" style="border-right:#eee 1px solid">　<input type="radio" name="reward_kind" id="reward_kindmoney" class="radioskin" value="money"<?php echo ($reward_kind == 'money')?' checked':'';?>><label for="reward_kindmoney" class="radioskin-label"><i class="i1"></i><b class="W50">现金</b></label></td>
	        <td height="50" align="left" class="S14">
	          　男　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="reg_money_sex1_num1" id="reg_money_sex1_num1" type="text" class="W50" maxlength="5" value="<?php echo $reg_money_sex1_num1;?>"> 元/人　　　
	          团队奖(上级)：<input name="reg_money_sex1_num2" id="reg_money_sex1_num2" type="text" class="W50" maxlength="5" value="<?php echo $reg_money_sex1_num2;?>"> 元/人　
	          <span class="tips S12">至少0.2元，填0不送</span>
            </td>
          </tr>
	      <tr class="bottomborder">
	        <td height="50" align="left" class="S14">
            
	          　女　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="reg_money_sex2_num1" id="reg_money_sex2_num1" type="text" class="W50" maxlength="5" value="<?php echo $reg_money_sex2_num1;?>"> 元/人　　　
	          团队奖(上级)：<input name="reg_money_sex2_num2" id="reg_money_sex2_num2" type="text" class="W50" maxlength="5" value="<?php echo $reg_money_sex2_num2;?>"> 元/人　
            <span class="tips S12">至少0.2元，填0不送</span>
            </td>
          </tr>
        </table>
	    
	    </td>
	  </tr>
	<tr>
	  <td class="tdL tdLbgHUI">单身用户充值</td>
	  <td class="tdR">
      
        男　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="cz_sex1_num1" id="cz_sex1_num1" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $cz_sex1_num1;?>"> %　　　团队奖(上级)：<input name="cz_sex1_num2" id="cz_sex1_num2" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $cz_sex1_num2;?>"> %　<span class="tips S12">填0不奖励</span>
        <div style="margin-top:8px"></div>
        女　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="cz_sex2_num1" id="cz_sex2_num1" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $cz_sex2_num1;?>"> %　　　团队奖(上级)：<input name="cz_sex2_num2" id="cz_sex2_num2" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $cz_sex2_num2;?>"> %　<span class="tips S12">填0不奖励</span>
 
      </td>
	  </tr>
        
        <tr>
        <td class="tdL tdLbgHUI">单身用户升级VIP</td>
        <td class="tdR lineH150">
        男　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="vip_sex1_num1" id="vip_sex1_num1" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $vip_sex1_num1;?>"> %　　　团队奖(上级)：<input name="vip_sex1_num2" id="vip_sex1_num2" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $vip_sex1_num2;?>"> %　<span class="tips S12">填0不奖励</span>
        <div style="margin-top:8px"></div>
        女　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="vip_sex2_num1" id="vip_sex2_num1" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $vip_sex2_num1;?>"> %　　　团队奖(上级)：<input name="vip_sex2_num2" id="vip_sex2_num2" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $vip_sex2_num2;?>"> %　<span class="tips S12">填0不奖励</span>
        </td>
        </tr>
        
        <tr>
        <td class="tdL tdLbgHUI">单身用户认证</td>
        <td class="tdR lineH150">
        男　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="rz_sex1_num1" id="rz_sex1_num1" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $rz_sex1_num1;?>"> %　　　团队奖(上级)：<input name="rz_sex1_num2" id="rz_sex1_num2" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $rz_sex1_num2;?>"> %　<span class="tips S12">（实名认证和真人认证）填0或认证费用填0不奖励</span>
        <div style="margin-top:8px"></div>
        女　<i class="ico Caaa S18">&#xe62d;</i>　直接奖(直推)：<input name="rz_sex2_num1" id="rz_sex2_num1" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $rz_sex2_num1;?>"> %　　　团队奖(上级)：<input name="rz_sex2_num2" id="rz_sex2_num2" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $rz_sex2_num2;?>"> %　<span class="tips S12">（实名认证和真人认证）填0或认证费用填0不奖励</span>
        </td>
        </tr>
                
        <tr>
        <td class="tdL tdLbgHUI">合伙人注册</td>
        <td class="tdR ">
        直接奖(直推)：<input name="union_reg_num1" id="union_reg_num1" type="text" class="W50" maxlength="5" value="<?php echo $union_reg_num1;?>"> 元/人　
        团队奖(上级)：<input name="union_reg_num2" id="union_reg_num2" type="text" class="W50" maxlength="5" value="<?php echo $union_reg_num2;?>"> 元/人
        <br><span class="tips2 S12">填0不奖励，大于0，推广员推荐合伙人注册成功，上级推广员也会获得相应的奖励，<font class="Cf00">推荐填0关闭</font></span>
        </td>
        </tr>

        <tr>
        <td class="tdL tdLbgHUI">合伙人帐号激活/升级</td>
        <td class="tdR ">
        直接奖(直推)：<input name="union_num1" id="union_num1" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $union_num1;?>"> %　　
        团队奖(上级)推荐人：<input name="union_num2" id="union_num2" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $union_num2;?>"> %
        　<span class="tips S12">填0不奖励，大于0，推广员新帐号激活或者升级，上级推广员也会获得相应设置的奖励</span>
        </td>
        </tr>

        <tr>
        <td class="tdL ">单笔最小提现金额</td>
        <td class="tdR"><input name="tx_min_price" id="tx_min_price" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $tx_min_price;?>"> 元　<span class="tips S12">最小1元</span></td>
        </tr>
        <tr>
        <td class="tdL ">每天提现最多金额限制</td>
        <td class="tdR"><input name="tx_daymax_price" id="tx_daymax_price" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $tx_daymax_price;?>"> 元　<span class="tips S12">填0不限</span></td>
        </tr>
        <tr>
        <td class="tdL ">提现扣除手续费比例</td>
        <td class="tdR"><input name="tx_sxf_bfb" id="tx_sxf_bfb" type="text" class="W50 FVerdana" maxlength="2" value="<?php echo $tx_sxf_bfb;?>"> %　<span class="tips S12">填0不收手续费，全额到账</span></td>
        </tr>
       
         <tr style="display:none">
        <td class="tdL tdLbgHUI">商家/机构推送通知</td>
        <td class="tdR">
        <?php $push_kindARR=explode(',',$push_kind);?>
        推送形式　
			<input type="checkbox" name="push_kind[]" id="push_kind1" class="checkskin " value="tips"<?php echo (@in_array('tips',$push_kindARR))?' checked':'';?>><label for="push_kind1" class="checkskin-label"><i class="i1"></i><b class="W100">站内信通知</b></label>
			<input type="checkbox" name="push_kind[]" id="push_kind2" class="checkskin " value="wxkefu"<?php echo (@in_array('wxkefu',$push_kindARR))?' checked':'';?>><label for="push_kind2" class="checkskin-label"><i class="i1"></i><b class="W200">公众号主动式(客服消息群发)</b></label>
			<input type="checkbox" name="push_kind[]" id="push_kind3" class="checkskin " value="wxkumy"<?php echo (@in_array('wxkumy',$push_kindARR))?' checked':'';?>><label for="push_kind3" class="checkskin-label"><i class="i1"></i><b class="W200">公众号被动式(会员点击【我的】触发)</b></label>
			<input type="checkbox" name="push_kind[]" id="push_kind4" class="checkskin " value="poster"<?php echo (@in_array('poster',$push_kindARR))?' checked':'';?>><label for="push_kind4" class="checkskin-label"><i class="i1"></i><b class="W200">弹出海报(会员登录后强制弹出海报)</b></label>

		<div style="margin-top:8px"></div>


        每月申请次数：<input name="push_month_apply_num" id="push_month_apply_num" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo $push_month_apply_num;?>"> 次　<span class="tips S12">每个月最多能提交申请的次数；0表示不限</span>
        <div style="margin-top:8px"></div>
        每次推送条数：<input name="push_month_push_num" id="push_month_push_num" type="text" class="W50  FVerdana" maxlength="3" value="<?php echo $push_month_push_num;?>"> 
        条　<span class="tips S12">每次推送最多能发送的条数；0表示不限，推荐200以内，以防公众号被封</span></td>
        </tr>
       
    <tr>
    <td valign="top" class="tdL">套餐详情</td>
    <td valign="top" class="tdR lineH150"><textarea name="content" id="content" rows="3" class="W700 S14"><?php echo $content;?></textarea></td>
    </tr>
    <tr>
    <td valign="top" class="tdL">备注</td>
    <td valign="top" class="tdR lineH150"><textarea name="bz" id="bz" rows="3" class="W700 S14"><?php echo $bz;?></textarea></td>
    </tr>
      
	
	</table>
    
    <div class="savebtnbox">
        <?php if ($submitok == "add") {?>
        	<input name="submitok" id="submitok" type="hidden" value="ajax_addupdate">
        <?php }elseif($submitok == "mod"){ ?>
            <input name="submitok" id="submitok" type="hidden" value="ajax_modupdate">
            <input name="oldgrade" type="hidden" value="<?php echo $grade;?>">
            <input name="id" type="hidden" value="<?php echo $id;?>">
        <?php }?>
		<input name="uu" id="uu" type="hidden" value="<?php echo $session_uid;?>">
		<input name="pp" id="pp" type="hidden" value="<?php echo $session_pwd;?>">
		<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
    </div>

    </form>
    <br><br><br><br>
<!--MOD-->
<?php }else{?>
<!--LIST-->
	<?php
	$rt = $db->query("SELECT * FROM ".__TBL_TG_ROLE__." WHERE shopgrade=0 ORDER BY px DESC,grade DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodataico'><i></i>暂无等级<br><a class='btn size2 HUANG3' onClick=\"zeai.openurl('".SELF."?submitok=add')\">新增等级</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="120" align="left"><button type="button" class="btn " onClick="zeai.openurl('<?php echo SELF;?>?submitok=add')"><i class="ico add">&#xe620;</i> 新增等级</button></td>
		<td align="left"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">系统默认权重为1</font></td>
	  </tr>
	</table>
	<table class="tablelist">
	<tr>
        <th width="60" align="center">组ID</th>
      <th width="60" align="center">置顶</th>
        <th width="60" align="center">图标</th>
        <th width="150" align="center">套餐名称</th>
        <th width="60" align="center">等级</th>
        <th width="80" align="center">价格(元)</th>
        <th width="150" align="center">升级条件</th>
        <th width="80" align="center">奖励内容</th>
        <th width="60" align="center">推广员数量</th>
        <th align="center">&nbsp;</th>
      <th width="100">默认注册等级</th>
        <th width="100" class="center">状态</th>
        <th width="50" class="center">修改</th>
        <th width="50" class="center">删除</th>
	</tr>
	<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$id = $rows['id'];
		$title = trimhtml(dataIO($rows['title'],'out'));
		$grade = intval($rows['grade']);
		$shopgrade = intval($rows['shopgrade']);
		$logo  = $rows['logo'];
		$ifdefault = $rows['ifdefault'];
		$price  = floatval($rows['price']);
		$flag   = $rows['flag'];
		$vip_tj_minUnum   = $rows['vip_tj_minUnum'];
		$reward_kind   = $rows['reward_kind'];
		$logo_url = (!empty($logo))?$_ZEAI['up2'].'/'.$logo:HOST.'/res/noP.gif';
		$unum = $db->COUNT(__TBL_TG_USER__,"grade=".$grade);
	?>
	<tr>
	<td width="60" height="60" align="center" class="S14"><?php echo $id;?></td>
    <td width="60" height="40" align="center"><a href="<?php echo "TG_role.php?id=".$id; ?>&submitok=ding" class="topico" title="置顶"></a></td>
	<td width="60" align="center"><img src="<?php echo $logo_url; ?>" class="m"></td>
	<td width="150" align="center" class="S14"><?php echo $title;?></td>
	<td width="60" align="center" class="S14"><?php echo $grade;?></td>
	<td width="80" align="center" class="S14 Cf00"><?php echo $price;?></td>
	<td width="150" align="center" class="S14"><?php if ($vip_tj_minUnum > 0){?>推满<?php echo $vip_tj_minUnum;?>人可升级<?php }?></td>
	<td width="80" align="center" class="S14">
    
    <?php
	if ($reward_kind == 'loveb'){
		echo $_ZEAI['loveB'];
	}elseif($reward_kind == 'money'){
		echo '现金';
	}
	?>
    
    
    </td>
	<td width="60" align="center" class="S14"><?php echo $unum;?></td>
	<td align="center">&nbsp;</td>
	<td width="100">
	  <?php if ($ifdefault == 1){?>
	  <a class="aQINGed not-allowed tips" title="<?php echo $title;?>" tips-title="默认第一次注册的等级">默认组</a>
	  <?php }else{ ?>
	  <a tips-title="默认第一次注册的等级" title="<?php echo $title;?>" value="<?php echo $id; ?>" class="aQING tips">设为默认组</a>
	  <?php }?>
</td>
	<td width="100" class="center"><input type="checkbox" id="flag<?php echo $id;?>" class="switch" value="<?php echo $flag;?>"<?php echo ($flag == 1)?' checked':'';?>><label value="<?php echo $id;?>" for="flag<?php echo $id;?>" class="switch-label"><i></i><b>启用</b><b>隐藏</b></label></td>
	<td width="50" class="center"><a value="<?php echo $id;?>" class="editico" title="修改<?php echo $title;?>" onClick="zeai.openurl('<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>')"></a></td>
	<td width="50" class="center"><a value="<?php echo $id; ?>" unum="<?php echo $unum;?>" class="delico" title="删除<?php echo $title;?>"></a></td>
	</tr>
	<?php } ?>
	<?php if ($total > $pagesize){?>
	<tfoot><tr>
	<td colspan="14" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
	</tr></tfoot>
	<?php } ?>
	</table>
	<?php } ?>
<?php } ?>
<script>
<?php if ($submitok == "add" || $submitok == "mod") {?>
	save.onclick = function(){
		zeai.ajax({url:'TG_role'+zeai.extname,form:o('ZEAIFORM')},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);
			if (rs.flag == 1){
				zeai.msg(rs.msg);
				setTimeout(function(){zeai.openurl('TG_role'+zeai.extname)},1000);
			}else if(rs.flag == 0){
				zeai.msg(rs.msg,o(rs.focus));
			}else{
				zeai.msg(rs.msg);
			}		
		});
	}
<?php }else{ ?>

	zeai.listEach('.delico',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var unum = parseInt(obj.getAttribute("unum"));
		var title = obj.getAttribute("title");
		var tips = (unum>0)?'当前等级包含 '+unum+' 个推广员，删除后这些推广员将被降级为权重最低等级。':'';
		obj.onclick = function(){
			zeai.confirm('<font color="red">请慎重！</font><br>'+tips+'真的要删除【'+title+'】么？',function(){
				zeai.ajax('TG_role'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	
	zeai.listEach('.aQING',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var title = obj.getAttribute("title");
		obj.onclick = function(){
			zeai.confirm('<font color="red">请慎重！</font><br>'+'真的要将【'+title+'】设为默认等级么？',function(){
				zeai.ajax('TG_role'+zeai.ajxext+'submitok=ajax_defaultupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	
	zeai.listEach('.switch-label',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var chkobj = o('flag'+id);
		obj.onclick = function(){
			var chkV = chkobj.checked;
			var flag = (chkobj.checked)?0:1;
			zeai.ajax('TG_role'+zeai.ajxext+'submitok=ajax_flagupdate&id='+id+'&flag='+flag,function(e){var rs=zeai.jsoneval(e);
				zeai.msg(rs.msg);
			});
		}
	});

<?php } ?>
</script>
<?php require_once 'bottomadm.php';?>


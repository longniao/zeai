<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('shop',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_shop.php';
if ($submitok == 'ajax_addupdate' || $submitok == 'ajax_modupdate'){
	if (empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【角色组】名称','focus'=>'title'));
	if (str_len($title) >500)json_exit(array('flag'=>0,'msg'=>'【等级标题】请不要超过500字节','focus'=>'title'));
	if (!ifint($shopgrade) )json_exit(array('flag'=>0,'msg'=>'请输入权重等级 1~10','focus'=>'shopgrade'));
	$shopgrade = abs(intval($shopgrade));
	if ($shopgrade > 10)$shopgrade = 10;if ($shopgrade == 0)$shopgrade = 1;
	$title  = dataIO($title,'out',100);
	$title2 = dataIO($title2,'out',500);
	$price  = floatval($price);
	$price2 = floatval($price2);
	$tx_daymax_price  = intval($tx_daymax_price);
	$tx_sxf_bfb       = intval($tx_sxf_bfb);
	$content = dataIO($content,'in');
	$bz      = dataIO($bz,'out');
	$grade=0;
	$productmaxnum = intval($productmaxnum);
	$yxq=intval($yxq);
}
switch ($submitok){
	case "ajax_addupdate":
		if ($db->ROW(__TBL_TG_ROLE__,"id","title='$title'"))json_exit(array('flag'=>0,'msg'=>'角色组名称【'.$title.'】出现重复，请重试','focus'=>'title'));
		if ($shopgrade>10 || $shopgrade==0)json_exit(array('flag'=>0,'msg'=>'请重输入权重1~10','focus'=>'shopgrade'));
		if ($db->ROW(__TBL_TG_ROLE__,"id","shopgrade=".$shopgrade))json_exit(array('flag'=>0,'msg'=>'权重【'.$shopgrade.'】出现重复，请重输','focus'=>'shopgrade'));
		$file = $_FILES["pic0"];
		if (!empty($file['tmp_name'])){
			if (empty($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'请给当前角色组上传一个牛逼点的图标吧'));
			$dbname = setphotodbname('shop',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$logo = $dbname;
		}
		$db->query("INSERT INTO ".__TBL_TG_ROLE__." (title,title2,grade,shopgrade,logo,price,price2,content,bz,tx_daymax_price,tx_sxf_bfb,productmaxnum,yxq) VALUES ('$title','$title2','$grade','$shopgrade','$logo','$price','$price2','$content','$bz','$tx_daymax_price','$tx_sxf_bfb','$productmaxnum',$yxq)");
		AddLog('【'.$_SHOP['title'].'套餐】->新增【'.$title.'】');
		shopgrade_chace();
		json_exit(array('flag'=>1,'msg'=>'新增成功'));
	break;
	case "ajax_modupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$shopgrade = abs(intval($shopgrade));
		if ($shopgrade != $oldshopgrade){
			if ($db->ROW(__TBL_TG_ROLE__,"id","shopgrade=".$shopgrade))json_exit(array('flag'=>0,'msg'=>'权重【'.$shopgrade.'】出现重复，请重输','focus'=>'shopgrade'));
		}
		$file = $_FILES["pic0"];
		if (!empty($file['tmp_name'])){
			$file = $_FILES["pic0"];
			if (empty($file['tmp_name']))json_exit(array('flag'=>0,'msg'=>'请给当前角色组上传一个牛逼点的图标吧'));
			$dbname = setphotodbname('shop',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			$SQL = ",logo='$dbname'";
		}
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET productmaxnum='$productmaxnum',title='$title',title2='$title2',shopgrade='$shopgrade',price='$price',price2='$price2',tx_daymax_price='$tx_daymax_price',tx_sxf_bfb='$tx_sxf_bfb',content='$content',bz='$bz',yxq='$yxq'".$SQL." WHERE id=".$id);
		$db->query("UPDATE ".__TBL_TG_USER__." SET shopgradetitle='$title' WHERE shopgrade=".$shopgrade);
		AddLog('【'.$_SHOP['title'].'套餐】->修改【'.$title.'】');
		shopgrade_chace();
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case "delpicupdate":
		if (!ifint($id))alert_adm_parent('forbidden','back');
		$row  = $db->ROW(__TBL_TG_ROLE__,"logo","id=".$id,"num");
		$logo = $row[0];
		@up_send_admindel($logo);
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET logo='' WHERE id=".$id);
		AddLog('【'.$_SHOP['title'].'套餐】->删除图标【id:'.$id.'】');
		shopgrade_chace();
		header("Location: ".SELF."?submitok=mod&id=".$id);
	break;
	case "ajax_delupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		//不能删光，必须要留一个
		$rolenum = $db->COUNT(__TBL_TG_ROLE__,"grade=0");
		if ($rolenum <= 1){
			$db->query("UPDATE ".__TBL_TG_ROLE__." SET ifdefault=1 WHERE grade=0");
			json_exit(array('flag'=>0,'msg'=>'亲，不能删光啊，至少要留一个啊'));	
		}
		//删除图标，获取是否默认角色组标记
		$row = $db->ROW(__TBL_TG_ROLE__,"shopgrade,ifdefault,logo","id=".$id,'num');
		if ($row){
			$shopgrade=$row[0];$ifdefault=$row[1];$logo=$row[2];
			@up_send_admindel($logo);
		}else{json_exit(array('flag'=>0,'msg'=>'forbidden'));}
		//删除角色组d
		$db->query("DELETE FROM ".__TBL_TG_ROLE__." WHERE id=".$id);
		//更新降级商家表
		if ($ifdefault == 1){
			$row = $db->ROW(__TBL_TG_ROLE__,"id","WHERE grade=0 ORDER BY shopgrade LIMIT 1","num");
			$db->query("UPDATE ".__TBL_TG_ROLE__." SET ifdefault=1 WHERE id=".$row[0]);
		}
		AddLog('【'.$_SHOP['title'].'套餐】->删除【id:'.$id.'】');
		shopgrade_chace();
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case "ajax_defaultupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET ifdefault=0 WHERE grade=0");
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET ifdefault=1 WHERE id=".$id);
		AddLog('【'.$_SHOP['title'].'套餐】->设默认组【id:'.$id.'】');
		shopgrade_chace();
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
		AddLog('【'.$_SHOP['title'].'套餐】->状态修改【id:'.$id.'】');
		shopgrade_chace();
		exit(json_encode(array('flag'=>1,'msg'=>$msg)));
	break;
	case"ding":
		if(!ifint($id))exit(JSON_ERROR);
		$db->query("UPDATE ".__TBL_TG_ROLE__." SET px=".ADDTIME." WHERE id=".$id);
		AddLog('【'.$_SHOP['title'].'套餐】->置顶【id:'.$id.'】');
		shopgrade_chace();
		header("Location: ".SELF);
	break;
}
function shopgrade_chace() {
	global $db,$_SHOP;
	$rt = $db->query("SELECT shopgrade AS g,title AS t,ifdefault AS d,yxq,flag AS f,logo AS ico FROM ".__TBL_TG_ROLE__." WHERE grade=0 ORDER BY px DESC,shopgrade DESC,id DESC");
	while($tmprows = $db->fetch_array($rt,'name')){$arr[]=$tmprows;}
	$_SHOP['shopgradearr'] = encode_json($arr);
	cache_mod_config($_SHOP,'config_shop','_SHOP');
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
.tips{font-size:12px}
</style>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<div class="navbox">
<a href="shop_role.php" <?php echo ($submitok != 'safetips')?' class="ed"':'';?>><?php echo $_SHOP['title'];?>等级套餐</a>
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
			$shopgrade  = $row['shopgrade'];
			$logo   = $row['logo'];
			$price  = $row['price'];
			$price2  = $row['price2'];
			$tx_daymax_price  = intval($row['tx_daymax_price']);
			$tx_sxf_bfb       = intval($row['tx_sxf_bfb']);
			$content = dataIO($row['content'],'wx');
			$bz      = dataIO($row['bz'],'out');
			$logo_url = (!empty($logo))?$_ZEAI['up2'].'/'.$logo:HOST.'/res/noP.gif';
			$productmaxnum = intval($row['productmaxnum']);
			$yxq = intval($row['yxq']);
		}else{exit('forbidden');}
	}
	?>
    <style>.table.cols2 .tdL{width:160px}</style>
	<form id="ZEAIFORM" name="ZEAIFORM" method="post" enctype="multipart/form-data">
	<table width="1192" class="table W1200 Mtop20  size2 cols2" style="margin:20px 0 100px 20px">
    <tr><th colspan="2"><?php echo ($submitok == 'add')?'新增':'修改';?><?php echo $_SHOP['title'];?>角色组</th></tr>
    
	<tr>
	<td width="156" class="tdL"><font class="Cf00">*</font>套餐角色名称</td>
	<td align="left" class="tdR"><input id="title" name="title" type="text" class="W300 size2" maxlength="250" value="<?php echo $title;?>">　<span class="tips S12">如：金牌<?php echo $_SHOP['title'];?>，银牌<?php echo $_SHOP['title'];?>，銅牌<?php echo $_SHOP['title'];?>等</span></td>
	</tr>
	<tr>
	<td width="156" class="tdL">小标题</td>
	<td align="left" class="tdR">
    <input id="title2" name="title2" type="text" class="W300 size2" maxlength="20" value="<?php echo $title2;?>">　<span class="tips S12">用于升级等级显示标题，为空则显示角色组名称</span></td>
	</tr>

	<tr>
	<td width="156" class="tdL"><font class="Cf00">*</font>权重等级</td>
	<td align="left" class="tdR"><input name="shopgrade" class="W100 size2" id="shopgrade"  type="number" min="1" maxlength="2" value="<?php echo $shopgrade;?>">　<span class="tips S12">填1~10，数字越大级别越高，每个角色组权重不要相同</span>
    </td>
	</tr>
	
    <tr>
    <td class="tdL"><font class="Cf00">*</font>等级图标</td>
    <td class="tdR">
		<?php if (!empty($logo)){?>    
            <a class="pic60" onClick="parent.piczoom('<?php echo $logo_url; ?>')"><img src="<?php echo $logo_url; ?>" class="m"></a>　
            <a class="btn size1" onClick="parent.zeai.confirm('确认删除图标重新上传么？',function(){zeai.openurl('shop_role'+zeai.ajxext+'submitok=delpicupdate&id=<?php echo $id; ?>');})">删除</a>
            <span class="tips S12">删除后可更换</span>　  
		<?php }else{echo "<input name='pic0' type='file' size='50' class='Caaa size2 W200' />";}?>  
        <span class='tips S12'>必须为jpg/gif/png格式，正方形，宽高100*100像数</span>
    </td>
    </tr>
	<tr>
	<td width="156" class="tdL"><font class="Cf00">*</font>入驻升级价格</td>
	<td align="left" class="tdR">
    
    原价：<input id="price2" name="price2" type="text" class="W100 size2" size="30" maxlength="6" value="<?php echo $price2;?>"> 元　　　现价：<input id="price" name="price" type="text" class="W100 size2" size="30" maxlength="6" value="<?php echo $price;?>"> 元
    <span class="tips S12">实际支付以现价为准</span>
    </td>
	</tr>
    <tr>
    <td class="tdL "><font class="Cf00">*</font>有效期限</td>
    <td class="tdR">
        <input type="radio" name="yxq" id="yxq_7" class="radioskin" value="7"<?php echo ($yxq == 7)?' checked':'';?>><label for="yxq_7" class="radioskin-label"><i class="i1"></i><b class="W50">1周</b></label>
        <input type="radio" name="yxq" id="yxq_30" class="radioskin" value="30"<?php echo ($yxq == 30)?' checked':'';?>><label for="yxq_30" class="radioskin-label"><i class="i1"></i><b class="W50">1个月</b></label>
        <input type="radio" name="yxq" id="yxq_90" class="radioskin" value="90"<?php echo ($yxq == 90)?' checked':'';?>><label for="yxq_90" class="radioskin-label"><i class="i1"></i><b class="W50">3个月</b></label>
        <input type="radio" name="yxq" id="yxq_180" class="radioskin" value="180"<?php echo ($yxq == 180)?' checked':'';?>><label for="yxq_180" class="radioskin-label"><i class="i1"></i><b class="W50">6个月</b></label>
        <input type="radio" name="yxq" id="yxq_365" class="radioskin" value="365"<?php echo ($yxq == 365 || empty($yxq))?' checked':'';?>><label for="yxq_365" class="radioskin-label"><i class="i1"></i><b class="W50">1年</b></label>
        <input type="radio" name="yxq" id="yxq_730" class="radioskin" value="730"<?php echo ($yxq == 730)?' checked':'';?>><label for="yxq_730" class="radioskin-label"><i class="i1"></i><b class="W50">2年</b></label>
        <input type="radio" name="yxq" id="yxq_1095" class="radioskin" value="1095"<?php echo ($yxq == 1095)?' checked':'';?>><label for="yxq_1095" class="radioskin-label"><i class="i1"></i><b class="W50">3年</b></label>
    </td>
    </tr>
    
    <tr>
    <td class="tdL "><font class="Cf00">*</font>商品总数量</td>
    <td class="tdR"><input name="productmaxnum" id="productmaxnum" type="text" class="W100 FVerdana" maxlength="6" value="<?php echo $productmaxnum;?>"> 件　<span class="tips S12">填0不限，超过将无法发布新商品</span></td>
    </tr>
    
    <tr>
    <td class="tdL tdLbgHUI">每天提现最多金额限制</td>
    <td class="tdR"><input name="tx_daymax_price" id="tx_daymax_price" type="text" class="W100 FVerdana" maxlength="6" value="<?php echo $tx_daymax_price;?>"> 元　<span class="tips S12">填0不限，不要超过微信支付商户平台单日限额</span></td>
    </tr>
    <tr>
    <td class="tdL tdLbgHUI">提现扣除手续费比例</td>
    <td class="tdR"><input name="tx_sxf_bfb" id="tx_sxf_bfb" type="text" class="W100 FVerdana" maxlength="2" value="<?php echo $tx_sxf_bfb;?>"> %　<span class="tips S12">如：填20%，提现1000元，扣除200元，实际到账800；填0不收手续费，全额到账</span></td>
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
    <td valign="top" class="tdR lineH150"><textarea name="content" id="content" rows="5" class="W700 S14"><?php echo $content;?></textarea></td>
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
            <input name="oldshopgrade" type="hidden" value="<?php echo $shopgrade;?>">
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
	$rt = $db->query("SELECT * FROM ".__TBL_TG_ROLE__." WHERE shopgrade>=1 ORDER BY px DESC,shopgrade DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodataico'><i></i>暂无角色组<br><a class='btn size2 HUANG3' onClick=\"zeai.openurl('".SELF."?submitok=add')\">新增角色组</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="120" align="left"><button type="button" class="btn " onClick="zeai.openurl('<?php echo SELF;?>?submitok=add')"><i class="ico add">&#xe620;</i> 新增套餐</button></td>
		<td align="left"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">系统默认权重为1</font></td>
	  </tr>
	</table>
	<table class="tablelist">
	<tr>
        <th width="60" align="center">组ID</th>
      <th width="60" align="center">置顶</th>
        <th width="60" align="center">图标</th>
        <th width="150" align="center">角色组名称</th>
        <th width="60" align="center">等级</th>
        <th width="120" align="center">价格(元)/时长</th>
        <th width="60" align="center">店铺数量</th>
        <th align="center">&nbsp;</th>
      <th width="100">默认注册角色组</th>
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
		$shopgrade = intval($rows['shopgrade']);
		$logo  = $rows['logo'];
		$ifdefault = $rows['ifdefault'];
		$price  = floatval($rows['price']);
		$flag   = $rows['flag'];
		$logo_url = (!empty($logo))?$_ZEAI['up2'].'/'.$logo:HOST.'/res/noP.gif';
		$unum = $db->COUNT(__TBL_TG_USER__,"shopgrade=".$shopgrade);
		$yxq=intval($rows['yxq']);
		if($price<=0){
			$price_str='免费';
		}else{
			$price_str='<font class="Cf00">￥'.$price.'</font>';
		}
		$price_str.=' / '.yxq($yxq);
	?>
	<tr>
	<td width="60" height="60" align="center" class="S14"><?php echo $id;?></td>
    <td width="60" height="40" align="center"><a href="<?php echo "shop_role.php?id=".$id; ?>&submitok=ding" class="topico" title="置顶"></a></td>
	<td width="60" align="center"><img src="<?php echo $logo_url; ?>" class="m zoom" onClick="parent.piczoom('<?php echo $logo_url; ?>')"></td>
	<td width="150" align="center" class="S14"><?php echo $title;?></td>
	<td width="60" align="center" class="S14"><?php echo $shopgrade;?></td>
	<td width="120" align="center" class="S14"><?php echo $price_str;?></td>
	<td width="60" align="center" class="S14"><?php echo $unum;?></td>
	<td align="center">&nbsp;</td>
	<td width="100">
	  <?php if ($ifdefault == 1){?>
	  <a class="aQINGed not-allowed tips" title="<?php echo $title;?>" tips-title="默认第一次注册的角色组">默认组</a>
	  <?php }else{ ?>
	  <a tips-title="默认第一次注册的角色组" title="<?php echo $title;?>" value="<?php echo $id; ?>" class="aQING tips">设为默认组</a>
	  <?php }?>
</td>
	<td width="100" class="center"><input type="checkbox" id="flag<?php echo $id;?>" class="switch" value="<?php echo $flag;?>"<?php echo ($flag == 1)?' checked':'';?>><label value="<?php echo $id;?>" for="flag<?php echo $id;?>" class="switch-label"><i></i><b>启用</b><b>隐藏</b></label></td>
	<td width="50" class="center"><a value="<?php echo $id;?>" class="editico" title="修改<?php echo $title;?>" onClick="zeai.openurl('<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>')"></a></td>
	<td width="50" class="center"><a value="<?php echo $id; ?>" unum="<?php echo $unum;?>" class="delico" title="删除<?php echo $title;?>"></a></td>
	</tr>
	<?php } ?>
	<?php if ($total > $pagesize){?>
	<tfoot><tr>
	<td colspan="12" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
	</tr></tfoot>
	<?php } ?>
	</table>
	<?php } ?>
<?php } ?>
<script>
<?php if ($submitok == "add" || $submitok == "mod") {?>
	save.onclick = function(){
		zeai.ajax({url:'shop_role'+zeai.extname,form:o('ZEAIFORM')},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);
			if (rs.flag == 1){
				zeai.msg(rs.msg);
				setTimeout(function(){zeai.openurl('shop_role'+zeai.extname)},1000);
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
		var tips = (unum>0)?'当前角色组包含 '+unum+' 个商家，删除后这些商家将被降级为权重最低角色组。':'';
		obj.onclick = function(){
			zeai.confirm('<font color="red">请慎重！</font><br>'+tips+'真的要删除【'+title+'】么？',function(){
				zeai.ajax('shop_role'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	
	zeai.listEach('.aQING',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var title = obj.getAttribute("title");
		obj.onclick = function(){
			zeai.confirm('<font color="red">请慎重！</font><br>'+'真的要将【'+title+'】设为默认角色组么？',function(){
				zeai.ajax('shop_role'+zeai.ajxext+'submitok=ajax_defaultupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
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
			zeai.ajax('shop_role'+zeai.ajxext+'submitok=ajax_flagupdate&id='+id+'&flag='+flag,function(e){var rs=zeai.jsoneval(e);
				zeai.msg(rs.msg);
			});
		}
	});

<?php } ?>
</script>
<?php require_once 'bottomadm.php';?>


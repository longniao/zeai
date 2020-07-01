<?php
require_once '../sub/init.php';
$currfieldstg="areaid,areatitle,nickname,weixin,worktime,qhdz,qhbz,longitude,latitude,qhlongitude,qhlatitude";
require_once ZEAI.'m4/shop_chk_u.php';
if($rowtg['shopflag']==0 || $rowtg['shopflag']==-1 || $rowtg['shopflag']==2)header("Location: shop_my_flag.php");
if($submitok=='hideshop'){
	if($cook_tg_flag!=1 && $cook_tg_flag!=-2)json_exit(array('flag'=>0,'msg'=>'亲，当前状态不能设置'));
	if($cook_tg_flag==1){
		$db->query("UPDATE ".__TBL_TG_USER__." SET shopflag=-2 WHERE id=".$cook_tg_uid);
		json_exit(array('flag'=>1,'msg'=>$_SHOP['title'].'隐藏成功，'.$_SHOP['title'].'列表将不显示'));
	}else{
		$db->query("UPDATE ".__TBL_TG_USER__." SET shopflag=1 WHERE id=".$cook_tg_uid);
		json_exit(array('flag'=>1,'msg'=>$_SHOP['title'].'开启成功，'.$_SHOP['title'].'列表显示恢复正常'));
	}
}elseif($submitok=='shop_my_shop_adm1_update'){
	$areaid     = dataIO($areaid,'in',100);
	$areatitle  = dataIO($areatitle,'in',100);
	$db->query("UPDATE ".__TBL_TG_USER__." SET areaid='$areaid',areatitle='$areatitle' WHERE id=".$cook_tg_uid);
	json_exit(array('flag'=>1,'msg'=>'修改成功'));
}elseif($submitok=='shop_my_shop_adm2_update'){
	$nickname = dataIO($nickname,'in',50);
	$weixin   = dataIO($weixin,'in',50);
	$db->query("UPDATE ".__TBL_TG_USER__." SET nickname='$nickname',weixin='$weixin' WHERE id=".$cook_tg_uid);
	json_exit(array('flag'=>1,'msg'=>'修改成功'));
}elseif($submitok=='shop_my_shop_adm3_update'){
	$worktime = dataIO($worktime,'in',200);
	$db->query("UPDATE ".__TBL_TG_USER__." SET worktime='$worktime' WHERE id=".$cook_tg_uid);
	json_exit(array('flag'=>1,'msg'=>'修改成功'));
}elseif($submitok=='shop_my_shop_adm4_update'){
	$qhdz = dataIO($qhdz,'in',200);
	$qhbz = dataIO($qhbz,'in',200);
	$db->query("UPDATE ".__TBL_TG_USER__." SET qhdz='$qhdz',qhbz='$qhbz' WHERE id=".$cook_tg_uid);
	json_exit(array('flag'=>1,'msg'=>'修改成功'));
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的</title>
<head>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select3.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php 
$url=HOST.'/m4/shop_my.php';
$mini_title = '<i class="ico goback" onClick="zeai.openurl(\''.$url.'\');">&#xe602;</i>'.$_SHOP['title'].'设置';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
$cook_tg_areaid    = $rowtg['areaid'];
$cook_tg_areatitle = $rowtg['areatitle'];
$marr=explode(',',$cook_tg_areaid);$m1=$marr[0];$m2=$marr[1];$m3=$marr[2];
$cook_tg_nickname = dataIO($rowtg['nickname'],'out');
$cook_tg_weixin   = dataIO($rowtg['weixin'],'out');
$cook_tg_worktime = dataIO($rowtg['worktime'],'out');
$cook_tg_qhdz = dataIO($rowtg['qhdz'],'out');
$cook_tg_qhbz = dataIO($rowtg['qhbz'],'out');
$cook_tg_longitude = dataIO($rowtg['longitude'],'out');
$cook_tg_latitude  = dataIO($rowtg['latitude'],'out');
$cook_tg_lnglat_str= (!empty($cook_tg_longitude))?$cook_tg_longitude.','.$cook_tg_latitude:'';
$cook_tg_qhlongitude = dataIO($rowtg['qhlongitude'],'out');
$cook_tg_qhlatitude  = dataIO($rowtg['qhlatitude'],'out');
$cook_tg_qhlnglat_str= (!empty($cook_tg_qhlongitude))?$cook_tg_qhlongitude.','.$cook_tg_qhlatitude:'';
?>
<div class="modlist">
	<ul>
		<li class="tborder" id="shop_my_shop_adm1"><h4><?php echo $_SHOP['title'];?>所在地区</h4><span></span></li>
		<li id="shop_my_shop_adm6"><h4><?php echo $_SHOP['title'];?>定位</h4><span><i class="ico S16 C09f">&#xe614;</i><?php echo $cook_tg_lnglat_str;?></span></li>
		<li id="shop_my_shop_adm2"><h4>联系人姓名/微信号</h4><span></span></li>
		<li id="shop_my_shop_adm3"><h4>营业时间</h4><span></span></li>
		<li class="nogo"><h4><?php echo $_SHOP['title'];?>状态</h4><span><input type="checkbox" name="hideshop" id="hideshop" class="switch" <?php echo ($cook_tg_flag ==1)?' checked':'';?>><label for="hideshop" class="switch-label"><i></i><b>显示</b><b>隐藏</b></label></span></li>
	</ul>
    <ul>
		<li id="shop_my_shop_adm4" class="tborder"><h4>线下取货地点</h4><span></span></li>
		<li id="shop_my_shop_adm5"><h4>取货地点定位</h4><span><i class="ico S16 C09f">&#xe614;</i><?php echo $cook_tg_qhlnglat_str;?></span></li>
    </ul>
    <ul>
		<li id="shop_my_shop_adm7" class="tborder"><h4>更多<?php echo $_SHOP['title'];?>资料修改</h4><span></span></li>
    </ul>
</div>
<div id="shop_my_shop_adm1box" class="btmboxinput">
    <h1><?php echo $_SHOP['title'];?>所在地区</h1>
    <form id="Zeai__cn_form1">
        <dl><dt>选择地区</dt><dd><script>LevelMenu3('m1|m2|m3|'+nulltext+'|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>','class="select area SW"');</script></dl>
        <input name="areaid" id="areaid" type="hidden" value="<?php echo $cook_tg_areaid;?>" />
        <input name="areatitle" id="areatitle" type="hidden" value="<?php echo $cook_tg_areatitle;?>" />
        <input type="hidden" name="submitok" value="shop_my_shop_adm1_update">
		<button type="button" class="btn size4 HONG3 yuan" id="shop_my_shop_adm1box_btn">确定并保存</button>
	</form>
</div>
<div id="shop_my_shop_adm2box" class="btmboxinput">
    <h1>联系人姓名/微信号</h1>
    <form id="Zeai__cn_form2">
        <dl><dt>姓　名</dt><dd><input name="nickname" type="text" class="input " placeholder="请输入【联系人姓名】"  autocomplete="off" maxlength="20" value="<?php echo $cook_tg_nickname;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
        <dl><dt>微信号</dt><dd><input name="weixin" type="text" class="input " placeholder="请输入【微信号】"  autocomplete="off" maxlength="50" value="<?php echo $cook_tg_weixin;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
        <input type="hidden" name="submitok" value="shop_my_shop_adm2_update">
		<button type="button" class="btn size4 HONG3 yuan" id="shop_my_shop_adm2box_btn">确定并保存</button>
	</form>
</div>
<div id="shop_my_shop_adm3box" class="btmboxinput">
    <h1><?php echo $_SHOP['title'];?>营业时间</h1>
    <form id="Zeai__cn_form3">
        <dl><dt>营业时间</dt><dd><input name="worktime" type="text" class="input " placeholder="请输入【营业时间】"  autocomplete="off" maxlength="20" value="<?php echo $cook_tg_worktime;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
        <input type="hidden" name="submitok" value="shop_my_shop_adm3_update">
		<button type="button" class="btn size4 HONG3 yuan" id="shop_my_shop_adm3box_btn">确定并保存</button>
	</form>
</div>

<div id="shop_my_shop_adm4box" class="btmboxinput">
    <h1>到店取货设置</h1>
    <form id="Zeai__cn_form4">
        <dl><dt>取货地址</dt><dd><input name="qhdz" type="text" class="input " placeholder="请输入详细取货地址"  autocomplete="off" maxlength="20" value="<?php echo $cook_tg_qhdz;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
        <dl><dt>说明备注</dt><dd><input name="qhbz" type="text" class="input " placeholder="请输入取货时相关注意事项"  autocomplete="off" maxlength="50" value="<?php echo $cook_tg_qhbz;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
        <input type="hidden" name="submitok" value="shop_my_shop_adm4_update">
		<button type="button" class="btn size4 HONG3 yuan" id="shop_my_shop_adm4box_btn">确定并保存</button>
	</form>
</div>
<div id="shop_my_shop_adm6box" class="btmboxinput">
    <input type="hidden" name="longitude" id="longitude" value="<?php echo $cook_tg_longitude;?>">
    <input type="hidden" name="latitude" id="latitude" value="<?php echo $cook_tg_latitude;?>">
</div>
<div id="shop_my_shop_adm5box" class="btmboxinput">
    <input type="hidden" name="qhlongitude" id="qhlongitude" value="<?php echo $cook_tg_qhlongitude;?>">
    <input type="hidden" name="qhlatitude" id="qhlatitude" value="<?php echo $cook_tg_qhlatitude;?>">
</div>
<script>
shop_my_shop_adm1.onclick=function(){ZeaiM.div_up({obj:shop_my_shop_adm1box,h:220});};
shop_my_shop_adm1box_btn.onclick=function(){
	var m1 = get_option('m1','v'),m2 = get_option('m2','v'),m3 = get_option('m3','v');
	var m1t = get_option('m1','t'),m2t = get_option('m2','t'),m3t = get_option('m3','t');
	m1t = (nulltext == m1t)?'':m1t;
	m2t = (nulltext == m2t)?'':' '+m2t;
	m3t = (nulltext == m3t)?'':' '+m3t;
	m1 = (m1 == 0)?'':m1;
	m2 = (m2 == 0)?'':','+m2;
	m3 = (m3 == 0)?'':','+m3;
	var mate_areaid = m1 + m2 + m3;
	mate_areaid = (mate_areaid == '0,0,0')?'':mate_areaid;
	var mate_areatitle = m1t + m2t + m3t;
	o('areaid').value = mate_areaid;
	o('areatitle').value = mate_areatitle;	
	zeai.ajax({url:'shop_my_shop_adm'+zeai.extname,form:Zeai__cn_form1},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);if(rs.flag==1){setTimeout("div_up_close.click()",1000);}
	});
}
shop_my_shop_adm2.onclick=function(){ZeaiM.div_up({obj:shop_my_shop_adm2box,h:280});};
shop_my_shop_adm2box_btn.onclick=function(){
	zeai.ajax({url:'shop_my_shop_adm'+zeai.extname,form:Zeai__cn_form2},function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);if(rs.flag==1){setTimeout("div_up_close.click()",1000);}
	});
}
shop_my_shop_adm3.onclick=function(){ZeaiM.div_up({obj:shop_my_shop_adm3box,h:220});};
shop_my_shop_adm3box_btn.onclick=function(){
	zeai.ajax({url:'shop_my_shop_adm'+zeai.extname,form:Zeai__cn_form3},function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);if(rs.flag==1){setTimeout("div_up_close.click()",1000);}
	});
}
shop_my_shop_adm4.onclick=function(){ZeaiM.div_up({obj:shop_my_shop_adm4box,h:280});};
shop_my_shop_adm4box_btn.onclick=function(){
	zeai.ajax({url:'shop_my_shop_adm'+zeai.extname,form:Zeai__cn_form4},function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);if(rs.flag==1){setTimeout("div_up_close.click()",1000);}
	});
}
//hideshop
hideshop.onclick=function(){zeai.ajax('shop_my_shop_adm'+zeai.ajxext+'submitok=hideshop',function(e){var rs=zeai.jsoneval(e);zeai.msg(0);zeai.msg(rs.msg);});}

shop_my_shop_adm6.onclick=function(){
	var lng=longitude.value,lat=latitude.value;
	zeai.openurl('map_set.php?kind=shop_address&longitude='+lng+'&latitude='+lat+'&areatitle=<?php echo $cook_tg_areatitle; ?>');
}
shop_my_shop_adm5.onclick=function(){
	var lng=qhlongitude.value,lat=qhlatitude.value;
	zeai.openurl('map_set.php?kind=shop_qh&longitude='+lng+'&latitude='+lat+'&areatitle=<?php echo $cook_tg_areatitle; ?>');
}
shop_my_shop_adm7.onclick=function(){
	zeai.openurl('shop_my_apply.php');
}
</script>
</body>
</html>
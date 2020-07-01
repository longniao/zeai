<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
$longitude = (empty($longitude))?0:$longitude;
$latitude  = (empty($latitude))?0:$latitude;
$areatitle = trimhtml($areatitle);
switch ($submitok) {
	case 'shop_qh_update':
		if(ifint($cook_tg_uid)){
			$qhlongitude = trimhtml(dataIO($lng,'in',20));
			$qhlatitude  = trimhtml(dataIO($lat,'in',20));
			$db->query("UPDATE ".__TBL_TG_USER__." SET qhlongitude='$qhlongitude',qhlatitude='$qhlatitude' WHERE id=".$cook_tg_uid);
			json_exit(array('flag'=>1,'msg'=>'标注成功','url'=>'shop_my_shop_adm.php'));
		}
	break;
	case 'shop_address_update':
		if(ifint($cook_tg_uid)){
			$longitude = trimhtml(dataIO($lng,'in',20));
			$latitude  = trimhtml(dataIO($lat,'in',20));
			$db->query("UPDATE ".__TBL_TG_USER__." SET longitude='$longitude',latitude='$latitude' WHERE id=".$cook_tg_uid);
			json_exit(array('flag'=>1,'msg'=>'标注成功','url'=>'shop_my_shop_adm.php'));
		}
	break;
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>标注位置</title>
<head>
<?php echo HEADMETA;?>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style type="text/css">
.maptop{width:100%;position:absolute;top:55px;left:0;text-align:left;color:#999}
.maptop .sobox{width:90%;height:40px;line-height:40px;margin:0 auto;position:relative;border:#dedede 1px solid;border-radius:3px}
.maptop .sobox .input{width:-webkit-calc(100% - 40px);line-height:30px;height:30px;margin-top:5px;text-indent:8px;float:left;border:0}
.maptop .sobox .so{border:0;color:#999;background-color:#fff;width:40px;height:30px;line-height:30px;margin-top:5px;display:inline-block;float:right;border-left:#eee 1px solid}
.maptop .sobox .so i{font-size:22px}
.maptop span{display:inline-block;position:absolute;left:30px;top:45px;font-size:14px}
.maptop input.tips{border:0;line-height:40px;width:80px;display:inline;color:#666;background-color:#fff;font-size:14px}
#zeai_map {width:100%;height:-webkit-calc(100% - 150px);overflow:hidden;position:absolute;bottom:0;left:0;z-index:1}
.HONG3{position:fixed;bottom:20px;width:70%;left:15%;z-index:2}
</style>
</head>
<body>
<?php
$mini_title = '<i class="ico goback" onClick="zeai.back();">&#xe602;</i>标注位置';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<div class="maptop">
	<form id="www_zeai_cn__RORM">
    	<div class="sobox">
            <input id="keyword" class="input" type="text" placeholder="搜索地点/城市/街道/小区等" />
            <button type="button" onClick="searchMap()" class="so" title="开始搜索" /><i class="ico">&#xe6c4;</i></button>
        </div>
        <span>经度：<input type="text" id="lng" name="lng" value="<?php echo $longitude;?>" class="tips" readonly />　纬度：<input type="text" id="lat" name="lat" class="tips" value="<?php echo $latitude;?>" readonly /></span>
        <input type="hidden" id="areatitle" value="<?php echo $areatitle;?>">
        <input type="hidden" value="<?php echo $kind;?>_update" name="submitok">
    </form>
</div>
<button id="zeai_map_save_btn" class="btn size4 HONG3 yuan">确定并保存</button>
<div id="zeai_map" style="width:100%"></div>
<script src="http://api.map.baidu.com/api?v=2.0&ak=d9jat2VxBFnIEWKGs2NaxAowYXDvlzal"></script>
<script src="<?php echo HOST;?>/res/m4/js/zeai_map.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
zeai.alert('拖动地图上红色的【标注】<i class="ico Cf00">&#xe614;</i> 图标定位您所在区域，点底部【确定并保存】按钮即可；也可以搜索你的地点');
zeai_map_save_btn.onclick=function(){
	Zeai_map_save(function(rs){
		zeai.ajax({url:'map_set'+zeai.extname,form:www_zeai_cn__RORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){
				zeai.openurl(rs.url);
			},1000);}
		});
	});
}
</script>
</body>
</html>
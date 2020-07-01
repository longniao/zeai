<?php
require_once '../sub/init.php';
$longitude = (empty($longitude))?0:$longitude;
$latitude  = (empty($latitude))?0:$latitude;
$areatitle = trimhtml($areatitle);
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $areatitle;?></title>
<head>
<?php echo HEADMETA;?>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style type="text/css">
.maptop{display:none}
#zeai_map {width:100%;height:-webkit-calc(100% - 44px);overflow:hidden;position:absolute;bottom:0;left:0;z-index:1}
</style>
</head>
<body>
<?php
$mini_title = '<i class="ico goback" onClick="zeai.back();">&#xe602;</i>';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<div class="maptop">
<form id="www_zeai_cn__RORM">
<input id="keyword" class="input" type="text" placeholder="搜索地点/城市/街道/小区等" />
<input type="text" id="lng" name="lng" value="<?php echo $longitude;?>" class="tips" readonly />
<input type="text" id="lat" name="lat" class="tips" value="<?php echo $latitude;?>" readonly />
<input type="hidden" id="areatitle" value="<?php echo $areatitle;?>">
</form>
</div>
<div id="zeai_map" style="width:100%"></div>
<script src="http://api.map.baidu.com/api?v=2.0&ak=d9jat2VxBFnIEWKGs2NaxAowYXDvlzal"></script>
<script src="<?php echo HOST;?>/res/m4/js/zeai_map_show.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</body>
</html>
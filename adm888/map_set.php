<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
$longitude = (empty($longitude))?0:$longitude;
$latitude  = (empty($latitude))?0:$latitude;
$areatitle = $areatitle;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style type="text/css">
	body,html {width:100%;height:100%;overflow:hidden;background-color:#fff}
	#zeai_map {height:400px;width:590px;overflow:hidden;position: absolute;bottom:20px;left:20px;}
	.maptop{width:100%;position:absolute;top:20px;left:20px;text-align:left;color:#999}
	.maptop .input{width:260px;line-height:40px;height:40px;text-indent:8px;border-color:#dedede}
	.so{border:0;border-left:#eee 1px solid;position:absolute;left:218px;top:3px;color:#999;background-color:#fff;width:40px;height:32px;line-height:32px;display:inline-block;border-radius:3px}
	.so:hover{color:#E83191}
	.so i{font-size:22px}
	.maptop span{display:inline-block;position:absolute;right:30px;top:10px}
	.maptop input.tips{border:0;line-height:40px;width:80px;display:inline;color:#666;background-color:#f5f5f5}
</style>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<body>
<div class="maptop">
	<form id="www_zeai_cn__RORM">
	<input id="keyword" class="input" type="text" placeholder="搜索地点/城市/街道/小区等" />
	<button type="button" onClick="searchMap()" class="so" title="开始搜索" /><i class="ico">&#xe6c4;</i></button>
	<button onClick="Zeai_map_save()" class="btn size3 HUANG3 tipss" tips-title="拖动地图上红色的【标注】图标定位您所在区域，点【确定】按钮即可；如果不对可以搜索你的地点" tips-direction='bottom'>确定</button>
    <span>经度：<input type="text" id="lng" name="lng" value="<?php echo $longitude;?>" class="tips" readonly />　纬度：<input type="text" id="lat" name="lat" class="tips" value="<?php echo $latitude;?>" readonly /></span>
    <input type="hidden" id="areatitle" value="<?php echo $areatitle;?>">
    </form>
</div>
<div id="zeai_map"></div>
<script>
var var1='<?php echo $var1;?>',var2='<?php echo $var2;?>';
</script>
<script src="http://api.map.baidu.com/api?v=2.0&ak=d9jat2VxBFnIEWKGs2NaxAowYXDvlzal"></script>
<script src="js/zeai_map.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</div>
<?php require_once 'bottomadm.php';?>
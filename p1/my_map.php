<?php
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
if(!iflogin() || !ifint($cook_uid))exit();
if($submitok=='ajax_mapset'){
	$db->query("UPDATE ".__TBL_USER__." SET longitude='$lng',latitude='$lat' WHERE id=".$cook_uid);
	json_exit(array('flag'=>1,'msg'=>'位置标注成功'));	
}
$row = $db->NAME($cook_uid,"longitude,latitude,areatitle");
$cook_longitude = (empty($row['longitude']))?0:$row['longitude'];
$cook_latitude  = (empty($row['latitude']))?0:$row['latitude'];
$cook_areatitle = $row['areatitle'];
?>
<!doctype html><html><head><meta charset="utf-8">
<title></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<style type="text/css">
	body,html {width:100%;height:100%;overflow:hidden;background-color:#fff}
	#zeai_map {height:400px;width:590px;overflow:hidden;position: absolute;bottom:20px;left:20px;}
	.maptop{width:100%;position:absolute;top:20px;left:20px;text-align:left;color:#999}
	.maptop .input{width:260px;line-height:40px;height:40px;text-indent:8px;border-color:#dedede}
	.so{border:0;border-left:#eee 1px solid;position:absolute;left:218px;top:3px;color:#999;background-color:#fff;width:40px;height:32px;line-height:32px;display:inline-block;border-radius:3px}
	.so:hover{color:#E83191}
	.so i{font-size:22px}
	.maptop span{display:inline-block;position:absolute;right:20px;top:0px}
	.maptop input.tips{border:0;line-height:40px;width:80px;display:inline;color:#666}
</style>
</head>
<body>
<div class="maptop">
	<form id="www_zeai_cn__RORM">
	<input id="keyword" class="input" type="text" placeholder="搜索地点/城市/街道/小区等" />
	<button type="button" onClick="searchMap()" class="so" title="开始搜索" /><i class="ico">&#xe6c4;</i></button>
	<button onClick="Zeai_map_save()" class="btn size3 HONG tipss" tips-title="拖动地图上红色的【标注】图标定位您所在区域，点【确定】按钮即可；如果不对可以搜索你的地点" tips-direction='bottom'>确定</button>
    <span>经度：<input type="text" id="lng" name="lng" value="<?php echo $cook_longitude;?>" class="tips" readonly />纬度：<input type="text" id="lat" name="lat" class="tips" value="<?php echo $cook_latitude;?>" readonly /></span>
    <input type="hidden" id="areatitle" value="<?php echo $cook_areatitle;?>">
    </form>
</div>
<div id="zeai_map"></div>
<script src="http://api.map.baidu.com/api?v=2.0&ak=d9jat2VxBFnIEWKGs2NaxAowYXDvlzal"></script>
<script src="js/zeai_map.js"></script>
</div>
</body>
</html>
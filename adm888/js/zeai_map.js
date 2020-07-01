/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2020/04/15 by supdes
*/
var ZEAI_LNG = lng.value,ZEAI_LAT=lat.value;
var map = new BMap.Map("zeai_map", {minZoom:5,maxZoom:16});//缩放级别
map.centerAndZoom(new BMap.Point(ZEAI_LNG,ZEAI_LAT),16);// 初始化
map.addControl(new BMap.OverviewMapControl()); //添加缩略地图控件
map.enableScrollWheelZoom(true);// 开启鼠标滚轮缩放功能，仅对PC上有效
map.addControl(new BMap.NavigationControl());//将控件（平移缩放控件）添加到地图上
map.addControl(new BMap.ScaleControl()); //添加比例尺控件
map.addControl(new BMap.MapTypeControl()); //添加地图类型控件放按	
//点击标注跟随
map.addEventListener("click", function(e){
	map.clearOverlays();//清空标注
	map_marker = new BMap.Marker(new BMap.Point(e.point.lng, e.point.lat));//重构
	map_marker.enableDragging();
	map.addOverlay(map_marker);
	setlnglat(e.point.lng,e.point.lat)
	map_marker.addEventListener("dragend",function(e){setlnglat(e.point.lng,e.point.lat);});//拖动
});
function searchMap() {
	var keyword = o('keyword').value;
	if (keyword.length > 0) {
		map.clearOverlays();//清空标注
		//获取默认
		function getBDFirstMarker(){
			if(zeai.empty(local.getResults().getPoi(0))){
				zeai.msg('当前选择的城市没查到此关键词');
			}else{
				var one = local.getResults().getPoi(0).point;//第一个结果
				map.centerAndZoom(one,16);
				map_marker = new BMap.Marker(new BMap.Point(one.lng,one.lat));
				map_marker.enableDragging();
				map.addOverlay(map_marker);
				setlnglat(one.lng,one.lat)
				map_marker.addEventListener("dragend",function(e){setlnglat(e.point.lng,e.point.lat);});
			}
		}
		var local = new BMap.LocalSearch(map,{onSearchComplete:getBDFirstMarker});
		local.search(keyword);
	}
}
//显示标注
var map_marker = new BMap.Marker(new BMap.Point(ZEAI_LNG,ZEAI_LAT));
map_marker.enableDragging();
map.addOverlay(map_marker);
map_marker.addEventListener("dragend",function(e){setlnglat(e.point.lng,e.point.lat);});
if (map_marker) {//如果存在标注
	map.addOverlay(map_marker);
	map_marker.enableDragging();
	map_marker.addEventListener("dragend",function(e){setlnglat(e.point.lng,e.point.lat);});
}
function setlnglat(lngv,latv) {
	o('lng').value=lngv;//o('lng').html(lngv);
	o('lat').value=latv;//o('lat').html(latv);
}
document.onkeydown = function (e) {
	var theEvent = window.event || e;
	var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
	if (code == 13 && !zeai.empty(o('keyword').value))searchMap();
}
zeai.listEach('.tipss',function(obj){
	var tv = obj.getAttribute("tips-title"),td = obj.getAttribute("tips-direction");
	if (!zeai.empty(tv))zeai.tips(tv,obj,{time:30,direction:td,color:'#333'})
});
function Zeai_map_save(){
	if(zeai.empty(o(lng).value)){
		zeai.msg('请设置您的位置，也可以搜索你的城市缩小范围');
	}else{
		var longitude = window.parent.document.getElementById(var1);
		var latitude  = window.parent.document.getElementById(var2);
		longitude.value=lng.value;
		latitude.value =lat.value;
		window.parent.zeai.iframe(0);
	}
}
if(!zeai.empty(o('areatitle').value) && zeai.empty(o('lng').value)){
	o('keyword').value = o('areatitle').value;
	setTimeout(function(){searchMap();},1000);
}
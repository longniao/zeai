<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
if($rowtg['shopflag']==0 || $rowtg['shopflag']==-1 || $rowtg['shopflag']==2)header("Location: shop_my_flag.php");
$t = (ifint($t,'1-2','1'))?$t:1;
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
if($submitok=='ajax_yuyue_adm'){
	$id=intval($id);
	$db->query("UPDATE ".__TBL_SHOP_YUYUE__." SET flag=1 WHERE flag=0 AND cid=".$cook_tg_uid." AND id=".$id);
	json_exit(array('flag'=>1,'msg'=>'处理成功'));
}elseif($submitok=='ajax_getyuyue_show'){
	if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
	$row = $db->ROW(__TBL_SHOP_YUYUE__,"truename,mob,ddtime,bz","id=".$id,"name");
	if ($row){
		$truename = trimhtml(dataIO($row['truename'],'out'));
		$mob      = trimhtml(dataIO($row['mob'],'out'));
		$bz       = trimhtml(dataIO($row['bz'],'out'));
		$ddtime   = trimhtml(dataIO($row['ddtime'],'out'));
		json_exit(array('flag'=>1,'truename'=>$truename,'mob'=>$mob,'ddtime'=>$ddtime,'bz'=>$bz));
	}else{
		json_exit(array('flag'=>0,'msg'=>'亲，参数好像跑路了'));
	}
}
function rows_ulist($rows,$p) {
	global $_ZEAI,$t;
	$O = '';
	$id     = $rows['id'];
	$flag   = $rows['flag'];
	$tg_uid = $rows['tg_uid'];
	$truename = trimhtml(dataIO($rows['truename'],'out'));
	$mob      = trimhtml(dataIO($rows['mob'],'out'));
	$bz       = trimhtml(dataIO($rows['bz'],'out'));
	$ddtime   = trimhtml(dataIO($rows['ddtime'],'out'));
	$ddtime_str=(!empty($ddtime))?'（'.$ddtime.'）':'';
	if($flag==1){
		$flag_str = '<font class="C090">已处理</font>';
		$new_str  = '';
	}else{
		$flag_str = '<button class="btn size2 HONG" onClick="yuyue_admFn('.$id.')">点击处理</button>';
		$new_str  = '<b></b>';
	}
	$addtime_str = YmdHis($rows['addtime'],'YmdHi');
	$img_str     = '<i class="ico2">&#xe626;</i>';
	$O .= '<dl>';
	$O .= '<dt tid="'.$id.'" onClick="shop_my_yuyue_showFn('.$id.')">'.$img_str.$new_str.'</dt>';
	$O .= '<dd onClick="shop_my_yuyue_showFn('.$id.')"><h4>'.$truename.'<div class="id">（ID：'.$tg_uid.'）</div></h4><h5>'.$mob.'</h5><h5>'.$bz.$ddtime_str.'</h5></dd>';
	$O .= '<span>'.$addtime_str.'</span>';
	$O .= $flag_str;
	$O .= '</dl>';
	return $O;
}
$_ZEAI['pagesize'] = 10;
if($t ==1){
	$ZEAI_SQL   = "cid=".$cook_tg_uid;
	$ZEAI_SELECT="SELECT id,addtime,truename,mob,flag,tg_uid,bz,ddtime FROM ".__TBL_SHOP_YUYUE__." WHERE ".$ZEAI_SQL." ORDER BY flag,id DESC";
	$ZEAI_total_SQL = "SELECT COUNT(*) FROM ".__TBL_SHOP_YUYUE__." WHERE ".$ZEAI_SQL;
}
if($submitok=='ZEAI_list'){exit(Zeai_ajax_list_fn($ZEAI_totalP,$p));}
$ZEAI_total = $db->query($ZEAI_total_SQL);
$ZEAI_total = $db->fetch_array($ZEAI_total);
$ZEAI_total = $ZEAI_total[0];
$ZEAI_totalP= ceil($ZEAI_total/$_ZEAI['pagesize']);?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php 
$mini_title = '<i class="ico goback" onClick="zeai.back();">&#xe602;</i>预约管理';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';?>
<div class="shop_yuyue_adm" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo Zeai_ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div>
<style>
.shop_my_yuyue_show{display:none;height:100%;height:-webkit-calc(100% - 15px);position:relative}
.shop_my_yuyue_show h1{height:40px;line-height:24px;font-size:17px;color:#333;margin:15px 0 20px;font-weight:bold;border-bottom:#f5f5f5 1px solid}
.shop_my_yuyue_show li{width:90%;margin:10px auto;height:34px;line-height:34px;font-size:15px;text-align:left}
.shop_my_yuyue_show li .ico2{display:inline-block;width:34px;height:34px;line-height:34px;border-radius:20px;font-size:20px;color:#fff;margin-right:7px;text-align:center;vertical-align:middle}
.shop_my_yuyue_show li .i1{background-color:#39F}
.shop_my_yuyue_show li .i2{background-color:#39F;font-size:18px}
.shop_my_yuyue_show li .i3{background-color:#39F}
.shop_my_yuyue_show li span{line-height:150%;display:inline-block;width:80%;vertical-align:middle}
</style>
<div id="shop_my_yuyue_showBox" class="shop_my_yuyue_show" style="display:none">
	<h1><b id="truename">--</b></h1>
    <li><i class="ico2 i2">&#xe7c1;</i><span id="mob">--</span></li>
    <li><i class="ico2 i3">&#xe61e;</i><span id="ddtime">--</span></li>
    <li><i class="ico2 i1">&#xe787;</i><span id="bz">--</span></li>
</div>
<script>
<?php
if ($ZEAI_total > $_ZEAI['pagesize'] && $t != 2){?>
var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2;
zeaiOnscroll_json={url:'shop_my_fav'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,t:<?php echo $t;?>}};
document.body.onscroll = zeaiOnscroll;
<?php }?>
function yuyue_admFn(id){
	ZeaiM.confirmUp({title:'确定已经联系处理了么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_yuyue_adm'+zeai.extname,data:{id:id,submitok:'ajax_yuyue_adm'}},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	}});
}
function shop_my_yuyue_showFn(id){
	zeai.ajax({url:'shop_my_yuyue_adm'+zeai.ajxext+'submitok=ajax_getyuyue_show&id='+id},function(e){var rs=zeai.jsoneval(e);
		if(rs.flag==1){
			ZeaiM.div({obj:shop_my_yuyue_showBox,w:310,h:320});
			setTimeout(function(){
			o('truename').html(rs.truename);
			o('bz').html(rs.bz);
			o('ddtime').html(rs.ddtime);
			o('mob').html('<a href="tel:'+rs.mob+'">'+rs.mob+'</a>');
			},300);}
	});
}
</script>
</body>
</html>
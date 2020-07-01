<?php require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_shop.php';
if(!in_array('shop',$QXARR))exit(noauth());
if($submitok=='ajax_reg_day'){
	$rt=$db->query("SELECT COUNT(id) AS num,from_unixtime(regtime,'%Y-%m-%d') AS day FROM ".__TBL_TG_USER__." WHERE ifshop=1 GROUP BY day ORDER BY day DESC LIMIT 30");
	$total = $db->num_rows($rt);
	if ($total <= 0) {
		exit('');
	} else {
		while($tmprows = $db->fetch_array($rt,'name')){$arr[]=$tmprows;}
		$arrnew = array_reverse($arr);
		foreach ($arrnew as $rows) {
			$day = $rows['day'];
			$num = $rows['num'];
			$arr[]=array("'".$day."'".",".$num);
		}
		exit(encode_json($arr));
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/highcharts.js?<?php echo $_ZEAI['cache_str'];?>"></script> 
<style type="text/css">
h5,h6,b{font-style:normal;font-weight:normal;margin:0px;padding:0px;display:block}
.box{width:100%;min-height:181px;background-color:#fff;border:#eee 1px solid;text-align:left;box-sizing:border-box;padding-bottom:15px;clear:both}
.box h5{width:100%;font-size:15px;border-bottom:#f5f5f5 1px solid;line-height:50px;margin:0 0 10px 0;padding:0 15px;display:block;position:relative;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.box span.line{width:20px;border-bottom:#009688 2px solid;position:absolute;top:50px;left:35px}
.box ul {width:100%;padding:0 10px;box-sizing:border-box;clear:both;overflow:auto} 
.box ul a{width:-webkit-calc(33% - 20px);padding:13px 15px 15px 15px;margin:10px;height:80px;float:left;background-color:#f8f8f8;border-radius:2px;float:left;box-sizing:border-box;-webkit-transition:all .5s;-moz-transition:all .5s;transition:all .5s;position:relative}
.box ul.cols3 a:nth-child(3n){margin-right:0;width:-webkit-calc(34% - 20px);}
.box ul a:hover{background-color:#f2f2f2}
.box ul.cols4 a{width:-webkit-calc(25% - 20px);}
.box ul.cols2 a{width:-webkit-calc(50% - 20px);}
.box ul a h6{color:#999;line-height:12px;display:inline-block}
.box ul a b{line-height:50px;color:#009688;font-size:30px;font-family:Arial, Helvetica, sans-serif;display:block}
.box ul a b font{font-size:14px}
.box ul a span{position:absolute;right:16px;top:45px;color:#F7564D;font-size:14px}
</style>
</head>
<body>
<div class="navbox">
	<a href="#" class="ed"><?php echo $_SHOP['title'];?>数据统计</a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<table border="0" cellpadding="8" cellspacing="0" style="width:1150px;margin:10px">
  <tr>
    <td width="50%" valign="top">
        <div class="box">
          <h5><?php echo $_SHOP['title'];?>总览<span class="line"></span></h5>
            <ul class="cols3">
                <a href="TG_u.php?k=2"><h6>店铺总数（个）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"shopflag<>2");?></b><span>今日+<?php echo $db->COUNT(__TBL_TG_USER__," (TO_DAYS(NOW()) - TO_DAYS(from_unixtime(regtime,'%Y-%m-%d')))=0 AND shopflag<>2  ");?></span></a>
                <a href="TG_u.php?f=f1&k=2"><h6>正常（个）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"shopflag=1");?></b></a>
                <a href="TG_u.php?f=f0&k=2"><h6>待审（个）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"shopflag=0");?></b></a>
                <a href="TG_u.php?f=f_2&k=2"><h6>隐藏（个）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"shopflag=-2");?></b></a>
                <a href="TG_u.php?f=f_1&k=2"><h6>已锁定（个）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"shopflag=-1");?></b></a>
                <a href="shop_u.php" title="买家：推广和<?php echo $_SHOP['title'];?>帐号未激活的用户"><h6>买家用户（个）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"shopflag=2 AND flag=2");?></b></a>
            </ul>
        </div>
        <br><div class="clear"></div>
    	<div class="box">
          <h5><?php echo $_SHOP['title'];?>等级套餐<span class="line"></span></h5>
          <ul class="cols3">
			<?php
			$rt2=$db->query("SELECT shopgrade,title FROM ".__TBL_TG_ROLE__." WHERE grade=0 ORDER BY px DESC,id DESC");
			$total2 = $db->num_rows($rt2);
			if ($total2 > 0) {
				for($j=0;$j<$total2;$j++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$shopgrade=intval($rows2[0]);$title=dataIO($rows2[1],'out');?>
					<a href="TG_u.php?g=<?php echo $shopgrade;?>&k=2"><h6><?php echo $title;?>（个）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"shopflag<>2 AND shopgrade=".$shopgrade);?></b></a>
					<?php
				}
			}
			?>
                
            </ul>
        </div>
        <br><div class="clear"></div>
    	<div class="box">
          <h5>商品总览<span class="line"></span></h5>
          <ul class="cols2">
    		<a href="TG_product.php"><h6>商品总数（件）</h6><b><?php echo $db->COUNT(__TBL_TG_PRODUCT__);?></b></a>
    		<a href="TG_product.php?flag=-1"><h6>锁定/删除（件）</h6><b><?php echo $db->COUNT(__TBL_TG_PRODUCT__,"flag=-1");?></b></a>
    		<a href="TG_product.php?flag=2"><h6>下架（件）</h6><b><?php echo $db->COUNT(__TBL_TG_PRODUCT__,"flag=2");?></b></a>
    		<a href="TG_product.php?stock=1"><h6>库存不足（件）</h6><b><?php echo $db->COUNT(__TBL_TG_PRODUCT__,"stock<=0");?></b></a>
            </ul>
        </div>
        <div class="clear"></div>
    </td>
    <td width="50%" valign="top">
        <div class="box">
          <h5>订单总览<span class="line"></span></h5>
            <ul class="cols2">
                <a href="shop_order.php"><h6>订单总数（笔）</h6><b><?php echo $db->COUNT(__TBL_SHOP_ORDER__);?></b><span>今日+<?php echo $db->COUNT(__TBL_SHOP_ORDER__," (TO_DAYS(NOW()) - TO_DAYS(from_unixtime(addtime,'%Y-%m-%d')))=0");?></span></a>
                
				<?php
					$orderflag=json_decode($_SHOP['orderflag'],true);
					$orderflagnum=count($orderflag);
                    for($i=0;$i<$orderflagnum;$i++) {?>
					<a href="shop_order.php?f=f<?php echo $i;?>"><h6><?php echo arrT($_SHOP['orderflag'],$i,'v2');?>（笔）</h6><b><?php echo $db->COUNT(__TBL_SHOP_ORDER__,"flag=".$i);?></b></a>
                	<?php
				    }
                ?>
            </ul>
        </div>
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
    	<div class="box">
          <h5><?php echo $_SHOP['title'];?>入驻增长<span class="line"></span></h5>
            <div id="tguser" style="width:95%; height:200px;margin:10px auto 0 auto"></div>
        </div>  
    </td>
  </tr>


</table>
<br><br><br>
<script>
window.onload=function(){
	function reg(arr){
		Highcharts.chart('tguser', {
			chart: {type: 'column'},
			title: {text: '　'},
			subtitle: {text: ''},
			credits: {"enabled": false},
			xAxis: {
				type: 'category',
				labels: {
					rotation: -45,
					style: {fontSize: '12px',fontFamily: 'Verdana, sans-serif'}
				}
			},
			yAxis: {min: 0,title: {text:'最近30天<?php echo $_SHOP['title'];?>入驻量（个）'}},
			legend: {enabled: false},
			tooltip: {pointFormat: '<b style="font-weight:normal;color:#666">入驻{point.y:.0f}个</b>'},
			colors: ["#F7564D"],
			series: [{
				name: 'zea.cn',
				data: arr,
				dataLabels: {
					enabled: true,
					rotation:0,
					///color: '#ffffff',
					align: 'center',
					format: '{point.y:.0f}', // one decimal
					y:0, //10 pixels down from the top
					style:{fontSize:'12px',fontWeight:'normal',fontFamily:'Arial,sans-serif'}
				}
			}]
		});
	}
	setTimeout(function(){
		zeai.ajax({url:'shop_welcome'+zeai.ajxext+'submitok=ajax_reg_day'},function(e){
			if(!zeai.empty(e)){
				var arr=e;
				arr = arr.replace(/"/g,"");
				arr = zeai.jsoneval(arr);
				reg(arr);
			}
		});
	},200);
}
</script>
</body>
</html>
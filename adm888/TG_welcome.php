<?php require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('u_tg',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
if($submitok=='ajax_reg_day'){
	$rt=$db->query("SELECT COUNT(id) AS num,from_unixtime(regtime,'%Y-%m-%d') AS day FROM ".__TBL_USER__." WHERE kind<>4 AND tguid>0 GROUP BY day ORDER BY day DESC LIMIT 30");
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
.box ul a span{position:absolute;right:16px;top:45px;color:#f60;font-size:14px}
</style>
</head>
<body>
<div class="navbox">
	<a href="#" class="ed">拓客数据统计</a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<table border="0" cellpadding="8" cellspacing="0" style="width:1150px;margin:10px">
  <tr>
    <td width="50%" valign="top">
    	<?php 
		$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=-1 AND tg_uid>0 AND flag=0");
		$row=$db->fetch_array($rt,'name');
		$tx =intval($row['paymoney']);
		?>
        <div class="box">
          <h5>推广审核<span class="line"></span></h5>
            <ul class="cols2">
                <a href="TG_u.php?f=f0&k=1"><h6>待审推广<?php echo $TG_set['tgytitle'];?>（人）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__," flag=0");?></b></a>
                <a href="u_tg.php"><h6>待审推广单身提成（人）</h6><b><?php echo $db->COUNT(__TBL_USER__,"tgflag=0 AND tguid>0");?></b></a>
                <a href="tg_tg.php"><h6>待审推广合伙人提成（人）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"tgflag=0 AND tguid>0");?></b></a>
                <a href="withdraw.php?t=tg"><h6>待审提现（元）</h6><b><font>￥</font><?php echo $tx;?></b></a>
            </ul>
        </div>
        
    </td>
    <td valign="top">
        
        <?php 
        $total_today=$db->COUNT(__TBL_USER__," tguid>0 AND (TO_DAYS(NOW()) - TO_DAYS(from_unixtime(regtime,'%Y-%m-%d')))=0  ");
        ?>
    	<div class="box">
          <h5>单身用户统计 <font class="C999">(有推荐人)</font><span class="line"></span></h5>
          <ul class="cols2">
                <a><h6>总数（人）</h6><b id="TG1"><?php echo $db->COUNT(__TBL_USER__,"tguid>0");?></b><span>+<?php echo $total_today;?> 今日新增</span></a>
                <a><h6>男（人）</h6><b id="TG2"><?php echo $db->COUNT(__TBL_USER__,"tguid>0 AND sex=1");?></b></a>
                <a><h6>VIP用户（人）</h6><b id="TG2"><?php echo $db->COUNT(__TBL_USER__,"tguid>0 AND grade>1");?></b></a>
                <a><h6>女（人）</h6><b id="TG2"><?php echo $db->COUNT(__TBL_USER__,"tguid>0 AND sex=2");?></b></a>
            </ul>
        </div>  
    </td>
  </tr>
  <tr>
    <td colspan="2">
    	<div class="box">
          <h5>单身用户注册增长 <font class="C999">(有推荐人)</font><span class="line"></span></h5>
            <div id="tguser" style="width:95%; height:250px;margin:10px auto 0 auto"></div>
        </div>  
    </td>
  </tr>

  <tr>
    <td>
    <div class="box" >
		<?php 
            $rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind>0 AND flag=1");
            $row=$db->fetch_array($rt,'name');
            $paymoney1=intval($row['paymoney']);
			
            $rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=5 AND flag=1");
            $row=$db->fetch_array($rt,'name');
            $paymoney2=intval($row['paymoney']);
			
            $rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=6 AND flag=1");
            $row=$db->fetch_array($rt,'name');
            $paymoney3=intval($row['paymoney']);
			
            $rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=1 AND flag=1");
            $row=$db->fetch_array($rt,'name');
            $paymoney4=intval($row['paymoney']);
			
            $rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=2 AND flag=1");
            $row=$db->fetch_array($rt,'name');
            $paymoney5=intval($row['paymoney']);
			
            $rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=3 AND flag=1");
            $row=$db->fetch_array($rt,'name');
            $paymoney6=intval($row['paymoney']);
			
        ?>
        <h5>收益统计<span class="line"></span></h5>
        <ul class="cols2">
			<?php 
            $rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind>0 AND flag=1 AND (TO_DAYS(NOW()) - TO_DAYS(from_unixtime(addtime,'%Y-%m-%d')))=0 ");
            $row=$db->fetch_array($rt,'name');
            $paymoney1_totay=intval($row['paymoney']);
            ?>
            <a><h6>累计总收益（元）</h6><b><font>￥</font><?php echo $paymoney1;?></b><span>+<?php echo $paymoney1_totay;?> 今日新增</span></a>
            <a href="pay.php?pkind=5"><h6>推广员升级（元）</h6><b><font>￥</font><?php echo $paymoney2;?></b></a>
            <a href="pay.php?pkind=6"><h6>推广员激活充值（元）</h6><b><font>￥</font><?php echo $paymoney3;?></b></a>
            <a href="pay.php?pkind=1"><h6>用户VIP升级（元）</h6><b><font>￥</font><?php echo $paymoney4;?></b></a>
            <a href="pay.php?pkind=2"><h6>用户<?php echo $_ZEAI['loveB'];?>充值（元）</h6><b><font>￥</font><?php echo $paymoney5;?></b></a>
            <a href="pay.php?pkind=3"><h6>用户余额充值（元）</h6><b><font>￥</font><?php echo $paymoney6;?></b></a>
        </ul>
    </div>
        
    </td>
    <td valign="top">
        <?php 
        $rt=$db->query("SELECT SUM(tgallmoney) AS tgallmoney FROM ".__TBL_TG_USER__);
        $row=$db->fetch_array($rt,'name');
        $tgallmoney=intval($row['tgallmoney']);
        $total_today=$db->COUNT(__TBL_TG_USER__," (TO_DAYS(NOW()) - TO_DAYS(from_unixtime(regtime,'%Y-%m-%d')))=0  ");
        ?>
        <div class="box">
          <h5>推广<?php echo $TG_set['tgytitle'];?>统计<span class="line"></span></h5>
            <ul class="cols2">
                <a><h6>总数（人）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__);?></b><span>+<?php echo $total_today;?> 今日新增</span></a>
                <a><h6>推广<?php echo $TG_set['tgytitle'];?>（人）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__," flag<>2");?></b></a>
                <a><h6>合伙人（个）</h6><b><?php echo $db->COUNT(__TBL_TG_USER__,"tguid>0");?></b></a>
                <a><h6>累计支出余额（元）</h6><b><font>￥</font><?php echo $tgallmoney;?></b></a>
            </ul>
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
			yAxis: {min: 0,title: {text:'最近30天用户注册量（人）'}},
			legend: {enabled: false},
			tooltip: {pointFormat: '<b style="font-weight:normal;color:#666">注册{point.y:.0f}人</b>'},
			colors: ["#7CC7C0"],
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
		zeai.ajax({url:'TG_welcome'+zeai.ajxext+'submitok=ajax_reg_day'},function(e){
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
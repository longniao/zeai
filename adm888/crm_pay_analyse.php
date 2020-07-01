<?php
require_once '../sub/init.php';
header("Cache-control: private");
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/udata.php';

if(!in_array('crm_pay_analyse',$QXARR))exit(noauth());


$SQL = "";

//非超管匹配自己门店
if(!in_array('crm',$QXARR))$SQL.=" AND agentid=$session_agentid";
//超管搜索按门店
if (ifint($agentid) && in_array('crm',$QXARR)){
	$row = $db->ROW(__TBL_CRM_AGENT__,"title","id=".$agentid);
	if ($row)$agenttitle= dataIO($row[0],'out');
	$SQL .= " AND agentid=$agentid";
}else{
	$agenttitle=$session_agenttitle;
}


if($submitok=='ajax_reg_day'){
	if(ifdate($date1)){
		$date1 = strtotime($date1);
		$SQL  .= " AND htdate >= $date1";
	}
	if(ifdate($date2)){
		$date2 = strtotime($date2);
		$SQL  .= " AND htdate <= $date2";
	}
	if(empty($SQL)){
		$LIMIT = " LIMIT 30";
	}else{
		$LIMIT = "";	
	}
	$rt=$db->query("SELECT SUM(price) AS num,from_unixtime(htdate,'%Y-%m-%d') AS day FROM ".__TBL_CRM_HT__." WHERE payflag=1".$SQL." GROUP BY day ORDER BY day DESC".$LIMIT);
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

//根据日期总
if(ifdate($date1)){
	$date11 = strtotime($date1);
	$SQL  .= " AND htdate >= $date11";
}
if(ifdate($date2)){
	$date22 = strtotime($date2);
	$SQL  .= " AND htdate <= $date22";
}
$rt=$db->query("SELECT SUM(price) AS num FROM ".__TBL_CRM_HT__." WHERE payflag=1".$SQL);
$total = $db->num_rows($rt);
$totalnum = 0;
if ($total > 0) {
	$row = $db->fetch_array($rt,'num');
	$totalnum  = $row[0];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="js/highcharts.js"></script> 

</head>
<link href="css/main.css" rel="stylesheet" type="text/css">

<style>
.box{width:100%;background-color:#fff;border:#eee 1px solid;text-align:left;box-sizing:border-box;padding-bottom:15px;clear:both}
.box h5{width:100%;font-size:15px;font-weight:normal;border-bottom:#f5f5f5 1px solid;line-height:50px;margin:0 0 10px 0;padding:0 15px;display:block;position:relative}
.box span.line{width:20px;border-bottom:#009688 2px solid;position:absolute;top:50px;left:35px}
.box ul {width:100%;padding:0 10px;box-sizing:border-box;clear:both;overflow:auto} 
.box ul a{width:-webkit-calc(33% - 20px);padding:13px 15px 15px 15px;margin:10px;height:80px;float:left;background-color:#f8f8f8;border-radius:2px;float:left;box-sizing:border-box;-webkit-transition:all .5s;-moz-transition:all .5s;transition:all .5s}
.box ul.cols3 a:nth-child(3n){margin-right:0;width:-webkit-calc(34% - 20px);}
.box ul a:hover{background-color:#f2f2f2}
.box ul.cols4 a{width:-webkit-calc(25% - 20px);}
.box ul a h6{color:#999;line-height:12px;display:inline-block}
.box ul a b{line-height:50px;color:#009688;font-size:30px;font-family:Arial, Helvetica, sans-serif;display:block}
/***/
</style>
<body>
<div class="navbox">
    <a class="ed">财务统计/分析</a>
    
    <div class="Rsobox">
 
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<table class="table0 W98_">
<tr>
<td width="50" align="left" class="border0" >
  
</td>
<td height="100" align="left" class="S14 C999">


<!--超管按门店查询-->
<?php if(in_array('crm',$QXARR)){?>
    <?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 > 0) {
		?>
        <div class="FL" style="margin-right:200px">
        按门店筛选：
		<select name="agentid" class="W200 size2 " style="margin-right:10px" onChange="zeai.openurl('<?php echo SELF;?>?agentid='+this.value+'&agenttitle=<?php echo $agenttitle;?>')">
		<option value="">全部门店</option>
		<?php
			for($j=0;$j<$total2;$j++) {
				$rows2 = $db->fetch_array($rt2,'num');
				if(!$rows2) break;
				$clss=($agentid==$rows2[0])?' selected':'';
				?>
				<option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[1],'out');?></option>
                <?php
			}
		?>
		</select>
        </div>
		<?php
    }
    ?>
<?php }?>
<!---->



    <form name="form1" method="get" action="<?php echo SELF; ?>">
    <input name="date1" type="text" id="date1" maxlength="25" class="input W100 size2" placeholder="起始时间" value="<?php echo $date1; ?>"  autocomplete="off">　～　
    <input name="date2" type="text" id="date2" maxlength="25" class="input W100 size2" placeholder="结束时间" value="<?php echo $date2; ?>"  autocomplete="off">　
    <input type="submit" value="开始统计" class="btn size2 QING" />
    <input type="hidden" name="agentid" value="<?php echo $agentid;?>" />
    </form>
</td>
<td width="50" align="right">&nbsp;</td>
</tr>
<tr>
  <td align="left" class="border0" ></td>
  <td align="center"><div class="box">
        <div id="container2" style="width:90%; height:280px;margin:0 auto"></div>
    
    </td>
  <td align="right">&nbsp;</td>
</tr>
<tr>
  <td align="left" class="border0" ></td>
  <td align="center">&nbsp;</td>
  <td align="right">&nbsp;</td>
</tr>
</table>

<br><br><br>
<script>
	
	//
	setTimeout(function(){
		zeai.ajax({url:'crm_pay_analyse'+zeai.ajxext+'submitok=ajax_reg_day&agentid=<?php echo $agentid;?>&date1=<?php echo $date1;?>&date2=<?php echo $date2;?>'},function(e){
			if(!zeai.empty(e)){
				var arr=e;
				arr = arr.replace(/"/g,"");
				arr = zeai.jsoneval(arr);
				reg(arr);
			}
		});
	},200);
	<?php if (!empty($agenttitle))$agenttitle='【'.$agenttitle.'】';?>
	function reg(arr){
		Highcharts.chart('container2', {
			chart: {type: 'column'},
			title: {text: '<?php echo $agenttitle;?>收益统计（总计：￥<?php echo $totalnum;?>）'},
			subtitle: {text: ''},
			credits: {"enabled": false},
			xAxis: {
				type: 'category',
				labels: {
					rotation: -45,
					style: {fontSize: '12px',fontFamily: 'Verdana, sans-serif'}
				}
			},
			yAxis: {min: 0,title: {text:'合同已审金额（元）'}},
			legend: {enabled: false},
			tooltip: {pointFormat: '<b style="font-weight:normal;color:#666">￥{point.y:.0f}</b>'},
			colors: ["#7CC7C0"],
			series: [{
				name: 'zeai.cn',
				data: arr,
				dataLabels: {
					enabled: true,
					rotation:0,
					color: '#ffffff',
					align: 'center',
					format: '￥{point.y:.0f}', // one decimal
					y:22, //10 pixels down from the top
					style:{fontSize:'12px',fontFamily:'Arial,sans-serif'}
				}
			}]
		});
	}



</script>

<script src="laydate/laydate.js"></script>
<script>
    laydate.render({
        theme: 'molv'
        ,elem: '#date1'
        ,type: 'date'
    }); 
    laydate.render({
        theme: 'molv'
        ,elem: '#date2'
        ,type: 'date'
    }); 
    </script>

<?php require_once 'bottomadm.php';?>


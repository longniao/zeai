<?php
ob_start();
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('analyse_month',$QXARR))exit(noauth());
require_once ZEAI.'cache/udata.php';
header("Cache-control: private");
$sDATE1    = (empty($sDATE1))?YmdHis(ADDTIME,'Ymd'):$sDATE1;
$sDATE1tmp = explode('-',$sDATE1);
$sDATE1sql = $sDATE1tmp[0].'-'.$sDATE1tmp[1];

$SQL    = " kind<>4 ";
$SQL_10 = " kind<>4 AND myinfobfb>10 AND mob<>'' ";
$SQL_month_reg = " AND ( date_format(from_unixtime(regtime),'%Y-%m') = '$sDATE1sql' )";

$SQLpay = " AND ( date_format(from_unixtime(addtime),'%Y-%m') = '$sDATE1sql' )";
if(ifint($agentid) && in_array('crm',$QXARR)){
	$SQL_AGENT = " AND agentid=$agentid";
	//
	$uidlist = array();
	$rtU=$db->query("SELECT id FROM ".__TBL_USER__." WHERE ".$SQL.$SQL_AGENT);
	$totalU = $db->num_rows($rtU);
	if ($totalU > 0) {
		for($iU=1;$iU<=$totalU;$iU++) {
			$rowsU = $db->fetch_array($rtU,'name');
			if(!$rowsU)break;
			$uidlist[]=$rowsU['id'];
		}
		$uidlist = (is_array($uidlist))?implode(',',$uidlist):'';
		$SQL_pay_agent_inU=" AND uid in ($uidlist)";
	}
}
$SQL_umonth = " AND ( date_format(from_unixtime(regtime),'%Y-%m') = '$sDATE1sql' )";
/////////////////////////////////////////////////////////////////////////////////////////
switch ($submitok) {
	case 'ajax_pay_month':
		//循环当前天数
		$totald=get_day($sDATE1,1);$paymoney = array();
		for($d=1;$d<=$totald;$d++) {
			$j = ($d<10)?$j='0'.$d:$d;
			$date2   = $sDATE1.'-'.$j;
			$SQLpay2 = " AND ( date_format(from_unixtime(paytime),'%Y-%m-%d') = '$date2' )";
			//查pay
			$rtPAY=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 ".$SQLpay2.$SQL_pay_agent_inU);
			$rowPAY=$db->fetch_array($rtPAY,'name');
			$paymoney[]=floatval($rowPAY['paymoney']);
		}
		$series[] = array('name'=>'日充值','data'=>$paymoney,'dashStyle'=>'Solid','dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));//
		$categories=get_day($sDATE1,2);
		json_exit(array('categories'=>$categories,'series'=>$series));
	break;
	case 'ajax_reg_month':
		$lmt=get_day($sDATE1,1);
		$rt=$db->query("SELECT COUNT(id) AS num,from_unixtime(regtime,'%Y-%m-%d') AS day FROM ".__TBL_USER__." WHERE ".$SQL.$SQL_month_reg.$SQL_AGENT." GROUP BY day ORDER BY day DESC LIMIT $lmt");
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
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/highcharts/highcharts.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/highcharts/exporting.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/highcharts/highcharts-zh_CN.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.table0{width:-webkit-calc(100% - 40px);margin:0 auto 10px auto;}
.cbox{width:-webkit-calc(100% - 40px);margin:0 auto;background-color:#fff;border:#E1E6EB 1px solid;text-align:left;box-sizing:border-box;padding-bottom:15px;clear:both;overflow:auto;position:relative}
.cbox h5{width:100%;background-color:#f9f9f9;font-size:15px;font-weight:normal;border-bottom:#f5f5f5 1px solid;line-height:50px;margin:0 0 10px 0;padding:0 15px;display:block;position:relative;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.cbox span.line,.cbox span.right_line{width:48px;border-bottom:#009688 2px solid;position:absolute;top:50px;left:35px}
.cbox span.right{position:absolute;top:0px;left:50%}
.cbox span.right_line{left:-webkit-calc(50% + 35px)}
.cbox ul{font-size:32px;padding:30px 0;width:23%;margin:10px 1%;border:#E1E6EB 1px solid;text-align:center;float:left;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.cbox ul em{height:40px;line-height:40px}
.cbox ul em i{color:#009688}
.cbox ul div{font-size:14px;color:#999;margin-top:5px}
.cbox ul:nth-child(2) em{color:#EE5A4E}
.cbox ul:nth-child(2) em i{color:#EE5A4E}
</style>
<script>
var jsfilename = 'analyse_month',sDATE1='<?php echo $sDATE1sql;?>',agentid=<?php echo intval($agentid);?>,daynum=<?php echo get_day($sDATE1,1);?>;
</script>
</head>
<body>
<div class="navbox">
    <a class='ed'>数据分析-月报</a>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<form name="form1" method="get" action="<?php echo SELF; ?>">
    <table class="table0">
    <tr>
    <td height="60" align="left" class="border0" >
		<span class="textmiddle S14">选择月份　</span><input name="sDATE1" type="text" id="sDATE1" maxlength="25" class="input W100 size2 picmiddle" placeholder="选择月份" value="<?php echo $sDATE1sql; ?>"  autocomplete="off">
        <!--超管按门店查询-->
        <?php if(in_array('crm',$QXARR)){?>
        <?php
        $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
        $total2 = $db->num_rows($rt2);
        if ($total2 > 0) {?>
        <span class="textmiddle S14">　　按门店　</span><select name="agentid" class="size2 W200 picmiddle">
          <option value="">查看全部</option>
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
        <?php
        }
        ?>
        <?php }?>
        <button type="submit" class="btn size2 picmiddle"><i class="ico">&#xe6c4;</i> 开始查询</button>
    </td>
    <td width="400" align="right" class="S14 C666" ><img src="images/!.png" width="14" height="14" valign="middle"> <font class="picmiddle">完善资料是指资料完整度>10%且有手机号</font></td>
    </tr>
    </table>
</form>
<div class="cbox">
    <h5>本月付费注册总览<span class="line"></span></h5>
    <ul>
		<?php 
        $rtPAY=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 ".$SQLpay.$SQL_pay_agent_inU);
        $rowPAY=$db->fetch_array($rtPAY,'name');
        $paymoney=floatval($rowPAY['paymoney']);
        ?>
        <em><i class="ico">&#xe61a;</i> <?php echo $paymoney;?></em><div>订单金额（元）</div>
    </ul>
    <ul>
		<?php $totalnum = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT.$SQL_umonth);?>
        <em><i class="ico2">&#xea3a;</i> <?php echo $totalnum;?></em><div>新增用户（人）</div>
    </ul>
    <ul>
		<?php $totalnum = $db->COUNT(__TBL_USER__,$SQL_10.$SQL_AGENT.$SQL_umonth);?>
        <em><i class="ico2">&#xe69d;</i> <?php echo $totalnum;?></em><div>新增完善资料用户数（人）</div>
    </ul>
    <ul>
		<?php 
		$rtP=$db->query("SELECT id FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 ".$SQL_pay_agent_inU.$SQLpay." AND flag=1 GROUP BY uid");
		$totalnum = $db->num_rows($rtP);
        ?>
        <em><i class="ico">&#xe6ab;</i> <?php echo intval($totalnum);?></em><div>付费用户数（人）</div>
    </ul>
</div>
<br>
<div class="cbox">
    <h5>本月付费分析<span class="line"></span></h5>
    <div id="pay_month_box" style="width:98%;height:300px"></div>
</div>
<br>
<div class="cbox">
    <h5>本月会员注册量<span class="line"></span></h5>
    <div id="reg_month_box" style="width:98%;height:300px"></div>
</div>
<br><br><br><br>
<script src="js/analyse_month.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="laydate/laydate.js"></script><script>
laydate.render({elem:'#sDATE1',type: 'month'});
</script>
<?php require_once 'bottomadm.php';
function get_day( $date ,$rtype = 1){
	$tem = explode('-',$date);//切割日期 得到年份和月份
	$year = intval($tem[0]);
	$month = intval($tem[1]);
	if( in_array($month,array(1,3,5,7,8,10,12))  ){
		$text = 31;// $text = $year.'年的'.$month.'月有31天';
	}elseif( $month == 2 ){
      if ( $year%400 == 0 || ($year%4 == 0 && $year%100 !== 0) ){//判断是否是闰年
        $text = 29; // $text = $year.'年的'.$month.'月有29天';
      }else{
        $text = 28;// $text = $year.'年的'.$month.'月有28天';
      }
    }else{
      $text = 30;// $text = $year.'年的'.$month.'月有30天';
    }
    if ($rtype == 2) {
      for ($i = 1; $i <= $text ; $i ++ ) {
		$j = ($i<10)?$j='0'.$i:$i;
		$m = ($month<10)?'0'.$month:$month;
        $r[] = $year."-".$m."-".$j;
      }
    } else {
      $r = $text;
    }
    return $r;
}
?>
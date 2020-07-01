<?php
ob_start();
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('analyse_hn_month',$QXARR))exit(noauth());
require_once ZEAI.'cache/udata.php';
header("Cache-control: private");
$sDATE1 = (empty($sDATE1))?YmdHis(ADDTIME,'Ymd'):$sDATE1;

$sDATE1tmp = explode('-',$sDATE1);
$sDATE1sql = $sDATE1tmp[0].'-'.$sDATE1tmp[1];


$SQLpay = " AND ( date_format(from_unixtime(paytime),'%Y-%m') = '$sDATE1sql' )";
$SQL_TIMEqx  = " AND ( date_format(from_unixtime(addtime),'%Y-%m') = '$sDATE1sql' )";
$SQL_TIMErl  = " AND ( date_format(from_unixtime(admtime),'%Y-%m') = '$sDATE1sql' )";
//超管可以按门店搜索
if(ifint($agentid) && in_array('crm',$QXARR))$SQL_AGENT = " AND agentid=$agentid";


switch ($submitok) {
	case 'ajax_hn_paydate':
		$rt=$db->query("SELECT admid FROM ".__TBL_USER__." WHERE admid>0 ".$SQL_AGENT." GROUP BY admid");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$hnidlist[]=$rows['admid'];
			}
			$hnidlist = (is_array($hnidlist))?implode(',',$hnidlist):'';
		}
		$rt=$db->query("SELECT id,username FROM ".__TBL_CRM_HN__." WHERE id in (".$hnidlist.") ".$SQL_AGENT." ORDER BY endtime DESC");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$admid    = intval($rows['id']);
				//$username = $rows['username'].'（ID:'.$admid.'）';
				$username = $rows['username'];
				//get ulist
				$uidlist = array();$paymoney = array();
				$rtU=$db->query("SELECT id FROM ".__TBL_USER__." WHERE admid=$admid AND admid>0");
				$totalU = $db->num_rows($rtU);
				if ($totalU > 0) {
					for($iU=1;$iU<=$totalU;$iU++) {
						$rowsU = $db->fetch_array($rtU,'name');
						if(!$rowsU)break;
						$uidlist[]=$rowsU['id'];
					}
					if(is_array($uidlist) && count($uidlist)>0){
						$uidlist = (is_array($uidlist))?implode(',',$uidlist):'';
						//循环当前天数
						$totald=get_day($sDATE1,1);
						for($d=1;$d<=$totald;$d++) {
							$j = ($d<10)?$j='0'.$d:$d;
							$date2   = $sDATE1.'-'.$j;
							$SQLpay2 = " AND ( date_format(from_unixtime(paytime),'%Y-%m-%d') = '$date2' )";
							//查pay
							$rtPAY=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE (kind=1 OR kind=2 OR kind=3) AND flag=1 AND uid in ($uidlist)   ".$SQLpay2);
							$rowPAY=$db->fetch_array($rtPAY,'name');
							$paymoney[]=floatval($rowPAY['paymoney']);
						}
					}else{
						$paymoney[] = 0;
					}
					$series[] = array('name'=>$username,'data'=>$paymoney,'dashStyle'=>'Solid');//,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal'))
				}
			}
			$categories=get_day($sDATE1,2);
			json_exit(array('categories'=>$categories,'series'=>$series));
		}
	break;
	case 'SSSS www.zeai.cn V6.7.2 SSSS':break;
}



//老
if ($submitok == "ajax_hn_pay") {
	$rt=$db->query("SELECT admid FROM ".__TBL_USER__." WHERE admid>0 ".$SQL_AGENT." GROUP BY admid");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$hnidlist[]=$rows['admid'];
		}
		$hnidlist = (is_array($hnidlist))?implode(',',$hnidlist):'';
	}
	//
	//echo '原hnidlist：'.$hnidlist.' <br> ';
	
	
	$rt=$db->query("SELECT id,username FROM ".__TBL_CRM_HN__." WHERE id in (".$hnidlist.") ".$SQL_AGENT." ORDER BY endtime DESC");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$admid    = intval($rows['id']);
			$username[]= $rows['username'].'（ID:'.$admid.'）';
			
			//get ulist
			$uidlist = array();
			$rtU=$db->query("SELECT id FROM ".__TBL_USER__." WHERE admid=$admid AND admid>0");
			$totalU = $db->num_rows($rtU);
			if ($totalU > 0) {
				for($iU=1;$iU<=$totalU;$iU++) {
					$rowsU = $db->fetch_array($rtU,'name');
					if(!$rowsU)break;
					$uidlist[]=$rowsU['id'];
				}
				if(is_array($uidlist) && count($uidlist)>0){
					$uidlist = (is_array($uidlist))?implode(',',$uidlist):'';
					//查pay
					$rtPAY=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE (kind=1 OR kind=2 OR kind=3) AND flag=1 AND uid in ($uidlist)   ".$SQLpay);
					$rowPAY=$db->fetch_array($rtPAY,'name');
					$paymoney[]=floatval($rowPAY['paymoney']);
				}else{
					$paymoney[] = 0;
				}
			}
		}
		json_exit(array('categories'=>$username,'data'=>$paymoney));
	}
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
<style>
.table0{width:-webkit-calc(100% - 40px);margin:0 auto 10px auto;}
.cbox{width:-webkit-calc(100% - 40px);margin:0 auto;background-color:#fff;border:#E1E6EB 1px solid;text-align:left;box-sizing:border-box;padding-bottom:15px;clear:both;overflow:auto}
.cbox h5{width:100%;background-color:#f9f9f9;font-size:15px;font-weight:normal;border-bottom:#f5f5f5 1px solid;line-height:50px;margin:0 0 10px 0;padding:0 15px;display:block;position:relative;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.cbox span.line{width:48px;border-bottom:#009688 2px solid;position:absolute;top:50px;left:35px}
.cbox span.tips{position:absolute;top:0px;right:20px;color:#f60;font-size:14px;}
.cbox ul {padding:15px;margin:10px 1%;border:#E1E6EB 1px solid;text-align:center;float:left;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.cbox ul li{font-size:14px;height:40px;line-height:40px;border-bottom:#eee 1px solid}

.cbox ul:nth-child(2){border:0;background-color:#1DA296;color:#fff}
.cbox ul:nth-child(2) li{border-color:#008b7e}
.cbox ul:nth-child(2) li:first-child{font-weight:bold;font-size:16px}
.cbox ul:nth-child(2) li:last-child{font-weight:bold;font-size:16px;background-color:#33ABA0}
.cbox ul:nth-child(3) li:last-child{font-weight:bold}

.cbox ul li:first-child{font-size:14px}
.cbox ul li:last-child{background-color:#f9f9f9}
.cbox ul li span{display:inline-block;width:33%}
</style>
<script>
var jsfilename = 'analyse_hn_month',sDATE1='<?php echo $sDATE1sql;?>',agentid=<?php echo intval($agentid);?>;
</script>
</head>
<body>
<div class="navbox">
    <a class='ed'>红娘月报数据分析</a>
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
        <span class="textmiddle S14">　　按门店　</span><select name="agentid" class="size2 picmiddle W200">
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
    <td width="400" align="right" class="S14 C666" ><img src="images/!.png" width="14" height="14" valign="middle"> <font class="picmiddle">金额只统计红娘录入认领的会员(VIP+<?php echo $_ZEAI['loveB'];?>+余额充值)</font></td>
    </tr>
    </table>
</form>


<?php 
$rt=$db->query("SELECT admid FROM ".__TBL_USER__." WHERE admid>0 ".$SQL_AGENT." GROUP BY admid");
$total = $db->num_rows($rt);
if ($total > 0) {
	for($i=1;$i<=$total;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$hnidlist[]=$rows['admid'];
	}
	$hnidlist = (is_array($hnidlist))?implode(',',$hnidlist):'';
}
if(empty($hnidlist)){echo "<br><br><div class='nodataico'><i></i>暂无红娘认领用户或未录入，无法统计";exit;}
$rt=$db->query("SELECT id,username FROM ".__TBL_CRM_HN__." WHERE id in (".$hnidlist.") ".$SQL_AGENT." ORDER BY endtime DESC");
$total = $db->num_rows($rt);
if ($total > 0) {while($tmprows = $db->fetch_array($rt,'name')){$HNARR[]=$tmprows;}}
function get_hn_total_paymoney($admid) {
	global $db,$SQLpay;
	$uidlist = array();$paymoney = array();
	$rtU=$db->query("SELECT id FROM ".__TBL_USER__." WHERE admid=$admid AND admid>0");
	$totalU = $db->num_rows($rtU);
	if ($totalU > 0) {
		for($iU=1;$iU<=$totalU;$iU++) {
			$rowsU = $db->fetch_array($rtU,'name');
			if(!$rowsU)break;
			$uidlist[]=$rowsU['id'];
		}
		if(is_array($uidlist) && count($uidlist)>0){
			$uidlist = (is_array($uidlist))?implode(',',$uidlist):'';
			//查pay
			$rtPAY  = $db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE (kind=1 OR kind=2 OR kind=3) AND flag=1 AND uid in ($uidlist)   ".$SQLpay);
			$rowPAY = $db->fetch_array($rtPAY,'name');
			$paymoney = floatval($rowPAY['paymoney']);
		}else{
			$paymoney = 0;
		}
		return $paymoney;
	}
	return 0;
}
?>        

<div class="cbox">
    <h5>红娘综合统计<span class="line"></span><span class="tips"></span></h5>
    <ul style="width:15%">
    	<li>红娘名称</li>
		<?php 
		$total_paymoney = 0;
		if (count($HNARR) >= 1 && is_array($HNARR)){
			foreach ($HNARR as $k=>$V) {
				echo '<li>'.$V['username'].'</li>';
				$tmp1 = get_hn_total_paymoney($V['id']);
				$hn_total_paymoneyARR[] = $tmp1;
				$total_paymoney = $total_paymoney+$tmp1;
			}
		}
		?>        
    	<li>总计</li>
    </ul>
    <ul style="width:14%">
    	<li>充值金额（元）</li>
		<?php 
		if (count($hn_total_paymoneyARR) >= 1 && is_array($hn_total_paymoneyARR)){
			foreach ($hn_total_paymoneyARR as $V) {
				echo '<li>'.$V.'</li>';
			}
		}
		?>        
    	<li><?php echo $total_paymoney;?></li>
    </ul>
    <ul style="width:31%">
    	<li><span>牵线次数</span><span>牵线成功次数</span><span>牵线VIP人数</span></li>
		<?php 
		$qxnum1N=0;$qxnum2N=0;$qxnum3N=0;
		if (count($HNARR) >= 1 && is_array($HNARR)){
			foreach ($HNARR as $k=>$V) {
				$admid = $V['id'];
				$username = $V['username'];
				$qxnum1 = $db->COUNT(__TBL_QIANXIAN__,"username='$username'".$SQL_TIMEqx);
				$qxnum2 = $db->COUNT(__TBL_QIANXIAN__,"username='$username' AND flag=2 ".$SQL_TIMEqx);
				//
				$rt=$db->query("SELECT id FROM ".__TBL_USER__." WHERE grade>1 AND admid=".$admid);
				$total = $db->num_rows($rt);
				if ($total > 0) {
					$qxvipuidlist=array();
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt,'name');
						if(!$rows)break;
						$qxvipuidlist[]=$rows['id'];
					}
					$qxvipuidlist = (is_array($qxvipuidlist))?implode(',',$qxvipuidlist):'';
				}
				if(!empty($qxvipuidlist)){
					$qxnum3 = $db->COUNT(__TBL_QIANXIAN__,"senduid in (".$qxvipuidlist.") AND username='$username'".$SQL_TIMEqx);
				}else{
					$qxnum3 = 0;
				}
				echo '<li><span>'.$qxnum1.'</span><span>'.$qxnum2.'</span><span>'.$qxnum3.'</span></li>';
				$qxnum1N = $qxnum1N+$qxnum1;
				$qxnum2N = $qxnum2N+$qxnum2;
				$qxnum3N = $qxnum3N+$qxnum3;
			}
		}
		?>        
   	  <li><span><?php echo $qxnum1N;?></span><span><?php echo $qxnum2N;?></span><span><?php echo $qxnum3N;?></span></li>
    </ul>
    <ul style="width:32%">
    	<li><span>认领(人)</span><span>约见(次)</span><span>跟进(次)</span></li>
		<?php 
		$rlnumN=0;$yjnumN=0;$gjnumN=0;
		if (count($HNARR) >= 1 && is_array($HNARR)){
			foreach ($HNARR as $k=>$V) {
				$admid = $V['id'];
				$username = $V['username'];
				$rlnum = $db->COUNT(__TBL_USER__,"admid=$admid".$SQL_TIMErl);
				$yjnum = $db->COUNT(__TBL_CRM_MATCH__,"admid=$admid ".$SQL_TIMEqx);
				$gjnum = $db->COUNT(__TBL_CRM_BBS__,"admid=$admid ".$SQL_TIMEqx);
				//
				echo '<li><span>'.$rlnum.'</span><span>'.$yjnum.'</span><span>'.$gjnum.'</span></li>';
				$rlnumN = $rlnumN+$rlnum;
				$yjnumN = $yjnumN+$yjnum;
				$gjnumN = $gjnumN+$gjnum;
			}
		}
		?>        
   	  <li><span><?php echo $rlnumN;?></span><span><?php echo $yjnumN;?></span><span><?php echo $gjnumN;?></span></li>
    </ul>
</div>

<br>
<div class="cbox">
    <h5>红娘按天充值<span class="line"></span></h5>
    <div id="paydatebox" style="width:98%;height:400px;margin:0 auto"></div>
</div>

<br>
<div class="cbox">
    <h5>红娘充值总量<span class="line"></span></h5>
    <div id="container" style="width:98%;height:400px;margin:0 auto"></div>
</div>
<br><br><br><br>
<script src="js/analyse_hn_month.js?<?php echo $_ZEAI['cache_str'];?>"></script>
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
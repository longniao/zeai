<?php require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_crm.php';

if(!in_array('crm_welcome',$QXARR))exit(noauth());

//非超管门店+地区 
$SQL_agent = getAgentSQL();

$SQL  = " (flag=1 OR flag=-2) AND kind<>4 AND admid>0 ";
$SQL .= $SQL_agent;



if(!in_array('crm',$QXARR)){
	$SQLA .= " AND ( agentid=$session_agentid ) ";//仅门店
	$SQLU .= " AND ( U.agentid=$session_agentid ) ";//仅门店多表
}

$year  = YmdHis(ADDTIME,'Y');
$month = YmdHis(ADDTIME,'Ym');
$today = YmdHis(ADDTIME,'Ymd');
$meet_flagARR = json_decode($_CRM['meet_flag'],true);
$qx_flagARR   = json_decode($_CRM['qxflag'],true);
$crm_flagARR  = json_decode($_CRM['crm_flag'],true);
$bbsN1 = ",(SELECT MAX(id) AS max_id FROM ".__TBL_CRM_BBS__." GROUP BY uid) N";$bbsN2 .= " AND B.id=N.max_id ";
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm_welcome.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
</head>
<body>
<?php if (strstr($session_crmkind,'adm') || in_array('crm',$QXARR)){?>
<div class="top4">
	<li>
	<i class="ico">&#xe603;</i>
	<em>
        <b><?php echo $db->COUNT(__TBL_USER__,$SQL);?></b> 人　<font class="hot">今天+<?php echo $db->COUNT(__TBL_USER__,$SQL." AND (  date_format(from_unixtime(admtime),'%Y-%m-%d') = '$today'    )");?></font>
        <span><br>
        <?php
        if (empty($session_agenttitle)){
            echo '<font class="h6b">全部门店总客户</font>（录入/认领）';
        }else{
            echo '<font class="h6b">门店总客户</font>（'.$session_agenttitle.'）';
        }
        ?>
        </span>
	</em>
    </li>
	<li>
    	<i class="ico">&#xe861;</i>
		<em>
			<b><?php echo $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade=0");?></b> 人
            <span><br><font class="h6b">售前客户</font>（售前未签约客户）</span>
        </em>
    </li>
	<li>
    	<i class="ico">&#xe621;</i>
		<em>
			<span class="textmiddle"><b><?php echo $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>0");?></b> 人</span>
            <span class="liRnum"><font>服务成功</font><b><font><?php echo $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>0 AND crm_flag=3");?></font></b><font>人</font></span>　<a href="crm_user.php?if3=1" class="btn size1 BAI">查看</a>
            <span><br><font class="h6b">售后客户</font>（售后已签约客户）</span>
        </em>
    </li>
	<li>
    	<i class="ico">￥</i>
		<em>
			￥<b><?php 
				$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=1 AND htflag=1".$SQLA);
				$row=$db->fetch_array($rt,'name');
				$HTprice_total = intval($row['price']);
				echo number_format($HTprice_total);
			?></b>
            <span><br><font class="h6b">总盈收</font>（已签约合同金额）</span>
        </em>
    </li>
</div>
<div class="clear"></div>
<?php }?>

<?php
//公共
if (strstr($session_crmkind,'adm') || strstr($session_crmkind,'cw') || in_array('crm',$QXARR)){
	//今年
	$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=1 ".$SQLA." AND htflag=1 AND date_format(from_unixtime(addtime),'%Y') = '$year' ");
	$row=$db->fetch_array($rt,'name');
	$HTprice_year = number_format($row['price']);
	//今天
	$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=1 ".$SQLA." AND htflag=1 AND date_format(from_unixtime(addtime),'%Y-%m-%d') = '$today' ");
	$row=$db->fetch_array($rt,'name');
	$HTprice_today = number_format($row['price']);
	//本月
	$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=1 ".$SQLA." AND htflag=1 AND date_format(from_unixtime(addtime),'%Y-%m') = '$month' ");
	$row=$db->fetch_array($rt,'name');
	$HTprice_month = number_format($row['price']);
	//待审付款
	$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=0 AND htflag=1".$SQLA);
	$row=$db->fetch_array($rt,'name');
	$HTprice_flag0 = number_format($row['price']);
	//合同总数
	$HTnum_total = $db->COUNT(__TBL_CRM_HT__,"1=1".$SQLA);
	//合同总数 - 今天
	$HTnum_totay = $db->COUNT(__TBL_CRM_HT__,"date_format(from_unixtime(addtime),'%Y-%m-%d') = '$today'".$SQLA);
	//待审合同
	$HTnum_flag0 = $db->COUNT(__TBL_CRM_HT__,"htflag=0".$SQLA);
	//本月签单
	$HTnum_flag1 = $db->COUNT(__TBL_CRM_HT__,"htflag=1 AND date_format(from_unixtime(addtime),'%Y-%m') = '$month' ".$SQLA);

	//到期合同 - 7天
	$HTnum_expire_7 = $db->COUNT(__TBL_CRM_HT__," (crm_usjtime2 - ".ADDTIME.") < 604800  ".$SQLA);
	//到期合同 - 30天
	$HTnum_expire_30 = $db->COUNT(__TBL_CRM_HT__," (crm_usjtime2 - ".ADDTIME.") < 2592000  ".$SQLA);
	
	$HT_expire=$db->COUNT(__TBL_CRM_HT__,"crm_usjtime2 < ".ADDTIME." ".$SQLA);
}
?>
<div class="wmain">
	<div class="LL">
		<?php if (strstr($session_crmkind,'adm') || in_array('crm',$QXARR)){?>
    	<!--待办统计-->
        <div class="box">
          <h5><i class="ico tiaose S18">&#xe648;</i> 待办统计<font style="color:#999;font-weight:normal;font-size:14px"> (总览)</font><span class="line"></span></h5>
            <ul class="cols3">
            	<!--合同-->
				<?php 
                //本月签单金额
                $rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE  htflag=1 ".$SQLA." AND date_format(from_unixtime(addtime),'%Y-%m') = '$month' ");
                $row=$db->fetch_array($rt,'name');
                $HTprice_month2 = number_format($row['price']);
				
				//到期合同 - 今天
				$HTnum_expire_totay = $db->COUNT(__TBL_CRM_HT__,"date_format(from_unixtime(crm_usjtime2),'%Y-%m-%d') = '$today'".$SQLA);
                ?>
                <li>
                    <h6><font class="h5b">本年总营收</font>（已审金额）<font class="hot FR" style="margin-top:2px">今天+￥<?php echo $HTprice_today;?></font></h6>￥<b><?php echo $HTprice_year;?></b>
                    
                    <span class="liRnum"><font>本月</font><b><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTprice_month;?></font></b></span><br><br>
                    
                    <h6><font class="h5b">待审付款</font>（合同金额未审）</h6>￥<b><?php echo $HTprice_flag0;?></b><button class="btn HONG2 size1" onclick="zeai.openurl('crm_ht.php?t=payflag0')">去审核</button><br><br>
                	<h6><font class="h5b">合同总数</font>（签约合同）<font class="hot FR" style="margin-top:2px">今天+<?php echo $HTnum_totay;?></font></h6><b><?php echo $HTnum_total;?></b> 份<br><br>
                    <h6><font class="h5b">待审合同</font></h6><b><?php echo $HTnum_flag0;?></b> 份　<button class="btn HONG2 size1" onclick="zeai.openurl('crm_ht.php?t=htflag0')">去审核</button><br><br>
                	
                    <h6><font class="h5b">本月签单</font>（合同已审）</h6><b><?php echo $HTnum_flag1;?></b> 份
                    <span class="liRnum"><font>签单金额</font><b><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTprice_month2;?></font></b></span><br><br>
                    
                    <div class="linebox"><div class="line"></div><div class="title BAI">到期合同<font class="hot"> 今天+<?php echo $HTnum_expire_totay;?></font></div></div>
                    <em class="hot"><i></i><span>7天内到期</span><b><?php echo $HTnum_expire_7;?></b><a href="crm_ht.php?gq=gq7" class="btn size1 HONG2">去查看</a></em>
                    <em><i></i><span>30天内到期</span><b><?php echo $HTnum_expire_30;?></b><a href="crm_ht.php?gq=gq30" class="btn size1 BAI">去查看</a></em>
                    <em><i></i><span>已过期</span><b><?php echo $HT_expire;?></b><a href="crm_ht.php?gq=gq_1" class="btn size1 BAI">去查看</a></em>
                </li>
            
            	<!--售前-->
                <?php
				//售前公海
				$sq_hnid0_num = $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade=0 AND hnid=0");
				//今天认领
				if(in_array('crm',$QXARR)){
					$SQLtmp = "";
					$row = $db->ROW(__TBL_CRM_AGENT__,"SUM(claimnumday) AS ALLL","1=1","name");
					$claimnumday_ALL = intval($row['ALLL']);
				}else{
					$SQLtmp = $SQLA;
					$row = $db->ROW(__TBL_CRM_AGENT__,"SUM(claimnumday) AS ALLL","id=".$session_agentid,"name");
					$claimnumday_ALL = intval($row['ALLL']);
				}
				$claimnum_HN_totay = $db->COUNT(__TBL_CRM_CLAIM_LIST__,"adddate='".$today."' ".$SQLtmp);
				
				//跟进小计 总
				$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U WHERE B.uid=U.id AND U.crm_ugrade=0 ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num = $roww[0];

				
				//售前跟进-今天记录
				$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U WHERE B.uid=U.id AND U.crm_ugrade=0 AND date_format(from_unixtime(B.addtime),'%Y-%m-%d') = '$today'  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_today_add = $roww[0];
				//售前跟进-今天需跟进 nexttime
				$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade=0 AND date_format(from_unixtime(B.nexttime),'%Y-%m-%d') = '$today'  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_today_nexttime = $roww[0];
				//售前跟进-超过7天未跟进 addtime
				$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade=0 AND (".ADDTIME." - B.addtime  ) > 604800  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_7_addno = $roww[0];
				
				//售前跟进-超过30天未跟进 addtime
				$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade=0 AND (".ADDTIME." - B.addtime  ) > 2592000  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_30_addno = $roww[0];
				
				//售前跟进-过期未跟进 nexttime
				$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade=0 AND B.nexttime<".ADDTIME."  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_nexttime_expire = $roww[0];
				?>
                <li>
                    <h6><font class="h5b">售前公海</font>（录入未分配售前红娘）</h6><b><?php echo $sq_hnid0_num;?></b> 人　<button class="btn HONG2 size1" onclick="zeai.openurl('crm_sq.php?ifhnid=ifhnid0')">去分配</button><br><br>
                    <h6><font class="h5b">今天认领</font>（员工今天公海认领客户）</h6><b><?php echo $claimnum_HN_totay;?></b> 人
                    <span class="liRnum"><font>门店剩余额度</font><b><?php echo abs(intval($claimnumday_ALL-$claimnum_HN_totay));?></b><font>人　总数</font><b><?php echo $claimnumday_ALL;?></b></span><br><br>
					<h6><font class="h5b">跟进小计</font>（售前跟进统计）</h6><b><?php echo $sq_gj_num;?></b>
					<span class="liRnum"><font>今天</font><b style="color:#EE5A4E">+<?php echo $sq_gj_num_today_add;?></b></span>
					<br><br>
					<?php
                    $intentionARR = json_decode($_CRM['bbs_intention'],true);
                    if (count($intentionARR) >= 1 && is_array($intentionARR)){?>
                    	<?php
                        foreach ($intentionARR as $V){
                            $intention_id    = intval($V['i']);
                            $intention_title = $V['v'];
							$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade=0 AND B.intention=".$intention_id.$SQLU);
							$roww = $db->fetch_array($rtt);
							$intention_num = $roww[0];?>
                            <em><i></i><span><?php echo $intention_title;?></span><b><?php echo $intention_num;?></b><a href="crm_user_bbs.php?k=sq&intention=<?php echo $intention_id;?>" class="btn size1 BAI">去查看</a></em><?php
                        }
                    }
                    ?>
                    <em class="hot"><i></i><span>今天需跟进</span><b><?php echo $sq_gj_num_today_nexttime;?></b><a href="crm_user_bbs.php?k=sq&nexttime=1" class="btn size1 HONG2">去跟进</a></em>
                    <em><i></i><span>7天未跟进</span><b><?php echo $sq_gj_num_7_addno;?></b><a href="crm_user_bbs.php?k=sq&addtime=1" class="btn size1 BAI">去跟进</a></em>
                    <em><i></i><span>30天未跟进</span><b><?php echo $sq_gj_num_30_addno;?></b><a href="crm_user_bbs.php?k=sq&addtime=2" class="btn size1 BAI">去跟进</a></em>
                    <em><i></i><span>过期未跟进</span><b><?php echo $sq_gj_num_nexttime_expire;?></b><a href="crm_user_bbs.php?k=sq&nexttime=4" class="btn size1 BAI">去跟进</a></em>
                </li>
                <!--售后-->
                <?php $sh_hnid20_num = $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>0 AND hnid2=0");?>
                <li>
                	<h6><font class="h5b">售后公海</font>（未分配售后红娘）</h6><b><?php echo $sh_hnid20_num;?></b> 人　<button class="btn HONG2 size1" onclick="zeai.openurl('crm_sh.php?ifhnid=ifhnid0')">去分配</button>
					<br><br>
                	<!--约见工单-->
                    <?php
					//总
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE B.uid=U.id".$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num = $roww[0];
					//今天
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE B.uid=U.id AND date_format(from_unixtime(B.addtime),'%Y-%m-%d') = '$today'  ".$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num_today = $roww[0];
					//已完成
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE B.uid=U.id AND B.meet_flag=3".$SQLU);$roww = $db->fetch_array($rtt);
					$meet_num_OK = $roww[0];
					//进行中
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE B.uid=U.id AND B.meet_flag<>3".$SQLU);$roww = $db->fetch_array($rtt);
					$meet_num_DOING = $roww[0];
					
					//今天需约见
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE B.uid=U.id AND U.crm_flag<>3 AND date_format(from_unixtime(nexttime),'%Y-%m-%d') = '$today'  ".$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num_today_nexttime = $roww[0];
					//超过7天未约见
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE B.uid=U.id AND U.crm_flag<>3 AND (".ADDTIME." - addtime) > 604800  ".$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num_7 = $roww[0];
					//过期未约见
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE B.uid=U.id AND U.crm_flag<>3 AND nexttime < ".ADDTIME.$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num_nexttime_expire = $roww[0];
					?>
					<h6><font class="h5b">约见工单</font>（客户线下见面工单）<font class="hot FR" style="margin-top:2px">今天+<?php echo $yj_num_today;?></font></h6><b><?php echo $yj_num;?></b>
					<span class="liRnum"><font>已完成</font><b><?php echo $meet_num_OK;?></b></span>
					<span class="liRnum"><font>进行中</font><b><?php echo $meet_num_DOING;?></b></span>
					<br><br>
					<em class="hot"><i></i><span>今天需约见</span><b><?php echo $yj_num_today_nexttime;?></b><a href="crm_user_meet.php?mt_nexttime=1" class="btn size1 HONG2">去约见</a></em>
					<em><i></i><span>7天未约见</span><b><?php echo $yj_num_7;?></b><a href="crm_user_meet.php?mt_time=1" class="btn size1 BAI">去约见</a></em>
					<em><i></i><span>过期未约见</span><b><?php echo $yj_num_nexttime_expire;?></b><a href="crm_user_meet.php?mt_nexttime=4" class="btn size1 BAI">去约见</a></em>
                    <!--牵线-->
					<?php
                    if (count($qx_flagARR) >= 1 && is_array($qx_flagARR)){
						$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_QIANXIAN__." B,".__TBL_USER__." U WHERE B.senduid=U.id AND U.crm_flag<>3 AND date_format(from_unixtime(B.addtime),'%Y-%m-%d') = '$today'   ".$SQLU);
						$roww = $db->fetch_array($rtt);
						$qx_num_today = $roww[0];
						?>
                    	<div class="linebox"><div class="line"></div><div class="title BAI">客户牵线<font class="hot"> 今天+<?php echo $qx_num_today;?></font></div></div><?php
                        foreach ($qx_flagARR as $V){
                            $qx_id    = intval($V['i']);
                            $qx_title = $V['v'];
							$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_QIANXIAN__." B,".__TBL_USER__." U WHERE B.senduid=U.id AND U.crm_flag<>3 AND ((U.flag=1 OR U.flag=-2) AND U.kind<>4) AND B.flag=".$qx_id.$SQLU);// AND U.crm_flag<>3
							$roww = $db->fetch_array($rtt);
							$qx_num = $roww[0];?>
                            <em><i></i><span><?php echo $qx_title;?></span><b><?php echo $qx_num;?></b><a href="u_qianxian.php?qxflag=<?php echo $qx_id;?>&k=crmqx" class="btn size1 BAI">去查看</a></em><?php
                        }
                    }
                    ?>
                    <!--售后跟进-->
					<?php
                    //售后跟进-今天记录
                    $rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade>0 AND U.crm_flag<>3 AND date_format(from_unixtime(B.addtime),'%Y-%m-%d') = '$today'  ".$SQLU);
                    $roww = $db->fetch_array($rtt);
                    $sh_gj_num_today_add = $roww[0];
                    //售后跟进-今天需跟进 nexttime
                    $rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade>0 AND U.crm_flag<>3 AND date_format(from_unixtime(B.nexttime),'%Y-%m-%d') = '$today'  ".$SQLU);
                    $roww = $db->fetch_array($rtt);
                    $sh_gj_num_today_nexttime = $roww[0];
                    //售后跟进-超过7天未跟进 addtime
                    $rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade>0 AND U.crm_flag<>3 AND (".ADDTIME." - B.addtime  ) > 604800  ".$SQLU);
                    $roww = $db->fetch_array($rtt);
                    $sh_gj_num_7_addno = $roww[0];
                    //售后跟进-过期未跟进 nexttime
                    $rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE B.uid=U.id".$bbsN2." AND U.crm_ugrade>0 AND U.crm_flag<>3 AND B.nexttime<".ADDTIME."  ".$SQLU);
                    $roww = $db->fetch_array($rtt);
                    $sh_gj_num_nexttime_expire = $roww[0];
                    ?>
                    <div class="linebox"><div class="line"></div><div class="title BAI">售后跟进<font class="hot"> 今天+<?php echo $sh_gj_num_today_add;?></font></div></div>
                    <em class="hot"><i></i><span>今天需跟进</span><b><?php echo $sh_gj_num_today_nexttime;?></b><a href="crm_user_bbs.php?k=sh&nexttime=1" class="btn size1 HONG2">去跟进</a></em>
                    <em><i></i><span>7天未跟进</span><b><?php echo $sh_gj_num_7_addno;?></b><a href="crm_user_bbs.php?k=sh&addtime=1" class="btn size1 BAI">去跟进</a></em>
                    <em><i></i><span>过期未跟进</span><b><?php echo $sh_gj_num_nexttime_expire;?></b><a href="crm_user_bbs.php?k=sh&nexttime=4" class="btn size1 BAI">去跟进</a></em>
                </li>
            </ul>
        </div>
        
        
        <!--主管售前待分配-->
        <?php
		if(in_array('crm_hn_utask_sq_add',$QXARR)){
		$rt = $db->query("SELECT id,sex,grade,photo_s,nickname,truename FROM ".__TBL_USER__." WHERE ".$SQL." AND crm_ugrade=0 AND hnid=0 ORDER BY hntime DESC,id DESC LIMIT 12");
		//
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
		?>
		<div class="box">
			<h5><i class="ico tiaose S18">&#xe861;</i> 我的任务（售前分配）<font class="S14 C999" style="font-weight:normal">(录入未分配售前红娘，请尽快分配开发客户)</font>
            <em class="titlebox"><span>未分配<font><?php echo $sq_hnid0_num;?></font>人</span>　<button class="btn HONG2 size1" onclick="zeai.openurl('crm_sq.php?ifhnid=ifhnid0')">更多售前未分配</button></em>
            
            <span class="line"></span></h5>
            <dl class="ulist">
			<?php
                for($i=1;$i<=$total;$i++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows) break;
                    $uid   = $rows['id'];
                    $sex    = $rows['sex'];
                    $grade  = $rows['grade'];
                    $photo_s= $rows['photo_s'];
                    $nickname= dataIO($rows['nickname'],'out');
                    $truename= dataIO($rows['truename'],'out');
					$nickname=(!empty($truename))?$truename:$nickname;
					$nickname=(!empty($nickname))?$nickname:'uid:'.$uid;
                    $photo_s= $rows['photo_s'];
                    $photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					$title2 = (!empty($nickname))?urlencode(trimhtml($nickname).'/'.$uid):$uid;?>
                    <li>
                        <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_ss"><img src="<?php echo $photo_s_url; ?>" class='m'></a>
                        <span><?php echo uicon($sex.$grade);?><font class="picmiddle"><?php echo $nickname;?></font></span>
                        <br><font class="C999">UID：<?php echo $uid;?></font>
                        <div style="margin-top:8px"><a href="javascript:;" photo_s_url="<?php echo $photo_s_url;?>" class="btn size1 BAI hnadd" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>">分配售前</a></div>
                    </li><?php
			 	}
				?>
            </dl>
            <div class="clear"></div>
        </div>
		<script>
        zeai.listEach('.hnadd',function(obj){
            obj.onclick = function(){
                var uid = parseInt(obj.getAttribute("uid")),
                title2 = obj.getAttribute("title2"),
                photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
                photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
                zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】分配售前红娘','crm_hn_utask_add.php?t=1&ulist='+uid,600,500);
            }
        });
        </script>        
		<?php }}?>
        
        <!--主管售后待分配-->
        <?php
		if(in_array('crm_hn_utask_sh_add',$QXARR)){
		$rt = $db->query("SELECT id,sex,grade,photo_s,nickname,truename FROM ".__TBL_USER__." WHERE ".$SQL." AND crm_ugrade>0 AND hnid2=0 ORDER BY hntime DESC,id DESC LIMIT 12");
		//
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
		?>
		<div class="box">
			<h5><i class="ico tiaose S18">&#xe861;</i> 我的任务（售后分配）<font class="S14 C999" style="font-weight:normal">(未分配售后红娘，请尽快分配服务客户)</font>
            <em class="titlebox"><span>未分配<font><?php echo $sh_hnid20_num;?></font>人</span>　<button class="btn HONG2 size1" onclick="zeai.openurl('crm_sh.php?ifhnid=ifhnid0')">更多售后未分配</button></em>
            
            <span class="line"></span></h5>
            <dl class="ulist">
			<?php
                for($i=1;$i<=$total;$i++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows) break;
                    $uid   = $rows['id'];
                    $sex    = $rows['sex'];
                    $grade  = $rows['grade'];
                    $photo_s= $rows['photo_s'];
                    $nickname= dataIO($rows['nickname'],'out');
                    $truename= dataIO($rows['truename'],'out');
					$nickname=(!empty($truename))?$truename:$nickname;
					$nickname=(!empty($nickname))?$nickname:'uid:'.$uid;
                    $photo_s= $rows['photo_s'];
                    $photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					$title2 = (!empty($nickname))?urlencode(trimhtml($nickname).'/'.$uid):$uid;?>
                    <li>
                        <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_ss"><img src="<?php echo $photo_s_url; ?>" class='m'></a>
                        <span><?php echo uicon($sex.$grade);?><font class="picmiddle"><?php echo $nickname;?></font></span>
                        <br><font class="C999">UID：<?php echo $uid;?></font>
                        <div style="margin-top:8px"><a href="javascript:;" photo_s_url="<?php echo $photo_s_url;?>" class="btn size1 BAI hnadd2" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>">分配售后</a></div>
                    </li><?php
			 	}
				?>
            </dl>
            <div class="clear"></div>
        </div>
		<script>
		zeai.listEach('.hnadd2',function(obj){
			obj.onclick = function(){
				var uid = parseInt(obj.getAttribute("uid")),
				title2 = obj.getAttribute("title2"),
				photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
				photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
				zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】分配售后红娘','crm_hn_utask_add.php?t=3&ulist='+uid,600,500);
			}
		});
        </script>        
		<?php }}?>
   
		<?php
		}
/********************************* 我的售前 **************************************/
		if(strstr($session_crmkind,'sq') && (!strstr($session_crmkind,'adm') && !in_array('crm',$QXARR))   ){?>
        <!--我的签单-->
		<div class="box">
			<h5><i class="ico tiaose S18">&#xe63a;</i> 我的签单<span class="line"></span></h5>
            <ul class="cols4">
				<?php
                //我的 - 签单总量
                $rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE  htflag=1 ".$SQLA." AND admid=$session_uid ");
                $row=$db->fetch_array($rt,'name');
                $HTflag1 = intval($row['price']);
                //我的 - 签单 - 今天
                $rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE  htflag=1 ".$SQLA." AND date_format(from_unixtime(addtime),'%Y-%m-%d') = '$today' AND admid=$session_uid ");
                $row=$db->fetch_array($rt,'name');
                $HTflag1_day = intval($row['price']);
				//我的 - 签单 - 本周
                $rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE  htflag=1 ".$SQLA." AND YEARWEEK(date_format(from_unixtime(addtime),'%Y-%m-%d')) = YEARWEEK(now())  AND admid=$session_uid");
                $row=$db->fetch_array($rt,'name');
                $HTflag1_week = intval($row['price']);
				//我的 - 签单 - 上周
                $rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE  htflag=1 ".$SQLA." AND (YEARWEEK(date_format(addtime,'%Y-%m-%d')) = YEARWEEK(now())-1)  AND admid=$session_uid");
                $row=$db->fetch_array($rt,'name');
                $HTflag1_preweek = intval($row['price']);
				//我的 - 签单 - 本月
                $rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE  htflag=1 ".$SQLA." AND date_format(from_unixtime(addtime),'%Y-%m') = '$month'  AND admid=$session_uid");
                $row=$db->fetch_array($rt,'name');
                $HTflag1_month = intval($row['price']);
				//我的 - 签单 - 上月
                $rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE  htflag=1 ".$SQLA." AND (date_format(from_unixtime(addtime),'%Y-%m')=date_format(DATE_SUB(curdate(), INTERVAL 1 MONTH),'%Y-%m'))  AND admid=$session_uid  ");
                $row=$db->fetch_array($rt,'name');
                $HTflag1_premonth = intval($row['price']);
				//已签约客户   未签约
                $sq_crm_ugrade0_num = $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade=0 AND hnid=$session_uid");
                $sq_crm_ugrade1_num = $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>=1 AND hnid=$session_uid");
                ?>
                <li><h6><font class="h5b">签单总量</font>（合同已审）</h6>￥<b><?php echo $HTflag1;?></b><span class="liRnum"><font>今天</font><b><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTflag1_day;?></font></b></span></li>
                <li><h6><font class="h5b">本周签单</font>（合同已审）</h6>￥<b><?php echo $HTflag1_week;?></b><span class="liRnum"><font>上周</font><b><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTflag1_preweek;?></font></b></span></li>
                <li><h6><font class="h5b">本月签单</font>（合同已审）</h6>￥<b><?php echo $HTflag1_month;?></b><span class="liRnum"><font>上月</font><b><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTflag1_premonth;?></font></b></span></li>
                <li>
                    <h6><font class="h5b">已签约客户</font>（付款已审）</h6><b><?php echo $sq_crm_ugrade1_num;?></b>
                    <span class="liRnum">未签约<b><?php echo $sq_crm_ugrade0_num;?></b></span>
                </li>
            </ul>
        </div>
        <!--售前跟进-->
		<div class="box">
			<h5><i class="ico tiaose S18">&#xe861;</i> 售前跟进<span class="line"></span></h5>
            <ul class="cols3">
				<?php
				//售前跟进-今天记录 addtime
				$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade=0 AND date_format(from_unixtime(B.addtime),'%Y-%m-%d') = '$today'  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_today_add_my = $roww[0];
				//售前跟进-超过7天未跟进 addtime
				$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade=0 AND (".ADDTIME." - B.addtime  ) > 604800  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_7_addno_my = $roww[0];
				//售前跟进-超过30天未跟进 addtime
				$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade=0 AND (".ADDTIME." - B.addtime  ) > 2592000  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_30_addno_my = $roww[0];
                ?>
                <li>
                    <h6><font class="h5b">今天已跟进</font>（今天跟进的记录）</h6><b style="color:#EE5A4E"><?php echo $sq_gj_num_today_add_my;?></b> 人<button class="btn HONG2 size1" onclick="zeai.openurl('crm_user_bbs.php?k=sq&addtime=4&ifmy=1')">去查看</button><br><br>
                    <h6><font class="h5b">7天未跟进</font>（超过7天未跟进）</h6><b><?php echo $sq_gj_num_7_addno_my;?></b> 人<button class="btn HONG2 size1" onclick="zeai.openurl('crm_user_bbs.php?k=sq&addtime=1&ifmy=1')">去跟进</button><br><br>
                	<h6><font class="h5b">30天未跟进</font>（超过30天未跟进）</h6><b><?php echo $sq_gj_num_30_addno_my;?></b> 人<button class="btn HONG2 size1" onclick="zeai.openurl('crm_user_bbs.php?k=sq&addtime=2&ifmy=1')">去跟进</button><br><br>
                </li>
				<?php 
				//售前跟进-今天需跟进 nexttime
				$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade=0 AND date_format(from_unixtime(B.nexttime),'%Y-%m-%d') = '$today'  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_today_nexttime_my = $roww[0];
				//售前跟进-明天需跟进 nexttime
				$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade=0 AND ( TO_DAYS(from_unixtime(B.nexttime))-TO_DAYS(NOW()) = 1 )  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_tomorrow_nexttime_my = $roww[0];
				//售前跟进-过期未跟进 nexttime
				$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade=0 AND B.nexttime<".ADDTIME."  ".$SQLU);
				$roww = $db->fetch_array($rtt);
				$sq_gj_num_nexttime_expire_my = $roww[0];
				?>
                <li>
                    <h6><font class="h5b">今天需跟进</font>（请立即更进）</h6><b style="color:#EE5A4E"><?php echo $sq_gj_num_today_nexttime_my;?></b> 人<button class="btn HONG2 size1" onclick="zeai.openurl('crm_user_bbs.php?k=sq&nexttime=1&ifmy=1')">去跟进</button><br><br>
                	<h6><font class="h5b">明天需跟进</font>（请明天需要跟进）</h6><b><?php echo $sq_gj_num_tomorrow_nexttime_my;?></b> 人<button class="btn HONG2 size1" onclick="zeai.openurl('crm_user_bbs.php?k=sq&nexttime=2&ifmy=1')">去跟进</button><br><br>
                    <h6><font class="h5b">过期未跟进</font>（请立即更进）</h6><b><?php echo $sq_gj_num_nexttime_expire_my;?></b> 人<button class="btn HONG2 size1" onclick="zeai.openurl('crm_user_bbs.php?k=sq&nexttime=4&ifmy=1')">去查看</button><br><br>
                </li>
                <li>
					<?php
                    $intentionARR = json_decode($_CRM['bbs_intention'],true);
                    if (count($intentionARR) >= 1 && is_array($intentionARR)){?>
                    	<h6><font class="h5b">客户意向</font>（人）</h6>
                    	<?php
                        foreach ($intentionARR as $V){
                            $intention_id    = intval($V['i']);
                            $intention_title = $V['v'];
							$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade=0 AND B.intention=".$intention_id.$SQLU);
							$roww = $db->fetch_array($rtt);
							$intention_num = $roww[0];?>
                            <em><i></i><span><?php echo $intention_title;?></span><b><?php echo $intention_num;?></b><a href="crm_user_bbs.php?k=sq&intention=<?php echo $intention_id;?>&ifmy=1" class="btn size1 BAI">去查看</a></em><?php
                        }
                    }
                    ?>
                </li>
            </ul>
        </div>
        <!--我的客户-->
		<div class="box">
			<h5><i class="ico tiaose S18">&#xe861;</i> 售前任务
            
            <font class="S14 C999" style="font-weight:normal"> (新分配客户，请尽快开发促成签单)</font>
            <em class="titlebox"><span>未签约<font><?php echo $sq_crm_ugrade0_num;?></font>人</span>　<button class="btn HONG2 size1" onclick="zeai.openurl('crm_sq.php?ifmy=1')">立即开发客户</button></em>
            
            <span class="line"></span></h5>
            <dl class="ulist">
			<?php
			$rt = $db->query("SELECT id,sex,grade,photo_s,nickname,truename,hntime,crm_ugrade,crm_usjtime1 FROM ".__TBL_USER__." WHERE hnid=".$session_uid." ORDER BY hntime DESC,id DESC LIMIT 20");
			$total = $db->num_rows($rt);
			if ($total <= 0 ) {
				echo "<br><br><div class='nodataicoS'><i></i>暂无分配";
				echo "</div><br><br>";
			} else {
                for($i=1;$i<=$total;$i++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows) break;
                    $uid   = $rows['id'];
                    $sex    = $rows['sex'];
                    $grade  = $rows['grade'];
                    $photo_s= $rows['photo_s'];
                    $nickname= dataIO($rows['nickname'],'out');
                    $truename= dataIO($rows['truename'],'out');
					$nickname=(!empty($nickname))?$nickname:$truename;
					$nickname=(!empty($nickname))?$nickname:'uid:'.$uid;
                    $photo_s= $rows['photo_s'];
					$hntime= $rows['hntime'];
					$crm_ugrade= intval($rows['crm_ugrade']);
					$crm_usjtime1= intval($rows['crm_usjtime1']);
                    $photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					$title2 = (!empty($nickname))?urlencode(trimhtml($nickname).'/'.$uid):$uid;
                ?>
                <li>
                    <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_ss"><img src="<?php echo $photo_s_url; ?>" class='m'></a>
                    <span><?php echo uicon($sex.$grade);?><font class="picmiddle"><?php echo $nickname;?></font></span>
                    <span class="block lineH200" style="margin-top:5px">
                    <?php
					if ($crm_ugrade >= 1){
						echo '<font class="C090">';
						echo crm_ugrade_title($crm_ugrade);
						echo '<br>签约于 '.YmdHis($crm_usjtime1,'Ymd').'</font>';
					}else{
						echo '(未签约)<br>分配于 '.YmdHis($hntime,'Ymd');
					}
					?>
                    </span>
                </li>
             <?php }}?>
            </dl>
            <div class="clear"></div>
        </div>
        <?php }?>
        <!--我的售前结束-->
        
        
		<?php
/*********************************************************************** 我的售后 ****************************************************************************/
		if(strstr($session_crmkind,'sh') && (!strstr($session_crmkind,'adm') && !in_array('crm',$QXARR)) ){?>
       	 <!--我的售后-->
		<div class="box">
			<h5><i class="ico tiaose S18">&#xe60e;</i> 我的售后<span class="line"></span></h5>
            <ul class="cols2">
                <li>
					<?php 
					//我的售后客户
					$sh_num_my = $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>=1 AND hnid2=$session_uid");
					//售后客户 - 未约见
					$rt=$db->query("SELECT COUNT(*) FROM ".__TBL_USER__." WHERE hnid2=".$session_uid." AND crm_ugrade>=1 ".$SQLA." AND id NOT IN (SELECT uid FROM ".__TBL_CRM_MATCH__." GROUP BY uid)   ");
					$row = $db->fetch_array($rt,"num");
					$sh_meet_num_no_my = $row[0];
					//售后客户 - 未牵线
					$rt=$db->query("SELECT COUNT(*) FROM ".__TBL_USER__." WHERE hnid2=".$session_uid." AND crm_ugrade>=1 ".$SQLA." AND id NOT IN (SELECT senduid FROM ".__TBL_QIANXIAN__." GROUP BY senduid)   ");
					$row = $db->fetch_array($rt,"num");
					$sh_qx_num_no_my = $row[0];
					//我的客户 - 客户过期
					$sh_expire_my = $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>=1 AND hnid2=".$session_uid." AND crm_usjtime2 < ".ADDTIME);
					//我的客户 - 7天到期
					$sh_expire7_my = $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>=1 AND hnid2=".$session_uid."  AND (crm_usjtime2 - ".ADDTIME.") < 604800 ");
					//我的客户 - 30天到期
					$sh_expire30_my = $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>=1 AND hnid2=".$session_uid."  AND (crm_usjtime2 - ".ADDTIME.") < 2592000 ");
					
					
					//我的客户 - 售后跟进
					$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid2=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade>0 ".$SQLU);$roww = $db->fetch_array($rtt,"num");
					$sh_gj_num_my = $roww[0];
					//我的客户 - 售后今天记录 addtime
					$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid2=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade>0 AND date_format(from_unixtime(B.addtime),'%Y-%m-%d') = '$today'  ".$SQLU);$roww = $db->fetch_array($rtt,"num");
					$sh_gj_num_today_add_my = $roww[0];
					//我的客户 - 售后今天需跟进 nexttime
					$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid2=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade>0 AND date_format(from_unixtime(B.nexttime),'%Y-%m-%d') = '$today'  ".$SQLU);$roww = $db->fetch_array($rtt,"num");
					$sh_gj_num_today_nexttime_my = $roww[0];
					//我的客户 - 售后超过7天未跟进 addtime
					$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid2=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade>0 AND (".ADDTIME." - B.addtime  ) > 604800  ".$SQLU);$roww = $db->fetch_array($rtt,"num");
					$sh_gj_num_7_addno_my = $roww[0];
					//我的客户 - 过期未跟进 nexttime
					$rtt = $db->query("SELECT COUNT(distinct(B.uid)) FROM ".__TBL_CRM_BBS__." B,".__TBL_USER__." U".$bbsN1." WHERE U.hnid2=".$session_uid.$bbsN2." AND B.uid=U.id AND U.crm_ugrade>0 AND B.nexttime<".ADDTIME."  ".$SQLU);$roww = $db->fetch_array($rtt,"num");
					$sh_gj_num_nexttime_expire_my = $roww[0];
					?>
                    <h6><font class="h5b">我的客户</font>（分配售后已签约客户）</h6><b><?php echo $sh_num_my;?></b> 人
                    <span class="liRnum"><font>未约见</font><b><?php echo $sh_meet_num_no_my;?></b><font>人</font></span>
                    <span class="liRnum"><font>未牵线</font><b><?php echo $sh_qx_num_no_my;?></b><font>人</font></span>
                    <a href="crm_sh.php?ifmy=1" class="btn size1 HONG2">去服务</a><br><br>
                    
                    <h6><font class="h5b">客户过期</font>（已签约服务时间过期）</h6><b><?php echo $sh_expire_my;?></b> 人
                    <span class="liRnum"><font>7天到期</font><b><?php echo $sh_expire7_my;?></b><font>人</font></span>
                    <span class="liRnum"><font>30天到期</font><b><?php echo $sh_expire30_my;?></b><font>人</font></span>
                    <a href="crm_sh.php?ifmy=1&g=30" class="btn size1 HONG2">去续签</a><br><br>
                    
                    
                    <!--售后跟进-->
					<h6><font class="h5b">售后跟进</font>（售后跟进小计）</h6><b><?php echo $sh_gj_num_my;?></b> 人<font class="hot FR" style="margin-top:7px"> 今天+<?php echo $sh_gj_num_today_add_my;?></font>
                    <em class="hot"><i></i><span>今天需跟进</span><b><?php echo $sh_gj_num_today_nexttime_my;?></b><a href="crm_user_bbs.php?ifmy=1&k=sh&nexttime=1" class="btn size1 HONG2">去跟进</a></em>
                    <em><i></i><span>7天未跟进</span><b><?php echo $sh_gj_num_7_addno_my;?></b><a href="crm_user_bbs.php?ifmy=1&k=sh&addtime=1" class="btn size1 BAI">去跟进</a></em>
                    <em><i></i><span>过期未跟进</span><b><?php echo $sh_gj_num_nexttime_expire_my;?></b><a href="crm_user_bbs.php?ifmy=1&k=sh&nexttime=4" class="btn size1 BAI">去跟进</a></em>
                </li>
            	<li>
                	<!--约见工单-->
                    <?php
					//跟进好像要有一个跟进状态，和约见工单状态差不多，完成了就不提醒，必须为每一次跟进设立一个状态，要不然会员服务结束了，永远提示 过期或需跟进信息
					
					//我的售后 - 约见工单 总
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.uid=U.id AND U.crm_flag<>3".$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num_my = $roww[0];
					//我的售后 约见工单 今天
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.uid=U.id AND U.crm_flag<>3 AND date_format(from_unixtime(B.addtime),'%Y-%m-%d') = '$today'  ".$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num_today_my = $roww[0];
					
					//我的售后 今天需约见
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.uid=U.id AND U.crm_flag<>3 AND date_format(from_unixtime(nexttime),'%Y-%m-%d') = '$today'  ".$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num_today_nexttime_my = $roww[0];
					
					//我的售后 超过7天未约见
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.uid=U.id AND U.crm_flag<>3 AND (".ADDTIME." - addtime) > 604800  ".$SQLU);$roww = $db->fetch_array($rtt);
					$sh_yj_num_7_addno_my = $roww[0];
					//我的售后 过期未约见
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.uid=U.id AND U.crm_flag<>3 AND nexttime < ".ADDTIME.$SQLU);$roww = $db->fetch_array($rtt);
					$yj_num_nexttime_expire_my = $roww[0];
					?>  
					<h6><font class="h5b">约见工单</font>（客户见面统计）</h6><b><?php echo $yj_num_my;?></b> 次<font class="hot FR" style="margin-top:7px"> 今天+<?php echo $yj_num_today_my;?></font>
					<?php 
					//我的售后 约见分类
                    if (count($meet_flagARR) >= 1 && is_array($meet_flagARR)){
                        foreach ($meet_flagARR as $V){
                            $meet_flag_id    = intval($V['i']);
                            $meet_flag_title = $V['v'];
							$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.uid=U.id AND U.crm_flag<>3 AND meet_flag = ".$meet_flag_id.$SQLU);$roww = $db->fetch_array($rtt);
							$meet_num = $roww[0];?>
                            <span class="liRnum"><font><?php echo $meet_flag_title;?></font><b><?php echo $meet_num;?></b></span><?php
                        }
                    }
					?>                    
                    <em class="hot"><i></i><span>今天需约见</span><b><?php echo $yj_num_today_nexttime_my;?></b><a href="crm_user_meet.php?mt_nexttime=1&ifmy=1" class="btn size1 HONG2">去约见</a></em>
                    <em><i></i><span>7天未约见</span><b><?php echo $sh_yj_num_7_addno_my;?></b><a href="crm_user_meet.php?mt_time=1&ifmy=1" class="btn size1 BAI">去约见</a></em>
                    <em><i></i><span>过期未约见</span><b><?php echo $yj_num_nexttime_expire_my;?></b><a href="crm_user_meet.php?mt_nexttime=4&ifmy=1" class="btn size1 BAI">去约见</a></em>
                	<!--客户牵线-->
                    <?php 
					//我的售后 - 客户牵线 - 总
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_QIANXIAN__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.senduid=U.id AND U.crm_flag<>3 ".$SQLU);
					$roww = $db->fetch_array($rtt);
					$qx_num_my = $roww[0];
					//我的售后 - 客户牵线 - 今天
					$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_QIANXIAN__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.senduid=U.id AND U.crm_flag<>3 AND date_format(from_unixtime(B.addtime),'%Y-%m-%d') = '$today'   ".$SQLU);
					$roww = $db->fetch_array($rtt);
					$qx_num_today_my = $roww[0];
					?>
                	<br>
					<h6><font class="h5b">客户牵线</font>（客户牵线统计）</h6><b><?php echo $qx_num_my;?></b> 次<font class="hot FR" style="margin-top:7px"> 今天+<?php echo $qx_num_today_my;?></font>
					<?php 
                    if (count($qx_flagARR) >= 1 && is_array($qx_flagARR)){
                        foreach ($qx_flagARR as $V){
                            $qx_id    = intval($V['i']);
                            $qx_title = $V['v'];
							$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_QIANXIAN__." B,".__TBL_USER__." U WHERE U.hnid2=".$session_uid." AND B.senduid=U.id AND U.crm_flag<>3 AND B.flag=".$qx_id.$SQLU);
							$roww = $db->fetch_array($rtt);
							$qx_num = $roww[0];
							?>
                            <em><i></i><span><?php echo $qx_title;?></span><b><?php echo $qx_num;?></b><a href="u_qianxian.php?qxflag=<?php echo $qx_id;?>&ifmy=1&k=crmqx" class="btn size1 BAI">去查看</a></em>
                            <?php
                        }
                    }
					?>                    
                </li>
            </ul>
        </div>
        
        
        
		<div class="box">
			<h5><i class="ico tiaose S18">&#xe648;</i> 售后任务<font class="S14 C999" style="font-weight:normal"> (新分配客户，请尽快牵线/跟进/约见，若服务成功请联系主管修改客户状态为<?php echo crm_arr_title($crm_flagARR,3);?>)</font><span class="line"></span>
            <em class="titlebox"><span>未成功<font><?php echo $db->COUNT(__TBL_USER__,$SQL." AND crm_ugrade>0 AND hnid2=$session_uid AND crm_flag<>3");
;?></font>人</span>　<button class="btn HONG2 size1" onclick="zeai.openurl('crm_sh.php?ifmy=1')">立即服务客户</button></em>
            </h5>
            <dl class="ulist">
			<?php
			$rt = $db->query("SELECT id,sex,grade,photo_s,nickname,truename,hntime2,crm_flag FROM ".__TBL_USER__." WHERE hnid2=".$session_uid." AND crm_ugrade>0 ORDER BY hntime2 DESC,id DESC");
			$total = $db->num_rows($rt);
			if ($total <= 0 ) {
				echo "<br><br><div class='nodataicoS'><i></i>暂无分配";
				echo "</div><br><br>";
			} else {
                for($i=1;$i<=$total;$i++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows) break;
                    $uid   = $rows['id'];
                    $sex    = $rows['sex'];
                    $grade  = $rows['grade'];
                    $photo_s= $rows['photo_s'];
                    $nickname= dataIO($rows['nickname'],'out');
                    $truename= dataIO($rows['truename'],'out');
					$nickname=(!empty($nickname))?$nickname:$truename;
					$nickname=(!empty($nickname))?$nickname:'uid:'.$uid;
                    $photo_s= $rows['photo_s'];
					$hntime2 = intval($rows['hntime2']);
					$crm_flag= intval($rows['crm_flag']);
                    $photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					$title2 = (!empty($nickname))?urlencode(trimhtml($nickname).'/'.$uid):$uid;
                ?>
                <li>
                    <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_ss"><img src="<?php echo $photo_s_url; ?>" class='m'></a>
                    <span><?php echo uicon($sex.$grade);?><font class="picmiddle"><?php echo $nickname;?></font></span>
                    <span class="block lineH200" style="margin-top:5px">
                    <?php
					$crm_flagT = crm_arr_title($crm_flagARR,$crm_flag);
					$hntime2T  = YmdHis($hntime2,'Ymd');
					if ($crm_flag == 1){
						echo '<font class="C090">'.$crm_flagT.'</font>';
						echo '<br><font class="C999">分配于 '.$hntime2T.'</font>';
					}else{
						echo '<font class="C999">'.$crm_flagT.'</font>';
						echo '<br><font class="C999">分配于 '.$hntime2T.'</font>';
					}
					?>
                    </span>
                </li>
             <?php }}?>
            </dl>
            <div class="clear"></div>
        </div>
        
		<?php
		 }
/*********************************************************************** 我的合同 ***********************************************************************/
		if(strstr($session_crmkind,'ht') && (!strstr($session_crmkind,'adm') && !in_array('crm',$QXARR)) ){?>
        <div class="box">
          <h5><i class="ico tiaose S18">&#xe656;</i> 合同待办统计<span class="line"></span><em class="titlebox"><button class="btn HONG2 size1" onclick="zeai.openurl('crm_ht.php?submitok=add')">录入新合同</button></em></h5>
            <ul class="cols3">
                <li>
                    <h6><font class="h6b">合同总数</font>（全部合同总数）</h6><b><?php echo $HTnum_total;?></b> 份<span class="liRnum"><font>今天</font><b style="color:#EE5A4E"><font><?php echo $HTnum_totay;?></font></b></span>
                    <button class="btn HONG2 size1" onclick="zeai.openurl('crm_ht.php')">去查看</button><br><br>
                    <h6><font class="h6b">待审合同</font>（未审核合同）</h6><b><?php echo $HTnum_flag0;?></b> 份<button class="btn HONG2 size1" onclick="zeai.openurl('crm_ht.php?t=payflag0')">去审核</button>
                </li>
                <li>
                	<h6><font class="h6b">7天内签单</font>（已审核合同-7天内）</h6><b><?php echo $db->COUNT(__TBL_CRM_HT__,"htflag=1".$SQLA." AND (    UNIX_TIMESTAMP() - addtime) <= 604800   ");?></b> 份<br><br>
                	<h6><font class="h6b">已过期</font>（合同服务到期时间-已过期）</h6><b><?php echo $HT_expire;?></b> 份<button class="btn HONG2 size1" onclick="zeai.openurl('crm_ht.php?gq=gq_1')">去查看</button>
                </li>
                <li>
                	<h6><font class="h6b">本月签单数</font>（已审核合同-本月内）</h6><b><?php echo $HTnum_flag1;?></b> 份<br><br>
                	<h6><font class="h6b">30天内到期</font>（合同时间-30天内到期）</h6><b><?php echo $HTnum_expire_30;?></b> 份
                    <span class="liRnum"><font>7天内到期</font><b><font><?php echo $HTnum_expire_7;?></font></b></span>
                    <button class="btn HONG2 size1" onclick="zeai.openurl('crm_ht.php?gq=gq30')">去查看</button>
                </li>
            </ul>
        </div>
		<?php
		}
/*********************************************************************** 我的财务 ***********************************************************************/
		if(strstr($session_crmkind,'cw') && (!strstr($session_crmkind,'adm') && !in_array('crm',$QXARR)) ){?>
			<div class="box"><h5><i class="ico tiaose S18">&#xe63a;</i> 财务待办统计<span class="line"></span></h5>
            <ul class="cols2">
				<?php 
				//去年
				$year_last = intval($year-1);
				$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=1 ".$SQLA." AND htflag=1 AND date_format(from_unixtime(addtime),'%Y') = '$year_last' ");
				$row=$db->fetch_array($rt,'name');
				$HTprice_year_last = intval($row['price']);
                
				//上月
				$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=1 ".$SQLA." AND htflag=1 AND (   date_format(from_unixtime(addtime),'%Y-%m')=date_format(DATE_SUB(curdate(), INTERVAL 1 MONTH),'%Y-%m')   )");
				$row=$db->fetch_array($rt,'name');
				$HTprice_monthr_last = intval($row['price']);
				
				//昨天
				$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=1 ".$SQLA." AND htflag=1  AND ( TO_DAYS(from_unixtime(addtime))-TO_DAYS(NOW()) = -1 )  ");
				$row=$db->fetch_array($rt,'name');
				$HTprice_yesterday = intval($row['price']);
				
				//总营收
				$rt=$db->query("SELECT SUM(price) AS price FROM ".__TBL_CRM_HT__." WHERE payflag=1 AND htflag=1".$SQLA);
				$row=$db->fetch_array($rt,'name');
				$HTprice_total = intval($row['price']);
                ?>
                <li>
                    <h6><font class="h6b">总营收</font>（已审金额）</h6>￥<b><?php echo $HTprice_total;?></b> 元
                    <span class="liRnum"><font>今年</font><b style="color:#EE5A4E"><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTprice_year;?></font></b></span>
                    <span class="liRnum"><font>去年</font><b><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTprice_year_last;?></font></b></span><br><br>
                    
                    <h6><font class="h6b">本月营收</font>（已审金额）</h6>￥<b style="color:#EE5A4E"><?php echo $HTprice_month;?></b> 元
                    <span class="liRnum"><font>上月</font><b><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTprice_monthr_last;?></font></b></span><br><br>
                </li>
                <li>
                    <h6><font class="h6b">今天营收</font>（已审金额）</h6>￥<b style="color:#EE5A4E"><?php echo $HTprice_today;?></b> 元
                    <span class="liRnum"><font>昨天</font><b><font style="font-size:14px;font-weight:normal"> ￥</font><font><?php echo $HTprice_yesterday;?></font></b></span><br><br>

                	<h6><font class="h6b">待审付款</font>（合同）</h6>￥<b><?php echo $HTprice_flag0;?></b> 元　<button class="btn HONG2 size1" onclick="zeai.openurl('crm_ht.php?t=payflag0')">去审核</button>
               	</li>
            </ul>
        </div>
        
        <?php }?>
	</div>
	<div class="RR">
		<?php if(!strstr($session_crmkind,'ht') && !strstr($session_crmkind,'cw')  || strstr($session_crmkind,'adm')   ){?>
        <!--我的资源-->
        <div class="box">
            <h5><i class="ico tiaose S18">&#xe63e;</i> 我的资源<span class="line"></span><span class="more"><a href="crm_user.php?ifmy_admid=1">更多</a></span></h5>
			<div class="user">
                <?php
                $rt=$db->query("SELECT id,sex,grade,photo_s,nickname,truename,birthday,heigh,job,love FROM ".__TBL_USER__." WHERE admid=".$session_uid.$SQLA." ORDER BY id DESC LIMIT 5");
                $total = $db->num_rows($rt);
                if ($total <= 0) {
					echo "<div class='nodataico'><i></i>暂无资源<br>";
					echo '<a href="javascript:;" class="aQINGed" onClick="zeai.iframe(\'我的二维码名片\',\'hn_ewm.php?id='.$session_uid.'\',550,550)">我的二维码名片</a>';
					echo "</div>";
                }else{?>
                    <?php
                    for($i=1;$i<=$total;$i++) {
                        $rows = $db->fetch_array($rt,'name');
                        if(!$rows)break;
						$uid      = $rows['id'];
						$nickname = dataIO($rows['nickname'],'out');
						$truename = dataIO($rows['truename'],'out');
						$sex       = $rows['sex'];
						$grade     = $rows['grade'];
						$photo_s   = $rows['photo_s'];
						$birthday  = $rows['birthday'];
						$heigh = intval($rows['heigh']);
						$job   = intval($rows['job']);
						$love  = intval($rows['love']);
						$nickname=(!empty($nickname))?$nickname:$truename;
						$nickname=(!empty($nickname))?$nickname:'uid:'.$uid;
						$areatitle_str = (str_len($areatitle)>2)?'<li>'.$areatitle.'</li>':'';
						$heigh_str = ($heigh>0)?'<li>'.$heigh.'cm</li>':'';
						$age_str   = (empty($birthday) || $birthday =='0000-00-00')?'':'<li>'.getage($birthday).'岁</li>';
						$job_str   = (ifint($job))?'<li>'.udata('job',$job).'</li>':'';
						$love_str  = (ifint($love))?'<li>'.udata('love',$love).'</li>':'';
						$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
						$title2 = (!empty($nickname))?urlencode(trimhtml($nickname).'/'.$uid):$uid;
                        ?>
                        <dl>
                            <dt><a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_ss"><img src="<?php echo $photo_s_url; ?>" class='userm'></a></dt>
                            <dd><font><?php echo uicon($sex.$grade).$nickname;?></font><em><?php echo $love_str.$age_str.$heigh_str.$job_str;?></em></dd>
                            <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="btn HONG2 size1 photo_ss">去跟进</a>
                        </dl>
                        <?php
                    }
                }?>
			</div>
		</div>
        <?php }?>
        
        <?php if (strstr($session_crmkind,'adm') || in_array('crm',$QXARR)){?>
		<!--红娘资源榜-->
        <div class="box">
            <h5><i class="ico tiaose S18">&#xe6fd;</i> 红娘资源榜<span class="line"></span><span class="more"><a href="javascript:parent.zeai.iframe('我的二维码名片','hn_ewm.php?id=<?php echo $session_uid;?>',550,550);" class="ico C999" title='我的二维码名片' style="font-weight:normal;font-size:24px">&#xe611;</a></span></h5>
			<div class="bang">
                <?php
                $rt=$db->query("SELECT admid,COUNT(id) AS num FROM ".__TBL_USER__." WHERE kind<>4 AND admid>0 GROUP BY admid ORDER BY num DESC LIMIT 5");
                $total = $db->num_rows($rt);
                if ($total <= 0) {
					echo "<br><div class='nodataicoS'><i></i>暂无资源";
					echo "<br><br><a class='aQINGed' href='u_add.php'>录入客户</a>";
					echo "</div><br>";
                }else{?>
                    <?php
                    for($i=1;$i<=$total;$i++) {
                        $rows = $db->fetch_array($rt,'name');
                        if(!$rows)break;
                        $admid = $rows['admid'];
                        $hnnum = $rows['num'];
						$row = $db->ROW(__TBL_CRM_HN__,"truename,agenttitle","id=".$admid,"name");
						if ($row){
							$admname = dataIO($row['truename'],'out');
							$agenttitle = dataIO($row['agenttitle'],'out');
							$agenttitle=(!empty($agenttitle))?'【'.$agenttitle.'】':'';
						}
							
                        if ($i == 1){
                            $ico = '<i class="ico i1">&#xe638;</i>';
                        }elseif($i == 2){
                            $ico = '<i class="ico i2">&#xe638;</i>';
                        }elseif($i == 3){
                            $ico = '<i class="ico i3">&#xe638;</i>';
                        }else{
                            //$ico = $i;
							$ico = '<i class="ico i4">&#xe638;</i>';
                        }
                        ?>
                        <dl>
                            <em><?php echo $ico;?></em>
                            <em><?php echo $agenttitle.$admname;?></em>
                            <em>ID：<?php echo $admid;?></em>
                            <em>名下 <?php echo $hnnum;?>人</em>
                        </dl>
                        <?php
                    }
                }?>
			</div>
		</div>
        <?php }?>

		<!--通知公告-->
        <div class="box">
            <h5><i class="ico tiaose S18">&#xe657;</i> 通知公告<span class="line"></span></h5>
            <div class="newsgg">
                <?php
				if(!in_array('crm',$QXARR)){
					$SQLn.=" AND agentid=$session_agentid ";
				}
                $rt=$db->query("SELECT id,title,addtime,agenttitle FROM ".__TBL_CRM_NEWS__." WHERE 1=1 ".$SQLn." ORDER BY px DESC,id DESC LIMIT 5");
                $total = $db->num_rows($rt);
                if ($total > 0){
                    for($i=1;$i<=$total;$i++){
                        $rowD = $db->fetch_array($rt,'name');
                        if(!$rowD) break;
                        $id = $rowD['id'];
                        $title = dataIO($rowD['title'],'out');
                        $agenttitle = dataIO($rowD['agenttitle'],'out');
                        $addtime   = YmdHis($rowD['addtime'],'Ymd');
						$agenttitle=(!empty($agenttitle))?'【'.$agenttitle.'】':'';?>
						<a href="javascript:;" class="newsdetail" newsid="<?php echo $id;?>"><font class="title"><i class="ico">&#xe657;</i><?php echo $agenttitle.$title;?></font><span><?php echo $addtime;?></span></a>
						<?php
					}
				}else{
					echo "<br><div class='nodataicoS'><i></i>暂无通知";
					echo "</div><br>";
				}
				?>
			</div>
		</div>

    </div>
    <div class="clear"></div>
</div>
<br><br>
<div class="clear"></div>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
parent.pageCRM_B(1);
parent.o('CB1').style.backgroundColor='#f0f0f0';
</script>
<script>
zeai.listEach('.photo_ss',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		urlpre = 'crm_user_detail.php?t=2&iframenav=1&uid='+uid;
		zeai.iframe('【'+decodeURIComponent(title2)+'】'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'\',this);" class="ed"><i class="ico add">&#xe7a0;</i> 个人主页</a>'+
		'</div>',urlpre);
	}
});
zeai.listEach('.newsdetail',function(obj){
	obj.onclick = function(){
		var newsid = parseInt(obj.getAttribute("newsid"));
		zeai.iframe('通知公告','crm_news.php?newsid='+newsid,700,550);
	}
});
</script>
<?php require_once 'bottomadm.php';?>
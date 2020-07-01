<?php
ob_start();
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('analyse_u',$QXARR))exit(noauth());
require_once ZEAI.'cache/udata.php';
header("Cache-control: private");
$SQL    = " kind<>4 ";
$SQL_10 = " kind<>4 AND myinfobfb>10 AND mob<>'' ";
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

switch ($submitok) {
	//用户总量增长分析
	case 'ajax_reg':
		$rt=$db->query("SELECT COUNT(id) AS num,from_unixtime(regtime,'%Y') AS year FROM ".__TBL_USER__." WHERE ".$SQL.$SQL_AGENT." GROUP BY year ORDER BY year DESC LIMIT 2");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$num  = $rows['num'];
				$year = $rows['year'];
				$Y[]=array('year'=>$year,'num'=>$num);
			}
		}
		if (count($Y) >= 1 && is_array($Y)){
			foreach ($Y as $V) {
				$year = $V['year'];
				$num  = $V['num'];
				$data=array();
				for($i=1;$i<=12;$i++) {
					$m  = ($i<10)?'0'.$i:$i;
					$YM = $year.'-'.$m;
					$totalnum = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND date_format(from_unixtime(regtime),'%Y-%m') = '$YM' ");
					$data[]=intval($totalnum);
				}
				$series[] = array('name'=>$year.'年（总'.$num.'人）','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));
			}
		}
		$categories=array('一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月');
		json_exit(array('categories'=>$categories,'series'=>$series));
	break;
	//用户男女占比分析
	case 'ajax_sex':
		$num1 = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 ");
		$num2 = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 ");
		$all = $num1+$num2;
		if($all>0){
			$bfb1=round($num1/$all,2)*100;
			$bfb2=round($num2/$all,2)*100;
		}else{
			$bfb1=0;
			$bfb2=0;
		}
		if($num1==0 || $num2==0){
			json_exit(array('flag'=>0));
		}else{
			json_exit(array('flag'=>1,'bfb1'=>$bfb1,'bfb2'=>$bfb2,'num1'=>$num1,'num2'=>$num2));
		}
	break;
	//用户婚姻状态分析
	case 'ajax_love':
		$_UDATA_love = json_decode($_UDATA['love'],true);
		$_UDATA_sex  = json_decode($_UDATA['sex'],true);
		if (count($_UDATA_love) >= 1 && is_array($_UDATA_love)){
			foreach ($_UDATA_love as $V) {
				$love     = intval($V['i']);
				$love_str = $V['v'];
				$categories[] = $love_str;
			}	
			$data = array();
			foreach ($_UDATA_love as $V2) {
				$love     = $V2['i'];
				$love_str = $V2['v'];
				$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 AND love=".$love);
				$data[] = intval($num);
			}
			$series[] = array('name'=>'男','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));
			$data = array();
			foreach ($_UDATA_love as $V2) {
				$love     = $V2['i'];
				$love_str = $V2['v'];
				$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 AND love=".$love);
				$data[] = intval($num);
			}
			$series[] = array('name'=>'女','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));
		}
		json_exit(array('categories'=>$categories,'series'=>$series));
	break;
	//用户学历分析
	case 'ajax_edu':
		$all = intval($db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND edu>0 "));
		$_UDATA_edu = json_decode($_UDATA['edu'],true);
		if (count($_UDATA_edu) >= 1 && is_array($_UDATA_edu)){
			foreach ($_UDATA_edu as $V) {
				$eduid    = intval($V['i']);
				$edutitle = $V['v'];
				$num      = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND edu=".$eduid);
				if($all>0){
					$bfb=round($num/$all,5)*100;
				}else{
					$bfb=0;
				}
				$data[] = array('name'=>$edutitle.'：'.$num.'人','y'=>$bfb);
			}
		}
		json_exit(array('data'=>$data));
	break;
	//用户学历分析VIP
	case 'ajax_edu_vip':
		$all = intval($db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND edu>0 AND grade>1 "));
		$_UDATA_edu = json_decode($_UDATA['edu'],true);
		if (count($_UDATA_edu) >= 1 && is_array($_UDATA_edu)){
			foreach ($_UDATA_edu as $V) {
				$eduid    = intval($V['i']);
				$edutitle = $V['v'];
				$num      = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND edu=".$eduid." AND grade>1 ");
				if($all>0){
					$bfb=round($num/$all,5)*100;
				}else{
					$bfb=0;
				}
				$data[] = array('name'=>$edutitle.'：'.$num.'人','y'=>$bfb);
			}
		}
		json_exit(array('data'=>$data));
	break;
	//房产
	case 'ajax_house':
		$_UDATA_house = json_decode($_UDATA['house'],true);
		if (count($_UDATA_house) >= 1 && is_array($_UDATA_house)){
			foreach ($_UDATA_house as $V) {
				$houseid    = intval($V['i']);
				$housetitle = $V['v'];
				$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND house =".$houseid);
				$categories[]=$housetitle;
				$arr[]=array("'".$housetitle."'".",".$num);
			}
			exit(encode_json($arr));
		}else{
			echo '';exit;	
		}
	break;
	//车
	case 'ajax_car':
		$all = intval($db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND car>0 "));
		$_UDATA_car = json_decode($_UDATA['car'],true);
		if (count($_UDATA_car) >= 1 && is_array($_UDATA_car)){
			foreach ($_UDATA_car as $V) {
				$carid    = intval($V['i']);
				$cartitle = $V['v'];
				$num      = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND car=".$carid);
				if($all>0){
					$bfb=round($num/$all,4)*100;
				}else{
					$bfb=0;
				}
				$data[] = array('name'=>$cartitle.'：'.$num.'人','y'=>$bfb);
			}
		}
		json_exit(array('data'=>$data));
	break;
	//年龄
	case 'ajax_age':
		//20岁以下
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <=19 )  ");
		$data[] = intval($num);
		//20~29岁
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= 20 ) AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <=29 )  ");
		$data[] = intval($num);
		//30~39岁
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= 30 ) AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <=39 )  ");
		$data[] = intval($num);
		//40~49岁
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= 40 ) AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <=49 )  ");
		$data[] = intval($num);
		//50岁及以上
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= 50 )  ");
		$data[] = intval($num);
		$series[] = array('name'=>'男','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));
		
		$data = array();
		//20岁以下
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <=19 )  ");
		$data[] = intval($num);
		//20~29岁
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= 20 ) AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <=29 )  ");
		$data[] = intval($num);
		//30~39岁
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= 30 ) AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <=39 )  ");
		$data[] = intval($num);
		//40~49岁
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= 40 ) AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <=49 )  ");
		$data[] = intval($num);
		//50岁及以上
		$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= 50 )  ");
		$data[] = intval($num);
		$series[] = array('name'=>'女','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));

		$categories = array('19岁及以下','20~29岁','30~39岁','40~49岁','50岁及以上');
		json_exit(array('categories'=>$categories,'series'=>$series));
	break;
	//VIP付费类型
	case 'ajax_vipkind':
		$urole = json_decode($_ZEAI['urole'],true);
        foreach($urole as $RV){
            $grade = intval($RV['g']);$t = $RV['t'];
            if($grade<=1)continue;
			$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 AND grade=".$grade);
			$data[] = intval($num);
			$categories[]=$t;
		}
		$series[] = array('name'=>'男','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));
		$data = array();
        foreach($urole as $RV){
            $grade = intval($RV['g']);
            if($grade<=1)continue;
			$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 AND grade=".$grade);
			$data[] = intval($num);
		}
		$series[] = array('name'=>'女','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));
		json_exit(array('categories'=>$categories,'series'=>$series));
	break;
	//帐号状态
	case 'ajax_flag':
		$_ZEAI['uflag']='[{"f":"1","t":"正常"},{"f":"-2","t":"隐藏"},{"f":"-1","t":"锁定"},{"f":"0","t":"未审"},{"f":"2","t":"注册未完成"}]';
		$uflag = json_decode($_ZEAI['uflag'],true);
        foreach($uflag as $RV){
            $flag = intval($RV['f']);$t = $RV['t'];
			$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1 AND flag=".$flag);
			$data[] = intval($num);
			$categories[]=$t;
		}
		$series[] = array('name'=>'男','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));
		$data = array();
        foreach($uflag as $RV){
            $flag = intval($RV['f']);
			$num = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2 AND flag=".$flag);
			$data[] = intval($num);
		}
		$series[] = array('name'=>'女','data'=>$data,'dataLabels'=>array('enabled'=>0,'allowOverlap'=>1,'style'=>array('fontWeight'=>'normal')));
		json_exit(array('categories'=>$categories,'series'=>$series));
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
.table0{width:-webkit-calc(100% - 40px);margin:13px auto 5px auto;}
.cbox{width:-webkit-calc(100% - 40px);margin:0 auto;background-color:#fff;border:#E1E6EB 1px solid;text-align:left;box-sizing:border-box;padding-bottom:15px;clear:both;overflow:auto;position:relative}
.cbox h5{width:100%;background-color:#f9f9f9;font-size:15px;font-weight:normal;border-bottom:#f5f5f5 1px solid;line-height:50px;height:50px;margin:0 0 10px 0;padding:0 15px;display:block;position:relative;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.cbox span.line,.cbox span.right_line{width:48px;border-bottom:#009688 2px solid;position:absolute;top:50px;left:35px}
.cbox span.right{position:absolute;top:0px;left:50%}
.cbox span.right_line{left:-webkit-calc(50% + 35px)}

.cbox ul{font-size:32px;padding:30px 0;width:18%;margin:10px 1%;background-color:#f9f9f9;text-align:center;float:left;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.cbox ul em{height:40px;line-height:40px}
.cbox ul em i{color:#009688}
.cbox ul div{font-size:14px;color:#999;margin-top:5px}
.cbox ul:nth-child(3) em{color:#4FA7FF}
.cbox ul:nth-child(3) em i{font-size:35px;color:#4FA7FF}
.cbox ul:nth-child(4) em{color:#FD66B5}
.cbox ul:nth-child(4) em i{font-size:33px;color:#FD66B5}
.cbox ul:nth-child(6) em{color:#EE5A4E}
.cbox ul:nth-child(6) em i{font-size:33px;color:#EE5A4E}


</style>
<script>
var jsfilename = 'analyse_u',sDATE1='<?php echo $sDATE1sql;?>',agentid=<?php echo intval($agentid);?>;
</script>
</head>
<body>
<div class="navbox">
    <a class='ed'>用户数据总分析</a>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<?php if(in_array('crm',$QXARR)){?>
<form name="form1" method="get" action="<?php echo SELF; ?>">
    <table class="table0">
    <tr>
    <td align="left">
        <?php
        $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
        $total2 = $db->num_rows($rt2);
        if ($total2 > 0) {?>
        <span class="textmiddle S14">按门店　</span><select name="agentid" class="size2 W200 picmiddle">
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
        
        <button type="submit" class="btn size2 picmiddle"><i class="ico">&#xe6c4;</i> 开始查询</button>
    </td>
    <td width="400" align="right" class="S14 C666" ></td>
    </tr>
    </table>
</form>
<?php }?>
<br>
<div class="cbox">
    <h5>用户付费/注册统计<span class="line"></span></h5>
    <ul>
		<?php $totalnum = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT);?>
        <em><i class="ico">&#xe645;</i> <?php echo $totalnum;?></em><div>用户总数（人）</div>
    </ul>
    <ul>
		<?php $totalnum = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=1");?>
        <em><i class="ico">&#xe60c;</i><?php echo $totalnum;?></em><div>男生总数（人）</div>
    </ul>
    <ul>
		<?php $totalnum = $db->COUNT(__TBL_USER__,$SQL.$SQL_AGENT." AND sex=2");?>
        <em><i class="ico">&#xe95d;</i><?php echo $totalnum;?></em><div>女生总数（人）</div>
    </ul>
    <ul>
		<?php 
		$rtP=$db->query("SELECT id FROM ".__TBL_PAY__." WHERE flag=1 ".$SQL_pay_agent_inU." AND flag=1 GROUP BY uid");
		$totalnum = $db->num_rows($rtP);
        ?>
        <em><i class="ico">&#xe6ab;</i> <?php echo intval($totalnum);?></em><div>付费总用户数（人）</div>
    </ul>
    <ul>
		<?php 
        $rtPAY=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 ".$SQL_pay_agent_inU);
        $rowPAY=$db->fetch_array($rtPAY,'name');
        $paymoney=floatval($rowPAY['paymoney']);
        ?>
        <em><i class="ico">&#xe61a;</i> <?php echo $paymoney;?></em><div>总收入（元）</div>
    </ul>
</div>
<br><br>
<div class="cbox">
    <h5>用户总量增长分析<span class="line"></span></h5>
    <div id="reg_box" style="width:98%;height:300px"></div>
</div>
<br><br>
<div class="cbox">
    <h5>用户男女占比分析<span class="line"></span><span class="right">用户婚姻状况分析</span><span class="right_line"></span></h5>
    <div id="sexbox" style="width:33%;margin:10px 0 10px 4%;height:300px;float:left"></div>
    <div id="lovebox" style="width:49%;margin:10px 0 10px 5%;height:300px;float:right"></div>
</div>
<br><br>
<div class="cbox">
    <h5>用户学历分析<span class="line"></span><span class="right">VIP用户学历分析</span><span class="right_line"></span></h5>
    <div id="edubox" style="width:40%;margin:10px 0 10px 0;height:320px;float:left"></div>
    <div id="edubox_vip" style="width:55%;margin:10px 0;height:310px;float:right"></div>
</div>
<br><br>
<div class="cbox">
    <h5>用户住房分析<span class="line"></span></h5>
    <div id="housebox" style="width:98%;height:350px;margin:20px 0 0"></div>
</div>
<br><br>
<div class="cbox">
    <h5>用户购车分析<span class="line"></span><span class="right">用户年龄男女分析</span><span class="right_line"></span></h5>
    <div id="carbox" style="width:40%;margin:10px 0 10px 0;height:320px;float:left"></div>
    <div id="agebox" style="width:50%;margin:10px 0;height:310px;float:right"></div>
</div>
<br><br>
<div class="cbox">
    <h5>VIP用户类型分析<span class="line"></span><span class="right">用户帐号状态分析</span><span class="right_line"></span></h5>
    <div id="vipkindbox" style="width:40%;margin:10px 0 10px 0;height:320px;float:left"></div>
    <div id="flagbox" style="width:50%;margin:10px 0;height:310px;float:right"></div>
</div>

<br><br>
<style>
.cbox h5 dt{width:25%;display:inline-block;text-align:center;float:left;position:relative}
.cbox h5 dt b{width:30px;border-bottom:#009688 2px solid;position:absolute;top:50px;left:-webkit-calc(50% - 15px)}

.ulli{padding:0 15px;width:23%;margin:10px 1% 0;height:520px;float:left;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.ulli li{font-size:14px;height:50px;line-height:50px;border-bottom:#eee 1px solid;position:relative}
.ulli li img.m{width:40px;height:40px;border-radius:30px;display:block;position:absolute;left:5px;object-fit:cover;-webkit-object-fit:cover}
.ulli li span{padding-left:55px;display:block;height:40px}
.ulli li b{display:block;height:40px;position:absolute;right:10px;top:0;font-weight:normal}
</style>
<div class="cbox">
    <h5>
    	<dt>人气排行榜<b></b></dt>
        <dt>关注排行榜<b></b></dt>
        <dt>粉丝排行榜<b></b></dt>
        <dt><?php echo $_ZEAI['loveB'];?>排行榜<b></b></dt>
    </h5>
    <div class="ulli">
		<?php 
		$rt=$db->query("SELECT id,nickname,truename,sex,grade,photo_s,click FROM ".__TBL_USER__." WHERE photo_s<>'' ORDER BY click DESC,id DESC LIMIT 10");
		$total = $db->num_rows($rt);
		if ($total == 0) {
			echo "<br><br><div class='nodataicoS'><i></i>暂无信息";
		} else {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'all');
				if(!$rows) break;
				$uid      = $rows['id'];
				$sex      = $rows['sex'];
				$grade    = $rows['grade'];
				$click    = $rows['click'];
				$photo_s  = $rows['photo_s'];
				$nickname = dataIO($rows['nickname'],'out');
				$truename = dataIO($rows['truename'],'out');
				$nickname = (!empty($truename))?$truename:$nickname;
				$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
				$href=Href('u',$uid);
				echo '<li><a href="'.$href.'" target="blank"><img src="'.$photo_s_url.'" class="m"></a><span>'.uicon($sex.$grade).'<font style="vertical-align:middle">'.$nickname.'</font></span><b>'.$click.'</b></li>';
			}
		}
        ?>
    </div>
    <div class="ulli">
		<?php 
		$rt=$db->query("SELECT senduid,COUNT(id) AS num FROM ".__TBL_GZ__." WHERE flag=1 GROUP BY senduid ORDER BY num DESC LIMIT 10");
		$total = $db->num_rows($rt);
		if ($total == 0) {
			echo "<br><br><div class='nodataicoS'><i></i>暂无信息";
		} else {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$senduid = $rows['senduid'];$num = $rows['num'];
				
				$row2 = $db->ROW(__TBL_USER__,"id,nickname,truename,sex,grade,photo_s","id=".$senduid,'name');
				$uid      = $row2['id'];
				$sex      = $row2['sex'];
				$grade    = $row2['grade'];
				$click    = $row2['click'];
				$photo_s  = $row2['photo_s'];
				$nickname = dataIO($row2['nickname'],'out');
				$truename = dataIO($row2['truename'],'out');
				$nickname = (!empty($truename))?$truename:$nickname;
				$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
				$href=Href('u',$uid);
				echo '<li><a href="'.$href.'" target="blank"><img src="'.$photo_s_url.'" class="m"></a><span>'.uicon($sex.$grade).'<font style="vertical-align:middle">'.$nickname.'</font></span><b>'.$num.'人</b></li>';
			}
		}
        ?>
    </div>
    <div class="ulli">
		<?php 
		$rt=$db->query("SELECT uid,COUNT(id) AS num FROM ".__TBL_GZ__." WHERE flag=1 GROUP BY uid ORDER BY num DESC LIMIT 10");
		$total = $db->num_rows($rt);
		if ($total == 0) {
			echo "<br><br><div class='nodataicoS'><i></i>暂无信息";
		} else {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$senduid = $rows['uid'];$num = $rows['num'];
				
				$row2 = $db->ROW(__TBL_USER__,"id,nickname,truename,sex,grade,photo_s","id=".$senduid,'name');
				$uid      = $row2['id'];
				$sex      = $row2['sex'];
				$grade    = $row2['grade'];
				$click    = $row2['click'];
				$photo_s  = $row2['photo_s'];
				$nickname = dataIO($row2['nickname'],'out');
				$truename = dataIO($row2['truename'],'out');
				$nickname = (!empty($truename))?$truename:$nickname;
				$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
				$href=Href('u',$uid);
				echo '<li><a href="'.$href.'" target="blank"><img src="'.$photo_s_url.'" class="m"></a><span>'.uicon($sex.$grade).'<font style="vertical-align:middle">'.$nickname.'</font></span><b>'.$num.'人</b></li>';
			}
		}
        ?>
    </div>
    <div class="ulli">
		<?php 
		$rt=$db->query("SELECT id,nickname,truename,sex,grade,photo_s,loveb FROM ".__TBL_USER__." ORDER BY loveb DESC,id DESC LIMIT 10");
		$total = $db->num_rows($rt);
		if ($total == 0) {
			echo "<br><br><div class='nodataicoS'><i></i>暂无信息";
		} else {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'all');
				if(!$rows) break;
				$uid      = $rows['id'];
				$sex      = $rows['sex'];
				$grade    = $rows['grade'];
				$loveb    = $rows['loveb'];
				$photo_s  = $rows['photo_s'];
				$nickname = dataIO($rows['nickname'],'out');
				$truename = dataIO($rows['truename'],'out');
				$nickname = (!empty($truename))?$truename:$nickname;
				$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
				$href=Href('u',$uid);
				echo '<li><a href="'.$href.'" target="blank"><img src="'.$photo_s_url.'" class="m"></a><span>'.uicon($sex.$grade).'<font style="vertical-align:middle">'.$nickname.'</font></span><b>'.$loveb.'</b></li>';
			}
		}
        ?>
    </div>
</div>

<br><br><br><br>
<script src="js/analyse_u.js?<?php echo $_ZEAI['cache_str'];?>"></script>
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
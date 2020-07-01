<?php require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_shop.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);

if(!in_array('welcome',$QXARR))exit(noauth('权限不足'));

if($submitok=='ajax_user1'){
	$num1 = $db->COUNT(__TBL_USER__,"sex=1 AND kind<>4");
	$num2 = $db->COUNT(__TBL_USER__,"sex=2 AND kind<>4");
	$all = $num1+$num2;
	$user1=round($num1/$all,2)*100;
	$user2=round($num2/$all,2)*100;
	if($user1==0 || $user2==0){
		json_exit(array('flag'=>0));
	}else{
		json_exit(array('flag'=>1,'user1'=>$user1,'user2'=>$user2,'num1'=>$num1,'num2'=>$num2));
	}
}elseif($submitok=='ajax_user2'){
	$urole = json_decode($_ZEAI['urole']);
	$total = $db->COUNT(__TBL_USER__,"kind<>4");
	$arr=array();
	foreach ($urole as $uv) {
		$grade = $uv->g;
		$title = $uv->t;
		$num = $db->COUNT(__TBL_USER__,"kind<>4 AND grade=".$grade);
		$bfb=round($num/$total,4)*100;
		$arr[]=array('name'=>$title.'：'.$num.'人','y'=>$bfb);
	}
	if(!empty($arr)){
		exit(encode_json($arr));
	}else{
		exit('');	
	}
}elseif($submitok=='ajax_reg_day'){
	$rt=$db->query("SELECT COUNT(id) AS num,from_unixtime(regtime,'%Y-%m-%d') AS day FROM ".__TBL_USER__." WHERE kind<>4 GROUP BY day ORDER BY day DESC LIMIT 10");
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
}elseif($submitok=='ajax_U'){
	$U['u1']=$db->COUNT(__TBL_USER__,"kind<>4");
	$U['u2']=$db->COUNT(__TBL_USER__,"sex=1 AND kind<>4");
	$U['u3']=$db->COUNT(__TBL_USER__,"sex=2 AND kind<>4");
	$U['u4']=$db->COUNT(__TBL_USER__,"grade>1 AND kind<>4");
	$U['u5']=$db->COUNT(__TBL_USER__,"flag=-2 AND kind<>4");
	$U['u6']=$db->COUNT(__TBL_USER__,"flag=2 AND kind<>4");
	$U['u7']=$db->COUNT(__TBL_USER__,"myinfobfb>=60 AND sex=1 AND kind<>4");
	$U['u8']=$db->COUNT(__TBL_USER__,"myinfobfb>=60 AND sex=2 AND kind<>4");
	json_exit(array('flag'=>1,'U'=>$U));
}elseif($submitok=='ajax_dataflag'){
	$F['f1']=$db->COUNT(__TBL_USER__,"dataflag=0 AND kind<>4");
	$F['f2']=$db->COUNT(__TBL_USER__,"photo_f=0 AND photo_s<>'' AND kind<>4");
	$F['f3']=$db->COUNT(__TBL_PHOTO__,"flag=0");
	$F['f4']=$db->COUNT(__TBL_VIDEO__,"flag=0");
	$F['f5']=$db->COUNT(__TBL_RZ__,"flag=0 AND rzid<>'photo'");
	$F['f6']=$db->COUNT(__TBL_DATING__,"flag=0");
	$F['f7']=$db->COUNT(__TBL_TREND__,"flag=0");
	$F['f8']=$db->COUNT(__TBL_TREND_BBS__,"flag=0");
	$F['f9']=$db->COUNT(__TBL_315__,"flag=0");
	json_exit(array('flag'=>1,'F'=>$F));
}elseif($submitok=='ajax_TG'){
	$t1=$db->COUNT(__TBL_USER__,"tgflag=0 AND tguid<>''");
	$t2=$db->COUNT(__TBL_TG_USER__,"tgflag=0 AND tguid<>''");
	$TG['tg1']=$t1+$t2;
	$TG['tg2']=$db->COUNT(__TBL_TG_USER__,"flag=0");
	$TG['tg3']=$db->COUNT(__TBL_TG_USER__,"shopflag=0");
	$TG['tg4']=$db->COUNT(__TBL_PAY__,"kind=-1 AND flag=0");
	$TG['wzbbs']=$db->COUNT(__TBL_NEWS_BBS__,"flag=0");
//	$rt=$db->query("SELECT SUM(tgallloveb) AS tgallloveb FROM ".__TBL_USER__,"kind<>4");
//	$row=$db->fetch_array($rt,'name');
//	$TG['tg3']=intval($row['tgallloveb']);
//	
//	$rt=$db->query("SELECT SUM(tgallmoney) AS tgallmoney FROM ".__TBL_USER__,"kind<>4");
//	$row=$db->fetch_array($rt,'name');
//	$TG['tg4']=intval($row['tgallmoney']);
	
	json_exit(array('flag'=>1,'TG'=>$TG));
}elseif($submitok=='ajax_SY'){
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=1 AND flag=1");
	$row=$db->fetch_array($rt,'name');
	$SY['sy1']=number_format($row['paymoney']);
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=2 AND flag=1");
	$row=$db->fetch_array($rt,'name');
	$SY['sy2']=number_format($row['paymoney']);
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=3 AND flag=1");
	$row=$db->fetch_array($rt,'name');
	$SY['sy3']=number_format($row['paymoney']);
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=4 AND flag=1");
	$row=$db->fetch_array($rt,'name');
	$SY['sy4']=number_format($row['paymoney']);
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=5 AND flag=1");
	$row=$db->fetch_array($rt,'name');
	$SY['sy5']=number_format($row['paymoney']);
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=6 AND flag=1");
	$row=$db->fetch_array($rt,'name');
	$SY['sy6']=number_format($row['paymoney']);
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind=7 AND flag=1");
	$row=$db->fetch_array($rt,'name');
	$SY['sy7']=number_format($row['paymoney']);
	//$SY['sy0']=$SY['sy1']+$SY['sy2']+$SY['sy3']+$SY['sy4']+$SY['sy5']+$SY['sy6']+$SY['sy7'];
/*	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 ");
	$row=$db->fetch_array($rt,'name');
	$SY['sy0']=intval($row['paymoney']);
*/	
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1");
	$row=$db->fetch_array($rt,'name');
	$SY['syAllcur']=number_format($row['paymoney']);
	
	//上月
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 AND (   date_format(from_unixtime(addtime),'%Y-%m')=date_format(DATE_SUB(curdate(), INTERVAL 1 MONTH),'%Y-%m')   )");
	$row=$db->fetch_array($rt,'name');
	$SY['syMbef']=number_format($row['paymoney']);
	//本月
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 AND (   date_format(from_unixtime(addtime),'%Y-%m') = date_format(now(),'%Y-%m')   )");
	$row=$db->fetch_array($rt,'name');
	$SY['syMcur']=number_format($row['paymoney']);
	//本周
/*	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 AND (   YEARWEEK(date_format(from_unixtime(addtime),'%Y-%m-%d')) = YEARWEEK(now())   )");
	$row=$db->fetch_array($rt,'name');
	$SY['syWcur']=intval($row['paymoney']);
*/	
	//本年
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE kind<>-1 AND flag=1 AND (  YEAR(from_unixtime(addtime)) = YEAR(now())  )");
	$row=$db->fetch_array($rt,'name');
	$SY['syYcur']=number_format($row['paymoney']);
	
	//今天
	$today = YmdHis(ADDTIME,'Ymd');
	$rt=$db->query("SELECT SUM(paymoney) AS paymoney FROM ".__TBL_PAY__." WHERE  kind<>-1 AND flag=1 AND ( date_format(from_unixtime(addtime),'%Y-%m-%d') = '$today'  )");
	$row=$db->fetch_array($rt,'name');
	$SY['syDcur']=number_format($row['paymoney']);
	
	json_exit(array('flag'=>1,'SY'=>$SY));
}elseif($submitok=='ajax_GZH'){
	$GZH['gzh1']=$db->COUNT(__TBL_USER__,"kind<>4");
	$GZH['gzh2']=$db->COUNT(__TBL_USER__,"subscribe=1 AND kind<>4");
	$GZH['gzh3']=$db->COUNT(__TBL_USER__,"subscribe=0 AND kind<>4");
	$GZH['gzh4']=$db->COUNT(__TBL_USER__,"subscribe=2 AND kind<>4");
	json_exit(array('flag'=>1,'GZH'=>$GZH));
}
$today = YmdHis(ADDTIME,'Ymd');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/highcharts.js"></script> 
<script src="js/welcome.js?<?php echo $_ZEAI['cache_str'];?>"></script> 
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style type="text/css">
body{background-color:#f0f0f0}
h5,h6,b{font-style:normal;font-weight:normal;margin:0px;padding:0px;display:block}
.box{width:100%;background-color:#fff;border:#eee 1px solid;text-align:left;box-sizing:border-box;padding-bottom:15px;clear:both}
.box h5{width:100%;font-size:15px;font-weight:normal;border-bottom:#f5f5f5 1px solid;line-height:50px;margin:0 0 10px 0;padding:0 15px;display:block;position:relative;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.box span.line{width:20px;border-bottom:#009688 2px solid;position:absolute;top:50px;left:35px}
.box ul {width:100%;padding:0 10px;box-sizing:border-box;clear:both;overflow:auto} 
.box ul a{width:calc(33% - 20px);width:-webkit-calc(33% - 20px);padding:13px 15px 15px 15px;margin:10px;height:80px;float:left;background-color:#f8f8f8;border-radius:2px;float:left;box-sizing:border-box;-webkit-transition:all .5s;-moz-transition:all .5s;transition:all .5s;position:relative}
.box ul.cols3 a:nth-child(3n){margin-right:0;width:calc(34% - 20px);width:-webkit-calc(34% - 20px);}
.box ul a:hover{background-color:#f2f2f2}
.box ul.cols2 a{width:calc(50% - 20px);width:-webkit-calc(50% - 20px);}
.box ul.cols4 a{width:calc(25% - 20px);width:-webkit-calc(25% - 20px);}
.box ul a h6{color:#999;line-height:12px;display:inline-block;font-size:12px}
.box ul a b{line-height:50px;color:#009688;font-size:30px;font-family:Arial, Helvetica, sans-serif;display:block}
.box ul a span{position:absolute;right:16px;top:45px;color:#f60;font-size:14px}
.totalbox{width:100%;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;padding:10px}
</style>
</head>
<body>
<div class="totalbox">
<table border="0" cellpadding="8" cellspacing="0" style="width:98%;min-width:1130px">
  <tr>
    <td width="50%" valign="top" style="min-width:520px">
    
        <div class="box" style="height:475px">
            <h5>待办审核<span class="line"></span></h5>
            <ul class="cols4">
                <a href="u_jb_list.php"><h6>待审资料</h6><b id="F1">0</b></a>
                <a href="photo_m.php"><h6>待审头像</h6><b id="F2">0</b></a>
                <a href="photo.php"><h6>待审相册</h6><b id="F3">0</b></a>
                <a href="video.php"><h6>待审视频</h6><b id="F4">0</b></a>
                <a href="cert.php"><h6>待审认证</h6><b id="F5">0</b></a>
                <a href="dating.php"><h6>待审约会</h6><b id="F6">0</b></a>
                <a href="trend.php?f=flag0"><h6>待审交友圈</h6><b id="F7">0</b></a>
                <a href="trend_bbs.php?f=flag0"><h6>待审评论</h6><b id="F8">0</b></a>
                <a href="u_tg.php"><h6>待审推广提成</h6><b id="TG1">0</b></a>
                <a href="TG_u.php?f=f0&k=1"><h6>待审兼职红娘</h6><b id="TG2">0</b></a>
                <a href="TG_u.php?f=f0&k=2"><h6>待审<?php echo $_SHOP['title'];?></h6><b id="TG3">0</b></a>
                <a href="withdraw.php"><h6>待审提现</h6><b id="TG4">0</b></a>
                <a href="news_bbs.php?f=f0"><h6>待审文章评论</h6><b id="WZBBS">0</b></a>
                <a href="315.php?f=f0"><h6>待处理举报</h6><b id="F9">0</b></a>
            </ul>
        </div>

	<?php echo(in_array('welcome_pay',$QXARR))?'<br>':'';?>
    <div class="box"<?php echo (!in_array('welcome_pay',$QXARR))?' style="height:375px;display:none"':' style="height:375px"'?>>
        <h5>收益统计<span class="line"></span></h5>
        <ul class="cols4">
            <a><h6>总盈收(元)</h6><b id="syAllcur">0</b></a>
            <a><h6>本年(元)</h6><b id="syYcur">0</b></a>
            <a><h6>上月(元)</h6><b id="syMbef">0</b></a>
            <a><h6>本月(元)</h6><b id="syMcur">0</b></a>
            <!--<a><h6>本周(元)</h6><b id="syWcur">0</b></a>-->
            <a><h6 style="color:#FF5722">今天(元)</h6><b id="syDcur">0</b></a>
            <a href="pay.php?pkind=1"><h6>VIP升级(元)</h6><b id="SY1">0</b></a>
            <a href="pay.php?pkind=2"><h6><?php echo $_ZEAI['loveB'];?>充值(元)</h6><b id="SY2">0</b></a>
            <a href="pay.php?pkind=3"><h6>余额充值(元)</h6><b id="SY3">0</b></a>
            <a href="pay.php?pkind=4"><h6>活动报名(元)</h6><b id="SY4">0</b></a>
            <a href="pay.php?pkind=5"><h6>推广员升级(元)</h6><b id="SY5">0</b></a>
            <a href="pay.php?pkind=6"><h6>推广员激活(元)</h6><b id="SY6">0</b></a>
            <a href="pay.php?pkind=7"><h6>实名认证(元)</h6><b id="SY7">0</b></a>
        </ul>
    </div>
	<br>
    <div class="box" style="height:275px">
        <h5>公众号<span class="line" style="left:27px"></span></h5>
        <ul class="cols2">
            <a href="u_select.php"><h6>总用户</h6><b id="GZH1">0</b></a>
            <a href="u_select.php?subscribe=1"><h6>已关注</h6><b id="GZH2">0</b><span>+<?php echo $db->COUNT(__TBL_USER__,"subscribe=1 AND date_format(from_unixtime(regtime),'%Y-%m-%d') = '$today'");?> 今日新增</span></a>
            <a href="u_select.php?subscribe=0"><h6>未关注</h6><b id="GZH3">0</b></a>
            <a href="u_select.php?subscribe=2"><h6>取消关注</h6><b id="GZH4">0</b><span>+<?php echo $db->COUNT(__TBL_USER__,"subscribe=2 AND date_format(from_unixtime(regtime),'%Y-%m-%d') = '$today'");?> 今日取消</span></a>
        </ul>
    </div> 
    </td>
    <td valign="top" style="min-width:580px">
    
    <div class="box">
        <h5>用户统计<span class="line"></span></h5>
        <ul class="cols4">
            <a href="u.php"><h6>用户总数</h6><b id="U1">0</b></a>
            <a href="u_select.php?sex=1"><h6>男生总数</h6><b id="U2">0</b></a>
            <a href="u_select.php?sex=2"><h6>女生总数</h6><b id="U3">0</b></a>
            <a href="u.php?g=2"><h6>当前VIP总数 </h6><b id="U4">0</b></a>
        </ul>
        <table width="100%" border="0">
          <tr>
            <td width="49%"><div id="container_user1" style="width:100%; height:250px;margin:20px 0"></div></td>
            <td><div id="container_user2" style="width:100%; height:300px"></div></td>
          </tr>
        </table>
        
        
        <ul class="cols4">
            <a href="u.php?f=-2"><h6>隐藏资料</h6><b id="U5">0</b></a>
             <a href="u.php?f=2"><h6>注册未完成</h6><b id="U6">0</b></a>
            <a href="u.php?sex=1&myinfobfb=60"><h6>资料>60%-男</h6><b id="U7">0</b></a>
            <a href="u.php?sex=2&myinfobfb=60"><h6>资料>60%-女</h6><b id="U8">0</b></a>
        </ul>

    </div>
   <br>
    <div class="box">
        <h5>用户注册增长<span class="line"></span></h5>
        <div id="container2" style="width:90%; height:306px;margin:0 auto"></div>
    </div> 
    
    
    </td>
  </tr>
  <tr>
    <td valign="top">
    


    
        
    </td>
    <td valign="top">
    

           
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
<br><br><br>
<?php require_once 'bottomadm.php';?>
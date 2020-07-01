<?php
ob_start();
require_once '../sub/init.php';
header("Cache-control: private");
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';

if($submitok == "ajax_admtjtime"){
	$uid=intval($uid);$admtjtime=intval($admtjtime);
	$row = $db->ROW(__TBL_USER__,"nickname,photo_f,photo_s","id=".$uid,'num');
	if($row){
		$nickname= $row[0];$photo_f= $row[1];$photo_s= $row[2];
		if(  ($photo_f==0 || empty($photo_s)) && $admtjtime == 0   )json_exit(array('flag'=>0,'msg'=>'会员无头像，推荐失败'));
		$SQL = ($admtjtime >0)?" admtjtime=0":" admtjtime=".ADDTIME;
		if($admtjtime >0){
			$SQL = " admtjtime=0";
			$flag_str1 = '已推荐';
			$flag_str2 = '未推荐';
		}else{
			$SQL = " admtjtime=".ADDTIME;
			$flag_str1 = '未推荐';
			$flag_str2 = '已推荐';
		}
		$db->query("UPDATE ".__TBL_USER__." SET ".$SQL." WHERE id=".$uid);
	}else{
		json_exit(array('flag'=>0,'msg'=>'会员不存在'));
	}
	AddLog('【优质会员推荐】原状态：'.$flag_str1.'->新状态：'.$flag_str2);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
exit;}
$SQL    = " kind<>4 ";
$ifmob  = 1;
$ifdata = 1;
if($session_kind == 'crm'){
	if(!in_array('crm_user_select',$QXARR))exit(noauth());
	$SQL .= getAgentSQL();
}else{
	if(!in_array('u_select',$QXARR))exit(noauth());
}
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.table0{min-width:1400px;width:98%;margin:10px 20px 20px 20px}
.mtop{ margin-top:10px;}
.SW,.SW_area,.SW_age{padding:0 3px}
.SW{width:100px}
.SW_area{width:120px;vertical-align:middle}
.SW_age{width:75px;padding:0}
.RCW,.RCW2{display:inline-block}
.RCW li{width:80px}
.RCW2 li{width:120px}
.formline{height:10px}
select{color:#666}
.sortbox{display:inline-block;width:70px}
.mate_echo li font{color:#999}
.gradeflag{display:block;color:#999;padding-top:5px;padding-bottom:10px;font-family:'Arial'}
.iframeAbox{display:inline-block}
.iframeAbox a{border-radius:2px;padding:0 10px;height:32px;font-size:14px;line-height:32px;display:inline-block;background-color:#e3f4ff;border:#84cdff 1px solid;color:#2484dd;margin:0 0 0 20px;vertical-align:middle;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.iframeAbox a.ed{background-color:#2484dd;border-color:#2484dd;color:#fff}
.modedatabox a{background-color:#f9f9f9;border:#ccc 1px solid;color:#888}
.modedatabox a.ed{background-color:#009688;border-color:#009688;color:#fff}
img.crm_flag3{width:70px}/*position:absolute;top:0px;right:0px;*/
.forcerz{margin-top:10px}
</style>
<?php
$Skeyword = trimm($Skeyword);

$parameter = "k=$k&iftj=$iftj&p=$p&sex=$sex&subscribe=$subscribe&pay=$pay&edu=$edu&job=$job&age1=$age1&age2=$age2&love=$love&child=$child&marrytype=$marrytype&heigh1=$heigh1&heigh2=$heigh2&car=$car&house=$house&weigh1=$weigh1&weigh2=$weigh2&a1=$a1&a2=$a2&a3=$a3&photo_s=$photo_s&grade2=$grade2&grade=$grade&marrytime=$marrytime&smoking=$smoking&drink=$drink&companykind=$companykind&regtime=$regtime&uflag=$uflag&ifmob=$ifmob&ifdata=$ifdata&ifbz=$ifbz&Skeyword=$Skeyword&g=$g&crm_ukind=$crm_ukind&crm_ugrade=$crm_ugrade&crm_flag=$crm_flag";

if (ifint($Skeyword)){
	$SQL .= " AND (id=$Skeyword) ";
}else{
	if (!empty($Skeyword))$SQL   .= " AND (( truename LIKE '%".trimm($Skeyword)."%' ) OR ( mob LIKE '%".trimm($Skeyword)."%' ) OR ( uname LIKE '%".trimm($Skeyword)."%' ) OR ( nickname LIKE '%".trimm($Skeyword)."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' )) ";
}

$areaid = '';
if (ifint($a1) && ifint($a2) && ifint($a3) && ifint($a4)){
	$areaid = $a1.','.$a2.','.$a3.','.$a4;
}elseif(ifint($a1) && ifint($a2) && ifint($a3)){
	$areaid = $a1.','.$a2.','.$a3;
}elseif(ifint($a1) && ifint($a2)){
	$areaid = $a1.','.$a2;
}elseif(ifint($a1)){
	$areaid = $a1;
}
$areaid2 = '';
if (ifint($h1) && ifint($h2) && ifint($h3) && ifint($h4)){
	$areaid2 = $h1.','.$h2.','.$h3.','.$h4;
}elseif(ifint($h1) && ifint($h2) && ifint($h3)){
	$areaid2 = $h1.','.$h2.','.$h3;
}elseif(ifint($h1) && ifint($h2)){
	$areaid2 = $h1.','.$h2;
}elseif(ifint($h1)){
	$areaid2 = $h1;
}
if (!empty($areaid2))$SQL  .= " AND area2id LIKE '%".$areaid2."%' ";
if (ifint($sex))$SQL       .= " AND sex=$sex ";
if ($subscribe=='subscribe1')$SQL     .= " AND subscribe=1 ";
if ($subscribe=='subscribe2')$SQL     .= " AND subscribe=2 ";
if ($subscribe=='subscribe0')$SQL .= " AND subscribe=0 ";
if (!empty($areaid))$SQL   .= " AND areaid LIKE '%".$areaid."%' ";
if (ifint($age1))$SQL      .= " AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= $age1 ) ";
if (ifint($age2))$SQL      .= " AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <= $age2 ) ";
if (ifint($pay))$SQL       .= " AND pay>=$pay ";
if (ifint($edu))$SQL       .= " AND edu>=$edu ";
if (ifint($job))$SQL       .= " AND job=$job ";
if (ifint($love))$SQL      .= " AND love=$love ";
if (ifint($child))$SQL     .= " AND child=$child ";
if (ifint($marrytype))$SQL .= " AND marrytype=$marrytype ";
if (ifint($marrytime))$SQL .= " AND marrytime=$marrytime ";
if (ifint($heigh1))$SQL    .= " AND ( heigh >= $heigh1 ) ";
if (ifint($heigh2))$SQL    .= " AND ( heigh <= $heigh2 ) ";
if (ifint($weigh1))$SQL    .= " AND ( heigh >= $weigh1 ) ";
if (ifint($weigh2))$SQL    .= " AND ( weigh <= $weigh2 ) ";
if (ifint($car))$SQL       .= " AND car=$car ";
if (ifint($house))$SQL     .= " AND house=$house ";
if (ifint($smoking))$SQL     .= " AND smoking=$smoking ";
if (ifint($drink))$SQL       .= " AND drink=$drink ";
if (ifint($companykind))$SQL .= " AND companykind=$companykind ";
if (ifint($ifmob))$SQL       .= " AND mob<>'' ";
if (ifint($ifdata))$SQL       .= " AND myinfobfb>10 ";
if (ifint($ifbz))$SQL       .= " AND bz<>'' ";
if ($ifadmid==1)$SQL .= " AND admid>0 ";
if ($iftguid==1)$SQL .= " AND tguid>0 ";
if($ifdata50 == 1)$SQL  .= " AND myinfobfb>50 ";
if($ifparent == 1)$SQL  .= " AND parent>1 ";
if ($photo_s == 1)$SQL  .= " AND photo_s<>'' ";
if ($grade2 == 1)$SQL   .= " AND grade>1 ";
if (ifint($grade))$SQL  .= " AND grade=".$grade;
if(!empty($rz))$SQL  .= " AND FIND_IN_SET('$rz',RZ)";
switch ($endtimee) {
	case 7: $SQL  .= " AND (UNIX_TIMESTAMP() - endtime) >= 604800";break;
	case 30:$SQL  .= " AND (UNIX_TIMESTAMP() - endtime) >= 2592000";break;
	case 180:$SQL .= " AND (UNIX_TIMESTAMP() - endtime) >= 15552000";break;
	case 365:$SQL .= " AND (UNIX_TIMESTAMP() - endtime) >= 31536000";break;
}
switch ($uflag) {
	case 1: $SQL  .= " AND flag=1 ";break;
	case -2:$SQL  .= " AND flag=-2 ";break;
	case 2:$SQL .= "  AND flag=2 ";break;
	case -1:$SQL .= "  AND flag=-1 ";break;
}

//CRM按客户分类
if(ifint($crm_ukind))$SQL .= " AND crm_ukind=$crm_ukind";
//CRM按客户等级
if(ifint($crm_ugrade))$SQL .= " AND crm_ugrade=$crm_ugrade";
//CRM按客户状态
if(ifint($crm_flag))$SQL .= " AND crm_flag=$crm_flag";
//CRM等级过期
if(ifint($g) || $g==-1){
	switch ($g) {
		case 3:$SQL  .= " AND (crm_usjtime2 - ".ADDTIME.") < 259200 ";break;
		case 7:$SQL  .= " AND (crm_usjtime2 - ".ADDTIME.") < 604800 ";break;
		case 30:$SQL .= " AND (crm_usjtime2 - ".ADDTIME.") < 2592000 ";break;
		case -1:$SQL .= " AND crm_usjtime2 < ".ADDTIME." ";break;
	}
	$SQL .= "AND crm_ugrade>0 AND crm_usjtime2>0";
}

?>
<body>
<div class="navbox">
    <a href="u_select.php" class="ed">用户筛选<?php echo '<b>'.$db->COUNT(__TBL_USER__,$SQL).'</b>';?></a>
    <div class="Rsobox">
    <form name="form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="输入：UID/用户名/手机/姓名/昵称" value="<?php echo $Skeyword; ?>">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input name="sex" type="hidden" value="<?php echo $sex; ?>" />
        <input name="a1" type="hidden" value="<?php echo $a1; ?>" />
        <input name="a2" type="hidden" value="<?php echo $a2; ?>" />
        <input name="a3" type="hidden" value="<?php echo $a3; ?>" />
        <input name="a4" type="hidden" value="<?php echo $a4; ?>" />
        <input name="h1" type="hidden" value="<?php echo $h1; ?>" />
        <input name="h2" type="hidden" value="<?php echo $h2; ?>" />
        <input name="h3" type="hidden" value="<?php echo $h3; ?>" />
        <input name="h4" type="hidden" value="<?php echo $h4; ?>" />
        <input name="age1" type="hidden" value="<?php echo $age1; ?>" />
        <input name="age2" type="hidden" value="<?php echo $age2; ?>" />
        <input name="pay" type="hidden" value="<?php echo $pay; ?>" />
        <input name="edu" type="hidden" value="<?php echo $edu; ?>" />
        <input name="job" type="hidden" value="<?php echo $job; ?>" />
        <input name="love" type="hidden" value="<?php echo $love; ?>" />
        <input name="child" type="hidden" value="<?php echo $child; ?>" />
        <input name="marrytype" type="hidden" value="<?php echo $marrytype; ?>" />
        <input name="heigh1" type="hidden" value="<?php echo $heigh1; ?>" />
        <input name="heigh2" type="hidden" value="<?php echo $heigh2; ?>" />
        <input name="weigh1" type="hidden" value="<?php echo $weigh1; ?>" />
        <input name="weigh2" type="hidden" value="<?php echo $weigh2; ?>" />
        <input name="car" type="hidden" value="<?php echo $car; ?>" />
        <input name="house" type="hidden" value="<?php echo $house; ?>" />
        <input name="photo_s" type="hidden" value="<?php echo $photo_s; ?>" />
        <input name="vip" type="hidden" value="<?php echo $vip; ?>" />
        <input name="marrytime" type="hidden" value="<?php echo $marrytime; ?>" />
        <input name="uflag" type="hidden" value="<?php echo $uflag; ?>" />
        <input name="smoking" type="hidden" value="<?php echo $smoking; ?>" />
        <input name="drink" type="hidden" value="<?php echo $drink; ?>" />
        <input name="companykind" type="hidden" value="<?php echo $companykind; ?>" />
        <input name="regtime" type="hidden" value="<?php echo $regtime; ?>" />
        <input name="endtimee" type="hidden" value="<?php echo $endtimee; ?>" />
        <input name="ifmob" type="hidden" value="<?php echo $ifmob; ?>" />
        <input name="ifdata" type="hidden" value="<?php echo $ifdata; ?>" />
        <input name="ifbz" type="hidden" value="<?php echo $ifbz; ?>" />
        <input name="iftj" type="hidden" value="<?php echo $iftj; ?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
        </form>     
    </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<?php
switch ($sort) {
	case 'heigh0':$SORT = " ORDER BY heigh ";break;
	case 'heigh1':$SORT = " ORDER BY heigh DESC ";break;
	case 'weigh0':$SORT = " ORDER BY weigh ";break;
	case 'weigh1':$SORT = " ORDER BY weigh DESC ";break;
	case 'addtime0':$SORT = " ORDER BY regtime ";break;
	case 'addtime1':$SORT = " ORDER BY regtime DESC ";break;
	case 'age0':$SORT = " AND birthday<>'' AND birthday<>'0000-00-00' ORDER BY birthday DESC";break;
	case 'age1':$SORT = " AND birthday<>'' AND birthday<>'0000-00-00' ORDER BY birthday";break;
	case 'edu0':$SORT = " ORDER BY edu ";break;
	case 'edu1':$SORT = " ORDER BY edu DESC ";break;
	case 'uid0':$SORT = " ORDER BY id ";break;
	case 'uid1':$SORT = " ORDER BY id DESC ";break;
	case 'flag0':$SORT = " ORDER BY flag,id DESC ";break;
	case 'flag1':$SORT = " ORDER BY flag DESC,id DESC ";break;
	case 'pay0':$SORT = " ORDER BY pay,id DESC ";break;
	case 'pay1':$SORT = " ORDER BY pay DESC ";break;
	case 'myinfobfb0':$SORT = " ORDER BY myinfobfb,id DESC ";break;
	case 'myinfobfb1':$SORT = " ORDER BY myinfobfb DESC,id DESC ";break;
	default:$SORT = " ORDER BY id DESC ";break;
}
if ($regtime==1){
	$SORT = " ORDER BY id DESC ";
}elseif($regtime==2){
	$SORT = " ORDER BY endtime DESC ";
}
$fields = ",mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2,mate_other";
$rt = $db->query("SELECT id,uname,nickname,truename,photo_s,mob,sex,grade,flag,admid,hnid,hnid2,agentid,agenttitle,areaid,areatitle,area2title,bz,heigh,weigh,birthday,edu,pay,job,love,marrytype,marrytime,child,house,car,subscribe,myinfobfb,companykind,smoking,drink,admname,admid,regtime,endtime,kind,flag,tguid,weixin,if2,sjtime,qq,admtjtime,crm_flag,RZ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	$uroleStr = str_replace("g","i",$_ZEAI['urole']);
	$uroleStr = str_replace("t","v",$uroleStr);
	$mate_diy_arr=explode(',',$_ZEAI['rz_data']);
?>
    <table class="table0">
    <tr>
    <td align="left" class="border0 S14" style="min-width:980px">
        <script>
            var nulltext = '不限';
            function chkform(){
                if (age1.value > age2.value && (!zeai.empty(age1.value) && !zeai.empty(age2.value)) ){
                    zeai.msg('年龄请选择一个正确的区间（左小右大）',age1);	
                    return false;
                }
                if (heigh1.value > heigh2.value && (!zeai.empty(heigh1.value) && !zeai.empty(heigh2.value)) ){
                    zeai.msg('身高请选择一个正确的区间（左小右大）',heigh1);	
                    return false;
                }
            }
        </script>
        <form id="zeaiFORMso" method="get" action="<?php echo SELF; ?>" onSubmit="return chkform();">
			月薪 <script>zeai_cn__CreateFormItem('select','pay','<?php echo $pay; ?>','class="size2 SW"',pay_ARR);</script>　
            学历 <script>zeai_cn__CreateFormItem('select','edu','<?php echo $edu; ?>','class="size2 SW"',edu_ARR);</script>　
            职业 <script>zeai_cn__CreateFormItem('select','job','<?php echo $job; ?>','class="size2 SW"',job_ARR);</script>　
            <span class="picmiddle">公众号</span> <script>zeai_cn__CreateFormItem('select','subscribe','<?php echo $subscribe; ?>','class="size2 RCW"',[{i:"subscribe1",v:"已关注"},{i:"subscribe0",v:"未关注"},{i:"subscribe2",v:"取消关注"}]);</script>　
            帐号状态 <script>zeai_cn__CreateFormItem('select','uflag','<?php echo $uflag; ?>','class="size2 RCW RCW2"',[{i:"1",v:"注册成功会员"},{i:"-2",v:"已隐藏会员"},{i:"2",v:"注册未完成"},{i:"-1",v:"已锁定(注销)会员"}]);</script>　
            线上会员等级 <script>zeai_cn__CreateFormItem('select','grade','<?php echo $grade; ?>','class="size2  SW"',<?php echo $uroleStr;?>);</script>　
            <input type="checkbox" name="iftguid" id="iftguid" class="checkskin" value="1"<?php echo ($iftguid == 1)?' checked':''; ?>><label for="iftguid" class="checkskin-label"><i></i><b class="W80 S14">有推荐人</b></label>
			<div class="formline"></div>
            
            年龄 <script>zeai_cn__CreateFormItem('select','age1','<?php echo $age1; ?>','class="size2 SW_age"',age_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','age2','<?php echo $age2; ?>','class="size2 SW_age"',age_ARR);</script>　
            婚姻 <script>zeai_cn__CreateFormItem('select','love','<?php echo $love; ?>','class="size2 SW"',love_ARR);</script>　
            子女 <script>zeai_cn__CreateFormItem('select','child','<?php echo $child; ?>','class="size2 SW"',child_ARR);</script>　
            结婚时间 <script>zeai_cn__CreateFormItem('select','marrytime','<?php echo $marrytime; ?>','class="size2 SW"',marrytime_ARR);</script>　
            <select id="rz" name="rz" class="size2 RCW RCW2"><option value="0">认证不限</option><?php foreach($mate_diy_arr as $V){?><option value="<?php echo $V;?>"<?php echo ($V == $rz)?' selected':'';?>><?php echo rz_data_info($V,'title');?></option><?php }?></select>　
            <script>zeai_cn__CreateFormItem('radio','sex','<?php echo $sex; ?>','class="size2 RCW"',sex_ARR);</script>
            <input type="checkbox" name="ifadmid" id="ifadmid" class="checkskin" value="1"<?php echo ($ifadmid == 1)?' checked':''; ?>><label for="ifadmid" class="checkskin-label"><i></i><b class="W50 S14">被认领</b></label>
            <div class="formline"></div>
            
            身高 <script>zeai_cn__CreateFormItem('select','heigh1','<?php echo $heigh1; ?>','class="size2 SW_age"',heigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','heigh2','<?php echo $heigh2; ?>','class="size2 SW_age"',heigh_ARR);</script>　
            车子 <script>zeai_cn__CreateFormItem('select','car','<?php echo $car; ?>','class="size2 SW"',car_ARR);</script>　
            房子 <script>zeai_cn__CreateFormItem('select','house','<?php echo $house; ?>','class="size2 SW"',house_ARR);</script>　
            嫁娶形式 <script>zeai_cn__CreateFormItem('select','marrytype','<?php echo $marrytype; ?>','class="size2 SW"',marrytype_ARR);</script>　
            <input type="checkbox" name="grade2" id="grade2" class="checkskin" value="1"<?php echo ($grade2 == 1)?' checked':''; ?>><label for="grade2" class="checkskin-label"><i></i><b class="W80 S14">线上VIP会员</b></label>　
            <input type="checkbox" name="photo_s" id="photo_s" class="checkskin" value="1"<?php echo ($photo_s == 1)?' checked':''; ?>><label for="photo_s" class="checkskin-label"><i></i><b class="W50 S14">有头像</b></label>　
            <input type="checkbox" name="ifparent" id="ifparent" class="checkskin" value="1"<?php echo ($ifparent == 1)?' checked':''; ?>><label for="ifparent" class="checkskin-label"><i></i><b class="W80 S14">父母帮征婚</b></label>　
            
            <div class="formline"></div>
           
            体重 <script>zeai_cn__CreateFormItem('select','weigh1','<?php echo $weigh1; ?>','class="size2 SW_age"',weigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','weigh2','<?php echo $weigh2; ?>','class="size2 SW_age"',weigh_ARR);</script>　
            吸烟 <script>zeai_cn__CreateFormItem('select','smoking','<?php echo $smoking; ?>','class="size2 SW"',smoking_ARR);</script>　
            饮酒 <script>zeai_cn__CreateFormItem('select','drink','<?php echo $drink; ?>','class="size2 SW"',drink_ARR);</script>　
            单位类型 <script>zeai_cn__CreateFormItem('select','companykind','<?php echo $companykind; ?>','class="size2 SW"',companykind_ARR);</script>　
            <input type="checkbox" name="ifmob" id="ifmob" class="checkskin" value="1"<?php echo ($ifmob == 1)?' checked':''; ?> disabled><label for="ifmob" class="checkskin-label"><i></i><b class="W50 S14">有手机</b></label>　
            <input type="checkbox" name="ifdata" id="ifdata" class="checkskin" value="1"<?php echo ($ifdata == 1)?' checked':''; ?> disabled><label for="ifdata" class="checkskin-label"><i></i><b class="W80 S14">资料>10%</b></label>
            <input type="checkbox" name="ifdata50" id="ifdata50" class="checkskin" value="1"<?php echo ($ifdata50 == 1)?' checked':''; ?> ><label for="ifdata50" class="checkskin-label"><i></i><b class="W80 S14">资料>50%</b></label>
           <input type="checkbox" name="ifbz" id="ifbz" class="checkskin" value="1"<?php echo ($ifbz == 1)?' checked':''; ?>><label for="ifbz" class="checkskin-label"><i></i><b class="W50 S14">有备注</b></label>
            
            
            <div class="formline"></div>
            
            工作地区 <script>LevelMenu4('a1|a2|a3|a4|不限|<?php echo $a1; ?>|<?php echo $a2; ?>|<?php echo $a3; ?>|<?php echo $a4; ?>|areaid|areatitle','class="size2 SW"');</script>　
            户籍地区 <script>LevelMenu4('h1|h2|h3|h4|不限|<?php echo $h1; ?>|<?php echo $h2; ?>|<?php echo $h3; ?>|<?php echo $a4; ?>|areaid2|areatitle2','class="size2 SW"','hj');</script>
            
            <div class="formline"></div>
            最后登录 <script>zeai_cn__CreateFormItem('select','endtimee','<?php echo $endtimee; ?>','class="size2 SW"',[{i:"7",v:"7天未登录"},{i:"30",v:"30天未登录"},{i:"180",v:"半年未登录"},{i:"365",v:"一年未登录"}]);</script>　
            时间排序 <script>zeai_cn__CreateFormItem('select','regtime','<?php echo $regtime; ?>','class="size2 SW"',[{i:"1",v:"最新注册"},{i:"2",v:"最后登录"}]);</script>　
            
            <script>nulltext='CRM客户分类';zeai_cn__CreateFormItem('select','crm_ukind','<?php echo $crm_ukind; ?>','class="size2 SW_area"',crm_ukind_ARR);</script>　
            <script>nulltext='CRM客户等级';zeai_cn__CreateFormItem('select','crm_ugrade','<?php echo $crm_ugrade; ?>','class="size2 RCW"',crm_ugrade_ARR);</script>　
            <script>nulltext='CRM客户过期';zeai_cn__CreateFormItem('select','g','<?php echo $g; ?>','class="size2 picmiddle"',[{i:"3",v:"3天内到期"},{i:"7",v:"7天内到期"},{i:"30",v:"30天内到期"},{i:"-1",v:"已过期"}]);</script>　
            <script>nulltext='CRM服务状态';zeai_cn__CreateFormItem('select','crm_flag','<?php echo $crm_flag; ?>','class="size2 picmiddle"',<?php echo $_CRM['crm_flag'];?>);</script>　
            
            <input type="hidden" name="p" value="<?php echo $p;?>">
            <input type="hidden" name="Skeyword" value="<?php echo $Skeyword;?>">
            <input name="iftj" type="hidden" value="<?php echo $iftj; ?>" />
            <input name="k" type="hidden" value="<?php echo $k; ?>" />
            <button type="submit" class="btn size3"><i class="ico">&#xe6c4;</i> 开始筛选</button>　
      </form>
    </td>
    </tr>
    </table>



    
    
    <table class="tablelist">
    
    <tr>
      <td colspan="14" align="left" class="searchli">
			<?php $sorthref = SELF."?".$parameter."&sort=";?>
            <b>升降序</b>：
            <div class="sortbox">      
                年龄<div class="sort">
                <a title="升序" href="<?php echo $sorthref."age0";?>" <?php echo($sort == 'age0')?' class="ed"':''; ?>></a>
                <a title="降序" href="<?php echo $sorthref."age1";?>" <?php echo($sort == 'age1')?' class="ed"':''; ?>></a>
                </div>
            </div>
            <div class="sortbox">     
                身高<div class="sort">
                <a title="升序" href="<?php echo $sorthref."heigh0";?>" <?php echo($sort == 'heigh0')?' class="ed"':''; ?>></a>
                <a title="降序" href="<?php echo $sorthref."heigh1";?>" <?php echo($sort == 'heigh1')?' class="ed"':''; ?>></a>
                </div>
            </div>
            <div class="sortbox">    
                月薪<div class="sort">
                <a title="升序" href="<?php echo $sorthref."pay0";?>" <?php echo($sort == 'pay0')?' class="ed"':''; ?>></a>
                <a title="降序" href="<?php echo $sorthref."pay1";?>" <?php echo($sort == 'pay1')?' class="ed"':''; ?>></a>
                </div>
            </div>
            <div class="sortbox">  
                学历<div class="sort">
                <a title="升序" href="<?php echo $sorthref."edu0";?>" <?php echo($sort == 'edu0')?' class="ed"':''; ?>></a>
                <a title="降序" href="<?php echo $sorthref."edu1";?>" <?php echo($sort == 'edu1')?' class="ed"':''; ?>></a>
                </div>
            </div>
            <div class="sortbox">  
                体重<div class="sort">
                <a title="升序" href="<?php echo $sorthref."weigh0";?>" <?php echo($sort == 'weigh0')?' class="ed"':''; ?>></a>
                <a title="降序" href="<?php echo $sorthref."weigh1";?>" <?php echo($sort == 'weigh1')?' class="ed"':''; ?>></a>
                </div>
            </div>
            <div class="sortbox" style="width:100px">  
                资料完整度
                <div class="sort">
                <a title="升序" href="<?php echo $sorthref."myinfobfb0";?>" <?php echo($sort == 'myinfobfb0')?' class="ed"':''; ?>></a>
                <a title="降序" href="<?php echo $sorthref."myinfobfb1";?>" <?php echo($sort == 'myinfobfb1')?' class="ed"':''; ?>></a>
                </div>
            </div>
      </td>
    </tr>
    
    <form id="zeaiFORM" method="get" action="<?php echo SELF; ?>">
    <tr>
    <th width="30"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="120">头像/UID/昵称/VIP</th>
    <th width="10">&nbsp;</th>
    <th width="130">认证信息/手机/微信</th>
    <th width="10">&nbsp;</th>
    <th width="120">基础资料</th>
    <th width="120">婚姻/生活</th>
    <th width="150">经济状况</th>
    <th width="100">工作地区</th>
    <th width="100">户籍地区</th>
    <th width="150">择偶要求</th>
    <th width="150">帐号信息</th>
    <th align="center"><span class="center">备注</span></th>
    <th width="<?php echo ($iftj == 'TG_xqk')?160:120?>" class="center">操作</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid=$id;
		$pwd = $rows['pwd'];
		$uname = strip_tags($rows['uname']);
		$truename = strip_tags($rows['truename']);
			$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
			$truename = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$truename);
			$nickname = dataIO($rows['nickname'],'out');
			$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$mob = strip_tags($rows['mob']);
		$mob = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$mob);
		$photo_s   = $rows['photo_s'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$flag      = $rows['flag'];
		$if2     = $rows['if2'];
		$admid   = $rows['admid'];
		$admname = $rows['admname'];
		$hnid    = $rows['hnid'];
		$hnid2   = $rows['hnid2'];
		$agentid  = $rows['agentid'];
		$RZZ  = $rows['RZ'];
		$agenttitle  = dataIO($rows['agenttitle'],'out');
		$areaid   = $rows['areaid'];
		$areatitle = $rows['areatitle'];
		$area2title = $rows['area2title'];
		$bz = dataIO($rows['bz'],'out');
		$tguid      = $rows['tguid'];
		$heigh = (intval($rows['heigh'])>0)?intval($rows['heigh']).'cm':'';
		$weigh = (intval($rows['weigh'])>0)?intval($rows['weigh']).'kg':'';
		$birthday  = $rows['birthday'];
		$age   = (@getage($birthday)<=0)?'':@getage($birthday).'岁';
		$edu   = udata('edu',intval($rows['edu']));
		$pay   = udata('pay',intval($rows['pay']));
		$job   = udata('job',intval($rows['job']));
		$love      = udata('love',intval($rows['love']));
		$marrytype = udata('marrytype',intval($rows['marrytype']));
		$child     = udata('child',intval($rows['child']));
		$house     = udata('house',intval($rows['house']));
		$car       = udata('car',intval($rows['car']));
		$marrytime = udata('marrytime',intval($rows['marrytime']));
		$subscribe = $rows['subscribe'];
		$myinfobfb = $rows['myinfobfb'];
		$companykind = udata('companykind',intval($rows['companykind']));
		$smoking = udata('smoking',intval($rows['smoking']));
		$drink   = udata('drink',intval($rows['drink']));
		$weixin = dataIO($rows['weixin'],'out');
		$admtjtime = intval($rows['admtjtime']);
		$sjtime = $rows['sjtime'];
		$qq = dataIO($rows['qq'],'out');
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		if(empty($rows['nickname'])){if(empty($rows['truename'])){$title = $rows['mob'];}else{$title = $rows['truename'];}}else{
			$title = $nickname;
		}
		//gradeflag
		$timestr1 = get_if2_title($if2);
		if (!empty($sjtime)){
			$d1  = ADDTIME;
			$d2  = $sjtime + $if2*30*86400;
			$ddiff = $d2-$d1;
			if ($ddiff < 0){
				$timestr2 = '<font class="Cf00 B">已过期</font>';
				$timestr2 .= '<br>过期日：'.YmdHis($d2,'Ymd');
			} else {
				$tmpday   = intval($ddiff/86400);
				$timestr2 = ',还剩<font class="Cf00">'.$tmpday.'</font>天';
				$timestr2 .= '<br>到期日：'.YmdHis($d2,'Ymd');
			}
			$timestr2 = ($if2 >= 999)?'':$timestr2;
		}
		$gradeflag = ($grade == 1 || $grade == 10)?'<span class="gradeflag">'.uicon($sex.$grade).'<font class="picmiddle">'.utitle($grade).'</font><br></span>':'<span class="gradeflag">'.uicon($sex.$grade).'<font class="picmiddle">'.utitle($grade).'</font><br>'.$timestr1.$timestr2.'</span>';
		$title2 = (!empty($nickname))?urlencode(trimhtml($nickname)).'／'.$uid:$uid;
		$title2 = urlencode(trimhtml($nickname.' ｜ '.$uid));
		$crm_flag     = intval($rows['crm_flag']);
		$crm_flag_str = ($crm_flag==3)?'<img src="images/crm_flag3.png" class="crm_flag3">':'';
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="30"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="120" align="center">
        <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_s"><img src="<?php echo $photo_s_url; ?>" class=" photo_s80"></a>
        <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_s"><font class="C999">UID：</font><?php echo $uid;?></a>
        <?php if(!empty($rows['nickname']))echo "</br><font class='C999'>昵称：</font>".$nickname.$gradeflag;?>
        </td>
        <td width="10" align="left">&nbsp;</td>
        <td width="130" align="left" class="lineH200">
        <?php if(!empty($RZZ)){?><div title="手动强制认证" uid="<?php echo $id;?>" class="forcerz hand"><?php echo RZ_html($RZZ,'s','color');?></div><?php }?>
		<?php 
		if(crm_ifcontact($agentid,$admid,$hnid,$hnid2) || $session_kind=='adm'){?>
            <?php if (!empty($mob)){?><font class="C999">手机：</font><?php echo $mob;?><br><?php }?>
            <?php if (!empty($weixin)){?><font class="C999">微信：</font><?php echo $weixin;?><br><?php }?>
            <?php echo (!empty($qq))?'<font class="C999">QQ：</font>'.$qq:'';?>
		<?php }else{?>
           <?php if (!empty($mob)){?> <font class="C999">手机：</font>*****<br><?php }?>
            <?php if (!empty($weixin)){?><font class="C999">微信：</font>*****<br><?php }?>
            <?php echo (!empty($qq))?'<font class="C999">QQ：</font>*****':'';?>
        <?php }?>
        
        </td>
      <td width="10" align="left" valign="bottom"></td>
      <td width="120" align="left" class="lineH200" style="padding:10px 0">
        <font class="C999">姓名：</font><?php echo $truename;?><br>
        <font class="C999">出生：</font><?php echo $birthday;?><br>
        <font class="C999">年龄：</font><?php echo $age;?><br>
        <font class="C999">身高：</font><?php echo $heigh;?><br>
        <font class="C999">体重：</font><?php echo $weigh;?><br>
        <font class="C999">学历：</font><?php echo $edu;?>
      </td>
        <td width="120" class="lineH200 " style="padding:10px 0">
        <font class="C999">婚况：</font><?php echo $love;?><br>
        <font class="C999">子女：</font><?php echo $child;?><br>
        <font class="C999">嫁娶：</font><?php echo $marrytype;?><br>
        <font class="C999">期望：</font><?php echo (!empty($marrytime))?$marrytime.'结婚':'';?><br>
        <font class="C999">抽烟：</font><?php echo $smoking;?><br>
        <font class="C999">喝酒：</font><?php echo $drink;?><br>
        </td>
        <td width="150" class="lineH200">
        <font class="C999">单位：</font><?php echo $companykind;?><br>
        <font class="C999">职业：</font><?php echo $job;?><br>
        <font class="C999">月薪：</font><?php echo $pay;?><br>
        <font class="C999">房子：</font><?php echo $house;?><br>
        <font class="C999">车子：</font><?php echo $car;?><br>
    </td>
      <td width="100" class="lineH200"><?php echo str_replace(" ","<br>",$areatitle); ?></td>
      <td width="100" class="lineH200"><?php echo str_replace(" ","<br>",$area2title); ?></td>
      <td width="150" class="lineH200"><div class="mate_echo"><?php echo mate_echo($rows);?></div></td>
      <td width="150" align="left" class="lineH200">
		<?php
		if(ifint($tguid)){
			$tgrow = $db->ROW(__TBL_TG_USER__,"uname,nickname","id=".$tguid,"name");
			if ($tgrow){
				$uname_tg    = dataIO($tgrow['uname'],'out');
				$nickname_tg = dataIO($tgrow['nickname'],'out');
				$nickname_tg=(empty($nickname_tg))?$uname_tg:$nickname_tg;
				echo '<font class="C999">推荐人：</font>'.$nickname_tg.'<font class="C999">(ID:'.$tguid.')</font><br>';
			}
		}
		?>
      <font class="C999">注册时间：</font><?php echo YmdHis($rows['regtime'],'Ymd');?><br>
      <font class="C999">最后更新：</font><?php echo YmdHis($rows['endtime'],'Ymd');?></font><br>
      <font class="C999">帐号类型：</font><?php echo user_kind($rows['kind']); ?></font><br>
      <font class="C999">帐号状态：</font><?php echo flagtitle($rows['flag']);?></font><br>
      <font class="C999">所属门店：</font><?php echo $agenttitle;?></font><br>
      <font class="C999">认领红娘：</font><?php echo $admname;echo (ifint($admid))?' ID:'.$admid:'';?></font>
      
      </td>
      <td class="center border0"><a href="#" onClick="zeai.iframe('给【<?php echo $title;?>】会员备注','u_bz.php?classid=<?php echo $id;?>',500,280)" class="editico tips" tips-title='<?php echo $bz;?>'></a><span id="bz<?php echo $id;?>"><?php if (!empty($bz))echo '<font class="newdian"></font>';?></span>
      <?php echo $crm_flag_str;?>
      </td>
      <td width="<?php echo ($iftj == 'TG_xqk')?160:120?>" class="center" style="line-height:35px">
        
        <?php
		if ($iftj == 'TG_xqk'){
			if ($admtjtime > 0){
				$admtjcls = 'aLVed TG_xqk';
				$admtjtitle = '已推荐';
			}else{
				$admtjcls = 'aHONG2ed TG_xqk';
				$admtjtitle = '推荐至【'.$TG_set['navtitle'].'】';
			}
			?>
            <a href="javascript:;" class="<?php echo $admtjcls;?> tips" admtjtime="<?php echo $admtjtime;?>" tips-title='推荐后将在【<?php echo $TG_set['navtitle'];?>】展示给【<?php echo $TG_set['tgytitle'];?>】进行分享分销' tips-direction='left' style="margin-top:5px" uid="<?php echo $id;?>" title2="<?php echo $title2;?>"><?php echo $admtjtitle;?></a>
        <?php }else{ ?>
        	<?php if ($k == 'crm'){?>
                <a href="javascript:;" class="aQINGed tips qianxian_crm" tips-title='点击选择对象牵线' photo_s_url=<?php echo $photo_s_url;?> tips-direction='left' uid="<?php echo $id;?>" title2="<?php echo $title2;?>">开始牵线</a><br>
                <a href="javascript:;" class="aHONG2ed tips yuejian_crm" tips-title='点击选择对象约见' photo_s_url=<?php echo $photo_s_url;?> tips-direction='left' uid="<?php echo $id;?>" title2="<?php echo $title2;?>">开始约见</a>
            <?php }else{ //主后台?>
                <a href="javascript:;" class="aHUI tips editdata" tips-title='设置/修改会员资料' tips-direction='left' style="margin-top:5px" uid="<?php echo $id;?>" title2="<?php echo $title2;?>"><span class="<?php if($myinfobfb >80){echo ' myinfobfb2 ';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>"><?php echo $myinfobfb;?>%</span> 修改</a><br>
                <a href="javascript:;" class="aHONG2ed qianxian"  tips-title='点击选择对象牵线' photo_s_url=<?php echo $photo_s_url;?> tips-direction='left' uid="<?php echo $id;?>" title2="<?php echo $title2;?>">开始牵线</a>
            <?php }?>
            <a href="#" class="aZI card" uid="<?php echo $id;?>" title2="<?php echo $title2;?>">相亲卡片</a>
            <?php if(ifCrmAgentArea($agentid,$areaid)){?>
                <br><a href="#" class="aLAN guiji" uid="<?php echo $id;?>" title2="<?php echo $title2;?>">行为轨迹</a>
            <?php }?>
        <?php }?>
      </td>
    </tr>
	<?php } ?>
    <div class="listbottombox">
        <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
        <button type="button" id="btnsend" value="" class="btn size2 disabled action"><i class="ico">&#xe676;</i> 发送消息</button>
        <?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </div>
    </form>
</table>
<script>var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<script>
if(!zeai.empty(o('btnsend')))btnsend.onclick = function() {
	if (this.hasClass('disabled')){
		zeai.alert('请选择要发送的会员');
		return false;
	}
	var arr  = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){if (arr[key].checked)ulist.push(arr[key].value);}
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的会员');
	}else{
		zeai.iframe('发送消息','u_tip.php?ulist='+ulist,600,500);
	}
}
zeai.listEach('.card',function(obj){
	var uid = parseInt(obj.getAttribute("uid"));
	var title2 = obj.getAttribute("title2");
	obj.onclick = function(){zeai.iframe('生成【'+decodeURIComponent(title2)+'】相亲卡','u_card.php?uid='+uid);}
});
zeai.listEach('.qianxian',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">',
		urlpre = 'crm_user_detail_select.php?t=3&uid='+uid+'&t=';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】牵线配对'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'3\',this);" class="ed"><i class="ico add">&#xe64b;</i> 按择偶要求</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'4\',this);"><i class="ico add">&#xe6c4;</i> 按搜索条件</a>'+
		'</div>',urlpre+'3');
	}
});
zeai.listEach('.photo_s',function(obj){
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
zeai.listEach('.editdata',function(obj){
	var uid = parseInt(obj.getAttribute("uid"));
	var title2 = obj.getAttribute("title2");
	var urlpre = 'u_mod_data.php?iframenav=1&submitok=mod&ifmini=1&uid=';
	obj.onclick = function(){
		zeai.iframe('修改【'+decodeURIComponent(title2)+'】资料'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframeA(\'modedatabox\',\''+urlpre+uid+'&t=1\',this);" class="ed">基本信息/资料</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+urlpre+uid+'&t=2\',this);">详细资料</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+urlpre+uid+'&t=3\',this);">联系方法</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+urlpre+uid+'&t=4\',this);">择偶要求</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+'u_mod_data.php?iframenav=1&submitok=photo&ifmini=1&uid='+uid+'&t=5\',this);">个人相册</a>'+
		'</div>','u_mod_data.php?iframenav=1&submitok=mod&ifmini=1&uid='+uid);
	}
});
zeai.listEach('.guiji',function(obj){
	var uid = parseInt(obj.getAttribute("uid"));
	var title2 = obj.getAttribute("title2");
	obj.onclick = function(){
		zeai.iframe('【'+decodeURIComponent(title2)+'】行为轨迹'+
		'<div class="iframeAbox" id="guijibox">'+
		'<a onclick="iframeA(\'guijibox\',\'view_list.php?uid='+uid+'\',this);" class="ed">看过谁</a>'+
		'<a onclick="iframeA(\'guijibox\',\'gz_list.php?uid='+uid+'\',this);">关注过谁</a>'+
		'<a onclick="iframeA(\'guijibox\',\'chat_list.php?uid='+uid+'\',this);">聊过谁</a>'+
		'<a onclick="iframeA(\'guijibox\',\'tip_list.php?uid='+uid+'\',this);">收到通知</a>'+
		'<a onclick="iframeA(\'guijibox\',\'u_qianxian.php?uid='+uid+'\',this);">牵线记录</a>'+
		'<a onclick="iframeA(\'guijibox\',\'pay.php?uid='+uid+'\',this);">支付清单</a>'+
		'<a onclick="iframeA(\'guijibox\',\'loveb.php?uid='+uid+'\',this);">爱豆清单</a>'+
		'<a onclick="iframeA(\'guijibox\',\'money.php?uid='+uid+'\',this);">余额清单</a>'+
		'</div>','view_list.php?uid='+uid,1100,540);
	}
});
function iframeA(boxid,url,that){
	iframeAreset();
	that.class('ed');
	o('iframe_iframe').src=url;
	function iframeAreset(){zeai.listEach(zeai.tag(o(boxid),'a'),function(obj){obj.removeClass('ed');});}
}
setTimeout(function(){
	zeai.listEach('.TG_xqk',function(obj){
		obj.onclick = function(){
			var uid = parseInt(obj.getAttribute("uid")),
			admtjtime = parseInt(obj.getAttribute("admtjtime")),
			title2 = obj.getAttribute("title2");
			str = (admtjtime>0)?'确定取消推荐？':'确定推荐至【<?php echo $TG_set['navtitle'];?>】？';
			zeai.confirm(str,function(){
				zeai.ajax({url:'u_select'+zeai.extname,data:{submitok:'ajax_admtjtime',uid:uid,admtjtime:admtjtime}},function(e){rs=zeai.jsoneval(e);
					window.parent.zeai.msg(0);window.parent.zeai.msg(rs.msg);
					if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
				});
			});
		}
	});
},500);

<?php if ($k == 'crm'){?>
zeai.listEach('.qianxian_crm',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">',
		urlpre = 'crm_select.php?k=qx&uid='+uid+'&t=';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】牵线配对'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'3\',this);" class="ed"><i class="ico add">&#xe64b;</i> 按择偶要求</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'4\',this);"><i class="ico add">&#xe6c4;</i> 按搜索条件</a>'+
		'</div>',urlpre+'3');
	}
});
zeai.listEach('.yuejian_crm',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">',
		urlpre = 'crm_select.php?k=yj&uid='+uid+'&t=';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】约见配对'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'3\',this,\'edHONG2\');" class="edHONG2"><i class="ico add">&#xe64b;</i> 按择偶要求</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'4\',this,\'edHONG2\');"><i class="ico add">&#xe6c4;</i> 按搜索条件</a>'+
		'</div>',urlpre+'3');
	}
});
<?php }?>
</script>
<?php require_once 'bottomadm.php';ob_end_flush(); ?>
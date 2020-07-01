<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if ( !ifint($uid))alert_adm("会员UID不正确","back");
/************** qianxian ************/
if ($submitok == 'ajax_qianxian_add' || $submitok == 'ajax_qianxian_chk'){
	if ( !ifint($senduid))json_exit(array('flag'=>0,'msg'=>'会员UID不正确'));
	if ( !ifint($uid))json_exit(array('flag'=>0,'msg'=>'被牵线人UID不正确'));
}
if ($submitok == 'ajax_qianxian_add'){
	$row2 = $db->ROW(__TBL_USER__,"nickname,agentid,areaid,openid,subscribe","id=".$senduid,'num');$sendnickname= $row2[0];$sendagentid= $row2[1];$sendareaid= $row2[2];$sendopenid= $row2[3];$sendsubscribe= $row2[4];
	$row2 = $db->ROW(__TBL_USER__,"nickname,agentid,areaid,openid,subscribe","id=".$uid,'num');$nickname= $row2[0];$agentid= $row2[1];$areaid= $row2[2];$openid= $row2[3];$subscribe= $row2[4];
	if(!ifCrmAgentArea($agentid,$areaid))json_exit(array('flag'=>0,'msg'=>'亲，不能跨门店或跨地区操作哦'));
	if(!ifCrmAgentArea($sendagentid,$sendareaid))json_exit(array('flag'=>0,'msg'=>'亲，不能跨门店或跨地区操作哦'));
	$sendkind=intval($sendkind);
	$db->query("INSERT INTO ".__TBL_QIANXIAN__." (username,uid,senduid,sendkind,addtime) VALUES ('$session_uname',$uid,$senduid,$sendkind,".ADDTIME.")");
	//
	//$title  = '【'.$_ZEAI['siteName'].'】推荐了一位跟你匹配度非常高的Ta';
	//$content='亲爱的，【'.$_ZEAI['siteName'].'】推荐了一位跟你匹配度非常高的Ta，缘份就在一瞬间，赶快去认识吧!';
	$title  = '【'.$_ZEAI['siteName'].'】正在给您牵线中';
	$content='【'.$_ZEAI['siteName'].'】正在给您牵线中!';
	//站内
	$db->SendTip($uid,$title,dataIO($content,'in',1000),'sys');
	if (!empty($openid) && $subscribe==1){
		$url = urlencode(wHref('u',$senduid));
		$ret = @wx_kf_sent($openid,urlencode($content.'<br><br>→<a href="'.$url.'">【点击进入查看】</a><br><br>　'),'text');
		$ret = json_decode($ret);
		if ($ret->errmsg != 'ok'){
			$keyword1  = urlencode($content);
			$keyword3  = urlencode($_ZEAI['siteName']);
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
	}
	//站内send
	$db->SendTip($senduid,$title,dataIO($content,'in',1000),'sys');
	if (!empty($sendopenid) && $sendsubscribe==1){
		$url = urlencode(wHref('u',$uid));
		$ret = @wx_kf_sent($sendopenid,urlencode($content.'<br><br>→<a href="'.$url.'">【点击进入查看】</a><br><br>　'),'text');
		$ret = json_decode($ret);
		if ($ret->errmsg != 'ok'){
			$keyword1  = urlencode($content);
			$keyword3  = urlencode($_ZEAI['siteName']);
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$sendopenid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
	}
	//
	$uid_ = $uid;
	$uid=0;
	AddLog('【牵线管理】【'.$sendnickname.'（uid:'.$senduid.'）】->【'.$nickname.'（uid:'.$uid_.'）】牵线时间->'.YmdHis(ADDTIME));
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok == 'ajax_qianxian_chk'){
	$row = $db->ROW(__TBL_QIANXIAN__,"uid","senduid=".$senduid." AND uid=".$uid,"num");
	if ($row){
		json_exit(array('flag'=>1,'msg'=>'该用户已经牵线，确定再次牵线么？'));
	}else{
		json_exit(array('flag'=>0,'msg'=>'未牵线'));
	}
}
/**************************/
$SQL  = " (flag=1 OR flag=-2) AND kind<>4 ";
$SQL .= " AND id<>".$uid;
if($session_kind == 'crm'){
	if(!in_array('crm_user_select',$QXARR))exit(noauth());
	if($hyk!=3)$SQL .= getAgentSQL();//整站会员库
}else{
	if(!in_array('u_select',$QXARR))exit(noauth());
}
require_once ZEAI.'cache/udata.php';$sex_ARR = json_decode($_UDATA['sex'],true);$extifshow = json_decode($_UDATA['extifshow'],true);
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_crm.php';
$mate_diy = explode(',',$_ZEAI['mate_diy']);
$qianxian_num = json_decode($_VIP['qianxian_num'],true);


$row = $db->ROW(__TBL_USER__,"*","id=".$uid);
if(!$row)alert_adm("UID输入有误或不存在此会员","back");
$Utruename   = dataIO($row['truename'],'out');
$Unickname = dataIO($row['nickname'],'out');
$Usex   = $row['sex'];
$Ugrade = $row['grade'];
$_s    = $row['photo_s'];
$Ukind  = $row['kind'];
$Ubirthday = $row['birthday'];
$Uheigh    = $row['heigh'];
$Uweigh    = $row['weigh'];
$Upay      = $row['pay'];
$Uedu      = $row['edu'];
$Ujob      = $row['job'];
$Uareaid   = $row['areaid'];
$Ulove     = $row['love'];
$Uhouse    = $row['house'];
$Ucar      = $row['car'];
$Uareaid   = $row['areaid'];
$Uareaid2  = $row['area2id'];
$Uchild    = $row['child'];
$Umarrytime= $row['marrytime'];
$Ucompanykind= $row['companykind'];
$Usmoking    = $row['smoking'];
$Udrink      = $row['drink'];
$Uif2      = $row['if2'];
$Usjtime   = $row['sjtime'];

$Uage_str   = (@getage($Ubirthday)<=0)?'':@getage($Ubirthday).'岁';
$Umarrytype_str = udata('marrytype',intval($row['marrytype']));
$Umarrytime_str = udata('marrytime',intval($row['marrytime']));
$Ucompanykind_str = udata('companykind',intval($row['companykind']));
$Usmoking_str = udata('smoking',intval($row['smoking']));
$Udrink_str   = udata('drink',intval($row['drink']));
$Uedu_str   = udata('edu',$Uedu);
$Upay_str   = udata('pay',$Upay);
$Ujob_str   = udata('job',$Ujob);
$Ulove_str  = udata('love',$Ulove);
$Uchild_str = udata('child',$Uchild);
$Uhouse_str = udata('house',$Uhouse);
$Ucar_str   = udata('car',intval($Ucar));
//$Uheigh_str = (intval($Uheigh)>0)?intval($Uheigh).'kg':'';
//$Uweigh_str = (intval($Uweigh)>0)?intval($Uweigh).'cm':'';
$Uheigh_str = udata('heigh',$Uheigh);
$Uweigh_str = udata('weigh',$Uweigh);
$Uareatitle = $row['areatitle'];
$Uarea2title = $row['area2title'];

$Umate_li_out= mate_echo($row);
//
if(!empty($_s)){
	$photo_m_url = $_ZEAI['up2'].'/'.smb($_s,'m');
	$photo_b_url = smb($_ZEAI['up2'].'/'.$_s,'b');
	$photo_m_str = '<img src="'.$photo_m_url.'?'.ADDTIME.'" class="m2">';
}else{
	$photo_m_url = HOST.'/res/photo_m'.$Usex.'.png';
	$photo_m_str = '<img src="'.$photo_m_url.'" class="m2">';
}	
$nickname = (empty($nickname))?$truename:$nickname;
$qxneednum=$qianxian_num[$Ugrade];
$qxnum1 = $db->COUNT(__TBL_QIANXIAN__,"flag=2 AND senduid=".$uid);
$qxnum2 = $db->COUNT(__TBL_QIANXIAN__,"flag=1 AND senduid=".$uid);
$qxnum3 = $db->COUNT(__TBL_QIANXIAN__,"flag=-1 AND senduid=".$uid);
$rowqx = $db->ROW(__TBL_QIANXIAN__,"flag","senduid=".$uid." ORDER BY id DESC LIMIT 1");
$qxflag_str= crm_qxflag_title($rowqx[0]);


$parameter = "t=$t&uid=$uid&p=$p&sex=$sex&subscribe=$subscribe&pay=$pay&edu=$edu&job=$job&age1=$age1&age2=$age2&love=$love&child=$child&marrytype=$marrytype&heigh1=$heigh1&heigh2=$heigh2&car=$car&house=$house&weigh1=$weigh1&weigh2=$weigh2&a1=$a1&a2=$a2&a3=$a3&photo_s=$photo_s&grade2=$grade2&hyk=$hyk&fhsf=$fhsf"; 
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select3.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:800px;margin:0 40px 50px 20px}
.tablelist td{padding:3px}
.table0{min-width:1300px;width:98%;margin:10px 20px 0 20px}
.gradeflag{display:block;color:#999;padding-top:5px;padding-bottom:10px;font-family:'Arial'}
img.m{width:120px;height:120px;display:block;margin:5px 0;object-fit:cover;-webkit-object-fit:cover}
td.border0{vertical-align:top;padding-top:10px;line-height:14px}
.SW{width:100px;}
.SW_area{width:100px;vertical-align:middle;font-size:14px}
.SW_house{width:160px}
.RCW{display:inline-block}
.RCW li{width:80px}
</style>
<?php
$SQL .= ($Usex == 2)?" AND sex=1 ":" AND sex=2 ";


//超管搜索按门店
if (ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND agentid=$agentid";

/************************************* 会员库 ********************************************/
$hyk = (ifint($hyk,'1-4','1'))?$hyk:2;
if($hyk==1){//我的会员库
	if($hyk == 1)$SQL .= " AND (admid=".$session_uid." OR hnid=".$session_uid." OR hnid2=".$session_uid.")";
}elseif($hyk==2){//我的门店库
	//非超管匹配自己门店
	//if(!in_array('crm',$QXARR))$SQL.=" AND agentid=$session_agentid";
	$SQL .= getAgentSQL();
}elseif($hyk==3){
	//$SQL .= getAgentSQL();//门店+地区
}

//按搜索条件 t4
/****************************************按搜索条件*****************************************/
$areaid = '';
if (ifint($a1) && ifint($a2) && ifint($a3)){
	$areaid = $a1.','.$a2.','.$a3;
}elseif(ifint($a1) && ifint($a2)){
	$areaid = $a1.','.$a2;
}elseif(ifint($a1)){
	$areaid = $a1;
}
if (ifint($sex))$SQL       .= " AND sex=$sex ";
if (!empty($areaid))$SQL   .= " AND areaid LIKE '%".$areaid."%' ";
if (ifint($age1))$SQL      .= " AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= $age1 ) ";
if (ifint($age2))$SQL      .= " AND ( (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <= $age2 ) ";
if (ifint($pay))$SQL       .= " AND pay>=$pay ";
if (ifint($edu))$SQL       .= " AND edu>=$edu ";
if (ifint($job))$SQL       .= " AND job=$job ";
if (ifint($love))$SQL      .= " AND love=$love ";
if (ifint($child))$SQL     .= " AND child=$child ";
if (ifint($marrytype))$SQL .= " AND marrytype=$marrytype ";
if (ifint($heigh1))$SQL    .= " AND ( heigh >= $heigh1 ) ";
if (ifint($heigh2))$SQL    .= " AND ( heigh <= $heigh2 ) ";
if (ifint($weigh1))$SQL    .= " AND ( heigh >= $weigh1 ) ";
if (ifint($weigh2))$SQL    .= " AND ( weigh <= $weigh2 ) ";
if (ifint($car))$SQL       .= " AND car=$car ";
if (ifint($house))$SQL     .= " AND house=$house ";
if ($photo_s == 1)$SQL     .= " AND photo_s<>'' ";
if ($grade2 == 1)$SQL      .= " AND grade>1 ";

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
	default:$SORT = " ORDER BY refresh_time DESC,id DESC ";break;
}

/**************************************** 按会员UID/昵称/姓名/手机 *****************************************/
//按会员
$Skeyword = trimhtml($Skeyword);
if (!empty($Skeyword)){
	if(ifint($Skeyword)){
		$SQL .= " AND id=".$Skeyword;	
	}else{
		$SQL .= " AND (( truename LIKE '%".$Skeyword."%' ) OR ( mob LIKE '%".$Skeyword."%' ) OR ( uname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' )) ";
	}
}

/***************************************按择偶要求******************************************/
//t=3 按择偶要求
//生成SQL语句
if($t == 3){
	$SQL .= mate_diy_SQL();
}
/************************************* 符合双方 ********************************************/
if($fhsf == 1){
	$SQL .= mate_diy_SQL2();
}
/************************************* 符合双方 结束 ********************************************/
?>
<body>
<?php if ($t == 3){
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	?>
	<style>
	.zoyqbox{line-height:150%;color:#666;font-size:14px}
	.zoyqbox li font{color:#999}
	.zoyqbox li{/*display:inline-block;*/margin:0 10px 0 0;color:#666}
	.zoyqbox h3{line-height:30px;height:30px;position:relative;color:#666;padding-left:10px;font-size:14px}
	.zoyqbox h3 i{position:absolute;left:0;top:8px;width:4px;height:14px;border-radius:5px;background-color:#FF6F6F}
	.zoyqbox b{font-size:14px;margin:0 5px}
	.mtitle{color:#666;margin:0 0 10px 0;font-size:14px}
	.crm_photo_m{position:relative;display:block}
	.crm_photo_m .gradeico{position:absolute;left:15px;top:0;background-color:#fff;border:#ddd 1px solid;border-radius:20px 20px 0 20px}
	img.m2{width:150px;height:150px;display:block;object-fit:cover;-webkit-object-fit:cover;margin:0 auto 10px auto;padding:8px;border:#ddd 1px solid;border-radius:200px}
    </style>
    <table class="table0" style="border-bottom:#eee 1px solid">
      <tr>
        <td width="200" align="left" valign="top"><a class="crm_photo_m" href="<?php echo Href('crm_u',$uid);?>" ><?php echo $photo_m_str; ?><div class="gradeico"><?php echo uicon($Usex.$Ugrade,3);?></div></a></td>
        <td width="20" align="left" valign="top">&nbsp;</td>
        <td width="180" align="left" valign="top">
        <div class="mtitle">
            <font class='C999'>UID：</font><?php echo $uid;if(!empty($Unickname))echo "</br><font class='C999'>昵称：</font>".$Unickname;?>
        </div>
        <div class="zoyqbox">
            <font class="C999">需牵线：</font><b><?php echo $qxneednum;?></b>次<br>
            <font class="C999">成功了：</font><b><?php echo $qxnum1;?></b>次<br>
            <font class="C999">进行中：</font><b><?php echo $qxnum2;?></b>次<br>
            <font class="C999">失败了：</font><b><?php echo $qxnum3;?></b>次<br>
            <font class="C999">最近状态：</font><?php echo $qxflag_str;?>
        </div>
        </td>
        <td width="150" align="left" valign="top">
        <div class="zoyqbox">
            <h3>基础资料<i></i></h3>
            <font class="C999">姓名：</font><?php echo $Utruename;?><br>
            <font class="C999">生日：</font><?php echo str_replace("0000-00-00","",$Ubirthday);;?><br>
            <font class="C999">年龄：</font><?php echo $Uage_str;?><br>
            <font class="C999">身高：</font><?php echo $Uheigh_str;?><br>
            <font class="C999">学历：</font><?php echo $Uedu_str;?>
        </div>
        </td>
        <td width="150" align="left" valign="top">
        <div class="zoyqbox">
            <h3>婚姻/生活<i></i></h3>
            <?php if (!empty($Ulove_str)){?><font class="C999">婚况：</font><?php echo $Ulove_str;?><br><?php }?>
            <?php if (!empty($Uchild_str)){?> <font class="C999">子女：</font><?php echo $Uchild_str;?><br><?php }?>
            <?php if (!empty($Umarrytype_str)){?><font class="C999">嫁娶：</font><?php echo $Umarrytype_str;?><br><?php }?>
            <?php if (!empty($Umarrytime_str)){?><font class="C999">期望：</font><?php echo $Umarrytime_str;?>结婚<br><?php }?>
            <?php if (!empty($Usmoking_str)){?><font class="C999">抽烟：</font><?php echo $Usmoking_str;?><br><?php }?>
            <?php if (!empty($Udrink_str)){?><font class="C999">喝酒：</font><?php echo $Udrink_str;?><?php }?>
        </div>
        </td>
        <td width="180" align="left" valign="top" class="S12">
        <div class="zoyqbox">
            <h3>经济状况<i></i></h3>
            <?php if (!empty($Ucompanykind_str)){?><font class="C999">单位：</font><?php echo $Ucompanykind_str;?><br><?php }?>
            <?php if (!empty($Ujob_str)){?><font class="C999">职业：</font><?php echo $Ujob_str;?><br><?php }?>
            <?php if (!empty($Upay_str)){?><font class="C999">月薪：</font><?php echo $Upay_str;?><br><?php }?>
            <?php if (!empty($Uhouse_str)){?><font class="C999">房子：</font><?php echo $Uhouse_str;?><br><?php }?>
            <?php if (!empty($Ucar_str)){?><font class="C999">车子：</font><?php echo $Ucar_str;?><?php }?>
        </div>
        </td>
        <td width="180" align="left" valign="top">
        <div class="zoyqbox">
            <h3>工作地区<i></i></h3>
            <?php echo $Uareatitle; ?>
        </div>
        <div class="zoyqbox" style="margin-top:10px">
            <h3>户籍地区<i></i></h3>
            <?php echo $Uarea2title; ?>
        </div>
        </td>
        <td align="left" valign="top" style="padding-bottom:10px"><div class="zoyqbox"><h3>择偶要求<i></i></h3><?php echo $Umate_li_out;?></div></td>
      </tr>
    </table>
    <table class="table0" style="margin-top:0">
        <tr>
        <td height="50" align="left" class="border0 S14" style="min-width:980px">
            <form id="zeaiFORMso" method="get" action="<?php echo SELF; ?>">
                <font class="C999">配对范围：</font>
                <input type="radio" name="hyk" id="hyk1" class="radioskin" value="1"<?php echo ($hyk == 1)?' checked':'';?>><label for="hyk1" class="radioskin-label"><i class="i1"></i><b class="W80 S14">我的会员库</b></label>
                <input type="radio" name="hyk" id="hyk3" class="radioskin" value="3"<?php echo ($hyk == 3)?' checked':'';?>><label for="hyk3" class="radioskin-label"><i class="i1"></i><b class="W100 S14">整站会员库</b></label>
                <input type="checkbox" name="fhsf" id="fhsf" class="checkskin" value="1"<?php echo ($fhsf == 1)?' checked':''; ?>><label for="fhsf" class="checkskin-label"><i></i><b class="W100 S14">符合双方</b></label>
                <input type="hidden" name="uid" value="<?php echo $uid;?>">
                <input type="hidden" name="p" value="<?php echo $p;?>">
                <input type="hidden" name="t" value="<?php echo $t;?>">
                <button type="submit" class="btn HONG2 size3 yuan"><i class="ico">&#xe6c4;</i> 开始配对</button>
            </form>
        </td>
        </tr>
    </table>
<?php }?>

<?php if ($t == 4){?>
<table class="table0" style="min-width:950px;margin-bottom:20px">
<tr>
<td align="left" class="border0 S14">
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
	<form id="zeaiFORMso" method="get" action="<?php echo $SELF; ?>" onSubmit="return chkform();">
		学历 <script>zeai_cn__CreateFormItem('select','edu','<?php echo $edu; ?>','class="size2 SW"',edu_ARR);</script>　
		月收入 <script>zeai_cn__CreateFormItem('select','pay','<?php echo $pay; ?>','class="size2 SW"',pay_ARR);</script>　
		职业 <script>zeai_cn__CreateFormItem('select','job','<?php echo $job; ?>','class="size2 SW"',job_ARR);</script>　　　
		嫁娶形式 <script>zeai_cn__CreateFormItem('select','marrytype','<?php echo $marrytype; ?>','class="size2 SW"',marrytype_ARR);</script>
		
	  <br><br>
		年龄 <script>zeai_cn__CreateFormItem('select','age1','<?php echo $age1; ?>','class="size2 SW"',age_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','age2','<?php echo $age2; ?>','class="size2 SW"',age_ARR);</script>　　　
		　婚姻 <script>zeai_cn__CreateFormItem('select','love','<?php echo $love; ?>','class="size2 SW"',love_ARR);</script>　　　
		子女情况 <script>zeai_cn__CreateFormItem('select','child','<?php echo $child; ?>','class="size2 SW"',child_ARR);</script>　　　
		
	  <br><br>
		身高 <script>zeai_cn__CreateFormItem('select','heigh1','<?php echo $heigh1; ?>','class="size2 SW"',heigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','heigh2','<?php echo $heigh2; ?>','class="size2 SW"',heigh_ARR);</script>　　　
		　购车 <script>zeai_cn__CreateFormItem('select','car','<?php echo $car; ?>','class="size2 SW"',car_ARR);</script>　　　
		住房情况 <script>zeai_cn__CreateFormItem('select','house','<?php echo $house; ?>','class="SW"',house_ARR);</script>
		
		<br><br>
		体重 <script>zeai_cn__CreateFormItem('select','weigh1','<?php echo $weigh1; ?>','class="size2 SW"',weigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','weigh2','<?php echo $weigh2; ?>','class="size2 SW"',weigh_ARR);</script>　　　
		　地区 <script>LevelMenu3('a1|a2|a3|'+nulltext+'|<?php echo $a1; ?>|<?php echo $a2; ?>|<?php echo $a3; ?>','class="size2 SW"');</script>
		　　会员UID/昵称/姓名 <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="W200 input size2" placeholder="输入：会员UID/手机/姓名/昵称" value="<?php echo $Skeyword; ?>">
		　
		
		<br><br>配对范围：
		<input type="radio" name="hyk" id="hyk1" class="radioskin" value="1"<?php echo ($hyk == 1)?' checked':'';?>><label for="hyk1" class="radioskin-label"><i class="i1"></i><b class="W80 S14">我的会员库</b></label>
		<input type="radio" name="hyk" id="hyk3" class="radioskin" value="3"<?php echo ($hyk == 3 || empty($hyk))?' checked':'';?>><label for="hyk3" class="radioskin-label"><i class="i1"></i><b class="W200 S14">整站会员库</b></label>
		<input type="checkbox" name="fhsf" id="fhsf" class="checkskin" value="1"<?php echo ($fhsf == 1)?' checked':''; ?>><label for="fhsf" class="checkskin-label"><i></i><b class="W80 S14">符合双方</b></label>
		<input type="checkbox" name="grade2" id="grade2" class="checkskin" value="1"<?php echo ($grade2 == 1)?' checked':''; ?>><label for="grade2" class="checkskin-label"><i></i><b class="W50 S14">VIP会员</b></label>
		<input type="checkbox" name="photo_s" id="photo_s" class="checkskin" value="1"<?php echo ($photo_s == 1)?' checked':''; ?>><label for="photo_s" class="checkskin-label"><i></i><b class="W50 S14">有照片</b></label>            
		<button type="submit" class="btn HONG2 size3 yuan"><i class="ico">&#xe6c4;</i> 开始配对</button>
		<input type="hidden" name="uid" value="<?php echo $uid;?>">
		<input type="hidden" name="p" value="<?php echo $p;?>">
		<input type="hidden" name="t" value="<?php echo $t;?>">
	</form>
</td>
</tr>
</table>
<?php }?>


<style>
.pagebox{margin:40px auto 60px auto}
.uli{background-color:#F2F2F2;padding:10px;color:#666}
.uli li{background-color:#fff;width:310px;padding:5px;height:300px;margin:8px;float:left;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;overflow:hidden;position:relative}
.uli li dt,.uli li dd{height:180px;float:left}
.uli li dt{width:144px;cursor:zoom-in}
.uli li dt img{width:144px;height:180px;object-fit:cover;-webkit-object-fit:cover}
.uli li dd{width:130px;text-align:left;margin-left:10px;line-height:150%;font-size:14px}
.uli li em{width:100%;display:block;margin-top:10px;font-size:14px;text-align:left;float:left}
.uli li .ulibtn{width:100%;position:absolute;bottom:10px;left:0;text-align:center}
.uli li .ulibtn button{padding:0 8px;font-size:14px;margin:0 3px;line-height:32px;height:32px}
.uli li img.flag_2{position:absolute;top:30px;right:5px;width:70px}
</style>
<?php
$fields = ",mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2,mate_other";
$rt = $db->query("SELECT id,flag,nickname,truename,photo_s,mob,sex,grade,flag,areatitle,area2title,bz,tguid,heigh,weigh,birthday,edu,pay,job,love,marrytype,marrytime,child,house,car,subscribe,myinfobfb,companykind,smoking,drink".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	$sorthref = SELF."?".$parameter."&sort=";
	echo '<div class="uli">';
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid2=$id;
		$pwd = $rows['pwd'];
		$truename = dataIO($rows['truename'],'out');
		$photo_s   = $rows['photo_s'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$areatitle = $rows['areatitle'];
		$flag      = $rows['flag'];
		//$hnname    = dataIO($rows['hnname'],'out');
		$nickname  = dataIO($rows['nickname'],'out');
		$areatitle = $rows['areatitle'];
		$heigh = udata('heigh',intval($rows['heigh']));
		$birthday  = $rows['birthday'];
		$age   = (@getage($birthday)<=0)?'':@getage($birthday).'岁';
		$edu   = udata('edu',intval($rows['edu']));
		$pay   = udata('pay',intval($rows['pay']));
		$love      = udata('love',intval($rows['love']));
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:'';
		$photo_m_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'m'):HOST.'/res/photo_m'.$sex.'.png';
		$flag_str = ($flag==-2)?'<img src="images/flag_2.png" class="flag_2">':'';
		?>
        <li>
       	  <dt onClick="parent.piczoom('<?php echo smb($photo_s_url,'b'); ?>');"><img src="<?php echo $photo_m_url;?>"></dt>
          <dd>
                UID：<?php echo $uid2;?><br>
                姓名：<?php echo $truename;?><br>
                年龄：<?php echo $age;?><br>
                学历：<?php echo $edu;?><br>
                身高：<?php echo $heigh;?><br>
                月薪：<?php echo $pay;?><br>
                婚状：<?php echo $love;?><br>
                地区：<?php echo $areatitle;?>
          </dd>
            <em>
            希望另一半：<?php
			$mate_echo = trimhtml(mate_echo($rows,'text'));
			$mate_echo = str_replace(",","，",$mate_echo);
			echo gylsubstr($mate_echo,60,0,"utf-8",true);;
			?>
            </em>
            <?php echo $flag_str;?>
            <div class="ulibtn">
			<?php
			$rowf = $db->ROW(__TBL_QIANXIAN__,"uid","senduid=".$uid." AND uid=".$uid2,"num");
            if ($rowf){
                $tjstr = '已牵线';
                $tjscls= 'HONG2_';
            }else{
                $tjstr = '确认牵线';
                $tjscls= 'BAI';
            }
            ?>
            <button type="button" class="btn size3 <?php echo $tjscls;?> qianxian" senduid="<?php echo $uid;?>" uid="<?php echo $uid2;?>"><?php echo $tjstr;?></button>
            <button type="button" class="btn size3 BAI detail" uid2="<?php echo $uid2;?>" title2="<?php echo urlencode(trimhtml($nickname.' ｜ '.$uid2));?>">查看详情</button>
            <button type="button" class="btn size3 BAI card" uid2="<?php echo $uid2;?>" title2="<?php echo urlencode(trimhtml($nickname.' ｜ '.$uid2));?>">相亲卡</button>
			<?php
            $rowf = $db->ROW(__TBL_CRM_FAV__,"id","uid=".$uid." AND uid2=".$uid2);
            if ($rowf){
                $tjstr = '已收藏';
                $tjscls= 'LV_';
            }else{
                $tjstr = '收藏';
                $tjscls= 'BAI';
            }
            ?>
            <button type="button" class="btn size3 <?php echo $tjscls;?> fav" uid="<?php echo $uid;?>" uid2="<?php echo $uid2;?>"><?php echo $tjstr;?></button>
            </div>
        </li>
		<?php
	}
	echo '<div class="clear"></div></div>';
	?>
	<?php if ($total > $pagesize)echo '<div class="pagebox ">'.$pagelist.'</div>'; ?>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';

zeai.listEach('.qianxian',function(obj){
	obj.onclick = function(){
		var senduid = parseInt(obj.getAttribute("senduid")),
		uid = parseInt(obj.getAttribute("uid")),
		button_str = obj.innerHTML,str;
		str = (button_str=='已牵线')?'和该用户已经牵线，确定再次牵线么？':'确定要牵线么？';
		zeai.confirm(str,function(){
			zeai.alertplus({title:'请选择牵线发起方',content:'',title1:'会员要求',title2:'红娘主动',
				fn1:function(){qianxianFn(uid,senduid,1);},
				fn2:function(){qianxianFn(uid,senduid,2);}
			});
		});
	}
});
function qianxianFn(uid,senduid,sendkind){
	zeai.alertplus(0);
	zeai.ajax({url:'crm_user_detail_select'+zeai.extname,data:{submitok:'ajax_qianxian_add',uid:uid,senduid:senduid,sendkind:sendkind}},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
	});
}

zeai.listEach('.card',function(obj){
	var uid2 = parseInt(obj.getAttribute("uid2"));
	var title2 = obj.getAttribute("title2");
	obj.onclick = function(){zeai.iframe('生成【'+decodeURIComponent(title2)+'】相亲卡','u_card.php?uid='+uid2);}
});

zeai.listEach('.fav',function(obj){
	obj.onclick = function(){
		var uid  = parseInt(obj.getAttribute("uid"));
		var uid2 = parseInt(obj.getAttribute("uid2"));
		zeai.ajax({url:'crm_user_detail.php?submitok=fav_update&uid='+uid+'&uid2='+uid2},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);
			if(rs.flag==1){
				zeai.msg(rs.msg,{time:3});
				setTimeout(function(){location.reload(true);},1000);
			}else{
				zeai.msg(rs.msg);
			}
		});
	}
});

zeai.listEach('.detail',function(obj){
	var uid2 = parseInt(obj.getAttribute("uid2"));
	var title2 = obj.getAttribute("title2");
	obj.onclick = function(){zeai.iframe('【'+decodeURIComponent(title2)+'】个人信息','crm_user_detail.php?iframenav=1&t=2&uid='+uid2);}
});


</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>


<br><br><br>
<?php require_once 'bottomadm.php';
function mate_diy_SQL2() {
	global $_ZEAI,$Ubirthday,$Uheigh,$Uweigh,$Upay,$Uedu,$Ulove,$Ucar,$Uhouse,$Uareaid,$Uareaid2,$Ujob,$Uchild,$Umarrytime,$Ucompanykind,$Usmoking,$Udrink;
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	$SQL = "";$Uage = getage($Ubirthday);
	if (count($mate_diy) >= 1 && is_array($mate_diy)){
		foreach ($mate_diy as $k=>$V) {
			$ifmate = mate_diy_par($V,'ifmate');
			$ext    = mate_diy_par($V,'ext');
			if($ifmate!=1)continue;
			$mate_fld = 'mate_'.$V;
			$Udata = 'U'.$V;$Udata=$$Udata;
			switch ($ext) {
				case 'radio':if(ifint($Udata))$SQL .= " AND ($mate_fld=$Udata OR $mate_fld=0) ";break;
				case 'checkbox':if(!empty($Udata))$SQL .= " AND FIND_IN_SET($Udata,$mate_fld) ";break;
				case 'radiorange':
					$mate_fld1 = 'mate_'.$V.'1';$mate_fld2 = 'mate_'.$V.'2';
					$mate_data1 = intval($$tmp1);$mate_data2 = intval($$tmp2);
					if (ifint($Udata))$SQL .= " AND ($Udata >= $mate_fld1 OR $mate_fld1=0) AND ($Udata <= $mate_fld2 OR $mate_fld2=0) ";
				break;
				case 'area':if (!empty($Udata)){$SQL .= " AND ($mate_fld LIKE '%".$Udata."%' OR $mate_fld=0) ";}break;
			}
		}
	}
	return $SQL;
}
?>


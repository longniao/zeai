<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/udata.php';$sex_ARR = json_decode($_UDATA['sex'],true);$extifshow = json_decode($_UDATA['extifshow'],true);
header("Cache-control: private");
require_once ZEAI.'cache/config_vip.php';
$qianxian_num = json_decode($_VIP['qianxian_num'],true);
$meet_num     = json_decode($_VIP['meet_num'],true);
$bbs_intentionARR = json_decode($_CRM['bbs_intention'],true);
$qxflagARR = json_decode($_CRM['qxflag'],true);
$meet_flagARR = json_decode($_CRM['meet_flag'],true);

if(!in_array('crm_user_home',$QXARR))exit(noauth('暂无【浏览客户主页】权限'));

if($submitok == 'fav_update'){
	if(!in_array('crm_user_fav_add',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【客户收藏(增加)】权限'));
	if ( !ifint($uid))json_exit(array('flag'=>0,'msg'=>'客户UID不正确'));
	if ( !ifint($uid2))json_exit(array('flag'=>0,'msg'=>'收藏人UID不正确'));
	ifsqsh($uid);
	$row = $db->ROW(__TBL_CRM_FAV__,"id","uid=".$uid." AND uid2=".$uid2,"num");
	if ($row){
		$id=$row[0];
		$db->query("UPDATE ".__TBL_CRM_FAV__." SET px=".ADDTIME." WHERE id=".$id);
	}else{
		$db->query("INSERT INTO ".__TBL_CRM_FAV__." (uid,uid2,px,admid,admname) VALUES ($uid,$uid2,".ADDTIME.",$session_uid,'$session_truename')");
	}
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid2,'num');$nickname2= $row2[0];
	AddLog('【CRM收藏】新增->【'.$nickname.'（uid:'.$uid.'）】->【'.$nickname2.'（uid:'.$uid2.'）】');
	//
	json_exit(array('flag'=>1,'msg'=>'收藏成功'));
}elseif($submitok == 'fav_del_update'){
	if(!in_array('crm_user_fav_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【客户收藏(删除)】权限'));
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在'));
	$row = $db->ROW(__TBL_CRM_FAV__,"uid,uid2","id=".$id,"num");
	if (!$row)json_exit(array('flag'=>0,'msg'=>'记录不存在'));
	$uid=$row[0];$uid2=$row[1];
	ifsqsh($uid);
	$db->query("DELETE FROM ".__TBL_CRM_FAV__." WHERE id=".$id);
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid2,'num');$nickname2= $row2[0];
	AddLog('【CRM收藏】删除->【'.$nickname.'（uid:'.$uid.'）】->【'.$nickname2.'（uid:'.$uid2.'）】');
	//
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}
if ($t != 1){
	if ( !ifint($uid))alert_adm("客户UID不正确","back");
	$t = (ifint($t,'1-6','1'))?$t:1;
	$row = $db->ROW(__TBL_USER__,"*","id=".$uid,"name");
	if(!$row)alert_adm("UID输入有误或不存在此客户","back");
	$row_ext = $row;
	//
	$uname   = dataIO($row['uname'],'out');
	$truename   = dataIO($row['truename'],'out');
	$identitynum= dataIO($row['identitynum'],'out');
	$aboutus    = dataIO($row['aboutus'],'out');
	$birthday   = $row['birthday'];
	$birthday   = (!ifdate($birthday))?'':$birthday;
	$age        = (getage($birthday)>0)?'（'.getage($birthday).'岁）':'';
	$RZ         = $row['RZ'];$RZarr = explode(',',$RZ);
	$areaid     = dataIO($row['areaid'],'out');
	$areatitle  = dataIO($row['areatitle'],'out');
	$area2title = dataIO($row['area2title'],'out');
	$crm_ubz    = dataIO($row['crm_ubz'],'out');
	$heigh      = $row['heigh'];
	$weigh      = $row['weigh'];
	$love       = $row['love'];
	$edu        = $row['edu'];
	$pay        = $row['pay'];
	$house      = $row['house'];
	$car        = $row['car'];
	$nation     = $row['nation'];
	$marrytype  = $row['marrytype'];
	$job        = $row['job'];
	$child      = $row['child'];
	$blood      = $row['blood'];
	$tag        = $row['tag'];
	$tguid      = $row['tguid'];
	$tgpic      = $row['tgpic'];
	$kind       = $row['kind'];
	
	$agenttitle= dataIO($row['agenttitle'],'out');
	$admname   = dataIO($row['admname'],'out');
	$hnname    = dataIO($row['hnname'],'out');
	$hnname2   = dataIO($row['hnname2'],'out');
	$agentid   = intval($row['agentid']);
	$admid     = intval($row['admid']);
	$hnid      = intval($row['hnid']);
	$hnid2     = intval($row['hnid2']);
	$admtime   = intval($row['admtime']);
	$hntime    = intval($row['hntime']);
	$hntime2   = intval($row['hntime2']);
	
	$crm_ukind = $row['crm_ukind'];
	$crm_ugrade = $row['crm_ugrade'];
	
	$crm_usjtime1 = $row['crm_usjtime1'];
	$crm_usjtime2 = $row['crm_usjtime2'];
	$crm_qxnum = $row['crm_qxnum'];
	$crm_yjnum = $row['crm_yjnum'];
	
	$agenttitle=(!empty($agenttitle))?$agenttitle:'';
	//
	$address    = dataIO($row['address'],'out');
	$weixin     = dataIO($row['weixin'],'out');
	$qq         = dataIO($row['qq'],'out');
	$email      = dataIO($row['email'],'out');
	$mob        = dataIO($row['mob'],'out');
	$weixin_pic = $row['weixin_pic'];
	//
	$mate_age1      = intval($row['mate_age1']);
	$mate_age2      = intval($row['mate_age2']);
	$mate_heigh1    = intval($row['mate_heigh1']);
	$mate_heigh2    = intval($row['mate_heigh2']);
	$mate_pay       = $row['mate_pay'];
	$mate_edu       = $row['mate_edu'];
	$mate_areaid    = $row['mate_areaid'];
	$mate_areatitle = $row['mate_areatitle'];
	$mate_love      = $row['mate_love'];
	$mate_car       = $row['mate_car'];
	$mate_house     = $row['mate_house'];
	$mate_weigh1      = intval($row['mate_weigh1']);
	$mate_weigh2      = intval($row['mate_weigh2']);
	$mate_job         = $row['mate_job'];
	$mate_child       = $row['mate_child'];
	$mate_marrytime   = $row['mate_marrytime'];
	$mate_companykind = $row['mate_companykind'];
	$mate_smoking     = $row['mate_smoking'];
	$mate_drink       = $row['mate_drink'];
	$mate_areaid2     = $row['mate_areaid2'];
	$mate_areatitle2  = $row['mate_areatitle2'];
	$mate_other       = $row['mate_other'];
	//
	$mate_age       = $mate_age1.','.$mate_age2;
	$mate_age_str   = mateset_out($mate_age1,$mate_age2,'岁');
	$mate_age_str = str_replace("不限","",$mate_age_str);
	$mate_heigh     = $mate_heigh1.','.$mate_heigh2;
	$mate_heigh_str = mateset_out($mate_heigh1,$mate_heigh2,'cm');
	$mate_heigh_str = str_replace("不限","",$mate_heigh_str);
	$mate_weigh     = $mate_weigh1.','.$mate_weigh2;
	$mate_weigh_str = mateset_out($mate_weigh1,$mate_weigh2,'kg');
	$mate_weigh_str = str_replace("不限","",$mate_weigh_str);
	$mate_areaid_str  = (!empty($mate_areatitle))?$mate_areatitle:'';
	$mate_areaid2_str = (!empty($mate_areatitle2))?$mate_areatitle2:'';
	$mate_pay_str   = udata('pay',$mate_pay);
	$mate_edu_str   = udata('edu',$mate_edu);
	$mate_love_str  = udata('love',$mate_love);
	$mate_car_str   = udata('car',$mate_car);
	$mate_house_str = udata('house',$mate_house);
	$mate_job_str         = udata('job',$mate_job);
	$mate_child_str       = udata('child',$mate_child);
	$mate_marrytime_str   = udata('marrytime',$mate_marrytime);
	$mate_companykind_str = udata('companykind',$mate_companykind);
	$mate_smoking_str     = udata('smoking',$mate_smoking);
	$mate_drink_str       = udata('drink',$mate_drink);
	//
	$nickname = dataIO($row['nickname'],'out');
	$sex      = $row['sex'];
	$grade    = $row['grade'];
	$photo_s  = $row['photo_s'];
	$photo_f  = $row['photo_f'];
	$regtime  = YmdHis($row['regtime'],'Ymd');
	$endtime  = YmdHis($row['endtime'],'Ymd');
	$flag     = $row['flag'];
	//
	$uhref = Href('u',$uid);
	if(!empty($photo_s)){
		$photo_m_url = $_ZEAI['up2'].'/'.getpath_smb($photo_s,'m');
		$photo_b_url = getpath_smb($_ZEAI['up2'].'/'.$photo_s,'b');
		$photo_m_str = '<img src="'.$photo_m_url.'?'.ADDTIME.'" class="m">';
	}else{
		$photo_m_url = HOST.'/res/photo_m'.$sex.'.png';
		$photo_m_str = '<img src="'.$photo_m_url.'" class="m">';
	}
	$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
	$photo_fstr = ($photo_f == 0 && !empty($photo_s))?'<span>审核中</span>':'';
	$nickname = (empty($nickname))?$uname:$nickname;
	
	$Uphoto_s_url = $photo_s_url;
	$Uuid = $uid;
	
	//
	$qxneednum=$crm_qxnum;
	$qxnum1 = $db->COUNT(__TBL_QIANXIAN__,"flag=2 AND senduid=".$uid);
	$qxnum2 = $db->COUNT(__TBL_QIANXIAN__,"flag=1 AND senduid=".$uid);
	$qxnum3 = $db->COUNT(__TBL_QIANXIAN__,"flag=-1 AND senduid=".$uid);
	$rowqx = $db->ROW(__TBL_QIANXIAN__,"flag","senduid=".$uid." ORDER BY id DESC LIMIT 1",'name');
	$qxflag_str= crm_arr_title($qxflagARR,$rowqx['flag']);

	$yjneednum=$crm_yjnum;
	$yjnum1 = $db->COUNT(__TBL_CRM_MATCH__,"meet_flag=3 AND uid=".$uid);//完成
	$yjnum2 = $db->COUNT(__TBL_CRM_MATCH__,"meet_flag=1 AND uid=".$uid);//进行中
	$yjnum3 = $db->COUNT(__TBL_CRM_MATCH__,"meet_flag=2 AND uid=".$uid);//暂停
	$rowyj = $db->ROW(__TBL_CRM_MATCH__,"meet_flag","uid=".$uid." ORDER BY px DESC,id DESC LIMIT 1");
	$yjflag_str= crm_arr_title($meet_flagARR,$rowyj['meet_flag']);
	
	$crm_flag     = intval($row['crm_flag']);
	$crm_flag_str = ($crm_flag==3)?'<img src="images/crm_flag3.png" class="crm_flag3">':'';
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
body{background-color:#f0f0f0}
.linebox .title{color:#009688;font-size:16px}
/*table*/
.tableU{width:1300px;float:left;margin:10px 0 50px 20px}
.table0 td{padding:0px}
.table0 td.left{background-color:#fff;text-align:center;font-size:14px}
.table0 td.left .am{width:180px;height:225px;display:block;background-color:#fff;margin:10px auto;cursor:zoom-in}
.table0 td.left .m{width:180px;height:225px;display:block;margin:10px auto;object-fit:cover;-webkit-object-fit:cover}

.table td{background-color:#fff;padding:2px 8px;font-size:12px;color:#666;border:1px solid #f0f0f0}
.table .tdL{width:50px;color:#999}
.table .tbodyT{height:40px;font-size:16px;position:relative;font-weight:normal}
.table .tbodyT div{position:relative}
.table .tbodyT em{display:inline-block;font-size:14px;color:#666;margin-left:20px}.table .tbodyT em b{font-size:16px;margin:0 3px;font-family:Arial, Helvetica, sans-serif}
.table .tbodyT button{position:absolute;right:0px;top:-3px}

table.list{width:100%;border-collapse:collapse;margin-bottom:0}
table.list tr:hover{background-color:#F9F9FA}
table.list td{border:0;border-bottom:#eee 1px solid;padding:3px 0}
table.list tr:last-child td{border:0;padding-bottom:0}
table.list td img.m{width:40px;height:40px;border-radius:30px;object-fit:cover;-webkit-object-fit:cover;display:block}
table.list th{border:0;border-bottom:#eee 0px solid;font-size:12px;color:#999;padding:7px 0;;background-color:#F5F6FA}

.leftbtn{width:90%;margin:20px auto;box-sizing:border-box;color:#999;line-height:40px;text-align:center}
.leftbtn{display:block;background-color:#009688;color:#fff;-webkit-transition:all .5s;-moz-transition:all .5s;transition:all .5s}
.leftbtn:hover{background-color:#30AA9F}
.leftbtn i.ico{font-size:24px;display:inline-block;line-height:18px;vertical-align:middle}

.leftUinfo{padding:0;margin:20px auto;background-color:#fff;box-sizing:border-box;text-align:left}
.leftUinfo li{width:90%;margin:0 auto;line-height:30px;border-bottom:#eee 0px solid}
.leftUinfo li:last-child{border:0}
.leftUinfo li dt,.leftUinfo li dd{display:inline-block;font-size:12px}
.leftUinfo li dd{display:inline-block;font-family:Arial, Helvetica, sans-serif}
.leftUinfo li dt{color:#999}
.leftUinfo li.text{line-height:12px;padding-bottom:8px}
.leftUinfo li dd.gradeico{width:100%;text-align:center;line-height:24px;color:#999;font-size:12px;padding-top:5px}
.leftUinfo li dd.gradeico em{line-height:20px;margin-top:5px}
.leftUinfo li dd.gradeico a{border-radius:2px}

.table0 .usize2{margin-top:5px}
.table0 .usize2 li dt,.table0 .usize2 li dd{font-size:14px}
.pathlist img{margin:8px 5px 5px 2px;width:30px;height:30px;object-fit:cover;-webkit-object-fit:cover;border-radius:2px}	
.am{position:relative}
.am .crm_kind{width:100%;position:absolute;bottom:0;right:0;background-color:rgba(255,255,255,0.8);color:#333;font-size:14px;line-height:30px}

img.crm_flag3{width:100px;display:block;margin:-10px auto 0 auto}
.pathlist img{margin:8px 5px 5px 2px;width:30px;height:30px;object-fit:cover;-webkit-object-fit:cover;border-radius:2px}
</style>
<body>
<?php if ($iframenav != 1){?>
<div class="navbox">
    <a href="<?php echo SELF; ?>?t=1&uid=<?php echo $uid; ?>"<?php echo (empty($t) || $t==1)?' class="ed"':''; ?>>按UID查询</a>
    <a href="<?php echo SELF; ?>?t=2&uid=<?php echo $uid; ?>"<?php echo ($t == 2)?' class="ed"':''; ?>>当前客户UID：<?php echo $uid;?></a>
    <a href="crm_user_detail_select.php?t=3&uid=<?php echo $uid;?>">配对-筛选</a>
<div class="clear"></div></div><div class="fixedblank"></div>
<?php }?>
<?php if ($t == 1){ ?>
<style>
.uidsobox{margin:100px auto 0 auto;border:#eee 1px solid;width:700px;line-height:200px;background-color:#f8f8f8;font-size:24px}
</style>
<div class="uidsobox">
    <form action="<?php echo SELF; ?>" method="post">
    输入客户UID
    <input name="uid" type="text" class="size3 W150" id="uid" size="8" maxlength="9" value="<?php echo $uid; ?>" style="font-size:18px;background-color:#fff"> 
    <button type="submit" class="btn size3">进入配对</button>
    <input name="submitok" type="hidden" value="mod" />
    <input name="t" type="hidden" value="2" />
    </form>
</div>
<?php }?>


<?php if ($t != 1){ ?>
<table class="table0 tableU">
<tr>
	<td width="200" height="500" align="center" valign="top" class="left">
    <a href="javascript:;" class="sexbg<?php echo $sex; ?> am" onClick="parent.parent.piczoom('<?php echo $photo_b_url; ?>');"><?php echo $photo_m_str;if (!empty($crm_ukind)){?><div class="crm_kind"><?php echo udata('crm_ukind',$crm_ukind);?></div><?php }?></a>
	<?php
    $rt=$db->query("SELECT path_s FROM ".__TBL_PHOTO__." WHERE uid=".$uid." ORDER BY id DESC LIMIT 8");
    $photo_total = $db->num_rows($rt);
    if ($photo_total>0) {
        echo '<div class="pathlist">';
		$pic_list = array();
        for($i=1;$i<=$photo_total;$i++) {
            $rows = $db->fetch_array($rt);
            if(!$rows) break;
            $path_s   = $rows[0];
            $dst_s    = $_ZEAI['up2'].'/'.$path_s;
            $dst_b    = smb($dst_s,'b');
			?>
			<a href="javascript:;" title="放大" class="zoom" onClick="parent.parent.piczoom('<?php echo $dst_b; ?>');"><img src="<?php echo $dst_s;?>" alt="放大"></a>
        <?php }
        echo '</div>';
    }?>
    <div class="leftUinfo usize2">
		<li><dt>UID：</dt><dd><?php echo $uid;?></dd></li>
		<li><dt>昵称：</dt><dd><?php echo uicon($sex.$grade); ?><span style="vertical-align: middle;"><?php echo $nickname; ?></span></dd></li>
		<?php if (!empty($truename)){?><li><dt>姓名：</dt><dd><?php echo $truename; ?></dd></li><?php }?>
    </div>
    
    <?php if ($crm_ugrade>0){?>
    <div class="leftUinfo">
		<div class="linebox"><div class="line"></div><div class="title BAI">签约信息</div></div>
		<li><dd class="gradeico">
		<?php
		echo $crm_flag_str;
		echo crm_ugrade_time($uid,$crm_ugrade,'btn_djs',$crm_usjtime1,$crm_usjtime2);
		echo '起始日：'.YmdHis($crm_usjtime1,'Ymd');
		$rtht=$db->query("SELECT id,price,htcode FROM ".__TBL_CRM_HT__." WHERE uid=".$uid." ORDER BY id DESC");
		$totalht = $db->num_rows($rtht);
		if ($totalht > 0) {	
			for($iht=1;$iht<=$totalht;$iht++) {
				$rowsht = $db->fetch_array($rtht,'name');
				if(!$rowsht) break;
				$htid =$rowsht['id'];$price =$rowsht['price'];
				$htcode=dataIO($rowsht['htcode'],'out');
				echo '<div><span class="textmiddle C666">合同：'.$htcode.'</span>';//.'<font class="Cf00">￥'.$price.'</font>'?>
                <a title="查看合同详情" class="btn size1 BAI picmiddle" onClick="zeai.iframe('<?php echo '<img src='.$Uphoto_s_url.' class=photo_s_iframe>';?>【<?php echo $Uuid;?>】合同详情','crm_gj_yj.php?submitok=ht_detail&fid=<?php echo $htid;?>',700,520)">详情</a>
				<?php echo '</div>';
			}?>
		<?php }
		?>
        </dd></li>
    </div>
    <?php }?>
    
    <div class="leftUinfo">
		<div class="linebox"><div class="line"></div><div class="title BAI">归属信息</div></div>
		<?php if (!empty($agenttitle)){?><li><dt>所属门店：</dt><dd><?php echo $agenttitle;?></dd></li><?php }?>
        <?php if ($admid>0){?><li style="border:0"><dt>认领红娘：</dt><dd><?php echo $admname.'<font class="C999">（ID:'.$admid.'）</font>';?></dd></li><?php }?>
        <?php if ($admtime>0){?><li class="text"><dt>认领时间：</dt><dd><?php echo YmdHis($admtime,'Ymd');?></dd></li><?php }?>
		<?php if ($hnid>0){?><li style="border:0"><dt>售前红娘：</dt><dd><?php echo $hnname.'<font class="C999">（ID:'.$hnid.'）</font>';?></dd></li><?php }?>
        <?php if ($hntime>0){?><li class="text"><dt>分配时间：</dt><dd><?php echo ($hntime>0)?YmdHis($hntime,'Ymd'):'';?></dd></li><?php }?>
		<?php if ($hnid2>0){?><li style="border:0"><dt>售后红娘：</dt><dd><?php echo $hnname2.'<font class="C999">（ID:'.$hnid2.'）</font>';?></dd></li><?php }?>
        <?php if ($hntime2>0){?><li class="text"><dt>分配时间：</dt><dd><?php echo ($hntime2>0)?YmdHis($hntime2,'Ymd'):'';?></dd></li><?php }?>
    </div>
    
    <div class="leftUinfo">
		<div class="linebox"><div class="line"></div><div class="title BAI">牵线信息</div></div>
		<li><dt>需牵线：</dt><dd><b><?php echo $qxneednum;?></b> 次</dd></li>
        <li><dt>成功了：</dt><dd><b><?php echo $qxnum1;?></b> 次</dd></li>
		<li><dt>进行中：</dt><dd><b><?php echo $qxnum2;?></b> 次</dd></li>
		<li><dt>失败了：</dt><dd><b><?php echo $qxnum3;?></b> 次</dd></li>
		<?php if (!empty($qxflag_str)){?><li><dt>最近状态：</dt><dd><?php echo $qxflag_str;?></dd></li><?php }?>
    </div>
    <div class="leftUinfo">
		<div class="linebox"><div class="line"></div><div class="title BAI">约见信息</div></div>
		<li><dt>需约见：</dt><dd><b><?php echo $yjneednum;?></b> 次</dd></li>
        <li><dt>已完成：</dt><dd><b><?php echo $yjnum1;?></b> 次</dd></li>
		<li><dt>进行中：</dt><dd><b><?php echo $yjnum2;?></b> 次</dd></li>
		<li><dt>暂停中：</dt><dd><b><?php echo $yjnum3;?></b> 次</dd></li>
		<?php if (!empty($yjflag_str)){?><li><dt>最近状态：</dt><dd><?php echo $yjflag_str;?></dd></li><?php }?>
    </div>
   
     <div class="leftUinfo">
		<div class="linebox"><div class="line"></div><div class="title BAI">帐号信息</div></div>
		<li><dt>注册时间：</dt><dd><?php echo $regtime;?></dd></li>
        <li><dt>最近登录：</dt><dd><?php echo $endtime;?></dd></li>
		<li><dt>帐号状态：</dt><dd><?php echo flagtitle($flag);?></dd></li>
		<li><dt>是否线上：</dt><dd><?php echo user_kind($kind); ?></dd></li>
    </div>
    
    </td>
  <td valign="top" style="padding:0 0 0 15px">
  
  <table class="table W100_">
    <tr><td height="20" colspan="2" class="tbodyT"><div class="tiaose">基本资料<button type="button" class="btn BAI size2" onClick="zeai.openurl('u_mod_data.php?t=1&iframenav=1&submitok=mod&ifmini=1&uid=<?php echo $uid;?>',700,520)">编辑</button></div></td></tr>
     <!-- 基本资料 -->
	<?php if(!empty($sex)){?><tr><td class="tdL">性　　别</td><td class="tdR"><?php echo udata('sex',$sex); ?></td></tr><?php }?>
    <?php if(!empty($birthday) && $birthday!='0000-00-00'){?><tr><td class="tdL">生　　日</td><td class="tdR"><?php echo $birthday.$age; ?></td></tr><?php }?>
    <?php if(!empty($areatitle)){?><tr><td class="tdL">工作地区</td><td class="tdR"><?php echo $areatitle; ?></td></tr><?php }?>
    <?php if(!empty($area2title)){?><tr><td class="tdL">户籍地区</td><td class="tdR"><?php echo $area2title; ?></td></tr><?php }?>
    <?php if(!empty($identitynum)){?><tr><td class="tdL">身份证号</td><td class="tdR"><?php echo $identitynum; ?></td></tr><?php }?>
    <?php if(!empty($love)){?><tr><td class="tdL">婚姻状况</td><td class="tdR"><?php echo udata('love',$love); ?></td></tr><?php }?>
    <?php if(!empty($heigh)){?><tr><td class="tdL">身　　高</td><td class="tdR"><?php echo udata('heigh',$heigh); ?></td></tr><?php }?>
    <?php if(!empty($weigh)){?><tr><td class="tdL">体　　重</td><td class="tdR"><?php echo udata('weigh',$weigh); ?></td></tr><?php }?>
    <?php if(!empty($edu)){?><tr><td class="tdL">学　　历</td><td class="tdR"><?php echo udata('edu',$edu); ?></td></tr><?php }?>
    <?php if(!empty($pay)){?><tr><td class="tdL">月　　薪</td><td class="tdR"><?php echo udata('pay',$pay); ?></td></tr><?php }?>
    <?php if(!empty($house)){?><tr><td class="tdL">房　　子</td><td class="tdR"><?php echo udata('house',$house); ?></td></tr><?php }?>
    <?php if(!empty($car)){?><tr><td class="tdL">车　　子</td><td class="tdR"><?php echo udata('car',$car); ?></td></tr><?php }?>
    <?php if(!empty($job)){?><tr><td class="tdL">职　　业</td><td class="tdR"><?php echo udata('job',$job); ?></td></tr><?php }?>
    <?php if(!empty($aboutus)){?><tr><td class="tdL">自我介绍</td><td class="tdR"><?php echo $aboutus; ?></td></tr><?php }?>
    <?php if(!empty($marrytype)){?><tr><td class="tdL">嫁娶形式</td><td class="tdR"><?php echo udata('marrytype',$marrytype); ?></td></tr><?php }?>
    <?php if(!empty($child)){?><tr><td class="tdL">子女情况</td><td class="tdR"><?php echo udata('child',$child); ?></td></tr><?php }?>
    <?php if(!empty($blood)){?><tr><td class="tdL">血　　型</td><td class="tdR"><?php echo udata('blood',$blood); ?></td></tr><?php }?>
    <?php if(!empty($nation)){?><tr><td class="tdL">民　　族</td><td class="tdR"><?php echo udata('nation',$nation); ?></td></tr><?php }?>
    <?php if(!empty($crm_ubz)){?><tr><td class="tdL B">备注</td><td class="tdR"><?php echo $crm_ubz; ?></td></tr><?php }?>
    
    <!-- 认证信息 -->
    <tr><td colspan="2" class="center tbodyT"><div class="tiaose">认证信息<button type="button" class="btn BAI size2" onClick="zeai.openurl('u_mod_data.php?t=6&iframenav=1&submitok=mod&ifmini=1&uid=<?php echo $uid;?>',700,520)">编辑</button></div></td></tr>
	<?php
    if (count($RZarr) >= 1 && is_array($RZarr) && !empty($RZ)){
        foreach ($RZarr as $k=>$V) {?>
        	<tr><td class="tdL"><?php echo rz_data_info($V,'title');?></td><td class="tdR S16">
			<?php
			if(in_array($V,$RZarr)){
				echo "<i class='ico S18 picmiddle' style='color:#45C01A'>&#xe60d;</i>　";?>
            <?php }?>
            <a title="查看详细" class="btn size1 BAI picmiddle" onClick="zeai.iframe('<?php echo '<img src='.$Uphoto_s_url.' class=photo_s_iframe>';?>【<?php echo $Uuid;?>】认证资料','crm_gj_yj.php?submitok=rz_list&uid=<?php echo $Uuid;?>',700,520)">查看</a>
            
            </td></tr>
    <?php }}?>

	<!-- 择偶要求 -->
	<tr><td colspan="2" class="center tbodyT"><div class="tiaose">择偶要求<button type="button" class="btn BAI size2" onClick="zeai.openurl('u_mod_data.php?t=4&iframenav=1&submitok=mod&ifmini=1&uid=<?php echo $uid;?>',700,520)">编辑</button></div></td></tr>
	<?php
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	if(!empty($mate_age1) || !empty($mate_age2) || !empty($mate_heigh1) || !empty($mate_heigh2) || !empty($mate_pay) || !empty($mate_edu) || !empty($mate_areatitle) || !empty($mate_love) || !empty($mate_house) || !empty($mate_other) ){
		if (count($mate_diy) >= 1 && is_array($mate_diy)){?>
            <?php
			foreach ($mate_diy as $k=>$V) {
				$tmpm     = 'mate_'.$V.'_str';
				$mate_str = $$tmpm;
				?>
				<?php if(!empty($mate_str)){?><tr><td class="tdL"><?php echo mate_diy_par($V);?></td><td class="tdR S16"><?php echo $mate_str; ?></td></tr><?php }?>
                
	<?php }}}?>
    <?php if(!empty($mate_other)){?><tr><td class="tdL">其他要求</td><td class="tdR S16"><?php echo dataIO($mate_other,'wx'); ?></td></tr><?php }?>
    
    
    <!--联系方法-->
    <?php if(crm_ifcontact($agentid,$admid,$hnid,$hnid2)){?>
	<tr><td colspan="2" class="center tbodyT"><div class="tiaose">联系方法<button type="button" class="btn BAI size2" onClick="zeai.openurl('u_mod_data.php?t=3&iframenav=1&submitok=mod&ifmini=1&uid=<?php echo $uid;?>',700,520)">编辑</button></div></td></tr>
	<?php
	if( !empty($mob) || !empty($weixin) || !empty($weixin_pic) || !empty($qq) || !empty($email) || !empty($address)  ){?>
        <!--联系方法-->
        <?php if(!empty($mob)){?><tr><td class="tdL">手　　机</td><td class="tdR"><?php echo $mob; ?></td></tr><?php }?>
        <?php if(!empty($weixin)){?><tr><td class="tdL">微 信 号</td><td class="tdR"><?php echo $weixin; ?></td></tr><?php }?>
        <?php if(!empty($weixin_pic)){?><tr><td class="tdL">微信二维码</td><td class="tdR"><div class="picli60"><li id="weixin_picshow"<?php echo (!empty($weixin_pic))?' style="display:block"':' style="display:none"'?>><img src="<?php echo (!empty($weixin_pic))?$_ZEAI['up2'].'/'.$weixin_pic:'';?>"></li></div></td></tr>
		<script>weixin_picshow.onclick = function(){parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$weixin_pic;?>');}</script>
		<?php }?>
        <?php if(!empty($qq)){?><tr><td class="tdL">QQ</td><td class="tdR"><?php echo $qq; ?></td></tr><?php }?>
        <?php if(!empty($email)){?><tr><td class="tdL">邮　　箱</td><td class="tdR"><?php echo $email; ?></td></tr><?php }?>
        <?php if(!empty($address)){?><tr><td class="tdL">地　　址</td><td class="tdR"><?php echo $address; ?></td></tr><?php }?>
	<?php }?>
    <?php }?>
    
	<!-- 详细资料 -->
	<tr><td colspan="2" class="center tbodyT"><div class="tiaose">详细资料<button type="button" class="btn BAI size2" onClick="zeai.openurl('u_mod_data.php?t=2&iframenav=1&submitok=mod&ifmini=1&uid=<?php echo $uid;?>',700,520)">编辑</button></div></td></tr> 
     <?php
		if (@count($extifshow) >= 1 && is_array($extifshow)){
			$e=0;
			foreach ($extifshow as $V) {
				$data = dataIO($row_ext[$V['f']],'out');
				switch ($V['s']) {
                    case 1:$Fkind = 'ipt';$span=$data;break;
                    case 2:$Fkind = 'slect';$span=udata($V['f'],$data);break;
                    case 3:$Fkind = 'chckbox';$span=checkbox_div_list_get_listTitle($V['f'],$data);break;
				}
				$F = $V['f'];
				if (!empty($span)){?>
                	<tr><td class="tdL"><?php echo $V['t'];?></td><td class="tdR"><?php echo $span;?></td></tr><?php
				}
			}
		}
		?>
	<?php if(!empty($tag)){?><tr><td class="tdL">标　　签</td><td class="tdR"><?php echo checkbox_div_list_get_listTitle('tag'.$sex,$tag);?></td></tr><?php }?>
     
</table> 
    
    
    </td>
  <td width="800" align="left" valign="top" style="padding:0 0 0 15px">
<?php 
$meet_neednum=$meet_num[$grade];
$meet_num_ed = $db->COUNT(__TBL_CRM_MATCH__,"meet_flag=3 AND (uid=".$uid." OR uid2=".$uid.")");
?>
<table class="table W100_">
    <tr>
        <td height="20" class="tbodyT">
        <div class="tiaose">售后约见<?php if ($crm_ugrade>0){?><em>【<?php echo crm_ugrade_title($crm_ugrade);?>】需成功约见<b><?php echo $yjneednum;?></b>次，当前<b><?php echo $yjnum1;?></b>次</em><?php }?>
        <button type="button" class="btn size2" onClick="zeai.iframe('【<?php echo $uid;?>】约见工单','crm_gj_yj.php?submitok=yj_add&uid=<?php echo $uid;?>',700,520)"><i class="ico addico">&#xe620;</i>新增约见</button>
        </div>
        </td>
    </tr>
    <tr>
      <td height="200" valign="top" style="padding-top:5px;padding-bottom:0">
    
<!---->

<?php
if(@in_array('crm_match_view',$QXARR)){
	//$rt = $db->query("SELECT a.*,U.uname,U.nickname,U.truename,U.sex,U.grade,U.photo_s FROM ".__TBL_CRM_MATCH__." a,".__TBL_USER__." U WHERE a.uid2=U.id AND a.uid=".$uid." ORDER BY a.px DESC,a.id DESC");
	$rt = $db->query("SELECT * FROM ".__TBL_CRM_MATCH__." WHERE uid=".$uid." OR uid2=".$uid." ORDER BY px DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<br><br><div class='nodataicoS'><i></i>暂无约见";
		echo "<br><br><a class='aQINGed' onclick=\"zeai.iframe('【".$uid."】约见工单','crm_gj_yj.php?submitok=yj_add&uid=".$uid."',700,520)\">新增</a>";
		echo "</div><br><br>";
	} else {
	?>
    <table class="list" >
    <tr>
    <th colspan="2" align="center">约见客户</th>
    <th width="150" align="center">服务方式</th>
    <th align="center">反馈情况</th>
    <th width="80" align="center">红娘</th>
    <th width="60" align="center">工单状态</th>
    <th width="80" align="center">见面时间</th>
    <th width="50" align="center">操作</th>
    </tr>
    <?php
    for($i=1;$i<=$total;$i++) {
        $rows = $db->fetch_array($rt,'name');
        if(!$rows) break;
        $id       = $rows['id'];
        $datauid  = $rows['uid'];
        $datauid2 = $rows['uid2'];

		if($datauid == $uid){
			$rows2 = $db->ROW(__TBL_USER__,"nickname,truename,sex,grade,photo_s","id=".$datauid2,'name');
			$sex      = $rows2['sex'];
			$grade    = $rows2['grade'];
			$photo_s  = $rows2['photo_s'];
			$nickname = dataIO($rows2['nickname'],'out');
			$truename = dataIO($rows2['truename'],'out');
			$uid2 = $datauid2;
		}else{
			$rows2 = $db->ROW(__TBL_USER__,"nickname,truename,sex,grade,photo_s","id=".$datauid,'name');
			$sex      = $rows2['sex'];
			$grade    = $rows2['grade'];
			$photo_s  = $rows2['photo_s'];
			$nickname = dataIO($rows2['nickname'],'out');
			$truename = dataIO($rows2['truename'],'out');
			$uid2 = $datauid;
		}
		
		$fwfs     = dataIO($rows['fwfs'],'out');
		$fkqk     = dataIO($rows['fkqk'],'out');
		$addtime  = YmdHis($rows['addtime'],'Ymd');
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$href        = Href('crm_u',$uid2);
		$nickname = (!empty($truename))?$truename:$nickname;
		$nickname = '<div class="S12 C999">'.$nickname.'</div>';
		$admid_mh   = $rows['admid'];
		$admname_mh = dataIO($rows['admname'],'out');
		$nexttime   = ($nexttime>0)?YmdHis($rows['nexttime']):'';
		$meet_flag  = $rows['meet_flag'];
		$meet_flag3 = $rows['meet_flag3'];
		$meet_ifagree  = $rows['meet_ifagree'];
		$meet_ifagree2 = $rows['meet_ifagree2'];
		$pathlist      = $rows['pathlist'];
    ?>
    <tr>
    <td width="60" height="30" align="center" >
    	<a href="<?php echo Href('crm_u',$uid2);?>&iframenav=1" class="yjdetail"><img src="<?php echo $photo_s_url; ?>" class="m"></a>
    	
    </td>
    <td width="100" height="30" align="left" ><?php echo uicon($sex.$grade);?><font style="vertical-align:middle"><?php echo $uid2;?></font><?php echo $nickname;?></td>
    <td width="150" height="30" align="center"><?php echo $fwfs;?></td>
    <td align="center" style="word-break:break-all;word-wrap:break-word;"><?php echo $fkqk;
	if(!empty($pathlist)){
		$ARR=explode(',',$pathlist);
		$pathlist=array();
		echo '<div class="pathlist">';
		foreach ($ARR as $V) {?>
			<a href="javascript:;" class="zoom" onClick="parent.parent.piczoom('<?php echo getpath_smb($_ZEAI['up2'].'/'.$V,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$V;?>"></a>
			<?php
		}
		echo '</div>';
	}
	?></td>
    <td width="80" align="center" class="C999"><?php if(!empty($admname_mh)){echo $admname_mh.'<br><font class="C999">ID:12'.$admid_mh.'</font>';}?></td>
    <td width="60" align="center" class="C999"><?php echo crm_arr_title($meet_flagARR,$meet_flag);?></td>
    <td width="80" height="30" align="center" class="C666" style="font-family:Arial"><?php echo $addtime;?></td>
    <td width="50" align="center"><a title="修改/详细查看" class="btn size1 BAI" onClick="zeai.iframe('<?php echo '<img src='.$photo_s_url.' class=photo_s_iframe>';?>【<?php echo $uid2;?>】约见工单','crm_gj_yj.php?submitok=yj_list&uid=<?php echo $uid2;?>',700,520)">查看</a></td>
    </tr>
    <?php } ?>
    </table>
    <br>
	<?php
	}
}else{echo "<div class='nodataico'><i></i>暂无【客户约见(查看)】权限</div>";}?>
<!---->


    </td></tr>
</table>
  
<br>
<!--跟进-->
<table class="table W100_">
    <tr>
        <td height="20" class="tbodyT"><div class="tiaose">跟进小计
        <button type="button" class="btn size2" onClick="zeai.iframe('【<?php echo $uid;?>】跟进记录','crm_gj_yj'+zeai.ajxext+'submitok=gj_add&uid=<?php echo $uid;?>',700,450)"><i class="ico addico">&#xe620;</i>新增跟进</button></div></td>
    </tr>
    <tr>
        <td height="200" valign="top">
<style>
.track-rcol {width: 100%}
.track-list {margin:10px;padding-left: 5px;position: relative;}
.track-list li {position: relative;padding:9px 0 0 25px;line-height:18px;border-left: 1px solid #d9d9d9}
.track-list li:first-child {color:red;padding-top:0;border-left-color:#fff;}
.track-list li .node-icon {position:absolute;left:-6px;top:50%;width: 11px;height: 11px;background: url(images/sjz.png) -11px top no-repeat;}
.track-list li:first-child .node-icon {background-position: 0 top;top:30%}
.track-list li .time {margin-right:20px;position: relative;top:0px;display: inline-block;vertical-align: middle;}
.track-list li .txt {position:relative;top:0px;display:inline-block;vertical-align:middle}
.track-list li:first-child .time {margin-right:20px}
.track-list li:first-child .txt {max-width:600px}
</style>
<?php
if(@in_array('crm_bbs_view',$QXARR)){
	$rt = $db->query("SELECT * FROM ".__TBL_CRM_BBS__." WHERE uid=".$uid." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<br><br><div class='nodataicoS'><i></i>暂无跟进";
		echo "<br><br><a class='aQINGed' onclick=\"zeai.iframe('【".$uid."】跟进管理','crm_gj_yj.php?submitok=gj_add&uid=".$uid."',700,450)\">新增</a>";
		echo "</div><br><br>";
	} else {
	?>
    <div class="track-rcol">
        <div class="track-list">
            <ul>
            	<?php
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows) break;
					$id      = $rows['id'];
					$uid     = $rows['uid'];
					$addtime = YmdHis($rows['addtime'],'YmdHi');
					$content = trimhtml(strip_tags(dataIO($rows['content'],'out')));
					
					$content=gylsubstr($content,15,0,"utf-8",true);
					
					$admid   = $rows['admid'];
					$admname = dataIO($rows['admname'],'out');
					$nexttime   = intval($rows['nexttime']);
					$intention  = intval($rows['intention']);
					$intention_str = '【'.crm_arr_title($bbs_intentionARR,$intention).'】';
					$nexttime_str  = ($nexttime>0)?' （下次联系：'.YmdHis($nexttime,'Ymd').'）':'';
				?>
                <li>
                    <i class="node-icon"></i>
                    <span class="time"><?php echo $addtime;?></span>
                    <span class="txt"><?php echo $intention_str.$content.$nexttime_str;if(!empty($admname)){echo '　<font class="C999"><i class="ico">&#xe62d;</i> '.$admname.'</font><font class="C999">(ID:'.$admid.')</font>';}?></span>　<a title="详细查看" class="btn size1 BAI" onClick="zeai.iframe('<?php echo '<img src='.$Uphoto_s_url.' class=photo_s_iframe>';?>【<?php echo $Uuid;?>】跟进信息','crm_gj_yj.php?submitok=gj_list&uid=<?php echo $Uuid;?>',700,520)">查看</a>
                </li>
                <?php }?>
            </ul>
        </div>
    </div>
	<?php }    
}else{echo "<div class='nodataico'><i></i>暂无【客户跟进(查看)】权限</div>";}?>
        </td>
    </tr>
</table><br>


<!---->
<table class="table W100_">
    <tr>
        <td height="20" class="tbodyT"><div class="tiaose">收藏客户</div></td>
    </tr>
    <tr>
        <td height="200" valign="top">
<?php
if(@in_array('crm_user_fav_view',$QXARR)){
	$rt = $db->query("SELECT a.id,a.uid2,U.sex,U.grade,U.photo_s,U.nickname FROM ".__TBL_CRM_FAV__." a,".__TBL_USER__." U WHERE a.uid=".$uid." AND a.uid2=U.id ORDER BY a.px DESC,a.id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<br><br><div class='nodataicoS'><i></i>暂无收藏";
		echo "<br><br><a class='aQINGed' href='crm_select.php?k=yj&t=3&uid=".$uid."'>新增</a>";
		echo "</div><br><br>";
	} else {
	?>
    <style>
	.fav-list{margin-top:20px}
	.fav-list li{width:90px;float:left;text-align:center;position:relative}
	.fav-list li img.m{width:40px;height:40px;border-radius:30px;display:block;margin:10px auto}
	.fav-list i{position:absolute;top:0px;right:19px;color:#aaa;font-size:14px}
	.fav-list i:hover{cursor:pointer;color:#E50025}
    </style>
    <div class="fav-list">
		<?php
        for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'name');
            if(!$rows) break;
            $id     = $rows['id'];
            $uid2   = $rows['uid2'];
            $sex    = $rows['sex'];
            $grade  = $rows['grade'];
            $photo_s= $rows['photo_s'];
            $nickname= dataIO($rows['nickname'],'out');
            $photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
            $sexbg       = (empty($photo_s))?' class="m sexbg'.$sex.'"':' class="m"';
        ?>
        <li>
            <a href="<?php echo Href('crm_u',$uid2);?>&iframenav=1"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a>
            <span><?php echo uicon($sex.$grade);?><font class="picmiddle"><?php echo $uid2;?></font></span>
            <i class="ico addico favdel" title="删除" clsid="<?php echo $id;?>">&#xe62c;</i>
        </li>
        <?php }?>
		 <script>
            zeai.listEach('.favdel',function(obj){
                var id = parseInt(obj.getAttribute("clsid"));
                obj.onclick = function(){
                    zeai.confirm('确定删除么？',function(){
                        zeai.ajax({url:'crm_user_detail.php?submitok=fav_del_update&id='+id},function(e){rs=zeai.jsoneval(e);
                            zeai.msg(0);zeai.msg(rs.msg);
                            if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
                        });
                    });
                }
            });
         </script>   
        
    </div>
	<?php }
}else{echo "<div class='nodataico'><i></i>暂无【客户收藏(查看)】权限</div>";}?>
   
        </td>
    </tr>
</table><br>
<!--牵线-->
	<?php 
    $qianxian_neednum=$qianxian_num[$grade];
    $qianxian_num_ed = $db->COUNT(__TBL_QIANXIAN__,"flag=2 AND senduid=".$uid);
    
    ?>
    <table class="table W100_">
        <tr>
            <td height="20" class="tbodyT"><div class="tiaose">牵线记录<?php if ($crm_ugrade>0){?><em>【<?php echo crm_ugrade_title($crm_ugrade);?>】需成功牵线<b><?php echo $qxneednum;?></b>次，当前<b><?php echo $qxnum1;?></b>次</em><?php }?>
            <button type="button" class="btn size2" onClick="zeai.openurl('crm_select.php?k=qx&t=3&uid=<?php echo $uid;?>')"><i class="ico addico">&#xe620;</i>开始牵线</button></div>
            </td>
        </tr>
        <tr>
            <td height="200" valign="top">
            
            
            
    <?php       
	$rt = $db->query("SELECT a.*,b.sex,b.grade,b.nickname,b.weixin,b.mob,b.photo_s FROM ".__TBL_QIANXIAN__." a,".__TBL_USER__." b WHERE a.senduid=b.id AND a.senduid=".$uid);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<br><br><div class='nodataicoS'><i></i>暂无牵线";
		echo "<br><br><a class='aQINGed' onclick=\"zeai.openurl('crm_select.php?k=qx&t=3&uid=".$uid."')\">新增</a>";
		echo "</div><br><br>";
	} else {
	?>
    <table class="list">
    <tr>
    <th width="60" align="center">&nbsp;</th>
    <th width="100" align="left">牵线用户</th>
    <th width="130" align="center">牵线时间</th>
    <th width="10" align="center">&nbsp;</th>
    <th width="50" align="left">&nbsp;</th>
    <th width="100" align="left">被牵用户</th>
    <th>备注</th>
    <th width="80" align="center">红娘</th>
    <th width="70" align="center">状态</th>
    <th width="80" align="center">发起牵线</th>
    </tr>
    <?php
	for($i=1;$i<=$total;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $rows['uid'];
		$username = dataIO($rows['username'],'out');
		$sendnickname = dataIO($rows['nickname'],'out');
		$sex     = $rows['sex'];
		$grade   = $rows['grade'];
		$bz      = $rows['bz'];
		$flag    = $rows['flag'];
		$senduid = $rows['senduid'];
		$sendkind = $rows['sendkind'];
		$addtime = YmdHis($rows['addtime'],'YmdHi');
		$photo_s = $rows['photo_s'];
		$mob = dataIO($rows['mob'],'out');
		$weixin = dataIO($rows['weixin'],'out');
		$bz = dataIO($rows['bz'],'out');
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s40">';
		}else{
			$photo_s_url = HOST.'/res/photo_s'.$sex.'.png';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s40">';
		}
		//
		$row2 = $db->ROW(__TBL_USER__,"sex,grade,nickname,photo_s,weixin,mob","id=".$uid,"name");
		if ($row2){
			$sex2      = $row2['sex'];
			$grade2    = $row2['grade'];
			$photo_s2  = $row2['photo_s'];
			$nickname2 = dataIO($row2['nickname'],'out');
			$mob2      = dataIO($row2['mob'],'out');
			$weixin2   = dataIO($row2['weixin'],'out');
			if(!empty($photo_s2)){
				$photo_s_url2 = $_ZEAI['up2'].'/'.$photo_s2;
				$photo_s_str2 = '<img src="'.$photo_s_url2.'" class="photo_s40">';
			}else{
				$photo_s_url2 = HOST.'/res/photo_s'.$sex2.'.png';
				$photo_s_str2 = '<img src="'.$photo_s_url2.'" class="photo_s40">';
			}
		}
	?>
    <tr>
      <td width="60" height="40" align="center"><a href="<?php echo Href('crm_u',$senduid);?>&iframenav=1"><?php echo $photo_s_str; ?></a></td>
      <td width="100" align="left" class="lineH150 C999">
        <a href="<?php echo Href('crm_u',$senduid);?>&iframenav=1">
          <?php echo uicon($sex.$grade) ?><?php echo '<font class="S12 picmiddle">'.$senduid.'</font></br>';?></a>
        <?php echo $sendnickname;?>
      </td>
      <td width="130" height="60" align="center" class="lineH150 C666">
      <h5><?php echo $addtime;?></h5>
      <i class="ico S24 Cccc">&#xe62d;</i>
      </td>
      <td width="10" height="60" align="center" class="lineH200 C666">&nbsp;</td>
      <td width="50" align="left"><a href="<?php echo Href('crm_u',$uid);?>&iframenav=1"><?php echo $photo_s_str2; ?></a></td>
    <td width="100" align="left" class="lineH150">
    
    <a href="<?php echo Href('crm_u',$uid);?>&iframenav=1">
    <?php echo uicon($sex2.$grade2) ?><?php echo '<font class="S12 picmiddle">'.$uid.'</font></br>';?></a>
    <font class="uleft">
    <?php
      if(!empty($nickname2))echo $nickname2."</br>";
      ?>
    </font>
    
    </td>
    <td><?php echo $bz;?></td>
    <td width="80" align="center"><?php echo $username;?></td>
    <td width="70" align="center"><?php echo crm_qxflag_title($flag);?></td>
    <td width="80" align="center"><?php echo ($sendkind == 1)?'客户要求':'红娘主动';?></td>
    </tr>
	<?php } ?>
</table>

<?php }?>

            </td>
        </tr>
    </table>

<!--牵线结束-->
</td></tr></table>


<?php }?>
<div class="clear"></div>
<br><br>
<?php
require_once 'bottomadm.php';?>
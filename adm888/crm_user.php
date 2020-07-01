<?php
ob_start();
require_once '../sub/init.php';
header("Cache-control: private");
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('crm_user',$QXARR))exit(noauth('暂无【客户管理】权限'));
if(!in_array('crm',$QXARR)){
	if(  !strstr($session_crmkind,'adm') && !strstr($session_crmkind,'sq') && !strstr($session_crmkind,'sh')  ){
		exit(noauth('只有角色类型【管理员】【售前】【售后】才可以进入'));
	}
}
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_crm.php';
require_once ZEAI.'cache/udata.php';
if($submitok=="crm_ubz_update"){
	if (!ifint($uid))exit('客户不存在或已被删除');
	$crm_ubz = dataIO(TrimEnter($crm_ubz),'in');
	//
	$rt=$db->query("SELECT crm_ubz,nickname FROM ".__TBL_USER__." WHERE id=".$uid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt,"num");
		$oldbz    = $row[0];
		$nickname = $row[1];
	}else{
		callmsg("您要备注的客户不存在！",-1);exit;
	}
	$logstr='【CRM】修改客户备注【'.$nickname.'（uid:'.$uid.'）】原备注：'.$oldbz.' -> 新备注：'.$crm_ubz;
	AddLog($logstr);
	$db->query("UPDATE ".__TBL_USER__." SET crm_ubz='$crm_ubz' WHERE id=".$uid);
	$endbz = $crm_ubz;
	$returnid = 'bz'.$uid;
	?>
    <script>
		parent.document.getElementById('<?php echo $returnid; ?>').innerHTML = '<?php echo $endbz; ?>';
		parent.document.getElementById('<?php echo $returnid; ?>').style.color='#f00';
		parent.zeai.iframe(0);
	</script>
	<?php
	exit;
}elseif($submitok=="usre_crm_ukind_mod_update"){//客户分类
	if (!ifint($uid))exit('客户不存在');
	$crm_ukind = intval($crm_ukind);
	$db->query("UPDATE ".__TBL_USER__." SET crm_ukind=$crm_ukind WHERE id=".$uid);
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	AddLog('【CRM】修改客户【'.$nickname.'（uid:'.$uid.'）】客户分类');
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
	
}elseif($submitok=="usre_kind_mod_update"){	//线上类型
	if (!ifint($uid))exit('会员不存在或已被删除');
	$kind=intval($kind);
	$db->query("UPDATE ".__TBL_USER__." SET kind=$kind WHERE id=".$uid);
	$title = dataIO($title,'out');
	switch ($kind) {
		case 1:$kindstyle = " class='aLAN'";break;
		case 2:$kindstyle = " class='aHEI'";break;
		case 3:$kindstyle = " class='aQING'";break;
	}
	?>
	<script>
    window.parent.document.getElementById('ukind<?php echo $uid; ?>').innerHTML = "<a href=\"javascript:;\" <?php echo $kindstyle;?> onClick=\"zeai.iframe('修改【<?php echo $title;?>】会员类型','crm_user.php?submitok=usre_kind_mod&uid=<?php echo $uid;?>',350,380)\"><?php echo user_kind($kind); ?></a>";
    window.parent.zeai.iframe(0);
    </script>    
    <?php
	exit;
}elseif($submitok=="crm_ugrade_mod_update"){//客户等级
	if (!ifint($uid))exit('客户不存在');
	$crm_ugrade   = intval($crm_ugrade);
	if($crm_ugrade>0){
		if(!ifdatetime($crm_usjtime1))json_exit(array('flag'=>0,'msg'=>'【服务起始时间】不合法'));
		if(!ifdatetime($crm_usjtime2))json_exit(array('flag'=>0,'msg'=>'【服务结束时间】不合法'));
		$crm_usjtime1 = strtotime($crm_usjtime1);
		$crm_usjtime2 = strtotime($crm_usjtime2);
	}else{
		$crm_usjtime1 = 0;
		$crm_usjtime2 = 0;
	}
	$crm_qxnum = intval($crm_qxnum);
	$crm_yjnum = intval($crm_yjnum);
	$crm_flag  = intval($crm_flag);
	$crm_flag  = ($crm_flag==1 || $crm_flag==2 || $crm_flag==3)?$crm_flag:1;
	$db->query("UPDATE ".__TBL_USER__." SET crm_flag=$crm_flag,crm_ugrade=$crm_ugrade,crm_usjtime1='$crm_usjtime1',crm_usjtime2='$crm_usjtime2',crm_qxnum='$crm_qxnum',crm_yjnum='$crm_yjnum' WHERE id=".$uid);
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	AddLog('【CRM】修改客户【'.$nickname.'（uid:'.$uid.'）】客户等级信息');
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}

$SQL = " (flag=1 OR flag=-2) AND kind<>4 AND admid>0 ";

//非超管门店+地区 
$SQL .= getAgentSQL();

//超管可以按门店搜索
if(ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND agentid=$agentid";

//我的
if($ifmy==1)$SQL .= " AND (U.admid=".$session_uid." OR U.hnid=".$session_uid." OR U.hnid2=".$session_uid.") ";

//我的admid
if($ifmy_admid==1)$SQL .= " AND U.admid=".$session_uid;



//按客户
$Skey = trimhtml($Skey);
if (!empty($Skey)){
	if(ifmob($Skey)){
		$SQL .= " AND mob=".$Skey;	
	}elseif(ifint($Skey)){
		$SQL .= " AND id=".$Skey;	
	}else{
		$SQL .= " AND (( truename LIKE '%".$Skey."%' ) OR ( crm_ubz LIKE '%".$Skey."%' ) OR ( uname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".urlencode($Skey)."%' )) ";
	}
}
//按红娘
$Skey2 = trimhtml($Skey2);
if (!empty($Skey2)){
	if(ifint($Skey2)){
		$SQL .= " AND (  admid=".$Skey2." OR hnid=".$Skey2." OR hnid2=".$Skey2." )";	
	}else{
		$SQL .= " AND (   ( admname LIKE '%".$Skey2."%' ) OR ( hnname LIKE '%".$Skey2."%' )  OR ( hnname2 LIKE '%".$Skey2."%' )     ) ";
	}
}
//按客户分类
if($crm_ugrade=='sq'){
	$SQL .= " AND crm_ugrade=0 ";
}else{
	if(ifint($crm_ukind))$SQL .= " AND crm_ukind=$crm_ukind";
}

//按客户等级
if(ifint($crm_ugrade))$SQL .= " AND crm_ugrade=$crm_ugrade";

if(ifint($if3))$SQL .= " AND crm_flag=3";

if(ifint($g) || $g==-1){
	switch ($g) {
		case 3:$SQL  .= " AND (crm_usjtime2 - ".ADDTIME.") < 259200 ";break;
		case 7:$SQL  .= " AND (crm_usjtime2 - ".ADDTIME.") < 604800 ";break;
		case 30:$SQL .= " AND (crm_usjtime2 - ".ADDTIME.") < 2592000 ";break;
		case -1:$SQL .= " AND crm_usjtime2 < ".ADDTIME." ";break;
	}
	$SQL .= "AND crm_ugrade>0 AND crm_usjtime2>0";
}

switch ($kind) {
	case 1:$SQL .= " AND kind=1 ";break;
	case 2:$SQL .= " AND kind=2 ";break;
	case 3:$SQL .= " AND kind=3 ";break;
}

if ($ifmob==1)$SQL       .= " AND mob<>'' ";
if ($ifdata==1)$SQL      .= " AND myinfobfb>10 ";
if ($ifcrm_ubz==1)$SQL   .= " AND crm_ubz<>'' ";

switch ($sort) {
	case 'regtime0':$sortSQL = " ORDER BY regtime ";break;
	case 'regtime1':$sortSQL = " ORDER BY regtime DESC ";break;
	case 'endtime0':$sortSQL = " ORDER BY endtime ";break;
	case 'endtime1':$sortSQL = " ORDER BY endtime DESC ";break;
	case 'admtime0':$sortSQL = " ORDER BY admtime,id DESC";break;
	case 'admtime1':$sortSQL = " ORDER BY admtime DESC,id DESC";break;
	case 'bbsnum0':$sortSQL = " ORDER BY bbsnum,id DESC";break;
	case 'bbsnum1':$sortSQL = " ORDER BY bbsnum DESC,id DESC ";break;
	case 'qxnum0':$sortSQL = " ORDER BY qxnum,id DESC";break;
	case 'qxnum1':$sortSQL = " ORDER BY qxnum DESC,id DESC ";break;
	case 'matchnum0':$sortSQL = " ORDER BY matchnum,id DESC";break;
	case 'matchnum1':$sortSQL = " ORDER BY matchnum DESC,id DESC ";break;
	case 'hntime0':$sortSQL = " ORDER BY hntime,id DESC";break;
	case 'hntime1':$sortSQL = " ORDER BY hntime DESC,id DESC";break;
	default:$sortSQL = " ORDER BY id DESC ";break;
}
$hnname = urldecode($hnname);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.flag0_str{display:block;line-height:20px;color:#f70;font-size:12px}
img.crm_flag3{width:70px;margin-bottom:5px}
</style>
<body>
<!--客户分类修改-->
<?php
if($submitok == 'usre_crm_ukind_mod'){
	if(!in_array('crm_ukind',$QXARR))exit(noauth());
	if (!ifint($uid))alert_adm("参数错误","-1");
	$rt = $db->query("SELECT crm_ukind FROM ".__TBL_USER__." WHERE id=".$uid);
	if($db->num_rows($rt)){
		$row  = $db->fetch_array($rt,'name');
		$crm_ukind = $row['crm_ukind'];
	}else{
		alert_adm("客户不存在！","-1");
	}
	?>
	<style>
    .RCW li{width:30%;height:20px;line-height:20px}
    </style>
    <form action="<?php echo SELF;?>" name="ZEAIFORM" id="ZEAIFORM" method="post">
    <table class="table W90_ Mtop30">
    <tr>
    <td class="tdL">客户分类</td>
    <td class="tdR"><script>zeai_cn__CreateFormItem('radio','crm_ukind','<?php echo $crm_ukind; ?>','class="size2 RCW"',crm_ukind_ARR);</script></td>
    </tr>
    </table>
    <input type="hidden" name="submitok" value="usre_crm_ukind_mod_update" />
    <input type="hidden" name="uid" value="<?php echo $uid;?>" />
    </form>
    <br><br><br><br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">修改</button></div>
	<script>
    save.onclick=function(){
        zeai.ajax({url:'crm_user'+zeai.extname,form:ZEAIFORM},function(e){rs=zeai.jsoneval(e);
            window.parent.zeai.msg(0);window.parent.zeai.msg(rs.msg);
            if(rs.flag==1){setTimeout(function(){window.parent.location.reload(true);},1000);}
        });
    }
    </script>
<!--客户等级修改-->
<?php exit;}
if($submitok == 'crm_ugrade_mod'){
	if(!in_array('crm_user_grade',$QXARR))exit(noauth());
	if (!ifint($uid))alert_adm("参数错误","-1");
	$rt = $db->query("SELECT crm_ugrade,crm_usjtime1,crm_usjtime2,crm_qxnum,crm_yjnum,crm_flag FROM ".__TBL_USER__." WHERE id=".$uid);
	if($db->num_rows($rt)){
		$row  = $db->fetch_array($rt,'name');
		$crm_ugrade   = $row['crm_ugrade'];
		$crm_usjtime1 = intval($row['crm_usjtime1']);
		$crm_usjtime1 = (!empty($crm_usjtime1))?YmdHis($crm_usjtime1):YmdHis(ADDTIME);
		$crm_usjtime2 = intval($row['crm_usjtime2']);
		$crm_usjtime2 = (!empty($crm_usjtime2))?YmdHis($crm_usjtime2):'';
		$crm_qxnum = intval($row['crm_qxnum']);
		$crm_yjnum = intval($row['crm_yjnum']);
		$crm_flag  = intval($row['crm_flag']);
	}else{alert_adm("客户不存在！","-1");}?>
	<style>
	.RCW li{width:100%;height:24px;line-height:24px}
    .tdL{width:20%}
    .tdR{width:30%}
    </style>
    <form action="<?php echo SELF;?>" name="ZEAIFORM" id="ZEAIFORM" method="post">
    <table class="table W90_ Mtop20">
    <tr><td rowspan="3" class="tdL"><font class="Cf00">*</font>客户等级</td><td rowspan="3" class="tdR">
    <ul class="size2 RCW"><li><input type="radio" class="radioskin" id="crm_ugrade0" name="crm_ugrade" value="0" onclick="zeai.msg('选此项【客户等级】将归为初始，服务时间也将清零',{time:4});"><label for="crm_ugrade0" class="radioskin-label"><i class="i1"></i><b class="">--</b></label></li></ul>
	<script>zeai_cn__CreateFormItem('radio','crm_ugrade','<?php echo $crm_ugrade; ?>','class="size2 RCW"',crm_ugrade_ARR);</script></td>
      <td class="tdL"><font class="Cf00">*</font>牵线次数</td>
      <td class="tdR"><input type="text" maxlength="19" class="input size2 W80" name="crm_qxnum" id="crm_qxnum" value="<?php echo $crm_qxnum; ?>" /></td>
    </tr>
    <tr>
      <td class="tdL"><font class="Cf00">*</font>约见次数</td>
      <td class="tdR"><input type="text" maxlength="19" class="input size2 W80" name="crm_yjnum" id="crm_yjnum" value="<?php echo $crm_yjnum; ?>" /></td>
    </tr>
    <tr>
      <td class="tdL"><font class="Cf00">*</font>服务状态</td>
      <td class="tdR"><div id="crm_flagbox"><script>zeai_cn__CreateFormItem('radio','crm_flag','<?php echo $crm_flag; ?>',' class="size2 RCW"',<?php echo $_CRM['crm_flag'];?>);//onclick="meet_flag(this);"</script></div></td>
    </tr>
    <tr><td class="tdL"><font class="Cf00">*</font>服务起始时间</td><td colspan="3" class="tdR"><input type="text" maxlength="19" class="input size2 W200" name="crm_usjtime1" id="crm_usjtime1" value="<?php echo $crm_usjtime1; ?>" /></td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font>服务结束时间</td><td colspan="3" class="tdR"><input type="text" maxlength="19" class="input size2 W200" name="crm_usjtime2" id="crm_usjtime2" value="<?php echo $crm_usjtime2; ?>" /></td></tr>
    
    </table>
    <input type="hidden" name="submitok" value="crm_ugrade_mod_update" />
    <input type="hidden" name="uid" value="<?php echo $uid;?>" />
    </form>
    <br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">修改</button></div>
	<script>
	zeai.listEach(zeai.tag(crm_flagbox,'input'),function(obj){
		if(obj.value==3)obj.onclick=function(){zeai.msg('<div style="line-height:150%;padding:20px 0">选择【服务成功】，表示此客户已经【服务结束】<br>售后会员列表将隐藏显示，将不再提示跟进等提醒</div>',{time:5});}
	});
    save.onclick=function(){
        if (!zeai.form.ifradio('crm_flag')){
            zeai.msg('请选择【服务状态】',crm_flag1);
            return false;
        }
        zeai.confirm('<b class="S18">确定修改么？</b><br>此修改属于硬改，不触发合同数据，请慎重！',function(){
			zeai.ajax({url:'crm_user'+zeai.extname,form:ZEAIFORM},function(e){rs=zeai.jsoneval(e);
				window.parent.zeai.msg(0);window.parent.zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){window.parent.location.reload(true);},1000);}
			});
		});
    }
    </script>
<?php exit;}?>

<!--客户线上类型修改-->
<?php
if($submitok == 'usre_kind_mod'){
	if(!in_array('crm_user_kind',$QXARR))exit(noauth());
	if (!ifint($uid))alert_adm("参数错误","-1");
	$rt = $db->query("SELECT kind,nickname,truename FROM ".__TBL_USER__." WHERE id=".$uid);
	if($db->num_rows($rt)){
		$row  = $db->fetch_array($rt,'name');
		$kind = $row['kind'];
		if(empty($row['nickname'])){
			if(empty($row['truename'])){
				$title = 'uid:'.$uid;
			}else{
				$title = dataIO($row['truename'],'out');
			}
		}
		$title=trimhtml($title);
	}else{
		alert_adm("客户不存在！","-1");
	}
	?>
    <form name="ZEAI_FORM" method="POST" action="<?php echo SELF;?>">
    <table class="table Mtop20" style="width:95%">
    <tr>
    <td align="center" class="C8d" style="padding:35px 0"">
        <input type="radio" name="kind" id="kind1" class="radioskin" value="1"  <?php if($kind==1){echo'checked="checked"';}?>><label for="kind1" class="radioskin-label"><i></i><b class="W50 S14" style="color:#1E7ABA">线上</b></label>
        <input type="radio" name="kind" id="kind2" class="radioskin" value="2"  <?php if($kind==2){echo'checked="checked"';}?>><label for="kind2" class="radioskin-label"><i></i><b class="W50 S14" style="color:#000">线下</b></label>
        <input type="radio" name="kind" id="kind3" class="radioskin" value="3"  <?php if($kind==3){echo'checked="checked"';}?>><label for="kind3" class="radioskin-label"><i></i><b class="W50 S14" style="color:#038A7D">均可</b></label>
        <input type="hidden" name="submitok" value="usre_kind_mod_update" />
        <input type="hidden" name="uid" value="<?php echo $uid;?>" />
        <input type="hidden" name="title" value="<?php echo $title;?>" />
    </td>
    </tr>
    <tr>
      <td align="left" class="S12 C8d lineH200" style=";padding:15px"">设为【线上】，在前端可以自主联系或被联系<br>设为【线下】其他客户员联系此人只能通过认领红娘<br>设为【均可】，相当于就是线上，在某前特殊场景会用到（如客户自主选择红娘）</td>
    </tr>
    </table>
    <br><br><br><br><br><br><div class="savebtnbox"><button type="submit"  class="btn size3 HUANG3">修改</button></div>
</form>



<!--crm备注-->
<?php exit;}elseif($submitok == 'bz'){
	if(!in_array('crm_user_bz',$QXARR))exit(noauth());
	
	if (!ifint($uid))alert_adm("参数错误","-1");
	$rt = $db->query("SELECT crm_ubz FROM ".__TBL_USER__." WHERE id=".$uid);
	if($db->num_rows($rt)){
		$row  = $db->fetch_array($rt,'name');
		$crm_ubz = dataIO($row['crm_ubz'],'out');
	}else{
		alert_adm("客户不存在！","-1");
	}
	
	?>
	<script>
    function chkform(){
        if(zeai.str_len(o("crm_ubz").value)>500){
            zeai.msg('内容长度请控制在500字节以内',{mask:'off',focus:crm_ubz});
            return false;
        }
    }
    </script>
    <form name="GYLform" id="GYLform" method="POST" action="<?php echo SELF;?>" onsubmit="return chkform();">
    <table class="table0 W98_ Mtop20">
    <tr>
    <td class="center"><textarea name="crm_ubz" placeholder="500字以内" rows="8" class="textarea W90_ Mcenter" id="crm_ubz"><?php echo $crm_ubz;?></textarea></td>
    </tr>
    <tr>
    <td height="60" class="center"><input type="hidden" name="submitok" value="crm_ubz_update" />
    <input type="hidden" name="uid" value="<?php echo $uid;?>" />
    </td>
    </tr>
    </table>
    <div class="savebtnbox"><button type="submit" id="save" class="btn size3 HUANG3">确认并保存</button></div>
    </form>
<?php exit;}?>

<!-- TAB标签 -->
<?php
$MATCHSQL = ",(SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." WHERE uid=U.id) AS matchnum";
$BBSSQL   = ",(SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." WHERE uid=U.id) AS bbsnum";
$QXSQL    = ",(SELECT COUNT(*) FROM ".__TBL_QIANXIAN__." WHERE senduid=U.id) AS qxnum";
$rt = $db->query("SELECT id,agenttitle,kind,agentid,admid,admtime,admname,hnid,hnname,hnid2,hnname2,nickname,if2,sjtime,truename,photo_s,photo_f,mob,sex,grade,flag,areatitle,crm_ubz,myinfobfb,crm_ugrade,crm_ukind,crm_usjtime1,crm_usjtime2,regtime,endtime,flag,crm_flag".$MATCHSQL.$BBSSQL.$QXSQL." FROM ".__TBL_USER__." U WHERE ".$SQL.$sortSQL." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
$navboxA="crm_user.php?agentid=$agentid&ifmy=$ifmy";
?>
<div class="navbox" style="min-width:1300px">
    <a href="<?php echo $navboxA;?>"<?php echo (empty($kind)  )?' class="ed"':'';?>>客户列表<?php if(empty($kind))echo '<b>'.$total.'</b>';?></a>
	<?php if($session_crmkind == 'adm' || in_array('crm',$QXARR)  ){?>
        <a href="<?php echo $navboxA;?>&kind=1"<?php echo ($kind==1)?' class="ed"':'';?>>线上<?php if($kind==1)echo '<b>'.$total.'</b>';?></a>
        <a href="<?php echo $navboxA;?>&kind=2"<?php echo ($kind==2)?' class="ed"':'';?>>线下<?php if($kind==2)echo '<b>'.$total.'</b>';?></a>
        <a href="<?php echo $navboxA;?>&kind=3"<?php echo ($kind==3)?' class="ed"':'';?>>均可<?php if($kind==3)echo '<b>'.$total.'</b>';?></a>
    <?php }?>
<div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank60"></div>


<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
  <table class="tablelist">
    <tr>
    <td colspan="14" class="searchform">
    
        <form name="form3" method="get" action="<?php echo SELF; ?>">
        <!--按门店查询-->
        <?php if(in_array('crm',$QXARR)){?>
            <?php
            $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {
                ?>
                <select name="agentid" class="size2 picmiddle">
                <option value="">全部门店</option>
                <?php
                    for($j=0;$j<$total2;$j++) {
                        $rows2 = $db->fetch_array($rt2,'num');
                        if(!$rows2) break;
                        $clss=($agentid==$rows2[0])?' selected':'';?>
                        <option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[1],'out');?></option><?php
                    }
                ?>
                </select>　
                <?php
            }
        }
        ?>
        <input name="Skey" type="text" id="Skey" class="W200 input size2" placeholder="按客户UID/姓名/昵称/手机/备注" value="<?php echo $Skey; ?>">　
        <input name="Skey2" type="text" id="Skey2" size="30" maxlength="25" class="W100 input size2" placeholder="按红娘ID/姓名" value="<?php echo $Skey2; ?>">　
        <script>nulltext='客户等级过期时间';zeai_cn__CreateFormItem('select','g','<?php echo $g; ?>','class="size2 picmiddle"',[{i:"3",v:"3天内到期"},{i:"7",v:"7天内到期"},{i:"30",v:"30天内到期"},{i:"-1",v:"已过期"}]);</script>　
        
        <input type="checkbox" name="ifmob" id="ifmob" class="checkskin" value="1"<?php echo ($ifmob == 1)?' checked':''; ?> ><label for="ifmob" class="checkskin-label"><i></i><b class="W50 S14">有手机</b></label>
        <input type="checkbox" name="ifdata" id="ifdata" class="checkskin" value="1"<?php echo ($ifdata == 1)?' checked':''; ?> ><label for="ifdata" class="checkskin-label"><i></i><b class="W80 S14">资料>10%</b></label>
        <input type="checkbox" name="ifcrm_ubz" id="ifcrm_ubz" class="checkskin" value="1"<?php echo ($ifcrm_ubz == 1)?' checked':''; ?>><label for="ifcrm_ubz" class="checkskin-label"><i></i><b class="W50 S14">有备注</b></label>
        <input type="checkbox" name="ifmy" id="ifmy" class="checkskin" value="1"<?php echo ($ifmy == 1)?' checked':''; ?>><label for="ifmy" class="checkskin-label"><i></i><b class="W80 S14">我的客户</b></label>
        <input type="checkbox" name="if3" id="if3" class="checkskin" value="1"<?php echo ($if3 == 1)?' checked':''; ?>><label for="if3" class="checkskin-label"><i></i><b class="W80 S14">服务成功</b></label>
        
        <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="kind" value="<?php echo $kind;?>" />
        <input type="hidden" name="hnu" value="<?php echo $hnu;?>" />
        <input type="hidden" name="myinfobfb" value="<?php echo $myinfobfb;?>" />
        <input type="hidden" name="crm_ukind" value="<?php echo $crm_ukind;?>" />
        <input type="hidden" name="crm_ugrade" value="<?php echo $crm_ugrade;?>" />
        <input type="hidden" name="t" value="<?php echo $t;?>" />
        </form>
    </td>
    </tr>
    <tr>
      <td colspan="14" align="left" class="searchli">
      <?php
		$searchA = "crm_user.php?if3=$if3&ifmy=$ifmy&ifcrm_ubz=$ifcrm_ubz&ifdata=$ifdata&ifmob=$ifmob&myinfobfb=$myinfobfb&kind=$kind&g=$g&agentid=$agentid&hnu=$hnu&Skey=$Skey&Skey2=$Skey2";
		$sortA = $searchA."&crm_ukind=$crm_ukind&crm_ugrade=$crm_ugrade&sort=";
	  ?>
      <dl>
      	
        <dd>
        <a href="javascript:;" <?php echo (empty($crm_ukind))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&crm_ukind=&crm_ugrade=<?php echo $crm_ugrade;?>&sort=<?php echo $sort;?>')">全部客户分类</a>
        <?php
			$crm_ukindARR = json_decode($_UDATA['crm_ukind'],true);
			if (count($crm_ukindARR) >= 1 && is_array($crm_ukindARR)){
				foreach ($crm_ukindARR as $V){
					$ukind_id    = $V['i'];
					$ukind_title = $V['v'];
					$ukindcls = ($ukind_id==$crm_ukind)?' class="ed"':'';
					?>
                    <a href="javascript:;" <?php echo $ukindcls;?> onClick="zeai.openurl('<?php echo $searchA;?>&crm_ukind=<?php echo $ukind_id;?>&crm_ugrade=<?php echo $crm_ugrade;?>&sort=<?php echo $sort;?>')"><?php echo $ukind_title;?></a>
					<?php
                }
			}
			?>
        </dd>
      </dl>
      <dl>
      	
        <dd>
        <a href="javascript:;" <?php echo (empty($crm_ugrade))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&crm_ukind=<?php echo $crm_ukind;?>&crm_ugrade=&sort=<?php echo $sort;?>')">全部客户等级</a>
        <a href="javascript:;" <?php echo ($crm_ugrade=='sq')?' class="ed tips"':' class="tips"'?> onClick="zeai.openurl('<?php echo $searchA;?>&crm_ukind=<?php echo $crm_ukind;?>&crm_ugrade=sq&sort=<?php echo $sort;?>')" tips-title='没有升级线下VIP等级星级的客户（例如：未缴纳线下服务费）' tips-direction='top'><b>售前未签约客户</b></a>
        <?php
			$crm_ugradeARR = json_decode($_UDATA['crm_ugrade'],true);
			if (count($crm_ugradeARR) >= 1 && is_array($crm_ugradeARR)){
				foreach ($crm_ugradeARR as $V){
					$ugrade_id    = $V['i'];
					$ugrade_title = $V['v'];
					$ugradecls = ($ugrade_id==$crm_ugrade)?' class="ed"':'';
					?>
                    <a href="javascript:;" <?php echo $ugradecls;?> onClick="zeai.openurl('<?php echo $searchA;?>&crm_ukind=<?php echo $crm_ukind;?>&crm_ugrade=<?php echo $ugrade_id;?>&sort=<?php echo $sort;?>')">售后：<?php echo $ugrade_title;?></a>
					<?php
                }
			}
			?>
        </dd>
      </dl>
      </td>
    </tr>
    
    
    
    <form id="zeaiFORM" method="get" action="<?php echo SELF; ?>">
    <tr>
    <th width="30"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60">头像</th>
    <th width="130" align="left">UID/姓名/昵称/手机</th>
    <th width="100" align="center">地区</th>
    <th width="10" align="center">&nbsp;</th>
    <th width="180" align="left">所属门店/服务红娘</th>
    <th width="120" align="center">客户等级</th>
    <th width="80" align="center">跟进(次)<div class="sort">
            <a title="升序" href="<?php echo $sortA."bbsnum0";?>" <?php echo($sort == 'bbsnum0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sortA."bbsnum1";?>" <?php echo($sort == 'bbsnum1')?' class="ed"':''; ?>></a>
            </div>
     </th>
    <th width="80" align="center" title="只统计主动牵线次数">牵线(次)<div class="sort">
            <a title="升序" href="<?php echo $sortA."qxnum0";?>" <?php echo($sort == 'qxnum0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sortA."qxnum1";?>" <?php echo($sort == 'qxnum1')?' class="ed"':''; ?>></a>
            </div>
    </th>
    <th width="80" align="center" title="只统计主动约见次数">约见(次)<div class="sort">
            <a title="升序" href="<?php echo $sortA."matchnum0";?>" <?php echo($sort == 'matchnum0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sortA."matchnum1";?>" <?php echo($sort == 'matchnum1')?' class="ed"':''; ?>></a>
            </div>
    </th>
    <th width="70" align="center">是否线上</th>
    <th align="center">客户分类/备注</th>
    <th width="130" align="left">帐号信息</th>
    <th width="60" align="center">资料</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid=$id;
		//$pwd = $rows['pwd'];
		//$uname = strip_tags($rows['uname']);
		$truename = strip_tags($rows['truename']);
			$uname = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$uname);
			$truename = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$truename);
			$nickname = dataIO($rows['nickname'],'out');
			$nickname = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$nickname);
		$mob = trimhtml($rows['mob']);
		$mob = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$mob);
		$photo_s   = $rows['photo_s'];
		$photo_f   = $rows['photo_f'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$flag      = $rows['flag'];
		$areatitle = $rows['areatitle'];
		$myinfobfb = $rows['myinfobfb'];
		$crm_ubz = dataIO($rows['crm_ubz'],'out');
		$crm_ugrade = intval($rows['crm_ugrade']);
		$crm_ukind = intval($rows['crm_ukind']);
		$crm_usjtime1 = intval($rows['crm_usjtime1']);
		$crm_usjtime2 = intval($rows['crm_usjtime2']);
		
		$regtime = $rows['regtime'];
		$endtime = $rows['endtime'];
		$flag = $rows['flag'];
		
		$kind      = $rows['kind'];
		$agentid   = intval($rows['agentid']);
		$admid     = intval($rows['admid']);
		$hnid      = intval($rows['hnid']);
		$hnid2     = intval($rows['hnid2']);
		$admtime   = intval($rows['admtime']);
		$admname   = dataIO($rows['admname'],'out');
		$hnname    = dataIO($rows['hnname'],'out');
		$hnname2   = dataIO($rows['hnname2'],'out');
		$agenttitle= dataIO($rows['agenttitle'],'out');
		$if2    = $rows['if2'];
		$sjtime = $rows['sjtime'];
		$matchnum = $rows['matchnum'];
		$bbsnum   = $rows['bbsnum'];
		$qxnum    = $rows['qxnum'];
		if(empty($rows['nickname'])){
			if(empty($rows['truename'])){
				$title = $rows['mob'];
			}else{
				$title = $rows['truename'];
			}
			if(empty($title))$title=$uname;
		}else{
			$title = $nickname;
		}
		$title=trimhtml($title);
		switch ($kind) {
			case 1:$kindstyle   = " class='aLAN'";break;
			case 2:$kindstyle   = " class='aHEI'";break;
			case 3:$kindstyle   = " class='aQING'";break;
		}
		//
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$agenttitle = dataIO($rows['agenttitle'],'out');
		$agenttitle = (!empty($agenttitle))?'<font class="C999">门店：</font>'.$agenttitle.'<br>':'';
		$crm_flag     = intval($rows['crm_flag']);
		$crm_flag_str = ($crm_flag==3)?'<img src="images/crm_flag3.png" class="crm_flag3">':'';
		$title2 = (!empty($nickname))?urlencode(trimhtml($nickname)).'／'.$uid:$uid;
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="30"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
        </td>
      <td width="60">
      
      <a href="javascript:;" class="photo_ss" uid="<?php echo $id;?>" title2="<?php echo $title2;?>"><img src="<?php echo $photo_s_url; ?>" class="photo_s"></a>
      
        <?php if ($flag == 0){?><span class="flag0_str">录入未审</span><?php }?>
        </td>
        <td width="130" align="left" class="lineH150" style="padding:10px 0">
        <a href="javascript:;" class="photo_ss" uid="<?php echo $id;?>" title2="<?php echo$title2;?>"><?php echo uicon($sex.$grade).'<span style="vertical-align:middle">'.$id.'</span>';?></a><br>
        <?php
		
		echo '<font class="uleft">';
		if(!empty($truename))echo $truename."</br>";
		if(!empty($nickname))echo $nickname;
		if(crm_ifcontact($agentid,$admid,$hnid,$hnid2)){
			if(!empty($mob))echo "</br>".$mob;
		}
		echo '</font>';
		?>
        
        </td>
      <td width="100" align="center" class="C999 lineH200"><?php echo str_replace(" ","<br>",$areatitle);;?></td>
      <td width="10" align="center">
        
  
        
      </td>
      <td width="180" class="lineH200 padding15">
      	<?php echo $agenttitle;?>
        <?php $adm_str = ($admtime>0)?'认领':'录入';?>
        <?php if(!empty($admname)){echo '<font class="C999">'.$adm_str.'：</font>'.$admname.' <font class="C999">ID:'.$admid.'</font>';}?><br>
        <?php if(!empty($hnname)){echo '<font class="C999">售前：</font>'.$hnname.' <font class="C999">ID:'.$hnid.'</font>';}?><br>
        <?php if(!empty($hnname2)){echo '<font class="C999">售后：</font>'.$hnname2.' <font class="C999">ID:'.$hnid2.'</font>';}?>
      </td>
      <td width="120" align="center" class="padding15" id="grade<?php echo $id;?>">
		<?php
		echo $crm_flag_str;
		echo crm_ugrade_time($uid,$crm_ugrade,'btn_djs',$crm_usjtime1,$crm_usjtime2);
		$rtHT=$db->query("SELECT id,htcode FROM ".__TBL_CRM_HT__." WHERE uid=".$uid." ORDER BY id DESC");
		$totalHT = $db->num_rows($rtHT);
		if ($totalHT > 0) {
			for($iHT=1;$iHT<=$totalHT;$iHT++) {
				$rowsHT = $db->fetch_array($rtHT,'name');
				if(!$rowsHT) break;
				$htid =$rowsHT['id'];
				$htcode=dataIO($rowsHT['htcode'],'out');?>
				<div><a href="javascript:;" title="查看合同详情" onClick="zeai.iframe('<?php echo '<img src='.$photo_s_url.' class=photo_s_iframe>';?>【<?php echo $uid;?>】合同详情','crm_gj_yj.php?submitok=ht_detail&fid=<?php echo $htid;?>',700,520)"><i class="ico S16 Caaa textmiddle">&#xe656;</i> <span class="textmiddle C666"><?php echo $htcode;?></span></a></div><?php
			}
		}
		?>
    </td>
    <?php
		$bbsnumCls=($bbsnum>0)?'aQING gj_list':'aHUI gj_list';
		$matchnumCls=($matchnum>0)?'aQING yj_list':'aHUI yj_list';
		$qxnumCls=($qxnum>0)?'aQING qx_list':'aHUI qx_list';
	?>
    <td width="80" align="center" class="center"><a href="javascript:;" class="<?php echo $bbsnumCls;?>" photo_s_url=<?php echo $photo_s_url;?> uid="<?php echo $id;?>" title2="<?php echo $title2;?>"><?php echo $bbsnum;?></a></td>
    <td width="80" align="center" class="center"><a href="javascript:;" class="<?php echo $qxnumCls;?>" photo_s_url=<?php echo $photo_s_url;?> uid="<?php echo $id;?>" title2="<?php echo $title2;?>"><?php echo $qxnum;?></a></td>
    <td width="80" align="center" class="center"><a href="javascript:;" class="<?php echo $matchnumCls;?>" photo_s_url=<?php echo $photo_s_url;?> uid="<?php echo $id;?>" title2="<?php echo $title2;?>"><?php echo $matchnum;?></a>
    </td>
    <td width="70" align="center" class="center C999" id="ukind<?php echo $id;?>">
      <a href="javascript:;" <?php echo $kindstyle;?> onClick="zeai.iframe('修改【<?php echo $id;?>】客户是否线上','crm_user.php?submitok=usre_kind_mod&uid=<?php echo $id;?>',500,380)"><?php echo user_kind($kind); ?></a>
    </td>
    <td align="center" class="center C999">
      
      <?php if ($crm_ukind>0){?>
      <a class="aHUI"  onClick="zeai.iframe('修改【<?php echo $id;?>】客户分类','crm_user.php?submitok=usre_crm_ukind_mod&uid=<?php echo $id;?>',700,320)"><?php echo udata('crm_ukind',$crm_ukind); ?></a>
      <?php }?>
      <br><br>
      <a title="点击查看/修改备注" href="javascript:;" onClick="zeai.iframe('给【<?php echo $id;?>】备注','crm_user.php?submitok=bz&uid=<?php echo $id;?>',500,280)" class="editico tips" tips-title='<?php echo $crm_ubz;?>'></a><span id="bz<?php echo $id;?>"><?php if (!empty($crm_ubz))echo '<font class="newdian"></font>';?></span> 
      
    </td>
    <td width="130" align="left" class="lineH200 padding10">
      <font class="C999">注册时间：</font><?php echo YmdHis($rows['regtime'],'Ymd');?><br>
      <font class="C999">最后登录：</font><?php echo YmdHis($rows['endtime'],'Ymd');?></font><br>
      <font class="C999">帐号状态：</font><?php echo flagtitle($rows['flag']);?></font>
    </td>
      <td width="60" align="center" class="center">
		<?php if(in_array('crm_user_mod',$QXARR)  ){//in_array('crm',$QXARR)?>
        
        	<?php if ($session_kind == 'adm' || strstr($session_crmkind,'adm') || ($session_crmkind == 'sq' && $session_uid==$hnid || $session_crmkind == 'sh' && $session_uid==$hnid2 )  ){?>
            	<span class="<?php if($myinfobfb >80){echo ' myinfobfb2 ';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>"><?php echo $myinfobfb;?>%</span><br>
                <a href="javascript:;" class="btn size2 tips editdata" tips-title='设置/修改客户资料' tips-direction='left' uid="<?php echo $id;?>" title2="<?php echo urlencode(trimhtml($nickname.' ｜ '.$id));?>" style="margin-top:5px">修改</a>                
            <?php }?>
            
            
        <?php }?>
      
      </td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="14">
    </td>
    </tr></tfoot>
    
    <div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" value="" class="btn size2 disabled action" onClick="sendTipFn(this);">发送消息</button>
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </div>
    
    
    </form>
</table>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<?php }?>
<br><br><br>
<script>
zeai.listEach('.photo_ss',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		urlpre = 'crm_user_detail.php?t=2&iframenav=1&uid='+uid;
		urlpre_qx = 'crm_select.php?k=qx&uid='+uid+'&t=';
		urlpre_yj = 'crm_select.php?k=yj&uid='+uid+'&t=';
		zeai.iframe('【'+decodeURIComponent(title2)+'】'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'\',this);" class="ed"><i class="ico add">&#xe7a0;</i> 个人主页</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre_qx+'3\',this);"><i class="ico add">&#xe64b;</i> 按择偶要求（牵线）</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre_yj+'3\',this);"><i class="ico add">&#xe64b;</i> 按择偶要求（约见）</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre_qx+'4\',this);"><i class="ico add">&#xe6c4;</i> 按搜索条件</a>'+
		'</div>',urlpre);
	}
});

zeai.listEach('.qx_list',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid"));
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】牵线管理','u_qianxian.php?uid='+uid);
	}
});
zeai.listEach('.gj_list',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">',
		urlpre = 'crm_gj_yj.php?uid='+uid+'&submitok=';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】跟进管理'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'gj_list\',this);" class="ed"><i class="ico add">&#xe64b;</i> 跟进列表</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'gj_add\',this);"><i class="ico add">&#xe620;</i> 新增跟进</a>'+
		'</div>',urlpre+'gj_list',700,520);
	}
});
zeai.listEach('.yj_list',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">',
		urlpre = 'crm_gj_yj.php?uid='+uid+'&submitok=';
		
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】约见管理'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'yj_list\',this,\'edHONG2\');" class="edHONG2"><i class="ico add">&#xe64b;</i> 约见列表</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'yj_add\',this,\'edHONG2\');"><i class="ico add">&#xe620;</i> 新增约见</a>'+
		'</div>',urlpre+'yj_list',700,520);
	}
});

zeai.listEach('.editdata',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid"));
		var title2 = obj.getAttribute("title2");
		var urlpre = 'u_mod_data.php?iframenav=1&submitok=mod&ifmini=1&uid=';
		zeai.iframe('修改【'+decodeURIComponent(title2)+'】资料'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframeA(\'modedatabox\',\''+urlpre+uid+'&t=1\',this);" class="ed">基本信息/资料</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+urlpre+uid+'&t=2\',this);">详细资料</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+urlpre+uid+'&t=3\',this);">联系方法</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+urlpre+uid+'&t=4\',this);">择偶要求</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+'u_mod_data.php?iframenav=1&submitok=photo&ifmini=1&uid='+uid+'&t=5\',this);">个人相册</a>'+
		'<a onclick="iframeA(\'modedatabox\',\''+'u_mod_data.php?iframenav=1&submitok=mod&ifmini=1&uid='+uid+'&t=6\',this);">认证资料</a>'+
		'</div>','u_mod_data.php?iframenav=1&submitok=mod&ifmini=1&uid='+uid);
	}
});
function iframeA(boxid,url,that){
	iframeAreset();
	that.class('ed');
	o('iframe_iframe').src=url;
	function iframeAreset(){zeai.listEach(zeai.tag(o(boxid),'a'),function(obj){obj.removeClass('ed');});}
}
</script>
<?php require_once 'bottomadm.php';ob_end_flush();?>


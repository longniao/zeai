<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if ( !ifint($classid) && !ifint($tg_uid))callmsg("forbidden","-1");
if($session_kind == 'crm' && !ifint($tg_uid)){
	if(!in_array('crm_user_bz',$QXARR))exit(noauth('暂无【给会员备注】权限'));
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php
if($submitok == "modok"){
	$bz = dataIO(TrimEnter($bz),'in');
	if (str_len($bz) > 500)callmsg("字太多不要超过500字节","-1");
	if(ifint($classid)){
		$TBL = __TBL_USER__;
		$SQL = " id=".$classid;
		$returnid = 'bz'.$classid;
	}elseif(ifint($tg_uid)){
		$TBL = __TBL_TG_USER__;
		$SQL = " id=".$tg_uid;
		$returnid = 'bz'.$tg_uid;
	}
	//
	$rt=$db->query("SELECT bz,nickname FROM ".$TBL." WHERE ".$SQL);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt,"num");
		$oldbz    = $row[0];
		$nickname = $row[1];
	}else{
		callmsg("您要备注的会员不存在！",-1);exit;
	}
	if(ifint($classid)){
		$uid=$classid;
		$logstr='修改会员备注【'.$nickname.'（uid:'.$uid.'）】原备注：'.$oldbz.' -> 新备注：'.$bz;
	}elseif(ifint($tg_uid)){
		$logstr='修改推广员备注【'.$nickname.'（id:'.$tg_uid.'）】原备注：'.$oldbz.' -> 新备注：'.$bz;
	}
	AddLog($logstr);
	//
	$db->query("UPDATE ".$TBL." SET bz='$bz' WHERE ".$SQL);
	$endbz = $bz;
?>
<script>
//document.domain = zeai.getDomain();
parent.document.getElementById('<?php echo $returnid; ?>').innerHTML = '<?php echo $endbz; ?>';
parent.document.getElementById('<?php echo $returnid; ?>').style.color='#f00';
parent.zeai.iframe(0);
</script>
<?php }?>
</head>
<link href="./css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<body>
<?php
if(ifint($classid)){
	$TBL = __TBL_USER__;
	$SQL = " id=".$classid;
}elseif(ifint($tg_uid)){
	$TBL = __TBL_TG_USER__;
	$SQL = " id=".$tg_uid;
}
$rt=$db->query("SELECT bz FROM ".$TBL." WHERE ".$SQL);
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,"num");
	$bz = $row[0];
}else{
	callmsg("您要备注的会员不存在！",-1);exit;
}
?>
<script>
function chkform(){
	if(zeai.str_len(o("bz").value)>500){
		zeai.msg('内容长度请控制在500字节以内',{mask:'off',focus:bz});
		return false;
	}
}
</script>
<form name="GYLform" id="GYLform" method="POST" action="<?php echo SELF;?>" onsubmit="return chkform();">
<table class="table0 W98_ Mtop20">
<tr>
<td class="center"><textarea name="bz" rows="8" class="textarea W90_ Mcenter" id="bz"><?php echo $bz;?></textarea></td>
</tr>
<tr>
<td height="60" class="center"><input class="btn size2 HUANG" type="submit" value="修改并保存" /><input type="hidden" name="submitok" value="modok" />
<input type="hidden" name="classid" value="<?php echo $classid;?>" />
<input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
</td>
</tr>
</table>
</form>
<?php require_once 'bottomadm.php';?>
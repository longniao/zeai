<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if ( !ifint($uid) && !ifint($tg_uid))callmsg("forbidden","-1");

if(ifint($uid)){
	$TBL = __TBL_USER__;
	$FLD = "uname,nickname,loveb,openid,subscribe";
	$SQL .= " id=".$uid;
}elseif(ifint($tg_uid)){
	$TBL = __TBL_TG_USER__;
	$FLD = "uname,title AS nickname,loveb,openid,subscribe";
	$SQL .= " id=".$tg_uid;
}


$row = $db->ROW($TBL,$FLD,$SQL);
if(!$row){
	textmsg("您要充值的会员不存在");
}else{
	$uname = dataIO($row[0],'out');
	$nickname = dataIO($row[1],'out');
	$loveb    = $row[2];
	$openid   = $row[3];
	$subscribe = $row[4];
	$nickname = (empty($nickname))?$uname:$nickname;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<title></title>
<?php
if($submitok == "modupdate"){
	if (!is_numeric($num) || empty($num))textmsg("请输入非0整数！",'back');
	if ($num>99999)textmsg("最多不能超过99999！",'back');
	$num     = intval($num);
	$content = ($num<0)?"管理员扣除":"管理员增加";
	$endnum  = $loveb + $num;
	if ($endnum<0)textmsg("账户不足".abs($num)."，扣除失败",'back');
	if(ifint($uid)){
		//loveb
		$db->query("UPDATE ".__TBL_USER__." SET loveb=$endnum WHERE id=".$uid);
		$db->AddLovebRmbList($uid,$content,$num,'loveb',1);
		//日志
		AddLog($content.'【'.$nickname.'（uid:'.$uid.'）】'.$num.$_ZEAI['loveB']);
		//站内消息2
		$C = $nickname.'您好，您的'.$_ZEAI["loveB"].'账户有变动：'.$content.$num.'　　<a href='.Href('loveb').' class=aQING>查看'.$_ZEAI["loveB"].'账户</a>';
		$db->SendTip($uid,"您的".$_ZEAI['loveB']."账户有变动",dataIO($C,'in'),'sys');
		//weixin_mb
		if (!empty($openid) && $subscribe==1){
			$first  = $nickname."您好，您的".$_ZEAI['loveB']."账户有变动：";
			$remark = $content."，查看详情";
			$url    = urlencode(mHref('loveb'));
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$openid.'&num='.$num.'&endnum='.$endnum.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		//
		$returnid = 'loveb'.$uid;
		$endbz = '<a href="javascript:;" class="aHONGed" onClick=zeai.iframe("【'.$uname.'】的'.$_ZEAI['loveB'].'清单","u_loveb_list.php?uid='.$uid.'",650,600)>'.$endnum.'</a>　<a href="javascript:;" onClick=zeai.iframe("给【'.$uname.'】修改'.$_ZEAI['loveB'].'","u_loveb_mod.php?uid='.$uid.'",320,250)><img src="images/add.gif" title="修改" /></a>';
	}elseif(ifint($tg_uid)){
		$db->query("UPDATE ".__TBL_TG_USER__." SET loveb=$endnum WHERE id=".$tg_uid);
		$db->AddLovebRmbList($tg_uid,$content,$num,'loveb',1,'tg');
		//日志
		AddLog($content.'【推广员：'.$nickname.'（id:'.$tg_uid.'）】'.$num.$_ZEAI['loveB']);
		//weixin_mb
		if (!empty($openid) && $subscribe==1){
			$first  = $nickname."您好，您的".$_ZEAI['loveB']."账户有变动：";
			//$remark = $content."，查看详情";
			//$url    = urlencode(mHref('loveb'));
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$openid.'&num='.$num.'&endnum='.$endnum.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
		$returnid = 'loveb'.$tg_uid;
		$endbz = '<a href="javascript:;" class="aHONGed" onClick=zeai.iframe("【'.$tg_uid.'】的'.$_ZEAI['loveB'].'清单","u_loveb_list.php?tg_uid='.$tg_uid.'",650,600)>'.$endnum.'</a>　<a href="javascript:;" onClick=zeai.iframe("给【'.$tg_uid.'】修改'.$_ZEAI['loveB'].'","u_loveb_mod.php?tg_uid='.$tg_uid.'",320,250)><img src="images/add.gif" title="修改" /></a>';
	}
	
?>
<script>
window.parent.document.getElementById('<?php echo $returnid; ?>').innerHTML = '<?php echo $endbz; ?>';
window.parent.zeai.iframe(0);
</script>
<?php exit;}?>

<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style type="text/css">.table{min-width:200px;}.table td{text-align:center;}</style>
<body>
<form name="GYLform" id="GYLform" method="POST" action="<?php echo $SELF;?>">
<table class="table Mtop30">
<tr>
<td style="border:none;" class="S16">增减数额 <input type="text" name="num" id="num" class="size2 S14 W80" value="0" maxlength="5" /></td>
</tr>
<tr>
<td style="border:none" class="C999">如果要扣除，直接填负数即可</td>
</tr>
<tr>
<td style="border:none;">
<input type="hidden" name="submitok" value="modupdate" />
<input type="hidden" name="uid" value="<?php echo $uid;?>" />
<input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
</td>
</tr>
</table>
<br><br><br><br><div class="savebtnbox"><button type="submit" class="btn size3 HONG">确认并保存</button></div>

</form>
<?php require_once 'bottomadm.php';?>
<?php
require_once '../sub/init.php';
header("Cache-control: private");
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('u_tg',$QXARR))exit(noauth());

require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
require_once ZEAI.'sub/TGfun.php';

if ($submitok == 'ajax_tg_flag1'){
	if (!ifint($uid) || !ifint($tguid))json_exit(JSON_ERROR);
	TG($tguid,$uid,'tg_reg',1);
	//
	$row2 = $db->ROW(__TBL_TG_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_TG_USER__,"nickname","id=".$tguid,'num');$tgnickname= $row2[0];
	AddLog('推广验证(合伙人)【推荐人：'.$tgnickname.'（id:'.$tguid.'）】->【合伙人：'.$nickname.'（id:'.$uid.'）】验证成功');
	json_exit(array('flag'=>1,'msg'=>'验证成功'));
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
<style>
.tablelist{min-width:1000px;margin:0 20px 50px 20px}
.table0{min-width:1000px;width:98%;margin:10px 20px 20px 20px}
.mtop{ margin-top:10px;}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
/***/
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
.SW{width:100px;}
.SW_area{width:120px;vertical-align:middle}
.SW_house{width:160px}
.RCW{display:inline-block}
.RCW li{width:80px}
i.wxlv{color:#31C93C;margin-right:2px}
.m{border:#eee 1px solid}
</style>
<?php
$SQL = "";
$Skeyword = trimhtml($Skeyword);
//搜索
$Skeyword = trimhtml($Skeyword);
if (!empty($Skeyword)){
	if(ifmob($Skeyword)){
		$SQL .= " AND mob=".$Skeyword;	
	}elseif(ifint($Skeyword)){
		$SQL .= " AND (id=".$Skeyword." OR tguid=".$Skeyword.")";	
	}else{
		$SQL .= " AND (  ( uname LIKE '%".$Skeyword."%' )  ) ";
	}
}
if (!empty($date1))$SQL .= " AND (regtime >= ".strtotime($date1.'00:00:01').") ";
if (!empty($date2))$SQL .= " AND (regtime <= ".strtotime($date2.'23:59:59').") ";
$rt = $db->query("SELECT id,photo_s,areatitle,regtime,tgflag,uname,flag,tguid,regip,mob,nickname,subscribe FROM ".__TBL_TG_USER__." WHERE tguid>0 AND tgflag=0 ".$SQL." ORDER BY id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);

$total0 = $db->COUNT(__TBL_USER__,"tguid>0 AND tgflag=0");
$total2 = $db->COUNT(__TBL_USER__,"tguid>0 AND tgflag=2");

?>
<body>
<div class="navbox">
    <a href="u_tg.php">单身验证审核（未审）<?php if($total0>0)echo '<b>'.$total0.'</b>';?></a>
    <a href="u_tg.php?t=2">单身验证审核（已驳回）<?php if($total2>0)echo '<b>'.$total2.'</b>';?></a>
  <a href="tg_tg.php" class="ed">合伙人验证审核<?php if($total>0)echo '<b>'.$total.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0">
    <tr>
    <td width="360" align="left" class="border0 S14">    <form name="form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="15" class="input size2" placeholder="按推荐人ID/合伙人ID/合伙人用户名/合伙人手机筛选" value="<?php echo $Skeyword; ?>" style="width:300px">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
        </form>   
    </td>
    <td align="center" class="border0 S14"></td>
    <td width="400" align="right" class="border0 S14" >
    
      <form name="www-zeai-cn.v6.2..QQ797311" method="get" action="<?php echo SELF; ?>">
        按合伙人注册时间 <input name="date1" type="text" id="date1" maxlength="25" class="input size2 W100" placeholder="起始时间" value="<?php echo $date1; ?>" autocomplete="off"> ～ 
        <input name="date2" type="text" id="date2" maxlength="25" class="input size2 W100" placeholder="结束时间" value="<?php echo $date2; ?>" autocomplete="off">
        <input type="hidden" name="Skeyword" value="<?php echo $Skeyword;?>" />
        <input type="hidden" name="sort" value="<?php echo $sort;?>" />
        <input type="hidden" name="t" value="<?php echo $t;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>   
    
    
    </td>
    </tr>
</table>
<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	$sorthref = SELF."?".$parameter."&sort=";
?>

    <form id="zeaiFORM" method="get" action="<?php echo SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="30"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="150">推荐人</th>
    <th width="34">&nbsp;</th>
    <th width="100">合伙人ID</th>
    <th width="50" class="center">合伙人头像</th>
    <th align="left">合伙人信息</th>
	<th width="90" align="center" class="center">关注公众号</th>
	<th width="90" align="center" class="center">合伙人注册状态</th>
    <th width="120" align="center" class="center">合伙人注册时间</th>
    <th width="200" align="center">确认有效性操作</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,"name");
		if(!$rows) break;
			$id  = $rows['id'];
			$uid  = $id ;
			$photo_s   = $rows['photo_s'];
			$flag      = $rows['flag'];
			$areatitle = $rows['areatitle'];
			$regtime   = $rows['regtime'];
			$tgflag    = $rows['tgflag'];
			$uname     = dataIO($rows['uname'],'out');
			$nickname  = dataIO($rows['nickname'],'out');
			$mob  = dataIO($rows['mob'],'out');
			$flag  = $rows['flag'];
			$tguid = $rows['tguid'];
			$regip = $rows['regip'];
			$mob   = dataIO($rows['mob'],'out');
			$regtime  = YmdHis($rows['regtime']);
			$subscribe = $rows['subscribe'];
			$uname=(empty($uname))?$mob:$uname;
			$uname = str_replace($Skeyword,"<b class='Cf00'>".$Skeyword."</b>",$uname);
			$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/noP.gif';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="m">';
			if($tgflag == 1){
				$tgflag_str = '<font class="flag1">成功</font>';
				$tgbtn_str  = '';
			}else{
				$tgflag_str = '<i class="timeico20"></i><font class="flag0">等待验证</font>';
				$tgbtn_str  = '<button uid="'.$uid.'" tguid="'.$tguid.'" type="button" class="btn size2 HUANG3 qq797311">验证并打款</button>';
			}
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="30"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" tguid="<?php echo $tguid; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
        </td>
      <td width="150"><?php
$row2 = $db->ROW(__TBL_TG_USER__,"uname,mob,money,tgallmoney","id=".$tguid);
if ($row2){
	$tguname2 = $row2[0];$tgmob2= dataIO($row2[1],'out');$money= $row2[2];;$tgallmoney= $row2[3];
	$tguname2 = (empty($tguname2))?$tgmob2:$tguname2;
	//echo $tguname2;
	echo 'ID：'.$tguid;
	echo '<br><font class="C999">当前余额：</font><font class="Cf00">￥'.str_replace(".00","",$money).'</font>';
	echo '<br><font class="C999">累计余额：</font><font class="C090">￥'.str_replace(".00","",$tgallmoney).'</font>';
}else{
	$db->query("UPDATE ".__TBL_TG_USER__." SET tguid=0,tgflag=0 WHERE id=".$id);
}
?></td>
      <td width="34"><img src="images/d2.gif"></td>
      <td width="100"><?php echo $id;?></td>
        <td width="50" class="center" style="padding:10px 0"><img src="<?php echo $photo_s_url; ?>" class="m"></td>
      <td align="left" class="lineH150 C999"><?php echo $uname; ?><br><?php echo $nickname; ?> <?php echo $areatitle; ?> <?php echo $mob; ?></td>
      <td width="90" align="center"><?php
if($subscribe==0){
	echo '<span class="C999"></span>';
}elseif($subscribe==1){
	echo '<i class="ico S14 wxlv">&#xe6b1;</i>';
}else{
	echo '<span class="C00f">取消</span>';
}
?></td>
      <td width="90" align="center"><?php

if($flag==1){echo '<font class="C090">正常</font>';}elseif($flag==0){echo '未审';}elseif($flag==2){echo '<font class="Cf00">未激活</font>';}

?></td>
      <td width="120" align="center" class="lineH150 C999"><?php echo $regtime; ?><br><?php echo $regip;?></td>
      <td width="200" align="center" class="lineH150 C666" style="padding:10px 0"><?php echo $tgbtn_str;?></td>
      </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="10">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action">发送消息</button>
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/zeai_tablelist.js"></script>
</form>
<?php }?>
<br><br><br>
<script>
if(!zeai.empty(o('btnsend')))o('btnsend').onclick = function() {sendTipFnTGU(this);}
function sendTipFnTGU(btnobj){
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要发送的推荐人');
		return false;
	}
	var arr = document.getElementsByName('list[]');
	var ulist = [],tguid;
	for( key in arr){
		if (arr[key].checked){
			tguid=arr[key].getAttribute("tguid");
			ulist.push(tguid);
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的推荐人');
	}else{
		zeai.iframe('发送消息','u_tip.php?kind=TG&ulist='+ulist,600,500);
	}
}

zeai.listEach('.qq797311',function(obj){obj.onclick = function(){
	zeai.confirm('确认要验证通过么？验证成功后将把奖励金额入账到推广员【余额账户】并进行消息通知',function(){
		zeai.msg('正在验证/发送通知...',{time:20});
		var uid=obj.getAttribute("uid");
		var tguid=obj.getAttribute("tguid");
		zeai.ajax('tg_tg'+zeai.ajxext+'submitok=ajax_tg_flag1&uid='+uid+'&tguid='+tguid,function(e){rs=zeai.jsoneval(e);
			if (rs.flag == 1){setTimeout(function(){zeai.msg(rs.msg);location.reload(true);},1000);}else{zeai.msg(0);zeai.alert(rs.msg);}
		});
	});
}});
</script>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem:'#date1',type: 'date'});
laydate.render({elem:'#date2',type: 'date'});
</script>

</script>
<?php require_once 'bottomadm.php';?>


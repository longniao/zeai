<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('shop',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_shop.php';
$TG_set = json_decode($_REG['TG_set'],true);
$kstr='买家用户';
switch ($submitok) {
	case"modflag":
		if (!ifint($uid))alert_adm_parent("forbidden","-1");
		$row = $db->ROW(__TBL_TG_USER__,"nickname,buyflag","id=$uid","num");
		if(!$row){
			alert_adm_parent("您要操作的用户不存在或已经删除！","-1");
		}else{
			$nickname = $row[0];$buyflag = $row[1];
			$SQL = "";
			switch($buyflag){
				case"-1":$SQL="buyflag=1";break;
				case"0":$SQL="buyflag=1";break;
				case"1":$SQL="buyflag=-1";break;
			}
			$db->query("UPDATE ".__TBL_TG_USER__." SET ".$SQL." WHERE id=".$uid);
			AddLog('【'.$kstr.'】->【'.$nickname.'（ID:'.$uid.'）】帐号状态修改');
			header("Location: ".SELF."?f=$f&p=$p");
		}
	break;
	case"alldel":
		if(!in_array('u_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'权限不足'));
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $uid){
				$uid=intval($uid);
				$row2 = $db->ROW(__TBL_TG_USER__,"uname,ifshop,title,photo_s,weixin_ewm,yyzz_pic,piclist","id=".$uid,'num');$nickname= $row2[0];$ifshop= $row2[1];$title= dataIO($row2[2],'out');
				$photo_s= $row2[3];$weixin_ewm= $row2[4];$yyzz_pic= $row2[5];$piclist= $row2[6];
				//if($ifshop==1){
				//	$kindstr=$_SHOP['title'];
				//	$nickname=$title;
				//}else{
				//	$kindstr='推广员';
				//}
				$kindstr='推广员/买家/'.$_SHOP['title'];
				if(!empty($photo_s)){
					@up_send_admindel($photo_s.'|'.smb($photo_s,'m').'|'.smb($photo_s,'b').'|'.smb(str_replace('/shop/','/tmp/',$photo_s),'blur'));
				}
				if(!empty($weixin_ewm)){
					@up_send_admindel($weixin_ewm.'|'.smb($weixin_ewm,'m').'|'.smb($weixin_ewm,'b').'|'.smb(str_replace('/shop/','/tmp/',$weixin_ewm),'blur'));
				}
				if(!empty($yyzz_pic)){
					@up_send_admindel($yyzz_pic.'|'.smb($yyzz_pic,'m').'|'.smb($yyzz_pic,'b').'|'.smb(str_replace('/shop/','/tmp/',$yyzz_pic),'blur'));
				}
				if(!empty($piclist)){
					$piclist = explode(',',$piclist);
					foreach ($piclist as $value) {
						@up_send_admindel($value.'|'.smb($value,'m').'|'.smb($value,'b').'|'.smb(str_replace('/shop/','/tmp/',$value),'blur'));
					}
				}
				$db->query("DELETE FROM ".__TBL_LOVEB_LIST__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_MONEY_LIST__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_TG_PRODUCT_KIND__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_SHOP_YUYUE__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_SHOP_FAV__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_SHOP_ORDER__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_SHOP_SEARCH__." WHERE tg_uid=".$uid);
				$rt = $db->query("SELECT id,path_s FROM ".__TBL_TG_PRODUCT__." WHERE tg_uid=".$uid);
				$total = $db->num_rows($rt);
				if ($total > 0 ) {
					for($i=1;$i<=$total;$i++) {
						$row = $db->fetch_array($rt,'name');
						if(!$row) break;
						$fid = $row['id'];
						$path_s = $row['path_s'];
						if(!empty($path_s)){$B = smb($path_s,'b');@up_send_admindel($path_s.'|'.$B.'|'.smb($path_s,'m'));}
						$db->query("DELETE FROM ".__TBL_TG_PRODUCT__." WHERE id=".$fid);
					}
				}
				$db->query("UPDATE ".__TBL_USER__." SET tguid=0,tgflag=0 WHERE tguid=".$uid);
				$db->query("DELETE FROM ".__TBL_TG_USER__." WHERE id=".$uid);
				AddLog('【'.$kindstr.'】帐号删除->【'.$nickname.'（ID:'.$uid.'）】');
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<?php if ($submitok != 'u' && $submitok != 'tgu'){?>
<style>
.tablelist{min-width:1111px;margin:0 20px 50px 20px}
.table0{min-width:1111px;width:98%;margin:10px 20px 10px 20px}
.mtop{ margin-top:10px;}
.dispaly{ display:block;}
.listtd{ display:block; width:50px;border-radius:12px;height:20px;line-height:20px;color:#888;padding:2px 5px;font-size:12px;background:#f9f9f9;border:#dedede solid 1px; margin:5px auto; margin-left:0px; text-align:center;}
.citybox{margin-left:20px}
.gradeflag{display:block;color:#999;padding-top:10px;font-family:'宋体'}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
i.wxlv{color:#31C93C;margin-right:2px}
.tjr{color:#666;text-align:center;margin:5px auto;border-radius:3px}
</style>
<?php }?>
<body>
<div class="navbox" style="min-width:1300px">
    <?php 
	$SQL = "1=1";
	$Skey = trimhtml($Skey);
	//搜索
	if (!empty($Skey)){
		if(ifmob($Skey)){
			$SQL .= " AND mob=".$Skey;	
		}elseif(ifint($Skey)){
			$SQL .= " AND (id=".$Skey." OR uid=".$Skey.")";	
		}elseif(str_len($Skey)>20){	
			$SQL .= " AND openid='$Skey'";				
		}else{
			$SQL .= " AND ( ( uname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".$Skey."%' ) ) ";
		}
	}
	switch ($f) {
		case 'f_1':$SQL .= " AND buyflag=-1";break;
		case 'f_2':$SQL .= " AND buyflag=-2";break;
		case 'f0':$SQL .= " AND buyflag=0";break;
		case 'f1':$SQL .= " AND buyflag=1";break;
		case 'f2':$SQL .= " AND buyflag=2";break;
		case 'f3':$SQL .= " AND buyflag=3";break;
	}
	?>
    <a href="shop_u.php"<?php echo (empty($f))?" class='ed'":""; ?>>买家管理<?php if (empty($f))echo '<b>'.$db->COUNT(__TBL_TG_USER__,$SQL).'</b>';?></a>
    <a href="shop_u.php?f=f1"<?php echo ($f == 'f1')?" class='ed'":""; ?>>正常<?php if ($f == 'f1')echo '<b>'.$db->COUNT(__TBL_TG_USER__,"  ".$SQL).'</b>';?></a>
    <a href="shop_u.php?f=f_1"<?php echo ($f == 'f_1')?" class='ed'":""; ?>>已锁定<?php if ($f == 'f_1')echo '<b>'.$db->COUNT(__TBL_TG_USER__,"  ".$SQL).'</b>';?></a>
    <div class="Rsobox">
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<?php
	switch ($sort) {
		case 'addtime0':$SORT = " ORDER BY regtime ";break;
		case 'addtime1':$SORT = " ORDER BY regtime DESC ";break;
		case 'endtime0':$SORT = " ORDER BY endtime ";break;
		case 'endtime1':$SORT = " ORDER BY endtime DESC ";break;
		case 'logincount0':$SORT = " ORDER BY logincount ";break;
		case 'logincount1':$SORT = " ORDER BY logincount DESC ";break;
		case 'uid0':$SORT = " ORDER BY id ";break;
		case 'uid1':$SORT = " ORDER BY id DESC ";break;
		default:$SORT = " ORDER BY id DESC ";break;
	}
$rt = $db->query("SELECT id,uname,nickname,uid,mob,photo_s,bz,tguid,tgflag,tgmoney,regtime,buyflag,flag,shopflag FROM ".__TBL_TG_USER__." TG_U WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a href='TG_u_mod.php?submitok=add&k=3' class='btn HUANG size2' href='javascript:history.back(-1)'>新增</a><br><br></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>

<table class="table0">
<tr>
<td width="120" align="left" class="border0" >


<button type="button" class="btn " onClick="zeai.openurl('TG_u_mod.php?submitok=add&k=3')"><i class="ico add">&#xe620;</i>新增<?php echo $kstr;?></button>
</td>
<td width="220" align="center" class="border0" ><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">买家：包括<?php echo $TG_set['navtitle'];?>和<?php echo $_SHOP['title'];?>用户</font></td>
<td align="center">
<form name="form1" method="get" action="<?php echo SELF; ?>">
    <input name="Skey" type="text" id="Skey" maxlength="50" class="W300 input size2" placeholder="输入：ID/用户名/手机/昵称/绑定的UID" title="也可以搜索OPENDID" value="<?php echo $Skey;?>">
    <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
    <input name="f" type="hidden" value="<?php echo $f; ?>" />
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    <input type="submit" value="搜索" class="btn size2 QING" />
</form>
</td>
<td width="220" align="left"></td>
<td width="90" align="right"><button type="button" id="btnsend" value="" class="btn size2 disabled action">发送消息</button></td>
</tr>
</table>
<form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
<table class="tablelist">
<tr>
<th width="30" class="Pleft10"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
<th width="130" align="center">ID/帐号/昵称</th>
<th width="60" align="center">头像</th>
<th width="100" align="center"><?php echo $TG_set['navtitle'];?></th>
<th width="100" align="center"><?php echo $_SHOP['title'];?></th>
<th align="center">&nbsp;</th>
<th width="100" style="min-width:50px">备注</th>
<th width="200" align="center">注册时间/IP/推荐人
  <div class="sort">
  	<?php $sorthref = SELF."?f=$f&sort="; ?>
    <a href="<?php echo $sorthref."uid0";?>" <?php echo($sort == 'uid0')?' class="ed"':''; ?>></a>
    <a href="<?php echo $sorthref."uid1";?>" <?php echo($sort == 'uid1')?' class="ed"':''; ?>></a>
  </div></th>
<th width="110" align="center">帐号状态/绑定UID</th>
<th width="70"  class="center">修改</th>
</tr>
<?php

	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$uname    = $rows['uname'];
		$nickname = strip_tags($rows['nickname']);
		$nickname = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$nickname);
		$mob = strip_tags($rows['mob']);
		$mob = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$mob);
		$photo_s   = $rows['photo_s'];
		$uid       = $rows['uid'];
		$bz = dataIO($rows['bz'],'out');
		$tguid     = $rows['tguid'];
		$tgflag    = $rows['tgflag'];
		$tgmoney   = $rows['tgmoney'];
		$regtime   = $rows['regtime'];
		$buyflag   = $rows['buyflag'];
		$flag      = $rows['flag'];
		$shopflag  = $rows['shopflag'];
		$nickname  = trimhtml(dataIO($rows['nickname'],'out'));
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/noP.gif';
		$photo_s_str = '<img src="'.$photo_s_url.'" class="m yuan">';
		$uname = empty($uname)?$mob:$uname;
?>
<tr id="tr<?php echo $id;?>">
<td width="30" height="68" class="Pleft10">
<input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
</td>
<td width="130" align="center">

<a href="#" onclick="zeai.openurl('<?php echo $fHREF2;?>')">
    <?php
    echo '<div class="S16">'.$id.'</div>';
	if(!empty($uname))echo '<div class="C999">'.$uname.'</div>';
	//if(!empty($mob))echo '<div class="C999">'.$mob.'</div>';
	if(!empty($nickname))echo '<div class="C999">'.$nickname.'</div>';
    ?>        
</a>


</td>
<td width="60" align="center">
<a href="javascript:;" class="zoom" onClick="parent.piczoom('<?php echo smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str; ?></a>
</td>
<td width="100" align="center"><?php echo ($flag != 2)?'<i class="ico S14 wxlv">&#xe6b1;</i>':'';?></td>
<td width="100" align="center"><?php echo ($shopflag != 2)?'<i class="ico S14 wxlv">&#xe6b1;</i>':'';?></td>
<td align="center"></td>

<td width="100" align="left" class="C8d " style="min-width:50px">
  <a href="#" onClick="zeai.iframe('给【<?php echo $id;?>】备注','u_bz.php?tg_uid=<?php echo $id;?>',500,280)" class="editico tips" tips-title='<?php echo $bz;?>'></a><span id="bz<?php echo $id;?>"><?php if (!empty($bz))echo '<font class="newdian"></font>';?></span>
</td>
<td width="200" align="center" class="C999 lineH150"><?php echo YmdHis($regtime);?><br><?php echo $rows['regip'];?>
  <?php
	if (ifint($tguid)){
		$row = $db->ROW(__TBL_TG_USER__,"id","id=".$tguid,"num");
		if ($row){
			$tgmob=dataIO($row[1],'out');
			echo '<div class="tjr">';
			echo '<div class="C666">推荐人ID:'.$tguid;
			if($tgflag==1 && $tgmoney>0){
				echo '　奖励 <font class="Cf00">'.str_replace(".00","",$tgmoney).'</font> 元';
			}
			echo '</div></div>';
		}
	}
    ?>
    </td>
<td width="110" align="center" >
	<?php 
	$fHREF  = SELF."?submitok=modflag&uid=$id&f=$f&p=$p";
	$fHREF2 = "TG_u_mod.php?submitok=mod&tg_uid=$id&f=$f&g=$g&k=3&p=$p&Skey=$Skey";
	?>
	<?php if($buyflag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击恢复正常">锁定</a><?php }?>
    <?php if($buyflag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击锁定后将不能登录">正常</a><?php }?>

  <?php if(ifint($uid)){?><br><a href="<?php echo Href('u',$uid);?>" title="前台用户UID" target="_blank" style="margin-top:15px;display:block"><?php echo $uid;?></a><?php }?>
</td>
<td width="70" class="center"><a onclick="zeai.openurl('<?php echo $fHREF2;?>')" class="btn size2 BAI tips" tips-title='设置/修改资料' tips-direction='left'>修改</a></td>
</tr>

<?php } ?>
<div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
	<button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnsend2" value="" class="btn size2 disabled action"><i class="ico">&#xe676;</i> 发送消息</button>　
	<input type="hidden" name="g" value="<?php echo $g;?>" />
	<input type="hidden" name="p" value="<?php echo $p;?>" />
	<input type="hidden" name="f" value="<?php echo $f;?>" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
</div>
</table>
<script>var bg = '<?php echo $_Style['list_bg']; ?>',overbg   = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</form>
<?php }?>
<br><br><br>
<?php if ($total > 0 ) {?>
<script>
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'shop_u'+zeai.ajxext+'submitok=alldel&k=<?php echo $k;?>',
		title:'批量删除（将同时删除绑定的商家和推广员）',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnsend').onclick = function() {sendTipFnTGU(this);}
o('btnsend2').onclick = function() {sendTipFnTGU(this);}
function sendTipFnTGU(btnobj){
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要发送的用户');
		return false;
	}
	var arr = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){
		if (arr[key].checked){
			ulist.push(arr[key].value);
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的用户');
	}else{
		zeai.iframe('发送消息','u_tip.php?kind=TG&ifshop=1&ulist='+ulist,600,500);
	}
}
</script>
<?php }?>
<?php require_once 'bottomadm.php';?>
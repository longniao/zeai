<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('u_tg',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
switch ($submitok) {
	case"company_apply_flag1":
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $uid){
				if ( !ifint($uid))exit(JSON_ERROR);
				$row = $db->ROW(__TBL_TG_USER__,"company_apply_kind,nickname,openid,subscribe","id=".$uid,"name");
				if ($row){
					$company_apply_kind= $row['company_apply_kind'];
					$data_nickname  = dataIO($row['nickname'],'out');
					$data_openid    = $row['openid'];
					$data_subscribe = $row['subscribe'];
					$kind=(empty($company_apply_kind))?2:intval($company_apply_kind);
					$db->query("UPDATE ".__TBL_TG_USER__." SET company_apply_flag=1,kind=$kind WHERE id=".$uid);
					AddLog('【商家/机构审核】->【'.$data_nickname.'（id:'.$uid.'）】通过');
					//站内消息
					$C = $data_nickname.'恭喜你！商户/机构申请审核通过';
					$db->SendTip($uid,"恭喜你！商户/机构申请审核通过",dataIO($C,'in'),'tg');
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode('已通过');
						$keyword2 = urlencode('申请的商户/机构信息符合规范');
						$url      = urlencode(HOST."/m1/tg_my.php");
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'批量审核'));
	break;
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
.tablelist{min-width:1400px;margin:0 20px 50px 20px}
.table0{min-width:1400px;width:98%;margin:10px 20px 10px 20px}
.mtop{ margin-top:10px;}
.dispaly{ display:block;}
.listtd{ display:block; width:50px;border-radius:12px;height:20px;line-height:20px;color:#888;padding:2px 5px;font-size:12px;background:#f9f9f9;border:#dedede solid 1px; margin:5px auto; margin-left:0px; text-align:center;}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
i.wxlv{color:#31C93C;margin-right:2px}
</style>
<body>
<div class="navbox" style="min-width:1300px">
    <?php 
	$SQL = " company_apply_flag=2 ";
	$Skeyword = trimhtml($Skeyword);
	//搜索
	$Skeyword = trimhtml($Skeyword);
	if (!empty($Skeyword)){
		if(ifmob($Skeyword)){
			$SQL .= " AND mob=".$Skeyword;	
		}elseif(ifint($Skeyword)){
			$SQL .= " AND id=".$Skeyword;	
		}else{
			$SQL .= " AND ( ( title LIKE '%".$Skeyword."%' ) OR ( uname LIKE '%".$Skeyword."%' ) ) ";
		}
	}
	if($f == 'f_1'){
		$SQL .= " AND flag=-1";
	}elseif($f == 'f0'){
		$SQL .= " AND flag=0";
	}elseif($f == 'f1'){
		$SQL .= " AND flag=1";
	}
	if(ifint($g))$SQL .= " AND grade=".$g;	
	?>
    <a href="TG_u.php?" class='ed'>商家/机构-申请审核<?php echo '<b>'.$db->COUNT(__TBL_TG_USER__,$SQL).'</b>';?></a>
    <div class="Rsobox">
        <form name="form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="W200 input size2" placeholder="输入：ID/帐号/手机/公司名称">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input name="f" type="hidden" value="<?php echo $f; ?>" />
        <input name="kind" type="hidden" value="<?php echo $kind; ?>" />
        <input name="k" type="hidden" value="<?php echo $k; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="g" value="<?php echo $g;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
      </form>     
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
$rt = $db->query("SELECT * FROM ".__TBL_TG_USER__." TG_U WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a><br></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
<form id="www_zeai_cn_FORM" method="get" action="<?php echo $SELF; ?>">
<table class="table0">
<tr>
<td width="150" align="left" class="border0" >
  <button type="button" id="btnflag" value="" class="btn size2 LV disabled action">批量审核</button>　</td>
<td align="left">
</td>
<td width="140" align="left"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">发消息关注公众号有效</font></td>
<td width="90" align="right"><button type="button" id="btnsend" value="" class="btn size2 disabled action">发送消息</button></td>
</tr>
</table>

<?php $sorthref = SELF."?f=$f&k=$k&g=$g&sort="; ?>
<table class="tablelist">
<tr>
<th width="30" class="Pleft10"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
<th width="150" align="center">申请人ID/帐号</th>
<th width="80" align="center">头像</th>
<th width="100" align="center"><span class="center">等级</span></th>
<th width="150" align="center" class="center">申请类型</th>
<th width="150" align="center" class="center">商家/机构名称</th>
<th width="150" align="center" class="center">地址/电话</th>
<th style="min-width:50px">备注</th>
<th width="140" align="center">申请时间
  <div class="sort">
    <a href="<?php echo $sorthref."uid0";?>" <?php echo($sort == 'uid0')?' class="ed"':''; ?>></a>
    <a href="<?php echo $sorthref."uid1";?>" <?php echo($sort == 'uid1' || empty($sort))?' class="ed"':''; ?>></a>
  </div></th>
<th width="150" align="center">操作</th>
</tr>
<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$kind = $rows['kind'];
		$uname = strip_tags($rows['uname']);
			$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
			$title = dataIO($rows['title'],'out');
			$title = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$title);
		$mob = strip_tags($rows['mob']);
		$mob = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$mob);
		$photo_s   = $rows['photo_s'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$gradetitle= $rows['gradetitle'];
		$loveb     = $rows['loveb'];
		$flag      = $rows['flag'];
		$areatitle = $rows['areatitle'];
		$bz = dataIO($rows['bz'],'out');
		$tel = dataIO($rows['tel'],'out');
		$title = dataIO($rows['title'],'out');
		$address = dataIO($rows['address'],'out');
		$company_apply_kind = $rows['company_apply_kind'];

		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/noP.gif';
		$photo_s_str = '<img src="'.$photo_s_url.'" class="m">';
		switch ($kind) {
			case 1:$kind_str  = '个人';break;
			case 2:$kind_str  = '商家';break;
			case 3:$kind_str  = '机构';break;
		}
		switch ($company_apply_kind) {
			case 1:$apply_kind_str  = '个人';break;
			case 2:$apply_kind_str  = '商家';break;
			case 3:$apply_kind_str  = '机构';break;
		}
		
		$fHREF  = SELF."?submitok=modflag&uid=$id&g=$g&f=$f&k=$k&p=$p";
		$fHREF2 = "TG_u_mod.php?submitok=mod&tg_uid=$id&f=$f&g=$g&k=$k&p=$p";
?>
<tr id="tr<?php echo $id;?>">
<td width="30" height="68" class="Pleft10">
<input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
</td>
<td width="150" align="center">

<a href="#" onclick="zeai.openurl('<?php echo $fHREF2;?>')">
    <?php
    echo '<div class="S16">'.$id.'</div>';
	if(!empty($uname))echo '<div class="C999">'.$uname.'</div>';
	if(!empty($mob))echo '<div class="C999">'.$mob.'</div>';
    ?>        
</a>




</td>
<td width="80" align="center">
<a href="javascript:;" class="zoom" onClick="parent.piczoom('<?php echo smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str; ?></a>
</td>

<td width="100" align="center" class="lineH150" style="padding:10px 0">

<?php echo $gradetitle; ?>
  
</td>
<td width="150" align="center" class="S14">

<?php echo $kind_str; ?>　<i class="ico Caaa">&#xe62d;</i>　<?php echo $apply_kind_str;?>

</td>
<td width="150" align="center" class="S14"><?php echo $title;?></td>
<td width="150" align="center" class="lineH150"><div><?php echo $address;?><div><?php echo $tel;?></div></div></td>
<td align="left" class="C8d " style="min-width:50px">
  <a href="#" onClick="zeai.iframe('给【<?php echo $id;?>】备注','u_bz.php?tg_uid=<?php echo $id;?>',500,280)" class="editico tips" tips-title='<?php echo $bz;?>'></a><span id="bz<?php echo $id;?>"><?php if (!empty($bz))echo '<font class="newdian"></font>';?></span>
</td>
<td width="140" align="center" class="C999"><?php 
echo YmdHis($rows['regtime']);?><br><?php echo $rows['regip'];
?></td>
<td width="150" align="center" class="lineH200">
  <font class="Caaa">未审核</font><br>
  <a href="javascript:;" title="审核" class="aLVed company_apply_flag1" uid="<?php echo $id; ?>" nickname="<?php echo urlencode(trimhtml($title));?>">通过审核</a>
</td>
</tr>

<?php } ?>
<tfoot><tr>
<td colspan="10">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btnsend2" value="" class="btn size2 disabled action">发送消息</button>　
	<input type="hidden" name="g" value="<?php echo $g;?>" />
	<input type="hidden" name="p" value="<?php echo $p;?>" />
	<input type="hidden" name="f" value="<?php echo $f;?>" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
</td>
</tr></tfoot>
</table>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</form>
<?php }?>
<br><br><br>
<?php if ($total > 0 ) {?>
<script>
zeai.listEach('.company_apply_flag1',function(obj){
	var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.confirm('确认要审核【'+decodeURIComponent(nickname)+'】的申请么？',function(){
			zeai.ajax('TG_u23apply'+zeai.ajxext+'submitok=company_apply_flag1&list[]='+uid,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});
o('btnflag').onclick = function() {
	allList({
		btnobj:this,
		url:'TG_u23apply'+zeai.ajxext+'submitok=company_apply_flag1',
		title:'批量审核',
		msg:'正在审核中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnsend').onclick = function() {sendTipFnTGU(this);}
o('btnsend2').onclick = function() {sendTipFnTGU(this);}
function sendTipFnTGU(btnobj){
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要发送的会员');
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
		zeai.alert('请选择要发送的会员');
	}else{
		zeai.iframe('发送消息','u_tip.php?kind=TG&ulist='+ulist,600,500);
	}
}
</script>
<?php }?>
<?php require_once 'bottomadm.php';?>


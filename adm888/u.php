<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('u',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
switch ($submitok) {
	case"all_update_endtime":
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要操作的用户'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $uid){
				if ( !ifint($uid))exit(JSON_ERROR);
				$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME." WHERE id=".$uid);
			}
			$list = (is_array($tmeplist))?implode(',',$tmeplist):'';$uid=0;
			AddLog('批量更新用户【'.$list.'】最后登录时间');
		}
		json_exit(array('flag'=>1,'msg'=>'更新成功'));
	break;
	case"modflag":
		if (!ifint($uid))alert_adm_parent("forbidden","-1");
		$row = $db->ROW(__TBL_USER__,"nickname,flag","id=".$uid,'num');
		if(!$row){
			alert_adm_parent("您要操作的用户不存在或已经删除！","-1");
		}else{
			$nickname= $row[0];$oldflag= $row[1];
			$SQL = "";
			switch($oldflag){
				case"-1":$SQL="flag=1";$newflag=1;break;
				case"-2":$SQL="flag=1";$newflag=1;break;
				case"0":$SQL="flag=1";$newflag=1;break;
				case"1":$SQL="flag=-1";$newflag=-1;break;
			}
			AddLog('修改用户【'.$nickname.'（uid:'.$uid.'）】帐号状态，原状态：'.flagtitle($oldflag).' -> '.'新状态【'.flagtitle($newflag).'】');
			//
			$db->query("UPDATE ".__TBL_USER__." SET ".$SQL." WHERE id=".$uid);
			header("Location: ".SELF."?kind=$kind&f=$f&p=$p&Skeyword=".$Skeyword);
		}
	break;
	case"alldel":
		if(!in_array('u_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'权限不足'));
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $uid){
				if ( !ifint($uid))exit(JSON_ERROR);
				$db->query("DELETE FROM ".__TBL_TIP__." WHERE uid=".$uid." OR senduid=".$uid);
				$db->query("DELETE FROM ".__TBL_LOVEB_LIST__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_MONEY_LIST__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_PAY__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_WXENDURL__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_HONGBAO__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_HONGBAO_USER__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_UCOUNT__." WHERE uid=".$uid);
				//
				$db->query("DELETE FROM ".__TBL_HN_BBS__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_CRM_HT__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_CRM_MATCH__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_CRM_MATCH__." WHERE uid2=".$uid);
				$db->query("DELETE FROM ".__TBL_CRM_FAV__." WHERE uid=".$uid);
				//删无图
				$db->query("DELETE FROM ".__TBL_CLICKHISTORY__." WHERE senduid=".$uid);
				$db->query("DELETE FROM ".__TBL_DATING__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_DATING_USER__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_GIFT_USER__." WHERE uid=".$uid." OR senduid=".$uid);
				$db->query("DELETE FROM ".__TBL_GZ__." WHERE uid=".$uid." OR senduid=".$uid);
				$db->query("DELETE FROM ".__TBL_LOVEB_LIST__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_MONEY_LIST__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_MSG__." WHERE uid=".$uid." OR senduid=".$uid);
				$db->query("DELETE FROM ".__TBL_PARTY_USER__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_PARTY_BBS__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_315__." WHERE uid=".$uid);
				//活动评论，签到
				$db->query("DELETE FROM ".__TBL_PARTY_USER__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_PARTY_BBS__." WHERE uid=".$uid);
				$db->query("DELETE FROM ".__TBL_PARTY_SIGN__." WHERE uid=".$uid);
				//视频
				$rt = $db->query("SELECT path_s FROM ".__TBL_VIDEO__." WHERE uid=".$uid);
				$total = $db->num_rows($rt);
				if ($total > 0) {
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt);
						if(!$rows) break;
						$path_s = $rows[0];
						$path_b = str_replace(".jpg",".mp4",$path_s);
						up_send_admindel($path_s.'|'.$path_b);
					}
					$db->query("DELETE FROM ".__TBL_VIDEO__." WHERE uid=".$uid);
				}
				//相册
				$rt=$db->query("SELECT path_s FROM ".__TBL_PHOTO__." WHERE uid=".$uid);
				$total = $db->num_rows($rt);
				if ($total > 0) {
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt);
						if(!$rows) break;
						$path_s = $rows[0];
						$path_b = smb($path_s,'b');
						up_send_admindel($path_s.'|'.$path_b);
					}
					$db->query("DELETE FROM ".__TBL_PHOTO__." WHERE uid=".$uid);
				}
				//认证
				$rt=$db->query("SELECT path_b,path_b2 FROM ".__TBL_RZ__." WHERE uid=".$uid);
				$total = $db->num_rows($rt);
				if ($total > 0) {
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt);
						if(!$rows) break;
						$path_b = $rows[0];$path_b2 = $rows[1];
						up_send_admindel($path_b.'|'.$path_b2);
					}
					$db->query("DELETE FROM ".__TBL_RZ__." WHERE uid=".$uid);
				}
				//交友圈
				$rt=$db->query("SELECT id,piclist FROM ".__TBL_TREND__." WHERE uid=".$uid);
				$total = $db->num_rows($rt);
				if ($total > 0) {
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt);
						if(!$rows) break;
						$fid = $rows[0];$piclist = $rows[1];
						if (!empty($piclist)){
							$piclist = explode(',',$piclist);
							if (count($piclist) >= 1){
								foreach ($piclist as $value){
									$path_s = $value;
									$path_b = smb($path_s,'b');
									up_send_admindel($path_s.'|'.$path_b);
								}
							}
						}
						$db->query("DELETE FROM ".__TBL_TREND_BBS__." WHERE fid=".$fid);
					}
					$db->query("DELETE FROM ".__TBL_TREND__." WHERE uid=".$uid);
					$db->query("DELETE FROM ".__TBL_TREND_BBS__." WHERE uid=".$uid);
				}
				//group
				$db->query("DELETE FROM ".__TBL_GROUP_USER__." WHERE userid=".$uid);
				$db->query("DELETE FROM ".__TBL_GROUP_BK__." WHERE userid=".$uid);
				$db->query("DELETE FROM ".__TBL_GROUP_WZ__." WHERE userid=".$uid);
				$db->query("DELETE FROM ".__TBL_GROUP_WZ_BBS__." WHERE userid=".$uid);
				$db->query("DELETE FROM ".__TBL_GROUP_CLUB_BBS__." WHERE userid=".$uid);
				$db->query("DELETE FROM ".__TBL_GROUP_CLUB_USER__." WHERE userid=".$uid);
				$rt = $db->query("SELECT id,picurl_s FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$uid);
				if($db->num_rows($rt)){
					$rows = $db->fetch_array($rt);
					$mainid = $rows[0];$photo_s = $rows[1];
					@up_send_admindel($photo_s);
					$db->query("DELETE FROM ".__TBL_GROUP_PHOTO__." WHERE mainid=".$mainid);
					$db->query("DELETE FROM ".__TBL_GROUP_PHOTO_KIND__." WHERE mainid=".$mainid);
					$db->query("DELETE FROM ".__TBL_GROUP_CLUB__." WHERE mainid=".$mainid);
					$db->query("DELETE FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE mainid=".$mainid);
					$db->query("DELETE FROM ".__TBL_GROUP_LINKS__." WHERE mainid=".$mainid);
				}
				$db->query("DELETE FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$uid);
				//主
				$rt = $db->query("SELECT photo_s,tgpic FROM ".__TBL_USER__." WHERE id=".$uid);
				if($db->num_rows($rt)){
					$rows = $db->fetch_array($rt);
					$photo_s = $rows[0];$tgpic = $rows[1];
					$photo_m = smb($photo_s,'m');
					$photo_b = smb($photo_s,'b');
					$photo_blur = smb($photo_s,'blur');
					@up_send_admindel($photo_s.'|'.$photo_m.'|'.$photo_b.'|'.$photo_blur.'|'.$tgpic);
				}
				$db->query("DELETE FROM ".__TBL_USER__." WHERE id=".$uid);
				//
			}
			$list = (is_array($tmeplist))?implode(',',$tmeplist):'';$uid=0;
			AddLog('批量删除用户，UID列表->【'.$list.'】');
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"allflag1":
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $uid){
				if ( !ifint($uid))exit(JSON_ERROR);
				$db->query("UPDATE ".__TBL_USER__." SET flag=1 WHERE (flag=0 OR flag=2) AND id=".$uid);
			}
			$list = (is_array($tmeplist))?implode(',',$tmeplist):'';$uid=0;
			AddLog('批量审核用户帐号状态为【正常】，UID列表->【'.$list.'】');
		}
		json_exit(array('flag'=>1,'msg'=>'批量审核'));
	break;
}

//
$SQL = "";
if($ukind == 4){
	$SQL1  = " kind=4";	
}else{
	$SQL1  = " kind<>4";	
}
$Skeyword = trimhtml($Skeyword);
if (!empty($Skeyword)){
	if(ifmob($Skeyword)){
		$SQL .= " AND mob=".$Skeyword;	
	}elseif(ifint($Skeyword)){
		$SQL .= " AND id=".$Skeyword;	
	}elseif(str_len($Skeyword)>20){	
		$SQL .= " AND openid='$Skeyword'";	
	}else{
		$SQL .= " AND (( truename LIKE '%".$Skeyword."%' ) OR ( uname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' ) OR ( bz LIKE '%".$Skeyword."%' )  ) ";
	}
}
switch ($f) {
	case -1:$SQL .= " AND flag=-1";break;
	case -2:$SQL .= " AND flag=-2";break;
	case 1:$SQL  .= " AND flag=1";break;
	case 2:$SQL  .= " AND flag=2";break;
}
if($g == 2){
	$SQL .= " AND grade>1";
	if(ifint($ugrade))$SQL .= " AND grade=$ugrade";
}
if($ifadm == 1)$SQL .= " AND ifadm=1";
if($sex == 1)$SQL .= " AND sex=1";
if($sex == 2)$SQL .= " AND sex=2";
if($ifdata_10 == 1)$SQL .= " AND myinfobfb<10 ";
if($ifdata50 == 1)$SQL  .= " AND myinfobfb>50 ";
if ($ifmob==1)$SQL   .= " AND mob<>'' ";
if ($ifbz==1)$SQL    .= " AND bz<>'' ";
if ($ifadmid==1)$SQL .= " AND admid>0 ";
if ($iftguid==1)$SQL .= " AND tguid>0 ";
if ($ifgz==1)$SQL    .= " AND subscribe=1 ";
if($ifparent == 1)$SQL  .= " AND parent>1 ";
if ($photo_s == 1)$SQL  .= " AND photo_s<>'' ";
if($g == 2 && !empty($g_)){
	switch ($g_) {
		case 3:$SQL  .= " AND (  (  (sjtime + if2*30*86400) - ".ADDTIME."  ) <= 259200     AND   (".ADDTIME." <= (sjtime + if2*30*86400))    ) ";break;
		case 7:$SQL  .= " AND (  (  (sjtime + if2*30*86400) - ".ADDTIME."  ) <= 604800     AND   (".ADDTIME." <= (sjtime + if2*30*86400))    )";break;
		case 30:$SQL .= " AND (  (  (sjtime + if2*30*86400) - ".ADDTIME."  ) <= 2592000    AND   (".ADDTIME." <= (sjtime + if2*30*86400))    )";break;
		case -1:$SQL .= " AND (  ".ADDTIME." > (sjtime + if2*30*86400) )";break;
	}
}
if (ifint($grade))$SQL  .= " AND grade=".$grade;
if(!empty($rz))$SQL  .= " AND FIND_IN_SET('$rz',RZ)";
$searchA = "u.php?grade=$grade&g=$g&f=$f&ukind=$ukind&ifmob=$ifmob&ifdata_10=$ifdata_10&ifdata50=$ifdata50&ifbz=$ifbz&ifadmid=$ifadmid&iftguid=$iftguid&ifgz=$ifgz&p=$p&Skeyword=$Skeyword";
$sorthref = $searchA."&sort="; 
$urole = json_decode($_ZEAI['urole'],true);
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
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
em{color:#666;display:none;padding-left:10px}
.success{color:#090}
.error{color:#f00}
.mtop{ margin-top:10px;}
.dispaly{ display:block;}
.listtd{ display:block; width:50px;border-radius:12px;height:20px;line-height:20px;color:#888;padding:2px 5px;font-size:12px;background:#f9f9f9;border:#dedede solid 1px; margin:5px auto; margin-left:0px; text-align:center;}
.citybox{margin-left:20px}
.ewmpic{width:30px;height:30px;border:#e6e6e6 1px solid;filter:alpha(opacity=40);-moz-opacity:0.4;opacity:0.4}
.ewmpic:hover{filter:alpha(opacity=100);-moz-opacity:1;opacity:1}
.gradeflag{display:block;color:#999;padding-top:10px}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:18px;background-color:rgba(255,111,111,0.7);color:#fff;font-size:12px}
.noU58 span.ifadm{background-color:#FF5722;color:#fff}
.tgbox{width:80%;padding:2px 5px;margin-top:5px;border:#eee 1px solid;background-color:#f9f9f9;clear:both;overflow:auto}
.tgbox a{color:#666}
.tgbox a.aHONGed{width:50px;margin:5px 0;display:block;padding:2px 6px;border-radius:2px;float:right;color:#fff}
.tgbox font{color:#74AC55;float:right}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
i.wxlv{color:#31C93C;margin-right:2px}
.navbox{min-width:1200px}
.RCWsex{display:inline-block}
.RCWsex li{width:60px}
.formline{height:10px}
.forcerz{margin-top:10px}
</style>
<body>
<div class="navbox">
    <a href="u.php"<?php echo ( $f != -1 && $f != 1 && $f != 2 && $f != -2 && $g != 2 && $ukind != 4 && $ifadm != 1 )?" class='ed'":""; ?>>用户管理/升级<?php if ($f != -1 && $f != 1 && $f != 2 && $f != -2 && $g != 2 && $ukind != 4 && $ifadm != 1 )echo '<b>'.$db->COUNT(__TBL_USER__,$SQL1.$SQL).'</b>';?></a>
    <a href="u.php?f=1"<?php echo ($f == 1)?" class='ed'":""; ?>>注册成功<?php if ($f == 1)echo '<b>'.$db->COUNT(__TBL_USER__," flag=1 AND kind<>4 ".$SQL).'</b>';?></a>
    <a href="u.php?g=2"<?php echo ($g == 2)?" class='ed'":""; ?>>VIP<?php if ($g == 2)echo '<b>'.$db->COUNT(__TBL_USER__," grade>1 AND kind<>4 ".$SQL).'</b>';?></a>
    <a href="u.php?f=-2"<?php echo ($f == -2)?" class='ed'":""; ?>>已隐藏<?php if ($f == -2)echo '<b>'.$db->COUNT(__TBL_USER__," flag=-2 ".$SQL).'</b>';?></a>
    <a href="u.php?f=2"<?php echo ($f == 2)?" class='ed'":""; ?>>注册未完成<?php if ($f == 2)echo '<b>'.$db->COUNT(__TBL_USER__," flag=2 AND kind<>4 ".$SQL).'</b>';?></a>
    <a href="u.php?ifadm=1"<?php echo ($ifadm ==1)?" class='ed'":""; ?>>前台管理员<?php if ($ifadm == 1)echo '<b>'.$db->COUNT(__TBL_USER__," ifadm=1 ".$SQL).'</b>';?></a>
    <a href="u.php?ukind=4"<?php echo ($ukind ==4)?" class='ed'":""; ?>>机器人<?php if ($ukind == 4)echo '<b>'.$db->COUNT(__TBL_USER__," kind=4 ".$SQL).'</b>';?></a>
    <a href="u.php?f=-1"<?php echo ($f == -1)?" class='ed'":""; ?>>已锁定/注销<?php if ($f == -1)echo '<b>'.$db->COUNT(__TBL_USER__," flag=-1 ".$SQL).'</b>';?></a>
    <div class="Rsobox">
    </div>
<div class="clear"></div></div>
<div class="fixedblank60"></div>
<?php
	
	switch ($sort) {
		case 'loveb0':$SORT = " ORDER BY loveb ";break;
		case 'loveb1':$SORT = " ORDER BY loveb DESC ";break;
		case 'money0':$SORT = " ORDER BY money ";break;
		case 'money1':$SORT = " ORDER BY money DESC ";break;
		case 'addtime0':$SORT = " ORDER BY regtime ";break;
		case 'addtime1':$SORT = " ORDER BY regtime DESC ";break;
		case 'endtime0':$SORT = " ORDER BY endtime ";break;
		case 'endtime1':$SORT = " ORDER BY endtime DESC ";break;
		case 'logincount0':$SORT = " ORDER BY logincount ";break;
		case 'logincount1':$SORT = " ORDER BY logincount DESC ";break;
		case 'click0':$SORT = " ORDER BY click ";break;
		case 'click1':$SORT = " ORDER BY click DESC ";break;
		case 'uid0':$SORT = " ORDER BY id ";break;
		case 'uid1':$SORT = " ORDER BY id DESC ";break;
		case 'flag0':$SORT = " ORDER BY flag,id DESC ";break;
		case 'flag1':$SORT = " ORDER BY flag DESC,id DESC ";break;
		case 'myinfobfb0':$SORT = " ORDER BY myinfobfb,id DESC ";break;
		case 'myinfobfb1':$SORT = " ORDER BY myinfobfb DESC,id DESC ";break;
		default:$SORT = " ORDER BY id DESC ";break;
	}
$fld="id,admid,admname,kind,uname,pwd,nickname,truename,photo_s,photo_f,mob,sex,ifadm,grade,loveb,money,subscribe,openid,if2,sjtime,flag,areatitle,myinfobfb,regkind,regtime,regip,endtime,endip,bz,longitude,latitude,tguid,tgflag,parent,RZ";
if($g == 2 && !empty($g_)){
	$rt = $db->query("SELECT $fld FROM ".__TBL_USER__." a WHERE ".$SQL1.$SQL."   AND grade>1 AND if2<>999 AND if2>0 AND sjtime>0 ORDER BY id DESC LIMIT ".$_ADM['admLimit']);
}else{
	$rt = $db->query("SELECT $fld FROM ".__TBL_USER__." WHERE ".$SQL1.$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
}
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	$gstr="<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回上一步</a>";	
	echo "<div class='nodataico'><i></i>暂无信息".$gstr."</div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	$mate_diy_arr=explode(',',$_ZEAI['rz_data']);
?>

<div class="topsearch" style="min-width:1200px">
        <form name="form1" method="get" action="<?php echo SELF; ?>" style="display:inline-block">
        按用户 <input name="Skeyword" type="text" id="Skeyword" size="40" class="input size2 W300" value="<?php echo $Skeyword;?>" placeholder="UID/用户名/手机/姓名/昵称/微信OPENID/备注" title="也可以搜索OPENDID">　
        按等级 <select id="grade" name="grade" class="size2 picmiddle C666">
        <option value="0"></option>
        <?php foreach($urole as $RV){?><option value="<?php echo $RV['g'];?>"<?php echo ($RV['g'] == $grade)?' selected':'';?>><?php echo $RV['t'];?></option><?php }?>
        </select>　
        按认证 <select id="rz" name="rz" class="size2 picmiddle C666">
        <option value="0"></option>
        <?php foreach($mate_diy_arr as $V){?><option value="<?php echo $V;?>"<?php echo ($V == $rz)?' selected':'';?>><?php echo rz_data_info($V,'title');?></option><?php }?>
        </select>　
        
        按性别 <ul id="sex_box" name="sex_box" class="size2 RCW RCWsex"><li><input type="radio" class="radioskin" id="sex1" name="sex" value="1"<?php echo ($sex == 1)?' checked':''; ?>><label for="sex1" class="radioskin-label"><i class="i1"></i><b class="">男</b></label></li><li><input type="radio" class="radioskin" id="sex2" name="sex" value="2"<?php echo ($sex == 2)?' checked':''; ?>><label for="sex2" class="radioskin-label"><i class="i1"></i><b class="">女</b></label></li></ul>
        
		<input type="checkbox" name="ifparent" id="ifparent" class="checkskin" value="1"<?php echo ($ifparent == 1)?' checked':''; ?>><label for="ifparent" class="checkskin-label"><i></i><b class="W80 S14">父母帮征婚</b></label>
        
        <div class="formline"></div>
        
        <input type="checkbox" name="ifmob" id="ifmob" class="checkskin" value="1"<?php echo ($ifmob == 1)?' checked':''; ?> ><label for="ifmob" class="checkskin-label"><i></i><b class="W50 S14">有手机</b></label>
        <input type="checkbox" name="ifdata_10" id="ifdata_10" class="checkskin" value="1"<?php echo ($ifdata_10 == 1)?' checked':''; ?> ><label for="ifdata_10" class="checkskin-label"><i></i><b class="W80 S14">资料<10%</b></label>
        <input type="checkbox" name="ifdata50" id="ifdata50" class="checkskin" value="1"<?php echo ($ifdata50 == 1)?' checked':''; ?> ><label for="ifdata50" class="checkskin-label"><i></i><b class="W80 S14">资料>50%</b></label>
        <input type="checkbox" name="ifbz" id="ifbz" class="checkskin" value="1"<?php echo ($ifbz == 1)?' checked':''; ?>><label for="ifbz" class="checkskin-label"><i></i><b class="W50 S14">有备注</b></label>
        <input type="checkbox" name="ifadmid" id="ifadmid" class="checkskin" value="1"<?php echo ($ifadmid == 1)?' checked':''; ?>><label for="ifadmid" class="checkskin-label"><i></i><b class="W50 S14">被认领</b></label>
        <input type="checkbox" name="iftguid" id="iftguid" class="checkskin" value="1"<?php echo ($iftguid == 1)?' checked':''; ?>><label for="iftguid" class="checkskin-label"><i></i><b class="W80 S14">有推荐人</b></label>
        <input type="checkbox" name="photo_s" id="photo_s" class="checkskin" value="1"<?php echo ($photo_s == 1)?' checked':''; ?>><label for="photo_s" class="checkskin-label"><i></i><b class="W50 S14">有头像</b></label>　
        <input type="checkbox" name="ifgz" id="ifgz" class="checkskin" value="1"<?php echo ($ifgz == 1)?' checked':''; ?>><label for="ifgz" class="checkskin-label"><i></i><b class="W80 S14">关注公众号</b></label>
        
        <button type="submit" class="btn size3 QING picmiddle"> <i class="ico">&#xe6c4;</i> 搜索 </button>
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input name="g" type="hidden" value="<?php echo $g; ?>" />
        <input name="f" type="hidden" value="<?php echo $f; ?>" />
        <input name="ukind" type="hidden" value="<?php echo $ukind; ?>" />
        <input name="ugrade" type="hidden" value="<?php echo $ugrade; ?>" />
        <input name="g_" type="hidden" value="<?php echo $g_; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        </form>     

    <?php if ($g == 2 && is_array($urole) && count($urole)>0){?>
    	<hr>
        <dl><dt>VIP等级：</dt><dd>
        <a href="javascript:;" <?php echo (empty($ugrade))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&g_=<?php echo $g_;?>')">不限</a>
        <?php
		foreach($urole as $RV){
			if($RV['g']==1)continue;?>
        	<a href="javascript:;" <?php echo ($ugrade == $RV['g'])?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&g_=<?php echo $g_;?>&ugrade=<?php echo $RV['g'];?>')"><?php echo $RV['t'];?></a>
        	<?php
		}?>
        </dd></dl>
        <dl><dt>过期时间：</dt><dd>
        <a href="javascript:;" <?php echo (empty($g_))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&ugrade=<?php echo $ugrade;?>&g_=')">不限</a>
        <a href="javascript:;" <?php echo ($g_==3)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&ugrade=<?php echo $ugrade;?>&g_=3')">3天内到期</a>
        <a href="javascript:;" <?php echo ($g_==7)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&ugrade=<?php echo $ugrade;?>&g_=7')">7天内到期</a>
        <a href="javascript:;" <?php echo ($g_==30)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&ugrade=<?php echo $ugrade;?>&g_=30')">30天内到期</a>
        <a href="javascript:;" <?php echo ($g_==-1)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&ugrade=<?php echo $ugrade;?>&g_=-1')">已过期</a>
        </dd></dl>
	<?php }?>
    <div class="clear"></div>
</div>



<table class="tablelist" style="min-width:1325px">
<form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
<tr>
<th width="30" class="Pleft10"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
<th width="70" align="center">用户UID/认证</th>
<th width="80" align="center">头像</th>
<th width="130">用户名/手机/昵称</th>
<th width="55" class="center">扫码查看</th>
<th width="120" class="center">所在地区</th>
<th width="100" class="center">用户组/等级</th>
<th width="100"><?php echo $_ZEAI['loveB'];?>
<div class="sort">
	<a href="<?php echo $sorthref."loveb0";?>" <?php echo($sort == 'loveb0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."loveb1";?>" <?php echo($sort == 'loveb1')?' class="ed"':''; ?>></a>
</div>
</th>
<th width="100">余额
<div class="sort">
	<a href="<?php echo $sorthref."money0";?>" <?php echo($sort == 'money0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."money1";?>" <?php echo($sort == 'money1')?' class="ed"':''; ?>></a>
</div></th>
<th width="40" align="center">公众号</th>
<th align="center">备注</th>
<th width="100" align="center">用户相亲卡片</th>
<th width="130">注册来源/时间
<div class="sort">
	<a href="<?php echo $sorthref."uid0";?>" <?php echo($sort == 'uid0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."uid1";?>" <?php echo($sort == 'uid1' || empty($sort))?' class="ed"':''; ?>></a>
</div>
</th>
<th width="70" align="center">类型/注册状态</th>
<th width="90" align="left">　个人资料
<div class="sort">
	<a href="<?php echo $sorthref."myinfobfb0";?>" <?php echo($sort == 'myinfobfb0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."myinfobfb1";?>" <?php echo($sort == 'myinfobfb1')?' class="ed"':''; ?>></a>
</div>
</th>
</tr>
<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$Ukind = $rows['kind'];
		$admid = $rows['admid'];
		$admname = dataIO($rows['admname'],'out');
		$pwd = $rows['pwd'];
		$uname = strip_tags($rows['uname']);
		$truename = strip_tags($rows['truename']);
			$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
			$truename = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$truename);
			$nickname = trimhtml(dataIO($rows['nickname'],'out'));
			$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$mob = strip_tags($rows['mob']);
		$mob = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$mob);
		$photo_s   = $rows['photo_s'];
		$photo_f   = $rows['photo_f'];
		$sex       = $rows['sex'];
		$ifadm     = $rows['ifadm'];
		$grade     = $rows['grade'];
		$loveb     = $rows['loveb'];
		$money     = $rows['money'];
		$subscribe = $rows['subscribe'];
		$openid = $rows['openid'];
		$if2       = $rows['if2'];
		$sjtime    = $rows['sjtime'];
		$flag      = $rows['flag'];
		$areatitle = $rows['areatitle'];
		$longitude = $rows['longitude'];
		$latitude  = $rows['latitude'];
		$RZZ = $rows['RZ'];
		$bz = dataIO($rows['bz'],'out');
		$tguid     = $rows['tguid'];
		$tgflag    = $rows['tgflag'];
		$tgpic     = $rows['tgpic'];
		$myinfobfb = $rows['myinfobfb'];
		$parent = $rows['parent'];
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'">';
		}else{
			$photo_s_str = '';
			$photo_s_url = '';
		}		
		if(empty($rows['nickname'])){
			if(empty($rows['truename'])){
				$title = $rows['mob'];
			}else{
				$title = $rows['truename'];
			}
			if(empty($title))$title=$uname;
		}else{
			$title = dataIO($rows['nickname'],'out');
		}
		switch ($grade) {
			case 1:$gradestyle   = " class='aHUI'";break;
			case 2:$gradestyle   = " class='aFEN'";break;
			case 3:$gradestyle   = " class='aLAN'";break;
			case 4:$gradestyle   = " class='aTUHAO'";break;
			case 5:$gradestyle   = " class='aHUANG'";break;
			case 6:$gradestyle   = " class='aHONG'";break;
			case 7:$gradestyle   = " class='aLV'";break;
			case 8:$gradestyle   = " class='aZI'";break;
			case 9:$gradestyle   = " class='aJIN'";break;
			case 10:$gradestyle  = " class='aQINGed'";break;
		}
		//gradeflag
		$timestr1 = get_if2_title($if2);
		if (!empty($sjtime)){
			$d1  = ADDTIME;
			$d2  = $sjtime + $if2*30*86400;
			$ddiff = $d2-$d1;
			if ($ddiff < 0){
				$timestr2 = ',<font class="Cf00 B">已过期</font>';
				$timestr2 .= '<br>过期日：'.YmdHis($d2,'Ymd');
			} else {
				$tmpday   = intval($ddiff/86400);
				$timestr2 = ',还剩<font class="Cf00">'.$tmpday.'</font>天';
				$timestr2 .= '<br>到期日：'.YmdHis($d2,'Ymd');
			}
			$timestr2 = ($if2 >= 999)?'':$timestr2;
		}
		$gradeflag = ($grade == 1 || $grade == 10)?'':'<span class="gradeflag">'.$timestr1.$timestr2.'</span>';
		//$photo_fstr = ($photo_f == 0 && !empty($photo_s))?'<span>头像未审</span>':'';
		$ifadm_str = ($ifadm == 1)?'<span class="ifadm">管理员</span>':'';
		
		switch ($parent) {
			case 2:$photo_fstr = '<span>父母帮</span>';break;
			case 3:$photo_fstr = '<span>亲友帮</span>';break;
			default:$photo_fstr='';break;
		}
		//
		$title2 = (!empty($nickname))?urlencode(trimhtml($nickname)).'／'.$id:$id;
?>
<tr id="tr<?php echo $id;?>">
<td width="30" height="68" class="Pleft10">
<input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
</td>
<td width="70" align="center">
<a href="<?php echo HOST."/m1/login.php?submitok=admlogin&uid=".$id."&pwd=".$pwd; ?>&uu=<?php echo $session_uid; ?>&pp=<?php echo $session_pwd; ?>" target="_blank" title="进入此用户个人中心" class="btn size1 BAI"><?php echo $id;?></a>
<?php if(!empty($RZZ)){?><div title="手动强制认证" uid="<?php echo $id;?>" class="forcerz hand"><?php echo RZ_html($RZZ,'s','color');?></div><?php }?>
</td>
<td width="80" align="center">
<a href="javascript:;" class="noU58 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str.$photo_fstr.$ifadm_str; ?></a>
</td>

<td width="130" align="left" class="lineH150" style="padding:10px 0">
    <a href="javascript:;" uid="<?php echo $id;?>" title2="<?php echo $title2;?>" class="photo_s">
    <?php echo uicon($sex.$grade) ?><?php if(!empty($rows['uname']))echo '<font class="S14">'.$uname.'</font></br>';?></a>
    <font class="uleft">
    <?php
    if(!empty($rows['mob']))echo $mob."</br>";
    if(!empty($rows['nickname']))echo $nickname;
    $title = urlencode($title);
    ?>
    </font>
</td>
<td width="55" align="left" class="center">
<a href="javascript:;" onclick="parent.zeai.iframe('【<?php echo $title;?>】二维码','u_ewm.php?uid=<?php echo $id;?>',300,300);" title="放大二维码" class="zoom">
<img src="images/ewm.gif" class="ewmpic">
</a></td>
<td width="120" class="center C8d" ><?php echo $areatitle; ?><?php if (!empty($longitude)){ ?>
<br><a class="hand" onClick="zeai.openurl_('http://map.qq.com/?type=marker&isopeninfowin=1&markertype=1&pointx=<?php echo $longitude; ?>&pointy=<?php echo $latitude; ?>&name=当前位置&addr=当前位置&ref=myapp')"><img src="images/gps.gif" style="padding-top:5px"></a>
	<?php }?></td>
<td width="100" valign="middle" class="center" id="grade<?php echo $id;?>" style="padding:12px 0 8px 0">
  <a href="javascript:;" <?php echo $gradestyle; ?> onClick="zeai.iframe('修改【<?php echo $title;?>】用户等级','u_grade.php?uid=<?php echo $id;?>',350,380)"><?php echo utitle($grade); ?></a>
  <?php echo $gradeflag; ?>
</td>
<td width="100" align="left" id="loveb<?php echo $id;?>">
  
	<a href="javascript:;" class="aHUI" onClick="zeai.iframe('【<?php echo $title;?>】的<?php echo $_ZEAI['loveB']; ?>清单','u_loveb_list.php?uid=<?php echo $id;?>',650,600)" title="<?php echo $_ZEAI['loveB']; ?>清单"><?php echo $loveb;?></a>&nbsp;
	<a href="javascript:;" onClick="zeai.iframe('给【<?php echo $title;?>】增减<?php echo $_ZEAI['loveB']; ?>','u_loveb_mod.php?uid=<?php echo $id;?>',320,250)"><img src="images/add.gif" title="增减<?php echo $_ZEAI['loveB']; ?>" /></a>
  
</td>
<td width="100" align="left" id="money<?php echo $id;?>">

	<a href="javascript:;" class="aHONG" onClick="zeai.iframe('【<?php echo $title;?>】的余额清单','u_money_list.php?uid=<?php echo $id;?>',650,600)" title="余额清单"><?php echo $money;?></a>&nbsp;
	<a href="javascript:;" onClick="zeai.iframe('给【<?php echo $title;?>】增减余额','u_money_mod.php?uid=<?php echo $id;?>',320,250)"><img src="images/add.gif" title="增减余额" /></a>

</td>
<td width="40" align="center"><?php
if($subscribe==0){
	echo '<span class="C999"></span>';
}elseif($subscribe==1){
	echo '<i class="ico S14 wxlv" title="已关注">&#xe6b1;</i>';
}else{
	echo '<span class="C00f">取消</span>';
}
?>
</td>
<td align="center" class="C8d ">
  <a href="#" onClick="zeai.iframe('给【<?php echo $title;?>】用户备注','u_bz.php?classid=<?php echo $id;?>',500,280)" class="editico tips" tips-title='<?php echo $bz;?>'></a><span id="bz<?php echo $id;?>"><?php if (!empty($bz))echo '<font class="newdian"></font>';?></span>
</td>
<td width="100" align="center" class="C8d "><a href="#" class="aHUI card" uid="<?php echo $id;?>" title2="<?php echo urlencode(trimhtml($nickname.'　｜　'.$id));?>">生成卡片</a></td>
<td width="130" align="left" class="C999 lineH150"><?php 
$openid_str=(!empty($openid))?'<i class="ico wxlv">&#xe607;</i>':'';
switch ($rows['regkind']) {
	case 1:$regkind_str  = 'PC注册';break;
	case 2:$regkind_str  = 'PC微信扫码';break;
	case 3:$regkind_str  = '<i class="ico wxlv">&#xe607;</i>微信关注/登录';break;
	case 4:$regkind_str  = 'QQ登录';break;
	case 5:$regkind_str  = '新浪微博';break;
	case 6:$regkind_str  = $openid_str.'手机端注册';break;
	case 7:$regkind_str  = '微信小程序';break;
	case 8:$regkind_str  = '手机APP';break;
	case 9:$regkind_str  = (ifint($admid))?'<font class="C666">'.$admname.'(ID:'.$admid.')</font>':'';break;
	case 10:
		if(ifint($admid)){
			$regkind_str = '<font class="C666">'.$admname.'(ID:'.$admid.')</font>';
		}
	break;
	case 11:
		$regkind_str  = (ifint($admid))?'<font class="C666">'.$admname.'(ID:'.$admid.')</font>':'';
		$regkind_str  = '表单采集-'.$regkind_str; 
	break;
	default:$regkind_str = '未知';break;
}
$regkind_str=($Ukind==4)?'机器人':$regkind_str;echo $regkind_str.'<br>';
echo YmdHis($rows['regtime']);?><?php if(!empty($rows['regip']))echo '<br>IP：'.$rows['regip'];
if (ifint($tguid)){
	$tgrow = $db->ROW(__TBL_TG_USER__,"nickname,title,kind,tgmoney","id=".$tguid,"name");
	if ($tgrow){
		$nickname_tg = dataIO($tgrow['nickname'],'out');
		$title_tg    = $tgrow['title'];
		$kind_tg     = $tgrow['kind'];
		//$tgflag_tg     = $tgrow['tgflag'];
		$tgmoney_tg     = $tgrow['tgmoney'];
		if($kind_tg==2 || $kind_tg == 3){
			$nickname_tg=$title_tg;
		}
		echo '<br>推荐人：'.$nickname_tg.'<br>推荐人ID：'.$tguid.' ';
		if($tgflag==0){
			echo '<font class="Cf60">未验证</font>';
		}
	}
}
?>
</td>
<td width="70" align="center" valign="top" style="line-height:200%;padding-top:10px;color:#999">
<style>.ukindd a{margin-bottom:9px;display:inline-block;line-height:20px;padding:0 10px}</style>
<?php 
switch ($Ukind) {
	case 1:$kindstyle   = " class='aLV'";break;
	case 2:$kindstyle   = " class='aHUI'";break;
	case 3:$kindstyle   = " class='aQING'";break;
}
?>
<div class="ukindd" id="ukind<?php echo $id;?>">
<a href="javascript:;" <?php echo $kindstyle;?> onClick="zeai.iframe('修改【<?php echo $title;?>】用户类型','crm_user.php?submitok=usre_kind_mod&uid=<?php echo $id;?>',350,380)"><?php echo user_kind($Ukind); ?></a>
</div>

<?php
//echo user_kind($Ukind).'<br>';
$fHREF = SELF."?submitok=modflag&uid=$id&g=$g&f=$f&p=$p&Skeyword=$Skeyword";
		 if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aHEI" title="点击恢复正常"><?php echo flagtitle(-1);?></a><?php }?>
  <?php if($flag==0){?><a href="#" class="aHUANG flag1" uid="<?php echo $id;?>" nickname="<?php echo urlencode(trimhtml($nickname.'　｜　'.$id));?>" title="点击强制完成—>正常"><?php echo flagtitle(0);?></a><?php }?>
  <?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击【锁定】用户，将不能登录"><?php echo flagtitle(1);?></a><?php }?>
  <?php if($flag==2){?><a href="#" class="aHUI flag1" uid="<?php echo $id;?>" nickname="<?php echo urlencode(trimhtml($nickname.'　｜　'.$id));?>" title="点击强制完成—>正常"><?php echo flagtitle(2);?></a><?php }?>
  <?php if($flag==-2){?><a href="<?php echo $fHREF;?>" class="aHUI" title="点击恢复正常"><?php echo flagtitle(-2);?></a><?php }?>

  </td>
<td width="90" class="center">
  <span class="<?php if($myinfobfb >80){echo ' myinfobfb2 ';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>"><?php echo $myinfobfb;?>%</span>
  <br>
  <a href="javascript:;" class="btn size2 BAI tips editdata" tips-title='设置/修改用户资料' tips-direction='left' style="margin-top:5px" uid="<?php echo $id;?>" title2="<?php echo urlencode(trimhtml($nickname.' ｜ '.$id));?>">修改</a>
</td>
</tr>

<?php } ?>
    <div class="listbottombox ">
        <input type="hidden" name="g" value="<?php echo $g;?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="f" value="<?php echo $f;?>" />
        <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
        <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">删除</button>　
        <button type="button" id="btnflag" value="" class="btn size2 LV disabled action">审核注册状态</button> 
        <button type="button" id="btnsend2" value="" class="btn size2 disabled action"><i class="ico">&#xe676;</i> 发消息</button> 
        <button type="button" id="btnsend3" value="" class="btn size2 disabled action">更新登录时间</button>
        <?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
    </div>
</form>
</table>
<script>var bg = '<?php echo $_Style['list_bg']; ?>',overbg   = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<script>
zeai.listEach('.forcerz',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid"));
		zeai.iframe('【UID：'+uid+'】手动强制认证','cert_hand.php?submitok=show&memberid='+uid)
	}
});
zeai.listEach('.card',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),title2 = obj.getAttribute("title2");
		zeai.iframe('生成【'+decodeURIComponent(title2)+'】相亲卡','u_card.php?uid='+uid)
	}
});
zeai.listEach('.flag1',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
		zeai.confirm('确认要审核【'+decodeURIComponent(nickname)+'】么？（此审核是注册状态审核，审核后用户可以登录系统操作，不是个人资料审核）',function(){
			zeai.ajax('u'+zeai.ajxext+'submitok=allflag1&list[]='+uid,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});
if(!zeai.empty(o('btndellist')))o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'u'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
if(!zeai.empty(o('btnflag')))o('btnflag').onclick = function() {
	allList({
		btnobj:this,
		url:'u'+zeai.ajxext+'submitok=allflag1',
		title:'批量审核（此审核是注册状态审核，审核后用户可以登录系统操作，不是个人资料审核）',
		msg:'正在审核中...',
		ifjson:true,
		ifconfirm:true
	});	
}
if(!zeai.empty(o('btnsend')))o('btnsend').onclick = function() {sendTipFn(this);}
if(!zeai.empty(o('btnsend2')))o('btnsend2').onclick = function() {sendTipFn(this);}
if(!zeai.empty(o('btnsend3')))o('btnsend3').onclick = function() {
	allList({
		btnobj:this,
		url:'u'+zeai.ajxext+'submitok=all_update_endtime',
		title:'批量更新最后登录时间（主要争对人工添加长期不登录或机器人用户）',
		msg:'正在更新中...',
		ifjson:true,
		ifconfirm:true
	});	
}
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

</script>
<?php require_once 'bottomadm.php';?>
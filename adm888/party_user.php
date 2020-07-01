<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if ( !ifint($fid))callmsg("www_zeai_cn_error_fid","-1");
if($submitok=='add_update' || $submitok=='mod_update'){
	//if ( !ifint($uid))callmsg("UID错误","-1");
	if(ifint($uid)){
		$row = $db->ROW(__TBL_USER__,"id","id=".$uid);
		if (!$row)alert_adm("找不到此会员","-1");
	}
	$row = $db->ROW(__TBL_PARTY__,"flag","id=".$fid,'num');
	if (!$row){alert_adm("活动不存在或已被删除","-1");}else{
		//if($row[0] != 1)alert_adm("活动状态只有在【报名中】才可以操作哦","-1");
	}
	$uid=intval($uid);
	$ifpay=intval($ifpay);
	$sex=intval($sex);
	$birthday=intval($birthday);
	$mob = dataIO($mob,'in',11);
	$truename = dataIO($truename,'in',12);
	$tel      = dataIO($tel,'in',200);
	$weixin   = dataIO($weixin,'in',11);
	if(empty($mob))alert_adm("手机不能为空","-1");
	if(empty($sex))alert_adm("请选择性别","-1");
	if(empty($birthday))alert_adm("出生年不能为空","-1");
	if(empty($truename))alert_adm("姓名不能为空","-1");
}
if($submitok=='add_update'){
	$row = $db->ROW(__TBL_PARTY_USER__,"id","(uid=".$uid." AND uid>0 OR mob=".$mob." AND mob<>'') AND fid=".$fid);
	if ($row){
		alert_adm("此用户已经报过名了","-1");
	}else{
		$db->query("INSERT INTO ".__TBL_PARTY_USER__."  (uid,fid,flag,mob,ifpay,addtime,sex,truename,weixin,birthday,tel) VALUES ('$uid','$fid',1,'$mob','$ifpay',".ADDTIME.",'$sex','$truename','$weixin','$birthday','$tel')");
		$db->query("UPDATE ".__TBL_PARTY__." SET bmnum=bmnum+1 WHERE flag=1 AND id=".$fid);
		if(ifint($uid)){
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
			AddLog('【交友活动】活动id:'.$fid.'->手动增加报名会员->【'.$nickname.'（uid:'.$uid.'）】');
		}else{
			AddLog('【交友活动】活动id:'.$fid.'->手动增加报名会员->【'.$truename.'（手机号:'.$mob.'）】');
		}
	}
	alert_adm("报名成功","party_user.php?fid=".$fid);
}elseif($submitok=='mod_update'){
	if (!ifint($id))alert_adm("id参数错误","-1");
	$db->query("UPDATE ".__TBL_PARTY_USER__." SET sex='$sex',uid='$uid',mob='$mob',truename='$truename',weixin='$weixin',ifpay='$ifpay',birthday='$birthday',tel='$tel' WHERE id=".$id);
	$row2 = $db->ROW(__TBL_PARTY_USER__,"fid,uid","id=".$id,'num');$fid= $row2[0];$uid= $row2[1];
	if(ifint($uid)){
		$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
		AddLog('【交友活动】活动id:'.$fid.'->修改报名会员信息->【'.$nickname.'（uid:'.$uid.'）】');
	}else{
		AddLog('【交友活动】活动id:'.$fid.'->修改报名会员信息->【'.$truename.'（手机号:'.$mob.'）】');
	}
	alert_adm("修改成功","party_user.php?fid=".$fid);
}elseif($submitok=='mod'){
	if (!ifint($id))alert_adm("id参数错误","-1");
	$row = $db->ROW(__TBL_PARTY__,"flag","id=".$fid,'num');
	if (!$row){alert_adm("活动不存在或已被删除","-1");}else{
		//if($row[0] != 1)alert_adm("活动状态只有在【报名中】才可以报名哦","-1");
	}
	$rt = $db->query("SELECT * FROM ".__TBL_PARTY_USER__." WHERE id=".$id);
	if($db->num_rows($rt)){
		$row   = $db->fetch_array($rt,'name');
		$ifpay = intval($row['ifpay']);
		$sex = intval($row['sex']);
		$mob   = dataIO($row['mob'],'out');
		$birthday = dataIO($row['birthday'],'out');
		$truename = dataIO($row['truename'],'out');
		$weixin = dataIO($row['weixin'],'out');
		$tel    = dataIO($row['tel'],'out');
		$uid    = $row['uid'];
	}else{
		alert_adm("该活动不存在！","-1");
	}	
}elseif($submitok=='ajax_del'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在或已被删除'));
	$row2 = $db->ROW(__TBL_PARTY_USER__,"uid","id=".$id,'num');$uid= $row2[0];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	AddLog('【交友活动】活动id:'.$fid.'->删除报名会员->【'.$nickname.'（uid:'.$uid.'）】');
	$db->query("DELETE FROM ".__TBL_PARTY_USER__." WHERE fid=".$fid." AND id=".$id);
	$totalnum = $db->COUNT(__TBL_PARTY_USER__,"fid=".$fid);
	$db->query("UPDATE ".__TBL_PARTY__." SET bmnum=$totalnum WHERE id=".$fid);
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok=='ajax_photo_ifshow'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在或已被删除'));
	$photo_ifshow=intval($v);
	$db->query("UPDATE ".__TBL_PARTY_USER__." SET photo_ifshow='$photo_ifshow' WHERE id=".$id);
	$row2 = $db->ROW(__TBL_PARTY_USER__,"fid,uid","id=".$id,'num');$fid= $row2[0];$uid= $row2[1];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	AddLog('【交友活动】活动id:'.$fid.'->修改报名会员头像隐藏->【'.$nickname.'（uid:'.$uid.'）】');
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok=='excel'){
	$nodatatext = '未填';
	//$rt = $db->query("SELECT a.*,U.nickname,U.sex,U.mob,U.birthday FROM ".__TBL_PARTY_USER__." a,".__TBL_USER__." U WHERE a.uid=U.id AND a.fid=".$fid." ORDER BY a.id DESC");
	$rt = $db->query("SELECT * FROM ".__TBL_PARTY_USER__." U WHERE fid=".$fid." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if($total>0){
		$content = "<meta http-equiv='Content-Type' content='text/html;charset=utf-8'><table><tr style='background:#FF6F6F;'><td>UID</td><td>姓名</td><td>性别</td><td>出生年</td><td>手机</td><td>微信</td><td>是否付款</td></tr>";
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$truename = dataIO($rows['truename'],'out');
			$sex      = $rows['sex'];
			$birthday = $rows['birthday'];
			$mob   = dataIO($rows['mob'],'out');
			$weixin = dataIO($rows['weixin'],'out');
			
			if ($sex == 1){
				$sex = '男';
			} else {
				$sex = '女';
			}
			
			$addtime = YmdHis($rows['addtime']);
			$tel = dataIO($rows['tel'],'out');
			$ifpay = $rows['ifpay'];
			
			if ($ifpay == 1){
				$ifpay = '已付款';
			} else {
				$ifpay = '未付款';
			}
			if ($birthday == '0000-00-00')$birthday = '';
			
			$content.= "<tr><td>".$rows['uid']."</td><td>".$truename."</td><td>".$sex."</td><td>".$birthday."</td><td>".$mob."</td><td>".$weixin."</td><td>".$ifpay."</td></tr>";
		}
		$content.= '</table>';
		//$filaname =  urldecode($partytitle).YmdHis(ADDTIME,'Ymd').'报名信息';
		$filaname =  YmdHis(ADDTIME).'报名信息';
		header("Content-type:application/vnd.ms-excel;charset=utf-8");
		header("Content-Disposition:filename=".$filaname.".xls");
		echo $content;
		AddLog('报名信息【数据导出】活动ID：'.$fid);
	} else {
		callmsg("暂无报名信息","-1");
	}
	exit;
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
a.pic50 img{height:100%;width:100%}
a.pic50{margin:10px auto}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;text-align:center;font-size:12px;margin-right:2px}
</style>
<body>
<?php
	$row = $db->ROW(__TBL_PARTY__,"bmnum,rmb_n,rmb_r,title","id=".$fid);
	if (!$row)json_exit(array('flag'=>0,'msg'=>'活动不存在或已被删除'));
	$bmnum = $row[0];
	$rmb_n = $row[1];
	$rmb_r = $row[2];
	$partytitle = dataIO($row[3],'out');
	//
	$SQL = "";
	$Skeyword = trimm($Skeyword);
	if (ifmob($Skeyword)){
		$SQL = " AND (mob=$Skeyword) ";
	}elseif(ifint($Skeyword)){
		$SQL = " AND (uid=$Skeyword) ";
	}elseif(!empty($Skeyword)){
		$SQL = " AND ( truename LIKE '%".$Skeyword."%' )";
	}
	if($s==1){
		$SQL .= " AND sex=1 ";
	}elseif($s==2){
		$SQL .= " AND sex=2 ";
	}
	$rt = $db->query("SELECT * FROM ".__TBL_PARTY_USER__." WHERE fid=".$fid.$SQL." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
		if (!empty($SQL))echo "<br><br><a class='aQINGed' href='javascript:history.back(-1)'>重新筛选</a>";
		echo "</div>";
	} else {
		$page_skin = 1;$pagesize=1000;require_once ZEAI.'sub/page.php';
		$totalsex1 = $db->COUNT(__TBL_PARTY_USER__,"sex=1 AND fid=".$fid);
		$totalsex2 = $db->COUNT(__TBL_PARTY_USER__,"sex=2 AND fid=".$fid);
	?>
    <div class="navbox">
    <a href="party_user.php?fid=<?php echo $fid;?>" <?php echo (empty($s))?' class="ed"':'';?>>活动报名管理<?php echo '<b>'.$total.'</b>';?></a>
    <a href="party_user.php?fid=<?php echo $fid;?>&s=1" <?php echo ($s==1)?' class="ed"':'';?>>男：<?php echo '<b class="border">'.$totalsex1.'</b>';?></a>
    <a href="party_user.php?fid=<?php echo $fid;?>&s=2" <?php echo ($s==2)?' class="ed"':'';?>>女：<?php echo '<b class="border">'.$totalsex2.'</b>';?></a>
    <div class="Rsobox"></div>
    <div class="clear"></div></div>
    <div class="fixedblank"></div>
<?php if($submitok=='add' || $submitok=='mod'){?>
    <form name="ZEAI_FORM" method="POST" action="<?php echo SELF;?>">
    <table class="table Mtop10 W500 size2">
    <tr>
    <td class="tdL">UID</td>
    <td class="tdR"><input name="uid" id="uid" type="text" class="input size2 W200" value="<?php echo $uid;?>" maxlength="10" placeholder="报名者会员UID，没有留空"  /></td>
    </tr>
    <tr>
    <td class="tdL">手机</td>
    <td class="tdR"><input name="mob" id="mob" type="text" class="input size2 W200" maxlength="11" value="<?php echo $mob;?>" placeholder="报名者手机号码"   /></td>
    </tr>
    <tr>
    <td class="tdL">姓名</td>
    <td class="tdR"><input name="truename" id="truename" type="text" class="input size2 W200" maxlength="12" value="<?php echo $truename;?>" placeholder="报名者真实姓名"  /></td>
    </tr>
    <tr>
    <td class="tdL">微信</td>
    <td class="tdR"><input name="weixin" id="weixin" type="text" class="input size2 W200" maxlength="50" value="<?php echo $weixin;?>" placeholder="报名者微信号"  /></td>
    </tr>
    <tr>
    <td class="tdL">性别</td>
    <td class="tdR">
    <input type="radio" name="sex" id="sex1" class="radioskin" value="1"  <?php if($sex==1){echo'checked="checked"';}?>><label for="sex1" class="radioskin-label"><i></i><b class="W50 S14 sex1color">男</b></label>
    <input type="radio" name="sex" id="sex2" class="radioskin" value="2"  <?php if($sex==2){echo'checked="checked"';}?>><label for="sex2" class="radioskin-label"><i></i><b class="W50 S14 sex2color">女</b></label>
    </td>
    </tr>
    <tr>
    <td class="tdL">出生年</td>
    <td class="tdR"><input name="birthday" id="birthday" type="text" class="input size2 W200" maxlength="4" placeholder="出生年份，如：1995" value="<?php echo $birthday;?>" /></td>
    </tr>

    <tr>
    <td class="tdL">是否缴费</td>
    <td class="tdR">
    <input type="radio" name="ifpay" id="ifpay0" class="radioskin" value="0"  <?php if($ifpay==0){echo'checked="checked"';}?>><label for="ifpay0" class="radioskin-label"><i></i><b class="W50 S14 C999">未缴</b></label>
    <input type="radio" name="ifpay" id="ifpay1" class="radioskin" value="1"  <?php if($ifpay==1){echo'checked="checked"';}?>><label for="ifpay1" class="radioskin-label"><i></i><b class="W50 S14 C090">已缴</b></label>
    </td>
    </tr>
    <tr>
    <td class="tdL">备注</td>
    <td class="tdR"><textarea name="tel" rows="3" class="W100_" id="tel"><?php echo $tel;?></textarea></td>
    </tr>

    </table>
    <input type="hidden" name="fid" value="<?php echo $fid;?>" />
    <?php if ($submitok == 'mod'){?>
      <input name="submitok" type="hidden" value="mod_update" />
      <input name="id" type="hidden" value="<?php echo $id;?>" />
    <?php }else{ ?>
      <input name="submitok" type="hidden" value="add_update" />
    <?php }?>      
    <div class="savebtnbox"><button type="submit" class="btn size3 HUANG3">确认并保存</button></div>
    </form>
<?php exit;}?>

    <table class="table0 W95_ Mtop10">
        <tr>
        <td align="left" class="S14">
          <form name="www.yzlove.com.v6.0..QQ7144100" method="get" action="<?php echo SELF; ?>">
            <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按UID/姓名/手机查询">
            <input type="hidden" name="fid" value="<?php echo $fid;?>" />
            <input type="submit" value="搜索" class="btn size2 QING" />
          </form>   
        </td>
        <td align="left" class="S14">
        <?php if ($rmb_n == 0 && $rmb_r==0){?>
        <font class="C090">此活动免费</font>
        <?php }else{ ?>
		男：<?php echo ($rmb_n>0)?$rmb_n.'元':'<font class="C090">免费</font>';?>　　女：<?php echo ($rmb_r>0)?$rmb_r.'元':'<font class="C090">免费</font>';?>
		<?php }?>
        
        
        </td>
        <td align="right" class="S14">
        <a href="party_user.php?submitok=excel&fid=<?php echo $fid;?>&partytitle=<?php echo urlencode($partytitle);?>" class="btn size2 QING" /><i class="ico2">&#xe63b;</i>导出报名资料</a>　
        <a href="party_user.php?submitok=add&fid=<?php echo $fid;?>" class="btn size2 QING" /><i class="ico add">&#xe620;</i>手动增加报名</a>
        
        </td>
        </tr>
    </table>
<div class="clear"></div>
<form id="www_zeai_cn_FORM">
    <table class="tablelist W95_ Mtop10 Mbottom50">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60">报名会员</th>
    <th width="150" align="left"></th>
    <th width="60" align="left">缴费</th>
    <th width="70" align="left">报名时间</th>
    <th width="15" align="left">&nbsp;</th>
    <th>手机/微信</th>
    <th width="60">出生年份</th>
    <th width="70" align="center">头像</th>
    <th width="40" align="center">删除</th>
    </tr>
    <?php
    for($i=1;$i<=$pagesize;$i++) {
        $rows = $db->fetch_array($rt,'name');
        if(!$rows) break;
        $id       = $rows['id'];
        $uid      = intval($rows['uid']);
        $truename = dataIO($rows['truename'],'out');
        $birthday = dataIO($rows['birthday'],'out');
        $sex      = $rows['sex'];
        $mob      = dataIO($rows['mob'],'out');
        $weixin      = dataIO($rows['weixin'],'out');
        $photo_ifshow = $rows['photo_ifshow'];
		$photo_s = '';
		if(ifint($uid)){
			$row = $db->ROW(__TBL_USER__,"truename,nickname,sex,grade,photo_s","id=".$uid);
			if ($row){
				$truename = dataIO($row['truename'],'out');
				$nickname = dataIO($row['nickname'],'out');
				$nickname = (empty($nickname))?'':'<br>'.$nickname;
				$sex      = $row['sex'];
				$grade    = $row['grade'];
				$photo_s  = $row['photo_s'];
			}
		}
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$ifpay    = $rows['ifpay'];
		$addtime  = YmdHis($rows['addtime']);
		$href     = (ifint($uid))?Href('u',$uid):'javascript:;';
    ?>
	<tr id="tr<?php echo $id;?>">
    <td width="20" height="30" align="left"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
    <td width="60" height="30" align="left"><a href="<?php echo $href;?>" class="pic50 yuan border0" target="_blank"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></td>
    <td width="150" height="30" align="left"><a href="<?php echo $href;?>" target="_blank" class="S14"><?php echo (ifint($uid))?uicon($sex.$grade):''; ?> <?php echo $truename; ?><br><font class="S12 C999"><?php echo (ifint($uid))?'UID：'.$uid.$nickname:'游客';?></font></a></td>
    <td width="60" align="left" class="C999"><?php
	$sexp=($sex==2)?$rmb_r:$rmb_n;
	if($sexp>0){
		echo ($ifpay==1)?'<font class="C090">已缴</font>':'<font class="Cf00">未缴</font>';
	}else{
		echo '<font class="C090">免费</font>';
	}?></td>
    <td width="70" align="left" class="C999"><?php echo $addtime;?></td>
    <td width="15" align="left" class="C999">&nbsp;</td>
    <td height="30" align="left" class="lineH150 S14"><i class="ico C999 S16">&#xe627;</i> <?php echo $mob;echo (!empty($weixin)?'<br><i class="ico C999">&#xe607;</i> '.$weixin:'');?></td>
    <td width="60" height="30" align="left" class="lineH150 S14"><?php echo (!empty($birthday))?$birthday.'<br>'.getage($birthday.'-01-15').'岁':'';?></td>
    <td width="70" align="center">
    <?php if ($uid>0){?><input type="checkbox" id="photo_ifshow<?php echo $id; ?>" class="switch photo_ifshow" fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" value="1"<?php echo ($photo_ifshow == 1)?' checked':'';?>><label for="photo_ifshow<?php echo $id; ?>" class="switch-label"><i></i><b>显示</b><b>隐藏</b></label><?php }?>
    </td>
    <td width="40" align="center" class="lineH200"><a title="修改" fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" uid="<?php echo $uid; ?>" class="editico" title2="<?php echo urlencode(strip_tags($nickname));?>"></a><br><a title="删除" fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" class="delico" title2="<?php echo urlencode(strip_tags($nickname));?>"></a></td>
    </tr>
    <?php } ?>
    <div class="listbottombox">
        <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
        <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);">发送消息</button>
        <input type="hidden" name="submitok" id="submitok" value="" />
        <?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </div>
</table>
</form>
<?php }?>
<br><br><br><br>
<script>
zeai.listEach('.delico',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("clsid")),
		fid = parseInt(obj.getAttribute("fid")),
		title=obj.getAttribute("title2");
		zeai.confirm('确定真的要删除【'+decodeURIComponent(title)+'】报名记录么？',function(){
			zeai.ajax({url:'party_user'+zeai.ajxext+'submitok=ajax_del&fid='+fid+'&id='+id},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
zeai.listEach('.editico',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("clsid")),
		fid = parseInt(obj.getAttribute("fid")),
		uid = parseInt(obj.getAttribute("uid"));
		zeai.openurl('party_user'+zeai.ajxext+'submitok=mod&fid='+fid+'&id='+id+'&uid='+uid);
	}
});
zeai.listEach('.photo_ifshow',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("clsid")),
		fid = parseInt(obj.getAttribute("fid"));
		var v=(this.checked)?1:0;
		zeai.ajax({url:'party_user'+zeai.ajxext+'submitok=ajax_photo_ifshow&id='+id+'&fid='+fid+'&v='+v},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	}
});
var bg = '<?php echo $_Style['list_bg']; ?>',overbg   = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</body>
</html>

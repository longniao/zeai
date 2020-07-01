<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/udata.php';
if(!in_array('claim',$QXARR))exit(noauth());
$parameter = "agentid=$agentid&p=$p&Skeyword=$Skeyword&Skeyword2=$Skeyword2"; 

if($submitok=='add_update'){
	if(empty($list) || !is_array($list))json_exit($listtips);if(count($list)==0)json_exit(array('flag'=>0,'msg'=>'请选择您要操作的用户'));
	$today = YmdHis(ADDTIME,'Ymd');
	$SQL = getAgentSQL('aloneArea');
	//门店
	if(ifint($session_agentid) && !in_array('crm',$QXARR)){
		$row = $db->ROW(__TBL_CRM_AGENT__,"claimnumday","id=".$session_agentid,"name");
		if ($row){
			$claimnumday = intval($row['claimnumday']);
		}else{json_exit(array('flag'=>0,'msg'=>'门店错误，请重新登录'));}
		if($claimnumday == 0)json_exit(array('flag'=>0,'msg'=>'已关闭门店认领功能，请联系超级管理员'));
	}
	//我
	$row = $db->ROW(__TBL_CRM_HN__,"claimnumday","id=".$session_uid,"name");
	if ($row){$claimnumday_my = intval($row['claimnumday']);}
	if($claimnumday_my == 0)json_exit(array('flag'=>0,'msg'=>'已关闭红娘认领功能，请联系超级管理员'));
	
	foreach($list as $uid){
		if(ifint($session_agentid) && !in_array('crm',$QXARR)){//门店
			$totalnum = $db->COUNT(__TBL_CRM_CLAIM_LIST__,"agentid=".$session_agentid." AND adddate='".$today."' ");
			if($totalnum >= $claimnumday)json_exit(array('flag'=>0,'msg'=>'今天认领已达门店上限 '.$claimnumday." 人，请明天再来认领"));
		}
		
		$totalnum_my = $db->COUNT(__TBL_CRM_CLAIM_LIST__,"agentid=".$session_agentid." AND adddate='".$today."' AND admid=".$session_uid);
		if($totalnum_my >= $claimnumday_my)json_exit(array('flag'=>0,'msg'=>'今天认领已达上限 '.$claimnumday_my." 人，请明天再来认领"));
		
		$row = $db->ROW(__TBL_USER__,"nickname,agentid","admid=0 AND id=".$uid,"name");
		if ($row){
			$nickname = dataIO($row['nickname'],'out');
			$agentid  = intval($row['agentid']);
			//if($agentid==0)$db->query("UPDATE ".__TBL_USER__." SET agentid=$session_agentid,agenttitle='$session_agenttitle' WHERE agentid=0 AND hnid=0 AND hnid2=0 AND id=".$uid.$SQL);
			$SQL2 = ",agentid=$session_agentid,agenttitle='$session_agenttitle',hnid=$session_uid,hnname='$session_truename',hntime=".ADDTIME;
			$db->query("UPDATE ".__TBL_USER__." SET admid=$session_uid,admname='$session_truename',admtime=".ADDTIME.$SQL2." WHERE id=".$uid.$SQL);
			$db->query("INSERT INTO ".__TBL_CRM_CLAIM_LIST__."  (agentid,agenttitle,admid,admname,uid,adddate) VALUES ('$session_agentid','$session_agenttitle','$session_uid','$session_truename',$uid,'$today')");
			AddLog($session_agenttitle.'红娘'.$session_truename.'->认领用户【'.$nickname.'（uid:'.$uid.'）】->成功！');
		}
	}
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}
//$totalnum = $db->COUNT(__TBL_USER__,"agentid=".$session_agentid." AND ( TO_DAYS(from_unixtime(admtime))-TO_DAYS(NOW()) = 0 ) AND admtime>0 AND admid>0 ");

$SQL_   = " flag=1 AND admid=0 AND kind<>4  AND mob<>'' AND mob<>0 AND myinfobfb>10 ";
$SQL    = $SQL_;
$ifmob  = 1;
$ifdata = 1;
$Skeyword = trimm($Skeyword);
$Skeyword2 = trimm($Skeyword2);
if (ifmob($Skeyword)){
	$SQL .= " AND (mob=$Skeyword) ";
}elseif(ifint($Skeyword)){
	$SQL .= " AND (id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL .= " AND ( ( uname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".$Skeyword."%' ) OR ( truename LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}
if(ifint($Skeyword2)){
	$SQL .= " AND (admid=$Skeyword2) ";
}elseif(!empty($Skeyword2)){
	$SQL .= " AND (  admname LIKE '%".$Skeyword2."%' )";
}
//if (ifint($ifmob))$SQL  .= " AND mob<>'' AND mob<>0 ";
//if (ifint($ifdata))$SQL .= " AND myinfobfb>10 ";
switch ($claimflag) {
	case 1:$SQL  .= " AND (".ADDTIME." - regtime) < 259200 ";break;
	case 2:$SQL  .= " AND (".ADDTIME." - regtime) < 604800 ";break;
	case 3:$SQL  .= " AND (".ADDTIME." - regtime) < 2592000 ";break;
}
//地区
$SQL .= getAgentSQL('aloneArea');

$areaid = '';
if (ifint($a1) && ifint($a2) && ifint($a3) && ifint($a4)){
	$areaid = $a1.','.$a2.','.$a3.','.$a4;
}elseif(ifint($a1) && ifint($a2) && ifint($a3)){
	$areaid = $a1.','.$a2.','.$a3;
}elseif(ifint($a1) && ifint($a2)){
	$areaid = $a1.','.$a2;
}elseif(ifint($a1)){
	$areaid = $a1;
}
$areaid2 = '';
if (ifint($h1) && ifint($h2) && ifint($h3) && ifint($h4)){
	$areaid2 = $h1.','.$h2.','.$h3.','.$h4;
}elseif(ifint($h1) && ifint($h2) && ifint($h3)){
	$areaid2 = $h1.','.$h2.','.$h3;
}elseif(ifint($h1) && ifint($h2)){
	$areaid2 = $h1.','.$h2;
}elseif(ifint($h1)){
	$areaid2 = $h1;
}
if (!empty($areaid))$SQL   .= " AND areaid LIKE '%".$areaid."%' ";
if (!empty($areaid2))$SQL  .= " AND area2id LIKE '%".$areaid2."%' ";

/*-------------------------------------------------------*/
switch ($sort) {
	case 'regtime0':$SORT = " ORDER BY regtime,id DESC ";break;
	case 'regtime1':$SORT = " ORDER BY regtime DESC,id DESC ";break;
	case 'endtime0':$SORT = " ORDER BY endtime,id DESC ";break;
	case 'endtime1':$SORT = " ORDER BY endtime DESC,id DESC ";break;
	default:$SORT = " ORDER BY id DESC ";break;
}

$rt = $db->query("SELECT id,sex,grade,nickname,truename,photo_s,regtime,endtime,admid,hnid,hnid2,admname,mob,weixin,qq,areatitle,area2title FROM ".__TBL_USER__." WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
$total_nav=$db->COUNT(__TBL_USER__,$SQL);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.RCW {display:inline-block}
.RCW li{width:80px}
.formline{height:10px}
</style>
<?php
?>
<body>
<div class="navbox">
    <a class="ed">公海用户认领<?php echo '<b>'.$total_nav.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank60"></div>

<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回上一页</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	?>
    
    <table class="tablelist" style="min-width:1000px">
    <tr>
    <td colspan="10" class="searchli">
    
    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>">
	<?php if(empty($session_agentareaid)){?>
        工作地区 <script>LevelMenu4('a1|a2|a3|a4|不限|<?php echo $a1; ?>|<?php echo $a2; ?>|<?php echo $a3; ?>|<?php echo $a4; ?>|areaid|areatitle','class="size2 SW"');</script>
        <div class="formline"></div>
        户籍地区 <script>LevelMenu4('h1|h2|h3|h4|不限|<?php echo $h1; ?>|<?php echo $h2; ?>|<?php echo $h3; ?>|<?php echo $a4; ?>|areaid2|areatitle2','class="size2 SW"');</script>
        <div class="formline"></div>
    <?php }?>
    <span class="textmiddle">按用户　</span> <input name="Skeyword" type="text" maxlength="25" class="input size2 W180" placeholder="输入姓名/昵称/UID/手机" value="<?php echo $Skeyword; ?>">
    
    　<input type="checkbox" name="ifmob" id="ifmob" class="checkskin" value="1"<?php echo ($ifmob == 1)?' checked':''; ?> disabled><label for="ifmob" class="checkskin-label"><i></i><b class="W50 S14">有手机</b></label>
    <input type="checkbox" name="ifdata" id="ifdata" class="checkskin" value="1"<?php echo ($ifdata == 1)?' checked':''; ?> disabled><label for="ifdata" class="checkskin-label"><i></i><b class="W80 S14">资料>10%</b></label>
    <span class="textmiddle">认领状态　</span><script>zeai_cn__CreateFormItem('select','claimflag','<?php echo $claimflag; ?>','class="size2 SW"',[{i:"1",v:"今日未认领"},{i:"2",v:"本周未认领"},{i:"3",v:"本月未认领"}]);</script>　
    
    <button type="submit" class="btn size2 QING picmiddle"><i class="ico">&#xe6c4;</i> 开始筛选</button>　　
    </form>
    
    </td>    
    </tr>
    <?php $sorthref = SELF."?".$parameter."&sort=";?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
    <tr>
    <th width="40"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60" align="left">头像</th>
    <th width="130" align="left">用户UID/昵称/姓名</th>
    <th width="140" align="center">注册时间<div class="sort">
	<a title="按时间升序" href="<?php echo $sorthref."regtime0";?>" <?php echo($sort == 'regtime0')?' class="ed"':''; ?>></a>
	<a title="按时间降序" href="<?php echo $sorthref."regtime1";?>" <?php echo($sort == 'regtime1')?' class="ed"':''; ?>></a>
</div></th>
    <th width="140" align="center">最近时间<div class="sort">
	<a title="按时间升序" href="<?php echo $sorthref."endtime0";?>" <?php echo($sort == 'endtime0')?' class="ed"':''; ?>></a>
	<a title="按时间降序" href="<?php echo $sorthref."endtime1";?>" <?php echo($sort == 'endtime1')?' class="ed"':''; ?>></a>
</div></th>
    <th width="10" align="center">&nbsp;</th>
    <th width="220" align="left">工作地区/户籍地区</th>
    <th align="left">联系方式</th>
    <th width="100" align="center">认领红娘</th>
    <th width="100" align="center">操作</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $id;
		$nickname = dataIO($rows['nickname'],'out');
		$truename = dataIO($rows['truename'],'out');
		$sex     = $rows['sex'];
		$grade   = $rows['grade'];
		$regtime = YmdHis($rows['regtime'],'YmdHi');
		$photo_s = $rows['photo_s'];
		$admid   = $rows['admid'];
		$hnid   = $rows['hnid'];
		$hnid2  = $rows['hnid2'];
		$mob    = dataIO($rows['mob'],'out');
		$weixin = dataIO($rows['weixin'],'out');
		$qq     = dataIO($rows['qq'],'out');
		$admname = dataIO($rows['admname'],'out');
		$endtime = $rows['endtime'];
		$endtime_str  = ($endtime>0)?YmdHis($endtime,'YmdHi'):'';
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}else{
			$photo_s_url = HOST.'/res/photo_s'.$sex.'.png';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}
		$areatitle = $rows['areatitle'];
		$area2title = $rows['area2title'];
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="40" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="60"><a href="javascript:;" class="qianxian" uid="<?php echo $uid;?>" title2="<?php echo urlencode(trimhtml($nickname.'　｜　'.$uid));?>"><?php echo $photo_s_str; ?></a></td>
      <td width="130" align="left" class="lineH150">
        
        <a href="javascript:;" class="qianxian" uid="<?php echo $uid;?>" title2="<?php echo urlencode(trimhtml($nickname.'　｜　'.$uid));?>">
        <?php echo uicon($sex.$grade) ?><?php echo '<font class="S14 picmiddle">'.$uid.'</font></br>';?></a>
        <font class="uleft"><?php if(!empty($nickname))echo $nickname.'</br>';if(!empty($truename))echo $truename;?></font>
        
      </td>
      <td width="140" height="60" align="center" class=" C666">
      <h5><i class="ico">&#xe634;</i> <?php echo $regtime;?></h5>
      </td>
      <td width="140" height="60" align="center" class="C666"><h5><i class="ico">&#xe634;</i> <?php echo $endtime_str;?></h5></td>
      <td width="10" height="60" align="center" class="C666">&nbsp;</td>
      <td width="220" height="60" align="left" class="lineH200"><div><?php echo (!empty($areatitle))?'<font class="C999">地区：</font>'.$areatitle:''; ?></div><div><?php echo (!empty($area2title))?'<font class="C999">户籍：</font>'.$area2title:''; ?></div></td>
      <td height="60" align="left" class="lineH200">
      
		<?php 
		if(crm_ifcontact($agentid,$admid,$hnid,$hnid2)){?>
            <font class="C999">手机：</font><?php echo $mob;?><br>
            <font class="C999">微信：</font><?php echo $weixin;?><br>
		<?php }else{?>
            <font class="C999">手机：</font>认领后可看<br>
            <font class="C999">微信：</font>认领后可看<br>
        <?php }?>
      
      </td>
      <td width="100" align="center" class="lineH150"><?php if(ifint($admid)){echo $admname.'<br><font class="C999">ID:'.$admid.'</font>';}else{echo '<font class="Cf60">还未认领~~</font>';}?></td>
        <td width="100" align="center" >
        <?php 
		if(ifint($admid)){$btncls='BAI cancel';$btnstr='取消认领';}else{$btncls='add';$btnstr='我要认领';}
		if($admid==$session_uid || $admid==0){
			?>
			<button type="button"  class="btn size2 <?php echo $btncls;?> " uid="<?php echo $uid;?>"><?php echo $btnstr;?></button><?php
		}else{
			if(@in_array('crm',$QXARR) || @in_array('claim_force_cancel',$QXARR)){?>
                <button type="button"  class="btn size2 HEI2 cancel" uid="<?php echo $uid;?>">强制取消</button>
        <?php }}?>
        </td>
      </tr>
	<?php } ?>
    <div class="listbottombox">
        <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
        <input type="hidden" name="submitok" id="submitok" value="" />
        <button type="button" id="btnadd" class="btn size2 disabled action">批量认领</button>　
        <?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
    </div>
    </form>
</table>

<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>',addtips='<b class="S16">确定认领此用户么？</b><br>1．认领后此用户主页展示您的二维码，其他用户联系此用户将会加您微信，将自动更新为您所在门店<br>2．管理员或红娘并且“<b>网站显示</b>”功能打开后用户主页会展示您的信息<br>3．认领后请尽快跟进此用户并提供服务（将自动将您设为【售前红娘】无需主管分配），财务将对认领后的红娘进行业绩考核<br>4．如要取消认领请到【认领管理】->取消认领';
zeai.listEach('.add',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("uid"));
		zeai.confirm(addtips,function(){
			zeai.ajax({url:'crm_claim_public'+zeai.ajxext+'submitok=add_update&list[]='+id},function(e){
				rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg,{time:3});
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
o('btnadd').onclick = function() {
	allList({
		btnobj:this,
		url:'crm_claim_public'+zeai.ajxext+'submitok=add_update',
		title:'批量认领',
		msg:'正在处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}

zeai.listEach('.qianxian',function(obj){
	obj.onclick = function(){var uid = parseInt(obj.getAttribute("uid")),title2 = obj.getAttribute("title2");zeai.iframe('【'+decodeURIComponent(title2)+'】个人主页','crm_user_detail.php?t=2&iframenav=1&uid='+uid);}
});
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
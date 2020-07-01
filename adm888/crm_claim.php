<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/udata.php';
if(!in_array('claim',$QXARR))exit(noauth());
$bbs_intentionARR = json_decode($_CRM['bbs_intention'],true);
$parameter = "claimflag=$claimflag&agentid=$agentid&p=$p&Skeyword=$Skeyword&Skeyword2=$Skeyword2"; 
if($submitok=='add_update'){
/*	$listtips=array('flag'=>0,'msg'=>'请选择您要操作的用户');
	if(empty($list) || !is_array($list))json_exit($listtips);if(count($list)==0)json_exit($listtips);
	//$SQL1 = getAgentSQL();
	foreach($list as $uid){
		$row = $db->ROW(__TBL_USER__,"nickname,agentid","admid=0 AND id=".$uid,"name");
		if ($row){
			$nickname = dataIO($row['nickname'],'out');
			$agentid  = intval($row['agentid']);
			if($agentid==0)$db->query("UPDATE ".__TBL_USER__." SET agentid=$session_agentid,agenttitle='$session_agenttitle' WHERE agentid=0 AND hnid=0 AND hnid2=0 AND id=".$uid);
			$db->query("UPDATE ".__TBL_USER__." SET admid=$session_uid,admname='$session_truename',admtime=".ADDTIME." WHERE id=".$uid);
			AddLog('【用户认领】->用户【'.$nickname.'（uid:'.$uid.'）】->【认领】成功！');
		}
	}
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
*/
}elseif($submitok=='cancel_update'){
	$listtips=array('flag'=>0,'msg'=>'请选择您要操作的用户');
	if(empty($list) || !is_array($list))json_exit($listtips);if(count($list)==0)json_exit($listtips);
	$SQL1 = getAgentSQL();
	foreach($list as $uid){
		$row = $db->ROW(__TBL_USER__,"nickname,hnid,hnid2","admid>0 AND id=".$uid.$SQL1,"name");
		if ($row){
			$nickname = dataIO($row['nickname'],'out');
			$hnid  = intval($row['hnid']);
			$hnid2 = intval($row['hnid2']);
			if(@in_array('crm',$QXARR) || @in_array('claim_force_cancel',$QXARR)){
				$SQL1="";
			}else{
				$SQL1.=" AND admid=$session_uid ";
			}
			$db->query("UPDATE ".__TBL_USER__." SET admid=0,admname='',admtime=0,hnid=0,hnname='',hntime=0,agentid=0,agenttitle='' WHERE 1=1 ".$SQL1." AND id=".$uid);
			//if($hnid==0 && $hnid2==0){
			//	$db->query("UPDATE ".__TBL_USER__." SET agentid=0,agenttitle='' WHERE agentid>0 ".$SQL1." AND id=".$uid);
			//}
			AddLog('【用户认领】->用户【'.$nickname.'（uid:'.$uid.'）】->【取消认领】成功！');
		}
	}	
	json_exit(array('flag'=>1,'msg'=>'操作成功'));	
}

$SQL    = " admid>0 AND kind<>4 ";
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
if (ifint($ifmob))$SQL  .= " AND mob<>'' AND mob<>0 ";
if (ifint($ifdata))$SQL .= " AND myinfobfb>10 ";

switch ($claimflag) {
	case 1:$SQL  .= " AND (".ADDTIME." - admtime) < 259200 ";break;
	case 2:$SQL  .= " AND (".ADDTIME." - admtime) < 604800 ";break;
	case 3:$SQL  .= " AND (".ADDTIME." - admtime) < 2592000 ";break;
	case 4:$SQL .= " AND admid=$session_uid";break;
}
/*-------------------------------------------------------*/
//门店+地区
$SQL .= getAgentSQL();
//超管可以按门店搜索
if(ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND agentid=$agentid";
/*-------------------------------------------------------*/
switch ($sort) {
	case 'regtime0':$SORT = " ORDER BY regtime,id DESC ";break;
	case 'regtime1':$SORT = " ORDER BY regtime DESC,id DESC ";break;
	case 'endtime0':$SORT = " ORDER BY endtime,id DESC ";break;
	case 'endtime1':$SORT = " ORDER BY endtime DESC,id DESC ";break;
	default:$SORT = " ORDER BY admtime DESC,id DESC ";break;
}
$rt = $db->query("SELECT id,sex,grade,nickname,truename,photo_s,regtime,endtime,agentid,agenttitle,admid,hnid,hnid2,admname,admtime,mob,weixin,qq FROM ".__TBL_USER__." WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
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
<script src="<?php echo HOST;?>/res/select3.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">

<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
.RCW {display:inline-block}
.RCW li{width:80px}
</style>
<?php
?>
<body>
<div class="navbox">
    <a class="ed">认领管理<?php echo '<b>'.$total_nav.'</b>';?></a>
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
    <td colspan="9" class="searchli">

    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>">
        <!--按门店查询-->
        <?php if(in_array('crm',$QXARR)){
            $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {
                ?>
                <div class="FL S14" style="margin-right:20px">
                <span class="textmiddle">按门店</span>
                <select name="agentid" class="W150 size2">
                <option value="">全部门店</option>
                <?php
                    for($j=0;$j<$total2;$j++) {
                        $rows2 = $db->fetch_array($rt2,'num');
                        if(!$rows2) break;
                        $clss=($agentid==$rows2[0])?' selected':'';
                        ?>
                        <option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[1],'out');?></option>
                        <?php
                    }
                ?>
                </select>
                </div>
                <?php
            }
        }
        ?>
    	<span class="textmiddle">按用户</span> <input name="Skeyword" type="text" maxlength="25" class="input size2 W180" placeholder="输入姓名/昵称/UID/手机" value="<?php echo $Skeyword; ?>">　
        <input type="checkbox" name="ifdata" id="ifdata" class="checkskin" value="1"<?php echo ($ifdata == 1)?' checked':''; ?> disabled><label for="ifdata" class="checkskin-label"><i></i><b class="W80 S14">资料>10%</b></label><input type="checkbox" name="ifmob" id="ifmob" class="checkskin" value="1"<?php echo ($ifmob == 1)?' checked':''; ?> disabled><label for="ifmob" class="checkskin-label"><i></i><b class="W50 S14">有手机</b></label>
        <div class="br"></div>
<span class="textmiddle">按红娘</span> <input name="Skeyword2" type="text" maxlength="25" class="input size2 W150" placeholder="按认领红娘ID/姓名" value="<?php echo $Skeyword2; ?>">
<span class="textmiddle">　认领状态</span> <script>zeai_cn__CreateFormItem('select','claimflag','<?php echo $claimflag; ?>','class="size2 SW"',[{i:"1",v:"今日认领"},{i:"2",v:"本周认领"},{i:"3",v:"本月认领"},{i:"4",v:"我的认领"}]);</script>　
        <button type="submit" class="btn size2 QING picmiddle"><i class="ico">&#xe6c4;</i> 开始筛选</button>　　
    </form>


    </td>
    </tr>
    
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
	<?php $sorthref = SELF."?".$parameter."&sort=";?>

    <tr>
    <th width="40"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60" align="center">头像</th>
    <th width="130" align="left">用户UID/昵称/姓名</th>
    <th width="140" align="center">注册时间<div class="sort">
	<a title="按时间升序" href="<?php echo $sorthref."regtime0";?>" <?php echo($sort == 'regtime0')?' class="ed"':''; ?>></a>
	<a title="按时间降序" href="<?php echo $sorthref."regtime1";?>" <?php echo($sort == 'regtime1')?' class="ed"':''; ?>></a>
</div></th>
    <th width="180" align="center">最近时间<div class="sort">
	<a title="按时间升序" href="<?php echo $sorthref."endtime0";?>" <?php echo($sort == 'endtime0')?' class="ed"':''; ?>></a>
	<a title="按时间降序" href="<?php echo $sorthref."endtime1";?>" <?php echo($sort == 'endtime1')?' class="ed"':''; ?>></a>
</div></th>
    <th align="left">联系方式</th>
    <th width="100" align="center">所属门店</th>
    <th width="140" align="center">认领红娘/认领时间</th>
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
		$mob    = dataIO($rows['mob'],'out');
		$weixin = dataIO($rows['weixin'],'out');
		$qq     = dataIO($rows['qq'],'out');
		$admname = dataIO($rows['admname'],'out');
		$endtime = $rows['endtime'];
		$endtime_str  = ($endtime>0)?YmdHis($endtime,'YmdHi'):'';

		$agentid = intval($rows['agentid']);
		$admid   = intval($rows['admid']);
		$admtime = intval($rows['admtime']);
		$hnid   = intval($rows['hnid']);
		$hnid2  = intval($rows['hnid2']);

		$agenttitle = dataIO($rows['agenttitle'],'out');
		$agenttitle = (!empty($agenttitle))?$agenttitle.'<br>':'';
		
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}else{
			$photo_s_url = HOST.'/res/photo_s'.$sex.'.png';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}
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
      <td width="180" height="60" align="center" class="C666"><h5><i class="ico">&#xe634;</i> <?php echo $endtime_str;?></h5></td>
      <td height="60" align="left" class="C666 lineH200">
      
		<?php 
		if(crm_ifcontact($agentid,$admid,$hnid,$hnid2)){?>
            <font class="C999">手机：</font><?php echo $mob;?><br>
            <font class="C999">微信：</font><?php echo $weixin;?><br>
		<?php }else{?>
            <font class="C999">手机：</font>*****<br>
            <font class="C999">微信：</font>*****<br>
        <?php }?>
      
      </td>
      <td width="100" align="center" class="lineH150"><?php echo $agenttitle;;?></td>
      <td width="140" align="center" class="lineH150"><?php
	if(ifint($admid)){
		echo $admname.'<font class="C999">（ID:'.$admid.'）</font>';
		if($admtime>0){
			echo '<br><font class="C999">'.YmdHis($admtime,'YmdHi').'</font>';
		}
	}else{
		echo '<font class="Cf60">还未认领~~</font>';
	}?></td>
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
      <button type="button" id="btncancel" value="" class="btn size2 HEI2 disabled action" onClick="sendTipFn2(this);">批量取消认领</button>
	  <?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
    </div>
    </form>
</table>

<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
//var addtips='<b class="S16">确定认领此客户么？</b><br>1．认领后此客户前台个人主页展示您的二维码，其他用户联系此客户将会加您微信，如果此客户没有门店归属将自动更新为您所在门店<br>2．管理员或红娘并且“<b>网站显示</b>”功能打开后前台客户个人主页会展示您<br>3．认领后请尽快跟进此客户并提供服务，财务将对认领后的红娘进行业绩考核',
canceltips='<b class="S16">确定取消认领么？</b><br>取消认领后此客户，门店归属将置空，售前置空，归为公海！<br><br><font class="Cf00">注：取消认领后，门店【每天认领人数】不退，请慎重操作！</font>';
zeai.listEach('.cancel',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("uid"));
		zeai.confirm(canceltips,function(){
			zeai.ajax({url:'crm_claim'+zeai.ajxext+'submitok=cancel_update&list[]='+id},function(e){
				rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
o('btncancel').onclick = function() {
	allList({
		btnobj:this,
		url:'crm_claim'+zeai.ajxext+'submitok=cancel_update',
		title:'批量取消认领',
		msg:'正在处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}

zeai.listEach('.qianxian',function(obj){
	var uid = parseInt(obj.getAttribute("uid"));
	var title2 = obj.getAttribute("title2");
	obj.onclick = function(){zeai.iframe('【'+decodeURIComponent(title2)+'】个人主页','crm_user_detail.php?t=2&iframenav=1&uid='+uid);}
});


</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<script src="laydate/laydate.js?<?php echo $_ZEAI['cache_str'];?>"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem:'#sDATE1'});
laydate.render({elem:'#sDATE2'});
</script>
<br><br><br>
<?php require_once 'bottomadm.php';?>
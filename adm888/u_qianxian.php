<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_crm.php';
if(!in_array('u_qianxian',$QXARR))exit(noauth());
if($submitok == "qx_bz_update"){
	$id=intval($id);
	if($flag!=2 && $flag!=-1)json_exit(array('flag'=>0,'msg'=>'zeaierror:flag'));
	$bz = dataIO(TrimEnter($content),'in');
	if (str_len($bz) > 500)json_exit(array('flag'=>0,'msg'=>'字太多不要超过500字节'));
	$row = $db->ROW(__TBL_QIANXIAN__,"uid,senduid,flag,bz","id=".$id,'num');
	if($row){
		$uid= $row[0];$senduid= $row[1];$flagoldstr= trimhtml(crm_qxflag_title($row[2]));$bzold= trimhtml($row[3]);
	}else{
		json_exit(array('flag'=>0,'msg'=>'您要备注的信息不存在'));
	}
	$db->query("UPDATE ".__TBL_QIANXIAN__." SET bz='$bz',flag=$flag WHERE flag!=2 AND id=".$id);
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$senduid,'num');$sendnickname= $row2[0];
	$uid_ = $uid;$uid=$senduid;
	AddLog('【牵线管理】操作牵线【'.$sendnickname.'（uid:'.$senduid.'）】->【'.$nickname.'（uid:'.$uid_.'）】原状态：'.$flagoldstr.'->新状态：'.trimhtml(crm_qxflag_title($flag)).'；原备注：'.$bzold.'->新备注：'.$bz);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
	exit;
}elseif($submitok == "alldel"){
	if(!in_array('u_qianxian_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【牵线】删除权限'));
	$tmeplist = $list;
	if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
	if(!is_array($tmeplist))exit(JSON_ERROR);
	if(count($tmeplist)>=1){
		foreach($tmeplist as $value){$v=intval($value);
			$row2 = $db->ROW(__TBL_QIANXIAN__,"senduid","id=".$v,'num');$uid= $row2[0];
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
			AddLog('删除牵线->【'.$nickname.'（uid:'.$uid.'）】');
			$db->query("DELETE FROM ".__TBL_QIANXIAN__." WHERE id=".$v);
		}
	}
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}

if(!in_array('crm',$QXARR)){
	$SQL = ($k=='crmqx')?"  (b.flag=1 OR b.flag=-2) AND b.kind<>4 AND b.admid>0 AND b.crm_ugrade>0 ":" (b.flag=1 OR b.flag=-2) AND b.kind<>4 ";
}else{
	$SQL = "(b.flag=1 OR b.flag=-2) AND b.kind<>4";
}
$SQL .= " AND b.crm_flag<>3 ";

//我的
if($ifmy==1)$SQL .= " AND b.hnid2=".$session_uid;

//if(!in_array('crm',$QXARR)){
//	$SQL .= " AND ( b.agentid=$session_agentid ) ";//门店
//}
//非超管门店+地区 
$SQL .= getAgentSQL('b');


$Skeyword = trimm($Skeyword);
$Skeyword2 = trimm($Skeyword2);
if (ifint($Skeyword)){
	$SQL .= " AND (b.id=$Skeyword OR a.uid=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL .= " AND ( ( b.uname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}
if(!empty($Skeyword2)){
	$SQL .= " AND (  a.username LIKE '%".$Skeyword2."%' )";
}
if(!empty($sDATE1)){
	$sDATE1 = strtotime($sDATE1.' 00:00:01');
	$SQL .= " AND ( a.addtime >= '$sDATE1' )";
}
if(!empty($sDATE2)){
	$sDATE2 = strtotime($sDATE2.' 23:59:59');
	$SQL .= " AND ( a.addtime <= '$sDATE2' )";
}
if (ifint($qxflag) || $qxflag==-1)$SQL .= " AND a.flag = $qxflag ";
if (ifint($sendkind))$SQL .= " AND a.sendkind = $sendkind ";
if (ifint($uid)){
	$SQL .= " AND (a.senduid=$uid OR a.uid=$uid) ";
	$_ADM['admPageSize']=8;
}
switch ($sort) {
	default:$SORT = " ORDER BY a.id DESC ";break;
}
$rt = $db->query("SELECT a.*,b.sex,b.grade,b.nickname,b.weixin,b.mob,b.photo_s FROM ".__TBL_QIANXIAN__." a,".__TBL_USER__." b WHERE ".$SQL." AND a.senduid=b.id ".$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<script>
qxflag_ARR=<?php echo $_CRM['qxflag'];?>;
</script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">

<style>

.tablebz{width:90%;margin:20px auto 0 auto}
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
img.photo_s{width:60px;height:60px;display:block;margin:12px auto;border-radius:40px;object-fit:cover;-webkit-object-fit:cover;cursor:zoom-in}
</style>
<body>
<?php if ($submitok == 'qx_bz'){
$id=intval($id);
$row = $db->ROW(__TBL_QIANXIAN__,"bz","id=".$id,'num');
if($row){$bz = dataIO($row[0],'out');}
?>
<form id="GYLform">
<table class=" tablebz">
<tr>
<td class="center"><textarea name="content" rows="5" class="textarea W90_ Mcenter S14" placeholder="内容请控制在500字节以内"><?php echo $bz;?></textarea></td>
</tr>
<tr>
<td height="60" class="center"><button class="btn size3 HUANG3" type="button" id="qxbzbtn">保存</button>
<input type="hidden" name="flag" value="<?php echo $flag;?>" />
<input type="hidden" name="id" value="<?php echo $id;?>" />
<input type="hidden" name="submitok" value="qx_bz_update" />
</td>
</tr>
</table>
</form>
<script>
qxbzbtn.onclick=function(){
	zeai.ajax({url:'u_qianxian'+zeai.extname,form:GYLform},function(e){rs=zeai.jsoneval(e);
		window.parent.zeai.msg(0);window.parent.zeai.msg(rs.msg);
		if(rs.flag==1){setTimeout(function(){window.parent.location.reload(true);},1000);}
	});
}
</script>
<?php
exit;}if (!ifint($uid)){?>
<div class="navbox">
    <a class="ed"><?php echo ($k=='crmqx')?'售后':'';?>牵线管理<?php echo '<b>'.$total.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank60"></div>

<?php }else{echo '<br>';}?>
<?php

if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无牵线记录";
	if (!ifint($uid))echo"<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a>";
	echo "</div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    
    <table class="tablelist">
    <?php if (!ifint($uid)){?>
    <tr>
    <td colspan="12" class="searchli">
    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>" style="margin-right:20px">
    	牵线用户UID
        <input name="Skeyword" type="text" maxlength="25" class="input size2 W100" placeholder="按用户UID" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />　
		红娘用户名
        <input name="Skeyword2" type="text" maxlength="25" class="input size2 W100" placeholder="按红娘用户名" value="<?php echo $Skeyword2; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />　
    	时间
        <input name="sDATE1" id="sDATE1" type="text"  class="input size2 W100" value="<?php echo (empty($sDATE1))?'':$sDATE1; ?>" size="10" maxlength="10" autocomplete="off">
        <b>～</b> 
        <input name="sDATE2" id="sDATE2" type="text"  class="input size2 W100" value="<?php echo (empty($sDATE2))?'':$sDATE2; ?>" size="10" maxlength="10" autocomplete="off">　
        
        <span class="picmiddle">牵线状态</span> <script>zeai_cn__CreateFormItem('select','qxflag','<?php echo $qxflag; ?>','class="size2 picmiddle"',qxflag_ARR);</script>　
        <span class="picmiddle">发起牵线</span> <script>zeai_cn__CreateFormItem('select','sendkind','<?php echo $sendkind; ?>','class="size2 picmiddle"',[{i:"1",v:"会员要求"},{i:"2",v:"红娘主动"}]);</script>
        
		<button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 筛选</button>
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="k" value="<?php echo $k;?>" />
    </form>
    </td>
    </tr>
    <?php }?>
    
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
    <tr>
    <th width="30"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60">&nbsp;</th>
    <th width="130" align="left">牵线用户</th>
    <th width="130" align="center">牵线时间</th>
    <th width="10" align="center">&nbsp;</th>
    <th width="60" align="left">&nbsp;</th>
    <th width="130" align="left">被牵用户</th>
    <th>备注</th>
    <th width="100" align="center">红娘</th>
    <th width="100" align="center">状态</th>
    <th width="80" align="center">发起牵线</th>
    <th width="170" align="center">操作</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $rows['uid'];
		$username = dataIO($rows['username'],'out');
		$sendnickname = dataIO($rows['nickname'],'out');
		$sex     = $rows['sex'];
		$grade   = $rows['grade'];
		$bz      = $rows['bz'];
		$flag    = $rows['flag'];
		$senduid = $rows['senduid'];
		$sendkind = $rows['sendkind'];
		$addtime = YmdHis($rows['addtime'],'YmdHi');
		$photo_s = $rows['photo_s'];
		$mob = dataIO($rows['mob'],'out');
		$weixin = dataIO($rows['weixin'],'out');
		$bz = dataIO($rows['bz'],'out');
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}else{
			$photo_s_url = HOST.'/res/photo_s'.$sex.'.png';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}
		//
		$row2 = $db->ROW(__TBL_USER__,"sex,grade,nickname,photo_s,weixin,mob","id=".$uid,"name");
		if ($row2){
			$sex2      = $row2['sex'];
			$grade2    = $row2['grade'];
			$photo_s2  = $row2['photo_s'];
			$nickname2 = dataIO($row2['nickname'],'out');
			$mob2      = dataIO($row2['mob'],'out');
			$weixin2   = dataIO($row2['weixin'],'out');
			if(!empty($photo_s2)){
				$photo_s_url2 = $_ZEAI['up2'].'/'.$photo_s2;
				$photo_s_str2 = '<img src="'.$photo_s_url2.'" class="photo_s">';
			}else{
				$photo_s_url2 = HOST.'/res/photo_s'.$sex2.'.png';
				$photo_s_str2 = '<img src="'.$photo_s_url2.'" class="photo_s">';
			}
		}
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="30" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="60"><a href="javascript:;" class="qianxian" photo_s_url="<?php echo $photo_s_url;?>" uid="<?php echo $senduid;?>" title2="<?php echo urlencode(trimhtml($sendnickname.'／'.$senduid));?>"><?php echo $photo_s_str; ?></a></td>
      <td width="130" align="left" class="lineH150">
        
        <a href="javascript:;" photo_s_url="<?php echo $photo_s_url;?>" class="qianxian" uid="<?php echo $senduid;?>" title2="<?php echo urlencode(trimhtml($sendnickname.'／'.$senduid));?>">
        <?php echo uicon($sex.$grade) ?><?php echo '<font class="S14 picmiddle">'.$senduid.'</font></br>';?></a>
        <font class="uleft">
        <?php
		  if(!empty($sendnickname))echo $sendnickname."</br>";
		  if(!empty($mob))echo $mob."</br>";
		  if(!empty($weixin))echo $weixin."</br>";
		  ?>
        </font>
        
      </td>
      <td width="130" height="60" align="center" class="lineH200 C666">
      <h5><?php echo $addtime;?></h5>
      <i class="ico S24 Cccc">&#xe62d;</i>
      </td>
      <td width="10" height="60" align="center" class="lineH200 C666">&nbsp;</td>
      <td width="60" align="left"><a href="javascript:;" class="qianxian" photo_s_url="<?php echo $photo_s_url2;?>" uid="<?php echo $uid;?>" title2="<?php echo urlencode(trimhtml($nickname2.'／'.$uid));?>"><?php echo $photo_s_str2; ?></a></td>
      <td width="130" align="left" class="lineH150">
        
        <a href="javascript:;" class="qianxian" photo_s_url="<?php echo $photo_s_url2;?>" uid="<?php echo $uid;?>" title2="<?php echo urlencode(trimhtml($nickname2.'／'.$uid));?>">
        <?php echo uicon($sex2.$grade2) ?><?php echo '<font class="S14 picmiddle">'.$uid.'</font></br>';?></a>
        <font class="uleft">
        <?php
		  if(!empty($nickname2))echo $nickname2."</br>";
		  if(!empty($mob2))echo $mob2."</br>";
		  if(!empty($weixin2))echo $weixin2."</br>";
		  ?>
        </font>

        </td>
        <td><?php echo $bz;?></td>
        <td width="100" align="center"><?php echo $username;?></td>
        <td width="100" align="center"><?php echo crm_qxflag_title($flag);?></td>
        <td width="80" align="center"><?php echo ($sendkind == 1)?'会员要求':'红娘主动';?></td>
        <td width="170" align="center" >
		<?php 
        if($flag == 2){
			$btncls1='disabled';
			$btncls2='disabled';
		}else{
			$btncls1='LV';
			$btncls2='BAI';
		}
        ?>
        <button type="button" class="btn size2 <?php echo $btncls1;?> flag" flag="2" qxid="<?php echo $id;?>" title2="<?php echo urlencode(trimhtml($sendnickname.' ｜ '.$senduid));?>"><?php echo trimhtml(crm_qxflag_title(2));?></button>　
        <button type="button"  class="btn size2 <?php echo $btncls2;?> flag" flag="-1" qxid="<?php echo $id;?>" title2="<?php echo urlencode(trimhtml($sendnickname.' ｜ '.$senduid));?>"><?php echo trimhtml(crm_qxflag_title(-1));?></button>
        </td>
      </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="12">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　

    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
    </form>
</table>

<script>
var bg  = '<?php echo $_Style['list_bg']; ?>',
overbg   = '<?php echo $_Style['list_overbg']; ?>',
selectbg = '<?php echo $_Style['list_selectbg']; ?>';
var k='<?php echo $k;?>';
zeai.listEach('.qianxian',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid"));
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		urlpre = 'crm_user_detail.php?t=2&iframenav=1&uid='+uid;
		if(k=='crmqx'){
			urlpre_qx = 'crm_select.php?k=qx&uid='+uid+'&t=';
		}else{
			urlpre_qx = 'crm_user_detail_select.php?uid='+uid+'&iframenav=1&t=';
		}

		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】牵线配对'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'\',this);" class="ed"><i class="ico add">&#xe7a0;</i> 个人主页</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre_qx+'3\',this);"><i class="ico add">&#xe64b;</i> 按择偶要求</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre_qx+'4\',this);"><i class="ico add">&#xe6c4;</i> 按搜索条件</a>'+
		'</div>',urlpre);
	}
});

zeai.listEach('.flag',function(obj){
	if(!obj.hasClass('disabled'))obj.onclick = function(){
		var id = parseInt(obj.getAttribute("qxid")),
		flag = parseInt(obj.getAttribute("flag")),
		title2 = obj.getAttribute("title2");
		str = (flag==2)?'<b class="S16">确定牵线成功？</b><br>牵线成功将在【手机端滚动公告】展示牵线成功信息，如：xxxx和xxxx牵线成功！':'<b class="S16">确定牵线失败？</b>';
		zeai.confirm(str,function(){
			zeai.iframe('【'+title2+'】牵线备注','u_qianxian.php?submitok=qx_bz&flag='+flag+'&id='+id,400,250);
		});
	}
});

o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'u_qianxian'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}

</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<?php }?>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem: '#sDATE1'});
laydate.render({elem: '#sDATE2'});
</script>
<br><br><br>
<?php require_once 'bottomadm.php';?>
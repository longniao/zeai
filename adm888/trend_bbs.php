<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
if(!in_array('trend',$QXARR))exit(noauth());
if($submitok=='ajax_del'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在或已被删除'));
	$db->query("DELETE FROM ".__TBL_TREND_BBS__." WHERE id=".$id);
	AddLog('【交友圈审核】评论删除->评论id:'.$id);
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok=='delupdate'){
	if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
	if(!is_array($list))exit(JSON_ERROR);
	if(count($list)>=1){
		foreach($list as $id){
			$db->query("DELETE FROM ".__TBL_TREND_BBS__." WHERE id=".$id);
			AddLog('【交友圈审核】评论删除->评论id:'.$id);
		}
	}
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok=='dataflag1'){
	if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
	if(!is_array($list))exit(JSON_ERROR);
	if(count($list)>=1){
		foreach($list as $id){
			$id=intval($id);
			$db->query("UPDATE ".__TBL_TREND_BBS__." SET flag=1 WHERE id=".$id);
			AddLog('【交友圈审核】评论审核通过->评论id:'.$id);
		}
	}
	json_exit(array('flag'=>1,'msg'=>'审核成功'));
	
}elseif($submitok=='modflag'){
	if (!ifint($classid))callmsg("forbidden","-1");
	$rt = $db->query("SELECT flag FROM ".__TBL_TREND_BBS__." WHERE id=".$classid);
	if($db->num_rows($rt)){
		$rows = $db->fetch_array($rt,'name');
		$flag = $rows['flag'];
		switch($flag){
			case"-1":$SQL="flag=1";break;
			case"0":$SQL="flag=1";break;
			case"1":$SQL="flag=-1";break;
		}
		$db->query("UPDATE ".__TBL_TREND_BBS__." SET ".$SQL." WHERE id=".$classid);
		AddLog('【交友圈审核】评论状态修改->评论id:'.$id);
		header("Location: ".SELF."?p=$p");
	}else{
		callmsg("您要操作的信息不存在或已经删除！","-1");
	}
}elseif($submitok=='mod'){
	if (!ifint($id))alert("id参数错误","-1");
	$rt = $db->query("SELECT content FROM ".__TBL_TREND_BBS__." WHERE id=".$id);
	if($db->num_rows($rt)){
		$row     = $db->fetch_array($rt,'name');
		$content = dataIO($row['content'],'out');
	}else{
		alert_adm("该评论不存在！","-1");
	}
}elseif($submitok=='mod_update'){
	if (!ifint($id))alert_adm("id参数错误","-1");
	$content = dataIO($content,'in',10000);
	$db->query("UPDATE ".__TBL_TREND_BBS__." SET content='$content' WHERE id=".$id);
	AddLog('【交友圈审核】评论内容修改->评论id:'.$id);
	alert_adm("修改成功","trend_bbs.php?fid=".$fid);
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
.table0{min-width:900px;width:98%;margin:10px 20px 20px 20px}

</style>
<body>
<?php
	$SQL = "";
	$Skeyword  = trimm($Skeyword);
	$Skeyword2 = trimm($Skeyword2);
	if (ifint($Skeyword)){
		$SQL .= " AND (U.id=$Skeyword) ";
	}elseif(!empty($Skeyword)){
		$SQL .= " AND ( ( U.uname LIKE '%".$Skeyword."%' ) OR ( U.nickname LIKE '%".$Skeyword."%' ) OR ( U.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
	}
	if(!empty($Skeyword2)){
		$SQL .= " AND ( a.content LIKE '%".$Skeyword2."%' )";
	}	
	$rt = $db->query("SELECT a.*,U.nickname,U.sex,U.grade,U.photo_s FROM ".__TBL_TREND_BBS__." a,".__TBL_USER__." U WHERE a.uid=U.id ".$SQL." ORDER BY a.id DESC");
	$total = $db->num_rows($rt);
	?>
    <div class="navbox">
    <a href="trend.php">交友圈管理</a>
    <a href="trend.php?t=pic">交友圈图片</a>
    <a href="trend_bbs.php" class="ed">交友圈评论<?php echo '<b>'.$total.'</b>';?></a>
    <div class="Rsobox"></div>
    <div class="clear"></div></div>
    <div class="fixedblank"></div>

<?php if($submitok=='mod'){?>
    <form name="ZEAI_FORM" method="POST" action="<?php echo SELF;?>">
    <table class="table Mtop50 W500">
    <tr>
      <td class="tdL">评论内容</td>
      <td class="tdR"><textarea name="content" rows="5" class="textarea W100_" id="content"><?php echo $content;?></textarea></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input class="btn size3" type="submit" value="修改并保存" />
        <input type="hidden" name="fid" value="<?php echo $fid;?>" />
        <input name="submitok" type="hidden" value="mod_update" />
        <input name="id" type="hidden" value="<?php echo $id;?>" />
      </td>
    </tr>
    </table>
    </form>
<?php exit;}?>



<table class="table0  Mtop10">
        <tr>
        <td align="left" class="S14">
          <form name="www.yzlove.com.v6.0..QQ7144100" method="get" action="<?php echo SELF; ?>">
            <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按UID/用户名/昵称搜索">
            <input type="hidden" name="fid" value="<?php echo $fid;?>" />
            <input type="submit" value="搜索" class="btn size2 QING" />
          </form>   
        </td>
        <td align="left" class="S14"></td>
        <td align="right" class="S14">
          <form name="www.yzlove.com.v6.0..QQ797311" method="get" action="<?php echo SELF; ?>">
            <input name="Skeyword2" type="text" id="Skeyword2" maxlength="25" class="input size2 W150" placeholder="按评论内容搜索">
            <input type="hidden" name="fid" value="<?php echo $fid;?>" />
            <input type="submit" value="搜索" class="btn size2 QING" />
          </form>   
        
        </td>
        </tr>
    </table>
<div class="clear"></div>

<?php 
	if ($total <= 0 ) {
		echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
		if (!empty($SQL))echo "<br><br><a class='aQINGed' href='javascript:history.back(-1)'>重新筛选</a>";
		echo "</div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';

?>
    <form id="www_zeai_cn_FORM">

    <table class="tablelist  Mtop10 Mbottom50" >
    <tr>
    <th width="29"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60" align="left">ID</th>
    <th width="60">发表会员</th>
    <th width="170" align="left"></th>
    <th align="left">评论内容</th>
    <th width="20" align="left">&nbsp;</th>
    <th width="80">发表时间</th>
    <th width="80" align="center">状态</th>
    <th width="50" align="center">修改</th>
    <th width="50" align="center">删除</th>
    </tr>
    <?php
    for($i=1;$i<=$pagesize;$i++) {
        $rows = $db->fetch_array($rt,'name');
        if(!$rows) break;
        $id       = $rows['id'];
        $uid      = $rows['uid'];
        $flag     = $rows['flag'];
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = str_replace($Skeyword,"<b class='Cf00'>".$Skeyword."</b>",$nickname);
		$content  = dataIO($rows['content'],'out');
		$content  = str_replace($Skeyword2,"<b class='Cf00'>".$Skeyword2."</b>",$content);
		$sex      = $rows['sex'];
		$grade    = $rows['grade'];
		$photo_s  = $rows['photo_s'];
		$addtime  = YmdHis($rows['addtime']);
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$sexbg       = (empty($photo_s))?' class="sexbg'.$sex.'"':'';
		$href        = Href('u',$uid);
    ?>
    <tr id="tr<?php echo $id;?>">
    <td width="29" height="30" align="left" ><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
    <td width="60" height="30" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
    <td width="60" height="30" align="left" ><a href="<?php echo $href;?>" class="pic50 yuan border0" target="_blank"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></td>
    <td width="170" height="30" align="left"><a href="<?php echo $href;?>" target="_blank" class="S14"><?php echo uicon($sex.$grade) ?> <?php echo $nickname; ?><br><font class="S12 C999">(UID:<?php echo $uid;?>)</font></a></td>
    <td align="left" class="S14" style="word-break:break-all;word-wrap:break-word;"><?php echo $content;?></td>
    <td width="20" align="left" class="C999">&nbsp;</td>
    <td width="80" height="30" align="left" class="C999"><?php echo $addtime;?></td>
    <td width="80" height="30" align="center" class="C999">
    
<?php
$fHREF = SELF."?submitok=modflag&classid=$id&t=$t&p=$p";
if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击显示">隐藏</a><?php }?>
<?php if($flag==0){?><a href="<?php echo $fHREF;?>" class="aHUANG" title="点击审核">未审</a><div class="C999" style="margin-top:6px">点击审核</div><?php }?>
<?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击隐藏">正常</a><?php }?>
    
    </td>
    <td width="50" align="center"><a title="修改" fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" class="editico" title2="<?php echo urlencode(strip_tags($nickname));?>"></a></td>
    <td width="50" align="center"><a title="删除" fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" class="delico"  title2="<?php echo urlencode(strip_tags($nickname));?>"></a></td>
    </tr>
    <?php } ?>
    <tfoot><tr>
    <td colspan="10">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnflaglist" class="btn size2 LV disabled action">批量审核</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);">发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
</form>
<?php }?>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
if(!zeai.empty(o('btndellist')))o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'trend_bbs'+zeai.ajxext+'submitok=delupdate',
		title:'批量删除',
		msg:'批量删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.delico',function(obj){
	var id = parseInt(obj.getAttribute("clsid"));
	var fid = parseInt(obj.getAttribute("fid"));
	var title=obj.getAttribute("title2");
	obj.onclick = function(){
		zeai.confirm('确定删除【'+decodeURIComponent(title)+'】评论么？',function(){
			zeai.ajax({url:'trend_bbs.php?submitok=ajax_del&fid='+fid+'&id='+id},function(e){
				rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
zeai.listEach('.editico',function(obj){
	var id = parseInt(obj.getAttribute("clsid"));
	var fid = parseInt(obj.getAttribute("fid"));
	obj.onclick = function(){
		zeai.openurl('trend_bbs.php?submitok=mod&fid='+fid+'&id='+id);
	}
});
if(!zeai.empty(o('btnflaglist')))o('btnflaglist').onclick = function() {
	allList({
		btnobj:this,
		url:'trend_bbs'+zeai.ajxext+'submitok=dataflag1',
		title:'批量审核',
		content:'',/*<br>此审核将同步批量发送所有粉丝消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。*/
		msg:'审核处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<br><br><br>
<?php require_once 'bottomadm.php';?>
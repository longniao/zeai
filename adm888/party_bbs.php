<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
if(!in_array('party_bbsall',$QXARR))exit(noauth());
if ( !ifint($fid))callmsg("www_zeai_cn_error_fid","-1");
if($submitok=='ajax_del'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在或已被删除'));
	$row2 = $db->ROW(__TBL_PARTY_BBS__,"content","id=".$id,'num');$c= $row2[0];
	AddLog('【交友活动】->删除活动评论【活动id:'.$fid.'】->评论内容：'.$c);
	//
	$db->query("DELETE FROM ".__TBL_PARTY_BBS__." WHERE fid=".$fid." AND id=".$id);
	$db->query("UPDATE ".__TBL_PARTY__." SET bbsnum=bbsnum-1 WHERE bbsnum>0 AND id=".$fid);
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok=='mod'){
	if (!ifint($id))alert("id参数错误","-1");
	$rt = $db->query("SELECT content FROM ".__TBL_PARTY_BBS__." WHERE id=".$id);
	if($db->num_rows($rt)){
		$row     = $db->fetch_array($rt,'name');
		$content = dataIO($row['content'],'out');
	}else{
		alert_adm("该评论不存在！","-1");
	}
}elseif($submitok=='mod_update'){
	if (!ifint($id))alert_adm("id参数错误","-1");
	$content = dataIO($content,'in',10000);
	$db->query("UPDATE ".__TBL_PARTY_BBS__." SET content='$content' WHERE id=".$id);
	AddLog('【交友活动】->修改活动评论【活动id:'.$fid.'】->评论内容：'.$content);
	alert_adm("修改成功","party_bbs.php?fid=".$fid);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">

<style>
a.pic50 img{height:100%;width:100%}
a.pic50{margin:10px auto}
.partyC{word-break:break-all;word-wrap:break-word;}
.partyC img{width:20px}
</style>
<body>
<?php
	$row = $db->ROW(__TBL_PARTY__,"bbsnum","id=".$fid);
	if (!$row)json_exit(array('flag'=>0,'msg'=>'活动不存在或已被删除'));
	$bbsnum = $row[0];
	//
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
	$rt = $db->query("SELECT a.*,U.nickname,U.sex,U.grade,U.photo_s FROM ".__TBL_PARTY_BBS__." a,".__TBL_USER__." U WHERE a.uid=U.id AND a.fid=".$fid.$SQL." ORDER BY a.id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
		if (!empty($SQL))echo "<br><br><a class='aQINGed' href='javascript:history.back(-1)'>重新筛选</a>";
		echo "</div>";
	} else {
		$page_skin = 1;$pagesize=1000;require_once ZEAI.'sub/page.php';
	?>
    <div class="navbox">
    <a href="party_bbs.php?fid=<?php echo $fid;?>" class="ed">活动评论管理<?php echo '<b>'.$total.'</b>';?></a>
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



<table class="table0 W95_ Mtop10">
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
    <table class="tablelist W95_ Mtop10 Mbottom50" >
    <tr>
    <th width="60">发表会员</th>
    <th width="170" align="left"></th>
    <th align="left">评论内容</th>
    <th width="20" align="left">&nbsp;</th>
    <th width="80">发表时间</th>
    <th width="50" align="center">修改</th>
    <th width="50" align="center">删除</th>
    </tr>
    <?php
    for($i=1;$i<=$pagesize;$i++) {
        $rows = $db->fetch_array($rt,'name');
        if(!$rows) break;
        $id       = $rows['id'];
        $uid      = $rows['uid'];
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
    <tr>
    <td width="60" height="30" align="left" ><a href="<?php echo $href;?>" class="pic50 yuan border0" target="_blank"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></td>
    <td width="170" height="30" align="left"><a href="<?php echo $href;?>" target="_blank" class="S14"><?php echo uicon($sex.$grade) ?> <?php echo $nickname; ?><br><font class="S12 C999">(UID:<?php echo $uid;?>)</font></a></td>
    <td align="left" class="C999 partyC"><?php echo $content;?></td>
    <td width="20" align="left" class="C999">&nbsp;</td>
    <td width="80" height="30" align="left" class="C999"><?php echo $addtime;?></td>
    <td width="50" align="center"><a title="修改" fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" class="edit" title2="<?php echo urlencode(strip_tags($nickname));?>">✎</a></td>
    <td width="50" align="center"><a title="删除" fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" class="del"  title2="<?php echo urlencode(strip_tags($nickname));?>">✖</a></td>
    </tr>
    <?php } ?>
    <?php if ($total > $pagesize){?>
    <tfoot>
    <tr>
    <td colspan="7"><?php if ($total > $pagesize)echo '<div class="pagebox">'.$pagelist.'</div>'; ?></div></td>
    </tr>
    </tfoot>
    <?php }?>
</table>
<?php }?>
<script>
zeai.listEach('.del',function(obj){
	var id = parseInt(obj.getAttribute("clsid"));
	var fid = parseInt(obj.getAttribute("fid"));
	var title=obj.getAttribute("title2");
	obj.onclick = function(){
		zeai.confirm('确定删除【'+decodeURIComponent(title)+'】评论么？',function(){
			zeai.ajax({url:'party_bbs.php?submitok=ajax_del&fid='+fid+'&id='+id},function(e){
				rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
zeai.listEach('.edit',function(obj){
	var id = parseInt(obj.getAttribute("clsid"));
	var fid = parseInt(obj.getAttribute("fid"));
	obj.onclick = function(){
		zeai.openurl('party_bbs.php?submitok=mod&fid='+fid+'&id='+id);
	}
});
</script>
</body>
</html>

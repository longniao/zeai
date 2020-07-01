<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
if ( !ifint($fid))callmsg("www_zeai_cn_error_fid","-1");
if($submitok=='ajax_del'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在或已被删除'));
	//
	$row2 = $db->ROW(__TBL_PARTY_SIGN__,"uid","id=".$id,'num');$uid= $row2[0];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	AddLog('【交友活动】活动id:'.$fid.'->删除签到会员->【'.$nickname.'（uid:'.$uid.'）】');
	//
	$db->query("DELETE FROM ".__TBL_PARTY_SIGN__." WHERE fid=".$fid." AND id=".$id);
	$db->query("UPDATE ".__TBL_PARTY__." SET signnum=signnum-1 WHERE signnum>0 AND id=".$fid);
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<meta http-equiv="refresh" content="30">
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">

<style>
a.pic50 img{height:100%;width:100%}
a.pic50{margin:10px auto}
i.ewm{display:inline-block;color:#fff;border-radius:50px;width:20px;height:20px;line-height:18px;text-align:center;font-size:20px;margin:0 0 0 5px;vertical-align:middle}

</style>
<body>
<?php
	$row = $db->ROW(__TBL_PARTY__,"signnum,title","id=".$fid);
	if (!$row)json_exit(array('flag'=>0,'msg'=>'活动不存在或已被删除'));
	$signnum = $row[0];
	$title = dataIO($row[1],'out');
	//
	$SQL = "";
	$Skeyword = trimm($Skeyword);
	if (ifint($Skeyword)){
		$SQL = " AND (U.id=$Skeyword) ";
	}elseif(!empty($Skeyword)){
		$SQL = " AND ( ( U.uname LIKE '%".$Skeyword."%' ) OR ( U.nickname LIKE '%".$Skeyword."%' ) OR ( U.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
	}
	$rt = $db->query("SELECT a.*,U.nickname,U.sex,U.grade,U.photo_s FROM ".__TBL_PARTY_SIGN__." a,".__TBL_PARTY__." b,".__TBL_USER__." U WHERE a.uid=U.id AND a.fid=b.id AND b.id=".$fid.$SQL." ORDER BY a.id");
	$total = $db->num_rows($rt);
	?>
    
    <table class="table0 W95_ Mtop10">
        <tr>
        <td align="left" class="S14">
          <form name="www.yzlove.com.v6.0..QQ7144100" method="get" action="<?php echo SELF; ?>">
            <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按UID/用户名/昵称查询">
            <input type="hidden" name="fid" value="<?php echo $fid;?>" />
            <input type="submit" value="搜索" class="btn size2 QING" />
          </form>   
        </td>
        <td align="left" class="S14"><a class="aHUANGed">已签到 <font class="S16"><?php echo $total;?></font> 人</a></td>
        <td align="right" class="S14"><button type="button" class="btn size2 QING" onClick="signewmFn(<?php echo $fid;?>,'<?php echo urlencode(strip_tags($title));?>')" /><span style="vertical-align:middle">打开签到二维码</span><i class="ico ewm">&#xe611;</i></button></td>
        </tr>
    </table>
	<?php 
	if ($total <= 0 ) {
		echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
		if (!empty($SQL))echo "<br><br><a class='aQINGed' href='javascript:history.back(-1)'>重新筛选</a>";
		echo "</div>";
	} else {
		$page_skin = 1;$pagesize=1000;require_once ZEAI.'sub/page.php';
	?>
    <div class="clear"></div>
    <table class="tablelist W95_ Mtop10 Mbottom50" >
    <tr>
    <th width="60">签到会员</th>
    <th width="170" align="left"></th>
    <th width="80" align="left">名次</th>
    <th>签到时间</th>
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
    <td width="80" align="left" class="C999"><span style="vertical-align:middle">第</span> <font class="Cf00 S24 italic"><?php echo $i;?></font> <span style="vertical-align:middle">位</span></td>
    <td height="30" align="left" class="C999"><?php echo $addtime;?></td>
    <td width="50" align="center"><a fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" class="del" title2="<?php echo urlencode(strip_tags($nickname));?>">✖</a></td>
    </tr>
    <?php } ?>
    <?php if ($total > $pagesize){?>
    <tfoot>
    <tr>
    <td colspan="5"><?php if ($total > $pagesize)echo '<div class="pagebox">'.$pagelist.'</div>'; ?></div></td>
    </tr>
    </tfoot>
    <?php }?>
</table>
<?php }?>
<script>
function signewmFn(fid,title){
	top.zeai.iframe('【'+decodeURIComponent(title)+'】现场签到二维码','party_sign_ewm.php?fid='+fid,500,500);
}
zeai.listEach('.del',function(obj){
	var id = parseInt(obj.getAttribute("clsid"));
	var fid = parseInt(obj.getAttribute("fid"));
	var title=obj.getAttribute("title2");
	obj.onclick = function(){
		zeai.confirm('确定真的要删除【'+decodeURIComponent(title)+'】签到记录么？',function(){
			zeai.ajax({url:'party_sign.php?submitok=ajax_del&fid='+fid+'&id='+id},function(e){
				rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});

</script>
</body>
</html>

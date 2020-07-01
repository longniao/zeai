<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('crm_hn_bbs',$QXARR))exit(noauth());
switch ($submitok) {
	case"modflag":
		if (!ifint($id))alert_adm("forbidden","-1");
		$rt = $db->query("SELECT flag FROM ".__TBL_HN_BBS__." WHERE id=".$id);
		if($db->num_rows($rt)){
			$rows = $db->fetch_array($rt,'name');
			$flag = $rows['flag'];
			switch($flag){
				case"-1":$SQL="flag=1";break;
				case"0":$SQL="flag=1";break;
				case"1":$SQL="flag=-1";break;
			}
			$db->query("UPDATE ".__TBL_HN_BBS__." SET ".$SQL." WHERE id=".$id);
			header("Location: ".SELF."?p=$p");
		}else{
			alert_adm("您要操作的信息不存在或已经删除！","-1");
		}
	break;
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$id=intval($v);
				$row = $db->ROW(__TBL_HN_BBS__,"hid","id=".$id,"num");
				if ($row){
					$hid=$row[0];
					//$db->query("DELETE FROM ".__TBL_HN_BBS__." WHERE id=".$id);
					$db->query("UPDATE ".__TBL_HN_BBS__." SET flag=-1 WHERE id='$v'");
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"mod_update":
		if (!ifint($id))alert_adm("id参数错误","-1");
		if (!ifint($hid))alert_adm("hid参数错误","-1");
		$content = dataIO($content,'in',1000);
		$content2 = dataIO($content2,'in',500);
		$pjkind = intval($pjkind);
		if ($pjkind != 1 && $pjkind != 2 && $pjkind != 3)alert_adm("forbidden4","-1");
		$rt = $db->query("SELECT id FROM ".__TBL_CRM_HN__." WHERE id=".$hid);
		if(!$db->num_rows($rt))alert_adm("forbidden5","-1");
		$db->query("UPDATE ".__TBL_HN_BBS__." SET content='$content',content2='$content2',pjkind='$pjkind' WHERE id=".$id);
		$pj_goodnum = $db->COUNT(__TBL_HN_BBS__,"pjkind=1 AND hid=".$hid);
		$pj_normalnum = $db->COUNT(__TBL_HN_BBS__,"pjkind=2 AND hid=".$hid);
		$pj_badnum = $db->COUNT(__TBL_HN_BBS__,"pjkind=3 AND hid=".$hid);
		$db->query("UPDATE ".__TBL_CRM_HN__." SET pj_good=$pj_goodnum,pj_normal=$pj_normalnum,pj_bad=$pj_badnum WHERE id=".$hid);
		switch ($pjkind){
			case 1:$pjkind_t = '<span class="pjkind k1">好评</span>';break;
			case 2:$pjkind_t = '<span class="pjkind k2">中评</span>';break;
			case 3:$pjkind_t = '<span class="pjkind k3">差评</span>';break;
		}
		$chkflag = 1;
		//alert_adm("修改成功","crm_hn_bbs.php?submitok=mod&id=".$id);
	break;
	case"mod":
		if (!ifint($id))alert_adm("id参数错误","-1");
		$rt = $db->query("SELECT hid,content,content2,pjkind FROM ".__TBL_HN_BBS__." WHERE id=".$id);
		if($db->num_rows($rt)) {
			$row = $db->fetch_array($rt,"name");
			$hid = $row['hid'];
			$pjkind = $row['pjkind'];
			$content = dataIO($row['content'],'out');
			$content2 = dataIO($row['content2'],'out');
		}else{
			alert_adm("该评价不存在！","-1");
		}
	break;
}
require_once ZEAI.'cache/udata.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php if ($chkflag == 1){ ?>
<script>
var bbsid = window.parent.document.getElementById('bbs<?php echo $id; ?>');
bbsid.innerHTML = '<?php echo $pjkind_t.$content; ?>';
bbsid.className = 'Cf00 S14';
var bbsid2 = window.parent.document.getElementById('bbs2<?php echo $id; ?>');
bbsid2.innerHTML = '<div class="hnhf">红娘回复：<?php echo $content2; ?></div>';
bbsid2.className = 'Cf00 S12';
window.parent.zeai.iframe(0);
</script>
<?php exit;}?>

</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">

<style>
.tablelist{margin:20px 20px 50px 20px}
.table0{width:98%;margin:10px 20px 20px 20px}

.tablelist .li{font-size:14px;line-height:200%}
.tablelist .li img{max-width:50px;max-height:50px;margin-left:5px}

.tablelist .li2 li{width:50px;height:50px;float:left;margin:10px;position:relative}
.tablelist .li2 li img{width:50px;height:50px;border-radius:2px;cursor:zoom-in;display:block}

.delico{;top:-10px;right:-10px;position:absolute;width:20px;height:20px;display:inline-block;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3);background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.delico:hover{background-position:-100px top;cursor:pointer}

.tablelist .sx{padding:15px 0 0 15px}


.hnhf{color:#D8271C;font-size:12px}
.zoompic{vertical-align:middle;width:20px;height:20px}
.pjkind{display:inline-block;color:#fff;padding:0 5px;line-height:24px;margin-right:15px;position:relative}
.pjkind:after{display:block;content:'';width:0;height:0;position:absolute;top:0;right:-6px;border-top:12px solid transparent;border-bottom: 12px solid transparent;border-left:6px solid #ff9600}
.k1{background-color:#95C057}.k1:after{border-left-color:#95C057}
.k2{background-color:#e3b26b}.k2:after{border-left-color:#e3b26b}
.k3{background-color:#999}.k3:after{border-left-color:#999}

</style>
<body>
<?php if($submitok=='mod'){?>
    <form name="ZEAI_FORM" method="POST" action="<?php echo SELF;?>">
    <table class="table Mtop30 W500">
    <tr>
      <td class="tdL" style="width:100px">评价内容</td>
      <td class="tdR"><textarea name="content" rows="5" class="textarea W100_" id="content"><?php echo $content;?></textarea></td>
    </tr>
    <tr>
      <td class="tdL">红娘回复</td>
      <td class="tdR"><textarea name="content2" rows="5" class="textarea W100_" id="content2"><?php echo $content2;?></textarea></td>
    </tr>
    <tr>
      <td class="tdL">服务打分</td>
      <td class="tdR">
    <input type="radio" name="pjkind" id="kind1" class="radioskin" value="1"<?php echo ($pjkind == 1)?' checked':''; ?>><label for="kind1" class="radioskin-label"><i class="i1"></i><b class="W50">好评</b></label>
    <input type="radio" name="pjkind" id="kind2" class="radioskin" value="2"<?php echo ($pjkind == 2)?' checked':''; ?>><label for="kind2" class="radioskin-label"><i class="i1"></i><b class="W50">中评</b></label>
    <input type="radio" name="pjkind" id="kind3" class="radioskin" value="3"<?php echo ($pjkind == 3)?' checked':''; ?>><label for="kind3" class="radioskin-label"><i class="i1"></i><b class="W50">差评</b></label>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input name="submitok" type="hidden" value="mod_update" />
        <input name="id" type="hidden" value="<?php echo $id;?>" />
        <input name="hid" type="hidden" value="<?php echo $hid; ?>">
      </td>
    </tr>
    </table>
<br><br><br><br><div class="savebtnbox"><button type="submit"  class="btn size3 HUANG3">修改并保存</button></div>
    </form>
<?php exit;}?>
<?php 
//门店权限
if(!in_array('crm',$QXARR)){
	$SQL=" AND b.agentid=$session_agentid";
}else{$SQL="";}
//门店权限结束
$Skeyword = trimm($Skeyword);
if (!empty($Skeyword)){
	if($t == 3){
		$SQL .= " AND a.content LIKE '%".dataIO($Skeyword,'in')."%' ";
	}elseif($t == 1){
		if (ifint($Skeyword))$SQL .= " AND b.id =".$Skeyword;
	}elseif($t == 2){
		if (ifint($Skeyword)){
			$SQL .= " AND a.hid =".$Skeyword;	
		}
	}
}
$rt = $db->query("SELECT a.*,b.nickname,b.sex,b.grade FROM ".__TBL_HN_BBS__." a,".__TBL_USER__." b WHERE a.uid=b.id ".$SQL." ORDER BY a.id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);



?>
<div class="navbox">
    <a href="crm_hn_bbs.php" class="ed">红娘评价管理<?php echo '<b>'.$total.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>


<table class="table0">
    <tr>
    <td width="260" align="left" class="S14">
    
    <form name="form1" method="get" action="<?php echo SELF; ?>" style="float:left;margin-right:30px">
      <input name="Skeyword" type="text" id="Skeyword" size="15" maxlength="8" class="input size2" placeholder="按会员UID搜索">
      <input type="submit" value="搜索" class="btn size2" />
      <input type="hidden" name="t" value="1" />
    </form>
    
    <form name="form2" method="get" action="<?php echo SELF; ?>">
      <input name="Skeyword" type="text" id="Skeyword" size="15" maxlength="8" class="input size2" placeholder="按红娘编号搜索">
      <input type="submit" value="搜索" class="btn size2" />
      <input type="hidden" name="t" value="2" />
    </form>
    
    </td>
    <td width="260" align="right">
    <form name="form3" method="get" action="<?php echo SELF; ?>">
      <input name="Skeyword" type="text" id="Skeyword" size="25" maxlength="25" class="W200 input size2" placeholder="按评价内容搜索">
      <input type="submit" value="搜索" class="btn size2" />
      <input type="hidden" name="t" value="3" />
    </form></td>
    </tr>
</table>
<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70" align="left">评价ID</th>
    <th width="180" align="left">评价会员</th>
    <th width="120" align="left">筛选此会员</th>
    <th width="100">红娘</th>
	<th align="left" >评价内容</th>
	<th width="70" align="center" >发表时间</th>
	<th width="70" align="center" >状态</th>
	<th width="80" align="center">修改评价内容</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$uid = $rows['uid'];
		$hid = $rows['hid'];
		$flag = $rows['flag'];
		$addtime = YmdHis($rows['addtime']);
		$title = dataIO($rows['title'],'out');
		$content = dataIO($rows['content'],'out');
		//
		$nickname  = dataIO($rows['nickname'],'out');
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$href      = Href('u',$uid);
		//
		$content = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$content);
		$pjkind = $rows['pjkind'];
		$content2 = dataIO($rows['content2'],'out');
		$content2 = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$content2);
		$content2_str  = (!empty($content2))?'<div class="hnhf">红娘回复：'.$content2.'</div>':'';
		switch ($pjkind){
			case 1:$pjkind_t = '<span class="pjkind k1">好评</span>';break;
			case 2:$pjkind_t = '<span class="pjkind k2">中评</span>';break;
			case 3:$pjkind_t = '<span class="pjkind k3">差评</span>';break;
		}
		
		$row2 = $db->ROW(__TBL_CRM_HN__,"truename","id=".$hid);
		if ($row2)$hnname= dataIO($row2[0],'out');
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
        <td width="70" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="180" align="left" style="padding:10px 0"><a href="<?php echo Href('u',$uid);?>" target="_blank">
  <?php echo uicon($sex.$grade); if(!empty($rows['nickname']))echo $nickname; ?>
  <font class="uleft">
  <?php
  echo 'UID：'.$uid."</br>";
 ?>
  </font>
  </a></td>
      <td width="120" align="left" class="S12"><a href="<?php echo SELF; ?>?Skeyword=<?php echo $uid; ?>&t=1" class="aHUI">筛选此会员</a></td>
    <td width="100" align="left">
    
	<?php
	  if(empty($hid)){
		  echo'<font class="C999">未分配</font>';
		}else{echo '<font class="C000 ">'.$hnname.'</font><br><font class="S12 C999">ID:'.$hid.'</font>';}
	  ?>    
    </td>
    <td align="left" class="lineH200">
    
  <span id="bbs<?php echo $id; ?>"><?php echo $pjkind_t; ?><?php echo $content; ?></span>
  <span id="bbs2<?php echo $id; ?>"><?php echo $content2_str; ?></span>
    
    
    </td>
    <td width="70" align="center" class="C999"><?php echo $addtime;?></td>
    <td width="70" align="center" class="C999">
    
<?php
$fHREF = SELF."?submitok=modflag&id=$id&t=$t&p=$p";
if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击显示">隐藏</a><?php }?>
<?php if($flag==0){?><a href="javascript:;" class="flag1 aHUANG" title="点击审核" clsid="<?php echo $id;?>">未审</a><?php }?>
<?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击隐藏">正常</a><?php }?>
    
    </td>
    <td width="80" align="center">
      <a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>"></a>
    </td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="9">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnflaglist" class="btn size2 LV disabled action" style="display:none">批量审核</button>
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);">发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
</form>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';

o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'crm_hn_bbs'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'批量删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}

zeai.listEach('.editico',function(obj){
	var id = parseInt(obj.getAttribute("clsid")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.iframe('修改【'+decodeURIComponent(nickname)+'】评价内容','crm_hn_bbs.php?submitok=mod&id='+id,550,450);
	}
});



</script>
<script src="js/zeai_tablelist.js"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
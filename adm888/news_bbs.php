<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('news',$QXARR))exit(noauth());
switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$id=intval($v);
				$row = $db->ROW(__TBL_NEWS_BBS__,"fid,content","id=".$id,"num");
				if ($row){
					$fid=$row[0];$content=$row[1];
					$db->query("DELETE FROM ".__TBL_NEWS_BBS__." WHERE id=".$id);
					AddLog('【文章】->删除文章评论【文章id:'.$fid.'】->评论内容：'.$content);
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"mod_update":
		if (!ifint($id))alert_adm("id参数错误","-1");
		$content = dataIO($content,'in',10000);
		$db->query("UPDATE ".__TBL_NEWS_BBS__." SET content='$content' WHERE id=".$id);
		AddLog('【文章】->修改文章评论->内容：'.$content);
		alert_adm("修改成功","news_bbs.php?submitok=mod&id=".$id);
	break;
	case"mod":
		if (!ifint($id))alert("id参数错误","-1");
		$rt = $db->query("SELECT content FROM ".__TBL_NEWS_BBS__." WHERE id=".$id);
		if($db->num_rows($rt)){
			$row     = $db->fetch_array($rt,'name');
			$content = dataIO($row['content'],'out');
		}else{
			alert_adm("该评论不存在！","-1");
		}
	break;
	case"modflag":
		if (!ifint($classid))callmsg("forbidden","-1");
		$rt = $db->query("SELECT flag FROM ".__TBL_NEWS_BBS__." WHERE id=".$classid);
		if($db->num_rows($rt)){
			$rows = $db->fetch_array($rt,'name');
			$flag = $rows['flag'];
			switch($flag){
				case"-1":$SQL="flag=1";break;
				case"0":$SQL="flag=1";break;
				case"1":$SQL="flag=-1";break;
			}
			$db->query("UPDATE ".__TBL_NEWS_BBS__." SET ".$SQL." WHERE id=".$classid);
			AddLog('【文章审核】评论状态修改->评论id:'.$id);
			header("Location: ".SELF."?p=$p&f=$f");
		}else{
			callmsg("您要操作的信息不存在或已经删除！","-1");
		}
	break;
	case"dataflag1":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);
				$db->query("UPDATE ".__TBL_NEWS_BBS__." SET flag=1 WHERE id=".$id);
				AddLog('【文章审核】评论审核通过->评论id:'.$id);
			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
}
require_once ZEAI.'cache/udata.php';
$extifshow = json_decode($_UDATA['extifshow'],true);
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
.tablelist{min-width:1200px;margin:20px 20px 50px 20px}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}

.tablelist .li{font-size:14px;line-height:200%}
.tablelist .li img{max-width:50px;max-height:50px;margin-left:5px}

.tablelist .li2 li{width:50px;height:50px;float:left;margin:10px;position:relative}
.tablelist .li2 li img{width:50px;height:50px;border-radius:2px;cursor:zoom-in;display:block}

.delico{;top:-10px;right:-10px;position:absolute;width:20px;height:20px;display:inline-block;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3);background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.delico:hover{background-position:-100px top;cursor:pointer}

.tablelist .sx{padding:15px 0 0 15px}
.table0{min-width:1600px;width:98%;margin:10px 20px 20px 20px}


.tablelist .ico{display:inline-block;color:#E83191;background-color:#E83191;color:#fff;border-radius:50px;width:26px;height:26px;line-height:26px;text-align:center}
.timestyle{display:inline-block;font-size:12px;margin:0 4px;color:#fff;border-radius:3px;padding:0 6px;height:18px;line-height:18px;text-align:center;background-color:#A7CAB2}

.partyC{word-break:break-all;word-wrap:break-word;}
.partyC img{width:20px}

</style>
<?php
?>
<body>

<?php if($submitok=='mod'){?>
    <form name="ZEAI_FORM" method="POST" action="<?php echo SELF;?>">
    <table class="table Mtop30 W500">
    <tr>
      <td class="tdL">评论内容</td>
      <td class="tdR"><textarea name="content" rows="5" class="textarea W100_" id="content"><?php echo $content;?></textarea></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input class="btn size3" type="submit" value="修改并保存" />
        <input name="submitok" type="hidden" value="mod_update" />
        <input name="id" type="hidden" value="<?php echo $id;?>" />
      </td>
    </tr>
    </table>
    </form>
<?php exit;}?>

<div class="navbox">
  <a href="news.php">文章管理</a>
  <a href="news_kind.php">文章分类</a>
  <a href="news_bbs.php" class="ed">文章评论<?php echo '<b>'.$db->COUNT(__TBL_NEWS_BBS__).'</b>';?></a>
  <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>


<table class="table0" style="min-width:980px">
    <tr>
    <td width="260" align="left" class="S14"><form name="form2" method="get" action="<?php echo $SELF; ?>">
      <input name="kuid" type="text" id="kuid" size="15" maxlength="8" class="input size2" placeholder="按会员UID搜索">
      <input type="submit" value="搜索" class="btn size2" />
      <input type="hidden" name="t" value="<?php echo $t; ?>" />
    </form></td>
    <td width="260" align="right"><form name="form1" method="get" action="<?php echo $SELF; ?>">
      <input name="Skeyword" type="text" id="Skeyword" size="25" maxlength="25" class="W200 input size2" placeholder="按评论内容搜索">
      <input type="submit" value="搜索" class="btn size2" />
      <input type="hidden" name="t" value="<?php echo $t; ?>" />
    </form></td>
    </tr>
</table>
<?php


$SQL="";
$Skeyword = trimm($Skeyword);
if (!empty($Skeyword)){
	$SQL = " AND a.content LIKE '%".dataIO($Skeyword,'in')."%' ";
}
if (ifint($kuid))$SQL .= " AND b.id =".$kuid;
if($f=='f0')$SQL .= " AND a.flag=0";

$rt = $db->query("SELECT a.flag,a.id,a.fid,a.uid,a.content,a.addtime,b.uname,b.nickname,b.grade,b.sex,b.photo_s,b.photo_f,c.title FROM ".__TBL_NEWS_BBS__." a,".__TBL_USER__." b,".__TBL_NEWS__." c WHERE a.fid=c.id AND a.uid=b.id ".$SQL." ORDER BY a.id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70" align="left">ID</th>
    <th width="180" align="left">发表人</th>
    <th width="120" align="left">筛选此会员</th>
    <th width="200">来自文章</th>
    <th width="100">状态</th>
	<th align="left" >评论内容</th>
	<th width="70" align="center" >发表时间</th>
	<th width="80" align="center">状态</th>
	<th width="80" align="center">修改评论内容</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$uid = $rows['uid'];
		$fid = $rows['fid'];
		$flag = $rows['flag'];
		$addtime = YmdHis($rows['addtime']);
		$title = dataIO($rows['title'],'out');
		$content = dataIO($rows['content'],'out');
		//
		$uid = $rows['uid'];
		$nickname  = dataIO($rows['nickname'],'out');
		$photo_s   = $rows['photo_s'];
		$photo_f   = $rows['photo_f'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$uname   = $rows['uname'];
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'">';
		}else{
			$photo_s_str = '';
		}
		$photo_fstr   = ($photo_f == 0 && !empty($photo_s))?'<span>审核中</span>':'';
		$href         = Href('u',$uid);
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
        <td width="70" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="180" align="left" style="padding:10px 0"><a href="<?php echo Href('u',$uid);?>" target="_blank">
  <?php echo uicon($sex.$grade) ?><?php if(!empty($rows['uname']))echo '<font class="S14">'.$uname.'</font>';?>
  <font class="uleft">
  <?php
  echo 'UID：'.$uid."</br>";
  if(!empty($rows['nickname']))echo $nickname;?>
  </font>
  </a></td>
      <td width="120" align="left" class="S12"><a href="<?php echo SELF; ?>?kuid=<?php echo $uid; ?>&t=<?php echo $t; ?>" class="aHUI">筛选此会员</a></td>
    <td width="200" align="left"  class="S16"><a href="<?php echo wHref('article',$fid); ?>" target="_blank" class="aHUI"><?php echo $title; ?></a></td>
    <td width="100" align="left"  class="S16">&nbsp;</td>
    <td align="left" class="lineH200 S14 partyC">●<?php echo str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$content); ?></td>
    <td width="70" align="center" class="C999"><?php echo $addtime;?></td>
    <td width="80" align="center">
    
<?php
$fHREF = SELF."?submitok=modflag&classid=$id&t=$t&p=$p&f=$f";
if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击显示">隐藏</a><?php }?>
<?php if($flag==0){?><a href="<?php echo $fHREF;?>" class="aHUANG" title="点击审核">未审</a><div class="C999" style="margin-top:6px">点击审核</div><?php }?>
<?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击隐藏">正常</a><?php }?>

    </td>
    <td width="80" align="center"><a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>"></a></td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="10">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btnflaglist" class="btn size2 LV disabled action">批量审核</button>　
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
		url:'news_bbs'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'批量删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.editico',function(obj){
	var id = parseInt(obj.getAttribute("clsid")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.iframe('修改【'+decodeURIComponent(nickname)+'】评论内容','news_bbs.php?submitok=mod&id='+id,600,300);
	}
});
if(!zeai.empty(o('btnflaglist')))o('btnflaglist').onclick = function() {
	allList({
		btnobj:this,
		url:'news_bbs'+zeai.ajxext+'submitok=dataflag1',
		title:'批量审核',
		content:'',
		msg:'审核处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
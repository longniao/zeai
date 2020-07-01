<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('dating_user',$QXARR))exit(noauth());
switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$id=intval($v);
				$db->query("DELETE FROM ".__TBL_DATING_USER__." WHERE id=".$id);
				AddLog('【约会审核】删除约会报名人员');
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
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
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">

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
</style>
<?php
?>
<body>
<div class="navbox">

    <a href="dating.php">约会管理</a>
    <a href="dating_user.php" class="ed">约会名单/联系方式<?php echo '<b>'.$db->COUNT(__TBL_DATING_USER__).'</b>';?></a>
    
    <div class="Rsobox">
    <form name="form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按约会标题搜索">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>     
    </div>
  
<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php


$tmpsql="";
$Skeyword = trimm($Skeyword);
if (!empty($Skeyword)){
	$tmpsql = " AND a.content LIKE '%".dataIO($Skeyword,'in')."%' ";
}
$rt = $db->query("SELECT a.id,a.fid,a.uid,a.content,a.addtime,b.uname,b.nickname,b.grade,b.sex,b.photo_s,b.photo_f FROM ".__TBL_DATING_USER__." a,".__TBL_USER__." b WHERE a.uid=b.id ".$tmpsql." ORDER BY a.id DESC LIMIT ".$_ADM['admLimit']);
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
    <th align="left">应约人</th>
    <th width="150" align="left">&nbsp;</th>
    <th width="300">约会主题</th>
	<th align="left" >报名应约内容</th>
	<th width="100" align="center" >报名时间</th>
	<th width="60" align="center">修改内容</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$uid = $rows['uid'];
		$fid = $rows['fid'];
		$addtime = YmdHis($rows['addtime']);
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
        <td width="70" align="left" style="padding:10px 0">
        	<a href="<?php echo $href; ?>" class="noU58 yuan sex<?php echo $sex; ?>" target="_blank"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
        </td>
      <td width="150" align="left" class="S12">
      
   <a href="<?php echo Href('u',$uid);?>" target="_blank">
  <?php echo uicon($sex.$grade) ?><?php if(!empty($rows['uname']))echo '<font class="S14">'.$uname.'</font>';?>
  <font class="uleft">
  <?php
  echo 'UID：'.$uid."</br>";
  if(!empty($rows['nickname']))echo $nickname;?>
  </font>
  </a>
  

    </td>
    <td width="300" align="left"  class="S16">
<?php
$row2 = $db->ROW(__TBL_DATING__,"title","id=".$fid);
if ($row2){
	$ftitle = dataIO($row2[0],'out');
}
?>
<a href="<?php echo Href('dating',$rows['fid']); ?>" target="_blank"><i class="ico">&#xe653;</i> <?php echo $ftitle;?></a>    
    </td>
    <td align="left" class="C999 lineH200"><?php echo str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",stripslashes($rows['content'])); ?></td>
    <td width="100" align="center" class="C999 lineH200"><?php echo $addtime;?></td>
    <td width="60" align="center">
      <a class="edit tips" tips-title='修改' tips-direction='left' id="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>">✎</a>
    </td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="8">
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
		url:'dating_user'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'批量删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}

zeai.listEach('.edit',function(obj){
	var id = parseInt(obj.getAttribute("id")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.iframe('修改应约内容','dating_user_mod.php?fid='+id,500,300);
	}
});



</script>
<script src="js/zeai_tablelist.js"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
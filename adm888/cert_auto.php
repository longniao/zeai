<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('cert',$QXARR))exit(noauth());
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
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:1200px;margin:20px 20px 50px 20px}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
.myinfobfb0,.myinfobfb1,.myinfobfb2{font-family:Arial;font-size:18px;display:block;height:20px;line-height:24px}
.myinfobfb0{color:#999}
.myinfobfb1{color:#f70}
.myinfobfb2{color:#090}

.ped{color:#FF5722;border-bottom:2px #FF5722 solid;padding-bottom:5px}
</style>
<?php
?>
<body>
<div class="navbox">
    <a class="ed">认证管理<?php echo '<b>'.$db->COUNT(__TBL_RZ__).'</b>';?></a>
    <div class="Rsobox">
        <form id="Zeai_search_form" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="输入：UID/用户名/手机/姓名/昵称">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0"><tr><td align="left" class="S14" style="padding-top:10px;color:#aaa">
    <a href="cert.php" class="C999">人工审核认证</a>　｜　
    <a href="cert_auto.php" class="ped">自助认证</a>
</td></tr></table>
<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL = " AND (b.id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL = " AND ( ( b.uname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}

$rt = $db->query("SELECT a.*,b.uname,b.nickname,b.sex,b.grade,b.mob,b.photo_s,b.photo_f,b.birthday,b.areatitle,b.house,b.car,b.edu,b.RZ,b.truename,b.identitynum FROM ".__TBL_PAY__." a,".__TBL_USER__." b WHERE a.kind<>-1 AND a.uid=b.id ".$SQL." AND a.kind=7 AND a.flag=1 ORDER BY a.id DESC LIMIT ".$_ADM['admLimit']);
	


$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70" align="left">UID</th>
    <th width="70" align="left">头像</th>
    <th width="180">认证会员</th>
    <th width="80" align="center">认证项目</th>
    <th width="60" align="center">当前状态</th>
    <th width="20" align="center">&nbsp;</th>
    <th width="200" align="left">对应当前所填资料</th>
	<th align="left" >自拍/认证照片</th>
	<th width="80" align="center" >提交时间</th>
	<th width="20" align="center" >&nbsp;</th>
    <th width="100" align="center">手动修改</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $rows['uid'];
		$uname = strip_tags($rows['uname']);
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$RZ        = $rows['RZ'];$RZarr=explode(',',$RZ);
		$rzid      = $rows['rzid'];
		$birthday  = $rows['birthday'];
		$love      = $rows['love'];
		$areatitle = $rows['areatitle'];
		$pay       = $rows['pay'];
		$house     = $rows['house'];
		$car       = $rows['car'];
		$edu       = $rows['edu'];
		$flag      = $rows['flag'];
		$truename   = dataIO($rows['truename'],'out');
		$identitynum= dataIO($rows['identitynum'],'out');
		
		$title= dataIO($rows['title'],'out');
		
		
		if(empty($rows['nickname'])){
			$nickname = $uname;
		}
		$addtime = YmdHis($rows['addtime']);
		$photo_s = $rows['photo_s'];
		$photo_f = $rows['photo_f'];
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'">';
		}else{
			$photo_s_str = '';
			$photo_s_url = '';
		}
		$mob = strip_tags($rows['mob']);
		$mob = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$mob);
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
      </td>
        <td width="70" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $uid;?></label></td>
        <td width="70" align="left">
        	<a href="javascript:;" class="noU58 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo getpath_smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
        </td>
      <td width="180" align="left" class="lineH150" style="padding:10px 0">
      	<div style="margin:5px 0"><?php echo RZ_html($RZ,'s','all');?></div>
        <a href="<?php echo Href('u',$uid);?>" target="_blank">
        <?php echo uicon($sex.$grade) ?><?php if(!empty($rows['uname']))echo '<font class="S14">'.$uname.'</font></br>';?>
        <font class="uleft">
        <?php
        if(!empty($rows['mob']))echo $mob."</br>";
        if(!empty($rows['nickname']))echo $nickname;?>
        </font>
        </a>
    </td>
    <td width="80" align="center" class="S16"><?php echo $title;?></td>
    <td width="60" align="center">
<?php
$str1 = "<i class='ico S18' style='color:#45C01A'>&#xe60d;</i>";
$str0 = "<i class='ico S18 Cf00'>&#xe62c;</i><br><font class='Cf00'>失败</font>";
if($title=='真人认证'){
	if (@in_array('photo',$RZarr)){
		echo $str1;
	}else{
		echo $str0;
	}
}elseif($title=='实名认证'){
	if (@in_array('identity',$RZarr)){
		echo $str1;
	}else{
		echo $str0;
	}
}
?></td>
    <td width="20" align="center" class="C999">&nbsp;</td>
    <td width="200" align="left" class="blue lineH150">
<?php 
echo '<font class="C999">姓　　名：</font>'.$truename.'<br>';
echo '<font class="C999">生　　日：</font>'.$birthday.'<br>';
echo '<font class="C999">身份证号：</font>'.$identitynum.'<br>';
if($title=='实名认证')echo '<font class="C999">手　　机：</font>'.$mob;
?>
    </td>
    <td align="left" class="C999 lineH200">
    <?php
	if ($title == '真人认证'){
		$row = $db->ROW(__TBL_RZ__,"path_b,path_b2","uid=".$uid." AND rzid='photo'","name");
		if ($row){
			$path_b  = $row['path_b'];
			$path_b2 = $row['path_b2'];
			if(!empty($path_b)){
				$path_b_url = $_ZEAI['up2'].'/'.$path_b;
				$path_b_str = '<img src="'.$path_b_url.'">';?>
				<a href="javascript:;" onClick="parent.piczoom('<?php echo $path_b_url; ?>');" class="pic100 FL"><?php echo $path_b_str; ?></a><?php
			}
			if(!empty($path_b2)){
				$path_b_url2 = $_ZEAI['up2'].'/'.$path_b2;
				$path_b_str2 = '<img src="'.$path_b_url2.'">';?>
				<a href="javascript:;" onClick="parent.piczoom('<?php echo $path_b_url2; ?>');" class="pic100 FL"><?php echo $path_b_str2; ?></a><?php
			}
		}
	}
	?>
    </td>
    <td width="80" align="center" class="C999 lineH150"><?php echo $addtime; ?></td>
    <td width="20" align="center" class="C999 lineH150">&nbsp;</td>
	<td width="100" align="center"><a href="javascript:;" title="手动修改" class="btn BAI mod" uid="<?php echo $uid; ?>">手动强制点亮</a></td>
      </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="12">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
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
zeai.listEach('.mod',function(obj){
	var uid = parseInt(obj.getAttribute("uid"));
	obj.onclick = function(){
		zeai.openurl('cert_hand.php?submitok=mod&memberid='+uid);
	}
});
</script>
<script src="js/zeai_tablelist.js"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
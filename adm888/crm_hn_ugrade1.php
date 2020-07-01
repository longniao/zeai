<?php
require_once '../sub/init.php';
header("Cache-control: private");
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';

if(!in_array('crm_hn_ugrade1',$QXARR))exit(noauth());


require_once ZEAI.'cache/udata.php';
$parameter = "p=$p&Skeyword=$Skeyword"; 

if($submitok=="usre_kind_mod_update"){
	if (!ifint($uid))exit('会员不存在或已被删除');
	$kind=intval($kind);
	$db->query("UPDATE ".__TBL_USER__." SET kind=$kind WHERE id=".$uid);
	alert_adm('修改成功',SELF."?submitok=usre_kind_mod&uid=".$uid);
}elseif($submitok=="usre_flag_mod_update"){
	if (!ifint($uid))exit('会员不存在或已被删除');
	$flag=intval($flag);
	if($flag==1 || $flag==2){
		$db->query("UPDATE ".__TBL_USER__." SET flag=$flag WHERE id=".$uid);
	}
	alert_adm('修改成功',SELF."?submitok=usre_flag_mod&uid=".$uid);
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

<style>
.tablelist{min-width:800px;margin-top:20px}

.mtop{ margin-top:10px;}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
.table0{width:98%;margin:10px 20px 10px 20px}
.gradeflag{display:block;color:#999;padding-top:10px;font-family:'宋体'}
img.m{width:50px;height:50px;border-radius:30px}

/***/
</style>
<body>
<div class="navbox">
    <a class="ed">售前未签约会员<?php echo '<b>'.$db->COUNT(__TBL_USER__,"(kind=2 OR kind=3) AND hnid>0 AND grade<2").'</b>';?></a>
    
    <div class="Rsobox">
 
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<table class="table0">
<tr>
<td width="350" align="left" class="border0" >
        <form name="form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="输入：UID/用户名/手机/姓名/昵称" value="<?php echo $Skeyword; ?>">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
        </form>    
</td>
<td>
</td>
<td width="300" align="right"><button type="button" value="" class="btn size2 disabled action" onClick="sendTipFn(this);">发送消息</button></td>
</tr>
</table>
<?php
$SQL = "";
if (!empty($Skeyword))$SQL   .= " AND (( truename LIKE '%".trimm($Skeyword)."%' ) OR ( mob LIKE '%".trimm($Skeyword)."%' ) OR ( id LIKE '%".trimm($Skeyword)."%' ) OR ( uname LIKE '%".trimm($Skeyword)."%' ) OR ( nickname LIKE '%".trimm($Skeyword)."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' )) ";
$areaid = '';
$SQL .= " AND (kind=2 OR kind=3) AND hnid>0 AND grade<2 ";

switch ($sort) {
	default:$SORT = " ORDER BY id DESC ";break;
}
$rt = $db->query("SELECT id,kind,hnid,hnname,uname,nickname,if2,sjtime,truename,photo_s,photo_f,mob,sex,grade,flag,areatitle,bz,heigh,weigh,birthday,edu,pay,job,love,marrytype,child,house,car FROM ".__TBL_USER__." WHERE (flag=1) ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
<form id="zeaiFORM" method="get" action="<?php echo $SELF; ?>">
  <?php $sorthref = "$SELF?".$parameter."&sort=";?>
  <table class="tablelist">
    <tr>
    <th width="30"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70">会员UID</th>
    <th width="70">形象照</th>
    <th width="130">会员昵称/手机</th>
    <th width="10">&nbsp;</th>
    <th align="left">会员基本信息</th>
    <th width="200" align="left">签约升级</th>
    <th width="80">售前红娘</th>
    <th width="60">&nbsp;</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid=$id;
		$pwd = $rows['pwd'];
		$uname = strip_tags($rows['uname']);
		$truename = strip_tags($rows['truename']);
			$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
			$truename = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$truename);
			$nickname = dataIO($rows['nickname'],'out');
			$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$mob = strip_tags($rows['mob']);
		$mob = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$mob);
		$photo_s   = $rows['photo_s'];
		$photo_f   = $rows['photo_f'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$flag      = $rows['flag'];
		$areatitle = $rows['areatitle'];
		$bz = dataIO($rows['bz'],'out');
		$tguid      = $rows['tguid'];
		$heigh = (intval($rows['heigh'])>0)?intval($rows['heigh']).'cm':'';
		$weigh = (intval($rows['weigh'])>0)?intval($rows['weigh']).'kg':'';
		$birthday  = $rows['birthday'];
		$age   = (@getage($birthday)<=0)?'':@getage($birthday).'岁';
		$edu   = udata('edu',intval($rows['edu']));
		$pay   = udata('pay',intval($rows['pay']));
		$job   = udata('job',intval($rows['job']));
		$love      = udata('love',intval($rows['love']));
		$marrytype = udata('marrytype',intval($rows['marrytype']));
		$child     = udata('child',intval($rows['child']));
		$house     = udata('house',intval($rows['house']));
		$car       = udata('car',intval($rows['car']));
		$kind      = $rows['kind'];
		$hnid      = $rows['hnid'];
		$hnname    = $rows['hnname'];
		$if2    = $rows['if2'];
		$sjtime = $rows['sjtime'];
		if(empty($rows['nickname'])){
			if(empty($rows['truename'])){
				$title = $rows['mob'];
			}else{
				$title = $rows['truename'];
			}
			if(empty($title))$title=$uname;
		}else{
			$title = $nickname;
		}
		
		
		
		switch ($grade) {
			case 1:$gradestyle   = " class='aHUI'";break;
			case 2:$gradestyle   = " class='aLAN'";break;
			case 3:$gradestyle   = " class='aZI'";break;
			case 4:$gradestyle   = " class='aHUANG'";break;
			case 5:$gradestyle   = " class='aJIN'";break;
			case 6:$gradestyle   = " class='aHONG'";break;
			case 7:$gradestyle   = " class='aLV'";break;
			case 8:$gradestyle   = " class='aHEI'";break;
			case 9:$gradestyle   = " class='aQING'";break;
			case 10:$gradestyle  = " class='aQINGed'";break;
		}
		//gradeflag
		$timestr1 = get_if2_title($if2);
		if (!empty($sjtime)){
			$d1  = ADDTIME;
			$d2  = $sjtime + $if2*30*86400;
			$ddiff = $d2-$d1;
			if ($ddiff < 0){
				$timestr2 = ',<font class="Caaa">已过期</font>';
			} else {
				$tmpday   = intval($ddiff/86400);
				$timestr2 = ',还剩<font class="Cf00">'.$tmpday.'</font>天';
			}
			$timestr2 = ($if2 == 999)?'':$timestr2;
		}
		$gradeflag = ($grade == 1 || $grade == 10)?'':'<span class="gradeflag">'.$timestr1.$timestr2.'</span>';
		//
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$sexbg       = (empty($photo_s))?' class="m sexbg'.$sex.'"':' class="m"';
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="30"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
        </td>
      <td width="70"><?php echo $id;?></td>
        <td width="70" align="left">
        <a href="<?php echo Href('crm_u',$id);?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a>
        </td>
        <td width="130" align="left" class="lineH150 S14" style="padding:10px 0">
        <a href="<?php echo Href('crm_u',$id);?>">
        <?php
		echo uicon($sex.$grade);
		echo '<span style="vertical-align:middle">';
		if(!empty($rows['uname']))echo $uname."</br>";
		echo '<font class="uleft">';
		
		if(!empty($rows['nickname']))echo $nickname."</br>";
		if(!empty($rows['mob']))echo $mob;
		echo '</font></span>';
		?>
        </a>
        </td>
      <td width="10" align="left" valign="bottom">&nbsp;</td>
      <td align="left" valign="middle" class="C666 lineH150">
	  
	  <?php echo $age.'　'.$heigh.'　'.$pay.'　'.$edu.'　'.$love.'　'.$areatitle.'　'.$house.'　'.$job.'　'.$marrytype.'　'.$child.'　'.$weigh.'　'.$car;?>
      
      </td>
      <td width="200" align="left" id="grade<?php echo $id;?>">
      
<a href="javascript:;" <?php echo $gradestyle; ?> onClick="zeai.openurl('crm_ht.php?submitok=add&uid=<?php echo $id;?>')"><?php echo str_replace("普通会员","未签约",utitle($grade));; ?></a>
      <?php echo $gradeflag; ?>      
      
      </td>
      <td width="80"><?php
	  if(empty($hnname)){
		  echo'<font class="C999">未分配</font>';
		}else{echo '<font class="C000 S14">'.$hnname.'</font><br><font class="S12 C999">编号:'.$hnid.'</font>';}
	  ?></td>
      <td width="60">&nbsp;</td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="10">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" value="" class="btn size2 disabled action" onClick="sendTipFn(this);">发送消息</button>
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/zeai_tablelist.js"></script>
</form>
<?php }?>
<br><br><br>
<script>
</script>
<?php require_once 'bottomadm.php';?>


<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/udata.php';
header("Cache-control: private");
if ($submitok_zeai == "modupdate") {
	$uid = $memberid;
	if ( !ifint($uid))alert_adm("用户UID不合法",'-1');
	if(!empty($list)){
		$RZ = implode(",",$list);
	}else{
		$RZ = '';
	}
	$db->query("UPDATE ".__TBL_USER__." SET RZ='$RZ' WHERE id=".$uid);
	alert_adm("操作成功。",SELF."?submitok=show&memberid=".$uid);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
/*uidsobox*/
.uidsobox{margin:100px auto 0 auto;border:#eee 1px solid;width:700px;line-height:100px;background-color:#f8f8f8}
.uidsobox .input{height:30px;line-height:30px;background-color:#fff}
.HUANG3{margin-bottom:5px}
a.noUW200 img{max-width:200px;display:block;cursor:zoom-in;}
</style>
<body>
<div class="navbox">
    <a class="ed">手动强制认证</a>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php if (empty($submitok)){ ?>
    <div class="uidsobox S16">
    <form action="<?php echo SELF; ?>" method="post">
    请输入会员UID
    <input name="memberid" type="text" class="input W150 size3" id="memberid"size="8" maxlength="9"> 
    <input type="submit" name="Submit" value="提交" class="btn size2">
    <input name="submitok" type="hidden" value="show" /></td>
    </form>
    </div>
<?php
}else{
	if ( !ifint($memberid))alert_adm("ID号输入有误或不存在此会员。请检查1","-1");
	$rtD=$db->query("SELECT nickname,sex,grade,photo_s,RZ,truename,photo_f,birthday,love,areatitle,pay,house,car,edu,mob,weixin,qq,email,identitynum,truename FROM ".__TBL_USER__." WHERE id=".$memberid);
	if ($db->num_rows($rtD)){
		$rowD = $db->fetch_array($rtD);
		$uid      = $memberid;
		$nickname = dataIO($rowD['nickname'],'out');
		$sex      = $rowD['sex'];
		$mob      = $rowD['mob'];
		$grade    = $rowD['grade'];
		$photo_s  = $rowD['photo_s'];
		$photo_f  = $rowD['photo_f'];
		$RZ       = $rowD['RZ'];$RZarr=explode(',',$RZ);
		$href = Href('u',$uid);
		//
		$birthday  = $rowD['birthday'];
		$love      = $rowD['love'];
		$areatitle = $rowD['areatitle'];
		$pay       = $rowD['pay'];
		$house     = $rowD['house'];
		$car       = $rowD['car'];
		$edu       = $rowD['edu'];
		$mob       = $rowD['mob'];
		
		$identitynum = dataIO($rowD['identitynum'],'out');
		$truename    = dataIO($rowD['truename'],'out');
		$email       = dataIO($rowD['email'],'out');
		$weixin      = dataIO($rowD['weixin'],'out');
		$qq          = dataIO($rowD['qq'],'out');
		//	
		if(empty($rowD['nickname'])){
			if(empty($truename)){
				$title = $mob;
			}else{
				$title = $truename;
			}
		}else{
			$title = $nickname;
		}
		$mob = (!empty($mob))?$mob:'';
		
		$photo_m_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'m'):HOST.'/res/photo_m'.$sex.'.png';
		
		$photo_b_url = getpath_smb($_ZEAI['up2'].'/'.$photo_s,'b');
		$photo_m_str = '<img src="'.$photo_m_url.'">';
		$RZarr=explode(',',$RZ);
	}else{
		alert_adm("UID输入有误或不存在此会员。请检查","-1");
	}
?>
<br>
<table class="table W900 Mtop50">
<form action="<?php echo $SELF; ?>" method="post" name="www-z　e　a　i-cn">
<tr>
<td width="200" rowspan="3" align="right" valign="top" bgcolor="#f3f3f3"><table width="110" border="0" cellspacing="0" cellpadding="5" class="table0">
<tr>
<td align="center"><a href="javascript:;" class="noUW200 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo $photo_b_url; ?>');"><?php echo $photo_m_str; ?><?php echo $photo_fstr; ?></a></td>
</tr>
<tr>
<td height="40" align="center" class="S16"><?php echo uicon($sex.$grade); ?> <a href="<?php echo $href; ?>" target="_blank"><?php echo $title; ?></a></td>
</tr>
<tr>
  <td height="35" align="center" valign="top" class="S16 C999">UID：<?php echo $uid; ?></td>
</tr>
<tr>
  <td height="10" align="center" valign="bottom"><?php echo RZ_html($RZ,'s','all');?></td>
</tr>
<tr style="display:none">
  <td height="80" align="center" valign="bottom"><a href="javascript:;" class="edit2 btn size2" tips-title='修改资料' tips-direction='left' uid="<?php echo $uid; ?>" nickname="<?php echo urlencode($nickname);?>">修改资料</a></td>
</tr>
</table>      </td>
<td align="left" bgcolor="#f8f8f8" style="padding:15px 10px"><b class="S18">认证项目（点亮图标）</b><br><font class="C999">打上勾通过认证，不打勾未认证</font></td>
</tr>
<tr>
<td align="left" bgcolor="#FFFFFF" class="blue S12" style="padding:30px">


<?php
$rz_dataARR = explode(',',$_ZEAI['rz_data']);
if (count($rz_dataARR) >= 1 && is_array($rz_dataARR)){
	foreach ($rz_dataARR as $k=>$V) {
	?>
    
    <input name="list[]" value="<?php echo $V;?>" type="checkbox" id="rzid_<?php echo $V;?>" class="checkskin"<?php echo (in_array($V,$RZarr))?' checked':'';?>><label for="rzid_<?php echo $V;?>" class="checkskin-label"><i class="i3"></i><b class="S18 C666"><?php echo rz_data_info($V,'title');?></b></label>　<br><br>
    
<?php }}?>



</td>
</tr>
<tr>
<td height="60" align="left" bgcolor="#f8f8f8" style="padding:20px 30px">
<input type="submit" value="　提交　" class="btn size3 HUANG3">
<input name="submitok_zeai" type="hidden" value="modupdate" />
<input name="memberid" type="hidden" value="<?php echo $memberid; ?>" />
<br>
<font color="#999999">此操作不触发任何操作，只改变主表认证字段内容</font></font></td>
</tr>
</form>
</table>
<?php }?>
<script>
zeai.listEach('.edit2',function(obj){
	var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.iframe('修改【'+decodeURIComponent(nickname)+'】资料','u_mod_data.php?submitok=mod&ifmini=1&uid='+uid,1300,700);
	}
});
</script>
<?php require_once 'bottomadm.php';?>
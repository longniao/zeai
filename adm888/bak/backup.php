<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once ZEAI2."chkuser.php";
//if(!strstr($_SESSION["promision"],'027'))callmsg("您当前角色为“".$cook_roletitle."”：权限不足","2");

$datadir = $_ZEAI['BAK_dir'].DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.$_ZEAI['dbname'];
$datazip = ZEAI.$_ZEAI['BAK_dir'].DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.$_ZEAI['dbname'].'.zip';
if ($submitok == "backup") {
	if (file_exists(ZEAI.$datazip))unlink(ZEAI.$datazip);
	require_once ZEAI.'sub/Zeai_bak.php';
	//$DbBak = new Zeai_dbbak(mysqli_connect($_ZEAI['dbserver'],$_ZEAI['dbuser'],$_ZEAI['dbpass']),ZEAI.DIRECTORY_SEPARATOR.$_ZEAI['BAK_dir'].DIRECTORY_SEPARATOR."data");  
	$DbBak = new Zeai_dbbak(mysqli_connect($_ZEAI['dbserver'],$_ZEAI['dbuser'],$_ZEAI['dbpass'],$_ZEAI['dbname']),ZEAI.$_ZEAI['BAK_dir'].DIRECTORY_SEPARATOR."data");  
	$DbBak->backupDb($_ZEAI['dbname']);
	callmsg("备份成功!","$SELF");
}elseif($submitok == "zip"){
	require_once ZEAI.'sub/Zeai_zipdatabase.php';
	callmsg("打包成功!","$SELF");
}elseif($submitok == "delbak"){
	del_dir(ZEAI.$datadir);
	if (file_exists($datazip))unlink($datazip);
	callmsg("删除成功!","$SELF");
//}elseif($submitok == "dounzip"){
//	require_once ZEAI.'sub/Zeai_unzip.php';
//	callmsg("上传数据库源并解压成功!","$SELF");
}elseif($submitok == "restore"){
	require_once ZEAI.'sub/Zeai_bak.php';
	$DbBak = new Zeai_dbbak(mysqli_connect($_ZEAI['dbserver'],$_ZEAI['dbuser'],$_ZEAI['dbpass'],$_ZEAI['dbname']),ZEAI.$_ZEAI['BAK_dir'].DIRECTORY_SEPARATOR."data");  
	$ifok = $DbBak->restoreDb($_ZEAI['dbname']);
	if ($ifok)del_dir(ZEAI.$datadir);
	callmsg("还原并清理临时文件成功!","$SELF");
}
function del_dir( $dirName ){
	if ( $handle = opendir( "$dirName" ) ) {
	   while ( false !== ( $item = readdir( $handle ) ) ) {
	   if ( $item != "." && $item != ".." ) {
	   if ( is_dir( "$dirName/$item" ) ) {
	   del_dir( "$dirName/$item" );
	   } else {
	  unlink( "$dirName/$item" );
	  // if( unlink( "$dirName/$item" ) )echo "成功删除文件： $dirName/$item<br />\n";
	   }}}
	   closedir( $handle );
	   rmdir( $dirName );
	  // if( rmdir( $dirName ) )echo "成功删除目录： $dirName<br />\n";
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="../css/main.css" rel="stylesheet" type="text/css">
<script src="./js/www_zeai_cn.js"></script>
<body>
<div class="navbox"><a href="javascript:;" class="ed">数据备份</a><div class="clear"></div></div>
<table width="700" height="116" border="0" align="center" cellpadding="10" cellspacing="1" bgcolor="#dddddd" class="Mtop50">
<tr>
<td colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT C999">数据库备份：</td>
</tr>
<tr>
<td width="124" align="right" bgcolor="#f8f8f8">数据备份：</td>
<td width="533" align="left" bgcolor="#FFFFFF">


<?php if (!is_dir(ZEAI.$datadir)) {?>
	<input type="button"  value="开始备份" class="btn" onClick="openurl('<?php echo $SELF; ?>?submitok=backup')">
<?php }else{?>

	<input type="button"  value="重新备份" class="btn" onClick="openurl('<?php echo $SELF; ?>?submitok=backup')"><br><br>
	<?php if (file_exists($datazip)){$a=filemtime($datazip);?>
				
	
		<a href="<?php echo 'data/'.$_ZEAI['dbname'].'.zip'; ?>" class="B tiaose">下载备份</a> (<font><?php echo $_ZEAI['BAK_dir'].DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.$_ZEAI['dbname'].'.zip'; ?></font> ,<font class="S11 C999"><?php echo date("Y-m-d H:i:s",$a); ?></font>)<br><br>
		为了安全起见，建议下载完毕后，立即删除备份文件　　<a class="B tiaose" href="<?php echo $SELF; ?>?submitok=delbak" onClick="return confirm('你真的要删除此备份吗？')">立即删除</a>
	<?php }else{?>
		已生成数据源备份集，<a href="<?php echo $SELF; ?>?submitok=zip" class="B">打包下载</a>
	<?php }?>
	  
<?php }?>	  </td>
</tr>

<!--<div style="display:none">
<tr>
<td colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT C999">数据库还原：</td>
</tr>
<tr>
<td align="right" bgcolor="#f8f8f8">还原数据库：</td>
<td align="left" bgcolor="#FFFFFF">
<?php if (!is_dir(ZEAI.$datadir)) {?>
<form name="myform" method="post" action="<?php echo $SELF;?>" enctype="multipart/form-data">
<input name="submitok" type="hidden" value="dounzip">
<input name="upfile" type="file" id="upfile" size="20" class="input">
<input type="submit" name="Submit" value="上传数据库" class="btn">
</form>
<?php }else{?>
检测到数据源备份集 <font><?php echo $datadir; ?></font> ,<font class="S11 C999">(<?php echo date("Y-m-d H:i:s",filemtime(ZEAI.$datadir)); ?>)</font>　　<a href="<?php echo $SELF; ?>?submitok=restore" class="B tiaose" onClick="return confirm('请慎重! 确认使用此备份集还原么?\n\n此操作将覆盖网站现有数据,无法恢复')">开始还原</a><?php }?></td>
</tr>
</div>
 -->







<tr>
<td colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT C999">照片备份：</td>
  </tr>
<tr>
<td align="right" bgcolor="#f8f8f8">备份照片：</td>
<td height="80" align="left" bgcolor="#FFFFFF">

<?php 
$url  = $_ZEAI['up2'].'/'.$_ZEAI['UpPath']."/tmp/zeaiup.zip";
$ifup = get_remote_file_info($url);
if ($ifup){
	$headInf = get_headers($url,1);//print_r($headInf );
	$zip_size = intval($headInf['Content-Length']/1024/1024);
	$zip_time = YmdHis(strtotime($headInf['Last-Modified']));
?>
检测到照片备份集 <?php echo $url; ?> 　<font class="S11 C999">(<?php echo $zip_time; ?>，<?php echo $zip_size; ?>M)</font><br />
<br />
<a href="<?php echo $url; ?>" class="B tiaose">下载</a>　　
<a href="<?php echo $_ZEAI['up2']; ?>/backup_up.php?submitok=delup&token=<?php echo md5('www.zeai.cn'); ?>&ssl=<?php echo md5('择爱交友系统V5.0'); ?>&uu=<?php echo $_SESSION['adminid']; ?>&pp=<?php echo $_SESSION['password']; ?>" class="B tiaose" onClick="return confirm('你真的要删除此备份吗？')">删除</a>
<?php }else{?>
<form name="myform" id="ZEAI_FORM" method="post" action="<?php echo $_ZEAI['up2']; ?>/backup_up.php">
	<input name="submitok" type="hidden" value="bakup" />
	<input name="uu" type="hidden" value=<?php echo $_SESSION['adminid']; ?>>
	<input name="pp" type="hidden" value=<?php echo $_SESSION['password']; ?>>
	<span id="btnokdiv"><input type="button" name="Submit" id="btnok" value=" 将照片打包 " class="btn"></span>
    
    <br><br>此操作将可能时间较长,请不要关闭窗口,耐心等待...<br><br>打包后,将在你网站前台 <?php echo $_ZEAI['up2']; ?>/<?php echo $_ZEAI['UpPath']; ?>/tmp/ 目录下面生成　＂zeaiup.zip＂　文件,由于文件较大,请用ftp或相关下载工具下载,下载完成后,请立即删除.以免被盗.
</form>
<script>
o('btnok').onclick = function(){
	parent.ZEAI_win_confirm('确认打包么？',"window.right.confirmok()");
}
function confirmok(){
	//o('btnok').onclick = null;
	o('btnokdiv').innerHTML = '<img src="../images/load2.gif">　<font class="C00f">打包中...</font>';
	ZEAI_FORM.submit();
}
</script>

<?php }?>
<br />
</td>
</tr>
</table>
<br>
<?php require_once ZEAI.'bottomadm.php';
function get_remote_file_info($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
	$result = curl_exec($curl);
	$found = false;
	if ($result !== false) {
		$statusCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		if ($statusCode == 200) {
			$found = true;
		}
	}
	curl_close($curl);
	return $found;
}
?>
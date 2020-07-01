<?php require_once '../../sub/init.php';
require_once ZEAI.'sub/upload_super.php';
require_once ZEAI.'my/chkuser.php';
require_once 'JSON.php';
if (ifpostpic($_FILES["imgFile"]['tmp_name'])){
	$file  = $_FILES["imgFile"];$dbpicname = setphotodbname($_ZEAI['UpPath'].'/group',$file['tmp_name'],'group_'.$cook_uid.'_');
	if (!up_send($file,$dbpicname,$_ZEAI['ifwaterimg'],$_ZEAI['UpBsize']))callmsg('移动照片出错',"-1");
	$file_url = $_ZEAI['up2'].'/'.$dbpicname;
	$db->query("INSERT INTO ".__TBL_PHOTOSUP__."(uid,path_b,kind,addtime) VALUES ($cook_uid,'".$dbpicname."','group',$ADDTIME)");
	$json = new Services_JSON();
?><script>document.domain = '<?php echo substr($_ZEAI['CookDomain'],1); ?>';</script>
<?php echo $json->encode(array('error' => 0, 'url' => $file_url));exit;}exit;?>
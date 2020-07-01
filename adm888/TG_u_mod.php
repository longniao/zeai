<?php
require_once '../sub/init.php';
header("Cache-control: private");
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('u_tg',$QXARR) && !in_array('shop',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_shop.php';
$TG_set = json_decode($_REG['TG_set'],true);
$logstr='推广员/买家/'.$_SHOP['title'];
if($k==2){
	$kstr=$_SHOP['title'];	
}elseif($k==1){
	$kstr='推广'.$TG_set['tgytitle'];	
}else{
	$kstr='买家';
}
if ( !ifint($tg_uid) && $submitok != 'add' && $submitok != 'addupdate' && $submitok != 'ajax_pic_path_s_up')alert_adm("ID号不正确","back");
if(ifint($tg_uid)){
	$rowc = $db->ROW(__TBL_TG_USER__,"title,piclist","id=".$tg_uid,"num");
	if ($rowc){
		$cname= ' '.dataIO($rowc[0],'out');
		$data_pathlist= $rowc[1];
	}else{exit(JSON_ERROR);}
}
if ($submitok == 'modupdate'){
	$setsql = "";$ifnext = true;
	$kind    = intval($kind);
	$flag    = intval($flag);
	$grade   = intval($grade);
	$uid     = intval($uid);
	$tguid   = intval($tguid);
	$uname   = trimhtml(dataIO($uname,'in',40));
	$nickname= trimhtml(dataIO($nickname,'in',40));
	$title   = dataIO($title,'in',200);
	$openid     = dataIO($openid,'in',32);
	$union      = dataIO($union,'in',32);
	$areaid     = dataIO($areaid,'in',100);
	$areatitle  = dataIO($areatitle,'in',100);
	$content    = dataIO($content,'in',50000);
	$bz         = dataIO($bz,'in',1000);
	$job        = dataIO($job,'in',200);
	$pwd        = dataIO($pwd,'in',20);
	$bank_name       = dataIO($bank_name,'in',100);
	$bank_name_kaihu = dataIO($bank_name_kaihu,'in',200);
	$bank_truename   = dataIO($bank_truename,'in',50);
	$bank_card       = dataIO($bank_card,'in',50);
	$alipay_truename = dataIO($alipay_truename,'in',50);
	$alipay_username = dataIO($alipay_username,'in',100);
	$worktime   = dataIO($worktime,'in',200);
	$tel        = dataIO($tel,'in',100);
	$address    = dataIO($address,'in',100);
	$qq         = dataIO($qq,'in',15);
	$weixin     = dataIO($weixin,'in',50);
	$email      = dataIO($email,'in',50);
	$px = intval($px);
	$subscribe = intval($subscribe);
	$row = $db->ROW(__TBL_TG_ROLE__,'title',"shopgrade=0 AND grade=".$grade,"num");
	$gradetitle=$row[0];
	$shopgrade = intval($shopgrade);
	$shopflag  = intval($shopflag);
	$buyflag  = intval($buyflag);
	$row = $db->ROW(__TBL_TG_ROLE__,'title',"grade=0 AND shopgrade=".$shopgrade,"num");
	$shopgradetitle=$row[0];
	$shopkind = intval($shopkind);
	$longitude = dataIO($longitude,'in',15);
	$latitude  = dataIO($latitude,'in',15);
	$qhlongitude = dataIO($qhlongitude,'in',15);
	$qhlatitude  = dataIO($qhlatitude,'in',15);
	$qhdz = dataIO($qhdz,'in',100);
	$qhbz = dataIO($qhbz,'in',100);
	if (!ifint($grade))json_exit(array('flag'=>0,'msg'=>'请输入正确的【推广等级】'));
	if($shopflag!=2){
		if (str_len($title) > 100 || str_len($title)<2)json_exit(array('flag'=>0,'msg'=>'请输入【'.$_SHOP['title'].'名称】'));
		if (!ifint($shopgrade))json_exit(array('flag'=>0,'msg'=>'请选择【'.$_SHOP['title'].'等级】'));
		if (!ifint($shopkind))json_exit(array('flag'=>0,'msg'=>'请选择【行业分类】'));
		$sjtime  = strtotime($sjtime);
		$sjtime2 = strtotime($sjtime2);
		$dd=$sjtime2-$sjtime;
		if($dd<86400)json_exit(array('flag'=>0,'msg'=>'【服务结束时间】必须大于或等于【服务起始时间】1天及以上'));
		$setsql  = "sjtime='$sjtime',sjtime2='$sjtime2',";
	}
	$setsql .= "buyflag='$buyflag',qhdz='$qhdz',qhbz='$qhbz',qhlongitude='$qhlongitude',qhlatitude='$qhlatitude',longitude='$longitude',latitude='$latitude',shopkind='$shopkind',shopgrade='$shopgrade',shopgradetitle='$shopgradetitle',subscribe='$subscribe',flag='$flag',shopflag='$shopflag',tguid='$tguid',px='$px',nickname='$nickname',uid=$uid,grade='$grade',gradetitle='$gradetitle',address='$address',tel='$tel',weixin='$weixin',qq='$qq',email='$email',title='$title',content='$content',bz='$bz',kind='$kind',areaid='$areaid',areatitle='$areatitle',job='$job',bank_name='$bank_name',bank_name_kaihu='$bank_name_kaihu',bank_truename='$bank_truename',bank_card='$bank_card',alipay_truename='$alipay_truename',alipay_username='$alipay_username',worktime='$worktime'";
	if ($openid != $openid_old){
		$row = $db->ROW(__TBL_TG_USER__,'id',"openid='$openid' AND openid<>''");
		if($row){$varmsg.="“openid”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",openid='$openid'";}
	}
	if ($uname != $username_old){
		$row = $db->ROW(__TBL_TG_USER__,'id',"uname='$uname' AND uname<>''");
		if($row){$varmsg.="“登录帐号”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",uname='$uname'";}
	}
	if ($email != $email_old){
		$row = $db->ROW(__TBL_TG_USER__,'id',"email='$email' AND email<>''");
		if($row){$varmsg.="“Email”已被【".dataIO($row[0],'out')."】占用";$ifnext=false;}else{$setsql .= ",email='$email'";}
	}
	if (!empty($pwd) && str_len($pwd) <= 20 && str_len($pwd) >= 6){
		$pwd = md5(trimm($pwd));
		$setsql  .= ",pwd='$pwd'";
	}		
	//
	if ($mob != $mob_old && ifmob($mob)){
		$row = $db->ROW(__TBL_TG_USER__,'id',"mob='$mob' AND mob<>'' AND FIND_IN_SET('mob',RZ)","num");
		if($row){$varmsg.="“手机”已被【".$row[0]."】占用";$ifnext=false;}else{$setsql .= ",mob='$mob'";}
	}else{
		$setsql .= ",mob='$mob'";
	}
	if($rz_mob=='mob' && ifmob($mob)){
		$setsql .= ",RZ='mob'";	
	}else{
		$setsql .= ",RZ=''";
	}
	if (!$ifnext)json_exit(array('flag'=>0,'msg'=>$varmsg));
	
	$file = $_FILES["pic1"];
	if (!empty($file['tmp_name'])){
		$dbname = setphotodbname('shop',$file['tmp_name'],'');
		if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$dbname = setpath_s($dbname);
		@up_send_admindel(smb($dbname,'blur'));
		$setsql .= ",weixin_ewm='$dbname'";
	}
	$file = $_FILES["pic2"];
	if (!empty($file['tmp_name'])){
		$dbname = setphotodbname('shop',$file['tmp_name'],'');
		if (!up_send($file,$dbname,$_UP['ifwaterimg'],'2000*2000',$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$dbname = setpath_s($dbname);
		@up_send_admindel(smb($dbname,'blur'));
		$setsql .= ",yyzz_pic='$dbname'";
	}
	
	$piclist = tmp_piclist_modupdate($pathlist,$data_pathlist,'shop');
	$setsql .= ",piclist='$piclist'";
	
	$db->query("UPDATE ".__TBL_TG_USER__." SET ".$setsql." WHERE id=".$tg_uid);

	AddLog('【'.$logstr.'】修改->【'.$nickname.'（id:'.$tg_uid.'）】');
	$url=($k==3)?'shop_u':'TG_u';
	json_exit(array('flag'=>1,'msg'=>'操作成功','url'=>$url.".php?f=$f&g=$g&k=$k&p=$p&Skey=$Skey"));
}elseif($submitok == 'mod' || $submitok == 'photo'){
	$fUnum  = ",(SELECT COUNT(id) FROM ".__TBL_USER__." WHERE tguid=TG_U.id) AS Unum";
	$fTGnum = ",(SELECT COUNT(id) FROM ".__TBL_TG_USER__." WHERE tguid=TG_U.id) AS TGnum";
	$rt=$db->query("SELECT *".$fUnum.$fTGnum." FROM ".__TBL_TG_USER__." TG_U WHERE id=".$tg_uid);
	$rows = $db->fetch_array($rt,'name');
	if(!$rows)alert_adm("不存在","back");
	$id   = $rows['id'];
	$kind = $rows['kind'];
	$flag = $rows['flag'];
	$shopflag = $rows['shopflag'];
	$buyflag = $rows['buyflag'];
	$uid  = $rows['uid'];
	$pwd  = $rows['pwd'];
	$uname = dataIO($rows['uname'],'out');
	$title = dataIO($rows['title'],'out');
	$nickname = dataIO($rows['nickname'],'out');
	if($kind==3)$title3=$title;
	$mob   = dataIO($rows['mob'],'out');
	$photo_s   = $rows['photo_s'];
	$grade     = $rows['grade'];
	$gradetitle= $rows['gradetitle'];
	$shopgrade     = $rows['shopgrade'];
	$shopgradetitle= $rows['shopgradetitle'];
	$shopkind = intval($rows['shopkind']);
	$sjtime  = YmdHis($rows['sjtime']);
	$sjtime2 = YmdHis($rows['sjtime2']);
	$areaid    = $rows['areaid'];
	$loveb     = $rows['loveb'];
	$money     = $rows['money'];
	$subscribe = $rows['subscribe'];
	$openid = $rows['openid'];
	$flag      = $rows['flag'];
	$areatitle = $rows['areatitle'];
	$longitude = $rows['longitude'];
	$latitude  = $rows['latitude'];
	$qhlongitude = $rows['qhlongitude'];
	$qhlatitude  = $rows['qhlatitude'];
	$qhdz = dataIO($rows['qhdz'],'out',100);
	$qhbz = dataIO($rows['qhbz'],'out',100);
	$bz = dataIO($rows['bz'],'out');
	$tguid     = $rows['tguid'];
	$tgflag    = $rows['tgflag'];
	$tgmoney   = $rows['tgmoney'];
	$job     = dataIO($rows['job'],'out');
	$content = dataIO($rows['content'],'out');
	$bank_name       = dataIO($rows['bank_name'],'out');
	$bank_name_kaihu = dataIO($rows['bank_name_kaihu'],'out');
	$bank_truename   = dataIO($rows['bank_truename'],'out');
	$bank_card       = dataIO($rows['bank_card'],'out');
	$alipay_truename = dataIO($rows['alipay_truename'],'out');
	$alipay_username = dataIO($rows['alipay_username'],'out');
	$worktime = dataIO($rows['worktime'],'out');
	$tel      = dataIO($rows['tel'],'out');
	$Unum  = $rows['Unum'];
	$TGnum = $rows['TGnum'];
	switch ($kind) {
		case 1:$kind_str  = '个人';break;
		case 2:$kind_str  = '商家';break;
		case 3:$kind_str  = '机构';break;
	}
	if($shopflag!=2 && $shopgrade>0){
		$shopgradetitle=shopgrade($shopgrade,'img',3);
	}
	//
	$areaid     = explode(',',$areaid);
	$a1 = $areaid[0];$a2 = $areaid[1];$a3 = $areaid[2];$a4 = $areaid[3];
	$address    = dataIO($rows['address'],'out');
	$weixin     = dataIO($rows['weixin'],'out');
	$weixin_ewm = $rows['weixin_ewm'];
	$yyzz_pic   = $rows['yyzz_pic'];
	$qq         = dataIO($rows['qq'],'out');
	$email      = dataIO($rows['email'],'out');
	$content    = dataIO($rows['content'],'out');
	$grade    = $rows['grade'];
	$photo_s  = $rows['photo_s'];
	$RZ       = $rows['RZ'];
	$regtime    = YmdHis($rows['regtime']);
	$regip      = $rows['regip'];
	$endtime    = YmdHis($rows['endtime']);
	$endip      = $rows['endip'];
	$px = intval($rows['px']);
	$click   = $rows['click'];
	$piclist = $rows['piclist'];
	if(!empty($photo_s)){
		$photo_s_url=$_ZEAI['up2'].'/'.$photo_s;
		$photo_b_url=smb($photo_s_url,'b');
		$photo_m_url=smb($photo_s_url,'m');
	}else{
		$photo_m_url=HOST.'/res/noP.gif';
		$photo_b_url='';
	}
}elseif($submitok == 'ajax_user_chkusername'){
	if (str_len($uname) > 20 || str_len($uname)<3)json_exit(array('flag'=>0,'msg'=>'请输入正确的【登录帐号】'));
	$row = $db->ROW(__TBL_TG_USER__,'id',"uname='".$uname."'");
	if($row)json_exit(array('flag'=>0,'msg'=>'此【登录帐号】已被占用,请重新输入'));
	json_exit(array('flag'=>1,'msg'=>'新增成功，请继续完善'));
}elseif($submitok == 'addupdate'){
	if (str_len($uname) > 20 || str_len($uname)<3)json_exit(array('flag'=>0,'msg'=>'请输入正确的【登录帐号】'));
	$row = $db->ROW(__TBL_TG_USER__,'id',"uname='".$uname."'");
	if($row)json_exit(array('flag'=>0,'msg'=>'此【登录帐号】已被占用,请重新输入'));
	if($k!=1 && $k!=2 && $k!=3)json_exit(array('flag'=>0,'msg'=>'zeai_forbidden'));
	if (str_len($uname) > 20 || str_len($uname)<3)json_exit(array('flag'=>0,'msg'=>'请输入正确的【登录帐号】'));
	if (str_len($pwd) > 20 || str_len($pwd)<6)json_exit(array('flag'=>0,'msg'=>'请输入正确的【登录密码】'));
	if (str_len($nickname) > 50 || str_len($nickname)<1)json_exit(array('flag'=>0,'msg'=>'请输入正确的【网名昵称】如果是公司请填负责人姓名'));
	if (!ifint($grade) && $k==1)json_exit(array('flag'=>0,'msg'=>'请输入正确的【推广等级】'));
	$uname = dataIO($uname,'in',20);
	$nickname = dataIO($nickname,'in',25);
	$bz    = dataIO($bz,'in',1000);
	$kind  = intval($kind);
	$grade = intval($grade);
	$pwd   = md5(trim($pwd));
	$ip=getip();
	$row = $db->ROW(__TBL_TG_ROLE__,'title',"shopgrade=0 AND grade=".$grade,"num");
	$gradetitle=$row[0];
	if($k==2){
		if (!ifint($shopgrade))json_exit(array('flag'=>0,'msg'=>'请选择【'.$_SHOP['title'].'等级】'));
		if (str_len($title) > 100 || str_len($title)<2)json_exit(array('flag'=>0,'msg'=>'请输入【'.$_SHOP['title'].'名称】'));
		if (!ifint($shopkind))json_exit(array('flag'=>0,'msg'=>'请选择【行业分类】'));
		if (!ifint($yxq))json_exit(array('flag'=>0,'msg'=>'请选择【有效期限】'));
		$sjtime=ADDTIME;
		$sjtime2=ADDTIME+$yxq*86400;
		$shopgrade=intval($shopgrade);
		$title = dataIO($title,'in',200);
		$row = $db->ROW(__TBL_TG_ROLE__,'title',"grade=0 AND shopgrade=".$shopgrade,"num");
		$shopgradetitle=$row[0];
		$db->query("INSERT INTO ".__TBL_TG_USER__." (nickname,bz,kind,uname,pwd,regtime,regip,endtime,endip,title,shopgrade,shopflag,shopgradetitle,sjtime,sjtime2,shopkind,flag) VALUES ('$nickname','$bz',$kind,'".$uname."','".$pwd."',".ADDTIME.",'$ip',".ADDTIME.",'$ip','$title','$shopgrade',1,'$shopgradetitle','$sjtime','$sjtime2','$shopkind',2)");
	}elseif($k==1){
		$db->query("INSERT INTO ".__TBL_TG_USER__." (nickname,bz,grade,gradetitle,kind,uname,pwd,regtime,regip,endtime,endip,flag) VALUES ('$nickname','$bz',$grade,'$gradetitle',$kind,'".$uname."','".$pwd."',".ADDTIME.",'$ip',".ADDTIME.",'$ip',1)");
	}elseif($k==3){
		$db->query("INSERT INTO ".__TBL_TG_USER__." (nickname,bz,uname,pwd,regtime,regip,endtime,endip,buyflag,flag) VALUES ('$nickname','$bz','".$uname."','".$pwd."',".ADDTIME.",'$ip',".ADDTIME.",'$ip',1,2)");
	}
	$tg_uid = intval($db->insert_id());
	AddLog('【'.$logstr.'】->新增【'.$nickname.'（id:'.$tg_uid.'）】');
	json_exit(array('flag'=>1,'msg'=>'录入成功，请继续完善资料','url'=>'TG_u_mod.php?submitok=mod&k='.$k.'&tg_uid='.$tg_uid));
}elseif($submitok == 'zeai_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbpicname = setphotodbname('tmp',$file['tmp_name'],'',$tg_uid);/*$tmp*/
		if (!up_send($file,$dbpicname,$_UP['ifwaterimg'],$_UP['upBsize']))alert_adm("上传图片失败","");
		if (!ifpic($_ZEAI['up2']."/".$dbpicname))alert_adm("图片格式错误","-1");
		json_exit(array('flag'=>1,'tmpphoto'=>$dbpicname));
	}else{
		json_exit(array('flag'=>0));
	}
}elseif($submitok == 'del_photo_s_update'){
	$rt = $db->query("SELECT photo_s,nickname FROM ".__TBL_TG_USER__." WHERE id=".$tg_uid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$path_s = $row[0];$nickname = $row[0];
		if (!empty($path_s)){
			$path_m = smb($path_s,'m');$path_b = smb($path_s,'b');$path_blur = str_replace("_b.","_blur.",$path_b);
			@up_send_admindel($path_s.'|'.$path_m.'|'.$path_b.'|'.$path_blur);
		}
		$db->query("UPDATE ".__TBL_TG_USER__." SET photo_s='' WHERE id=".$tg_uid);
		AddLog('【推广员】->头像删除【'.$nickname.'（id:'.$tg_uid.'）】');
	}
	header("Location: ".SELF."?submitok=mod&tg_uid=".$tg_uid);
}elseif($submitok == 'ajax_pic_path_s_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('tmp',$file['tmp_name'],'');
		if (!up_send($file,$dbname,0,$_UP['upMsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$dbname=setpath_s($dbname);
		$newpic = $_ZEAI['up2']."/".$dbname;
		if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}elseif($submitok == 'ajax_photo_up'){
//	if (ifpostpic($file['tmp_name'])){
//		$dbpicname = setphotodbname('photo',$file['tmp_name'],$tg_uid.'_');
//		$_s = setpath_s($dbpicname);
//		if (!up_send($file,$dbpicname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize']))alert_adm("图片写入失败","");
//		if (!ifpic($_ZEAI['up2']."/".$_s))alert_adm("图片格式错误","-1");
//		$db->query("INSERT INTO ".__TBL_TG_PHOTO__." (tgid,path_s,flag,addtime) VALUES ($tg_uid,'$_s',1,".ADDTIME.")");
//		
//		json_exit(array('flag'=>1,'tmpphoto'=>$dbpicname));
//	}else{
//		json_exit(array('flag'=>0));
//	}
}elseif($submitok == 'ajax_photo_del'){
//	$row = $db->ROW(__TBL_TG_PHOTO__,"path_s","id=".$id);
//	if ($row){
//		$path_s = $row[0];
//		if (!empty($path_s)){
//			$path_b = smb($path_s,'b');
//			@up_send_admindel($path_s.'|'.$path_b);
//			$db->query("DELETE FROM ".__TBL_TG_PHOTO__." WHERE id=".$id);
//		}
//	}exit;
}elseif($submitok == 'delpicupdate'){
	if (!ifint($tg_uid))alert_adm_parent('forbidden','back');
	$row  = $db->ROW(__TBL_TG_USER__,"weixin_ewm","id=".$tg_uid,"num");
	$weixin_ewm = $row[0];
	@up_send_admindel($weixin_ewm.'|'.smb($weixin_ewm,'m').'|'.smb($weixin_ewm,'b'));
	$db->query("UPDATE ".__TBL_TG_USER__." SET weixin_ewm='' WHERE id=".$tg_uid);
	AddLog('【'.$_SHOP['title'].'】->删除'.$_SHOP['title'].'微信二维码【id:'.$tg_uid.'】');
	header("Location: ".SELF."?submitok=mod&tg_uid=$tg_uid&f=$f&g=$g&k=$k&p=$p");
}elseif($submitok == 'delpicupdate2'){
	if (!ifint($tg_uid))alert_adm_parent('forbidden','back');
	$row  = $db->ROW(__TBL_TG_USER__,"yyzz_pic","id=".$tg_uid,"num");
	$yyzz_pic = $row[0];
	@up_send_admindel($yyzz_pic.'|'.smb($yyzz_pic,'m').'|'.smb($yyzz_pic,'b'));
	$db->query("UPDATE ".__TBL_TG_USER__." SET yyzz_pic='' WHERE id=".$tg_uid);
	AddLog('【'.$_SHOP['title'].'】->删除'.$_SHOP['title'].'营业执照【id:'.$tg_uid.'】');
	header("Location: ".SELF."?submitok=mod&tg_uid=$tg_uid&f=$f&g=$g&k=$k&p=$p");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php if ($submitok == 'mod'){?>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var uid = <?php echo $tg_uid; ?>;
var up2='<?php echo $_ZEAI['up2'];?>/',upMaxMB=<?php echo $_UP['upMaxMB']; ?>;
function cut(id,nkname,p) {zeai.iframe('裁切【'+nkname+'】主图','tg_photo_cut.php?ifm=1&id='+uid+'&submitok=www___zeai__cn_inphotocut'+'&p='+p,650,560);}
function up_m(id,nkname) {
	var pic = o('pic');
	pic.click();
	pic.onchange = function(){
		var FILES = pic.files[0];
		if (FILES['size'] > upMaxMB*1024000){pic.value='';zeai.alert('图片【'+FILES['name']+'】太大，已超过'+upMaxMB+'M，请重新选择');return false;}
		var filename = FILES['name'].toLowerCase();
		var ftype    = filename.substring(filename.lastIndexOf("."),filename.length);
		if ((ftype != '.jpg')&&(ftype != '.jpeg')&&(ftype != '.gif')&&(ftype != '.png')){pic.value='';zeai.alert('只能上传 .jpg 或 .gif 格式的图片,请重新选择!');return false;}
		setTimeout(zeai.msg('<img src="images/loadingData.gif" class="picmiddle">图片【'+FILES['name']+'】正在上传中',{time:30}),300);
		//POST
		var postjson = {"submitok":"zeai_up","file":FILES,"tg_uid":uid};
		zeai.ajax({url:'TG_u_mod'+zeai.ajxext,data:postjson},function(e){var rs=zeai.jsoneval(e);
			pic.value='';
			zeai.msg('',{flag:'hide'});
			if (rs.flag == 1){
				zeai.iframe('裁切【'+nkname+'】主头像','tg_photo_cut.php?ifm=1&id='+uid+'&submitok=www___zeai__cn_inphotocut&tmpphoto='+rs.tmpphoto,650,560);
			}else{
				zeai.alert('上传图片出错，请联系原作者QQ：797311');
			}
		});
	}
}
</script>
<!-- editor -->
<link rel="stylesheet" href="editor/themes/default/default.css" />
<script charset="utf-8" src="editor/kindeditor.js?1"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js?1"></script>
<script>
var editor;
KindEditor.ready(function(K){
  editor=K.create('textarea[name="content"]',{
	resizeType :1,
	cssData:'body {font-family: "微软雅黑"; font-size: 14px}',
	minWidth : 400,
	allowPreviewEmoticons : true,
	allowImageUpload : true,
	afterBlur:function(){this.sync();},
	items : [
		'undo','redo','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','removeformat', '|', 'insertorderedlist','insertunorderedlist', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull','lineheight','|',
		'selectall','quickformat', '|','image','multiimage','media', '|','plainpaste','wordpaste', 'link', 'unlink','baidumap', '|','clearhtml','source', '|','preview','fullscreen']
  });
});
</script>
<!--editor end -->
<?php }?>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
/*uidsobox*/
.uidsobox{margin:100px auto 0 auto;border:#eee 1px solid;width:700px;line-height:100px;background-color:#f8f8f8}
.uidsobox .input{height:30px;line-height:30px}
/*table*/
.table td{padding:8px;border:1px solid #eee}
.table .tdL{color:#999}
a.noUW200 img{max-width:200px;display:block;cursor:zoom-in;}
.sexRW li{width:45%}
.SW{width:150px}
.table .tdL{width:100px}
.table .tdR{min-width:250px}
td.tdLbgHUI{background-color:#eee}
</style>
<body>
<div class="navbox">
	<?php if ($submitok == 'add'){?>
		<a class="ed">新增<?php echo $kstr;?></a>
    <?php }else{ ?>
        <a href="<?php echo SELF."?tg_uid=$tg_uid&f=$f&g=$g&k=$k&p=$p"; ?>&submitok=mod"<?php echo ($submitok == 'mod' || empty($submitok))?' class="ed"':''; ?>><?php echo $kstr.$cname.'【ID:'.$tg_uid.'】';?>基本信息</a>
        <?php if ($k == 2){?>
			<a href="<?php echo "TG_u_product.php?tg_uid=$tg_uid&k=$k"; ?>">商品管理<?php echo '<b>'.$db->COUNT(__TBL_TG_PRODUCT__,"tg_uid=".$tg_uid).'</b>';?></a>
        <?php }?>
    <?php }?>
<div class="clear"></div></div><div class="fixedblank">
</div>
<?php if ($submitok == 'add'){?>
	<script>
    function chkform(){
        if(zeai.empty(uname.value) || zeai.str_len(uname.value)<3 || zeai.str_len(uname.value)>20){
            zeai.msg('请输入正确的【登录帐号】',uname);return false;
        }
        if(zeai.empty(pwd.value) || zeai.str_len(pwd.value)<6 || zeai.str_len(pwd.value)>20){
            zeai.msg('请输入【登录密码】',pwd);return false;
        }
		<?php if($k==1){?>
        if(zeai.empty(grade.value)){
            zeai.msg('请选择【推广等级】');return false;
        }
		<?php }?>
        zeai.ajax({url:'TG_u_mod'+zeai.extname,form:GYLform},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);if (rs.flag == 1)setTimeout(function(){zeai.openurl(rs.url);},1000);
        });
    }
    </script>
	<style>.table .tdL{width:160px}.tips{margin-left:20px}</style>
	<form name="GYLform" id="GYLform" action="<?php echo SELF; ?>" method="post">
    <table class="table W900 Mtop30 size2">
    <tr><td class="tdL">主体类型</td><td class="tdR">
    <input type="radio" name="kind" id="kind1" class="radioskin" value="1"<?php echo ($kind == 1)?' checked':'';?>><label for="kind1" class="radioskin-label"><i class="i1"></i><b class="W50">个人</b></label>　　
    <input type="radio" name="kind" id="kind2" class="radioskin" value="2"<?php echo ($kind == 2 )?' checked':'';?>><label for="kind2" class="radioskin-label"><i class="i1"></i><b class="W50">公司</b></label>　　
    <input type="radio" name="kind" id="kind3" class="radioskin" value="3"<?php echo ($kind == 3)?' checked':'';?>><label for="kind3" class="radioskin-label"><i class="i1"></i><b class="W50">机构</b></label>　　
    </td></tr>


    <tr><td class="tdL"><font class="Cf00">*</font>登录帐号</td><td class="tdR"><input name="uname" id="uname" type="text" class="input size2 W200" value="tg<?php echo cdstr(5); ?>" size="20" maxlength="20"   autocomplete="off" /><span class="tips">3~15位英文字母或加数字组合；如：zeai，zeai_123</span></td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font>登录密码</td><td class="tdR C8d"><input name="pwd" id="pwd" type="text" class="input size2 W200"  size="20" maxlength="20"   autocomplete="off" /><span class="tips">6~20位英文字母或加数字组合</span></td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font>网名昵称</td><td class="tdR"><input name="nickname" id="nickname" type="text" class="input size2 W200" size="20" maxlength="20"   autocomplete="off" /><span class="tips">如果是公司可填负责人姓名</span></td></tr>
    
    <?php if($k==1){?>
    <tr><td class="tdL"><font class="Cf00">*</font>推广等级</td><td class="tdR">
		<select name="grade" id="grade" class=" size2 W200" required>
        <?php
        $rt2=$db->query("SELECT grade,title FROM ".__TBL_TG_ROLE__." WHERE shopgrade=0 AND flag=1 ORDER BY px DESC,id DESC");
        $total2 = $db->num_rows($rt2);
        if ($total2 <= 0) {
            alert_adm('【等级组】为空，请先去增加','TG_role.php');
        } else {
        ?>
        <option value="">请选择</option>
        <?php
            for($j=0;$j<$total2;$j++) {
                $rows2 = $db->fetch_array($rt2,'num');
                if(!$rows2) break;
                $clss=($grade==$rows2[0])?' selected':'';
                echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
            }
        }?>
        </select><span class="tips">奖励额度详情请点击【运营管理】->【推广等级套餐】中查看设置</span>
    </td></tr>
    <?php }?>
	<?php if($k==2){?>
	<tr><td class="tdL"><font class="Cf00">*</font><?php echo $_SHOP['title'];?>等级</td><td class="tdR">
		<select name="shopgrade" id="shopgrade" class=" size2 W200" required>
        <?php
        $rt2=$db->query("SELECT shopgrade,title FROM ".__TBL_TG_ROLE__." WHERE grade=0 AND flag=1 ORDER BY px DESC,id DESC");
        $total2 = $db->num_rows($rt2);
        if ($total2 <= 0) {
            alert_adm('【'.$_SHOP['title'].'等级组】为空，请先去增加','shop_role.php');
        } else {
        ?>
        <option value="">请选择</option>
        <?php
            for($j=0;$j<$total2;$j++) {
                $rows2 = $db->fetch_array($rt2,'num');
                if(!$rows2) break;
                $clss=($shopgrade==$rows2[0])?' selected':'';
                echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
            }
        }?>
        </select>
	</td></tr>
	<tr><td class="tdL"><font class="Cf00">*</font><?php echo $_SHOP['title'];?>名称</td><td class="tdR"><input name="title" type="text" class="W400" id="title"  value="<?php echo $title; ?>"></td></tr>
	<tr><td class="tdL"><font class="Cf00">*</font>行业分类</td><td class="tdR">
        <select name="shopkind" class="size2 W200">
			<?php
			$kindarr=json_decode($_SHOP['kindarr'],true);
			if (count($kindarr) >= 1 && is_array($kindarr)){
				echo '<option value="">请选择</option>';
				foreach ($kindarr as $V) {
					$clss=($shopkind==$kindid=$V['i'])?' selected':'';
					echo "<option value=".$kindid=$V['i'].$clss.">".dataIO($V['v'],'out')."</option>";
				}
			}else{
				alert_adm('【行业分类】为空，请先去增加','shop_role.php');
			}
			?>
		</select>    
    </td></tr>
	<tr><td class="tdL"><font class="Cf00">*</font>有效期限</td><td class="tdR">
        <input type="radio" name="yxq" id="yxq_7" class="radioskin" value="7"<?php echo ($yxq == 7)?' checked':'';?>><label for="yxq_7" class="radioskin-label"><i class="i1"></i><b class="W50">1周</b></label>
        <input type="radio" name="yxq" id="yxq_30" class="radioskin" value="30"<?php echo ($yxq == 30)?' checked':'';?>><label for="yxq_30" class="radioskin-label"><i class="i1"></i><b class="W50">1个月</b></label>
        <input type="radio" name="yxq" id="yxq_90" class="radioskin" value="90"<?php echo ($yxq == 90)?' checked':'';?>><label for="yxq_90" class="radioskin-label"><i class="i1"></i><b class="W50">3个月</b></label>
        <input type="radio" name="yxq" id="yxq_180" class="radioskin" value="180"<?php echo ($yxq == 180)?' checked':'';?>><label for="yxq_180" class="radioskin-label"><i class="i1"></i><b class="W50">6个月</b></label>
        <input type="radio" name="yxq" id="yxq_365" class="radioskin" value="365"<?php echo ($yxq == 365 || empty($yxq))?' checked':'';?>><label for="yxq_365" class="radioskin-label"><i class="i1"></i><b class="W50">1年</b></label>
        <input type="radio" name="yxq" id="yxq_730" class="radioskin" value="730"<?php echo ($yxq == 730)?' checked':'';?>><label for="yxq_730" class="radioskin-label"><i class="i1"></i><b class="W50">2年</b></label>
        <input type="radio" name="yxq" id="yxq_1095" class="radioskin" value="1095"<?php echo ($yxq == 1095)?' checked':'';?>><label for="yxq_1095" class="radioskin-label"><i class="i1"></i><b class="W50">3年</b></label>
    </td></tr>
    <?php }?> 
    
    <tr><td class="tdL">备　　注</td><td class="tdR"><textarea name="bz" rows="5" class="W98_ S14" placeholder="备注(500字节内，没有请留空)"></textarea></td></tr>
    
    <tr>
      <td class="tdL">&nbsp;</td>
      <td class="tdR"><input type="button" class="btn size3 HUANG3" value="下一步" onclick="return chkform();"/>
      <input name="submitok" type="hidden" value="addupdate" />
      <input name="k" type="hidden" value="<?php echo $k;?>" />
      </td>
    </tr>
    </table>
    </form>

<?php }elseif($submitok == 'mod' || $submitok == 'photo'){?>

<form action="<?php echo SELF; ?>" method="post" name="www_zeai_cn_FORM" onsubmit="return chkform();">

<table class="table0 Mtop10 Mbottom50 size2" style="width:1111px;">
<tr>
<td width="220" align="right" valign="top" bgcolor="#ffffff"><table class="table0 " style="border:#eee 1px solid">
<tr>
<td width="200" align="center" style="padding:6px">
<a href="javascript:;" class="noUW200 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo $photo_b_url; ?>');"><img src="<?php echo $photo_m_url;?>"></a>
</td>
</tr>

<?php if(!empty($photo_s)){?>
<tr><td height="50" align="center"><a href="javascript:cut(<?php echo $tg_uid; ?>,'<?php echo $id; ?>','<?php echo $p; ?>');" class="aHEI">裁切主图</a></td></tr>
<tr><td height="50" align="center"><a href="#" class="aHEI" id="del_photo_s">删除主图</a></td></tr>
<script>
del_photo_s.onclick=function(){
	zeai.confirm('确认删除主图？',function(){zeai.post('TG_u_mod'+zeai.extname,{submitok:'del_photo_s_update',tg_uid:<?php echo $tg_uid; ?>});	});
}
</script>
<?php }?>

<tr><td height="50" align="center">
<a href="javascript:up_m(<?php echo $tg_uid; ?>,'<?php echo $id; ?>');" class="aHEI">管理员上传主图</a>
<input id="pic" type="file" style="display:none;" />
</td></tr>

<tr><td height="40" align="center" class="S14">ID：<?php echo $tg_uid; ?></td></tr>
<?php if($flag!=2){?>
<tr style="border-bottom:#eee 1px solid"><td height="40" align="center" valign="top" class="S14 C999" id="grade<?php echo $tg_uid;?>">推广等级：<?php echo $gradetitle; ?></td></tr>
<?php }?>
<?php if($shopflag!=2){?>
<tr style="border-bottom:#eee 1px solid">
<td height="40" align="center" class="S14 C999" style="padding:10px 0">
<?php echo $_SHOP['title'];?>等级：<?php echo $shopgradetitle;?>
</td></tr>
<?php }?>

<?php if (ifint($tguid)){?>
<tr style="border-bottom:#eee 1px solid">
<td height="40" align="center" class="S14 C999" style="padding:10px 0">
<?php
	$row = $db->ROW(__TBL_TG_USER__,"uname,mob","id=".$tguid,"num");
	if ($row){
		$tguname=dataIO($row[0],'out');
		$tgmob=dataIO($row[1],'out');
		echo '<div>上级推荐人ID：'.$tguid.'</div>';
		//if(!empty($tguname))echo '<div class="C999">'.$tguname.'</div>';
		//if(!empty($tgmob))echo '<div class="C999">'.$tgmob.'</div>';
	}
	if($tgflag==1){
		echo '已奖励<font class="Cf00">'.str_replace(".00","",$tgmoney).'</font>元';
	}
?>
</td></tr>
<?php }?>
<tr>
  <td height="24" align="center" valign="bottom">

<br>
<table class="table0 W80_">
    <tr><td height="30">注册：<?php echo $regtime; ?></td></tr>
    <tr><td height="30">注册IP：<?php echo $regip; ?></td></tr>
    <tr><td height="30">最近：<?php echo $endtime; ?></td></tr>
    <tr><td height="30">最近IP：<?php echo $endip; ?></td></tr>
    <tr><td height="30">人气：<?php echo $click; ?></td></tr>
</table>
<?php if (!empty($longitude)){?>
<table class="table0 W80_ ">
<tr>
<td height="30">经度：<?php echo $longitude; ?></td>
<td width="50" rowspan="2" align="left" valign="middle">
<?php if (!empty($longitude)){ ?>
<a href="http://map.qq.com/?type=marker&isopeninfowin=1&markertype=1&pointx=<?php echo $longitude; ?>&pointy=<?php echo $latitude; ?>&name=当前位置&addr=当前位置&ref=myapp" target="_blank"><img src="images/gps.gif" style="display:inline"></a>
<?php }?>
</td>
</tr>
<tr>
<td height="30">纬度：<?php echo $latitude; ?></td>
</tr>
</table>
<?php }?>
  </td>
</tr>
<tr>
  <td height="24" align="center" valign="bottom">&nbsp;</td>
</tr>

</table>
  
  
  <br><br><br>
</td>
<td align="left" valign="top" style="padding:0">



<?php if ($submitok == 'photo'){ ?>



<?php }else{ ?>
<!--基本资料-->
    <table class="table W98_ size2">
    <tr><td height="20" colspan="4"><font class="S14">基本信息</font></td></tr>
    <!-- 基本资料 -->
	<script>
    function chkform(){}
    </script>

    <tr>
    <td class="tdL">主体类型</td>
    <td class="tdR">
<input type="radio" name="kind" id="kind1" class="radioskin" value="1"<?php echo ($kind == 1)?' checked':'';?>><label for="kind1" class="radioskin-label"><i class="i1"></i><b class="W50">个人</b></label>　　
<input type="radio" name="kind" id="kind2" class="radioskin" value="2"<?php echo ($kind == 2)?' checked':'';?>><label for="kind2" class="radioskin-label"><i class="i1"></i><b class="W50">公司</b></label>　　
<input type="radio" name="kind" id="kind3" class="radioskin" value="3"<?php echo ($kind == 3)?' checked':'';?>><label for="kind3" class="radioskin-label"><i class="i1"></i><b class="W50">机构</b></label></td>
      <td class="tdL">公众号</td>
      <td class="tdR">
        <input type="checkbox" name="subscribe" id="subscribe" class="switch" value="1"<?php echo ($subscribe == 1)?' checked':'';?>><label for="subscribe" class="switch-label"><i></i><b>已关注</b><b>未关注</b></label>
    </td>
    </tr>
    
	<tr>
      <td class="tdL">登录帐号</td>
      <td class="tdR"><input name="uname" type="text" class="W150" id="uname" value="<?php echo $uname;?>" maxlength="30" /></td>
      <td class="tdL">登录密码</td>
      <td class="tdR"><input name="pwd" type="text" class="W150" id="pwd"/><span class="tips">不修改或没有请留空</span></td>
    </tr>

		<?php 
        $RZARR = explode(',',$RZ);
        ?>
      <tr>
        <td class="tdL">手机号码</td>
        <td class="tdR">
        
        	<input name="mob" type="text" class="W150" id="mob" value="<?php echo $mob; ?>" maxlength="11"><?php echo $rzstr;?>　
        
			<input type="checkbox" name="rz_mob" id="rz_mob" class="checkskin " value="mob"<?php echo (@in_array('mob',$RZARR) && ifmob($mob))?' checked':'';?>><label for="rz_mob" class="checkskin-label"><i class="i1"></i><b class="W100">已认证</b></label>
        
        </td>
        <td class="tdL">上级推荐人ID</td>
        <td class="tdR"><input name="tguid" type="text" class="W100" id="tguid" value="<?php echo $tguid;?>" maxlength="8" /></td>
      </tr>
    <tr>
      <td class="tdL">绑定单身UID</td>
      <td class="tdR"><input name="uid" type="text" class="W100" id="uid" value="<?php echo $uid; ?>" maxlength="8"><span class="tips">绑定后在【我的】可直接进入</span></td>
      <td class="tdL">排序</td>
      <td class="tdR"><input name="px" type="text" class="W100" id="px" value="<?php echo $px;?>" maxlength="8" /><span class="tips">排序/排名，正整数，值大靠前</span></td>
    </tr>
    <tr>
      <td class="tdL">Openid</td>
      <td class="tdR"><input name="openid" type="text" class="W300" id="openid" value="<?php echo $openid;?>" maxlength="100" /></td>
      <td class="tdL">&nbsp;</td>
      <td class="tdR"></td>
    </tr>

	<tr><td height="50" colspan="4" valign="bottom" style="border:0"><font class="S14">买家信息</font></td></tr>
        <tr>
      <td class="tdL">买家帐号状态</td>
      <td colspan="3" class="tdR">
        <input type="radio" name="buyflag" id="buyflag_1" class="radioskin" value="-1"<?php echo ($buyflag == -1)?' checked':'';?>><label for="buyflag_1" class="radioskin-label"><i class="i1"></i><b class="W50">锁定</b></label>　　
        <input type="radio" name="buyflag" id="buyflag1" class="radioskin" value="1"<?php echo ($buyflag == 1)?' checked':'';?>><label for="buyflag1" class="radioskin-label"><i class="i1"></i><b class="W50">正常</b></label>　<span class="tips">锁定后将不能购买商品，<?php echo $_SHOP['title'];?>“我的”也无法进入</span>
      </td>
    </tr>
    
    <tr><td height="50" colspan="4" valign="bottom" style="border:0"><font class="S14">推广信息</font></td></tr>
    <tr>
    <td class="tdL">推广帐号状态</td>
    <td colspan="3" class="tdR">
    <input type="radio" name="flag" id="flag2" class="radioskin" value="2"<?php echo ($flag == 2)?' checked':'';?>><label for="flag2" class="radioskin-label"><i class="i1"></i><b class="W50">未激活</b></label>　　
    <input type="radio" name="flag" id="flag0" class="radioskin" value="0"<?php echo ($flag == 0)?' checked':'';?>><label for="flag0" class="radioskin-label"><i class="i1"></i><b class="W50">未审</b></label>　　
    <input type="radio" name="flag" id="flag_1" class="radioskin" value="-1"<?php echo ($flag == -1)?' checked':'';?>><label for="flag_1" class="radioskin-label"><i class="i1"></i><b class="W50">锁定</b></label>　　
    <input type="radio" name="flag" id="flag1" class="radioskin" value="1"<?php echo ($flag == 1)?' checked':'';?>><label for="flag1" class="radioskin-label"><i class="i1"></i><b class="W50">正常</b></label>　　
    </td>
    </tr>

    <tr>
      <td class="tdL">昵称</td>
      <td class="tdR"><input name="nickname" type="text" class="W150" id="nickname" maxlength="25"  value="<?php echo $nickname; ?>"></td>
      <td class="tdL">职位</td>
      <td class="tdR"><input name="job" type="text" class="W300" id="job"  value="<?php echo $job; ?>"></td>
    </tr>

    
    <tr>
      <td class="tdL">推广等级</td>
      <td colspan="3" class="tdR">
    <select name="grade" id="grade" class="SW" required>
	<?php
    $rt2=$db->query("SELECT grade,title FROM ".__TBL_TG_ROLE__." WHERE shopgrade=0 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('【等级组】为空，请先去增加','TG_role.php');
    } else {
    ?>
    <option value="">请选择</option>
    <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
			$clss=($grade==$rows2[0])?' selected':'';
            echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
        }
    }
    ?></select>   
      
     </td>
    </tr>    


    
    
    </table>
    <!--店铺-->
    <table class="table W98_ size2">
    <tr><td height="50" colspan="4" valign="bottom" style="border:0"><font class="S14"><?php echo $_SHOP['title'];?>信息</font>
    <?php if ($k == 2){?>
    <button type="button" class="btn size2 HUANG3 FR" onClick="zeai.openurl('<?php echo "TG_u_product.php?tg_uid=$tg_uid"; ?>&k=2')"><i class="ico add">&#xe620;</i> 商品/服务管理</button>
    <?php }?></td></tr>

        <tr>
      <td class="tdL"><?php echo $_SHOP['title'];?>帐号状态</td>
      <td colspan="3" class="tdR">
<input type="radio" name="shopflag" id="shopflag2" class="radioskin" value="2"<?php echo ($shopflag == 2)?' checked':'';?>><label for="shopflag2" class="radioskin-label"><i class="i1"></i><b class="W50">未激活</b></label>　　

<input type="radio" name="shopflag" id="shopflag0" class="radioskin" value="0"<?php echo ($shopflag == 0)?' checked':'';?>><label for="shopflag0" class="radioskin-label"><i class="i1"></i><b class="W50">未审</b></label>　　
<input type="radio" name="shopflag" id="shopflag_1" class="radioskin" value="-1"<?php echo ($shopflag == -1)?' checked':'';?>><label for="shopflag_1" class="radioskin-label"><i class="i1"></i><b class="W50">锁定</b></label>　　
<input type="radio" name="shopflag" id="shopflag_2" class="radioskin" value="-2"<?php echo ($shopflag == -2)?' checked':'';?>><label for="shopflag_2" class="radioskin-label"><i class="i1"></i><b class="W50">隐藏</b></label>　　

<input type="radio" name="shopflag" id="shopflag1" class="radioskin" value="1"<?php echo ($shopflag == 1)?' checked':'';?>><label for="shopflag1" class="radioskin-label"><i class="i1"></i><b class="W50">正常</b></label>　　

      </td>
    </tr>   


    <tr>
      <td class="tdL "><?php echo $_SHOP['title'];?>名称</td>
      <td class="tdR"><input name="title" type="text" class="W300" id="title"  value="<?php echo $title; ?>"></td>
      <td class="tdL "><?php echo $_SHOP['title'];?>等级</td>
      <td class="tdR">
			<select name="shopgrade" class="W300 size2">
			<?php
			$rt2=$db->query("SELECT shopgrade,title FROM ".__TBL_TG_ROLE__." WHERE grade=0 ORDER BY px DESC,id DESC");
			$total2 = $db->num_rows($rt2);
			if ($total2 <= 0) {
				alert_adm('【等级组】为空，请先去增加','shop_role.php');
			} else {
			?>
			<option value="">请选择</option>
			<?php
				for($j=0;$j<$total2;$j++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$clss=($shopgrade==$rows2[0])?' selected':'';
					echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
				}
			}
			?></select>	
		</td>
    </tr>
    <tr>
      <td class="tdL ">服务起始时间</td>
      <td class="tdR"><input name="sjtime" type="text" class="W300" id="sjtime"  value="<?php echo $sjtime; ?>" maxlength="50" placeholder="格式：<?php echo YmdHis(ADDTIME);?>"></td>
      <td class="tdL ">服务结束时间</td>
      <td class="tdR"><input name="sjtime2" type="text" class="W300" id="sjtime2"  value="<?php echo $sjtime2; ?>" maxlength="50" placeholder="格式：<?php echo YmdHis(ADDTIME);?>"></td>
    </tr>
    <tr>
      <td class="tdL ">行业分类</td>
      <td class="tdR">
        <select name="shopkind" class="W300 size2">
			<?php
			$kindarr=json_decode($_SHOP['kindarr'],true);
			if (count($kindarr) >= 1 && is_array($kindarr)){
				echo '<option value="">请选择</option>';
				foreach ($kindarr as $V) {
					$clss=($shopkind==$V['i'])?' selected':'';
					echo "<option value=".$kindid=$V['i'].$clss.">".dataIO($V['v'],'out')."</option>";
				}
			}else{
				alert_adm('【行业分类】为空，请先去增加','shop_role.php');
			}
			?>
		</select>      
      </td>
      <td class="tdL ">营业时间</td>
      <td class="tdR"><input name="worktime" type="text" class="W300" id="worktime"  value="<?php echo $worktime; ?>" placeholder="格式：09:00~22:00"></td>
    </tr>

    
    <tr>
      <td class="tdL">所在地区</td>
      <td colspan="3" class="tdR">
         <script>LevelMenu4('a1|a2|a3|a4|请选择|<?php echo $a1; ?>|<?php echo $a2; ?>|<?php echo $a3; ?>|<?php echo $a4; ?>|areaid|areatitle|<?php echo $areatitle;?>');</script>
        </td>
    </tr>
    <tr>
      <td class="tdL">电话</td>
      <td class="tdR"><input name="tel" type="text" class="W300" id="tel" value="<?php echo $tel; ?>"></td>
      <td class="tdL">微信号</td>
      <td class="tdR"><input name="weixin" type="text" class="W300" id="weixin" value="<?php echo $weixin; ?>"></td>
    </tr>
    <tr>
      <td class="tdL">邮箱</td>
      <td class="tdR"><input name="email" type="text" class="W300" id="email" value="<?php echo $email; ?>"></td>
      <td rowspan="2" class="tdL">微信二维码</td>
      <td rowspan="2" class="tdR">
      
		<?php if (!empty($weixin_ewm)){
			$weixin_ewm_url=$_ZEAI['up2'].'/'.$weixin_ewm;?>    
            <a class="pic60" onClick="parent.piczoom('<?php echo $weixin_ewm_url; ?>')"><img src="<?php echo $weixin_ewm_url; ?>" class="m"></a>　
            <a class="btn size1" onClick="parent.zeai.confirm('确认删除重新上传么？',function(){zeai.openurl('TG_u_mod'+zeai.ajxext+'submitok=delpicupdate&tg_uid=<?php echo $tg_uid."&f=$f&g=$g&k=$k&p=$p"; ?>');})">删除</a>
            <span class="tips S12">删除后可更换</span>　  
		<?php }else{echo "<input name='pic1' type='file' size='50' class='Caaa size2 W300' />";}?>  
        <br><span class='tips S12'>jpg/gif/png格式，正方形，宽高500*500像数以内</span>
      
      </td>
    </tr>
    <tr>
      <td class="tdL">QQ</td>
      <td class="tdR"><input name="qq" type="text" class="W300" id="qq" value="<?php echo $qq; ?>"></td>
      </tr>
    <tr>
      <td class="tdL"><?php echo $_SHOP['title'];?>地址</td>
      <td colspan="3" class="tdR"><input name="address" type="text" id="address" value="<?php echo $address; ?>" maxlength="100" style="width:446px"></td>
    </tr>
    <tr>
      <td class="tdL"><?php echo $_SHOP['title'];?>坐标定位</td>
      <td colspan="3" class="tdR C999">
        经度：<input name="longitude" type="text" id="longitude" value="<?php echo $longitude; ?>" maxlength="20" >　
        纬度：<input name="latitude" type="text" id="latitude" value="<?php echo $latitude; ?>" maxlength="20" >　
        <a href="javaacript:;" id="Zeai_map_btn" class="btn size2"><i class="ico">&#xe614;</i> 标注位置</a>
        <script>
			Zeai_map_btn.onclick=function(){
			var lng=longitude.value,lat=latitude.value;
			zeai.iframe('定位标注','map_set.php?var1=longitude&var2=latitude&longitude='+lng+'&latitude='+lat+'&areatitle=<?php echo $areatitle; ?>',630,550);}
		</script>
        </td>
    </tr>
    
    <tr>
      <td class="tdL">线下取货地点</td>
      <td colspan="3" class="tdR"><input name="qhdz" type="text" id="qhdz" value="<?php echo $qhdz; ?>" maxlength="100" style="width:446px"></td>
    </tr>
    <tr>
      <td class="tdL">取货备注说明</td>
      <td colspan="3" class="tdR"><input name="qhbz" type="text" id="qhbz" value="<?php echo $qhbz; ?>" maxlength="100" style="width:446px"></td>
    </tr>
    
    <tr>
      <td class="tdL">取货地点定位</td>
      <td colspan="3" class="tdR C999">
        经度：<input name="qhlongitude" type="text" id="qhlongitude" value="<?php echo $qhlongitude; ?>" maxlength="20" >　
        纬度：<input name="qhlatitude" type="text" id="qhlatitude" value="<?php echo $qhlatitude; ?>" maxlength="20" >　
        <a href="javaacript:;" id="Zeai_map_btn2" class="btn size2"><i class="ico">&#xe614;</i> 标注位置</a>
        <script>
			Zeai_map_btn2.onclick=function(){
			var lng2=qhlongitude.value,lat2=qhlatitude.value;
			zeai.iframe('定位标注','map_set.php?var1=qhlongitude&var2=qhlatitude&longitude='+lng2+'&latitude='+lat2+'&areatitle=<?php echo $areatitle; ?>',630,550);}
		</script>
        </td>
    </tr>
    
    <tr>
      <td class="tdL">介绍/详情</td>
      <td colspan="3" class="tdR"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999;font-size:12px">如果从公众号编辑器或外部网页或Word里拷入内容请先过虑垃圾代码，请点击下方 <img src="images/cclear.png" class="picmiddle"> 图标，然后插入文字内容</font>
        <textarea name="content" id="content" class="textarea_k" style="width:100%;height:300px" ><?php echo $content;?></textarea>      
      </td>
    </tr>
    <tr>
      <td class="tdL"><?php echo $_SHOP['title'];?>图片展示</td>
      <td colspan="3" class="tdR">
        <div class="picli100" id="picli_pathlist">
        	<li class="add" id="pathlist_add"></li>
			<?php
            if(!empty($piclist)){
                $ARR=explode(',',$piclist);
                $piclist=array();
                foreach ($ARR as $V) {
                   echo '<li><img src="'.$_ZEAI['up2'].'/'.$V.'"><i></i></li>';
				}
            }?>      
        </div>
        <script>
        window.onload=function(){end();}
		zeai.photoUp({
			btnobj:pathlist_add,
			upMaxMB:upMaxMB,
			url:"TG_u_mod"+zeai.extname,
			multiple:5,
			submitok:"ajax_pic_path_s_up",
			end:function(rs){end();},
			li:function(rs){zeai.msg(0);zeai.msg(rs.msg);if (rs.flag == 1){picli_pathlist.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');}}
		});
		function end(){
			var i=zeai.tag(picli_pathlist,'i'),img=zeai.tag(picli_pathlist,'img');if(zeai.empty(i))return;
			for(var k=0;k<img.length;k++) {(function(k){var src=img[k].src;img[k].onclick = function(){parent.piczoom(src.replace('_s.','_b.'));}})(k);}
			for(var k=0;k<i.length;k++) {(function(k){i[k].onclick = function(){var thiss=this;zeai.confirm('亲~~确认删除么？',function(){thiss.parentNode.remove();pathlistReset();});}})(k);}
			function pathlistReset(){var img=zeai.tag(picli_pathlist,'img'),pathlist=[],src;for(var k=0;k<img.length;k++){var src=img[k].src.replace(up2,'');pathlist.push(src);}o('pathlist').value=pathlist.join(",");}
			pathlistReset();
		}
        </script>
        
      </td>
    </tr>
    <tr>
      <td class="tdL">营业执照</td>
      <td colspan="3" class="tdR">
		<?php if (!empty($yyzz_pic)){
			$yyzz_pic_url=$_ZEAI['up2'].'/'.$yyzz_pic;?>    
            <a class="pic60" onClick="parent.piczoom('<?php echo smb($yyzz_pic_url,'b'); ?>')"><img src="<?php echo $yyzz_pic_url; ?>" class="m"></a>　
            <a class="btn size1" onClick="parent.zeai.confirm('确认删除重新上传么？',function(){zeai.openurl('TG_u_mod'+zeai.ajxext+'submitok=delpicupdate2&tg_uid=<?php echo $tg_uid."&f=$f&g=$g&k=$k&p=$p"; ?>');})">删除</a>
            <span class="tips S12">删除后可更换</span>　  
		<?php }else{echo "<input name='pic2' type='file' size='50' class='Caaa size2 W300' />";}?>  
        <br><span class='tips S12'>必须为jpg/gif/png格式</span>      
      </td>
    </tr>
    
    </table>
    <!--公共部分-->
	<table class="table W98_ size2">
    <tr><td colspan="4" valign="bottom" style="border:0;height:50px"><font class="S14">提现收款帐号：</font><span class="tips C999">（如果开通微信支付，将直接打款打账到对方微信钱包，以下只做备注方便人工打款）</span></td></tr>

    <tr><td class="tdL">银行卡</td>
      <td colspan="3" class="tdR C999">
        银行名称 <input name="bank_name" type="text" id="bank_name" value="<?php echo $bank_name;?>" maxlength="100" style="width:280px" />　
        开户行名称 <input name="bank_name_kaihu" type="text" id="bank_name_kaihu" value="<?php echo $bank_name_kaihu;?>" maxlength="100"  style="width:280px" />
        <div style="margin-top:8px;">
        银行卡号 <input name="bank_card" type="text"  id="bank_card" value="<?php echo $bank_card;?>" maxlength="50"  style="width:280px" />　 　卡号姓名 <input name="bank_truename" type="text" class="W150" id="bank_truename" value="<?php echo $bank_truename;?>" maxlength="20" />
        </div>
      </td></tr>
    <tr>
    <td class="tdL">支付宝</td>
    <td colspan="3" class="tdR C999">
    支付宝姓名 <input name="alipay_truename" type="text" class="W150" id="alipay_truename" value="<?php echo $alipay_truename;?>" maxlength="10" />　　
    支付宝账号 <input name="alipay_username" type="text" class="W300" id="alipay_username" value="<?php echo $alipay_username;?>" maxlength="100" />
    
    </td>
    </tr>
    <tr><td colspan="4" style="border:0">&nbsp;</td></tr>
    <tr>
      <td class="tdL">备注</td>
      <td colspan="3" class="tdR"><textarea name="bz" rows="2" class="W100_" id="bz" placeholder="内部显示，前端不展示"><?php echo $bz; ?></textarea></td>
    </tr>
    </table>
<!--基本资料，详细资料，择偶要求 结束-->
<?php }?>
<!--  -->
</td>
</tr>
</table>
<br><br><br><br><br>
<?php if($submitok == 'mod'){?>
<div class="savebtnbox">
    <input name="tg_uid" type="hidden" value="<?php echo $tg_uid; ?>" />
    <input name="username_old" type="hidden" value="<?php echo $uname; ?>" />
    <input name="openid_old" type="hidden" value="<?php echo $openid; ?>" />
    <input name="email_old" type="hidden" value="<?php echo $email; ?>" />
    <input name="mob_old" type="hidden" value="<?php echo $mob; ?>" />
    <input name="f" type="hidden" value="<?php echo $f; ?>" />
    <input name="g" type="hidden" value="<?php echo $g; ?>" />
    <input name="k" type="hidden" value="<?php echo $k; ?>" />
    <input name="Skey" type="hidden" value="<?php echo $Skey; ?>" />
    <input name="pathlist" id="pathlist" type="hidden" value="" />
    <input name="submitok" type="hidden" value="modupdate">
    <button type="button" class="btn size3 HUANG3" id="supdesbtn">确认并保存</button>
</div>
<script>
supdesbtn.onclick=function(){
	zeai.ajax({url:'TG_u_mod'+zeai.extname,form:www_zeai_cn_FORM},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){setTimeout(function(){zeai.openurl(rs.url);},1000);}
	});
}
</script>
<?php }?>
</form>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);laydate.render({elem: '#sjtime',type: 'datetime'});laydate.render({elem: '#sjtime2',type: 'datetime'});</script>
<?php }?>
<?php
require_once 'bottomadm.php';?>
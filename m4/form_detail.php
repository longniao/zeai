<?php
ob_start();
if (ini_get('session.auto_start') == 0)session_start();
require_once '../sub/init.php';
if(is_weixin()){
	if(empty($cook_openid)){
		$server_openid = wx_get_openid();
	}else{
		$server_openid = $cook_openid;
	}
}
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_sms.php';
$rzmaxnum=15;$certrzmaxnum = 'maxnum'.$cook_form_id.$cook_form_uid;
if($submitok == 'ajax_pay'){
	if($rzkind=='identity'){
		if($_SMS['rz_mobile3']!=1)json_exit(array('flag'=>0,'msg'=>'自助实名已关闭'));
		if(!ifmob($form_mob))json_exit(array('flag'=>0,'msg'=>'请输入【手机号码】'));
		$rztitle='表单登记-实名认证';
	}elseif($rzkind=='photo'){
		$rztitle='表单登记-真人认证';
		if(empty($formphoto) || !file_exists(ZEAI.'up'.DIRECTORY_SEPARATOR.$formphoto) )json_exit(array('flag'=>0,'msg'=>'请自拍或上传本人正脸照片'));
	}else{json_exit(array('flag'=>0,'msg'=>'error:form rzkind lose'));}
	$cook_form_id=intval($cook_form_id);$cook_form_uid=intval($cook_form_uid);
	$truename=trim($form_truename);
	$truename=dataIO($truename,'in',12);$identitynum=dataIO($form_identitynum,'in',18);$mob=$form_mob;
	if(empty($truename) || !ifsfz($identitynum))json_exit(array('flag'=>0,'msg'=>'请输入真实的【姓名】和【身份证号码】'));
	$row = $db->ROW(__TBL_FORM_U__,"id","rz_identitynum='$identitynum' AND rz_identitynum<>'' AND id<>".$cook_form_uid." AND fid=".$cook_form_id);
	if ($row)json_exit(array('flag'=>0,'msg'=>'此【身份证号码】已被登记使用，请更换'));
	$row = $db->ROW(__TBL_FORM_U__,"id","mob='$mob' AND mob<>'' AND id<>".$cook_form_uid." AND fid=".$cook_form_id);
	if ($row)json_exit(array('flag'=>0,'msg'=>'此手机号码已被登记使用，请更换'));
	$row = $db->ROW(__TBL_FORM__,"rz_price","id=".intval($cook_form_id),"name");
	if ($row){
		if ($_COOKIE["$certrzmaxnum"] > $rzmaxnum )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多'));
		$rz_price = floatval($row['rz_price']);
		$orderid  = 'FORM-'.$cook_form_id.'-'.$cook_form_uid.'-'.date("YmdHis");
		//tmp
		if($rzkind=='photo')$c['formphoto']=$formphoto;
		$c['rzkind']=$rzkind;
		$c['form_id']=$cook_form_id;
		$c['form_uid']=$cook_form_uid;
		$c['mob']=$form_mob;
		$c['orderid']=$orderid;
		$c['truename']=$truename;
		$c['identitynum']=$identitynum;
		$c = encode_json($c);
		$db->query("INSERT INTO ".__TBL_TMP__." (c) VALUES ('$c')");
		$tmpid = $db->insert_id();
		//tmp end
		if($_COOKIE["$certrzmaxnum"] >= 1 && $_COOKIE["$certrzmaxnum"] < $rzmaxnum || $rz_price==0){
			$rflag=2;//继续免费认证
		}else{
			$rflag=1;
		}
		$return_url= HOST.'/m4/form_detail.php?id='.$cook_form_id.'&submitok='.$rzkind.'&ifpayok=1&tmpid='.$tmpid;
		$jumpurl = $return_url;
		json_exit(array('flag'=>$rflag,'money'=>$rz_price,'orderid'=>$orderid,'form_id'=>$cook_form_id,'form_uid'=>$cook_form_uid,'title'=>$rztitle,'return_url'=>$return_url,'jumpurl'=>$jumpurl));
	}
	json_exit(array('flag'=>0,'msg'=>'当前订单状态异常'));
}elseif($submitok=='ajax_cert_auto'){
	if ($_COOKIE["$certrzmaxnum"] > $rzmaxnum )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多'));
	start_net_verify($tmpid);
}

$id=intval($id);
$row = $db->ROW(__TBL_FORM__,"title,content,path_s,form_data,flag,ifadmlist,agree_reg,agree_wxshare,rz_mob,rz_identity,rz_photo,rz_price,wxshareT,wxshareC,stopurl,privateC","id=".$id,"name");
if ($row){
	$title        = trimhtml(dataIO($row['title'],'out'));
	$content      = dataIO($row['content'],'out');
	$form_data    = $row['form_data'];
	$form_data_px = $row['form_data_px'];
	$path_s       = $row['path_s'];
	$flag         = $row['flag'];
	$agree_reg     = $row['agree_reg'];
	$agree_wxshare = $row['agree_wxshare'];
	$rz_mob        = $row['rz_mob'];
	$rz_identity   = $row['rz_identity'];
	$rz_photo      = $row['rz_photo'];
	$rz_price      = $row['rz_price'];
	$form_data    = explode(',',$form_data);
	$ifadmlist    = $row['ifadmlist'];
	$wxshareT     = dataIO($row['wxshareT'],'wx');
	$wxshareC     = dataIO($row['wxshareC'],'wx');
	$stopurl      = dataIO($row['stopurl'],'out');
	$privateC     = dataIO($row['privateC'],'out');
	if($flag==-1){
		$urll=(!empty($stopurl))?$stopurl:HOST;
		header("Location: $urll");	
	}
	$db->query("UPDATE ".__TBL_FORM__." SET click=click+1 WHERE id=".$id);
	setcookie("cook_form_id",$id,time()+720000,"/",$_ZEAI['CookDomain']);
}else{
	alert('该表单已结束或不存在',HOST);
}
$data_disable=array('tag1','tag2','age');
function data_data_title($data_data,$f,$kind='') {foreach($data_data as $v){if($v['fieldname'] == $f)if($kind=='subkind'){return $v['subkind'];}else{return $v['title'];}}}
$rt = $db->query("SELECT fieldname,title,subkind FROM ".__TBL_UDATA__." WHERE flag=1 ORDER BY px DESC,id DESC");
while($tmprows = $db->fetch_array($rt,'name')){
	if (strstr($tmprows['fieldname'],'crm_') || in_array($tmprows['fieldname'],$data_disable) )continue;
	if($tmprows['fieldname']=='heigh' || $tmprows['fieldname']=='weigh')$tmprows['subkind']=2;
	if($tmprows['fieldname']=='parent')$tmprows['title']='替谁征婚';
	if($rz_mob==1 && $tmprows['fieldname']=='mob')$tmprows['subkind']=5;
	$data_data[]=$tmprows;
}
switch ($submitok) {
	case 'ajax_Zeai_mob_verify_get':
		require_once ZEAI.'cache/config_sms.php';
		require_once ZEAI.'sub/www_zeai_cn_sms.php';
		$row = $db->ROW(__TBL_FORM__,"flag","id=".$cook_form_id,"name");
		if (!$row || $row['flag'] != 1){exit(JSON_ERROR);}else{
			if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'【手机号码】不正确'));
			if ($Temp_formyzmrenum > $_SMS['sms_yzmnum'] && $_SMS['sms_yzmnum']>0 )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
			$row = $db->ROW(__TBL_FORM_U__,"mob","mob='$mob' AND mob<>'' AND fid=".$cook_form_id);
			if ($row)json_exit(array('flag'=>0,'msg'=>'【'.$mob.'】此手机号码已被登记使用，请更换'));
			$_SESSION['Zeai_cn__verify'] = cdstr(4);
			//sms
			$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__verify']);
			if ($rtn == 0){
				setcookie("Temp_formyzmrenum",$Temp_formyzmrenum+1,time()+720000,"/",$_ZEAI['CookDomain']);  
				$chkflag = 1;
				$content = '验证码发送成功，请注意查收';//.$_SESSION['Zeai_cn__verify']
			}else{
				$chkflag = 0;
				$content = "错误码：$rtn"."-".sms_error($rtn);
			}
			//sms end
			$_SESSION['Zeai_cn__mob'] = $mob;
			json_exit(array('flag'=>$chkflag,'msg'=>$content));
		}
	break;
	case 'ajax_Zeai_mob_verify_chk':
		if ($_SESSION['Zeai_cn__verify'] != $verify)json_exit(array('flag'=>0,'msg'=>'【短信验证码】不正确'));
		if ($_SESSION['Zeai_cn__mob'] != $mob)json_exit(array('flag'=>0,'msg'=>'【手机号码】获取异常，请重新输入'));
		json_exit(array('flag'=>1));
	break;
	case 'ajax_photo_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],$id.'_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s = setpath_s($dbname);
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_photo_up_wx':
		if (str_len($serverIds) > 15){
			$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$serverIds;
			$dbname = wx_get_up('tmp',$url,$id.'_','SMB');
			$_s = setpath_s($dbname);
			@up_send_userdel(smb($_s,'blur'),$delvar);
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
		}
	break;
	case 'ajax_photo_up_app':
		$f=$_FILES['file'];
		$dbname = setphotodbname('tmp',$file['tmp_name'],$id.'_');
		if (!up_send($f,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$_s = setpath_s($dbname);
		json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
	break;
	case 'ajax_facephoto_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_form_uid.'_RZ_');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$dbname));
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));}
	break;
	case 'ajax_facephoto_up_wx':
		if (str_len($serverIds) > 15){
			$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$serverIds;
			$dbname = wx_get_up('tmp',$url,$cook_form_uid,'B');
			json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$dbname));
		}else{json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));}
	break;
	case 'ajax_tmp_del':
		$url = str_replace($_ZEAI['up2']."/","",$url);
		if(!empty($url))@up_send_userdel($url.'|'.smb($url,'m').'|'.smb($url,'b').'|'.smb($url,'blur'),'guest_userdelpic');
		json_exit(array('flag'=>1,'url'=>$url));
	break;
	case 'ajax_next':
		if(empty($FORM) || $FORM=='{}')json_exit(array('flag'=>0,'msg'=>'请选择或输入登记资料'));
		$ARR=json_decode($FORM,true);
		$pushUinfo='';
		foreach($form_data as $F){
			$T = data_data_title($data_data,$F);
			if(empty($ARR[$F])){
				if($F=='photo_s'){
					json_exit(array('flag'=>0,'msg'=>'【'.$T.'】必填项，请上传个人近照'));
				}else{
					json_exit(array('flag'=>0,'msg'=>'【'.$T.'】必填项，不能为空哦'));
				}
			}
			switch ($F) {
				case 'identitynum':
					if(!ifsfz($ARR[$F]))json_exit(array('flag'=>0,'msg'=>'【'.$T.'】请输入正确的身份证号'));
				break;
				case 'aboutus':
					if(str_len($ARR[$F])>2000)json_exit(array('flag'=>0,'msg'=>'【'.$T.'】字太多，请控制在1000个字以内'));
				break;
				case 'nickname':
					if(str_len($ARR[$F])>50)json_exit(array('flag'=>0,'msg'=>'【'.$T.'】字太多，请控制在50个字以内'));
					$pushUinfo.='　'.$ARR[$F].'　';
				break;
				case 'truename':
					if(str_len($ARR[$F])>12)json_exit(array('flag'=>0,'msg'=>'【'.$T.'】字太多，请控制在6个字以内'));
					$pushUinfo.='　'.$ARR[$F].'　';
				break;
				case 'sex':
					$pushUinfo.='　'.udata($F,$ARR[$F]).'　';
				break;
				case 'mob':
					$mob=$ARR[$F];
					if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'【'.$T.'】请输入正确的手机号码'));
					$row = $db->ROW(__TBL_FORM_U__,"mob","mob='$mob' AND mob<>'' AND fid=".$id);
					if ($row)json_exit(array('flag'=>0,'msg'=>'【'.$mob.'】此手机已被登记使用，请更换'));
					$pushUinfo.='　'.substr($mob,0,3).'****'.substr($mob,7,4).'　';
				break;
			}
		}
		foreach($form_data as $F){
			switch ($F) {
				case 'photo_s':
					$piclist=$ARR[$F];
					if (!empty($piclist)){
						$form_arr = explode(',',$piclist);
						if (is_weixin()){
							$serverIds = $piclist;
							if (str_len($serverIds) > 15){
								$serverIds = $form_arr;
								foreach ($serverIds as $value) {
									if(!strstr($value,'p/')){
										$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
										$dbname = wx_get_up('form',$url,$id.'_','SMB');
										$_s     = setpath_s($dbname);
										$list[] = $_s;
									}else{
										$list[] = $value;
									}
								}
								$piclist = implode(",",$list);
							}else{
								json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
							}
						}else{
							foreach ($form_arr as $value) {
								$_s = str_replace($_ZEAI['up2'].'/','',$value);
								if(!strstr($value,'p/tmp/')){
									$list[]=$value;
								}else{
									u_pic_reTmpDir_send($_s,'form','u_pic_reTmpDir_guest');
									u_pic_reTmpDir_send(smb($_s,'m'),'form','u_pic_reTmpDir_guest');
									u_pic_reTmpDir_send(smb($_s,'b'),'form','u_pic_reTmpDir_guest');
									u_pic_reTmpDir_send(smb($_s,'blur'),'form','u_pic_reTmpDir_guest');
									$_s = str_replace('/tmp/','/form/',$_s);
									$list[]=$_s;
								}
							}
							$piclist = implode(",",$list);
						}
					}
					$ARR[$F]=$piclist;
				break;
			}
		}
		$form_agree_reg=intval($form_agree_reg);
		$form_agree_wxshare=intval($form_agree_wxshare);
		$form_rz_mob=($rz_mob==1 && ifmob($mob))?1:0;
		$udata=encode_json($ARR);
		$tguid=intval($tguid);
		$admid=intval($admid);
		$db->query("INSERT INTO ".__TBL_FORM_U__."  (fid,mob,agree_reg,agree_wxshare,rz_mob,addtime,udata,tguid,admid,openid) VALUES ($id,'$mob',$form_agree_reg,$form_agree_wxshare,$form_rz_mob,".ADDTIME.",'$udata',$tguid,$admid,'$server_openid')");
		$formuid = intval($db->insert_id());
		if(!empty($pushUinfo))$pushUinfo='（'.$pushUinfo.'）';
		if(!empty($ifadmlist)){
			$rt=$db->query("SELECT openid FROM ".__TBL_ADMIN__." WHERE id in ($ifadmlist) AND flag=1 AND subscribe=1 AND openid<>''");
			$total = $db->num_rows($rt);
			if ($total > 0) {
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows) break;
					$openid = $rows['openid'];
					$C='新用户录入成功 -> 表单用户编号：'.$formuid.$pushUinfo.'，来自【'.$title.'】';
					@wx_kf_sent($openid,urlencode($C),'text');
				}
			}	
		}
		setcookie("cook_form_uid",$formuid,time()+720000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'登记成功'));
	break;
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $title;?></title>
<?php echo HEADMETA;?>
<meta name="generator" content="Zeai.cn FORM1.0" />
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/zeai_ios_select/separate/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/birthday.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/ZeaiUP.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php if ($rz_identity == 1 || $rz_photo == 1){?><script src="<?php echo HOST;?>/api/zeai_PAY.js?<?php echo $_ZEAI['cache_str'];?>"></script><?php }?>
<script>
Sbindbox = '';var url='form_detail'+zeai.ajxext+'id=<?php echo $id;?>',FORM={},picliobj='piclibox';
var upMaxMB = <?php echo $_UP['upMaxMB']; ?>,maxnum=5,upurl=url,browser='<?php if(is_h5app()){echo 'app';}else{ echo (is_weixin())?'wx':'h5';}?>',up2='<?php echo $_ZEAI['up2'];?>/';
function backtopFnn(){backtopFn();}
</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr:'<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList:['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','chooseImage','uploadImage','previewImage']});
	var FX_title = '<?php echo $wxshareT;?>',
	FX_desc  = '<?php echo trimhtml($wxshareC).'【'.$_ZEAI['siteName'].'】';?>',
	FX_link  = '<?php echo HOST; ?>/m4/form_detail.php?id=<?php echo $id;?>&admid=<?php echo $admid;?>&tguid=<?php echo $tguid;?>',
	  FX_imgurl= '<?php echo $_ZEAI['up2'].'/'.smb($path_s,'b'); ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:FX_title,desc:FX_desc,link:FX_link,imgUrl:FX_imgurl});
		wx.onMenuShareTimeline({title:FX_title,link:FX_link,imgUrl:FX_imgurl});
	});
	</script>
<?php }?>
<link href="<?php echo HOST;?>/res/m4/css/form_detail.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
</head><body>
<?php
$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');
$jurl='form_detail.php?id='.$cook_form_id.'&tguid='.$tguid.'&admid='.$admid;
if($submitok == 'identity' || $submitok == 'identity_success' || $submitok == 'photo' || $submitok == 'photo_success' || $submitok == 'success'){
	if(!ifint($cook_form_uid))alert('请您先登记资料',$jurl);
	$row = $db->ROW(__TBL_FORM_U__,"fid,mob,rz_identity,rz_photo,udata,rz_truename,rz_identitynum","id=".$cook_form_uid,"name");
	if (!$row)alert('请先登记',$jurl);
	$data_fid = $row['fid'];
	$data_mob = $row['mob'];
	$data_rz_identity = $row['rz_identity'];
	$data_rz_photo = $row['rz_photo'];
	$data_udata = $row['udata'];
	$data_rz_truename = $row['rz_truename'];
	$data_rz_identitynum = $row['rz_identitynum'];
	if($data_fid!=$cook_form_id)alert('您在此表单还没登记，请您先登记资料',$jurl);
}
/*************************************实名认证*************************************/
if ($submitok == 'identity' && $rz_identity == 1){
	if($data_rz_identity==1)alert('请不要重复认证','-1');
	$udata=json_decode($data_udata,true);
	$data_truename=dataIO($udata['truename'],'out');
	$data_identitynum=dataIO($udata['identitynum'],'out');
	if(ifint($tmpid)){
		$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid,'num');
		if($row){
			$c=json_decode($row[0],true);
			$data_mob=$c['mob'];
			$data_truename=$c['truename'];
			$data_identitynum=$c['identitynum'];
		}
	}
	//运营商
	if($_SMS['rz_mobile3']==1 && $rz_mob==1){?>
		<style>body{background:url("../m1/img/rz_bg.jpg") top center no-repeat;background-size:100%}</style>
        <div class="mob3box form_detail_rz">
            <form id="ZEAI_FORM" class="form">
                <dl><dt>本人手机</dt><dd><input name="form_mob" type="text" id="form_mob" maxlength="11" pattern="[0-9]*" placeholder="请填写您本人手机号" autocomplete="off" class="input_login" value="<?php echo $data_mob;?>" onBlur="zeai.setScrollTop(0);" /></dd></dl>
                <dl><dt>真实姓名</dt><dd><input value="<?php echo $data_truename;?>" onBlur="zeai.setScrollTop(0);" name="form_truename" type="text" class="input_login" id="form_truename" placeholder="请填写您的真实姓名" autocomplete="off" maxlength="8" ></dd></dl>
                <dl><dt>身份证号</dt><dd><input value="<?php echo $data_identitynum;?>" onBlur="zeai.setScrollTop(0);" name="form_identitynum" type="text" id="form_identitynum" maxlength="18" placeholder="请填写您的身份证号" pattern="[0-9]*" autocomplete="off" class="input_login" /></dd></dl>
                <dl style="border:0;margin-bottom:0px"><dt>认证费用</dt><dd><?php echo ($rz_price>0)?'<font class="S12">￥</font><font class="S18">'.$rz_price.'</font>':'<font class="C090">免费</font>';?></dd></dl>
                <input type="hidden" name="submitok" value="ajax_pay">
                <input type="hidden" name="rzkind" value="identity">
    			<input type="button" value="开始认证" class="btn size4 B" id="mob3_btn">
            </form>
            <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
            <h5>
                <b>●</b><?php echo $_ZEAI['siteName'];?>作为一个真实、严肃平台、我们要求用户必须完成身份证实名认证，保证会员真实可信<br>
                <b>●</b>通过电信+移动+联通三网联合实名校验，确保信息真实有效<br>
                <b>●</b>您提交的信息仅用于本站诚信认证服务，不公开，将严格保护您的隐私安全<br>
            </h5>
            <br><br>
		</div>
        <script>
		mob3_btn.onclick=function(){
			zeai.confirm('通过电信+移动+联通三网联合实名校验<br><font class="S12">（姓名+手机+身份证号必须同一个人才能验证通过）</font><?php if($rz_price>0){echo '<br>请认真核对信息，提交一次验证将扣费，如果填写有误或不是本人导致验证失败，认证费不退哦~~';}?>',function(){
				zeai.ajax({url:'form_detail'+zeai.extname,form:ZEAI_FORM},function(e){var rs=zeai.jsoneval(e);
					if(rs.flag==1){
						zeai_PAY({money:rs.money,paykind:'wxpay',kind:7,oid:rs.orderid,tmpid:rs.form_uid,title:decodeURIComponent(rs.title),return_url:rs.return_url,jumpurl:rs.jumpurl});
					}else if(rs.flag==2){
						zeai.openurl(rs.return_url);
					}else{zeai.msg(rs.msg);}
				});
			});
		}
		<?php
		if ($ifpayok==1){
			if(!ifint($tmpid))alert('error:tmpid1','-1');
			if($_COOKIE["$certrzmaxnum"] >= 1 && $_COOKIE["$certrzmaxnum"] < $rzmaxnum){
			}else{
				if($rz_price>0){
					$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid,'num');
					if(!$row){alert('error:tmpid2','-1');}else{
						$c=json_decode($row[0],true);$oid=$c['orderid'];
					}
					$row = $db->ROW(__TBL_PAY__,"id","orderid='$oid' AND flag=1",'num');
					if (!$row){alert('订单支付状态flag:0','-1');}
				}
			}
			setcookie("$certrzmaxnum",$_COOKIE["$certrzmaxnum"]+1,time()+3600*24,"/",$_ZEAI['CookDomain']);
			?>
			zeai.msg('正在实名验证',{time:8});
			zeai.ajax({url:'form_detail'+zeai.extname,data:{submitok:'ajax_cert_auto',tmpid:<?php echo $tmpid;?>}},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);
				if (rs.flag == 1){
					zeai.openurl('<?php echo $jurl.'&submitok=identity_success';?>');
					//zeai.msg(rs.msg,{time:3});setTimeout(function(){zeai.openurl('<?php echo $jurl.'&submitok=identity_success';?>');},3000);
				}else{
					zeai.msg(rs.msg,{time:3});
				}
			});
		<?php }?>
        </script>
		<?php
	}
/*************************************实名认证 成功*************************************/
}elseif($submitok == 'identity_success' && $rz_identity == 1){
	if($data_rz_identity!=1)alert('请先实名认证','-1');?>
	<style>
	body{background:url("../m1/img/rz_bg.jpg") top center no-repeat;background-size:100%}
	.form_detail_success{margin-top:250px}
    </style>
    <div class="form_detail_success">
        <i class="ico flag">&#xe60d;</i>
        <h4 class="textsuccess">实名认证成功</h4>
        <div class="button">
            <?php if($data_rz_photo != 1 && $rz_photo == 1){?>
            	<a href="<?php echo $jurl.'&submitok=photo';?>" class="btn size5 HONG photo">立即真人认证</a>
            <?php }else{?>
            	<a href="../" class="btn size5 HONG B">进入缘分大厅</a>
            <?php }?>
        </div>
    </div>
	<?php
/*************************************人脸识别*************************************/
}elseif($submitok == 'photo' && $rz_photo == 1){
	if($data_rz_photo==1)alert('请不要重复认证1','-1');
	if(!empty($data_rz_truename) && !empty($data_rz_identitynum)){
		$data_truename=dataIO($data_rz_truename,'out');
		$data_identitynum=dataIO($data_rz_identitynum,'out');
	}else{
		$udata=json_decode($data_udata,true);
		$data_truename=dataIO($udata['truename'],'out');
		$data_identitynum=dataIO($udata['identitynum'],'out');
		//$data_photo_s=$udata['photo_s'];
	}
	if(ifint($tmpid)){
		$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid,'num');
		if($row){
			$c=json_decode($row[0],true);
			$data_mob=$c['mob'];
			$data_truename=$c['truename'];
			$data_identitynum=$c['identitynum'];
		}
	}
	?>
	<style>body{background:url("../m1/img/rz_bg2.jpg") top center no-repeat;background-size:100%}</style>
    <div class="rz_photobox form_detail_rz">
		<form id="ZEAI_FORM" class="form">
			<div class="rz_photo" id="rz_photo"><img src="<?php echo HOST;?>/m1/img/rz_photo.jpg" ></div>
            <h4>请自拍或上传本人正脸照片</h4>
			<dl><dt>真实姓名</dt><dd><input name="form_truename" type="text" class="input_login" id="form_truename" value="<?php echo $data_truename;?>" placeholder="请填写您的真实姓名" autocomplete="off" maxlength="8" onBlur="zeai.setScrollTop(0);"></dd></dl>
			<dl><dt>身份证号</dt><dd><input name="form_identitynum" type="text" id="form_identitynum" maxlength="18" value="<?php echo $data_identitynum;?>"  placeholder="请填写您的身份证号" autocomplete="off" class="input_login" onBlur="zeai.setScrollTop(0);" /></dd></dl>
			<dl style="border:0;"><dt>认证费用</dt><dd><?php echo ($rz_price>0)?'<font class="S12">￥</font><font class="S18">'.$rz_price.'</font>':'<font class="C090">免费</font>';?></dd></dl>
			<input type="hidden" name="formphoto" id="formphoto">
            <input type="hidden" name="submitok" value="ajax_pay">
            <input type="hidden" name="rzkind" value="photo">
            <input type="button" value="开始认证" class="btn size4 W85_ B" id="rz_photo_btn">
		</form>
        <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提醒</div></div>
        <h5>
			<b>●</b><?php echo $_ZEAI['siteName'];?>作为一个真实、严肃平台、我们要求用户必须完成身份证实名认证，保证会员真实可信<br>
			<b>●</b>通过上传的照片与公安库进行校验，确保信息真实有效<br>
            <b>●</b>您提交的信息仅用于本站诚信认证服务，不公开，将严格保护您的隐私安全<br>
        </h5>
		<script>
		ZeaiUP.one({
			onclick:false,
			btnobj:rz_photo,
			url:upurl,
			submitokBef:"ajax_facephoto_",
			wxtmp:false,
			li:function(e){},
			end:function(e){zeai.msg(0);rz_photo.html('<img src='+up2+e._s+'>');formphoto.value=e._s;}
		});
		rz_photo_btn.onclick=function(){
			zeai.confirm('通过连接到公安库进行实名真人校验<br><font class="S12">（姓名+身份证号+自拍照必须同一个人才能验证通过）</font><?php if($rz_price>0){echo '<br>请认真核对信息，提交一次验证将扣费一次，如果填写有误或不是本人导致验证失败，认证费不退哦~~';}?>',function(){
				zeai.ajax({url:'form_detail'+zeai.extname,form:ZEAI_FORM},function(e){var rs=zeai.jsoneval(e);
					if(rs.flag==1){
						zeai_PAY({money:rs.money,paykind:'wxpay',kind:7,oid:rs.orderid,tmpid:rs.form_uid,title:decodeURIComponent(rs.title),return_url:rs.return_url,jumpurl:rs.jumpurl});
					}else if(rs.flag==2){
						zeai.openurl(rs.return_url);
					}else{zeai.msg(rs.msg);}
				});
			});
		}
		<?php
		if ($ifpayok==1){
			if(!ifint($tmpid))alert('error:tmpid3','-1');
			if($_COOKIE["$certrzmaxnum"] >= 1 && $_COOKIE["$certrzmaxnum"] < $rzmaxnum){
				
			}else{
				$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid,'num');
				if(!$row){alert('error:tmpid4','-1');}else{
					$c=json_decode($row[0],true);$oid=$c['orderid'];
				}
				if($rz_price>0){
					$row = $db->ROW(__TBL_PAY__,"id","orderid='$oid' AND flag=1",'num');
					if (!$row){alert('订单支付状态flag:0','-1');}
				}
			}
			setcookie("$certrzmaxnum",$_COOKIE["$certrzmaxnum"]+1,time()+3600*24,"/",$_ZEAI['CookDomain']);
			?>
			zeai.msg('正在真人验证',{time:8});
			zeai.ajax({url:'form_detail'+zeai.extname,data:{submitok:'ajax_cert_auto',tmpid:<?php echo $tmpid;?>}},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);
				if (rs.flag == 1){
					zeai.openurl('<?php echo $jurl.'&submitok=photo_success';?>');
				}else{
					zeai.msg(rs.msg,{time:3});
				}
			});
		<?php }?>
    </script>
    </div>
<?php
/*************************************实名认证 成功*************************************/
}elseif($submitok == 'photo_success' && $rz_photo == 1){
	if($data_rz_photo!=1)alert('请先真人认证','-1');?>
	<style>
	body{background:url("../m1/img/rz_bg2.jpg") top center no-repeat;background-size:100%}
	.form_detail_success{margin-top:250px}
    </style>
    <div class="form_detail_success">
        <i class="ico flag">&#xe60d;</i>
        <h4 class="textsuccess">真人认证成功</h4>
        <div class="button">
            <?php if($data_rz_identity != 1 && $rz_identity == 1){?>
            	<a href="<?php echo $jurl.'&submitok=photo';?>" class="btn size5 HONG photo">立即实名认证</a>
            <?php }else{?>
            	<a href="../" class="btn size5 HONG B">进入缘分大厅</a>
            <?php }?>
        </div>
    </div>
	<?php
/************************************* success *************************************/
}elseif($submitok == 'success'){?>
    <div class="form_detail_success">
        <i class="ico flag">&#xe60d;</i>
        <h4 class="title"><?php echo $title;?></h4>
        <h4 class="textsuccess">报名登记成功</h4>
        <div class="button">
            <?php if($rz_mob==1 && $rz_identity == 1 && $data_rz_identity==0){?>
            	<a href="<?php echo $jurl.'&submitok=identity';?>" class="btn size5 LV2 mob3">立即实名认证</a>
            <?php }elseif($rz_photo == 1 && $data_rz_photo==0){?>
            	<a href="<?php echo $jurl.'&submitok=photo';?>" class="btn size5 HONG photo">立即真人认证</a>
            <?php }else{
					if(!empty($server_openid)){
						$row = $db->ROW(__TBL_USER__,"id,mob,uname,pwd,sex,grade,nickname,RZ","openid<>'' AND openid='".$server_openid."'","name");
						if($row){
							$Mdata_uid=$row['id'];
							$Mdata_mob=$row['mob'];
							$Mdata_RZ=explode(',',$row['RZ']);
							setcookie("cook_uid",$Mdata_uid,time()+720000,"/",$_ZEAI['CookDomain']);
							setcookie("cook_uname",dataIO($row['uname'],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
							setcookie("cook_nickname",dataIO($row['nickname'],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
							setcookie("cook_pwd",$row['pwd'],time()+720000,"/",$_ZEAI['CookDomain']);
							setcookie("cook_sex",$row['sex'],time()+720000,"/",$_ZEAI['CookDomain']);
							setcookie("cook_grade",$row['grade'],time()+720000,"/",$_ZEAI['CookDomain']);
							if((!in_array('mob',$Mdata_RZ) || empty($Mdata_mob)) && $rz_mob==1 && ifmob($data_mob)){
								$Mdata_RZ[]='mob';
								$RZ = (is_array($Mdata_RZ))?implode(',',$Mdata_RZ):'';
								$db->query("UPDATE ".__TBL_USER__." SET mob='$data_mob',RZ='$RZ' WHERE id=".$Mdata_uid);
							}
						}
					}
				?>
            	<a href="../" class="btn size5 HONG">进入缘分大厅</a>
            <?php }?>
        </div>
    </div>
<?php }else{ ?>
    <?php if (!empty($path_s)){?><div class="form_detail_BN"><img src="<?php echo $_ZEAI['up2'].'/'.smb($path_s,'b');?>"></div><?php }?>
    <?php if (str_len($content)>5){?><div class="form_detail_C"><?php echo $content;?></div><?php }?>
    <form class="listbox" id="ZEAI_CN__FORM">
        <?php
        function getarrdef($arrstr) {
            $arr=json_decode($arrstr,true);
            $cout=intval(count($arr)/2)-1;
            $rt=$arr[$cout]['i'];
            return $rt;
        }
        if (is_array($form_data) && count($form_data)>0){
            $n=1;
            foreach($form_data as $F){
                $subkind = data_data_title($data_data,$F,'subkind');
                $T = data_data_title($data_data,$F);
                $data='';$placeholder='';$spanid='';
                switch ($subkind) {//1:文本,2:单选,3:复选,4:区间,5:特殊
                    case 1:$class='ipt';$placeholder='请输入';break;
                    case 2:$class='slect';$placeholder='请选择';if($F=='sex' || $F=='parent' || $F=='love')$class='rdio';break;
                    case 3:$class='chckbox';$placeholder='请选择';break;
                }
                switch ($F) {
                    case 'birthday':$Y=date('Y')-23;$class='bthdy';$placeholder='请选择';$data=$Y.'-01-15';break;
                    case 'edu':$data=getarrdef($_UDATA[$F]);break;
                    case 'pay':$data=getarrdef($_UDATA[$F]);break;
                    case 'heigh':$data=170;break;
                    case 'weigh':$data=50;break;
                    case 'mob':if($subkind==5){$class='rz_mob';$placeholder='请输入';}break;
                    case 'areaid':$class='aread';$placeholder='请选择';$spanid=' id="areatitle"';break;
                    case 'area2id':$class='aread2';$placeholder='请选择';$spanid=' id="area2title"';break;
                    case 'aboutus':$class='txtarea';break;
                }
                if($F=='photo_s'){?>
                    <div class="dlpicmore">
                        <dl>
                            <dt>个人近照<span>（最多5张 推荐3张 单张<?php echo $_UP['upMaxMB'];?>M以内）</span></dt>
                            <dd class="piclibox" id="piclibox"><ul><li></li></ul></dd>
                            <div class="clear"></div>
                        </dl>
                        <div class="clear"></div>
                    </div>
                    <input type="hidden" name="piclist" id="piclist">
                    <script>ZeaiUP.more({obj:piclibox,end:function(e){	FORM['photo_s']=piclist.value;}});</script>
                <?php
                }else{
                    $dl0=($n % 5==0)?' dl0':'';
                    echo '<dl id="'.$F.'" class="'.$class.$dl0.'" data="'.$data.'"><dt>'.$T.'</dt><dd><span'.$spanid.'>'.$placeholder.'</span></dd></dl>';
                }
                $n++;
            }
        }
		$form_agree_reg = 1;$form_agree_wxshare =1;
		?>
        <?php if($agree_reg==1){?>
        <dl class="other"><dt>是否同意注册正式用户</dt><dd><input type="checkbox" value="1" name="form_agree_reg" id="form_agree_reg" class="switch" <?php echo ($form_agree_reg ==1)?' checked':'';?>><label for="form_agree_reg" class="switch-label"><i></i><b>同意</b><b>不同意</b></label></dd></dl>
        <?php }
		if($agree_wxshare==1){?>
        <dl class="other dl0"><dt>是否同意朋友圈/推文推荐</dt><dd><input type="checkbox" value="1" name="form_agree_wxshare" id="form_agree_wxshare" class="switch" <?php echo ($form_agree_wxshare ==1)?' checked':'';?>><label for="form_agree_wxshare" class="switch-label"><i></i><b>同意</b><b>不同意</b></label></dd></dl>
        <?php }?>
        <input type="hidden" name="tguid" value="<?php echo $tguid;?>">
        <input type="hidden" name="admid" value="<?php echo $admid;?>">
        <button type="button" class="btn size4 HONG3 B yuan" id="nextbtn">填好了，提交</button>
        <div id="areabox" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
        <div id="areabox2" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
        <div id="mate_areaidbox" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
        
    </form>
    <?php if (str_len($privateC)>10){?>
    <div class="index_private" id="index_private">
        <div class="C">
            <h1>《个人隐私与用户协议》提示</h1><em id="index_privateC"><?php echo $privateC;?></em>
            <div class="agreebox"><button class="btn size4 FL" type="button" onClick="index_privateFn(0)">不同意</button>
            <button class="btn size4 FR" type="button" onClick="index_privateFn(1)">同意</button></div>
        </div>
    </div>
	<script>
    setTimeout(function(){zeai.mask({son:index_private,cancelBubble:'off'});},500);
    function index_privateFn(k){o('Mindex_private').parentNode.removeChild(o('Mindex_private'));}
    </script>
    <?php }?>
	<div id="backtop"><a href="#top" id="btmTopBtn"><i class="ico">&#xe60a;</i>顶部</a></div>
    <a href="javascript:;" id="btmKefuBtn" class="btmKefuBtn loop_s_b_s"><i class="ico">&#xe6a6;</i>客服</a>
    <div id="btmKefuBox" class="my-subscribe_box"><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><h3>长按二维码添加客服微信<br>享受客服1对1人工服务</h3></div>
    <script>
    zeai.listEach(zeai.tag(ZEAI_CN__FORM,'dl'),function(obj){
        switch (obj.className.replace(' dl0','')) {
            case 'ipt':ZeaiM.divMod('input',obj,url);break;
            case 'txtarea':ZeaiM.divMod('textarea',obj,url);break;
            case 'aread':
                areaid.onclick=function(){
                    ZeaiM.div_up({obj:areabox,h:360});
                    ZEAI_area({areaid:'',areatitle:'',ul:areabox.children[0],str:'job',end:function(z,e){
                        areatitle.html(e);areatitle.class('ed');areaid.setAttribute("data",z);FORM[areaid.id]=z;FORM[areatitle.id]=e;
                    }});
                }
            break;
            case 'aread2':
                area2id.onclick=function(){
                    ZeaiM.div_up({obj:areabox2,h:360});
                    ZEAI_area({areaid:'',areatitle:'',ul:areabox2.children[0],str:'hj',datastr:'hj',end:function(z,e){
                        area2title.html(e);area2title.class('ed');area2id.setAttribute("data",z);FORM[area2id.id]=z;FORM[area2title.id]=e;
                    }});
                }
            break;
            case 'slect':ZeaiM.divMod('select',obj,url);break;
            case 'photo':ZeaiM.divMod('photo',obj,url);break;
            case 'rdio':ZeaiM.divMod('radio',obj,url);break;
            case 'rang':ZeaiM.divMod('range',obj,url);break;
            case 'bthdy':ZeaiM.divMod('birthday',obj,url);break;
            case 'chckbox':ZeaiM.divMod('checkbox',obj,url);break;
            case 'rz_mob':ZeaiM.divMod('rz_mob',obj,url);break;
        }
    });
    nextbtn.onclick=function(){
        zeai.ajax({url:url,form:ZEAI_CN__FORM,data:{submitok:'ajax_next',FORM:JSON.stringify(FORM)}},function(e){var rs=zeai.jsoneval(e);
            if(rs.flag==1){zeai.openurl(url+'&submitok=success');}else{zeai.msg(rs.msg);}
        });
    }
	document.body.onscroll = backtopFnn;
	btmKefuBtn.onclick=function(){ZeaiM.div({obj:btmKefuBox,w:260,h:280});}
    </script>
    <script src="<?php echo HOST;?>/m1/js/zeai_div_area.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<div class="form_detail_bottom">
    <div class="linebox"><div class="line"></div><div class="title BAI S14 C999">扫一扫缘分到</div></div>
    <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><?php }?>
    <div class="copyright">
    <?php if (!empty($kf_tel)){?>
        <i class="ico">&#xe60e;</i> <a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a>
    <?php }else{?>
        <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i> <a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
    <?php }?>
        <a href="<?php echo HOST;?>" class="zeai">&copy;<?php echo date('Y').' '.$_ZEAI['siteName'];?>提供技术支持</a><!--http://www.zeai.cn-->
    </div>
    <div class="hui"></div>
</div>
</body></html>
<?php 
function start_net_verify($tmpid) {
	global $db;
	require_once ZEAI.'api/zeai_RZ.php';
	$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid,'num');
	if(!$row){json_exit(array('flag'=>0,'msg'=>'error:nofound tmpid database'));}else{
		$c=json_decode($row[0],true);
		$form_uid=intval($c['form_uid']);
		$rzkind=$c['rzkind'];
		$mob=$c['mob'];
		$truename=$c['truename'];
		$identitynum=$c['identitynum'];
		$formphoto=$c['formphoto'];
	}
	if($rzkind=='identity'){
		$retarr = Zeai_RZ_mob3($truename,$identitynum,$mob);
	}elseif($rzkind=='photo'){
		if(@file_exists(ZEAI.'up'.DIRECTORY_SEPARATOR.$formphoto)){
			$retarr = Zeai_RZ_face_id_card($truename,$identitynum,ZEAI.'up'.DIRECTORY_SEPARATOR.smb($formphoto,'b'));
		}else{
			json_exit(array('flag'=>0,'msg'=>'请检查照片格式是否为jpg/png/gif格式'));
		}
	}
	start_net_verifyUpdate($retarr,$truename,$identitynum,$mob,$rzkind,$formphoto,$form_uid,$tmpid);
}
function start_net_verifyUpdate($retarr,$truename,$identitynum,$mob,$rzkind,$formphoto,$form_uid,$tmpid) {
	global $db;
	//$retarr['flag']=1;
	if($retarr['flag']==1){
		$SQL="";
		if(ifmob($mob))$SQL  .= ",mob='$mob'";
		if(!empty($truename))$SQL  .= ",rz_truename='$truename'";
		if(ifsfz($identitynum))$SQL  .= ",rz_identitynum='$identitynum'";
		if($rzkind=='photo' && @file_exists(ZEAI.'up'.DIRECTORY_SEPARATOR.$formphoto)){
			@u_pic_reTmpDir_send($formphoto,'form','u_pic_reTmpDir_guest');
			$formphoto = str_replace('/tmp/','/form/',$formphoto);
			$db->query("UPDATE ".__TBL_FORM_U__." SET rz_photo_path1='$formphoto',rz_photo=1".$SQL." WHERE id=".$form_uid);
		}elseif($rzkind=='identity'){
			$db->query("UPDATE ".__TBL_FORM_U__." SET rz_identity=1".$SQL." WHERE id=".$form_uid);
		}
		$db->query("DELETE FROM ".__TBL_TMP__." WHERE id=".$tmpid);
		json_exit(array('flag'=>1,'msg'=>'恭喜你认证成功'));
	}else{
		json_exit(array('flag'=>0,'msg'=>'最终认证结果：'.$retarr['msg']));
	}
}
ob_end_flush();
?>
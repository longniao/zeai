<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$currfields   = 'mob,uname,pwd,nickname,subscribe,openid,RZ';
$currfieldstg = "mob,title,shopkind,content,address,tel,weixin_ewm,yyzz_pic,photo_s,piclist";
$maxnum=5;
//chk tg
$rowtg = shop_chk();
if($rowtg){
	$cook_tg_uid    = $rowtg['id'];
	$cook_tg_shopflag= $rowtg['shopflag'];
	if($cook_tg_shopflag==0 || $cook_tg_shopflag==-1)header("Location: shop_my_flag.php");
	$cook_tg_title      = dataIO($rowtg['title'],'out');
	$cook_tg_content    = dataIO($rowtg['content'],'out');
	$cook_tg_shopkind   = intval($rowtg['shopkind']);
	$cook_tg_address    = dataIO($rowtg['address'],'out');
	$cook_tg_mob        = dataIO($rowtg['mob'],'out');
	$cook_tg_tel        = dataIO($rowtg['tel'],'out');
	$cook_tg_photo_s    = $rowtg['photo_s'];
	$cook_tg_weixin_ewm = $rowtg['weixin_ewm'];
	$cook_tg_yyzz_pic   = $rowtg['yyzz_pic'];
	$cook_tg_piclist    = $rowtg['piclist'];
}else{
	header("Location: ".HOST."/m1/tg_login.php?loginkind=shop&jumpurl=".urlencode(HOST.'/m4/shop_my_apply.php'));
}
$delvar   ='tg_userdelpic';
$renamevar='u_pic_reTmpDir_tg';
$picliobj = 'picliboxmod';
$picli_tipstr='最多'.$maxnum.'张，推荐3张';
$nextbtnstr='保存修改';
$mini_title_str=$_SHOP['title'].'信息';
//chk end


$cook_tg_mob = (empty($cook_tg_mob))?$cook_mob:$cook_tg_mob;
$cook_tg_tel = (empty($cook_tg_tel))?$cook_tg_mob:$cook_tg_tel;
if($submitok == 'ajax_photo_up_h5'){
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_tg_uid.'_');
		if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$_s = setpath_s($dbname);
		json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}elseif($submitok == 'ajax_photo_up_wx'){
	if (str_len($serverIds) > 15){
		$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$serverIds;
		$dbname = wx_get_up('tmp',$url,$cook_tg_uid.'_','SMB');
		$_s = setpath_s($dbname);
		@up_send_userdel(smb($_s,'blur'),$delvar);
		json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
	}
}elseif($submitok == 'ajax_photo_up_app'){
	$f=$_FILES['file'];
	$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_tg_uid.'_');
	if (!up_send($f,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
	$_s = setpath_s($dbname);
	json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
}elseif($submitok == 'ajax_tmp_del'){
	$url = str_replace($_ZEAI['up2']."/","",$url);
	if(!empty($url))@up_send_userdel($url.'|'.smb($url,'m').'|'.smb($url,'b').'|'.smb($url,'blur'),$delvar);
	if($ifmod){
		$ARR=explode(',',$cook_tg_piclist);
		$newARR=array();
		if (count($ARR) >= 1 && is_array($ARR)){
			foreach ($ARR as $V) {
				if($V!=$url)$newARR[]=$V;
			}
			$piclist = (is_array($newARR))?implode(',',$newARR):$cook_tg_piclist;
			$db->query("UPDATE ".__TBL_TG_USER__." SET piclist='$piclist' WHERE id=".$cook_tg_uid);
		}
	}
	json_exit(array('flag'=>1,'url'=>$url));
}elseif($submitok=='addupdate'){
	if(str_len($title)>50 || empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【'.$_SHOP['title'].'名称】'));
	if(empty($shopkind))json_exit(array('flag'=>0,'msg'=>'请输入【服务类型】'));
	if(empty($address))json_exit(array('flag'=>0,'msg'=>'请输入【'.$_SHOP['title'].'地址】'));
	if(empty($tel))json_exit(array('flag'=>0,'msg'=>'请输入【联系电话】'));
	if($rowtg && empty($photo_s))json_exit(array('flag'=>0,'msg'=>'请上传【'.$_SHOP['title'].'LOGO主图】'));
	if(empty($weixin_ewm))json_exit(array('flag'=>0,'msg'=>'请上传【客服微信二维码】'));
	if(str_len($content)>1000 || empty($content))json_exit(array('flag'=>0,'msg'=>'请输入【'.$_SHOP['title'].'介绍】'));
	$form_arr = explode(',',$piclist);
	if(empty($piclist) || count($form_arr)<3)json_exit(array('flag'=>0,'msg'=>'请上传【'.$_SHOP['title'].'图片】至少3张'));
	$endtime = ADDTIME;
	$title=dataIO($title,'in',50);
	$address=dataIO($address,'in',100);
	$content=dataIO($content,'in',1000);
	$shopkind=intval($shopkind);
	$tel=dataIO($tel,'in',100);
	$SQL = "";
	$weixin_ewm = str_replace($_ZEAI['up2'].'/','',$weixin_ewm);
	if (!empty($weixin_ewm) && $cook_tg_weixin_ewm!=$weixin_ewm)$SQL .= picaloneFn('weixin_ewm',$weixin_ewm,$cook_tg_weixin_ewm,$delvar,$renamevar,$rowtg);
	$yyzz_pic = str_replace($_ZEAI['up2'].'/','',$yyzz_pic);
	if (!empty($yyzz_pic) && $cook_tg_yyzz_pic!=$yyzz_pic)$SQL .= picaloneFn('yyzz_pic',$yyzz_pic,$cook_tg_yyzz_pic,$delvar,$renamevar,$rowtg);
	$photo_s = str_replace($_ZEAI['up2'].'/','',$photo_s);
	if (!empty($photo_s) && $cook_tg_photo_s!=$photo_s)$SQL .= picaloneFn('photo_s',$photo_s,$cook_tg_photo_s,$delvar,$renamevar,$rowtg);
	//piclist
	if (!empty($piclist) && $cook_tg_piclist!=$piclist){
		$data_arr = explode(',',$cook_tg_piclist);
		$form_arr = explode(',',$piclist);
		$delarr=array();
		if (count($data_arr) >= 1 && is_array($data_arr)){
			foreach ($data_arr as $D) {
				if(!in_array($D,$form_arr))@up_send_userdel($D.'|'.smb($D,'m').'|'.smb($D,'b'),$delvar);
			}
		}
		if (is_weixin()){
			$serverIds = $piclist;
			if (str_len($serverIds) > 15){
				$serverIds = $form_arr;
				foreach ($serverIds as $value) {
					if(!strstr($value,'p/')){
						$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
						$dbname = wx_get_up('shop',$url,$cook_uid.'_','SMB');
						$_s     = setpath_s($dbname);
						$list[] = $_s;
						@up_send_userdel(smb($_s,'blur'),$delvar);
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
					u_pic_reTmpDir_send($_s,'shop',$renamevar);
					u_pic_reTmpDir_send(smb($_s,'m'),'shop',$renamevar);
					u_pic_reTmpDir_send(smb($_s,'b'),'shop',$renamevar);
					@up_send_userdel(smb($_s,'blur'),$delvar);
					$_s = str_replace('/tmp/','/shop/',$_s);
					$list[]=$_s;
				}
			}
			$piclist = implode(",",$list);
		}
		$SQL .= ",piclist='$piclist'";
	}
	//if($cook_tg_shopflag==-2 || $cook_tg_shopflag==1){
	//	$SQL .= ",shopflag=0";
	//}
	$db->query("UPDATE ".__TBL_TG_USER__." SET title='$title',endtime=".ADDTIME.",endip='$regip',content='$content',shopkind='$shopkind',address='$address',tel='$tel'".$SQL." WHERE id=".$cook_tg_uid);
	if($cook_tg_shopflag==2)$ifpay=1;
	json_exit(array('flag'=>1,'ifpay'=>$ifpay,'msg'=>'保存成功'));
}
$nav = 'shop_my';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $_SHOP['title'];?>入驻-<?php echo $_ZEAI['siteName'];?></title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="<?php echo HOST;?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug: false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	</script>
<?php }?>
</head>
<body>
<?php 
$url=HOST.'/m4/shop_index.php';
$mini_title = '<i class="ico goback" onClick="zeai.back(\''.$url.'\');">&#xe602;</i>'.$mini_title_str;
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
?>
<form id="ZEAI_cnFORM_shop">
<div class="shop_apply">
	<div class="dlbox">
        <dl><dt><?php echo $_SHOP['title'];?>名称</dt><dd><input name="title" id="title" type="text" class="input " placeholder="请输入<?php echo $_SHOP['title'];?>名称" autocomplete="off" maxlength="50" value="<?php echo $cook_tg_title;?>" /></dd></dl>
        <dl><dt>服务类型</dt><dd>
		<select name="shopkind" id="shopkind" class="select" onChange="shopkindFn()">
			<?php
            $kindarr=json_decode($_SHOP['kindarr'],true);
            if (count($kindarr) >= 1 && is_array($kindarr)){
                echo '<option value="">请选择服务类型</option>';
                foreach ($kindarr as $V) {
                    $clss=($cook_tg_shopkind==$V['i'])?' selected':'';
                    echo "<option value=".$kindid=$V['i'].$clss.">".dataIO($V['v'],'out')."</option>";
                }
            }
            ?>
		</select>
        </dd></dl>
        <dl><dt><?php echo $_SHOP['title'];?>地址</dt><dd><input name="address" id="address" type="text" class="input "  placeholder="请输入<?php echo $_SHOP['title'];?>地址" autocomplete="off" maxlength="100" value="<?php echo $cook_tg_address;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
        <dl><dt>联系电话</dt><dd><input name="tel" id="tel" type="text" class="input " placeholder="请输入联系电话" autocomplete="off" maxlength="20" value="<?php echo $cook_tg_tel;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
    </div>
	<div class="dlpic">
        <dl>
            <dt><?php echo $_SHOP['title'];?>LOGO主图</dt>
            <dd><p class="icoadd" id="photo_s_btn"><?php echo (!empty($cook_tg_photo_s))?'<img src="'.$_ZEAI['up2'].'/'.smb($cook_tg_photo_s,'m').'">':'<i class="ico">&#xe620;</i>';?></p></dd>
        </dl>
    </div>
	<div class="dlpic">
        <dl>
            <dt>客服微信二维码</dt>
            <dd><p class="icoadd" id="weixin_ewm_btn"><?php echo (!empty($cook_tg_weixin_ewm))?'<img src="'.$_ZEAI['up2'].'/'.smb($cook_tg_weixin_ewm,'m').'">':'<i class="ico">&#xe620;</i>';?></p></dd>
        </dl>
    </div>
    <div class="dlcontent">
        <dl><dt><?php echo $_SHOP['title'];?>介绍</dt><dd><textarea id="content" name="content" maxlength="1000" class="textarea" placeholder="请输入<?php echo $_SHOP['title'];?>介绍.." onBlur="zeai.setScrollTop(9999);"><?php echo $cook_tg_content; ?></textarea></dd></dl>
	</div>
    <div class="clear"></div>
    <div class="dlpicmore">
        <dl>
            <dt><?php echo $_SHOP['title'];?>图片展示<span>（<?php echo $picli_tipstr;?>）</span></dt>
            <dd class="piclibox" id="<?php echo $picliobj;?>">
                <ul><li></li>
                <?php
				if ($rowtg && !empty($cook_tg_piclist)){
					$ARR=explode(',',$cook_tg_piclist);
					if (count($ARR) >= 1 && is_array($ARR)){foreach ($ARR as $PP){echo '<li><img src="'.$_ZEAI['up2'].'/'.$PP.'"><b></b></li>';}}
                }?>
                </ul>
            </dd>
            <div class="clear"></div>
        </dl>
        <div class="clear"></div>
    </div>
    <div class="dlpic" style="border-bottom:#f5f5f5 12px solid">
        <dl>
            <dt>工商营业执照</dt>
            <dd><p class="icoadd" id="yyzz_pic_btn"><?php echo (!empty($cook_tg_yyzz_pic))?'<img src="'.$_ZEAI['up2'].'/'.smb($cook_tg_yyzz_pic,'m').'">':'<i class="ico">&#xe620;</i>';?></p></dd>
        </dl>
    </div>
    <button type="button" class="btn size4 HONG3 B yuan" id="nextbtn"><?php echo $nextbtnstr;?></button>
</div>
<input type="hidden" name="weixin_ewm" id="weixin_ewm" value="<?php echo $cook_tg_weixin_ewm;?>">
<input type="hidden" name="yyzz_pic" id="yyzz_pic" value="<?php echo $cook_tg_yyzz_pic;?>">
<input type="hidden" name="piclist" id="piclist" value="<?php echo $cook_tg_piclist;?>">
<input type="hidden" name="photo_s" id="photo_s" value="<?php echo $cook_tg_photo_s;?>">
<input type="hidden" name="tguid" value="<?php echo $tguid;?>">
<input type="hidden" name="submitok"  value="addupdate">
</form>
<script>
var picliobj=<?php echo $picliobj;?>,upMaxMB = <?php echo $_UP['upMaxMB']; ?>,maxnum=<?php echo $maxnum;?>,browser='<?php if(is_h5app()){echo 'app';}else{ echo (is_weixin())?'wx':'h5';}?>',up2='<?php echo $_ZEAI['up2'];?>/';
<?php echo ($rowtg)?'var mod=true;':'var mod=false;';?>
</script>
<script src="<?php echo HOST;?>/res/m4/js/shop_apply.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</body>
</html>
<?php
function picaloneFn($form_pic_strname,$form_pic,$data_pic,$delvar,$renamevar,$rowtg) {
	global $_ZEAI,$cook_tg_uid;
	$form_pic = str_replace($_ZEAI['up2'].'/','',$form_pic);
	$SQL="";
	if (!empty($form_pic) && $form_pic!=$data_pic){
		u_pic_reTmpDir_send($form_pic,'shop',$renamevar);
		u_pic_reTmpDir_send(smb($form_pic,'m'),'shop',$renamevar);
		u_pic_reTmpDir_send(smb($form_pic,'b'),'shop',$renamevar);
		@up_send_userdel(smb($form_pic,'blur'),$delvar);
		$form_pic = str_replace('/tmp/','/shop/',$form_pic);
		@up_send_userdel($data_pic.'|'.smb($data_pic,'m').'|'.smb($data_pic,'b'),$delvar);
		$SQL = ($rowtg)?",$form_pic_strname='$form_pic'":$form_pic;
	}
	return $SQL;
}
?>
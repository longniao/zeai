<?php
require_once '../sub/init.php';
$currfieldstg="title";
require_once ZEAI.'m4/shop_chk_u.php';
if($rowtg['shopflag']==0 || $rowtg['shopflag']==-1 || $rowtg['shopflag']==2)header("Location: shop_my_flag.php");
if($submitok!='productmaxnum' && !ifint($id)){
$row = $db->ROW(__TBL_TG_ROLE__,"title,productmaxnum","shopgrade=".$cook_tg_shopgrade,"num");
if ($row){
	$shopgradetitle=$row[0];$productmaxnum=$row[1];
	$pnum = $db->COUNT(__TBL_TG_PRODUCT__,"tg_uid=".$cook_tg_uid);
	if($pnum>=$productmaxnum)header("Location: shop_my_goods_addmod.php?submitok=productmaxnum");
}}
$cook_tg_title = $rowtg['title'];$cook_tg_uid=intval($cook_tg_uid);
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
$maxnum   = 5;
$delvar   ='tg_userdelpic';
$renamevar='u_pic_reTmpDir_tg';
$picliobj = ($submitok=='mod')?'picliboxmod':'piclibox';
if($submitok == 'ajax_path_up_h5'){
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_tg_uid.'_');
		if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$_s = setpath_s($dbname);
		json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}elseif($submitok == 'ajax_path_up_wx'){
	if (str_len($serverIds) > 15){
		$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$serverIds;
		$dbname = wx_get_up('tmp',$url,$cook_tg_uid.'_','SMB');
		$_s = setpath_s($dbname);
		@up_send_userdel(smb($_s,'blur'),$delvar);
		json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
	}
}elseif($submitok == 'ajax_path_up_app'){
	$f=$_FILES['file'];
	$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_tg_uid.'_');
	if (!up_send($f,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
	$_s = setpath_s($dbname);
	json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
}elseif($submitok == 'ajax_tmp_del'){
	$url = str_replace($_ZEAI['up2']."/","",$url);
	if(!empty($url))@up_send_userdel($url.'|'.smb($url,'m').'|'.smb($url,'b').'|'.smb($url,'blur'),$delvar);
	if($ifmod){
		$row = $db->ROW(__TBL_TG_PRODUCT__,"piclist","id=".$id,"num");
		if ($row){
			$piclist= $row[0];
			$ARR=explode(',',$piclist);
			$newARR=array();
			if (count($ARR) >= 1 && is_array($ARR)){
				foreach ($ARR as $V){if($V!=$url)$newARR[]=$V;}
				$piclist = (is_array($newARR))?implode(',',$newARR):$piclist;
				$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET piclist='$piclist' WHERE id=".$id);
			}
		}
	}
	json_exit(array('flag'=>1,'url'=>$url));
}elseif($submitok == 'mod'){
	if (!ifint($id))alert_adm("此商品不存在","-1");
	$rt = $db->query("SELECT path_s,stock,title,content,price,price2,piclist,limitnum,tgbfb1,tgbfb2 FROM ".__TBL_TG_PRODUCT__." WHERE id=".$id);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$data_path_s  = $row['path_s'];
		$data_stock   = $row['stock'];
		$data_piclist = $row['piclist'];
		$data_title   = dataIO($row['title'],'out');
		$data_content = dataIO($row['content'],'wx');
		$data_price   = str_replace(".00","",$row['price']);
		$data_price2  = str_replace(".00","",$row['price2']);
		$data_limitnum= $row['limitnum'];
		$data_tgbfb1= $row['tgbfb1'];
		$data_tgbfb2= $row['tgbfb2'];
		$data_tgbfb_str='直接奖：'.$data_tgbfb1.'%　团队奖：'.$data_tgbfb2.'%';
	}else{
		alert("此商品不存在！！","back");
	}
}elseif($submitok == 'addupdate'){
	if(str_len($title)>50 || empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【商品名称】'));
	$title   = dataIO($title,'in');
	$content = dataIO($content,'in');
	$price  = abs(floatval($price));
	$price2 = abs(floatval($price2));
	$tgbfb1 = abs(intval($tgbfb1));
	$tgbfb2 = abs(intval($tgbfb2));
	if($tgbfb1<0 || $tgbfb1>99 || $tgbfb2<0 || $tgbfb2>99)json_exit(array('flag'=>0,'msg'=>'【分享推广奖励】请填1~99之间的数字'));
	$stock  = intval($stock);
	$limitnum = intval($limitnum);
	if($fahuokind!=1 && $fahuokind!=2 && !ifint($id))json_exit(array('flag'=>0,'msg'=>'请选择发货方式：【快递物流】还是【到店取货】'));
	if($limitnum>$stock)json_exit(array('flag'=>0,'msg'=>'限购数量必须小于库存数量哦'));
	if(empty($path_s))json_exit(array('flag'=>0,'msg'=>'请上传【商品主图】'));
	if(str_len($content)>10000 || empty($content))json_exit(array('flag'=>0,'msg'=>'请输入【商品详情】'));
	if(ifint($id)){
		$SQL = "";
		$row = $db->ROW(__TBL_TG_PRODUCT__,"path_s,piclist","id=".$id,"name");
		if(!$row)json_exit(array('flag'=>0,'msg'=>'没有找到此信息'));
		$data_path_s  = $row['path_s'];
		$data_piclist = $row['piclist'];
		$path_s = str_replace($_ZEAI['up2'].'/','',$path_s);
		if (!empty($path_s) && $data_path_s!=$path_s)$SQL .= picaloneFn('path_s',$path_s,$data_path_s,$delvar,$renamevar,true);
		//piclist
		if (!empty($piclist) && $data_piclist!=$piclist){
			$data_arr = explode(',',$data_piclist);
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
		if (empty($piclist))$SQL .= ",piclist=''";
		$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET limitnum=$limitnum,tgbfb1=$tgbfb1,tgbfb2=$tgbfb2,stock=$stock,price='$price',price2='$price2',title='$title',content='$content',cname='$cook_tg_title' ".$SQL." WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
	}else{
		$path_s = str_replace($_ZEAI['up2'].'/','',$path_s);
		if (!empty($path_s) && $data_path_s!=$path_s)$path_s = picaloneFn('path_s',$path_s,$data_path_s,$delvar,$renamevar,false);
		if (!empty($piclist)){
			 if (is_weixin()){
				$serverIds = $piclist;
				if (str_len($serverIds) > 15){
					$serverIds = explode(',',$serverIds);
					foreach ($serverIds as $value) {
						$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
						$dbname = wx_get_up('shop',$url,$cook_uid.'_','SMB');
						$_s    = setpath_s($dbname);
						$list[]=$_s;
						@up_send_userdel(smb($_s,'blur'),$delvar);
					}
					$piclist = implode(",",$list);
				}else{
					json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
				}
			 }else{
				$sARR = explode(',',$piclist);
				foreach ($sARR as $value) {
					$_s = str_replace($_ZEAI['up2'].'/','',$value);
					u_pic_reTmpDir_send($_s,'shop',$renamevar);
					u_pic_reTmpDir_send(smb($_s,'m'),'shop',$renamevar);
					u_pic_reTmpDir_send(smb($_s,'b'),'shop',$renamevar);
					@up_send_userdel(smb($_s,'blur'),$delvar);
					$_s = str_replace('/tmp/','/shop/',$_s);
					$list[]=$_s;
				}
				$piclist = implode(",",$list);
			}
		}
		$db->query("INSERT INTO ".__TBL_TG_PRODUCT__." (tgbfb1,tgbfb2,price,price2,tg_uid,cname,title,content,path_s,piclist,stock,addtime,fahuokind,px,limitnum) VALUES ($tgbfb1,$tgbfb2,$price,$price2,$cook_tg_uid,'$cook_tg_title','$title','$content','$path_s','$piclist',$stock,".ADDTIME.",'$fahuokind',".ADDTIME.",$limitnum)");
	}
	json_exit(array('flag'=>1,'msg'=>'保存发布成功'));
}
$mini_title_str='商品发布';
$nav = 'shop_my'; 
?>
<!doctype html><html><head><meta charset="utf-8">
<title>商品发布-<?php echo $_ZEAI['siteName'];?></title>
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
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems']
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
if($submitok=='productmaxnum'){
	$row = $db->ROW(__TBL_TG_ROLE__,"title,productmaxnum","shopgrade=".$cook_tg_shopgrade,"num");
	$shopgradetitle=$row[0];
	$pnum = $db->COUNT(__TBL_TG_PRODUCT__,"tg_uid=".$cook_tg_uid);?>
	<style>
    .productmaxnum{width:80%;margin:100px auto;line-height:300%;color:#666}
	.productmaxnum b{color:#f00;margin:0 2px}
    </style>
	<div class="productmaxnum">
        <div class="S18">您当前是【<?php echo $shopgradetitle;?>】等级</div>
        最多只能发布个<b><?php echo $pnum;?></b>个商品，请升级<br><br>
        <button type="button" class="btn size4 HONG yuan"  style="width:80%" onClick="zeai.openurl('shop_my_vip.php')">点击升级</button>
	</div>
<?php exit;}?>
<form id="ZEAI_cnFORM_shop">
<div class="shop_apply">
	<div class="dlbox">
        <dl><dt>商品名称</dt><dd><input name="title" id="title" type="text" class="input " placeholder="输入商品名称" autocomplete="off" maxlength="50" value="<?php echo $data_title;?>" /></dd></dl>
        <dl><dt>当前价格</dt><dd><input name="price" id="price" type="text" class="input " placeholder="输入商品价格(元)" pattern="[0-9]*" autocomplete="off" maxlength="8" value="<?php echo $data_price;?>" /></dd></dl>
        <dl><dt>市场价</dt><dd><input name="price2" id="price2" type="text" class="input " placeholder="只做展示之用(元)" pattern="[0-9]*" autocomplete="off" maxlength="8" value="<?php echo $data_price2;?>" /></dd></dl>
        <dl><dt>库存</dt><dd><input name="stock" id="stock" type="text" class="input " placeholder="库存数量" pattern="[0-9]*" autocomplete="off" maxlength="8" value="<?php echo $data_stock;?>" /></dd></dl>
        <?php if ($submitok != 'mod'){?>
        <dl><dt>发货方式</dt><dd style="text-align:right">
<input type="radio" name="fahuokind" id="fahuokind1" class="radioskin" value="1" onClick="zeai.msg('发货方式选择后不可更改哦');"><label for="fahuokind1" class="radioskin-label"><i class="i1"></i><b class="W200">快递物流</b></label>　
<input type="radio" name="fahuokind" id="fahuokind2" class="radioskin" value="2" onClick="zeai.msg('发货方式选择后不可更改哦');"><label for="fahuokind2" class="radioskin-label"><i class="i1"></i><b class="W100">到店取货</b></label>        
        </dd></dl>
        <?php }?>
        <dl><dt>用户限购</dt><dd><input name="limitnum" id="limitnum" type="text" class="input " placeholder="单个用户最多购买数量" pattern="[0-9]*" autocomplete="off" maxlength="5" value="<?php echo $data_limitnum;?>" /></dd></dl>
        <dl><dt>分享推广</dt><dd><input id="tgnumbtn_str" type="text" class="input " placeholder="分享购买奖励" autocomplete="off" maxlength="20" value="<?php echo $data_tgbfb_str;?>" /></dd></dl>
    </div>
	<div class="dlpic">
        <dl>
            <dt>商品主图</dt>
            <dd>
            	<p class="icoadd" id="path_s_btn"><?php echo (!empty($data_path_s))?'<img src="'.$_ZEAI['up2'].'/'.$data_path_s.'">':'<i class="ico">&#xe620;</i>';?></p>
            </dd>
        </dl>
    </div>
    <div class="dlcontent">
        <dl><dt>商品详情</dt><dd><textarea id="content" name="content" maxlength="1000" class="textarea" placeholder="请输入商品详情介绍.." onBlur="zeai.setScrollTop(9999);"><?php echo $data_content; ?></textarea></dd></dl>
	</div>
    <div class="clear"></div>
    <div class="dlpicmore">
        <dl>
            <dt>图片展示<span>（<?php echo '最多'.$maxnum.'张';?>，推荐3张）</span></dt>
            <dd class="piclibox" id="<?php echo $picliobj;?>">
                <ul><li></li>
                <?php
				if ($submitok=='mod' && !empty($data_piclist)){
					$ARR=explode(',',$data_piclist);
					if (count($ARR) >= 1 && is_array($ARR)){foreach ($ARR as $PP){echo '<li><img src="'.$_ZEAI['up2'].'/'.$PP.'"><b></b></li>';}}
                }?>
                </ul>
            </dd>
            <div class="clear"></div>
        </dl>
        <div class="clear"></div>
    </div>
    <button type="button" class="btn size4 HONG3 yuan" id="nextbtn" style="width:50%;left:25%">保存发布</button>
</div>
<input type="hidden" name="piclist" id="piclist" value="<?php echo $data_piclist;?>">
<input type="hidden" name="path_s" id="path_s" value="<?php echo $data_path_s;?>">
<input type="hidden" name="id" id="id" value="<?php echo $id;?>">
<input type="hidden" name="submitok"  value="addupdate">
<style>
.btmboxinput h1{margin-bottom:0px}
.btmboxinput dl{border:0}
.btmboxinput dl dt{width:28%;font-size:14px}
.btmboxinput dl dd{width:72%;font-size:14px}
.btmboxinput ::-webkit-input-placeholder{font-size:14px}
.btmboxinput dd .input{border-bottom:#eee 1px solid}
.btmboxinput .tgnum_tips{line-height:150%;color:#FC8982}
</style>
<div id="tgnumbox" class="btmboxinput">
	<h1>奖励设置</h1>
    <span class="tgnum_tips">用户分享商品链接或二维码如果有新用户购买后<br>可以获得商品成交<b>订单总价百分比</b>作为奖励<br>只有订单确认收货交易完成才会奖励<br>
    例如：直接奖填10，团队奖填5，将总共奖励订单总额15%，如果没有上级则奖10%；填0不奖励</span>
	<dl><dt>直接奖(直推)</dt><dd><input name="tgbfb1" id="tgbfb1" type="text" class="input " placeholder="输入（0~99之间数字）" autocomplete="off" maxlength="2" value="<?php echo $data_tgbfb1;?>" onBlur="zeai.setScrollTop(0);" /></dd></dl>
	<dl><dt>团队奖(上级)</dt><dd><input name="tgbfb2" id="tgbfb2" type="text" class="input " placeholder="输入（0~99之间数字）" autocomplete="off" maxlength="2" value="<?php echo $data_tgbfb2;?>" onBlur="zeai.setScrollTop(0);"></dd></dl>
	<button type="button" class="btn size4 HONG3 yuan" id="tgbfb_btn">确定并保存</button>
</div>
</form>
<script>
var picliobj='<?php echo $picliobj;?>',upMaxMB = <?php echo $_UP['upMaxMB']; ?>,maxnum=<?php echo $maxnum;?>,browser='<?php if(is_h5app()){echo 'app';}else{ echo (is_weixin())?'wx':'h5';}?>',up2='<?php echo $_ZEAI['up2'];?>/';
<?php echo ($submitok=='mod')?'var mod=true,id='.$id.';':'var mod=false;';?>
</script>
<script src="<?php echo HOST;?>/res/m4/js/shop_my_goods_addmod.js?<?php echo $_ZEAI['cache_str'];?>"></script>
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
		if(!empty($data_pic))@up_send_userdel($data_pic.'|'.smb($data_pic,'m').'|'.smb($data_pic,'b'),$delvar);
		$SQL = ($rowtg)?",$form_pic_strname='$form_pic'":$form_pic;
	}
	return $SQL;
}
?>
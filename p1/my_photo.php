<?php
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$currfields = 'grade';
require_once 'my_chkuser.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/udata.php';
$data_grade  = $row['grade'];
if($submitok == 'ajax_del'){
	if(!ifint($clsid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_PHOTO__,"path_s","uid=".$cook_uid." AND id=".$clsid,"num");
	if ($row){
		$path_s = $row[0];$path_b = getpath_smb($path_s,'b');
		@up_send_userdel($path_s.'|'.$path_b);
		$db->query("DELETE FROM ".__TBL_PHOTO__." WHERE id=".$clsid);
	}
	json_exit(array('flag'=>'1','msg'=>'删除成功'));
}elseif($submitok == 'ajax_pic_path_s_up'){
	$chkok=chkVPmaxNumFlag('photo');
	if (ifpostpic($file['tmp_name'])  && $chkok){
		$dbname = setphotodbname('photo',$file['tmp_name'],$cook_uid.'_');
		if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$_s = setpath_s($dbname);
		$newpic = $_ZEAI['up2']."/".$_s;
		if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		
		$flag  = ($switch['sh']['photo_'.$data_grade] == 1)?1:0;
		
		$db->query("INSERT INTO ".__TBL_PHOTO__." (uid,path_s,flag,addtime) VALUES ($cook_uid,'$_s',$flag,".ADDTIME.")");
		json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$_s));//,'data_num'=>$data_num
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}
$zeai_cn_menu = 'my_photo';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的相册 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<script>
var upMaxMB = <?php echo $_UP['upMaxMB']; ?>,up2='<?php echo $_ZEAI['up2'].'/';?>';
</script>
<script src="js/my_photo.js"></script>
<style>
.my_photo{width:100%;padding:20px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.my_photo li{width:20%;margin:20px 0;padding-bottom:30px;float:left;text-align:center;cursor:pointer;position:relative}
.my_photo li p{width:110px;height:110px;line-height:110px;text-align:center;margin:0 auto;border:#eee 1px solid}
.my_photo li p img{display:inline-block;vertical-align:middle;cursor:zoom-in}
.my_photo li p span{width:44px;line-height:20px;color:#fff;font-size:12px;position:absolute;top:15px;left:28px;background-color:#f70}
.my_photo li h4{width:60px;color:#999;position:absolute;line-height:24px;left:33px;bottom:0px;font-size:12px}
.my_photo li strong{background-image:url('img/ico.png');background-repeat:no-repeat;display:none}
.my_photo li strong{width:20px;height:20px;position:absolute;bottom:3px;right:32px;background-position:-62px top;filter:alpha(opacity=40);-moz-opacity:.4;opacity:.4}
.my_photo li strong:hover{cursor:pointer;animation:delshan .2s infinite;filter:alpha(opacity=70);-moz-opacity:.7;opacity:.7}
@keyframes delshan{0%{transform:rotate(0deg)}25%{transform:rotate(-7deg)}50%{transform:rotate(0deg)}75%{transform:rotate(7deg)}100%{transform:rotate(0deg)}}
.my_photo li:hover strong{display:block}
.my_photo .icoadd{width:108px;height:108px;line-height:106px;border:#dedede 2px dashed;font-size:50px;padding:0;text-align:center;color:#ddd;display:inline-block;cursor:pointer}
.my_photo .icoadd:hover{color:#bbb;border-color:#bbb}
</style>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>个人相册</h1>
        <div class="tab">
            <a href="<?php echo SELF;?>" class="ed">我的相册</a>
            <a href="javascript:;" id="btnadd1">我要上传</a>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="my_photo fadeInR" id="main">
                <li title="点击选择本机照片"><i class="ico icoadd" id="btnadd2">&#xe620;</i></li>
				<?php
				$rt=$db->query("SELECT id,path_s,flag,addtime FROM ".__TBL_PHOTO__." WHERE uid=".$cook_uid." ORDER BY id DESC");
                $total = $db->num_rows($rt);
				$data_photo_num = $total;
                if($total>0){
                    $page_skin=2;$pagemode=4;$pagesize=20;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=0;$i<$pagesize;$i++) {
                        $rows = $db->fetch_array($rt,'num');
                        if(!$rows)break;
						$id     = $rows[0];
						$path_s = $rows[1];
						$flag   = $rows[2];
						$addtime= date_str($rows[3]);
						$path_s_url = $_ZEAI['up2'].'/'.$path_s;
						$flag = ($flag == 0)?'<span>审核中</span>':'';
                    ?>
                    <li value='<?php echo $id;?>'>
                        <p><img src="<?php echo $path_s_url; ?>"><?php echo $flag; ?></p>
                        <h4><?php echo $addtime; ?></h4>
                        <strong id="del<?php echo $id; ?>" title="删除"></strong>
                    </li>
                    <?php }
                    if ($total > $pagesize)echo '<div class="pagebox mypagebox">'.$pagelist.'</div>';
                }
            	?>
        	</div>
                <!--提示开始-->
                <div class="clear"></div> <br><br>
                <div class="tipsbox">
                    <div class="tipst">温馨提示：</div>
                    <div class="tipsc">
                    	● <?php echo getVPmaxNum('photo');?>，想要上传更多请升级VIP会员　<a href="my_vip.php" class="btn size2 HONG3 yuan">升级VIP会员 <i class="ico">&#xe6ab;</i></a><br>
                        ● 每张照片大小限制在 <?php echo $_UP['upMaxMB']; ?>M 以内<br>
                        ● 照片需要是您本人，能让人容易辨认，不可用上传网络上别人的或者明星的照片<br>
                    </div>
                </div>
                <!--提示结束-->                
        </div>
        <!-- end C -->
</div></div></div>
<script>photoFn();</script>
<?php require_once ZEAI.'p1/bottom.php';ob_end_flush();
function chkVPmaxNumFlag($type,$curTotalN=0){
	global $db,$_VIP,$cook_uid,$data_grade;
	if ($type=='video'){
		$cfgARR=$_VIP['video_num'];
		$tbname=__TBL_VIDEO__;
		$dw='个';
	}elseif($type=='photo'){
		$cfgARR=$_VIP['photo_num'];
		$tbname=__TBL_PHOTO__;
		$dw='张';
	}
	$NUM=json_decode($cfgARR,true);$cfgMaxnum = $NUM[$data_grade];
	$data_num = $db->COUNT($tbname,"uid=".$cook_uid);
	if (($curTotalN+$data_num)>=$cfgMaxnum){json_exit(array('flag'=>0,'msg'=>utitle($data_grade).'最多上传'.$cfgMaxnum.$dw));}else{
		return true;
	}
}
function getVPmaxNum($type){
	global $db,$_VIP,$cook_uid,$cook_sex,$data_grade;
	if ($type=='video'){
		$cfgARR=$_VIP['video_num'];
		$tbname=__TBL_VIDEO__;
		$dw='个';
	}elseif($type=='photo'){
		$cfgARR=$_VIP['photo_num'];
		$tbname=__TBL_PHOTO__;
		$dw='张';
	}
	$NUM=json_decode($cfgARR,true);$cfgMaxnum = $NUM[$data_grade];
	return '您当前是'.uicon($cook_sex.$data_grade).utitle($data_grade).'，最多上传 <font class="S16 Cf00">'.$cfgMaxnum.'</font> '.$dw;
}
?>
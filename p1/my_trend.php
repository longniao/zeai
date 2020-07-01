<?php
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$chk_u_jumpurl=Href('trend');
if($submitok == 'add'){
	require_once ZEAI.'sub/conn.php';
	if(!iflogin() || !ifint($cook_uid))exit("<html><body><script>window.onload = function (){parent.location.href='".HOST."/p1/login.php';}</script></body></html>");
}else{
	require_once 'my_chkuser.php';
}
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
//
require_once ZEAI.'cache/config_vip.php';
$trend_addflag = json_decode($_VIP['trend_addflag'],true);
//
if($submitok == 'ajax_del'){
	if(!ifint($id))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TREND__,"piclist","piclist<>'' AND uid=".$cook_uid." AND id=".$id);
	if ($row){
		$piclist= $row[0];
		$sARR = explode(',',$piclist);
		if(count($sARR) >0){foreach ($sARR as $value) {@up_send_userdel($value.'|'.getpath_smb($value,'b'));}}
	}
	$db->query("DELETE FROM ".__TBL_TREND__." WHERE id=".$id." AND uid=".$cook_uid);
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok == 'ajax_photo_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_');
		if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upMsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$_s = setpath_s($dbname);
		$tmppicurl = $_ZEAI['up2']."/".$_s;
		if (!ifpic($tmppicurl))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}	
}elseif($submitok == 'ajax_addupdate'){
	$content = dataIO($content,'in',280);
	if (empty($content))json_exit(array('flag'=>0,'msg'=>'请输入内容～'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发布～'));
	if (!empty($morelist)){
		$sARR = explode(',',$morelist);
		foreach ($sARR as $value) {
			$_s = str_replace($_ZEAI['up2'].'/','',$value);
			u_pic_reTmpDir_send($_s,'trend');
			u_pic_reTmpDir_send(getpath_smb($_s,'b'),'trend');
			$_s = str_replace('/tmp/','/trend/',$_s);
			$piclist[] = $_s;
		}
		$piclist = implode(",",$piclist);
	}
	setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
	if(ifint($cook_uid)){
		$row = $db->ROW(__TBL_USER__,"grade","id=".$cook_uid);
		if ($row){
			$data_grade = $row[0];
			$flag     = intval($trend_addflag[$data_grade]);
			$flag_str = ($flag==0)?'，请等待我们审核':'';
			$db->query("INSERT INTO ".__TBL_TREND__." (uid,piclist,content,addtime,flag) VALUES ($cook_uid,'$piclist','$content',".ADDTIME.",$flag)");
			//给他粉丝站内推送
			$tip_title   = '您关注的好友【'.$cook_nickname.'】发布了新交友圈';
			$tip_content = $cook_nickname.'发布了新交友圈'.'　　<a href="'.Href('trend',$cook_uid).'" class="aQING" target="_blank">进入查看</a>';
			@push_friend_tip($cook_uid,$tip_title,$tip_content);
			//给他粉丝微信推送
	//		$CARR = array();
	//		$CARR['url']      = urlencode(mHref('trend'));
	//		$CARR['picurl']   = $CARR['url'];
	//		$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
	//		$CARR['contentMB']= urlencode($tip_title);
			//@push_friend_wx($uid,$CARR);
		}
	}
	json_exit(array('flag'=>1,'msg'=>'发布成功'.$flag_str));	
}
$t = (ifint($t,'1-2','1'))?$t:1;
$zeai_cn_menu = 'my_trend';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的交友圈 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../rex/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/p1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/my.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/my_trend.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="../rex/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/p1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php
if ($submitok == 'add'){
?>
<style>body{background-color:#fff}</style>
	<div class="trend_add">
    	<h1>发布交友圈</h1>
        <form id='trendbox'>
            <textarea name="content" id="content" placeholder="这一刻的想法......" class="textarea"></textarea>
            <h4><span id="inpttext">0</span>/140</h4><ul><li title="上传照片"></li></ul>
            <input type="hidden" name="morelist" id="morelist" value="">
            <div class="clear"></div>
            <button type="button" id="trend_btn_save" class="btn size4 HONG">开始发布</button>
        </form>
    </div>
	<script src="js/my_trend.js"></script>
	<script>
		var ul = zeai.tag(o('trendbox'),"ul")[0],btnpic = ul.children[0],
		upMaxMB = <?php echo $_UP['upMaxMB']; ?>,
		up2='<?php echo $_ZEAI['up2'];?>/';
		trend_btn_save.onclick = trend_btn_saveFn;
		content.oninput = contentFn;
		btnpic.onclick = btnpicFn;
    </script> 
<?php exit;}?>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>我的交友圈</h1>
        <div class="tab">
			<?php
            if ($t == 1) {
                $rt=$db->query("SELECT id,piclist,content,agreenum,agreelist,addtime,flag FROM ".__TBL_TREND__." WHERE  uid=".$cook_uid." ORDER BY id DESC");
				$total = $db->num_rows($rt);
            }
            ?>
            <a href="<?php echo SELF;?>?t=1"<?php echo ($t==1)?' class="ed"':'';?>>我的交友圈<?php echo ($total>0)?' ('.$total.')':'';?></a>
            <a onclick="supdes=ZeaiPC.iframe({url:'my_trend.php?submitok=add',w:666,h:400})">发布交友圈</a>
            <a href="<?php echo Href('trend',$cook_uid);?>">交友圈预览</a>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="my_trend" id="my_trend_box">
				<?php
                if($total>0){
                    $page_skin=2;$pagemode=4;$pagesize=6;$page_color='#E83191';require_once ZEAI.'sub/page.php';
					?>
                    <table class="tablelist">
                    <?php
                    for($i=0;$i<$pagesize;$i++) {
                        $rows = $db->fetch_array($rt);
                        if(!$rows) break;
                        $id     = $rows[0];
                        $piclist= $rows[1];
                        $content= dataIO($rows[2],'out');
                        $agreenum  = $rows[3];
                        $agreelist = $rows[4];
                        $addtime   = '<b>'.YmdHis($rows[5],'d').'</b>日<br><font>'.YmdHis($rows[5],'Y').'/'.YmdHis($rows[5],'m').'</font>';
                        $flag      = $rows[6];
						$piclist_str = '';
						if (!empty($piclist)){
							$piclist = explode(',',$piclist);
							if (count($piclist) >= 1){foreach ($piclist as $value){
								$_s= $_ZEAI['up2'].'/'.$value;
								$_b=str_replace("_s","_b",$_s);
								$piclist_str .= '<img src="'.$_s.'" onclick="ZeaiPC.piczoom(\''.$_b.'\')">';}
							}
						}
						if(strstr($content,"photo_v")){
							preg_match('/<img.+src=\"?(.+\.(jpg))\"?.+>/i',$content,$match);
							$src = $match[1];
							$cvs = '<video class="zeaiVbox" id="zeaiVbox'.$id.'" controls="controls" controlslist="nodownload"><source>您浏览器版本太低，请升级</video><span class="play ico">&#xe600;</span>';
							$vplay   = 'zeaiplay("'.$id.'")';
							$content = '<h1>上传了个人视频</h1><span class="vbox"><strong onclick='.$vplay.'><img src="'.$src.'" id="img'.$id.'">'.$cvs.'</strong></span>';
							$echo   .=$content;
						}else{
							$echo   .='<h1>'.$content.'</h1>';
						}
                    ?>
                        <tr>
                        <td width="120" height="60" align="left" valign="top"><div class="timebox"><?php echo $addtime;?><div class="zj"></div></div></td>
                        <td height="60" align="left" class="S16">
						  <div class="content"><?php echo $content; ?><?php if ($flag==0)echo " <font class='flag0'>未审</font>";?></div>
                        	<?php if (!empty($piclist)){?><div class="piclist"><?php echo $piclist_str; ?></div><?php }?>
                        </td>
                        <td width="50" align="left" class="C999" title="点赞数"><i class="ico">&#xe652;</i> <font><?php echo $agreenum; ?></font></td>
                        <td width="60" align="center"><button type="button" class="bai" clsid="<?php echo $id;?>">删除</button></td>
                        </tr>
                    <?php }?>
                </table>
                    <?php
                    if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo nodatatips('暂无交友圈<br><a class="btn HONG" onclick="supdes=ZeaiPC.iframe({url:\'my_trend.php?submitok=add\',w:666,h:400})">＋发布交友圈</a>');}
            	?>
        	</div>
        </div>
        <!-- end C -->
</div></div></div>
<script src="js/my_trend.js"></script>
<script>my_trendInit();</script>
<?php require_once ZEAI.'p1/bottom.php';ob_end_flush();?>
<?php
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
require_once 'my_chkuser.php';
require_once ZEAI.'cache/udata.php';
if($submitok == 'ajax_follow_del'){
	if(!ifint($clsid))exit(JSON_ERROR);
	$db->query("DELETE FROM ".__TBL_GZ__." WHERE senduid=".$cook_uid." AND id=".$clsid);
	json_exit(array('flag'=>'1','msg'=>'操作成功'));
}elseif($submitok == 'ajax_gz'){
	if(!ifint($clsid))exit(JSON_ERROR);$uid=$clsid;
	$db->query("INSERT INTO ".__TBL_GZ__."(uid,senduid,px) VALUES ($uid,$cook_uid,".ADDTIME.")");
	json_exit(array('flag'=>'1','msg'=>'关注成功'));
}elseif($submitok == 'ajax_fans_del'){
	if(!ifint($clsid))exit(JSON_ERROR);$uid=$clsid;
	$db->query("DELETE FROM ".__TBL_GZ__." WHERE senduid=".$cook_uid." AND uid=".$uid);
	json_exit(array('flag'=>'1','msg'=>'操作成功'));
}elseif($submitok == 'ajax_hmd_cancel'){
	if(!ifint($clsid))exit(JSON_ERROR);$uid=$clsid;
	$db->query("DELETE FROM ".__TBL_GZ__." WHERE uid=".$uid." AND senduid=".$cook_uid);
	json_exit(array('flag'=>'1','msg'=>'操作成功'));
}elseif($submitok == 'ajax_del_all'){
	//$db->query("DELETE FROM ".__TBL_CLICKHISTORY__." WHERE uid=".$cook_uid);
	//json_exit(array('flag'=>'1','msg'=>'清空成功'));
}
$t = (ifint($t,'1-3','1'))?$t:1;
if($t==1){
	$t_str='我关注的人';
	//$GZSQL = ",(SELECT COUNT(*) FROM ".__TBL_GZ__." WHERE flag=1 AND senduid=a.uid AND uid=".$cook_uid.") AS gzflag";
	$SQL   = "SELECT a.id,a.uid,b.uname,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.edu,b.pay,b.heigh".$GZSQL." FROM ".__TBL_GZ__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.senduid=".$cook_uid." AND a.flag=1 ORDER BY a.px DESC";
}elseif($t==2){
	$t_str='我的粉丝';
	$SQL = "SELECT a.id,a.senduid AS uid,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.edu,b.pay,b.heigh FROM ".__TBL_GZ__." a,".__TBL_USER__." b WHERE a.uid=".$cook_uid." AND a.senduid=b.id AND a.flag=1 ORDER BY a.px DESC";
}elseif($t==3){
	$t_str='黑名单';
	$SQL = "SELECT a.id,a.uid AS uid,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.edu,b.pay,b.heigh FROM ".__TBL_GZ__." a,".__TBL_USER__." b WHERE a.senduid=".$cook_uid." AND a.uid=b.id AND a.flag=-1 ORDER BY a.px DESC";
}
$zeai_cn_menu = 'my_follow';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $t_str;?> - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_msg.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1><?php echo $t_str;?></h1>
        <div class="tab">
            <a href="<?php echo SELF;?>?t=1"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>>我关注的人</a>
            <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>我的粉丝</a>
            <a href="<?php echo SELF;?>?t=3"<?php echo ($t==3)?' class="ed"':'';?>>黑名单</a>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="sx" id="main">
			<?php
                $rt=$db->query($SQL);
                $total = $db->num_rows($rt);
                if($total>0){
                    $page_skin=2;$pagemode=4;$pagesize=10;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=0;$i<$pagesize;$i++) {
                        $rows = $db->fetch_array($rt,'name');
                        if(!$rows)break;
                        $id      = $rows['id'];
                        $uid     = $rows['uid'];
                        $new     = $rows['new'];
                        $addtime = date_str($rows['addtime']);
                        //
                        $uname    = dataIO($rows['uname'],'out');
                        $nickname = dataIO($rows['nickname'],'out');
                        $sex      = $rows['sex'];
                        $grade    = $rows['grade'];
                        $photo_s  = $rows['photo_s'];
                        $photo_f  = $rows['photo_f'];
                        $nickname = (empty($nickname))?$uname:$nickname;
						$areatitle= $rows['areatitle'];
						$birthday = $rows['birthday'];
						$edu      = $rows['edu'];
						$pay      = $rows['pay'];
						$heigh    = $rows['heigh'];
						
						//	
						$pay_str       = (empty($pay))?'':''.' ';
						$edu_str       = (empty($edu))?'':''.' ';
						$birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁';
						$heigh_str     = (empty($heigh))?'':' '.$heigh.'cm';
						$areatitle_str = (empty($areatitle))?'':' '.$areatitle;
						$content = $birthday_str.$heigh_str.$pay_str.udata('pay',$pay).$edu_str.udata('edu',$edu).$areatitle_str;
                        //
                        $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
                        $sexbg  = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
						if($t==1){
							$btntitle='点击取消关注';
							$gzflag = gzflag($cook_uid,$uid);
							if($gzflag == 1){
								$btncls=' ed R0';
								$btnstr='互相关注';
							}else{
								$btncls=' edhui R0';
								$btnstr='已关注';
							}
						}elseif($t==2){
							$btncls=' ed';
							$btntitle='点击关注';
							$gzflag = gzflag($uid,$cook_uid);
							if($gzflag == 1){
								$btncls=' ed R0';
								$btnstr='互相关注';
							}else{
								$btncls=' edlan R0';
								$btnstr='+ 加关注';
							}
						}elseif($t==3){
							$btncls=' edhei R0';
							$btntitle='点击取消拉黑';
							$btnstr='取消拉黑';
						}
                    ?>
                    <dl>
                        <dt clsid="<?php echo $id; ?>" uid="<?php echo $uid; ?>"><a href="<?php echo Href('u',$uid);?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></dt>
                        <dd><h4><a href="<?php echo Href('u',$uid);?>"><?php echo $nickname.uicon($sex.$grade); ?></a></h4><h6><?php echo $content; ?></h6></dd>
                        <button id="btn<?php echo $id; ?>" type="button" style="padding:0" class="W80 btn size3<?php echo $btncls;?>" t="<?php echo $t;?>" title="<?php echo $btntitle;?>"><?php echo $btnstr;?></button>
                    </dl>
                    <?php }
                    if ($total > $pagesize)echo '<div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo nodatatips('暂时还没有相关记录');}
            ?>
        	</div>
        </div>
        <!-- end C -->
</div></div></div>
<script src="js/my_msg.js"></script>
<script>my_followFn();</script>
<?php require_once ZEAI.'p1/bottom.php';ob_end_flush();?>
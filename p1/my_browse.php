<?php
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
require_once 'my_chkuser.php';
require_once ZEAI.'cache/udata.php';
if($submitok == 'ajax_del'){
	if(!ifint($clsid))exit(JSON_ERROR);
	$db->query("DELETE FROM ".__TBL_CLICKHISTORY__." WHERE uid=".$cook_uid." AND id=".$clsid);
	json_exit(array('flag'=>'1','msg'=>'删除成功'));
}elseif($submitok == 'ajax_del_all'){
	$db->query("DELETE FROM ".__TBL_CLICKHISTORY__." WHERE uid=".$cook_uid);
	json_exit(array('flag'=>'1','msg'=>'清空成功'));
}
$t = (ifint($t,'1-2','1'))?$t:1;
if($t==1){
	$db->query("UPDATE ".__TBL_CLICKHISTORY__." SET new=0 WHERE uid=".$cook_uid." AND new=1");
	$t_str='谁看过我';
	$SQL="SELECT a.id,a.senduid AS uid,a.new,a.addtime,b.uname,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.edu,b.pay,b.heigh FROM ".__TBL_CLICKHISTORY__." a,".__TBL_USER__." b WHERE a.senduid=b.id AND a.uid=".$cook_uid." ORDER BY a.addtime DESC";
}elseif($t==2){
	$t_str='我看过谁';
	$SQL="SELECT a.id,a.uid,a.new,a.addtime,b.uname,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.edu,b.pay,b.heigh FROM ".__TBL_CLICKHISTORY__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.senduid=".$cook_uid." ORDER BY a.addtime DESC";
}
$zeai_cn_menu = 'my_browse';
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
            <a href="<?php echo SELF;?>?t=1"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>>谁看过我</a>
            <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>我看过谁</a>
            <?php if ($t == 1){ ?><a href="javascript:;" class="tabRbtn" id="browse_delall" title="清空全部【谁看过我】浏览记录">清空全部</a><?php }?>
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
                        $sexbg = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
						if($new == 1){
							$new_str = '<i class="new_hi"></i>';
							$btncls  = '';
						}else{
							$new_str = '';
							$btncls  = ' ed';
						}
						if($sex == $cook_sex){
							$btncls=' disabled';
							$btntitle='同性不能聊天';
							$ifchat=0;
						}else{
							$btncls='';
							$btntitle='开始聊天';
							$ifchat=1;
						}
						$btncls2 = ($t==2)?' R0':'';
                    ?>
                    <dl>
                        <dt clsid="<?php echo $id; ?>" uid="<?php echo $uid; ?>"><a href="<?php echo Href('u',$uid);?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></dt>
                        <?php echo $new_str; ?>
                        <dd><h4><a href="<?php echo Href('u',$uid);?>"><?php echo $nickname.uicon($sex.$grade); ?></a></h4><h6><?php echo $content; ?></h6></dd>
                        <span><?php echo $addtime; ?></span>
                        <button id="btn<?php echo $id; ?>" type="button" class="btn size3<?php echo $btncls.$btncls2;?>" ifchat="<?php echo $ifchat;?>" title="<?php echo $btntitle;?>"<?php echo (!in_array('chat',$navarr))?' style="display:none"':'';?>><i class="ico">&#xe676;</i><font>聊天</font></button>
                        <?php if ($t==1){?><b title="删除" id="del<?php echo $id; ?>"><i class="ico">&#xe65b;</i></b><?php }?>
                    </dl>
                    <?php }
                    if ($total > $pagesize)echo '<div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo nodatatips('暂时还没有浏览记录');}
            ?>
        	</div>
        </div>
        <!-- end C -->
</div></div></div>
<script src="js/my_msg.js"></script>
<script>browseFn();</script>
<?php require_once ZEAI.'p1/bottom.php';ob_end_flush();?>
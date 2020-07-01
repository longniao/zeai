<?php
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
require_once 'my_chkuser.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';

if($submitok == 'ajax_delmsg'){
	if(!ifint($uid))exit($json_error);
	$SQL = " WHERE ifdel=0 AND senduid=".$uid." AND uid=".$cook_uid;
	$rt=$db->query("SELECT uid,new FROM ".__TBL_MSG__.$SQL);
	$total = $db->num_rows($rt);
	$n_my = 0;
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt);
			if(!$rows) break;
			$uid_    = $rows[0];
			$new     = $rows[1];
			if ($new == 1)$n_my++;
		}
	}
	if ($n_my>0)$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-$n_my WHERE tipnum>$n_my AND id=".$cook_uid);
	$SQL = " (uid=".$uid." AND senduid=".$cook_uid.") OR (senduid=".$uid." AND uid=".$cook_uid.") ";
	$db->query("UPDATE ".__TBL_MSG__." SET ifdel=".$cook_uid." WHERE ifdel=0 AND uid=".$SQL);
	//我发给对方的
	$SQL1 = "senduid=".$cook_uid." AND uid=".$uid;
	$cont = $db->COUNT(__TBL_MSG__,"ifdel=".$uid." AND ".$SQL1);
	if ($cont > 0){//对方已删除
		$db->query("DELETE FROM ".__TBL_MSG__." WHERE ".$SQL);
	}
	json_exit(array('flag'=>'1','n'=>$n_my,'msg'=>'删除成功'));
}elseif($submitok == 'ajax_tz_del'){
	if(!ifint($tid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TIP__,"id","new=1 AND uid=".$cook_uid." AND id=".$tid);
	if ($row)$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
	$db->query("DELETE FROM ".__TBL_TIP__." WHERE uid=".$cook_uid." AND id=".$tid);
	json_exit(array('flag'=>'1','msg'=>'删除成功'));
}elseif($submitok == 'ajax_msg_hi_div'){
	if(!ifint($tid))exit(JSON_ERROR);
	$rt=$db->query("SELECT a.senduid,a.content,a.new,b.sex,b.nickname,b.photo_s,b.photo_f FROM ".__TBL_TIP__." a,".__TBL_USER__." b WHERE a.kind=3 AND a.senduid=b.id AND a.uid=".$cook_uid." AND a.id=".$tid);
	if ($db->num_rows($rt)){
		$row = $db->fetch_array($rt,'num');
		$senduid = $row[0];
		$content = urlencode($row[1]);
		$new     = $row[2];
		$sex     = $row[3];
		$nickname= dataIO($row[4],'out');
		$photo_s = $row[5];
		$photo_f = $row[6];
		if ($new == 1){
			$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE uid=".$cook_uid." AND kind=3 AND id=".$tid);
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
		}
		$photo_s = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		$ifhiher = ($db->COUNT(__TBL_TIP__,"senduid=".$cook_uid." AND uid=".$senduid." AND kind=3") > 0)?1:0;
		echo $tid.'|ZEAI|'.$senduid.'|ZEAI|'.$nickname.'|ZEAI|'.$sex.'|ZEAI|'.$photo_s.'|ZEAI|'.$content.'|ZEAI|'.$new.'|ZEAI|'.$ifhiher;
	}exit;
}elseif($submitok == 'ajax_gift_div_msg'){
	if(!ifint($tid))exit(JSON_ERROR);
	$T = $db->ROW(__TBL_TIP__,"remark,new","id=".$tid,"num");
	if ($T){$guid = $T[0];$tnew=$T[1];}else{exit(JSON_ERROR);}
	if($tnew==1){
		$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE uid=".$cook_uid." AND kind=2 AND id=".$tid);
		$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
	}
    $rt=$db->query("SELECT a.senduid AS uid,a.new AS ifnew,b.title,b.picurl,b.price,c.nickname FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b,".__TBL_USER__." c WHERE a.id=".$guid." AND a.uid=".$cook_uid." AND a.gid=b.id AND a.senduid=c.id LIMIT 1");
    if ($db->num_rows($rt)){
		$G = $db->fetch_array($rt,'name');
		$G['nickname'] = strip_tags(dataIO($G['nickname'],'out'));
		$G['title']    = dataIO($G['title'],'out');
		$G['picurl']   = $_ZEAI['up2'].'/'.$G['picurl'];
		$G['uhref']    = Href('u',$G['uid']);
		if ($G['ifnew'] == 1){
			$db->query("UPDATE ".__TBL_GIFT_USER__." SET new=0 WHERE uid=".$cook_uid." AND id=".$guid);
		}
		$G = array('flag'=>'1') + $G;
		json_exit($G);
    }else{exit(JSON_ERROR);}
}
$chat_duifangfree = json_decode($_VIP['chat_duifangfree'],true);

$chatnum = $db->COUNT(__TBL_MSG__,"new=1 AND ifdel=0 AND uid=".$cook_uid);
$hinum   = $db->COUNT(__TBL_TIP__,"new=1 AND kind=3 AND uid=".$cook_uid);
//$tznum   = $db->COUNT(__TBL_TIP__,"new=1 AND (kind=1 OR kind=2) AND uid=".$cook_uid);
//$tznum_str  =($tznum>0)?'<b>'.$tznum.'</b>':'';

$chatnum_str=($chatnum>0)?'<b>'.$chatnum.'</b>':'';
$hinum_str  =($hinum>0)?'<b>'.$hinum.'</b>':'';

$zeai_cn_menu = 'my_msg';

if (!in_array('chat',$navarr) && !in_array('hi',$navarr) ){
	$t=4;
}elseif(!in_array('chat',$navarr) && in_array('hi',$navarr) && !ifint($t)){
	$t=3;
}
$t = (ifint($t,'1-4','1'))?$t:1;
?>
<!doctype html><html><head><meta charset="utf-8">
<title>消息中心 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../rex/www_esyyw_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_msg.css" rel="stylesheet" type="text/css" />
<script src="../rex/www_esyyw_cn.js"></script>
<script src="js/p1.js"></script>
</head>
<body>
<?php if($submitok == 'tip_detail'){
	if(!ifint($tid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TIP__,"content,new,addtime,kind","uid=".$cook_uid." AND id=".$tid);
	if ($row){
		$content = dataIO($row[0],'out');
		$content = str_replace("<br>","　",$content);
		$new     = $row[1];
		$addtime = YmdHis($row[2]);
		$kind    = $row[3];
		if ($new == 1){
			$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE id=".$tid." AND uid=".$cook_uid);
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
		}
		$kindT=($kind==1)?'系统消息':'红娘消息';
	}else{exit(JSON_ERROR);}
	?>
	<style>body{background-color:#fff}</style>
    <div class="tip_detail">
    	<h1><?php echo $kindT;?></h1>
		<div id="msgC" class="msgC"><?php echo $content;?></div>
        <div class="linebox"><div class="line "></div><div class="title BAI S14 C999"><?php echo $addtime; ?></div></div>
    </div>
    <script>zeai.listEach(zeai.tag(msgC,'a'),function(a){a.setAttribute('target','_parent');});</script>
<?php exit;}?>


<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>消息中心</h1>
        <div class="tab">
        	<?php if (in_array('chat',$navarr)){?>
                <a href="<?php echo SELF;?>?t=1"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>>收到私信<?php echo $chatnum_str;?></a>
                <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>发出私信</a>
            <?php }?>
            <?php if (in_array('hi',$navarr)){?>
            	<a href="<?php echo SELF;?>?t=3"<?php echo ($t==3)?' class="ed"':'';?>>收到招呼<?php echo $hinum_str;?></a>
            <?php }?>
            
            <a href="<?php echo SELF;?>?t=4"<?php echo ($t==4)?' class="ed"':'';?>>系统通知<?php $tznum=$tipnum-$chatnum-$hinum;echo($tznum>0)?'<b>'.$tznum.'</b>':'';?></a>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="sx" id="main">
			<?php
			//收到私信
            if($t==1){
                //$rt=$db->query("SELECT id,uid,senduid,t,content,addtime FROM ".__TBL_MSG__." WHERE uid=".$cook_uid." AND ifdel<>".$cook_uid." GROUP BY senduid ORDER BY id DESC");
				$sql = "uid=".$cook_uid." AND ifdel=0";
                $rt=$db->query("SELECT id,senduid,t,content,addtime,t AS kind FROM ".__TBL_MSG__." A,(SELECT MAX(id) AS max_id FROM ".__TBL_MSG__." WHERE ".$sql." GROUP BY senduid) B WHERE A.id=B.max_id AND ".$sql." ORDER BY A.id DESC");
                $total = $db->num_rows($rt);
                if($total>0){
                    $page_skin=2;$pagemode=4;$pagesize=10;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=0;$i<$pagesize;$i++) {
						 $rows = $db->fetch_array($rt,'name');
						 if(!$rows) break;
                            $senduid = $rows['senduid'];
                            $t       = $rows['t'];
                            $content = dataIO($rows['content'],'out');
                            $addtime_str = date_str($rows['addtime']);
							$kind = $rows['kind'];
                            //主表信息
                            $row = $db->NUM($senduid,"sex,grade,nickname,photo_s,photo_f,birthday,areatitle,heigh,uname");
                            $sex      = $row[0];
                            $grade    = $row[1];
                            $nickname = dataIO($row[2],'out');
                            $photo_s  = $row[3];
                            $photo_f  = $row[4];
                            $birthday  = $row[5];
                            $areatitle = $row[6];
                            $heigh     = $row[7];
                            $uname     = dataIO($row[8],'out');
                            $birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁';
                            $heigh_str     = (empty($heigh))?'':' '.$heigh.'cm';
                            $nickname = (empty($nickname))?$uname:$nickname;
                            //
                            $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
                            $sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
                            //列表红点
                            $inum        = $db->COUNT(__TBL_MSG__," new=1 AND ifdel=0 AND senduid=".$senduid." AND uid=".$cook_uid);
                            $inum_str    = ($inum>0)?'<i class="new">'.$inum.'</i>':'';
                            if($inum>0){
                                $inum_str ='<i class="new">'.$inum.'</i>';
                                $btncls = '';
                            }else{
                                $inum_str = '';
                                $btncls = ' ed';
                            }
                            //锁
							if($chat_duifangfree[$grade]!=1){
								$MsgFlag = lockU($senduid);
							}else{
								$MsgFlag=true;
							}
                            if ($MsgFlag){
                                if($kind == 2){$content = '[语音]';}elseif(strstr($content,"[/img]")){$content = preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=".HOST.'/res/bq/'."\\1.gif>",$content);}
                            }else{
								$content = $birthday_str.$heigh_str.' '.$areatitle;
                            }
                        	?>
                            <dl>
                                <dt><a href="<?php echo Href('u',$senduid);?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></dt>
                                <dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $content; ?></h6></dd>
                                <span><?php echo $addtime_str; ?></span><?php echo $inum_str; ?>
                                <button type="button" class="btn size3<?php echo $btncls;?>" onClick="ZeaiPC.chat(<?php echo $senduid;?>);"><i class="ico">&#xe676;</i><font>聊天</font></button>
                                <b onClick="malldel(<?php echo $senduid;?>);" title="删除"><i class="ico">&#xe65b;</i></b>
                                <?php if (!$MsgFlag){?>
                                <i class="ico lock">&#xe61e;</i>
                                <?php }?>
                            </dl>
						<?php
					}
					if ($total > $pagesize)echo '<div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo nodatatips('暂时还没有人给你发信');}
			//发出私信
            }elseif($t==2){
				$sql = "senduid=".$cook_uid;
                //$rt=$db->query("SELECT id,uid,uid,t,content,addtime FROM ".__TBL_MSG__." WHERE senduid=".$cook_uid."  GROUP BY uid ORDER BY id DESC");
                $rt=$db->query("SELECT id,uid,t,content,addtime,t AS kind FROM ".__TBL_MSG__." A,(SELECT MAX(id) AS max_id FROM ".__TBL_MSG__." WHERE ".$sql." GROUP BY uid) B WHERE A.id=B.max_id AND ".$sql." ORDER BY A.id DESC");
                $total = $db->num_rows($rt);
                if($total>0){
                    $page_skin=2;$pagemode=4;$pagesize=10;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=0;$i<$pagesize;$i++) {
						 $rows = $db->fetch_array($rt,'name');
						 if(!$rows) break;
                            $uid  = $rows['uid'];
                            $t    = $rows['t'];
							$kind = $rows['kind'];
                            $content = dataIO($rows['content'],'out');
                            $addtime_str = date_str($rows['addtime']);
                            //主表信息
                            $row = $db->NUM($uid,"sex,grade,nickname,photo_s,photo_f,birthday,areatitle,heigh,uname");
                            $sex      = $row[0];
                            $grade    = $row[1];
                            $nickname = dataIO($row[2],'out');
                            $photo_s  = $row[3];
                            $photo_f  = $row[4];
                            $birthday  = $row[5];
                            $areatitle = $row[6];
                            $heigh     = $row[7];
                            $uname     = dataIO($row[8],'out');
                            $birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁';
                            $heigh_str     = (empty($heigh))?'':' '.$heigh.'cm';
                            $nickname = (empty($nickname))?$uname:$nickname;
                            //
                            $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
                            $sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
                            //列表红点
                            $inum        = $db->COUNT(__TBL_MSG__," new=1 AND ifdel=0 AND senduid=".$cook_uid." AND uid=".$uid);
                            $inum_str    = ($inum>0)?'<i class="new">'.$inum.'</i>':'';
                            if($inum>0){
                                $inum_str ='<i class="new">'.$inum.'</i>';
                                $btncls = '';
                            }else{
                                $inum_str = '';
                                $btncls = ' ed';
                            }
                            //锁
							if($chat_duifangfree[$grade]!=1){
								$MsgFlag = lockU($uid);
							}else{
								$MsgFlag=true;
							}
							if($kind == 2){$content = '[语音]';}elseif(strstr($content,"[/img]")){$content = preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=".HOST.'/res/bq/'."\\1.gif>",$content);}
                        	?>
                            <dl>
                                <dt><a href="<?php echo Href('u',$uid);?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></dt>
                                <dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $content; ?></h6></dd>
                                <span><?php echo $addtime_str; ?></span><?php echo $inum_str; ?>
                                <button type="button" class="btn size3<?php echo $btncls;?>" onClick="ZeaiPC.chat(<?php echo $uid;?>);"><i class="ico">&#xe676;</i><font>聊天</font></button>
                                <b style="display:none"></b>
                                <?php if (!$MsgFlag){?>
                                <i class="ico lock">&#xe61e;</i>
                                <?php }?>
                            </dl>
						<?php
					}
					if ($total > $pagesize)echo '<div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo nodatatips('暂时还没有人给你发信');}
			//收到招呼
            }elseif($t==3){
                $SQL = "SELECT a.id,a.senduid,a.content,a.new,a.addtime,U.uname,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f FROM ".__TBL_TIP__." a,".__TBL_USER__." U WHERE a.kind=3 AND a.uid=".$cook_uid." AND a.senduid=U.id ORDER BY a.id DESC";
                $rt=$db->query($SQL);
                $total = $db->num_rows($rt);
                if($total>0){
                    $page_skin=2;$pagemode=4;$pagesize=10;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=0;$i<$pagesize;$i++) {
                        $rows = $db->fetch_array($rt,'name');
                        if(!$rows)break;
                        $id      = $rows['id'];
                        $senduid = $rows['senduid'];
                        $content = dataIO($rows['content'],'out');
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
                        //
                        $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
                        $sexbg = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
                        //列表红点
                        if($new>0){
                            $new_str ='<i class="new_hi"></i>';
                            $btncls = '';
                        }else{
                            $new_str = '';
                            $btncls = ' ed';
                        }
                        //$ifhiher = ($db->COUNT(__TBL_TIP__,"senduid=".$cook_uid." AND uid=".$senduid." AND kind=3") > 0)?true:false;
                    ?>
                    <dl>
                        <dt tid="<?php echo $id; ?>" kind="3"><a href="<?php echo Href('u',$senduid);?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></dt>
                        <?php echo $new_str; ?>
                        <dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $content; ?></h6></dd>
                        <span><?php echo $addtime; ?></span>
                        <button type="button" class="btn size3<?php echo $btncls;?>"><i class="ico">&#xe628;</i><font>查看</font></button>
                        <b onClick="tzdel(<?php echo $id;?>);" title="删除"><i class="ico">&#xe65b;</i></b>
                    </dl>
                    <?php }
                    if ($total > $pagesize)echo '<div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo nodatatips('暂时还没有人给你打招呼');}
			//系统通知
            }elseif($t==4){
				$rt=$db->query("SELECT id,senduid,title,content,new,kind,addtime FROM ".__TBL_TIP__." WHERE (kind=1 OR kind=2 OR kind=4) AND uid=".$cook_uid." ORDER BY id DESC");
                $total = $db->num_rows($rt);
                if($total>0){
                    $page_skin=2;$pagemode=4;$pagesize=10;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=0;$i<$pagesize;$i++) {
                        $rows = $db->fetch_array($rt,'num');
                        if(!$rows)break;
						$id      = $rows[0];
						$senduid = $rows[1];
						$new     = $rows[4];
						$kind    = $rows[5];
						$addtime = date_str($rows[6]);
						if($new == 1){
							$new_str = '<i class="new_hi"></i>';
							$btncls  = '';
						}else{
							$new_str = '';
							$btncls  = ' ed';
						}
						if ($kind == 2){
							$row = $db->NUM($senduid,"sex,grade,nickname,photo_s,photo_f,birthday,pay,areatitle,heigh,edu");
							$sex      = $row[0];
							$grade    = $row[1];
							$nickname = dataIO($row[2],'out');
							$nickname = urldecode($nickname);
							$photo_s  = $row[3];
							$photo_f  = $row[4];
							$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
							$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
							$img_str     = '<a href="'.Href('u',$senduid).'"><img src="'.$photo_s_url.'"'.$sexbg.'></a>';
							if($kind == 2){
								$kind_str = '<i class="ico k2">&#xe69a;</i>';
							}elseif($kind == 3){
								$kind_str = '<i class="ico k3">&#xe6bd;</i>';
							}
							$title       = '<a href="'.Href('u',$senduid).'">'.$nickname.uicon($sex.$grade).'</a>';
							$content = dataIO($rows[3],'out');
							$btn_str='<font>查看</font>';
						}else{//1,4
							if($kind == 1){
								$img_str = '<i class="ico k1">&#xe654;</i>';
								$title   = '系统消息';
							}elseif($kind == 4){
								$img_str = '<i class="ico k4">&#xe605;</i>';
								$title   = '红娘消息';
							}
							$kind_str = '';
							$content = dataIO($rows[2],'out');
							$btn_str='<font>详情</font>';
						}
                    ?>
                    <dl>
                        <dt tid="<?php echo $id; ?>" kind="<?php echo $kind;?>"><?php echo $img_str; ?></dt>
                        <?php echo $new_str; ?>
                        <dd><h4><?php echo $title; ?></h4><h6><?php echo $content; ?></h6></dd>
                        <span><?php echo $addtime; ?></span>
                        <button type="button" class="btn size3<?php echo $btncls;?>"><?php echo $btn_str;?></button>
                        <b onClick="tzdel(<?php echo $id;?>);" title="删除"><i class="ico">&#xe65b;</i></b>
                    </dl>
                    <?php }
                    if ($total > $pagesize)echo '<div class="pagebox mypagebox">'.$pagelist.'</div>';
                }else{echo nodatatips('暂时还没有通知');}
            }
            ?>
        	</div>
        </div>
        <!-- end C -->
</div></div></div>
<script src="js/my_msg.js"></script>

<?php if($t==3){?>
<div id="msg_gift_box" class="box_gift">
    <em><img><h3></h3><h6></h6></em>
    <a href="javascript:;">看TA资料</a>
    <a href="javascript:;">回赠礼物</a>
</div>
<?php }?>
<div id='tips0_100_msg_hi' class='tips0_100_0 alpha0_100_0'></div>
    
<script>
var lovebstr='<?php echo $_ZEAI['loveB'];?>'
<?php if($t==3 || $t==4){echo 'msgtzFn();';}?>
</script>

<?php require_once ZEAI.'p1/bottom.php';ob_end_flush();?>
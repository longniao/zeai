<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'活动参数错误'));
if(is_mobile())header("Location: ".wHref('party',$fid));
require_once ZEAI.'sub/conn.php';
if($submitok == 'ajax_bbs_add_update'){
	if (empty($content))json_exit(array('flag'=>0,'msg'=>'请输入内容'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发表'));
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来发表','jumpurl'=>Href('party',$fid)));
	$content = dataIO($content,'in',280);
	$db->query("INSERT INTO ".__TBL_PARTY_BBS__." (uid,fid,content,addtime) VALUES ($cook_uid,$fid,'$content',".ADDTIME.")");
	$db->query("UPDATE ".__TBL_PARTY__." SET bbsnum=bbsnum+1 WHERE id=".$fid);
	setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
	json_exit(array('flag'=>1,'msg'=>'发表成功'));
}elseif($submitok == 'ajax_bm_ifpay'){
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来报名','jumpurl'=>Href('party',$fid)));
	$rowf = $db->ROW(__TBL_PARTY__,"title,ifpay","flag=1 AND id=".$fid,'name');
	if (!$rowf)json_exit(array('flag'=>0,'msg'=>'活动已经结束'));
	$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","uid=".$cook_uid." AND fid=".$fid);
	if ($row2){
		if($rowf['ifpay']==1 && $row2['ifpay']==0){
			//没交费，跳转交费
			json_exit(array('flag'=>0,'ifpay'=>1,'msg'=>'您已经报名成功，请在线支付报名费'));
		}
	}
	json_exit(array('flag'=>0,'ifpay'=>0));
}elseif($submitok == 'ajax_bm_add_update'){//step 2
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来报名','jumpurl'=>Href('party',$fid)));
	if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请留下手机号码，方便活动通知您哦'));

	$rowf = $db->ROW(__TBL_PARTY__,"flag,ifpay","flag=1 AND id=".$fid,'name');
	if (!$rowf)json_exit(array('flag'=>0,'msg'=>'活动已经结束'));
	$ifpay=$rowf['ifpay'];
	
	$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","uid=".$cook_uid." AND fid=".$fid,'name');
	if ($row2){
		if($row2['ifpay']==1)json_exit(array('flag'=>0,'msg'=>'您已经报过名，也交过费了'));
		if($ifpay==1)json_exit(array('flag'=>1,'fid'=>$fid,'ifpay'=>1,'msg'=>'您已经报过名，请在线支付报名费'));
		json_exit(array('flag'=>0,'msg'=>'您已经报过名，请不要重复报名'));
	}else{
		$cook_uid=intval($cook_uid);
		$mob=dataIO($mob,'in',11);
		$weixin=dataIO($weixin,'in',50);
		$truename=dataIO($truename,'in',20);
		$sex=intval($cook_sex);
		$sextitle=($sex==2)?'女':'男';
		$birthday=intval($birthday);
		$db->query("INSERT INTO ".__TBL_PARTY_USER__."  (uid,fid,flag,addtime,truename,birthday,sex,mob,weixin) VALUES ('$cook_uid','$fid',0,".ADDTIME.",'$truename','$birthday','$sex','$mob','$weixin')");
		$db->query("UPDATE ".__TBL_PARTY__." SET bmnum=bmnum+1 WHERE flag=1 AND id=".$fid);
		
	}
	json_exit(array('flag'=>1,'fid'=>$fid,'ifpay'=>$ifpay,'msg'=>'报名成功'));
}
$rt = $db->query("SELECT * FROM ".__TBL_PARTY__." WHERE flag>0 AND path_s<>'' AND id=".$fid);
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'name');
	$path_s   = $row['path_s'];
	$pathlist = $row['pathlist'];
	$jzbmtime = $row['jzbmtime'];
	$flag     = $row['flag'];
	$ifpay = $row['ifpay'];
	$bmnum = $row['bmnum'];
	$signnum = $row['signnum'];
	$bbsnum  = $row['bbsnum'];
	$hdtime= dataIO($row['hdtime'],'out');
	$address = dataIO($row['address'],'out');
	$title = trimhtml(dataIO($row['title'],'out'));
	$num_n = $row['num_n'];
	$num_r = $row['num_r'];
	$rmb_n = $row['rmb_n'];
	$rmb_r = $row['rmb_r'];
	$content  = dataIO($row['content'],'out');
	$path_s_url = $_ZEAI['up2'].'/'.$path_s;
	$path_b_url = getpath_smb($path_s_url,'b');
	$num_n_str=($num_n==0)?'<b>不限</b>':'<b>'.$num_n.'</b>人';
	$num_r_str=($num_r==0)?'<b>不限</b>':'<b>'.$num_r.'</b>人';
	$rmb_n_str=($rmb_n==0)?'<b class="C090">免费</b>':'<b>'.$rmb_n.'</b>元';
	$rmb_r_str=($rmb_r==0)?'<b class="C090">免费</b>':'<b>'.$rmb_r.'</b>元';	
	$bmnum_str=($bmnum>0)?'<b id="bmnum">'.$bmnum.'</b>':'<b id="bmnum" class="nodata">0</b>';	
	$signum_str=($signnum>0)?'<b>'.$signnum.'</b>':'<b class="nodata">0</b>';	
	$bbsnum_str=($bbsnum>0)?'<b id="bbsnum">'.$bbsnum.'</b>':'<b id="bbsnum" class="nodata">0</b>';
	$bmico = '<i class="ico">&#xe65c;</i>';
	$bmbtn_cls=' class="ed"';
	if($flag==1){
		if(ifint($cook_uid)){
			$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","uid=".$cook_uid." AND fid=".$fid,'name');
			if (!$row2){
				$bmbtn_str=$bmico.'我要报名';
			}else{
				$price=($cook_sex==1)?$rmb_n:$rmb_r;
				if($price>0){
					$bmbtn_str=($row2['ifpay']==1)?'已报名交费':'我要交费';
				}else{
					$bmbtn_str='免费参加';
					$bmbtn_cls=' class="free"';
				}
			}
		}else{
			$bmbtn_str=$bmico.'我要报名';	
		}

	}else{
		$bmbtn_str='活动已结束';
		$bmbtn_cls=' class="off" style="color:#aaa"';
	}
	$difftime=ADDTIME-$jzbmtime;
	if($difftime>86400){
		$db->query("UPDATE ".__TBL_PARTY__." SET flag=3 WHERE id=".$fid);
	}elseif($difftime>0){
		$db->query("UPDATE ".__TBL_PARTY__." SET flag=2 WHERE id=".$fid);
	}
} else {json_exit(array('flag'=>0,'msg'=>'活动不存在'));}
require_once ZEAI.'cache/udata.php';
$nav='party';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $title;?>_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/cache/udata.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
<link href="<?php echo HOST;?>/p1/css/party.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
if ($submitok == 'bbs_add' || $submitok == 'ajax_bm_add'){
	if(!iflogin() || !ifint($cook_uid))exit("<html><body><script>window.onload = function (){parent.location.href='".HOST."/p1/login.php?jumpurl=".Href('party',$fid)."';}</script></body></html>");
}
if ($submitok == 'bbs_add'){?>
	<style>body{background-color:#fff}</style>
	<div class="partybbs_add">
    	<h1>发表活动评论</h1>
        <form id='partyZ_eA_I____cn_bbsbox'>
            <textarea name="content" id="content" placeholder="请文明发言~~" class="textarea"></textarea>
            <h4><span id="inpttext">0</span>/140</h4>
            <input type="hidden" name="submitok" value="ajax_bbs_add_update">
            <input type="hidden" name="fid" value="<?php echo $fid;?>">
            <button type="button" id="partybbs_btn_save" class="btn size3 HONG">提交评论</button>
        </form>
    </div>
	<script src="<?php echo HOST;?>/p1/js/party_detail.js"></script>
	<script>partybbs_btn_save.onclick = partybbs_btn_saveFn;content.oninput = contentFn;</script> 
	<?php echo '</body></html>';exit;
}elseif($submitok == 'ajax_bm_add'){
	$rowf = $db->ROW(__TBL_PARTY__,"title,ifpay","flag=1 AND id=".$fid,'name');
	if (!$rowf)alert('活动已经结束','-1');
	$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","uid=".$cook_uid." AND fid=".$fid);
	if ($row2){
		$paystr=($row2['ifpay']==1)?'，报名费也交过了！':'';
		alert('您已经报过名了'.$paystr,'-1');
	}
	$row = $db->NAME($cook_uid,"sex,grade,nickname,birthday,mob,weixin,truename");
	?>
	<style>body{background-color:#fff}</style>
	<div class="submain party_detail_bm">
    	<div class="ptitle"><?php echo dataIO($rowf['title'],'out');?></div>
        <dl><dt>帐号</dt><dd><em><?php echo uicon($row['sex'].$row['grade']).dataIO($row['nickname'],'out').'　UID：'.$cook_uid;?></em></dd></dl>
        <form id="Www_Zeai_cn_PartyBm">
        <dl><dt>姓名</dt><dd><input name="truename" class="input W100_" value="<?php echo dataIO($row['truename'],'out');?>" placeholder="请输入真实姓名" /></dd></dl>
        <dl><dt>性别</dt><dd>
            <input type="radio" name="sex" id="sex1" class="radioskin" value="1"<?php echo ($row['sex'] == 1)?' checked':'';?>><label for="sex1" class="radioskin-label"><i class="i2"></i><b class="W50 S16">男</b></label>　
            <input type="radio" name="sex" id="sex2" class="radioskin" value="2"<?php echo ($row['sex'] == 2)?' checked':'';?>><label for="sex2" class="radioskin-label"><i class="i2"></i><b class="W50 S16">女</b></label>　
        </dd></dl>
        <dl><dt>生年</dt><dd><input name="birthday" id="birthday" class="input W100_" value="<?php echo substr($row['birthday'],0,4);?>" autocomplete="off" maxlength="4" pattern="[0-9]*" placeholder="出生年份，如：1992"  /></dd></dl>
        <dl><dt>手机</dt><dd><input name="mob" class="input W100_" value="<?php echo dataIO($row['mob'],'out');?>" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*" /></dd></dl>
        <dl><dt>微信</dt><dd><input name="weixin" class="input W100_" value="<?php echo dataIO($row['weixin'],'out');?>" placeholder="请输入微信号" maxlength="30" /></dd></dl>
        <input type="hidden" name="fid" value="<?php echo $fid;?>" />
        <input name="submitok" type="hidden" value="ajax_bm_add_update" />
        </form>
        <button type="button" id="party_detail_bm_btn" class="btn size3 ">开始报名</button>
        <div class="linebox" style="z-index:0"><div class="line BAI "></div><div class="title S12 BAI">温馨提醒</div></div>
        <div>以上信息受隐私保护，仅用于活动报名联系通知，不对外公开。</div>
	</div>
    <script src="<?php echo HOST;?>/p1/js/party_detail.js"></script>
    <script>party_detail_bm_btn.onclick=function(){party_detail_bm_btnFn();}</script>
	<?php echo '</body></html>';exit;
}elseif($submitok == 'ajax_bm_add_update_pay'){//最后一步
	$rowf = $db->ROW(__TBL_PARTY__,"ifpay,rmb_n,rmb_r","flag<>3 AND id=".$fid,'name');
	//if (!$rowf)json_exit(array('flag'=>0,'msg'=>'活动已经结束'));
	if (!$rowf)alert('活动已经结束','-1');
	
	$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","uid=".$cook_uid." AND fid=".$fid,'name');
	//if (!$row2)json_exit(array('flag'=>0,'msg'=>'你从哪来的呢？你还没有报名吧'));
	if (!$row2)alert('你从哪来的呢？你还没有报名吧','-1');
	//if($row2['ifpay']==1)json_exit(array('flag'=>0,'msg'=>'您已经交过费用了'));
	if($row2['ifpay']==1)alert('您已经交过费用了','-1');
	$row = $db->NAME($cook_uid,"sex,grade,nickname,qq,mob,weixin,truename");
	?>
    <style>body{background-color:#fff}</style>
	<div class=" party_detail_bm_pay">
    	<div class="success">
            <i class="ico">&#xe60d;</i>
            <h2 class="B"><font class="S16"><?php echo dataIO($row['nickname'],'out');?></font><br>恭喜您报名成功</h2>
        </div>
        <div class="pay">
        <?php
			$price=($row['sex']==1)?$rowf['rmb_n']:$rowf['rmb_r'];
			if($price>0){?>
                <br><h5 class="C999">您此次需交纳 <font class="S16 Cf00"><?php echo $price;?>元</font> 活动费用</h5><br><br>
                <?php if ($rowf['ifpay'] == 1){
					$jumpurl=HOST.'/p1/party_detail.php?fid='.$fid;?>
                    <button type="button" class="btn size4 LV2 W80_" id="party_nextbtn">立即支付</button>
                    <script>
					party_nextbtn.onclick=function(){
						zeai.openurl(PCHOST+'/my_pay'+zeai.ajxext+'kind=4&money='+parseInt(<?php echo $price;?>)+'&jumpurl=<?php echo urlencode($jumpurl);?>');
					}
                    </script>
                <?php }?>
        	<?php }else{?>
                <br><h5 class="S18 C090">您将免费参加此次活动</h5>
         <?php }?>
    	</div>
    </div>
<?php exit;}?>
<?php require_once ZEAI.'p1/top.php';?>
<div class="party_box party_dtl S5 fadeInL">
	<div class="banner">
        <p style="background-image:url('<?php echo $path_b_url;?>')"></p>
        <div class="djs"><?php echo party_djs($flag,$jzbmtime);?></div>        
    </div>
	<div class="dtl">
    	<h3><?php echo $title;?></h3>
        <h5><i class="ico timee">&#xe634;</i><?php echo $hdtime;?></h5>    
        <h5><i class="ico address">&#xe614;</i><?php echo $address;?></h5>
        <div class="bmrs2">
            <dl><dt><?php echo $num_n_str;?></dt><dd>男士</dd></dl>
            <dl><dt><?php echo $num_r_str;?></dt><dd>女士</dd></dl>
            <dl><dt><?php echo $rmb_n_str;?></dt><dd>男士</dd></dl>
            <dl><dt><?php echo $rmb_r_str;?></dt><dd>女士</dd></dl>
        </div>
        <div class="party_btn" id="BtmNav">
            <a href="#bbs" id="party_bbsbtn"><i class="ico">&#xe676;</i>我要评论</a>
            <a href="#kefu" id="kefubtn"><i class="ico">&#xe60e;</i>我要咨询</a>
            <a id="party_bmbtn"<?php echo $bmbtn_cls;?>><?php echo $bmbtn_str;?></a>
        </div>
        <div class="p2wm">
            <img src="<?php echo HOST;?>/p1/img/uewm.png" id="partyewm">
            <span>手机扫一扫 快速报名</span>
            <div class="linebox" style="margin-top:14px"><div class="line "></div><div class="title S14 BAI">已报名<font class="Cf00"><?php echo $bmnum;?></font>人</div></div>
        </div>
	</div>
</div>
<div class="party_box party_dtlc fadeInR">
	<div class="party_dtlcL">
        <div class="box content S5"><h1>活动详情</h1><?php echo $content; ?>
            <div class="party_kefu" id="party_kefu">
            	<a name="kefu"></a>
                <div class="linebox"><div class="line"></div><div class="title S14 BAI">联系我们</div></div><br>
                如需了解更多请联系客服。
                <?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
                <?php if (!empty($kf_tel)){?><br>电话：<a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a><?php }?>
                <?php if (!empty($kf_mob)){?><br>手机：<a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
                <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>微信扫码加客服微信</font><?php }?>
            </div>
        </div>
		<?php
        if(!empty($pathlist)){
            echo '<div class="box S5"><h1 style="margin-bottom:0">活动展示</h1><div id="piclist">';
            $ARR=explode(',',$pathlist);
            foreach ($ARR as $V){?>
                <li value="<?php echo getpath_smb($_ZEAI['up2'].'/'.$V,'b');?>"></li>
				<?php
            }
            echo '</div></div>';
        }
		?>
        <div class="box S5">
            <div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a></div>        
        </div>
	</div>
	<div class="party_dtlcR">
		<div class="box S5">
			<h1 class="bmh1">报名会员</h1>
            <ul class="ulist">
				<?php 
                $rt=$db->query("SELECT a.photo_ifshow AS Pphoto_ifshow,a.uid,U.uname,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_PARTY_USER__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC");
                $echo = '';$i=0;
                WHILE ($rows = $db->fetch_array($rt,'name')){
                    $i++;
                    $uid      = $rows['uid'];
                    $sex      = $rows['sex'];
                    $photo_s  = $rows['photo_s'];
                    $photo_f  = $rows['photo_f'];
                    $photo_ifshow  = $rows['photo_ifshow'];
					$Pphoto_ifshow = $rows['Pphoto_ifshow'];
                    $grade    = $rows['grade'];
                    $uname    = dataIO($rows['uname'],'out');
					$nickname = dataIO($rows['nickname'],'out');
					$nickname = (empty($nickname))?$uname:$nickname;
					$nickname = (ifmob($nickname))?$uid:$nickname;
                    $photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					if($photo_ifshow==0 || $Pphoto_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
                    $sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
					if($Pphoto_ifshow==0){
						$echo .= '<a class="m">';
						$echo .='<img src='.$photo_s_url.' '.$sexbg.'>';
						$echo .= '<span>*****</span>';
						$echo .= '</a>';
					}else{
						$echo .= '<a href="'.Href('u',$uid).'" target="_blank" class="m">';
						$echo .='<img src='.$photo_s_url.' '.$sexbg.'>';
						$echo .= '<span>'.$nickname.'</span>';/*.uicon($sex.$grade)*/
						$echo .= '</a>';
					}
                }
                if($i>0){echo $echo;}else{echo nodatatips('暂时还没有人报名','s');}
                ?>            
            </ul>
        </div>
		<div class="box S5">
			<h1>现场签到</h1>
            <ul class="ulist">
				<?php 
                $rt=$db->query("SELECT a.uid,U.uname,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_PARTY_SIGN__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC");
                $echo = '';$j=0;
                WHILE ($rows = $db->fetch_array($rt,'name')){
					$j++;
                    $uid      = $rows['uid'];
                    $sex      = $rows['sex'];
                    $photo_s  = $rows['photo_s'];
                    $photo_f  = $rows['photo_f'];
                    $photo_ifshow = $rows['photo_ifshow'];
                    $grade    = $rows['grade'];
                    $uname    = dataIO($rows['uname'],'out');
					$nickname = dataIO($rows['nickname'],'out');
					$nickname = (empty($nickname))?$uname:$nickname;
					$nickname = (ifmob($nickname))?$uid:$nickname;
                    $photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
					if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
                    $sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
                    $echo .= '<a href="'.Href('u',$uid).'" target="_blank">';
                    $echo .='<img src="'.$photo_s_url.'"'.$sexbg.'>';
                    $echo .= '<span>'.$nickname.'</span>';/*uicon($sex.$grade).*/
                    $echo .= '</a>';
                }
				if($j>0){echo $echo;}else{echo nodatatips('暂时还没有人签到','s');}
				?>
                <div class="clear"></div>
                <div class="linebox" style="margin:14px 0 5px"><div class="line "></div><div class="title S14 BAI">温馨提醒</div><a name="bbs"></a></div>
                【参加活动时，请携带本人身份证到现场扫码签到】
            </ul>
        </div>
        <button class="btn size4 HONG W100_" type="button" id="party_bbsbtn2" style="margin-bottom:30px"><i class="ico">&#xe676;</i> 我来说两句</button>
		<div class="box S5">
			<h1>活动评论</h1>
            <div class="bbslist">
				<?php 
                $rt=$db->query("SELECT a.uid,a.content,a.addtime,U.uname,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_PARTY_BBS__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC");
                $echo = '';$k=0;
                WHILE ($rows = $db->fetch_array($rt,'name')){
					$k++;
                    $uid      = $rows['uid'];
                    $sex      = $rows['sex'];
                    $photo_s  = $rows['photo_s'];
                    $photo_f  = $rows['photo_f'];
                    $photo_ifshow = $rows['photo_ifshow'];
                    $grade    = $rows['grade'];
                    $uname    = dataIO($rows['uname'],'out');
					$nickname = dataIO($rows['nickname'],'out');
					$nickname = (empty($nickname))?$uname:$nickname;
					$nickname = (ifmob($nickname))?$uid:$nickname;
                    $content  = dataIO($rows['content'],'out');
                    $photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
					if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
                    $sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
                    $addtime    = date_str($rows['addtime']);
                    $echo .= '<dl>';
                    $echo .= '<dt><a href="'.Href('u',$uid).'" target="_blank"><img src="'.$photo_s_url.'"'.$sexbg.'></a></dt>';
                    $echo .= '<dd>';
                    $echo .= '<h6>'.uicon($sex.$grade).$nickname.'</h6>';
                    $echo .= '<em>'.$content.'</em>';
                    $echo .= '<span>'.$addtime.'</span>';
                    $echo .= '</dd>';
                    $echo .= '</dl>';
                }
           		if($k>0){echo $echo;}else{echo nodatatips('暂时还没有人评论','s');}
				?>
			</div>
        </div>
	</div>
</div>
<div class="clear"></div>
<script src="<?php echo HOST;?>/p1/js/party_detail.js"></script>
<script>var fid=<?php echo $fid;?>,supdes;party_bmbtn.onclick=party_bmbtnFn,partyhref='<?php echo mHref('party',$fid);?>';
window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"1","bdSize":"32"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
</script>
<?php require_once ZEAI.'p1/bottom.php';
function party_djs($flag,$jzbmtime) {
	$d1  = ADDTIME;
	$d2  = $jzbmtime;
	$totals  = ($d2-$d1);
	$day     = intval( $totals/86400 );
	$hour    = intval(($totals % 86400)/3600);
	$hourmod = ($totals % 86400)/3600 - $hour;
	$minute  = intval($hourmod*60);
	if ($flag >= 2)$totals = -1;
	if (($totals) > 0) {
		$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> 离截止报名还剩</span>';
		if ($day > 0){
			$outtime = $tmp."<span class=timestyle>$day</span>天";
		} else {
			$outtime = $tmp;
		}
		$outtime .= "<span class=timestyle>$hour</span>小时<span class=timestyle>$minute</span>分钟";
	} else {
		$outtime = '　报名已经结束';
	}
	$outtime = '<font>'.$outtime.'</font>';
	return $outtime;
}
?>

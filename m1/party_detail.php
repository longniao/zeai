<?php
if (ini_get('session.auto_start') == 0)session_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if (!ifint($fid))alert('活动不存在',wHref('party'));
if (empty($cook_openid) && is_weixin()){
	$cook_openid=wx_get_openid();
}
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
//if($submitok == 'ajax_bm_ifpay' || $submitok == 'ajax_bm_add' || $submitok == 'ajax_bm_add_update'|| $submitok == 'ajax_bm_add_update_pay' ){
if($submitok == 'ajax_bbs_add_update' || ifint($cook_uid)){
	$currfields = "sex,mob,weixin,truename,birthday";
	$$rtn='json';$chk_u_jumpurl=wHref('party',$fid);
	require_once ZEAI.'my_chk_u.php';
}
if($submitok == 'ajax_bbs_add_update'){
	if (empty($content))json_exit(array('flag'=>0,'msg'=>'请输入内容'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发表'));
	$row = $db->NAME($cook_uid,"nickname,sex,grade,photo_s,photo_f");
	if ($row){
		$sex      = $row['sex'];
		$photo_s  = $row['photo_s'];
		$photo_f  = $row['photo_f'];
		$grade    = $row['grade'];
		$nickname = dataIO($row['nickname'],'out');
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
		$addtime    = date_str(ADDTIME-1);
		$echo .= '<dl>';
		$echo .= '<dt onClick="PTuA('.$cook_uid.');"><img src="'.$photo_s_url.'"'.$sexbg.'></dt>';
		$echo .= '<dd>';
		$echo .= '<h6>'.uicon($sex.$grade).$nickname.'</h6>';
		$echo .= '<em>'.$content.'</em>';
		$echo .= '<span>'.$addtime.'</span>';
		$echo .= '</dd>';
		$echo .= '</dl>';
	}
	$content = dataIO($content,'in',5000);
	$db->query("INSERT INTO ".__TBL_PARTY_BBS__." (uid,fid,content,addtime) VALUES ($cook_uid,$fid,'$content',".ADDTIME.")");
	$db->query("UPDATE ".__TBL_PARTY__." SET bbsnum=bbsnum+1 WHERE id=".$fid);
	setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
	json_exit(array('flag'=>1,'msg'=>'发表成功','list'=>dataIO($echo,'in')));
}elseif($submitok == 'ajax_party_getBMulist'){
	$ret = party_getBMulist($fid);
	json_exit(array('flag'=>1,'total'=>$ret['total'],'list'=>dataIO($ret['list'],'in')));
}elseif($submitok == 'ajax_bm_ifpay'){
	$rowf = $db->ROW(__TBL_PARTY__,"title,ifpay","flag=1 AND id=".$fid,'name');
	if (!$rowf)json_exit(array('flag'=>0,'msg'=>'活动已经结束'));
	$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","mob='".$cook_mob."' AND mob<>'' AND fid=".$fid);
	if ($row2){
		if($rowf['ifpay']==1 && $row2['ifpay']==0){
			//没交费，跳转交费
			json_exit(array('flag'=>0,'ifpay'=>1,'msg'=>'您已经报名成功，请在线支付报名费'));
		}
	}
	json_exit(array('flag'=>0,'ifpay'=>0));
}elseif($submitok == 'ajax_bm_add'){//step 2
	$rowf = $db->ROW(__TBL_PARTY__,"title,ifpay","flag=1 AND id=".$fid,'name');
	if (!$rowf)json_exit(array('flag'=>0,'msg'=>'活动已经结束'));
	$SQL = (ifint($cook_uid))?" OR uid=$cook_uid":"";
	$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","(mob='".$cook_mob."' AND mob<>'' ".$SQL.") AND fid=".$fid);
	if ($row2){
		$paystr=($row2['ifpay']==1)?'（报名费已交）':'';
		json_exit(array('flag'=>0,'msg'=>'您已经报过名了'.$paystr));
	}
	//
	$party_joingrade = json_decode($_VIP['party_joingrade'],true);
	if(!empty($_VIP['party_joingrade']) && @is_array($party_joingrade) && @count($party_joingrade)>0){
		if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','jumpurl'=>wHref('party',$fid)));
		$row = $db->ROW(__TBL_USER__,"grade","id=".$cook_uid,"num");
		if ($row)$cook_grade= $row[0];
		if(!in_array($cook_grade,$party_joingrade))json_exit(array('flag'=>'nolevel','msg'=>'亲，只有VIP用户才可以参加活动哦','jumpurl'=>wHref('party',$fid)));
	}
	//
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-party_detail_bm">&#xe602;</i>填写报名资料';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '';
    require_once ZEAI.'m1/top_mini.php';
	?>
	<div class="submain party_detail_bm">
    	<div class="ptitle" style="display:none">【<?php echo dataIO($rowf['title'],'out');?>】</div>
        <form id="Www_Zeai_cn_PartyBm">
            <dl><dt>手机</dt><dd><input name="mob" id="mob" class="input W100_" value="<?php echo dataIO($row['mob'],'out');?>" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*" /></dd></dl>
            <dl class="linee"><dt>验证码</dt><dd class="yzmF"><input name="verify" id="verify" class="input W100_"  placeholder="输入手机验证码" autocomplete="off" maxlength="4" pattern="[0-9]*" /><a href="javascript:;" class="yzmbtn" id="yzmbtn">获取验证码</a></dd></dl>
            <dl><dt>姓名</dt><dd><input name="truename" id="truename" class="input W100_" value="<?php echo dataIO($row['truename'],'out');?>" placeholder="请输入真实姓名" onBlur="rettop();" /></dd></dl>
            <dl><dt>性别</dt><dd>
                <input type="radio" name="sex" id="sex1" class="radioskin" value="1"<?php echo ($row['sex'] == 1)?' checked':'';?>><label for="sex1" class="radioskin-label"><i class="i2"></i><b class="W50 S16">男</b></label>　
                <input type="radio" name="sex" id="sex2" class="radioskin" value="2"<?php echo ($row['sex'] == 2)?' checked':'';?>><label for="sex2" class="radioskin-label"><i class="i2"></i><b class="W50 S16">女</b></label>　
            </dd></dl>
            <dl><dt>生年</dt><dd><input name="birthday" id="birthday" class="input W100_" value="<?php echo substr($row['birthday'],0,4);?>" autocomplete="off" maxlength="4" pattern="[0-9]*" placeholder="出生年份，如：1992" onBlur="rettop();" /></dd></dl>
            <dl class="linee"><dt>微信</dt><dd><input name="weixin" id="weixin" class="input W100_" value="<?php echo dataIO($row['weixin'],'out');?>"  placeholder="请输入微信号" maxlength="30" onBlur="rettop();" /></dd></dl>
            <input type="hidden" name="fid" value="<?php echo $fid;?>" />
            <input name="submitok" type="hidden" value="ajax_bm_add_update" />
        </form>
        <button type="button" id="party_detail_bm_btn" class="btn size4"<?php echo ($_ZEAI['mob_mbkind']==3)?' style="background:#FF6F6F";':'';?>>开始报名</button>
        <div class="linebox" style="z-index:0"><div class="line BAI "></div><div class="title S12 BAI">温馨提醒</div></div>
        <div class="ys">以上信息受隐私保护，仅用于活动报名联系通知，不对外公开。</div>
	</div>
    <script>
	party_detail_bm_btn.onclick=function(){party_detail_bm_btnFn();}
	party_detail_bm.addEventListener('touchmove', function(e){e.preventDefault();});
	zeai.setScrollTop(0);
	function rettop(){zeai.setScrollTop(0);}
	if (!zeai.empty(o('yzmbtn'))){
		yzmbtn.onclick = function(){
			if (zeai.ifmob(o('mob').value)){
				if (!this.hasClass('disabled')){
					yzmbtn.addClass('disabled');
					zeai.ajax({'url':HOST+'/m1/party_detail'+zeai.extname,'data':{submitok:'ajax_get_verify',mob:o('mob').value,fid:<?php echo $fid;?>}},function(e){
						var rs=zeai.jsoneval(e);
						if (rs.flag == 1){
							zeai.msg(rs.msg,{time:5});
							o('verify').value='';
							yzmtimeFn(5);
						}else{
							zeai.msg(rs.msg,mob);
							yzmbtn.removeClass('disabled');
						}
					});
				}
			}else{
				zeai.msg('请输入手机号码',mob);
				return false;
			}
		}
	}
    </script>
	<?php
exit;}elseif($submitok == 'ajax_get_verify'){//step 2_mobyzm↑
	if(!ifmob($mob)){
		json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
	}else{
		$SQL = (ifint($cook_uid))?" OR uid=$cook_uid":"";
		if ($db->ROW(__TBL_PARTY_USER__,"id","(mob='$mob'".$SQL.") AND fid=".$fid))json_exit(array('flag'=>0,'msg'=>'您已经报过名了，请不要重复报名'));
	}
	if ($Temp_regyzmrenum > $_SMS['sms_yzmnum'] && $_SMS['sms_yzmnum']>0 )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
	$_SESSION['Zeai_cn__verify'] = cdstr(4);
	//sms
	$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__verify']);
	if ($rtn == 0){
		setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,time()+720000,"/",$_ZEAI['CookDomain']);
		$chkflag = 1;
		$content = '验证码发送成功，请注意查收';
	}else{
		$chkflag = 0;
		$content = "发送失败：".sms_error($rtn);
	}
	//sms end
	$_SESSION['Zeai_cn__mob'] = $mob;
	json_exit(array('flag'=>$chkflag,'msg'=>$content));
}elseif($submitok == 'ajax_bm_add_update'){//step 2
	if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请留下手机号码，方便活动通知您哦'));
	//验证码对比
	$verify = intval($verify);
	if (empty($_SESSION['Zeai_cn__verify'])){
		json_exit(array('flag'=>0,'msg'=>'短信验证码错误，请重新获取'));
	}else{
		if ($_SESSION['Zeai_cn__verify'] != $verify){
			json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
		}
		if ($_SESSION['Zeai_cn__mob'] != $mob && ifmob($mob)){
			unset($_SESSION["Zeai_cn__verify"]);
			unset($_SESSION["Zeai_cn__mob"]);
			setcookie("cook_mob",'',time()+720000,"/",$_ZEAI['CookDomain']);
			json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
		}
	}
	if (empty($truename))json_exit(array('flag'=>0,'msg'=>'请输入【真实姓名】'));
	if (!ifint($birthday))json_exit(array('flag'=>0,'msg'=>'请输入【出生年份】'));

	$rowf = $db->ROW(__TBL_PARTY__,"flag,ifpay,title","flag=1 AND id=".$fid,'name');
	if (!$rowf)json_exit(array('flag'=>0,'msg'=>'活动已经结束'));
	$ifpay=$rowf['ifpay'];$pttitle=dataIO($rowf['title'],'out');
	$SQL = (ifint($cook_uid))?" OR uid=$cook_uid":"";
	$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","(mob='".$cook_mob."' AND mob<>'' ".$SQL.") AND fid=".$fid,'name');
	if ($row2){
		if($row2['ifpay']==1)json_exit(array('flag'=>0,'msg'=>'您已经报过名(费用已交)，无需再提交'));
		if($ifpay==1)json_exit(array('flag'=>1,'fid'=>$fid,'ifpay'=>1,'msg'=>'您已经报过名，请在线支付报名费'));
		json_exit(array('flag'=>0,'msg'=>'您已经报过名，请不要重复报名'));
	}else{
		$cook_uid=intval($cook_uid);
		$mob=dataIO($mob,'in',11);
		$weixin=dataIO($weixin,'in',50);
		$truename=dataIO($truename,'in',20);
		$sex=intval($sex);
		$sextitle=($sex==2)?'女':'男';
		$birthday=intval($birthday);
		$db->query("INSERT INTO ".__TBL_PARTY_USER__."  (uid,fid,flag,addtime,truename,birthday,sex,mob,weixin) VALUES ('$cook_uid','$fid',0,".ADDTIME.",'$truename','$birthday','$sex','$mob','$weixin')");
		$db->query("UPDATE ".__TBL_PARTY__." SET bmnum=bmnum+1 WHERE flag=1 AND id=".$fid);
		setcookie("cook_mob",$mob,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_truename",$truename,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_sex",$sex,time()+720000,"/",$_ZEAI['CookDomain']);
		//
		$rt=$db->query("SELECT openid,subscribe FROM ".__TBL_USER__." WHERE ifadm=1");
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$openid    = $rows['openid'];
				$subscribe = $rows['subscribe'];
				if (!empty($openid) && $subscribe==1){
					$first     = urlencode('有新的用户报名参加活动啦~~');
					$keyword1  = urlencode('活动报名');
					$keyword3  = urlencode($sextitle.'　'.$truename.'　uid：'.$uid.'　手机：'.$mob.'　微信：'.$weixin);
					$remark    = '来自活动【'.$pttitle.'】';
					@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.wHref('party',$fid));
				}
			}
		}
	}
	json_exit(array('flag'=>1,'fid'=>$fid,'ifpay'=>$ifpay,'msg'=>'报名成功'));
}elseif($submitok == 'ajax_bm_add_update_pay'){//step3付款
	$rowf = $db->ROW(__TBL_PARTY__,"ifpay,rmb_n,rmb_r,title","flag<>3 AND id=".$fid,'name');
	if (!$rowf)json_exit(array('flag'=>0,'msg'=>'活动已经结束'));
	$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay,truename","mob='".$cook_mob."' AND mob<>'' AND fid=".$fid,'name');
	if (!$row2)json_exit(array('flag'=>0,'msg'=>'你从哪来的呢？你还没有报名吧'));
	if($row2['ifpay']==1)json_exit(array('flag'=>0,'msg'=>'您已经交过费用了'));
	$truename   = $row2['truename'];
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-party_detail_bm_pay">&#xe602;</i>在线交费';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '';
    require_once ZEAI.'m1/top_mini.php';?>
	<div class="submain party_detail_bm_pay">
    	<div class="success">
            <i class="ico">&#xe60d;</i>
            <h2 class="B"><font class="S16"><?php echo dataIO($cook_truename,'out');?></font><br>恭喜您报名成功</h2>
        </div>
        <div class="pay">
        <?php
			$price=($cook_sex==2)?$rowf['rmb_r']:$rowf['rmb_n'];
			$ptitle=dataIO($rowf['title'],'out');
			if($price>0){?>
                <br><h5 class="C999">您此次需交纳 <font class="S16 Cf00"><?php echo $price;?>元</font> 活动费用</h5><br><br>
                <?php if ($rowf['ifpay'] == 1){
					$jumpurl=wHref('party',$fid);?>
                    <button type="button" class="btn size4 LV2 W80_ yuan" id="party_nextbtn">立即支付</button>
                    <br /><br /><br /><a href="<?php echo $jumpurl;?>" class="S16">暂不支付，先看看</a>
                    <?php if (!ifint($cook_uid)){?><script src="<?php echo HOST;?>/api/zeai_PAY.js?<?php echo $_ZEAI['cache_str'];?>"></script><?php }?>
                    <script>
					party_nextbtn.onclick=function(){
						<?php if (ifint($cook_uid)){?>
						ZeaiM.page.load({url:HOST+'/m1/my_pay.php',data:{kind:4,money:parseInt(<?php echo $price;?>),jumpurl:'<?php echo $jumpurl;?>'}},'party_detail_bm_pay','my_pay');
						<?php }else{
						$orderid='PARTY-'.$cook_mob.'-'.date("YmdHis");?>
						zeai_PAY({money:<?php echo $price;?>,iflogin:0,paykind:'wxpay',tmpid:<?php echo $fid;?>,kind:4,orderid:'<?php echo $orderid;?>',title:encodeURIComponent('<?php echo $ptitle.'(手机：'.$cook_mob.'，姓名：'.$truename.')';?>'),return_url:'<?php echo $jumpurl;?>',jumpurl:'<?php echo $jumpurl;?>'});
						<?php }?>
					}
                    party_bmbtn.html('我要交费');
                    </script>
                <?php }?>
        	<?php }else{?>
                <br><h5 class="S18 C090">您将免费参加此次活动</h5>
                <script>party_bmbtn.class('free');party_bmbtn.html('免费参加');</script>
                <br /><br /><a href="<?php echo wHref('party',$fid);;?>" class="btn size4 HONG2 W80_ yuan"<?php echo ($_ZEAI['mob_mbkind']==3)?' style="background:#FF6F6F";':'';?>>返回活动页</a>
         <?php }?>
    	</div>
    </div>
	<script>
	zeai.ajax({url:HOST+'/m1/party_detail'+zeai.ajxext+'submitok=ajax_party_getBMulist&fid=<?php echo $fid;?>'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			bmnum.html(rs.total);
			party_detail_C2.html('<ul class="signlist">'+htmlspecialchars_decode(rs.list)+'</ul>');
			bmnum.removeClass('nodata');
		}
	});
	party_detail_bm_pay.addEventListener('touchmove', function(e){e.preventDefault();});
	zeai.setScrollTop(0);
	</script>
<?php exit;}

/*********************** detail php ***************************/
if(!is_mobile())header("Location: ".Href('party',$fid));
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
	$hdtime = str_replace(" 0时0分","",$hdtime);
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
	//$bmico = '<i class="ico">&#xe65c;</i>';
	if($flag==1){
		if(ifint($cook_uid)){
			$row2 = $db->ROW(__TBL_PARTY_USER__,"ifpay","uid=".$cook_uid." AND fid=".$fid,'name');
			if (!$row2){
				$bmbtn_str=$bmico.'我要报名';
			}else{
				$price=($cook_sex==1)?$rmb_n:$rmb_r;
				if($price>0){
					$bmbtn_str=($row2['ifpay']==1)?'已交费':'我要交费';
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
		$bmbtn_cls=' style="background:#aaa"';
	}
} else {
	alert('活动不存在',wHref('party'));
}
	$headertitle = $title;
	require_once ZEAI.'m1/header.php';
/*********************** detail html***************************/
?>
<link href="<?php echo HOST;?>/m1/css/party_detail.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<?php if($_ZEAI['mob_mbkind']==3){?>
	<style>
    #party_detail_nav li.ed span{color:#FF6F6F}
    #party_detail_nav i{background:#FF6F6F}
    #party_bmbtn{background:#FF6F6F}
    #backtop a{color:#FF6F6F}
    </style>
<?php }?>
<div class="party_detail huadong" id="main">
    <i class="ico goback Ugoback" id="ZEAIGOBACK-party_detail" onclick="zeai.back()">&#xe602;</i>
	<div class="banner">
        <img src="<?php echo $path_b_url;?>" class="banner">
        <div class="djs"><?php echo party_djs($flag,$jzbmtime);?></div>        
    </div>
    <div class="titleinfo">
        <h3><?php echo $title;?></h3>
        <h5><i class="ico timee">&#xe634;</i><?php echo $hdtime;?></h5>    
        <h5><i class="ico address">&#xe614;</i><?php echo $address;?></h5>
    </div>
    
    <div class="bmrs2">
    	<dl><dt><?php echo $num_n_str;?></dt><dd>男士</dd></dl>
    	<dl><dt><?php echo $num_r_str;?></dt><dd>女士</dd></dl>
    	<dl><dt><?php echo $rmb_n_str;?></dt><dd>男士</dd></dl>
    	<dl><dt><?php echo $rmb_r_str;?></dt><dd>女士</dd></dl>
    </div>
	<div class="Cbox">
        <div class="tabmenu tabmenu_4 tabmenuParty" id="party_detail_nav">
            <li id="party_detail1btn" class="ed"><span>活动详情</span></li>
            <li id="party_detail2btn"><span>报名<?php echo $bmnum_str;?></span></li>
            <li id="party_detail3btn"><span>签到<?php echo $signum_str;?></span></li>
            <li id="party_detail4btn"><span>评论<?php echo $bbsnum_str;?></span></li>
            <i></i>
        </div>
    	<div class="C">
        	<div class="C1 fadeInL" id="party_detail_C1">
				<?php echo $content;
                if(!empty($pathlist)){
                    $ARR=explode(',',$pathlist);
                    foreach ($ARR as $V){echo '<img src="'.getpath_smb($_ZEAI['up2'].'/'.$V,'b').'">';}
                }
                ?>
                <div class="party_kefu">
                    <div class="linebox"><div class="line"></div><div class="title S14 BAI">联系我们</div></div><br>
                    遇到问题？请联系客服帮忙。
                    <?php $kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); ?>
                    <?php if (!empty($kf_tel)){?><br>电话：<a href="tel:<?php echo $kf_tel;?>"><?php echo $kf_tel;?></a><?php }?>
                    <?php if (!empty($kf_mob)){?><br>手机：<a href="tel:<?php echo $kf_mob;?>"><?php echo $kf_mob;?></a><?php }?>
                    <?php if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
                </div>
            </div>
        	<div class="C2 fadeInL" id="party_detail_C2">
				<ul class="signlist"><?php echo party_getBMulist($fid,'list')?></ul>
            </div>
        	<div class="C3 fadeInL" id="party_detail_C3">
            	<ul class="signlist"><?php echo party_getSignUlist($fid)?></ul>
                <br><br><div class="linebox" style="z-index:0"><div class="line BAI W50"></div><div class="title S12 BAI">温馨提醒</div></div><center>【参加活动时，请携带本人身份证到现场签到】</center><br>
            </div>
            <div class="C4 fadeInL" id="party_detail_C4">
				<div id="party_bbsbox"><?php echo party_getBBSulist($fid);?></div>
                <form id="Www_Z_e_a_i_C_n_Party"><textarea id="content" name="content" class="textarea" placeholder="我想说两句...请文明发言~~" onBlur="zeai.setScrollTop(0);"></textarea>
				<input name="fid" type="hidden" value="<?php echo $fid;?>" />
                <input name="submitok" type="hidden" value="ajax_bbs_add_update" />
                </form>
                <button type="button" id="party_bbs_btn" class="btn size3">提交评论</button>
            </div>
        </div>
    </div>
</div>
<div id="backtop"><a href="#top" id="btmTopBtn"><i class="ico">&#xe60a;</i>顶部</a></div>
<div id="partyshare"><div class="loop_s_b_s"><i class="ico">&#xe615;</i> 分享</div></div>
<div id="partysharebox">
	<li><i class="ico2" onClick="copy('【<?php echo $title;?>】这个活动不错，分享给你看看。<?php echo wHref('party',$fid); ?>');">&#xe616;</i><span>复制链接</span></li>
	<li id="zeai_haibaobtn"><i class="ico2">&#xea3b;</i><span>生成海报</span></li>
	<li id="wxshare"><i class="ico">&#xe607;</i><span>微信分享</span></li>
</div>
<div class="party_detailBtmBM" id="BtmNav">
    <a href="#content" class="bbs" id="party_bbsbtn"><i class="ico">&#xe676;</i>说两句</a>
    <a class="kefu" id="party_kefubtn"><i class="ico">&#xe60e;</i>我要咨询</a>
    <a id="party_bmbtn"<?php echo $bmbtn_cls;?>><?php echo $bmbtn_str;?></a>
</div>
<div id="share_mask" class="mask1"></div>
<div id="share_box"><img src="<?php echo HOST;?>/res/shareico.png"></div>

<div id="card_detail">
	<div class="cardbox">
		<div class="pcard" id="cardcontent">
        	<div class="plogo"><dl><dt><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo'];?>?<?php echo $_ZEAI['cache_str'];?>"></dt><dd><font><?php echo $_ZEAI['siteName'];?></font><font>向您推荐相亲活动</font></dd></dl></div>
        	<img src="<?php echo $path_b_url;?>" class="partypic">
            <h3 class="title"><?php echo $title;?></h3>
            <div class="titleinfo">
                <h5>活动时间：<?php echo $hdtime;?></h5>    
                <h5>活动地点：<?php echo $address;?></h5>
            </div>
            <img src="<?php echo HOST.'/sub/creat_ewm.php?url='.wHref('party',$fid);?>" class="card_ewm">
            <h6>识别二维码参加活动</h6>
		</div>
	</div>
	<div class="cardbox_view" id="cardbox_view" ></div>
</div>

<div id="Zeai_cn__PageBox"></div><div id="blankpage"></div>
<script src="<?php echo HOST;?>/res/html2canvas.js"></script>    
<script src="<?php echo HOST;?>/res/html2canvas_img.js"></script>
<script src="<?php echo HOST;?>/m1/js/party_detail.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>var fid=<?php echo $fid;?>;</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	var share_party_detail_title = '<?php echo $title; ?>_<?php echo $_ZEAI['siteName'];?>',
	share_party_detail_desc  = '<?php echo dataIO(trimhtml($content),'out',50); ?>',
	share_party_detail_link  = '<?php echo wHref('party',$fid); ?>',
	share_party_detail_imgurl= '<?php echo $path_s_url; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_party_detail_title,desc:share_party_detail_desc,link:share_party_detail_link,imgUrl:share_party_detail_imgurl});
		wx.onMenuShareTimeline({title:share_party_detail_title,link:share_party_detail_link,imgUrl:share_party_detail_imgurl});
	});
	</script>
<?php }?>
<?php
if (!empty($_GZH['wx_gzh_ewm']) && ifint($cook_uid)){wx_endurl('您刚刚浏览的页面【交友活动】'.$title,wHref('party',$fid));}
function party_getBMulist($fid,$list='') {
	global $db,$_ZEAI;
	$rt=$db->query("SELECT a.photo_ifshow AS Pphoto_ifshow,a.uid,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_PARTY_USER__." a,".__TBL_USER__." U WHERE a.uid=U.id AND a.fid=$fid ORDER BY a.id DESC");
	$echo = '';
	$i=0;
	WHILE ($rows = $db->fetch_array($rt,'name')){
		$i++;
		$uid      = $rows['uid'];
		$sex      = $rows['sex'];
		$photo_s  = $rows['photo_s'];
		$photo_f  = $rows['photo_f'];
		$photo_ifshow = $rows['photo_ifshow'];
		$grade    = $rows['grade'];
		$nickname = dataIO($rows['nickname'],'out');
		$Pphoto_ifshow = $rows['Pphoto_ifshow'];
		$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		if($photo_ifshow==0 || $Pphoto_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
		if($Pphoto_ifshow==0){
			$echo .= '<li>';
			$nickname = '*****';
		}else{
			$echo .= '<li onClick="PTuA('.$uid.');">';
		}
		$echo .='<img src='.$photo_s_url.' '.$sexbg.'>';
		$echo .= '<span>'.$nickname.'</span>';/*.uicon($sex.$grade)*/
		$echo .= '</li>';
	}
	$totalnum = $db->COUNT(__TBL_PARTY_USER__,"fid=".$fid);
	$db->query("UPDATE ".__TBL_PARTY__." SET bmnum=".$totalnum." WHERE id=".$fid);
	$nodatatips = "<div class='nodatatips' style='margin:20px auto'><i class='ico'>&#xe651;</i><br>暂时还没有注册用户报名<br>（游客报名信息不显示）</div>";
	$echo = (empty($echo))?$nodatatips:$echo.'<div class="clear"></div><br><div class="linebox" style="z-index:0"><div class="line BAI W50"></div><div class="title S12 BAI">温馨提醒</div></div><center>【游客报名信息不显示】</center><br>';
	if($list=='list')return $echo;
	return array('total'=>$totalnum,'list'=>$echo);
}
function party_getSignUlist($fid) {
	global $db,$_ZEAI;
	$rt=$db->query("SELECT a.uid,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_PARTY_SIGN__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC");
	$echo = '';
	$i=0;
	WHILE ($rows = $db->fetch_array($rt,'name')){
		$i++;
		$uid      = $rows['uid'];
		$sex      = $rows['sex'];
		$photo_s  = $rows['photo_s'];
		$photo_f  = $rows['photo_f'];
		$photo_ifshow = $rows['photo_ifshow'];
		$grade    = $rows['grade'];
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
		$echo .= '<li onClick="PTuA('.$uid.');">';
		$echo .='<img src="'.$photo_s_url.'"'.$sexbg.'>';
		$echo .= '<span>'.$nickname.'</span>';/*uicon($sex.$grade).*/
		$echo .= '</li>';
	}
	$db->query("UPDATE ".__TBL_PARTY__." SET signnum=".$i." WHERE id=".$fid);
	return $echo;
}
function party_getBBSulist($fid) {
	global $db,$_ZEAI;
	$rt=$db->query("SELECT a.uid,a.content,a.addtime,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f,U.photo_ifshow FROM ".__TBL_PARTY_BBS__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC");
	$echo = '';
	$i=0;
	WHILE ($rows = $db->fetch_array($rt,'name')){
		$i++;
		$uid      = $rows['uid'];
		$sex      = $rows['sex'];
		$photo_s  = $rows['photo_s'];
		$photo_f  = $rows['photo_f'];
		$photo_ifshow = $rows['photo_ifshow'];
		$grade    = $rows['grade'];
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
		$content  = dataIO($rows['content'],'out');
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
		$addtime    = date_str($rows['addtime']);
		$echo .= '<dl>';
		$echo .= '<dt onClick="PTuA('.$uid.');"><img src="'.$photo_s_url.'"'.$sexbg.'></dt>';
		$echo .= '<dd>';
		$echo .= '<h6>'.uicon($sex.$grade).$nickname.'</h6>';
		$echo .= '<em>'.$content.'</em>';
		$echo .= '<span>'.$addtime.'</span>';
		$echo .= '</dd>';
		$echo .= '</dl>';
	}
	$db->query("UPDATE ".__TBL_PARTY__." SET bbsnum=".$i." WHERE id=".$fid);
	return $echo;
}
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
		$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> 截止报名还剩</span>';
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
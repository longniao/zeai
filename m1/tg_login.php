<?php
if (ini_get('session.auto_start') == 0)session_start();
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
//if (ifint($cook_admid)){header("Location: tg_my.php");exit;}
if (!is_mobile())exit('请用手机打开');
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
require_once ZEAI.'cache/config_shop.php';
$TG_set = json_decode($_REG['TG_set'],true);
//
if(ifint($cook_tg_uid) && !empty($cook_tg_pwd)){
	$rowtg = $db->ROW(__TBL_TG_USER__,"id,uname,pwd","id=".$cook_tg_uid." AND pwd='$cook_tg_pwd'","name");
	if ($rowtg){
		if($loginkind=='shop'){
			$jumpurl=(!empty($jumpurl))?urldecode($jumpurl):HOST.'/m4/shop_my.php';
			header("Location: ".$jumpurl);
		}else{
			header("Location: tg_my.php");
		}
	}
}else{
	if(ifint($cook_uid) && !empty($cook_pwd)){
		$rowU = $db->ROW(__TBL_USER__,"uname,pwd,nickname,mob,RZ,openid,subscribe,weixin,qq,aboutus,areaid,areatitle,photo_s,tguid","id=".$cook_uid." AND pwd='$cook_pwd'");
		if($rowU){
			$rowtg = $db->ROW(__TBL_TG_USER__,"id,uname,pwd","uid=".$cook_uid,"name");
			if ($rowtg){
				$cook_tg_uid   = $rowtg['id'];
				$cook_tg_uname = $rowtg['uname'];
				$cook_tg_pwd   = $rowtg['pwd'];
				setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
				if($loginkind=='shop'){
					$jumpurl=(!empty($jumpurl))?urldecode($jumpurl):HOST.'/m4/shop_my.php';
					header("Location: ".$jumpurl);
				}else{
					header("Location: tg_my.php");
				}
			}else{
				//新增
				$mob= $rowU['mob'];
				$uname=$rowU['uname'];
				$pwd  = $rowU['pwd'];
				$openid    =$rowU['openid'];
				$subscribe =$rowU['subscribe'];
				$RZ      = $rowU['RZ'];$RZarr = explode(',',$RZ);
				$weixin  = $rowU['weixin'];
				$qq      = $rowU['qq'];
				$aboutus = $rowU['aboutus'];
				$areaid  = $rowU['areaid'];
				$areatitle = $rowU['areatitle'];
				$nickname  = $rowU['nickname'];
				$photo_s   = $rowU['photo_s'];
				$U_tguid   = intval($rowU['tguid']);
				if(ifmob($mob) && in_array('mob',$RZarr)){
					$rowtg2 = $db->ROW(__TBL_TG_USER__,"id,uname,mob,pwd","mob='$mob' AND FIND_IN_SET('mob',RZ)","name");
					if($rowtg2){
						$cook_tg_uid   = $rowtg2['id'];
						$cook_tg_uname = $rowtg2['uname'];
						$cook_tg_mob   = $rowtg2['mob'];
						$cook_tg_pwd   = $rowtg2['pwd'];
						setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
						setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
						setcookie("cook_tg_mob",$cook_tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
						setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
					}else{
						$ifadd=true;
					}
				}else{
					$ifadd=true;
				}
				//
				if($ifadd){
					$ip     =getip();
					$row2 = $db->ROW(__TBL_TG_USER__,"id","uname='$uname'");
					if($row2)$uname=$cook_uid;
					//
					if(!empty($photo_s)){
						$dbdir  = 'p/tg/'.date('Y').'/'.date('m').'/';
						@mk_dir(ZEAI.'/up/'.$dbdir);
						//
						$old_s = $photo_s;
						$old_m = smb($photo_s,'m');
						$old_b = smb($photo_s,'b');
						$old_blur = smb($photo_s,'blur');
						//
						$oldDST_s = ZEAI.'/up/'.$old_s;
						$oldDST_m = ZEAI.'/up/'.$old_m;
						$oldDST_b = ZEAI.'/up/'.$old_b;
						//$oldDST_blur = ZEAI.'/up/'.$old_blur;
						//
						$newDST_s = ZEAI.'/up/'.$dbdir.basename($oldDST_s);
						$newDST_m = ZEAI.'/up/'.$dbdir.basename($oldDST_m);
						$newDST_b = ZEAI.'/up/'.$dbdir.basename($oldDST_b);
						//$newDST_blur = ZEAI.'/up/'.$dbdir.basename($oldDST_blur);
						//
						@copy($oldDST_s,$newDST_s);
						@copy($oldDST_m,$newDST_m);
						@copy($oldDST_b,$newDST_b);
						//@copy($oldDST_blur,$newDST_blur);
						$new_photo_s = $dbdir.basename($oldDST_s);
					}
					//
					$db->query("INSERT INTO ".__TBL_TG_USER__." (tguid,uid,uname,nickname,pwd,regtime,endtime,regip,endip,openid,subscribe,qq,weixin,content,areaid,areatitle,photo_s) VALUES ($U_tguid,$cook_uid,'$uname','$nickname','".$pwd."',".ADDTIME.",".ADDTIME.",'$ip','$ip','$openid','$subscribe','$qq','$weixin','$aboutus','$areaid','$areatitle','$new_photo_s')");
					$cook_tg_uid = intval($db->insert_id());
					if(ifmob($mob) && in_array('mob',$RZarr)){
						$db->query("UPDATE ".__TBL_TG_USER__." SET mob='$mob',RZ='mob' WHERE id=".$cook_tg_uid);
					}
					$cook_tg_pwd = $pwd;
					$cook_tg_openid= $openid;
					setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_tg_openid",$cook_tg_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
					if($loginkind=='shop'){
						$jumpurl=(!empty($jumpurl))?urldecode($jumpurl):HOST.'/m4/shop_my.php';
						header("Location: ".$jumpurl);
					}else{
						header("Location: tg_my.php");
					}
				}
			}
		}
	}
}
//微信自动进入
if(is_weixin()){
	if(isset($cook_tg_openid) && !empty($cook_tg_openid) ){
		$server_tg_openid = $cook_tg_openid;
	}else{
		$server_tg_openid = wx_get_openid(0);
		setcookie("cook_tg_openid",$server_tg_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
	}
	$row = $db->ROW(__TBL_TG_USER__,"id,uname,flag,mob,kind,pwd,subscribe","openid<>'' AND openid='".$server_tg_openid."'","num");
	if ($row){
		$tg_uid = $row[0];$tg_uname = $row[1];$flag = $row[2];$tg_mob = $row[3];$tg_kind = $row[4];$tg_pwd = $row[5];$tg_subscribe = $row[6];
		if ($flag==-1)alert('您的帐号已被锁定','back');
		setcookie("cook_tg_uid",$tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_mob",$tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_uname",$tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_pwd",$tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_kind",$tg_kind,time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_tg_subscribe",$tg_subscribe,time()+720000,"/",$_ZEAI['CookDomain']);
		if($loginkind=='shop'){
			$jumpurl=(!empty($jumpurl))?urldecode($jumpurl):HOST.'/m4/shop_my.php';
			header("Location: ".$jumpurl);
		}else{
			header("Location: tg_my.php");
		}
	}
}
//微信自动进入END
/*************AJAX页面开始*************/
switch ($submitok) {
	case 'ajax_login_uname_chk':
		$uname=trimhtml($uname);
		$pwd  =trimhtml($pwd);
		$chkflag = 1;
		$uname = dataIO($uname,'in');$pwd = dataIO($pwd,'in');//$jumpurl = dataIO($jumpurl,'in');
		if (str_len($uname) > 20 || str_len($uname) < 1) {$content="请输入正确的登录帐号";$chkflag=0;}
		if (str_len($pwd) > 20 || str_len($pwd) < 6) {$content="密码长度必须在6~20字节";$chkflag=0;}
		if ($chkflag == 0)exit(json_encode(array('flag'=>$chkflag,'msg'=>$content)));
		if (ifint($uname,'0-9','1,8')){
			$tmpNAME = "id='$uname'";
		}elseif(ifmob($uname)){
			$tmpNAME = "mob='$uname' AND FIND_IN_SET('mob',RZ) ";
		}else{
			$tmpNAME = "uname='$uname'";
		}
		$pwd = md5(trimm($pwd));
		$rt = $db->query("SELECT id,uname,flag,mob,kind,pwd,subscribe FROM ".__TBL_TG_USER__." WHERE ".$tmpNAME." AND pwd='$pwd'");
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'num');
			$tg_uid = $row[0];$tg_uname = $row[1];$flag = $row[2];$tg_mob = $row[3];$tg_kind = $row[4];$tg_pwd = $row[5];$tg_subscribe = $row[6];
			//if ($flag==0){json_exit(array('flag'=>0,'msg'=>'您已注册，请等待客审核'));}
			if ($flag==-1){json_exit(array('flag'=>0,'msg'=>'您的帐号已被锁定'));}
			setcookie("cook_tg_uid",$tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_mob",$tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_uname",$tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_pwd",$tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_kind",$tg_kind,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_subscribe",$tg_subscribe,time()+720000,"/",$_ZEAI['CookDomain']);
			$endip=getip();
			$db->query("UPDATE ".__TBL_TG_USER__." SET endtime=".ADDTIME.",endip='$endip',logincount=logincount+1 WHERE id=".$tg_uid);
			$chkflag = 1;
		}else{
			$chkflag = 0;
			$content = "帐号密码不正确";
		}
		if(empty($jumpurl))$jumpurl=($loginkind=='shop')?HOST.'/m4/shop_my.php':'tg_my.php';
		json_exit(array('flag'=>$chkflag,'msg'=>$content,'jumpurl'=>$jumpurl));
	break;
	case 'ajax_getverify':
		if(@!in_array('tg',$navarr))exit("<div style='font-size:30px;text-align:center;margin:50px'>推广功能暂未开启</div>");
		if ($Temp_regyzmrenum > $_SMS['sms_yzmnum'] && $_SMS['sms_yzmnum']>0 )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}else{
			if ($db->ROW(__TBL_TG_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)")){
				$_SESSION['Zeai_cn__verify'] = cdstr(4);
				//sms
				$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__verify']);
				if ($rtn == 0){
					setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,time()+720000,"/",$_ZEAI['CookDomain']);  
					$chkflag = 1;
					$content = '验证码发送成功，请注意查收';
				}else{
					$chkflag = 0;
					$content = "发送失败,错误码：$rtn";
				}
				//sms end
				$_SESSION['Zeai_cn__mob'] = $mob;
				json_exit(array('flag'=>$chkflag,'msg'=>$content));
			}else{
				json_exit(array('flag'=>0,'msg'=>'此手机号码未注册或未认证'));	
			}
		}
		exit;
	break;
	case 'tg_login_forgetpass':
        $mini_title = '<i class="ico goback" id="ZEAIGOBACK-tg_login_forgetpass">&#xe602;</i>忘记密码';
        $mini_class = 'top_mini top_miniBAI';
        $mini_backT = '返回';
        require_once ZEAI.'m1/top_mini.php';?>
        <style>
		.submainX{width:100%;max-width:640px;bottom:0;overflow:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;box-sizing:border-box;position:absolute;top:44px;text-align:left}
		.submainX{background-color:#fff;padding:30px;line-height:200%;font-size:16px}
        .submainX{padding:0}
        .submainX dl dt{width:30%;float:left}
        .submainX dl dd{width:70%;float:right}
        .submainX .tg_mobcert_str{text-align:center}
        .submainX .tg_mobcert_str div{border:0;font-size:18px}
        </style>
        <div class="submainX tgreg">
            <form id="WWW__ZEAI_CN_form" style="margin:20px;padding-top:0">
                <div class="tg_mobcert_str"><?php echo $rz_str;?></div>
                <dl style="padding-top:0px;margin-top:0"><dt><i class="ico">&#xe627;</i></dt><dd><input name="mob" type="text" class="input_login" id="mob" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*"></dd></dl>
                <dl><dt><i class="ico">&#xe6c3;</i></dt><dd class="yzmF">
                <input name="verify" id="verify" type="text" required class="input_login" maxlength="4" placeholder="输入手机短信验证码" autocomplete="off" /><a href="javascript:;" class="yzmbtn" id="yzmbtn">获取验证码</a>
                </dd></dl>
                <button type="button" class="btn size4 HONG4 B yuan" onClick="my_info_save();" style="width:80%;left:10%;background-color:#F7564D;color:#fff">开始修改</button>
            </form>
        </div>
        <script>
            if (!zeai.empty(o('yzmbtn'))){
                yzmbtn.onclick = function(){
                    if (zeai.ifmob(o('mob').value)){
                        if (!this.hasClass('disabled')){
                            yzmbtn.addClass('disabled');
                            zeai.ajax({'url':'tg_login'+zeai.extname,'data':{'submitok':'ajax_getverify',mob:o('mob').value}},function(e){
                                var rs=zeai.jsoneval(e);
                                if (rs.flag == 1){
                                    zeai.msg(rs.msg,{time:5});
                                    o('verify').value='';
                                    yzmtimeFn(120);
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
			function yzmtimeFn(countdown) { 
				if (countdown == 0) {
					yzmbtn.removeClass('disabled');
					yzmbtn.html('<font>重新获取</font>'); 
					return false;
				} else { 
					if (!zeai.empty(o('yzmbtn'))){
						yzmbtn.addClass('disabled');
						yzmbtn.html('<b>'+countdown + "S</b>后重新发送"); 
						countdown--; 
					}
				} 
				cleandsj=setTimeout(function(){yzmtimeFn(countdown)},1000);
			}
            function my_info_save(){
                var mobV = o('mob').value,verifyV=o('verify').value;
                if(!zeai.ifmob(mobV)){zeai.msg('请输入正确手机号',o('mob'));return false;}
                if(!zeai.ifint(verifyV) || zeai.str_len(verifyV)!=4 ){zeai.msg('请输入【手机验证码】',o('verify'));return false;}
                zeai.ajax({url:'tg_login'+zeai.ajxext+'submitok=tg_login_forgetpass_update',form:WWW__ZEAI_CN_form},function(e){rs=zeai.jsoneval(e);
                    zeai.msg(0);zeai.msg(rs.msg);
                    if(rs.flag==1)setTimeout(function(){o('ZEAIGOBACK-tg_login_forgetpass').click();},1000);
                });
            }
        </script>    
		<?php
		exit;
	break;
	case 'tg_login_forgetpass_update':
		if (!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请输入正确的手机号码'));
		$row = $db->ROW(__TBL_TG_USER__,'id',"mob='".$mob."' AND FIND_IN_SET('mob',RZ) ");
		if(!$row)json_exit(array('flag'=>0,'msg'=>'此手机号码未注册或未认证,请重新输入'));
		//验证码处理
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
				json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
			}
		}
		$newpass = cdstr(6);
		//sms
		$rtn = Zeai_sendsms_authcode($mob,$newpass,'findpass');
		if ($rtn == 0){
			$chkflag = 1;
			$newpass2 = md5($newpass);
			$db->query("UPDATE ".__TBL_TG_USER__." SET pwd='$newpass2' WHERE mob=".$mob);
			$msg='新密码已发送至您的手机'.$mob;
		}else{
			$chkflag = 0;
			$msg = "发送失败,错误码：$rtn";
		}
		json_exit(array('flag'=>$chkflag,'msg'=>$msg));	
	break;
}

/*if($loginkind=='shop'){
	$h1title = $_SHOP['title'];
}else{
	$h1title = $TG_set['tgytitle'];
}*/
$h1title='用户';
$headertitle = $h1title.'登录-';
$nav = '';require_once ZEAI.'m1/header.php';
$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');
?>
<link href="css/tg_loginreg.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php 
$mini_title .= '<i class="ico goback" onClick="history.back(-1)">&#xe602;</i>';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
?>
<div class="tglogin huadong fadeInL" id="main">
    <div class="box">
    	<h1><?php echo $h1title.'登录';?></h1>
        <div class="linebox" ><div class="line W50"></div><div class="title BAI C666">输入 <?php echo $TG_set['tgytitle'].'/'.$_SHOP['title'];?>/买家 帐号登录</div></div>
        <div class="logo"></div>
        <form id="WWW_ZEAI_CN_form" method="post">
            <dl><dt><i class="ico">&#xe645;</i></dt><dd><input name="uname" type="text" class="input_login" id="uname" placeholder="请输入ID/用户名/手机" autocomplete="off" maxlength="20" value="<?php echo $cook_admid;?>"></dd></dl>
            <dl><dt><i class="ico">&#xe61e;</i></dt><dd><input name="pwd" type="password" class="input_login" id="pwd" placeholder="请输入登录密码" autocomplete="off" maxlength="20"></dd></dl>
            <br><input type="button" value="立即登录" class="btn size4 HONG2 B" id="loginbtn">
            <input type="hidden" name="submitok" value="ajax_login_uname_chk">
            <input type="hidden" name="loginkind" value="<?php echo $loginkind;?>">
            <input type="hidden" name="jumpurl" value="<?php echo dataIO($jumpurl,'out');?>">
        </form>
        <div class="clear"></div>
        <div class="areg">
        <a href="reg.php?subscribe=<?php echo $subscribe;?>&tguid=<?php echo $tguid;?>&loginkind=<?php echo $loginkind;?>&jumpurl=<?php echo $jumpurl;?>">注册新帐号</a>
        <a onclick="page({g:'tg_login.php?submitok=tg_login_forgetpass',l:'tg_login_forgetpass'})">忘记密码？</a>
        </div>
    </div>
    <div class="kefu">
    <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按二维码加客服微信</font><br><?php }?>
    <?php if (!empty($kf_tel)){?>
        <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
    <?php }else{?>
        <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
    <?php }?>
    </div>
</div>
<script>
if(!zeai.empty(o('loginbtn')))o('loginbtn').onclick = function(){
	var uname = o('uname').value,pwd = o('pwd').value;
	if(zeai.str_len(uname) < 1 || zeai.str_len(uname)>20){zeai.msg('请输入ID/用户名/手机',o('uname'));return false;}
	if(zeai.str_len(pwd)<6 || zeai.str_len(pwd)>20){zeai.msg('请输入登录密码',o('pwd'));return false;}
	zeai.ajax({url:'tg_login'+zeai.extname,form:WWW_ZEAI_CN_form},function(e){rs=zeai.jsoneval(e);
		(rs.flag == 1 && zeai.openurl(rs.jumpurl)) || (rs.flag==0 && zeai.msg(rs.msg));
	});
	return false;
}
</script>
<div id="Zeai_cn__PageBox"></div><div id="blankpage"></div>
</body></html>
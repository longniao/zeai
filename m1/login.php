<?php
if (ini_get('session.auto_start') == 0)session_start();
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';

if($submitok=='admlogin'){
	if (!ifint($uid) || !ifint($uu) || str_len($pp) != 32)callmsg("Forbidden1!","-1");
	$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id=".$uu." AND password='$pp'");
	if (!$db->num_rows($rt))exit;
	$password = trim($pwd);
	if (str_len($password) != 32){
		$password = 'www.zeai.cn_eb72c92a54_d5a330112';
		$db->query("UPDATE ".__TBL_USER__." SET pwd='$password' WHERE id=".$uid);
	}
	$rt = $db->query("SELECT id,pwd,sex,photo_s,nickname,grade,birthday FROM ".__TBL_USER__." WHERE id=".$uid." AND pwd='$password'  AND (flag=1 || flag=-2)");
	if(!$db->num_rows($rt)){
		callmsg("● 用户名/密码错误/或不存在！","-1");
	} else {
		$row = $db->fetch_array($rt,'num');
		setcookie("cook_uid",$row[0],null,"/",$_ZEAI['CookDomain']);
		setcookie("cook_pwd",$row[1],null,"/",$_ZEAI['CookDomain']);
		setcookie("cook_sex",$row[2],null,"/",$_ZEAI['CookDomain']);
		setcookie("cook_photo_s",$row[3],null,"/",$_ZEAI['CookDomain']);
		setcookie("cook_nickname",dataIO($row[4],'out'),null,"/",$_ZEAI['CookDomain']);
		setcookie("cook_grade",$row[5],null,"/",$_ZEAI['CookDomain']);
		setcookie("cook_birthday",$row[6],null,"/",$_ZEAI['CookDomain']);
		$jumpurl = (empty($jumpurl))?'':$jumpurl;
		$uid=$row[0];
		AddLog2('进入用户【'.dataIO($row[4],'out').'（uid:'.$uid.'）】个人中心');
		header("Location: ".HOST."/?z=my");
		exit;
	}
}
if (!is_mobile())exit('请用手机浏览器打开');
//if (ifint($cook_uid)){header("Location: ../?z=my");exit;}
//微信自动进入
if(is_weixin()){
/*	if(isset($cook_openid) && !empty($cook_openid) ){
		$server_openid = $cook_openid;
	}else{
		$server_openid = wx_get_openid(0);
		setcookie("cook_openid",$server_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
	}
	$row = $db->ROW(__TBL_USER__,"id,uname,nickname,pwd,sex,photo_s,grade,birthday,flag","openid<>'' AND openid='".$server_openid."'","name");
	if ($row){
		if ($row['flag']==-1)alert('您的帐号已被锁定','back');
		setcookie("cook_uid",$row['id'],time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_pwd",$row['pwd'],time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_sex",$row['sex'],time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_photo_s",$row['photo_s'],time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_uname",dataIO($row['uname'],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_nickname",dataIO($row['nickname'],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_grade",$row['grade'],time()+720000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_birthday",$row['birthday'],time()+720000,"/",$_ZEAI['CookDomain']);
		header("Location: ../?z=my");
	}
*/}
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_sms.php';
$loginip=getip();
$rt = $db->query("SELECT id FROM ".__TBL_IP__." WHERE ipurl='$loginip'");
if($db->num_rows($rt))exit(JSON_ERROR);
$curpage = 'login';
/*************AJAX页面开始*************/
switch ($submitok) {
	case 'ajax_dl_uname':
		$btnt = (ifint($tmpid))?'绑定':'登录';
		//$reghref = (@in_array('tg',$navarr))?"reg.php?":"reg_diy.php?";
		$reghref = 'reg.php?';
		if (is_h5app()){?><a href="javascript:history.back(-1)" class="ico goback">&#xe602;</a><br /><?php }?>
        <div class="box">
        	<h1>欢迎登录<a href="<?php echo $reghref."subscribe=$subscribe&tmpid=$tmpid&tguid=$tguid&ifback=1";?>">新用户注册</a></h1>
            <form id="ZEAI_form" method="post">
                <dl><dt><i class="ico">&#xe645;</i></dt><dd><input name="uname" type="text" class="input_login" id="uname" placeholder="请输入用户名/UID/手机/邮箱" autocomplete="off" maxlength="20" oninput="btnfun('pwd')" ><span class="ico" onClick="uname.value='';this.hide();btnfun('pwd');" id="reset">&#xe65b;</span></dd></dl>
                <dl><dt><i class="ico">&#xe61e;</i></dt><dd><input name="pwd" type="password" class="input_login" id="pwd" placeholder="请输入登录密码" autocomplete="off" maxlength="20" oninput="btnfun('pwd')"><span class="ico" onClick="showpass(this);">&#xe606;</span></dd></dl>
                <div class="regfindpass"><a href="javascript:;" onclick="page({g:'reg.php?submitok=login_forgetpass',l:'login_forgetpass'})" class="hand">忘记密码？</a></div>
              	<input type="button" value="<?php echo $btnt;?>" class="hui btn size4 HONG W85_ B" id="submitbtn" onclick="chkform();" <?php echo ($_ZEAI['mob_mbkind'] == 3)?' style="background-color:#FF6F6F"':'';?>>
                <input type="hidden" id="jumpurl" name="jumpurl" value="<?php echo $jumpurl;?>">
                <input type="hidden" id="tmpid" name="tmpid" value="<?php echo $tmpid;?>">
                <input type="hidden" id="subscribe" name="subscribe" value="<?php echo $subscribe;?>">
                <input type="hidden" name="submitok" value="ajax_login_uname_chk">
            </form>
            <div class="clear"></div>
            <div class="login3">
                <div class="linebox"><div class="line"></div><div class="title S12 C999 BAI">其它登录注册方式</div></div>
                <?php if ($_REG['reg_kind'] != 2){?><em class="mob" onClick="login3('yzm');"><i class="ico">&#xe627;</i><font>短信登录</font></em><?php }?>
                <?php if ($_REG['reg_3login_qq'] == 1 && !is_h5app()){?><em class="qq" onClick="login3('qq');"><i class="ico">&#xe612;</i><font>QQ</font></em><?php }?>
                <?php if ($_REG['reg_3login_wx'] == 1 && !is_h5app()){
					if(is_weixin() && is_mobile()){
					?><em class="weixin" onClick="login3('weixin');"><i class="ico">&#xe607;</i><font>微信</font></em>
				<?php }}?>
                <?php if(is_h5app()){?><em class="weixin" onClick="authLogin();"><i class="ico">&#xe607;</i><font>微信</font></em><?php }?>                
            </div>
        </div>
	<?php exit;break;case 'ajax_dl_yzm':?>
        <div class="box">
        	<h1>手机短信登录</h1>
            <form>
            <dl><dt><i class="ico">&#xe627;</i></dt><dd>
            <input name="mob" type="text" class="input_login" id="mob" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*" oninput="btnfun('mob')">
            <input type="hidden" id="jumpurl" value="<?php echo $jumpurl;?>">
            <span class="ico" onClick="mob.value='';this.hide();btnfun('mob');" id="reset">&#xe65b;</span>
            </dd></dl>
            <div class="regfindpass"></div>
			<input type="button" value="获取验证码" class="btn size4 HONG W85_ B hui" id="submitbtn" onClick="ajax_dl_yzm()">
            </form>
            <div class="clear"></div>
            <div class="login3">
                <div class="linebox"><div class="line"></div><div class="title S12 BAI">其它登录方式</div></div>
                <em class="uname" onClick="login3('uname');"><i class="ico">&#xe645;</i><font>帐号登录</font></em>
                <?php if ($_REG['reg_3login_qq'] == 1 && !is_h5app()){?><em class="qq" onClick="login3('qq');"><i class="ico">&#xe612;</i><font>QQ</font></em><?php }?>
                <?php if ($_REG['reg_3login_wx'] == 1 && !is_h5app()){?><em class="weixin" onClick="login3('weixin');"><i class="ico">&#xe607;</i><font>微信</font></em><?php }?>
                <?php if(is_h5app()){?><em class="weixin" onClick="authLogin();"><i class="ico">&#xe607;</i><font>微信</font></em><?php }?>
            </div>
        </div>
	<?php exit;break;case 'ajax_dl_yzm_get':
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}else{
			if (!$db->ROW(__TBL_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)"))json_exit(array('flag'=>0,'msg'=>'登录失败，此手机暂未注册'));
		}	
		if ($Temp_regyzmrenum > $_SMS['sms_yzmnum'] )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
		$_SESSION['Zeai_cn__mobyzm'] = cdstr(4);
		//sms
		$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__mobyzm']);
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
	?>
	<?php exit;break;case 'ajax_dl_yzm_get_html':
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}
		$mobstr = substr($mob,0,3).'****'.substr($mob,-4,4);
	?>
    <div class="login_dl_yzm_get_html box">
        <h1>输入短信验证码</h1>
        <h3>验证码已发送至<?php echo $mobstr;?>，请在下方输入框内输入4位数字验证码</h3>
        <div class="yzmnumbox" id="yzmnumbox">
        	<div class="yzmerrstrbox"><span id="yzmerrstr">验证码错误</span></div>
            <input type="tel" maxlength='4' id="verify" pattern="[0-9]*" autocomplete="off"><ul id="yzmul"><li></li><li></li><li></li><li></li></ul>
        </div>
        <button type="button" id="yzmbtn">120s后重新发送</button>
        <input id="mob_" type="hidden" value="<?php echo $mob;?>" >
        <input id="jumpurl_" type="hidden" value="<?php echo $jumpurl;?>" >
        <div class="clear"></div>
        <div class="login3">
            <div class="linebox"><div class="line"></div><div class="title S12 C999 BAI">其它登录方式</div></div>
            <em class="uname" onClick="login3('uname');"><i class="ico">&#xe645;</i><font>帐号密码</font></em>
            <?php if ($_REG['reg_3login_qq'] == 1 && !is_h5app()){?><em class="qq" onClick="login3('qq');"><i class="ico">&#xe612;</i><font>QQ</font></em><?php }?>
            <?php if ($_REG['reg_3login_wx'] == 1 && !is_h5app()){?><em class="weixin" onClick="login3('weixin');"><i class="ico">&#xe607;</i><font>微信</font></em><?php }?>
            <?php if(is_h5app()){?><em class="weixin" onClick="authLogin();"><i class="ico">&#xe607;</i><font>微信</font></em><?php }?>   
        </div>
    </div>
<script>
    if (!zeai.empty(o('yzmbtn'))){
		yzmbtn.onclick = function(){
			if (zeai.ifmob(mob_.value)){
				if (!this.hasClass('disabled')){
					yzmbtn.addClass('disabled');
					zeai.ajax({'url':'login'+zeai.extname,'data':{'submitok':'ajax_dl_yzm_get','mob':mob_.value}},function(e){
						var rs=zeai.jsoneval(e);
						if (rs.flag == 1){
							yzmtimeFn(120);
						}else{
							yzmbtn.removeClass('disabled');
						}
					});
				}
			}else{
				zeai.msg('请输入手机号码',{mask:0});
				return false;
			}
		}
		//yzmbtn.click();
	}
	function yzmtimeFn(countdown) { 
		if (countdown == 0) {
			yzmbtn.removeClass('disabled');
			yzmbtn.html('<font>重新获取验证码</font>'); 
			return false;
		} else { 
			if (!zeai.empty(o('yzmbtn'))){
				yzmbtn.addClass('disabled');
				yzmbtn.html(countdown + "s后重新发送"); 
				countdown--; 
			}
		} 
		cleandsj=setTimeout(function(){yzmtimeFn(countdown);},1000);
	}
	function login_dl_yzm_get_html(){
		zeai.ajax({url:'login'+zeai.extname,data:{'submitok':'login_dl_yzm_get_html_update','verify':verify.value,'mob':mob_.value,'jumpurl':jumpurl_.value}},
			function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){
					zeai.openurl(rs.jumpurl);
				}else{
					yzmerrstr.html(rs.msg);
					yzmerrstr.show();yzmerrstr.addClass('shakeLR');setTimeout(function(){yzmerrstr.removeClass('shakeLR');},200);
					yzmnumbox.addClass('shakeLR_loop');setTimeout(function(){yzmnumbox.removeClass('shakeLR_loop');},200);
					verify.value='';verify.focus();setTimeout(function(){yzmerrstr.hide();},2000);
				}
			}
		);
	}
	o('yzmnumbox').onclick=function(){verify.focus();}
	o('yzmul').onclick=function(){verify.focus();}
    </script>
	<?php
	exit;break;
	case 'login_dl_yzm_get_html_update':
		$verify = intval($verify);
		if (empty($_SESSION['Zeai_cn__mobyzm'])){
			json_exit(array('flag'=>0,'msg'=>'短信验证码错误，请重新获取'));
		}else{
			if ($_SESSION['Zeai_cn__mobyzm'] != $verify){
				json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
			}
			if ($_SESSION['Zeai_cn__mob'] != $mob && ifmob($mob)){
				unset($_SESSION["Zeai_cn__mob"]);
				json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
			}
		}
		//	
		$rt = $db->query("SELECT id,uname,nickname,pwd,sex,photo_s,grade,birthday FROM ".__TBL_USER__." WHERE mob='$mob' AND FIND_IN_SET('mob',RZ) AND (flag=1 || flag=-2) AND kind<>2");
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			setcookie("cook_uid",$row[0],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_pwd",$row[3],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_sex",$row[4],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_photo_s",$row[5],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_uname",dataIO($row[1],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_nickname",dataIO($row[2],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_grade",$row[6],time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_birthday",$row[7],time()+720000,"/",$_ZEAI['CookDomain']);
			$uid = $row[0];
			$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",endip='$loginip',logincount=logincount+1 WHERE id=".$uid);
			$chkflag = 1;
			$jumpurl = urldecode($jumpurl);
			$jumpurl = (empty($jumpurl) || $jumpurl=='undefined' || strpos($jumpurl,'login.php') !== false  )?HOST.'/?z=my':$jumpurl;
			unset($_SESSION["Zeai_cn__mobyzm"]);
			unset($_SESSION["Zeai_cn__mob"]);
		}else{
			$chkflag = 0;
			$content = "手机号码未验证注册～";
			$obj     ='pwd';
		}
		json_exit(array('flag'=>$chkflag,'msg'=>$content,'jumpurl'=>$jumpurl));
	exit;break;case 'ajax_login_uname_chk':
		$chkflag = 1;
		$uname = dataIO($uname,'in');$pwd = dataIO($pwd,'in');$jumpurl = dataIO($jumpurl,'in');
		if (str_len($uname) > 20 || str_len($uname) < 1) {$content="请输入正确的登录帐号";$chkflag=0;}
		if (str_len($pwd) > 20 || str_len($pwd) < 6) {$content="密码长度必须在6~20字节";$chkflag=0;}
		if ($chkflag == 0)exit(json_encode(array('flag'=>$chkflag,'msg'=>$content)));
		if (ifint($uname,'0-9','1,8')){
			$tmpNAME = "id='$uname'";
		}elseif(ifmob($uname)){
			$tmpNAME = "mob='$uname' AND FIND_IN_SET('mob',RZ) ";
		}elseif(ifemail($uname)){
			$tmpNAME = "email='$uname' AND FIND_IN_SET('email',RZ) ";
		}else{
			$tmpNAME = "uname='$uname'";
		}
		$pwd = md5(trimm($pwd));
		$rt = $db->query("SELECT id,uname,nickname,pwd,sex,photo_s,grade,birthday,flag FROM ".__TBL_USER__." WHERE ".$tmpNAME." AND pwd='$pwd' AND  kind<>2");//(flag=1 || flag=2 || flag=-2) AND
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'num');
			$uid = $row[0];
			$flag = $row[8];
			if($flag==1 || $flag==-2 || $flag==2){
				//BD
				if (ifint($tmpid)){
					$row2 = $db->ROW(__TBL_TMP__,"c","id=".$tmpid);
					if ($row2){
						$c=json_decode($row2[0],true);
						if ($c['regkind']=='qq' || $c['regkind']=='weibo'){
							$SQL = "loginkey='".$c['openid']."'";
						}elseif($c['regkind']=='weixin'){
							$SQL = "openid='".$c['openid']."'";
						}elseif($c['regkind']=='app'){
							$SQL = "unionid='".$c['unionid']."'";
						}
						if ($c['regkind'] == 'qq' || $c['regkind']=='weixin' || $c['regkind']=='app'){
							$db->query("UPDATE ".__TBL_USER__." SET ".$SQL." WHERE id=".$uid);
							$db->query("DELETE FROM ".__TBL_TMP__." WHERE id=".$tmpid);
						}
					}
				}
				//
				setcookie("cook_uid",$uid,time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_pwd",$row[3],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_sex",$row[4],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_photo_s",$row[5],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_uname",dataIO($row[1],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_nickname",dataIO($row[2],'out'),time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_grade",$row[6],time()+720000,"/",$_ZEAI['CookDomain']);
				setcookie("cook_birthday",$row[7],time()+720000,"/",$_ZEAI['CookDomain']);
				$uid = $row[0];
				$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",endip='$loginip',logincount=logincount+1 WHERE id=".$uid);
				$chkflag = 1;
				$jumpurl = urldecode($jumpurl);
				$jumpurl = (empty($jumpurl) || strpos($jumpurl,'login.php') !== false  )?HOST.'/?z=my':$jumpurl;
			}elseif($flag==0){
				$chkflag = 0;
				$content = "请等待管理员审核";
				$obj     ='pwd';
			}else{
				$chkflag = 0;
				$content = "帐号密码验证不正确";
				$obj     ='pwd';
			}
		}else{
			$chkflag = 0;
			$content = "帐号密码验证不正确";
			$obj     ='pwd';
		}
		json_exit(array('flag'=>$chkflag,'msg'=>$content,'jumpurl'=>$jumpurl));
	break;
}
/*************AJAX结束*************/
/********************************************************主体开始********************************************************/
$headertitle = '用户登录-';$nav = 'my';require_once ZEAI.'m1/header.php';
$a = (empty($a))?'dl':$a;
?>
<script src="js/login_reg.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="../res/zeai_ios_select/separate/select.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="../cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="../cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="css/login_reg.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<main class="login huadong" id="main"></main>
<div id="Zeai_cn__PageBox"></div>
<?php
if(strstr($jumpurl,'login.php'))$jumpurl='';
?>
<script>var jumpurl='<?php echo urlencode($jumpurl);?>',REG={},subscribe='<?php echo $subscribe;?>',tmpid='<?php echo $tmpid;?>',tguid='<?php echo $tguid;?>',t='<?php echo $t;?>',reg_style=<?php echo $_REG['reg_style']?>;
localStorage.tguid='<?php echo $tguid;?>';
</script>
</body></html>
<?php 
function AddLog2($c) {
	global $db,$_SESSION,$uid,$tguid,$tg_uid;
	$session_uname   = $_SESSION["admuname"];
	$session_kind    = $_SESSION["kind"];//adm,crm
	$session_agentid = intval($_SESSION["agentid"]);
	$session_agenttitle  = $_SESSION["agenttitle"];
	$kind=($session_kind=='crm')?2:1;$c=dataIO($c,'in',2000);$uid=intval($uid);$tguid=(ifint($tg_uid))?$tg_uid:intval($tguid);
	$db->query("INSERT INTO ".__TBL_LOG__."  (username,kind,content,addtime,agentid,agenttitle,uid,tguid) VALUES ('$session_uname',$kind,'$c',".ADDTIME.",$session_agentid,'$session_agenttitle',$uid,$tguid)");
}
?>
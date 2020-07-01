<?php
//$nav='index';
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if(strstr($jumpurl,'login.php'))$jumpurl='';
if(is_mobile())header("Location: ".HOST."/m1/login.php");
if (ifint($cook_uid)){header("Location: ".HOST."/p1/my.php");exit;}
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
require_once ZEAI.'sub/zeai_up_func.php';
$loginip=getip();
switch ($submitok){
	case 'ajax_login_uname_chk':
		$chkflag = 1;
		$uname = dataIO($uname,'in');$pwd = dataIO($pwd,'in');$jumpurl = dataIO($jumpurl,'in');
		if (str_len($uname) > 50 || str_len($uname) < 1) {$content="请输入正确格式的登录帐号";$chkflag=0;$obj='uname';}
		if (str_len($pwd) > 20 || str_len($pwd) < 6) {$content="密码长度必须在6~20字节".$pwd;$chkflag=0;$obj='pwd';}
		if ($chkflag == 0)json_exit(array('flag'=>$chkflag,'msg'=>$content,'obj'=>'pwd'));
		if (ifint($uname,'0-9','1,8')){
			$tmpNAME = "id='$uname'";
		}elseif(ifmob($uname)){
			$tmpNAME = "mob='$uname' AND FIND_IN_SET('mob',RZ) ";
		}elseif(ifemail($uname)){
			$tmpNAME = "email='$uname' AND FIND_IN_SET('email',RZ) ";
		}else{
			$tmpNAME = "uname='$uname'";
		}
		$pwd_login = $pwd;
		$pwd       = md5(trimm($pwd));
		$rt = $db->query("SELECT id,uname,nickname,pwd,sex,photo_s,grade,birthday FROM ".__TBL_USER__." WHERE ".$tmpNAME." AND pwd='$pwd' AND (flag=1 || flag=-2) AND kind<>2");
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'num');
			$uid = $row[0];
			//BD
			if (ifint($tmpid)){
				$row2 = $db->ROW(__TBL_TMP__,"c","id=".$tmpid);
				if ($row2){
					$c=json_decode($row2[0],true);
					if ($c['regkind']=='qq' || $c['regkind']=='weibo'){
						$SQL = "loginkey='".$c['openid']."'";
					}elseif($c['regkind']=='weixin'){
						$SQL = "unionid='".$c['unionid']."'";
					}
					if ($c['regkind'] == 'qq' || $c['regkind']=='weixin'){
						$db->query("UPDATE ".__TBL_USER__." SET ".$SQL." WHERE id=".$uid);
						$db->query("DELETE FROM ".__TBL_TMP__." WHERE id=".$tmpid);
					}
				}
			}
			//设计cookie
			setcookie("cook_uid",$row[0],null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_pwd",$row[3],null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_sex",$row[4],null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_photo_s",$row[5],null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_uname",dataIO($row[1],'out'),null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_nickname",dataIO($row[2],'out'),null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_grade",$row[6],null,"/",$_ZEAI['CookDomain']);
			setcookie("cook_birthday",$row[7],null,"/",$_ZEAI['CookDomain']);

            $uid = $row[0];
			$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",endip='$loginip',logincount=logincount+1 WHERE id=".$uid);
			$chkflag = 1;
			$jumpurl = urldecode($jumpurl);
			$jumpurl = (empty($jumpurl) || strpos($jumpurl,'login.php') !== false  )?HOST.'/p1/my.php':$jumpurl;
		}else{
			$chkflag = 0;
			$content = "帐号密码不正确或未验证或已锁定";
			$obj     ='pwd';
		}
		json_exit(array('flag'=>$chkflag,'msg'=>$content,'jumpurl'=>$jumpurl,'obj'=>$obj));
	break;
	case 'ajax_chklogin':
		if (!ifint($cook_uid,'0-9','1,8'))exit(JSON_ERROR);
		if($db->NUM($cook_uid)){
			$retarr = array('flag'=>1);
		}else{
			$retarr = array('flag'=>0);
		}
		json_exit($retarr);
	break;
	case 'admlogin':
		if (!ifint($uid) || !ifint($uu) || str_len($pp) != 32)alert("ZEAI_forbidden","-1");
		$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id=".$uu." AND password='$pp'");
		if (!$db->num_rows($rt))exit;
		$password = trim($pwd);
		if (str_len($password) != 32){
			$password = 'www.zeai.cn_eb72c92a54_d5a330112';
			$db->query("UPDATE ".__TBL_USER__." SET pwd='$password' WHERE id=".$uid);
		}
		$rt = $db->query("SELECT id,pwd,sex,photo_s,nickname,grade,birthday FROM ".__TBL_USER__." WHERE id=".$uid." AND pwd='$password' AND (flag=1 || flag=-2)");
		if(!$db->num_rows($rt)){
			alert("● 用户名/密码错误/或不存在！","-1");
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
			header("Location: ".HOST."/p1/my");
			exit;
		}
	break;
	case 'ajax_findpass':
		$chkflag = 1;$uname = dataIO($uname,'in');
		if (str_len($uname) > 30 || str_len($uname) < 1){$chkflag=0;$msg="请输入正确格式用户名/QQ/手机号";}else{
			if (ifint($uname,'0-9','1,8')){
				$tmpNAME = "id='$uname'";
			}elseif(ifmob($uname)){
				$tmpNAME = "mob='$uname' AND FIND_IN_SET('mob',RZ) ";
			}elseif(ifemail($uname)){
				$tmpNAME = "email='$uname' AND FIND_IN_SET('email',RZ) ";
			}else{
				$tmpNAME = "uname='$uname'";
			}
			$rt = $db->query("SELECT id,mob FROM ".__TBL_USER__." WHERE kind<>2 AND ".$tmpNAME);
			if(!$db->num_rows($rt)){
				$chkflag=0;$msg="用户名/手机号不存在";
			} else {
				$row = $db->fetch_array($rt,'num');
				$uid = $row[0];$mob = $row[1];
				if (!ifmob($mob)){
					$chkflag=0;$msg="您的帐号没有填写手机号码,无法找回";
				}else{
					if ($Temp_regyzmrenum > $_SMS['sms_yzmnum'] ){exit(json_encode(array('flag'=>0,'msg'=>'请求次数过多，请明天再试')));}
					$newpass = cdnumletters(6);
					//sms
					$rtn = Zeai_sendsms_authcode($mob,$newpass,'findpass');
					if ($rtn == 0){
						setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,null,"/",$_ZEAI['CookDomain']);  
						$chkflag = 1;$msg = '新密码已发送至手机'.$mob.'，请查收';
						$newpass = md5($newpass);
						$db->query("UPDATE ".__TBL_USER__." SET pwd='$newpass' WHERE id=".$uid);
					}else{
						$chkflag = 0;$content = "发送失败,请联系管理员,错误码：$rtn";
					}
					//sms end
				}
			}
		}
		$retarr = array('flag'=>$chkflag,'msg'=>$msg,'obj'=>'uname');
		exit(json_exit($retarr));
	break;
}
if(ifint($tmpid)){
	$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid);
	if ($row){
		$c=json_decode($row[0],true);
		
		$c = json_decode($row[0],true);
		$tmp_regkind  = $c['regkind'];
		$tmp_nickname= dataIO($c['nickname'],'out');
		$tmp_sex     = ($c['sex']==2)?2:1;
		
		if($tmp_regkind=='qq'){
			$tmp_photo_s = $c['photo_s'];
			$tmp_openid  = $c['openid'];
		}elseif($tmp_regkind=='weixin'){
			$unionid  = $c['unionid'];
			$tmp_photo_s = $c['headimgurl'];
			$tmp_openid  = $unionid;
		}
		$photo_s_url = (!empty($tmp_photo_s) )?$tmp_photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$sexbg       = (empty($tmp_photo_s))?' class="sexbg'.$sex.'"':'';
	}
	$dtT  = '绑定';
	$btnt = '绑定';
}else{
	$dtT  = '登录';
	$btnt = '登录';
}
$nav='index';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>会员登录 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/login.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="regmain">
    <div class="boxx S5">
    	<div class="tbody">
        <?php
		if (!empty($tmp_openid)){
        	echo '<img src="'.$photo_s_url.'"'.$sexbg.'><span>'.$tmp_nickname.'，欢迎回来，请输入已有帐号密码绑定</span>';
        }else{ ?>
            欢迎登录
        <?php }?>
        </div>
        <form id="WWW_ZEAI_CN_form">
        <div class="L">
            <dl><dt><?php echo $dtT;?>帐号</dt><dd><input name="uname" id="uname" type="text" required class="input" maxlength="15" placeholder="输入用户名/UID/手机号码" autocomplete="off" /></dd></dl>
            <dl><dt>登录密码</dt><dd><input name="pwd" id="pwd" type="password" required class="input" maxlength="20" placeholder="请输入登录密码" autocomplete="off" /><a onclick="Zeai_cn__getpass();" class="forgetpass hand">忘记密码？</a></dd></dl>
            <dl><dt>&nbsp;</dt><dd><input type="button" class="regbtn" value="立即<?php echo $btnt;?>" id="loginbtn" /></dd></dl>
            <div class="clear"></div>
            <div class="newuser">
            <div class="linebox "><div class="line W50"></div><div class="title BAI S14 C999">附近最新加入会员</div></div>
            <?php
			$rt=$db->query("SELECT id,nickname,sex,photo_s,photo_f,areatitle,birthday,love FROM ".__TBL_USER__." WHERE flag=1 AND photo_f=1 ORDER BY id DESC LIMIT 3");
			$total = $db->num_rows($rt);
			if ($total > 0) {
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows) break;
					$uid      = $rows['id'];
					$nickname = dataIO($rows['nickname'],'out');
					$sex      = $rows['sex'];
					$areatitle= $rows['areatitle'];
					$birthday = $rows['birthday'];
					$love      = $rows['love'];
					$photo_s  = $rows['photo_s'];
					$photo_f  = $rows['photo_f'];
					$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
					$love_str     = (empty($love))?'':' '.udata('love',$love).' ';
					$aARR = explode(' ',$areatitle);$areatitle = $aARR[1];
					$areatitle_str = (empty($areatitle))?'':$areatitle;
					$areatitle_str  = str_replace("不限","",$areatitle_str);
					$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
					echo '<li><img src="'.$photo_s_url.'"'.$sexbg.'><span>'.$birthday_str.$areatitle_str.$love_str.'</span></li>';
				}
			}
			?>
            </div>
        </div>
        <input type="hidden" id="jumpurl" name="jumpurl" value="<?php echo $jumpurl;?>">
        <input type="hidden" id="tmpid" name="tmpid" value="<?php echo $tmpid;?>">
        <input type="hidden" name="submitok" value="ajax_login_uname_chk">
        </form>
        <div class="CC"><i></i><i></i><div class="clear"></div></div>
        <div class="R">
            <h1 class="C666 S18">没有帐号？ 请这边注册</h1>
			<a href="<?php echo HOST;?>/p1/reg.php" class="reg">免费注册</a>
            <?php if ($_REG['reg_3login_qq'] == 1 || $_REG['reg_3login_wx'] == 1){?>
            <h1 class="C666 S18 Mtop50"><?php if ($_REG['reg_3login_wx'] == 1){?>使用微信扫码<?php }?><?php if ($_REG['reg_3login_qq'] == 1){?>或QQ帐号登录<?php }?></h1>
            <div class="em">
            <?php if ($_REG['reg_3login_wx'] == 1){?><a onClick="login3('weixin')" class="weixin"><i class='ico'>&#xe607;</i></a><?php }?>
            <?php if ($_REG['reg_3login_qq'] == 1){?><a onClick="login3('qq')" class="qq"><i class='ico'>&#xe612;</i></a><?php }?>
            </div>
            <?php }?>
            
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<script src="js/login_reg.js"></script>
<script>
var jumpurl='<?php echo urlencode($jumpurl);?>',tmpid='<?php echo $tmpid;?>';sessionStorage.tguid='<?php echo $tguid;?>',sessionStorage.tmpid='<?php echo $tmpid;?>';
if(sessionStorage.pagekind=='reg')zeai.openurl('reg.php?tmpid='+tmpid);
</script>
<?php require_once ZEAI.'p1/bottom.php';?>
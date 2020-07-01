<?php 
@session_start();
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once ZEAI.'sub/conn.php';
$endip = getip();
$rt = $db->query("SELECT ipurl FROM ".__TBL_IP__." WHERE ipurl='$endip'");
if($db->num_rows($rt))json_exit(array('flag'=>0,'msg'=>'本站拒绝你的访问'));
if ($submitok == 'ajax_submit') {
	$chkflag = 1;
	if (str_len($uname) >20 || str_len($uname) <2 || str_len($pwd) > 20 || str_len($pwd) < 6 ) {
		$content ="用户名密码错误，登录失败";
		$chkflag = 0;
	}
	if (!ifint($v,"0-9","4")){
		$content ="验证码错误，请重试";
		$chkflag = 0;
	}
	if (trim($v) !== $_SESSION['ZEAI_CN__YZM']) {	
		$content ="验证码校对错误，请重试";
		$chkflag = 0;
	}
	if ($adm_loginnum > 5 )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请联系管理员'));
	if ($chkflag == 1){
		$uname = trimm($uname);
		$pwd   = md5(trim($pwd));
		$rt = $db->query("SELECT id,roleid,kind,truename,agentid,agenttitle,path_s FROM ".__TBL_ADMIN__." WHERE  username='$uname' AND password='$pwd' AND flag=1");
		if ($db->num_rows($rt)) {
			$db->query("UPDATE ".__TBL_ADMIN__." SET endtime=".ADDTIME.",endip='$endip',logincount=logincount+1 WHERE username='$uname'");
			$row = $db->fetch_array($rt,'num');
			$_SESSION["admuid"]   = $row[0];
			$roleid               = $row[1];
			$_SESSION["kind"]     = $row[2];
			$_SESSION["truename"] = $row[3];
			$_SESSION["agentid"]  = intval($row[4]);
			$_SESSION["agenttitle"]= $row[5];
			$_SESSION["path_s"]    = $row[6];
			$_SESSION["admuname"] = $uname;
			$_SESSION["admpwd"]   = $pwd;
			if ( !ifint($roleid) )callmsg("角色载入错误","-1");
			$rtD=$db->query("SELECT authoritylist,crmkind,title,sq_sh_bfb FROM ".__TBL_ROLE__." WHERE id=".$roleid);
			if ($db->num_rows($rtD)){
				$rowD = $db->fetch_array($rtD,'num');
				$_SESSION["authoritylist"] = $rowD[0];
				$_SESSION["title"] = dataIO($rowD[2],'out');
				$_SESSION["sq_sh_bfb"] = $rowD[3];
			}else{
				json_exit(array('flag'=>0,'msg'=>'角色载入错误'));
			}
			if ($_SESSION["kind"] == 'crm'){
				$_SESSION["crmkind"]  = $rowD[1];
				if(ifint($_SESSION["agentid"])){
					$row = $db->ROW(__TBL_CRM_AGENT__,"flag,areaid","id=".$_SESSION["agentid"],'num');
					if ($row[0]!=1)json_exit(array('flag'=>0,'msg'=>'门店【'.$_SESSION["agenttitle"].'】已停止服务，请用超级管理员帐号进入开启'));	
					$_SESSION["agent_areaid"]=$row[1];
				}else{
					json_exit(array('flag'=>0,'msg'=>'门店载入错误'));
				}
			}
			unset($_SESSION['ZEAI_CN__YZM']);
			AddLog2('后台登录成功'.'【'.$_SESSION["admuname"].'（id:'.$_SESSION["admuid"].'）】IP：'.$endip);
			setcookie("adm_loginnum",0,time()+720000,"/",$_ZEAI['CookDomain']);
			json_exit(array('flag'=>1,'msg'=>'已登录','url'=>'./'));
		} else {
			AddLog2('后台登录失败'.'【'.$uname.'】IP：'.$endip);
			setcookie("adm_loginnum",$adm_loginnum+1,time()+720000,"/",$_ZEAI['CookDomain']);
			json_exit(array('flag'=>0,'msg'=>'您的用户名密码错误，超过5次将自动锁定不能再试，当前：'.($adm_loginnum+1).'次'));
		}
	}else{
		json_exit(array('flag'=>$chkflag,'msg'=>$content));
	}
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href="css/login.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<meta name="generator" content="Zeai.cn V6.0" />
</head>
<body>
<div id="mask_box" class="alpha0_100"></div><table border="0" align="center" cellpadding="5" cellspacing="0" class="tablee animattime_fast fadeInDown">
<form name="WWWzeaiCN" method="post" action="<?php echo SELF; ?>" onSubmit="return chkform()" autoComplete="off">
<tr><td height="110" align="center" class="tbg"><div class="title"><?php echo $_ZEAI['siteName']; ?> - 管理系统V<?php echo $_ZEAI['ver'];?></div></td></tr>
<tr><td height="25" align="left"></td></tr>
<tr>
<td height="55" align="center"><i class="ico">&#xe645;</i><input name="uname" id="uname" class="login W240" size="35" maxlength="20" autoComplete="off" placeholder="请输入登录用户名" /></td>
</tr>
<tr>
<td height="55" align="center"><i class="ico">&#xe61e;</i><input name="pwd" id="pwd"  type="password" class="login W240" size="35" maxlength="20" autoComplete="off" placeholder="请输入登录密码"  />
  </td>
</tr>
<tr>
<td height="55" align="center"><table border="0" cellspacing="0" cellpadding="0" style="width:340px">
  <tr>
    <td align="left"><i class="ico verify">&#xe6c3;</i><input name="verifycode"  id="verifycode" class="login" size="15" maxlength="4" autocomplete="off" placeholder="请输入右侧验证码"  /></td>
    <td width="70" align="right" style="padding-left:5px" title="看不清楚请点击刷新验证码"><img src="../sub/authcode.php" alt="看不清楚请点击刷新验证码" name="gylverify" align="middle" id="gylverify" style="cursor : pointer;" onclick="ReloadCode()" /></td>
    <td width="38" align="right" style="padding-left:5px;cursor:pointer;" title="看不清楚请点击刷新验证码" onclick="ReloadCode()"><img src="images/reload.png" width="28" height="32" onclick="ReloadCode()" /></td>
    </tr>
</table></td>
</tr>
<tr>
<td height="70" align="center" valign="bottom"><input type="submit" value="登 录" class="loginbtn" /></td>
</tr>
<tr>
<td align="center" valign="top" class="btmm">推荐使用【1920*1080分辨率】和【谷歌chrome浏览器】以达最佳效果</td>
</tr>
<input type="hidden" name="submitok" value="chkuser" />
</form>
</table>
</body>
</html>
<script src="js/login.js?<?php echo $_ZEAI['cache_str'];?>" ></script>
<?php
function AddLog2($c) {
	global $db,$_SESSION;
	$session_uname   = $_SESSION["admuname"];
	$session_kind    = $_SESSION["kind"];//adm,crm
	$session_agentid = intval($_SESSION["agentid"]);
	$session_agenttitle  = $_SESSION["agenttitle"];
	$kind=($session_kind=='crm')?2:1;$c=dataIO($c,'in',2000);
	$db->query("INSERT INTO ".__TBL_LOG__."  (username,kind,content,addtime,agentid,agenttitle) VALUES ('$session_uname',$kind,'$c',".ADDTIME.",$session_agentid,'$session_agenttitle')");
}
ob_end_flush();
?>
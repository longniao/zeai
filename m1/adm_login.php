<?php
//if (ini_get('session.auto_start') == 0)session_start();
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
if (ifint($cook_admid)){header("Location: adm_u_add.php");exit;}
if (!is_mobile())exit('请用手机浏览器打开');
/*************AJAX页面开始*************/
switch ($submitok) {
	case 'ajax_login_uname_chk':
		$uname=$adm_uname;
		$pwd  =$adm_pwd;
		$chkflag = 1;
		$uname = dataIO($uname,'in');$pwd = dataIO($pwd,'in');//$jumpurl = dataIO($jumpurl,'in');
		if (str_len($uname) > 20 || str_len($uname) < 1) {$content="请输入正确的登录帐号";$chkflag=0;}
		if (str_len($pwd) > 20 || str_len($pwd) < 6) {$content="密码长度必须在6~20字节";$chkflag=0;}
		if ($chkflag == 0)exit(json_encode(array('flag'=>$chkflag,'msg'=>$content)));
		if (ifint($uname,'0-9','1,8')){
			$tmpNAME = "id='$uname'";
		}else{
			$tmpNAME = "username='$uname'";
		}
		$pwd = md5(trimm($pwd));
		$rt = $db->query("SELECT id,truename,roleid FROM ".__TBL_ADMIN__." WHERE ".$tmpNAME." AND password='$pwd' AND flag=1 AND kind='adm'");
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'num');
			$admid = $row[0];$admtruename = $row[1];$roleid = $row[2];
			
			if ( !ifint($roleid) ){json_exit(array('flag'=>0,'msg'=>'角色载入错误'));}
			$rtD=$db->query("SELECT authoritylist FROM ".__TBL_ROLE__." WHERE id=".$roleid);
			if ($db->num_rows($rtD)){
				$rowD = $db->fetch_array($rtD,'num');
				$authoritylist = $rowD[0];
			}else{
				json_exit(array('flag'=>0,'msg'=>'角色载入错误'));
			}
			setcookie("cook_admauthoritylist",$authoritylist,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_admid",$admid,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_admuname",$uname,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_admtruename",$admtruename,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_admpwd",$pwd,time()+720000,"/",$_ZEAI['CookDomain']);
			$db->query("UPDATE ".__TBL_ADMIN__." SET endtime=".ADDTIME.",endip='$endip',logincount=logincount+1 WHERE username='$uname'");
			$chkflag = 1;
		}else{
			$chkflag = 0;
			$content = "帐号密码不正确";
		}
		json_exit(array('flag'=>$chkflag,'msg'=>$content,'jumpurl'=>'adm_u_add.php'));
	break;
}
/*************AJAX结束*************/
/********************************************************主体开始********************************************************/
$headertitle = '业务员登录-';$nav = 'my';require_once ZEAI.'m1/header.php';
?>
<script src="../res/zeai_ios_select/separate/select.js"></script>
<script src="../cache/areadata.js"></script>
<script src="../cache/udata.js"></script>
<link href="css/login_reg.css" rel="stylesheet" type="text/css" />
<main class="login" id="main">
    <div class="box">
        <h1><center>业务员登录</center></h1>
        <form id="WWW_ZEAI_CN_form" method="post">
            <dl><dt><i class="ico">&#xe645;</i></dt><dd><input name="adm_uname" type="text" class="input_login" id="adm_uname" placeholder="请输入用户名/ID编号" autocomplete="off" maxlength="20" value="<?php echo $cook_admid;?>"></dd></dl>
            <dl><dt><i class="ico">&#xe61e;</i></dt><dd><input name="adm_pwd" type="password" class="input_login" id="adm_pwd" placeholder="请输入登录密码" autocomplete="off" maxlength="20" value="<?php echo $cook_admpwd;?>"></dd></dl>
            <br><input type="button" value="登录" class="btn size4 HONG W85_ B" id="loginbtn">
            <input type="hidden" name="submitok" value="ajax_login_uname_chk">
        </form>
        <div class="clear"></div>
    </div>
</main>
<script>
if(!zeai.empty(o('loginbtn')))o('loginbtn').onclick = function(){
	var uname = o('adm_uname').value,pwd = o('adm_pwd').value;
	if(zeai.str_len(uname) < 1 || zeai.str_len(uname)>20){zeai.msg('请输入正确的登录帐号',o('uname'));return false;}
	if(zeai.str_len(pwd)<6 || zeai.str_len(pwd)>20){zeai.msg('请输入正确的密码',o('pwd'));return false;}
	zeai.ajax({url:'adm_login'+zeai.extname,form:WWW_ZEAI_CN_form},function(e){rs=zeai.jsoneval(e);
		(rs.flag == 1 && zeai.openurl(rs.jumpurl)) || (rs.flag==0 && zeai.msg(rs.msg));
	});
	return false;
}
</script>
</body></html>
<?php
//if (ini_get('session.auto_start') == 0)session_start();
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
if (!ifint($cook_admid)){header("Location: adm_login.php");exit;}
if (!is_mobile())exit('请用手机浏览器打开');
$QXARR = explode(',',$cook_admauthoritylist);
function m_noauth($t='权限不足') {
	global $_ZEAI;
	$ret  ="<!doctype html><html><head><meta http-equiv='refresh' content='3'><meta charset='utf-8'><title>".$t."</title>".HEADMETA."<link href='".$_ZEAI['adm2']."/css/main.css' rel='stylesheet' type='text/css' /></head><body>";
	$ret .= "<div class='nodataico'><i></i>".$t."</div>";
	$ret .= "</body></html>";
	return $ret;
}
if(!in_array('u_add',$QXARR) || !in_array('modpass',$QXARR) ){
	setcookie("cook_admauthoritylist","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_admid","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_admuname","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_admtruename","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_admpwd","",null,"/",$_ZEAI['CookDomain']);
	exit(m_noauth());	
}

switch ($submitok) {
	case 'passmodupdate':
		if (str_len($form_password1)<6 || str_len($form_password1)>20)json_exit(array('flag'=>0,'msg'=>'“新密码”请在20字节以内。'));
		if (str_len($form_password2)<6 || str_len($form_password2)>20)json_exit(array('flag'=>0,'msg'=>'“确认新密码”请在20字节以内。'));
		if ($form_password1 <> $form_password2)json_exit(array('flag'=>0,'msg'=>'两次密码输入不一样，请重试！'));
		$password = trimm($form_password1);
		$password = md5($password);
		$old_password = md5($old_password);
		$rt = $db->query("SELECT id FROM ".__TBL_ADMIN__." WHERE id=".$cook_admid." AND password='$old_password'");
		if(!$db->num_rows($rt))json_exit(array('flag'=>0,'msg'=>'旧密码验证错误，提交失败！'));
		$db->query("UPDATE ".__TBL_ADMIN__." SET password='$password' WHERE id=".$cook_admid);
		setcookie("cook_admpwd",$password,time()+720000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'修改成功！'));
	break;
}
$headertitle = '会员录入-';require_once ZEAI.'m1/header.php';
$mini_title = '<i class="ico goback" id="backmodpass" onClick="history.back(-1)">&#xe602;</i>修改密码';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
?>
<style>
body{position:absolute;top:0;background-color:#fff;-webkit-overflow-scrolling: touch}
::-webkit-input-placeholder{font-size:14px}
.admbottom{padding:20px 0;text-align:center;background-color:#f0f0f0}
.sexbox{float:right;margin-right:-20px}
</style>
        <div class="submain" id="my_set_modpass">
        <form id="ZEAI_form" class="lxbox">
			<style>
			.lxbox {background-color:#fff;padding-bottom:20px;margin-bottom:40px}
			.lxbox dl{width:90%;border-bottom:#eee 1px solid;padding:10px 5% 15px 5%}
			.lxbox dl dt,.lxbox dl dd{width:100%;line-height:30px;text-align:left}
			.lxbox dl dt{line-height:24px;}
			.lxbox .input{width:70%}
			.lxbox .input.W100_{width:100%}
			.btn2{width:90%;height:44px;line-height:44px;margin-top:15px;border-radius:2px}
			body{position:absolute}
			</style>
            <dl><dt>输入旧密码</dt><dd><input name="old_password" type="password" class="input W100_"   id="old_password" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()"></dd></dl>
            <dl><dt>输入新密码</dt><dd><input name="form_password1" type="password" class="input W100_" id="form_password1" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()" /></dd></dl>
            <dl><dt>确认新密码</dt><dd><input name="form_password2" type="password" class="input W100_" id="form_password2" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()"></dd></dl>
            <input name="submitok" type="hidden" value="passmodupdate" /><br>
            <input class="btn size4 HONG center W90_" type="button" value="保存并修改"  onclick="my_set_modpass()" />
        </form>
        <script>
			function my_set_modpass(){
				if(zeai.empty(o('old_password').value) || zeai.str_len(o('old_password').value)<6){
					zeai.msg('请输入旧密码6~20个字节内');
					return false;
				}
				if(zeai.empty(o('form_password1').value)){
					zeai.msg('请输入新密码6~20个字节内！');
					return false;
				}
				if(zeai.str_len(o('form_password1').value)>20 || zeai.str_len(o('form_password1').value)<6){
					zeai.msg('新密码请控制在6~20个字节内！');
					return false;
				}
				if(zeai.empty(o('form_password2').value)){
					zeai.msg('请再输入一次新密码');
					return false;
				}
				if(zeai.str_len(o('form_password2').value)>20 || zeai.str_len(o('form_password2').value)<6){
					zeai.msg('新密码请在6~20个字节内！');
					return false;
				}
				if(o('form_password1').value!=o('form_password2').value) {
					zeai.msg('两次密码不一致');
					return false;		
				}
				zeai.ajax({url:'adm_modpass.php',form:ZEAI_form},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if(rs.flag==1){
						setTimeout(function(){
							zeai.openurl('adm_u_add.php');
						},1000);
					}
				});
			}
			function my_set_modpasstop(){zeai.setScrollTop(0);}
        </script>
        </div>
</body></html>
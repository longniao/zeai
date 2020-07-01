function chkform(){
	var unameV = o("uname").value;
	var pwdV   = o("pwd").value;
	var verifycodeV = o("verifycode").value;
	if(zeai.str_len(unameV) < 2 || zeai.str_len(unameV)>20){
		zeai.msg('请输入用户名',uname);
		return false;
	}
	if(zeai.str_len(pwdV)<6 || zeai.str_len(pwdV)>20){
		zeai.msg('密码长度请控制在6～20字节之间',pwd);
		return false;
	}
	if(!zeai.ifint(verifycodeV,"0-9","4")){
		zeai.msg('请输入正确的验证码',verifycode);
		return false;
	}
	zeai.ajax({url:'./login'+zeai.extname,data:{submitok:'ajax_submit',uname:unameV,pwd:pwdV,v:verifycodeV}},function(e){rs=zeai.jsoneval(e);
		if (rs.flag == 1){zeai.openurl(rs.url);}else{
			zeai.msg(rs.msg,{time:5,focus:verifycode});
			return false;
		}		
	});
	return false;
}
function ReloadCode(){o('gylverify' ).src='../sub/authcode.php?dt='+new Date().getTime();}
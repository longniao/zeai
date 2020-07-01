/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2020/04/21 by supdes
*/
if(!zeai.empty(o('regbtn')))o('regbtn').onclick = function(){
	var pwdV = o('pwd').value;
	//uname
	if(regkind==2){
		var unameV = o('uname').value;
		if(zeai.str_len(unameV) < 3 || zeai.str_len(unameV)>20){zeai.msg('请输入3-15个字符【登录用户名】',{time:3,focus:o('uname')});return false;}
	//mob
	}else if(regkind==1){
		var mobV = o('mob').value,verifyV=o('verify').value;
		if(!zeai.ifmob(mobV)){zeai.msg('请输入正确手机号',o('mob'));return false;}
		if(!zeai.ifint(verifyV) || zeai.str_len(verifyV)!=4 ){zeai.msg('请输入【手机验证码】',o('verify'));return false;}
	}
	var kV = o('k').value;
	if(kV==2){
		var titleV=o('title').value;
		if(zeai.str_len(titleV) < 2 || zeai.str_len(titleV)>30){zeai.msg('请输入【'+kind_str+'名称】',{time:3,focus:o('title')});return false;}
	}else{
		var nicknameV=o('nickname').value;
		if(zeai.str_len(nicknameV) < 2 || zeai.str_len(nicknameV)>25){zeai.msg('请输入【网名/昵称】',{time:3,focus:o('nickname')});return false;}
	}
	if(zeai.str_len(pwdV)<6 || zeai.str_len(pwdV)>20){zeai.msg('请输入【正确的登录密码】(长度6~20)',{time:3,focus:o('pwd')});return false;}
	zeai.ajax({url:'tg_reg'+zeai.extname,form:WWW__ZEAI_CN_form},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);
		(rs.flag == 1 && zeai.openurl(rs.jumpurl)) || (rs.flag==0 && zeai.msg(rs.msg,{time:3}));
	});
	return false;
}

	
if (!zeai.empty(o('yzmbtn'))){
	yzmbtn.onclick = function(){
		if (zeai.ifmob(o('mob').value)){
			if (!this.hasClass('disabled')){
				yzmbtn.addClass('disabled');
				zeai.ajax({'url':'tg_reg'+zeai.extname,'data':{'submitok':'ajax_reg_verify',mob:o('mob').value}},function(e){
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

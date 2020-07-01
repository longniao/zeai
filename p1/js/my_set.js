var gyl;
zeai.listEach('.switch',function(obj){
	var objname = obj.name;
	obj.onclick = function(){
		var v=(obj.checked)?1:0
		zeai.ajax({url:PCHOST+'/my_set'+zeai.ajxext+'submitok=ajax_set&objname='+objname+'&v='+v},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
		});
	}
});
function my_set_modpass(){
	if(zeai.empty(o('old_password').value) || zeai.str_len(o('old_password').value)<6){
		zeai.msg('请输入旧密码6~20个字节内',o('old_password'));
		return false;
	}
	if(zeai.empty(o('form_password1').value)){
		zeai.msg('请输入新密码6~20个字节内！',o('form_password1'));
		return false;
	}
	if(zeai.str_len(o('form_password1').value)>20 || zeai.str_len(o('form_password1').value)<6){
		zeai.msg('新密码请控制在6~20个字节内！',o('form_password1'));
		return false;
	}
	if(zeai.empty(o('form_password2').value)){
		zeai.msg('请再输入一次新密码',o('form_password2'));
		return false;
	}
	if(zeai.str_len(o('form_password2').value)>20 || zeai.str_len(o('form_password2').value)<6){
		zeai.msg('新密码请在6~20个字节内！',o('form_password2'));
		return false;
	}
	if(o('form_password1').value!=o('form_password2').value) {
		zeai.msg('两次密码不一致',o('form_password2'));
		return false;		
	}
	zeai.ajax({url:PCHOST+'/my_set'+zeai.extname,form:wwwZeaicnV6Form},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
	});
}
function zeaiBindWeixin(uid,type){
	//Z_e_A___I_c____N.src=HOST+'/res/loadingData.gif';Z__e_A___I_c____N.style.width='20px';Z__e_A___I_c____N.style.height='20px';
	if(type=='bind'){
		setTimeout(function(){Z__e_A___I_c____N.src=HOST+'/sub/creat_ewm'+zeai.ajxext+'&url='+PCHOST+'/openid_get'+zeai.ajxext+'&submitok=getopenid&uid='+uid;},50);
		zeai.ajax({url:PCHOST+'/my_set'+zeai.ajxext+'submitok=ajax_binding_openid'},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==0){
				supdes=ZeaiPC.div({obj:o('weixin_login_ewm_box'),w:360,h:370});
				setInterval(chk_binding_openid,2000);
			}else{setTimeout(function(){location.reload(true);},1000);}
		});
	}else if(type=='cancel'){
		zeai.confirm('确定要解除微信绑定么？\n如果您用微信登录帐号密码，系统将自动再次绑定',function(){
			zeai.ajax({url:PCHOST+'/my_set'+zeai.ajxext+'submitok=ajax_bind_cancel_wx'},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){zeai.msg(rs.msg);setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}else if(type=='gzh_bind'){
		zeai.ajax({url:PCHOST+'/my_set'+zeai.ajxext+'submitok=ajax_binding_gzh'},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==0){
				clearInterval(gyl);
				zeai.ajax({url:PCHOST+'/my_money'+zeai.ajxext+'submitok=ajax_get_ewm'},function(e){rs=zeai.jsoneval(e);
					if (rs.flag==1){
						supdes=ZeaiPC.div({obj:o('subscribe_box_my_set'),w:360,h:370});
						Z_e___A___I__c___N.src=rs.ewm;
						gyl= setInterval(chk_binding_gzh,3000);
					}
				});
			}
		});
	}else if(type=='gzh_cancel'){
		zeai.ajax({url:PCHOST+'/my_set'+zeai.ajxext+'submitok=ajax_binding_gzh_cancel'},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg,{time:3});
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1500);}
		});
	}
}
function chk_binding_openid(){
	zeai.ajax({url:PCHOST+'/my_set'+zeai.ajxext+'submitok=ajax_binding_openid'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){zeai.msg(0);zeai.msg(rs.msg);setTimeout(function(){location.reload(true);},1500);}
	});
}
function chk_binding_gzh(){
	zeai.ajax({url:PCHOST+'/my_set'+zeai.ajxext+'submitok=ajax_binding_gzh'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){zeai.msg(0);zeai.msg(rs.msg);setTimeout(function(){location.reload(true);},1500);}
	});
}
function zeaiBindQQ(type){
	if(type=='bind'){
		zeai.openurl(HOST+'/api/qq/login/CS'+zeai.ajxext+'submitok=qq_bind');
	}else if(type=='cancel'){
		zeai.ajax({url:PCHOST+'/my_set'+zeai.ajxext+'submitok=ajax_qq_bind_cancel'},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	}
}
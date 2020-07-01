function my_certFn(){
	zeai.listEach(zeai.tag(my_certbox,'li'),function(obj){
		var w=460,h=560;
		if(obj.id=='mob'){h=360;}
		if(obj.id=='qq' ||obj.id=='weixin' || obj.id=='email' || obj.id=='sesame' || obj.id=='police' ){h=430;}
		obj.onclick=function(){supdes=ZeaiPC.iframe({url:PCHOST+"/my_cert"+zeai.ajxext+'submitok='+obj.id,w:w,h:h});}
	});
}
/**
 * [WWW.ZEAI.CN] (C)2001-2099 WWW.ZEAI.CN
 * QQ:797311，7144100
 * Email: supdes@qq.com
 * This is NOT a freeware, use is subject to license terms
 * Last update 2019/01/01
*/
function chkform_weixin(){
	if(zeai.str_len(weixin2.value)>30 || zeai.str_len(weixin2.value)<2){zeai.msg('请填写您正确的微信号',weixin2);return false;}
	zeai.ajax({"url":PCHOST+"/my_cert"+zeai.extname,"form":ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			zeai.msg(rs.msg,{time:2});setTimeout(function(){parent.location.reload(true);},2000);
		}else{
			zeai.msg(rs.msg);
		}
	});
}

function chkinputmob(){
	var obj=o('submitbtn_rz');
	var mob=o('mob_rz');
	if (zeai.ifmob(mob.value)){
		if (obj.hasClass('hui'))obj.removeClass('hui');	
	}else{
		if (!obj.hasClass('hui'))obj.addClass('hui');
	}
	if (!zeai.empty(mob.value)){
		reset_rz.show();
	}else{
		reset_rz.hide();
	}
}
function yzmtimeFn(countdown) { 
	if (countdown == 0) {
		mob_rz.disabled = false;
		yzmbtn.removeClass('disabled');
		yzmbtn.html('重新获取验证码'); 
		return false;
	} else { 
		if (!zeai.empty(o('yzmbtn'))){
			yzmbtn.addClass('disabled');
			yzmbtn.html("重新获取<font>(" + countdown + ")</font>"); 
			countdown--; 
		}
	}
	setTimeout(function(){yzmtimeFn(countdown)},1000);
}
function chkform_verify(){
	if(!zeai.ifmob(mob_rz.value)){zeai.msg('请输入手机号码',mob_rz);return false;}
	if(!zeai.ifint(verify.value)){zeai.msg('请输入验证码',verify);return false;}
	zeai.ajax({url:PCHOST+'/my_cert'+zeai.extname,data:{"submitok":"ajax_cert_mob_addupdate","mob":mob_rz.value,"verify":verify.value}},function(e){var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			zeai.msg(rs.msg,{time:2});setTimeout(function(){parent.location.reload(true);},2000);
		}else{
			zeai.msg(rs.msg)
		}
	});
}
function yzmbtnFn(){
	var mob=o('mob_rz');
	if (zeai.ifmob(mob.value)){
		if (!this.hasClass('disabled')){
			mob_rz.disabled = true;verify.value='';
			yzmbtn.addClass('disabled');
			zeai.ajax(PCHOST+"/my_cert"+zeai.ajxext+"submitok=ajax_cert_mob_getyzm&mob="+mob.value,function(e){var rs=zeai.jsoneval(e);
				zeai.msg(rs.msg);
				if (rs.flag == 1){
					yzmtimeFn(120);
				}else{
					mob_rz.disabled = false;
					yzmbtn.removeClass('disabled');
				}
			});
		}
	}else{
		zeai.msg('请输入手机号码');
		return false;
	}
}
/*identity*/
function sfz_up(browser,btnobj,objstr) {
	if(browser=='h5'){
		zeai.up({"url":PCHOST+"/my_cert"+zeai.extname,"upMaxMB":upMaxMB,"submitok":"ajax_sfz_h5_up","ajaxLoading":0,"fn":function(e){var rs=zeai.jsoneval(e);
			sfz_set(btnobj,rs,objstr);
		}});
	}
	function sfz_set(btnobj,rs,objstr){
		if (rs.flag == 1){
			zeai.msg(0);zeai.msg('上传成功！');
			var img=btnobj.getElementsByTagName("img")[0];
			img.src=up2+rs.dbname;
			o(objstr).value=rs.dbname;
		}else{zeai.alert(rs.msg);}
	}
}
function chkform_identity(){
	if(zeai.str_len(truename.value)>8 || zeai.str_len(truename.value)<2){zeai.msg('请填写您正确的真实姓名',truename);return false;}
	if(!zeai.ifsfz(identitynum.value)){zeai.msg('请填写您正确的身份证号码',identitynum);return false;}
	if (zeai.empty(sfz1.value) || zeai.empty(sfz2.value)){zeai.msg('您上传您本人身份证照片');return false;}
	zeai.ajax({"url":PCHOST+"/my_cert"+zeai.extname,"form":ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			sfz1.value='';sfz2.value='';
			zeai.msg(rs.msg,{time:2});setTimeout(function(){parent.location.reload(true);},2000);
		}else{
			zeai.msg(rs.msg);
		}
	});
}
function chkform_i(objstr){
	var ot;
	switch (objstr) {
		case 'edu':ot= '学历证书';break;
		case 'car':ot= '汽车行驶证';break;
		case 'house':ot= '房产证';break;
	}
	if (zeai.empty(path_b.value)){zeai.msg('请上传'+ot);return false;}
	zeai.ajax({"url":PCHOST+"/my_cert"+zeai.extname,"form":ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			path_b.value='';
			zeai.msg(rs.msg,{time:2});setTimeout(function(){parent.location.reload(true);},2000);
		}else{
			zeai.msg(rs.msg);
		}
	});
}
/*QQ*/
function yzmInit(json) {
	var obj=json.obj,btn=json.btn,S=json.S,T=json.T;
	if (!zeai.empty(o(btn))){
		btn.onclick = function(){
			if (!zeai.empty(obj.value)){
				if (!this.hasClass('disabled')){
					obj.disabled = true;
					btn.addClass('disabled');verify.value='';
					zeai.msg('正在发送QQ邮箱验证码',{time:5});
					zeai.ajax({"url":json.url,"data":{"submitok":"ajax_cert_"+obj.id+"_yzm_get","value":obj.value}},function(e){var rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if (rs.flag == 1){
							yzmTime(S);
						}else{
							obj.disabled = false;
							btn.removeClass('disabled');
						}
					});
				}
			}else{
				zeai.msg(T);
				return false;
			}
		}
	}
	function yzmTime(S) { 
		if (S == 0) {
			obj.disabled = false;
			btn.removeClass('disabled');
			btn.html('重新获取验证码'); 
			return;
		} else { 
			if (!zeai.empty(btn)){
				btn.addClass('disabled');
				btn.html("重新获取<font>(" + S + ")</font>"); 
				S--; 
			}else{return;}
		}
		setTimeout(function(){yzmTime(S)},1000);
	}
	if (!zeai.empty(o('submitbtn_'+obj.id))){
		o('submitbtn_'+obj.id).onclick = function(){
			if(zeai.empty(obj.value)){zeai.msg(T,obj);return false;}
			if(!zeai.ifint(verify.value)){zeai.msg('请输入验证码',verify);return false;}
			zeai.ajax({"url":json.url,"data":{"submitok":"ajax_cert_"+obj.id+"_yzm_submit","value":obj.value,"verify":verify.value}},function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){
					zeai.msg(rs.msg,{time:2});setTimeout(function(){parent.location.reload(true);},2000);
				}else{
					zeai.msg(rs.msg)
				}
			});
		}
	}
}

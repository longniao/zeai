/**
 * [ZEAI.CN] (C)2001-2099 WWW.ZEAI.CN
 * QQ:797311，7144100
 * Email: supdes@qq.com
 * This is NOT a freeware, use is subject to license terms
 * Last update 2019/01/01
*/
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
	zeai.ajax({url:HOST+'/m1/my_cert'+zeai.extname,data:{"submitok":"ajax_cert_mob_addupdate","mob":mob_rz.value,"verify":verify.value}},function(e){var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			zeai.msg(rs.msg);o('ZEAIGOBACK-my_cert_mob').click();o('my_info_certbtn').click();
		}else{
			zeai.msg(rs.msg,{mask:0})
		}
	});
}
/*identity*/
function sfz_up(browser,btnobj,objstr) {
	if(browser=='h5'){
		zeai.up({"url":HOST+"/m1/my_cert"+zeai.extname,"upMaxMB":upMaxMB,"submitok":"ajax_sfz_h5_up","ajaxLoading":0,"fn":function(e){var rs=zeai.jsoneval(e);
			sfz_set(btnobj,rs,objstr);
		}});
	}else if(browser=='wx'){
		zeai.msg(0);zeai.msg('载入中...',{time:5});
		ZeaiM.up_wx({"url":HOST+"/m1/my_cert"+zeai.extname,"submitok":"ajax_sfz_wx_up","ajaxLoading":0,"fn":function(e){var rs=zeai.jsoneval(e);
			sfz_set(btnobj,rs,objstr);
		}});
	}else if(browser=='app'){
		app_uploads({url:HOST+"/m1/my_cert"+zeai.extname+'?submitok=ajax_sfz_app_up',num:1},function(e){
			var rs=zeai.jsoneval(e);
			sfz_set(btnobj,rs,objstr);
		});
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

function chkform_sfz(truename,identitynum){
	if(zeai.str_len(truename)>8 || zeai.str_len(truename)<2){zeai.msg('请填写您本人真实姓名');return false;}
	if(!zeai.ifsfz(identitynum)){zeai.msg('请填写您本人真实身份证号码');return false;}
}
//传统
function chkform_identity(){
	chkform_sfz(truename.value,identitynum.value)
	if (zeai.empty(sfz1.value) || zeai.empty(sfz2.value)){zeai.msg('您上传身份证正反面照片');return false;}
	zeai.ajax({"url":HOST+"/m1/my_cert"+zeai.extname,"form":ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			sfz1.value='';sfz2.value='';
			zeai.msg(rs.msg);o('ZEAIGOBACK-my_cert_identity').click();o('my_info_certbtn').click();
		}else{
			zeai.msg(rs.msg);
		}
	});
}
//三元素
function chkform_identity_mob3(json){
	chkform_sfz(truename.value,identitynum.value)
	if(json.money>0){
		rz_pay(json,'identity');
	}else{
		zeai.msg('正在实名验证',{time:8});
		zeai.ajax({url:HOST+'/m1/my_cert'+zeai.extname,data:{submitok:json.submitok,truename:json.truename,identitynum:json.identitynum}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);
			if (rs.flag == 1){
				zeai.msg(rs.msg,{time:3});setTimeout(function(){json.ZEAIGOBACK.click();o('my_info_certbtn').click();},3000);
			}else{
				zeai.msg(rs.msg,{time:3});
			}
		});
	}
}
//人脸
function chkform_photo(json){
	chkform_sfz(truename.value,identitynum.value)
	if(json.money>0){
		rz_pay(json,'photo');
	}else{
		zeai.msg('正在人脸识别验证',{time:8});
		zeai.ajax({url:HOST+'/m1/my_cert'+zeai.extname,data:{formphoto:json.formphoto,submitok:json.submitok,truename:json.truename,identitynum:json.identitynum}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);
			if (rs.flag == 1){
				zeai.msg(rs.msg,{time:3});setTimeout(function(){json.ZEAIGOBACK.click();o('my_info_certbtn').click();},3000);
			}else{
				zeai.msg(rs.msg,{time:3});
			}
		});
	}
}

function rz_pay(json,rzkind){
	var data={truename:json.truename,identitynum:json.identitynum,formphoto:json.formphoto},
	postjson = Object.assign({},data,{submitok:json.submitok,money:json.money,paykind:json.paykind,kind:json.kind,orderid:json.orderid,rzkind:rzkind});
	zeai.ajax({url:HOST+'/m1/my_cert'+zeai.extname,js:1,data:postjson},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);
		if (rs.flag==1){
			if (rs.trade_type=='H5'){
				var redirect_url=rs.redirect_url;
				var postjson_payok = Object.assign({},data,{submitok:json.submitok+'_payok',payid:rs.payid,redirect_url:redirect_url})
				sessionStorage.postjson_payok=JSON.stringify(postjson_payok);
				zeai.openurl(rs.redirect_url);
				return false;
				zeai.msg('正在验证',{time:8});
				zeai.ajax({url:HOST+'/m1/my_cert'+zeai.extname,data:postjson_payok},function(e){var rs=zeai.jsoneval(e);
					zeai.msg(0);
					if (rs.flag == 1){
						zeai.msg(rs.msg,{time:3});setTimeout(function(){zeai.openurl(redirect_url);},3000);
					}else{
						zeai.msg(rs.msg,{time:3});
					}
				});
				
			}else{
				function jsApiCall(){
					WeixinJSBridge.invoke('getBrandWCPayRequest',rs.jsApiParameters,function(res){
						//WeixinJSBridge.log(res.err_msg);
						if(res.err_msg == "get_brand_wcpay_request:ok"){
							zeai.msg('正在验证',{time:8});
							var postjson_payok = Object.assign({},data,{submitok:json.submitok+'_payok'})
							zeai.ajax({url:HOST+'/m1/my_cert'+zeai.extname,data:postjson_payok},function(e){var rs=zeai.jsoneval(e);
								zeai.msg(0);
								if (rs.flag == 1){
									zeai.msg(rs.msg,{time:3});setTimeout(function(){json.ZEAIGOBACK.click();o('my_info_certbtn').click();},3000);
								}else{
									zeai.msg(rs.msg,{time:3});
								}
							});
						}else{
						   zeai.msg("支付失败,请返回上一步重新操作");
						   //alert(JSON.stringify(res));
						}				
					});
				}
				if (typeof WeixinJSBridge == "undefined"){
					if( document.addEventListener ){
						document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
					}else if (document.attachEvent){
						document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
						document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
					}
				}else{
					jsApiCall();
				}
			}
		}else{
			zeai.msg(rs.msg,{time:3});	
		}
	});
}

function chkform_weixin(){
	if(zeai.str_len(weixin2.value)>30 || zeai.str_len(weixin2.value)<2){zeai.msg('请填写您正确的微信号',weixin2);return false;}
	zeai.ajax({"url":HOST+"/m1/my_cert"+zeai.extname,"form":ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			zeai.msg(rs.msg);o('ZEAIGOBACK-my_cert_weixin').click();o('my_info_certbtn').click();
			weixin.class('ed');
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
		case 'love':ot= '单身证明或离婚证';break;
		case 'pay':ot= '收入证明/工资流水';break;
	}
	if (zeai.empty(path_b.value)){zeai.msg('请上传'+ot);return false;}
	zeai.ajax({"url":HOST+"/m1/my_cert"+zeai.extname,"form":ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			path_b.value='';
			zeai.msg(rs.msg);o('ZEAIGOBACK-my_cert_'+objstr).click();o('my_info_certbtn').click();
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
					btn.addClass('disabled');
					zeai.ajax({"url":json.url,"data":{"submitok":"ajax_cert_"+obj.id+"_yzm_get","value":obj.value}},function(e){var rs=zeai.jsoneval(e);
						zeai.msg(rs.msg);
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
					zeai.msg(rs.msg);o(json.goback).click();o('my_info_certbtn').click();
				}else{
					zeai.msg(rs.msg,{mask:0})
				}
			});
		}
	}
}

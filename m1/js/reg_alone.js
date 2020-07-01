var REG={};
function chkform(){
	var pwdV = o('pwd').value;
	if(reg_force_wx==1){
		var weixinV = o('weixin').value;
	}
	if(reg_kind==2){
		var unameV = o('uname').value;
		if(zeai.str_len(unameV) < 3 || zeai.str_len(unameV)>20){zeai.msg('请输入3-15个字符登录用户名',o('uname'));return false;}
		REG['uname']=unameV;
		var submitok='ajax_reg_uname_addupdate';
	}else if(reg_kind==1 || reg_kind==3){	
		var mobV = o('mob').value;
		if(!zeai.ifmob(mobV)){zeai.msg('请输入正确手机号',o('mob'));return false;}
		REG['mob']=mobV;
		if(reg_kind==3){
			var unameV = o('uname').value;
			if(zeai.str_len(unameV) < 3 || zeai.str_len(unameV)>20){zeai.msg('请输入3-15个字符登录用户名',o('uname'));return false;}
			REG['uname']=unameV;
		}
		var submitok='ajax_reg_yzm';
	}
	if(zeai.str_len(pwdV)<6 || zeai.str_len(pwdV)>20){zeai.msg('请输入正确的登录密码(长度6~20)',o('pwd'));return false;}
	if(reg_force_wx==1){
		if(  zeai.str_len(weixinV)<3 || zeai.str_len(weixinV)>40 ){zeai.msg('请输入正确的微信号',o('weixin'));return false;}
		REG['weixin']=weixinV;
	}
	REG['pwd']=pwdV;
	var url='reg_alone';
	zeai.ajax({url:url+zeai.extname,data:{tguid:sessionStorage.tguid,submitok:submitok,REG:JSON.stringify(REG)}},function (e){var rs=zeai.jsoneval(e);//form:o('ZEAI_form_reg'),
		if (rs.flag==1){
			if (rs.reg_kind == 2){
				zeai.openurl('reg_ed'+zeai.ajxext+'t=success');
			}else{
				ZeaiM.page.load({url:url+zeai.extname,data:{t:'yzm','mob':mobV}},'reg_end','reg_yzm');
			}
		}else{
			zeai.msg(0);zeai.msg(rs.msg);
		}
	});
}
function ajax_dl_yzm(){
	zeai.ajax({'url':'login'+zeai.extname,'data':{'submitok':'ajax_dl_yzm_get','mob':mob.value}},function(e){rs=zeai.jsoneval(e);
	if (rs.flag==1){
		zeai.ajax({'url':'login'+zeai.extname,'data':{'submitok':'ajax_dl_yzm_get_html','mob':mob.value,'jumpurl':jumpurl}},function(e){
			o('main').html(e);ZeaiM.eval(e);verify.focus();
			verify.oninput=function(){
				if(zeai.str_len(this.value) == 4){
					this.blur();login_dl_yzm_get_html();
				}
			}
		});
	}else{zeai.msg(rs.msg,{mask:0});}
});}

function reg_alone_udata(t,nextvar){
	//if (zeai.empty(REG['sex']) || zeai.empty(REG['areaid'])){zeai.msg('资料遗漏，请返回重选');}
	switch (REG['sex']){
		case 1:ico='&#xe60c;';cls='sex1';break;
		case 2:ico='&#xe95d;';cls='sex2';break;
		default:ico='&#xe61f;';cls='sex'+REG['sex'];break;
	}
	var ARR=eval(t+'_ARR');
	o('vtphoto'+t).append('<div class="'+cls+'"><i class="ico">'+ico+'</i></div>');
	for(var h=0;h<ARR.length;h++) {
		var hed=(ARR[h].i==REG[t])?' HONG':' BAI';
		var wsty=(t=='job' || t=='pay')?'':'W80_';
		o(t).append('<li id="li'+t+h+'" class="'+t+'li btn size4 '+wsty+' yuan'+hed+'">'+ARR[h].v+'</li>');
		(function(h){
			o('li'+t+h).onclick=function(){
				REG[t]=ARR[h].i;
				zeai.listEach('.'+t+'li',function(obj){if (obj.hasClass('HONG')){obj.removeClass('HONG');obj.addClass('BAI');}});
				this.removeClass('BAI');this.addClass('HONG');
				ZeaiM.page.load('reg_alone'+zeai.ajxext+'t='+nextvar,'reg_'+t,'reg_'+nextvar);
			}
		})(h);
	}
}

function reg(){
	zeai.alertplus({'title':'会员注册条款','content':'如果您同意本站条款<br>请点“同意”继续注册<br><br><a href="javascript:;" onclick="agreeDeclara();" class="Clan">查看条款</a>','title1':'取消','title2':'同意',
		'fn1':function(){zeai.alertplus(0);},
		'fn2':function(){
			zeai.alertplus(0);
		}
	});
}
function agreeDeclara(){
	o('alertpro_mask').style.zIndex=777;
	zeai.alertplus(0);
	ZeaiM.page.load('about'+zeai.ajxext+'t=declara',ZEAI_MAIN,'about_declara');
}
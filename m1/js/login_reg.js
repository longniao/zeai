function chkform(kind){
	var url=(zeai.empty(kind))?'login':kind;
	kind=(zeai.empty(kind))?'':'_'+kind;
	var unameV = o('uname'+kind).value,pwdV = o('pwd'+kind).value;
	if(zeai.str_len(unameV) < 1 || zeai.str_len(unameV)>20){zeai.msg('请输入正确的登录帐号',o('uname'+kind));return false;}
	if(zeai.str_len(pwdV)<6 || zeai.str_len(pwdV)>20){zeai.msg('请输入正确的密码',o('pwd'+kind));return false;}
	zeai.ajax({'url':url+zeai.extname,'form':o('ZEAI_form'+kind),data:{REG:JSON.stringify(REG)}},function (e){var rs=zeai.jsoneval(e);
		if(kind=='_reg'){
			if (rs.flag==1){
				REG['pwd']=pwdV;
				if (rs.reg_kind == 1 || rs.reg_kind == 3){
					REG['mob']=unameV;
					if(rs.reg_kind == 3){
						REG['username']=o('username').value;
					}
					ZeaiM.page.load({url:url+zeai.extname,data:{t:'yzm','mob':unameV}},'reg_end','reg_yzm');
				}else{
					REG['uname']=unameV;
					ZeaiM.page.load({url:url+zeai.extname,data:{t:'success'}},'reg_end','reg_success');
				}
			}else{
				zeai.msg(rs.msg,{mask:0});
			}
		}else{
			(rs.flag == 1 && zeai.openurl(rs.jumpurl)) || (rs.flag==0 && zeai.msg(rs.msg,{mask:0}));
		}
	});
}
function btnfun(Vkind,kind){
	kind=(zeai.empty(kind))?'':kind;
	var btnid='submitbtn'+kind;
	var resetid='reset'+kind;
	obj = o(btnid);
	if (Vkind=='mob'){
		if (zeai.ifmob(o('mob'+kind).value)){
			if (obj.hasClass('hui'))obj.removeClass('hui');	
		}else{
			if (!obj.hasClass('hui'))obj.addClass('hui');
		}
		if (!zeai.empty(o('mob'+kind).value)){
			o(resetid).show();
		}else{
			o(resetid).hide();
		}
	}else if(Vkind=='pwd'){	
		if (zeai.str_len(o('pwd'+kind).value)>=6 && zeai.str_len(o('pwd'+kind).value)<=20 && zeai.str_len(o('uname'+kind).value)>=1  && zeai.str_len(o('uname'+kind).value)<=20 ){
			if (obj.hasClass('hui'))obj.removeClass('hui');	
		}else{
			if (!obj.hasClass('hui'))obj.addClass('hui');
		}
		if (!zeai.empty(o('uname'+kind).value)){
			o(resetid).show();
		}else{
			o(resetid).hide();
		}
	}
}
function btnfun_reg(Vkind){
	btnfun(Vkind,'_reg');
}

function showpass(obj,passid){
	passid=(zeai.empty(passid))?'pwd':passid;
	if (o(passid).type == 'password'){
		o(passid).type = 'text';obj.html('&#xe62b;');
	}else{
		o(passid).type = 'password';obj.html('&#xe606;');
	}
}
function forgetpwd(){
	ZeaiM.page.load({'url':'reg'+zeai.extname,data:{t:'forgetpwd'}},ZEAI_MAIN,'reg_forgetpwd');
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

zeai.ready(function(){
	var regurl=(reg_style==1)?'reg_alone':'reg_diy';
	if (zeai.ifint(tmpid)){
		REG['tmpid'] = tmpid;
		if(zeai.empty(t)){
			zeai.alertplus({'title':'绑定帐号','content':'如果您已有帐号请直接点绑定<br>如果没有请先注册新帐号','title1':'绑定','title2':'注册',
				'fn1':function(){zeai.alertplus(0);uname.focus();},
				'fn2':function(){zeai.alertplus(0);//ZeaiM.page.load('reg'+zeai.extname,ZEAI_MAIN,'reg_sex');
				zeai.openurl(regurl+zeai.ajxext+'ifback=1&tmpid='+tmpid+'&tguid='+tguid);
				}
			});
		}
	}
	zeai.ajax({'url':'login'+zeai.extname,'data':{subscribe:subscribe,submitok:'ajax_dl_uname',tmpid:tmpid,tguid:tguid,'jumpurl':jumpurl}},function(e){
		main.html(e);
		//if (!zeai.empty(t))ZeaiM.page.load('reg'+zeai.ajxext+'t='+t,ZEAI_MAIN,'reg_'+t);
		if (!zeai.empty(t))zeai.openurl(regurl+zeai.ajxext+'ifback=1&tmpid='+tmpid+'&tguid='+tguid);
	});
});

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
		o(t).append('<li id="li'+t+h+'" class="'+t+'li btn size4 W80_ yuan'+hed+'">'+ARR[h].v+'</li>');
		(function(h){
			o('li'+t+h).onclick=function(){
				REG[t]=ARR[h].i;
				zeai.listEach('.'+t+'li',function(obj){if (obj.hasClass('HONG')){obj.removeClass('HONG');obj.addClass('BAI');}});
				this.removeClass('BAI');this.addClass('HONG');
				
				ZeaiM.page.load('reg'+zeai.ajxext+'t='+nextvar,'reg_'+t,'reg_'+nextvar);
				
			}
		})(h);
	}
}
function login3(kind){
	switch (kind) {
		case 'uname':
			zeai.ajax({'url':'login'+zeai.extname,'data':{'submitok':'ajax_dl_uname'}},function(e){o('main').html(e);});
		break;
		case 'yzm':
			zeai.ajax({'url':'login'+zeai.extname,'data':{'submitok':'ajax_dl_yzm','jumpurl':jumpurl}},function(e){o('main').html(e);});
		break;
		case 'qq':zeai.openurl('../api/qq/login/CS.php?tguid='+localStorage.tguid+'&jumpurl='+encodeURIComponent(jumpurl));break;
		case 'weixin':zeai.openurl('../api/weixin/login/CS.php?tguid='+localStorage.tguid+'&jumpurl='+encodeURIComponent(jumpurl));break;
	}
}
/*function reg(){
	ZeaiM.page.load('reg'+zeai.ajxext+'t=sex',ZEAI_MAIN,'reg_sex');
}*/
function reg(){
	zeai.alertplus({'title':'会员条款','content':'如果您同意本站条款　<a href="javascript:;" onclick="agreeDeclara();" class="Clan">查看条款</a><br>请点“同意”继续注册','title1':'取消','title2':'同意',
		'fn1':function(){zeai.alertplus(0);},
		'fn2':function(){
			zeai.alertplus(0);ZeaiM.page.load('reg'+zeai.ajxext+'t=sex',ZEAI_MAIN,'reg_sex');
		}
	});
}

function agreeDeclara(){
	o('alertpro_mask').style.zIndex=777;
	zeai.alertplus(0);
	ZeaiM.page.load('about'+zeai.ajxext+'t=declara',ZEAI_MAIN,'about_declara');
}
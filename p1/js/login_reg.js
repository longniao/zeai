/**************************************************
版权所有@2019 www.zeai.cn 
原创作者：QQ:797311 (supdes) Zeai.cn V6.0 微信号：supdes
**************************************************/
if(!zeai.empty(o('loginbtn')))o('loginbtn').onclick = function(){
	var uname = o('uname').value,pwd = o('pwd').value,jumpurl = o('jumpurl').value;
	if(zeai.str_len(uname) < 1 || zeai.str_len(uname)>20){zeai.msg('请输入正确的登录帐号',o('uname'));return false;}
	if(zeai.str_len(pwd)<6 || zeai.str_len(pwd)>20){zeai.msg('请输入正确的密码',o('pwd'));return false;}
	zeai.ajax({url:'login'+zeai.extname,form:WWW_ZEAI_CN_form},function(e){rs=zeai.jsoneval(e);
		(rs.flag == 1 && zeai.openurl(rs.jumpurl)) || (rs.flag==0 && zeai.msg(rs.msg));
	});
	return false;
}
function Zeai_cn__getpass(){
	var uname = o('uname').value;
	if(zeai.str_len(uname) < 1 || zeai.str_len(uname)>20){zeai.msg('请输入正确的登录帐号',o('uname'));return false;}else{
		zeai.ajax({url:'login'+zeai.ajxext+'submitok=ajax_findpass&uname='+uname},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg,o('uname'));
		});
	}
}
function login3(kind,page){
	if(page=='reg'){
		sessionStorage.pagekind='reg';
	}else{
		sessionStorage.pagekind='login';
	}
	switch (kind) {
		case 'qq':zeai.openurl('../api/qq/login/CS.php?page='+page+'&tguid='+localStorage.tguid+'&jumpurl='+encodeURIComponent(jumpurl));break;
		case 'weixin':zeai.openurl('../api/weixin/login/CS.php?page='+page+'&tguid='+localStorage.tguid+'&jumpurl='+encodeURIComponent(jumpurl));break;
	}
}
zeai.ready(function(){
	tmpid=parseInt(tmpid);
	if (zeai.ifint(tmpid)){
		zeai.alertplus({'title':'绑定帐号','content':'如果您已有帐号请直接点绑定<br>如果没有请先注册新帐号','title1':'绑定','title2':'注册',
			'fn1':function(){zeai.alertplus(0);uname.focus();},
			'fn2':function(){zeai.alertplus(0);zeai.openurl('reg.php?tmpid='+tmpid);}
		});
	}
});
if(!zeai.empty(o('regbtn')))o('regbtn').onclick = function(){
	if(zeai.empty(o('birthday').value)){
		zeai.msg('请选择-生日');
		zeai.setScrollTop(100);
		setTimeout(function(){o('birthday_Ybox').parentNode.show();},1000);
		return false;
	}
	if(zeai.empty(o('reg_area_area1id').value) || zeai.empty(o('reg_area_area2id').value) || zeai.empty(o('reg_area_area3id').value)){
		zeai.msg('请选择-所在地区');
		zeai.setScrollTop(200);
		setTimeout(function(){
			if(!zeai.empty(o('reg_area_area1id').value)&&!zeai.empty(o('reg_area_area2id').value)&&zeai.empty(o('reg_area_area3id').value)){
				o('reg_area_dt1id').removeClass('ed');
				o('reg_area_dt2id').removeClass('ed');
				o('reg_area_dt3id').class('ed');
				o('reg_area_a1box').parentNode.hide();
				o('reg_area_a2box').parentNode.hide();
				o('reg_area_a3box').parentNode.show();
			}else{
				o('reg_area_a1box').parentNode.show();
			}
		},1000);
		return false;
	}
	if(zeai.empty(o('height').value)){
		zeai.msg('请选择-身高');
		zeai.setScrollTop(250);
		setTimeout(function(){o('reg_height_box').parentNode.show();},1000);
		return false;
	}
	if(!ZEAI_select_chk('edu')){ZEAI_select_do('edu','reg_edu','请选择-学历',350);return false;}
	if(!ZEAI_select_chk('love')){ZEAI_select_do('love','reg_love','请选择-婚姻状况',400);return false;}
	if(!ZEAI_select_chk('job')){ZEAI_select_do('job','reg_job','请选择-职业',450);return false;}
	if(!ZEAI_select_chk('pay')){ZEAI_select_do('pay','reg_pay','请选择-月收入',500);return false;}
	if(zeai.empty(o('nickname').value)){zeai.msg('请输入-昵称',nickname);return false;}
	if(reg_kind==1 || reg_kind==3){
		if(!zeai.ifmob(o('mob').value)){zeai.msg('请输入-手机号码',mob);return false;}
		if(!zeai.ifint(o('verify').value) || zeai.str_len(o('verify').value)!=4 ){zeai.msg('请输入-手机验证码',verify);return false;}
	}
	if(reg_kind==2 || reg_kind==3){
		if(zeai.str_len(o('uname').value) <3 || zeai.str_len(o('uname').value)>16){zeai.msg('请输入3-15个字符用户名',uname);return false;}
	}
	if(zeai.str_len(o('pwd').value) <3 || zeai.str_len(o('pwd').value)>20){zeai.msg('请输入6-20个字符密码',pwd);return false;}
	zeai.ajax({url:'reg'+zeai.extname,form:WWW_ZEAI_CN_form},function(e){rs=zeai.jsoneval(e);
		(rs.flag==1 && zeai.openurl(PCHOST+'/my.php')) || (rs.flag==0 && zeai.msg(rs.msg))
	});
}

function ZEAI_select_chk(obj){
	if(zeai.empty(o(obj).value))return false;
	return true;
}
function ZEAI_select_do(obj,f,t,scrolT){
	scrolT=(zeai.empty(scrolT) || !zeai.ifint(scrolT))?0:scrolT;
	zeai.setScrollTop(scrolT);
	setTimeout(function(){o(f).children[1].show();},1000);
	zeai.msg(t);
}
if (!zeai.empty(o('yzmbtn'))){
	yzmbtn.onclick = function(){
		if (zeai.ifmob(o('mob').value)){
			function ajax_click_yzmFn(){
				zeai.ajax({url:PCHOST+'/reg'+zeai.ajxext+'submitok=ajax_click_yzm'},function(e){var rs=zeai.jsoneval(e);
					zeai_yzm_em.style.backgroundColor=rs.bg;
					zeai_yzm_li.html(rs.li);
					zeai_yzm.show();
					zeai.listEach(zeai.tag(zeai_yzm_li,'li'),function(obj){
						obj.onclick=function(){zeai_yzm_liFn(rs.bg,this.innerHTML);};
					});
	
				});
			}
			ajax_click_yzmFn();
			function zeai_yzm_liFn(bg,v){
				zeai.ajax({url:PCHOST+'/reg'+zeai.ajxext+'submitok=ajax_click_yzm_chk',data:{bg:bg,v:v}},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if(rs.flag==1){
						zeai_yzm.hide();
						//zeai.msg(rs.msg);
						setTimeout(function(){
							if (!yzmbtn.hasClass('disabled')){
								yzmbtn.addClass('disabled');
								zeai.ajax({url:'reg'+zeai.extname,data:{'submitok':'ajax_reg_verify',mob:o('mob').value,bg:bg,v:v}},function(e){
									var rs=zeai.jsoneval(e);
									zeai.msg(0);
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
						},200);			
					}else{
						zeai.msg(rs.msg);
						setTimeout(function(){ajax_click_yzmFn();},1000);
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
if (!zeai.empty(o('verify')))verify.oninput=function(){
	if(zeai.str_len(this.value) == 4 && zeai.ifint(this.value)){
		zeai.ajax({url:'reg'+zeai.extname,data:{'submitok':'ajax_reg_verify_chk','verify':verify.value}},function(e){var rs=zeai.jsoneval(e);
			if (rs.flag == 0){zeai.msg(rs.msg);}
		});
	}
}
function readclause(){ZeaiPC.iframe({url:PCHOST+'/reg'+zeai.ajxext+'submitok=clause',w:700,h:550});}

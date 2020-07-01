if(!zeai.empty(o('piclist'))){
	zeai.listEach(zeai.tag(o('piclist'),'li'),function(obj){
		var b=obj.getAttribute("value");
		obj.style.backgroundImage='url('+b+')';
		obj.onclick=function(){
			ZeaiPC.piczoom(b);
		}
	});
}
if(!zeai.empty(o('party_bbsbtn')))o('party_bbsbtn').onclick=function(){
	bbs_addFn();
}
if(!zeai.empty(o('party_bbsbtn2')))o('party_bbsbtn2').onclick=function(){
	bbs_addFn();
}
function bbs_addFn(){
	supdes=ZeaiPC.iframe({url:PCHOST+'/party_detail'+zeai.ajxext+'submitok=bbs_add&fid='+fid,w:500,h:300});
}
function contentFn(){
	var ic = this.value.length;
	if (ic>140){
		this.value = this.value.substring(0,140);
		zeai.msg(0);zeai.msg('最多140字');
		ic = 140;
		return false;
	}
	inpttext.html(ic);
}
function partybbs_btn_saveFn(){
	var C=zeai.clearhtml(content.value);
	content.value = C.substring(0,140);
	if (zeai.empty(C) || C.length > 140){zeai.msg('亲，该说点什么吧～',content);return false;}
	zeai.ajax({url:PCHOST+'/party_detail'+zeai.extname,form:partyZ_eA_I____cn_bbsbox},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){
			setTimeout(function(){parent.location.reload(true);},1000);
		}else if(rs.flag=='nologin'){
			setTimeout(function(){if(rs.flag=='nologin'){parent.zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}},1000);
		}else{setTimeout(function(){parent.supdes.click();},1000);}
	});
}
if(!zeai.empty(o('kefubtn')))o('kefubtn').onclick=function(){
	setTimeout(function(){o('party_kefu').addClass('shake');},300);
	setTimeout(function(){o('party_kefu').removeClass('shake');},800);
}
if(!zeai.empty(o('party_bbsbtn')))o('party_bbsbtn').onclick=function(){
	setTimeout(function(){o('party_bbsbtn2').addClass('shake');},300);
	setTimeout(function(){o('party_bbsbtn2').removeClass('shake');},800);
}
function party_bmbtnFn(){
	zeai.ajax({url:PCHOST+'/party_detail'+zeai.ajxext+'submitok=ajax_bm_ifpay&fid='+fid},function(e){rs=zeai.jsoneval(e);
		if(rs.ifpay==1){
			supdes=ZeaiPC.iframe({url:PCHOST+'/party_detail'+zeai.ajxext+'submitok=ajax_bm_add_update_pay&fid='+fid,w:500,h:500});
		}else if(rs.flag=='nologin'){
			zeai.msg(0);zeai.msg(rs.msg);
			setTimeout(function(){if(rs.flag=='nologin'){parent.zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}},1000);
		}else{
			supdes=ZeaiPC.iframe({url:PCHOST+'/party_detail'+zeai.ajxext+'submitok=ajax_bm_add&fid='+fid,w:500,h:500});
		}
	});


}
function party_detail_bm_btnFn(){
	zeai.confirm('确定信息无误提交报名么？',function(){
		zeai.ajax({url:PCHOST+'/party_detail'+zeai.extname,form:Www_Zeai_cn_PartyBm},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){
				zeai.openurl(PCHOST+'/party_detail'+zeai.ajxext+'submitok=ajax_bm_add_update_pay&fid='+rs.fid);
			}
		});	
	});
}
if(!zeai.empty(o('partyewm')))setTimeout(function(){partyewm.src=HOST+'/sub/creat_ewm'+zeai.ajxext+'&url='+partyhref;},500);
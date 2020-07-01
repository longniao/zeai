var supdes;
if(!zeai.empty(o('list'))){
zeai.listEach(zeai.tag(list,'p'),function(obj){
	var psrc=obj.getAttribute("value");
	obj.style.backgroundImage='url('+psrc+')';
		
});}if(!zeai.empty(o('list'))){
zeai.listEach(zeai.tag(list,'dt'),function(dt){
	var uid=dt.getAttribute("uid"),gz=dt.lastChild; 
	gz.onclick = function (){
		var self=this;
		zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_gz',uid:uid}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				self.addClass('ed');self.html('<i class="ico">&#xe6b1;</i> 已关注');
			}else{
				self.removeClass('ed');self.html('<i class="ico">&#xe620;</i> 加关注');
			}
			zeai.msg(rs.msg);
		});
	}
});
}

if (o('qiang'))o('qiang').onclick = function(){
	if (hb_flag != 1){zeai.msg('已抢光或已过期');return false;}
	zeai.ajax({url:PCHOST+'/hongbao_detail'+zeai.ajxext+'submitok=ajax_add_qiang&fid='+fid,js:1},function(e){rs=zeai.jsoneval(e);
		switch (rs.flag){
			case 'nologin':
				zeai.msg(0);zeai.msg(rs.msg);
				setTimeout(function(){if(rs.flag=='nologin'){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}},1000);
			break;
			case 1:
				o('qiang').className = 'ed';
				o('qiang').onclick   = null
				o('mask_qd').show();o('qdokbox').show();
				o('randloveb').html(rs.moeny);
			break;
			default:zeai.msg(0);zeai.msg(rs.msg);break;
		}
	});
}
if (!zeai.empty(o('mask_qd'))){
	o('mask_qd').onclick = function(){location.reload(true);}
	o('qdokbox').onclick = function(){location.reload(true);}
}
if (o('btnshang'))o('btnshang').onclick = function(){
	zeai.confirm('确定要发红包给Ta么？',function(){
		zeai.ajax({url:PCHOST+'/hongbao_detail'+zeai.ajxext+'submitok=ajax_add_shang&fid='+fid,js:1},function(e){rs=zeai.jsoneval(e);
			if(rs.flag == 'nomoney'){
				zeai.msg(0);zeai.msg(rs.msg,{time:2});
				setTimeout(function(){zeai.openurl(PCHOST+'/my_money'+zeai.ajxext+'t=3&jumpurl='+encodeURIComponent(rs.jumpurl));},2000);
			}else if(rs.flag == 1){
				supdes=ZeaiPC.iframe({url:PCHOST+'/hongbao_detail'+zeai.ajxext+'submitok=add_shang&fid='+fid,w:500,h:300});
			}else{zeai.msg(0);zeai.msg(rs.msg,{time:2});}
		});
	});
}
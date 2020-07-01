if(!zeai.empty(o('ulist'))){
zeai.listEach(zeai.tag(ulist,'p'),function(obj){
	var psrc=obj.getAttribute("value");
	obj.style.backgroundImage='url('+psrc+')';
		
});}
if(!zeai.empty(o('hn_bbs_btn')))o('hn_bbs_btn').onclick=function(){
	zeai.confirm('您只有一次评价机会～确定么？',function(){
		if(zeai.str_len(content.value)<1 || zeai.str_len(content.value)>1000){
			zeai.msg('请输入评价内容',content);
			return false;
		}
		content.value = zeai.clearhtml(content.value);
		if (!zeai.form.ifradio('kind')){zeai.msg('请给红娘服务打分【好评－中评－差评】');return false;}
		zeai.ajax({url:HOST+'/p2/hongniang_detail'+zeai.extname,js:1,form:WwW_Zeai_CN_hnBBS},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg,{time:2});
			if(rs.flag==1)setTimeout(function(){location.reload(true);},2000);
		});
	});
}
if(!zeai.empty(o('joinhn')))o('joinhn').onclick=function(){
	//zeai.ajax({url:HOST+'/p2/hongniang_detail'+zeai.ajxext+'submitok=ajax_iflogin&fid='+fid,js:1});
	//supdes=ZeaiPC.iframe({url:HOST+'/p2/hongniang_detail'+zeai.ajxext+'submitok=ajax_join&fid='+fid,w:500,h:500});
	zeai.ajax({url:HOST+'/p2/hongniang_detail'+zeai.ajxext+'submitok=ajax_iflogin&fid='+fid,js:1},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			supdes=ZeaiPC.iframe({url:HOST+'/p2/hongniang_detail'+zeai.ajxext+'submitok=ajax_join&fid='+fid,w:500,h:500});
		}
	});
}
function hn_join_btnFn(){
	if(zeai.ifint(localStorage.uid))uuid.value=localStorage.uid;
	zeai.confirm('确定信息无误提交申请么？',function(){
		zeai.ajax({url:HOST+'/p2/hongniang_detail'+zeai.extname,js:1,form:WwW_Zeai_CN_hnJoin},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg,{time:5});
			if(rs.flag==1)setTimeout(function(){parent.location.reload(true);},2000);
		});
	});
}
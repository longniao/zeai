sokind.onclick=function(e){e.cancelBubble = true;if(soul.style.display=='block'){soul.hide();Zeai_search_mask0.hide();}else{soul.show();Zeai_search_mask0.show();}}
zeai.listEach(zeai.tag(soul,'li'),function(obj){
	obj.onclick=function(e){
		e.cancelBubble = true;sokind.firstChild.html(this.innerHTML);
		zeai.listEach(zeai.tag(soul,'li'),function(obj2){obj2.removeClass('ed');});	
		this.class('ed');sk.value=this.getAttribute("value");key.setAttribute('placeholder','请输入'+this.innerHTML+'名称');
		if(soul.style.display=='block'){soul.hide();Zeai_search_mask0.hide()}
		hismy.html('<img src="'+HOST+'/res/loading.gif" class="middle">');
		zeai.ajax({url:'shop_search'+zeai.extname,data:{submitok:'ajax_kind_history_my',sk:sk.value}},function(e){hismy.html(e);});
		hishot.html('<img src="'+HOST+'/res/loading.gif" class="middle">');
		setTimeout(function(){zeai.ajax({url:'shop_search'+zeai.extname,data:{submitok:'ajax_kind_history_hot',sk:sk.value}},function(e){hishot.html(e);});},200);
	}
});
Zeai_search_mask0.onclick=function(){soul.hide();Zeai_search_mask0.hide();}
sobtn.onclick=function(){
	if(zeai.empty(key.value)){
		zeai.msg(key.getAttribute("placeholder"));
	}else{
		zeai.ajax({url:'shop_search'+zeai.extname,data:{submitok:'ajax_key_save',sk:sk.value,key:key.value}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				var action=(sk.value=='goods')?'shop_goods'+zeai.extname:'shop_shop'+zeai.extname;
				zeai_cn__form_search.setAttribute('action',action);zeai_cn__form_search.submit();
			}
		});
	}
}
shop_search_clearbtn.onclick=function(){
	ZeaiM.confirmUp({title:'确定清空搜索记录么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_search'+zeai.extname,data:{submitok:'ajax_clear_my',sk:sk.value}},function(){hismy.html('');});
	}});
}
setTimeout(function(){key.focus();},100);
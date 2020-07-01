if(!zeai.empty(o('list'))){
zeai.listEach(zeai.tag(list,'p'),function(obj){
	var psrc=obj.getAttribute("value");
	obj.style.backgroundImage='url('+psrc+')';
		
});
}if(!zeai.empty(o('ulist'))){
zeai.listEach(zeai.tag(ulist,'p'),function(obj){
	var psrc=obj.getAttribute("value");
	obj.style.backgroundImage='url('+psrc+')';
		
});
}window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"1","bdSize":"32"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src=HOST+'/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
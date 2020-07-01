function indexOnscroll(){
	var t = zeai.getScrollTop(),cH= parseInt(document.body.clientHeight),H= parseInt(document.body.scrollHeight);
	if ((H-t-cH) <100){
		if (p > ZEAI_totalP){
			return false;
		}else{
			zeai.ajax({url:'shop_index'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,p:p,ie:ie}},function(e){
			if (e == 'end'){
				zeai.msg(0);zeai.msg('已达末页，全部加载结束');return false;
			}else{
				ZEAI_load(ZEAI_list,e,p);p++;
			}
		});}
	}
	backtopFn();
}
function ZEAI_load(mainobj,e,p){
	mainobj.append(e);
	var newlist = mainobj.getElementsByClassName('p'+p);
	ZEAI_init(newlist);
}
function ZEAI_init(newlist){
	zeai.listEach(newlist,function(img){
		var oldSrc = img.src;
		img.src = HOST+'/res/loading.gif';
		img.onload=function(){this.src=oldSrc}
	});
}
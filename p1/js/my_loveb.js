var supdes;
function my_loveb_Fn(czlist){
	zeai.listEach(czlist,function(obj){
		if (obj.hasClass('ed')){priceInit(obj);}
		obj.onclick=function(){priceInit(obj);cleardom(obj);}
	});
}
function priceInit(obj){
	var rmb=obj.getAttribute("rmb");
	o('money').value = rmb;
	price.html(rmb*loveBzk+'元');
	var tt = (loveBzk < 1)?'　('+loveBzk*10+'折优惠)':'';
	pricetitle.html(tt);
}
function cleardom(curdom){
	zeai.listEach(czlist,function(obj){obj.removeClass('ed');});
	curdom.addClass('ed');
}
function my_loveb_nextbtnFn(){
	if (o('money').value<=0){zeai.msg('请选择');return false;}
	supdes=ZeaiPC.iframe({url:PCHOST+'/my_pay'+zeai.ajxext+'kind='+o('kind').value+'&money='+o('money').value+'&jumpurl='+jumpurl,w:500,h:450})
}
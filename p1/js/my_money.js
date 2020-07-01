var supdes,moneyzk=1,gyl;
function my_money_Fn(czlist){
	zeai.listEach(czlist,function(obj){
		if (obj.hasClass('ed')){priceInit(obj);}
		obj.onclick=function(){priceInit(obj);cleardom(obj);}
	});
}
function priceInit(obj){
	var rmb=obj.getAttribute("rmb");
	o('money').value = rmb;
	price.html(rmb*moneyzk+'元');
	var tt =(moneyzk < 1)?'　('+moneyzk*10+'折优惠)':'';
	pricetitle.html(tt);
}
function cleardom(curdom){
	zeai.listEach(czlist,function(obj){obj.removeClass('ed');});
	curdom.addClass('ed');
}
function my_money_nextbtnFn(){
	if (o('money').value<=0){zeai.msg('请选择');return false;}
	supdes=ZeaiPC.iframe({url:PCHOST+'/my_pay'+zeai.ajxext+'kind='+o('kind').value+'&money='+o('money').value+'&jumpurl='+jumpurl,w:500,h:450})
}
function my_money_txbtnFn(){
	zeai.ajax({url:PCHOST+'/my_money'+zeai.ajxext+'submitok=ajax_binding'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==0){
			clearInterval(gyl);
			zeai.ajax({url:PCHOST+'/my_money'+zeai.ajxext+'submitok=ajax_get_ewm'},function(e){rs=zeai.jsoneval(e);
				if (rs.flag==1){
					supdes=ZeaiPC.div({obj:o('subscribe_box_my_money_tx'),w:360,h:370});
					Z__e_A___I_c____N.src=rs.ewm;
					gyl= setInterval(chk_binding,3000);
				}
			});
		}else if(rs.flag==1){
			zeai.confirm('确定真的要提现么？',function (){zeai.ajax({'url':PCHOST+'/my_money'+zeai.extname,'js':1,'form':z_e_a_i__c_n__tx_form},function(e){rs=zeai.jsoneval(e);
				if (rs.flag==1){
					zeai.msg(rs.msg,{time:3});
					setTimeout(function(){location.reload(true);},3000);
				}else{
					zeai.msg(rs.msg);
				}
			});});
		}
	});
}
function chk_binding(){
	zeai.ajax({url:PCHOST+'/my_money'+zeai.ajxext+'submitok=ajax_binding'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){zeai.msg(rs.msg);setTimeout(function(){location.reload(true);},2000);}
	});
}
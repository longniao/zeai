function vipFn(grade,title){
	o('grade').value = grade;
	vipListener(grade);
	gradename1.html(decodeURIComponent(title+'特权'));
	gradename2.html(decodeURIComponent(title+'套餐详情'));
	vipbtn.html(decodeURIComponent('立即开通 '+title+'服务'));
}
function vipListener(grade){
	var curdom = o('vip'+grade),vipbtn=o('vipbtn');
	var i=0;
	zeai.listEach('.vipli',function(obj){
		if (curdom != obj)obj.checked = false;
		if (obj.checked == true)i++;
	});
	if (i>0){
		vipbtn.removeClass('HUI');vipbtn.addClass('HONG');
		vipcleartdcls();
		o('vipprice'+grade).class('vippricebox on');
		getvipauth(grade);
	}else{
		vipbtn.removeClass('HONG');
		if (!vipbtn.hasClass('HUI'))vipbtn.addClass('HUI');
		o('vipprice'+grade).class('vippricebox off');
		o('grade').value = 0;
	}
}
function vipcleartdcls(){
	zeai.listEach('.vippricebox',function(obj){
		obj.class('vippricebox off');
	});
}
function getvipauth(grade){
	zeai.ajax({url:'shop_my_vip'+zeai.extname,data:{submitok:'ajax_getvipauth',grade:grade}},function(e){var rs=zeai.jsoneval(e);
		if(rs.flag==1){vipauth.html(rs.C);vipC.html(html_decode(rs.vipC));}
	});
}
if(!zeai.empty(o('vipbtn')))vipbtn.onclick=function(){
	var grade = parseInt(o('grade').value);
	if(zeai.ifint(grade) && grade>0){
		zeai.ajax({url:'shop_my_vip'+zeai.ajxext+'submitok=ajax_pay',data:{grade:grade}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){
				zeai_PAY({money:rs.money,paykind:'wxpay',kind:11,orderid:rs.orderid,tmpid:grade,tg_uid:rs.cid,title:decodeURIComponent(rs.title),return_url:rs.return_url,jumpurl:rs.jumpurl});
			}else if(rs.flag=='success'){
				setTimeout(function(){zeai.openurl(rs.url);},1000);
			}else if(rs.flag=='noshop'){
				setTimeout(function(){zeai.openurl('shop_my_apply.php');},1000);
			}
		});
	}
}
if(!zeai.empty(o('vipbtnfree')))vipbtnfree.onclick=function(){
	zeai.ajax({url:'shop_my_vip'+zeai.ajxext+'submitok=ajax_free'},function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag=='success'){setTimeout(function(){zeai.openurl(rs.url);},1000);}
	});
}

function vipFn(grade,if2,title){
	o('grade').value = grade;
	o('if2').value = if2;
	vipListener(grade);
	gradename.html(decodeURIComponent(title+'尊享特权'));
	gradename2.html(decodeURIComponent(title+'套餐详情'));
}
function vipListener(grade){
	var curdom = o('vip'+grade);
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
		o('if2').value = 0;
	}
}
function vipcleartdcls(){
	zeai.listEach('.vippricebox',function(obj){
		obj.class('vippricebox off');
	});
}
function getvipauth(grade){
	zeai.ajax({url:HOST+'/m1/my_vip'+zeai.extname,data:{submitok:'ajax_getvipauth',grade:grade}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){vipauth.html(rs.C);vipC.html(html_decode(rs.vipC));}
	});
}
function html_decode(str){           
  str = str.replace(/&amp;/g, '&'); 
  str = str.replace(/&lt;/g, '<');
  str = str.replace(/&gt;/g, '>');
  str = str.replace(/&quot;/g, "'");  
  str = str.replace(/&#039;/g, "'");  
  return str;  
}
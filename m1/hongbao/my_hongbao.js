function chkform3(){
//	var m1 = get_option('m1','v');
//	var m2 = get_option('m2','v');
//	var m3 = get_option('m3','v');
//	var m1t = get_option('m1','t');
//	var m2t = get_option('m2','t');
//	var m3t = get_option('m3','t');
//	m1t = (nulltext == m1t)?'':m1t;
//	m2t = (nulltext == m2t)?'':' '+m2t;
//	m3t = (nulltext == m3t)?'':' '+m3t;
//	m1 = (m1 == 0)?'':m1;
//	m2 = (m2 == 0)?'':','+m2;
//	m3 = (m3 == 0)?'':','+m3;
//	var mate_areaid = m1 + m2 + m3;
//	mate_areaid = (mate_areaid == '0,0,0')?'':mate_areaid;
//	var mate_areatitle = m1t + m2t + m3t;
//	o('areaid').value = mate_areaid;
//	o('areatitle').value = mate_areatitle;
	var amount = parseInt(o('amount').value);
	var num    = parseInt(o('num').value);
	var kind;
	if (o('kind1').checked){
		kind = 1;
	}else{
		kind = 2;
	}
	ZEAI_win_confirm('确定发布么～',function (){
		XML_ajax('hongbao'+ajxext+'submitok=ajax_chkmoney&amount='+amount+'&num='+num+'&t=3'+'&kind='+kind,function(e){rs=jsoneval(e);
			if (rs.flag == 1){
				o('GYLform3').submit();
			}else if(rs.flag == -1){
				ZEAI_win_alert(rs.msg,rs.jumpurl);
			}else{ZEAI_win_alert(rs.msg);}
		});
	});
}
function chkform4(){
	var money = parseInt(o('money').value);
	ZEAI_win_confirm('确定发布么～',function (){
		XML_ajax('hongbao'+ajxext+'submitok=ajax_chkmoney4&money='+money+'&t=4',function(e){rs=jsoneval(e);
			if (rs.flag == 1){
				o('GYLform4').submit();
			}else if(rs.flag == -1){
				ZEAI_win_alert(rs.msg,rs.jumpurl);
			}else{ZEAI_win_alert(rs.msg,rs.jumpurl);}
		});
	});
}
if (!empty(o('submit3')))o('submit3').onclick = function(){chkform3();}
if (!empty(o('submit4')))o('submit4').onclick = function(){chkform4();}
if (!empty(o('kind1')))o('kind1').onclick = function(){
	o('amount_t').innerHTML = '红包总金额';
	o('amount').value = 20;
	o('money_t').innerHTML = 20;
}
if (!empty(o('kind2')))o('kind2').onclick = function(){
	o('amount_t').innerHTML = '单个金额';
	o('amount').value = 5;
	o('money_t').innerHTML = parseInt(o('amount').value)*parseInt(o('num').value);
}
if (!empty(o('amount')))o('amount').oninput = function (){	autoprice();}
if (!empty(o('num')))o('num').oninput = function (){autoprice();}
function autoprice(){
	var amount = parseInt(o('amount').value);
	var num    = parseInt(o('num').value);
	if (amount <= 0 || num<= 0){
		ZEAI_win_alert('请输入一个大于0的正整数');
		o('amount').value = 20;
		o('num').value = 5;
	}
	money_();
}
function money_(){
	var amount = parseInt(o('amount').value);
	var num    = parseInt(o('num').value);
	if (o('kind1').checked){
		o('money_t').innerHTML = amount;
	}else{
		o('money_t').innerHTML = amount*num;
	}
}


if (!empty(o('amountobj'))){
	var obj1 = o('amountobj').getElementsByTagName("a");
	obj1[0].onclick = function(){
		if (o('amount').value > 2){
			o('amount').value = parseInt(o('amount').value) - 2;
			autoprice();
		}
	}
	obj1[1].onclick = function(){
		o('amount').value = parseInt(o('amount').value) + 2;
		autoprice();
	}
}
//
if (!empty(o('numobj'))){
	var obj2 = o('numobj').getElementsByTagName("a");
	obj2[0].onclick = function(){
		if (o('num').value > 1){
			o('num').value = parseInt(o('num').value) - 1;
			autoprice();
		}
	}
	obj2[1].onclick = function(){
		o('num').value = parseInt(o('num').value) + 1;
		autoprice();
	}
}
/*
ifolddiv = 0;
if (!empty(o('content')))o('content').onclick = function (){
	selectli('contentlist');
}
function selectli(obj){
	if (ifolddiv != 1)o('C_div').innerHTML = '';
	o('C_div').appendChild(o(obj));
	display(obj,1);
	ZEAI_div('auto',200,'选择一句祝福语');
	ifolddiv = 1;
	var divbox = o(obj).children;
	console.log(divbox);
	for (var i in divbox) {
		(function(i){
			divbox[i].onclick = function(){
				var text = divbox[i].innerHTML;
				o('content').value = text;
				ZEAI_div_close();
			}
		})(i);
	}
}

*/
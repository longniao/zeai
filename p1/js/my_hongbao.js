var nulltext = '不限';
var selstr  = '请上下滚动选择';
var selstr2 = '不限';
var sex_ARR = [
	{'i':'0','v':'性别不限'},
	{'i':'1','v':'男性朋友'},
	{'i':'2','v':'女性朋友'},
];
function hongbao_add(kind){
	zeai.ajax({url:PCHOST+'/my_hongbao'+zeai.ajxext+'submitok=ajax_chklogin'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag=='nologin'){
			zeai.msg(0);zeai.msg(rs.msg,{time:2});
			setTimeout(function(){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));},2000);
			return false;
		}else{
			if(kind=='out'){
				var url= PCHOST+'/my_hongbao'+zeai.ajxext+'submitok=add',w=900,h=550;
			}else if(kind=='in'){
				var url= PCHOST+'/my_hongbao'+zeai.ajxext+'submitok=add_in',w=500,h=480;
			}
			supdes=ZeaiPC.iframe({url:url,w:w,h:h});
		}
	});
}
function hongbao_btn_saveFn(){
	var m1 = get_option('m1','v');
	var m2 = get_option('m2','v');
	var m3 = get_option('m3','v');
	var m1t = get_option('m1','t');
	var m2t = get_option('m2','t');
	var m3t = get_option('m3','t');
	m1t = (nulltext == m1t)?'':m1t;
	m2t = (nulltext == m2t)?'':' '+m2t;
	m3t = (nulltext == m3t)?'':' '+m3t;
	m1 = (m1 == 0)?'':m1;
	m2 = (m2 == 0)?'':','+m2;
	m3 = (m3 == 0)?'':','+m3;
	var mate_areaid = m1 + m2 + m3;
	mate_areaid = (mate_areaid == '0,0,0')?'':mate_areaid;
	var mate_areatitle = m1t + m2t + m3t;
	o('areaid').value = mate_areaid;
	o('areatitle').value = mate_areatitle;
	var amount = parseInt(o('amount').value);
	var num    = parseInt(o('num').value);
	var kind;
	if (o('kind1').checked){
		kind = 1;
	}else{
		kind = 2;
	}
	zeai.ajax({url:PCHOST+'/my_hongbao'+zeai.ajxext+'submitok=ajax_chkmoney&amount='+amount+'&num='+num+'&t=3'+'&kind='+kind},function(e){rs=zeai.jsoneval(e);
		if(rs.flag == 1){
			zeai.ajax({url:PCHOST+'/my_hongbao'+zeai.extname,form:zeai_cn_FORM},function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg,{time:2});
				if(rs.flag == 1){
					setTimeout(function(){parent.location.reload(true);},2000);
				}
			});
		}else if(rs.flag=='nomoney'){
			zeai.msg(rs.msg,{time:2});
			setTimeout(function(){
				parent.zeai.openurl(PCHOST+'/my_money'+zeai.ajxext+'t=3&jumpurl='+encodeURIComponent(rs.jumpurl));
			},2000);
		}else{
			zeai.msg(0);zeai.msg(rs.msg);	
		}
	});

}
function hongbao_btn_in_saveFn(){
	var money = parseInt(o('money').value);
	zeai.ajax({url:PCHOST+'/my_hongbao'+zeai.extname,form:zeai_cn_FORM},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg,{time:2});
		if(rs.flag == 1){
			setTimeout(function(){parent.location.reload(true);},2000);
		}else if(rs.flag=='nophoto'){
			setTimeout(function(){parent.zeai.openurl(rs.jumpurl);},2000);
		}else{setTimeout(function(){parent.supdes.click();},2500);}
	});
}
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
window.onload=function(){
	if (!zeai.empty(o('kind1')))o('kind1').onclick = function(){
		o('amount_t').innerHTML = '红包总金额';
		o('amount').value = 20;
		o('money_t').innerHTML = 20;
	}
	if (!zeai.empty(o('kind2')))o('kind2').onclick = function(){
		o('amount_t').innerHTML = '单个金额';
		o('amount').value = 5;
		o('money_t').innerHTML = parseInt(o('amount').value)*parseInt(o('num').value);
	}
	if (!zeai.empty(o('amount')))o('amount').oninput = function (){autoprice();}
	if (!zeai.empty(o('num')))o('num').oninput = function (){autoprice();}
	if (!zeai.empty(o('amountobj'))){
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
	if (!zeai.empty(o('numobj'))){
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
}

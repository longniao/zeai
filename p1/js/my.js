function setA1() {
	var list_obj = zeai.tag(o("list1"),'li');
	for(var a=0;a<list_obj.length;a++) {
		(function(a){
			var a,uid;
			a   = list_obj[a].children[1];
			uid = a.getAttribute("value");
			a.onclick = function(){ZeaiPC.hi({uid:uid,btnobj:a,edstr:'已打招呼'});}
		})(a);
	}
}
function setA2() {
	var list_obj = zeai.tag(o("list2"),'li');
	for(var a=0;a<list_obj.length;a++) {
		(function(a){
			var li,a,id,ifhi,uid;
			li = list_obj[a];
			a  = li.children[1];
			uid = a.getAttribute("value");
			ifhi = a.getAttribute("ifhi");
			a.onclick = function(){
				if (ifhi == 1){
					ZeaiPC.chat(uid);
				}else if(ifhi == 0){
					ZeaiPC.hi({uid:uid,btnobj:a,edstr:'已打招呼'});
				}
			}
		})(a);
	}
}
setA1();setA2();
o('rematchlist').onclick = function (){
	o('list1').html(load8);
	zeai.ajax({js:0,url:PCHOST+'/my'+zeai.ajxext+'submitok=ajax_getmate_ulist'},function(e){var rs = e.split('|WWWzeaiCN|');
		var C = rs[0],flag = rs[1];
		o("list1").html(C);if (flag == 1)setA1();
	});
}
photoUp({
	btnobj:photo_s,
	url:PCHOST+"/my_info"+zeai.extname,
	submitok:"ajax_photo_s_up",
	_:function(rs){
		zeai.msg(0);zeai.msg(rs.msg);
		if (rs.flag == 1){
			ZeaiPC.iframe({url:PCHOST+"/u_photo_cut"+zeai.ajxext+'ifm=1&id='+uid+'&submitok=www___zeai__cn_inphotocut&tmpphoto='+rs.tmpphoto,w:640,h:550});
		}else{
			zeai.msg(rs.msg,{time:3});
			//zeai.alert('上传图片出错，请联系原作者QQ：797311');
		}
	}
});
setTimeout(function(){	zeai.ajax({loading:0,url:PCHOST+'/my'+zeai.ajxext+'submitok=ajax_tipnum_tb'});},1000);
o('sign').onclick = function(){sign(PCHOST+'/my'+zeai.extname);}
o('mask_sign').onclick = function(){ZEAI_signclose();}
o('signokbox').onclick = function(){ZEAI_signclose();}
function ZEAI_signclose(){zeai.showSwitch('mask_sign,signokbox');}
function sign(url){
	zeai.ajax({'url':url,'data':{submitok:'ajax_sign'}},function(e){rs=zeai.jsoneval(e);
		console.log(rs);
		if (rs.flag==1){
			zeai.showSwitch('mask_sign,signokbox,mask_gif');
			randloveb.html(rs.num);
			if(!zeai.empty(o('my_loveb_num')))o('my_loveb_num').html(parseInt(o('my_loveb_num').innerHTML)+parseInt(rs.num));
			signstr.html('已签到');
		}else{zeai.msg(rs.msg);}
		if (!zeai.empty(o('signbox')))o('signbox').remove();
	});	
}

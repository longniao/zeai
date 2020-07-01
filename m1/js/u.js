function UblackFn(){
	this.style.zIndex=999;
	document.body.append(this);
	zeai.mask({fobj:u,son:rtbox,close:function(e){
		Ublack.style.zIndex=3;u.append(Ublack);
	}});
}
function aboutusFn (){	ZeaiM.page.load(HOST+'/m1/u_data'+zeai.ajxext+'uid='+uid+'&i=u_aboutus','u','u_aboutus');}
function mydataFn(){ZeaiM.page.load(HOST+'/m1/u_data'+zeai.ajxext+'uid='+uid+'&i=u_data','u','u_data');}
function myumateFn (){	ZeaiM.page.load(HOST+'/m1/u_data'+zeai.ajxext+'uid='+uid+'&i=u_mate','u','u_mate');}
function photo_mFn(){
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_photo_s_zoom',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			ZeaiM.piczoom({browser:browser,b:photo_b,list:[photo_b]});
		}else{
			zeai.alertplus({'title':'请上传形象照','content':rs.msg,'title1':'取消','title2':'去上传','fn1':function(){zeai.alertplus(0);},
				'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_info'+zeai.ajxext+'a=data','u','my_info');}
			});
		}
	});
}
function PlistFn(pic_list){
	zeai.listEach(zeai.tag(Plist,'img'),function(obj){
		var b=obj.src.replace('_s','_b');
		obj.onclick=function(){
			zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_Plist_zoom',uid:uid}},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){
					ZeaiM.piczoom({browser:browser,b:b,list:pic_list});
				}else{
					zeai.alertplus({'title':'请上传个人相册','content':rs.msg,'title1':'取消','title2':'去上传',
						'fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_info'+zeai.ajxext+'a=photo','u','my_info');}
					});
				}
			});
		}
	});
}
function gzFn(){
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_gz',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			gz.addClass('ed');gz.html('已关注');
		}else{
			gz.removeClass('ed');gz.html('关注');
		}
		zeai.msg(rs.msg);
	});
}
function admuFn(kind,uid){zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_adm',kind:kind,uid:uid}},function(e){rs=zeai.jsoneval(e);zeai.msg(rs.msg);});}
function inBlackFn(){
	var self=this;
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_inblack',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			self.addClass('ed');gz.removeClass('ed');gz.html('关注');
		}else{
			self.removeClass('ed');
		}
		zeai.msg(rs.msg);
	});
}
function Fn315(){
	Ublack.style.zIndex=3;u.append(Ublack);u.append(rtbox);rtbox.hide();
	o('Mrtbox').remove();
	ZeaiM.page.load(HOST+'/m1/315'+zeai.ajxext+'uid='+uid,'u','jubao');
}
function loop_TBfn1(){ 
	var t = parseInt(this.scrollTop);
	if(t > 188){
		loop_TB.style.opacity = 1;
	}else{
		loop_TB.style.opacity = 0;
	}
}function loop_TBfn2(){ 
	Ubox.scrollTop=0;
}
function mycontactFn(){ZeaiM.page.load(HOST+'/m1/u_data'+zeai.ajxext+'uid='+uid+'&i=u_contact','u','u_contact');}
function hiFn(){
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_senddzh',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			hi.hide();
			tips0_100_0.html('<i class="ico hi">&#xe6bd;</i>招呼已发送');tips0_100_0.show();setTimeout(function(){tips0_100_0.hide()},2100);
		}else if(rs.flag=='nodata'){
			nodata('u') ;
		}else if(!zeai.empty(rs.flag)){
			ZeaiM.page.sorry(rs,'u');
		}else{
			zeai.msg(rs.msg);
		}
	});
}
function setgift(giftbtn,fobj,box){
	if(!zeai.empty(giftbtn)){giftbtn.onclick = function(){gift_ajaxdata(0,fobj,box);}}
	var li,id,rs,gid,div;
	zeai.listEach(zeai.tag(gift,'li'),function(li){li.onclick = function(){gift_ajaxdata(li.getAttribute("gid"),fobj,box);}});
}
function ucertFn(){ZeaiM.page.load(HOST+'/m1/u_data'+zeai.ajxext+'uid='+uid+'&i=u_cert','u','u_cert');}
function hnFn(){ZeaiM.div_up({fobj:u,obj:hnbox,h:360});}
function tgxqkFn(){
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_tgxqk',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			ZeaiM.page.load(HOST+'/m1/tg_my_ucard.php?submitok=tg_my_ucard_list&uid='+uid,'u','tg_my_ucard_list');
		}else{
			zeai.openurl(HOST+'/m1/tg_index.php');
		}
	});
}
function chcitFn(){
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_sendchcit',uid:uid}},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){setTimeout(function(){chcit.parentNode.remove();},2000);}
	});
}
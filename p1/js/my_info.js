/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
var bfbbgW=my_info_bfb.offsetWidth;
bfbbgW = bfbbgW/100;
var newbfbW= parseInt(bfbbgW*data_myinfobfb);
if((data_myinfobfb>=100)){
	newbfbW=(newbfbW-2);my_info_bfbbar.style.borderRadius='15px';
}
my_info_bfbbar.style.width=newbfbW+'px';
if(!zeai.empty(o('Zeai_map_btn')))Zeai_map_btn.onclick=function(){zeaimap=ZeaiPC.iframe({url:PCHOST+"/my_map"+zeai.extname,w:630,h:500});}
if(!zeai.empty(o('photo_s'))){
	photoUp({
		btnobj:photo_s,
		url:PCHOST+"/my_info"+zeai.extname,
		submitok:"ajax_photo_s_up",
		_:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			//jubaopic.html('<div class="photo_s" style="background-image:url(\''+up2+rs.dbname+'\')"><span>更换头像</span></div>');
			if (rs.flag == 1){
				ZeaiPC.iframe({url:PCHOST+"/u_photo_cut"+zeai.ajxext+'ifm=1&id='+uid+'&submitok=www___zeai__cn_inphotocut&tmpphoto='+rs.tmpphoto,w:640,h:550});
			}else{
				zeai.msg(rs.msg,{time:3});
				//zeai.alert('上传图片出错，请联系原作者QQ7144100');
			}
		}
	});
}
if(!zeai.empty(o('ewmys'))){
	photoUp({
		btnobj:ewmys,
		url:PCHOST+"/my_info"+zeai.extname,
		submitok:"ajax_weixin_pic_up",
		_:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){
				ewmys.html('<div class="photo_s" style="border-radius:0;background-image:url(\''+up2+rs.dbname+'\')"><span>更换二维码</span></div>');
			}else{
				zeai.alert('上传图片出错，请联系原作者QQ：797311');
			}
		}
	});
}

function chkform(t){
	if(t==1){
		if(zeai.empty(o('nickname').value)){
			zeai.msg('请输入昵称',o('nickname'));return false;
		}
		if(zeai.empty(o('heigh').value)){
			zeai.msg('请选择身高',o('heigh'));return false;
		}
	}else if(t==2){
		
	}else if(t==3){

	}else if(t==4){
		if(ifage){
		if (mate_age1.value > mate_age2.value && (!zeai.empty(mate_age1.value) && !zeai.empty(mate_age2.value)) ){
			zeai.msg('年龄请选择一个正确的区间（左小右大）',mate_age1);	
			return false;
		}}
		if(ifheigh){
		if (mate_heigh1.value > mate_heigh2.value && (!zeai.empty(mate_heigh1.value) && !zeai.empty(mate_heigh2.value)) ){
			zeai.msg('身高请选择一个正确的区间（左小右大）',mate_heigh1);	
			return false;
		}}
		if(ifweigh){
		if (mate_weigh1.value > mate_weigh2.value && (!zeai.empty(mate_weigh1.value) && !zeai.empty(mate_weigh2.value)) ){
			zeai.msg('体重请选择一个正确的区间（左小右大）',mate_weigh1);	
			return false;
		}}
	}else if(t==5){

	}
	zeai.ajax({url:PCHOST+'/my_info'+zeai.extname,form:ZeaiCnForm},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){
			if(!zeai.empty(rs.jumpurl))	{
				setTimeout(function(){zeai.openurl(rs.jumpurl);},1000);
			}else{
				setTimeout(function(){location.reload(true);},1000);
			}
		}
	});
}
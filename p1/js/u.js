function inBlackFn(){
	var self=this;
	zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_inblack',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			self.addClass('ed');self.html('已拉黑');gz.removeClass('ed');gz.html('<i class="ico">&#xe70f;</i>关注');
		}else{
			self.removeClass('ed');self.html('拉黑');
		}
		zeai.msg(rs.msg);
	});
}
function gzFn(){
	var self=this;
	zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_gz',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			self.addClass('ed');gz.html('<i class="ico">&#xe62f;</i>已关注');Ublack.removeClass('ed');Ublack.html('拉黑');
		}else{
			self.removeClass('ed');gz.html('<i class="ico">&#xe70f;</i>关注');
		}
		zeai.msg(rs.msg);
	});
}
Ublack.onclick=inBlackFn;
gz.onclick=gzFn;
if(!zeai.empty(o('photo_m')))o('photo_m').onclick=function(){ZeaiPC.piczoom(this.src.replace('_m.','_b.'));}
Fn315.onclick=function(){zeai.iframe('举报中心',PCHOST+'/315'+zeai.ajxext+'submitok=315&uid='+uid,600,450)}
function showaboutus(str){zeai.div({obj:aboutusALL,title:'<font style="color:#E83191">个人独白</font>',w:600,h:550});}
agree.onclick=function(){
	var self=this;
	zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_agree',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			if(!self.hasClass('ed')){
				self.addClass('ed');
				var font=self.lastChild;
				font.html(parseInt(font.innerHTML)+1);
				zeai.msg(rs.msg);
			}
		}
	});
}
//
if(ifphoto==1){
	if(!zeai.empty(pre))pre.onclick=preFn;
	if(!zeai.empty(next))next.onclick=nextFn;
	photoFn();
	var pw=0,liw=90;
	o(libox).css('width:'+liboxLenth()*liw+'px');
	var photo_totalP=Math.ceil(liboxLenth()/3);
}
function liboxLenth(){
	return libox.children.length;
}
function preFn(){
	if(pw==0)return false;
	pw = pw+liw;
	o(libox).css('transform:translate('+pw+'px)');
	photo_ed_center(-1);
}
function nextFn(){
	if(photo_totalP<=1)return false;
	var curP = Math.abs(pw);
	curP = parseInt(curP/(liw*3))+1;
	if(curP>=photo_totalP)return false;
	var totalW=liboxLenth()*liw;
	if(totalW-Math.abs(pw)<=270)return false;
	pw = pw-liw;
	o(libox).css('transform:translate('+pw+'px)');
	photo_ed_center(1);
}
function photo_ed_center(direction){
	var cobj;
	zeai.listEach(zeai.tag(libox,'li'),function(li,i){
		if(i==0 || i==1){cobj=li;li.class('ed');}
		if(li.hasClass('ed')){
			li.removeClass('ed');
			cobj=li;
		}
	});
	if(direction==1){
		if(!zeai.empty(cobj.nextElementSibling))cobj.nextElementSibling.class('ed');
	}else if(direction==-1){
		if(!zeai.empty(cobj.previousElementSibling))cobj.previousElementSibling.class('ed');
	}
}
function photoFn(){
	var listobj=zeai.tag(libox,'li');
	zeai.listEach(listobj,function(obj){
		var src=obj.getAttribute("src");src=src.replace('_m.','_s.');
		obj.style.backgroundImage="url("+src+")";
		obj.onclick=function(){
			zeai.listEach(listobj,function(li){li.class('');});
			this.class('ed');
			ZeaiPC.piczoom(src.replace('_s.','_b.'));
		}
	});
}
//
setTimeout(function(){uewm.src=HOST+'/sub/creat_ewm'+zeai.ajxext+'&url='+uhref;},500);
if(!zeai.empty(o('chat')))chat.onclick=function(){ZeaiPC.chat(uid);}
function hiFn(){
	zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_senddzh',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			hi.class('ed');hi.html('<i class="ico">&#xe628;</i><span>已打招呼</span>');hi.onclick=null;
			tips0_100_0.html('<i class="ico hi">&#xe6bd;</i>招呼已发送');tips0_100_0.show();setTimeout(function(){tips0_100_0.hide()},2100);
		}else if(rs.flag=='nodata'){
			nodata('u');
		}else if(!zeai.empty(rs.flag)){
			ZeaiPC.no(rs);
		}else{
			zeai.msg(rs.msg);
		}
	});
}
function mycontactFn(){
	zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_u_contact',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			contactbox.html(rs.C);
			zeai.div({obj:contactbox,title:'Ta的联系方法',w:440,h:450});
		}else if(rs.flag=='noucount'){
			zeai.div({obj:o('u_contact_daylooknumHelp'),title:rs.msg+'人',w:340,h:320});
		}else if(rs.flag=='nocontact'){
			zeai.alertplus({'title':'请完善联系方法','content':rs.msg,'title1':'取消','title2':'去完善','fn1':function(){zeai.alertplus(0);},
				'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_info'+zeai.extname);}
			});
		}else if(rs.flag=='nolevel'){
			zeai.div({obj:o('contact_levelHelp'),title:'会员级别不够',w:340,h:320});
		}else if(rs.flag=='clickloveb_confirm'){
			zeai.div({obj:o('u_contact_lovebHelp'),title:rs.title,w:340,h:320});
		}else if(rs.flag=='nodata'){
			//nodata('u') ;老6.6之前
			zeai.alertplus({'title':'----- 请完善个人资料 -----','content':rs.msg,'title1':'取消','title2':'去完善','fn1':function(){zeai.alertplus(0);},
				'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_info'+zeai.extname);}
			});
		}else if(rs.flag=='nophoto'){
			zeai.alertplus({'title':'----- 请先上传头像 -----','content':rs.msg,'title1':'取消','title2':'去上传','fn1':function(){zeai.alertplus(0);},
				'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_info'+zeai.extname);}
			});
		}else if(rs.flag=='nocert'){
			zeai.alertplus({'title':'-------- 诚信认证 --------','content':rs.msg,'title1':'取消','title2':'去认证','fn1':function(){zeai.alertplus(0);},
				'fn2':function(){zeai.alertplus(0);zeai.openurl_(PCHOST+'/my_cert'+zeai.extname);}
			});
		}else{
			zeai.msg(rs.msg);
		}
	});
}
function clickloveb(uid,kind){
	zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_clickloveb',uid:uid,kind:kind}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			contactbox.html(rs.C);
			div_close.click();zeai.div({obj:contactbox,title:'Ta的联系方法',w:440,h:450});
		}else if(rs.flag=='noloveb'){
			zeai.alertplus({'title':rs.title,'content':rs.msg,'title1':'取消','title2':'去充值','fn1':function(){zeai.alertplus(0);},
				'fn2':function(){
					zeai.openurl_(PCHOST+'/my_loveb'+zeai.ajxext+'a=cz&jumpurl='+encodeURIComponent(rs.jumpurl));
				}
			});
		}else{
			zeai.msg(rs.msg);
		}
	});
}
if (!zeai.empty(o('ip')))o('ip').onclick = function (){var url = o('ip').getAttribute("value");zeai.openurl_('http://www.baidu.com/s?wd='+url);}

window.onload = function () {
	//客服区
	I_photo_s.onclick=function(){zeai.openurl('./?z=my');};
	indexNkname.onclick=function(){zeai.openurl('./?z=my&e=my_info');};
	Ikefubtn.onclick=IkefuFn;
	zeai.listEach(zeai.tag(Irbtn,'a'),function(obj,i){
		obj.onclick=function(){
			IkefuFnMclose(o(IkefuM));
			var url;
			switch (i) {
				case 0:zeai.openurl('./?z=my&e=my_viewuser');break;
				case 1:zeai.openurl('./?z=my&e=my_follow');break;
				case 2:url={g:'m1/about'+zeai.ajxext+'t=us',l:'about_us'};setTimeout(function(){page(url)},18);break;
			}
			
		}
	});
	//
	iso.onclick=function(){page({g:'m1/search'+zeai.ajxext+'a=2',l:'search'});}
	pushindex_btn.onclick=function(){page({g:'m1/my_push_index'+zeai.extname,l:'my_push_index'});}
	main.onscroll = indexOnscroll;
	//partymore.onclick=function(){zeai.openurl('./?z=party');}
	//articlemore.onclick=function(){page({g:'m1/article'+zeai.extname,l:'article'});}
	
	if(!zeai.empty(o('partymore')))o('partymore').onclick=function(){zeai.openurl('./?z=party');}
	//if(!zeai.empty(o('articlemore')))o('articlemore').onclick=function(){page({g:'m1/article'+zeai.extname,l:'article'});}
	if(!zeai.empty(o('articlemore')))o('articlemore').onclick=function(){zeai.openurl(HOST+'/m1/article.php');}
	
	
	
/*	if(!zeai.empty(o('iarticlekind'))){
		zeai.listEach(zeai.tag(iarticlekind,'a'),function(obj){
			var kind = obj.getAttribute("kindid");
			obj.onclick=function(){
				ZeaiM.page.load('m1/article'+zeai.ajxext+'kind='+kind,ZEAI_MAIN,'article');
			}
		});
	}
	
*/	
}
function indexOnscroll(){backtopFn(o(main));}
btmKefuBtn.onclick=function(){
	ZeaiM.div({obj:btmKefuBox,w:260,h:280});
}
function ulink(uid,ifblur){
	//if(ifblur==1){
	//	page({g:'m1/my_vip'+zeai.ajxext+'&jumpurl='+HOST+'/?z=index',l:'my_vip'});
	//}else{
		page({g:'m1/u'+zeai.ajxext+'uid='+uid,l:'u'});
	//}
}

function IkefuFn(){
	var M=zeai.addtag('div');M.class('mask1 alpha0_100');M.id='IkefuM';document.body.append(M);
	M.append(Ikefu);
	Ikefu.removeClass('fadeInIkefuR');Ikefu.addClass('fadeInIkefuL')
	Ikefu.show();ZeaiM.fade({arr:['itop','nav','main'],num:'-40%'});
	M.onclick=IkefuFnMclose;
	M.addEventListener('touchmove', function(e){ e.preventDefault();});
	Ikefu.onclick = function(e){e.cancelBubble = true;}
}
function IkefuFnMclose(){
	var M=o(IkefuM);
	document.body.append(Ikefu);
	Ikefu.removeClass('fadeInIkefuL');Ikefu.addClass('fadeInIkefuR');
	M.removeClass('alpha0_100');M.addClass('alpha100_0');
	ZeaiM.fade({arr:['itop','nav','main'],num:0});
	setTimeout(function(){if (!zeai.empty(M)){M.remove();}},400);
}


function iu_btn1Fn(){
	indexT='tj';indexmore.html('更多优质会员');
	ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:this});
	zeai.ajax({url:'m1/index'+zeai.extname,data:{submitok:'ajax_tj'}},function(e){iubox.html(e);	});
}
function iu_btn2Fn(){
	indexT='fj';indexmore.html('<i class="ico">&#xe614;</i>更多离我最近的');
	ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:this});
	zeai.ajax({url:'m1/index'+zeai.extname,data:{submitok:'ajax_fj'}},function(e){
		if(e=='nologin'){
			zeai.openurl('m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(HOST))
		}else if(e=='nogpsdata'){
			if(browser=='wx'){
				wx.getLocation({
					type: 'wgs84',
					success: function (res) {
						var latitude = res.latitude,longitude= res.longitude;
						zeai.ajax({url:'m1/index'+zeai.extname,data:{submitok:'ajax_gps_save',latitude:latitude,longitude:longitude}},function(e){iubox.html(e);});
					},
					cancel: function (res) {ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:iu_btn1});zeai.msg('您已拒绝授权定位，将无法使用附近的人');},
					fail: function (res) {
						ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:iu_btn1});
						zeai.msg('请开启手机本身的GPS定位后并同意授权');
					}
				});
			}else if(browser=='app'){
				plus.geolocation.getCurrentPosition(function(p){
					zeai.ajax({url:'m1/index'+zeai.extname,data:{submitok:'ajax_gps_save',latitude:p.coords.latitude,longitude:p.coords.longitude}},function(e){iubox.html(e);});					
				}, function(e){
					alert('获取定位失败:' + e.message);
				});
			}else{
				ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:iu_btn1});
				zeai.msg('请使用微信打开并同意获取自己的定位');
			}
		}else{
			iubox.html(e);
		}
	});
}
function iu_btn3Fn(){
	indexT='vip';indexmore.html('更多VIP会员');
	ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:this});
	zeai.ajax({url:'m1/index'+zeai.extname,data:{submitok:'ajax_vip'}},function(e){iubox.html(e);	});
}
function iu_btn4Fn(){
	indexT='pp';indexmore.html('更多匹配我的');
	ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:this});
	zeai.ajax({url:'m1/index'+zeai.extname,data:{submitok:'ajax_pp'}},function(e){
		if(e=='nologin'){
			zeai.openurl('m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(HOST))
		}else if(e=='noauth'){
			zeai.msg('您是线下会员或帐号已被锁定');
		}else if(e=='nomate'){
			zeai.alertplus({title:'择友条件未设置',content:'只有自己设置完择偶条件才能使用',title1:'取消',title2:'去设置',
				'fn1':function(){zeai.alertplus(0);},
				'fn2':function(){zeai.alertplus(0);ZeaiM.page.load('m1/my_info'+zeai.ajxext+'a=mate&href=mate',ZEAI_MAIN,'my_info');}
			});
		}else{
			iubox.html(e);
		}
	});
}
function indexmoreFn(){
	if(iModuleU_bigmore==1){
		zeai.openurl(HOST+'/?z=index_more_big&t='+indexT);
	}else{
		ZeaiM.page.load({url:'m1/index_more'+zeai.extname,data:{t:indexT}},ZEAI_MAIN,'index_more_ulist');
	}
}
function iu_btn2_2Fn(){
	indexT='sex1';indexmore.html('更多优质男会员');
	ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:this});
	zeai.ajax({url:'m1/index'+zeai.extname,data:{submitok:'ajax_sex1'}},function(e){iubox.html(e);});
}
function iu_btn3_3Fn(){
	indexT='sex2';indexmore.html('更多优质女会员');
	ZeaiM.tabmenu.onclk({obj:tabmenuIndex,li:this});
	zeai.ajax({url:'m1/index'+zeai.extname,data:{submitok:'ajax_sex2'}},function(e){iubox.html(e);});
}

function ipartyFn(fid){
	ZeaiM.page.load({url:'m1/party_detail'+zeai.ajxext+'fid='+fid},ZEAI_MAIN,'party_detail');
}
//function iwzFn(id){page({g:'m1/article'+zeai.ajxext+'id='+id,l:'article_detail'});}
function iwzFn(id){zeai.openurl(HOST+'/m1/article_detail.php?id='+id);}
function iBannerFn(){
	var screenW=parseInt(screen.width)
	if(screenW>640)screenW=640;
	var W = parseInt(screenW-20),H=parseInt(W*0.4647);
	var oPic = o("pic_box").getElementsByTagName("li");
	for(var i=0;i<oPic.length;i++){
		oPic[i].style.width = W+'px';
		oPic[i].style.height= H+'px';
	}
	var zeaiad = new ScrollPic();
	zeaiad.scrollContId   = "pic_box";
	zeaiad.dotListId      = "focus_dot";
	zeaiad.dotOnClassName = "ed";
	zeaiad.frameWidth     = W;
	zeaiad.pageWidth      = W;
	zeaiad.upright        = false;
	zeaiad.speed          = 20;
	zeaiad.space          = 50;
	zeaiad.autoPlay       = true;
	zeaiad.initialize();
}
var zeai_iMarquee = function (id, tag, heigh, speed, delay) {
	var me = this;
	me.EL = document.getElementById(id);
	me.PA = 0;
	me.TI = null;
	me.LH = heigh;
	me.SP = speed;
	me.DY = delay;
	me.exec = function () {
		if (me.PA) return;
		me.EL.scrollTop += 2;
		if (me.EL.scrollTop % me.LH <= 1) {
			clearInterval(me.TI);
			me.EL.appendChild(me.EL.getElementsByTagName(tag)[0]);
			me.EL.scrollTop = 0;
			setTimeout(me.start, me.DY * 1000);
		}
	};
	me.start = function () {if (me.EL.scrollHeight - me.EL.offsetHeight >= me.LH) me.TI = setInterval(me.exec, me.SP);};
	//me.EL.onmouseover = function () { me.PA = 1 };
	//me.EL.onmouseout = function () { me.PA = 0 };
	setTimeout(me.start, me.DY * 1000);
};
function zeai_iMarqueeFn(){
	zeai.ajax({url:'m1/index'+zeai.ajxext+'submitok=ajax_iMarquee'},function(e){
		if(!zeai.empty(e)){
			iMarquee.html(e);
			setTimeout(function(){new zeai_iMarquee("iMarquee","li",30,50,1.5);},100);
		}
	});
}

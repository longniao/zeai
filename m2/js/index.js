var WTFarr = [];
function eq(element,index){
	var x,list;
	if (typeof(element) == "object"){list = element;}else if(typeof(element) == "string"){list = document.querySelectorAll(element);}
	if (!zeai.empty(list)){var listL=list.length;for(x=0;x<listL;x++){if(x==index)return list[x];}}
}
function waterfall_load(mainobj,e,p){
	var columns = 2;
	var div = zeai.addtag('div');div.id='p'+p;div.append(e);
	var newlist = zeai.tag(div,'a');
	mainobj.append(div);
	var l = newlist.length,m = 0;
	zeai.listEach(newlist,function(obj,i){
		//if(p>2){obj.style.top = 'auto';obj.style.left = 'auto';}
		obj.style.top = 'auto';obj.style.left = 'auto';
		var img=obj.firstChild;
		img.onload=function(){m++;if (m >= l)imgInit();}
		img.onerror=function(){this.src='res/photo_m'+obj.getAttribute("sex")+'.png';}
		gzHiInit(obj);
	});
	function imgInit(){
		zeai.listEach(newlist,function(obj,i){
			var w=obj.offsetWidth;
			var h=obj.offsetHeight;
			var aHeight = h;
//			if(p==2 && i<columns){
//				obj.style.top  = '0px';
//				obj.style.left = w*i+'px';
//				WTFarr.push(aHeight);
//			}else{
				var minHeight = WTFarr[0],index = 0,WTFarrL=WTFarr.length;
				for (var j = 0; j < WTFarrL; j++) {if (minHeight > WTFarr[j]){index = j;}}
				var minobj     = eq(zeai.tag(mainobj,'a'),index);
				obj.style.top  = WTFarr[index]+'px';
				obj.style.left = minobj.style.left;
				WTFarr[index] = WTFarr[index] + aHeight;
//			}
			//obj.addClass('alpha0_100');
			obj.addClass('fadeInUp');
			obj.firstChild.onclick=zeaiM_link;
		});
	}
}
function waterfall(mainobj){
	var columns = 2,ulist=zeai.tag(mainobj,'a');
	zeai.listEach(ulist,function(obj,i){
		var w=obj.offsetWidth,h=obj.offsetHeight,boxheight=h,gz,hi;
		if (i<columns){
			obj.style.top='0px';
			obj.style.left=w*i+'px';
			WTFarr.push(boxheight);
		}else{
			//找到数组最小高和索引
			var minHeight = WTFarr[0],index = 0,WTFarrL=WTFarr.length;
			for (var j = 0; j < WTFarrL; j++) {if (minHeight > WTFarr[j]) {index = j;}}
			//设置下一行的第一个盒子位置top值就是最小列高 
			var minobj    = eq(ulist,index);
			obj.style.top  = WTFarr[index]+'px';
			obj.style.left = minobj.style.left;
			//改最小列高 最小列高 = 当前自己高 + 拼接过来的高
			WTFarr[index] = WTFarr[index] + boxheight;
		}
		obj.addClass('small_big');
		obj.firstChild.onclick=zeaiM_link;
		gzHiInit(obj);
	});
}
function gzHiInit(obj){
	var uid2=obj.getAttribute("uid"),gz=zeai.tag(obj,'h5')[0].parentNode,hi=zeai.tag(obj,'h6')[0].parentNode;
	gz.onclick=function(){gzFnIndex(gz,uid2);}
	hi.onclick=function(){hiFnIndex(hi,uid2);}
}

function gzFnIndex(thiss,uid2){
	var ico=thiss.firstChild,gz=thiss.lastChild;
	zeai.ajax({url:'m1/u'+zeai.extname,js:1,data:{submitok:'ajax_gz',uid:uid2}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			ico.addClass('ed');gz.html('已关注');
		}else{
			ico.removeClass('ed');gz.html('关注');
		}
		zeai.msg(rs.msg,{time:1});
	});
}
function hiFnIndex(hi,uid2){
	var ico=hi.firstChild,histr=hi.lastChild;
	localStorage.uid=uid2;
	if(histr.innerHTML=='聊天'){
		ZeaiM.page.load('m1/msg_show'+zeai.ajxext+'uid='+uid2,ZEAI_MAIN,'msg_show');
	}else{
		zeai.ajax({url:'m1/u'+zeai.extname,js:1,data:{submitok:'ajax_senddzh',uid:uid2}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				ico.addClass('ed');histr.html('聊天');
				index_tips0_100_0.html('<i class="ico hi">&#xe6bd;</i>招呼已发送');index_tips0_100_0.show();setTimeout(function(){index_tips0_100_0.hide()},2100);
			}else if(rs.flag=='nodata'){
				nodata(ZEAI_MAIN);
			}else{
				zeai.msg(rs.msg);
			}
		});
	}
}

function zeaiM_link(){
	page({g:'m1/u'+zeai.ajxext+'uid='+this.parentNode.getAttribute("uid"),l:'u'});
}
zeaiLoadBack=['nav'];/*'topnav',*/
topnav.addEventListener('touchmove', function(e){e.preventDefault();});

function indexOnscroll(){
	var t = parseInt(o(indexUlist).scrollTop);
	var cH= parseInt(o(indexUlist).clientHeight);
	var  H= parseInt(o(indexUlist).scrollHeight);
	if (H-t-cH <128 && t>100){//t+cH==H
		if (p >= totalP){
			//zeai.msg('已达末页，全部加载结束');
			//o(indexUlist).onscroll = null;
			return false;
		}else{
			//p++;
			zeai.ajax({url:'m2/index'+zeai.ajxext+'e='+e,data:{submitok:'ajax_ulist',totalP:totalP,p:p}},function(e){
				if (e == 'end'){
					zeai.msg(0);zeai.msg('已达末页，全部加载结束');return false;
				}else{
					waterfall_load(indexUlist,e,p);
					//o('pstr').html(p);
					p++;
				}
		});}
	}
	if(t>88){
		o('topnav').style.top='-45px';
		o('main').style.top='50px';
	}else{
		o('topnav').style.top='0px';
		o('main').style.top='95px';
	}
	backtopFn(o(indexUlist));
}


function indexLoad(indexUlist) {
	var adom=zeai.tag(indexUlist,'a'),aL=adom.length,s=0;
	zeai.listEach(adom,function(obj,i){
		obj.firstChild.onload=function(){s++;if (s >= aL)indexInit(indexUlist);}
		obj.firstChild.onerror=function(){this.src='res/photo_m'+obj.getAttribute("sex")+'.png';}
	});		
}
function indexInit(indexUlist){
	WTFarr = [];p=2,waterfall(indexUlist);
}
window.onresize = function(){WTFarr = [];
	waterfall(indexUlist);
	ZeaiM.tabmenu.onclk({obj:tabmenu_index,li:index_btn});
}


/******************/

window.onload = function () {
	ZeaiM.tabmenu.init({obj:tabmenu_index,showbox:o('main')});
	index_btn.click();
	match_btn.onclick=function(){
		ZeaiM.tabmenu.onclk({obj:tabmenu_index,li:this});
		zeai.ajax({url:'m2/index'+zeai.ajxext+'e=mate',js:0},function(e){
			if(zeai.str_len(e)<100){
				rs=zeai.jsoneval(e);	
				if(rs.flag=='nologin'){zeai.openurl('m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));}else if(rs.flag=='nomate'){
					zeai.alertplus({'title':rs.title,'content':rs.msg,'title1':'取消','title2':'去设置',
						'fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);ZeaiM.page.load('m1/my_info'+zeai.ajxext+'a=mate&href=mate',ZEAI_MAIN,'my_info');}
					});
				}
			}else{o('main').html(e);ZeaiM.eval(e);}
		});
	}
	
	//客服区
	I_photo_s.onclick=function(){zeai.openurl('./?z=my&e=my_info');};
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
	pushindex_btn.onclick=function(){page({g:'m1/my_push_index'+zeai.extname,l:'my_push_index'});}
	indexso.onclick=function(){page({g:'m1/search'+zeai.extname,l:'search'});}
}


function IkefuFn(){
	var M=zeai.addtag('div');M.class('mask1 alpha0_100');M.id='IkefuM';document.body.append(M);
	M.append(Ikefu);
	Ikefu.removeClass('fadeInIkefuR');Ikefu.addClass('fadeInIkefuL')
	Ikefu.show();ZeaiM.fade({arr:['topnav','nav','main'],num:'-40%'});
	M.onclick=IkefuFnMclose;
	M.addEventListener('touchmove', function(e){ e.preventDefault();});
	Ikefu.onclick = function(e){e.cancelBubble = true;}
}
function IkefuFnMclose(){
	var M=o(IkefuM);
	document.body.append(Ikefu);
	Ikefu.removeClass('fadeInIkefuL');Ikefu.addClass('fadeInIkefuR');
	M.removeClass('alpha0_100');M.addClass('alpha100_0');
	ZeaiM.fade({arr:['topnav','nav','main'],num:0});
	setTimeout(function(){if (!zeai.empty(M)){M.remove();}},400);
}




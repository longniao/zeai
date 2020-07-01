function shop_gzFn(){
	zeai.ajax({url:'shop_detail'+zeai.extname,js:1,data:{submitok:'ajax_gz',id:id}},function(e){rs=zeai.jsoneval(e);
		zeai.msg(rs.msg);
		if(rs.flag==1){
			shop_gzbtn.addClass('ed');shop_gzbtn.html('<i class="ico">&#xe604;</i> 取消收藏');fsbox.html(html_decode(rs.list));
		}else{
			shop_gzbtn.removeClass('ed');shop_gzbtn.html('<i class="ico">&#xe620;</i> 收藏');fsbox.html(html_decode(rs.list));
		}
	});
}
function indexOnscroll(){
	var tt = zeai.getScrollTop(),cH= parseInt(document.documentElement.clientHeight),sH= parseInt(document.body.scrollHeight);//cH=document.documentElement.clientHeight;
	//console.log('sH='+sH+'　tt='+tt+'　cH='+cH+'　　sH-tt-cH＝'+(sH-tt-cH));
	if ((sH-tt-cH) <100){
		if (p > ZEAI_totalP){
			return false;
		}else{
			zeai.ajax({url:'shop_detail'+zeai.extname,data:{submitok:'ZEAI_list',ZEAI_totalP:ZEAI_totalP,p:p,id:id}},function(e){
			if (e == 'end'){
				zeai.msg(0);zeai.msg('已达末页，全部加载结束');return false;
			}else{
				ZEAI_load(ZEAI_list,e,p);p++;
			}
		});}
	}
	backtopFn();
}
function ZEAI_load(mainobj,e,p){
	mainobj.append(e);
	var newlist = mainobj.getElementsByClassName('p'+p);
	ZEAI_init(newlist);
}
function ZEAI_init(newlist){
	zeai.listEach(newlist,function(img){
		var oldSrc = img.src;
		img.src = HOST+'/res/loading.gif';
		img.onload=function(){this.src=oldSrc}
	});
}
function shop_yuyueFn(){ZeaiM.div_up({obj:shop_yuyue,h:370});}
function rettop(){zeai.setScrollTop(0);}
shop_yuyue_btn.onclick=shop_yuyueFn;
yuyueaddbtn.onclick=function(){
	if(zeai.empty(truename.value)){
		zeai.msg('请输入您的【姓名】');	
		return false;
	}
	if(zeai.empty(mob.value)){
		zeai.msg('请输入【联系电话或微信】');	
		return false;
	}
	zeai.ajax({url:'shop_detail'+zeai.extname,form:zeai_form},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){setTimeout("div_up_close.click()",1000);}
	});
}

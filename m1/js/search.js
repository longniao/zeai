function search_chkform(){
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
	o('mate_areaid').value = mate_areaid;
	o('mate_areatitle').value = mate_areatitle;
	if (mate_age1.value > mate_age2.value && (!zeai.empty(mate_age1.value) && !zeai.empty(mate_age2.value)) ){
		zeai.msg('年龄请选择一个正确的区间（左小右大）',mate_age1);	
		return false;
	}
	if (mate_heigh1.value > mate_heigh2.value && (!zeai.empty(mate_heigh1.value) && !zeai.empty(mate_heigh2.value)) ){
		zeai.msg('身高请选择一个正确的区间（左小右大）',mate_heigh1);	
		return false;
	}
	ZeaiM.page.load({url:'m1/search'+zeai.extname,form:www_zeai__cn_FORM,data:{submitok:'ulist'}},'search','search_ulist');
}
function search_btnFn(){if(!zeai.empty(keyword.value)){
	ZeaiM.page.load({url:'m1/search'+zeai.extname,data:{submitok:'ulist',keyword:keyword.value,t:3}},'search','search_ulist');
}else{zeai.msg('请输入搜索内容');}}

/*********************/
var WtfSarr = [];
function eq_search(element,index){
	var x,list;
	if (typeof(element) == "object"){list = element;}else if(typeof(element) == "string"){list = document.querySelectorAll(element);}
	if (!zeai.empty(list)){for(x=0;x<list.length;x++){if(x==index)return list[x];}}
}
function waterfallSearch_load(mainobj,e){
	var div = zeai.addtag('div');div.id='p_so'+p_so;div.append(e),newlist = zeai.tag(div,'a');
	mainobj.append(div);
	zeai.listEach(newlist,function(obj,i){
		obj.onclick=Slink;
	});
}
function waterfallSearch(mainobj){
	var ulist=zeai.tag(mainobj,'a');
	zeai.listEach(ulist,function(obj,i){
		obj.onclick=Slink;
	});
}
function Slink(){page({g:'m1/u'+zeai.ajxext+'uid='+this.getAttribute("uid"),y:'search_ulist',l:'u'});}
window.onresize = function(){WtfSarr = [];
	if(!zeai.empty(o('searchUlist'))){setTimeout(function(){waterfallSearch(searchUlist);},500);}
	if(!zeai.empty(o('main'))){setTimeout(function(){index_btn.click();},666);}
}
function searchInit(){WtfSarr = [];p_so=2,waterfallSearch(o('searchUlist'));}//waterfallSearch
function searchOnscroll(){
	var t = parseInt(o(searchUlist).scrollTop);
	var cH= parseInt(o(searchUlist).clientHeight);
	var  H= parseInt(o(searchUlist).scrollHeight);
	if (H-t-cH <128 && t>100){//t+cH==H
		if (p_so > totalP_so){
			o(searchUlist).onscroll = null;
			zeai.msg('已达末页，全部加载结束');
		}else{
			//p_so++;
			setTimeout(function(){
				var postjson = {submitok:'ajax_ulist',totalP:totalP_so,p:p_so};
				if(!zeai.empty(scs))Object.assign(postjson,tojson(scs.split('&')));
				zeai.ajax({'url':'m1/search'+zeai.extname,data:postjson},function(e){
				if (e == 'end'){zeai.msg(0);zeai.msg('已达末页，全部加载结束');}else{waterfallSearch_load(o(searchUlist),e);p_so++;}
			},1000);
		});}
	}
	function tojson(arr){
		var theRequest = new Object();
		var L=arr.length;
		for (var i = 0; i < L; i++) {
			var kye = arr[i].split("=")[0]
			var value = arr[i].split("=")[1]
			theRequest[kye] = value
		}
		return theRequest;
	}
}
/*function searchLoad() {
	var adom=zeai.tag(searchUlist,'a'),aL=adom.length,s=0;
	zeai.listEach(adom,function(obj,i){
		obj.firstChild.onload=function(){s++;if (s >= aL)searchInit();}
		obj.firstChild.onerror=function(){this.src='res/photo_m'+obj.getAttribute("sex")+'.png';}
	});		
}
*/
function so1Fn(box){
	zeai.listEach(zeai.tag(box,'li'),function(li){
		li.onclick=function(){ZeaiM.page.load({url:'m1/search'+zeai.extname,data:{submitok:'ulist',t:1,sokind:li.getAttribute("name")}},'search','search_ulist');}
	});
}



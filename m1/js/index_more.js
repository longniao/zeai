/*******WWW.ZEAI.CN V6.0**************/
var WtfIarr = [];
function eq_index_more(element,index){
	var x,list;
	if (typeof(element) == "object"){list = element;}else if(typeof(element) == "string"){list = document.querySelectorAll(element);}
	if (!zeai.empty(list)){for(x=0;x<list.length;x++){if(x==index)return list[x];}}
}
function waterfallIndex_more_load(mainobj,e){
	var div = zeai.addtag('div');div.id='i_so'+i_so;div.append(e),newlist = zeai.tag(div,'a');
	mainobj.append(div);
	zeai.listEach(newlist,function(obj){
		obj.onclick=Ilink;
	});
}
function waterfallIndex_more(mainobj){
	var ulist=zeai.tag(mainobj,'a');
	zeai.listEach(ulist,function(obj,i){
		obj.onclick=Ilink;
	});
}
function Ilink(){page({g:'m1/u'+zeai.ajxext+'uid='+this.getAttribute("uid"),y:'index_more_ulist',l:'u'});}
window.onresize = function(){WtfIarr = [];
	if(!zeai.empty(o('index_moreUlist'))){setTimeout(function(){waterfallIndex_more(o('index_moreUlist'));},500);}
}
function index_moreInit(){WtfIarr = [];i_so=2,waterfallIndex_more(o('index_moreUlist'));}//waterfallIndex_more
function index_moreOnscroll(){
	var t = parseInt(o(index_moreUlist).scrollTop);
	var cH= parseInt(o(index_moreUlist).clientHeight);
	var  H= parseInt(o(index_moreUlist).scrollHeight);
	if (H-t-cH <128 && t>100){//t+cH==H
		if (i_so > totalP_i){
			o(index_moreUlist).onscroll = null;
			zeai.msg('已达末页，全部加载结束');
		}else{
			//i_so++;
			setTimeout(function(){
				var postjson = {submitok:'ajax_ulist',totalP:totalP_i,p:i_so};
				if(!zeai.empty(scs))Object.assign(postjson,tojson(scs.split('&')));
				zeai.ajax({'url':'m1/index_more'+zeai.extname,data:postjson},function(e){
				if (e == 'end'){zeai.msg(0);zeai.msg('已达末页，全部加载结束');}else{
					//setTimeout(function(){waterfallIndex_more_load(o(index_moreUlist),e);i_so++;},100);
					waterfallIndex_more_load(o(index_moreUlist),e);i_so++;
				}
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
/*function index_moreLoad() {
	var adom=zeai.tag(index_moreUlist,'a'),aL=adom.length,s=0;
	zeai.listEach(adom,function(obj,i){
		obj.firstChild.onload=function(){
			s++;if (s >= aL){setTimeout(function(){index_moreInit();},100);}
		}
		obj.firstChild.onerror=function(){this.src='res/photo_m'+obj.getAttribute("sex")+'.png';}
	});		
}
*/
//function index_more_ppbtnFn(){page({g:'m1/u'+zeai.ajxext+'a=mate',y:'index_more_ulist',l:'my_info'});}
function setList(list){zeai.listEach(zeai.tag(list,'li'),function(li){setList_li(li)});}
function listOnscroll(){
	var t = parseInt(o('main').scrollTop);
	var cH= parseInt(o('main').clientHeight);
	var  H= parseInt(o('main').scrollHeight);
	if (H-t-cH <128 && t>100){
		if (p > totalP){
			o('main').onscroll = null;
			zeai.msg('已达末页，全部加载结束');
		}else{
			var postjson = {submitok:'ajax_list',totalP:totalP,p:p,t:t};
			zeai.ajax({'url':'m1/party'+zeai.extname,data:postjson},function(e){
			if (e == 'end'){
				zeai.msg(0);zeai.msg('已达末页，全部加载结束');
			}else{
				o('list').append('<div id="p'+p+'">'+e+'</div>');
				var dllist=zeai.tag(o('p'+p),'li'),l;
				l=dllist.length;
				for(var k=0;k<l;k++){setList_li(dllist[k]);}
				p++;
			}
		});}
	}
	backtopFn(o('main'));
}
//function setList_li(li){(function(li){li.onclick = function (){ZeaiM.page.load('m1/party_detail'+zeai.ajxext+'fid='+li.getAttribute("clsid"),ZEAI_MAIN,'party_detail');}})(li);}
topminibox.addEventListener('touchmove', function(e){e.preventDefault();});
nav.addEventListener('touchmove', function(e){e.preventDefault();});
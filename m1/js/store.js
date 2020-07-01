zeaiLoadBack=['nav','store_title'];
function storeOnscroll(){
	var t = parseInt(o('main').scrollTop);
	var cH= parseInt(o('main').clientHeight);
	var  H= parseInt(o('main').scrollHeight);
	if (H-t-cH <128 && t>100){//t+cH==H
		if (p > totalP){
			o(main).onscroll = null;
			zeai.msg('已达末页，全部加载结束');
		}else{
			var postjson = {submitok:'ajax_ulist',kind:kind,totalP:totalP,p:p};
			zeai.ajax({'url':HOST+'/m1/store'+zeai.extname,data:postjson},function(e){
			if (e == 'end'){
				zeai.msg(0);zeai.msg('已达末页，全部加载结束');
			}else{
				o('list').append('<div id="p'+p+'">'+e+'</div>');
				p++;
			}
		});}
	}
	backtopFn(o('main'));
}
nav.addEventListener('touchmove', function(e){e.preventDefault();});
function storedetail(id){page({g:HOST+'/m1/store_detail.php?e=store_kind1&a='+id,l:'store_kind1'});}
function storedetail2(id){page({g:HOST+'/m1/store_detail.php?e=store_kind2&a='+id,l:'store_kind2'});}
function store_gzFn(){
	zeai.ajax({url:HOST+'/m1/store_detail'+zeai.extname,js:1,data:{submitok:'ajax_gz',a:a}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			store_gzbtn.addClass('ed');store_gzbtn.html('<i class="ico">&#xe604;</i> 取消关注');
			store_gznum.html(parseInt(store_gznum.innerHTML)+1);
		}else{
			store_gzbtn.removeClass('ed');store_gzbtn.html('<i class="ico">&#xe620;</i> 加关注');
			store_gznum.html(parseInt(store_gznum.innerHTML)-1);
		}
		fsbox.html(html_decode(rs.list));
		zeai.msg(rs.msg);
	});
}

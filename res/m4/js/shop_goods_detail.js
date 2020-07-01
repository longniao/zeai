function goodsshareFn(){ZeaiM.div_up({obj:goodssharebox,h:150});}
wxshare.onclick = function(){div_up_close.click();zeai.showSwitch('share_mask,share_box');}
share_mask.onclick = function(){zeai.showSwitch('share_mask,share_box');}
share_box.onclick = function(){zeai.showSwitch('share_mask,share_box');}
function copy(str) {
	zeai.copy(str,function(){
		zeai.msg('复制成功');div_up_close.click();	
	})
}
function Zeai_makecardFn() {
	card_detail.show();
	zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在生成',{time:10})
	html2canvas(cardcontent).then(function(canvas) {
		var cW=canvas.width,cH=canvas.height;
		var img = Canvas2Image.convertToImage(canvas,cW, cH);
		if(is_h5app()){
			zeai.msg(0);
			app_save_share({durl:HOST,data:img.src,uid:0});					
		}else{
			cardbox_view.html('');
			img.style.width='80%';
			img.id='fximgs';
			cardbox_view.append(img);
			img.onload=function(){
				zeai.msg(0);
				setTimeout(function(){
					div_up_close.click();
					ZeaiM.div_pic({fobj:o(card_detail),obj:cardbox_view,title:'',w:cardbox_view.offsetWidth,h:cardbox_view.offsetHeight,fn:function(){
						card_detail.hide();
						o('cardbox_view').html('');
					}});
					zeai.msg('长按图片保存',{time:3});							
				},200);
			}
		}
	});
}
cardcontent.addEventListener('touchmove',function(e) {e.preventDefault();});
zeai_haibaobtn.onclick=Zeai_makecardFn;
goodsshare.onclick = goodsshareFn;
function shop_gzFn(){
	zeai.ajax({url:'shop_goods_detail'+zeai.extname,js:1,data:{submitok:'ajax_gz',id:id,tguid:tguid}},function(e){rs=zeai.jsoneval(e);
		zeai.msg(rs.msg);
		if(rs.flag==1){
			shop_gzbtn.addClass('ed');shop_gzbtn.html('<i class="ico">&#xe604;</i> 取消收藏');
		}else{
			shop_gzbtn.removeClass('ed');shop_gzbtn.html('<i class="ico">&#xe620;</i> 收藏');
		}
	});
}
shop_buy_btn.onclick=function(){
	if(stock<=0){zeai.alert('库存不足，无法下单');return false;}
	zeai.ajax({url:'shop_goods_detail'+zeai.extname,js:1,data:{submitok:'ajax_buy',id:id,tguid:tguid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			ZeaiM.div_up({obj:buybox,h:340});
		}else{
			zeai.msg(rs.msg);	
		}
	});
}
shop_my_buybox_btn.onclick=function(){
	zeai.ajax({url:'shop_goods_detail'+zeai.extname,js:1,form:Zeai__cn_formP},function(e){rs=zeai.jsoneval(e);
		zeai.msg(rs.msg);
		if(rs.flag==1)zeai.openurl('shop_goods_detail_pay.php?oid='+rs.oid);
	});
}
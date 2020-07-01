function Chide(){
	var idarr = ['party_detail_C1','party_detail_C2','party_detail_C3','party_detail_C4'];
	for(var i=0;i<idarr.length;i++){o(idarr[i]).hide();}
}	
function PTuA(uid){
	//ZeaiM.page.load(HOST+'/m1/u'+zeai.ajxext+'uid='+uid,'party_detail','u');
	zeai.openurl(HOST+'/m1/u.php?uid='+uid+'&m=wap');
}
function party_detail1btnFn(){
	ZeaiM.tabmenu.onclk({obj:party_detail_nav,li:this});
	Chide();party_detail_C1.show();
}
function party_detail2btnFn(){
	ZeaiM.tabmenu.onclk({obj:party_detail_nav,li:this});
	Chide();party_detail_C2.show();
}
function party_detail3btnFn(){
	ZeaiM.tabmenu.onclk({obj:party_detail_nav,li:this});
	Chide();party_detail_C3.show();
}
function party_detail4btnFn(){
	ZeaiM.tabmenu.onclk({obj:party_detail_nav,li:this});
	Chide();party_detail_C4.show();
}
function party_kefubtnFn(){page({g:HOST+'/m1/about.php?t=contact',y:'party_detail',l:'about_contact'})}
function party_detail_bm_btnFn(){
	var mobV = o('mob').value,verifyV=o('verify').value;
	if(!zeai.ifmob(mobV)){zeai.msg('请输入正确【手机号码】',o('mob'));return false;}
	if(!zeai.ifint(verifyV) || zeai.str_len(verifyV)!=4 ){zeai.msg('请输入【手机验证码】',o('verify'));return false;}
	var truenameV=o('truename').value;
	if(zeai.str_len(truenameV) < 2 || zeai.str_len(truenameV)>12){zeai.msg('请输入【真实姓名】',{time:3,focus:o('truename')});return false;}
	if(!zeai.form.ifradio('sex')){zeai.msg('请选择您的【性别】');return false;}
	if(zeai.str_len(o('birthday').value) !=4 ){zeai.msg('请输入【出生年份，如：1992】',{time:3,focus:o('birthday')});return false;}
	ZeaiM.confirmUp({title:'确定信息无误提交报名么？',cancel:'取消',ok:'确定',okfn:function(){
	zeai.ajax({url:HOST+'/m1/party_detail'+zeai.extname,form:Www_Zeai_cn_PartyBm},function(e){rs=zeai.jsoneval(e);
		zeai.msg(rs.msg);
		console.log(rs);
		if(rs.flag==1)ZeaiM.page.load(HOST+'/m1/party_detail'+zeai.ajxext+'submitok=ajax_bm_add_update_pay&fid='+rs.fid,'party_detail_bm','party_detail_bm_pay');
	});}});	
}
function htmlspecialchars_decode(str){
  str = str.replace(/&amp;/g, '&'); 
  str = str.replace(/&lt;/g, '<');
  str = str.replace(/&gt;/g, '>');
  str = str.replace(/&quot;/g, "'");  
  str = str.replace(/&#039;/g, "'");  
  return str;  
}
function party_bbs_btnFn(){
	ZeaiM.confirmUp({title:'确定要发表么？',cancel:'取消',ok:'确定',okfn:function(){
	zeai.ajax({url:HOST+'/m1/party_detail'+zeai.extname,form:Www_Z_e_a_i_C_n_Party},function(e){rs=zeai.jsoneval(e);
		zeai.msg(rs.msg);
		if(rs.flag==1){
			party_bbsbox.insertAdjacentHTML('afterbegin',htmlspecialchars_decode(rs.list));
			bbsnum.html(parseInt(bbsnum.innerHTML)+1);
			content.value='';
			bbsnum.removeClass('nodata');
		}else if(rs.flag=='nologin'){
			setTimeout(function(){zeai.openurl(HOST+'/m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl))},2000)
		}
	});}});	
}
function party_bmbtnFn(fid){
	zeai.ajax({url:HOST+'/m1/party_detail'+zeai.ajxext+'submitok=ajax_bm_ifpay&fid='+fid},function(e){rs=zeai.jsoneval(e);
		if(rs.ifpay==1){
			ZeaiM.page.load(HOST+'/m1/party_detail'+zeai.ajxext+'submitok=ajax_bm_add_update_pay&fid='+fid,ZEAI_MAIN,'party_detail_bm_pay');
		}else{
			ZeaiM.page.load(HOST+'/m1/party_detail'+zeai.ajxext+'submitok=ajax_bm_add&fid='+fid,ZEAI_MAIN,'party_detail_bm');
		}
	});
}
main.onscroll = function(){backtopFn(o(main));}
function partyshareFn(){ZeaiM.div_up({fobj:ZEAI_MAIN,obj:partysharebox,h:150});}
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
ZeaiM.tabmenu.init({obj:party_detail_nav});
setTimeout(function(){party_detail1btn.click();},200);
party_detail1btn.onclick=party_detail1btnFn;
party_detail2btn.onclick=party_detail2btnFn;
party_detail3btn.onclick=party_detail3btnFn;
party_detail4btn.onclick=party_detail4btnFn;
party_bmbtn.onclick=function(){party_bmbtnFn(fid)};
party_bbsbtn.onclick=function(){party_detail4btn.click();content.focus();}
party_kefubtn.onclick=function(){party_detail1btn.click();
	setTimeout(function(){main.scrollTop = 99999;},100);}
party_bbs_btn.onclick=party_bbs_btnFn;
partyshare.onclick = partyshareFn;

function yzmtimeFn(countdown) { 
	if (countdown == 0) {
		yzmbtn.removeClass('disabled');
		yzmbtn.html('<font>重新获取</font>'); 
		return false;
	} else { 
		if (!zeai.empty(o('yzmbtn'))){
			yzmbtn.addClass('disabled');
			yzmbtn.html('<b>'+countdown + "S</b>后重新发送"); 
			countdown--; 
		}
	} 
	cleandsj=setTimeout(function(){yzmtimeFn(countdown)},1000);
}

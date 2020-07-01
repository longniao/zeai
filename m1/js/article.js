if(ifbbs==1){
share.onclick = function(){zeai.showSwitch('share_mask,share_box');}
share_mask.onclick = function(){zeai.showSwitch('share_mask,share_box');}
share_box.onclick = function(){zeai.showSwitch('share_mask,share_box');}
zeai.listEach('.agree',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("clsid")),f=obj.firstChild,i=obj.lastChild;
		if(!obj.hasClass('ed')){
			zeai.ajax({url:HOST+'/m1/article_detail'+zeai.extname,data:{submitok:'ajax_agree',id:id}},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);obj.addClass('ed');f.html(parseInt(f.innerHTML)+1);
			});
		}
	}
});
bbsaddbtnsave.onclick=function(){
	content.value=zeai.clearhtml(content.value);
	if(zeai.empty(content.value) || zeai.str_len(content.value)>500){zeai.msg('请输入评论内容1~500字');return false;}
	zeai.ajax({url:HOST+'/m1/article_detail'+zeai.extname,form:zeai_wz_bbs_form},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);setTimeout(function(){zeai.setScrollTop(9999);},1500);}else if(rs.flag=='nologin'){setTimeout(function(){zeai.openurl(HOST+'/m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl))},1000);}
	});
}
bbsaddbtn.onclick=bbsaddbtnFn
bbsaddbtn2.onclick=bbsaddbtnFn
function bbsaddbtnFn(){
	zeai.ajax({url:HOST+'/m1/article_detail'+zeai.extname,data:{submitok:'ajax_iflogin',id:id}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			zeai.div({obj:zeai_wz_bbs_form,title:'随便说点什么',w:300,h:240});
		}else if(rs.flag=='nologin'){
			zeai.openurl(HOST+'/m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));
		}
	});
}}
if(ifpay==1){
	agreebtn.onclick=function(){
		zeai.ajax({url:HOST+'/m1/article_detail'+zeai.extname,data:{submitok:'ajax_iflogin',id:id}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				zeai.div({obj:detail_agree_pay,title:'选择赞赏金额',w:300,h:270});
			}else if(rs.flag=='nologin'){
				zeai.openurl(HOST+'/m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));
			}
		});
	}	
	zeai.listEach(zeai.tag(detail_agree_pay,'li'),function(obj){
		obj.onclick = function(){
			var money = obj.firstChild;money = parseFloat(money.innerHTML);
			zeai_PAY({money:money,paykind:'wxpay',kind:8,tmpid:id,title:title+'【赞赏】',return_url:return_url,jumpurl:jumpurl});
		}
	});
}
window.onscroll = function(){backtopFn();}
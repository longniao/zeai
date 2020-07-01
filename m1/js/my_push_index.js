function my_push_index_sbmtbtnFn(){
	ZeaiM.confirmUp({title:'确定提交置顶排名么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'m1/my_push_index'+zeai.extname,data:{submitok:'ajax_modupdate'}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				my_push_s0btn.click();my_push_idxmc.html(1);my_push_idxlovb.html(parseInt(my_push_idxlovb.innerHTML)-push_indexnum)
			}else if(rs.flag=='noloveb'){
				
				zeai.alertplus({'title':'余额不足','content':rs.msg,'title1':'取消','title2':'去充值','fn1':function(){zeai.alertplus(0);},
					'fn2':function(){zeai.alertplus(0);ZeaiM.page.load('m1/my_loveb'+zeai.ajxext+'a=cz&jumpurl='+encodeURIComponent(rs.jumpurl),my_push_index,'my_loveb');}
				});
				
			}else{
				zeai.msg(rs.msg);	
			}
		});
	}});
}
function my_push_indexAfn(){zeai.listEach(zeai.tag(my_push_index_list,'a'),function(a){a.onclick=function(){ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+this.getAttribute('uid'),'my_push_index','u');}});}







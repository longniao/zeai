/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2020/03/28 by supdes
*/
function shop_tipFn(){
    zeai.listEach(zeai.tag(shop_tipbox,'dt'),function(dt){
		var tid=dt.getAttribute("tid");
        var dd=dt.nextElementSibling;
		var dl=dt.parentNode,del=dl.lastElementChild;
        dl.onclick=function(){
			var b=dt.children[1];
			if(!zeai.empty(b))b.remove();
			zeai.openurl('shop_my_tip'+zeai.ajxext+'submitok=shop_tip_detail&tid='+tid);
        }
		shop_tiptouchDel(dl,dd);
		del.onclick = function(e){e.cancelBubble = true;dl.remove();zeai.ajax({url:'shop_my_tip'+zeai.ajxext+'submitok=ajax_MSG_del',data:{tid:tid}});}
    });
}
function shop_tiptouchDel(dl,dd) {
	var x,xy;
	function mstart(e){x = e.changedTouches[0].clientX;}
	function mmove(e){dlreset();
		var endx = e.changedTouches[0].clientX;
		xy = x - endx;
		if( xy > 30){ZeaiM.fade({arr:[dl],num:'-70px'});
		}else if( xy < -1){ZeaiM.fade({arr:[dl],num:'0'});}
	}
	dd.addEventListener('touchstart',mstart);
	dd.addEventListener('touchmove',mmove);	
	function dlreset(){zeai.listEach(zeai.tag(shop_tipbox,'dl'),function(dl){ZeaiM.fade({arr:[dl],num:'0'});});}
}
function shop_touchDelFn(objbox,url){
    zeai.listEach(zeai.tag(objbox,'dt'),function(dt){
		var tid=dt.getAttribute("tid"),dd=dt.nextElementSibling,dl=dt.parentNode,del=dl.lastElementChild;
		shop_touchDelFn_(objbox,dl,dd);
		del.onclick = function(e){e.cancelBubble = true;dl.remove();zeai.ajax({url:url,data:{tid:tid}});}
    });
}
function shop_touchDelFn_(objbox,dl,dd) {
	var x,xy;
	function mstart(e){x = e.changedTouches[0].clientX;}
	function mmove(e){dlreset();
		var endx = e.changedTouches[0].clientX;
		xy = x - endx;
		if( xy > 30){ZeaiM.fade({arr:[dl],num:'-70px'});
		}else if( xy < -1){ZeaiM.fade({arr:[dl],num:'0'});}
	}
	dd.addEventListener('touchstart',mstart);
	dd.addEventListener('touchmove',mmove);	
	function dlreset(){zeai.listEach(zeai.tag(objbox,'dl'),function(dl){ZeaiM.fade({arr:[dl],num:'0'});});}
}
function shop_my_goodsFn(id,kind){
	if(kind=='del'){ZeaiM.confirmUp({title:'确定要删除么？',cancel:'取消',ok:'确定',okfn:function(){ajaxGoodsFn();}});	}else{ajaxGoodsFn();}
	function ajaxGoodsFn(){
		if(kind=='mod'){zeai.openurl('shop_my_goods_addmod'+zeai.ajxext+'submitok=mod&id='+id);return false;}
		zeai.ajax({url:'shop_my_goods'+zeai.extname,data:{id:id,submitok:kind+'_update'}},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	}
}
function shop_my_order_payFn(oid){
	zeai.ajax({url:'shop_goods_detail_pay'+zeai.ajxext+'submitok=ajax_pay',data:{oid:oid,ifadm:ifadm}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			zeai_PAY({money:rs.money,paykind:'wxpay',kind:12,oid:rs.orderid,tmpid:rs.oid,tg_uid:rs.cid,title:decodeURIComponent(rs.title),return_url:rs.return_url,jumpurl:rs.jumpurl});
		}else{zeai.msg(rs.msg);}
	});
}
function shop_my_order_cancel(oid){
	ZeaiM.confirmUp({title:'亲，确定要【取消订单】么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{oid:oid,submitok:'ajax_order_cancel'}},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	}});
}
function shop_my_order_wuliufoFn(oid){
	zeai.ajax({url:'shop_my_order'+zeai.ajxext+'submitok=ajax_getwlinfo&oid='+oid},function(e){var rs=zeai.jsoneval(e);
		if(rs.flag==1){
			ZeaiM.div({obj:shop_my_order_wuliufoBox,w:310,h:300});
			setTimeout(function(){
			o('orderid').html(rs.orderid);
			o('kdname').html(rs.kdname);
			o('kdcode').html(rs.kdcode);
			o('mjtel').html('<a href="tel:'+rs.mjtel+'">'+rs.mjtel+'</a>');
			},300);}
	});
}
function shop_my_order_flag3Fn(oid,title){
	ZeaiM.confirmUp({title:title,cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{oid:oid,submitok:'ajax_order_flag3OK'}},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){zeai.openurl('shop_my_order.php?f=3&ifadm='+ifadm);},3000);}
		});
	}});
}
function shop_my_order_fahuoBoxFn(oid,fhkind){
	if(fhkind==2){
		ZeaiM.confirmUp({title:'确定【开始发货】？发货后买家将到店自取',cancel:'取消',ok:'确定',okfn:function(){
			zeai.ajax({url:'shop_my_order'+zeai.extname,data:{oid:oid,ifadm:1,submitok:'ajax_fahuo_update'}},function(e){rs=zeai.jsoneval(e);
				zeai.msg(rs.msg);
				if(rs.flag==1){
					setTimeout(function(){location.reload(true);},1000);
				}else if(rs.flag=='noaddress'){
					setTimeout(function(){zeai.openurl('shop_my_shop_adm.php');},3000);
				}
			});
		}});
	}else{
		ZeaiM.div_up({obj:shop_my_order_fahuoBox,h:260});
		setTimeout(function(){o('oid').value=oid;},300);
	}
}


function shop_my_order_fahuoFn(){
	var oid=o('oid').value;
	ZeaiM.confirmUp({title:'确定物流信息填写无误【开始发货】么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,js:1,form:wwwyzlovecom_form},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1)setTimeout(function(){zeai.openurl('shop_my_order.php?f=2&ifadm='+ifadm);},3000);
		});
	}});
}
function yuyue_adm_orderFn(id){
	ZeaiM.confirmUp({title:'确定已经联系处理了么？将变更状态为【预约完成】',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,submitok:'ajax_yuyue_adm_order',ifadm:1}},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	}});
}
function shop_my_order_tuikuanFn(id){
	ZeaiM.confirmUp({title:'确定【退款】么？这将会影响您的信誉哦',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,submitok:'ajax_order_tuikuan'}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},3000);}
		});
	}});
}
function shop_my_order_tuikuan_cancelFn(id){
	ZeaiM.confirmUp({title:'确定 撤消【退款申请】么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,submitok:'ajax_order_tuikuan_cancel'}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},3000);}
		});
	}});
}
function shop_my_order_tuikuanAdmFn(id){
	ZeaiM.confirmUp({title:'确定同意【退款】么？费用将自动返还买家',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,submitok:'ajax_order_tuikuan_adm'}},function(e){var rs=zeai.jsoneval(e);
			if(rs.flag==1){
				//zeai.post(rs.url,rs);
				setTimeout(function(){zeai.openurl(rs.url);},1000);
			}else{
				zeai.msg(rs.msg);
			}
		});
	}});
}
function shop_my_order_tuihuoFn(id,title){
	ZeaiM.confirmUp({title:title,cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,submitok:'ajax_order_tuihuo'}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},3000);}
		});
	}});
}
function shop_my_order_tuihuo_cancelFn(id,title){
	ZeaiM.confirmUp({title:title,cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,submitok:'ajax_order_tuihuo_cancel'}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},3000);}
		});
	}});
}
function shop_my_order_tuihuoAdmFn(id,title){
	ZeaiM.confirmUp({title:title,cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,submitok:'ajax_order_tuihuo_adm'}},function(e){var rs=zeai.jsoneval(e);
			if(rs.flag==1){
				zeai.msg(rs.msg);
				setTimeout(function(){location.reload(true);},2000);
			}else{
				zeai.msg(rs.msg);
			}
		});
	}});
}
function shop_my_order_hdFn(id,title){
	ZeaiM.confirmUp({title:title,cancel:'取消',ok:'查看核销码',okfn:function(){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,submitok:'shop_my_order_hd'}},function(e){var rs=zeai.jsoneval(e);
			if(rs.flag==1){
				zeai.alert('商　品：'+rs.ptitle+'<br>数　量：'+rs.num+'<br>总金额：'+rs.orderprice+'<br><b>核销码</b>：<font class="Cf00 S18">'+rs.hdcode+'</font><br>请向商家提供【核销码】进行核销');
			}else{
				zeai.msg(rs.msg);
			}
		});
	}});
}
function shop_my_order_hdAdmFn(id){
	ZeaiM.divBtmMod({objstr:'hdcode',title:'请输入核销码',value:'',placeholder:'输入买家提供的【核销码】',maxLength:8,fn:function(inputV){
		zeai.ajax({url:'shop_my_order'+zeai.extname,data:{id:id,form_hdcode:inputV,submitok:'shop_my_order_hdAdmFn'}},function(e){var rs=zeai.jsoneval(e);
			if(rs.flag==1){
				zeai.msg(rs.msg);
				setTimeout(function(){location.reload(true);},2000);
			}else{
				zeai.msg(rs.msg);
			}
		});

	}});
}
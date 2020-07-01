function malldel(uid){
	zeai.alertplus({title:'确认要删除么？',content:'如果您要删除请点【确定】',title1:'取消',title2:'确定',
		fn1:function(){zeai.alertplus(0);},
		fn2:function(){zeai.alertplus(0);
			zeai.ajax({url:PCHOST+'/my_msg'+zeai.extname,data:{submitok:'ajax_delmsg',uid:uid}},function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		}
	});
}
//function tzdel(id){
//	zeai.alertplus({title:'确认要删除么？',content:'如果您要删除请点【确定】',title1:'取消',title2:'确定',
//		fn1:function(){zeai.alertplus(0);},
//		fn2:function(){zeai.alertplus(0);
//			zeai.ajax({url:PCHOST+'/my_msg'+zeai.extname,data:{submitok:'ajax_tz_del',id:id}},function(e){rs=zeai.jsoneval(e);
//				zeai.msg(0);zeai.msg(rs.msg);
//				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
//			});
//		}
//	});
//}
function msgtzFn(){
    zeai.listEach(zeai.tag(main,'dt'),function(dt){
		var tid=dt.getAttribute("tid"),t=dt.getAttribute("kind");
        var dd=dt.nextElementSibling;
		var dl=dt.parentNode,del=dl.lastElementChild;
		var btn = del.previousElementSibling;
        btn.onclick=function(){
			var b=dt.children[1];if(!zeai.empty(b)){b.remove();}
			((t==1 || t==4) && ZeaiPC.iframe({url:PCHOST+'/my_msg'+zeai.ajxext+'submitok=tip_detail&tid='+tid,w:700,h:450})) || (t==2 && msg_gift_div(tid)) || (t==3 && msg_hi_div(tid));
        }
		del.onclick = function(){
			zeai.alertplus({title:'确认要删除么？',content:'如果您要删除请点【确定】',title1:'取消',title2:'确定',
				fn1:function(){zeai.alertplus(0);},
				fn2:function(){zeai.alertplus(0);
					zeai.ajax({url:PCHOST+'/my_msg'+zeai.extname,data:{submitok:'ajax_tz_del',tid:tid}},function(e){rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				}
			});
		}
    });
	function msg_gift_div(tid) {
		var box=o('msg_gift_box');
		zeai.ajax({url:PCHOST+'/my_msg'+zeai.ajxext+'submitok=ajax_gift_div_msg',data:{tid:tid}},function(e){rs=zeai.jsoneval(e);
		
			console.log(rs);
		
			var div=zeai.div({obj:box,title:'收到【'+rs.nickname+'】礼物',w:300,h:290});
			var em=zeai.tag(box,'em')[0];em=em.children;
			em[0].src       = rs.picurl;
			em[1].html(rs.title);
			em[2].html(rs.price + lovebstr);
			var a=zeai.tag(box,'a'),tipbox;
			a[0].onclick = function (){zeai.openurl_(rs.uhref);}
			a[1].onclick = function (){div.click();ZeaiPC.iframe({url:PCHOST+'/gift'+zeai.ajxext+'uid='+rs.uid,w:600,h:500});}
		});	
	}
	function msg_hi_div(tid){
		zeai.ajax({url:PCHOST+'/my_msg'+zeai.ajxext+'submitok=ajax_msg_hi_div',data:{tid:tid}},function(e){
			var s = e.split("|ZEAI|"),id=s[0],uid=s[1],nickname=s[2],sex=s[3],photo_s=s[4],content=s[5],ifnew=s[6],ifhiher=s[7];
			var box_hi,dl,a,dt,dd,img,span,atitle;
			atitle = (ifhiher==0)?'回复TA':'与TA聊天';
			box_hi = zeai.addtag('div');box_hi.class('box_hi');box_hi.id='box_hi';
			dl = zeai.addtag('dl');
			a  = zeai.addtag('a');a.html(atitle);
			dt = zeai.addtag('dt');dt.class('sexbg'+sex);
			dd = zeai.addtag('dd');dd.html(decodeURI(content));
			img = zeai.addtag('img');img.src = photo_s;
			span = zeai.addtag('span');span.html(decodeURI(nickname));
			dt.appendChild(img);dt.appendChild(span);dl.appendChild(dt);dl.appendChild(dd);
			box_hi.appendChild(dl);box_hi.appendChild(a);
			var div=zeai.div({removeobj:true,obj:box_hi,title:'收到招呼',w:360,h:290});
			//dt.onclick=function(){div.click();ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+uid,ZEAI_MAIN,'u');}
			a.onclick=function(){div.click();
				(ifhiher==0 && zeai.ajax({url:PCHOST+'/u'+zeai.extname,js:1,data:{submitok:'ajax_senddzh',uid:uid}},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1){
						tips0_100_msg_hi.html('<i class="ico hi">&#xe6bd;</i>招呼已发送');tips0_100_msg_hi.show();setTimeout(function(){tips0_100_msg_hi.hide()},2100);
					}else if(rs.flag=='nodata'){
						nodata();
					}else{
						zeai.msg(rs.msg);
					}
				})) || (ifhiher==1 && ZeaiPC.chat(uid));
			}
		});	
	}
}
function browseFn() {
    zeai.listEach(zeai.tag(main,'dt'),function(dt){
		var clsid=dt.getAttribute("clsid"),uid=dt.getAttribute("uid");
        var dd=dt.nextElementSibling;
		var dl=dt.parentNode,del=o('del'+clsid);
        o('btn'+clsid).onclick=function(){if(this.getAttribute("ifchat")==1)ZeaiPC.chat(uid);}
		if(!zeai.empty(del))del.onclick = function(){
			zeai.alertplus({title:'确定要删除么？',content:'如果您要删除请点【确定】',title1:'取消',title2:'确定',
				fn1:function(){zeai.alertplus(0);},
				fn2:function(){zeai.alertplus(0);
					zeai.ajax({url:PCHOST+'/my_browse'+zeai.extname,data:{submitok:'ajax_del',clsid:clsid}},function(e){rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				}
			});
		}
    });
}
if(!zeai.empty(o('browse_delall')))o('browse_delall').onclick=function(){
	zeai.alertplus({title:'确定要清空全部么？',content:'如果您要清空全部请点【确定】',title1:'取消',title2:'确定',
		fn1:function(){zeai.alertplus(0);},
		fn2:function(){zeai.alertplus(0);
			zeai.ajax({url:PCHOST+'/my_browse'+zeai.extname,data:{submitok:'ajax_del_all'}},function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		}
	});
}

function my_followFn() {
    zeai.listEach(zeai.tag(main,'dt'),function(dt){
		var clsid=dt.getAttribute("clsid"),uid=dt.getAttribute("uid");
        o('btn'+clsid).onclick=function(){
			var t=this.getAttribute("t"),t_str;
			if(t==1){
				zeai.alertplus({title:'确定取消关注么？',content:'取消后将无法接收Ta的动态哦，如果您要取消关注请点【确定】',title1:'取消',title2:'确定',fn1:function(){zeai.alertplus(0);},
					fn2:function(){zeai.alertplus(0);Zeai_inFunc('ajax_follow_del',clsid);}
				});
			}else if(t==2){
				if(this.hasClass('edlan')){
					Zeai_inFunc('ajax_gz',uid);
				}else{
					zeai.alertplus({title:'确定取消关注么？',content:'取消后将无法接收Ta的动态哦，如果您要取消关注请点【确定】',title1:'取消',title2:'确定',fn1:function(){zeai.alertplus(0);},
						fn2:function(){zeai.alertplus(0);Zeai_inFunc('ajax_fans_del',uid);}
					});
				}
			}else if(t==3){
				Zeai_inFunc('ajax_hmd_cancel',uid);
			}
		}
    });
	function Zeai_inFunc(submitok,clsid){
		zeai.ajax({url:PCHOST+'/my_follow'+zeai.extname,data:{submitok:submitok,clsid:clsid}},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	}
}

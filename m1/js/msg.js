function msgsxFn(){
    zeai.listEach(zeai.tag(main,'dt'),function(dt){
		var uid=dt.getAttribute("uid");
        dt.onclick=function(){page({g:'m1/u'+zeai.ajxext+'uid='+uid,l:'u'});}
        var dd=dt.nextElementSibling;
		var dl=dt.parentNode,del=zeai.tag(dl,'strong')[0];
        dd.onclick=function(){
			var newi=dl.getElementsByClassName("new")[0];
			if(!zeai.empty(newi))newi.remove();
			sessionStorage.uid=uid;
            ZeaiM.page.load('m1/msg_show'+zeai.ajxext+'uid='+uid,ZEAI_MAIN,'msg_show');
			//if(dt.className=='lock'){setTimeout(function(){if(!zeai.empty(o('msg_sxbtn')))msg_sxbtn.click();},800);}//会闪跳
        }
		touchDel(dl,dd);
		del.onclick = function(){dl.remove();
			zeai.ajax({url:'m1/msg'+zeai.ajxext+'submitok=ajax_sx_clearmsg',data:{uid:uid}},function(e){rs=zeai.jsoneval(e);newdian(rs);});
		}
    });
}
function touchDel(dl,dd){
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
	function dlreset(){zeai.listEach(zeai.tag(main,'dl'),function(dl){ZeaiM.fade({arr:[dl],num:'0'});});}
}
function msgtzFn(){
    zeai.listEach(zeai.tag(main,'dt'),function(dt){
		var tid=dt.getAttribute("tid"),t=dt.getAttribute("kind");
        var dd=dt.nextElementSibling;
		var dl=dt.parentNode,del=dl.lastElementChild;
        dl.onclick=function(){
			var b=dt.children[1];
			if(!zeai.empty(b)){b.remove();
				if(!zeai.empty(o('num_tz'))){
					var numtz=parseInt(o('num_tz').innerHTML);
					if(numtz<=1){
						o('num_tz').remove();
					}else{
						o('num_tz').html(numtz-1);
					}
				}
				if(!zeai.empty(o('num_btm'))){
					var numbtm=parseInt(o('num_btm').innerHTML);
					if(numbtm<=1){
						o('num_btm').remove();
					}else{
						o('num_btm').html(numbtm-1);
					}
				}
			}
			((t==1 || t==4) && page({g:'m1/msg'+zeai.ajxext+'submitok=tip_detail&tid='+tid,l:'msg_detail'})) || (t==2 && msg_gift_div(tid)) || (t==3 && msg_hi_div(tid));
        }
		touchDel(dl,dd);
		del.onclick = function(){
			dl.remove();
			zeai.ajax({url:'m1/msg'+zeai.ajxext,data:{submitok:'ajax_tz_del',tid:tid}},function(e){rs=zeai.jsoneval(e);newdian(rs);});
		}
    });
	function msg_gift_div(tid) {
		var box=o('msg_gift_box');
		zeai.ajax({url:'m1/msg'+zeai.ajxext+'submitok=ajax_gift_div_msg',data:{tid:tid}},function(e){rs=zeai.jsoneval(e);
			var div=zeai.div({fobj:ZEAI_MAIN,obj:box,title:'收到【'+rs.nickname+'】礼物',w:300,h:290});
			var em=zeai.tag(box,'em')[0];em=em.children;
			em[0].src       = rs.picurl;
			em[1].html(rs.title);
			em[2].html(rs.price + lovebstr);
			var a=zeai.tag(box,'a'),tipbox;
			a[0].onclick = function (){div.click();ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+rs.uid,ZEAI_MAIN,'u');}
			a[1].onclick = function (){
				div.click();ZeaiM.page.load('m1/gift'+zeai.ajxext+'uid='+rs.uid,ZEAI_MAIN,'gift_index');
			}
		});	
	}
	function msg_hi_div(tid){
		zeai.ajax({url:'m1/msg'+zeai.ajxext+'submitok=ajax_msg_hi_div',data:{tid:tid}},function(e){
			if(e.indexOf('{') != -1){
				var rs=zeai.jsoneval(e);
				if(rs.flag=='nohi'){
					zeai.msg(rs.msg)
				}else if(!zeai.empty(rs.flag)){
					ZeaiM.page.sorry(rs,ZEAI_MAIN);
				}else{
					zeai.msg(rs.msg);
				}
				return false;
			}
			//
			var s = e.split("|ZEAI|"),id=s[0],uid=s[1],nickname=s[2],sex=s[3],photo_s=s[4],content=s[5],ifnew=s[6],ifhiher=s[7];
			var box_hi,dl,a,dt,dd,img,span,atitle;
			sessionStorage.uid = uid;//2019-10-04
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
			/*处理new和num*/
			if (ifnew == 1){
				
			}
			var div=zeai.div({removeobj:true,obj:box_hi,title:'收到招呼',w:300,h:290});
			dt.onclick=function(){div.click();ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+uid,ZEAI_MAIN,'u');}
			a.onclick=function(){div.click();
				(ifhiher==0 && zeai.ajax({url:'m1/u'+zeai.extname,js:1,data:{submitok:'ajax_senddzh',uid:uid}},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1){
						tips0_100_msg_hi.html('<i class="ico hi">&#xe6bd;</i>招呼已发送');tips0_100_msg_hi.show();setTimeout(function(){tips0_100_msg_hi.hide()},2100);
					}else if(rs.flag=='nodata'){
						nodata(ZEAI_MAIN) ;
					}else{
						zeai.msg(rs.msg);
					}
				})) || (ifhiher==1 && ZeaiM.page.load('m1/msg_show'+zeai.ajxext+'uid='+uid,ZEAI_MAIN,'msg_show'));
			}
		});	
	}
}


function msgggFn(){
    zeai.listEach(zeai.tag(main,'a'),function(a){
		var nid=a.getAttribute("nid");
        a.parentNode.onclick=function(){ZeaiM.page.load('m1/msg'+zeai.ajxext+'submitok=msg_news_detail&nid='+nid,ZEAI_MAIN,'msg_news_detail');}
    });
}
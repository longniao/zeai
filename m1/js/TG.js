function TG_btn(){
	if(zeai.empty(tgpic))zeai.msg('正在生成您的专属推广海报，请稍等...',{time:3});
	ZeaiM.page.load('m1/TG'+zeai.ajxext+'submitok=ewm','TG','TG_ewm');
}
function tgpicdown(){ZeaiM.piczoom({browser:browser,b:up2+tgpic,list:[up2+tgpic]});}
function TGuA(uid,fpage){ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+uid,fpage,'u');}
function TG_UabtnFn(){ZeaiM.page.load('m1/TG'+zeai.ajxext+'submitok=ewm','TG_U','TG_ewm');}
function TG_BANGbtnFn(){ZeaiM.page.load('m1/TG'+zeai.ajxext+'submitok=ewm','TG_BANG','TG_ewm');}
function TG_TDabtnFn(){ZeaiM.page.load('m1/TG'+zeai.ajxext+'submitok=ewm','TG_TD','TG_ewm');}
function TD_count(){
	setTimeout(function(){
		zeai.ajax({url:'m1/TG'+zeai.ajxext+'submitok=ajax_TD_count1'},function(e){rs=zeai.jsoneval(e);
			var TG_NUM1V=rs.XX['num1'],TG_NUM2V=rs.XX['num2'],TG_NUM3V=rs.XX['num3'];
			setTimeout(function(){TG_NUM1.html(TG_NUM1V);},100);
			setTimeout(function(){TG_NUM2.html(TG_NUM2V);},200);
			setTimeout(function(){TG_NUM3.html(TG_NUM3V);},300);
		});
	},200);
}	
function TG_MSGtouchDel(dl,dd) {
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
	function dlreset(){zeai.listEach(zeai.tag(TG_MSGbox,'dl'),function(dl){ZeaiM.fade({arr:[dl],num:'0'});});}
}
function TG_MSGFn(){
    zeai.listEach(zeai.tag(TG_MSGbox,'dt'),function(dt){
		var tid=dt.getAttribute("tid");
        var dd=dt.nextElementSibling;
		var dl=dt.parentNode,del=dl.lastElementChild;
        dl.onclick=function(){
			var b=dt.children[1];if(!zeai.empty(b)){b.remove();}
			page({g:'m1/TG'+zeai.ajxext+'submitok=TG_MSG_detail&tid='+tid,y:'TG_MSG',l:'TG_MSG_detail'});
        }
		TG_MSGtouchDel(dl,dd);
		del.onclick = function(){dl.remove();zeai.ajax({url:'m1/TG'+zeai.ajxext+'submitok=ajax_MSG_del',data:{tid:tid}});}
    });
}
function TG_dhlovebBtnFn() {
	zeai.alertplus({title:'兑换小提示',content:'1.点击【开始兑换】<br>2.选择充值数量<br>3.最后【余额支付】',title1:'取消',title2:'开始兑换',
		fn1:function(){zeai.alertplus(0);uname.focus();},
		fn2:function(){zeai.alertplus(0);page({g:'m1/my_loveb.php?a=cz',y:'TG',l:'my_loveb'});}
	});
}
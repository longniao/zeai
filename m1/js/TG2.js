function TD_count(){
	setTimeout(function(){
		zeai.ajax({url:'tg_my'+zeai.ajxext+'submitok=ajax_TD_count1'},function(e){rs=zeai.jsoneval(e);
			var TG_NUM1V=rs.XX['num1'],TG_NUM2V=rs.XX['num2'],TG_NUM3V=rs.XX['num3'];
			setTimeout(function(){TG_NUM1.html(TG_NUM1V);},100);
			setTimeout(function(){TG_NUM2.html(TG_NUM2V);},200);
			setTimeout(function(){TG_NUM3.html(TG_NUM3V);},300);
		});
	},200);
}	
function TG_MSGFn(){
    zeai.listEach(zeai.tag(TG_MSGbox,'dt'),function(dt){
		var tid=dt.getAttribute("tid");
        var dd=dt.nextElementSibling;
		var dl=dt.parentNode,del=dl.lastElementChild;
        dl.onclick=function(){
			var b=dt.children[1];
			if(!zeai.empty(b)){
				b.remove();
				var tipnum=parseInt(tg_num_btm.innerHTML);
				if(tipnum>1){
					tg_num_btm.html(tipnum-1);	
				}else{
					tg_num_btm.remove();
				}
			}
			page({g:'tg_my'+zeai.ajxext+'submitok=TG_MSG_detail&tid='+tid,y:'TG_MSG',l:'TG_MSG_detail'});
        }
		TG_MSGtouchDel(dl,dd);
		del.onclick = function(){dl.remove();zeai.ajax({url:'tg_my'+zeai.ajxext+'submitok=ajax_MSG_del',data:{tid:tid}});}
    });
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


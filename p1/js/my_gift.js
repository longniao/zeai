function mygift_showflagFn(){
	var fnum = mygift_countnum();
	if (fnum > 0){
		btn_duihuan.addClass('on');
	}else{
		btn_duihuan.removeClass('on');
	}
	function  mygift_countnum(){
		var retn = 0;
		zeai.listEach(zeai.tag(my_gift_box,'li'),function(li){
			var input = li.getElementsByTagName("input")[0];
			if (input.checked){
				retn++;
			}
		});
		return retn;
	}
}
function selectall_mygiftFn(){
	if (this.checked == true){
		zeai.listEach('.my_gift_dh',function(obj){
			obj.firstChild.checked = true;
		});
	}else{
		zeai.listEach('.my_gift_dh',function(obj){
			obj.firstChild.checked = false;
		});				
	}
	mygift_showflagFn();
}
function btn_duihuanFn(){
	var fidlist = [];
	zeai.listEach(zeai.tag(my_gift_box,'li'),function(li){
		var input = li.getElementsByTagName("input")[0];
		if (input.checked){
			fidlist.push(input.value);
		}
	});
	var fidliststr = fidlist.join(',');
	if (zeai.empty(fidliststr)){zeai.msg('请选您要兑换的礼物');return false;}
		
	zeai.alertplus({title:'确定要兑换么？',content:dhstr+'，如果要兑换请点击【确定】',title1:'取消',title2:'确定',
		fn1:function(){zeai.alertplus(0);},
		fn2:function(){zeai.alertplus(0);
			zeai.ajax({url:PCHOST+'/my_gift'+zeai.extname,data:{submitok:'ajax_duihuan',fidlist:fidliststr}},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
				zeai.msg(rs.msg);
			});
		}
	});
}

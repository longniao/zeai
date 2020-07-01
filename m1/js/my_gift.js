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
	if (zeai.empty(fidliststr)){return false;}
	ZeaiM.confirmUp({title:dhstr,cancel:'取消',ok:'确定兑换',okfn:function(){
		zeai.ajax({url:'m1/my_gift'+zeai.extname,data:{submitok:'ajax_duihuan',fidlist:fidliststr}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){my_gift_inbtn.click();selectall_mygift.checked=false;btn_duihuan.removeClass('on');}
			zeai.msg(rs.msg);
		});
	}});
}

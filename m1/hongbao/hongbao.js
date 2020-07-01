if (o('qiang'))o('qiang').onclick = function(){
	if (hb_flag != 1)ZEAI_win_alert('已抢光或已过期');
	XML_ajax('detail'+ajxext+'submitok=ajax_add_qiang&fid='+fid,function(e){rs=jsoneval(e);
		switch (rs.flag){
			case 'nologin':openlinks('../../m1/login.php?jumpurl='+encodeURIComponent('../../m1/hongbao/detail.php?fid='+fid));break;
			case 1:
				o('qiang').className = 'ed';
				o('qiang').onclick   = null
				display('mask_qd',1);
				display('qdokbox',1);
				o('randloveb').innerHTML = rs.moeny;
			break;
			default:ZEAI_win_alert(rs.msg);break;
		}
	});
}
if (!empty(o('mask_qd'))){
	o('mask_qd').onclick = function(){openurl('detail'+ajxext+'fid='+fid);}
	o('qdokbox').onclick = function(){openurl('detail'+ajxext+'fid='+fid);}
}
if (o('btnshang'))o('btnshang').onclick = function(){
	ZEAI_win_confirm('确定要发红包么～',function (){
		XML_ajax('detail'+ajxext+'submitok=ajax_add_shang&fid='+fid,function(e){rs=jsoneval(e);
			if (rs.flag == 'nologin'){
				//openlinks('/my'+ajxext_);
				openlinks('../../m1/login.php?jumpurl='+encodeURIComponent('../../m1/hongbao/detail.php?fid='+fid));
			}else if(rs.flag == 'nomoney'){
				ZEAI_win_alert(rs.msg,rs.jumpurl);
			}else if(rs.flag == 1){
				//ZEAI_iframe('发私包给'+nickname,'detail'+ajxext+'fid='+fid+'&submitok=add_shang');
				
				openlinks('detail'+ajxext+'submitok=add_shang'+'&fid='+fid+'&title_shang='+'发私包给 - '+nickname);
				
			}else{ZEAI_win_alert(rs.msg);}
		});
	});
}
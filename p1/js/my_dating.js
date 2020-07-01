/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
function bmlist(id,title){
	supdes=ZeaiPC.iframe({url:PCHOST+'/my_dating'+zeai.ajxext+'submitok=bmuser&id='+id+'&title='+title,w:666,h:500})
}
function dating_btn_saveFn(){
	var m1 = get_option('m1','v');
	var m2 = get_option('m2','v');
	var m3 = get_option('m3','v');
	var m1t = get_option('m1','t');
	var m2t = get_option('m2','t');
	var m3t = get_option('m3','t');
	m1t = (nulltext == m1t)?'':m1t;
	m2t = (nulltext == m2t)?'':' '+m2t;
	m3t = (nulltext == m3t)?'':' '+m3t;
	m1 = (m1 == 0)?'':m1;
	m2 = (m2 == 0)?'':','+m2;
	m3 = (m3 == 0)?'':','+m3;
	var mate_areaid = m1 + m2 + m3;
	mate_areaid = (mate_areaid == '0,0,0')?'':mate_areaid;
	var mate_areatitle = m1t + m2t + m3t;
	o('areaid').value = mate_areaid;
	o('areatitle').value = mate_areatitle;
	content.value = zeai.clearhtml(content.value);
	content.value = content.value.substring(0,1000);
	if(!zeai.ifint(o('datingkind').value)){
		zeai.msg("请选择约会类型",o('datingkind'));
		return false;
	}
	if(zeai.empty(o('title').value)){
		zeai.msg("请选择约会主题",o('title'));
		return false;
	}
	if(!zeai.ifint(o('price').value)){
		zeai.msg("请选择费用预算",o('price'));
		return false;
	}
	if(!zeai.ifint(o('day8').value)){
		zeai.msg("请输入约会时间【日】",o('day8'));
		return false;
	}
	if(!zeai.ifint(o('hour8').value)){
		zeai.msg("请输入约会时间【时】",o('hour8'));
		return false;
	}
	if(!zeai.ifint(o('minute8').value)){
		zeai.msg("请输入约会时间【分】",o('minute8'));
		return false;
	}
	if(!zeai.ifint(o('maidian').value)){
		zeai.msg("请选择【谁来买单】",o('maidian'));
		return false;
	}
	if(!zeai.ifint(o('m1').value)){
		zeai.msg("请选择约会地区",o('m1'));
		return false;
	}
	if(zeai.empty(o('contact').value)){
		zeai.msg("请输入【联系方法】",o('contact'));
		return false;
	}
	if(zeai.empty(o('content').value)){
		zeai.msg("请输入【约会内容】",o('content'));
		return false;
	}
	zeai.msg("正在保存",{time:30});
	zeai.ajax({url:PCHOST+'/my_dating'+zeai.extname,form:zeai_cn_FORM},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag == 1){
			setTimeout(function(){parent.location.reload(true);},1500);
		}
	});
}
function bmstop(id) {
	zeai.alertplus({title:'确定要强制结束当前约会么？',content:'如果要强制结束请点击【确定】',title1:'我再想想',title2:'确定',
		fn1:function(){zeai.alertplus(0);},
		fn2:function(){zeai.alertplus(0);
			zeai.ajax({url:PCHOST+'/my_dating'+zeai.ajxext+'submitok=ajax_dating_stop&fid='+id},function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){
					setTimeout(function(){location.reload(true);},1000);
				}
			});
		}
	});
}
function dating_del(id) {
	zeai.alertplus({title:'确定要删除当前约会么？',content:'请慎重操作，随意删除审核后的约会将影响您的曝光率和信誉度，且删除后不可恢复，报名会员信息也将同时删除<br>如果真的要删除请点击【确定】',title1:'我再想想',title2:'确定',
		fn1:function(){zeai.alertplus(0);},
		fn2:function(){zeai.alertplus(0);
			zeai.ajax({url:PCHOST+'/my_dating'+zeai.ajxext+'submitok=ajax_dating_del&fid='+id},function(e){rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){
					setTimeout(function(){location.reload(true);},1000);
				}
			});
		}
	});
}
function dating_add(){supdes=ZeaiPC.iframe({url:'my_dating'+zeai.ajxext+'submitok=add',w:900,h:550});}
function dating_mod(fid){supdes=ZeaiPC.iframe({url:'my_dating'+zeai.ajxext+'submitok=mod&fid='+fid,w:900,h:550});}
function dating_listBmBoxuInit(list) {
	zeai.listEach(zeai.tag(list,'li'),function(li){
		var U=li.children[0],clsid,uA,nickname,fid;
		uA=li.lastChild;
		if(uA.tagName=='A'){
			uA.onclick = function (){
				nickname=decodeURI(this.getAttribute("nickname"));
				clsid   =this.parentNode.getAttribute("clsid");
				fid   =this.parentNode.getAttribute("fid");
				zeai.alertplus({title:'★确定邀请此人★',content:'<div style="text-align:left">① 操作后本约会将自动变为结束报名<br>② 【'+nickname+'】将看到你留的联系方法<br>③ 请速与【'+nickname+'】联系进行线下约会<br>祝你们约会成功，交友愉快！</div>	',title1:'取消',title2:'确定',
					fn1:function(){zeai.alertplus(0);},
					fn2:function(){
						zeai.alertplus(0);
						zeai.msg('邀请并正在通知对方..',{time:20});
						zeai.ajax({url:PCHOST+'/my_dating'+zeai.ajxext+'submitok=ajax_dating_bmuser_update&clsid='+clsid+'&fid='+fid},function(e){rs=zeai.jsoneval(e);
							zeai.msg(0);zeai.msg(rs.msg,{time:3});
							if(rs.flag==1){
								setTimeout(function(){location.reload(true);},3000);
							}
						});
					}
				});
			}
		}
	});
}
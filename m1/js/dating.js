//setList
function setList(list){
	zeai.listEach(zeai.tag(list,'li'),function(li){setList_li(li)});
}
//分页
function listOnscroll(){
	var tt = parseInt(o('main').scrollTop);
	var cH= parseInt(o('main').clientHeight);
	var  H= parseInt(o('main').scrollHeight);
	if (H-tt-cH <128 && tt>100){
		if (p > totalP){
			o('main').onscroll = null;
			zeai.msg('已达末页，全部加载结束');
		}else{
			var postjson = {submitok:'ajax_list',totalP:totalP,p:p,t:t};
			zeai.ajax({'url':'m1/dating'+zeai.extname,data:postjson},function(e){
			if (e == 'end'){
				zeai.msg(0);zeai.msg('已达末页，全部加载结束');
			}else{
				o('list').append('<div id="p'+p+'">'+e+'</div>');
				var dllist=zeai.tag(o('p'+p),'li'),l;
				l=dllist.length;
				for(var k=0;k<l;k++){setList_li(dllist[k]);}
				p++;
			}
		});}
	}
	backtopFn(o('main'));
}
function setList_li(li){
	(function(li){
		li.onclick = function (){
			ZeaiM.page.load('m1/dating'+zeai.ajxext+'id='+li.getAttribute("clsid"),ZEAI_MAIN,'dating_detail');
		}
		li.children[0].onclick = function (e){
			e.cancelBubble = true;
			ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+this.getAttribute("uid"),ZEAI_MAIN,'u');
		}
	})(li);
}

topminibox.addEventListener('touchmove', function(e){e.preventDefault();});
nav.addEventListener('touchmove', function(e){e.preventDefault();});

function addInit(){
	zeai.listEach(zeai.tag(www_zeai_cn_FORM,'input'),function(obj){
		if(obj.type!='hidden'){
			obj.onblur=blurFn;
		}
	});
	zeai.listEach(zeai.tag(www_zeai_cn_FORM,'select'),function(obj){
		obj.onblur=blurFn;
	});
	function blurFn(){zeai.setScrollTop(0);}
	content.onblur=blurFn;
}

function dating_btn_saveFn(){
	zeai.msg("请稍后，正在保存",{time:30});
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
	content.value = content.value.substring(0,140);
	zeai.ajax({url:'m1/dating.php',form:www_zeai_cn_FORM},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
		if(rs.flag==1){
			if(!zeai.empty(rs.url)){
				setTimeout(function(){zeai.openurl(rs.url);},1000);
			}else{
				setTimeout(function(){location.reload(true);},1000);
			}
		}
	});
}
function dating_btn_detailBMfn() {
	ZeaiM.confirmUp({title:'确定要报名此约会么？',cancel:'取消',ok:'确定报名',okfn:function(){
		zeai.msg('正在报名中..',{time:5});
		zeai.ajax({url:'m1/dating.php',form:www_zeai_cn_FORM},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag=='nologin'){
				setTimeout(function(){zeai.openurl('m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));},1000);
			}else if(rs.flag==1){
				setTimeout(function(){location.reload(true);},1000);
			}
		});
	}});
}
function dating_delmy(id) {
	ZeaiM.confirmUp({title:'请慎重，确定要删除此约会么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'m1/dating.php?submitok=ajax_detail_del&clsid='+id},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){
				setTimeout(function(){location.reload(true);},1000);
			}
		});
	}});
}
function dating_manage(id,title) {
	ZeaiM.page.load('m1/dating'+zeai.ajxext+'submitok=ajax_dating_bmuser&clsid='+id+'&title='+title,'dating_detail','dating_detail_bmuser');
}
function dating_listBmBoxuInit(list) {
	zeai.listEach(zeai.tag(list,'li'),function(li){
		var U=li.children[0],clsid,uA,nickname,fid;
		li.children[0].onclick = function (){
			ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+this.getAttribute("uid"),'dating_detail_bmuser','u');
		}
		uA=li.lastChild;
		if(uA.tagName=='A'){
			uA.onclick = function (){
				nickname=this.getAttribute("nickname");
				clsid   =this.parentNode.getAttribute("clsid");
				fid   =this.parentNode.getAttribute("fid");
				zeai.alertplus({title:'★确定邀请此人★',content:'<div style="text-align:left">① 操作后本约会将自动变为结束报名<br><br>② 【'+nickname+'】将看到你在约会中的联系方法<br><br>③ 请速与【'+nickname+'】联系进行线下约会<br><br>祝你们约会成功，交友愉快！</div>	',title1:'取消',title2:'确认',
					fn1:function(){zeai.alertplus(0);},
					fn2:function(){
						zeai.alertplus(0);
						zeai.msg('邀请并正在通知对方..',{timd:5});
						zeai.ajax({url:'m1/dating'+zeai.ajxext+'submitok=ajax_dating_bmuser_update&clsid='+clsid+'&fid='+fid},function(e){rs=zeai.jsoneval(e);
							zeai.msg(0);zeai.msg(rs.msg);
							if(rs.flag==1){
								setTimeout(function(){location.reload(true);},1000);
							}
						});
					}
				});
			}
		}
	});
}
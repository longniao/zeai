var checkAllFlag = false;
var ifTrOnclick = false;
(function tablelistInit(){
	var listOBJ=document.getElementsByName("list[]");
	for (var i = 0; i < listOBJ.length; i++) {
		(function(i){
			var tr = o('tr'+listOBJ[i].value);
			tablelistTR(tr,0);
			listOBJ[i].onclick = function(e){
				tablelistOne(this);
				e.cancelBubble = true;
			}
			if (ifTrOnclick){
				tr.onclick = function(){
					tablelistOne(listOBJ[i]);
				}
			}
		})(i);
	}
})();
function tablelistOne(obj) {
	var tr = o('tr'+obj.value);
	if (ifTrOnclick){
		if (!obj.checked){
			obj.checked=true;
			tablelistTR(tr,1);
		}else{
			obj.checked=false;
			tablelistTR(tr,0);
		}
	}else{
		if (obj.checked){
			tablelistTR(tr,1);
		}else{
			tablelistTR(tr,0);
		}
	}
	chkAction();
}
function tablelistTR(tr,flag) {
	if (flag == 1){
		tr.style.backgroundColor=selectbg;
		tr.onmouseover = function (){this.style.backgroundColor = selectbg;} 
		tr.onmouseout  = function (){this.style.backgroundColor = selectbg;}
	}else{
		tr.style.backgroundColor = bg;
		tr.onmouseover = function (){this.style.backgroundColor = overbg;} 
		tr.onmouseout  = function (){this.style.backgroundColor = bg;}
	}
}
/***全选***/
(function checkboxAllInit() {
	var checkbox = document.querySelectorAll(".checkboxall");
	for(var j=0;j<checkbox.length;j++) {(function(j){
		checkbox[j].onclick = function(){
			if (!checkAllFlag){
				checkAllFlag = true;
				checkboxAlled(1);
				checkboxAllList(1);
			}else{
				checkAllFlag = false;
				checkboxAlled(0);
				checkboxAllList(0);
			}
		}
	})(j);}
	function checkboxAlled(flag){
		var checkboxall = document.querySelectorAll(".checkboxall");
		for(var j=0;j<checkboxall.length;j++)checkboxall[j].checked = (flag == 1)?true:false;
	}
})();
function checkboxAllList(flag){
	var listOBJ=document.getElementsByName("list[]");
	var listOBJlength = listOBJ.length;
	for (var i = 0; i < listOBJlength; i++) {
		(function(i){
			var obj = listOBJ[i];
			var tr  = o('tr'+obj.value);
			if (flag == 1){
				tablelistTR(tr,1);
				obj.checked = true;
			}else{
				tablelistTR(tr,0);
				obj.checked = false;
			}
		})(i);
	}
	chkAction();
}
/*btn Action disabled*/
function chkAction(){
	var listOBJ=document.getElementsByName("list[]");
	for (var i=0;i<listOBJ.length;i++){
		if (listOBJ[i].checked){
			btnDisabled(1);
			return false;
		}
	}
	btnDisabled(0);
}
function btnDisabled(flag){
	var btnAction = document.querySelectorAll(".action");
	var Actlength = btnAction.length;
	if (Actlength > 0){
		for(var k=0;k<Actlength;k++){
			if (flag == 1){
				//btnAction[k].class('btn size2 action');
				btnAction[k].removeClass('disabled');
			}else{
				//btnAction[k].class('btn size2 action disabled');
				btnAction[k].addClass('disabled');
			}
		}
	}
}


////////////////////////////////////

function sendTipFn(btnobj,kind){
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要发送的会员');
		return false;
	}
	var arr = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){
		if (arr[key].checked){
			if(kind==2){
				ulist.push(arr[key].getAttribute("uid"));
			}else{
				ulist.push(arr[key].value);
			}
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的会员');
	}else{
		zeai.iframe('发送消息','u_tip.php?ulist='+ulist,600,500);
	}
}
function sendTipFn2(btnobj){
	sendTipFn(btnobj,2);
}

function hnTask(btnobj,t,iframetitle){
	iframetitle = (!zeai.empty(iframetitle))?iframetitle:'分配红娘';
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要分配的客户');
		return false;
	}
	var arr = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){
		if (arr[key].checked){
			/*if(kind==2){
				ulist.push(arr[key].getAttribute("uid"));
			}else{
				ulist.push(arr[key].value);
			}
			*/
			ulist.push(arr[key].value);
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要分配的客户');
	}else{
		zeai.iframe(iframetitle,'crm_hn_utask_add.php?t='+t+'&ulist='+ulist,600,500);
	}
}
function hnTasked(btnobj,kind){
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要调换的会员');
		return false;
	}
	var arr = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){
		if (arr[key].checked){
			if(kind==2){
				ulist.push(arr[key].getAttribute("uid"));
			}else{
				ulist.push(arr[key].value);
			}
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要调换的会员');
	}else{
		zeai.iframe('调换红娘','crm_hn_umod.php?ulist='+ulist,600,500);
	}
}



function delList(json) {
	if (json.btnobj.hasClass('disabled')){
		zeai.alert('请选择要删除的信息');
	}else{
		if(json.ifjson){
			zeai.confirm('真的要删除么？',function(){
				zeai.ajax({url:'photo_m'+zeai.ajxext+'submitok='+json.submitok,form:www_zeai_cn_FORM},function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});		
		}else{
			//o('submitok').value=(zeai.empty(json.submitok))?'批量删除':json.submitok;
			zeai.confirm('真的要删除么？',www_zeai_cn_FORM);
		}
	}
}

function allList(json){
	json.title     = (zeai.empty(json.title))?'删除':json.title;
	json.ifconfirm = (zeai.empty(json.ifconfirm))?true:json.ifconfirm;
	json.ifjson    = (zeai.empty(json.ifjson))?false:json.ifjson;
	json.content    = (zeai.empty(json.content))?'':json.content;
	if (json.btnobj.hasClass('disabled')){
		zeai.alert('请选择要'+json.title+'的信息');
	}else{
		if(json.ifjson){
			if(json.ifconfirm){
				zeai.confirm('真的要'+json.title+'么？'+json.content,function(){ajaxtj();});
			}else{
				ajaxtj();
			}
			function ajaxtj(){
				if(!zeai.empty(json.msg))zeai.msg(json.msg,{time:666});
				zeai.ajax({url:json.url,form:www_zeai_cn_FORM},function(e){var rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}
				});
			}
		}else{
			//o('submitok').value=(zeai.empty(json.submitok))?'批量删除':json.submitok;
			if(json.ifconfirm){
				zeai.confirm('真的要'+json.title+'么？'+json.content,www_zeai_cn_FORM);
			}else{www_zeai_cn_FORM.submit();}
		}
	}
}
function iframe_A(boxid,url,that,ed){
	ed=(zeai.empty(ed))?'ed':ed;
	iframeAreset();
	that.class(ed);
	o('iframe_iframe').src=url;
	function iframeAreset(){zeai.listEach(zeai.tag(o(boxid),'a'),function(obj){obj.removeClass(ed);});}
}





/*最多几个,没有全选
function chkbox_dl_maxnum(i,id,maxnum) {
	var c = o('chk'+id);
	var t = o('dl'+i);
	if (i % 2 == 0){bg=bg1;	} else {bg=bg2;}
	if (c.checked){
		c.checked=false;
		t.style.backgroundColor=bg;
		t.onmouseover = function (){t.style.backgroundColor = overbg;} 
		t.onmouseout  = function (){
			if (i % 2 == 0){bg=bg1;} else {bg=bg2;}
			t.style.backgroundColor = bg;
		}
	}else{
		c.checked=true;
		t.style.backgroundColor=selectbg;
		t.onmouseover = function (){t.style.backgroundColor = overbg;} 
		t.onmouseout  = function (){t.style.backgroundColor = selectbg;}
	}
	ifmaxcbxcnt_dl_maxnum('list','chk'+id,maxnum);
}
function checkboxcount_dl_maxnum(objname){
 	var n = 0;
	var obj = document.getElementsByName(objname+'[]');
	for(var k = 0;k<obj.length;k++){
		if (obj[k].checked)n++;
	}
	return n;
}
function ifmaxcbxcnt_dl_maxnum(objname,objid,maxnum){
 	//var objname = objname.replace('[]','');
	var cbxcnt = checkboxcount_dl_maxnum(objname);
	if (cbxcnt > maxnum){
		//alert('最多只能选择'+num+'项。');
		parent.ZEAI_win_alert('最多只能选择'+maxnum+'项。')
		o(objid).checked = false;
		o(objid).focus;
		
	}
}
*/
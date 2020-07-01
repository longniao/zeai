var checkflag = "false";
var bg='';
var bg1      = '#ffffff';//id=1d
var bg2      = '#ffffff';//id=2
var overbg   = '#F9F9FA';//MouseOver
var selectbg = '#EAEDF1';//Selected
function chkformall(field) {
	var t;
	var listOBJ=document.getElementsByName("list[]");
	var listOBJlength = listOBJ.length;
	if (checkflag == "false") {
		for (i = 0; i < listOBJlength; i++) {
			listOBJ[i].checked = true;
			j=i+1;
			t = o('tr'+j);
			t.style.backgroundColor=selectbg;
			t.onmouseover = function (){this.style.backgroundColor = overbg;} 
			t.onmouseout  = function (){this.style.backgroundColor = selectbg;}
		}
		checkflag = "true";
		o('chkall').checked = true;
		o('chkalltext').innerHTML = "取消";
	} else {
		for (i = 0; i < listOBJlength; i++) {
			listOBJ[i].checked = false;
			j=i+1;
			if (j % 2 == 0){bg=bg1;} else {bg=bg2;}
			t = o('tr'+j);
			t.style.backgroundColor=bg;
			t.onmouseover = function (){this.style.backgroundColor = overbg;} 
			t.onmouseout  = function (){
				f = this.id;
				var f=f.slice(2);
				if (f % 2 == 0){bg=bg1;} else {bg=bg2;}
				this.style.backgroundColor = bg;
			}
		}
		checkflag = "false";
		o('chkall').checked = false;
		o('chkalltext').innerHTML = "全选";
	}
}
function chkbox(i,id) {
	var c = o('chk'+id);
	var t = o('tr'+i);
	if (i % 2 == 0){bg=bg1;	} else {bg=bg2;}
	if (c.checked){//当前状态选中
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
}
/*最多几个,没有全选*/
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

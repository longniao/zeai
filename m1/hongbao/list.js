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
var checkflag;
function chkformalone(field) {
	var t;
	var listOBJ=document.getElementsByName("list[]");
	var listOBJlength = listOBJ.length;
	if (checkflag == "false") {
		for (i = 0; i < listOBJlength; i++) {
			listOBJ[i].checked = true;
		}
		checkflag = "true";
		o('chkalone').checked = true;
		o('chkalonetext').innerHTML = "取消";
	} else {
		for (i = 0; i < listOBJlength; i++) {
			listOBJ[i].checked = false;
		}
		checkflag = "false";
		o('chkalone').checked = false;
		o('chkalonetext').innerHTML = "全选";
	}
}

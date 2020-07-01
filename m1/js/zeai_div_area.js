/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/07/10 by supdes
*/
function ZEAI_area(json){
	var areaid=json.areaid,areatitle=json.areatitle,bx=json.bx,ul=json.str,datastr=json.datastr;
	areaid = areaid.split(',');areatitle = areatitle.split(' ');
	var a1v = areaid[0],a2v = areaid[1],a3v = areaid[2],a4v = areaid[3];
	var a1t = areatitle[0],a2t = areatitle[1],a3t = areatitle[2],a4t = areatitle[3];
	a1v=(zeai.empty(a1v))?'':a1v;a2v=(zeai.empty(a2v))?'':a2v;a3v=(zeai.empty(a3v))?'':a3v;a4v=(zeai.empty(a4v))?'':a4v;
	a1t=(zeai.empty(a1t))?'':a1t;a2t=(zeai.empty(a2t))?'':a2t;a3t=(zeai.empty(a3t))?'':a3t;a4t=(zeai.empty(a4t))?'':a4t;
	var selstr1 = '不限',selstr2 = '请选择';
	if(datastr=='hj'){areaARR1=areaARRhj1,areaARR2=areaARRhj2,areaARR3=areaARRhj3,areaARR4=areaARRhj4}
	if(bx){var ARR1=area_bx(1),ARR2=area_bx(2,ARR1),ARR3=area_bx(3,ARR2),ARR4=area_bx(4,ARR3);}else{var ARR1=areaARR1,ARR2=areaARR2,ARR3=areaARR3,ARR4=areaARR4;}
	var areaul = json.ul,
	areaulli = areaul.children[0],
	areauldl = areaulli.getElementsByTagName("dl")[0];
	if(zeai.empty(o(ul+'a1box'))){
		var em1 = zeai.addtag('em');em1.id=ul+'a1box';
		var dt1 = zeai.addtag('dt');dt1.innerHTML='选择省份';dt1.className = 'ed';dt1.id = ul+'dt1id';
		for(var k1=0;k1<ARR1.length;k1++) {(function(k1){
			var A1  = zeai.addtag('a');
			A1.innerHTML = ARR1[k1].v;
			A1.onclick = function (){
				var em2 = o(ul+'a2box'),em3 = o(ul+'a3box'),em4 = o(ul+'a4box'),dt2 = o(ul+'dt2id'),dt3 = o(ul+'dt3id'),dt4 = o(ul+'dt4id');
				if (!zeai.empty(em2))em2.parentNode.removeChild(em2);
				if (!zeai.empty(em3))em3.parentNode.removeChild(em3);
				if (!zeai.empty(em4))em4.parentNode.removeChild(em4);
				if (!zeai.empty(dt2))dt2.parentNode.removeChild(dt2);
				if (!zeai.empty(dt3))dt3.parentNode.removeChild(dt3);
				if (!zeai.empty(dt4))dt4.parentNode.removeChild(dt4);
				ZEAI_area_tab();dt1.innerHTML = this.innerHTML;
				var em2 = ZEAI_creat_area2(ARR1[k1].i);areaulli.appendChild(em2);em1.hide();
				ZEAI_delclass(em1.getElementsByTagName("a"));this.className = 'ed';//保持已选中
				a1v = ARR1[k1].i;
				o(ul+'dt2id').className = 'ed';
			}
			if (a1v == ARR1[k1].i){A1.className = 'ed';dt1.innerHTML = A1.innerHTML;}
			em1.appendChild(A1);
		})(k1);}
		areaulli.appendChild(em1);areauldl.appendChild(dt1);
		if (zeai.ifint(a1v)){
			em2 = ZEAI_creat_area2(a1v);areaulli.appendChild(em2);ZEAI_area_tab();
			dt1.className = 'ed';o(ul+'a1box').show();o(ul+'a2box').hide();o(ul+'dt2id').className = '';
			a1t = dt1.innerHTML;
			if (zeai.ifint(a2v)){
				em3 = ZEAI_creat_area3(a2v);
				if(em3){
					areaulli.appendChild(em3);ZEAI_area_tab();
					dt1.className = 'ed';o(ul+'a1box').show();
					a2t = o(ul+'dt2id').innerHTML;
					if (zeai.ifint(a3v)){
						em4 = ZEAI_creat_area4(a3v);
						if(em4){
							areaulli.appendChild(em4);ZEAI_area_tab();
							a3t = o(ul+'dt3id').innerHTML;
							if (zeai.ifint(a4v)){a4t = o(ul+'dt4id').innerHTML;dt1.className = 'ed';o(ul+'a1box').show();}
						}
					}
				}
			}
			
			dt1.className = 'ed';em1.show();
			
		}
	}
	function ZEAI_area_tab(){
		var dtarr = areauldl.getElementsByTagName("dt");
		ZEAI_area_delfclass(dtarr);
		for(var i=0; i<dtarr.length;i++){(function(i){
			dtarr[i].onclick = function(){
				ZEAI_area_delfclass(dtarr);
				this.className = 'ed';
				var j=i+1;
				o(ul+'a'+j+'box').show();
			}
		})(i);}
	}
	function area_bx(leval,parent){
		if(leval==1){
			var newA1 = [{'i':'ZE0000','v':selstr1,f:0}];
			return newA1.concat(areaARR1);
		}
		if(leval==2){
			var newA2 = [];
			function newA2fn(){
				for(var k=0;k<areaARR1.length;k++){newA2.push({'i':'AI'+k+'00','v':selstr1,f:parent[k].i});}
				return newA2;
			}
			return newA2fn().concat(areaARR2);
		}
		if(leval==3){
			var newA3 = [];
			function newA3fn(){
				for(var k=0;k<areaARR2.length;k++) {newA3.push({'i':'CN00'+k,'v':selstr1,f:parent[k].i});}
				return newA3;
			}
			return newA3fn().concat(areaARR3);
		}
		if(leval==4){
			var newA4 = [];
			function newA4fn(){
				for(var k=0;k<areaARR3.length;k++) {newA4.push({'i':'CN00'+k,'v':selstr1,f:parent[k].i});}
				return newA4;
			}
			return newA4fn().concat(areaARR4);
		}
	}
	function ZEAI_creat_area2(a1v){
		var em2 = zeai.addtag('em');em2.id=ul+'a2box';
		var dt2 = zeai.addtag('dt');dt2.innerHTML='选择城市';dt2.id = ul+'dt2id';
		for(var k2=0;k2<ARR2.length;k2++) {(function(k2){	
			if (ARR2[k2].f == a1v){
				var A2  = zeai.addtag('a');
				A2.innerHTML = ARR2[k2].v;
				A2.onclick = function (){
					var em3 = o(ul+'a3box');
					var dt3 = o(ul+'dt3id');
					if (!zeai.empty(em3))em3.parentNode.removeChild(em3);
					if (!zeai.empty(dt3))dt3.parentNode.removeChild(dt3);
					var em4 = o(ul+'a4box');
					var dt4 = o(ul+'dt4id');
					if (!zeai.empty(em4))em4.parentNode.removeChild(em4);
					if (!zeai.empty(dt4))dt4.parentNode.removeChild(dt4);
					dt2.innerHTML = this.innerHTML;
					a2v = ARR2[k2].i;
					var em3 = ZEAI_creat_area3(a2v);
					if(em3){
						areaulli.appendChild(em3);
						ZEAI_delclass(em2.getElementsByTagName("a"));this.className = 'ed';//保持已选中
						ZEAI_area_tab();
						o(ul+'dt3id').class('ed');
						em1.hide();em2.hide();em3.show();
					}else{
						var retid    = a1v+','+a2v;
						var rettitle = o(ul+'dt1id').innerHTML +' '+ o(ul+'dt2id').innerHTML;
						if (typeof(json.end) == "function"){json.end(retid,rettitle);}
						ZEAI_area_tab();
						em2.hide();o(ul+'a1box').show();o(ul+'dt1id').className = 'ed';
						if(!zeai.empty(o('div_up_close')))div_up_close.click();
					}
				}
				if (a2v == ARR2[k2].i){A2.className = 'ed';dt2.innerHTML = A2.innerHTML;}
				em2.appendChild(A2);
			}
		})(k2);}
		areauldl.appendChild(dt2);
		return em2;
	}
	function ZEAI_creat_area3(a2v){
		var em3 = zeai.addtag('em');em3.id=ul+'a3box';
		var dt3 = zeai.addtag('dt');dt3.innerHTML='选择区县';dt3.id = ul+'dt3id';
		for(var k3=0;k3<ARR3.length;k3++) {(function(k3){	
			if (ARR3[k3].f == a2v){
				var A3  = zeai.addtag('a');
				A3.innerHTML = ARR3[k3].v;
				A3.onclick = function (){
					var em4 = o(ul+'a4box');
					var dt4 = o(ul+'dt4id');
					if (!zeai.empty(em4))em4.parentNode.removeChild(em4);
					if (!zeai.empty(dt4))dt4.parentNode.removeChild(dt4);
					dt3.innerHTML = this.innerHTML;
					a3v = ARR3[k3].i;
					ZEAI_delclass(em3.getElementsByTagName("a"));this.className = 'ed';//保持已选中
					var em4 = ZEAI_creat_area4(a3v);
					if(em4){
						areaulli.appendChild(em4);
						ZEAI_area_tab();
						ZEAI_delclass(em4.getElementsByTagName("a"));this.className = 'ed';//保持已选中
						em3.hide();em4.show();
						o(ul+'dt4id').className = 'ed';
					}else{
						ZEAI_area_tab();
						em3.hide();o(ul+'a1box').show();o(ul+'dt1id').className = 'ed';
						var retid    = a1v+','+a2v+','+a3v;
						var rettitle = o(ul+'dt1id').innerHTML +' '+ o(ul+'dt2id').innerHTML+' ' + o(ul+'dt3id').innerHTML;
						if (typeof(json.end) == "function"){json.end(retid,rettitle);}
						if(!zeai.empty(o('div_up_close')))div_up_close.click();
					}
				}
				if (a3v == ARR3[k3].i){A3.className = 'ed';dt3.innerHTML = A3.innerHTML;}
				em3.appendChild(A3);
				ifcreat3=true;
			}
		})(k3);}
		if(ifcreat3){
			areauldl.appendChild(dt3);
			return em3;
		}else{
			return false;	
		}
	}
	function ZEAI_creat_area4(a3v){
		var em4 = zeai.addtag('em');em4.id=ul+'a4box';
		var dt4 = zeai.addtag('dt');dt4.innerHTML='选择乡镇';dt4.id = ul+'dt4id';
		var ARR4length=ARR4.length,ifcreat4=false;
		for(var k4=0;k4<ARR4length;k4++) {(function(k4){	
			if (ARR4[k4].f == a3v){
				var A4 = zeai.addtag('a');
				A4.innerHTML = ARR4[k4].v;
				A4.onclick = function (){
					dt4.innerHTML = this.innerHTML;o(ul+'dt1id').className = 'ed';
					ZEAI_area_tab();
					o(ul+'a1box').show();o(ul+'dt1id').className = 'ed';
					ZEAI_delclass(em4.getElementsByTagName("a"));this.className = 'ed';//保持已选中
					a4v = ARR4[k4].i;
					var retid    = a1v+','+a2v+','+a3v+','+a4v;
					var rettitle = o(ul+'dt1id').innerHTML +' '+ o(ul+'dt2id').innerHTML+' ' + o(ul+'dt3id').innerHTML+' ' + o(ul+'dt4id').innerHTML;
					if (typeof(json.end) == "function"){json.end(retid,rettitle);}
					if(!zeai.empty(o('div_up_close')))div_up_close.click();
				}
				if (a4v == ARR4[k4].i){A4.className = 'ed';dt4.innerHTML = A4.innerHTML;}
				em4.appendChild(A4);
				ifcreat4=true;
			}
		})(k4);}
		if(ifcreat4){
			areauldl.appendChild(dt4);
			return em4;
		}else{
			return false;	
		}
	}
	function ZEAI_area_delfclass(dtarr){
		for(var i=0; i<dtarr.length;i++){
		if (!zeai.empty(dtarr[i]))dtarr[i].className = '';
		var j=i+1;
		if (!zeai.empty(o(ul+'a'+j+'box')))o(ul+'a'+j+'box').hide();
	}}
	function ZEAI_delclass(arr){for(var i=0; i<arr.length;i++){arr[i].className = '';}}
}

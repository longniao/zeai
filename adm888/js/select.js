/***************************************************
Copyright (C) 2016
未经本人同意，不得转载
作 者: 郭余林　QQ:797311 (supdes)
***************************************************/
var selectext;
function build_CheckboxRadio(objid,type,selectext,arr,defarr) {
/*type：1radio，2checkbox
  checkbox：defarr格式'2|3'　多值
  radio：defarr格式'3'  值唯一
*/
	var arrlength = arr.length;
	var brnum     = 1;
	if (arrlength > 0){
		if (arrlength > brnum){document.write('<div>');}
		for( key in arr){
			var idd = parseInt(key)+1;
			idd   = objid+idd;
			var gyl   = arr[key].split("|"); 
			var value = gyl[0];
			var text  = gyl[1];
			var name    = (type == 2)?objid+"[]":objid;
			//var name    = objid;
			var boxkind = (type == 2)?"checkbox":"radio";
			var CRbig = (type == 2)?"":"class=CRbig";
			var selectext;
			if (type == 3){
				var classnamee = 'radiolist';//一行一行
				var blankL   = '<li class='+classnamee+'>';
				var blankR   = '</li>';
			}else{
				var classnamee = 'radboxlist';
				//var blankL   = (arrlength > brnum)?'<li class='+classnamee+'>':'';
				//var blankR   = (arrlength > brnum)?'</li>':'　';
				var blankL   = '<li class='+classnamee+'>';
				var blankR   = '</li>';
			}
			document.write(blankL+'<label for='+idd+'><input type='+boxkind+' name='+name+' id='+idd+' value='+value+' '+CRbig+' '+selectext+' /> '+text+'</label>'+blankR);
			if (!empty(defarr)){
				/*	
				var gyl_def    = defarr.split("|"); 
				var value_def  = gyl_def[key];
				if (value_def == value && ifint("0-9","1,5",value_def) && !empty(value_def)){getid(idd).checked = true;}
				*/		
				var gyl_def    = defarr.split("|"); 
				for( var key2 in gyl_def){
					var value_def  = gyl_def[key2];
					if (value_def == value && ifint(value_def,"0-9","1,5") && !empty(value_def)){getid(idd).checked = true;}
				}
			}

		}
		//if (type == 2)document.write('<input type=hidden name='+objid+'_arr id='+objid+'_arr value=\'\' />');
		if (arrlength > brnum){document.write('</div>');}
	}
}
function build_CheckboxRadio_return(objid,type,selectext,arr,defarr) {
	arr = arr.replace(/\"/g, "");
	arr = arr.split(",");
	var newarr = new Array();
	for (key in arr) {newarr.push(arr[key])}
	var arrlength = newarr.length;
	var brnum     = 1;
	var C1 = '';C2 = '';C3 = '';
	if (arrlength > 0){
		if (arrlength > brnum){var C1= '<div>';}
		for( key in newarr){
			var idd = parseInt(key)+1;
			idd   = objid+idd;
			var gyl   = newarr[key].split("|"); 
			var value = gyl[0];
			var text  = gyl[1];
			var name    = (type == 2)?objid+"[]":objid;
			var boxkind = (type == 2)?"checkbox":"radio";
			var CRbig = (type == 2)?"":"class=CRbig";
			var selectext;
			if (type == 3){
				var classnamee = 'radiolist';//一行一行
				var blankL   = '<li class='+classnamee+'>';
				var blankR   = '</li>';
			}else{
				var classnamee = 'radboxlist';
				var blankL   = '<li class='+classnamee+'>';
				var blankR   = '</li>';
			}
			C2=C2+ blankL+'<label for='+idd+'><input type='+boxkind+' name='+name+' id='+idd+' value='+value+' '+CRbig+' '+selectext+' /> '+text+'</label>'+blankR;
			if (!empty(defarr)){
				var gyl_def    = defarr.split("|"); 
				for( var key2 in gyl_def){
					var value_def  = gyl_def[key2];
					if (value_def == value && ifint(value_def,"0-9","1,5") && !empty(value_def)){getid(idd).checked = true;}
				}
			}

		}
		if (arrlength > brnum){var C3='</div>';}
		C = C1+C2+C3;
		return C;
	}
}
function build_option(objid,arr,nulltext,selectext,defvalue) {
	var gyl,value,text,sOption;
	var ifed = false;
	build_select(objid,nulltext,selectext);
    for( key in arr){
		gyl  = arr[key].split("|"); 
		value = gyl[0];text = gyl[1];
        sOption = document.createElement("OPTION");
        sOption.text  = text;sOption.value = value;
		if(defvalue == value && ifint(defvalue,"0-9","1,5") && !empty(defvalue)){sOption.id = "STmpId"+objid;var ifed = true;}
        getid(objid).options.add(sOption);
    }
	if (ifed) getid("STmpId"+objid).selected = true;
}
function build_select(objid,nulltext,selectext) {
	var html='<select id="'+objid+'" name="'+objid+'" '+selectext+'>';
	html += '<option value="0">'+nulltext+'</option>';
	html += '</select>';
	document.write(html);
}
function build_minioption(objid,arr,nulltext,defvalue) {
	var gyl,value,text,sOption;
	var ifed = false;
    for( key in arr){
		gyl  = arr[key].split("|"); 
		value = gyl[0];text = gyl[1];
        sOption = document.createElement("OPTION");
        sOption.text  = text;sOption.value = value;
		if(defvalue == value && ifint(defvalue,"0-9","1,5") && !empty(defvalue)){sOption.id = "STmpId"+objid;var ifed = true;}
        getid(objid).options.add(sOption);
    }
	if (ifed) getid("STmpId"+objid).selected = true;
}
function LevelMenu3(ajaxname,clientstr,selectext) {
	var s = clientstr.split("|");
	var objid1    = s[0];
	var objid2    = s[1];
	var objid3    = s[2];
	var nulltext  = s[3];
	var selectext2= "onchange=levelmenu3_chanage(this.value,'"+clientstr+"',"+ajaxname+")";
	build_select(objid1,nulltext,selectext);document.write(' ');
	build_select(objid2,nulltext,selectext+" "+selectext2);document.write(' ');
	build_select(objid3,nulltext,selectext);
	SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&k=1&clientstr="+clientstr);
}
function levelmenu1(dataARR1,clientstr,ajaxname) {
	var dataARR1 = dataARR1.split(",");
	var s = clientstr.split("|");
	var objid1    = s[0];
	var nulltext  = s[3];
	var defvalue1 = s[4];
	build_minioption(objid1,dataARR1,nulltext,defvalue1);
	getid(objid1).onchange=function(){levelmenu2_chanage(this.value,clientstr,ajaxname);} 
	if (!empty(defvalue1)){SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&k=2&clientstr="+clientstr);}
}
function levelmenu2_chanage(thisvalue,clientstr,ajaxname) {
	var s = clientstr.split("|");
	var objid2    = s[1];
	var objid3    = s[2];
	var nulltext  = s[3];
	var defvalue1 = thisvalue;
	clientstr = '|'+objid2+'|'+objid3+'|'+nulltext+'|'+defvalue1+'||';
	if (thisvalue == defvalue1){getid(objid2).onchange=function(){levelmenu3_chanage(this.value,clientstr,ajaxname);}}
	getid(objid2).options.length = 0;getid(objid2).options.add(new Option(nulltext,"0"),1);
	getid(objid3).options.length = 0;getid(objid3).options.add(new Option(nulltext,"0"),1);
	if (!empty(thisvalue)){SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&k=2&clientstr="+clientstr);}
}
function levelmenu2(dataARR2,clientstr,ajaxname) {
	var dataARR2 = dataARR2.split(",");
	var s = clientstr.split("|");
	var objid2    = s[1];
	var nulltext  = s[3];
	var defvalue2 = s[5];
	build_minioption(objid2,dataARR2,nulltext,defvalue2);
	if (!empty(defvalue2)){SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&k=3&clientstr="+clientstr);}
}
function levelmenu3_chanage(thisvalue,clientstr,ajaxname) {
	var s = clientstr.split("|");
	var objid3    = s[2];
	var nulltext  = s[3];
	var defvalue1 = s[4];
	var defvalue2 = thisvalue;
	clientstr = '||'+objid3+'|'+nulltext+'|'+defvalue1+'|'+defvalue2+'|';
	getid(objid3).options.length = 0;getid(objid3).options.add(new Option(nulltext,"0"),1);
	if (!empty(thisvalue)){SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&k=3&clientstr="+clientstr);}
}
function levelmenu3(dataARR3,clientstr) {
	var dataARR3 = dataARR3.split(",");
	var s = clientstr.split("|");
	var objid3    = s[2];
	var nulltext  = s[3];
	var defvalue3 = s[6];
	build_minioption(objid3,dataARR3,nulltext,defvalue3);
}
function intto(objid,int1,int2,nulltext,defvalue) {
	build_select(objid,nulltext,'');
	var ar='';
	for(var i=int1; i<=int2; i++){
		ar += i+'|'+i;
		if (i != int2)ar += ',';
	}
	var arr = ar.split(",");
	build_minioption(objid,arr,nulltext,defvalue)
}
function checkboxcount(objname){
 	var n = 0;
	var obj = document.getElementsByName(objname+'[]');
	for(var k = 0;k<obj.length;k++){
		if (obj[k].checked){n++;console.log(n);}
	}
	return n;
}
function ifmaxcbxcnt(objname,objid,num){
 	var objname = objname.replace('[]','');
	var cbxcnt = checkboxcount(objname);
	
	if (cbxcnt > num){
		//alert('最多只能选择'+num+'项。');
		parent.ZEAI_win_alert('最多只能选择'+num+'项。')
		getid(objid).checked = false;
		getid(objid).focus;
		
	}
}
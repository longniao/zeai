/***************************************************
Copyright (C) 2017
未经本人同意，不得转载
作 者: 郭余林　QQ:797311 (supdes)
/*************************************************/
var nulltext = '请选择';
function LevelMenu3(clientstr,selectext) {
	var selectext = arguments[1] ? arguments[1]:'class="select SW"';
	var s = clientstr.split("|");
	var objid1    = s[0];
	var objid2    = s[1];
	var objid3    = s[2];
	var nulltext  = s[3];
	build_select(objid1,nulltext,selectext);document.write(' ');
	var selectext2= "onchange=levelmenu3_chanage(this.value,'"+clientstr+"')";
	build_select(objid2,nulltext,selectext+" "+selectext2);document.write(' ');
	build_select(objid3,nulltext,selectext);
	create1(clientstr);
}
function build_select(objid,nulltext,selectext) {
	var html='<select id="'+objid+'" name="'+objid+'" '+selectext+'>';
	html += '<option value="0">'+nulltext+'</option>';
	html += '</select>';
	document.write(html);
}
function build_option(objid,ARR,defvalue) {
	var value,text,sOption;
	var ifed = false;
    for( key in ARR){
		value = ARR[key].id;text = ARR[key].value;
        sOption       = document.createElement("OPTION");
        sOption.text  = text;
		sOption.value = value;
		o(objid).options.add(sOption);
		if(defvalue == value && !empty(defvalue)){sOption.id = "STmpId"+objid;var ifed = true;}
    }if (ifed)o("STmpId"+objid).selected = true;
}
function create1(clientstr) {
	var s = clientstr.split("|");
	var objid1    = s[0];
	var nulltext  = s[3];
	var defvalue1 = s[4];
	build_option(objid1,areaid_ARR1,defvalue1);
	o(objid1).onchange=function(){levelmenu2_chanage(this.value,clientstr);} 
	create2(clientstr);
}
function create2(clientstr) {
	var s = clientstr.split("|");
	var objid2    = s[1];
	var nulltext  = s[3];
	var defvalue1 = s[4];
	var defvalue2 = s[5];
	var defvalue3 = s[6];
	var dataARR2  = get_endARR(defvalue1,areaid_ARR2);
	build_option(objid2,dataARR2,defvalue2);
	create3(clientstr);
}
function create3(clientstr) {
	var s = clientstr.split("|");
	var objid2    = s[1];
	var objid3    = s[2];
	var nulltext  = s[3];
	var defvalue2 = s[5];
	var defvalue3 = s[6];
	var dataARR3  = get_endARR(defvalue2,areaid_ARR3);
	build_option(objid3,dataARR3,defvalue3);
}
function levelmenu2_chanage(Id1,clientstr) {
	var dataARR2 = get_endARR(Id1,areaid_ARR2);
	var s = clientstr.split("|");
	var objid2    = s[1];
	var objid3    = s[2];
	var nulltext  = s[3];
	var defvalue2 = s[5];
	o(objid2).options.length = 0;o(objid2).options.add(new Option(nulltext,"0"),true);
	o(objid3).options.length = 0;o(objid3).options.add(new Option(nulltext,"0"),true);
	build_option(objid2,dataARR2,defvalue2);
}
function levelmenu3_chanage(Id2,clientstr) {
	var dataARR3  = get_endARR(Id2,areaid_ARR3);
	var s = clientstr.split("|");
	var objid3    = s[2];
	var nulltext  = s[3];
	var defvalue3 = s[6];	
	o(objid3).options.length = 0;o(objid3).options.add(new Option(nulltext,"0"),true);
	build_option(objid3,dataARR3,defvalue3);
}
function get_endARR(Id1,areaid_ARR2){
	var tmpARR = [],newitem;
	for(var key2 in areaid_ARR2){
		id       = areaid_ARR2[key2].id;
		value    = areaid_ARR2[key2].value;
		parentId = areaid_ARR2[key2].parentId;
		if (Id1 == parentId){
			newitem = {'id':id,'value':value,'parentId':parentId};
			tmpARR.push(newitem);
		}
	}
	return tmpARR;
}
/*create form item*/



function create_radio_arr_list(objid,defvalue,selectext,ARR){
	var ARR       = arguments[3] ? arguments[3]:'';
	var selectext = arguments[2] ? arguments[2]:'class="RCW"';
	ARR = (empty(ARR))?eval(objid+'_ARR'):ARR;
	ARR.shift();
	document.write('<ul id="'+objid+'_box" name="'+objid+'" '+selectext+'>');
	
	for(var key in ARR){
		(function(key){
			id    = ARR[key].id;
			value = ARR[key].value;
			var li   = document.createElement('li');
			var obj    = document.createElement('input');
			var label = document.createElement('label');
			obj.type  ="radio";
			obj.className = 'radio';
			obj.id    = objid+id;
			obj.name  = objid;
			obj.value = id;
			obj.checked = in_array(id,defvalue);
			li.appendChild(obj);
			label.innerHTML = value;
			label.setAttribute("for",obj.id);
			if (objid == 'sex'){
				obj.onclick = function (){
					tag12(obj.value);
				}
			}
			li.appendChild(label);
			o(objid+'_box').appendChild(li);
		})(key);
	}
	
	
	document.write('</ul');
}
function create_checkbox_arr_list(objid,defvalue,selectext,ARR){
	var ARR       = arguments[3] ? arguments[3]:'';
	var selectext = arguments[2] ? arguments[2]:'class="RCW"';
	ARR = (empty(ARR))?eval(objid+'_ARR'):ARR;
	ARR.shift();
	document.write('<ul id="'+objid+'_box" name="'+objid+'" '+selectext+'>');
	var defv = defvalue.split(',');
	for( key in ARR){
		var id    = ARR[key].id;
		var text  = ARR[key].value;
		var li = document.createElement('li');
		var obj  = document.createElement('input');
		obj.type  ="checkbox";
		obj.className = 'checkbox';
		obj.id    = objid+id;
		obj.name  = objid;
		obj.value = id;
		obj.checked = in_array(id,defv);
		obj.onclick = function (){if (get_checked_num(objid) > 3){parent.ZEAI_alert('最多选择 3 项');return false;}};
		li.appendChild(obj);
		var label       = document.createElement('label');
		label.innerHTML = text;
		label.setAttribute("for",obj.id);
		li.appendChild(label);
		o(objid+'_box').appendChild(li);
	}
	document.write('</ul');
}
function create_select_arr_list(objid,defvalue,selectext,ARR){
	var ARR       = arguments[3] ? arguments[3]:'';
	var selectext = arguments[2] ? arguments[2]:'class="select SW"';
	ARR = (empty(ARR))?eval(objid+'_ARR'):ARR;
	ARR.shift();
	build_select(objid,nulltext,selectext);
	build_option(objid,ARR,defvalue);
}
function get_checked_num(objname){
	var n = 0;
	var obj = document.getElementsByName(objname);
	for(var k = 0;k<obj.length;k++){
		if (obj[k].checked)n++;
	}
	return n;	
}
function zeai_cn__CreateFormItem(type,objid,defvalue,selectext,ARR){
	switch (type) {
		case    'radio':create_radio_arr_list(objid,defvalue,selectext,ARR);break;
		case 'checkbox':create_checkbox_arr_list(objid,defvalue,selectext,ARR);break;
		case   'select':create_select_arr_list(objid,defvalue,selectext,ARR);break;
		default:break;
	}
}
function get_option(objid,t_){
	var obj = o(objid);
	var o_value = obj.options[obj.selectedIndex].value;
	var o_text  = obj.options[obj.selectedIndex].text;
	if (t_ == 'v'){
		return o_value;
	}else if(t_ == 't'){
		return o_text;
	}
}
function get_checked_value_list(objname){
	var list = '';
	var obj = document.getElementsByName(objname);
	for(var k = 0;k<obj.length;k++){
		if (obj[k].checked)list += obj[k].value + ',';
	}
	return rtrim(list);
}
//









/*
function intto(objid,int1,int2,nulltext,defvalue) {
	build_select(objid,nulltext,'');
	var ar='';
	for(var i=int1; i<=int2; i++){
		ar += i+'|'+i;
		if (i != int2)ar += ',';
	}
	var arr = ar.split(",");
	build_minioption(objid,arr,nulltext,defvalue)
}*/

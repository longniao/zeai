/***************************************************
Copyright (C) 2019
未经本人同意，不得转载
作 者: 郭余林　QQ:797311 (supdes)
/*************************************************/
var nulltext = '请选择';
function LevelMenu3(clientstr,selectext) {
	var selectext = arguments[1] ? arguments[1]:'class="SW"';
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
	html += '<option value="0" class="C999">'+nulltext+'</option>';
	html += '</select>';
	document.write(html);
}
function build_option(objid,ARR,defvalue) {
	var value,text,sOption;
	var ifed = false;
	for(var k=0;k<ARR.length;k++){
		value = ARR[k].i;text = ARR[k].v;
        sOption       = document.createElement("OPTION");
        sOption.text  = text;
		sOption.value = value;
		o(objid).options.add(sOption);
		if(defvalue == value && !zeai.empty(defvalue)){sOption.id = "STmpId"+objid;var ifed = true;}
    }if (ifed)o("STmpId"+objid).selected = true;
}
function create1(clientstr) {
	var s = clientstr.split("|");
	var objid1    = s[0];
	var nulltext  = s[3];
	var defvalue1 = s[4];
	build_option(objid1,areaARR1,defvalue1);
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
	var dataARR2  = get_endARR(defvalue1,areaARR2);
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
	var dataARR3  = get_endARR(defvalue2,areaARR3);
	build_option(objid3,dataARR3,defvalue3);
}
function levelmenu2_chanage(Id1,clientstr) {
	var dataARR2 = get_endARR(Id1,areaARR2);
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
	var dataARR3  = get_endARR(Id2,areaARR3);
	var s = clientstr.split("|");
	var objid3    = s[2];
	var nulltext  = s[3];
	var defvalue3 = s[6];	
	o(objid3).options.length = 0;o(objid3).options.add(new Option(nulltext,"0"),true);
	build_option(objid3,dataARR3,defvalue3);
}
function get_endARR(Id1,areaARR2){
	var tmpARR = [],newitem,i,v,f;
	for(var i2=0;i2<areaARR2.length;i2++){
		i = areaARR2[i2].i;
		v = areaARR2[i2].v;
		f = areaARR2[i2].f;
		if (Id1 == f){
			newitem = {'i':i,'v':v,'f':f};
			tmpARR.push(newitem);
		}
	}
	return tmpARR;
}
/*create form item*/
function create_radio_arr_list(objid,defvalue,selectext,ARR){
	var ARR       = arguments[3] ? arguments[3]:'';
	var selectext = arguments[2] ? arguments[2]:'class="RCW"';
	ARR = (zeai.empty(ARR))?eval(objid+'_ARR'):ARR;
	//ARR.shift();
	document.write('<ul id="'+objid+'_box" name="'+objid+'" '+selectext+'>');
	var defv = defvalue.split(',');
	for(var k=0;k<ARR.length;k++){
		id    = ARR[k].i;
		value = ARR[k].v;
		var li   = document.createElement('li');
		var obj    = document.createElement('input');
		var label = document.createElement('label');
		var i = document.createElement('i');i.className = 'i1';
		var b = document.createElement('b');b.innerHTML = value;b.className = '';
		obj.type  ="radio";obj.className = 'radioskin';obj.id = objid+id;obj.name = objid;obj.value = id;obj.checked = defv.in_array(id);
		label.setAttribute("for",obj.id);label.className = 'radioskin-label';label.appendChild(i);label.appendChild(b);
		li.appendChild(obj);
		li.appendChild(label);
		o(objid+'_box').appendChild(li);
	}
	document.write('</ul>');
}
function create_checkbox_arr_list(objid,defvalue,selectext,ARR){
	var ARR       = arguments[3] ? arguments[3]:'';
	var selectext = arguments[2] ? arguments[2]:'class="RCW"';
	ARR = (zeai.empty(ARR))?eval(objid+'_ARR'):ARR;
	document.write('<ul id="'+objid+'_box" name="'+objid+'" '+selectext+'>');
	var defv = defvalue.split(',');
	for(var k=0;k<ARR.length;k++){
		var id    = ARR[k].i;
		var text  = ARR[k].v;
		var li = document.createElement('li');
		var obj  = document.createElement('input');
		var label = document.createElement('label');
		var i = document.createElement('i');i.className = 'i1';
		var b = document.createElement('b');b.innerHTML = text;b.className = '';
		obj.type  ="checkbox";obj.className = 'checkskin';obj.id = objid+id;obj.name = objid+'[]';obj.value = id;obj.checked = defv.in_array(id);
		obj.onclick = function (){if (zeai.form.ifcheckbox(objid+'[]') > 3){zeai.msg('最多选择 3 项');return false;}};
		label.setAttribute("for",obj.id);label.className = 'checkskin-label';label.appendChild(i);label.appendChild(b);
		li.appendChild(obj);
		li.appendChild(label);
		o(objid+'_box').appendChild(li);
	}
	document.write('</ul>');
}
function create_select_arr_list(objid,defvalue,selectext,ARR){
	var ARR       = arguments[3] ? arguments[3]:'';
	var selectext = arguments[2] ? arguments[2]:'class="SW"';
	ARR = (zeai.empty(ARR))?eval(objid+'_ARR'):ARR;
	build_select(objid,nulltext,selectext);
	build_option(objid,ARR,defvalue);
}
function create_text_arr_list(objid,defvalue,selectext){
	var selectext = arguments[2] ? arguments[2]:'class="TW"';
	document.write('<input name="'+objid+'" type="text" id="'+objid+'" value="'+defvalue+'" '+selectext+'>');
}

function zeai_cn__CreateFormItem(type,objid,defvalue,selectext,ARR){
	switch (type) {
		case    'radio':create_radio_arr_list(objid,defvalue,selectext,ARR);break;
		case 'checkbox':create_checkbox_arr_list(objid,defvalue,selectext,ARR);break;
		case   'select':create_select_arr_list(objid,defvalue,selectext,ARR);break;
		case     'text':create_text_arr_list(objid,defvalue,selectext);break;
		default:break;
	}
}
function get_option(objid,t){
	var obj = o(objid);
	if (t == 'v'){
		return obj.options[obj.selectedIndex].value;
	}else if(t == 't'){
		return obj.options[obj.selectedIndex].text;
	}
}
function get_checked_value_list(objname){
	var list = [];
	var obj = document.getElementsByName(objname);
	for(var k = 0;k<obj.length;k++){
		if (obj[k].checked)list.push(obj[k].value);
	}
	return list.join(',');
}
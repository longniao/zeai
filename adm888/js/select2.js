/***************************************************
二级联动
Copyright (C) 2016
未经本人同意，不得转载
作 者: 郭余林　QQ:797311 (supdes)
***************************************************/
function build_select2(objid,nulltext,selectext) {
	var html='<select id="'+objid+'" name="'+objid+'" '+selectext+'>';
	html += '<option value="0">'+nulltext+'</option>';
	html += '</select>';
	document.write(html);
}
function build_minioption2(objid,arr,nulltext,defvalue) {
	var gyl,value,text,sOption;
	var ifed = false;
    for( key in arr){
		gyl  = arr[key].split("|"); 
		value = gyl[0];text = gyl[1];
        sOption = document.createElement("OPTION");
        sOption.text  = text;sOption.value = value;
		if(defvalue == value && ifint(defvalue,"0-9","1,5") && !empty(defvalue)){sOption.id = "STmpId2"+objid;var ifed = true;}
        getid(objid).options.add(sOption);
    }
	if (ifed) getid("STmpId2"+objid).selected = true;
}
function LevelMenu2(ajaxname,clientstr,selectext) {
	var s = clientstr.split("|");
	var objid1    = s[0];
	var objid2    = s[1];
	var nulltext  = s[2];
	build_select2(objid1,nulltext,selectext);document.write(' ');
	build_select2(objid2,nulltext,selectext);document.write(' ');
	SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&k=1&clientstr="+clientstr);
}
function levelmenu12(dataARR1,clientstr,ajaxname) {
	var dataARR1 = dataARR1.split(",");
	var s = clientstr.split("|");
	var objid1    = s[0];
	var nulltext  = s[2];
	var defvalue1 = s[3];
	build_minioption2(objid1,dataARR1,nulltext,defvalue1);
	getid(objid1).onchange=function(){levelmenu2_chanage2(this.value,clientstr,ajaxname);} 
	if (!empty(defvalue1)){SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&k=2&clientstr="+clientstr);}
}
function levelmenu2_chanage2(thisvalue,clientstr,ajaxname) {
	var s = clientstr.split("|");
	var objid2    = s[1];
	var nulltext  = s[2];
	var defvalue1 = thisvalue;
	clientstr = '|'+objid2+'|'+nulltext+'|'+defvalue1+'|';
	getid(objid2).options.length = 0;getid(objid2).options.add(new Option(nulltext,"0"),1);
	if (!empty(thisvalue)){SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&k=2&clientstr="+clientstr);}
}
function levelmenu22(dataARR2,clientstr) {
	var dataARR2 = dataARR2.split(",");
	var s = clientstr.split("|");
	var objid2    = s[1];
	var nulltext  = s[2];
	var defvalue2 = s[4];
	build_minioption2(objid2,dataARR2,nulltext,defvalue2);
}
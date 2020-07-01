/***************************************************
Copyright (C) 2016
未经本人同意，不得转载
作 者: 郭余林　QQ:797311 (supdes)
***************************************************/
function build_select1(objid,nulltext,selectext) {
	var html='<select id="'+objid+'" name="'+objid+'" '+selectext+'>';
	html += '<option value="0">'+nulltext+'</option>';
	html += '</select>';
	document.write(html);
}
function build_minioption1(objid,arr,nulltext,defvalue) {
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
function LevelMenu1(ajaxname,clientstr,selectext) {
	var s = clientstr.split("|");
	var objid1    = s[0];
	var nulltext  = s[1];
	build_select1(objid1,nulltext,selectext);document.write(' ');
	SendData(ajxpath+ajaxname+ajxext+"ajaxname="+ajaxname+"&clientstr="+clientstr);
}
function levelmenu11(dataARR1,clientstr,ajaxname) {
	var dataARR1 = dataARR1.split(",");
	var s = clientstr.split("|");
	var objid1    = s[0];
	var nulltext  = s[1];
	var defvalue1 = s[2];
	build_minioption1(objid1,dataARR1,nulltext,defvalue1);
}

/***************************************************
联动select
Copyright (C) 2019 www.zeai.cn
未经本人同意，不得转载
作 者: 郭余林　QQ:797311 (supdes)
***************************************************/
var Sbindbox='';
document.write('<link href="../res/zeai_ios_select/separate/iosSelect.css?4" rel="stylesheet" type="text/css" />');
document.write('<script src="../res/zeai_ios_select/separate/zepto.js"></script>');
document.write('<script src="../res/iscroll.js"></script>');
document.write('<script src="../res/zeai_ios_select/separate/iosSelect.js?4"></script>');
/*function area_select(title,objid,objid_def,level,objid_title){
	level       = arguments[3] ? arguments[3]:3;
	objid_title = arguments[4] ? arguments[4]:objid+'_str';
	if (!ifobj(objid+'_tmpdef'))createtag('input',objid+'_tmpdef',objid_def);
    var selectContactDom       = $('#'+objid);
    var showContactDom         = $('#'+objid_title);
    selectContactDom.bind('click', function () {
		var tmpobj = o(objid+'_tmpdef');
		if (tmpobj.value != objid_def && !empty(tmpobj.value))objid_def = tmpobj.value;
		var defobj = objid_def.split(',');
		var oneLevelId   = defobj[0];
		var twoLevelId   = defobj[1];
		var threeLevelId = defobj[2];
        var iosSelect = new IosSelect(level,[areaARR1, areaARR2, areaARR3],{
			title: title,
			itemHeight:35,
			oneTwoRelation:1,
			twoThreeRelation:1,
			oneLevelId:oneLevelId,
			twoLevelId:twoLevelId,
			threeLevelId:threeLevelId,
			callback: function (selectOneObj, selectTwoObj, selectThreeObj ) {
				var areaid    = selectOneObj.i + ',' + selectTwoObj.i + ',' + selectThreeObj.i;
				var areatitle = selectOneObj.v + ' ' + selectTwoObj.v + ' ' + selectThreeObj.v;
				

					showContactDom.html(selectOneObj.value + ' ' + selectTwoObj.value + ' ' + selectThreeObj.value);
					//var jsn = '{"'+objid+'":"'+areaidV+'","areatitle":"'+areatitleV+'"}';
					//Send_XML_select('iframe'+ajxext,objid,jsn);
					//tmpobj.value = v1 + ',' + v2 + ',' + v3;

				
				tmpobj.value = areaid;
				
				
			}
		});
    });
}*/
function ios_select_area(title,ARR1,ARR2,ARR3,defvalue,fn,fg){
	level=3
	if (!zeai.empty(defvalue)){
		var defobj = defvalue.split(fg);
		var oneLevelId   = defobj[0];
		var twoLevelId   = defobj[1];
		var threeLevelId = defobj[2];
	}
	var iosSelect = new IosSelect(level,[ARR1, ARR2, ARR3],{
		title: title,
		itemHeight:35,
		oneTwoRelation:1,
		twoThreeRelation:1,
		oneLevelId:oneLevelId,
		addClassName:'',
		twoLevelId:twoLevelId,
		threeLevelId:threeLevelId,
		callback: function (selectOneObj, selectTwoObj, selectThreeObj){fn(selectOneObj, selectTwoObj, selectThreeObj);}
	});
}

function ios_select1_normal(title,ARR1,defvalue,fn){
	level=1
	if (!zeai.empty(defvalue)){
		var oneLevelId = defvalue;
	}
	var iosSelect = new IosSelect(level,[ARR1],{
		title:title,
		itemHeight:35,
		oneTwoRelation:0,
		twoThreeRelation:0,
		oneLevelId:oneLevelId,
		addClassName:'',
		twoLevelId:0,
		threeLevelId:0,
		callback: function (selectOneObj){fn(selectOneObj);}
	});
}


function ios_select_next(cls,ARR1,ARR2,ARR3,defvalue,fn,fg){
	var title='',level=3
	if (!zeai.empty(defvalue)){
		var defobj = defvalue.split(fg);
		var oneLevelId   = defobj[0];
		var twoLevelId   = defobj[1];
		var threeLevelId = defobj[2];
	}
	var iosSelect = new IosSelect(level,[ARR1, ARR2, ARR3],{
		title: title,
		itemHeight:35,
		oneTwoRelation:1,
		twoThreeRelation:1,
		oneLevelId:oneLevelId,
		addClassName:cls,
		twoLevelId:twoLevelId,
		threeLevelId:threeLevelId,
		callback: function (selectOneObj, selectTwoObj, selectThreeObj){fn(selectOneObj, selectTwoObj, selectThreeObj);}
	});
}
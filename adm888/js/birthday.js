/**************************************************
Copyright (C) 2016 www.zeai.cn (ZEAI5.0)
程序开发：郭余林　QQ:797311 (supdes)
**************************************************/
function birthday_select(title,objid,objid_def,objid_title){
	objid_title = arguments[3] ? arguments[3]:objid+'_str';
	if (!ifobj(objid+'_tmpdef'))createtag('input',objid+'_tmpdef',objid_def);
	var selectDateDom       = $('#'+objid);
	var showDateDom         = $('#'+objid_title);
	// 初始化时间
	var now = new Date();
	var nowYear = now.getFullYear();
	//var nowMonth = now.getMonth() + 1;
	//var nowDate = now.getDate();
	// 数据初始化
	function formatYear (nowYear) {
		var arr = [];
		for (var i = nowYear - 70; i <= nowYear - 18; i++) {
			var istr = (i<10)?'0'+i:''+i;
			arr.push({
				'id':istr + '',
				'value':istr + '年'
			});
		}
		return arr;
	}
	function formatMonth () {
		var arr = [];
		for (var i = 1; i <= 12; i++) {
			var istr = (i<10)?'0'+i:''+i;
			arr.push({
				'id':istr + '',
				'value':istr + '月'
			});
		}
		return arr;
	}
	function formatDate (count) {
		var arr = [];
		for (var i = 1; i <= count; i++) {
			var istr = (i<10)?'0'+i:''+i;
			arr.push({
				'id':istr + '',
				'value':istr + '日'
			});
		}
		return arr;
	}
	var yearData = formatYear(nowYear);
	var monthData = function () {
		return formatMonth();
	};
	var dateData = function (year, month) {
		
		if (/^01|03|05|07|08|1|3|5|7|8|10|12$/.test(month)) {
			return formatDate(31);
		}else if (/^04|06|09|4|6|9|11$/.test(month)) {
			return formatDate(30);
		}else if (/^02|2$/.test(month)) {
			if (year % 4 === 0 && year % 100 !==0 || year % 400 === 0) {
				return formatDate(29);
			}else {
				return formatDate(28);
			}
		}else {
			throw new Error('month is illegal');
		}
	};
	selectDateDom.bind('click', function () {
		var tmpobj = getid(objid+'_tmpdef');
		if (tmpobj.value != objid_def && !empty(tmpobj.value))objid_def = tmpobj.value;
		var defobj = objid_def.split('-');
		var oneLevelId   = defobj[0];
		var twoLevelId   = defobj[1];
		var threeLevelId = defobj[2];
		var iosSelect = new IosSelect(3,[yearData, monthData, dateData],{
			title: title,
			itemHeight: 35,
			oneTwoRelation: 1,
			twoThreeRelation: 1,
			oneLevelId: oneLevelId,
			twoLevelId: twoLevelId,
			threeLevelId: threeLevelId,
			callback: function (selectOneObj, selectTwoObj, selectThreeObj) {
				var savevalue = selectOneObj.id + '-' + selectTwoObj.id + '-' + selectThreeObj.id;
				var showalue  = selectOneObj.value + '-' + selectTwoObj.value + '-' + selectThreeObj.value;
				Send_XML_select('iframe'+ajxext,objid,savevalue);
				showDateDom.html(savevalue);
				tmpobj.value = savevalue;
			}
		});
	});	
}



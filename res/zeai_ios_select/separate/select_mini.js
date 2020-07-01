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
	level=1;
	if (!zeai.empty(defvalue)){
		var oneLevelId = defvalue;
	}
	var iosSelect = new IosSelect(level,[ARR1],{
		title:title,
		itemHeight:35,
		oneTwoRelation:0,
		twoThreeRelation:0,
		oneLevelId:oneLevelId,
		addClassName:'btm',
		twoLevelId:0,
		threeLevelId:0,
		callback: function (selectOneObj){fn(selectOneObj);}
	});
}
function ios_select2_range(title,ARR1,ARR2,defvalue,fn,fg){
	level=2
	if (!zeai.empty(defvalue)){
		var defobj = defvalue.split(fg);
		var oneLevelId = parseInt(defobj[0]);
		var twoLevelId = parseInt(defobj[1]);
		var threeLevelId = 0;
	}
	var iosSelect = new IosSelect(level,[ARR1, ARR2],{
		title:title,
		oneLevelId:oneLevelId,
		twoLevelId:twoLevelId,
		threeLevelId:0,
		itemHeight:35,
		oneTwoRelation:0,
		twoThreeRelation:0,
		addClassName:'',
		callback: function (selectOneObj, selectTwoObj){fn(selectOneObj, selectTwoObj);}
	});
}

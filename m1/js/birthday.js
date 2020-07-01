var now = new Date();
var nowYear = now.getFullYear();
function formatYear (nowYear) {
	var arr = [];
	for (var i = nowYear - 70; i <= nowYear - 18; i++) {
		var istr = (i<10)?'0'+i:''+i;
		arr.push({
			'i':istr + '',
			'v':istr + '年'
		});
	}
	return arr;
}
function formatMonth () {
	var arr = [];
	for (var i = 1; i <= 12; i++) {
		var istr = (i<10)?'0'+i:''+i;
		arr.push({
			'i':istr + '',
			'v':istr + '月'
		});
	}
	return arr;
}
function formatDate (count) {
	var arr = [];
	for (var i = 1; i <= count; i++) {
		var istr = (i<10)?'0'+i:''+i;
		arr.push({
			'i':istr + '',
			'v':istr + '日'
		});
	}
	return arr;
}
var yearData = formatYear(nowYear);
var monthData = function () {return formatMonth();};
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
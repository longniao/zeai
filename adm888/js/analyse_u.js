window.onload=function(){
	//用户总量增长分析
	function tubiao_reg(categories,series){
		var chart = Highcharts.chart('reg_box', {
			chart: {type: 'column'},//line
			title: {text: ''},
			subtitle: {text: ''},
			credits: {"enabled": false},
			xAxis: {categories:categories},
			yAxis: {title: {text: '注册人数占比（人）'}},
			
			tooltip: {
				// head + 每个 point + footer 拼接成完整的 table
				headerFormat: '<span style="font-size:14px;font-weight:bold">{point.key}</span><table>',
				pointFormat: '<tr><td style="font-size:14px;color:{series.color};padding:2px">{series.name}: </td>' +
				'<td style="padding:0">本月：<b>{point.y:f} 人</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				line: {
					dataLabels: {enabled: true},//开启数据标签
					// 关闭鼠标跟踪，对应的提示框、点击事件会失效
					enableMouseTracking: true
				}
			},
			colors: ["#FF6F6F","#7CC7C0","#F7A35C","#95CEFF","#009688","#FD66B5","#38C5FF","#FEA2C8","#7CC7C0","#ffab4a","#90C9FB","#ff6600","#0099cc","#ff9900","#0000ff","#0000cc"],
			series:series,
			legend: {verticalAlign:"top"}
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_reg',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var series = rs.series;
				for(var k=0;k<series.length;k++) {
					series[k].data = eval('['+series[k].data+']');
				}
				tubiao_reg(rs.categories,series);
			}
		});
	},100);
	
	
	//用户男女占比分析
	function tubiao_sex(bfb1,bfb2,num1,num2){
		Highcharts.chart('sexbox', {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {text: ''},
			tooltip: {pointFormat: '<font style="font-weight:normal;color:#666">{series.name}<br>{point.percentage:.0f}%</font>'},
			credits: {"enabled": false},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b style="font-size:12px;font-weight:normal;color:#666">{point.name}</b><br><br><br><b style="font-size:14px;font-weight:normal;color:#666">{point.percentage:.0f} %</b>',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
					}
				}
			},
			colors: ["#95CEFF","#FEA2C8","#ffab4a","#90C9FB"],
			series: [{
				name: '',
				colorByPoint: true,
				data: [{
					name: '男：'+num1+'人',y:bfb1
				}, {
					name: '女：'+num2+'人',y:bfb2,
					sliced: true//selected: true
				}]
			}]
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_sex',data:{sDATE1:sDATE1,agentid:agentid}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				var bfb1=parseInt(rs.bfb1),bfb2=parseInt(rs.bfb2);
				tubiao_sex(bfb1,bfb2,rs.num1,rs.num2);
			}
		});
	},1000);
	
	
	//婚姻
	function tubiao_love(categories,series){
		var chart = Highcharts.chart('lovebox',{
			chart: {type: 'column'},
			title: {text: ''},
			credits: {"enabled": false},
			subtitle: {text: ''},
			xAxis: {categories:categories,crosshair:true},
			yAxis: {min: 0,title: {text: '婚姻状况人数占比'}},
			tooltip: {
				// head + 每个 point + footer 拼接成完整的 table
				headerFormat: '<span style="font-size:18px;font-weight:bold">{point.key}</span><table>',
				pointFormat: '<tr><td style="font-size:14px;color:{series.color};padding:2px">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:f} 人</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {column: {borderWidth:1,}},
			colors: ["#95CEFF","#FEA2C8","#ffab4a","#90C9FB"],
			series:series,
			legend: {verticalAlign:"top"}
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_love',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var series = rs.series;
				for(var k=0;k<series.length;k++) {
					series[k].data = eval('['+series[k].data+']');
				}
				tubiao_love(rs.categories,series);
			}
		});
	},1500);
	
	//用户学历分析
	function tubiao_edu(data){
		Highcharts.chart('edubox', {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {text: ''},
			tooltip: {pointFormat: '<font style="font-weight:normal;color:#666">{series.name}<br>{point.percentage:.2f}%</font>'},
			credits: {"enabled": false},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b style="font-size:12px;font-weight:normal;color:#666">{point.name}</b><br><br><br><b style="font-size:12px;font-weight:normal;color:#666">占比：{point.percentage:.2f} %</b>',
						style: {color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'}
					}
				}
			},
			colors: ["#FF6F6F","#7CC7C0","#F7A35C","#95CEFF","#009688","#FEA2C8","#38C5FF","#FEA2C8","#7CC7C0","#ffab4a","#90C9FB","#ff6600","#0099cc","#ff9900","#0000ff","#0000cc"],
			series: [{
				name: '',
				colorByPoint: true,
				data:data
			}]
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_edu',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var yarr = rs.data;
				for(var k=0;k<yarr.length;k++) {
					yarr[k].y = parseFloat(yarr[k].y);
				}
				tubiao_edu(yarr);
			}
		});
	},2000);
	
	//用户学历分析VIP
	function tubiao_edu_vip(data){
		Highcharts.chart('edubox_vip', {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {text: ''},
			tooltip: {pointFormat: '<font style="font-weight:normal;color:#666">{series.name}<br>{point.percentage:.2f}%</font>'},
			credits: {"enabled": false},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b style="font-size:12px;font-weight:normal;color:#666">{point.name}</b><br><br><br><b style="font-size:12px;font-weight:normal;color:#666">占比：{point.percentage:.2f} %</b>',
						style: {color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'}
					}
				}
			},
			colors: ["#FF6F6F","#7CC7C0","#F7A35C","#95CEFF","#009688","#FEA2C8","#38C5FF","#FEA2C8","#7CC7C0","#ffab4a","#90C9FB","#ff6600","#0099cc","#ff9900","#0000ff","#0000cc"],
			series: [{
				name: '',
				colorByPoint: true,
				data:data
			}]
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_edu_vip',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var yarr = rs.data;
				for(var k=0;k<yarr.length;k++) {
					yarr[k].y = parseFloat(yarr[k].y);
				}
				tubiao_edu_vip(yarr);
			}
		});
	},2500);
	
	
	//housebox
	function tubiao_house(data){
		Highcharts.chart('housebox', {
			chart: {type: 'column'},
			title: {text: '　'},
			subtitle: {text: ''},
			credits: {"enabled": false},
			xAxis: {
				type: 'category',
				labels: {
					rotation: 0,
					style: {fontSize: '12px',fontFamily: 'Verdana, sans-serif'}
				}
			},
			yAxis: {min: 0,title: {text:'住房占比人数'}},
			legend: {enabled: false},
			tooltip: {pointFormat: '<b style="font-weight:normal;color:#666">{point.y:.0f}人</b>'},
			colors: ["#95CEFF"],
			series: [{
				name: 'zea.cn',
				data: data,
				dataLabels: {
					enabled: true,
					rotation:0,
					align: 'center',
					format: '{point.y:.0f}人', // one decimal
					y:0, //10 pixels down from the top
					style:{fontSize:'12px',fontWeight:'normal',fontFamily:'Arial,sans-serif'}
				}
			}]
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_house',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var arr=e;
				arr = arr.replace(/"/g,"");
				arr = zeai.jsoneval(arr);
				tubiao_house(arr);
			}
		});
	},3000);


	//用户购车分析
	function tubiao_car(data){
		Highcharts.chart('carbox', {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {text: ''},
			tooltip: {pointFormat: '<font style="font-weight:normal;color:#666">{series.name}<br>{point.percentage:.2f}%</font>'},
			credits: {"enabled": false},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b style="font-size:12px;font-weight:normal;color:#666">{point.name}</b><br><br><br><b style="font-size:12px;font-weight:normal;color:#666">占比：{point.percentage:.2f} %</b>',
						style: {color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'}
					}
				}
			},
			colors: ["#FF6F6F","#7CC7C0","#F7A35C","#95CEFF","#009688","#FEA2C8","#38C5FF","#FEA2C8","#7CC7C0","#ffab4a","#90C9FB","#ff6600","#0099cc","#ff9900","#0000ff","#0000cc"],
			series: [{
				name: '',
				colorByPoint: true,
				data:data
			}]
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_car',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var yarr = rs.data;
				for(var k=0;k<yarr.length;k++) {
					yarr[k].y = parseFloat(yarr[k].y);
				}
				tubiao_car(yarr);
			}
		});
	},3500);

	//用户年龄分析
	function tubiao_age(categories,series){
		var chart = Highcharts.chart('agebox',{
			chart: {type: 'column'},
			title: {text: ''},
			credits: {"enabled": false},
			subtitle: {text: ''},
			xAxis: {categories:categories,crosshair:true},
			yAxis: {min: 0,title: {text: '年龄段人数占比'}},
			tooltip: {
				// head + 每个 point + footer 拼接成完整的 table
				headerFormat: '<span style="font-size:18px;font-weight:bold">{point.key}</span><table>',
				pointFormat: '<tr><td style="font-size:14px;color:{series.color};padding:2px">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:f} 人</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {column: {borderWidth:1,}},
			colors: ["#95CEFF","#FEA2C8"],
			series:series,
			legend: {verticalAlign:"top"}
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_age',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var series = rs.series;
				for(var k=0;k<series.length;k++) {
					series[k].data = eval('['+series[k].data+']');
				}
				tubiao_age(rs.categories,series);
			}
		});
	},4000);

	//用户VIP升级类型
	function tubiao_vipkind(categories,series){
		var chart = Highcharts.chart('vipkindbox',{
			chart: {type: 'column'},
			title: {text: ''},
			credits: {"enabled": false},
			subtitle: {text: ''},
			xAxis: {categories:categories,crosshair:true},
			yAxis: {min: 0,title: {text: 'VIP升级人数占比'}},
			tooltip: {
				// head + 每个 point + footer 拼接成完整的 table
				headerFormat: '<span style="font-size:18px;font-weight:bold">{point.key}</span><table>',
				pointFormat: '<tr><td style="font-size:14px;color:{series.color};padding:2px">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:2f} 人</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {column: {borderWidth:1,}},
			colors: ["#95CEFF","#FEA2C8"],
			series:series,
			legend: {verticalAlign:"top"}
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_vipkind',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var series = rs.series;
				for(var k=0;k<series.length;k++) {
					series[k].data = eval('['+series[k].data+']');
				}
				tubiao_vipkind(rs.categories,series);
			}
		});
	},4500);


	//用户帐号状态分析
	function tubiao_flag(categories,series){
		var chart = Highcharts.chart('flagbox',{
			chart: {type: 'column'},
			title: {text: ''},
			credits: {"enabled": false},
			subtitle: {text: ''},
			xAxis: {categories:categories,crosshair:true},
			yAxis: {min: 0,title: {text: '帐号状态人数占比'}},
			tooltip: {
				// head + 每个 point + footer 拼接成完整的 table
				headerFormat: '<span style="font-size:18px;font-weight:bold">{point.key}</span><table>',
				pointFormat: '<tr><td style="font-size:14px;color:{series.color};padding:2px">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:2f} 人</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {column: {borderWidth:1,}},
			colors: ["#95CEFF","#FEA2C8"],
			series:series,
			legend: {verticalAlign:"top"}
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_flag',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var series = rs.series;
				for(var k=0;k<series.length;k++) {
					series[k].data = eval('['+series[k].data+']');
				}
				tubiao_flag(rs.categories,series);
			}
		});
	},5000);
}

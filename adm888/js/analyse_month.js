window.onload=function(){
	function tubiao_pay_month(categories,series){
		var chart = Highcharts.chart('pay_month_box',{
			chart: {type: 'column'},
			title: {text: ''},
			credits: {"enabled": false},
			subtitle: {text: ''},
			xAxis: {categories:categories,crosshair:true},
			yAxis: {min: 0,title: {text: '充值（元）'}},
			tooltip: {
				// head + 每个 point + footer 拼接成完整的 table
				headerFormat: '<span style="font-size:14px;font-weight:bold">{point.key}</span><table>',
				pointFormat: '<tr><td style="font-size:14px;color:{series.color};padding:2px">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:f} 元</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {column: {borderWidth:1,}},
			colors: ["#EE5D51"],
			series:series,
			legend: {verticalAlign:"top"}
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_pay_month',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var series = rs.series;
				for(var k=0;k<series.length;k++) {
					series[k].data = eval('['+series[k].data+']');
				}
				tubiao_pay_month(rs.categories,series);
			}
		});
	},100);
	//reg_month_box
	function tubiao_reg_month(arr){
		Highcharts.chart('reg_month_box', {
			chart: {type: 'column'},
			title: {text: '　'},
			subtitle: {text: ''},
			credits: {"enabled": false},
			xAxis: {
				type: 'category',
				labels: {
					rotation: -45,
					style: {fontSize: '12px',fontFamily: 'Verdana, sans-serif'}
				}
			},
			yAxis: {min: 0,title: {text:'本月'+daynum+'天会员注册量'}},
			legend: {enabled: false},
			tooltip: {pointFormat: '<b style="font-weight:normal;color:#666">注册{point.y:.0f}人</b>'},
			colors: ["#7CC7C0"],
			series: [{
				name: 'zea.cn',
				data: arr,
				dataLabels: {
					enabled: true,
					rotation:0,
					color: '#ffffff',
					align: 'center',
					format: '{point.y:.0f}', // one decimal
					y:22, //10 pixels down from the top
					style:{fontSize:'12px',fontFamily:'Arial,sans-serif'}
				}
			}]
		});
	}
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_reg_month',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var arr=e;
				arr = arr.replace(/"/g,"");
				arr = zeai.jsoneval(arr);
				tubiao_reg_month(arr);
			}
		});
	},300);
}
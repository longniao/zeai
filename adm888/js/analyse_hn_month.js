window.onload=function(){

//paydatebox
	function paydate(categories,series){
		var chart = Highcharts.chart('paydatebox',{
			chart: {type: 'column'},
			title: {text: ''},
			credits: {"enabled": false},
			colors: ["#FF6F6F","#7CC7C0","#F7A35C","#95CEFF","#009688","#FD66B5","#38C5FF","#FEA2C8","#7CC7C0","#ffab4a","#90C9FB","#ff6600","#0099cc","#ff9900","#0000ff","#0000cc"],
			subtitle: {text: ''},
			xAxis: {categories:categories,crosshair:true},
			yAxis: {min: 0,title: {text: '充值 (元)'}},
			tooltip: {
				// head + 每个 point + footer 拼接成完整的 table
				headerFormat: '<span style="font-size:12px;font-weight:bold">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:2px">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.1f} 元</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {column: {borderWidth:1}},
			series:series,
			legend: {verticalAlign:"top"}
		});
	}
	//paydate
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_hn_paydate',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				var series = rs.series;
				for(var k=0;k<series.length;k++) {
					series[k].data = eval('['+series[k].data+']');
				}
				paydate(rs.categories,series);
			}
		});
	},500);

	
	//hn_pay老
	function hn_pay(categories,data){
		var chart = Highcharts.chart('container', {
			chart: {type: 'bar'},//column
			title: {text: ''},
			subtitle: {text: ''},
			credits: {"enabled": false},
			colors: ["#FF6F6F","#38C5FF","#FEA2C8","#7CC7C0","#ffab4a","#90C9FB"],
			xAxis: {
				categories:categories,
				title: {text: null},
			},
			series:[{name: '认领会员充值(元)',data:data}],
			yAxis: {
				min: 0,
				title:{text: '红娘充值 (元)',align: 'high'},
				labels: {overflow: 'justify'}
			},
			tooltip: {valueSuffix: ' 元'},
			plotOptions: {
				bar: {dataLabels: {enabled: true,allowOverlap:true}}
			},
			legend: {
				layout: 'vertical',
				align: 'right',
				verticalAlign: 'top',
				x: -40,y: 0,
				floating: true,
				borderWidth: 1,
				backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
				shadow: true
			}
		});
	}
	//hn_pay老
	setTimeout(function(){
		zeai.ajax({url:jsfilename+zeai.ajxext+'submitok=ajax_hn_pay',data:{sDATE1:sDATE1,agentid:agentid}},function(e){
			if(!zeai.empty(e)){
				var rs=zeai.jsoneval(e);
				data = eval('['+rs.data+']');
				hn_pay(rs.categories,data);
			}
		});
	},200);

}

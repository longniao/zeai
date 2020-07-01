window.onload=function(){
	function user1(u1,u2,num1,num2){
		Highcharts.chart('container_user1', {
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
						format: '<b style="font-weight:normal;color:#666">{point.name}</b><br><b style="font-weight:normal;color:#666">占比：{point.percentage:.0f} %</b>',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
					}
				}
			},
			colors: ["#7CC7C0","#FEA2C8","#ffab4a","#90C9FB"],
			series: [{
				name: '',
				colorByPoint: true,
				data: [{
					name: '男生：'+num1+'人',
					y:u1
					//sliced: true,
					//selected: true
				}, {
					name: '女生：'+num2+'人',
					y:u2,
					sliced: true//selected: true
				}]
			}]
		});
	}
	
	function user2(arr){
		Highcharts.chart('container_user2', {
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
						format: '<b style="font-weight:normal;color:#666">{point.name}</b><br><b style="font-weight:normal;color:#666">占比：{point.percentage:.2f} %</b>',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
					}
				}
			},
			colors: ["#7CC7C0","#FEA2C8","#ffab4a","#90C9FB","#ffab4a","#90C9FB","#ff6600","#0099cc","#ff9900","#0000ff","#0000cc"],
			series: [{
				name: '',
				colorByPoint:true,
				data:arr
			}]
		});
	}
	function reg(arr){
		Highcharts.chart('container2', {
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
			yAxis: {min: 0,title: {text:'最近10天会员注册量'}},
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

	//Uflag 
	setTimeout(function(){
		zeai.ajax({url:'welcome'+zeai.ajxext+'submitok=ajax_dataflag'},function(e){rs=zeai.jsoneval(e);
			var F1V=rs.F['f1'],F2V=rs.F['f2'],F3V=rs.F['f3'],F4V=rs.F['f4'],F5V=rs.F['f5'],F6V=rs.F['f6'],F7V=rs.F['f7'],F8V=rs.F['f8'],F9V=rs.F['f9'];
			setTimeout(function(){F1.html(F1V);},100);
			setTimeout(function(){F2.html(F2V);},200);
			setTimeout(function(){F3.html(F3V);},300);
			setTimeout(function(){F4.html(F4V);},400);
			setTimeout(function(){F5.html(F5V);},500);
			setTimeout(function(){F6.html(F6V);},600);
			setTimeout(function(){F7.html(F7V);},700);
			setTimeout(function(){F8.html(F8V);},800);
			setTimeout(function(){F9.html(F9V);},900);
		});
	},100);

	//U
	setTimeout(function(){
		zeai.ajax({url:'welcome'+zeai.ajxext+'submitok=ajax_U'},function(e){rs=zeai.jsoneval(e);
			var U1V=rs.U['u1'],U2V=rs.U['u2'],U3V=rs.U['u3'],U4V=rs.U['u4'],
			U5V=rs.U['u5'],U6V=rs.U['u6'],U7V=rs.U['u7'],U8V=rs.U['u8'];
			setTimeout(function(){U1.html(U1V);},100);
			setTimeout(function(){U2.html(U2V);},200);
			setTimeout(function(){U3.html(U3V);},300);
			setTimeout(function(){U4.html(U4V);},400);
			setTimeout(function(){U5.html(U5V);},500);
			setTimeout(function(){U6.html(U6V);},600);
			setTimeout(function(){U7.html(U7V);},700);
			setTimeout(function(){U8.html(U8V);},800);
			
		});
	},300);
	
	//user1 tubiao1
	setTimeout(function(){
		zeai.ajax({url:'welcome'+zeai.ajxext+'submitok=ajax_user1'},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				var u1=parseInt(rs.user1),u2=parseInt(rs.user2),num1=parseInt(rs.num1),num2=parseInt(rs.num2);
				user1(u1,u2,num1,num2);
			}
		});
	},550);
	
	//user2 tubiao2
	setTimeout(function(){
		zeai.ajax({url:'welcome'+zeai.ajxext+'submitok=ajax_user2'},function(e){
			if(!zeai.empty(e)){
				var arr=zeai.jsoneval(e);
				var parent=[],son=[];
				for(var j=0;j<arr.length;j++){
					var son=[];
					for( key in arr[j]){
						if(key=='y'){
							var str=parseFloat(arr[j][key]);
						}else{
							var str=arr[j][key];
						}
						son.push(str);
					}
					parent.push(son);
				}
				user2(parent);
			}
		});
	},700);
	
	
	//TG
	setTimeout(function(){
		zeai.ajax({url:'welcome'+zeai.ajxext+'submitok=ajax_TG'},function(e){rs=zeai.jsoneval(e);
			var TG1V=rs.TG['tg1'],TG2V=rs.TG['tg2'],TG3V=rs.TG['tg3'],TG4V=rs.TG['tg4'],WZBBSV=rs.TG['wzbbs'];
			setTimeout(function(){TG1.html(TG1V);},100);
			setTimeout(function(){TG2.html(TG2V);},200);
			setTimeout(function(){TG3.html(TG3V);},300);
			setTimeout(function(){TG4.html(TG4V);},400);
			setTimeout(function(){WZBBS.html(WZBBSV);},400);
			
		});
	},900);
	//SY
	setTimeout(function(){
		zeai.ajax({url:'welcome'+zeai.ajxext+'submitok=ajax_SY'},function(e){rs=zeai.jsoneval(e);
			var syYcurV=rs.SY['syYcur'],syMbefV=rs.SY['syMbef'],syMcurV=rs.SY['syMcur'],syAllcurV=rs.SY['syAllcur'],syDcurV=rs.SY['syDcur'],
			SY0V=rs.SY['sy0'],SY1V=rs.SY['sy1'],SY2V=rs.SY['sy2'],SY3V=rs.SY['sy3'],SY4V=rs.SY['sy4'],SY5V=rs.SY['sy5'],SY6V=rs.SY['sy6'],SY7V=rs.SY['sy7'];
			//setTimeout(function(){SY0.html(SY0V);},100);
			setTimeout(function(){syAllcur.html(syAllcurV);},100);
			setTimeout(function(){syYcur.html(syYcurV);},200);
			setTimeout(function(){syMbef.html(syMbefV);},300);
			setTimeout(function(){syMcur.html(syMcurV);},400);
			setTimeout(function(){syDcur.html(syDcurV);},500);
			setTimeout(function(){SY1.html(SY1V);},600);
			setTimeout(function(){SY2.html(SY2V);},700);
			setTimeout(function(){SY3.html(SY3V);},800);
			setTimeout(function(){SY4.html(SY4V);},900);
			setTimeout(function(){SY5.html(SY5V);},1000);
			setTimeout(function(){SY6.html(SY6V);},1100);
			setTimeout(function(){SY7.html(SY7V);},1200);
			
		});
	},1200);
	
	//GZH
	setTimeout(function(){
		zeai.ajax({url:'welcome'+zeai.ajxext+'submitok=ajax_GZH'},function(e){rs=zeai.jsoneval(e);
			var GZH1V=rs.GZH['gzh1'],GZH2V=rs.GZH['gzh2'],GZH3V=rs.GZH['gzh3'],GZH4V=rs.GZH['gzh4'];
			setTimeout(function(){GZH1.html(GZH1V);},100);
			setTimeout(function(){GZH2.html(GZH2V);},200);
			setTimeout(function(){GZH3.html(GZH3V);},300);
			setTimeout(function(){GZH4.html(GZH4V);},400);
		});
	},1300);
	
	//reg  tubiao3
	setTimeout(function(){
		zeai.ajax({url:'welcome'+zeai.ajxext+'submitok=ajax_reg_day'},function(e){
			if(!zeai.empty(e)){
				var arr=e;
				arr = arr.replace(/"/g,"");
				arr = zeai.jsoneval(arr);
				reg(arr);
			}
		});
	},1500);
	parent.pageB(1);
	parent.o('LB1').style.backgroundColor='#f0f0f0';
}
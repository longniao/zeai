function iBannerFn(screenW){
	//if(screenW>1920)screenW=1920;
	//var W = parseInt(screenW-20),H=parseInt(W*0.4647);
	var W=screenW,H=442;
	var oPic = o("pic_box").getElementsByTagName("li");
	for(var i=0;i<oPic.length;i++){
		oPic[i].style.width = W+'px';
		oPic[i].style.height= H+'px';
	}
	var zeaiV6 = new ScrollPic();
	zeaiV6.scrollContId   = "pic_box";
	zeaiV6.dotListId      = "focus_dot";
	zeaiV6.dotOnClassName = "ed";
	zeaiV6.arrLeftId      = "prev";
	zeaiV6.arrRightId     = "next";
	zeaiV6.frameWidth     = W;
	zeaiV6.pageWidth      = W;
	zeaiV6.upright        = false;
	zeaiV6.speed          = 20;
	zeaiV6.space          = 50;
	zeaiV6.autoPlay       = true;
	zeaiV6.initialize();
}
if(!zeai.empty(o('indexRegBtn')))o('indexRegBtn').onclick=function(){
	console.log(o('reg_area_area1id').value);
	console.log(o('reg_area_area2id').value);
	console.log(o('reg_area_area3id').value);
}
if(iModuleU_pc==2){
	o('unavbtn2_1').onclick=function(){unavbtn2(1)}
	o('unavbtn2_2').onclick=function(){unavbtn2(2)}
	o('unavbtn2_3').onclick=function(){unavbtn2(3)}
	o('unavbtn2_4').onclick=function(){unavbtn2(4)}
	function unavbtn2(i){
		for(var k=1;k<=4;k++){o('unavbtn2_'+k).class('');}	
		o('unavbtn2_'+i).class('ed');
		o('ulist').html(load8);
		zeai.ajax({url:'p1/index'+zeai.ajxext+'submitok=ajax_unav2_'+i,js:0},function(e){
			if(e=='nologin'){zeai.openurl('p1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(HOST))}else{
				o('ulist').html(e);
			}
		});
	}
}else{
	o('unavbtn1').onclick=function(){unavbtn(1)}
	o('unavbtn2').onclick=function(){unavbtn(2)}
	o('unavbtn3').onclick=function(){unavbtn(3)}
	o('unavbtn4').onclick=function(){unavbtn(4)}
	o('unavbtn5').onclick=function(){unavbtn(5)}
	function unavbtn(i){
		for(var k=1;k<=5;k++){o('unavbtn'+k).class('');}	
		o('unavbtn'+i).class('ed');
		o('ulist').html(load8);
		zeai.ajax({url:'p1/index'+zeai.ajxext+'submitok=ajax_unav'+i,js:0},function(e){
			if(e=='nologin'){zeai.openurl('p1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(HOST))}else{
				o('ulist').html(e);
			}
		});
	}
}



ZEAI_area('so_area_',true,'请选择　所在地区');
var age1_ARR = age_ARR,age2_ARR = age_ARR;
ZEAI_select('so','age1',true);ZEAI_select('so','age2',true);ZEAI_select('so','sex',true);

if(!zeai.empty(o('indexRegBtn'))){
ZEAI_area('reg_area_',false,'请选择　所在地区');//ul
ZEAI_select('reg','love',false,'请选择　婚姻状况');
ZEAI_birthday({ul:birthday_,bx:false,selstr:'请选择　出生年月'});
}
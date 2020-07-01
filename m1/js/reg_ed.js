function my_info_data(json) {
    if (json.aboutus)json.aboutus.onblur=function(){
		aboutus.value = zeai.clearhtml(aboutus.value);
        zeai.ajax({'url':HOST+'/m1/reg_ed'+zeai.extname,'data':{"submitok":"ajax_aboutus","value":aboutus.value}},function(e){rs=zeai.jsoneval(e);
            if (rs.flag==1)aboutus.html(aboutus.value);//zeai.msg(rs.msg,{"time":0.5});
        });
        zeai.setScrollTop(0);
    }
	var url=HOST+'/m1/reg_ed';
    zeai.listEach(json.modlist,function(obj){
        switch (obj.className) {
            case 'ipt':ZeaiM.divMod('input',obj,url);break;
            case 'aread':ZeaiM.divMod('area',obj,url);break;
            case 'slect':ZeaiM.divMod('select',obj,url);break;
            case 'rang':ZeaiM.divMod('range',obj,url);break;
            case 'bthdy':ZeaiM.divMod('birthday',obj,url);break;
            case 'chckbox':ZeaiM.divMod('checkbox',obj,url);break;
        }	
    });
}
var selstr2='不限';
var bx = [{'i':'0','v':selstr2}];
history.pushState({btn:'WWW_ZEAI_CN__GOBACK'},'',null);
/*
var mate_age_ARR1 = bx.concat(age_ARR);
var mate_age_ARR2 = bx.concat(age_ARR);
var mate_heigh_ARR1 = bx.concat(heigh_ARR);
var mate_heigh_ARR2 = bx.concat(heigh_ARR);
var mate_pay_ARR = bx.concat(pay_ARR);
var mate_edu_ARR = bx.concat(edu_ARR);
var mate_love_ARR = bx.concat(love_ARR);
var mate_house_ARR = bx.concat(house_ARR);
*/
var mate_age_ARR1 = age_ARR;
var mate_age_ARR2 = age_ARR;
var mate_heigh_ARR1 = heigh_ARR;
var mate_heigh_ARR2 = heigh_ARR;
var mate_pay_ARR = pay_ARR;
var mate_edu_ARR = edu_ARR;
var mate_love_ARR = love_ARR;
var mate_house_ARR = house_ARR;

/*var mate_areaARR1,mate_areaARR2,mate_areaARR3;
(function aeraPushBx(){
	var newA1 = [{'i':'ZE0000','v':selstr2,f:0}];
	var newA2 = [];
	var newA3 = [];
	mate_areaARR1=newA1.concat(areaARR1);
	function newA2fn(){
		for(var k=0;k<areaARR1.length;k++){newA2.push({'i':'AI'+k+'00','v':selstr2,f:mate_areaARR1[k].i});}
		return newA2;
	}
	function newA3fn(){
		for(var k=0;k<areaARR2.length;k++) {newA3.push({'i':'CN00'+k,'v':selstr2,f:mate_areaARR2[k].i});}
		return newA3;
	}
	mate_areaARR2=newA2fn().concat(areaARR2);
	mate_areaARR3=newA3fn().concat(areaARR3);
})();
*/
var mate_areaARR1=areaARR1,mate_areaARR2=areaARR2,mate_areaARR3=areaARR3;
function my_info_save(){
	zeai.ajax({url:'reg_ed'+zeai.ajxext+'submitok=ajax_chkdata'},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg,{time:2});
		if(rs.flag==1){
			setTimeout(function(){zeai.openurl(HOST);},1000);
		}else{
			if(rs.obj=='aboutus'){
				//my_info_submain.scrollTop=1010;
				//zeai.setScrollTop(2010);
			}else if(rs.obj=='nickname' || rs.obj=='car' || rs.obj=='house'){
				zeai.setScrollTop(345);
			}else if(rs.obj=='photo_s'){
				zeai.setScrollTop(0);
			}else if(rs.obj=='mate'){
				zeai.setScrollTop(380);
			}
		}
	});
}
photoUp({
	btnobj:photo_s,
	url:"reg_ed"+zeai.extname,
	submitokBef:"ajax_photo_s_",
	_:function(rs){
		zeai.msg(0);zeai.msg(rs.msg);
		if (rs.flag == 1){
			photo_s.html('<img src='+rs.photo_s+'>');
			nophoto_sBox.hide();
			setTimeout(function(){zeai.setScrollTop(0);},400);
		}
	}
});
my_info_data({"aboutus":aboutus,"modlist":document.querySelector('.modlist').getElementsByTagName("li")});


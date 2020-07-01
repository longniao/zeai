/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:7144100,797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/07/01 by supdes
*/
var REG={},url='reg_diy',
mate_age_ARR1 = age_ARR,
mate_age_ARR2 = age_ARR,
mate_heigh_ARR1 = heigh_ARR,
mate_heigh_ARR2 = heigh_ARR,
mate_weigh_ARR1 = weigh_ARR,
mate_weigh_ARR2 = weigh_ARR,
mate_pay_ARR = pay_ARR,
mate_edu_ARR = edu_ARR,
mate_love_ARR = love_ARR,
mate_job_ARR = job_ARR,
mate_house_ARR = house_ARR,
mate_child_ARR = child_ARR,
mate_marrytime_ARR = marrytime_ARR,
mate_companykind_ARR = companykind_ARR,
mate_smoking_ARR = smoking_ARR,
mate_drink_ARR = drink_ARR,
mate_car_ARR = car_ARR;
var mate_areaARR1=areaARR1,mate_areaARR2=areaARR2,mate_areaARR3=areaARR3;
var selstr2='不限';
var bx = [{'i':'0','v':selstr2}];
//var mate_love_ARR = bx.concat(love_ARR);
//var mate_house_ARR = bx.concat(house_ARR);
/*var mate_areaARR1,mate_areaARR2,mate_areaARR3;*/
var defarea1=areaARR1[0].i;
var defarea2=areaARRhj1[0].i;
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
var mate_areaARRhj1=areaARRhj1,mate_areaARRhj2=areaARRhj2,mate_areaARRhj3=areaARRhj3;
(function aerahjPushBx(){
	var newA1 = [{'i':'ZE0000','v':selstr2,f:0}];
	var newA2 = [];
	var newA3 = [];
	mate_areaARRhj1=newA1.concat(areaARRhj1);
	function newA2fn(){
		for(var k=0;k<areaARRhj1.length;k++){newA2.push({'i':'AI'+k+'00','v':selstr2,f:mate_areaARRhj1[k].i});}
		return newA2;
	}
	function newA3fn(){
		for(var k=0;k<areaARRhj2.length;k++) {newA3.push({'i':'CN00'+k,'v':selstr2,f:mate_areaARRhj2[k].i});}
		return newA3;
	}
	mate_areaARRhj2=newA2fn().concat(areaARRhj2);
	mate_areaARRhj3=newA3fn().concat(areaARRhj3);
})();

function regbtnFn(){
	var pwdV = o('pwd').value;
	var subscribeV = o('subscribe').value;
	if(reg_kind==2){
		var unameV = o('uname').value;
		if(zeai.str_len(unameV) < 3 || zeai.str_len(unameV)>20){zeai.msg('请输入3-15个字符登录用户名',o('uname'));return false;}
		REG['uname']=unameV;
	}else if(reg_kind==1 || reg_kind==3){	
		var mobV = o('mob').value,verifyV=o('verify').value;
		if(!zeai.ifmob(mobV)){zeai.msg('请输入正确手机号',o('mob'));return false;}
		if(!zeai.ifint(verifyV)){zeai.msg('请输入手机验证码',o('verify'));return false;}
		REG['mob']=mobV;
		if(reg_kind==3){
			var unameV = o('uname').value;
			if(zeai.str_len(unameV) < 3 || zeai.str_len(unameV)>20){zeai.msg('请输入3-15个字符登录用户名',o('uname'));return false;}
			REG['uname']=unameV;
		}
		var verifyv=verify.value;
	}
	if(zeai.str_len(pwdV)<6 || zeai.str_len(pwdV)>20){zeai.msg('请输入正确的登录密码(长度6~20)',o('pwd'));return false;}
	if(!o('clause').checked){zeai.msg('请勾选同意【会员注册协议】');return false;}
	REG['pwd']=pwdV;
	zeai.ajax({url:url+zeai.extname,data:{subscribe:subscribeV,tguid:tguid.value,tmpid:tmpid,verify:verifyv,submitok:'ajax_uname_addupdate',REG:JSON.stringify(REG)}},function (e){var rs=zeai.jsoneval(e);//form:o('ZEAI_form_reg'),
		if (rs.flag==1){
			ZeaiM.page.load({url:url+zeai.ajxext+'t=sex'},ZEAI_MAIN,'sex');
			setTimeout(function(){
				o('WWW-ZEAI-CN-form').hide();
				o('ifreg2').show();
			},500);
		}else if(rs.flag=='logined'){
			setTimeout(function(){zeai.openurl(HOST+'/?z=my');},2000);
		}else{
			zeai.msg(0);zeai.msg(rs.msg);
		}
	});
}
function regnext(backname){
	backname=(zeai.empty(backname))?ZEAI_MAIN:backname;
	zeai.ajax({url:url+zeai.ajxext+'submitok=ajax_next'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			//console.log(rs);
			if(rs.url=='my'){
				zeai.msg(0);zeai.msg('注册成功',{time:4});
				setTimeout(function(){zeai.openurl(HOST+'/?z=my');},2000);
			}else{
				//ZeaiM.page.load({url:url+zeai.ajxext+'t='+rs.url},backname,rs.url);
				zeai.openurl(url+zeai.ajxext+'t='+rs.url);
			}
		}else if(rs.flag==2){	
			zeai.msg(0);zeai.msg(rs.msg);
		}else{
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);	
		}
	});
}
		
function reg_diy_data_save(f,v,t){
	zeai.ajax({url:url+zeai.ajxext+'submitok=ajax_data_save',data:{f:f,v:encodeURIComponent(v),t:encodeURIComponent(t)}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			regnext(f);
		}else if(rs.flag==2){
			zeai.msg(0);zeai.msg(rs.msg);
		}else{
			zeai.msg(rs.msg);setTimeout(function(){zeai.openurl(HOST+'/m1/login.php')},1000);
		}
	});
}

function reg_alone_udata(sex,t,def){
	switch (sex){
		case 1:ico='&#xe60c;';cls='sex1';break;
		case 2:ico='&#xe95d;';cls='sex2';break;
		default:ico='&#xe61f;';cls='sex'+REG['sex'];break;
	}
	switch (t){
		case 'edu':ico='&#xe6c0;';cls='kind sex'+sex;break;
		case 'job':ico='&#xe638;';cls='kind sex'+sex;break;
		case 'house':ico='&#xe7a0;';cls='kind sex'+sex;break;
		case 'car':ico='&#xe6b4;';cls='kind sex'+sex;break;
		case 'pay':ico='&#xe61a;';cls='kind sex'+sex;break;
	}
	var ARR=eval(t+'_ARR');
	o('vtphoto'+t).append('<div class="'+cls+'"><i class="ico">'+ico+'</i></div>');
	for(var h=0;h<ARR.length;h++) {
		var hed=(ARR[h].i==def)?' HONG':' BAI';
		var wsty='';
		o('li'+t).append('<li id="li'+t+h+'" class="'+t+'li btn size4 '+wsty+' yuan'+hed+'">'+ARR[h].v+'</li>');
		(function(h){
			o('li'+t+h).onclick=function(){
				zeai.listEach('.'+t+'li',function(obj){if (obj.hasClass('HONG')){obj.removeClass('HONG');obj.addClass('BAI');}});
				this.removeClass('BAI');this.addClass('HONG');
				if(t=='parent' && h>0){
					var hstr=(h==2)?'帮亲友':'帮子女';
					zeai.confirm('亲，由于您是'+hstr+'找对象，<font class="Cf00">必须上传他(她)本人照片和个人资料</font>，以便他万里挑一，早日脱单！',function(){
						reg_diy_data_save(t,ARR[h].i,'');
					});
				}else{
					reg_diy_data_save(t,ARR[h].i,'');
				}
			}
		})(h);
	}
}

function yzmbtnFn(){
//	if (!zeai.empty(o('yzmbtn'))){
//		yzmbtn.onclick = function(){
			if (zeai.ifmob(o('mob').value)){
				if (!o('yzmbtn').hasClass('disabled')){
					o('yzmbtn').addClass('disabled');
					zeai.ajax({url:'reg_diy'+zeai.extname,data:{submitok:'ajax_get_verify',mob:o('mob').value}},function(e){
						var rs=zeai.jsoneval(e);
						if (rs.flag == 1){
							zeai.msg(rs.msg,{time:5});
							o('verify').value='';
							yzmtimeFn(120);
						}else{
							zeai.msg(rs.msg,mob);
							yzmbtn.removeClass('disabled');
						}
					});
				}
			}else{
				zeai.msg('请输入手机号码',mob);
				return false;
			}
//		}
//	}
}
function yzmtimeFn(countdown) { 
	if (countdown == 0) {
		yzmbtn.removeClass('disabled');
		yzmbtn.html('<font>重新获取</font>'); 
		return false;
	} else { 
		if (!zeai.empty(o('yzmbtn'))){
			yzmbtn.addClass('disabled');
			yzmbtn.html('<b>'+countdown + "S</b>后重新发送"); 
			countdown--; 
		}
	} 
	cleandsj=setTimeout(function(){yzmtimeFn(countdown)},1000);
}

function my_info_data(json) {
	var url=HOST+'/m1/reg_diy';
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

//后加
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
function agreeDeclara(){
	ZeaiM.page.load('about'+zeai.ajxext+'t=declara',ZEAI_MAIN,'about_declara');
}
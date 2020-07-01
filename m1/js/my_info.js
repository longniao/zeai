function photo_s_h5(btnobj){
	zeai.up({"url":HOST+"/m1/my_info"+zeai.extname,"upMaxMB":upMaxMB,"submitok":"ajax_photo_s_up_h5","ajaxLoading":0,"fn":function(e){var rs=zeai.jsoneval(e);
		photo_s_set(btnobj,rs);
	}});
}
function photo_s_wx(btnobj) {
	ZeaiM.up_wx({"url":HOST+"/m1/my_info"+zeai.extname,"submitok":"ajax_photo_s_up_wx","ajaxLoading":0,"fn":function(e){var rs=zeai.jsoneval(e);
		photo_s_set(btnobj,rs);
	}});
}

function photo_s_app(btnobj) {
	app_uploads({url:HOST+"/m1/my_info.php?submitok=ajax_photo_s_up_app",num:1},function(e){
		var rs=zeai.jsoneval(e);
		if (rs.flag == 1){
			if(o("my_info_databtn"))o("my_info_databtn").click();
		}else{
			zeai.msg(0);zeai.msg(rs.msg);
		}
	});
}

function photo_s_set(btnobj,rs) {
	zeai.msg(0);zeai.msg(rs.msg);
	if (rs.flag == 1){
		var img=btnobj.getElementsByTagName("img")[0];
		img.src=rs.photo_s;
		if(!zeai.empty(o('my_photo_s')))o('my_photo_s').html('<img src='+rs.photo_s+'>');
	}
}
function my_info_data(json) {
    if (json.aboutus)json.aboutus.onblur=function(){
		aboutus.value = zeai.clearhtml(aboutus.value);
        zeai.ajax({'url':HOST+'/m1/my_info'+zeai.extname,'data':{"submitok":"ajax_aboutus","value":aboutus.value}},function(e){rs=zeai.jsoneval(e);
            if (rs.flag==1)aboutus.html(aboutus.value);//zeai.msg(rs.msg,{"time":0.5});
        });
        zeai.setScrollTop(0);
    }
    zeai.listEach(json.modlist,function(obj){
        switch (obj.className) {
            case 'ipt':ZeaiM.divMod('input',obj);break;
            case 'aread':ZeaiM.divMod('area',obj);break;
            case 'slect':ZeaiM.divMod('select',obj);break;
            case 'rang':ZeaiM.divMod('range',obj);break;
            case 'bthdy':ZeaiM.divMod('birthday',obj);break;
            case 'chckbox':ZeaiM.divMod('checkbox',obj);break;
        }	
    });
}
/*mate init*/
var selstr2='不限';
var bx = [{'i':'0','v':selstr2}];

/*var mate_age_ARR1 = bx.concat(age_ARR);
var mate_age_ARR2 = bx.concat(age_ARR);
var mate_heigh_ARR1 = bx.concat(heigh_ARR);
var mate_heigh_ARR2 = bx.concat(heigh_ARR);
var mate_pay_ARR = bx.concat(pay_ARR);
var mate_edu_ARR = bx.concat(edu_ARR);
var mate_love_ARR = bx.concat(love_ARR);
var mate_house_ARR = bx.concat(house_ARR);
*/
var
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
var defarea1=areaARR1[0].i;
var defarea2=areaARRhj1[0].i;


var mate_areaARR1,mate_areaARR2,mate_areaARR3;
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

function photo_up(json) {
	var btnadd=json.btnadd;
	if(json.onclick && !zeai.empty(o(btnadd))){
		btnadd.onclick=function(){up();}
	}else{up()}
	function up(){
		if(browser=='h5'){
			zeai.up({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitokBef+"up_h5","ajaxLoading":0,"multiple":json.multiple,"fn":function(e){var rs=zeai.jsoneval(e);json._(rs);}});
		}else if(browser=='wx'){
			ZeaiM.up_wx({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitokBef+"up_wx","ajaxLoading":0,"multiple":json.multiple,"fn":function(e){var rs=zeai.jsoneval(e);json._(rs);}});
		}
	}
}
function photoView(b) {
	if (browser=='wx'){
		wx.previewImage({current:b,urls:pic_list});
	}else{
		ZeaiM.piczoom(b);
	}
}
function PVdel(id,submitok){
	zeai.ajax({url:HOST+'/m1/my_info'+zeai.extname,data:{submitok:submitok,id:id}},function(e){var rs=zeai.jsoneval(e);
		if(rs.flag==1){
			var liobj = o('li'+id);
			liobj.remove();
			var data_num=parseInt(o('data_num').innerHTML)-1;
			o('data_num').html(data_num);
			if (submitok=='ajax_photo_del'){
				data_photo_num--;
				o('my_info_photobtn').click()
			}else if(submitok=='ajax_video_del'){
				data_video_num--;
				o("my_info_videobtn").click();
			}
		}
	});
}
function video_up(json){
	var ftypearr = ["video/mp4","video/x-ms-wmv","video/quicktime","video/3gpp","video/x-flv"];
	if(zeai.empty(o('video'))){
		var video = zeai.addtag('input');video.id='video';video.type='file';video.accept="video/mp4,video/x-ms-wmv,video/quicktime,video/3gpp,video/x-flv";video.hide();/*avi,.3gp,.mov,.mpeg,.mpg,.flv,.asf,.mp4,.mkv,.wmv,.rmvb*/
		document.body.append(video);
	}else{video=o('video');}
	video.click();
	video.onchange = function(){
		var FILES = video.files[0];
		if (FILES['size'] > upVMaxMB*1024000){videoNull();zeai.msg('视频太大，已超过'+upVMaxMB+'M，请重选');return false;}
		if (!ftypearr.in_array(FILES['type'])){videoNull();zeai.msg('格式错误,只能 mp4/mov/3gp/mov等'+FILES['type']);return false;}
		var filename = FILES['name'].toLowerCase();var extname = filename.substring(filename.lastIndexOf(".")+1,filename.length);
		zeai.msg("正在上传中...<span id='upbfb'></span>％",{time:333});
		var postjson = {file:FILES,extname:extname};Object.assign(postjson,json);
		zeai.ajax({"url":json.url,"ajaxLoading":0,"data":postjson},function(e){var rs=zeai.jsoneval(e);
			videoNull();zeai.msg(rs.msg);
			o('my_info_videobtn').click();
		},function(bfbnum){
			if(!zeai.empty(o('upbfb')))o('upbfb').innerHTML = bfbnum;	
			if (bfbnum==100){zeai.msg(0);zeai.msg('正在保存...',{time:33});}
		});
	}
	function videoNull(){zeai.msg(0);video.remove();}
}
function zeaiplay(zeai_cn) {
	var v = o('zeaiVbox'+zeai_cn);
	//showhidden('zeaiVbox',1);
	//v.src = zeai_cn;
	zeai.msg('视频加载中...');
	v.play();
}
function my_info_save(){
	zeai.ajax({url:HOST+'/m1/my'+zeai.ajxext+'submitok=ajax_getmyinfobfb'},function(e){rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg,{time:2});
		if(rs.flag==1){
			if(!zeai.empty(o('ZEAIGOBACK-my_info'))){
				setTimeout(function(){o('ZEAIGOBACK-my_info').click();},1000);
			}
			setTimeout(function(){
				if(!zeai.empty(o('my_info_bfbbar')))o('my_info_bfbbar').html(rs.myinfobfb);
				if(!zeai.empty(o('my_bfbbar')))o('my_bfbbar').html(rs.myinfobfb);
			},200);
		}else{
			if(rs.obj=='weixin' || rs.obj=='mob'){
				my_info_submain.scrollTop=1010;
			}else if(rs.obj=='nickname'){
				my_info_submain.scrollTop=60;
			}
		}
	});
}


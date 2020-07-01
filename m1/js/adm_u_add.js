/*mate init*/
var selstr2='不限';
var bx = [{'i':'0','v':selstr2}];
//var mate_age_ARR1 = bx.concat(age_ARR);
//var mate_age_ARR2 = bx.concat(age_ARR);
//var mate_heigh_ARR1 = bx.concat(heigh_ARR);
//var mate_heigh_ARR2 = bx.concat(heigh_ARR);
//var mate_pay_ARR = bx.concat(pay_ARR);
//var mate_edu_ARR = bx.concat(edu_ARR);
//var mate_love_ARR = bx.concat(love_ARR);
var mate_age_ARR1 = age_ARR;
var mate_age_ARR2 = age_ARR;
var mate_heigh_ARR1 = heigh_ARR;
var mate_heigh_ARR2 = heigh_ARR;
var mate_pay_ARR = pay_ARR;
var mate_edu_ARR = edu_ARR;
var mate_love_ARR = love_ARR;

var mate_house_ARR = bx.concat(house_ARR);
/*(function aeraPushBx(){
	var newA1 = [{'i':'ZE0000','v':selstr2,f:0}];
	var newA2 = [];
	var newA3 = [];
	areaARR1=newA1.concat(areaARR1);
	function newA2fn(){
		for(var k=0;k<areaARR1.length;k++){newA2.push({'i':'AI'+k+'00','v':selstr2,f:areaARR1[k].i});}
		return newA2;
	}
	function newA3fn(){
		for(var k=0;k<areaARR2.length;k++) {newA3.push({'i':'CN00'+k,'v':selstr2,f:areaARR2[k].i});}
		return newA3;
	}
	areaARR2=newA2fn().concat(areaARR2);
	areaARR3=newA3fn().concat(areaARR3);
})();

*//*
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
function photo_s_h5(btnobj){
	zeai.up({"url":"m1/my_info"+zeai.extname,"upMaxMB":upMaxMB,"submitok":"ajax_photo_s_up_h5","ajaxLoading":0,"fn":function(e){var rs=zeai.jsoneval(e);
		photo_s_set(btnobj,rs);
	}});
}
function photo_s_wx(btnobj) {
	ZeaiM.up_wx({"url":"m1/my_info"+zeai.extname,"submitok":"ajax_photo_s_up_wx","ajaxLoading":0,"fn":function(e){var rs=zeai.jsoneval(e);
		photo_s_set(btnobj,rs);
	}});
}
function photo_s_set(btnobj,rs) {
	zeai.msg(0);zeai.msg(rs.msg);
	if (rs.flag == 1){
		var img=btnobj.getElementsByTagName("img")[0];
		img.src=rs.photo_s;
		if(!zeai.empty(o('my_photo_s')))o('my_photo_s').html('<img src='+rs.photo_s+'>');
	}
}
*/
function my_info_data(json) {
    if (json.aboutus)json.aboutus.onblur=function(){
		aboutus.value = zeai.clearhtml(aboutus.value);
        zeai.setScrollTop(0);
    }
    if (json.bz)json.bz.onblur=function(){
		bz.value = zeai.clearhtml(bz.value);
        zeai.setScrollTop(0);
    }
    zeai.listEach(json.modlist,function(obj){
        switch (obj.className) {
            case 'ipt':divMod('input',obj);break;
            case 'aread':divMod('area',obj);break;
            case 'slect':divMod('select',obj);break;
            case 'rang':divMod('range',obj);break;
            case 'bthdy':divMod('birthday',obj);break;
            case 'chckbox':divMod('checkbox',obj);break;
        }	
    });
}

function divMod(kind,obj){
	var objstr=obj.id;
	//Sbindbox=objstr;
	obj.onclick = function(){
		var span = obj.getElementsByTagName("span")[0];
		var h4   = obj.getElementsByTagName("h4")[0];
		var defV = obj.getAttribute("data"),title=h4.innerHTML;
		title = title.replace(/<b.*?>.*?<\/b[^>]*>/ig,"");
		title = title.replace(/　/ig,'');
		switch (kind) {
			case 'input':
				divBtmMod({"objstr":objstr,"title":title,"value":defV,fn:function(inputV){
					span.html(inputV);obj.setAttribute("data",inputV);o(obj.id+'8').value=inputV;
				}});
			break;
			case 'area':
				ios_select_area(title,areaARR1,areaARR2,areaARR3,defV,function(obj1,obj2,obj3){
					var areaid    = obj1.i + ',' + obj2.i + ',' + obj3.i;
					var areatitle = obj1.v + ' ' + obj2.v + ' ' + obj3.v;
						span.html(areatitle);obj.setAttribute("data",areaid);
						o(obj.id+'8').value=areaid;o(obj.id+'8title').value=areatitle;
				},',');
			break;
			case 'range':
				ios_select2_range(title,eval(objstr+'_ARR1'),eval(objstr+'_ARR2'),defV,function(obj1,obj2){
					var i,v,i1=obj1.i,i2=obj2.i,v1=obj1.v,v2 = obj2.v;
					if (parseInt(i1) > parseInt(i2) && parseInt(i2)!= 0){i=i1,i1=i2,i2=i;v=v1,v1=v2,v2=v;}
					var list  = i1 + ',' + i2,title = v1 + '～' + v2;
						span.html(title);obj.setAttribute("data",list)
						o(obj.id+'1').value=i1;o(obj.id+'2').value=i2;
				},',');
			break;
			case 'birthday':
				ios_select_area(title,yearData, monthData, dateData,defV,function(obj1,obj2,obj3){
					var birthday = obj1.i + '-' + obj2.i + '-' + obj3.i;
						span.html(birthday);obj.setAttribute("data",birthday);o(obj.id+'8').value=birthday;
				},'-');
			break;
			case 'select':
				ios_select1_normal(title,eval(objstr+'_ARR'),defV,function(obj1){
					var sid = obj1.i,sv=obj1.v;
						span.html(sv);obj.setAttribute("data",sid);o(obj.id+'8').value=sid;
				},',');
			break;
			case 'checkbox':
				divBtmMod({"objstr":objstr,"title":title,"value":defV,"kind":"checkbox",fn:function(chkV){
					//span.html(checkbox_div_list_get_listTitle(objstr,chkV));obj.setAttribute("data",chkV);
					span.html(checkbox_div_list_get_listTitle(objstr,chkV));
					obj.setAttribute("data",chkV);o(obj.id+'8').value=chkV;
				}});
			break;
		}	
	}
}


function divBtmMod(json){
	if (json.flag==0){div_close_fn();return false;}
	if (typeof(json) != "object")return false;
	var M = zeai.addtag('div');M.id = 'divBtmMod';M.class('mask alpha0_100');
	var obj = zeai.addtag('div');obj.class('divBtmMod fadeInUp');
	var em  = zeai.addtag('em');
	var h3  = zeai.addtag('h3');h3.html(json.title);
	var cancel = zeai.addtag('button');cancel.type='button';cancel.class('divBtmCancel');cancel.html('取消');
	var form  = zeai.addtag('div');form.class('form');
	if (json.kind=='checkbox'){
		checkbox_div_list_create(json.objstr,json.value,eval(json.objstr+'_ARR'),form);
	}else{
		var placeholder = json.title.replace('<font>（替您保密，身份验证之用）</font>','');
		var input = zeai.addtag('input');input.type='text';input.placeholder='请输入'+placeholder,input.maxlength=20;input.id='divBtmC';input.value=json.value;
		form.append(input);
	}
	var save = zeai.addtag('button');save.html('确定');save.type='button';save.class('divBtmSave');
	//
	em.append(h3);em.append(cancel);
	form.append(save);
	obj.append(em);obj.append(form);
	//
	obj.onclick = function(e){e.cancelBubble = true;}
	save.onclick=function(){
		if(typeof(json.fn) == "function"){
			if (json.kind=='checkbox'){
				json.fn(zeai.form.checkbox_div_list_get(json.objstr+'[]'));
			}else{
				json.fn(input.value);
			}
			div_close_fn();
		}
	}
	cancel.onclick=function(){div_close_fn();}
	M.onclick=function(){div_close_fn();}
	if (json.kind=='input' || zeai.empty(json.kind)){
		input.onblur=function(){
			if(typeof(json.fn) == "function"){
				
				zeai.setScrollTop(0);
			}
		}
		setTimeout(function(){o('divBtmC').focus();},300);
	}
	M.append(obj);M.show();
	if (typeof(fobj) == "object"){
		M.style.height = fobj.scrollHeight+'px';
		fobj.append(M);
	}else{
		document.body.append(M);
	}
	function div_close_fn(){
		o('divBtmMod').removeClass('alpha0_100');o('divBtmMod').addClass('alpha100_0');
		obj.removeClass('fadeInUp');obj.addClass('fadeInDown');
		setTimeout(function(){
			if (!zeai.empty(o('divBtmMod'))){
				o('divBtmMod').remove();
			}
		},150);//obj.removeClass('fadeInDown');obj.hide();
		zeai.setScrollTop(0);
	}
}

my_info_data({"aboutus":aboutus,"bz":bz,"modlist":document.querySelector('.modlist').getElementsByTagName("li")});
photoUp({
	btnobj:photo_sbox,
	url:"adm_u_add"+zeai.extname,
	submitokBef:"ajax_photo_pic_",
	_:function(rs){
		zeai.msg(0);zeai.msg(rs.msg);
		if (rs.flag == 1){
			photo_sbox.html('<img src='+up2+rs.dbname+'>');
			photo_s.value=rs.dbname;
		}
	}
});
photoUp({
	btnobj:weixin_picbox,
	url:"adm_u_add"+zeai.extname,
	submitokBef:"ajax_weixin_pic_",
	_:function(rs){
		zeai.msg(0);zeai.msg(rs.msg);
		if (rs.flag == 1){
			weixin_picbox.html('<img src='+up2+rs.dbname+'>');
			weixin_pic.value=rs.dbname;
		}
	}
});
regbtn.onclick=function(){
	zeai.alertplus({title:'确定提交上传保存么？',content:'提交后将不能修改，请仔细核对资料无误后再提交',title1:'取消',title2:'确定',
		fn1:function(){zeai.alertplus(0);},
		fn2:function(){zeai.alertplus(0);
			zeai.msg('正在上传资料',{time:30});
			zeai.ajax({url:'adm_u_add'+zeai.extname,form:WWW__ZEAI___CN_form},function(e){rs=zeai.jsoneval(e);
				console.log(rs);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		}
	});
}

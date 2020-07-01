/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/08/11 by supdes
*/
var ZeaiM={
	zindex:1,
	page:{
		curpage:'',
		load:function(url,backName,curName){
			var go1='ZEAIGOBACK-'+curName,ZeaiPstack=getZeaiPstack();
			if(ZeaiPstack.length>0){for(var k=0;k<ZeaiPstack.length;k++){if(ZeaiPstack[k].id==go1)return false;	}}	
			if (typeof(url) == "object"){var data = url,form = url.form,url = url.url;}
			var postjson = {'url':url};
			if(!zeai.empty(form))Object.assign(postjson,{form:form});
			if(!zeai.empty(data))Object.assign(postjson,{data:data.data});
			zeai.ajax(postjson,function(e){
				var rs;
				if(zeai.empty(e)){
					rs=zeai.jsoneval(JSON_ERROR);zeai.msg(rs.msg);
				}else if(zeai.str_len(e)<200 ){//中断提示，不能输出内容flag!=1，判断逻辑在load页面
					if (e.indexOf("openurl") != -1){
						ZeaiM.eval(e);
					}else{
						ZeaiM.page.sorry(zeai.jsoneval(e),backName);
					}
				}else{
					ZeaiM.dojsFunc(e,function(e){loadPage(e);});
					function loadPage(e){
						ZeaiM.curpage=curName;//for jump
						bkMw1 = (backName == ZEAI_MAIN)?-40:-140;
						setTimeout(function(){if (!zeai.empty(o(backName)))o(backName).css('transform:translate('+bkMw1+'%)');},100);
						//暗
						ZeaiM.zindex++;
						var M=zeai.addtag('div');M.class('mask1 alpha0_100');M.id='M'+backName;M.style.zIndex=ZeaiM.zindex;
						if(backName == ZEAI_MAIN){
							if (!zeai.empty(o('Mmain'))){o('Mmain').remove();}document.body.append(M);M.style.zIndex=1;
						}else{if (!zeai.empty(o(backName)))o(backName).append(M);}
						//暗束
						//头尾
						if (backName == ZEAI_MAIN)ZeaiM.fade({arr:zeaiLoadBack,num:bkMw1+'%',dely:110});
						//头尾束
						var nameDOM = Zeai_cn__PageBox.append('<div id="' + curName + '" class="zeai-body">' + e + '</div>');
						setTimeout(function(){nameDOM.css('z-index:'+ZeaiM.zindex+';transform:translate(-100%)');},50);ZeaiM.eval(e);
						var curNameO=o('ZEAIGOBACK-'+curName);
						if (!zeai.empty(curNameO)){
							curNameO.onclick = function(){ZeaiM.page.back(backName,curName);}
							history.pushState({btn:'WWW_ZEAI_CN__GOBACK'},'',null);
						}
					}
				}
			});
		},
		back:function(backName,curName){
			ZeaiM.curpage=backName;//for jump
			dom = o(curName);
			bkMw2 = (backName == ZEAI_MAIN)?0:-100;
			if (!zeai.empty(o(backName)))o(backName).css('transform:translate('+bkMw2+'%)');
			//头尾
			if (backName == ZEAI_MAIN)ZeaiM.fade({arr:zeaiLoadBack,num:bkMw2+'%'});
			//头尾束
			//暗
			var MbackName=o('M'+backName);
			if(!zeai.empty(MbackName)){MbackName.removeClass('alpha0_100');MbackName.addClass('alpha100_0');ZeaiM.zindex--;}
			//暗束
			if(!zeai.empty(dom))dom.style.webkitTransform = 'translate(0)';
			setTimeout(function(){
				if(!zeai.empty(o(curName))){o(curName).remove();}
				//暗
				if (!zeai.empty(o('M'+backName))){o('M'+backName).remove();}
				//暗束
			},300);
			clearInterval(cleandsj);
		},
		jump:function(url,pageName){
			if (url=='main'){
				o(url).css('transform:translate(0%)');
					//暗淡
					if (!zeai.empty(o('Mmain'))){o('Mmain').remove();}
					//暗淡结束
				Zeai_cn__PageBox.html('');
				o(nav).css('transform:translate(0%)');
				return;
			}
			blankpage.show();
			o(ZeaiM.curpage).css('transform:translate(0%)');
			o(ZEAI_MAIN).hide();
			setTimeout(function(){
				Zeai_cn__PageBox.html('');
				ZeaiM.page.load(url,ZEAI_MAIN,pageName);
			},200);
			setTimeout(function(){blankpage.hide();},888);
		},
		sorry:function(rs,backName){
			switch (rs.flag) {
				case 'logined':zeai.openurl(HOST+'/?z=my');break;
				case 'nologin':zeai.openurl(HOST+'/m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));break;
				case 'nologin_tg':zeai.msg(rs.msg,{time:2});setTimeout(function(){zeai.openurl(HOST+'/m1/tg_login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl))},2000);break;
				case 'jumpurl':zeai.msg(rs.msg,{time:2});setTimeout(function(){zeai.openurl(rs.jumpurl);},1000);break;
				case 'nophoto':
					zeai.alertplus({'title':'----- 请先上传头像 -----','content':rs.msg,'title1':'取消','title2':'去上传','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_info'+zeai.extname,backName,'my_info');}
					});
				break;
				case 'nodata':
					zeai.alertplus({'title':'----- 请完善个人资料 -----','content':rs.msg,'title1':'取消','title2':'去完善','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_info'+zeai.extname,backName,'my_info');}
					});
				break;
				case 'nocontact':
					zeai.alertplus({'title':'----- 请完善联系方法 -----','content':rs.msg,'title1':'取消','title2':'去完善','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_info'+zeai.ajxext+'href=contact',backName,'my_info');}
					});
				break;
				case 'nolevel':
					zeai.alertplus({'title':'升级VIP，沟通无极限','content':rs.msg,'title1':'取消','title2':'去升级','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl),backName,'my_vip');}
					});
				break;
				case 'noucount':
					if(rs.win=='div'){
						if(rs.kind=='contact'){
							obj=o('u_contact_daylooknumHelp');
						}else if(rs.kind=='chat'){
							obj=o(backName+'_chat_daylooknumHelp');
						}
						zeai.div({fobj:o(backName),obj:obj,title:rs.msg,w:300,h:300});
					}else{
						zeai.msg(rs.msg);
					}
				break;
				case 'clickloveb_confirm':
					var obj;
					if(rs.kind=='contact'){
						obj=o('u_contact_lovebHelp');
					}else if(rs.kind=='chat'){
						obj=o(backName+'_chat_lovebHelp');
					}
					zeai.div({fobj:o(backName),obj:obj,title:rs.title,w:300,h:300});//backName只是字符串
				break;
				case 'nocert':
					zeai.alertplus({'title':'-------- 诚信认证 --------','content':rs.msg,'title1':'取消','title2':'去认证','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_info'+zeai.ajxext+'a=cert',backName,'my_info');}
					});
				break;
				default:zeai.msg(rs.msg);break;
			}
		}
	},
	tabmenu:{
		init:function(json){
			//
			var showbox=json.showbox;
			var elem=zeai.tag(json.obj,'li');
			var ZminiEtabAmenuI_cn=zeai.tag(json.obj,'i')[0];
			//
			zeai.listEach(elem,function(li){
				if (li.hasClass('ed')){
					var span = li.getElementsByTagName("span")[0];
					var iW = span.offsetWidth;
					var iL = span.offsetLeft;
					ZminiEtabAmenuI_cn.css('width:'+iW+'px;transform:translate('+iL+'px)');
				}
				var url=li.getAttribute("data");
				if (!zeai.empty(url)){
					li.onclick = function(){
						ZeaiM.tabmenu.onclk({obj:json.obj,li:li,kind:json.kind});
						if (!zeai.empty(showbox)){
							var url=this.getAttribute("data");
							zeai.ajax({'url':url},function(e){
								ZeaiM.dojsFunc(e,function(e){o(showbox).html(e);ZeaiM.eval(e);});
								if (!zeai.empty(p) && p>1)p=1;
							});
						}
						if(!zeai.empty(json.click))json.click(li);
					}
				}
			});
		},
		onclk:function(json){
			var ZminiEtabAmenuI_cn=zeai.tag(json.obj,'i')[0];
			zeai.listEach(zeai.tag(json.obj,'li'),function(li){li.removeClass('ed');});
			json.li.addClass('ed');
			var span = json.li.getElementsByTagName("span")[0],iL,iW;
			if(json.kind=='block'){
				iL = json.li.offsetLeft;
				iW = json.li.offsetWidth;
			}else{
				iL = span.offsetLeft;
				iW = span.offsetWidth;
			}
			ZminiEtabAmenuI_cn.css('width:'+iW+'px;transform:translate('+iL+'px)');
		}
	},
	dojsFunc:function(e,fn){
		var jsF = [],m=0,n=0;
		var es = e.match(/<script src=[\w\W]+?><\/script>/ig);
		if(es) {
			for (var i in es) {
				str = es[i];
				if (typeof str == 'string'){
					jsF.push(str.match(/<script .*?src=\"(.+?)\"/)[1]);
					e = e.replace(str,'');m++;
				}
			}
		}
		//console.log('m='+m);
		if (m>0){
			return srcJsFile();
		}else{
			return fn(e);
		}
		function srcJsFile(){
			var q=0;
			for (var i in jsF) {
				srcjs = jsF[i];
				if (typeof srcjs == 'string'){
					q++;
					//console.log('srcjs='+srcjs);
					var head = document.getElementsByTagName('head')[0].innerHTML;
					if (head.indexOf(srcjs) == -1){
						loadJs(srcjs,function(){
								n++;
								if (n==m){
									//console.log('m=n');
									fn(e);return;
								}
							}
						);
					}else{
						//console.log('(srcjs) != -1'+'|            q='+q+'|m='+m);
						if (q==m)fn(e);
					}
				}
			}
		}
	},
	eval:function(e){
		var eJs = /<script(.|\n)*?>(.|\n|\r\n)*?<\/script>/ig;
		var eJsed = e.match(eJs);
		if(eJsed) {
			var regjs = /<script(.|\n)*?>((.|\n|\r\n)*)?<\/script>/im;
			for(var j=0;j<eJsed.length;j++){
				var jsV = eJsed[j].match(regjs);
				if(jsV[2]){if(window.execScript) {window.execScript(jsV[2]);}else{window.eval(jsV[2]);}}
			}
		}	
	},
	divBtmMod:function(json){
		if (json.flag==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		var M = zeai.addtag('div');M.id = 'divBtmMod';M.class('mask alpha0_100');
		/*
		<div class="divBtmMod" id="divBtmMod">
			<em><h3>修改昵称</h3><button type="button" class="divBtmCancel" id="divBtmCancel">取消</button></em>
			<div class="form">
				<input placeholder="请输入昵称" maxlength="20" id="divBtmC">
				<ul class="chkbox"></ul>
				<button type="button" class="divBtmSave" id="divBtmSave">保存</button>
			</div>
		</div>
		*/	
		var obj = zeai.addtag('div');obj.class('divBtmMod fadeInUp');
		var em  = zeai.addtag('em');
		var h3  = zeai.addtag('h3');h3.html(json.title);
		var cancel = zeai.addtag('button');cancel.type='button';cancel.class('divBtmCancel');cancel.html('取消');
		var form  = zeai.addtag('div');form.class('form');
		if (json.kind=='checkbox'){
			checkbox_div_list_create(json.objstr,json.value,eval(json.objstr+'_ARR'),form);
		}else{
			//var placeholder = json.title.replace(/<\/?font[^>]*>/gi,""); 
			var placeholder = json.title.replace('<font>（替您保密，身份验证之用）</font>','');
			var input = zeai.addtag('input');input.type='text';input.placeholder='请输入'+placeholder,input.maxlength=20;input.id='divBtmC';input.value=json.value;
			form.append(input);
		}
		var save = zeai.addtag('button');save.html('保存');save.type='button';save.class('divBtmSave');
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
					
					//json.fn(input.value);
					//div_close_fn();
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
	},
	divMod:function(kind,obj,url){
		var objstr=obj.id;
		url=(zeai.empty(url))?HOST+'/m1/my_info':url;
		//Sbindbox=objstr;
		obj.onclick = function(){
			var span = obj.getElementsByTagName("span")[0];
			var h4   = obj.getElementsByTagName("h4")[0];
			var defV = obj.getAttribute("data"),title=h4.innerHTML;
			title = title.replace(/<b.*?>.*?<\/b[^>]*>/ig,"");
			title = title.replace(/　/ig,'');
			switch (kind) {
				case 'input':
					ZeaiM.divBtmMod({"objstr":objstr,"title":title,"value":defV,fn:function(inputV){
						zeai.ajax({'url':url+zeai.extname,'data':{"submitok":'ajax_'+objstr,"value":inputV}},function(e){rs=zeai.jsoneval(e);
							if (rs.flag==1){span.html(inputV);obj.setAttribute("data",inputV)}else{
								zeai.msg(0);zeai.msg(rs.msg);
							}
							//zeai.msg(rs.msg,{"time":0.5});
						});
					}});
				break;
				case 'area':
					if(obj.id=='mate_area' || obj.id=='mate_areaid'){
						var ARR1=mate_areaARR1,ARR2=mate_areaARR2,ARR3=mate_areaARR3;
					}else if(obj.id=='mate_areaid2'){
						var ARR1=mate_areaARRhj1,ARR2=mate_areaARRhj2,ARR3=mate_areaARRhj3;
					}else{
						var ARR1=areaARR1,ARR2=areaARR2,ARR3=areaARR3;
					}
					ios_select_area(title,ARR1,ARR2,ARR3,defV,function(obj1,obj2,obj3){
						var areaid    = obj1.i + ',' + obj2.i + ',' + obj3.i;
						var areatitle = obj1.v + ' ' + obj2.v + ' ' + obj3.v;
						zeai.ajax({'url':url+zeai.extname,'data':{"submitok":'ajax_'+objstr,"areaid":areaid,"areatitle":areatitle}},function(e){rs=zeai.jsoneval(e);
							if (rs.flag==1){span.html(areatitle);obj.setAttribute("data",areaid)}
							//zeai.msg(rs.msg,{"time":0.5});
						});
					},',');
				break;
				case 'range':
					ios_select2_range(title,eval(objstr+'_ARR1'),eval(objstr+'_ARR2'),defV,function(obj1,obj2){
						var i,v,i1=obj1.i,i2=obj2.i,v1=obj1.v,v2 = obj2.v;
						if (parseInt(i1) > parseInt(i2) && parseInt(i2)!= 0){i=i1,i1=i2,i2=i;v=v1,v1=v2,v2=v;}
						var list  = i1 + ',' + i2,title = v1 + '～' + v2;
						zeai.ajax({'url':url+zeai.extname,'data':{"submitok":'ajax_'+objstr,"i1":i1,"i2":i2}},function(e){rs=zeai.jsoneval(e);
							if (rs.flag==1){span.html(title);obj.setAttribute("data",list)}
							//zeai.msg(rs.msg,{"time":0.5});
						});
					},',');
				break;
				case 'birthday':
					ios_select_area(title,yearData, monthData, dateData,defV,function(obj1,obj2,obj3){
						var birthday = obj1.i + '-' + obj2.i + '-' + obj3.i;
						zeai.ajax({'url':url+zeai.extname,'data':{"submitok":'ajax_'+objstr,"value":birthday}},function(e){rs=zeai.jsoneval(e);
							if (rs.flag==1){span.html(birthday);obj.setAttribute("data",birthday);}
							//zeai.msg(rs.msg,{"time":0.5});
						});
					},'-');
				break;
				case 'select':
					ios_select1_normal(title,eval(objstr+'_ARR'),defV,function(obj1){
						var sid = obj1.i,sv=obj1.v;
						zeai.ajax({'url':url+zeai.extname,'data':{"submitok":'ajax_'+objstr,"value":sid}},function(e){rs=zeai.jsoneval(e);
							if (rs.flag==1){span.html(sv);obj.setAttribute("data",sid);}
							//zeai.msg(rs.msg,{"time":0.5});
						});
						
					},',');
				break;
				case 'checkbox':
					ZeaiM.divBtmMod({"objstr":objstr,"title":title,"value":defV,"kind":"checkbox",fn:function(chkV){
						zeai.ajax({'url':url+zeai.extname,'data':{"submitok":'ajax_'+objstr,"value":chkV}},function(e){rs=zeai.jsoneval(e);
							if (rs.flag==1){span.html(checkbox_div_list_get_listTitle(objstr,chkV));obj.setAttribute("data",chkV);}
							//zeai.msg(rs.msg,{"time":0.5});
						});
					}});
				break;
			}	
		}
	},
	piczoom:function(url){
		if (url==0){pz_close_fn();return false;}
		if(zeai.ifjson(url)){
			var json=url;
			if (json.browser=='wx'){
				wx.previewImage({current:json.b,urls:json.list});
			}else{
				ZeaiM.piczoom(json.b);
			}
			return;
		}
		var pzM = zeai.addtag('div');pzM.class('piczoom mask alpha0_100');
		var pzPIC = zeai.addtag('img');pzPIC.src=url;pzPIC.className='fadeInUp';
		pzPIC.onload=function(){
			pzM.append(pzPIC);
			pzM.onclick=function(){pz_close_fn();}
			document.body.append(pzM);
			var w=pzPIC.offsetWidth,h=pzPIC.offsetHeight;
			iL = parseInt(zeai.bodyW()/2 - w/2);
			iT = parseInt(zeai.bodyH()/2 - h/2);
			pzPIC.style.left = parseInt(iL)+'px';
			pzPIC.style.left = 0+'px';
			pzPIC.style.top  = parseInt(iT)+'px';
		}
		function pz_close_fn(){
			pzM.removeClass('alpha0_100');pzM.addClass('alpha100_0');
			pzPIC.removeClass('fadeInUp');pzPIC.addClass('fadeInDown');
			setTimeout(function(){
				if (!zeai.empty(pzM)){pzM.remove();}
			},150);
		}
	},
	up_wx:function(json){
		var multiple=(!zeai.ifint(json.multiple) || zeai.empty(json.multiple))?1:json.multiple;
		wx.chooseImage({
			count:multiple,
			sizeType:['compressed'],
			success: function (res) {
				var localIds = res.localIds,serverIds=[];
				var i = 0, length = localIds.length;
				function wxupload() {
					wx.uploadImage({
					localId:localIds[i],
					isShowProgressTips:1,
					success: function (res) {
						i++;serverIds.push(res.serverId);
						zeai.msg(0);
						zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在上传...　'+i+' / '+length,{time:99})
						if (i < length) {
							setTimeout(wxupload,300);
						}else{
							zeai.msg(0);zeai.msg('正在保存中...',{time:30});
							var postjson = {submitok:json.submitok,serverIds:serverIds};
							zeai.ajax({"url":json.url,"ajaxLoading":json.ajaxLoading,"data":postjson},function(e){if(typeof(json.fn)=="function")json.fn(e);});
						}
					},fail: function (res) {alert(JSON.stringify(res));}});
				}
				wxupload();
			}
		});
	},
	up_wx_tmp:function(json){
		var multiple=(!zeai.ifint(json.multiple) || zeai.empty(json.multiple))?1:json.multiple;
		wx.chooseImage({
			count:multiple,
			sizeType:['compressed'],
			success: function (res) {
				var localIds = res.localIds,serverIds=[];
				var i = 0, length = localIds.length;
				function wxupload() {
					wx.uploadImage({
					localId:localIds[i],
					isShowProgressTips:1,
					success: function (res) {
						
						if(typeof(json.fnli)=="function")json.fnli([localIds[i],res.serverId]);
						
						i++;serverIds.push(res.serverId);
						zeai.msg(0);
						zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在上传...　'+i+' / '+length,{time:99})
						if (i < length) {
							setTimeout(wxupload,300);
						}else{
							zeai.msg(0);zeai.msg('正在保存中...',{time:30});
							//var postjson = {submitok:json.submitok,serverIds:serverIds};
							
							if(typeof(json.fn)=="function")json.fn(serverIds);
							
						}
					},fail: function (res) {alert(JSON.stringify(res));}});
				}
				wxupload();
			}
		});
	},
	fade:function(json){
		if (!zeai.empty(json.arr)){
		for(var pg=0;pg<json.arr.length;pg++){
			(function(pg){
				if (zeai.ifint(json.delay)){setTimeout(set,json.delay);}else{set();}
				function set(){o(json.arr[pg]).css('transform:translate('+json.num+')');}
			})(pg);
		}}
	},
	confirmUp:function(json){
		var confirm_mask  = zeai.addtag('div');
		var confirm_box   = zeai.addtag('div');
		var confirm_title = zeai.addtag('div');
		var confirm_ok     = zeai.addtag('div');
		var confirm_cancel = zeai.addtag('div');
		confirm_mask.id  = 'confirmUp_mask';confirm_mask.className = 'mask alpha0_100';
		confirm_box.className = 'confirmUp confirmUpAnm1';
		confirm_cancel.html(json.cancel);
		confirm_mask.onclick = function(){close();}
		confirm_box.onclick = function(e){e.cancelBubble = true;}
		confirm_ok.onclick = function(){confirm_mask.hide();
			confirm_mask.remove();
			if (typeof(json.okfn) == "function"){json.okfn();}else if(typeof(json.okfn) == "string"){zeai.openurl(json.okfn);
			}else if(typeof(okfn) == "object"){json.okfn.submit();}
		}
		confirm_cancel.onclick = function(){close();}
		confirm_box.appendChild(confirm_title);
		confirm_box.appendChild(confirm_ok);confirm_box.appendChild(confirm_cancel);
		confirm_mask.appendChild(confirm_box);
		document.body.appendChild(confirm_mask);
		confirm_title.html(json.title);
		confirm_ok.html(json.ok);
		confirm_mask.show();
		function close(){
			confirm_box.removeClass('confirmUpAnm1');confirm_box.addClass('confirmUpAnm2');confirm_mask.removeClass('alpha0_100');confirm_mask.addClass('alpha100_0');
			setTimeout(function(){confirm_mask.remove();},400);
		}
	},
	div_up:function(json){
		if (json==0){close();return false;}
		if (typeof(json) != "object")return false;
		var fobj=json.fobj/*,vname=json.obj.id*/;
		var H=zeai.bodyH(),W=zeai.bodyW();
		//var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(zeai.bodyW() - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(zeai.bodyH() - 30):json.h;
		var div_up_mask  = zeai.addtag('div');
		var div_up_box   = zeai.addtag('div');
		var div_up_close = zeai.addtag('div');div_up_close.html('<i class="ico">&#xe65b;</i>');div_up_close.className = 'div_up_close div_upAnm1';div_up_close.id='div_up_close';
		div_up_mask.id  = 'div_up_mask';div_up_mask.className = 'mask1 alpha0_100';
		div_up_box.className = 'div_up_box div_upAnm1';
		div_up_mask.onclick = function(){close();}
		div_up_box.onclick = function(e){e.cancelBubble = true;}
		div_up_box.appendChild(json.obj);json.obj.show();
		div_up_mask.appendChild(div_up_close);
		div_up_box.style.height = height+'px';
		div_up_close.style.bottom =  parseInt(height+10)+'px';
		if (typeof(fobj) == "object"){
			div_up_mask.append(div_up_box);
			fobj.append(div_up_mask);
			
			//H=H+fobj.scrollTop*2;
			//div_mask.style.height = fobj.scrollHeight+'px';
			
		}else{
			div_up_mask.append(div_up_box);
			document.body.append(div_up_mask);
		}
		div_up_mask.show();
		div_up_close.onclick=function(){close()}
		function close(){
			if(zeai.empty(json.removeobj)){
				if (typeof(fobj) == "object"){
					fobj.appendChild(json.obj);
				}else{
					document.body.append(json.obj);
				}
				json.obj.hide();
			}
			div_up_box.removeClass('div_upAnm1');div_up_box.addClass('div_upAnm2');
			div_up_close.removeClass('div_upAnm1');div_up_close.addClass('div_upAnm2');
			setTimeout(function(){div_up_mask.removeClass('alpha0_100');div_up_mask.addClass('alpha100_0');},100);
			setTimeout(function(){div_up_mask.remove();},300);
		}
	},
	div:function(json){
		if (json==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		var fobj=json.fobj/*,vname=json.obj.id*/;
		var H=zeai.bodyH(),W=zeai.bodyW();
		var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(zeai.bodyW() - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(zeai.bodyH() - 30):json.h;
		var div_mask  = zeai.addtag('div');div_mask.class('mask1 alpha0_100');/*div_mask.id = 'div_mask'+vname;*/
		if (typeof(fobj) == "object")H=H+fobj.scrollTop*2;
		
		var div_boxm   = zeai.addtag('div');div_boxm.class('big_normal');div_boxm.id = 'div_boxm';
		var div_closem = zeai.addtag('div');div_closem.class('div_closem');div_closem.id='div_closem';
		var i = zeai.addtag('i');div_closem.appendChild(i)
		
		div_mask.onclick = function(){div_closem_fn();}
		div_closem.onclick = function(){div_closem_fn();}
		div_boxm.appendChild(json.obj);json.obj.show();
		div_boxm.append(div_closem);div_mask.append(div_boxm);
		div_boxm.style.width  = width+'px';
		div_boxm.style.height = height+'px';
		div_boxm.style.left = parseInt((W-width)/2) + 'px';
		div_boxm.style.top = parseInt((H-height)/2-30) + 'px';
		div_mask.show();
		
		div_boxm.onclick = function(e){e.cancelBubble = true;}
		div_boxm.addEventListener('touchmove',function(e) {e.cancelBubble = true;});
		div_mask.addEventListener('touchmove',function(e) {e.preventDefault();});
		if (typeof(fobj) == "object"){
			div_mask.style.height = fobj.scrollHeight+'px';
			fobj.append(div_mask);
		}else{
			document.body.append(div_mask);
		}
		function div_closem_fn(){
			if(zeai.empty(json.removeobj)){
				if (typeof(fobj) == "object"){
					fobj.appendChild(json.obj);
				}else{
					document.body.appendChild(json.obj);
				}
				json.obj.hide();
			}
			div_boxm.class('big_small');div_mask.removeClass('alpha0_100');div_mask.addClass('alpha100_0');
			setTimeout(function(){div_mask.remove();},200);
		}
		return div_closem;
	},
	div_pic:function (json){
		if (json==0){div_close_fn();return false;}
		if (typeof(json) != "object")return false;
		var H=zeai.bodyH(),W=zeai.bodyW();
		var width  = (json.w  == 'auto' || zeai.empty(json.w))?parseInt(W - 30):json.w;
		var height = (json.h == 'auto'|| zeai.empty(json.h))?parseInt(H - 30):json.h;
		var div_mask  = zeai.addtag('div');div_mask.class('mask1 alpha0_100');
		var div_box = zeai.addtag('div');div_box.class('bounce div_pic');
		var div_close=zeai.addtag('div');div_close.className='div_pic_close';div_close.html('<i class="ico">&#xe65b;</i>');div_close.title='关闭';
		div_box.append(div_close);div_box.append(json.obj.innerHTML);div_mask.append(div_box);
		div_mask.onclick = function(){div_close_fn();}
		div_box.onclick = function(e){e.cancelBubble = true;}
		div_close.onclick = function(){div_close_fn();}
		var L=parseInt((W-width)/2),T=parseInt((H-height)/2);
		T=(T<0)?0:T;
		div_box.style.width  = width+'px';div_box.style.height = height+'px';
		div_box.style.left = L + 'px';
		div_box.style.top  = T + 'px';
		div_mask.show();
		document.body.append(div_mask);
		function div_close_fn(){
			if (typeof(json.fn) == "function"){json.fn();}
			div_box.removeClass('small_big');div_box.addClass('big_small');div_mask.removeClass('alpha0_100');div_mask.addClass('alpha100_0');
			setTimeout(function(){if(!zeai.empty(o(div_mask))){o(div_mask).remove();}},200);
		}
		return div_close;
	}
}
var p;
function loadJs(src, callback) {
    var script = document.createElement('script'),
	head = document.getElementsByTagName('head')[0];
	var h=head.innerHTML;
	if (h.indexOf(src) == -1){
		script.src = src;
		if (script.addEventListener) {
			script.addEventListener('load', function () {
			   callback();
			}, false);
		} else if (script.attachEvent) {
			script.attachEvent('onreadystatechange', function () {
				var target = window.event.srcElement;
				if (target.readyState == 'loaded') {
				   callback();
				}
			});
		}
		head.appendChild(script);
	}
}
function checkbox_div_list_create(objstr,defvalue,ARR,fobj,clsname){
	var ARR     = arguments[2] ? arguments[2]:'';
	var clsname = arguments[4] ? arguments[4]:'RCW';
	ARR = (zeai.empty(ARR))?eval(objstr+'_ARR'):ARR;
	var chkbox = zeai.addtag('ul');chkbox.id=objstr+'_box';chkbox.name=objstr;chkbox.className=clsname;
	var defv = defvalue.split(',');
	for(var k=0;k<ARR.length;k++){
		var id    = ARR[k].i;
		var text  = ARR[k].v;
		var li = document.createElement('li');
		var checkbox  = document.createElement('input');
		var label = document.createElement('label');
		var i = document.createElement('i');i.className = 'i2';
		var b = document.createElement('b');b.innerHTML = text;b.className = 'S16';
		checkbox.type  ="checkbox";checkbox.className = 'checkskin';checkbox.id = objstr+id;checkbox.name = objstr+'[]';checkbox.value = id;checkbox.checked = defv.in_array(id);
		checkbox.onclick = function (){if (zeai.form.ifcheckbox(objstr+'[]') > checkboxMaxNum){zeai.msg('最多选择 '+checkboxMaxNum+' 项');return false;}};
		label.setAttribute("for",checkbox.id);label.className = 'checkskin-label';label.append(i);label.append(b);
		li.append(checkbox);
		li.append(label);
		chkbox.append(li);
		fobj.append(chkbox);
	}
}
function checkbox_div_list_get_title(id,objstr_ARR){for( var key in objstr_ARR){if (objstr_ARR[key].i == id)return objstr_ARR[key].v;}}
function page(json){var y=(zeai.empty(json.y))?ZEAI_MAIN:json.y;ZeaiM.page.load(json.g,y,json.l);}
function checkbox_div_list_get_listTitle(objstr,list){
	var list = list.split(',');
	var list_text = [];
	for( var key in list){
		if (typeof list[key] == 'string'){
			list_text.push(checkbox_div_list_get_title(list[key],eval(objstr + '_ARR')));
		}
	}
	return list_text.join(",");	
}
function photoUp(json) {
	var btnobj=json.btnobj;
	if(!zeai.empty(o(btnobj))){
		btnobj.onclick=function(){up();}
	}else{up()}
	function up(){	
		if(browser=='h5'){
			zeai.up({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitokBef+"up_h5","ajaxLoading":0,"multiple":json.multiple,
			"fn":function(e){var rs=zeai.jsoneval(e);json._(rs);},
			"li":function(e){var rs=zeai.jsoneval(e);json.li(rs);}
			});
		}else if(browser=='wx'){
			if(json.wxtmp){
				ZeaiM.up_wx_tmp({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitokBef+"up_wx","ajaxLoading":0,"multiple":json.multiple,
				"fn":function(e){json._(e);},
				"fnli":function(e){json.li(e);}
				});
			}else{
				ZeaiM.up_wx({"url":json.url,"upMaxMB":upMaxMB,"submitok":json.submitokBef+"up_wx","ajaxLoading":0,"multiple":json.multiple,
				"fn":function(e){var rs=zeai.jsoneval(e);json._(rs);}
				});
			}
		}
	}
}
var zeaiLoadBack;
function nodata(backName){
	zeai.alertplus({'title':'请完善个人资料','content':'请完善个人资料','title1':'取消','title2':'去完善','fn1':function(){zeai.alertplus(0);},
		'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_info'+zeai.extname,backName,'my_info');}
	});
}
function clickloveb(kind,backName){
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,js:1,data:{submitok:'ajax_clickloveb',uid:sessionStorage.uid,kind:kind}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			o(div_close).click();
			(kind=='contact' && mycontactFn()) || (kind=='chat' && chatFn(backName));
		}else if(rs.flag=='noloveb'){
			zeai.alertplus({'title':rs.title,'content':rs.msg,'title1':'取消','title2':'去充值','fn1':function(){zeai.alertplus(0);},
				'fn2':function(){zeai.alertplus(0);o(div_close).click();ZeaiM.page.load(HOST+'/m1/my_loveb'+zeai.ajxext+'a=cz&jumpurl='+encodeURIComponent(rs.jumpurl),backName,'my_loveb');}
			});
		}else{
			zeai.msg(rs.msg);
		}
	});
}
function chatFn(backName,uid){
	uid=(!zeai.ifint(uid))?sessionStorage.uid:uid;
	ZeaiM.page.load(HOST+'/m1/msg_show'+zeai.ajxext+'uid='+uid,backName,'msg_show');
	setTimeout(function(){if(!zeai.empty(o('msg_sxbtn')))msg_sxbtn.click();},800);
}
var cleandsj;
function gif_index(fobj,box){
	zeai.listEach(zeai.tag(giflist,'li'),function(li){
		li.onclick = function(){
			gift_ajaxdata(li.getAttribute("gid"),fobj,box_gift_index);
		}
	});
}
function gift_ajaxdata(gid,fobj,box){
	zeai.ajax({url:HOST+'/m1/u'+zeai.extname,data:{submitok:'ajax_gift_div',gid:gid,uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag=='nologin'){zeai.openurl(HOST+'/m1/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));return false;}
		div=zeai.div({fobj:fobj,obj:box,title:'送礼物给【'+rs.nickname+'】',w:300,h:290});
		var em=zeai.tag(box,'em')[0];em=em.children;
		em[0].src       = rs.picurl;
		em[1].innerHTML = rs.title;
		em[2].innerHTML = rs.price + lovebstr;
		var a=zeai.tag(box,'a'),tipbox;
		if(box.id=='box_gift_index'){
			a[0].onclick = function (){div.click();}
			tipbox=tips0_100_02;
		}else{
			a[0].onclick = function (){div.click();ZeaiM.page.load(HOST+'/m1/gift'+zeai.ajxext+'uid='+uid,'u','gift_index');}
			tipbox=tips0_100_0;
		}
		a[1].onclick = function (){
			zeai.ajax({url:HOST+'/m1/u'+zeai.extname,data:{submitok:'ajax_gift_send',gid:rs.id,uid:uid}},function(e2){rs2=zeai.jsoneval(e2);
				if(rs2.flag==1){
					div.click();
					tipbox.html('<i class="ico hi">&#xe69a;</i>礼物已送出');tipbox.show();setTimeout(function(){tipbox.hide()},2100);
					if(!zeai.empty(rs2.C) && !zeai.empty(o('gift'))){
						gift.html('<li gid="0" uid="'+uid+'"><i class="ico">&#xe69a;</i></li>');
						gift.append(rs2.C);
						setgift(gift,u,box_gift);
					}
				}else if(rs2.flag=='noloveb'){
					zeai.alertplus({title:rs2.title,content:rs2.msg,title1:'取消','title2':'去充值','fn1':function(){zeai.alertplus(0);},
						'fn2':function(){zeai.alertplus(0);ZeaiM.page.load(HOST+'/m1/my_loveb'+zeai.ajxext+'a=cz&jumpurl='+encodeURIComponent(rs2.jumpurl),'u','my_loveb');}
					});
				}else{
					zeai.msg(rs2.msg);
				}
			});
		}
	});
}
function backtopFn(obj){
    var t = (zeai.empty(obj))?zeai.getScrollTop():obj.scrollTop;
	var backtop=o('backtop'); 
    if(t<88){
		if (backtop.style.display == 'block' && backtop.hasClass('fadeInUp')){
			backtop.class('big_small ');
			setTimeout(backtop_close,200);
			function backtop_close(){backtop.hide();}				
		}
	}else{
		if (backtop.style.display == 'none' || backtop.style.display == ''){
			backtop.class('fadeInUp');
			backtop.show()
		}
	}
	if(zeai.empty(obj)){
		btmTopBtn.onclick=function(){zeai.setScrollTop(0);}
	}else{
		btmTopBtn.onclick=function(){obj.scrollTop=0;}
	}
}
function ifX(){
	var isIPhoneX = /iphone/gi.test(window.navigator.userAgent) && window.devicePixelRatio && window.devicePixelRatio === 3 && window.screen.width === 375 && window.screen.height === 812;
	var isIPhoneXSMax = /iphone/gi.test(window.navigator.userAgent) && window.devicePixelRatio && window.devicePixelRatio === 3 && window.screen.width === 414 && window.screen.height === 896;
	var isIPhoneXR = /iphone/gi.test(window.navigator.userAgent) && window.devicePixelRatio && window.devicePixelRatio === 2 && window.screen.width === 414 && window.screen.height === 896;
	if(isIPhoneX || isIPhoneXSMax || isIPhoneXR){return true;}else{return false;}
}
function mobkind(){
	var u = navigator.userAgent,app = navigator.appVersion;
	var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1;
	var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
	if(isAndroid){
		return 'android';
	}else if(isiOS){
		return 'ios';
	}
}
function is_h5app(){
	var u = navigator.userAgent;
	var isapp = u.indexOf('Html5Plus') > -1;
	if(isapp){return true;}else{return false;}
}
function newdian(rs){
	var numbtm=parseInt(rs.tipnum);
	var numtz=parseInt(rs.num_tz);
	var numsx=parseInt(rs.num_sx);
	if(!zeai.empty(o('num_btm'))){
		if(numbtm==0){
			o('num_btm').remove();
		}else{
			o('num_btm').html(numbtm);
		}
	}
	if(!zeai.empty(o('num_tz'))){
		if(numtz==0){
			o('num_tz').remove();
		}else{
			o('num_tz').html(numtz);
		}
	}
	if(!zeai.empty(o('num_sx'))){
		if(numsx==0){
			o('num_sx').remove();
		}else{
			o('num_sx').html(numsx);
		}
	}
}
function html_decode(str){           
	str = str.replace(/&amp;/g, '&'); 
	str = str.replace(/&lt;/g, '<');
	str = str.replace(/&gt;/g, '>');
	str = str.replace(/&quot;/g, "'");  
	str = str.replace(/&#039;/g, "'");
	return str;  
}
window.addEventListener("popstate",function(){
	if(zeai.empty(location.hash)){
		var Zeai_cn__PageBox=o('Zeai_cn__PageBox');
		if(!zeai.empty(Zeai_cn__PageBox)){
			var goback=getZeaiPstack();
			goback = goback[goback.length-1];
			if(!zeai.empty(goback))goback.click();
		}
		if(!zeai.empty(o('iosMask'))){o('iosMask').click();}
		if(!zeai.empty(o('divBtmMod'))){o('divBtmMod').click();}
	}
});
function getZeaiPstack(){return document.querySelectorAll(".goback");}
if(mobkind()=="ios" && is_h5app()){
	//当页面加载状态改变的时候执行function
	document.onreadystatechange = function(){ 
		if(document.readyState == "complete"){//当页面加载状态为完全结束时进入 
			var thisweb=plus.webview.currentWebview();
			thisweb.setJsFile('_www/zeai.append.js');
	    }
	}
}
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2020/03/21 by supdes
*/
var ZeaiUP={
	pic_Slist:[],localIds:[],
	more:function(json){
		var ul = zeai.tag(json.obj,"ul")[0],btnpic = ul.children[0];
		btnpic.onclick = function(){
			var piclistV=o('piclist').value;
			ZeaiUP.pic_Slist = piclistV.split(',');
			curnum=ZeaiUP.arrLength(ZeaiUP.pic_Slist);
			if(curnum>=maxnum){zeai.msg('最多只能传'+maxnum+'张哦');return;}
			if(is_h5app()){/*app*/}else{
				ZeaiUP.one({
					onclick:false,
					btnadd:ul,
					url:upurl,
					submitokBef:"ajax_photo_",
					multiple:maxnum-curnum,
					wxtmp:true,
					li:function(e){if(browser=='wx'){ZeaiUP.addli({_s:e,ul:ul,end:function(d){json.end(d)}});}else{ZeaiUP.addli({_s:e._s,ul:ul,end:function(d){json.end(d)}});}},//e._s,ul
					end:function(e){if(typeof(json.end)=="function"){zeai.msg(0);json.end(e);}}
				});
			}
		}
	},
	one:function(json){
		var btnobj=json.btnobj;
		if(!zeai.empty(o(btnobj))){
			btnobj.onclick=function(){up();}
		}else{up()}
		function up(){	
			if(browser=='h5'){
				zeai.up({url:json.url,upMaxMB:upMaxMB,submitok:json.submitokBef+"up_h5",ajaxLoading:0,multiple:json.multiple,
				"fn":function(e){var rs=zeai.jsoneval(e);json.end(rs);},
				"li":function(e){var rs=zeai.jsoneval(e);json.li(rs);}
				});
			}else if(browser=='wx'){
				if(json.wxtmp){
					ZeaiUP.wx_tmp({url:json.url,upMaxMB:upMaxMB,submitok:json.submitokBef+"up_wx",ajaxLoading:0,multiple:json.multiple,
					"fn":function(e){json.end(e);},
					"fnli":function(e){json.li(e);}
					});
				}else{
					ZeaiUP.up_wx({url:json.url,upMaxMB:upMaxMB,submitok:json.submitokBef+"up_wx",ajaxLoading:0,multiple:json.multiple,
					"fn":function(e){var rs=zeai.jsoneval(e);json.end(rs);}
					});
				}
			}
		}
	},
	addli:function(json){
		var url=json._s,ul=json.ul,li = zeai.addtag('li'),img = zeai.addtag('img'),b = zeai.addtag('b');
		li.appendChild(img);li.appendChild(b);ul.appendChild(li);
		if(browser=='wx'){
			var local=url[0],url=url[1];
			ZeaiUP.localIds.push(local);
			img.src = local;
		}else{
			img.src = up2+url;
		}
		ZeaiUP.pic_Slist.push(url);
		ZeaiUP.pic_Slist = ZeaiUP.arrReset(ZeaiUP.pic_Slist);
		piclist.value = ZeaiUP.pic_Slist.join(",");
		b.onclick = function (){
			li.parentNode.removeChild(li);
			ZeaiUP.pic_Slist = ZeaiUP.pic_Slist.remove(url);
			piclist.value  = ZeaiUP.arrReset(ZeaiUP.pic_Slist).join(",");
			if(browser=='wx'){
				ZeaiUP.localIds = ZeaiUP.localIds.remove(url);
				ZeaiUP.localIds = arrReset(ZeaiUP.localIds);
			}else{
				zeai.ajax({url:upurl+'&submitok=ajax_tmp_del',data:{url:url}});
			}
			json.end(url);
		}
		img.onclick = function (){
			if(browser=='wx'){
				ZeaiM.piczoom({browser:browser,b:local,list:ZeaiUP.localIds});
			}else{
				ZeaiM.piczoom({browser:browser,b:up2+url.replace('_s.','_b.')});
			}
		}
	},
	arrLength:function(ARR){
		var l=0;
		for(var k=0;k<ARR.length;k++) {
			if(typeof(ARR[k]) == "string" && !zeai.empty(ARR[k]) )l++;
		}
		return l;
	},
	arrReset:function(ARR){
		var l=[];
		for(var k=0;k<ARR.length;k++) {
			if(  typeof(ARR[k]) == "string" && zeai.str_len(ARR[k])>10 )l.push(ARR[k]);
		}
		return l;
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
	wx_tmp:function(json){
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
							if(typeof(json.fn)=="function")json.fn(serverIds);
						}
					},fail: function (res) {alert(JSON.stringify(res));}});
				}
				wxupload();
			}
		});
	}
}

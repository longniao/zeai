/**
* Copyright (C)2001-2099 Zeai.cn V6.0 All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2020/03/15 by supdes
*/
function zeaiplay(zeai_cn) {
	var v = o('zeaiVbox'+zeai_cn),p=o('ZEAI_'+zeai_cn);
	//zeai.msg('视频加载中...');
	ZeaiPC.div({obj:v,w:400,h:430,fn:function(){v.hide();}});
	v.style.width='90%';v.style.height='90%';v.style.position='absolute';v.style.left='20px';v.style.top='20px';
	p=p.getAttribute("value");p=p.replace('.jpg','.mp4');
	v.src=p;v.show();v.play();
	v.addEventListener("contextmenu", function(event){event.preventDefault();});
}
if(!zeai.empty(o('list'))){
zeai.listEach(zeai.tag(list,'p'),function(obj){
	var psrc=obj.getAttribute("value");
	obj.style.backgroundImage='url('+psrc+')';
		
});
}if(!zeai.empty(o('ulist'))){
zeai.listEach(zeai.tag(ulist,'p'),function(obj){
	var psrc=obj.getAttribute("value");
	obj.style.backgroundImage='url('+psrc+')';
		
});
}
function video_add(){
	zeai.alert('请上传15秒以下或20M以下的小视频。',function(){
		video_up({url:PCHOST+"/my_video"+zeai.extname,submitok:"ajax_video_up"},1);
	});
}
function video_up(json,i){
	var ftypearr = ["video/mp4","video/x-ms-wmv","video/quicktime","video/3gpp","video/x-flv"];
	if(zeai.empty(o('video'+i))){
		var video = zeai.addtag('input');video.id='video'+i;video.type='file';video.accept="video/mp4,video/x-ms-wmv,video/quicktime,video/3gpp,video/x-flv";video.hide();
		document.body.append(video);
	}else{video=o('video'+i);}
	video.click();
	video.onchange = function(){
		var FILES = video.files[0];
		if (FILES['size'] > upVMaxMB*1024000){videoNull();zeai.msg('视频太大，已超过'+upVMaxMB+'M，请重选');return false;}
		if (!ftypearr.in_array(FILES['type'])){videoNull();zeai.msg('格式错误,只能 mp4/mov/3gp/mov等'+FILES['type']);return false;}
		var filename = FILES['name'].toLowerCase();var extname = filename.substring(filename.lastIndexOf(".")+1,filename.length);
		zeai.msg("正在上传中...<span id='upbfb'></span>％",{time:333});
		var postjson = {file:FILES,extname:extname};Object.assign(postjson,json);
		zeai.ajax({"url":json.url,"ajaxLoading":0,"data":postjson},function(e){var rs=zeai.jsoneval(e);
			videoNull();zeai.msg(rs.msg,{time:2});
			if(rs.flag==1){setTimeout(function(){zeai.openurl(PCHOST+"/my_video"+zeai.extname);},2000);}
		},function(bfbnum){
			if(!zeai.empty(o('upbfb')))o('upbfb').innerHTML = bfbnum;	
			if (bfbnum==100){zeai.msg(0);zeai.msg('正在上传保存...',{time:33});}
		});
	}
	function videoNull(){zeai.msg(0);video.remove();}
}
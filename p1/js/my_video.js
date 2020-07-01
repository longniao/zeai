/**
* Copyright (C)2001-2099 Zeai.cn V6.0 All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
function zeaiplay(zeai_cn) {
	var v = o('zeaiVbox'+zeai_cn),img=o('img'+zeai_cn);
	zeai.msg('视频加载中...');
	ZeaiPC.div({obj:v,w:400,h:430,fn:function(){v.hide();}});
	v.style.width='90%';v.style.height='90%';v.style.position='absolute';v.style.left='20px';v.style.top='20px';
	img=img.src;img=img.replace('.jpg','.mp4');
	v.src=img;v.show();v.play();
	v.addEventListener("contextmenu", function(event){event.preventDefault();});
}

function videoFn(){
	zeai.listEach(zeai.tag(o('main'),'li'),function(li,i){if(i>0){
		var clsid = li.getAttribute("value"),del;
		var lichild = li.children;
		del = lichild[2];
		if(!zeai.empty(o(del)))del.onclick = function(e){
			zeai.confirm('确认要删除么？',function(){
				zeai.ajax({url:PCHOST+'/my_video'+zeai.extname,data:{submitok:'ajax_del',clsid:clsid}},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
				});
			});
		}
	}});
	videofn(o("btnadd1"),1);
	videofn(o("btnadd2"),2);
}
function videofn(btnobj,i){
	btnobj.onclick=function(){
		video_up({url:PCHOST+"/my_video"+zeai.extname,submitok:"ajax_video_up"},i);
	}
/*	zeai.photoUp({
		btnobj:btnobj,
		upMaxMB:upMaxMB,
		url:PCHOST+"/my_video"+zeai.extname,
		multiple:5,
		submitok:"ajax_pic_path_s_up",
		end:function(rs){
			zeai.msg(rs.msg);
			if(rs.flag==1){zeai.msg(0);zeai.msg('上传成功');setTimeout(function(){location.reload(true);},1000);}
		},
		li:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){main.append('<li><p><img src="'+up2+rs.dbname+'"></p><h4>刚刚</h4></li>');}
		}
	});
*/
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
			videoNull();zeai.msg(rs.msg);
			//o('my_info_videobtn').click();
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		},function(bfbnum){
			if(!zeai.empty(o('upbfb')))o('upbfb').innerHTML = bfbnum;	
			if (bfbnum==100){zeai.msg(0);zeai.msg('正在上传保存...',{time:33});}
		});
	}
	function videoNull(){zeai.msg(0);video.remove();}
}

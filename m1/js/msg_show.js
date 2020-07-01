function bqbtnFn () {
	if (o('bq').style.display == 'block'){
		deflocation();
	}else{
		$('#msg').height(H-bqH);
		o('bq').show();
		o('write').style.bottom = (bqH + 3) + 'px';
		zeai.setScrollTop(0);
		scrollTobtm();
		audiostyle(1);
	}
}
function domm() {
	var s= o("bqlist");
	del_ff(s);
	var chils= s.childNodes;
	for(var i=0; i<chils.length;i++){
		var obj = chils[i];
		obj.onclick = function () {
			o('content').append(this.outerHTML);
		}
	}
}

function msgFn() {
	o('bq').hide();
	$('#msg').height(H);
	o('write').style.bottom = '0px';
	zeai.setScrollTop(0);
}

function deflocation() {
	o('bq').hide();
	$('#msg').height(H);
	o('write').style.bottom = '0px';
	//o('msg').scrollTop = 9999;
	scrollTobtm();
	zeai.setScrollTop(0);
}
function bqlist(der) {
	if (der == 'top'){
		o('bq').scrollTop = 0;
	}else if(der == 'bottom'){
		o('bq').scrollTop = 999;
	}
}
function del_ff(elem){
	var elem_child = elem.childNodes;
	for(var i=0; i<elem_child.length;i++){
		if(elem_child[i].nodeName == "#text" && !/\s/.test(elem_child.nodeValue)){
			elem.removeChild(elem_child[i]);
		}
	}
}

function ajax_chat_audio(json){
	zeai.ajax(json,function(e){//rs=zeai.jsoneval(e);
		var rt = e;
		if (!zeai.empty(rt)){
			if (o('msg').innerHTML == '<div class="nodatatipsS">..暂无信息..</div>')o('msg').innerHTML = "";
			var rt = rt.split("|"); 
			var sid      = rt[0];
			var path_s   = up2 + '/'+ rt[1] + '.mp3';
			var difftime = rt[2];
			var t        = rt[3];
			var C = "<em voiceId=\""+sid+"\" src=\""+path_s+"\" t=\""+t+"\"><div class=\"voiceIcon\"></div><div class=\"voiceSec\">"+difftime+"\"</div></em>";
			C = "<dl class='my'><dt>"+cook_photo_s_str+"</dt><dd>"+C+"</dd></dl>";
			o('msg').append(C);
			o('msg').scrollTop = 9999;
			setTimeout("scrollTobtm()",500);
		}
	});	
}

function ajax_app_audio(rt){
	if (!zeai.empty(rt)){
		if (o('msg').innerHTML == '<div class="nodatatipsS">..暂无信息..</div>')o('msg').innerHTML = "";
		var rt = rt.split("|"); 
		var sid      = rt[0];
		var path_s   = up2 + '/'+ rt[1] + '.mp3';
		var difftime = rt[2];
		var t        = rt[3];
		var C = "<em voiceId=\""+sid+"\" src=\""+path_s+"\" t=\""+t+"\"><div class=\"voiceIcon\"></div><div class=\"voiceSec\">"+difftime+"\"</div></em>";
		C = "<dl class='my'><dt>"+cook_photo_s_str+"</dt><dd>"+C+"</dd></dl>";
		o('msg').append(C);
		o('msg').scrollTop = 9999;
		setTimeout("scrollTobtm()",500);
	}

}


function iput(){
	var obj = o('content');
	obj.style.height = parseInt(obj.scrollHeight-10) + 'px';
	if (obj.scrollHeight > 100)obj.style.height = 100 + 'px';
	o('bq').hide();
	o('write').style.bottom = '0px';
	$('#msg').height(H);
}

function onblurFN(){
	msg_send(uid);
	setTimeout(function(){zeai.setScrollTop(0);},200);
}
function msg_send(uid){
	var content = o('content').innerHTML;
	content  = clearHtml(content);
	if(!zeai.empty(content)){
		
		content2 = content.replace(new RegExp('<img src='+HOST+'/res/bq/','g'),'[img]');
		content2 = content2.replace(/.gif>/g,'[/img]');
//		content2 = content.replace(/<img[^>]*src=[\'\"\s]*([^\s\'\"]+)[^>]*>/ig,'[img]'+'$1'+'[/img]');
//		content2 = content2.replace(new RegExp(HOST+'/res/bq/','g'),'');
//		content2 = content2.replace(/.gif/g,'');
		
		if (zeai.str_len(content2) > 100){zeai.msg('输入内容过多');deflocation();return false;}
		//o('msg').append("<dl class='my'><dt>"+cook_photo_s_str+"</dt><dd>"+content+"</dd></dl>");
		o('sendbtn').style.backgroundColor = '#ccc';
		o('sendbtn').html('<img src='+HOST+'/res/loadingData.gif>');
		zeai.ajax({url:HOST+'/m1/msg_show'+zeai.extname,data:{submitok:'ajax_add',uid:uid,content:content2}},function(e){rs=zeai.jsoneval(e);
			if (rs.flag==1){
				if (o('msg').innerHTML == '<div class="nodatatipsS">..暂无信息..</div>')o('msg').html('');
				o('msg').append("<dl class='my'><dt>"+cook_photo_s_str+"</dt><dd>"+content+"</dd></dl>");
				//setTimeout("scrollTobtm()",500);
				o('bq').hide();
				o('sendbtn').style = 'initial';
				o('sendbtn').style.backgroundColor = '#E83191';o('sendbtn').html('发送');
				o('content').html('');
				o('content').style = 'initial';
				o('msg').scrollTop = 9999;
				o('write').style.bottom = '0px';
				o('content').style.height = '25px';//ios
				
				$('#msg').height(H);
			}
		});	
	}
}
function ajax_getMsgMore(uid){
	var p = o('p').value;
	p = parseInt(o('p').value);
	o('loadmore').innerHTML = "努力加载中...";
	zeai.ajax({url:HOST+'/m1/msg_show'+zeai.extname,data:{submitok:'ajax_getMsgMore',uid:uid,p:p}},function(e){//rs=zeai.jsoneval(e);
			s=e.split("|GYL-SUPDES|"); 
			var C=s[0];var ifmore=s[1];
			if (ifmore == 1){
				o('loadmore').innerHTML = "查看更多消息";
				p = parseInt(o('p').value);
				p++;
				o('p').value = p;
			}else{
				o('loadmore').innerHTML   = "已全部加载完";
				o('loadmore').hide();
			}
			//C = C.replace(/\[img\]([^\[]*)\[\/img\]/ig,'<img src=\'$1.gif\' />');
			C = C.replace(/\[img\]([^\[]*)\[\/img\]/ig,'<img src=\''+HOST+'/res/bq/$1.gif\' />');
			//o('msg').append(C);
			o('msg').insertAdjacentHTML('afterBegin',C)
			o('msg').scrollTop = 0;
			setTimeout("scrollTotop()",500);
	});
}
function ajax_chk_flag(uid){
	zeai.ajax({url:HOST+'/m1/msg_show'+zeai.extname,data:{submitok:'ajax_chk_flag',uid:uid}},function(e){
		if (e == 1)ajax_getmess_one(uid);	
	});	
}
function ajax_getmess_one(uid){
	zeai.ajax({url:HOST+'/m1/msg_show'+zeai.extname,data:{submitok:'ajax_getmess_one',uid:uid,p:1}},function(e){
		if(!zeai.empty(e)){
			var arr=zeai.jsoneval(e);
			var arrlen=arr.length;
			var c,t,addtime,k;
			for(k=0;k<arrlen;k++) {
				t = arr[k][0];
				c = arr[k][1];
				addtime= arr[k][2];
				switch (t) {
					case '1':break;
					case '2':
						var a        = c.split("|");
						var sid      = a[0];
						var path_s   = up2 + '/'+ a[1] + '.mp3';
						var difftime = a[2];
						var c        = "<em voiceId=\""+sid+"\" src=\""+path_s+"\" t=\""+addtime+"\"><div class=\"voiceIcon\"></div><div class=\"voiceSec\">"+difftime+"\"</div></em>";	
					break;
				}
				//c = c.replace(/\[img\]([^\[]*)\[\/img\]/ig,'<img src=\'$1.gif\' />');
				c = c.replace(/\[img\]([^\[]*)\[\/img\]/ig,'<img src=\''+HOST+'/res/bq/$1.gif\' />');
				c = escape2Html(c);
				if (c != '')o('msg').append("<dl><dt>"+photo_s_str+"</dt><dd>"+c+"</dd></dl>");
			}
			o('msg').scrollTop = 9999;
			setTimeout("scrollTobtm()",500);
		}
	});	
}

function escape2Html(str) {
	var arrEntities={'lt':'<','gt':'>','nbsp':' ','amp':'&','quot':'"'};
	return str.replace(/&(lt|gt|nbsp|amp|quot);/ig,function(all,t){return arrEntities[t];});
}
function ajax_getmess(uid){
	var msg = o('msg');
	msg.html('<br><br><br><br><img src='+HOST+'/res/loadingData.gif>');
	zeai.ajax({url:HOST+'/m1/msg_show'+zeai.extname,data:{submitok:'ajax_getmess',uid:uid,p:1}},function(e){//rs=zeai.jsoneval(e);
			var s = e.split("|GYL-SUPDES|");
			var C = s[0];var ifmore=s[1];
			if (ifmore == 1){
				o('p').value = 1;
				msg.onscroll = function () { 
					var loadmore = o("loadmore");
					var scrollTop = msg.scrollTop;
					scrollTop = (scrollTop == 0)?1:scrollTop;
					if(scrollTop > 100){
						loadmore.style.opacity = 0;
					}else{
						var tmd = 100-scrollTop;
						tmd = tmd/100;
						loadmore.style.opacity = tmd;
					}
					o('loadmore').show();
				}
			}
			//C = C.replace(/\[img\]([^\[]*)\[\/img\]/ig,'<img src=\'$1.gif\' />');
			C = C.replace(/\[img\]([^\[]*)\[\/img\]/ig,'<img src=\''+HOST+'/res/bq/$1.gif\' />');
			
			msg.html(C);
			msg.scrollTop = 9999;
			setTimeout("scrollTobtm()",500);
	});
}
function scrollTobtm(){
	o("msg").scrollTop=o("msg").scrollHeight;
}
function scrollTotop(){
	o("msg").scrollTop=0;
}
function delbtm0(){
	clearInterval(btm0);
}
function audiostyle(type){
	var type = arguments[0] ? arguments[0]:'';	
	var ifopen = o('ifopen').value;
	if (ifopen == 1 || type == 1){
		o('content').show();o('startRecord').hide();
		o('sendbtn').style.backgroundColor = '#E83191';
		o('audiobtn').style.backgroundPosition = 'left top';
		o('ifopen').value  = 0;
	}else if(ifopen == 0){
		o('content').hide();o('startRecord').show();
		o('sendbtn').style.backgroundColor = '#ccc';
		o('audiobtn').style.backgroundPosition = '-72px top';
		o('ifopen').value = 1;
		o('startRecord').innerHTML = '按住 说话';
	}
}
function clearHtml(sTxt) {
	var c=sTxt;
	c = c.replace(/<script.*?>.*?<\/scrip[^>]*>/ig,"");
	c = c.replace(/<[^>]*?javascript:[^>]*>/ig,"");
	c = c.replace(/<style.*?>.*?<\/styl[^>]*>/ig,"");
	c = c.replace(/<(\w[^>]*) style="([^"]*)"([^>]*)/ig, "<$1$3");
	//c = c.replace(/<img.*?src=([^ |>]*)[^>]*>/ig,"<img src=user/$1>");
	c = c.replace(/<\/?(code|h\d)[^>]*>/ig,'<br>');
	c = c.replace(/<\/?(a|sohu|form|input|select|textarea|iframe|SUB|SUP|table|tr|th|td|tbody|module|OPTION|onload|div|center)(\s[^>]*)?>/ig,"");
	c = c.replace(/<\?xml[^>]*>/ig,'');
	c = c.replace(/<\!--.*?-->/ig,'');
	c = c.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onclick="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onclick=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onerror="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onload="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) onmouseover="([^"]*)"([^>]*)/ig, "<$1$3");
	c = c.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/ig, "<$1$3");
	c = c.replace(/<\\?\?xml[^>]*>/ig, "");
	c = c.replace(/<\/?\w+:[^>]*>/ig, "");
	c = c.replace(/<a.*?href="([^"]*)"[^>]*>/ig,"<a href=\"$1\">");
	//c = c.replace(/<center>\s*<center>/ig, '<center>');
	//c = c.replace(/<\/center>\s*<\/center>/ig, '</center>');
	//c = c.replace(/<center>/ig, '<center>');
	//c = c.replace(/<\/center>/ig, '</center>');
	c=c.replace(/\'/g,"");
	c=c.replace(/\"/g,"");
	//c=c.replace(/</g,"《").replace(/>/g,"》");
	sTxt = c;
	return sTxt;
}

function wxRecord(){
	wx.error(function (res){alert(res.errMsg);});
	wx.ready(function () {
		//第一次进来点授权
		if(!localStorage.rainAllowRecord || localStorage.rainAllowRecord !== 'true'){
			wx.startRecord({
				success: function(){
					localStorage.rainAllowRecord = 'true';
					wx.stopRecord();
				},
				cancel: function () {
					alert('拒绝授权录音，您将无使用语音功能');
				}
			});
		}	
		//
		var images = {localId: [],serverId: []};
		//audio
		document.querySelector('#audiobtn').onclick = function (){audiostyle();deflocation();};
		var voice = {localId: '',serverId: ''};
		//时间超过回调
		wx.onVoiceRecordEnd({
			complete: function (res) {
				voice.localId = res.localId;
				audio_upload();
			}
		});
		//
		document.querySelector('#startRecord').ontouchstart = function (e) {
			e.preventDefault();e.stopPropagation();
			o('startRecord').innerHTML = '松开 发送';
			o('startRecord').style.backgroundColor = '#ccc';
			wx.startRecord({
					success: function(){localStorage.rainAllowRecord = 'true';},
					cancel: function () {alert('拒绝授权录音，您将无使用语音功能');}
				}
			);	
			o('starttime').value = new Date().getTime();
		};
		//停止
		document.querySelector('#startRecord').ontouchend = function () {
			var starttime = parseInt(o('starttime').value);
			var endtime   = parseInt(new Date().getTime());
			var difftime  = endtime - starttime;
			o('startRecord').innerHTML = '按住 说话';
			o('startRecord').style.backgroundColor = '#fff';
			wx.stopRecord({
				success: function (res) {
					voice.localId = res.localId;
					audio_upload(difftime);
				},
				fail: function (res) {alert(JSON.stringify(res));}
			});
		}
		function audio_upload(difftime) {
			wx.uploadVoice({
			  localId:voice.localId,
			  success: function (res) {
				var jsonurl={url:HOST+'/m1/msg_show'+zeai.extname,data:{submitok:'ajax_chat_audio',uid:uid,difftime:difftime,sid:res.serverId}};
					ajax_chat_audio(jsonurl);
				}
			});
		}
		wx.onVoicePlayEnd({
			success: function (res) {
				var localId = res.localId;
				$('#msg em[voiceId="' + localId + '"]').find(".voiceIcon").removeClass("play");
			}
		});
	});
}

function msgAudioLiPlay(){
	//$('#msg').height(H);
	var audio_array="";
	$("#msg").on("click","em",function (e) {
		var self = $(this);
		var srcId   = up2 + self.attr("src");
		var voiceId = self.attr("voiceId");
		var t       = self.attr("t");
		if(self.find(".voiceIcon").hasClass("play")) {
			self.find(".voiceIcon").removeClass("play");
			if(voiceId.indexOf("://") > -1 && browser=='wx') {
				wx.pauseVoice({localId:voiceId});
			}else{
				audio_array.pause();
			}
		} else {
			if(browser=='wx'){
				wxplay();
			}else if(is_h5app()){
				appplay(self.attr("src"),self);
			}else{
				h5play();
			}
			function wxplay(){
				//播放
				self.find(".voiceIcon").addClass("play");
				//if(voiceId.indexOf("weixin://") > -1) {
				if(voiceId.indexOf("://") > -1) {
					wx.playVoice({
						localId:voiceId
					});
				} else {
					var nowt = Date.parse(new Date())/1000;
					if ((nowt - t) < 200000){//259200
						wx.downloadVoice({
							serverId:voiceId,
							isShowProgressTips:0,
							success:function (res) {
								iflocal = false;
								localId = res.localId;
								//alert('下载成功' + localId);
								self.attr("voiceId",localId);
								wx.playVoice({
									localId:localId
								});
								iflocal = false;
							},
							fail:function(res) {
								//alert('下载失败');
								//self.attr("voiceId",localId);
								//过期处理
								loadSound(self,srcId);
								audio_array.play();
							}
						});
					}else{
						loadSound(self,srcId);
						audio_array.play();
					}
				}
			}
			function h5play(){
				var audioarr = document.querySelectorAll(".voiceIcon");
				for(var i=0;i<audioarr.length;i++) {audioarr[i].removeClass("play");}
				if (!zeai.empty(audio_array))audio_array.pause();
				self.find(".voiceIcon").addClass("play");
				var nowt = Date.parse(new Date())/1000;
				loadSound(self,srcId);
				audio_array.play();
			}
		}
	});
	function loadSound(obj,srcId){
		if (zeai.empty(audio_array)){audio_array = document.createElement("audio");}
		audio_array.src = srcId;
		$(obj).append(audio_array);
		audio_array.load();
		audio_array.addEventListener("canplaythrough", function(){
			audio_array.play();
			audio_array.loop=false;
		});
		audio_array.addEventListener("ended",function(){
			$(obj).find(".voiceIcon").removeClass("play");
		});
	}
}

function appplay(url,obj){
	obj.find(".voiceIcon").addClass("play");
	app_downloadaudio(url,HOST+'/m1/msg_show.php',function(){
		obj.find(".voiceIcon").removeClass("play");
	});
}

function bk(par) {
	o('ZEAIGOBACK-msg_show').click();
}

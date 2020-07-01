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
	c=c.replace(/<br>/g,"");
	c=c.replace(/\'/g,"");
	c=c.replace(/\"/g,"");
	c=c.replace(/\r/g,"");
	c=c.replace(/\n/g,""); 
	sTxt = c;
	return sTxt;
}
function escape2Html(str) {
	var arrEntities={'lt':'<','gt':'>','nbsp':' ','amp':'&','quot':'"'};
	return str.replace(/&(lt|gt|nbsp|amp|quot);/ig,function(all,t){return arrEntities[t];});
}
function deflocation() {
	o('bq').hide();
	//o('msg').scrollTop = 9999;
	scrollTobtm();
}
function bqbtnFn () {
	if (o('bq').style.display == 'block'){
		deflocation();
	}else{
		o('bq').show();
		scrollTobtm();
	}
}
function scrollTobtm(){
	o("msg").scrollTop=o("msg").scrollHeight;
}
function msgFn() {
	o('bq').hide();
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
function del_ff(elem){
	var elem_child = elem.childNodes;
	for(var i=0; i<elem_child.length;i++){
		if(elem_child[i].nodeName == "#text" && !/\s/.test(elem_child.nodeValue)){
			elem.removeChild(elem_child[i]);
		}
	}
}
function ajax_getmess_one(uid){
	zeai.ajax({url:PCHOST+'/chat'+zeai.extname,data:{submitok:'ajax_getmess_one',uid:uid,p:1}},function(e){
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
function ajax_getmess(uid){
	var msg = o('msg');
	msg.html('<br><br><br><br><img src='+HOST+'/res/loadingData.gif>');
	zeai.ajax({url:PCHOST+'/chat'+zeai.extname,data:{submitok:'ajax_getmess',uid:uid,p:1}},function(e){//rs=zeai.jsoneval(e);
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
			C = C.replace(/\[img\]([^\[]*)\[\/img\]/ig,'<img src=\''+HOST+'/res/bq/$1.gif\' />');
			msg.html(C);
			msg.scrollTop = 9999;
			setTimeout("scrollTobtm()",500);
	});
}
function ajax_chk_flag(uid){
	zeai.ajax({url:PCHOST+'/chat'+zeai.extname,data:{submitok:'ajax_chk_flag',uid:uid}},function(e){
		if (e == 1)ajax_getmess_one(uid);	
	});	
}
function delbtm0(){
	clearInterval(btm0);
}
function iput(){
	var obj = o('content');
	o('bq').hide();
}
function msg_send(uid){
	var content = clearHtml(o('content').innerHTML);
	if(!zeai.empty(content)){
		content2 = content.replace(new RegExp('<img src='+HOST+'/res/bq/','g'),'[img]');
		content2 = content2.replace(/.gif>/g,'[/img]');
		//content2 = content.replace(/<img[^>]*src=[\'\"\s]*([^\s\'\"]+)[^>]*>/ig,'[img]$1[/img]');
		//content2 = content2.replace(new RegExp(HOST+'/res/bq/','g'),'');
		//content2 = content2.replace(/.gif/g,'');
		if (zeai.str_len(content2) > 100){zeai.msg('输入内容过多');deflocation();return false;}
		o('msg').append("<dl class='my'><dt>"+cook_photo_s_str+"</dt><dd>"+content+"</dd></dl>");
		o('sendbtn').style.backgroundColor = '#ccc';
		o('sendbtn').html('<img src='+HOST+'/res/loadingData.gif>');
		zeai.ajax({url:PCHOST+'/chat'+zeai.extname,data:{submitok:'ajax_add',uid:uid,content:content2}},function(e){rs=zeai.jsoneval(e);
			if (rs.flag==1){
				if (o('msg').innerHTML == '<div class="nodatatipsS">..暂无信息..</div>')o('msg').html('');
				//o('msg').append("<dl class='my'><dt>"+cook_photo_s_str+"</dt><dd>"+content+"</dd></dl>");
				o('bq').hide();
				o('sendbtn').style = 'initial';
				o('sendbtn').style.backgroundColor = '#E83191';o('sendbtn').html('发送');
				o('content').html('');
				o('content').style = 'initial';
				o('msg').scrollTop = 9999;
			}
		});	
	}else{
		o('content').html('');
		zeai.msg('请输入聊天内容',o('content'));	
	}
}
function onblurFN(){
	//msg_send(uid);
}
function ajax_getMsgMore(uid){
	var p = o('p').value;
	p = parseInt(o('p').value);
	o('loadmore').innerHTML = "努力加载中...";
	zeai.ajax({url:PCHOST+'/chat'+zeai.extname,data:{submitok:'ajax_getMsgMore',uid:uid,p:p}},function(e){//rs=zeai.jsoneval(e);
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
			C = C.replace(/\[img\]([^\[]*)\[\/img\]/ig,'<img src=\''+HOST+'/res/bq/$1.gif\' />');
			o('msg').insertAdjacentHTML('afterBegin',C)
			o('msg').scrollTop = 0;
			setTimeout("scrollTotop()",500);
	});
}
document.addEventListener("contextmenu",function(event){event.preventDefault();});
function scrollTotop(){
	o("msg").scrollTop=0;
}
document.onkeydown = function (e) {
    var theEvent = window.event || e;
    var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
    if (code == 13) {
		if(ifchat==0){
			lockopen(uid);
		}else{
			msg_send(uid);
		}
		e.preventDefault();
    }
}
function lockopen(uid){
	zeai.ajax({url:PCHOST+'/chat'+zeai.extname,data:{submitok:'ajax_lockopen',uid:uid}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		}else if(rs.flag=='clickloveb_confirm'){
			zeai.div({obj:o('chat_lovebHelp'),title:rs.title,w:340,h:320});
		}else if(rs.flag=='noucount'){
			zeai.div({obj:o('chat_daylooknumHelp'),title:rs.msg,w:340,h:320});
		}else if(rs.flag=='nolevel'){
			zeai.div({obj:o('chat_levelHelp'),title:'会员级别不够',w:340,h:320});
		}
	});
}
function clickloveb(uid,kind){
	zeai.ajax({url:PCHOST+'/chat'+zeai.extname,js:1,data:{submitok:'ajax_clickloveb',uid:uid,kind:kind}},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		}else if(rs.flag=='noloveb'){
			zeai.alertplus({'title':rs.title,'content':rs.msg,'title1':'取消','title2':'去充值','fn1':function(){zeai.alertplus(0);},
				'fn2':function(){
					zeai.openurl_(PCHOST+'/my_loveb'+zeai.ajxext+'a=cz&jumpurl='+encodeURIComponent(rs.jumpurl));
				}
			});
		}else{
			zeai.msg(rs.msg);
		}
	});
}
function msgAudioLiPlay(){
	var audio_array="";
	$("#msg").on("click","em",function (e) {
		var self = $(this);
		var srcId   = up2 + self.attr("src");
		var voiceId = self.attr("voiceId");
		var t       = self.attr("t");
		if(self.find(".voiceIcon").hasClass("play")) {
			self.find(".voiceIcon").removeClass("play");
			audio_array.pause();
		} else {
			h5play()
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

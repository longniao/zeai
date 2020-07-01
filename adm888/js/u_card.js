function makecardFn() {
	zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在生成',{time:10})
	html2canvas(cardcontent).then(function(canvas) {
		var cW=canvas.width,cH=canvas.height;
		var img = Canvas2Image.convertToImage(canvas,cW, cH);
		
		img.style.width='100%';
		cardbox_view.append(img);
		img.onload=function(){
			zeai.msg(0);
			setTimeout(function(){
				ZeaiM.div_pic({obj:cardbox_view,title:'',w:313,h:500,fn:function(){
					if(!zeai.empty(o('cardbox_view')))o('cardbox_view').html('');
				}});
				zeai.msg('鼠标右键复制/另存为',{time:1});
			},200);
			
		}
		/*
		downLoadImage(canvas,picname);
		function downLoadImage(canvas,name) {
			var a = document.createElement("a");
			a.href = canvas.toDataURL();
			a.download = name;
			a.click();
			zeai.msg(0);
		}
		*/
	});
}
function get_mLTW(i){
	i = parseInt(i);
	switch (i) {
		case 1:return {l:0.355,t:0.055,w:0.32,uid_t:0.444,uid_color:'#ff6b93',me_t:0.59,you_t:0.921,ewm_t:1.21,ewm_l:0.313,ewm_color:'#3f688b',sitename_t:1.520,sitename_color:'#ff5093'};break;
		case 2:return {l:0.348,t:0.0823,w:0.32,uid_t:0.52,uid_color:'#13786d',me_t:0.63,you_t:0.98,ewm_t:1.22,ewm_l:0.717,ewm_w:0.2,ewm_color:'#a72645',sitename_t:1.520,sitename_color:'#13786d'};break;
		case 3:return {l:0.348,t:0.048,w:0.32,uid_t:0.42,uid_color:'#fff',me_t:0.57,you_t:0.95,ewm_t:1.21,ewm_l:0.717,ewm_w:0.2,ewm_color:'#fff',sitename_t:1.508,sitename_color:'#FFEAB3'};break;
		case 4:return {l:0.35,t:0.049,w:0.32,uid_t:0.415,uid_color:'#fff',me_t:0.54,you_t:0.90,ewm_t:1.21,ewm_l:0.717,ewm_w:0.2,ewm_color:'#fff',sitename_t:1.508,sitename_color:'#FFEAB3'};break;
		case 5:return {l:0.348,t:0.122,w:0.32,uid_t:0.472,uid_color:'#000',me_t:0.605,you_t:0.96,ewm_t:1.21,ewm_l:0.717,ewm_w:0.2,ewm_color:'#000',sitename_t:1.538,sitename_color:'#000'};break;
		case 6:return {l:0.348,t:0.032,w:0.32,uid_t:0.395,uid_color:'#f18336',me_t:0.531,you_t:0.893,ewm_t:1.17,ewm_l:0.717,ewm_w:0.2,ewm_color:'#f18336',sitename_t:1.51,sitename_color:'#000'};break;
		case 7:return {l:0.3395,t:0.089,w:0.32,uid_t:0.45,uid_color:'#323232',me_t:0.58,you_t:0.93,ewm_t:1.17,ewm_l:0.717,ewm_w:0.2,ewm_color:'#323232',sitename_t:1.475,sitename_color:'#323232'};break;
		case 8:return {l:0.348,t:0.044,w:0.32,uid_t:0.42,uid_color:'#cb3d3c',me_t:0.591,you_t:0.94,ewm_t:1.21,ewm_l:0.717,ewm_w:0.2,ewm_color:'#cb5958',sitename_t:1.53,sitename_color:'#ca5857'};break;
		case 9:return {l:0.348,t:0.13,w:0.32,uid_t:0.48,uid_color:'#cb3d3c',me_t:0.585,you_t:0.92,ewm_t:1.18,ewm_l:0.717,ewm_w:0.2,ewm_color:'#cb5958',sitename_t:1.54,sitename_color:'#ca5857'};break;
		case 10:return {l:0.348,t:0.027,w:0.32,uid_t:0.4,uid_color:'#819f35',me_t:0.53,you_t:0.93,ewm_t:1.16,ewm_l:0.717,ewm_w:0.2,ewm_color:'#999',sitename_t:1.54,sitename_color:'#ca5857'};break;
	}
}
function ewmkindFn() {
	if(card_ewm.className=='uewm'){
		this.html('换为个人二维码');
		card_ewm.class('hnewm');card_ewm.src=kf_wxpic;ewmtitle.html('长按加红娘微信<br>享受一对一牵线');
	}else{
		this.html('换为红娘二维码');
		card_ewm.class('uewm');card_ewm.src=HOST+'/sub/creat_ewm'+zeai.ajxext+'&url='+uhref;ewmtitle.html('长按识别二维码<br>马上认识Ta');
	}
}

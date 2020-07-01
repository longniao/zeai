/******************************************
作者: 郭余林　QQ:797311 (supdes)
未经本人同意，请不要删除版权
*****************************************/
var if__www_zeai_cn__video = false;
function ZEAI_win_piczoom(W,H,text,kind){
	bodyW = document.body.clientWidth;
	bodyH = document.documentElement.clientHeight;
	o('divbg_piczoom').style.width   = bodyW+'px';   
	o('divbg_piczoom').style.height  = bodyH+'px';   
	o('divbg_piczoom').show();
	o('divcontent_piczoom').style.height = H+'px';
	o('divcontent_piczoom').style.width  = W+'px';
	cW = parseInt(bodyW/2 - W/2);
	cH = parseInt(bodyH/2 - H/2);
	o('divcontent_piczoom').style.left = cW+'px';
	o('divcontent_piczoom').style.top  = cH+'px';
	o('close_piczoom').style.left = parseInt(cW+W-30)+'px';
	o('close_piczoom').style.top  = parseInt(cH-30)+'px';
	o('divcontent_piczoom').show();
	o('close_piczoom').show();
	var Cpic_piczoom = o('Cpic_piczoom');
	Cpic_piczoom.innerHTML = text;
	Cpic_piczoom.style.lineHeight = H+'px';
	if(kind=='video'){
		if__www_zeai_cn__video=true;
	}else{
		if__www_zeai_cn__video=false;
		var imgs = Cpic_piczoom.getElementsByTagName('img');
		imgs[0].style.maxHeight  = H+'px';
	}
}
function ZEAI_win_piczoom_close(){
	if (if__www_zeai_cn__video)o('zeaiVbox').pause();
	zeai.showSwitch('divbg_piczoom,divcontent_piczoom,close_piczoom');
}
document.write("<style type='text/css'>");
document.write("#divbg_piczoom {position:fixed; top:0px;filter:alpha(opacity=75);-moz-opacity:0.75;opacity:0.75;background-color:#000;z-index:999;left:0px;display:none}");
document.write("#divcontent_piczoom {position:fixed;z-index:999;overflow:hidden;display:none;background-color:#000;padding:5px;text-align:center;cursor:zoom-out;font-size:0px}");
document.write("#divcontent_piczoom img{vertical-align:middle}");
document.write("#close_piczoom{position:fixed;z-index:999;display:none;width:60px;height:60px;border-radius:34px;border:#666 2px solid;background-color:#333;background-image:url('images/close2.png');cursor:pointer}");
document.write("#close_piczoom{background-size:50px 50px;background-repeat:no-repeat;background-position:center center}");
document.write("#close_piczoom:hover{background-color:#666}");
document.write("#zeaiVbox{width:700px;height:700px;cursor:pointer}");
document.write("</style>");
document.write("<div id='divbg_piczoom'></div>");
document.write("<div id='divcontent_piczoom' class='fadeInDown'><div id='Cpic_piczoom'></div></div>");
document.write("<div id='close_piczoom' class='fadeInUp'></div>");
o('divcontent_piczoom').onclick = function(e){
	if (!if__www_zeai_cn__video){
		ZEAI_win_piczoom_close();
	}
}
o('close_piczoom').onclick = function(){ZEAI_win_piczoom_close();}
o('divbg_piczoom').onclick = function(){ZEAI_win_piczoom_close();}
function piczoom(pic) {
	var ext = pic.split('.').pop().toLowerCase();
	if (ext == 'mp4'){
		var cvs = '<video id="zeaiVbox" controls="controls" autoplay style="width:555px;height:555px">';
		cvs     += '<source src="'+pic+'?'+new Date().getTime()+'">';
		cvs     += '您浏览器本太低，无法播放，建议使用谷歌chrome浏览器</video>';
		ZEAI_win_piczoom(555,555,cvs,'video');
	}else if(ext == 'mp3'){
		var cvs = '<audio autoplay="autoplay" id="zeaiVbox" controls="controls" src="'+pic+'?'+new Date().getTime()+'">您浏览器本太低，无法播放，建议使用谷歌chrome浏览器</audio>';
		ZEAI_win_piczoom(555,555,cvs);
	}else{
		if (pic!='')ZEAI_win_piczoom(555,555,'<img src='+pic+'?'+new Date().getTime()+'>');
	}
}
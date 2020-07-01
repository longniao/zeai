/******************************************
作者: 郭余林　QQ:797311 (supdes)
未经本人同意，请不要删除版权
*****************************************/
function ZEAI_winclose_confirm(){showhidden('mask_confirm',0);showhidden('box_confirm',0);}
function ZEAI_win_confirm(title,ifunc){
	var H = 131;
	bodyH = document.documentElement.clientHeight;
	showhidden('mask_confirm',1);showhidden('box_confirm',1);
	getid('box_confirm').style.height = H+'px';
	cH = getid('box_confirm').offsetHeight;
	cH = parseInt((bodyH - cH)/2-50);
	getid('box_confirm').style.top  = cH+'px';
	getid('title_confirm').innerHTML = title;
	getid('cancel_confirm').onclick = function(){ZEAI_winclose_confirm();}
	//getid('ok_confirm').onclick = function(){ZEAI_winclose_confirm();eval(ifunc);}
	if( (typeof(ifunc) == "string" || typeof(ifunc) == "number") && !empty(ifunc)){
		getid('ok_confirm').onclick = function(){ZEAI_winclose_confirm();eval(ifunc);}
	}else if(ifunc){
		getid('ok_confirm').onclick = function(){
			ZEAI_winclose_confirm();
			ifunc();
		}
	}
}
document.write('<link href="/css/win_confirm.css" rel="stylesheet" type="text/css" \/>');
document.write("<div id='mask_confirm' class='alpha0_100'></div>");
document.write("<div id='box_confirm' class='animattime_fast fadeInDown'>");
document.write("<div id='title_confirm'></div>");
document.write("<div><a id='ok_confirm'>确定</a><a id='cancel_confirm'>取消</a></div>");
document.write("</div>");
getid('mask_confirm').addEventListener('touchmove',function(e) {e.preventDefault();});
getid('box_confirm').addEventListener('touchmove',function(e) {e.preventDefault();});
/******************************************
作者: 郭余林　QQ:797311 (supdes) www.Zeai.cn
未经本人同意，请不要删除版权
*****************************************/
var div_move = 0;
var IE = document.all?true:false;
var tempX,tempY,oldX,oldY;
var have_move =	0;
var maxL = parseInt((cut_box_w-photo_s_w)/2);
var maxT = parseInt((cut_box_h-photo_s_h)/2);
function grasp(){
	div_move	=	1;
	if(IE)	{o("source_div").setCapture();}
}
function free(){
	div_move	=	0;
	have_move	=	0;
	if(IE)	{o("source_div").releaseCapture();}
	validatorImgXY();
}
function validatorImgXY (){
	var imgw =parseInt(o("show_img").width) ;
	var imgh =parseInt(o("show_img").height);
	if(IE &&(angle==1 || angle== 3)){
		var temp = imgw;
		imgw = imgh 
		imgh = temp ;
	}
	var l = parseInt(o("source_div").style.left);
	var t = parseInt(o("source_div").style.top);
	if(l>maxL)o("source_div").style.left = maxL+"px";
	if(t>maxT)o("source_div").style.top  = maxT+"px";
	var maxl = photo_s_w + maxL - imgw;
	var maxt = photo_s_h + maxT - imgh ;
	if(l < maxl)o("source_div").style.left = maxl+"px";
 	if(t < maxt)o("source_div").style.top = maxt+"px";
}
function centerLine(img){
	var h = parseInt(img.height);
	var w = parseInt(img.width);
	if(w <= photo_s_w)o("source_div").style.left = maxL+"px";
	if(h <= photo_s_h)o("source_div").style.top = maxT+"px";
	if(w>(photo_s_w + maxL) && h>(photo_s_h + maxT)){
		o("source_div").style.left = "0px";
		o("source_div").style.top  = "0px";
	}else{
		o("source_div").style.left = "0px";
		o("source_div").style.top  = "0px";
		validatorImgXY ();
	}
}

function getMouseXY(e){
	if (IE)	{
		tempX = event.clientX + document.body.scrollLeft
		tempY = event.clientY + document.body.scrollTop
	}else{
		tempX = e.pageX
		tempY = e.pageY
	}	
	if (tempX < 0){tempX = 0}
	if (tempY < 0){tempY = 0}	
}

function move_it(e){
	getMouseXY(e);
	if(div_move	==	1)	{
		if(have_move ==	0){
			oldX        = tempX;
			oldY        = tempY;
			have_move	= 1;
		}
		var left	=	parseInt(o("source_div").style.left);
		var top		=	parseInt(o("source_div").style.top);
		//var left	=	o("source_div").style.left;
		//var top		=	o("source_div").style.top;
		o("source_div").style.left	=	(left	+	tempX	-	oldX)	+	'px';
		o("source_div").style.top	=	(top	+	tempY	-	oldY)	+	'px';
		oldX	=	tempX;
		oldY	=	tempY;
	}
}

function change_size(method){
	//var oldImagesrc = o("show_img").src;//gyl 新加
	if(method == 1){
		var per	= 1.25;
	}else{
		var per	= 0.9;
	}
	var obj = o("show_img") ;
	var h = obj.height;
	var w = obj.width;
	if(w * per <photo_s_w || h * per<photo_s_h){return;}
	if(IE){
		obj.width  = w * per ;
		obj.height = h * per ;
	}else{
		if(obj.tagName=="CANVAS"){
			var image = o('show_img');
			var canvas = document.createElement('canvas');
			if(canvas.getContext("2d")) {
				canvas.id = image.id;
				canvas.alt = image.alt;
				canvas.name = image.name;
				canvas.title = image.title;
				canvas.className = image.className;
				canvas.style.cssText = image.style.cssText;
				canvas.height = parseFloat(h*per);
				canvas.width  = parseFloat(w*per);
				var object = image.parentNode;
				object.replaceChild(canvas,image);
				var context = canvas.getContext("2d");
				context.drawImage(obj,0,0,parseFloat(w*per),parseFloat(h*per));
			}
		}else{
			obj.width  = parseFloat(w*per);
			obj.height = parseFloat(h*per);
		}
	}
	validatorImgXY ();
	setImageSize();//后加
}
var angle = 0;
function turn(method){
	var isIE = (document.uniqueID)?1:0;
	angle = angle + parseInt(method);
	if(angle < 0)angle += 4;
	if(angle > 4)angle -= 4;
        try{
        var canvas = document.createElement('canvas');
        var image = o('show_img');
        var oldsrc = o("show_img").src;
		if(canvas.getContext("2d")) {
		    canvas.id = image.id;
			canvas.alt = image.alt;
			canvas.name = image.name;
			canvas.title = image.title;
			canvas.className = image.className;
			canvas.style.cssText = image.style.cssText;
			canvas.height=image.width;
            canvas.width=image.height;
            var object = image.parentNode;
		    object.replaceChild(canvas,image);
			var context = canvas.getContext("2d");		
		    if(parseInt(method) > 0) {
		    	context.translate(image.height, 0);
		    	context.rotate(90*Math.PI/180);
			} else {
				context.translate(image.height, 0);
				context.rotate(90*Math.PI/180);
			  	context.translate(image.width, 0);
				context.rotate(90*Math.PI/180);
			  	context.translate(image.height, 0);
				context.rotate(90*Math.PI/180);						
		  	}
            context.drawImage(image,0,0,image.width,image.height);
		}
		document['myform'].turn.value = angle;
		o("show_img").src = oldsrc;
		}catch(e){}			
	document['myform'].turn.value = angle;
	validatorImgXY ();
}
function supdessubmit(){
	var Oform =	document.myform;
	Oform.width.value = o("show_img").width;
	Oform.height.value= o("show_img").height;
	Oform.left.value  = o("source_div").style.left;
	Oform.top.value   = o("source_div").style.top;
	if(IE)Oform.turn.value	= o('show_img').style.filter.match(/\d/)[0];
	Oform.submit();
}
window.onload = function (){setImageSize();}
function setImageSize(){
	var img  = o("show_img");
	var per1 = 0 ;
	var per2 = 0;
	var per  = 0;
	var h = img.height;
	var w = img.width;
	if(w < photo_s_w || h < photo_s_h){
		
		if(w < photo_s_w ){
			per1 = photo_s_w - w;
			img.width  = photo_s_w;
			img.height = (h + per1);
		}
		h = img.height;
		w = img.width;
		if(h < photo_s_h){
			per2 = photo_s_h - h;
			img.height = photo_s_h;
			img.width= (w + per2);
		}
		validatorImgXY ();
	}
  	centerLine(img);
}
var fnRotateScale = function(dom, angle, scale) {
    if (dom && dom.nodeType === 1) {
        angle = parseFloat(angle) || 0;
        scale = parseFloat(scale) || 1;
        if (typeof(angle) === "number") {
            //IE
            var rad = angle * (Math.PI / 180);
            var m11 = Math.cos(rad) * scale, m12 = -1 * Math.sin(rad) * scale, m21 = Math.sin(rad) * scale, m22 = m11;
            if (!dom.style.Transform) {
                dom.style.filter = "progid:DXImageTransform.Microsoft.Matrix(M11="+ m11 +",M12="+ m12 +",M21="+ m21 +",M22="+ m22 +",SizingMethod='auto expand')";
            }
            //Modern
            dom.style.MozTransform = "rotate("+ angle +"deg) scale("+ scale +")";
            dom.style.WebkitTransform = "rotate("+ angle +"deg) scale("+ scale +")";
            dom.style.OTransform = "rotate("+ angle +"deg) scale("+ scale +")";
            dom.style.Transform = "rotate("+ angle +"deg) scale("+ scale +")";
        }     
    }
};

/******************************************
作者: 郭余林　QQ:797311 (supdes)
未经本人同意，请不要删除版权
*****************************************/
function ZEAI_winclose_alert(){
showhidden('mask_alert',0);showhidden('box_alert',0);
var formid = getid('formid').value;
if (formid != ''){
var regm = "^[a-zA-Z][a-zA-Z0-9_]*$";
if (formid == '0'){	
window.opener=null;window.open('','_self');window.close();
}else if(formid == '-1'){window.history.back(-1);
}else if(formid.match(regm)){getid(formid).focus();
}else if(empty(formid)){

}else{window.location.href=formid;}}}
function ZEAI_win_alert(title,formid){
	
var formid = arguments[1] ? arguments[1]:'';
//var W = 280
var H = 111;
//bodyW = document.body.clientWidth;
bodyH = document.documentElement.clientHeight;
//getid('mask_alert').style.width   = bodyW+'px';   
//getid('mask_alert').style.height  = bodyH+'px'; 
showhidden('mask_alert',1);showhidden('box_alert',1);
getid('box_alert').style.height = H+'px';
//getid('box_alert').style.width = W+'px';
//cW = getid('box_alert').offsetWidth;
cH = getid('box_alert').offsetHeight;
//cW = parseInt((bodyW - cW)/2);
cH = parseInt((bodyH - cH)/2-50);
//getid('box_alert').style.left = cW+'px';
getid('box_alert').style.top  = cH+'px';
getid('title_alert').innerHTML = title;
if (!empty(formid) || formid == 0)getid('formid').value = formid;}
document.write('<link href="win_alert.css" rel="stylesheet" type="text/css" \/>');
document.write("<input type='hidden' value='' id='formid'>");
document.write("<div id='mask_alert' class='alpha0_100'></div>");
document.write("<div id='box_alert' class='animattime_fast fadeInDown'>");
document.write("<div id='title_alert'></div>");
document.write("<div id='close_alert' onclick='ZEAI_winclose_alert();'>确定</div>");
document.write("</div>");
if (!empty(getid('mask_div'))){getid('mask_alert').addEventListener('touchmove',function(e) {e.preventDefault();});}
if (!empty(getid('mask_div'))){getid('box_alert').addEventListener('touchmove',function(e) {e.preventDefault();});}
if (!empty(getid('mask_div'))){getid('close_alert').onclick = function(){ZEAI_winclose_alert();}}
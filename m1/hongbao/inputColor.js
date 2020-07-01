function input(formobj){//,classstr
	var form = document.getElementById(formobj); 
	var a = form.elements.length;
	for (var j=0;j<a;j++){
		var objclsname = form.elements[j].className;
		//if(chkifarrvalue(classstr,objclsname)){
			form.elements[j].onfocus = function(){
				this.className = this.className+" inputed";
			}
			form.elements[j].onblur  = function(){
				var str = this.className;
				str=str.replace(" inputed","");
				this.className = str;
			}
		//}
	}
}
/*
function chkifarrvalue(arr,value){
	var strs= new Array(); 
	strs = arr.split(","); 
	var i = strs.length;
	//alert(i);
	if (i <= 1){
		if (arr == value)return true;
	}else{
		while(i--){
			if(strs[i] == value){
				return true;
			}
		}
	}
	return false;
}
*/
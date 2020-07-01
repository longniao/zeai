<?php !function_exists('ifalone') && exit('Forbidden!');?>
<div id="bottom_top"><img src="<?php echo $_ZEAI['adm2'];?>/images/frd_up.png"><span>返回<br>顶部</span></div>
<script>
window.onscroll = function(){
    var t = document.documentElement.scrollTop || document.body.scrollTop; 
    if( t < 30 ){
		if (bottom_top.style.display == 'block' && bottom_top.hasClass('fadeInUp')){
			bottom_top.class('big_small ');
			setTimeout(bottom_top_close,200);
			function bottom_top_close(){bottom_top.hide();}				
		}
	}else{
		if (bottom_top.style.display == 'none' || bottom_top.style.display == ''){
			bottom_top.class('fadeInUp');
			bottom_top.show()
		}
	}
}
bottom_top.onclick = function(){window.scrollTo(0,0);}
</script>
</body>
</html>
<script>
//启动全局tips title
zeai.listEach('.tips',function(obj){
	var tv = obj.getAttribute("tips-title");
	var td = obj.getAttribute("tips-direction");
	if (!zeai.empty(tv)){
		obj.onmouseover = function(){zeai.tips(tv,obj,{time:60,direction:td,color:'#333'})}
		obj.onmouseout = function(){zeai.tips(tv,obj,{flag:'hide'})}
	}
});
var adm2='<?php echo $_ZEAI['adm2'];?>';
</script>
<script src="<?php echo $_ZEAI['adm2'];?>/js/piczoom.js"></script>
<?php ob_end_flush();?>
<?php !function_exists('zeai_alone') && exit('Forbidden!');
if(ifint($cook_uid)){
	$num = $db->NUM($cook_uid,"tipnum","id=".$cook_uid." AND tipnum>0");
	$num = $num[0];
	$tipnum_str = ($num>0)?'<span id="num_btm">'.$num.'</span>':'';
}
$iftrend=true;$ifhn=true;
if(@!in_array('hn',$navarr) )$ifhn=false;
if(@!in_array('trend',$navarr) )$iftrend=false;
if($iftrend && $ifhn){
	$navAW=20;
}elseif(!$iftrend && $ifhn || $iftrend && !$ifhn){
	$navAW=25;
}else{
	$navAW=33;
}
?>
<div id="Zeai_cn__PageBox"></div><div id="blankpage"></div>
<?php
if($_ZEAI['Mnavbtmkind']==2){
	$Mnavbtm = json_decode($_ZEAI['Mnavbtm'],true);
	$nn=0;
	if(@is_array($Mnavbtm) && @count($Mnavbtm)>0){
		foreach ($Mnavbtm as $V){
			$title=$V['title'];$path1=$V['path1'];$url=$V['url'];
			if(empty($title) || empty($path1) || empty($url))continue;
			$nn++;
		}
	}
}
if($_ZEAI['Mnavbtmkind']==2 && $nn>0 ){?>
	<div class="navdiy huadong" id="nav">
    	<div class="shadow"></div>
		<?php $ww = intval(100/$nn);?>
        <style>.navdiy a.ed font{color:#FF6F6F}.navdiy a{width:<?php echo $ww;?>%}</style>
		<?php
		foreach ($Mnavbtm as $V){
			$title=urldecode($V['title']);
			$var  =urldecode($V['var']);
			$path1=$V['path1'];
			$path2=$V['path2'];
			$url=urldecode($V['url']);
			$pathok=($nav==$var)?$path2:$path1;
			if(empty($title) || empty($path1) || empty($url))continue;?>
            <a href="<?php echo $url; ?>"<?php if($nav==$var)echo' class="ed"';?>><img src="<?php echo $_ZEAI['up2'].'/'.$pathok;?>" /><font><?php echo $title;?></font></a>
		<?php }?>
	</div>
<?php }else{ ?>
	<style>nav a{width:<?php echo $navAW;?>%}</style>
    <nav id="nav" class="huadong">
        <div class="shadow"></div>
        <a href="<?php echo HOST; ?>/?z=index"<?php if($nav=='home')echo' class="ed"';?>><i class="home">&#xe629;</i><font>首页</font></a>
        <?php if ($iftrend){?><a href="<?php echo HOST; ?>/?z=trend"<?php if($nav=='trend')echo' class="ed"';?>><i class="find">&#xe63b;</i><font>交友圈</font></a><?php }?>
        <a href="<?php echo HOST; ?>/?z=tuijian"<?php if($nav=='tj')echo' class="ed"';?>><i class="home">&#xe6ab;</i><font>推荐</font></a>
        <a href="<?php echo HOST; ?>/?z=msg&e=sx"<?php if($nav=='msg')echo' class="ed"';?>><i class="navmsg">&#xe676;<?php echo $tipnum_str;?></i><font>消息</font></a>
        <a href="<?php echo HOST; ?>/?z=my"<?php if($nav=='my')echo' class="ed"';?>><i class="my">&#xe632;</i><font>我的</font></a>
    </nav>
<?php }?>
<div id="backtop"><a href="#top" id="btmTopBtn"><i class="ico">&#xe60a;</i>顶部</a></div>
</body>
</html>
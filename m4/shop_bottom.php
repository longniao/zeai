<?php
//wwwzeaicnbottomnav
foreach ($navbtm as $V){if($V['f']==0 || empty($V['t']) || empty($V['img']) || empty($V['url']))continue;$nn++;$newnavbtm[]=$V;}
if (count($newnavbtm) >= 1 && is_array($newnavbtm)){echo '<div class="shop_btm flex">';foreach ($newnavbtm as $V){
    $title=urldecode($V['t']);
    $var=urldecode($V['var']);
    $img=$V['img'];
    $img2=$V['img2'];
    $url=urldecode($V['url']);
    $imgok=($nav==$var)?$img:$img2;
    $imgok=$_ZEAI['up2'].'/'.$imgok;?>
    <a href="<?php echo $url;?>"<?php if($nav==$var)echo' class="ed"';?>><img src="<?php echo $imgok;?>"><font><?php echo $title;?></font></a>
<?php }echo '</div>';}?>
<div id="backtop"><a href="#top" id="btmTopBtn"><i class="ico">&#xe60a;</i>顶部</a></div>
</body>
</html>
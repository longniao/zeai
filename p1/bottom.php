<?php !function_exists('zeai_alone') && exit('Forbidden!');
require_once ZEAI.'cache/config_wxgzh.php';


?>
<div class="bottom">
    <ul>
        <li><dt>关于我们</dt><dd><a href="<?php echo Href('about');?>">网站介绍</a><?php if(@in_array('hn',$navarr)){?><a href="<?php echo Href('hongniang');?>">线下人工服务</a><?php }?><a href="<?php echo Href('kefu');?>">联系我们</a></dd></li>
        <li <?php if(@!in_array('article',$navarr)){echo 'style="display:none"';}?>><dt>婚恋学堂</dt><dd>
			<?php
			if(@in_array('article',$navarr)){
            $rt2=$db->query("SELECT id,title FROM ".__TBL_NEWS_KIND__." WHERE id>1 ORDER BY px DESC,id DESC LIMIT 3");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {
                for($j=0;$j<$total2;$j++) {
                    $rows2 = $db->fetch_array($rt2,'num');
                    if(!$rows2) break;
                    $kindid   = $rows2[0];
                    $kindtitle=dataIO($rows2[1],'out');
                    $clss=($kindid==$t)?' class="ed"':'';
                    echo '<a href="'.HOST.'/p1/news.php?t='.$kindid.'">'.$kindtitle.'</a>';
            }}}?>
        </dd></li>
        <li><dt>注册登录</dt><dd><a href="<?php echo Href('clause');?>">注册条款</a><a href="<?php echo HOST;?>/p1/reg.php">会员注册</a><a href="<?php echo HOST;?>/p1/login.php">会员登录</a></dd></li>
        <li><p><?php if (!empty($_ZEAI['m_ewm'])){?><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['m_ewm']; ?>?<?php echo $_ZEAI['cache_str'];?>"><?php }?></p><span><font>手机版二维码</font></span></li>
        <li><p><?php if (!empty($_GZH['wx_gzh_ewm'])){?><img src="<?php echo $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm']; ?>?<?php echo $_ZEAI['cache_str'];?>"><?php }?></p><span><font>公众号二维码</font></span></li>
        <li><h1><i class="ico">&#xe60e;</i> <?php echo $_ZEAI['kf_tel']; ?></h1><h3>周一至周六：09:00~17:30</h3><a href="<?php echo Href('kefu');?>">联系在线客服</a></li>
    </ul>
    
    <div class="bottom2"><div class="bottom2C"><?php echo dataIO($_ZEAI['pc_bottom'],'out');?></div></div>
    
    </div>
</div>

<div class="Zeai_sidebar">
    <li title="公众号版"><i class="ico ico1">&#xe611;</i><em><?php if (!empty($_GZH['wx_gzh_ewm'])){?><img src="<?php echo $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm']; ?>?<?php echo $_ZEAI['cache_str'];?>"><?php }?><span class="tipss"><i class="ico icoo2">&#xe607;</i>微信扫码，进入微信版</span></em></li>
    <li title="手机/APP版"><i class="ico ico1">&#xe627;</i><em><?php if (!empty($_ZEAI['m_ewm'])){?><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['m_ewm']; ?>?<?php echo $_ZEAI['cache_str'];?>"><?php }?><span class="tipss"><i class="ico icoo3">&#xe627;</i>手机扫码，进入手机版</span></em></li>
    <li title="点击进入客服中心"><a href="<?php echo Href('kefu');?>"><i class="ico ico1">&#xe621;</i></a></li>
    <li title="点击返回顶部" id="Zeai_bottom_top"><i class="ico ico1">&#xe60a;</i><span class="top">返回<br>顶部</span></li>

</div>

<script>

window.onscroll = function(){
    var t = zeai.getScrollTop(); 
    if( t < 300 ){
		if (Zeai_bottom_top.style.display == 'block' && Zeai_bottom_top.hasClass('fadeInUp')){
			Zeai_bottom_top.class('big_small');
			setTimeout(Zeai_bottom_top_close,200);
			function Zeai_bottom_top_close(){Zeai_bottom_top.hide();}				
		}
	}else{
		if (Zeai_bottom_top.style.display == 'none' || Zeai_bottom_top.style.display == ''){
			Zeai_bottom_top.class('fadeInUp');
			Zeai_bottom_top.show()
		}
	}
}
Zeai_bottom_top.onclick = function(){window.scrollTo(0,0);}
//iframe huangdong
var widthBar = 17;
var root = document.documentElement;	
if (typeof window.innerWidth == 'number'){	widthBar = window.innerWidth - root.clientWidth;}
//
</script>



</body></html>
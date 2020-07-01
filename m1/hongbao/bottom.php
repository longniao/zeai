<?php !function_exists('zeai_alone') && exit('Forbidden!');?>
<div class="clear"></div><div class="blankb"></div>
<nav>
    <div class="shadow"></div>
    <a href="<?php echo HOST; ?>/?z=index"<?php if($nav=='home')echo' class="ed"';?>><i class="home">&#xe629;</i><font>首页</font></a>
    <a href="<?php echo HOST; ?>/?z=trend"<?php if($nav=='trend')echo' class="ed"';?>><i class="find">&#xe61d;</i><font>发现</font></a>
    <a href="<?php echo HOST; ?>/?z=index&e=hongniang"<?php if($nav=='hongniang')echo' class="ed"';?>><i class="navhn">&#xe621;</i><font>红娘</font></a>
    <a href="<?php echo HOST; ?>/?z=msg&e=sx"<?php if($nav=='msg')echo' class="ed"';?>><i class="navmsg">&#xe644;</i><font>消息</font></a>
    <a href="<?php echo HOST; ?>/?z=my"<?php if($nav=='my')echo' class="ed"';?>><i class="my">&#xe632;</i><font>我的</font></a>
</nav>
<script src="<?php echo HOST; ?>/m1/hongbao/win_alert.js"></script>

</body>
</html>
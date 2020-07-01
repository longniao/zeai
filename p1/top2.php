<div class="navbox">
	<div class="nav">
        <a href="<?php echo HOST;?>" title="返回首页"><img src="<?php echo $_ZEAI['up2']."/".$_ZEAI['pclogo'];?>" class="logo"></a>
        <ul>
        	<a href="<?php echo HOST;?>"<?php echo ($nav=='index')?' class="ed idx"':'';?>>首页</a>
        	<a href="<?php echo HOST;?>/p1/my"<?php echo ($nav=='my')?' class="ed"':'';?>>我的</a>
        	<a href="0">找缘分</a>
        	<a href="0">约会</a>
        	<a href="0">活动</a>
            <a href="0">红娘</a>
        	<a href="0">动态</a>
            <a href="0">情感</a>
        	<a href="0">视频</a>
        	<a href="0">圈子</a>
        	<a href="0">红包</a>
        </ul>
        <?php
		
		if (ifint($cook_uid)){
		$photo_s_url = (!empty($cook_photo_s) )?$_ZEAI['up2'].'/'.$cook_photo_s:HOST.'/res/photo_s'.$cook_sex.'.png';
		$sexbg       = (empty($cook_photo_s))?' class="m sexbg'.$cook_sex.'"':'class="m"';
		$photo_str   = '<img src="'.$photo_s_url.'"'.$sexbg.'>';
		?>
		<style>
        .nav .ico3{right:-20px}
        .nav .logined{right:120px}
        </style>
        <div class="logined S5">
        	<?php echo $photo_str;?><span><?php echo $cook_uname;?></span><i class="ico xj">&#xe60b;</i>
            <div class="j S5"></div>
            <div class="embox S5">
            <em>

            </em>
            </div>
        </div>
        <?php }else{?>
        <div class="loginreg"><a href="<?php echo HOST;?>/p1/login.php">登录</a> | <a href="<?php echo HOST;?>/p1/reg.php">注册</a></div>
        <?php }?>
        <div class="ico3">
        	<a href="0" class="time">
            	<i><img src="<?php echo HOST;?>/p1/img/clock.gif" title="在线时长"></i>
                <div class="j S5"></div>
            	<div class="timebox S5">
                <em>
                	<div class="clockbox ">
                    	
                        <div class="clock_gif clock_run"></div>
                        <div class="djs">
                        	<div class="text">在线时间</div>
                            <font>5</font>时<font>22</font>分<font>23</font>秒
                        </div>
                    </div>
                	 <div class="tips">每天最多累计时间10小时<br>(满48小时可 <b>免费兑换</b> 爱豆)</div>
                </em>
                </div>
            </a>
        	<a href="0" title="站内信"><i class="ico">&#xe640;</i></a>
        	<a href="0" title="升级VIP会员"><i class="ico">&#xe6ab;</i></a>
        </div>
    </div>
</div>

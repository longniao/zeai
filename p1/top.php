<?php !function_exists('IFZEAI') && exit('Forbidden!');
if(ifint($cook_uid)){
	$tipnum  = $db->ROW(__TBL_USER__,"photo_s,photo_f,tipnum","id=".$cook_uid,'name');
	$data_photo_s = $tipnum['photo_s'];
	$data_photo_f = $tipnum['photo_f'];
	$tipnum=$tipnum['tipnum'];$iftipnum=1;
}
?>
<div class="navbox">
	<div class="nav">
        <a href="<?php echo HOST;?>" title="返回首页" class="logo"><img src="<?php echo $_ZEAI['up2']."/".$_ZEAI['pclogo'];?>?<?php echo $_ZEAI['cache_str'];?>"></a>
        <ul>
        	<a href="<?php echo HOST;?>"<?php echo ($nav=='index')?' class="ed idx"':'';?>>首页</a>
        	<a href="<?php echo HOST;?>/p1/my.php"<?php echo ($nav=='my')?' class="ed"':'';?>>我的</a>
        	<a href="<?php echo Href('user');?>"<?php echo ($nav=='user')?' class="ed"':'';?>>找缘分</a>
            <?php 
			if($_ZEAI['navkind']==2){
				$navdiy = json_decode($_ZEAI['navdiy'],true);
				if (count($navdiy) >= 1 && is_array($navdiy)){
					foreach ($navdiy as $V){
						$Vurl=dataIO($V['url2'],'out');$Vvar=dataIO($V['var'],'out');
						if($V['f']==0 || empty($V['t']) || empty($Vurl))continue;
						?><a href="<?php echo $Vurl;?>"<?php echo ($nav==$Vvar)?' class="ed"':'';?>><span><?php echo dataIO($V['t'],'out');?></span></a><?php }
				}
			}else{?>
                <?php if(@in_array('dating',$navarr)){?><a href="<?php echo Href('dating');?>"<?php echo ($nav=='dating')?' class="ed"':'';?>>约会</a><?php }?>
                <?php if(@in_array('party',$navarr)){?><a href="<?php echo Href('party');?>"<?php echo ($nav=='party')?' class="ed"':'';?>>活动</a><?php }?>
                <?php if(@in_array('hn',$navarr)){?><a href="<?php echo Href('hongniang');?>"<?php echo ($nav=='hongniang')?' class="ed"':'';?>>红娘</a><?php }?>
                <?php if(@in_array('trend',$navarr)){?><a href="<?php echo Href('trend');?>"<?php echo ($nav=='trend')?' class="ed"':'';?>>交友圈</a><?php }?>
                <?php if(@in_array('article',$navarr)){?><a href="<?php echo Href('news');?>"<?php echo ($nav=='news')?' class="ed"':'';?>>学堂</a><?php }?>
                <?php if(@in_array('video',$navarr)){?><a href="<?php echo Href('video');?>"<?php echo ($nav=='video')?' class="ed"':'';?>>视频</a><?php }?>
                <?php if(@in_array('hb',$navarr)){?><a href="<?php echo Href('hongbao');?>"<?php echo ($nav=='hongbao')?' class="ed"':'';?>>红包</a><?php }?>
            <?php }?>
        </ul>
        <?php
		if (ifint($cook_uid)){
			if(!empty($data_photo_s)){
				$cook_photo_s=$data_photo_s;
				setcookie("cook_photo_s",$data_photo_s,null,"/",$_ZEAI['CookDomain']);
			}else{
				setcookie("cook_photo_s",'',null,"/",$_ZEAI['CookDomain']);
				$cook_photo_s='';
			}
			$cook_photo_s_url = (!empty($cook_photo_s) )?$_ZEAI['up2'].'/'.$cook_photo_s:HOST.'/res/photo_s'.$cook_sex.'.png';
			$cook_photo_m_url = (!empty($cook_photo_s) )?$_ZEAI['up2'].'/'.getpath_smb($cook_photo_s,'m'):HOST.'/res/photo_m'.$cook_sex.'.png';
			$sexbg       = (empty($cook_photo_s))?' class="m sexbg'.$cook_sex.'"':'class="m"';
			$photo_str   = '<img src="'.$cook_photo_s_url.'"'.$sexbg.'>';
			$photo_str_index = '<img src="'.$cook_photo_m_url.'"'.$sexbg.'>';
			?>
            <div class="logined S5" onClick="zeai.openurl('<?php echo HOST;?>/p1/my.php')">
                <?php echo $photo_str;?><span><?php echo (empty($cook_nickname)?$cook_uname:$cook_nickname);?></span><i class="ico xj">&#xe60b;</i>
                <div class="j S5"></div>
                <div class="embox S5">
                <dl>
                	<dt>
                    	<a href="<?php echo HOST;?>/p1/my_vip.php" class="vip"><i class="ico">&#xe6ab;</i>升级VIP</a>
                    	<a href="<?php echo HOST;?>/p1/my_loveb.php"><i class="ico">&#xe618;</i><?php echo $_ZEAI['loveB'];?>账户</a>
                    	<a href="<?php echo HOST;?>/loginout.php">退出登录</a>
                    </dt>
                    <dd>
                    	<?php if (in_array('chat',$navarr)){?><a href="<?php echo HOST;?>/p1/my_msg.php"><i class="ico">&#xe640;</i>私信消息</a><?php }?>
                    	<a href="<?php echo HOST;?>/p1/my_msg.php?t=4"><i class="ico">&#xe654;</i>系统通知</a>
                    	<a href="<?php echo HOST;?>/p1/my_browse.php"><i class="ico">&#xe67a;</i>谁看过我</a>
                    	<a href="<?php echo HOST;?>/p1/my.php"><i class="ico">&#xe648;</i>每日签到</a>
                    	<a href="<?php echo HOST;?>/p1/my_info.php"><i class="ico">&#xe65c;</i>完善资料</a>
                    	<a href="<?php echo HOST;?>/p1/my_info.php"><i class="ico">&#xe664;</i>上传头像</a>
                    	<a href="<?php echo HOST;?>/p1/my_cert.php"><i class="ico rectico">&#xe613;</i>诚信认证</a>
                    	<a href="<?php echo HOST;?>/p1/my_set.php?t=3"><i class="ico passico">&#xe619;</i>修改密码</a>
                    </dd>
                </dl>
                </div>
            </div>
            <div class="msgvip">
                <a href="<?php echo HOST;?>/p1/my_msg.php" title="私信/通知"><i class="ico">&#xe640;</i><?php if ($tipnum>0){?><b><?php echo $tipnum;?></b><?php }?></a>
                <a href="<?php echo HOST;?>/p1/my_vip.php" title="升级VIP会员"><i class="ico">&#xe6ab;</i></a>
            </div>
        <?php }else{?>
        	<div class="loginreg"><a href="<?php echo HOST;?>/p1/login.php"><i class="ico">&#xe607;</i> 登录</a> | <a href="<?php echo HOST;?>/p1/reg.php">注册</a></div>
        <?php }?>
    </div>
</div>

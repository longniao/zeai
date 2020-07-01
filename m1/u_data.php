<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
if (!ifint($uid) && ($i<>'u_aboutus' || $i<>'u_data' || $i<>'u_mate' || $i<>'u_contact' || $i<>'u_cert' ))json_exit(array('flag'=>0,'msg'=>'会员不存在或已被锁定'));
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=u&a='.$uid.'&i='.$i;
$currfields = "RZ,myinfobfb,photo_f";
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
$cook_RZ = $row['RZ'];$cook_RZarr = explode(',',$cook_RZ);
$cook_myinfobfb = intval($row['myinfobfb']);
$cook_photo_f   = intval($row['photo_f']);
//检查拉黑
if (gzflag($cook_uid,$uid) == -1)json_exit(array('flag'=>0,'msg'=>'对方觉得你不太适合Ta'));
//
if($i=='u_contact'){
	//聊天/查看联系方式
	$chatContact_data = explode(',',$_VIP['chatContact_data']);
	if(count($chatContact_data)>0 && is_array($chatContact_data)){
		foreach ($chatContact_data as $V){
			switch ($V) {
				case 'rz_mob':if(!in_array('mob',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('mob','title').'】<br>＾_＾'));break;
				case 'rz_identity':if(!in_array('identity',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％'));break;
				case 'rz_photo':if(!in_array('photo',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％'));break;
				case 'bfb':$config_bfb = intval($_VIP['chatContact_bfb_num']);if($cook_myinfobfb < $config_bfb)json_exit(array('flag'=>'nodata','msg'=>'请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％'));break;
				case 'sex':$row0 = $db->NUM($uid,"sex");if($row0[0]==$cook_sex)json_exit(array('flag'=>0,'msg'=>'同性不能查看＾_＾'));break;
				case 'photo':if($cook_photo_f!=1)json_exit(array('flag'=>'nophoto','msg'=>'请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％'));break;
			}
		}
	}
	nocontact($cook_uid);
	nolevel($uid,$cook_uid,'contact',$chk_u_jumpurl);
	noucount_clickloveb($uid,$cook_uid,'contact');
}elseif($i=='u_cert'){
	//nocert($cook_uid,$cook_RZ);
}else{
	//nodata($cook_uid);
}
if ($i == 'u_data'){
	require_once ZEAI.'cache/udata.php';$extifshow = json_decode($_UDATA['extifshow'],true);
	$fields = "id,nickname,sex,grade,birthday,areatitle,love,heigh,weigh,edu,pay,house,car,nation,area2title,child,blood,tag,marrytype,marrytime,job";
	if (@count($extifshow) >0 && is_array($extifshow)){foreach ($extifshow as $ev){$evARR[] = $ev['f'];}$fields .= ",".implode(",",$evARR);}
	$row = $db->NAME($uid,$fields);
	if ($row){
		$row_ext    = $row;
		$nickname   = dataIO($row['nickname'],'out');
		$sex        = $row['sex'];
		$grade      = $row['grade'];
		$birthday   = $row['birthday'];
		$birthday   = (!ifdate($birthday))?'':$birthday;
		$areatitle  = dataIO($row['areatitle'],'out');
		$area2title = dataIO($row['area2title'],'out');
		$heigh      = $row['heigh'];
		$weigh      = $row['weigh'];
		$love       = $row['love'];
		$edu        = $row['edu'];
		$pay        = $row['pay'];
		$house      = $row['house'];
		$car        = $row['car'];
		$nation     = $row['nation'];
		$marrytype  = $row['marrytype'];
		$marrytime  = $row['marrytime'];
		$job        = $row['job'];
		$child      = $row['child'];
		$blood      = $row['blood'];
		$tag        = $row['tag'];
	}else{json_exit(array('flag'=>0,'msg'=>'会员不存在或已被锁定'));}?>
	<style>
    .u_data{background-color:#fff;padding:0}
    .u_data .udata{padding:0 0 20px 0}
    .u_data .udata ul{background-color:#fff;border-bottom:#f0f0f0 12px solid}
    .u_data .udata ul li:last-child{border:0}
	.linebox{margin:20px auto 50px auto}
    </style>
	<?php	
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$i.'">&#xe602;</i>详细资料';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
    <div class="submain u_data">
        <div class="udata">
            <ul>
                <li><h4>UID</h4><span><?php echo $uid; ?></span></li>
                <?php if (!empty($sex)){?><li><h4>性别</h4><span><?php echo udata('sex',$sex);?></span></li><?php }?>
                <?php if (!empty($nickname)){?><li><h4>昵称</h4><span><?php echo $nickname; ?></span></li><?php }?>
                <li><h4>会员等级</h4><span><?php echo uicon($sex.$grade,2); ?><?php echo utitle($grade); ?></span></li>
                <?php if (!empty($birthday)){?><li><h4>生日</h4><span><?php echo $birthday;?></span></li><?php }?>
                <?php if (!empty($areatitle)){?><li><h4>所在地区</h4><span><?php echo $areatitle;?></span></li><?php }?>
                <?php if (!empty($love)){?><li><h4>婚姻状况</h4><span><?php echo udata('love',$love);?></span></li><?php }?>
                <?php if (!empty($heigh)){?><li><h4>身高</h4><span><?php echo udata('heigh',$heigh);?></span></li><?php }?>
                <?php if (!empty($edu)){?><li><h4>学历</h4><span><?php echo udata('edu',$edu);?></span></li><?php }?>
                <?php if (!empty($pay)){?><li><h4>月收入</h4><span><?php echo udata('pay',$pay);?></span></li><?php }?>
                <?php if (!empty($job)){?><li><h4>职业</h4><span><?php echo udata('job',$job);?></span></li><?php }?>
                <?php if (!empty($marrytime)){?><li><h4>期望结婚时间</h4><span><?php echo udata('marrytime',$marrytime);?></span></li><?php }?>
            </ul>
            <?php if (!empty($house) || !empty($car) || !empty($weigh) || !empty($marrytype) || !empty($child) || !empty($blood) || !empty($tag) || !empty($area2title) || !empty($nation)  ){?>
            <ul>
                <?php if (!empty($house)){?><li><h4>住房情况</h4><span><?php echo udata('house',$house);?></span></li><?php }?>
                <?php if (!empty($car)){?><li><h4>买车情况</h4><span><?php echo udata('car',$car);?></span></li><?php }?>
                <?php if (!empty($weigh)){?><li><h4>体重</h4><span><?php echo udata('weigh',$weigh);?></span></li><?php }?>
                <?php if (!empty($marrytype)){?><li><h4>嫁娶形式</h4><span><?php echo udata('marrytype',$marrytype);?></span></li><?php }?>
                <?php if (!empty($child)){?><li><h4>子女情况</h4><span><?php echo udata('child',$child);?></span></li><?php }?>
                <?php if (!empty($blood)){?><li><h4>血型</h4><span><?php echo udata('blood',$blood);?></span></li><?php }?>
                <?php if (!empty($tag)){?><li><h4>我的标签</h4><span><?php echo checkbox_div_list_get_listTitle('tag'.$sex,$tag);?></span></li><?php }?>
                <?php if (!empty($area2title)){?><li><h4>户籍地区</h4><span><?php echo $area2title;?></span></li><?php }?>
                <?php if (!empty($nation)){?><li><h4>民族</h4><span><?php echo udata('nation',$nation);?></span></li><?php }?>
            </ul>
            <?php }
            $showul=false;
			if (@count($extifshow) > 0 || is_array($extifshow)){?>
            <ul id="extifshow">
            <?php
            foreach ($extifshow as $V) {
                $data = dataIO($row_ext[$V['f']],'out');
                switch ($V['s']) {
                    case 1:$Fkind = 'ipt';$span=$data;break;
                    case 2:$Fkind = 'slect';$span=udata($V['f'],$data);break;
                    case 3:$Fkind = 'chckbox';$span=checkbox_div_list_get_listTitle($V['f'],$data);break;
                }
				if (!empty($span)){
					$showul=true;
                ?>
                <li><h4><?php echo $V['t'];?></h4><span><?php echo $span;?></span></li>
            <?php }}}?></ul>
			<?php 
            if (!$showul){?>
            <script>extifshow.hide();</script>
            <?php }?>
        </div>
        <div class="linebox"><div class="line W50"></div><div class="title S12 C999 BAI">我是有底线的</div></div>
    </div>
<?php }elseif($i == 'u_aboutus'){
	$row = $db->NAME($uid,"aboutus");
	if ($row){
		$aboutus = dataIO($row['aboutus'],'out');
	}else{json_exit(array('flag'=>0,'msg'=>'会员不存在或已被锁定'));}?>
	<style>.u_aboutus{background-color:#fff;padding:30px;line-height:200%;font-size:18px}.linebox{margin:20px auto 50px auto}</style>
	<?php	
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$i.'">&#xe602;</i>自我介绍';
    $mini_class = 'top_mini top_miniBAI';$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>?>
    <div class="submain u_aboutus"><?php echo $aboutus;?><div class="linebox"><div class="line"></div><div class="title BAI S12 C999">我是有底线的</div></div></div>
<?php }elseif($i == 'u_contact'){
	$row = $db->NAME($uid,"mob,weixin,weixin_pic,qq,email,mob_ifshow,qq_ifshow,weixin_pic_ifshow,weixin_ifshow,email_ifshow");//,mob_ifshow
	if ($row){
		$weixin     = dataIO($row['weixin'],'out');
		$weixin_pic = dataIO($row['weixin_pic'],'out');
		$qq         = dataIO($row['qq'],'out');
		$email      = dataIO($row['email'],'out');
		$mob        = dataIO($row['mob'],'out');

		//
		$mob_ifshow        = $row['mob_ifshow'];
		$qq_ifshow         = $row['qq_ifshow'];
		$weixin_pic_ifshow = $row['weixin_pic_ifshow'];
		$weixin_ifshow     = $row['weixin_ifshow'];
		$email_ifshow	   = $row['email_ifshow'];	
		$weixin_str     =($weixin_ifshow==1)?$weixin:' 已设置保密';
		$weixin_pic_str =($weixin_pic_ifshow==1)?$weixin_pic:' 已设置保密';
		$mob_str        =($mob_ifshow==1)?$mob:' 已设置保密';
		$qq_str         =($qq_ifshow==1)?$qq:' 已设置保密';
		$email_str      =($email_ifshow==1)?$email:' 已设置保密';
		//
		$weixin_str =(!empty($weixin_str))?$weixin_str:' -未填-';
		$weixin_pic_str =(!empty($weixin_pic_str))?$weixin_pic_str:' -未上传-';
		$qq_str     =(!empty($qq_str))?$qq_str:' -未填-';
		$email_str  =(!empty($email_str))?$email_str:' -未填-';
		$mob_str    =(!empty($mob_str))?$mob_str:' -未填-';

	}else{json_exit(array('flag'=>0,'msg'=>'会员不存在或已被锁定'));}?>
	<style>
    </style>
	<?php	
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$i.'">&#xe602;</i>联系方法';
    $mini_class = 'top_mini top_miniBAI';$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
    <div class="submain u_contact">
        <div class="udata">
            <ul>
                <li><h4><i class='ico weixin'>&#xe607;</i>微信</h4><span><?php echo $weixin_str;?></span></li>
                <li><h4><i class='ico weixin'>&#xe611;</i>微信二维码</h4><span>
                <div class="wxpic" id="wxpic">
                <?php if (!empty($weixin_pic) && $weixin_pic_ifshow==1){?>
                	点击放大→　
                    <img src="<?php echo $_ZEAI['up2'].'/'.$weixin_pic;?>" onClick="ZeaiM.piczoom('<?php echo $_ZEAI['up2'].'/'.$weixin_pic;?>');">
                 <?php }else{echo $weixin_pic_str;}?>
                </div>                
                </span></li>
                <li><h4><i class='ico qq'>&#xe612;</i>QQ</h4><span><?php echo $qq_str; ?></span></li>
                <li><h4><i class='ico email'>&#xe641;</i>邮箱</h4><span><?php echo $email_str;?></span></li>
                <li><h4><i class='ico mob'>&#xe627;</i> 手机</h4><span><a href="tel:<?php echo $mob_str;?>" class="C666"><font class="ico S18 C666" style="display:inline-block">&#xe60e;</font><?php echo $mob_str;?></a></span></li>
            </ul>
		</div>    
    </div>
<?php }elseif($i == 'u_mate'){
	require_once ZEAI.'cache/udata.php';
	$row = $db->NAME($uid,"mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areatitle,mate_love,mate_house");
	if ($row){
		$mate_age1      = intval($row['mate_age1']);
		$mate_age2      = intval($row['mate_age2']);
		$mate_heigh1    = intval($row['mate_heigh1']);
		$mate_heigh2    = intval($row['mate_heigh2']);
		$mate_pay       = intval($row['mate_pay']);
		$mate_edu       = intval($row['mate_edu']);
		$mate_areatitle = dataIO($row['mate_areatitle'],'out');
		$mate_love      = intval($row['mate_love']);
		$mate_house     = intval($row['mate_house']);
		$mate_areaid    = explode(',',$mate_areaid);
		$mate_age       = $mate_age1.','.$mate_age2;
		$mate_age_str   = mateset_out($mate_age1,$mate_age2,' 岁');
		$mate_heigh     = $mate_heigh1.','.$mate_heigh2;
		$mate_heigh_str = mateset_out($mate_heigh1,$mate_heigh2,' 厘米');
		$mate_areatitle_str = (!empty($mate_areatitle))?$mate_areatitle:'不限';
		$mate_pay_str = (!empty($mate_pay))?udata('pay',$mate_pay):'不限';
		$mate_edu_str = (!empty($mate_edu))?udata('edu',$mate_edu):'不限';
		$mate_love_str = (!empty($mate_love))?udata('love',$mate_love):'不限';
		$mate_house_str = (!empty($mate_house))?udata('house',$mate_house):'不限';
	}else{json_exit(array('flag'=>0,'msg'=>'会员不存在或已被锁定'));}?>
	<style>
    .u_mate{background-color:#fff;padding:0}
    .u_mate .udata{padding:0 0 20px 0}
	.linebox{margin:20px auto 50px auto}
    </style>
	<?php	
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$i.'">&#xe602;</i>择友要求';
    $mini_class = 'top_mini top_miniBAI';$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
    <div class="submain u_mate">
        <div class="udata">
            <ul>
                <li><h4>年龄</h4><span><?php echo $mate_age_str; ?></span></li>
                <li><h4>身高</h4><span><?php echo $mate_heigh_str; ?></span></li>
                <li><h4>最低月收入</h4><span><?php echo $mate_pay_str;?></span></li>
                <li><h4>最低学历</h4><span><?php echo $mate_edu_str;?></span></li>
                <li><h4>所在地区</h4><span><?php echo $mate_areatitle_str; ?></span></li>
                <li><h4>婚姻状况</h4><span><?php echo $mate_love_str;?></span></li>
                <li><h4>住房情况</h4><span><?php echo $mate_house_str;?></span></li>
            </ul>
		</div>    
    </div>
<?php }elseif($i == 'u_cert'){
	$row = $db->NAME($uid,"RZ");/*,mob,identitynum,qq,email*/
	if ($row){
		$RZ    = $row['RZ'];$RZarr=explode(',',$RZ);
//		$qq    = dataIO($row['qq'],'out');
//		$identitynum = dataIO($row['identitynum'],'out');
//		$email = dataIO($row['email'],'out');
//		$mob   = dataIO($row['mob'],'out');
	}else{json_exit(array('flag'=>0,'msg'=>'会员不存在或已被锁定'));}?>
	<style>
	.u_cert{background-color:#fff;padding:0;font-size:12px;color:#999}
	.u_cert .ucert ul{width:100%}
	.u_cert .ucert li{height:130px}
	.u_cert .ucert:after{content:'';}
    </style>
	<?php	
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-u_cert">&#xe602;</i>诚信认证';
    $mini_class = 'top_mini top_miniBAI';$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';
	$rz_dataARR = explode(',',$_ZEAI['rz_data']);
	?>
    <div class="submain  u_cert">
    	<div class="ucert">
    	<ul>
			<?php
            if (count($rz_dataARR) >= 1 && is_array($rz_dataARR)){
                foreach ($rz_dataARR as $k=>$V) {
                ?>
                <li id="<?php echo $V;?>" class="rz<?php echo (in_array($V,$RZarr))?' ed':'';?>"><i class="ico <?php echo $V;?>"><?php echo rz_data_info($V,'ico');?></i><h5><?php echo rz_data_info($V,'title');?></h5></li>
            <?php }}?>
        </ul>
        </div>
    </div>
<?php }?>
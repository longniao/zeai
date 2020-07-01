<?php
//if (ini_get('session.auto_start') == 0)session_start();
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
if (!ifint($cook_admid)){header("Location: adm_login.php");exit;}
if (!is_mobile())exit('请用手机浏览器打开');
$QXARR = explode(',',$cook_admauthoritylist);
function m_noauth($t='权限不足') {
	global $_ZEAI;
	$ret  ="<!doctype html><html><head><meta http-equiv='refresh' content='3'><meta charset='utf-8'><title>".$t."</title>".HEADMETA."<link href='".$_ZEAI['adm2']."/css/main.css' rel='stylesheet' type='text/css' /></head><body>";
	$ret .= "<div class='nodataico'><i></i>".$t."</div>";
	$ret .= "</body></html>";
	return $ret;
}
if(!in_array('u_add',$QXARR) && !in_array('crm_u_add',$QXARR)){
	setcookie("cook_admauthoritylist","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_admid","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_admuname","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_admtruename","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_admpwd","",null,"/",$_ZEAI['CookDomain']);
	setcookie("cook_agentid","",time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_agenttitle","",time()+720000,"/",$_ZEAI['CookDomain']);
	exit(m_noauth());	
}
require_once ZEAI.'cache/udata.php';$sex_ARR = json_decode($_UDATA['sex'],true);$extifshow = json_decode($_UDATA['extifshow'],true);
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';

switch ($submitok) {
	case 'ajax_photo_pic_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_admid.'_adm_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$dbname = setpath_s($dbname);
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_photo_pic_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_up('tmp',$url,$cook_admid.'_adm_','SMB');
				}
				$dbname = setpath_s($dbname);
				$newpic = $_ZEAI['up2']."/".$dbname;
				if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
		}
	break;
	case 'ajax_weixin_pic_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_admid.'_adm_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$dbname = setpath_s($dbname);
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_weixin_pic_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_up('tmp',$url,$cook_admid.'_adm_','SB');
				}
				$dbname = setpath_s($dbname);
				$newpic = $_ZEAI['up2']."/".$dbname;
				if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
		}
	break;
	case 'addupdate':
	
	
		

		if ($mob == $cook_adm_u_add_mob && ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请不要重复提交'));
		$photo_s = dataIO($photo_s,'in',100);
		$nickname   = trimhtml(dataIO($nickname8,'in',50));
		$truename   = trimhtml(dataIO($truename8,'in',12));
		$sex        = intval($sex);
		$areaid     = dataIO($areaid8,'in',20);
		$areatitle  = dataIO($areaid8title,'in',80);
		$area2id     = dataIO($area2id8,'in',20);
		$area2title  = dataIO($area2id8title,'in',80);
		$birthday   = (!ifdate($birthday8))?'0000-00-00':$birthday8;
		$aboutus    = trimhtml(dataIO($aboutus,'in',1000));
		$bz         = trimhtml(dataIO($bz,'in',1000));
		$love       = intval($love8);
		$heigh      = intval($heigh8);
		$weigh      = intval($weigh8);
		$house      = intval($house8);
		$marrytype  = intval($marrytype8);
		$edu        = intval($edu8);
		$pay        = intval($pay8);
		$house      = intval($house8);
		$car        = intval($car8);
		$child      = intval($child8);
		$job        = intval($job8);
		$blood      = intval($blood8);
		$nation     = intval($nation8);
		
		//
		$qq         = trimhtml(dataIO($qq8,'in',15));
		$weixin     = dataIO($weixin8,'in',50);
		$weixin_pic = dataIO($weixin_pic,'in',100);
		$mob        =(!ifmob($mob8))?0:$mob8;
		$identitynum= trimhtml(dataIO($identitynum8,'in',20));
		//
		if (!ifsfz($identitynum))json_exit(array('flag'=>0,'msg'=>'请输入正确的【身份证号】'));
		
		if(!ifint($sex))json_exit(array('flag'=>0,'msg'=>'请选择【性别】'));
		if($birthday=='0000-00-00')json_exit(array('flag'=>0,'msg'=>"请选择【生日】".$birthday));
		if (str_len($nickname) > 16 || str_len($nickname)<2 )json_exit(array('flag'=>0,'msg'=>'请输入2-16长度昵称'));
		if (empty($truename))json_exit(array('flag'=>0,'msg'=>'请输入【真实姓名】'));
		if(ifmob($nickname))json_exit(array('flag'=>0,'msg'=>'昵称不能是手机号码'));
		if(empty($areaid))json_exit(array('flag'=>0,'msg'=>'请选择【所在地区】'));
		if(!ifint($love))json_exit(array('flag'=>0,'msg'=>'请选择【婚姻状况】'));
		if(!ifint($heigh))json_exit(array('flag'=>0,'msg'=>'请选择【身高】'));
		if(!ifint($edu))json_exit(array('flag'=>0,'msg'=>'请选择【学历】'));
		if(!ifint($pay))json_exit(array('flag'=>0,'msg'=>'请选择【月收入】'));
		if(!ifint($job))json_exit(array('flag'=>0,'msg'=>'请选择【职业】'));
		if (ifmob($mob)){
			$row = $db->ROW(__TBL_USER__,'nickname',"mob='$mob'","num");/* AND mob<>'' AND FIND_IN_SET('mob',RZ)*/
			if($row)json_exit(array('flag'=>0,'msg'=>"“手机”已被【".dataIO($row[0],'out')."】占用"));
		}else{
			json_exit(array('flag'=>0,'msg'=>"请填写【手机号码】"));
		}
		//if(empty($weixin8))json_exit(array('flag'=>0,'msg'=>'请填写【微信号】'));
		//if(!ifint($mate_age1) || !ifint($mate_age2) || !ifint($mate_heigh1) || !ifint($mate_heigh2) || !ifint($mate_pay) || !ifint($mate_edu) || empty($mate_areaid) || !ifint($mate_love)  )json_exit(array('flag'=>0,'msg'=>'择偶要求必填','obj'=>'mate'));
		//
		$mate_age1      = intval($mate_age1);
		$mate_age2      = intval($mate_age2);
		$mate_heigh1    = intval($mate_heigh1);
		$mate_heigh2    = intval($mate_heigh2);
		$mate_pay       = intval($mate_pay8);
		$mate_edu       = intval($mate_edu8);
		$mate_areaid    = dataIO($mate_areaid8,'in',50);
		$mate_areatitle = dataIO($mate_areaid8title,'in',100);
		$mate_love      = intval($mate_love8);
		$mate_house     = intval($mate_house8);
		//
		$flag = 0;
		$kind = 2;
		$uname = 'm_'.cdstr(5);
		$pwd='_www@Zeai@cn@v6.0';
		$regtime=ADDTIME;
		$endtime=ADDTIME;
		$refresh_time=ADDTIME;
		$regkind=10;
		$dataflag=1;
		$admid = $cook_admid;
		$regip=getip();
		if(!empty($photo_s)){
			adm_u_pic_reTmpDir_send($photo_s,'photo');
			adm_u_pic_reTmpDir_send(smb($photo_s,'m'),'photo');
			adm_u_pic_reTmpDir_send(smb($photo_s,'b'),'photo');
			adm_u_pic_reTmpDir_send(smb($photo_s,'blur'),'photo');
			$photo_s = str_replace('tmp','photo',$photo_s);
		}
		if(!empty($weixin_pic)){
			adm_u_pic_reTmpDir_send($weixin_pic,'weixin');
			adm_u_pic_reTmpDir_send(smb($weixin_pic,'b'),'weixin');
			$weixin_pic = str_replace('tmp','weixin',$weixin_pic);
		}
		$agentid = intval($cook_agentid);
		$agenttitle = dataIO($cook_agenttitle,'in',100);
		
		$db->query("INSERT INTO ".__TBL_USER__." (flag,kind,uname,pwd,nickname,regtime,endtime,refresh_time,regkind,sex,birthday,dataflag,admid,aboutus,areaid,areatitle,area2id,area2title,love,heigh,weigh,edu,pay,car,job,child,truename,identitynum,marrytype,bz,mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areaid,mate_areatitle,mate_love,mate_house,house,regip,photo_s,weixin_pic,qq,mob,weixin,blood,nation,agentid,agenttitle) VALUES ($flag,'$kind','$uname','$pwd','$nickname','$regtime','$endtime','$refresh_time','$regkind','$sex','$birthday','$dataflag','$admid','$aboutus','$areaid','$areatitle','$area2id','$area2title','$love','$heigh','$weigh','$edu','$pay','$car','$job','$child','$truename','$identitynum','$marrytype','$bz','$mate_age1','$mate_age2','$mate_heigh1','$mate_heigh2','$mate_pay','$mate_edu','$mate_areaid','$mate_areatitle','$mate_love','$mate_house','$house','$regip','$photo_s','$weixin_pic','$qq','$mob','$weixin','$blood','$nation','$agentid','$agenttitle')");
		$uid = $db->insert_id();
		//
		if (@count($extifshow) > 0 || is_array($extifshow)){
			$sql = array();
			foreach ($extifshow as $V) {
				$fieldname = $V['f'];
				$value     = $fieldname.'8';
				switch ($V['s']) {
					case 1://text
						$sql[] = "$fieldname='".dataIO($$value,'in')."'";
					break;
					case 2://select
						$sql[] = "$fieldname=".intval($$value)."";
					break;
					case 3://checkbox
						//$fieldvalue = (is_array($$value))?implode(',',$$value):'';
						//$sql[] = "$fieldname = '".$fieldvalue."'";
						$sql[] = "$fieldname='".dataIO($$value,'in')."'";
					break;
				}
			}
			$setsql = (is_array($sql))?implode(',',$sql):'';
			$db->query("UPDATE ".__TBL_USER__." SET ".$setsql." WHERE id=".$uid);
		}
		set_data_ed_bfb($uid);
		setcookie("cook_adm_u_add_mob",$mob,time()+7200000,"/",$_ZEAI['CookDomain']);
		//gyl_debug($setsql);
		json_exit(array('flag'=>1,'msg'=>'录入成功！'));
	break;
}
$headertitle = '会员录入-';require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="../res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage']
	});
	</script>
<?php }?>
<link href="../res/zeai_ios_select/separate/iosSelect.css" rel="stylesheet" type="text/css" />
<script src="../res/zeai_ios_select/separate/zepto.js?212ss"></script>
<script src="../res/iscroll.js"></script>
<script src="../res/zeai_ios_select/separate/iosSelect.js"></script>
<script src="../res/zeai_ios_select/separate/select_mini.js"></script>
<script src="../cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="../cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/birthday.js"></script>
<script>
Sbindbox='';
var upMaxMB = <?php echo $_UP['upMaxMB']; ?>,
browser='<?php echo (is_weixin())?'wx':'h5';?>',
up2='<?php echo $_ZEAI['up2'];?>/';
</script>
<style>
body{position:absolute;top:0;background-color:#fff;-webkit-overflow-scrolling: touch}
::-webkit-input-placeholder{font-size:14px}
.ios-select-widget-box.olay {background-color:rgba(0,0,0,.4)}
.ios-select-widget-box.olay > div {width:100%;top:auto;bottom:0;left:0;border-radius:0;padding-bottom:58px}

.wxpicli .icoadd,.wxpicli img{width:40px;height:40px;display:block;margin:5px -30px 0}
.wxpicli .icoadd{line-height:40px;border:#dedede 1px solid;font-size:30px;text-align:center;color:#aaa;display:inline-block;margin:4px -30px 0}
.modlist ul li.wxpicli:after{content:''}
.wxpicli .wxpic{position:absolute;right:40px;top:0}
.admbottom{padding:20px 0;text-align:center;background-color:#f0f0f0}
.sexbox{float:right;margin-right:-20px}
.lineSsquare .title{color:#FD66B5}
</style>
<div class="modlist fadeInL">
	 <form id="WWW__ZEAI___CN_form">
    <!--基本资料-->
    <div class="lineSsquare"><div class="line BAI"></div><div class="title BAI S14">基本<br>资料</div></div>

    <ul>
        <li class="wxpicli"><h4>性　　别 <b class="Cf00">*</b></h4><span></span>
            <div class="sexbox">
<?php foreach ($sex_ARR as $v) {?>
    <input type="radio" name="sex" value="<?php echo $v['i'];?>" id="sex_<?php echo $v['i'];?>" class="radioskin"><label for="sex_<?php echo $v['i'];?>" class="radioskin-label"><i class="i2"></i><b class="W30"><?php echo $v['v'];?></b></label>　
<?php }?>
            </div>
        </li>
        <li class="wxpicli"><h4>照　　片 <b class="Cf00">*</b></h4><span></span>
            <div class="wxpic" id="photo_sbox">
                <p class="icoadd"><i class="ico">&#xe620;</i></p>
            </div>
        </li>
        <li id="nickname" class="<?php echo (empty($nickname))?'ipt':'none';?>" data="<?php echo $nickname;?>"><h4>昵　　称 <b class="Cf00">*</b></h4><span><?php echo $nickname;?></span></li>
        <li id="birthday" class="<?php echo (empty($birthday) || $birthday=='0000-00-00')?'bthdy':'none';?>" data="<?php echo ($birthday=='0000-00-00')?'1992-01-15':$birthday;?>"><h4>生　　日 <b class="Cf00">*</b></h4><span><?php echo $birthday;?></span></li>
        <li id="areaid" class="aread" data="<?php echo $areaid;?>"><h4>所在地区 <b class="Cf00">*</b></h4><span><?php echo $areatitle;?></span></li>
        <li id="love" class="<?php echo (empty($love))?'slect':'none';?>" data="<?php echo $love;?>"><h4>婚姻状况 <b class="Cf00">*</b></h4><span><?php echo udata('love',$love);?></span></li>
        <li id="heigh" class="slect" data="165"><h4>身　　高 <b class="Cf00">*</b></h4><span><?php echo str_replace("cm","",udata('heigh',$heigh));;?></span></li>
        <li id="edu" class="slect" data="3"><h4>学　　历 <b class="Cf00">*</b></h4><span><?php echo udata('edu',$edu);?></span></li>
        <li id="pay" class="slect" data="4"><h4>月 收 入 <b class="Cf00">*</b></h4><span><?php echo udata('pay',$pay);?></span></li>
        <li id="job" class="slect" data="<?php echo $job;?>"><h4>职　　业 <b class="Cf00">*</b></h4><span><?php echo udata('job',$job);?></span></li>
    </ul>
    
    
    
    <!--择友要求-->
    <div class="lineSsquare"><div class="line"></div><div class="title BAI S14">择偶<br>要求</div></div>
    <ul>
        <li id="mate_age" class="rang" data="23,30"><h4>年龄区间 <b class="Cf00">*</b></h4><span><?php echo $mate_age_str;?></span></li>
        <li id="mate_heigh" class="rang" data="160,175"><h4>身高区间 <b class="Cf00">*</b></h4><span><?php echo $mate_heigh_str;?></span></li>
        <li id="mate_pay" class="slect" data="3"><h4>最低月收入 <b class="Cf00">*</b></h4><span><?php echo $mate_pay_str;?></span></li>
        <li id="mate_edu" class="slect" data="3"><h4>最低学历 <b class="Cf00">*</b></h4><span><?php echo $mate_edu_str;?></span></li>
        <li id="mate_areaid" class="aread" data="<?php echo $mate_areaid;?>"><h4>地区要求 <b class="Cf00">*</b></h4><span><?php echo $mate_areatitle_str;?></span></li>
        <li id="mate_love" class="slect" data="<?php echo $mate_love;?>"><h4>婚姻要求 <b class="Cf00">*</b></h4><span><?php echo $mate_love_str;?></span></li>
        <li id="mate_house" class="slect" data="<?php echo $mate_house;?>"><h4>住房要求</h4><span><?php echo $mate_house_str;?></span></li>
    </ul>
    
    <!--联系方式-->
    <br><div class="lineSsquare M"><div class="line"></div><div class="title BAI">联系<br>方式</div></div>
    <ul>
        <li id="truename" class="ipt" data="<?php echo $truename;?>"><h4>真实姓名 <b class="Cf00">*</b></h4><span style="width:70px"><?php echo $truename;?></span></li>
        <li id="mob" class="<?php echo (strstr($data_RZ,'mob') && ifmob($mob))?'none':'ipt';?>" data="<?php echo $mob;?>"><h4>手机号码 <b class="Cf00">*</b></h4><span><?php echo $mob;?></span></li>
        <li id="weixin" class="ipt" data="<?php echo $weixin;?>"><h4>微信号</h4><span><?php echo $weixin;?></span></li>
        <li class="wxpicli"><h4>微信二维码</h4><span></span>
            <div class="wxpic" id="weixin_picbox">
                <p class="icoadd"><i class="ico">&#xe620;</i></p>
            </div>
        </li>
        <li id="qq" class="<?php echo (strstr($data_RZ,'qq'))?'none':'ipt';?>" data="<?php echo $qq;?>"><h4>QQ</h4><span><?php echo $qq;?></span></li>
        <li id="identitynum" class="ipt" data="<?php echo $identitynum;?>"><h4>身份证号 <b class="Cf00">*</b></h4><span style="width:70px"><?php echo $identitynum;?></span></li>
    </ul>
    
    <!--详细资料-->
    <br><div class="lineSsquare M"><div class="line"></div><div class="title BAI">详细<br>资料</div></div><ul>
    <ul class="textarea2">
        <li><h4>自我介绍</h4></li>
        <textarea class="textarea" name="aboutus" id="aboutus" placeholder="自我介绍（10~500字）"><?php echo $aboutus;?></textarea>
    </ul>
    <ul>
        <li id="area2id" class="aread" data="<?php echo $area2id;?>"><h4>户籍地区 </h4><span><?php echo $area2title;?></span></li>
        <li id="weigh" class="slect" data="<?php echo $weigh;?>"><h4>体重</h4><span><?php echo udata('weigh',$weigh);?></span></li>
        <li id="house" class="slect" data="<?php echo $house;?>"><h4>住房情况</h4><span><?php echo udata('house',$house);?></span></li>
        <li id="car" class="slect" data="<?php echo $car;?>"><h4>买车情况</h4><span><?php echo udata('car',$car);?></span></li>
        <li id="marrytype" class="slect" data="<?php echo $marrytype;?>"><h4>嫁娶形式</h4><span><?php echo udata('marrytype',$marrytype);?></span></li>
        <li id="child" class="slect" data="<?php echo $child;?>"><h4>子女情况</h4><span><?php echo udata('child',$child);?></span></li>
        <li id="blood" class="slect" data="<?php echo $blood;?>"><h4>血　　型</h4><span><?php echo udata('blood',$blood);?></span></li>
        <li id="nation" class="slect" data="<?php echo $nation;?>"><h4>民　　族</h4><span><?php echo udata('nation',$nation);?></span></li>
    </ul>
	<?php
	if (@count($extifshow) > 0 || is_array($extifshow)){
		echo ' <ul>';
    	foreach ($extifshow as $V) {
			$objstr = $V['f'];
			$data   = dataIO($row_ext[$objstr],'out');
			switch ($V['s']) {
				case 1:$Fkind = 'ipt';$span=$data;break;
				case 2:$Fkind = 'slect';$span=udata($V['f'],$data);break;
				case 3:$Fkind = 'chckbox';$span=checkbox_div_list_get_listTitle($V['f'],$data);break;
			}
			?>
			<li id="<?php echo $objstr;?>" class="<?php echo $Fkind;?>" data="<?php echo $data; ?>"><h4><?php echo $V['t'];?></h4><span><?php echo $span;?></span></li>
			<?php
		}
		echo '</ul>';
		?>
	<?php }?>

    
    <ul class="textarea2">
        <li><h4>备注</h4></li>
        <textarea class="textarea" name="bz" id="bz" placeholder="备注信息（10~500字）"></textarea>
    </ul>
    <input type="hidden" name="nickname8" id="nickname8" value="" />
    <input type="hidden" name="birthday8" id="birthday8" value="" />
    <input type="hidden" name="areaid8" id="areaid8" value="" />
    <input type="hidden" name="areaid8title" id="areaid8title" value="" />
    <input type="hidden" name="area2id8" id="area2id8" value="" />
    <input type="hidden" name="area2id8title" id="area2id8title" value="" />
    <input type="hidden" name="love8" id="love8" value="" />
    <input type="hidden" name="heigh8" id="heigh8" value="" />
    <input type="hidden" name="weigh8" id="weigh8" value="" />
    <input type="hidden" name="edu8" id="edu8" value="" />
    <input type="hidden" name="pay8" id="pay8" value="" />
    <input type="hidden" name="job8" id="job8" value="" />
    <input type="hidden" name="house8" id="house8" value="" />
    <input type="hidden" name="car8" id="car8" value="" />
    <input type="hidden" name="child8" id="child8" value="" />
    <input type="hidden" name="blood8" id="blood8" value="" />
    <input type="hidden" name="nation8" id="nation8" value="" />
    <input type="hidden" name="marrytype8" id="marrytype8" value="" />
    <input type="hidden" name="mate_age1" id="mate_age1" value="" />
    <input type="hidden" name="mate_age2" id="mate_age2" value="" />
    <input type="hidden" name="mate_heigh1" id="mate_heigh1" value="" />
    <input type="hidden" name="mate_heigh2" id="mate_heigh2" value="" />
    <input type="hidden" name="mate_pay8" id="mate_pay8" value="" />
    <input type="hidden" name="mate_edu8" id="mate_edu8" value="" />
    <input type="hidden" name="mate_areaid8" id="mate_areaid8" value="" />
    <input type="hidden" name="mate_areaid8title" id="mate_areaid8title" value="" />
    <input type="hidden" name="mate_love8" id="mate_love8" value="" />
    <input type="hidden" name="mate_house8" id="mate_house8" value="" />
    <input type="hidden" name="mob8" id="mob8" value="" />
    <input type="hidden" name="weixin8" id="weixin8" value="" />
    <input type="hidden" name="qq8" id="qq8" value="" />
    <input type="hidden" name="truename8" id="truename8" value="" />
    <input type="hidden" name="identitynum8" id="identitynum8" value="" />
    <input type="hidden" name="photo_s" id="photo_s" value="" />
    <input type="hidden" name="weixin_pic" id="weixin_pic" value="" />
    <input type="hidden" name="submitok" value="addupdate">
	<?php
	if (@count($extifshow) > 0 || is_array($extifshow)){
    	foreach ($extifshow as $V) {
			$objstr = $V['f'];
			$data   = dataIO($row_ext[$objstr],'out');
			?>
			<input type="hidden" name="<?php echo $objstr;?>8" id="<?php echo $objstr;?>8" value="" />
			<?php
		}
	}?>
    <button type="button" class="btn size4 HONG  B" id="regbtn" style="width:85%;display:block;margin:20px auto 20px auto">提交保存</button>
    </ul>
</div>
<div class="admbottom">
<?php echo (!empty($cook_agenttitle))?'【'.$cook_agenttitle.'】':'';?>
<?php echo $cook_admtruename;?>（<?php echo $cook_admuname;?>）您好，当前会员数：<span class="numdian"><?php 
if(ifint($cook_admid)){
echo $db->COUNT(__TBL_USER__,"admid=".$cook_admid);
}else{echo '0';}
?></span><br><br><a class="btn size3 BAI" href="adm_modpass.php">修改密码</a>　　　<a class="btn size3 BAI" href="adm_loginout.php">退出</a><br><br>
</div>
<script src="js/adm_u_add.js?1"></script>
</body></html>
<?php
//$nav='index';
/**************************************************
版权所有@2019 www.zeai.cn 
原创作者：QQ:797311 (supdes) Zeai.cn V6.0 微信号：supdes
**************************************************/
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if(strstr($jumpurl,'login.php'))$jumpurl='';
if (ifint($cook_uid)){header("Location: ".HOST."/p1/my.php");exit;}
if(is_mobile())header("Location: ".HOST."/m1/login.php");
if (ini_get('session.auto_start') == 0)session_start();
header("Cache-control: private");
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/udata.php';
if($submitok == 'ajax_click_yzm'){
	$yzmarr = array('绿'=>'#0f0','蓝'=>'#00f','红'=>'#f00','黑'=>'#000','黄'=>'#fc0','粉'=>'#F8C2DE','灰'=>'#ccc','青'=>'#1EFFFB','紫'=>'#81007F','棕'=>'#993400');
	$_SESSION['colorKey'] = array_rand($yzmarr,1);
	$_SESSION['colorV']   = $yzmarr[$_SESSION['colorKey']];
	$yzmarr_new=shuffle_arr($yzmarr);
	foreach ($yzmarr_new as $k=>$V) {$echo.= '<li>'.$k.'</li>';}
	json_exit(array('flag'=>1,'bg'=>$_SESSION['colorV'],'li'=>$echo));
}elseif($submitok=='ajax_click_yzm_chk'){
	if($v==$_SESSION['colorKey'] && $bg==$_SESSION['colorV']){
		json_exit(array('flag'=>1,'msg'=>'好样的，选择正确，正在发送手机验证码'));
	}else{
		json_exit(array('flag'=>0,'msg'=>'选择错误，请重新选择！'));
	}
}elseif($submitok=='ajax_reg_chk'){
	$ip=getip();
	$reg_loveb = abs(intval($_REG['reg_loveb']));
	$reg_grade = (ifint($_REG['reg_grade']) && $_REG['reg_grade']<=10)?intval($_REG['reg_grade']):1;
	$reg_if2   = 999;
	$reg_flag  =($_REG['reg_flag']==1)?1:0;
	
	if ($_REG['reg_flag']==3)json_exit(array('flag'=>0,'msg'=>'本站已关闭新会员注册'));
	if(!ifdate($birthday))json_exit(array('flag'=>0,'msg'=>'请选择生日'));
	if(!ifint($a1) && !ifint($a2) && !ifint($a3))json_exit(array('flag'=>0,'msg'=>'请选择地区'));
	if (str_len($nickname) > 16 || str_len($nickname)<2 )json_exit(array('flag'=>0,'msg'=>'请输入2-16长度昵称'));/*|| !preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u',$uname) */
	if(ifmob($nickname))json_exit(array('flag'=>0,'msg'=>'昵称不能是手机号码'));
	if (str_len($pwd) > 20 || str_len($pwd)<6 )json_exit(array('flag'=>0,'msg'=>'请输入正确的密码(长度6~20)'));
	
	//手机+密码
	if($_REG['reg_kind'] == 1){
		if (!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请输入正确的手机号码'));
		$row = $db->ROW(__TBL_USER__,'id',"mob='".$mob."' OR (mob='$mob' AND FIND_IN_SET('mob',RZ)) ");
		if($row)json_exit(array('flag'=>0,'msg'=>'此手机号码已被注册,请重新输入'));
		$uname=$mob;$RZ='mob';
	//用户名+密码
	}elseif($_REG['reg_kind'] == 2){
		if (ifmob($uname) || ifint($uname))json_exit(array('flag'=>0,'msg'=>'用户名不能是手机号码和纯数字'));
		if (str_len($uname) > 20 || str_len($uname)<3 || ifint($uname) )json_exit(array('flag'=>0,'msg'=>'请输入3-15个字符用户名（推荐3~15位字母或加数字组合）'));
		$row = $db->ROW(__TBL_USER__,'id',"uname='".$uname."' OR (mob='$uname' AND FIND_IN_SET('mob',RZ)) ");
		if($row)json_exit(array('flag'=>0,'msg'=>'此用户名已被注册,请重新输入'));
		$mob='';$RZ='';
	//手机+用户名+密码
	}elseif($_REG['reg_kind'] == 3){
		if (!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请输入正确的手机号码'));
		$row = $db->ROW(__TBL_USER__,'id',"mob='".$mob."' OR (mob='$mob' AND FIND_IN_SET('mob',RZ)) ");
		if($row)json_exit(array('flag'=>0,'msg'=>'此手机号码已被注册,请重新输入'));
		//
		if (ifmob($uname) || ifint($uname))json_exit(array('flag'=>0,'msg'=>'用户名不能是手机号码和纯数字'));
		if (str_len($uname) > 20 || str_len($uname)<3 )json_exit(array('flag'=>0,'msg'=>'请输入3-15个字符用户名（推荐3~15位字母或加数字组合）'));
		$row = $db->ROW(__TBL_USER__,'id',"uname='".$uname."' OR (mob='$uname' AND FIND_IN_SET('mob',RZ)) ");
		if($row)json_exit(array('flag'=>0,'msg'=>'此用户名已被注册,请重新输入'));
		$RZ='mob';
	}
	//验证码处理
	if($_REG['reg_kind'] == 1 || $_REG['reg_kind'] == 3){
		$verify = intval($verify);
		if (empty($_SESSION['Zeai_cn__verify'])){
			json_exit(array('flag'=>0,'msg'=>'短信验证码错误，请重新获取'));
		}else{
			if ($_SESSION['Zeai_cn__verify'] != $verify){
				json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
			}
			if ($_SESSION['Zeai_cn__mob'] != $mob && ifmob($mob)){
				unset($_SESSION["Zeai_cn__verify"]);
				unset($_SESSION["Zeai_cn__mob"]);
				json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
			}
		}
	}
	$nickname  = dataIO($nickname,'in',30);
	$areaid    = $a1.','.$a2.','.$a3;
	$areatitle = dataIO($areatitle,'in',100);
	$heigh     = intval($height);
	$job       = intval($job);
	$edu       = intval($edu);
	$love      = intval($love);
	$pay       = intval($pay);
	$tmpid     = intval($tmpid);
	$regkind   = 1;
	$tguid     = intval($tguid);
	if ($sex==0 || $pay==0 || $pay==0 || $job==0 || $height==0)json_exit(array('flag'=>0,'msg'=>'注册数据遗漏^_^'));
	$uname = dataIO($uname,'in',15);
	$pwd   = dataIO($pwd,'in',20);
	$pwd   = md5(trim($pwd));
	/**************** 入库 ******************/
	$db->query("INSERT INTO ".__TBL_USER__." (flag,uname,mob,RZ,pwd,nickname,sex,grade,if2,areaid,areatitle,birthday,edu,heigh,job,love,pay,loveb,regtime,endtime,regip,endip,refresh_time,regkind,tguid) VALUES ($reg_flag,'".$uname."','$mob','$RZ','".$pwd."','".$nickname."',$sex,$reg_grade,$reg_if2,'".$areaid."','".$areatitle."','".$birthday."',".$edu.",".$heigh.",".$job.",".$love.",".$pay.",".$reg_loveb.",".ADDTIME.",".ADDTIME.",'$ip','$ip',".ADDTIME.",$regkind,$tguid)");
	$uid = intval($db->insert_id());
	addupdate($uid,$uname,$pwd,$sex,$reg_grade,$birthday,$nickname,$tguid,$tmpid);
}elseif($submitok=='ajax_reg_verify'){
	if ($_REG['reg_kind']==2)exit(JSON_ERROR);
	if(  $v!=$_SESSION['colorKey'] || $bg!=$_SESSION['colorV'] || empty($_SESSION['colorKey']) ||  empty($_SESSION['colorV'])  ){json_exit(array('flag'=>0,'msg'=>'验证码选择错误，请重新选择获取！'));}
	unset($_SESSION["colorKey"]);
	unset($_SESSION["colorV"]);
	if(!ifmob($mob)){
		json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
	}else{
		if ($db->ROW(__TBL_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)"))json_exit(array('flag'=>0,'msg'=>'此手机号码已被注册，请更换'));
	}
	if (($Temp_regyzmrenum > $_SMS['sms_yzmnum']) && $_SMS['sms_yzmnum']>0  )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
	$_SESSION['Zeai_cn__verify'] = cdstr(4);
	//sms
	$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__verify']);
	if ($rtn == 0){
		setcookie("Temp_regyzmrenum",$Temp_regyzmrenum+1,time()+720000,"/",$_ZEAI['CookDomain']);  
		$chkflag = 1;
		$content = '验证码发送成功，请注意查收';
	}else{
		$chkflag = 0;
		$content = "发送失败,错误码：$rtn";
	}
	//sms end
	$_SESSION['Zeai_cn__mob'] = $mob;
	json_exit(array('flag'=>$chkflag,'msg'=>$content));
}elseif($submitok=='ajax_reg_verify_chk'){
	if ($_SESSION['Zeai_cn__verify'] != $verify)json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
	json_exit(array('flag'=>1));
}
function addupdate($uid,$uname,$pwd,$sex,$reg_grade,$birthday,$nickname,$tguid,$tmpid){
	global $_ZEAI,$db,$reg_loveb;
	/**************** 第三方 ******************/
	if(ifint($tmpid)){
		$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid,"num");
		if ($row){
			$c        = json_decode($row[0],true);
			$regkind  = $c['regkind'];
			$tguid    = intval($tguid);
			/**** QQ ******/
			if($regkind=='qq'){
				$regkind  = 4;
				$loginkey = $c['openid'];
				if(empty($nickname))$nickname = dataIO($c['nickname'],'in');
				$dbname = (!empty($c['photo_s']))?wx_get_uinfo_logo($c['photo_s'],$uid):'';
				$photo_s=setpath_s($dbname);
				$db->query("UPDATE ".__TBL_USER__." SET regkind=$regkind,loginkey='$loginkey',nickname='$nickname',photo_s='$photo_s' WHERE id=".$uid);
			/**** weixin ****/
			}elseif($regkind=='weixin'){
				$regkind=2;
				//$openid   = $c['openid'];
				if(empty($nickname))$nickname = dataIO($c['nickname'],'in');
				$unionid  = $c['unionid'];
				$dbname = (!empty($c['headimgurl']))?wx_get_uinfo_logo($c['headimgurl'],$uid):'';
				$photo_s=setpath_s($dbname);
				$db->query("UPDATE ".__TBL_USER__." SET regkind=$regkind,unionid='$unionid',nickname='$nickname',photo_s='$photo_s' WHERE id=".$uid);/*openid='$openid',*/
			}
		}
		$db->query("DELETE FROM ".__TBL_TMP__." WHERE id=".$tmpid);
	}
	/**************** 清单通知 ******************/
	if ($reg_loveb > 0){
		//Love币清单
		$db->AddLovebRmbList($uid,'新用户注册',$reg_loveb,'loveb',6);		
		//站内消息
		$C = $uname.'您好，您有一笔'.$_ZEAI['loveB'].'到账！　　<a href='.Href('loveb').' class=aQING>查看详情</a>';
		$db->SendTip($uid,'您有一笔'.$_ZEAI['loveB'].'到账！',dataIO($C,'in'),'sys');
	}
	/**********************************/
	require_once ZEAI.'sub/TGfun.php';
	$tg=json_decode($_REG['tg'],true);
	if(ifint($tguid) && $tg['flag'] == 1){
		TG($tguid,$uid,'reg');exit;
	}
	setcookie("cook_uid",$uid,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_uname",$uname,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_pwd",$pwd,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_sex",$sex,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_photo_s",$photo_s,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_nickname",dataIO($nickname,'out'),time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_grade",$reg_grade,time()+720000,"/",$_ZEAI['CookDomain']);
	setcookie("cook_birthday",$birthday,time()+720000,"/",$_ZEAI['CookDomain']);
	set_data_ed_bfb($uid);
	json_exit(array('flag'=>1));
}
if(ifint($tmpid)){
	$row = $db->ROW(__TBL_TMP__,"c","id=".$tmpid);
	if ($row){
		$c = json_decode($row[0],true);
		$tmp_regkind  = $c['regkind'];
		$tmp_nickname= dataIO($c['nickname'],'out');
		$tmp_sex     = ($c['sex']==2)?2:1;
		$sex=(ifint($tmp_sex))?$tmp_sex:$sex;
		$nickname=(!empty($tmp_nickname))?$tmp_nickname:'';
		if($tmp_regkind=='qq'){
			$tmp_photo_s = $c['photo_s'];
			$tmp_openid  = $c['openid'];
		}elseif($tmp_regkind=='weixin'){
			$unionid  = $c['unionid'];
			$tmp_photo_s = $c['headimgurl'];
			$tmp_openid  = $unionid;
		}
		$photo_s_url = (!empty($tmp_photo_s) )?$tmp_photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$sexbg       = (empty($tmp_photo_s))?' class="sexbg'.$sex.'"':'';
		$province = trimm($c['province']);
		$city     = trimm($c['city']);
		if (!empty($province)){
			$rowa = $db->ROW(__TBL_AREA1__,"id","title LIKE '%".$province."%'");
			if ($rowa)$a1 = $rowa[0];
			if (ifint($a1)){
				$rowa = $db->ROW(__TBL_AREA2__,"id","title LIKE '%".$city."%'");
				if ($rowa){
					$a2 = $rowa[0];
					$areaid = $a1.','.$a2;
				}
			}
		}
		$areatitle= $province.' '.$city;
	}
}
$nav='index';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>新会员注册 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/login_reg.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
</head>
<body>
<?php if ($submitok == 'clause'){?>
	<style>body{background-color:#fff}</style>
	<div class="clauseC"><h1>会员条款/免责声明</h1><?php $row = $db->ROW(__TBL_NEWS__,"content","id=1");echo ($row)?dataIO($row[0],'out'):nodatatips('暂无内容');?></div>
<?php exit;}require_once ZEAI.'p1/top.php';?>
<div class="regmain">
    <div class="boxx reg S5">
    	<div class="tbody">1分钟注册，找到一辈子的幸福</div>
        <form id="WWW_ZEAI_CN_form">
        <div class="L">
            <?php
			if (!empty($tmp_photo_s)){
			?>
        	<div class="tmpbox">
        		<img src="<?php echo $photo_s_url;?>"<?php echo $sexbg;?>><span><?php echo $nickname;?>，你好！　请如实完善以下资料，诚信交友</span>
            </div>
        	<?php }?>
        	<dl><dt>我的性别</dt><dd>
                <input type="radio" name="sex" id="sex1" class="radioskin" value="1"<?php echo ($sex == 1 || empty($sex))?' checked':'';?>><label for="sex1" class="radioskin-label"><i></i><b class="W50 S14">男</b></label>
                <input type="radio" name="sex" id="sex2" class="radioskin" value="2"<?php echo ($sex == 2)?' checked':'';?>><label for="sex2" class="radioskin-label"><i></i><b class="W50 S14">女</b></label>
            </dd></dl>
        
            <dl><dt>我的生日</dt><dd>
                <ul class="birthday" id="birthday_">
                    <span></span>
                    <li>
                    	<div class="msk"></div>
                    	<div class="Ybox" id="birthday_Ybox"></div>
                        <div class="Mbox" id="birthday_Mbox"></div>
                        <div class="Dbox" id="birthday_Dbox"></div>
                    </li>
                    
                </ul>
            </dd></dl>
        	<dl><dt>我的地区</dt><dd>
                <ul class="area" id="reg_area_">
                    <span></span>
                    <li><div class="msk"></div><dl><dd></dd></dl></li>
                </ul>
            </dd></dl>
        	<dl><dt>选择身高</dt><dd>
                <ul class="height" id="reg_height_">
                    <span>请选择(厘米)</span>
                    <li>
                    	<div class="msk"></div>
                    	<div class="height_box" id="reg_height_box"></div>
                    </li>
                </ul>
            </dd></dl>
        	<dl><dt>我的学历</dt><dd>
            	<ul id="reg_edu" class="edu"><span></span><li></li></ul>
            </dd></dl>
        	<dl><dt>婚姻状况</dt><dd>
            	<ul id="reg_love" class="love"><span></span><li></li></ul>
            </dd></dl>
        	<dl><dt>我的职业</dt><dd>
            	<ul id="reg_job" class="job"><span></span><li></li></ul>
            </dd></dl>
        	<dl><dt>月 收 入</dt><dd>
            	<ul id="reg_pay" class="pay"><span></span><li></li></ul>
            </dd></dl>
            <dl><dt>我的昵称</dt><dd><input name="nickname" id="nickname" type="text" required class="input" maxlength="16" placeholder="请输入2-16个汉字或字符" autocomplete="off" value="<?php echo $nickname;?>" /></dd></dl>
            <div class="linebox" style="margin-left:20px"><div class="line W50"></div><div class="title BAI S14 C999">帐号登录信息</div></div>
            <?php if($_REG['reg_kind'] == 1 || $_REG['reg_kind'] == 3){?>
            <dl><dt>输入手机</dt><dd><input name="mob" id="mob" type="text" required class="input" maxlength="11" placeholder="请输入您的手机号" autocomplete="off"  /></dd></dl>
            <dl><dt>验 证 码</dt><dd class="yzmF"><input name="verify" id="verify" type="text" required class="input" maxlength="4" placeholder="输入手机短信验证码" autocomplete="off" /><a href="javascript:;" class="yzmbtn" id="yzmbtn">获取验证码</a>
                <div id="zeai_yzm">
                	<div class="j"></div>
                    <em id="zeai_yzm_em"></em>
                    <span>这是什么颜色？请点击下面文字确定</span>
                    <div class="text" id="zeai_yzm_li"></div>
                </div>
            </dd></dl>
            <?php }?>
            <?php if($_REG['reg_kind'] == 2 || $_REG['reg_kind'] == 3){?>
            <dl><dt>用 户 名</dt><dd><input name="uname" id="uname" type="text" required class="input" maxlength="16" placeholder="请输入3-15个字符用户名" autocomplete="off" /></dd></dl>
            <?php }?>
            <dl><dt>设置密码</dt><dd><input name="pwd" id="pwd" type="password" required class="input" maxlength="20" placeholder="请输入6-20个字符密码" autocomplete="off" /></dd></dl>
            <dl><dt></dt><dd>
              <div class="clause"><input type="checkbox" name="clause" id="clause" class="checkskin " value="1" checked><label for="clause" class="checkskin-label"><i></i><b class="W400">已阅读和同意<?php echo $_ZEAI['siteName'];?>的 <a href="javascript:readclause();" class="a09f">服务条款</a> 和 <a href="javascript:readclause();" class="a09f">隐私政策</a></b></label></div>
            </dd></dl>
            <dl><dt>&nbsp;</dt><dd><input type="button" class="regbtn btn size4 HONG" value="立即注册" id="regbtn" /></dd></dl>
            <div class="clear"></div>
            <div class="newuser">
            <div class="linebox "><div class="line W50"></div><div class="title BAI S14 C999">附近最新加入会员</div></div>
            <?php
			$rt=$db->query("SELECT id,nickname,sex,photo_s,photo_f,areatitle,birthday,love FROM ".__TBL_USER__." WHERE photo_f=1 AND flag=1 ORDER BY id DESC LIMIT 4");
			$total = $db->num_rows($rt);
			if ($total > 0) {
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows) break;
					$uid      = $rows['id'];
					$nickname = dataIO($rows['nickname'],'out');
					$sex      = $rows['sex'];
					$areatitle= $rows['areatitle'];
					$birthday_ = $rows['birthday'];
					$love_      = $rows['love'];
					$photo_s  = $rows['photo_s'];
					$photo_f  = $rows['photo_f'];
					$birthday_str = (getage($birthday_)<=0)?'':getage($birthday_).'岁 ';
					$love_str     = (empty($love_))?'':' '.udata('love',$love_).' ';
					$aARR = explode(' ',$areatitle);$areatitle = $aARR[1];
					$areatitle_str = (empty($areatitle))?'':$areatitle;
					$areatitle_str  = str_replace("不限","",$areatitle_str);
					$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
					$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
					echo '<li><img src="'.$photo_s_url.'"'.$sexbg.'><span>'.$birthday_str.$areatitle_str.$love_str.'</span></li>';
				}
			}
			?>
            </div>
        </div>
        <input type="hidden" name="birthday" id="birthday" value="<?php echo $birthday;?>">
        <input type="hidden" name="a1" id="reg_area_area1id" value="<?php echo $a1;?>">
        <input type="hidden" name="a2" id="reg_area_area2id" value="<?php echo $a2;?>">
        <input type="hidden" name="a3" id="reg_area_area3id" value="<?php echo $a3;?>">
        <input type="hidden" name="areatitle" id="areatitle" value="<?php echo $areatitle;?>">
        <input type="hidden" name="height" id="height" value="">
        <input type="hidden" name="edu" id="edu" value="">
        <input type="hidden" name="love" id="love" value="<?php echo $love;?>">
        <input type="hidden" name="job" id="job" value="">
        <input type="hidden" name="pay" id="pay" value="">
        <input type="hidden" id="jumpurl" name="jumpurl" value="<?php echo $jumpurl;?>">
        <input type="hidden" id="tmpid" name="tmpid" value="<?php echo $tmpid;?>">
        <input type="hidden" id="tguid" name="tguid" value="<?php echo $tguid;?>">
        <input type="hidden" name="submitok" value="ajax_reg_chk">
        </form>
        <div class="CC"><i></i><i></i><div class="clear"></div></div>
        <div class="R">
            <h1 class="C666 S18">已有帐号？ 请这边登录</h1>
			<a href="<?php echo HOST;?>/p1/login.php" class="reg">点此登录</a>
            <?php if ($_REG['reg_3login_qq'] == 1 || $_REG['reg_3login_wx'] == 1){?>
            <h1 class="C666 S18 Mtop50"><?php if ($_REG['reg_3login_wx'] == 1){?>微信扫码登录<?php }?><?php if ($_REG['reg_3login_qq'] == 1){?>或QQ帐号登录<?php }?></h1>
            <div class="em">
            <?php if ($_REG['reg_3login_wx'] == 1){?><a onClick="login3('weixin','reg')" class="weixin"><i class='ico'>&#xe607;</i></a><?php }?>
            <?php if ($_REG['reg_3login_qq'] == 1){?><a onClick="login3('qq','reg')" class="qq"><i class='ico'>&#xe612;</i></a></div><?php }?>
            <?php }?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<script src="../cache/areadata.js"></script>
<script src="../cache/udata.js"></script>
<script src="js/Zeai_birthday.js"></script>
<script src="js/login_reg.js"></script>

<script>
var reg_kind=<?php echo $_REG['reg_kind'];?>;
ZEAI_area('reg_area_',false);
ZEAI_birthday({ul:birthday_,bx:false,defdate:'<?php echo $birthday;?>',selstr:''});
ZEAI_height('reg_height_','height');
ZEAI_select('reg','edu',false);
ZEAI_select('reg','love',false);
ZEAI_select('reg','job',false);
ZEAI_select('reg','pay',false);
if(zeai.ifint(sessionStorage.tguid))o('tguid').value=sessionStorage.tguid;
if(zeai.ifint(sessionStorage.tmpid))o('tmpid').value=sessionStorage.tmpid;
setTimeout(function(){
	if(!zeai.empty(o('reg_area_area1id').value)&&!zeai.empty(o('reg_area_area2id').value)&&zeai.empty(o('reg_area_area3id').value)){
		o('reg_area_dt1id').removeClass('ed');
		o('reg_area_dt2id').removeClass('ed');
		o('reg_area_dt3id').class('ed');
		o('reg_area_a1box').hide();
		o('reg_area_a2box').hide();
		o('reg_area_a3box').show();
	}
},200);
sessionStorage.pagekind='';
</script>
<?php require_once ZEAI.'p1/bottom.php';?>
<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/config_tjdiy.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
function tjdiy_info($id,$k) {
	global $_TJDIY;
	$ARR = json_decode($_TJDIY['tjdiy'],true);
	if (count($ARR) >= 1 && is_array($ARR)){
		foreach ($ARR as $V) {
			if($V['id']==$id){
				switch ($k) {
					case 'title':return $V['title'];break;
					case 'par':return $V['par'];break;
				}			
			}
		}
	}
	return false;
}	

if($submitok == 'ajax_tjdiy'){
	$SQL=array();
	$areaid = '';
	if (ifint($a1) && ifint($a2) && ifint($a3) && ifint($a4)){
		$areaid = $a1.','.$a2.','.$a3.','.$a4;
	}elseif(ifint($a1) && ifint($a2) && ifint($a3)){
		$areaid = $a1.','.$a2.','.$a3;
	}elseif(ifint($a1) && ifint($a2)){
		$areaid = $a1.','.$a2;
	}elseif(ifint($a1)){
		$areaid = $a1;
	}
	$areaid2 = '';
	if (ifint($h1) && ifint($h2) && ifint($h3) && ifint($h4)){
		$areaid2 = $h1.','.$h2.','.$h3.','.$h4;
	}elseif(ifint($h1) && ifint($h2) && ifint($h3)){
		$areaid2 = $h1.','.$h2.','.$h3;
	}elseif(ifint($h1) && ifint($h2)){
		$areaid2 = $h1.','.$h2;
	}elseif(ifint($h1)){
		$areaid2 = $h1;
	}
	if (!empty($areaid))$SQL['areaid']   = $areaid;
	if (!empty($areaid2))$SQL['areaid2'] = $areaid2;
	if (ifint($sex))$SQL['sex']          = $sex;
	if (ifint($age1))$SQL['age1']        = $age1;
	if (ifint($age2))$SQL['age2']        = $age2;
	if (ifint($pay))$SQL['pay']         = $pay;
	if (ifint($edu))$SQL['edu']         = $edu;
	if (ifint($job))$SQL['job']         = $job;
	if (ifint($love))$SQL['love']       = $love;
	if (ifint($child))$SQL['child']     = $child;
	if (ifint($marrytype))$SQL['marrytype'] = $marrytype;
	if (ifint($marrytime))$SQL['marrytime'] = $marrytime;
	if (ifint($heigh1))$SQL['heigh1']  = $heigh1;
	if (ifint($heigh2))$SQL['heigh2']  = $heigh2;
	if (ifint($weigh1))$SQL['weigh1']  = $weigh1;
	if (ifint($weigh2))$SQL['weigh2']  = $weigh2;
	if (ifint($car))$SQL['car']        = $car;
	if (ifint($house))$SQL['house']    = $house;
	if (ifint($smoking))$SQL['smoking'] = $smoking;
	if (ifint($drink))$SQL['drink']     = $drink;
	if (ifint($companykind))$SQL['companykind'] = $companykind;
	if (ifint($ifmob))$SQL['ifmob']     = $ifmob;
	if($ifdata50 == 1)$SQL['ifdata50']  = $ifdata50;
	if($ifparent == 1)$SQL['ifparent']  = $ifparent;
	if ($photo_s == 1)$SQL['photo_s']   = $photo_s;
	if ($grade2 == 1)$SQL['grade2']     = $grade2;
	if (ifint($grade))$SQL['grade']     = $grade;
	json_exit(array('flag'=>1,'msg'=>'设置成功','par'=>http_build_query($SQL)));
exit;}elseif($submitok == 'ajax_pic_path_s_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('tmp',$file['tmp_name'],'');
		if (!up_send($file,$dbname,0,$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$newpic = $_ZEAI['up2']."/".$dbname;
		//if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		AddLog('【前台导航模块】上传图标->url:'.$dbname);
		json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
exit;}
switch ($t) {
	default:
		require_once ZEAI.'cache/config_adm.php';
		require_once ZEAI.'cache/config_wxgzh.php';
		require_once ZEAI.'cache/config_sms.php';
	break;
	case 2:
		require_once ZEAI.'cache/config_wxgzh.php';
		$_GZH['wx_gzh_name'] = dataIO($_GZH['wx_gzh_name'],'out');
		$_GZH['wx_gzh_welcome'] = dataIO($_GZH['wx_gzh_welcome'],'out');
		$_GZH['wx_gzh_hfcontent'] = dataIO($_GZH['wx_gzh_hfcontent'],'out');
		$_GZH['wx_gzh_mb_loveb'] = dataIO($_GZH['wx_gzh_mb_loveb'],'out');
		$_GZH['wx_gzh_mb_msgchat'] = dataIO($_GZH['wx_gzh_mb_msgchat'],'out');
		$_GZH['wx_gzh_mb_udata'] = dataIO($_GZH['wx_gzh_mb_udata'],'out');
		$_GZH['wx_gzh_mb_adminfo'] = dataIO($_GZH['wx_gzh_mb_adminfo'],'out');
		$_GZH['wx_gzh_mb_honor'] = dataIO($_GZH['wx_gzh_mb_honor'],'out');
		$_GZH['wx_gzh_mb_productpay'] = dataIO($_GZH['wx_gzh_mb_productpay'],'out');
	break;
	case 3:
		require_once ZEAI.'cache/config_login.php';
		$_LOGIN['wx_open_appid'] = dataIO($_LOGIN['wx_open_appid'],'out');
		$_LOGIN['wx_open_appsecret'] = dataIO($_LOGIN['wx_open_appsecret'],'out');
		$_LOGIN['qq_login_appid'] = dataIO($_LOGIN['qq_login_appid'],'out');
		$_LOGIN['qq_login_appkey'] = dataIO($_LOGIN['qq_login_appkey'],'out');
	break;
	case 4:
		require_once ZEAI.'cache/config_sms.php';
		$_SMS['sms_sid'] = dataIO($_SMS['sms_sid'],'out');
		$_SMS['sms_apikey'] = dataIO($_SMS['sms_apikey'],'out');
		$_SMS['sms_tplid_authcode'] = dataIO($_SMS['sms_tplid_authcode'],'out');
		$_SMS['sms_tplid_findpass'] = dataIO($_SMS['sms_tplid_findpass'],'out');
		$_SMS['sms_yzmnum'] = $_SMS['sms_yzmnum'];
	break;
	case 5:
		require_once ZEAI.'cache/config_pay.php';
		$_PAY['wxpay_mchid'] = dataIO($_PAY['wxpay_mchid'],'out');
		$_PAY['wxpay_key'] = dataIO($_PAY['wxpay_key'],'out');
		$_PAY['alipay_partner'] = dataIO($_PAY['alipay_partner'],'out');
		$_PAY['alipay_key'] = dataIO($_PAY['alipay_key'],'out');
		$_PAY['alipay_ID'] = $_PAY['alipay_ID'];
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tdLbgBAI{background-color:#fff}
@-moz-document url-prefix() {.savebtnbox{bottom:50px}}
</style>
</head>
<body>
<?php
if ($submitok == 'tjdiy'){
	require_once ZEAI.'cache/udata.php';
	$uroleStr = str_replace("g","i",$_ZEAI['urole']);
	$uroleStr = str_replace("t","v",$uroleStr);
	$par=tjdiy_info($id,'par');
	parse_str($par,$parARR);
	$pay=$parARR['pay'];
	$edu=$parARR['edu'];
	$job=$parARR['job'];
	$grade=$parARR['grade'];
	$regtime=$parARR['regtime'];
	$age1=$parARR['age1'];
	$age2=$parARR['age2'];
	$love=$parARR['love'];
	$child=$parARR['child'];
	$marrytime=$parARR['marrytime'];
	$sex=$parARR['sex'];
	$ifadmid=$parARR['ifadmid'];
	$heigh1=$parARR['heigh1'];
	$heigh2=$parARR['heigh2'];
	$car=$parARR['car'];
	$house=$parARR['house'];
	$marrytype=$parARR['marrytype'];
	$grade2=$parARR['grade2'];
	$photo_s=$parARR['photo_s'];
	$ifparent=$parARR['ifparent'];
	$weigh1=$parARR['weigh1'];
	$weigh2=$parARR['weigh2'];
	$smoking=$parARR['smoking'];
	$drink=$parARR['drink'];
	$companykind=$parARR['companykind'];
	$ifmob=$parARR['ifmob'];
	$ifdata50=$parARR['ifdata50'];
	$areaid=$parARR['areaid'];
	$areaid2=$parARR['areaid2'];
	$areaid=explode(',',$areaid);
	$areaid2=explode(',',$areaid2);
	if(count($areaid)>0){
		$a1=$areaid[0];
		$a2=$areaid[1];
		$a3=$areaid[2];
		$a4=$areaid[3];
	}
	if(count($areaid2)>0){
		$h1=$areaid2[0];
		$h2=$areaid2[1];
		$h3=$areaid2[2];
		$h4=$areaid2[3];
	}
	?>
	<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
    <script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
    <script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
	<style>
    .table0{min-width:1400px;width:98%;margin:10px 20px 20px 20px}
    .mtop{ margin-top:10px;}
    .SW,.SW_area,.SW_age{padding:0 3px}
    .SW{width:100px}
    .SW_area{width:120px;vertical-align:middle}
    .SW_age{width:75px;padding:0}
    .RCW,.RCW2{display:inline-block}
    .RCW li{width:80px}
    .RCW2 li{width:120px}
    .formline{height:10px}
    select{color:#666}
    </style>
    <table class="table0">
    <tr>
    <td align="left" class="border0 S14" style="min-width:980px">
        <script>
            var nulltext = '--';
            function chkform(){
                if (age1.value > age2.value && (!zeai.empty(age1.value) && !zeai.empty(age2.value)) ){
                    zeai.msg('年龄请选择一个正确的区间（左小右大）',age1);	
                    return false;
                }
                if (heigh1.value > heigh2.value && (!zeai.empty(heigh1.value) && !zeai.empty(heigh2.value)) ){
                    zeai.msg('身高请选择一个正确的区间（左小右大）',heigh1);	
                    return false;
                }
            }
        </script>
        <form id="zeaiCN_FORM_gyl">
			月薪 <script>zeai_cn__CreateFormItem('select','pay','<?php echo $pay; ?>','class="size2 SW"',pay_ARR);</script>　
            学历 <script>zeai_cn__CreateFormItem('select','edu','<?php echo $edu; ?>','class="size2 SW"',edu_ARR);</script>　
            职业 <script>zeai_cn__CreateFormItem('select','job','<?php echo $job; ?>','class="size2 SW"',job_ARR);</script>　
            线上会员等级 <script>zeai_cn__CreateFormItem('select','grade','<?php echo $grade; ?>','class="size2  SW"',<?php echo $uroleStr;?>);</script>　
            时间排序 <script>zeai_cn__CreateFormItem('select','regtime','<?php echo $regtime; ?>','class="size2 SW"',[{i:"1",v:"最新注册"},{i:"2",v:"最后登录"}]);</script>
			<div class="formline"></div>
            
            年龄 <script>zeai_cn__CreateFormItem('select','age1','<?php echo $age1; ?>','class="size2 SW_age"',age_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','age2','<?php echo $age2; ?>','class="size2 SW_age"',age_ARR);</script>　
            婚姻 <script>zeai_cn__CreateFormItem('select','love','<?php echo $love; ?>','class="size2 SW"',love_ARR);</script>　
            子女 <script>zeai_cn__CreateFormItem('select','child','<?php echo $child; ?>','class="size2 SW"',child_ARR);</script>　
            结婚时间 <script>zeai_cn__CreateFormItem('select','marrytime','<?php echo $marrytime; ?>','class="size2 SW"',marrytime_ARR);</script>　
            性别 <script>zeai_cn__CreateFormItem('radio','sex','<?php echo $sex; ?>','class="size2 RCW"',sex_ARR);</script>
            <input type="checkbox" name="ifadmid" id="ifadmid" class="checkskin" value="1"<?php echo ($ifadmid == 1)?' checked':''; ?>><label for="ifadmid" class="checkskin-label"><i></i><b class="W50 S14">被认领</b></label>
            <div class="formline"></div>
            
            身高 <script>zeai_cn__CreateFormItem('select','heigh1','<?php echo $heigh1; ?>','class="size2 SW_age"',heigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','heigh2','<?php echo $heigh2; ?>','class="size2 SW_age"',heigh_ARR);</script>　
            车子 <script>zeai_cn__CreateFormItem('select','car','<?php echo $car; ?>','class="size2 SW"',car_ARR);</script>　
            房子 <script>zeai_cn__CreateFormItem('select','house','<?php echo $house; ?>','class="size2 SW"',house_ARR);</script>　
            嫁娶形式 <script>zeai_cn__CreateFormItem('select','marrytype','<?php echo $marrytype; ?>','class="size2 SW"',marrytype_ARR);</script>　
            <input type="checkbox" name="grade2" id="grade2" class="checkskin" value="1"<?php echo ($grade2 == 1)?' checked':''; ?>><label for="grade2" class="checkskin-label"><i></i><b class="W80 S14">线上VIP会员</b></label>　
            <input type="checkbox" name="photo_s" id="photo_s" class="checkskin" value="1"<?php echo ($photo_s == 1)?' checked':''; ?>><label for="photo_s" class="checkskin-label"><i></i><b class="W50 S14">有头像</b></label>　
            <input type="checkbox" name="ifparent" id="ifparent" class="checkskin" value="1"<?php echo ($ifparent == 1)?' checked':''; ?>><label for="ifparent" class="checkskin-label"><i></i><b class="W80 S14">父母帮征婚</b></label>　
            
            <div class="formline"></div>
            
            体重 <script>zeai_cn__CreateFormItem('select','weigh1','<?php echo $weigh1; ?>','class="size2 SW_age"',weigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','weigh2','<?php echo $weigh2; ?>','class="size2 SW_age"',weigh_ARR);</script>　
            吸烟 <script>zeai_cn__CreateFormItem('select','smoking','<?php echo $smoking; ?>','class="size2 SW"',smoking_ARR);</script>　
            饮酒 <script>zeai_cn__CreateFormItem('select','drink','<?php echo $drink; ?>','class="size2 SW"',drink_ARR);</script>　
            单位类型 <script>zeai_cn__CreateFormItem('select','companykind','<?php echo $companykind; ?>','class="size2 SW"',companykind_ARR);</script>　
            <input type="checkbox" name="ifmob" id="ifmob" class="checkskin" value="1"<?php echo ($ifmob == 1)?' checked':''; ?>><label for="ifmob" class="checkskin-label"><i></i><b class="W50 S14">有手机</b></label>　
            <input type="checkbox" name="ifdata50" id="ifdata50" class="checkskin" value="1"<?php echo ($ifdata50 == 1)?' checked':''; ?> ><label for="ifdata50" class="checkskin-label"><i></i><b class="W80 S14">资料>50%</b></label>
            
            <div class="formline"></div>
            
            工作地区 <script>LevelMenu4('a1|a2|a3|a4|--|<?php echo $a1; ?>|<?php echo $a2; ?>|<?php echo $a3; ?>|<?php echo $a4; ?>|areaid|areatitle','class="size2 SW"');</script>　
            户籍地区 <script>LevelMenu4('h1|h2|h3|h4|--|<?php echo $h1; ?>|<?php echo $h2; ?>|<?php echo $h3; ?>|<?php echo $a4; ?>|areaid2|areatitle2','class="size2 SW"');</script>
            
            <div class="formline"></div>
            
            <input name="id" type="hidden" value="<?php echo $id; ?>" />
            <input name="submitok" type="hidden" value="ajax_tjdiy" />
            <div style="margin:0 auto;text-align:center"><button type="reset" class="btn size3 BAI">重设清除</button>　<button type="button" class="btn size3" id="ok"><i class="ico">&#xe6b1;</i> 确定</button>　</div>
            
      </form>
    </td>
    </tr>
    </table>
	<script>
    ok.onclick=function(){
		chkform();
		zeai.ajax({url:'var'+zeai.extname,form:zeaiCN_FORM_gyl},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){
				window.parent.tjpar<?php echo $id; ?>.value=decodeURIComponent(rs.par);
				setTimeout(function(){window.parent.zeai.iframe(0);},1000);
			}
		});
	}
    </script>
<?php exit;}elseif($submitok == 'navdiy'){
	$i=nav_info($id,'i');$t=nav_info($id,'t');$img=nav_info($id,'img');$url=nav_info($id,'url');$var=nav_info($id,'var');$url2=nav_info($id,'url2');$f=nav_info($id,'f');
	?>
	<style>
	.table{margin:15px 0 0 15px}
    .table td{padding:8px;border:1px solid #eee}
	.table .tdL{width:100px;color:#666}
	.table .tdR img.add{width:60px;height:60px;object-fit:cover;-webkit-object-fit:cover;background-color:#ccc;vertical-align:middle}
	.table .tdR span{vertical-align:middle;font-size:14px;color:#999;margin-left:20px;display:inline-block}
    </style>
    <form id="ZEAIFORM">
        <table class="table W95_ Mtop20" >
        <tr>
          <td class="tdL">导航名称</td>
          <td class="tdR"><input name="title" id="title" type="text" class="input size2" maxlength="30" value="<?php echo $t;?>"  autocomplete="off" /><span>← 请控制在4个字以内</span></td>
        </tr>
        <tr>
          <td class="tdL">导航图标</td>
          <td class="tdR">
        <div class="picli60" id="picli_path">
          <li class="add" id="path_add"></li>
          <?php if(!empty($img)){
                echo '<li><img src="'.$_ZEAI['up2'].'/'.$img.'"><i></i></li>';
            }?>
        </div>
        <br><span>← 正方形.png/.jpg/.gif格式，150*150像数</span></td>
        </tr>
        <tr>
          <td class="tdL">手机端链接</td>
          <td class="tdR"><textarea name="url" rows="3" class="textarea W100_" id="url"><?php echo $url;?></textarea></td>
        </tr>
        <tr>
          <td class="tdL">电脑端链接</td>
          <td class="tdR"><textarea name="url2" rows="3" class="textarea W100_" id="url2"><?php echo $url2;?></textarea></td>
        </tr>
        <tr>
          <td class="tdL"> 导航变量</td>
          <td class="tdR"><input name="var" class="input size2" maxlength="30" id="var" value="<?php echo $var;?>"></td>
        </tr>
        </table>
        <br><br><div class="savebtnbox"><button type="button" id="submit_add" class="btn size3 HUANG3">保存并提交</button></div>
        <input name="id" type="hidden" value="<?php echo $id;?>" />
        <input name="path_s" id="path_s" type="hidden" value="" />
        <input name="uu" type="hidden" value="<?php echo $session_uid;?>">
        <input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
        <input name="submitok" type="hidden" value="cache_navdiy_mod">
	</form>
    <script>
	var up2='<?php echo $_ZEAI['up2'];?>/',upMaxMB=<?php echo $_UP['upMaxMB']; ?>;
	zeai.photoUp({
		btnobj:path_add,
		upMaxMB:upMaxMB,
		url:"var"+zeai.extname,
		submitok:"ajax_pic_path_s_up",
		end:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){
				picli_path.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');
				path_s.value=rs.dbname;
				path_add.hide();
				var i=zeai.tag(o(picli_path),'i')[0],img=zeai.tag(o(picli_path),'img')[0];
				i.onclick = function(){
					zeai.confirm('亲~~确认删除么？',function(){
						img.parentNode.remove();path_add.show();path_s.value='';
					});
				}
				img.onclick = function(){parent.piczoom(up2+rs.dbname.replace('_s.','_b.'));}
			}
		}
	});
	window.onload=function(){
		path_s_mod();
		function path_s_mod(){
			var i=zeai.tag(picli_path,'i')[0],img=zeai.tag(picli_path,'img')[0];
			if(zeai.empty(i))return;
			path_add.hide();
			var src=img.src.replace(up2,'');
			path_s.value=src;
			i.onclick = function(){
				zeai.confirm('亲~~确认删除么？',function(){
					img.parentNode.remove();path_add.show();path_s.value='';
				});
			}
			img.onclick = function(){parent.piczoom(up2+src.replace('_s.','_b.'));}
		}
	}
	submit_add.onclick=function(){
		zeai.confirm('<b class="S18">确定提交么？</b><br>亲~~这三项都不为空才会显示导航哦',function(){
			zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){
					setTimeout(function(){
						if(zeai.empty(rs.out_img)){
							var src='images/navadd.png';
						}else{
							var src=up2+rs.out_img;
						}
						parent.o('img'+<?php echo $id;?>).src=src;
						parent.o('span'+<?php echo $id;?>).html(rs.out_t);
						parent.zeai.iframe(0);
					},1000);
				}
			});
		});
	}
    </script>
<?php exit;}?>
<div class="navbox">
	<?php if ($t == 2){?><a href="<?php echo SELF;?>?t=2" <?php echo ($t == 2)?' class="ed"':'';?>>微信公众号设置</a><?php }?>
    <?php if ($t == 1 || empty($t)){?><a href="<?php echo SELF;?>?t=1" <?php echo (empty($t) || $t == 1)?' class="ed"':'';?>>基本设置</a><?php }?>
    <?php if ($t == 6){?><a href="<?php echo SELF;?>?t=6" <?php echo ($t == 6)?' class="ed"':'';?>>首页广告</a><?php }?>
    <?php if ($t == 5){?><a href="<?php echo SELF;?>?t=5" <?php echo ($t == 5)?' class="ed"':'';?>>支付配置</a><?php }?>
    <?php if ($t == 3){?><a href="<?php echo SELF;?>?t=3" <?php echo ($t == 3)?' class="ed"':'';?>>帐号互联</a><?php }?>
    <?php if ($t == 4){?><a href="<?php echo SELF;?>?t=4" <?php echo ($t == 4)?' class="ed"':'';?>>短信/邮箱</a><?php }?>
    <?php if ($t == 'nav'){?><a href="<?php echo SELF;?>?t=nav" class="ed">导航/模块设置</a><?php }?>
    <?php if ($t == 'rz'){?><a class="ed">公安库/运营商/实名认证设置</a><?php }?>
</div>
<div class="fixedblank"></div>
<form id="ZEAIFORM" name="ZEAIFORM" method="post" enctype="multipart/form-data">
	<!--网站设置-->
	<?php if ($t == 1 || empty($t)) {
		
		if(!in_array('var_jb',$QXARR))exit(noauth());
		
		?>
        <table class="table size1 cols2" style="width:1111px;margin:0 0 0 20px">
		<tr>
			<th colspan="2" align="left" style="border:0">基础设置</td>
			<th colspan="2" align="left" style="border:0">上传相关设置</td>
		</tr>
		<tr>
			<td class="tdL">网站名称简称</td>
			<td class="tdR"><input id="siteName" name="siteName" type="text" class="W400" maxlength="100" value="<?php echo $_ZEAI['siteName'];?>"></td>
			<td class="tdL">上传单个图片最大</td>
			<td class="tdR">
            	<input id="upMaxMB" name="upMaxMB" type="text" class="W30" maxlength="2" value="<?php echo $_UP['upMaxMB'];?>"> MB<span class="tips">正整数，如1,2,10</span>
            	　　　　<font class="C999">视频最大</font>　<input id="upVMaxMB" name="upVMaxMB" type="text" class="W30" maxlength="2" value="<?php echo $_UP['upVMaxMB'];?>"> MB
            </td>
		</tr>
		<tr>
			<td class="tdL">首页Title标题SEO</td>
			<td class="tdR"><input id="indexTitle" name="indexTitle" type="text" class="W400" maxlength="100" value="<?php echo dataIO($_INDEX['indexTitle'],'out');?>"></td>
			<td class="tdL">缩略小图尺寸范围</td>
			<td class="tdR"><input id="upSsize" name="upSsize" type="text" class="W75" maxlength="10" value="<?php echo $_UP['upSsize'];?>"> 像数<span class="tips">推荐100*100(宽*高)，小列表显示图</span></td>
		</tr>
		<tr>
			<td class="tdL">首页关键词SEO</td>
			<td class="tdR"><input id="indexKeywords" name="indexKeywords" type="text" class="W400" maxlength="100" placeholder="Keywords请控制在5个以内，以英文半角逗号隔开" value="<?php echo dataIO($_INDEX['indexKeywords'],'out');?>"></td>
			<td class="tdL">缩略中图尺寸范围</td>
			<td class="tdR"><input id="upMsize" name="upMsize" type="text" class="W75" maxlength="10" value="<?php echo $_UP['upMsize'];?>"> 像数<span class="tips">推荐200*250(宽*高)，用户列表显示图</span></td>	</tr>
		<tr>
			<td class="tdL">首页简介SEO</td>
			<td class="tdR"><textarea name="indexContent" cols="30" rows="2" class="W400" id="indexContent" placeholder="要用于首页SEO搜索引擎优化，Description网站简介，最精短介绍，100字以内"><?php echo dataIO($_INDEX['indexContent'],'out');?></textarea></td>
			<td class="tdL">缩略大图尺寸范围</td>
			<td class="tdR"><input id="upBsize" name="upBsize" type="text" class="W75" maxlength="10" value="<?php echo $_UP['upBsize'];?>"> 像数<span class="tips">推荐720*720(宽*高)，放大显示图</span></td>	</tr>
		<tr>
			<td class="tdL">手机、微信端Logo</td>
			<td class="tdR">
				<?php if (!empty($_ZEAI['logo'])) {?>
                    <input name='logo_' type='hidden' value="<?php echo $_ZEAI['logo'];?>" />
                    <a class="pic60" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>')"><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo'].'?'.ADDTIME; ?>?"></a>　
                    <a href="#" class="btn size1" id="logodel">删除</a>　
                <?php }else{echo "<input name='logo' type='file' size='50' class='Caaa W300' />";}?>
                <br><span class='tips2'>先删除后更换，必须.png格式，高度小于114像数，宽度小于200像数</span>
			</td>
			<td class="tdL">大图水印图片</td>
			<td class="tdR">
			<?php if (!empty($_UP['waterimg'])) {?>
            <input name='waterimg_' type='hidden' value="<?php echo $_UP['waterimg'];?>" />
			<a class="pic60" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$_UP['waterimg']; ?>')"><img src="<?php echo $_ZEAI['up2'].'/'.$_UP['waterimg'].'?'.ADDTIME; ?>"></a>　
			<a href="#" class="btn size1" id="waterimgdel">删除</a>　
			<?php }else{echo "<input name='waterimg' type='file' size='50' class='Caaa W300' />";}?>
            <br><span class='tips2'>先删除后更换，必须.png透明格式，160*70像数，</span>
		</td>
		</tr>
		<tr>
			<td class="tdL">虚拟币名称</td>
			<td class="tdR"><input id="loveB" name="loveB" type="text" class="W100" maxlength="20" value="<?php echo $_ZEAI['loveB'];?>"><span class="tips">如：爱豆，Love币</span></td>
			<td class="tdL">大图是否打水印</td>
			<td class="tdR"><input type="checkbox" name="ifwaterimg" id="ifwaterimg" class="switch" value="1"<?php echo ($_UP['ifwaterimg'] == 1)?' checked':'';?>><label for="ifwaterimg" class="switch-label"><i></i><b>开启</b><b>关闭</b></label></td>
		</tr>
		<tr>
			<td class="tdL">前端最多显示记录数</td>
			<td class="tdR"><input id="limit" name="limit" type="text" class="W50" maxlength="8" value="<?php echo $_ZEAI['limit'];?>"> 条<span class="tips">前台列表最多显示记录条数，推荐500，越少打开速度越快</span></td>
			<td class="tdL">图片域名/目录</td>
			<td class="tdR"><input id="up2" name="up2" type="text" class="W200" maxlength="100" value="<?php echo (empty($_ZEAI['up2']))?HOST.'/up':$_ZEAI['up2'];?>" readonly> <span class="tips">请勿更改</span></td>	
		</tr>
        
		<tr>
			<td class="tdL">电脑端Logo</td>
			<td class="tdR">
            
				<?php if (!empty($_ZEAI['pclogo'])) {?>
                    <input name='pclogo_' type='hidden' value="<?php echo $_ZEAI['pclogo'];?>" />
                    <a class="pic60" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$_ZEAI['pclogo']; ?>')"><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['pclogo'].'?'.ADDTIME; ?>?"></a>　
                    <a href="#" class="btn size1" id="pclogodel">删除</a>
                <?php }else{echo "<input name='pclogo' type='file' size='50' class='Caaa W300' />";}?>					
            <br><span class='tips2'>先删除后更换，白色.png透明格式，最佳尺寸：66*66像数正方形</span>
            
            </td>
			<td class="tdL">手机H5端二维码图片</td>
		  <td class="tdR">
          
				<?php if (!empty($_ZEAI['m_ewm'])) {?>
                    <input name='m_ewm_' type='hidden' value="<?php echo $_ZEAI['m_ewm'];?>" />
                    <a class="pic60" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$_ZEAI['m_ewm']; ?>')"><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['m_ewm'].'?'.ADDTIME; ?>?"></a>　
                    <a href="#" class="btn size1" id="m_ewmdel">删除</a>
                <?php }else{echo "<input name='m_ewm' type='file' size='50' class='Caaa W300' />";}?>
                <br><span class='tips2'>先删除后更换，png/gif/jpg格式 ，300*300像数以内，百度搜“二维码生成”，填入二维码H5网址：<font class="blue"><?php echo HOST;?></font></span>
          
          
          </td>	
		</tr>
		<tr>
			<td class="tdL">强制更新缓存</td>
			<td class="tdR">
            <input id="cache_str" name="cache_str" type="text" class="W100" maxlength="8" value="<?php echo $_ZEAI['cache_str'];?>">
            <span class="tips2">缓存后缀变量，更换其它英文字母或纯数字即可<br>
            用途：如果发现更改的设置没有生效，比如：修改了地区或用户资料字段等</span>
            
            </td>
			<td class="tdL">关注公众号<br>自动获取会员微信头像</td>
			<td class="tdR"><input type="checkbox" name="wx_gzh_getphoto_s" id="wx_gzh_getphoto_s" class="switch" value="1"<?php echo ($_GZH['wx_gzh_getphoto_s'] == 1)?' checked':'';?>><label for="wx_gzh_getphoto_s" class="switch-label"><i></i><b>开启</b><b>关闭</b></label></td>	
		  </tr>
		<tr>
		<tr>
			<td class="tdL">电脑端底部信息</td>
		  <td colspan="3" class="tdR"><textarea name="pc_bottom" class="textarea W100_ center"><?php echo dataIO($_ZEAI['pc_bottom'],'out');?></textarea></td>
		  </tr>
        <tr><th colspan="4" align="left" style="border:0">&nbsp;</th></tr>
        
		<tr>
			<th colspan="2" align="left" style="border:0">数据库 <span class="tips">（请在官方指导下进行修改，否则会导致网站崩溃）</span></th>
			<th colspan="2" align="left" style="border:0">前端页面风格</th>
		</tr>
        <?php $db = json_decode($_ZEAI['db'],true);?>
		<tr>
			<td class="tdL">数据库地址</td>
			<td class="tdR"><input id="dbserver" name="dbserver" type="text" class="W200" maxlength="100" value="<?php echo $db['s'];?>"></td>
			<td class="tdL">手机端首页风格</td>
			<td class="tdR">
                <input type="radio" name="mob_mbkind" id="mob_mbkind1" class="radioskin" value="1"<?php echo ($_ZEAI['mob_mbkind'] == 1)?' checked':'';?>><label for="mob_mbkind1" class="radioskin-label"><i class="i1"></i><b class="W80 S12">多模块展示</b></label>　
                <input type="radio" name="mob_mbkind" id="mob_mbkind2" class="radioskin" value="2"<?php echo ($_ZEAI['mob_mbkind'] == 2)?' checked':'';?>><label for="mob_mbkind2" class="radioskin-label"><i class="i1"></i><b class="W80 S12">简洁瀑布流</b></label>　
                <input type="radio" name="mob_mbkind" id="mob_mbkind3" class="radioskin" value="3"<?php echo ($_ZEAI['mob_mbkind'] == 3)?' checked':'';?>><label for="mob_mbkind3" class="radioskin-label"><i class="i1"></i><b class="W80 S12">新版瀑布流</b></label>
                
                
            </td>
		</tr>
		<tr>
			<td class="tdL">数据库名称</td>
			<td class="tdR"><input style="color:#F9F9F9" id="dbname" name="dbname" type="text" class="W200" maxlength="50" value="<?php echo $db['n'];?>"></td>
			<td class="tdL">电脑端首页风格</td>
			<td class="tdR">
            
            <input type="radio" name="pc_mbkind" id="pc_mbkind1" class="radioskin" value="1"<?php echo ($_ZEAI['pc_mbkind'] == 1)?' checked':'';?>><label for="pc_mbkind1" class="radioskin-label"><i class="i1"></i><b class="W100 S12">官方默认</b></label>
            
            <input type="radio" name="pc_mbkind" id="pc_mbkind2" class="radioskin" value="2" disabled><label for="pc_mbkind2" class="radioskin-label"><i class="i1"></i><b class="W100 S12">时尚H5风格</b></label>            
            </td>
		</tr>
		<tr>
			<td class="tdL">数据库用户名</td>
			<td class="tdR"><input style="color:#F9F9F9" id="dbuser" name="dbuser" type="text" class="W200" maxlength="50" value="<?php echo $db['u'];?>"></td>
			<td class="tdL">&nbsp;</td>
			<td class="tdR"></td>
		</tr>
		<tr>
			<td class="tdL">数据库密码</td>
			<td class="tdR"><input style="color:#F9F9F9" id="dbpass" name="dbpass" type="text" class="W200" maxlength="50" value="<?php echo $db['p'];?>"></td>
			<td class="tdL">&nbsp;</td>
			<td class="tdR">&nbsp;</td>
		</tr>
        
         <tr><th colspan="4" align="left" style="border:0">&nbsp;</th></tr>
		<tr>
		<th colspan="4" align="left" style="border:0">总后台</td>
		</tr>
		<tr>
			<td class="tdL">后台左上角名称</td>
			<td class="tdR"><input id="admSiteName" name="admSiteName" type="text" class="W200" maxlength="20" value="<?php echo dataIO($_ADM['admSiteName'],'out');?>"></td>
			<td class="tdL">后台默认分页</td>
			<td class="tdR"><input id="admPageSize" name="admPageSize" type="text" class="W50" maxlength="5" value="<?php echo $_ADM['admPageSize'];?>"> 条<span class="tips">列表一页是显示多少条记录，推荐15，越少打开速度越快</span></td></tr>
		<tr>
			<td class="tdL">后台域名/目录</td>
			<td class="tdR"><input id="adm2" name="adm2" type="text" class="W200" maxlength="100" value="<?php echo $_ZEAI['adm2'];?>"></td>
			<td class="tdL">后台最多显示记录数</td>
			<td class="tdR"><input id="admLimit" name="admLimit" type="text" class="W50" maxlength="8" value="<?php echo $_ADM['admLimit'];?>"> 条<span class="tips">列表最多显示记录条数，推荐500，越少打开速度越快</span></td>
		</tr>
		<tr>
			<td height="50" colspan="4" align="center">
			<input name="submitok" type="hidden" value="cache_config">
			<!--<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>-->
			</td>
		</tr>
		</table>
        
        
<br><br><br><br><br>
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
        
        
        
        
	<?php }elseif($t == 2){
		
		if(!in_array('var_gzh',$QXARR))exit(noauth());
		
	?>
        <table class="table size1 cols2" style="width:1111px;margin:0 0 0 20px">
		<tr>
			<th colspan="4" align="left" style="border:0">公众号(服务号)基本信息　<font class="tips FR"><a href="https://mp.weixin.qq.com" target="_blank" class="aQING">申请/管理 微信公众号</a></font></th>
		</tr>
		<tr>
			<td class="tdL">公众号名称</td>
			<td class="tdR"><input id="wx_gzh_name" name="wx_gzh_name" type="text" class="W300" maxlength="100" value="<?php echo $_GZH['wx_gzh_name'];?>"></td>
			<td align="left" class="tdL">公众号appid</td>
			<td align="left"class="tdR"><input id="wx_gzh_appid" name="wx_gzh_appid" type="text" class="W300" maxlength="100" value="<?php echo $_ZEAI['wx_gzh_appid'];?>"></td>
		</tr>
		<tr>
			<td class="tdL">公众号token</td>
			<td class="tdR"><input id="wx_gzh_token" name="wx_gzh_token" type="text" class="W300" maxlength="100" value="<?php echo $_GZH['wx_gzh_token'];?>"></td>
			<td align="left" class="tdL">公众号appsecret</td>
			<td align="left"class="tdR"><input id="wx_gzh_appsecret" name="wx_gzh_appsecret" type="text" class="W300" maxlength="100" value="<?php echo $_ZEAI['wx_gzh_appsecret'];?>"></td>
		</tr>
		<tr>
			<td class="tdL">公众号二维码</td>
			<td class="tdR">
            
			<?php if (!empty($_GZH['wx_gzh_ewm'])) {?>
            <input name='subscribe_' type='hidden' value="<?php echo $_GZH['wx_gzh_ewm'];?>" />
			<a class="pic60" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm']; ?>')"><img src="<?php echo $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm'].'?'.ADDTIME; ?>"></a>　
			<a href="#" class="btn size1" id="subscribedel">删除</a>　
			<?php }else{echo "<input name='subscribe' type='file' size='50' class='Caaa W300' /><br><span class='tips2'>必须gif/jpg/png格式</span>";}?>
            
            </td>
			<td align="left" class="tdL">公众号菜单</td>
			<td align="left"class="tdR"><a class="btn" onClick="zeai.iframe('公众号底部自定义菜单','var_gzhmenu.php',900,550)">公众号底部菜单设置</a></td>
	</tr>
		<tr><td height="20" colspan="4" align="left" style="border:0"></td></tr>
		<tr>
			<th colspan="2" align="left" style="border:0">关注公众号欢迎信息
			<div id="welcome" class="helpC S14">会员关注公众号后，公众号会自动推送此内容到窗口<br><b>请注意以下三点：</b><br>1. 系统会在最前面加上网友“昵称”<br>2. 内容请控制点在400字节以内<br>3. 内有超链接源码，请在官方客服指导下进行修改，以防程序出错。</div><img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:welcome,title:'关注公众号欢迎信息',w:400,h:280});">
			</th>
			<th colspan="2" align="left" style="border:0">公众号自动回复信息<div id="hfcontent" class="helpC S14">如果会员与公众号互动，输入消息，会自动回复此内容。<br><b>请注意以下二点：</b><br>1. 内容请控制点在400字节以内<br>2. 如果要加超链接源码，请在官方客服指导下进行修改，以防程序出错</div><img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:hfcontent,title:'公众号自动回复信息',w:400,h:240});"></th>
		</tr>
		<tr>
			<th colspan="2"><textarea name="wx_gzh_welcome" id="wx_gzh_welcome" cols="50" rows="6" class="textarea W90_" ><?php echo dataIO($_GZH['wx_gzh_welcome'],'wx');?></textarea></th>
			<th colspan="2"><textarea name="wx_gzh_hfcontent" id="wx_gzh_hfcontent" cols="50" rows="6" class="textarea W90_"><?php echo dataIO($_GZH['wx_gzh_hfcontent'],'wx');?></textarea></th>
		</tr>
		<tr><td height="20" colspan="4" align="left" style="border:0"></td></tr>
		<tr>
			<th colspan="4" align="left" style="border:0">微信模板消息通知设置 <span class="tips">(请在下面填写对应的<b>模板ID</b>)</span>
			  <div id="wx_gzh_mb" class="helpC S14">登录公众平台后，左侧菜单 > 添加功能插件 > 模板消息 > 申请 > <b>主营 (IT科技，互联网|电子商务)</b>　<b>副营行业(其他，其他)</b><br>申请理由：业务需要，有时需要发送一些通知类消息，望通过！！！<br>注意：行业必须要选对，否则无效，特别是副营行业(<font class="Cf00">其他，其他</font>)，第一次申请时选择的不生效（微信bug），需二次修改才生效</div>
				<img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:wx_gzh_mb,title:'公众号自动回复信息',w:600,h:250});"> <span class="S12">公众号模版消息 <img src="images/d2.gif"> 所在行业：主营 (IT科技，互联网|电子商务)　副营行业(其他，其他)</span>
			</th>
		</tr>
		<tr>
			<td rowspan="2" class="tdL C999">资金变动提醒</td>
			<td colspan="3" class="tdR C999">
				OPENTM207679800 <img src="images/d2.gif"> <input id="wx_gzh_mb_loveb" name="wx_gzh_mb_loveb" type="text" class="W400 Caaa" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_loveb'];?>">
				<span class="tips">此模版新客户添加已失效，新客户请留空，新客户埴写下面没失效的</span>
			</td>
		  </tr>
		<tr>
			<td colspan="3" class="tdR">
				OPENTM415437054 <img src="images/d2.gif"> <input id="wx_gzh_mb_loveb2" name="wx_gzh_mb_loveb2" type="text" class="W400" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_loveb2'];?>">
				<div id="gzh_mb_loveb2" class="helpC S14">请登录公众平台后在左侧菜单 > 模板消息 > 模板库 > 搜索“<font class="Cf00">账户资金变动提醒</font>”找到编号：OPENTM415437054，点击 详情 > 添加 > 后可获得该模版ID，其他模版消息同理。。。</div>
				
                <img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:gzh_mb_loveb2,title:'公众号自动回复信息',w:400,h:230});">
			</td>
		  </tr>
		<tr>
		  <td rowspan="3" class="tdL C999 ">到账提醒</td>
		  <td colspan="3" class="tdR C999">OPENTM400265867 <img src="images/d2.gif"> <input id="wx_gzh_mb_productpay" name="wx_gzh_mb_productpay" type="text" class="W400 Caaa" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_productpay'];?>"><span class="tips">此模版新客户添加已失效，新客户请留空，新客户埴写下面没失效的</span></td>
		  </tr>
		<tr>
		  <td colspan="3" class="tdR C999">OPENTM204602735 <img src="images/d2.gif"> <input id="wx_gzh_mb_productpay2" name="wx_gzh_mb_productpay2" type="text" class="W400 Caaa" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_productpay2'];?>"><span class="tips">此模版新客户添加已失效，新客户请留空，新客户埴写下面没失效的</span></td>
		  </tr>
		<tr>
		  <td colspan="3" class="tdR">OPENTM417991932 <img src="images/d2.gif"> <input id="wx_gzh_mb_productpay3" name="wx_gzh_mb_productpay3" type="text" class="W400" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_productpay3'];?>"><div id="OPENTM417991932" class="helpC S14">请登录公众平台后在左侧菜单 > 模板消息 > 模板库 > 搜索“<font class="Cf00">充值到账提醒</font>”找到编号：OPENTM417991932，点击 详情 > 添加 > 后可获得该模版ID，其他模版消息同理。。。</div>
				<img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:OPENTM417991932,title:'公众号自动回复信息',w:400,h:230});"></td>
		  </tr>
		<tr>
		  <td class="tdL">后台操作提醒</td>
		  <td colspan="3" class="tdR">
          
          OPENTM207104826 <img src="images/d2.gif"> <input id="wx_gzh_mb_adminfo" name="wx_gzh_mb_adminfo" type="text" class="W400" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_adminfo'];?>">
          <div id="wx_gzh_mb_adminfo_help" class="helpC S14">请登录公众平台后在左侧菜单 > 模板消息 > 模板库 > 搜索“<font class="Cf00">后台操作提醒</font>”找到编号：OPENTM207104826，点击 详情 > 添加 > 后可获得该模版ID，其他模版消息同理。。。</div>
          <img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:wx_gzh_mb_adminfo_help,title:'公众号自动回复信息',w:400,h:230});">
          
          </td>
		  </tr>
		<tr>
		  <td rowspan="2" class="tdL">用户咨询提醒</td>
		  <td colspan="3" class="tdR C999">OPENTM202119578 <img src="images/d2.gif"> <input id="wx_gzh_mb_msgchat" name="wx_gzh_mb_msgchat" type="text" class="W400 Caaa" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_msgchat'];?>"><span class="tips">此模版新客户添加已失效，新客户请留空，新客户埴写下面没失效的</span></td>
		  </tr>
          
		<tr>
		  <td colspan="3" class="tdR">OPENTM401760085 <img src="images/d2.gif"> <input id="wx_gzh_mb_msgchat2" name="wx_gzh_mb_msgchat2" type="text" class="W400" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_msgchat2'];?>">
          
          <div id="OPENTM401760085" class="helpC S14">请登录公众平台后在左侧菜单 > 模板消息 > 模板库 > 搜索“<font class="Cf00">新咨询通知</font>”找到编号：OPENTM401760085，点击 详情 > 添加 > 后可获得该模版ID，其他模版消息同理。。。</div>
          <img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:OPENTM401760085,title:'公众号自动回复信息',w:400,h:230});">

          </td>
		  </tr>
          
		<tr>
		  <td class="tdL">会员资料审核提醒</td>
		  <td colspan="3" class="tdR">OPENTM201057607 <img src="images/d2.gif"> <input id="wx_gzh_mb_udata" name="wx_gzh_mb_udata" type="text" class="W400" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_udata'];?>">
          <div id="OPENTM201057607" class="helpC S14">请登录公众平台后在左侧菜单 > 模板消息 > 模板库 > 搜索“<font class="Cf00">会员资料审核提醒</font>”找到编号：OPENTM201057607，点击 详情 > 添加 > 后可获得该模版ID，其他模版消息同理。。。</div>
          <img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:OPENTM201057607,title:'公众号自动回复信息',w:400,h:230});">
          </td>
		  </tr>
            <tr>
            <td rowspan="2" class="tdL">认证通知</td>
            <td colspan="3" class="tdR C999">OPENTM204559869 <img src="images/d2.gif"> <input id="wx_gzh_mb_honor" name="wx_gzh_mb_honor" type="text" class="W400 Caaa" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_honor'];?>"><span class="tips">此模版新客户添加已失效，新客户请留空，新客户埴写下面没失效的</span></td>
            </tr>
            
            <tr>
            <td colspan="3" class="tdR">OPENTM415975057 <img src="images/d2.gif"> <input id="wx_gzh_mb_honor2" name="wx_gzh_mb_honor2" type="text" class="W400" maxlength="100" value="<?php echo $_GZH['wx_gzh_mb_honor2'];?>">
            <div id="OPENTM415975057" class="helpC S14">请登录公众平台后在左侧菜单 > 模板消息 > 模板库 > 搜索“<font class="Cf00">身份认证结果通知</font>”找到编号：OPENTM415975057，点击 详情 > 添加 > 后可获得该模版ID，其他模版消息同理。。。</div>
            <img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:OPENTM415975057,title:'公众号自动回复信息',w:400,h:230});">
            </td>
            </tr>
            
        <input name="submitok" type="hidden" value="cache_gzh">
		</table>
        
<br><br><br><br><br>
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
        
        
	<!--QQ/微信登录API设置-->
	<?php }elseif($t == 3){
		
		if(!in_array('var_qqloginemail',$QXARR))exit(noauth());
		
	?>		

		<table class="table size1 cols2" style="width:1111px;margin:0 0 0 20px">
		<tr>
			<th colspan="4" align="left" style="border:0">PC微信登录
				<div id="wx_open" class="helpC S14">
１。顶部【管理中心】，下面第二个【网站应用】然后【创建网站应用】，创建成功后点【查看】就可以看到当前网站的AppID和AppSecret了<br><br>
２。顶部【管理中心】，下面第三个【公众帐号】然后【绑定公从号】				
<br>
				</div>
				<img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:wx_open,title:'微信登录开放平台设置',w:600,h:240});">
			　<font class="tips FR">注册/申请微信开放平台帐号：<a href="https://open.weixin.qq.com/" target="_blank" class="aQING"> 微信开放平台</a></font>
			</th>
		</tr>
		<tr>
			<td class="tdL">微信开放平台appid</td>
			<td class="tdR"><input id="wx_open_appid" name="wx_open_appid" type="text" class="W300" maxlength="100" value="<?php echo $_LOGIN['wx_open_appid'];?>"></td>
			<td align="left" class="tdL">微信开放平台appsecret</td>
			<td align="left"class="tdR"><input id="wx_open_appsecret" name="wx_open_appsecret" type="text" class="W300" maxlength="100" value="<?php echo $_LOGIN['wx_open_appsecret'];?>"></td>
		</tr>
		<tr><th colspan="4" align="left" style="border:0">&nbsp;</th></tr>
		<tr>
			<th colspan="4" align="left" style="border:0">QQ登录设置
				<div id="qq_login" class="helpC S14">
登录到QQ互联<br>
１．顶部【应用管理】，网站应用 > 创建应用 > 创建网站应用<br>
２．网站回调域，如：<?php echo HOST; ?>/api/qq/login/CS.php，域名换成自己的
				</div>
				<img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:qq_login,title:'QQ登录QQ互联设置',w:600,h:240});">
			　<font class="tips FR">注册/申请QQ互联平台帐号：<a href="https://connect.qq.com/" target="_blank" class="aQING">QQ互联平台</a></font>
			</th>
		</tr>

		<tr>
			<td class="tdL">QQ网站应用appid</td>
			<td class="tdR"><input id="qq_login_appid" name="qq_login_appid" type="text" class="W300" maxlength="100" value="<?php echo $_LOGIN['qq_login_appid'];?>"></td>
			<td align="left" class="tdL">QQ网站应用appkey</td>
			<td align="left"class="tdR"><input id="qq_login_appkey" name="qq_login_appkey" type="text" class="W300" maxlength="100" value="<?php echo $_LOGIN['qq_login_appkey'];?>"></td>
		</tr>
		</table>
    <input name="submitok" type="hidden" value="cache_login">
    <div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
	<!--手机短信运营商设置-->
	<?php }elseif($t == 4){
		
		if(!in_array('var_smsemail',$QXARR))exit(noauth());
		
	 ?>		
		<table class="table size1 cols2" style="width:1111px;margin:0 0 0 20px">
		<tr>
			<th colspan="4" align="left" style="border:0">手机短信参数
				<div id="sms" class="helpC S14">

登录<a href="http://www.rcscloud.cn/hy/hy_zh/login" target="_blank" class="blue">美圣融云短信运营商平台</a>，如果没有帐号，先注册一个。<br>
--------------------------------------------------------------------------------------------------------<br>
１．进入后，点击左侧菜单　短信充值>【短信充值】，套餐１（50元），先充1000条，去支付。<br>
２．顶部第二个菜单【资质】进入上传认证。<br>
３．点击左侧菜单　短信设置>【帐号/签名】，选中（打勾）当前【美圣融云】签名>点上面的第三个黄色按钮【✎编辑】，把“美圣融云”改成你自己的比如择爱网，报备IP就是你服务器的IP，其它默认。<br>
４．资质认证成功和短信充值好了以后，点击左侧菜单　短信设置>【短信模版】>然后点上面的第一个蓝色按钮【＋新建】两个短信模版。<br>
　　-------------------模版1-----------------<br>
　　模板名称：择爱网_验证码<br>
　　模版内容：您的验证码为：@1@，请尽快验证!<br>
　　产品帐号：默认已选中了你改过的签名了，不要动<br>
　　模版类型：验证码<br>
　　其它默认<br>
　　-------------------模版2-------------------<br>
　　模板名称：择爱网_密码找回<br>
　　模版内容：您的新密码为：@1@，请妥善保管!<br>
　　产品帐号：默认已选中了你改过的签名了，不要动<br>
　　模版类型：通知类<br>
　　其它默认<br>
５．点击左侧菜单　短信设置>【帐号/签名】和【短信模版】即可查看到 短信帐号、APIKEY、模版编号
<br><br><br>
				</div>
				<img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:sms,title:'手机短信运营商设置帮助',w:800,h:500});">
			　<font class="tips FR">注册/申请短信运营商帐号：<a href="http://www.rcscloud.cn/hy/hy_zh/login" target="_blank" class="aQING"> 美圣融云</a></font></th>
		</tr>
		<tr>
			<td class="tdL">短信产品帐号</td>
			<td class="tdR"><input id="sms_sid" name="sms_sid" type="text" class="W300" maxlength="100" value="<?php echo $_SMS['sms_sid'];?>"></td>
			<td align="left" class="tdL">短信产品帐号apikey</td>
			<td align="left"class="tdR"><input id="sms_apikey" name="sms_apikey" type="text" class="W300" maxlength="100" value="<?php echo $_SMS['sms_apikey'];?>"></td>
		</tr>
		<tr>
			<td class="tdL"><b>验证码</b>-模板编号</td>
			<td class="tdR"><input id="sms_tplid_authcode" name="sms_tplid_authcode" type="text" class="W300" maxlength="100" value="<?php echo $_SMS['sms_tplid_authcode'];?>"></td>
			<td align="left" class="tdL">手机验证码/邮箱验证码获取最多次数</td>
			<td align="left"class="tdR"><input id="sms_yzmnum" name="sms_yzmnum" type="text" class="W50" maxlength="3" value="<?php echo $_SMS['sms_yzmnum'];?>">
            <span class="tips">设为0不限次数</span></td>
		</tr>
		<tr>
			<td class="tdL"><b>找回密码</b>-模板编号</td>
			<td class="tdR"><input id="sms_tplid_findpass" name="sms_tplid_findpass" type="text" class="W300" maxlength="100" value="<?php echo $_SMS['sms_tplid_findpass'];?>"></td>
			<td align="left" class="tdL">&nbsp;</td>
			<td align="left"class="tdR">&nbsp;</td>
		</tr>
		<tr><th colspan="4" align="left" style="border:0"></tr><tr>
        
		<tr><th colspan="4" align="left" style="border:0">邮箱参数<span class="tips">　(请咨询邮箱提供商是否开通不限量网站发邮件功能，推荐使用<font class="C00f">收费企业邮局或QQ邮箱</font>,QQ邮箱需开启smtp并获取授权码)。注：阿里云已屏蔽25端口，必须使用465</span></tr>
			<td class="tdL">发件人邮箱地址</td>
			<td class="tdR"><input id="email_email" name="email_email" type="text" class="W300" maxlength="100" value="<?php echo $_SMS['email_email'];?>"></td>
			<td align="left" class="tdL">端口</td>
			<td align="left"class="tdR"><input id="email_port" name="email_port" type="text" class="W300" maxlength="10" value="<?php echo $_SMS['email_port'];?>">
			  QQ邮箱为465</td>
		</tr>
		<tr>
			<td class="tdL">SMTP地址</td>
			<td class="tdR"><input id="email_smtp" name="email_smtp" type="text" class="W300" maxlength="100" value="<?php echo $_SMS['email_smtp'];?>">
		    QQ邮箱前面要加ssl://</td>
			<td align="left" class="tdL">SMTP授权码/邮箱密码</td>
			<td align="left"class="tdR"><input id="email_pwd" name="email_pwd" type="text" class="W300" maxlength="100" value="<?php echo $_SMS['email_pwd'];?>"></td>
		</tr>
		<tr>
			<td class="tdL">SMTP服务器用户名</td>
			<td class="tdR"><input id="email_uid" name="email_uid" type="text" class="W300" maxlength="100" value="<?php echo $_SMS['email_uid'];?>">
			QQ邮箱只需@前面部分</td>
			<td align="left" class="tdL">是否开启debug调试</td>
			<td align="left"class="tdR"><input id="email_debug" name="email_debug" type="text" class="W50" maxlength="1" value="<?php echo $_SMS['email_debug'];?>">
			默认0，2为开启，正式使用请填0</td>
		</tr>
		</table>
        <input name="submitok" type="hidden" value="cache_sms">
        <div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
	<!--在线支付账户设置-->
	<?php }elseif($t == 5){ 
	if(!in_array('var_payset',$QXARR))exit(noauth());
	?>		
		<table class="table size1 cols2" style="width:1111px;margin:0 0 0 20px">
		<tr>
			<th colspan="4" align="left" style="border:0"><img src="../res/wxpayico.png" width="20" style="vertical-align:middle"> <font style="vertical-align:middle">微信支付设置</font>
<div id="wxpay" class="helpC S14">
１． 登录 <a href="https://pay.weixin.qq.com" target="_blank" class="blue">公众号（服务号）</a>，点击左侧菜单【微信支付】，然后申请，申请成功后会自动发送支付商号MCHID给您<br>　　<font class="Cf00">注意：</font>首次登录商户平台设置操作会跳转到设置操作密码步骤，这个很重要，相当于登录密码，记下来不要忘记，以后每操作一步都会让你验证这个操作密码<br><br>
２． 扫码登录<a href="https://pay.weixin.qq.com" target="_blank" class="blue"> 微信支付商户平台 </a>后，点击顶部菜单【账户中心】>再点左侧菜单【API安全】>[设置密钥]和[下载证书]，下载证书（apiclient_cert.pem、apiclient_key.pem、apiclient_cert.p12）放到总后台cert目录下面，然后把支付商号MCHID、密钥填到后台<br><br>
３． 产品中心 > 我的产品>添加>[企业付到零钱] 模块，并开通。如果没有请把https://pay.weixin.qq.com/index.php/public/product/detail?pid=5 这个网址复制到刚才支付的那个浏览器地址栏里面，打开一下就有了<br><br>
４． 产品中心 > 我的产品>添加>[H5支付]模块，并开通<br><br>
５． 产品中心 >开发配置，添加><br>　　公众号支付授权目录： <font class="blue"><?php echo HOST;?>/api/weixin/pay/</font>和<font class="blue"><?php echo HOST;?>/</font><br>　　H5支付域名 <font class="blue"><?php echo $_SERVER['HTTP_HOST'];?></font><br><br><br>
</div>
				<img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:wxpay,title:'微信支付设置',w:600,h:500});" style="vertical-align:middle">
			　<font class="tips FR"><a href="https://pay.weixin.qq.com" target="_blank" class="aQING"> 微信支付商户平台</a></font>
		</th>
		</tr>
		<tr>
			<td class="tdL">微信支付商户号MCHID</td>
			<td class="tdR"><input id="wxpay_mchid" name="wxpay_mchid" type="text" class="W300" maxlength="100" value="<?php echo $_PAY['wxpay_mchid'];?>"> <span class="tips"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">留空将自动隐藏微信支付</font></span></td>
			<td align="left" class="tdL">微信支付商户密钥KEY</td>
			<td align="left"class="tdR"><input id="wxpay_key" name="wxpay_key" type="text" class="W300" maxlength="100" value="<?php echo $_PAY['wxpay_key'];?>"></td>
		</tr>
         <tr><th colspan="4" align="left" style="border:0">&nbsp;</th></tr>
		<tr>
			<th colspan="4" align="left" style="border:0"><img src="../res/alipayico.png" width="20" style="vertical-align:middle"> <font style="vertical-align:middle">支付宝设置</font>
<div id="alipay" class="helpC S14">
首先登录 <a href="http://www.alipay.com" target="_blank" class="blue">支付宝商户平台</a>，选【我是商家用户】 > [我是支付宝商家] > 【电脑网站支付】和【手机网站支付】，如果没有帐号，先注册一个。<br>
------------------------------------------------------------------------------<br>
１．下载<a href="http://p.tb.cn/rmsportal_6680_secret_key_tools_RSA_win.zip?spm=a219a.7629140.0.0.FlpLkO&file=rmsportal_6680_secret_key_tools_RSA_win.zip" class="blue">一键生成RSA密钥工具</a>，下载后点击“RSA签名验签工具.bat” > PKCSI1(非JAVA适用) > 密钥长度2048 > 生成密钥，复制【商户应用私钥】内容填到后台【商户私钥】，生成密钥工具窗口不要关闭，第3步要用<br><br>
２．给您的应用 设置私钥和公钥 <a href="https://openhome.alipay.com/platform/keyManage.htm" target="_blank" class="blue">进入支付宝开放平台设置界面</a><br>　　设置回调地址<?php echo HOST.'/api/ali/pay/notify_url.php';?><br><br>
３．复制刚才下载的生成工具【商户应用公钥】内容填到支付宝开放平台的您的应用中，就可以获得【支付宝公钥】，获取后复制填到后台【支付宝公钥】<br><br>
４.也可以进入开放平台后，点击顶部【开发者中心】->网页&移动应用->创建应用->支付接入->上传你的LOGO，审核成功后，点应用信息设置公钥和回调地址，其它默认</div>
				<img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:alipay,title:'支付宝设置',w:600,h:400});">
			　<font class="tips FR">注册/申请支付宝平台帐号：<a href="https://www.alipay.com" target="_blank" class="aQING">支付宝商户平台</a></font>
		</th>
		</tr>

		<tr>
			<td class="tdL">应用APPID</td>
			<td colspan="3" class="tdR"><input id="alipay_appid" name="alipay_appid" type="text" class="W300" maxlength="100" value="<?php echo $_PAY['alipay_appid'];?>"> <span class="tips"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">留空将自动隐藏支付宝支付</font></span></td>
		  </tr>
		<tr>
			<td class="tdL">商户私钥</td>
			<td colspan="3" class="tdR"><textarea name="alipay_key1" rows="13" class="W98_" id="alipay_key1"><?php echo $_PAY['alipay_key1'];?></textarea></td>
		  </tr>
		<tr>
			<td class="tdL">支付宝公钥</td>
			<td colspan="3" class="tdR"><textarea name="alipay_key2" rows="4" class="W98_" id="alipay_key2"><?php echo $_PAY['alipay_key2'];?></textarea></td>
		  </tr>
		<tr><th colspan="4" align="left" style="border:0">&nbsp;</th></tr>
		<tr><th colspan="4" align="left" style="border:0">在线支付记录安全日志</th></tr>
		<tr>
			<td class="tdL">安全后辍变量</td>
			<td colspan="3" class="tdR"><input id="logname" name="logname" type="text" class="W100" maxlength="20" value="<?php echo $_PAY['logname'];?>"><span class="tips">日志文件名后辍，安全防盗，如：填_zeai，实际名称将是<?php echo date('Ymd');?>_zeai.txt，此支付日志在您的服务器<?php echo ZEAI.'up'.DIRECTORY_SEPARATOR.'p'.DIRECTORY_SEPARATOR.'paylog'.DIRECTORY_SEPARATOR;?>目录下面，可以下载后进行删除</span></td>
		  </tr>
		</table>
        <input name="submitok" type="hidden" value="cache_pay">
<br><br><br><br><br>
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>

	<!--首页轮翻广告-->
	<?php }elseif($t == 6){
		if(!in_array('adv',$QXARR))exit(noauth());
		?>		

		<table class="table size1 cols2" style="width:1111px;margin:0 0 0 20px">
		<tr><th colspan="2" align="left">手机端首页轮翻广告（尺寸:1000*463像数，类型:jpg，品质65左右为宜）</th></tr>

          <tr>
            <td align="right" bgcolor="#f8f8f8">手机轮翻广告①</td>
            <td align="left" valign="top">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
                <tr>
                <td width="320">
					<?php if (!empty($_INDEX['mBN_path1_s'])) {?>
                        <img width="250" height="116" src="<?php echo $_ZEAI['up2']."/".$_INDEX['mBN_path1_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($_INDEX['mBN_path1_s'],'b'); ?>')">　
                        <a href="###" onClick="bannerDel(1)" class="btn size1" >删除</a>　
                    <?php }else{ 
						echo "<input name=pic1 type=file size=30 class='input size2' />";
                    }?>
                </td>
                <td>
                	链接① <input name="path1_url" type="text" class="input size2" id="path1_url" value="<?php echo stripslashes($_INDEX['mBN_path1_url']);?>"size="50" maxlength="100">
                </td>
                </tr>
                </table>
            </td>
        </tr>


    <tr>
        <td align="right" bgcolor="#f8f8f8">手机轮翻广告②</td>
        <td align="left" valign="top">
        
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
        <tr>
        <td width="320"><?php if (!empty($_INDEX['mBN_path2_s'])) {?>
        <img width="250" height="116" src="<?php echo $_ZEAI['up2']."/".$_INDEX['mBN_path2_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($_INDEX['mBN_path2_s'],'b'); ?>')">　<a href="###"  onClick="bannerDel(2)" class="btn size1">删除</a>　
        <?php }else{ 
        echo "<input name=pic2 type=file size=30 class='input size2' />";
        }?></td>
        <td>链接②
        <input name="path2_url" type="text" class="input size2" id="path2_url" value="<?php echo stripslashes($_INDEX['mBN_path2_url']);?>"size="50" maxlength="100"></td>
        </tr>
        </table>
        </td>
    </tr>
          
          
    <tr>
        <td align="right" bgcolor="#f8f8f8">手机轮翻广告③</td>
        <td align="left" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
        <tr>
        <td width="320"><?php if (!empty($_INDEX['mBN_path3_s'])) {?>
        <img width="250" height="116" src="<?php echo $_ZEAI['up2']."/".$_INDEX['mBN_path3_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($_INDEX['mBN_path3_s'],'b'); ?>')">　<a href="###"  onClick="bannerDel(3)" class="btn size1">删除</a>　
        <?php }else{ 
        echo "<input name=pic3 type=file size=30 class='input size2' />";
        }?></td>
        <td>链接③
        <input name="path3_url" type="text" class="input size2" id="path3_url" value="<?php echo stripslashes($_INDEX['mBN_path3_url']);?>"size="50" maxlength="100"></td>
        </tr>
        </table>
        </td>
    </tr>
    <tr><th colspan="2" align="left">电脑端首页轮翻广告（尺寸:1903*442像数，类型:jpg，品质65左右为宜）</th></tr>


          <tr>
            <td align="right" bgcolor="#f8f8f8">电脑轮翻广告①</td>
            <td align="left" valign="top">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
                <tr>
                <td width="320">
					<?php if (!empty($_INDEX['pcBN_path1_s'])) {?>
                        <img width="250" height="58" src="<?php echo $_ZEAI['up2']."/".$_INDEX['pcBN_path1_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($_INDEX['pcBN_path1_s'],'b'); ?>')">　
                        <a href="###" onClick="bannerDel(4)" class="btn size1" >删除</a>　
                    <?php }else{ 
						echo "<input name=pic4 type=file size=30 class='input size2' />";
                    }?>
                </td>
                <td>
                	链接① <input name="path4_url" type="text" class="input size2" id="path4_url" value="<?php echo stripslashes($_INDEX['pcBN_path1_url']);?>"size="50" maxlength="100">
                </td>
                </tr>
                </table>
            </td>
        </tr>


          <tr>
            <td align="right" bgcolor="#f8f8f8">电脑轮翻广告②</td>
            <td align="left" valign="top">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
                <tr>
                <td width="320">
					<?php if (!empty($_INDEX['pcBN_path2_s'])) {?>
                        <img width="250" height="58" src="<?php echo $_ZEAI['up2']."/".$_INDEX['pcBN_path2_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($_INDEX['pcBN_path2_s'],'b'); ?>')">　
                        <a href="###" onClick="bannerDel(5)" class="btn size1" >删除</a>　
                    <?php }else{ 
						echo "<input name=pic5 type=file size=30 class='input size2' />";
                    }?>
                </td>
                <td>
                	链接② <input name="path5_url" type="text" class="input size2" id="path5_url" value="<?php echo stripslashes($_INDEX['pcBN_path2_url']);?>"size="50" maxlength="100">
                </td>
                </tr>
                </table>
            </td>
        </tr>


          <tr>
            <td align="right" bgcolor="#f8f8f8">电脑轮翻广告③</td>
            <td align="left" valign="top">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
                <tr>
                <td width="320">
					<?php if (!empty($_INDEX['pcBN_path3_s'])) {?>
                        <img width="250" height="58" src="<?php echo $_ZEAI['up2']."/".$_INDEX['pcBN_path3_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($_INDEX['pcBN_path3_s'],'b'); ?>')">　
                        <a href="###" onClick="bannerDel(6)" class="btn size1" >删除</a>　
                    <?php }else{ 
						echo "<input name=pic6 type=file size=30 class='input size2' />";
                    }?>
                </td>
                <td>
                	链接③ <input name="path6_url" type="text" class="input size2" id="path6_url" value="<?php echo stripslashes($_INDEX['pcBN_path3_url']);?>"size="50" maxlength="100">
                </td>
                </tr>
                </table>
            </td>
        </tr>
        <!--植入广告-->
		<tr><th colspan="2" align="left">手机端瀑布流植入广告（尺寸:300*375像数，类型:jpg，品质65左右为宜，<font class="Cf00">删除图片将不显示</font>）</th></tr>
        <?php
        $zeai_pplAD = json_decode($_INDEX['zeai_pplAD'],true);
        $zeai_pplAD_num=count($zeai_pplAD);
        for($i=1;$i<=$zeai_pplAD_num;$i++) {
            switch ($i) {
                case 1:$ii  = '①';break;
                case 2:$ii  = '②';break;
                case 3:$ii  = '③';break;
                case 4:$ii  = '④';break;
                case 5:$ii  = '⑤';break;
                case 6:$ii  = '⑥';break;
                case 7:$ii  = '⑦';break;
                case 8:$ii  = '⑧';break;
            }
            $AD=Zeai_pplAD($i,'i');
            if(!empty($AD)){
                $img=$AD['img'];$url=$AD['url'];$p=intval($AD['p']);
            }else{
                $img='';$url='';
            }?>
          <tr>
            <td align="right" bgcolor="#f8f8f8">瀑布流广告<?php echo $ii;?></td>
            <td align="left" valign="top">
              <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
                <tr>
                <td width="200">
                    <?php if (!empty($img)) {?>
                        <img width="100" height="125" src="<?php echo $_ZEAI['up2']."/".$img; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".$img; ?>')">　
                        <a onClick="pplimgDel(<?php echo $i;?>)" class="btn size1 hand" >删除</a>　
                    <?php }else{ 
                        echo "<input name=pplpic".$i." type=file size=30 class='input size2' />";
                    }?>
                </td>
                <td style="padding-left:20px">链接网址<?php echo $ii;?> <input name="pplurl<?php echo $i;?>" type="text" class="input size2 W80_" value="<?php echo urldecode($url);?>" maxlength="300"><br><br>
                插入位置<?php echo $ii;?> <input name="pplp<?php echo $i;?>" type="text" class="input size2" value="<?php echo $p;?>" size="5" maxlength="5"> <span class="tips">就是首页排名名次位置，比如填：3，就显示到第三个，插入位置请不要相同</span>
                </td>
                </tr>
                </table>
            </td>
        </tr>
    <?php }?>

    </table>
<input name="submitok" type="hidden" value="cache_banner">
<br><br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">确认并保存</button></div>
    
    
<!--导航/模块设置-->
	<?php }elseif($t == 'nav'){
		if(!in_array('nav',$QXARR))exit(noauth());
		$nav = json_decode($_ZEAI['nav'],true);
	?>
	<script src="js/Sortable1.6.1.js"></script>
    <style>
    .inav{clear:both;overflow:auto;text-align:center}
    .inav li{width:118px;float:left;margin:20px 0;display:block}
    .inav li font{width:60px;height:60px;line-height:60px;border-radius:18px;font-size:40px;display:inline-block;color:#fff}
    .inav li span{width:100px;display:block;font-size:14px;margin:5px auto}
    .inav li em{height:30px;}
    .inav li font.dating{background-color:#FD45A7}
    .inav li font.trend{background-color:#FF7624}
    .inav li font.party{background-color:#D86EA3;font-size:32px}
    .inav li font.video{background-color:#D861B9}
    .inav li font.hn{background-color:#FF5065}
    .inav li font.article{background-color:#4c9664;}
    .inav li font.group{background-color:#A884E9}
    .inav li font.hb{background-color:#EE5A4E}
    .inav li font.gift{background-color:#FD9F23;font-size:33px}
    .inav li font.xqcard{background-color:#F7564D}
    .inav li font.tg{background-color:#1478F0}
    .inav li font.hi{background-color:#FD66B5}
    .inav li font.robot{background-color:#01296E}
	.inav li font.chat{background-color:#31C93C}
	.inav li font.contact{background-color:#51B7EC}
	.inav li font.store{background-color:#E21C19;font-size:36px}
    .inav li.off span{color:#999}
    .inav li.off font{background-color:#ccc}
	.tips{font-size:12px;}
	.navkind{border-bottom:#eee 1px solid;padding:10px 0 20px 10px;text-align:center}
	.navdiy{clear:both;overflow:auto;text-align:left;padding:10px 0}
	.navdiy .stepbox{margin-bottom:10px}
	.navdiy .stepbox .stepli{margin-top:5px}
	.navdiy .stepbox .stepli li{text-align:center;width:90px;padding:14px 10px 10px 10px;float:left;margin:8px 5px 8px 12px;border:#ddd 1px solid;cursor:move;background-color:#f8f8f8;border-radius:13px}
	.navdiy .stepbox .stepli li p{width:60px;height:60px;line-height:60px;border-radius:18px;display:inline-block;background-color:#f29999;overflow:hidden}
	.navdiy .stepbox .stepli li p:hover{background-color:#FF6F6F;cursor:pointer}
	.navdiy .stepbox .stepli li.off p:hover{background-color:#f29999}
	.navdiy .stepbox .stepli li p img{width:60px;height:60px;object-fit:cover;-webkit-object-fit:cover}
	.navdiy .stepbox .stepli li span{width:100%;display:block;font-size:14px;margin:0 0 5px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
	.navdiy .stepbox .stepli li em{height:30px;}
	.navdiy .stepbox .stepli li.off p{background-color:#ccc}
	.navdiy .stepbox .stepli li.off p img{-webkit-filter:grayscale(100%);-moz-filter:grayscale(100%);-ms-filter:grayscale(100%);-o-filter:grayscale(100%);filter:grayscale(100%)}
	.navdiy .stepbox .stepli li.off span{color:#999}
    </style>  
	<table class="table size2" style="min-width:1111px;margin:0 0 0 20px">
    <tr><th colspan="2" align="left" style="border:0">前台导航模块</th></tr>
	<tr><td colspan="2" align="left" class="S14">
    <div class="navkind">
        <input type="radio" name="navkind" id="navkind1" class="radioskin" value="1"<?php echo ($_ZEAI['navkind'] == 1)?' checked':'';?>><label for="navkind1" class="radioskin-label"><i></i><b class="W200 ">官方默认导航</b></label>
        <input type="radio" name="navkind" id="navkind2" class="radioskin" value="2"<?php echo ($_ZEAI['navkind'] == 2)?' checked':'';?>><label for="navkind2" class="radioskin-label"><i></i><b class="W200 ">自定义导航</b></label>
   </div> 
    <!--官方默认导航-->
    <div class="inav" id="navdefault"<?php echo ($_ZEAI['navkind'] != 1)?' style="display:none"':'';?>>
        <li<?php echo (@!in_array('dating',$nav))?' class="off"':'';?>>
        	<font class="ico dating">&#xe72e;</font><span>约会</span>
            <em><input type="checkbox" name="nav[]" id="dating" class="switch" value="dating"<?php echo (@in_array('dating',$nav))?' checked':'';?>><label for="dating" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        <li<?php echo (@!in_array('video',$nav))?' class="off"':'';?>>
        	<font class="ico video">&#xe668;</font><span>视频</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="video" value="video"<?php echo (@in_array('video',$nav))?' checked':'';?>><label for="video" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        <li<?php echo (@!in_array('hb',$nav))?' class="off"':'';?>>
        	<font class="ico hb">&#xe66b;</font><span>红包</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="hb" value="hb"<?php echo (@in_array('hb',$nav))?' checked':'';?>><label for="hb" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
         </li>
        <li<?php echo (@!in_array('trend',$nav))?' class="off"':'';?>>
        	<font class="ico trend">&#xe63b;</font><span>交友圈</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="trend" value="trend"<?php echo (@in_array('trend',$nav))?' checked':'';?>><label for="trend" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
		</li>
        <li<?php echo (@!in_array('hn',$nav))?' class="off"':'';?>>
        	<font class="ico hn">&#xe621;</font><span>红娘服务</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="hn" value="hn"<?php echo (@in_array('hn',$nav))?' checked':'';?>><label for="hn" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
		</li>
        <li<?php echo (@!in_array('party',$nav))?' class="off"':'';?>>
        	<font class="ico party">&#xe776;</font><span>交友活动</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="party" value="party"<?php echo (@in_array('party',$nav))?' checked':'';?>><label for="party" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
		</li>
        <li<?php echo (@!in_array('article',$nav))?' class="off"':'';?>>
        	<font class="ico article">&#xe63c;</font><span>婚恋课堂</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="article" value="article"<?php echo (@in_array('article',$nav))?' checked':'';?>><label for="article" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
		</li>
        <li<?php echo (@!in_array('shop',$nav))?' class="off"':'';?>>
        	<font class="ico2 store">&#xe71a;</font><span>商家</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="shop" value="shop"<?php echo (@in_array('shop',$nav))?' checked':'';?>><label for="shop" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>

        <li<?php echo (@!in_array('group',$nav))?' class="off"':'';?>>
        	<font class="ico group">&#xe637;</font><span>圈子</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="group" value="group"<?php echo (@in_array('group',$nav))?' checked':'';?>><label for="group" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>

    </div>
    <!--自定义导航-->
    <div class="navdiy" id="navdiy"<?php echo ($_ZEAI['navkind'] != 2)?' style="display:none"':'';?>>
        <dd class="stepbox" id="stepbox">
            <div class="stepli">
                <?php
                $navdiyARR = json_decode($_ZEAI['navdiy'],true);
                if (count($navdiyARR) >= 1 && is_array($navdiyARR)){
                    foreach ($navdiyARR as $V) {
                        $i=$V['i'];$img=$V['img'];$f=intval($V['f']);
                        $img=(empty($img))?'images/navadd.png':$_ZEAI['up2'].'/'.$img;?>
                        <li<?php echo ($f==0)?' class="off"':'';?>>
                            <p title="点击进行设置" class="navdiyli" i="<?php echo $i;?>"><img src="<?php echo $img;?>" id="img<?php echo $i;?>"></p><span id="span<?php echo $i;?>"><?php echo dataIO($V['t'],'out');?></span>
                            <em><input type="checkbox" class="switch navdiy_f" i="<?php echo $i;?>" name="navdiy_f<?php echo $i;?>" id="navdiy_f<?php echo $i;?>" value="1"<?php echo ($f==1)?' checked':'';?>><label for="navdiy_f<?php echo $i;?>" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
                        </li><?php
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
        </dd>
        <img src="images/!.png" width="14" height="14" valign="middle" style="margin-left:20px"> <font class="picmiddle S12 C999">修改图片/名称/超链接，请点击图标修改；可以按住不放拖动项目调整前后顺序，开启显示，关闭不显示；图标请上传正方形png格式150x150像数为宜；注：联盟和圈子只有手机端</font>
        <button class="btn size2 FR" type="button" onClick="zeai.div({obj:navdiy_help,title:'官网默认功能链接（根据需要复制）',w:666,h:450});" style="margin-right:30px">查看内置链接</button>
    </div>
    <div id="navdiy_help" class="helpC S14">
        <span class="tips" style="font-family:Verdana, Geneva, sans-serif">
        	<font class="Cf00">注：图标、导航名称、导航链接三个同时不为空才会显示导航</font><br>
            【首页】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/?z=index　　导航变量：home<br>
            【商家】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/m4/shop_index.php　　导航变量：无<br>
            【活动】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/?z=party　　导航变量：party<br>
            【约会】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/?z=dating　　导航变量：dating<br>
            【视频】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/?z=video　　导航变量：video<br>
            【红娘】<img src="images/d2.gif"> 导航链接：javascript:page({g:'m1/hongniang.php',l:'hongniang'});<br>
            【婚恋学堂】<img src="images/d2.gif"> 导航链接：<?php echo wHref('article'); ?>　　导航变量：article<br>
            【交友圈】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/?z=trend　　导航变量：trend<br>
            【推荐】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/?z=tuijian　　导航变量：tj<br>
            【消息】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/?z=msg&e=sx　　导航变量：msg<br>
            【我的】<img src="images/d2.gif"> 导航链接：<?php echo HOST; ?>/?z=my　　导航变量：my<br>
        </span>
    </div>
	</td>
	</tr>

    <!--前台互动模块-->
    <tr><th colspan="2" align="left" style="border:0">&nbsp;</th></tr>
    <tr><th colspan="2" align="left" style="border:0">前台互动模块</th></tr>
    <tr><td class="tdL">模块列表</td>
    <td class="tdR">
    <div class="inav">
        <li<?php echo (@!in_array('gift',$nav))?' class="off"':'';?>>
        	<font class="ico gift">&#xe69a;</font><span>礼物</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="gift" value="gift"<?php echo (@in_array('gift',$nav))?' checked':'';?>><label for="gift" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        <li<?php echo (@!in_array('xqcard',$nav))?' class="off"':'';?>>
        	<font class="ico2 xqcard">&#xe64f;</font><span>相亲卡</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="xqcard" value="xqcard"<?php echo (@in_array('xqcard',$nav))?' checked':'';?>><label for="xqcard" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        <li<?php echo (@!in_array('tg',$nav))?' class="off"':'';?>>
        	<font class="ico tg">&#xe615;</font><span>获客推广分销</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="tg" value="tg"<?php echo (@in_array('tg',$nav))?' checked':'';?>><label for="tg" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        <li<?php echo (@!in_array('hi',$nav))?' class="off"':'';?>>
        	<font class="ico hi">&#xe6bd;</font><span>打招呼</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="hi" value="hi"<?php echo (@in_array('hi',$nav))?' checked':'';?>><label for="hi" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        <li<?php echo (@!in_array('chat',$nav))?' class="off"':'';?>>
        	<font class="ico chat">&#xe623;</font><span>私信/聊天</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="chat" value="chat"<?php echo (@in_array('chat',$nav))?' checked':'';?>><label for="chat" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        <li<?php echo (@!in_array('contact',$nav))?' class="off"':'';?>>
        	<font class="ico contact">&#xe60e;</font><span>查看联系方式</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="contact" value="contact"<?php echo (@in_array('contact',$nav))?' checked':'';?>><label for="contact" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        <li<?php echo (@!in_array('robot',$nav))?' class="off"':'';?>>
        	<font class="ico2 robot">&#xe695;</font><span>机器人</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="robot" value="robot"<?php echo (@in_array('robot',$nav))?' checked':'';?>><label for="robot" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>
        
        <li<?php echo (@!in_array('store',$nav))?' class="off"':'';?> style="display:none">
        	<font class="ico2 store">&#xe71a;</font><span>红娘圈</span>
        	<em><input type="checkbox" class="switch" name="nav[]" id="store" value="store"<?php echo (@in_array('store',$nav))?' checked':'';?>><label for="store" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
        </li>        
        
        
    </div>
    </td>
    </tr>
    <script>
	zeai.listEach('.switch',function(obj){
		obj.onclick = function(){
			var p=obj.parentNode.parentNode;
			if(obj.checked){
				p.removeClass('off');
			}else{
				p.addClass('off');
			}
		}
	});
	navkind1.onclick=function(){navdiy.hide();navdefault.show();}
	navkind2.onclick=function(){navdiy.show();navdefault.hide();}
	function drag_init(){
		(function (){
			[].forEach.call(stepbox.getElementsByClassName('stepli'), function (el){
				Sortable.create(el,{group: 'zeai_navdiy',animation:150});
			});
	})();}
	drag_init();
	zeai.listEach('.navdiyli',function(obj){
		obj.onclick = function(){
			var id = parseInt(obj.getAttribute("i"));
			zeai.iframe('导航DIY设置','var'+zeai.ajxext+'submitok=navdiy&id='+id,600,450);
		}
	});
    </script>
    
    <!--首页会员展示模块-->
    <tr><th colspan="2" align="left" style="border:0">&nbsp;</th></tr>
    <tr><th colspan="2" align="left" style="border:0">首页会员展示模块</th></tr>
    <tr><td class="tdL">手机端会员展示</td>
      <td class="tdR" style="padding:10px">
            <input type="radio" name="iModuleU" id="iModuleU1" class="radioskin" value="1"<?php echo ($_INDEX['iModuleU'] == 1)?' checked':'';?>><label for="iModuleU1" class="radioskin-label" style="margin-bottom:10px"><i class="i1"></i><b class="W500">
            <span class="btn size2 BAI">推荐<font class="C999">（智能适配，男显女，女显男，游客全部）</font></span>　<span class="btn size2 BAI">附近</span>　<span class="btn size2 BAI">VIP</span>　<span class="btn size2 BAI">匹配</span>
            </b></label><br>
            
            <input type="radio" name="iModuleU" id="iModuleU2" class="radioskin" value="2"<?php echo ($_INDEX['iModuleU'] == 2)?' checked':'';?>><label for="iModuleU2" class="radioskin-label" style="margin-bottom:10px"><i class="i1"></i><b class="W500">
            <span class="btn size2 BAI">推荐<font class="C999">（男女一起显示）</font></span>　<span class="btn size2 BAI">男生</span>　<span class="btn size2 BAI">女生</span>　<span class="btn size2 BAI">匹配</span>
            </b></label><br>
            
            显示会员数量： <input name="iModuleU_num" id="iModuleU_num" type="text" class="W50 FVerdana" maxlength="2" value="<?php echo $_INDEX['iModuleU_num'];?>"> <span class="tips">推荐6名，请填偶数，越少首页打开速度越快，不要超过20，这个数字对瀑布流展示风格无效</span><br><br>
            
            新版瀑布流首页头像： <input type="radio" name="waterfall_photo" id="waterfall_photo1" class="radioskin" value="m"<?php echo ($_INDEX['waterfall_photo'] == 'm')?' checked':'';?>><label for="waterfall_photo1" class="radioskin-label"><i class="i1"></i><b class="W120 ">裁切后标准头像</b></label>
            <input type="radio" name="waterfall_photo" id="waterfall_photo2" class="radioskin" value="b"<?php echo ($_INDEX['waterfall_photo'] == 'b')?' checked':'';?>><label for="waterfall_photo2" class="radioskin-label"><i class="i1"></i><b class="W80 ">原始大照片</b></label>
             <span class="tips">瀑布流首页头像展示类型，默认为裁切后标准头像（使用【裁切后标准头像】可节省流量，首页加载速度更快）</span>
      </td>
    </tr>
    <tr><td class="tdL">电脑端会员展示</td>
      <td class="tdR" style="padding:10px">
      
            <input type="radio" name="iModuleU_pc" id="iModuleU_pc1" class="radioskin" value="1"<?php echo ($_INDEX['iModuleU_pc'] == 1)?' checked':'';?>><label for="iModuleU_pc1" class="radioskin-label" style="margin-bottom:10px"><i class="i1"></i><b class="W700">
            <span class="btn size2 BAI">今日之星<font class="C999">（智能适配，男显女，女显男，游客全部）</font></span>　<span class="btn size2 BAI">同城会员</span>　<span class="btn size2 BAI">配匹我的</span>　<span class="btn size2 BAI">VIP会员</span>　<span class="btn size2 BAI">线下会员</span>
            </b></label><br>
            
            <input type="radio" name="iModuleU_pc" id="iModuleU_pc2" class="radioskin" value="2"<?php echo ($_INDEX['iModuleU_pc'] == 2)?' checked':'';?>><label for="iModuleU_pc2" class="radioskin-label" style="margin-bottom:10px"><i class="i1"></i><b class="W500">
            <span class="btn size2 BAI">今日之星<font class="C999">（男女一起显示）</font></span>　<span class="btn size2 BAI">优质男会员</span>　<span class="btn size2 BAI">优质女会员</span>　<span class="btn size2 BAI">配匹我的</span>
            </b></label><br>
            
            显示会员数量 <input name="iModuleU_pc_num" id="iModuleU_pc_num" type="text" class="W50 FVerdana" maxlength="2" value="<?php echo $_INDEX['iModuleU_pc_num'];?>"> <span class="tips">推荐10名，请填偶数，越少首页打开速度越快，不要超过40</span>
      </td>
    </tr>
    <tr><th colspan="2" align="left" style="border:0">&nbsp;</th></tr>
    <tr><th colspan="2" align="left" style="border:0">手机端【推荐】模块</th></tr>
    <tr><td class="tdL">红娘推荐</td>
    <td class="tdR" style="padding:10px">
		<style>
        .stepbox .stepli{margin-top:5px}
        .stepbox .stepli li{padding:5px 10px;margin:5px;border:#ddd 1px solid;background-color:#f8f8f8;border-radius:3px}
        .stepbox .stepli li input{background-color:#fff;font-size:14px}
		
		.Mnavbtmkind dl{padding:15px 0;margin:10px 0;text-align:center;border:#ddd 1px solid;background-color:#f8f8f8;border-radius:3px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
		.Mnavbtmkind dl input{padding:0;margin:10px 0;background-color:#fff}
		.Mnavbtmkind dl dt{width:30%;float:left}
		.Mnavbtmkind dl dt img{border-radius:6px}
		.Mnavbtmkind dl dd{width:65%;float:left;text-align:left}
        </style>
        <dd class="stepbox" id="stepbox">
            <div class="stepli">
                <?php
				for($i=1;$i<=$_TJDIY['tjdiy_num'];$i++){
					$title=tjdiy_info($i,'title');
					$par=tjdiy_info($i,'par');
					?>
					<li>
						<?php echo $i;?>．
						标题 <input name="tjtitle<?php echo $i;?>" class="W300" type="text" id="tjtitle<?php echo $i;?>" maxlength="50" value="<?php echo $title;?>" autoComplete="off"> <button class="btn size2" type="button" onClick="tjdiy(<?php echo $i;?>,'<?php echo urlencode($title);?>')">设置条件</button>　
						参数 <input name="tjpar<?php echo $i;?>" type="text" id="tjpar<?php echo $i;?>" value="<?php echo $par;?>" autoComplete="off"  class="W400" style="border:0;background-color:#F8F8F8" readonly>　
					</li>
					<?php
				}
                ?>
            </div>
            <div class="clear"></div>
        </dd>
    </td></tr>
    
    <!--底部导航菜单-->
    <tr><th colspan="2" align="left" style="border:0">&nbsp;</th></tr>
    <tr><th colspan="2" align="left" style="border:0">手机端【底部导航菜单DIY】</th></tr>
    <tr>
    <td class="tdL">底部导航菜单</td>
    <td class="tdR">
        <input type="radio" name="Mnavbtmkind" id="navbtmkind1" class="radioskin" value="1"<?php echo ($_ZEAI['Mnavbtmkind'] == 1)?' checked':'';?>><label for="navbtmkind1" class="radioskin-label"><i></i><b class="W120 ">官方默认：【首页】【推荐】【消息】【我的】</b></label><span class="tips">注：将默认调用官方优化图标</span><br>
        <input type="radio" name="Mnavbtmkind" id="navbtmkind2" class="radioskin" value="2"<?php echo ($_ZEAI['Mnavbtmkind'] == 2)?' checked':'';?>><label for="navbtmkind2" class="radioskin-label"><i></i><b class="W120 ">下方自定义</b></label>
        <button class="btn size2 FR" type="button" onClick="zeai.div({obj:navdiy_help,title:'官网默认功能链接（根据需要复制）',w:666,h:450});">查看内置链接</button>
        <div class="Mnavbtmkind">
            <?php
            for($i=1;$i<=5;$i++){
                $title=urldecode(Mnavbtm_info($i,'title'));
                $url  =urldecode(Mnavbtm_info($i,'url'));
                $var  =urldecode(Mnavbtm_info($i,'var'));
                $path1  =Mnavbtm_info($i,'path1');
                $path2  =Mnavbtm_info($i,'path2');
                ?>
                <dl>
                	<dt>
                    <?php if (!empty($path1)) {?>
                        <a class="pic60"><img  src="<?php echo $_ZEAI['up2']."/".$path1; ?>" class="zoom" align="absmiddle" alt="点击放大显示" title="点击放大显示" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".$path1; ?>')"></a>
                        <a href="javascript:;" onClick="MnavbtmDel('<?php echo $i;?>_1')" class="btn size1" >删除</a><br><br>
                        <input name="path<?php echo $i;?>_1" type="hidden" value="<?php echo $path1;?>">
                    <?php }else{ 
                        echo "未选图标 <input name='pic".$i."_1' type='file' class='input size2 W200' accept='image/gif,image/jpeg,image/png' /><br>";
                    }?> 
                    <?php if (!empty($path2)) {?>
                        <a class="pic60"><img  src="<?php echo $_ZEAI['up2']."/".$path2; ?>" class="zoom" align="absmiddle" alt="点击放大显示" title="点击放大显示" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".$path2; ?>')"></a>
                        <a href="javascript:;" onClick="MnavbtmDel('<?php echo $i;?>_2')" class="btn size1" >删除</a>
                        <input name="path<?php echo $i;?>_2" type="hidden" value="<?php echo $path2;?>">
                    <?php }else{ 
                        echo "选中图标 <input name='pic".$i."_2' type='file' class='input size2 W200' accept='image/gif,image/jpeg,image/png' />";
                    }?> 
                    </dt>
					<dd>
                        导航名称 <input name="title<?php echo $i;?>" type="text" id="title<?php echo $i;?>" maxlength="4" class="W100" value="<?php echo $title;?>" autoComplete="off">　
                        导航变量 <input name="var<?php echo $i;?>" type="text" id="var<?php echo $i;?>" maxlength="20" class="W100" value="<?php echo $var;?>" autoComplete="off"><span class="tips">可以根据此判断未选和已选图标变色</span><br>
                        导航链接 <input name="url<?php echo $i;?>" type="text" id="url<?php echo $i;?>" maxlength="200" class="W500" value="<?php echo $url;?>" autoComplete="off">
					</dd>
                    <div class="clear"></div>
                </dl>
                <?php
            }
            ?>
        </div>
    	<span class="tips">图标尺寸：100*100像数正方形，超过将自动缩略，图片类型为.jpg，.gif，.png，推荐透明png格式，导航名称不能超过4个字</span>
    </td></tr>
	<script>function tjdiy(id,title2){zeai.iframe('【'+decodeURIComponent(title2)+'】条件筛选','var.php?submitok=tjdiy&id='+id);}</script>
</table>
<input name="submitok" type="hidden" value="cache_nav">
<input name="navdiy_px" id="navdiy_px" type="hidden" value="">

<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
<script>
save.onclick = function(){
	var DATAPX=[];
	zeai.listEach('.navdiy_f',function(obj){
		DATAPX.push(obj.getAttribute("i"));
	});
	navdiy_px.value=DATAPX.join(",");
	zeai.confirm('确认要修改么？此修改将触发缓存机制同步更新，立即生效！',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		});
	});
}
</script>




<!--认证设置-->
<?php }elseif($t == 'rz'){if(!in_array('var_rz',$QXARR))exit(noauth());?>
    <style>
	.table.cols1 .tdL{width:160px}    
    </style>
	<table class="table size2 cols1" style="width:1111px;margin:0 0 0 20px">
	<tr><th colspan="4" align="left" style="border:0">认证项目DIY</th></tr>
	<tr>
		<td class="tdL tdLbgHUI">认证项目</td>
		<td class="tdR">
			<style>
            .stepbox .stepli{margin-top:5px}
            .stepbox .stepli li{text-align:center;width:80px;padding:10px;float:left;margin:5px 10px;border:#ddd 1px solid;cursor:move;background-color:#f8f8f8;border-radius:13px}
            .stepbox li i.ico{margin:10px 0;width:60px;font-size:40px;height:60px;line-height:60px;color:#fff;display:inline-block;background-color:#ddd;border-radius:5px;position:relative}
            .stepbox li.ed i.identity{background-color:<?php echo rz_data_info('identity','color');?>}
            .stepbox li.ed i.photo{background-color:<?php echo rz_data_info('photo','color');?>}
            .stepbox li.ed i.mob{background-color:<?php echo rz_data_info('mob','color');?>}
            .stepbox li.ed i.edu{background-color:<?php echo rz_data_info('edu','color');?>}
            .stepbox li.ed i.car{background-color:<?php echo rz_data_info('car','color');?>}
            .stepbox li.ed i.house{background-color:<?php echo rz_data_info('house','color');?>}
            .stepbox li.ed i.weixin{background-color:<?php echo rz_data_info('weixin','color');?>}
            .stepbox li.ed i.qq{background-color:<?php echo rz_data_info('qq','color');?>}
            .stepbox li.ed i.email{background-color:<?php echo rz_data_info('email','color');?>}
            .stepbox li.ed i.love{background-color:<?php echo rz_data_info('love','color');?>}
            .stepbox li.ed i.pay{background-color:<?php echo rz_data_info('pay','color');?>}
            </style>
            <dd class="stepbox" id="stepbox">
                <div class="stepli">
                    <?php
                    $rz_dataARR    = explode(',',$_ZEAI['rz_data']);
                    $rz_data_pxARR = explode(',',$_ZEAI['rz_data_px']);
                    if (count($rz_data_pxARR) >= 1 && is_array($rz_data_pxARR)){
                        foreach ($rz_data_pxARR as $k=>$V) {
                            ?>
                            <li>
                            <i class="ico <?php echo $V;?>"><?php echo rz_data_info($V,'ico');?></i>
                            <input type="checkbox" name="rz_data[]" id="rz_data_<?php echo $V;?>" class="checkskin rz_data" value="<?php echo $V;?>"<?php echo (in_array($V,$rz_dataARR))?' checked':'';?>><label for="rz_data_<?php echo $V;?>" class="checkskin-label"><i class="i1"></i><b class="W80"><?php echo rz_data_info($V,'title');?></b></label>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="clear"></div>
            </dd>
            <font class="S12 C999">可以按住不放拖动项目调整前后顺序，选中请打勾，选中将出现在认证页面并展示或点亮对应图标，否则不显示</font>
        </td>
	</tr>


    <tr><th colspan="4" align="left" style="border:0;height:20px"></th></tr>
	<tr>
		<th colspan="4" align="left" style="border:0">【实名认证】【真人认证】参数设置 <font class="S12 C999">如果上方认证项目没勾选则无需设置参数</font>
            <div id="rzhelp" class="helpC S14">
            登录<a href="http://platform.shumaidata.com/login" target="_blank" class="blue">天眼数据第三方平台</a>，如果没有帐号，先注册一个。<a href="http://platform.shumaidata.com/login" target="_blank" class="blue B">点击登录注册</a><br>
            --------------------------------------------------------------<br>
            1．注册成功登录进去，点击左侧菜单【用户中心】->【账户资料】可以看见 <font class="Cf00">appId</font>和<font class="Cf00">appSecurity</font>，把这两个复制填到择爱后台<br><br>
            2．点击左侧菜单【用户中心】->【账户充值】先充值100元试试效果，【运营商手机实名】0.27元/次，【人脸真人认证】0.35元/次<br><br>
            <font class="Cf00 B">申请帐号后请加【天眼数据】客服微信号：18600121500<br>加微信时备注：【择爱婚恋交友系统客户】然后把公司名称帐号等信息发送给对方，让他们改价格，即可享受超低价，否则默认价很高（大于0.27和0.35元））</font><br><br>
            3．点击左上角【天眼数据】的Logo图标进入首页，可以看到剩余次数和余额
            <br><br><br>
            </div>
            <font class="tips FR"><a href="http://platform.shumaidata.com/register" target="_blank" class="aQING"> 注册/登录/充值帐号</a></font>
	</th>
	</tr>
	<tr>
		<td class="tdL tdLbgHUI">appId</td>
		<td class="tdR"><input id="rz_appId" name="rz_appId" type="text" class="W400" maxlength="32" value="<?php echo $_SMS['rz_appId'];?>"><img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:rzhelp,title:'天眼数据第三方平台设置帮助',w:600,h:500});"></td>
	</tr>
	<tr>
		<td class="tdL tdLbgHUI">appSecurity</td>
		<td class="tdR"><input id="rz_appSecurity" name="rz_appSecurity" type="text" class="W400" maxlength="32" value="<?php echo $_SMS['rz_appSecurity'];?>"><img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:rzhelp,title:'天眼数据第三方平台设置帮助',w:600,h:500});"></td>
	</tr>

	<tr>
		<td class="tdL tdLbgHUI">运营商【实名认证】</td>
		<td class="tdR lineH150">
        <input type="checkbox" class="switch" name="rz_mobile3" id="rz_mobile3" value="1"<?php echo ($_SMS['rz_mobile3'] == 1)?' checked':'';?>><label for="rz_mobile3" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
        <div class=" S12 C999" style="margin-top:10px">
        1．上方认证项目【实名认证】勾选后并开启此开关，会员实名认证会根据“手机号+姓名+身份证号”自动进入“电信/移动/联通”数据库验证是否是同一个人<br>
        2．验证一致将自动点亮【实名认证】图标，并将姓名和身份证号自动更新入库<br>
        3．<font class="Cf00">上方认证项目【实名认证】勾选后并关闭此开关，将采用传统上传身份证形式上传至后台，进行人工审核</font>
        </div>
        </td>
	</tr>
	<tr>
		<td class="tdL ">人脸识别【真人认证】</td>
		<td class="tdR lineH150">
        <div class=" S12 C999">
        1．上方认证项目勾选后，会员认证会根据“上传的照片+姓名+身份证号”和公安库里面照片进行比对<br>
        2．验证一致将自动点亮对应【真人认证】图标，并将姓名和身份证号自动更新入库<br>
        </div>
        </td>
	</tr>
	<tr>
		<td class="tdL tdLbgHUI">会员认证费用</td>
		<td class="tdR lineH150">
              <input name="rz_price" id="rz_price" type="text" class="W80 FVerdana" maxlength="6" value="<?php echo floatval($_SMS['rz_price']);?>"> 元
              <div class=" S12 C999" style="margin-top:10px">
              设置了费用，认证费用由会员支付（推荐填1~10元，可以小赚），填0由官方支付（【运营商手机实名】0.27元/次，【人脸真人认证】0.35元/次）<br>
              <font class="Cf00">此认证费用只针对开启【(运营商)实名认证】和【真人认证】有效，会员认证此项目会先跳出支付页面，支付成功后认证</font>
              </div>
        </td>
	</tr>
    
	</table>
<br><br><br><br><br>
<input name="submitok" type="hidden" value="cache_rz">
<input name="rz_data_px" id="rz_data_px" type="hidden" value="<?php echo $rz_data_px;?>">
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
<script src="js/Sortable1.6.1.js"></script>
<script>
zeai.listEach('.rz_data',function(obj){
	var li=obj.parentNode;
	if(obj.checked){
		li.addClass('ed');
	}else{
		li.removeClass('ed');
	}
	obj.onclick = function(){
		var p=obj.parentNode;
		if(obj.checked){
			p.addClass('ed');
		}else{
			p.removeClass('ed');
		}
	}
});
function drag_init(){
	(function (){
		[].forEach.call(stepbox.getElementsByClassName('stepli'), function (el){
			Sortable.create(el, {
				group: 'zeai_rz',
				animation:150
			});
		});
	})();
}
drag_init();
save.onclick = function(){
	var DATAPX=[];
	zeai.listEach('.rz_data',function(obj){
		DATAPX.push(obj.value);
	});
	rz_data_px.value=DATAPX.join(",");
	zeai.confirm('确认要修改么？此修改将触发缓存机制同步更新，立即生效！',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		});
	});
}

</script>
<?php }?>
	<input name="uu" type="hidden" value="<?php echo $session_uid;?>">
	<input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
</form>
<script>
<?php if($t != 'rz' && $t != 'nav'){?>
save.onclick = function(){
	zeai.confirm('确认要修改么？此修改将触发缓存机制同步更新，立即生效！',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		});
	});
}
<?php }?>
/*系统基本设置*/
var uu=<?php echo $session_uid;?>,pp='<?php echo $session_pwd;?>';
if (!zeai.empty(o('logodel')))o('logodel').onclick = function(){delpic('cache_config_del_logo');}
if (!zeai.empty(o('waterimgdel')))o('waterimgdel').onclick = function(){delpic('cache_config_del_waterimg');}
if (!zeai.empty(o('subscribedel')))o('subscribedel').onclick = function(){delpic('cache_config_del_subscribe');}
if (!zeai.empty(o('pclogodel')))o('pclogodel').onclick = function(){delpic('cache_config_del_pclogo');}
if (!zeai.empty(o('m_ewmdel')))o('m_ewmdel').onclick = function(){delpic('cache_config_del_m_ewm');}
function pplimgDel(i){
	zeai.confirm('确认要删除么？',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:'pplimgDel',uu:uu,pp:pp,i:i}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}else{zeai.alert(rs.msg);}
		});
	});
}
function bannerDel(i){
	zeai.confirm('确认要删除么？',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:'bannerDel',uu:uu,pp:pp,i:i}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}else{zeai.alert(rs.msg);}
		});
	});
}
function delpic(submitok){
	zeai.confirm('确认要删除么？',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:submitok,uu:uu,pp:pp}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}else{zeai.alert(rs.msg);}
		});
	});
}
function MnavbtmDel(i){
	zeai.confirm('确认要删除么？',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:'Mnavbtm_icoDel',uu:uu,pp:pp,iarrstr:i}},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		});
	});
}
</script>
<br><br><br><br><br><br>
<?php require_once 'bottomadm.php';?>
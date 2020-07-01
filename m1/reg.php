<?php
if (ini_get('session.auto_start') == 0)session_start();
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
if (!is_mobile())exit('请用手机打开');
if(!in_array('tg',$navarr) && !in_array('shop',$navarr))header("Location: reg_diy.php?subscribe=$subscribe&tguid=$tguid&ifback=1");
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'sub/www_zeai_cn_sms.php';
require_once ZEAI.'cache/config_wxgzh.php';
$TG_set = json_decode($_REG['TG_set'],true);
$headertitle = '用户注册-';$nav = '';
$kf_tel=dataIO($_ZEAI['kf_tel'],'out');$kf_mob=dataIO($_ZEAI['kf_mob'],'out');$kf_qq=dataIO($_ZEAI['kf_qq'],'out');$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');

switch ($submitok) {
	case 'ajax_getverify':
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}else{
			if (!$db->ROW(__TBL_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)"))json_exit(array('flag'=>0,'msg'=>'此手机号未注册或验证，请换其它'));
		}
		if (($Temp_regyzmrenum > $_SMS['sms_yzmnum']) && $_SMS['sms_yzmnum']>0  )json_exit(array('flag'=>0,'msg'=>'你重复发送次数过多，请明天再试'));
		$_SESSION['Zeai_cn__mobyzm'] = cdstr(4);
		//sms
		$rtn = Zeai_sendsms_authcode($mob,$_SESSION['Zeai_cn__mobyzm']);
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
	break;
	case 'login_forgetpass_update':
		if(!ifmob($mob)){
			json_exit(array('flag'=>0,'msg'=>'手机号码不正确'));
		}else{
			$row = $db->ROW(__TBL_USER__,"id,nickname","mob='$mob' AND FIND_IN_SET('mob',RZ)","num");
			if ($row){
				$uid=$row[0];
				setcookie("cook_nickname",dataIO($row[1],'out'),time()+720000,"/",$_ZEAI['CookDomain']);  
			}else{
				json_exit(array('flag'=>0,'msg'=>'此手机号未注册或验证，请换其它'));				
			}
		}
		$verify = intval($verify);
		if (empty($_SESSION['Zeai_cn__mobyzm'])){
			json_exit(array('flag'=>0,'msg'=>'短信验证码错误，请重新获取'));
		}else{
			if ($_SESSION['Zeai_cn__mobyzm'] != $verify){
				json_exit(array('flag'=>0,'msg'=>'短信验证码不正确'));
			}
			if ($_SESSION['Zeai_cn__mob'] != $mob){
				unset($_SESSION["Zeai_cn__mob"]);
				json_exit(array('flag'=>0,'msg'=>'手机号码异常，请重新获取'));
			}
		}
		$newpass = cdstr(6);
		//sms
		$rtn = Zeai_sendsms_authcode($mob,$newpass,'findpass');
		if ($rtn == 0){
			$chkflag = 1;
			$newpass2 = md5($newpass);
			$db->query("UPDATE ".__TBL_USER__." SET pwd='$newpass2' WHERE mob=".$mob);
			$msg='新密码已发送至您的手机'.$mob;
		}else{
			$chkflag = 0;
			$msg = "发送失败,错误码：$rtn";
		}
		//sms end
		json_exit(array('flag'=>$chkflag,'msg'=>$msg));
	break;
	case 'login_forgetpass':
        $mini_title = '<i class="ico goback" id="ZEAIGOBACK-login_forgetpass">&#xe602;</i>忘记密码';
        $mini_class = 'top_mini top_miniBAI';
        $mini_backT = '返回';
        
        require_once ZEAI.'m1/top_mini.php';?>
        <link href="<?php echo HOST;?>/m1/css/tg_loginreg.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />

        <style>
		.submainX{width:100%;max-width:640px;bottom:0;overflow:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;box-sizing:border-box;position:absolute;top:44px;text-align:left}
		.submainX{background-color:#fff;padding:30px;line-height:200%;font-size:16px}
        .submainX{padding:0}
        .submainX dl dt{width:30%;float:left}
        .submainX dl dd{width:70%;float:right}
        .submainX .tg_mobcert_str{text-align:center}
        .submainX .tg_mobcert_str div{border:0;font-size:18px}
        </style>
        <div class="submainX tgreg">
            <form id="WWW__ZEAI_CN_form" style="margin:20px;padding-top:0">
                <div class="tg_mobcert_str"><?php echo $rz_str;?></div>
                <dl style="padding-top:0px;margin-top:0"><dt><i class="ico">&#xe627;</i></dt><dd><input name="mob" type="text" class="input_login" id="mob" placeholder="请输入手机号码" autocomplete="off" maxlength="11" pattern="[0-9]*"></dd></dl>
                <dl><dt><i class="ico">&#xe6c3;</i></dt><dd class="yzmF">
                <input name="verify" id="verify" type="text" required class="input_login" maxlength="4" placeholder="输入手机短信验证码" autocomplete="off" /><a href="javascript:;" class="yzmbtn" id="yzmbtn">获取验证码</a>
                </dd></dl>
                <button type="button" class="btn size4 <?php echo ($_ZEAI['mob_mbkind']==3)?'HONG2':'HONG';?> B yuan" onClick="my_info_save();" style="width:80%;left:10%">开始修改</button>
            </form>
        </div>
        <script>
            if (!zeai.empty(o('yzmbtn'))){
                yzmbtn.onclick = function(){
                    if (zeai.ifmob(o('mob').value)){
                        if (!this.hasClass('disabled')){
                            yzmbtn.addClass('disabled');
                            zeai.ajax({'url':HOST+'/m1/reg'+zeai.extname,'data':{'submitok':'ajax_getverify',mob:o('mob').value}},function(e){
                                var rs=zeai.jsoneval(e);
                                if (rs.flag == 1){
                                    zeai.msg(rs.msg,{time:5});
                                    o('verify').value='';
                                    yzmtimeFn(120);
                                }else{
                                    zeai.msg(rs.msg,mob);
                                    yzmbtn.removeClass('disabled');
                                }
                            });
                        }
                    }else{
                        zeai.msg('请输入手机号码',mob);
                        return false;
                    }
                }
            }
			function yzmtimeFn(countdown) { 
				if (countdown == 0) {
					yzmbtn.removeClass('disabled');
					yzmbtn.html('<font>重新获取</font>'); 
					return false;
				} else { 
					if (!zeai.empty(o('yzmbtn'))){
						yzmbtn.addClass('disabled');
						yzmbtn.html('<b>'+countdown + "S</b>后重新发送"); 
						countdown--; 
					}
				} 
				cleandsj=setTimeout(function(){yzmtimeFn(countdown)},1000);
			}
            function my_info_save(){
                var mobV = o('mob').value,verifyV=o('verify').value;
                if(!zeai.ifmob(mobV)){zeai.msg('请输入正确手机号',o('mob'));return false;}
                if(!zeai.ifint(verifyV) || zeai.str_len(verifyV)!=4 ){zeai.msg('请输入【手机验证码】',o('verify'));return false;}
                zeai.ajax({url:HOST+'/m1/reg'+zeai.ajxext+'submitok=login_forgetpass_update',form:WWW__ZEAI_CN_form},function(e){rs=zeai.jsoneval(e);
                    zeai.msg(0);zeai.msg(rs.msg);
                    if(rs.flag==1)setTimeout(function(){o('ZEAIGOBACK-login_forgetpass').click();},1000);
                });
            }
        </script>
		<?php
		exit;
	break;
}
require_once ZEAI.'m1/header.php';?>
<style>
body{background-color:#fff}
.reg h1{width:77%;font-size:30px;margin:60px auto 0px auto;font-weight:bold;position:relative}
.reg .size4{width:77%;display:block;height:55px;font-size:16px;line-height:55px;border-radius:30px;margin:30px auto;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;border:#E83191 1px solid;background-color:#fff;color:#E83191}
.reg .ed{background-color:#E83191;border-color:#E83191;color:#fff}
<?php if($_ZEAI['mob_mbkind'] == 3){?>
.reg .size4{border-color:#FF6F6F;color:#FF6F6F;}
.reg .ed{background-color:#FF6F6F;border-color:#FF6F6F;color:#fff}
<?php }?>
.reg h5{font-size:14px;text-align:center}
.reg h2{margin-top:10px;font-size:18px;color:#F7564D}
.reg h5{margin-top:0px;font-size:18px;}
.reg .kefu{margin-top:50px;margin-bottom:50px}
.reg .kefu img{width:25%;margin:10px auto;display:block;padding:3px;border:#eee 1px solid}
.reg .kefu font{color:#999}
.reg .kefu a{margin-top:10px;display:block;color:#666}
.reg .kefu .ico{margin-right:4px;}
.linebox .W50:before{left:10%;width:80%}
.linebox .line:before{background-color:#ccc}
</style>
<div class="reg fadeInL">
    <h1>欢迎您加入</h1>
    <h5><?php echo $_ZEAI['siteName'];?></h5>
    <div class="linebox" style="margin-top:25px"><div class="line W50"></div><div class="title BAI C666">请选择您要注册的身份</div></div>
    <a href="<?php echo ($_REG['reg_style']==1)?'reg_alone':'reg_diy';?>.php?subscribe=<?php echo $subscribe;?>&tguid=<?php echo $tguid;?>&ifback=1" class="btn size4 ed">我单身，给自己找个对象</a>
    <?php if(@in_array('tg',$navarr)){?><a href="tg_reg.php?subscribe=<?php echo $subscribe;?>&tguid=<?php echo $tguid;?>&ifback=1&k=1&jumpurl=<?php echo urlencode($jumpurl);?>" class="btn size4">当红娘，成人之美赚佣金</a><?php }?>
    <?php if(@in_array('shop',$navarr)){?>
    <a href="tg_reg.php?subscribe=<?php echo $subscribe;?>&tguid=<?php echo $tguid;?>&ifback=1&k=2&jumpurl=<?php echo urlencode($jumpurl);?>" class="btn size4">我是商家，想开店卖东西</a>
	<a href="tg_reg.php?subscribe=<?php echo $subscribe;?>&tguid=<?php echo $tguid;?>&ifback=1&k=3&jumpurl=<?php echo urlencode($jumpurl);?>" class="btn size4">我是买家，想购买商品</a>
	<?php }?>
    <div class="kefu">
    <?php if (1==2){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>" style="display:none"><font>长按二维码加客服微信</font><br><?php }//!empty($kf_wxpic)?>
    <img src="<?php echo $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm'];?>"><font>长按二维码关注公众号</font><br>
    <?php if (!empty($kf_tel)){?>
        <a href="tel:<?php echo $kf_tel;?>" class="C999"><i class="ico">&#xe60e;</i><?php echo $kf_tel;?></a><br>
    <?php }else{?>
        <?php if (!empty($kf_mob)){?><i class="ico">&#xe60e;</i><a href="tel:<?php echo $kf_mob;?>" class="C999"><?php echo $kf_mob;?></a><?php }?>
    <?php }?>
    </div>
</div>
</body></html>
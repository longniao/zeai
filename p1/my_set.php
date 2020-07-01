<?php
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$currfields = "mob_ifshow,qq_ifshow,weixin_ifshow,weixin_pic_ifshow,email_ifshow,openid,unionid,subscribe,regkind,loginkey,nickname";
require_once 'my_chkuser.php';
$data_mob_ifshow        = $row['mob_ifshow'];
$data_qq_ifshow         = $row['qq_ifshow'];
$data_weixin_ifshow     = $row['weixin_ifshow'];
$data_weixin_pic_ifshow = $row['weixin_pic_ifshow'];
$data_email_ifshow      = $row['email_ifshow'];
$data_openid            = $row['openid'];
$data_unionid           = $row['unionid'];
$data_subscribe         = $row['subscribe'];
$data_regkind           = $row['regkind'];
$data_loginkey          = $row['loginkey'];
$data_nickname  = dataIO($row['nickname'],'out');

require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_vip.php';

if($submitok == 'ajax_set'){
	if(empty($objname))exit(JSON_ERROR);
	if($v==1){
		$v_str='设置成功';
	}else{
		$v=0;
		$v_str='设置成功';
	}
	switch ($objname) {
		case 'mob_ifshow':$sql="mob_ifshow=".$v;break;
		case 'qq_ifshow':$sql="qq_ifshow=".$v;break;
		case 'weixin_ifshow':$sql="weixin_ifshow=".$v;break;
		case 'weixin_pic_ifshow':$sql="weixin_pic_ifshow=".$v;break;
		case 'email_ifshow':$sql="email_ifshow=".$v;break;
	}
	$db->query("UPDATE ".__TBL_USER__." SET ".$sql." WHERE id=".$cook_uid);
	json_exit(array('flag'=>1,'msg'=>$v_str));
}elseif($submitok == 'ajax_binding_openid'){	
	if (str_len($data_openid) >10){
		json_exit(array('flag'=>1,'msg'=>'已成功绑定【'.$data_nickname.'(ID：'.$cook_uid.')】'));
	}
	json_exit(array('flag'=>0));
}elseif($submitok == 'ajax_bind_cancel_wx'){	
	$db->query("UPDATE ".__TBL_USER__." SET openid='' WHERE id=".$cook_uid);
	json_exit(array('flag'=>1,'msg'=>'解除成功'));
}elseif($submitok == 'ajax_binding_gzh_cancel'){	
	if ($data_subscribe==1){
		json_exit(array('flag'=>0,'msg'=>'请用手机登录公众号进行取消关注即可'));
	}else{
		$db->query("UPDATE ".__TBL_USER__." SET subscribe=2 WHERE id=".$cook_uid);
		json_exit(array('flag'=>1,'msg'=>'解除成功'));
	}
}elseif($submitok == 'ajax_qq_bind_cancel'){	
	$db->query("UPDATE ".__TBL_USER__." SET loginkey='' WHERE id=".$cook_uid);
	json_exit(array('flag'=>1,'msg'=>'解除成功'));
}elseif($submitok == 'ajax_binding_gzh'){	
	if (str_len($data_openid) >10 && $data_subscribe==1){
		json_exit(array('flag'=>1,'msg'=>'已成功绑定【'.$data_nickname.'(ID：'.$cook_uid.')】'));
	}
	json_exit(array('flag'=>0));
}elseif($submitok == 'passmodupdate'){
	if (str_len($form_password1)<6 || str_len($form_password1)>20)json_exit(array('flag'=>0,'msg'=>'“新密码”请在20字节以内。'));
	if (str_len($form_password2)<6 || str_len($form_password2)>20)json_exit(array('flag'=>0,'msg'=>'“确认新密码”请在20字节以内。'));
	if ($form_password1 <> $form_password2)json_exit(array('flag'=>0,'msg'=>'两次密码输入不一样，请重试！'));
	$password = trimm($form_password1);
	$password = md5($password);
	$old_password = md5($old_password);
	$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$cook_uid." AND pwd='$old_password'");
	if(!$db->num_rows($rt))json_exit(array('flag'=>0,'msg'=>'旧密码验证错误，修改失败！'));
	$db->query("UPDATE ".__TBL_USER__." SET pwd='$password' WHERE id=".$cook_uid);
	setcookie("cook_pwd",$password,time()+720000,"/",$_ZEAI['CookDomain']);
	json_exit(array('flag'=>1,'msg'=>'修改成功！'));
}
$t = (ifint($t,'1-3','1'))?$t:1;
switch ($t) {
	case 1:$zeai_cn_menu  = 'my_set1';$title='帐号设置';break;
	case 2:$zeai_cn_menu  = 'my_set2';$title='隐私设置';break;
	case 3:$zeai_cn_menu  = 'my_set3';$title='密码设置';break;
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $title;?> - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<style>
.tableli{width:100%;border-collapse:collapse;text-align:left}
.tableli tr td:first-child{padding-left:15px}
.tableli tr td:last-child{padding-right:15px}
.tableli td{padding:30px 0;word-break:break-all;word-wrap:break-word;border-bottom:#f5f5f5 1px solid}
.tableli td h2{font-size:16px;}
.myRC .formdl{width:520px;margin-top:100px}
.myRC .formdl dl dt{width:80px;margin-left:0px}
.myRC .formdl dl dd{width:320px}
.myRC .wxico,.myRC .qqico{width:60px;height:60px;line-height:60px;border-radius:30px;font-size:36px;color:#fff;text-align:center}
.myRC .wxico{background-color:#31C93C}
.myRC .qqico{background-color:#51B7EC}
/*关注*/
.my-subscribe_box{padding:15px 15px 30px 15px;background-color:#fff;border-radius:12px;display:none}
.my-subscribe_box img{width:240px;height:240px;padding:2px;border:#eee 1px solid;display:block;margin:20px auto}
.my-subscribe_box h3{display:inline-block;line-height:20px;font-size:14px;color:#999;margin-top:0px}
</style>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>设置</h1>
        <div class="tab">
            <a href="<?php echo SELF;?>?t=1"<?php echo ($t==1)?' class="ed"':'';?>>帐号设置</a>
          <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>隐私设置</a>
            <a href="<?php echo SELF;?>?t=3"<?php echo ($t==3)?' class="ed"':'';?>>密码设置</a>
        </div>
         <!-- start C -->
        <div class="myRC">
        	<?php if ($t == 1){?>
                <table class="tableli">
                <tr>
                <td width="100"><i class='ico wxico'>&#xe607;</i></td>
                <td width="100"><h2>微信公众号</h2></td>
                <td align="left" class="S12 C999">绑定后，可以用手机微信登录/公众号登录/电脑端扫码登录，个人资料多端同步</td>
                <td width="90" align="right">
                <?php if ($data_subscribe==1){?>
                    <button type="button" class="hong" onClick="zeaiBindWeixin(<?php echo $cook_uid;?>,'gzh_cancel');">解除绑定</button>
				<?php }else{ ?>
                    <button type="button" class="honged" onClick="zeaiBindWeixin(<?php echo $cook_uid;?>,'gzh_bind');">立即绑定</button>
                <?php }?>
                </td>
                </tr>
                
                <?php if ($_REG['reg_3login_wx'] == 1){?>
                <tr>
                <td width="100"><i class='ico wxico'>&#xe607;</i></td>
                <td width="100"><h2>微信登录</h2></td>
                <td align="left" class="S12 C999">绑定后，可以用手机微信登录/公众号登录/电脑端扫码登录，个人资料多端同步</td>
                <td width="90" align="right">
                <?php if (!empty($data_openid)){?>
                    <button type="button" class="hong" onClick="zeaiBindWeixin(<?php echo $cook_uid;?>,'cancel');">解除绑定</button>
				<?php }else{ ?>
                    <button type="button" class="honged" onClick="zeaiBindWeixin(<?php echo $cook_uid;?>,'bind');">立即绑定</button>
                <?php }?>
                </td>
                </tr>
                <?php }?>
                <?php if ($_REG['reg_3login_qq'] == 1){?>
                <tr>
                  <td width="100"><i class='ico qqico'>&#xe612;</i></td>
                  <td width="100"><h2>QQ登录</h2></td>
                <td align="left" class="S12 C999">绑定后，可以用手机QQ登录/电脑端QQ直接登录，个人资料多端同步</td>
                <td width="90" align="right">
                <?php if (!empty($data_loginkey)){?>
               		<button type="button" class="hong" onClick="zeaiBindQQ('cancel');">解除绑定</button>
				<?php }else{ ?>
                    <button type="button" class="honged" onClick="zeaiBindQQ('bind');">立即绑定</button>
                <?php }?>
                </td>
                </tr>
                <?php }?>
                
                </table>
            <?php }elseif($t == 2){?>
				<table class="tableli">
                <tr>
                <td width="100"><h2>手机号码</h2></td>
                <td align="left" class="S12 C999">设置保密/公开，公开后，其他会员可以联系你，保密后将隐藏　　<a class="btn size1 BAI" href="my_info.php?t=5">修改</a></td>
                <td width="90" align="right"><input type="checkbox" name="mob_ifshow" id="mob_ifshow" class="switch" value="1"<?php echo ($data_mob_ifshow == 1)?' checked':'';?>><label for="mob_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></td>
            	</tr>
                
                <?php if ($_VIP['hideprivacy'] == 1){?>
                <tr>
                    <td width="100"><h2>微信号</h2></td>
                    <td align="left" class="S12 C999">设置保密/公开，公开后，其他会员可以联系你，保密后将隐藏　　<a class="btn size1 BAI" href="my_info.php?t=5">修改</a></td>
                    <td width="90" align="right"><input type="checkbox" name="weixin_ifshow" id="weixin_ifshow" class="switch" value="1"<?php echo ($data_weixin_ifshow == 1)?' checked':'';?>><label for="weixin_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></td>
                </tr>
                <td width="100"><h2>微信二维码</h2></td>
                <td align="left" class="S12 C999">设置保密/公开，公开后，其他会员可以联系你，保密后将隐藏　　<a class="btn size1 BAI" href="my_info.php?t=5">修改</a></td>
                <td width="90" align="right"><input type="checkbox" name="weixin_pic_ifshow" id="weixin_pic_ifshow" class="switch" value="1"<?php echo ($data_weixin_pic_ifshow == 1)?' checked':'';?>><label for="weixin_pic_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></td>
                </tr>
                <tr>
                <td width="100"><h2>QQ</h2></td>
                <td align="left" class="S12 C999">设置保密/公开，公开后，其他会员可以联系你，保密后将隐藏　　<a class="btn size1 BAI" href="my_info.php?t=5">修改</a></td>
                <td width="90" align="right"><input type="checkbox" name="qq_ifshow" id="qq_ifshow" class="switch" value="1"<?php echo ($data_qq_ifshow == 1)?' checked':'';?>><label for="qq_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></td>
                </tr>
                <tr>
                <td width="100"><h2>邮箱</h2></td>
                <td align="left" class="S12 C999">设置保密/公开，公开后，其他会员可以联系你，保密后将隐藏　　<a class="btn size1 BAI" href="my_info.php?t=5">修改</a></td>
                <td width="90" align="right"><input type="checkbox" name="email_ifshow" id="email_ifshow" class="switch" value="1"<?php echo ($data_email_ifshow== 1)?' checked':'';?>><label for="email_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></td>
                 </tr>
                 <?php }?>
         	 </table>
            <?php }elseif($t == 3){?>
            	 <form id="wwwZeaicnV6Form" class="formdl">
                <dl><dt>旧密码</dt><dd><input name="old_password" type="password" class="input W300" id="old_password"  maxlength="20" placeholder="请输入原密码" autocomplete="off"></dd></dl>
                <dl><dt>新密码</dt><dd><input name="form_password1" type="password" class="input W300" id="form_password1"  maxlength="20" placeholder="请输入6-16个字符密码" autocomplete="off"></dd></dl>
                <dl><dt>确认密码</dt><dd><input name="form_password2" type="password" class="input W300" id="form_password2"  maxlength="20" placeholder="请再次输入新密码" autocomplete="off"></dd></dl>
                <dl><dt>&nbsp;</dt><dd><button type="button" class="btn size4 HONG" onclick="my_set_modpass()">修改并保存</button></dd></dl>
                <input name="submitok" type="hidden" value="passmodupdate" />
          </form>
            <?php }?>
        </div>
        <!-- end C -->
</div></div></div>

<div id="weixin_login_ewm_box" class="my-subscribe_box"><img id="Z__e_A___I_c____N">
<h3>请用微信扫码进行帐号绑定<br>绑定成功后下次就可以直接扫码登录啦</h3>
</div>

<?php if ($data_subscribe!=1 ){?>
<div id="subscribe_box_my_set" class="my-subscribe_box"><img id="Z_e___A___I__c___N">
<h3>请用微信扫码关注公众号进行帐号绑定<br>绑定后就可以微信扫码登录和接收消息通知啦</h3>
</div>
<?php }?>
<script src="js/my_set.js"></script>
<?php if (str_len($data_loginkey)>15 && $flag == 1){?>
<script>zeai.msg('QQ绑定成功');</script>
<?php }?>
<?php require_once ZEAI.'p1/bottom.php';?>
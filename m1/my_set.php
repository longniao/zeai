<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
$currfields = "mob_ifshow,qq_ifshow,weixin_ifshow,weixin_pic_ifshow,email_ifshow,flag,photo_ifshow,ifViewPush";//
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=my_set';require_once ZEAI.'my_chk_u.php';
$data_mob_ifshow        = $row['mob_ifshow'];
$data_qq_ifshow         = $row['qq_ifshow'];
$data_weixin_ifshow     = $row['weixin_ifshow'];
$data_weixin_pic_ifshow = $row['weixin_pic_ifshow'];
$data_email_ifshow      = $row['email_ifshow'];
$data_flag = $row['flag'];
$data_photo_ifshow = $row['photo_ifshow'];
$data_ifViewPush = $row['ifViewPush'];//
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_vip.php';
switch ($submitok) {
	case 'ajax_set':
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
	break;
	//修改密码
	case 'modpass':
		$mini_title = '<i class="ico goback" id="ZEAIGOBACK-my_set_modpass">&#xe602;</i>修改密码';
		$mini_backT = '返回';
		require_once ZEAI.'m1/top_mini.php';
		?>
        <div class="submain" id="my_set_modpass">
        <form id="ZEAI_form" class="lxbox">
			<style>
			.lxbox {background-color:#fff;padding-bottom:20px;margin-bottom:40px}
			.lxbox dl{width:90%;border-bottom:#eee 1px solid;padding:10px 5% 15px 5%}
			.lxbox dl dt,.lxbox dl dd{width:100%;line-height:30px;text-align:left}
			.lxbox dl dt{line-height:24px;}
			.lxbox .input{width:70%}
			.lxbox .input.W100_{width:100%}
			.btn2{width:90%;height:44px;line-height:44px;margin-top:15px;border-radius:2px}
			body{position:absolute}
			</style>
            <dl><dt>输入旧密码</dt><dd><input name="old_password" type="password" class="input W100_"   id="old_password" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()"></dd></dl>
            <dl><dt>输入新密码</dt><dd><input name="form_password1" type="password" class="input W100_" id="form_password1" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()" /></dd></dl>
            <dl><dt>确认新密码</dt><dd><input name="form_password2" type="password" class="input W100_" id="form_password2" autocomplete="off" maxlength="20" onBlur="my_set_modpasstop()"></dd></dl>
            <input name="submitok" type="hidden" value="passmodupdate" /><br>
            <input class="btn size4 HONG center W90_" type="button" value="保存并修改"  onclick="my_set_modpass()" />
        </form>
        <script>
			function my_set_modpass(){
				if(zeai.empty(o('old_password').value) || zeai.str_len(o('old_password').value)<6){
					zeai.msg('请输入旧密码6~20个字节内');
					return false;
				}
				if(zeai.empty(o('form_password1').value)){
					zeai.msg('请输入新密码6~20个字节内！');
					return false;
				}
				if(zeai.str_len(o('form_password1').value)>20 || zeai.str_len(o('form_password1').value)<6){
					zeai.msg('新密码请控制在6~20个字节内！');
					return false;
				}
				if(zeai.empty(o('form_password2').value)){
					zeai.msg('请再输入一次新密码');
					return false;
				}
				if(zeai.str_len(o('form_password2').value)>20 || zeai.str_len(o('form_password2').value)<6){
					zeai.msg('新密码请在6~20个字节内！');
					return false;
				}
				if(o('form_password1').value!=o('form_password2').value) {
					zeai.msg('两次密码不一致');
					return false;		
				}
				zeai.ajax({url:'m1/my_set.php',form:ZEAI_form},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1)ZeaiM.page.jump('main');
					zeai.msg(rs.msg);
				});
			}
			function my_set_modpasstop(){zeai.setScrollTop(0);}
        </script>
        </div>
	<?php 
	exit;break;case 'passmodupdate':
		if (str_len($form_password1)<6 || str_len($form_password1)>20)json_exit(array('flag'=>0,'msg'=>'“新密码”请在20字节以内。'));
		if (str_len($form_password2)<6 || str_len($form_password2)>20)json_exit(array('flag'=>0,'msg'=>'“确认新密码”请在20字节以内。'));
		if ($form_password1 <> $form_password2)json_exit(array('flag'=>0,'msg'=>'两次密码输入不一样，请重试！'));
		$password = trimm($form_password1);
		$password = md5($password);
		$old_password = md5($old_password);
		$rt = $db->query("SELECT id FROM ".__TBL_USER__." WHERE id=".$cook_uid." AND pwd='$old_password'");
		if(!$db->num_rows($rt))json_exit(array('flag'=>0,'msg'=>'旧密码验证错误，提交失败！'));
		$db->query("UPDATE ".__TBL_USER__." SET pwd='$password' WHERE id=".$cook_uid);
		setcookie("cook_pwd",$password,time()+720000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'修改成功！'));
	break;
	exit;break;case 'my_delmy':
		if($_VIP['hidedel']!=1)json_exit(array('flag'=>1,'msg'=>'zeai_cn__forbidden'));
		ZEclearcookAI_CN();
		$uid=$cook_uid;
		$db->query("UPDATE ".__TBL_USER__." SET flag=-1 WHERE id=".$uid);
		ZEclearcookAI_CN();
		json_exit(array('flag'=>1,'msg'=>'注销成功，下次有空来坐坐^_^'));
		$db->query("DELETE FROM ".__TBL_TIP__." WHERE uid=".$uid." OR senduid=".$uid);
		$db->query("DELETE FROM ".__TBL_LOVEB_LIST__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_MONEY_LIST__." WHERE uid=".$uid);
		//$db->query("DELETE FROM ".__TBL_PAY__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_WXENDURL__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_HONGBAO__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_HONGBAO_USER__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_UCOUNT__." WHERE uid=".$uid);
		//
		$db->query("DELETE FROM ".__TBL_HN_BBS__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_CRM_HT__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_CRM_MATCH__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_CRM_MATCH__." WHERE uid2=".$uid);
		$db->query("DELETE FROM ".__TBL_CRM_FAV__." WHERE uid=".$uid);
		//删无图
		$db->query("DELETE FROM ".__TBL_CLICKHISTORY__." WHERE senduid=".$uid);
		$db->query("DELETE FROM ".__TBL_DATING__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_DATING_USER__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_GIFT_USER__." WHERE uid=".$uid." OR senduid=".$uid);
		$db->query("DELETE FROM ".__TBL_GZ__." WHERE uid=".$uid." OR senduid=".$uid);
		$db->query("DELETE FROM ".__TBL_LOVEB_LIST__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_MONEY_LIST__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_MSG__." WHERE uid=".$uid." OR senduid=".$uid);
		$db->query("DELETE FROM ".__TBL_PARTY_USER__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_PARTY_BBS__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_315__." WHERE uid=".$uid);
		//活动评论，签到
		$db->query("DELETE FROM ".__TBL_PARTY_USER__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_PARTY_BBS__." WHERE uid=".$uid);
		$db->query("DELETE FROM ".__TBL_PARTY_SIGN__." WHERE uid=".$uid);
		//视频
		$rt = $db->query("SELECT path_s FROM ".__TBL_VIDEO__." WHERE uid=".$uid);
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt);
				if(!$rows) break;
				$path_s = $rows[0];
				$path_b = str_replace(".jpg",".mp4",$path_s);
				up_send_userdel($path_s.'|'.$path_b);
			}
			$db->query("DELETE FROM ".__TBL_VIDEO__." WHERE uid=".$uid);
		}
		//相册
		$rt=$db->query("SELECT path_s FROM ".__TBL_PHOTO__." WHERE uid=".$uid);
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt);
				if(!$rows) break;
				$path_s = $rows[0];
				$path_b = smb($path_s,'b');
				up_send_userdel($path_s.'|'.$path_b);
			}
			$db->query("DELETE FROM ".__TBL_PHOTO__." WHERE uid=".$uid);
		}
		//认证
		$rt=$db->query("SELECT path_b,path_b2 FROM ".__TBL_RZ__." WHERE uid=".$uid);
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt);
				if(!$rows) break;
				$path_b = $rows[0];$path_b2 = $rows[1];
				up_send_userdel($path_b.'|'.$path_b2);
			}
			$db->query("DELETE FROM ".__TBL_RZ__." WHERE uid=".$uid);
		}
		//动态
		$rt=$db->query("SELECT id,piclist FROM ".__TBL_TREND__." WHERE uid=".$uid);
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt);
				if(!$rows) break;
				$fid = $rows[0];$piclist = $rows[1];
				if (!empty($piclist)){
					$piclist = explode(',',$piclist);
					if (count($piclist) >= 1){
						foreach ($piclist as $value){
							$path_s = $value;
							$path_b = smb($path_s,'b');
							up_send_userdel($path_s.'|'.$path_b);
						}
					}
				}
				$db->query("DELETE FROM ".__TBL_TREND_BBS__." WHERE fid=".$fid);
			}
			$db->query("DELETE FROM ".__TBL_TREND__." WHERE uid=".$uid);
			$db->query("DELETE FROM ".__TBL_TREND_BBS__." WHERE uid=".$uid);
		}
		//group
		$db->query("DELETE FROM ".__TBL_GROUP_USER__." WHERE userid=".$uid);
		$db->query("DELETE FROM ".__TBL_GROUP_BK__." WHERE userid=".$uid);
		$db->query("DELETE FROM ".__TBL_GROUP_WZ__." WHERE userid=".$uid);
		$db->query("DELETE FROM ".__TBL_GROUP_WZ_BBS__." WHERE userid=".$uid);
		$db->query("DELETE FROM ".__TBL_GROUP_CLUB_BBS__." WHERE userid=".$uid);
		$db->query("DELETE FROM ".__TBL_GROUP_CLUB_USER__." WHERE userid=".$uid);
		$rt = $db->query("SELECT id,picurl_s FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$uid);
		if($db->num_rows($rt)){
			$rows = $db->fetch_array($rt);
			$mainid = $rows[0];$photo_s = $rows[1];
			@up_send_userdel($photo_s);
			$db->query("DELETE FROM ".__TBL_GROUP_PHOTO__." WHERE mainid=".$mainid);
			$db->query("DELETE FROM ".__TBL_GROUP_PHOTO_KIND__." WHERE mainid=".$mainid);
			$db->query("DELETE FROM ".__TBL_GROUP_CLUB__." WHERE mainid=".$mainid);
			$db->query("DELETE FROM ".__TBL_GROUP_CLUB_PHOTO__." WHERE mainid=".$mainid);
			$db->query("DELETE FROM ".__TBL_GROUP_LINKS__." WHERE mainid=".$mainid);
		}
		$db->query("DELETE FROM ".__TBL_GROUP_MAIN__." WHERE userid=".$uid);
		//主
		$rt = $db->query("SELECT photo_s,tgpic FROM ".__TBL_USER__." WHERE id=".$uid);
		if($db->num_rows($rt)){
			$rows = $db->fetch_array($rt);
			$photo_s = $rows[0];$tgpic = $rows[1];
			$photo_m = getpath_smb($photo_s,'m');
			$photo_b = getpath_smb($photo_s,'b');
			$photo_blur = getpath_smb($photo_s,'blur');
			@up_send_userdel($photo_s.'|'.$photo_m.'|'.$photo_b.'|'.$photo_blur.'|'.$tgpic);
		}
		$db->query("DELETE FROM ".__TBL_USER__." WHERE id=".$uid);
		//
		ZEclearcookAI_CN();
		json_exit(array('flag'=>1,'msg'=>'注销成功，下次有空来坐坐^_^'));
	break;
	exit;break;case 'my_hidemy':
		if($_VIP['hidedata']!=1)json_exit(array('flag'=>1,'msg'=>'zeai_cn__forbidden'));
		if($data_flag==1){
			$db->query("UPDATE ".__TBL_USER__." SET flag=-2 WHERE id=".$cook_uid);
			json_exit(array('flag'=>1,'msg'=>'个人资料隐藏成功，会员列表将不显示'));
		}else{
			$db->query("UPDATE ".__TBL_USER__." SET flag=1 WHERE id=".$cook_uid);
			json_exit(array('flag'=>1,'msg'=>'开启成功，个人资料显示恢复正常'));
		}
		//ZEclearcookAI_CN();
	break;
	exit;break;case 'my_hidephoto':
		if($_VIP['hidephoto']!=1)json_exit(array('flag'=>1,'msg'=>'zeai_cn__forbidden'));
		if($data_photo_ifshow==1){
			$db->query("UPDATE ".__TBL_USER__." SET photo_ifshow=0 WHERE id=".$cook_uid);
		}else{
			$db->query("UPDATE ".__TBL_USER__." SET photo_ifshow=1 WHERE id=".$cook_uid);
		}
		json_exit(array('flag'=>1,'msg'=>'设置成功'));
	break;
	exit;break;case 'ifViewPush_update':
		if($data_ifViewPush==1){
			$db->query("UPDATE ".__TBL_USER__." SET ifViewPush=0 WHERE id=".$cook_uid);
			json_exit(array('flag'=>1,'msg'=>'关闭成功'));
		}else{
			$db->query("UPDATE ".__TBL_USER__." SET ifViewPush=1 WHERE id=".$cook_uid);
			json_exit(array('flag'=>1,'msg'=>'开启成功'));
		}
	break;
}
?>
<style>
.modlist{padding-top:0}
.my_set{background-color:#fff;padding:0 0 50px;text-align:center}
.my_set .nogo:after{content:''}
.my_set .nogo span{right:15px}
.my_set .size4{width:90%;border-color:#eee;color:#666;font-size:16px;margin:20px auto;display:block}
#hidedel_rmbbox{display:none}
#hidedel_rmbbox .ul{width:80%;margin:0 auto}
#hidedel_rmbbox .ul li{width:100%;height:32px;line-height:32px;margin:5px 0;border:1px solid #ffdede;color:#333;font-size:14px;display:block;text-align:center;border-radius:32px}
</style>
<?php 
$mini_class = 'top_mini top_miniBAI';
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-my_set">&#xe602;</i>设置';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
?>
<div class="submain my_set">
    <div class="modlist">
    	<br><h5 class="C999">保密后将无法查看别人联系方法<br />为了您的个人隐私，手机号默认保密状态</h5>
        <ul>
            <li class="nogo"><h4>手机号码</h4><span><input type="checkbox" name="mob_ifshow" id="mob_ifshow" class="switch" value="1"<?php echo ($data_mob_ifshow == 1)?' checked':'';?>><label for="mob_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></span></li>
            <?php if ($_VIP['hideprivacy'] == 1){?>
        	<li class="nogo"><h4>微信号</h4><span><input type="checkbox" name="weixin_ifshow" id="weixin_ifshow" class="switch" value="1"<?php echo ($data_weixin_ifshow == 1)?' checked':'';?>><label for="weixin_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></span></li>
        	<li class="nogo"><h4>微信二维码</h4><span><input type="checkbox" name="weixin_pic_ifshow" id="weixin_pic_ifshow" class="switch" value="1"<?php echo ($data_weixin_pic_ifshow == 1)?' checked':'';?>><label for="weixin_pic_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></span></li>
        	<li class="nogo"><h4>QQ</h4><span><input type="checkbox" name="qq_ifshow" id="qq_ifshow" class="switch" value="1"<?php echo ($data_qq_ifshow == 1)?' checked':'';?>><label for="qq_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></span></li>
        	<li class="nogo"><h4>邮箱</h4><span><input type="checkbox" name="email_ifshow" id="email_ifshow" class="switch" value="1"<?php echo ($data_email_ifshow== 1)?' checked':'';?>><label for="email_ifshow" class="switch-label"><i></i><b>公开</b><b>保密</b></label></span></li>
            <?php }?>
            <?php if ($_VIP['hidedata'] == 1){?><li class="nogo" style="border-top:12px  #f5f5f5 solid"><h4>个人资料显示</h4><span><input type="checkbox" name="my_hidemy" id="my_hidemy" class="switch" <?php echo ($data_flag ==1)?' checked':'';?>><label for="my_hidemy" class="switch-label"><i></i><b>显示</b><b>隐藏</b></label></span></li><?php }?>
            <?php if ($_VIP['hidephoto'] == 1){?><li class="nogo"><h4>头像照片显示</h4><span><input type="checkbox" name="my_hidephoto" id="my_hidephoto" class="switch" <?php echo ($data_photo_ifshow ==1)?' checked':'';?>><label for="my_hidephoto" class="switch-label"><i></i><b>显示</b><b>隐藏</b></label></span></li><?php }?>
            <li class="nogo"><h4>谁看过我微信通知</h4><span><input type="checkbox" name="ifViewPush" id="ifViewPush" class="switch" <?php echo ($data_ifViewPush ==1)?' checked':'';?>><label for="ifViewPush" class="switch-label"><i></i><b>开启</b><b>关闭</b></label></span></li>
            <li id="my_modpass" style="border-top:12px  #f5f5f5 solid"><h4>修改登录密码</h4><span></span></li>
            <?php if ($_VIP['hidedel'] == 1){?><li id="my_delmy"><h4>注销个人资料</h4><span></span></li><?php }?>
            <?php if (!is_weixin()){?>
            <button type="button" onClick="zeai.openurl('<?php echo HOST;?>/loginout.php')" class="btn size4 BAI yuan">退出当前帐号</button>
            <?php }?>
        </ul>
	</div>
</div>
<?php if ($_VIP['hidedel'] == 1 && $_VIP['hidedel_rmb']>0){?>
<div id="hidedel_rmbbox"><div class="ul"><li>脱单了</li><li>因被骚扰，所以注销</li><li>平台介绍牵线中</li><li>怕被熟人看到</li><li>加入找不到对象</li><li>红娘不理会，服务不行</li></div></div>
<script src="<?php echo HOST;?>/api/zeai_PAY.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<script>
zeai.listEach('.switch',function(obj){
	var objname = obj.name;
	obj.onclick = function(){
		var v=(obj.checked)?1:0
		zeai.ajax({url:'m1/my_set'+zeai.ajxext+'submitok=ajax_set&objname='+objname+'&v='+v},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
		});
	}
});

my_modpass.onclick=function(){ZeaiM.page.load('m1/my_set.php?submitok=modpass','my_set','my_set_modpass');}
<?php if (!empty($a)){?>my_modpass.click();<?php }if ($_VIP['hidedel'] == 1 && $_VIP['hidedel_rmb']==0){?>
my_delmy.onclick=function(){
	zeai.alertplus({title:'确定要注销个人资料么？',content:'注销后，您将不能登录本站<br><br>如果真的要注销请点击【确定】',title1:'我再想想',title2:'确定',
	fn1:function(){zeai.alertplus(0);},
	fn2:function(){zeai.alertplus(0);
		zeai.msg('正在清理数据',{time:10});
		zeai.ajax('m1/my_set'+zeai.ajxext+'submitok=my_delmy',function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg,{time:4});
			if (rs.flag == 1){setTimeout(function(){zeai.openurl('<?php echo HOST;?>');},3000);}
		});
	}
	});
}
<?php }if ($_VIP['hidedata'] == 1){?>
my_hidemy.onclick=function(){
	zeai.ajax('m1/my_set'+zeai.ajxext+'submitok=my_hidemy',function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg,{time:4});
	});
}
<?php }if ($_VIP['hidephoto'] == 1){?>
my_hidephoto.onclick=function(){
	zeai.ajax('m1/my_set'+zeai.ajxext+'submitok=my_hidephoto',function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
	});
}
<?php }?>
ifViewPush.onclick=function(){
	zeai.ajax('m1/my_set'+zeai.ajxext+'submitok=ifViewPush_update',function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.msg(rs.msg);
	});
}
<?php if ($_VIP['hidedel'] == 1 && $_VIP['hidedel_rmb']>0){?>
my_delmy.onclick=function(){
	zeai.alertplus({title:'确定要注销个人资料么？',content:'注销后，您将不能登录本站<br><br>为了补贴网站运营成本<br>将收取注销费用<font color=red>￥<?php echo $_VIP['hidedel_rmb'];?>元</font><br><br>确认注销请点击【确定】',title1:'我再想想',title2:'确定',
	fn1:function(){zeai.alertplus(0);},
	fn2:function(){zeai.alertplus(0);
		zeai.div({obj:hidedel_rmbbox,fobj:my_set,title:'请选择注销原因',w:300,h:300});
	}});
}
zeai.listEach(zeai.tag(hidedel_rmbbox,'li'),function(obj){obj.onclick = function(){zeai_PAY({money:<?php echo floatval($_VIP['hidedel_rmb']);?>,paykind:'wxpay',kind:9,title:'用户注销资料：'+obj.innerHTML,return_url:HOST+'/loginout.php',jumpurl:HOST+'/loginout.php'});}});
<?php }?>
</script>
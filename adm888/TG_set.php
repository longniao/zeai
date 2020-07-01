<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_shop.php';
$TG_set = json_decode($_REG['TG_set'],true);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
::-webkit-input-placeholder{color:#bbb;font-size:14px}
.zoom{background-color:#666;padding:20px}
td.tdLbgHUI{background-color:#eee}
</style>
<body>
<div class="navbox">
	<?php if ($t == 'set'){?><a href="#" class="ed">推广全局设置</a><?php }?>
	<?php if ($t == 'role'){?><a href="#" class="ed">推广组/套餐权限</a><?php }?>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<!---->
<div class="clear"></div> 
<form id="W_W_W_Z_E__A_I__C_N__FORM">
<?php
if ($t == 'vipdatarz'){
	?>

<?php }elseif($t == 'set'){
	if(!in_array('u_tg',$QXARR))exit(noauth());
	?>

    <style>
	.table.cols1 .tdL{width:160px}    
/*	.table.cols1 .tdR2{width:390px}    
*/    </style>
    <table class="table size2 cols1" style="width:1111px;margin:20px 0 100px 20px">
    
    
    <tr><th align="left" colspan="2">获客推广分销设置
    <div id="tg_help" class="helpC S14"><b>什么叫三级推广分销？</b><br>被推广的用户，我，我的下线，包括自己一共三级；<br><b>假设：一级奖励40%（设为40%）。二级奖励10%（设为10%）。</b><br>
比如，我是一级，名字叫A。用户B，关注了A的二维码之后，又分享出去，有用户C关注了B的二维码， 如果C办理线上1年用户698，则B可以得到奖励40%，279.2元。A可以获得奖励10%，69.8元。如果C也分享出去了，D注册了，如果D办理线上1年用户698，则C可以得到奖励40%，279.2元。B可以获得奖励10%，69.8元。和A就没有关系了。这就是三级分销。<br><br>

<b>什么叫合伙人：</b>A推广员推荐了“推广员/商家/买家用户”注册后，对于A来说“推广员/商家/买家用户”就是A的合伙人<br><br>
<b>什么叫单身团：</b>A推广员推荐了“单身相亲用户”注册后，对于A来说“单身相亲用户”就是A的单身团
</div><img src="images/wenhao.png" class="helpico" onClick="zeai.div({obj:tg_help,title:'获客推广分销说明',w:600,h:500});"></th></tr>
	<tr>
		<td class="tdL">推广返利总开关</td>
	  	<td class="tdR">
        <input type="checkbox" class="switch" id="tg1" value="1"<?php echo (@in_array('tg',$navarr))?' checked':'';?> disabled><label for="tg1" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
        　　　<i class="ico Caaa S18">&#xe62d;</i>　<a href="var.php?t=nav" class="blue">点击前往【导航/模块设置】去设置>></a>
        </td>
    </tr>
    
    
	<tr>
		<td class="tdL">推广模块名称</td>
		<td class="tdR"><input name="navtitle" id="navtitle" type="text" class="W200" maxlength="50" value="<?php echo $TG_set['navtitle'];?>">　　<span class="tips S12">默认：【推广中心】</span></td>
	</tr>
	<tr>
		<td class="tdL">推广员名称</td>
		<td class="tdR"><input name="tgytitle" id="tgytitle" type="text" class="W200" maxlength="50" value="<?php echo $TG_set['tgytitle'];?>">　　<span class="tips S12">默认：【红娘】</span></td>
	</tr>
    
	<tr>
		<td class="tdL tdLbgHUI" style="line-height:150%"><?php echo '推广员/'.$_SHOP['title'].'/买家';?><br>帐号注册类型</td>
		<td class="tdR">
        
			<input type="radio" name="regkind" id="regkind1" class="radioskin" value="2"<?php echo ($TG_set['regkind'] == 2)?' checked':'';?>><label for="regkind1" class="radioskin-label"><i class="i1"></i><b class="W100">用户名＋密码</b></label>　　
            <input type="radio" name="regkind" id="regkind2" class="radioskin" value="1"<?php echo ($TG_set['regkind'] == 1)?' checked':'';?>><label for="regkind2" class="radioskin-label"><i class="i1"></i><b class="W80">手机＋密码</b></label><span class='tips2'>选择手机+密码需配置相关短信验证码接口</span>
	  </td>
	</tr>

	<tr>
		<td class="tdL tdLbgHUI">推广员强制关注公众号</td>
		<td class="tdR">
        <input type="checkbox" class="switch" name="force_subscribe" id="force_subscribe" value="1"<?php echo ($TG_set['force_subscribe'] == 1)?' checked':'';?>><label for="force_subscribe" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　　<span class="tips S12">开启之后，注册后会跳转到关注公众号页面，只对微信端有效</span>
        </td>
	</tr>
	<tr>
		<td class="tdL tdLbgHUI">推广员关注欢迎信息</td>
		<td class="tdR"><textarea name="wx_gzh_welcome" id="wx_gzh_welcome"  rows="3" class="W700 S14" ><?php echo dataIO($TG_set['wx_gzh_welcome'],'wx');?></textarea></td>
	</tr>
    
	<tr>
		<td class="tdL tdLbgHUI">推广员注册审核</td>
		<td class="tdR">
        <input type="checkbox" class="switch" name="regflag" id="regflag" value="1"<?php echo ($TG_set['regflag'] == 1)?' checked':'';?>><label for="regflag" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　　<span class="tips S12">开启之后，只有审核通过，帐号才能正常使用，关闭后直接进入使用，如果下面设置了激活费用推荐关闭</span>
        </td>
	</tr>

    
	<tr>
		<td class="tdL tdLbgHUI">推广员新帐号激活费用</td>
		<td class="tdR">
              <input name="active_price" id="active_price" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $TG_set['active_price'];?>"> 元　<span class="tips S12">设置了费用，需要推广员支付费用帐号才能激活，填0直接激活，自动归为默认权重等级</span>
        </td>
	</tr>
	<tr>
		<td class="tdL tdLbgHUI">强制微信端使用</td>
		<td class="tdR">
        <input type="checkbox" class="switch" name="force_weixin" id="force_weixin" value="1"<?php echo ($TG_set['force_weixin'] == 1)?' checked':'';?>><label for="force_weixin" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　　<span class="tips S12">开启之后，只能在微信里浏览使用</span>
        </td>
	</tr>
	<tr>
		<td class="tdL tdLbgHUI">推广员角色/等级升级</td>
		<td class="tdR">
        <input type="checkbox" class="switch" name="openvip" id="openvip" value="1"<?php echo ($TG_set['openvip']==1)?' checked':'';?>><label for="openvip" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　　<span class="tips S12">关闭后，推广员不能升级推广等级（升级按钮消失），关闭后默认权重等级</span>
        </td>
	</tr>
    
    
	<tr>
		<td class="tdL">单身新用户注册奖励条件</td>
		<td class="tdR">
        	<?php $reward_tjARR=explode(',',$TG_set['reward_tj']);?>
			<input type="checkbox" name="reward_tj[]" id="reward_tj1" class="checkskin " value="gzh"<?php echo (@in_array('gzh',$reward_tjARR))?' checked':'';?>><label for="reward_tj1" class="checkskin-label"><i class="i1"></i><b class="W100">关注公众号</b></label>
			<input type="checkbox" name="reward_tj[]" id="reward_tj2" class="checkskin " value="bfb"<?php echo (@in_array('bfb',$reward_tjARR))?' checked':'';?>><label for="reward_tj2" class="checkskin-label"><i class="i1"></i><b class="W200">资料完整度达 <input name="reward_tj_bfb" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo intval($TG_set['reward_tj_bfb']);?>"> %</b></label>
			<input type="checkbox" name="reward_tj[]" id="reward_tj3" class="checkskin " value="rz_mob"<?php echo (@in_array('rz_mob',$reward_tjARR))?' checked':'';?>><label for="reward_tj3" class="checkskin-label"><i class="i1"></i><b class="W100">完成手机认证</b></label>　
			<input type="checkbox" name="reward_tj[]" id="reward_tj4" class="checkskin " value="rz_identity"<?php echo (@in_array('rz_identity',$reward_tjARR))?' checked':'';?>><label for="reward_tj4" class="checkskin-label"><i class="i1"></i><b class="W100">完成身份认证</b></label>　
			<input type="checkbox" name="reward_tj[]" id="reward_tj5" class="checkskin " value="rz_edu"<?php echo (@in_array('rz_edu',$reward_tjARR))?' checked':'';?>><label for="reward_tj5" class="checkskin-label"><i class="i1"></i><b class="W100">完成学历认证</b></label>
            
            　　<br><span class="tips2 S12">此条件需同时满足，只对下方【后台人工验证审核后奖励】有效，应用于推荐过来的单身相亲用户</span>
        </td>
	</tr>
    
	<tr>
		<td class="tdL">奖励到账方式</td>
		<td class="tdR">
	          <input type="radio" name="reward_flag" id="reward_flag1" class="radioskin" value="0"<?php echo ($TG_set['reward_flag'] == 0)?' checked':'';?>><label for="reward_flag1" class="radioskin-label"><i class="i1"></i><b class="W200">后台人工验证审核后奖励</b></label>
            <input type="radio" name="reward_flag" id="reward_flag2" class="radioskin" value="1"<?php echo ($TG_set['reward_flag'] == 1)?' checked':'';?>><label for="reward_flag2" class="radioskin-label"><i class="i1"></i><b class="W100">直接通过并奖励</b></label>　　<span class="tips S12">推荐人工验证审核，以防刷单</span>
        </td>
	</tr>
    

	<tr>
		<td class="tdL tdLbgHUI">商家入驻合作</td>
		<td class="tdR">
        <input type="checkbox" class="switch" name="company_switch" id="company_switch" value="1"<?php echo ($TG_set['company_switch']==1)?' checked':'';?>><label for="company_switch" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　　<span class="tips S12">开启后，在“我的”出现商家入驻申请和管理入口</span>
        </td>
	</tr>

	<tr style="display:none">
		<td class="tdL tdLbgHUI">商家/机构入驻审核</td>
		<td class="tdR">
        <input type="checkbox" class="switch" name="company_ifsh" id="company_ifsh" value="1"<?php echo ($TG_set['company_ifsh']==1)?' checked':'';?>><label for="company_ifsh" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　　<span class="tips S12">开启后，只有审核通过的商家/机构才能正常使用</span>
        </td>
	</tr>

    
    <tr  >
		<td class="tdL">H5/手机端推广海报</td>
	  	<td class="tdR">
			<?php if (!empty($TG_set['wapbgpic'])) {?>
                <input name='wapbgpic_' type='hidden' value="<?php echo $TG_set['wapbgpic'];?>" />
                <a href="###" class="pic60" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$TG_set['wapbgpic']; ?>')"><img src="<?php echo $_ZEAI['up2'].'/'.$TG_set['wapbgpic']; ?>"></a>　
                <a href="###" class="btn size1" id="wapbgpicdel">删除</a>　
			<?php }else{echo "<input name='wapbgpic' type='file' size='50' class='Caaa W300' />";}?>	
            <br><span class='tips2'>先删除后更换，必须gif/jpg/png格式，尺寸750*1200像数，大小不要超过200KB，示例图中黄色部分留空（系统会自动插入用户头像和二维码）</span>
        </td>
	</tr>
    <tr >
		<td class="tdL">通用推广文本</td>
	  	<td class="tdR"><input name="tg_text" type="text" class="W100_" id="tg_text" value="<?php echo dataIO($TG_set['tg_text'],'out');?>" size="30">

	  <br><span class="C999 S12">{tglink}符号代表推广链接网址，在推广中心会自动替换，您只要更换<b>汉字部分</b>即可</span>
      </td>
	</tr>
    <tr >
		<td class="tdL tdLbgHUI">微信公众号端推广海报</td>
	  	<td class="tdR">
			<?php if (!empty($TG_set['wxbgpic'])) {?>
                <input name='wxbgpic_' type='hidden' value="<?php echo $TG_set['wxbgpic'];?>" />
                <a href="###" class="pic60" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$TG_set['wxbgpic']; ?>')"><img src="<?php echo $_ZEAI['up2'].'/'.$TG_set['wxbgpic']; ?>"></a>　
                <a href="###" class="btn size1" id="wxbgpicdel">删除</a>　
			<?php }else{echo "<input name='wxbgpic' type='file' size='50' class='Caaa W300' />";}?>
            <br><span class='tips2'>先删除后更换，必须gif/jpg/png格式，尺寸750*1200像数，大小不要超过200KB，示例图中黄色部分留空（系统会自动插入用户头像和二维码）</span>
        </td>
	</tr>

    <tr >
		<td class="tdL tdLbgHUI">关注海报公众号后内容</td>
	  	<td class="tdR  lineH150">
        <textarea name="wxhbT" id="wxhbT" rows="4" class="W700 S14"><?php echo dataIO($TG_set['wxhbT'],'wx');?></textarea>
        <br>
        <span class='tips2'>
        别人识别海报二维码后，公众号出现的推送欢迎消息 100个字以内
        </span>
	</td>
	</tr>


    <tr >
		<td class="tdL ">微信推广/分享标题</td>
	  	<td class="tdR  lineH150">
        <textarea name="wxshareT" id="wxshareT" rows="3" class="W700 S14"><?php echo dataIO($TG_set['wxshareT'],'wx');?></textarea>
        <br>
        <span class='tips2'>
        微信分享推广用户注册用 100个字以内
        </span>
	</td>
	</tr>

    <tr >
		<td class="tdL ">微信推广/分享描述</td>
	  	<td class="tdR lineH150">
        <textarea name="wxshareC" id="wxshareC" rows="3" class="W700 S14"><?php echo dataIO($TG_set['wxshareC'],'wx');?></textarea>
        <br>
        <span class='tips2'>
        微信分享推广用户注册用 200个字以内
        </span>
	</td>
	</tr>
    
    
    </table>
    <div class="savebtnbox">
        <input name="submitok" type="hidden" value="cache_TG_set">
        <button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
    </div>
<?php }?>

<input id="uu" name="uu" type="hidden" value="<?php echo $session_uid;?>">
<input id="pp" name="pp" type="hidden" value="<?php echo $session_pwd;?>">
</form>
<script>
	var uu='<?php echo $session_uid;?>',pp='<?php echo $session_pwd;?>';
	save.onclick = function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:W_W_W_Z_E__A_I__C_N__FORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg,o(rs.focus));
			if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}
		});
	}
	if (!zeai.empty(o('wxbgpicdel')))o('wxbgpicdel').onclick = function(){delpic('cache_config_del_wxbgpic');}
	if (!zeai.empty(o('wapbgpicdel')))o('wapbgpicdel').onclick = function(){delpic('cache_config_del_wapbgpic');}
	
	function delpic(submitok){
		zeai.confirm('确认要删除么？',function(){
			zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:submitok,uu:uu,pp:pp}},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);
				if (rs.flag == 1){location.reload(true);}else{zeai.alert(rs.msg);}
			});
		});
	}
	
</script>
<br><br><br><br><br>
<?php require_once 'bottomadm.php';?>
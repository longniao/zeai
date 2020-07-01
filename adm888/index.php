<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
if($submitok=='ajax_active_session'){exit;}
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_shop.php';
$TG_set = json_decode($_REG['TG_set'],true);
if(!empty($session_path_s))$path_s_str='<img src="'.$_ZEAI['up2'].'/'.$session_path_s.'">';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_ZEAI['siteName']; ?> ● 管理系统</title>
<link href="./css/index.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="./css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="./js/jquery.min.index.js"></script>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
</head>
<body scroll="no"style="overflow:hidden">
<div class="top">
	<h1 id="siteName"><?php echo $_ADM['admSiteName']; ?></h1>
	<ul id="top_menu">
		<li tips-title="打开前台首页" tips-direction='bottom' id="top_index" class="tips"><img src="images/admin_img/2.png" onClick="zeai.openurl_('<?php echo HOST;?>')"></li>
		<li tips-title="刷新内容窗口" tips-direction='bottom' id="top_refresh" class="tips"><img src="images/admin_img/3.png"></li>
		<div id="love">
		<a id="LA1" onclick="LAfun(1,'welcome.php',1);" class="ed"><i class="ico">&#xe7a0;</i>工作台</a>
		<a id="LA2" onclick="LAfun(2,'u.php',1);" tips-title="用户录入/升级/资料审核/查询/管理、VIP套餐权限等" tips-direction='bottom' class="tips"><i class="ico2">&#xe601;</i>用户</a>
		<a id="LA3" onclick="LAfun(3,'news.php',5);" tips-title="网站简介/客服信息/文章，公告，交友活动等" tips-direction='bottom' class="tips"><i class="ico2">&#xe609;</i>内容</a>
		<a id="LA6" onclick="LAfun(6,'shop_welcome.php',1);" tips-title="商家、商品、订单、数据分析、优惠券管理等" tips-direction='bottom' class="tips"><i class="ico2">&#xe71a;</i>商家</a>
		<a id="LA4" onclick="LAfun(4,'TG_welcome.php',7);" tips-title="广告/海报发布/<?php echo $TG_set['navtitle'];?>等" tips-direction='bottom' class="tips"><i class="ico">&#xe6fd;</i>运营<font class="newdian"></font></a>
		<a id="LA5" onclick="LAfun(5,'var.php?t=1',1);" tips-title="网站基本参数、公众号/客服信息/认证/地区设置等" tips-direction='bottom' class="tips"><i class="ico">&#xe649;</i>设置</a>
		<?php if(in_array('crm',$QXARR)){?><a id="crmo" onClick="crmoFn()">进入线下CRM</a><?php }?>
		</div>
		<div id="crm">
		<a id="CA1" onclick="CAfun(1,'crm_welcome.php',1);" class="ed"><i class="ico">&#xe7a0;</i> 工作台</a>
		<a id="CA3" onclick="CAfun(3,'crm_user.php',1);" tips-title="线下VIP等级升级/客户资料查询/录入/修改等" tips-direction='bottom' class="tips"><i class="ico">&#xe645;</i>客户</a>
		<a id="CA4" onclick="CAfun(4,'crm_sq.php?t=1',1);"<?php echo(in_array('crm',$QXARR) || strstr($session_crmkind,'adm') || strstr($session_crmkind,'sq') )?'':' style="display:none"';?> tips-title="售前客户管理/售前红娘分配/售前跟进等" tips-direction='bottom' class="tips"><i class="ico">&#xe861;</i>售前</a>
		<a id="CA5" onclick="CAfun(5,'crm_sh.php?t=3',1);"<?php echo(in_array('crm',$QXARR) || strstr($session_crmkind,'adm') || strstr($session_crmkind,'sh') )?'':' style="display:none"';?> tips-title="星级客户售后管理/售后红娘分配/牵线约见等" tips-direction='bottom' class="tips"><i class="ico">&#xe621;</i>售后</a>
		<a id="CA6" onclick="CAfun(6,'crm_ht.php?t=htflagall',1);"<?php echo(in_array('crm',$QXARR) || strstr($session_crmkind,'adm') || strstr($session_crmkind,'ht') || strstr($session_crmkind,'sq') )?'':' style="display:none"';?>><i class="ico">&#xe656;</i>合同</a>
		<a id="CA7" onclick="CAfun(7,'crm_ht.php?t=payflagall',1);"<?php echo(in_array('crm',$QXARR) || strstr($session_crmkind,'adm') || strstr($session_crmkind,'cw'))?'':' style="display:none"';?>><i class="ico">&#xe61a;</i>财务</a>
		<a id="CA2" onclick="CAfun(2,'crm_hn.php',3);"<?php echo(in_array('crm',$QXARR) || strstr($session_crmkind,'adm'))?'':' style="display:none"';?> tips-title="门店管理/红娘管理等" tips-direction='bottom' class="tips"><i class="ico">&#xe649;</i>设置<font class="newdian"></font></a>
		<?php if(in_array('love',$QXARR)){?><a id="loveo" onClick="loveoFn()">进入主后台</a><?php }?>
		</div>
	</ul>
	<u class="transition">
		<i><?php echo $path_s_str;?></i><span><?php echo $session_uname; ?></span>
		<em>
            <a><?php echo $session_title;?></a>
            <a>ID：<?php echo $session_uid;?></a>
			<a href="modpass.php" target="right">修改密码</a>
			<a href="exit.php">退出后台</a>
		</em>
	</u>

</div>
<main>
	<div id="leftMain"></div>
	<iframe id="rightMain" name="right" src="<?php echo ($session_kind == 'crm')?'crm_welcome.php':'welcome.php';?>" frameborder="false" scrolling="auto" allowtransparency="true"></iframe>
    <em class="indent open" title="左侧缩进" id="indent"><i class="ico">&#xe602;</i></em>
</main>
<script>
indent.onclick = function(){
	if (o('leftMain').className == 'off'){
		o('leftMain').class('');
		o('rightMain').class('');
		o('siteName').class('');
		this.class('indent open');
		this.setAttribute('title','左侧缩进');
		this.html('<i class="ico">&#xe602;</i>');
	}else{
		o('leftMain').class('off');
		o('rightMain').class('off');
		o('siteName').class('off');
		this.class('indent close');
		this.setAttribute('title','左侧展开');
		this.html('<i class="ico">&#xe601;</i>');
	}
}
</script>
<?php if (!empty($session_truename)){$session_truename='<font>('.$session_truename.')</font>';}?>
<script src="./js/index.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>var admSiteName='<?php echo $_ADM['admSiteName']; ?>',agenttitle='<?php echo $session_agenttitle;?>',session_truename='<?php echo $session_truename;?>';
<?php if ($session_kind == 'crm'){?>
window.onload=function(){
	love.hide();crm.show();
	siteName.html(agenttitle+session_truename);
		top_index.hide();
		top_refresh.addClass('borderleft1');
	CAfun(1,'crm_welcome.php',1);
}
<?php }?>
</script>
<script src="./js/piczoom.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</body>
</html>

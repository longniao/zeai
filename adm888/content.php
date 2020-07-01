<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'sub/upload_super.php';
if ($submitok == "mod") {
	switch ($kind) {
		case 'kefu':
			$mob = trimm($mob);
			$floatqq = trimm($floatqq);
			$kefuqq  = trimm($kefuqq);
			if (str_len($tel) >100)callmsg("客服中心热线电话长度请控制在100字节以内","-1");
			if (str_len($mob) >100)callmsg("手机长度请控制在100字节以内","-1");
			if (str_len($email) >100)callmsg("Email长度请控制在100字节以内","-1");
			if (str_len($kefuqq) >100)callmsg("客服中心QQ长度请控制在100字节以内","-1");
			if (str_len($floatqq) >100)callmsg("浮动客服QQ长度请控制在100字节以内","-1");
			$kefuweixin = dataIO($kefuweixin,'in',50);
			$db->query("UPDATE ".__TBL_CONTENT__." SET kefuqq='$kefuqq',floatqq='$floatqq',mob='$mob',tel='$tel',email='$email',kefuweixin='$kefuweixin'");
		break;
		case 'basic':
			if (str_len($SiteName) >200)callmsg("网站名称长度请控制在200字节以内","-1");
			if (str_len($SiteIndexTitle) >200)callmsg("网站首页Title标题长度请控制在200字节以内","-1");
			if (str_len($Keywords) >200)callmsg("网站seo关键词长度请控制在200字节以内","-1");
			if (str_len($Description) >200)callmsg("网站seo简介长度请控制在200字节以内","-1");
			if (str_len($areaid) >50)callmsg("首页今日之星地区长度请控制在50字节以内","-1");
			if (str_len($DefArea) >50)callmsg("默认地区长度请控制在50字节以内","-1");
			$index_GBM = dataIO($areaid,'in',30);
			if (str_len($pc_bottominfo) >800)callmsg("底部信息长度请控制在800字节以内","-1");
			$SiteName_adm  = dataIO($SiteName_adm,'in',50);
			if ($_SERVER['REQUEST_METHOD'] == 'POST'){ 
				for($i=1;$i<=3;$i++) {
					$FILES = $_FILES["pic".$i];
					if (!empty($FILES)){
						$dbpicname = setphotodbname($_ZEAI['UpPath'].'/banner',$FILES['tmp_name'],$partyid.'_');
						if ($dbpicname){
							if (!up_send($FILES,$dbpicname,$_ZEAI['ifwaterimg'],$_ZEAI['UpMsize'],'900*900'))continue;
							$_s = setpath_s($dbpicname);
							$tmppicurl = $_ZEAI['up2']."/".$_s;
							if (!ifpic($tmppicurl))continue;
							switch ($i) {
								case 1:$SQL = " path1_s = '$_s'";break;
								case 2:$SQL = " path2_s = '$_s'";break;
								case 3:$SQL = " path3_s = '$_s'";break;
							}
							$db->query("UPDATE ".__TBL_CONTENT__." SET ".$SQL);
						}
					}
				}
			}
			$path1_url = dataIO($path1_url,'in',100);
			$path2_url = dataIO($path2_url,'in',100);
			$path3_url = dataIO($path3_url,'in',100);
			$db->query("UPDATE ".__TBL_CONTENT__." SET path1_url='$path1_url',path2_url='$path2_url',path3_url='$path3_url',SiteName='$SiteName',SiteIndexTitle='$SiteIndexTitle',Keywords='$Keywords',Description='$Description',index_GBM='$index_GBM',DefArea='$DefArea',pc_bottominfo='$pc_bottominfo',SiteName_adm='$SiteName_adm'");
		break;
		case 'basic_set':
			$tg_if   = intval($tg_if);
			$task_if = intval($task_if);
			$db->query("UPDATE ".__TBL_CONTENT__." SET tg_if='$tg_if',task_if='$task_if'");
		break;
		case 'pay':
			if (str_len($tenpay1) >100)callmsg("商户名称长度请控制在100字节以内","-1");
			if (str_len($tenpay2) >100)callmsg("商户号长度请控制在100字节以内","-1");
			if (str_len($tenpay3) >100)callmsg("密钥长度请控制在100字节以内","-1");
			if (str_len($alipay1) >100)callmsg("合作身份者ID长度请控制在100字节以内","-1");
			if (str_len($alipay2) >100)callmsg("支付宝账号长度请控制在100字节以内","-1");
			if (str_len($alipay3) >100)callmsg("安全检验码长度请控制在100字节以内","-1");
			if (str_len($content) >500)callmsg("银行账号长度请控制在500字节以内","-1");
			$db->query("UPDATE ".__TBL_CONTENT__." SET tenpay1='$tenpay1',tenpay2='$tenpay2',tenpay3='$tenpay3',alipay1='$alipay1',alipay2='$alipay2',alipay3='$alipay3',bank='$content'");
		break;
		case 'Weixin':
			$wxapi_name  = dataIO($wxapi_name,'in',50);
			$wxapi_appid = dataIO($wxapi_appid,'in',50);
			$wxapi_appsecret = dataIO($wxapi_appsecret,'in',50);
			$wxapi_token = dataIO($wxapi_token,'in',50);
			$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_name='$wxapi_name',wxapi_appid='$wxapi_appid',wxapi_appsecret='$wxapi_appsecret',wxapi_token='$wxapi_token'");
		break;
		case 'WeixinWelcome':
			if (str_len($wxapi_welcome) >400)callmsg("内容请控制在400字节以内","-1");
			if (str_len($wxapi_hfcontent) >400)callmsg("内容请控制在400字节以内","-1");
			$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_welcome='$wxapi_welcome',wxapi_hfcontent='$wxapi_hfcontent'");
		break;
		case 'LoginPaySms':
			$wxapi_MCHID = dataIO($wxapi_MCHID,'in',50);
			$wxapi_KEY = dataIO($wxapi_KEY,'in',50);
			$SiteIP = dataIO($SiteIP,'in',20);
			$wxapi_certpath = dataIO($wxapi_certpath,'in',100);
			$wxapi_open_appid = dataIO($wxapi_open_appid,'in',50);
			$wxapi_open_appsecret = dataIO($wxapi_open_appsecret,'in',50);
			$QQ_appid = dataIO($QQ_appid,'in',50);
			$QQ_appkey = dataIO($QQ_appkey,'in',50);
			$weibo_AppKey = dataIO($weibo_AppKey,'in',50);
			$weibo_AppSercet = dataIO($weibo_AppSercet,'in',50);
			$sms_sid = dataIO($sms_sid,'in',50);
			$sms_apikey = dataIO($sms_apikey,'in',50);
			$sms_tplid_authcode = dataIO($sms_tplid_authcode,'in',50);
			$sms_tplid_findpass = dataIO($sms_tplid_findpass,'in',50);
			$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_MCHID='$wxapi_MCHID',wxapi_KEY='$wxapi_KEY',SiteIP='$SiteIP',wxapi_certpath='$wxapi_certpath',wxapi_open_appid='$wxapi_open_appid',wxapi_open_appsecret='$wxapi_open_appsecret',QQ_appid='$QQ_appid',QQ_appkey='$QQ_appkey',weibo_AppKey='$weibo_AppKey',weibo_AppSercet='$weibo_AppSercet',sms_sid='$sms_sid',sms_apikey='$sms_apikey',sms_tplid_authcode='$sms_tplid_authcode',sms_tplid_findpass='$sms_tplid_findpass'");
		break;
		case 'WeixinTemplate':
			$wxapi_mb_loveb  = dataIO($wxapi_mb_loveb,'in',50);
			$wxapi_mb_msgchat  = dataIO($wxapi_mb_msgchat,'in',50);
			$wxapi_mb_userdata  = dataIO($wxapi_mb_userdata,'in',50);
			$wxapi_mb_admininfo  = dataIO($wxapi_mb_admininfo,'in',50);
			$wxapi_mb_honor  = dataIO($wxapi_mb_honor,'in',50);
			$wxapi_mb_msggift  = dataIO($wxapi_mb_msggift,'in',50);
			$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_mb_loveb='$wxapi_mb_loveb',wxapi_mb_msgchat='$wxapi_mb_msgchat',wxapi_mb_userdata='$wxapi_mb_userdata',wxapi_mb_admininfo='$wxapi_mb_admininfo',wxapi_mb_honor='$wxapi_mb_honor',wxapi_mb_msggift='$wxapi_mb_msggift'");
		break;
		case 'WeixinPushInfo':
			if ($pushkind != 'text' && $pushkind != 'pic' && $pushkind != 'user')callmsg("推送类型不符","-1");
			switch ($pushkind) {
				case 'text':
					if (str_len($wxapi_push_Text) >400)callmsg("图文推送信息超过400字节","-1");
					$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_push_Text='$wxapi_push_Text',wxapi_push_kind='$pushkind'");
				break;
				case 'pic':
					$wxapi_push_PicTtitle  = dataIO($wxapi_push_PicTtitle,'in',100);
					$wxapi_push_PicContent = dataIO($wxapi_push_PicContent,'in',100);
					$wxapi_push_PicUrl     = dataIO($wxapi_push_PicUrl,'in',50);
					$rowc = get_db_content("wxapi_push_PicPath");
					$_ZEAI["wxapi_push_PicPath"] = $rowc['wxapi_push_PicPath'];
					if (empty($_ZEAI["wxapi_push_PicPath"])){ 
						$FILES = $_FILES["pic1"];
						if (!empty($FILES['tmp_name'])){
							$dbpicname = setphotodbname($_ZEAI['UpPath'].'/weixin',$FILES['tmp_name']);
							if ($dbpicname){
								if (!up_send($FILES,$dbpicname,$_ZEAI['ifwaterimg'],'900*500'))continue;
								$tmppicurl = $_ZEAI['up2']."/".$dbpicname;
								if (!ifpic($tmppicurl))callmsg("图片格式错误!","-1");
								$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_push_PicPath='$dbpicname'");
							}
						}else{
							callmsg("图片不能为空!","-1");
						}
					}
					$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_push_PicTtitle='$wxapi_push_PicTtitle',wxapi_push_PicContent='$wxapi_push_PicContent',wxapi_push_PicUrl='$wxapi_push_PicUrl',wxapi_push_kind='$pushkind'");
				break;
				case 'user':
					$wxapi_push_Userlist = dataIO($wxapi_push_Userlist,'in',100);
					$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_push_Userlist='$wxapi_push_Userlist',wxapi_push_kind='$pushkind'");
				break;
			}
			$db->query("UPDATE ".__TBL_USER__." SET ifWeixinPushInfo=1 WHERE ifWeixinPushInfo=0 AND openid<>''");
		break;
	}
	callmsg("修改成功!","$SELF?kind=$kind");
}elseif($submitok == 'delpicupdate2'){
	if ($num != 1)callmsg("forbidden.","-1");
	$rt = $db->query("SELECT wxapi_push_PicPath FROM ".__TBL_CONTENT__);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$path2 = $row[0];
		up_send_admindel($path2);
	}
	$db->query("UPDATE ".__TBL_CONTENT__." SET wxapi_push_PicPath=''");
	header("Location: ".$SELF."?kind=WeixinPushInfo&pushkind=pic");
	
}elseif($submitok == 'delpicupdate'){
	if ( !ifint($num,'1-3','1') )callmsg("forbidden.","-1");
	switch ($num) {
		case 1:$SQL = "path1_s";$SETT = "path1_s=''";break;
		case 2:$SQL = "path2_s";$SETT = "path2_s=''";break;
		case 3:$SQL = "path3_s";$SETT = "path3_s=''";break;
	}
	$rt = $db->query("SELECT ".$SQL." FROM ".__TBL_CONTENT__);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$path_s = $row[0];$path_b = getpath_b($path_s);
		up_send_admindel($path_s.'|'.$path_b);
	}
	$db->query("UPDATE ".__TBL_CONTENT__." SET ".$SETT);
	header("Location: ".$SELF."?kind=basic");
}
$rt=$db->query("SELECT * FROM ".__TBL_CONTENT__);
if ($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
}else{
	$db->query("INSERT INTO ".__TBL_CONTENT__."  (kefu) VALUES ('')");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js"></script>
<script src="../js/areaData.js"></script>
<script src="js/select3.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<div class="navbox">
    <?php if ($kind == 'Weixin' || $kind == 'WeixinTemplate' || $kind == 'WeixinWelcome' || $kind == 'WeixinPushInfo'){
		$a1cls = ($kind == 'Weixin')?' class="ed"':'';
		$a2cls = ($kind == 'WeixinTemplate')?' class="ed"':'';
		$a3cls = ($kind == 'WeixinWelcome')?' class="ed"':'';
		$a4cls = ($kind == 'WeixinPushInfo')?' class="ed"':'';
    	echo '<a href="content.php?kind=Weixin"'.$a1cls.'>公众号基本信息</a>';
    	echo '<a href="content.php?kind=WeixinTemplate"'.$a2cls.'>微信模板消息设置</a>';
    	echo '<a href="content.php?kind=WeixinWelcome"'.$a3cls.'>关注公众号欢迎/回复信息</a>';
    	echo '<a href="content.php?kind=WeixinPushInfo"'.$a4cls.'>文字/图文推送信息</a>';
	}elseif($kind == 'basic' || $kind == 'basic_set'){
		$a1cls = ($kind == 'basic')?' class="ed"':'';
		$a2cls = ($kind == 'basic_set')?' class="ed"':'';
    	echo '<a href="content.php?kind=basic"'.$a1cls.'>网站基本信息</a>';
    	echo '<a href="content.php?kind=basic_set"'.$a2cls.'>网站功能设置</a>';
	}else{
		echo '<a href="javascript:;" class="ed">';
		switch ($kind) {
			case 'LoginPaySms':$kindtitle = "登录/微信/短信";break;
			case 'kefu':$kindtitle = "客服信息设置";break;
			//case 'basic':$kindtitle =  "网站基本信息";break;
			case 'pay':$kindtitle =  "银行支付账号";break;
		}
		echo $kindtitle;
		echo '</a>';
    }
	?>
  <div class="clear"></div>
</div>
<script>function chkform(){
	var a1 = get_option('a1','v');
	var a2 = get_option('a2','v');
	var a3 = get_option('a3','v');
	a1 = (a1 == 0)?'':a1;
	a2 = (a2 == 0)?'':','+a2;
	a3 = (a3 == 0)?'':','+a3;
	var areaid = a1 + a2 + a3;
	areaid = (areaid == '0,0,0')?'':areaid;
	getid('areaid').value = areaid;
}</script>
<form action="<?php echo $SELF; ?>"  enctype="multipart/form-data"  method="post"<?php if ($kind == 'basic'){?> onsubmit="return chkform();"<?php }?>>
<table class="table900 Mtop50">
<?php if ($kind == 'kefu'){ ?>
    <tr><td colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT">
    <?php echo $kindtitle;?>：
    <?php if ($kind == 'kefu'){?><input class="btnHONG" type="button" value="预览客服中心" onClick="openurl_('<?php echo $_ZEAI['www_2domain']; ?>/servicecenter')" style="float:right" /><?php }?>
    </td></tr>
    <tr><td class="tdL">客服中心热线电话：</td>
    <td class="tdR">
    <input name="tel" type="text" class="input" id="tel" value="<?php echo stripslashes($row['tel']);?>"size="50" maxlength="100">
    </font>
    </td></tr>
    <tr><td class="tdL">客服中心手机：</td>
    <td class="tdR">
    <input name="mob" type="text" class="input" id="mob" value="<?php echo stripslashes($row['mob']);?>"size="50" maxlength="100">
    </font>
    </td>
    </tr>
    <tr>
    <td class="tdL">客服中心QQ：</td>
    <td class="tdR"><font color="#666666">
    <input name="kefuqq" type="text" class="input" id="kefuqq" value="<?php echo stripslashes($row['kefuqq']);?>" size="50" maxlength="100">
    </font><font color="#999999"><br>
    可多个QQ，每个QQ之间用英文半角逗号隔开，如：<font color="#0000FF">797311,7144100</font></font><input name="kind" type="hidden" value="kefu" /></td>
    </tr>
    <tr>
    <td class="tdL">客服中心Email：</td>
    <td class="tdR">
    <input name="email" type="text" class="input" id="email" value="<?php echo stripslashes($row['email']);?>"size="50" maxlength="100">
    </font>
    </td>
    </tr>
    <tr>
    <td class="tdL">浮动客服QQ：</td>
    <td class="tdR"><font color="#999999">
    <input name="floatqq" type="text" class="input" id="floatqq" value="<?php echo stripslashes($row['floatqq']);?>" size="50" maxlength="100" >
    <br>
    可多个QQ，最多5个,每个QQ之间用英文半角逗号隔开，如：<font color="#0000FF">797311,7144100</font>；关闭浮动请留空</font></td>
    </tr>
    <tr><td class="tdL">客服微信号：</td><td class="tdR"><input name="kefuweixin" type="text" class="input" value="<?php echo stripslashes($row['kefuweixin']);?>" size="50" maxlength="100" ></td></tr>
<?php }elseif($kind == 'Weixin'){ ?>
	<style>	.tdL{width:150px}.input{width:300px}</style>
	<tr><td height="40" colspan="2" align="left" class="S14 C999"><b>微信公众号(服务号)</b><font class="S12 FR">注册微信公众平台帐号：<a href="https://mp.weixin.qq.com" target="_blank" class="aLAN">微信公众平台</a></font></td></tr>    
    <tr><td class="tdL">公众号名称：</td><td class="tdR"><input name="wxapi_name" type="text" class="input" value="<?php echo stripslashes($row['wxapi_name']);?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">公众号appid：</td><td class="tdR"><input name="wxapi_appid" type="text" class="input" value="<?php echo dataIO($row['wxapi_appid'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">公众号appsecret：</td><td class="tdR"><input name="wxapi_appsecret" type="text" class="input" value="<?php echo dataIO($row['wxapi_appsecret'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">公众号token：</td><td class="tdR"><input name="wxapi_token" type="text" class="input" value="<?php echo dataIO($row['wxapi_token'],'out');?>" maxlength="100" ></td></tr>    
    <input name="kind" type="hidden" value="Weixin" />
<?php }elseif($kind == 'WeixinWelcome'){ ?>
    <tr><td class="tdL">关注公众号欢迎信息：</td><td class="tdR lineH150"><textarea name="wxapi_welcome" cols="50" rows="8" class="textarea W400"><?php echo stripslashes($row['wxapi_welcome']);?></textarea><br><span class="C999">1.系统会在最前面加上网友“昵称”<br>2.内容请控制点在400字节以内<br>3.内有超链接源码，请在官方客服指导下进行修改，以防程序出错</span></td></tr> 
    <tr><td class="tdL">公众号自动回复信息：</td><td class="tdR lineH150"><textarea name="wxapi_hfcontent" cols="50" rows="8" class="textarea W400"><?php echo stripslashes($row['wxapi_hfcontent']);?></textarea><br><span class="C999">1.会员在公众号输入信息后，自动出现这个内容<br>2.内容请控制点在400字节以内<br>3.如果要加超链接源码，请在官方客服指导下进行修改，以防程序出错</span></td></tr> 
    <input name="kind" type="hidden" value="WeixinWelcome" />   
<?php }elseif($kind == 'WeixinPushInfo'){ ?>
	<tr><td height="40" colspan="2" align="left" class="S14 C999"><b>公众号信息推送</b><font class="S12">（会员关注公众号时或与公众号发生交互48小时以内会触发本推送）</font></td></tr>    
	<?php $pushkind = (empty($pushkind))?$row['wxapi_push_kind']:$pushkind; ?>
    <tr><td class="tdL">推送类型：</td><td class="tdR">
    <label for="ts1" onClick="openurl('<?php echo $SELF; ?>?kind=WeixinPushInfo&pushkind=text')"><input id="ts1" class="radio" type="radio" name="pushkind" value="text"<?php echo ($pushkind == 'text')?'checked':''; ?>>文本</label>　　
    <label for="ts2" onClick="openurl('<?php echo $SELF; ?>?kind=WeixinPushInfo&pushkind=pic')"><input id="ts2" class="radio" type="radio" name="pushkind" value="pic"<?php echo ($pushkind == 'pic')?'checked':''; ?>>图文</label>　　
    <label for="ts3" onClick="openurl('<?php echo $SELF; ?>?kind=WeixinPushInfo&pushkind=user')"><input id="ts3" class="radio" type="radio" name="pushkind" value="user"<?php echo ($pushkind == 'user')?'checked':''; ?>>推荐会员信息</label>
    </td></tr>
    <tr><td class="tdL">图文推送信息：</td><td class="tdR">
    
    
    
    <?php if ($pushkind == 'text'){ ?>
    <textarea name="wxapi_push_Text" cols="50" rows="8" class="textarea W400"><?php echo stripslashes($row['wxapi_push_Text']);?></textarea><br><span class="C999">1.内容请控制点在400字节以内<br>2.如果要加超链接源码，请在官方客服指导下进行修改，以防程序出错</span>
    
    
    <?php }elseif($pushkind == 'pic'){  ?>
		<style>
        .tablein{width:540px;border:#dedede 1px solid}
        .tablein td{border:0}
        .tablein tr td{border-bottom:#dedede 1px solid}
        .tablein tr:last-child td{border:0}
        </style>
        <table border="0" cellpadding="0" cellspacing="0" class="tablein">
        <tr><td width="50" height="35" align="right">标题：</td><td align="left"><input name="wxapi_push_PicTtitle" type="text" class="input" value="<?php echo stripslashes($row['wxapi_push_PicTtitle']);?>"size="50" maxlength="200"><span class="tips">100字节以内</span></td></tr>
        <tr><td height="35" align="right">图片：</td><td align="left">
		  <?php if (!empty($row['wxapi_push_PicPath'])) {?>
          <img src="<?php echo $_ZEAI['up2']."/".$row['wxapi_push_PicPath']; ?>" class="zoom" align="absmiddle" width="200" height="111" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".$row['wxapi_push_PicPath']; ?>')">　<a href="<?php echo $SELF;?>?submitok=delpicupdate2&kind=WeixinPushInfo&num=1" onClick="return confirm('确认删除？')"><img src="images/del.gif" alt="删除"></a>　
          <?php }else{echo "<input name=pic1 type=file size=30 class=input /><span class='tips'>（格式jpg，尺寸900x500像数）</span>";}?>
          <br><span class="C999">如果图片为空，将无法推送</span>
          </td></tr>
        <tr><td height="35" align="right">内容：</td><td align="left"><textarea name="wxapi_push_PicContent" cols="50" rows="3" class="textarea W300"><?php echo stripslashes($row['wxapi_push_PicContent']);?></textarea><span class="tips">100字节以内</span></td></tr>
        <tr><td height="35" align="right">链接：</td><td align="left"><input name="wxapi_push_PicUrl" type="text" class="input" value="<?php echo stripslashes($row['wxapi_push_PicUrl']);?>"size="50" maxlength="100">　<a href="news.php?kind=10" target="_blank" class="aLAN">素材库获取</a></td></tr>
        </table>    
    <?php }elseif($pushkind == 'user'){  ?>
    <input name="wxapi_push_Userlist" id="wxapi_push_Userlist" type="text" class="input W400" value="<?php echo stripslashes($row['wxapi_push_Userlist']);?>"size="50" maxlength="100">　<a href="###" class="aLAN" onClick="parent.ZEAI_win(600,600,'选择推荐会员','content_weixin_ulist.php?submitok=so')" title="选择推荐会员">选择会员</a>
    <?php }?>
    
    </td></tr>    
    <input name="kind" type="hidden" value="WeixinPushInfo" />
<?php }elseif($kind == 'LoginPaySms'){ ?>
	<style>	.tdL{width:150px}.input{width:300px}</style>
    
    <tr><td height="40" colspan="2" align="left" class="S14 C999"><b>微信支付</b><font class="S12 FR">微信支付平台帐号：<a href="https://pay.weixin.qq.com" target="_blank" class="aLAN">微信支付平台</a></font></td></tr>
    <tr><td class="tdL">微信支付商户号MCHID：</td><td class="tdR"><input name="wxapi_MCHID" type="text" class="input" value="<?php echo dataIO($row['wxapi_MCHID'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">微信支付商户密钥KEY：</td><td class="tdR"><input name="wxapi_KEY" type="text" class="input" value="<?php echo dataIO($row['wxapi_KEY'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">服务器IP：</td><td class="tdR"><input name="SiteIP" type="text" class="input" value="<?php echo dataIO($row['SiteIP'],'out');?>" maxlength="20" ></td></tr>
    <tr><td class="tdL">后台支付证书路径：</td><td class="tdR"><input name="wxapi_certpath" type="text" class="input" value="<?php echo $row['wxapi_certpath'];?>" maxlength="100" >
    <span class="tips">CERT证书绝对路径,如“D:\web\yzlove.com\admin\cert\”<br>请将apiclient_cert.pem、apiclient_key.pem、rootca.pem、apiclient_cert.p12放至该目录下</span></td></tr>
    
    <tr><td height="40" colspan="2" align="left" class="S14 C999"><b>微信登录</b><font class="S12 FR">注册微信开放平台帐号：<a href="https://open.weixin.qq.com" target="_blank" class="aLAN">微信开放平台</a></font></td></tr>    
    <tr><td class="tdL">微信开放平台appid：</td><td class="tdR"><input name="wxapi_open_appid" type="text" class="input" value="<?php echo dataIO($row['wxapi_open_appid'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">微信开放平台appsecret：</td><td class="tdR"><input name="wxapi_open_appsecret" type="text" class="input" value="<?php echo dataIO($row['wxapi_open_appsecret'],'out');?>" maxlength="100" ></td></tr>
    
    <tr><td height="40" colspan="2" align="left" class="S14 C999"><b>QQ登录</b><font class="S12 FR">注册QQ互联平台帐号：<a href="https://connect.qq.com" target="_blank" class="aLAN">QQ互联平台</a></font></td></tr>    
    <tr><td class="tdL">QQ网站应用appid：</td><td class="tdR"><input name="QQ_appid" type="text" class="input" value="<?php echo dataIO($row['QQ_appid'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">QQ网站应用appkey：</td><td class="tdR"><input name="QQ_appkey" type="text" class="input" value="<?php echo dataIO($row['QQ_appkey'],'out');?>" maxlength="100" ></td></tr>   
    
    <tr><td height="40" colspan="2" align="left" class="S14 C999"><b>新浪微博登录</b><font class="S12 FR">注册新浪微博开放平台帐号：<a href="http://open.weibo.com" target="_blank" class="aLAN">新浪微博开放平台</a></font></td></tr>    
    <tr><td class="tdL">新浪微博AppKey：</td><td class="tdR"><input name="weibo_AppKey" type="text" class="input" value="<?php echo dataIO($row['weibo_AppKey'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">新浪微博AppSercet：</td><td class="tdR"><input name="weibo_AppSercet" type="text" class="input" value="<?php echo dataIO($row['weibo_AppSercet'],'out');?>" maxlength="100" ></td></tr>    
	
    <tr><td height="40" colspan="2" align="left" class="S14 C999"><b>手机短信</b><font class="S12 FR">注册短信运营商帐号：<a href="http://www.rcscloud.cn" target="_blank" class="aLAN">美圣融云</a></font></td></tr>    
    <tr><td class="tdL">短信产品帐号：</td><td class="tdR"><input name="sms_sid" type="text" class="input" value="<?php echo dataIO($row['sms_sid'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">短信产品帐号apikey：</td><td class="tdR"><input name="sms_apikey" type="text" class="input" value="<?php echo dataIO($row['sms_apikey'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">验证码-模板id编号：</td><td class="tdR"><input name="sms_tplid_authcode" type="text" class="input" value="<?php echo dataIO($row['sms_tplid_authcode'],'out');?>" maxlength="100" ></td></tr>    
    <tr><td class="tdL">找回密码-模板id编号：</td><td class="tdR"><input name="sms_tplid_findpass" type="text" class="input" value="<?php echo dataIO($row['sms_tplid_findpass'],'out');?>" maxlength="100" ></td></tr>    
	<input name="kind" type="hidden" value="LoginPaySms" />
<?php }elseif($kind == 'basic'){ ?>
	<tr><td class="tdL">后台名称：</td><td class="tdR"><input name="SiteName_adm" type="text" class="input" id="SiteName_adm" value="<?php echo stripslashes($row['SiteName_adm']);?>"size="30" maxlength="100"><font class="tips">后台左上角名称，三个字为宜</font></td></tr>
	<tr><td class="tdL">网站名称：</td><td class="tdR"><input name="SiteName" type="text" class="input" id="SiteName" value="<?php echo stripslashes($row['SiteName']);?>"size="30" maxlength="100"><font class="tips">最简短,与seo无关</font></td></tr>
    
	<tr>
	<td class="tdL">首页Title标题：</td>
	<td class="tdR"><font color="#999999">
	<input name="SiteIndexTitle" type="text" class="input" id="SiteIndexTitle" value="<?php echo stripslashes($row['SiteIndexTitle']);?>"size="80" maxlength="100">
	</font></td>
	</tr>	
	<tr>
	<td class="tdL">首页关键词：</td>
	<td class="tdR"><font color="#999999">
	<input name="Keywords" type="text" class="input" id="Keywords" value="<?php echo stripslashes($row['Keywords']);?>"size="80" maxlength="100">
	<br>
	首页seo关键词(keywords),最好不要超过5个,以逗号隔开</font><input name="kind" type="hidden" value="basic" /></td>
	</tr>	
	<tr>
	<td class="tdL">首页seo简介：</td>
	<td class="tdR"><font color="#999999">
	<textarea name="Description" cols="50" rows="5" id="Description"><?php echo stripslashes($row['Description']);?></textarea>
	<br>
	首页seo网站简短介绍(description),100字以内</font></td>
	</tr>	
	<tr>
	  <td class="tdL">首页Logo右侧默认地区名称：</td>
	  <td class="tdR">
	    <input name="DefArea" type="text" class="input" id="DefArea" value="<?php echo stripslashes($row['DefArea']);?>"size="20" maxlength="50">
</td>
    </tr>	
	<tr>
	<td class="tdL">首页今日之星地区：</td>
	<td class="tdR">
<style>
#arealist{margin-top:10px}
#arealist a{margin:0 10px 0 0;color:#E75385}
</style>
    <?php 
	$areaid     = explode(',',$row['index_GBM']);
	$a1 = $areaid[0];$a2 = $areaid[1];$a3 = $areaid[2];
	?>
    <script>LevelMenu3('a1|a2|a3||<?php echo $a1; ?>|<?php echo $a2; ?>|<?php echo $a3; ?>');</script>
    <input name="areaid" id="areaid" type="hidden" value="0" />
    <div id="arealist"></div>
	<script>
		var a = '<?php echo $row['index_GBM']; ?>';
		if (!empty(a)){
			var a = a.split(','),C;
			if (a.length == 1){
				C = creat_area2(a[0]);
			}else if(a.length == 2 || a.length == 3){
				C = creat_area3(a[1]);
			}
			o('arealist').appendChild(C);
		}
		function creat_area2(area1id){
			var em2 = document.createElement('em');
			em2.innerHTML = '显示效果： <a>全部</a>';
			for (var k2 in areaid_ARR2){(function(k2){
				if (areaid_ARR2[k2].parentId == area1id){
					var A2  = document.createElement('a');
					A2.innerHTML = areaid_ARR2[k2].value;
					em2.appendChild(A2);
				}
			})(k2);}
			return em2;
		}
		function creat_area3(area2id){
			var em3 = document.createElement('em');
			em3.innerHTML = '显示效果： <a>全部</a>';
			for (var k3 in areaid_ARR3){(function(k3){
				if (areaid_ARR3[k3].parentId == area2id){
					var A3  = document.createElement('a');
					A3.innerHTML = areaid_ARR3[k3].value;
					em3.appendChild(A3);
				}
			})(k3);}
			return em3;
		}

    </script>
    </td>
          <tr>
            <td align="right" bgcolor="#f8f8f8"><span class="tdL">首页</span>Banner广告①</td>
            <td align="left" valign="top">
			


<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
  <tr>
    <td width="50%"><?php if (!empty($row['path1_s'])) {?>
	<img src="<?php echo $_ZEAI['up2']."/".$row['path1_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_b($row['path1_s']); ?>')">　<a href="<?php echo $SELF;?>?submitok=delpicupdate&kind=basic&num=1" onClick="return confirm('确认删除？')"><img src="images/del.gif" alt="删除"></a>　
<?php }else{ 
	echo "<input name=pic1 type=file size=30 class=input />";
}?></td>
    <td width="50%">链接①
      <input name="path1_url" type="text" class="input" id="path1_url" value="<?php echo stripslashes($row['path1_url']);?>"size="50" maxlength="100"></td>
  </tr>
</table></td>
</tr>
<tr>
<td align="right" bgcolor="#f8f8f8"><span class="tdL">首页</span>Banner广告1②</td>
<td align="left" valign="top">

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
  <tr>
    <td width="50%"><?php if (!empty($row['path2_s'])) {?>
	<img src="<?php echo $_ZEAI['up2']."/".$row['path2_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_b($row['path2_s']); ?>')">　<a href="<?php echo $SELF;?>?submitok=delpicupdate&kind=basic&num=2" onClick="return confirm('确认删除？')"><img src="images/del.gif" alt="删除"></a>　
<?php }else{ 
	echo "<input name=pic2 type=file size=30 class=input />";
}?></td>
    <td width="50%">链接②
      <input name="path2_url" type="text" class="input" id="path2_url" value="<?php echo stripslashes($row['path2_url']);?>"size="50" maxlength="100"></td>
  </tr>
</table>
</td>
          </tr>
          <tr>
            <td align="right" bgcolor="#f8f8f8"><span class="tdL">首页</span>Banner广告1③</td>
            <td align="left" valign="top">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
<tr>
<td width="50%"><?php if (!empty($row['path3_s'])) {?>
<img src="<?php echo $_ZEAI['up2']."/".$row['path3_s']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_b($row['path3_s']); ?>')">　<a href="<?php echo $SELF;?>?submitok=delpicupdate&kind=basic&num=3" onClick="return confirm('确认删除？')"><img src="images/del.gif" alt="删除"></a>　
<?php }else{ 
echo "<input name=pic3 type=file size=30 class=input />";
}?></td>
<td width="50%">链接③
<input name="path3_url" type="text" class="input" id="path3_url" value="<?php echo stripslashes($row['path3_url']);?>"size="50" maxlength="100"></td>
</tr>
</table>
</td></tr>
<tr><td class="tdL">PC端底部版权信息：</td><td class="tdR"><textarea name="pc_bottominfo" class="textarea W500"><?php echo dataIO($row['pc_bottominfo'],'out');?></textarea></td></tr>    
<?php }elseif($kind == 'basic_set'){ ?>

	<style>.tdL{width:200px}.input{width:300px}.li{font-size:12px;line-height:150%;padding:20px 0 0 10px;color:#999}.li font{color:#f00}</style>
	<tr><td height="40" colspan="2" align="left" class="S14 C999"><b>网站功能设置</b></td></tr>    
    <tr><td class="tdL">
    	推广奖励注册（人民币）：</td><td class="tdR S14" style="padding-top:15px">
        <label for="tg_if1"><input id="tg_if1" class="radio" type="radio" name="tg_if" value="1"<?php echo ($row['tg_if'] == 1)?'checked':''; ?>>开启</label>　　
        <label for="tg_if2"><input id="tg_if2" class="radio" type="radio" name="tg_if" value="0"<?php echo ($row['tg_if'] == 0)?'checked':''; ?>>关闭</label>　　
        <div class="li">
    	●1.<?php echo $_ZEAI['Grade1Name']."会员推荐注册将获得 <font>".($_ZEAI['money_tg']*1)."元</font>/人";?>
    	<br>●2.<?php echo $_ZEAI['Grade2Name']."会员推荐注册将获得 <font>".($_ZEAI['money_tg']*2)."元</font>/人";?>
    	<br>●3.<?php echo $_ZEAI['Grade3Name']."会员推荐注册将获得 <font>".($_ZEAI['money_tg']*3)."元</font>/人";?>
    	<br>●4.<?php echo $_ZEAI['Grade4Name']."会员推荐注册将获得 <font>".($_ZEAI['money_tg']*4)."元</font>/人";?>
        <br>●在会员管理中确认审核打款后，将钱打入会员人民币账户，如果会员提现(扣除手续费<?php echo (1-$_ZEAI['money_out_fee'])*100; ?>%)，<?php echo ($_ZEAI['money_out_fee'])*100; ?>%资金自动打到会员的微信钱包。
        </div>
    </td></tr>
    <tr><td class="tdL">完善资料等任务奖励（<?php echo $_ZEAI['LoveB']; ?>）：</td><td class="tdR" style="padding-top:15px">
        <label for="task_if1"><input id="task_if1" class="radio" type="radio" name="task_if" value="1"<?php echo ($row['task_if'] == 1)?'checked':''; ?>>开启</label>　　
        <label for="task_if2"><input id="task_if2" class="radio" type="radio" name="task_if" value="0"<?php echo ($row['task_if'] == 0)?'checked':''; ?>>关闭</label>　　
        <div class="li">
        ●1. 完善资料80%以上 <font>+<?php echo $_ZEAI['task_myinfo']; ?></font>
        <br>●2.上传形象照 <font>+<?php echo $_ZEAI['task_photo_s']; ?></font>
        <br>●3.完成一项认证 <font>+<?php echo $_ZEAI['task_honor']; ?></font>
        <br>●4.推荐会员注册 <font>+<?php echo $_ZEAI['task_tgreg']; ?></font>
        <br>●5.发布相册3张 <font>+<?php echo $_ZEAI['task_photo']; ?></font>
        <br>●6.发布视频语音 <font>+<?php echo $_ZEAI['task_video']; ?></font>
        </div>
    </td></tr>
    <input name="kind" type="hidden" value="basic_set" />

<?php }elseif($kind == 'WeixinTemplate'){ ?>
	<style>	.tdL{width:180px}.input{width:320px}</style>
    <tr><td height="40" colspan="2" align="left" class="S14 C999"><b>微信模板消息编号设置</b><font class="S12 FR">进入公众平台获取：<a href="https://mp.weixin.qq.com" target="_blank" class="aLAN">微信公众平台</a></font></td></tr>    
    <tr><td class="tdL">帐户资金变动提醒 模板ID：</td><td class="tdR"><input name="wxapi_mb_loveb" type="text" class="input" value="<?php echo dataIO($row['wxapi_mb_loveb'],'out');?>" maxlength="50" ><span class="tips">到模板库搜索“帐户资金变动提醒”选中,添加后可获得ID</span></td></tr>    
    <tr><td class="tdL">用户咨询提醒 模板ID：</td><td class="tdR"><input name="wxapi_mb_msgchat" type="text" class="input" value="<?php echo dataIO($row['wxapi_mb_msgchat'],'out');?>" maxlength="50" ><span class="tips">到模板库搜索“用户咨询提醒”选中,添加后可获得ID</span></td></tr>
    <tr><td class="tdL">会员资料审核提醒 模板ID：</td><td class="tdR"><input name="wxapi_mb_userdata" type="text" class="input" value="<?php echo dataIO($row['wxapi_mb_userdata'],'out');?>" maxlength="50" ><span class="tips">到模板库搜索“会员资料审核提醒”选中,添加后可获得ID</span></td></tr>
    <tr><td class="tdL">后台操作提醒 模板ID：</td><td class="tdR"><input name="wxapi_mb_admininfo" type="text" class="input" value="<?php echo dataIO($row['wxapi_mb_admininfo'],'out');?>" maxlength="50" ><span class="tips">到模板库搜索“后台操作提醒”选中,添加后可获得ID</span></td></tr>
    <tr><td class="tdL">认证通知 模板ID：</td><td class="tdR"><input name="wxapi_mb_honor" type="text" class="input" value="<?php echo dataIO($row['wxapi_mb_honor'],'out');?>" maxlength="50" ><span class="tips">到模板库搜索“认证通知”选中,添加后可获得ID</span></td></tr>
    <tr><td class="tdL">到账提醒 模板ID：</td><td class="tdR"><input name="wxapi_mb_msggift" type="text" class="input" value="<?php echo dataIO($row['wxapi_mb_msggift'],'out');?>" maxlength="50" ><span class="tips">到模板库搜索“到账提醒”选中,添加后可获得ID</span></td></tr>
	<input name="kind" type="hidden" value="WeixinTemplate" />
<?php }elseif($kind == 'pay'){ ?>
	<tr>
	  <td colspan="2" align="left" bgcolor="#FFFFFF" class="B" style="padding:10px">财付通：<br>
	    <table width="690" border="0" cellpadding="2"  cellspacing="0" style="margin:10px">
          <tr>
            <td width="117" align="right" bgcolor="#f8f8f8">商户名称：</td>
            <td width="553"><font color="#999999">
              <input name="tenpay1" type="text" class="input" id="tenpay1" value="<?php echo stripslashes($row['tenpay1']);?>"size="50" maxlength="100">
            </font></td>
          </tr>
          <tr>
            <td class="tdL">商户号：</td>
            <td><font color="#999999">
              <input name="tenpay2" type="text" class="input" id="tenpay2" value="<?php echo stripslashes($row['tenpay2']);?>"size="50" maxlength="100">
              如：1900000113</font></td>
          </tr>
          <tr>
            <td class="tdL">密　钥：</td>
            <td><font color="#999999">
              <input name="tenpay3" type="text" class="input" id="tenpay3" value="<?php echo stripslashes($row['tenpay3']);?>"size="50" maxlength="100">
            </font></td>
          </tr>
        </table></td>
    </tr>	
	<tr>
	<td colspan="2" align="left" bgcolor="#FFFFFF" class="B" style="padding:10px">支付宝：<input name="kind" type="hidden" value="pay" /><br>
	  <table width="690" border="0" cellpadding="2" cellspacing="0"  style="margin:10px">
        <tr>
          <td width="117" align="right" bgcolor="#f8f8f8">合作身份者ID：</td>
          <td width="553"><font color="#999999">
            <input name="alipay1" type="text" class="input" id="alipay1" value="<?php echo stripslashes($row['alipay1']);?>"size="50" maxlength="100">
            以2088开头的16位纯数字</font></td>
        </tr>
        <tr>
          <td class="tdL">支付宝账号：</td>
          <td><font color="#999999">
            <input name="alipay2" type="text" class="input" id="alipay2" value="<?php echo stripslashes($row['alipay2']);?>"size="50" maxlength="100">
            签约支付宝账号或卖家支付宝账户</font></td>
        </tr>
        <tr>
          <td class="tdL">安全检验码：</td>
          <td><font color="#999999">
            <input name="alipay3" type="text" class="input" id="alipay3" value="<?php echo stripslashes($row['alipay3']);?>"size="50" maxlength="100">
          以数字和字母组成的32位字符</font></td>
        </tr>
      </table></td>
	</tr>	<tr>
	<td colspan="2" align="left" bgcolor="#FFFFFF" class="B" style="padding:10px">银行账号：
	  <table width="690" border="0" cellpadding="5" cellspacing="0"  style="margin:10px">
        <tr>
          <td align="left" bgcolor="#f8f8f8"><textarea name="content" class="textarea_k" id="content" style="width:100%;height:200px"><?php echo stripslashes($row['bank']);?></textarea></td>
          </tr>
      </table></td>
	</tr>	
<?php }?>



<tr>
<td colspan="2" align="center" bgcolor="#FFFFFF">
<input name="submitok" type="hidden" value="mod" /><input type="submit" name="Submit" value=" 保存并修改 " class="btn2"></td>
</tr>

</table>
</form>
<br><br><br><br>
<?php require_once 'bottomadm.php';?>
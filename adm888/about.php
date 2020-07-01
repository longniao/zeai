<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';

if(!in_array('about',$QXARR))exit(noauth());

header("Cache-control: private");
if ($submitok == "us_update") {
	if(str_len($content)>40000 || str_len($content)<2){alert_adm("内容不能为空且不超过20000个字节，1个汉字等于2个字节！","-1");}
	$db->query("UPDATE ".__TBL_NEWS__." SET content='".dataIO($content,'in',40000)."' WHERE id=2");
	AddLog('【基础设置】->【关于我们】修改');
	alert_adm("修改成功","about.php?t=us");
}elseif($submitok == "declara_update"){
	if(str_len($content)>40000 || str_len($content)<2){alert_adm("内容不能为空且不超过20000个字节，1个汉字等于2个字节！","-1");}
	$db->query("UPDATE ".__TBL_NEWS__." SET content='".dataIO($content,'in',40000)."' WHERE id=1");
	AddLog('【基础设置】->【会员条款/免责声明】修改');
	alert_adm("修改成功","about.php?t=us");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<!-- editor -->
<link rel="stylesheet" href="editor/themes/default/default.css" />
<script charset="utf-8" src="editor/kindeditor.js"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js"></script>
<script>
var editor;
KindEditor.ready(function(K){
  editor=K.create('textarea[name="content"]',{
	resizeType : 1,
	cssData:'body {font-family: "微软雅黑"; font-size: 14px}',
	minWidth : 400,
	allowPreviewEmoticons : true,
	allowImageUpload : true,
	afterBlur:function(){this.sync();},
	items : [
		'undo','redo','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','removeformat', '|', 'insertorderedlist','insertunorderedlist', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull','lineheight','|',
		'selectall','quickformat', '|','image','multiimage','media', '|','plainpaste','wordpaste','hr','table', 'link', 'unlink','baidumap', '|','clearhtml','source', '|','preview','fullscreen']
  });
});
</script>
<!--editor end -->
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<div class="navbox">
  <a href="<?php echo SELF;?>?t=us"<?php echo ($t == 'us' || empty($t))?' class="ed"':'';?>>关于我们</a>
  <a href="<?php echo SELF;?>?t=contact"<?php echo ($t == 'contact')?' class="ed"':'';?>>联系我们/客服信息</a>
  <a href="<?php echo SELF;?>?t=declara"<?php echo ($t == 'declara')?' class="ed"':'';?>>会员条款/免责声明</a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>


<?php if ($t == "us") {
	$row = $db->ROW(__TBL_NEWS__,"content","id=2","num");if ($row){$C=dataIO($row[0],'wx');}else{callmsg("zeai_id_error","-1");}
	?>
    <table class="table0 W98_ Mtop20">
    <form name="GYLform" id="GYLform" action="<?php echo SELF; ?>" method="post">
    <tr><td align="center"><textarea name="content" id="content" class="textarea_k" style="width:100%;height:500px"><?php echo $C; ?></textarea></td></tr>
    <tr><td height="100" align="center"><input class="btn size3 HUANG3" type="submit" value="确定并保存"/>
    <input name="submitok" type="hidden" value="us_update" /></td>
    </tr></form></table>

<?php }elseif($t == "contact") {?>

    <table class="table W98_ Mtop20">
    <form name="ZEAIFORM" id="ZEAIFORM" method="post" enctype="multipart/form-data">
    <tr><td class="tdL">手机</td>
      <td class="tdR">
        <input name="kf_mob" type="text" class="input" id="kf_mob" value="<?php echo dataIO($_ZEAI['kf_mob'],'out');?>"size="50" maxlength="100">
      </td>
    </tr>
    <tr><td class="tdL">电话</td>
      <td class="tdR">
        <input name="kf_tel" type="text" class="input" id="kf_tel" value="<?php echo dataIO($_ZEAI['kf_tel'],'out');?>"size="50" maxlength="100">
      </td>
    </tr>
    <tr><td class="tdL">微信号</td><td class="tdR"><input name="kf_wx" id="kf_wx" type="text" class="input" value="<?php echo dataIO($_ZEAI['kf_wx'],'out');?>" size="50" maxlength="100" ></td></tr>
    <tr>
      <td class="tdL">微信二维码</td><td class="tdR">
			<?php if (!empty($_ZEAI['kf_wxpic'])) {?>
                <input name='logo_' type='hidden' value="<?php echo $_ZEAI['kf_wxpic'];?>" />
                <a class="pic60" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.$_ZEAI['kf_wxpic']; ?>')"><img src="<?php echo $_ZEAI['up2'].'/'.$_ZEAI['kf_wxpic']; ?>?"></a>　
                <a href="javascript:;" id="kf_wxpicdel" class="btn size1">删除</a>　
            <?php }else{echo "<input name='kf_wxpic' type='file' size='50' class='Caaa W300' /><br><span class='tips2'>必须.png/.gif/.png格式</span>";}?>					
      </td></tr>
    <tr>
    <td class="tdL">QQ</td>
    <td class="tdR">
    <input name="kf_qq" type="text" class="input" id="kf_qq" value="<?php echo dataIO($_ZEAI['kf_qq'],'out');?>" size="50" maxlength="100">
    </td>
    </tr>
    <tr>
      <td class="tdL">邮箱</td>
      <td class="tdR">
        <input name="kf_email" type="text" class="input" id="kf_email" value="<?php echo dataIO($_ZEAI['kf_email'],'out');?>"size="50" maxlength="100">
      </td>
    </tr>    
    <tr>
      <td class="tdL">地址</td>
      <td class="tdR">
        <input name="kf_address" type="text" class="input" id="kf_address" value="<?php echo dataIO($_ZEAI['kf_address'],'out');?>"size="50" maxlength="100">
      </td>
    </tr>    
	<input name="uu" type="hidden" value="<?php echo $session_uid;?>">
	<input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
    
    
    
    
    <tr>
      <td class="tdL">&nbsp;</td>
      <td class="tdR"><input class="btn size3 HUANG3" type="button" id="save" value="确定并保存"/>
      <input name="submitok" type="hidden" value="contact_update" /></td>
    </tr>
    </form>
    </table>
	<script>
    save.onclick = function(){
        zeai.confirm('确认要修改么？此修改将触发缓存机制同步更新，立即生效！',function(){
            zeai.msg('正在保存设置...',{time:20});
            zeai.ajax({"url":'<?php echo HOST;?>/sub/cache'+zeai.extname,"form":ZEAIFORM},function(e){
				var rs=zeai.jsoneval(e);
                zeai.msg(0);
                if (rs.flag == 1){zeai.alert(rs.msg,'about.php?t=contact');}else{zeai.alert(rs.msg);}
            });
        });
    }
	var uu=<?php echo $_SESSION['admuid'];?>,pp='<?php echo $_SESSION['admpwd'];?>';
	if (!zeai.empty(o('kf_wxpicdel')))o('kf_wxpicdel').onclick = function(){delpic('kf_wxpic_delupdate');}
	function delpic(submitok){
		zeai.confirm('确认要删除么？',function(){
			zeai.msg('删除中...',{time:20});
			
			
			zeai.ajax({"url":'<?php echo HOST;?>/sub/cache'+zeai.extname,"data":{"submitok":submitok,"uu":uu,"pp":pp}},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);
				if (rs.flag == 1){location.reload(true);}else{zeai.alert(rs.msg);}
			});
		});
	}
    </script>
<?php }elseif($t == "declara") {
	$row = $db->ROW(__TBL_NEWS__,"content","id=1","num");if ($row){$C=dataIO($row[0],'out');}else{callmsg("zeai_id_error","-1");}
	?>
    <table class="table0 W98_ Mtop20">
    <form name="GYLform" id="GYLform" action="<?php echo SELF; ?>" method="post">
    <tr><td align="center"><textarea name="content" id="content" class="textarea_k" style="width:100%;height:500px"><?php echo $C; ?></textarea></td></tr>
    <tr><td height="100" align="center"><input class="btn size3 HUANG3" type="submit" value="确定并保存"/>
    <input name="submitok" type="hidden" value="declara_update" /></td>
    </tr></form></table>
<?php }
require_once 'bottomadm.php';?>
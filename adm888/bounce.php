<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_vip.php';
$bounce=json_decode($_ZEAI['bounce'],true);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<style>
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
::-webkit-input-placeholder{color:#bbb;font-size:14px}
.zoom{background-color:#666;padding:20px}
</style>
<body>
<div class="navbox">
    <a class="ed">反弹海报</a>
    <div class="Rsobox">
    
    </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<!---->
<div class="clear"></div> 
<form id="W_W_W_Z_E__A_I__C_N__FORM">

<?php
if ($t == 'vipdatarz'){
	if(!in_array('bounce_vip',$QXARR))exit(noauth());
	?>
<table class="table size1 W1200" style="margin:20px 0 0 20px">
	<tr><th colspan="2" align="left">反弹海报　（尺寸：<font class="Cf00">600*680</font>像数，类型:透明png或jpg）图片大小请控制在200K以内，越小打开速度越快</th></tr>
	<tr>
		<td class="tdL">状态</td>
		<td class="tdR">
        <table class="table0" style="margin:0">
          <tr>
            <td align="left"><input type="checkbox" name="bounce_flag_vipdatarz" id="bounce_flag_vipdatarz" class="switch" value="1"<?php echo ($bounce['flag']['vipdatarz'] == 1)?' checked':'';?>><label for="bounce_flag_vipdatarz" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　<span class="tips">开启后，在手机端【消息】【我的】【会员展示页】会依次蹦出vip升级、完善资料、实名认证海报（如果已是vip或资料完善后将不弹出）</span>
</td>
          </tr>

        </table></td>
	</tr> 
    
	<tr>
		<td class="tdL">VIP升级海报</td>
		<td class="tdR">
        
		<?php if (!empty($bounce['vip_picurl'])) {?>
            <img width="125" height="142" src="<?php echo $_ZEAI['up2']."/".$bounce['vip_picurl']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($bounce['vip_picurl'],'b'); ?>')">　
            <a href="###" onClick="bounceDel(1)" class="btn size1" >删除</a>　<span class="tips">删除后将不再弹出</span>
        <?php }else{ 
            echo "<input name=pic1 type=file size=30 class='input size2' />";
        }?>
        
	</td>
	</tr>    
	<tr>
		<td class="tdL">完善资料海报</td>
		<td class="tdR">
        
		<?php if (!empty($bounce['my_info_picurl'])) {?>
            <img width="125" height="142" src="<?php echo $_ZEAI['up2']."/".$bounce['my_info_picurl']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($bounce['my_info_picurl'],'b'); ?>')">　
            <a href="###" onClick="bounceDel(2)" class="btn size1" >删除</a>　<span class="tips">删除后将不再弹出</span>
        <?php }else{ 
            echo "<input name=pic2 type=file size=30 class='input size2' />";
        }?>
        
        
        </td>
	</tr> 
	<tr>
		<td class="tdL">实名认证海报</td>
		<td class="tdR">
        
		<?php if (!empty($bounce['rz_picurl'])) {?>
            <img width="125" height="142" src="<?php echo $_ZEAI['up2']."/".$bounce['rz_picurl']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($bounce['rz_picurl'],'b'); ?>')">　
            <a href="###" onClick="bounceDel(3)" class="btn size1" >删除</a>　<span class="tips">删除后将不再弹出</span>
        <?php }else{ 
            echo "<input name=pic3 type=file size=30 class='input size2' />";
        }?>
        
        </td>
	</tr> 
       
       
	<tr>    
		<td colspan="2" align="center">
		<input name="submitok" type="hidden" value="cache_bounce_vipdatarz">
		<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
		</td>
	</tr>

</table>
<?php }elseif($t == 'indexgg'){
	if(!in_array('bounce_index',$QXARR))exit(noauth());
	?>


<table class="table size1 W1200" style="margin:20px 0 0 20px">
	<tr><th colspan="2" align="left">首页海报公告　（尺寸：<font class="Cf00">600*680</font>像数，类型:透明png或jpg）图片大小请控制在200K以内，越小打开速度越快</th></tr>
	<tr>
		<td class="tdL">状态</td>
		<td class="tdR">
        <table class="table0" style="margin:0">
          <tr>
            <td align="left"><input type="checkbox" name="bounce_flag_indexgg" id="bounce_flag_indexgg" class="switch" value="1"<?php echo ($bounce['flag']['indexgg'] == 1)?' checked':'';?>><label for="bounce_flag_indexgg" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　<span class="tips">开启后，在手机端【首页】会蹦出此海报（每天只弹出一次）</span>
</td>
          </tr>

        </table></td>
	</tr> 
    
	<tr>
		<td class="tdL">首页海报</td>
		<td class="tdR">
        
		<?php if (!empty($bounce['indexgg_picurl'])) {?>
            <img width="125" height="142" src="<?php echo $_ZEAI['up2']."/".$bounce['indexgg_picurl']; ?>" class="zoom" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".getpath_smb($bounce['indexgg_picurl'],'b'); ?>')">　
            <a href="#" onClick="bounceDel(4)" class="btn size1" >删除</a>　
        <?php }else{ 
            echo "<input name=pic4 type=file size=30 class='input size2' />";
        }?>
        
	</td>
	</tr>    
	<tr>
		<td class="tdL">URL网址链接</td>
		<td class="tdR"><input name="indexgg_url" type="text" class="input size2 W500" id="indexgg_url" value="<?php echo trimhtml($bounce['indexgg_url']);?>"size="50" maxlength="100"></td>
	</tr> 
       
       
	<tr>    
		<td colspan="2" align="center">
		<input name="submitok" type="hidden" value="cache_bounce_indexgg">
		<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
		</td>
	</tr>

</table>
<?php }?>

<input id="uu" name="uu" type="hidden" value="<?php echo $session_uid;?>">
<input id="pp" name="pp" type="hidden" value="<?php echo $session_pwd;?>">
</form>
<script>
	save.onclick = function(){
		zeai.msg('正在更新中',{time:30});
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:W_W_W_Z_E__A_I__C_N__FORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}
		});
	}
	function bounceDel(i){
		zeai.confirm('确认要删除么？',function(){
			zeai.msg('删除中...',{time:20});
			zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:'bounceDel',uu:uu.value,pp:pp.value,i:i}},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
</script>
<br><br><br>
<?php require_once 'bottomadm.php';?>
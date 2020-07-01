<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
//if(!in_array('bounce',$QXARR))exit(noauth());
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/cache/areadata.js"></script>
<script src="<?php echo HOST;?>/res/select3.js"></script>

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
    <a href="agent.php" class="ed">代理加盟商管理</a>
    <div class="Rsobox">
    
    </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<!---->
<div class="clear"></div> 
<script>
var nulltext = '不限';
var mate_areaid_ARR1 = areaARR1;
var mate_areaid_ARR2 = areaARR2;
var mate_areaid_ARR3 = areaARR3;

function chkform(){
	var m1 = get_option('m1','v');
	var m2 = get_option('m2','v');
	var m3 = get_option('m3','v');
	var m1t = get_option('m1','t');
	var m2t = get_option('m2','t');
	var m3t = get_option('m3','t');
	m1t = (nulltext == m1t)?'':m1t;
	m2t = (nulltext == m2t)?'':' '+m2t;
	m3t = (nulltext == m3t)?'':' '+m3t;
	m1 = (m1 == 0)?'':m1;
	m2 = (m2 == 0)?'':','+m2;
	m3 = (m3 == 0)?'':','+m3;
	var mate_areaid = m1 + m2 + m3;
	mate_areaid = (mate_areaid == '0,0,0')?'':mate_areaid;
	var mate_areatitle = m1t + m2t + m3t;
	o('mate_areaid').value = mate_areaid;
	o('mate_areatitle').value = mate_areatitle;
}</script>

<form id="W_W_W_Z_E__A_I__C_N__FORM">

<table class="table W1200 Mtop20  size2 cols2" style="margin:20px 0 0 20px">
	<tr><th colspan="2" align="left">代理帐号信息</th></tr>
	<tr>
		<td class="tdL">状态</td>
		<td class="tdR">
        <input type="checkbox" name="bounce_flag_indexgg" id="bounce_flag_indexgg" class="switch" value="1"<?php echo ($bounce['flag']['indexgg'] == 1)?' checked':'';?>><label for="bounce_flag_indexgg" class="switch-label"><i></i><b>开启</b><b>停止</b></label>　<span class="tips">【开启】后正常登录使用，【关闭】后冻结代理帐号不能登录管理</span></td>
	</tr> 
    
	<td class="tdL">登录帐号</td>
	<td class="tdR"><input id="username" name="username" type="text" class="input W200 size2" maxlength="20" value="<?php echo $username;?>"><span class="tips">3~20位英文母或与数字组合</span></td>
	</tr>
	<tr>
    <tr>
    <td class="tdL">登录密码</td>
    <td class="tdR"><input name="password" type="text" required class="input size2 W200" id="password" size="50" maxlength="20" /><span class="tips">长度6~20</span></td>
    </tr> 
    <tr>
    <td class="tdL">共享总站会员库</td>
    <td class="tdR"><input type="checkbox" name="agent_ushare" id="agent_ushare" class="switch" value="1"<?php echo ($bounce['flag']['indexgg'] == 1)?' checked':'';?>><label for="agent_ushare" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　<span class="tips">【开启】后代理商后台将显示总站全部会员数据，【关闭】后只显示自己名下会员</span></td>
    </tr>
    <tr>
    <td class="tdL">绑定地区</td>
    <td class="tdR">
	<script>LevelMenu3('m1|m2|m3|'+nulltext+'|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>');</script>
    <span class="tips">选择后，代理商后台只能显示此地区会员，不限将显示全部地区</span>
    </td>
    </tr>
    <tr>
    <td class="tdL">手　机</td>
    <td class="tdR"><input name="mob" type="text" required class="input W300 size2" id="mob" value="<?php echo $mob;?>" maxlength="50" /></td>
    </tr>
    <tr>
    <td class="tdL">手　机</td>
    <td class="tdR"><input name="mob" type="text" required class="input W300 size2" id="mob" value="<?php echo $mob;?>" maxlength="50" /></td>
    </tr>
    <tr>
    <td class="tdL">QQ</td>
    <td class="tdR"><input name="qq" type="text" required class="input W300 size2" id="qq" value="<?php echo $qq;?>"  maxlength="20" /></td>
    </tr>
    <tr>
    <td class="tdL">微　信</td>
    <td class="tdR"><input name="weixin" type="text" class="input W300 size2" id="weixin" value="<?php echo $weixin;?>"  maxlength="50" /></td>
    </tr>    

	<tr>
		<td class="tdL">URL网址链接</td>
		<td class="tdR"><input name="indexgg_url" type="text" class="input size2 W500" id="indexgg_url" value="<?php echo trimhtml($bounce['indexgg_url']);?>"size="50" maxlength="100"></td>
	</tr> 
       
       
	<tr>    
		<td colspan="2" align="center">
		<input name="submitok" type="hidden" value="ssss">
        <input name="mate_areaid" id="mate_areaid" type="hidden" value="" />
        <input name="mate_areatitle" id="mate_areatitle" type="hidden" value="" />
		<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
		</td>
	</tr>

</table>

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
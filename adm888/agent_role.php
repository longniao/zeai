<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
//if(!in_array('bounce',$QXARR))exit(noauth());


$AUTH = array();
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
::-webkit-input-placeholder{color:#bbb;font-size:12px}
.zoom{background-color:#666;padding:20px}
</style>
<body>
<div class="navbox">
    <a href="agent.php" class="ed">代理商用户组</a>
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

	<tr><th colspan="2" align="left"><?php echo ($submitok == 'add')?'新增':'修改';?>代理商用户组</th></tr>
    <tr>
    <td class="tdL">用户组名称</td>
    <td class="tdR"><input id="title" name="title" type="text" class="W300 size2" size="30" maxlength="20" placeholder="如省级代理，县级代理，分店1，分店2等" value="<?php echo $title;?>"></td>
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
    <td class="tdL">分配权限</td>
    <td class="tdR">
    
    
<table cellpadding="0" cellspacing="0" class="table0" style="width:900px;margin:0 0 0 10px">
<tr><td colspan="5" style="border-bottom:#990000 0px dotted; height:30px;">
<span class="btnbox gray"><script>
var checkgrade = "false";
function check(field) {
	if (checkgrade == "false") {
		for (i = 0; i < field.length; i++) {
			field[i].checked = true;
		}
		checkgrade = "true";
		return "取消"; 
	} else {
	for (i = 0; i < field.length; i++) {
		field[i].checked = false;
		}
		checkgrade = "false";
		return "全选"; 
	}
} 
</script><input class="btn size2" type="button" value="全选" accesskey="a" onClick="this.value=check(this.form)" /></span>
</td>
</tr>

<tr><td height="40" colspan="5" align="left" valign="bottom" class="B">会员管理</td></tr>
<tr>
  <td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u" value="u" <?php echo (in_array('u',$AUTH))?' checked':'';?>><label for="u" class="checkskin-label"><i class="i1"></i><b class="W100">登录会员中心操作</b></label></td>
  <td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_select" value="u_select" <?php echo (in_array('u_select',$AUTH))?' checked':'';?>><label for="u_select" class="checkskin-label"><i class="i1"></i><b class="W100">会员查询/筛选</b></label></td>
  <td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_reg" value="switchs_reg" <?php echo (in_array('switchs_reg',$AUTH))?' checked':'';?>><label for="switchs_reg" class="checkskin-label"><i class="i1"></i><b class="W100">会员录入/修改</b></label></td>
  <td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="udata" value="udata" <?php echo (in_array('udata',$AUTH))?' checked':'';?>><label for="udata" class="checkskin-label"><i class="i1"></i><b class="W100">会员删除</b></label></td>
  <td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_loveb" value="switchs_loveb" <?php echo (in_array('switchs_loveb',$AUTH))?' checked':'';?>><label for="switchs_loveb" class="checkskin-label"><i class="i1"></i><b class="W100"><?php echo $_ZEAI['loveB'];?>账户操作</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_shkg" value="switchs_shkg" <?php echo (in_array('switchs_shkg',$AUTH))?' checked':'';?>><label for="switchs_shkg" class="checkskin-label"><i class="i1"></i><b class="W100">余额账户操作</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="robot" value="robot" <?php echo (in_array('robot',$AUTH))?' checked':'';?>><label for="robot" class="checkskin-label"><i class="i1"></i><b class="W100">会员锁定操作</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="urole" value="urole" <?php echo (in_array('urole',$AUTH))?' checked':'';?>><label for="urole" class="checkskin-label"><i class="i1"></i><b class="W100">升级会员VIP</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_mod_pass" value="u_mod_pass" <?php echo (in_array('u_mod_pass',$AUTH))?' checked':'';?>><label for="u_mod_pass" class="checkskin-label"><i class="i1"></i><b class="W100">资料导出</b></label></td>
<td width="153" height="20" align="left"></td>
</tr>

<tr><td height="40" colspan="5" align="left" valign="bottom" class="B">会员审核</td></tr>
<tr>
  <td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u" value="u" <?php echo (in_array('u',$AUTH))?' checked':'';?>><label for="u" class="checkskin-label"><i class="i1"></i><b class="W100">会员资料审核</b></label></td>
  <td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_select" value="u_select" <?php echo (in_array('u_select',$AUTH))?' checked':'';?>><label for="u_select" class="checkskin-label"><i class="i1"></i><b class="W100">会员头像审核</b></label></td>
  <td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_reg" value="switchs_reg" <?php echo (in_array('switchs_reg',$AUTH))?' checked':'';?>><label for="switchs_reg" class="checkskin-label"><i class="i1"></i><b class="W100">会员相册审核</b></label></td>
  <td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="udata" value="udata" <?php echo (in_array('udata',$AUTH))?' checked':'';?>><label for="udata" class="checkskin-label"><i class="i1"></i><b class="W100">会员认证审核</b></label></td>
  <td width="153" height="20" align="left"></td>
</tr>

<tr>
  <td height="30" align="left">&nbsp;</td>
  <td height="20" align="left">&nbsp;</td>
  <td height="20" align="left">&nbsp;</td>
  <td height="20" align="left">&nbsp;</td>
  <td height="20" align="left">&nbsp;</td>
</tr>
</table>    
    </td>
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
<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_reg.php';$TG_set = json_decode($_REG['TG_set'],true);
require_once ZEAI.'cache/config_shop.php';

if(!in_array('role',$QXARR))exit(noauth());
if($submitok=='add_update' || $submitok=='mod_update'){
	if(str_len($title)<1 || str_len($title)>50 )json_exit(array('flag'=>0,'msg'=>'【用户组名称】长度1~50','focus'=>'title'));
	$title  = dataIO($title,'in',50);
	if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择权限'));
	$authoritylist = implode(",",$list);
	if(in_array('crm',$list)){
		$authoritylist .= ',love,crm_welcome,modpass,pay_qd,loveb_qd,money_qd,tip_list,chat_list,view_list,gz_list,log,claim,claim_adm,claim_force_cancel,crm_hn_bbs,crm_hn,crm_news,crm_user,crm_user_mod,crm_u_add,u_qianxian,u_qianxian_del,crm_user_bz,crm_user_kind,crm_ukind,crm_user_grade,crm_user_home,crm_user_select,crm_user_contact,crm_user_contact_my,crm_match_list,crm_match_view,crm_match_add,crm_match_mod,crm_match_del,crm_bbs_list,crm_bbs_view,crm_bbs_add,crm_bbs_mod,crm_bbs_del,crm_user_fav_view,crm_user_fav_add,crm_user_fav_del,crm_hn_utask_sq_add,crm_hn_utask_sq_mod,crm_hn_utask_sh_add,crm_hn_utask_sh_mod,crm_ht_view,crm_ht_flag,crm_ht_add,crm_ht_mod,crm_ht_del,crm_pay_view,crm_pay_flag,crm_pay_analyse';
	}
}
if (!empty($submitok)){
	AddLog('【基础设置】->【管理员角色】修改');
}
switch ($submitok){
	case "add_update":
		$sq_sh_bfb = intval($sq_sh_bfb);
		$sq_sh_bfb = ($sq_sh_bfb>100)?100:$sq_sh_bfb;
		$db->query("INSERT INTO ".__TBL_ROLE__." (title,authoritylist,kind,px,sq_sh_bfb) VALUES ('$title','$authoritylist',2,".ADDTIME.",$sq_sh_bfb)");	
		json_exit(array('flag'=>1,'msg'=>'增加成功'));
	break;
	case "mod_update":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$sq_sh_bfb = intval($sq_sh_bfb);
		$sq_sh_bfb = ($sq_sh_bfb>100)?100:$sq_sh_bfb;
		$db->query("UPDATE ".__TBL_ROLE__." SET title='$title',authoritylist='$authoritylist',sq_sh_bfb='$sq_sh_bfb' WHERE id=".$id);
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case "ajax_delupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$db->query("DELETE FROM ".__TBL_ROLE__." WHERE id<>11 AND id=".$id);
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($id))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_ROLE__." SET px=".ADDTIME." WHERE id=".$id);
		header("Location: ".SELF);
	break;
	case"add":
		$AUTH = array();
	break;
	case"mod":
		if (!ifint($id))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_ROLE__." WHERE kind=2 AND id=".$id);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$authoritylist = $row['authoritylist'];
			$AUTH = explode(',',$authoritylist);
			$title= dataIO($row['title'],'out');
			$sq_sh_bfb = intval($row['sq_sh_bfb']);
		}else{
			alert_adm("该用户组不存在！","-1");
		}
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">

<style>.table0 b{font-size:14px}</style>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<div class="navbox">
	<a href="role.php" class="ed">管理员用户组/权限<?php echo '<b>'.$db->COUNT(__TBL_ROLE__,"kind=2").'</b>';?></a>
</div>
<div class="fixedblank"></div>

<!--ADD-->
<?php 
/************************************** 【发布】【修改】 add **************************************/
if ($submitok == 'add' || $submitok == 'mod'){?>
	<table class="table W1200 Mtop50" style="float:left;margin:15px 0 100px 20px">
	<form id="ZEAIFORM" name="ZEAIFORM" method="post" >
	<tr>
	<td colspan="2" align="left" class="tbodyT"><?php
if($submitok == 'add')echo '新建用户组';
if($submitok == 'mod')echo '修改用户组';
?></td>
	</tr>
	<tr>
	  <td class="tdL">用户组名称</td>
	  <td class="tdR"><input id="title" name="title" type="text" class="input W200 size2" maxlength="50" value="<?php echo $title;?>"><span class="tips">如：客服，业务员，财务，超级管理员等，修改之后此用户组需退出后台重新登录才会生效</span></td>
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

<tr><td height="20" colspan="5" align="left" valign="bottom" class="tiaose S14"></td></tr>


<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" id="crm" class="checkskin " value="crm" <?php echo (in_array('crm',$AUTH))?' checked':'';?>><label for="crm" class="checkskin-label" onClick="zeai.alert('请慎重！ 一经选择将拥用CRM最高权限（CRM超级管理员），可以自由切换交友和CRM管理');"><i class="i1"></i><b class="W150" style="font-weight:bold">线下CRM超级管理员</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_tg" value="u_tg" <?php echo (in_array('u_tg',$AUTH))?' checked':'';?>><label for="u_tg" class="checkskin-label"><i class="i1"></i><b class="W150" style="font-weight:bold"><?php echo $TG_set['navtitle'];?>-推广返利</b></label></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="shop" value="shop" <?php echo (in_array('shop',$AUTH))?' checked':'';?>><label for="shop" class="checkskin-label"><i class="i1"></i><b class="W150" style="font-weight:bold"><?php echo $_SHOP['title'];?>-商家系统</b></label></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left"></td>
</tr>
<tr>


<tr><td height="40" colspan="5" align="left" valign="bottom" class="tiaose S14">工作台</td></tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" id="welcome" class="checkskin " value="welcome" <?php echo (in_array('welcome',$AUTH))?' checked':'';?>>
  <label for="welcome" class="checkskin-label"><i class="i1"></i><b class="W150">首页/统计待办</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="pay_qd" value="pay_qd" <?php echo (in_array('pay_qd',$AUTH))?' checked':'';?>>
  <label for="pay_qd" class="checkskin-label"><i class="i1"></i><b class="W150">支付清单</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="loveb_qd" value="loveb_qd" <?php echo (in_array('loveb_qd',$AUTH))?' checked':'';?>>
  <label for="loveb_qd" class="checkskin-label"><i class="i1"></i><b class="W150"><?php echo $_ZEAI['loveB'];?>清单</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="money_qd" value="money_qd" <?php echo (in_array('money_qd',$AUTH))?' checked':'';?>>
  <label for="money_qd" class="checkskin-label"><i class="i1"></i><b class="W150">余额清单</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="tip_list" value="tip_list" <?php echo (in_array('tip_list',$AUTH))?' checked':'';?>>
  <label for="tip_list" class="checkskin-label"><i class="i1"></i><b class="W100">消息通知记录</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="chat_list" value="chat_list" <?php echo (in_array('chat_list',$AUTH))?' checked':'';?>>
  <label for="chat_list" class="checkskin-label"><i class="i1"></i><b class="W100">私信聊天记录</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="view_list" value="view_list" <?php echo (in_array('view_list',$AUTH))?' checked':'';?>>
  <label for="view_list" class="checkskin-label"><i class="i1"></i><b class="W100">谁看过我记录</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="gz_list" value="gz_list" <?php echo (in_array('gz_list',$AUTH))?' checked':'';?>>
  <label for="gz_list" class="checkskin-label"><i class="i1"></i><b class="W100">用户关注记录</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="log" value="log" <?php echo (in_array('log',$AUTH))?' checked':'';?>>
  <label for="log" class="checkskin-label"><i class="i1"></i><b class="W100">系统日志查看</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" id="logdel" class="checkskin " value="logdel" <?php echo (in_array('logdel',$AUTH))?' checked':'';?>>
  <label for="logdel" class="checkskin-label" ><i class="i1"></i><b class="W150">系统日志删除</b></label></td>
</tr>

<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" id="analyse_month" class="checkskin " value="analyse_month" <?php echo (in_array('analyse_month',$AUTH))?' checked':'';?>>
  <label for="analyse_month" class="checkskin-label" ><i class="i1"></i><b class="W150">数据月报</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" id="analyse_u" class="checkskin " value="analyse_u" <?php echo (in_array('analyse_u',$AUTH))?' checked':'';?>>
  <label for="analyse_u" class="checkskin-label" ><i class="i1"></i><b class="W150">用户分析</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" id="analyse_hn_month" class="checkskin " value="analyse_hn_month" <?php echo (in_array('analyse_hn_month',$AUTH))?' checked':'';?>><label for="analyse_hn_month" class="checkskin-label" ><i class="i1"></i><b class="W150">红娘月报</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" id="welcome_pay" class="checkskin " value="welcome_pay" <?php echo (in_array('welcome_pay',$AUTH))?' checked':'';?>><label for="welcome_pay" class="checkskin-label" ><i class="i1"></i><b class="W150">工作台收益显示</b></label></td>
<td width="153" height="20" align="left"></td>
</tr>



<tr><td height="40" colspan="5" align="left" valign="bottom" class="tiaose S14">用户管理</td></tr>

<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u" value="u" <?php echo (in_array('u',$AUTH))?' checked':'';?>><label for="u" class="checkskin-label"><i class="i1"></i><b class="W100">用户管理/升级</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_select" value="u_select" <?php echo (in_array('u_select',$AUTH))?' checked':'';?>><label for="u_select" class="checkskin-label"><i class="i1"></i><b class="W100">用户查询</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_del" value="u_del" <?php echo (in_array('u_del',$AUTH))?' checked':'';?>><label for="u_del" class="checkskin-label"><i class="i1"></i><b class="W100">用户删除</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_add" value="u_add" <?php echo (in_array('u_add',$AUTH))?' checked':'';?>><label for="u_add" class="checkskin-label"><i class="i1"></i><b class="W100">用户录入</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_qianxian" value="u_qianxian" <?php echo (in_array('u_qianxian',$AUTH))?' checked':'';?>><label for="u_qianxian" class="checkskin-label"><i class="i1"></i><b class="W100">牵线管理</b></label></td>
</tr>

<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="urole" value="urole" <?php echo (in_array('urole',$AUTH))?' checked':'';?>>
  <label for="urole" class="checkskin-label"><i class="i1"></i><b class="W150">用户组/VIP套餐</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="excel_out" value="excel_out" <?php echo (in_array('excel_out',$AUTH))?' checked':'';?>>
  <label for="excel_out" class="checkskin-label"><i class="i1"></i><b class="W100">用户导入/导出</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="claim" value="claim" <?php echo (in_array('claim',$AUTH))?' checked':'';?>>
  <label for="claim" class="checkskin-label"><i class="i1"></i><b class="W100">公海用户认领</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="claim_adm" value="claim_adm" <?php echo (in_array('claim_adm',$AUTH))?' checked':'';?>>
  <label for="claim_adm" class="checkskin-label"><i class="i1"></i><b class="W100">认领管理</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_qianxian_del" value="u_qianxian_del" <?php echo (in_array('u_qianxian_del',$AUTH))?' checked':'';?>><label for="u_qianxian_del" class="checkskin-label"><i class="i1"></i><b class="W100">牵线删除</b></label></td>
</tr>



<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_mod_pass" value="u_mod_pass" <?php echo (in_array('u_mod_pass',$AUTH))?' checked':'';?>>
  <label for="u_mod_pass" class="checkskin-label"><i class="i1"></i><b class="W100">重置用户密码</b></label></td>
<td width="154" height="20" align="left">&nbsp;</td>
<td width="150" height="20" align="left">&nbsp;</td>
<td width="145" height="20" align="left">&nbsp;</td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>



<tr>
  <td height="40" colspan="5" align="left" valign="bottom" class="tiaose S14">内容审核/管理</td>
</tr>
<tr>
  <td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_jb_list" value="u_jb_list" <?php echo (in_array('u_jb_list',$AUTH))?' checked':'';?>><label for="u_jb_list" class="checkskin-label"><i class="i1"></i><b class="W100">资料审核</b></label></td>
  <td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="photo_m" value="photo_m" <?php echo (in_array('photo_m',$AUTH))?' checked':'';?>><label for="photo_m" class="checkskin-label"><i class="i1"></i><b class="W100">头像审核/置顶</b></label></td>
  <td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="cert" value="cert" <?php echo (in_array('cert',$AUTH))?' checked':'';?>><label for="cert" class="checkskin-label"><i class="i1"></i><b class="W100">认证审核</b></label></td>
  <td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="photo" value="photo" <?php echo (in_array('photo',$AUTH))?' checked':'';?>><label for="photo" class="checkskin-label"><i class="i1"></i><b class="W100">相册审核</b></label></td>
  <td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="wxewm" value="wxewm" <?php echo (in_array('wxewm',$AUTH))?' checked':'';?>><label for="wxewm" class="checkskin-label"><i class="i1"></i><b class="W100">用户微信二维码</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="video" value="video" <?php echo (in_array('video',$AUTH))?' checked':'';?>><label for="video" class="checkskin-label"><i class="i1"></i><b class="W100">视频管理</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="tx" value="tx" <?php echo (in_array('tx',$AUTH))?' checked':'';?>><label for="tx" class="checkskin-label"><i class="i1"></i><b class="W150">提现审核</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="315" value="315" <?php echo (in_array('315',$AUTH))?' checked':'';?>>
  <label for="315" class="checkskin-label"><i class="i1"></i><b class="W100">举报审核</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="party" value="party" <?php echo (in_array('party',$AUTH))?' checked':'';?>>
  <label for="party" class="checkskin-label"><i class="i1"></i><b class="W100">交友活动</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="party_bbsall" value="party_bbsall" <?php echo (in_array('party_bbsall',$AUTH))?' checked':'';?>>
  <label for="party_bbsall" class="checkskin-label"><i class="i1"></i><b class="W100">活动评论</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="dating" value="dating" <?php echo (in_array('dating',$AUTH))?' checked':'';?>>
  <label for="dating" class="checkskin-label"><i class="i1"></i><b class="W100">约会审核/管理</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="trend" value="trend" <?php echo (in_array('trend',$AUTH))?' checked':'';?>>
  <label for="trend" class="checkskin-label"><i class="i1"></i><b class="W100">动态审核/管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="news_kind" value="news_kind" <?php echo (in_array('news_kind',$AUTH))?' checked':'';?>>
  <label for="news_kind" class="checkskin-label"><i class="i1"></i><b class="W100">文章分类</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="news" value="news" <?php echo (in_array('news',$AUTH))?' checked':'';?>>
  <label for="news" class="checkskin-label"><i class="i1"></i><b class="W100">文章管理</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="hongbao" value="hongbao" <?php echo (in_array('hongbao',$AUTH))?' checked':'';?>>
  <label for="hongbao" class="checkskin-label"><i class="i1"></i><b class="W100">红包管理</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="dating_user" value="dating_user" <?php echo (in_array('dating_user',$AUTH))?' checked':'';?>>
  <label for="dating_user" class="checkskin-label"><i class="i1"></i><b class="W100">约会名单</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="gift" value="gift" <?php echo (in_array('gift',$AUTH))?' checked':'';?>>
  <label for="gift" class="checkskin-label"><i class="i1"></i><b class="W100">礼物管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="group" value="group" <?php echo (in_array('group',$AUTH))?' checked':'';?>>
  <label for="group" class="checkskin-label"><i class="i1"></i><b class="W100">圈子管理</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="about" value="about" <?php echo (in_array('about',$AUTH))?' checked':'';?>><label for="about" class="checkskin-label"><i class="i1"></i><b class="W150">关于我们/客服/隐私</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="news_gg" value="news_gg" <?php echo (in_array('news_gg',$AUTH))?' checked':'';?>><label for="news_gg" class="checkskin-label"><i class="i1"></i><b class="W100">网站公告</b></label></td>
</tr>
<tr>
  <td height="40" colspan="5" align="left" valign="bottom" class="tiaose S14">运营管理</td>
</tr>
<tr>
  <td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="wx_gzh_push" value="wx_gzh_push" <?php echo (in_array('wx_gzh_push',$AUTH))?' checked':'';?>><label for="wx_gzh_push" class="checkskin-label"><i class="i1"></i><b class="W150">微信公众号推送</b></label></td>
  <td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="form" value="form" <?php echo (in_array('form',$AUTH))?' checked':'';?>><label for="form" class="checkskin-label"><i class="i1"></i><b class="W150">表单采集管理</b></label></td>
  <td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="bounce_vip" value="bounce_vip" <?php echo (in_array('bounce_vip',$AUTH))?' checked':'';?>>
    <label for="bounce_vip" class="checkskin-label"><i class="i1"></i><b class="W100">反弹引导海报</b></label></td>
  <td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="bounce_index" value="bounce_index" <?php echo (in_array('bounce_index',$AUTH))?' checked':'';?>><label for="bounce_index" class="checkskin-label"><i class="i1"></i><b class="W100">首页海报</b></label></td>
  <td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_adm" value="u_adm" <?php echo (in_array('u_adm',$AUTH))?' checked':'';?>>
    <label for="u_adm" class="checkskin-label"><i class="i1"></i><b class="W100">线下业务员</b></label></td></tr>
<tr>
  <td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="adv" value="adv" <?php echo (in_array('adv',$AUTH))?' checked':'';?>><label for="adv" class="checkskin-label"><i class="i1"></i><b class="W100">广告管理</b></label></td>
  <td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="robot" value="robot" <?php echo (in_array('robot',$AUTH))?' checked':'';?>>
    <label for="robot" class="checkskin-label"><i class="i1"></i><b class="W100">AI机器人</b></label></td>
  <td width="150" height="20" align="left">&nbsp;</td>
  <td width="145" height="20" align="left">&nbsp;</td>
  <td width="153" height="20" align="left">&nbsp;</td>
</tr>
<tr>
  <td height="40" colspan="5" align="left" valign="bottom" class="tiaose S14">基础配置</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_jb" value="var_jb" <?php echo (in_array('var_jb',$AUTH))?' checked':'';?>><label for="var_jb" class="checkskin-label"><i class="i1"></i><b class="W100">站点设置</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_gzh" value="var_gzh" <?php echo (in_array('var_gzh',$AUTH))?' checked':'';?>>
  <label for="var_gzh" class="checkskin-label"><i class="i1"></i><b class="W150">公众号设置</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_payset" value="var_payset" <?php echo (in_array('var_payset',$AUTH))?' checked':'';?>>
  <label for="var_payset" class="checkskin-label"><i class="i1"></i><b class="W100">支付设置</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_qqloginemail" value="var_qqloginemail" <?php echo (in_array('var_qqloginemail',$AUTH))?' checked':'';?>><label for="var_qqloginemail" class="checkskin-label"><i class="i1"></i><b class="W150">帐号互联</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" id="modpass" class="checkskin " value="modpass" <?php echo (in_array('modpass',$AUTH))?' checked':'';?>>
  <label for="modpass" class="checkskin-label"><i class="i1"></i><b class="W100">后台密码修改</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_smsemail" value="var_smsemail" <?php echo (in_array('var_smsemail',$AUTH))?' checked':'';?>><label for="var_smsemail" class="checkskin-label"><i class="i1"></i><b class="W150">短信/邮箱</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="area" value="area" <?php echo (in_array('area',$AUTH))?' checked':'';?>><label for="area" class="checkskin-label"><i class="i1"></i><b class="W100">地区管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="role" value="role" <?php echo (in_array('role',$AUTH))?' checked':'';?>><label for="role" class="checkskin-label"><i class="i1"></i><b class="W150">管理员角色</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="adminuser" value="adminuser" <?php echo (in_array('adminuser',$AUTH))?' checked':'';?>><label for="adminuser" class="checkskin-label"><i class="i1"></i><b class="W100">管理员用户</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="databak" value="databak" <?php echo (in_array('databak',$AUTH))?' checked':'';?>><label for="databak" class="checkskin-label"><i class="i1"></i><b class="W100">数据备份</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="nav" value="nav" <?php echo (in_array('nav',$AUTH))?' checked':'';?>>
  <label for="nav" class="checkskin-label"><i class="i1"></i><b class="W100">导航/模块设置</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_rz" value="var_rz" <?php echo (in_array('var_rz',$AUTH))?' checked':'';?>>
  <label for="var_rz" class="checkskin-label"><i class="i1"></i><b class="W100">认证设置</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_loveb" value="switchs_loveb" <?php echo (in_array('switchs_loveb',$AUTH))?' checked':'';?>><label for="switchs_loveb" class="checkskin-label"><i class="i1"></i><b class="W100"><?php echo $_ZEAI['loveB'];?>配置</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_reg" value="switchs_reg" <?php echo (in_array('switchs_reg',$AUTH))?' checked':'';?>><label for="switchs_reg" class="checkskin-label"><i class="i1"></i><b class="W100">用户注册选项</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="udata" value="udata" <?php echo (in_array('udata',$AUTH))?' checked':'';?>><label for="udata" class="checkskin-label"><i class="i1"></i><b class="W100">用户资料属性</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_shkg" value="switchs_shkg" <?php echo (in_array('switchs_shkg',$AUTH))?' checked':'';?>><label for="switchs_shkg" class="checkskin-label"><i class="i1"></i><b class="W100">功能开关</b></label></td>
<td width="154" height="20" align="left"></td>
<td width="150" height="20" align="left"></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left"></td>
</tr>

<tr>
  <td height="40" colspan="5" align="left" valign="bottom" class="tiaose S14">售前审核专区</td>
</tr>
<tr>
<td height="30" colspan="5" align="left" class="S14">

<span class="picmiddle"><input type="checkbox" name="list[]" id="sq_sh" class="checkskin " value="sq_sh" <?php echo (in_array('sq_sh',$AUTH))?' checked':'';?>><label for="sq_sh" class="checkskin-label"><i class="i1"></i><b class="W100">售前用户审核</b></label></span>
<span class="picmiddle" style="display:inline-block;margin-top:5px">显示用户列表资料完善度以下：<input name="sq_sh_bfb" id="sq_sh_bfb" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $sq_sh_bfb;?>"> %　<span class="tips S12">显示未审核用户资料完善度在此百分比以下的用户</span></span>
  
  </td>
</tr>
<tr>
<td height="30" colspan="2" align="left"><input type="checkbox" name="list[]" id="sq_sh_view" class="checkskin " value="sq_sh_view" <?php echo (in_array('sq_sh_view',$AUTH))?' checked':'';?>><label for="sq_sh_view" class="checkskin-label"><i class="i1"></i><b class="W150">售前审核反馈查看</b></label></td>
<td width="150" height="20" align="left"></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left">&nbsp;</td>
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
		<?php if ($submitok == 'mod'){?>
          <input name="submitok" type="hidden" value="mod_update" />
          <input name="id" type="hidden" value="<?php echo $id;?>" />
          <input name="username_ord" type="hidden" value="<?php echo $username;?>" />
        <?php }else{ ?>
          <input name="submitok" type="hidden" value="add_update" />
        <?php }?>        
	</form>
	</table>
<br><br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">确认并保存</button></div>

<?php }else{?>    
    
    
<!--LIST-->
	<?php
	$rt = $db->query("SELECT * FROM ".__TBL_ROLE__." WHERE kind=2 ORDER BY px DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodataico'><i></i>暂无用户组<br><a class='btn size2 HUANG' onClick=\"zeai.iframe('新增用户组','".SELF."?submitok=add',500,300)\">新增用户组</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="150" align="left"><button type="button" class="btn tips" onClick="zeai.openurl('<?php echo SELF;?>?submitok=add')" ><i class="ico addico">&#xe620;</i>新增用户组</button></td>
		<td align="left">&nbsp;</td>
	  </tr>
	</table>
	<table class="tablelist">
	<tr>
        <th width="60" align="center">ID</th>
        <th width="60" align="center">置顶</th>
        <th width="200">用户组名称</th>
        <th><span class="list_title">下属管理员用户</span></th>
        <th width="80" class="center">修改权限</th>
        <th width="80" class="center">删除</th>
	</tr>
	<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$id = $rows['id'];
		$title = dataIO($rows['title'],'out');
	?>
	<tr>
	<td width="60" height="40" align="center"><?php echo $id;?></td>
    <td width="60" height="40" align="center"><a href="<?php echo "role.php?id=".$id; ?>&submitok=ding"><img src="images/ding.gif" border="0" title="置顶" /></a></td>
	<td width="200" class="S14"><a href='<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>'><?php echo $title;?></a></td>
	<td class="S14 Cccc"><?php 
$rt2=$db->query("SELECT id,username FROM ".__TBL_ADMIN__." WHERE roleid=".$rows['id']." ORDER BY id DESC");
$total2 = $db->num_rows($rt2);
if ($total2 == 0) {
	echo "暂无";
} else {
	for($i2=1;$i2<=$total2;$i2++) {
		$rows2 = $db->fetch_array($rt2);
		if(!$rows2) break;
		$id2    = $rows2[0];
		$username2 = htmlout($rows2[1]);
?>
        <a href="adminuser.php?submitok=mod&id=<?php echo $id2; ?>" class="tiaose"><?php echo $username2; ?></a>
<?php 
		if ($i2 != $total2)echo "　|　";
	}
}
?></td>
	<td width="80" class="center">
    
    <a href='<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>' class="editico" title='修改'></a>
    
    </td>
	<td width="80" class="center"><a value="<?php echo $id; ?>" class="delico"></a></td>
	</tr>
	<?php } ?>
	<?php if ($total > $pagesize){?>
	<tfoot><tr>
	<td colspan="6" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
	</tr></tfoot>
	<?php } ?>
	</table>
	<?php } ?>

<?php require_once 'bottomadm.php';?>


<?php }?> 
<script>
<?php if ($submitok == "add" || $submitok == "mod") {?>

	save.onclick = function(){
		var oktips ='<b class="S18">确定保存么？</b><br>保存后，此角色组下属帐号权限将会更新<br><font class="Cf00">注：帐号退出后重新登录方可生效</font>';
		zeai.confirm(oktips,function(){
			zeai.ajax({url:'role'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){
					zeai.msg(rs.msg);
					setTimeout(function(){zeai.openurl('role.php');},1000);
				}else if(rs.flag == 0){
					zeai.msg(rs.msg,o(rs.focus));
				}else{
					zeai.msg(rs.msg);
				}		
			});
		});
	}
<?php }else{ ?>
	zeai.listEach('.delico',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		obj.onclick = function(){
			zeai.confirm('<b class="S18">请慎重！真的要删除么？</b><br>删除后不可恢复，删除后请重新指定下属管理员的【用户组】',function(){
				zeai.ajax('role'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
<?php } ?>
</script>
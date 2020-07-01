<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('role',$QXARR))exit(noauth());
if($submitok=='add_update' || $submitok=='mod_update'){
	if(str_len($title)<1 || str_len($title)>50 )json_exit(array('flag'=>0,'msg'=>'【角色名称】长度1~50','focus'=>'title'));
	$title  = dataIO($title,'in',50);
	if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择权限'));
	$authoritylist = implode(",",$list);
	
	if(in_array('crm',$list)){
		$authoritylist .= ',love,crm_welcome,modpass,crm_hn,crm_hn_bbs,crm_role,crm_news,crm_user_mod,crm_u_add,crm_user_select,crm_match_add,crm_match_mod,crm_match_del,crm_bbs_add,crm_bbs_mod,crm_bbs_del,crm_user_bz,crm_user_kind,crm_user_flag,crm_user_grade,crm_hn_ugrade1,crm_hn_utask,crm_hn_utasked,crm_hn_work1,crm_ht_add,crm_hn_work_1,crm_user_fav,crm_htflagall,crm_htflag0,crm_htflag2,crm_htflag1,crm_payflagall,crm_payflag0,crm_payflag2,crm_payflag1,crm_pay_analyse';
	}
}

switch ($submitok){
	case "add_update":
		$db->query("INSERT INTO ".__TBL_ROLE__." (title,authoritylist,kind,px) VALUES ('$title','$authoritylist',2,".ADDTIME.")");	
		json_exit(array('flag'=>1,'msg'=>'增加成功'));
	break;
	case "mod_update":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$db->query("UPDATE ".__TBL_ROLE__." SET title='$title',authoritylist='$authoritylist' WHERE id=".$id);
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
		}else{
			alert_adm("该角色不存在！","-1");
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
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">

<script src="../res/www_zeai_cn.js"></script>
<body>
<div class="navbox">
	<a href="role.php" class="ed">角色管理<?php echo '<b>'.$db->COUNT(__TBL_ROLE__,"kind=2").'</b>';?></a>
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
if($submitok == 'add')echo '新建角色';
if($submitok == 'mod')echo '修改角色';
?></td>
	</tr>
	<tr>
	  <td class="tdL">角色名称</td>
	  <td class="tdR"><input id="title" name="title" type="text" class="input W200 size2" maxlength="50" value="<?php echo $title;?>"><span class="tips">如：客服，财务，管理员等</span></td>
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


<tr>
<td height="40" colspan="5" align="left" valign="bottom" class="B">&nbsp;</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" id="crm" class="checkskin " value="crm" <?php echo (in_array('crm',$AUTH))?' checked':'';?>><label for="crm" class="checkskin-label" onClick="zeai.alert('请慎重！ 一经选择将拥用CRM最高权限（红娘管理员），可以自由切换交友和CRM管理');"><i class="i1"></i><b class="W100" style="font-weight:bold">红娘CRM管理</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" id="welcome" class="checkskin " value="welcome" <?php echo (in_array('welcome',$AUTH))?' checked':'';?>><label for="welcome" class="checkskin-label"><i class="i1"></i><b class="W150">管理首页/整站统计待办</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" id="modpass" class="checkskin " value="modpass" <?php echo (in_array('modpass',$AUTH))?' checked':'';?>><label for="modpass" class="checkskin-label"><i class="i1"></i><b class="W100">后台密码修改</b></label></td>
<td width="145" height="20" align="left">&nbsp;</td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>



<tr>
<td height="40" colspan="5" align="left" valign="bottom" class="B">会员管理</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u" value="u" <?php echo (in_array('u',$AUTH))?' checked':'';?>><label for="u" class="checkskin-label"><i class="i1"></i><b class="W100">会员管理/升级</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_select" value="u_select" <?php echo (in_array('u_select',$AUTH))?' checked':'';?>><label for="u_select" class="checkskin-label"><i class="i1"></i><b class="W100">会员筛选管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_jb_list" value="u_jb_list" <?php echo (in_array('u_jb_list',$AUTH))?' checked':'';?>><label for="u_jb_list" class="checkskin-label"><i class="i1"></i><b class="W100">基本资料审核</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="photo_m" value="photo_m" <?php echo (in_array('photo_m',$AUTH))?' checked':'';?>><label for="photo_m" class="checkskin-label"><i class="i1"></i><b class="W100">形象照审核/置顶</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="photo" value="photo" <?php echo (in_array('photo',$AUTH))?' checked':'';?>><label for="photo" class="checkskin-label"><i class="i1"></i><b class="W100">个人相册审核管理</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="wxewm" value="wxewm" <?php echo (in_array('wxewm',$AUTH))?' checked':'';?>><label for="wxewm" class="checkskin-label"><i class="i1"></i><b class="W100">会员微信二维码</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="cert" value="cert" <?php echo (in_array('cert',$AUTH))?' checked':'';?>><label for="cert" class="checkskin-label"><i class="i1"></i><b class="W100">认证审核管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="video" value="video" <?php echo (in_array('video',$AUTH))?' checked':'';?>><label for="video" class="checkskin-label"><i class="i1"></i><b class="W100">个人视频管理</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="chat_list" value="chat_list" <?php echo (in_array('chat_list',$AUTH))?' checked':'';?>><label for="chat_list" class="checkskin-label"><i class="i1"></i><b class="W100">私信聊天记录管理</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="tip_list" value="tip_list" <?php echo (in_array('tip_list',$AUTH))?' checked':'';?>><label for="tip_list" class="checkskin-label"><i class="i1"></i><b class="W100">站内消息通知管理</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_mod_pass" value="u_mod_pass" <?php echo (in_array('u_mod_pass',$AUTH))?' checked':'';?>><label for="u_mod_pass" class="checkskin-label"><i class="i1"></i><b class="W100">重置会员密码</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="excel_out" value="excel_out" <?php echo (in_array('excel_out',$AUTH))?' checked':'';?>><label for="excel_out" class="checkskin-label"><i class="i1"></i><b class="W100">会员资料导出</b></label></td>
<td width="150" height="20" align="left">&nbsp;</td>
<td width="145" height="20" align="left">&nbsp;</td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>


<tr>
<td height="40" colspan="5" align="left" valign="bottom" class="B">交友活动</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="party" value="party" <?php echo (in_array('party',$AUTH))?' checked':'';?>><label for="party" class="checkskin-label"><i class="i1"></i><b class="W100">交友活动管理</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="party_bbsall" value="party_bbsall" <?php echo (in_array('party_bbsall',$AUTH))?' checked':'';?>><label for="party_bbsall" class="checkskin-label"><i class="i1"></i><b class="W100">活动评论管理</b></label></td>
<td width="150" height="20" align="left">&nbsp;</td>
<td width="145" height="20" align="left">&nbsp;</td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>
<tr>
<td height="40" colspan="5" align="left" valign="bottom" class="B">动态/约会</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="trend" value="trend" <?php echo (in_array('trend',$AUTH))?' checked':'';?>><label for="trend" class="checkskin-label"><i class="i1"></i><b class="W100">会员动态管理</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="dating" value="dating" <?php echo (in_array('dating',$AUTH))?' checked':'';?>><label for="dating" class="checkskin-label"><i class="i1"></i><b class="W100">约会管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="dating_user" value="dating_user" <?php echo (in_array('dating_user',$AUTH))?' checked':'';?>><label for="dating_user" class="checkskin-label"><i class="i1"></i><b class="W100">约会名单/联系方式</b></label></td>
<td width="145" height="20" align="left">&nbsp;</td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>
<tr>
<td height="40" colspan="5" align="left" valign="bottom" class="B">运营设置/管理</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="udata" value="udata" <?php echo (in_array('udata',$AUTH))?' checked':'';?>><label for="udata" class="checkskin-label"><i class="i1"></i><b class="W100">会员资料字段设置</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="urole" value="urole" <?php echo (in_array('urole',$AUTH))?' checked':'';?>><label for="urole" class="checkskin-label"><i class="i1"></i><b class="W100">会员组/等级管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_reg" value="switchs_reg" <?php echo (in_array('switchs_reg',$AUTH))?' checked':'';?>><label for="switchs_reg" class="checkskin-label"><i class="i1"></i><b class="W100">会员注册选项</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_loveb" value="switchs_loveb" <?php echo (in_array('switchs_loveb',$AUTH))?' checked':'';?>><label for="switchs_loveb" class="checkskin-label"><i class="i1"></i><b class="W100">爱豆和收费机制</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="switchs_shkg" value="switchs_shkg" <?php echo (in_array('switchs_shkg',$AUTH))?' checked':'';?>><label for="switchs_shkg" class="checkskin-label"><i class="i1"></i><b class="W100">审核/功能/开关</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="wx_gzh" value="wx_gzh" <?php echo (in_array('wx_gzh',$AUTH))?' checked':'';?>><label for="wx_gzh" class="checkskin-label"><i class="i1"></i><b class="W150">微信公众号推送/群发</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="news_kind" value="news_kind" <?php echo (in_array('news_kind',$AUTH))?' checked':'';?>><label for="news_kind" class="checkskin-label"><i class="i1"></i><b class="W100">文章分类</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="news" value="news" <?php echo (in_array('news',$AUTH))?' checked':'';?>><label for="news" class="checkskin-label"><i class="i1"></i><b class="W100">文章发布/管理</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="gift" value="gift" <?php echo (in_array('gift',$AUTH))?' checked':'';?>><label for="gift" class="checkskin-label"><i class="i1"></i><b class="W100">礼物管理</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="hongbao" value="hongbao" <?php echo (in_array('hongbao',$AUTH))?' checked':'';?>><label for="hongbao" class="checkskin-label"><i class="i1"></i><b class="W100">红包管理</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="group" value="group" <?php echo (in_array('group',$AUTH))?' checked':'';?>><label for="group" class="checkskin-label"><i class="i1"></i><b class="W100">圈子管理</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="315" value="315" <?php echo (in_array('315',$AUTH))?' checked':'';?>><label for="315" class="checkskin-label"><i class="i1"></i><b class="W100">会员举报管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="robot" value="robot" <?php echo (in_array('robot',$AUTH))?' checked':'';?>><label for="robot" class="checkskin-label"><i class="i1"></i><b class="W100">机器人管理</b></label></td>
<td width="145" height="20" align="left">&nbsp;</td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>



<tr>
  <td height="40" colspan="5" align="left" valign="bottom" class="B">推广管理</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_tg" value="u_tg" <?php echo (in_array('u_tg',$AUTH))?' checked':'';?>><label for="u_tg" class="checkskin-label"><i class="i1"></i><b class="W100">推广会员管理</b></label></td>
<td width="154" height="20" align="left">&nbsp;</td>
<td width="150" height="20" align="left">&nbsp;</td>
<td width="145" height="20" align="left">&nbsp;</td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>
<tr>
  <td height="40" colspan="5" align="left" valign="bottom" class="B">综合管理</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_jb" value="var_jb" <?php echo (in_array('var_jb',$AUTH))?' checked':'';?>><label for="var_jb" class="checkskin-label"><i class="i1"></i><b class="W100">系统基本设置</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="about" value="about" <?php echo (in_array('about',$AUTH))?' checked':'';?>><label for="about" class="checkskin-label"><i class="i1"></i><b class="W150">关于我们/客服/声明</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="news" value="news" <?php echo (in_array('news',$AUTH))?' checked':'';?>><label for="news" class="checkskin-label"><i class="i1"></i><b class="W100">网站公告</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_gzh" value="var_gzh" <?php echo (in_array('var_gzh',$AUTH))?' checked':'';?>><label for="var_gzh" class="checkskin-label"><i class="i1"></i><b class="W150">微信公众号参数设置</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_qqloginemail" value="var_qqloginemail" <?php echo (in_array('var_qqloginemail',$AUTH))?' checked':'';?>><label for="var_qqloginemail" class="checkskin-label"><i class="i1"></i><b class="W150">QQ/微信登录/邮箱参数</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_smsemail" value="var_smsemail" <?php echo (in_array('var_smsemail',$AUTH))?' checked':'';?>><label for="var_smsemail" class="checkskin-label"><i class="i1"></i><b class="W150">手机短信运营商/邮箱</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="var_payset" value="var_payset" <?php echo (in_array('var_payset',$AUTH))?' checked':'';?>><label for="var_payset" class="checkskin-label"><i class="i1"></i><b class="W100">在线支付设置</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="area" value="area" <?php echo (in_array('area',$AUTH))?' checked':'';?>><label for="area" class="checkskin-label"><i class="i1"></i><b class="W100">地区管理</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="loveb_qd_tx" value="loveb_qd_tx" <?php echo (in_array('loveb_qd_tx',$AUTH))?' checked':'';?>><label for="loveb_qd_tx" class="checkskin-label"><i class="i1"></i><b class="W150">爱豆余额支付清单/提现</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="adminuser" value="adminuser" <?php echo (in_array('adminuser',$AUTH))?' checked':'';?>><label for="adminuser" class="checkskin-label"><i class="i1"></i><b class="W100">管理员用户分配</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="role" value="role" <?php echo (in_array('role',$AUTH))?' checked':'';?>><label for="role" class="checkskin-label"><i class="i1"></i><b class="W100">后台用户角色权限</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="databak" value="databak" <?php echo (in_array('databak',$AUTH))?' checked':'';?>><label for="databak" class="checkskin-label"><i class="i1"></i><b class="W100">数据库照片备份</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="zeaimall" value="zeaimall" <?php echo (in_array('zeaimall',$AUTH))?' checked':'';?>><label for="zeaimall" class="checkskin-label"><i class="i1"></i><b class="W100">择爱商店</b></label></td>
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
	<tr>
	  <td class="tdL">&nbsp;</td>
	  <td class="tdR">
	    <button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
		<?php if ($submitok == 'mod'){?>
          <input name="submitok" type="hidden" value="mod_update" />
          <input name="id" type="hidden" value="<?php echo $id;?>" />
          <input name="username_ord" type="hidden" value="<?php echo $username;?>" />
        <?php }else{ ?>
          <input name="submitok" type="hidden" value="add_update" />
        <?php }?>        
      </td>
	  </tr>
	</form>
	</table>
<?php }else{?>    
    
    
<!--LIST-->
	<?php
	$rt = $db->query("SELECT * FROM ".__TBL_ROLE__." WHERE kind=2 ORDER BY px DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodataico'><i></i>暂无角色<br><a class='btn size2 HUANG' onClick=\"zeai.iframe('新增角色','".SELF."?submitok=add',500,300)\">新增角色</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="150" align="left"><button type="button" class="btn tips" onClick="zeai.openurl('<?php echo SELF;?>?submitok=add')" ><i class="ico addico">&#xe620;</i>新增角色</button></td>
		<td align="left">&nbsp;</td>
	  </tr>
	</table>
	<table class="tablelist">
	<tr>
        <th width="60" align="center">ID</th>
        <th width="60" align="center">置顶</th>
        <th width="150">角色名称</th>
        <th><span class="list_title">下属用户</span></th>
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
	<td width="150" class="S16"><a href='<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>'><?php echo $title;?></a></td>
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
	<td width="80" class="center"><a href='<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>' class="edit">✎</a></td>
	<td width="80" class="center"><a value="<?php echo $id; ?>"  class="del">✖</a></td>
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
	}
<?php }else{ ?>
	zeai.listEach('.del',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		obj.onclick = function(){
			zeai.confirm('<font color="red">请慎重！</font><br>真的要删除么？<br>删除后不可恢复',function(){
				zeai.ajax('role'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	
<?php } ?>
</script>
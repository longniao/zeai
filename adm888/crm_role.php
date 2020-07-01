<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('crm',$QXARR))exit(noauth('只有CRM超级管理员才有权限'));

$crmkind_ARR=array();
if ($submitok == 'ajax_addupdate' || $submitok == 'ajax_modupdate'){
	if (empty($crmkind) )json_exit(array('flag'=>0,'msg'=>'请选择【角色类型】','focus'=>'crmkind'));
	if (empty($title) )json_exit(array('flag'=>0,'msg'=>'请输入【角色名称】','focus'=>'title'));
	if (str_len($title) >50)json_exit(array('flag'=>0,'msg'=>'亲，角色名称【'.$title.'】这么长有意义么？ 请不要超过20字节','focus'=>'title'));
	$title   = dataIO($title,'in',50);
	$crmkind = implode(",",$crmkind);
	$crmkind = dataIO($crmkind,'in',20);
	if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择权限'));
	$authoritylist = implode(",",$list);
	//gyl_debug($authoritylist);
}
switch ($submitok){
	case "ajax_addupdate":
		if ($db->NUM('zeai.urole',"id","kind=3 AND title='$title'",__TBL_ROLE__))json_exit(array('flag'=>0,'msg'=>'角色名称【'.$title.'】出现重复，请重试','focus'=>'title'));
		$db->query("INSERT INTO ".__TBL_ROLE__." (title,crmkind,authoritylist,kind,px) VALUES ('$title','$crmkind','$authoritylist',3,".ADDTIME.")");
		AddLog('【CRM】->新增【红娘角色“'.$title.'”】');
		json_exit(array('flag'=>1,'msg'=>'新增成功'));
	break;
	case "ajax_modupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		//if ($title != $oldtitle){
			//if ($db->NUM('zeai.urole',"id","kind=3 AND title='$title'",__TBL_ROLE__))json_exit(array('flag'=>0,'msg'=>'角色名称【'.$title.'】出现重复，请重试','focus'=>'title'));
		//}
		$db->query("UPDATE ".__TBL_ROLE__." SET crmkind='$crmkind',title='$title',authoritylist='$authoritylist' WHERE kind=3 AND id=".$id);
		$db->query("UPDATE ".__TBL_CRM_HN__." SET crmkind='$crmkind',roletitle='$title' WHERE roleid=".$id);
		AddLog('【CRM】->修改【红娘角色“'.$title.'”】');
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case "ajax_delupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		//不能删光，必须要留一个
		$rolenum = $db->COUNT(__TBL_ROLE__,"kind=3");
		if ($rolenum <= 1){
			json_exit(array('flag'=>0,'msg'=>'亲，不能删光啊，至少要留一个啊'));	
		}
		//删除角色
		$db->query("DELETE FROM ".__TBL_ROLE__." WHERE kind=3 AND grade<>10 AND id=".$id);
		AddLog('【CRM】->删除【红娘角色“角色ID:'.$id.'”】');
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"add":
		$AUTH = array();
	break;
	case"mod":
		if (!ifint($id))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_ROLE__." WHERE kind=3 AND id=".$id);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$authoritylist = $row['authoritylist'];
			$crmkind = $row['crmkind'];
			$crmkind_ARR = explode(',',$crmkind);
			$AUTH = explode(',',$authoritylist);
			$title= dataIO($row['title'],'out');
			$crmkind= dataIO($row['crmkind'],'out');
		}else{
			alert_adm("该角色不存在！","-1");
		}
	break;
	case"ding":
		if(!ifint($id))exit(JSON_ERROR);
		$db->query("UPDATE ".__TBL_ROLE__." SET px=".ADDTIME." WHERE kind=3 AND id=".$id);
		AddLog('【CRM】->红娘角色置顶【角色id:'.$id.'】');
		header("Location: ".SELF);
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<style>
.tablelist{margin:20px 20px 50px 20px}
.table0{width:98%;margin:10px 20px 20px 20px}

#tmp input{margin-right:10px}
#tmp .tr{margin-bottom:10px}
.jsonlist{border-radius:2px;display:inline-block;background-color:#aaa;padding:2px 7px;margin:3px 10px 3px 0}
.jsonlistbox{width:500px;overflow:hidden;display:inline-block;float:left}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
.table0 b{font-size:14px;width:150px}
</style>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<div class="navbox"><a class="ed">红娘角色管理<?php if ($submitok != "add" && $submitok != "mod" )echo '<b>'.$db->COUNT(__TBL_ROLE__,"kind=3").'</b>';?></a></div>
<div class="fixedblank"></div>
<!--ADD-->
<?php if ($submitok == "add" || $submitok == "mod") {
	?>
	<table class="table W1000 Mtop50" style="float:left;margin:15px 0 100px 20px">
	<form id="ZEAIFORM" name="ZEAIFORM" method="post">
	<tr>
	<td class="tdL">角色类型</td>
	<td class="tdR lineH150">
        <input type="checkbox" name="crmkind[]" id="crmkind_adm" class="checkskin " value="adm" <?php echo (in_array('adm',$crmkind_ARR))?' checked':'';?>><label for="crmkind_adm" class="checkskin-label"><i class="i1"></i><b class="W80">管理员</b></label>
        <input type="checkbox" name="crmkind[]" id="crmkind_sq" class="checkskin " value="sq" <?php echo (in_array('sq',$crmkind_ARR))?' checked':'';?>><label for="crmkind_sq" class="checkskin-label"><i class="i1"></i><b class="W80">售前</b></label>
        <input type="checkbox" name="crmkind[]" id="crmkind_sh" class="checkskin " value="sh" <?php echo (in_array('sh',$crmkind_ARR))?' checked':'';?>><label for="crmkind_sh" class="checkskin-label"><i class="i1"></i><b class="W80">售后</b></label>
        <input type="checkbox" name="crmkind[]" id="crmkind_ht" class="checkskin " value="ht" <?php echo (in_array('ht',$crmkind_ARR))?' checked':'';?>><label for="crmkind_ht" class="checkskin-label"><i class="i1"></i><b class="W80">合同</b></label>
        <input type="checkbox" name="crmkind[]" id="crmkind_cw" class="checkskin " value="cw" <?php echo (in_array('cw',$crmkind_ARR))?' checked':'';?>><label for="crmkind_cw" class="checkskin-label"><i class="i1"></i><b class="W80">财务</b></label>
     <br>
     <span class="tips2">
     注：如果想增加一个“资料审核员/等其它职能”请单选【管理员】<br>
     如果想增加一个“红娘主管”请全选这5个类型，如果红娘同时兼售前售后，可以同时选【售前】【售后】<br>
     选择对应的类型后，请在下方相关权限打勾，将会有对应的管理功能，可多选</span>
    </td>
	</tr>
	<tr>
	<td class="tdL">角色名称</td>
	<td class="tdR"><input id="title" name="title" type="text" class="W200 size2" size="30" maxlength="50" value="<?php echo $title;?>"><span class="tips">如：售前红娘，售后红娘，红娘主管等</span></td>
	</tr>
    <script>
//	zeai.listEach('.radioskin',function(obj){
//		var objv=obj.value;
//		obj.onclick = function(){
//			zeai.openurl('crm_role.php?submitok=<?php echo $submitok;?>&crmkind='+objv+'&id=<?php echo $id;?>&title='+encodeURIComponent(title.value));
//		}
//	});
    </script>
	<tr>
	<td class="tdL">分配权限</td>
	<td class="tdR">
    
    
    
<table cellpadding="0" cellspacing="0" class="table0" style="width:800px;margin:0 0 0 10px">
<tr><td colspan="5" style="border-bottom:#990000 0px dotted; height:30px;">
<script>
var checkgrade = "false";
function check(field) {
	if (checkgrade == "false") {
		zeai.listEach('.checkskin',function(obj){
			obj.checked = true;
		});
		checkgrade = "true";
		return "取消"; 
	} else {
		zeai.listEach('.checkskin',function(obj){
			obj.checked = false;
		});
		checkgrade = "false";
		return "全选"; 
	}
} 
</script><input class="btn size2" type="button" value="全选" accesskey="a" onClick="this.value=check(this.form)" /></span>
</td>
</tr>


<tr>
<td colspan="5" align="left" valign="bottom" class="B">&nbsp;</td>
</tr>

<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" id="crm_welcome" class="checkskin " value="crm_welcome" <?php echo (in_array('crm_welcome',$AUTH))?' checked':'';?>><label for="crm_welcome" class="checkskin-label"><i class="i1"></i><b class="W150">工作台/统计待办</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" id="modpass" class="checkskin " value="modpass" <?php echo (in_array('modpass',$AUTH))?' checked':'';?>><label for="modpass" class="checkskin-label"><i class="i1"></i><b class="W100">后台密码修改</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="pay_qd" value="pay_qd" <?php echo (in_array('pay_qd',$AUTH))?' checked':'';?>><label for="pay_qd" class="checkskin-label"><i class="i1"></i><b class="W150">支付清单</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="loveb_qd" value="loveb_qd" <?php echo (in_array('loveb_qd',$AUTH))?' checked':'';?>><label for="loveb_qd" class="checkskin-label"><i class="i1"></i><b class="W150"><?php echo $_ZEAI['loveB'];?>清单</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="money_qd" value="money_qd" <?php echo (in_array('money_qd',$AUTH))?' checked':'';?>><label for="money_qd" class="checkskin-label"><i class="i1"></i><b class="W150">余额清单</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="tip_list" value="tip_list" <?php echo (in_array('tip_list',$AUTH))?' checked':'';?>><label for="tip_list" class="checkskin-label"><i class="i1"></i><b class="W100">消息通知记录</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="chat_list" value="chat_list" <?php echo (in_array('chat_list',$AUTH))?' checked':'';?>><label for="chat_list" class="checkskin-label"><i class="i1"></i><b class="W100">私信聊天记录</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="view_list" value="view_list" <?php echo (in_array('view_list',$AUTH))?' checked':'';?>><label for="view_list" class="checkskin-label"><i class="i1"></i><b class="W100">谁看过我记录</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="gz_list" value="gz_list" <?php echo (in_array('gz_list',$AUTH))?' checked':'';?>><label for="gz_list" class="checkskin-label"><i class="i1"></i><b class="W100">客户关注记录</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="log" value="log" <?php echo (in_array('log',$AUTH))?' checked':'';?>><label for="log" class="checkskin-label"><i class="i1"></i><b class="W100">操作日志</b></label></td>
</tr>

<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="claim" value="claim" <?php echo (in_array('claim',$AUTH))?' checked':'';?>><label for="claim" class="checkskin-label"><i class="i1"></i><b class="W100">公海用户认领</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="claim_adm" value="claim_adm" <?php echo (in_array('claim_adm',$AUTH))?' checked':'';?>><label for="claim_adm" class="checkskin-label"><i class="i1"></i><b class="W100">认领管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="claim_force_cancel" value="claim_force_cancel" <?php echo (in_array('claim_force_cancel',$AUTH))?' checked':'';?>><label for="claim_force_cancel" class="checkskin-label"><i class="i1"></i><b class="W100">强制取消他人认领</b></label></td>
<td width="145" height="20" align="left">&nbsp;</td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>


<tr>
<td height="40" colspan="5" align="left" valign="bottom" class="tiaose S14">系统管理</td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" id="crm_disable1" class="checkskin" disabled><label class="checkskin-label" title="CRM超级管理员独享权限"><i class="i1"></i><b class="W150">门店管理</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" id="crm_disable2" class="checkskin" disabled><label class="checkskin-label" title="CRM超级管理员独享权限"><i class="i1"></i><b class="W100">角色管理</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_hn_bbs" value="crm_hn_bbs" <?php echo (in_array('crm_hn_bbs',$AUTH))?' checked':'';?>><label for="crm_hn_bbs" class="checkskin-label"><i class="i1"></i><b class="W100">红娘评价管理</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_hn" value="crm_hn" <?php echo (in_array('crm_hn',$AUTH))?' checked':'';?>><label for="crm_hn" class="checkskin-label"><i class="i1"></i><b class="W100">红娘管理</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_news" value="crm_news" <?php echo (in_array('crm_news',$AUTH))?' checked':'';?>><label for="crm_news" class="checkskin-label"><i class="i1"></i><b class="W100">通知公告管理</b></label></td>
</tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" id="crm_disable1" class="checkskin" disabled><label class="checkskin-label" title="CRM超级管理员独享权限"><i class="i1"></i><b class="W150">CRM资料属性</b></label></td>
<td width="154" height="20" align="left"></td>
<td width="150" height="20" align="left"></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left"></td>
</tr>

<tr><td height="40" colspan="5" align="left" valign="bottom" class="S14 tiaose">客户管理</td></tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user" value="crm_user" <?php echo (in_array('crm_user',$AUTH))?' checked':'';?>><label for="crm_user" class="checkskin-label"><i class="i1"></i><b class="W150">客户管理</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_mod" value="crm_user_mod" <?php echo (in_array('crm_user_mod',$AUTH))?' checked':'';?>><label for="crm_user_mod" class="checkskin-label"><i class="i1"></i><b class="W100">客户资料修改</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_u_add" value="crm_u_add" <?php echo (in_array('crm_u_add',$AUTH))?' checked':'';?>><label for="crm_u_add" class="checkskin-label"><i class="i1"></i><b class="W100">客户录入</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_qianxian" value="u_qianxian" <?php echo (in_array('u_qianxian',$AUTH))?' checked':'';?>><label for="u_qianxian" class="checkskin-label"><i class="i1"></i><b class="W100">牵线管理</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="u_qianxian_del" value="u_qianxian_del" <?php echo (in_array('u_qianxian_del',$AUTH))?' checked':'';?>><label for="u_qianxian_del" class="checkskin-label"><i class="i1"></i><b class="W100">牵线删除</b></label></td>
</tr>


<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_kind" value="crm_user_kind" <?php echo (in_array('crm_user_kind',$AUTH))?' checked':'';?>><label for="crm_user_kind" class="checkskin-label"><i class="i1"></i><b class="W100">修改线上类型</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_ukind" value="crm_ukind" <?php echo (in_array('crm_ukind',$AUTH))?' checked':'';?>><label for="crm_ukind" class="checkskin-label"><i class="i1"></i><b class="W100">修改客户分类</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_grade" value="crm_user_grade" <?php echo (in_array('crm_user_grade',$AUTH))?' checked':'';?>><label for="crm_user_grade" class="checkskin-label"><i class="i1"></i><b class="W150">修改等级(签约升级)</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_home" value="crm_user_home" <?php echo (in_array('crm_user_home',$AUTH))?' checked':'';?>><label for="crm_user_home" class="checkskin-label"><i class="i1"></i><b class="W100">浏览客户主页</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_select" value="crm_user_select" <?php echo (in_array('crm_user_select',$AUTH))?' checked':'';?>><label for="crm_user_select" class="checkskin-label"><i class="i1"></i><b class="W100">用户查询</b></label></td>
</tr>

<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_contact" value="crm_user_contact" <?php echo (in_array('crm_user_contact',$AUTH))?' checked':'';?>><label for="crm_user_contact" class="checkskin-label"><i class="i1"></i><b class="W100">查看联系方法(门店名下)</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_contact_my" value="crm_user_contact_my" <?php echo (in_array('crm_user_contact_my',$AUTH))?' checked':'';?>><label for="crm_user_contact_my" class="checkskin-label"><i class="i1"></i><b class="W100">查看联系方法(自己名下)</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_bz" value="crm_user_bz" <?php echo (in_array('crm_user_bz',$AUTH))?' checked':'';?>><label for="crm_user_bz" class="checkskin-label"><i class="i1"></i><b class="W100">给客户备注</b></label></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left"></td>
</tr>


<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_match_list" value="crm_match_list" <?php echo (in_array('crm_match_list',$AUTH))?' checked':'';?>><label for="crm_match_list" class="checkskin-label"><i class="i1"></i><b class="W100">约见列表</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_match_view" value="crm_match_view" <?php echo (in_array('crm_match_view',$AUTH))?' checked':'';?>><label for="crm_match_view" class="checkskin-label"><i class="i1"></i><b class="W100">客户约见(查看)</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_match_add" value="crm_match_add" <?php echo (in_array('crm_match_add',$AUTH))?' checked':'';?>><label for="crm_match_add" class="checkskin-label"><i class="i1"></i><b class="W100">客户约见(增加)</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_match_mod" value="crm_match_mod" <?php echo (in_array('crm_match_mod',$AUTH))?' checked':'';?>><label for="crm_match_mod" class="checkskin-label"><i class="i1"></i><b class="W100">客户约见(修改)</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_match_del" value="crm_match_del" <?php echo (in_array('crm_match_del',$AUTH))?' checked':'';?>><label for="crm_match_del" class="checkskin-label"><i class="i1"></i><b class="W100">客户约见(删除)</b></label></td>
</tr>



<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_bbs_list" value="crm_bbs_list" <?php echo (in_array('crm_bbs_list',$AUTH))?' checked':'';?>><label for="crm_bbs_list" class="checkskin-label"><i class="i1"></i><b class="W100">跟进列表</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_bbs_view" value="crm_bbs_view" <?php echo (in_array('crm_bbs_view',$AUTH))?' checked':'';?>><label for="crm_bbs_view" class="checkskin-label"><i class="i1"></i><b class="W100">客户跟进(查看)</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_bbs_add" value="crm_bbs_add" <?php echo (in_array('crm_bbs_add',$AUTH))?' checked':'';?>><label for="crm_bbs_add" class="checkskin-label"><i class="i1"></i><b class="W100">客户跟进(增加)</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_bbs_mod" value="crm_bbs_mod" <?php echo (in_array('crm_bbs_mod',$AUTH))?' checked':'';?>><label for="crm_bbs_mod" class="checkskin-label"><i class="i1"></i><b class="W100">客户跟进(修改)</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_bbs_del" value="crm_bbs_del" <?php echo (in_array('crm_bbs_del',$AUTH))?' checked':'';?>><label for="crm_bbs_del" class="checkskin-label"><i class="i1"></i><b class="W100">客户跟进(删除)</b></label></td>
</tr>


<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_fav_view" value="crm_user_fav_view" <?php echo (in_array('crm_user_fav_view',$AUTH))?' checked':'';?>><label for="crm_user_fav_view" class="checkskin-label"><i class="i1"></i><b class="W150">客户收藏(查看)</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_fav_add" value="crm_user_fav_add" <?php echo (in_array('crm_user_fav_add',$AUTH))?' checked':'';?>><label for="crm_user_fav_add" class="checkskin-label"><i class="i1"></i><b class="W150">客户收藏(增加)</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_user_fav_del" value="crm_user_fav_del" <?php echo (in_array('crm_user_fav_del',$AUTH))?' checked':'';?>><label for="crm_user_fav_del" class="checkskin-label"><i class="i1"></i><b class="W150">客户收藏(删除)</b></label></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left"></td>
</tr>


<tr><td height="40" colspan="5" align="left" valign="bottom" class="S14 tiaose">售前分配管理</td></tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_hn_utask_sq_add" value="crm_hn_utask_sq_add" <?php echo (in_array('crm_hn_utask_sq_add',$AUTH))?' checked':'';?>><label for="crm_hn_utask_sq_add" class="checkskin-label"><i class="i1"></i><b class="W100">客户售前分配</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_hn_utask_sq_mod" value="crm_hn_utask_sq_mod" <?php echo (in_array('crm_hn_utask_sq_mod',$AUTH))?' checked':'';?>><label for="crm_hn_utask_sq_mod" class="checkskin-label"><i class="i1"></i><b class="W100">客户售前调配(换红娘)</b></label></td>
<td width="150" height="20" align="left"></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left"></td>
</tr>
<tr><td height="40" colspan="5" align="left" valign="bottom" class="S14 tiaose">售后分配管理</td></tr>

<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_hn_utask_sh_add" value="crm_hn_utask_sh_add" <?php echo (in_array('crm_hn_utask_sh_add',$AUTH))?' checked':'';?>><label for="crm_hn_utask_sh_add" class="checkskin-label"><i class="i1"></i><b class="W150">客户售后分配</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_hn_utask_sh_mod" value="crm_hn_utask_sh_mod" <?php echo (in_array('crm_hn_utask_sh_mod',$AUTH))?' checked':'';?>><label for="crm_hn_utask_sh_mod" class="checkskin-label"><i class="i1"></i><b class="W150">客户售后调配(换红娘)</b></label></td>
<td width="150" height="20" align="left"></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left">&nbsp;</td>
</tr>







<tr><td height="40" colspan="5" align="left" valign="bottom" class="S14 tiaose">合同管理</td></tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_ht_view" value="crm_ht_view" <?php echo (in_array('crm_ht_view',$AUTH))?' checked':'';?>><label for="crm_ht_view" class="checkskin-label"><i class="i1"></i><b class="W100">合同查看</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_ht_flag" value="crm_ht_flag" <?php echo (in_array('crm_ht_flag',$AUTH))?' checked':'';?>><label for="crm_ht_flag" class="checkskin-label"><i class="i1"></i><b class="W100">合同审核</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_ht_add" value="crm_ht_add" <?php echo (in_array('crm_ht_add',$AUTH))?' checked':'';?>><label for="crm_ht_add" class="checkskin-label"><i class="i1"></i><b class="W100">合同录入</b></label></td>
<td width="145" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_ht_mod" value="crm_ht_mod" <?php echo (in_array('crm_ht_mod',$AUTH))?' checked':'';?>><label for="crm_ht_mod" class="checkskin-label"><i class="i1"></i><b class="W100">合同修改</b></label></td>
<td width="153" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_ht_del" value="crm_ht_del" <?php echo (in_array('crm_ht_del',$AUTH))?' checked':'';?>><label for="crm_ht_del" class="checkskin-label"><i class="i1"></i><b class="W100">合同删除</b></label></td>
</tr>
<tr><td height="40" colspan="5" align="left" valign="bottom" class="S14 tiaose">财务管理</td></tr>
<tr>
<td width="152" height="30" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_pay_view" value="crm_pay_view" <?php echo (in_array('crm_pay_view',$AUTH))?' checked':'';?>><label for="crm_pay_view" class="checkskin-label"><i class="i1"></i><b class="W100">付款查看</b></label></td>
<td width="154" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_pay_flag" value="crm_pay_flag" <?php echo (in_array('crm_pay_flag',$AUTH))?' checked':'';?>><label for="crm_pay_flag" class="checkskin-label"><i class="i1"></i><b class="W100">付款审核</b></label></td>
<td width="150" height="20" align="left"><input type="checkbox" name="list[]" class="checkskin" id="crm_pay_analyse" value="crm_pay_analyse" <?php echo (in_array('crm_pay_analyse',$AUTH))?' checked':'';?>><label for="crm_pay_analyse" class="checkskin-label"><i class="i1"></i><b class="W100">财务统计/分析</b></label></td>
<td width="145" height="20" align="left"></td>
<td width="153" height="20" align="left"></td>
</tr>

</table>
    
    </td>
	</tr>
    <input name="submitok" type="hidden" value="ajax_addupdate">
    <?php if ($submitok == 'mod'){?>
      <input name="submitok" type="hidden" value="ajax_modupdate" />
      <input name="id" type="hidden" value="<?php echo $id;?>">
    <?php }else{ ?>
      <input name="submitok" type="hidden" value="ajax_addupdate" />
    <?php }?>
	</form>
	</table>
<br><br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">确认并保存</button></div>

	<script>
        save.onclick = function(){
			var oktips ='<b class="S18">确定保存么？</b><br>保存后，此角色组下属红娘权限将会更新<br><font class="Cf00">注：红娘退出CRM后重新登录方可生效</font>';
			zeai.confirm(oktips,function(){
				zeai.ajax({url:'crm_role'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){
						zeai.msg(rs.msg);
					   setTimeout(function(){zeai.openurl('crm_role.php')},1000);
					}else if(rs.flag == 0){
						zeai.msg(rs.msg,o(rs.focus));
					}else{
						zeai.msg(rs.msg);
					}		
				});
			});
        }
    </script>

<?php exit;}?>
<!--ADD MOD 结束-->


<div class="clear"></div>
<!--LIST-->
	<?php
	$rt = $db->query("SELECT id,crmkind,title FROM ".__TBL_ROLE__." WHERE kind=3 ORDER BY px DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodataico'><i></i>暂无信息<br><a class='btn size2 HUANG' onClick=\"zeai.iframe('新增角色','".SELF."?submitok=add',500,300)\">新增角色</a></div>";
	} else {
		$page_skin = 2;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	?>
	<table class="table0 W98_ Mbottom10 Mtop10">
	  <tr>
		<td width="150" align="left"><button type="button" class="btn tips" onClick="zeai.openurl('<?php echo SELF;?>?submitok=add')"><i class="ico add">&#xe620;</i>新增角色</button></td>
		<td align="left">&nbsp;</td>
	  </tr>
	</table>
	<table class="tablelist">
	<tr>
        <th width="50" align="center">角色ID</th>
        <th width="60" align="center">置顶</th>
        <th width="280">角色类型</th>
        <th width="200">角色名称</th>
        <th width="99" align="center">下属红娘数量</th>
        <th align="center">&nbsp;</th>
        <th width="120" class="center">设置/修改权限</th>
        <th width="50" class="center">删除</th>
	</tr>
	<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id    = $rows['id'];
		$title = dataIO($rows['title'],'out');
		$crmkind = dataIO($rows['crmkind'],'out');
		$unum = $db->COUNT(__TBL_CRM_HN__,"kind='crm' AND roleid=".$id);
	?>
	<tr>
	<td width="50" height="40" align="center"><?php echo $id;?></td>
	<td width="60" height="40" align="center"><a href="<?php echo "crm_role.php?id=".$id; ?>&submitok=ding" class="topico" title="置顶"></a></td>
	<td width="280" height="40" class="S14"><?php echo crm_crmkindtitle($crmkind,' - ');?></td>
    <td width="200" class="S14"><?php echo $title;?></td>
	<td width="99" align="center" ><span class="S14"><?php echo $unum;?></span></td>
	<td align="center">&nbsp;</td>
	<td width="120" class="center">
    
    <a class="editico tips" tips-title='修改权限' tips-direction='left' onClick="zeai.openurl('<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>')"></a>
    
    </td>
	<td width="50" class="center"><a value="<?php echo $id; ?>" unum="<?php echo $unum;?>" class="delico" title="<?php echo $title;?>"></a></td>
	</tr>
	<?php } ?>
	<?php if ($total > $pagesize){?>
	<tfoot><tr>
	<td colspan="8" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div></td>
	</tr></tfoot>
	<?php } ?>
	</table>
	<?php } ?>

<?php require_once 'bottomadm.php';?>

<script>

	zeai.listEach('.delico',function(obj){
		var id = parseInt(obj.getAttribute("value"));
		var unum = parseInt(obj.getAttribute("unum"));
		var title = obj.getAttribute("title");
		var tips = (unum>0)?'当前角色包含 '+unum+' 个成员':'';
		obj.onclick = function(){
			zeai.confirm('<font color="red">请慎重！</font><br>'+tips+'真的要删除【'+title+'】么？',function(){
				zeai.ajax('crm_role'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
</script>
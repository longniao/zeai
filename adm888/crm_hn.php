<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('crm_hn',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
if($submitok=='add_update' || $submitok=='mod_update'){
	if(!ifint($agentid))json_exit(array('flag'=>0,'msg'=>'请选择【所属门店】','focus'=>'agentid'));
	if(!ifint($roleid))json_exit(array('flag'=>0,'msg'=>'请选择【所属角色】','focus'=>'roleid'));
	if(empty($username))json_exit(array('flag'=>0,'msg'=>'请输入【CRM登录用户名】','focus'=>'username'));
	if(str_len($username)<3)json_exit(array('flag'=>0,'msg'=>'【CRM登录用户名】至少要3位长度','focus'=>'username'));
	if(empty($password) && $submitok=='add_update' )json_exit(array('flag'=>0,'msg'=>'请输入【CRM登录密码	】','focus'=>'password'));
	if(empty($truename))json_exit(array('flag'=>0,'msg'=>'请输入【红娘姓名】','focus'=>'truename'));
	$roleid = intval($roleid);
	
	$row = $db->ROW(__TBL_CRM_AGENT__,"title","id=".$agentid);
	if ($row){
		$agenttitle= $row[0];
	}else{json_exit(array('flag'=>0,'msg'=>'【门店】为空，请先去增加并设置【开启】状态','focus'=>'agentid'));}
	//
	$row = $db->ROW(__TBL_ROLE__,"title,crmkind","kind=3 AND id=".$roleid,"num");
	if ($row){
		$roletitle= $row[0];
		$crmkind  = $row[1];
	}else{json_exit(array('flag'=>0,'msg'=>'角色库为空，请先去增加','focus'=>'roleid'));}
	$username=trimhtml($username);
	$truename=trimhtml($truename);
	//
	$username = dataIO($username,'in',50);
	$truename= dataIO($truename,'in',100);
	$title   = dataIO($title,'in',200);
	$uid     = intval($uid);
	$sex     = intval($sex);
	$aboutus = dataIO($content,'in',5000);
	$ifwebshow = ($ifwebshow==1)?1:0;
	$mob    = dataIO($mob,'in',20);
	$qq     = dataIO($qq,'in',20);
	$weixin = dataIO($weixin,'in',50);
	$email  = dataIO($email,'in',100);
	$address = dataIO($address,'in',150);
	$claimnumday = abs(intval($claimnumday));
}

switch ($submitok) {
	case"modflag":
		if (!ifint($fid))callmsg("forbidden","-1");
		$row = $db->ROW(__TBL_CRM_HN__,"flag","id=".$fid,"num");
		if(!$row){
			alert_adm("您要操作的客户不存在","-1");
		}else{
			$flag = $row[0];
			$SQL = "";
			switch($flag){
				case"-1":$SQL="flag=1";break;
				case"0":$SQL="flag=1";break;
				case"1":$SQL="flag=-1";break;
			}
			$db->query("UPDATE ".__TBL_CRM_HN__." SET ".$SQL." WHERE kind='crm' AND id=".$fid." AND id<>".$_SESSION["admuid"]);
			header("Location: ".SELF."?p=$p");
		}
	break;
	case"ajax_pic_path_s_up":
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upMsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$dbname=setpath_s($dbname);
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case "add_update":
		$row = $db->ROW(__TBL_CRM_HN__,"id"," username='$username'");
		if ($row)json_exit(array('flag'=>0,'msg'=>'此用户名已被占用，请更换其它','focus'=>'username'));
		if(!empty($path_s)){
			adm_pic_reTmpDir_send($path_s,'crm');
			adm_pic_reTmpDir_send(getpath_smb($path_s,'b'),'crm');
			$path_s = str_replace('tmp','crm',$path_s);
		}
		if(!empty($ewm)){
			adm_pic_reTmpDir_send($ewm,'crm');
			adm_pic_reTmpDir_send(getpath_smb($ewm,'b'),'crm');
			$ewm = str_replace('tmp','crm',$ewm);
		}
		$kind     = 'crm';
		$password = md5(trim($password));
		$db->query("INSERT INTO ".__TBL_CRM_HN__." (claimnumday,crmkind,agentid,agenttitle,roleid,roletitle,truename,password,username,kind,title,uid,sex,aboutus,ifwebshow,mob,qq,weixin,email,address,path_s,ewm,px,addtime) VALUES ('$claimnumday','$crmkind','$agentid','$agenttitle','$roleid','$roletitle','$truename','$password','$username','$kind','$title','$uid','$sex','$aboutus','$ifwebshow','$mob','$qq','$weixin','$email','$address','$path_s','$ewm',".ADDTIME.",".ADDTIME.")");
		json_exit(array('flag'=>1,'msg'=>'增加成功'));
	break;
	case"mod_update":
		if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		//门店权限
		if(!in_array('crm',$QXARR)){
			$SQL=" AND agentid=$session_agentid";
		}else{$SQL="";}
		//门店权限结束
		$row = $db->ROW(__TBL_CRM_HN__,"path_s,ewm","kind='crm' AND id=".$fid.$SQL);
		if (!$row)json_exit(array('flag'=>0,'msg'=>'权限不足FID'.$fid));
		$data_path_s = $row[0];
		$data_ewm    = $row[1];
		$SQL="";
		if ($username != $oldusername){
			$row = $db->ROW(__TBL_CRM_HN__,"id"," username='$username'");
			if ($row)json_exit(array('flag'=>0,'msg'=>'此用户名已被占用，请更换其它','focus'=>'username'));
			
			$SQL .= ",username='$username'";
			if ($_SESSION["admuid"] == $fid)$_SESSION["admuname"] = $username;
		}
		if(!empty($password)){
			$password = md5(trim($password));
			if ($password != $oldpassword){
				$SQL .= ",password='$password'";
				if ($_SESSION["admuid"] == $fid)$_SESSION["admpwd"] = $password;
			}
		}
		/******************************************** 主图path_s ********************************************/
		//提交空，数据库有，删老
		if(empty($path_s) && !empty($data_path_s)){
			$B = getpath_smb($data_path_s,'b');
			@up_send_admindel($data_path_s.'|'.$B);
		//提交有，数据库无
		}elseif(!empty($path_s) && empty($data_path_s)){
			//上新
			adm_pic_reTmpDir_send($path_s,'crm');
			adm_pic_reTmpDir_send(getpath_smb($path_s,'b'),'crm');
			$path_s = str_replace('tmp','crm',$path_s);
		//提交有，数据库有
		}elseif(!empty($path_s) && !empty($data_path_s)){
			//有改动
			if($path_s != $data_path_s){
				//删老
				$B = getpath_smb($data_path_s,'b');
				@up_send_admindel($data_path_s.'|'.$B);
				//上新
				adm_pic_reTmpDir_send($path_s,'crm');
				adm_pic_reTmpDir_send(getpath_smb($path_s,'b'),'crm');
				$path_s = str_replace('tmp','crm',$path_s);
			}
		}
		/******************************************** ewm ********************************************/
		//提交空，数据库有，删老
		if(empty($ewm) && !empty($data_ewm)){
			$B = getpath_smb($data_ewm,'b');
			@up_send_admindel($data_ewm.'|'.$B);
		//提交有，数据库无
		}elseif(!empty($ewm) && empty($data_ewm)){
			//上新
			adm_pic_reTmpDir_send($ewm,'crm');
			adm_pic_reTmpDir_send(getpath_smb($ewm,'b'),'crm');
			$ewm = str_replace('tmp','crm',$ewm);
		//提交有，数据库有
		}elseif(!empty($ewm) && !empty($data_ewm)){
			//有改动
			if($ewm != $data_ewm){
				//删老
				$B = getpath_smb($data_ewm,'b');
				@up_send_admindel($data_ewm.'|'.$B);
				//上新
				adm_pic_reTmpDir_send($ewm,'crm');
				adm_pic_reTmpDir_send(getpath_smb($ewm,'b'),'crm');
				$ewm = str_replace('tmp','crm',$ewm);
			}
		}
		
		$db->query("UPDATE ".__TBL_CRM_HN__." SET claimnumday='$claimnumday',crmkind='$crmkind',agentid='$agentid',agenttitle='$agenttitle',roleid='$roleid',roletitle='$roletitle',truename='$truename',title='$title',uid='$uid',sex='$sex',aboutus='$aboutus',ifwebshow='$ifwebshow',mob='$mob',qq='$qq',weixin='$weixin',email='$email',address='$address',path_s='$path_s',ewm='$ewm' ".$SQL." WHERE id=".$fid);
		$db->query("UPDATE ".__TBL_USER__." SET admname='$truename' WHERE admid=".$fid);
		$db->query("UPDATE ".__TBL_USER__." SET hnname='$truename' WHERE hnid=".$fid);
		$db->query("UPDATE ".__TBL_USER__." SET hnname2='$truename' WHERE hnid2=".$fid);
		$db->query("UPDATE ".__TBL_CRM_MATCH__." SET admname='$truename' WHERE admid=".$fid);
		$db->query("UPDATE ".__TBL_CRM_BBS__." SET admname='$truename' WHERE admid=".$fid);
		$db->query("UPDATE ".__TBL_CRM_FAV__." SET admname='$truename' WHERE admid=".$fid);
		$db->query("UPDATE ".__TBL_CRM_CLAIM_LIST__." SET admname='$truename' WHERE admid=".$fid);
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case"ajax_del":
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'红娘不存在或已被删除'));
		//门店权限
		if(!in_array('crm',$QXARR)){
			$SQL=" AND agentid=$session_agentid";
		}else{$SQL="";}
		//门店权限结束
		$rt = $db->query("SELECT path_s,ewm FROM ".__TBL_CRM_HN__." WHERE kind='crm' AND id=".$fid.$SQL);
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
			for($i=1;$i<=$total;$i++) {
				$row = $db->fetch_array($rt,'name');
				if(!$row) break;
				$path_s = $row['path_s'];
				$ewm    = $row['ewm'];
				if(!empty($path_s)){
					$B = getpath_smb($path_s,'b');@up_send_admindel($path_s.'|'.$B);
				}
				if(!empty($ewm)){
					$B = getpath_smb($ewm,'b');@up_send_admindel($ewm.'|'.$B);
				}
			}
			$db->query("DELETE FROM ".__TBL_CRM_HN__." WHERE kind='crm' AND id=".$fid);
			$db->query("UPDATE ".__TBL_USER__." SET admid=0,admname='',admtime=0 WHERE admid=".$fid);
			$db->query("UPDATE ".__TBL_USER__." SET hnid=0,hnname='',hntime=0 WHERE hnid=".$fid);
			$db->query("UPDATE ".__TBL_USER__." SET hnid2=0,hnname2='',hntime2=0 WHERE hnid2=".$fid);
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($fid))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_CRM_HN__." SET px=".ADDTIME." WHERE id=".$fid);
		header("Location: ".SELF);
	break;
	case"mod":
		if (!ifint($fid))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_CRM_HN__." WHERE id=".$fid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$path_s    = $row['path_s'];
			$ewm       = $row['ewm'];
			$roleid    = intval($row['roleid']);
			$agentid    = intval($row['agentid']);
			$agenttitle = dataIO($row['agenttitle'],'out');
			$password  = $row['password'];
			$roletitle = dataIO($row['roletitle'],'out');
			$truename  = dataIO($row['truename'],'out');
			$username  = dataIO($row['username'],'out');
			$title     = dataIO($row['title'],'out');
			$uid       = intval($row['uid']);
			$sex       = intval($row['sex']);
			$ifwebshow_ = intval($row['ifwebshow']);
			$content   = dataIO($row['aboutus'],'out');
			$mob       = dataIO($row['mob'],'out');
			$qq        = dataIO($row['qq'],'out');
			$weixin    = dataIO($row['weixin'],'out');
			$email     = dataIO($row['email'],'out');
			$address   = dataIO($row['address'],'out');
			$claimnumday = intval($row['claimnumday']);
		}else{
			alert_adm("该红娘不存在！","-1");
		}
	break;
}
require_once ZEAI.'cache/udata.php';
$extifshow = json_decode($_UDATA['extifshow'],true);


$SQL=" kind='crm' ";
//门店
if(!in_array('crm',$QXARR)){
	$SQL .=" AND agentid=$session_agentid";
}
if (ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND agentid=$agentid";
//门店结束


if (!empty($Skeyword)){
	$Skeyword = trimm($Skeyword);
	if(ifint($Skeyword)){
		$SQL .= " AND id=".$Skeyword;
	}else{
		$SQL .= " AND ( truename LIKE '%".dataIO($Skeyword,'in')."%' OR username LIKE '%".dataIO($Skeyword,'in')."%' ) ";
	}
}
if($ifwebshow==1)$SQL.=" AND ifwebshow=1";
if($ifwebshow==-1)$SQL.=" AND ifwebshow<>1";
if(!empty($crmkind))$SQL.=" AND FIND_IN_SET('$crmkind',crmkind)";
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php if ($submitok == 'add' || $submitok == 'mod'){?>
<!-- editor -->
<link rel="stylesheet" href="editor/themes/default/default.css?<?php echo $_ZEAI['cache_str'];?>" />
<script charset="utf-8" src="editor/kindeditor.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var editor;
KindEditor.ready(function(K){
  editor=K.create('textarea[name="content"]',{
	resizeType :1,
	cssData:'body {font-family: "微软雅黑"; font-size: 14px}',
	minWidth : 400,
	allowPreviewEmoticons : true,
	allowImageUpload : true,
	afterBlur:function(){this.sync();},
	items : [
		'undo','redo','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','removeformat', '|', 'insertorderedlist','insertunorderedlist', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull','lineheight','|',
		'selectall','quickformat', '|','image','multiimage','media', '|','plainpaste','wordpaste','hr', 'link', 'unlink','baidumap', '|','clearhtml','source', '|','preview','fullscreen']
  });
});
var up2='<?php echo $_ZEAI['up2'];?>/',upMaxMB=<?php echo $_UP['upMaxMB']; ?>;
</script>
<!--editor end -->
<?php }?>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.table0{min-width:1000px;width:98%;margin:10px 20px 20px 15px}

i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
.textarea_k{text-align:left}
.picli{padding:0px}
.picli li{width:100px;height:100px;line-height:100px;border:#eee 1px solid;box-sizing:border-box;cursor:pointer;float:left;margin:10px 15px 10px 0;text-align:center;position:relative}
.picli li.add,.picli li i{background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.picli li.add{background-size:150px 100px;background-repeat:no-repeat;background-position:-2px -2px;border:#dedede 2px dashed}
.picli li:hover{background-color:#f5f7f9}
.picli li img{vertical-align:middle;margin-top:-5px;max-width:98px;max-height:98px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;cursor:zoom-in}
.picli li:hover .img{cursor:zoom-in}
.picli li i{width:20px;height:20px;top:-10px;right:-10px;position:absolute;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3)}
.picli li i:hover{background-position:-100px top;cursor:pointer}
.picli #picmore{display:none}

.pathlist img{margin:0 2px 2px 2px;width:30px;height:30px}
i.top{font-size:18px;color:#FF5722}

.ewmpic{width:30px;height:30px;border:#e6e6e6 1px solid;filter:alpha(opacity=40);-moz-opacity:0.4;opacity:0.4}
.ewmpic:hover{filter:alpha(opacity=100);-moz-opacity:1;opacity:1}

</style>
<body>
<div class="navbox">
	<a href="crm_hn.php?agentid=<?php echo $agentid;?>"<?php echo (empty($ifwebshow) && empty($crmkind))?' class="ed"':'';?>>红娘管理<?php if(empty($ifwebshow) && empty($crmkind))echo '<b>'.$db->COUNT(__TBL_CRM_HN__,$SQL).'</b>';?></a>
	<a href="crm_hn.php?ifwebshow=1&agentid=<?php echo $agentid;?>"<?php echo ($ifwebshow==1)?' class="ed"':'';?>>网站显示<?php if($ifwebshow==1)echo '<b>'.$db->COUNT(__TBL_CRM_HN__,$SQL." AND ifwebshow=1 ").'</b>';?></a>
	<a href="crm_hn.php?ifwebshow=-1&agentid=<?php echo $agentid;?>"<?php echo ($ifwebshow==-1)?' class="ed"':'';?>>内部红娘<?php if($ifwebshow==-1)echo '<b>'.$db->COUNT(__TBL_CRM_HN__,$SQL." AND ifwebshow<>1 ").'</b>';?></a>

	<a href="crm_hn.php?crmkind=adm&agentid=<?php echo $agentid;?>"<?php echo ($crmkind=='adm')?' class="ed"':'';?>>管理员<?php if($crmkind=='adm')echo '<b>'.$db->COUNT(__TBL_CRM_HN__,$SQL." AND FIND_IN_SET('adm',crmkind) ").'</b>';?></a>
	<a href="crm_hn.php?crmkind=sq&agentid=<?php echo $agentid;?>"<?php echo ($crmkind=='sq')?' class="ed"':'';?>>售前<?php if($crmkind=='sq')echo '<b>'.$db->COUNT(__TBL_CRM_HN__,$SQL." AND  FIND_IN_SET('sq',crmkind) ").'</b>';?></a>
	<a href="crm_hn.php?crmkind=sh&agentid=<?php echo $agentid;?>"<?php echo ($crmkind=='sh')?' class="ed"':'';?>>售后<?php if($crmkind=='sh')echo '<b>'.$db->COUNT(__TBL_CRM_HN__,$SQL." AND  FIND_IN_SET('sh',crmkind) ").'</b>';?></a>
	<a href="crm_hn.php?crmkind=ht&agentid=<?php echo $agentid;?>"<?php echo ($crmkind=='ht')?' class="ed"':'';?>>合同<?php if($crmkind=='ht')echo '<b>'.$db->COUNT(__TBL_CRM_HN__,$SQL." AND  FIND_IN_SET('ht',crmkind) ").'</b>';?></a>
	<a href="crm_hn.php?crmkind=cw&agentid=<?php echo $agentid;?>"<?php echo ($crmkind=='cw')?' class="ed"':'';?>>财务<?php if($crmkind=='cw')echo '<b>'.$db->COUNT(__TBL_CRM_HN__,$SQL." AND  FIND_IN_SET('cw',crmkind) ").'</b>';?></a>


    <div class="Rsobox">
    </div>
<div class="clear"></div></div>
<div class="fixedblank60"></div>
<!---->
<?php
/************************************** 【发布】【修改】 add **************************************/
if ($submitok == 'add' || $submitok == 'mod'){?>
<!--【发布】-->

    <table class="table Mtop20" style="width:1111px;margin:0 0 100px 20px">
    <form id="Www_zeai_cn_form">

    <tr><td class="tdL"><font class="Cf00">*</font>所属门店</td><td class="tdR">
    <?php if(in_array('crm',$QXARR)){?>
    <select name="agentid" id="agentid" class="W200 size2" required>
	<?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('【门店】为空，请先去增加并设置【开启】状态','crm_agent.php');
    } else {
    ?>
    <option value="">选择门店</option>
    <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
			$clss=($agentid==$rows2[0])?' selected':'';
            echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
        }
    }
    ?></select>
    <?php }else{ ?>
    <input name="agentid" id="agentid" type="hidden" value="<?php echo $session_agentid;?>" />
    <?php echo $session_agenttitle;?>
    <?php }?>
    </td>
      <td class="tdL"><font class="Cf00">*</font>所属角色</td>
      <td class="tdR"><select name="roleid" id="roleid" class="W200 size2" required>
	<?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_ROLE__." WHERE kind=3 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('请先增加红娘角色','crm_role.php');
    } else {
    ?>
    <option value="">选择角色</option>
    <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
			$clss=($roleid==$rows2[0])?' selected':'';
            echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
        }
    }
    ?></select></td>
    </tr>
    
    
    
    <tr><td class="tdL"><font class="Cf00">*</font>CRM登录帐号</td><td class="tdR"><input name="username" id="username" type="text" class="input size2 W200" maxlength="20" value="<?php echo $username;?>" /><br><span class="tips2">3~15位英文字母或加数字组合；如：zeai，zeai_123</span></td>
      <td class="tdL"><font class="Cf00">*</font>CRM登录密码</td>
      <td class="tdR"><input name="password" id="password" type="text" class="input size2 W200" maxlength="20" /><br><span class="tips2 S12"><?php if ($submitok == 'mod'){?>不改请留空<?php }else{echo '6~20位英文字母或加数字组合';}?></span></td>
    </tr>
    <tr><td class="tdL"><font class="Cf00">*</font>每天认领人数</td><td colspan="3" class="tdR S12"><input id="claimnumday" name="claimnumday" type="text" class="input W100 size2" maxlength="6" value="<?php echo $claimnumday;?>"><span class="tips">每天从【公海用户】中认领总人数，如果门店总人数超过限额将不能认领；填0将不能认领，推荐平均分配，比如门店总量为30，有3个员工，这里就填10</span></td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font>红娘姓名</td><td class="tdR"><input name="truename" id="truename" type="text" class="input size2 W100" maxlength="20" value="<?php echo $truename;?>" /><span class="tips S12">如：郭余林、郭美美、李老师等，用于显示的名子</span></td>
      <td class="tdL"><font class="Cf00">*</font>性别</td>
      <td class="tdR">
    <input type="radio" name="sex" id="sex1" class="radioskin" value="1"<?php echo ($sex == 1)?' checked':'';?>><label for="sex1" class="radioskin-label"><i class="i1"></i><b class="W50 S14">男</b></label>
    <input type="radio" name="sex" id="sex2" class="radioskin" value="2"<?php echo ($sex == 2 || empty($sex))?' checked':'';?>><label for="sex2" class="radioskin-label"><i class="i1"></i><b class="W50 S14">女</b></label></td>
    </tr>
    <tr><td class="tdL">是否网站展示</td><td class="tdR">
    <input type="checkbox" name="ifwebshow" id="ifwebshow" class="switch" value="1"<?php echo ($ifwebshow_ == 1)?' checked':'';?>><label for="ifwebshow" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
    <div class="tips2">开启后，前端将展示，【线上】客户可以自主选择认领</div>
    </td>
      <td class="tdL">绑定网站UID</td>
      <td class="tdR"><input name="uid" id="uid" type="text" class="input size2 W100" maxlength="8" value="<?php echo $uid;?>" /><span class="tips S12">绑定后，关注公众号后会接收到通知</span></td>
    </tr>
    <tr>
    
    <td class="tdL"><font class="Cf00">*</font>红娘头像<br>(比例正方形)</td>
    <td class="tdR">
        <div class="picli" id="picli_path">
        	<li class="add" id="path_add"></li>
			<?php if(!empty($path_s)){
				echo '<li><img src="'.$_ZEAI['up2'].'/'.$path_s.'"><i></i></li>';
			}?>
        </div>
        <br><br><span class="tips S12">无头像前台网站展示将不显示</span>
	</td>
    <td class="tdL"><font class="Cf00">*</font>微信二维码</td>
    <td class="tdR">
        <div class="picli" id="ewm_path">
        	<li class="add" id="ewm_add"></li>
			<?php if(!empty($ewm)){
				echo '<li><img src="'.$_ZEAI['up2'].'/'.$ewm.'"><i></i></li>';
			}?>
        </div>
        <br><br><span class="tips S12">将显示到认领客户的个人主页中或自己的主页中</span></td>
    </tr>
    <tr><td class="tdL"><font class="Cf00">*</font>手机</td><td class="tdR C8d"><input name="mob" id="mob" type="text" class="input size2 W300" maxlength="100" value="<?php echo $mob;?>" /></td>
      <td class="tdL "><font class="Cf00">*</font>微信</td>
      <td class="tdR "><input name="weixin" id="weixin" type="text" class="input size2 W300" maxlength="100" value="<?php echo $weixin;?>" /></td>
    </tr>
    <tr><td class="tdL">QQ</td><td class="tdR"><input name="qq" id="qq" type="text" class="input size2 W300" maxlength="20" value="<?php echo $qq;?>" /></td>
      <td class="tdL">邮箱</td>
      <td class="tdR"><input name="email" id="email" type="text" class="input size2 W300" maxlength="100" value="<?php echo $email;?>" /></td>
    </tr>
    <tr><td class="tdL">一句话概况</td><td class="tdR"><textarea name="title" rows="3" class="textarea W300 S14" id="title" placeholder="比如服务资历，座佑铭，人生格言，三观等"><?php echo $title;?></textarea></td>
      <td class="tdL">地址</td>
      <td class="tdR"><input name="address" id="address" type="text" class="input size2 W300"  maxlength="100" value="<?php echo $address;?>" /></td>
    </tr>

    <tr><td class="tdL">红娘详细介绍</td><td colspan="3" class="tdR S12"><img src="images/!.png" width="14" height="14" class="picmiddle"> <font style="vertical-align:middle;color:#999">如果从公众号编辑器或外部网页或Word里拷入内容请先过虑垃圾代码，请点击下方 <img src="images/cclear.png" class="picmiddle"> 图标，然后插入文字内容</font><textarea name="content" id="content" class="textarea_k" style="width:100%;height:300px" ><?php echo $content;?></textarea></td></tr>
      <input name="path_s" id="path_s" type="hidden" value="" />
      <input name="ewm" id="ewm" type="hidden" value="" />
      <input name="pathlist" id="pathlist" type="hidden" value="" />
      <input name="p" type="hidden" value="<?php echo $p;?>" />
      <?php if ($submitok == 'mod'){?>
          <input name="submitok" type="hidden" value="mod_update" />
          <input name="fid" type="hidden" value="<?php echo $fid;?>" />
          <input name="oldpassword" type="hidden" value="<?php echo $password;?>" />
          <input name="oldusername" type="hidden" value="<?php echo $username;?>" />
      <?php }else{ ?>
          <input name="submitok" type="hidden" value="add_update" />
      <?php }?>
    </form>
    </table>
<br><br><br><br><div class="savebtnbox"><button type="button" id="submit_add" class="btn size3 HUANG3">确认并保存</button></div>

<script>
	<?php if($submitok=='mod'){?>
	window.onload=function(){
		path_s_mod();
		function path_s_mod(){
			var i=zeai.tag(picli_path,'i')[0],img=zeai.tag(picli_path,'img')[0];
			if(zeai.empty(i))return;
			path_add.hide();
			var src=img.src.replace(up2,'');
			path_s.value=src;
			i.onclick = function(){
				zeai.confirm('亲~~确认删除么？',function(){
					img.parentNode.remove();path_add.show();path_s.value='';
				});
			}
			img.onclick = function(){parent.piczoom(up2+src.replace('_s.','_b.'));}
		}
		ewm_mod();
		function ewm_mod(){
			var i=zeai.tag(ewm_path,'i')[0],img=zeai.tag(ewm_path,'img')[0];
			if(zeai.empty(i))return;
			ewm_add.hide();
			var src=img.src.replace(up2,'');
			ewm.value=src;
			i.onclick = function(){
				zeai.confirm('亲~~确认删除么？',function(){
					img.parentNode.remove();ewm_add.show();ewm.value='';
				});
			}
			img.onclick = function(){parent.piczoom(up2+src.replace('_s.','_b.'));}
		}
	}
	<?php }?>
		zeai.photoUp({
			btnobj:path_add,
			upMaxMB:upMaxMB,
			url:"crm_hn"+zeai.extname,
			submitok:"ajax_pic_path_s_up",
			end:function(rs){
				zeai.msg(0);zeai.msg(rs.msg);
				if (rs.flag == 1){
					picli_path.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');
					path_s.value=rs.dbname;
					path_add.hide();
					var i=zeai.tag(o(picli_path),'i')[0],img=zeai.tag(o(picli_path),'img')[0];
					i.onclick = function(){
						zeai.confirm('亲~~确认删除么？',function(){
							img.parentNode.remove();path_add.show();path_s.value='';
						});
					}
					img.onclick = function(){parent.piczoom(up2+rs.dbname.replace('_s.','_b.'));}
				}
			}
		});
		zeai.photoUp({
			btnobj:ewm_add,
			upMaxMB:upMaxMB,
			url:"crm_hn"+zeai.extname,
			submitok:"ajax_pic_path_s_up",
			end:function(rs){
				zeai.msg(0);zeai.msg(rs.msg);
				if (rs.flag == 1){
					ewm_path.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');
					ewm.value=rs.dbname;
					ewm_add.hide();
					var i=zeai.tag(o(ewm_path),'i')[0],img=zeai.tag(o(ewm_path),'img')[0];
					i.onclick = function(){
						zeai.confirm('亲~~确认删除么？',function(){
							img.parentNode.remove();ewm_add.show();ewm.value='';
						});
					}
					img.onclick = function(){parent.piczoom(up2+rs.dbname.replace('_s.','_b.'));}
				}
			}
		});
		submit_add.onclick=function(){
			zeai.confirm('确定检查无误发布提交么？',function(){
				zeai.ajax({url:'crm_hn'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);
					if(rs.flag==1){
						zeai.msg(rs.msg,{time:1});
						setTimeout(function(){zeai.openurl('crm_hn'+zeai.ajxext+'&p=<?php echo $p;?>'+'&agentid='+agentid.value);},1000);
					}else{
						zeai.msg(rs.msg,{time:1,focus:o(rs.focus)});
					}
				});
			});
		}
    </script>
<!--【发布 修改 结束】-->
<?php
/************************************** 【列表】 list **************************************/
exit;}else{
	$rt = $db->query("SELECT id,uid,path_s,username,truename,agenttitle,roletitle,flag,title,addtime,ifwebshow,crmkind,claimnumday FROM ".__TBL_CRM_HN__." WHERE ".$SQL." ORDER BY px DESC LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回上一步</a><br><br>";?>
        <button type="button" class="btn size2" onClick="zeai.openurl('crm_hn.php?submitok=add')">新增红娘</button>
		<?php
		echo "</div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
		?>
		
		<table class="tablelist" style="min-width:1111px">
        <tr><td colspan="15" class="searchli">
        
        
        <form name="form1" method="get" action="<?php echo SELF; ?>">
        	<button type="button" class="btn size2" onClick="zeai.openurl('crm_hn.php?submitok=add')"><i class="ico add">&#xe620;</i>新增红娘</button>　　　　
            <span class="textmiddle">按红娘</span> <input name="Skeyword" type="text" id="Skeyword" maxlength="15" class="input size2 W200" placeholder="输入红娘CRM用户名/ID/姓名" value="<?php echo $Skeyword;?>">
            <!--超管按门店查询-->
            <?php if(in_array('crm',$QXARR)){?>
                <?php
                $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
                $total2 = $db->num_rows($rt2);
                if ($total2 > 0) {
                    ?><span class="textmiddle">　　按门店</span> 
                    <select name="agentid" class="W200 size2 textmiddle">
                    <option value="">全部门店</option>
                    <?php
                        for($j=0;$j<$total2;$j++) {
                            $rows2 = $db->fetch_array($rt2,'num');
                            if(!$rows2) break;
                            $clss=($agentid==$rows2[0])?' selected':'';
                            ?>
                            <option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[1],'out');?></option>
                            <?php
                        }
                    ?>
                    </select>
                    <?php
                }
                ?>
            <?php }?>            
            　<button type="submit" class="btn size2 QING picmiddle"><i class="ico">&#xe6c4;</i> 开始筛选</button>　
            <input name="ifwebshow" type="hidden" value="<?php echo $ifwebshow; ?>" />
            <input name="crmkind" type="hidden" value="<?php echo $crmkind; ?>" />
            <input type="hidden" name="p" value="<?php echo $p;?>" />
        </form>     
        
        
        </td></tr>
        <form id="www_zeai_cn_FORM">
		<tr>
		<th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label" style="display:none"><i class="i1"></i></label></th>
		<th width="50" align="center">红娘ID</th>
		<th width="50" align="left">置顶</th>
		<th width="80" align="left">头像</th>
		<th width="150" align="left">红娘姓名/CRM帐号</th>
		<th width="100" align="center">所属门店</th>
		<th width="100" align="center">所属角色/类型</th>
		<th width="70" align="center" title="录入/认领+分配">名下客户</th>
		<th width="70" align="center">网站显示</th>
		<th width="120" align="center">每天公海认领(人)</th>
		<th align="center">名片二维码</th>
		<th width="70" align="center">加入时间</th>
		<th width="60" align="center">红娘状态</th>
		<th width="50" align="center">修改</th>
		<th width="50" align="center">删除</th>
		</tr>
		<?php
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$id        = $rows['id'];
			$path_s    = $rows['path_s'];
			$unum      = $rows['unum'];
			$flag      = $rows['flag'];
			$claimnumday = intval($rows['claimnumday']);
			$addtime   = YmdHis($rows['addtime']);
			$agenttitle = dataIO($rows['agenttitle'],'out');
			$roletitle = dataIO($rows['roletitle'],'out');
			$truename  = dataIO($rows['truename'],'out');
			$username  = dataIO($rows['username'],'out');
			$title     = dataIO($rows['title'],'out');
			$uid       = intval($rows['uid']);
			$sex       = intval($rows['sex']);
			$ifwebshow = intval($rows['ifwebshow']);
			$mob       = dataIO($rows['mob'],'out');
			$crmkind   = dataIO($rows['crmkind'],'out');
			//
			if(!empty($Skeyword)){
				$truename = str_replace($Skeyword,'<font class="Cf00 B">'.$Skeyword.'</font>',$truename);
			}
			if(!empty($path_s)){
				$path_s_url = $_ZEAI['up2'].'/'.$path_s;
				$path_s_str = '<img src="'.$path_s_url.'">';
			}else{
				$path_s_url = '';
				$path_s_str = '';
			}
			$href = Href('hn',$id);
			$unum1 = $db->COUNT(__TBL_USER__,"hnid=".$id." OR hnid2=".$id." OR admid=".$id);
			$ifwebshow_str = ($ifwebshow==1)?"<i class='ico S18' style='color:#45C01A'>&#xe60d;</i>":'<span class=" C999">隐藏</span>';
		?>
		<tr id="tr<?php echo $id;?>">
		<td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
		<td width="50" align="center" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
		<td width="50" align="left">
        <a href="<?php echo "crm_hn.php?fid=".$id; ?>&submitok=ding" class="topico" title="置顶"></a>
        </td>
		<td width="80" align="left" style="padding:10px 0">
			<?php if (empty($path_s_url)){?>
            <a href="javascript:;" class="pic60 ">无图</a>
            <?php }else{ ?>
            <a href="javascript:;" class="pic60 " onClick="parent.piczoom('<?php echo getpath_smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a>
            <?php }?>
        </td>
		<td width="150" align="left" class="C999">
        <div class="S14 C000"><?php echo $truename;?></div >
        <?php echo $username;?><br>
        <?php if(ifint($uid)){?>
        	
        	绑定网站UID：<a href="<?php echo Href('u',$uid);?>" target="_blank"><?php echo $uid;?></a>
        
        <?php }?>
        </td>
		<td width="100" align="center" class="S14"><?php echo $agenttitle;?></td>
		<td width="100" align="center" class="S14"><?php echo $roletitle;?><div class="S12 C999" style="margin-top:3px"><?php echo crm_crmkindtitle($crmkind,' - ');?></div></td>
		<td width="70" align="center">
        
        <a href="crm_user.php?Skeyword2=<?php echo $id;?>" title="名下客户正在服务中" clsid="<?php echo $id;?>" class="aHUI " title2="<?php echo urlencode(strip_tags($title));?>"><?php echo ($unum1>0)?'<font class="Cf00 FArial S14">'.$unum1.'</font>人':$unum1;?></a>
        
        </td>
		<td width="70" align="center">
        <?php echo $ifwebshow_str;?></td>
		<td width="120" align="center" class="S14"><?php echo $claimnumday;?></td>
		<td align="center" ><a href="javascript:parent.zeai.iframe('【<?php echo trimhtml($truename);?>】二维码','hn_ewm.php?id=<?php echo $id;?>',550,550);" title="放大二维码" class="zoom"><img src="images/ewm.gif" class="ewmpic"></a></td>
		<td width="70" align="center" class="C999"><?php echo $addtime;?></td>
		<td width="60" align="center" class="C999">
  <?php
$fHREF = SELF."?submitok=modflag&fid=$id&p=$p";
if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击恢复">锁定</a><?php }?>
  <?php if($flag==0){?><a href="<?php echo $fHREF;?>" class="aHUANG">未审</a><?php }?>
  <?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击锁定">正常</a><?php }?>

        </td>
		<td width="50" align="center">
		  <a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>"></a>
		</td>
		<td width="50" align="center"><a clsid="<?php echo $id; ?>" class="delico" title='删除'></a></td>
		</tr>
		<?php } ?>
        <div class="listbottombox" style="text-align:center">
            <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label" style="display:none"><i class="i1"></i></label>　
            <input type="hidden" name="submitok" id="submitok" value="" />
            <?php if ($total > $pagesize)echo '<div class="pagebox">'.$pagelist.'</div>'; ?>
        </div>
        </form>
		</table>
		<script>
		var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
		zeai.listEach('.editico',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.openurl('crm_hn.php?p=<?php echo $p;?>&submitok=mod&fid='+id);
			}
		});
		zeai.listEach('.delico',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.confirm('<b class="S18">★请慎重★　确定真的要删除么？</b><br>删除后将清空所有名下用户红娘归属为空无法恢复',function(){
					zeai.ajax({url:'crm_hn.php?submitok=ajax_del&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.bbsadm',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			var title=obj.getAttribute("title2");
			obj.onclick = function(){
				zeai.iframe('【'+decodeURIComponent(title)+'】评论管理','party_bbs.php?fid='+id,700,600);
			}
		});
		</script>
		<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }}?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
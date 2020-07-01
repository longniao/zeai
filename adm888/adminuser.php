<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('adminuser',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';

if($submitok == 'ajax_binding_gzh_cancel'){
	$admid=intval($admid);
	$row = $db->ROW(__TBL_ADMIN__,"openid,subscribe,truename","id=".$admid,"num");$data_openid=$row[0];$data_subscribe=$row[1];$data_truename=dataIO($row[2],'out');
	$db->query("UPDATE ".__TBL_ADMIN__." SET openid='',unionid='',subscribe=0 WHERE id=".$admid);
	if(!empty($data_openid) && $data_subscribe==1)@wx_kf_sent($data_openid,urlencode('后台管理员/红娘帐号【'.$data_truename.'(ID：'.$admid.')】公众号解绑成功！'),'text');
	AddLog('【管理员/红娘】->解除绑定公众号成功->【'.$data_truename.'(ID：'.$admid.')】');
	json_exit(array('flag'=>1,'msg'=>'解绑成功'));
}elseif($submitok == 'ajax_binding_gzh'){
	$admid=intval($admid);
	$row = $db->ROW(__TBL_ADMIN__,"openid,subscribe,truename","id=".$admid,"num");$data_openid=$row[0];$data_subscribe=$row[1];$data_truename=dataIO($row[2],'out');
	if (str_len($data_openid) >10 && $data_subscribe==1){
		json_exit(array('flag'=>1,'msg'=>'已成功绑定【'.$data_truename.'(ID：'.$admid.')】'));
	}
	json_exit(array('flag'=>0));
}elseif($submitok == 'ajax_get_ewm'){
	$token = wx_get_access_token();
	$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
	$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"bdadm_'.$admid.'"}}}';
	$ticket = Zeai_POST_stream($ticket_url,$ticket_data);
	$T = json_decode($ticket,true);
	$qrcode_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
	json_exit(array('flag'=>1,'ewm'=>$qrcode_url));
}


if($submitok=='add_update' || $submitok=='mod_update'){
	if(!ifint($roleid))json_exit(array('flag'=>0,'msg'=>'请选择【用户组】','focus'=>'roleid'));
	if(str_len($username)<3 || str_len($username)>20 )json_exit(array('flag'=>0,'msg'=>'【登录帐号】长度3~20','focus'=>'username'));
	if(empty($truename))json_exit(array('flag'=>0,'msg'=>'请输入【姓名】','focus'=>'truename'));
	$username=trimhtml($username);
	$truename=trimhtml($truename);
	$roleid   = intval($roleid);
	$username = dataIO($username,'in',50);
	$truename= dataIO($truename,'in',100);
	$title   = dataIO($title,'in',200);
	$uid     = intval($uid);
	$sex     = intval($sex);
	$claimnumday = abs(intval($claimnumday));
	$aboutus = dataIO($content,'in',5000);
	$ifwebshow = ($ifwebshow==1)?1:0;
	$mob    = dataIO($mob,'in',20);
	$qq     = dataIO($qq,'in',20);
	$weixin = dataIO($weixin,'in',50);
	$email  = dataIO($email,'in',100);
	$address = dataIO($address,'in',150);
	$row = $db->ROW(__TBL_ROLE__,'title',"kind=2 AND id=".$roleid,"num");
	if($row){
		$roletitle = $row[0];
	}else{
		json_exit(array('flag'=>0,'msg'=>'找不到所选用户组'.$roleid));
	}
}


switch ($submitok){
	case "add_update":
		if(str_len($password)<6 || str_len($password)>20 )json_exit(array('flag'=>0,'msg'=>'请输入【登录密码】长度6~20','focus'=>'password'));
		$row = $db->ROW(__TBL_ADMIN__,'username',"username='$username' AND username<>''");//kind='adm' AND 
		if($row){
			json_exit(array('flag'=>0,'msg'=>'“用户名【'.$username.'】”已被占用'));
		}
		if(!empty($path_s)){
			adm_pic_reTmpDir_send($path_s,'crm');
			adm_pic_reTmpDir_send(smb($path_s,'b'),'crm');
			$path_s = str_replace('tmp','crm',$path_s);
		}
		if(!empty($ewm)){
			adm_pic_reTmpDir_send($ewm,'crm');
			adm_pic_reTmpDir_send(smb($ewm,'b'),'crm');
			$ewm = str_replace('tmp','crm',$ewm);
		}
		
		
		$kind     = 'adm';
		$password = md5(trim($password));
		$db->query("INSERT INTO ".__TBL_CRM_HN__." (roleid,roletitle,truename,password,username,kind,title,uid,sex,aboutus,ifwebshow,mob,qq,weixin,email,address,path_s,ewm,px,addtime,claimnumday) VALUES ('$roleid','$roletitle','$truename','$password','$username','$kind','$title','$uid','$sex','$aboutus','$ifwebshow','$mob','$qq','$weixin','$email','$address','$path_s','$ewm',".ADDTIME.",".ADDTIME.",'$claimnumday')");
		
		
		AddLog('【基础设置】->增加【管理员'.$username.'】');
		json_exit(array('flag'=>1,'msg'=>'增加成功'));
	break;
	case "mod_update":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$SQL="";
		if ($username != $username_old){
			$row = $db->ROW(__TBL_ADMIN__,'username',"username='$username' AND username<>''");//kind='adm' AND 
			if($row){
				json_exit(array('flag'=>0,'msg'=>'“用户名【'.$username.'】”已被占用'));
			}else{
				$SQL .= ",username='$username'";
			}
		}
		
		$row = $db->ROW(__TBL_ADMIN__,"path_s,ewm","kind='adm' AND id=".$id);
		if (!$row)json_exit(array('flag'=>0,'msg'=>'管理员不存在'.$id));
		$data_path_s = $row[0];
		$data_ewm    = $row[1];
		
		
		if (!empty($password) && str_len($password) <= 20 && str_len($password) >= 6){
			$password = md5(trimm($password));
			$SQL .= ",password='$password'";
			if ($_SESSION["admuid"] == $fid)$_SESSION["admpwd"] = $password;
		}
		
		/******************************************** 主图path_s ********************************************/
		//提交空，数据库有，删老
		if(empty($path_s) && !empty($data_path_s)){
			$B = smb($data_path_s,'b');
			@up_send_admindel($data_path_s.'|'.$B);
		//提交有，数据库无
		}elseif(!empty($path_s) && empty($data_path_s)){
			//上新
			adm_pic_reTmpDir_send($path_s,'crm');
			adm_pic_reTmpDir_send(smb($path_s,'b'),'crm');
			$path_s = str_replace('tmp','crm',$path_s);
		//提交有，数据库有
		}elseif(!empty($path_s) && !empty($data_path_s)){
			//有改动
			if($path_s != $data_path_s){
				//删老
				$B = smb($data_path_s,'b');
				@up_send_admindel($data_path_s.'|'.$B);
				//上新
				adm_pic_reTmpDir_send($path_s,'crm');
				adm_pic_reTmpDir_send(smb($path_s,'b'),'crm');
				$path_s = str_replace('tmp','crm',$path_s);
			}
		}
		/******************************************** ewm ********************************************/
		//提交空，数据库有，删老
		if(empty($ewm) && !empty($data_ewm)){
			$B = smb($data_ewm,'b');
			@up_send_admindel($data_ewm.'|'.$B);
		//提交有，数据库无
		}elseif(!empty($ewm) && empty($data_ewm)){
			//上新
			adm_pic_reTmpDir_send($ewm,'crm');
			adm_pic_reTmpDir_send(smb($ewm,'b'),'crm');
			$ewm = str_replace('tmp','crm',$ewm);
		//提交有，数据库有
		}elseif(!empty($ewm) && !empty($data_ewm)){
			//有改动
			if($ewm != $data_ewm){
				//删老
				$B = smb($data_ewm,'b');
				@up_send_admindel($data_ewm.'|'.$B);
				//上新
				adm_pic_reTmpDir_send($ewm,'crm');
				adm_pic_reTmpDir_send(smb($ewm,'b'),'crm');
				$ewm = str_replace('tmp','crm',$ewm);
			}
		}
		//crmkind='$crmkind',
		$db->query("UPDATE ".__TBL_ADMIN__." SET claimnumday='$claimnumday',roleid='$roleid',roletitle='$roletitle',truename='$truename',title='$title',uid='$uid',sex='$sex',aboutus='$aboutus',ifwebshow='$ifwebshow',mob='$mob',qq='$qq',weixin='$weixin',email='$email',address='$address',path_s='$path_s',ewm='$ewm' ".$SQL." WHERE id=".$id);
		
		if ($username != $username_old){
			$db->query("UPDATE ".__TBL_QIANXIAN__." SET admname='$truename' WHERE admid=".$id);
		}
		if ($truename != $truename_old){
			$db->query("UPDATE ".__TBL_USER__." SET admname='$truename' WHERE admid=".$id);
			$db->query("UPDATE ".__TBL_USER__." SET hnname='$truename' WHERE hnid=".$id);
			$db->query("UPDATE ".__TBL_USER__." SET hnname2='$truename' WHERE hnid2=".$id);
			$db->query("UPDATE ".__TBL_CRM_MATCH__." SET admname='$truename' WHERE admid=".$id);
			$db->query("UPDATE ".__TBL_CRM_BBS__." SET admname='$truename' WHERE admid=".$id);
			$db->query("UPDATE ".__TBL_CRM_FAV__." SET admname='$truename' WHERE admid=".$id);
			$db->query("UPDATE ".__TBL_CRM_FAV__." SET admname='$truename' WHERE admid=".$id);
			$db->query("UPDATE ".__TBL_CRM_CLAIM_LIST__." SET admname='$truename' WHERE admid=".$fid);
		}
		
		AddLog('【基础设置】->修改【管理员用户'.$username.'】');
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case "ajax_delupdate":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$rt = $db->query("SELECT path_s,ewm FROM ".__TBL_ADMIN__." WHERE kind='adm' AND id=".$id);
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
			for($i=1;$i<=$total;$i++) {
				$row = $db->fetch_array($rt,'name');
				if(!$row) break;
				$path_s = $row['path_s'];
				$ewm    = $row['ewm'];
				if(!empty($path_s)){
					$B = smb($path_s,'b');@up_send_admindel($path_s.'|'.$B);
				}
				if(!empty($ewm)){
					$B = smb($ewm,'b');@up_send_admindel($ewm.'|'.$B);
				}
			}
			$db->query("DELETE FROM ".__TBL_ADMIN__." WHERE kind='adm' AND id=".$id);
			$db->query("UPDATE ".__TBL_USER__." SET admid=0,admname='',admtime=0 WHERE admid=".$id);
			$db->query("UPDATE ".__TBL_USER__." SET hnid=0,hnname='',hntime=0 WHERE hnid=".$id);
			$db->query("UPDATE ".__TBL_USER__." SET hnid2=0,hnname2='',hntime2=0 WHERE hnid2=".$id);
			AddLog('【基础设置】->【管理员用户】删除');
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
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
	case"ding":
		if (!ifint($id))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_ADMIN__." SET px=".ADDTIME." WHERE id=".$id);
		header("Location: ".SELF);
	break;
	case -1:
		if ( !ifint($classid))alert_adm("forbidden","-1");
		if ( $classid==1)alert_adm("不能操作当前帐号","-1");
		$db->query("UPDATE ".__TBL_ADMIN__." SET flag=-1 WHERE id=$classid");
		header("Location: ".SELF."?p=".$p);
	break;
	case 1:
		if ( !ifint($classid))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_ADMIN__." SET flag=1 WHERE id=$classid");
		header("Location: ".SELF."?p=".$p);
	break;
	case"mod":
		if (!ifint($id))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_ADMIN__." WHERE id=".$id);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$path_s    = $row['path_s'];
			$ewm       = $row['ewm'];
			$roleid    = $row['roleid'];
			$agentid    = $row['agentid'];
			$agenttitle = dataIO($row['agenttitle'],'out');
			$password  = $row['password'];
			$data_subscribe  = $row['subscribe'];
			$data_openid  = $row['openid'];
			$roletitle = dataIO($row['roletitle'],'out');
			$truename  = dataIO($row['truename'],'out');
			$username  = dataIO($row['username'],'out');
			$title     = dataIO($row['title'],'out');
			$uid       = intval($row['uid']);
			$sex       = intval($row['sex']);
			$ifwebshow_ = intval($row['ifwebshow']);
			$claimnumday = intval($row['claimnumday']);
			$content   = dataIO($row['aboutus'],'out');
			$mob       = dataIO($row['mob'],'out');
			$qq        = dataIO($row['qq'],'out');
			$weixin    = dataIO($row['weixin'],'out');
			$email     = dataIO($row['email'],'out');
			$address   = dataIO($row['address'],'out');
		}else{
			alert_adm("该用户不存在！","-1");
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
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>

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
<style>
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
.tips{font-size:12px}
/*关注*/
.my-subscribe_box{padding:15px 15px 30px 15px;background-color:#fff;border-radius:12px;display:none}
.my-subscribe_box img{width:240px;height:240px;padding:2px;border:#eee 1px solid;display:block;margin:20px auto}
.my-subscribe_box h3{display:inline-block;line-height:20px;font-size:14px;color:#999;margin-top:0px}
</style>
<body>
<div class="navbox">
	<a href="adminuser.php" class="ed">管理员用户<?php echo '<b>'.$db->COUNT(__TBL_ADMIN__,"kind='adm'").'</b>';?></a>
</div>
<div class="fixedblank"></div>

<!--ADD-->
<?php 
/************************************** 【发布】【修改】 add **************************************/
if ($submitok == 'add' || $submitok == 'mod'){?>
<form id="Www_zeai_cn_form" name="ZEAIFORM" method="post" >
	 <table class="table W90_  Mtop20" style="margin:0 0 100px 20px;min-width:1111px">
	<tr>
	<th colspan="4" align="left" style="border:0">基本信息</th>
	</tr>
	<tr>
	<td class="tdL"><font class="Cf00">*</font>所属角色组</td>
	<td class="tdR">
        
    <select name="roleid" id="roleid" class="W200 size2" required>
    <?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_ROLE__." WHERE kind=2 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('请先创建用户组','role.php');
    } else {
    ?>
    <option value="">选择用户组</option>
    <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2);
            if(!$rows2) break;
            $clss=($roleid==$rows2[0])?' selected':'';
            echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
        }
    }
    ?></select>    
    </td>
	<td class="tdL B">是否网站展示</td>
	<td class="tdR">
        <input type="checkbox" name="ifwebshow" id="ifwebshow" class="switch" value="1"<?php echo ($ifwebshow_ == 1)?' checked':'';?>><label for="ifwebshow" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
        <span class="tips">　开启后，前台将展示，会员可以自主选择认领</span>
    </td>
	</tr>
	<tr>
	<td class="tdL"><font class="Cf00">*</font>后台登录帐号</td>
	<td class="tdR"><input id="username" name="username" type="text" class="input W200 size2" maxlength="20" value="<?php echo $username;?>"><span class="tips">3~20位英文母或与数字组合</span></td>
	<td class="tdL"><font class="Cf00">*</font>后台登录密码</td>
	<td class="tdR"><input name="password" type="text" required class="input size2 W200" id="password" maxlength="20" /><span class="tips">长度6~20</span></td>
	</tr>
    <tr><td class="tdL"><font class="Cf00">*</font>每天认领人数</td><td class="tdR S12"><input id="claimnumday" name="claimnumday" type="text" class="input W100 size2" maxlength="6" value="<?php echo $claimnumday;?>"><span class="tips">红娘每天从【公海用户】中认领总人数；填0将不能认领</span></td>
      <td class="tdL">绑定关注公众号</td>
      <td class="tdR">
		<?php if($submitok=='mod'){?>
            <?php if (!empty($data_openid)){?>
                <button type="button" onClick="zeaiBindWeixin(<?php echo $id;?>,'gzh_cancel');" class="btn size2 HUI">解除绑定</button>
                <?php echo '　<font class="S12">OPENID：'.$data_openid.'</font>';if($data_subscribe==2)echo'<font class="C00f S12">（已取消关注）</font>';
            }else{ ?>
                <button type="button" onClick="zeaiBindWeixin(<?php echo $id;?>,'gzh_bind');" class="btn size2">立即绑定</button>
            <?php }?>
            <div id="subscribe_box_my_set" class="my-subscribe_box"><img id="Z_e___A___I__c___N">
            <h3>请用微信扫码关注公众号进行帐号绑定<br>绑定后就可以接收消息通知哦</h3>
            </div>
        <?php }else{echo '<span class="tips">请新增成功后再进行绑定</span>';} ?>
      </td>
    </tr>

    <tr>
    <td class="tdL"><font class="Cf00">*</font>姓名</td>
    <td class="tdR"><input name="truename" type="text"  class="input W100 size2" id="truename" value="<?php echo $truename;?>" maxlength="20" /><span class="tips">如：郭余林或刘老师等</span></td>
    <td class="tdL">性　别</td>
    <td class="tdR">
    <input type="radio" name="sex" id="sex1" class="radioskin" value="1" <?php echo ($sex == 1)?' checked':'';?>><label for="sex1" class="radioskin-label"><i class="i1"></i><b class="W50 S14">男</b></label>
    <input type="radio" name="sex" id="sex2" class="radioskin" value="2" <?php echo ($sex == 2)?' checked':'';?>><label for="sex2" class="radioskin-label"><i class="i1"></i><b class="W50 S14">女</b></label>
    </td>
    </tr>
    
    
    <tr>
    <th height="40" colspan="4" align="left" style="border:0" >【是否网站展示】关闭状态，以下无需设置</th>
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
        <br><br><span class="tips">无头像前台网站展示将不显示</span>
	</td>
    <td class="tdL"><font class="Cf00">*</font>本人微信二维码</td>
    <td class="tdR">
        <div class="picli" id="ewm_path">
        	<li class="add" id="ewm_add"></li>
			<?php if(!empty($ewm)){
				echo '<li><img src="'.$_ZEAI['up2'].'/'.$ewm.'"><i></i></li>';
			}?>
        </div>
      <br><br><span class="tips">认领会员个人主页或红娘主页中将展示</span></td>
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

    <tr><td class="tdL">红娘详细介绍</td><td colspan="3" class="tdR"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">如果从公众号编辑器或外部网页或Word里拷入内容请先过虑垃圾代码，请点击下方 <img src="images/cclear.png" class="picmiddle"> 图标，然后插入文字内容</font><textarea name="content" id="content" class="textarea_k" style="width:100%;height:300px" ><?php echo $content;?></textarea></td></tr>

	</table>
    
    <input name="path_s" id="path_s" type="hidden" value="" />
    <input name="ewm" id="ewm" type="hidden" value="" />
    <input name="pathlist" id="pathlist" type="hidden" value="" />
    <input name="p" type="hidden" value="<?php echo $p;?>" />

  <?php if ($submitok == 'mod'){?>
      <input name="submitok" type="hidden" value="mod_update" />
      <input name="id" type="hidden" value="<?php echo $id;?>" />
      <input name="username_old" type="hidden" value="<?php echo $username;?>" />
      <input name="truename_old" type="hidden" value="<?php echo $truename;?>" />
    <?php }else{ ?>
      <input name="submitok" type="hidden" value="add_update" />
    <?php }?>        
    <br><br><br><br><div class="savebtnbox"><button type="button" id="submit_add" class="btn size3 HUANG3">确认并保存</button></div>
    </form>
    
    
<?php }else{?>    
    
<!--LIST-->
	<?php
	$rt = $db->query("SELECT * FROM ".__TBL_ADMIN__." WHERE kind='adm' ORDER BY px DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		if ($submitok !== "add")echo "<div class='nodataico'><i></i>暂无用户<br><a class='btn size2 HUANG' onClick=\"zeai.iframe('新增用户','".SELF."?submitok=add',500,300)\">新增用户</a></div>";
	} else {
		$page_skin = 2;$pagesize=20;require_once ZEAI.'sub/page.php';?>
        <table class="table0 W98_ Mbottom10 Mtop10">
        <tr>
        <td width="150" align="left"><button type="button" class="btn tips" onClick="zeai.openurl('<?php echo SELF;?>?submitok=add')" ><i class="ico addico">&#xe620;</i>新增用户/红娘</button></td>
        <td align="left">&nbsp;</td>
        </tr>
        </table>
<table class="tablelist" style="width:98%">
	<tr>
        <th width="60" align="center">ID</th>
        <th width="60" align="center">置顶</th>
        <th width="80" align="center">头像</th>
        <th width="80" align="center">姓名</th>
        <th width="100" align="center">后台登录帐号</th>
        <th width="110" align="center"><span class="list_title">角色组</span></th>
        <th width="60" align="center">前台显示</th>
        <th width="70" align="center" title="录入或认领">名下会员</th>
        <th width="110" align="center">每天公海认领(人)</th>
      <th width="70" align="center"><span class="list_title">创建时间</span></th>
        <th width="5">&nbsp;</th>
        <th width="70" align="center">最后登录</th>
        <th align="center">&nbsp;</th>
        <th width="66" align="center">名片二维码</th>
        <th width="60" align="center">帐号状态</th>
        <th width="50" class="center">修改</th>
        <th width="50" class="center">删除</th>
	</tr>
	<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt);
		if(!$rows) break;
		$id = $rows['id'];
		$username = trimhtml(dataIO($rows['username'],'out'));
		$username = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$username);
		$truename = trimhtml(dataIO($rows['truename'],'out'));
		$unum = $db->COUNT(__TBL_USER__,"admid=".$id);
		$ifwebshow = intval($rows['ifwebshow']);
		$claimnumday = intval($rows['claimnumday']);
		$ifwebshow_str = ($ifwebshow==1)?"<i class='ico S18' style='color:#45C01A'>&#xe60d;</i>":'<span class=" C999">隐藏</span>';
		$path_s = $rows['path_s'];
		if(!empty($path_s)){
			$path_s_url = $_ZEAI['up2'].'/'.$path_s;
			$path_s_str = '<img src="'.$path_s_url.'">';
		}else{
			$path_s_url = '';
			$path_s_str = '';
		}
	?>
	<tr>
	<td width="60" height="40" align="center"><?php echo $id;?></td>
    <td width="60" height="40" align="center"><a href="<?php echo "adminuser.php?id=".$id; ?>&submitok=ding"><img src="images/ding.gif" border="0" title="置顶" /></a></td>
	<td width="80" align="center" style="padding:10px 0">
		<?php if (empty($path_s_url)){?>
        <a href="javascript:;" class="pic60 ">无图</a>
        <?php }else{ ?>
        <a href="javascript:;" class="pic60 " onClick="parent.piczoom('<?php echo smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a>
        <?php }?>
    </td>
	<td width="80" align="center" class="S14"><a href='<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>'><?php echo $truename;?></a></td>
	<td width="100" align="center"><a href='<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>'><?php echo $username;?></a></td>
	<td width="110" align="center">【<?php echo $rows['roletitle']; ?>】</td>
	<td width="60" align="center"><?php echo $ifwebshow_str;?></td>
	<td width="70" align="center" class="S14"><?php echo $unum;?></td>
	<td width="110" align="center" class="S14"><?php echo $claimnumday;?></td>
	<td width="70" align="center" class="C999"><?php echo YmdHis($rows['addtime']);?></td>
	<td width="5">&nbsp;</td>
	<td width="70" align="center" class="C999"><?php echo YmdHis($rows['endtime']);?></td>
	<td align="center"></td>
	<td width="66" align="center"><a href="javascript:parent.zeai.iframe('【<?php echo trimhtml($truename);?>】二维码','hn_ewm.php?id=<?php echo $id;?>',550,550);" title="放大二维码" class="zoom"><img src="images/ewm.gif" class="ewmpic"></a></td>
	<td width="60" align="center"><?php
		switch ($rows['flag']) {
			case 1:
				$flagtips = "点击锁定此用户(不能登录)";
				$flagecho = "<a href=".SELF."?submitok=-1&classid=".$rows['id']."&p=".$p." title='".$flagtips."' class='aLV'>正常</a>";
			break;
			case 0:	
				$flagtips = "点击通过审核";
				$flagecho =  "<a href=".SELF."?submitok=1&classid=".$rows['id']."&p=".$p." title='".$flagtips."' class='aHUANG'>未审</a>";
			break;
			case -1:
				$flagtips = "点击解除锁定";
				$flagecho =  "<a href=".SELF."?submitok=1&classid=".$rows['id']."&p=".$p." title='".$flagtips."' class='aLAN'>已锁定</a>";
			break;
		}
		echo $flagecho; ?>
	  </td>
	<td width="50" class="center"><a href='<?php echo SELF;?>?submitok=mod&id=<?php echo $id;?>' class="editico"></a></td>
	<td width="50" class="center"><?php if ($id != 1){?><a value="<?php echo $id; ?>"  class="delico"></a><?php }?></td>
	</tr>
	<?php } ?>
    
	<?php if ($total > $pagesize){?>
	<tfoot><tr>
	<td colspan="17" class="Pleft10"><?php echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></td>
	</tr></tfoot>
	<?php } ?>
</table>
	<?php } ?>
<?php }?> 


<script>
<?php if ($submitok == "add" || $submitok == "mod") {?>
/*	save.onclick = function(){
		zeai.ajax({url:'adminuser'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
			if (rs.flag == 1){
				zeai.msg(rs.msg);
				setTimeout(function(){zeai.openurl('adminuser.php');},1000);
			}else if(rs.flag == 0){
				zeai.msg(rs.msg,o(rs.focus));
			}else{
				zeai.msg(rs.msg);
			}		
		});
	}
*/
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
	var gyl;
	function zeaiBindWeixin(admid,type){
		if(type=='gzh_bind'){
			zeai.ajax({url:'adminuser'+zeai.ajxext+'submitok=ajax_binding_gzh&admid='+admid},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==0){
					clearInterval(gyl);
					zeai.ajax({url:'adminuser'+zeai.ajxext+'submitok=ajax_get_ewm&admid='+admid},function(e){rs=zeai.jsoneval(e);
						if (rs.flag==1){
							supdes=zeai.div({obj:o('subscribe_box_my_set'),title:'关注公众号',w:400,h:450});
							Z_e___A___I__c___N.src=rs.ewm;
							gyl= setInterval(function(){chk_binding_gzh(admid);},3000);
						}
					});
				}
			});
		}else if(type=='gzh_cancel'){
			zeai.confirm('确认解除绑定么？',function(){
				zeai.ajax({url:'adminuser'+zeai.ajxext+'submitok=ajax_binding_gzh_cancel&admid='+admid},function(e){rs=zeai.jsoneval(e);
					zeai.msg(rs.msg);
					if(rs.flag==1){setTimeout(function(){location.reload(true);},1500);}
				});
			});
		}
		function chk_binding_gzh(admid){
			zeai.ajax({url:'adminuser'+zeai.ajxext+'submitok=ajax_binding_gzh&admid='+admid},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){zeai.msg(0);zeai.msg(rs.msg);setTimeout(function(){location.reload(true);},1500);}
			});
		}
	}
	<?php }?>
		zeai.photoUp({
			btnobj:path_add,
			upMaxMB:upMaxMB,
			url:"adminuser"+zeai.extname,
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
			url:"adminuser"+zeai.extname,
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
				zeai.ajax({url:'adminuser'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);
					if(rs.flag==1){
						zeai.msg(rs.msg,{time:1});
						setTimeout(function(){zeai.openurl('adminuser'+zeai.ajxext+'&p=<?php echo $p;?>');},1000);
					}else{
						zeai.msg(rs.msg,{time:1,focus:o(rs.focus)});
					}
				});
			});
		}
	
	
	
<?php }else{ ?>
	zeai.listEach('.delico',function(obj){
		obj.onclick = function(){
			var id = parseInt(obj.getAttribute("value"));
			zeai.confirm('<b class="S18">★请慎重★　确定真的要删除么？</b><br>删除后将清空所有名下用户红娘归属为空无法恢复',function(){
				zeai.ajax('adminuser'+zeai.ajxext+'submitok=ajax_delupdate&id='+id,function(e){var rs=zeai.jsoneval(e);
					if (rs.flag == 1){zeai.msg(rs.msg);setTimeout(function(){location.reload(true);},1000);}else{zeai.msg(rs.msg);}
				});
			});
		}
	});
	
<?php } ?>
</script>
<?php require_once 'bottomadm.php';?>
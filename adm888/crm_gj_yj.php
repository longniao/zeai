<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_crm.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
$meet_flagARR = json_decode($_CRM['meet_flag'],true);
$rz_dataARR = explode(',',$_ZEAI['rz_data']);
$t = (ifint($t,'1-2','1'))?$t:1;
/********************************************************************* 跟进 *********************************************************************/
$bbs_intentionARR = json_decode($_CRM['bbs_intention'],true);
if ($submitok == 'gj_add' || $submitok == 'gj_add_update'){
	if(!in_array('crm_bbs_add',$QXARR))exit(noauth('暂无【客户跟进(增加)】权限'));
}
if ($submitok == 'gj_mod' || $submitok == 'gj_mod_update'){
	if(!in_array('crm_bbs_mod',$QXARR))exit(noauth('暂无【客户跟进(修改)】权限'));
}
if($submitok == 'gj_del_update'){
	if(!in_array('crm_bbs_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【客户跟进(删除)】权限'));
}

if($submitok=='gj_add_update' || $submitok=='gj_mod_update'){
	if ( !ifint($uid))json_exit(array('flag'=>0,'msg'=>'客户UID不正确'));
	if(str_len($content)<1)json_exit(array('flag'=>0,'msg'=>'请输入【跟进内容】','focus'=>'content'));
	$nexttime   = (!empty($nexttime))?strtotime($nexttime):0;
	if ( !ifint($intention))json_exit(array('flag'=>0,'msg'=>'请选择【售前意向】'));
	$content = dataIO($content,'in',1000);
}
if($submitok == 'gj_add_update'){
	//ifsqsh($uid);
	$row2 = $db->ROW(__TBL_USER__,"agentid,agenttitle","id=".$uid,'num');$agentid= $row2[0];$agenttitle= $row2[1];
	if(!ifint($agentid) || empty($agenttitle)){
		$agentid=intval($session_agentid);
		$agenttitle=$session_agenttitle;
	}
	if(!empty($pathlist)){
		$ARR=explode(',',$pathlist);
		if (count($ARR) >= 1 && is_array($ARR)){
			$pathlist=array();
			foreach ($ARR as $V) {
				adm_pic_reTmpDir_send($V,'crm');
				adm_pic_reTmpDir_send(smb($V,'b'),'crm');
				$path_s = str_replace('tmp','crm',$V);
				$pathlist[]=$path_s;
			}
			$pathlist = implode(',',$pathlist);
		}
	}
	$db->query("INSERT INTO ".__TBL_CRM_BBS__."  (uid,content,addtime,admid,admname,agentid,agenttitle,nexttime,intention,pathlist) VALUES ($uid,'$content',".ADDTIME.",$session_uid,'$session_truename','$agentid','$agenttitle','$nexttime','$intention','$pathlist')");
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	AddLog('【CRM跟进】新增->【'.$nickname.'（uid:'.$uid.'）】，跟进内容：'.$content);
	json_exit(array('flag'=>1,'msg'=>'增加成功'));
}elseif($submitok == 'gj_mod_update'){
	if ( !ifint($id))alert_adm("ID不正确","back");
	$row = $db->ROW(__TBL_CRM_BBS__,"uid,pathlist","id=".$id,"num");
	if ($row){
		$uid= $row[0];$data_pathlist= $row[1];
	}else{
		alert_adm("跟进记录不存在","back");
	}
	
	$row2 = $db->ROW(__TBL_USER__,"agentid,agenttitle","id=".$uid,'num');$agentid= $row2[0];$agenttitle= $row2[1];
	if(!ifint($agentid) || empty($agenttitle)){
		$agentid=intval($session_agentid);
		$agenttitle=$session_agenttitle;
	}
	
	/******************************************** 批量 list ********************************************/
	//提交空，数据库有，删老
	if(empty($pathlist) && !empty($data_pathlist)){
		$ARR=explode(',',$data_pathlist);
		foreach ($ARR as $S){
			$B = smb($S,'b');@up_send_admindel($S.'|'.$B);
		}
	//提交有，数据库无
	}elseif(!empty($pathlist) && empty($data_pathlist)){
		//上新
		$ARR=explode(',',$pathlist);
		$pathlist=array();
		foreach ($ARR as $V) {
			adm_pic_reTmpDir_send($V,'crm');
			adm_pic_reTmpDir_send(smb($V,'b'),'crm');
			$_s         = str_replace('tmp','crm',$V);
			$pathlist[] = $_s;
		}
		$pathlist = implode(',',$pathlist);
	//提交有，数据库有
	}elseif(!empty($pathlist) && !empty($data_pathlist)){
		//有改动
		if($pathlist != $data_pathlist){
			$ARR = explode(',',$pathlist);
			$pathlist = array();
			//循环新列表
			foreach ($ARR as $V) {
				//新上传，上新
				if(strstr($V,'/tmp/')){
					adm_pic_reTmpDir_send($V,'crm');
					adm_pic_reTmpDir_send(smb($V,'b'),'crm');
					$_s = str_replace('tmp','crm',$V);
					$pathlist[]=$_s;
				//老图，直接赋值
				}else{
					$pathlist[]=$V;
				}
			}
			$pathlist = implode(',',$pathlist);
			//循环老库，处理多图被删除的部分
			$ARR2=explode(',',$data_pathlist);
			foreach ($ARR2 as $V2) {
				//不在新列表，删之
				if(!in_array($V2,$ARR)){
					$B = smb($V2,'b');@up_send_admindel($V2.'|'.$B);
				}
			}
		}
	}
	$db->query("UPDATE ".__TBL_CRM_BBS__." SET content='$content',nexttime='$nexttime',intention='$intention',pathlist='$pathlist',agentid='$agentid',agenttitle='$agenttitle' WHERE id=".$id);
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	AddLog('【CRM跟进】修改->【'.$nickname.'（uid:'.$uid.'）】，跟进内容：'.$content);
	//
	json_exit(array('flag'=>1,'msg'=>'修改成功'));
}elseif($submitok == 'gj_del_update'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在'));
	$row = $db->ROW(__TBL_CRM_BBS__,"uid,content,pathlist","id=".$id,"num");
	if ($row){
		$uid= $row[0];$content= $row[1];$data_pathlist= $row[2];
	}else{
		alert_adm("跟进记录不存在","back");
	}
	$uid=$row[0];
	ifsqsh($uid);
	$arr    = explode(',',$data_pathlist);
	if (count($arr) >= 1){
		foreach ($arr as $v){
			$path_s = $v;
			$path_b = smb($path_s,'b');
			@up_send_admindel($path_s.'|'.$path_b);
		}
	}
	$db->query("DELETE FROM ".__TBL_CRM_BBS__." WHERE id=".$id);
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	AddLog('【CRM跟进】删除->【'.$nickname.'（uid:'.$uid.'）】，删除内容：'.$content);
	//
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok == 'ajax_pic_path_s_up'){
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
}
/********************************************************************* 约见 *********************************************************************/
if ($submitok == 'yj_add' || $submitok == 'yj_add_update'){
	if(!in_array('crm_match_add',$QXARR))exit(noauth('暂无【客户约见(增加)】权限'));
}
if ($submitok == 'yj_mod' || $submitok == 'yj_mod_update'){
	if(!in_array('crm_match_mod',$QXARR))exit(noauth('暂无【客户约见(修改)】权限'));
}
if($submitok == 'yj_del_update'){
	if(!in_array('crm_match_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【客户约见(删除)】权限'));
}
if(($submitok=='yj_add_update' || $submitok=='yj_mod_update')  && $submitok!='yj_del_update' ){
	if ( !ifint($uid))json_exit(array('flag'=>0,'msg'=>'客户UID不正确'));
	if ( !ifint($uid2))json_exit(array('flag'=>0,'msg'=>'请输入约见人UID'));
	if(empty($fwfs))json_exit(array('flag'=>0,'msg'=>'请输入【服务方式】','focus'=>'fwfs'));
	//if(str_len($fkqk)<1)json_exit(array('flag'=>0,'msg'=>'请输入【反馈情况】','focus'=>'fkqk'));
	if(!ifdatetime($addtime))json_exit(array('flag'=>0,'msg'=>'请输入【约见时间】','focus'=>'addtime'));
	if(!ifdatetime($nexttime) && !empty($nexttime))json_exit(array('flag'=>0,'msg'=>'请输入【下次约见时间】','focus'=>'addtime'));
	//
	$meet_flag  = intval($meet_flag);
	$meet_flag3 = intval($meet_flag3);
	$nexttime   = (!empty($nexttime))?strtotime($nexttime):0;
	$meet_ifagree  = intval($meet_ifagree);
	$meet_ifagree2 = intval($meet_ifagree2);
	$fwfs    = dataIO($fwfs,'in',200);
	$fkqk    = dataIO($fkqk,'in',200);
	$addtime = strtotime($addtime);
	//
	$row = $db->ROW(__TBL_USER__,"nickname,crm_flag","id=".$uid2,"name");
	if ($row){
		$crm_flag = intval($row['crm_flag']);
		$nickname = dataIO($row['nickname'],'out');
		if($crm_flag==3)json_exit(array('flag'=>0,'msg'=>'此约见对象客户“'.$nickname.'(UID:'.$uid2.')”已服务成功，【服务状态】->已完成，请换其他客户'));
	}else{
		json_exit(array('flag'=>0,'msg'=>'客户不存在'));
	}
}
if($submitok == 'yj_add_update'){
	//$row = $db->ROW(__TBL_CRM_MATCH__,"*","uid=".$uid." AND uid2=".$uid2);
	//if ($row)json_exit(array('flag'=>0,'msg'=>'约见记录已存在'));
	//ifsqsh($uid);
	$agentid   = intval($session_agentid);
	$agenttitle=$session_agenttitle;
	if(!empty($pathlist)){
		$ARR=explode(',',$pathlist);
		if (count($ARR) >= 1 && is_array($ARR)){
			$pathlist=array();
			foreach ($ARR as $V) {
				adm_pic_reTmpDir_send($V,'crm');
				adm_pic_reTmpDir_send(smb($V,'b'),'crm');
				$path_s = str_replace('tmp','crm',$V);
				$pathlist[]=$path_s;
			}
			$pathlist = implode(',',$pathlist);
		}
	}
	$db->query("INSERT INTO ".__TBL_CRM_MATCH__."  (uid,uid2,fwfs,fkqk,addtime,admid,admname,agentid,agenttitle,nexttime,meet_ifagree,meet_ifagree2,meet_flag,meet_flag3,px,pathlist) VALUES ($uid,$uid2,'$fwfs','$fkqk',$addtime,$session_uid,'$session_truename','$agentid','$agenttitle','$nexttime','$meet_ifagree','$meet_ifagree2','$meet_flag','$meet_flag3',".ADDTIME.",'$pathlist')");
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid2,'num');$nickname2= $row2[0];
	AddLog('【CRM约见】新增->【'.$nickname.'（uid:'.$uid.'）】->【'.$nickname2.'（uid:'.$uid2.'）】');
	json_exit(array('flag'=>1,'msg'=>'增加成功'));
}elseif($submitok == 'yj_mod_update'){
	if ( !ifint($id))alert_adm("约见ID不正确","back");
	$row = $db->ROW(__TBL_CRM_MATCH__,"uid,pathlist","id=".$id,"num");
	if ($row){
		$uid= $row[0];$data_pathlist= $row[1];
	}else{
		alert_adm("约见记录不存在","back");
	}
	$row2 = $db->ROW(__TBL_USER__,"agentid,agenttitle","id=".$uid,'num');$agentid= $row2[0];$agenttitle= $row2[1];
	if(!ifint($agentid) || empty($agenttitle)){
		$agentid=intval($session_agentid);
		$agenttitle=$session_agenttitle;
	}
	/******************************************** 批量 list ********************************************/
	//提交空，数据库有，删老
	if(empty($pathlist) && !empty($data_pathlist)){
		$ARR=explode(',',$data_pathlist);
		foreach ($ARR as $S){
			$B = smb($S,'b');@up_send_admindel($S.'|'.$B);
		}
	//提交有，数据库无
	}elseif(!empty($pathlist) && empty($data_pathlist)){
		//上新
		$ARR=explode(',',$pathlist);
		$pathlist=array();
		foreach ($ARR as $V) {
			adm_pic_reTmpDir_send($V,'crm');
			adm_pic_reTmpDir_send(smb($V,'b'),'crm');
			$_s         = str_replace('tmp','crm',$V);
			$pathlist[] = $_s;
		}
		$pathlist = implode(',',$pathlist);
	//提交有，数据库有
	}elseif(!empty($pathlist) && !empty($data_pathlist)){
		//有改动
		if($pathlist != $data_pathlist){
			$ARR = explode(',',$pathlist);
			$pathlist = array();
			//循环新列表
			foreach ($ARR as $V) {
				//新上传，上新
				if(strstr($V,'/tmp/')){
					adm_pic_reTmpDir_send($V,'crm');
					adm_pic_reTmpDir_send(smb($V,'b'),'crm');
					$_s = str_replace('tmp','crm',$V);
					$pathlist[]=$_s;
				//老图，直接赋值
				}else{
					$pathlist[]=$V;
				}
			}
			$pathlist = implode(',',$pathlist);
			//循环老库，处理多图被删除的部分
			$ARR2=explode(',',$data_pathlist);
			foreach ($ARR2 as $V2) {
				//不在新列表，删之
				if(!in_array($V2,$ARR)){
					$B = smb($V2,'b');@up_send_admindel($V2.'|'.$B);
				}
			}
		}
	}
	$db->query("UPDATE ".__TBL_CRM_MATCH__." SET uid2=".$uid2.",fwfs='$fwfs',fkqk='$fkqk',addtime='$addtime',nexttime='$nexttime',meet_ifagree='$meet_ifagree',meet_ifagree2='$meet_ifagree2',meet_flag='$meet_flag',meet_flag3='$meet_flag3',px=".ADDTIME.",pathlist='$pathlist',agentid='$agentid',agenttitle='$agenttitle' WHERE id=".$id);
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid2,'num');$nickname2= $row2[0];
	AddLog('【CRM约见】修改->【'.$nickname.'（uid:'.$uid.'）】->【'.$nickname2.'（uid:'.$uid2.'）】');
	json_exit(array('flag'=>1,'msg'=>'修改成功'));
}elseif($submitok == 'yj_del_update'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在'));
	$row = $db->ROW(__TBL_CRM_MATCH__,"uid,uid2,pathlist","id=".$id,"num");
	if (!$row)json_exit(array('flag'=>0,'msg'=>'记录不存在'));
	$uid=$row[0];$uid2=$row[1];$data_pathlist= $row[2];
	ifsqsh($uid);
	$arr    = explode(',',$data_pathlist);
	if (count($arr) >= 1){
		foreach ($arr as $v){
			$path_s = $v;
			$path_b = smb($path_s,'b');
			@up_send_admindel($path_s.'|'.$path_b);
		}
	}
	$db->query("DELETE FROM ".__TBL_CRM_MATCH__." WHERE id=".$id);
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid2,'num');$nickname2= $row2[0];
	AddLog('【CRM约见】删除->【'.$nickname.'（uid:'.$uid.'）】->【'.$nickname2.'（uid:'.$uid2.'）】');
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok == 'ajax_getuinfo'){
	$uid2=trimm($uid2);
	if ( !ifint($uid2))json_exit(array('flag'=>0,'msg'=>'请输入约见人UID'));
	$rowp = $db->ROW(__TBL_USER__,"sex,grade,truename,nickname,photo_s","id=".$uid2." AND crm_flag<3 ");
	if ($rowp){
		$sex   = $rowp[0];
		$grade = $rowp[1];
		$nickname = dataIO($rowp[2],'out');
		$truename = dataIO($rowp[3],'out');
		$photo_s2  = $rowp[4];
		$C = '&nbsp;'.uicon($sex.$grade).' '.$nickname.' '.$truename;
		$photo_s_url2 = (!empty($photo_s2))?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_s'.$sex.'.png';
		exit(json_encode(array('flag'=>1,'photo_s_url2'=>$photo_s_url2,'C'=>$C)));
	}else{exit(json_encode(array('flag'=>0,'msg'=>'此客户不存在或已服务成功','uid2'=>$uid2)));}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
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
.rzboxx li{width:100px;float:left;text-align:center}
</style>
<body>
<?php 
if($submitok == 'gj_list'){
	if(!in_array('crm_bbs_view',$QXARR))echo "<div class='nodataico'><i></i>暂无【客户跟进(查看)】权限</div>";?>
	
	<!--跟进列表-->
    <?php
	$rt = $db->query("SELECT * FROM ".__TBL_CRM_BBS__." WHERE uid=".$uid." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<br><br><div class='nodataicoS'><i></i>暂无跟进";
		echo "<br><br><a href='crm_gj_yj.php?submitok=gj_add&uid=".$uid."' class='aQINGed'>新增</a>";
		echo "</div><br><br>";
	} else {
	?>
	<style>
	.table_gj{width:95%;margin:10px auto 20px auto;border-collapse:collapse}
	.table_gj tr:hover{background-color:#F9F9FA}
	.table_gj td{font-size:12px;color:#666;border-bottom:#eee 1px solid}
	.table_gj td em{font-size:14px;line-height:200%;color:#000;margin:5px 0 10px 0}
    .timebox{width:60px;height:60px;padding:8px 0 0 0;background-color:#c9ede8;border-radius:2px;text-align:center;color:#009688;position:relative;font-size:12px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;font-family:Arial, Helvetica, sans-serif}
    .timebox b{font-size:22px;margin-right:2px;}
    .timebox font{display:inline-block}
    .timebox .zj{width:0;height:0;left:60px;top:20px;position:absolute;border-top:10px solid transparent;border-bottom:10px solid transparent;border-left:10px solid #c9ede8}
	.pathlist img{margin:0 5px 2px 2px;width:50px;height:50px;object-fit:cover;-webkit-object-fit:cover;border-radius:2px}
	</style>
    <table class="table_gj">
    <?php
    for($i=1;$i<=$total;$i++) {
        $rows = $db->fetch_array($rt,'name');
        if(!$rows) break;
        $id      = $rows['id'];
        $uid     = $rows['uid'];
        $addtime = '<b>'.YmdHis($rows['addtime'],'d').'</b>日<br><font>'.YmdHis($rows['addtime'],'Y').'/'.YmdHis($rows['addtime'],'m').'</font>';
        $content = '<em>'.dataIO($rows['content'],'out').'</em>';
        $admid   = $rows['admid'];
        $admname = dataIO($rows['admname'],'out');
        $nexttime   = intval($rows['nexttime']);
        $intention  = intval($rows['intention']);
        $intention_str = '　　售前意向：'.crm_arr_title($bbs_intentionARR,$intention);
        $nexttime_str  = ($nexttime>0)?'　　下次联系：'.YmdHis($nexttime,'YmdHi'):'';
        $pathlist      = $rows['pathlist'];
		
		$crm_ugrade=0;
		$row = $db->ROW(__TBL_USER__,"crm_ugrade","id=".$uid,"name");
		if ($row)$crm_ugrade = intval($row['crm_ugrade']);
		if ($crm_ugrade<1){
			$intention_str = '　　售前意向：'.crm_arr_title($bbs_intentionARR,$intention);
		}else{
			$intention_str = '';
		}
		?>
		<tr>
		<td width="80" align="left" valign="top" style="padding:10px 0">
		<div class="timebox"><?php echo $addtime;?><div class="zj"></div></div>
		</td>
		<td width="10" align="left"></td>
		<td align="left" style="padding:10px 0;word-break:break-all;word-wrap:break-word">
		<?php
			echo '跟进人：'.$admname.'(ID:'.$admid.')'.$intention_str.$nexttime_str;
			echo $content;
            if(!empty($pathlist)){
                $ARR=explode(',',$pathlist);
                $pathlist=array();
				echo '<div class="pathlist">';
                foreach ($ARR as $V) {?>
					<a href="javascript:;" class="zoom" onClick="parent.parent.piczoom('<?php echo smb($_ZEAI['up2'].'/'.$V,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$V;?>"></a>
                    <?php
                }
				echo '</div>';
            }
            ?>        
        </td>
		<td width="10" align="left">&nbsp;</td>
		<td width="20" align="left" ><a href="crm_gj_yj.php?submitok=gj_mod&id=<?php echo $id;?>&uid=<?php echo $uid;?>" title="修改" class="editico"></a></td>
		</tr>
	<?php }?>
    </table>
    <?php }?>
    
<!--跟进-新增-->
<?php }elseif ($submitok =='gj_add' || $submitok =='gj_mod'){
	if($submitok == 'gj_mod'){
		if ( !ifint($id))alert_adm("跟进ID不正确","back");
		if ( !ifint($uid))alert_adm("客户UID不正确","back");
		$row = $db->ROW(__TBL_CRM_BBS__,"*","id=".$id,"name");
		if ($row){
			$content   = dataIO($row['content'],'out');
			$intention = $row['intention'];
			$uid       = $row['uid'];
			$nexttime  = $row['nexttime'];
			$nexttime = ($nexttime>0)?YmdHis($nexttime):'';
			$pathlist = $row['pathlist'];
		}
	}else{
		$intention=4;
	}
	$row = $db->ROW(__TBL_USER__,"crm_ugrade","id=".$uid,"name");
	if ($row){
		$crm_ugrade = intval($row['crm_ugrade']);
	}else{
		alert_adm("客户不存在","back");
	}
	?>
	<style>
    .table td{padding:8px;border:1px solid #eee}
	.table .tdL{width:100px;color:#666}
	.RCW li{width:110px}
    </style>
    <form id="Www_zeai_cn_form">
    <table class="table W95_ Mtop20" style="margin:15px 0 0 15px">
    <tr>
      <td class="tdL"><font class="Cf00">*</font>下次联系</td>
      <td class="tdR"><input name="nexttime" id="nexttime" type="text" class="input size2" style="width:162px" maxlength="30" value="<?php echo $nexttime;?>"  autocomplete="off" /></td>
    </tr>
    <?php if ($crm_ugrade<1){?>
    <tr>
        <td class="tdL"><font class="Cf00">*</font>售前意向</td>
        <td class="tdR"><script>zeai_cn__CreateFormItem('radio','intention','<?php echo $intention; ?>','class="size2 RCW"',<?php echo $_CRM['bbs_intention'];?>);</script></td>
    </tr>
	<?php }else{?>
     <input name="intention" id="intention" type="hidden" value="<?php echo $intention;?>" />
    <?php }?>
    <tr>
      <td class="tdL"><font class="Cf00">*</font>跟进内容</td>
      <td class="tdR"><textarea name="content" rows="3" class="textarea W100_" id="content"><?php echo $content;?></textarea>
        <div class="tips2 C8d" style="padding-top:5px">如：【今天电话了对方，说过两天】，【微信确认，客户说明天到我们公司面谈】，【电话回访，这个月出差，下个月回来】等</div>
      </td>
    </tr>
    <tr>
      <td class="tdL">跟进截图<br><font class="S12 C999">支持批量上传</font></td>
      <td class="tdR">
        <div class="picli" id="picli_pathlist">
        	<li class="add" id="pathlist_add"></li>
			<?php
            if(!empty($pathlist)){
                $ARR=explode(',',$pathlist);
                $pathlist=array();
                foreach ($ARR as $V) {
                   echo '<li><img src="'.$_ZEAI['up2'].'/'.$V.'"><i></i></li>';
				}
            }?>      
        </div>
      </td>
    </tr>
    </table>
    
    <input name="uid" type="hidden" value="<?php echo $uid;?>" />
    <input name="pathlist" id="pathlist" type="hidden" value="" />
    <?php if ($submitok == 'gj_mod'){?>
      <input name="submitok" type="hidden" value="gj_mod_update" />
      <input name="id" type="hidden" value="<?php echo $id;?>" />
    <?php }else{ ?>
      <input name="submitok" type="hidden" value="gj_add_update" />
    <?php }?>
    </form>
    <br><br><div class="savebtnbox"><button type="button" id="bbs_submit_add" class="btn size3 HUANG3">保存并提交</button></div>
	<script>
	var up2='<?php echo $_ZEAI['up2'];?>/',upMaxMB=<?php echo $_UP['upMaxMB']; ?>,uid=<?php echo $uid; ?>;
    bbs_submit_add.onclick=function(){
        if (zeai.empty(o('nexttime').value)){
            zeai.msg('请选择【下次联系】时间'+o('nexttime').value,o('nexttime'));	
            return false;
        }
		<?php if ($crm_ugrade<1){?>
		if (!zeai.form.ifradio('intention')){
			zeai.msg('请选择【售前意向】');	
			return false;
		}		
		<?php }?>
        if (zeai.empty(o('content').value)){
            zeai.msg('请输入【跟进内容】'+o('content').value,o('content'));	
            return false;
        }
        zeai.confirm('确定要提交么？',function(){
            zeai.ajax({url:'crm_gj_yj'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
                zeai.msg(0);
                if(rs.flag==1){
                    zeai.msg(rs.msg,{time:3});
                    setTimeout(function(){zeai.openurl('crm_gj_yj'+zeai.ajxext+'submitok=gj_list&uid='+uid);},1000);
                }else{
                    zeai.msg(rs.msg);
                }
            });
        });
    }
	//
	<?php if($submitok=='gj_mod'){?>
	window.onload=function(){end();}
	<?php }?>
	zeai.photoUp({
		btnobj:pathlist_add,
		upMaxMB:upMaxMB,
		url:"crm_gj_yj"+zeai.extname,
		multiple:8,
		submitok:"ajax_pic_path_s_up",
		end:function(rs){end();},
		li:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){picli_pathlist.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');}
		}
	});
	function end(){
		var i=zeai.tag(picli_pathlist,'i'),img=zeai.tag(picli_pathlist,'img');
		if(zeai.empty(i))return;
		for(var k=0;k<img.length;k++) {
			(function(k){
				var src=img[k].src;
				img[k].onclick = function(){parent.piczoom(src.replace('_s.','_b.'));}
			})(k);
		}
		for(var k=0;k<i.length;k++) {
			(function(k){
				i[k].onclick = function(){
					var thiss=this;
					zeai.confirm('亲~~确认删除么？',function(){
						thiss.parentNode.remove();
						pathlistReset();
					});
				}
			})(k);
		}
		function pathlistReset(){
			var img=zeai.tag(picli_pathlist,'img'),pathlist=[],src;
			for(var k=0;k<img.length;k++){
				var src=img[k].src.replace(up2,'');
				pathlist.push(src);
			}
			o('pathlist').value=pathlist.join(",");
		}
		pathlistReset();
	}
    </script>
	<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);laydate.render({elem:'#nexttime',type: 'datetime'});</script>


<!--约见列表-->
<?php }elseif ($submitok =='yj_list' ){

	if(!@in_array('crm_match_view',$QXARR))echo "<div class='nodataico'><i></i>暂无【客户约见(查看)】权限</div>";

	$rt = $db->query("SELECT * FROM ".__TBL_CRM_MATCH__." WHERE uid=".$uid." OR uid2=".$uid." ORDER BY px DESC,id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<br><br><div class='nodataicoS'><i></i>暂无约见";
		echo "<br><br><a class='aQINGed' onclick=\"zeai.openurl('crm_gj_yj.php?submitok=yj_add&uid=".$uid."')\">新增</a>";
		echo "</div><br><br>";
	}else{
	?>
<style>
	.table_yj{width:100%;margin:0 auto 20px auto;border-collapse:collapse}
	.table_yj tr:hover{background-color:#F9F9FA}
	.table_yj td{font-size:12px;color:#666;border-bottom:#eee 1px solid;padding:10px 0}
	.table_yj td em{font-size:14px;line-height:200%;color:#000;margin:5px 0 10px 0}
    .timebox{width:60px;height:60px;padding:8px 0 0 0;background-color:#ffeded;border-radius:2px;text-align:center;color:#EE5A4E;position:relative;font-size:12px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;font-family:Arial, Helvetica, sans-serif}
    .timebox b{font-size:22px;margin-right:2px;}
    .timebox font{display:inline-block}
    .timebox .zj{width:0;height:0;left:60px;top:20px;position:absolute;border-top:10px solid transparent;border-bottom:10px solid transparent;border-left:10px solid #ffeded}
	.pathlist img{margin:8px 5px 5px 2px;width:30px;height:30px;object-fit:cover;-webkit-object-fit:cover;border-radius:2px}	
    </style>
    <table class="table_yj" >
    <tr>
      <td width="10" align="center"></td>
      <td width="80" align="center">时间</td>
      <td width="70" align="center" >约见对象</td>
      <td width="100" align="center">服务方式	</td>
      <td align="center">反馈情况</td>
      <td width="120" align="center" class="C999">红娘/下次联系</td>
      <td width="60" align="center" class="C999">工单状态</td>
      <td width="50" align="center">操作</td>
      <td width="10" align="center">&nbsp;</td>
    </tr>
    <?php
    for($i=1;$i<=$total;$i++) {
        $rows = $db->fetch_array($rt,'name');
        if(!$rows) break;
        $id       = $rows['id'];
        $datauid  = $rows['uid'];
        $datauid2 = $rows['uid2'];

		if($datauid == $uid){
			$rows2 = $db->ROW(__TBL_USER__,"nickname,truename,sex,grade,photo_s","id=".$datauid2,'name');
			$sex      = $rows2['sex'];
			$grade    = $rows2['grade'];
			$photo_s  = $rows2['photo_s'];
			$nickname = dataIO($rows2['nickname'],'out');
			$truename = dataIO($rows2['truename'],'out');
			$uid2 = $datauid2;
		}else{
			$rows2 = $db->ROW(__TBL_USER__,"nickname,truename,sex,grade,photo_s","id=".$datauid,'name');
			$sex      = $rows2['sex'];
			$grade    = $rows2['grade'];
			$photo_s  = $rows2['photo_s'];
			$nickname = dataIO($rows2['nickname'],'out');
			$truename = dataIO($rows2['truename'],'out');
			$uid2 = $datauid;
		}
		
		$fwfs = dataIO($rows['fwfs'],'out');
		$fkqk = dataIO($rows['fkqk'],'out');
		$px   = intval($rows['px']);
        //$addtime = '<b>'.YmdHis($rows['addtime'],'d').'</b>日<br><font>'.YmdHis($rows['addtime'],'Y').'/'.YmdHis($rows['addtime'],'m').'</font>';
        $px = ($px>0)?'<b>'.YmdHis($rows['px'],'d').'</b>日<br><font>'.YmdHis($rows['px'],'Y').'/'.YmdHis($rows['px'],'m').'</font>':'<b>无</b>';
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$href        = Href('crm_u',$uid2);
		$nickname = (!empty($truename))?$truename:$nickname;
		$nickname = '<div class="S12 C999">'.$nickname.'</div>';
		$admid_mh   = $rows['admid'];
		$admname_mh = dataIO($rows['admname'],'out');
		$nexttime   = $rows['nexttime'];
		$meet_flag  = $rows['meet_flag'];
		$meet_flag3 = $rows['meet_flag3'];
		$meet_ifagree  = $rows['meet_ifagree'];
		$meet_ifagree2 = $rows['meet_ifagree2'];
		$pathlist      = $rows['pathlist'];
    ?>
    <tr>
    <td width="10" height="30" align="center">
    	
    </td>
    <td width="80" align="center"><div class="timebox"><?php echo $px;?><div class="zj"></div></div></td>
    <td width="70" align="center" >
    
    <a href="javascript:;" class="yjdetail" uid2="<?php echo $uid2;?>"><img src="<?php echo $photo_s_url; ?>" class="photo_s40"></a>
	<?php echo $uid2;?><?php echo $nickname;?>
    
    </td>
    <td width="100" height="30" align="center" style="word-break:break-all;word-wrap:break-word;"><?php echo $fwfs;?></td>
    <td height="30" align="center" style="word-break:break-all;word-wrap:break-word;">
	<?php echo $fkqk;
	if(!empty($pathlist)){
		$ARR=explode(',',$pathlist);
		$pathlist=array();
		echo '<div class="pathlist">';
		foreach ($ARR as $V) {?>
			<a href="javascript:;" class="zoom" onClick="parent.parent.piczoom('<?php echo smb($_ZEAI['up2'].'/'.$V,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$V;?>"></a>
			<?php
		}
		echo '</div>';
	}
	?>
    </td>
    <td width="120" align="center" class="C999 lineH150">
	<?php
	if(!empty($admname_mh)){echo $admname_mh.'<br><font class="C999">ID:12'.$admid_mh.'</font>';}
	if ($nexttime > 0){
		echo '<div>'.YmdHis($nexttime,'YmdHi').'</div>';
		$nextday = intval($nexttime-ADDTIME);
		$nextday = intval($nextday/86400);
		if($nexttime<ADDTIME){//过期
			$nextday_str = ($nextday<-1)?abs($nextday).'天':'';
			echo '<div class="Cf00">过期'.$nextday_str.'未约见</div>'; 
		}else{
			if($nextday>=1){
				echo '<div class="C090">'.$nextday.'天后约见</div>'; 
			}
		}
	}
	?>
    </td>
    <td width="60" align="center" class="C999"><?php echo crm_arr_title($meet_flagARR,$meet_flag);?></td>
    <td width="50" align="center">
    <?php if ($meet_flag != 3){?><a href="crm_gj_yj.php?submitok=yj_mod&id=<?php echo $id;?>&uid=<?php echo $datauid;?>" title="修改" class="editico"></a><br><br><?php }?>
    <a title="删除" clsid="<?php echo $id; ?>" class="delico yjdel" ></a>
    </td>
    <td width="10" align="center">&nbsp;</td>
    </tr>
    <?php } ?>
    </table>
	<script>
    zeai.listEach('.yjdel',function(obj){
        obj.onclick = function(){
			var id = parseInt(obj.getAttribute("clsid"));
            zeai.confirm('确定删除么？',function(){
                zeai.ajax({url:'crm_gj_yj.php?submitok=yj_del_update&id='+id},function(e){
                    rs=zeai.jsoneval(e);
                    zeai.msg(0);zeai.msg(rs.msg);
                    if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
                });
            });
        }
    });
    </script>
    <br>
	<?php
	}
	?>






<!--约见新增修改-->
<?php }elseif ($submitok =='yj_add' || $submitok =='yj_mod'){
		if($submitok == 'yj_mod'){
			if ( !ifint($id))alert_adm("约见ID不正确","back");
			$row = $db->ROW(__TBL_CRM_MATCH__,"*","id=".$id);
			if ($row){
				$uid  = $row['uid'];
				$uid2 = $row['uid2'];
				$fwfs = dataIO($row['fwfs'],'out');
				$fkqk = dataIO($row['fkqk'],'out');
				$addtime    = YmdHis($row['addtime']);
				$meet_flag  = $row['meet_flag'];
				$meet_flag3 = $row['meet_flag3'];
				$nexttime   = $row['nexttime'];
				$nexttime   = ($nexttime>0)?YmdHis($nexttime):'';
				$meet_ifagree  = $row['meet_ifagree'];
				$meet_ifagree2 = $row['meet_ifagree2'];
				$row2 = $db->ROW(__TBL_USER__,"sex,grade,photo_s,nickname","id=".$uid2,'num');$sex2= $row2[0];$grade2= $row2[1];$photo_s2= $row2[2];$nickname2= dataIO($row2[3],'out');
				$photo_s_url2 = (!empty($photo_s2))?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_s'.$sex2.'.png';
				$pathlist = $row['pathlist'];
			}else{
				alert_adm("约见ID不正确","back");
			}
		}else{
			$photo_s_url2 = HOST.'/res/photo_s.png';
			if(ifint($uid2)){
				$row2 = $db->ROW(__TBL_USER__,"sex,grade,photo_s,nickname","id=".$uid2,'name');
				if($row2){
					$sex2      = $row2['sex'];
					$grade2    = $row2['grade'];
					$photo_s2  = $row2['photo_s'];
					$nickname2 = dataIO($row2['nickname'],'out');
					$photo_s_url2 = (!empty($photo_s2))?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_s'.$sex2.'.png';
				}
			}
		}
		$row = $db->ROW(__TBL_USER__,"sex,grade,photo_s,nickname","id=".$uid,'name');
		if($row){
			$sex      = $row['sex'];
			$grade    = $row['grade'];
			$photo_s  = $row['photo_s'];
			$nickname  = dataIO($row['nickname'],'out');
			$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		}else{
			alert_adm("UID不正确","back");
		}
        ?>
        <style>
        .table td{padding:8px}
        .table .tdL{width:100px;color:#666}
        .tabletd td{padding:0;border:0}
        #meet_flag3box{padding:10px;background-color:#f9f9f9;border:#eee 1px solid;margin-top:5px}
        #meet_flag3box .meet_flag3span{margin-bottom:5px;color:#f00}
        </style>
        <form id="Www_zeai_cn_form">
        <table class="table W95_ Mtop20" style="margin:15px 0 0 15px">
        <tr>
            <td class="tdL"><font class="Cf00">*</font>见面时间</td>
            <td class="tdR"><input name="addtime" id="addtime" type="text" class="input size2" style="width:162px" maxlength="30" value="<?php echo $addtime;?>"  autocomplete="off" /></td>
            <td class="tdL">下次联系</td>
            <td class="tdR"><input name="nexttime" id="nexttime" type="text" class="input size2" style="width:162px" maxlength="30" value="<?php echo $nexttime;?>"  autocomplete="off" /></td>
        </tr>
        <tr>
          <td class="tdL"><font class="Cf00">*</font>约见双方</td><td colspan="3" class="tdR C8d">
          
              <table border="0" cellpadding="0" cellspacing="0" class="tabletd">
                <tr>
                  <td width="190" align="center" valign="top">
                        <img src="<?php echo $photo_s_url; ?>" class=" photo_s">
                        <?php echo uicon($sex.$grade) ?><?php echo '<font class="S14 picmiddle">'.$uid.'</font></br>'.$nickname;?>
                  </td>
                  <td width="50" valign="top" style="padding-top:20px"><i class="ico S30 Cccc">&#xe62d;</i></td>
                  <td width="190" align="center" valign="top">
                    <img src="<?php echo $photo_s_url2; ?>" class="photo_s" id="photo_s2">
                      <?php if (ifint($uid2)){?>
                            <?php echo uicon($sex2.$grade2) ?><?php echo '<font class="S14 picmiddle">'.$uid2.'</font></br>'.$nickname2;?>
                            <input name="uid2" id="uid2" type="hidden" value="<?php echo $uid2;?>" />
                      <?php }else{ ?>
                          <input name="uid2" id="uid2" type="text" class="input size2 W100 center" style="margin-bottom:3px" placeholder="被约见人UID" maxlength="11" onBlur="getuinfo(this.value);"  value="<?php echo $uid2;?>" autocomplete="off" /><br>
                          <span id="uinfo"></span>
                      <?php }?>
                  </td>
                </tr>
              </table>
          
          </td></tr>
          
        <tr>
            <td class="tdL"><font class="Cf00">*</font>见面意愿</td>
            <td colspan="3" class="tdR">
                <table border="0" cellpadding="0" cellspacing="0" class="tabletd">
                <tr>
                <td width="190" align="left" valign="top"><script>zeai_cn__CreateFormItem('select','meet_ifagree','<?php echo $meet_ifagree; ?>','class="SW size2"',<?php echo $_CRM['meet_ifagree'];?>);</script></td>
                <td width="50" valign="top"><i class="ico S30 Cccc" style="line-height:30px;height:30px">&#xe62d;</i></td>
                <td width="190" align="center" valign="top"><script>zeai_cn__CreateFormItem('select','meet_ifagree2','<?php echo $meet_ifagree2; ?>','class="SW size2"',<?php echo $_CRM['meet_ifagree'];?>);</script></td>
                </tr>
                </table>        
            </td>
        </tr>
        
        <tr>
        <td class="tdL"><font class="Cf00">*</font>服务方式</td>
        <td colspan="3" class="tdR">
          <input name="fwfs" id="fwfs" type="text" class="input size2 W100_" maxlength="100" value="<?php echo $fwfs;?>"  autocomplete="off" />
        <div class="tips2 C8d" style="padding-top:5px">如：万达见面，XXX咖啡馆见面，公司包房见面，提供电话和微信等</div></td>
        </tr>
        
        
        <tr>
        <td class="tdL">反馈情况</td>
        <td colspan="3" class="tdR"><textarea name="fkqk" rows="2" class="textarea S14 W100_" id="fkqk"><?php echo $fkqk;?></textarea>
        <div class="tips2 C8d" style="padding-top:5px">如：对方说长相不满意，对方说不合适，女方不愿提供相片，感觉一般等</div>
        </td>
        </tr>
        
        <tr>
        <td class="tdL"><font class="Cf00">*</font>工单状态</td>
        <td colspan="3" class="tdR">
          <script>zeai_cn__CreateFormItem('radio','meet_flag','<?php echo $meet_flag; ?>',' class="size2 RCW"',<?php echo $_CRM['meet_flag'];?>);//onclick="meet_flag(this);"</script>
            <div id="meet_flag3box" <?php echo ($meet_flag == 3)?'':'style="display:none;"';?>>
                <div class="meet_flag3span">（选择完成，表示工单结束（无法修改），双方客户完成一次见面）</div>
                <input type="radio" name="meet_flag3" id="meet_flag3_a" class="radioskin" value="2"<?php echo ($meet_flag3 == 2)?' checked':'';?>>
                <label for="meet_flag3_a" class="radioskin-label"><i class="i1"></i><b class="W80">双方已交往</b></label>
                <input type="radio" name="meet_flag3" id="meet_flag3_b" class="radioskin" value="1"<?php echo ($meet_flag3 == 1)?' checked':'';?>>
                <label for="meet_flag3_b" class="radioskin-label"><i class="i1"></i><b class="W80">双方未交往</b></label>      
            </div>
        </td>
        </tr>
        
        <tr>
          <td class="tdL">约见截图<br><font class="S12 C999">支持批量上传</font></td>
          <td class="tdR" colspan="3">
            <div class="picli" id="picli_pathlist">
                <li class="add" id="pathlist_add"></li>
                <?php
                if(!empty($pathlist)){
                    $ARR=explode(',',$pathlist);
                    $pathlist=array();
                    foreach ($ARR as $V) {
                       echo '<li><img src="'.$_ZEAI['up2'].'/'.$V.'"><i></i></li>';
                    }
                }?>      
            </div>
          </td>
        </tr>
        
        </table>
        <input name="pathlist" id="pathlist" type="hidden" value="" />
        <input name="uid" type="hidden" value="<?php echo $uid;?>" />
        <?php if ($submitok == 'yj_mod'){?>
            <input name="submitok" type="hidden" value="yj_mod_update" />
            <input name="id" type="hidden" value="<?php echo $id;?>" />
        <?php }else{ ?>
            <input name="submitok" type="hidden" value="yj_add_update" />
        <?php }?>
        </form>
    <br><br><br><br><br><br><div class="savebtnbox"><button type="button" id="yj_submit_add" class="btn size3 HUANG3">保存并提交</button></div>
	<script>
	var up2='<?php echo $_ZEAI['up2'];?>/',upMaxMB=<?php echo $_UP['upMaxMB']; ?>,uid=<?php echo $uid; ?>;
    zeai.listEach(zeai.tag(meet_flag_box,'input'),function(obj){
        if(obj.value==3){
            obj.onclick = function(){meet_flag3box.show();}
        }else{
            obj.onclick = function(){meet_flag3box.hide();}
        }
    });
	function getuinfo(uid2){
		uid2 = uid2.replace(/\s*/g,"");o('uid2').value=uid2;
		zeai.ajax('crm_gj_yj'+zeai.ajxext+'submitok=ajax_getuinfo&uid2='+uid2,function(e){var rs = zeai.jsoneval(e);
			if (rs.flag == 1){
				o('uinfo').html(rs.C);
				o('photo_s2').src=rs.photo_s_url2;
			}else{
				if (zeai.ifint(rs.uid2,"0-9","1,8"))zeai.msg(rs.msg,o('uid2'));
				o('uinfo').html('');
				o('photo_s2').src='<?php echo $photo_s_url2;?>';
			}
		});
	}
    yj_submit_add.onclick=function(){
        if (zeai.empty(addtime.value)){
            zeai.msg('请选择【见面时间】',addtime);	
            return false;
        }
        if (!zeai.ifint(uid2.value)){
            zeai.msg('请输入正确的【被约见人UID】',uid2);	
            return false;
        }
        if (!zeai.ifint(meet_ifagree.value)){
            zeai.msg('请选择【见面意愿】',meet_ifagree);	
            return false;
        }
        if (!zeai.ifint(meet_ifagree2.value)){
            zeai.msg('请选择【被约见人见面意愿】',meet_ifagree2);	
            return false;
        }
        if (!zeai.form.ifradio('meet_flag')){
            zeai.msg('请选择【工单状态】',meet_flag1);	
            return false;
        }else{
            if(meet_flag3.checked){
                if (!zeai.form.ifradio('meet_flag3')){
                    zeai.msg('请选择【工单状态】->双方是否交往',meet_flag1);	
                    return false;
                }
            }
        }
        zeai.confirm('确定要提交么？',function(){
            zeai.ajax({url:'crm_gj_yj'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
                zeai.msg(0);
                if(rs.flag==1){
                    zeai.msg(rs.msg,{time:4});
                    //setTimeout(function(){parent.location.reload(true);},1000);
					setTimeout(function(){zeai.openurl('crm_gj_yj'+zeai.ajxext+'submitok=yj_list&uid='+<?php echo $uid;?>);},1000);
                }else{
                    zeai.msg(rs.msg,{time:5});
                }
            });
        });
    }
    
	<?php if($submitok=='yj_mod'){?>
	window.onload=function(){end();}
	<?php }?>
	zeai.photoUp({
		btnobj:pathlist_add,
		upMaxMB:upMaxMB,
		url:"crm_gj_yj"+zeai.extname,
		multiple:8,
		submitok:"ajax_pic_path_s_up",
		end:function(rs){end();},
		li:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){picli_pathlist.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');}
		}
	});
	function end(){
		var i=zeai.tag(picli_pathlist,'i'),img=zeai.tag(picli_pathlist,'img');
		if(zeai.empty(i))return;
		for(var k=0;k<img.length;k++) {
			(function(k){
				var src=img[k].src;
				img[k].onclick = function(){parent.piczoom(src.replace('_s.','_b.'));}
			})(k);
		}
		for(var k=0;k<i.length;k++) {
			(function(k){
				i[k].onclick = function(){
					var thiss=this;
					zeai.confirm('亲~~确认删除么？',function(){
						thiss.parentNode.remove();
						pathlistReset();
					});
				}
			})(k);
		}
		function pathlistReset(){
			var img=zeai.tag(picli_pathlist,'img'),pathlist=[],src;
			for(var k=0;k<img.length;k++){
				var src=img[k].src.replace(up2,'');
				pathlist.push(src);
			}
			o('pathlist').value=pathlist.join(",");
		}
		pathlistReset();
	}
    </script>
	<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
    laydate.render({elem:'#addtime',type: 'datetime'});laydate.render({elem:'#nexttime',type: 'datetime'});
    </script>
<!--认证资料-->
<?php }elseif ($submitok =='rz_list' ){?>
    <table class="table W95_ size2 Mtop20">
	<?php if (count($rz_dataARR) >= 1 && is_array($rz_dataARR)){foreach ($rz_dataARR as $k=>$V) {?>
    <tr><td class="tdL"><?php echo rz_data_info($V,'title');?></td><td colspan="3" class="tdR ">
    <div class="rzboxx">
        <?php $rzdata = RZ_get_tableinfo($uid,$V);if(is_array($rzdata)){$p1 = $rzdata['p1'];$p2 = $rzdata['p2'];$bz = dataIO($rzdata['bz'],'out');}else{$p1 = '';$p2 = '';$bz = '';}?>
        <li>
            <?php if (!empty($p1)) {?>
                <a class="pic60"><img  src="<?php echo $_ZEAI['up2']."/".$p1; ?>" class="zoom" align="absmiddle" alt="点击放大显示" title="点击放大显示" onClick="top.piczoom('<?php echo $_ZEAI['up2']."/".smb($p1,'b'); ?>')"></a>
            <?php }?>        
        </li>
        <li>
            <?php if (!empty($p2)) {?>
                <a class="pic60"><img  src="<?php echo $_ZEAI['up2']."/".$p2; ?>" class="zoom " align="absmiddle"  title="点击放大显示" alt="点击放大显示" onClick="top.piczoom('<?php echo $_ZEAI['up2']."/".smb($p2,'b'); ?>')"></a>
            <?php }?>        
        </li>
        <li style="width:280px;text-align:left;line-height:150%"><?php echo $bz; ?></li>
    </div>
    </td></tr>
	<?php }}?>
	</table>
<!--合同查看-->
<?php }elseif ($submitok =='ht_detail' ){
	if(!in_array('crm_ht_view',$QXARR))exit(noauth('暂无【合同查看】权限'));
		$fid=intval($fid);
		$rt = $db->query("SELECT * FROM ".__TBL_CRM_HT__." WHERE id=".$fid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'name');
			$uid   = $row['uid'];
			$agenttitle = dataIO($row['agenttitle'],'out');
			$admid  = $row['admid'];
			$admname  =dataIO($row['admname'],'out');	
			$hnid2  = $row['hnid2'];
			$hnname2= dataIO($row['hnname2'],'out');
			$agentid = $row['agentid'];
			$ifpay = $row['ifpay'];
			$htdate = YmdHis($row['htdate']);
			$htcode = dataIO($row['htcode'],'out');	
			$pathlist = $row['pathlist'];
			$htflag = $row['htflag'];
			$bz = dataIO($row['bz'],'out');	
			$if2 = $row['if2'];
			$price = $row['price'];
			$sjtime = YmdHis($row['sjtime']);
			$grade = $row['grade'];
			$meetnum = $row['meetnum'];
			
			$grade   = $row['grade'];
			$crm_usjtime1 = intval($row['crm_usjtime1']);
			$crm_usjtime2 = intval($row['crm_usjtime2']);
			$crm_usjtime1 =YmdHis($crm_usjtime1);
			$crm_usjtime2 = YmdHis($crm_usjtime2);
			$qxnum = intval($row['qxnum']);
			$meetnum = intval($row['meetnum']);
			$crm_ukind = intval($row['crm_ukind']);
		}else{
			alert_adm("该合同不存在！","-1");
		}
		?>
	<style>
		.table td{padding:2px 10px}
		.pathlist img{display:block;float:left;margin:10px;width:50px;height:67px;object-fit:cover;-webkit-object-fit:cover;border:#eee 1px solid;padding:3px;background-color:#fff}
    </style>
    <table class="table W95_ Mtop20" style="margin:15px auto 0 auto">
    <tr><td height="50" colspan="4" align="center" class="S18">合同编号：<?php echo $htcode;?></td>
      </tr>
    <tr><td class="tdL">录入人</td><td class="tdR"><?php echo $admname;?></td>
      <td class="tdL">分配售后</td>
      <td class="tdR"><?php echo $hnname2;?></td>
	</tr>
    <tr><td class="tdL">所属门店</td><td class="tdR"><?php echo $agenttitle;?></td>
      <td class="tdL">签署日期</td>
      <td class="tdR"><?php echo $htdate;?></td>
    </tr>
    <tr>
      <td class="tdL">合同金额</td><td class="tdR"><?php echo intval($price);?>元</td>
      <td class="tdL">是否已付款</td><td class="tdR"><?php echo ($ifpay == 1)?'已付款':'未付款';?></td>
    </tr>
    <tr>
      <td class="tdL">牵线次数</td>
      <td class="tdR"><?php echo intval($qxnum); ?></td>
      <td class="tdL">约见次数</td>
      <td class="tdR"><?php echo intval($meetnum);?></td>
    </tr>
    <tr><td class="tdL">服务起始时间</td><td class="tdR"><?php echo $crm_usjtime1;?></td>
      <td class="tdL">服务结束时间</td>
      <td class="tdR"><?php echo $crm_usjtime2; ?></td>
    </tr>
    <tr><td class="tdL">客户等级</td><td class="tdR"><?php echo crm_ugrade_title($grade);?></td>
      <td class="tdL">客户分类</td>
      <td class="tdR"><?php echo udata('crm_ukind',$crm_ukind); ?></td>
    </tr>
    <tr>
      <td class="tdL">合同拍照存档</td>
      <td colspan="3" class="tdR">
        <div class="pathlist">
        <?php
		if(!empty($pathlist)){
			$ARR=explode(',',$pathlist);
			$pathlist=array();
			foreach ($ARR as $V) {?>
   		  <a href="javascript:;" class="zoom" onClick="parent.parent.piczoom('<?php echo smb($_ZEAI['up2'].'/'.$V,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$V;?>"></a>
                <?php
			}
		}
		?>
        </div></td></tr>
    <tr><td class="tdL">服务内容/备注</td><td colspan="3" class="tdR"><?php echo $bz;?></td></tr>
    </table>    
	<?php
}
?>

<!---->

<br><br><br>
<?php require_once 'bottomadm.php';
function RZ_get_tableinfo($uid,$rzid) {
	global $db;
	$row = $db->ROW(__TBL_RZ__,"path_b,path_b2,bz","uid=".$uid." AND rzid='$rzid'");
	if ($row){
		return array('p1'=>$row[0],'p2'=>$row[1],'bz'=>$row[2]);
	}else{
		return '';
	}
}
?>


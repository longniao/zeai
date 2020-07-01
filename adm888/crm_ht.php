<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';

if($submitok=='add' && !in_array('crm_ht_add',$QXARR))exit(noauth('暂无【合同录入】权限'));
if($submitok=='mod' && !in_array('crm_ht_add',$QXARR))exit(noauth('暂无【合同修改】权限'));

require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
$t=(!empty($t))?$t:'htflag0';
//
if($submitok=='add_update' || $submitok=='mod_update'){
	if(!ifint($agentid))json_exit(array('flag'=>0,'msg'=>'请选择【所属门店】','focus'=>'agentid'));
	if(!ifint($admid))json_exit(array('flag'=>0,'msg'=>'请输入【录入人ID】','focus'=>'admid'));
	if(!ifint($hnid2))json_exit(array('flag'=>0,'msg'=>'请选择【售后红娘】','focus'=>'hnid2'));
	if(!ifint($uid))json_exit(array('flag'=>0,'msg'=>'请输入【客户UID】','focus'=>'uid'));
	if(empty($htcode))json_exit(array('flag'=>0,'msg'=>'请输入【合同编号】','focus'=>'htcode'));
	if (!ifdatetime($htdate,'Y-m-d H:i:s'))json_exit(array('flag'=>0,'msg'=>'请输入正确格式【合同签署日期】','focus'=>'htdate'));
	if(!ifint($price) && $price!=0 )json_exit(array('flag'=>0,'msg'=>'请输入【合同金额】正整数','focus'=>'price'));
	$ifpay = ($ifpay==1)?1:0;
	if (!ifdatetime($crm_usjtime1,'Y-m-d H:i:s'))json_exit(array('flag'=>0,'msg'=>'请输入正确格式【服务起始时间】','focus'=>'crm_usjtime1'));
	if (!ifdatetime($crm_usjtime2,'Y-m-d H:i:s'))json_exit(array('flag'=>0,'msg'=>'请输入正确格式【服务结束时间】','focus'=>'crm_usjtime2'));
	$bz  = dataIO($bz,'in',500);
	if(!ifint($grade))json_exit(array('flag'=>0,'msg'=>'请选择【客户等级】'));
	if(!ifint($crm_ukind))json_exit(array('flag'=>0,'msg'=>'请选择【客户分类】'));
	$htdate = strtotime($htdate);
	$crm_usjtime1 = strtotime($crm_usjtime1);
	$crm_usjtime2 = strtotime($crm_usjtime2);
	$qxnum   = intval($qxnum);
	$meetnum = intval($meetnum);
	$row = $db->ROW(__TBL_CRM_HN__,"truename","id=".$admid,"name");
	if (!$row)json_exit(array('flag'=>0,'msg'=>'当前录入人不存在','focus'=>'admid'));
	$admname=$row['truename'];
	//
	$row = $db->ROW(__TBL_CRM_HN__,"truename","id=".$hnid2,"name");
	if (!$row)json_exit(array('flag'=>0,'msg'=>'当前售后红娘不存在','focus'=>'admid'));
	$hnname2=$row['truename'];	
	//
	$row = $db->ROW(__TBL_CRM_AGENT__,"title","id=".$agentid);
	if ($row){
		$agenttitle= $row[0];
	}else{json_exit(array('flag'=>0,'msg'=>'【门店】为空，请先去增加并设置【开启】状态','focus'=>'agentid'));}
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');
	if($row2){
		$nickname= $row2[0];
	}else{
		json_exit(array('flag'=>0,'msg'=>'客户不存在','focus'=>'uid'));
	}
}

switch ($submitok) {
	case"ajax_pic_path_s_up":
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upSsize'],'2000*2000'))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$dbname=setpath_s($dbname);
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case "add_update":
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
		$db->query("INSERT INTO ".__TBL_CRM_HT__." (uid,admid,admname,hnid2,hnname2,agentid,agenttitle,ifpay,price,htdate,htcode,pathlist,addtime,htflag,bz,crm_usjtime1,crm_usjtime2,grade,meetnum,qxnum,crm_ukind) VALUES ('$uid','$admid','$admname','$hnid2','$hnname2','$agentid','$agenttitle','$ifpay','$price','$htdate','$htcode','$pathlist',".ADDTIME.",0,'$bz','$crm_usjtime1','$crm_usjtime2','$grade','$meetnum','$qxnum','$crm_ukind')");
		AddLog('【CRM】->客户【'.$nickname.'（uid:'.$uid.'）】合同录入成功【合同编号：'.$htcode.'，金额：￥'.$price.'元】');
		json_exit(array('flag'=>1,'msg'=>'录入成功'));
	break;
	case"mod_update":
		if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$row = $db->ROW(__TBL_CRM_HT__,"pathlist,htflag,payflag","id=".$fid);
		if (!$row)exit(JSON_ERROR);
		$data_pathlist= $row[0];
		$htflag  = $row[1];
		$payflag = $row[2];
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
		$SQL = "";
		
		if(in_array('crm',$QXARR)){
			$SQL2="1=1";
		}else{
			if($htflag==2){
				$SQL .= ",htflag=0";
			}
			if($payflag==2){
				$SQL .= ",payflag=0";
			}
			$SQL2="htflag<>1";
		}
		
		$db->query("UPDATE ".__TBL_CRM_HT__." SET crm_ukind='$crm_ukind',qxnum='$qxnum',meetnum='$meetnum',uid='$uid',admid='$admid',admname='$admname',hnid2='$hnid2',hnname2='$hnname2',agentid='$agentid',agenttitle='$agenttitle',ifpay='$ifpay',price='$price',htdate='$htdate',htcode='$htcode',bz='$bz',crm_usjtime1='$crm_usjtime1',crm_usjtime2='$crm_usjtime2',grade='$grade',pathlist='$pathlist' ".$SQL." WHERE ".$SQL2." AND id=".$fid);
		AddLog('【CRM】->客户【'.$nickname.'（uid:'.$uid.'）】合同修改成功【合同编号：'.$htcode.'，金额：￥'.$price.'元】');
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case"ajax_del":
		if(!in_array('crm_ht_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【合同删除】权限'));

		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'合同不存在或已被删除'));
		
		//非超管匹配自己门店
		if(!in_array('crm',$QXARR))$SQL=" AND agentid=$session_agentid";
		
		$rt = $db->query("SELECT pathlist,uid,htcode FROM ".__TBL_CRM_HT__." WHERE id=".$fid.$SQL);
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
			$row = $db->fetch_array($rt,'name');
			$pathlist = $row['pathlist'];
			$uid      = $row['uid'];
			$htcode   = $row['htcode'];
			if(!empty($pathlist)){
				$ARR=explode(',',$pathlist);
				foreach ($ARR as $V){$B = smb($V,'b');@up_send_admindel($V.'|'.$B);}
			}
			$db->query("DELETE FROM ".__TBL_CRM_HT__." WHERE id=".$fid);
			
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');
			if($row2){
				$nickname= $row2[0];	
			}
			AddLog('【CRM】->客户【'.$nickname.'（uid:'.$uid.'）】合同删除成功【合同编号：'.$htcode.'】');
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"add":
		$admid   = $session_uid;
		$hnname = $session_truename;
		$crm_usjtime1 = YmdHis(ADDTIME);
	break;
	case"mod":
		if(!in_array('crm_ht_mod',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【合同修改】权限'));
		if (!ifint($fid))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_CRM_HT__." WHERE id=".$fid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt,'name');
			$uid   = $row['uid'];
			$admid  = $row['admid'];			
			$hnid2  = $row['hnid2'];			
			$agentid = $row['agentid'];			
			$ifpay = $row['ifpay'];			
			$htdate = YmdHis($row['htdate']);
			$htcode = dataIO($row['htcode'],'out');	
			$pathlist = $row['pathlist'];
			$htflag = $row['htflag'];
			$bz = dataIO($row['bz'],'out');	
			$price = $row['price'];
			$grade = $row['grade'];
			$meetnum = $row['meetnum'];
			
			$grade   = $row['grade'];
			$crm_usjtime1 = intval($row['crm_usjtime1']);
			$crm_usjtime2 = intval($row['crm_usjtime2']);
			$crm_usjtime1 =YmdHis($crm_usjtime1);
			$crm_usjtime2 = (!empty($crm_usjtime2))?YmdHis($crm_usjtime2):'';
			$qxnum = intval($row['qxnum']);
			$meetnum = intval($row['meetnum']);
			$crm_ukind = intval($row['crm_ukind']);
			
			if ($htflag==1 && !in_array('crm',$QXARR))alert_adm("合同已经审核不能修改","-1");
		}else{
			alert_adm("该合同不存在！","-1");
		}
	break;
	case"ajax_getuinfo":
		$uid=trimhtml($uid);
		if ( !ifint($uid))json_exit(array('flag'=>0,'msg'=>'请输入客户UID'));
		$rowp = $db->NUM($uid,"sex,grade,truename,nickname");
		if ($rowp){
			$sex   = $rowp[0];
			$grade = $rowp[1];
			$nickname = dataIO($rowp[2],'out');
			$truename = dataIO($rowp[3],'out');
			$C = '&nbsp;'.uicon($sex.$grade).' '.$nickname.' '.$truename;
			exit(json_encode(array('flag'=>1,'C'=>$C)));
		}else{exit(json_encode(array('flag'=>0,'msg'=>'此客户不存在！','uid'=>$uid)));}
	break;
	case"ajax_htflag1":
		if(!in_array('crm_ht_flag',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【合同审核】权限'));
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'合同不存在'));
		if(!in_array('crm',$QXARR))$SQL=" AND agentid=$session_agentid";//非超管匹配自己门店
		$row = $db->ROW(__TBL_CRM_HT__,"uid,htcode"," id=".$fid.$SQL,"num");
		if ($row){
			$uid= $row[0];$htcode= $row[1];
			$db->query("UPDATE ".__TBL_CRM_HT__." SET htflag=1 WHERE id=".$fid.$SQL);
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');if($row2)$nickname= $row2[0];	
			AddLog('【CRM】->客户【'.$nickname.'（uid:'.$uid.'）】合同审核成功【合同编号：'.$htcode.'】');
		}else{
			json_exit(array('flag'=>0,'msg'=>'合同不存在或不属于本门店'));
		}
		json_exit(array('flag'=>1,'msg'=>'合同内容审核成功'));
	break;
	case"ajax_htflag2":
		if(!in_array('crm_ht_flag',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【合同审核】权限'));
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'合同不存在'));
		//
		if(!in_array('crm',$QXARR))$SQL=" AND agentid=$session_agentid";//非超管匹配自己门店
		$row = $db->ROW(__TBL_CRM_HT__,"uid,htcode"," id=".$fid.$SQL,"num");
		if ($row){
			$db->query("UPDATE ".__TBL_CRM_HT__." SET htflag=2 WHERE id=".$fid);
			$uid= $row[0];$htcode= $row[1];
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');if($row2)$nickname= $row2[0];	
			AddLog('【CRM】->客户【'.$nickname.'（uid:'.$uid.'）】合同驳回成功【合同编号：'.$htcode.'】');
		}else{
			json_exit(array('flag'=>0,'msg'=>'合同不存在或不属于本门店'));
		}
		json_exit(array('flag'=>1,'msg'=>'合同内容驳回成功'));
	break;
	
	case"ajax_payflag0":
		if(!in_array('crm_pay_flag',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【财务审核】权限'));
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'合同不存在'));
		//
		if(!in_array('crm',$QXARR))$SQL=" AND agentid=$session_agentid";//非超管匹配自己门店
		$row = $db->ROW(__TBL_CRM_HT__,"uid,htcode"," id=".$fid.$SQL,"num");
		if ($row){
			$db->query("UPDATE ".__TBL_CRM_HT__." SET payflag=0 WHERE id=".$fid);
			$uid= $row[0];$htcode= $row[1];
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');if($row2)$nickname= $row2[0];	
			AddLog('【CRM】->客户【'.$nickname.'（uid:'.$uid.'）】确定款已到账提交给财务审核【合同编号：'.$htcode.'】');
		}else{
			json_exit(array('flag'=>0,'msg'=>'合同不存在或不属于本门店'));
		}
		json_exit(array('flag'=>1,'msg'=>'提交成功'));
	break;
	case"ajax_payflag1":
		if(!in_array('crm_pay_flag',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【财务审核】权限'));
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'合同不存在'));
		if(!in_array('crm',$QXARR))$SQL=" AND agentid=$session_agentid";//非超管匹配自己门店

		$row = $db->ROW(__TBL_CRM_HT__,"uid,if2,sjtime,grade,htcode,crm_usjtime1,crm_usjtime2,crm_ukind,meetnum,qxnum,hnid2,hnname2,agentid,agenttitle","id=".$fid.$SQL,'num');
		if ($row){
			$uid    = $row[0];
			$if2    = $row[1];
			$sjtime = $row[2];
			$crm_ugrade    = $row[3];
			$htcode        = $row[4];
			$crm_usjtime1  = $row[5];
			$crm_usjtime2  = $row[6];
			$crm_ukind     = $row[7];
			$crm_yjnum     = $row[8];
			$crm_qxnum     = $row[9];
			$hnid2     = $row[10];
			$hnname2   = $row[11];
			$hntime2   = ADDTIME;
			$agentid   = $row[12];
			$agenttitle = $row[13];
		}else{
			json_exit(array('flag'=>0,'msg'=>'合同不存在或不属于本门店'));
		}
		$db->query("UPDATE ".__TBL_CRM_HT__." SET payflag=1 WHERE id=".$fid);
		$db->query("UPDATE ".__TBL_USER__." SET agentid='$agentid',agenttitle='$agenttitle',hnid2='$hnid2',hnname2='$hnname2',hntime2='$hntime2',crm_qxnum='$crm_qxnum',crm_yjnum='$crm_yjnum',crm_usjtime1='$crm_usjtime1',crm_usjtime2='$crm_usjtime2',crm_ugrade='$crm_ugrade',crm_ukind='$crm_ukind' WHERE id=".$uid);
		AddLog('【CRM】->客户【'.$nickname.'（uid:'.$uid.'）】合同付款审核成功【合同编号：'.$htcode.'】');
		json_exit(array('flag'=>1,'msg'=>'付款审核成功'));
	break;
	case"ajax_payflag2":
		if(!in_array('crm_pay_flag',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【财务审核】权限'));
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'合同不存在'));
		//
		if(!in_array('crm',$QXARR))$SQL=" AND agentid=$session_agentid";//非超管匹配自己门店
		$row = $db->ROW(__TBL_CRM_HT__,"uid,htcode"," id=".$fid.$SQL,"num");
		if ($row){
			$db->query("UPDATE ".__TBL_CRM_HT__." SET payflag=2 WHERE id=".$fid);
			$uid= $row[0];$htcode= $row[1];
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');if($row2)$nickname= $row2[0];	
			AddLog('【CRM】->客户【'.$nickname.'（uid:'.$uid.'）】付款驳回成功【合同编号：'.$htcode.'】');
		}else{
			json_exit(array('flag'=>0,'msg'=>'合同不存在或不属于本门店'));
		}
		json_exit(array('flag'=>1,'msg'=>'付款驳回成功'));
	break;
}
require_once ZEAI.'cache/udata.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>var up2='<?php echo $_ZEAI['up2'];?>/',upMaxMB=<?php echo $_UP['upMaxMB']; ?>;</script>
<?php if ($submitok == 'add' || $submitok == 'mod'){?>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:1200px;margin:20px 20px 50px 20px}
.table0{min-width:1200px;width:98%;margin:10px 20px 20px 20px}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
.timestyle{display:inline-block;font-size:12px;margin:0 4px;color:#fff;border-radius:3px;padding:0 6px;height:18px;line-height:18px;text-align:center;background-color:#A7CAB2}
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

.pathlist img{margin:0 2px 2px 2px;width:50px;height:67px;object-fit:cover;-webkit-object-fit:cover;border:#eee 1px solid;padding:3px;background-color:#fff}
i.top{font-size:18px;color:#FF5722}

.radioskin:checked + label.radioskin-label b{color:#000}
.crm_ugrade a{border-radius:2px;border:0}

.hnlistbox li{padding:5px 10px;border-bottom:#eee 1px solid}
.hnlistbox li:hover{background-color:#f0f0f0}
.hnlistbox li:last-child{border:0}
</style>
<?php
if (strstr($t,'htflag')){
	$colspan = 13;
}else{
	$colspan = 11;
}
?>
<body>
<div class="navbox">
	<?php if ($submitok == 'mod' || $submitok == 'add'){?><a class="ed">合同录入/修改</a><?php }else{?>

	<?php if ($t == 'htflagall'){if(!in_array('crm_ht_view',$QXARR))exit(noauth('暂无【合同查看】权限'));?><a class="ed">合同管理</a><?php }?>
	<?php if ($t == 'htflag0'){if(!in_array('crm_ht_view',$QXARR))exit(noauth('暂无【合同查看】权限'));?><a class="ed">待审合同</a><?php }?>
	<?php if ($t == 'htflag1'){if(!in_array('crm_ht_view',$QXARR))exit(noauth('暂无【合同查看】权限'));?><a class="ed">已审合同</a><?php }?>
	<?php if ($t == 'htflag2'){if(!in_array('crm_ht_view',$QXARR))exit(noauth('暂无【合同查看】权限'));?><a class="ed">被驳回合同</a><?php }?>
    

	<?php if ($t == 'payflagall'){if(!in_array('crm_pay_view',$QXARR))exit(noauth('暂无【付款查看】权限'));?><a class="ed">付款管理</a><?php }?>
	<?php if ($t == 'payflag0'){if(!in_array('crm_pay_view',$QXARR))exit(noauth('暂无【付款查看】权限'));?><a class="ed">待审付款</a><?php }?>
	<?php if ($t == 'payflag1'){if(!in_array('crm_pay_view',$QXARR))exit(noauth('暂无【付款查看】权限'));?><a class="ed">已审核付款</a><?php }?>
	<?php if ($t == 'payflag2'){if(!in_array('crm_pay_view',$QXARR))exit(noauth('暂无【付款查看】权限'));?><a class="ed">被驳回付款</a><?php }?>
	
    <?php }?>
	<div class="Rsobox"></div>
    <div class="clear"></div>
</div>
<div class="fixedblank"></div>

<!---->
<?php
/************************************** 【发布】【修改】 add **************************************/
if ($submitok == 'add' || $submitok == 'mod'){?>

<!--【发布】-->
<form id="Www_zeai_cn_form">
    <table class="table W1200 Mtop20" style="margin:15px auto 100px auto">
    <tr><td class="tdL"><font class="Cf00 S16">*</font>客户UID</td><td class="tdR"><input name="uid" id="uid" type="text" class="input size2 W100" maxlength="10" value="<?php echo $uid;?>" onBlur="getuinfo(this.value);" autocomplete="off" /><span id="uinfo"></span></td>
      <td class="tdL"><font class="Cf00 S16">*</font>录入人ID</td>
      <td class="tdR"><input name="admid" id="admid" type="text" class="input size2 W100" maxlength="10" value="<?php echo $admid;?>" /></td>
      </tr>

    <tr><td class="tdL"><font class="Cf00 S16">*</font>所属门店</td><td class="tdR">
    <?php if(in_array('crm',$QXARR)){?>
    <select name="agentid" id="agentid" class="W200 size2" required>
	<?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('【门店】为空，请先去增加并设置【开启】状态','crm_agent.php');
    } else {?>
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
    <input name="agentid" type="hidden" value="<?php echo $session_agentid;?>" />
    <?php echo $session_agenttitle;?>
    <?php }?>
    </td>
      <td class="tdL"><font class="Cf00 S16">*</font>分配售后</td>
      <td class="tdR">
      <div class="hnlistbox">
      <?php 
		//门店
		if (!in_array('crm',$QXARR)){
			$SQL.=" AND h.agentid=$session_agentid";
		}
		$SQL .= " AND FIND_IN_SET('sh',r.crmkind) ";
		$rt=$db->query("SELECT h.id,h.truename,h.roletitle,h.agenttitle FROM ".__TBL_CRM_HN__." h,".__TBL_ROLE__." r WHERE h.roleid=r.id  ".$SQL." ORDER BY h.px DESC,h.id DESC");//AND h.kind='crm'
		$totalsh = $db->num_rows($rt);
		if ($totalsh == 0) {
			echo "<div class=' C999'>暂无售后，请联系红娘主管新增<span class='ico S14'>&#xe62d;</span>顶部【系统】<span class='ico S14'>&#xe62d;</span>左侧【红娘管理】</div>";
			if (in_array('crm',$QXARR)){?>
            	<a title="新增售后红娘" class="btn size2 HONG2 picmiddle" onClick="zeai.openurl('crm_hn.php?submitok=add')">新增售后红娘</a><?php
			}
		} else {
			for($i=1;$i<=$totalsh;$i++) {
				$rows = $db->fetch_array($rt,'num');
				if(!$rows) break;
				$hnid   = $rows[0];
				$hnname = $rows[1];
				$roletitle  = dataIO($rows[2],'out');
				$agenttitle = dataIO($rows[3],'out');
				?>
				<li><input type="radio" name="hnid2" id="hnid<?php echo $hnid;?>" class="radioskin" value="<?php echo $hnid;?>"<?php echo ($hnid2 == $hnid)?' checked':'';?>><label for="hnid<?php echo $hnid;?>" class="radioskin-label"><i class="i1"></i><b class="W300 S14"><?php echo '<font class="S12 C999">'.$agenttitle.' <span class="ico S14">&#xe62d;</span></font> '.$hnname.' <font class="S12 C999">ID：'.$hnid.'（'.$roletitle.'）</font>';?></b></label></li>
				<?php
			}
		}
	  ?>
      </div>
      </td>
    </tr>
    <tr><td class="tdL"><font class="Cf00 S16">*</font>合同编号</td><td class="tdR"><input name="htcode" id="htcode" type="text" class="input size2 W200" maxlength="100" value="<?php echo $htcode;?>" /><span class="tips">格式如：HT00001</span></td>
      <td class="tdL"><font class="Cf00 S16">*</font>签署日期</td>
      <td class="tdR"><input name="htdate" id="htdate" type="text" class="input size2 W200" maxlength="50" value="<?php echo $htdate;?>"  autocomplete="off" /></td>
    </tr>

    
    <tr>
      <td class="tdL"><font class="Cf00 S16">*</font>合同金额</td><td class="tdR"><input name="price" id="price" type="text" class="input size2 W100" maxlength="7" value="<?php echo intval($price);?>"> 元</td>
      <td class="tdL"><font class="Cf00 S16">*</font>是否已付款</td><td class="tdR"><input type="checkbox" name="ifpay" id="ifpay" class="switch" value="1"<?php echo ($ifpay == 1)?' checked':'';?>><label for="ifpay" class="switch-label"><i></i><b>已付款</b><b>未付款</b></label></td>
    </tr>
    
    <tr>
      <td class="tdL"><font class="Cf00 S16">*</font>牵线次数</td>
      <td class="tdR"><input type="text" maxlength="19" class="input size2 W100" name="qxnum" id="qxnum" value="<?php echo intval($qxnum); ?>" /></td>
      <td class="tdL"><font class="Cf00 S16">*</font>约见次数</td>
      <td class="tdR"><input name="meetnum" id="meetnum" type="text" class="input size2 W100" maxlength="7" value="<?php echo intval($meetnum);?>"></td>
    </tr>
        
    <tr><td class="tdL"><font class="Cf00 S16">*</font>服务起始时间</td><td class="tdR">
      <input name="crm_usjtime1" id="crm_usjtime1" type="text" class="input size2 W200" maxlength="50" value="<?php echo $crm_usjtime1;?>"  autocomplete="off" /></td>
      <td class="tdL"><font class="Cf00 S16">*</font>服务结束时间</td>
      <td class="tdR"><input type="text" maxlength="19" class="input size2 W200" name="crm_usjtime2" id="crm_usjtime2" value="<?php echo $crm_usjtime2; ?>" /><span class="tips">格式如：<?php echo $crm_usjtime1 = YmdHis(ADDTIME+31536000);;?></span></td>
    </tr>
    <tr><td class="tdL"><font class="Cf00 S16">*</font>客户等级</td><td class="tdR" style="min-width:350px">
		<style>
		.RCW li{height:24px;line-height:24px}
		.RCW1 li{width:100%;}
		.RCW2 li{width:160px;}
        </style>
        <div class="gradelist"><script>zeai_cn__CreateFormItem('radio','grade','<?php echo $grade; ?>','class="size2 RCW RCW1"',crm_ugrade_ARR);</script><div class="clear"></div></div>
	</td>
      <td class="tdL"><font class="Cf00 S16">*</font>客户分类</td>
      <td class="tdR"><script>zeai_cn__CreateFormItem('radio','crm_ukind','<?php echo $crm_ukind; ?>','class="size2 RCW RCW2"',crm_ukind_ARR);</script></td>
    </tr>
    <tr>
      <td class="tdL">合同拍照存档<br>支持批量上传</td>
      <td colspan="3" class="tdR">
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
      </td></tr>
    <tr><td class="tdL">服务内容/备注</td><td colspan="3" class="tdR"><textarea name="bz" rows="5" class="W100_" id="bz"><?php echo $bz;?></textarea></td></tr>
      <input name="pathlist" id="pathlist" type="hidden" value="" />
      <input name="t" id="t" type="hidden" value="<?php echo $t;?>" />
      <?php if ($submitok == 'mod'){?>
          <input name="submitok" type="hidden" value="mod_update" />
          <input name="fid" type="hidden" value="<?php echo $fid;?>" />
      <?php }else{ ?>
          <input name="submitok" type="hidden" value="add_update" />
      <?php }?>
    
    </table>
    </form>
<?php if ($totalsh>0){?>
<style>
@-moz-document url-prefix() {.savebtnbox{bottom:50px}}
</style>
<br><br><br><br><div class="savebtnbox"><button type="button" id="submit_add" class="btn size3 HUANG3">确认并保存</button></div>
<?php }?>
	<script>
		<?php if($submitok=='mod'){?>
		window.onload=function(){end();
			setTimeout(function(){
				if(zeai.ifint(o('uid').value))getuinfo(o('uid').value);
			},1000);		
		}
		<?php }?>
	
		zeai.photoUp({
			btnobj:pathlist_add,
			upMaxMB:upMaxMB,
			url:"crm_ht"+zeai.extname,
			multiple:8,
			submitok:"ajax_pic_path_s_up",
			end:function(rs){end();},
			li:function(rs){
				zeai.msg(0);zeai.msg(rs.msg,{time:3});
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
		<?php if ($totalsh>0){?>
		submit_add.onclick=function(){
			zeai.confirm('<b class="S18">确定检查无误提交么？</b><br>审核成功后合同内容将不能修改，如果被驳回后，需要重新修改提交再审<br><font class="Cf00">注：合同审核成功以后，将自动按合同内容升级【客户等级】并同时修改【客户分类】</font>',function(){
				zeai.ajax({url:'crm_ht'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);
					if(rs.flag==1){
						zeai.msg(rs.msg,{time:3});
						setTimeout(function(){zeai.openurl('crm_ht'+zeai.ajxext+'t='+t.value);},1000);
					}else{
						//zeai.msg(rs.msg,{time:3,focus:o(rs.focus)});
						zeai.msg(rs.msg,{time:3});
					}
				});
			});
		}
		<?php }?>
        function getuinfo(uid){
            zeai.ajax('crm_ht'+zeai.ajxext+'submitok=ajax_getuinfo&uid='+uid,function(e){var rs = zeai.jsoneval(e);
                if (rs.flag == 1){
                    o('uinfo').html(rs.C);
                }else{
                    if (zeai.ifint(rs.uid,"0-9","1,8")){
                        zeai.msg(rs.msg,o('uid'));
                    }
					o('uinfo').html('');
                }
            });
        }

    </script>
<script src="laydate/laydate.js"></script>
<script>
        laydate.render({
            theme: 'molv'
            ,elem: '#htdate'
            ,type: 'datetime'
        }); 
        laydate.render({
            theme: 'molv'
            ,elem: '#crm_usjtime1'
            ,type: 'datetime'
        }); 
        laydate.render({
            theme: 'molv'
            ,elem: '#crm_usjtime2'
            ,type: 'datetime'
        }); 
    </script>
        
<!--【发布 修改 结束】-->
<?php
exit;
/************************************** 【列表】 list **************************************/
}else{
	?>
    <div class="clear"></div>
<table class="table0">
    <tr>
    <td align="left" class="border0 S14" >
      <?php if (strstr($t,"htflag")){?>
      <button type="button" class="btn size2" onClick="zeai.openurl('crm_ht.php?submitok=add&t=<?php echo $t;?>')"><i class="ico add">&#xe620;</i>录入新合同</button>　　
      <?php }?>

  <!--超管按门店查询-->
  <?php if(in_array('crm',$QXARR)){?>
      <?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 > 0) {
		?>按门店筛选：
      <select name="agentid" class="W200 size2 textmiddle" style="margin-right:10px"  onChange="zeai.openurl('<?php echo SELF;?>?t=<?php echo $t;?>&agentid='+this.value)">
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
  <!---->
  
    <form name="form1" method="get" action="<?php echo SELF; ?>" style="display:inline-block;margin-left:20px" class="textmiddle">
        <input name="Skey" type="text" id="Skey" size="30" maxlength="25" class="input size2" placeholder="按合同编号搜索">
        <input name="t" type="hidden" value="<?php echo $t;?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>    </td>
    <td width="300" align="right">
        
    </td>
    </tr>
    </table>
    <?php
	if(!in_array('crm_ht_view',$QXARR) && !in_array('crm_pay_view',$QXARR))exit(noauth('暂无【查看】权限'));
	
	$SQL="";
	//非超管匹配自己门店
	if(!in_array('crm',$QXARR))$SQL.=" AND agentid=$session_agentid";
	//超管搜索按门店
	if (ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND agentid=$agentid";
	//红娘管理员和合同看全部，其它看自己 //adm,sq,sh,ht,cw
	if($session_crmkind=='sq' || $session_crmkind=='sh' || $session_crmkind=='cw')$SQL.=" AND admid=$session_uid";
	
	//合同管理
	if ($t == 'htflag0')$SQL .= " AND htflag=0";
	if ($t == 'htflag1')$SQL .= " AND htflag=1";
	if ($t == 'htflag2')$SQL .= " AND htflag=2";
	//if ($t == 'htflag3')$SQL .= " AND htflag=3";
	
	
	
	
	//付款管理
	if ($t == 'payflagall')$SQL .= " AND htflag=1";
	if ($t == 'payflag0')$SQL .= " AND payflag=0 AND htflag=1";
	if ($t == 'payflag1')$SQL .= " AND payflag=1 AND htflag=1";
	if ($t == 'payflag2')$SQL .= " AND payflag=2 AND htflag=1";
	
	//合同编号搜索
	$Skey = trimhtml($Skey);
	if (!empty($Skey))$SQL .= " AND ( htcode LIKE '%".dataIO($Skey,'in')."%' ) ";
	
	$today = YmdHis(ADDTIME,'Ymd');
	switch ($gq) {
		case 'gqtotay':$SQL  = " AND date_format(from_unixtime(addtime),'%Y-%m-%d') = '$today'  ";break;
		case 'gq3':$SQL  = " AND (crm_usjtime2 - ".ADDTIME.") < 259200 ";break;
		case 'gq7':$SQL  = " AND (crm_usjtime2 - ".ADDTIME.") < 604800 ";break;
		case 'gq30':$SQL = " AND (crm_usjtime2 - ".ADDTIME.") < 2592000 ";break;
		case 'gq_1':$SQL = " AND (crm_usjtime2 < ".ADDTIME.")";break;
	}

	$rt = $db->query("SELECT * FROM ".__TBL_CRM_HT__." WHERE 1=1 ".$SQL." ORDER BY id DESC LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
		$searchA = 'crm_ht.php?agentid='.$agentid;
		?>
		<table class="tablelist">
        <tr><td colspan="<?php echo $colspan;?>" align="left" class="searchli">
		<dl>
        <dt>合同到期：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($gq))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&gq=')">不限</a>
		<a href="javascript:;" <?php echo ($gq=='gqtotay')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&gq=gqtotay')" title="今天到期">今天到期</a>
		<a href="javascript:;" <?php echo ($gq=='gq3')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&gq=gq3')" title="3天内到期">3天内到期</a>
		<a href="javascript:;" <?php echo ($gq=='gq7')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&gq=gq7')" title="7天内到期">7天内到期</a>
		<a href="javascript:;" <?php echo ($gq=='gq30')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&gq=gq30')" title="30天内到期">30天内到期</a>
		<a href="javascript:;" <?php echo ($gq=='gq_1')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&gq=gq_1')" title="已经过期">已过期</a>
        </dd></dl>
        </td>
        <form id="www_zeai_cn_FORM">
		<tr>
		<th width="10"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label" style="display:none"><i class="i1"></i></label></th>
		<th width="60" align="left">头像</th>
		<th width="120" align="left">UID/昵称/姓名</th>
		<th width="150" align="center">合同编号/签署日期</th>
		<th width="170" align="center">合同客户等级/服务时间</th>
		<th width="80" align="center">合同金额(元)</th>
		<th width="120" align="center">门店/录入</th>
		<th width="70" align="center">录入时间</th>
		<th width="20" align="center">&nbsp;</th>
		<th>合同拍照存档</th>
		<?php if (strstr($t,"htflag")){?><th width="150" align="center">合同审核确认</th><?php }?>
		<?php if (strstr($t,"payflag")){?><th width="150" align="center">付款审核确认</th><?php }?>
        <?php if (strstr($t,"htflag")){?>
			<th width="50" align="center">修改</th>
            <th width="50" align="center">删除</th>
        <?php }?>
		</tr>
		<?php
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$id    = $rows['id'];
			$uid   = $rows['uid'];
			$admid  = $rows['admid'];
			$admname= dataIO($rows['admname'],'out');
			$ifpay = $rows['ifpay'];			
			$htdate = $rows['htdate'];
			$htcode = dataIO($rows['htcode'],'out');	
			$pathlist = $rows['pathlist'];
			$htflag = $rows['htflag'];
			$bz  = dataIO($row['bz'],'out');	
			$if2 = $rows['if2'];
			$grade    = $rows['grade'];
			$addtime  = YmdHis($rows['addtime']);
			$htflag  = $rows['htflag'];
			$payflag = $rows['payflag'];
			$price = $rows['price'];
			$agenttitle= dataIO($rows['agenttitle'],'out');
			$agenttitle=(!empty($agenttitle))?$agenttitle:'';
			
			
			$crm_usjtime1 = intval($rows['crm_usjtime1']);
			$crm_usjtime2 = intval($rows['crm_usjtime2']);
			$crm_ukind = intval($rows['crm_ukind']);
			$qxnum     = intval($rows['qxnum']);
			$meetnum   = intval($rows['meetnum']);
			
			//
			if(!empty($Skey)){
				$htcode = str_replace($Skey,'<font class="Cf00 B">'.$Skey.'</font>',$htcode);
			}
			if(!empty($path_s)){
				$path_s_url = $_ZEAI['up2'].'/'.$path_s;
				$path_s_str = '<img src="'.$path_s_url.'">';
			}else{
				$path_s_url = '';
				$path_s_str = '';
			}
			//
			$row2 = $db->ROW(__TBL_USER__,"sex,grade,photo_s,truename,nickname,uname","id=".$uid,'name');
			if ($row2){
				$nickname  = dataIO($row2['nickname'],'out');
				$uname     = trimhtml($row2['uname']);
				$truename  = trimhtml($row2['truename']);
				$photo_s   = $row2['photo_s'];
				$sex       = $row2['sex'];
				$Ugrade    = $row2['grade'];
				$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
			}
			$title2 = (!empty($nickname))?urlencode(trimhtml($nickname).'/'.$uid):$uid;
		?>
		<tr id="tr<?php echo $id;?>">
		<td width="10" height="40"></td>
		<td width="60" align="left">
        <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_ss"><img src="<?php echo $photo_s_url; ?>" class="photo_s"></a>
		</td>
		<td width="120" align="left" style="padding:10px 0" class="lineH150">
			<?php
			echo uicon($sex.$Ugrade).'<span class="picmiddle">'.$uid.'</span><br>';
			echo '<span class="picmiddle">';
			echo'<font class="uleft">';
				if(!empty($nickname))echo $nickname;
				if(!empty($truename))echo '<br>'.$truename;
			echo'</font>';
			echo'</span>';
            ?>        
        </td>
		<td width="150" align="center" class="lineH200">
            <a title="查看合同详情" class="aBAI" onClick="zeai.iframe('<?php echo '<img src='.$photo_s_url.' class=photo_s_iframe>';?>【<?php echo $uid;?>】合同详情','crm_gj_yj.php?submitok=ht_detail&fid=<?php echo $id;?>',700,520)"><?php echo $htcode;?></a>
            <br>
			<font class="C666"><?php echo YmdHis($htdate,'Ymd'); ?></font>
          </td>
		<td width="170" align="center" class="lineH200 C666 padding15">
          
          <div class="center"><?php echo crm_ugrade_time($uid,$grade,'btn_djs_noA',$crm_usjtime1,$crm_usjtime2);?></div>
          
          <font class="C666">起始日：</font><?php echo YmdHis($crm_usjtime1,'Ymd'); ?><br>
<!--          <font class="C666">约见</font> <?php echo $meetnum; ?> 次　
          <font class="C666">牵线</font> <?php echo $qxnum; ?> 次
-->        </td>
		<td width="80" align="center" class="C999 lineH200">￥<font class="S16 Cf00 FArial"><?php echo $price;?></font>
        <br>
        <?php
		echo ($ifpay==1)?'<font class="C090">已付款</font>':'未付款';

		?>
        
        </td>
		<td width="120" align="center" class="lineH150" >
		<div class="linebox"><div class="line"></div><div class="title BAI"><?php echo $agenttitle;?></div></div>
		<?php if(!empty($admname)){echo $admname.'<br><font class="C999">ID:'.$admid.'</font>';}?></td>
		<td width="70" align="center" class="C666"><?php echo $addtime;?></td>
		<td width="20" align="center" class="C999">&nbsp;</td>
		<td align="left" >
        <div class="pathlist">
        <?php
		if(!empty($pathlist)){
			$ARR=explode(',',$pathlist);
			$pathlist=array();
			foreach ($ARR as $V) {?>
   		  <a href="javascript:;" class="zoom" onClick="parent.piczoom('<?php echo smb($_ZEAI['up2'].'/'.$V,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$V;?>"></a>
                <?php
			}
		}
		?>
        </div>
        </td>
        
        
            <?php if (strstr($t,"htflag")){?>
            <td width="150" align="center" class="C999"><?php 
            switch ($htflag){ 
                case 0:
                    if(in_array('crm_ht_flag',$QXARR)){
						echo "<a class='btn size2 LV htflag1' title='审核通过' clsid='".$id."' title2='".urlencode(trimhtml($htcode))."'>审核</a>　　";
						echo "<a class='btn size2 HUI htflag2' title='驳回' clsid='".$id."' title2='".urlencode(trimhtml($htcode))."'>驳回</a>";
					}else{
						echo '<i class="timeico20"></i> <font class="picmiddle C999">等待审核</font>';
					}
                break;
                case 1:
					echo "<i class='ico S16' style='color:#45C01A'>&#xe60d;</i> <font style='color:#090' class='S14'>合同已审核</font>";
					switch ($payflag){ 
						case 0:echo "<br>等待财务确认";break;
						case 1:echo "<br><i class='ico S16' style='color:#45C01A'>&#xe60d;</i> <font style='color:#090' class='S14'>财务已确认</font>";break;
						case 2:
							echo "<br>财务已驳回";
							echo "<br><a class='aLANed payflag0' title='重新提交' clsid='".$id."' title2='".urlencode(trimhtml($htcode))."' style='display:inline-block;margin-top:5px'>重新提交</a>";
						break;
					}
		
					
				break;
                case 2:
					if(in_array('crm_ht_flag',$QXARR)){
						echo "已驳回<div class='Caaa' style='margin-top:5px'><i class='timeico20'></i> <font class='picmiddle C999'>等待修改重新提交</font></dov>";
					}else{
						echo "已驳回<div class='Caaa' style='margin-top:5px'><i class='timeico20'></i> <font class='picmiddle C999'>请修改后重新提交</font></dov>";
					}
				break;
            }
            ?>
            </td>
        	<?php }?>
        
        	<?php if (strstr($t,"payflag")){?>
            <td width="150" align="center">
			<?php 
            switch ($payflag){ 
                case 0:
                    echo "<a class='btn size2 LV payflag1' title='审核付款状态' clsid='".$id."' title2='".urlencode(trimhtml($htcode))."'>审核</a>　　";
                    echo "<a class='btn size2 HUI payflag2' title='驳回付款状态' clsid='".$id."' title2='".urlencode(trimhtml($htcode))."'>驳回</a>";
                break;
                case 1:echo "<i class='ico S18' style='color:#45C01A'>&#xe60d;</i><br><font style='color:#45C01A' class='S14'>已审核</font>";break;
                case 2:echo "已驳回<div class='Caaa' style='margin-top:5px'>等待客户打款</dov>";break;
            }
			?>
          </td>
			<?php }?>
        
        
        	<?php if (strstr($t,"htflag")){?>
            <td width="50" align="center">
            <?php if ($htflag != 1 || in_array('crm',$QXARR)){?><a class="btn size2 BAI tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>" href="crm_ht.php?submitok=mod&fid=<?php echo $id;?>&t=<?php echo $t;?>">修改</a><?php }?>
            </td>
          	<td width="50" align="center"><a clsid="<?php echo $id; ?>" class="delico del2 tips" tips-direction="left" tips-title="删除"></a></td>
            <?php }?>
        
		</tr>
		<?php } ?>
		<tfoot><tr>
		<td colspan="<?php echo $colspan;?>">
		<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label" style="display:none"><i class="i1"></i></label>　
		<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
		</td>
		</tr></tfoot>
        </form>
</table>
		
<script>
		var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';

		zeai.listEach('.del2',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			obj.onclick = function(){
				zeai.confirm('★请慎重★确定真的要删除么？',function(){
					zeai.ajax({url:'crm_ht.php?submitok=ajax_del&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg,{time:3});
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.htflag1',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			var title=obj.getAttribute("title2");
			obj.onclick = function(){
				zeai.confirm('<b class="S18">确定【'+decodeURIComponent(title)+'】合同“内容无误”审核通过么？</b><br>此操作不可逆转',function(){
					zeai.ajax({url:'crm_ht.php?submitok=ajax_htflag1&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg,{time:3});
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.htflag2',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			var title=obj.getAttribute("title2");
			obj.onclick = function(){
				zeai.confirm('确定驳回【'+decodeURIComponent(title)+'】么？<br>驳回后，合同重新修改后可以重审',function(){
					zeai.ajax({url:'crm_ht.php?submitok=ajax_htflag2&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg,{time:3});
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		
		zeai.listEach('.payflag0',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			var title=obj.getAttribute("title2");
			obj.onclick = function(){
				zeai.confirm('确定款已到账，提交给财务审核么？',function(){
					zeai.ajax({url:'crm_ht.php?submitok=ajax_payflag0&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg,{time:3});
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});

		
		zeai.listEach('.payflag1',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			var title=obj.getAttribute("title2");
			obj.onclick = function(){
				zeai.confirm('<b class="S18">确定【'+decodeURIComponent(title)+'】合同“付款真实到账”审核通过么？</b><br>1．此操作触发升级客户等级和服务期限等(合同选项)<br>2．自动分配售后人员(合同选项))<br>3．自动强制更新门店归属(合同选项)<br>4．此操作不可逆转',function(){
					zeai.ajax({url:'crm_ht.php?submitok=ajax_payflag1&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg,{time:3});
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.payflag2',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			var title=obj.getAttribute("title2");
			obj.onclick = function(){
				zeai.confirm('确定款项没到账，驳回【'+decodeURIComponent(title)+'】付款状态么？',function(){
					zeai.ajax({url:'crm_ht.php?submitok=ajax_payflag2&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg,{time:3});
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.photo_ss',function(obj){
			obj.onclick = function(){
				var uid = parseInt(obj.getAttribute("uid")),
				title2 = obj.getAttribute("title2");
				zeai.iframe('【'+decodeURIComponent(title2)+'】个人主页','crm_user_detail.php?t=2&iframenav=1&uid='+uid);
			}
		});
		</script>
		<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }}?>




<br><br><br>

<?php require_once 'bottomadm.php';?>
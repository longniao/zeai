<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('form',$QXARR))exit(noauth());
require_once ZEAI.'cache/udata.php';
if ( !ifint($fid))callmsg("www_zeai_cn_error_fid","-1");
$row2 = $db->ROW(__TBL_FORM__,"defpass","id=".$fid,"num");if (!$row2){json_exit(array('flag'=>0,'msg'=>'表单不存在'));}else{$defpass = dataIO($row2[0],'out');}
if($submitok=='ajax_del'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'记录不存在'));
	$row = $db->ROW(__TBL_FORM_U__,"mob","id=".$id);
	if ($row){$mob= $row[0];}else{json_exit(array('flag'=>0,'msg'=>'记录不存在'));}
	$db->query("DELETE FROM ".__TBL_FORM_U__." WHERE fid=".$fid." AND id=".$id);
	AddLog('【表单采集】删除用户->【'.$mob.' id:'.$id.'】');
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok=='ajax_indata'){
	if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要操作的信息'));
	if(!is_array($list))exit(JSON_ERROR);
	if(count($list)>=1){
		foreach($list as $id){if(!indata($id))continue;}
	}
	AddLog('【表单采集】批量入库，表单ID:'.$fid);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok=='ajax_inalldata'){
	$rt=$db->query("SELECT id FROM ".__TBL_FORM_U__." WHERE ifindatabase=0 AND fid=".$fid);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'num');
			if(!$rows) break;
			if(!indata($rows[0]))continue;
		}
	}
	AddLog('【表单采集】一键入库，表单ID:'.$fid);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok=='excel'){
	$data_disable=array('tag1','tag2','age');
	function data_data_title($data_data,$f,$kind='') {
		foreach($data_data as $v){
			if($v['fieldname'] == $f){
				if($kind=='subkind'){
					return $v['subkind'];
				}else{
					return $v['title'];
				}
			}
		}
	}
	$rtt = $db->query("SELECT fieldname,title,subkind FROM ".__TBL_UDATA__." ORDER BY px DESC,id DESC");
	while($tmprows = $db->fetch_array($rtt,'name')){
		if (in_array($tmprows['fieldname'],$data_disable) )continue;
		if($tmprows['fieldname']=='heigh' || $tmprows['fieldname']=='weigh')$tmprows['subkind']=2;
		if($tmprows['fieldname']=='parent')$tmprows['title']='替谁征婚';
		$data_data[]=$tmprows;
	}
	$row = $db->ROW(__TBL_FORM__,"form_data","id=".$fid,"num");
	$form_data= explode(',',$row[0]);
	//
	//$data_disable_li=array('photo_s');//'areaid','area2id',
	$nodatatext = '未填';
	$rt = $db->query("SELECT * FROM ".__TBL_FORM_U__." U WHERE fid=".$fid." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if($total>0){
		$content = "<meta http-equiv='Content-Type' content='text/html;charset=utf-8'><table border='1' cellpadding='0' cellspacing='0' bordercolor='#000000'><tr style='background:#FF6F6F;color:#fff'>";
		if (is_array($form_data) && count($form_data)>0){
			foreach($form_data as $F){
				//if(in_array($F,$data_disable_li))continue;
				$T = data_data_title($data_data,$F);
				$content .= '<td height="50">'.$T.'</td>';
			}
		}
		$content .= '<td>认证</td>';
		$content .= '<td>注册入库</td>';
		$content .= '<td>微信推文</td>';	
		$content .= "</tr>";
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$rzstr = '';
			$mob      = $rows['mob'];
			$rz_mob   = $rows['rz_mob'];
			$rz_identity = $rows['rz_identity'];
			$rz_photo    = $rows['rz_photo'];
			$addtime    = YmdHis($rows['addtime']);
			$udata      = json_decode(dataIO($rows['udata'],'out'),true);;
			$admid      = $rows['admid'];
			$agree_reg  = $rows['agree_reg'];
			$agree_wxshare  = $rows['agree_wxshare'];
			$rz_photo_path1 = $rows['rz_photo_path1'];
			$rz_truename    = dataIO($rows['rz_truename'],'out');
			$rz_identitynum = $rows['rz_identitynum'];
			$ifindatabase   = $rows['ifindatabase'];
			if($rz_mob==1)$rzstr = '手　　机：'.$mob.'<br>';
			if(!empty($rz_truename))$rzstr .= '姓　　名：'.$rz_truename.'<br>';
			if(!empty($rz_identitynum))$rzstr .= '身份证号：'.$rz_identitynum.'<br>';
			if($rz_photo==1 && !empty($rz_photo_path1)){
				$rzstr .= '真人认证：';
				$rz_photo_url = $_ZEAI['up2'].'/'.$rz_photo_path1;
				$rzstr .= '<a href='.$rz_photo_url.' target="_blank"><img src="'.$rz_photo_url.'" width="30" height="30"></a>';
			}
			
			if($agree_reg==1){
				$ifindatabase_str=($ifindatabase==1)?'已经入库注册':'未入库注册';
			}else{
				$ifindatabase_str='不同意注册入库'; 
			}
			if($agree_wxshare==1){
				$agree_wxshare_str='同意分享';
			}else{
				$agree_wxshare_str='不同意分享';
			}
			$content .=  "<tr>";
			foreach($form_data as $F){
				//if(in_array($F,$data_disable_li))continue;
				//if($F=='photo_s')continue;
				$subkind = data_data_title($data_data,$F,'subkind');
				$out='';
				switch ($subkind) {//1:文本,2:单选,3:复选,4:区间,5:特殊
					case 1:$out = dataIO($udata[$F],'out');break;
					case 2:$out = udata($F,$udata[$F]);break;
					case 3:$out = udata($F,$udata[$F]);break;
					default:
						if($F=='areaid')$F='areatitle';
						if($F=='area2id')$F='area2title';
						if($F=='photo_s'){
							$photo_s = explode(',',$udata['photo_s']);
							$photo_s = $photo_s[0];
							$sex = intval($udata['sex']);
							$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
							$out='<a href="'.smb($photo_s_url,'b').'" target="_blank"><img src="'.$photo_s_url.'" width="30" height="30"></a>';
						}else{
							$out = $udata[$F];
						}
					break;
				}
				$out=(!empty($out))?$out:'&nbsp;';
				$content .= '<td valign="middle">'.$out.'</td>';
			}
			$content .= '<td valign="middle">'.$rzstr.'</td>';
			$content .= '<td valign="middle">'.$ifindatabase_str.'</td>';
			$content .= '<td valign="middle">'.$agree_wxshare_str.'</td>';	
			$content .= "</tr>";
		}
		$content.= '</table>';
		$filaname =  YmdHis(ADDTIME).'表单采集信息';
		header("Content-type:application/vnd.ms-excel;charset=utf-8");
		header("Content-Disposition:filename=".$filaname.".xls");
		echo $content;
		AddLog('【表单采集】数据导出，表单ID：'.$fid);
	} else {
		callmsg("暂无报名信息","-1");
	}
	exit;
}
function indata($id) {
	global $db,$defpass;
	$id=intval($id);
	$row = $db->ROW(__TBL_FORM_U__,"mob,openid,rz_mob,rz_identity,rz_photo,addtime,udata,admid,rz_photo_path1,rz_truename,rz_identitynum,agree_reg","ifindatabase=0 AND id=".$id,'name');
	if(!$row)return false;;
	$admid    = intval($row['admid']);
	$mob      = $row['mob'];
	$openid   = $row['openid'];
	$agree_reg= $row['agree_reg'];
	$addtime  = $row['addtime'];
	if(ifmob($mob)){
		$row2 = $db->ROW(__TBL_USER__,"id","mob=".$mob);
		if ($row2){
			$db->query("UPDATE ".__TBL_FORM_U__." SET ifindatabase=1 WHERE id=".$id);
			return false;
		}
	}
	if(!empty($openid)){
		$row2 = $db->ROW(__TBL_USER__,"id","openid='$openid'");
		if ($row2){
			$db->query("UPDATE ".__TBL_FORM_U__." SET ifindatabase=1 WHERE id=".$id);
			return false;
		}
	}
	if($agree_reg==0)return false;
	//
	$pwd =md5($defpass);
	$regkind=11;
	$regtime=$addtime;
	$endtime=$addtime;
	$refresh_time=$addtime;
	$flag   = 1;
	$dataflag=1;
	$grade=1;
	$db->query("INSERT INTO ".__TBL_USER__."  (pwd,regkind,regtime,endtime,refresh_time,flag,dataflag,grade) VALUES ('$pwd','$regkind','$regtime','$endtime','$refresh_time','$flag','$dataflag','$grade')");
	$uid = $db->insert_id();
	$uname='form'.$uid;
	$SQL  = "kind=1,uname='$uname'";		
	//
	$admid  = intval($admid);
	if(ifint($admid)){
		$row2 = $db->ROW(__TBL_ADMIN__,"truename,agentid,agenttitle","id=".$admid,"name");
		if ($row2){
			$admname = $row2['truename'];
			$agentid = intval($row2['agentid']);
			$agenttitle = $row2['agenttitle'];
			$SQL.=",admid=$admid,admname='$admname',agentid='$agentid',agenttitle='$agenttitle'";
		}
	}
	if(!empty($openid))$SQL .= ",openid='$openid'";
	//
	$udata    = json_decode(dataIO($row['udata'],'out'),true);;
	$rz_mob   = $row['rz_mob'];
	$rz_identity= $row['rz_identity'];
	$rz_photo   = $row['rz_photo'];
	$rz_photo_path1 = $row['rz_photo_path1'];
	$rz_truename    = dataIO($row['rz_truename'],'out');
	$rz_identitynum = $row['rz_identitynum'];
	//
	if (is_array($udata) && count($udata)>0){
	foreach($udata as $F=>$V){
		if($F=='photo_s'){
			$photo_s = explode(',',$V);
			$V = $photo_s[0];
			$SQL.=",photo_f=1";
			if(count($photo_s)>0){
				foreach($photo_s as $P){
					if($P==$V)continue;
					$db->query("INSERT INTO ".__TBL_PHOTO__." (uid,path_s,flag,addtime) VALUES ($uid,'$P',1,'$addtime')");
				}
			}
		}elseif($F=='mob'){
			if($rz_mob==1)continue;
		}elseif($F=='truename'){
			if($rz_identity==1 || $rz_photo==1)continue;
		}elseif($F=='identitynum'){
			if($rz_identity==1 || $rz_photo==1)continue;
		}
		if(!empty($V))$SQL.=",$F='$V'";
	}}
	$ifRZ=false;
	if($rz_mob==1 && ifmob($mob)){$RZ[]='mob';$ifRZ=true;$SQL.=",mob='$mob'";}
	if($rz_identity==1){
		$RZ[]='identity';$ifRZ=true;
		if($rz_photo!=1)$SQL.=",truename='$rz_truename',identitynum='$rz_identitynum'";
		$bz=$rz_identitynum.'（'.$rz_truename.'）';
		$db->query("INSERT INTO ".__TBL_RZ__."(uid,rzid,flag,addtime,bz) VALUES ($uid,'identity',1,".ADDTIME.",'$bz')");
	}
	if($rz_photo==1 && !empty($rz_photo_path1)){
		$RZ[]='photo';$ifRZ=true;
		if($rz_identity!=1)$SQL.=",truename='$rz_truename',identitynum='$rz_identitynum'";
		$bz=$rz_identitynum.'（'.$rz_truename.'）';
		$db->query("INSERT INTO ".__TBL_RZ__."(uid,rzid,flag,path_b,addtime,bz) VALUES ($uid,'photo',1,'$rz_photo_path1',".ADDTIME.",'$bz')");
	}
	if($ifRZ){$RZ = (is_array($RZ))?implode(',',$RZ):'';$SQL .= ",RZ='$RZ'";}
	$db->query("UPDATE ".__TBL_USER__." SET ".$SQL." WHERE id=".$uid);
	set_data_ed_bfb($uid);
	$db->query("UPDATE ".__TBL_FORM_U__." SET ifindatabase=1 WHERE id=".$id);
	return true;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.listdatabox{padding:10px 0}
.listdatabox li{display:inline-block;padding:1px 5px;border:#ddd 1px solid;margin:3px;border-radius:3px;color:#888}
img.photo_s40{border-radius:2px;display:inline-block;;vertical-align:middle}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;text-align:center;font-size:12px;margin-right:2px}
i.wxlv{color:#31C93C;margin-right:2px}
</style>
<body>
<?php
	$row = $db->ROW(__TBL_FORM__,"*","id=".$fid);
	if (!$row)exit('<br><br>表单不存在或已被删除');
	//
	$SQL = "";
	$Skey = trimm($Skey);
	if (ifmob($Skey)){
		$SQL = " AND (mob=$Skey) ";
	}elseif(!empty($Skey)){
		$SQL = " AND ( rz_truename LIKE '%".$Skey."%' || udata LIKE '%".$Skey."%' )";
	}
	if($s==1){
		$SQL .= " AND ifindatabase=1 AND agree_reg=1";
	}elseif($s==2){
		$SQL .= " AND ifindatabase=0 AND agree_reg=1";
	}elseif($s==3){
		$SQL .= " AND agree_reg=0";
	}
	$rt = $db->query("SELECT id,mob,rz_mob,rz_identity,rz_photo,addtime,udata,admid,agree_reg,agree_wxshare,rz_photo_path1,rz_truename,rz_identitynum,ifindatabase FROM ".__TBL_FORM_U__." WHERE fid=".$fid.$SQL." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
		if (!empty($SQL))echo "<br><br><a class='aQINGed' href='javascript:history.back(-1)'>重新筛选</a>";
		echo "</div>";
	} else {
		$page_skin = 1;$pagesize=20;require_once ZEAI.'sub/page.php';
		$totalsex1 = $db->COUNT(__TBL_FORM_U__,"ifindatabase=1 AND agree_reg=1 AND fid=".$fid);
		$totalsex2 = $db->COUNT(__TBL_FORM_U__,"ifindatabase=0 AND agree_reg=1 AND fid=".$fid);
		$totalsex3 = $db->COUNT(__TBL_FORM_U__,"agree_reg=0 AND fid=".$fid);
	?>
    <div class="navbox">
    <a href="form_u.php?fid=<?php echo $fid;?>" <?php echo (empty($s))?' class="ed"':'';?>>表单用户管理<?php echo '<b>'.$total.'</b>';?></a>
    <a href="form_u.php?fid=<?php echo $fid;?>&s=1" <?php echo ($s==1)?' class="ed"':'';?>>已入库<?php echo '<b class="border">'.$totalsex1.'</b>';?></a>
    <a href="form_u.php?fid=<?php echo $fid;?>&s=2" <?php echo ($s==2)?' class="ed"':'';?>>未入库<?php echo '<b class="border">'.$totalsex2.'</b>';?></a>
    <a href="form_u.php?fid=<?php echo $fid;?>&s=3" <?php echo ($s==3)?' class="ed"':'';?>>不同意入库<?php echo '<b class="border">'.$totalsex3.'</b>';?></a>
    <div class="Rsobox"></div>
    <div class="clear"></div></div>
    <div class="fixedblank"></div>


<table class="table0 W95_ Mtop10">
        <tr>
        <td align="left" class="S14">
          <form name="www.yzlove.com.v6.0..QQ7144100" method="get" action="<?php echo SELF; ?>">
            <input name="Skey" type="text" id="Skey" maxlength="25" class="input size2 W150" placeholder="按姓名/手机/昵称查询">
            <input type="hidden" name="fid" value="<?php echo $fid;?>" />
            <input type="submit" value="搜索" class="btn size2 QING" />
          </form>   
        </td>
        <td align="left" class="S14">

        
        </td>
        <td align="right" class="S14">
        <a href="form_u.php?submitok=excel&fid=<?php echo $fid;?>" class="btn size2 QING" /><i class="ico2">&#xe63b;</i>导出用户</a>　
        <button type="button" class="btn size2 QING" id="ajax_inalldata_btn" />一键入库</button>
        </td>
        </tr>
    </table>
<div class="clear"></div>
<form id="www_zeai_cn_FORM">
    <table class="tablelist W95_ Mtop10 Mbottom50">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60" align="center">用户</th>
    <th align="left">资料</th>
    <th width="200" align="left">认证信息</th>
    <th width="70">登记时间</th>
    <th width="70" align="center">是否入库</th>
    <th width="40" align="center">删除</th>
    </tr>
    <?php
	$data_disable=array('tag1','tag2','age');
	function data_data_title($data_data,$f,$kind='') {foreach($data_data as $v){if($v['fieldname'] == $f)if($kind=='subkind'){return $v['subkind'];}else{return $v['title'];}}}
	$rtt = $db->query("SELECT fieldname,title,subkind FROM ".__TBL_UDATA__." ORDER BY px DESC,id DESC");
	while($tmprows = $db->fetch_array($rtt,'name')){
		if (in_array($tmprows['fieldname'],$data_disable) )continue;
		if($tmprows['fieldname']=='heigh' || $tmprows['fieldname']=='weigh')$tmprows['subkind']=2;
		if($tmprows['fieldname']=='parent')$tmprows['title']='替谁征婚';
		$data_data[]=$tmprows;
	}
	$data_disable_li=array('areaid','area2id','photo_s');
    for($i=1;$i<=$pagesize;$i++) {
        $rows = $db->fetch_array($rt,'name');
        if(!$rows) break;
        $id       = $rows['id'];
        $mob      = $rows['mob'];
        $rz_mob   = $rows['rz_mob'];
        $rz_identity = $rows['rz_identity'];
        $rz_photo    = $rows['rz_photo'];
		$addtime    = YmdHis($rows['addtime']);
        $udata      = json_decode(dataIO($rows['udata'],'out'),true);;
        $admid      = $rows['admid'];
        $agree_reg  = $rows['agree_reg'];
        $agree_wxshare  = $rows['agree_wxshare'];
        $rz_photo_path1 = $rows['rz_photo_path1'];
        $rz_truename    = dataIO($rows['rz_truename'],'out');
        $rz_identitynum = $rows['rz_identitynum'];
        $ifindatabase   = $rows['ifindatabase'];
		//
		$photo_s = explode(',',$udata['photo_s']);
		$photo_s = $photo_s[0];
		$sex = intval($udata['sex']);
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		if($agree_reg==1){
			$ifindatabase_str=($ifindatabase==1)?'<i class="ico S14 wxlv">&#xe6b1;</i><font class="C090">已经入库</font>':'<font class="C999">未入库</font>';
		}else{
			$ifindatabase_str='<font class="C999">不同意入库</font>'; 
		}
		if($agree_wxshare==1){
			$agree_wxshare_str='<br><i class="ico S14 wxlv">&#xe6b1;</i><font class="C090">同意分享</font>';
		}else{
			$agree_wxshare_str='<font class="C999">不同意分享</font>';
		}
    ?>
	<tr id="tr<?php echo $id;?>">
    <td width="20" height="30" align="left"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
    <td width="60" height="30" align="center">
    <a href="javascript:;" onClick="parent.piczoom('<?php echo smb($photo_s_url,'b'); ?>');"><img class="photo_s" src="<?php echo $photo_s_url; ?>"></a></td>
    <td height="30" align="left">
	<div class="listdatabox">
	<?php if (is_array($udata) && count($udata)>0){
		foreach($udata as $F=>$V){
			if(in_array($F,$data_disable_li))continue;
			$subkind = data_data_title($data_data,$F,'subkind');
			switch ($subkind) {//1:文本,2:单选,3:复选,4:区间,5:特殊
				case 1:$out = dataIO($V,'out');break;
				case 2:$out = udata($F,$V);break;
				case 3:$out = udata($F,$V);break;
				//case 5:$out = $V;break;
				default:$out = $V;break;
			}
			if($F=='weixin')$out='微信：'.$out;
			if($F=='area2title')$out='户籍：'.$out;
			echo '<li>'.$out.'</li>';
		}}?>
	</div>
    </td>
    <td width="200" height="30" align="left" style="padding:10px 0 0">
  <?php 
if($rz_mob==1)echo '<font class="C999">手　　机：</font>'.$mob.'<br>';
if(!empty($rz_truename))echo '<font class="C999">姓　　名：</font>'.$rz_truename.'<br>';
if(!empty($rz_identitynum))echo '<font class="C999">身份证号：</font>'.$rz_identitynum.'<br>';
if($rz_photo==1 && !empty($rz_photo_path1)){
	echo '<font class="C999">真人认证：</font>';
	$rz_photo_url = $_ZEAI['up2'].'/'.$rz_photo_path1;
	?>
	<img class="photo_s40" src="<?php echo $rz_photo_url; ?>" onClick="parent.piczoom('<?php echo $rz_photo_url; ?>');">
    <?php	
    }
?>    </td>
    <td width="70" height="30" align="left" class="C999"><?php echo $addtime;?></td>
    <td width="70" align="center"><?php echo $ifindatabase_str.$agree_wxshare_str;?></td>
    <td width="40" align="center"><a title="删除" fid="<?php echo $fid; ?>" clsid="<?php echo $id; ?>" class="delico"></a></td>
    </tr>
    <?php } ?>
    <div class="listbottombox">
        <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
        <button type="button" id="btnsend" value="" class="btn size2 disabled action">批量入库</button>
        <input type="hidden" name="submitok" id="submitok" value="" />
        <?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </div>
</table>
</form>
<?php }?>
<br><br><br><br>
<script>
btnsend.onclick=function(){
	zeai.confirm('<b class="S18">确定正式入库么？</b><br>1.如果表单用户手机和OPNEID已被主库占用将忽略并更新已入库标记<br>2.只对【同意注册】的用户入库',function(){
		zeai.msg('正在入库...',{time:20});
		zeai.ajax({url:'form_u'+zeai.ajxext+'submitok=ajax_indata&fid=<?php echo $fid;?>',form:www_zeai_cn_FORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	});
}
ajax_inalldata_btn.onclick=function(){
	zeai.confirm('<b class="S18">确定一键全部正式入库么？</b><br>1.如果表单用户手机和OPNEID已被主库占用将忽略并更新已入库标记<br>2.只对【同意注册】的用户入库',function(){
		zeai.msg('正在入库...',{time:20});
		zeai.ajax({url:'form_u'+zeai.ajxext+'submitok=ajax_inalldata&fid=<?php echo $fid;?>',form:www_zeai_cn_FORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	});
}
zeai.listEach('.delico',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("clsid")),
		fid = parseInt(obj.getAttribute("fid"));
		zeai.confirm('确定真的要删除么（不可恢复）？',function(){
			zeai.ajax({url:'form_u'+zeai.ajxext+'submitok=ajax_del&fid='+fid+'&id='+id},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
var bg = '<?php echo $_Style['list_bg']; ?>',overbg   = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</body>
</html>

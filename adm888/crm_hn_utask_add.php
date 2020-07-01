<?php
require_once '../sub/init.php';
if ( empty($ulist) )textmsg('forbidden');
require_once 'chkUadm.php';
if($t!=1 && $t!=2 && $t!=3 && $t!=4)textmsg('参数有误','back','返回');


switch ($t) {
	//售前分配
	case 1:
		if(!in_array('crm_hn_utask_sq_add',$QXARR))exit(noauth('暂无【售前分配】权限'));
		$btnstr = '分配';
	break;
	//售前客户调配(换红娘)      1 售前待确认,2 已确认
	case 2:
		if(!in_array('crm_hn_utask_sq_mod',$QXARR))exit(noauth('暂无【客户售前调配(换红娘)】权限'));
		$btnstr = '更换';
	break;
	//客户售后分配           3 售后待确认
	case 3:
		if(!in_array('crm_hn_utask_sh_add',$QXARR))exit(noauth('暂无【客户售后分配】权限'));
		$btnstr = '分配';
	break;
	//售后客户调配(换红娘)   4 售后服务中
	case 4:
		if(!in_array('crm_hn_utask_sh_mod',$QXARR))exit(noauth('暂无【客户售后调配(换红娘)】权限'));
		$btnstr = '更换';
	break;
}


$tmeplist = explode('_',$ulist);
if(@count($tmeplist)>=1){
	foreach($tmeplist as $uid){
		if ( !ifint($uid) )textmsg('uid：'.$uid.'不存在');
		$row = $db->ROW(__TBL_USER__,"areaid,agentid","id=".$uid,"name");
		if(!$row){alert_adm("UID输入有误或不存在此客户","back");}else{
			$areaid  = dataIO($row['areaid'],'out');
			$agentid_ = intval($row['agentid']);
			//门店+地区
			if(!in_array('crm',$QXARR)){
				if($agentid_!=$session_agentid)exit(noauth('暂无【跨门店操作】权限'));
				if(!empty($session_agentareaid) && str_len($session_agentareaid)>5){
					$areaidS = explode(',',$session_agentareaid);
					$m1=$areaidS[0];$m2=$areaidS[1];$m3=$areaidS[2];
					if (ifint($m1) && ifint($m2) && ifint($m3)){
						$areaidS = $m1.','.$m2.','.$m3;
					}elseif(ifint($m1) && ifint($m2)){
						$areaidS = $m1.','.$m2;
					}elseif(ifint($m1)){
						$areaidS = $m1;
					}
					if(!strstr($areaid,$areaidS))exit(noauth('暂无【跨地区操作】权限'));
				}
			}
			//门店+地区 结束
		}
	}
}

if ($submitok == 'sendupdate'){
	if ( !ifint($hnid) )textmsg('请选择要分配的红娘','back','返回');
	$tmeplist = explode('_',$ulist);
	if(count($tmeplist)>=1){
		$row = $db->ROW(__TBL_CRM_HN__,"truename,agentid,agenttitle","id=".$hnid,'name');
		if ($row){
			$hnname       = $row['truename'];
			$hn_agentid   = $row['agentid'];
			$hn_agenttitle= $row['agenttitle'];
		}else{
			textmsg('请选择要分配的红娘','back','返回');
		}
		foreach($tmeplist as $uid){
			if ( !ifint($uid) )textmsg('uid：'.$uid.'不存在');
			$sqll="";
			$row = $db->ROW(__TBL_USER__,"nickname,agentid,agenttitle,hnid,hnname,hnid2,hnname2","id=".$uid,'name');
			if ($row){
				$nickname  = $row['nickname'];
				$agentid   = $row['agentid'];
				$agenttitle= $row['agenttitle'];
				
				$hnid_old     = $row['hnid'];
				$hnname_old   = $row['hnname'];
				$hnid_old2    = $row['hnid2'];
				$hnname_old2  = $row['hnname2'];
				
				if(!ifint($agentid) || empty($agenttitle))$sqll=",agentid=".$hn_agentid.",agenttitle='".$hn_agenttitle."'";
				//if(ifint($hn_agentid) && !empty($hn_agenttitle))$sqll=",agentid=".$hn_agentid.",agenttitle='".$hn_agenttitle."'";
				switch ($t) {
					case 1:
						$SQL = "hnid=".$hnid.",hntime=".ADDTIME.",hnname='$hnname'".$sqll;
						AddLog('【CRM】->售前红娘分配->【'.$hnname.'（id:'.$hnid.'）】');
					break;
					case 2:
						$SQL = "hnid=".$hnid.",hntime=".ADDTIME.",hnname='$hnname'".$sqll;
						AddLog('【CRM】->售前红娘更换，老红娘：【'.$hnname_old.'（id:'.$hnid_old.'）】->新红娘：【'.$hnname.'（id:'.$hnid.'）】');
					break;
					case 3:
						$SQL = "hnid2=".$hnid.",hntime2=".ADDTIME.",hnname2='$hnname'".$sqll;
						AddLog('【CRM】->售后红娘分配->【'.$hnname.'（id:'.$hnid.'）】');
					break;
					case 4:
						$SQL = "hnid2=".$hnid.",hntime2=".ADDTIME.",hnname2='$hnname'".$sqll;
						AddLog('【CRM】->售后红娘更换，老红娘：【'.$hnname_old2.'（id:'.$hnid_old2.'）】->新红娘：【'.$hnname.'（id:'.$hnid.'）】');
					break;
				}	
				$db->query("UPDATE ".__TBL_USER__." SET ".$SQL." WHERE id=".$uid);
			}			
		}
		$sussess = '分配成功!';
	}
}else{
	$sussess = '';
}
$ulist_str = str_replace("_"," , ",$ulist);
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

<?php if (empty($sussess)){?>
<style>
.table0{width:90%;margin:20px auto}
.table0 td{font-size:14px;padding:10px 5px}
.table0 td:hover{background:none}
.table0 .tdL{width:100px;font-size:14px;color:#666;background:none}
.table0 .input{width:400px}
.table0 textarea{width:400px;height:150px}
</style>
<?php }else{?>
<style>
.sussesstips{width:300px;margin:0 auto;padding-top:100px;font-size:24px;text-align:center}
</style>
<?php }?>
<body>
<?php if (!empty($sussess)){?>
    <script>
	zeai.msg('操作成功',{time:5});
	setTimeout(function(){
		parent.location.reload(true);
	},1000);
    </script>
	<div class="sussesstips"><img src="images/sussess.png"><br><br><?php echo $sussess;?><br><br>
    <a class="btn size3" href="javascript:window.parent.zeai.iframe(0);">关闭</a></div>
<?php exit;}?>
<script>
function chkform(){
	parent.zeai.confirm('<b class="S16">确定要分配此红娘么？</b>',function(){www_zeai_cn_FORM.submit();})
}
</script>
<form id="www_zeai_cn_FORM" method="post" action="<?php echo SELF; ?>">
<table class="table0">
<tr>
<td class="tdL">待<?php echo $btnstr;?>客户UID</td>
<td class="tdR"><?php echo $ulist_str; ?></td>
</tr>


<tr>
<td class="tdL">选择门店</td>
<td class="tdR">
    <?php
	if(in_array('crm',$QXARR)){
		$rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
		$total2 = $db->num_rows($rt2);
		if ($total2 > 0) {
			?>
			<select name="agentid" class="W200 size2 FL" style="margin-right:10px" onChange="zeai.openurl('<?php echo SELF;?>?t=<?php echo $t;?>&ulist=<?php echo $ulist;?>&agentid='+this.value)">
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
	}else{echo $session_agenttitle;}
    ?>
</td>
</tr>


<tr>
<td class="tdL">选择红娘</td>
<td class="tdR">
<style>
/*.hnlistbox{background-color:#fbfbfb;border:#eee 1px solid;padding:10px}
.hnlistbox div{margin:15px 0}
*/
.center{text-align:center;padding:50px 0;color:#999}

.hnlistbox li{padding:8px 10px;border-bottom:#eee 1px solid}
.hnlistbox li:hover{background-color:#f0f0f0}
.hnlistbox li:last-child{border:0}
.W300{width:350px}
</style>
<div class="hnlistbox">
	<?php
	//门店
	if (!in_array('crm',$QXARR)){
		$SQL.=" AND h.agentid=$session_agentid";
	}else{
		if(ifint($agentid))$SQL .= " AND h.agentid=$agentid";	
	}
	//门店结束
	switch ($t) {
		case 1:$SQL .= " AND FIND_IN_SET('sq',r.crmkind) ";break;
		case 2:$SQL .= " AND FIND_IN_SET('sq',r.crmkind) ";break;
		case 3:$SQL .= " AND FIND_IN_SET('sh',r.crmkind) ";break;
		case 4:$SQL .= " AND FIND_IN_SET('sh',r.crmkind) ";break;
	}	
    $rt=$db->query("SELECT h.id,h.truename,h.roletitle,h.agenttitle FROM ".__TBL_CRM_HN__." h,".__TBL_ROLE__." r WHERE h.roleid=r.id  ".$SQL." ORDER BY h.px DESC,h.id DESC");//AND h.kind='crm'
    $total = $db->num_rows($rt);
    if ($total == 0) {
		echo "<div class='center C999'>暂无售后，请联系超级管理员<br>新增<span class='ico S14'>&#xe62d;</span>顶部【系统】<span class='ico S14'>&#xe62d;</span>左侧【红娘管理】</div>";
        exit;
    } else {
        for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'num');
            if(!$rows) break;
            $hnid   = $rows[0];
            $hnname = $rows[1];
            $roletitle = dataIO($rows[2],'out');
			$agenttitle = dataIO($rows[3],'out');
            ?>
            <li>
            <input type="radio" name="hnid" id="hnid<?php echo $hnid;?>" class="radioskin" value="<?php echo $hnid;?>"><label for="hnid<?php echo $hnid;?>" class="radioskin-label"><i></i><b class="W300"><?php echo '<font class="S12 C999">'.$agenttitle.' <span class="ico S14">&#xe62d;</span></font> '.$hnname.'　<font class="S12 C999">ID：'.$hnid.'（'.$roletitle.'）</font>';?></b></label>
            </li>
            <?php		
        }
    }
    ?>
    </div>
</td>
</tr>

</table>

<br><br><br><br><div class="savebtnbox"><input type="button" value="　开始<?php echo $btnstr;?>　" class="btn size3 HONG2" onclick="chkform()"></div>

<input type="hidden" name="submitok" value="sendupdate">
<input type="hidden" name="ulist" value="<?php echo $ulist; ?>">
<input type="hidden" name="t" value="<?php echo $t; ?>">
</form>
</body>
</html>
<?php ob_end_flush();?>
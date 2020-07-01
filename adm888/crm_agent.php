<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('crm',$QXARR))exit(noauth('只有CRM超级管理员才有权限'));

if($submitok=='add_update' || $submitok=='mod_update'){
	if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【门店名称】','focus'=>'username'));
	if(str_len($title)<3)json_exit(array('flag'=>0,'msg'=>'【门店名称】至少要3位长度','focus'=>'username'));
	$bz      = dataIO($bz,'in',1000);
	$title   = dataIO($title,'in',100);
	$mate_areaid    = dataIO($mate_areaid,'in',100);
	$mate_areatitle = dataIO($mate_areatitle,'in',100);
	$content = dataIO($content,'in',10000);
	$flag=intval($flag);
	$claimnumday=abs(intval($claimnumday));
}

switch ($submitok) {
	case "add_update":
		$db->query("INSERT INTO ".__TBL_CRM_AGENT__." (areaid,areatitle,title,content,addtime,bz,flag,claimnumday) VALUES ('$mate_areaid','$mate_areatitle','$title','$content',".ADDTIME.",'$bz',$flag,$claimnumday)");
		AddLog('【CRM】->新增门店【'.$title.'】');
		json_exit(array('flag'=>1,'msg'=>'增加成功'));
	break;
	case"mod_update":
		if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'该门店不存在'));
		$db->query("UPDATE ".__TBL_CRM_AGENT__." SET areaid='$mate_areaid',areatitle='$mate_areatitle',title='$title',content='$content',bz='$bz',flag=$flag,claimnumday=$claimnumday WHERE id=".$fid);
		$db->query("UPDATE ".__TBL_USER__." SET agenttitle='$title' WHERE agentid=".$fid);
		AddLog('【CRM】->修改门店【'.$title.'】');
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case"ajax_del":
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'门店不存在或已被删除'));
		$db->query("DELETE FROM ".__TBL_CRM_AGENT__." WHERE id=".$fid);
		AddLog('【CRM】->删除门店【门店ID：'.$fid.'】');
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($id))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_CRM_AGENT__." SET px=".ADDTIME." WHERE id=".$id);
		AddLog('【CRM】->置顶门店【门店ID：'.$id.'】');
		header("Location: ".SELF);
	break;
	case"mod":
		if (!ifint($fid))alert_adm("该门店不存在","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_CRM_AGENT__." WHERE id=".$fid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$title     = dataIO($row['title'],'out');
			$uid       = intval($row['uid']);
			$flag      = intval($row['flag']);
			$content   = dataIO($row['content'],'out');
			$mate_areaid = dataIO($row['areaid'],'out');
			$mate_areatitle = dataIO($row['areatitle'],'out');
			$bz = dataIO($row['bz'],'out');
			$mate_areaid = explode(',',$mate_areaid);
			$claimnumday = intval($row['claimnumday']);
			$m1 = $mate_areaid[0];$m2 = $mate_areaid[1];$m3 = $mate_areaid[2];
		}else{
			alert_adm("该门店不存在！","-1");
		}
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select3.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:900px;margin:20px 20px 50px 20px}
.table0{min-width:900px;width:98%;margin:10px 20px 20px 20px}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
::-webkit-input-placeholder{color:#bbb;font-size:14px}
.zoom{background-color:#666;padding:20px}
.SW{width:131px}
</style>
<body>
<div class="navbox">
    <a href="crm_agent.php" class="ed">门店管理</a>
    <div class="Rsobox">
    
    </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<!---->
<div class="clear"></div> 
<?php if ($submitok == 'add' || $submitok == 'mod'){?>
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
<table class="table Mtop20  size2 cols2" style="width:1111px;margin:20px 0 20px 20px">
	<tr><th colspan="2" align="left">门店信息</th></tr>
	<tr>
		<td class="tdL">当前状态</td>
		<td class="tdR">
        <input type="checkbox" name="flag" id="flag" class="switch" value="1"<?php echo ($flag == 1)?' checked':'';?>><label for="flag" class="switch-label"><i></i><b>开启</b><b>停止</b></label>　<span class="tips">【开启】后正常使用，【关闭】后冻结该店下面的所有帐号不能登录管理</span></td>
	</tr> 
    <tr>
    <td class="tdL">门店名称</td>
    <td class="tdR"><input id="title" name="title" type="text" class="input W400 size2" maxlength="20" value="<?php echo $title;?>"><span class="tips">如：1号店，万达店等</span></td>
    </tr>
    <tr>
      <td class="tdL">绑定地区显示</td>
      <td class="tdR">
        <script>LevelMenu3('m1|m2|m3|'+nulltext+'|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>');</script>
        <div class="C999 line150">
        选择后，门店后台只能显示此工作地区会员，不限将显示全部地区
        <font class="Cf00">注：初期客户量少，建议全部选择不限地区，否则客户基本是空的</font>
        </div>
      </td>
    </tr>
    
    <tr>
    <td class="tdL">每天认领人数</td>
    <td class="tdR"><input id="claimnumday" name="claimnumday" type="text" class="input W100 size2" maxlength="6" value="<?php echo $claimnumday;?>"><span class="tips">门店员工每天从【公海用户】中认领总人数，填0将不能认领</span></td>
    </tr>
    
    <tr>
    <td class="tdL">门店介绍</td>
    <td class="tdR"><textarea name="content" rows="5" class="textarea W400 S14" id="content" ><?php echo $content;?></textarea></td>
    </tr>    
	<tr>
		<td class="tdL">备注</td>
		<td class="tdR"><textarea name="bz" rows="3" class="textarea W400 S14" id="bz" ><?php echo $bz;?></textarea></td>
	</tr> 
</table>
<input name="mate_areaid" id="mate_areaid" type="hidden" value="" />
<input name="mate_areatitle" id="mate_areatitle" type="hidden" value="" />
<?php if ($submitok == 'mod'){?>
  <input name="submitok" type="hidden" value="mod_update" />
  <input name="fid" type="hidden" value="<?php echo $fid;?>" />
<?php }else{ ?>
  <input name="submitok" type="hidden" value="add_update" />
<?php }?>
<br><br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">确认并保存</button></div>
</form>
<script>

	save.onclick = function(){
		chkform();
		var oktips ='<b class="S18">确定保存么？</b><br>保存后，请点击左侧菜单【红娘管理】新增属于此门店下的【红娘主管】，然后把红娘主管帐号密码发给他，他就可以进去创建属于此门店下的其他下属红娘（如：售前，售后，财务等），红娘主管可以给当前门店客户分配售前售后红娘<br><br>下属红娘登录后可以到【工作台】->【公海认领】认领用户进行服务，也可以到【客户管理】人工录入客户，录入即在名下';
		zeai.confirm(oktips,function(){
			zeai.ajax({url:'crm_agent'+zeai.extname,form:W_W_W_Z_E__A_I__C_N__FORM},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if (rs.flag == 1){setTimeout(function(){zeai.openurl('crm_agent.php')},1000);}
			});
		});
	}
</script>
<!--【发布 修改 结束】-->
<?php
/************************************** 【列表】 list **************************************/
exit;}else{
	?>
    <table class="table0">
    <tr>
    <td width="200" align="left" class="border0" >
    <button type="button" class="btn size2" onClick="zeai.openurl('crm_agent.php?submitok=add')"><i class="ico add">&#xe620;</i>新增门店</button>
    </td>
    <td>
    </td>
    <td width="300" align="right">&nbsp;</td>
    </tr>
    </table>
    <?php
	$SQL="";
	$Skeyword = trimm($Skeyword);
	if (!empty($Skeyword))$SQL = " AND ( title LIKE '%".dataIO(trimm($Skeyword),'in')."%' ) ";
	
	$rt = $db->query("SELECT id,areatitle,title,flag,addtime,claimnumday FROM ".__TBL_CRM_AGENT__." WHERE 1=1 ".$SQL."  ORDER BY px DESC,id DESC LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无门店<br><a class='aHUANGed' href='javascript:zeai.openurl(\"crm_agent.php?submitok=add\")'>新增门店</a></div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
		?>
		<form id="www_zeai_cn_FORM">
		<table class="tablelist">
		<tr>
		<th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label" style="display:none"><i class="i1"></i></label></th>
		<th width="80" align="center">门店ID</th>
		<th width="50" align="center">置顶</th>
		<th width="200" align="center">门店名称</th>
		<th width="150" align="center">限定地区</th>
		<th width="100" align="center">每天认领人数</th>
		<th width="100" align="center">名下员工数</th>
		<th width="120" align="center">名下客户数</th>
		<th align="center">&nbsp;</th>
		<th width="70" align="center">创建时间</th>
		<th width="100" align="center">状态</th>
		<th width="60" align="center">修改</th>
		<th width="60" align="center">删除</th>
		</tr>
		<?php
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$id      = $rows['id'];
			$addtime = YmdHis($rows['addtime'],'Ymd');
			$title   = dataIO($rows['title'],'out');
			$areatitle   = dataIO($rows['areatitle'],'out');
			$flag        = intval($rows['flag']);
			$claimnumday = intval($rows['claimnumday']);
			//
			if(!empty($Skeyword)){
				$title = str_replace($Skeyword,'<font class="Cf00 B">'.$Skeyword.'</font>',$title);
			}
			$unum1 = $db->COUNT(__TBL_CRM_HN__,"agentid=".$id);
			$unum2 = $db->COUNT(__TBL_USER__,"agentid=".$id);
			$flag_str = ($flag==1)?'<span class="C090">正常</span>':'<span class="C999">已停止</span>';
		?>
		<tr id="tr<?php echo $id;?>">
		<td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
		<td width="80" align="center"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
		<td width="50" align="center">
        <a href="<?php echo "crm_agent.php?id=".$id; ?>&submitok=ding" class="topico" title="置顶"></a>
        </td>
		<td width="200" align="center" class="S14"><?php echo $title;?></td>
		<td width="150" align="center" class="S14"><?php echo $areatitle;?></td>
		<td width="100" align="center" class="S14"><?php echo $claimnumday;?></td>
		<td width="100" align="center" class="S14"><?php echo $unum1; ?></td>
		<td width="120" align="center" class="S14"><?php echo $unum2; ?></td>
		<td align="center">&nbsp;</td>
		<td width="70" align="center" class="padding15"><?php echo $addtime;?></td>
		<td width="100" align="center"><?php echo $flag_str;?></td>
		<td width="60" align="center">
		  <a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>"></a>
		</td>
		<td width="60" align="center"><a clsid="<?php echo $id; ?>" class="delico" title='删除'></a></td>
		</tr>
		<?php } ?>
		<tfoot><tr>
		<td colspan="13">
		<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label" style="display:none"><i class="i1"></i></label>　
		<input type="hidden" name="submitok" id="submitok" value="" />
		<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
		</td>
		</tr></tfoot>
		</table>
</form>
		<script>
		var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
		zeai.listEach('.editico',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.openurl('crm_agent.php?submitok=mod&fid='+id);
			}
		});
		zeai.listEach('.delico',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.confirm('★请慎重★　确定真的要删除么？',function(){
					zeai.ajax({url:'crm_agent.php?submitok=ajax_del&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.bbsadm',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				var title=obj.getAttribute("title2");
				zeai.iframe('【'+decodeURIComponent(title)+'】评论管理','party_bbs.php?fid='+id,700,600);
			}
		});
		</script>
		<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }}?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
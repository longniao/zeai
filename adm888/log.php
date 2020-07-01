<?php
ob_start();
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('log',$QXARR))exit(noauth());
switch ($submitok) {
	case"alldel":
		if(!in_array('logdel',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【删除】权限'));
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $value){$v=intval($value);$db->query("DELETE FROM ".__TBL_LOG__." WHERE id=".$v);}
			$list = (is_array($tmeplist))?implode(',',$tmeplist):'';
			AddLog('批量删除日志，日志ID列表【'.$list.'】');
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"clear":
		if(!in_array('logdel',$QXARR))json_exit(array('flag'=>0,'msg'=>'暂无【删除】权限'));
		$db->query("DELETE FROM ".__TBL_LOG__);
		AddLog('清空日志');
		json_exit(array('flag'=>1,'msg'=>'清空成功'));
	break;
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
<style>
.tablelist{min-width:900px;margin:0 20px 50px 20px}
.table0{min-width:1300px;width:98%;margin:0 20px 20px 20px}
.table0 form{float:left;}
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
</style>
<body>
<div class="navbox">
    <a href="loveb.php" class="ed">后台操作日志<?php echo '<b>'.$db->COUNT(__TBL_LOG__).'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0">
    <tr>
    <td align="left" class="border0 S14">
    
    
    
    <div>
     <form name="ZEAI_CN__form5" method="get" action="<?php echo SELF; ?>" style="margin-right:20px">
    <!--按门店查询-->
    <?php if(in_array('crm',$QXARR)){?>
        <?php
        $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
        $total2 = $db->num_rows($rt2);
        if ($total2 > 0) {
            ?>
            <select name="agentid" class="W150 size2 picmiddle"><!-- onChange="zeai.openurl('<?php echo SELF;?>?agentid='+this.value)"-->
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
            <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
            <?php
        }
    }
    ?>
    </form>
    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>" style="margin-right:20px">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="20" class="input size2 W150" placeholder="按管理员和红娘用户名" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="Skeyword2" value="<?php echo $Skeyword2;?>">
        <input type="hidden" name="Skeyword3" value="<?php echo $Skeyword3;?>">
        <input type="hidden" name="Skeyword4" value="<?php echo $Skeyword4;?>">
        <input type="hidden" name="sDATE1" value="<?php echo $sDATE1;?>">
        <input type="hidden" name="sDATE2" value="<?php echo $sDATE2;?>">
    </form>
    <form name="ZEAI_CN__form2" method="get" action="<?php echo SELF; ?>" style="margin-right:20px">
        <input name="Skeyword2" type="text" id="Skeyword2" maxlength="25" class="input size2 W150" placeholder="按会员UID/昵称" value="<?php echo $Skeyword2; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="Skeyword1" value="<?php echo $Skeyword1;?>">
        <input type="hidden" name="Skeyword4" value="<?php echo $Skeyword4;?>">
        <input type="hidden" name="Skeyword3" value="<?php echo $Skeyword3;?>">
        <input type="hidden" name="sDATE1" value="<?php echo $sDATE1;?>">
        <input type="hidden" name="sDATE2" value="<?php echo $sDATE2;?>">
    </form>  
	</div>
    <div class="clear"></div>
    <div style="margin-top:10px">
    <form name="ZEAI_CN__form4" method="get" action="<?php echo SELF; ?>" style="margin-right:20px">
        <input name="Skeyword4" type="text" id="Skeyword4" maxlength="25" class="input size2 W150" placeholder="按推广员ID/昵称" value="<?php echo $Skeyword4; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="Skeyword1" value="<?php echo $Skeyword1;?>">
        <input type="hidden" name="Skeyword2" value="<?php echo $Skeyword2;?>">
        <input type="hidden" name="Skeyword3" value="<?php echo $Skeyword3;?>">
        <input type="hidden" name="sDATE1" value="<?php echo $sDATE1;?>">
        <input type="hidden" name="sDATE2" value="<?php echo $sDATE2;?>">
    </form>  
    <form name="ZEAI_CN__form3" method="get" action="<?php echo SELF; ?>" style="margin-right:20px">
        <input name="Skeyword3" type="text" id="Skeyword3" maxlength="50" class="input size2 W150" placeholder="按日志内容" value="<?php echo $Skeyword3; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="Skeyword1" value="<?php echo $Skeyword1;?>">
        <input type="hidden" name="Skeyword2" value="<?php echo $Skeyword2;?>">
        <input type="hidden" name="Skeyword4" value="<?php echo $Skeyword4;?>">
        <input type="hidden" name="sDATE1" value="<?php echo $sDATE1;?>">
        <input type="hidden" name="sDATE2" value="<?php echo $sDATE2;?>">
        <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
    </form>  
    <form name="ZEAI_CN__form3" method="get" action="<?php echo SELF; ?>" style="margin-left:20px">
    	按日期：
        <input name="sDATE1" id="sDATE1" type="text"  class="input size2 W100" value="<?php echo (empty($sDATE1))?'':$sDATE1; ?>" size="10" maxlength="10" autocomplete="off">
        <b>～</b> 
        <input name="sDATE2" id="sDATE2" type="text"  class="input size2 W100" value="<?php echo (empty($sDATE2))?'':$sDATE2; ?>" size="10" maxlength="10" autocomplete="off">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="Skeyword1" value="<?php echo $Skeyword1;?>">
        <input type="hidden" name="Skeyword2" value="<?php echo $Skeyword2;?>">
        <input type="hidden" name="Skeyword3" value="<?php echo $Skeyword3;?>">
        <input type="hidden" name="Skeyword4" value="<?php echo $Skeyword4;?>">
        <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 筛选</button>
    </form>
	</div>
    </td>
    </tr>
</table>
<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
if(!empty($Skeyword)){
	$SQL .= " AND ( username LIKE '%".$Skeyword."%' )";
}
if (ifint($Skeyword2)){
	$SQL .= " AND (uid=$Skeyword2) ";
}elseif(!empty($Skeyword2)){
	$SQL .= " AND ( content LIKE '%".$Skeyword2."%' )";
}
if(!empty($Skeyword3)){
	$SQL .= " AND ( content LIKE '%".$Skeyword3."%' )";
}
if (ifint($Skeyword4)){
	$SQL .= " AND (tguid=$Skeyword4) ";
}elseif(!empty($Skeyword2)){
	$SQL .= " AND ( content LIKE '%".$Skeyword4."%' )";
}

if(!empty($sDATE1)){
	$sDATE1 = strtotime($sDATE1.' 00:00:01');
	$SQL .= " AND ( addtime >= '$sDATE1' )";
}
if(!empty($sDATE2)){
	$sDATE2 = strtotime($sDATE2.' 23:59:59');
	$SQL .= " AND ( addtime <= '$sDATE2' )";
}


//门店+地区
if ($session_kind=='crm' && !in_array('crm',$QXARR)){
	//$SQL .= getAgentSQL();
	$SQL.=" AND agentid=$session_agentid ";
}

//超管可以按门店搜索
if(ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND agentid=$agentid";

$rt = $db->query("SELECT * FROM ".__TBL_LOG__." WHERE 1=1 ".$SQL." ORDER BY id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60">ID</th>
    <th width="80" align="left">操作人</th>
    <th width="30" align="left">&nbsp;</th>
    <th width="220" align="left">操作对象</th>
    <th width="160">操作时间</th>
	<th align="left" >操作内容</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$ustr='';
		$id = $rows['id'];
		$uid = $rows['uid'];
		$tguid = $rows['tguid'];
		$username = $rows['username'];
		$content = dataIO($rows['content'],'out');
		if(ifint($uid)){
			$row = $db->ROW(__TBL_USER__,"nickname,grade,sex","id=".$uid,'num');
			if($row){
				$nickname= $nickname= dataIO($row[0],'out');;$grade= $row[1];$sex= $row[2];
				$ustr = uicon($sex.$grade);
				$ustr.= '<span class="middle">';
				$ustr.= $nickname."　<font class='S12 C999'>(uid：".$uid.")</font>";
				$ustr.= '</span>';
			}
		}
		if(ifint($tguid)){
			$row = $db->ROW(__TBL_TG_USER__,"uname,nickname","id=".$tguid,'num');
			if($row){
				$uname= $nickname= dataIO($row[0],'out');$nickname= dataIO($row[1],'out');
				$nickname = (empty($nickname))?$uname:$nickname;
				$ustr = '<span class="middle">';
				$ustr.= $nickname."　<font class='S12 C999'>(id：".$tguid.")</font>";
				$ustr.= '</span>';
			}
		}
		$username = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$username);
		$content = str_replace($Skeyword2,"<font color=red><b>".$Skeyword2."</b></font>",$content);
		$content = str_replace($Skeyword3,"<font color=red><b>".$Skeyword3."</b></font>",$content);
		$content = str_replace('->',' <img src="images/d2.gif"> ',$content);
		$addtime = YmdHis($rows['addtime']);
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="60" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="80" align="left"><?php echo $username; ?></td>
        <td width="30" align="left"><?php if (!empty($ustr)){?><img src="images/d2.gif"><?php }?></td>
        <td width="220" align="left"><?php echo $ustr; ?></td>
      <td width="160" align="left" class="S12"><?php echo $addtime;?></td>
      <td align="left"><?php echo $content; ?></td>
	</tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="7">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <?php if ($session_kind=='adm'){?>
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" class="btn size2 HEI" onClick="clearlog();">清空</button>
    <?php }?>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
</form>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
<?php if ($session_kind=='adm'){?>
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'log'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
function clearlog() {
	zeai.confirm('确定要清空全部日志么？',function(){
		zeai.ajax({url:'log'+zeai.ajxext+'submitok=clear'},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	});
}
<?php }?>
</script>
<script src="js/zeai_tablelist.js"></script>
<?php }?>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem: '#sDATE1'});
laydate.render({elem: '#sDATE2'});
</script>
<br><br><br>
<?php require_once 'bottomadm.php';ob_end_flush(); ?>
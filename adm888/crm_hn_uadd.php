<?php
require_once '../sub/init.php';
if ( empty($ulist) )textmsg('forbidden');
require_once 'chkUadm.php';

if ($submitok == 'sendupdate'){
	if ( !ifint($hnid) )textmsg('请选择要分配的红娘','back','返回');
	$tmeplist = explode('_',$ulist);
	if(count($tmeplist)>=1){
		$row = $db->ROW(__TBL_CRM_HN__,"truename","id=".$hnid,'num');
		if ($row){
			$hnname= $row[0];
		}else{
			textmsg('请选择要分配的红娘','back','返回');
		}
		foreach($tmeplist as $uid){
			if ( !ifint($uid) )textmsg('uid：'.$uid.'不存在');
			$db->query("UPDATE ".__TBL_USER__." SET hnid=".$hnid.",hnname='$hnname' WHERE id=".$uid);
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
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
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
    <script>parent.location.reload(true);</script>
	<div class="sussesstips"><img src="images/sussess.png"><br><br><?php echo $sussess;?><br><br>
    <a class="btn size3" href="javascript:window.parent.zeai.iframe(0);">关闭</a></div>
<?php exit;}?>
<script>
function chkform(){
	parent.zeai.confirm('确定要分配么？',function(){zeai.msg('正在分配中...',{time:100});www_zeai_cn_FORM.submit();})
}
</script>
<form id="www_zeai_cn_FORM" method="post" action="<?php echo SELF; ?>">
<table class="table0">
<tr>
<td class="tdL">待分配会员UID</td>
<td class="tdR"><?php echo $ulist_str; ?></td>
</tr>
<tr>
<td class="tdL">选择红娘</td>
<td class="tdR">
<style>
.hnlistx{background-color:#fbfbfb;border:#eee 1px solid;padding:10px}
.hnlistx div{margin:15px 0}
</style>
<div class="hnlistx">
	<?php
    $rt=$db->query("SELECT id,truename,roletitle FROM ".__TBL_CRM_HN__." WHERE kind='crm' ORDER BY px DESC,id DESC");
    $total = $db->num_rows($rt);
    if ($total == 0) {
        echo "<br><br><br<center>暂无红娘</center><br><br>";exit;
    } else {
        for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'num');
            if(!$rows) break;
            $hnid   = $rows[0];
            $hnname = $rows[1];
            $roletitle = dataIO($rows[2],'out');
            ?>
            <div><?php echo $truename;?>
            <input type="radio" name="hnid" id="hnid<?php echo $hnid;?>" class="radioskin" value="<?php echo $hnid;?>"><label for="hnid<?php echo $hnid;?>" class="radioskin-label"><i></i><b class="W300 S18"><?php echo $hnname.' <font class="S12 C999">红娘编号：'.$hnid.'（'.$roletitle.'）</font>';?></b></label>
            </div>
            <?php		
        }
    }
    ?>
    </div>
</td>
</tr>
<tr>
  <td height="60" colspan="2" class="center"><input type="button" value="　开始分配　" class="btn size3 HONG2" onclick="chkform()"></td>
</tr>
</table>
<input type="hidden" name="submitok" value="sendupdate">
<input type="hidden" name="ulist" value="<?php echo $ulist; ?>">
</form>
</body>
</html>
<?php ob_end_flush();?>
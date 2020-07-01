<?php
require_once '../sub/init.php';
if(!ifint($cook_uid))alert('请用手机登录后重新扫码签到',HOST.'/m1/login.php');
if(!ifdate(YmdHis($time),'Y-m-d H:i:s'))alert('zeai_error_time','-1');

require_once ZEAI.'sub/conn.php';
require_once ZEAI.'m1/header.php';
if($submitok == 'ewm' && ifint($fid)){
	
	require_once ZEAI.'my_chk_u.php';
	
	$row = $db->ROW(__TBL_PARTY__,"title,refresh_time,flag","id=".$fid,"num");
	if (!$row)alert("活动不存在或已被删除","-1");
	$title   = dataIO($row[0],'out');
	$refresh_time = $row[1];
	$flag    = $row[2];
	
	if ($flag!=2){
		alert("活动还没开始哦","../?z=party&e=detail&a=".$fid);
	}

	$row = $db->ROW(__TBL_PARTY_USER__,"id","uid=".$cook_uid." AND fid=".$fid);
	if (!$row){
		alert("您还没有报名，请先报名后再签到","../?z=party&e=detail&a=".$fid);
	}
	
	if($time!=$refresh_time)alert("二维码已失效或过期。","-1");
	
	$diff=ADDTIME-$time;
	
	if($diff>0){
		alert("二维码已失效或过期。。","-1");
	}

	$row = $db->ROW(__TBL_PARTY_SIGN__,"addtime","uid=".$cook_uid." AND fid=".$fid);
	if ($row){
		$addtime= $row[0];
	}else{
		$db->query("INSERT INTO ".__TBL_PARTY_SIGN__."  (fid,uid,addtime) VALUES ($fid,'$cook_uid',".ADDTIME.")");
		$db->query("UPDATE ".__TBL_PARTY__." SET signnum=signnum+1 WHERE id=".$fid);
		$addtime= ADDTIME;
	}
	
	$mc = $db->COUNT(__TBL_PARTY_SIGN__," addtime<".$addtime);
	$mc = $mc+1;
?>
<style>
body{background-color:#f2f2f2}
.party_sign2{width:80%;background-color:#fff;border-radius:20px;box-sizing:border-box;padding-top:120px}
.party_sign2 i.ico{font-size:60px;color:#5FB878;display:block;margin:0 auto 30px auto}
.party_sign2 .timestyle{display:inline-block;font-size:18px;color:#f70;border-radius:3px;padding:5px 5px;height:18px;line-height:18px;text-align:center;background-color:#fff}
.party_sign2 h5{color:#999;margin-top:10px;font-size:14px}

.party_sign2{padding:0 0 30px 0;margin:20px auto}
.party_sign2 img{width:100%;margin-top:20px;display:block;margin:0 auto 5px auto;border-radius:20px 20px 0 0}
.party_sign2 a.btn{margin:10px auto}
.party_sign2 h3{text-align:center;margin-bottom:10px;color:#999}
.party_sign2 {line-height:200%;font-size:14px;text-align:center}
</style>
<div class="party_sign2 success">
    <br><br>
    <i class="ico">&#xe60d;</i>
    <h2 class="B"><?php echo $nickname;?> 恭喜您签到成功</h2>
    <h5>第<font class="Cf00 S24 FArial" style="vertical-align:middle;font-style:italic"> <?php echo $mc;?> </font>位</h5>
    <br><br>
    <h3><?php echo $title;?></h3>
    <a href="../?z=party&e=detail&a=<?php echo $fid;?>" class="btn size4 BAI W80_ yuan">进入此活动页面</a>
    <a href="../?z=index" class="btn size4 BAI W80_ yuan">进入缘分大厅</a>
</div>
<?php }?>
</body>
</html>
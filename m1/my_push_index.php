<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
$currfields = "birthday,loveb,photo_f,photo_s,heigh,pay,refresh_time,dataflag";
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=my_push_index';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
$data_birthday     = $row['birthday'];
$data_loveb        = $row['loveb'];
$data_photo_f      = $row['photo_f'];
$data_photo_s      = $row['photo_s'];
$data_heigh        = $row['heigh'];
$data_pay          = $row['pay'];
$data_refresh_time = $row['refresh_time'];
$data_dataflag     = $row['dataflag'];
if ($data_photo_f  == 1 && $data_photo_s<>'' && !empty($data_heigh) && !empty($data_pay) && $data_birthday<>'0000-00-00'){
	$ifshow = true;
}else{
	$ifshow = false;
}
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员</div>";
if($submitok == 'ajax_sex'){
	$sql = "";
	if ($t == 1){
		$sql = " AND sex=1";
	}elseif($t == 2){
		$sql = " AND sex=2";
	}
	$rt=$db->query("SELECT id,sex,grade,nickname,photo_s,photo_f FROM ".__TBL_USER__." WHERE birthday<>'0000-00-00' AND heigh>0 AND flag=1 AND dataflag=1 ".$sql." ORDER BY refresh_time DESC LIMIT 10");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,"name");
			if(!$rows)break;
			$uid           = $rows['id'];
			$sex           = $rows['sex'];
			$grade         = $rows['grade'];
			$nickname      = dataIO($rows['nickname'],'out');
			$nickname      = urldecode($nickname);
			$photo_s       = $rows['photo_s'];
			$photo_f       = $rows['photo_f'];
			$birthday      = $rows['birthday'];
			$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up_2domain'].'/'.getpath_smb($photo_s,'m'):'/images/photo_m'.$sex.'.png';
			$ifown = ($uid == $cook_uid)?' <font>(我)</font>':'';
			if ($uid == $cook_uid){
				$ifown = ' <font>(我)</font>';
				$ifed  = ' class="my"';
			}else{
				$ifown = '';$ifed  = '';
			}
	?>
	<dl<?php echo $ifed; ?>><dt>第<font><?php echo $i; ?></font>名</dt><dd><a uid='<?php echo $uid;?>'></i><?php echo uicon($sex.$grade).$nickname; ?><?php echo $ifown; ?></a></dd></dl>
	<?php }}else{echo $nodatatips ;}?><script>my_push_indexAfn();</script>	
<?php exit;}elseif($submitok=="ajax_modupdate"){
	if ($ifshow){
		$IndexPushLoveb = intval(abs($_VIP['push_index']));
		if ($IndexPushLoveb > $data_loveb) {
			json_exit(array('flag'=>'noloveb','msg'=>$_ZEAI['loveB']."账户余额不足".$IndexPushLoveb.$_ZEAI['loveB']));
		} else {
			$db->query("UPDATE ".__TBL_USER__." SET loveb=loveb-$IndexPushLoveb,refresh_time=".ADDTIME." WHERE id=".$cook_uid);
			//爱豆清单入库
			$db->AddLovebRmbList($cook_uid,'置顶排名',-$IndexPushLoveb,'loveb',10);		
			json_exit(array('flag'=>1,'msg'=>'恭喜您置顶成功！'));
		}
	}else{
		if ($data_photo_f == 0 || empty($data_photo_s)){
			json_exit(array('flag'=>0,'msg'=>'请先上传形象照'));
		}else{
			json_exit(array('flag'=>0,'msg'=>'请完善基本资料'));
		}
	}
}
$mc = $db->COUNT(__TBL_USER__,"birthday<>'0000-00-00' AND heigh>0 AND flag=1 AND dataflag=1 AND refresh_time>".$data_refresh_time);
$mc = $mc+1;
?>
<link href="m1/css/my_push_index.css?8" rel="stylesheet" type="text/css" />
<style>.my_push_index .mpi section div:last-child:before{content:'当前<?php echo $_ZEAI['loveB']; ?>'}</style>
<i class="ico goback" id="ZEAIGOBACK-my_push_index" style="z-index:10;color:#000">&#xe602;</i>
<?php
$mini_title = '置顶排名';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
$a = (empty($a))?'s0':$a;
?>
<div class="submain my_push_index">
	<?php if (!$ifshow){ ?>
    <div class="mpi">
    	<i class="ico sorrympi">&#xe61f;</i>
        <h3>您无法使用此功能</h3>
        <font class="S14 C999">1.资料不完善 <br>2.形象照未上传 <br>3.资料或形象照审核中<br><br>
		<a class="aQINGed" onclick="page({g:'m1/my_info'+zeai.ajxext+'a=data',y:'my_push_index',l:'my_info'})">我要完善资料</a>　<a class="aQINGed" onclick="page({g:'m1/my_info'+zeai.ajxext+'a=data',y:'my_push_index',l:'my_info'})">我要上传照片</a></font><br><br>
    </div>
	<?php }else{?>
        <div class="mpi">
            <section>
                <div><font>第</font><span class="Cf60" id="my_push_idxmc"><?php echo $mc; ?></span><font>名</font></div>
                <div><span id="my_push_idxlovb"><?php echo $data_loveb; ?></span><font>个</font></div>
            </section>
            <input type="button" value="　提交申请　" class="btn size4 LV2" id="my_push_index_sbmtbtn">
            <div class="sbmtips">请不要连续提交，提交一次就扣除<?php echo $_VIP['push_index']; ?></div>
            <div class="Clist">
                <div>● 申请一次花费<?php echo $_ZEAI['loveB']; ?><?php echo $_VIP['push_index']; ?>个，直到其他会员将你顶下去，否则你将永远排第一</div>
                <div>● 无形象照或个人基本资料不完整的会员申请后，将不显示　　<a onclick="page({g:'m1/my_info'+zeai.ajxext+'a=data',y:'my_push_index',l:'my_info'})" class="aQING">上传照片</a>　<a onclick="page({g:'m1/my_info'+zeai.ajxext+'a=data',y:'my_push_index',l:'my_info'})" class="aQING">完善资料</a></div>
            </div>
        </div>
    <?php }?>
	<br>
	<div class="mpi">
        <div class="linebox"><div class="line"></div><div class="title BAI">本站会员排名TOP10</div></div><br>
        <div class="tabmenu tabmenu_3 tabmenuBAI2" id="my_push_index_nav">
            <li<?php echo ($a == 1)?' class="ed"':''; ?> data="m1/my_push_index.php?submitok=ajax_sex&t=0" id="my_push_s0btn"><span>全部会员</span></li>
            <li<?php echo ($a == 2)?' class="ed"':''; ?> data="m1/my_push_index.php?submitok=ajax_sex&t=1" id="my_gift_s1btn"><span>男会员</span></li>
            <li<?php echo ($a == 3)?' class="ed"':''; ?> data="m1/my_push_index.php?submitok=ajax_sex&t=2" id="my_gift_s2btn"><span>女会员</span></li>
            <i></i>
        </div>
        <dl class="tbody"><dt>名次</dt><dd>竞价人</dd></dl>
        <div id="my_push_index_list"></div><br><br><br><br>
   </div>
    <script src="m1/js/my_push_index.js"></script>
	<script>
		var push_indexnum=<?php echo $_VIP['push_index'];?>;
		mini_title.id='';
		ZeaiM.tabmenu.init({obj:my_push_index_nav,showbox:my_push_index_list});
		setTimeout(function(){my_push_s0btn.click();},200);
		if(!zeai.empty(o('my_push_index_sbmtbtn')))my_push_index_sbmtbtn.onclick=my_push_index_sbmtbtnFn;
    </script>
</div>
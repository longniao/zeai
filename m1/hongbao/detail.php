<?php 
require_once '../../sub/init.php';
header("Cache-control: private");
require_once ZEAI.'sub/conn.php';
require_once 'hongbao_init.php';

if (!ifint($fid))callmsg("信息不存在","-1");
if ($submitok == 'ajax_add_qiang'){
	if(!ifint($cook_uid))exit(json_encode(array('flag'=>'nologin','msg'=>'亲！请您先登录～')));
	$row = $db->NUM($cook_uid,'sex,areaid,birthday,heigh,dataflag,money,openid');
	if (!$row){exit(json_encode(array('flag'=>'nologin','msg'=>'亲！请您先登录～')));}else{
		$data_sex    = $row[0];
		$data_areaid = $row[1];
		$data_age    = getage($row[2]);
		$data_heigh  = $row[3];
		$data_dataflag = $row[4];
		$data_money  = $row[5];
		$data_openid = $row[6];
	}
	//
	$row = $db->ROW(__TBL_HONGBAO_USER__,"id","fid=".$fid." AND uid=".$cook_uid);
	if ($row)exit(json_encode(array('flag'=>'ed','msg'=>'亲！您已经抢过了～')));
	$row = $db->ROW(__TBL_HONGBAO__,"sex,areaid,age1,age2,heigh1,heigh2,ruleout,kind,amount,num,money,flag,uid","id=".$fid." AND flag=1 AND (kind=1 OR kind=2)","num");
	if ($row){
		$hb_sex    = $row[0];
		$hb_areaid = $row[1];
		$hb_age1   = $row[2];
		$hb_age2   = $row[3];
		$hb_heigh1 = $row[4];
		$hb_heigh2 = $row[5];
		$hb_ruleout= $row[6];
		$hb_kind   = $row[7];
		$hb_amount = $row[8];
		$hb_num    = $row[9];
		$hb_money  = $row[10];
		$hb_flag   = $row[11];
		$hb_uid    = $row[12];
		if ($hb_ruleout == 1){
			$rt=$db->query("SELECT id FROM ".__TBL_HONGBAO__." WHERE uid=".$hb_uid);
			WHILE ($rows = $db->fetch_array($rt,'num')){
				$joned = $db->COUNT(__TBL_HONGBAO_USER__,"fid=".$rows[0]." AND uid=".$cook_uid);
				if ($joned > 0){
					exit(json_encode(array('flag'=>'ed','msg'=>'已经抢过他红包了～新人可抢')));
					break;
				}
			}
		}
		//条件
		$mateflag = true;
		if ($data_dataflag != 1)$mateflag = false;
		if ( ($hb_sex == 1 || $hb_sex == 2) && $data_sex != $hb_sex )$mateflag = false;
		if (ifint($hb_age1) && $data_age < $hb_age1 )$mateflag = false;
		if (ifint($hb_age2) && $data_age >= $hb_age2 )$mateflag = false;
		if (ifint($hb_heigh1) && $data_heigh < $hb_heigh1 )$mateflag = false;
		if (ifint($hb_heigh2) && $data_heigh >= $hb_heigh2 )$mateflag = false;
		/*
		if (!empty($hb_areaid)){
			if (empty($data_areaid)){
				$mateflag = false;
			}elseif(!strstr($hb_areaid,$data_areaid)){
				$mateflag = false;
			}
			
		}
		*/
		if (!empty($hb_areaid) && str_len($hb_areaid)>4){
			$hb_area_arr   = explode(',',$hb_areaid);
			$data_area_arr = explode(',',$data_areaid);
			$hb_area_num   = count($hb_area_arr);
			$data_area_num = count($data_area_arr);
			switch ($hb_area_num) {
				case 1:
					if ($hb_area_arr[0] != $data_area_arr[0])$mateflag = false;
				break;
				case 2:
					if ($hb_area_arr[0] != $data_area_arr[0] && $hb_area_arr[1] != $data_area_arr[1])$mateflag = false;
				break;
				case 3:
					if ($hb_areaid != $data_areaid)$mateflag = false;
				break;
			}
		}
		
		if (!$mateflag){
			$retarr = array('flag'=>'nomatch','msg'=>'你不符合领红包的条件～');
		}else{
			if ($hb_flag != 1){
				$retarr = array('flag'=>'expire','msg'=>'已抢光或已过期～');
			}else{
				//统计已抢人数
				$data_num = $db->COUNT(__TBL_HONGBAO_USER__,"fid=".$fid);
				$endtotalnum   = intval($hb_num - $data_num);
				if ($endtotalnum <= 0){
					$db->query("UPDATE ".__TBL_HONGBAO__." SET flag=2 WHERE id=".$fid);
					$retarr = array('flag'=>'expire','msg'=>'已抢光或已过期～');
				}else{
					if ($hb_kind == 1){//运气
						//统计已抢金额
						$rt=$db->query("SELECT SUM(money) FROM ".__TBL_HONGBAO_USER__." WHERE fid=".$fid);
						$row = $db->fetch_array($rt);
						$endtotalmoney = $hb_amount - intval($row[0]);
						//
						require_once 'zeai_hongbao.php';
						$bonus_total = $endtotalmoney;  
						$bonus_count = $endtotalnum;  
						$bonus_max   = ($endtotalnum > 1)?intval($endtotalmoney/$endtotalnum + 1):$endtotalmoney;//最大值要大于平均值  
						$bonus_min   = 1;
						$bonus_ARR   = getBonus($bonus_total,$bonus_count,$bonus_max,$bonus_min);  
						//
						$money = intval($bonus_ARR[array_rand($bonus_ARR)]);
					}elseif($hb_kind == 2){//定额
						$money = $hb_amount;
					}
					$db->query("INSERT INTO ".__TBL_HONGBAO_USER__." (fid,uid,money,addtime) VALUES ($fid,$cook_uid,$money,$ADDTIME)");
					//money_list
					$endnum  = $money + $data_money;
					$db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$cook_uid);
					$content = '抢红包';
					$db->AddLovebRmbList($cook_uid,$content,$money,'money',14);
					//weixin_mb
					if (!empty($data_openid)){
						$first  = $username."您好，您的余额账户有变动：";
						$remark = $content."，查看详情";
						$url    = HOST."/?z=my&e=my_money";
						wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid.'&money='.$money.'&money_total='.$endnum.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					$retarr = array('flag'=>1,'moeny'=>$money);
				}
			}
		}
	}else{
		$retarr = array('flag'=>'nodata','msg'=>'红包不存在～');
	}
	exit(json_encode($retarr));
}elseif($submitok == 'ajax_add_shang'){
	if(!ifint($cook_uid))exit(json_encode(array('flag'=>'nologin','msg'=>'亲！请您先登录～')));
	$row = $db->NUM($cook_uid,'money,openid');
	if (!$row){exit(json_encode(array('flag'=>'nologin','msg'=>'亲！请您先登录～')));}else{
		$data_money  = $row[0];
		$data_openid = $row[1];
	}
	$row = $db->ROW(__TBL_HONGBAO__,"uid,money","id=".$fid." AND flag=1 AND kind=3","num");
	if (!$row){
		exit(json_encode(array('flag'=>'forbidden','msg'=>'红包不存在～')));
	}else{
		$uid   = $row[0];
		$money = $row[1];
		if($cook_uid == $uid)exit(json_encode(array('flag'=>'noeaning','msg'=>'操作自己有意义么～')));
		if($money > $data_money && $money>0)exit(json_encode(array('flag'=>'nomoney','msg'=>'账户余额不足'.$money.'元～','jumpurl'=>HOST.'/?z=my&e=my_money')));
		exit(json_encode(array('flag'=>1)));
	}
}elseif($submitok == 'add_shang'){
	$row = $db->ROW(__TBL_HONGBAO__,"money","id=".$fid." AND flag=1 AND kind=3",'num');
	if (!$row){callmsg("红包不存在～","-1");}else{
		$data_money_str = ($row[0] == 0)?1:$row[0];
	}
}elseif($submitok == 'add_shang_update'){
	if(!ifint($cook_uid))callmsg("亲！请您先登录～","-1");
	$amount = abs(intval($amount));
	$row = $db->NUM($cook_uid,'money,openid');
	if (!$row){callmsg("亲！请您先登录～","-1");}else{
		$data_money  = $row[0];
		$data_openid = $row[1];
	}
	$row = $db->ROW(__TBL_HONGBAO__,"uid,money,kind","id=".$fid." AND flag=1 AND kind=3","num");
	if (!$row){
		callmsg("红包不存在～","-1");
	}else{
		$uid      = $row[0];
		$hb_money = $row[1];
		$hb_kind  = $row[2];
		$hb_money = ($hb_money <= 0)?1:$hb_money;
		if ($amount <= 0)callmsg("至少发".$hb_money."元给我哦～","-1");
		if ($hb_money > $amount)callmsg("老板，至少发".$hb_money.'元给我哦～',"-1");
		if($amount > $data_money && $amount>0){
			$ifok = 0;
			$ifok_str = "账户余额不足".$amount."元～";
		}else{
			//处理我本人
			//////////////////
			$endnum  = intval($data_money - $amount);
			$db->query("UPDATE ".__TBL_USER__." SET money=".$endnum." WHERE id=".$cook_uid);
			$content = dataIO($content,'in',250);
			$db->query("INSERT INTO ".__TBL_HONGBAO_USER__." (fid,uid,money,addtime,content,kind) VALUES ($fid,$cook_uid,$amount,$ADDTIME,'$content',$hb_kind)");
			//money_list
			$content = '发布红包(打赏)';
			$db->AddLovebRmbList($cook_uid,$content,-$amount,'money',13);		

			//weixin_mb
			if (!empty($data_openid)){
				$first  = $cook_nickname."您好，您的余额账户有变动：";
				$remark = $content."，查看详情";
				$url    = HOST."/?z=my&e=my_money";
				wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid.'&money=-'.$amount.'&money_total='.$endnum.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
			//////////////////
			//处理她
			$row = $db->NUM($uid,'nickname,money,openid');
			if ($row){
				$data_nickname2 = urldecode(dataIO($row[0],'out'));
				$data_money2    = $row[1];
				$data_openid2   = $row[2];
				$endnum2  = intval($data_money2 + $amount);
				$db->query("UPDATE ".__TBL_USER__." SET money=".$endnum2." WHERE id=".$uid);
				//money_list
				$content = '收到红包(打赏)';
				//$db->AddHistoryList($uid,$content,$amount,1);
				$db->AddLovebRmbList($uid,$content,$amount,'money',12);		
				//weixin_mb
				if (!empty($data_openid2)){
					$first  = $data_nickname2."您好，您的余额账户有变动：";
					$remark = $content."，查看详情";
					$url    = HOST."/?z=my&e=my_money";
					wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid2.'&money='.$amount.'&money_total='.$endnum2.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
				}
			}
			//////////////////
			$ifok = 1;
		}
	}
}
//
if ($submitok != 'add_shang'){
$rt = $db->query("SELECT a.id,a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,a.job,a.birthday,a.love,a.dataflag,a.areatitle,a.aboutus,b.sex,b.areatitle,b.age1,b.age2,b.heigh1,b.heigh2,b.kind,b.amount,b.num,b.money,b.content,b.addtime,b.click,b.content,b.flag,b.ruleout FROM ".__TBL_USER__." a,".__TBL_HONGBAO__." b WHERE a.id=b.uid AND a.flag=1 AND b.flag>0 AND b.id=".$fid);
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
	$uid = $row[0];
	$Unickname = dataIO($row[1],'out');
	$Usex      = $row[2];
	$Ugrade    = $row[3];
	$Uphoto_s  = $row[4];
	$Uphoto_f  = $row[5];
	$Ujob = $row[6];
	$Ubirthday = $row[7];
	$Ulove = $row[8];
	$Udataflag  = $row[9];
	$Uareatitle = $row[10];
	$Uaboutus   = dataIO($row[11],'out');
	//
	$hb_sex  = $row[12];
	$hb_areatitle = $row[13];
	$hb_age1 = $row[14];
	$hb_age2 = $row[15];
	$hb_heigh1 = $row[16];
	$hb_heigh2 = $row[17];
	$hb_kind = $row[18];
	$hb_amount = $row[19];
	$hb_num = $row[20];
	$hb_money = $row[21];
	$hb_content = $row[22];
	$hb_addtime = $row[23];
	$hb_click = $row[24];
	$hb_content = dataIO($row[25],'out');
	$hb_flag = $row[26];
	$hb_ruleout = $row[27];
	//运气或定额过期超时退款
	$difftime = $ADDTIME - $hb_addtime;
	if ( $difftime > $_ZEAI['HB_refundtime']*86400 && ($hb_kind == 1 || $hb_kind == 2) && $hb_flag==1 ){
		$db->query("UPDATE ".__TBL_HONGBAO__." SET flag=2 WHERE id=".$fid);
		//统计已抢金额
		$rt2=$db->query("SELECT SUM(money) FROM ".__TBL_HONGBAO_USER__." WHERE fid=".$fid);
		$row2 = $db->fetch_array($rt2);
		$nomoney = intval($row2[0]);
		$endtotalmoney = $hb_money - $nomoney;
		if ($endtotalmoney > 0){
			$row2 = $db->NUM($uid,'uname,money,openid');
			if ($row2){
				$data_username2= $row2[0];
				$data_money2   = $row2[1];
				$data_openid2  = $row2[2];
				//money_list
				$endnum  = $endtotalmoney + $data_money2;
				$db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$uid);
				$db->AddLovebRmbList($uid,'红包未抢完退款',$endtotalmoney,'money',15);


				//weixin_mb
				if (!empty($data_openid2)){
					$first  = $data_username2."您好，您的余额账户有变动(红包退款)：";
					$remark = "红包未抢完退款，查看详情";
					$url    = HOST."/?z=my&e=my_money";
					wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid2.'&money='.$endtotalmoney.'&money_total='.$endnum.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
				}
			}
		}
		//
	}
	//
	$hb_ruleout_str = ($hb_ruleout == 1)?'，只有新人可抢':'';
	if ($hb_kind == 3){
		$hb_money_str = ($hb_money == 0)?'不限':$hb_money.'元';
		//$hb_num_str   = '不限';
	}else{
		//统计已抢金额
		$rt=$db->query("SELECT SUM(money) FROM ".__TBL_HONGBAO_USER__." WHERE fid=".$fid);
		$row = $db->fetch_array($rt);
		$totalmoneyed = intval($row[0]);
		//统计已抢人数
		$data_num = $db->COUNT(__TBL_HONGBAO_USER__,"fid=".$fid);
		$totalnumed = intval($data_num);
		$hb_money_str = $totalmoneyed.'/'.$hb_money;
		$hb_num_str   = $totalnumed.'/'.$hb_num;
		if (($totalmoneyed >= $hb_money || $totalnumed >= $hb_num) && $hb_flag == 1){
			$db->query("UPDATE ".__TBL_HONGBAO__." SET flag=2 WHERE id=".$fid);
		}
	}
	$addtime_str = date_str($hb_addtime);
	if ($hb_flag == 1){
		$qiang_str   = '<img src="hb.png?2">';
		$qiang_class = '';
	}else{
		$qiang_str = '<img src="yqw.png?3">';
		$qiang_class = ' class="ed"';
	}
	$href = 'detail.php?fid='.$id;
	//
	$Uhref  = HOST.'/?z=index&e=u&a='.$uid;
	$photo_s     = $Uphoto_s;
	$Uphoto_s_url = (!empty($Uphoto_s) && $Uphoto_f==1)?$_ZEAI['up2'].'/'.$Uphoto_s:'/images/photo_s'.$Usex.'.png';
	$imgbdr      = (empty($Uphoto_s) || $Uphoto_f==0)?' class="imgbdr'.$Usex.'"':'';
	$db->query("UPDATE ".__TBL_HONGBAO__." SET click=click+1 WHERE id=".$fid);
}else{NoUserInfo();}
	$iflogin = false;
	if (ifint($cook_uid,'0-9','1,8')){
		$row = $db->NUM($cook_uid,"id");
		if ($row){$iflogin = true;}
	}
}

if ($submitok == 'add_shang'){
	$mini_title = $title_shang;
}else{
	if ($hb_kind == 1 || $hb_kind == 2){
		$mini_title = $Unickname.'-发红包(围观:'.$hb_click.'人)';
	}else{
		$mini_title = $Unickname.'-讨红包(围观:'.$hb_click.'人)';
	}
}

$mini_show  = true;$nav='trend';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>红包_<?php echo $_ZEAI['siteName']; ?></title>
<?php echo $headmeta; ?>
<link href="hongbao.css?2" rel="stylesheet" type="text/css" />
<script src="www_zeai_cn.js?2"></script>
</head>
<body>
<?php require_once 'top_mini.php';?>

<?php if ($submitok == 'add_shang_update'){ ?>
	<?php if ($ifok == 1){ 
	require_once 'bottom.php';
	?>
        <script>
        ZEAI_winclose_alert();
        //ZEAI_win_alert('～红包发送成功～','<?php echo $SELF; ?>?fid=<?php echo $fid; ?>');
        ZEAI_win_alert('～红包发送成功～','<?php echo $SELF; ?>?fid=<?php echo $fid; ?>');
        </script>
        <?php
		
		exit;
		}elseif($ifok == 0){?>
        <div class='nodatatips W300' style="margin-top:30px"><i class='sorry50'></i><br><?php echo $ifok_str; ?><br><br><a href="<?php echo HOST;?>/?z=my&e=my_money&a=cz" target="_parent" class="aLAN">我要充值</a><br><br></div>
	<?php require_once 'bottom.php';exit;}?>
<?php }?>
<!-- -->
<?php if ($submitok == 'add_shang'){ ?><br>
    <form action="<?php echo $SELF; ?>" method="post" class="detail">
    <table>
    <tr>
    <td height="50" align="right" class="S16">红包金额：</td>
    <td align="left" class="S16"><input name="amount" type="text" id="amount" autocomplete="off" value="<?php echo $data_money_str; ?>" maxlength="3" class="input W80" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;">
      元 <font class="S12 C999" style="display:inline-block">(不接受小数)</font></td>
    </tr>
    <tr>
    <td height="50" align="right" class="S16">说两句：</td>
    <td align="left"><select id="content" name="content" class="select">
    <option value="相识系于缘，相知系于诚，一个真正的朋友不论在身何处，总时时付出关和爱！">相识系于缘，相知系于诚，一个真正的朋友不论在身何处，总时时付出关和爱！</option>
    <option value="快乐、快乐、快乐、快乐，如果这些还不能装满你的心窝，那再送你一个大红包，装着我满满的祝福！">快乐、快乐、快乐、快乐，如果这些还不能装满你的心窝，那再送你一个大红包，装着我满满的祝福！</option>
    <option value="红包代表我的心，你就收下吧">红包代表我的心，你就收下吧</option>
    <option value="情谊深浓不在联系频繁，而在心底的惦念;祝福不在字数多少，而在感情的真挚">情谊深浓不在联系频繁，而在心底的惦念;祝福不在字数多少，而在感情的真挚</option>
    <option value="寄一句真真的问候，字字句句都祝你新年快乐；送一串深深的祝福，分分秒秒都祈祷你新年平安；传一份浓浓的心意，点点滴滴都愿你新年如意！">寄一句真真的问候，字字句句都祝你新年快乐；送一串深深的祝福，分分秒秒都祈祷你新年平安；传一份浓浓的心意，点点滴滴都愿你新年如意！</option>
    <option value="相传幸福是个美丽的玻璃球，跌碎散落世间每个角落，有人拾得多些，有人拾得少些，却没有谁能拥有全部，我愿你比别人更幸福！">相传幸福是个美丽的玻璃球，跌碎散落世间每个角落，有人拾得多些，有人拾得少些，却没有谁能拥有全部，我愿你比别人更幸福！</option>
    <option value="花生栗子红枣，健康跟着你跑；核桃杏仁红糖，快乐无处可藏；大米小米玉米，运气无人可比；幸福元素全倒锅里，熬成粥送给你。">花生栗子红枣，健康跟着你跑；核桃杏仁红糖，快乐无处可藏；大米小米玉米，运气无人可比；幸福元素全倒锅里，熬成粥送给你。</option>
    <option value="有钱也好，没钱也好，不如开心好；苦点也好，累点也好，保持心情好；在家也好，在外也好，平安无事才好；过去也好，现在也好，愿你将来能更美好">有钱也好，没钱也好，不如开心好；苦点也好，累点也好，保持心情好；在家也好，在外也好，平安无事才好；过去也好，现在也好，愿你将来能更美好</option>
    <option value="生活其实很简单，昨天、今天和明天；生活其实很复杂，人心、人情和人欲。把简单的生活变充实是聪明；把复杂的人生变简单是聪慧。愿你人生精彩！">生活其实很简单，昨天、今天和明天；生活其实很复杂，人心、人情和人欲。把简单的生活变充实是聪明；把复杂的人生变简单是聪慧。愿你人生精彩！</option>
    <option value="商务讲合作，伙伴是朋友；诚意送问候，短信送朋友；祝福不能少，情意要更多；祝你工作顺利没烦恼！生活美满心情好！">商务讲合作，伙伴是朋友；诚意送问候，短信送朋友；祝福不能少，情意要更多；祝你工作顺利没烦恼！生活美满心情好！</option>
    <option value="如果快乐可以存取，我愿将我的快乐，存入你的户头，让你随意支配；如果幸福可以邮寄，我愿将我的幸福，邮递给你，让你随时领取。愿你幸福快乐!">如果快乐可以存取，我愿将我的快乐，存入你的户头，让你随意支配；如果幸福可以邮寄，我愿将我的幸福，邮递给你，让你随时领取。愿你幸福快乐!</option>
    <option value="缘分，擦肩而过是十年修来的，彼此相遇是百年修来的，成为朋友是千年修来的，能为你送祝福是万年修来的，我真心祝你：快乐幸福每一天！">缘分，擦肩而过是十年修来的，彼此相遇是百年修来的，成为朋友是千年修来的，能为你送祝福是万年修来的，我真心祝你：快乐幸福每一天！</option>
    <option value="人之相交，交于情；人之相信，信于诚；人之相处，处于心；人之问候，发信息。多变的天气，不变的友谊，彼此的忙碌不等于忘记，好好照顾自己哦！">人之相交，交于情；人之相信，信于诚；人之相处，处于心；人之问候，发信息。多变的天气，不变的友谊，彼此的忙碌不等于忘记，好好照顾自己哦！</option>
    <option value="在错的时间遇到错的人，是无缘；在错的时间遇到对的人，是遗憾；在对的时间遇到错的人，是错爱；在对的时间遇到对的人，是幸福。愿你收获幸福。">在错的时间遇到错的人，是无缘；在错的时间遇到对的人，是遗憾；在对的时间遇到错的人，是错爱；在对的时间遇到对的人，是幸福。愿你收获幸福。</option>
    <option value="两个人，两颗心，一生等待。两个人，两座城，一生牵挂。两个人，两条路，一生相伴。两个人，两句话，一声问候：我愿永伴你身边，给你快乐幸福！">两个人，两颗心，一生等待。两个人，两座城，一生牵挂。两个人，两条路，一生相伴。两个人，两句话，一声问候：我愿永伴你身边，给你快乐幸福！</option>
    </select>
    </td>
    </tr>
    <tr>
    <td height="50" align="right">&nbsp;</td>
    <td align="left">
    <input name="submitok" type="hidden" value="add_shang_update" />
    <input name="fid" type="hidden" value="<?php echo $fid; ?>" />
    <input class="btn2LV" type="submit" value="发送" /></td>
    </tr>
    </table>
    </form>
<?php require_once 'bottom.php';?>
<?php exit;}?>
<!-- -->
<main>
    <em class="detail">

        
        <?php if ($hb_kind == 1 || $hb_kind == 2){ ?>
            <span><i></i><?php echo $addtime_str; ?></span>
            <div class="hbbox"><a id="qiang"<?php echo $qiang_class; ?>><?php echo $qiang_str; ?></a></div>
            <div class="hb_content">～<?php echo $hb_content; ?>～</div>
            <div class="hbinfo"><?php switch ($hb_kind){case 1:echo "运气红包";break;case 2:echo "定额红包";break;case 3:echo "讨红包";break;}?>　|　红包金额<font><?php echo $hb_money_str; ?></font>元　|　红包个数 <?php echo $hb_num_str; ?> </div>
            <div class="hbmate"><font>领取条件</font>
            性别：<?php switch ($hb_sex){default:echo "不限";break;case 1:echo "仅限男士";break;case 2:echo "仅限女士";break;}?>，
            年龄：<?php if (!empty($hb_age1) && !empty($hb_age2)){echo $hb_age1.'～'.$hb_age2.'岁';}elseif(empty($hb_age1) && !empty($hb_age2)){echo $hb_age2.'岁以内';}elseif(!empty($hb_age1) && empty($hb_age2)){echo $hb_age1.'岁以上';}else{echo '不限';}?>，
            身高：<?php if (!empty($hb_heigh1) && !empty($hb_heigh2)){echo $hb_heigh1.'～'.$hb_heigh2.'厘米';}elseif(empty($hb_heigh1) && !empty($hb_heigh2)){echo $hb_heigh2.'厘米以下';}elseif(!empty($hb_heigh1) && empty($hb_heigh2)){echo $hb_heigh1.'厘米以上';}else{echo '不限';}echo $hb_ruleout_str;?>
            </div>
            <div class="linee"><div class="span">领取列表</div></div>
            <ul>
            <?php
            $rt=$db->query("SELECT a.id,a.uid,a.money,b.nickname,b.sex,b.photo_s,b.photo_f FROM ".__TBL_HONGBAO_USER__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.fid=".$fid." ORDER BY a.id DESC");/*a.flag>0 AND*/ 
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt);
                if(!$rows) break;
                $id       = $rows[0];
                $uid      = $rows[1];
                $money    = $rows[2];
                $nickname = urldecode(dataIO($rows[3],'out'));
                $sex      = $rows[4];
                $photo_s  = $rows[5];
                $photo_f  = $rows[6];
                $umoney_str = '<br>抢到 <font>'.$money.'元</font>';
                $href    = HOST.'/?z=index&e=u&a='.$uid;
                $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
                $imgbdr      = (empty($photo_s) || $photo_f==0)?' class="imgbdr'.$sex.'"':'';
                $img_str     = '<img src="'.$photo_s_url.'" class="imgbdr'.$sex.'">';
                ?>
                <li><a href="<?php echo $href; ?>"><?php echo $img_str; ?><h5><?php echo $nickname.$umoney_str; ?></h5></a></li>
            <?php }}else{echo "<div class='nodatatips W150'><i class='sorry50'></i><br>居然红包都没人领</div>";}?>
        </ul>
        <?php }else{ ?>
            <span><i></i><?php echo $addtime_str; ?></span>
            <div class="hbbox shang"><a><i id="btnshang">赏一个</i></a></div>
            <div class="hb_content">～<?php echo $hb_content; ?>～</div>
            <div class="hbinfo"><?php switch ($hb_kind){case 1:echo "运气红包";break;case 2:echo "定额红包";break;case 3:echo "讨红包";break;}?>　|　红包金额<font><?php echo $hb_money_str; ?></font></div>
            <div class="linee"><div class="span">都有谁打赏过</div></div>
            <ul>
            <?php
            $rt=$db->query("SELECT a.id,a.uid,a.money,b.nickname,b.sex,b.photo_s,b.photo_f FROM ".__TBL_HONGBAO_USER__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.fid=".$fid." ORDER BY a.id DESC");/*a.flag>0 AND*/ 
            $total = $db->num_rows($rt);
            if ($total > 0) {
                for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt);
                if(!$rows) break;
                $id       = $rows[0];
                $uid      = $rows[1];
                $money    = $rows[2];
                $nickname = urldecode(dataIO($rows[3],'out'));
                $sex      = $rows[4];
                $photo_s  = $rows[5];
                $photo_f  = $rows[6];
                $umoney_str = '<br>打赏 <font>'.$money.'元</font>';
                $href    = HOST.'/?z=index&e=u&a='.$uid;
                $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
                $imgbdr      = (empty($photo_s) || $photo_f==0)?' class="imgbdr'.$sex.'"':'';
                $img_str     = '<img src="'.$photo_s_url.'" class="imgbdr'.$sex.'">';
                ?>
                <li><a href="<?php echo $href; ?>"><?php echo $img_str; ?><h5><?php echo $nickname.$umoney_str; ?></h5></a></li>
            <?php }}else{echo "<div class='nodatatips W150'><i class='sorry50'></i><br>好可怜，居然没人打赏我<br><br></div>";}?>
            </ul>
        <?php }?>
        

    </em>
</main>
<?php if ($hb_flag == 1 && ifint($cook_uid)){ ?>
<div id="mask_qd" class='alpha0_100'><div class="gif rotate" id="mask_gif"></div></div>
<div id="qdokbox" class="scale"><div class="qdok"><h1>已抢到</h1><div class="hr"></div><h4>恭喜你抢得<font id="randloveb">0</font>元</h4></div></div>
<?php }?>
<script>var uid=<?php echo $uid; ?>,fid=<?php echo $fid;?>,hb_flag=<?php echo $hb_flag; ?>,nickname='<?php echo $Unickname; ?>';</script>
<script src="hongbao.js?2"></script>
<script src="win_confirm.js"></script>
<?php require_once 'bottom.php';?>
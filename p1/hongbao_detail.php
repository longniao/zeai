<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
header("Cache-control: private");
if (!ifint($fid))alert("信息不存在","-1");
require_once ZEAI.'sub/conn.php';
/*if($submitok == 'ajax_chklogin'){
	require_once ZEAI.'sub/conn.php';
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来抢','jumpurl'=>Href('hongbao',$fid)));
	json_exit(array('flag'=>1,'msg'=>'已登录'));
}
*/
if ($submitok == 'ajax_add_qiang'){
	if(!ifint($cook_uid))exit(json_encode(array('flag'=>'nologin','msg'=>'亲！请先登录后再来抢吧','jumpurl'=>Href('hongbao',$fid))));
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来抢吧','jumpurl'=>Href('hongbao',$fid)));
	$row = $db->NUM($cook_uid,'sex,areaid,birthday,heigh,dataflag,money,openid,subscribe');
	if (!$row){
		json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来抢吧','jumpurl'=>Href('hongbao',$fid)));
	}else{
		$data_sex    = $row[0];
		$data_areaid = $row[1];
		$data_age    = getage($row[2]);
		$data_heigh  = $row[3];
		$data_dataflag = $row[4];
		$data_money  = $row[5];
		$data_openid = $row[6];
		$data_subscribe = $row[7];
	}
	//
	$row = $db->ROW(__TBL_HONGBAO_USER__,"id","fid=".$fid." AND uid=".$cook_uid);
	if ($row)exit(json_encode(array('flag'=>'ed','msg'=>'亲！您已经抢过了，请不要贪心哦')));
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
					exit(json_encode(array('flag'=>'ed','msg'=>'您已经抢过他红包了～只有新人可抢')));
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
		
		if (!empty($hb_areaid)){
			if (empty($data_areaid)){
				$mateflag = false;
			}elseif(!strstr($hb_areaid,$data_areaid)){
				$mateflag = false;
			}
			
		}
		
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
						$row = $db->fetch_array($rt,'num');
						$endtotalmoney = $hb_amount - intval($row[0]);
						//
						require_once ZEAI.'sub/zeai_hongbao.php';
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
					$db->query("INSERT INTO ".__TBL_HONGBAO_USER__." (fid,uid,money,addtime) VALUES ($fid,$cook_uid,$money,".ADDTIME.")");
					//money_list
					$endnum  = $money + $data_money;
					$db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$cook_uid);
					$content = '抢红包';
					$db->AddLovebRmbList($cook_uid,$content,$money,'money',14);
					//余额站内消息
					$C = $cook_nickname.'您好，您有一笔资金到账！　　<a href='.Href('money').' class=aQING>查看详情</a>';
					$db->SendTip($cook_uid,'抢红包到账'.$money.'元',dataIO($C,'in'),'sys');
					//weixin_mb
					if (!empty($data_openid) && $data_subscribe==1){
						$first   = urlencode($cook_nickname."您好，您有一笔资金到账！");
						$content = urlencode('抢红包');
						$url     = urlencode(mHref('money'));
						@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$money.'&first='.$first.'&content='.$content.'&url='.$url);
					}
					$retarr = array('flag'=>1,'moeny'=>$money);
				}
			}
		}
	}else{
		$retarr = array('flag'=>'nodata','msg'=>'红包不存在或已过期～');
	}
	exit(json_encode($retarr));
}elseif($submitok == 'ajax_add_shang'){
	if(!ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'亲！请您先登录～','jumpurl'=>Href('hongbao',$fid)));
	$row = $db->NUM($cook_uid,'money');
	if (!$row){json_exit(array('flag'=>'nologin','msg'=>'亲！请您先登录～','jumpurl'=>Href('hongbao',$fid)));}else{
		$data_money  = $row[0];
	}
	$row = $db->ROW(__TBL_HONGBAO__,"uid,money","id=".$fid." AND flag=1 AND kind=3","num");
	if (!$row){
		exit(json_encode(array('flag'=>'forbidden','msg'=>'红包不存在或已过期～')));
	}else{
		$uid   = $row[0];
		$money = $row[1];
		if($cook_uid == $uid)exit(json_encode(array('flag'=>'noeaning','msg'=>'操作自己有意义么～')));
		if($money > $data_money && $money>0)exit(json_encode(array('flag'=>'nomoney','msg'=>'账户余额不足'.$money.'元，请先充值','jumpurl'=>Href('hongbao',$fid))));
		exit(json_encode(array('flag'=>1)));
	}
}elseif($submitok == 'add_shang'){
	$row = $db->ROW(__TBL_HONGBAO__,"money","id=".$fid." AND flag=1 AND kind=3",'num');
	if (!$row){callmsg("红包不存在～","-1");}else{
		$data_money_str = ($row[0] == 0)?1:$row[0];
	}
}elseif($submitok == 'add_shang_update'){
	if(!ifint($cook_uid))alert("亲！请您先登录～","-1");
	$amount = abs(intval($amount));
	$row = $db->NUM($cook_uid,'money,openid,subscribe');
	if (!$row){alert("亲！请您先登录～","-1");}else{
		$data_money  = $row[0];
		$data_openid = $row[1];
		$data_subscribe = $row[2];
	}
	$row = $db->ROW(__TBL_HONGBAO__,"uid,money,kind","id=".$fid." AND flag=1 AND kind=3","num");
	if (!$row){
		alert("红包不存在～","-1");
	}else{
		$uid      = $row[0];
		$hb_money = $row[1];
		$hb_kind  = $row[2];
		$hb_money = ($hb_money <= 0)?1:$hb_money;
		if ($amount <= 0)alert("老板，至少发".$hb_money."元给我哦～","-1");
		if ($hb_money > $amount)alert("老板，至少发".$hb_money.'元给我哦～',"-1");
		if($amount > $data_money && $amount>0){
			$ifok = 0;
			$ifok_str = "账户余额不足".$amount."元～";
		}else{
			/////////处理我本人/////////
			$endnum  = intval($data_money - $amount);
			$db->query("UPDATE ".__TBL_USER__." SET money=".$endnum." WHERE id=".$cook_uid);
			$content = dataIO($content,'in',250);
			$db->query("INSERT INTO ".__TBL_HONGBAO_USER__." (fid,uid,money,addtime,content,kind) VALUES ($fid,$cook_uid,$amount,".ADDTIME.",'$content',$hb_kind)");
			//money_list
			$content = '发布红包(打赏)';
			$db->AddLovebRmbList($cook_uid,$content,-$amount,'money',13);	
			//余额站内消息
			$C = $cook_nickname.'您好，您的余额账户有变动,打赏红包扣除　　<a href='.Href('money').' class=aQING>查看详情</a>';
			$db->SendTip($cook_uid,'您的余额账户有变动',dataIO($C,'in'),'sys');
			//weixin_mb
			if (!empty($data_openid) && $data_subscribe==1){
				$first  = $cook_nickname."您好，您的余额账户有变动：";
				$remark = $content."，查看详情";
				$url    = urlencode(mHref('money'));
				wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid.'&money=-'.$amount.'&money_total='.$endnum.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
			/////////处理她/////////
			$row = $db->NUM($uid,'nickname,money,openid,subscribe');
			if ($row){
				$data_nickname2 = trimhtml(dataIO($row[0],'out'));
				$data_money2    = $row[1];
				$data_openid2   = $row[2];
				$data_subscribe2   = $row[3];
				$endnum2  = intval($data_money2 + $amount);
				$db->query("UPDATE ".__TBL_USER__." SET money=".$endnum2." WHERE id=".$uid);
				//money_list
				$db->AddLovebRmbList($uid,'收到红包(打赏)',$amount,'money',12);	
				//余额站内消息
				$C = $data_nickname2.'您好，您有一笔资金到账,红包打赏　　<a href='.Href('money').' class=aQING>查看详情</a>';
				$db->SendTip($uid,'您有一笔资金到账，收到红包(打赏)',dataIO($C,'in'),'sys');
				//weixin_mb
				if (!empty($data_openid2) && $data_subscribe2==1){
					$first   = urlencode($data_nickname2."您好，您有一笔资金到账！");
					$content = urlencode('收到红包(打赏)');
					$url     = urlencode(mHref('money'));
					@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid2.'&num='.$amount.'&first='.$first.'&content='.$content.'&url='.$url);
				}
			}
			//////////////////
			$ifok = 1;
		}
	}
}
require_once ZEAI.'cache/udata.php';
//
if ($submitok != 'add_shang'){
	$rt = $db->query("SELECT a.id,a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,a.job,a.birthday,a.love,a.dataflag,a.areatitle,a.pay,b.sex,b.areatitle,b.age1,b.age2,b.heigh1,b.heigh2,b.kind,b.amount,b.num,b.money,b.content,b.addtime,b.click,b.content,b.flag,b.ruleout,a.photo_ifshow FROM ".__TBL_USER__." a,".__TBL_HONGBAO__." b WHERE a.id=b.uid AND a.flag=1 AND b.flag>0 AND b.id=".$fid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$uid = $row[0];
		$Unickname = trimhtml(dataIO($row[1],'out'));
		$Usex      = $row[2];
		$Ugrade    = $row[3];
		$Uphoto_s  = $row[4];
		$Uphoto_f  = $row[5];
		$Ujob = $row[6];
		$Ubirthday = $row[7];
		$Ulove = $row[8];
		$Udataflag  = $row[9];
		$Uareatitle = $row[10];
		$Upay   = dataIO($row[11],'out');
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
		$photo_ifshow = $row[28];
		//运气或定额过期超时退款
		$difftime = ADDTIME - $hb_addtime;
		if ( $difftime > $_ZEAI['HB_refundtime']*86400 && $hb_flag==1 ){
			$db->query("UPDATE ".__TBL_HONGBAO__." SET flag=2 WHERE id=".$fid);
			if(($hb_kind == 1 || $hb_kind == 2)){
				//统计已抢金额
				$rt2=$db->query("SELECT SUM(money) FROM ".__TBL_HONGBAO_USER__." WHERE fid=".$fid);
				$row2 = $db->fetch_array($rt2,'num');
				$nomoney = intval($row2[0]);
				$endtotalmoney = $hb_money - $nomoney;
				if ($endtotalmoney > 0){
					$row2 = $db->NUM($uid,'uname,money,openid,subscribe');
					if ($row2){
						$data_username2= $row2[0];
						$data_money2   = $row2[1];
						$data_openid2  = $row2[2];
						$data_subscribe2  = $row2[3];
						//money_list
						$endnum  = $endtotalmoney + $data_money2;
						$db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$uid);
						$db->AddLovebRmbList($uid,'红包未抢完退款',$endtotalmoney,'money',15);
						//余额站内消息
						$C = $data_nickname.'您好，您有一笔余额到账(红包退款)　　<a href='.Href('money').' class=aQING>查看详情</a>';
						$db->SendTip($cook_uid,'红包未抢完退款',dataIO($C,'in'),'sys');
						//weixin_mb
						if (!empty($data_openid2) && $data_subscribe2==1){
							$first  = $data_username2."您好，您的余额账户有变动(红包退款)：";
							$remark = "红包未抢完退款，查看详情";
							$url    = urlencode(mHref('money'));
							wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid2.'&money='.$endtotalmoney.'&money_total='.$endnum.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
						}
					}
				}
			}
			//
		}
		//
		$hb_ruleout_str = ($hb_ruleout == 1)?'，只有新人可抢':'';
		if ($hb_kind == 3){
			$hb_money_str = ($hb_money == 0)?'不限':$hb_money.'元';
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
			$qiang_str   = '<i class="ico">&#xe66b;</i><font>猛戳开红包</font>';
			$qiang_class = '';
		}else{
			$qiang_str = '<img src="'.HOST.'/res/yqw.png">';
			$qiang_class = ' class="ed"';
		}
		//$href = 'detail.php?fid='.$id;
		//
		$Unickname = (empty($Unickname))?'uid:'.$uid:$Unickname;
		$birthday_str = (getage($Ubirthday)<=0)?'':getage($Ubirthday).'岁 ';
		$aARR = explode(' ',$Uareatitle);
		$areatitle_str = (empty($aARR[1]))?'':$aARR[1].$aARR[2];
		$areatitle_str  = str_replace("不限","",$areatitle_str);
		$job_str      = (empty($Ujob))?'':udata('job',$Ujob).' ';
		$pay_str      = (empty($Upay))?'':udata('pay',$Upay).'/月'.' ';
		$love_str     = (empty($Ujob))?'':udata('love',$Ujob).' ';
		$heigh_str    = ($Ujob>140)?$Ujob.'cm ':'';
	
		$Uhref  = Href('u',$uid);
		$photo_m     = getpath_smb($Uphoto_s,'m');
		$photo_m_url = (!empty($Uphoto_s) && $Uphoto_f==1)?$_ZEAI['up2'].'/'.$photo_m:HOST.'/res/photo_m'.$Usex.'.png';
		if($photo_ifshow==0)$photo_m_url=HOST.'/res/photo_m'.$Usex.'_hide.png';
		
		$db->query("UPDATE ".__TBL_HONGBAO__." SET click=click+1 WHERE id=".$fid);
	}else{alert('信息不存在','-1');}
}
$nav='hongbao';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>红包_<?php echo $_ZEAI['siteName']; ?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/hongbao.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
</head>
<body>
<?php if ($submitok == 'add_shang_update'){ ?>
	<style>body{background-color:#fff}</style>
	<div  class="dtl_dashang">
	<?php if ($ifok == 1){ ?>
        <script>
		zeai.msg('～红包打赏成功～',{time:2});
		setTimeout(function(){parent.location.reload(true);},2000);
        </script>
        <?php
		exit;
	}elseif($ifok == 0){?>
        <script>
			zeai.msg(0);zeai.msg('<?php echo $ifok_str; ?>',{time:2});
			setTimeout(function(){parent.zeai.openurl(PCHOST+'/my_money'+zeai.ajxext+'t=3&jumpurl='+encodeURIComponent('<?php echo Href('hongbao',$fid);?>'));},2000);
		</script>
	<?php exit;}?>
    </div>
<?php }?>
<!-- -->
<?php if ($submitok == 'add_shang'){ ?>
	<style>body{background-color:#fff}</style>
    <form action="<?php echo SELF; ?>" method="post" class="dtl_dashang">
    <h1>红包打赏</h1>
	<table>
    <tr>
    <td width="70" height="60" align="right" class="S16">打赏金额</td>
    <td align="left" class="S16"><input name="amount" type="text" id="amount" autocomplete="off" value="<?php echo $data_money_str; ?>" maxlength="3" class="input W80" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;">
      元 <font class="S12 C999" style="display:inline-block">(不接受小数)</font></td>
    </tr>
    <tr>
    <td width="70" height="50" align="right" class="S16">说两句</td>
    <td align="left"><select id="content" name="content" class="select W98_">
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
    <td width="70" height="50" align="right">&nbsp;</td>
    <td align="left">
    <input name="submitok" type="hidden" value="add_shang_update" />
    <input name="fid" type="hidden" value="<?php echo $fid; ?>" />
    <input class="btn size4 LV2" type="submit" value="确定" />
    </td>
    </tr>
  </table>
    </form>
<?php exit;}?>

<?php require_once ZEAI.'p1/top.php';?>
<div class="main hongbao fadeInL">
	<div class="hongbaoL">
    	<div class="box S5 U" style="margin-bottom:0">
            <h1>来自：<?php echo uicon($Usex.$Ugrade).$Unickname; ?></h1>
            <em class="detail">
                <?php if ($hb_kind == 1 || $hb_kind == 2){ ?>
                    <div class="hbbox"><a id="qiang"<?php echo $qiang_class; ?>><?php echo $qiang_str; ?></a></div>
                    <div class="hb_content">～<?php echo $hb_content; ?>～</div>
                    <div class="hbinfo"><?php switch ($hb_kind){case 1:echo "运气红包";break;case 2:echo "定额红包";break;case 3:echo "讨红包";break;}?>　|　红包金额<font><?php echo $hb_money_str; ?></font>元　|　红包个数 <?php echo $hb_num_str; ?>　|　<?php echo $addtime_str; ?></div>
                    <div class="hbmate"><font>领取条件</font>
                    性别：<?php switch ($hb_sex){default:echo "不限";break;case 1:echo "仅限男士";break;case 2:echo "仅限女士";break;}?>，
                    年龄：<?php if (!empty($hb_age1) && !empty($hb_age2)){echo $hb_age1.'～'.$hb_age2.'岁';}elseif(empty($hb_age1) && !empty($hb_age2)){echo $hb_age2.'岁以内';}elseif(!empty($hb_age1) && empty($hb_age2)){echo $hb_age1.'岁以上';}else{echo '不限';}?>，
                    身高：<?php if (!empty($hb_heigh1) && !empty($hb_heigh2)){echo $hb_heigh1.'～'.$hb_heigh2.'厘米';}elseif(empty($hb_heigh1) && !empty($hb_heigh2)){echo $hb_heigh2.'厘米以下';}elseif(!empty($hb_heigh1) && empty($hb_heigh2)){echo $hb_heigh1.'厘米以上';}else{echo '不限';}echo $hb_ruleout_str;?>
                    </div>
                    <div class="linee"><div class="span">领取详情</div></div>
                    <ul>
                    <?php
                    $rt=$db->query("SELECT a.id,a.uid,a.money,b.nickname,b.sex,b.photo_s,b.photo_f,b.grade,b.photo_ifshow FROM ".__TBL_HONGBAO_USER__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.fid=".$fid." ORDER BY a.id DESC");/*a.flag>0 AND*/ 
                    $total = $db->num_rows($rt);
                    if ($total > 0) {
                        for($i=1;$i<=$total;$i++) {
                        $rows = $db->fetch_array($rt);
                        if(!$rows) break;
                        $id       = $rows[0];
                        $uid      = $rows[1];
                        $money    = $rows[2];
                        $nickname = trimhtml(dataIO($rows[3],'out'));
                        $sex      = $rows[4];
                        $photo_s  = $rows[5];
                        $photo_f  = $rows[6];
                        $grade    = $rows[7];
                        $photo_ifshow = $rows[8];
                        $umoney_str = '<br>抢到 <font>'.$money.'元</font>';
                        $href    = Href('u',$uid);
                        $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
						if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
                        $sexbg       = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':' class="m"';
                        $img_str     = '<img src="'.$photo_s_url.'" '.$sexbg.'>';
                        ?>
                        <li><a href="<?php echo $href; ?>" target="_blank"><?php echo $img_str; ?><h5><?php echo uicon($sex.$grade).$nickname.$umoney_str; ?></h5></a></li>
                    <?php }}else{echo nodatatips('居然红包都没人领');}?>
                </ul>
                <?php }else{ ?>
                    <div class="hbbox shang"><a><i id="btnshang">赏一个</i></a></div>
                    <div class="hb_content">～<?php echo $hb_content; ?>～</div>
                    <div class="hbinfo"><?php switch ($hb_kind){case 1:echo "运气红包";break;case 2:echo "定额红包";break;case 3:echo "讨红包";break;}?>　|　红包金额<font><?php echo $hb_money_str; ?></font>　|　<?php echo $addtime_str; ?></div>
                    <div class="linee"><div class="span">都有谁打赏过</div></div>
                    <ul>
                    <?php
                    $rt=$db->query("SELECT a.id,a.uid,a.money,b.nickname,b.sex,b.photo_s,b.photo_f,b.photo_ifshow FROM ".__TBL_HONGBAO_USER__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.fid=".$fid." ORDER BY a.id DESC");/*a.flag>0 AND*/ 
                    $total = $db->num_rows($rt);
                    if ($total > 0) {
                        for($i=1;$i<=$total;$i++) {
                        $rows = $db->fetch_array($rt,'num');
                        if(!$rows) break;
                        $id       = $rows[0];
                        $uid      = $rows[1];
                        $money    = $rows[2];
                        $nickname = trimhtml(dataIO($rows[3],'out'));
                        $sex      = $rows[4];
                        $photo_s  = $rows[5];
                        $photo_f  = $rows[6];
                        $photo_ifshow = $rows[7];
                        $umoney_str = '<br>打赏 <font>'.$money.'元</font>';
                        $href    = Href('u',$uid);
                        $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
						if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
                        $sexbg       = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':' class="m"';
                        $img_str     = '<img src="'.$photo_s_url.'" '.$sexbg.'>';
                        ?>
                        <li><a href="<?php echo $href; ?>" target="_blank"><?php echo $img_str; ?><h5><?php echo $nickname.$umoney_str; ?></h5></a></li>
                    <?php }}else{echo nodatatips('好可怜，居然没人打赏我');}?>
                    </ul>
                <?php }?>
            </em>
        </div>
	</div>
	<div class="hongbaoR">
        <div class="box S5">
			<div class="Uinfo">
            	<a href="<?php echo $Uhref;?>" target="_blank">
				<p class="sexbg<?php echo $Usex;?>" style="background-image:url('<?php echo $photo_m_url;?>')"></p>
            	<em>
                    <h5><?php echo uicon($Usex.$Ugrade).$Unickname; ?></h5>
                    <h5><?php echo $birthday_str.' '.$areatitle_str; ?></h5>
                    <h5><?php echo $love_str.' '.$job_str.' '.$pay_str; ?></h5>
                </em>
                </a>
            </div>
		</div>
        <div class="box S5 addbox">
			<h1>发布红包</h1>
            <div>
                <a href="javascript:;" class="ed" onclick="hongbao_add('out');"><i class="ico">&#xe64c;</i>　我要发红包</a><a href="javascript:;" class="ed2" onclick="hongbao_add('in');"><i class="ico">&#xe64c;</i>　我要讨红包</a>
            </div>
		</div>

	</div>
</div>
<?php if ($hb_flag == 1 && ifint($cook_uid)){ ?>
<div id="mask_qd" class='alpha0_100'><div class="gif rotate" id="mask_gif"></div></div>
<div id="qdokbox" class="scale"><div class="qdok"><h1>抢到</h1><div class="hr"></div><h4><font id="randloveb">0</font> 元</h4></div></div>
<?php }?>
<script>var uid=<?php echo $uid; ?>,fid=<?php echo $fid;?>,hb_flag=<?php echo $hb_flag; ?>,nickname='<?php echo $Unickname; ?>';</script>
<script src="<?php echo HOST;?>/p1/js/hongbao.js"></script>
<script src="<?php echo HOST;?>/p1/js/my_hongbao.js"></script>
<?php require_once ZEAI.'p1/bottom.php';?>
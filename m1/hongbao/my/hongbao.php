<?php 
require_once"../../../sub/init.php";
require_once '../hongbao_init.php';

$currfields = "nickname,sex,photo_s,photo_f,money,openid";
$chk_u_jumpurl=HOST.'/m1/hongbao/my/hongbao.php';require_once ZEAI.'my_chk_u.php';
$data_sex   = intval($row['sex']);
$data_money = intval($row['money']);
$data_photo_s = $row['photo_s'];
$data_photo_f = intval($row['photo_f']);
$data_openid  =  $row['openid'];
$data_nickname = dataIO($row['nickname'],'out');
switch ($t) {
	case 1:
		$menuid = 'send';
	break;
	case 2:
		$menuid = 'receive';
	break;
	case 3:
		if ($submitok == 'ajax_chkmoney'){
			$amount = intval($amount);
			$num    = intval($num);
			if ($kind == 1){//随机
				$money = $amount;
				if ($num > $money)exit(json_encode(array('flag'=>'dataerror','msg'=>'红包个数须小于红包总金额')));
			}elseif($kind == 2){//定额
				$money = $amount*$num;
			}else{
				exit(json_encode(array('flag'=>0,'msg'=>'forbidden')));
			}
			$money = abs($money);
			if ($money > $data_money || $data_money <= 0){
				$retarr = array('flag'=>-1,'msg'=>'余额不足'.$money.'元，请先充值','jumpurl'=>HOST.'/?z=my&e=my_money&a=cz');
			}else{
				$retarr = array('flag'=>1);
			}
			exit(json_encode($retarr));
		}elseif($submitok == 'addupdate'){
			$sex      = intval($sex);
			$areaid    = dataIO($areaid,'in',50);
			$areatitle = dataIO($areatitle,'in',100);
			$age1_      = intval($age1);
			$age2_      = intval($age2);
			$heigh1_    = intval($heigh1);
			$heigh2_    = intval($heigh2);
			if ($age1_ > $age2_ && $age2_>0){
				$age1      = $age2_;
				$age2      = $age1_;
			}else{
				$age1      = $age1_;
				$age2      = $age2_;
			}
			if ($heigh1_ > $heigh2_ && $heigh2_>0){
				$heigh1    = $heigh2_;
				$heigh2    = $heigh1_;
			}else{
				$heigh1    = $heigh1_;
				$heigh2    = $heigh2_;
			}
			$ruleout = intval($ruleout);
			$kind    = intval($kind);
			$amount  = intval($amount);
			$num     = intval($num);
			$content = dataIO($content,'in',200);
			if ($kind == 1){
				$money = $amount;
			}elseif($kind == 2){
				$money = $amount*$num;
			}else{
				callmsg("forbidden");
			}
			$money = abs($money);
			if ($money > $data_money || $data_money <= 0){
				callmsg('余额不足'.$money.'元，请先充值',HOST.'/?z=my&e=my_money&a=cz');
			}
			$db->query("INSERT INTO ".__TBL_HONGBAO__." (uid,sex,areaid,areatitle,age1,age2,heigh1,heigh2,ruleout,kind,amount,num,money,content,addtime) VALUES ($cook_uid,$sex,'$areaid','$areatitle',$age1,$age2,$heigh1,$heigh2,$ruleout,$kind,$amount,$num,$money,'$content',$ADDTIME)");
			$hbid = $db->insert_id();
			//////////////////
			$endnum  = intval($data_money - $money);
			$db->query("UPDATE ".__TBL_USER__." SET money=".$endnum." WHERE id=".$cook_uid);
			//money_list
			$content = '发布红包';
			//$db->AddHistoryList($cook_uid,$content,-$money,1);
			$db->AddLovebRmbList($cook_uid,$content,-$money,'money',13);

			//weixin_mb
			if (!empty($data_openid)){
				$first  = $data_nickname."您好，您的人民币账户有变动：";
				$remark = $content."，查看详情";
				$url    = HOST."/?z=my&e=my_money";
				@wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid.'&money=-'.$money.'&money_total='.$endnum.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
			//////////////////
			$first  = "有人发红包";
			$remark = "点击开抢";
			$url    = HOST."/m1/hongbao/detail.php?fid=".$hbid;
			@wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid=oQDDf0ciAxM6490BoIGiN3I7asrs&money='.$money.'&money_total='.$money.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			//////////////////
			callmsg('红包发布成功',"hongbao.php?t=1");
		}
		$sex = ($cook_sex == 1)?2:1;
	break;
	case 4:
		if($submitok == 'ajax_chkmoney4'){
			if (empty($data_photo_s) || $data_photo_f==0){
				$retarr = array('flag'=>'nophoto','msg'=>'请先上传形像照后再来讨红包','jumpurl'=>'./');
				exit(json_encode($retarr));
			}
			$retarr = array('flag'=>1);
			exit(json_encode($retarr));
		}elseif($submitok == 'addupdate4'){
			if (empty($data_photo_s) || $data_photo_f==0){
				callmsg('请先上传形像照后再来讨红包','./');
			}
			$rt=$db->query("SELECT addtime FROM ".__TBL_HONGBAO__." WHERE kind=3 AND uid=".$cook_uid." ORDER BY id DESC");
			if ($db->num_rows($rt)){
				$row = $db->fetch_array($rt);
				$addtime2 = $row[0];
				$difftime = $ADDTIME - $addtime2;
				//没过期提示
				if ( $difftime < $_ZEAI['HB_refundtime']*86400 ){
					callmsg('你很贪心哦，请'.$_ZEAI['HB_refundtime'].'天后再来讨吧',$SELF.'?t=1&k=2',420);
				}else{
					$db->query("INSERT INTO ".__TBL_HONGBAO__." (uid,kind,money,content,addtime) VALUES ($cook_uid,3,$money,'$content',$ADDTIME)");
				}
			}else{
				$db->query("INSERT INTO ".__TBL_HONGBAO__." (uid,kind,money,content,addtime) VALUES ($cook_uid,3,$money,'$content',$ADDTIME)");
			}
			callmsg('红包发布成功',"hongbao.php?t=1");
		}
	break;
	default:
		$t = 1;
		$menuid = '';
	break;
}
$t = (ifint($t,'1-4','1'))?$t:1;
$money      = intval($data_money);
$mini_url = HOST.'/?z=my';
$mini_show  = true;$mini_title = '我的红包';$nav='my';
$_Style['list_bg1']       = '#ffffff';//id=1d
$_Style['list_bg2']       = '#ffffff';//id=2
$_Style['list_overbg']    = '#fcfcfc';//MouseOver
$_Style['list_selectbg']  = '#f5f5f5';//Selected

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<?php echo $headmeta; ?>
<link href="../my_hongbao.css?2" rel="stylesheet" type="text/css" />
<script src="../www_zeai_cn.js"></script>
<script src="../select3.js"></script>
<script src="../date_input.js"></script>
<script src="../inputColor.js"></script>
<script>
var nulltext = '不限';
var selstr  = '请上下滚动选择';
var selstr2 = '不限';
var age_ARR = [{'id':'0','value':selstr2}];
for (var i = 20; i <= 80; i++) {
	var istr = i.toString();
	age_ARR.push({'id':istr,'value':istr+' 岁'});
}
var heigh_ARR = [{'id':'0','value':selstr2}];
for (var i = 130; i <= 230; i++) {
	var istr = i.toString();
	heigh_ARR.push({'id':istr,'value':istr+' 厘米'});
}
var sex_ARR = [
	{'id':'0','value':'性别不限'},
	{'id':'1','value':'男性朋友'},
	{'id':'2','value':'女性朋友'},
];
</script>
</head>
<body>
<?php require_once '../top_mini.php';?>
<div class="tabmenu tabmenu_4">
    <li<?php echo ($t == 1)?' class="ed"':''; ?> onclick="openlinks('<?php echo $SELF; ?>?t=1');"><span>我发出的</span></li>
    <li<?php echo ($t == 2)?' class="ed"':''; ?> onclick="openlinks('<?php echo $SELF; ?>?t=2');"><span>我收到的</span></li>
    <li<?php echo ($t == 3)?' class="ed"':''; ?> onclick="openlinks('<?php echo $SELF; ?>?t=3');"><span>发红包</span></li>
    <li<?php echo ($t == 4)?' class="ed"':''; ?> onclick="openlinks('<?php echo $SELF; ?>?t=4');"><span>讨红包</span></li>
</div>
<main class="C">
    	<?php if ($t == 1){ ?>
        
      		<table class="table0 navu"><tr>
            <td height="60" align="left" >
            <a href="<?php echo $SELF; ?>?t=<?php echo $t; ?>"<?php echo ($k == '')?' class="ed"':''; ?>>全部</a>
            <a href="<?php echo $SELF; ?>?t=<?php echo $t; ?>&k=1"<?php echo ($k == 1)?' class="ed"':''; ?>>我发出的红包</a>
            <a href="<?php echo $SELF; ?>?t=<?php echo $t; ?>&k=2"<?php echo ($k == 2)?' class="ed"':''; ?>>我求讨的红包</a>
            </td></tr>
            </table>            
            <?php
			if ($k == 1){
				$sqll = " AND (kind=1 OR kind=2)";
			}elseif($k == 2){
				$sqll = " AND kind=3";
			}else{
				$sqll = "";
			}
			$SQL = "SELECT id,kind,amount,num,money,addtime,flag FROM ".__TBL_HONGBAO__." WHERE uid=".$cook_uid.$sqll." ORDER BY id DESC";
            $rt = $db->query($SQL);
            $total = $db->num_rows($rt);
            if ($total <= 0 ) {
                echo "<br><br><div class='nodatatips W300'><i class='hongbao60'></i><br>您暂时还没有发过红包<br><a href='hongbao.php?t=3' style='margin:20px 0 10px 0;display:inline-block' class='aHONG'>我要发红包</a></div>";
            } else {$page_stylesize = 1;require_once '../page.php';$pagesize=$_ZEAI['pagesize'];if ($p<1 || empty($p))$p=1;$mypage=new zeaipage($total,$pagesize);$pagelist = $mypage->pagebar();$db->data_seek($rt,($p-1)*$pagesize);?>
  <script>
            var checkflag = "false";
            var bg='';
            var bg1      = '<?php echo $_Style['list_bg1']; ?>';
            var bg2      = '<?php echo $_Style['list_bg2']; ?>';
            var overbg   = '<?php echo $_Style['list_overbg']; ?>';
            var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
            </script>
  <script src="../list.js"></script>
            <form name="FORM" id="zeai_cnFORM" method="post" action="<?php echo $SELF; ?>">
            <table class="tablelist">
            <?php
            for($i=1;$i<=$pagesize;$i++) {
                $rows = $db->fetch_array($rt);
                if(!$rows) break;
                $id      = $rows[0];
                $kind    = $rows[1];
				$amount  = $rows[2];
				$num     = $rows[3];
				$money   = $rows[4];
				$addtime = $rows[5];
				$addtime_str = YmdHis($addtime);
                $flag    = $rows[6];
				//
				$money_alone = '';
				$href = '../detail.php?fid='.$id;
				if ($kind == 3){
					$money_str = '不限';
					if ($money == 0){
						$money_alone = '不限';
					}else{
						$money_alone = $money.'元';
					}
					$num_str = '不限';
					$acls = 'aLVed';
					$icocls = ' LV';
				}else{
					if ($kind == 1){
						$money_alone = '随机';
					}else{
						$money_alone = $amount.'元';
					}
					$money_str = $money.'元';
					$num_str = $num;
					$acls = 'aHONGed';
					$icocls = '';
				}
				$difftime = $ADDTIME - $addtime;
				//运气或定额过期超时退款
				if ( $difftime > $_ZEAI['HB_refundtime']*86400 && ($kind == 1 || $kind == 2) && $flag==1 ){
					$db->query("UPDATE ".__TBL_HONGBAO__." SET flag=2 WHERE id=".$id);
					//统计已抢金额
					$rt=$db->query("SELECT SUM(money) FROM ".__TBL_HONGBAO_USER__." WHERE fid=".$id);
					$row = $db->fetch_array($rt);
					$nomoney = intval($row[0]);
					$endtotalmoney = $money - $nomoney;
					if ($endtotalmoney > 0){
						//money_list
						$endnum  = $endtotalmoney + $data_money;
						$db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$cook_uid);
						$content = '红包未抢完退款';
						//$db->AddHistoryList($cook_uid,$content,$endtotalmoney,1);
						$db->AddLovebRmbList($cook_uid,$content,$endtotalmoney,'money',15);
						//weixin_mb
						if (!empty($data_openid)){
							$first  = $cook_nickname."您好，您的余额账户有变动(红包退款)：";
							$remark = $content."，查看详情";
							$url    = HOST."/?z=my&e=my_money";
							wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid.'&money='.$endtotalmoney.'&money_total='.$endnum.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
						}
					}
					//
					$flag = 2;
				}
            ?>
            <tr <?php echo tr_mouse($i,$id);?>>
            <input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="chk<?php echo $id; ?>" style="display:none">
            <td width="60"><a href="<?php echo $href; ?>" class="hongbao50<?php echo $icocls; ?>"></a></td>
            <td width="50" class="center"><?php echo $money_str; ?></td>
            <td class="center S12" style="color:#999;line-height:150%">
            <?php
			if ($flag == 0){
				echo '<font class="S16 C999">审核中</font>';
			}elseif($flag == 1){
				$totals  = $_ZEAI['HB_refundtime']*86400 - $difftime;
				$day     = intval($totals/86400 );
				$hour    = intval(($totals % 86400)/3600);
				$hourmod = ($totals % 86400)/3600 - $hour;
				$minute  = intval($hourmod*60);
				$outtime  = "";
				$outtime .= ($day > 0)?"<span class='Cf00'>$day</span> 天 ":"";
				$outtime .= ($hour > 0)?"<span class='Cf00'>$hour</span> 小时 ":"";
				$outtime .= ($minute > 0)?"<span class='Cf00'>$minute</span> 分钟 ":"";
				echo '<font class="S16 Cf60">进行中</font>';
				if (!empty($outtime))echo '<br>离结束还有<br>'.$outtime;
			}elseif($flag == 2){
				echo '<font class="S16 C090">结束或过期</font>';
           	}?>            
            </td>
            <td width="60" align="right"><a href="<?php echo $href; ?>" class="<?php echo $acls; ?>">详情</a></td>
            </tr>
            <?php } ?>
            </table>
           <table class="table0">
            <tr>
            <td height="60" align="right" valign="bottom" class="list_page"><?php if ($total > $pagesize){ echo $pagelist; } ?></td>
            </tr>
            </table>
		  </form>
            <?php }?>

   	  <?php }elseif ($t == 2){ ?>
            <table class="table0 navu"><tr>
            <td height="60" align="left" >
            <a href="<?php echo $SELF; ?>?t=<?php echo $t; ?>"<?php echo ($k == '')?' class="ed"':''; ?>>全部</a>
            <a href="<?php echo $SELF; ?>?t=<?php echo $t; ?>&k=1"<?php echo ($k == 1)?' class="ed"':''; ?>>运气红包</a>
            <a href="<?php echo $SELF; ?>?t=<?php echo $t; ?>&k=2"<?php echo ($k == 2)?' class="ed"':''; ?>>定额红包</a>
            <a href="<?php echo $SELF; ?>?t=<?php echo $t; ?>&k=3"<?php echo ($k == 3)?' class="ed"':''; ?>>打赏</a>
            </td></tr>
            </table>            
            <?php
			$k = intval($k);
			if (!empty($k))$sqll = " AND b.kind=$k";
			$currfields = ($k == 3)?",a.content":"";
			$SQL = "SELECT a.id,a.money,a.addtime".$currfields.",b.kind,b.id AS fid FROM ".__TBL_HONGBAO_USER__." a,".__TBL_HONGBAO__." b WHERE a.fid=b.id AND a.uid=".$cook_uid.$sqll." ORDER BY a.id DESC";
            $rt = $db->query($SQL);
            $total = $db->num_rows($rt);
            if ($total <= 0 ) {
                echo "<br><br><div class='nodatatips W300'><i class='hongbao60'></i><br>暂时还没有抢到红包<br><a href='../' style='margin:20px 0 10px 0;display:inline-block' class='aHONG'>我要抢红包</a></div>";
            } else {$page_stylesize = 1;require_once '../page.php';$pagesize=$_ZEAI['pagesize'];if ($p<1 || empty($p))$p=1;$mypage=new zeaipage($total,$pagesize);$pagelist = $mypage->pagebar();$db->data_seek($rt,($p-1)*$pagesize);?>
  <script>
            var checkflag = "false";
            var bg='';
            var bg1      = '<?php echo $_Style['list_bg1']; ?>';
            var bg2      = '<?php echo $_Style['list_bg2']; ?>';
            var overbg   = '<?php echo $_Style['list_overbg']; ?>';
            var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
            </script>
  <script src="../list.js"></script>
            <form name="FORM" id="zeai_cnFORM" method="post" action="<?php echo $SELF; ?>">
            <table class="tablelist">
            <?php
            for($i=1;$i<=$pagesize;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows) break;
                $id      = $rows['id'];
                $kind    = $rows['kind'];
				$money   = $rows['money'];
				$addtime = $rows['addtime'];
				$addtime_str = YmdHis($addtime);
                $fid     = $rows['fid'];
				$content = ($kind == 3)?dataIO($rows['content'],'out'):'';
				$href = '../detail.php?fid='.$fid;
				if ($kind == 3){
					$acls = 'aLVed';	
					$icocls = ' LV';
				}else{
					$acls = 'aHONGed';	
					$icocls = '';
				}
            ?>
            
            <tr <?php echo tr_mouse($i,$id);?>>
            <input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="chk<?php echo $id; ?>" style="display:none">
            <td width="60"><a href="<?php echo $href; ?>"  class="hongbao50<?php echo $icocls; ?>"><?php echo $img_str; ?></a></td>
            <td width="50" class="center"><?php echo $money; ?>元</td>
            <td width="80" class="C8d center S12"><?php echo $addtime_str;?></td>
            <td class="S12 C999"><?php if ($kind == 3){echo $content;}else{echo '&nbsp;';} ?></td>
            <td width="50" class="center" ><a href="<?php echo $href; ?>" class="<?php echo $acls; ?>">详情</a></td>
            </tr>
            <?php } ?>
            </table>
            <?php  if ($total > $pagesize){ ?>
           <table class="table0"><tr>
            <td height="60" align="center" class="list_page"><?php echo $pagelist; ?></td>
            </tr>
            </table>
            <?php }?>
  			</form>
            <?php }?>

   	  <?php }elseif ($t == 3){ ?>
      
      <script>
      </script>
            <form action="<?php echo $SELF; ?>" id="GYLform3" method="post" class="form">
            <dl><dt>谁可以抢</dt><dd><script>zeai_cn__CreateFormItem('select','sex','<?php echo $sex; ?>','',sex_ARR);</script></dd></dl>
            <dl><dt>&nbsp;</dt><dd><script>zeai_cn__CreateFormItem('select','age1','20','',age_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','age2','<?php echo $age2; ?>','',age_ARR);</script></dd></dl>
            <dl><dt>&nbsp;</dt><dd><script>zeai_cn__CreateFormItem('select','heigh1','150','',heigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','heigh2','<?php echo $heigh2; ?>','',heigh_ARR);</script></dd></dl>
            <dl><dt>&nbsp;</dt><dd><div class="checkbox"><input <?php echo ($ifphoto == 1)?'checked':''; ?> type="checkbox" id="ruleout" name="ruleout" value="1"><label for="ruleout" class="check-box"><span>已抢过我的红包除外</span></label></div></dd></dl>
            <dl><dt>红包种类</dt><dd>
            
            
            <input type="radio" name="kind" id="kind1" class="radioskin" value="1" checked><label for="kind1" class="radioskin-label"><i class="i1"></i><b class="W80">运气红包</b></label>
            <input type="radio" name="kind" id="kind2" class="radioskin" value="2"><label for="kind2" class="radioskin-label"><i class="i1"></i><b class="W80">定额红包</b></label>

                
                
            </dd></dl>
            <dl id="amountobj"><dt id="amount_t">红包总金额</dt><dd class="jjbox"><a>-</a><input name="amount" type="text" id="amount" autocomplete="off" value="20" maxlength="4"><a>+</a><span class="dw">(元)</span></dd></dl>
            <dl id="numobj"><dt>红包数量</dt><dd class="jjbox"><a>-</a><input name="num" type="text" id="num" autocomplete="off" value="5" maxlength="3"><a>+</a><span class="dw">(个)</span></dd></dl>
            <dl><dt>支付金额</dt><dd><span class="Cf00">￥<font id="money_t">20</font>元</span>　当前余额：<font class="C090">￥<?php echo $data_money; ?>元</font></dd></dl>
            <dl><dt>红包祝福</dt><dd><!--<input readonly name="content" id="content" value="恭喜发财，大吉大利" class="input"> -->
            <select name="content" class="select W90_">
              <option value="恭喜发财，大吉大利！">恭喜发财，大吉大利！</option>
              <option value="有钱就是任性">有钱就是任性</option>
              <option value="人傻，钱多，就是我">人傻，钱多，就是我</option>
              <option value="拿走，不送">拿走，不送</option>
              <option value="快去 买 买 买">快去 买 买 买</option>
              <option value="如果心是近的，再远的路也是短的，孤单的人那么多，快乐的没有几个，祝大家天天快乐！">如果心是近的，再远的路也是短的，孤单的人那么多，快乐的没有几个，祝大家天天快乐！</option>
              <option value="多年以前我就已在我的生命里邀约了你，所以今天的相遇，我很珍惜！就算距离再长，也有我的牵挂！">多年以前我就已在我的生命里邀约了你，所以今天的相遇，我很珍惜！就算距离再长，也有我的牵挂！</option>
              <option value="但愿我寄予您的祝福是最新鲜最令你百读不厌的，祝福你新年快乐，万事如意！祝您新年快乐">但愿我寄予您的祝福是最新鲜最令你百读不厌的，祝福你新年快乐，万事如意！祝您新年快乐</option>
              <option value="作光棍很多年了吧，想天上掉下个林妹妹吗？">作光棍很多年了吧，想天上掉下个林妹妹吗？</option>
              <option value="听说，和漂亮的女人交往养眼，和聪明的女人交往养脑，和健康的女人交往养身，和快乐的女人交往养心。和你交往全养啦！">听说，和漂亮的女人交往养眼，和聪明的女人交往养脑，和健康的女人交往养身，和快乐的女人交往养心。和你交往全养啦！</option>
              <option value="阳光是我的祝福，月光是我的祈祷，轻风是我的呢喃，细雨是我的期望">阳光是我的祝福，月光是我的祈祷，轻风是我的呢喃，细雨是我的期望</option>
              <option value="但愿你的眼睛，只看得到笑容，但愿你流下每一滴泪，都让人感动，但愿你以后每一场梦，都让人感动！">但愿你的眼睛，只看得到笑容，但愿你流下每一滴泪，都让人感动，但愿你以后每一场梦，都让人感动！</option>
              <option value="在一年的每个日子，在一天每个小时，在一小时的每一分钟，在一分钟的每一秒，我都在想你。亲爱的，节日快乐！">在一年的每个日子，在一天每个小时，在一小时的每一分钟，在一分钟的每一秒，我都在想你。亲爱的，节日快乐！</option>
            </select>
            </dd></dl>
            <dl><dt>&nbsp;</dt><dd>
            <input name="submitok" type="hidden" value="addupdate" />
            <input name="t" type="hidden" value="<?php echo $t; ?>" />
            <input name="areaid" id="areaid" type="hidden" value="" />
            <input name="areatitle" id="areatitle" type="hidden" value="" />
            <input class="btn2" type="button" id="submit3" value="确定" style="line-height:40px;height:40px;border-radius:3px" />
            </dd></dl>
            </form>
            <!--提示开始-->
            <div class="tipsbox">
            <div class="tipst">红包发起规则：</div>
            <div class="tipsc">
            ● 我们不做毛毛分分的红包，一律一元起发<br />
            ● 如果72小时内未被领取，余额将自动退还到您的账户</div>
            </div>
            <!--提示结束-->
			<script>input("GYLform3");</script>
            <script src="../zeai_win.js"></script>
    	<?php }elseif ($t == 4){ ?>
            <form action="<?php echo $SELF; ?>" id="GYLform4" method="post" class="form">
            <dl><dt>当前余额</dt><dd><font class="C090">￥<?php echo $data_money; ?>元</font></dd></dl>
            <dl><dt>红包金额</dt><dd>
                <select id="money" name="money" class="select W90_"><option value="0">不限金额，随意就好</option>
                <option value="2">2元以上</option><option value="5">5元以上</option>
                <option value="10">10元以上</option><option value="20">20元以上</option>
                <option value="50">50元以上</option><option value="100">100元以上</option>
                </select>
            </dd></dl>
            <dl><dt>红包寄语</dt><dd><!--<input readonly name="content" id="content" value="恭喜发财，大吉大利" class="input W400"> -->
            <select name="content" class="select W90_">
				<option value="恭喜发财，红包拿来">恭喜发财，红包拿来</option>
                <option value="土豪，咱做个朋友吧">土豪，咱做个朋友吧</option>
				<option value="谁给我红包我就跟谁好^_^">谁给我红包我就跟谁好^_^</option>
				<option value="我们之间除了新年快乐，难道没有红包吗?！">我们之间除了新年快乐，难道没有红包吗?</option>
				<option value="发我多少，你就瘦多少!">发我多少，你就瘦多少!</option>
				<option value="那些年我错过了你，但今天，我不想再错过你的红包!">那些年我错过了你，但今天，我不想再错过你的红包!</option>
				<option value="这世间难道就没有一点点大红包么?">这世间难道就没有一点点大红包么?</option>
				<option value="一个人，我们不必看他平时的表现，只要在过年这几天他发出的红包数额是远远超过收到的红包数的，我们就可以认定他是一个高尚的人，是一个纯粹的人，是一个脱离了低级趣味的人。">一个人，我们不必看他平时的表现，只要在过年这几天他发出的红包数额是远远超过收到的红包数的，我们就可以认定他是一个高尚的人，是一个纯粹的人，是一个脱离了低级趣味的人。</option>
				<option value="但愿你的眼睛能用红包表达感情的，就不要发些新年快乐什么的祝福了，祝福又不一定会如愿，但红包是一定可以提现的。">但愿你的眼睛能用红包表达感情的，就不要发些新年快乐什么的祝福了，祝福又不一定会如愿，但红包是一定可以提现的。</option>
				<option value="长得好看的人已经给我发红包了，长得丑的还在犹豫。">长得好看的人已经给我发红包了，长得丑的还在犹豫。</option>
            </select>
            </dd></dl>
            <dl><dt>&nbsp;</dt><dd>
            <input name="submitok" type="hidden" value="addupdate4" />
            <input name="t" type="hidden" value="<?php echo $t; ?>" />
            <input class="btn2" type="button" id="submit4" value="确定" />
            </dd></dl>
            </form>
            <!--提示开始-->
            <div class="tipsbox">
            <div class="tipst">索红包发起规则：</div>
            <div class="tipsc">
            ● 必须上传个人形象照片且通过审核。<a href="<?php echo HOST;?>/?z=my&e=my_info" class="aLAN">上传形象照</a><br />
            ● 必须个人基本资料完整且通过审核。<a href="<?php echo HOST;?>/?z=my&e=my_info" class="aLAN">修改基本资料</a></div>
            </div>
            <!--提示结束-->
		  <script>input("GYLform4");</script>
          <script src="../zeai_win.js"></script>
        <?php }?>
	</div>
    <!-- end C -->
</main>
<script src="../my_hongbao.js?2"></script>
<script src="../win_confirm.js"></script>
<?php require_once '../bottom.php';?>
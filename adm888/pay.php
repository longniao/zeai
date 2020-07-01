<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('pay_qd',$QXARR))exit(noauth());
switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $value){$v=intval($value);
				$db->query("DELETE FROM ".__TBL_PAY__." WHERE flag=0 AND id=".$v);
			}
			//
			$list = (is_array($list))?implode(',',$list):'';$uid=0;
			switch ($pkind) {
				case 1:$showalt  = '会员升级清单';break;
				case 2:$showalt  = '爱豆充值清单';break;
				case 3:$showalt  = '余额充值清单';break;
				case 4:$showalt  = '活动报名费清单';break;
				case 5:$showalt  = '推广员升级清单';break;
				case 5:$showalt  = '推广员激活清单';break;
				case 5:$showalt  = '实名认证清单';break;
			}
			AddLog('批量删除在线支付“'.$showalt.'”(未付款)，清单id->【'.$list.'】');
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
}
$SQL = "";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL = " AND (uid=$Skeyword OR tg_uid=$Skeyword) ";
}
if (ifint($uid)){
	$SQL = " AND uid=$uid ";
	$_ADM['admPageSize']=6;
}
if(!empty($Skeyword2)){
	$SQL = " AND ( title LIKE '%".$Skeyword2."%' )";
}
if(!empty($Skeyword3)){
	$SQL = " AND ( orderid LIKE '%".$Skeyword3."%' )";
}
$pkind=intval($pkind);
if (ifint($pkind))$SQL .= " AND kind=".$pkind;

if ($flag == 'flag1')$SQL .= " AND flag=1";
if ($flag == 'flag0')$SQL .= " AND flag=0";

$rt = $db->query("SELECT * FROM ".__TBL_PAY__." WHERE kind<>-1 ".$SQL." ORDER BY id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
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
.table0{min-width:900px;width:98%;margin:0 20px 20px 20px}
.table0 form{float:left}
.ped{color:#FF5722;border-bottom:2px #FF5722 solid;padding-bottom:5px}
</style>
<?php
?>
<body>
<?php if (!ifint($uid)){?>
<div class="navbox">
    <a href="pay.php" class="ed">在线支付清单<b><?php echo $total;?></b></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0">
    <tr>
    <td align="left" class="S14" style="padding-top:10px">
    <?php if (!ifint($uid)){?>
    <form name="ZEAI_CN__form0" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword3" type="text" id="Skeyword3" maxlength="25" class="input size2 W150" placeholder="按支付订单号" value="<?php echo $Skeyword3; ?>">
        <input name="submitok" type="hidden" value="u" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="pkind" value="<?php echo $pkind;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>
    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>" style="margin-left:30px">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按UID/推广员ID" value="<?php echo $Skeyword; ?>">
        <input name="submitok" type="hidden" value="u" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="pkind" value="<?php echo $pkind;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>
    <form name="ZEAI_CN__form2" method="get" action="<?php echo SELF; ?>" style="margin-left:30px">
        <input name="Skeyword2" type="text" id="Skeyword2" maxlength="25" class="input size2 W150" placeholder="按支付项目内容" value="<?php echo $Skeyword2; ?>">
        <input name="submitok" type="hidden" value="c" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="pkind" value="<?php echo $pkind;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>  
	<?php }?>　
    </td>
    <td align="right" class="S14" style="padding-top:10px">
    <a href="<?php echo SELF;?>?pkind=<?php echo $pkind;?>&uid=<?php echo $uid;?>&flag=flag1"<?php echo ($flag == 'flag1')?' class="btn size2 LV"':' class="btn size2 "';?> style="font-size:14px">付款成功</a>　
    <a href="<?php echo SELF;?>?pkind=<?php echo $pkind;?>&uid=<?php echo $uid;?>&flag=flag0"<?php echo ($flag == 'flag0')?' class="btn size2 HUI"':' class="btn size2 "';?> style="font-size:14px">未付款</a>
    </td>
    </tr>
</table>
<?php }else{echo '<br>';}?>

<table class="table0"><tr><td align="left" class="S14" style="padding-bottom:5px;color:#aaa">
    <a href="pay.php?uid=<?php echo $uid;?>"<?php echo (empty($pkind))?' class="ped"':' class="C999"';?>>全部</a>　|　
    <a href="pay.php?pkind=1&uid=<?php echo $uid;?>"<?php echo ($pkind == 1)?' class="ped"':' class="C999"';?>>用户升级</a>　|　
    <a href="pay.php?pkind=2&uid=<?php echo $uid;?>"<?php echo ($pkind == 2)?' class="ped"':' class="C999"';?>><?php echo $_ZEAI['loveB'];?>充值</a>　|　
    <a href="pay.php?pkind=3&uid=<?php echo $uid;?>"<?php echo ($pkind == 3)?' class="ped"':' class="C999"';?>>余额充值</a>　|　
    <a href="pay.php?pkind=4&uid=<?php echo $uid;?>"<?php echo ($pkind == 4)?' class="ped"':' class="C999"';?>>活动报名</a>　|　
    <a href="pay.php?pkind=8&uid=<?php echo $uid;?>"<?php echo ($pkind == 8)?' class="ped"':' class="C999"';?>>文章打赏</a>　|　
    <a href="pay.php?pkind=9&uid=<?php echo $uid;?>"<?php echo ($pkind == 9)?' class="ped"':' class="C999"';?>>用户注销</a>　|　
    <a href="pay.php?pkind=7&uid=<?php echo $uid;?>"<?php echo ($pkind == 7)?' class="ped"':' class="C999"';?>>用户实名认证</a>　|　
    
    <?php if (!ifint($uid)){?>
    <a href="pay.php?pkind=5&uid=<?php echo $uid;?>"<?php echo ($pkind == 5)?' class="ped"':' class="C999"';?>>推广员升级</a>　|　
    <a href="pay.php?pkind=6&uid=<?php echo $uid;?>"<?php echo ($pkind == 6)?' class="ped"':' class="C999"';?>>推广员激活</a>　|　
    <a href="pay.php?pkind=11&uid=<?php echo $uid;?>"<?php echo ($pkind == 11)?' class="ped"':' class="C999"';?>>商家入驻</a>　|　
    <a href="pay.php?pkind=12&uid=<?php echo $uid;?>"<?php echo ($pkind == 12)?' class="ped"':' class="C999"';?>>商品购买</a>
    <?php }?>
    
</td></tr></table>

<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70">ID</th>
    <th width="220" align="left">支付人</th>
    <th width="200" align="left">支付订单号</th>
    <th width="80" align="left">类型</th>
    <th width="100">订单金额(元)</th>
    <th width="100">实际支付(元)</th>
	<th width="200" align="left">支付项目</th>
	<th align="left">TRADE_NO</th>
    <th width="70">订单状态</th>
    <th width="80">时间</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];
		$kind = $rows['kind'];
		if ($kind == 5 || $kind == 6 || $kind == 11 || $kind == 12){
			$uid = $rows['tg_uid'];
			$row2 = $db->ROW(__TBL_TG_USER__,"uname,grade,nickname","id=".$uid);
			if ($row2){
				$grade     = $row2['grade'];
				$nickname = dataIO($row2['nickname'],'out');
			}
		}else{
			$uid = $rows['uid'];
			$row2 = $db->ROW(__TBL_USER__,"uname,sex,grade,nickname","id=".$uid);
			if ($row2){
				$sex       = $row2['sex'];
				$grade     = $row2['grade'];
				$nickname = dataIO($row2['nickname'],'out');
			}
		}
		$uname = strip_tags($row2['uname']);
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$nickname = dataIO($row2['nickname'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex       = $row2['sex'];
		$grade     = $row2['grade'];
		if(empty($nickname))$nickname = $uname;
		
		
		$orderid = $rows['orderid'];
		$orderid = str_replace($Skeyword3,"<font color=red><b>".$Skeyword3."</b></font>",$orderid);
		$title = dataIO($rows['title'],'out');
		$title = str_replace($Skeyword2,"<font color=red><b>".$Skeyword2."</b></font>",$title);
		$money  = $rows['money'];
		$paymoney  = $rows['paymoney'];
		$flag  = $rows['flag'];
		$kind  = $rows['kind'];
		$trade_no  = $rows['trade_no'];
		$addtime = YmdHis($rows['addtime']);
		$bz = dataIO($rows['bz'],'out');
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
   	  <td width="70" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="220" align="left">
        <?php
		if ($kind == 5 || $kind == 6 || $kind == 11 || $kind == 12){
			echo '<span class="middle">';
			echo $nickname."　<font class='S12 C999'>(ID：".$uid.")</font>";
			echo '</span>';
		}else{
			echo uicon($sex.$grade);
			echo '<span class="middle">';
			echo $nickname."　<font class='S12 C999'>(uid：".$uid.")</font>";
			echo '</span>';
		}
		?>
        </td>
        <td width="200" align="left"><?php echo $orderid;?></td>
        <td width="80" align="left"><?php if($kind == 1){echo'会员升级';}elseif($kind==2){echo $_ZEAI['loveB'].'充值';}elseif($kind==3){echo '余额充值';} ?></td>
      <td width="100" align="left" class="Cf00"><?php echo $money;?></td>
      <td width="100" align="left" class="Cf00"><?php echo $paymoney;?></td>
      <td width="200" align="left" style="padding:10px 0;line-height:150%;color:#999"><b class="C666"><?php echo $title;?></b><br><?php echo $bz;?></td>
      <td align="left"><?php echo $trade_no;?></td>
	<td width="70"><?php
		if($flag == 1){
			echo '<font class="C090 B">成功</font>';
		}elseif($flag==-2){
			echo '<font class="C00f">已退款</font>';
		}else{
			echo '<font class="C999">未付款</font>';
		}
	 ?></td>
      <td width="80" class="C999"><?php echo $addtime; ?></td>
    </tr>
	<?php } ?>
</table>
<div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">删除未付款</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);"><i class="ico">&#xe676;</i> 发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
    <input type="hidden" name="pkind" value="<?php echo $pkind;?>" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
</div>
<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'pay'+zeai.ajxext+'submitok=alldel&pkind=<?php echo $pkind;?>',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</form>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';ob_end_flush(); ?>
<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('tx',$QXARR))exit(noauth());
if ($submitok == 'ajax_delupdate'){
	if ( !ifint($id))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_PAY__,"uid,money,paymoney,flag,money_list_id,tg_uid","id=".$id,"num");
	if ($row){
		$uid      = $row[0];
		$money    = $row[1];
		$paymoney = $row[2];
		$flag     = $row[3];
		$money_list_id = $row[4];
		$tg_uid = $row[5];
		if($flag==1)json_exit(array('flag'=>0,'msg'=>'已打款的提现记录不可删除'));
		if($t=='tg'){
			//推广员信息
			$row = $db->ROW(__TBL_TG_USER__,"nickname,money,openid","id=".$tg_uid,"num");
			if ($row){
				$data_nickname = dataIO($row[0],'out');
				$data_money = $row[1];
				$data_openid= $row[2];
			}else{json_exit(array('flag'=>0,'msg'=>'forbidden'));}
			$endnum = $data_money + $money;
			//执行资金变动
			$db->query("UPDATE ".__TBL_TG_USER__." SET money=$endnum WHERE id=".$tg_uid);
			$db->query("UPDATE ".__TBL_MONEY_LIST__." SET content='申请提现' WHERE id=".$money_list_id);
			//余额清单入库
			$db->AddLovebRmbList($tg_uid,'申请提现<span class=\"C999 S12\">（驳回申请）</span>',$money,'money',2,'tg');	
			//余额站内消息
			$C = $data_nickname.'您好，申请提现被驳回！';
			$db->SendTip($tg_uid,'申请提现被驳回',dataIO($C,'in'),'tg');
			AddLog('【提现审核】推广员【'.$data_nickname.'（id:'.$tg_uid.'）】->驳回提现，金额：￥'.$money);

		}else{
			//会员信息
			$row = $db->NUM($uid,"nickname,money,openid");
			if ($row){
				$data_nickname = dataIO($row[0],'out');
				$data_money = $row[1];
				$data_openid= $row[2];
			}else{json_exit(array('flag'=>0,'msg'=>'forbidden'));}
			$endnum = $data_money + $money;
			//执行资金变动
			$db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$uid);
			$db->query("UPDATE ".__TBL_MONEY_LIST__." SET content='申请提现' WHERE id=".$money_list_id);
			//余额清单入库
			$db->AddLovebRmbList($uid,'申请提现<span class=\"C999 S12\">（驳回申请）</span>',$money,'money',2);	
			//余额站内消息
			$C = $data_nickname.'您好，申请提现被驳回！<br><br><br><a href='.Href('money').' class=aQING>查看详情</a>';
			$db->SendTip($uid,'申请提现被驳回',dataIO($C,'in'),'sys');
			AddLog('【提现审核】会员【'.$data_nickname.'（uid:'.$uid.'）】->驳回提现，金额：￥'.$money);
		}
		
		//账户资金变动提醒
		if (!empty($data_openid)){
			$first  = urlencode($data_nickname."您好，您的余额账户资金有变动：");//$_ZEAI['loveB']
			$remark = urlencode("申请提现被驳回");
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_CHANGE&openid='.$data_openid.'&num='.$money.'&endnum='.$endnum.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('money')));
		}
		//删除支付记录
		$db->query("DELETE FROM ".__TBL_PAY__." WHERE id=".$id);
	}
	json_exit(array('flag'=>1,'msg'=>'驳回删除成功'));
}elseif($submitok == 'ajax_tx_hand_update'){
	if ( !ifint($id))json_exit(array('flag'=>0,'msg'=>'forbidden'));
	$rt = $db->query("SELECT money_list_id,paymoney FROM ".__TBL_PAY__." WHERE kind=-1 AND id=".$id,"name");
	if ($db->num_rows($rt)) {
		$row = $db->fetch_array($rt,"name");
		$money_list_id = $row['money_list_id'];
		$paymoney = $row['paymoney'];
		$db->query("UPDATE ".__TBL_PAY__." SET paytime=".ADDTIME.",flag=1 WHERE id=".$id);
		$content = "申请提现<span style=\"color:#090;font-size:12px\">（打款成功）</span>";
		$db->query("UPDATE ".__TBL_MONEY_LIST__." SET content='$content' WHERE id=".$money_list_id);
		AddLog('【人工提现打款】支付id号【'.$id.'】->打款成功，金额：￥'.$paymoney);
	}
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
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
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:900px;margin:0 20px 50px 20px}
.table0{min-width:900px;width:98%;margin:0 20px 20px 20px}
.table0 form{float:left;}
</style>
<body>
<?php
if($submitok == 'tx_hand'){
	if ( !ifint($id))callmsg("forbidden","-1");
	$rt = $db->query("SELECT id,uid,paymoney,flag,addtime,tg_uid FROM ".__TBL_PAY__." WHERE id=".$id);
	if (!$db->num_rows($rt)){
		exit('不好意思，没找到');
	}else{
		$rows = $db->fetch_array($rt,'name');
		$uid = $rows['uid'];
		
		$row = $db->NUM($uid,"truename,nickname,mob,openid,photo_s,weixin");
		if ($row){
			$data_truename = dataIO($row[0],'out');
			$data_nickname = dataIO($row[1],'out');
			$data_mob      = dataIO($row[2],'out');
			$data_openid   = $row[3];
			$data_photo_s  = $row[4];
			$data_weixin   = $row[5];
		}
		$tg_uid     = intval($rows['tg_uid']);
		$pay_money  = $rows['paymoney'];
		$addtime    = $rows['addtime'];
		$truename   = $data_truename;
		$nickname   = $data_nickname;
		$truename   = (!empty($truename))?'　（'.$truename.'）':'';
		$nickname   = $nickname.$truename.'　UID：'.$uid;
		if($rows['flag'] == 1){?>
            <table width="500" border="0" cellspacing="10" cellpadding="0" style="margin-top:50px;" align="center">
            <tr>
            <td rowspan="2" align="right" valign="top" nowrap="nowrap"><img src="images/close.png"></td>
            <td align="left" valign="middle" nowrap="nowrap" style="color:#F30; font-family:'微软雅黑'; font-size:24px;">亲，付款失败了！信息如下：</td>
            </tr>
            <tr>
            <td align="left" valign="middle">订单已处理或不存，请确认再处理！</td>
            </tr>
            </table>
   <?php }elseif($rows['flag'] == 0){
			if(ifint($tg_uid)){
				$rowtg = $db->ROW(__TBL_TG_USER__,"uname,photo_s,mob,title,bank_name,bank_name_kaihu,bank_truename,bank_card,alipay_truename,alipay_username","id=".$tg_uid,"name");
				if ($rowtg){
					$title = dataIO($rowtg['title'],'out');
					$uname = dataIO($rowtg['uname'],'out');
					$mob   = dataIO($rowtg['mob'],'out');
					$photo_s = $rowtg['photo_s'];
					$alipay_truename = dataIO($rowtg['alipay_truename'],'out');
					$alipay_username = dataIO($rowtg['alipay_username'],'out');
					$bank_name       = dataIO($rowtg['bank_name'],'out');
					$bank_name_kaihu = dataIO($rowtg['bank_name_kaihu'],'out');
					$bank_truename   = dataIO($rowtg['bank_truename'],'out');
					$bank_card       = dataIO($rowtg['bank_card'],'out');
					//
					$title    = (!empty($title))?$title:$uname;
					$nickname = $title.'　ID：'.$tg_uid;
				}
			}
			$mob      = (empty($mob))?$data_mob:$mob;
			$photo_s  = (empty($photo_s))?$data_photo_s:$photo_s;
			$path_s_url = $_ZEAI['up2'].'/'.$photo_s;
			?>
			<style>
            .table {width: 90%;	min-width: 400px;margin-bottom:50px}
            .tdL {width: 120px;	height: 20px;	background: #f9f9f9;	text-align: right;	color: #666;	line-height: 20px;}
            .tdR {text-align: left;	line-height: 20px;}
            .tdL:hover, .tdR:hover {background: #f9f9f9}
            .tdR:hover .input, .tdR:hover textarea, .tdR:hover select {background: #fff}
            </style>
        	<div style="font-size:16px;margin:20px auto;color:#EE5A4E">☆★ 请对此用户进行人工支付宝或银行转账 ★☆</div>
            <table class="table" style="margin-top:10px;">
                <tr>
                <td class="tdL">用户照片</td>
                <td class="tdR"><?php if (!empty($photo_s)) {?>
                <a href="javascript:;" class="pic100"><img src="<?php echo $path_s_url; ?>"></a>
                <?php } else {?>
                <a href="javascript:;" class="nopic">无图</a>
                <?php }?></td>
                </tr>
                
                <tr>
                <td class="tdL">收款人</td>
                <td class="tdR"><?php echo $nickname;?></td>
                </tr>
                <tr>
                <td class="tdL">打款金额</td>
                <td class="tdR Cf00">¥<?php echo $pay_money; ?></td>
                </tr>

                <tr>
                <td class="tdL">手机微信</td>
                <td class="tdR">
				<?php if (!empty($mob)){?><i class="ico S18" style="color:#4FA7FF">&#xe627;</i><?php echo $mob; }?>
				<?php if (!empty($data_weixin)){?>　<i class="ico S18" style="color:#00c250">&#xe607;</i><?php echo $data_weixin;?><?php }?>
                </td>
                </tr>
                
                <?php if (ifint($tg_uid) && $rowtg){?>
                <tr><td class="tdL">支付宝账号</td><td class="tdR"><?php echo $alipay_username;?></td></tr>
                <tr><td class="tdL">支付宝姓名</td><td class="tdR"><?php echo $alipay_truename;?></td></tr>
                <tr><td class="tdL">银行名称</td><td class="tdR"><?php echo $bank_name;?></td></tr>
                <tr><td class="tdL">开户行名称</td><td class="tdR"><?php echo $bank_name_kaihu;?></td></tr>
                <tr><td class="tdL">银行卡号</td><td class="tdR"><?php echo $bank_card;?></td></tr>
                <tr><td class="tdL">卡号姓名</td><td class="tdR"><?php echo $bank_truename;?></td></tr>
                <?php }?>
                <tr>
                <td class="tdL">申请日期</td>
                <td class="tdR"><?php echo YmdHis($addtime);?></td>
                </tr>
            </table>
            <form id="zeai_cn_form">
            <input name="id" type="hidden" value="<?php echo $id;?>">
            <input name="submitok" type="hidden" value="ajax_tx_hand_update">
            <br><br><br><br><div class="savebtnbox"><button type="button" id="submit_add" class="btn size3 HONG2"><i class="ico">&#xe6b1;</i> 确定已转账</button></div>
            </form>
            <script>
			submit_add.onclick=function(){
				zeai.confirm('确定人工转账么？ 此操作将改变提现状态为【打款成功】',function(){
					zeai.ajax({url:'withdraw'+zeai.extname,form:zeai_cn_form},function(e){rs=zeai.jsoneval(e);
						zeai.msg(rs.msg);if(rs.flag==1){setTimeout(function(){parent.location.reload(true);},1000);}
					});
				});
			}
            </script>
			<?php
		}
	}
	exit;
}


$SQL = "a.kind=-1";
$Skeyword = trimhtml($Skeyword);
switch ($sort) {
	default:$SORT = " ORDER BY a.id DESC ";break;
}
if($t=='tg'){
	$SQL .= " AND tg_uid<>''";
	if (ifint($Skeyword)){
		$SQL = " AND a.id=$Skeyword ";
	}elseif(ifmob($Skeyword)){	
		$SQL = " AND b.mob=$Skeyword ";
	}elseif(!empty($Skeyword)){
		$SQL = " AND ( b.uname LIKE '%".$Skeyword."%' )";
	}
	$placeholder='按ID/用户名';
}else{
	if (ifint($Skeyword)){
		$SQL = " AND (a.id=$Skeyword) ";
	}elseif(!empty($Skeyword)){
		$SQL = " AND ( ( b.uname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
	}
	$placeholder='按UID/用户名/昵称';
}

$rt1 = $db->query("SELECT a.*,b.grade,b.uname,b.flag AS uflag,b.title FROM ".__TBL_PAY__." a,".__TBL_TG_USER__." b WHERE a.tg_uid=b.id AND ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total1 = $db->num_rows($rt1);

$rt2 = $db->query("SELECT a.*,b.sex,b.grade,b.nickname,b.flag AS uflag FROM ".__TBL_PAY__." a,".__TBL_USER__." b WHERE a.uid=b.id AND ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total2 = $db->num_rows($rt2);
	
if($t=='tg'){
	$rt = $rt1;
	$total = $total1;
}else{
	$rt = $rt2;
	$total = $total2;
}
?>
<div class="navbox">
    <a href="withdraw.php"<?php echo (empty($t))?' class="ed"':'';?>>用户提现申请<?php if($total2>0)echo '<b>'.$total2.'</b>';?></a>
    <a href="withdraw.php?t=tg"<?php echo ($t=='tg')?' class="ed"':'';?>>推广员/商家 提现申请<?php if($total1>0)echo '<b>'.$total1.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0">
    <tr>
    <td align="left" class="S14" style="padding-top:10px">
    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="<?php echo $placeholder;?>" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="t" value="<?php echo $t;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>
    </td>
    </tr>
</table>
<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="zeaiFORM" method="get" action="<?php echo $SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70">ID</th>
    <th width="200" align="left">提现用户</th>
    <th width="150" align="left">说明</th>
    <th width="110">提现金额(元)</th>
    <th width="110">实际打款金额(元)</th>
    <th width="150">申请时间</th>
    <th align="center">打款时间/交易单号</th>
    <th width="100" align="center">状态/操作</th>
    <th width="80" align="center">驳回</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $rows['uid'];$tg_uid = $rows['tg_uid'];
		$uname = strip_tags($rows['uname']);
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		if(empty($rows['nickname'])){
			$nickname = $uname;
		}
		$uflag = $rows['uflag'];
		//申请提现相关字段
		$money = $rows['money'];
		$paymoney = $rows['paymoney'];
		$trade_no  = $rows['trade_no'];
		$money_list_id = $rows['money_list_id'];
		$addtime = YmdHis($rows['addtime']);
		$paytime = YmdHis($rows['paytime']);;
		$flag = $rows['flag'];
		$title = $rows['title'];
		if($t=='tg'){
			$uid=$tg_uid;
			$title = dataIO($rows['title'],'out');
			$nickname = (!empty($title))?$title:$nickname;
		}
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="50"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="70" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="200" align="left">
        <?php
		if($t=='tg'){
			echo '<span class="middle">';
			echo $nickname."　<font class='S12 C999'>(ID：".$tg_uid.")</font>";
			echo '</span>';
		}else{
			echo uicon($sex.$grade);
			echo '<span class="middle">';
			echo $nickname."　<font class='S12 C999'>(UID：".$uid.")</font>";
			echo '</span>';
		}
		?>
        </td>
        <td width="150" align="left"><?php echo $title;?></td>
      <td width="110" align="left" class="S14"><?php echo $money;?></td>
      <td width="110" align="left" class="S14 Cf00"><?php echo $paymoney;?></td>
      <td width="150" align="left" class="S12"><?php echo $addtime;?></td>
      <td align="center" class="S12 lineH150"><?php if($rows['flag']==1){echo $paytime;}else{echo'未打款';}?>
<?php echo "<br>".$trade_no;?></td>
      <td width="100" align="center" style="padding:15px 0">

<?php
if($flag==1){
	echo'<font class="C090">已打款</font><br>';
}else{
	if($uflag==-1){
		echo'<font class="C999 S12">账户已锁定</font>';
	}elseif($uflag==1 || $uflag==-2){
?>
<a href="javascript:;" class="btn size2 LV2 auto" value="<?php echo $id;?>" nickname="<?php echo $nickname;?>">自动打款</a><br><br>
<a href="javascript:;" class="btn size2 HONG2 hand" value="<?php echo $id;?>" nickname="<?php echo $nickname;?>">手动打款</a>
<?php }}?>      
      
      </td>
      <td width="80" align="center"><?php if($rows['flag']!=1){?>
<a href="javascript:;" value="<?php echo $id;?>" class="delico"></a>
<?php }else{echo'&nbsp;';}?></td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="10">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btnsend2" value="" class="btn size2 disabled action">发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</form>
<?php }?>
<br><br><br>
<script>
zeai.listEach('.auto',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("value")),nickname = obj.getAttribute("nickname");
		zeai.iframe('向【'+nickname+'】打款','<?php echo ($t=='tg')?'tg_u_money_withdraw':'u_money_withdraw';?>'+zeai.ajxext+'id='+id,550,530)
	}
});
zeai.listEach('.hand',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("value")),nickname = obj.getAttribute("nickname");
		zeai.iframe('向【'+nickname+'】打款','withdraw'+zeai.ajxext+'submitok=tx_hand&id='+id,550,530)
	}
});

zeai.listEach('.delico',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("value"));
		zeai.confirm('确认要驳回此提现申请吗？驳回后钱将会退回到该会员账户。',function(){
		zeai.ajax('withdraw'+zeai.ajxext+'submitok=ajax_delupdate&t=<?php echo $t;?>&id='+id,function(e){var rs=zeai.jsoneval(e);
			if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
		});});
	}
});
o('btnsend2').onclick = function() {
	<?php if ($t == 'tg'){?>
		sendTipFnTGU(this);
	<?php }else{ ?>
		sendTipFn2(this);
	<?php }?>
}
function sendTipFnTGU(btnobj){
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要发送的推广员');
		return false;
	}
	var arr = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){
		if (arr[key].checked){
			ulist.push(arr[key].getAttribute("uid"));
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的推广员');
	}else{
		zeai.iframe('发送消息','u_tip.php?kind=TG&ulist='+ulist,600,500);
	}
}

</script>
<?php require_once 'bottomadm.php';?>
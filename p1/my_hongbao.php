<?php
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
if($submitok == 'ajax_chklogin'){
	require_once ZEAI.'sub/conn.php';
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请您先登录后再来发红包','jumpurl'=>Href('hongbao')));
	json_exit(array('flag'=>1,'msg'=>'已登录'));
}
$currfields = "nickname,sex,photo_s,photo_f,money,openid,subscribe,myinfobfb";
require_once 'my_chkuser.php';
$data_sex   = intval($row['sex']);
$data_money = intval($row['money']);
$data_photo_s = $row['photo_s'];
$data_photo_f = intval($row['photo_f']);
$data_openid  =  $row['openid'];
$data_subscribe = $row['subscribe'];
$data_myinfobfb = $row['myinfobfb'];
$data_nickname = dataIO($row['nickname'],'out');
require_once ZEAI.'cache/udata.php';

if ($submitok == 'ajax_chkmoney'){
	$amount = intval($amount);
	$num    = intval($num);
	if ($kind == 1){//随机
		$money = $amount;
		if ($num > $money)exit(json_encode(array('flag'=>'dataerror','msg'=>'红包个数须小于红包总金额')));
	}elseif($kind == 2){//定额
		$money = $amount*$num;
	}else{
		exit(json_encode(array('flag'=>0,'msg'=>'请选择红包种类')));
	}
	$money = abs($money);
	if ($money > $data_money || $data_money <= 0){
		$retarr = array('flag'=>'nomoney','msg'=>'余额不足'.$money.'元，请先充值','jumpurl'=>HOST.'/p1/my_hongbao.php');
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
		json_exit(array('flag'=>'nomoney','msg'=>'余额不足'.$money.'元，请先充值','jumpurl'=>Href('my_hongbao')));
	}
	$db->query("INSERT INTO ".__TBL_HONGBAO__." (uid,sex,areaid,areatitle,age1,age2,heigh1,heigh2,ruleout,kind,amount,num,money,content,addtime) VALUES ($cook_uid,$sex,'$areaid','$areatitle',$age1,$age2,$heigh1,$heigh2,$ruleout,$kind,$amount,$num,$money,'$content',".ADDTIME.")");
	//$hbid = $db->insert_id();
	//////////////////
	$endnum  = intval($data_money - $money);
	$db->query("UPDATE ".__TBL_USER__." SET money=".$endnum." WHERE id=".$cook_uid);
	//money_list
	$content = '发布红包扣除';
	$db->AddLovebRmbList($cook_uid,$content,-$money,'money',13);
	//weixin_mb
	if (!empty($data_openid) && $data_subscribe==1){
		$first  = $data_nickname."您好，您的余额账户有变动：";
		$remark = $content."，查看详情";
		$url    = urlencode(mHref('money'));
		@wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid.'&money=-'.$money.'&money_total='.$endnum.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
	}
	//余额站内消息
	$C = $data_nickname.'您好，您的余额账户有资金变动！　　<a href='.Href('money').' class=aQING>查看详情</a>';
	$db->SendTip($cook_uid,'您的余额账户有变动',dataIO($C,'in'),'sys');
	//////////////////
//	$first  = "有人发红包";
//	$remark = "点击开抢";
//	$url    = HOST."/m1/hongbao/detail.php?fid=".$hbid;
//	@wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid=oQDDf0ciAxM6490BoIGiN3I7asrs&money='.$money.'&money_total='.$money.'&time='.$ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
	//////////////////
	json_exit(array('flag'=>1,'msg'=>'红包发布成功'));
}elseif($submitok == 'addupdate_in'){
	if (empty($data_photo_s) || $data_photo_f==0)json_exit(array('flag'=>'nophoto','msg'=>'请先上传头像并审核通过后再来讨红包吧','jumpurl'=>HOST.'/p1/my_info.php'));
	if(intval($data_myinfobfb)<80)json_exit(array('flag'=>'nophoto','msg'=>'请先完善资料80%以上再来讨红包吧','jumpurl'=>HOST.'/p1/my_info.php'));
	$rt=$db->query("SELECT addtime FROM ".__TBL_HONGBAO__." WHERE kind=3 AND uid=".$cook_uid." ORDER BY id DESC LIMIT 1");
	if ($db->num_rows($rt)){
		$row = $db->fetch_array($rt,'num');
		$addtime2 = $row[0];
		$difftime = ADDTIME - $addtime2;
		if ( $difftime < $_ZEAI['HB_refundtime']*86400 ){
			json_exit(array('flag'=>0,'msg'=>'你很贪心哦，请'.$_ZEAI['HB_refundtime'].'天后再来讨吧'));
		}else{
			$db->query("INSERT INTO ".__TBL_HONGBAO__." (uid,kind,money,content,addtime) VALUES ($cook_uid,3,$money,'$content',".ADDTIME.")");
		}
	}else{
		$db->query("INSERT INTO ".__TBL_HONGBAO__." (uid,kind,money,content,addtime) VALUES ($cook_uid,3,$money,'$content',".ADDTIME.")");
	}
	json_exit(array('flag'=>1,'msg'=>'恭喜你，红包发布成功'));
}
$t = (ifint($t,'1-2','1'))?$t:1;
$zeai_cn_menu = 'my_hongbao';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的红包 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_hongbao.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<script src="<?php echo HOST;?>/cache/udata.js"></script>
<script src="<?php echo HOST;?>/cache/areadata.js"></script>
<script src="<?php echo HOST;?>/res/select3.js"></script>
<script src="js/my_hongbao.js"></script>
</head>
<body>
<?php if ($submitok == 'add'){?>
<style>body{background-color:#fff}</style>
<form id="zeai_cn_FORM" class="form">
<table class="tablelist">
  <tr>
    <td><h1>红包内容</h1></td>
    <td><h1>谁可以抢</h1></td>
  </tr>

  <tr>
    <td width="58%" valign="top">
    <dl><dt>红包种类</dt><dd>
    <input type="radio" name="kind" id="kind1" class="radioskin" value="1" checked><label for="kind1" class="radioskin-label"><i class="i1"></i><b class="W80">运气红包</b></label>
    <input type="radio" name="kind" id="kind2" class="radioskin" value="2"><label for="kind2" class="radioskin-label"><i class="i1"></i><b class="W80">定额红包</b></label>
    </dd></dl>
    <dl id="amountobj"><dt id="amount_t">红包总金额</dt><dd class="jjbox"><a>-</a><input name="amount" type="text" id="amount" autocomplete="off" value="20" maxlength="4" class="input W50"><a>+</a><span class="dw">(元)</span></dd></dl>
    <dl id="numobj"><dt>红包数量</dt><dd class="jjbox"><a>-</a><input name="num" type="text" id="num" autocomplete="off" value="5" maxlength="3" class="input W50"><a>+</a><span class="dw">(个)</span></dd></dl>
    <dl><dt>支付金额</dt><dd><span class="Cf00">￥<font id="money_t">20</font>元</span>　当前余额：<font class="C090">￥<?php echo $data_money; ?>元</font></dd></dl>
    <dl><dt>红包祝福</dt><dd>
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
    
    </td>
    <td valign="top">
    <?php
	$sex = ($cook_sex == 1)?2:$sex;
	?>
    <dl><dd><script>zeai_cn__CreateFormItem('radio','sex','<?php echo $sex; ?>',' class="RCW"',sex_ARR);</script></dd></dl>
    <dl><dd style="width:340px;padding-left:8px"><script>zeai_cn__CreateFormItem('select','age1','20','',age_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','age2','40','',age_ARR);</script></dd></dl>
    <dl><dd style="width:340px;padding-left:8px"><script>zeai_cn__CreateFormItem('select','heigh1','150','',heigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','heigh2','175','',heigh_ARR);</script></dd></dl>
    <dl><dd style="width:340px;padding-left:8px"><input type="checkbox" name="ruleout" id="ruleout" class="checkskin " value="1" <?php echo ($ifphoto == 1)?'checked':''; ?>><label for="ruleout" class="checkskin-label"><i></i><b class="W200">已抢过我的红包除外</b></label></dd></dl>
    <dl><dd style="width:340px;padding-left:8px"><script>LevelMenu3('m1|m2|m3|地区不限|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>',' class="SW area"');</script></dd></dl>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
    <input name="submitok" type="hidden" value="addupdate" />
    <input name="areaid" id="areaid" type="hidden" value="" />
    <input name="areatitle" id="areatitle" type="hidden" value="" /><div class="clear"></div>
    <button type="button" class="btn size4 HONG W200" onClick="hongbao_btn_saveFn();" />保存并发布</button>
    <!--提示开始-->
    <div class="clear"></div>
    <div class="tipsbox" style="padding:0">
        <div class="tipst">红包发起规则：</div>
        <div class="tipsc">
            ● 我们不做毛毛分分的红包，一律一元起发<br />
            ● 如果72小时内未被领取，余额将自动退还到您的账户
        </div>
    </div>
    <!--提示结束-->                
    </td>
    </tr>
</table>
</form>
<?php echo '</body></html>';exit;}elseif ($submitok == 'add_in'){?>
	<style>body{background-color:#fff}.form {width:90%}.form dl dd{text-align:left}.form h1{margin:5px 0 10px 10px;border-bottom:#eee 1px solid;padding-bottom:20px}.form button{margin-top:10px}.form dl{margin:20px 0 10px 0}
</style>
    <form id="zeai_cn_FORM" class="form">
    <h1>讨红包</h1>
    <dl><dt>当前余额</dt><dd><font class="C090">￥<?php echo $data_money; ?>元</font></dd></dl>
    <dl><dt>红包金额</dt><dd>
        <select id="money" name="money" class="select W90_"><option value="0">不限金额，随意就好</option>
        <option value="2">2元以上</option><option value="5">5元以上</option>
        <option value="10">10元以上</option><option value="20">20元以上</option>
        <option value="50">50元以上</option><option value="100">100元以上</option>
        </select>
    </dd></dl>
    <dl><dt>红包寄语</dt><dd>
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
    <input name="submitok" type="hidden" value="addupdate_in" />
    </dd></dl><div class="clear"></div>
    <button type="button" class="btn size4 HONG W200" onClick="hongbao_btn_in_saveFn();" />保存并发布</button>
    <!--提示开始-->
    <div class="clear"></div>
    <div class="tipsbox" style="padding:15px 0 0 0">
        <div class="tipst">索红包发起规则：</div>
        <div class="tipsc">
            ● 必须上传个人形象照片且通过审核。<a href="my_info.php" class="btn size1 BAI" target="_parent">上传形象照</a><br />
            ● 必须个人基本资料完整且通过审核。<a href="my_info.php" class="btn size1 BAI" target="_parent">修改基本资料</a></div>
        </div>
    </div>
    <!--提示结束-->                
    </form>
<?php echo '</body></html>';exit;}?>



<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>我的红包</h1>
        <div class="tab">
            <a href="<?php echo SELF;?>?t=1"<?php echo ($t==1)?' class="ed"':'';?>>我发出的</a>
            <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>我收到的</a>
            <a href="javascript:;" onclick="hongbao_add('out');">我要发红包</a>
            <a href="javascript:;" onclick="hongbao_add('in');">我要讨红包</a>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="my_hongbao" id="main">
            	<?php if ($t == 1){
                    if ($k == 1){
                        $sqll = " AND (kind=1 OR kind=2)";
                    }elseif($k == 2){
                        $sqll = " AND kind=3";
                    }else{
                        $sqll = "";
                    }
                    $rt = $db->query("SELECT id,kind,amount,num,money,addtime,flag FROM ".__TBL_HONGBAO__." WHERE uid=".$cook_uid.$sqll." ORDER BY id DESC");
                    $total = $db->num_rows($rt);
                    ?>
                    <table class=" navu W100_"><tr>
                    <td height="60" align="center" >
                    <a href="<?php echo SELF; ?>?t=<?php echo $t; ?>"<?php echo ($k == '')?' class="hong"':' class="bai"'; ?>>全部</a>　
                    <a href="<?php echo SELF; ?>?t=<?php echo $t; ?>&k=1"<?php echo ($k == 1)?' class="hong"':' class="bai"'; ?>>我发的红包</a>　
                    <a href="<?php echo SELF; ?>?t=<?php echo $t; ?>&k=2"<?php echo ($k == 2)?' class="hong"':' class="bai"'; ?>>我讨的红包</a>
                    </td></tr>
                    </table>
                    <?php
                    if($total>0){
                        $page_skin=2;$pagemode=4;$pagesize=8;$page_color='#E83191';require_once ZEAI.'sub/page.php';?>
                        <table class="tablelist">
                        <tr>
                        <td width="90" class="list_title">&nbsp;</td>
                        <td width="100" class="list_title center">红包类型</td>
                        <td width="90" class="list_title center">金额(元)</td>
                        <td width="90" class="list_title center">单个金额(元)</td>
                        <td width="80" class="list_title center">红包数量</td>
                        <td width="80" class="list_title center">发布时间</td>
                        <td class="list_title center">红包状态</td>
                        <td width="110" class="list_title center">详情</td>
                        </tr>
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
                            $href = Href('hongbao',$id);
                            if ($kind == 3){
                                $money_str = '不限';
                                if ($money == 0){
                                    $money_alone = '不限';
                                }else{
                                    $money_alone = $money;
                                }
                                $num_str = '不限';
                                $acls = 'bai';
                            }else{
                                if ($kind == 1){
                                    $money_alone = '随机';
                                }else{
                                    $money_alone = $amount;
                                }
                                $money_str = $money;
                                $num_str = $num;
                                $acls = 'bai';
                            }
                            $difftime = ADDTIME - $addtime;
                            //运气或定额过期超时退款
                            if ( $difftime > $_ZEAI['HB_refundtime']*86400 && ($kind == 1 || $kind == 2) && $flag==1 ){
                                $db->query("UPDATE ".__TBL_HONGBAO__." SET flag=2 WHERE id=".$id);
                                //统计已抢金额
                                $rt=$db->query("SELECT SUM(money) FROM ".__TBL_HONGBAO_USER__." WHERE fid=".$id);
                                $row = $db->fetch_array($rt,'num');
                                $nomoney = intval($row[0]);
                                $endtotalmoney = $money - $nomoney;
                                if ($endtotalmoney > 0){
                                    //money_list
                                    $endnum  = $endtotalmoney + $data_money;
                                    $db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$cook_uid);
                                    $content = '红包未抢完退款';
                                    $db->AddHistoryList($cook_uid,$content,$endtotalmoney,1);
                                    //余额站内消息
                                    $C = $data_nickname.'您好，您有一笔资金到账！　　<a href='.Href('money').' class=aQING>查看详情</a>';
                                    $db->SendTip($cook_uid,'红包退款成功',dataIO($C,'in'),'sys');
                                    //weixin_mb
                                    if (!empty($data_openid) && $data_subscribe==1){
                                        $first  = $data_nickname."您好，您的余额账户有变动(红包退款)：";
                                        $remark = $content."，查看详情";
                                        $url    = urlencode(mHref('money'));
                                        wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$data_openid.'&money='.$endtotalmoney.'&money_total='.$endnum.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
                                    }
                                }
                                //
                                $flag = 2;
                            }
                        ?>
                        <tr>
                        <input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="chk<?php echo $id; ?>" style="display:none">
                        <td width="90"><a href="<?php echo $href; ?>" class="hongbao60" target="_blank"><?php echo $img_str; ?><?php echo $new_str; ?></a></td>
                        <td width="100" class="center"><?php switch ($kind){case 1:echo "运气红包";break;case 2:echo "定额红包";break;case 3:echo "讨红包";break;}?></td>
                        <td width="90" class="center"><?php echo $money_str; ?></td>
                        <td width="90" class="center"><?php echo $money_alone; ?></td>
                        <td width="80" class="center"><?php echo $num_str; ?></td>
                        <td width="80" class="C8d center"><font class="C999"><?php echo $addtime_str;?></font></td>
                        <td class="center" style="color:#999;line-height:200%">
                        <?php
                        if ($flag == 0){
                            echo '<font class="S14 C999">审核中</font>';
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
                            echo '<font class="S14 Cf60">进行中</font>';
                            if (!empty($outtime))echo '<br>离结束还有 '.$outtime;
                        }elseif($flag == 2){
                            echo '<font class="S14 C090">结束或过期</font>';
                        }?>            
                        </td>
                        <td width="110" align="left" class="center" ><a href="<?php echo $href; ?>" class="<?php echo $acls; ?>" target="_blank">查看详情</a></td>
                        </tr>
                        <?php } ?>
                        </table>                    
                        <?php 
                        if ($total > $pagesize)echo '<div class="pagebox mypagebox">'.$pagelist.'</div>';
                    }else{
                        $tipsstr=($k==1 || empty($k))?'您暂时还没有发过红包<br><br><a class="btn HONG" onclick="hongbao_add(\'out\');">＋我要发红包</a>':'您暂时还没有讨过红包<br><br><a class="btn HONG" onclick="hongbao_add(\'in\');">＋我要讨红包</a>';
                        echo nodatatips($tipsstr);
                    }
				//我收到的
				}elseif($t==2){
					$k = intval($k);
					if (!empty($k))$sqll = " AND b.kind=$k";
					$currfields = ($k == 3)?",a.content":"";
					$rt = $db->query("SELECT a.id,a.money,a.addtime".$currfields.",b.kind,b.id AS fid FROM ".__TBL_HONGBAO_USER__." a,".__TBL_HONGBAO__." b WHERE a.fid=b.id AND a.uid=".$cook_uid.$sqll." ORDER BY a.id DESC");
					$total = $db->num_rows($rt);
					?>
                    <table class="W100_ navu"><tr>
                    <td height="60" align="center" >
                    <a href="<?php echo SELF; ?>?t=<?php echo $t; ?>"<?php echo ($k == '')?' class="hong"':' class="bai"'; ?>>全部</a>　
                    <a href="<?php echo SELF; ?>?t=<?php echo $t; ?>&k=1"<?php echo ($k == 1)?' class="hong"':' class="bai"'; ?>>运气红包</a>　
                    <a href="<?php echo SELF; ?>?t=<?php echo $t; ?>&k=2"<?php echo ($k == 2)?' class="hong"':' class="bai"'; ?>>定额红包</a>　
                    <a href="<?php echo SELF; ?>?t=<?php echo $t; ?>&k=3"<?php echo ($k == 3)?' class="hong"':' class="bai"'; ?>>打赏</a>
                    </td></tr>
                    </table>
					<?php
					if($total>0){
						$page_skin=2;$pagemode=4;$pagesize=8;$page_color='#E83191';require_once ZEAI.'sub/page.php';
						?>
                        <table class="tablelist">
                        <tr>
                        <td width="90" class="list_title">&nbsp;</td>
                        <td width="100" class="list_title center">红包类型</td>
                        <td width="130" class="list_title center">红包金额(元)</td>
                        <td width="80" class="list_title center">领取时间</td>
                        <td width="30" class="list_title center">&nbsp;</td>
                        <td class="list_title center"><?php if ($k == 3){echo '打赏人留言';}else{echo '&nbsp;';} ?></td>
                        <td width="120" class="list_title center">详情</td>
                        </tr>
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
							$href = Href('hongbao',$fid);
                            if ($kind == 3){
                                $acls = 'bai';	
                            }else{
                                $acls = 'bai';	
                            }
                        ?>
                        <tr>
                        <td width="90"><a href="<?php echo $href; ?>" class="hongbao60" target="_blank"><?php echo $img_str; ?></a></td>
                        <td width="100" class="center"><?php switch ($kind){case 1:echo "运气红包";break;case 2:echo "定额红包";break;case 3:echo "打赏";break;}?></td>
                        <td width="130" class="center"><?php echo $money; ?></td>
                        <td width="80" class=" center"><font class="C999"><?php echo $addtime_str;?></font></td>
                        <td width="30" class="C8d center">&nbsp;</td>
                        <td style="color:#999;line-height:200%"><?php if ($kind == 3){echo $content;}else{echo '&nbsp;';} ?></td>
                        <td width="120" class="center" ><a href="<?php echo $href; ?>" class="<?php echo $acls; ?>" target="_blank">红包详情</a></td>
                        </tr>
                        <?php } ?>
                        </table>
            		<?php
					}else{echo nodatatips('您暂时还没有抢过红包<br><br><a href="'.Href('hongbao').'" class="btn HONG">我要抢红包</a>');}
					?>
                <?php }?>
        	</div>
        </div>
        <!-- end C -->
</div></div></div>
<?php require_once ZEAI.'p1/bottom.php';ob_end_flush();?>
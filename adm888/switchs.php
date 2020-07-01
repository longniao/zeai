<?php
require_once '../sub/init.php';

header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/udata.php';
$urole = json_decode($_ZEAI['urole'],true);
$TG_set = json_decode($_REG['TG_set'],true);

//检查性别库
$ifsex = true;
$row = $db->ROW(__TBL_UDATA__,"subjsonstr","fieldname='sex'","num");
if ($row){$sex_ARR = json_decode($row[0],true);if(!is_array($sex_ARR) || count($sex_ARR)==0)$ifsex = false;}else{$ifsex = false;}
if (!$ifsex)alert_adm('性别出错请联系开发者QQ797311','close');

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/Sortable1.6.1.js"></script>
<body>
<div class="navbox">
	<?php if ($t == 1){?><a href="<?php echo $SELF;?>?t=1"<?php echo ($t == 1 || empty($t))?' class="ed"':'';?>>会员注册选项</a><?php }?>
	<?php if ($t == 2){?><a href="<?php echo $SELF;?>?t=2"<?php echo ($t == 2)?' class="ed"':'';?> ><?php echo $_ZEAI['loveB'];?>/收费机制</a><?php }?>
	<?php if ($t == 3){?><a href="<?php echo $SELF;?>?t=3"<?php echo ($t == 3)?' class="ed"':'';?>>审核/功能/开关</a><?php }?>
</div>
<div class="fixedblank"></div>
<form id="ZEAIFORM" name="ZEAIFORM" method="post">
<!------------------会员注册选项------------------>
<?php if ($t == 1){
if(!in_array('switchs_reg',$QXARR))exit(noauth());	
?>
<style>
.table .tdL{width:200px}
</style>
  <table class="table size2 W1200" style="margin:20px 0 0 20px">
	<tr><th colspan="2" align="left">注册开关</th></tr>
	<tr>
		<td class="tdL">新会员注册</td>
		<td class="tdR">
        <input type="radio" name="reg_flag" id="reg_flag1" class="radioskin" value="1"<?php echo ($_REG['reg_flag'] == 1)?' checked':'';?>><label for="reg_flag1" class="radioskin-label"><i class="i1"></i><b class="W80 ">直接通过</b></label>
        <input type="radio" name="reg_flag" id="reg_flag2" class="radioskin" value="2"<?php echo ($_REG['reg_flag'] == 2)?' checked':'';?>><label for="reg_flag2" class="radioskin-label"><i class="i1"></i><b class="W80 ">需要审核</b></label>
        <input type="radio" name="reg_flag" id="reg_flag3" class="radioskin" value="3"<?php echo ($_REG['reg_flag'] == 3)?' checked':'';?>><label for="reg_flag3" class="radioskin-label"><i class="i1"></i><b class="W100 ">关闭新会员注册</b></label>
		<br><span class="tips S12">如果选择【需要审核】，新会员注册后将不能登录进入，啥都干不了，只有总后台【会员管理】审核以后才可以</span>
        </td>
	</tr>
	<tr>
		<td class="tdL" style="line-height:normal">首次关注公众号后<br>强制注册资料后进入</td>
	  <td class="tdR">
        <table class="table0" style="margin:0">
        <tr>
        <td align="left"><input type="checkbox" name="gzflag2" id="gzflag2" class="switch" value="1"<?php echo ($_REG['gzflag2'] == 1)?' checked':'';?>><label for="gzflag2" class="switch-label"><i></i><b>开启</b><b>关闭</b></label></td>
        <td align="left">
        <span class="tips">开启后，首次关注公众号后，将不能登录系统（会员状态：关注未注册），必须注册个人资料绑定登录帐号密码或手机后激活登录状态</span><br>
        <span class="tips">关闭后，首次关注公众号后，资料不全也可以登录系统操作，快捷进入</span>
        </td>
        </tr>
        </table>      
     </td>
	</tr>
    <tr><th colspan="2" align="left">注册步骤/资料</th></tr>
	<tr>
		<td class="tdL">手机端注册风格</td>
		<td class="tdR lineH150">
        
        
        
			<input type="radio" name="reg_style" id="reg_style1" class="radioskin" value="1"<?php echo ($_REG['reg_style'] == 1)?' checked':'';?>><label for="reg_style1" class="radioskin-label"><i class="i1"></i><b class="W150 ">默认必填风格</b></label><br>
			<span class="tips2 S12">
            　　会员资料选项默认，注册完成后，资料完整度达80%<br>
            　　必填项：【性别、地区、生日、身高、学历、婚况、职业、月收入——微信号、照片、昵称、住房、购车、择偶要求、自我介绍】
            	<div>　　注册强制填写微信号：<input type="checkbox" name="reg_force_wx" id="reg_force_wx" class="switch" value="1"<?php echo ($_REG['reg_force_wx'] == 1)?' checked':'';?>><label for="reg_force_wx" class="switch-label"><i></i><b>开启</b><b>关闭</b></label></div>
            </span>
            
            <div style="margin-top:15px;border-top:#eee 1px solid;padding-top:10px">
                <input type="radio" name="reg_style" id="reg_style2" class="radioskin" value="2"<?php echo ($_REG['reg_style'] == 2)?' checked':'';?>><label for="reg_style2" class="radioskin-label"><i class="i1"></i><b class="W100 ">分步DIY自选</b></label>
                <style>
                .stepbox .stepli{margin-top:5px}
                .stepbox .stepli li{padding:5px 10px;float:left;margin:5px;border:#ddd 1px solid;cursor:move;background-color:#f8f8f8;border-radius:3px}
                </style>
                <dd class="stepbox" id="stepbox">
                    <div class="stepli">
                        <?php
                        $reg_dataARR    = explode(',',$_REG['reg_data']);
                        $reg_data_pxARR = explode(',',$_REG['reg_data_px']);
                        if (count($reg_data_pxARR) >= 1 && is_array($reg_data_pxARR)){
                            foreach ($reg_data_pxARR as $k=>$V) {
								$bW = ($V=='parent')?'W120':'W80';
                                ?>
                                <li class="<?php echo $bW;?>"><input type="checkbox" name="reg_data[]" id="reg_data_<?php echo $V;?>" class="checkskin reg_data" value="<?php echo $V;?>"<?php echo (in_array($V,$reg_dataARR))?' checked':'';?>><label for="reg_data_<?php echo $V;?>" class="checkskin-label"><i class="i1"></i><b class="<?php echo $bW;?>"><?php echo reg_data_title($V);?></b></label></li>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="clear"></div>
                </dd>
                <font class="S12 C999">可以按住不放拖动项目调整前后顺序，选中请打勾，选中将出现在注册页面，否则不显示</font>
            </div>
        
        
        </td>
	</tr>
    
    <tr><th colspan="2" align="left">注册/登录帐号类型</th></tr>
	<tr>
		<td class="tdL">注册/登录帐号形式</td>
		<td class="tdR">
			<input type="radio" name="reg_kind" id="reg1_kind1" class="radioskin" value="1"<?php echo ($_REG['reg_kind'] == 1)?' checked':'';?>><label for="reg1_kind1" class="radioskin-label"><i class="i1"></i><b class="W150 ">手机(验证码)＋密码</b></label>
			<input type="radio" name="reg_kind" id="reg1_kind2" class="radioskin" value="2"<?php echo ($_REG['reg_kind'] == 2)?' checked':'';?>><label for="reg1_kind2" class="radioskin-label"><i class="i1"></i><b class="W100 ">用户名＋密码</b></label>
			<input type="radio" name="reg_kind" id="reg1_kind3" class="radioskin" value="3"<?php echo ($_REG['reg_kind'] == 3)?' checked':'';?>><label for="reg1_kind3" class="radioskin-label"><i class="i1"></i><b class="W200 ">手机(验证码)＋用户名＋密码</b></label>

	  </td>
	</tr>
	<tr>
		<td class="tdL">第三方登录</td>
		<td class="tdR">
			<input type="checkbox" name="reg_3login_qq" id="reg_3login_1" class="checkskin " value="1"<?php echo ($_REG['reg_3login_qq'] == 1)?' checked':'';?>><label for="reg_3login_1" class="checkskin-label"><i class="i1"></i><b class="W100">QQ登录</b></label>
			<input type="checkbox" name="reg_3login_wx" id="reg_3login_2" class="checkskin " value="1"<?php echo ($_REG['reg_3login_wx'] == 1)?' checked':'';?>><label for="reg_3login_2" class="checkskin-label"><i class="i1"></i><b class="W100">微信登录</b></label>
		</td>
	</tr>
    
    
	<tr><th colspan="2" align="left">初始值</th></tr>
	<tr>
		<td class="tdL">新会员初始<?php echo $_ZEAI['loveB'];?></td>
		<td class="tdR"><input name="reg_loveb" type="text" class="W50" maxlength="7" value="<?php echo $_REG['reg_loveb'];?>"> <?php echo $_ZEAI['loveB'];?> <span class="tips S12">设为0不送</span></td>
	</tr>
	<tr>
		<td class="tdL">新会员默认会员组</td>
		<td class="tdR">
        	<?php if (is_array($urole) && count($urole)>0){?>
            <table class="table0" style="margin:0">
            <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            	?>
            <tr>
            <td align="left"><input type="radio" name="reg_grade" id="reg_grade<?php echo $grade;?>" class="radioskin" value="<?php echo $grade;?>"<?php echo ($_REG['reg_grade'] == $grade)?' checked':'';?>><label for="reg_grade<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle;font-size:14px"><?php echo $RV['t'];?></font></b></label>
</td>
            </tr>
            <?php }?>
            </table>
            <?php }else{
                echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";
            }
            ?>        
		</td>
	</tr>
    <input name="submitok" type="hidden" value="cache_reg">
    <input name="uu" type="hidden" value="<?php echo $session_uid;?>">
    <input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
    <input name="reg_data_px" id="reg_data_px" type="hidden" value="<?php echo $reg_data_px;?>">
</table>
<br><br><br><br><br>
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
<script>
function drag_init(){
	(function (){
		[].forEach.call(stepbox.getElementsByClassName('stepli'), function (el){
			Sortable.create(el, {
				group: 'zeai_reg',
				animation:150
			});
		});
	})();
}
drag_init();
save.onclick = function(){
	var DATAPX=[];
	zeai.listEach('.reg_data',function(obj){
		DATAPX.push(obj.value);
	});
	reg_data_px.value=DATAPX.join(",");
	zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
		zeai.msg(0);zeai.alert(rs.msg);
	});
}
</script>





<!------------------爱豆和收费机制------------------>
<?php }elseif($t == 2){
	
if(!in_array('switchs_loveb',$QXARR))exit(noauth());
?>
<style>
.table .tdL{width:150px}
.table .tdR{min-width:400px}
span.tips{font-size:12px}
.table0 td{font-size:14px;padding:5px 0}
.table0 td input[type="text"]{margin:0 5px}
</style>
<table class="table size2 W1200" style="margin:0 0 50px 20px">

<!--查看联系方式-->
	<?php
	$contact_loveb = json_decode($_VIP['contact_loveb'],true);
	$contact_daylooknum = json_decode($_VIP['contact_daylooknum'],true);
	?>
	<tr><th colspan="4" align="left" style="border:0">查看联系方式</th></tr>
	<tr>
		<td class="tdL">同级查看</td>
		<td colspan="3" class="tdR">
        <input type="checkbox" name="contact_level" id="contact_level" class="switch" value="1"<?php echo ($_VIP['contact_level'] == 1)?' checked':'';?>><label for="contact_level" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
		　<span class="tips">（说明：只能高级别权重看同级或低的，低的不能看高的）</span>
        </td>
	</tr>    
	<tr>
		<td class="tdL">每天查看总人数</td>
		<td class="tdR">
        	<?php if (is_array($urole) && count($urole)>0){?>
            <table class="table0" style="margin:0">
            <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
            <tr>
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td width="100" align="left"><input name="contact_daylooknum_<?php echo $grade;?>" type="text" class="W50" maxlength="5" value="<?php echo $contact_daylooknum[$grade];?>"> 人</td>
            </tr>
            <?php }?>
            </table>
            <?php }else{
                echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";
            }
            ?>
		</td>
		<td class="tdL">查看按次计费</td>
		<td class="tdR">
<?php if (is_array($urole) && count($urole)>0){?>
            <table class="table0" style="margin:0">
            <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
            <tr>
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td align="left"><input name="contact_loveb_<?php echo $grade;?>" type="text" class="W50" maxlength="5" value="<?php echo $contact_loveb[$grade];?>"> <?php echo $_ZEAI['loveB'];?>/次</td>
            </tr>
            <?php }?>
            </table>
            <?php }else{
                echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";
            }
            ?>
		
        </td>
	</tr>



<!--查看联系方式 結束-->
    
    
    <!--聊天解锁-->
	<?php
	$chat_loveb = json_decode($_VIP['chat_loveb'],true);
	$chat_daylooknum = json_decode($_VIP['chat_daylooknum'],true);
	?>
	<tr><th height="40" colspan="4" align="left" valign="bottom" style="border:0">聊天看信解锁</th></tr>
	<tr>
		<td class="tdL">同级查看</td>
		<td colspan="3" class="tdR">
        <input type="checkbox" name="chat_level" id="chat_level" class="switch" value="1"<?php echo ($_VIP['chat_level'] == 1)?' checked':'';?>><label for="chat_level" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
		　<span class="tips">（说明：开启后，只能高级别权重看同级或低的，低的不能看高的）</span>
        </td>
	</tr>
	<tr>
		<td class="tdL">每天看信解锁总人数</td>
		<td class="tdR">
        	<?php if (is_array($urole) && count($urole)>0){?>
            <table class="table0" style="margin:0">
            <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
            <tr>
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td width="100" align="left"><input name="chat_daylooknum_<?php echo $grade;?>" type="text" class="W50" maxlength="5" value="<?php echo $chat_daylooknum[$grade];?>"> 人</td>
            </tr>
            <?php }?>
            </table>
            <?php }else{
                echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";
            }
            ?>
		</td>
	  <td class="tdL">看信按次计费</td>
		<td class="tdR">
<?php if (is_array($urole) && count($urole)>0){?>
            <table class="table0" style="margin:0">
            <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
            <tr>
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td align="left"><input name="chat_loveb_<?php echo $grade;?>" type="text" class="W50" maxlength="5" value="<?php echo $chat_loveb[$grade];?>"> <?php echo $_ZEAI['loveB'];?>/次</td>
            </tr>
            <?php }?>
            </table>
            <?php }else{
                echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";
            }
            ?>
		
      </td>
	</tr>


	<!--聊天解锁 结束-->
    
    
    <!--礼物兑换-->
	<tr><th height="40" colspan="4" align="left" valign="bottom" style="border:0">礼物兑换</th></tr>
	<tr>
		<td class="tdL">兑换到账户币种</td>
		<td colspan="3" class="tdR">
        <input type="radio" name="gift_dhkind" id="gift_dhkind1" class="radioskin" value="money"<?php echo ($_VIP['gift_dhkind'] == 'money')?' checked':'';?>><label for="gift_dhkind1" class="radioskin-label"><i class="i1"></i><b class="W180">余额账户(人民币)</b>　<img src="images/d2.gif">折扣 <input name="gift_dhmoney_num" type="text" class="W50" maxlength="4" value="<?php echo $_VIP['gift_dhmoney_num'];?>">
        </label>
        <span class="tips">说明：大于0小于等于1，填1为原价兑换；例如兑换了100元，填0.95相当于95折实际到账95元</span>
        
        
        <div style="height:8px"></div>
        <input type="radio" name="gift_dhkind" id="gift_dhkind2" class="radioskin" value="loveb"<?php echo ($_VIP['gift_dhkind'] == 'loveb')?' checked':'';?>><label for="gift_dhkind2" class="radioskin-label"><i class="i1"></i><b class="W180"><?php echo $_ZEAI['loveB'];?>账户</b>　<img src="images/d2.gif">折扣 <input name="gift_dhloveb_num" type="text" class="W50" maxlength="4" value="<?php echo $_VIP['gift_dhloveb_num'];?>"></label>

		　<span class="tips">说明：大于0小于等于1，填1为原价兑换；例如礼物价格为1000<?php echo $_ZEAI['loveB'];?>，填0.95相当于95折实际到账950个<?php echo $_ZEAI['loveB'];?></span>
        </td>
	</tr>
	<!--充值爱豆折扣-->
	<?php
	$loveb_buy = json_decode($_VIP['loveb_buy'],true);
	?>
	<tr><th height="40" colspan="4" align="left" valign="bottom" style="border:0">其他设置</th></tr>
	<tr>
		<td class="tdL"><?php echo $_ZEAI['loveB'];?>余额换算比例</td>
		<td class="tdR">1：<input name="loveBrate" type="text" class="W50" maxlength="7" value="<?php echo $_ZEAI['loveBrate'];?>">  <span class="tips">推荐100，如1元=100<?php echo $_ZEAI['loveB'];?>，填大于0正整数</span></td>
		<td class="tdL">新会员注册赠送<?php echo $_ZEAI['loveB'];?></td>
		<td class="tdR"><input name="reg_loveb" type="text" class="W50" maxlength="7" value="<?php echo $_REG['reg_loveb'];?>"> <?php echo $_ZEAI['loveB'];?> <span class="tips">设为0不送</span></td>
	</tr>
	<tr>
		<td class="tdL">会员置顶首页<?php echo $_ZEAI['loveB'];?></td>
		<td colspan="3" class="tdR"><input name="push_index" type="text" class="W50" maxlength="7" value="<?php echo $_VIP['push_index'];?>"> <?php echo $_ZEAI['loveB'];?> <span class="tips">设为0免费置顶</span></td>
	</tr>
    <tr>
		<td class="tdL">在线充值<?php echo $_ZEAI['loveB'];?>折扣</td>
		<td class="tdR">
        <?php
            if (is_array($urole) && count($urole)>0){
            ?>
            <table class="table0" style="margin:0">
            <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
            <tr>
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td width="100" align="left"><input name="loveb_buy_<?php echo $grade;?>" type="text" class="W50" maxlength="5" value="<?php echo $loveb_buy[$grade];?>"></td>
            </tr>
            <?php }?>
            </table>
            <?php }else{
                echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";
            }
            ?>
        	<span class="tips">说明：1为原价不打折，如：0.95（相当于95折）</span>
        </td>
		<td class="tdL"><?php echo $_ZEAI['loveB'];?>在线充值最低</td>
		<td class="tdR"><input name="cz_minnum" type="text" class="W50" maxlength="7" value="<?php echo $_VIP['cz_minnum'];?>"> 元 <span class="tips">推荐10元，最低1元</span></td>
	</tr>
    <!--相册容量-->
	<?php $photo_num = json_decode($_VIP['photo_num'],true);?>
	<tr>
		<td class="tdL">相册容量</td>
		<td class="tdR">
        	<?php if (is_array($urole) && count($urole)>0){?>
            <table class="table0" style="margin:0">
            <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
            <tr>
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td width="100" align="left"><input name="photo_num_<?php echo $grade;?>" type="text" class="W50" maxlength="5" value="<?php echo $photo_num[$grade];?>"> 张</td>
            </tr>
            <?php }?>
            </table>
            <?php }else{
                echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";
            }
            ?>
        </td>
		<td class="tdL">视频容量</td>
		<td class="tdR">
		<?php
		$video_num = json_decode($_VIP['video_num'],true);
		if (is_array($urole) && count($urole)>0){ ?>
            <table class="table0" style="margin:0">
            <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
            <tr>
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td width="100" align="left"><input name="video_num_<?php echo $grade;?>" type="text" class="W50" maxlength="5" value="<?php echo $video_num[$grade];?>"> 个</td>
            </tr>
            <?php }?>
            </table>
            <?php }else{
                echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";
            }
            ?>
        </td>
	</tr>
    <!--签到数目-->
	<tr>
		<td class="tdL">签到得<?php echo $_ZEAI['loveB'];?>随机数组</td>
		<td colspan="3" class="tdR"><input style="font-family:Verdana, Geneva, sans-serif" name="sign_numlist" type="text" class="W300" maxlength="100" value="<?php echo @implode(",",json_decode($_VIP['sign_numlist'],true));?>"> <span class="tips">说明：数字（正整数）请以英文半角逗号隔开，如：<font class="blue">2,9,18,48,188</font>，会员签到后将随机得到其中一个数的<?php echo $_ZEAI['loveB'];?></span></td>
	</tr>
    <!--做任务奖励-->
    <?php $task_loveb = json_decode($_VIP['task_loveb'],true);?>
	<tr style="display:none">
		<td class="tdL">做任务奖励<?php echo $_ZEAI['loveB'];?></td>
		<td colspan="3" class="tdR">
      		<table class="table0" style="margin:0">
      		  <tr>
      		    <td width="120">完善资料80%以上</td>
      		    <td width="100" align="left"><input name="task_loveb_myinfo" type="text" class="W50" maxlength="5" value="<?php echo $task_loveb['myinfo'];?>"> <?php echo $_ZEAI['loveB'];?></td>
   		      </tr>
      		  <tr>
      		    <td width="120">上传形象照</td>
      		    <td width="100" align="left"><input name="task_loveb_photom" type="text" class="W50" maxlength="5" value="<?php echo $task_loveb['photom'];?>"> <?php echo $_ZEAI['loveB'];?></td>
   		      </tr>
      		  <tr>
      		    <td width="120">每完成1项实名认证</td>
      		    <td width="100" align="left"><input name="task_loveb_rz" type="text" class="W50" maxlength="5" value="<?php echo $task_loveb['rz'];?>"> <?php echo $_ZEAI['loveB'];?></td>
   		      </tr>
      		  <tr>
      		    <td width="120">每上传1张相册</td>
      		    <td width="100" align="left"><input name="task_loveb_photo" type="text" class="W50" maxlength="5" value="<?php echo $task_loveb['photo'];?>"> <?php echo $_ZEAI['loveB'];?></td>
   		      </tr>
      		  <tr>
      		    <td width="120">每上传1个视频</td>
      		    <td width="100" align="left"><input name="task_loveb_video" type="text" class="W50" maxlength="5" value="<?php echo $task_loveb['video'];?>"> <?php echo $_ZEAI['loveB'];?></td>
   		      </tr>
	      </table>
      	</td>
	</tr>
    
    
    <!--保存结束-->
</table>
		<input name="submitok" type="hidden" value="cache_vip">
		<input name="uu" id="uu" type="hidden" value="<?php echo $session_uid;?>">
		<input name="pp" id="pp" type="hidden" value="<?php echo $session_pwd;?>">

<br><br><br><br><br>
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
<script>
	save.onclick = function(){
		zeai.msg('正在更新中',{time:30});
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){
			//location.reload(true);
			var rs=zeai.jsoneval(e);zeai.msg(0);zeai.alert(rs.msg,'switchs.php?t=2');
		});
	}
</script>

<!----------------审核机制设置------------------>
<?php }elseif($t == 3){

if(!in_array('switchs_shkg',$QXARR))exit(noauth());	
	
?>
<?php $switch = json_decode($_ZEAI['switch'],true);?>
<style>
.table .tdL{width:200px}
.tips{font-size:12px}
td.tdLbgHUI{background-color:#eee}
</style>

<table class="table size2 W1200" style="margin:20px 0 100px 20px">
	<tr><th align="left" colspan="2">全局功能设置</th></tr>
    
	<tr><td class="tdL ">游客浏览个人主页</td><td class="tdR">
        <input type="checkbox" name="YKviewU" id="YKviewU" class="switch" value="1"<?php echo ($_VIP['YKviewU'] == 1)?' checked':'';?>><label for="YKviewU" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
        <span class="tips S12">开启后，游客可以浏览个人主页，关闭后跳转到登录注册页</span> 
    </td></tr>
	<tr><td class="tdL ">会员浏览个人主页</td><td class="tdR">
		<style>
        .stepbox2 .stepli2{margin-top:5px}
        .stepbox2 .stepli2 li{padding:5px 10px;float:left;margin:5px;border:#ddd 1px solid;cursor:move;background-color:#f8f8f8;border-radius:3px}
        </style>
        <dd class="stepbox2" id="stepbox2">
            <div class="stepli2">
                <?php
                $viewhomepage_dataARR    = explode(',',$_VIP['viewhomepage_data']);
                $viewhomepage_data_pxARR = explode(',',$_VIP['viewhomepage_data_px']);
                if (count($viewhomepage_data_pxARR) >= 1 && is_array($viewhomepage_data_pxARR)){
                    foreach ($viewhomepage_data_pxARR as $k=>$V) {
                        ?>
                        <li><input type="checkbox" name="viewhomepage_data[]" id="viewhomepage_data_<?php echo $V;?>" class="checkskin viewhomepage" value="<?php echo $V;?>"<?php echo (in_array($V,$viewhomepage_dataARR))?' checked':'';?>><label for="viewhomepage_data_<?php echo $V;?>" class="checkskin-label"><i class="i1"></i>
                        <b class="<?php ($V == 'bfb')?'W200':'W100';?>"><?php echo chatContact_data_title($V);?>
                        <?php if ($V == 'bfb'){?> <input name="viewhomepage_bfb_num" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo intval($_VIP['viewhomepage_bfb_num']);?>"> %<?php }?>
                        </b></label></li>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
        </dd>
        <font class="S12 C999">可以按住不放拖动项目调整前后顺序，系统检查规则(从左到右)，选中请打勾，选中将进行验证和检查，不符合将不能解锁（自动弹出引导页面完善），如全不选将忽略检查</font>
        <div class="S12 Cf00">注：此功能必须在上面【游客浏览个人主页】功能关闭后生效</div>
        <input name="viewhomepage_data_px" id="viewhomepage_data_px" type="hidden" value="<?php echo $_VIP['viewhomepage_data_px'];?>">
    </td></tr>

	<tr><td class="tdL tdLbgHUI">会员自己隐藏头像</td><td class="tdR">
        <input type="checkbox" name="hidephoto" id="hidephoto" class="switch" value="1"<?php echo ($_VIP['hidephoto'] == 1)?' checked':'';?>><label for="hidephoto" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
        <span class="tips S12">开启后，手机端【我的】<img src="images/d2.gif" class="picmiddle">【设置】出现隐藏开关选项，关闭后不显示</span> 
    </td></tr>

	<tr><td class="tdL tdLbgHUI">会员自己隐藏资料</td><td class="tdR">
        <input type="checkbox" name="hidedata" id="hidedata" class="switch" value="1"<?php echo ($_VIP['hidedata'] == 1)?' checked':'';?>><label for="hidedata" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
        <span class="tips S12">开启后，手机端【我的】<img src="images/d2.gif" class="picmiddle">【设置】出现隐藏开关选项，关闭后不显示</span> 
    </td></tr>

	<tr><td class="tdL tdLbgHUI">会员自己注销资料</td><td class="tdR">
        <input type="checkbox" name="hidedel" id="hidedel" class="switch" value="1"<?php echo ($_VIP['hidedel'] == 1)?' checked':'';?>><label for="hidedel" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
        <span class="tips S12">开启后，手机端【我的】<img src="images/d2.gif" class="picmiddle">【设置】出现注销按钮，关闭后不显示</span> 　　　
        注销收费 <input name="hidedel_rmb" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $_VIP['hidedel_rmb'];?>"> 元<span class="tips">推荐10元，填0免费注销</span><br>
        <span class="tips">注：注销只是软删除进入回收站，也就是锁定账户（不能登录也不显示），后台【会员管理】可以恢复至正常状态</span>
    </td></tr>
    
	<tr><td class="tdL tdLbgHUI">会员隐私设置选项</td><td class="tdR">
        <input type="checkbox" name="hideprivacy" id="hideprivacy" class="switch" value="1"<?php echo ($_VIP['hideprivacy'] == 1)?' checked':'';?>><label for="hideprivacy" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
        <span class="tips S12">开启后，手机端和电脑端【微信/微信二维码/QQ/邮箱】<img src="images/d2.gif" class="picmiddle">【隐私设置】出现对应隐私开关，关闭后不显示</span> 
    </td></tr>    
	<!--解锁聊天/联系方式-->
	<tr><td class="tdL">解锁聊天/联系方式</td><td class="tdR">
		<style>
        .stepbox .stepli{margin-top:5px}
        .stepbox .stepli li{padding:5px 10px;float:left;margin:5px;border:#ddd 1px solid;cursor:move;background-color:#f8f8f8;border-radius:3px}
        .FVerdana{background-color:#fff}
        </style>
        <dd class="stepbox" id="stepbox">
            <div class="stepli">
                <?php
                $chatContact_dataARR    = explode(',',$_VIP['chatContact_data']);
                $chatContact_data_pxARR = explode(',',$_VIP['chatContact_data_px']);
                if (count($chatContact_data_pxARR) >= 1 && is_array($chatContact_data_pxARR)){
                    foreach ($chatContact_data_pxARR as $k=>$V) {
                        ?>
                        <li><input type="checkbox" name="chatContact_data[]" id="chatContact_data_<?php echo $V;?>" class="checkskin chatContact" value="<?php echo $V;?>"<?php echo (in_array($V,$chatContact_dataARR))?' checked':'';?>><label for="chatContact_data_<?php echo $V;?>" class="checkskin-label"><i class="i1"></i>
                        <b class="<?php ($V == 'bfb')?'W200':'W100';?>"><?php echo chatContact_data_title($V);?>
                        <?php if ($V == 'bfb'){?> <input name="chatContact_bfb_num" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo intval($_VIP['chatContact_bfb_num']);?>"> %<?php }?>
                        </b></label></li>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
        </dd>
        <font class="S12 C999">可以按住不放拖动项目调整前后顺序，系统检查规则(从左到右)，选中请打勾，选中将进行验证和检查，不符合将不能解锁（自动弹出引导页面完善），如全不选将忽略检查</font>
        <input name="chatContact_data_px" id="chatContact_data_px" type="hidden" value="<?php echo $_VIP['chatContact_data_px'];?>">
    </td></tr>
    
	<tr><td class="tdL tdLbgHUI">谁看过我微信通知</td><td class="tdR">
        <input type="radio" name="ifViewPushsex" id="ifViewPushsex1" class="radioskin" value="1"<?php echo ($_VIP['ifViewPushsex'] == 1)?' checked':'';?>>
        <label for="ifViewPushsex1" class="radioskin-label"><i class="i1"></i><b class="W100 S14">男生</b></label>
        
        <input type="radio" name="ifViewPushsex" id="ifViewPushsex2" class="radioskin" value="2"<?php echo ($_VIP['ifViewPushsex'] == 2)?' checked':'';?>>
        <label for="ifViewPushsex2" class="radioskin-label"><i class="i1"></i><b class="W100 S14">女生</b></label>

        <input type="radio" name="ifViewPushsex" id="ifViewPushsex0" class="radioskin" value="0"<?php echo ($_VIP['ifViewPushsex'] == 0)?' checked':'';?>>
        <label for="ifViewPushsex0" class="radioskin-label"><i class="i1"></i><b class="W100 S14">男生＋女生</b></label>
        
        <span class="tips S12">用户需关注公众号后才能正确接收，同性浏览不发送</span> 
    </td></tr>
    
    
    <!--打招呼-->
	<tr><td class="tdL">向对方打招呼</td><td align="left" class="tdR">
		<style>
        .stepbox .stepli3{margin-top:5px}
        .stepbox .stepli3 li{height:32px;padding:5px 10px;float:left;margin:5px;border:#ddd 1px solid;cursor:move;background-color:#f8f8f8;border-radius:3px}
        .FVerdana{background-color:#fff}
        </style>
        <dd class="stepbox" id="stepbox3">
            <div class="stepli3">
                <?php
                $hi_dataARR    = explode(',',$_VIP['hi_data']);
                $hi_data_pxARR = explode(',',$_VIP['hi_data_px']);
                if (count($hi_data_pxARR) >= 1 && is_array($hi_data_pxARR)){
                    foreach ($hi_data_pxARR as $k=>$V) {?>
                        <li><input type="checkbox" name="hi_data[]" id="hi_data_<?php echo $V;?>" class="checkskin hi" value="<?php echo $V;?>"<?php echo (in_array($V,$hi_dataARR))?' checked':'';?>><label for="hi_data_<?php echo $V;?>" class="checkskin-label"><i class="i1"></i>
                        <b class="<?php ($V == 'bfb')?'W200':'W100';?>"><?php echo chatContact_data_title($V);?>
                        <?php if ($V == 'bfb'){?> <input name="hi_bfb_num" type="text" class="W50 FVerdana" maxlength="3" value="<?php echo intval($_VIP['hi_bfb_num']);?>"> %<?php }?>
                        </b></label></li>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
        </dd>
        <font class="S12 C999">可以按住不放拖动项目调整前后顺序，系统检查规则(从左到右)，选中请打勾，选中将进行验证和检查，不符合将不能打招呼（自动弹出引导页面完善），如全不选将忽略检查</font>
        <input name="hi_data_px" id="hi_data_px" type="hidden" value="<?php echo $_VIP['hi_data_px'];?>">
    </td></tr>
	<script>
    function  drag_init(){(function (){[].forEach.call(stepbox.getElementsByClassName('stepli'),    function (el){Sortable.create(el,{group: 'zeai_reg',animation:150});});})();}drag_init();
	function drag_init2(){(function (){[].forEach.call(stepbox2.getElementsByClassName('stepli2'), function (el){Sortable.create(el,{group: 'zeai_reg2',animation:150});});})();}drag_init2();
	function drag_init3(){(function (){[].forEach.call(stepbox3.getElementsByClassName('stepli3'), function (el){Sortable.create(el,{group: 'zeai_reg3',animation:150});});})();}drag_init3();
    </script>
    

	<!--会员服务模式-->
	<tr>
		<td class="tdL">会员服务模式</td>
	  	<td class="tdR">
		
		<?php if (is_array($urole) && count($urole)>0){?>
        <table class="table0" style="margin:0">
			<?php
            foreach($urole as $RV){
                $grade = $RV['g'];
				?>
                <tr class="bottomborder">
                    <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
                    <td align="left">　
                        <input type="radio" name="switch_Smode_<?php echo $grade;?>" id="switch_Smode_<?php echo $grade;?>1" class="radioskin" value="1"<?php echo ($switch['Smode']['g_'.$grade] == 1)?' checked':'';?>>
                        <label for="switch_Smode_<?php echo $grade;?>1" class="radioskin-label"><i class="i1"></i><b class="W100 S14">线上自助服务</b></label>
                        
                        <input type="radio" name="switch_Smode_<?php echo $grade;?>" id="switch_Smode_<?php echo $grade;?>2" class="radioskin" value="2"<?php echo ($switch['Smode']['g_'.$grade] == 2)?' checked':'';?>>
                        <label for="switch_Smode_<?php echo $grade;?>2" class="radioskin-label"><i class="i1"></i><b class="W100 S14">线下人工服务</b></label>
					</td>
          		</tr>
			<?php }?>
		</table>
      <?php }else{echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";}?>
      
      <span class="tips S12">
      	会员服务模式说明：<br>
		　1．选择【线上自助服务】，【线上自助服务】会员组个人主页将显示联系方式和私信聊天入口<br>
		　2．选择【线下人工服务】，【线下人工服务】会员组个人主页将隐藏联系方式和私信聊天入口，联系唯一途径就是寻求官方红娘来牵线<br>
      </span>
      
      </td>
	</tr>
    
    <!--通知类型-->
	<tr style="display:none">
		<td class="tdL">消息通知类型</td>
	  	<td class="tdR" style="padding-top:15px">
            <input type="checkbox" name="notice_kind[]" id="notice_kind_1" class="checkskin" value="1" checked><label for="zeai.cn" class="checkskin-label"><i class="i1"></i><b class="W80 S14">站内通知</b></label>     
            <input type="checkbox" name="notice_kind[]" id="notice_kind_2" class="checkskin" value="2"<?php echo (@in_array(2,$switch['notice_kind']))?' checked':'';?>><label for="notice_kind_2" class="checkskin-label"><i class="i1"></i><b class="W250 S14">微信客服/模版消息(优先客服消息)</b></label>  
               
            <!--<input type="checkbox" name="notice_kind[]" id="notice_kind_3" class="checkskin" value="3"<?php echo (@in_array(3,$switch['notice_kind']))?' checked':'';?>><label for="notice_kind_3" class="checkskin-label"><i class="i1"></i><b class="W80 S14">手机短信</b></label>-->
            
<div style="margin-top:10px">
<span class="tips">说明：需要正确配置微信和手机短信参数通知才能有效；<a onClick="parent.pageABC(6,2,'var.php?t=2')" class="blue">微信消息参数设置</a>　<a onClick="parent.pageABC(6,4,'var.php?t=4')" class="blue">手机短信参数设置</a></span><br>
<span class="tips">应用方面：聊天提醒，打招呼，好友动态（发布动态，参加活动，上传照片，修改资料，认证审核等），新会员提醒，网站通知，网站推送等</span>
</div>
        </td>
	</tr>
    
    <!--模糊功能开始-->
	<tr>
		<td class="tdL">首页头像上锁/模糊</td>
	  	<td class="tdR" style="padding:10px">
            <input type="checkbox" name="grade1LockBlur" id="grade1LockBlur" class="switch" value="1"<?php echo ($switch['grade1LockBlur'] == 1)?' checked':'';?>><label for="grade1LockBlur" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
            <span class="tips">（游客和非VIP会员）浏览首页会员大图展示和搜索列表显示加锁图标和头像模糊功能，<font class="Cf00">注：开启后，会员个人中心头像隐藏功能将失效</font></span>
            <br>
            上锁后文字说明 <input name="grade1LockBlurT" type="text" class="W200 FVerdana" maxlength="100" value="<?php echo dataIO($switch['grade1LockBlurT'],'out');?>">
            <span class="tips">如：VIP会员可见，最好控制在10个字以内</span>
        </td>
	</tr>
    <!--模糊功能结束-->



    <!--手机端开启更多大图浏览效果-->


    <!--手机首页滚动-->
	<tr>
		<td class="tdL">手机端首页滚动播报</td>
	  	<td class="tdR">
<input type="checkbox" name="iMarquee" id="iMarquee" class="switch" value="1"<?php echo ($_INDEX['iMarquee'] == 1)?' checked':'';?>><label for="iMarquee" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
      <span class="tips">开启后，在手机端首页左上角会出现浮层滚动播报（会员动态+新会员注册+VIP升级充值混合随机展现）</span> 
        </td>
	</tr>
    <!--手机端开启更多大图浏览效果-->
	<tr>
		<td class="tdL">手机端满屏大图切换</td>
	  	<td class="tdR">
            <input type="checkbox" name="iModuleU_bigmore" id="iModuleU_bigmore" class="switch" value="1"<?php echo ($_INDEX['iModuleU_bigmore'] == 1)?' checked':'';?>><label for="iModuleU_bigmore" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>      <span class="tips">开启后，在手机端首页点击【更多优质会员】，将出现满屏大图左右滑动切换展示效果（左右切换会员，上滑加关注，下滑取消关注），关闭后传统列表展示</span>   
        </td>
	</tr>
    
    
    <!--强制完善资料-->
	<tr>
		<td class="tdL">强制跳转完善资料</td>
	  	<td class="tdR">
            <input type="checkbox" name="force_data" id="force_data" class="switch" value="1"<?php echo ($switch['force']['data'] == 1)?' checked':'';?>><label for="force_data" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>    <span class="tips">开启后，在手机端点击【我的】进行跳转</span>
        </td>
	</tr>
    <!--强制上传形象照-->
	<tr>
		<td class="tdL">强制跳转上传头像</td>
	  	<td class="tdR">
            <input type="checkbox" name="force_photom" id="force_photom" class="switch" value="1"<?php echo ($switch['force']['photom'] == 1)?' checked':'';?>><label for="force_photom" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>    <span class="tips">开启后，在手机端点击【我的】进行跳转</span>
        </td>
	</tr>
    <!--强制手机验证-->
	<tr>
		<td class="tdL">强制跳转手机验证</td>
	  	<td class="tdR">
            <input type="checkbox" name="force_mob" id="force_mob" class="switch" value="1"<?php echo ($switch['force']['mob'] == 1)?' checked':'';?>><label for="force_mob" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>    <span class="tips">开启后，在手机端点击【我的】进行跳转</span>
        </td>
	</tr>
    <!--强制实名认证-->
	<tr>
		<td class="tdL">强制跳转实名认证</td>
	  	<td class="tdR">
            <input type="checkbox" name="force_cert" id="force_cert" class="switch" value="1"<?php echo ($switch['force']['cert'] == 1)?' checked':'';?>><label for="force_cert" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>    <span class="tips">开启后，在手机端点击【我的】进行跳转</span>
        </td>
	</tr>
	<tr>
		<td class="tdL">余额账户提现（总开关）</td>
	  	<td class="tdR">
        <input type="checkbox" name="ifrmbtx" id="ifrmbtx" class="switch" value="1"<?php echo ($switch['ifrmbtx'] == 1)?' checked':'';?>><label for="ifrmbtx" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
        <span class="tips">此开关将控制【单身】、【推广员】、【商家】提现功能显示与隐藏</span>
        </td>
	</tr>
	<tr>
		<td class="tdL"></td>
	  	<td class="tdR">单身用户最低提现金额 <input name="ifrmbtx_minnum" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $switch['ifrmbtx_minnum'];?>"> 元<span class="tips">推荐50</span>　　
		单身用户提现折扣 <input name="ifrmbtx_num"  type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $switch['ifrmbtx_num'];?>">
        <span class="tips">填1或小于1的小数，例如0.8，如果有会员提现1000元，实际只给他们800元</span>
        </td>
	</tr>
  
    
    <!--防骗/托设置-->
    <tr><th align="left" colspan="2">防骗/防托设置　<span class="tips2 S12 C999">此功能主要是防止婚托，酒托或专业网络美女诈骗，如：资料登记的很漂亮，填个联系方式，传个假的美女照片，然后人就消失，坐等收鱼</span></th></tr>
	<tr>
		<td class="tdL">个人主页联系方式/聊天/打招呼入口显示需满足右侧认证条件</td>
	  	<td class="tdR">
			<input type="checkbox" name="chatHiContact_ifshow[]" id="chatHiContact_ifshow1" class="checkskin" value="mob"<?php echo (@in_array('mob',$switch['chatHiContact_ifshow']))?' checked':'';?>><label for="chatHiContact_ifshow1" class="checkskin-label"><i class="i1"></i><b class="W150">完成手机认证</b></label>
			<input type="checkbox" name="chatHiContact_ifshow[]" id="chatHiContact_ifshow2" class="checkskin" value="identity"<?php echo (@in_array('identity',$switch['chatHiContact_ifshow']))?' checked':'';?>><label for="chatHiContact_ifshow2" class="checkskin-label"><i class="i1"></i><b class="W150">完成实名认证</b></label>
			<input type="checkbox" name="chatHiContact_ifshow[]" id="chatHiContact_ifshow3" class="checkskin" value="photo"<?php echo (@in_array('photo',$switch['chatHiContact_ifshow']))?' checked':'';?>><label for="chatHiContact_ifshow3" class="checkskin-label"><i class="i1"></i><b class="W150">完成真人认证</b></label>
    		<div><span class="tips2">打勾选中后将进行触发判断，此为累加条件不影响其它设置，比如线下会员即使此处打勾也照样不显示</span></div>
            <div class="lineH150 tips2">
                <span class="S14 C000">显示风险提示</span> <input type="checkbox" name="chatHiContact_iftips" id="chatHiContact_iftips" class="switch" value="1"<?php echo ($switch['chatHiContact_iftips'] == 1)?' checked':'';?>><label for="chatHiContact_iftips" class="switch-label"><i></i><b>开启</b><b>关闭</b></label><br>
                <span style="margin-top:5px;display:inline-block">开启后个人主页将显示提示，如：此用户没有完成**认证，请谨慎联系，【提醒对方认证】等字样<br>点击【提醒对方认证】将自动发送通知消息给对方引导认证，如果上面条件全部不选、已认证、关闭 将不显示</span>
            </div>
      </td>
	</tr>
    
    
	<tr style="display:none">
		<td class="tdL">发红包功能</td>
	  	<td class="tdR"><input type="checkbox" name="ifhb" id="ifhb" class="switch" value="1"<?php echo ($switch['ifhb'] == 1)?' checked':'';?>><label for="ifhb" class="switch-label"><i></i><b>开启红包模块</b><b>关闭</b></label></td>
	</tr>
    
    <!--文章设置-->
    <?php $WZ = json_decode($_ZEAI['WZ'],true);?>
    <tr><th align="left" colspan="2">文章详情页设置</th></tr>
	<tr>
		<td class="tdL">顶部会员推荐</td>
	  	<td class="tdR">
            <input type="checkbox" name="wz_iftjU" id="wz_iftjU" class="switch" value="1"<?php echo ($WZ['iftjU'] == 1)?' checked':'';?>><label for="wz_iftjU" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
            <span class="tips">开启后，顶部会显示推荐会员列表，关闭隐藏；推荐规则：顶部菜单【运营管理】<img src="images/d2.gif">左侧【优质会员推荐】<img src="images/d2.gif"><?php echo '【推荐至'.$TG_set['navtitle'].'】';?><a class="btn size1" href="u_select.php?iftj=TG_xqk">进入推荐</a></span>
		</td>
    </tr>    
    
	<tr>
		<td class="tdL">打赏设置</td>
	  	<td class="tdR">
            <input type="checkbox" name="wz_ifpay" id="wz_ifpay" class="switch" value="1"<?php echo ($WZ['ifpay'] == 1)?' checked':'';?>><label for="wz_ifpay" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
            <span class="tips">开启后，在手机文章详情页会出现打赏按钮</span><br>
            打赏金额列表 <input name="wz_ifpay_num"  type="text" class="W200 FVerdana" maxlength="100" value="<?php echo $WZ['ifpay_num'];?>"> 元
            <span class="tips">说明：数字请以英文半角逗号隔开，如：<font class="blue">1,2,5,10</font>，用户会选对应的金额进行支付，这个费用直接入账官方账户</span>
		</td>
    </tr>    
	<tr>
		<td class="tdL">评论总开关</td>
	  	<td class="tdR">
            <input type="checkbox" name="wz_ifbbs" id="wz_ifbbs" class="switch" value="1"<?php echo ($WZ['ifbbs'] == 1)?' checked':'';?>><label for="wz_ifbbs" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
            <span class="tips">开启后，文章页将出现评论功能，关闭隐藏</span>
		</td>
    </tr>    
	<tr>
		<td class="tdL">评论审核开关</td>
	  	<td class="tdR">
            <input type="checkbox" name="wz_bbs_ifsh" id="wz_bbs_ifsh" class="switch" value="1"<?php echo ($WZ['bbs_ifsh'] == 1)?' checked':'';?>><label for="wz_bbs_ifsh" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
            <span class="tips">开启后，评论将需要审核才会显示，关闭直接显示</span>
		</td>
    </tr>
	<tr>
		<td class="tdL">底部推荐阅读</td>
	  	<td class="tdR">
            <input type="checkbox" name="wz_iftjWZ" id="wz_iftjWZ" class="switch" value="1"<?php echo ($WZ['iftjWZ'] == 1)?' checked':'';?>><label for="wz_iftjWZ" class="switch-label"><i></i><b>开启</b><b>关闭</b></label> 
            <span class="tips">开启后，底部会显示置顶推荐文章列表，关闭隐藏</span>
		</td>
    </tr>    
    
    <!--活动设置-->
    <?php $party_joingrade = (empty($_VIP['party_joingrade']))?array():json_decode($_VIP['party_joingrade'],true);?>
    <tr><th align="left" colspan="2">相亲活动设置</th></tr>
	<tr>
		<td class="tdL">报名参加活动用户等级</td>
	  	<td class="tdR">
		<?php if (is_array($urole) && count($urole)>0){
            foreach($urole as $RV){$grade = $RV['g'];?>
            	<input type="checkbox" name="party_joingrade[]" id="party_joingrade_<?php echo $grade;?>" class="checkskin" value="<?php echo $grade;?>"<?php echo (in_array($grade,$party_joingrade))?' checked':'';?>><label for="party_joingrade_<?php echo $grade;?>" class="checkskin-label"><i class="i1"></i><b class="W150"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></b></label>
			<?php }
      }else{echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";}?>
      <div><span class="tips2">只有打勾的才能报名参加活动，等级不够会自动跳转VIP升级页面，游客会自动跳转登录页</span></div>
      </td>
	</tr>
    <!---->

    <tr><th align="left" colspan="2">审核设置</th></tr>
	<!--修改基本资料-->
	<tr>
		<td class="tdL">修改基本资料</td>
	  	<td class="tdR"><?php if (is_array($urole) && count($urole)>0){?>
        <table class="table0" style="margin:0">
          <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
          <tr class="bottomborder">
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td align="left">　
            	<input type="radio" name="switch_sh_moddata_<?php echo $grade;?>" id="switch_sh_moddata<?php echo $grade;?>1" class="radioskin" value="0"<?php echo ($switch['sh']['moddata_'.$grade] == 0)?' checked':'';?>>
                <label for="switch_sh_moddata<?php echo $grade;?>1" class="radioskin-label"><i class="i1"></i><b class="W80 S14">需要审核</b></label>
                
                <input type="radio" name="switch_sh_moddata_<?php echo $grade;?>" id="switch_sh_moddata<?php echo $grade;?>2" class="radioskin" value="1"<?php echo ($switch['sh']['moddata_'.$grade] == 1)?' checked':'';?>>
                <label for="switch_sh_moddata<?php echo $grade;?>2" class="radioskin-label"><i class="i1"></i><b class="W80 S14">直接通过</b></label></td>
          </tr>
          <?php }?>
        </table>
      <?php }else{echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";}?></td>
	</tr>
	<!--上传形象照-->
	<tr>
		<td class="tdL">上传形象照片</td>
	  	<td class="tdR"><?php if (is_array($urole) && count($urole)>0){?>
        <table class="table0" style="margin:0">
          <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
          <tr class="bottomborder">
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td align="left">　<input type="radio" name="switch_sh_photom_<?php echo $grade;?>" id="switch_photom1<?php echo $grade;?>" class="radioskin" value="0"<?php echo ($switch['sh']['photom_'.$grade] == 0)?' checked':'';?>>
              <label for="switch_photom1<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80 S14">需要审核</b></label>
              <input type="radio" name="switch_sh_photom_<?php echo $grade;?>" id="switch_photom2<?php echo $grade;?>" class="radioskin" value="1"<?php echo ($switch['sh']['photom_'.$grade] == 1)?' checked':'';?>>
              <label for="switch_photom2<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80 S14">直接通过</b></label></td>
          </tr>
          <?php }?>
        </table>
      <?php }else{echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";}?></td>
	</tr>
	<!--个人相册-->
	<tr>
		<td class="tdL">上传个人相册</td>
	  	<td class="tdR"><?php if (is_array($urole) && count($urole)>0){?>
        <table class="table0" style="margin:0">
          <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
          <tr class="bottomborder">
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td align="left">　<input type="radio" name="switch_sh_photo_<?php echo $grade;?>" id="switch_photo1<?php echo $grade;?>" class="radioskin" value="0"<?php echo ($switch['sh']['photo_'.$grade] == 0)?' checked':'';?>>
              <label for="switch_photo1<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80 S14">需要审核</b></label>
              <input type="radio" name="switch_sh_photo_<?php echo $grade;?>" id="switch_photo2<?php echo $grade;?>" class="radioskin" value="1"<?php echo ($switch['sh']['photo_'.$grade] == 1)?' checked':'';?>>
              <label for="switch_photo2<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80 S14">直接通过</b></label></td>
          </tr>
          <?php }?>
        </table>
      <?php }else{echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";}?></td>
	</tr>
    <!--视频-->
	<tr>
		<td class="tdL">上传个人视频</td>
	  	<td class="tdR"><?php if (is_array($urole) && count($urole)>0){?>
        <table class="table0" style="margin:0">
          <?php
            foreach($urole as $RV){
                $grade = $RV['g'];
            ?>
          <tr class="bottomborder">
            <td align="left"><?php echo uicon_grade_all($grade); ?> <font style="vertical-align:middle"><?php echo $RV['t'];?></font></td>
            <td align="left">　<input type="radio" name="switch_sh_video_<?php echo $grade;?>" id="switch_video1<?php echo $grade;?>" class="radioskin" value="0"<?php echo ($switch['sh']['video_'.$grade] == 0)?' checked':'';?>>
              <label for="switch_video1<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80 S14">需要审核</b></label>
              <input type="radio" name="switch_sh_video_<?php echo $grade;?>" id="switch_video2<?php echo $grade;?>" class="radioskin" value="1"<?php echo ($switch['sh']['video_'.$grade] == 1)?' checked':'';?>>
              <label for="switch_video2<?php echo $grade;?>" class="radioskin-label"><i class="i1"></i><b class="W80 S14">直接通过</b></label></td>
          </tr>
          <?php }?>
        </table>
      <?php }else{echo "<div class='nodatatipsS'>暂无会员组<br><br><a class='btn size1' href='urole.php'>新增会员组</a></div>";}?></td>
	</tr>
    <!---->
</table>
<input name="submitok" type="hidden" value="cache_switch">
<input name="uu" id="uu" type="hidden" value="<?php echo $session_uid;?>">
<input name="pp" id="pp" type="hidden" value="<?php echo $session_pwd;?>">
<br><br><br><br><br>
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
<script>
	save.onclick = function(){
        var DATAPX=[];
        zeai.listEach('.chatContact',function(obj){DATAPX.push(obj.value);});
        chatContact_data_px.value=DATAPX.join(",");
        var DATAPX2=[];
        zeai.listEach('.viewhomepage',function(obj){DATAPX2.push(obj.value);});
        viewhomepage_data_px.value=DATAPX2.join(",");
		
        var DATAPX3=[];
        zeai.listEach('.hi',function(obj){DATAPX3.push(obj.value);});
        hi_data_px.value=DATAPX3.join(",");
		
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){
			var rs=zeai.jsoneval(e);zeai.msg(0);zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		});
	}
	if (!zeai.empty(o('wxbgpicdel')))o('wxbgpicdel').onclick = function(){delpic('cache_config_del_wxbgpic');}
	if (!zeai.empty(o('wapbgpicdel')))o('wapbgpicdel').onclick = function(){delpic('cache_config_del_wapbgpic');}
	function delpic(submitok){
		zeai.confirm('确认要删除么？',function(){
			zeai.msg('删除中...',{time:20});
			zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:submitok,uu:uu.value,pp:pp.value}},function(e){
				var rs=zeai.jsoneval(e);
				zeai.msg(0);
				if (rs.flag == 1){location.reload(true);}else{zeai.alert(rs.msg);}
			});
		});
	}
</script>

<!----------------扩展---------------->
<?php }elseif($t == 4){ ?>

<?php }?>
</form>
<script>
<?php if(ifint($top)){ ?>
zeai.setScrollTop(<?php echo $top;?>);
<?php }?>
</script>
<?php
require_once 'bottomadm.php';
function reg_data_title($var) {
	switch ($var){
		case 'sex':$t = '性别';break;
		case 'birthday':$t = '生日';break;
		case 'areaid':$t = '地区';break;
		case 'heigh':$t = '身高';break;
		case 'weigh':$t = '体重';break;
		case 'edu':$t = '学历';break;
		case 'love':$t = '婚况';break;
		case 'job':$t = '职业';break;
		case 'car':$t = '购车';break;
		case 'house':$t = '住房';break;
		case 'truename':$t = '姓名';break;
		case 'nickname':$t = '昵称';break;
		case 'weixin':$t = '微信';break;
		case 'photo_s':$t = '照片';break;
		case 'mate':$t = '择偶要求';break;
		case 'aboutus':$t = '自我介绍';break;
		case 'child':$t = '子女情况';break;
		case 'marrytime':$t = '结婚时间';break;
		case 'pay':$t = '月收入';break;
		case 'identitynum':$t = '身份证号';break;
		case 'kefu':$t = '客服红娘';break;
		case 'parent':$t = '父母亲友帮征婚';break;
		case 'area2id':$t = '户籍';break;
	}
	return $t;
}
function chatContact_data_title($var) {
	switch ($var){
		case 'rz_mob':$t = '完成手机认证';break;
		case 'rz_identity':$t = '完成实名认证';break;
		case 'rz_photo':$t = '完成真人认证';break;
		case 'bfb':$t = '资料完整度达';break;
		case 'photo':$t = '有头像且已审';break;
		case 'sex':$t = '对方必须异性';break;
		case 'mysex1':$t = '发送方为男性';break;
		case 'mysex2':$t = '发送方为女性';break;
		case 'vip':$t = 'VIP会员';break;
	}
	return $t;
}
?>
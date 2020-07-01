<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'sub/TGfun.php';$tg=json_decode($_REG['tg'],true);

if ( !ifint($uid))json_exit(JSON_ERROR);
$t = (ifint($t,'1-3','1'))?$t:1;

$parameter = "uid=".$uid."&kind=".$kind."&t=".$t."&sort=".$sort."&date1=".$date1."&date2=".$date2."&Skeyword=".$Skeyword;
if ($submitok == 'ajax_tg_flag1'){
	if (!ifint($subuid1))json_exit(JSON_ERROR);
	$tguid = $uid;
	$uid   = $subuid1;
	if(ifint($tguid)){
		TG($tguid,$uid,'reg',1);
		json_exit(array('flag'=>1,'msg'=>'验证成功'));
	}
}

$SQL = "";
switch ($t){
	case 2:$SQL = " AND tgflag=1";break;
	case 3:$SQL = " AND tgflag=0";break;
}
switch ($sort) {
	case 'time0':$ORDER = " ORDER BY id ";break;
	case 'time1':$ORDER = " ORDER BY id DESC ";break;
	case 'flag0':$ORDER  = " ORDER BY tgflag,id DESC ";break;
	case 'flag1':$ORDER  = " ORDER BY tgflag DESC,id DESC ";break;
	default:$ORDER = " ORDER BY id DESC ";break;
}
if (!empty($Skeyword))$SQL .= " AND (( mob LIKE '%".trimm($Skeyword)."%' ) OR ( id LIKE '%".trimm($Skeyword)."%' ) OR ( nickname LIKE '%".trimm($Skeyword)."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' )) ";
if (!empty($date1))$SQL .= " AND (regtime >= ".strtotime($date1.'00:00:01').") ";
if (!empty($date2))$SQL .= " AND (regtime <= ".strtotime($date2.'23:59:59').") ";
$rt = $db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,heigh,regtime,tgflag,uname,flag,myinfobfb,regkind FROM ".__TBL_USER__." WHERE tguid=".$uid.$SQL.$ORDER);
$total = $db->num_rows($rt);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist font{display:inline-block;padding:2px 5px;font-size:12px;border-radius:1px;vertical-align:middle}
.tablelist font.flag1{background-color:#45C01A;color:#fff}
.tablelist font.flag0{color:#999}
</style>
<body>
<div class="navbox" style="background-color:#F8F8F8">
	<?php $thref = "$SELF?uid=$uid&t=";?>
    <a href="<?php echo $thref; ?>1"<?php echo (empty($t) || $t==1)?" class='ed'":""; ?>>全部<?php if (empty($t) || $t==1){echo '<b>'.$total.'人</b>';}else{echo '<b class="border">'.$db->COUNT(__TBL_USER__,"tguid=".$uid).'人</b>';}?></a>
    <a href="<?php echo $thref; ?>2"<?php echo ($t==2)?" class='ed'":""; ?>>已验证<?php if ($t == 2){echo '<b>'.$total.'人</b>';}else{echo '<b class="border">'.$db->COUNT(__TBL_USER__,"tgflag=1 AND tguid=".$uid).'人</b>';};?></a>
    <a href="<?php echo $thref; ?>3"<?php echo ($t==3)?" class='ed'":""; ?>>未验证<?php if ($t == 3){echo '<b>'.$total.'人</b>';}else{echo '<b class="border">'.$db->COUNT(__TBL_USER__,"tgflag=0 AND tguid=".$uid).'人</b>';};?></a>
<div class="clear"></div></div><div class="fixedblank"></div>

<?php
if ($total <= 0 ) {
	echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
	if (!empty($SQL))echo "<br><br><a class='aQINGed' href='".SELF."?".$parameter."'>重新筛选</a>";
    echo "</div>";
} else {    
	$page_skin = 1;$pagesize=20;require_once ZEAI.'sub/page.php';
?>

<table class="table0 W95_ Mtop10">
    <tr>
    <td align="left" class="S14">
      <form name="www—yzlove—com.v6.1..QQ797243" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按会员ID/昵称" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="date1" value="<?php echo $date1;?>" />
        <input type="hidden" name="date2" value="<?php echo $date2;?>" />
        <input type="hidden" name="uid" value="<?php echo $uid;?>" />
        <input type="hidden" name="sort" value="<?php echo $sort;?>" />
        <input type="hidden" name="t" value="<?php echo $t;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>   
    </td>
    <td align="right" class="S14">
      <form name="www-zeai-cn.v6.2..QQ797311" method="get" action="<?php echo $SELF; ?>">
        <input name="date1" type="text" id="date1" maxlength="25" class="input size2 W100" placeholder="起始时间" value="<?php echo $date1; ?>" autocomplete="off"> ～ 
        <input name="date2" type="text" id="date2" maxlength="25" class="input size2 W100" placeholder="结束时间" value="<?php echo $date2; ?>" autocomplete="off">
        <input type="hidden" name="Skeyword" value="<?php echo $Skeyword;?>" />
        <input type="hidden" name="uid" value="<?php echo $uid;?>" />
        <input type="hidden" name="sort" value="<?php echo $sort;?>" />
        <input type="hidden" name="t" value="<?php echo $t;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>   
    </td>
    </tr>
</table>

<?php $sorthref = SELF."?uid=$uid&t=$t&sort=";?>
<table class="tablelist W95_ Mtop10 ">
    <tr>
        <th width="60">推荐的会员</th>
        <th align="left">&nbsp;</th>
        <th width="130" align="center">资料完整度/注册状态</th>
        <th width="70">
            注册时间<div class="sort">
                <a href="<?php echo $sorthref."time0";?>" <?php echo($sort == 'time0')?' class="ed"':''; ?>></a>
                <a href="<?php echo $sorthref."time1";?>" <?php echo($sort == 'time1')?' class="ed"':''; ?>></a>
            </div>
        </th>
        <th width="80" align="center">
            审核状态<div class="sort">
                <a href="<?php echo $sorthref."flag0";?>" <?php echo($sort == 'flag0')?' class="ed"':''; ?>></a>
                <a href="<?php echo $sorthref."flag1";?>" <?php echo($sort == 'flag1')?' class="ed"':''; ?>></a>
            </div>
        </th>
        <th width="120" align="center">确认有效性操作</th>
	</tr>
	<?php
	if ($total > 0) {
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'all');
			if(!$rows)break;
			$subuid1  = $rows[0];
			//
			$nickname = dataIO($rows[1],'out');
			$nickname = str_replace($Skeyword,"<b class='Cf00'>".$Skeyword."</b>",$nickname);
			$sex      = $rows[2];
			$grade    = $rows[3];
			$photo_s  = $rows[4];
			$photo_f  = $rows[5];
			$areatitle= $rows[6];
			$birthday = $rows[7];
			$heigh    = $rows[8];
			$regtime  = YmdHis($rows[9]);
            $tgflag   = $rows[10];
            $uname   = dataIO($rows[11],'out');
			$flag = $rows['flag'];
			$myinfobfb = $rows['myinfobfb'];
			$regkind = $rows['regkind'];
			$nickname=(empty($nickname))?$uname:$nickname;
			//
			$birthday_str  = (@getage($birthday)<=0)?'':getage($birthday).'岁 ';
			$heigh_str     = (empty($heigh))?'':$heigh.'cm ';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			//
			$href        = Href('u',$subuid1);
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
			$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			if($tgflag == 1){
				$tgflag_str = '<font class="flag1">成功</font>';
				$tgbtn_str  = '';
			}else{
				$tgflag_str = '<i class="timeico20"></i><font class="flag0">等待验证</font>';
				$tgbtn_str  = '<button subuid1="'.$subuid1.'" type="button" class="btn size2 HUANG3 qq797311">验证并打款</button>';
			}
	?>
    <tr>
		<td width="60" align="left"><a href="<?php echo $href;?>" class="pic50 yuan border0" target="_blank"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></a></td>
		<td align="left" class="lineH150 C999"><a href="<?php echo $href;?>" target="_blank" class="S14"><?php echo uicon($sex.$grade) ?> <?php echo $nickname; ?> <font class="S12 C999">(UID:<?php echo $subuid1;?>)</font></a><br><?php echo $birthday_str.$heigh_str; ?><?php echo $areatitle_str; ?></td>
		<td width="130" align="center" class="lineH150 C999">
        	<span class="S14 <?php if($myinfobfb >80){echo ' myinfobfb2 ';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>"><?php echo $myinfobfb;?>%</span>
            <?php if ($flag==2){
				echo '<br>';
				 if ($rows['regkind'] == 3){echo '关注未注册';}else{echo '注册未完成';}
			}elseif($flag==1){echo '<br><font class="C090">正常</font>';}?>
        </td>
        <td width="70" class="lineH150 C999"><?php echo $regtime; ?></td>
        <td width="80" align="center"><?php echo $tgflag_str; ?></td>
        <td width="120" align="center"><?php echo $tgbtn_str;?></td>
    </tr>
	<?php }?>
	<?php if ($total > $pagesize){?>
    <tfoot>
    <tr>
    <td colspan="6"><?php if ($total > $pagesize)echo '<div class="pagebox">'.$pagelist.'</div>'; ?></div></td>
    </tr>
    </tfoot>
    <?php }?>
    <?php } ?>
</table>
<?php }?>
<br><br>
<script>
zeai.listEach('.qq797311',function(obj){obj.onclick = function(){
	zeai.confirm('确认要验证通过么？验证后将进行打款并进行消息通知',function(){
		zeai.msg('正在验证/发送通知...',{time:20});
		zeai.ajax('u_tg_ulist'+zeai.ajxext+'submitok=ajax_tg_flag1&uid='+<?php echo $uid;?>+'&subuid1='+obj.getAttribute("subuid1"),function(e){rs=zeai.jsoneval(e);
			if (rs.flag == 1){setTimeout(function(){zeai.msg(rs.msg);location.reload(true);},1000);}else{zeai.msg(0);zeai.alert(rs.msg);}
		});
	});
}});
</script>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem:'#date1',type: 'date'});
laydate.render({elem:'#date2',type: 'date'});
</script>
<?php require_once 'bottomadm.php';?>

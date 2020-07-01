<?php
require_once '../sub/init.php';
header("Cache-control: private");
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('u_tg',$QXARR))exit(noauth());

require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
require_once ZEAI.'sub/TGfun.php';

if ($submitok == 'ajax_tg_flag1'){
	if (!ifint($uid) || !ifint($tguid))json_exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TG_USER__,"id","id=".$tguid);
	if (!$row){
		$db->query("UPDATE ".__TBL_USER__." SET tguid=0,tgflag=0 WHERE id=".$uid);
		json_exit(array('flag'=>1,'msg'=>'推广员不存在，清理处理成功！'));
	}
	reward_tj($uid);
	TG($tguid,$uid,'reg',1);
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_TG_USER__,"nickname","id=".$tguid,'num');$tgnickname= $row2[0];
	AddLog('推广验证(会员)【推荐人：'.$tgnickname.'（id:'.$tguid.'）】->【会员：'.$nickname.'（uid:'.$uid.'）】验证成功');
	json_exit(array('flag'=>1,'msg'=>'验证成功'));
}elseif($submitok == 'ajax_tg_clear'){
	$rt=$db->query("SELECT id,tguid FROM ".__TBL_USER__." WHERE tguid>9999");//
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$idid  = $rows['id'];
			$tguid = $rows['tguid'];
			
			$row3 = $db->ROW(__TBL_USER__,"id","id=".$tguid);
			if (!$row3){
				$db->query("UPDATE ".__TBL_USER__." SET tguid=0,tgflag=0 WHERE id=".$idid);
				continue;	
			}
			//////////////////////////////////
			$ifadd=true;
			if(ifint($tguid)){
				$rowtg = $db->ROW(__TBL_TG_USER__,"id","uid=".$tguid,"name");
				//如果新推广库没有，绑定会员uid，调出老推广员会员资料
				//新增
				if (!$rowtg){
					$rowU = $db->ROW(__TBL_USER__,"uname,pwd,mob,RZ,openid,subscribe,weixin,qq,aboutus,areaid,areatitle,photo_s,money,tgallmoney","id=".$tguid,"name");
					$uname=$rowU['uname'];
					$pwd  = $rowU['pwd'];
					$mob  = $rowU['mob'];
					$openid    =$rowU['openid'];
					$subscribe =$rowU['subscribe'];
					$RZ      = $rowU['RZ'];
					$RZarr   = explode(',',$RZ);
					$weixin  = $rowU['weixin'];
					$qq      = $rowU['qq'];
					$aboutus = $rowU['aboutus'];
					$areaid = $rowU['areaid'];
					$areatitle = $rowU['areatitle'];
					//$photo_s = $rowU['photo_s'];
					$money = $rowU['money'];
					$tgallmoney = $rowU['tgallmoney'];
					//如果手机在新库存在，跳过
					if(ifmob($mob) && in_array('mob',$RZarr)){
						$row2 = $db->ROW(__TBL_TG_USER__,"id","mob='$mob' AND FIND_IN_SET('mob',RZ)");
						if($row2)$ifadd=false;
					}
					//
					if($ifadd){
							$row2 = $db->ROW(__TBL_TG_USER__,"id","uname='$uname'");
							if($row2)$uname=$tguid;
							//
							$TG_set = json_decode($_REG['TG_set'],true);
							$flag   = ($TG_set['regflag'] == 1)?0:1;
							$row2   = $db->ROW(__TBL_TG_ROLE__,"grade,title","ifdefault=1","num");
							$grade  = $row2[0];
							$gradetitle  = $row2[1];
							$sjtime = ADDTIME;
							$ip     =getip();
							$kind   = 1;
							if($TG_set['active_price']>0)$flag=2;
							$db->query("INSERT INTO ".__TBL_TG_USER__." (money,tgallmoney,uid,uname,flag,pwd,grade,gradetitle,regtime,endtime,regip,endip,kind,openid,qq,weixin,content,areaid,areatitle,photo_s) VALUES ('$money','$tgallmoney',$tguid,'$uname',$flag,'".$pwd."',$grade,'$gradetitle',".ADDTIME.",".ADDTIME.",'$ip','$ip',$kind,'$cook_tg_openid','$qq','$weixin','$aboutus','$areaid','$areatitle','$photo_s')");
							$tg_uid = $db->insert_id();
							if(ifmob($mob) && in_array('mob',$RZarr)){
								$db->query("UPDATE ".__TBL_TG_USER__." SET mob='$mob',RZ='mob' WHERE id=".$tg_uid);
							}
							
							$db->query("UPDATE ".__TBL_USER__." SET tguid='$tg_uid' WHERE id=".$idid);
							$db->query("UPDATE ".__TBL_USER__." SET money=0,tgallmoney=0 WHERE id=".$tguid);
							
							//
					}
				}else{
					$db->query("UPDATE ".__TBL_USER__." SET tguid='".$rowtg['id']."' WHERE id=".$idid);
				}
				
			}
			////////////////////////////////
		}
	}
	json_exit(array('flag'=>1,'msg'=>'同步成功'));
}elseif($submitok == 'sendupdate'){
	if ( !ifint($uid) )textmsg('UID：'.$uid.'不存在');
	if ( !ifint($tguid) )textmsg('ID：'.$tguid.'不存在');
	if (str_len($title) > 0 && str_len($content) > 0 && str_len($content) <1000){
		$title   = dataIO($title,'in',100);
		//$content = dataIO($content,'in',1000);
	}else{
		textmsg('请输入发送内容500字节以内');
	}
	$row = $db->ROW(__TBL_TG_USER__,'openid,subscribe',"id=".$tguid,"num");
	if(!$row){
		textmsg('tguid：'.$tguid.'不存在，发送中断');
	}else{
		$openid = $row[0];$subscribe = $row[1];
	}
	$db->query("UPDATE ".__TBL_USER__." SET tgflag=2 WHERE id=".$uid);
	
	//
	$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
	$row2 = $db->ROW(__TBL_TG_USER__,"nickname","id=".$tguid,'num');$tgnickname= $row2[0];
	AddLog('推广验证(会员)【推荐人：'.$tgnickname.'（id:'.$tguid.'）】->【会员：'.$nickname.'（uid:'.$uid.'）】->驳回，驳回内容：'.dataIO($content,'in',1000));
	
	//站内消息
	$db->SendTip($tguid,$title,dataIO($content,'in',1000),'tg');

	//微信通知
	if (!empty($openid) && $subscribe==1){
		//客服通知
		$C = urlencode($content);
		$ret = @wx_kf_sent($openid,$C,'text');
		$ret = json_decode($ret);
		echo '<font color="#fff">'.$ret->errmsg.'</font>';
		//模版通知
		if ($ret->errmsg != 'ok'){
			$keyword1  = $C;
			$keyword3  = urlencode($_ZEAI['siteName']);
			//$url       = urlencode(mHref('my_tz'));
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
		}
	}
	$sussess = '驳回成功!';
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
<body>
<?php
if (!empty($sussess)){?>
	<style>.sussesstips{width:300px;margin:0 auto;padding-top:100px;font-size:24px;text-align:center}</style>
	<?php
	echo '<div class="sussesstips"><img src="images/sussess.png"><br><br>'.$sussess.'<br><br><br><br><a class="btn HUANG3 size4" href="javascript:window.parent.location.reload(),window.parent.zeai.iframe(0);">关闭并刷新父窗口</a></div>';exit;}
if($submitok == 'pass'){?>
	<style>
    .table0{width:90%;margin:20px auto}
    .table0 td{font-size:14px;padding:10px 5px}
    .table0 td:hover{background:none}
    .table0 .tdL{width:70px;font-size:14px;color:#666;background:none}
    .table0 .input{width:400px}
    .table0 textarea{width:400px;height:150px}
    </style>
	<script>
    function chkform(){
        if(zeai.str_len(title.value)<1 || zeai.str_len(title.value)>100){
            zeai.msg('消息标题1~100个字节',title);
            return false;
        }else if( zeai.str_len(content.value)<1 || zeai.str_len(content.value)>1000 ){
            zeai.msg('消息内容1~1000个字节',content);
            return false;
        }else{
            parent.zeai.confirm('请仔细检查发送内容，一经发送不可逆转',function(){zeai.msg('发送中... 请不要关闭窗口',{time:100});www_zeai_cn_FORM.submit();})
        }
    }
    </script>
    <form id="www_zeai_cn_FORM" method="post" action="<?php echo SELF; ?>">
        <table class="table0">
        <tr>
        <td class="tdL">接收人</td>
        <td class="tdR"><?php echo $tguid; ?></td>
        </tr>
        <tr>
        <td class="tdL">消息标题</td>
        <td class="tdR"><input name="title" type="text" required class="size2 W90_" id="title" maxlength="50"></td>
        </tr>
        <tr>
        <td valign="top" class="tdL">消息内容</td>
        <td class="tdR"><textarea name="content" id="content" class="S14" style="width:90%"></textarea></td>
        </tr>
        <tr>
        <td valign="top" class="tdL">&nbsp;</td>
        <td class="tdR C8d">
        <input type="checkbox" name="ifwxmb" id="ifwxmb" class="checkskin" value="1" checked><label for="ifwxmb" class="checkskin-label"><i></i><b class="W100 S14 C666">微信通知</b></label></td>
        </tr>
        <tr>
        <td height="60" colspan="2" class="center"><input type="button" value="开始驳回" class="btn size3 HUANG3" onclick="chkform()"></td>
        </tr>
        </table>
        <input type="hidden" name="submitok" value="sendupdate">
        <input type="hidden" name="tguid" value="<?php echo $tguid; ?>">
        <input type="hidden" name="uid" value="<?php echo $uid; ?>">
    </form>
    
    
<?php exit;}?>
<style>
.tablelist{min-width:1111px;margin:0 20px 50px 20px}
.table0{min-width:1111px;width:98%;margin:10px 20px 20px 20px}
.mtop{ margin-top:10px;}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
.SW{width:100px;}
.SW_area{width:120px;vertical-align:middle}
.SW_house{width:160px}
.RCW{display:inline-block}
.RCW li{width:80px}
i.wxlv{color:#31C93C;margin-right:2px}
.forcerz{margin-top:10px}
</style>
<?php
$SQL = "tguid>0";
$Skeyword = trimhtml($Skeyword);
//搜索
$Skeyword = trimhtml($Skeyword);
if (!empty($Skeyword)){
	if(ifmob($Skeyword)){
		$SQL .= " AND mob=".$Skeyword;
	}elseif(ifint($Skeyword)){
		$SQL .= " AND (id=".$Skeyword." OR tguid=".$Skeyword.")";	
	}else{
		$SQL .= " AND ( ( truename LIKE '%".$Skeyword."%' ) OR ( uname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%') )";
	}
}
if (!empty($date1))$SQL .= " AND (regtime >= ".strtotime($date1.'00:00:01').") ";
if (!empty($date2))$SQL .= " AND (regtime <= ".strtotime($date2.'23:59:59').") ";
if($t==2){
	$SQL .= " AND tgflag=2";	
}else{
	$SQL .= " AND tgflag=0";	
}
$rt = $db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,heigh,mob,regtime,tgflag,uname,flag,myinfobfb,regkind,tguid,regip,subscribe,RZ FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY myinfobfb DESC,id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
$total0 = $db->COUNT(__TBL_USER__,"tguid>0 AND tgflag=0");
$total2 = $db->COUNT(__TBL_USER__,"tguid>0 AND tgflag=2");
$total_tg = $db->COUNT(__TBL_TG_USER__,"tguid>0 AND tgflag=0");
?>

<div class="navbox">
    <a href="u_tg.php"<?php echo (empty($t))?' class="ed"':'';?>>单身用户验证审核（未审）<?php if($total0>0)echo '<b>'.$total0.'</b>';?></a>
    <a href="u_tg.php?t=2"<?php echo ($t == 2)?' class="ed"':'';?>>单身用户验证审核（已驳回）<?php if($total2>0)echo '<b>'.$total2.'</b>';?></a>
    <a href="tg_tg.php">合伙人验证审核<?php if($total_tg>0)echo '<b>'.$total_tg.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0">
    <tr>
    <td width="320" align="left" class="border0 S14">    <form name="form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按推荐人UID/用户名/手机/姓名/昵称筛选" value="<?php echo $Skeyword; ?>">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
        </form>   
    </td>
    <td align="center" class="border0 S14"><button type="button" class="btn size2" id="clearold">一键同步老记录</button></td>
    <td width="400" align="right" class="border0 S14" >
    
      <form name="www-zeai-cn.v6.2..QQ797311" method="get" action="<?php echo $SELF; ?>">
        按注册时间 <input name="date1" type="text" id="date1" maxlength="25" class="input size2 W100" placeholder="起始时间" value="<?php echo $date1; ?>" autocomplete="off"> ～ 
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
<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	$sorthref = SELF."?".$parameter."&sort=";
?>

    <form id="zeaiFORM" method="get" action="<?php echo $SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="30"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="150">推荐人(推广员)</th>
    <th width="34">&nbsp;</th>
    <th width="80" align="center">单身UID/认证</th>
    <th width="70" class="center">头像　</th>
    <th>单身昵称/资料/手机</th>
	<th width="90" align="center" class="center">单身资料完整度</th>
	<th width="90" align="center" class="center">关注公众号</th>
	<th width="90" align="center" class="center">单身注册状态</th>
    <th width="120" align="center" class="center">单身注册时间/IP</th>
    <th width="150" align="center">确认单身有效性操作</th>
    <th width="120" class="center">驳回</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
			$id  = $rows['id'];
			$uid  = $id ;
			//
			$nickname = dataIO($rows['nickname'],'out');
			$nickname = str_replace($Skeyword,"<b class='Cf00'>".$Skeyword."</b>",$nickname);
			$sex      = $rows['sex'];
			$grade    = $rows['grade'];
			$photo_s  = $rows['photo_s'];
			$photo_f  = $rows['photo_f'];
			$areatitle= $rows['areatitle'];
			$birthday = $rows['birthday'];
			$heigh    = $rows['heigh'];
			$regtime  = YmdHis($rows['regtime']);
            $tgflag   = $rows['tgflag'];
            $uname   = dataIO($rows['uname'],'out');
            $mob   = dataIO($rows['mob'],'out');
			$flag = $rows['flag'];
			$myinfobfb = $rows['myinfobfb'];
			$regkind = $rows['regkind'];
			$regip = $rows['regip'];
			$tguid = $rows['tguid'];
			$subscribe = $rows['subscribe'];
			$RZZ = $rows['RZ'];
			$nickname=(empty($nickname))?$uname:$nickname;
			//
			$birthday_str  = (@getage($birthday)<=0)?'':getage($birthday).'岁 ';
			$heigh_str     = (empty($heigh))?'':$heigh.'cm ';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle.' ';
			$mob_str       = (!ifmob($mob))?'':$mob;
			//
			$href        = Href('u',$uid);
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
			if($tgflag == 1){
				$tgflag_str = '<font class="flag1">成功</font>';
				$tgbtn_str  = '';
			}else{
				$tgflag_str = '<i class="timeico20 picmiddle"></i> <font class="flag0 picmiddle">等待验证</font><br>';
				$tgbtn_str  = '<button uid="'.$uid.'" tguid="'.$tguid.'" type="button" class="btn size2 HUANG3 qq797311" style="margin-top:5px">验证通过</button>';
			}
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="30"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
        </td>
      <td width="150">
  <?php
$row2 = $db->ROW(__TBL_TG_USER__,"uname,mob,money,tgallmoney","id=".$tguid);
if ($row2){
	$tguname2 = $row2[0];$tgmob2= dataIO($row2[1],'out');$money= $row2[2];;$tgallmoney= $row2[3];
	$tguname2 = (empty($tguname2))?$tgmob2:$tguname2;
	//echo $tguname2;
	echo 'ID：'.$tguid;
	echo '<br><font class="C999">当前余额：</font><font class="Cf00">￥'.str_replace(".00","",$money).'</font>';
	echo '<br><font class="C999">累计余额：</font><font class="C090">￥'.str_replace(".00","",$tgallmoney).'</font>';
}else{
	
	echo '<br>老ID：'.$tguid;
}
?>      </td>
      <td width="34"><img src="images/d2.gif"></td>
      <td width="80" align="center"><?php echo $id;?><?php if(!empty($RZZ)){?><div title="认证" uid="<?php echo $id;?>" class="forcerz hand"><?php echo RZ_html($RZZ,'s','color');?></div><?php }?></td>
        <td width="70" class="center" style="padding:10px 0">
        <a href="<?php echo $href;?>" target="_blank"><img src="<?php echo $photo_s_url; ?>" class="m"></a>
        </td>
        <td align="left" class="lineH150 C999"><a href="<?php echo $href;?>" target="_blank" class="S14"><?php echo uicon($sex.$grade) ?> <?php echo $nickname; ?></a><br><?php echo $birthday_str.$heigh_str.$areatitle_str.$mob_str; ?></td>
      <td width="90" align="center" class=" C999"><span class="S14 <?php if($myinfobfb >80){echo ' myinfobfb2 ';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>"><?php echo $myinfobfb;?>%</span></td>
      <td width="90" align="center" class=" C999"><?php
if($subscribe==0){
	echo '<span class="C999"></span>';
}elseif($subscribe==1){
	echo '<i class="ico S14 wxlv">&#xe6b1;</i>';
}else{
	echo '<span class="C00f">取消</span>';
}
?></td>
      <td width="90" align="center" class="lineH150"><?php if ($flag==2){if ($regkind == 3){echo '关注未注册';}else{echo '注册未完成';}}elseif($flag==1){echo'<font class="C090">'.flagtitle(1).'</font>';}else{echo flagtitle($flag);}?></td>
      <td width="120" align="center" class="lineH150 C999"><?php echo $regtime; ?><br><?php echo $regip;?></td>
      <td width="150" align="center" class="lineH150 C666" style="padding:10px 0"><?php echo $tgflag_str.$tgbtn_str;?></td>
      <td width="120" align="center" class="center"><br><button uid="<?php echo $uid;?>" tguid="<?php echo $tguid;?>" type="button" class="btn size2 HUI qq7144100" style="margin-top:5px"><?php echo ($t == 2)?'通知推广员':'驳回';?></button></td>
    </tr>
	<?php } ?>
</table>
<div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action"><i class="ico">&#xe676;</i> 发送消息</button>
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
</div>
<script>var bg = '<?php echo $_Style['list_bg']; ?>',overbg   = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</form>
<?php }?>
<br><br><br>
<script>
if(!zeai.empty(o('btnsend')))btnsend.onclick = function() {
	if (this.hasClass('disabled')){
		zeai.alert('请选择要发送的会员');
		return false;
	}
	var arr  = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){if (arr[key].checked)ulist.push(arr[key].value);}
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的会员');
	}else{
		zeai.iframe('发送消息','u_tip.php?ulist='+ulist,600,500);
	}
}
zeai.listEach('.qq797311',function(obj){obj.onclick = function(){
	zeai.confirm('确认要验证通过么？验证成功后将把奖励金额入账到推广员【余额账户】并进行消息通知',function(){
		zeai.msg('正在验证/发送通知...',{time:20});
		var uid=obj.getAttribute("uid");
		var tguid=obj.getAttribute("tguid");
		zeai.ajax('u_tg'+zeai.ajxext+'submitok=ajax_tg_flag1&uid='+uid+'&tguid='+tguid,function(e){var rs=zeai.jsoneval(e);
			if (rs.flag == 1){zeai.msg(0);zeai.msg(rs.msg);setTimeout(function(){zeai.msg(rs.msg);location.reload(true);},1000);}else{zeai.msg(0);zeai.alert(rs.msg);}
		});
	});
}});
zeai.listEach('.qq7144100',function(obj){obj.onclick = function(){
	//zeai.confirm('确认要驳回么？',function(){
		var uid=obj.getAttribute("uid");
		var tguid=obj.getAttribute("tguid");
		zeai.iframe('驳回推荐人【'+tguid+'】','u_tg.php?submitok=pass&uid='+uid+'&tguid='+tguid,600,500);
	//});
}});
clearold.onclick=function(){
	zeai.confirm('确认同步么？确认后，将自动将老推广员新增到独立新库中【请只能点一次，否则会导致记录混乱，切记，如果是新站不要操作】',function(){
		zeai.ajax('u_tg'+zeai.ajxext+'submitok=ajax_tg_clear',function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.alert(rs.msg);
			if (rs.flag == 1){setTimeout(function(){zeai.msg(rs.msg);location.reload(true);},1000);}
		});
	});
}
</script>
<script src="laydate/laydate.js"></script>
<script>
lay('#version').html('-v'+ laydate.v);
laydate.render({elem:'#date1',type: 'date'});
laydate.render({elem:'#date2',type: 'date'});
</script>
<?php
require_once 'bottomadm.php';
function reward_tj($uid) {
	global $db,$TG_set;
	$tjARR  = explode(',',$TG_set['reward_tj']);
	$row = $db->ROW(__TBL_USER__,"openid,subscribe,myinfobfb,RZ","id=".$uid,"name");
	if (!$row)return false;
	$openid    = $row['openid'];
	$subscribe = $row['subscribe'];
	$myinfobfb = $row['myinfobfb'];
	$RZ = $row['RZ'];$RZarr = explode(',',$RZ);
	if(in_array('gzh',$tjARR)){
		if($subscribe==0 || $subscribe==2)json_exit(array('flag'=>0,'msg'=>'不满足条件【公众号未关注】'));//str_len($openid)<10 || 
		//return false;
	}
	$tj_bfb = intval($TG_set['reward_tj_bfb']);
	if(in_array('bfb',$tjARR) && $tj_bfb>0 && $tj_bfb<=100){
		if($myinfobfb < $tj_bfb)json_exit(array('flag'=>0,'msg'=>'不满足条件【资料完整度未达标'.$tj_bfb.'％】'));
	}
	if(in_array('rz_mob',$tjARR)){
		if(!in_array('mob',$RZarr))json_exit(array('flag'=>0,'msg'=>'不满足条件【未完成手机认证】'));
	}
	if(in_array('rz_identity',$tjARR)){
		if(!in_array('identity',$RZarr))json_exit(array('flag'=>0,'msg'=>'不满足条件【未完成身份认证】'));
	}
	if(in_array('rz_edu',$tjARR)){
		if(!in_array('edu',$RZarr))json_exit(array('flag'=>0,'msg'=>'不满足条件【未完成学历认证】'));
	}
	return true;
}
?>


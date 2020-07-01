<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';

if(!in_array('crm_match_list',$QXARR))exit(noauth());
$meet_ifagreeARR=json_decode($_CRM['meet_ifagree'],true);
$meet_flagARR = json_decode($_CRM['meet_flag'],true);

$SQL   = " a.uid=b.id AND b.kind<>4 AND b.crm_flag<3 ";
$Skey  = trimhtml($Skey);
$Skey2 = trimhtml($Skey2);
$Skey3 = trimhtml($Skey3);
if (ifmob($Skey)){
	$SQL .= " AND (b.mob=$Skey) ";
}elseif(ifint($Skey)){
	$SQL .= " AND (b.id=$Skey OR a.uid2=$Skey) ";
}elseif(!empty($Skey)){
	$SQL .= " AND ( ( b.uname LIKE '%".$Skey."%' ) OR ( b.nickname LIKE '%".$Skey."%' ) OR ( b.nickname LIKE '%".urlencode($Skey)."%' ) )";
}
if(ifint($Skey2)){
	$SQL .= " AND (a.admid=$Skey2) ";
}elseif(!empty($Skey2)){
	$SQL .= " AND (  a.admname LIKE '%".$Skey2."%' )";
}
if(!empty($Skey3))$SQL .= " AND (  a.fwfs LIKE '%".$Skey3."%' OR a.fkqk LIKE '%".$Skey3."%' )";
if (ifint($mt_flag))$SQL .= " AND a.meet_flag = $mt_flag ";
if (ifint($mt_jwang))$SQL .= " AND a.meet_flag3 = $mt_jwang ";	

//时间
if(!empty($sDATE1)){
	$sDATE1_ = strtotime($sDATE1.' 00:00:01');
	$SQL .= " AND ( a.addtime >= '$sDATE1_' )";
}
if(!empty($sDATE2)){
	$sDATE2_ = strtotime($sDATE2.' 23:59:59');
	$SQL .= " AND ( a.addtime <= '$sDATE2_' )";
}

switch ($mt_time) {
	case 1:$SQL .= " AND (".ADDTIME." - a.addtime) > 604800 ";break;
	case 2:$SQL .= " AND (".ADDTIME." - a.addtime) > 2592000 ";break;
	case 3:$SQL .= " AND (".ADDTIME." - a.addtime) > 7948800 ";break;
	case 4:$SQL .= " AND ( TO_DAYS(from_unixtime(a.addtime))-TO_DAYS(NOW()) = 0 )";break;//今天已约见
	case 5:$SQL .= " AND ( TO_DAYS(from_unixtime(a.addtime))-TO_DAYS(NOW()) = -1 )";break;//昨天已约见
}
switch ($mt_nexttime) {
	case 1:$SQL .= " AND ( TO_DAYS(from_unixtime(a.nexttime))-TO_DAYS(NOW()) = 0 )";break;//今天需约见
	case 2:$SQL .= " AND ( TO_DAYS(from_unixtime(a.nexttime))-TO_DAYS(NOW()) = 1 )";break;//明天需约见
	case 3:$SQL .= " AND ( TO_DAYS(from_unixtime(a.nexttime))-TO_DAYS(NOW()) = 2 )";break;//后天需约见
	case 4:$SQL .= " AND a.nexttime < ".ADDTIME;break;//过期
}

/*-------------------------------------------------------*/
//门店+地区
$SQL .= getAgentSQL('b');
//超管可以按门店搜索
if(ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND b.agentid=$agentid";
//我的
if($ifmy==1)$SQL .= " AND b.hnid2=".$session_uid;

//iframenav
if (ifint($uid)){
	$SQL .= " AND (a.uid=$uid OR a.uid2=$uid) ";
	$_ADM['admPageSize']=8;
}
/*-------------------------------------------------------*/
switch ($sort) {
	case 'nexttime0':$SORT = " ORDER BY a.nexttime,id DESC ";break;
	case 'nexttime1':$SORT = " ORDER BY a.nexttime DESC,id DESC ";break;
	case 'addtime0':$SORT = " ORDER BY a.addtime,id DESC ";break;
	case 'addtime1':$SORT = " ORDER BY a.addtime DESC,id DESC ";break;
	default:$SORT = " ORDER BY a.px DESC,a.id DESC ";break;
}

$rt = $db->query("SELECT a.*,b.sex,b.grade,b.nickname,b.weixin,b.mob,b.photo_s FROM ".__TBL_CRM_MATCH__." a,".__TBL_USER__." b WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">




<style>
.tablebz{width:90%;margin:20px auto 0 auto}
td.border0{vertical-align:top;padding-top:10px;line-height:12px}
img.photo_s{width:60px;height:60px;display:block;margin:12px auto;border-radius:40px;object-fit:cover;-webkit-object-fit:cover;cursor:zoom-in}
.RCW {display:inline-block}
.RCW li{width:80px}
.sobox{padding:15px 0 5px}
.sortbox{display:inline-block;width:180px}
.sobox select{;vertical-align:middle}
</style>
<body>
<?php if (!ifint($uid)){?>
<div class="navbox">
    <a class="ed">客户约见管理<?php echo '<b>'.$total.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank60"></div>
<?php }else{echo '<br>';}?>
<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合";
	if (!ifint($uid))echo"<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回上一页</a>";
	echo "</div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <table class="tablelist">
    <tr><td colspan="14" align="left" class="searchform">


    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>">
        <!--按门店查询-->
        <?php if(in_array('crm',$QXARR)){
            $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {
                ?>
                <span class="textmiddle"></span><select name="agentid" class="W150 size2 picmiddle" style="margin-right:10px"><!-- onChange="zeai.openurl('<?php echo SELF;?>?agentid='+this.value)"-->
                <option value="">全部门店</option>
                <?php
				for($j=0;$j<$total2;$j++) {
					$rows2 = $db->fetch_array($rt2,'num');
					if(!$rows2) break;
					$clss=($agentid==$rows2[0])?' selected':'';
					?>
					<option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[1],'out');?></option>
					<?php
				}
                ?></select><?php
            }
        }
        ?>
        
    	<span class="textmiddle"></span>
        <input name="sDATE1" id="sDATE1" type="text"  class="input size2 W100" value="<?php echo (empty($sDATE1))?'':$sDATE1; ?>" size="10" maxlength="10" autocomplete="off" placeholder="开始见面时间">
        ～
        <input name="sDATE2" id="sDATE2" type="text"  class="input size2 W100" value="<?php echo (empty($sDATE2))?'':$sDATE2; ?>" size="10" maxlength="10" autocomplete="off" placeholder="结束见面时间">
    	<span class="textmiddle">　</span><input name="Skey" type="text" maxlength="25" class="input size2 W180" placeholder="客户UID/昵称/姓名/手机" value="<?php echo $Skey; ?>">
		<span class="textmiddle">　</span><input name="Skey2" type="text" maxlength="25" class="input size2 W100" placeholder="红娘姓名/ID" value="<?php echo $Skey2; ?>">
		<span class="textmiddle">　</span><input name="Skey3" type="text" maxlength="25" class="input size2 W150" placeholder="服务方式/反馈情况内容" value="<?php echo $Skey3; ?>">
        
        <span class="textmiddle">　</span><input type="checkbox" name="ifmy" id="ifmy" class="checkskin" value="1"<?php echo ($ifmy == 1)?' checked':''; ?>><label for="ifmy" class="checkskin-label"><i></i><b class="W80 S14">我的客户</b></label>
        
        <button type="submit" class="btn size2 QING picmiddle"><i class="ico">&#xe6c4;</i> 开始搜索</button>
    </form>
    </td></tr>
	<?php
	$searchA = SELF."?ifmy=$ifmy&p=$p&agentid=$agentid&Skey=$Skey&Skey2=$Skey2&Skey3=$Skey3&sDATE1=$sDATE1&sDATE2=$sDATE2"; 
	$mt_flagA     = $searchA."&mt_time=$mt_time&mt_nexttime=$mt_nexttime&mt_jwang=$mt_jwang&sort=$sort";
    $mt_timeA     = $searchA."&mt_flag=$mt_flag&mt_nexttime=$mt_nexttime&mt_jwang=$mt_jwang&sort=$sort";
    $mt_nexttimeA = $searchA."&mt_flag=$mt_flag&mt_time=$mt_time&mt_jwang=$mt_jwang&sort=$sort";
	$mt_jwangA    = $searchA."&mt_flag=$mt_flag&mt_time=$mt_time&mt_nexttime=$mt_nexttime&sort=$sort";
	$sortA        = $searchA."&mt_flag=$mt_flag&mt_time=$mt_time&mt_nexttime=$mt_nexttime&mt_jwang=$mt_jwang";
	?>
    <tr><td colspan="14" align="left" class="searchli">
    
    
		<dl>
        <dt>工单状态：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($mt_flag))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_flagA;?>')">不限</a>
        <?php
			$meet_flagARR = json_decode($_CRM['meet_flag'],true);
			if (count($meet_flagARR) >= 1 && is_array($meet_flagARR)){
				foreach ($meet_flagARR as $V){
					$meet_flag_id    = $V['i'];
					$meet_flag_title = $V['v'];
					$meet_flagcls = ($meet_flag_id==$mt_flag)?' class="ed"':'';?>
                    <a href="javascript:;" <?php echo $meet_flagcls;?> onClick="zeai.openurl('<?php echo $mt_flagA."&mt_flag=$meet_flag_id";?>')"><?php echo $meet_flag_title;?></a>
					<?php
                }
			}
			?>
        </dd></dl>
    
		<dl>
        <dt>见面时间：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($mt_time))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_timeA;?>')">不限</a>
		<a href="javascript:;" <?php echo ($mt_time==1)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_timeA.'&mt_time=1';?>')" title="超过一周没有约见工单">一周未约见</a>
		<a href="javascript:;" <?php echo ($mt_time==2)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_timeA.'&mt_time=2';?>')" title="超过一月没有约见工单">一月未约见</a>
		<a href="javascript:;" <?php echo ($mt_time==3)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_timeA.'&mt_time=3';?>')" title="超过三个月没有约见工单">三月未约见</a>
		<a href="javascript:;" <?php echo ($mt_time==4)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_timeA.'&mt_time=4';?>')" title="今天新增约见工单">今天已约见</a>
		<a href="javascript:;" <?php echo ($mt_time==5)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_timeA.'&mt_time=5';?>')" title="昨天新增约见工单">昨天已约见</a>
        </dd></dl>
        
        
		<dl>
        <dt>下次联系：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($mt_nexttime))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_nexttimeA;?>')">不限</a>
		<a href="javascript:;" <?php echo ($mt_nexttime==1)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_nexttimeA.'&mt_nexttime=1';?>')" title="超过一周没有跟进记录">今天需约见</a>
		<a href="javascript:;" <?php echo ($mt_nexttime==2)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_nexttimeA.'&mt_nexttime=2';?>')" title="超过一月没有跟进记录">明天需约见</a>
		<a href="javascript:;" <?php echo ($mt_nexttime==3)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_nexttimeA.'&mt_nexttime=3';?>')" title="超过一月没有跟进记录">后天需约见</a>
		<a href="javascript:;" <?php echo ($mt_nexttime==4)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_nexttimeA.'&mt_nexttime=4';?>')" title="今天新增的约见记录">过期未约见</a>
        </dd></dl>
    
     
		<dl>
        <dt>交往状态：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($mt_jwang))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $mt_jwangA;?>')">不限</a>
        <?php
			$jwangARR = json_decode('[{"i":"2","v":"双方已交往"},{"i":"1","v":"双方未交往"}]',true);
			if (count($jwangARR) >= 1 && is_array($jwangARR)){
				foreach ($jwangARR as $V){
					$jwang_id    = $V['i'];
					$jwang_title = $V['v'];
					$jwangcls = ($jwang_id==$mt_jwang)?' class="ed"':'';?>
                    <a href="javascript:;" <?php echo $jwangcls;?> onClick="zeai.openurl('<?php echo $mt_jwangA.'&mt_jwang='.$jwang_id;?>')"><?php echo $jwang_title;?></a>
					<?php
                }
			}
			?>
        </dd></dl>
    

	</td></tr>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall" style="display:none"></th>
    <th width="60">&nbsp;</th>
    <th width="130" align="left">约见客户</th>
    <th width="140" align="center"><i class="ico textmiddle">&#xe634;</i> <span class="textmiddle">见面时间 / 意愿</span><div class="sort textmiddle">
	<a title="按见面时间升序" href="javascript:;" onClick="zeai.openurl('<?php echo $sortA.'&sort=addtime0';?>')" <?php echo($sort == 'addtime0')?' class="ed"':''; ?>></a>
	<a title="按见面时间降序" href="javascript:;" onClick="zeai.openurl('<?php echo $sortA.'&sort=addtime1';?>')" <?php echo($sort == 'addtime1')?' class="ed"':''; ?>></a>
</div></th>
    <th width="30" align="center">&nbsp;</th>
    <th width="60" align="left">&nbsp;</th>
    <th width="130" align="left">被约见客户</th>
    <th width="150">服务方式</th>
    <th>反馈情况</th>
    <th width="110" align="center"><i class="ico textmiddle">&#xe634;</i> <span class="textmiddle">下次联系</span><div class="sort textmiddle">
        <a title="按下次联系升序" href="javascript:;" onClick="zeai.openurl('<?php echo $sortA.'&sort=nexttime0';?>')" <?php echo($sort == 'nexttime0')?' class="ed"':''; ?>></a>
        <a title="按下次联系降序" href="javascript:;" onClick="zeai.openurl('<?php echo $sortA.'&sort=nexttime1';?>')" <?php echo($sort == 'nexttime1')?' class="ed"':''; ?>></a>
        </div>
	</th>
    <th width="100" align="center">红娘</th>
    <th width="100" align="center">交往状态</th>
    <th width="80" align="center">工单状态</th>
    <th width="150" align="center">操作</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$senduid = $rows['uid'];
		$username = dataIO($rows['username'],'out');
		$sendnickname = dataIO($rows['nickname'],'out');
		$sex     = $rows['sex'];
		$grade   = $rows['grade'];
		$bz      = $rows['bz'];
		$flag    = $rows['flag'];
		$uid     = $rows['uid2'];
		$addtime = $rows['addtime'];
		$nexttime= $rows['nexttime'];
		$photo_s = $rows['photo_s'];
		$fwfs = dataIO($rows['fwfs'],'out');
		$fkqk = dataIO($rows['fkqk'],'out');
		$meet_flag = intval($rows['meet_flag']);
		$meet_flag3 = intval($rows['meet_flag3']);
		$meet_ifagree = intval($rows['meet_ifagree']);
		$meet_ifagree2 = intval($rows['meet_ifagree2']);
		$admid   = $rows['admid'];
		$admname = dataIO($rows['admname'],'out');
		$bz = dataIO($rows['bz'],'out');
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}else{
			$photo_s_url = HOST.'/res/photo_s'.$sex.'.png';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}
		//
		$row2 = $db->ROW(__TBL_USER__,"sex,grade,nickname,photo_s,weixin,mob","id=".$uid,"name");
		if ($row2){
			$sex2      = $row2['sex'];
			$grade2    = $row2['grade'];
			$photo_s2  = $row2['photo_s'];
			$nickname2 = dataIO($row2['nickname'],'out');
			if(!empty($photo_s2)){
				$photo_s_url2 = $_ZEAI['up2'].'/'.$photo_s2;
				$photo_s_str2 = '<img src="'.$photo_s_url2.'" class="photo_s">';
			}else{
				$photo_s_url2 = HOST.'/res/photo_s'.$sex2.'.png';
				$photo_s_str2 = '<img src="'.$photo_s_url2.'" class="photo_s">';
			}
		}
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $senduid; ?>" id="id<?php echo $id; ?>" class="checkskin">
        </td>
      <td width="60"><a href="javascript:;" class="yunjian" uid="<?php echo $senduid;?>" title2="<?php echo urlencode(trimhtml($sendnickname.'／'.$senduid));?>"><?php echo $photo_s_str; ?></a></td>
      <td width="130" align="left" class="lineH150">
        
        <a href="javascript:;" class="yunjian" uid="<?php echo $senduid;?>" title2="<?php echo urlencode(trimhtml($sendnickname.'／'.$senduid));?>">
        <?php echo uicon($sex.$grade) ?><?php echo '<font class="S14 picmiddle">'.$senduid.'</font></br>';?></a>
        <font class="uleft">
        <?php
		  if(!empty($sendnickname))echo $sendnickname."</br>";
		  ?>
        </font>
        
      </td>
      <td width="140" height="60" align="center" class="lineH150">
		<?php
		if ($addtime > 0){echo '<div>'.YmdHis($addtime,'YmdHi').'</div>';
			$addday = intval(ADDTIME-$addtime);
			$addday = intval($addday/86400);
			if($addday>0){
				$addday_cls = ($addday>7)?' class="Cf00"':' class="C999"';
				echo '<div'.$addday_cls.'>已过'.$addday.'天未约见</div>';
			}
		}?>      
     <span class="picmiddle"><?php echo crm_arr_title($meet_ifagreeARR,$meet_ifagree);?></span>
     <i class="ico S24 Cccc picmiddle" style="margin:0 10px">&#xe62d;</i>
	 <span class="picmiddle"><?php echo crm_arr_title($meet_ifagreeARR,$meet_ifagree2);?></span>
      </td>
      <td width="30" height="60" align="center" class="lineH200 C666">&nbsp;</td>
      <td width="60" align="left"><a href="javascript:;" class="yunjian" uid="<?php echo $uid;?>" title2="<?php echo urlencode(trimhtml($nickname2.'　｜　'.$uid));?>"><?php echo $photo_s_str2; ?></a></td>
        <td width="130" align="left" class="lineH150">

        <a href="javascript:;" class="yunjian" uid="<?php echo $uid;?>" title2="<?php echo urlencode(trimhtml($nickname2.'　｜　'.$uid));?>">
        <?php echo uicon($sex2.$grade2) ?><?php echo '<font class="S14 picmiddle">'.$uid.'</font></br>';?></a>
        <font class="uleft">
        <?php if(!empty($nickname2))echo $nickname2;?>
        </font>

        </td>
        <td width="150"><?php echo $fwfs;?></td>
        <td><?php echo $fkqk;?></td>
        <td width="110" align="center" class="lineH150">
		<?php
		if ($nexttime > 0){
			echo '<div>'.YmdHis($nexttime,'YmdHi').'</div>';
			$nextday = intval($nexttime-ADDTIME);
			$nextday = intval($nextday/86400);
			if($nexttime<ADDTIME){//过期
				$nextday_str = ($nextday<-1)?abs($nextday).'天':'';
				echo '<div class="Cf00">过期'.$nextday_str.'未约见</div>'; 
			}else{
				if($nextday>=1){
					echo '<div class="C090">'.$nextday.'天后约见</div>'; 
				}
			}
		}?>
        </td>
		<td width="100" align="center"><?php if(ifint($admid))echo $admname.'<br><font class="C999">ID:'.$admid.'</font>';?></td>
        <td width="100" align="center">
		<?php 
        switch ($meet_flag3) {
            case 1:echo'双方未交往';break;
            case 2:echo'双方已交往';break;
            default:echo'--';break;
        }
        ?>  
        </td>
        <td width="80" align="center"><?php echo crm_arr_title($meet_flagARR,$meet_flag);?></td>
        <td width="150" align="center" >
		<?php 
		if($meet_flag == 3){
			$btncls1='BAI disabled';
		}else{
			$btncls1='';
		}
		?>
        <button type="button" class="btn size2  meetmod<?php echo $btncls1;?>" meetid="<?php echo $id;?>" photo_s_url="<?php echo $photo_s_url;?>" uid="<?php echo $senduid;?>" title2="<?php echo urlencode(trimhtml($sendnickname.'／'.$senduid));?>">修改</button>　
        <button type="button"  class="btn size2 BAI meetdel" meetid="<?php echo $id;?>">删除</button>
        </td>
      </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="14" align="center">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall">
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox" style="float:none">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
    </form>
</table>

<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
zeai.listEach('.yunjian',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid"));
		title2 = obj.getAttribute("title2"),
		urlpre = 'crm_user_detail.php?t=2&iframenav=1&uid='+uid,
		urlpre_yj = 'crm_select.php?k=yj&uid='+uid+'&t=';
		zeai.iframe('【'+decodeURIComponent(title2)+'】'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'\',this);" class="ed"><i class="ico add">&#xe7a0;</i> 个人主页</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre_yj+'3\',this);"><i class="ico add">&#xe64b;</i> 按择偶要求</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre_yj+'4\',this);"><i class="ico add">&#xe6c4;</i> 按搜索条件</a>'+
		'</div>',urlpre);
	}
});

zeai.listEach('.meetmod',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid"));
		id = parseInt(obj.getAttribute("meetid")),
		title2 = obj.getAttribute("title2"),
		
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">',
		
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】约见管理','crm_gj_yj.php?submitok=yj_mod&id='+id+'&uid='+uid,700,520);
	}
});
zeai.listEach('.meetdel',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("meetid"));
		zeai.confirm('确定删除么？',function(){
			zeai.ajax({url:'crm_gj_yj.php?submitok=yj_del_update&id='+id},function(e){
				rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});



</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<?php }?>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem: '#sDATE1'});
laydate.render({elem: '#sDATE2'});
</script>
<br><br><br>
<?php require_once 'bottomadm.php';?>
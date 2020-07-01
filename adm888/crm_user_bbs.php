<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/udata.php';

if(!in_array('crm_bbs_list',$QXARR))exit(noauth());
$bbs_intentionARR = json_decode($_CRM['bbs_intention'],true);

$SQL = " a.uid=b.id AND b.crm_flag<3 ";
if($k=='sq'){
	$SQL .=" AND b.crm_ugrade=0 ";
	$navtitle = '售前';
}elseif($k=='sh'){
	$SQL .=" AND b.crm_ugrade>0 ";
	$navtitle = '售后';
}
$Skey = trimm($Skey);
$Skey2 = trimm($Skey2);
$Skey3 = trimm($Skey3);

$Skey = trimhtml($Skey);
if (!empty($Skey)){
	if(ifmob($Skey)){
		$SQL .= " AND b.mob=".$Skey;	
	}elseif(ifint($Skey)){
		$SQL .= " AND b.id=".$Skey;	
	}else{
		$SQL .= " AND (( b.truename LIKE '%".$Skey."%' ) OR ( b.crm_ubz LIKE '%".$Skey."%' ) OR ( b.uname LIKE '%".$Skey."%' ) OR ( b.nickname LIKE '%".$Skey."%' ) OR ( b.nickname LIKE '%".urlencode($Skey)."%' )) ";
	}
}

if(ifint($Skey2)){
	$SQL .= " AND (a.admid=$Skey2) ";
}elseif(!empty($Skey2)){
	$SQL .= " AND (  a.admname LIKE '%".$Skey2."%' )";
}
if(!empty($Skey3)){
	$SQL .= " AND (  a.content LIKE '%".$Skey3."%' )";
}
if (ifint($intention))$SQL .= " AND a.intention = $intention ";


//时间
if(!empty($sDATE1)){
	$sDATE1_ = strtotime($sDATE1.' 00:00:01');
	$SQL .= " AND ( a.addtime >= '$sDATE1_' )";
}
if(!empty($sDATE2)){
	$sDATE2_ = strtotime($sDATE2.' 23:59:59');
	$SQL .= " AND ( a.addtime <= '$sDATE2_' )";
}

//按客户等级
if(ifint($crm_ugrade))$SQL .= " AND b.crm_ugrade=$crm_ugrade";

//按分类
if(ifint($crm_ukind))$SQL  .= " AND b.crm_ukind=$crm_ukind";

//跟进时间
switch ($addtime) {
	case 1:$SQL .= " AND (".ADDTIME." - a.addtime) > 604800 ";break;//一周未跟进
	case 2:$SQL .= " AND (".ADDTIME." - a.addtime) > 2592000 ";break;//一月未跟进
	case 3:$SQL .= " AND (".ADDTIME." - a.addtime) > 7948800 ";break;//三月未跟进
	case 4:$SQL .= " AND ( TO_DAYS(from_unixtime(a.addtime))-TO_DAYS(NOW()) = 0 )";break;//今天已跟进
	case 5:$SQL .= " AND ( TO_DAYS(from_unixtime(a.addtime))-TO_DAYS(NOW()) = -1 )";break;//昨天已跟进
}
//下次联系
switch ($nexttime) {
	case 1:$SQL .= " AND ( TO_DAYS(from_unixtime(a.nexttime))-TO_DAYS(NOW()) = 0 )";break;//今天需跟进
	case 2:$SQL .= " AND ( TO_DAYS(from_unixtime(a.nexttime))-TO_DAYS(NOW()) = 1 )";break;//明天需跟进
	case 3:$SQL .= " AND ( TO_DAYS(from_unixtime(a.nexttime))-TO_DAYS(NOW()) = 2 )";break;//后天需跟进
	case 4:$SQL .= " AND ( a.nexttime<".ADDTIME."  )";break;//过期
}





/*-------------------------------------------------------*/
//门店+地区
$SQL .= getAgentSQL('b');
//超管可以按门店搜索
if(ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND b.agentid=$agentid";

if($ifmy==1)$SQL .= " AND b.hnid=$session_uid";

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
	default:$SORT = " ORDER BY a.addtime DESC,a.id DESC ";break;
}
//
$SQLnew = ",(SELECT MAX(id) AS max_id,COUNT(id) AS gjnum FROM ".__TBL_CRM_BBS__." GROUP BY uid) N";$SQL .= " AND a.id=N.max_id ";
$rt = $db->query("SELECT a.*,b.sex,b.grade,b.nickname,b.photo_s,b.crm_ukind,b.crm_ugrade,b.crm_usjtime1,b.crm_usjtime2,N.gjnum FROM ".__TBL_CRM_BBS__." a,".__TBL_USER__." b".$SQLnew." WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select3.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist td{font-family:Arial}
.RCW {display:inline-block}
.RCW li{width:80px}
.sobox{padding:15px 0 5px}
.sortbox{display:inline-block;width:180px}
.sobox select{vertical-align:middle}
.pathlist img{margin:8px 5px 5px 2px;width:30px;height:30px;object-fit:cover;-webkit-object-fit:cover;border-radius:2px}
.crm_ugrade a{border:0}

button.bbsadd{position:relative}
button.bbsadd span{position:absolute;top:-12px;right:-8px;min-width:18px;padding:0 4px;line-height:16px;background-color:#fff;border:#FF5722 1px solid;color:#FF5722;font-size:12px;border-radius:10px 10px 10px 10px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
</style>
<body>
<?php if (!ifint($uid)){?>
<div class="navbox">
    <a class="ed"><?php echo $navtitle;?>跟进管理<?php echo '<b>'.$total.'</b>';?></a>
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank60"></div>
<?php }else{echo '<br>';}?>
<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回上一页</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';?>
    <table class="tablelist">
    <tr><td colspan="13" class="searchli">

    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>">
    	<span class="textmiddle">跟进时间　</span><input name="sDATE1" id="sDATE1" type="text"  class="input size2 W100" value="<?php echo (empty($sDATE1))?'':$sDATE1; ?>" size="10" maxlength="10" autocomplete="off">
        <b>～</b> 
        <input name="sDATE2" id="sDATE2" type="text"  class="input size2 W100" value="<?php echo (empty($sDATE2))?'':$sDATE2; ?>" size="10" maxlength="10" autocomplete="off">　
        <!--按门店查询-->
        <?php if(in_array('crm',$QXARR)){
            $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
            $total2 = $db->num_rows($rt2);
            if ($total2 > 0) {?>
                <span class="textmiddle"></span><select name="agentid" class="size2 picmiddle">
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
                ?>
                </select>
                </div>
                <?php
            }
        }
        ?>
    	<span class="textmiddle">　</span><input name="Skey" type="text" maxlength="25" class="input size2 W250" placeholder="输入客户UID/姓名/昵称/手机/备注" value="<?php echo $Skey; ?>">
		<span class="textmiddle">　</span><input name="Skey2" type="text" maxlength="25" class="input size2 W150" placeholder="输入红娘姓名/ID" value="<?php echo $Skey2; ?>">
		<span class="textmiddle">　</span><input name="Skey3" type="text" maxlength="25" class="input size2 W150" placeholder="输入跟进内容" value="<?php echo $Skey3; ?>">　
        <input type="checkbox" name="ifmy" id="ifmy" class="checkskin" value="1"<?php echo ($ifmy == 1)?' checked':''; ?>><label for="ifmy" class="checkskin-label"><i></i><b class="W80 S14">我的客户</b></label>
        <input type="hidden" name="k" value="<?php echo $k;?>" />
        <button type="submit" class="btn size2 QING picmiddle"><i class="ico">&#xe6c4;</i> 开始搜索</button>　　
		<?php
        $searchA = SELF."?ifmy=$ifmy&k=$k&p=$p&agentid=$agentid&Skey=$Skey&Skey2=$Skey2&Skey3=$Skey3&sDATE1=$sDATE1&sDATE2=$sDATE2"; 
		
		$crm_ukindA = $searchA."&intention=$intention&crm_ugrade=$crm_ugrade&addtime=$addtime&nexttime=$nexttime&sort=$sort";
		$intentionA = $searchA."&crm_ukind=$crm_ukind&crm_ugrade=$crm_ugrade&addtime=$addtime&nexttime=$nexttime&sort=$sort";
		$crm_ugradeA= $searchA."&intention=$intention&crm_ukind=$crm_ukind&addtime=$addtime&nexttime=$nexttime&sort=$sort";
		$addtimeA   = $searchA."&intention=$intention&crm_ukind=$crm_ukind&crm_ugrade=$crm_ugrade&nexttime=$nexttime&sort=$sort";
		$nexttimeA  = $searchA."&intention=$intention&crm_ukind=$crm_ukind&crm_ugrade=$crm_ugrade&addtime=$addtime&sort=$sort";
		$sortA      = $searchA."&intention=$intention&crm_ukind=$crm_ukind&crm_ugrade=$crm_ugrade&addtime=$addtime&nexttime=$nexttime";
        ?>
    </form>
    </td></tr>
    <tr><td colspan="13" class="searchli">
		<dl><dt>客户分类：</dt>
		<dd>
            <a href="javascript:;" <?php echo (empty($crm_ukind))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $crm_ukindA;?>')">不限</a>
        	<?php
			$crm_ukindARR = json_decode($_UDATA['crm_ukind'],true);
			if (count($crm_ukindARR) >= 1 && is_array($crm_ukindARR)){
				foreach ($crm_ukindARR as $V){
					$ukind_id    = $V['i'];
					$ukind_title = $V['v'];
					$ukindcls = ($ukind_id==$crm_ukind)?' class="ed"':'';?>
                    <a href="javascript:;" <?php echo $ukindcls;?> onClick="zeai.openurl('<?php echo $crm_ukindA;?>&crm_ukind=<?php echo $ukind_id;?>')"><?php echo $ukind_title;?></a>
					<?php
                }
			}
			?>
        </dd></dl>

    	<?php if($k=='sq'){?>
		<dl>
        <dt>售前意向：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($intention))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $intentionA;?>')">不限</a>
        <?php
			$intentionARR = json_decode($_CRM['bbs_intention'],true);
			if (count($intentionARR) >= 1 && is_array($intentionARR)){
				foreach ($intentionARR as $V){
					$intention_id    = $V['i'];
					$intention_title = $V['v'];
					$intentioncls = ($intention_id==$intention)?' class="ed"':'';?>
                    <a href="javascript:;" <?php echo $intentioncls;?> onClick="zeai.openurl('<?php echo $intentionA;?>&intention=<?php echo $intention_id;?>')"><?php echo $intention_title;?></a>
					<?php
                }
			}
			?>
        </dd></dl>
    	<?php }?>
        
        
        <?php if($k=='sh'){?>
          <dl>
            <dt>客户等级：</dt>
            <dd>
            <a href="javascript:;" <?php echo (empty($crm_ugrade))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $crm_ugradeA;?>')">不限</a>
            <?php
                $crm_ugradeARR = json_decode($_UDATA['crm_ugrade'],true);
                if (count($crm_ugradeARR) >= 1 && is_array($crm_ugradeARR)){
                    foreach ($crm_ugradeARR as $V){
                        $ugrade_id    = $V['i'];
                        $ugrade_title = $V['v'];
                        $ugradecls = ($ugrade_id==$crm_ugrade)?' class="ed"':'';
                        ?>
                        <a href="javascript:;" <?php echo $ugradecls;?> onClick="zeai.openurl('<?php echo $crm_ugradeA;?>&crm_ugrade=<?php echo $ugrade_id;?>')"><?php echo $ugrade_title;?></a>
                        <?php
                    }
                }
            ?>
            </dd>
          </dl>
        
        <?php }?>
    
		<dl>
        <dt>跟进时间：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($addtime))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $addtimeA;?>')">不限</a>
		<a href="javascript:;" <?php echo ($addtime==1)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $addtimeA.'&addtime=1';?>')" title="超过一周没有跟进">一周未跟进</a>
		<a href="javascript:;" <?php echo ($addtime==2)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $addtimeA.'&addtime=2';?>')" title="超过一月没有跟进">一月未跟进</a>
		<a href="javascript:;" <?php echo ($addtime==3)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $addtimeA.'&addtime=3';?>')" title="超过三个月没有跟进">三月未跟进</a>
		<a href="javascript:;" <?php echo ($addtime==4)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $addtimeA.'&addtime=4';?>')" title="今天新增跟进">今天已跟进</a>
		<a href="javascript:;" <?php echo ($addtime==5)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $addtimeA.'&addtime=5';?>')" title="昨天新增跟进">昨天已跟进</a>
        </dd></dl>
        
		<dl>
        <dt>下次联系：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($nexttime))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $nexttimeA;?>')">不限</a>
		<a href="javascript:;" <?php echo ($nexttime==1)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $nexttimeA.'&nexttime=1';?>')" title="今天需跟进">今天需跟进</a>
		<a href="javascript:;" <?php echo ($nexttime==2)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $nexttimeA.'&nexttime=2';?>')" title="明天需跟进">明天需跟进</a>
		<a href="javascript:;" <?php echo ($nexttime==3)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $nexttimeA.'&nexttime=3';?>')" title="后天需跟进">后天需跟进</a>
		<a href="javascript:;" <?php echo ($nexttime==4)?' class="ed"':''?> onClick="zeai.openurl('<?php echo $nexttimeA.'&nexttime=4';?>')" title="过期未跟进">过期未跟进</a>
        </dd></dl>
    
    </td></tr>
    <?php $sorthref = $searchA."&sort=";?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall" style="display:none"></th>
    <th width="60">&nbsp;</th>
    <th width="130" align="left">跟进客户</th>
    <th width="120" align="center"<?php echo ($k=='sq')?" style='display:none;'":"";?>>客户等级</th>
    <th width="80" align="center">客户分类</th>
    <th width="120" align="center">跟进时间<div class="sort">
        <a title="按跟进时间升序" href="<?php echo $sortA."&sort=addtime0";?>" <?php echo($sort == 'addtime0')?' class="ed"':''; ?>></a>
        <a title="按跟进时间降序" href="<?php echo $sortA."&sort=addtime1";?>" <?php echo($sort == 'addtime1')?' class="ed"':''; ?>></a>
</div></th>
    <th width="80" align="center"<?php echo ($k=='sh')?" style='display:none;'":"";?>>售前意向</th>
    <th width="10">&nbsp;</th>
    <th align="left">跟进内容（最新）</th>
    <th width="10">&nbsp;</th>
    <th width="120" align="center">下次联系<div class="sort">
	<a title="按下次联系时间升序" href="<?php echo $sortA."&sort=nexttime0";?>" <?php echo($sort == 'nexttime0')?' class="ed"':''; ?>></a>
	<a title="按下次联系时间降序" href="<?php echo $sortA."&sort=nexttime1";?>" <?php echo($sort == 'nexttime1')?' class="ed"':''; ?>></a>
</div></th>
    <th width="80" align="center">跟进红娘</th>
    <th width="190" align="center">操作</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $rows['uid'];
		$nickname = dataIO($rows['nickname'],'out');
		$sex     = $rows['sex'];
		$grade   = $rows['grade'];
		$addtime = $rows['addtime'];
		$addtime_ = YmdHis($addtime,'YmdHi');
		$photo_s = $rows['photo_s'];
		$content = dataIO($rows['content'],'out');
		$intention = intval($rows['intention']);
		$admid   = $rows['admid'];
		$admname = dataIO($rows['admname'],'out');
		$nexttime = $rows['nexttime'];
		$nexttime_str  = ($nexttime>0)?YmdHis($nexttime,'YmdHi'):'';
        $pathlist      = $rows['pathlist'];
		
		$crm_ukind = intval($rows['crm_ukind']);
		$crm_ugrade = intval($rows['crm_ugrade']);
		$crm_usjtime1 = intval($rows['crm_usjtime1']);
		$crm_usjtime2 = intval($rows['crm_usjtime2']);
		
		$gjnum = intval($rows['gjnum']);
		
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}else{
			$photo_s_url = HOST.'/res/photo_s'.$sex.'.png';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}
		$title2 = (!empty($nickname))?urlencode(trimhtml($nickname)).'／'.$uid:$uid;
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $senduid; ?>" id="id<?php echo $id; ?>" class="checkskin">
        </td>
      <td width="60"><a href="javascript:;" class="photo_ss" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>"><?php echo $photo_s_str; ?></a></td>
      <td width="130" align="left" class="lineH150">
        
        <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>">
        <?php echo uicon($sex.$grade) ?><?php echo '<font class="S14 picmiddle">'.$uid.'</font></br>';?></a>
        <font class="uleft"><?php if(!empty($nickname))echo $nickname;?></font>
        
      </td>
      <td width="120" class="lineH150 padding15" align="center"<?php echo ($k=='sq')?" style='display:none;'":"";?>>
		<?php
		if($k=='sh'){
			echo crm_ugrade_time($uid,$crm_ugrade,'btn_djs_noA',$crm_usjtime1,$crm_usjtime2);
			$rtHT=$db->query("SELECT id,htcode FROM ".__TBL_CRM_HT__." WHERE uid=".$uid." ORDER BY id DESC");
			$totalHT = $db->num_rows($rtHT);
			if ($totalHT > 0) {
				for($iHT=1;$iHT<=$totalHT;$iHT++) {
					$rowsHT = $db->fetch_array($rtHT,'name');
					if(!$rowsHT) break;
					$htid =$rowsHT['id'];
					$htcode=dataIO($rowsHT['htcode'],'out');
					echo '<div style="margin-bottom:5px"><span class="textmiddle C666">合同：'.$htcode.'</span>';?>
					<a title="查看合同详情" class="btn size1 BAI picmiddle" onClick="zeai.iframe('<?php echo '<img src='.$photo_s_url.' class=photo_s_iframe>';?>【<?php echo $uid;?>】合同详情','crm_gj_yj.php?submitok=ht_detail&fid=<?php echo $htid;?>',700,520)">详情</a>
					<?php echo '</div>';
				}
			}
		}
		?>      
      </td>
      <td width="80" align="center" class="lineH150 C666"><?php echo udata('crm_ukind',$crm_ukind); ?></td>
      <td width="120" height="60" align="center" class="C666">
        <i class="ico">&#xe634;</i> <?php echo $addtime_;?>
        
        <?php
    if ($addtime > 0){
        $bbsday = intval(ADDTIME-$addtime);
        $bbsday = intval($bbsday/86400);
        if($bbsday>0){
            $bbsday_cls = ($bbsday>7)?' class="Cf00"':'';
            echo '<div'.$bbsday_cls.'>已过'.$bbsday.'天未联系</div>';
        }
    }?>
        
      </td>
      <td width="80" class="C666" align="center"<?php echo ($k=='sh')?" style='display:none;'":"";?>><?php echo crm_arr_title($bbs_intentionARR,$intention);?></td>
      <td width="10">&nbsp;</td>
      <td height="60" align="left" class="padding10 S14">
	  <?php echo $content;
            if(!empty($pathlist)){
                $ARR=explode(',',$pathlist);
                $pathlist=array();
				echo '<div class="pathlist">';
                foreach ($ARR as $V) {?>
					<a href="javascript:;" title="放大" class="zoom" onClick="parent.parent.piczoom('<?php echo smb($_ZEAI['up2'].'/'.$V,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$V;?>" alt="放大"></a>
                    <?php
                }
				echo '</div>';
            }
	  ?>
      </td>
      <td width="10">&nbsp;</td>
      <td width="120" height="60" align="center" class="C666">
		<i class="ico">&#xe634;</i> <?php echo $nexttime_str;?>
		<?php
		if ($nexttime > 0){
			$nextday = intval($nexttime-ADDTIME);
			$nextday = intval($nextday/86400);
			if($nexttime<ADDTIME){//过期
				$nextday_str = ($nextday<-1)?abs($nextday).'天':'';
				echo '<div class="Cf00">过期'.$nextday_str.'未跟进</div>'; 
			}else{
				if($nextday>=1){
					echo '<div class="C090">'.$nextday.'天后联系</div>'; 
				}
			}
		}?>
      </td>
      <td width="80" align="center" class="lineH150"><?php if(ifint($admid))echo $admname.'<br><font class="C999">ID:'.$admid.'</font>';?></td>
        <td width="190" align="center" >
        <?php if(in_array('crm_bbs_add',$QXARR)){?>
        <button type="button" class="btn size2 QING2 bbsadd" bbsid="<?php echo $id;?>" photo_s_url="<?php echo $photo_s_url;?>" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" style="margin:5px"><i class="ico">&#xe620;</i> <span><?php echo $gjnum;?></span>跟进</button>
        <?php }?>
        <?php if(in_array('crm_bbs_mod',$QXARR)){?>
        <button type="button" class="btn size2 BAI bbsmod" bbsid="<?php echo $id;?>" photo_s_url="<?php echo $photo_s_url;?>" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" style="margin:5px">修改</button>
		<?php }?>
        
        <?php if(in_array('crm_bbs_del',$QXARR)){?>
        <button type="button"  class="btn size2 BAI bbsdel" bbsid="<?php echo $id;?>" style="margin:5px">删除</button>
        <?php }?>
        </td>
      </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="13" align="center">
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
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
zeai.listEach('.photo_ss',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		urlpre = 'crm_user_detail.php?t=2&iframenav=1&uid='+uid;
		zeai.iframe('【'+decodeURIComponent(title2)+'】'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'\',this);" class="ed"><i class="ico add">&#xe7a0;</i> 个人主页</a>'+
		'</div>',urlpre);
	}
});

zeai.listEach('.bbsadd',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		urlpre = 'crm_gj_yj'+zeai.ajxext+'uid='+uid+'&submitok=';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】跟进管理'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'gj_list\',this);" class="ed"><i class="ico add">&#xe64b;</i> 跟进列表</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'gj_add\',this);"><i class="ico add">&#xe620;</i> 新增跟进</a>'+
		'</div>',urlpre+'gj_list',700,520);
	}
});
zeai.listEach('.bbsmod',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		id = parseInt(obj.getAttribute("bbsid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		urlpre = 'crm_gj_yj'+zeai.ajxext+'id='+id+'&uid='+uid+'&submitok=';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】跟进管理'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'gj_list\',this);"><i class="ico add">&#xe64b;</i> 跟进列表</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'gj_add\',this);"><i class="ico add">&#xe620;</i> 新增跟进</a>'+
		'</div>',urlpre+'gj_mod',700,520);
	}
});

zeai.listEach('.bbsdel',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("bbsid"));
		zeai.confirm('确定删除么？',function(){
			zeai.ajax({url:'crm_gj_yj'+zeai.ajxext+'submitok=gj_del_update&id='+id},function(e){
				rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
</script>
<?php }?>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem: '#sDATE1'});
laydate.render({elem: '#sDATE2'});
</script>
<br><br><br>
<?php require_once 'bottomadm.php';?>
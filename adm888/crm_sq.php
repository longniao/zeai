<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_crm.php';
require_once ZEAI.'cache/udata.php';
$t = (ifint($t,'1-2','1'))?$t:1;

$SQL = " (flag=1 OR flag=-2) AND kind<>4 AND admid>0 AND crm_ugrade=0 ";

//非超管门店+地区 
$SQL .= getAgentSQL();

//超管搜索按门店
if (ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND agentid=$agentid";

//按会员搜索
$Skey = trimhtml($Skey);
if (!empty($Skey)){
	if(ifmob($Skey)){
		$SQL .= " AND mob=".$Skey;	
	}elseif(ifint($Skey)){
		$SQL .= " AND id=".$Skey;	
	}else{
		$SQL .= " AND (( truename LIKE '%".$Skey."%' ) OR ( crm_ubz LIKE '%".$Skey."%' ) OR ( uname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".urlencode($Skey)."%' )) ";
	}
}

//按红娘
$Skey2 = trimhtml($Skey2);
if (!empty($Skey2)){
	if(ifint($Skey2)){
		$SQL .= " AND (admid=".$Skey2." OR hnid=".$Skey2.") ";// OR hnid2=".$Skey2."
	}else{
		$SQL .= " AND (    admname LIKE '%".$Skey2."%' OR hnname LIKE '%".$Skey2."%' OR hnname2 LIKE '%".$Skey2."%'  ) ";
	}
}

//按客户分类
if(ifint($crm_ukind))$SQL   .= " AND crm_ukind=$crm_ukind";
if (ifint($ifcontact))$SQL .= " AND (mob<>'' OR weixin<>'') ";
if (ifint($myinfobfb))$SQL .= " AND myinfobfb>$myinfobfb ";
if ($photo_s == 1)$SQL     .= " AND photo_s<>'' ";


//超管搜索按门店
if (ifint($agentid) && in_array('crm',$QXARR)){
	$SQL_AGENT .= " AND agentid=$agentid ";	
}else{
	$SQL_AGENT .=" AND agentid=$session_agentid ";
}

if($ifhnid == 'ifhnid0'){
	$SQL .= " AND hnid=0 ";
}elseif($ifhnid == 'ifhnid1'){
	$SQL .= " AND hnid>0 ";
}
if($ifmy==1)$SQL .= " AND hnid=$session_uid";

switch ($sort) {
	case 'regtime0':$sortSQL = " ORDER BY regtime ";break;
	case 'regtime1':$sortSQL = " ORDER BY regtime DESC ";break;
	case 'endtime0':$sortSQL = " ORDER BY endtime ";break;
	case 'endtime1':$sortSQL = " ORDER BY endtime DESC ";break;
	case 'admtime0':$sortSQL = " ORDER BY admtime,id DESC";break;
	case 'admtime1':$sortSQL = " ORDER BY admtime DESC,id DESC";break;
	case 'bbsnum0':$sortSQL = " ORDER BY bbsnum,id DESC";break;
	case 'bbsnum1':$sortSQL = " ORDER BY bbsnum DESC,id DESC ";break;
	case 'hntime0':$sortSQL = " ORDER BY hntime,id DESC";break;
	case 'hntime1':$sortSQL = " ORDER BY hntime DESC,id DESC";break;
	case 'bbs_nexttime0':$sortSQL = " ORDER BY bbs_nexttime,id DESC ";break;
	case 'bbs_nexttime1':$sortSQL = " ORDER BY bbs_nexttime DESC,id DESC ";break;
	case 'bbs_endtime0':$sortSQL = " ORDER BY bbs_endtime,id DESC";break;
	case 'bbs_endtime1':$sortSQL = " ORDER BY bbs_endtime DESC,id DESC";break;
	default:$sortSQL = " ORDER BY id DESC ";break;
}


$BBS_numSQL       = ",(SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." WHERE uid=U.id".$SQLBBSss.") AS bbsnum ";
$BBS_nexttimeSQL  = ",(SELECT nexttime FROM ".__TBL_CRM_BBS__." WHERE uid=U.id".$SQLBBSss." ORDER BY nexttime DESC LIMIT 1) AS bbs_nexttime";
$BBS_endtimeSQL   = ",(SELECT addtime FROM ".__TBL_CRM_BBS__."  WHERE uid=U.id".$SQLBBSss." ORDER BY addtime DESC LIMIT 1 ) AS bbs_endtime";


//客户跟进：
if(!empty($bbskind)){
	$today = YmdHis(ADDTIME,'Ymd');
	switch ($bbskind) {
		case 'gj_month_no':$SQLBBS= " AND (".ADDTIME." - addtime) > 604800 ";break;//一周未跟进
		case 'gj_yes':$SQLBBS= "  ";break;//有跟进
		case 'gj_today_no':$SQLBBS= " AND ( TO_DAYS(from_unixtime(nexttime))-TO_DAYS(NOW()) = 0 ) AND nexttime>0 ";break;//今天需跟进
		case 'gj_tomorrow_no':$SQLBBS= " AND (TO_DAYS(from_unixtime(nexttime))-TO_DAYS(NOW())) = 1 ";break;//明天需跟进、、date_format(now(),'%Y-%m-%d')
		case 'gj_today_yes':$SQLBBS= " AND ( date_format(from_unixtime(addtime),'%Y-%m-%d') = '$today' ) ";break;//今天已跟进
	}
	$uidlist = array();
	$rtU=$db->query("SELECT uid FROM ".__TBL_CRM_BBS__." A,(SELECT MAX(id) AS max_id FROM ".__TBL_CRM_BBS__." GROUP BY uid) B WHERE A.id=B.max_id ".$SQLBBS." ORDER BY A.id DESC");// GROUP BY uid
	$totalU = $db->num_rows($rtU);
	if ($totalU > 0) {
		for($iU=1;$iU<=$totalU;$iU++) {
			$rowsU = $db->fetch_array($rtU,'name');
			if(!$rowsU)break;
			$uidlist[]=$rowsU['uid'];
		}
		$uidlist = (is_array($uidlist))?implode(',',$uidlist):'';
		$SQL_BBS_inU=" AND id in ($uidlist)";
	}else{
		$SQL_BBS_inU=" AND 1=2 ";
	}
}else{
	$SQL_BBS_inU = "";
}
$fld = "id,crm_ukind,crm_ukind,agentid,agenttitle,admid,admname,admtime,hnid,hnname,hnid2,hnname2,uname,truename,nickname,photo_s,sex,grade,birthday,edu,areatitle,area2title,love,regtime,endtime,kind,flag,tguid,crm_ubz";
$rt = $db->query("SELECT ".$fld.$BBS_numSQL.$BBS_nexttimeSQL.$BBS_endtimeSQL." FROM ".__TBL_USER__." U WHERE ".$SQL.$SQL_BBS_inU.$sortSQL." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);

$totalnum = $db->COUNT(__TBL_USER__,$SQL);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.gradeflag{display:block;color:#999;padding-top:5px;padding-bottom:10px;font-family:'Arial'}
.sortbox{display:inline-block;width:100px}
</style>
<body>
<div class="navbox">
  <?php if ($t == 1){?><a class="ed">售前客户列表<?php echo '<b>'.$totalnum.'</b>';?></a><?php }?>
	<?php if ($t == 2){?><a class="ed">售前客户调配(换红娘)<?php echo '<b>'.$totalnum.'</b>';?></a><?php }?>
<div class="Rsobox"></div><div class="clear"></div></div><div class="fixedblank60">
</div>

<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
	$searchA = "crm_sq.php?ifmy=$ifmy&myinfobfb=$myinfobfb&ifcontact=$ifcontact&myinfobfb=$myinfobfb&agentid=$agentid&t=$t&Skey=$Skey&Skey2=$Skey2";
?>
<table class="tablelist">
    <tr><td colspan="13" align="left" class="searchform">

    <form name="form1" method="get" action="<?php echo SELF; ?>">
      <!--超管按门店查询-->
      <?php if(in_array('crm',$QXARR)){?>
      <?php
        $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
        $total2 = $db->num_rows($rt2);
        if ($total2 > 0) {?>
          <div class="FL"><!-- onChange="zeai.openurl('<?php echo SELF;?>?agentid='+this.value)"-->
            按门店
            <select name="agentid" class="W150 size2" style="margin-right:10px">
              <option value="">不限门店</option>
              <?php
                for($j=0;$j<$total2;$j++) {
                    $rows2 = $db->fetch_array($rt2,'num');
                    if(!$rows2) break;
                    $clss=($agentid==$rows2[0])?' selected':'';
                    ?><option value="<?php echo $rows2[0];?>"<?php echo $clss;?>><?php echo dataIO($rows2[1],'out');?></option>
              <?php
                }
                ?>
              </select>
            </div>
          <?php
        }}?>
      <!---->
　按客户
    <input name="Skey" type="text" id="Skey" maxlength="25" class="W180 input size2" placeholder="UID/姓名/昵称/手机/备注" value="<?php echo $Skey; ?>">　
	按红娘
    <input name="Skey2" type="text" id="Skey2" size="30" maxlength="25" class="W150 input size2" placeholder="红娘ID/姓名" value="<?php echo $Skey2; ?>">　
    <span class="picmiddle">资料完整度</span> <script>zeai_cn__CreateFormItem('select','myinfobfb','<?php echo $myinfobfb; ?>','class="size2 picmiddle"',[{i:"10",v:"资料完整度>10%"},{i:"60",v:"资料完整度>60%"}]);</script>　
    <input type="checkbox" name="ifcontact" id="ifcontact" class="checkskin" value="1"<?php echo ($ifcontact == 1)?' checked':''; ?> ><label for="ifcontact" class="checkskin-label"><i></i><b class="W80 S14">有联系方法</b></label>
    <input type="checkbox" name="photo_s" id="photo_s" class="checkskin" value="1"<?php echo ($photo_s == 1)?' checked':''; ?>><label for="photo_s" class="checkskin-label"><i></i><b class="W50 S14">有照片</b></label>
    <input type="checkbox" name="ifmy" id="ifmy" class="checkskin" value="1"<?php echo ($ifmy == 1)?' checked':''; ?>><label for="ifmy" class="checkskin-label"><i></i><b class="W80 S14">我的客户</b></label>
    <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    <input type="hidden" name="t" value="<?php echo $t;?>" />
    <input type="hidden" name="k" value="<?php echo $k;?>" />
    <input type="hidden" name="crm_ukind" value="<?php echo $crm_ukind;?>" />
    <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
    </form>

    </td></tr>
    <tr><td colspan="13" align="left" class="searchli" style="padding-bottom:15px">
		<dl>
        <dt>客户分类：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($crm_ukind))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&crm_ukind=&bbskind=<?php echo $bbskind;?>&ifhnid=<?php echo $ifhnid;?>')">不限</a>
        <?php
			$crm_ukindARR = json_decode($_UDATA['crm_ukind'],true);
			if (count($crm_ukindARR) >= 1 && is_array($crm_ukindARR)){
				foreach ($crm_ukindARR as $V){
					$ukind_id    = $V['i'];
					$ukind_title = $V['v'];
					$ukindcls = ($ukind_id==$crm_ukind)?' class="ed"':'';?>
                    <a href="javascript:;" <?php echo $ukindcls;?> onClick="zeai.openurl('<?php echo $searchA;?>&crm_ukind=<?php echo $ukind_id;?>&bbskind=<?php echo $bbskind;?>&ifhnid=<?php echo $ifhnid;?>')"><?php echo $ukind_title;?></a>
					<?php
                }
			}
			?>
        </dd></dl>
        <div class="clear"></div>
		<dl>
        <dt>客户跟进：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($bbskind))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&bbskind=&crm_ukind=<?php echo $crm_ukind;?>&ifhnid=<?php echo $ifhnid;?>')">不限</a>
		<a href="javascript:;" <?php echo ($bbskind=='gj_yes')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&bbskind=gj_yes&crm_ukind=<?php echo $crm_ukind;?>&ifhnid=<?php echo $ifhnid;?>')" title="有跟进记录">有跟进</a>
		<a href="javascript:;" <?php echo ($bbskind=='gj_month_no')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&bbskind=gj_month_no&crm_ukind=<?php echo $crm_ukind;?>&ifhnid=<?php echo $ifhnid;?>')" title="超过一周没有跟进记录">一周未跟进</a>
		<a href="javascript:;" <?php echo ($bbskind=='gj_today_no')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&bbskind=gj_today_no&crm_ukind=<?php echo $crm_ukind;?>&ifhnid=<?php echo $ifhnid;?>')" title="今天需跟进(根据下次联系时间匹配)">今天需跟进</a>
		<a href="javascript:;" <?php echo ($bbskind=='gj_tomorrow_no')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&bbskind=gj_tomorrow_no&crm_ukind=<?php echo $crm_ukind;?>&ifhnid=<?php echo $ifhnid;?>')" title="明天需跟进(根据下次联系时间匹配)">明天需跟进</a>
		<a href="javascript:;" <?php echo ($bbskind=='gj_today_yes')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&bbskind=gj_today_yes&crm_ukind=<?php echo $crm_ukind;?>&ifhnid=<?php echo $ifhnid;?>')" title="今天新增的跟进记录">今天已跟进</a>
        </dd></dl>
        
        <div class="clear"></div>
		<dl>
        <dt>售前红娘：</dt>
		<dd>
		<a href="javascript:;" <?php echo (empty($ifhnid))?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&ifhnid=&bbskind=<?php echo $bbskind;?>&crm_ukind=<?php echo $crm_ukind;?>')">不限</a>
		<a href="javascript:;" <?php echo ($ifhnid=='ifhnid0')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&ifhnid=ifhnid0&bbskind=<?php echo $bbskind;?>&crm_ukind=<?php echo $crm_ukind;?>')" title="未分配售前红娘">未分配</a>
		<a href="javascript:;" <?php echo ($ifhnid=='ifhnid1')?' class="ed"':''?> onClick="zeai.openurl('<?php echo $searchA;?>&ifhnid=ifhnid1&bbskind=<?php echo $bbskind;?>&crm_ukind=<?php echo $crm_ukind;?>')" title="已分配售前红娘">已分配</a>
        </dd></dl>
        
        
        <div class="clear" style="margin-top:10px"></div>
		<?php $sorthref = $searchA."&ifhnid=$ifhnid&bbskind=$bbskind&crm_ukind=$crm_ukind&sort=";?>
        <b style="font-weight:normal">升降排序</b>：
        <div class="sortbox">      
            注册时间<div class="sort ">
            <a title="升序" href="<?php echo $sorthref."regtime0";?>" <?php echo($sort == 'regtime0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sorthref."regtime1";?>" <?php echo($sort == 'regtime1')?' class="ed"':''; ?>></a>
            </div>
        </div>
        <div class="sortbox">     
            登录时间<div class="sort ">
            <a title="升序" href="<?php echo $sorthref."endtime0";?>" <?php echo($sort == 'endtime0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sorthref."endtime1";?>" <?php echo($sort == 'endtime1')?' class="ed"':''; ?>></a>
            </div>
        </div>
        <div class="sortbox">    
            认领时间<div class="sort ">
            <a title="升序" href="<?php echo $sorthref."admtime0";?>" <?php echo($sort == 'admtime0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sorthref."admtime1";?>" <?php echo($sort == 'admtime1')?' class="ed"':''; ?>></a>
            </div>
        </div>
        <div class="sortbox" title="按售前分配时间排序">  
            售前分配<div class="sort ">
            <a title="升序" href="<?php echo $sorthref."hntime0";?>" <?php echo($sort == 'hntime0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sorthref."hntime1";?>" <?php echo($sort == 'hntime1')?' class="ed"':''; ?>></a>
            </div>
        </div>
    </td></tr>
    
    <form id="zeaiFORM" method="get" action="<?php echo SELF; ?>">
    <tr>
    <th width="30"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="120">头像/UID/昵称</th>
    <th width="5" align="center">&nbsp;</th>
    <th width="130" align="left">基本资料</th>
    <th width="80" align="left">工作地区</th>
    <th width="80">户籍地区</th>
    <th width="200" align="left">所属门店/服务红娘</th>
    <th width="120" align="center">最后联系
            <div class="sort ">
            <a title="升序" href="<?php echo $sorthref."bbs_endtime0";?>" <?php echo($sort == 'bbs_endtime0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sorthref."bbs_endtime1";?>" <?php echo($sort == 'bbs_endtime1')?' class="ed"':''; ?>></a>
            </div>
    </th>
    <th width="120" align="center">下次联系
            <div class="sort">
            <a title="升序" href="<?php echo $sorthref."bbs_nexttime0";?>" <?php echo($sort == 'bbs_nexttime0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sorthref."bbs_nexttime1";?>" <?php echo($sort == 'bbs_nexttime1')?' class="ed"':''; ?>></a>
            </div>
    </th>
    <th width="10" align="center">
    </th>
    <th width="80" align="center">客户分类/备注</th>
    <th align="center" style="min-width:100px">跟进(次)<div class="sort">
            <a title="升序" href="<?php echo $sorthref."bbsnum0";?>" <?php echo($sort == 'bbsnum0')?' class="ed"':''; ?>></a>
            <a title="降序" href="<?php echo $sorthref."bbsnum1";?>" <?php echo($sort == 'bbsnum1')?' class="ed"':''; ?>></a>
      </div></th>
    <th width="140">帐号信息</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		//
		$agentid = $rows['agentid'];
		$agenttitle = dataIO($rows['agenttitle'],'out');
		$agenttitle = (!empty($agenttitle))?'<font class="C999">门店：</font>'.$agenttitle.'<br>':'';
		$admid   = $rows['admid'];
		$admname = dataIO($rows['admname'],'out');
		$admtime = intval($rows['admtime']);
		$hnid    = $rows['hnid'];
		$hnname  = dataIO($rows['hnname'],'out');
		$hnid2   = $rows['hnid2'];
		$hnname2 = dataIO($rows['hnname2'],'out');
		$crm_ukind = intval($rows['crm_ukind']);
		//
		$id = $rows['id'];$uid = $id;
		$sex      = $rows['sex'];
		$grade    = $rows['grade'];
		$photo_s  = $rows['photo_s'];
		
		$truename = strip_tags($rows['truename']);
		$uname = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$uname);
		$truename = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$truename);
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$nickname);
		//
		$areatitle = dataIO($rows['areatitle'],'out');
		$area2title = dataIO($rows['area2title'],'out');
		$crm_ubz = dataIO($rows['crm_ubz'],'out');
		//
		$tguid      = $rows['tguid'];
		$birthday  = $rows['birthday'];
		$age   = (@getage($birthday)<=0)?'':@getage($birthday).'岁';
		$edu   = udata('edu',intval($rows['edu']));
		$love  = udata('love',intval($rows['love']));
		
		if(!empty($nickname)){
			$title = $nickname;
		}else{
			if(!empty($truename)){
				$title=$truename;	
			}else{
				$title = 'UID:'.$uid;
			}
		}
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		if(empty($rows['nickname'])){if(empty($rows['truename'])){$title = $rows['mob'];}else{$title = $rows['truename'];}}else{
			$title = $nickname;
		}
		$bbsnum = intval($rows['bbsnum']);
		$bbsnumCls=($bbsnum>0)?'aLAN gjlist':'aHUI gjlist';
		$bbs_endtime = intval($rows['bbs_endtime']);
		$bbs_nexttime = intval($rows['bbs_nexttime']);
		$title2 = (!empty($nickname))?urlencode(trimhtml($nickname).'/'.$uid):$uid;
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="30"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
        </td>
      <td width="120" class="padding10">
        <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_ss"><img src="<?php echo $photo_s_url; ?>" class="photo_s"></a>
        <a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="photo_ss"><?php echo uicon($sex.$grade);?><?php echo $uid;?></a>
        <?php
		if(!empty($rows['nickname']))echo "</br><font class='uleft'>".$nickname."</font>";
		?>
        </td>
      <td width="5" align="center">
              
      </td>
      <td width="130" align="left"  class="lineH200">
      
        <?php if (!empty($truename)){?><font class="C999">姓名：</font><?php echo $truename;?><br><?php }?>
        <font class="C999">年龄：</font><?php echo $age.'('.substr($birthday,0,4).')';?><br>
        <?php if (!empty($love)){?><font class="C999">婚况：</font><?php echo $love;?><br><?php }?>
        <?php if (!empty($edu)){?><font class="C999">学历：</font><?php echo $edu;?><?php }?>
      
      </td>
      <td width="80" align="left" class="lineH200" style="padding:10px 0"><?php echo str_replace(" ","<br>",$areatitle); ?></td>
      <td width="80" class="lineH200"><?php echo str_replace(" ","<br>",$area2title); ?></td>
      <td width="200" align="left" class="lineH200">
		<?php echo $agenttitle;?>
        <?php $adm_str = ($admtime>0)?'认领':'录入';?>
        <?php if(!empty($admname)){echo '<font class="C999">'.$adm_str.'：</font>'.$admname.' <font class="C999">ID:'.$admid.'</font>';}?>

		<br><font class="C999 textmiddle">售前：</font><?php
		if(empty($hnname)){
			if(in_array('crm_hn_utask_sq_add',$QXARR)){?><a href="javascript:;" photo_s_url="<?php echo $photo_s_url;?>" class="btn size1 QING hnadd" uid="<?php echo $id;?>" title2="<?php echo $title2;?>">分配售前</a><br><?php }
		}else{?><span class="textmiddle"><?php echo $hnname;?> <font class="C999">ID:<?php echo $hnid;?></font></span>
			<?php if(in_array('crm_hn_utask_sq_mod',$QXARR)){?><a href="javascript:;" photo_s_url="<?php echo $photo_s_url;?>" class="btn size1 QING_ hnmod" uid="<?php echo $id;?>" title2="<?php echo $title2;?>">更换</a><?php }
		}?> 

      </td>
      <td width="120" align="center" class="lineH150">
	  	<?php
		if ($bbs_endtime > 0){echo '<div>'.YmdHis($bbs_endtime,'YmdHi').'</div>';
			$bbsday = intval(ADDTIME-$bbs_endtime);
			$bbsday = intval($bbsday/86400);
			if($bbsday>0){
				$bbsday_cls = ($bbsday>7)?' class="Cf00"':'';
				echo '<div'.$bbsday_cls.'>已过'.$bbsday.'天未联系</div>';
			}
		}?>
      </td>
      <td width="120" align="center" class="lineH150">
		<?php
		if ($bbs_nexttime > 0){
			echo '<div>'.YmdHis($bbs_nexttime,'YmdHi').'</div>';
			$nextday = intval($bbs_nexttime-ADDTIME);
			$nextday = intval($nextday/86400);
			if($bbs_nexttime<ADDTIME){//过期
				$nextday_str = ($nextday<-1)?abs($nextday).'天':'';
				echo '<div class="Cf00">过期'.$nextday_str.'未跟进</div>'; 
			}else{
				if($nextday>=1){
					echo '<div class="C090">'.$nextday.'天后联系</div>'; 
				}
			}
		}?>
      </td>
      <td width="10" align="center" >

      </td>
      <td width="80" align="center" ><a class="aBAI"  onClick="zeai.iframe('修改【<?php echo $id;?>】客户分类','crm_user.php?submitok=usre_crm_ukind_mod&uid=<?php echo $id;?>',700,320)"><?php echo udata('crm_ukind',$crm_ukind); ?></a><br><br>
      <a href="#" onClick="zeai.iframe('给【<?php echo $id;?>】备注','crm_user.php?submitok=bz&uid=<?php echo $id;?>',500,280)" class="editico tips" tips-title='<?php echo $crm_ubz;?>'></a><span id="bz<?php echo $id;?>"><?php if (!empty($crm_ubz))echo '<font class="newdian"></font>';?></span>
      
      </td>
      <td align="center">
      
			<a href="javascript:;" title="点击开始跟进" class="<?php echo $bbsnumCls;?>" photo_s_url=<?php echo $photo_s_url;?> uid="<?php echo $id;?>" title2="<?php echo $title2;?>"><?php echo $bbsnum;?></a>
        <br><br>
        <a href="javascript:;" class="btn QING2 size2 tips gjlist" tips-title='点击查看跟进' photo_s_url=<?php echo $photo_s_url;?> tips-direction='top' uid="<?php echo $id;?>" title2="<?php echo $title2;?>"><i class="ico">&#xe620;</i> 跟进</a>
      
      </td>
      <td width="140" class="lineH200">
        <font class="C999">注册时间：</font><?php echo YmdHis($rows['regtime'],'Ymd');?><br>
        <font class="C999">最后登录：</font><?php echo YmdHis($rows['endtime'],'Ymd');?></font><br>
        <font class="C999">帐号类型：</font><?php echo user_kind($rows['kind']); ?></font><br>
        <font class="C999">帐号状态：</font><?php echo flagtitle($rows['flag']);?></font>
      </td>
      </tr>
	<?php } ?>
    
    
    <div class="listbottombox">
        <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
        <button type="button" value="" class="btn size2 QING disabled action" onClick="hnTask(this,1,'批量分配售前红娘');">分配售前红娘</button>　
        <button type="button" value="" class="btn size2 QING2 disabled action" onClick="hnTask(this,2,'批量更换售前红娘');">更换售前红娘</button>　
        <?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </div>
    
    
    
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

zeai.listEach('.hnadd',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】分配售前红娘','crm_hn_utask_add.php?t=1&ulist='+uid,600,500);
	}
});
zeai.listEach('.hnmod',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】更换售前红娘','crm_hn_utask_add.php?t=2&ulist='+uid,600,500);
	}
});

zeai.listEach('.gjlist',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		urlpre = 'crm_gj_yj.php?uid='+uid+'&submitok=',
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】跟进管理'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'gj_list\',this);" class="ed"><i class="ico add">&#xe64b;</i> 跟进列表</a>'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'gj_add\',this);"><i class="ico add">&#xe620;</i> 新增跟进</a>'+
		'</div>',urlpre+'gj_list',700,520);
	}
});
</script>


<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>


<?php
require_once '../sub/init.php';
header("Cache-control: private");
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_crm.php';
require_once ZEAI.'cache/udata.php';

$t = (ifint($t,'1-4','1'))?$t:1;

$SQL = " (flag=1 OR flag=-2) AND kind<>4 ";
switch ($t) {
	//公海会员售前分配
	case 1:
		$SQL.=" AND sflag=0 AND hnid=0 ";
		if(!in_array('crm_hn_utask_sq_add',$QXARR))exit(noauth('暂无【公海会员售前分配】权限'));
	break;
	//售前会员调配(换红娘)      1 待确认,2 已确认
	case 2:
		if(!in_array('crm_hn_utask_sq_mod',$QXARR))exit(noauth('暂无【会员售前调配(换红娘)】权限'));
		$SQL.=" AND (sflag=0 OR sflag=1 OR sflag=2) ";
	break;
	//会员售后分配     2待确认      3 待服务
	case 3:
		if(!in_array('crm_hn_utask_sh_add',$QXARR))exit(noauth('暂无【会员售后分配】权限'));
		$SQL.="  AND hnid>0 AND hnid2=0 ";
	break;
	//售后会员调配(换红娘)   4 服务中
	case 4:
		if(!in_array('crm_hn_utask_sh_mod',$QXARR))exit(noauth('暂无【会员售后调配(换红娘)】权限'));
		$SQL.=" AND (sflag=3 OR sflag=4)  AND hnid>0 AND hnid2>0 ";
	break;
}
$SQL .= getAgentSQL();//非超管门店+地区 

//超管搜索按门店
if (ifint($agentid) && in_array('crm',$QXARR))$SQL .= " AND agentid=$agentid";

//按会员搜索
$Skeyword = trimhtml($Skeyword);
if (!empty($Skeyword)){
	if(ifint($Skeyword)){
		$SQL .= " AND id=".$Skeyword;	
	}else{
		$SQL .= " AND (( truename LIKE '%".$Skeyword."%' ) OR ( mob LIKE '%".$Skeyword."%' ) OR ( uname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' )) ";
	}
}
//按客户分类
if(ifint($crmukind))$SQL   .= " AND crmukind=$crmukind";
if (ifint($ifcontact))$SQL .= " AND (mob<>'' OR weixin<>'') ";
if (ifint($myinfobfb))$SQL .= " AND myinfobfb>$myinfobfb ";
if ($photo_s == 1)$SQL     .= " AND photo_s<>'' ";

$parameter = "p=$p&Skeyword=$Skeyword"; 

if($submitok=="usre_kind_mod_update"){
	if (!ifint($uid))exit('会员不存在或已被删除');
	$kind=intval($kind);
	$db->query("UPDATE ".__TBL_USER__." SET kind=$kind WHERE id=".$uid);
	alert_adm('修改成功',SELF."?submitok=usre_kind_mod&uid=".$uid);
}elseif($submitok=="usre_flag_mod_update"){
	if (!ifint($uid))exit('会员不存在或已被删除');
	$flag=intval($flag);
	if($flag==1 || $flag==2){
		$db->query("UPDATE ".__TBL_USER__." SET flag=$flag WHERE id=".$uid);
	}
	alert_adm('修改成功',SELF."?submitok=usre_flag_mod&uid=".$uid);
}
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
<style>
.tablelist{min-width:1200px;margin-top:20px}
.table0{width:98%;margin:10px auto}
.mtop{ margin-top:10px;}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
.table0{width:98%;margin:10px 20px 10px 20px}
.gradeflag{display:block;color:#999;padding-top:10px;font-family:'宋体'}
img.m{width:60px;height:60px;border-radius:40px;display:block;margin:5px 0;object-fit:cover;-webkit-object-fit:cover}
.datali{padding:15px 0}
.datali li{display:inline-block;padding:1px 5px;border:#ddd 1px solid;margin:3px;border-radius:3px;color:#888}
.mate li{color:#5EB87B;border-color:#A7CAB2}
</style>
<body>
<div class="navbox">


	<?php if ($t == 1){?><a class="ed">公海会员售前分配<?php echo '<b>'.$db->COUNT(__TBL_USER__,$SQL).'</b>';?></a><?php }?>
	<?php if ($t == 2){?><a class="ed">售前会员调配(换红娘)<?php echo '<b>'.$db->COUNT(__TBL_USER__,$SQL).'</b>';?></a><?php }?>
    
	<?php if ($t == 3){?><a class="ed">会员售后分配<?php echo '<b>'.$db->COUNT(__TBL_USER__,$SQL).'</b>';?></a><?php }?>
	<?php if ($t == 4){?><a class="ed">会员售后调配(换红娘)<?php echo '<b>'.$db->COUNT(__TBL_USER__,$SQL).'</b>';?></a><?php }?>
    
  <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<table class="table0">
<tr>
<td align="left" class="border0 S14 C999" >
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
  　　按会员
    <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="W150 input size2" placeholder="UID/昵称/姓名/手机" value="<?php echo $Skeyword; ?>">　　
    <span class="picmiddle">按客户分类</span> <script>zeai_cn__CreateFormItem('select','crmukind','<?php echo $crmukind; ?>','class="size2 picmiddle"',crmukind_ARR);</script>　　
    <span class="picmiddle">资料完整度</span> <script>zeai_cn__CreateFormItem('select','myinfobfb','<?php echo $myinfobfb; ?>','class="size2 picmiddle"',[{i:"10",v:"资料完整度>10%"},{i:"60",v:"资料完整度>60%"}]);</script>　
    
    <input type="checkbox" name="ifcontact" id="ifcontact" class="checkskin" value="1"<?php echo ($ifcontact == 1)?' checked':''; ?> ><label for="ifcontact" class="checkskin-label"><i></i><b class="W80 S14">有联系方法</b></label>
    <input type="checkbox" name="photo_s" id="photo_s" class="checkskin" value="1"<?php echo ($photo_s == 1)?' checked':''; ?>><label for="photo_s" class="checkskin-label"><i></i><b class="W50 S14">有照片</b></label>
    
    
    <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    <input type="hidden" name="t" value="<?php echo $t;?>" />
    <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
</form>    
</td>
</tr>
</table>
<?php


$areaid = '';

switch ($sort) {
	default:$SORT = " ORDER BY id DESC ";break;
}
$fields = ",mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2,mate_other";

$rt = $db->query("SELECT id,crmukind,sflag,agentid,agenttitle,admid,admname,hnid,hnname,hnid2,hnname2,truename,nickname,photo_s,sex,grade,areatitle,love,heigh,weigh,edu,pay,house,car,child,marrytype,job,mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areatitle,mate_love,mate_house".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
<form id="zeaiFORM" method="get" action="<?php echo SELF; ?>">
  <?php $sorthref = SELF."?".$parameter."&sort=";?>
  <table class="tablelist">
    <tr>
    <th width="30"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60">头像</th>
    <th width="120" align="center">UID/姓名/昵称</th>
    <th width="270" align="left">基本资料</th>
    <th width="270" align="left">择偶要求</th>
    <th align="center">&nbsp;</th>
    <th width="70" align="center">服务状态</th>
    <th width="120" align="center">门店</th>
    <th width="140" align="center">录入/认领</th>
    <th width="140" align="center">售前</th>
    <th width="140" align="center">售后</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		//
		$agentid = $rows['agentid'];
		$agenttitle = dataIO($rows['agenttitle'],'out');
		$agenttitle = (!empty($agenttitle))?'【'.$agenttitle.'】':'';
		$admid   = $rows['admid'];
		$admname = dataIO($rows['admname'],'out');
		$hnid    = $rows['hnid'];
		$hnname  = dataIO($rows['hnname'],'out');
		$hnid2   = $rows['hnid2'];
		$hnname2 = dataIO($rows['hnname2'],'out');
		$sflag    = $rows['sflag'];
		//
		$id = $rows['id'];$uid = $id;
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex      = $rows['sex'];
		$grade    = $rows['grade'];
		$photo_s  = $rows['photo_s'];
		//
		$birthday  = $rows['birthday'];
		$heigh = intval($rows['heigh']);
		$weigh = intval($rows['weigh']);
		$edu   = intval($rows['edu']);
		$pay   = intval($rows['pay']);
		$job   = intval($rows['job']);
		$love      = intval($rows['love']);
		$marrytype = intval($rows['marrytype']);
		$child     = intval($rows['child']);
		$house     = intval($rows['house']);
		$car       = intval($rows['car']);
		$areatitle = dataIO($rows['areatitle'],'out');
		//
		$areatitle_str = (str_len($areatitle)>2)?'<li>'.$areatitle.'</li>':'';
		$heigh_str = ($heigh>0)?'<li>'.$heigh.'cm</li>':'';
		$weigh_str = ($weigh>0)?'<li>'.$weigh.'kg</li>':'';
		$age_str   = (empty($birthday) || $birthday =='0000-00-00')?'':'<li>'.$birthday.'</li>';
		$edu_str   = (ifint($edu))?'<li>'.udata('edu',intval($rows['edu'])).'</li>':'';
		$pay_str   = (ifint($pay))?'<li>'.udata('pay',intval($rows['pay'])).'</li>':'';
		$job_str   = (ifint($job))?'<li>'.udata('job',intval($rows['job'])).'</li>':'';
		$love_str      = (ifint($love))?'<li>'.udata('love',intval($rows['love'])).'</li>':'';
		$marrytype_str = (ifint($marrytype))?'<li>'.udata('marrytype',intval($rows['marrytype'])).'</li>':'';
		$child_str     = (ifint($child))?'<li>'.udata('child',intval($rows['child'])).'</li>':'';
		$house_str     = (ifint($house))?'<li>'.udata('house',intval($rows['house'])).'</li>':'';
		$car_str       = (ifint($car))?'<li>'.udata('car',intval($rows['car'])).'</li>':'';
		//
		$truename = dataIO($rows['truename'],'out');
		$truename = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$truename);
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
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="30"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
        </td>
      <td width="60">        <a href="<?php echo Href('crm_u',$id);?>"><img src="<?php echo $photo_s_url; ?>" class="m"></a></td>
      <td width="120" >
        <a href="crm_user_detail.php?t=2&uid=<?php echo $id;?>"><?php echo uicon($sex.$grade).'<span class="picmiddle">'.$id.'</span>';?></a><br>
		<?php
        echo '<span class="picmiddle">';
        echo'<font class="uleft">';
            if(!empty($rows['truename']))echo $truename;
            if(!empty($rows['nickname']))echo '<br>'.$nickname;
        echo'</font>';
        echo'</span>';
		?>        
        
        </td>
      <td width="270" align="left" valign="middle" class="lineH150">
        <ul class="datali"><?php echo $areatitle_str.$love_str.$age_str.$heigh_str.$weigh_str.$job_str.$pay_str.$edu_str.$marrytype_str.$child_str.$house_str.$car_str;?></ul>
      </td>
      <td width="270" align="left" valign="middle" class="lineH150"><ul class="datali mate"><?php echo mate_echo($rows,'li');?><?php  //echo $mate_areatitle_str.$mate_love_str.$mate_age_str.$mate_heigh_str.$mate_pay_str.$mate_edu_str.$mate_house_str;?></ul></td>
      <td align="center" valign="middle" class="lineH200">&nbsp;</td>
      <td width="70" align="center" valign="middle" ><?php echo crm_stitle('text',$sflag);?></td>
      <td width="120" align="center" valign="middle" ><?php echo $agenttitle;?></td>
      <td width="140" align="center" valign="middle" class="lineH200"><?php if(!empty($admname)){echo $admname.'<font class="C999">（ID:'.$admid.'）</font>';}?></td>
      <td width="140" align="center" id="grade<?php echo $id;?>" class="lineH200">
      
		<?php
		//售前
		if(!empty($hnname)){
			echo $hnname.'<font class="C999">（ID:'.$hnid.'）</font>';
			if( ($sflag==0 || $sflag==1 || $sflag==2) && $t==2 ){//换红娘（售前）
				echo '<br><a href="javascript:;" class="btn size2 HONG2" onClick="zeai.iframe(\'给【'.$title.'】更换售前红娘\',\'crm_hn_utask_add.php?t=2&ulist='.$uid.'\',600,500)">更换售前红娘</a>';
			}
		}else{
			if( $t==1 ){
				echo '<a href="javascript:;" class="btn size2 HONG2" onClick="zeai.iframe(\'给【'.$title.'】分配售前红娘\',\'crm_hn_utask_add.php?t=1&ulist='.$uid.'\',600,500)">分配售前红娘</a>';
			}
		}
	   ?>
      </td>
      <td width="140" align="center" class="lineH200">
      
		<?php
		//售后
		if(in_array('crm',$QXARR)){//超管
			if(!empty($hnname2)){
				echo $hnname2.'<font class="C999">（ID:'.$hnid2.'）</font><br>';
			}
			echo '<a href="javascript:;" class="btn size2 HONG3" onClick="zeai.iframe(\'给【'.$title.'】分配售后红娘\',\'crm_hn_utask_add.php?t=3&ulist='.$uid.'\',600,500)">分配售后红娘</a>';
		}else{
			if(!empty($hnname2)){
				echo $hnname2.'<font class="C999">（ID:'.$hnid2.'）</font>';
				if(($sflag==3 || $sflag==4) && $t==4 ){//换红娘（售后）
					echo '<br><a href="javascript:;" class="btn size2 HONG3" onClick="zeai.iframe(\'给【'.$title.'】更换售后红娘\',\'crm_hn_utask_add.php?t=4&ulist='.$uid.'\',600,500)">更换售后红娘</a>';
				}
			}else{
				if( $t==3 ){
					echo '<a href="javascript:;" class="btn size2 HONG3" onClick="zeai.iframe(\'给【'.$title.'】分配售后红娘\',\'crm_hn_utask_add.php?t=3&ulist='.$uid.'\',600,500)">分配售后红娘</a>';
				}
			}
		}
	   ?>
      
      
      </td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="11">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" value="" class="btn size2 HONG2 disabled action" onClick="hnTask(this,<?php echo $t;?>);">批量分配</button>　
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
<?php require_once 'bottomadm.php';?>


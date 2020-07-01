<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('u_adm',$QXARR))exit(noauth('暂无【录入审核】权限'));
require_once ZEAI.'sub/zeai_up_func.php';

switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$v=intval($v);
				$row = $db->ROW(__TBL_USER__,"photo_s,nickname,openid,subscribe","id=".$v,"num");
				if ($row){
					$path_s = $row[0];$data_nickname = dataIO($row[1],'out');$data_openid = $row[2];$data_subscribe = $row[3];
					if (!empty($path_s)){
						$path_m = getpath_smb($path_s,'m');$path_b = getpath_smb($path_s,'b');$path_blue = getpath_smb($path_s,'blur');
						@up_send_admindel($path_s.'|'.$path_m.'|'.$path_b.'|'.$path_blue);
					}
					$uid=$v;
					$db->query("UPDATE ".__TBL_USER__." SET photo_s='',photo_f=0 WHERE id=".$v);
					AddLog('【业务员注册审核】删除会员【'.$data_nickname.'（uid:'.$v.'）】头像');
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;

	case"dataflag1":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);
				$db->query("UPDATE ".__TBL_USER__." SET flag=1,dataflag=1 WHERE (flag=0 || flag=2) AND id=".$id);
				$db->query("UPDATE ".__TBL_USER__." SET photo_f=1 WHERE photo_s<>'' AND id=".$id);
			}
			$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$id,'num');$nickname= $row2[0];
			AddLog('【业务员注册审核】审核会员【'.$nickname.'（uid:'.$id.'）】->通过');
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
}
require_once ZEAI.'cache/udata.php';
$extifshow = json_decode($_UDATA['extifshow'],true);
require_once ZEAI.'cache/config_vip.php';

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
.tablelist{min-width:1200px;margin:20px 20px 50px 20px}
.datali{padding:15px 0}
.datali li{display:inline-block;padding:1px 5px;border:#ddd 1px solid;margin:3px;border-radius:3px;color:#888}
.mate li{color:#5EB87B;border-color:#A7CAB2}
.u_add_ewm{width:200px;height:200px;pdding:5px;border:#ddd 1px solid;display:block;margin:0 auto}
.gradelist{padding:5px 0 10px 15px;border-left:#eee 4px solid;color:#999}
.radioskin:checked + label.radioskin-label b{color:#000}


a.noU58{width:60px;height:75px;position:relative;line-height:75px;}
a.noU58 img{margin-top:-3px;vertical-align:middle;width:60px;max-height:75px}
a.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:29px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}

.photombox{width:60px;position:relative}
.photombox .del{background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat;width:20px;height:20px;top:-10px;right:-10px;position:absolute;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3)}
.photombox .del:hover{background-position:-100px top;cursor:pointer}

</style>
<body>
<?php if ($t == 'ewm'){?>
<div class="navbox">
    <a class='ed'>线下采集二维码</a>
    <div class="clear"></div>
</div>
<div class="fixedblank"></div>

<table class="table W900 Mtop50" style="float:left;margin:15px 0 100px 20px">
  <tr>
    <td width="240" align="center" valign="top" class="S14"><br><img src="../sub/creat_ewm.php?url=<?php echo HOST.'/m1/adm_u_add.php';?>" class="u_add_ewm"><br>
微信扫码进行业务员录入<br><br><input class="btn size3" type="button" value=" 打开链接 " onClick="zeai.openurl_('<?php echo HOST.'/m1/adm_u_add.php';?>')" />
    <br></td>
    <td align="left" valign="top" class="lineH200 S14" style="padding:15px"><b class="S18">业务员如何创建和会员如何采集？</b><br><br>
    1．创建内部业务员角色，顶部【基础配置】->左侧【管理员用户组/权限】->【新增用户组】用户组名称填“业务员”，“分配权限	”->会员管理：<font class="Cf00">会员录入</font>，只要给这一个就行了，确认并保存<br><br>
    2．创建内部业务员帐号和资料，顶部【基础配置】->左侧【管理员用户】->【新增用户】用户组选刚才建的“业务员”，设置登录帐号密码和相关资料，最后保存<br><br>
    3．左边二维码扫码后进入手机端会员录入页面，登录页填此帐号密码，就开始线下现场采集会员资料了，采集后，会员默认是冻结未审核状态，后台审查无误后，正式纳入系统线下会员库<br><br>
    <font class="Cf00" >注：如果业务员离职，可以将其帐号删除或锁定，顶部【基础配置】->左侧【管理员用户】列表右侧，点【正常】按钮锁定或右边X进行删除</font>
     <font class="Cf00" style="display:none">注：也可以直接用电脑登录后台录入，顶部【会员管理】->左侧【会员录入】</font>
    <br><br>
    
    <b class="S18">采集后的会员如何审核？</b><br><br>
    1．顶部【内容审核/管理】->左侧【业务员注册审核】，将出现未审核和已审核会员列表，也可以到CRM后台顶部【会员管理】-&gt;左侧【录入审核】<br><br>
    2．会员列表中，点击【通过审核】，会员将正式纳入线下会员库，也可以二次修改，完善更详细的资料
    <br>
    <br>
<br></td>
  </tr>
</table>
<?php exit;}elseif($t == 'shupdate222222222222'){
	$fgrade = intval($fgrade);
	if ( !ifint($fgrade,'0-9','1,2') )textmsg("forbidden");
	$row = $db->ROW(__TBL_ROLE__,'if2',"grade=$fgrade AND kind=1","num");
	if(!$row){
		textmsg("此会员组不存在");
	}else{
		$fif2 = $row[0];
	}
	function is_time($time){
		$pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';
		return preg_match($pattern,$time);
	}
	function isDateTime($dateTime){
		$ret = strtotime($dateTime);
		return $ret !== FALSE && $ret != -1;
	}
	if (!is_time($fsjtime) || !isDateTime($fsjtime))textmsg("日期时间格式不对，请检查<br><br>例：".(date("Y")+1)."-01-15 06:48:29<br><br>",'back','返回重写');
	$fsjtime = strtotime($fsjtime);
	if ($fgrade == 1){
		$fif2 = 0;$fsjtime = 0;
	}
	$SQL = " ,if2=".$fif2.",sjtime=".$fsjtime;
	$db->query("UPDATE ".__TBL_USER__." SET flag=1,dataflag=1,grade=".$fgrade.$SQL." WHERE id=".$uid);
	$db->query("UPDATE ".__TBL_USER__." SET photo_f=1 WHERE photo_s<>'' AND id=".$uid);
	?>
	<script>
   // window.parent.document.getElementById('<?php echo $returnid; ?>').innerHTML = '<?php echo $endbz; ?>';
	window.parent.location.reload(true);
    //window.parent.zeai.iframe(0);
    </script>
    
    
    
<?php exit;}elseif($t == 'sh22222222222'){
	$urole  = json_decode($_ZEAI['urole'],true);
	$sj_if2ARR = json_decode($_VIP['sj_if2'],true);
	if (!is_array($sj_if2ARR) || @count($sj_if2ARR)<=0){
		exit("<div class='nodatatipsS'>会员组时间没有设置，请前往 顶部主菜单【会员管理】然后左侧【会员组套餐】</div>");
	}
	$row = $db->ROW(__TBL_USER__,'grade,sjtime',"id=$uid");
	if(!$row){
		textmsg("此会员不存在或已经锁定！");
	}else{
		$grade    = $row[0];
		$sjtime   = $row[1];
	}
	?>
	<script>
    function chkform(){
        if(zeai.empty(o('fsjtime').value)){
            parent.zeai.msg('请选择有效期限');
            return false;
        }
    }
    </script>
    <form name="ZEAI_FORM" method="POST" action="<?php echo SELF;?>" onsubmit="return chkform();">
    <table align="center" cellpadding="5" cellspacing="5" class="Mtop20 W90_">
        <?php if ($sjtime > 0){ ?>
            <tr>
                <td width="70" align="right" class="S14">起始时间 </td>
                <td align="left" class="S14"><input type="text" class="input size2 W90_" name="fsjtime" id="fsjtime" value="<?php echo YmdHis(ADDTIME,'YmdHis'); ?>" /></td>
            </tr>
        <?php }else{?>
            <input type="hidden" name="fsjtime" value="<?php echo date('Y-m-d H:i:s'); ?>" />
        <?php }?>
        <tr>
            <td align="right" class="S14">会员等级</td>
            <td align="left">
            <?php foreach($urole as $RV){?>
                <div class="gradelist">
                  <input type="radio" name="fgrade" id="fgrade<?php echo $RV['g'];?>" class="radioskin" value="<?php echo $RV['g'];?>"<?php echo ($RV['g'] == $grade)?' checked':'';?>>
                  <label for="fgrade<?php echo $RV['g'];?>" class="radioskin-label"><i class="i1"></i><b class="W120 S14"><?php echo $RV['t'].uicon_grade_all($RV['g']);?></b></label>
                </div>
            <?php }?>
            </td>
        </tr>
        <tr>
            <td align="center"><input type="hidden" name="t" value="shupdate" /><input type="hidden" name="uid" value="<?php echo $uid;?>" /></td>
            <td align="left"><input class="btn size3" type="submit" value=" 确定审核 " /></td>
        </tr>
    </table>
    </form>
<?php exit;}?>


<?php 
$SQL = "";
//门店+地区
if(!in_array('crm',$QXARR)){
	$SQL .= " AND agentid=$session_agentid";
	if(!empty($session_agentareaid) && str_len($session_agentareaid)>5){
		$areaid = explode(',',$session_agentareaid);
		$m1=$areaid[0];$m2=$areaid[1];$m3=$areaid[2];
		if (ifint($m1) && ifint($m2) && ifint($m3)){
			$areaid = $m1.','.$m2.','.$m3;
		}elseif(ifint($m1) && ifint($m2)){
			$areaid = $m1.','.$m2;
		}elseif(ifint($m1)){
			$areaid = $m1;
		}
		if (!empty($areaid))$SQL .= " AND areaid LIKE '%".$areaid."%' ";
	}
}
//门店+地区 结束

?>
<div class="navbox">


    <a href="u_adm.php"<?php echo ( $f != 1 && $f != 2 )?" class='ed'":""; ?>>未审核<?php if ($f != 1)echo '<b>'.$db->COUNT(__TBL_USER__,"admid>0 AND kind<>4 AND flag=0".$SQL).'</b>';?></a>
    <a href="u_adm.php?f=1"<?php echo ( $f == 1)?" class='ed'":""; ?>>已审核<?php if ($f == 1)echo '<b>'.$db->COUNT(__TBL_USER__,"admid>0 AND kind<>4 AND flag=1".$SQL).'</b>';?></a>
    <a href="u_adm.php?f=2"<?php echo ( $f == 2)?" class='ed'":""; ?>>已关注(注册未完成)<?php if ($f == 2)echo '<b>'.$db->COUNT(__TBL_USER__,"admid>0 AND kind<>4 AND flag=2".$SQL).'</b>';?></a>
    
    
    <div class="Rsobox">
        <form id="Zeai_search_form1" method="get" action="<?php echo SELF; ?>" class="FL" style="margin-right:15px">
        <input name="Skeyword1" type="text" id="Skeyword1" size="10" maxlength="10" class="input size2" placeholder="推荐人ID">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="f" value="<?php echo $f;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>
        
        <form id="Zeai_search_form" method="get" action="<?php echo SELF; ?>" class="FL">
        <input name="Skeyword" type="text" id="Skeyword" size="20" maxlength="30" class="W200 input size2" placeholder="会员UID/手机/姓名/昵称">
        <input type="hidden" name="f" value="<?php echo $f;?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>        
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php

$Skeyword1 = trimhtml($Skeyword1);
$Skeyword = trimhtml($Skeyword);
if (ifint($Skeyword) && !ifmob($Skeyword)){
	$SQL = " AND (id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL = " AND ( ( uname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".dataIO($Skeyword,'out')."%' ) OR ( truename LIKE '%".$Skeyword."%' ) OR ( mob LIKE '%".$Skeyword."%' ) )";
}
if (ifint($Skeyword1)){
	$SQL = " AND (admid=$Skeyword1) ";
}
if($f == 1){
	$SQL .= " AND flag=1";
}elseif($f == 2){	
	$SQL .= " AND flag=2";
}else{
	$SQL .= " AND flag=0";
}

$fieldlist = "id,admid,agentid,agenttitle,uname,truename,nickname,mob,photo_s,sex,grade,areatitle,love,heigh,weigh,edu,pay,house,car,child,marrytype,job,bz,myinfobfb,mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areatitle,mate_love,mate_house,regtime,regip,flag,qq,weixin,weixin_pic";
$rt = $db->query("SELECT ".$fieldlist." FROM ".__TBL_USER__." WHERE admid>0 AND kind<>4 ".$SQL." ORDER BY flag,id DESC");
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="120" align="left">UID/昵称</th>
    <th width="90" align="center">头像</th>
    <th width="20" align="center">&nbsp;</th>
    <th width="270" align="left">基本资料</th>
	<th width="300" align="left" >择偶要求</th>
	<th width="120" align="left" >联系方法</th>
	<th align="left" >备注</th>
	<th width="140" align="left" >录入/推荐人</th>
    <th width="80" align="center">资料完整度</th>
    <th width="100" align="center">注册状态审核操作</th>
    <th width="80" align="center">修改资料</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $id;
		$admid = $rows['admid'];
		$agentid = $rows['agentid'];
		$agenttitle = dataIO($rows['agenttitle'],'out');
		$agenttitle = (!empty($agenttitle))?'【'.$agenttitle.'】<br>':'';
		$flag  = $rows['flag'];
		$uname = strip_tags($rows['uname']);
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
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
		$mate_age1      = intval($rows['mate_age1']);
		$mate_age2      = intval($rows['mate_age2']);
		$mate_heigh1    = intval($rows['mate_heigh1']);
		$mate_heigh2    = intval($rows['mate_heigh2']);
		$mate_pay       = intval($rows['mate_pay']);
		$mate_edu       = intval($rows['mate_edu']);
		$mate_areatitle = dataIO($rows['mate_areatitle'],'out');
		$mate_love      = intval($rows['mate_love']);
		$mate_house     = intval($rows['mate_house']);
		  //
		  if (!empty($mate_age1) && !empty($mate_age2)){
			  $mate_age_str = $mate_age1.'～'.$mate_age2.' 岁';
		  }elseif(empty($mate_age1) && !empty($mate_age2)){
			  $mate_age_str = '小于 '.$mate_age2.' 岁';
		  }elseif(!empty($mate_age1) && empty($mate_age2)){
			  $mate_age_str = '大于 '.$mate_age1.' 岁';
		  }else{$mate_age_str = '不限';}
		  //
		  if (!empty($mate_heigh1) && !empty($mate_heigh2)){
			  $mate_heigh_str = $mate_heigh1.'～'.$mate_heigh2.' cm';
		  }elseif(empty($mate_heigh1) && !empty($mate_heigh2)){
			  $mate_heigh_str = '小于 '.$mate_heigh2.' cm';
		  }elseif(!empty($mate_heigh1) && empty($mate_heigh2)){
			  $mate_heigh_str = '大于 '.$mate_heigh1.' cm';
		  }else{$mate_heigh_str = '不限';}
		  $mate_pay_str = ($mate_pay>0)?'最低'.udata('pay',$mate_pay):'不限';
		  $mate_edu_str = ($mate_edu>0)?'最低'.udata('edu',$mate_edu):'不限';
		  $mate_areatitle_str = (!empty($mate_areatitle))?$mate_areatitle:'不限';
		  $mate_love_str      = ($mate_love>0)?udata('love',$mate_love):'不限';
		  $mate_house_str     = ($mate_house>0)?udata('house',$mate_house):'不限';
		  //
		  $mate_age_str   = ($mate_age_str=='不限')?'':'<li>'.$mate_age_str.'</li>';
		  $mate_heigh_str = ($mate_heigh_str=='不限')?'':'<li>'.$mate_heigh_str.'</li>';
		  $mate_pay_str   = ($mate_pay_str=='不限')?'':'<li>'.$mate_pay_str.'</li>';
		  $mate_edu_str   = ($mate_edu_str=='不限')?'':'<li>'.$mate_edu_str.'</li>';
		  $mate_areatitle_str= ($mate_areatitle_str=='不限')?'':'<li>'.$mate_areatitle_str.'</li>';
		  $mate_love_str  = ($mate_love_str=='不限')?'':'<li>'.$mate_love_str.'</li>';
		  $mate_house_str = ($mate_house_str=='不限')?'':'<li>'.$mate_house_str.'</li>';
		//
		if(empty($rows['nickname'])){$nickname = $uname;}
		$bz   = trimhtml(dataIO($rows['bz'],'out'));
		$addtime = YmdHis($rows['addtime']);
		$photo_s = $rows['photo_s'];
		$photo_f = $rows['photo_f'];
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'">';
		}else{
			$photo_s_str = '';
			$photo_s_url = '';
		}	
		$mob = trimhtml($rows['mob']);
		$mob = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$mob);
		$truename = dataIO($rows['truename'],'out');
		$truename = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$truename);
		$myinfobfb = $rows['myinfobfb'];
		$qq         = trimhtml(dataIO($rows['qq'],'out'));
		$weixin     = trimhtml(dataIO($rows['weixin'],'out'));
		$weixin_pic = $rows['weixin_pic'];
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
      </td>
        <td width="120" align="left" class="C999"><label for="id<?php echo $id; ?>">
		<?php echo uicon($sex.$grade).$id;?><br>
        <?php
		if(!empty($rows['nickname']))echo '<font class="uleft">'.$nickname.'</font>';
		?>        
        
        </label></td>
      <td width="90" align="center" style="padding:15px 0">
<!--        	<a href="javascript:;" class="noU58 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo getpath_smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
-->            
            
            

        	<div class="photombox">
        		<a href="javascript:;" class="noU58 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo getpath_smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
                <?php if (!empty($photo_s) && $photo_f==0){?><a href="javascript:;" class="del" uid="<?php echo $uid;?>" title="删除头像"></a><?php }?>
            </div>
            <?php if (!empty($photo_s)){?>
            <br><a href="javascript:cut(<?php echo $uid; ?>,'<?php echo urlencode($nickname); ?>','<?php echo $p; ?>');" class="aHEI" title="裁切头像">裁切头像</a>
            <?php }?>            
            
            
            
            
            
        </td>
      <td width="20" align="center" class="S12">&nbsp;</td>
    <td width="270" align="left" class="C999 lineH200">
<ul class="datali"><?php echo $areatitle_str.$love_str.$age_str.$heigh_str.$weigh_str.$job_str.$pay_str.$edu_str.$marrytype_str.$child_str.$house_str.$car_str;?></ul>

</td>
<td width="300" align="left" class="C999 lineH200">
<ul class="datali mate"><?php echo $mate_areatitle_str.$mate_love_str.$mate_age_str.$mate_heigh_str.$mate_pay_str.$mate_edu_str.$mate_house_str;?></ul>
</td>
<td width="120" align="left" class="C999">
<?php 
if(!empty($truename))echo '姓名：<font class="C666">'.$truename.'</font><br>';
if(!empty($mob))echo '手机：<font class="C666">'.$mob.'</font><br>';
if(!empty($weixin))echo '微信：<font class="C666">'.$weixin.'</font><br>';
if(!empty($weixin_pic)){
?>
<a style="margin:5px" href="javascript:;" class="noU58" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.getpath_smb($weixin_pic,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$weixin_pic; ?>"></a>
<?php }?>
</td>
<td align="left" class="C999 lineH200"><a href="#" onClick="zeai.iframe('给【<?php echo urlencode($nickname);?>】会员备注','u_bz.php?classid=<?php echo $id;?>',500,280)" class="edit tips" tips-title='<?php echo $bz;?>'>✎</a><span id="bz<?php echo $id;?>">
  <?php if (!empty($bz))echo '<font class="newdian"></font>';?>
</span></td>
<td width="140" align="left" class="C999 lineH150">
<?php 
echo $agenttitle;
if(ifint($admid)){
	$row = $db->ROW(__TBL_ADMIN__,"truename","id=".$admid);
	if ($row){
		$adm_truename=dataIO($row[0],'out');
		echo '<font class="C666">'.dataIO($row[0],'out').'(ID:'.$admid.')</font><br>';
	}
}
?>
<?php echo YmdHis($rows['regtime']);?><br><?php echo $rows['regip'];?>

</td>
	<td width="80" align="center" class="S14">
	  <span class="<?php if($myinfobfb >80){echo ' myinfobfb2';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>">
	    <?php echo $myinfobfb;?>%
	    </span>
	  </td>
	<td width="100" align="center">
      <?php if ($flag == 0){?>
          <font class="Caaa">未审核</font><br><br>
          <a href="javascript:;" title="审核" class="aLVed dataflag1" uid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>">通过审核</a>
      <?php }elseif($flag == 2){ ?>
          <font class="Caaa">已关注<br>(注册未完成)</font><br><br>
          <a href="javascript:;" title="审核" class="aLVed dataflag1" uid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>">强制通过</a>
      <?php }else{ ?>
          <i class='ico S18' style='color:#45C01A'>&#xe60d;</i><br><font style='color:#45C01A' class="S14">已审核</font>
      <?php }?>
   
    
    </td>
      <td width="80" align="center">
      
 <a href="javascript:;" class="btn size2 BAI tips editdata" tips-title='修改会员资料' tips-direction='left' uid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>">修改</a>
      
      </td>
      </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="12">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btnflaglist1" value="" class="btn size2 LV disabled action">批量审核</button>　
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
</form>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';

o('btnflaglist1').onclick = function() {
	allList({
		btnobj:this,
		url:'u_adm'+zeai.ajxext+'submitok=dataflag1',
		title:'批量审核（审核以后会员将进入CRM公海等待服务；如果是线上会员则可以登录前台自主交友）',
		msg:'正在审核中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.dataflag1',function(obj){
	var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.confirm('确定要审核【'+decodeURIComponent(nickname)+'】么？（审核以后会员将进入CRM公海等待服务；如果是线上会员则可以登录前台自主交友）',function(){
			
			//zeai.iframe('审核【'+decodeURIComponent(nickname)+'】','u_adm.php?t=sh&uid='+uid,350,440);
			zeai.ajax('u_adm'+zeai.ajxext+'submitok=dataflag1&list[]='+uid,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg);}
			});
			
		});
	}
});
zeai.listEach('.editdata',function(obj){
	var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.iframe('修改【'+decodeURIComponent(nickname)+'】资料','u_mod_data.php?submitok=mod&ifmini=1&uid='+uid,1300,600);
	}
});

function cut(id,nkname,p) {zeai.iframe('裁切【'+nkname+'】主头像','u_photo_cut.php?ifm=1&id='+id+'&submitok=www___zeai__cn_inphotocut'+'&p='+p,650,560);}
zeai.listEach('.del',function(obj){
	var uid = parseInt(obj.getAttribute("uid"));
	obj.onclick = function(){
		zeai.confirm('确定要删除头像么？',function(){
			zeai.ajax('u_adm'+zeai.ajxext+'submitok=alldel&list[]='+uid,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		});
	}
});

</script>
<script src="js/zeai_tablelist.js"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
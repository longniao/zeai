<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('u_jb_list',$QXARR))exit(noauth());
switch ($submitok) {
	case"dataflag1":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);$uid=$id;
				$row = $db->ROW(__TBL_USER__,"nickname,openid,subscribe","id=".$id,"num");
				if ($row){
					$db->query("UPDATE ".__TBL_USER__." SET dataflag=1 WHERE dataflag<>1 AND id=".$id);
					$db->query("UPDATE ".__TBL_USER__." SET photo_f=1 WHERE photo_s<>'' AND id=".$id);
					$data_nickname = dataIO($row[0],'out');$data_openid = $row[1];$data_subscribe = $row[2];
					AddLog('【资料审核】会员【'.$data_nickname.'（uid:'.$id.'）】->通过');
					//站内消息
					$C = $data_nickname.'恭喜你！个人资料已通过审核';
					$db->SendTip($uid,"恭喜你！个人资料已通过审核",dataIO($C,'in'),'sys');
					
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode('已通过');
						$keyword2 = urlencode('个人资料符合规范');
						$url      = urlencode(mHref('my'));
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					/*************通知粉丝*************/
					//给他粉丝站内推送
					//$tip_title   = '您关注的好友【'.$data_nickname.'】更新了个人资料';
					//$tip_content = $tip_title.'　　<a href="'.mHref('u',$uid).'" class="aQING">进入查看</a>';
					//@push_friend_tip($uid,$tip_title,$tip_content);
					//给他粉丝微信推送
					//$CARR = array();
					//$CARR['url']      = urlencode(mHref('u',$uid));
					//$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
					//$CARR['contentMB']= urlencode($tip_title);
					//@push_friend_wx($uid,$CARR);
					//
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
	case"dataflag2":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要驳回的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);$uid=$id;
				$row = $db->ROW(__TBL_USER__,"nickname,openid,subscribe","id=".$id,"num");
				if ($row){
					$db->query("UPDATE ".__TBL_USER__." SET dataflag=2 WHERE dataflag<>2 AND id=".$id);
					$data_nickname = dataIO($row[0],'out');$data_openid = $row[1];$data_subscribe = $row[2];
					AddLog('【资料审核】会员【'.$data_nickname.'（uid:'.$id.'）】->驳回');

					$title_def     = "对不起！个人基本资料审核失败！";
					$content_def   = "个人资料审核失败！请检查以下信息：<br>";
					$content_def  .= "　　1.基本资料不完善或不真实<br>";
					$content_def  .= "　　2.个人头像不是本人或未上传<br>";
					$content_def  .= "　　<a href=".Href('my_info')." class=aQING target=_blank>修改基本资料</a>　<a href=".Href('my_info')." class=aQING target=_blank>上传头像</a>";

					//站内消息
					$C = $content_def;
					$db->SendTip($uid,$title_def,dataIO($C,'in'),'sys');
					
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode('未通过');
						$keyword2 = urlencode('请重点检查【1.基本资料不完善或不真实 2.个人头像不是本人或未上传】');
						$url      = urlencode(mHref('my_info'));
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'驳回成功'));
	break;
}
require_once ZEAI.'cache/udata.php';
$extifshow = json_decode($_UDATA['extifshow'],true);
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
.tablelist{width:98%;min-width:1100px;margin:20px 20px 50px 20px}
a.noU58{width:60px;height:75px;position:relative;line-height:75px;border-radius:0}
a.noU58 img{margin-top:-3px;vertical-align:middle;width:60px;max-height:75px}
a.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:29px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
.datali li{display:inline-block;padding:1px 5px;border:#ddd 1px solid;margin:3px;border-radius:3px;color:#888}
.mate li{color:#5EB87B;border-color:#A7CAB2}
.photombox{width:60px;position:relative}
.photombox .del{background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat;width:20px;height:20px;top:-10px;right:-10px;position:absolute;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3)}
.photombox .del:hover{background-position:-100px top;cursor:pointer}
.forcerz{margin:0 0 10px 0}
i.wxlv{color:#31C93C;margin-right:2px}
</style>
<body>
<div class="navbox">
    <a href="u_jb_list.php?dataflag=0"<?php echo (empty($dataflag))?' class="ed"':'';?>>资料未审核<?php if(empty($dataflag))echo '<b>'.$db->COUNT(__TBL_USER__,"dataflag=0").'</b>';?></a>
    <a href="u_jb_list.php?dataflag=2"<?php echo ($dataflag==2)?' class="ed"':'';?>>资料被驳回<?php if($dataflag==2)echo '<b>'.$db->COUNT(__TBL_USER__,"dataflag=2").'</b>';?></a>
    <div class="Rsobox">
        <form id="Zeai_search_form" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="输入：UID/用户名/手机/姓名/昵称">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="dataflag" value="<?php echo $dataflag;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<?php $sorthref = SELF."?dataflag=$dataflag&p=$p&sort="; ?>

<?php
$SQL = "1=1";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL .= " AND (id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL .= " AND ( ( uname LIKE '%".$Skeyword."%' ) OR ( mob LIKE '%".$Skeyword."%' )  OR ( truename LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}
if ($dataflag==2){
	$SQL .= " AND dataflag=2 ";
}else{
	$SQL .= " AND dataflag=0 ";
}

$fieldlist = "id,birthday,admid,uname,truename,nickname,mob,photo_s,photo_f,sex,grade,areatitle,love,heigh,weigh,edu,pay,house,car,child,marrytype,job,bz,myinfobfb,mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_pay,mate_edu,mate_areatitle,mate_love,mate_house,regtime,regip,flag,qq,weixin,weixin_pic,aboutus,RZ,subscribe";

switch ($sort) {
	case 'myinfobfb0':$SORT = " ORDER BY myinfobfb,id DESC ";break;
	case 'myinfobfb1':$SORT = " ORDER BY myinfobfb DESC,id DESC ";break;
	default:$SORT = " ORDER BY endtime DESC ";break;
}

if (@count($extifshow) >0 && is_array($extifshow)){foreach ($extifshow as $ev){$evARR[] = $ev['f'];}$fieldlist .= ",".implode(",",$evARR);}
$rt = $db->query("SELECT ".$fieldlist." FROM ".__TBL_USER__." WHERE ".$SQL.$SORT);
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
    <th width="70" align="left">UID</th>
    <th width="90" align="center">头像</th>
    <th width="130">认证/用户名/昵称</th>
    <th width="270">基本资料</th>
	<th width="300" align="left" >择偶要求</th>
	<th width="120" align="left" >联系方法</th>
	<th align="left" >&nbsp;</th>
    <th width="100" align="center">资料完整度
<div class="sort">
	<a href="<?php echo $sorthref."myinfobfb0";?>" <?php echo($sort == 'myinfobfb0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."myinfobfb1";?>" <?php echo($sort == 'myinfobfb1')?' class="ed"':''; ?>></a>
</div>
    
    </th>
    <th width="60" align="center">公众号</th>
    <th width="100" align="center">通过</th>
    <?php if ($dataflag == 0){?><th width="100" align="center">不通过</th><?php }?>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $id;
		$admid = $rows['admid'];
		$flag  = $rows['flag'];
		$uname = strip_tags($rows['uname']);
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$nickname = trimhtml(dataIO($rows['nickname'],'out'));
		$aboutus = dataIO($rows['aboutus'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex   = $rows['sex'];
		$grade = $rows['grade'];
		$RZZ   = $rows['RZ'];
		$subscribe   = $rows['subscribe'];
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
		$areatitle_str = (!empty($areatitle))?'<li>'.$areatitle.'</li>':'';
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
		$photo_fstr = ($photo_f == 0 && !empty($photo_s))?'<span>头像未审</span>':'';
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
      </td>
      <td width="70" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="90" align="center" style="padding:15px 0">
        	<div class="photombox">
        		<a href="javascript:;" class="noU58 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo getpath_smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
                <?php if (!empty($photo_s) && $photo_f==0){?><a href="javascript:;" class="del" uid="<?php echo $uid;?>" title="删除头像"></a><?php }?>
            </div>
            <?php if ($photo_f == 0 && !empty($photo_s)){?>
            <br><a href="javascript:;" title="只审头像" class="aHUI photo_mflag1" uid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>">单审头像</a><br>
            <?php }?>
            <?php if (!empty($photo_s)){?>
            <br><a href="javascript:cut(<?php echo $uid; ?>,'<?php echo urlencode($nickname); ?>','<?php echo $p; ?>');" class="aHEI" title="裁切头像">裁切头像</a>
            <?php }?>
        </td>
      <td width="130" align="left" class="S12">
      <?php if(!empty($RZZ)){?><div title="手动强制认证" uid="<?php echo $id;?>" class="forcerz hand"><?php echo RZ_html($RZZ,'s','color');?></div><?php }?>
        <a href="<?php echo Href('u',$id);?>" target="_blank">
        <?php
		echo uicon($sex.$grade);
		echo $uname."</br>";
		echo '<span style="vertical-align:middle">';
		echo '<font class="uleft">'.$nickname.'</font>';
		//if(!empty($rows['truename']))echo '<font class="uleft">'.$truename.'</font>';
		echo '</span>';
		?>
      </a><br></td>
    <td width="200" align="left" class="C999 lineH200" style="padding:10px 0">
        <ul class="datali">
        <?php echo $areatitle_str.$love_str.$age_str.$heigh_str.$weigh_str.$job_str.$pay_str.$edu_str.$marrytype_str.$child_str.$house_str.$car_str;?>
        </ul>
        <?php if (!empty($aboutus)){?><div>自我介绍：<font class="blue"><?php echo $aboutus;?></font></div><?php }?>
		<?php
        if (@count($extifshow) > 0 || is_array($extifshow)){
			foreach ($extifshow as $V) {
				if($V['s']==1){
					if(!empty($rows[$V['f']])){
					echo '<div>'.$V['t'].'：<font class="blue">'.dataIO($rows[$V['f']],'out').'</font></div>';
					}
				}
			}
		}
		?>
    </td>
    <td width="300" align="left" class="C999 lineH200"><ul class="datali mate">
<?php echo $mate_love_str.$mate_age_str.$mate_heigh_str.$mate_pay_str.$mate_edu_str.$mate_areatitle_str.$mate_house_str;?>
</ul></td>
    <td width="120" align="left" class="C999 lineH200"><?php 
if(!empty($truename))echo '姓名：<font class="C666">'.$truename.'</font><br>';
if(!empty($mob))echo '手机：<font class="C666">'.$mob.'</font><br>';
if(!empty($weixin))echo '微信：<font class="C666">'.$weixin.'</font><br>';
if(!empty($weixin_pic)){
?>
<a style="margin:5px" href="javascript:;" class="noU58" onClick="parent.piczoom('<?php echo $_ZEAI['up2'].'/'.getpath_smb($weixin_pic,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$weixin_pic; ?>"></a>
<?php }?></td>
    <td align="left" class="C999 lineH200">&nbsp;</td>
	<td width="100" align="center" class="S18">
	  <span class="<?php if($myinfobfb >80){echo ' myinfobfb2';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>">
	    <?php echo $myinfobfb;?>%
	    </span>
        
        <a href="javascript:;" class="editico tips" tips-title='修改会员资料' tips-direction='left' uid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>"></a>
	  </td>
    <td width="60" align="center"><?php
if($subscribe==0){
	echo '<span class="C999"></span>';
}elseif($subscribe==1){
	echo '<i class="ico S14 wxlv">&#xe6b1;</i>';
}else{
	echo '<span class="C00f">取消</span>';
}
?></td>
    <td width="100" align="center"><a href="javascript:;" title="审核" class="aLVed dataflag1" uid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>">通过审核</a></td>
    <?php if ($dataflag == 0){?><td width="100" align="center"><a href="javascript:;" title="拒绝" class="aHEIed dataflag2" uid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>">驳回</a></td><?php }?>
    </tr>
	<?php } ?>
</table>
<div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量驳回</button>　
    <button type="button" id="btnflaglist" class="btn size2 LV disabled action">批量审核</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);"><i class="ico">&#xe676;</i> 发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
</div>
</form>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';

o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'u_jb_list'+zeai.ajxext+'submitok=dataflag2',
		title:'批量驳回',
		msg:'驳回处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.dataflag2',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
		zeai.confirm('真的要驳回【'+decodeURIComponent(nickname)+'】个人资料么？',function(){
			zeai.msg('正在驳回处理...',{time:300});
			zeai.ajax('u_jb_list'+zeai.ajxext+'submitok=dataflag2&list[]='+uid,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});
zeai.listEach('.photo_mflag1',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
		zeai.confirm('确认要审核【'+decodeURIComponent(nickname)+'】头像么？',function(){
			zeai.msg('正在审核处理【'+decodeURIComponent(nickname)+'】',{time:300});
			zeai.ajax('photo_m'+zeai.ajxext+'submitok=allflag1&list[]='+uid,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});
o('btnflaglist').onclick = function() {
	allList({
		btnobj:this,
		url:'u_jb_list'+zeai.ajxext+'submitok=dataflag1',
		title:'批量审核',
		content:'此审核将连同头像一起审核<br>将同步批量发送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。',
		msg:'审核处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.dataflag1',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
		zeai.confirm('确认要审核【'+decodeURIComponent(nickname)+'】么？（此审核将连同头像一起审核）',function(){
			zeai.msg('正在审核处理【'+decodeURIComponent(nickname)+'】',{time:300});
			
			//console.log('u_jb_list'+zeai.ajxext+'submitok=dataflag1&list[]='+uid);return false;
			
			zeai.ajax('u_jb_list'+zeai.ajxext+'submitok=dataflag1&list[]='+uid,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg);}
			});
		});
	}
});
zeai.listEach('.editico',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
		zeai.iframe('修改【'+decodeURIComponent(nickname)+'】资料','u_mod_data.php?submitok=mod&ifmini=1&uid='+uid,1300,700);
	}
});
function cut(id,nkname,p) {zeai.iframe('裁切【'+nkname+'】主头像','u_photo_cut.php?ifm=1&id='+id+'&submitok=www___zeai__cn_inphotocut'+'&p='+p,650,560);}

zeai.listEach('.del',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid"));
		zeai.confirm('真的要删除么？<br>删除后将自动发送驳回信息（微信通知和站内信），引导会员重新上传',function(){
			zeai.ajax('photo_m'+zeai.ajxext+'submitok=alldel&list[]='+uid,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		});
	}
});
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
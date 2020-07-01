<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if($k==2 || $k==3){
	if(!in_array('shop',$QXARR))exit(noauth());
}elseif($k==1){
	if(!in_array('u_tg',$QXARR))exit(noauth());
}else{
	exit(noauth());
}
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_shop.php';
$TG_set = json_decode($_REG['TG_set'],true);
$logstr='推广员/买家/'.$_SHOP['title'];
switch ($submitok) {
	case"ding":
		if(!ifint($id))exit(JSON_ERROR);
		$db->query("UPDATE ".__TBL_TG_USER__." SET px=".ADDTIME." WHERE id=".$id);
		AddLog('【'.$logstr.'】->置顶【id:'.$id.'】');
		header("Location: ".SELF."?k=".$k);
	break;
	case"all_update_endtime":
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要操作的用户'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $uid){
				if ( !ifint($uid))exit(JSON_ERROR);
				$db->query("UPDATE ".__TBL_TG_USER__." SET endtime=".ADDTIME." WHERE id=".$uid);
			}
		}
		json_exit(array('flag'=>1,'msg'=>'更新成功'));
	break;
	case"modflag":
		if (!ifint($uid))alert_adm_parent("forbidden","-1");
		$row = $db->ROW(__TBL_TG_USER__,"flag,uname,shopflag","id=$uid","num");
		if(!$row){
			alert_adm_parent("您要操作的用户不存在或已经删除！","-1");
		}else{
			$flag = $row[0];$nickname = $row[1];$shopflag = $row[2];
			$SQL = "";
			if($k==2){
				$logstr=$_SHOP['title'];
				switch($shopflag){
					case"-1":$SQL="shopflag=1";break;
					case"0":$SQL="shopflag=1";break;
					case"1":$SQL="shopflag=-1";break;
				}
			}else{
				$logstr='推广员';
				switch($flag){
					case"-1":$SQL="flag=1";break;
					case"0":$SQL="flag=1";break;
					case"1":$SQL="flag=-1";break;
				}
			}
			$db->query("UPDATE ".__TBL_TG_USER__." SET ".$SQL." WHERE id=".$uid);
			AddLog('【'.$logstr.'】->【'.$nickname.'（ID:'.$uid.'）】帐号状态修改');
			header("Location: ".SELF."?kind=$kind&f=$f&k=$k&p=$p");
		}
	break;
	case"alldel":
		if(!in_array('u_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'权限不足'));
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $uid){
				$uid=intval($uid);
				$row2 = $db->ROW(__TBL_TG_USER__,"uname,title,photo_s,weixin_ewm,yyzz_pic,piclist","id=".$uid,'num');$nickname= $row2[0];$title= dataIO($row2[1],'out');
				$photo_s= $row2[2];$weixin_ewm= $row2[3];$yyzz_pic= $row2[4];$piclist= $row2[5];
				if($shopflag!=2){
					$nickname=$title;
				}
				if(!empty($photo_s)){
					@up_send_admindel($photo_s.'|'.smb($photo_s,'m').'|'.smb($photo_s,'b').'|'.smb(str_replace('/shop/','/tmp/',$photo_s),'blur'));
				}
				if(!empty($weixin_ewm)){
					@up_send_admindel($weixin_ewm.'|'.smb($weixin_ewm,'m').'|'.smb($weixin_ewm,'b').'|'.smb(str_replace('/shop/','/tmp/',$weixin_ewm),'blur'));
				}
				if(!empty($yyzz_pic)){
					@up_send_admindel($yyzz_pic.'|'.smb($yyzz_pic,'m').'|'.smb($yyzz_pic,'b').'|'.smb(str_replace('/shop/','/tmp/',$yyzz_pic),'blur'));
				}
				if(!empty($piclist)){
					$piclist = explode(',',$piclist);
					foreach ($piclist as $value) {
						@up_send_admindel($value.'|'.smb($value,'m').'|'.smb($value,'b').'|'.smb(str_replace('/shop/','/tmp/',$value),'blur'));
					}
				}
				$db->query("DELETE FROM ".__TBL_LOVEB_LIST__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_MONEY_LIST__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_TG_PRODUCT_KIND__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_SHOP_YUYUE__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_SHOP_FAV__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_SHOP_ORDER__." WHERE tg_uid=".$uid);
				$db->query("DELETE FROM ".__TBL_SHOP_SEARCH__." WHERE tg_uid=".$uid);
				$rt = $db->query("SELECT id,path_s FROM ".__TBL_TG_PRODUCT__." WHERE tg_uid=".$uid);
				$total = $db->num_rows($rt);
				if ($total > 0 ) {
					for($i=1;$i<=$total;$i++) {
						$row = $db->fetch_array($rt,'name');
						if(!$row) break;
						$fid = $row['id'];
						$path_s = $row['path_s'];
						if(!empty($path_s)){$B = smb($path_s,'b');@up_send_admindel($path_s.'|'.$B.'|'.smb($path_s,'m'));}
						$db->query("DELETE FROM ".__TBL_TG_PRODUCT__." WHERE id=".$fid);
					}
				}
				$db->query("UPDATE ".__TBL_USER__." SET tguid=0,tgflag=0 WHERE tguid=".$uid);
				$db->query("DELETE FROM ".__TBL_TG_USER__." WHERE id=".$uid);
				AddLog('【'.$logstr.'】帐号删除->【'.$nickname.'（ID:'.$uid.'）】');
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"allflag1":
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $uid){
				if ( !ifint($uid))exit(JSON_ERROR);
				$row = $db->ROW(__TBL_TG_USER__,"nickname,openid,subscribe,shopflag","id=".$uid,"name");
				if ($row){
					$data_nickname  = dataIO($row['nickname'],'out');
					$data_openid    = $row['openid'];
					$data_subscribe = $row['subscribe'];
					if($k==2){
						$kindstr=$_SHOP['title'];
						$tipkind='shop';
						$db->query("UPDATE ".__TBL_TG_USER__." SET shopflag=1 WHERE id=".$uid);
					}else{
						$kindstr='推广员';
						$tipkind='tg';
						$db->query("UPDATE ".__TBL_TG_USER__." SET flag=1 WHERE id=".$uid);
					}
					//站内消息
					$C = $data_nickname.'【'.$kindstr.'】帐号审核通过！';
					$db->SendTip($uid,'【'.$kindstr.'】帐号审核通过！',dataIO($C,'in'),$tipkind);
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode('已通过');
						$keyword2 = urlencode('【'.$kindstr.'】帐号符合规范');
						$url      = urlencode(HOST."/m1/tg_my.php");
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
				}
				AddLog('【'.$kindstr.'】帐号审核通过->【'.$data_nickname.'（ID:'.$uid.'）】');
			}
		}
		json_exit(array('flag'=>1,'msg'=>'批量审核'));
	break;
	case"shop_grade_mod_update":
		if (!ifint($tg_uid))exit('tg_uid不存在');
		$shopgrade = intval($shopgrade);
		if($shopgrade>0){
			if(!ifdatetime($sjtime))json_exit(array('flag'=>0,'msg'=>'【服务起始时间】不合法'));
			if(!ifdatetime($sjtime2))json_exit(array('flag'=>0,'msg'=>'【服务结束时间】不合法'));
			$sjtime  = strtotime($sjtime);
			$sjtime2 = strtotime($sjtime2);
		}else{
			$sjtime1 = 0;
			$sjtime2 = 0;
		}
		$row = $db->ROW(__TBL_TG_ROLE__,'title',"grade=0 AND shopgrade=".$shopgrade,"num");
		$shopgradetitle=$row[0];
		$flag = intval($flag);
		$db->query("UPDATE ".__TBL_TG_USER__." SET shopflag=$flag,shopgrade=$shopgrade,shopgradetitle='$shopgradetitle',sjtime='$sjtime',sjtime2='$sjtime2' WHERE id=".$tg_uid);
		AddLog('【'.$_SHOP['title'].'】修改【ID:'.$tg_uid.'）】等级信息');
		json_exit(array('flag'=>1,'msg'=>'操作成功'));
	break;
	case"grade_mod_update":
		if (!ifint($tg_uid))exit('tg_uid不存在');
		$grade = intval($grade);
		$row = $db->ROW(__TBL_TG_ROLE__,'title',"shopgrade=0 AND grade=".$grade,"num");
		$gradetitle=$row[0];
		$flag = intval($flag);
		$db->query("UPDATE ".__TBL_TG_USER__." SET flag=$flag,grade=$grade,gradetitle='$gradetitle' WHERE id=".$tg_uid);
		AddLog('【推广员】修改【ID:'.$tg_uid.'）】等级信息');
		json_exit(array('flag'=>1,'msg'=>'操作成功'));
	break;
}
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
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<?php if ($submitok != 'u' && $submitok != 'tgu'){?>
<style>
.tablelist{min-width:1111px;margin:0 20px 50px 20px}
.table0{min-width:1111px;width:98%;margin:10px 20px 10px 20px}
.mtop{ margin-top:10px;}
.dispaly{ display:block;}
.listtd{ display:block; width:50px;border-radius:12px;height:20px;line-height:20px;color:#888;padding:2px 5px;font-size:12px;background:#f9f9f9;border:#dedede solid 1px; margin:5px auto; margin-left:0px; text-align:center;}
.citybox{margin-left:20px}
.gradeflag{display:block;color:#999;padding-top:10px;font-family:'宋体'}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
i.wxlv{color:#31C93C;margin-right:2px}
.tjr{border:#eee 1px solid;background-color:#f9f9f9;color:#666;padding:5px 10px;text-align:center;margin:5px auto}
</style>
<?php }?>
<body>
<?php if ($submitok == 'u'){
	$tg_uid=intval($tg_uid);
	$SQL = "";
	$Skey = trimm($Skey);
	if (ifint($Skey)){
		$SQL = " AND (id=$Skey) ";
	}elseif(!empty($Skey)){
		$SQL = " AND ( ( uname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".urlencode($Skey)."%' ) )";
	}
	$rt = $db->query("SELECT id,nickname,sex,grade,photo_s,regtime,myinfobfb,birthday,areatitle,love,tgflag FROM ".__TBL_USER__." WHERE tguid=$tg_uid ".$SQL." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
		if (!empty($SQL))echo "<br><br><a class='aQINGed' href='javascript:history.back(-1)'>重新筛选</a>";
		echo "</div>";
	} else {
		$page_skin = 1;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';?>
<div class="navbox" style="min-width:500px">
        <a href="TG_u.php?tg_uid=<?php echo $tg_uid;?>&submitok=<?php echo $submitok;?>" class="ed">推荐用户 <?php echo '<b>'.$total.'</b>';?> 人</a>
        <div class="Rsobox">
          <form name="www.yzlove.com.v6.0..QQ7144100" method="get" action="<?php echo SELF; ?>">
            <input name="Skey" type="text" id="Skey" maxlength="25" class="input size2" value="<?php echo $Skey;?>" placeholder="按UID/用户名/昵称查询">
            <input type="hidden" name="submitok" value="<?php echo $submitok;?>">
            <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>">
            <input type="hidden" name="k" value="<?php echo $k;?>">
            <input type="submit" value="搜索" class="btn size2 QING" />
          </form>
        </div>
        <div class="clear"></div></div>
        <div class="fixedblank"></div>
        <table class="tablelist W95_ Mtop10 Mbottom50">
        <tr>
        <th width="60">用户</th>
        <th width="170" align="left"></th>
        <th align="left">基本信息</th>
        <th width="70" align="center">资料完整度</th>
        <th width="70" align="center">注册时间</th>
        <th width="50" align="center">推荐状态</th>
        </tr>
        <?php
        for($i=1;$i<=$pagesize;$i++) {
            $rows = $db->fetch_array($rt,'name');
            if(!$rows) break;
            $id       = $rows['id'];
            $uid      = $id;
            $nickname = dataIO($rows['nickname'],'out');
            $nickname = str_replace($Skey,"<b class='Cf00'>".$Skey."</b>",$nickname);
            $sex      = $rows['sex'];
            $grade    = $rows['grade'];
            $tgflag   = $rows['tgflag'];
            $photo_s  = $rows['photo_s'];
            $birthday  = $rows['birthday'];$birthday_str  = (@getage($birthday)<=0)?'':@getage($birthday).'岁 ';
            $areatitle  = $rows['areatitle'];
            $love  = $rows['love'];$love=udata('love',$love);$love_str=(!empty($love))?$love.'<br>':'';
            $regtime  = YmdHis($rows['regtime']);
            $myinfobfb  = $rows['myinfobfb'];
            $photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
            $href        = Href('u',$uid);
        ?>
        <tr>
        <td width="60" height="30" align="left" class="padding10"><a class="pic50 yuan border0"><img src="<?php echo $photo_s_url; ?>"></a></td>
        <td width="170" height="30" align="left"><a class="S14"><?php echo uicon($sex.$grade) ?> <?php echo $nickname; ?><br><font class="S12 C999">UID:<?php echo $uid;?></font></a></td>
        <td align="left" class="C999 lineH200"><?php echo $birthday_str.$love_str.$areatitle;?></td>
        <td width="70" align="center" class="C999 S16"><span class="<?php if($myinfobfb >80){echo ' myinfobfb2 ';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>"><?php echo ($myinfobfb>0)?$myinfobfb.'%':'';?></span></td>
        <td width="70" height="30" align="center" class="C999"><?php echo $regtime;?></td>
        <td width="50" align="center" class="C999" title="<?php echo ($tgflag == 1)?'已奖励':'未审或未奖励';?>"><?php
            if ($tgflag == 1){
                echo "<i class='ico S18' style='color:#45C01A;vertical-align:middle'>&#xe60d;</i>";
            } else {
                echo "<i class='ico S18 Cccc' style='vertical-align:middle'>&#xe62c;</i>";
            }
        ?></td>
        </tr>
        <?php } ?>
        <?php if ($total > $pagesize){?>
        <tfoot>
        <tr>
        <td colspan="6"><?php if ($total > $pagesize)echo '<div class="pagebox">'.$pagelist.'</div>'; ?></div></td>
        </tr>
        </tfoot>
    <?php }?>
    </table>
<?php }exit;}elseif($submitok == 'tgu'){

	$tg_uid=intval($tg_uid);
	$SQL = "";
	$Skey = trimm($Skey);
	if (ifint($Skey)){
		$SQL = " AND (id=$Skey) ";
	}elseif(!empty($Skey)){
		$SQL = " AND ( ( uname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".urlencode($Skey)."%' ) )";
	}
	$rt = $db->query("SELECT id,uname,photo_s,regtime,tgflag,gradetitle,shopgradetitle,kind FROM ".__TBL_TG_USER__." WHERE tguid=$tg_uid ".$SQL." ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataicoS Mtop100'><i></i>暂无信息";
		if (!empty($SQL))echo "<br><br><a class='aQINGed' href='javascript:history.back(-1)'>重新筛选</a>";
		echo "</div>";
	} else {
		$page_skin = 1;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';?>
        <div class="navbox" style="min-width:500px">
        <a href="TG_u.php?tg_uid=<?php echo $tg_uid;?>&submitok=<?php echo $submitok;?>" class="ed">推荐合伙人 <?php echo '<b>'.$total.'</b>';?> 人</a>
        <div class="Rsobox">
            <form name="www.yzlove.com.v6.0..QQ7144100" method="get" action="<?php echo SELF; ?>">
            <input name="Skey" type="text" id="Skey" maxlength="25" class="input size2" value="<?php echo $Skey;?>" placeholder="按ID/用户名/昵称查询">
            <input type="hidden" name="submitok" value="<?php echo $submitok;?>">
            <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>">
            <input type="hidden" name="k" value="<?php echo $k;?>">
            <input type="submit" value="搜索" class="btn size2 QING" />
            </form>
        </div>
        <div class="clear"></div></div>
        <div class="fixedblank"></div>
        <table class="tablelist W95_ Mtop10 Mbottom50">
        <tr>
        <th width="60" align="center">合伙人</th>
        <th width="170">帐号</th>
        <th align="center">等级</th>
        <th width="70" align="center">类型</th>
        <th width="70" align="center">注册时间</th>
        <th width="50" align="center">推荐状态</th>
        </tr>
        <?php
        for($i=1;$i<=$pagesize;$i++) {
            $rows = $db->fetch_array($rt,'name');
            if(!$rows) break;
            $id       = $rows['id'];
			$kind = $rows['kind'];
			$uname = strip_tags($rows['uname']);
				$uname = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$uname);
				$title = dataIO($rows['title'],'out');
				$title = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$title);
			$mob = strip_tags($rows['mob']);
			$mob = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$mob);
			$photo_s   = $rows['photo_s'];
			$gradetitle= ($kind==1)?$rows['gradetitle']:$rows['shopgradetitle'];
			$areatitle = $rows['areatitle'];
			$tgflag    = $rows['tgflag'];
			$nickname  = dataIO($rows['nickname'],'out');
			$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/noP.gif';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="m yuan">';
			switch ($kind) {
				case 1:$kind_str = '个人';break;
				case 2:$kind_str = '商家';break;
				case 3:$kind_str = '机构';break;
			}
			$regtime  = YmdHis($rows['regtime']);
        ?>
        <tr>
        <td width="60" height="30" align="center" class="padding10"><?php echo $photo_s_str; ?></td>
        <td width="170" height="30">
	<?php
    echo '<div class="S16">'.$id.'</div>';
	if(!empty($uname))echo '<div class="C999">'.$uname.'</div>';
	//if(!empty($mob))echo '<div class="C999">'.$mob.'</div>';
	if(!empty($nickname))echo '<div class="C999">'.$nickname.'</div>';
    ?></td>
        <td align="center" class="C999 lineH200"><?php echo $gradetitle;?></td>
        <td width="70" align="center" class="C999"><?php echo $kind_str;?></td>
        <td width="70" height="30" align="center" class="C999"><?php echo $regtime;?></td>
        <td width="50" align="center" class="C999" title="<?php echo ($tgflag == 1)?'已奖励':'未审或未奖励';?>"><?php
            if ($tgflag == 1){
                echo "<i class='ico S18' style='color:#45C01A;vertical-align:middle'>&#xe60d;</i>";
            } else {
                echo "<i class='ico S18 Cccc' style='vertical-align:middle'>&#xe62c;</i>";
            }
        ?></td>
        </tr>
        <?php } ?>
        <?php if ($total > $pagesize){?>
        <tfoot>
        <tr>
        <td colspan="6"><?php if ($total > $pagesize)echo '<div class="pagebox">'.$pagelist.'</div>'; ?></div></td>
        </tr>
        </tfoot>
    <?php }?>
    </table>
<?php }exit;}elseif($submitok == 'shop_grade_mod'){
	if (!ifint($tg_uid))alert_adm("参数错误","-1");
	$rt = $db->query("SELECT shopgrade,sjtime,sjtime2,shopflag FROM ".__TBL_TG_USER__." WHERE id=".$tg_uid);
	if($db->num_rows($rt)){
		$row  = $db->fetch_array($rt,'name');
		$shopgrade   = $row['shopgrade'];
		$sjtime  = intval($row['sjtime']);
		$sjtime2 = intval($row['sjtime2']);
		$sjtime = (!empty($sjtime))?YmdHis($sjtime):'';
		$sjtime2 = (!empty($sjtime2))?YmdHis($sjtime2):'';
		$flag  = intval($row['shopflag']);
	}else{alert_adm("记录不存在！","-1");}?>
	<style>
	.RCW li{width:100%;height:24px;line-height:24px}
    .tdL{width:20%}
    .tdR{width:30%}
    </style>
    <form id="ZEAIFORM">
    <table class="table W90_ Mtop20">
    <tr><td class="tdL"><font class="Cf00">*</font><?php echo $_SHOP['title'];?>等级</td><td class="tdR">
	<?php
    $rt2=$db->query("SELECT shopgrade,title FROM ".__TBL_TG_ROLE__." WHERE grade=0 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('【'.$_SHOP['title'].'等级组】为空，请先去增加','shop_role.php');
    } else {?>
      <!--<ul class="size2 RCW"><li><input type="radio" class="radioskin" id="shopgrade0" name="shopgrade" value="0"><label for="shopgrade0" class="radioskin-label"><i class="i1"></i><b class="">--</b></label></li></ul>-->
      <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
            $clss=($shopgrade==$rows2[0])?' checked':'';?>
      		<ul class="size2 RCW"><li><input type="radio" class="radioskin" id="shopgrade<?php echo $rows2[0];?>" name="shopgrade" value="<?php echo $rows2[0];?>" <?php echo $clss;?>><label for="shopgrade<?php echo $rows2[0];?>" class="radioskin-label"><i class="i1"></i><b class=""><?php echo dataIO($rows2[1],'out');?></b></label></li></ul>
      <?php
        }
    }?>
      </td>
      <td class="tdL"><font class="Cf00">*</font>帐号状态</td>
      <td class="tdR"><div id="crm_flagbox"><script>zeai_cn__CreateFormItem('radio','flag','<?php echo $flag; ?>',' class="size2 RCW"',<?php echo $_SHOP['flagarr'];?>);//onclick="meet_flag(this);"</script></div></td>
    </tr>
    <tr><td class="tdL"><font class="Cf00">*</font>服务起始时间</td><td colspan="3" class="tdR"><input type="text" maxlength="19" class="input size2 W200" name="sjtime" id="sjtime" value="<?php echo $sjtime; ?>" placeholder="如：<?php echo YmdHis(ADDTIME);?>" /></td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font>服务结束时间</td><td colspan="3" class="tdR"><input type="text" maxlength="19" class="input size2 W200" name="sjtime2" id="sjtime2" value="<?php echo $sjtime2; ?>" /></td></tr>
    </table>
    <input type="hidden" name="submitok" value="shop_grade_mod_update" />
    <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
    <input type="hidden" name="k" value="<?php echo $k;?>">
</form>
    <br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">修改</button></div>
	<script>
    save.onclick=function(){
        if (!zeai.form.ifradio('shopgrade')){
            zeai.msg('请选择【<?php echo $_SHOP['title'];?>等级】',crm_flag1);
            return false;
        }
        zeai.confirm('<b class="S18">确定修改么？</b><br>此修改属于硬改，请慎重！',function(){
			zeai.ajax({url:'TG_u'+zeai.extname,form:ZEAIFORM},function(e){rs=zeai.jsoneval(e);
				window.parent.zeai.msg(0);window.parent.zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){window.parent.location.reload(true);},1000);}
			});
		});
    }
    </script>
<?php exit;}elseif($submitok == 'grade_mod'){
	if (!ifint($tg_uid))alert_adm("参数错误","-1");
	$rt = $db->query("SELECT grade,flag FROM ".__TBL_TG_USER__." WHERE id=".$tg_uid);
	if($db->num_rows($rt)){
		$row   = $db->fetch_array($rt,'name');
		$grade = $row['grade'];
		$flag  = intval($row['flag']);
	}else{alert_adm("记录不存在！","-1");}?>
	<style>
	.RCW li{width:100%;height:24px;line-height:24px}
    .tdL{width:20%}
    .tdR{width:30%}
    </style>
    <form id="ZEAIFORM">
    <table class="table W90_ Mtop20">
    <tr><td class="tdL"><font class="Cf00">*</font>推广等级</td><td class="tdR">
	<?php
    $rt2=$db->query("SELECT grade,title FROM ".__TBL_TG_ROLE__." WHERE shopgrade=0 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('【推广等级组】为空，请先去增加','TG_role.php?submitok=add');
    } else {?>
      <ul class="size2 RCW"><li><input type="radio" class="radioskin" id="grade0" name="grade" value="0"><label for="grade0" class="radioskin-label"><i class="i1"></i><b class="">--</b></label></li></ul>
      <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
            $clss=($grade==$rows2[0])?' checked':'';?>
      		<ul class="size2 RCW"><li><input type="radio" class="radioskin" id="grade<?php echo $rows2[0];?>" name="grade" value="<?php echo $rows2[0];?>" <?php echo $clss;?>><label for="grade<?php echo $rows2[0];?>" class="radioskin-label"><i class="i1"></i><b class=""><?php echo dataIO($rows2[1],'out');?></b></label></li></ul>
      <?php
        }
    }?>
      </td>
      <td class="tdL"><font class="Cf00">*</font>帐号状态</td>
      <td class="tdR"><div id="crm_flagbox"><script>zeai_cn__CreateFormItem('radio','flag','<?php echo $flag; ?>',' class="size2 RCW"',<?php echo $_SHOP['flagarr'];?>);//onclick="meet_flag(this);"</script></div></td>
    </tr>
    </table>
    <input type="hidden" name="submitok" value="grade_mod_update" />
    <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
    <input type="hidden" name="k" value="<?php echo $k;?>">
    </form>
    <br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">修改</button></div>
	<script>
    save.onclick=function(){
        if (!zeai.form.ifradio('grade')){
            zeai.msg('请选择【推广等级】',crm_flag1);
            return false;
        }
        zeai.confirm('<b class="S18">确定修改么？</b><br>此修改属于硬改，请慎重！',function(){
			zeai.ajax({url:'TG_u'+zeai.extname,form:ZEAIFORM},function(e){rs=zeai.jsoneval(e);
				window.parent.zeai.msg(0);window.parent.zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){window.parent.location.reload(true);},1000);}
			});
		});
    }
    </script>
<?php exit;}?>


<div class="navbox" style="min-width:1300px">
    <?php 
	$SQL = "1=1";
	$Skey = trimhtml($Skey);
	//搜索
	if (!empty($Skey)){
		if(ifmob($Skey)){
			$SQL .= " AND mob=".$Skey;	
		}elseif(ifint($Skey)){
			$SQL .= " AND (id=".$Skey." OR uid=".$Skey.")";	
		}elseif(str_len($Skey)>20){	
			$SQL .= " AND openid='$Skey'";				
		}else{
			$SQL .= " AND ( ( title LIKE '%".$Skey."%' ) OR ( uname LIKE '%".$Skey."%' ) OR ( nickname LIKE '%".$Skey."%' ) ) ";
		}
	}
	if($k == 1){
		$kstr='推广'.$TG_set['tgytitle'];
		$fld = "grade";
		$KSQL=" WHERE shopgrade=0 ";
		$SQL  .= " AND flag<>2";
		if(ifint($g))$SQL .= " AND grade=".$g;
		switch ($f) {
			case 'f_1':$SQL .= " AND flag=-1";break;
			case 'f_2':$SQL .= " AND flag=-2";break;
			case 'f0':$SQL .= " AND flag=0";break;
			case 'f1':$SQL .= " AND flag=1";break;
			case 'f2':$SQL .= " AND flag=2";break;
			case 'f3':$SQL .= " AND flag=3";break;
		}
	}elseif($k==2){
		$kstr=$_SHOP['title'];
		$fld = "shopgrade";	
		$KSQL=" WHERE grade=0 ";

		$SQL  .= " AND shopflag<>2";
		if(ifint($g))$SQL .= " AND shopgrade=".$g;
		switch ($f) {
			case 'f_1':$SQL .= " AND shopflag=-1";break;
			case 'f_2':$SQL .= " AND shopflag=-2";break;
			case 'f0':$SQL .= " AND shopflag=0";break;
			case 'f1':$SQL .= " AND shopflag=1";break;
			case 'f2':$SQL .= " AND shopflag=2";break;
			case 'f3':$SQL .= " AND shopflag=3";break;
		}
	}
	?>
    <a href="TG_u.php?k=<?php echo $k;?>"<?php echo (empty($f) && empty($g))?" class='ed'":""; ?>><?php echo $kstr;?>管理<?php if (empty($f) && empty($g))echo '<b>'.$db->COUNT(__TBL_TG_USER__,$SQL).'</b>';?></a>
    <a href="TG_u.php?f=f1&k=<?php echo $k;?>"<?php echo ($f == 'f1')?" class='ed'":""; ?>>正常<?php if ($f == 'f1')echo '<b>'.$db->COUNT(__TBL_TG_USER__,"  ".$SQL).'</b>';?></a>
    <a href="TG_u.php?f=f0&k=<?php echo $k;?>"<?php echo ($f == 'f0')?" class='ed'":""; ?>>未审核<?php if ($f == 'f0')echo '<b>'.$db->COUNT(__TBL_TG_USER__,"  ".$SQL).'</b>';?></a>
    <a href="TG_u.php?f=f_2&k=<?php echo $k;?>"<?php echo ($f == 'f_2')?" class='ed'":""; ?>>已隐藏<?php if ($f == 'f_2')echo '<b>'.$db->COUNT(__TBL_TG_USER__,"  ".$SQL).'</b>';?></a>
    <a href="TG_u.php?f=f_1&k=<?php echo $k;?>"<?php echo ($f == 'f_1')?" class='ed'":""; ?>>已锁定<?php if ($f == 'f_1')echo '<b>'.$db->COUNT(__TBL_TG_USER__,"  ".$SQL).'</b>';?></a>
	<?php 
	$rtg=$db->query("SELECT grade,shopgrade,title FROM ".__TBL_TG_ROLE__.$KSQL." ORDER BY px DESC,grade DESC,id DESC");
	$totalg = $db->num_rows($rtg);
	if ($totalg > 0) {
		for($ig=1;$ig<=$totalg;$ig++) {
			$rowsg = $db->fetch_array($rtg,'num');
			if(!$rowsg) break;
			$grade  = $rowsg[0];
			$shopgrade  = $rowsg[1];
			$grade = ($k>1)?$shopgrade:$grade;
			$gtitle = dataIO($rowsg[2],'out');
			?>
			<a href="TG_u.php?g=<?php echo $grade;?>&k=<?php echo $k ?>"<?php echo ($g == $grade)?" class='ed'":""; ?>><?php echo $gtitle;?><?php if ($g == $grade)echo '<b>'.$db->COUNT(__TBL_TG_USER__,"  ".$SQL).'</b>';?></a>
			<?php 
        }
	}
    ?>
    <div class="Rsobox">
             
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<?php
	$fUnum  = ",(SELECT COUNT(id) FROM ".__TBL_USER__." WHERE tguid=TG_U.id) AS Unum";
	$fTGnum = ",(SELECT COUNT(id) FROM ".__TBL_TG_USER__." WHERE tguid=TG_U.id) AS TGnum";
	switch ($sort) {
		case 'loveb0':$SORT = " ORDER BY loveb ";break;
		case 'loveb1':$SORT = " ORDER BY loveb DESC ";break;
		case 'money0':$SORT = " ORDER BY money ";break;
		case 'money1':$SORT = " ORDER BY money DESC ";break;
		case 'addtime0':$SORT = " ORDER BY regtime ";break;
		case 'addtime1':$SORT = " ORDER BY regtime DESC ";break;
		case 'endtime0':$SORT = " ORDER BY endtime ";break;
		case 'endtime1':$SORT = " ORDER BY endtime DESC ";break;
		case 'logincount0':$SORT = " ORDER BY logincount ";break;
		case 'logincount1':$SORT = " ORDER BY logincount DESC ";break;
		case 'uid0':$SORT = " ORDER BY id ";break;
		case 'uid1':$SORT = " ORDER BY id DESC ";break;
		default:$SORT = " ORDER BY px DESC,id DESC ";break;
	}
$rt = $db->query("SELECT * ".$fUnum.$fTGnum." FROM ".__TBL_TG_USER__." TG_U WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a href='TG_u_mod.php?submitok=add&k=".$k."' class='btn HUANG size2' href='javascript:history.back(-1)'>新增".$kstr."</a><br><br></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>

<table class="table0">
<tr>
<td width="120" align="left" class="border0" >


<button type="button" class="btn " onClick="zeai.openurl('TG_u_mod.php?submitok=add&k=<?php echo $k;?>')"><i class="ico add">&#xe620;</i>新增<?php echo $kstr;?></button>
</td>
<td width="150" align="center" class="border0" >　</td>
<td align="center">
<form name="form1" method="get" action="<?php echo SELF; ?>">
    <input name="Skey" type="text" id="Skey" maxlength="50" class="W300 input size2" placeholder="输入：ID/帐号/手机/<?php echo $_SHOP['title'];?>名称/绑定的UID" title="也可以搜索OPENDID">
    <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
    <input name="f" type="hidden" value="<?php echo $f; ?>" />
    <input name="kind" type="hidden" value="<?php echo $kind; ?>" />
    <input name="k" type="hidden" value="<?php echo $k; ?>" />
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    <input type="hidden" name="g" value="<?php echo $g;?>" />
    <input type="submit" value="搜索" class="btn size2 QING" />
</form>
</td>
<td width="140" align="left"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">发消息关注公众号有效</font></td>
<td width="90" align="right"><button type="button" id="btnsend" value="" class="btn size2 disabled action">发送消息</button></td>
</tr>
</table>
<form id="www_zeai_cn_FORM" method="get" action="<?php echo $SELF; ?>">
<?php $sorthref = SELF."?f=$f&k=$k&g=$g&sort="; ?>
<table class="tablelist">
<tr>
<th width="30" class="Pleft10"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
<th width="100" align="center">ID/帐号/昵称</th>
<th width="60" align="center">置顶</th>
<th width="60" align="center">头像</th>
<th width="130" align="center"><span class="center"><?php echo $kstr;?>等级</span></th>
<th width="10" align="center" class="center">&nbsp;</th>
<th width="200" align="center"<?php if($k == 1)echo ' style="display:none"';?>><?php echo $_SHOP['title'];?>名称/扫码进入</th>
<th width="60" align="center"<?php if($k == 1)echo ' style="display:none"';?>>商品数</th>
<th width="60" align="center" class="center" title="推荐的下级单身用户" style="cursor:help">名下单身</th>
<th width="80" align="center" class="center" title="推荐的下级推广员" style="cursor:help">名下合伙人</th>

<!--<th width="100"><?php echo $_ZEAI['loveB'];?>收益
<div class="sort">
	<a href="<?php echo $sorthref."loveb0";?>" <?php echo($sort == 'loveb0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."loveb1";?>" <?php echo($sort == 'loveb1')?' class="ed"':''; ?>></a>
</div>
</th>-->


<th width="100">余额
<div class="sort">
	<a href="<?php echo $sorthref."money0";?>" <?php echo($sort == 'money0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."money1";?>" <?php echo($sort == 'money1')?' class="ed"':''; ?>></a>
</div></th>
<th width="70" align="center">关注公众号</th>


<th style="min-width:50px">备注</th>
<th width="140" align="center">注册时间/IP/推荐人
  <div class="sort">
    <a href="<?php echo $sorthref."uid0";?>" <?php echo($sort == 'uid0')?' class="ed"':''; ?>></a>
    <a href="<?php echo $sorthref."uid1";?>" <?php echo($sort == 'uid1')?' class="ed"':''; ?>></a>
  </div></th>
<th width="110" align="center">帐号状态/绑定UID</th>
<th width="70"  class="center">修改</th>
</tr>
<?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$kind = $rows['kind'];
		$pwd = $rows['pwd'];
		$uname = strip_tags($rows['uname']);
			$uname = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$uname);
			$title = dataIO($rows['title'],'out');
			$title = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$title);
		$mob = strip_tags($rows['mob']);
		$mob = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$mob);
		$photo_s   = $rows['photo_s'];
		$sex       = $rows['sex'];
		$uid       = $rows['uid'];
		$grade     = $rows['grade'];
		$gradetitle= $rows['gradetitle'];
		$shopgrade= $rows['shopgrade'];
		$shopflag = $rows['shopflag'];
		$shopgradetitle= $rows['shopgradetitle'];
		$loveb     = $rows['loveb'];
		$money     = $rows['money'];
		$subscribe = $rows['subscribe'];
		$openid    = $rows['openid'];
		$flag      = $rows['flag'];
		$areatitle = $rows['areatitle'];
		$longitude = $rows['longitude'];
		$latitude  = $rows['latitude'];
		$bz = dataIO($rows['bz'],'out');
		$tguid     = $rows['tguid'];
		$tgflag    = $rows['tgflag'];
		$tgmoney   = $rows['tgmoney'];
		$nickname  = dataIO($rows['nickname'],'out');
		$Unum  = $rows['Unum'];
		$TGnum = $rows['TGnum'];
		$sjtime=$rows['sjtime'];
		$sjtime2=$rows['sjtime2'];
		$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/noP.gif';
		$photo_s_str = '<img src="'.$photo_s_url.'" class="m yuan">';
		switch ($kind) {
			case 1:$kind_str = '个人';break;
			case 2:$kind_str = '公司';break;
			case 3:$kind_str = '机构';break;
		}
		
		$fHREF  = SELF."?submitok=modflag&uid=$id&g=$g&f=$f&k=$k&p=$p";
		$fHREF2 = "TG_u_mod.php?submitok=mod&tg_uid=$id&f=$f&g=$g&k=$k&p=$p&k2=$k&Skey=$Skey";
		$uname = empty($uname)?$mob:$uname;
		if($k==2){
			$pnum   = $db->COUNT(__TBL_TG_PRODUCT__,"tg_uid=".$id);
			$grade=$shopgrade;
			$gradetitle=$shopgradetitle;
			$grade_str=grade_time(array('url'=>'\''.SELF.'?submitok=shop_grade_mod&k='.$k.'&tg_uid='.$id.'\'','grade'=>$shopgrade,'gradetitle'=>shopgrade($shopgrade,'img',2).$shopgradetitle,'d1'=>ADDTIME,'d2'=>$sjtime2,'ifA'=>'btn_djs','T'=>'\'【'.$title.'，ID:'.$id.'】'.$_SHOP['title'].'等级\'','WH'=>'600,460'));
		}else{
			$grade_str=grade_time(array('url'=>'\''.SELF.'?submitok=grade_mod&k='.$k.'&tg_uid='.$id.'\'','grade'=>$grade,'gradetitle'=>$gradetitle,'d1'=>$sjtime,'d2'=>$sjtime2,'ifA'=>'btn','T'=>'\'【'.$nickname.'，ID:'.$id.'】推广等级\'','WH'=>'600,360'));
		}
?>
<tr id="tr<?php echo $id;?>">
<td width="30" height="68" class="Pleft10">
<input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="uid<?php echo $id; ?>" class="checkskin"><label for="uid<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>　
</td>
<td width="100" align="center">

<a href="#" onclick="zeai.openurl('<?php echo $fHREF2;?>')">
    <?php
    echo '<div class="S16">'.$id.'</div>';
	if(!empty($uname))echo '<div class="C999">'.$uname.'</div>';
	//if(!empty($mob))echo '<div class="C999">'.$mob.'</div>';
	if(!empty($nickname))echo '<div class="C999">'.$nickname.'</div>';
    ?>        
</a>


</td>
<td width="60" align="center"><a href="<?php echo "TG_u.php?id=".$id; ?>&submitok=ding&k=<?php echo $k;?>" class="topico" title="置顶"></a></td>
<td width="60" align="center"><a href="javascript:;" class="zoom" onClick="parent.piczoom('<?php echo smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str; ?></a></td>

<td width="130" align="center" class="lineH150" style="padding:15px 0 10px 0"><?php echo $grade_str; ?></td>
<td width="10" align="center" class="center">&nbsp;</td>
<td width="200" align="center" class="S14"<?php if($k==1)echo ' style="display:none"';?>>
<?php if (!empty($title)){?>
<a href="<?php echo $fHREF2;?>"><?php echo $title;?></a>
<?php }?>
<div>
<a href="javascript:;" onclick="parent.zeai.iframe('【<?php echo $title;?>】二维码','u_ewm.php?cid=<?php echo $id;?>',400,300);" title="放大二维码" class="zoom">
<img src="images/ewm.gif" class="ewmpic">
</a></div>
</td>
<td width="60" align="center" class="lineH200 S14"<?php if($k==1)echo ' style="display:none"';?>><a href="TG_u_product.php?tg_uid=<?php echo $id;?>" class="<?php echo ($pnum >0)?'aHONG':'aHUI';?>"><?php echo $pnum;?></a></td>
<td width="60" align="center" class="center"><a href="javascript:;" class="<?php echo ($Unum >0)?'aHONG':'aHUI';?>" onClick="zeai.iframe('【<?php echo $id;?>】的名下用户','TG_u.php?submitok=u&tg_uid=<?php echo $id;?>&k=<?php echo $k;?>',650,600)"><?php echo $Unum;?></a></td>
<td width="80" align="center" class="center"><a href="javascript:;" class="<?php echo ($TGnum >0)?'aHONG':'aHUI';?>" onClick="zeai.iframe('【<?php echo $id;?>】的名下合伙人','TG_u.php?submitok=tgu&tg_uid=<?php echo $id;?>&k=<?php echo $k;?>',650,600)"><?php echo $TGnum;?></a></td>

<!--<td width="100" align="left" id="loveb<?php echo $id;?>">
  <a href="javascript:;" class="aHUI" onClick="zeai.iframe('【<?php echo $id;?>】的<?php echo $_ZEAI['loveB']; ?>清单','u_loveb_list.php?tg_uid=<?php echo $id;?>',650,600)" title="<?php echo $_ZEAI['loveB']; ?>清单"><?php echo $loveb;?></a>&nbsp;
  <a href="javascript:;" onClick="zeai.iframe('给【<?php echo $id;?>】增加<?php echo $_ZEAI['loveB']; ?>','u_loveb_mod.php?tg_uid=<?php echo $id;?>',320,250)"><img src="images/add.gif" title="充值" /></a>
</td>-->

<td width="100" align="left" id="money<?php echo $id;?>">

	<a href="javascript:;" class="aHONG" onClick="zeai.iframe('【<?php echo $id;?>】的余额清单','u_money_list.php?tg_uid=<?php echo $id;?>',650,600)" title="余额清单"><?php echo $money;?></a>&nbsp;
	<a href="javascript:;" onClick="zeai.iframe('给【<?php echo $id;?>】增加余额','u_money_mod.php?tg_uid=<?php echo $id;?>',320,250)"><img src="images/add.gif" title="增加余额" /></a>

</td>
<td width="70" align="center" id="money<?php echo $id;?>"><?php
if($subscribe==0){
	echo '<span class="C999"></span>';
}elseif($subscribe==1){
	echo '<i class="ico S14 wxlv">&#xe6b1;</i>';
}else{
	echo '<span class="C00f">取消</span>';
}
?>
</td>
<td align="left" class="C8d " style="min-width:50px">
  <a href="#" onClick="zeai.iframe('给【<?php echo $id;?>】备注','u_bz.php?tg_uid=<?php echo $id;?>',500,280)" class="editico tips" tips-title='<?php echo $bz;?>'></a><span id="bz<?php echo $id;?>"><?php if (!empty($bz))echo '<font class="newdian"></font>';?></span>
</td>
<td width="140" align="center" class="C999 lineH150"><?php 
$openid_str=(!empty($openid))?'<i class="ico wxlv">&#xe607;</i>':'';
echo YmdHis($rows['regtime']);?><br><?php echo $rows['regip'];?>

  <?php
	if (ifint($tguid)){
		
		$row = $db->ROW(__TBL_TG_USER__,"uname,mob","id=".$tguid,"num");
		if ($row){
			$tguname=dataIO($row[0],'out');
			$tgmob=dataIO($row[1],'out');
			echo '<div class="tjr">';
			echo '<div class="C666">推荐人ID:'.$tguid.'</div>';
			//if(!empty($tguname))echo '<div class="C999">'.$tguname.'</div>';
			//if(!empty($tgmob))echo '<div class="C999">'.$tgmob.'</div>';
			if($tgflag==1 && $tgmoney>0){
				echo '已奖励 <font class="Cf00">'.str_replace(".00","",$tgmoney).'</font> 元';
			}
			echo '</div>';
		}
	}
    ?>
   
    
    </td>
<td width="110" align="center" >

<?php if($k==2){?>
	<?php if($shopflag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击恢复正常">锁定</a><?php }?>
    <?php if($shopflag==0){?><a href="#" class="aHUANG flag1" uid="<?php echo $id;?>" nickname="<?php echo $id;?>" title="点击审核" jstip="审核">未审</a><?php }?>
    <?php if($shopflag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击锁定后将不能登录">正常</a><?php }?>
    <?php if($shopflag==2){?><a href="#" class="aHONG flag1" uid="<?php echo $id;?>" nickname="<?php echo $id;?>"  title="点击激活" jstip="激活">未激活</a><?php }?>
    <?php if($shopflag==-2){?><a href="#" class="aHEI flag1" uid="<?php echo $id;?>" nickname="<?php echo $id;?>"  title="点击恢复正常" jstip="恢复正常">隐藏</a><?php }?>
<?php }else{ ?>
	<?php if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击恢复正常">锁定</a><?php }?>
    <?php if($flag==0){?><a href="#" class="aHUANG flag1" uid="<?php echo $id;?>" nickname="<?php echo $id;?>" title="点击审核" jstip="审核">未审</a><?php }?>
    <?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击锁定后将不能登录">正常</a><?php }?>
    <?php if($flag==2){?><a href="#" class="aHONG flag1" uid="<?php echo $id;?>" nickname="<?php echo $id;?>"  title="点击激活" jstip="激活">未激活</a><?php }?>
    <?php if($flag==-2){?><a href="#" class="aHEI flag1" uid="<?php echo $id;?>" nickname="<?php echo $id;?>"  title="点击恢复正常" jstip="恢复正常">隐藏</a><?php }?>
<?php }?>  
  <?php if(ifint($uid)){?><br><a href="<?php echo Href('u',$uid);?>" title="前台用户UID" target="_blank" style="margin-top:15px;display:block"><?php echo $uid;?></a><?php }?>
</td>
<td width="70" class="center"><a onclick="zeai.openurl('<?php echo $fHREF2;?>')" class="btn size2 BAI tips" tips-title='设置/修改资料' tips-direction='left'>修改</a></td>
</tr>

<?php } ?>
<div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
	<button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnflag" value="" class="btn size2 LV disabled action">批量审核</button>　
    <button type="button" id="btnsend2" value="" class="btn size2 disabled action"><i class="ico">&#xe676;</i> 发送消息</button>　
	<input type="hidden" name="g" value="<?php echo $g;?>" />
	<input type="hidden" name="p" value="<?php echo $p;?>" />
	<input type="hidden" name="f" value="<?php echo $f;?>" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
</div>
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
<?php if ($total > 0 ) {?>
<script>
zeai.listEach('.flag1',function(obj){
	var uid = parseInt(obj.getAttribute("uid")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		var jstip=obj.getAttribute("jstip");
		zeai.confirm('<b class="S18">确定要'+jstip+'【ID:'+decodeURIComponent(nickname)+'】么？</b><br>① '+jstip+'以后将向用户自动发送'+jstip+'成功消息通知<br>② '+jstip+'以后用户可以正常进入平台互动操作',function(){
			zeai.ajax('TG_u'+zeai.ajxext+'submitok=allflag1&list[]='+uid+'&k=<?php echo $k;?>',function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'TG_u'+zeai.ajxext+'submitok=alldel&k=<?php echo $k;?>',
		title:'批量删除（将同时删除绑定的商家和推广员）',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnflag').onclick = function() {
	allList({
		btnobj:this,
		url:'TG_u'+zeai.ajxext+'submitok=allflag1&k=<?php echo $k;?>',
		title:'批量审核',
		msg:'正在审核中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnsend').onclick = function() {sendTipFnTGU(this);}
o('btnsend2').onclick = function() {sendTipFnTGU(this);}
function sendTipFnTGU(btnobj){
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要发送的用户');
		return false;
	}
	var arr = document.getElementsByName('list[]');
	var ulist = [];
	for( key in arr){
		if (arr[key].checked){
			ulist.push(arr[key].value);
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的用户');
	}else{
		zeai.iframe('发送消息','u_tip.php?kind=TG&ifshop=1&ulist='+ulist,600,500);
	}
}
</script>
<?php }?>
<?php require_once 'bottomadm.php';?>
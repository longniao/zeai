<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';

if($ifwebshow==1){
	if(!in_array('crm_hn_work1',$QXARR))exit(noauth());
}
if($ifwebshow==-1){
	if(!in_array('crm_hn_work_1',$QXARR))exit(noauth());
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">

<style>
.tablelist{min-width:1400px;margin:20px 20px 50px 20px}
.table0{min-width:1600px;width:98%;margin:10px 20px 20px 20px}

a.pic60{padding:0;border:0}
</style>

<body>
<div class="navbox">
	<!--<a href="crm_hn_work.php"<?php echo (empty($ifwebshow))?' class="ed"':'';?>>红娘业务统计<?php if(empty($ifwebshow))echo '<b>'.$db->COUNT(__TBL_CRM_HN__,"kind='crm'").'</b>';?></a>-->
    
	<?php if($ifwebshow==1){?><a href="crm_hn_work.php?ifwebshow=1" class="ed">售前红娘业务统计<?php echo '<b>'.$db->COUNT(__TBL_CRM_HN__,"ifwebshow=1 AND kind='crm'").'</b>';?></a><?php }?>
	<?php if($ifwebshow==-1){?><a href="crm_hn_work.php?ifwebshow=-1" class="ed">售后红娘业务统计<?php echo '<b>'.$db->COUNT(__TBL_CRM_HN__,"ifwebshow<>1 AND kind='crm'").'</b>';?></a><?php }?>

  <div class="Rsobox">
    
  </div>
  
<div class="clear"></div></div>
<div class="fixedblank"></div>
<!---->
<?php
/************************************** 【列表】 list **************************************/
?>
<table class="table0">
    <tr>
    <td width="400" align="left" class="border0" >
    <form name="form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按红娘名称/编号搜索">
        <input name="ifwebshow" type="hidden" value="<?php echo $ifwebshow; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>     </td>
    <td>
    </td>
    <td width="20" align="right">&nbsp;</td>
  </tr>
    </table>
    <?php
	$SQL="";
	$Skeyword = trimm($Skeyword);
	if(!empty($Skeyword)){
		if(ifint($Skeyword)){
			$SQL = " AND id=".$Skeyword;
		}else{
			$SQL = " AND ( truename LIKE '%".dataIO($Skeyword,'in')."%' ) ";
		}
	}
	
	if($ifwebshow==1)$SQL.=" AND ifwebshow=1";
	if($ifwebshow==-1)$SQL.=" AND ifwebshow<>1";



	$NUM1 = ",(SELECT COUNT(*) FROM ".__TBL_USER__." WHERE flag=1 AND (kind=2 OR kind=3) AND hnid=HN.id) AS num1";
	$NUM2 = ",(SELECT COUNT(*) FROM ".__TBL_USER__." WHERE flag=2 AND (kind=2 OR kind=3) AND hnid=HN.id) AS num2";
	$NUM3 = ",(SELECT COUNT(*) FROM ".__TBL_USER__." WHERE grade>1 AND (kind=2 OR kind=3) AND hnid=HN.id) AS num3";
	$NUM4 = ",(SELECT COUNT(*) FROM ".__TBL_USER__." WHERE grade=1 AND (kind=2 OR kind=3) AND hnid=HN.id) AS num4";
	

	$rt = $db->query("SELECT id,uid,path_s,username,truename,roletitle,flag,title,pj_good,pj_normal,pj_bad,click,addtime,ifwebshow".$NUM1.$NUM2.$NUM3.$NUM4." FROM ".__TBL_CRM_HN__." HN WHERE kind='crm' ".$SQL."  ORDER BY px DESC LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
		?>
		
		<table class="tablelist">
		<tr>
		<th width="70" align="center">编号</th>
		<th width="80" align="left">红娘照片</th>
		<th width="150" align="left">红娘名称</th>
		<th width="100" align="left">红娘角色</th>
		<th width="120" align="center">名下会员（服务中）</th>
		<th width="120" align="center">名下会员（服务成功）</th>
		<th width="80" align="center">已签约</th>
		<th width="80" align="center">未签约</th>
		<th width="80" align="center">约见配对</th>
		<th width="80" align="center">跟进小计</th>
		<th align="center">&nbsp;</th>
		<th width="120" align="center">好评-中评-差评</th>
		<th width="60">&nbsp;</th>
		</tr>
		<?php
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$id        = $rows['id'];
			$path_s    = $rows['path_s'];
			$unum      = $rows['unum'];
			$flag      = $rows['flag'];
			$pj_good   = $rows['pj_good'];
			$pj_normal = $rows['pj_normal'];
			$pj_bad    = $rows['pj_bad'];
			$addtime   = YmdHis($rows['addtime']);
			$roletitle = dataIO($rows['roletitle'],'out');
			$truename  = dataIO($rows['truename'],'out');
			$username  = dataIO($rows['username'],'out');
			$uid       = intval($rows['uid']);
			$sex       = intval($rows['sex']);

			$num1 = $rows['num1'];
			$num2 = $rows['num2'];
			$num3 = $rows['num3'];
			$num4 = $rows['num4'];
			//
			if(!empty($Skeyword)){
				$truename = str_replace($Skeyword,'<font class="Cf00 B">'.$Skeyword.'</font>',$truename);
			}
			if(!empty($path_s)){
				$path_s_url = $_ZEAI['up2'].'/'.$path_s;
				$path_s_str = '<img src="'.$path_s_url.'">';
			}else{
				$path_s_url = '';
				$path_s_str = '';
			}
			
			//
						
			//约见配对
			$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_MATCH__." WHERE uid IN ( SELECT id FROM ".__TBL_USER__." WHERE (kind=2 OR kind=3) AND hnid=$id )");
			$roww = $db->fetch_array($rtt);
			$matchnum = $roww[0];
			
			//跟进小计
			$rtt = $db->query("SELECT COUNT(*) FROM ".__TBL_CRM_BBS__." WHERE uid IN ( SELECT id FROM ".__TBL_USER__." WHERE (kind=2 OR kind=3) AND hnid=$id )");
			$roww = $db->fetch_array($rtt);
			$bbsnum = $roww[0];

		?>
		<tr id="tr<?php echo $id;?>">
		<td width="70" height="40" align="center" class="S14"><?php echo $id;?></td>
		<td width="80" align="left" style="padding:10px 0">
        	<?php if (empty($path_s_url)){?>
          <a href="javascript:;" class="pic60 ">无图</a>
            <?php }else{ ?>
            <a href="javascript:;" class="pic60 " onClick="parent.piczoom('<?php echo getpath_smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a>
			<?php }?>
        </td>
		<td width="150" align="left" class="C999">
        <div class="S16 C000"><?php echo $truename;?></div >
        <?php if(ifint($uid)){?>
        	CRM用户名：<?php echo $username;?><br>
        	绑定前端UID：<a href="<?php echo Href('u',$uid);?>" target="_blank"><?php echo $uid;?></a>
        
        <?php }?>
        </td>
		<td width="100" align="left" class="S14"><?php echo $roletitle;?></td>
        <?php $hnhref='&hnid='.$id.'&hnname='.urlencode($truename)?>
		<td width="120" align="center">
        
        <a title="名下会员正在服务中" class="aHUI" href="crm_user.php?t=HN_user1<?php echo $hnhref;?>"><?php echo ($num1>0)?'<font class="Cf00 FArial S14">'.$num1.'</font>人':$num1;?></a>
        
        </td>
		<td width="120" align="center">
        
        <a title="名下会员正在服务中成功" class="aHUI " href="crm_user.php?t=HN_user2<?php echo $hnhref;?>"><?php echo ($num2>0)?'<font class="Cf00 FArial S14">'.$num2.'</font>人':$num2;?></a>
        
        </td>
		<td width="80" align="center"><a title="名下会员已签约" class="aHUI" href="crm_user.php?t=HN_user_vip<?php echo $hnhref;?>"><?php echo ($num3>0)?'<font class="Cf00 FArial S14">'.$num3.'</font>人':$num3;?></a></td>
		<td width="80" align="center"><a title="名下会员未签约" class="aHUI" href="crm_user.php?t=HN_user_grade1<?php echo $hnhref;?>" ><?php echo ($num4>0)?'<font class="Cf00 FArial S14">'.$num4.'</font>人':$num4;?></a></td>
		<td width="80" align="center" ><a title="名下会员约见配对" class="aHUI" href="crm_user.php?t=HN_user_macth<?php echo $hnhref;?>"><?php echo ($matchnum>0)?'<font class="Cf00 FArial S14">'.$matchnum.'</font>次':$matchnum;?></a></td>
		<td width="80" align="center" ><a title="名下会员跟进小计" class="aHUI" href="crm_user.php?t=HN_user_bbs<?php echo $hnhref;?>"><?php echo ($bbsnum>0)?'<font class="Cf00 FArial S14">'.$bbsnum.'</font>次':$bbsnum;?></a></td>
		<td align="center" >&nbsp;</td>
		<td width="120" align="center" ><a href="crm_hn_bbs.php?t=2&Skeyword=<?php echo $id;?>" title="评价" class="aHUI " ><font class="Cf00"><?php echo $pj_good;?></font> / <font class="C666"><?php echo $pj_normal;?></font> / <font class="C00f"><?php echo $pj_bad;?></font></a></td>
		<td width="60" align="left" >&nbsp;</td>
		</tr>
		<?php } ?>
		<tfoot><tr>
		<td colspan="13">
		<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label" style="display:none"><i class="i1"></i></label>　
		<input type="hidden" name="submitok" id="submitok" value="" />
		<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
		</td>
		</tr></tfoot>
		</table>

		<script>
		var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';


		</script>
		<script src="js/zeai_tablelist.js"></script>
<?php }?>




<br><br><br>
<?php require_once 'bottomadm.php';?>
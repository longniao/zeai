<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('sq_sh',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/udata.php';
switch ($submitok) {
	case"delupdate":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$rt=$db->query("SELECT pathlist FROM ".__TBL_CRM_BBS__." WHERE id=".$id);
				$total = $db->num_rows($rt);
				if ($total > 0) {
					for($i=1;$i<=$total;$i++) {
						$rows = $db->fetch_array($rt,'num');
						if(!$rows) break;
						$piclist = $rows[0];
						if (!empty($piclist)){
							$piclist = explode(',',$piclist);
							if (count($piclist) >= 1){
								foreach ($piclist as $value){
									$path_s = $value;
									$path_b = smb($path_s,'b');
									@up_send_admindel($path_s.'|'.$path_b);
								}
							}
						}
					}
					$db->query("DELETE FROM ".__TBL_CRM_BBS__." WHERE id=".$id);
					AddLog('售前【审核跟进反馈记录】删除->id:'.$id);
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'操作成功'));
	break;
}
$SQL = "";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL .= " AND (U.id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL .= " AND ( ( U.uname LIKE '%".$Skeyword."%' ) OR ( U.mob LIKE '%".$Skeyword."%' )  OR ( U.truename LIKE '%".$Skeyword."%' ) OR ( U.nickname LIKE '%".$Skeyword."%' ) OR ( U.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}
if ($dataflag==2){
	$SQL .= " AND B.sqflag=2 ";
}else{
	$SQL .= " AND B.sqflag=1 ";
}
$fieldlist = "U.uname,U.truename,U.nickname,U.photo_s,U.photo_f,U.sex,U.grade,U.myinfobfb,B.id,B.uid,B.admid,B.admname,B.content,B.sqflag,B.addtime,B.pathlist,B.addtime";
switch ($sort) {
	case 'myinfobfb0':$SORT = " ORDER BY U.myinfobfb,B.id DESC ";break;
	case 'myinfobfb1':$SORT = " ORDER BY U.myinfobfb DESC,B.id DESC ";break;
	default:$SORT = " ORDER BY B.id DESC ";break;
}
$rt = $db->query("SELECT ".$fieldlist." FROM ".__TBL_USER__." U,".__TBL_CRM_BBS__." B WHERE U.id=B.uid ".$SQL."  ".$SORT);
$total = $db->num_rows($rt);
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
.tablelist{min-width:1200px;margin:20px 20px 50px 20px}
.pathlist img{margin:8px 5px 5px 2px;width:30px;height:30px;object-fit:cover;-webkit-object-fit:cover;border-radius:2px}
</style>
<body>
<div class="navbox">
    <a href="u_jb_list_sq_view.php?dataflag=1"<?php echo (empty($dataflag) || $dataflag==1)?' class="ed"':'';?>>售前审核反馈查看（已审）<?php if(empty($dataflag) || $dataflag==1)echo '<b>'.$total.'</b>';?></a>
    <a href="u_jb_list_sq_view.php?dataflag=2"<?php echo ($dataflag==2)?' class="ed"':'';?>>售前审核反馈查看（驳回）<?php if($dataflag==2)echo '<b>'.$total.'</b>';?></a>
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
<?php $sorthref = SELF."?dataflag=$dataflag&p=$p&sort=";
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70" align="left">ID</th>
    <th width="90" align="center">头像</th>
    <th width="130">用户UID/昵称</th>
    <th width="10">&nbsp;</th>
	<th width="80" align="center" >资料完整度
<div class="sort">
	<a href="<?php echo $sorthref."myinfobfb0";?>" <?php echo($sort == 'myinfobfb0')?' class="ed"':''; ?>></a>
	<a href="<?php echo $sorthref."myinfobfb1";?>" <?php echo($sort == 'myinfobfb1')?' class="ed"':''; ?>></a>
</div></th>
	<th width="100" align="center" >审核动作</th>
	<th align="left" >跟进反馈内容</th>
	<th width="120" align="center" >审核跟进</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];
		$uid = $rows['uid'];
		$admid = $rows['admid'];
		$admname = dataIO($rows['admname'],'out');
		$content = dataIO($rows['content'],'out');
		$sqflag = $rows['sqflag'];
		$pathlist = $rows['pathlist'];
		//
		$flag  = $rows['flag'];
		$uname = strip_tags($rows['uname']);
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$nickname = trimhtml(dataIO($rows['nickname'],'out'));
		$aboutus = dataIO($rows['aboutus'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex   = $rows['sex'];
		$grade = $rows['grade'];
		//
		if(empty($rows['nickname'])){$nickname = $uname;}
		$addtime = YmdHis($rows['addtime'],'YmdHi');
		$photo_s = $rows['photo_s'];
		$photo_f = $rows['photo_f'];
		$truename = dataIO($rows['truename'],'out');
		$truename = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$truename);
		$myinfobfb = $rows['myinfobfb'];
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}else{
			$photo_s_url = HOST.'/res/photo_s'.$sex.'.png';
			$photo_s_str = '<img src="'.$photo_s_url.'" class="photo_s">';
		}
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
      </td>
      <td width="70" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="90" align="center" style="padding:15px 0"><a href="javascript:;" photo_s_url="<?php echo $photo_s_url;?>" class="homepage" uid="<?php echo $uid;?>" title2="<?php echo urlencode(trimhtml($nickname.'／'.$uid));?>"><?php echo $photo_s_str; ?></a></td>
      <td width="130" align="left" class="S12">
        <?php
		echo uicon($sex.$grade).$uid;
		echo '<span style="vertical-align:middle">';
		echo '<font class="uleft">'.$nickname.'</font>';
		echo '</span>';
		?>
	</td>
    <td width="10" align="left" class="C999 lineH200" style="padding:10px 0">&nbsp;</td>
    <td width="80" align="center" class="S18"><span class="<?php if($myinfobfb >80){echo ' myinfobfb2';}elseif($myinfobfb >20){echo ' myinfobfb1';}else{echo ' myinfobfb0';}?>">
	    <?php echo $myinfobfb;?>%
      </span></td>
    <td width="100" align="center">
	<?php 
    $str1 = "<i class='ico S18' style='color:#45C01A' title='审核通过'>&#xe60d;</i>";
    $str0 = "<i class='ico S18 C999' title='审核驳回'>&#xe62c;</i>";
    if($sqflag==1){
		echo $str1;
    }elseif($sqflag==2){
		echo $str0;
    }
    ?>
    </td>
    <td align="left" class="padding10 S14">
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
    <td width="120" align="center"class=" lineH200">
	<?php
	if(ifint($admid)){
		echo $admname.'<font class="C999">（ID:'.$admid.'）</font>';
		echo '<br><font class="C999">'.$addtime.'</font>';
	}?>
	</td>
    </tr>
	<?php }?>
    
    <div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);">发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
    </div>
    
</table>
</form>
<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
zeai.listEach('.homepage',function(obj){
	obj.onclick = function(){var uid = parseInt(obj.getAttribute("uid")),title2 = obj.getAttribute("title2"),
	photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
	photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
	zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(title2)+'】个人主页','crm_user_detail.php?t=2&iframenav=1&uid='+uid);}
});
if(!zeai.empty(o('btndellist')))o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'u_jb_list_sq_view'+zeai.ajxext+'submitok=delupdate',
		title:'批量删除',
		msg:'批量删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
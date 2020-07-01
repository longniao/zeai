<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('dating',$QXARR))exit(noauth());
switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$id=intval($v);
				$row = $db->ROW(__TBL_DATING__,"id","id=".$id);
				if ($row){
					$db->query("DELETE FROM ".__TBL_DATING_USER__." WHERE fid=".$id);
					$db->query("DELETE FROM ".__TBL_DATING__." WHERE  id=".$id);
				}
				AddLog('【约会审核】删除约会->id:'.$id);
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;

	case"flag1":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);
				
				if ($row = $db->ROW(__TBL_DATING__,"uid,title","id=".$id)){
					$uid = intval($row[0]);$title = dataIO($row[1],'out');
				}else{exit(JSON_ERROR);}
				
				$row = $db->ROW(__TBL_USER__,"nickname,openid,subscribe","id=".$uid,"num");
				if ($row){
					$data_nickname = dataIO($row[0],'out');$data_openid = $row[1];$data_subscribe = $row[2];
					$db->query("UPDATE ".__TBL_DATING__." SET flag=1 WHERE id=".$id);
					
					AddLog('【约会审核】审核通过->id:'.$id);
					
					$URL_pc  = Href('dating',$id);
					$URL_mob = mHref('dating',$id);
					
					//【站内】消息
					$C = $data_nickname.'您好，您发布的约会【'.$title.'】审核成功！　　<a href='.$URL_pc.' class=aHUI>查看约会</a>';
					$db->SendTip($uid,'您发布的约会【'.$title.'】审核成功',dataIO($C,'in'),'sys');

					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$first    = urlencode($data_nickname.'您好，您发布的约会【'.$title.'】审核成功！');
						$keyword1 = urlencode('约会审核通过');
						$keyword2 = urlencode('约会符合规范');
						$url      = urlencode($URL_mob);
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					//发布动态
					$content = $data_nickname.'发布了约会活动<br><a href='.$URL_pc.' class=aQING>'.$title.'</a>';
					$db->query("INSERT INTO ".__TBL_TREND__." (uid,content,addtime) VALUES ($uid,'$content',".ADDTIME.")");
					
					//给他粉丝【站内】推送
					$tip_title   = '您关注的好友【'.$data_nickname.'】发布了约会活动【'.$title.'】';
					$tip_content = $tip_title.'　　<a href="'.$URL_pc.'" class="aQING" target="_blank">进入查看</a>';
					@push_friend_tip($uid,$tip_title,$tip_content);
					
					//给他粉丝微信推送
					$CARR = array();
					$CARR['url']      = urlencode($URL_mob);
					$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
					$CARR['contentMB']= urlencode($tip_title);
					@push_friend_wx($uid,$CARR);
				}

			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
	case"modflag":
		if (!ifint($classid))callmsg("forbidden","-1");
		$rt = $db->query("SELECT flag FROM ".__TBL_DATING__." WHERE id=".$classid);
		if($db->num_rows($rt)){
			$rows = $db->fetch_array($rt);
			$flag = $rows['flag'];
			switch($flag){
				case"-1":$SQL="flag=1";break;
				//case"0":$SQL="flag=1";break;
				case"1":$SQL="flag=-1";break;
				case"2":$SQL="flag=-1";break;
			}
			$db->query("UPDATE ".__TBL_DATING__." SET ".$SQL." WHERE id='$classid'");
			AddLog('【约会审核】修改约会状态 ->id:'.$classid);
			header("Location: ".SELF."?p=$p");
		}else{
			callmsg("您要操作的信息不存在或已经删除！","-1");
		}
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
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">

<style>
.tablelist{min-width:1200px;margin:20px 20px 50px 20px}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}


.tablelist .li{font-size:14px;line-height:200%}
.tablelist .li img{max-width:50px;max-height:50px;margin-left:5px}

.tablelist .li2 li{width:50px;height:50px;float:left;margin:10px;position:relative}
.tablelist .li2 li img{width:50px;height:50px;border-radius:2px;cursor:zoom-in;display:block}

.delico{;top:-10px;right:-10px;position:absolute;width:20px;height:20px;display:inline-block;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3);background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.delico:hover{background-position:-100px top;cursor:pointer}

.tablelist .sx{padding:15px 0 0 15px}
.table0{min-width:1600px;width:98%;margin:10px 20px 20px 20px}


.tablelist .ico{display:inline-block;color:#E83191;background-color:#E83191;color:#fff;border-radius:50px;width:26px;height:26px;line-height:26px;text-align:center}
.timestyle{display:inline-block;font-size:12px;margin:0 4px;color:#fff;border-radius:3px;padding:0 6px;height:18px;line-height:18px;text-align:center;background-color:#A7CAB2}
</style>
<?php
?>
<body>
<div class="navbox">

    <a href="<?php echo SELF; ?>?t=1"<?php echo ( $t==1 || empty($t) )?" class='ed'":""; ?>>约会管理<?php echo '<b>'.$db->COUNT(__TBL_DATING__).'</b>';?></a>
    <a href="dating_user.php"<?php echo ($t == 'pic')?' class="ed"':''; ?>>约会名单/联系方式</a>
    
    <div class="Rsobox">
    <form name="form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按约会标题搜索">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>     
    </div>
  
<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php


$tmpsql="";
$Skeyword = trimm($Skeyword);
if (!empty($Skeyword)){
	$tmpsql = " AND a.title LIKE '%".dataIO($Skeyword,'in')."%' ";
}
$rt = $db->query("SELECT a.id,a.uid,a.title,a.yhtime,a.bmnum,a.click,a.flag,a.addtime,b.nickname,b.grade,b.sex,b.uname,b.mob,b.photo_s,b.photo_f FROM ".__TBL_DATING__." a,".__TBL_USER__." b WHERE a.uid=b.id ".$tmpsql." ORDER BY a.id DESC LIMIT ".$_ADM['admLimit']);
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
    <th width="70" align="left">ID</th>
    <th align="left">发布人</th>
    <th width="150" align="left">&nbsp;</th>
    <th>约会主题</th>
	<th width="200" align="center" >约会时间</th>
	<th width="80" align="center" >发布时间</th>
    <th width="100" align="center">报名 / 围观数</th>
    <th width="80" align="center">状态</th>
    <th width="60" align="center">修改约会</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$title = dataIO($rows['title'],'out');
		$title = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$title);
		$addtime = YmdHis($rows['addtime']);
		$flag = $rows['flag'];
		//
		$uid = $rows['uid'];
		$nickname  = dataIO($rows['nickname'],'out');
		$photo_s   = $rows['photo_s'];
		$photo_f   = $rows['photo_f'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$uname   = $rows['uname'];
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'">';
		}else{
			$photo_s_str = '';
		}
		$photo_fstr   = ($photo_f == 0 && !empty($photo_s))?'<span>审核中</span>':'';
		$href         = Href('u',$uid);
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
        <td width="70" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="70" align="left" style="padding:10px 0">
        	<a href="<?php echo $href; ?>" class="noU58 yuan sex<?php echo $sex; ?>" target="_blank"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
        </td>
      <td width="150" align="left" class="S12">
      
   <a href="<?php echo Href('u',$uid);?>" target="_blank">
  <?php echo uicon($sex.$grade); ?>
  <?php echo '<font class="S14">'.$uname.'</font>';?>
  <font class="uleft">
  <?php
  echo 'UID：'.$uid."</br>";
  echo $nickname;
  ?>
  </font>
  </a>
  

    </td>
    <td align="left">
		
        
<a href="<?php echo Href('dating',$rows['id']); ?>" target="_blank" class="S14"><i class="ico">&#xe653;</i> <?php echo str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",htmlout(stripslashes($rows['title']))); ?></a>        
        
    </td>
    <td width="200" align="center" class="C999 lineH200"><?php echo YmdHis($rows['yhtime'],'YmdHi').' '.getweek(YmdHis($rows['yhtime'],'Ymd'));?><br />
  <?php
$d1  = strtotime("now");
$d2  = $rows['yhtime'];
$totals  = ($d2-$d1);
$day     = intval( $totals/86400 );
$hour    = intval(($totals % 86400)/3600);
$hourmod = ($totals % 86400)/3600 - $hour;
$minute  = intval($hourmod*60);
if ($rows['flag'] == 2)$totals = -1;
if (($totals) > 0) {
	if ($day > 0){
		$outtime = "还剩<span class=timestyle>$day</span>天";
	} else {
		$outtime = "还剩";
	}
	$outtime .= "<span class=timestyle>$hour</span>小时<span class=timestyle>$minute</span>分钟";
} else {
	$outtime = "<font color=#999999><b>已经结束</b></font>";
}
echo '<font color=#666666>'.$outtime.'</font>';
?></td>
    <td width="80" align="center" class="C999"><?php echo $addtime;?></td>
	<td width="100" align="center" class="C999"><font color="#FF0000"><?php echo $rows['bmnum']; ?></font> / <font color="#FF0000"><?php echo $rows['click']; ?></font></td>
      <td width="80" align="center">
<?php
$fHREF = SELF."?submitok=modflag&classid=$id&t=$t&p=$p";
if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击显示">隐藏</a><?php }?>
<?php if($flag==0){?><a href="javascript:;" class="flag1 aHUANG" title="点击审核" clsid="<?php echo $id;?>">未审</a><?php }?>
<?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击隐藏">正常</a><?php }?>
<?php if($flag==2){?><a href="<?php echo $fHREF;?>" class="aHUI" title="点击隐藏">结束</a><?php }?>
      </td>
      <td width="60" align="center">
      <a class="edit tips" tips-title='约会修改' tips-direction='left' id="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>">✎</a>
      </td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="10">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnflaglist" class="btn size2 LV disabled action">批量审核</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);">发送消息</button>
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

o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'dating'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'批量删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnflaglist').onclick = function() {
	allList({
		btnobj:this,
		url:'dating'+zeai.ajxext+'submitok=flag1',
		title:'批量审核',
		content:'<br>此审核将同步批量发送所有粉丝消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。',
		msg:'审核处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}

zeai.listEach('.flag1',function(obj){
	var id = parseInt(obj.getAttribute("clsid")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.confirm('确认要审核么？<br>此审核将同步批量发送所有粉丝消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。',function(){
			zeai.msg('正在审核/推送粉丝消息',{time:300});
			zeai.ajax('dating'+zeai.ajxext+'submitok=flag1&list[]='+id,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});

zeai.listEach('.edit',function(obj){
	var id = parseInt(obj.getAttribute("id")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.iframe('修改【'+decodeURIComponent(nickname)+'】约会','dating_mod.php?fid='+id,600,500);
	}
});



</script>
<script src="js/zeai_tablelist.js"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('trend',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
switch ($submitok) {
	case"dataflag1":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);$uid=$id;
				$db->query("UPDATE ".__TBL_TREND__." SET flag=1 WHERE id=".$id);
				AddLog('【交友圈审核】主题审核通过->id:'.$id);
			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
	case"modflag":
		if (!ifint($classid))callmsg("forbidden","-1");
		$rt = $db->query("SELECT flag FROM ".__TBL_TREND__." WHERE id=".$classid);
		if($db->num_rows($rt)){
			$rows = $db->fetch_array($rt);
			$flag = $rows['flag'];
			switch($flag){
				case"-1":$SQL="flag=1";break;
				case"0":$SQL="flag=1";break;
				case"1":$SQL="flag=-1";break;
			}
			$db->query("UPDATE ".__TBL_TREND__." SET ".$SQL." WHERE id='$classid'");
			AddLog('【交友圈审核】主题状态修改->id:'.$id);
			header("Location: ".SELF."?p=$p");
		}else{
			callmsg("您要操作的信息不存在或已经删除！","-1");
		}
	break;
	case"delpic":
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(str_len($picurl) < 20)json_exit(array('flag'=>0,'msg'=>'图片地址错误'));
		$rt=$db->query("SELECT piclist FROM ".__TBL_TREND__." WHERE id=".$id);
		$total = $db->num_rows($rt);
		if ($total > 0) {
			$row = $db->fetch_array($rt,'num');
			$arr    = explode(',',$row[0]);
			$newarr = array();
			if (count($arr) >= 1){
				foreach ($arr as $v){
					if ($picurl == $v){
						$path_s = $v;
						$path_b = getpath_smb($path_s,'b');
						@up_send_admindel($path_s.'|'.$path_b);
					}else{
						array_push($newarr,$v);
					}
				}
			}
			$newlist = (count($newarr) > 0 && !empty($newarr))?implode(",",$newarr):'';
			$db->query("UPDATE ".__TBL_TREND__." SET piclist='$newlist' WHERE id=".$id);
			AddLog('【交友圈审核】主题图片删除->id:'.$id);
			json_exit(array('flag'=>1,'msg'=>'删除成功'));
		}
	break;
	case"delupdate":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);
				$rt=$db->query("SELECT piclist FROM ".__TBL_TREND__." WHERE id=".$id);
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
									$path_b = getpath_smb($path_s,'b');
									@up_send_admindel($path_s.'|'.$path_b);
								}
							}
						}
					}
					$db->query("DELETE FROM ".__TBL_TREND__." WHERE id=".$id);
					AddLog('【交友圈审核】主题删除->id:'.$id);
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ajax_clear":
		$rt=$db->query("SELECT id,content FROM ".__TBL_TREND__);
		$total = $db->num_rows($rt);
		if ($total > 0) {
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$id      = $rows['id'];
				$content = dataIO($rows['content'],'out');
				if(strstr($content,"photo_v") || strstr($content,"photo_m")){
					preg_match('/<img.+src=\"?(.+\.(jpg))\"?.+>/i',$content,$match);
					$tmp_src = str_replace(HOST.'/',ZEAI,$match[1]);
					if(!file_exists($tmp_src)){
						$db->query("DELETE FROM ".__TBL_TREND__." WHERE id=".$id);
						AddLog('【交友圈审核】清空无效图片->id:'.$id);
					}
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'清理成功'));
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
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">

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

.tablelist .sx{padding:5px 0 0 15px}
.table0{min-width:1600px;width:98%;margin:10px 20px 20px 20px}

</style>
<?php
?>
<body>
<div class="navbox">

    <a href="trend.php" <?php echo (empty($t))?' class="ed"':''; ?>>交友圈管理<?php echo '<b>'.$db->COUNT(__TBL_TREND__).'</b>';?></a>
    <a href="trend.php?t=pic"<?php echo ($t == 'pic')?' class="ed"':''; ?>>交友圈图片</a>
    <a href="trend_bbs.php">交友圈评论</a>
    
    <div class="Rsobox"></div>
  
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0" style="min-width:980px">
    <tr>
    <td width="260" align="left" class="S14"><form name="form2" method="get" action="<?php echo $SELF; ?>">
      <input name="kuid" type="text" id="kuid" size="15" maxlength="8" class="input size2" placeholder="按会员UID筛选">
      <input type="submit" value="搜索" class="btn size2" />
      <input type="hidden" name="t" value="<?php echo $t; ?>" />
    </form></td>
    <td align="center" class="S14"><button type="button" class="btn size2" id="clearold">一键清理无效图片</button></td>
    <td width="260" align="right"><form name="form1" method="get" action="<?php echo $SELF; ?>">
      <input name="Skeyword" type="text" id="Skeyword" size="25" maxlength="25" class="W200 input size2" placeholder="按内容搜索">
      <input type="submit" value="搜索" class="btn size2" />
      <input type="hidden" name="t" value="<?php echo $t; ?>" />
    </form></td>
    </tr>
    </table>  
<?php
$SQL = "";
if (!empty($Skeyword))$SQL .= " AND ( a.content LIKE '%".dataIO(trimm($Skeyword),'in')."%' ) ";
if ($t == 'pic')$SQL   .= " AND a.piclist<>'' ";
if (ifint($kuid))$SQL .= " AND b.id =".$kuid;
$rt = $db->query("SELECT a.*,b.uname,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f FROM ".__TBL_TREND__." a,".__TBL_USER__." b WHERE a.uid=b.id ".$SQL." ORDER BY a.id DESC LIMIT ".$_ADM['admLimit']);
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
    <th width="180" align="left">&nbsp;</th>
    <th width="130" align="left">筛选此会员</th>
    <th>内容</th>
	<th width="50" align="center" >点赞数	</th>
    <th width="80" align="center">发布时间</th>
    <th width="270" align="center">图片</th>
    <th width="80" align="center">状态</th>
    <th width="60" align="center">修改</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id  = $rows['id'];
		$content = dataIO($rows['content'],'out');
		$content = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$content);
		$addtime = YmdHis($rows['addtime']);
		$flag = $rows['flag'];
        $piclist = $rows['piclist'];
		$agreenum      = $rows['agreenum'];
		//
		$uid = $rows['uid'];
		$nickname  = dataIO($rows['nickname'],'out');
		$photo_s   = $rows['photo_s'];
		$uname   = $rows['uname'];
		$photo_f   = $rows['photo_f'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
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
        <td width="70" align="left">
        	<a href="<?php echo $href; ?>" class="noU58 yuan sex<?php echo $sex; ?>" target="_blank"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
        </td>
      <td width="180" align="left" class="S12" style="padding:10px 0">
      
	  
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
      <td width="130" align="left" class="S12" style="padding:10px 0"><a href="<?php echo SELF; ?>?kuid=<?php echo $uid; ?>&t=<?php echo $t; ?>" class="aHUI">筛选此会员</a></td>
    <td align="left" class="li">
		<span id="trend<?php echo $id; ?>">● <?php
	$preg = "/<script[\s\S]*?<\/script>/i";$content = preg_replace($preg,"",$content,3); 	
		
		 echo $content; ?></span>
    </td>
    <td width="50" align="center"><?php echo $agreenum; ?></td>
	<td width="80" align="center" class="C999"><?php echo $addtime;?></td>
	<td width="270" align="center " class="li2"><?php
if (!empty($piclist)){
	$piclist = explode(',',$piclist);
	if (count($piclist) >= 1){foreach ($piclist as $value){
		$path_s = $_ZEAI['up2'].'/'.$value;
		$path_b = getpath_smb($path_s,'b');
		?>
        <li>
            <img onClick="parent.piczoom('<?php echo $path_b; ?>')" src="<?php echo $path_s; ?>" title="放大">
            <a href="javascript:;" class="delico" title="删除" picurl="<?php echo $value; ?>" clsid="<?php echo $id; ?>"></a>
        </li>
        <?php
		}
	}
}
?></td>
      <td width="80" align="center">
<?php
$fHREF = SELF."?submitok=modflag&classid=$id&t=$t&p=$p";
if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击显示">隐藏</a><?php }?>
<?php if($flag==0){?><a href="<?php echo $fHREF;?>" class="aHUANG" title="点击审核">未审</a><div class="C999" style="margin-top:6px">点击审核</div><?php }?>
<?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击隐藏">正常</a><?php }?>
      </td>
      <td width="60" align="center">
      <a class="editico tips" tips-title='交友圈修改' tips-direction='left' id="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>"></a>
      </td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="11">
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
		url:'trend'+zeai.ajxext+'submitok=delupdate',
		title:'批量删除',
		msg:'批量删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnflaglist').onclick = function() {
	allList({
		btnobj:this,
		url:'trend'+zeai.ajxext+'submitok=dataflag1',
		title:'批量审核',
		content:'',/*<br>此审核将同步批量发送所有粉丝消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。*/
		msg:'审核处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.editico',function(obj){
	var id = parseInt(obj.getAttribute("id")),nickname=obj.getAttribute("nickname");
	obj.onclick = function(){
		zeai.iframe('修改【'+decodeURIComponent(nickname)+'】交友圈','trend_mod.php?classid='+id,500,300);
	}
});

zeai.listEach('.delico',function(obj){
	var id = obj.getAttribute("clsid");
	var picurl = obj.getAttribute("picurl");
	obj.onclick = function(){
		zeai.confirm('确认要删除么？',function(){
			zeai.ajax({url:'trend'+zeai.extname,data:{submitok:'delpic',id:id,picurl:picurl}},function(e){rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		});
	}
});


clearold.onclick=function(){
	zeai.confirm('确认清理么？确认后，将删除对应的交友圈主题信息',function(){
		zeai.ajax('trend'+zeai.ajxext+'submitok=ajax_clear',function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.alert(rs.msg);
			if (rs.flag == 1){setTimeout(function(){zeai.msg(rs.msg);location.reload(true);},1000);}
		});
	});
}

</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
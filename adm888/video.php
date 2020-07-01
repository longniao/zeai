<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('video',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$id=intval($v);
				$row = $db->ROW(__TBL_VIDEO__,"path_s,addtime,uid","id=".$id);
				if ($row){
					$path_s = $row[0];$addtime = $row[1];$path_b = getpath_smb($path_s,'b');$uid = $row[2];
					if ((ADDTIME - $addtime) > 300){
						$path_b = str_replace('.jpg','.mp4',$path_s);
						@up_send_admindel($path_s.'|'.$path_b);
						$db->query("DELETE FROM ".__TBL_VIDEO__." WHERE id=".$id);
						//
						$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
						AddLog('【视频审核】会员【'.$nickname.'（uid:'.$uid.'）】->视频驳回并删除');
					}
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"allflag1":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$id=intval($v);
				
				if ($row = $db->ROW(__TBL_VIDEO__,"uid,path_s","id=".$id)){
					$uid = intval($row[0]);$path_s = $row[1];
					$path_b = $path_s;;
				}else{exit(JSON_ERROR);}
								
				$row = $db->ROW(__TBL_USER__,"nickname,openid,subscribe","id=".$uid,"num");
				if ($row){
					$data_nickname = dataIO($row[0],'out');$data_openid = $row[1];$data_subscribe = $row[2];
					$db->query("UPDATE ".__TBL_VIDEO__." SET flag=1 WHERE id=".$id);
					
					AddLog('【视频审核】会员【'.$data_nickname.'（uid:'.$uid.'）】->视频审核通过');
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode('审核通过');
						$keyword2 = urlencode('个人视频符合规范');
						$url      = urlencode(mHref('my_video'));
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					//发布动态
					$content = '上传了个人视频<img src="'.$_ZEAI['up2'].'/'.$path_b.'" class="photo_v">';
					$db->query("INSERT INTO ".__TBL_TREND__." (uid,content,addtime) VALUES ($uid,'$content',".ADDTIME.")");
					//给他粉丝站内推送
					//$tip_title   = '您关注的好友【'.$data_nickname.'】上传了新视频';
					//$tip_content = $data_nickname.'上传了新视频'.'　　<a href="'.mHref('video').'" class="aHUI">进入查看</a>';
					//@push_friend_tip($uid,$tip_title,$tip_content);
					//给他粉丝微信推送
//					$CARR = array();
//					$CARR['url']      = urlencode(mHref('u',$uid));
//					$CARR['videourl'] = str_replace('.jpg','.mp4',$path_b);
//					$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
//					$CARR['contentMB']= urlencode($tip_title);
//					@push_friend_wx($uid,$CARR);
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
	case"loveb":
		if (!ifint($id) || !ifint($num))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$awardnum = intval(abs($num));
		if ($row = $db->ROW(__TBL_VIDEO__,"uid,path_s","id=".$id)){
			$uid = intval($row[0]);$path_s = $row[1];
			$path_b = $path_s;
		}else{exit(JSON_ERROR);}
		//
		$row = $db->NUM($uid,"loveb,nickname,openid,subscribe");
		if(!$row)json_exit(array('flag'=>0,'msg'=>'会员不存在'));
		
		$data_loveb = $row[0];$data_nickname = dataIO($row[1],'out');$data_openid = $row[2];$data_subscribe = $row[3];
		$db->query("UPDATE ".__TBL_VIDEO__." SET flag=1 WHERE id=".$id);
		
		AddLog('【视频审核】会员【'.$data_nickname.'（uid:'.$uid.'）】->视频审核通过，奖励'.$_ZEAI['loveB'].$awardnum.'个');
		//入库入清单
		$endnum  = $data_loveb + $awardnum;
		$db->query("UPDATE ".__TBL_USER__." SET loveb=$endnum WHERE id=".$uid);
		$db->AddLovebRmbList($uid,'上传视频',$awardnum,'loveb',12);
		
		//微信模版
		if (!empty($data_openid) && $data_subscribe==1){
			//审核通过提醒
			$keyword1 = urlencode('审核通过');
			$keyword2 = urlencode('个人视频符合规范');
			$url      = urlencode(mHref('my_video'));
			@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
			//爱豆到账提醒
			$F = urlencode($data_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
			$C = urlencode('上传视频');
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$awardnum.'&first='.$F.'&content='.$C.'&url='.urlencode(mHref('loveb')));
		}

		//发布动态
		$content = '上传了个人视频<img src="'.$_ZEAI['up2'].'/'.$path_b.'" class="photo_v">';
		$db->query("INSERT INTO ".__TBL_TREND__." (uid,content,addtime) VALUES ($uid,'$content',".ADDTIME.")");
		//给他粉丝站内推送
		//$tip_title   = '您关注的好友【'.$data_nickname.'】上传了新视频';
		//$tip_content = $data_nickname.'上传了新视频'.'　　<a href="'.Href('u',$uid).'" class="aQING">进入查看</a>';
		//@push_friend_tip($uid,$tip_title,$tip_content);
		//给他粉丝微信推送
//		$CARR = array();
//		$CARR['url']      = urlencode(mHref('u',$uid));
//		$CARR['videourl'] = str_replace('.jpg','.mp4',$path_b);
//		$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
//		$CARR['contentMB']= urlencode($tip_title);
//		@push_friend_wx($uid,$CARR);
		//
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
}
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
.tablelist{min-width:900px;margin:0 20px 50px 20px}
.tablelist td:hover{background-color:#fff}
/*picadd*/
.picli{width:99%;margin:10px auto;display:-webkit-flex;display:flex;-webkit-flex-wrap:wrap;flex-wrap:wrap;-webkit-justify-content:space-between;justify-content:initial}
.picli li{width:130px;height:190px;line-height:100px;margin:10px 40px 30px 13px;background-color:#fff;position:relative;text-align:center;box-shadow:0 0 10px rgba(0,0,0,0.2);}
.picli li.flag0{background-color:#ffa}
.picli .add,.picli li .del{background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.picli li:hover{background-color:#F2F9FD}
.picli li img{vertical-align:middle;max-width:100px;max-height:100px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;}
.picli li:hover .img{border:#fff 1px solid;cursor:zoom-in}
.picli li .del{width:20px;height:20px;top:-10px;right:-10px;position:absolute;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3)}
.picli li .del:hover{background-position:-100px top;cursor:pointer}
.picli li a.pic{display:block;padding:5px 0}
.picli li .f0{background-color:#ffc}
.picli li .addtime,
.picli li .nickname,
.picli li .loveb,
.picli li .chekbox{line-height:24px;height:24px;font-size:12px;color:#aaa;border-top:#eee 1px solid;font-family:'Verdana';overflow:hidden}
.picli li .addtime{font-family:'Microsoft YaHei','宋体'}
.picli li .chekbox{padding:0 5px 2px 5px}
.picli li .loveb{line-height:30px}
.picli li .chekbox .l{float:left}
.picli li .chekbox .l a{margin:0 20px 0 0}
.picli li .chekbox .l a:hover{filter:alpha(opacity=60);-moz-opacity:0.6;opacity:0.6}
.picli li .chekbox .r{float:right}
.picli li a span{display:block;width:100%;line-height:24px;position:absolute;top:28px;background-color:rgba(0,0,0,0.5);color:#aaa;font-size:12px}
.picli li .flagstr{width:30px;line-height:20px;color:#fff;font-size:12px;position:absolute;top:6px;left:-2px;background-color:#f70}
.picli li .playico{width:50px;height:50px;position:absolute;left:40px;top:5px;filter:alpha(opacity=60);-moz-opacity:0.6;opacity:0.6}
.picli li:hover .playico{width:46px;height:46px;left:41px;top:6px;filter:alpha(opacity=100);-moz-opacity:1;opacity:1;cursor:zoom-in}
.picli li:hover .playico img{width:46px;height:46px}
.picli li .chekbox .l a i{font-size:18px;color:#ccc}
.picli li:hover .chekbox .l a i:hover{color:#009688}
</style>
<body>
<div class="navbox">
    <a class="ed">会员视频<?php echo '<b>'.$db->COUNT(__TBL_VIDEO__).'</b>';?></a>
    <div class="Rsobox">
    <form name="ZEAI_CN__form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按UID/用户名/昵称" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>    
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
$fieldlist = "a.*,b.uname,b.nickname,b.sex,b.grade,b.mob,b.photo_s";
if (!empty($Skeyword))$SQL = " (( b.truename LIKE '%".trimm($Skeyword)."%' ) OR ( b.mob LIKE '%".trimm($Skeyword)."%' ) OR ( b.id LIKE '%".trimm($Skeyword)."%' ) OR ( b.uname LIKE '%".trimm($Skeyword)."%' ) OR ( b.nickname LIKE '%".trimm($Skeyword)."%' ) OR ( b.nickname LIKE '%".urlencode($Skeyword)."%' )) AND ";
if (ifint($memberid))$SQL = "a.uid='$memberid' AND ";
$rt = $db->query("SELECT ".$fieldlist." FROM ".__TBL_VIDEO__." a ,".__TBL_USER__." b WHERE ".$SQL." a.uid=b.id ORDER BY a.flag,a.id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=27;require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo $SELF; ?>">
    <table class="tablelist">
    <tr style="display:none">
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i><b class="W50">全选</b></label></th>
    <th>&nbsp;</th>
    </tr>
    <tr>
    <td colspan="2">
<!---->
    <div class="picli">
		<?php
        for($i=1;$i<=$pagesize;$i++) {
            $rows      = $db->fetch_array($rt);if(!$rows) break;
            $id        = $rows['id'];
            $uid       = $rows['uid'];
            $path_s    = $rows['path_s'];
            $flag      = $rows['flag'];
            $addtime   = $rows['addtime']; $addtime2 = $addtime;
			$nickname  = strip_tags(urldecode($rows['nickname']));
			$sex       = $rows['sex'];
			$grade     = $rows['grade'];
            $photo_s   = $rows['photo_s'];
			//
			if(empty($rows['nickname'])){
				if(empty($rows['uname'])){
					$title = $rows['mob'];
				}else{
					$title = $rows['uname'];
				}
			}else{
				$title=str_replace(" ","",$nickname);
				$title=str_replace("'","",$title);
			}
			$title = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$title);
			$_s    = $_ZEAI['up2'].'/'.$path_s;
			$dst_b = str_replace('.jpg','.mp4',$_s);
			if ((ADDTIME - $addtime) > 300){
				$dst_s = $_s;
				$ifdel = true;
			}else{
				$dst_s = 'images/videomaking.gif';	
				$ifdel = false;
			}
			//
			$addtime   = YmdHis($addtime);
			$flagbg   = ($flag == 'W W w .Z　e a i　.C n')?' class="flag0"':'';
			$flagstr  = ($flag == 0)?'<div class="flagstr">未审</div>':'';
			$href     = Href('u',$uid);
    	?>
        <li id="tr<?php echo $id;?>" class="fadeInUp"<?php echo ($flag == 0)?' style="height:220px"':''; ?>>
        	<a href="javascript:;" onClick="parent.piczoom('<?php echo $dst_b; ?>')" class="pic<?php if ($flag == 0)echo ' f0'; ?>"><img src="<?php echo $dst_s; ?>" class="img"></a>
        	<?php if ($ifdel){ ?>
            <a href="javascript:;" class="del" pid="<?php echo $id;?>"></a>
            <?php }?>
            <?php echo $flagstr; ?>
            <div class="playico" onClick="parent.piczoom('<?php echo $dst_b; ?>')"><img src="images/play50.png"></div>
			<div class="addtime"><?php echo $addtime; ?></div>
            <div class="nickname"><a href="<?php echo $href; ?>" target="_blank"><?php echo uicon($sex.$grade) ?> <?php echo $title; ?></a></div>
			<div class="chekbox">
            	<div class="l">
                    <a href="javascript:send_msg(<?php echo $uid; ?>,'<?php echo $title; ?>');"><i class="ico">&#xe676;</i></a>
				</div>
          		<div class="r">
<input type="checkbox" class="checkskin" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="chk<?php echo $id; ?>" onclick="chkbox(<?php echo $i; ?>,<?php echo $id; ?>)"><label for="chk<?php echo $rows['id']; ?>" class="checkskin-label"><i class="i1"></i></label>
                </div>
			</div>
            <?php if ($flag == 0 ){ ?>
              <div class="loveb">
                <?php
				if((ADDTIME - $addtime2) > 300){
                $Photo_awardARR = explode(',',$_ZEAI['Video_awardARR']);
                $alength = count($Photo_awardARR);
                $k = 0;
                foreach($Photo_awardARR as $valuep){
                    $k++;?>
                    <a class="Cf60 award" title="审核并奖励<?php echo $valuep.$_ZEAI['loveB']; ?>" pid="<?php echo $id; ?>" nickname="<?php echo $nickname;?>"><?php echo $valuep; ?></a>
                     <?php
                    if ($alength != $k)echo '&nbsp;|&nbsp;';
                }
				}else{
					echo '封面生成成功后再审';
				}
                ?>
                </div>
             <?php }?>
		</li>
    <?php }//end for ?>
    </div>
<!---->
    </td>
    </tr>
</table>
<div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" class="btn size2 HEI2 disabled action" id="btndellist">批量删除</button>　
    <button type="button" class="btn size2 LV disabled action"  id="btnflag">批量审核</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);"><i class="ico">&#xe676;</i> 发送消息</button>
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
</div>
</form>
<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg   = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
var if__www_zeai_cn__video=true;
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'video'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnflag').onclick = function() {
	allList({
		btnobj:this,
		url:'video'+zeai.ajxext+'submitok=allflag1',
		title:'批量审核',
		content:'<br>1.批量审核不奖励<?php echo $_ZEAI['loveB'];?><br>2.此操作将大批量发送他们所有粉丝视频消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。',
		msg:'正在审核中...',
		ifjson:true,
		ifconfirm:true
	});	
	
}

zeai.listEach('.del',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("pid"));
		zeai.confirm('真的要删除么？',function(){
			zeai.ajax('video'+zeai.ajxext+'submitok=alldel&list[]='+id,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		});
	}
});
function send_msg(uid,nkname) {zeai.iframe('发送消息','u_tip.php?ulist='+uid,600,500);}
zeai.listEach('.award',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("pid")),num=parseInt(obj.innerHTML),nickname=obj.getAttribute("nickname");
		zeai.confirm('1.此审核将奖励【'+decodeURIComponent(nickname)+'】<?php echo $_ZEAI['loveB'];?><font class="Cf00">'+num+'</font>。<br>2.并将同步批量发送【'+decodeURIComponent(nickname)+'】所有粉丝视频消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。',function(){
			zeai.msg('正在审核处理【'+decodeURIComponent(nickname)+'】视频',{time:300});
			zeai.ajax('video'+zeai.ajxext+'submitok=loveb&id='+id+'&num='+num,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
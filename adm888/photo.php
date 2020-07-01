<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('photo',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$id=intval($v);
				$row = $db->ROW(__TBL_PHOTO__,"path_s,uid","id=".$id);
				if ($row){
					$path_s = $row[0];$uid = $row[1];$path_b = getpath_smb($path_s,'b');
					@up_send_admindel($path_s.'|'.$path_b);
					$db->query("DELETE FROM ".__TBL_PHOTO__." WHERE id=".$id);
					//
					$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
					AddLog('【相册审核】会员【'.$nickname.'（uid:'.$uid.'）】->照片驳回并删除');
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
				
				if ($row = $db->ROW(__TBL_PHOTO__,"uid,path_s","id=".$id)){
					$uid = intval($row[0]);$path_s = $row[1];
					$path_b = getpath_smb($path_s,'b');
				}else{exit(JSON_ERROR);}
								
				$row = $db->ROW(__TBL_USER__,"nickname,openid,subscribe","id=".$uid,"num");
				if ($row){
					$data_nickname = dataIO($row[0],'out');$data_openid = $row[1];$data_subscribe = $row[2];
					$db->query("UPDATE ".__TBL_PHOTO__." SET flag=1 WHERE id=".$id);
					AddLog('【相册审核】会员【'.$data_nickname.'（uid:'.$uid.'）】->照片审核通过');
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode('审核通过');
						$keyword2 = urlencode('个人相册照片符合规范');
						$url      = urlencode(mHref('my_photo'));
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					
					//发布动态
					$content = '上传了个人相册<img src="'.$_ZEAI['up2'].'/'.$path_b.'" class="photo_m">';
					$db->query("INSERT INTO ".__TBL_TREND__." (uid,content,addtime) VALUES ($uid,'$content',".ADDTIME.")");
					//给他粉丝站内推送
					//$tip_title   = '您关注的好友【'.$data_nickname.'】上传了新相册';
					//$tip_content = $data_nickname.'上传了新相册'.'　　<a href="'.mHref('u',$uid).'" class="aQING" target="_blank">进入查看</a>';
					//@push_friend_tip($uid,$tip_title,$tip_content);
					//给他粉丝微信推送
					//$CARR = array();
					//$CARR['url']      = urlencode(mHref('u',$uid));
					//$CARR['picurl']   = $path_b;
					//$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
					//$CARR['contentMB']= urlencode($tip_title);
					//@push_friend_wx($uid,$CARR);
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
	case"loveb":
		if (!ifint($id) || !ifint($num))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$awardnum = intval(abs($num));
		if ($row = $db->ROW(__TBL_PHOTO__,"uid,path_s","id=".$id)){
			$uid = intval($row[0]);$path_s = $row[1];
			$path_b = getpath_smb($path_s,'b');
		}else{exit(JSON_ERROR);}
		//
		$row = $db->NUM($uid,"loveb,nickname,openid,subscribe");
		if(!$row)json_exit(array('flag'=>0,'msg'=>'会员不存在'));
		
		$data_loveb = $row[0];$data_nickname = dataIO($row[1],'out');$data_openid = $row[2];$data_subscribe = $row[3];
		$db->query("UPDATE ".__TBL_PHOTO__." SET flag=1 WHERE id=".$id);
		//入库入清单
		$endnum  = $data_loveb + $awardnum;
		$db->query("UPDATE ".__TBL_USER__." SET loveb=$endnum WHERE id=".$uid);
		AddLog('【相册审核】会员【'.$data_nickname.'（uid:'.$uid.'）】->照片审核通过，奖励'.$_ZEAI['loveB'].$awardnum.'个');
		$db->AddLovebRmbList($uid,'上传相册',$awardnum,'loveb',12);
		
		//微信模版
		if (!empty($data_openid) && $data_subscribe==1){
			//审核通过提醒
			$keyword1 = urlencode('审核通过');
			$keyword2 = urlencode('个人相册照片符合规范');
			$url      = urlencode(mHref('my_photo'));
			@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
			//爱豆到账提醒
			$F = urlencode($data_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
			$C = urlencode('上传相册');
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$awardnum.'&first='.$F.'&content='.$C.'&url='.urlencode(mHref('loveb')));
		}
		
		//发布动态
		$content = '上传了个人相册<img src="'.$_ZEAI['up2'].'/'.$path_b.'" class="photo_m">';
		$db->query("INSERT INTO ".__TBL_TREND__." (uid,content,addtime) VALUES ($uid,'$content',".ADDTIME.")");
		//给他粉丝站内推送
		//$tip_title   = '您关注的好友【'.$data_nickname.'】上传了新相册';
		//$tip_content = $data_nickname.'上传了新相册'.'　　<a href="'.mHref('u',$uid).'" class="aQING" target="_blank">进入查看</a>';
		//@push_friend_tip($uid,$tip_title,$tip_content);
		//给他粉丝微信推送
		//$CARR = array();
		//$CARR['url']      = urlencode(mHref('u',$uid));
		//$CARR['picurl']   = $path_b;
		//$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
		//$CARR['contentMB']= urlencode($tip_title);
		//@push_friend_wx($uid,$CARR);
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
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:900px;margin:0 20px 50px 20px}
.tablelist td:hover{background-color:#fff}
/*picadd*/
.picli{width:100%;margin:10px auto;display:-webkit-flex;display:flex;-webkit-flex-wrap:wrap;flex-wrap:wrap;-webkit-justify-content:space-between;justify-content:initial}
.picli li{width:130px;height:170px;line-height:100px;margin:10px 40px 30px 15px;background-color:#fff;position:relative;text-align:center;box-shadow:0 0 10px rgba(0,0,0,0.2);}
.picli li.pf0{height:190px}
.picli li.flag0{background-color:#ffa}
.picli .add,.picli li .del{background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.picli li:hover{background-color:#F2F9FD}
.picli li img{vertical-align:middle;max-width:100px;max-height:100px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;}
.picli li:hover .img{border:#fff 1px solid;cursor:zoom-in}
.picli li .del{width:20px;height:20px;top:-10px;right:-10px;position:absolute;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3)}
.picli li .del:hover{background-position:-100px top;cursor:pointer}
.picli li a.pic{display:block;padding:5px 0}
.picli li .f0{background-color:#ffc}
.picli li .loveb,
.picli li .nickname,
.picli li .chekbox{line-height:24px;height:24px;font-size:12px;color:#ddd;border-top:#eee 1px solid;font-family:'Verdana';overflow:hidden}
.picli li .chekbox{padding:5px}
.picli li .loveb{line-height:30px}
.picli li .chekbox .l{float:left}
.picli li .chekbox .l a{margin:0 12px 0 0}
.picli li .chekbox .r{float:right}

.picli li .chekbox .l a i{font-size:18px;color:#ccc}
.picli li:hover .chekbox .l a i:hover{color:#009688}

.picli li a span{display:block;width:100%;line-height:24px;position:absolute;top:28px;background-color:rgba(0,0,0,0.5);color:#aaa;font-size:12px}
.picli li .flagstr{width:30px;line-height:20px;color:#fff;font-size:12px;position:absolute;top:6px;left:-2px;background-color:#f70}
</style>
<?php
?>
<body>
<div class="navbox">
    <a href="photo.php"<?php echo (empty($t) && $t!='f0' && $t!='f1')?' class="ed"':'';?>>会员相册管理<?php echo '<b>'.$db->COUNT(__TBL_PHOTO__).'</b>';?></a>
    <a href="photo.php?t=f0"<?php echo ($t=='f0')?' class="ed"':'';?>>未审<?php echo '<b>'.$db->COUNT(__TBL_PHOTO__,"flag=0").'</b>';?></a>
    <a href="photo.php?t=f1"<?php echo ($t=='f1')?' class="ed"':'';?>>已审<?php echo '<b>'.$db->COUNT(__TBL_PHOTO__,"flag=1").'</b>';?></a>
    <div class="Rsobox">
    <?php if ($ifmini != 1){?>
    <form name="ZEAI_CN__form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W200" placeholder="按UID/用户名/昵称" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="t" value="<?php echo $t;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>
     <?php }?>
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL = " AND (a.uid=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL = " AND ( ( b.uname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}
if ($t=='f0'){
	$SQL .= " AND a.flag=0 ";
}elseif($t=='f1'){
	$SQL .= " AND a.flag=1 ";
}
$fieldlist = "a.*,b.uname,b.nickname,b.sex,b.grade,b.photo_s";
if (ifint($memberid))$SQL .= " AND a.uid=".$memberid;
$rt = $db->query("SELECT ".$fieldlist." FROM ".__TBL_PHOTO__." a ,".__TBL_USER__." b WHERE a.uid=b.id ".$SQL."  ORDER BY a.flag,a.id DESC LIMIT ".$_ADM['admLimit']);
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

      <div class="picli">
        <?php
        for($i=1;$i<=$pagesize;$i++) {
            $rows      = $db->fetch_array($rt);if(!$rows) break;
            $id        = $rows['id'];
            $uid       = $rows['uid'];
			$uname     = dataIO($rows['uname'],'out');
			$nickname  = dataIO($rows['nickname'],'out');
			$sex       = $rows['sex'];
			$grade     = $rows['grade'];
            $path_s    = $rows['path_s'];
            $photo_s    = $rows['photo_s'];
			$flag      = $rows['flag'];
			//
			if(empty($nickname))$nickname = $uname;
			$nickname = str_replace($Skeyword,"<b class='Cf00'>".$Skeyword."</b>",$nickname);
			//
			$dst_s    = $_ZEAI['up2'].'/'.$path_s;
			$dst_b    = getpath_smb($dst_s,'b');
			$flagbg   = ($flag == 'W　w w .z e 　a i　.c n')?' class="flag0"':'';
			$flagstr  = ($flag == 0)?'<div class="flagstr">未审</div>':'';
			$href     = Href('u',$uid);
    	?>
        <li<?php echo $flagbg; ?><?php echo ($flag == 0)?' style="height:200px;"':''; ?> id="tr<?php echo $id;?>" class="fadeInUp">
        
          <a href="javascript:;" onClick="parent.piczoom('<?php echo $dst_b; ?>')" class="pic<?php if ($flag == 0)echo ' f0'; ?>"><img src="<?php echo $dst_s; ?>" class="img"></a>
          <a href="javascript:;" class="del" pid="<?php echo $id;?>"></a>
          <?php echo $flagstr; ?>
          
          <div class="nickname"><a href="<?php echo $href; ?>" target="_blank" class="C999"><?php echo uicon($sex.$grade) ?> <?php echo $nickname; ?></a></div>
          <div class="chekbox">
          
            <div class="l">
                <a href="javascript:cut(<?php echo $id; ?>,'<?php echo trimhtml($nickname); ?>','<?php echo $p; ?>');" title="裁切为头像"><i class="ico2">&#xe6a5;</i> </a>
                <a href="javascript:send_msg(<?php echo $uid; ?>,'<?php echo trimhtml($nickname); ?>');" title="发送消息"><i class="ico">&#xe676;</i></a>
            </div>
              
            <div class="r">
              <input type="checkbox" class="checkskin" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="chk<?php echo $id; ?>" onclick="chkbox(<?php echo $i; ?>,<?php echo $id; ?>)"><label for="chk<?php echo $rows['id']; ?>" class="checkskin-label"><i class="i1"></i></label>
             </div>
            </div>
          <?php if ($flag == 0){ ?>
          <div class="loveb">
            <?php
			$Photo_awardARR = explode(',',$_ZEAI['Photo_awardARR']);
			$alength = count($Photo_awardARR);
			$k = 0;
			foreach($Photo_awardARR as $valuep){
				$k++;?>
				<a class="Cf60 award" title="审核并奖励<?php echo $valuep.$_ZEAI['loveB']; ?>" pid="<?php echo $id; ?>" nickname="<?php echo $nickname;?>"><?php echo $valuep; ?></a>
				 <?php
				if ($alength != $k)echo '&nbsp;|&nbsp;';
			}
			?>
            </div>
          <?php }?>
          </li>
        <?php }//end for ?>
      </div>
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
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'photo'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnflag').onclick = function() {
	allList({
		btnobj:this,
		url:'photo'+zeai.ajxext+'submitok=allflag1',
		title:'批量审核',
		content:'<br>1.批量审核不奖励<?php echo $_ZEAI['loveB'];?><br>2.此操作将发送照片消息提醒推送，过程可能有点慢，请不要关闭窗口耐心等待。',
		msg:'正在审核中...',
		ifjson:true,
		ifconfirm:true
	});	
	
}

zeai.listEach('.del',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("pid"));
		zeai.confirm('真的要删除么？',function(){
			zeai.ajax('photo'+zeai.ajxext+'submitok=alldel&list[]='+id,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		});
	}
});
function send_msg(uid,nkname) {zeai.iframe('发送消息','u_tip.php?ulist='+uid,600,500);}
function cut(id,nkname,p) {zeai.iframe('裁切【'+nkname+'】主头像','u_photo_cut.php?id='+id+'&submitok=www___zeai__cn_inphotocut'+'&p='+p,650,560);}

zeai.listEach('.award',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("pid")),num=parseInt(obj.innerHTML),nickname=obj.getAttribute("nickname");
		zeai.confirm('1.此审核将奖励【'+decodeURIComponent(nickname)+'】<?php echo $_ZEAI['loveB'];?><font class="Cf00">'+num+'</font>。<br>2.将发送消息提醒推送，过程可能有点慢，请不要关闭窗口耐心等待。',function(){
			zeai.msg('正在审核处理【'+decodeURIComponent(nickname)+'】照片',{time:300});
			zeai.ajax('photo'+zeai.ajxext+'submitok=loveb&id='+id+'&num='+num,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>">"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
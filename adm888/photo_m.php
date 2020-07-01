<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';

if(!in_array('photo_m',$QXARR))exit(noauth());

require_once ZEAI.'sub/zeai_up_func.php';

switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$v=intval($v);
				$row = $db->ROW(__TBL_USER__,"photo_s,nickname,openid,subscribe","id=".$v);
				if ($row){
					$path_s = $row[0];$data_nickname = dataIO($row[1],'out');$data_openid = $row[2];$data_subscribe = $row[3];
					if (!empty($path_s)){
						$path_m = getpath_smb($path_s,'m');$path_b = getpath_smb($path_s,'b');$path_blue = getpath_smb($path_s,'blur');
						@up_send_admindel($path_s.'|'.$path_m.'|'.$path_b.'|'.$path_blue);
					}
					$uid=$v;
					$db->query("UPDATE ".__TBL_USER__." SET photo_s='',photo_f=0 WHERE id=".$v);
					AddLog('审核驳回并删除会员【'.$nickname.'（uid:'.$v.'）】头像');
					//站内消息
					$C = $data_nickname.'您好，您的头像未通过审核，请重新上传本人照片';
					$db->SendTip($uid,"您的头像未通过审核",dataIO($C,'in'),'sys');

					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode('审核未通过');
						$keyword2 = urlencode('头像不符合规范，原因（不是本人或照片太小不清晰），进入重新上传');
						$url      = urlencode(mHref('my_info'));
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
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
				$v=intval($v);
				$row = $db->ROW(__TBL_USER__,"photo_s,nickname,openid,subscribe","id=".$v,"num");
				if ($row){
					$uid=$v;
					$photo_s = $row[0];$data_nickname = dataIO($row[1],'out');$data_openid = $row[2];$data_subscribe = $row[3];
					$photo_m = getpath_smb($photo_s,'m');
					$photo_b = getpath_smb($photo_s,'b');
					//站内消息
					$C = $data_nickname.'您好，您的头像已通过审核';
					$db->SendTip($uid,"您的头像已通过审核",dataIO($C,'in'),'sys');
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode('审核通过');
						$keyword2 = urlencode('头像符合规范');
						$url      = urlencode(mHref('my'));
						@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					//发布动态
					$content = '上传了头像<img src="'.$_ZEAI['up2'].'/'.$photo_m.'" class="photo_m">';
					$db->query("INSERT INTO ".__TBL_TREND__." (uid,content,addtime) VALUES ($uid,'$content',".ADDTIME.")");
					//给他粉丝站内推送
					//$tip_title   = '您关注的好友【'.$data_nickname.'】上传了新头像';
					//$tip_content = $data_nickname.'上传了新头像'.'　　<a href="'.mHref('u',$uid).'" class="aQING" target="_blank">进入查看</a>';
					//@push_friend_tip($uid,$tip_title,$tip_content);
					//给他粉丝微信推送
					//$CARR = array();
					//$CARR['url']      = urlencode(mHref('u',$uid));
					//$CARR['picurl']   = $photo_b;
					//$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
					//$CARR['contentMB']= urlencode($tip_title);
					//@push_friend_wx($uid,$CARR);
					$db->query("UPDATE ".__TBL_USER__." SET photo_f=1 WHERE id=".$v);
					AddLog('审核通过会员【'.$data_nickname.'（uid:'.$uid.'）】头像');
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
	case 'updateendtime':
		if ( !ifint($uid))alert_adm_parent("forbidden","-1");
		$uid = intval($uid);
		$db->query("UPDATE ".__TBL_USER__." SET refresh_time=".ADDTIME." WHERE id=".$uid);
		//
		$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
		AddLog('置顶排名【'.$nickname.'（uid:'.$uid.'）】');
		header("Location: ".SELF."?p=".$p);
	break;
	case 'updatedowntime':
		if ( !ifint($uid) || !ifint($num))json_exit(array('flag'=>0,'msg'=>'zeai_forbidden'));
		$p = abs(intval($p));
		$p = ($p > 1)?$p:1;
		$limt = $num*$p;
		$row = $db->ROW(__TBL_USER__,"refresh_time,id","photo_s<>'' AND photo_f=1 ORDER BY refresh_time DESC LIMIT $limt,1");
		$refresh_time = abs(intval($row[0]-cdstr(5)));
		$db->query("UPDATE ".__TBL_USER__." SET refresh_time=".$refresh_time." WHERE id=".$uid);
		//
		$row2 = $db->ROW(__TBL_USER__,"nickname","id=".$uid,'num');$nickname= $row2[0];
		AddLog('置底排名【'.$nickname.'（uid:'.$uid.'）】');
		json_exit(array('flag'=>1,'msg'=>'操作成功'));
	break;
	case"loveb":
		if (!ifint($uid) || !ifint($num))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$awardnum = intval(abs($num));
		//
		$row = $db->NUM($uid,"photo_s,loveb,nickname,openid,subscribe");
		if(!$row)json_exit(array('flag'=>0,'msg'=>'会员不存在'));
		
		$photo_s = $row[0];$data_loveb = $row[1];$data_nickname = dataIO($row[2],'out');$data_openid = $row[3];$data_subscribe = $row[4];
		$photo_m = getpath_smb($photo_s,'m');
		$photo_b = getpath_smb($photo_s,'b');
		//入库入清单
		$endnum  = $data_loveb + $awardnum;
		$db->query("UPDATE ".__TBL_USER__." SET loveb=$endnum,photo_f=1 WHERE id=".$uid);
		$db->AddLovebRmbList($uid,'上传头像',$awardnum,'loveb',12);
		//
		AddLog('审核头像【'.$data_nickname.'（uid:'.$uid.'）】已通过，奖励'.$_ZEAI['loveB'].$awardnum.'个');

		//站内消息
		$C = $data_nickname.'您好，您的头像已通过审核';
		$db->SendTip($uid,"您的头像已通过审核",dataIO($C,'in'),'sys');

		//微信模版
		if (!empty($data_openid) && $data_subscribe==1){
			//审核通过提醒
			$keyword1 = urlencode('审核通过');
			$keyword2 = urlencode('头像符合规范');
			$url      = urlencode(mHref('my'));
			@wx_mb_sent('mbbh=ZEAI_DATA_CHECK&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
			//爱豆到账提醒
			$F = urlencode($data_nickname."您好，您有一笔".$_ZEAI['loveB']."到账！");
			$C = urlencode('上传头像');
			@wx_mb_sent('mbbh=ZEAI_ACCOUNT_IN&openid='.$data_openid.'&num='.$awardnum.'&first='.$F.'&content='.$C.'&url='.urlencode(mHref('loveb')));
		}
		
		//发布动态
		$content = '上传了头像<img src="'.$_ZEAI['up2'].'/'.$photo_m.'" class="photo_m">';
		$db->query("INSERT INTO ".__TBL_TREND__." (uid,content,addtime) VALUES ($uid,'$content',".ADDTIME.")");
		
		/*************通知粉丝*************/
		//给他粉丝站内推送
		$tip_title   = '您关注的好友【'.$data_nickname.'】上传了新头像';
		$tip_content = $data_nickname.'上传了新头像'.'　　<a href="'.mHref('u',$uid).'" class="aQING">进入查看</a>';
		@push_friend_tip($uid,$tip_title,$tip_content);
		//给他粉丝微信推送
		$CARR = array();
		$CARR['url']      = urlencode(mHref('u',$uid));
		$CARR['picurl']   = $photo_b;
		$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
		$CARR['contentMB']= urlencode($tip_title);
		@push_friend_wx($uid,$CARR);
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
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:900px;margin:0 20px 50px 20px}
.tablelist td:hover{background-color:#fff}
/*picadd*/
.picli{width:100%;margin:10px auto;display:-webkit-flex;display:flex;-webkit-flex-wrap:wrap;flex-wrap:wrap;-webkit-justify-content:space-between;justify-content:initial}
.picli li{width:130px;height:170px;line-height:100px;margin:10px 40px 30px 15px;background-color:#fff;position:relative;text-align:center;box-shadow:0 0 10px rgba(0,0,0,0.2);}
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
.picli li .loveb{line-height:30px;padding-bottom:10px}
.picli li .chekbox .l{float:left}
.picli li .chekbox .l a{margin:0 2px 0 0}
.picli li .chekbox .r{float:right}
.picli li .chekbox .l a i{font-size:18px;color:#ccc}
.picli li:hover .chekbox .l a i:hover{color:#009688}
.picli li a span{display:block;width:100%;line-height:24px;position:absolute;top:28px;background-color:rgba(0,0,0,0.5);color:#aaa;font-size:12px}
.picli li .flagstr,.picli li .RZstr_identity,.picli li .RZstr_photo{width:30px;line-height:20px;color:#fff;font-size:12px;position:absolute;top:6px;left:-2px;background-color:#f70}
.picli li .RZstr_identity,.picli li .RZstr_photo{width:20px}
.picli li .RZstr_identity{top:46px;background-color:#FD787B}
.picli li .RZstr_photo{top:76px;background-color:#54A791}
.zdbox{margin:20px auto 0 auto;width:90%}
.zdbox button{margin:10px}
</style>
<body>
<?php if ($submitok == 'downtime'){
	$p=intval($p);
	$uid=intval($uid);?>
    <div class="navbox">
    <a class="ed">选择置底名次</a>
    <div class="Rsobox"></div><div class="clear"></div></div><div class="fixedblank"></div>
	<form id="GYLform">
        <table class=" tablebz">
        <tr>
        <td class="center">
            <div class="zdbox">
                <button type="button" class="btn size4 BAI zdli">30</button>
                <button type="button" class="btn size4 BAI zdli">50</button>
                <button type="button" class="btn size4 BAI zdli">100</button>
                <button type="button" class="btn size4 BAI zdli">500</button>
                <button type="button" class="btn size4 BAI zdli">1000</button>
            </div>
        </td>
        </tr>
        <tr>
        <td height="10" class="center">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="uid" value="<?php echo $uid;?>" />
        <input type="hidden" name="submitok" value="updatedowntime" />
        </td>
        </tr>
        </table>
	</form>
	<script>
	zeai.listEach('.zdli',function(obj){
		obj.onclick=function(){
			var num = parseInt(obj.innerHTML);			
			zeai.confirm('确定要置底【'+num+'】么？',function(){
				zeai.ajax({url:'photo_m'+zeai.extname,data:{num:num},form:GYLform},function(e){rs=zeai.jsoneval(e);
					window.parent.zeai.msg(0);window.parent.zeai.msg(rs.msg);
					if(rs.flag==1){setTimeout(function(){window.parent.location.reload(true);},1000);}
				});
			});
		}
	});
	</script>
<?php exit;}?>

<div class="navbox">
    <a href="photo_m.php"<?php echo (empty($t) && $t!='f0' && $t!='f1')?' class="ed"':'';?>>头像管理/置顶<?php echo '<b>'.$db->COUNT(__TBL_USER__,"photo_s<>'' AND flag=1 ").'</b>';?></a>
    <a href="photo_m.php?t=f0"<?php echo ($t=='f0')?' class="ed"':'';?>>未审<?php echo '<b>'.$db->COUNT(__TBL_USER__,"photo_f=0 AND photo_s<>''").'</b>';?></a>
    <a href="photo_m.php?t=f1"<?php echo ($t=='f1')?' class="ed"':'';?>>已审<?php echo '<b>'.$db->COUNT(__TBL_USER__,"photo_f=1 AND photo_s<>''").'</b>';?></a>
    <a href="photo_m.php?t=f_1_0"<?php echo ($t=='f_1_0')?' class="ed"':'';?>>已锁定<?php echo '<b>'.$db->COUNT(__TBL_USER__,"flag=-1 AND photo_s<>''").'</b>';?></a>
    <div class="Rsobox">
    <form name="ZEAI_CN__form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W200" placeholder="按UID/用户名/昵称" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="t" value="<?php echo $t;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>    
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL = " AND (id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL = " AND ( ( uname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}
if ($t=='f0'){
	$SQL .= " AND photo_f=0 ";
}elseif($t=='f1'){
	$SQL .= " AND photo_f=1 ";
}elseif($t=='f_1_0'){
	$SQL .= " AND flag=-1 ";
}else{
	$SQL .= " AND flag=1 ";
}

$rt = $db->query("SELECT id,uname,nickname,sex,grade,mob,photo_s,photo_f,refresh_time,RZ,photo_ifshow FROM ".__TBL_USER__." WHERE  photo_s<>'' ".$SQL." ORDER BY photo_f ASC,refresh_time DESC LIMIT ".$_ADM['admLimit']);
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
            $uid       = $id;
			$uname     = dataIO($rows['uname'],'out');
			$nickname  = dataIO($rows['nickname'],'out');
			$sex       = $rows['sex'];
			$grade     = $rows['grade'];
            $photo_s   = $rows['photo_s'];
            $photo_f   = $rows['photo_f'];
            $photo_ifshow = $rows['photo_ifshow'];
            $RZ        = $rows['RZ'];$RZarr=explode(',',$RZ);
			$RZstr_identity =(in_array('identity',$RZarr))?'<div class="RZstr_identity" title="已实名认证"><font class="ico">&#xea2e;</font></div>':'';
			$RZstr_photo    =(in_array('photo',$RZarr))?'<div class="RZstr_photo" title="已真人认证"><font class="ico">&#xe645;</font></div>':'';
			//
			if(empty($nickname))$nickname = $uname;
			$nickname = str_replace($Skeyword,"<b class='Cf00'>".$Skeyword."</b>",$nickname);
			//
			$dst_s    = $_ZEAI['up2'].'/'.$photo_s;
			$dst_b    = smb($dst_s,'b');
			$flagbg   = ($flag == 'W　w w .z e 　a i　.c n')?' class="flag0"':'';
			$flagstr  = ($photo_f == 0)?'<div class="flagstr">未审</div>':'';
			$href     = Href('u',$uid);
			$title2 = (!empty($nickname))?urlencode(trimhtml($nickname)).'／'.$uid:$uid;
			$title2 = urlencode(trimhtml($nickname.' ｜ '.$uid));
    	?>
        <li<?php echo $flagbg; ?><?php echo ($photo_f == 0)?' style="height:200px;"':''; ?> id="tr<?php echo $id;?>" class="fadeInUp">
          <a href="javascript:;" onClick="parent.piczoom('<?php echo $dst_b; ?>')" class="pic<?php if ($photo_f == 0)echo ' f0'; ?>"><img src="<?php echo $dst_s; ?>" class="img"></a>
          <a href="javascript:;" class="del" uid="<?php echo $uid;?>" title="删除头像"></a>
          <?php echo $flagstr.$RZstr_identity.$RZstr_photo; ?>
          
          <div class="nickname"><a href="javascript:;" uid="<?php echo $uid;?>" title2="<?php echo $title2;?>" class="C999 photo_s"><?php echo uicon($sex.$grade) ?><?php echo $nickname; ?></a></div>
          <div class="chekbox">
            <div class="l">
            <a href="javascript:cut(<?php echo $id; ?>,'<?php echo strip_tags($nickname); ?>','<?php echo $p; ?>');" title="裁切头像"><i class="ico2">&#xe6a5;</i> </a>
            <a href="javascript:send_msg(<?php echo $uid; ?>,'<?php echo $nickname; ?>');" title="发送消息"><i class="ico">&#xe676;</i></a>
            <a href="<?php echo SELF; ?>?uid=<?php echo $uid; ?>&submitok=updateendtime&p=<?php echo $p; ?>" title="置顶排名"><i class="ico2">&#xe602;</i></a>
            <a href="javascript:;" class="abottom" uid="<?php echo $uid; ?>" p="<?php echo $p; ?>" title="置底排名"><i class="ico2">&#xe63b;</i></a>
            </div>
              
            <div class="r">
              <input type="checkbox" class="checkskin" name="list[]" value="<?php echo $id; ?>" id="chk<?php echo $id; ?>" onclick="chkbox(<?php echo $i; ?>,<?php echo $id; ?>)"><label for="chk<?php echo $rows['id']; ?>" class="checkskin-label" title="选择"><i class="i1"></i></label>
             </div>
            </div>
          <?php if ($photo_f == 0){ ?>
          <div class="loveb">
            <?php
			$Photo_awardARR = explode(',',$_ZEAI['Photo_awardARR']);
			$alength = count($Photo_awardARR);
			$k = 0;
			foreach($Photo_awardARR as $valuep){
				$k++;?>
				<a class="Cf60 award" title="审核并奖励<?php echo $valuep.$_ZEAI['loveB']; ?>" uid="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>"><?php echo $valuep; ?></a>
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
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn(this);"><i class="ico">&#xe676;</i> 发送消息</button>
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?>
</div>
</form>
<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg   = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'photo_m'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
o('btnflag').onclick = function() {
	allList({
		btnobj:this,
		url:'photo_m'+zeai.ajxext+'submitok=allflag1',
		title:'批量审核',
		content:'<br>1.批量审核不奖励<?php echo $_ZEAI['loveB'];?><br>2.此操作将发送消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。',
		msg:'正在审核中...',
		ifjson:true,
		ifconfirm:true
	});	
	
}

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
function send_msg(uid,nkname) {zeai.iframe('发送消息','u_tip.php?ulist='+uid,600,500);}
function cut(id,nkname,p) {zeai.iframe('裁切【'+nkname+'】主头像','u_photo_cut.php?ifm=1&id='+id+'&submitok=www___zeai__cn_inphotocut'+'&p='+p,650,560);}

zeai.listEach('.award',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),num=parseInt(obj.innerHTML),nickname=obj.getAttribute("nickname");
		zeai.confirm('1.此审核将奖励【'+decodeURIComponent(nickname)+'】<?php echo $_ZEAI['loveB'];?><font class="Cf00">'+num+'</font>。<br>2.并将同步发送消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。',function(){
			zeai.msg('正在审核处理【'+decodeURIComponent(nickname)+'】照片',{time:300});
			zeai.ajax('photo_m'+zeai.ajxext+'submitok=loveb&uid='+uid+'&num='+num,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});


zeai.listEach('.abottom',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		p = parseInt(obj.getAttribute("p"));
		zeai.iframe('【'+uid+'】排序置底','photo_m'+zeai.ajxext+'submitok=downtime&p='+p+'&uid='+uid,400,300);
	}
});


zeai.listEach('.photo_s',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		urlpre = 'crm_user_detail.php?t=2&iframenav=1&uid='+uid;
		zeai.iframe('【'+decodeURIComponent(title2)+'】'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframe_A(\'modedatabox\',\''+urlpre+'\',this);" class="ed"><i class="ico add">&#xe7a0;</i> 个人主页</a>'+
		'</div>',urlpre);
	}
});

</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('cert',$QXARR))exit(noauth());
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'sub/zeai_up_func.php';

switch ($submitok) {
	case"flag1":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){$id=intval($id);
				//
				if ($row = $db->ROW(__TBL_RZ__,"uid,path_b,path_b2,rzid","flag=0 AND id=".$id)){
					$uid = intval($row[0]);$path_b = $row[1];$path_b2 = $row[2];$rzid = $row[3];
				}else{exit(JSON_ERROR);}
				//
				$row = $db->ROW(__TBL_USER__,"nickname,openid,subscribe,RZ","id=".$uid,"num");
				if ($row){
					$data_nickname = dataIO($row[0],'out');$data_openid = $row[1];$data_subscribe = $row[2];$data_RZ = $row[3];
					//
					$RZ = explode(',',$data_RZ);
					if (empty($RZ) || count($RZ)<=0 || empty($data_RZ)){
						$db->query("UPDATE ".__TBL_USER__." SET RZ='$rzid' WHERE id=".$uid);
					}else{
						if (!in_array($rzid,$RZ)){
							$RZ[]=$rzid;
							$list = implode(',',$RZ);
							$db->query("UPDATE ".__TBL_USER__." SET RZ='$list' WHERE id=".$uid);
						}
					}
					$db->query("UPDATE ".__TBL_RZ__." SET flag=1 WHERE id=".$id);
					//
					AddLog('【认证审核】会员【'.$data_nickname.'（uid:'.$uid.'）】->【'.RZtitle($rzid).'】->通过');
					//站内消息
					$C = '恭喜你！'.RZtitle($rzid).'认证审核已通过';
					$db->SendTip($uid,$C,dataIO($C,'in'),'sys');
					
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode(RZtitle($rzid));
						$keyword2 = urlencode('已通过');
						$remark   = urlencode('已点亮'.RZtitle($rzid).'认证图标');
						$url      = urlencode(mHref('cert'));
						@wx_mb_sent('mbbh=ZEAI_HONOR_CHECK&openid='.$data_openid.'&flag=1&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
					}
					
					/*************通知粉丝*************/
					//给他粉丝站内推送
					$tip_title   = '您关注的好友【'.$data_nickname.'】'.RZtitle($rzid).'认证审核已通过';
					$tip_content = $tip_title.'　　<a href="'.Href('u',$uid).'" class="aQING" target="_blank">进入查看</a>';
					@push_friend_tip($uid,$tip_title,$tip_content);
					//给他粉丝微信推送
					$CARR = array();
					$CARR['url']      = urlencode(mHref('u',$uid));
					$CARR['contentKF']= urlencode($tip_title.'　　<a href="'.$CARR['url'].'">进入查看</a>');
					$CARR['contentMB']= urlencode($tip_title);
					@push_friend_wx($uid,$CARR);
					//
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
	case"flag0":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要驳回的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){$id=intval($id);
				//
				$row = $db->ROW(__TBL_RZ__,"uid,path_b,path_b2,rzid"," id=".$id,"num");
				if ($row){
					$uid = intval($row[0]);$path_b = $row[1];$path_b2 = $row[2];$rzid = $row[3];
				}else{exit(JSON_ERROR);}
				//
				$row = $db->ROW(__TBL_USER__,"nickname,openid,subscribe,RZ","id=".$uid,"num");
				if ($row){
					$data_nickname = dataIO($row[0],'out');$data_openid = $row[1];$data_subscribe = $row[2];
					$RZ = $row[3];
					$RZ=explode(',',$RZ);
					if(is_array($RZ) && count($RZ)>0){
						foreach ($RZ as $key=>$value){
							if ($value === $rzid)unset($RZ[$key]);
						}
						$RZ = implode(",",$RZ);
					}else{
						$RZ = '';	
					}
					$db->query("UPDATE ".__TBL_USER__." SET RZ='$RZ' WHERE id=".$uid);
					AddLog('【认证审核】会员【'.$data_nickname.'（uid:'.$uid.'）】->【'.RZtitle($rzid).'】->驳回');

					$db->query("DELETE FROM ".__TBL_RZ__." WHERE id=".$id);
					@up_send_admindel($path_b.'|'.$path_b2);

					//站内消息
					$C = '请检查('.RZtitle($rzid).')证件是否有效';
					$C .= "　<a href=".Href('cert')." class=aQING target=_blank>重新认证</a>";
					$db->SendTip($uid,'对不起！'.RZtitle($rzid).'审核失败！',dataIO($C,'in'),'sys');
					
					//微信模版
					if (!empty($data_openid) && $data_subscribe==1){
						//审核通过提醒
						$keyword1 = urlencode(RZtitle($rzid));
						$keyword2 = urlencode('未通过');
						$remark   = urlencode('请检查('.RZtitle($rzid).')证件是否有效');
						$url      = urlencode(mHref('cert'));
						@wx_mb_sent('mbbh=ZEAI_HONOR_CHECK&openid='.$data_openid.'&flag=0&keyword1='.$keyword1.'&keyword2='.$keyword2.'&first='.$first.'&remark='.$remark.'&url='.$url);
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
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:1111px;margin:20px 20px 50px 20px}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
.myinfobfb0,.myinfobfb1,.myinfobfb2{font-family:Arial;font-size:18px;display:block;height:20px;line-height:24px}
.myinfobfb0{color:#999}
.myinfobfb1{color:#f70}
.myinfobfb2{color:#090}

.ped{color:#FF5722;border-bottom:2px #FF5722 solid;padding-bottom:5px}
</style>
<?php
?>
<body>
<div class="navbox">
    <a class="ed">认证管理<?php echo '<b>'.$db->COUNT(__TBL_RZ__).'</b>';?></a>
    <a href="cert_hand.php">手动强制认证</a>
    <div class="Rsobox">
        <form id="Zeai_search_form" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="输入：UID/用户名/手机/姓名/昵称">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
        </form>
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0"><tr><td align="left" class="S14" style="padding-top:10px;color:#aaa">
    <a href="cert.php" class="ped">人工审核认证</a>　｜　
    <a href="cert_auto.php" class="C999">自助认证</a>
</td></tr></table>
<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL = " AND (b.id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL = " AND ( ( b.uname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".$Skeyword."%' ) OR ( b.nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}
$fieldlist  = "a.*,b.uname,b.nickname,b.sex,b.grade,b.love,b.pay,b.mob,b.photo_s,b.photo_f,b.birthday,b.areatitle,b.house,b.car,b.edu,b.RZ,b.truename,b.identitynum";
$rt = $db->query("SELECT ".$fieldlist." FROM ".__TBL_RZ__." a ,".__TBL_USER__." b WHERE a.uid=b.id ".$SQL." AND a.rzid<>'photo' ORDER BY a.flag,a.addtime DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70" align="left">UID</th>
    <th width="70" align="left">头像</th>
    <th width="200">认证会员</th>
    <th width="100" align="center">认证项目/状态</th>
    <th width="20" align="center">&nbsp;</th>
    <th width="200" align="left">对应当前所填资料</th>
	<th align="left" >提交的证件照片</th>
	<th width="180" align="center" >时间/备注</th>
	<th align="center" >操作</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];$uid = $rows['uid'];
		$uname = strip_tags($rows['uname']);
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$nickname);
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		$RZ        = $rows['RZ'];
		$rzid      = $rows['rzid'];
		$birthday  = $rows['birthday'];
		$love      = $rows['love'];
		$areatitle = $rows['areatitle'];
		$pay       = $rows['pay'];
		$house     = $rows['house'];
		$car       = $rows['car'];
		$edu       = $rows['edu'];
		$flag      = $rows['flag'];
		$path_b    = $rows['path_b'];
		$path_b2   = $rows['path_b2'];
		$ifadm     = dataIO($rows['ifadm'],'out');
		$bz        = dataIO($rows['bz'],'out');
		$truename   = dataIO($rows['truename'],'out');
		$identitynum= dataIO($rows['identitynum'],'out');
		
		if(empty($rows['nickname'])){
			$nickname = $uname;
		}
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
		if(!empty($path_b)){
			$path_b_url = $_ZEAI['up2'].'/'.$path_b;
			$path_b_str = '<img src="'.$path_b_url.'">';
		}else{
			$path_b_url = '';
			$path_b_str = '无图';
		}
		if(!empty($path_b2)){
			$path_b2_url = $_ZEAI['up2'].'/'.$path_b2;
			$path_b2_str = '<img src="'.$path_b2_url.'">';
		}else{
			$path_b2_url = '';
			$path_b2_str = '无图';
		}
		$mob = strip_tags($rows['mob']);
		$mob = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$mob);
		$photo_fstr = ($photo_f == 0 && !empty($photo_s))?'<span>审核中</span>':'';
		$title2 = (!empty($nickname))?urlencode(trimhtml($nickname).'/'.$uid):$uid;
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
      </td>
        <td width="70" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $uid;?></label></td>
        <td width="70" align="left">
        	<a href="javascript:;" class="noU58 sex<?php echo $sex; ?>" onClick="parent.piczoom('<?php echo getpath_smb($photo_s_url,'b'); ?>');"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
        </td>
      <td width="200" align="left" class="lineH150" style="padding:10px 0">
      	<div style="margin:5px 0"><?php echo RZ_html($RZ,'s','all');?></div>
        <a href="<?php echo Href('u',$uid);?>" target="_blank">
        <?php echo uicon($sex.$grade) ?><?php if(!empty($rows['uname']))echo '<font class="S14">'.$uname.'</font></br>';?>
        <font class="uleft">
        <?php
        if(!empty($rows['mob']))echo $mob."</br>";
        if(!empty($rows['nickname']))echo $nickname;?>
        </font>
        </a>
    </td>
    <td width="100" align="center"><font class="S16"><?php echo RZtitle($rzid);?></font><br>
    
		<?php if ($flag == 1){
        echo "<i class='ico S18' style='color:#45C01A;vertical-align:middle'>&#xe60d;</i>";
        } else {
        echo "<i class='ico S18 Cf00' style='vertical-align:middle'>&#xe62c;</i>";
        }?>

    </td>
    <td width="20" align="center" class="C999">&nbsp;</td>
    <td width="200" align="left" class="blue lineH150">
<?php 
switch ($rzid){
	case 'identity':
		echo '<font class="C999">姓　　名：</font>'.$truename.'<br>';
		echo '<font class="C999">生　　日：</font>'.$birthday.'<br>';
		echo '<font class="C999">身份证号：</font>'.$identitynum.'<br>';
		echo '<font class="C999">所在地区：</font>'.$areatitle;
	break;
	case 'edu':
		echo'<font class="C999">学　　历：</font>'.udata('edu',$edu);
	break;
	case 'car':
		echo '<font class="C999">汽　　车：</font>'.udata('car',$car);
	break;
	case 'house':
		echo'<font class="C999">房　　产：</font>'.udata('house',$house);
	break;
	case 'love':
		echo'<font class="C999">婚　　况：</font>'.udata('love',$love);
	break;
	case 'pay':
		echo'<font class="C999">月收入：</font>'.udata('pay',$pay);
	break;
}
?>
    </td>
    <td align="left" class="C999 lineH200 padding10">
    
    <a href="javascript:;" onClick="parent.piczoom('<?php echo $path_b_url; ?>');" class="pic100 FL"><?php echo $path_b_str; ?></a>
    <?php if(!empty($path_b2)){?>
        <a href="javascript:;" onClick="parent.piczoom('<?php echo $path_b2_url; ?>');" class="pic100 FL" style="margin-left:15px"><?php echo $path_b2_str; ?></a>
    <?php }?>
    
    </td>
    <td width="180" align="center" class="C666 lineH150 padding15"><?php echo $addtime; 
	if(!empty($ifadm)){
		echo '<div class="linebox"><div class="line"></div><div class="title BAI">后台操作</div></div>';
		echo '操作人：'.$ifadm;
	}
	if(!empty($bz)){
		echo '<br>备注：'.$bz;
	}
	?></td>
    <td align="center">
      <?php if ($flag == 0){?><div><a href="javascript:;" title="审核" class="btn size2 LV flag1" id="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>" style="margin:5px 0">通过</a></div><?php }?>
      <?php if(empty($ifadm)){?><div><a href="javascript:;" title="拒绝" class="btn size2 HUI flag0" id="<?php echo $id; ?>" nickname="<?php echo urlencode($nickname);?>" style="margin:5px 0">驳回</a></div><?php }?>
      <a href="javascript:;" photo_s_url="<?php echo $photo_s_url;?>" title2="<?php echo $title2;?>" title="修改" class="btn size2 BAI submod" uid="<?php echo $uid; ?>" style="margin:5px 0">修改</a></td>
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
var bg = '<?php echo $_Style['list_bg']; ?>',overbg   = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'cert'+zeai.ajxext+'submitok=flag0',
		title:'批量驳回',
		msg:'驳回处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.flag0',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("id")),nickname=obj.getAttribute("nickname");
		zeai.confirm('真的要驳回【'+decodeURIComponent(nickname)+'】么？',function(){
			zeai.msg('正在驳回处理...',{time:300});
			zeai.ajax('cert'+zeai.ajxext+'submitok=flag0&list[]='+id,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});


o('btnflaglist').onclick = function() {
	allList({
		btnobj:this,
		url:'cert'+zeai.ajxext+'submitok=flag1',
		title:'批量审核',
		content:'<br>此审核将同步批量发送所有粉丝消息提醒推送（站内和微信公众号），过程可能有点慢，请不要关闭窗口耐心等待。',
		msg:'审核处理中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.flag1',function(obj){
	obj.onclick = function(){
		zeai.confirm('真的要通过审核么？',function(){
			var id = parseInt(obj.getAttribute("id")),nickname=obj.getAttribute("nickname");
			zeai.msg('正在审核处理【'+decodeURIComponent(nickname)+'】/推送粉丝消息',{time:300});
			zeai.ajax('cert'+zeai.ajxext+'submitok=flag1&list[]='+id,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(0);zeai.msg(rs.msg,{time:1});}
			});
		});
	}
});

zeai.listEach('.submod',function(obj){
	obj.onclick = function(){
		var uid = parseInt(obj.getAttribute("uid")),
		title2 = obj.getAttribute("title2"),
		//photo_s_url = decodeURIComponent(obj.getAttribute("photo_s_url")),
		urlpre = 'crm_gj_yj.php?uid='+uid+'&submitok=';
		//photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		zeai.iframe('【'+decodeURIComponent(title2)+'】修改认证资料','u_mod_data.php?t=6&iframenav=1&submitok=mod&ifmini=1&uid='+uid);
	}
});

</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
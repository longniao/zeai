<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
$currfields="sex,grade,nickname,photo_s,photo_f";
$chk_u_jumpurl=HOST.'/?z=trend';require_once ZEAI.'my_chk_u.php';
//
require_once ZEAI.'cache/config_vip.php';
$trend_addflag = json_decode($_VIP['trend_addflag'],true);
$trend_bbsflag = json_decode($_VIP['trend_bbsflag'],true);
//
$data_sex=$row['sex'];
$data_grade=$row['grade'];
$data_photo_s=$row['photo_s'];
$data_photo_f=$row['photo_f'];
$data_nickname=dataIO($row['nickname'],'out');
$data_nickname = (empty($data_nickname))?'uid:'.$cook_uid:$data_nickname;
if($submitok == 'trend_bbs_add'){
	if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'zeai_fid_error'));
	if (empty($content) || str_len($content)>1000)json_exit(array('flag'=>0,'msg'=>'请输入内容(200字以内)'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发表'));
	$T = $db->ROW(__TBL_TREND__,"uid,content","id=".$fid,"num");
	if ($T){
		$uid = $T[0];$Tcontent = trimhtml($T[1]);
		//
		$content = dataIO($content,'in',1000);
		$addtime    = date_str(ADDTIME-10);
		$photo_s_url= (!empty($data_photo_s) && $data_photo_f==1)?$_ZEAI['up2'].'/'.$data_photo_s:HOST.'/res/photo_s'.$data_sex.'.png';
		$flag       = intval($trend_bbsflag[$data_grade]);
		$flag_str   = ($flag==0)?'，请等待我们审核':'';
		$db->query("INSERT INTO ".__TBL_TREND_BBS__." (uid,fid,content,addtime,flag) VALUES ($cook_uid,$fid,'$content',".ADDTIME.",$flag)");
		setcookie("cook_content",$content,time()+60,"/",$_ZEAI['CookDomain']);
		$sexcolor = ($data_sex==2)?' class=\'Chong\'':' class=\'Clan\'';
		$CC .= '<li>';
		$CC .= '<a onClick=\'trend_uFn('.$cook_uid.')\'><img class=\'m\' src=\''.$photo_s_url.'\'><h5'.$sexcolor.'>'.$data_nickname.'：<font>'.$addtime.'</font></h5></a>';
		$CC .= '<span>'.trimContact($content).'</span>';
		$CC .= '</li>';
		//
		//通知
		$row = $db->ROW(__TBL_USER__,'openid,subscribe',"id=".$uid,"num");
		if($row){
			$openid = $row[0];$subscribe = $row[1];
			//站内消息
			$title='您的交友圈/动态【'.$Tcontent.'】有人评论了';
			$C = $title.'　　<a href='.Href('trend',$uid).' class=aQING>进入查看</a>';
			$db->SendTip($uid,$title,dataIO($C,'in',1000),'sys');
			//微信通知
			if (!empty($openid) && $subscribe==1){
				//客服通知
				$C = urlencode(dataIO($title.'<br>→<a href="'.HOST.'/?z=trend&submitok=my">【进入查看】</a>','wx'));
				$ret = @wx_kf_sent($openid,$C,'text');
				$ret = json_decode($ret);
				//模版通知
				if ($ret->errmsg != 'ok'){
					$first   = (!empty($first))?dataIO($first,'out'):'系统智能提醒！';
					$remark  = '';
					$keyword1  = $title;
					$keyword3  = urlencode($_ZEAI['siteName']);
					$url       = urlencode(HOST.'/?z=trend&submitok=my');
					@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
				}
			}
		}
		//通知END		
		json_exit(array('flag'=>1,'msg'=>'发表成功'.$flag_str,'C'=>$CC));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_nodata_error'));
	}
}elseif($submitok=='ajax_vdetail'){
	if(!ifint($tid))json_exit(array('flag'=>0,'msg'=>'zeai_id_error'));
	$T = $db->ROW(__TBL_TREND__,"content","id=".$tid,"num");
	if ($T){
		$Tcontent = dataIO($T[0],'out');
		preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i',$Tcontent,$match);
		$src=$match[1];
		$src=str_replace($_ZEAI['up2'].'/',"",$src);
		$V = $db->ROW(__TBL_VIDEO__,"id","path_s='$src'","num");
		if($V)json_exit(array('flag'=>1,'vid'=>$V[0]));
	}
	json_exit(array('flag'=>0,'msg'=>'zeai_nodata_error'));
}
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
$switch = json_decode($_ZEAI['switch'],true);
//
$_ZEAI['pagesize']= 5;
if ($submitok == 'my' && ifint($cook_uid)){
	$_ZEAI['pagesize']= 500;
	$SQL = " a.uid=".$cook_uid." AND ";
}else{
	//$SQL = (ifint($cook_uid))?"":" a.flag=1 AND ";
	$SQL = " a.flag=1 AND ";
}
$RTSQL = "SELECT a.id,a.uid,a.piclist,a.content,a.agreenum,a.agreelist,a.addtime,b.sex,b.grade,b.nickname,b.photo_s,b.photo_f,b.birthday,b.pay,b.heigh,b.edu,b.areatitle,b.photo_ifshow FROM ".__TBL_TREND__." a,".__TBL_USER__." b WHERE ".$SQL." a.uid=b.id AND b.flag=1 ORDER BY a.id DESC";
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容～～</div>";

if ($submitok == 'ajax_ulist'){
	exit(ajax_ulist_fn($totalP,$p));
}elseif($submitok == 'addupdate'){
	$content = dataIO($content,'in',280);
	if (empty($content))json_exit(array('flag'=>0,'msg'=>'请输入内容～'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发布～'));
	if (!empty($morelist)){
		 if (is_weixin()){
			$serverIds = $morelist;
			if (str_len($serverIds) > 15){
				$serverIds = explode(',',$serverIds);
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_up('trend',$url,$cook_uid.'_','SB');
					$_s     = setpath_s($dbname);
					$piclist[]=$_s;
				}
				$piclist = implode(",",$piclist);
			}else{
				json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
			}
		 }else{
			$sARR = explode(',',$morelist);
			foreach ($sARR as $value) {
				$_s = str_replace($_ZEAI['up2'].'/','',$value);
				u_pic_reTmpDir_send($_s,'trend');
				u_pic_reTmpDir_send(getpath_smb($_s,'b'),'trend');
				$_s = str_replace('/tmp/','/trend/',$_s);
				$piclist[] = $_s;
			}
			$piclist = implode(",",$piclist);
		}
	}
	setcookie("cook_content",$content,time()+20,"/",$_ZEAI['CookDomain']);
	$flag = intval($trend_addflag[$data_grade]);
	$db->query("INSERT INTO ".__TBL_TREND__." (uid,piclist,content,addtime,flag) VALUES ($cook_uid,'$piclist','$content',".ADDTIME.",$flag)");
	$flag_str= ($flag==0)?'，请等待我们审核':'';
	json_exit(array('flag'=>1,'msg'=>'发布成功'.$flag_str));
}elseif($submitok == 'delmy'){
	if(!ifint($id))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TREND__,"piclist","uid=".$cook_uid." AND id=".$id);
	if ($row){
		$piclist= $row[0];
		if(!empty($piclist)){
			$sARR = explode(',',$piclist);
			if(count($sARR) >0){
				foreach ($sARR as $value) {
					@up_send_userdel($value.'|'.getpath_smb($value,'b'));
				}
			}
		}
		$db->query("DELETE FROM ".__TBL_TREND__." WHERE id=".$id." AND uid=".$cook_uid);
		$db->query("DELETE FROM ".__TBL_TREND_BBS__." WHERE fid=".$id);
	}
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok == 'ajax_photo_up_h5'){
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_');
		if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upMsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$_s = setpath_s($dbname);
		$tmppicurl = $_ZEAI['up2']."/".$_s;
		if (!ifpic($tmppicurl))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
		json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}elseif($submitok == 'ajax_photo_up_app'){
	$f=$_FILES['file'];
	$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_');
	if (!up_send($f,$dbname,$_UP['ifwaterimg'],$_UP['upMsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
	$_s = setpath_s($dbname);
	$tmppicurl = $_ZEAI['up2']."/".$_s;
	if (!ifpic($tmppicurl))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
	json_exit(array('flag'=>1,'msg'=>'上传成功','_s'=>$_s));

}elseif($submitok == 'ajax_agree'){
	if(!ifint($tid))json_exit(array('flag'=>0,'msg'=>'zeai_tid_error'));
	$T = $db->ROW(__TBL_TREND__,"uid,agreelist,content","id=".$tid,"num");
	if ($T){
		$uid = $T[0];$agreelist = $T[1];$content = trimhtml($T[2]);
		$newlist   = array($cook_uid);
		if (!empty($agreelist)){
			$agreelist = explode(',',$agreelist);
			if (in_array($cook_uid,$agreelist)){
				exit(json_encode(array('flag'=>0,'msg'=>'不能重复点赞哦～')));
			}else{
				$newlist = array_merge($newlist,$agreelist);
			}
		}
		$arrnum = count($newlist);
		$newlist = implode(',',$newlist);
		$db->query("UPDATE ".__TBL_TREND__." SET agreelist='$newlist',agreenum = agreenum+1 WHERE id=".$tid);
		//
		$imgurl    = (!empty($data_photo_s) && $data_photo_f==1)?$_ZEAI['up2'].'/'.$data_photo_s:'res/photo_m'.$data_sex.'.png';
		$classname = (empty($data_photo_s) || $data_photo_f==0)?'sexbg'.$data_sex:'';
		
		//通知
		$row = $db->ROW(__TBL_USER__,'openid,subscribe',"id=".$uid,"num");
		if($row){
			$openid = $row[0];$subscribe = $row[1];
			//站内消息
			$title='您的交友圈/动态【'.$content.'】有人点赞了';
			$C = $title.'　　<a href='.Href('trend',$uid).' class=aQING>进入查看</a>';
			$db->SendTip($uid,$title,dataIO($C,'in',1000),'sys');
			//微信通知
			if (!empty($openid) && $subscribe==1){
				//客服通知
				$C = urlencode(dataIO($title.'<br>→<a href="'.HOST.'/?z=trend&submitok=my">【进入查看】</a>','wx'));
				$ret = @wx_kf_sent($openid,$C,'text');
				$ret = json_decode($ret);
				//模版通知
				if ($ret->errmsg != 'ok'){
					$first   = (!empty($first))?dataIO($first,'out'):'系统智能提醒！';
					$remark  = '';
					$keyword1  = $title;
					$keyword3  = urlencode($_ZEAI['siteName']);
					$url       = urlencode(HOST.'/?z=trend&submitok=my');
					@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
				}
			}
		}
		//通知END
		
		exit(json_encode(array('flag'=>'1','num'=>$arrnum,'uid'=>$cook_uid,'classname'=>$classname,'imgurl'=>$imgurl)));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_nodata_error'));
	}
}elseif($submitok == 'add'){
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-trend_add">&#xe602;</i>发表话题<a id="trend_btn_save">保存</a>';
$mini_backT = '返回';?>
<?php if($_ZEAI['mob_mbkind']==3){?><style>.top_mini{background:#FF6F6F}</style><?php }?>
<?php
require_once ZEAI.'m1/top_mini.php';
?>
<div class="submain">
    <form id='trendbox'>
        <textarea name="content" id="content" placeholder="这一刻的想法......"></textarea>
        <h4><span id="inpttext">0</span>/140</h4><ul><li></li></ul>
        <input type="hidden" name="morelist" id="morelist" value="">
    </form>
	<script>
		var ul = zeai.tag(o('trendbox'),"ul")[0],
		btnpic = ul.children[0],
		upMaxMB = <?php echo $_UP['upMaxMB']; ?>,
		browser='<?php if(is_h5app()){ echo 'app';}else{ echo (is_weixin())?'wx':'h5';}?>',
		up2='<?php echo $_ZEAI['up2'];?>/',trend_pic_Slist=[],localIds=[];
		trend_btn_save.onclick = trend_btn_saveFn;
		content.oninput = contentFn;
		btnpic.onclick = btnpicFn;
    </script> 
</div>
<?php exit;}
/***********************正文开始***************************/
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
$headertitle = '交友圈 - ';
require_once ZEAI.'m1/header.php';
 if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	</script>
<?php }
$mainT=($submitok=='my')?'我的交友圈':'交友圈';
$mini_title = '　　　'.$mainT.'<a id="trend_btn_add" class="ico">&#xe620;</a>';
$mini_class = 'top_mini huadong';
$mini_ext = 'id="topminibox"';
//require_once ZEAI.'m1/top_mini.php';
$nav = 'trend';
?>
<link href="m1/css/trend.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php if($_ZEAI['mob_mbkind']==3){?>
<style>
.trendtop li{border-color:#FF6F6F}
.trendtop li:nth-child(1){background-color:#FF6F6F}
.trendtop li:nth-child(2){color:#FF6F6F}
.trendmain dl dd h3 b{border-color:#f7dddd;background-color:#FFF5F5}
#backtop a, #btmKefuBtn {background-color:#FF6F6F}
</style>
<?php }?>
<div id="trendtop" class="trendtop huadong"><li>交友圈</li><li id="trend_btn_add"><i class="ico">&#xe620;</i>发表话题</li></div>
<main id="main" class="trendmain huadong">
    <div id="list">
		<?php
        if ($submitok == 'my'){
            $total = $db->COUNT(__TBL_TREND__,"uid=".$cook_uid);
        }else{
            $total = $db->COUNT(__TBL_TREND__," flag = 1");
        }
		$totalP = ceil($total/$_ZEAI['pagesize']);
        echo ajax_ulist_fn($totalP,1);
		?>
    </div>
</main>
<div class="trend_yd" id="trend_yd"><img src="m1/img/trend_yd.png"></div>
<?php if ($total > $_ZEAI['pagesize']){?>
<script>
	var totalP = parseInt(<?php echo $totalP; ?>),p=2;
	zeai.ready(function(){o('main').onscroll = trendOnscroll;});
</script>
<?php }?>
<?php 
if($_ZEAI['mob_mbkind']==3){
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}
?>
<script src="m1/js/trend.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
zeaiLoadBack=['nav','trendtop'];
var browser='<?php echo (is_weixin())?'wx':'h5';?>';
<?php
if (!empty($_GZH['wx_gzh_ewm']) && ($data_subscribe!=1)){
	wx_endurl('您刚刚浏览的页面【动态】',HOST.'/?z=trend');?>
	<?php
}
?>
trend_btn_add.onclick=function(){ZeaiM.page.load('m1/trend.php?submitok=add',ZEAI_MAIN,'trend_add');}
trendSetList();
if(zeai.empty(localStorage.trend_yd)){
	zeai.mask({fobj:main,son:trend_yd,cancelBubble:'off',close:function(){
		localStorage.trend_yd='WWWzeaiCN';
	}});
}

<?php if(is_weixin()){?>
var share_title = '交友圈 - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>',
share_desc  = '我在这个网站发布了朋友圈动态，来看看啊^_^',
share_link  = '<?php echo HOST; ?>/?z=trend',
share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
wx.ready(function () {
	wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
	wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
});
<?php }?>

<?php if ($submitok == 'my'){?>
main.scrollTop=220;
<?php }?>
</script>
<?php
function ajax_ulist_fn($totalP,$p) {
	global $_ZEAI,$db,$nodatatips,$RTSQL;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);

	if($totalP==0 && $p == 1)return $nodatatips;
	if ($p > $totalP)exit("end");
	
	$LIMIT = ($p == 1)?$_ZEAI['pagesize']:$_ZEAI['limit'];
	$RTSQL.=" LIMIT ".$LIMIT;
	$rt = $db->query($RTSQL);
	$total = $db->num_rows($rt);
	
	if ($p == 1){
		if ($total <= 0)return $nodatatips;
		$fort= $total;
	}else{
		if ($total <= 0)exit("end");
		$fort= $_ZEAI['pagesize'];
		$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);
	}
	$rows_ulist='';
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_ulist .= rows_ulist($rows);
	}
	return $rows_ulist;
}
function rows_ulist($rows) {
	global $_ZEAI,$db,$cook_uid;
	$id            = $rows['id'];
	$uid           = $rows['uid'];
	$piclist       = $rows['piclist'];
	$content       = dataIO($rows['content'],'out');
	$agreenum      = $rows['agreenum'];
	$agreelist     = $rows['agreelist'];
	$addtime_str   = date_str($rows['addtime']);
	$areatitle     = $rows['areatitle'];
	$area_s_title = explode(' ',$areatitle);$area_s_title = $area_s_title[1];
	//
	$sex           = $rows['sex'];
	$grade         = $rows['grade'];
	$nickname      = dataIO($rows['nickname'],'out');
	$nickname      = (empty($nickname))?'uid:'.$uid:$nickname;
	$photo_s       = $rows['photo_s'];
	$photo_f       = $rows['photo_f'];
	$photo_ifshow  = $rows['photo_ifshow'];
	$birthday      = $rows['birthday'];
	$pay           = $rows['pay'];
	$heigh         = $rows['heigh'];
	$edu           = $rows['edu'];
	//
	$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
	if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
	$ifh3 = ($birthday == '0000-00-00' && empty($heigh) && empty($pay) && empty($edu))?false:true;
	if ($ifh3){
		$area_str     = (empty($area_s_title))?'':'<b>'.$area_s_title.'</b>';
		$birthday_str = (getage($birthday)<=0)?'':'<b>'.getage($birthday).'岁</b>';
		$heigh_str    = (empty($heigh))?'':'<b>'.$heigh.'cm</b>';
		$edu_str      = (empty($edu))?'':'<b>'.udata('edu',$edu).'</b>';
		$pay_str      = (empty($pay))?'':'<b>'.udata('pay',$pay).'</b>';
	}
	$piclist_str = '';
	if (!empty($piclist)){
		$piclist = explode(',',$piclist);
		if (count($piclist) >= 1){foreach ($piclist as $value){$piclist_str .= '<img src="'.$_ZEAI['up2'].'/'.$value.'">';}}
	}
	$echo  ='<dl id="dl'.$id.'">';
	$echo .='<dt uid="'.$uid.'"><img src="'.$photo_s_url.'"'.$sexbg.'></dt>';
	$echo .='<dd>';
	$echo .='<h2>'.uicon($sex.$grade).$nickname.'</h2>';
	if ($ifh3){
	$echo .='<h3>'.$area_str.$birthday_str.$heigh_str.$edu_str.$pay_str.'</h3>';
	}
	if ($cook_uid == $uid){
		$echo .='<a clsid="'.$id.'" class="delmy">删除</a>';
	}
	$echo .= '<span class="time">'.$addtime_str.'</span>';
	//
	if(strstr($content,"photo_v")){
		preg_match('/<img.+src=\"?(.+\.(jpg))\"?.+>/i',$content,$match);
		$src = $match[1];
		$mp4 = str_replace('.jpg','.mp4',$src);
		$cvs = '<video class="zeaiVbox" id="zeaiVbox'.$id.'"><source src="'.$mp4.'">您浏览器本太低，请升级</video><span class="play ico">&#xe600;</span>';
		if(is_h5app()){ 		
			$vplay   = 'javascript:app_VideoPlayer("'.$mp4.'");';
		}else{
			$vplay   = 'zeaiplay("'.$id.'")';
		}
		
		$content = '<h1>上传了个人视频</h1><span class="vbox"><strong onclick='.$vplay.'><img src="'.$src.'">'.$cvs.'</strong></span>';
		$echo   .=$content;
	}else{
		$echo .='<h1>'.$content.'</h1>';
	}
	//
	if (!empty($piclist)){$echo .='<p>'.$piclist_str.'</p>';}
	
	$agreebbs='';
	//bbs
	$rt=$db->query("SELECT a.flag,a.uid,a.content,a.addtime,U.photo_s,U.photo_f,U.uname,U.nickname,U.sex,U.photo_ifshow FROM ".__TBL_TREND_BBS__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$id ORDER BY a.id DESC");
	$C = '';$k=0;
	WHILE ($rowsbbs = $db->fetch_array($rt,'name')){
		$k++;
		$flag     = $rowsbbs['flag'];
		$uid      = $rowsbbs['uid'];
		$sex      = $rowsbbs['sex'];
		$photo_s  = $rowsbbs['photo_s'];
		$photo_f  = $rowsbbs['photo_f'];
		$photo_ifshow = $rowsbbs['photo_ifshow'];
		$uname    = dataIO($rowsbbs['uname'],'out');
		$nickname = dataIO($rowsbbs['nickname'],'out');
		$nickname = (empty($nickname))?$uname:$nickname;
		$nickname = (ifmob($nickname))?$uid:$nickname;
		$content  = trimhtml(dataIO($rowsbbs['content'],'out'));
		$content  = ($flag==1)?$content:'<font class="C999">审核中</font>';
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
		$addtime    = date_str($rowsbbs['addtime']);
		$sexcolor = ($sex==2)?' class="Chong"':' class="Clan"';
		$C .= '<li>';
		$C .= '<a onClick="trend_uFn('.$uid.')"><img class="m" src="'.$photo_s_url.'" uid="'.$uid.'"><h5'.$sexcolor.'>'.$nickname.'：<font>'.$addtime.'</font></h5></a>';
		$C .= '<span>'.trimContact($content).'</span>';
		$C .= '</li>';
	}
	//bbs end
	//agree
	if (!empty($agreelist)){
		$agreelistarr= explode(',',$agreelist);
		$agree_class = (in_array($cook_uid,$agreelistarr))?' class="ed"':'';
		$agree_ulist = '';
		foreach ($agreelistarr as $uid2){
			$AU = $db->ROW(__TBL_USER__,"sex,photo_s,photo_f,photo_ifshow","id=".$uid2,"num");
			$sex2     = $AU[0];
			$photo_s2 = $AU[1];
			$photo_f2 = $AU[2];
			$photo_ifshow2 = $AU[3];
			$photo_s2_url = (!empty($photo_s2) && $photo_f2==1)?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_m'.$sex2.'.png';
			if($photo_ifshow2==0)$photo_s2_url=HOST.'/res/photo_m'.$sex2.'_hide.png';
			$agree_ulist .= '<img src="'.$photo_s2_url.'"'.$sexbg2.' uid="'.$uid2.'">';
		}
	}else{
		$agree_class = '';
	}
	$agreebbs ='<div class="agree" tid="'.$id.'"><i'.$agree_class.'><font class="ico">&#xe652;</font><font> 赞<b class="agreefly">+1</b><span>'.$agreenum.'</span>　</font></i><i><font class="ico S16">&#xe676;</font><font> 评<span>'.$k.'</span></font></i></div>';
	
	if ($agreenum>0 || $k>0){$agreebbs .='<div class="j"></div>';}
	if ($agreenum>0 && !empty($agreelist)){$agreebbs .='<em>'.$agree_ulist.'</em>';}
	if(!empty($C))$agreebbs .='<ul>'.$C.'</ul>';

	$echo .='</dd>';
	
	$echo .=$agreebbs;
	
	$echo .='</dl>';
	return $echo;
}
?>
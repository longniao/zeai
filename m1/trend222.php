<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
$currfields="sex,grade,nickname,photo_s,photo_f";
$chk_u_jumpurl=HOST.'/?z=trend';require_once ZEAI.'my_chk_u.php';
$data_sex=$row['sex'];
$data_grade=$row['grade'];
$data_photo_s=$row['photo_s'];
$data_photo_f=$row['photo_f'];
$data_nickname=dataIO($row['nickname'],'out');
$data_nickname = (empty($data_nickname))?'uid:'.$cook_uid:$data_nickname;

if($submitok == 'trend_bbs_add'){
	if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'zeai_fid_error'));
	if (empty($content) || str_len($content)>1000)json_exit(array('flag'=>0,'msg'=>'请输入内容(500字节以内)'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发表'));
	$content = dataIO($content,'in',1000);
	$db->query("INSERT INTO ".__TBL_TREND_BBS__." (uid,fid,content,addtime) VALUES ($cook_uid,$fid,'$content',".ADDTIME.")");
	setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
	$C .= '<li>';
	$C .= '<a onClick=\'trend_uFn('.$cook_uid.')\'><h5>'.str_replace('"',"'",uicon($data_sex.$data_grade)).'<font>'.$data_nickname.'：</font></h5></a>';
	$C .= '<span>'.trimContact($content).'</span>';
	$C .= '</li>';
	json_exit(array('flag'=>1,'msg'=>'发表成功','C'=>$C));
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
	$SQL = " a.flag=1 AND ";
}
$RTSQL = "SELECT a.id,a.uid,a.piclist,a.content,a.agreenum,a.agreelist,a.addtime,b.sex,b.grade,b.nickname,b.photo_s,b.photo_f,b.birthday,b.pay,b.heigh,b.edu FROM ".__TBL_TREND__." a,".__TBL_USER__." b WHERE ".$SQL." a.uid=b.id AND b.flag=1 ORDER BY a.id DESC";
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有动态～～</div>";

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
					$_s = setpath_s($dbname);
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
	setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
	$db->query("INSERT INTO ".__TBL_TREND__." (uid,piclist,content,addtime) VALUES ($cook_uid,'$piclist','$content',".ADDTIME.")");
	json_exit(array('flag'=>1,'msg'=>'发布成功'));
}elseif($submitok == 'delmy'){
	if(!ifint($id))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TREND__,"piclist","piclist<>'' AND uid=".$cook_uid." AND id=".$id);
	if ($row){
		$piclist= $row[0];
		$sARR = explode(',',$piclist);
		if(count($sARR) >0){
			foreach ($sARR as $value) {
				@up_send_userdel($value.'|'.getpath_smb($value,'b'));
			}
		}
	}
	$db->query("DELETE FROM ".__TBL_TREND__." WHERE id=".$id." AND uid=".$cook_uid);
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
}elseif($submitok == 'ajax_agree'){
	if(!ifint($tid))json_exit(array('flag'=>0,'msg'=>'zeai_tid_error'));
	$T = $db->ROW(__TBL_TREND__,"agreelist","id=".$tid);
	if ($T){
		$agreelist = $T[0];
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
		exit(json_encode(array('flag'=>'1','num'=>$arrnum,'uid'=>$cook_uid,'classname'=>$classname,'imgurl'=>$imgurl)));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_nodata_error'));
	}
}elseif($submitok == 'add'){
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-trend_add">&#xe602;</i>发布动态<a id="trend_btn_save">保存</a>';
$mini_backT = '返回';
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
		browser='<?php echo (is_weixin())?'wx':'h5';?>',
		up2='<?php echo $_ZEAI['up2'];?>/',trend_pic_Slist=[],localIds=[];
		trend_btn_save.onclick = trend_btn_saveFn;
		content.oninput = contentFn;
		btnpic.onclick = btnpicFn;
    </script> 
</div>
<?php exit;}
/***********************正文开始***************************/
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
$headertitle = '会员动态 - ';
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
$mainT=($submitok=='my')?'我的动态':'会员动态';
$mini_title = '　　　'.$mainT.'<a id="trend_btn_add" class="ico">&#xe620;</a>';
$mini_class = 'top_mini huadong';
$mini_ext = 'id="topminibox"';
require_once ZEAI.'m1/top_mini.php';
$nav = 'trend';
$iffind=true;
if(@!in_array('dating',$navarr) && @!in_array('video',$navarr) && @!in_array('group',$navarr) && @!in_array('hb',$navarr)  && @!in_array('party',$navarr))$iffind=false;
?>
<link href="m1/css/trend.css?3" rel="stylesheet" type="text/css" />
<main id="main" class="trendmain huadong">
	<?php if ($iffind){?>
    <div class="xnav">
        <?php if(@in_array('trend',$navarr)){?><li class="trend" onClick="zeai.openurl('<?php echo HOST;?>/?z=trend')"><i></i><h2 class="ed1">动态</h2></li><?php }?>
        <?php if(@in_array('video',$navarr)){?><li class="video" onClick="zeai.openurl('<?php echo HOST;?>/?z=video')"><i></i><h2>视频</h2></li><?php }?>
        <?php if(@in_array('dating',$navarr)){?><li class="dating" onClick="zeai.openurl('<?php echo HOST;?>/?z=dating')"><i></i><h2>约会</h2></li><?php }?>
        <?php if(@in_array('party',$navarr)){?><li class="party" onClick="zeai.openurl('<?php echo HOST;?>/?z=party')"><i></i><h2>活动</h2></li><?php }?>
        <?php if(@in_array('group',$navarr)){?><li class="group" onClick="zeai.openurl('m1/group')"><i></i><h2>圈子</h2></li><?php }?>
        <?php if(@in_array('hb',$navarr)){?><li class="hb" onClick="zeai.openurl('<?php echo HOST;?>/m1/hongbao')"><i></i><h2>红包</h2></li><?php }?>
    </div>
    <?php }?>
    <!--主BOX-->
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
	zeai.ready(function(){o('main').onscroll = trendOnscroll;	});
</script>
<?php }?>
<?php 
require_once ZEAI.'m1/bottom.php';
?>
<script src="m1/js/trend.js?3"></script>
<script>
zeaiLoadBack=['nav','topminibox'];
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
var share_title = '会员动态 - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>',
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
	//
	$sex           = $rows['sex'];
	$grade         = $rows['grade'];
	$nickname      = dataIO($rows['nickname'],'out');
	$nickname      = (empty($nickname))?'uid:'.$uid:$nickname;
	$photo_s       = $rows['photo_s'];
	$photo_f       = $rows['photo_f'];
	$birthday      = $rows['birthday'];
	$pay           = $rows['pay'];
	$heigh         = $rows['heigh'];
	$edu           = $rows['edu'];
	//
	$sexbg        = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
	$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
	$ifh3 = ($birthday == '0000-00-00' && empty($heigh) && empty($pay) && empty($edu))?false:true;
	if ($ifh3){
		$birthday_str = (getage($birthday)<=0)?'':'<b>'.getage($birthday).'岁</b>';
		$heigh_str    = (empty($heigh))?'':'<b>'.$heigh.'厘米</b>';
		$edu_str      = (empty($edu))?'':'<b>'.udata('edu',$edu).'</b>';
		$pay_str      = (empty($pay))?'':'<b>'.udata('pay',$pay).'</b>';
	}
	$piclist_str = '';
	if (!empty($piclist)){
		$piclist = explode(',',$piclist);
		if (count($piclist) >= 1){foreach ($piclist as $value){$piclist_str .= '<img src="'.$_ZEAI['up2'].'/'.$value.'">';}}
	}
	if (!empty($agreelist)){
		$agreelistarr= explode(',',$agreelist);
		$agree_class = (in_array($cook_uid,$agreelistarr))?' class="ed"':'';
		$agree_ulist = '';
		foreach ($agreelistarr as $uid2){
			$AU = $db->ROW(__TBL_USER__,"sex,photo_s,photo_f","id=".$uid2,"num");
			$sex2     = $AU[0];
			$photo_s2 = $AU[1];
			$photo_f2 = $AU[2];
			$photo_s2_url = (!empty($photo_s2) && $photo_f2==1)?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_m'.$sex2.'.png';
			$sexbg2      = (empty($photo_s2) || $photo_f2==0)?' class="sexbg'.$sex2.'"':'';
			$agree_ulist .= '<img src="'.$photo_s2_url.'"'.$sexbg2.' uid="'.$uid2.'">';
		}
	}else{
		$agree_class = '';
	}
	$echo  ='<dl id="dl'.$id.'">';
	$echo .='<dt uid="'.$uid.'"><img src="'.$photo_s_url.'"'.$sexbg.'></dt>';
	$echo .='<dd>';
	$echo .='<h2>'.$nickname.uicon($sex.$grade).'</h2>';
	if ($ifh3){
	$echo .='<h3>'.$birthday_str.$heigh_str.$edu_str.$pay_str.'</h3>';
	}
	if ($cook_uid == $uid){
	$echo .='<a clsid="'.$id.'" class="delmy">删除</a>';
	}
	//
	if(strstr($content,"photo_v")){
		preg_match('/<img.+src=\"?(.+\.(jpg))\"?.+>/i',$content,$match);
		$src = $match[1];
		$mp4 = str_replace('.jpg','.mp4',$src);
		$cvs = '<video class="zeaiVbox" id="zeaiVbox'.$id.'"><source src="'.$mp4.'">您浏览器本太低，请升级</video><span class="play ico">&#xe600;</span>';
		$vplay   = 'zeaiplay("'.$id.'")';
		$content = '<h1>上传了个人视频</h1><span class="vbox"><strong onclick='.$vplay.'><img src="'.$src.'">'.$cvs.'</strong></span>';
		$echo   .=$content;
	}else{
		$echo   .='<h1>'.$content.'</h1>';
	}
	//
	if (!empty($piclist)){$echo .='<p>'.$piclist_str.'</p>';}
	$echo .='<div class="agree"><span>'.$addtime_str.'</span><i'.$agree_class.' tid="'.$id.'"></i><i></i><i>'.$agreenum.'</i></div>';
	
	
	if ($agreenum>0 && !empty($agreelist)){$echo .='<div class="j"></div><em>'.$agree_ulist.'</em>';}
	//bbs
	$rt=$db->query("SELECT a.uid,a.content,U.uname,U.nickname,U.sex,U.grade FROM ".__TBL_TREND_BBS__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$id ORDER BY a.id DESC");
	$C = '';$k=0;
	WHILE ($rows = $db->fetch_array($rt,'name')){
		$k++;
		$uid      = $rows['uid'];
		$sex      = $rows['sex'];
		$photo_s  = $rows['photo_s'];
		$photo_f  = $rows['photo_f'];
		$grade    = $rows['grade'];
		$uname    = dataIO($rows['uname'],'out');
		$nickname = dataIO($rows['nickname'],'out');
		$nickname = (empty($nickname))?$uname:$nickname;
		$nickname = (ifmob($nickname))?$uid:$nickname;
		$content  = trimhtml(dataIO($rows['content'],'out'));
		$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		$addtime    = date_str($rows['addtime']);
		$C .= '<li>';
		$C .= '<a onClick="trend_uFn('.$uid.')"><h5>'.uicon($sex.$grade).'<font>'.$nickname.'：</font></h5></a>';
		$C .= '<span>'.trimContact($content).'</span>';
		$C .= '</li>';
	}
	//$C=(!empty($C))?'<ul>'.$C.'</ul>':'';
	//if($k>0)$echo .=$C;
	
	$echo .='<ul>'.$C.'</ul>';
	//bbs end
	
	$echo .='</dd></dl>';
	return $echo;
}
?>
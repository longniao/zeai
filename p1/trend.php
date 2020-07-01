<?php
ob_start();
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/udata.php';
//
require_once ZEAI.'cache/config_vip.php';
$trend_bbsflag = json_decode($_VIP['trend_bbsflag'],true);
//
if($submitok == 'ajax_agree' || $submitok == 'ajax_bbs_add_update' || $submitok == 'ajax_chklogin'){
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请您先登录后再来点赞评论','jumpurl'=>Href('trend')));
}
if($submitok == 'ajax_agree'){
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
		$row = $db->NAME($cook_uid,"sex,photo_s,photo_f");
		$data_sex    =$row['sex'];
		$data_photo_s=$row['photo_s'];
		$data_photo_f=$row['photo_f'];
		$imgurl    = (!empty($data_photo_s) && $data_photo_f==1)?$_ZEAI['up2'].'/'.$data_photo_s:HOST.'/res/photo_s'.$data_sex.'.png';
		$classname = (empty($data_photo_s) || $data_photo_f==0)?' class="sexbg'.$data_sex.'"':'';
		$C = '<a href="'.Href('u',$cook_uid).'" target="_blank"><img src="'.$imgurl.'"'.$classname.'></a>';
		exit(json_encode(array('flag'=>1,'num'=>$arrnum,'C'=>$C)));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_nodata_error'));
	}
}elseif($submitok == 'ajax_chklogin'){
	json_exit(array('flag'=>1,'msg'=>'已登录'));
}elseif($submitok == 'ajax_bbs_add_update'){
	if(!ifint($tid))json_exit(array('flag'=>0,'msg'=>'zeai_tid_error'));
	if (empty($content))json_exit(array('flag'=>0,'msg'=>'请输入内容'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发表'));
	$content = dataIO($content,'in',280);
	if(ifint($cook_uid)){
		$row = $db->ROW(__TBL_USER__,"grade","id=".$cook_uid);
		if ($row){
			$data_grade = $row[0];
			$flag     = intval($trend_bbsflag[$data_grade]);
			$flag_str = ($flag==0)?'，请等待我们审核':'';
			$db->query("INSERT INTO ".__TBL_TREND_BBS__." (uid,fid,content,addtime,flag) VALUES ($cook_uid,$tid,'$content',".ADDTIME.",$flag)");
			setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
		}
	}
	json_exit(array('flag'=>1,'msg'=>'发表成功'.$flag_str));
}
//
$nav='trend';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>会员交友圈_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/rex/www_esyyw_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/trend.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/rex/www_esyyw_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/select3.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>var nulltext = '请选择';
var nulltext = '不限';
var mate_sex_ARR = sex_ARR,mate_age_ARR = age_ARR,mate_heigh_ARR = heigh_ARR,mate_pay_ARR = pay_ARR,mate_edu_ARR = edu_ARR,mate_love_ARR = love_ARR,mate_house_ARR = house_ARR,mate_areaid_ARR1 = areaARR1,mate_areaid_ARR2 = areaARR2,mate_areaid_ARR3 = areaARR3;
</script>
</head>
<body>
<?php
if ($submitok == 'trend_bbs_add'){?>
	<style>body{background-color:#fff}</style>
	<div class="partybbs_add">
    	<h1>发表评论</h1>
        <form id='trendZ_eA_I____cn_bbsbox'>
            <textarea name="content" id="content" placeholder="请文明发言~~" class="textarea"></textarea>
            <h4><span id="inpttext">0</span>/140</h4>
            <input type="hidden" name="submitok" value="ajax_bbs_add_update">
            <input type="hidden" name="tid" value="<?php echo $tid;?>">
            <button type="button" id="trendbbs_btn_save" class="btn size3 HONG">提交评论</button>
        </form>
    </div>
	<script src="<?php echo HOST;?>/p1/js/trend.js"></script>
	<script>trendbbs_btn_save.onclick = trendbbs_btn_saveFn;content.oninput = contentFn;</script> 
	<?php echo '</body></html>';exit;
}
require_once ZEAI.'p1/top.php';?>
<div class="main trend fadeInL">
	<div class="trendL">
    	<div class="" id="list">
			<?php
			$SQL="";
			if (ifint($uid)){
				$_ZEAI['pagesize']= 500;
				$SQL = " AND a.uid=".$uid."  ";
			}
			//
			if (ifint($mate_sex,'1-2','1'))$SQL .= " AND b.sex='$mate_sex' ";
			$areaid = '';
			if (ifint($m1) && ifint($m2) && ifint($m3)){
				$areaid = $m1.','.$m2.','.$m3;
			}elseif(ifint($m1) && ifint($m2)){
				$areaid = $m1.','.$m2;
			}elseif(ifint($m1)){
				$areaid = $m1;
			}
			if (!empty($areaid))$SQL .= " AND b.areaid LIKE '%".$areaid."%' ";
			if (ifint($mate_age1))$SQL .= " AND ( YEAR(NOW()) - YEAR(b.birthday) >= '$mate_age1' ) ";
			if (ifint($mate_age2))$SQL .= " AND ( YEAR(NOW()) - YEAR(b.birthday) <= '$mate_age2' ) ";
			if (ifint($mate_heigh1))$SQL .= " AND ( b.heigh >= '$mate_heigh1' ) ";
			if (ifint($mate_heigh2))$SQL .= " AND ( b.heigh <= '$mate_heigh2' ) ";
			if (ifint($mate_pay))$SQL .= " AND b.pay>='$mate_pay' ";
			if (ifint($mate_edu))$SQL .= " AND b.edu>='$mate_edu' ";
			if (ifint($mate_love))$SQL .= " AND b.love='$mate_love' ";
			if (!empty($k)){
				$k = dataIO(trimhtml($k),'in');
				if (ifint($k)){
					$SQL .= " AND b.id='$k' ";
				}else{
					$SQL .= " AND b.nickname LIKE '%".$k."%' ";
				}
			}
			$RTSQL = "SELECT a.id,a.uid,a.piclist,a.content,a.agreenum,a.agreelist,a.addtime,b.sex,b.grade,b.nickname,b.love,b.photo_s,b.photo_f,b.birthday,b.pay,b.job,b.heigh,b.edu,b.areatitle,b.RZ,b.photo_ifshow FROM ".__TBL_TREND__." a,".__TBL_USER__." b WHERE a.uid=b.id AND b.flag=1 AND a.flag=1  ".$SQL." ORDER BY a.id DESC";
			$rt=$db->query($RTSQL);
			$total = $db->num_rows($rt);
			if ($total > 0) {
				$page_skin='4_yuan';$pagemode=4;$pagesize=5;$page_color='#E83191';require_once ZEAI.'sub/page.php';
				for($i=1;$i<=$pagesize;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows)break;
					$rows_ulist .= rows_ulist($rows);
				}
				echo $rows_ulist;
			}else{echo '<br><br><br><br>'.nodatatips('暂时还没有交友圈<br><br><a onclick="trend_add();"class="size2 btn HONG">我要发表</a>');}
			if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox zeaipagebox">'.$pagelist.'</div>';
            ?>
		</div>
	</div>
	<div class="trendR ">
        <div class="box S5">
			<h1>发表话题</h1>
            <div class="addbox">
                <a href="javascript:;" class="ed" onclick="trend_add();"><i class="ico2">&#xe613;</i>　我要发表</a>
                <a href="<?php echo HOST;?>/p1/my_trend.php?t=1">管理我的交友圈</a>
            </div>
		</div>
        <div class="so box S5">
			<h1>按条件筛选</h1>
            <form method="get" action="<?php echo HOST;?>/p1/trend.php" name="YZLOVE_com.form" id="GYLform7" class="form">
                <dl><dt>性　　别</dt><dd><script>zeai_cn__CreateFormItem('radio','mate_sex','<?php echo $mate_sex; ?>');</script></dd></dl>
                <dl><dt>年　　龄</dt><dd><script>zeai_cn__CreateFormItem('select','mate_age1','<?php echo $mate_age1; ?>','class="select SW0"',mate_age_ARR);</script> - <script>zeai_cn__CreateFormItem('select','mate_age2','<?php echo $mate_age2; ?>','class="select SW0"',mate_age_ARR);</script></dd></dl>
                <dl><dt>身　　高</dt><dd><script>zeai_cn__CreateFormItem('select','mate_heigh1','<?php echo $mate_heigh1; ?>','class="select SW0"',mate_heigh_ARR);</script> - <script>zeai_cn__CreateFormItem('select','mate_heigh2','<?php echo $mate_heigh2; ?>','class="select SW0"',mate_heigh_ARR);</script><font></font></dd></dl>
                <dl><dt>最低月薪</dt><dd><script>zeai_cn__CreateFormItem('select','mate_pay','<?php echo $mate_pay; ?>','class="select SW"',mate_pay_ARR);</script></dd></dl>
                <dl><dt>最低学历</dt><dd><script>zeai_cn__CreateFormItem('select','mate_edu','<?php echo $mate_edu; ?>','class="select SW"',mate_edu_ARR);</script></dd></dl>
                <dl><dt>所在地区</dt><dd><script>LevelMenu3('m1|m2|m3|'+nulltext+'|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>','class="select SW SWarea"');</script></dd></dl>
                <dl><dt>婚姻状况</dt><dd><script>zeai_cn__CreateFormItem('select','mate_love','<?php echo $mate_love; ?>','class="select SW"',mate_love_ARR);</script></dd></dl>
                <dl><dt><input type="hidden" name="t" value="7" />&nbsp;</dt><dd><button type="submit" class="btn size3 HONG3 W100_"><i class="ico">&#xe6c4;</i> 开始筛选</button></dd></dl>
            </form>
		</div>
        <div class="soK box S5">
        	<h1>按昵称UID</h1>
            <form method="get" action="<?php echo HOST.'/p1/trend.php';?>" name="ZEAI.cn.form1" id="GYLform1">
            <input name="k" type="text" class="input" maxlength="30" placeholder="按会员昵称/UID"><button type="submit" class="btn size3 HONG S14"><i class="ico">&#xe6c4;</i> 搜索</button>
            </form>
		</div>
	</div>
</div>
<?php if ($total > $_ZEAI['pagesize']){?>
<?php }?>
<script src="<?php echo HOST;?>/p1/js/trend.js"></script>
<div class="clear"></div>
<?php
require_once ZEAI.'p1/bottom.php';
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
	$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
	$photo_s       = $rows['photo_s'];
	$photo_f       = $rows['photo_f'];
	$birthday      = $rows['birthday'];
	$pay           = $rows['pay'];
	$heigh         = $rows['heigh'];
	$edu           = $rows['edu'];
	$areatitle     = $rows['areatitle'];
	$RZ            = $rows['RZ'];
	$love          = $rows['love'];
	$job           = $rows['job'];
	$photo_ifshow  = $rows['photo_ifshow'];
	$sexbg        = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
	$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
	if($photo_ifshow==0)$photo_s_url=HOST.'/res/photo_m'.$sex.'_hide.png';
	$ifh3 = ($birthday == '0000-00-00' && empty($heigh) && empty($pay) && empty($edu))?false:true;
	if ($ifh3){
		$birthday_str = (getage($birthday)<=0)?'':'<b>'.getage($birthday).'岁</b>';
		$heigh_str    = (empty($heigh))?'':'<b>'.$heigh.'厘米</b>';
		$love_str     = (empty($edu))?'':'<b>'.udata('love',$love).'</b>';
		$edu_str      = (empty($edu))?'':'<b>'.udata('edu',$edu).'</b>';
		$job_str      = (empty($job))?'':'<b>'.udata('job',$job).'</b>';
		$pay_str      = (empty($pay))?'':'<b>'.udata('pay',$pay).'</b>';
	}
	$areatitle_str =(empty($areatitle))?'':'<b>'.$areatitle.'</b>';
	$piclist_str = '';
	if (!empty($piclist)){
		$piclist = explode(',',$piclist);
		if (count($piclist) >= 1){foreach ($piclist as $value){$piclist_str .= '<span value="'.$_ZEAI['up2'].'/'.$value.'"></span>';}}
	}
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
			$photo_s2_url = (!empty($photo_s2) && $photo_f2==1)?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_s'.$sex2.'.png';
			if($photo_ifshow2==0)$photo_s2_url=HOST.'/res/photo_m'.$sex2.'_hide.png';
			$agree_ulist .= '<a href="'.Href('u',$uid2).'"><img src="'.$photo_s2_url.'"'.$sexbg2.'></a>';
		}
	}else{
		$agree_class = '';
	}
	if (ifint($cook_uid,'0-9','1,8')){$ifgz = gzflag($uid,$cook_uid);}else{$ifgz = 0;}
	$gzcls   = ($ifgz == 1)?' class="ed"':'';$gztitle = ($ifgz == 1)?'<i class="ico">&#xe6b1;</i> 已关注':'<i class="ico">&#xe620;</i> 加关注';
	//
	$echo  = '<div class="box trendmain S5">';
	$echo .='<dl id="dl'.$id.'">';
	//$echo .='<dt uid="'.$uid.'"><a href="'.Href('u',$uid).'" target="_blank"><img src="'.$photo_s_url.'"'.$sexbg.'></a><a href="javascript:;"'.$gzcls.'>'.$gztitle.'</a></dt>';
	$echo .='<dt uid="'.$uid.'"><a href="'.Href('u',$uid).'" target="_blank" '.$sexbg.'><span value="'.$photo_s_url.'"></span></a><a href="javascript:;"'.$gzcls.'>'.$gztitle.'</a></dt>';
	$echo .='<dd>';
	$echo .='<h2>'.uicon($sex.$grade).'<b>'.$nickname.'</b><font>'.RZ_star($RZ).'</font></h2>';
	if ($ifh3){$echo .='<h3>'.$birthday_str.$love_str.$heigh_str.$edu_str.$job_str.$pay_str.$areatitle_str.'</h3>';}
	if(strstr($content,"photo_v")){
		preg_match('/<img.+src=\"?(.+\.(jpg))\"?.+>/i',$content,$match);
		$src = $match[1];
		$cvs = '<video class="zeaiVbox" id="zeaiVbox'.$id.'" controls="controls" controlslist="nodownload"><source>您浏览器版本太低，请升级</video><span class="play ico">&#xe600;</span>';
		$vplay   = 'zeaiplay("'.$id.'")';
		$content = '<h1>上传了个人视频</h1><span class="vbox"><strong onclick='.$vplay.'><img src="'.$src.'" id="img'.$id.'">'.$cvs.'</strong></span>';
		$echo   .=$content;
	}else{
		$echo   .='<h1>'.$content.'</h1>';
	}
	if (!empty($piclist)){$echo .='<p>'.$piclist_str.'</p>';}
	//bbs
	$rt=$db->query("SELECT a.flag,a.uid,a.content,U.uname,U.nickname,U.sex,U.grade FROM ".__TBL_TREND_BBS__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$id ORDER BY a.id DESC");
	$C = '';$k=0;
	WHILE ($rows = $db->fetch_array($rt,'name')){
		$k++;
		$flag     = $rows['flag'];
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
		$content  = ($flag==1)?$content:'<font class="C999">审核中</font>';
		$addtime    = date_str($rows['addtime']);
		$C .= '<li>';
		$C .= '<a href="'.Href('u',$uid).'" target="_blank">';
		$C .= '<h5>'.uicon($sex.$grade).'<font>'.$nickname.'：</font></h5></a>';
		$C .= '<span>'.trimContact($content).'</span>';
		$C .= '</li>';
	}
	$C=(!empty($C))?'<ul>'.$C.'</ul>':'';
	//bbs end
	$echo .='<div class="agree"><span>'.$addtime_str.'</span><i'.$agree_class.' tid="'.$id.'" title="点赞"></i><i tid="'.$id.'" title="评论"></i><i>'.$agreenum.'</i></div>';
	if ($agreenum>0 && !empty($agreelist)){ 
		$echo .='<div class="j"></div><em>'.$agree_ulist.'</em>';
	}
	if($k>0)$echo .=$C;
	$echo .='</dd></dl>';
	$echo .='</div>';
	return $echo;
}
?>

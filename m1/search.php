<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
$currfields = "birthday,loveb,photo_f,photo_s,heigh,pay,refresh_time,dataflag";
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=search';
require_once ZEAI.'my_chk_u.php';
/*******************************************/
if($submitok=='ulist' || $submitok == 'ajax_ulist'){
	require_once ZEAI.'cache/udata.php';
	$_ZEAI['pagesize']= 8;
	$SQL   = " flag=1 AND dataflag=1 AND kind<>4 ";/* AND photo_s<>'' AND photo_f=1*/
	$fields="id,sex,grade,nickname,photo_s,photo_f,birthday,pay,job,RZ,photo_ifshow";
	$SQL2   = " a.flag=1 AND a.dataflag=1 ";
	$fields2="a.id,a.sex,a.grade,a.nickname,a.photo_s,a.photo_f,a.birthday,a.pay,a.job,a.RZ,a.photo_ifshow";
	$ORDER = " ORDER BY refresh_time DESC ";
	
	switch ($t) {
		//按推荐
		case 1:
			switch ($sokind) {
				case 'vip':$mt = 'VIP高级会员';
					$SQL  .= "AND grade>1";
					$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY grade DESC,refresh_time DESC";
					$TP=getTP($SQL);$total=$TP[0];$totalP=$TP[1];
				break;
				case 'new':$mt='最新注册会员';
					$RTSQL= "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY id DESC";
					$TP=getTP($SQL);$total=$TP[0];$totalP=$TP[1];
				break;
				case 'endtime':$mt='最近登录会员';
					$RTSQL= "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY endtime DESC";
					$TP=getTP($SQL);$total=$TP[0];$totalP=$TP[1];
				break;
				case 'endgift':$mt='最近送礼会员';
					$RTSQL = "SELECT ".$fields2." FROM ".__TBL_USER__." a,(SELECT * FROM ".__TBL_GIFT_USER__." ORDER BY id DESC) b WHERE ".$SQL2." AND a.id=b.senduid GROUP BY b.senduid ";
					$TP=getTP($RTSQL,true);$total=$TP[0];$totalP=$TP[1];
					$RTSQL .= " ORDER BY b.id DESC";
				break;
				case 'endpay':$mt='最近充值会员';
					$RTSQL = "SELECT ".$fields2." FROM ".__TBL_USER__." a,(SELECT * FROM ".__TBL_PAY__." ORDER BY id DESC) b WHERE ".$SQL2." AND a.id=b.uid AND b.kind>0 AND b.flag=1 GROUP BY b.uid";
					$TP=getTP($RTSQL,true);$total=$TP[0];$totalP=$TP[1];
					$RTSQL .= " ORDER BY b.id DESC";
				break;
				case 'click':$mt='人气会员';
					$RTSQL= "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY click DESC";
					$TP=getTP($SQL);$total=$TP[0];$totalP=$TP[1];
				break;
				case 'cert':$mt='认证会员';
					$RTSQL= "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL." AND RZ<>'' ".$ORDER;
					$TP=getTP($SQL);$total=$TP[0];$totalP=$TP[1];
				break;
				case 'offline':$mt='线下优质会员';
					$Garr = getSmodeUGarr();
					if (!empty($Garr) && str_len($Garr)>1){
						$SQL .= " AND grade IN($Garr) ";
						$RTSQL= "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
						$TP=getTP($SQL);$total=$TP[0];$totalP=$TP[1];
					}else{$total=0;$totalP=0;}
				break;
				case 'photo':$mt='最近上传相册会员';
					$RTSQL = "SELECT ".$fields2." FROM ".__TBL_USER__." a,(SELECT * FROM ".__TBL_PHOTO__." ORDER BY id DESC) b WHERE ".$SQL2." AND a.id=b.uid AND b.flag=1 GROUP BY b.uid";
					$TP=getTP($RTSQL,true);$total=$TP[0];$totalP=$TP[1];
					$RTSQL .= " ORDER BY b.id DESC";
				break;
				case 'loveb':$mt='土豪会员';
					$RTSQL= "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL." ORDER BY loveb DESC";
					$TP=getTP($SQL);$total=$TP[0];$totalP=$TP[1];
				break;
				case 'fans':$mt='最有魅力会员';
					$RTSQL= "SELECT ".$fields.",(SELECT COUNT(*) FROM ".__TBL_GZ__." WHERE uid=a.id AND flag=1 ) AS fansnum FROM ".__TBL_USER__." a WHERE ".$SQL." ORDER BY fansnum DESC,refresh_time DESC";
					$TP=getTP($SQL);$total=$TP[0];$totalP=$TP[1];
				break;
				default:exit(JSON_ERROR);break;
			}
			$cs = "t=".$t."&sokind=".$sokind."&sokind=".$sokind;
		break;
		//按条件
		case 2:
			if ($cook_sex == 1 && empty($keyword))$SQL .= " AND sex=2 ";
			if ($cook_sex == 2 && empty($keyword))$SQL .= " AND sex=1 ";
			if ($ifphoto == 1)$SQL .= " AND photo_s<>'' AND photo_f=1 ";
			$areaid = '';
			if (ifint($m1) && ifint($m2) && ifint($m3)){
				$areaid = $m1.','.$m2.','.$m3;
			}elseif(ifint($m1) && ifint($m2)){
				$areaid = $m1.','.$m2;
			}elseif(ifint($m1)){
				$areaid = $m1;
			}
			if (!empty($areaid))$SQL .= " AND areaid LIKE '%".$areaid."%' ";
			if (ifint($mate_age1))$SQL .= " AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) >= '$mate_age1' ) ";
			if (ifint($mate_age2))$SQL .= " AND (  (YEAR(CURDATE())-YEAR(birthday)-(RIGHT(CURDATE(),5)<RIGHT(birthday,5))) <= '$mate_age2' ) ";
			if (ifint($mate_heigh1))$SQL .= " AND ( heigh >= '$mate_heigh1' ) ";
			if (ifint($mate_heigh2))$SQL .= " AND ( heigh <= '$mate_heigh2' ) ";
			if (ifint($mate_weigh1))$SQL .= " AND ( heigh >= '$mate_weigh1' ) ";
			if (ifint($mate_weigh2))$SQL .= " AND ( heigh <= '$mate_weigh2' ) ";
			if (ifint($mate_pay))$SQL .= " AND pay>='$mate_pay' ";
			if (ifint($mate_edu))$SQL .= " AND edu>='$mate_edu' ";
			if (ifint($mate_love))$SQL .= " AND love='$mate_love' ";
			if (ifint($mate_house))$SQL .= " AND house='$mate_house' ";
			if (ifint($mate_car))$SQL .= " AND car='$mate_car' ";
			if (ifint($mate_job))$SQL .= " AND job='$mate_job' ";
			if (ifint($mate_marrytime))$SQL .= " AND marrytime='$mate_marrytime' ";
			$cs = "t=".$t."&sokind=".$sokind."&ifphoto=".$ifphoto."&mate_age1=".$mate_age1."&mate_age2=".$mate_age2."&mate_heigh1=".$mate_heigh1."&mate_heigh2=".$mate_heigh2."&mate_weigh1=".$mate_weigh1."&mate_weigh2=".$mate_weigh2."&mate_pay=".$mate_pay."&mate_edu=".$mate_edu."&m1=".$m1."&m2=".$m2."&m3=".$m3."&mate_love=".$mate_love."&mate_house=".$mate_house."&mate_car=".$mate_car."&mate_job=".$mate_job."&mate_marrytime=".$mate_marrytime."&keyword=".$keyword;
			$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
			//
		    $rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");
		    $row   = $db->fetch_array($rt,'num');
		    $total = $row[0];
		    $totalP= ceil($total/$_ZEAI['pagesize']);
		break;
		//按网名
		case 3:
			if (!empty($keyword)){
				$keyword = dataIO($keyword,'in');
				if (ifint($keyword)){
					$SQL .= " AND id='$keyword' ";
				}else{
					$SQL .= " AND nickname LIKE '%".$keyword."%' ";
				}
			}
			$cs = "t=".$t."&sokind=".$sokind."&keyword=".$keyword;
			$RTSQL = "SELECT ".$fields." FROM ".__TBL_USER__." WHERE ".$SQL.$ORDER;
			//
		    $rt    = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$SQL." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");
		    $row   = $db->fetch_array($rt,'num');
		    $total = $row[0];
		    $totalP= ceil($total/$_ZEAI['pagesize']);
		break;
		default:exit(JSON_ERROR);break;
	}
	//
	$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员～～</div>";
	if($submitok == 'ajax_ulist')exit(ajax_ulist_fn($totalP,$p));
	//
	switch ($t){case 1:$mini_title= $mt;break;case 2:$mini_title= '按条件';break;case 3:$mini_title= '按网名';break;}
	$mini_title .= '<i class="ico goback" id="ZEAIGOBACK-search_ulist">&#xe602;</i>';
    $mini_class = 'top_mini top_miniBAI';
    require_once ZEAI.'m1/top_mini.php';
    ?>
    <div class="submain search_ulist huadong" id="searchUlist"><?php echo ajax_ulist_fn($totalP,1);?></div><script>
	var scs='<?php echo $cs; ?>';
	<?php
	if ($total > $_ZEAI['pagesize']){?>
		var ifmore_so=true,totalP_so = parseInt(<?php echo $totalP; ?>),p_so;o('searchUlist').onscroll = searchOnscroll;	
	<?php }else{?>
		var ifmore_so=false;
	<?php }?>
	searchInit();
    </script>
<?php exit;}
/*******************************************/
if($submitok == 'ajax_so1'){?>
	<div class="s1box">
        <ul id="so1box">
        	<li name='vip'><h4>VIP高级会员</h4></li>
            <li name='new'><h4>最新注册会员</h4></li>
            <li name='endtime'><h4>最近登录会员</h4></li>
            <?php if(@in_array('gift',$navarr)){?><li name='endgift'><h4>最近送礼会员</h4></li><?php }?>
            <li name='endpay'><h4>最近充值会员</h4></li>
            <li name='click'><h4>人气会员</h4></li> 
            <li name='cert'><h4>认证会员</h4></li> 
        	<li name='offline'><h4>线下优质会员</h4><span>只能委托红娘牵线</span></li>
            <li name='photo'><h4>最近上传相片会员</h4></li>
            <li name='loveb'><h4>土豪会员</h4><span>按<?php echo $_ZEAI['loveB'];?>账户余额</span></li>
            <li name='fans'><h4>最有魅力会员</h4><span>按粉丝数</span></li>
        </ul>
    </div>
    <script>so1Fn(so1box);search_btn.hide();</script>
<?php exit;}elseif($submitok == 'ajax_so2'){?>
	<style>body{position:absolute}</style>
	<script>
	var nulltext = '不限',
	mate_age_ARR = age_ARR,
	mate_heigh_ARR = heigh_ARR,
	mate_pay_ARR = pay_ARR,
	mate_edu_ARR = edu_ARR,
	mate_car_ARR = car_ARR,
	mate_love_ARR = love_ARR,
	mate_job_ARR = job_ARR,
	mate_marrytime_ARR = marrytime_ARR,
	mate_house_ARR = house_ARR,
	mate_areaid_ARR1 = areaARR1,
	mate_areaid_ARR2 = areaARR2,
	mate_areaid_ARR3 = areaARR3;
    </script>
    <form id="www_zeai__cn_FORM" class="soC">
        <dl><dt>照　　片</dt><dd><input type="checkbox" id="ifphoto" class="checkskin" name="ifphoto" value="1"><label for="ifphoto" class="checkskin-label"><i class="i1"></i><b class="W50">有照片</b></label></dd></dl>
        <dl><dt>年　　龄</dt><dd id="mate_age_box">
		<script>
			zeai_cn__CreateFormItem_ajax('select','mate_age1','<?php echo $mate_age1; ?>','class="select SW"',mate_age_ARR,o('mate_age_box'));
			mate_age_box.append(' ～ ');
			zeai_cn__CreateFormItem_ajax('select','mate_age2','<?php echo $mate_age2; ?>','class="select SW"',mate_age_ARR,o('mate_age_box'));
        </script>
        </dd></dl>
        
        <dl><dt>身　　高</dt><dd id="mate_heigh_box">
		<script>
			zeai_cn__CreateFormItem_ajax('select','mate_heigh1','<?php echo $mate_heigh1; ?>','class="select SW"',mate_heigh_ARR,o('mate_heigh_box'));
			mate_heigh_box.append(' ～ ');
			zeai_cn__CreateFormItem_ajax('select','mate_heigh2','<?php echo $mate_heigh2; ?>','class="select SW"',mate_heigh_ARR,o('mate_heigh_box'));
        </script>
        </dd></dl>
        <dl><dt>最低月薪</dt><dd id="mate_pay_box"><script>zeai_cn__CreateFormItem_ajax('select','mate_pay','<?php echo $mate_pay; ?>','class="select SW"',mate_pay_ARR,o('mate_pay_box'));</script></dd></dl>        
        <dl><dt>最低学历</dt><dd id="mate_edu_box"><script>zeai_cn__CreateFormItem_ajax('select','mate_edu','<?php echo $mate_edu; ?>','class="select SW"',mate_edu_ARR,o('mate_edu_box'));</script></dd></dl>
        <dl><dt>工作地区</dt><dd id="area1_box"><script>LevelMenu3('m1|m2|m3|'+nulltext+'|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>','class="select SW"',o('area1_box'));</script></dd></dl>
        <dl><dt>婚姻状况</dt><dd id="mate_love_box"><script>zeai_cn__CreateFormItem_ajax('select','mate_love','<?php echo $mate_love; ?>','class="select SW"',mate_love_ARR,o('mate_love_box'));</script></dd></dl>        
        <dl><dt>住房情况</dt><dd id="mate_house_box"><script>zeai_cn__CreateFormItem_ajax('select','mate_house','<?php echo $mate_house; ?>','class="select W150"',mate_house_ARR,o('mate_house_box'));</script></dd></dl>
        <dl><dt>职　　业</dt><dd id="mate_job_box"><script>zeai_cn__CreateFormItem_ajax('select','mate_job','<?php echo $mate_job; ?>','class="select W150"',mate_job_ARR,o('mate_job_box'));</script></dd></dl>
        <dl><dt>购车情况</dt><dd id="mate_car_box"><script>zeai_cn__CreateFormItem_ajax('select','mate_car','<?php echo $mate_car; ?>','class="select SW"',mate_car_ARR,o('mate_car_box'));</script></dd></dl>
        <dl><dt>结婚时间</dt><dd id="mate_marrytime_box"><script>zeai_cn__CreateFormItem_ajax('select','mate_marrytime','<?php echo $mate_marrytime; ?>','class="select SW"',mate_marrytime_ARR,o('mate_marrytime_box'));</script></dd></dl>
        <!--<input class="btn size4 HONG W80_" type="button" value="开始寻缘" id="search_btn" style="position:fixed;bottom:10px;left:10%" />-->
        <input name="mate_areaid" id="mate_areaid" type="hidden" value="" />
        <input name="mate_areatitle" id="mate_areatitle" type="hidden" value="" />
        <input name="t" type="hidden" value="2" />
	</form>
    <script>search_btn.onclick=search_chkform;
	zeai.listEach(zeai.tag(www_zeai__cn_FORM,'select'),function(obj){
		obj.onblur=function(){zeai.setScrollTop(0);}
	});

	<?php if ($i=='ulist'){?>search_btn.click();<?php }?>
	search_btn.show();
    </script>
<?php exit;}elseif($submitok == 'ajax_so3'){?>
	<div class="soC">
		<div class="keyword"><input name="keyword" type="text" class="input" id="keyword" size="30" maxlength="20" placeholder="输入会员UID号/昵称"><i class="ico" id="sozoom">&#xe6c4;</i></div>
        <!--<input class="btn size4 W80_ HONG" type="button" value="开始寻缘" id="search_btn" />-->
    </div>
    <script>search_btn.onclick=search_btnFn;<?php if ($i=='ulist'){?>search_btn.click();<?php }?>
    search_btn.show();
    </script>
<?php exit;}?>
<link href="m1/css/search.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<i class="ico goback" id="ZEAIGOBACK-search" style="z-index:10;color:#000">&#xe602;</i>
<?php
$mini_title = '';//搜索
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '首页';
require_once ZEAI.'m1/top_mini.php';
$a = (empty($a))?'1':$a;
if($_ZEAI['mob_mbkind']==3){?>
<style>
.tabmenu li.ed span{color:#FF6F6F}
.tabmenu i,input.size4{background-color:#FF6F6F}
</style>
<?php }?>
<div class="tabmenu tabmenu_3 tabmenuBAIso" id="searchnav">
    <li<?php echo ($a == 1)?' class="ed"':''; ?> data="m1/search.php?submitok=ajax_so1&t=1&i=<?php echo $i;?>" id="search1btn"><span>按推荐</span></li>
    <li<?php echo ($a == 2)?' class="ed"':''; ?> data="m1/search.php?submitok=ajax_so2&t=2&i=<?php echo $i;?>" id="search2btn"><span>按条件</span></li>
    <li<?php echo ($a == 3)?' class="ed"':''; ?> data="m1/search.php?submitok=ajax_so3&t=3&i=<?php echo $i;?>" id="search3btn"><span>按网名</span></li>
    <i></i>
</div>
<div class="submain search" id="index_search"></div>
<input class="btn size4 HONG W80_" type="button" value="开始寻缘" id="search_btn" style="position:fixed;bottom:10px;left:10%;border-radius:30px" />
<script src="cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="res/select3_ajax.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="m1/js/search.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
ZeaiM.tabmenu.init({obj:searchnav,showbox:index_search});
setTimeout(function(){search<?php echo $a;?>btn.click();},200);
</script>
<?php
function ajax_ulist_fn($totalP,$p) {
	global $_ZEAI,$db,$nodatatips,$RTSQL,$cook_grade;
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
	//
	$switch = json_decode($_ZEAI['switch'],true);
	$blurclass = '';$lockstr = '';$ifblur=0;
	if($switch['grade1LockBlur']==1 && intval($cook_grade)<=1){
		$blurclass = ' blur';$lockstr = '<i class="ico lockico">&#xe61e;</i><div class="lockstr">'.dataIO($switch['grade1LockBlurT'],'out').'</div>';
		$ifblur=1;
	}
	//
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_ulist .= rows_ulist($rows,$ifblur,$lockstr,$blurclass);
	}
	return $rows_ulist;//$RTSQL.'('.$p.')'.
}
function rows_ulist($rows,$ifblur=0,$lockstr='',$blurclass='') {
	global $_ZEAI; 
	$uid      = $rows['id'];
	$sex      = $rows['sex'];
	$grade    = $rows['grade'];
	$nickname = dataIO($rows['nickname'],'out');
	$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
	$photo_s  = $rows['photo_s'];
	$photo_f  = $rows['photo_f'];
	$job      = $rows['job'];
	$pay      = $rows['pay'];
	$photo_ifshow = $rows['photo_ifshow'];
	$age = getage($rows['birthday']);
	$RZ  = $rows['RZ'];
	$age_str = ($age>18)?$age.'岁':'';
	$pay_str = (ifint($pay))?udata('pay',$pay):'';
	$job_str = udata('job',$job);
	if($ifblur==1){
		$photo_m = 'blur';
		if(empty($photo_s) || $photo_f==0)$lockstr='';
	}else{
		$photo_m = 'm';
	}
	//
	if($photo_ifshow==0 && $ifblur==0){
		$lockstr = '';
		$photo_m_url='res/photo_m'.$sex.'_hide.png';
	}else{
		$photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.smb($photo_s,$photo_m):HOST.'/res/photo_m'.$sex.'.png';	
	}
	//
	$echo .= '<a uid="'.$uid.'" sex="'.$sex.'">';
	$vipj = ($grade>1)?'<img src="m1/img/vipj.png">':'';
	
	$echo .= '<em class="small_big'.$blurclass.'" style=\'background-image:url("'.$photo_m_url.'");\'>';
		if (!empty($RZ))$echo.= '<div class="payrz">'.RZ_html($RZ,'s','color').'</div>';
	$echo .= $lockstr.$vipj.'</em>';
	$echo .= '<div class="uinfo">';
		$echo.= '<div class="nik"><span>'.uicon($sex.$grade).$nickname.'</span><font>'.$age_str.'</font></div>';
		if($t=='fj'){
			$areatitle=$rows['areatitle'];
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			$areatitle_str = str_replace("不限","",$areatitle_str);
			$distance = $rows['distance'];
			if ($distance<1000){
				$distance_str  = $distance.'m';
			}else{
				$distance_str  = intval($distance/1000).'km';
			}
			$echo.= '<div class="data"><span>'.$areatitle.'</span><span><i class="ico">&#xe614;</i>'.$distance_str.'</span></div>';
		}else{
			$echo.= '<div class="data"><span>'.$pay_str.'</span><span>'.$job_str.'</span></div>';			
		}
	$echo.= '</div>';
	$echo.= '</a>';
	return $echo;
}
function getTP($TJ,$moretb=false) {
	global $_ZEAI,$db;
	if($moretb){
		$rt = $db->query("SELECT COUNT(*) FROM (".$TJ.") ZEAI__cn_SQL");$row = $db->fetch_array($rt,'num');
	}else{
		$rt = $db->query("SELECT COUNT(*) FROM (SELECT id FROM ".__TBL_USER__." WHERE ".$TJ." LIMIT ".$_ZEAI['limit'].") ZEAI__cn_SQL");$row = $db->fetch_array($rt,'num');
	}
	$total=$row[0];
	return array($total,ceil($total/$_ZEAI['pagesize']));
}
?>
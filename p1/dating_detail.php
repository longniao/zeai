<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
header("Cache-control: private");
if (!ifint($fid))alert("信息不存在","-1");
require_once ZEAI.'sub/conn.php';
if($submitok == 'ajax_chklogin'){
	require_once ZEAI.'sub/conn.php';
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来报名','jumpurl'=>Href('dating',$fid)));
	json_exit(array('flag'=>1,'msg'=>'已登录'));
}
require_once ZEAI.'cache/udata.php';
if($submitok=='ajax_detail_bm'){
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来报名','jumpurl'=>Href('dating',$fid)));
	if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'不能操作自已'));
	if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'zeai_error_fid'));
	$id=$fid;
	$content = dataIO($content,'in',1000);
	if (str_len($content)>1000 || str_len($content)<3)json_exit(array('flag'=>0,'msg'=>'信息内容过多或过少'));
	
	$row2 = $db->ROW(__TBL_DATING__,"uid,flag,title,age1,age2,heigh1,heigh2,love,edu,sex","id=".$id,"num");
	if($row2){
		$uid = $row2[0];
		$title = trimhtml(dataIO($row2[2],'out'));
		$age1 = $row2[3];
		$age2 = $row2[4];
		$heigh1 = $row2[5];
		$heigh2 = $row2[6];
		$love = $row2[7];
		$edu  = $row2[8];
		$sex  = $row2[9];
		if ($row2[1] != 1)json_exit(array('flag'=>0,'msg'=>'此约会已经结束或未审核'));
	}else{
		json_exit(array('flag'=>0,'msg'=>'该信息不存在或已被删除'));
	}
	if($db->ROW(__TBL_DATING_USER__,"id","uid=".$cook_uid." AND fid=".$id))json_exit(array('flag'=>0,'msg'=>'你已经报过名啦，请不要重复操作'));
	if (gzflag($cook_uid,$uid) != -1){//如果没有拉黑下一步

		//调出我报名者资料
		$row = $db->NAME($cook_uid,"sex,birthday,heigh,love,edu");
		$data_sex      = $row['sex'];
		$data_birthday = $row['birthday'];
		$data_age      = getage($data_birthday);
		$data_heigh    = $row['heigh'];
		$data_love     = $row['love'];
		$data_edu      = $row['edu'];

		//调出发布人资料
		$row2 = $db->ROW(__TBL_USER__,"nickname,openid,subscribe","id=".$uid,"num");
		$nickname = dataIO($row2[0],'out');$openid = $row2[1];$subscribe = $row2[2];
		
		//年龄、身高、婚姻状况、学历 匹配
		if ($data_sex != $sex && !empty($sex))json_exit(array('flag'=>0,'msg'=>'您不符合此次约会性别要求'));
		if ($data_age < $age1 && !empty($age1))json_exit(array('flag'=>0,'msg'=>'您不符合此次约会年龄要求'));
		if ($data_age > $age2 && !empty($age2))json_exit(array('flag'=>0,'msg'=>'您不符合此次约会年龄要求'));
		if ($data_heigh < $heigh1 && !empty($heigh1))json_exit(array('flag'=>0,'msg'=>'您不符合此次约会身高要求'));
		if ($data_heigh > $heigh2 && !empty($heigh2))json_exit(array('flag'=>0,'msg'=>'您不符合此次约会身高要求'));
		if ($data_edu < $edu && !empty($edu))json_exit(array('flag'=>0,'msg'=>'您不符合此次约会学历要求'));
		if ($data_love != $love && !empty($love))json_exit(array('flag'=>0,'msg'=>'您不符合此次约会婚姻状况要求'));
		
		//入库
		$db->query("INSERT INTO ".__TBL_DATING_USER__."  (fid,uid,content,addtime) VALUES ($id,$cook_uid,'$content',".ADDTIME.")");
		$db->query("UPDATE ".__TBL_DATING__." SET bmnum=bmnum+1 WHERE id=".$id);
		
		//站内消息
		$C = $nickname.'有人报名了你发起的约会【'.$title.'】　　　<a href='.Href("dating",$id).' class=aQING>查看详情</a>';
		$db->SendTip($uid,'你发布的约会有人报名了',dataIO($C,'in'),'sys');
		
		//微信通知
		if (!empty($openid) && $subscribe==1){
			$wxurl = urlencode(mHref('dating',$id));
			//客服通知
			$content = urlencode('您好，有人报名了你发起的约会【'.$title.'】　　<a href="'.$wxurl.'">进入查看</a>');
			$ret = @wx_kf_sent($openid,$content,'text');
			$ret = json_decode($ret);
			//模版通知
			if ($ret->errmsg != 'ok'){
				$keyword1  = urlencode('有人报名了你发起的约会【'.$title.'】');
				$keyword3  = urlencode($_ZEAI['siteName']);
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$wxurl);
			}
		}
	}
	json_exit(array('flag'=>1,'msg'=>'报名成功！请等候发起人的通知～'));
}

$rt = $db->query("SELECT a.id,a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,a.job,a.birthday,a.love,a.dataflag,a.areatitle,a.birthday,b.datingkind,b.title,b.areatitle,b.price,b.yhtime,b.maidian,b.contact,b.content,b.sex,b.age1,b.age2,b.heigh1,b.heigh2,b.love,b.edu,b.bmnum,b.click,b.flag,a.love,a.heigh,a.pay FROM ".__TBL_USER__." a,".__TBL_DATING__." b WHERE a.id=b.uid AND a.flag=1 AND b.flag>0 AND b.id=".$fid);
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
	$uid = $row[0];
	$Unickname = dataIO($row[1],'out');
	$Usex      = $row[2];
	$Ugrade    = $row[3];
	$Uphoto_s  = $row[4];
	$Uphoto_f  = $row[5];
	$job      = $row[6];
	$Ubirthday = $row[7];
	$Ulove = $row[8];
	$Udataflag  = $row[9];
	$Uareatitle = $row[10];
	$birthday   = dataIO($row[11],'out');
	$love  = $row[30];
	$heigh = $row[31];
	$pay   = $row[32];
	$job_str      = (empty($job))?'':udata('job',$job).' ';
	$pay_str      = (empty($pay))?'':udata('pay',$pay).'/月'.' ';
	$love_str     = (empty($love))?'':udata('love',$love).' ';
	$heigh_str    = ($heigh>140)?$heigh.'cm ':'';
			
	$Unickname = (empty($Unickname))?'uid:'.$uid:$Unickname;
	$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
	$aARR = explode(' ',$Uareatitle);
	$areatitle_str = (empty($aARR[1]))?'':$aARR[1].$aARR[2];
	$areatitle_str  = str_replace("不限","",$areatitle_str);
	//
	$datingkind = $row[12];
	$title      = trimhtml(dataIO($row[13],'out'));
	$areatitle  = $row[14];
	$price      = $row[15];
	$yhtime  = $row[16];
	$maidian = $row[17];
	$contact = dataIO($row[18],'out');
	$content = dataIO($row[19],'out');
	$sex     = $row[20];
	$age1 = $row[21];
	$age2 = $row[22];
	$heigh1 = $row[23];
	$heigh2 = $row[24];
	$love = $row[25];
	$edu  = $row[26];
	$bmnum = $row[27];
	$click = $row[28];
	$flag  = $row[29];
	$Uhref  = Href('u',$uid);
	$photo_m     = getpath_smb($Uphoto_s,'m');
	$photo_m_url = (!empty($Uphoto_s) && $Uphoto_f==1)?$_ZEAI['up2'].'/'.$photo_m:HOST.'/res/photo_m'.$Usex.'.png';
} else {
	alert("信息不存在","-1");
}
switch ($datingkind){ 
	case 1:$tbody = "_喝茶小叙";break;
	case 2:$tbody = "_共进晚餐";break;
	case 3:$tbody = "_相约出游";break;
	case 4:$tbody = "_看电影";break;
	case 5:$tbody = "_欢唱K歌";break;
}
$db->query("UPDATE ".__TBL_DATING__." SET click=click+1 WHERE id=".$fid);
if (ifint($cook_uid,'0-9','1,8')){
	$row = $db->NUM($cook_uid,"mob,qq,weixin");
	$mob    = dataIO($row[0],'out');
	$qq     = dataIO($row[1],'out');
	$weixin = dataIO($row[2],'out');
	$iflogin = true;
	//ifbest 约会同意结速后查最佳人选
	$ifbest=0;
	if($flag==2){
		$row2 = $db->ROW(__TBL_DATING_USER__,"id","flag=1 AND fid=".$fid);
		if ($row2)$ifbest=1;
	}
}
$nav='dating';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $title.$tbody; ?>_<?php echo $_ZEAI['siteName']; ?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/dating.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<div class="main dating fadeInL">
	<div class="datingL">
    	<div class="box S5 C" style="margin-bottom:0">
        	<h1><?php echo $title;?></h1>
            <div class="CC">
                <dl><dt>约会主题</dt><dd><?php echo $title; ?></dd></dl>
                <dl><dt>约会类型</dt><dd><?php
        switch ($datingkind){ 
        case 1:echo "喝茶小叙";break;
        case 2:echo "共进晚餐";break;
        case 3:echo "相约出游";break;
        case 4:echo "看电影";break;
        case 5:echo "欢唱K歌";break;
        case 6:echo "其他";break;
        default:echo "不限，都可以";break;
        }?></dd></dl>
                <dl><dt>约会时间</dt><dd><?php echo YmdHis($yhtime,'YmdHi').' '.getweek(YmdHis($yhtime,'Ymd'));?></dd></dl>
                <dl><dt>约会城市</dt><dd><?php echo $areatitle; ?></dd></dl>
                <dl><dt>费用预算</dt><dd><?php
        switch ($price){ 
        case 1:echo "100元以下";break;
        case 2:echo "100～300元";break;
        case 3:echo "300--500元";break;
        case 4:echo "500元以上";break;
        default :echo "约会费用不限";break;
        }?></dd></dl>
                <dl><dt>谁来买单</dt><dd><?php
        switch ($maidian){ 
        case 1:echo "我来买单";break;
        case 2:echo "应约人买单";break;
        case 3:echo "AA制";break;
        default :echo "谁买单无所谓";break;
        }?></dd></dl>
                <dl><dt>联系方法</dt><dd><?php if ($ifbest == 1 || $uid==$cook_uid){echo '<font>'.$contact.'</font>';}else{echo '只有发起人选中的最佳人选才可以看见！';} ?></dd></dl>
                <dl><dt>约会内容</dt><dd><?php echo $content; ?></dd></dl>
                <dl><dt>约会对象</dt><dd>
                <span><?php if ($sex >0){echo udata('sex',$sex).'性　';} ?></span>
                <span><?php if (!empty($age1) && !empty($age2)){echo $age1.'～'.$age2.'岁　';}else{echo $nulltext;} ?></span>
                <span><?php if (!empty($heigh1) && !empty($heigh2)){echo $heigh1.'～'.$heigh2.'厘米　';} ?></span>
                <?php if (ifint($love)){?><?php echo udata('love',$love);?></span>　<?php }?>
                <?php if (ifint($edu)){?><span><?php echo udata('edu',$edu);?></span><?php }?>
                </dd></dl>
                <br><br><div class="linebox"><div class="line"></div><div class="title BAI S16">报名赴约</div></div>
                <form id="www_zeai_cn_FORM" class="bmform">
                    <textarea id="content" class="textarea" name="content"<?php if ($flag > 1 ){echo ' disabled="disabled"';}?>><?php if ($flag == 1){echo "联系电话：".$mob."\nQQ：".$qq."\n微信：".$weixin."\n应约说明：";}elseif($flag==0){echo "此约会审核中";}else{echo "此约会已经结束或已有最佳人选，报名终止。";}?></textarea>
                    <span>此联系方式不会公开，只有约会发起人才能看见，请放心填写。</span>
                    <input type="button" class="btn size4 HONG B" value="　　确认报名　　"<?php if ($flag != 1 /*|| !$iflogin*/ ){echo ' style="background-color:#ccc;cursor:not-allowed" disabled="disabled"';}?> onClick="dating_btn_detailBMfn('supdes');" />
                    <input type="hidden" name="fid" value="<?php echo $fid; ?>">
                    <input type="hidden" name="uid" id="uid" value="<?php echo $uid; ?>">
                    <input type="hidden" name="submitok" value="ajax_detail_bm">
                </form>
            </div>
            <em>
                <li>离约会结束还有</li>
                <li>
                    <?php
                    $showhref = ($iflogin)?'#content':'javascript:bm();';
                    if ($flag == 1){
                        $d1  = ADDTIME;
                        $d2  = $yhtime;
                        $totals  = ($d2-$d1);
                        $day     = intval( $totals/86400 );
                        $hour    = intval(($totals % 86400)/3600);
                        $hourmod = ($totals % 86400)/3600 - $hour;
                        $minute  = intval($hourmod*60);
                        if (($totals) > 0) {
                            if ($day > 0){
                                $outtime = "<span>$day</span>天 ";
                            } else {
                                $outtime = "";
                            }
                            $outtime .= "<span>$hour</span>小时 <span>$minute</span>分";
                        } else {
                            $db->query("UPDATE ".__TBL_DATING__." SET flag=2 WHERE id=".$fid);
                            $outtime = "<font>已经结束</font>";
                            $disable = " class='disable'";
                            $showhref = 'javascript:;';
                        }
                    } else {
                        $outtime = "<font>已经结束</font>";
                        $disable = " class='disable'";
                        $showhref = 'javascript:;';
                    }
                    echo $outtime;
                    ?>
                </li>
                <li>报名人数<font><?php echo $bmnum ?></font>人</li>
                <li>围观人数<font><?php echo $click ?></font>人</li>
                <li><a href="<?php echo $showhref; ?>"<?php echo $disable; ?>><i class="ico">&#xe65c;</i> 我要报名</a></li>
            </em>            
        </div>
	</div>
	<div class="datingR">
        <div class="box S5">
			<h1>发起人信息</h1>
			<div class="Uinfo">
            	<a href="<?php echo $Uhref;?>" target="_blank">
				<p class="sexbg<?php echo $Usex;?>" style="background-image:url('<?php echo $photo_m_url;?>')"></p>
            	<em>
                    <h5><?php echo uicon($Usex.$Ugrade).$Unickname; ?></h5>
                    <h5><?php echo $birthday_str.' '.$areatitle_str; ?></h5>
                    <h5><?php echo $love_str.' '.$job_str.' '.$pay_str; ?></h5>
                </em>
                </a>
            </div>
		</div>
        <div class="box S5">
			<h1>报名会员</h1>
            <ul class="ulist">
				<?php 
                $rt=$db->query("SELECT a.uid,U.uname,U.nickname,U.sex,U.grade,U.photo_s,U.photo_f FROM ".__TBL_DATING_USER__." a,".__TBL_USER__." U WHERE a.uid=U.id AND U.flag=1 AND a.fid=$fid ORDER BY a.id DESC");
                $echo = '';$i=0;
                WHILE ($rows = $db->fetch_array($rt,'name')){
                    $i++;
                    $uid      = $rows['uid'];
                    $sex      = $rows['sex'];
                    $photo_s  = $rows['photo_s'];
                    $photo_f  = $rows['photo_f'];
                    $grade    = $rows['grade'];
                    $uname    = dataIO($rows['uname'],'out');
					$nickname = dataIO($rows['nickname'],'out');
					$nickname = (empty($nickname))?$uname:$nickname;
					$nickname = (ifmob($nickname))?$uid:$nickname;
                    $photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
                    $sexbg      = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
                    $echo .= '<a href="'.Href('u',$uid).'" target="_blank" class="m">';
                    $echo .='<img src='.$photo_s_url.' '.$sexbg.'>';
                    $echo .= '<span>'.$nickname.'</span>';/*.uicon($sex.$grade)*/
                    $echo .= '</a>';
                }
                if($i>0){echo $echo;}else{echo nodatatips('暂时没人报名<br>','s');}
                ?>            
            </ul>
		</div>
        <div class="box S5" style="margin-bottom:0">
			<h1>约会安全</h1>
            <div class="safetips">　声明：网络约会有风险，同城约会行为属网友个人行为，本站对双方交友过程中发生的任何纠纷不承担责任，请确定后再赴约。<br><br>　<?php echo $_ZEAI['siteName'];?>是一个公众交友平台，网站信息无法保证百分百真实，如果被骗，请与警方联系，且与本站无关，希望大家谨防各类骗局。<br><br></div>
		</div>
	</div>
</div><script>
function dating_btn_detailBMfn(supdes) {
	zeai.confirm('确定要报名此约会么？',function(){
		zeai.msg('正在报名中..',{time:5});
		zeai.ajax({url:PCHOST+'/dating_detail'+zeai.extname,form:www_zeai_cn_FORM},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg,{time:2});
			if(rs.flag=='nologin'){
				setTimeout(function(){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));},2000);
			}else if(rs.flag==1){
				setTimeout(function(){location.reload(true);},2000);
			}
		});
	});
}
function bm(){
	zeai.ajax({url:PCHOST+'/dating_detail'+zeai.ajxext+'fid=<?php echo $fid;?>&submitok=ajax_chklogin'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag=='nologin'){
			zeai.msg(0);zeai.msg(rs.msg,{time:2});
			setTimeout(function(){zeai.openurl(PCHOST+'/login'+zeai.ajxext+'jumpurl='+encodeURIComponent(rs.jumpurl));},2000);
			return false;
		}
	});
}
</script>
<?php require_once ZEAI.'p1/bottom.php';?>
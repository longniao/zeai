<?php
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$chk_u_jumpurl=Href('trend');
if($submitok == 'add' || $submitok == 'mod'){
	require_once ZEAI.'sub/conn.php';
	if(!iflogin() || !ifint($cook_uid))exit("<html><body><script>window.onload = function (){parent.location.href='".HOST."/p1/login.php';}</script></body></html>");
}else{
	require_once 'my_chkuser.php';
}
require_once ZEAI.'cache/udata.php';
if($submitok == 'ajax_dating_del'){
	if(!ifint($fid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_DATING__,"id","uid=".$cook_uid." AND id=".$fid);
	if(!$row)json_exit(array('flag'=>0,'msg'=>'无权操作'));
	$db->query("DELETE FROM ".__TBL_DATING_USER__." WHERE fid=".$fid);
	$db->query("DELETE FROM ".__TBL_DATING__." WHERE id=".$fid);
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok == 'ajax_dating_stop'){
	if(!ifint($fid))exit(JSON_ERROR);
	$db->query("UPDATE ".__TBL_DATING__." SET flag=2 WHERE id=".$fid." AND uid=".$cook_uid);
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok == 'ajax_dating_bmuser_update'){
	if(!ifint($clsid) || !ifint($fid))json_exit(array('flag'=>0,'msg'=>'zeai_error_clsid'));
	$id=$clsid;
	$rt = $db->query("SELECT uid FROM ".__TBL_DATING_USER__." WHERE flag=0 AND fid=".$fid." AND id=".$id);
	if($db->num_rows($rt)){
	$row = $db->fetch_array($rt,'num');
	$bbsuid = $row[0];
	}else{json_exit(array('flag'=>0,'msg'=>'约会已结束'));}
	$rt = $db->query("SELECT uid,title FROM ".__TBL_DATING__." WHERE flag=1 AND id=".$fid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt,'num');
		$wzuid = $row[0];
		$titleOld = dataIO($row[1],'out');
	}else{
		json_exit(array('flag'=>0,'msg'=>'约会已过期'));
	}
	if($wzuid!==$cook_uid)json_exit(array('flag'=>0,'msg'=>'zeai_error_wzuid_cook_uid'));
	
	$db->query("UPDATE ".__TBL_DATING__." SET flag=2 WHERE id=".$fid);
	$db->query("UPDATE ".__TBL_DATING_USER__." SET flag=1 WHERE id=".$id);

	$row = $db->NUM($bbsuid,"nickname,openid,subscribe");
	$bbsnickname = dataIO($row[0],'out');$bbsopenid = $row[1];$bbssubscribe = $row[2];
	
	//站内消息
	$T   = $cook_nickname." 已接受了您的约会报名，正式邀请你准时赴约 ！";
	$C  = "会员 <a href=".Href('u',$cook_uid).">【".$cook_nickname."】</a> 正式邀请你参加TA发起的约会【".$titleOld."】，请速与TA取得联系，以免错失良机，<a href=".Href('dating',$fid)." class=aQING>立即查看</a>";
	$db->SendTip($bbsuid,$T,dataIO($C,'in'),'sys');
	
	//微信通知
	if (!empty($bbsopenid) && $bbssubscribe==1){
		//客服通知
		$content = urlencode('恭喜您，收到约会【'.$titleOld.'】赴约通知　　<a href="'.mHref('dating',$fid).'">进入查看</a>');
		$ret = @wx_kf_sent($bbsopenid,$content,'text');
		$ret = json_decode($ret);
		//模版通知
		if ($ret->errmsg != 'ok'){
			$keyword1  = urlencode('恭喜您，收到约会【'.$titleOld.'】赴约通知！');
			$keyword3  = urlencode($_ZEAI['siteName']);
			@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$bbsopenid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.urlencode(mHref('dating',$fid)));
		}
	}
	//
	json_exit(array('flag'=>1,'msg'=>'邀请成功！请尽快联系，以免错失良机'));
}elseif($submitok == 'addupdate' || $submitok == 'modupdate'){
	if (!ifint($datingkind,'0-9','1'))json_exit(array('flag'=>0,'msg'=>'请选择约会类型'));
	if (str_len($title)>200 || str_len($title)<1)json_exit(array('flag'=>0,'msg'=>'约会主题请控制在1~100字节'));
	if (!ifint($price,'0-9','1'))json_exit(array('flag'=>0,'msg'=>'请选择正确格式的费用预算'));
	if ($hour8 <0 || $hour8 >24 || $minute8<0 || $minute8>59)json_exit(array('flag'=>0,'msg'=>'请输入正确格式时间，如：18:30'));
	$year8 = ($year8>date('Y'))?date('Y')+1:$year8;
	$yhtime1 = $year8.'-'.$month8.'-'.$day8;
	$yhtime2 = ' '.$hour8.':'.$minute8.':00'; 
	if(!ifdate($yhtime1))json_exit(array('flag'=>0,'msg'=>'请输入正确格式时间'.$yhtime1));
	$yhtime = $yhtime1.$yhtime2;
	$yhtime = strtotime($yhtime);
	if (ADDTIME >= $yhtime)json_exit(array('flag'=>0,'msg'=>'无效日期，请检查是否过期'));
	if (!ifint($datingkind,'0-9','1'))json_exit(array('flag'=>0,'msg'=>'请选择约会类型'));
	if (str_len($contact)>100 || str_len($contact)<5)json_exit(array('flag'=>0,'msg'=>'联系方式请控制在5~100字节'));
	if (str_len($content)>2000 || str_len($content)<2)json_exit(array('flag'=>0,'msg'=>'约会内容请控制在2~1000字节'));
	$areaid    = dataIO($areaid,'in',50);
	$areatitle = dataIO($areatitle,'in',40);
	$age1_      = intval($age1);
	$age2_      = intval($age2);
	$heigh1_    = intval($heigh1);
	$heigh2_    = intval($heigh2);
	if ($age1_ > $age2_){
		$age1      = $age2_;
		$age2      = $age1_;
	}else{
		$age1      = $age1_;
		$age2      = $age2_;
	}
	if ($heigh1_ > $heigh2_){
		$heigh1    = $heigh2_;
		$heigh2    = $heigh1_;
	}else{
		$heigh1    = $heigh1_;
		$heigh2    = $heigh2_;
	}
	$sex  = intval($sex);
	$edu  = intval($edu);
	$love = intval($love);
	$maidian = intval($maidian);
	//$flag = ($data_grade > 2 && $data_if2 = 999)?1:0;
	$flag=0;
	if ($submitok == 'addupdate'){
		if ($cook_dating == $title)json_exit(array('flag'=>0,'msg'=>'请不要重复发布'));
		$db->query("INSERT INTO ".__TBL_DATING__." (uid,datingkind,title,areaid,areatitle,price,yhtime,maidian,contact,content,sex,age1,age2,heigh1,heigh2,love,edu,addtime,px,flag) VALUES ($cook_uid,$datingkind,'$title','$areaid','$areatitle',$price,$yhtime,$maidian,'$contact','$content',$sex,$age1,$age2,$heigh1,$heigh2,$love,$edu,".ADDTIME.",".ADDTIME.",$flag)");
		setcookie("cook_dating",$title,ADDTIME+7200000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'发布成功，请等待客服审核'));
	}elseif($submitok == 'modupdate'){
		if (!ifint($fid))exit(JSON_ERROR);
		$db->query("UPDATE ".__TBL_DATING__." SET datingkind='$datingkind',title='$title',areaid='$areaid',areatitle='$areatitle',price='$price',yhtime='$yhtime',maidian='$maidian',contact='$contact',content='$content',sex='$sex',age1='$age1',age2='$age2',heigh1='$heigh1',heigh2='$heigh2',love='$love',edu='$edu',flag=$flag WHERE id=".$fid);
		json_exit(array('flag'=>1,'msg'=>'修改成功，请等待客服审核'));
	}
	exit(JSON_ERROR);
}elseif($submitok == 'mod'){
	if (!ifint($fid))alert("Forbidden!","-1");
	$row = $db->ROW(__TBL_DATING__,"datingkind,title,areaid,areatitle,price,yhtime,maidian,contact,content,sex,age1,age2,heigh1,heigh2,love,edu","flag=0 AND uid=".$cook_uid." AND id=".$fid,"name");
	if(!$row)alert("该信息暂时不能修改","-1");
	$datingkind = $row['datingkind'];
	$title      = dataIO($row['title'],'out');
	$areaid     = $row['areaid'];
	$areatitle  = $row['areatitle'];
	$price      = $row['price'];
	$yhtime     = $row['yhtime'];
	$maidian    = $row['maidian'];
	$contact    = dataIO($row['contact'],'out');
	$content    = dataIO($row['content'],'out');
	$sex        = $row['sex'];
	$age1       = $row['age1'];
	$age2       = $row['age2'];
	$heigh1     = $row['heigh1'];
	$heigh2     = $row['heigh2'];
	$love       = $row['love'];
	$edu        = $row['edu'];
	//
	$year8  = YmdHis($yhtime,'Y');
	$month8 = YmdHis($yhtime,'m');
	$day8   = YmdHis($yhtime,'d');
	$hour8  = YmdHis($yhtime,'H');
	$minute8= YmdHis($yhtime,'i');
	$areaid = explode(',',$areaid);
	$m1 = $areaid[0];$m2 = $areaid[1];$m3 = $areaid[2];
}
$t = (ifint($t,'1-2','1'))?$t:1;
$zeai_cn_menu = 'my_dating';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的约会 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_dating.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
</head>
<body>
<?php if ($submitok == 'bmuser'){?>
<style>body{background-color:#fff}</style>
<?php
$fid=intval($id);
$rt=$db->query("SELECT a.sex,a.grade,a.nickname,a.photo_s,a.photo_f,a.job,a.birthday,a.love,b.id as bbsid,b.uid,b.content,b.addtime,b.flag AS bbsflag,c.flag FROM ".__TBL_USER__." a,".__TBL_DATING_USER__." b,".__TBL_DATING__." c WHERE a.id=b.uid AND c.id=".$fid." AND b.fid=".$fid." ORDER BY b.flag DESC,b.id DESC");
$total = $db->num_rows($rt);
if ($total <= 0){exit(nodatatips('暂时还没有人报名'));}else{
	echo '<div class="my_dating_bm">';
	echo '<div class="bmtbody"><h3>'.urldecode($title).'</h3>报名总人数<b class="hot">'.$total.'</b></div><div class="listboxu" id="my_dating_listbmuZeaiV6">';
	for($ii=0;$ii<$total;$ii++) {
		$rows = $db->fetch_array($rt,'num');
		if(!$rows) break;
		$sex      = $rows[0];
		$grade    = $rows[1];
		$nickname = strip_tags(dataIO($rows[2],'out'));
		$photo_s  = $rows[3];
		$photo_f  = $rows[4];
		//
		$job       = $rows[5];
		$birthday  = $rows[6];
		$love      = $rows[7];
		$bbsid     = $rows[8];
		$uid       = $rows[9];
		$content   = (dataIO($rows[10],'out'));
		$addtime   = $rows[11];
		$bbsflag   = $rows[12];
		$flag      = $rows[13];
		$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁';
		$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
		$sexbg       = (empty($photo_s) || $photo_f==0)?' class="photo_s sexbg'.$sex.'"':' class="photo_s"';
		echo '<li fid="'.$fid.'" clsid="'.$bbsid.'"><a href="'.Href('u',$uid).'" target="_blank"><img src="'.$photo_s_url.'" uid="'.$uid.'"'.$sexbg.'></a><h4>'.uicon($sex.$grade).$nickname.$flagstr.'</h4><h6>'.$birthday_str.'　'.udata('love',$love).'　'.udata('job',$job).'</h6>';
		if ($flag == 1) {
			echo'<a class="btn size2 HONG3" nickname="'.urlencode($nickname).'">邀请此人</a>';
		}else{
			if ($bbsflag == 1){echo "<span class='best'><i class='ico'>&#xe652;</i> 最佳人选</span><span class='contact'>".$content."</span>";}
		}
		echo'</li>';
	}}
	?>
<script src="js/my_dating.js"></script>
<script>dating_listBmBoxuInit(my_dating_listbmuZeaiV6);</script>
<?php exit('</div></div>');
}elseif($submitok == 'add' || $submitok == 'mod'){
	$year8=(!ifint($year8))?date('Y'):$year8;
	$month8=(!ifint($month8))?date('m'):$month8;
	$nulltext='请选择';?>
<style>body{background-color:#fff}</style>
<script src="../cache/udata.js"></script>
<script src="../cache/areadata.js"></script>
<script src="../res/select3.js"></script>
<script>
var nulltext = '不限',selstr2 = '';
</script>
<div class="my_dating_add">
<form id="zeai_cn_FORM">
<table class="tablelist"><tr>
	<td width="550">
    <h1>约会内容</h1>
    </td>
    <td><h1>约会对象条件</h1></td>
  </tr>
  <tr>
    <td>
    <div class="C">
        <dl><dt>约会类型</dt><dd><select name="datingkind" id="datingkind" class="SW">
        <option value="0" <?php if ($datingkind == 0)echo 'selected'; ?>><?php echo $nulltext; ?></option>
        <option value="1" <?php if ($datingkind == 1)echo 'selected'; ?>>喝茶小叙</option>
        <option value="2" <?php if ($datingkind == 2)echo 'selected'; ?>>共进晚餐</option>
        <option value="3" <?php if ($datingkind == 3)echo 'selected'; ?>>相约出游</option>
        <option value="4" <?php if ($datingkind == 4)echo 'selected'; ?>>看电影</option>
        <option value="5" <?php if ($datingkind == 5)echo 'selected'; ?>>欢唱K歌</option>
        <option value="6" <?php if ($datingkind == 6)echo 'selected'; ?>>其他</option>
      </select></dd></dl>
        <dl><dt>约会主题</dt><dd><input name="title" class="input W400" id="title" value="<?php echo $title;?>" placeholder="起一个响亮的名称吧" /></dd></dl>
        <dl><dt>费用预算</dt><dd><select name="price" id="price" class="SW">
        <option value="0" <?php if ($price == 0)echo 'selected'; ?>><?php echo $nulltext; ?></option>
        <option value="1" <?php if ($price == 1)echo 'selected'; ?>>100元以下</option>
        <option value="2" <?php if ($price == 2)echo 'selected'; ?>>100--300元</option>
        <option value="3" <?php if ($price == 3)echo 'selected'; ?>>300--500元</option>
        <option value="4" <?php if ($price == 4)echo 'selected'; ?>>500元以上</option>
      </select></dd></dl>
        <dl><dt>约会时间</dt><dd>
    <input name="year8" type="text" class="input W50" id="year8" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" size="4" maxlength="4" value="<?php echo $year8; ?>" /> 年
    <input name="month8" type="text" class="input W50" id="month8" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" size="2" maxlength="2" value="<?php echo $month8; ?>" /> 月
    <input name="day8" type="text" class="input W50" id="day8" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" size="2" maxlength="2" value="<?php echo $day8; ?>" /> 日　
    <input name="hour8" type="text" class="input W50" id="hour8" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" size="2" maxlength="2" value="<?php echo $hour8; ?>" /> 时
    <input name="minute8" type="text" class="input W50" id="minute8" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" size="2" maxlength="2" value="<?php echo $minute8; ?>" /> 分</dd></dl>
        <dl><dt>谁来买单</dt><dd><select name="maidian" id="maidian" class="SW">
    <option value=0 <?php if ($maidian == 0)echo 'selected'; ?>><?php echo $nulltext; ?></option>
    <option value=1 <?php if ($maidian == 1)echo 'selected'; ?>>我买单</option>
    <option value=2 <?php if ($maidian == 2)echo 'selected'; ?>>应约人买单</option>
    <option value=3 <?php if ($maidian == 3)echo 'selected'; ?>>AA制</option>
    </select></dd></dl>
        <dl><dt>约会地区</dt><dd><script>LevelMenu3('m1|m2|m3|请选择|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>','class="SW area"');</script></dd></dl>
        <dl><dt>联系方法</dt><dd>
         <input name="contact" class="input W400" id="contact" value="<?php echo $contact; ?>" placeholder="手机/QQ/微信，联系方式仅显示给成功赴约人，不公开" /></dd></dl>
        <dl><dt>约会内容</dt><dd><textarea name="content" id="content" rows="2" class="textarea W400" placeholder="如：约会大概内容、详细约会地点、交通路线、注意事项等。"><?php echo $content; ?></textarea></dd></dl>
    </div>
    
    
    </td>
    <td valign="top">
    <div class="C">
        <dl><dt>性　　别</dt><dd><script>zeai_cn__CreateFormItem('select','sex','<?php echo $sex; ?>','class="SW SW2"');</script></dd></dl>
        <dl><dt>年　　龄</dt><dd><script>zeai_cn__CreateFormItem('select','age1','<?php echo $age1; ?>','class="SW SW2"',age_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','age2','<?php echo $age2; ?>','class="SW SW2"',age_ARR);</script></dd></dl>
        <dl><dt>身　　高</dt><dd><script>zeai_cn__CreateFormItem('select','heigh1','<?php echo $heigh1; ?>','class="SW SW2"',heigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','heigh2','<?php echo $heigh2; ?>','class="SW SW2"',heigh_ARR);</script></dd></dl>
        <dl><dt>婚姻状况</dt><dd><script>zeai_cn__CreateFormItem('select','love','<?php echo $love; ?>','class="SW SW2"');</script></dd></dl>
        <dl><dt>学　　历</dt><dd><script>zeai_cn__CreateFormItem('select','edu','<?php echo $edu; ?>','class="SW SW2"');</script></dd></dl>
     </div>
    <div class="btnbox">
    <?php if ($submitok == 'add'){$bnt_str='开始发布';?>
    <input name="submitok" type="hidden" value="addupdate" />
    <?php }else{$bnt_str='保存并修改';?>
    <input name="submitok" type="hidden" value="modupdate" />
    <?php }?>
    <input name="areaid" id="areaid" type="hidden" value="" />
    <input name="fid" id="fid" type="hidden" value="<?php echo $fid; ?>" />
    <input name="areatitle" id="areatitle" type="hidden" value="" />
    <button type="button" class="btn size4 HONG" id="dating_btn_save" /><?php echo $bnt_str;?></button>
    </div>
    </td>
  </tr>
</table>
</form>
</div>
<script src="js/my_dating.js"></script>
<script>o('dating_btn_save').onclick = dating_btn_saveFn;</script> 
<?php exit;}
/******W**W*W**Z**E*A****I**.**C**N*/
require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>我的约会</h1>
        <div class="tab">
			<?php
            if ($t == 1) {
                $rt=$db->query("SELECT id,title,yhtime,bmnum,click,flag FROM ".__TBL_DATING__." WHERE  uid=".$cook_uid." ORDER BY px DESC,id DESC");
				$total = $db->num_rows($rt);
				$total1= $total;
            }elseif($t==2){
				$rt=$db->query("SELECT a.uname,a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,b.id,b.uid,b.title,b.yhtime,b.contact,b.bmnum,b.click,b.flag,c.flag as bmflag FROM ".__TBL_USER__." a,".__TBL_DATING__." b,".__TBL_DATING_USER__." c WHERE a.id=b.uid AND b.id=c.fid AND c.uid=".$cook_uid." ORDER BY b.id DESC");
				$total = $db->num_rows($rt);
				$total2= $total;
			}
            ?>
            <a href="<?php echo SELF;?>?t=1"<?php echo ($t==1)?' class="ed"':'';?>>我的约会<?php echo ($total1>0)?' ('.$total1.')':'';?></a>
            <a onclick="dating_add();">发起约会</a>
            <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>我参加的约会<?php echo ($total2>0)?' ('.$total2.')':'';?></a>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="my_dating">
				<?php
				
				//我的约会
				if($t==1){
					if($total>0){$page_skin=2;$pagemode=4;$pagesize=8;$page_color='#E83191';require_once ZEAI.'sub/page.php';
					?>
					<table class="tablelist">
					<?php
					for($i=0;$i<$pagesize;$i++) {
						$rows = $db->fetch_array($rt,'num');
						if(!$rows) break;
						$id     = $rows[0];
						$title  = dataIO($rows[1],'out');
						$yhtime = YmdHis($rows[2],'YmdHi');
						$jzbmtime = $rows[2];
						$bmnum  = $rows[3];
						$click  = $rows[4];
						$flag   = $rows[5];
					?>
					<tr>
					  <td width="70" height="60" align="right"><div class="yueico">约</div></td>
					  <td align="left" style="padding-left:15px"><a href="<?php echo Href('dating',$id);?>" target="_blank" class="S16"><?php echo $title; ?></a>
					  <?php if ($flag==0)echo " <font class='C999'>(未审)</font>";?></td>
					  <td width="230" height="60" align="center" class="C999">
						<?php echo $yhtime.' '.getweek($yhtime);?><br />
						<div class="djs"><?php echo dating_djs($id,$flag,$jzbmtime,$jzbmtitle='离结束还剩',$jzbmtitle2='已经结束');?></div>
						<div style="margin-top:8px">
                        <a href="javascript:;" class="bai" onClick="bmlist(<?php echo $id; ?>,'<?php echo urlencode($title);?>');">报名管理 <font class='Cf00'><?php echo $bmnum; ?></font>人</a>　
                        <?php if ($flag == 1){?>
                        <a href="javascript:;" class="bai" onClick="bmstop(<?php echo $id; ?>);">结束</a>
                        <?php }?>
                        </div>
					  </td>
					  <td width="30" align="center">&nbsp;</td>
					  <td width="70" align="center">
					  <?php if ($flag == 0){?>
					  <button type="button" onclick="dating_mod(<?php echo $id;?>)" class="bai">修改</button>
					  <?php }else{echo '&nbsp;';}?>
                      </td>
					  <td width="70" align="center"><a href="javascript:;" class="bai" onClick="dating_del('<?php echo $id;?>','<?php echo $p;?>')">删除</a></td>
					</tr>
					<?php }?>
					</table>
					<?php
					if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox mypagebox">'.$pagelist.'</div>';
					}else{echo nodatatips('暂无约会<br><br><a class="btn HONG" onclick="dating_add();">＋我要发起约会</a>');}
				//我参加的
				}elseif($t==2){
					if($total>0){$page_skin=2;$pagemode=4;$pagesize=8;$page_color='#E83191';require_once ZEAI.'sub/page.php';
					?>
					<table class="tablelist">
					<?php
					for($i=0;$i<$pagesize;$i++) {
						$rows = $db->fetch_array($rt,'name');
						if(!$rows) break;
						$uname     = dataIO($rows['uname'],'out');
						$nickname = dataIO($rows['nickname'],'out');
						$sex      = $rows['sex'];
						$grade    = $rows['grade'];
                        $photo_s  = $rows['photo_s'];
                        $photo_f  = $rows['photo_f'];
                        $nickname = (empty($nickname))?$uname:$nickname;
                        //
                        $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_s'.$sex.'.png';
                        $sexbg   = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
						$img_str = '<img src="'.$photo_s_url.'"'.$sexbg.'>';
						//
						$id       = $rows['id'];
						$uid      = $rows['uid'];
						$title    = dataIO($rows['title'],'out');
						$yhtime   = $rows['yhtime'];
						$jzbmtime = $yhtime;
						$yhtime   = YmdHis($yhtime,'YmdHi');
						$contact  = dataIO($rows['contact'],'out');
						$bmnum    = $rows['bmnum'];
						$click    = $rows['click'];
						$flag     = $rows['flag'];
						$bmflag   = $rows['bmflag'];
						?>
                        <tr>
                        <td width="70" height="60" align="right"><div class="yueico"><a href="<?php echo Href('u',$uid);?>"><?php echo $img_str; ?></a></div></td>
                        <td align="left" style="padding-left:15px">
                        <a href="<?php echo Href('u',$uid);?>"><?php echo uicon($sex.$grade).$nickname;?></a><br>
                        <a href="<?php echo Href('dating',$id);?>" target="_blank" class="S16"><?php echo $title; ?></a>
                        
                        </td>
                        <td width="300" height="60" align="center" class="C999">
						<?php echo $yhtime.' '.getweek($yhtime);?><br />
						<div class="djs"><?php echo dating_djs($id,$flag,$jzbmtime,$jzbmtitle='离结束还剩',$jzbmtitle2='已经结束');?></div>
                        <font class="S14">报名<?php echo $bmnum ?>人 / 围观<?php echo $click ?>人</font>
					  </td>
					  <td width="300" align="left" class="C999">
						<?php
                        if ($bmflag == 1){
						?>
                        <font class="Cf00">恭喜你！已被选中</font>，<?php echo $nickname;?>正在等候您准时赴约，Ta联系方法：<font class="C666"><?php echo htmlout($contact);?></font>
                        <br><button type="button" class="hong FR" onClick="ZeaiPC.chat(<?php echo $uid;?>);"><i class="ico">&#xe676;</i> <font>在线聊天</font></button>
                        <?php
                        }else{
                            if ($flag == 1){
                                echo '<font color=#ff6600>发起人正在考虑您的应约，请等候佳音</font>';
                            } else {
                                echo '<font color=#999999>已经落选，下次还有机会哟^_^</font>';
                            }
                        }
                        ?>                      
                      </td>
					  </tr>
					<?php }?>
					</table>
					<?php
					if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox mypagebox">'.$pagelist.'</div>';
					}else{echo nodatatips('您还没有参加过约会<br><br><a href="'.Href('dating').'" class="btn HONG">进入约会员大厅</a>');}
				}
            	?>
        	</div>
        </div>
        <!-- end C -->
</div></div></div>
<script src="js/my_dating.js"></script>
<script>//my_trendInit();</script>
<?php
require_once ZEAI.'p1/bottom.php';
function dating_djs($id,$flag,$jzbmtime,$jzbmtitle='离结束还剩',$jzbmtitle2='已经结束') {
	global $db;
	$d1  = ADDTIME;
	$d2  = $jzbmtime;
	$totals  = ($d2-$d1);
	$day     = intval( $totals/86400 );
	$hour    = intval(($totals % 86400)/3600);
	$hourmod = ($totals % 86400)/3600 - $hour;
	$minute  = intval($hourmod*60);
	if ($flag >= 2)$totals = -1;
	if (($totals) > 0) {
		//$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> </span>';
		$tmp='<span class="jzbmT"><i class="ico">&#xe634;</i> '.$jzbmtitle.'</span>';
		if ($day > 0){
			$outtime = $tmp."<span class=timestyle>$day</span>天";
		} else {
			$outtime = $tmp;
		}
		$outtime .= "<span class=timestyle>$hour</span>小时<span class=timestyle>$minute</span>分";
	} else {
		$outtime = '<b>'.$jzbmtitle2.'</b>';
		$db->query("UPDATE ".__TBL_DATING__." SET flag=2 WHERE flag=1 AND id=".$id);
	}
	$outtime = '<font>'.$outtime.'</font>';
	return $outtime;
}
ob_end_flush();
?>
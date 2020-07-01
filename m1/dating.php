<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
//$i2=(!empty($i))?$i:'';
if($submitok == 'addupdate' || $submitok == 'modupdate' || $submitok == 'add' || $submitok == 'ajax_detail_bm' ){
	$currfields = "sex,grade,if2,loveb,qq,mob,weixin,mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_edu,mate_love,mate_areaid";
	if($submitok == 'ajax_detail_bm'){
		$$rtn='json';
		$chk_u_jumpurl=HOST.'/?z=dating&e=detail&a='.$clsid;
	}elseif($submitok == 'add'){
		$$rtn='json';
		$chk_u_jumpurl=HOST.'/?z=dating&e=add';
	}else{
		$chk_u_jumpurl=HOST.'/?z=dating';
	}
	require_once ZEAI.'my_chk_u.php';
}
require_once ZEAI.'cache/udata.php';
$nodatatips = "<div class='nodatatips' style='margin:0 auto'><br><br><i class='ico'>&#xe651;</i>暂时木有内容～～</div>";

//
/*********************** 详情 detail ***************************/
if (ifint($id)){
	$mini_title = '<i class="ico goback" id="ZEAIGOBACK-dating_detail">&#xe602;</i><font id="dating_minitt">约会</font><a href="#content" class="btn_save" id="dating_href">我要赴约</a>';
	$mini_backT = '返回';
	require_once ZEAI.'m1/top_mini.php';
	echo '<div class="submain dating detail">';
	$SQL = "";
	//

	$rt = $db->query("SELECT a.id,a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,a.job,a.birthday,a.love,a.dataflag,a.areatitle,a.aboutus,b.datingkind,b.title,b.areatitle,b.price,b.yhtime,b.maidian,b.contact,b.content,b.sex,b.age1,b.age2,b.heigh1,b.heigh2,b.love,b.edu,b.bmnum,b.click,b.flag FROM ".__TBL_USER__." a,".__TBL_DATING__." b WHERE a.id=b.uid AND a.flag=1 ".$SQL." AND b.id=".$id);//AND b.flag>0 
	
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt,'num');
		$uid = $row[0];
		$flag  = $row[29];
		if($uid!=$cook_uid && $flag==0)exit($nodatatips);
		$nickname = dataIO($row[1],'out');
		$Usex     = $row[2];
		$grade    = $row[3];
		$photo_s  = $row[4];
		$photo_f  = $row[5];
		$Ujob = $row[6];
		$Ubirthday = $row[7];
		$Ulove = $row[8];
		$Udataflag  = $row[9];
		$Uareatitle = $row[10];
		$Uaboutus   = dataIO($row[11],'out');
		//
		$datingkind = $row[12];
		$title      = dataIO($row[13],'out');
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
		
		$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$Usex.'.png';
		$img_str     = '<img src="'.$photo_s_url.'" class="sexbg'.$Usex.'">';
	} else {exit($nodatatips);}
	switch ($datingkind){ 
		case 1:$tbody = "_喝茶小叙";break;
		case 2:$tbody = "_共进晚餐";break;
		case 3:$tbody = "_相约出游";break;
		case 4:$tbody = "_看电影";break;
		case 5:$tbody = "_欢唱K歌";break;
	}
	$db->query("UPDATE ".__TBL_DATING__." SET click=click+1 WHERE id=".$id);
	$iflogin = false;$ifbest=0;
	if (ifint($cook_uid,'0-9','1,8')){
		$row = $db->NUM($cook_uid,"mob,qq,weixin");
		$mob    = dataIO($row[0],'out');
		$qq     = dataIO($row[1],'out');
		$weixin = dataIO($row[2],'out');
		$iflogin = true;
		//ifbest 约会同意结速后查最佳人选
		if($flag==2){
			$row2 = $db->ROW(__TBL_DATING_USER__,"id","flag=1 AND uid=".$cook_uid." AND fid=".$id);
			if ($row2)$ifbest=1;
		}
	}
	$flagstr = ($flag==0)?'<font class="Cf00 S12">【未审】</font>':'';
	if (is_weixin()){?>
	<script>
        var share_title2 = '<?php echo strip_tags(TrimEnter($title)); ?>_约么？_<?php echo $_ZEAI['siteName'];?>';
        var share_desc2  = '<?php echo strip_tags(TrimEnter(dataIO($content,'out',50))); ?>';
        var share_link2  = '<?php echo HOST; ?>?z=dating&e=detail&a=<?php echo $id; ?>';
        var share_imgUrl2= '<?php echo $photo_s_url; ?>';
		wx.ready(function () {
			wx.onMenuShareAppMessage({title:share_title2,desc:share_desc2,link:share_link2,imgUrl:share_imgUrl2});
			wx.onMenuShareTimeline({title:share_title2,link:share_link2,imgUrl:share_imgUrl2});
		});
    </script>
    <?php
	}
	?>
    <!--正文显示-->
    <div class="read">
        <div class="titled">
            <div class="titleL"><a onClick="ZeaiM.page.load('m1/u'+zeai.ajxext+'uid=<?php echo $uid; ?>','dating_detail','u');"><?php echo $img_str; ?></a></div>
            <div class="titleR">
                <h1><?php echo $title.$flagstr;?>
				<?php if ($cook_uid == $uid ){
					if ($flag==0)echo'<a onclick="dating_delmy('.$id.');" class="delmy">删除</a>';
					if ($flag>0)echo'<a onclick="dating_manage('.$id.',\''.urlencode($title).'\');" class="delmy">管理报名人员（'.$bmnum.'人）</a>';
				}?>
                </h1>
                <span>围观<?php echo $click; ?>　已报名<b class="hot"><?php echo $bmnum ?></b>人</span>
            </div>
        </div>
        <em>
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
            <dl><dt>电话/QQ</dt><dd><?php if ($ifbest == 1 || $uid==$cook_uid){echo '<font>'.$contact.'</font>';}else{echo '只有发起人选中的最佳人选才可以看见！';} ?></dd></dl>
            <dl><dt>约会内容</dt><dd><?php echo $content; ?></dd></dl>
            <dl><dt>约会对象</dt><dd>
            
			<span><?php if ($sex >0){echo udata('sex',$sex).'性　';} ?></span>
			<span><?php if (!empty($age1) && !empty($age2)){echo $age1.'～'.$age2.'岁　';}else{echo $nulltext;} ?></span>
			<span><?php if (!empty($heigh1) && !empty($heigh2)){echo $heigh1.'～'.$heigh2.'厘米　';} ?></span>
			<?php if (ifint($love)){?><?php echo udata('love',$love);?></span>　<?php }?>
			<?php if (ifint($edu)){?><span><?php echo udata('edu',$edu);?></span><?php }?>
  
            </dd></dl>
			<br><div class="linebox"><div class="line"></div><div class="title BAI S16">报名赴约</div></div>

            <form id="www_zeai_cn_FORM" class="bmform">
                <textarea onBlur="zeai.setScrollTop(0);" id="content" class="textarea" name="content"<?php if ($flag > 1 ){echo ' disabled="disabled"';}?>><?php if ($flag == 1){echo "联系电话：".$mob."\nQQ：".$qq."\n微信：".$weixin."\n应约说明：";}elseif($flag==0){echo "此约会审核中";}else{echo "此约会已经结束或已有最佳人选，报名终止。";}?></textarea>
                <span>此联系方式不会公开，只有约会发起人才能看见，请放心填写。</span>
                <input type="button" class="btn size4 yuan HONG B" value="　　确认赴约　　"<?php if ($flag != 1 /*|| !$iflogin*/ ){echo ' style="background-color:#ccc" disabled="disabled"';}?> id="dating_btn_detailBM" />
                <input type="hidden" name="clsid" value="<?php echo $id; ?>">
                <input type="hidden" name="uid" id="uid" value="<?php echo $uid; ?>">
                <input type="hidden" name="submitok" value="ajax_detail_bm">
            </form>
        </em>
    </div>
    <script>
	dating_btn_detailBM.onclick=dating_btn_detailBMfn;
	<?php if($flag==0 || $uid==$cook_uid){?>dating_href.remove();<?php }?>
	<?php if($uid==$cook_uid){?>dating_minitt.html('我的约会');<?php }?>
	console.log(<?php echo $aaa;?>);
	<?php if ($e == 'detail' && ifint($a) && !empty($ii)){?>ZeaiM.page.load('m1/dating.php?submitok=<?php echo $ii;?>&clsid=<?php echo $a;?>','dating_detail','dating_detail_bmuser');<?php }?>
	zeaiLoadBack=['nav','topminibox'];
    </script>
    <!--正文显示结束-->
<?php
exit('</div>');}
/*报名**********************详情detail报名BM**************************报名*/
//报名会员管理 html
if ($submitok == 'ajax_dating_bmuser'){
	if(!ifint($clsid))json_exit(array('flag'=>0,'msg'=>'zeai_error_clsid'));
	$mini_title = '<i class="ico goback" id="ZEAIGOBACK-dating_detail_bmuser">&#xe602;</i>报名会员管理';
	$mini_backT = '返回';
	require_once ZEAI.'m1/top_mini.php';
	echo '<div class="submain dating"><div class="listboxu" id="dating_listboxu">';
	$fid=$clsid;
	$rt=$db->query("SELECT a.sex,a.grade,a.nickname,a.photo_s,a.photo_f,a.job,a.birthday,a.love,b.id as bbsid,b.uid,b.content,b.addtime,b.flag AS bbsflag,c.flag FROM ".__TBL_USER__." a,".__TBL_DATING_USER__." b,".__TBL_DATING__." c WHERE a.id=b.uid AND c.id=".$fid." AND b.fid=".$fid." ORDER BY b.flag DESC,b.id DESC");
	$total = $db->num_rows($rt);
	if ($total <= 0){exit($nodatatips);}else{
		echo '<div class="bmtbody"><h3>'.urldecode($title).'</h3>报名总人数<b class="hot">'.$total.'</b></div>';
		for($ii=0;$ii<$total;$ii++) {
			$rows = $db->fetch_array($rt);
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
			$content   = strip_tags(dataIO($rows[10],'out'));
			$addtime   = $rows[11];
			$bbsflag   = $rows[12];
			$flag      = $rows[13];
			$birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁';
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_s'.$sex.'.png';
			$sexbg       = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			echo '<li fid="'.$fid.'" clsid="'.$bbsid.'"><img class="photo_s" src="'.$photo_s_url.'" uid="'.$uid.'"'.$sexbg.'><h4>'.uicon($sex.$grade).$nickname.$flagstr.'</h4><h6>'.$birthday_str.'　'.udata('love',$love).'　'.udata('job',$job).'</h6>';
			if ($flag == 1) {
				echo'<a class="btn size2" nickname="'.urldecode($nickname).'">邀请此人</a>';
			}else{
				if ($bbsflag == 1){
					echo "<span class='best'>最佳人选</span><span class='contact'>".$content."</span>";
				}
			}
			echo'</li>';
		}?>
		<script>dating_listBmBoxuInit(dating_listboxu);</script>
        <?php
	}
	exit('</div></div>');
////// 报名会员管理 php //////
}elseif($submitok=='ajax_dating_bmuser_update'){
	if(!ifint($clsid) || !ifint($fid))json_exit(array('flag'=>0,'msg'=>'zeai_error_clsid'));
	$id=$clsid;
	$rt = $db->query("SELECT uid FROM ".__TBL_DATING_USER__." WHERE flag=0 AND fid=".$fid." AND id=".$id);
	if($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
	$bbsuid = $row[0];
	}else{json_exit(array('flag'=>0,'msg'=>'zeai_error_fid_clsid'));}
	$rt = $db->query("SELECT uid,title FROM ".__TBL_DATING__." WHERE flag=1 AND id=".$fid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$wzuid = $row[0];
		$titleOld = dataIO($row[1],'out');
	}else{
		json_exit(array('flag'=>0,'msg'=>'已过期或未知错误'));
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
	
	
////// 报名按钮提交处理 php //////
}elseif($submitok=='ajax_detail_bm'){
	if ($uid == $cook_uid)json_exit(array('flag'=>0,'msg'=>'不能操作自已'));
	if(!ifint($clsid))json_exit(array('flag'=>0,'msg'=>'zeai_error_clsid'));
	$id=$clsid;
	$content = dataIO($content,'in',1000);
	if (str_len($content)>1000 || str_len($content)<3)json_exit(array('flag'=>0,'msg'=>'信息内容过多或过少'));
	
	$row2 = $db->ROW(__TBL_DATING__,"uid,flag,title,age1,age2,heigh1,heigh2,love,edu,sex","id=".$id,"num");
	if($row2){
		$uid = $row2[0];
		$title = dataIO($row2[2],'out');
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
	if($db->ROW(__TBL_DATING_USER__,"id","uid=".$cook_uid." AND fid=".$id))json_exit(array('flag'=>0,'msg'=>'你已经报过名，请不要重复操作'));
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
		$C = $nickname.'有人报名了你发起的约会【'.$title.'】　<a href='.Href('dating',$id).' class=aQING>查看详情</a>';
		$db->SendTip($uid,'你发布的约会有人报名了',dataIO($C,'in'),'sys');
		
		//微信通知
		if (!empty($openid) && $subscribe==1){
			$wxurl = urlencode(HOST.'?z=dating&e=detail&a='.$id);
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
}elseif($submitok=='ajax_detail_del'){
	if(!ifint($clsid))json_exit(array('flag'=>0,'msg'=>'zeai_error_clsid'));
	$db->query("DELETE FROM ".__TBL_DATING__." WHERE flag=0 AND uid=".$cook_uid." AND id=".$clsid);
	$db->query("DELETE FROM ".__TBL_DATING_USER__." WHERE fid=".$clsid);
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}
/***********************主体入口***************************/
$_ZEAI['pagesize']= 10;
$SQL = "";
switch ($t){ 
	case 1:$SQL = " AND a.datingkind=1 ";break;
	case 2:$SQL = " AND a.datingkind=2 ";break;
	case 3:$SQL = " AND a.datingkind=3 ";break;
	case 4:$SQL = " AND a.datingkind=4 ";break;
	case 5:$SQL = " AND a.datingkind=5 ";break;
}
if ($submitok == 'my' && ifint($cook_uid)){
	$_ZEAI['pagesize']= 500;
	$SQL .= " AND a.uid=".$cook_uid;
}else{
	$SQL .= " AND a.flag>0 ";
}

$RTSQL = "SELECT a.id,a.uid,a.title,a.bmnum,a.click,a.flag,b.sex,b.photo_s,b.photo_f,a.areatitle FROM ".__TBL_DATING__." a,".__TBL_USER__." b WHERE a.uid=b.id AND b.flag=1 ".$SQL." ORDER BY a.px DESC";
if ($submitok == 'ajax_list'){
	exit(ajax_list_fn($totalP,$p));
/***********************发布入库/修改 php ***************************/
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
	if (str_len($contact)>100 || str_len($contact)<1)json_exit(array('flag'=>0,'msg'=>'联系方式请控制在1~100字节'));
	if (str_len($content)>2000 || str_len($content)<10)json_exit(array('flag'=>0,'msg'=>'约会内容请控制在10~2000字节'));
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
		
		$db->query("INSERT INTO ".__TBL_DATING__." (uid,datingkind,title,areaid,areatitle,price,yhtime,maidian,contact,content,sex,age1,age2,heigh1,heigh2,love,edu,addtime,px,flag) VALUES ($cook_uid,$datingkind,'$title','$areaid','$areatitle',$price,$yhtime,$maidian,'$contact','$content',$sex,$age1,$age2,$heigh1,$heigh2,$love,$edu,".ADDTIME.",".ADDTIME.",$flag)");
		json_exit(array('flag'=>1,'msg'=>'发布成功，请等待客服审核','url'=>HOST.'/?z=dating&submitok=my'));
		
	}elseif($submitok == 'modupdate'){
		
		if (!ifint($fid))exit(JSON_ERROR);
		$db->query("UPDATE ".__TBL_DATING__." SET datingkind='$datingkind',title='$title',areaid='$areaid',areatitle='$areatitle',price='$price',yhtime='$yhtime',maidian='$maidian',contact='$contact',content='$content',sex='$sex',age1='$age1',age2='$age2',heigh1='$heigh1',heigh2='$heigh2',love='$love',edu='$edu',flag=$flag WHERE id=".$fid);
		json_exit(array('flag'=>1,'msg'=>'修改成功，请等待客服审核'));
		
	}
/***********************发布 html ***************************/
}elseif($submitok == 'add'){
	$mini_title = '<i class="ico goback" id="ZEAIGOBACK-dating_add">&#xe602;</i>发布约会<a id="dating_btn_save" class="btn_save">保存</a>';
	$mini_backT = '返回';
	require_once ZEAI.'m1/top_mini.php';
	echo '<div class="submain C">';
	$data_qq    = dataIO($row['qq'],'out');
	$data_mob   = dataIO($row['mob'],'out');
	$data_weixin= dataIO($row['weixin'],'out');
	$age1 = $row['mate_age1'];
	$age2 = $row['mate_age2'];
	$heigh1 = $row['mate_heigh1'];
	$heigh2 = $row['mate_heigh2'];
	$edu = $row['mate_edu'];
	$love = $row['mate_love'];
	$areaid = $row['mate_areaid'];
	$areaid = explode(',',$areaid);
	$m1 = $areaid[0];$m2 = $areaid[1];$m3 = $areaid[2];
	$sex = $row['sex'];
	$sex=($sex==1)?2:1;
	$nulltext='请选择';
	if (!empty($data_mob))$mob='手机：'.$data_mob;
	if (!empty($data_qq))$qq  ='　QQ：'.$data_qq;
	if (!empty($data_weixin))$weixin='　微信：'.$data_weixin;
	$data_contact = $mob.$qq.$weixin;
	?>
	<script>var nulltext = '不限';</script>
    <br><div class="linebox"><div class="line"></div><div class="title BAI S16">约会内容</div></div>
    <form id="www_zeai_cn_FORM">
    <dl><dt>约会类型</dt><dd><select name="datingkind" id="datingkind" class="select W150">
    <option value="0" selected="selected"><?php echo $nulltext; ?></option>
    <option value="1">喝茶小叙</option>
    <option value="2">共进晚餐</option>
    <option value="3">相约出游</option>
    <option value="4">看电影</option>
    <option value="5">欢唱K歌</option>
    <option value="6">其他</option>
  </select></dd></dl>
    <dl><dt>约会主题</dt><dd><input name="title" class="input W100_" id="title" /></dd></dl>
    <dl><dt>费用预算</dt><dd><select name="price" id="price" class="select W150">
    <option value="0" selected="selected"><?php echo $nulltext; ?></option>
    <option value="1">100元以下</option>
    <option value="2">100--300元</option>
    <option value="3">300--500元</option>
    <option value="4">500元以上</option>
  </select></dd></dl>

    <dl><dt>约会时间</dt><dd>
    <input name="year8" type="text" class="input W50" id="year8" pattern="[0-9]" value="<?php echo date("Y"); ?>" size="4" maxlength="4" /> 年
    <input name="month8" type="text" class="input W50" id="month8" pattern="[0-9]" size="2" maxlength="2" value="<?php echo date("m"); ?>" /> 月
    <input name="day8" type="text" class="input W50" id="day8"  pattern="[0-9]" onkeypress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" size="2" maxlength="2" /> 日　
    <input name="hour8" type="text" class="input W50" id="hour8" pattern="[0-9]" value="20" size="2" maxlength="2" /> 时
    <input name="minute8" type="text" class="input W50" id="minute8" pattern="[0-9]" value="30" size="2" maxlength="2" /> 分<span class="tips">（例如： <?php echo date("Y"); ?>-07-07 19:30）</span></dd></dl>
    
    <dl><dt>谁来买单</dt><dd><select name=maidian id="maidian" class="select W150">
    <option value="0">不限</option>
    <option value=1>我来买单</option>
    <option value=2>应约人买单</option>
    <option value="3">AA制</option>
    </select></dd></dl>
    
    <dl><dt>所在地区</dt><dd id="area1_box"><script>LevelMenu3('m1|m2|m3|'+nulltext+'|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>','class="select SW"',o('area1_box'));</script></dd></dl>
    <dl><dt>联系方式</dt><dd><input name="contact" class="input W100_" id="contact" value="<?php echo $data_contact; ?>" /><span class="tips">请填写手机/QQ/微信（仅显示给成功赴约人，不会公开）</span></dd></dl>
    <dl><dt>约会内容</dt><dd><textarea name="content" id="content" rows="5" class="textarea W100_"></textarea><span class="tips">如：约会大概内容、详细约会地点、交通路线、注意事项等。</span></dd></dl>
    
    
    <br><div class="linebox"><div class="line"></div><div class="title BAI S16">约会对象</div></div>

    <dl><dt>性　　别</dt><dd id="sex_box"><script>zeai_cn__CreateFormItem_ajax('select','sex','<?php echo $sex; ?>','class="select SW"',sex_ARR,o('sex_box'));</script></dd></dl>
    
    <dl><dt>年　　龄</dt><dd id="age_box">
    <script>
        zeai_cn__CreateFormItem_ajax('select','age1','<?php echo $age1; ?>','class="select SW"',age_ARR,o('age_box'));
        age_box.append(' ～ ');
        zeai_cn__CreateFormItem_ajax('select','age2','<?php echo $age2; ?>','class="select SW"',age_ARR,o('age_box'));
    </script>
    </dd></dl>
    
    <dl><dt>身　　高</dt><dd id="heigh_box">
    <script>
        zeai_cn__CreateFormItem_ajax('select','heigh1','<?php echo $heigh1; ?>','class="select SW"',heigh_ARR,o('heigh_box'));
        heigh_box.append(' ～ ');
        zeai_cn__CreateFormItem_ajax('select','heigh2','<?php echo $heigh2; ?>','class="select SW"',heigh_ARR,o('heigh_box'));
    </script>
    </dd></dl>
    
    <dl><dt>最低学历</dt><dd id="edu_box"><script>zeai_cn__CreateFormItem_ajax('select','edu','<?php echo $edu; ?>','class="select SW"',edu_ARR,o('edu_box'));</script></dd></dl>
    
    <input name="submitok" type="hidden" value="addupdate" />
    <input name="areaid" id="areaid" type="hidden" value="" />
    <input name="areatitle" id="areatitle" type="hidden" value="" />
    <script>dating_btn_save.onclick = dating_btn_saveFn;addInit();</script> 
<?php echo '</div>';exit;}



/*********************** BODY 开始***************************/
switch ($t){ 
	case 1:$tTitle="喝茶小叙";break;
	case 2:$tTitle="共进晚餐";break;
	case 3:$tTitle="相约出游";break;
	case 4:$tTitle="看电影";break;
	case 5:$tTitle="欢唱K歌";break;
	case 6:$tTitle="其他";break;
	default:$tTitle="不限，都可以";break;
}
$headertitle = '会员约会 - '.$tTitle.' - ';
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
		jsApiList: ['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	//
	var share_title = '约会大厅 - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>',
	share_desc  = '喝茶小叙 共进晚餐 相约出游 看电影 欢唱K歌，约么？',
	share_link  = '<?php echo HOST; ?>/?z=dating',
	share_imgUrl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_title,desc:share_desc,link:share_link,imgUrl:share_imgUrl});
		wx.onMenuShareTimeline({title:share_title,link:share_link,imgUrl:share_imgUrl});
	});
	</script>
<?php }
$mainT=($tTitle=='不限，都可以')?'约会':$tTitle;
if($submitok=='my')$mainT='我的约会';
$mini_title = '　　　'.$mainT.'<a id="btn_add" class="btn_add ico">&#xe620;</a>';
$mini_class = 'top_mini huadong';
$mini_ext = 'id="topminibox"';
$nav = 'trend';
?>
<link href="m1/css/dating.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />

<?php if($_ZEAI['mob_mbkind']==3){?>
<style>
.top_mini{background:#FF6F6F}
.datingkind .kind a.ed span{color:#FF6F6F}
.datingkind .kind a.ed i{background-color:#FF6F6F}
</style>
<?php }?>
<script src="cache/udata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="cache/areadata.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="res/select3_ajax.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php require_once ZEAI.'m1/top_mini.php';?>
<main id="main" class="main huadong dating">

    <div class="datingkind">
    <div class="kind">
        <a href="../?z=dating"<?php echo ($t==0)?'class="ed"':''; ?>><i class="ico2">&#xe609;</i><span>全部约会</span></a>
        <a href="../?z=dating&t=1"<?php echo ($t==1)?'class="ed"':''; ?>><i class="ico2">&#xe60e;</i><span>喝茶小叙</span></a>
        <a href="../?z=dating&t=2"<?php echo ($t==2)?'class="ed"':''; ?>><i class="ico2">&#xe6a9;</i><span>共进晚餐</span></a>
        <a href="../?z=dating&t=3"<?php echo ($t==3)?'class="ed"':''; ?>><i class="ico2">&#xe656;</i><span>相约出游</span></a>
        <a href="../?z=dating&t=4"<?php echo ($t==4)?'class="ed"':''; ?>><i class="ico2">&#xe600;</i><span>看电影</span></a>
        <a href="../?z=dating&t=5"<?php echo ($t==5)?'class="ed"':''; ?>><i class="ico2">&#xe739;</i><span>欢唱K歌</span></a>
    </div>
    </div>
    
    <!--主BOX-->
    <div id="list" class="listbox">
		<?php
        $total = $db->COUNT(__TBL_DATING__," flag >= 1");
		$totalP = ceil($total/$_ZEAI['pagesize']);
        echo ajax_list_fn($totalP,1);
		?>
    </div>
    <?php
require_once ZEAI.'m1/footer.php';
?>
</main>
<?php if ($total > $_ZEAI['pagesize']){?>
<script>
	var totalP = parseInt(<?php echo $totalP; ?>),p=2,t='<?php echo $t; ?>';
	zeai.ready(function(){o('main').onscroll = listOnscroll;});
</script>
<?php }?>
<?php 
if($_ZEAI['mob_mbkind']==3){
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}
?>
<script src="m1/js/dating.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
zeaiLoadBack=['nav','topminibox'];
var browser='<?php echo (is_weixin())?'wx':'h5';?>';
<?php if (!empty($_GZH['wx_gzh_ewm']) && ifint($cook_uid)){wx_endurl('您刚刚浏览的页面【会员约会】',HOST.'/?z=dating');}?>
btn_add.onclick=function(){ZeaiM.page.load('m1/dating.php?submitok=add',ZEAI_MAIN,'dating_add');}
setList(list);
<?php if ($e == 'add'){?>ZeaiM.page.load('m1/dating.php?submitok=add',ZEAI_MAIN,'dating_add');<?php }?>

<?php if ($e == 'detail' && ifint($a) && !empty($i)){?>
	ZeaiM.page.load('m1/dating.php?clsid=<?php echo $a;?>&submitok=<?php echo $i;?>',ZEAI_MAIN,'dating_detail_bmuser');
<?php }else{?>
<?php if ($e == 'detail' && ifint($a)){?>ZeaiM.page.load('m1/dating.php?id=<?php echo $a;?>&i=<?php echo $i;?>',ZEAI_MAIN,'dating_detail');<?php }}?>
</script>
<?php
function ajax_list_fn($totalP,$p) {
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
	$rows_list='';
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_list .= rows_list($rows);
	}
	return $rows_list;
}
function rows_list($rows) {
	global $_ZEAI,$db,$cook_uid;
	$id    = $rows['id'];
	$uid   = $rows['uid'];
	$title = dataIO($rows['title'],'out');
	$bmnum  = $rows['bmnum'];
	$click  = $rows['click'];
	$sex  = $rows['sex'];
	$photo_f  = $rows['photo_f'];
	$photo_s  = $rows['photo_s'];
	$flag    = $rows['flag'];
	$areatitle = $rows['areatitle'];
	if(!empty($areatitle)){
		$areatitle = explode(' ',$areatitle);
		if(empty($areatitle[1]) && empty($areatitle[2])){
			$areatitle = $areatitle[0];
		}else{
			$areatitle = $areatitle[1].'-'.$areatitle[2];
		}
	}
	//
	$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
	$sexbg       = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
	$flagstr = ($flag==0)?'<font class="Cf00 S12">【未审】</font>':'';
	$echo = '<li clsid="'.$id.'"><img src="'.$photo_s_url.'" uid="'.$uid.'"'.$sexbg.'><h4>'.$title.$flagstr.'</h4><h6>'.$areatitle.' 围观'.$click.'　已报名<b class="hot">'.$bmnum.'</b>人</h6></li>';
	return $echo;
}
?>
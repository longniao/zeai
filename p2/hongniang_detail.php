<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'红娘ID错误'));
if($submitok == 'ajax_join_update'){
	if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'红娘ID错误'));
	if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请留下正确的手机号码，方便给您服务哦'));
	if ($db->ROW(__TBL_USER__,"id","id<>".$cook_uid." AND (   (mob='$mob' AND FIND_IN_SET('mob',RZ))   OR  (qq<>'' AND qq='$qq' AND FIND_IN_SET('qq',RZ))  )   "))json_exit(array('flag'=>0,'msg'=>'此手机号码或QQ已被其他会员占用，请更换'));

	$hid=$fid;

	//当前红娘
	$rowf = $db->ROW(__TBL_CRM_HN__,"id,uid,truename,agentid,agenttitle","ifwebshow=1 AND flag=1 AND id=".$hid,'name');
	if (!$rowf)json_exit(array('flag'=>0,'msg'=>'红娘不存在'));
	$hn_uid        = $rowf['uid'];
	$hn_truename   = dataIO($rowf['truename'],'out');
	$hn_agentid    = intval($rowf['agentid']);
	$hn_agenttitle = $rowf['agenttitle'];

	//我
	$row2 = $db->ROW(__TBL_USER__,"nickname,sex,photo_s,photo_f,birthday,hnid,bz,admid,admname,hnid,hnname,hnid2,hnname2","id=".$cook_uid,"name");
	if ($row2){
		$mob     = dataIO($mob,'in',11);
		$qq      = dataIO($qq,'in',50);
		$weixin  = dataIO($weixin,'in',50);
		$truename= dataIO($truename,'in',50);
		
		$admid   = intval($row2['admid']);$admname= dataIO($row2['admname'],'out');
		$hnid    = intval($row2['hnid']);;$hnname= dataIO($row2['hnname'],'out');
		$hnid2   = intval($row2['hnid2']);;$hnname2= dataIO($row2['hnname2'],'out');
		if($admid>0)json_exit(array('flag'=>0,'msg'=>'您已经被红娘【'.$admname.' -> ID:'.$admid.'】认领，我们会很快联系您哦'));
		if($hnid>0)json_exit(array('flag'=>0,'msg'=>'您已经被分配到-售前红娘【'.$hn_agenttitle.' -> '.$hn_truename.'】，我们会很快联系您哦'));
		if($hnid2>0)json_exit(array('flag'=>0,'msg'=>'您已经被分配到-售后红娘【'.$hnname2.' -> ID:'.$hnid2.'】正在为您服务中'));
		
		if($hnid==0 && $hnid2==0){
			$SQLA = ",agentid=$hn_agentid,agenttitle='$hn_agenttitle',hnid=".$hid.",hnname='$hn_truename',hntime=".ADDTIME.",admid=".$hid.",admname='$hn_truename',admtime=".ADDTIME;
		}else{
			$SQLA = ",hnid=".$hid.",hnname='$hn_truename',hntime=".ADDTIME.",admid=".$hid.",admname='$hn_truename',admtime=".ADDTIME;
		}
		
		if(ifint($uuid)){
			$bz = $row2['bz'].'【我看中了UID：'.$uuid.'这个会员，请帮我牵线】';
			$sql= ",bz='$bz'";
		}
		
		$db->query("UPDATE ".__TBL_USER__." SET truename='$truename',mob='$mob',qq='$qq',weixin='$weixin'".$SQLA.$sql." WHERE (flag=1 OR flag=-1) AND id=".$cook_uid);//kind=3,
	}
	//通知红娘
	$row = $db->NUM($hn_uid,"nickname,openid,subscribe");
	if ($row){
		$hn_nickname = dataIO($row[0],'out');
		$hn_openid   = $row[1];
		$hn_subscribe= $row[2];
		//站内tips发给红娘
		$sex_title = ($cook_sex == 1)?'他':'她';
		$T = '【'.$cook_nickname.' uid:'.$cook_uid.'】委托您为'.$sex_title.'牵线服务';
		$C = $T.'，请到CRM后台【售前管理】进行联系跟进服务';
		$db->SendTip($hn_uid,$T,dataIO($C,'in'),'sys');
		//微信通知
		if ($hn_subscribe == 1 && !empty($hn_openid)){
			//客服通知
			$content = urlencode($C);
			$ret = @wx_kf_sent($hn_openid,$content,'text');
			$ret = json_decode($ret);
			//模版通知
			if ($ret->errmsg != 'ok'){
				$keyword1  = $content;
				$keyword3  = urlencode($_ZEAI['siteName']);
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$hn_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
		}
	}
	json_exit(array('flag'=>1,'msg'=>'申请已发送，请等待【'.$hn_truename.'】接洽～','C'=>$CC));
/************************ BBS ***********************/
exit;}elseif($submitok == 'ajax_hnBBS_update'){
	if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'红娘ID错误'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发布'));
	$$rtn='json';$chk_u_jumpurl=Href('hongniang',$fid);
	require_once ZEAI.'my_chk_u.php';
	//
	if ( (str_len($content)>10000 || str_len($content)<1) )json_exit(array('flag'=>0,'msg'=>'评价内容请控制在1~1000字节以内'));
	if ($kind != 1 && $kind != 2 && $kind != 3)json_exit(array('flag'=>0,'msg'=>'请打分【好评－中评－差评】'));
	switch ($kind) {
		case 1:$sql  = " ,pj_good=pj_good+1 ";break;
		case 2:$sql  = " ,pj_normal=pj_normal+1 ";break;
		case 3:$sql  = " ,pj_bad=pj_bad+1 ";break;
	}
	$row = $db->ROW(__TBL_HN_BBS__,"id","uid=".$cook_uid." AND hid=".$fid);
	if ($row)json_exit(array('flag'=>0,'msg'=>'您已经评价过了～请不要重复评价'));
	
	$row = $db->ROW(__TBL_USER__,"nickname,sex,photo_s,photo_f","id=".$cook_uid." AND hnid=".$fid);
	if ($row){
		$content = dataIO($content,'in',1000);
		$db->query("INSERT INTO ".__TBL_HN_BBS__." (uid,hid,content,addtime,pjkind) VALUES ($cook_uid,'$fid','$content',".ADDTIME.",$kind)");
		$db->query("UPDATE ".__TBL_CRM_HN__." SET bbsnum=bbsnum+1 ".$sql." WHERE id=".$fid);
		setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'评价成功','C'=>$CC,'pjkind'=>$pjkind));
	}else{
		json_exit(array('flag'=>0,'msg'=>'您没有找Ta牵线，无法评价哦'));
	}
}elseif($submitok == 'ajax_iflogin'){
	if(!iflogin() || !ifint($cook_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来申请','jumpurl'=>Href('hongniang',$fid)));
	json_exit(array('flag'=>1));
}
$row = $db->ROW(__TBL_CRM_HN__,"sex,qq,weixin,mob,path_s,ewm,truename,sex,aboutus,title,pj_good,pj_normal,pj_bad,click","flag=1 AND id=".$fid,"name");
if ($row){
	$truename = trimhtml(dataIO($row['truename'],'out',7));
	$aboutus  = trimhtml(dataIO($row['aboutus'],'out'));
	$title    = trimhtml(dataIO($row['title'],'out'));
	$pj_good  = $row['pj_good'];
	$pj_normal= $row['pj_normal'];
	$pj_bad   = $row['pj_bad'];
	$unum     = $row['unum'];
	$bbsnum   = $row['bbsnum'];
	$click    = $row['click'];
	$sex      = $row['sex'];
	$path_s   = $row['path_s'];
	$ewm      = $row['ewm'];
	$qq = dataIO($row['qq'],'out');
	$weixin = dataIO($row['weixin'],'out');
	$mob = dataIO($row['mob'],'out');
	//
	$sex_title= ($sex == 1)?'他':'她';
	$path_s_url = (!empty($path_s))?$_ZEAI['up2'].'/'.getpath_smb($path_s,'b'):HOST.'/res/photo_m'.$sex.'.png';
	$ewm_str    = (!empty($ewm))?'<img src="'.$_ZEAI['up2'].'/'.$ewm.'">':'暂无';
	$sexbg   = (empty($path_s))?' class="sexbg'.$sex.'"':'';
	$join_title = '～ 找'.$sex_title.'牵线 ～';	

	$db->query("UPDATE ".__TBL_CRM_HN__." SET click=click+1 WHERE id=".$fid);
	$unum = $db->COUNT(__TBL_USER__,"hnid=".$fid);
	if (ifint($cook_uid)){
		$row = $db->ROW(__TBL_USER__,"crm_flag","id=".$cook_uid." AND hnid=".$fid);
		if ($row){
			$joinclass  = ' class="addmeed"';	
			switch ($row[0]) {
				case 1:$join_title  = '正在服务中';break;
				case 2:$join_title  = '服务跟进中';break;
				case 3:$join_title  = '服务成功';break;
			}
		}
	}
}else{alert('暂无信息');}
require_once ZEAI.'cache/udata.php';
$nav='hongniang';$up2 = $_ZEAI['up2']."/";
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $truename;?>_红娘_<?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p2/css/p2.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p2/css/hongniang.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php if($submitok == 'ajax_join'){
    $currfields = "sex,grade,nickname,qq,mob,weixin,truename";
	$$rtn='json';$chk_u_jumpurl=Href('hongniang',$fid);
	require_once ZEAI.'my_chk_u.php';	
	//
	$data_sex      = $row['sex'];
	$data_grade    = $row['grade'];
	$data_nickname = dataIO($row['nickname'],'out');
	$data_qq       = dataIO($row['qq'],'out');
	$data_mob      = dataIO($row['mob'],'out');
	$data_weixin   = dataIO($row['weixin'],'out');
	$data_truename = dataIO($row['truename'],'out');
	//
	$row = $db->ROW(__TBL_CRM_HN__,"truename","id=".$fid,"num");
	if ($row){
		$hn_truename= dataIO($row[0],'out');
	}else{exit(JSON_ERROR);}
	?>
	<div class="hn_join">
        <dl><dt>我的帐号</dt><dd><em><?php echo uicon($data_sex.$data_grade).$data_nickname.'　UID：'.$cook_uid;?></em></dd></dl>
        <form id="WwW_Zeai_CN_hnJoin">
        <dl><dt>姓名</dt><dd><input name="truename" class="input W100_" value="<?php echo $data_truename;?>" /></dd></dl>
        <?php if (ifmob($data_mob)){?>
        	<dl><dt>手机</dt><dd style="line-height:40px"><?php echo $data_mob;?></dd></dl>
            <input type="hidden" name="mob" value="<?php echo $data_mob;?>" />
        <?php }else{ ?>
        	<dl><dt>手机</dt><dd><input name="mob" class="input W100_"  /></dd></dl>
        <?php }?>
        <dl><dt>QQ</dt><dd><input name="qq" class="input W100_" value="<?php echo $data_qq;?>" /></dd></dl>
        <dl><dt>微信</dt><dd><input name="weixin" class="input W100_" value="<?php echo $data_weixin;?>" /></dd></dl>
        <input type="hidden" name="fid" value="<?php echo $fid;?>" />
        <input type="hidden" name="uuid" id="uuid" value="" />
        <input name="submitok" type="hidden" value="ajax_join_update" />
        </form><br>
        <button type="button" id="hn_join_btn" class="btn size3 ">提交申请</button>
        <br><br><div class="linebox"><div class="line BAI W50"></div><div class="title S14 BAI">温馨提醒</div></div>
        <div>以上信息受隐私保护，仅用于红娘联系通知，不对外公开。</div>
		<script>hn_join_btn.onclick=function(){hn_join_btnFn();}</script>
        <script src="<?php echo HOST;?>/p2/js/hongniang_detail.js"></script>
	</div>
<?php exit;}
require_once ZEAI.'p2/top.php';?>
<div class="dtlbannerbox">
    <div class="dtlbanner"></div>
    <div class="hninfo S5">
    	<p style="background-image:url(<?php echo $path_s_url; ?>)"<?php echo $sexbg; ?>></p>
        <em>
            <div class="title">
				<h2><?php echo $truename;?><span><i class="ico">&#xe643;</i><font><?php echo $click;?></font></span></h2>
                <div class="pj">
                    <a href="#content">会员<font id="hn_unum"><?php echo $unum; ?></font>个</a>
                    <a href="#content">好评<font id="hn_pjkind1"><?php echo $pj_good; ?></font></a>
                    <a href="#content">中评<font id="hn_pjkind2"><?php echo $pj_normal; ?></font></a>
                    <a href="#content">差评<font id="hn_pjkind3"><?php echo $pj_bad; ?></font></a>
                </div>
                <div class="titlestr"><?php echo $title;?></div>
                <div class="aboutus"><?php echo $aboutus; ?></div>
            </div>
        </em>
		<a href="javascript:;" id="joinhn"<?php echo $joinclass; ?>><?php echo $join_title; ?></a>
    </div>
</div>
<div class="clear"></div>
<div class="dtl_contactbox">
	<h1>联系红娘</h1>
	<div class="dtl_contact">
        <li><i class="ico telico">&#xe60e;</i><span><?php echo $mob; ?>&nbsp;</span><font>红娘热线</font></li>
        <li><i class="ico qqico">&#xe612;</i><span><?php echo $qq; ?>&nbsp;</span><font>红娘QQ</font></li>
        <li><i class="ico wxico">&#xe607;</i><span><?php echo $weixin; ?>&nbsp;</span><font>红娘微信</font></li>
        <div class="hnewm"><p><?php echo $ewm_str; ?></p><h6>微信扫码加红娘微信</h6></div><div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<div class="dtl_ubox">
	<h1>这些会员正在委托<?php echo $sex_title; ?></h1>
	<div class="list" id="ulist">
		<?php 
        $rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,job,pay,love,RZ,heigh,photo_ifshow FROM ".__TBL_USER__." WHERE hnid=".$fid." ORDER BY refresh_time DESC LIMIT 100");
        $total = $db->num_rows($rt);
        if ($total > 0) {
            $page_skin='4_yuan';$pagemode=4;$pagesize=20;$page_color='#E83191';require_once ZEAI.'sub/page.php';
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows)break;
                $uid      = $rows['id'];
                $nickname = dataIO($rows['nickname'],'out');
                $sex      = $rows['sex'];
                $love     = $rows['love'];
                $grade    = $rows['grade'];
                $photo_s  = $rows['photo_s'];
                $photo_f  = $rows['photo_f'];
                $areatitle= $rows['areatitle'];
                $birthday = $rows['birthday'];
                $job      = $rows['job'];
                $pay      = $rows['pay'];
                $RZ       = $rows['RZ'];
                $heigh    = $rows['heigh'];
                $photo_ifshow = $rows['photo_ifshow'];
                $nickname = (empty($nickname))?'uid:'.$uid:$nickname;
                //
                $birthday_str = (getage($birthday)<=0)?'':getage($birthday).'岁 ';
                $job_str      = (empty($job))?'':udata('job',$job).' ';
                $pay_str      = (empty($pay))?'':udata('pay',$pay).'/月'.' ';
                $love_str     = (empty($love))?'':udata('love',$love).' ';
                $heigh_str    = ($heigh>140)?$heigh.'cm ':'';
    
                $aARR = explode(' ',$areatitle);
                $areatitle_str = (empty($aARR[1]))?'':$aARR[1];
                $areatitle_str  = str_replace("不限","",$areatitle_str);
                $photo_m_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2']."/".getpath_smb($photo_s,'m'):HOST.'/res/photo_m'.$sex.'.png';
				if($photo_ifshow==0)$photo_m_url=HOST.'/res/photo_m'.$sex.'_hide.png';
				
                $sexbg      = (empty($photo_s) || $photo_f==0)?' class="m sexbg'.$sex.'"':' class="m"';
                $echo .= '<li>';
                $uhref = Href('u',$uid);
                $echo .= '<a href="'.$uhref.'" class="mbox" target="_blank">';
                $echo .= '<p value="'.$photo_m_url.'"'.$sexbg.'></p>';
                $echo .= '<em><span>'.$love_str.'</span><span>'.$job_str.'</span><span>'.$pay_str.'</span><span>'.$areatitle.'</span></em>';
                $echo .= '<b>联系Ta</b>';
                $echo .= '</a>';
                $echo .= '<a href="'.$uhref.'" target="_blank"><h4>'.$nickname.'</h4></a>';
				$echo .= '<h5>'.$birthday_str.$heigh_str.$job_str.$areatitle_str.'</h5>';
                $echo .= '</li>';
            }
            echo $echo;
        }else{
            echo '<br>'.nodatatips('暂时还没有会员找'.$sex_title.'牵线');
        }
        ?>    
    </div>
</div>
<div class="dtl_bbs">
	<h1>服务评价</h1>
    <div  class="dtl_bbs_li">
	<!-- 留言开始 -->
        <div id="hn_bbs_listbox">
            <?php
            $rt=$db->query("SELECT a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,b.* FROM ".__TBL_USER__." a,".__TBL_HN_BBS__." b WHERE a.id=b.uid AND b.hid=".$fid." ORDER BY b.id");
            $total = $db->num_rows($rt);
            if($total>0){
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows) break;
                $sex           = $rows['sex'];
                $grade         = $rows['grade'];
                $nickname      = dataIO($rows['nickname'],'out');
                $photo_s       = $rows['photo_s'];
                $photo_f       = $rows['photo_f'];
                $sexbg        = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
                $photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
                $content       = dataIO($rows['content'],'out');
                $content2      = dataIO($rows['content2'],'out');
                $content2_str  = (!empty($content2))?'<div class="hnhf">红娘回复：'.$content2.'</div>':'';
                $flag          = $rows['flag'];
                $pjkind        = $rows['pjkind'];
                switch ($pjkind){
                    case 1:$pjkind_t = '<span class="pjkind k1">好评</span>';break;
                    case 2:$pjkind_t = '<span class="pjkind k2">中评</span>';break;
                    case 3:$pjkind_t = '<span class="pjkind k3">差评</span>';break;
                }
            ?>
            <table class="table li"><tr><td valign="top" class="liL"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></td>
            <td valign="top" class="liR">
            <table class="table nickname"><tr>
            <td align="left" class="S14"><?php echo $nickname; ?>　评价于<?php echo date_str($rows['addtime']); ?></td>
            <td align="right" class="lou"><span><?php echo $i;?></span>楼</td>
            </tr></table>
            <div class="C">
                <?php
                echo $pjkind_t;
                if ($flag == 1){echo $content.$content2_str;}else{echo'<font>该评价已被冻结或删除！</font>';}
                ?>
            </div>
            </td></tr></table>
            <?php }}else{echo '<br>'.nodatatips('暂无评价');} ?>  
        </div>
        <form id="WwW_Zeai_CN_hnBBS" class="bmform">
        <textarea id="content" name="content" placeholder="我想说两句...　请如实评价~~" class="textarea"></textarea>
        <div class="pjform"><span>服务打分</span>
            <input type="radio" name="kind" id="kind1" class="radioskin" value="1"><label for="kind1" class="radioskin-label"><i class="i2"></i><b>好评</b></label>
            <input type="radio" name="kind" id="kind2" class="radioskin" value="2"><label for="kind2" class="radioskin-label"><i class="i2"></i><b>中评</b></label>
            <input type="radio" name="kind" id="kind3" class="radioskin" value="3"><label for="kind3" class="radioskin-label"><i class="i2"></i><b>差评</b></label>
        </div>
        <input type="button" class="btn size4 HONG2" value=" 发表评价 " id="hn_bbs_btn" />
        <input type="hidden" name="fid" id="fid" value="<?php echo $fid; ?>">
        <input type="hidden" name="i" value="<?php echo $total; ?>">
        <input type="hidden" name="submitok" value="ajax_hnBBS_update">        
        </form>
    </div>
    <!-- 留言结束 QQ797311-->
</div>
<div class="clear"></div>
<script>var supdes,fid=<?php echo $fid;?>;</script>
<script src="<?php echo HOST;?>/p2/js/hongniang_detail.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php require_once ZEAI.'p1/bottom.php';?>
<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
require_once ZEAI.'sub/conn.php';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无红娘</div>";
if($submitok == 'ajax_join'){
	$currfields = "sex,grade,nickname,qq,mob,weixin,truename";
	$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=hongniang&a='.$hid;
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
	$row = $db->ROW(__TBL_CRM_HN__,"truename","id=".$hid,"num");
	if ($row){
		$hn_truename= dataIO($row[0],'out');
	}else{exit(JSON_ERROR);}

	$mini_title = '<i class="ico goback" id="ZEAIGOBACK-hongniang_join">&#xe602;</i>委托【'.$hn_truename.'】牵线';
	$mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
	require_once ZEAI.'m1/top_mini.php';?>
	<div class="submain hn_join">
	
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
        <input type="hidden" name="hid" value="<?php echo $hid;?>" />
        <input type="hidden" name="uuid" id="uuid" value="" />
        <input name="submitok" type="hidden" value="ajax_join_update" />
        </form>
        <button type="button" id="hn_join_btn" class="btn size3 ">提交申请</button>
        <br><div class="linebox" style="z-index:0"><div class="line BAI W50"></div><div class="title S12 BAI">温馨提醒</div></div>
        <div>以上信息受隐私保护，仅用于红娘联系通知，不对外公开。</div>
		<script>hn_join_btn.onclick=function(){hn_join_btnFn();}
			function hn_join_btnFn(){
				//if(zeai.ifint(localStorage.uid))uuid.value=localStorage.uid;
				if(zeai.ifint(sessionStorage.uid))uuid.value=sessionStorage.uid;
				
				
				ZeaiM.confirmUp({title:'确定信息无误提交申请么？',cancel:'取消',ok:'确定',okfn:function(){
				zeai.ajax({url:HOST+'/m1/hongniang'+zeai.extname,form:WwW_Zeai_CN_hnJoin},function(e){rs=zeai.jsoneval(e);
					zeai.msg(rs.msg,{time:5});
					if(rs.flag==1){
						var listC=hn_userbox.innerHTML;
						if (listC.indexOf('nodatatips') == -1){
							hn_userbox.insertAdjacentHTML('afterbegin',html_decode(rs.C));
						}else{
							hn_userbox.html(html_decode(rs.C));
						}
						hn_unum.html(parseInt(hn_unum.innerHTML)+1);
						setTimeout(function(){o('ZEAIGOBACK-hongniang_join').click();},200);
					}
				});}});	
			}
        </script>
	</div>
<?php exit;}elseif($submitok == 'ajax_join_update'){
	
	if(!ifint($hid))json_exit(array('flag'=>0,'msg'=>'红娘ID错误'));
	if(!ifmob($mob))json_exit(array('flag'=>0,'msg'=>'请留下正确的手机号码，方便给您服务哦'));
	if ($db->ROW(__TBL_USER__,"id","id<>".$cook_uid." AND (   (mob='$mob' AND FIND_IN_SET('mob',RZ))   OR  (qq<>'' AND qq='$qq' AND FIND_IN_SET('qq',RZ))  )   "))json_exit(array('flag'=>0,'msg'=>'此手机号码或QQ已被其他会员占用，请更换'));

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
		//回调会员列表
		$uid      = $cook_uid;
		$nickname = dataIO($row2['nickname'],'out');
		$sex      = $row2['sex'];
		$photo_s  = $row2['photo_s'];
		$photo_f  = $row2['photo_f'];
		$birthday = $row2['birthday'];
		$birthday_str= (getage($birthday)<=0)?'':' '.getage($birthday).'岁';
		$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
		$img_str    = '<img src="'.$photo_s_url.'" class="sexbg'.$sex.'">';
		$CC = '<li><a onClick="hn_userAfn('.$uid.')">'.$img_str.'<h5>'.$nickname.$birthday_str.'</h5></a></li>';
		$CC = dataIO($CC,'in');
	}
	//通知红娘
	$row = $db->NUM($hn_uid,"nickname,openid,subscribe");
	if ($row){
		$hn_nickname = dataIO($row[0],'out');
		$hn_openid   = $row[1];
		$hn_subscribe= $row[2];
		//站内tips发给红娘
		$sex_title = ($cook_sex == 1)?'他':'她';
		$T = '【'.$cook_nickname.' uid:'.$cook_uid.'】想委托您为'.$sex_title.'服务';
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
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$hn_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url=');
			}
		}
	}
	json_exit(array('flag'=>1,'msg'=>'申请已发送，请等待【'.$hn_truename.'】接洽～','C'=>$CC));
/************************ BBS ***********************/
exit;}elseif($submitok == 'ajax_hnBBS_update'){
	if(!ifint($hid))json_exit(array('flag'=>0,'msg'=>'红娘ID错误'));
	if ($cook_content == $content && !empty($content))json_exit(array('flag'=>0,'msg'=>'请不要重复发布'));
	
	$currfields = "sex,grade,nickname,qq,mob,weixin,truename";
	$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=hongniang&a='.$hid;
	require_once ZEAI.'my_chk_u.php';
	//
	if ( (str_len($content)>10000 || str_len($content)<1) )json_exit(array('flag'=>0,'msg'=>'评价内容请控制在1~1000字节以内'));
	if ($kind != 1 && $kind != 2 && $kind != 3)json_exit(array('flag'=>0,'msg'=>'请打分【好评－中评－差评】'));
	switch ($kind) {
		case 1:$sql  = " ,pj_good=pj_good+1 ";break;
		case 2:$sql  = " ,pj_normal=pj_normal+1 ";break;
		case 3:$sql  = " ,pj_bad=pj_bad+1 ";break;
	}
	$row = $db->ROW(__TBL_HN_BBS__,"id","uid=".$cook_uid." AND hid=".$hid);
	if ($row)json_exit(array('flag'=>0,'msg'=>'您已经评价过了～请不要重复评价'));
	
	$row = $db->ROW(__TBL_USER__,"nickname,sex,photo_s,photo_f","id=".$cook_uid." AND hnid=".$hid);
	if ($row){
		$content = dataIO($content,'in',1000);
		$db->query("INSERT INTO ".__TBL_HN_BBS__." (uid,hid,content,addtime,pjkind) VALUES ($cook_uid,'$hid','$content',".ADDTIME.",$kind)");
		$db->query("UPDATE ".__TBL_CRM_HN__." SET bbsnum=bbsnum+1 ".$sql." WHERE id=".$hid);
		setcookie("cook_content",$content,time()+720000,"/",$_ZEAI['CookDomain']);
		
		//回调会员列表
		$uid           = $cook_uid;
		$sex           = $row['sex'];
		$grade         = $row['grade'];
		$nickname      = dataIO($row['nickname'],'out');
		$photo_s       = $row['photo_s'];
		$photo_f       = $row['photo_f'];
		$sexbg        = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
		$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
		$content       = dataIO($content,'out');
		$flag          = 1;
		$pjkind        = $kind;
		switch ($pjkind){
			case 1:$pjkind_t = '<span class="pjkind k1">好评</span>';break;
			case 2:$pjkind_t = '<span class="pjkind k2">中评</span>';break;
			case 3:$pjkind_t = '<span class="pjkind k3">差评</span>';break;
		}
		$i = (!ifint($i))?1:$i;
		$echo  = '<table class="table li"><tr><td valign="top" class="liL"><img src="'.$photo_s_url.'"'.$sexbg.'></td>';
		$echo .= '<td valign="top" class="liR">';
		$echo .= '<table class="table nickname"><tr>';
		$echo .= '<td align="left" class="S12">'.$nickname.'　评价于'.date_str(ADDTIME).'</td>';
		$echo .= '<td align="right" class="lou"><span>'.($i+1).'</span>楼</td>';
		$echo .= '</tr></table>';
		$echo .= '<div class="C">';
		$echo .= $pjkind_t;
		if ($flag == 1){$echo .= $content.$content2_str;}else{$echo .='<font>该评价已被冻结或删除！</font>';}
		$echo .= '</div>';
		$echo .= '</td></tr></table>';
		$CC = dataIO($echo,'in');
		json_exit(array('flag'=>1,'msg'=>'评价成功','C'=>$CC,'pjkind'=>$pjkind));
	}else{
		json_exit(array('flag'=>0,'msg'=>'您没有找Ta牵线，无法评价哦'));
	}
}

$BK=(ifint($a))?'hongniang':'hongniang_detail';
$id = (ifint($id))?$id:$a;
if(ifint($id)){
	$row = $db->ROW(__TBL_CRM_HN__,"qq,weixin,mob,path_s,ewm,truename,sex,aboutus,title,pj_good,pj_normal,pj_bad,click","flag=1 AND id=".$id,"name");
	if ($row){
		$truename = dataIO($row['truename'],'out',7);
		$aboutus  = dataIO($row['aboutus'],'out');
		$title    = dataIO($row['title'],'out');
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
		$path_s_url = (!empty($path_s))?$_ZEAI['up2'].'/'.$path_s:HOST.'/res/photo_m'.$sex.'.png';
		$ewm_url    = (!empty($ewm))?$_ZEAI['up2'].'/'.$ewm:HOST.'/res/photo_m'.$sex.'.png';
		$sexbg   = (empty($path_s))?' class="sexbg'.$sex.'"':'';
		$join_title = '～ 找'.$sex_title.'牵线 ～';	

		$db->query("UPDATE ".__TBL_CRM_HN__." SET click=click+1 WHERE id=".$id);
		$unum = $db->COUNT(__TBL_USER__,"hnid=".$id);
		if (ifint($cook_uid)){
			$row = $db->ROW(__TBL_USER__,"crm_flag","id=".$cook_uid." AND hnid=".$id);
			if ($row){
				$joinclass  = ' class="addmeed"';	
				switch ($row[0]) {
					case 1:$join_title  = '正在服务中';break;
					case 2:$join_title  = '服务跟进中';break;
					case 3:$join_title  = '服务成功';break;
				}
			}
		}
	}else{echo $nodatatips;}
	$hid=$id;
	?>
    <link href="<?php echo HOST;?>/m1/css/hongniang.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
    <?php
	$mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$BK.'">&#xe602;</i>';
	$mini_class = 'top_mini top_miniHN';
	$mini_backT = '返回';
	require_once ZEAI.'m1/top_mini.php';?>
	<div class="submain hn">
        <div class="info">
            <p><a onclick="ZeaiM.piczoom('<?php echo smb($path_s_url,'b');?>')"><img src="<?php echo $path_s_url; ?>"<?php echo $sexbg; ?>></a></p>
            <em>
                <h2><?php echo $truename; ?></h2>
                <span class="title"><?php echo $title; ?></span>
                <span class="click">人气：<?php echo $click; ?></span>
                <div class="pj">
                    <a href="#content">会员<font id="hn_unum"><?php echo $unum; ?></font>个</a>
                    <a href="#content">好评<font id="hn_pjkind1"><?php echo $pj_good; ?></font></a>
                    <a href="#content">中评<font id="hn_pjkind2"><?php echo $pj_normal; ?></font></a>
                    <a href="#content">差评<font id="hn_pjkind3"><?php echo $pj_bad; ?></font></a>
                </div>                
            </em><div class="clear"></div>
        </div>
        <div class="hnline"><img src="<?php echo HOST;?>/m1/img/hn_5.png"></div>
        <div class="aboutus">
            <h4><?php echo $aboutus; ?></h4>
            <a id="joinhn"<?php echo $joinclass; ?>><?php echo $join_title; ?></a>
        </div>
        
        <script>
        joinhn.onclick=function(){
			page({g:HOST+'/m1/hongniang'+zeai.ajxext+'submitok=ajax_join&hid=<?php echo $hid;?>',y:'<?php echo $BK;?>',l:'hongniang_join'});
		}
        function hn_userAfn(uid){page({g:HOST+'/m1/u'+zeai.ajxext+'uid='+uid,y:'<?php echo $BK;?>',l:'u'});}
        </script>
		<?php if (is_weixin()){//分享?>
        <script>
            var share_hn_detail_title = '<?php echo strip_tags(TrimEnter($truename)); ?>';
            var share_hn_detail_desc  = '<?php echo strip_tags(TrimEnter(dataIO($title,'out',50))); ?>';
            var share_hn_detail_link  = '<?php echo HOST; ?>/?z=index&e=hongniang&a=<?php echo $hid; ?>';
            var share_hn_detail_imgurl= '<?php echo $path_s_url; ?>';
            wx.ready(function () {
                wx.onMenuShareAppMessage({title:share_hn_detail_title,desc:share_hn_detail_desc,link:share_hn_detail_link,imgUrl:share_hn_detail_imgurl});
                wx.onMenuShareTimeline({title:share_hn_detail_title,link:share_hn_detail_link,imgUrl:share_hn_detail_imgurl});
            });
            </script>   
        <?php }?>
        <div class="mainn detaicotact">
            <h3>联系红娘</h3>
            <?php if (!empty($mob)){ ?><li><p class="ico telico">&#xe60e;</p>红娘热线 <span><?php echo $mob; ?></span><a href="tel:<?php echo $mob; ?>">拔打</a></li><?php }?>
            <?php if (!empty($qq)){ ?><li><p class="ico qqico">&#xe612;</p>红娘QQ <span><?php echo $qq; ?></span></li><?php }?>
            <?php if (!empty($weixin)){ ?><li><p class="ico wxico">&#xe607;</p>红娘微信 <span><?php echo $weixin; ?></span></li><?php }?>
            <?php if (!empty($ewm)){ ?>
            <div class="hnewm"><img src="<?php echo $ewm_url; ?>"><h6>长按识别二维码加红娘微信</h6></div>
            <?php }?>
        </div>
        
        <!-- 留言开始 -->
        <div class="mainn detaibbs">
            <h3>服务评价</h3>
            <div id="hn_bbs_listbox">
				<?php
                $rt=$db->query("SELECT a.nickname,a.sex,a.grade,a.photo_s,a.photo_f,b.* FROM ".__TBL_USER__." a,".__TBL_HN_BBS__." b WHERE a.id=b.uid AND b.hid=".$hid." ORDER BY b.id");
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
                <td align="left" class="S12"><?php echo $nickname; ?>　评价于<?php echo date_str($rows['addtime']); ?></td>
                <td align="right" class="lou"><span><?php echo $i;?></span>楼</td>
                </tr></table>
                <div class="C">
                    <?php
                    echo $pjkind_t;
                    if ($flag == 1){echo $content.$content2_str;}else{echo'<font>该评价已被冻结或删除！</font>';}
                    ?>
                </div>
                </td></tr></table>
                
                <?php }}else{ ?><div class='nodatatips W150'><i class='sorry50'></i><br>暂无评价</div><?php } ?>  
			</div>
            <form id="WwW_Zeai_CN_hnBBS" class="bmform">
            <textarea id="content" name="content" placeholder="我想说两句...请如实评价~~"></textarea>
            <div class="pjform">服务打分：
                <input type="radio" name="kind" id="kind1" class="radioskin" value="1"><label for="kind1" class="radioskin-label"><i class="i1"></i><b>好评</b></label>
                <input type="radio" name="kind" id="kind2" class="radioskin" value="2"><label for="kind2" class="radioskin-label"><i class="i1"></i><b>中评</b></label>
                <input type="radio" name="kind" id="kind3" class="radioskin" value="3"><label for="kind3" class="radioskin-label"><i class="i1"></i><b>差评</b></label>
            </div>
            <input type="button" class="btn2FEN" value=" 发表评价 " id="hn_bbs_btn" />
            <input type="hidden" name="hid" id="hid" value="<?php echo $hid; ?>">
            <input type="hidden" name="i" value="<?php echo $total; ?>">
            <input type="hidden" name="submitok" value="ajax_hnBBS_update">        
            </form>
            <script>
			hn_bbs_btn.onclick=function(){
				ZeaiM.confirmUp({title:'您只有一次评价机会～确定么？',cancel:'取消',ok:'确定',okfn:function(){
					if(zeai.str_len(content.value)<1 || zeai.str_len(content.value)>1000){
						zeai.msg('评价内容请控制在1~1000字节！',content);
						return false;
					}
					if (!zeai.form.ifradio('kind')){zeai.msg('请给红娘服务打分【好评－中评－差评】');return false;}
					zeai.ajax({url:HOST+'/m1/hongniang'+zeai.extname,form:WwW_Zeai_CN_hnBBS},function(e){rs=zeai.jsoneval(e);
						zeai.msg(rs.msg);
						if(rs.flag==1){
							console.log(rs.pjkind);
							var pjkindObj=o('hn_pjkind'+rs.pjkind);
							pjkindObj.html(parseInt(pjkindObj.innerHTML)+1);
							var listbbsC=hn_bbs_listbox.innerHTML;
							if (listbbsC.indexOf('nodatatips') == -1){
								hn_bbs_listbox.insertAdjacentHTML('afterbegin',html_decode(rs.C));
							}else{
								hn_bbs_listbox.html(html_decode(rs.C));
							}
							content.value='';
						}
				});}});	
				
			}
            </script>
        </div>
		<!-- 留言结束 -->
        
        <div class="mainn detaiuser">
            <h3>这些会员正在委托<?php echo $truename; ?></h3>
            <ul id="hn_userbox">
                <?php
                $rt=$db->query("SELECT id,nickname,sex,photo_s,photo_f,birthday FROM ".__TBL_USER__." WHERE hnid=".$hid." ORDER BY refresh_time DESC LIMIT 100");
                $total = $db->num_rows($rt);
                if ($total > 0) {
                    for($i=1;$i<=$total;$i++) {
                    $rows = $db->fetch_array($rt,'name');
                    if(!$rows) break;
                    $uid      = $rows['id'];
                    $nickname = dataIO($rows['nickname'],'out');
                    $sex      = $rows['sex'];
                    $photo_s  = $rows['photo_s'];
                    $photo_f  = $rows['photo_f'];
                    $birthday = $rows['birthday'];
                    $birthday_str = (getage($birthday)<=0)?'':' '.getage($birthday).'岁';
                    $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
                    $sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
                    $img_str     = '<img src="'.$photo_s_url.'" class="sexbg'.$sex.'">';
                    ?>
                    <li><a onClick="hn_userAfn(<?php echo $uid;?>)"><?php echo $img_str; ?><h5><?php echo $nickname.$birthday_str; ?></h5></a></li>
                <?php }}else{echo "<div class='nodatatips W150'><i class='sorry50'></i><br>暂时还没有人找".$sex_title."牵线</div>";}?>
            </ul>
        </div>        
        
        
	</div>
	<?php
	exit;
}
?>
<link href="<?php echo HOST;?>/m1/css/hongniang.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<i class="ico goback Ugoback" id="ZEAIGOBACK-hongniang">&#xe602;</i>
<div class="submain hongniang huadong">
    <div class="hnbnr"><img src="<?php echo HOST;?>/m1/img/hn_1.jpg?<?php echo $_ZEAI['cache_str'];?>"></div>
    <div class="bn"><img src="<?php echo HOST;?>/m1/img/hn_2.gif?<?php echo $_ZEAI['cache_str'];?>"><img src="<?php echo HOST;?>/m1/img/hn_3.png"></div>
    <div class="bnt"><img src="<?php echo HOST;?>/m1/img/hn_4.png?<?php echo $_ZEAI['cache_str'];?>"></div>
    
    <div id="list" class="hnlist">
        <?php 
        $rt=$db->query("SELECT id,sex,truename,path_s,title,pj_good,pj_bad FROM ".__TBL_CRM_HN__." WHERE ifwebshow=1 AND flag=1 AND path_s<>'' ORDER BY px DESC");
        $total = $db->num_rows($rt);
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows)break;
                $id       = $rows['id'];
                $sex      = $rows['sex'];
                $truename = dataIO($rows['truename'],'out',7);
                $path_s   = $rows['path_s'];
                $title    = dataIO($rows['title'],'out');
                $pj_good  = intval($rows['pj_good']);
                $pj_bad   = intval($rows['pj_bad']);
                $pjbfb    = 100;
                if ($pj_good>0){
                    $pj_ = $pj_good+$pj_bad;
                    $pj_ = $pj_good/$pj_;
                    $pjbfb = round($pj_,2)*100;
                }
                //$href     = 'detail.php?hid='.$hid;
                $path_s_url = (!empty($path_s))?$_ZEAI['up2'].'/'.getpath_smb($path_s,'b'):HOST.'/res/photo_m'.$sex.'.png';
                $sexbg   = (empty($path_s))?' class="sexbg'.$sex.'"':'';
                ?>
                <li onClick="dtlhnFn(<?php echo $id;?>)"><img src="<?php echo $path_s_url; ?>"<?php echo $sexbg; ?>>
                    <h2><?php echo $truename; ?></h2>
                    <h3><span>好评:<?php echo $pjbfb; ?>%</span></h3>
                    <h4><?php echo $title; ?></h4>
                    <a class="add"><i class="ico">&#xe620;</i>联系红娘</a>
                </li>
        <?php }}else{echo $nodatatips;}?>
    </div>
	<?php if (is_weixin()){//分享?>
    <script>
        var share_hongniang_title = '<?php echo '红娘线下人工服务_'.$_ZEAI['siteName']; ?>';
        var share_hongniang_desc  = '帮您找对象·教您谈恋爱，专业红娘一对一服务！';
        var share_hongniang_link  = '<?php echo HOST; ?>/?z=index&e=hongniang';
        var share_hongniang_imgurl= '<?php echo HOST; ?>/m1/img/share_hn.gif';
        wx.ready(function () {
            wx.onMenuShareAppMessage({title:share_hongniang_title,desc:share_hongniang_desc,link:share_hongniang_link,imgUrl:share_hongniang_imgurl});
            wx.onMenuShareTimeline({title:share_hongniang_title,link:share_hongniang_link,imgUrl:share_hongniang_imgurl});
        });
        </script>   
    <?php }?>
    <script>function dtlhnFn(id){page({g:HOST+'/m1/hongniang'+zeai.ajxext+'id='+id,y:'hongniang',l:'hongniang_detail'});}</script>
</div>
<?php
function get_list($kindid) {
	global $db,$_ZEAI,$nodatatips;
	$SQL = (ifint($kindid))?" AND kind=".$kindid:'';
	$rt2=$db->query("SELECT id,title,kindtitle,path_s,addtime FROM ".__TBL_NEWS__." WHERE flag=1 AND id>2 AND path_s<>'' ".$SQL." ORDER BY px DESC,id DESC LIMIT 100");
	$total2 = $db->num_rows($rt2);
	if ($total2 > 0) {
		for($j=0;$j<$total2;$j++) {
			$rows2 = $db->fetch_array($rt2,'name');
			if(!$rows2) break;
			$id   = $rows2['id'];
			$title=dataIO($rows2['title'],'out');
			$path_s    = $rows2['path_s'];
			$addtime   = YmdHis($rows2['addtime'],'Ymd');
			$kindtitle = dataIO($rows2['kindtitle'],'out');
			$path_s_url=$_ZEAI['up2'].'/'.$path_s;
			$echo .= '<li class="fadeInL" onClick="dtlwzFn('.$id.')">';
			$echo .= '<img src="'.$path_s_url.'">';
			$echo .= '<em>';
			$echo .= '<h4>'.$title.'</h4>';
			$echo .= '<span>'.$kindtitle.'</span><font>'.$addtime.'</font>';
			$echo .= '</em>';
			$echo .= '</li>';
		}
		return $echo;
	}else{
		return $nodatatips;
	}
}
?>
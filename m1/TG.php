<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
$currfields = "nickname,tgpic,photo_s,photo_f,money,tgallloveb,tgallmoney,tipnum";
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=TG';require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_reg.php';$tg=json_decode($_REG['tg'],true);$reward=$tg['reward'];
if($reward['kind']==1){//loveb
	$priceT = $_ZEAI['loveB'];
}elseif($reward['kind']==2){//元
	$priceT = '元';
}
$data_tgpic = $row['tgpic'];
$data_tgallloveb = $row['tgallloveb'];
$data_tgallmoney = $row['tgallmoney'];
$data_money      = $row['money'];
$data_nickname   = dataIO($row['nickname'],'out');
//
if($submitok == 'TG_TD_list1' || $submitok == 'TG_TD_list2'){
	$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe61f;</i>～～你还没有发展下线～～<a id=\"TG_Uabtn\" class=\"btn size4 yuan TG_Uabtn\" onclick=\"TG_TDabtnFn();\">立即去招募</a><div>通过我分享的分享二维码加入即可成为我的团队成员，发展的我团队，让收益迅速暴增</div></div>";
	if($submitok == 'TG_TD_list2'){
		$dlcls=' class="fadeInR"';
		$rt=$db->query("SELECT id,uname,nickname,sex,grade,photo_s,photo_f,regtime,tgallloveb,tgallmoney FROM ".__TBL_USER__." WHERE tguid IN (SELECT id FROM zeai_user WHERE tguid=".$cook_uid.") ORDER BY id DESC");
	}else{
		$rt=$db->query("SELECT id,uname,nickname,sex,grade,photo_s,photo_f,regtime,tgallloveb,tgallmoney FROM ".__TBL_USER__." WHERE tguid=".$cook_uid." ORDER BY id DESC");
		$dlcls=' class="fadeInL"';
	}
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows)break;
			$uid     = $rows['id'];
			$uname    = dataIO($rows['uname'],'out');
			$nickname = dataIO($rows['nickname'],'out');
			$sex      = $rows['sex'];
			$grade    = $rows['grade'];
			$photo_s  = $rows['photo_s'];
			$photo_f  = $rows['photo_f'];
			$tgallloveb = $rows['tgallloveb'];
			$tgallmoney = $rows['tgallmoney'];
			$regtime  = YmdHis($rows['regtime']);
			//
			$nickname = (empty($nickname))?$uname:$nickname;
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
			$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			if($reward['kind']==1){//loveb
				$price = $tgallloveb;
			}elseif($reward['kind']==2){//元
				$price = $tgallmoney;
			}
			$clsname = ($price > 0)?' ed':'';		
			?>
			<dl<?php echo $dlcls;?>>
				<dt onClick="TGuA(<?php echo $uid; ?>,'TG_TD')"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></dt>
				<dd><h4><?php echo uicon($sex.$grade);?><span><?php echo $nickname; ?></span></h4><h6><?php echo $regtime; ?></h6></dd>
				<div class="time">已奖励</div>
				<div class="price<?php echo $clsname;?>"><?php echo $price; ?><span><?php echo $priceT;?></span></div>
			</dl>
	<?php }}else{echo $nodatatips;}
exit;}elseif($submitok == 'TG_U'){
	$nodatatips = "<div class='nodatatips' style=\"margin-top:20px\"><i class='ico'>&#xe61f;</i>～～你还没有单身团成员～～<a id=\"TG_Uabtn\" class=\"btn size4 yuan TG_Uabtn\" onclick=\"TG_UabtnFn();\">立即去招募</a><br>通过我分享的分享二维码加入到我的单身团注册会员即可获得奖励</div>";
	$mini_backT = '';
	$mini_title = '　我的单身团（<font id="TG_UnumTop"></font>人）';
	$mini_class = 'top_mini top_miniTG';
	require_once ZEAI.'m1/top_mini.php';?>
	<i class='ico goback' id='ZEAIGOBACK-TG_U'>&#xe602;</i>
	<div class="submain TG_U">
		<?php
        $rt=$db->query("SELECT id,uname,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,heigh,regtime,tgflag FROM ".__TBL_USER__." WHERE tguid=".$cook_uid." ORDER BY id DESC");
        $total = $db->num_rows($rt);
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows)break;
                $uid     = $rows['id'];
                $uname    = dataIO($rows['uname'],'out');
                $nickname = dataIO($rows['nickname'],'out');
                $sex      = $rows['sex'];
                $grade    = $rows['grade'];
                $photo_s  = $rows['photo_s'];
                $photo_f  = $rows['photo_f'];
                $areatitle= $rows['areatitle'];
                $birthday = $rows['birthday'];
                $heigh    = $rows['heigh'];
                $tgflag   = $rows['tgflag'];
                $regtime  = date_str($rows['regtime']);
                //
				$nickname = (empty($nickname))?$uname:$nickname;
                $birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁　';
                $heigh_str     = (empty($heigh))?'':$heigh.'cm　';
                $aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
                $areatitle_str = (empty($areatitle))?'':$areatitle;
                $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
                $sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
            	$tgflag_str  = ($tgflag == 1)?'<span class="ed">已奖励</span>':'<span>待验证</span>';
        ?>
        <dl>
            <dt onClick="TGuA(<?php echo $uid; ?>,'TG_U')"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></dt>
            <dd><h4><?php echo uicon($sex.$grade);?><span><?php echo $nickname; ?></span></h4><h6><?php echo $birthday_str.$heigh_str; ?><?php echo $areatitle_str; ?></h6></dd>
            <div class="time"><?php echo $regtime; ?></div>
            <div class="tgflag"><?php echo $tgflag_str; ?></div>
        </dl>
        <?php }}else{echo $nodatatips;}?>
		<script>
		setTimeout(function(){TG_UnumTop.html(<?php echo $total;?>)},50);
        </script>
	</div>
	<?php
exit;}elseif($submitok == 'TG_TD'){
	$mini_title = '我的人脉';
	$mini_class = 'top_mini top_miniTG';
	require_once ZEAI.'m1/top_mini.php';
	?>
	<i class='ico goback' id='ZEAIGOBACK-TG_TD'>&#xe602;</i>
	<div class="submain TG_TD">
        <div class="topbg">
            <ul>
                <li><dt><b id="TG_NUM1"></b><?php echo $priceT;?></dt><dd>来自团队收益</dd></li>
                <li><dt><b id="TG_NUM2"></b>人</dt><dd>我的粉丝</dd></li>
                <li><dt><b id="TG_NUM3"></b>人</dt><dd>我的团队</dd></li>
            </ul>
        </div>
		<div class="tabmenu tabmenu_2" id="tabmenuTG_TD">
			<li data='m1/TG.php?submitok=TG_TD_list1' id="TG_TD_btn1" class="ed"><span>我的粉丝</span></li>
			<li data='m1/TG.php?submitok=TG_TD_list2' id="TG_TD_btn2"><span>我的团队</span></li>
			<i></i>
		</div>
        <div class="ubox" id="TG_TD_ubox"></div>
		<script>
		ZeaiM.tabmenu.init({showbox:TG_TD_ubox,obj:tabmenuTG_TD});
		//TG_TD_btn1.click();
		setTimeout(function(){TG_TD_btn1.click();console.log(111111);},100);
		TD_count();
        </script>
	</div>
	<?php
exit;}elseif($submitok == 'TG_MSG'){
	$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
	$mini_backT = '返回';
	$mini_title = "<i class='ico goback' id='ZEAIGOBACK-TG_MSG'>&#xe602;</i>系统通知";
	$mini_class = 'top_mini top_miniBAI';$mini_R = '<a id="TG_MSG_btndel">清空</a>';
	require_once ZEAI.'m1/top_mini.php';
	?>
	<div class="submain TG_MSG" id="TG_MSGbox">
        <?php 
        $_ZEAI['pagesize'] = 100;
        $rt=$db->query("SELECT id,title,new,addtime FROM ".__TBL_TIP__." WHERE uid=".$cook_uid." AND kind=1 AND ( title LIKE '%推荐%' ) ORDER BY id DESC LIMIT ".$_ZEAI['pagesize']);//推荐
        $total = $db->num_rows($rt);
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'num');
                if(!$rows)break;
                $id    = $rows[0];
                $title = dataIO($rows[1],'out');
                $new   = $rows[2];
                $addtime_str = date_str($rows[3]);
                $new_str = ($new == 1)?'<b></b>':'';
    
                $img_str = '<i class="ico k1">&#xe657;</i>';
                $kind_str = '';
                $content = dataIO($rows[2],'out');
        ?>
        <dl>
            <dt tid="<?php echo $id; ?>"><?php echo $img_str; ?><?php echo $new_str; ?></dt>
            <dd><h4><?php echo $title; ?></h4></dd>
            <span><?php echo $addtime_str; ?></span>
            <strong>删除</strong>
        </dl>
        <?php }}else{echo $nodatatips;}?>
        <script>
		TG_MSGFn();
		TG_MSG_btndel.onclick=function(){
			ZeaiM.confirmUp({title:'确定清空全部系统通知么？',cancel:'取消',ok:'确定清空',okfn:function(){
				zeai.ajax({url:'m1/TG'+zeai.extname,data:{submitok:'ajax_MSG_clean'}},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1){TG_MSGbox.html("<?php echo $nodatatips;?>");}
				});
			}});
		}
        </script>
	</div>
	<?php
exit;}elseif($submitok == 'ajax_MSG_del'){
	if(!ifint($tid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TIP__,"id","new=1 AND uid=".$cook_uid." AND id=".$tid);
	if ($row){
		$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
	}
	$db->query("DELETE FROM ".__TBL_TIP__." WHERE uid=".$cook_uid." AND id=".$tid);
exit;}elseif($submitok == 'ajax_MSG_clean'){
	$total = $db->COUNT(__TBL_TIP__,"( title LIKE '%推荐%' ) AND new=1 AND uid=".$cook_uid);
	if($total>0){
		$data_tipnum = $row['tipnum'];
		$endnum = ($data_tipnum >= $total)?($data_tipnum - $total):0;
		$db->query("UPDATE ".__TBL_USER__." SET tipnum=$endnum id=".$cook_uid);
	}
	$db->query("DELETE FROM ".__TBL_TIP__." WHERE ( title LIKE '%推荐%' ) AND uid=".$cook_uid);
	json_exit(array('flag'=>1));
exit;}elseif($submitok == 'TG_MSG_detail'){
	if(!ifint($tid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TIP__,"content,new,addtime","uid=".$cook_uid." AND id=".$tid);
	if ($row){
		$content = dataIO($row[0],'out');
		$new     = $row[1];
		$addtime = YmdHis($row[2]);
		if ($new == 1){
			$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE id=".$tid." AND uid=".$cook_uid);
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
		}
	}else{exit(JSON_ERROR);}
	//	
	$mini_backT = '返回';
	$mini_title = "<i class='ico goback' id='ZEAIGOBACK-TG_MSG_detail'>&#xe602;</i>";
	$mini_class = 'top_mini top_miniBAI';
	require_once ZEAI.'m1/top_mini.php';
	?>
	<div class="submain TG_MSG_detail">
		<div id="TG_MSG_msgC"><?php echo $content;?></div>
        <div class="linebox"><div class="line "></div><div class="title BAI S14 C999"><?php echo $addtime; ?></div></div>
	</div>
	<script>zeai.listEach(zeai.tag(TG_MSG_msgC,'a'),function(a){a.remove();});</script>
	<?php
exit;}elseif($submitok == 'ajax_TD_count1'){
	if($reward['kind']==1){//loveb
		$filed     = 'tgallloveb';
		$XX['num1'] = $data_tgallloveb;
	}elseif($reward['kind']==2){//元
		$filed     = 'tgallmoney';
		$XX['num1'] = $data_tgallmoney;
	}
	
	$XX['num2'] = $db->COUNT(__TBL_USER__,"tguid=".$cook_uid);
	$rt=$db->query("SELECT COUNT(*) AS num FROM ".__TBL_USER__." WHERE tguid IN (SELECT id FROM zeai_user WHERE tguid=".$cook_uid.") ");
	$row = $db->fetch_array($rt,'name');
	$XX['num3'] = intval($row['num']);
	//$XX['num1'] = $XX['num2'] + $XX['num3'];
	json_exit(array('XX'=>$XX));
/*
exit;}elseif($submitok == 'ajax_TD_count2'){
	if($reward['kind']==1){//loveb
		$filed     = 'tgallloveb';
		$SY['num1'] = $data_tgallloveb;
	}elseif($reward['kind']==2){//元
		$filed     = 'tgallmoney';
		$SY['num1'] = $data_tgallmoney;
	}
	
	$rt=$db->query("SELECT SUM($filed) AS allnum FROM ".__TBL_USER__." WHERE tguid=".$cook_uid);
	$row=$db->fetch_array($rt,'name');
	$SY['num2']=intval($row['allnum']);
	
	$rt=$db->query("SELECT SUM($filed) AS allnum FROM ".__TBL_USER__." WHERE tguid IN (SELECT id FROM zeai_user WHERE tguid=".$cook_uid.") ");
	$row=$db->fetch_array($rt,'name');
	$SY['num3']=intval($row['allnum']);
	
	json_exit(array('SY'=>$SY));
*/	
exit;}elseif($submitok == 'ajax_TG_U_num'){
	json_exit(array('num'=>$db->COUNT(__TBL_USER__,"tguid=".$cook_uid)));
exit;}elseif($submitok == 'ewm'){
	$photo_s = $row['photo_s'];
	$photo_f = $row['photo_f'];
	if (empty($data_tgpic)){
		$browser= (is_weixin())?'wx':'h5';
		$token =($browser=='wx')?wx_get_access_token():'';
		$ret   = make_tg_ewm($token);
		$msg   = $ret['msg'];
		$tgpic = $ret['tgpic'];
		if (!empty($tgpic)){
			$tgrow = $db->NUM($cook_uid,"tgpic");
			$data_tgpic = $tgrow[0];
		}else{
			json_exit(array('flag'=>0,'msg'=>$msg));
		}
		?>
        <script>zeai.msg(0);</script>
        <?php
	}
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-TG_ewm">&#xe602;</i>我的分享海报';
    $mini_class = 'top_mini top_miniBAI';
	$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';
	?>
	<div class="submain TG_ewm">
        <img src="<?php echo $_ZEAI['up2'].'/'.$data_tgpic;?>" class="tgpic" onClick="tgpicdown()">
		<input type="button" value="下载我的分享二维码" class="btn size4 LV2 tgpicdown" onClick="tgpicdown()">
        <div class="linebox" style="z-index:0"><div class="line BAI W50"></div><div class="title S14 BAI">分享方法</div></div>
		<div class="sbmtips">
            ● 点击<font style="color:#45C01A">“下载我的分享二维码”</font>，弹出您的专属代言海报，长按图片选择“发送给朋友”或是“保存图片”<br>
            ● 让朋友微信扫一扫或者长按图片识别二维码<br>
            ● 点击右上角"…"去分享朋友圈或发送给朋友<br>
        </div>
        <div class="linebox" style="z-index:0"><div class="line BAI W50"></div><div class="title S14 BAI">都有哪些收益</div></div>
        <ul class="sy">
			<?php
            $reward=$tg['reward'];
            if($reward['kind']==1){//loveb
                $price1  = $reward['kind1num1'];
                $price2  = $reward['kind1num2'];
                $priceT = $_ZEAI['loveB'];
            }elseif($reward['kind']==2){//元
                $price1  = $reward['kind2num1'];
                $price2  = $reward['kind2num2'];
                $priceT = '元';
            }
            $czbfb1  = intval($reward['cz_tcnum1']*100);
            $czbfb2  = intval($reward['cz_tcnum2']*100);
            $vipbfb1 = intval($reward['vip_tcnum1']*100);
            $vipbfb2 = intval($reward['vip_tcnum2']*100);
			
			$reward_flagstr=($reward['flag']==1)?'':'<em>注：为防刷单，网站开启了验证功能，新注册会员需客服人员核验真实有效后奖励，已注册的会员充值和升VIP直接奖励无需审核</em>';
            ?>
            <li>
            ● 新会员注册成功奖励
            <p>直接奖励：<b> <?php echo $price1.$priceT;?>/人</b> (您直接分享注册的会员)</p>
            <p>团队奖励：<b> <?php echo $price2.$priceT;?>/人</b> (您的团队成员分享注册的会员)</p>
            <?php echo $reward_flagstr;?>
            </li>
            <li>
            ● 会员在线充值奖励
            <p>直接奖励：<b> <?php echo $czbfb1;?>%</b></p>
            <p>团队奖励：<b> <?php echo $czbfb2;?>%</b> </p>
            </li>
            <li>
            ● 会员开通VIP奖励
            <p>直接奖励：<b> <?php echo $vipbfb1;?>%</b></p>
            <p>团队奖励：<b> <?php echo $vipbfb2;?>%</b></p>
            </li>
        </ul>
	</div>
    <script>
    tgpic='<?php echo $data_tgpic;?>';
	<?php if (is_weixin()){//分享?>
		var share_TG_title = '推荐一个靠谱的婚恋平台';
		var share_TG_desc  = '<?php echo (!empty($data_nickname))?'我是'.$data_nickname.'，':'';?>这是一个非常靠谱信誉度非常高的婚恋平台，强烈推荐给你，点开后直接注册.';
		var share_TG_link  = '<?php echo HOST; ?>/m1/reg.php?tguid=<?php echo $cook_uid; ?>';
		var share_TG_imgurl= '<?php echo $_ZEAI['up2'].'/'.$data_tgpic;?>';
		wx.ready(function () {
			wx.onMenuShareAppMessage({title:share_TG_title,desc:share_TG_desc,link:share_TG_link,imgUrl:share_TG_imgurl});
			wx.onMenuShareTimeline({title:share_TG_title,link:share_TG_link,imgUrl:share_TG_imgurl});
		});
	<?php }?>
    </script>
	<?php
	exit;
exit;}elseif($submitok == 'TG_BANG'){
	$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe61f;</i>～～暂无内容～～<a class=\"btn size4 yuan TG_BANGabtn\" onclick=\"TG_BANGbtnFn();\">立即去招募</a><div>通过我分享的分享二维码加入即可成为我的团队成员，发展的我团队，让收益迅速暴增</div></div>";
	$mini_backT = '返回';
	$mini_title = "<i class='ico goback' id='ZEAIGOBACK-TG_BANG'>&#xe602;</i>";
	$mini_class = 'top_mini top_miniBAI';
	require_once ZEAI.'m1/top_mini.php';
	?>
	<div class="submain TG_BANG">
		<?php
		if($reward['kind']==1){//loveb
			$fild = 'tgallloveb';
		}elseif($reward['kind']==2){//元
			$fild = 'tgallmoney';
		}
        $rt=$db->query("SELECT id,uname,nickname,sex,grade,tgallloveb,tgallmoney FROM ".__TBL_USER__." ORDER BY $fild DESC LIMIT 8");
        $total = $db->num_rows($rt);
        if ($total <= 0) {
			echo $nodatatips;
		}else{?>
            <h2>收益榜</h2>
            <ul><li>排名</li><li>分享会员</li><li>总收益(<?php echo $priceT;?>)</li></ul>
			<?php
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'name');
                if(!$rows)break;
                $uid     = $rows['id'];
                $uname    = dataIO($rows['uname'],'out');
                $nickname = dataIO($rows['nickname'],'out');
                $sex      = $rows['sex'];
                $grade    = $rows['grade'];
                $tgallloveb = $rows['tgallloveb'];
                $tgallmoney = $rows['tgallmoney'];
                //
				if($reward['kind']==1){//loveb
					$price = $tgallloveb;
				}elseif($reward['kind']==2){//元
					$price = $tgallmoney;
				}
				$nickname = (empty($nickname))?$uname:$nickname;
                $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
                $sexbg       = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
				if ($i == 1){
					$ico = '<i class="ico i1">&#xe638;</i>';
				}elseif($i == 2){
					$ico = '<i class="ico i2">&#xe638;</i>';
				}elseif($i == 3){
					$ico = '<i class="ico i3">&#xe638;</i>';
				}else{
					$ico = $i;
				}
				?>
				<dl>
					<ul class="C">
						<li><?php echo $ico;?></li>
						<li><span><?php echo uicon($sex.$grade);?><?php echo $nickname;?></span></li>
						<li><?php echo $price;?></li>
					</ul>
				</dl>
				<?php }
				echo '<a class="btn size4 yuan TG_BANGabtn" onclick="TG_BANGbtnFn();">我要分享赚赏金</a>';
			}?>
	</div>
	<?php
exit;}elseif($submitok == 'TG_HELP'){
	$mini_backT = '返回';
	$mini_title = "<i class='ico goback' id='ZEAIGOBACK-TG_HELP'>&#xe602;</i>帮助";
	$mini_class = 'top_mini top_miniBAI';
	require_once ZEAI.'m1/top_mini.php';
	?>
    <div class="submain TG_HELP">
    	<h2>收益是怎么算的?</h2>
        <ul class="sy">
			<?php
            $reward=$tg['reward'];
            if($reward['kind']==1){//loveb
                $price1  = $reward['kind1num1'];
                $price2  = $reward['kind1num2'];
                $priceT = $_ZEAI['loveB'];
            }elseif($reward['kind']==2){//元
                $price1  = $reward['kind2num1'];
                $price2  = $reward['kind2num2'];
                $priceT = '元';
            }
            $czbfb1  = intval($reward['cz_tcnum1']*100);
            $czbfb2  = intval($reward['cz_tcnum2']*100);
            $vipbfb1 = intval($reward['vip_tcnum1']*100);
            $vipbfb2 = intval($reward['vip_tcnum2']*100);
			$reward_flagstr=($reward['flag']==1)?'':'<em>注：为防刷单，网站开启了验证功能，新注册会员需客服人员核验真实有效后奖励，已注册的会员充值和升VIP直接奖励无需审核</em>';
            ?>
            <li>
                <h5>1.新会员注册成功奖励</h5>
                <p>直接奖励：<b> <?php echo $price1.$priceT;?>/人</b> <br>(您直接分享注册的会员，一级下线)</p>
                <p>团队奖励：<b> <?php echo $price2.$priceT;?>/人</b> <br>(您的一级下线成员又分享注册了会员)</p>
                <?php echo $reward_flagstr;?>
            </li>
            <li>
                <h5>2.会员在线充值奖励</h5>
                <p>直接奖励：<b> <?php echo $czbfb1;?>%</b></p>
                <p>团队奖励：<b> <?php echo $czbfb2;?>%</b> </p>
                </li>
            <li>
                <h5>3.会员开通VIP奖励</h5>
                <p>直接奖励：<b> <?php echo $vipbfb1;?>%</b></p>
                <p>团队奖励：<b> <?php echo $vipbfb2;?>%</b></p>
            </li>
        </ul><br>
        <h2>如何分享？</h2>
        <ul class="sy">
            <li>
                <h5>1.分享/发送我的专属二维码分享海报给好友</h5>
                <input type="button" value="查看我的二维码" class="btn size4 LV2 tgpicdown" onClick="ZeaiM.page.load('m1/TG'+zeai.ajxext+'submitok=ewm','TG_HELP','TG_ewm');">
                <br><h5>2.公众号菜单直接呼出分享海报二维码</h5>
                在公众号菜单点【我的】然后点【我要分享】，即可自动呼出您的专属二维码
                <br><br><h5>3.进入分享页面，点击微信右上角分享给好友或朋友圈进行分享</h5>
                <input type="button" value="进入分享页面" class="btn size4 LV2 tgpicdown" onClick="ZeaiM.page.load('m1/TG'+zeai.ajxext+'submitok=ewm','TG_HELP','TG_ewm');">
                <br><h5>4.网址分享，可以把网址和文字一起发送给好友微信或QQ等，开始把下面的绿色部分文字和网址复制给你朋友吧</h5>
                <div class="C090"><?php echo str_replace("@ZEAI@",$cook_uid,dataIO($tg['text'],'out'));?></div>
            </li>
        
        </ul>
    </div>
    <?php
exit;}
/**************************W*W*W*.*Z*E*A*I*.*C*N*****V*6*.*0**********************/
$rt=$db->query("SELECT SUM(money) AS txallmoney FROM ".__TBL_PAY__." WHERE kind=-1 AND uid=".$cook_uid);
$row=$db->fetch_array($rt,'name');
$txallmoney=intval($row['txallmoney']);
$tgtipnum = $db->COUNT(__TBL_TIP__,"( title LIKE '%推荐%' ) AND new=1 AND uid=".$cook_uid);
$tgtipnum_str = ($tgtipnum>0)?'<b></b>':'';
?>
<link href="m1/css/TG.css" rel="stylesheet" type="text/css" />
<?php
$mini_backT = '';
$mini_title = '分享赚赏金';
$mini_class = 'top_mini top_miniTG';
require_once ZEAI.'m1/top_mini.php';?>
<i class='ico goback' id='ZEAIGOBACK-TG'>&#xe602;</i>
<div class="submain TG">
	<div class="topbg">
    	<ul class="navT">
        	<li><dt><b><?php echo $data_tgallmoney;?></b>元</dt><dd>累计总收益</dd></li>
        	<li><dt><b><?php echo $txallmoney;?></b>元</dt><dd>累计总提现</dd></li>
        	<li><dt><b><?php echo $data_money;?></b>元</dt><dd>账户余额</dd></li>
            <div class="clear"></div>
            <a class="btn size4 yuan" onClick="TG_btn();" id="maintgbtn">立即分享</a>
        </ul>
    </div>
    <div class="menu">
    	<ul>
            <li onclick="page({g:'m1/TG.php?submitok=TG_U',y:'TG',l:'TG_U'})"><i class="ico wddst">&#xe603;</i><h4>我的单身团</h4><span id="TG_U_num"></span></li>
            <li onclick="page({g:'m1/TG.php?submitok=TG_TD',y:'TG',l:'TG_TD'})"><i class="ico wdtd">&#xe637;</i><h4>我的团队</h4><span>团队作战，收益倍增</span></li>
            <li onclick="page({g:'m1/TG.php?submitok=TG_MSG',y:'TG',l:'TG_MSG'})"><i class="ico xttz">&#xe657;</i><h4>系统通知</h4><?php echo $tgtipnum_str;?></li>
        </ul>
        <ul>
            <li onclick="page({g:'m1/my_money.php?a=tx',y:'TG',l:'my_money'})"><i class="ico wytx">&#xe639;</i><h4>我要提现</h4></li>
            <li id="TG_dhlovebBtn"><i class="ico dhad">&#xe618;</i><h4>兑换<?php echo $_ZEAI['loveB'];?></h4></li>
            <li onclick="page({g:'m1/my_money.php?a=mx&i=提现',y:'TG',l:'my_money'})"><i class="ico txjl">&#xe63a;</i><h4>提现记录</h4></li>
            <li onclick="page({g:'m1/my_money.php?a=mx&i=推荐',y:'TG',l:'my_money'})"><i class="ico symx">&#xe656;</i><h4>收益明细</h4></li>
        </ul>
    </div>
</div>
<div class="TG_BtmBM">
	<a class="sy ed"><i class="ico">&#xe7a0;</i><span>首页</span></a>
	<a class="syb" onclick="page({g:'m1/TG.php?submitok=TG_BANG',y:'TG',l:'TG_BANG'})"><i class="ico">&#xe6fd;</i><span>收益榜</span></a>
	<a class="ljtg" onclick="page({g:'m1/TG.php?submitok=TG_HELP',y:'TG',l:'TG_HELP'})"><i class="ico">&#xe616;</i><span>帮助</span></a>
	<a class="ljtg" onClick="TG_btn();"><i class="ico">&#xe615;</i><span>立即分享</span></a>
</div>
<script>
var tgpic='<?php echo $data_tgpic;?>',
	browser='<?php echo (is_weixin())?'wx':'h5';?>',
	up2='<?php echo $_ZEAI['up2'];?>/';
	TG_dhlovebBtn.onclick=TG_dhlovebBtnFn;
	setTimeout(function(){	zeai.ajax({url:'m1/TG.php?submitok=ajax_TG_U_num'},function(e){rs=zeai.jsoneval(e);TG_U_num.html(rs.num+'人');});	},500);	
	
	
<?php if (!empty($a)){?>

	<?php if ($a == 'TG_ewm'){?>
		//setTimeout(function(){maintgbtn.click();},500);
		if(zeai.empty(tgpic))zeai.msg('正在生成您的专属分享海报，请稍等...',{time:3});
		ZeaiM.page.load('m1/TG'+zeai.ajxext+'submitok=ewm','TG','TG_ewm');
	<?php }else{?>
		page({g:'m1/TG.php?submitok=<?php echo $a;?>',y:'TG',l:'<?php echo $a;?>'});
	<?php }?>

<?php }?>
	


</script>
<script src="m1/js/TG.js"></script>
<?php
function make_tg_ewm($token){
	global $_ZEAI,$cook_uid,$cook_pwd,$browser;
	$url = $_ZEAI['up2'].'/TG_ewm.php';
	$data = array (
		'uid' => $cook_uid,
		'pwd' => $cook_pwd,
		'browser' => $browser,
		'token' => $token
	);
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );
	$ret = json_decode($return,true);
	return $ret;
}
?>
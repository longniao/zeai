<?php
require_once '../sub/init.php';
if($request=='www_zeai_cn__ajax' && !ifint($cook_tg_uid))json_exit(array('flag'=>'nologin_tg','msg'=>'请您先登录帐号','jumpurl'=>HOST.'/m1/tg_my.php'));
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_shop.php';
require_once ZEAI.'sub/conn.php';
$TG_set = json_decode($_REG['TG_set'],true);
if(ifint($cook_uid)){
	$ifed=true;
	if(!empty($cook_openid)){
		$rowtg = $db->ROW(__TBL_TG_USER__,"id,uname,nickname,mob,pwd,uid","openid='$cook_openid'","name");
		if ($rowtg){
			$cook_tg_uid   = $rowtg['id'];
			$uid           = intval($rowtg['uid']);
			$cook_tg_uname = $rowtg['uname'];
			$cook_tg_nickname = $rowtg['nickname'];
			$cook_tg_mob   = $rowtg['mob'];
			$cook_tg_pwd   = $rowtg['pwd'];
			if(!ifint($uid))$db->query("UPDATE ".__TBL_TG_USER__." SET uid=".$cook_uid." WHERE id=".$cook_tg_uid);
			setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_mob",$cook_tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_openid",$cook_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
		}else{
			$ifed=false;
		}
	}
	if(empty($cook_openid) || !$ifed){
		$rowtg = $db->ROW(__TBL_TG_USER__,"id,uname,mob,pwd,nickname","uid=".$cook_uid,"name");
		if (!$rowtg){
			$ifadd=false;
			$rowU = $db->ROW(__TBL_USER__,"uname,pwd,mob,RZ,openid,subscribe,weixin,qq,aboutus,areaid,areatitle,nickname,photo_s,tguid","id=".$cook_uid,"name");
			if ($rowU){
				$mob= $rowU['mob'];
				$uname=$rowU['uname'];
				$pwd  = $rowU['pwd'];
				$openid    =$rowU['openid'];
				$subscribe =$rowU['subscribe'];
				$RZ      = $rowU['RZ'];$RZarr = explode(',',$RZ);
				$weixin  = $rowU['weixin'];
				$qq      = $rowU['qq'];
				$aboutus = $rowU['aboutus'];
				$areaid  = $rowU['areaid'];
				$areatitle = $rowU['areatitle'];
				$nickname  = $rowU['nickname'];
				$photo_s   = $rowU['photo_s'];
				$U_tguid   = intval($rowU['tguid']);
				if(ifmob($mob) && in_array('mob',$RZarr)){
					$rowtg2 = $db->ROW(__TBL_TG_USER__,"id,uname,mob,pwd","mob='$mob' AND FIND_IN_SET('mob',RZ)","name");
					if($rowtg2){
						$cook_tg_uid   = $rowtg2['id'];
						$cook_tg_uname = $rowtg2['uname'];
						$cook_tg_mob   = $rowtg2['mob'];
						$cook_tg_pwd   = $rowtg2['pwd'];
						setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
						setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
						setcookie("cook_tg_mob",$cook_tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
						setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
					}else{
						$ifadd=true;
					}
				}else{
					$ifadd=true;
				}
				//
				if($ifadd){
					$flag   = ($TG_set['regflag'] == 1)?0:1;
					$row2   = $db->ROW(__TBL_TG_ROLE__,"grade,title","shopgrade=0 AND ifdefault=1","num");
					$grade  = $row2[0];
					$gradetitle = $row2[1];
					$sjtime = ADDTIME;
					$ip     =getip();
					$kind   = 1;
					if($TG_set['active_price']>0)$flag=2;
					//
					$row2 = $db->ROW(__TBL_TG_USER__,"id","uname='$uname'");
					if($row2)$uname=$cook_uid;
					//
					if(!empty($photo_s)){
						$dbdir  = 'p/tg/'.date('Y').'/'.date('m').'/';
						@mk_dir(ZEAI.'/up/'.$dbdir);
						//
						$old_s = $photo_s;
						$old_m = smb($photo_s,'m');
						$old_b = smb($photo_s,'b');
						$old_blur = smb($photo_s,'blur');
						//
						$oldDST_s = ZEAI.'/up/'.$old_s;
						$oldDST_m = ZEAI.'/up/'.$old_m;
						$oldDST_b = ZEAI.'/up/'.$old_b;
						$oldDST_blur = ZEAI.'/up/'.$old_blur;
						//
						$newDST_s = ZEAI.'/up/'.$dbdir.basename($oldDST_s);
						$newDST_m = ZEAI.'/up/'.$dbdir.basename($oldDST_m);
						$newDST_b = ZEAI.'/up/'.$dbdir.basename($oldDST_b);
						$newDST_blur = ZEAI.'/up/'.$dbdir.basename($oldDST_blur);
						//
						@copy($oldDST_s,$newDST_s);
						@copy($oldDST_m,$newDST_m);
						@copy($oldDST_b,$newDST_b);
						@copy($oldDST_blur,$newDST_blur);
						$new_photo_s = $dbdir.basename($oldDST_s);
					}
					//
					$db->query("INSERT INTO ".__TBL_TG_USER__." (tguid,uid,uname,nickname,flag,pwd,grade,gradetitle,regtime,endtime,regip,endip,kind,openid,subscribe,qq,weixin,content,areaid,areatitle,photo_s) VALUES ($U_tguid,$cook_uid,'$uname','$nickname',$flag,'".$pwd."',$grade,'$gradetitle',".ADDTIME.",".ADDTIME.",'$ip','$ip',$kind,'$openid','$subscribe','$qq','$weixin','$aboutus','$areaid','$areatitle','$new_photo_s')");
					$tg_uid = intval($db->insert_id());
					if(ifmob($mob) && in_array('mob',$RZarr)){
						$db->query("UPDATE ".__TBL_TG_USER__." SET mob='$mob',RZ='mob' WHERE id=".$tg_uid);
					}
					$cook_tg_uid = $tg_uid;
					$cook_tg_pwd = $pwd;
					$cook_tg_openid= $openid;
					setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
					setcookie("cook_tg_openid",$cook_tg_openid,time()+7200000,"/",$_ZEAI['CookDomain']);
				}
				//
			}
		}else{
			$cook_tg_uid = $rowtg['id'];
			$cook_tg_uname = $rowtg['uname'];
			$cook_tg_nickname = $rowtg['nickname'];
			$cook_tg_mob = $rowtg['mob'];
			$cook_tg_pwd = $rowtg['pwd'];
			setcookie("cook_tg_uid",$cook_tg_uid,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_uname",$cook_tg_uname,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_mob",$cook_tg_mob,time()+720000,"/",$_ZEAI['CookDomain']);
			setcookie("cook_tg_pwd",$cook_tg_pwd,time()+720000,"/",$_ZEAI['CookDomain']);
		}
	}
}
if(ifint($cook_tg_uid)){
	$row  = $db->ROW(__TBL_TG_USER__,"subscribe,uid,openid,ifshop,nickname,title","id='".$cook_tg_uid."'","num");
	$data_subscribe = $row[0];
	$data_uid       = $row[1];
	$data_openid    = $row[2];
	$data_ifshop    = $row[3];
	$cook_tg_nickname = $row[4];
	$cook_tg_title = $row[5];
	$cook_tg_nickname = (empty($cook_tg_nickname))?$cook_tg_title:$cook_tg_nickname;
	if(empty($data_openid) && ifint($data_uid)){
		$Urow = $db->ROW(__TBL_USER__,"subscribe,openid","id=".$data_uid,"num");
		if ($Urow){
			$Usubscribe     = intval($Urow[0]);
			$data_subscribe = $Usubscribe;
			$Uopenid        = $Urow[1];
			$db->query("UPDATE ".__TBL_TG_USER__." SET subscribe=$Usubscribe,openid='$Uopenid' WHERE id=".$cook_tg_uid);
		}
	}
	if($request=='www_zeai_cn__ajax' && $TG_set['force_subscribe']==1 && $data_subscribe!=1){
		json_exit(array('flag'=>'jumpurl','msg'=>'请您先关注公众号','jumpurl'=>HOST.'/m1/tg_subscribe.php'));
	}else{
		if($TG_set['force_subscribe']==1 && $data_subscribe!=1 ){header("Location: ".HOST."/m1/tg_subscribe.php");exit;}
	}
}
$currfields = "photo_s,tgallmoney,tgallloveb,grade,money,kind,tgallloveb,tgallmoney,subscribe";
require_once 'tg_chkuser.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
$data_photo_s=$row['photo_s'];
$data_tgallmoney=str_replace(".00","",$row['tgallmoney']);
$data_tgallloveb=str_replace(".00","",$row['tgallloveb']);
$data_grade=$row['grade'];
$data_kind=$row['kind'];
$data_subscribe=$row['subscribe'];
$data_money=str_replace(".00","",$row['money']);;
$photo_m_url = (!empty($data_photo_s ))?$_ZEAI['up2'].'/'.smb($data_photo_s,'m'):HOST.'/res/tg_my_u1.png';

$switch = json_decode($_ZEAI['switch'],true);

$row_role=$db->ROW(__TBL_TG_ROLE__,"*","shopgrade=0 AND grade=".$data_grade,"name");
$data_gradetitle=$row_role['title'];
$data_logo  =$row_role['logo'];
$reward_kind=$row_role['reward_kind'];



if($reward_kind=='loveb'){//loveb
//	$reg_sex1_num1  = intval($row_role['reg_loveb_sex1_num1']);
//	$reg_sex1_num2  = intval($row_role['reg_loveb_sex1_num2']);
//	$reg_sex2_num1  = intval($row_role['reg_loveb_sex2_num1']);
//	$reg_sex2_num2  = intval($row_role['reg_loveb_sex2_num2']);
	$priceT = $_ZEAI['loveB'];
}elseif($reward_kind=='money'){//元
	$reg_sex1_num1  = floatval($row_role['reg_money_sex1_num1']);
	$reg_sex1_num2  = floatval($row_role['reg_money_sex1_num2']);
	$reg_sex2_num1  = floatval($row_role['reg_money_sex2_num1']);
	$reg_sex2_num2  = floatval($row_role['reg_money_sex2_num2']);

	$cz_sex1_num1  = intval($row_role['cz_sex1_num1']);
	$cz_sex1_num2  = intval($row_role['cz_sex1_num2']);
	$cz_sex2_num1  = intval($row_role['cz_sex2_num1']);
	$cz_sex2_num2  = intval($row_role['cz_sex2_num2']);
	
	$vip_sex1_num1 = intval($row_role['vip_sex1_num1']);
	$vip_sex1_num2 = intval($row_role['vip_sex1_num2']);
	$vip_sex2_num1 = intval($row_role['vip_sex2_num1']);
	$vip_sex2_num2 = intval($row_role['vip_sex2_num2']);

	$rz_sex1_num1 = intval($row_role['rz_sex1_num1']);
	$rz_sex1_num2 = intval($row_role['rz_sex1_num2']);
	$rz_sex2_num1 = intval($row_role['rz_sex2_num1']);
	$rz_sex2_num2 = intval($row_role['rz_sex2_num2']);
	
	$union_reg_num1  = floatval($row_role['union_reg_num1']);
	$union_reg_num2  = floatval($row_role['union_reg_num2']);
	$union_num1 = intval($row_role['union_num1']);
	$union_num2 = intval($row_role['union_num2']);

	$priceT = '元';
}

if(!empty($data_logo)){
	$gradeico_str='<img src="'.$_ZEAI['up2'].'/'.$data_logo.'">';
}else{
	$gradeico_str='<img src="'.HOST.'/res/tg_ico.svg">';
}
switch ($data_kind) {
	case 1:$kind_str='个人';break;
	case 2:$kind_str='公司';break;
	case 3:$kind_str='机构';break;
}
/*************AJAX页面开始*************/
if($submitok == 'TG_TD_list1' || $submitok == 'TG_TD_list2'){
	$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe61f;</i>～～你还没有发展小伙伴～～<a id=\"TG_Uabtn\" class=\"btn size4 yuan TG_Uabtn\" onclick=\"page({g:'tg_my_ewm.php',y:'TG_TD',l:'tg_my_ewm'});\">立即去招募</a><div>通过我分享的分享二维码加入即可成为我的团队成员，发展的我团队，让收益迅速暴增</div></div>";
	//我的团队
	if($submitok == 'TG_TD_list2'){
		$rt=$db->query("SELECT id,uname,nickname,photo_s,regtime,tgallmoney,tgflag,tgmoney FROM ".__TBL_TG_USER__." WHERE tguid IN (SELECT id FROM zeai_tg_user WHERE tguid=".$cook_tg_uid.") ORDER BY id DESC");
		$dlcls=' class="fadeInL"';
	//我的合伙人
	}else{
		$dlcls=' class="fadeInR"';
		$rt=$db->query("SELECT id,uname,nickname,photo_s,regtime,tgallmoney,tgflag,tgmoney FROM ".__TBL_TG_USER__." WHERE tguid=".$cook_tg_uid." ORDER BY id DESC");
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
			$tgallmoney = $rows['tgallmoney'];
			$regtime  = YmdHis($rows['regtime']);
			$tgflag   = $rows['tgflag'];
			$tgmoney  = $rows['tgmoney'];
			//
			$nickname = (empty($nickname))?$uname:$nickname;
			$nickname = (empty($nickname))?'ID:'.$uid:$nickname;
			$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
			$clsname = ($tgmoney > 0)?' ed':'';	
			$tgmoney = str_replace("0.00","",$tgmoney);
			if($tgmoney > 0){
				$tgmoney=$tgmoney.'<span>'.$priceT.'</span>';
			}
			switch ($tgflag) {
				case 1:$tgflag_str = '<fnot class="C090">已奖励</font>';break;
				case 2:$tgflag_str = '已驳回';break;
				case 0:$tgflag_str = '待审核';break;
			}
			?>
			<dl<?php echo $dlcls;?>>
				<dt><img src="<?php echo $photo_s_url; ?>"></dt>
				<dd><h4><span><?php echo $nickname; ?></span></h4><h6><?php echo $regtime; ?></h6></dd>
				<div class="time"><?php echo $tgflag_str;?></div>
				<div class="price<?php echo $clsname;?>"><?php echo $tgmoney; ?></div>
			</dl>
	<?php }}else{echo $nodatatips;}
	exit;
}elseif($submitok == 'TG_U_list1' || $submitok == 'TG_U_list2'){
	$nodatatips = "<div class='nodatatips' style=\"margin-top:20px\"><i class='ico'>&#xe61f;</i>～～你还没有单身团成员～～<a id=\"TG_Uabtn\" class=\"btn size4 yuan TG_Uabtn\" onclick=\"page({g:'tg_my_ewm.php',y:'TG_U',l:'tg_my_ewm'});\">立即去招募</a><br>通过我分享的分享二维码加入到我的单身团注册用户即可获得奖励</div>";
	$fldU="id,uname,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,heigh,regtime,tgflag";
	//我的单身团团队
	if($submitok == 'TG_U_list2'){
		$rt=$db->query("SELECT $fldU FROM ".__TBL_USER__." WHERE tguid IN (SELECT id FROM zeai_tg_user WHERE tguid=".$cook_tg_uid.") ORDER BY id DESC");
		$dlcls=' class="fadeInL"';
	//我的单身团
	}else{
		$dlcls=' class="fadeInR"';
		$rt=$db->query("SELECT $fldU FROM ".__TBL_USER__." WHERE tguid=".$cook_tg_uid." ORDER BY id DESC");
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
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
			$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			$tgflag_str  = ($tgflag == 1)?'<span class="ed">已奖励</span>':'<span>待验证</span>';
			?>
			<dl<?php echo $dlcls;?>>
				<dt><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></dt>
				<dd><h4><?php echo uicon($sex.$grade);?><span><?php echo $nickname; ?></span></h4><h6><?php echo $birthday_str.$heigh_str; ?><?php echo $areatitle_str; ?></h6></dd>
				<div class="time"><?php echo $regtime; ?></div>
				<div class="tgflag"><?php echo $tgflag_str; ?></div>
			</dl>
	<?php }}else{echo $nodatatips;}
	exit;
}

switch ($submitok) {
	case 'TG_U':
		$mini_backT = '';
		$mini_title = '　我的单身团';
		$mini_class = 'top_mini top_miniTG';
		require_once ZEAI.'m1/top_mini.php';
		//用户
		$rt=$db->query("SELECT COUNT(*) AS num FROM ".__TBL_USER__." WHERE tguid=".$cook_tg_uid." ");
		$row  = $db->fetch_array($rt,'name');
		$num1 = intval($row['num']);
		$num1 = ($num1>0)?'<b>'.$num1.'</b>':'';
		//团队
		$rt=$db->query("SELECT COUNT(*) AS num FROM ".__TBL_USER__." WHERE tguid IN (SELECT id FROM zeai_tg_user WHERE tguid=".$cook_tg_uid.") ");
		$row  = $db->fetch_array($rt,'name');
		$num2 = intval($row['num']);
		$num2 = ($num2>0)?'<b>'.$num2.'</b>':'';
		?>
		<i class='ico goback' id='ZEAIGOBACK-TG_U'>&#xe602;</i>
		<div class="submain TG_U TG_TDU">
			<div class="tabmenu tabmenu_2" id="tabmenuTG_U">
				<li data='tg_my.php?submitok=TG_U_list1' id="TG_U_btn1" class="ed"><span>直接推荐</span><?php echo $num1;?></li>
				<li data='tg_my.php?submitok=TG_U_list2' id="TG_U_btn2"><span>团队推荐</span><?php echo $num2;?></li>
				<i></i>
			</div>
			<div class="ubox" id="TG_U_ubox"></div>
			<script>ZeaiM.tabmenu.init({showbox:TG_U_ubox,obj:tabmenuTG_U});setTimeout(function(){TG_U_btn1.click();},100);</script>
		</div>
		<?php
		exit;
	break;
	case 'TG_TD':
		$mini_title = '我的合伙人';
		$mini_class = 'top_mini top_miniTG';
		require_once ZEAI.'m1/top_mini.php';
		//合伙人
		$rt=$db->query("SELECT COUNT(*) AS num FROM ".__TBL_TG_USER__." WHERE tguid=".$cook_tg_uid." ");
		$row  = $db->fetch_array($rt,'name');
		$num1 = intval($row['num']);
		$num1 = ($num1>0)?'<b>'.$num1.'</b>':'';
		//团队
		$rt=$db->query("SELECT COUNT(*) AS num FROM ".__TBL_TG_USER__." WHERE tguid IN (SELECT id FROM zeai_tg_user WHERE tguid=".$cook_tg_uid.") ");
		$row  = $db->fetch_array($rt,'name');
		$num2 = intval($row['num']);
		$num2 = ($num2>0)?'<b>'.$num2.'</b>':'';
		?>
		<i class='ico goback' id='ZEAIGOBACK-TG_TD'>&#xe602;</i>
		<div class="submain TG_TD TG_TDU">
			<div class="tabmenu tabmenu_2" id="tabmenuTG_TD">
				<li data='tg_my.php?submitok=TG_TD_list1' id="TG_TD_btn1" class="ed"><span>直接推荐</span><?php echo $num1;?></li>
				<li data='tg_my.php?submitok=TG_TD_list2' id="TG_TD_btn2"><span>团队推荐</span><?php echo $num2;?></li>
				<i></i>
			</div>
			<div class="ubox" id="TG_TD_ubox"></div>
			<script>ZeaiM.tabmenu.init({showbox:TG_TD_ubox,obj:tabmenuTG_TD});setTimeout(function(){TG_TD_btn1.click();},100);</script>
		</div>
	<?php
	exit;break;
	case 'TG_MSG':
		$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
		$mini_backT = '返回';
		$mini_title = "<i class='ico goback' id='ZEAIGOBACK-TG_MSG'>&#xe602;</i>系统通知";
		$mini_class = 'top_mini top_miniBAI';$mini_R = '<a id="TG_MSG_btndel">清空</a>';
		require_once ZEAI.'m1/top_mini.php';
		?>
		<div class="submain TG_MSG" id="TG_MSGbox">
			<?php 
			$_ZEAI['pagesize'] = 100;
			$rt=$db->query("SELECT id,title,new,addtime FROM ".__TBL_TIP__." WHERE tg_uid=".$cook_tg_uid." AND kind=5 ORDER BY id DESC LIMIT ".$_ZEAI['pagesize']);//推荐
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
					zeai.ajax({url:'tg_my'+zeai.extname,data:{submitok:'ajax_MSG_clean'}},function(e){rs=zeai.jsoneval(e);
						if(rs.flag==1){TG_MSGbox.html("<?php echo $nodatatips;?>");tg_num_btm.remove();}
					});
				}});
			}
			</script>
		</div>
		<?php
	exit;break;
	case 'TG_MSG_detail':
		if(!ifint($tid))exit(JSON_ERROR);
		$row = $db->ROW(__TBL_TIP__,"content,new,addtime","tg_uid=".$cook_tg_uid." AND kind=5 AND id=".$tid);
		if ($row){
			$content = dataIO($row[0],'out');
			$new     = $row[1];
			$addtime = YmdHis($row[2]);
			if ($new == 1){
				$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE kind=5 AND id=".$tid." AND tg_uid=".$cook_tg_uid);
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
	exit;break;
	case 'ajax_MSG_del':
		if(!ifint($tid))exit(JSON_ERROR);
		$db->query("DELETE FROM ".__TBL_TIP__." WHERE kind=5 AND tg_uid=".$cook_tg_uid." AND id=".$tid);
		json_exit(array('flag'=>1));
	exit;break;
	case 'ajax_MSG_clean':
		$db->query("DELETE FROM ".__TBL_TIP__." WHERE kind=5 AND tg_uid=".$cook_tg_uid);
		json_exit(array('flag'=>1));
	exit;break;
/*	case 'ajax_TD_count1':
		if($reward_kind=='loveb'){//loveb
			$filed     = 'tgallloveb';
			$XX['num1'] = $data_tgallloveb;
		}elseif($reward_kind=='money'){//元
			$filed     = 'tgallmoney';
			$XX['num1'] = $data_tgallmoney;
		}
		$XX['num2'] = $db->COUNT(__TBL_USER__,"tguid=".$cook_tg_uid);
		$rt=$db->query("SELECT COUNT(*) AS num FROM ".__TBL_USER__." WHERE tguid IN (SELECT id FROM zeai_user WHERE tguid=".$cook_tg_uid.") ");
		$row = $db->fetch_array($rt,'name');
		$XX['num3'] = intval($row['num']);
		json_exit(array('XX'=>$XX));
	exit;break;
*/	case 'TG_HELP':
		$mini_backT = '返回';
		$mini_title = "<i class='ico goback' id='ZEAIGOBACK-TG_HELP'>&#xe602;</i>帮助";
		$mini_class = 'top_mini top_miniBAI';
		require_once ZEAI.'m1/top_mini.php';
		?>
		<div class="submain TG_HELP">
			<h2><?php echo $data_gradetitle;?>收益是怎么算的?</h2>
			<ul class="sy">
                <?php if ($reg_sex1_num1>0 || $reg_sex2_num1>0 || $reg_sex1_num2>0 || $reg_sex2_num2>0){?>
				<li>
					<h5>单身用户注册</h5>
					<p>直接奖励：男<b> <?php echo ($reg_sex1_num1>0)?$reg_sex1_num1.$priceT.'/人':'无';?></b>　女<b> <?php echo ($reg_sex2_num1>0)?$reg_sex2_num1.$priceT.'/人':'无';?></b> <br>(您直接分享注册的用户，一级小伙伴)</p>
					<p>团队奖励：男<b> <?php echo ($reg_sex1_num2>0)?$reg_sex1_num2.$priceT.'/人':'无';?></b>　女<b> <?php echo ($reg_sex2_num2>0)?$reg_sex2_num2.$priceT.'/人':'无';?></b> <br>(您的一级小伙伴成员又分享注册了用户)</p>
					<?php echo ($TG_set['reward_flag'] == 1)?'':'<em>注：为防刷单，网站开启了验证功能，新注册用户需客服人员核验真实有效后奖励，已注册的用户【充值/升VIP/认证】直接奖励无需审核</em>';?>
				</li>
                <?php }
				if ($cz_sex1_num1>0 || $cz_sex2_num1>0 || $cz_sex1_num2>0 || $cz_sex2_num2>0){?>
				<li>
					<h5>单身用户在线充值</h5>
					<p>直接奖励：男<b><?php echo ($cz_sex1_num1>0)?$cz_sex1_num1.'%':'无';?></b>　女<b><?php echo ($cz_sex2_num1>0)?$cz_sex2_num1.'%':'无';?></b></p>
					<p>团队奖励：男<b><?php echo ($cz_sex1_num2>0)?$cz_sex1_num2.'%':'无';?></b>　女<b><?php echo ($cz_sex2_num2>0)?$cz_sex2_num2.'%':'无';?></b></p>
				</li>
                <?php }
                if ($vip_sex1_num1>0 || $vip_sex2_num1>0 || $vip_sex1_num2>0 || $vip_sex2_num2>0){?>
				<li>
					<h5>单身用户开通VIP</h5>
					<p>直接奖励：男<b><?php echo ($vip_sex1_num1>0)?$vip_sex1_num1.'%':'无';?></b>　女<b><?php echo ($vip_sex2_num1>0)?$vip_sex2_num1.'%':'无';?></b></p>
					<p>团队奖励：男<b><?php echo ($vip_sex1_num2>0)?$vip_sex1_num2.'%':'无';?></b>　女<b><?php echo ($vip_sex2_num2>0)?$vip_sex2_num2.'%':'无';?></b></p>
				</li>
                <?php }
				if ($rz_sex1_num1>0 || $rz_sex1_num1>0 || $rz_sex2_num2>0 || $rz_sex2_num2>0){?>
				<li>
					<h5>单身用户认证奖励（实名+真人）</h5>
					<p>直接奖励：男<b><?php echo ($rz_sex1_num1>0)?$rz_sex1_num1.'%':'无';?></b>　女<b><?php echo ($rz_sex1_num1>0)?$rz_sex2_num1.'%':'无';?></b></p>
					<p>团队奖励：男<b><?php echo ($rz_sex2_num2>0)?$rz_sex1_num2.'%':'无';?></b>　女<b><?php echo ($rz_sex2_num2>0)?$rz_sex2_num2.'%':'无';?></b></p>
				</li>
                <?php }
                if ($union_reg_num1>0 || $union_reg_num2>0){?>
				<li>
					<h5>合伙人注册</h5>
					<?php if ($union_reg_num1>0){?><p>直接奖励：<b><?php echo $union_reg_num1;?><?php echo $priceT;?></b>/个</b></p><?php }?>
					<?php if ($union_reg_num2>0){?><p>团队奖励：<b><?php echo $union_reg_num2;?><?php echo $priceT;?></b>/个</b></p><?php }?>
				</li>
                <?php }
                if ($union_num1>0 || $union_num2>0){?>
				<li>
					<h5>合伙人激活/升级</h5>
                  	<p>您分享注册的推广员激活帐号或升级至更高级别的推广员</p>
					<?php if ($union_num1>0){?><p>直接奖励：<b><?php echo $union_num1;?>%</b></p><?php }?>
					<?php if ($union_num2>0){?><p>团队奖励：<b><?php echo $union_num2;?>%</b></p><?php }?>
				</li>
                <?php }?>
			</ul><br>
			<h2>如何分享？</h2>
			<ul class="sy">
				<li>
					<h5>1.分享/发送我的专属二维码分享海报给好友</h5>
					<input type="button" value="下载我的二维码" class="btn size4 LV2 tgpicdown"  onClick="page({g:'tg_my_ewm.php',y:'TG_HELP',l:'tg_my_ewm'});">
					<br><h5>2.公众号菜单直接呼出分享海报二维码</h5>
					在公众号菜单点【我的】然后点【我要分享】，即可自动呼出您的专属二维码
					<br><br><h5>3.进入分享页面，点击微信右上角分享给好友或朋友圈进行分享</h5>
					<input type="button" value="进入分享页面" class="btn size4 LV2 tgpicdown" onClick="page({g:'tg_my_ewm.php',y:'TG_HELP',l:'tg_my_ewm'});">
					<br><h5>4.网址分享，可以把网址和文字一起发送给好友微信或QQ等，开始把下面的绿色部分文字和网址复制给你朋友吧</h5>
                    <?php $tglink = HOST.'/m1/reg.php?tguid='.$cook_tg_uid;?>
					<div class="C090"><?php $echo=str_replace("{tglink}",$tglink,dataIO($TG_set['tg_text'],'out'));echo $echo;?>
                    <input type="button" value="复制网址文本" class="btn size4 LV2 tgpicdown"  onclick="zeai.copy('<?php echo $echo;?>',function(){zeai.msg('复制成功');})">
                    </div>
				</li>
			
			</ul>
		</div>
	<?php	
	exit;break;
}
$headertitle = '我的'.$TG_set['navtitle'].'-';$nav = 'tg_my';require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="<?php echo HOST;?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug: false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	</script>
	<?php
}
$cook_tg_uname = (empty($cook_tg_uname))?$cook_tg_mob:$cook_tg_uname;
$rt=$db->query("SELECT SUM(money) AS txallmoney FROM ".__TBL_PAY__." WHERE kind=-1 AND tg_uid=".$cook_tg_uid);
$row=$db->fetch_array($rt,'name');
$txallmoney = floatval($row['txallmoney']);
$txallmoney=str_replace(".00","",$txallmoney);
?>
<link href="css/TG2.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<?php if(@!in_array('tg',$navarr))exit("<div class='nodatatips'><i class='ico'>&#xe61f;</i>推广功能暂未开启</div>");?>

<?php if(ifint($cook_uid)){?>
<i class="ico goback Ugoback" onClick="zeai.openurl('<?php echo HOST;?>/?z=my')" style="z-index:1">&#xe602;</i>
<?php }?>
<div id='main' class='TG huadong'>
    <div class="topbg">
    	<div class="tg_title"><?php echo $TG_set['navtitle'];?></div>
        <div class="photo_s">
        	<img src="<?php echo $photo_m_url;?>" class="m" id="photo_s_id" onclick="page({g:'tg_my_info.php?a=data1',l:'tg_my_info'})">
            <?php if (!empty($kind_str)){?><div class="tg_my_kind tg_my_kind<?php echo $data_kind;?>"><?php echo $kind_str;?></div><?php }?>
            <?php if ($TG_set['company_switch'] == 1){?>
            
                <?php if ($data_ifshop == 1){?>
            	<a class="unionapply" onclick="zeai.openurl('<?php echo HOST;?>/m4/shop_my.php')">我的<?php echo $_SHOP['title'];?><i class="ico S14">&#xe601;</i></a>
                <?php }else{?>
            	<a class="unionapply" onclick="zeai.openurl('<?php echo HOST;?>/m4/shop_my_apply.php')">商家入驻<i class="ico S14">&#xe601;</i></a>
                <?php }?>
            
            <?php }else{?>
            	<?php if ($TG_set['openvip'] == 1){?><a class="unionapply" onClick="page({g:'tg_my_vip.php',l:'tg_my_vip'});"><?php echo $TG_set['tgytitle'];?>升级<i class="ico S14">&#xe601;</i></a><?php }?>
            <?php }?>
            
        	<em>
            	<h4><?php echo $cook_tg_nickname.'<font class="S12">（ID:'.$cook_tg_uid.'）</font>';?></h4>
            	<span<?php if ($TG_set['openvip'] == 1){?> onClick="page({g:'tg_my_vip.php',l:'tg_my_vip'});"<?php }?>><?php echo $gradeico_str.$data_gradetitle;?></span>
            </em>
        </div>
        <ul class="navT">
            <li onclick="page({g:'tg_my_money.php?a=mx&i=sy',l:'tg_my_money'})"><dt><b><?php echo $data_tgallmoney;?></b></dt><dd>累计收益</dd></li>
            <li onclick="page({g:'tg_my_money.php?a=mx&i=tx',l:'tg_my_money'})"><dt><b id="tg_tx_allmoney"><?php echo $txallmoney;?></b></dt><dd>累计提现</dd></li>
            <li onclick="page({g:'tg_my_money.php?a=ye',l:'tg_my_money'})"><dt><b><?php echo $data_money;?></b></dt><dd>账户余额</dd></li>
            <div class="clear"></div>
            <a class="btn size4 yuan" onClick="page({g:'tg_my_ewm.php',l:'tg_my_ewm'});"><i class="ico">&#xe615;</i> 立即推广</a>
        </ul>
    </div>
    <div class="menu">
		<?php if ($TG_set['openvip'] == 1){?>
        <div class="btmvip">
            <div class="vipbox" onClick="page({g:'tg_my_vip.php',l:'tg_my_vip'});">
                <span class="vipico ico">&#xe63f;</span>
                <span class="viptitle">升级等级，提升收益</span>
                <span class="vipc">多重奖励机制，收益翻翻</span>
                <span class="vipbtn">立级升级</span>
            </div>
        </div>
        <?php }?>
    	<ul>
            <?php if ($TG_set['company_switch'] == 1){?><li onclick="zeai.openurl('<?php echo HOST;?>/m4/shop_my.php')"><i class="ico2 product">&#xe71a;</i><h4>我的<?php echo $_SHOP['title'];?></h4><b class="b5"></b></li><?php }?>
            
            <li onclick="page({g:'tg_my.php?submitok=TG_U',l:'TG_U'})"><i class="ico wddst">&#xe603;</i><h4>我的单身团</h4><span id="TG_U_num"></span></li>
            <?php if(@in_array('xqcard',$navarr)){?><li onclick="page({g:'tg_my_ucard.php',l:'tg_my_ucard'})"><i class="ico2 xqk">&#xe64f;</i><h4>用户相亲卡</h4><span>分享拿奖励</span></li><?php }?>
            <li onclick="page({g:'tg_my.php?submitok=TG_TD',l:'TG_TD'})"><i class="ico wdtd">&#xe637;</i><h4>我的合伙人</h4><span>团队作战，收益倍增</span></li>
            <li onclick="page({g:'tg_my.php?submitok=TG_MSG',l:'TG_MSG'})"><i class="ico xttz">&#xe657;</i><h4>系统通知</h4><?php echo $tgtipnum_str;?></li>
        </ul>
        <ul>
            <li onclick="page({g:'tg_my_info.php?a=tg_my_set',l:'tg_my_set'})"><i class="ico2 account">&#xe678;</i><h4>账户/安全</h4></li>
            <li onclick="page({g:'tg_my_money.php?a=mx&i=sy',l:'tg_my_money'})"><i class="ico symx">&#xe656;</i><h4>收益明细</h4></li>
            <?php if($switch['ifrmbtx'] == 1){ ?>
            <li onclick="page({g:'tg_my_money.php?a=tx',l:'tg_my_money'})"><i class="ico wytx">&#xe639;</i><h4>我要提现</h4></li>
            <li onclick="page({g:'tg_my_money.php?a=mx&i=tx',l:'tg_my_money'})"><i class="ico txjl">&#xe63a;</i><h4>提现记录</h4></li>
            <?php }?>
        </ul>
    </div>
	<div class="tg_myblank"><button type="button" onClick="zeai.openurl('<?php echo HOST;?>/loginout.php?url=<?php echo HOST;?>/m1/tg_index.php')" class="btn size4 BAI W90_ yuan my_exit">退出当前帐号</button></div>
    
    <div class="storeapplybox" id="storeapplybox">
        <div class="linebox"><div class="line W50"></div><div class="title S24 BAI B" style="color:#FF5065">联盟合作</div></div>
        <h4>结合线下实体店成为联盟商户，联盟成员可以在平台获得更多品牌曝光，机构用户可以帮助员工及更多客户提供一个严肃真实的婚恋平台</h4>
        <div>
            <a onclick="page({g:'tg_my_info.php?a=apply&i=2',l:'tg_my_info'});applaydiv.click();" class="btn size4 ed">商户合作</a>
            <a onclick="page({g:'tg_my_info.php?a=apply&i=3',l:'tg_my_info'});applaydiv.click();" class="btn size4">机构合作</a>
        </div>
    </div>    
    
</div>
<script>zeaiLoadBack=['nav'];
var upMaxMB = <?php echo intval($_UP['upMaxMB']); ?>,browser='<?php echo (is_weixin())?'wx':'h5';?>',applaydiv;
<?php if ($data_kind == 1 && $TG_set['company_switch'] == 1){?>//unionapply.onclick=function(){applaydiv=ZeaiM.div({obj:storeapplybox,w:'auto',h:360});}<?php }?>

<?php if ($e=='TG_TD'){?>ZeaiM.page.load('tg_my.php?submitok=<?php echo $e;?>',ZEAI_MAIN,'<?php echo $e;?>');
<?php }elseif($e=='TG_U'){?>ZeaiM.page.load('tg_my.php?submitok=<?php echo $e;?>',ZEAI_MAIN,'<?php echo $e;?>');
<?php }elseif($e=='tg_my_money_mx_sy'){?>page({g:'tg_my_money.php?a=mx&i=sy',l:'tg_my_money'});
<?php }elseif($e=='data'){?>page({g:'tg_my_info.php?a=data<?php echo $data_kind;?>',l:'tg_my_info'});
<?php }?>

</script>
<script src="js/TG2.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php require_once ZEAI.'m1/tg_bottom.php';	?>
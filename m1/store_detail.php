<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);

//product_detail
if(ifint($id)){
	$rt = $db->query("SELECT * FROM ".__TBL_TG_PRODUCT__." WHERE flag=1 AND id=".$id);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt,'name');
		$id      = $row['id'];
		$tg_uid  = $row['tg_uid'];
		$path_s  = $row['path_s'];
		$click   = intval($row['click']);
		$kindtitle = dataIO($row['kindtitle'],'out');
		$title     = dataIO($row['title'],'out');
		$content   = dataIO($row['content'],'out');
		$addtime   = $row['addtime'];
		$price   = str_replace(".00","",$row['price']);;
		$price2  = str_replace(".00","",$row['price2']);;
		$path_b_url = (!empty($path_s))?$_ZEAI['up2'].'/'.smb($path_s,'b'):HOST.'/res/noTGbanner.jpg?'.$_ZEAI['cache_str'];
		$rowC = $db->ROW(__TBL_TG_USER__,"uname,title,kind,address,tel,weixin,qq","id=".$tg_uid);
		if ($rowC){
			$photo_s       = $rowC['photo_s'];
			$title_store = dataIO($rowC['title'],'out');
			$kind        = $rowC['kind'];
			$title       = (empty($title))?$uname:$title;
			$title       = (empty($title))?$tg_uid:$title;
			$address     = dataIO($rowC['address'],'out');
			$weixin      = dataIO($rowC['weixin'],'out');
			$qq          = dataIO($rowC['qq'],'out');
			$tel = dataIO($rowC['tel'],'out');
			$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'b'):HOST.'/res/noP.gif?'.$_ZEAI['cache_str'];
			switch ($kind) {
				case 1:$kind_str='公益红娘';break;
				case 2:$kind_str='商户';$p_str='商品';break;
				case 3:$kind_str='机构';$p_str='服务';break;
			}
		}
		$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET click=click+1 WHERE id=".$id);
	} else {json_exit(array('flag'=>0,'msg'=>'信息不存在'));}
	?>
	<style>
	.Ugoback{background-color:rgba(226,28,25,.8)}
	.product_detail{top:0;padding:0}
	.product_detail .banner{position:relative;margin:0;max-height:250px}
	.product_detail .banner img{width:100%;height:100%;max-height:250px;display:block;object-fit:cover;-webkit-object-fit:cover}
	.product_detail .banner .logo{position:absolute;bottom:-40px;left:10px;width:60px;height:60px;border-radius:40px;border:#eee 1px solid}
	.product_detail .banner .kind{width:30px;height:30px;border-radius:30px 0 30px 30px;padding:10px;background-color:#f60;color:#fff;position:absolute;right:5px;top:5px;font-size:12px;text-align:center}

	.product_title{width:90%;margin:15px auto 5px auto;position:relative}
	.product_title h4{font-weight:normal;font-size:14px;color:#999;padding-left:70px}
	.product_title h2{font-weight:bold;font-size:20px;margin-top:15px}
	
	.product_title h4 font{padding:2px 3px;line-height:18px;font-size:12px;color:#fff;border-radius:2px;margin-right:5px;vertical-align:middle}
	.product_title h4 font.f1{background-color:#fc8982}
	.product_title h4 font.f2{background-color:#fac177}
	.product_title h4 font.f3{background-color:#8bd3a2}
	.product_title h4 span{vertical-align:middle}
	.product_title em{display:inline-block;float:right}
	
	.product_title .pricebox{margin-top:5px;color:#999;font-size:15px;line-height:20px;clear:both;overflow:auto}
	.product_title .pricebox .price{float:left;margin-right:20px}
	.product_title .pricebox .price b{color:#F7564D;font-family:Arial;font-size:20px}
    .product_title .pricebox .price2{float:left;text-decoration:line-through}
	
	.product_title .address{width:100%;margin:15px auto;padding-top:10px;line-height:200%;border-top:1px #f6f6f6 solid}
	.product_title .address a{display:block;color:#aaa}
	.product_title .address i{margin-right:5px;width:20px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
	
	.product_detail .C{font-size:16px;line-height:200%;padding:20px 20px 80px 20px}
	.product_detail .C img{width:95%;margin:10px auto;display:block}
    </style>
    <i class="ico goback Ugoback" id="ZEAIGOBACK-product_detail">&#xe602;</i>
    <div class="submain product_detail">
        <div class="banner">
            <img src="<?php echo $path_b_url;?>">
            <img src="<?php echo $photo_s_url;?>" class="logo">
            <span class="kind"><?php echo $kindtitle;?></span>
        </div>
        <div class="product_title">
            <h4 id="enterstore"><font class="f<?php echo $kind;?>"><?php echo $kind_str;?></font><span><?php echo $title_store;?></span><em><span class="ico">&#xe643;</span> <span><?php echo $click; ?></span></em></h4>
        	<h2><?php echo $title;?></h2>
            <div class="pricebox">
                <div class="price">现价 ￥<b><?php echo $price;?></b></div>
                <div class="price2">原价: ￥<?php echo $price2;?></div>
            </div>
            <div class="address">
                <?php if (!empty($address)){?><a><i class="ico S16" style="margin-left:-2px">&#xe614;</i><?php echo $address;?></a><?php }?>
                <?php if (!empty($tel)){?><a href="tel:<?php echo $tel;?>"><i class="ico">&#xe60e;</i><?php echo $tel;?></a><?php }?>
                <?php if (!empty($weixin)){?><a><i class="ico">&#xe607;</i><?php echo $weixin;?></a><?php }?>
                <?php if (!empty($qq)){?><a><i class="ico">&#xe612;</i><?php echo $qq;?></a><?php }?>
                <?php if ($kind == 2 && !empty($worktime)){?><a><i class="ico">&#xe634;</i><?php echo $worktime;?></a><?php }?>
            </div>
        </div>
        <div class="C"><?php echo $content;?></div>
        <script>
		enterstore.onclick=function(){
			o('ZEAIGOBACK-product_detail').click();
			setTimeout(function(){
				page({g:HOST+'/m1/store_detail.php?e=store_kind<?php echo $kind;?>&a='+<?php echo $tg_uid;?>,l:'store_kind<?php echo $kind;?>'});
			},300);
		}
        </script>
	</div>
	<?php
	exit;
}
if (!ifint($a))json_exit(array('flag'=>0,'msg'=>'信息不存在'));
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
$rt = $db->query("SELECT * FROM ".__TBL_TG_USER__." WHERE flag=1 AND id=".$a);
if($db->num_rows($rt)){
	$rows = $db->fetch_array($rt,'name');
	$id            = $rows['id'];
	$tg_uid        = $id;
	$photo_s       = $rows['photo_s'];
	$areatitle     = $rows['areatitle'];
	$area_s_title  = explode(' ',$areatitle);$area_s_title = $area_s_title[1];
	$title         = dataIO($rows['title'],'out');
	$uname         = dataIO($rows['uname'],'out');
	$kind          = $rows['kind'];
	$grade         = $rows['grade'];
	$click         = $rows['click'];
	$gradetitle   = $rows['gradetitle'];
	$title       = (empty($title))?$uname:$title;
	$title       = (empty($title))?$tg_uid:$title;
	$content     = dataIO($rows['content'],'out');
	$job         = dataIO($rows['job'],'out');
	
	$address  = dataIO($rows['address'],'out');
	$worktime = dataIO($rows['worktime'],'out');
	$tel = dataIO($rows['tel'],'out');
	$noP=($kind==1)?'noP.gif?'.$_ZEAI['cache_str']:'noTGbanner.jpg?'.$_ZEAI['cache_str'];
	$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'b'):HOST.'/res/'.$noP;
	$job_str = (!empty($job))?'（'.$job.'）':'';
	if(empty($submitok))$db->query("UPDATE ".__TBL_TG_USER__." SET click=click+1 WHERE id=".$a);
} else {json_exit(array('flag'=>0,'msg'=>'信息不存在'));}


switch ($submitok) {
	case 'store_detail_kind1sex':
		function store_detail_kindsex($tg_uid,$sex) {
			global $db,$_ZEAI;
			if($sex==1 || $sex==2)$SQL=" AND sex=".$sex;
			$rt=$db->query("SELECT id,nickname,sex,grade,photo_s,photo_f FROM ".__TBL_USER__." WHERE tguid=".$tg_uid." AND flag=1 ".$SQL." ORDER BY id DESC LIMIT 100");
			$echo = '';
			$total = $db->num_rows($rt);
			$sex_str=($sex==1)?'男':'女';
			if($total>0){
				for($i=1;$i<=$total;$i++){
					$rows = $db->fetch_array($rt,'name');
					$uid      = $rows['id'];
					$sex      = $rows['sex'];
					$photo_s  = $rows['photo_s'];
					$photo_f  = $rows['photo_f'];
					$grade    = $rows['grade'];
					$nickname = dataIO($rows['nickname'],'out');
					$nickname = (empty($nickname))?'uid:'.$uid:$nickname;
					$photo_s_url= (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
					$echo .= '<li onClick="STK1uA('.$uid.');">';
					$echo .='<img src='.$photo_s_url.' class="photo_s">';
					$echo .= '<span>'.$nickname.'</span>';/*.uicon($sex.$grade)*/
					$echo .= '</li>';
				}
			}else{
				$echo = "<div class='nodatatips'><i class='ico'>&#xe61f;</i>暂时还没有".$sex_str."单身会员~~</div>";
			}
			return $echo;
		}	
		exit(store_detail_kindsex($tg_uid,$sex));
	break;
	case 'store_detail_kind2aboutus':
		if(empty($content))$content=$nodatatips;
		exit('<div class="aboutus">'.$content.'</div>');
	break;
	case 'store_detail_kind2product':
        $rtP = $db->query("SELECT id,title,path_s,price,click,kindtitle FROM ".__TBL_TG_PRODUCT__." WHERE tg_uid=".$tg_uid." AND flag=1 ORDER BY px DESC,id DESC LIMIT 50");
        $totalP = $db->num_rows($rtP);
        if($totalP > 0){?>
            <ul class="xglist">
                <?php	
                for($j=1;$j<=$totalP;$j++){
                    $rowsP = $db->fetch_array($rtP,'name');
                    if(!$rowsP) break;
                    $pid    = $rowsP['id'];
                    $ptitle = $rowsP['title'];
                    $path_s = $rowsP['path_s'];
                    $price  = $rowsP['price'];
                    $price=str_replace(".00","",$price);
                    $click     = $rowsP['click'];
                    $kindtitle = $rowsP['kindtitle'];
                    if(!empty($path_s)){
                        $path_s_url2 = $_ZEAI['up2'].'/'.smb($path_s,'b');
                    }else{
                        $path_s_url2=HOST."/res/noP.gif?".$_ZEAI['cache_str'];
                    }
					$path_s_str = (!empty($path_s))?'<img src="'.$path_s_url2.'">':'<img src="'.$path_s_url2.'" class="no">';
                    ?>
                <li onClick="product_detailA(<?php echo $pid;?>)">
                    <p><?php echo $path_s_str;?><span class="kind"><?php echo $kindtitle;?></span></p>
                    <h2><?php echo $ptitle; ?></h2>
                    <em><font><?php echo $price; ?></font><i><span class="ico">&#xe643;</span> <?php echo $click; ?></i></em>
                </li>
                <?php }?>
            </ul>
        <?php }else{echo $nodatatips;}
		exit;
	break;
	case 'store_tgu_ewm':
		if(ifint($cook_uid))json_exit(array('flag'=>1,'qrcode_url'=>HOST.'/?z=store&a='.$tg_uid));
//		if(is_weixin()){
//			$token = wx_get_access_token();
//			if(str_len($token) < 50)json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_token'));
//			$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
//			$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"www_ZEAI_cn_tg_'.$tg_uid.'"}}}';
//			$ticket      = Zeai_POST_stream($ticket_url,$ticket_data);
//			$T           = json_decode($ticket,true);
//			$qrcode_url  = 'http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
//		}else{
			$qrcode_url = HOST.'/m1/reg.php?tguid='.$tg_uid;
//		}
		json_exit(array('flag'=>1,'qrcode_url'=>$qrcode_url));
	break;
	case 'ajax_gz':
		//if(!ifint($cook_uid))json_exit(array('flag'=>0,'msg'=>'请登录后再操作'));
		$$rtn='json';$chk_u_jumpurl=HOST.'/?z=store&a='.$tg_uid;
		require_once ZEAI.'my_chk_u.php';
		
		$F = 1;$C = '关注成功！';
		$row = $db->ROW(__TBL_TG_GZ__,"flag","tg_uid=".$tg_uid." AND senduid=".$cook_uid,"num");
		if($row[0] == 1){
			$db->query("DELETE FROM ".__TBL_TG_GZ__." WHERE tg_uid=".$tg_uid." AND senduid=".$cook_uid);
			$F = 0;
			$C = '取消成功！';
			
			$gzclass='';
			$gz_str='<i class="ico">&#xe620;</i> 加关注';
		}elseif($row[0] == 0){
			$db->query("INSERT INTO ".__TBL_TG_GZ__."(tg_uid,senduid,px) VALUES ($tg_uid,$cook_uid,".ADDTIME.")");

			$gzclass=' ed';
			$gz_str='<i class="ico">&#xe604;</i> 取消关注';

		}

		$rt2=$db->query("SELECT U.sex,U.photo_s,U.photo_f,U.id FROM ".__TBL_USER__." U,".__TBL_TG_GZ__." b WHERE U.id=b.senduid AND b.tg_uid=".$tg_uid." ORDER BY b.id DESC LIMIT 6");
		$total2 = $db->num_rows($rt2);
		if ($total2 == 0) {
			$ubox =  '<div class="fs">Ta还没有会员关注！<button id="store_gzbtn" onclick="store_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
		} else {
			$ubox = '<div class="fs""><a '.$href.'>';
			for($ii=1;$ii<=$total2;$ii++) {
				$rows2 = $db->fetch_array($rt2,'name');
				if(!$rows2) break;
				$sex      = $rows2['sex'];
				$photo_s2  = $rows2['photo_s'];
				$photo_f  = $rows2['photo_f'];
				$photo_s2_url = (!empty($photo_s2) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_s'.$sex.'.png';
				$ubox .= '<span><img src="'.$photo_s2_url.'" alit='.$rows2['id'].'></span>';
			}
			$ubox .= '</a><button id="store_gzbtn" onclick="store_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
		}
		json_exit(array('flag'=>$F,'msg'=>$C,'list'=>dataIO($ubox,'in')));
	break;
}
?>
<link href="<?php echo HOST;?>/m1/css/store.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />

<?php if ($e == 'store_kind1'){
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-store_kind1">&#xe602;</i>'.$TG_set['tgytitle'];
    $mini_class = 'top_mini top_mini_kind1';
	$mini_backT = '';
    require_once ZEAI.'m1/top_mini.php';
	$area_s_title  = explode(' ',$areatitle);$area_s_title = $area_s_title[1];
	$area_str      = (empty($area_s_title))?'':'　来自：'.$area_s_title;
	$photo_s_str = (!empty($photo_s))?'<img src="'.$photo_s_url.'">':'<img src="'.$photo_s_url.'" class="no">';
	$unum1 = $db->COUNT(__TBL_USER__,"flag=1 AND sex=1 AND tguid=".$tg_uid);
	$unum2 = $db->COUNT(__TBL_USER__,"flag=1 AND sex=2 AND tguid=".$tg_uid);
	$unum3 = $db->COUNT(__TBL_TG_GZ__,"tg_uid=".$tg_uid);
	?>
    <div class="submain store_detail store_kind1">
		<div class="tg_user">
        	<dt><?php echo $photo_s_str;?></dt>
            <dd>
            	<h3><span><?php echo $title;?></span></h3>
                <h4>ID：<?php echo $tg_uid;?><?php echo $area_str;?></h4>
                <h4><?php echo $gradetitle.$job_str;?></h4>
            </dd>
        	<em id="store_tgu_ewm"><i class="ico">&#xe611;</i></em>
        </div>
        <div class="fsbox" id="fsbox">
            <?php 
			$gzclass='';
			$gz_str='<i class="ico">&#xe620;</i> 加关注';
			if(ifint($cook_uid)){
				$row = $db->ROW(__TBL_TG_GZ__,"flag","tg_uid=".$tg_uid." AND senduid=".$cook_uid,"num");
				if($row[0] == 1){
					$gzclass=' ed';
					$gz_str='<i class="ico">&#xe604;</i> 取消关注';
				}else{
					$gzclass='';
					$gz_str='<i class="ico">&#xe620;</i> 加关注';
				}
			}
			$rt2=$db->query("SELECT U.sex,U.photo_s,U.photo_f,U.id FROM ".__TBL_USER__." U,".__TBL_TG_GZ__." b WHERE U.id=b.senduid AND b.tg_uid=".$tg_uid." ORDER BY b.id DESC LIMIT 6");
            $total2 = $db->num_rows($rt2);
            if ($total2 == 0) {
                $ubox =  '<div class="fs">Ta还没有会员关注！<button id="store_gzbtn" onclick="store_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
            } else {
                $ubox = '<div class="fs""><a '.$href.'>';
                for($ii=1;$ii<=$total2;$ii++) {
                    $rows2 = $db->fetch_array($rt2,'name');
                    if(!$rows2) break;
                    $sex      = $rows2['sex'];
                    $photo_s2  = $rows2['photo_s'];
                    $photo_f  = $rows2['photo_f'];
                    $photo_s2_url = (!empty($photo_s2) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_s'.$sex.'.png';
                    $ubox .= '<span><img src="'.$photo_s2_url.'" alit='.$rows2['id'].'></span>';
                }
                $ubox .= '</a><button id="store_gzbtn" onclick="store_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
            }
            echo $ubox;
            ?>
        </div>
    	<div class="kind1nav">
            <a><i class="ico nav1" onClick="store_detail_kind1sex1btn.click();">&#xe60c;</i><span><b style="color:#7AC5F0"><?php echo $unum1;?></b>单身男</span></a>
            <a><i class="ico nav2" onClick="store_detail_kind1sex2btn.click();">&#xe95d;</i><span><b style="color:#F58CB7"><?php echo $unum2;?></b>单身女</span></a>
            <a><i class="ico nav3">&#xe603;</i><span><b style="color:#b1a1e8" id="store_gznum"><?php echo $unum3;?></b>粉丝数</span></a>
            <a><i class="ico nav4">&#xe643;</i><span><b style="color:#FF9F70"><?php echo $click;?></b>围观数</span></a>
        </div>
        <div class="tabmenuBox">
            <div class="tabmenu tabmenu_2 tabmenuStore" id="store_detail_kind1_nav">
                <li data="<?php echo HOST;?>/m1/store_detail.php?a=<?php echo $a;?>&submitok=store_detail_kind1sex&sex=1" id="store_detail_kind1sex1btn" class="ed"><span>单身男</span></li>
                <li data="<?php echo HOST;?>/m1/store_detail.php?a=<?php echo $a;?>&submitok=store_detail_kind1sex&sex=2" id="store_detail_kind1sex2btn"><span>单身女<?php echo $bmnum_str;?></span></li>
                <i></i>
            </div>
        </div>
        <ul class="dsnv" id="store_kind1_ubox"></ul>
        <div id="store_tgu_ewmBox" class="my-subscribe_box" style="display:none"><img id="store_tgu_ewm_img"><h3>长按二维码关注加入Ta的单身团</h3></div>
        <script>
		var a=<?php echo $a;?>;
        ZeaiM.tabmenu.init({showbox:store_kind1_ubox,obj:store_detail_kind1_nav});
        setTimeout(function(){store_detail_kind1sex2btn.click();},100);
		function STK1uA(uid){page({g:HOST+'/m1/u.php?uid='+uid,y:'store_kind1',l:'u'});}
		store_tgu_ewm.onclick=function(){
			ZeaiM.div({obj:store_tgu_ewmBox,w:260,h:260});
			zeai.ajax({url:HOST+'/m1/store_detail'+zeai.ajxext+'submitok=store_tgu_ewm&a=<?php echo $a;?>'},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){store_tgu_ewm_img.src=HOST+'/sub/creat_ewm.php?url='+rs.qrcode_url}else{zeai.msg(0);zeai.msg(rs.msg);}
			});
		}
        </script>
    </div>

<?php exit;}elseif($e == 'store_kind2'){
	if($kind==1)json_exit(array('flag'=>0,'msg'=>'会员类型好像有问题'));
	$photo_b_url = $photo_s_url;
	switch ($kind) {
		case 1:$kind_str='公益红娘';break;
		case 2:$kind_str='商户';$p_str='商品';break;
		case 3:$kind_str='机构';$p_str='服务';break;
	}
	$unum1 = $db->COUNT(__TBL_USER__,"flag=1 AND sex=1 AND tguid=".$tg_uid);
	$unum2 = $db->COUNT(__TBL_USER__,"flag=1 AND sex=2 AND tguid=".$tg_uid);
	$unum3 = $db->COUNT(__TBL_TG_GZ__,"tg_uid=".$tg_uid);
	?>
    <i class="ico goback Ugoback" id="ZEAIGOBACK-store_kind2">&#xe602;</i>
	<style>
    .store_detail .fsbox{width:88%;border-bottom:0;padding:5px 0 0}
    .store_detail .tabmenuStore{top:590px}
    .store_detail .tabmenuStore i{max-width:24px;margin-left:20px;background:#F7564D;border-radius:0}
	#store_kind2_box img{width:95%;margin:10px auto;display:block}

    </style>
    <div class="submain store_detail store_kind2">
        <div class="banner">
        	<img src="<?php echo $photo_b_url;?>" class="banner">
        	<em id="store_tgu_ewm"><i class="ico">&#xe611;</i></em>
        </div>
    	<h3 class="store_title">
        	<font class="f<?php echo $kind;?>"><?php echo $kind_str;?></font><span><?php echo $title;?></span>
        </h3>
    	<div class="address">
        	<?php if (!empty($address)){?><a><i class="ico S16" style="margin-left:-2px">&#xe614;</i><?php echo $address;?></a><?php }?>
            <?php if (!empty($tel)){?><a href="tel:<?php echo $tel;?>"><i class="ico">&#xe60e;</i><?php echo $tel;?></a><?php }?>
            <?php if ($kind == 2 && !empty($worktime)){?><a><i class="ico">&#xe634;</i><?php echo $worktime;?></a><?php }?>
        </div>
        <div class="fsbox" id="fsbox">
            <?php 
			$gzclass='';
			$gz_str='<i class="ico">&#xe620;</i> 加关注';
			if(ifint($cook_uid)){
				$row = $db->ROW(__TBL_TG_GZ__,"flag","tg_uid=".$tg_uid." AND senduid=".$cook_uid,"num");
				if($row[0] == 1){
					$gzclass=' ed';
					$gz_str='<i class="ico">&#xe604;</i> 取消关注';
				}else{
					$gzclass='';
					$gz_str='<i class="ico">&#xe620;</i> 加关注';
				}
			}
			$rt2=$db->query("SELECT U.sex,U.photo_s,U.photo_f,U.id FROM ".__TBL_USER__." U,".__TBL_TG_GZ__." b WHERE U.id=b.senduid AND b.tg_uid=".$tg_uid." ORDER BY b.id DESC LIMIT 6");
            $total2 = $db->num_rows($rt2);
            if ($total2 == 0) {
                $ubox =  '<div class="fs">Ta还没有推荐单身团会员！<button id="store_gzbtn" onclick="store_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
            } else {
                $ubox = '<div class="fs""><a '.$href.'>';
                for($ii=1;$ii<=$total2;$ii++) {
                    $rows2 = $db->fetch_array($rt2,'name');
                    if(!$rows2) break;
                    $sex      = $rows2['sex'];
                    $photo_s2  = $rows2['photo_s'];
                    $photo_f  = $rows2['photo_f'];
                    $photo_s2_url = (!empty($photo_s2) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_s'.$sex.'.png';
                    $ubox .= '<span><img src="'.$photo_s2_url.'" alit='.$rows2['id'].'></span>';
                }
                $ubox .= '</a><button id="store_gzbtn" onclick="store_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
            }
            echo $ubox;
            ?>
        </div>
        <div class="clear"></div>
    	<div class="kind1nav">
            <a><i class="ico nav1">&#xe60c;</i><span><b style="color:#7AC5F0"><?php echo $unum1;?></b>单身男</span></a>
            <a><i class="ico nav2">&#xe95d;</i><span><b style="color:#F58CB7"><?php echo $unum2;?></b>单身女</span></a>
            <a><i class="ico nav3">&#xe603;</i><span><b style="color:#b1a1e8" id="store_gznum"><?php echo $unum3;?></b>粉丝数</span></a>
            <a><i class="ico nav4">&#xe643;</i><span><b style="color:#FF9F70"><?php echo $click;?></b>围观数</span></a>
        </div>
        <div class="tabmenuBox">
            <div class="tabmenu tabmenu_2 tabmenuStore" id="store_detail_kind2_nav" style="top:0">
                <li data="<?php echo HOST;?>/m1/store_detail.php?a=<?php echo $a;?>&submitok=store_detail_kind2aboutus" id="store_detail_kind2aboutus_btn" class="ed"><span><?php echo $kind_str;?>简介</span></li>
                <li data="<?php echo HOST;?>/m1/store_detail.php?a=<?php echo $a;?>&submitok=store_detail_kind2product" id="store_detail_kind2product_btn"><span><?php echo $p_str;?>展示</span></li>
                <i></i>
            </div>
        </div>
        <ul id="store_kind2_box"></ul>
        
        <div id="store_tgu_ewmBox" class="my-subscribe_box" style="display:none"><img id="store_tgu_ewm_img"><h3>长按二维码关注加入Ta的单身团</h3></div>
        <script>
		var a=<?php echo $a;?>;
        ZeaiM.tabmenu.init({showbox:store_kind2_box,obj:store_detail_kind2_nav});
        setTimeout(function(){store_detail_kind2product_btn.click();},100);
		function product_detailA(id){page({g:HOST+'/m1/store_detail.php?id='+id,y:'store_kind2',l:'product_detail'});}
		store_tgu_ewm.onclick=function(){
			ZeaiM.div({obj:store_tgu_ewmBox,w:260,h:260});
			zeai.ajax({url:HOST+'/m1/store_detail'+zeai.ajxext+'submitok=store_tgu_ewm&a=<?php echo $a;?>'},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){store_tgu_ewm_img.src=HOST+'/sub/creat_ewm.php?url='+rs.qrcode_url}else{zeai.msg(0);zeai.msg(rs.msg);}
			});
		}
        </script>
    </div>
<?php }?>
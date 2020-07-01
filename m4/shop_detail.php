<?php
require_once '../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_shop.php';
$navtop = json_decode($_SHOP['navtop'],true);
$navbtm = json_decode($_SHOP['navbtm'],true);
$up2 = $_ZEAI['up2'].'/';
$_ZEAI['pagesize']=6;
$nav = 'shop_index';
$nodatatips="<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无信息</div>";
//
if($t != 2){
	$ZEAI_SQL = "flag=1 AND path_s<>'' AND tg_uid=".$id;
	$ZEAI_SELECT="SELECT id,title,path_s,price,click,kindtitle,fahuokind,url FROM ".__TBL_TG_PRODUCT__." WHERE ".$ZEAI_SQL." ORDER BY px DESC,id DESC";
	if($submitok=='ZEAI_list'){
		exit(ajax_list_fn($ZEAI_totalP,$p));
	}
	$ZEAI_total = $db->COUNT(__TBL_TG_PRODUCT__,$ZEAI_SQL);
	$ZEAI_totalP= ceil($ZEAI_total/$_ZEAI['pagesize']);
}
//
if (!ifint($id))alert('信息不存在','shop_index.php');
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容</div>";
$rt = $db->query("SELECT * FROM ".__TBL_TG_USER__." WHERE shopflag=1 AND id=".$id);
if($db->num_rows($rt)){
	$rows = $db->fetch_array($rt,'name');
	$id            = $rows['id'];
	$tg_uid        = $id;
	$photo_s       = $rows['photo_s'];
	$piclist       = $rows['piclist'];
	$areatitle     = $rows['areatitle'];
	$area_s_title  = explode(' ',$areatitle);$area_s_title = $area_s_title[1];
	$title         = dataIO($rows['title'],'out');
	$uname         = dataIO($rows['uname'],'out');
	$kind          = $rows['kind'];
	$grade         = $rows['grade'];
	$gradetitle   = $rows['gradetitle'];
	$weixin_ewm   = $rows['weixin_ewm'];
	$title       = (empty($title))?$uname:$title;
	$title       = (empty($title))?$tg_uid:$title;
	$content     = dataIO($rows['content'],'out');
	$job         = dataIO($rows['job'],'out');
	$address  = dataIO($rows['address'],'out');
	$worktime = dataIO($rows['worktime'],'out');
	$tel = dataIO($rows['tel'],'out');
	$openid    = $rows['openid'];
	$subscribe = $rows['subscribe'];
	$longitude = $rows['longitude'];
	$latitude  = $rows['latitude'];
	$noP=($kind==1)?'noP.gif?'.$_ZEAI['cache_str']:'noTGbanner.jpg?'.$_ZEAI['cache_str'];
	$photo_s_url = (!empty($photo_s))?$_ZEAI['up2'].'/'.smb($photo_s,'b'):HOST.'/res/'.$noP;
	$job_str = (!empty($job))?'（'.$job.'）':'';
	if(empty($submitok))$db->query("UPDATE ".__TBL_TG_USER__." SET click=click+1 WHERE id=".$id);
} else {alert('信息不存在或已隐藏','shop_index.php');}

switch ($submitok) {
	case 'yuyue_addupdate':
		$truename=dataIO($truename,'in',20);
		$mob=dataIO($mob,'in',20);
		$bz=dataIO($form_bz,'in',200);
		$ddtime=dataIO($form_ddtime,'in',100);
		if(empty($truename) || empty($mob))json_exit(array('flag'=>0,'msg'=>'请输入姓名和联系方法'));
		$cook_uid    = intval($cook_uid);
		$cook_tg_uid = intval($cook_tg_uid);
		$row = $db->ROW(__TBL_SHOP_YUYUE__,"flag","cid=".$id." AND ((uid=".$cook_uid." OR tg_uid=".$cook_tg_uid.") && mob='".$mob."')","num");
		if($row){
			json_exit(array('flag'=>1,'msg'=>'您已经提交了预约信息，我们会尽快联系您'));
		}elseif($row[0] == 0){
			$db->query("INSERT INTO ".__TBL_SHOP_YUYUE__."(cid,uid,tg_uid,truename,mob,addtime,bz,ddtime) VALUES ($id,$cook_uid,$cook_tg_uid,'$truename','$mob',".ADDTIME.",'$bz','$ddtime')");
			//通卖
			$cid=$id;$cname=$title;
			//站内
			$C = '客户到店预约（客户ID:'.$cook_tg_uid.'）';//　　<a href='.Href('my').' class=aQING>查看详情</a>
			$db->SendTip($cid,$C,dataIO($C,'in'),'shop');
			//微信
			if (!empty($openid) && $subscribe==1){
				$keyword1 = urlencode($C);
				$keyword3 = urlencode($_ZEAI['siteName']);
				$url      = urlencode(HOST.'/m4/shop_my_yuyue_adm.php');
				@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
			}
			json_exit(array('flag'=>1,'msg'=>'预约成功，我们会尽快联系您'));
		}
	break;
	case 'shop_detail_kind1sex':
		function shop_detail_kindsex($tg_uid,$sex) {
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
		exit(shop_detail_kindsex($tg_uid,$sex));
	break;
	case 'shop_detail_kind2product':
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
	case 'shop_tgu_ewm':
		//if(ifint($cook_uid))json_exit(array('flag'=>1,'qrcode_url'=>HOST.'/m4/shop_detail.php?id='.$id));
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
		if(!ifint($cook_tg_uid)){
			$jumpurl=HOST.'/m1/tg_login.php?loginkind=shop&jumpurl='.urlencode(HOST.'/m4/shop_detail.php?id='.$tg_uid.'&tguid='.$tguid);
			json_exit(array('flag'=>'nologin_tg','tguid'=>$tguid,'msg'=>'请登录后再操作','jumpurl'=>$jumpurl));
		}
		$F = 1;$C = '收藏成功！';
		$row = $db->ROW(__TBL_SHOP_FAV__,"id","favid=".$tg_uid." AND kind=1 AND tg_uid=".$cook_tg_uid,"num");
		if($row){
			$db->query("DELETE FROM ".__TBL_SHOP_FAV__." WHERE favid=".$tg_uid."  AND kind=1 AND tg_uid=".$cook_tg_uid);
			$F = 0;
			$C = '取消成功！';
			$gzclass='';
			$gz_str='<i class="ico">&#xe620;</i> 收藏';
		}else{
			$db->query("INSERT INTO ".__TBL_SHOP_FAV__."(favid,tg_uid,addtime,kind) VALUES ($tg_uid,$cook_tg_uid,".ADDTIME.",1)");
			$gzclass=' ed';
			$gz_str='<i class="ico">&#xe604;</i> 取消收藏';
		}
		$rt2=$db->query("SELECT U.photo_s,U.id FROM ".__TBL_TG_USER__." U,".__TBL_SHOP_FAV__." b WHERE U.id=b.tg_uid AND b.favid=".$tg_uid." AND b.kind=1 ORDER BY b.id DESC LIMIT 6");
		$total2 = $db->num_rows($rt2);
		if ($total2 == 0) {
			$ubox =  '<div class="fs">暂无用户收藏！<button id="shop_gzbtn" onclick="shop_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
		} else {
			$ubox = '<div class="fs""><a '.$href.'>';
			for($ii=1;$ii<=$total2;$ii++) {
				$rows2 = $db->fetch_array($rt2,'name');
				if(!$rows2) break;
				$photo_s2  = $rows2['photo_s'];
				$photo_s2_url = (!empty($photo_s2))?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_m.jpg';
				$ubox .= '<span><img src="'.$photo_s2_url.'" alit='.$rows2['id'].'></span>';
			}
			$ubox .= '</a><button id="shop_gzbtn" onclick="shop_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
		}
		json_exit(array('flag'=>$F,'msg'=>$C,'list'=>dataIO($ubox,'in')));
	break;
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $title;?></title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop_detail.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php
$photo_b_url = $photo_s_url;
switch ($kind) {
	case 1:$kind_str='个人';$p_str='商品';break;
	case 2:$kind_str='公司';$p_str='商品';break;
	case 3:$kind_str='机构';$p_str='服务';break;
}
?>
<i class="ico goback Ugoback" onClick="zeai.back('<?php echo HOST.'/m4/shop_index.php';?>');">&#xe602;</i>
<div class="shop_detail">
	<div class="banner">
		<img src="<?php echo $photo_b_url;?>" class="banner">
		<em id="shop_tgu_ewm"><i class="ico">&#xe611;</i></em>
	</div>
	<h3 class="shop_title">
		<font class="f<?php echo $kind;?>"><?php echo $kind_str;?></font><span><?php echo $title;?></span>
	</h3>
	<div class="address">
		<?php if (!empty($address)){?><a><i class="ico S16" style="margin-left:-2px">&#xe614;</i><?php echo $address;if(!empty($longitude) && !empty($latitude)){?>
        <span onclick="openmap(<?php echo $longitude;?>,<?php echo $latitude;?>,'<?php echo $title;?>','<?php echo $address;?>')"><i class="ico icomap">&#xe614;</i><font>地图直达</font></span>
        <?php }?>
        </a>
        <?php
		}if (!empty($tel)){?><a href="tel:<?php echo $tel;?>"><i class="ico">&#xe60e;</i><?php echo $tel;?></a><?php
		}if ($kind == 2 && !empty($worktime)){?><a><i class="ico">&#xe634;</i>营业时间：<?php echo $worktime;?></a><?php }?>
	</div>
    <div class="fsbox" id="fsbox">
        <?php 
        $gzclass='';
        $gz_str='<i class="ico">&#xe620;</i> 收藏';
        if(ifint($cook_tg_uid)){
            $row = $db->ROW(__TBL_SHOP_FAV__,"id","favid=".$tg_uid." AND kind=1 AND tg_uid=".$cook_tg_uid,"num");
            if($row[0]){
                $gzclass=' ed';
                $gz_str='<i class="ico">&#xe604;</i> 取消收藏';
            }else{
                $gzclass='';
                $gz_str='<i class="ico">&#xe620;</i> 收藏';
            }
        }
        $rt2=$db->query("SELECT U.photo_s,U.id FROM ".__TBL_TG_USER__." U,".__TBL_SHOP_FAV__." b WHERE U.id=b.tg_uid AND b.favid=".$tg_uid." AND b.kind=1 ORDER BY b.id DESC LIMIT 6");
        $total2 = $db->num_rows($rt2);
        if ($total2 == 0) {
            $ubox =  '<div class="fs">暂无用户收藏！<button id="shop_gzbtn" onclick="shop_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
        } else {
            $ubox = '<div class="fs""><a '.$href.'>';
            for($ii=1;$ii<=$total2;$ii++) {
                $rows2 = $db->fetch_array($rt2,'name');
                if(!$rows2) break;
                $photo_s2  = $rows2['photo_s'];
				$photo_s2_url = (!empty($photo_s2))?$_ZEAI['up2'].'/'.$photo_s2:HOST.'/res/photo_m.jpg';
                $ubox .= '<span><img src="'.$photo_s2_url.'" alit='.$rows2['id'].'></span>';
            }
            $ubox .= '</a><button id="shop_gzbtn" onclick="shop_gzFn();" class="btn size2 yuan gz'.$gzclass.'">'.$gz_str.'</button></div>';
        }
        echo $ubox;
        ?>
    </div>
	<div class="tab flex" style="">
		<a href="<?php echo SELF;?>?t=1&id=<?php echo $id;?>"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>><?php echo $p_str;?>展示</a>
		<a href="<?php echo SELF;?>?t=2&id=<?php echo $id;?>"<?php echo ($t==2)?' class="ed"':'';?>><?php echo $_SHOP['title'];?>简介</a>
	</div>
	<?php if ($t == 2){?>
        <div class="aboutus"><?php
		if(empty($content))$content=$nodatatips;
		echo $content;
		if(!empty($piclist)){
			$ARR=explode(',',$piclist);
			$ln=count($ARR);
			if($ln>=0){
				$picli='<div class="piclist">';
				foreach ($ARR as $V) {$picli.='<img src="'.$_ZEAI['up2'].'/'.smb($V,'b').'">';}
				$picli.='</div>';
				echo $picli;
			}
		}
		?></div>
    <?php }else{ ?>
        <div class="list" id="ZEAI_list"><?php if($ZEAI_totalP>0){echo ajax_list_fn($ZEAI_totalP,$p);}else{echo $nodatatips;}?></div>
    <?php }?>
	<div id="shop_tgu_ewmBox" class="my-subscribe_box" style="display:none"><img id="shop_tgu_ewm_img"><h3>长按二维码关注加入Ta的单身团</h3></div>
    <div style="height:100px"></div>
</div>
<?php $weixin_ewm_url=(empty($weixin_ewm))?HOST.'/res/noP.gif':$_ZEAI['up2'].'/'.smb($weixin_ewm,'b');?>
<div id="shop_weixin_ewmBox" class="my-subscribe_box" style="display:none"><img src="<?php echo $weixin_ewm_url;?>"><h3>长按二维码添加客服微信</h3></div>
<div class="shop_detail_btm">
	<a href="shop_index.php"><i class="ico2">&#xe74e;</i><span>首页</span></a>
	<a href="tel:<?php echo $tel;?>"><i class="ico2">&#xe7c1;</i><span>电话</span></a>
	<a onClick="ZeaiM.div({obj:shop_weixin_ewmBox,w:260,h:260});"><i class="ico2">&#xe784;</i><span>客服</span></a>
	<a id="shop_yuyue_btn">预约商家</a>
</div>

<div id="shop_yuyue" class="shop_yuyue">
    <h1>到店在线预约</h1>
    <form id="zeai_form">
    <input type="text" name="truename" id="truename" class="input" placeholder="请输入您的姓名" onBlur="rettop();" autocomplete="off" >
    <input type="text" name="mob" id="mob" class="input" placeholder="请输入您的联系电话或微信" onBlur="rettop();" autocomplete="off" >
	<input type="text" name="form_bz" id="form_bz" class="input" placeholder="事由/备注（100字以内）" maxlength="100" value="<?php echo $form_bz;?>" onBlur="rettop();" autocomplete="off" >
	<input type="text" name="form_ddtime" id="form_ddtime" class="input" placeholder="到店时间（如：<?php echo YmdHis(ADDTIME+86400,'Ymd');?>下午2点）" maxlength="100" value="<?php echo $form_ddtime;?>" onBlur="rettop();" autocomplete="off" >
    <input type="hidden" name="submitok" value="yuyue_addupdate">
    <input type="hidden" name="id" value="<?php echo $id;?>">
    <button type="button" class="btn size4 HONG3 yuan" id="yuyueaddbtn">开始预约</button>
    </form>
</div>
<script src="<?php echo HOST;?>/res/m4/js/shop_detail.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script>
var id=<?php echo $id;?>;
shop_tgu_ewm.onclick=function(){
	ZeaiM.div({obj:shop_tgu_ewmBox,w:260,h:260});
	zeai.ajax({url:'shop_detail'+zeai.ajxext+'submitok=shop_tgu_ewm&id=<?php echo $id;?>'},function(e){rs=zeai.jsoneval(e);
		if(rs.flag==1){shop_tgu_ewm_img.src=HOST+'/sub/creat_ewm.php?url='+rs.qrcode_url}else{zeai.msg(0);zeai.msg(rs.msg);}
	});
}
<?php
if ($ZEAI_total > $_ZEAI['pagesize'] && $t != 2){?>
	var ZEAI_totalP = parseInt(<?php echo $ZEAI_totalP; ?>),p=2,id=<?php echo $id;?>;
	document.body.onscroll = indexOnscroll;
<?php }?>
</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,appId: '<?php echo $signPackage["appId"];?>',timestamp: <?php echo $signPackage["timestamp"];?>,nonceStr:'<?php echo $signPackage["nonceStr"];?>',signature: '<?php echo $signPackage["signature"];?>',jsApiList:['hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','getLocation','openLocation']});
	var FX_title = '<?php echo $title;?>',
	FX_desc  = '<?php echo trimhtml($content).'【'.$_ZEAI['siteName'].'】';?>',
	FX_link  = '<?php echo HOST; ?>/m4/shop_detail.php?id=<?php echo $id;?>',
	FX_imgurl= '<?php echo $photo_s_url; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:FX_title,desc:FX_desc,link:FX_link,imgUrl:FX_imgurl});
		wx.onMenuShareTimeline({title:FX_title,link:FX_link,imgUrl:FX_imgurl});
	});
	function openmap (lng,lat,title,address){
		var newgps=b_t(lng,lat);
		lng=parseFloat(newgps[0]);lat=parseFloat(newgps[1]);
		wx.openLocation({
			latitude:lat,
			longitude:lng,
			name:title,
			address:address,
			scale:14,
			infoUrl:'http://weixin.qq.com'
		});
	}
	function b_t(lng,lat) {
		if (lng == null || lng == '' || lat == null || lat == '')return [lng, lat];
		var x_pi = 3.14159265358979324;
		var x = parseFloat(lng) - 0.0065;
		var y = parseFloat(lat) - 0.006;
		var z = Math.sqrt(x * x + y * y) - 0.00002 * Math.sin(y * x_pi);
		var theta = Math.atan2(y, x) - 0.000003 * Math.cos(x * x_pi);
		var lng = (z * Math.cos(theta)).toFixed(7);
		var lat = (z * Math.sin(theta)).toFixed(7);
		return [lng,lat];
	}	
	</script>
<?php }else{?>
	<script>function openmap (lng,lat,title,address){
		zeai.openurl('http://api.map.baidu.com/marker?location='+lat+','+lng+'&title='+address+'&content='+title+'&output=html');
	}</script>
<?php }?>
<div id="backtop"><a href="#top" id="btmTopBtn"><i class="ico">&#xe60a;</i>顶部</a></div>
</body>
</html>
<?php
function ajax_list_fn($totalP,$p) {
	global $_ZEAI,$db,$nodatatips,$ZEAI_SELECT;
	$p = intval($p);if ($p<1)$p=1;$totalP = intval($totalP);
	if($totalP==0 && $p == 1)return $nodatatips;
	if ($p > $totalP)exit("end");
	$LIMIT = ($p == 1)?$_ZEAI['pagesize']:($p-1)*$_ZEAI['pagesize'].",".$_ZEAI['pagesize'];
	$rt = $db->query($ZEAI_SELECT." LIMIT ".$LIMIT);
	$total = $db->num_rows($rt);
	$rows_ulist='';
	for($n=1;$n<=$total;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$rows_ulist .= rows_ulist($rows,$p);
	}
	return $rows_ulist;
}	
function rows_ulist($rows,$p) {
	global $db,$_ZEAI;
	$pid    = $rows['id'];
	$ptitle = trimhtml(dataIO($rows['title'],'out'));
	$path_s = $rows['path_s'];
	$price  = $rows['price'];
	$price=str_replace(".00","",$price);
	$fahuokind = $rows['fahuokind'];
	$fahuokind_str=($fahuokind==2)?'<span class="fahuokind2">线下</span>':'';
	$url = trimhtml(dataIO($rows['url'],'out'));
	$url = (!empty($url))?$url:'shop_goods_detail.php?id='.$pid;
	$click  = $rows['click'];
	if(!empty($path_s)){
		$path_s_url2 = $_ZEAI['up2'].'/'.smb($path_s,'b');
	}else{
		$path_s_url2=HOST."/res/noP.gif?".$_ZEAI['cache_str'];
	}
	$onum = $db->COUNT(__TBL_SHOP_ORDER__,"pid=".$pid);
	$onum_str=($onum>0)?'<span class="onum">销量 '.$onum.'</span>':'';
	$path_s_str = '<img src="'.$path_s_url2.'" class="p'.$p.'">';
	$O = '<a href="'.$url.'">';
	$O.= '<p>'.$path_s_str.$onum_str.'</p>';
	$O.= '<h2>'.$fahuokind_str.$ptitle.'</h2>';
	$O.= '<em><font>'.number_format($price).'</font><i><span class="ico">&#xe643;</span> '.$click.'</i></em>';
	$O.= '</a>';
	return $O;
}
?>
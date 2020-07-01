<?php
require_once '../sub/init.php';
require_once 'chkUadm.php';
if(!ifint($uid))exit("会员UID不存在");
require_once ZEAI.'cache/udata.php';$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); 
$mate_diy = explode(',',$_ZEAI['mate_diy']);
$fields  = "grade,sex,photo_s,photo_f,birthday,areatitle,love,heigh,weigh,edu,pay,house,car,child,blood,pay,blood,job,admid,marrytime,companykind,smoking,drink,marrytime,area2title";
//$fields .= ",mate_age1,mate_age2,mate_heigh1,mate_heigh2,mate_weigh1,mate_weigh2,mate_pay,mate_edu,mate_love,mate_car,mate_house,mate_areaid,mate_areatitle,mate_job,mate_child,mate_marrytime,mate_companykind,mate_smoking,mate_drink,mate_areaid2,mate_areatitle2";
$row = $db->ROW(__TBL_USER__,$fields,"id=".$uid,"name");
if (!$row)exit('会员不存在');
$grade      = $row['grade'];
$sex        = $row['sex'];
$photo_s    = $row['photo_s'];
$photo_f    = $row['photo_f'];
$birthday   = $row['birthday'];
//$birthday   = (!ifdate($birthday))?'':$birthday;
$areatitle  = dataIO($row['areatitle'],'out');
$heigh      = $row['heigh'];
$weigh      = $row['weigh'];
$love       = $row['love'];
$edu        = $row['edu'];
$pay        = $row['pay'];
$job        = $row['job'];
$house      = $row['house'];
$car        = $row['car'];
$child      = $row['child'];
$blood      = $row['blood'];
//
$area2title  = dataIO($row['area2title'],'out');
$admid        = intval($row['admid']);
$marrytime    = $row['marrytime'];
$companykind  = $row['companykind'];
$smoking  = $row['smoking'];
$drink    = $row['drink'];
$area_s_title = explode(' ',$areatitle);$area_s_title = $area_s_title[2];if(empty($area_s_title))$area_s_title = $area_s_title[1];
//$area_s_title2 = explode(' ',$area2title);$area_s_title2 = $area_s_title2[1].$area_s_title2[2];
$area_s_title2 = $area2title;
$sex_str2 = ($sex == 1)?'男':'女';
$marrytime_str=udata('marrytime',$marrytime);
$edu_str = udata('edu',$edu);
$pay_str = udata('pay',$pay);
//
$SQL = ($admid>0)?" AND id=".$admid:" ORDER BY rand() LIMIT 1";
$rowf = $db->ROW(__TBL_CRM_HN__,"id,truename,ewm,mob","ifwebshow=1 AND kind='crm' AND flag=1 ".$SQL,'name');
if ($rowf){
	$hn_ewm   = $rowf['ewm'];
	$kf_wxpic = (!empty($hn_ewm))?$hn_ewm:'';
}
//
$birthday_str  = (!empty($birthday) && $birthday!='0000-00-00')?getage($birthday).'岁':'';
if (!empty($photo_s)){
	$photo_s_url = $_ZEAI['up2']."/".$photo_s;
	$photo_m_url = smb($photo_s_url,'m');
	$photo_m_str = '<img src="'.$photo_m_url.'" class="m">';
	$photo_ewm_s_url = $photo_s_url;
	$photo_b_url = smb($photo_s_url,'b');
}else{
	$photo_m_url = HOST.'/res/photo_m'.$sex.'.png';
	$photo_m_str = '<img src="'.HOST.'/res/photo_m'.$sex.'.png" class="m">';
	$photo_ewm_s_url = HOST.'/res/photo_s'.$sex.'.png';
	$photo_b_url = HOST.'/res/photo_m'.$sex.'.png';
}
$grade_str=($grade>1)?'VIP':'';
$bigextname=($cardid==11 || $cardid==12)?'.png':'.jpg';
$cardid=(!ifint($cardid))?3:$cardid;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/m1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/html2canvas.js"></script>
<script src="<?php echo HOST;?>/res/html2canvas_img.js"></script>
<script src="js/u_card.js?<?php echo $_ZEAI['cache_str'];?>"></script>    
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.cards{margin:20px auto 30px auto}
.cards ul{width:100%;margin:0 auto}
.cards ul li{width:6%;margin:0 1%;text-align:center;float:left;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;background-color:#000;position:relative}
.cards ul li img{width:100%;display:block;padding:5px;border:#ddd 1px solid;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;background-color:#fff}
.cards ul li:hover img,.cards ul li.ed img{filter:alpha(opacity=40);-moz-opacity:0.4;opacity:0.4;cursor:pointer}
.cards ul li i{width:30px;height:30px;line-height:30px;font-size:30px;color:#fff;display:none;position:absolute;bottom:25px;right:13px}
.cards ul li.ed i{display:block}
.my_card_detail{width:750px;height:1200px;margin:30px auto}

.div_pic{position:absolute;z-index:1000}
.div_pic_close{position:absolute;right:-30px;top:-30px;width:50px;height:50px;line-height:50px;border-radius:30px;color:#fff;z-index:2;background-color:#000}
.div_pic_close:hover{color:#FD66B5;cursor:pointer}
.div_pic_close i.ico{font-size:40px;}
.div_pic_div{position:absolute;top:0;left:0;overflow-x:hidden}
@keyframes bounceFn{
	0%, 20%, 40%, 60%, 80%, 100% {-webkit-transition-timing-function:cubic-bezier(0.215,.610,.355,1.000);transition-timing-function: cubic-bezier(0.215,.610,.355,1.000);}
	0% {opacity:0;-webkit-transform: scale3d(.3,.3,.3);transform: scale3d(.3,.3,.3);}
	20% {-webkit-transform: scale3d(1.1,1.1,1.1);transform: scale3d(1.1,1.1,1.1);}
	40% {-webkit-transform: scale3d(.9,.9,.9);transform: scale3d(.9,.9,.9);}
	60% {opacity: 1;-webkit-transform: scale3d(1.03,1.03,1.03);transform: scale3d(1.03,1.03,1.03);}
	80% {-webkit-transform: scale3d(.97,.97,.97);transform: scale3d(.97,.97,.97);}
	100% {opacity: 1;-webkit-transform: scale3d(1,1,1);transform: scale3d(1,1,1);}		
}
.bounce{-webkit-animation-name:bounceFn;animation-name:bounceFn;animation-duration:.75s;animation-fill-mode:both;animation-timing-function: ease;-webkit-animation-duration: .75s;-webkit-animation-fill-mode: both;-webkit-animation-timing-function: ease;}

<?php if($cardid==11){?>
	.cardbox .card{width:750px;height:1200px;position:relative;background:url('<?php echo $photo_b_url;?>');background-size:contain;background-repeat:no-repeat}
	.cardbox .card div{position:absolute;left:0;text-align:center}
	.cardbox .card .mb{position:absolute;left:0;z-index:1;display:block}
	.cardbox .card .sitename{width:100%;text-align:center;font-size:24px}
	
	.cardbox .card div{position:absolute;z-index:1;}
	.cardbox .card .sex11{left:36%;top:54%;font-size:48px;color:#101d84;width:68px;height:68px;background-color:#fff;border:#323D91 6px solid;border-radius:90px}
	.cardbox .card .uid11{left:65%;top:50%;font-size:39px;color:#fff;text-align:center;width:33%;text-shadow:0px 1px 1px #E87B29}
	
	.card .dl{width:100%;height:20%;left:65%;top:62%}
	.card .dl dt{width:30%;height:100%;float:left}
	.card .dl dt .photo_m11{background-repeat:no-repeat;width:70%;height:85%;border:#FEE49A 8px solid;border-radius:25px;margin-left:10%}
	.card .dl dd{width:70%;height:31px;line-height:31px;float:left;margin-top:0}
	
	.card .dl dd li{width:45%;height:50px;line-height:50px;float:left;background-color:#6675E8;border-radius:30px;margin:0 5% 6% 0}
	.card .dl dd li h5,.card .dl dd li span{height:100%;color:#fff;display:block;float:left;border-radius:30px;font-size:32px}
	.card .dl dd li h5{width:45%;background-color:#101D84}
	.card .dl dd li span{width:55%;background-color:#6675E8;padding-right:10px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}

	.card div.dlmore {width:66%;left:3%;top:83%;text-align:left;line-height:30px;text-shadow:0px 1px 1px #E87B29}
	.card div.dlmore li{display:inline;padding:0 20px 0 0;font-size:28px;color:#fff;text-align:left}
	
	.cardbox .card .ewm11{width:30%;left:70%;bottom:1%;text-align:center;font-size:18px;color:#fff}
	.cardbox .card .ewm11 img{width:70%;margin:0 auto;display:block;margin-bottom:3px}
	.cardbox .card .sitename{bottom:1%;color:#ffc}
<?php }elseif($cardid==12){?>
	.cardbox .card{width:750px;height:1200px;position:relative;background:url('<?php echo $photo_b_url;?>');background-size:contain;background-repeat:no-repeat}
	.cardbox .card div{position:absolute;left:0;text-align:center}
	.cardbox .card .mb{position:absolute;left:0;z-index:0;display:block}
	.cardbox .card .sitename{width:100%;text-align:center;font-size:24px}

	.cardbox .card div{position:absolute;z-index:1;}
	.cardbox .card .uid12{left:8%;top:57%;font-size:32px;color:#FF4F5A;text-align:left;width:50vw;text-shadow:0px 2px 2px #fff}
	.card .dl{width:100%;height:20%;left:65%;top:63%}
	.card .dl dd{width:64%;height:50px;line-height:50px;float:left;margin-left:6%}
	.card .dl dd li{width:45%;height:100%;float:left;background-color:#FF7D7D;border-radius:30px;margin:0 5% 4% 0}
	.card .dl dd li h5,.card .dl dd li span{height:100%;color:#fff;display:block;float:left;border-radius:30px;font-size:30px}
	
	.card .dl dd li h5{width:45%;background-color:#950026;text-shadow:0px 0px 0px #000}
	.card .dl dd li span{width:55%;background-color:#FF7D7D;text-shadow:0px 0px 0px #000}
	.card .dl dt{width:30%;height:100%;float:left}
	.card .dl dt .photo_m11{background-repeat:no-repeat;width:72%;height:82%;border:#FF7D7D 6px solid;border-radius:30px;margin-left:3%;margin-top:-14%}

	.card div.dlmore {width:60%;left:8%;top:80%;text-align:left;line-height:36px;text-shadow:0px 3px 3px #fff}
	.card div.dlmore li{display:inline;padding:0 24px 0 0;font-size:26px;color:#000;text-align:left}

	.cardbox .card .ewm12{width:30%;left:68%;bottom:4%;text-align:center;font-size:20px;color:#FF7D7D;line-height:100%;text-shadow:0px 1px 1px #fff}
	.cardbox .card .ewm12 img{width:68%;margin:0 auto;display:block;margin-bottom:3px}
	.cardbox .card .sitename{bottom:3%;color:#FF7D7D;text-shadow:0px 1px 1px #fff}
<?php }else{?>
	.cardbox{height:100%;position:relative;display:flex;align-items:center;}
	.cardbox img{width:100%}
	.cardbox .card{width:100%;position:relative;}
	.cardbox .card .mb{display:block;background-color:#fff}
	.cardbox .card .newm{border-radius:50%;position:absolute;background-size:cover;background-position:center center;background-repeat:no-repeat}/*width:120px;height:120px;left:-webkit-calc(50% - 60px);top:20px;*/
	.cardbox .card div{position:absolute;left:0;text-align:center}
	.cardbox .card .uid{width:100%;font-size:32px;line-height:18px;height:18px;text-align:center}
	.cardbox .card .me,.cardbox .card .you{font-size:32px;/*background-color:#000;*/width:80%;margin:0 auto;left:10%}
	.cardbox .card .me li,.cardbox .card .you li{display:inline-block;padding:0 5px;}
	.cardbox .card .ewm{width:44%;left:28%;text-align:center;font-size:24px}/**/
	.cardbox .card .ewm img{width:40%;margin:0 auto;display:block;margin-bottom:5px;}
	.cardbox .card .sitename{width:100%;text-align:center;font-size:24px}
	.cardbox .card .me li{padding:8px 10px;font-size:32px}
<?php }?>
</style>
<body>
<table width="950" border="0" align="center">
  <tr>
    <td valign="top">
    	<div class="cards">
            <ul id="zeai_xqk">
                <li onClick="zeai.openurl('u_card.php?cardid=11&uid=<?php echo $uid;?>')"<?php echo ($cardid == 11)?' class="ed"':'';?>><img value="11" src="<?php echo HOST;?>/res/my_card/11_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li onClick="zeai.openurl('u_card.php?cardid=12&uid=<?php echo $uid;?>')"<?php echo ($cardid == 12)?' class="ed"':'';?>><img value="12" src="<?php echo HOST;?>/res/my_card/12_s.jpg?5"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 3)?' class="ed"':'';?>><img value="3" src="<?php echo HOST;?>/res/my_card/3_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 4)?' class="ed"':'';?>><img value="4" src="<?php echo HOST;?>/res/my_card/4_s.jpg?5"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 9)?' class="ed"':'';?>><img value="9" src="<?php echo HOST;?>/res/my_card/9_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 10)?' class="ed"':'';?>><img value="10" src="<?php echo HOST;?>/res/my_card/10_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 2)?' class="ed"':'';?>><img value="2" src="<?php echo HOST;?>/res/my_card/2_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 1)?' class="ed"':'';?>><img value="1" src="<?php echo HOST;?>/res/my_card/1_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 5)?' class="ed"':'';?>><img value="5" src="<?php echo HOST;?>/res/my_card/5_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 6)?' class="ed"':'';?>><img value="6" src="<?php echo HOST;?>/res/my_card/6_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 7)?' class="ed"':'';?>><img value="7" src="<?php echo HOST;?>/res/my_card/7_s.jpg?3"><i class="ico">&#xe60d;</i></li>
                <li<?php echo ($cardid == 8)?' class="ed"':'';?>><img value="8" src="<?php echo HOST;?>/res/my_card/8_s.jpg?3"><i class="ico">&#xe60d;</i></li>
            </ul>
            <div class="clear"></div>
       </div>
        <button type="button"  class="btn size4 HONG2 makebtn" id="makecard">点击生成 / 下载相亲卡</button>　　　
        <button type="button"  class="btn size4 BAI ewmkind" id="ewmkind">换为个人二维码</button>
	</td>
	</tr>
  <tr><td valign="top">
<div class="cardbox_view" id="cardbox_view"></div>

    <div class="my_card_detail">
        <div class="cardbox">
            <div class="card" id="cardcontent">
                <img src="<?php echo HOST;?>/res/my_card/<?php echo $cardid.$bigextname.'?'.$_ZEAI['cache_str'];?>" class="mb" id="mb"><!--www_zeai_cn_V6_7.1-->
                <?php if ($cardid == 11){?>

                    <div class="sex11"><?php echo $sex_str2;?></div>
                    <div class="uid11">相亲卡卡号<br /><?php echo $uid;?></div>
                    <div class="dl">
                    	<dt><em class="photo_m11" style="background:url('<?php echo $photo_m_url;?>');background-position:center center;background-size:cover"></em></dt>
                        <dd>
                        	<li><h5>生年</h5><span><?php if (!empty($birthday) && $birthday!='0000-00-00'){echo substr($birthday,0,4);}?></span></li>
                        	<li><h5>婚况</h5><span><?php echo udata('love',$love);?></span></li>
                        	<li><h5>学历</h5><span <?php echo (str_len($edu_str) >6)?' style="padding-right:0;font-size:24px"':'';?>><?php echo $edu_str;?></span></li>
                        	<li><h5>身高</h5><span><?php echo udata('heigh',$heigh);?></span></li>
                        	<li><h5>月薪</h5><span<?php echo (str_len($pay_str) >6)?' style="padding-right:0;font-size:28px"':'';?>><?php echo $pay_str;?></span></li>
                        	<li><h5>地区</h5><span><?php echo $area_s_title;?></span></li>
                        </dd>
                    </div>
                    <div class="dlmore">
						<?php if (!empty($birthday) && $birthday!='0000-00-00'){?>
                            <li>属<?php echo getbirthpet($birthday);?></li>
                            <li><?php echo getstar($birthday);?></li>
                        <?php }?>
                        <?php if (!empty($child)){?><li><?php echo udata('child',$child);?></li><?php }?>
                        <?php if (!empty($area2title)){?><li>户籍<?php echo $area_s_title2;?></li><?php }?>
                        <?php if (!empty($job)){?><li>职业<?php echo udata('job',$job);?></li><?php }?>
                        <?php if (!empty($companykind)){?><li><?php echo udata('companykind',$companykind);?></li><?php }?>
                        <?php if (!empty($house)){?><li><?php echo udata('house',$house);?></li><?php }?>
                        <?php if (!empty($car)){?><li><?php echo udata('car',$car);?></li><?php }?>
                        <?php if (!empty($smoking)){?><li><?php echo udata('smoking',$smoking);?></li><?php }?>
                        <?php if (!empty($drink)){?><li><?php echo udata('drink',$drink);?></li><?php }?>
                        <?php if (!empty($marrytime) && $marrytime_str!='不限'){?><li>期望<?php echo $marrytime_str;?>结婚</li><?php }?>
                    </div>
                    <div class="ewm11">
						<?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>?<?php echo $_ZEAI['cache_str'];?>" id="card_ewm" class="hnewm"><font id="ewmtitle">长按加红娘微信</font><?php }else{?>
                        <img src="<?php echo HOST.'/sub/creat_ewm.php?url='.mHref('u',$uid);?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font>
                        <?php }?>
					</div>
                    <div class="sitename">－<?php echo $_ZEAI['siteName'];?>提供－</div>
                <?php }elseif($cardid == 12){?>
                	
                            <div class="uid12">相亲卡卡号：<?php echo $uid;?></div>
                            <div class="dl">
                                <dd>
                                    <li><h5>性别</h5><span><?php echo $sex_str2;?></span></li>
                                    <li><h5>生年</h5><span><?php if (!empty($birthday) && $birthday!='0000-00-00'){echo substr($birthday,0,4);}?></span></li>
                                    <li><h5>婚况</h5><span><?php echo udata('love',$love);?></span></li>
                                    <li><h5>身高</h5><span><?php echo udata('heigh',$heigh);?></span></li>
                                    <li><h5>学历</h5><span<?php echo (str_len($edu_str) >6)?' style="padding-right:0;font-size:23px"':'';?>><?php echo $edu_str;?></span></li>
                                    <li><h5>地区</h5><span><?php echo $area_s_title;?></span></li>
                                </dd>
                                <dt><em class="photo_m11" style="background:url('<?php echo $photo_m_url;?>');background-position:center center;background-size:cover"></em></dt>
                            </div>
                            <div class="dlmore">
                                <?php if (!empty($birthday) && $birthday!='0000-00-00'){?>
                                    <li>属<?php echo getbirthpet($birthday);?></li>
                                    <li><?php echo getstar($birthday);?></li>
                                <?php }?>
                                <?php if (!empty($child)){?><li><?php echo udata('child',$child);?></li><?php }?>
                                <?php if (!empty($area2title)){?><li>户籍<?php echo $area_s_title2;?></li><?php }?>
                                <?php if (!empty($pay)){?><li>月薪<?php echo udata('pay',$pay);?></li><?php }?>
                                <?php if (!empty($job)){?><li>职业<?php echo udata('job',$job);?></li><?php }?>
                                <?php if (!empty($companykind)){?><li><?php echo udata('companykind',$companykind);?></li><?php }?>
                                <?php if (!empty($house)){?><li><?php echo udata('house',$house);?></li><?php }?>
                                <?php if (!empty($car)){?><li><?php echo udata('car',$car);?></li><?php }?>
                                <?php if (!empty($smoking)){?><li><?php echo udata('smoking',$smoking);?></li><?php }?>
                                <?php if (!empty($drink)){?><li><?php echo udata('drink',$drink);?></li><?php }?>
                                <?php if (!empty($marrytime) && $marrytime_str!='不限'){?><li>期望<?php echo $marrytime_str;?>结婚</li><?php }?>
                            </div>
                            <div class="ewm12">
                                <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>?<?php echo $_ZEAI['cache_str'];?>" id="card_ewm" class="hnewm"><font id="ewmtitle">长按加红娘微信</font><?php }else{?>
                                <img src="<?php echo HOST.'/sub/creat_ewm.php?url='.mHref('u',$uid);?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font>
                                <?php }?>
                            </div>
                            <div class="sitename">－<?php echo $_ZEAI['siteName'];?>提供－</div>
                
                <?php }else{ ?>
                    <div class="newm"></div>
                    <div class="uid"><?php echo $grade_str;?>会员编号：<?php echo $uid;?></div>
                    <div class="me">
                        <li><?php echo $sex_str2;?></li>
                        <?php if (!empty($birthday) && $birthday!='0000-00-00'){?><li><?php echo substr($birthday,0,4);?>年</li><?php }?>
                        <?php if (!empty($birthday) && $birthday!='0000-00-00'){?>
                            <li>属<?php echo getbirthpet($birthday);?></li>
                            <li><?php echo getstar($birthday);?></li>
                        <?php }?>
                        <?php if (!empty($areatitle)){?><li>在<?php echo $area_s_title;?>工作</li><?php }?>
                        <?php if (!empty($area2title)){?><li>户籍<?php echo $area_s_title2;?></li><?php }?>
                        <?php if (!empty($love)){?><li><?php echo udata('love',$love);?><?php if (!empty($child)){?>（<?php echo udata('child',$child);?>）<?php }?></li><?php }?>
                        <?php if (!empty($heigh)){?><li>身高<?php echo udata('heigh',$heigh);?></span></li><?php }?>
                        <?php if (!empty($edu)){?><li>学历<?php echo udata('edu',$edu);?></li><?php }?>
                        <?php if (!empty($job)){?><li>职业<?php echo udata('job',$job);?></li><?php }?>
                        <?php if (!empty($companykind)){?><li><?php echo udata('companykind',$companykind);?></li><?php }?>
                        <?php if (!empty($pay)){?><li>月收入<?php echo udata('pay',$pay);?></li><?php }?>
                        <?php if (!empty($house)){?><li><?php echo udata('house',$house);?></li><?php }?>
                        <?php if (!empty($car)){?><li><?php echo udata('car',$car);?></li><?php }?>
                        <?php if (!empty($smoking)){?><li><?php echo udata('smoking',$smoking);?></li><?php }?>
                        <?php if (!empty($drink)){?><li><?php echo udata('drink',$drink);?></li><?php }?>
                        <?php if (!empty($marrytime) && $marrytime_str!='不限'){?><li>期望<?php echo $marrytime_str;?>结婚</li><?php }?>
                    </div>
                    <div class="you" style="display:none"><?php echo $mate_li_out;?></div>
                    <div class="ewm">
                        <?php if (!empty($kf_wxpic) ){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>?<?php echo $_ZEAI['cache_str'];?>" id="card_ewm" class="hnewm"><font id="ewmtitle">长按二维码加红娘微信<br>注册VIP会员享受一对一牵线</font><?php }else{?>
                        <img src="<?php echo HOST.'/sub/creat_ewm.php?url='.mHref('u',$cook_uid);?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font>
                        <?php }?>
                    </div>
                    <div class="sitename"><?php echo $_ZEAI['siteName'];?>提供</div>
                <?php }?>
                
            </div>
        </div>
    </div>
      <script>
		var uhref='<?php echo mHref('u',$uid);?>',kf_wxpic='<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>?<?php echo $_ZEAI['cache_str'];?>',cardid=<?php echo $cardid;?>,uid=<?php echo $uid;?>;
		<?php
		if ($cardid == 11 || $cardid == 12){?>
			o('mb').onload=function(){
				cardcontent.style.height=mb.height+'px';
			}
		<?php }else{ ?>
			var card=o('cardcontent').children,photo_m=card[0],newm=card[1],uid=card[2],me=card[3],you=card[4],ewm=card[5],sitename=card[6],defm='<?php echo $photo_m_url;?>',picname=<?php echo $uid;?>;
			function setcardpic(b){
				var js=get_mLTW(b);
				var W=photo_m.offsetWidth;
				var l=js.l*W;t=js.t*W;w=js.w*W;
				newm.style.width=w+'px';newm.style.height=newm.style.width;newm.style.left=l+'px';newm.style.top=t+'px';newm.style.backgroundImage='url('+defm+')';
				uid.style.top=parseInt(js.uid_t*W)+'px';uid.style.color=js.uid_color;
				me.style.top=parseInt(js.me_t*W)+'px';me.style.color=js.uid_color;
				you.style.top=parseInt(js.you_t*W)+'px';you.style.color=js.uid_color;
				ewm.style.top=parseInt(js.ewm_t*W)+'px';ewm.style.color=js.ewm_color;
				sitename.style.top=parseInt(js.sitename_t*W)+'px';sitename.style.color=js.sitename_color;
			}
			setcardpic(cardid);
		<?php }?>
		ewmkind.onclick=ewmkindFn;
		makecard.onclick=makecardFn;
    </script>
    </td>
  </tr>
</table>
</body>
</html>
<script>
	zeai.listEach(zeai.tag(zeai_xqk,'img'),function(img){
		var i=img.getAttribute("value");
		img.onclick=function(){
			zeai.openurl('u_card.php?cardid='+i+'&uid=<?php echo $uid;?>');	
		}
	});	
</script>
<?php ob_end_flush();?>

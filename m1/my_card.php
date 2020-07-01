<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=my&e=my_card';require_once ZEAI.'my_chk_u.php';
//$uid=8;
$card_uid = (ifint($uid))?$uid:$cook_uid;
$mate_diy = explode(',',$_ZEAI['mate_diy']);
if($submitok == 'my_card_detail'){
	if(!ifint($cardid))exit(JSON_ERROR);
	require_once ZEAI.'cache/udata.php';$kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out'); 
	$fields  = "grade,sex,photo_s,photo_f,birthday,areatitle,area2title,love,heigh,weigh,edu,pay,house,car,child,blood,pay,job,admid,marrytime,companykind,smoking,drink,marrytime";
	$row = $db->NAME($card_uid,$fields);
	$grade      = $row['grade'];
	$sex        = $row['sex'];
	$photo_s    = $row['photo_s'];
	$photo_f    = $row['photo_f'];
	$birthday   = $row['birthday'];
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
	$admid        = intval($row['admid']);
	$marrytime    = $row['marrytime'];
	$companykind  = $row['companykind'];
	$area2title  = dataIO($row['area2title'],'out');
	$smoking  = $row['smoking'];
	$drink    = $row['drink'];
	$area_s_title = explode(' ',$areatitle);
	$area_s_title1 = $area_s_title[0];
	$area_s_title2 = $area_s_title[1];
	$area_s_title3 = $area_s_title[2];
	if(!empty($area_s_title3)){
		$area_s_title=$area_s_title3;
	}elseif(!empty($area_s_title2)){
		$area_s_title=$area_s_title2;
	}elseif(!empty($area_s_title1)){
		$area_s_title=$area_s_title1;
	}else{$area_s_title='';}
	
	//$area_s_title2 = explode(' ',$area2title);$area_s_title2 = $area_s_title2[1].$area_s_title2[2];
	$area_s_title2 = $area2title;
	$sex_str2 = ($sex == 1)?'男':'女';
	$marrytime_str=udata('marrytime',$marrytime);
	//
	$SQL = ($admid>0)?" AND id=".$admid:" ORDER BY rand() LIMIT 1";
	$rowf = $db->ROW(__TBL_CRM_HN__,"ewm","ifwebshow=1 AND kind='crm' AND flag=1 ".$SQL,'name');
	if ($rowf){
		$hn_ewm   = $rowf['ewm'];
		$kf_wxpic = (!empty($hn_ewm))?$hn_ewm:'';
	}
	$birthday_str  = (!empty($birthday) && $birthday!='0000-00-00')?getage($birthday).'岁':'';
	if (!empty($photo_s) && $photo_f == 1){
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
	?>
    <i class='ico goback Ugoback' id='ZEAIGOBACK-my_card_detail'>&#xe602;</i>
	<script src="<?php echo HOST;?>/res/html2canvas.js"></script>    
	<script src="<?php echo HOST;?>/res/html2canvas_img.js"></script>
	<?php if($cardid==11){?>
		<style>
			.cardbox .makebtn,.cardbox .ewmkind{z-index:180;background:#000;color:#f60}
			.cardbox .makebtn:hover,.cardbox .ewmkind:hover{background:#000;filter:alpha(opacity=100);-moz-opacity:1;opacity:1}
			.cardbox .card .mb{position:absolute;left:0;z-index:1;display:block}
			.cardbox .card .newb{width:100%;height:51%;background-repeat:no-repeat;position:absolute;left:0;top:0;z-index:0}
			.cardbox .card div{position:absolute;z-index:1;}
			.cardbox .card .sex11{left:36%;top:53%;font-size:24px;color:#101d84;width:36px;height:36px;background-color:#fff;border:#323D91 4px solid;border-radius:36px}
			.cardbox .card .uid11{left:65%;top:49%;font-size:20px;color:#fff;text-align:center;width:33vw;text-shadow:0px 1px 1px #E87B29}
			.card .dl{width:100%;height:20%;left:65%;top:62%}
			.card .dl dt{width:30%;height:100%;float:left}
			.card .dl dt .photo_m11{background-repeat:no-repeat;width:76%;height:85%;border:#FEE49A 4px solid;border-radius:10px;margin-left:10%}
			.card .dl dd{width:70%;height:31px;line-height:31px;float:left;margin-top:-5px}
			.card .dl dd li{width:45%;height:100%;float:left;background-color:#6675E8;border-radius:30px;margin:0 5% 4% 0}
			.card .dl dd li h5,.card .dl dd li span{height:100%;color:#fff;display:block;float:left;border-radius:30px;font-size:15px}
			.card .dl dd li h5{width:45%;background-color:#101D84}
			.card .dl dd li span{width:55%;background-color:#6675E8}
			.card div.dlmore {width:66%;left:2%;top:82%;text-align:left;line-height:180%;text-shadow:0px 1px 1px #E87B29}
			.card div.dlmore li{display:inline;padding:0 5px 0 0;font-size:14px;color:#fff;text-align:left}
			.cardbox .card .ewm11{width:30%;left:70%;bottom:1%;text-align:center;font-size:12px;color:#fff}
			.cardbox .card .ewm11 img{width:70%;margin:0 auto;display:block;margin-bottom:3px}
			.cardbox .card .sitename{bottom:1%;color:#ffc}
        </style>
	<?php }elseif($cardid==12){?>
    	<style>
			.cardbox .makebtn,.cardbox .ewmkind{z-index:4;}
			.cardbox .card .mb{position:absolute;left:0;z-index:1;display:block}
			.cardbox .card .newb{width:100%;height:67%;background-repeat:no-repeat;position:absolute;left:0;top:0;z-index:0}
			.cardbox .card div{position:absolute;z-index:1;}
			.cardbox .card .uid12{left:8%;top:57%;font-size:18px;color:#FF4F5A;text-align:left;width:50vw;text-shadow:0px 2px 2px #fff}
			.card .dl{width:100%;height:20%;left:65%;top:63%}
			.card .dl dd{width:64%;height:26px;line-height:26px;float:left;margin-left:6%}
			.card .dl dd li{width:45%;height:100%;float:left;background-color:#FF7D7D;border-radius:30px;margin:0 5% 4% 0}
			.card .dl dd li h5,.card .dl dd li span{height:100%;color:#fff;display:block;float:left;border-radius:30px;font-size:15px}
			.card .dl dd li h5{width:45%;background-color:#950026;text-shadow:0px 0px 0px #000}
			.card .dl dd li span{width:55%;background-color:#FF7D7D;text-shadow:0px 0px 0px #000}
			.card .dl dt{width:30%;height:100%;float:left}
			.card .dl dt .photo_m11{background-repeat:no-repeat;width:72%;height:82%;border:#FF7D7D 3px solid;border-radius:15px;margin-left:3%;margin-top:-14%}
			.card div.dlmore {width:60%;left:8%;top:80%;text-align:left;line-height:150%;text-shadow:0px 1px 1px #fff}
			.card div.dlmore li{display:inline;padding:0 5px 0 0;font-size:14px;color:#000;text-align:left}
			.cardbox .card .ewm12{width:30%;left:68%;bottom:4%;text-align:center;font-size:12px;color:#FF7D7D;line-height:100%;text-shadow:0px 1px 1px #fff}
			.cardbox .card .ewm12 img{width:68%;margin:0 auto;display:block;margin-bottom:3px}
			.cardbox .card .sitename{bottom:3%;color:#FF7D7D;text-shadow:0px 1px 1px #fff}
		</style>
    <?php }?>
	<div class="submain my_card_detail" id="card_detail">
		<div class="cardbox">
        	<div class="card"  id="cardcontent">
                <img src="<?php echo HOST;?>/res/my_card/<?php echo $cardid.$bigextname.'?'.$_ZEAI['cache_str'];?>" class="mb" id="my_cardbigpic"><!--www_zeai_cn_V6_7.1-->
                <?php if ($cardid == 11){?>
                	<div class="newb" style="background:url('<?php echo $photo_b_url;?>');background-size:cover"></div>
                    <div class="sex11"><?php echo $sex_str2;?></div>
                    <div class="uid11">相亲卡卡号<br /><?php echo $card_uid;?></div>
                    <div class="dl">
                    	<dt><em class="photo_m11" style="background:url('<?php echo $photo_m_url;?>');background-position:center center;background-size:cover"></em></dt>
                        <dd>
                        	<li><h5>生年</h5><span><?php if (!empty($birthday) && $birthday!='0000-00-00'){echo substr($birthday,0,4);}?></span></li>
                        	<li><h5>婚况</h5><span><?php echo udata('love',$love);?></span></li>
                        	<li><h5>学历</h5><span><?php echo udata('edu',$edu);?></span></li>
                        	<li><h5>身高</h5><span><?php echo udata('heigh',$heigh);?></span></li>
                        	<li><h5>月薪</h5><span style="font-size:14px"><?php echo udata('pay',$pay);?></span></li>
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
                        <img src="<?php echo HOST.'/sub/creat_ewm.php?url='.mHref('u',$card_uid);?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font>
                        <?php }?>
					</div>
                    <div class="sitename">－<?php echo $_ZEAI['siteName'];?>提供－</div>
                <?php }elseif($cardid == 12){ ?>
                	<div class="newb" style="background:url('<?php echo $photo_b_url;?>');background-size:cover"></div>
                    <div class="uid12">相亲卡卡号:<?php echo $card_uid;?></div>
                    <div class="dl">
                        <dd>
                        	<li><h5>性别</h5><span><?php echo $sex_str2;?></span></li>
                        	<li><h5>生年</h5><span><?php if (!empty($birthday) && $birthday!='0000-00-00'){echo substr($birthday,0,4);}?></span></li>
                        	<li><h5>婚况</h5><span><?php echo udata('love',$love);?></span></li>
                        	<li><h5>身高</h5><span><?php echo udata('heigh',$heigh);?></span></li>
                        	<li><h5>学历</h5><span><?php echo udata('edu',$edu);?></span></li>
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
                        <img src="<?php echo HOST.'/sub/creat_ewm.php?url='.mHref('u',$card_uid);?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font>
                        <?php }?>
					</div>
                    <div class="sitename">－<?php echo $_ZEAI['siteName'];?>提供－</div>
                <?php }else{ ?>
                        <div class="newm"></div>
                        <div class="uid"><?php  echo $grade_str.'会员编号：'.$card_uid;?></div>
                        <div class="me">
                            <li><?php echo $sex_str2;?></li>
                            <?php if (!empty($birthday) && $birthday!='0000-00-00'){echo substr($birthday,0,4);?>年</li><?php }?>
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
                            <img src="<?php echo HOST.'/sub/creat_ewm.php?url='.mHref('u',$card_uid);?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font>
                            <?php }?>
                        </div>
                        <div class="sitename"><?php echo $_ZEAI['siteName'];?>提供</div>
                <?php }?>
            </div>
        	<button type="button"  class="btn size4 makebtn" id="makecard">生成相亲卡</button>
        	<button type="button"  class="btn size4 ewmkind" id="ewmkind"<?php /*if (empty($kf_wxpic) ){echo ' style="display:none"';}*/?>>个人二维码</button>
        </div>
        <div class="cardbox_view" id="cardbox_view"></div>
	</div>
    <script>
	var uhref='<?php echo mHref('u',$card_uid);?>',kf_wxpic='<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>',cardid=<?php echo $cardid;?>,uid=<?php echo $card_uid;?>;
	<?php
	if ($cardid == 11 || $cardid == 12){?>
		o('my_cardbigpic').onload=function(){
			cardcontent.style.height=my_cardbigpic.height+'px';
		}
	<?php }else{ ?>
		var card=o('cardcontent').children,photo_m=card[0],newm=card[1],uidbox=card[2],me=card[3],you=card[4],ewm=card[5],sitename=card[6],defm='<?php echo $photo_m_url;?>';
				photo_m.onload=function(){
					var js=get_mLTW(cardid);
					var W=photo_m.offsetWidth;
					var l=js.l*W;t=js.t*W;w=js.w*W;
					newm.style.width=w+'px';newm.style.height=newm.style.width;newm.style.left=l+'px';newm.style.top=t+'px';newm.style.backgroundImage='url('+defm+')';
					uidbox.style.top=parseInt(js.uid_t*W)+'px';uidbox.style.color=js.uid_color;
					me.style.top=parseInt(js.me_t*W)+'px';me.style.color=js.uid_color;
					you.style.top=parseInt(js.you_t*W)+'px';you.style.color=js.uid_color;
					ewm.style.top=parseInt(js.ewm_t*W)+'px';ewm.style.color=js.ewm_color;
					sitename.style.top=parseInt(js.sitename_t*W)+'px';sitename.style.color=js.sitename_color;
				}
	
	<?php }?>
	ewmkind.onclick=ewmkindFn;
	cardcontent.addEventListener('touchmove',function(e) {e.preventDefault();});
	
	function makecardFn_lht() {
	zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在生成',{time:10})
	html2canvas(cardcontent).then(function(canvas) {
		var cW=canvas.width,cH=canvas.height;
		var img = Canvas2Image.convertToImage(canvas,cW, cH);
		<?php if(is_h5app()){?>
			zeai.msg(0);
			app_save_share({durl:HOST,data:img.src,uid:<?php echo ifint($cook_tg_uid)?$cook_tg_uid:0;?>});					
		<?php }else{?>
			cardbox_view.html('');
			img.style.width='80%';
			cardbox_view.append(img);
			img.onload=function(){
				zeai.msg(0);
				setTimeout(function(){
					ZeaiM.div_pic({fobj:o(card_detail),obj:cardbox_view,title:'',w:cardbox_view.offsetWidth,h:cardbox_view.offsetHeight,fn:function(){
						if(!zeai.empty(o('cardbox_view')))o('cardbox_view').html('');
					}});
					zeai.msg('长按图片保存到相册',{time:4});
				},200);
				
			}
		<?php }?>
	});
}
	
	makecard.onclick=makecardFn_lht;
    </script>
	<?php
exit;}
/**************************W*W*W*.*Z*E*A*I*.*C*N*****V*6*.*1**********************/
?>
<link href="<?php echo HOST;?>/m1/css/my_card.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php
$mini_backT = '';
$mini_title = '点击模版生成相亲卡';
$mini_class = 'top_mini top_miniCARD';
require_once ZEAI.'m1/top_mini.php';?>
<i class='ico goback Ugoback' id='ZEAIGOBACK-my_card'>&#xe602;</i>
<div class="submain CARD">
	<div class="topbg">
    	<ul id="zeai_xqk">
            <li value="11"><img src="<?php echo HOST;?>/res/my_card/11_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
            <li value="12"><img src="<?php echo HOST;?>/res/my_card/12_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
            <li value="3"><img src="<?php echo HOST;?>/res/my_card/3_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
            <li value="4"><img src="<?php echo HOST;?>/res/my_card/4_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
			<li value="9"><img src="<?php echo HOST;?>/res/my_card/9_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
            <li value="10"><img src="<?php echo HOST;?>/res/my_card/10_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
			<li value="2"><img src="<?php echo HOST;?>/res/my_card/2_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
			<li value="1"><img src="<?php echo HOST;?>/res/my_card/1_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
			<li value="5"><img src="<?php echo HOST;?>/res/my_card/5_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
			<li value="6"><img src="<?php echo HOST;?>/res/my_card/6_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
			<li value="7"><img src="<?php echo HOST;?>/res/my_card/7_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
			<li value="8"><img src="<?php echo HOST;?>/res/my_card/8_s.jpg?<?php echo $_ZEAI['cache_str'];?>"></li>
        </ul>
    </div>
</div>
<script>
var browser='<?php echo (is_weixin())?'wx':'h5';?>',
	up2='<?php echo $_ZEAI['up2'];?>/';
	zeai_xqkFn();
	<?php if (!empty($a)){?>
		page({g:HOST+'/m1/my_card'+zeai.ajxext+'submitok=<?php echo $a;?>&cardid=<?php echo $i;?>&uid=<?php echo $uid;?>',y:'my_card',l:'<?php echo $a;?>'});
	<?php }?>
</script>
<script src="<?php echo HOST;?>/m1/js/my_card.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
if(!ifint($cook_tg_uid))json_exit(array('flag'=>'nologin_tg','msg'=>'请先登录后再来','jumpurl'=>HOST.'/m1/tg_my.php'));
$currfields = "photo_s,grade,kind";
require_once 'tg_chkuser.php';
require_once ZEAI.'cache/config_reg.php';
$data_photo_s=$row['photo_s'];
$data_grade=$row['grade'];
$data_kind =$row['kind'];
$card_uid = $uid;
//33333
if($submitok == 'tg_my_ucard_detail'){
	if(!ifint($cardid))exit(JSON_ERROR);
	require_once ZEAI.'cache/udata.php';
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	$fields  = "grade,sex,photo_s,photo_f,birthday,areatitle,area2title,love,heigh,weigh,edu,pay,house,car,child,blood,pay,blood,job,admid,marrytime,companykind,smoking,drink,marrytime";
	$row = $db->NAME($uid,$fields);
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
	$area_s_title = explode(' ',$areatitle);$area_s_title = $area_s_title[1];
	//$area_s_title2 = explode(' ',$area2title);$area_s_title2 = $area_s_title2[1].$area_s_title2[2];
	$area_s_title2 = $area2title;
	$sex_str2 = ($sex == 1)?'男':'女';
	$marrytime_str=udata('marrytime',$marrytime);
	$birthday_str  = (!empty($birthday) && $birthday!='0000-00-00')?getage($birthday).'岁':'';
	//
	//
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
	////////////
	if(is_weixin()){
		$qrcode_url  = HOST.'/res/loadingData.gif';
	}else{
		$qrcode_url = HOST.'/m1/reg.php?tguid='.$cook_tg_uid;
		$qrcode_url = HOST.'/sub/creat_ewm.php?url='.$qrcode_url;
	}
	////////////
	$bigextname=($cardid==11 || $cardid==12)?'.png':'.jpg';
	?>
    <i class='ico goback Ugoback' id='ZEAIGOBACK-tg_my_ucard_detail'>&#xe602;</i>
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

	<div class="submain my_card_detail" id="tg_card_detail">
		<div class="cardbox">
        	<div class="card"  id="cardcontent">
                <img src="<?php echo HOST;?>/res/my_card/<?php echo $cardid.$bigextname.'?'.$_ZEAI['cache_str'];?>" class="mb" id="my_cardbigpic"><!--www_zeai_cn_V6_7.2-->
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
                    <div class="ewm11"><img src="<?php echo $qrcode_url;?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font></div>
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
                    <div class="ewm12"><img src="<?php echo $qrcode_url;?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font></div>
                    <div class="sitename">－<?php echo $_ZEAI['siteName'];?>提供－</div>
                <?php }else{ ?>
                        <div class="newm"></div>
                        <div class="uid"><?php  echo $grade_str.'会员编号：'.$card_uid;?></div>
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
                        <div class="ewm"><img src="<?php echo $qrcode_url;?>" id="card_ewm" class="uewm"><font id="ewmtitle">长按识别二维码<br>马上认识Ta</font></div>
                        <div class="sitename"><?php echo $_ZEAI['siteName'];?>提供</div>
                <?php }?>
            </div>
        	<button type="button" class="btn size4 makebtn" id="makecard"><i class="ico2 ">&#xe64f;</i> 生成相亲卡</button>
        </div>
        <div class="cardbox_view" id="tg_cardbox_view"></div>
	</div>
    <script>
		var cardid=<?php echo $cardid;?>,uid=<?php echo $uid;?>;
		<?php
		if ($cardid == 11 || $cardid == 12){?>
			o('my_cardbigpic').onload=function(){
				cardcontent.style.height=my_cardbigpic.height+'px';
			}
		<?php }else{ ?>
			var card=o('cardcontent').children,photo_m=card[0],newm=card[1],uidbox=card[2],me=card[3],you=card[4],ewm=card[5],sitename=card[6],defm='<?php echo $photo_m_url;?>';
				photo_m.onload=function(){
					var js=tg_get_mLTW(cardid);
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
		cardcontent.addEventListener('touchmove',function(e) {e.preventDefault();});
		
		function tg_makecardFn_lht() {
			zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在生成',{time:10})
			html2canvas(cardcontent).then(function(canvas) {
				var cW=canvas.width,cH=canvas.height;
				var img = Canvas2Image.convertToImage(canvas,cW, cH);
				<?php if(is_h5app()){?>
					zeai.msg(0);
					app_save_share({durl:HOST,data:img.src,uid:<?php echo ifint($cook_tg_uid)?$cook_tg_uid:0;?>});					
				<?php }else{?>
					tg_cardbox_view.html('');
					img.style.width='80%';
					tg_cardbox_view.append(img);
					img.onload=function(){
						zeai.msg(0);
						setTimeout(function(){
							ZeaiM.div_pic({fobj:o(tg_card_detail),obj:tg_cardbox_view,title:'',w:tg_cardbox_view.offsetWidth,h:tg_cardbox_view.offsetHeight,fn:function(){
								if(!zeai.empty(o('tg_cardbox_view')))o('tg_cardbox_view').html('');
							}});
							zeai.msg('长按图片保存到相册',{time:4});
						},200);
						
					}
				<?php }?>
			});
		}		
		makecard.onclick=tg_makecardFn_lht;
		<?php if(is_weixin()){?>
			setTimeout(function(){
				zeai.ajax({url:HOST+'/m1/tg_my_ucard'+zeai.ajxext+'submitok=ajax_tg_gzh_ewm&uid='+uid},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1){
						card_ewm.src=rs.qrcode_url;
						card_ewm.onload=function(){setTimeout(function(){makecard.click();},400);}
					}
				});
			},200);
		<?php }else{?>
			setTimeout(function(){makecard.click();},1200);
		<?php }?>
    </script>
<?php
exit;
//22222
}elseif($submitok == 'tg_my_ucard_list'){?>
	<link href="<?php echo HOST;?>/m1/css/my_card.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
	<?php
	$mini_backT = '';
	$mini_title = '点击模版生成相亲卡';
	$mini_class = 'top_mini top_miniCARD';
	require_once ZEAI.'m1/top_mini.php';?>
	<i class='ico goback Ugoback' id='ZEAIGOBACK-tg_my_ucard_list'>&#xe602;</i>
	<div class="submain CARD">
		<div class="topbg">
			<ul id="tg_zeai_xqk">
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
	<script>var browser='<?php echo (is_weixin())?'wx':'h5';?>',up2='<?php echo $_ZEAI['up2'];?>/';tg_zeai_xqkFn(<?php echo $uid;?>);</script>
	<script src="<?php echo HOST;?>/m1/js/tg_my_ucard.js?<?php echo $_ZEAI['cache_str'];?>"></script>	
<?php exit;}elseif($submitok == 'ajax_tg_gzh_ewm'){
	if(is_weixin()){
		$token = wx_get_access_token();
		if(str_len($token) < 50)json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_token'));
		$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
		$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"www_ZEAI_cn_tg_'.$cook_tg_uid.'_'.$uid.'"}}}';
		$ticket      = Zeai_POST_stream($ticket_url,$ticket_data);
		$T           = json_decode($ticket,true);
		$qrcode_url  = 'http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
		//json_exit(array('flag'=>1,'qrcode_url'=>$qrcode_url));
		$dbdir  = 'p/tmp/'.date('Y').'/'.date('m').'/';
		@mk_dir(ZEAI.'/up/'.$dbdir);
		$dbname = $dbdir.$cook_tg_uid.'_'.cdstrletters(3).'.jpg';
		$DST    = ZEAI.'/up/'.$dbname;
		$im=imagecreatefromjpeg($qrcode_url);
		imagejpeg($im,$DST,90);
		imagedestroy($im);
		json_exit(array('flag'=>1,'qrcode_url'=>$_ZEAI['up2'].'/'.$dbname));
	}
exit;}
//11111
$nodatatips = "<div class='nodatatips' style=\"margin-top:20px\"><i class='ico'>&#xe61f;</i>～～暂无推荐会员～～<br>通过推广优质会员的相亲卡，分享我的推广二维码。让更多用户加入到我的单身团下。</div>";
$mini_backT = '';
$mini_title = '会员相亲卡';
$mini_class = 'top_mini top_miniTG';
require_once ZEAI.'m1/top_mini.php';?>
<i class='ico goback' id='ZEAIGOBACK-tg_my_ucard'>&#xe602;</i>
<div class="submain TG_Uxqk">
	<div class="tg_card_tips">　　通过推广优质会员的相亲卡，分享我的推广二维码。让更多用户加入到我的单身团下，获得收益。</div>
	<?php
	$rt=$db->query("SELECT id,uname,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,heigh FROM ".__TBL_USER__." WHERE tguid<>".$cook_tg_uid." AND flag=1 AND dataflag=1 AND photo_s<>'' AND photo_f=1 ORDER BY admtjtime DESC,refresh_time DESC,id DESC LIMIT 30");
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
			//
			$nickname = (empty($nickname))?$uname:$nickname;
			$birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁　';
			$heigh_str     = (empty($heigh))?'':$heigh.'cm　';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:HOST.'/res/photo_m'.$sex.'.png';
	?>
	<dl onClick="ZeaiM.page.load(HOST+'/m1/tg_my_ucard.php?submitok=tg_my_ucard_list&uid=<?php echo $uid;?>','tg_my_ucard','tg_my_ucard_list');">
		<dt><img src="<?php echo $photo_s_url; ?>"></dt>
		<dd><h4><?php echo uicon($sex.$grade);?><span><?php echo $nickname; ?></span></h4><h6><?php echo $birthday_str.$heigh_str; ?><?php echo $areatitle_str; ?></h6></dd>
		<button type="button" class="btn size2 HONG4">推广相亲卡</button>
	</dl>
	<?php }}else{echo $nodatatips;}?>
</div>
<?php
exit;
function tg_mateset_out_card($i1,$i2,$unit){
	if($i1 == 0 && $i2 == 0){
		$str = "不限";
	}elseif($i1 == $i2){
		$str = $i1.$unit;
	}elseif($i1 > 0 && $i2 > 0){
		$str = $i1."-".$i2.$unit;
	}elseif($i1 == 0 && $i2 > 0){
		$str = $i2.$unit."以下";
	}elseif($i1 > 0 && $i2 == 0){
		$str = $i1.$unit."以上";
	}
	return $str;
}
?>

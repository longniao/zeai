<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
if(!ifint($cook_tg_uid))json_exit(array('flag'=>'nologin_tg','msg'=>'请您先登录帐号','jumpurl'=>HOST.'/m1/tg_my.php'));
require_once ZEAI.'sub/conn.php';

require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
//if($TG_set['force_subscribe']==1 && $data_subscribe!=1){
//	json_exit(array('flag'=>'jumpurl','msg'=>'请您先关注公众号','jumpurl'=>HOST.'/m1/tg_subscribe.php'));
//}

if(ifint($cook_tg_uid)){
	$row  = $db->ROW(__TBL_TG_USER__,"subscribe","id='".$cook_tg_uid."'","num");
	$data_subscribe = $row[0];
	if( $TG_set['force_subscribe']==1 && $data_subscribe!=1){
		json_exit(array('flag'=>'jumpurl','msg'=>'请您先关注公众号','jumpurl'=>HOST.'/m1/tg_subscribe.php'));
	}
}

$currfields = "photo_s,grade,kind,flag,subscribe";
require_once 'tg_chkuser.php';
$data_photo_s=$row['photo_s'];
$data_grade=$row['grade'];
$data_kind =$row['kind'];
$data_subscribe =$row['subscribe'];
//

$photo_m_url = (!empty($data_photo_s ))?$_ZEAI['up2'].'/'.smb($data_photo_s,'m'):HOST.'/res/tg_my_u'.$data_kind.'.png';
if (is_weixin()){
	$emw_url     = $_ZEAI['up2'].'/'.$TG_set['wxbgpic'];
	$qrcode_url  = HOST.'/res/loadingData.gif';
}else{
	$emw_url=$_ZEAI['up2'].'/'.$TG_set['wapbgpic'];
	$qrcode_url = HOST.'/m1/reg.php?tguid='.$cook_tg_uid;
	$qrcode_url = HOST.'/sub/creat_ewm.php?url='.$qrcode_url;
}

//if(is_weixin()){
//	$token = wx_get_access_token();
//	if(str_len($token) < 50)json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_token'));
//	$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
//	$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"www_ZEAI_cn_tg_'.$cook_tg_uid.'"}}}';
//	$ticket      = Zeai_POST_stream($ticket_url,$ticket_data);
//	$T           = json_decode($ticket,true);
//	$qrcode_url  = 'http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($T['ticket']);
//}else{
//	$qrcode_url = HOST.'/m1/reg.php?tguid='.$cook_tg_uid;
//}

if($submitok == 'ajax_tg_gzh_ewm'){
	if(is_weixin()){
		$token = wx_get_access_token();
		if(str_len($token) < 50)json_exit(array('flag'=>0,'tgpic'=>'','msg'=>'zeai_error_token'));
		$ticket_url  = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
		$ticket_data = '{"action_name":"QR_LIMIT_STR_SCENE","action_info":{"scene":{"scene_str":"www_ZEAI_cn_tghb_'.$cook_tg_uid.'"}}}';
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
?>
<style>
.Ugoback{border:#fff 1px solid}#msg_box{z-index:1000}
.my_card_detail{background-color:#E4007F;top:0;padding:0;-webkit-user-select:none;-moz-user-select:none;}
.my_card_detail .cardbox{height:100%;position:relative;display:flex;align-items:center;}
.my_card_detail .cardbox img{width:100%}/*;display:block*/
.cardbox .makebtn{font-size:18px;font-weight:bold;position:absolute;left:-webkit-calc(50% - 80px);bottom:0;width:160px;background-color:rgba(0,0,0,0.7);color:#fff;padding:0 15px;line-height:40px;height:40px;border-radius:3px 3px 0 0;box-shadow: 2px 3px 5px rgba(0, 0, 0, .3);cursor:pointer;}
.cardbox .card{width:100%;position:relative;}
.cardbox .card .mb{display:block;background-color:#fff}
.cardbox .card .newm{border-radius:50%;position:absolute;background-size:cover;background-position:center center;background-repeat:no-repeat}/*width:120px;height:120px;left:-webkit-calc(50% - 60px);top:20px;*/
.cardbox .card div{position:absolute;left:0;text-align:center}
.cardbox .card .uid{width:100%;font-size:16px;line-height:18px;height:18px;text-align:center}
.cardbox .card .ewm{width:42%;left:29%;text-align:center;font-size:12px}/**/
.cardbox .card .ewm img{width:80%;margin:0 auto;display:block;margin-bottom:5px;}
.cardbox .card .sitename{width:100%;text-align:center}
#fximgs,.mask1,.mask0{-webkit-user-select:none;-moz-user-select:none}
</style>
<script src="../res/html2canvas.js"></script>    
<script src="../res/html2canvas_img.js"></script>
<i class="ico goback Ugoback" id="ZEAIGOBACK-tg_my_ewm">&#xe602;</i>

<div class="TG my_card_detail" id="card_detail">
	<div class="cardbox">
		<div class="card" id="cardcontent">
            <img src="<?php echo $emw_url;?>" class="mb">
            <div class="newm"></div>

            <div class="ewm"><img src="<?php echo $qrcode_url;?>" id="card_ewm" class="uewm"></div>

            <div class="sitename" style="display:none"><?php echo $_ZEAI['siteName'];?>提供</div>
		</div>
		<button type="button"  class="btn size4 makebtn" id="makecard"><?php echo is_h5app()?'点击保存或分享':'下载二维码图片';?></button>
	</div>
	<div class="cardbox_view" id="cardbox_view" onClick="alert('点了，确认');"></div>
</div>


<script>
<?php if (is_weixin()){//分享?>
	var share_TG_title = '<?php echo dataIO($TG_set['wxshareT'],'out');?>';
	var share_TG_desc  = '<?php echo dataIO($TG_set['wxshareC'],'out');?>';
	var share_TG_link  = '<?php echo HOST.'/m1/reg.php?tguid='.$cook_tg_uid; ?>';
	var share_TG_imgurl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo'];?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_TG_title,desc:share_TG_desc,link:share_TG_link,imgUrl:share_TG_imgurl});
		wx.onMenuShareTimeline({title:share_TG_title,link:share_TG_link,imgUrl:share_TG_imgurl});
	});
<?php }elseif(is_h5app()){?>
setTimeout(function(){app_set_top_color('#E4007F');},500);

<?php }?>

function longpress(id, func) {
	var timeOutEvent;	
	document.querySelector('#' + id).addEventListener('touchstart', function (e) {
				e.preventDefault();
		clearTimeout(timeOutEvent);
		timeOutEvent = setTimeout(function () {func();}, 300);  // 长按时间为300ms，可以自己设置
	});	
	document.querySelector('#' + id).addEventListener('touchmove', function (e) {

		clearTimeout(timeOutEvent);
	});
	
	document.querySelector('#' + id).addEventListener('touchend', function (e) {
		clearTimeout(timeOutEvent);
	});
}
/*if(is_h5app()){
	longpress('cardbox_view',function(){ 
		zeai.msg('长按了',{time:3});		
		//app_down_file('aaaaa.jpg',img.src);
	
	});
}*/
	var card=o('cardcontent').children,photo_m=card[0],newm=card[1],ewm=card[2],sitename=card[3],defm='<?php echo $photo_m_url;?>';
	photo_m.onload=function(){
		var js=get_mLTW(1);
		var W=photo_m.offsetWidth;
		var l=js.l*W;t=js.t*W;w=js.w*W;
		newm.style.width=w+'px';newm.style.height=newm.style.width;newm.style.left=l+'px';newm.style.top=t+'px';newm.style.backgroundImage='url('+defm+')';
		
		ewm.style.top=parseInt(js.ewm_t*W)+'px';
		ewm.style.left=parseInt(js.ewm_l*W)+'px';
		ewm.style.width=parseInt(js.ewm_w*W)+'px';

		sitename.style.top=parseInt(js.sitename_t*W)+'px';sitename.style.color=js.sitename_color;
	}
	cardcontent.addEventListener('touchmove',function(e) {e.preventDefault();});
	makecard.onclick=makecardFn;
	///setTimeout(function(){makecard.click();},1000);
	function get_mLTW(i){
		switch (i) {
			case 1:return {l:0.401,t:0.418,w:0.20,uid_t:0.433,uid_color:'#ff6b93',ewm_w:0.5,ewm_t:0.96,ewm_l:0.252,sitename_t:1.520,sitename_color:'#ff5093'};break;
		}
	}
	function makecardFn() {
			zeai.msg('<img src="'+HOST+'/res/loadingData.gif" class="middle">正在生成',{time:10})
			html2canvas(cardcontent).then(function(canvas) {
				var cW=canvas.width,cH=canvas.height;
				var img = Canvas2Image.convertToImage(canvas,cW, cH);
				if(is_h5app()){
					zeai.msg(0);
					app_save_share({durl:HOST,data:img.src,uid:<?php echo ifint($cook_tg_uid)?$cook_tg_uid:0;?>});					
				}else{
					cardbox_view.html('');
					img.style.width='80%';
					img.id='fximgs';
					cardbox_view.append(img);
					img.onload=function(){
						zeai.msg(0);
						setTimeout(function(){
							ZeaiM.div_pic({fobj:o(card_detail),obj:cardbox_view,title:'',w:cardbox_view.offsetWidth,h:cardbox_view.offsetHeight,fn:function(){
								if(!zeai.empty(o('cardbox_view')))o('cardbox_view').html('');
							}});
							zeai.msg('长按图片保存到相册',{time:3});							
						},200);
					}
				}
			});
		}
	
	<?php if(is_weixin()){?>
		setTimeout(function(){
			zeai.ajax({url:HOST+'/m1/tg_my_ewm'+zeai.ajxext+'submitok=ajax_tg_gzh_ewm'},function(e){rs=zeai.jsoneval(e);
				if(rs.flag==1){
					card_ewm.src=rs.qrcode_url;
					card_ewm.onload=function(){setTimeout(function(){makecard.click();},200);}
				}
			});
		},100);
	<?php }else{?>
		setTimeout(function(){makecard.click();},1000);
	<?php }?>				
	
</script>
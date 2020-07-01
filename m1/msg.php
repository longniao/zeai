<?php
ob_start();
//$parARR = array('ajax_tz','ajax_sx','ajax_gg','ajax_sx_clearmsg','ajax_tz_del','tip_detail','ajax_gift_div_msg','ajax_msg_hi_div','msg_news_detail');
//if(in_array($_GET['submitok'],$parARR) ){require_once '../sub/init.php';}
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
//
$currfields = 'grade,tipnum,openid,subscribe,myinfobfb,RZ,photo_f';
/*$$rtn='json';*/$chk_u_jumpurl=HOST.'/?z=msg&e='.$e.'&a='.$a;require_once ZEAI.'my_chk_u.php';

$data_grade  = $row['grade'];
$data_tipnum = $row['tipnum'];
$data_openid = $row['openid'];
$data_subscribe = $row['subscribe'];
$RZ = $row['RZ'];
$data_myinfobfb= $row['myinfobfb'];
//
$cook_myinfobfb = $data_myinfobfb;
$cook_RZ = $RZ;$cook_RZarr = explode(',',$cook_RZ);
$cook_photo_f = $row['photo_f'];
require_once ZEAI.'cache/config_wxgzh.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';
//
function newdiandata() {
	global $db,$cook_uid;
	$num_tz = $db->COUNT(__TBL_TIP__,"new=1 AND uid=".$cook_uid);
	$num_sx = $db->COUNT(__TBL_MSG__,"new=1 AND ifdel=0 AND uid=".$cook_uid);
	$tipnum = $num_tz+$num_sx;
	return array('num_tz'=>$num_tz,'num_sx'=>$num_sx,'tipnum'=>$tipnum);
}
if($submitok == 'ajax_sx_clearmsg'){
	if(!ifint($uid))exit(JSON_ERROR);
	$n_my = $db->COUNT(__TBL_MSG__,"ifdel=0 AND new=1 AND senduid=".$uid." AND uid=".$cook_uid);
	if($n_my>$data_tipnum){
		$endnum=0;
	}else{
		$endnum=$data_tipnum-$n_my;
	}
	if ($endnum != $data_tipnum)$db->query("UPDATE ".__TBL_USER__." SET tipnum=$endnum WHERE id=".$cook_uid);
	$SQL = " (uid=".$uid." AND senduid=".$cook_uid.") OR (senduid=".$uid." AND uid=".$cook_uid.") ";
	$db->query("UPDATE ".__TBL_MSG__." SET ifdel=".$cook_uid." WHERE ifdel=0 AND ".$SQL);
	//我发给对方的
	$SQL1 = "senduid=".$cook_uid." AND uid=".$uid;
	$cont = $db->COUNT(__TBL_MSG__,"ifdel=".$uid." AND ".$SQL1);
	if ($cont > 0){//对方已删除
		$db->query("DELETE FROM ".__TBL_MSG__." WHERE ".$SQL);
	}
	$ret=array('flag'=>1)+newdiandata();
	json_exit($ret);
}elseif($submitok == 'ajax_getnewdian'){
	json_exit(newdiandata());
}elseif($submitok == 'ajax_tz_del'){
	if(!ifint($tid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TIP__,"id","new=1 AND uid=".$cook_uid." AND id=".$tid);
	if ($row)$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
	$db->query("DELETE FROM ".__TBL_TIP__." WHERE uid=".$cook_uid." AND id=".$tid);
	$ret=array('flag'=>1)+newdiandata();
	json_exit($ret);
}elseif($submitok == 'ajax_gift_div_msg'){
	if(!ifint($tid))exit(JSON_ERROR);
	$T = $db->ROW(__TBL_TIP__,"remark","id=".$tid);
	if ($T){$guid = $T[0];}else{exit(JSON_ERROR);}
    $rt=$db->query("SELECT a.senduid AS uid,a.new AS ifnew,b.title,b.picurl,b.price,c.nickname FROM ".__TBL_GIFT_USER__." a,".__TBL_GIFT__." b,".__TBL_USER__." c WHERE a.id=".$guid." AND a.uid=".$cook_uid." AND a.gid=b.id AND a.senduid=c.id LIMIT 1");
    if ($db->num_rows($rt)){
		$G = $db->fetch_array($rt,'name');
		$G['nickname'] = dataIO($G['nickname'],'out');
		$G['title']    = dataIO($G['title'],'out');
		$G['picurl']   = $_ZEAI['up2'].'/'.$G['picurl'];
		if ($G['ifnew'] == 1){
			$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE uid=".$cook_uid." AND kind=2 AND id=".$tid);
			$db->query("UPDATE ".__TBL_GIFT_USER__." SET new=0 WHERE uid=".$cook_uid." AND id=".$guid);
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
		}
		$G = array('flag'=>'1') + $G;
		json_exit($G);
    }else{exit(JSON_ERROR);}
}elseif($submitok == 'ajax_msg_hi_div'){
	if(!ifint($tid))exit(JSON_ERROR);
	if(!in_array('hi',$navarr))json_exit(array('flag'=>0,'msg'=>'此功能已关闭'));
	//招招呼前提条件
	$hi_data = explode(',',$_VIP['hi_data']);
	if(count($hi_data)>0 && is_array($hi_data)){
		function hi_ifsex() {
			global $cook_sex,$hi_data;
			$hi_ifsexall = (in_array('mysex1',$hi_data)&&in_array('mysex2',$hi_data))?true:false;
			if(!$hi_ifsexall){
				if($cook_sex==1){
					if(!in_array('mysex1',$hi_data))json_exit(array('flag'=>0,'msg'=>'男性不能打招呼＾_＾'));
				}elseif($cook_sex==2){
					if(!in_array('mysex2',$hi_data))json_exit(array('flag'=>0,'msg'=>'女性不能打招呼＾_＾'));
				}
			}
		}
		foreach ($hi_data as $V){
			switch ($V) {
				case 'rz_mob':if(!in_array('mob',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('mob','title').'】<br>＾_＾'));break;
				case 'rz_identity':if(!in_array('identity',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('identity','title').'】<br>认证成功后，相亲成功率可提升300％'));break;
				case 'rz_photo':if(!in_array('photo',$cook_RZarr))json_exit(array('flag'=>'nocert','msg'=>'请您先进行【'.rz_data_info('photo','title').'】<br>认证成功后，相亲成功率提升300％'));break;
				case 'bfb':$config_bfb = intval($_VIP['hi_bfb_num']);if($cook_myinfobfb < $config_bfb)json_exit(array('flag'=>'nodata','msg'=>'请您先完善资料达'.$config_bfb.'％<br>您当前资料完整度为：'.$cook_myinfobfb.'％'));break;
				case 'sex':$row0 = $db->NUM($uid,"sex");if($row0[0]==$cook_sex && $cook_uid<>$uid)json_exit(array('flag'=>0,'msg'=>'同性不能打招呼＾_＾'));break;
				case 'photo':if($cook_photo_f!=1)json_exit(array('flag'=>'nophoto','msg'=>'请用【本人真实照片】作为头像<br>无头像首页不显示，排名也无效<br>有头像会员，受关注度提升600％'));break;
				case 'mysex1':hi_ifsex();break;//发送方为男
				case 'mysex2':hi_ifsex();break;//发送方为女
				case 'vip':if($data_grade<2)json_exit(array('flag'=>'nolevel','msg'=>'只有VIP会员才可以打招呼＾_＾'));break;
			}
		}
	}
	//招招呼前提条件结束
	$rt=$db->query("SELECT a.senduid,a.content,a.new,b.sex,b.nickname,b.photo_s,b.photo_f FROM ".__TBL_TIP__." a,".__TBL_USER__." b WHERE a.kind=3 AND a.senduid=b.id AND a.uid=".$cook_uid." AND a.id=".$tid);
	if ($db->num_rows($rt)){
		$row = $db->fetch_array($rt,'num');
		$senduid = $row[0];
		$content = urlencode($row[1]);
		$new     = $row[2];
		$sex     = $row[3];
		$nickname= dataIO($row[4],'out');
		$photo_s = $row[5];
		$photo_f = $row[6];
		if ($new == 1){
			$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE uid=".$cook_uid." AND kind=3 AND id=".$tid);
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
		}
		$photo_s = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
		$ifhiher = ($db->COUNT(__TBL_TIP__,"senduid=".$cook_uid." AND uid=".$senduid." AND kind=3") > 0)?1:0;
		echo $tid.'|ZEAI|'.$senduid.'|ZEAI|'.$nickname.'|ZEAI|'.$sex.'|ZEAI|'.$photo_s.'|ZEAI|'.$content.'|ZEAI|'.$new.'|ZEAI|'.$ifhiher;
	}exit;
}elseif($submitok == 'tip_detail'){
	if(!ifint($tid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_TIP__,"content,new,addtime,kind","uid=".$cook_uid." AND id=".$tid);
	if ($row){
		$content = dataIO($row[0],'out');
		$new     = $row[1];
		$addtime = YmdHis($row[2]);
		$kind    = $row[3];
		if ($new == 1){
			$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE id=".$tid." AND uid=".$cook_uid);
			$db->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum-1 WHERE tipnum>=1 AND id=".$cook_uid);
		}
		$kindT=($kind==1)?'系统消息':'红娘消息';
		$rs=encode_json(newdiandata());
	}else{exit(JSON_ERROR);}
	?>
	<style>.msg_detail{background-color:#fff;padding:20px 30px 30px 30px;line-height:200%;font-size:18px}.linebox{margin:20px 0 0;z-index:0}</style>
	<?php
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-msg_detail">&#xe602;</i>'.$kindT;
    $mini_class = 'top_mini top_miniBAI';$mini_backT = '返回';
    require_once ZEAI.'m1/top_mini.php';?>
    <div class="submain msg_detail">
		<div id="msgC"><?php echo $content;?></div>
        <div class="linebox"><div class="line "></div><div class="title BAI S14 C999"><?php echo $addtime; ?></div></div>
    </div>
	<script>
		//红点btm
		rs=zeai.jsoneval('<?php echo $rs;?>');
		newdian(rs);

		function returnId(href,str){
			var hrefarr = href.split(str);
			var newhref=hrefarr[hrefarr.length-1];
			newhref=newhref.replace('/','');
			var id=newhref.replace('.html','');
			return id;
		}	
		//链接处理
		(function pcAreset(){//var mobAarr=['my_money','my_loveb','my_info'];
		zeai.listEach(zeai.tag(msgC,'a'),function(a){
			var href=a.href;
			//pc链接处理
			if (href.indexOf('/u/') !== -1){
				var uid=returnId(href,'/u/');
				a.onclick=function(){ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+uid,'msg_detail','u');}
			}
			if (href.indexOf('/u.php?uid=') !== -1){
				var uid=returnId(href,'/u.php?uid=');
				a.onclick=function(){ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+uid,'msg_detail','u');}
			}
			//
			if (href.indexOf('/video') !== -1){a.onclick=function(){zeai.openurl('<?php echo HOST;?>/?z=video');}}
			if (href.indexOf('/my_money') !== -1){a.onclick=function(){ZeaiM.page.load('m1/my_money'+zeai.extname,'msg_detail','my_money');}}
			if (href.indexOf('/my_loveb') !== -1){a.onclick=function(){ZeaiM.page.load('m1/my_loveb'+zeai.extname,'msg_detail','my_loveb');}}
			if (href.indexOf('/my_cert') !== -1){a.onclick=function(){ZeaiM.page.load('m1/my_info'+zeai.ajxext+'a=cert','msg_detail','my_info');}}
			if (href.indexOf('/my_tg') !== -1){a.onclick=function(){ZeaiM.page.load('m1/TG'+zeai.extname,'msg_detail','TG');}}
			if (href.indexOf('/my_info') !== -1){a.onclick=function(){ZeaiM.page.load('m1/my_info'+zeai.extname,'msg_detail','my_info');}}
			if (href.indexOf('/my_photo') !== -1){a.onclick=function(){ZeaiM.page.load('m1/my_info'+zeai.extname,'msg_detail','my_info');}}
			if (href.indexOf('/my.php') !== -1){a.onclick=function(){zeai.openurl('<?php echo HOST;?>/?z=my');}}
			if (href.indexOf('/my_vip') !== -1){a.onclick=function(){ZeaiM.page.load('m1/my_vip'+zeai.extname,'msg_detail','my_vip');}}
			//约会
			if (href.indexOf('/dating_detail.php?fid=') !== -1){
				var fid=returnId(href,'/dating_detail.php?fid=');
				a.onclick=function(){zeai.openurl('<?php echo HOST;?>/?z=dating&e=detail&a='+fid);}
			}else if(href.indexOf('/dating/') !== -1){
				var fid=returnId(href,'/dating/');
				if(zeai.ifint(fid)){
					a.onclick=function(){zeai.openurl('<?php echo HOST;?>/?z=dating&e=detail&a='+fid);}
				}else{
					a.onclick=function(){zeai.openurl('<?php echo HOST;?>/?z=dating');}
				}
			}
			//交友圈
			if(href.indexOf('/trend/') !== -1){
				var fid=returnId(href,'/trend/');
				if(zeai.ifint(fid)){
					a.onclick=function(){zeai.openurl('<?php echo HOST;?>/?z=trend&submitok=my');}
				}
			}
			//活动
			if (href.indexOf('/party_detail.php?fid=') !== -1){
				var fid=returnId(href,'/party_detail.php?fid=');
				a.onclick=function(){ZeaiM.page.load('m1/party_detail'+zeai.ajxext+'fid='+fid,'msg_detail','party_detail');}
			}else if(href.indexOf('/party/') !== -1){
				var fid=returnId(href,'/party/');
				if(zeai.ifint(fid)){
					a.onclick=function(){ZeaiM.page.load('m1/party_detail'+zeai.ajxext+'fid='+fid,'msg_detail','party_detail');}
				}else{
					a.onclick=function(){zeai.openurl('<?php echo HOST;?>/?z=party');}
				}
			}
			//红包
			if (href.indexOf('/hongbao_detail.php?fid=') !== -1){
				var fid=returnId(href,'/hongbao_detail.php?fid=');
				a.onclick=function(){zeai.openurl('<?php echo HOST;?>/m1/hongbao/detail.php?fid='+fid);}
			}else if(href.indexOf('/hongbao/') !== -1){
				var fid=returnId(href,'/hongbao/');
				if(zeai.ifint(fid)){
					a.onclick=function(){zeai.openurl('<?php echo HOST;?>/m1/hongbao/detail.php?fid='+fid);}
				}else{
					a.onclick=function(){zeai.openurl('<?php echo HOST;?>/m1/hongbao');}
				}
			}
			/*手机内部链接处理/http://www.yzlove.com/?z=dating&e=detail&a=23
			for(var m=0;m<mobAarr.length;m++){
				if (href.indexOf(mobAarr[m]) !== -1){
					a.onclick=function(){ZeaiM.page.load('m1/'+pagestr+zeai.extname,'msg_detail',pagestr);}
				}
			}*/
			a.removeAttribute("href");
		});
	})()
    </script>
	<?php
	exit;
}elseif($submitok == 'msg_news_detail'){
	if(!ifint($nid))exit(JSON_ERROR);
	$row = $db->ROW(__TBL_NEWS__,"title,content,addtime","id=".$nid." AND flag=1");
	if ($row){
		$title   = dataIO($row[0],'out');
		$content = dataIO($row[1],'out');
		$addtime = YmdHis($row[2],"YmdHi");
		$db->query("UPDATE ".__TBL_NEWS__." SET click=click+1 WHERE id=".$nid." AND flag=1");
	}else{exit(JSON_ERROR);}
	?>
	<style>.news_detail{background-color:#fff;padding:20px 15px 20px 15px;line-height:200%;font-size:18px}.linebox{margin:20px 0 0;z-index:0}.top_mini{background:-webkit-linear-gradient(left,#E83191, #FD45A7 30%, #FD45A7, #FD45A7 30%,#FD45A7,#E83191);}
	.news_detail img{width:100%;margin:20px auto}
</style>
	<?php
    $mini_title = '<i class="ico goback" id="ZEAIGOBACK-msg_news_detail">&#xe602;</i>'.$title;
    $mini_class = 'top_mini ';$mini_backT = '';
    require_once ZEAI.'m1/top_mini.php';?>
    <div class="submain news_detail">
		<?php echo $content;?>
        <div class="linebox"><div class="line "></div><div class="title BAI S14 C999"><?php echo $addtime; ?></div></div>
    </div>
	<?php
	exit;
}
//


$nav = 'msg';
//
//$urole = json_decode($_ZEAI['urole']);
$urolenew = json_decode($_ZEAI['urole'],true);
$newarr=array();foreach($urolenew as $RV){if($RV['f']==1){$newarr[]=$RV;}else{continue;}}
$newarr=encode_json($newarr);
$urole = json_decode($newarr);

$chat_daylooknum  = json_decode($_VIP['chat_daylooknum']);
$chat_loveb       = json_decode($_VIP['chat_loveb']);
$chat_duifangfree = json_decode($_VIP['chat_duifangfree'],true);
$a = (empty($a))?'sx':$a;
/*************Ajax Start*************/
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
//通知
if ($submitok == 'ajax_tz'){ ?>
	<?php 
	//$totalnum = $db->COUNT(__TBL_TIP__,"uid=".$cook_uid);
	//$totalpage = ceil($totalnum/$_ZEAI['pagesize']);
	if(!in_array('gift',$navarr))$SQL = " AND kind<>2";
	if(!in_array('hi',$navarr))$SQL = " AND kind<>3";
	
	$_ZEAI['pagesize'] = 100;
	$rt=$db->query("SELECT id,senduid,title,content,new,kind,addtime FROM ".__TBL_TIP__." WHERE uid=".$cook_uid.$SQL." ORDER BY id DESC LIMIT ".$_ZEAI['pagesize']);
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'num');
			if(!$rows)break;
			$id      = $rows[0];
			$senduid = $rows[1];
			$new     = $rows[4];
			$kind    = $rows[5];
			$addtime_str = date_str($rows[6]);
			$new_str = ($new == 1)?'<b></b>':'';
			if ($kind == 2 || $kind == 3){
				$row = $db->NUM($senduid,"sex,grade,nickname,photo_s,photo_f,birthday,pay,areatitle,heigh,edu");
				$sex      = $row[0];
				$grade    = $row[1];
				$nickname = dataIO($row[2],'out');
				$nickname = (empty($nickname))?'uid:'.$senduid:$nickname;
				$photo_s  = $row[3];
				$photo_f  = $row[4];
				$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
				$imgbdr      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
				$img_str     = '<img src="'.$photo_s_url.'"'.$imgbdr.'>';
				if($kind == 2){
					$kind_str = '<i class="ico k2">&#xe69a;</i>';
				}elseif($kind == 3){
					$kind_str = '<i class="ico k3">&#xe6bd;</i>';
				}
				$title       = uicon($sex.$grade).$nickname;
				$content = dataIO($rows[3],'out');
			}else{//1,4
				if($kind == 1){
					$img_str = '<i class="ico k1">&#xe654;</i>';
					$title   = '系统消息';
				}elseif($kind == 4){
					$img_str = '<i class="ico k4">&#xe605;</i>';
					$title   = '红娘消息';
				}
				$kind_str = '';
				$content = dataIO($rows[2],'out');
			}
	?>
    <dl>
        <dt tid="<?php echo $id; ?>" kind="<?php echo $kind;?>"><?php echo $img_str; ?><?php echo $new_str; ?></dt>
        <dd><h4><?php echo $title; ?></h4><h6><?php echo $content; ?></h6></dd>
        <span><?php echo $addtime_str; ?></span><?php echo $kind_str; ?>
        <strong>删除</strong>
    </dl>
	<?php }}else{echo $nodatatips;}?>
    
    <div id="msg_gift_box" class="box_gift">
        <em><img><h3></h3><h6></h6></em>
        <a href="javascript:;">看TA资料</a>
        <a href="javascript:;">回赠礼物</a>
    </div>
    <!--新加-->
    <div id="main_chat_daylooknumHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_daylooknum->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> 人/天':' 无权聊天';
            $ifmy = ($data_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
            $outA .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outA;
        ?>
        </ul>
        <a class="btn size3 HUANG Mcenter block center" onClick="o(div_close).click();ZeaiM.page.load('m1/my_vip'+zeai.extname,ZEAI_MAIN,'my_vip');">我要升级会员</a>
    </div>
    
    <div id="main_chat_lovebHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_loveb->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> '.$_ZEAI['loveB'].'/人':' 免费聊天';
            if($data_grade==$grade){
                $ifmy = '　<font class="Cf00">（我）</font>';
                $myclkB=$num;
            }else{
                $ifmy = '';
            }
            $outI .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outI;
        ?>
        </ul>
        <a class="btn size3 HONG W50_" onClick="clickloveb('chat',ZEAI_MAIN)">单次<?php echo $myclkB;?>解锁</a>
        <a class="btn size3 HUANG W50_" onClick="o(div_close).click();page({g:'m1/my_vip'+zeai.extname,l:'my_vip'});">升级会员</a>
    </div>    
    <!--新加结束-->
    <script>msgtzFn();</script>
<?php
//消息
exit;}elseif($submitok == 'ajax_sx'){
	$sql_ = " (uid=".$cook_uid." OR senduid=".$cook_uid.") AND ifdel<>".$cook_uid;
	$SQL = "SELECT id,uid,senduid,t,content,addtime FROM ".__TBL_MSG__." A,(SELECT MAX(id) AS max_id FROM ".__TBL_MSG__." WHERE ".$sql_." GROUP BY senduid) B WHERE A.id=B.max_id AND ".$sql_." ORDER BY A.id DESC";
	$rt=$db->query($SQL);
	while($tmprows = $db->fetch_array($rt,'name')){if($tmprows['senduid'] == $cook_uid){$tmprows['senduid'] = $tmprows['uid'];}$arr[]=$tmprows;}
	if($arr){
		$list = msg_reset($arr);
		foreach ($list as $rows) {
			$senduid = $rows['senduid'];
			$t       = $rows['t'];
			$content = dataIO($rows['content'],'out');
			$addtime_str = date_str($rows['addtime']);
			//主表信息
			$row = $db->NUM($senduid,"sex,grade,nickname,photo_s,photo_f,birthday,pay,areatitle,heigh,edu");
			$sex      = $row[0];
			$grade    = $row[1];
			$nickname = dataIO($row[2],'out');
			$nickname = (empty($nickname))?'uid:'.$senduid:$nickname;
			$photo_s  = $row[3];
			$photo_f  = $row[4];
			$birthday  = $row[5];
			$pay       = $row[6];
			$areatitle = $row[7];
			$heigh     = $row[8];
			$edu       = $row[9];
			$birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁';
			$heigh_str     = (empty($heigh))?'':' '.$heigh.'cm';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':' '.$areatitle;
			//
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
			//列表红点
			$inum        = $db->COUNT(__TBL_MSG__," new=1 AND ifdel=0 AND senduid=".$senduid." AND uid=".$cook_uid);
			$inum_str    = ($inum>0)?'<i class="new">'.$inum.'</i>':'';
			//锁
			if($chat_duifangfree[$grade]!=1){
				$MsgFlag = lock_msg($senduid);
			}else{
				$MsgFlag=true;
			}
			if ($MsgFlag){
				if($kind == 2){$content = '[语音]';}elseif(strstr($content,"[/img]")){$content = preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=".HOST."/res/bq/\\1.gif>",$content); }$dtcls = '';
			}else{
				$content = $birthday_str.$heigh_str.' '.udata('pay',$pay).' '.udata('edu',$edu).$areatitle_str;
				$dtcls = 'class="lock"';
			}
		?>
		<dl>
			<dt uid="<?php echo $senduid; ?>"<?php echo $dtcls;?>><img src="<?php echo $photo_s_url; ?>"><i class="ico lockico">&#xe61e;</i></dt>
			<dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $content; ?></h6></dd>
			<span><?php echo $addtime_str; ?></span><?php echo $inum_str; ?>
            <strong>删除</strong>
		</dl>
	<?php }}else{echo $nodatatips;}?>
    
    <div id="main_chat_daylooknumHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_daylooknum->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> 人/天':' 无权聊天';
            $ifmy = ($data_grade==$grade)?'　　<font class="Cf00">（我）</font>':'';
            $outA .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outA;
        ?>
        </ul>
        <a class="btn size3 HUANG Mcenter block center" onClick="o(div_close).click();ZeaiM.page.load('m1/my_vip'+zeai.extname,ZEAI_MAIN,'my_vip');">我要升级会员</a>
    </div>
    
    <div id="main_chat_lovebHelp" class="helpDiv">
        <ul>
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $num   = $chat_loveb->$grade;
            $num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> '.$_ZEAI['loveB'].'/人':' 免费聊天';
            if($data_grade==$grade){
                $ifmy = '　<font class="Cf00">（我）</font>';
                $myclkB=$num;
            }else{
                $ifmy = '';
            }
            $outI .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
        }echo $outI;
        ?>
        </ul>
        <a class="btn size3 HONG W50_" onClick="clickloveb('chat',ZEAI_MAIN)">单次<?php echo $myclkB;?>解锁</a>
        <a class="btn size3 HUANG W50_" onClick="o(div_close).click();page({g:'m1/my_vip'+zeai.extname,l:'my_vip'});">升级会员</a>
    </div>    
    <script>msgsxFn();</script>
<?php exit;}elseif($submitok == 'ajax_gg'){ ?>
	<ul class="msg_news">
		<?php
        $rt=$db->query("SELECT id,title,addtime FROM ".__TBL_NEWS__." WHERE id>2 AND kind=1 AND flag=1 ORDER BY px DESC LIMIT 50");
        $total = $db->num_rows($rt);
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt);
            if(!$rows) break;
            $id      = $rows[0];
            $title   = dataIO($rows[1],'out');
            $addtime = YmdHis($rows[2],'Ymd');
            echo '<li><a nid="'.$id.'">'.$title.'</a><span>'.$addtime.'</span></li>';
        ?>
       <?php }}else{echo $nodatatips;}?>
    </ul>
	<script>msgggFn();</script>
<?php exit;}elseif($submitok == 'msg_news_detail'){ ?>
    
    
<?php
exit;}
/*************Ajax End*************/
$headertitle = '我的消息 - ';
require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug:false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','startRecord','stopRecord','onVoiceRecordEnd','uploadVoice','downloadVoice','playVoice','pauseVoice']
	});
	</script>
<?php }?>

<link href="m1/css/msg.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php 
$tipnumtz = $db->COUNT(__TBL_TIP__,"new=1 AND uid=".$cook_uid);
$tipnumsx = $db->COUNT(__TBL_MSG__,"new=1 AND ifdel=0 AND uid=".$cook_uid);
$tipnumtz_str=($tipnumtz>0)?'<b id="num_tz">'.$tipnumtz.'</b>':'';
$tipnumsx_str=($tipnumsx>0)?'<b id="num_sx">'.$tipnumsx.'</b>':'';
if($_ZEAI['mob_mbkind']==3){
?>
<style>
.tabmenu_msg em li span b{color:#FF6F6F;border-color:#FF6F6F}
.tabmenu_msg i{background:#FF6F6F}
.msg dl i.new{background-color:#FF6F6F}
</style>
<?php }?>
<div class="tabmenu tabmenu_3 tabmenu_msg huadong" id="tabmenu_msg">
	<em>
    <li<?php echo ($e == 'tz')?' class="ed"':''; ?> data="m1/msg.php?submitok=ajax_tz" id="msg_tzbtn"><span>通知<?php echo $tipnumtz_str;?></span></li>
    <li<?php echo ($e == 'sx')?' class="ed"':''; ?> data="m1/msg.php?submitok=ajax_sx" id="msg_sxbtn"><span>私信<?php echo $tipnumsx_str;?></span></li>
    <li<?php echo ($e == 'gg')?' class="ed"':''; ?> data="m1/msg.php?submitok=ajax_gg" id="msg_ggbtn"><span>公告</span></li>
    </em>
	<i></i>
</div>
<script>if(mobkind()=='android'){msg_tzbtn.style.lineHeight = '52px';msg_sxbtn.style.lineHeight = '52px';msg_ggbtn.style.lineHeight = '52px';}</script>
<main id='main' class='main msg huadong'></main>
<div class="msg_yd" id="msg_yd"><img src="m1/img/msg_yd.png"></div>
<div id='tips0_100_msg_hi' class='tips0_100_0 alpha0_100_0'></div>

<?php if (is_weixin() && !empty($_GZH['wx_gzh_ewm']) && ($data_subscribe!=1)){?>
<div id="subscribe_box_msg" class="my-subscribe_box"><img src="<?php echo $_ZEAI['up2'].'/'.$_GZH['wx_gzh_ewm']; ?>"><h3>长按二维码关注公众号<br>获取帐号消息通知等全功能体验<br>关注成功之后将不再弹出</h3></div>
<?php }?>

<div id="areabox" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
<div id="areabox2" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>
<div id="mate_areaidbox" class="areabox"><div class="ul"><li><dl><dd></dd></dl></li></div></div>


<?php 
if($_ZEAI['mob_mbkind']==3){
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}
function lock_msg($uid){
	global $cook_uid,$db;
	$row = $db->ROW(__TBL_UCOUNT__,"id","FIND_IN_SET($uid,listed) AND kind='chat' AND uid=".$cook_uid);
	if($row){return true;}else{return false;}
}
?>
<script>
var browser='<?php echo (is_weixin())?'wx':'h5';?>',uid,lovebstr='<?php echo $_ZEAI['loveB'];?>';
zeaiLoadBack=['nav','tabmenu_msg'];
if(zeai.empty(localStorage.msg_yd)){
	zeai.mask({fobj:main,son:msg_yd,cancelBubble:'off',close:function(){
		localStorage.msg_yd='wwwZEAIcn';
	}});
}
tabmenu_msg.addEventListener('touchmove',function(e){e.preventDefault();});
nav.addEventListener('touchmove',function(e){e.preventDefault();});
ZeaiM.tabmenu.init({obj:tabmenu_msg,showbox:ZEAI_MAIN,kind:'blockS'});
msg_<?php echo $e;?>btn.click();
</script>


<?php
if (is_weixin() && !empty($_GZH['wx_gzh_ewm']) && ($data_subscribe!=1)){
	wx_endurl('您刚刚浏览的页面【消息-私信】',HOST.'/?z=msg&e=sx');?>
    <script>setTimeout(function(){ZeaiM.div({obj:o('subscribe_box_msg'),w:260,h:300});},500);</script>
<?php }else{
	//蹦图
	$bounce=json_decode($_ZEAI['bounce'],true);
	if($bounce['flag']['vipdatarz'] == 1){
		$bouncev = '';
		$bounceTip = 'cook_my_bounce'.YmdHis(ADDTIME,'d');
		if($data_grade<=1 && $_COOKIE[$bounceTip.'my_vip'] != 'my_vip' && !empty($bounce['vip_picurl'])  ){
			$bouncev='my_vip';$url='m1/my_vip.php';$pageid='my_vip';$picurl=$_ZEAI['up2']."/".$bounce['vip_picurl'];
		}elseif($data_myinfobfb<60 && $_COOKIE[$bounceTip.'my_info'] != 'my_info' && !empty($bounce['my_info_picurl']) ){
			$bouncev='my_info';$url='m1/my_info.php';$pageid='my_info';$picurl=$_ZEAI['up2']."/".$bounce['my_info_picurl'];
		}elseif(empty($RZ) && $_COOKIE[$bounceTip.'my_rz'] != 'my_rz' && !empty($bounce['rz_picurl']) ){
			$bouncev='my_rz';$url='m1/my_info.php?a=cert';$pageid='my_info';$picurl=$_ZEAI['up2']."/".$bounce['rz_picurl'];
		}
		if(!empty($bouncev)){
			$bounceTip = $bounceTip.$bouncev;
			if($_COOKIE[$bounceTip] != $bouncev){
				setcookie($bounceTip,$bouncev,null,"/",$_ZEAI['CookDomain']);
				?>
				<script>var my_divclose;setTimeout(function(){my_divclose=ZeaiM.div_pic({fobj:main,obj:msg_bounce_box,w:320,h:360});},1500);</script>
				<div id="msg_bounce_box" class="bounce_box bounce"><img style="width:100%;display:block" src="<?php echo $picurl;?>" onClick="my_divclose.click();page({g:'<?php echo $url;?>',l:'<?php echo $pageid;?>'});"></div>
				<?php
			}
		}
	}
}
?>
<script src="m1/js/msg.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php ob_end_flush();?>
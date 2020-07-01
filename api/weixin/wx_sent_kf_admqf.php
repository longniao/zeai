<?php 
require_once '../sub/init.php';
require_once '../sub/conn.php';

$rowc = get_db_content("wxapi_push_kind,wxapi_push_Text,wxapi_push_PicTtitle,wxapi_push_PicPath,wxapi_push_PicContent,wxapi_push_PicUrl,wxapi_push_Userlist");
$_ZEAI['wxapi_push_kind'] = $rowc['wxapi_push_kind'];
$_ZEAI['wxapi_push_Text'] = $rowc['wxapi_push_Text'];
$_ZEAI['wxapi_push_PicTtitle']  = $rowc['wxapi_push_PicTtitle'];
$_ZEAI['wxapi_push_PicPath']    = $rowc['wxapi_push_PicPath'];
$_ZEAI['wxapi_push_PicContent'] = $rowc['wxapi_push_PicContent'];
$_ZEAI['wxapi_push_PicUrl']     = $rowc['wxapi_push_PicUrl'];
$_ZEAI['wxapi_push_Userlist']   = $rowc['wxapi_push_Userlist'];


$rt=$db->query("SELECT openid FROM ".__TBL_USER__." WHERE openid<>'' AND subscribe=1 AND flag=1 ORDER BY id DESC");
$total = $db->num_rows($rt);
if ($total > 0) {
	for($i=1;$i<=$total;$i++) {
		$rows = $db->fetch_array($rt,'all');
		if(!$rows) break;
		$openid = $rows[0];
		wx_sent_WeixinPushInfo_admqf($openid,$wxapi_push_kind);
	}
}					

echo '发送成功！  '.$total.'个';

function wx_sent_WeixinPushInfo_admqf($openid,$wxapi_push_kind){
	global $db,$_ZEAI,$_DATA;
	switch ($_ZEAI['wxapi_push_kind']) {
		case 'text':
			if (!empty($_ZEAI['wxapi_push_Text'])){
				//@wx_sent_kf_msg($openid,addslashes($_ZEAI['wxapi_push_Text']));
				@wx_sent_kf_msg($openid,$_ZEAI['wxapi_push_Text']);
			}
		break;  
		case 'pic':
			$title   = $_ZEAI['wxapi_push_PicTtitle'];
			$content = $_ZEAI['wxapi_push_PicContent'];
			$picurl  = $_ZEAI['up_2domain']."/".$_ZEAI['wxapi_push_PicPath'];
			$url     = $_ZEAI['wxapi_push_PicUrl'];
			if (!empty($_ZEAI['wxapi_push_PicTtitle'])){
				$news_list[] = array('title'=>$title,'description'=>$content,'picurl'=>$picurl,'url'=>$url);
				@wx_sent_kf_msg($openid,$news_list);
			}
		break;
	}
}
?>
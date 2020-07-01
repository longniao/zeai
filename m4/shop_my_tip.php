<?php
require_once '../sub/init.php';
require_once ZEAI.'m4/shop_chk_u.php';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容</div>";
if($submitok=='ajax_MSG_clean'){
	$db->query("DELETE FROM ".__TBL_TIP__." WHERE kind=6 AND tg_uid=".$cook_tg_uid);
	json_exit(array('flag'=>1));
}elseif($submitok=='ajax_MSG_del'){
	if(!ifint($tid))exit(JSON_ERROR);
	$db->query("DELETE FROM ".__TBL_TIP__." WHERE kind=6 AND tg_uid=".$cook_tg_uid." AND id=".$tid);
	json_exit(array('flag'=>1));
}
if($submitok=='shop_tip_detail'){
	$mt='消息内容';
}else{
	$mt='消息通知列表';
	$mini_R = '<a id="shop_tip_btndel">清空</a>';
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的</title>
<?php echo HEADMETA;?>
<link href="<?php echo HOST;?>/res/m4/css/m4.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/res/m4/css/shop.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/res/m4/js/m4.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php 
$url=HOST.'/m4/shop_index.php';
$mini_title = '<i class="ico goback" onClick="zeai.back(\''.$url.'\');">&#xe602;</i>'.$mt;
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
if($submitok=='shop_tip_detail'){
	if(!ifint($tid))exit($nodatatips);
	$row = $db->ROW(__TBL_TIP__,"title,content,new,addtime","tg_uid=".$cook_tg_uid." AND id=".$tid,"name");
	if ($row){
		$title   = dataIO($row['title'],'out');
		$content = dataIO($row['content'],'out');
		$new     = $row['new'];
		$addtime = YmdHis($row['addtime'],'YmdHi');
		if ($new == 1)$db->query("UPDATE ".__TBL_TIP__." SET new=0 WHERE id=".$tid." AND tg_uid=".$cook_tg_uid);
	}else{exit($nodatatips);}?>
	<div class="shop_tip shop_tip_detail">
		<h2><?php echo $title;?></h2>
        <em><?php echo $content;?></em>
        <div class="linebox"><div class="line "></div><div class="title BAI S14 C999"><?php echo $addtime; ?></div></div>
	</div>
<?php }else{?>
    <div class="shop_tip" id="shop_tipbox">
        <?php 
        $_ZEAI['pagesize'] = 100;
        $rt=$db->query("SELECT id,title,new,addtime FROM ".__TBL_TIP__." WHERE tg_uid=".$cook_tg_uid." AND kind=6 ORDER BY id DESC LIMIT ".$_ZEAI['pagesize']);
        $total = $db->num_rows($rt);
        if ($total > 0) {
            for($i=1;$i<=$total;$i++) {
                $rows = $db->fetch_array($rt,'num');
                if(!$rows)break;
                $id    = $rows[0];
                $title = dataIO($rows[1],'out',32);
                $new   = $rows[2];
                $addtime_str = date_str($rows[3]);
                $new_str = ($new == 1)?'<b></b>':'';
                $img_str = '<i class="ico k1">&#xe657;</i>';
                $kind_str = '';
                $content = dataIO($rows[2],'out');
        ?>
        <dl>
            <dt tid="<?php echo $id; ?>"><?php echo $img_str.$new_str; ?></dt>
            <dd><h4><?php echo $title; ?></h4></dd>
            <span><?php echo $addtime_str; ?></span>
            <strong>删除</strong>
        </dl>
        <?php }}else{echo $nodatatips;}?>
    </div>
    <div class="shop_msg_yd" id="msg_yd"><img src="<?php echo HOST;?>/res/m4/img/msg_yd.png"></div>
    <script src="<?php echo HOST;?>/res/m4/js/shop.js?<?php echo $_ZEAI['cache_str'];?>"></script>
    <script>shop_tipFn();var nodatatips="<?php echo $nodatatips;?>";
    shop_tip_btndel.onclick=function(){
        ZeaiM.confirmUp({title:'确定清空全部消息通知么？',cancel:'取消',ok:'确定清空',okfn:function(){
            zeai.ajax({url:'shop_my_tip'+zeai.extname,data:{submitok:'ajax_MSG_clean'}},function(e){rs=zeai.jsoneval(e);
                if(rs.flag==1){shop_tipbox.html(nodatatips);}
            });
        }});
    }
	if(zeai.empty(sessionStorage.msg_yd)){zeai.mask({son:msg_yd,cancelBubble:'off',close:function(){sessionStorage.msg_yd='wwwZEAIcn_shop';}});}
    </script>
	<?php
}require_once ZEAI.'m4/shop_bottom.php';?>
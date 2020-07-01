<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
if (!ifint($uid))json_exit(array('flag'=>0,'msg'=>'发送对象不存在或已被锁定'));
$$rtn='json';$chk_u_jumpurl=Href('u',$uid);
require_once ZEAI.'my_chk_u.php';
//检查拉黑
if (gzflag($cook_uid,$uid) == -1)json_exit(array('flag'=>0,'msg'=>'对方觉得你不太适合Ta，请求失败'));
$up2 = $_ZEAI['up2']."/";
?>
<!doctype html><html><head><meta charset="utf-8">
<title>礼物大厅 - <?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
<style>
body{background-color:#fff;overflow:hidden}
.gift_index{width:96%;height:470px;padding:0 10px 0 0;line-height:200%;font-size:18px;margin:15px auto;overflow:auto}
.gift_index h1{line-height:30px;font-size:30px;margin-bottom:20px;font-weight:bold;color:#FD9F24}
.topbg img{width:100%;display:block}
.gift_index ul{padding:10px 0;clear:both;overflow:auto}
.gift_index ul li{width:20%;padding:15px 0;float:left;text-align:center;cursor:pointer}
.gift_index ul li:hover h2{color:#f70}
.gift_index ul li:hover{background-color:#ffebd1;border-radius:6px}
.gift_index ul li p{width:70px;height:70px;line-height:70px;margin:0 auto;text-align:center}
.gift_index ul li img{max-width:70px;max-height:70px;margin-top:-3px;vertical-align:middle}
.gift_index ul li h2{color:#666;font-size:14px;margin-top:10px;color:#8d8d8d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.gift_index ul li h4{font-size:12px;color:#fd9f23}
.gift_index ul li h4 i{display:inline-block;width:15px;height:15px;line-height:15px}
</style>
</head>
<body>
<div class="gift_index">
	<h1>礼物大厅</h1>
    <div class="topbg"><img src="<?php echo HOST;?>/res/giftbg.png"></div>
    <div id="giflist">
    <?php 
    $rt=$db->query("SELECT id,title,price,picurl FROM ".__TBL_GIFT__." ORDER BY px DESC,id DESC");
    $total = $db->num_rows($rt);
    if ($total > 0) {
        echo '<ul>';
        for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'num');
            if(!$rows)break;
            $id     = $rows[0];
            $title  = dataIO($rows[1],'out');
            $price  = $rows[2];
            $picurl = $_ZEAI['up2'].'/'.$rows[3];
            ?>
            <li gid='<?php echo $id;?>'><p><img src="<?php echo $picurl; ?>"></p><h2><?php echo $title; ?></h2><h4><i class="ico">&#xe618;</i><?php echo $price; ?></h4></li>
            <?php
            if ($i % 5 == 0){echo '</ul>';if ($i != $total)echo '<ul>';}
    }}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
    </div>
</div>


<div id='tips0_100_02' class='tips0_100_0 alpha0_100_0'></div>

<div id="box_gift_index" class="box_gift">
    <em><img><h3></h3><h6></h6></em>
    <a href="javascript:;">取消</a>
    <a href="javascript:;">确认赠送</a>
</div>

<script>
var uid=<?php echo $uid;?>,lovebstr='<?php echo $_ZEAI['loveB'];?>';
function gif_index(box){
	zeai.listEach(zeai.tag(giflist,'li'),function(li){
		li.onclick = function(){
			gift_ajaxdata(li.getAttribute("gid"),box_gift_index,uid);
		}
	});
}
gif_index(box_gift_index);
</script>


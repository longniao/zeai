<?php
require_once '../sub/init.php';
!function_exists('zeai_alone') && exit('forbidden');
if (!ifint($uid))json_exit(array('flag'=>0,'msg'=>'发送对象不存在或已被锁定'));
$$rtn='json';$chk_u_jumpurl=HOST.'/?z=index&e=u&a='.$uid.'&i='.$i;
require_once ZEAI.'my_chk_u.php';
//检查拉黑
if (gzflag($cook_uid,$uid) == -1)json_exit(array('flag'=>0,'msg'=>'对方觉得你不太适合Ta，请求失败'));
?>
<style>
.gift_index{background-color:#fff;padding:0;line-height:200%;font-size:18px}
.linebox{margin:20px auto 50px auto}
.topbg img{width:100%;display:block}
.gift_index ul{background:#fff;padding:10px 0;clear:both;overflow:auto}
.gift_index ul li{width:33%;padding:15px 0;float:left;text-align:center}
.gift_index ul li:hover h2{color:#f70}
.gift_index ul li p{width:70px;height:70px;line-height:70px;margin:0 auto;text-align:center}
.gift_index ul li img{max-width:70px;max-height:70px;margin-top:-3px;vertical-align:middle}
.gift_index ul li h2{color:#666;font-size:14px;margin-top:10px;color:#8d8d8d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.gift_index ul li h4{font-size:12px;color:#fd9f23;margin-top:5px}
.gift_index ul li h4 i{display:inline-block;width:15px;height:15px;line-height:15px}
</style>
<?php	
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-gift_index">&#xe602;</i>礼物商店';
$mini_class = 'top_mini top_miniBAI ';$mini_backT = '返回';//top_miniBAI
require_once ZEAI.'m1/top_mini.php';?>
<div class="submain gift_index">
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
            if ($i % 3 == 0){echo '</ul>';if ($i != $total)echo '<ul>';}
    }}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
    </div>
	
	<div class="linebox"><div class="line W50"></div><div class="title BAI S12 C999">我是有底线的</div></div>
</div>
<div id='tips0_100_02' class='tips0_100_0 alpha0_100_0'></div>
<script>var uid=<?php echo $uid;?>;gif_index(gift_index,box_gift_index);</script>
<div id="box_gift_index" class="box_gift">
    <em><img><h3></h3><h6></h6></em>
    <a href="javascript:;">取消</a>
    <a href="javascript:;">确认赠送</a>
</div>

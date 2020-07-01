<?php
require_once '../sub/init.php';
require_once ZEAI.'my_chk_u.php';
if ($submitok == 'ajax_follow_top'){
	if(ifint($id)){
		$db->query("UPDATE ".__TBL_GZ__." SET px=".ADDTIME." WHERE senduid=".$cook_uid." AND id=".$id);
		json_exit(array('flag'=>1));
	}
}elseif($submitok == 'ajax_follow_del'){
	if(ifint($id)){
		$db->query("DELETE FROM ".__TBL_GZ__." WHERE senduid=".$cook_uid." AND id=".$id);
		json_exit(array('flag'=>1));
	}
}elseif($submitok == 'ajax_hmd_del'){
	if(ifint($id)){
		$db->query("DELETE FROM ".__TBL_GZ__." WHERE senduid=".$cook_uid." AND id=".$id);
		json_exit(array('flag'=>1));
	}
}
$curpage = 'my_follow';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有～～</div>";
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-my_follow">&#xe602;</i>我关注的人';
$mini_class = 'top_mini top_miniBAI';$mini_backT = '';
require_once ZEAI.'m1/top_mini.php';
?>
<style>
.my_follow{background-color:#fff;padding:0 15px}
.my_follow dl{border-bottom:#f2f2f2 1px solid;position:relative;height:70px;box-sizing:border-box}
.my_follow dl dt,.my_follow dl dd{position:absolute;top:13px}
.my_follow dl dt{width:60px;left:0}
.my_follow dl dt img{width:44px;height:44px;border-radius:25px;object-fit:cover;-webkit-object-fit:cover}
.my_follow dl dd{left:60px;max-width:-webkit-calc(100% - 150px);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.my_follow dl dd h6{color:#999;margin-top:3px}
.my_follow dl button{width:66px;display:inline-block;border-radius:3px;background-color:#fff;border:#999 1px solid;color:#999;font-size:14px;position:absolute;line-height:28px;right:0;top:17px}
</style>
<div class="submain <?php echo $curpage;?>" id="<?php echo $curpage;?>_box">
	<?php
	$GZSQL = ",(SELECT COUNT(*) FROM ".__TBL_GZ__." WHERE flag=1 AND senduid=a.uid AND uid=".$cook_uid.") AS gzflag";
	$rt=$db->query("SELECT a.id,a.uid,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.heigh".$GZSQL." FROM ".__TBL_GZ__." a,".__TBL_USER__." b WHERE a.uid=b.id AND a.senduid=".$cook_uid." AND a.flag=1 ORDER BY a.px DESC");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'num');
			if(!$rows)break;
			$id      = $rows[0];
			$uid     = $rows[1];
			$nickname = urldecode(dataIO($rows[2],'out'));
			$sex      = $rows[3];
			$grade    = $rows[4];
			$photo_s  = $rows[5];
			$photo_f  = $rows[6];
			$areatitle= $rows[7];
			$birthday = $rows[8];
			$heigh    = $rows[9];
			$gzflag   = $rows[10];
			//
			$birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁，';
			$heigh_str     = (empty($heigh))?'':$heigh.'cm，';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':$areatitle;
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:'res/photo_m'.$sex.'.png';
			$sexbg      = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			$btnstr=($gzflag == 1)?'互相关注':'已关注';
	?>
	<dl>
        <dt uid="<?php echo $uid; ?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></dt>
        <dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $birthday_str.$heigh_str; ?><?php echo $areatitle_str; ?></h6></dd>
        <button type="button" id="<?php echo $id; ?>"><?php echo $btnstr;?></button>
    </dl>
    <?php }}else{echo $nodatatips;}?>
</div>
<script>
zeai.listEach(zeai.tag(<?php echo $curpage;?>_box,'dt'),function(dt){
	dt.onclick=function(){page({g:'m1/u'+zeai.ajxext+'uid='+this.getAttribute("uid"),y:'my_follow',l:'u'});}
});
zeai.listEach(zeai.tag(<?php echo $curpage;?>_box,'button'),function(btn){
	btn.onclick=function(){
		ZeaiM.confirmUp({title:'取消关注后，你将无法接收Ta的动态哦',cancel:'关闭',ok:'取消关注',okfn:function(){
			zeai.ajax({url:'m1/my_follow'+zeai.extname,data:{submitok:'ajax_follow_del',id:btn.getAttribute("id")}},function(e){rs=zeai.jsoneval(e);if(rs.flag==1){
				btn.parentNode.remove();
				if(!zeai.empty(o('my_gz')))o('my_gz').html(parseInt(o('my_gz').innerHTML)-1);
			}});
		}});
	}
});
</script>
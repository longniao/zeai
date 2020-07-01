<?php
require_once '../sub/init.php';
require_once ZEAI.'my_chk_u.php';
if($submitok == 'ajax_fans_del'){
	if(!ifint($uid))exit(JSON_ERROR);
	$db->query("DELETE FROM ".__TBL_GZ__." WHERE senduid=".$cook_uid." AND uid=".$uid);
	json_exit(array('flag'=>1));
}elseif($submitok == 'ajax_gz'){
	if(!ifint($uid))exit(JSON_ERROR);
	$db->query("INSERT INTO ".__TBL_GZ__."(uid,senduid,px) VALUES ($uid,$cook_uid,".ADDTIME.")");
	json_exit(array('flag'=>1));
}
$curpage = 'my_fans';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有～～</div>";
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-my_fans">&#xe602;</i>我的粉丝';
$mini_class = 'top_mini top_miniBAI';$mini_backT = '';
require_once ZEAI.'m1/top_mini.php';
?>
<style>
.my_fans{background-color:#fff;padding:0 15px}
.my_fans dl{border-bottom:#f2f2f2 1px solid;position:relative;height:70px;box-sizing:border-box}
.my_fans dl dt,.my_fans dl dd{position:absolute;top:13px}
.my_fans dl dt{width:60px;left:0}
.my_fans dl dt img{width:44px;height:44px;border-radius:25px;object-fit:cover;-webkit-object-fit:cover}
.my_fans dl dd{left:60px;max-width:-webkit-calc(100% - 150px);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.my_fans dl dd h6{color:#999;margin-top:3px}
.my_fans dl button{width:66px;display:inline-block;border-radius:3px;background-color:#fff;border:#999 1px solid;color:#999;font-size:14px;position:absolute;line-height:28px;right:0;top:17px}
.my_fans dl button.off{color:#037afe;border-color:#037afe}
</style>
<div class="submain <?php echo $curpage;?>" id="<?php echo $curpage;?>_box">
	<?php
	$rt=$db->query("SELECT a.id,a.senduid,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.heigh FROM ".__TBL_GZ__." a,".__TBL_USER__." b WHERE a.uid=".$cook_uid." AND a.senduid=b.id AND a.flag=1 ORDER BY a.px DESC");
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
			$gzflag = gzflag($uid,$cook_uid);
			$btnstr=($gzflag == 1)?'互相关注':'+关注';
			$btn_cls=($gzflag == 1)?'':' class="off"';
	?>
	<dl>
        <dt uid="<?php echo $uid; ?>"><img src="<?php echo $photo_s_url; ?>"<?php echo $sexbg; ?>></dt>
        <dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $birthday_str.$heigh_str; ?><?php echo $areatitle_str; ?></h6></dd>
        <button type="button"  uid="<?php echo $uid; ?>" id="<?php echo $id; ?>"<?php echo $btn_cls;?>><?php echo $btnstr;?></button>
    </dl>
    <?php }}else{echo $nodatatips;}?>
</div>
<script>
zeai.listEach(zeai.tag(<?php echo $curpage;?>_box,'dt'),function(dt){
	dt.onclick=function(){page({g:'m1/u'+zeai.ajxext+'uid='+this.getAttribute("uid"),y:'my_fans',l:'u'});}
});
zeai.listEach(zeai.tag(<?php echo $curpage;?>_box,'button'),function(btn){
	btn.onclick=function(){
		if(this.hasClass('off')){
				zeai.ajax({url:'m1/my_fans'+zeai.extname,data:{submitok:'ajax_gz',uid:btn.getAttribute("uid")}},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1){
						btn.html('互相关注');btn.class('');
						if(!zeai.empty(o('my_gz')))o('my_gz').html(parseInt(o('my_gz').innerHTML)+1);
					}
				});
			
		}else{
			ZeaiM.confirmUp({title:'取消关注后，你将无法接收Ta的动态哦',cancel:'关闭',ok:'取消关注',okfn:function(){
				zeai.ajax({url:'m1/my_fans'+zeai.extname,data:{submitok:'ajax_fans_del',uid:btn.getAttribute("uid")}},function(e){rs=zeai.jsoneval(e);
					if(rs.flag==1){
						btn.html('+关注');btn.class('off');
						if(!zeai.empty(o('my_gz')))o('my_gz').html(parseInt(o('my_gz').innerHTML)-1);
					}
				});
			}});
		}
	}
});
</script>
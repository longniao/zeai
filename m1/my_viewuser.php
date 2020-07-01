<?php
require_once '../sub/init.php';
$currfields = 'grade';
require_once ZEAI.'my_chk_u.php';
$data_grade   = intval($row['grade']);
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';
$viewlist = json_decode($_VIP['viewlist'],true);
if ($viewlist[$data_grade]!=1)json_exit(array('flag'=>'nolevel','msg'=>'只有VIP会员才可以查看'));
if($submitok == 'ajax_clean'){
	$db->query("DELETE FROM ".__TBL_CLICKHISTORY__." WHERE uid=".$cook_uid);
	json_exit(array('flag'=>1));
}
$curpage = 'my_viewuser';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂无内容～～</div>";
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-my_viewuser">&#xe602;</i>谁看过我';
$mini_class = 'top_mini top_miniBAI';$mini_R = '<a id="my_viewuser_del">清空</a>';
require_once ZEAI.'m1/top_mini.php';
?>
<style>
.my_viewuser{background-color:#fff;padding:0 15px}
.my_viewuser dl{border-bottom:#f2f2f2 1px solid;position:relative;height:70px;box-sizing:border-box}
.my_viewuser dl dt,.my_viewuser dl dd{position:absolute;top:13px}
.my_viewuser dl dt{width:60px;left:0}
.my_viewuser dl dt img{width:44px;height:44px;border-radius:25px;object-fit:cover;-webkit-object-fit:cover}
.my_viewuser dl dd{left:60px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.my_viewuser dl dd h6{color:#999;margin-top:3px}
.my_viewuser dl span{color:#999;font-size:12px;position:absolute;right:0;top:15px}
#my_viewuser_del{display:inline-block;width:40px;position:absolute;top:0;right:0px;font-size:18px;color:#037afe}
</style>
<div class="submain <?php echo $curpage;?>" id="<?php echo $curpage;?>_box">
	<?php
	$rt=$db->query("SELECT a.id,a.senduid,a.new,a.addtime,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.areatitle,b.birthday,b.edu,b.pay,b.heigh FROM ".__TBL_CLICKHISTORY__." a,".__TBL_USER__." b WHERE a.senduid=b.id AND a.uid=".$cook_uid." ORDER BY a.addtime DESC");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'num');
			if(!$rows)break;
			$id      = $rows[0];
			$senduid = $rows[1];
			$new     = $rows[2];
			$addtime_str = date_str($rows[3]);
			$new_str = ($new == 1)?'<b></b>':'';
			//
			$nickname = urldecode(dataIO($rows[4],'out'));
			$sex      = $rows[5];
			$grade    = $rows[6];
			$photo_s  = $rows[7];
			$photo_f  = $rows[8];
			$areatitle= $rows[9];
			$birthday = $rows[10];
			$edu      = $rows[11];
			$pay      = $rows[12];
			$heigh    = $rows[13];
			//	
			$pay_str       = (empty($pay))?'':''.' ';
			$edu_str       = (empty($edu))?'':''.' ';
			$birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁';
			$heigh_str     = (empty($heigh))?'':' '.$heigh.'cm';
			$aARR = explode(' ',$areatitle);$areatitle = $aARR[1].$aARR[2];
			$areatitle_str = (empty($areatitle))?'':' '.$areatitle;
			$photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s.'?1':'res/photo_m'.$sex.'.png';
			$sexbg       = (empty($photo_s) || $photo_f==0)?' class="sexbg'.$sex.'"':'';
			$img_str     = '<img src="'.$photo_s_url.'"'.$sexbg.'>';
	?>
	<dl>
        <dt uid="<?php echo $senduid; ?>"><?php echo $img_str; ?></dt>
        <dd><h4><?php echo uicon($sex.$grade).$nickname; ?></h4><h6><?php echo $birthday_str.$heigh_str.$pay_str.udata('pay',$pay).$edu_str.udata('edu',$edu).$areatitle_str; ?></h6></dd>
        <span><?php echo $addtime_str; ?></span>
    </dl>
    <?php }}else{echo $nodatatips;}?>
</div>
<script>
my_viewuser_del.onclick=function(){
	ZeaiM.confirmUp({title:'确定要清空浏览记录么？',cancel:'取消',ok:'清空',okfn:function(){
		zeai.ajax({url:'m1/my_viewuser'+zeai.extname,data:{submitok:'ajax_clean'}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){<?php echo $curpage;?>_box.html("<?php echo $nodatatips;?>");}
		});
	}});
}
zeai.listEach(zeai.tag(<?php echo $curpage;?>_box,'dt'),function(dt){
	dt.onclick=function(){
		page({g:'m1/u'+zeai.ajxext+'uid='+this.getAttribute("uid"),y:'my_viewuser',l:'u'});}
});
</script>
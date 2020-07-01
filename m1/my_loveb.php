<?php
require_once '../sub/init.php';
$currfields = 'loveb,grade';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/udata.php';
$data_loveb    = $row['loveb'];
$data_grade    = $row['grade'];
//
$curpage = 'my_loveb';
//$urole = json_decode($_ZEAI['urole']);
$urolenew = json_decode($_ZEAI['urole'],true);
$newarr=array();foreach($urolenew as $RV){if($RV['f']==1){$newarr[]=$RV;}else{continue;}}
$newarr=encode_json($newarr);
$urole = json_decode($newarr);


$contact_loveb = json_decode($_VIP['contact_loveb']);
$chat_loveb    = json_decode($_VIP['chat_loveb']);
$task_loveb    = json_decode($_VIP['task_loveb'],true);
$loveb_buy     = json_decode($_VIP['loveb_buy'],true);
$tg = json_decode($_REG['tg'],true);
$a = (empty($a))?'ye':$a;
/*************Ajax Start*************/
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
if ($submitok == 'ajax_ye'){ ?>
<div class="ye fadeInL">
	<div class="box">
    	<div class="dt"><i class="ico">&#xe618;</i></div>
    	<div class="dd"><span class="LoveB"<?php echo ($_ZEAI['mob_mbkind']==3)?' style="color:#FF6F6F"':'';?>><?php echo $data_loveb; ?><font>个</font></span></div>
    </div>
    <div class="clear"></div>
	<div class="loveBdetail">
        ● <?php echo $_ZEAI['loveB']; ?>可用来参与首页会员置顶排名<br>
        <?php if(in_array('gift',$navarr)){?>● 可以用来赠送对方礼物<br><?php }?>
        <?php if(in_array('contact',$navarr)){?>● 可以解锁对方联系方式 <i class="ico helpIco" onClick="zeai.div({fobj:<?php echo $curpage;?>,obj:<?php echo $curpage;?>_contactHelp,title:'解锁联系方式费用说明',w:300,h:300});">&#xe616;</i><br><?php }?>
        <?php if(in_array('chat',$navarr)){?>● 解锁查看回复邮件/聊天 <i class="ico helpIco" onClick="zeai.div({fobj:<?php echo $curpage;?>,obj:<?php echo $curpage;?>_chathelp,title:'解锁聊天费用说明',w:300,h:300});">&#xe616;</i><br><?php }?>
        <br>
        <button class="btnA" onClick="ZeaiM.page.load(HOST+'/m1/my_loveb_getTip.php?backPage=my_loveb','<?php echo $curpage;?>','my_loveb_getTip');"<?php echo ($_ZEAI['mob_mbkind']==3)?' style="background:#FF6F6F"':'';?>>如何获得<?php echo $_ZEAI['loveB']; ?>？</button>
    </div>
</div>    
<?php exit;}elseif($submitok == 'ajax_mx'){
	$p = intval($p);if ($p<1)$p=1;$SQL="";$totalP = intval($totalP);
	if ($p > $totalP)exit($nodatatips);
	if ($p == 1)$SQL = " LIMIT ".$_ZEAI['pagesize'];
	$rt = $db->query("SELECT id,content,num,addtime FROM ".__TBL_LOVEB_LIST__." WHERE uid=".$cook_uid."  ORDER BY id DESC ".$SQL);
	$total = $db->num_rows($rt);
	if ($total <= 0)exit("end");
	if ($p == 1){
		echo '<div class="mx" id="mx">';
		$fort= $total;
	}else{
		$fort= $_ZEAI['pagesize'];
		$db->data_seek($rt,($p-1)*$_ZEAI['pagesize']);
	}
	for($n=1;$n<=$fort;$n++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows)break;
		$id       = $rows['id'];
		$content  = dataIO($rows['content'],'out');
		$num      = $rows['num'];
		$addtime  = YmdHis($rows['addtime']);
		if ($num<0){$numstyle = "fs";}else{$numstyle = "zs";$num = '+'.$num;}
		echo '<dl><dt>'.$content.'<font>'.$addtime.'</font></dt><dd class="'.$numstyle.'">'.$num.'</dd></dl>';
	}
	if ($p == 1)echo '</div>';
	
exit;}elseif($submitok == 'ajax_cz'){ ?>
	<style>.my_loveb .cz dl dd em .ibox h4{background-image:url('<?php echo HOST;?>/res/xgz.png');background-repeat:no-repeat;position:absolute;bottom:-7px;right:-8px;width:14px;height:14px;background-position:left right;background-size:14px 14px;}</style>
    <form id="zeaiFORM" class="cz"><!-- action="../api/pay_weixin_wap/h5pay.php" method="post"-->
    <dl>
        <dt>充值数量</dt>
        <dd id="numlist">
            <em rmb="<?php echo $_VIP['cz_minnum'];?>"><?php echo $_VIP['cz_minnum']*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
            <em rmb="50" class="ed"><?php echo 50*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
            <em rmb="100"><?php echo 100*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
            <em rmb="500"><?php echo 500*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
            <em rmb="1000"><?php echo 1000*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
            <em rmb="2000"><?php echo 2000*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
        </dd>
    </dl>
    <dl><dt>应付金额</dt><dd><span id="price"></span><span id="pricetitle"></span></dd></dl>
    <div style="text-align:center;margin:20px auto"><button type="button" class="btn size4 LV2 W80_ " id="nextbtn">下一步</button></div>
    <?php
		$jumpurl1 = (!empty($jumpurl))?$jumpurl:HOST.'/?z=my&e=my_loveb&a=ye';
		$jumpurl2 = (!empty($jumpurl))?$jumpurl:HOST.'/?z=my&e=my_loveb&a=cz';
	?>
    <input type="hidden" id="return_okurl" value="<?php echo $jumpurl1;?>">
    <input type="hidden" id="return_nourl" value="<?php echo $jumpurl2;?>">
    <input type="hidden" id="money" value="0">
    <input type="hidden" id="kind" value="2">
    <div class="linebox">
        <div class="line W50"></div>
        <div class="title BAI S14" style="background-color:#fff">温馨提醒</div>
    </div>
    <div class="tips">
        <?php
        foreach ($urole as $uv) {
            $grade = $uv->g;
            $title = $uv->t;
            $str = $loveb_buy[$grade];
            $str = ($str >= 1)?'原价':10*$str.'折';
            $out3 .= $title.'<font class="C999">'.$str.'</font><br>';
        }echo $out3;
        ?>
        <a class="aHUANGed" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.extname,'<?php echo $curpage;?>','my_vip');">我要升级</a>
    </div>
    </form>
	<script>
	var loveBzk = <?php echo $loveb_buy[$data_grade];?>;
	var czlist  = numlist.getElementsByTagName("em");
    zeai.listEach(czlist,function(obj){
		if (obj.hasClass('ed')){priceInit(obj);}
		obj.onclick=function(){priceInit(obj);cleardom(obj);}
	});
	function priceInit(obj){
		var rmb=obj.getAttribute("rmb");
		o('money').value = rmb;
		price.html(rmb*loveBzk+'元');
		var tt = (loveBzk < 1)?'　('+loveBzk*10+'折优惠)':'';
		pricetitle.html(tt);
	}
	function cleardom(curdom){
		zeai.listEach(czlist,function(obj){obj.removeClass('ed');});
		curdom.addClass('ed');
	}
	nextbtn.onclick=function(){
		if (o('money').value<=0){zeai.msg('请选择');return false;}
		//ZeaiM.page.load({url:'m1/my_pay.php',kind:o('kind').value,money:o('money').value,return_okurl:o('return_okurl').value,return_nourl:o('return_nourl').value},'<?php echo $curpage;?>','my_pay');
		ZeaiM.page.load({url:HOST+'/m1/my_pay.php',data:{kind:o('kind').value,money:o('money').value,jumpurl:'<?php echo $jumpurl;?>'}},'<?php echo $curpage;?>','my_pay');
	}
    </script>
<?php exit;}?>
<link href="<?php echo HOST;?>/m1/css/my_loveb.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<?php
/*************Ajax End*************/
$mini_title = '<i class="ico goback" id="ZEAIGOBACK-'.$curpage.'">&#xe602;</i>'.$_ZEAI['loveB'].'账户';
$mini_class = 'top_mini top_miniBAI';
$mini_backT = '返回';
require_once ZEAI.'m1/top_mini.php';
?>
<style>.my_loveb .ye .LoveB:before{content:'当前<?php echo $_ZEAI['loveB']; ?>';}
<?php if($_ZEAI['mob_mbkind']==3){?>
#tabmenu_my_loveb i{background:#FF6F6F}
#tabmenu_my_loveb .ed span{color:#FF6F6F}
<?php }?>

</style>
<?php
$totalnum = $db->COUNT(__TBL_LOVEB_LIST__,"uid=".$cook_uid);
$totalP = ceil($totalnum/$_ZEAI['pagesize']);
if ($totalnum > $_ZEAI['pagesize']){?>
	<script>
	var submain = '<?php echo $curpage;?>_submain',
	totalP  = parseInt(<?php echo $totalP; ?>),
	p=1;
	o(submain).onscroll = function(){
		if(!zeai.empty(o('mx'))){
			var t = parseInt(o(submain).scrollTop);
			var cH= parseInt(o(submain).clientHeight);
			var  H= parseInt(o(submain).scrollHeight);
			if (zeai.empty(o('loading_btm')))o(mx).append('<div id="loading_btm"></div>');
			if (H-t-cH <20){//t+cH==H
				var loading_btm = o('loading_btm');
				if (p >= totalP){
					loading_btm.html('已达末页，全部加载结束');
				}else{
					p++;
					o('loading_btm').html('<i class="ico">&#xe502;</i>');
					zeai.ajax({'url':'<?php echo SELF;?>','data':{'submitok':'ajax_mx','totalP':totalP,'p':p}},function(e){
						if (e == 'end'){
							o('loading_btm').html('已达末页，全部加载结束');
						}else{
							loading_btm.remove();
							o(mx).append(e+'<div id="loading_btm"></div>');
						}
					});
				}
			}
		}
	}
	</script>
	<?php
}
?>
<div class="tabmenu tabmenu_3 tabmenumy_loveb" id="tabmenu_my_loveb">
    <li<?php echo ($a == 'ye')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_ye" id="<?php echo $curpage;?>_yebtn"><span><?php echo $_ZEAI['loveB']; ?></span></li>
    <li<?php echo ($a == 'mx')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_mx&totalP=<?php echo $totalP;?>" id="<?php echo $curpage;?>_mxbtn"><span>明细</span></li>
    <li<?php echo ($a == 'cz')?' class="ed tmli"':' class="tmli"'; ?> data="<?php echo SELF;?>?submitok=ajax_cz&jumpurl=<?php echo $jumpurl;?>" id="<?php echo $curpage;?>_czbtn"><span>充值</span></li>
	<i></i>
</div>
<div class="submain2 <?php echo $curpage;?>" id="<?php echo $curpage;?>_submain"></div>
<?php
/*************Main End*************/


/*************helpDiv Start*************/?>
<div id="<?php echo $curpage;?>_contactHelp" class="helpDiv">
    <ul>
    <?php
    foreach ($urole as $uv) {
        $grade = $uv->g;
        $title = $uv->t;
		$num   = $contact_loveb->$grade;
		$num_str = ($num>0)?' <font class="Cf00">'.$num.'</font> '.$_ZEAI['loveB'].'/人':' 免费查看';
		if($data_grade==$grade){
			$ifmy = '　<font class="Cf00">（我）</font>';
			$myclkB=$num;
		}else{
			$ifmy = '';
		}
		$out1 .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
	    }echo $out1;
    ?>
    </ul>
    <a class="btn size3 HUANG Mcenter block center" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.extname,'<?php echo $curpage;?>','my_vip');">我要升级会员</a>
</div>
<div id="<?php echo $curpage;?>_chathelp" class="helpDiv">
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
		$out2 .= '<li>'.uicon_grade_all($grade).' '.$title.' <i class="ico">&#xe62d;</i>'.$num_str.$ifmy.'</li>';
    }echo $out2;
    ?>
    </ul>
    <a class="btn size3 HUANG Mcenter block center" onClick="ZeaiM.page.load(HOST+'/m1/my_vip'+zeai.extname,'<?php echo $curpage;?>','my_vip');">我要升级会员</a>
</div>
<?php /*************helpDiv End*************/?>

<script>
ZeaiM.tabmenu.init({obj:tabmenu_my_loveb,showbox:'<?php echo $curpage;?>_submain'});
setTimeout(function(){<?php echo $curpage;?>_<?php echo $a;?>btn.click();},400);
</script>
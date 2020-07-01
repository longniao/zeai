<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<style>
.main{background:#fff;padding:0 0 20px 0;border-bottom:#e4e4e4 1px solid;margin:0 0 20px 0;clear:both;overflow:auto}
.main dl{width:94%;padding:0 3%;height:70px;overflow:hidden;border-bottom:#eee 1px solid;position:relative;-webkit-overflow-scrolling:touch;-webkit-user-select:none;-moz-user-select:none;cursor:pointer}
/*.main dl:hover{background-color:#f8f8f8}*/
.main dl:last-child{border-bottom:0}
.main dl dt,.main dl dd{float:left;display:block;line-height:30px;margin-top:10px}
.main dl dt{width:50px;font-size:14px;text-align:left}
.main dl dt img{width:50px;height:50px;border-radius:25px;display:block}
.main dl dd{width:-webkit-calc(95% - 50px);margin:10px 0 0 5%;text-align:left;line-height:normal;position:relative}
.main dl dd h2,.main dl dd h3{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:normal}
.main dl dd h2{font-size:16px;margin-top:3px}
.main dl dd h3{font-size:12px;color:#aaa;margin-top:6px}
.main dl .time{position:absolute;right:10px;top:15px;font-size:12px;color:#999}
.tabmenu li:hover{border-color:#e4e4e4;color:#8d8d8d}

.main dl .tgflag{position:absolute;right:10px;top:35px;text-align:center}
.main dl .tgflag div{color:#999;font-size:12px}
.main dl .tgflag div font{color:#f00;font-size:16px}
.main dl .tgflag span{display:inline-block;padding:2px 5px;color:#fff;font-size:12px;background-color:#aaa;border-radius:1px}

/*so*/
.rbox h2{margin-top:16px;height:18px;line-height:18px;text-align:left;position:relative;padding-left:15px;color:#444}
.rbox h2:after{content:'';width:5px;height:18px;background-color:#E75385;position:absolute;left:0;top:0}
.form h2{margin-top:16px;height:18px;line-height:18px;text-align:left;position:relative;padding-left:15px;margin-bottom:10px}
.form h2:after{content:'';width:5px;height:18px;background-color:#E75385;position:absolute;left:0;top:0}

.form{width:450px;background-color:#fff;position:relative;margin-top:0;padding-bottom:20px;margin:0 auto}
.form:last-child{margin-top:20px;padding-bottom:35px}
.form dl{width:100%;height:40px;clear:both;position:relative;margin-bottom:10px}
.form dl dt,.form dl dd{float:left;line-height:40px}
.form dl dt{width:80px;margin:0 10px 0 0px;text-align:right;color:#8d8d8d}
.form dl dd{width:300px;text-align:left}
.form dl dd font{font-size:12px;margin-left:5px;color:#444}
.select{height:30px;line-height:30px}
.SW0{text-indent:3px;width:58px}.SW{text-indent:3px;width:138px}.SW2{width:90px}
.RCW li{width:90px;float:left;padding:0;margin:10px 0 0 -5px;font-size:12px}


</style>
<body>
<div class="navbox">
<a href="<?php echo $SELF; ?>?submitok=so"<?php echo (empty($t))?' class="ed"':''; ?>>筛选会员</a>
<?php if ($t == 1 || $t == 2){ ?>
<a href="###" class="ed">请选择下面要推送的会员（最多8个）</a>
<?php }?>
<div class="clear"></div></div>

<?php if ($submitok == 'so'){ ?>
		<script src="/up/cache/udata.js"></script>
        <script src="/js/select3.js"></script>
		<script src="/js/date_input.js"></script>
        <script src="/js/areaData.js"></script>
        <script>
		var nulltext = '不限';
		var mate_age_ARR = [{'id':'0','value':selstr2}];
		for (var i = 20; i <= 80; i++) {
			var istr = i.toString();
			mate_age_ARR.push({'id':istr,'value':istr+''});
		}
		var mate_heigh_ARR = [{'id':'0','value':selstr2}];
		for (var i = 130; i <= 230; i++) {
			var istr = i.toString();
			mate_heigh_ARR.push({'id':istr,'value':istr+''});
		}
		var mate_weigh_ARR = [{'id':'0','value':selstr2}];
		for (var i = 35; i <= 130; i++) {
			var istr = i.toString();
			mate_weigh_ARR.push({'id':istr,'value':istr+''});
		}
		var mate_sex_ARR = sex_ARR;mate_sex_ARR[1].value = '男朋友';mate_sex_ARR[2].value = '女朋友'
		var mate_pay_ARR = pay_ARR;mate_pay_ARR[0].value = nulltext;
		var mate_edu_ARR = edu_ARR;mate_edu_ARR[0].value = nulltext;
		var mate_love_ARR = love_ARR;mate_love_ARR[0].value = nulltext;
		var mate_house_ARR = house_ARR;mate_house_ARR[0].value = nulltext;
		var mate_car_ARR = car_ARR;mate_car_ARR[0].value = nulltext;
		var mate_areaid_ARR1 = areaid_ARR1;
		var mate_areaid_ARR2 = areaid_ARR2;
		var mate_areaid_ARR3 = areaid_ARR3;
		</script>
        <form method="get" action="<?php echo $SELF; ?>" name="YZLOVE_com.form" id="GYLform8" class="rbox form">
        	<h2>按条件筛选</h2>
            <dl><dt>性　　别</dt><dd><script>zeai_cn__CreateFormItem('radio','mate_sex','<?php echo $mate_sex; ?>','',mate_sex_ARR);</script></dd></dl>
            <dl><dt>年　　龄</dt><dd><script>zeai_cn__CreateFormItem('select','mate_age1','<?php echo $mate_age1; ?>','class="select SW0"',mate_age_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','mate_age2','<?php echo $mate_age2; ?>','class="select SW0"',mate_age_ARR);</script><font>岁</font></dd></dl>
            <dl><dt>身　　高</dt><dd><script>zeai_cn__CreateFormItem('select','mate_heigh1','<?php echo $mate_heigh1; ?>','class="select SW0"',mate_heigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','mate_heigh2','<?php echo $mate_heigh2; ?>','class="select SW0"',mate_heigh_ARR);</script><font>厘米</font></dd></dl>
            <dl><dt>体　　重</dt><dd><script>zeai_cn__CreateFormItem('select','mate_weigh1','<?php echo $mate_weigh1; ?>','class="select SW0"',mate_weigh_ARR);</script> ～ <script>zeai_cn__CreateFormItem('select','mate_weigh2','<?php echo $mate_weigh2; ?>','class="select SW0"',mate_weigh_ARR);</script><font>公斤</font></dd></dl>
            <dl><dt>最低月薪</dt><dd><script>zeai_cn__CreateFormItem('select','mate_pay','<?php echo $mate_pay; ?>','',mate_pay_ARR);</script></dd></dl>
            <dl><dt>最低学历</dt><dd><script>zeai_cn__CreateFormItem('select','mate_edu','<?php echo $mate_edu; ?>','',mate_edu_ARR);</script></dd></dl>
            <dl><dt>所在地区</dt><dd><script>LevelMenu3('m1|m2|m3|'+nulltext+'|<?php echo $m1; ?>|<?php echo $m2; ?>|<?php echo $m3; ?>','class="select SW2"');</script></dd></dl>
            <dl><dt>婚姻状况</dt><dd><script>zeai_cn__CreateFormItem('select','mate_love','<?php echo $mate_love; ?>','',mate_love_ARR);</script></dd></dl>
            <dl><dt>住房情况</dt><dd><script>zeai_cn__CreateFormItem('select','mate_house','<?php echo $mate_house; ?>','',mate_house_ARR);</script></dd></dl>
            <dl><dt>购车情况</dt><dd><script>zeai_cn__CreateFormItem('select','mate_car','<?php echo $mate_car; ?>','',mate_car_ARR);</script></dd></dl>
            <dl><dt><input type="hidden" name="t" value="1" />&nbsp;</dt><dd><input type="submit" value=" 开始筛选 " class="btn2"></dd></dl>
        </form>
		<form method="get" action="<?php echo $SELF; ?>" name="ZEAI.cn.form" id="GYLform7" class="rbox form" style="border-top:#dedede 1px solid">
        	<h2 style="margin-bottom:30px">按昵称搜索</h2>
            <input type="hidden" name="t" value="2" />
            <input value="<?php echo $k; ?>" type="text" name="k" class="input W200" placeholder="输入会员昵称/ID号">&nbsp;<input type="submit" value=" 开始筛选 " class="btn">
        </form>
<?php }else{?>
		<?php
		$SQL = "";
		if ($t == 1){
			if (ifint($mate_sex,'1-2','1'))$SQL .= " AND sex='$mate_sex' ";
			if ($ifphoto == 1)$SQL .= " AND photo_s<>'' AND photo_f=1 ";
			$areaid = '';
			if (ifint($m1) && ifint($m2) && ifint($m3)){
				$areaid = $m1.','.$m2.','.$m3;
			}elseif(ifint($m1) && ifint($m2)){
				$areaid = $m1.','.$m2;
			}elseif(ifint($m1)){
				$areaid = $m1;
			}
			if (!empty($areaid))$SQL .= " AND areaid LIKE '%".$areaid."%' ";
			if (ifint($mate_age1))$SQL .= " AND ( YEAR(NOW()) - YEAR(birthday) >= '$mate_age1' ) ";
			if (ifint($mate_age2))$SQL .= " AND ( YEAR(NOW()) - YEAR(birthday) <= '$mate_age2' ) ";
			if (ifint($mate_heigh1))$SQL .= " AND ( heigh >= '$mate_heigh1' ) ";
			if (ifint($mate_heigh2))$SQL .= " AND ( heigh <= '$mate_heigh2' ) ";
			if (ifint($mate_weigh1))$SQL .= " AND ( heigh >= '$mate_weigh1' ) ";
			if (ifint($mate_weigh2))$SQL .= " AND ( heigh <= '$mate_weigh2' ) ";
			if (ifint($mate_pay))$SQL .= " AND pay>='$mate_pay' ";
			if (ifint($mate_edu))$SQL .= " AND edu>='$mate_edu' ";
			if (ifint($mate_love))$SQL .= " AND love='$mate_love' ";
			if (ifint($mate_house))$SQL .= " AND house='$mate_house' ";
			if (ifint($mate_car))$SQL .= " AND car='$mate_car' ";
		}
		if ($t == 2 && !empty($k)){
			$k = dataIO($k,'in');
			if (ifint($k)){
				$SQL .= " AND id='$k' ";
			}else{
				$SQL .= " AND (uname LIKE '%".$k."%' OR nickname LIKE '%".$k."%') ";
			}
		}
		$rt = $db->query("SELECT id,nickname,sex,grade,photo_s,photo_f,areatitle,birthday,heigh,edu FROM ".__TBL_USER__." WHERE photo_s<>'' AND photo_f=1 ".$SQL." ORDER BY refresh_time DESC LIMIT 100");
		$total = $db->num_rows($rt);
        if ($total <= 0 ) {
            echo "<div class='nodatatips W150'>暂无信息</div>";
            exit;
        } else {    
            $pagesize=100;
            if ($p<1)$p=1;
            require_once ZEAI.'sub/pageadmin.php';
            $mypage=new gylpage($total,$pagesize);
            $pagelist = $mypage->pagebar(1);
            $pagelistinfo = $mypage->limit2();
            $db->data_seek($rt,($p-1)*$pagesize);
        ?>
		<script>
		document.domain = '<?php echo substr($_ZEAI['CookDomain'],1); ?>';
        function sx(listname){
			var newlist = Array();
			var list = document.getElementsByName(listname+'[]');
			for(var k  = 0;k<list.length;k++){if (list[k].checked)newlist.push(list[k].value);}
			window.parent.right.document.getElementById('wxapi_push_Userlist').value = newlist;
			window.parent.ZEAI_winclose();
		}
        </script>
        <style>.btn{top:6px;right:50px;position:fixed;z-index:10}</style>
        <input type="submit" value=" 确认会员 " class="btn" onClick="sx('list');">
        <form name="FORM" id="FORM" method="post" action="<?php echo $SELF; ?>">
        <script src="js/zeai_tablelist.js"></script>
		<script>
        var checkflag = "false";
        var bg='';
        var bg1      = '<?php echo $_Style['list_bg1']; ?>';
        var bg2      = '<?php echo $_Style['list_bg2']; ?>';
        var overbg   = '<?php echo $_Style['list_overbg']; ?>';
        var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
        </script>
        <div class="main">
            <?php
            if ($total > 0) {
                for($i=1;$i<=$pagesize;$i++) {
                    $rows = $db->fetch_array($rt);
                    if(!$rows)break;
                    $id      = $rows[0];
					$uid     = $id;
                    //
                    $nickname = dataIO($rows[1],'out');
                    $sex      = $rows[2];
                    $grade    = $rows[3];
                    $photo_s  = $rows[4];
                    $photo_f  = $rows[5];
                    $areatitle= $rows[6];
                    $birthday = $rows[7];
                    $heigh    = $rows[8];
                    $edu      = $rows[9];
                    //
                    $birthday_str  = (getage($birthday)<=0)?'':getage($birthday).'岁，';
                    $heigh_str     = (empty($heigh))?'':$heigh.'cm，';
                    $aARR = $areatitle;
                    $areatitle_str = (empty($areatitle))?'':$areatitle;
					$edu_ARR = $_DATA['edu'];
					$edu = $edu_ARR[$edu];
					$edu_str = (empty($edu))?'':$edu.'，';
                    //
                    $href        = $_ZEAI['user_2domain'].'/'.$uid;
                    $photo_s_url = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up2'].'/'.$photo_s:$_ZEAI['www_2domain'].'/images/photo_s'.$sex.'.png';
                    $imgbdr      = (empty($photo_s) || $photo_f==0)?' class="imgbdr'.$sex.'"':'';
            ?>
            <dl <?php echo dl_mouse_maxnum($i,$id,8);?>>
                <dt onClick="openurl_('<?php echo $href; ?>')"><img src="<?php echo $photo_s_url; ?>"<?php echo $imgbdr; ?>></dt>
                <dd><h2><?php echo get_user_grade_icon($sex.$grade) ?> <?php echo $nickname; ?><font class="S12 C999">（ID：<?php echo $uid; ?>）</font></h2><h3><?php echo $birthday_str.$heigh_str.$edu_str.$areatitle_str; ?></h3></dd>
                <div class="time">
                <input type="checkbox" name="list[]" value="<?php echo $uid; ?>" id="chk<?php echo $id; ?>" class="checkbox" onclick="chkbox_dl_maxnum(<?php echo $i;?>,<?php echo $id;?>)">
                </div>
                <div class="tgflag"></div>
            </dl>
            <?php }}else{echo "<div class='nodatatipsS'>暂无信息</div>";}?>
        </div>
        <table class="table0" style="width:95%;"><tr><td align="right" class="list_page"><?php echo '<b>'.$pagelist.'</b>';echo "　".$pagelistinfo;?></td></tr></table>
        </form>
        <?php }?>
<?php }?>




<br><br><br><br>
<?php require_once 'bottomadm.php';?>
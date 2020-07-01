<?php
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$currfields = "birthday,loveb,photo_f,photo_s,heigh,pay,refresh_time,dataflag";
require_once 'my_chkuser.php';
require_once ZEAI.'cache/udata.php';

require_once ZEAI.'cache/config_vip.php';
$data_birthday     = $row['birthday'];
$data_loveb        = $row['loveb'];
$data_photo_f      = $row['photo_f'];
$data_photo_s      = $row['photo_s'];
$data_heigh        = $row['heigh'];
$data_pay          = $row['pay'];
$data_refresh_time = $row['refresh_time'];
$data_dataflag     = $row['dataflag'];
if ($data_photo_f == 1 && $data_photo_s<>'' && !empty($data_heigh) && !empty($data_pay) && $data_birthday<>'0000-00-00'){
	$ifshow = true;
}else{
	$ifshow = false;
}
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有会员</div>";
if($submitok == 'ajax_sex'){
	$sql = "";
	if ($t == 1){
		$sql = " AND sex=1";
	}elseif($t == 2){
		$sql = " AND sex=2";
	}
	$rt=$db->query("SELECT id,sex,grade,nickname,photo_s,photo_f FROM ".__TBL_USER__." WHERE birthday<>'0000-00-00' AND heigh>0 AND flag=1 AND dataflag=1 ".$sql." ORDER BY refresh_time DESC LIMIT 10");
	$total = $db->num_rows($rt);
	if ($total > 0) {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,"name");
			if(!$rows)break;
			$uid           = $rows['id'];
			$sex           = $rows['sex'];
			$grade         = $rows['grade'];
			$nickname      = dataIO($rows['nickname'],'out');
			$nickname      = urldecode($nickname);
			$photo_s       = $rows['photo_s'];
			$photo_f       = $rows['photo_f'];
			$birthday      = $rows['birthday'];
			$photo_s_url   = (!empty($photo_s) && $photo_f==1)?$_ZEAI['up_2domain'].'/'.getpath_smb($photo_s,'m'):'/images/photo_m'.$sex.'.png';
			$ifown = ($uid == $cook_uid)?' <font>(我)</font>':'';
			if ($uid == $cook_uid){
				$ifown = ' <font>(我)</font>';
				$ifed  = ' class="my"';
			}else{
				$ifown = '';$ifed  = '';
			}
	?>
	<dl<?php echo $ifed; ?>><dt>第<font><?php echo $i; ?></font>名</dt><dd><a uid='<?php echo $uid;?>'></i><?php echo uicon($sex.$grade).$nickname; ?><?php echo $ifown; ?></a></dd></dl>
	<?php }}else{echo $nodatatips ;}?>
<?php exit;}elseif($submitok=="ajax_modupdate"){
	if ($ifshow){
		$IndexPushLoveb = intval(abs($_VIP['push_index']));
		if ($IndexPushLoveb > $data_loveb) {
			json_exit(array('flag'=>'noloveb','msg'=>$_ZEAI['loveB']."账户余额不足".$IndexPushLoveb));
		} else {
			$db->query("UPDATE ".__TBL_USER__." SET loveb=loveb-$IndexPushLoveb,refresh_time=".ADDTIME." WHERE id=".$cook_uid);
			//爱豆清单入库
			$db->AddLovebRmbList($cook_uid,'置顶排名',-$IndexPushLoveb,'loveb',10);
			//站内消息
			$C = $nickname.'您好，您的'.$_ZEAI["loveB"].'账户有变动：扣除'.$_VIP['push_index'].'　<a href='.Href('loveb').' class=aQING>查看账户</a>';
			$db->SendTip($cook_uid,"您的".$_ZEAI['loveB']."账户有变动：",dataIO($C,'in'),'sys');
			json_exit(array('flag'=>1,'msg'=>'恭喜您置顶成功！'));
		}
	}else{
		if ($data_photo_f == 0 || empty($data_photo_s)){
			json_exit(array('flag'=>0,'msg'=>'请先上传形象照'));
		}else{
			json_exit(array('flag'=>0,'msg'=>'请完善基本资料'));
		}
	}
}

$mc = $db->COUNT(__TBL_USER__,"birthday<>'0000-00-00' AND heigh>0 AND flag=1 AND dataflag=1 AND refresh_time>".$data_refresh_time);
$mc = $mc+1;

$zeai_cn_menu = '';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>置顶排名 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<link href="css/my_msg.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<style>
.my_push_index{top:44px;text-align:left;background-color:#fff;padding:0 20px}
.my_push_index .tabmenuBAI2{width:100%;background:#fff;height:44px;position:relative;text-align:center;top:0}
.my_push_index .tabmenuBAI2 li{font-size:16px;color:#000}
.my_push_index .tabmenuBAI2 i{height:4px;min-width:auto;background:#FD45A7;border-radius:0}
.my_push_index .tabmenuBAI2 .ed span{color:#000;color:#FD45A7}
.my_push_index .mpi{width:80%;margin:20px auto;position:relative;text-align:center}
.my_push_index .mpi section{width:90%;margin:0 auto}
.my_push_index .mpi section div{width:50%;float:left;color:#FEA2C8;font:normal 36px/36px Arial;margin:15px 0}
.my_push_index .mpi section div font{color:#8d8d8d;font-size:20px;padding:0 3px}
.my_push_index .mpi section div:before{font-size:16px;display:block;color:#999;font-family:'Microsoft YaHei','SimSun','宋体'}
.my_push_index .mpi section div:first-child{border-right:#e4e4e4 1px solid;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.my_push_index .mpi section div:first-child:before{content:'当前排名'}
.my_push_index .mpi .sbmtips{font-size:14px;color:#999;margin:20px auto 30px auto}
.my_push_index .mpi .Clist{width:80%;margin:15px auto 20px auto;line-height:200%;color:#999;text-align:left;clear:both;overflow:auto;font-size:14px}
.my_push_index .mpi dl{width:80%;padding:15px 0;margin:0 auto;clear:both;overflow:auto;border-bottom:#eee 1px dashed;color:#8d8d8d}
.my_push_index .mpi dl dt,dl dd{float:left;display:block;line-height:30px}
.my_push_index .mpi dl dt{width:40%;margin-left:10%;font-size:14px;text-align:left}
.my_push_index .mpi dl dt font{color:#f70;margin:0 10px;font-size:16px;text-align:center;display:inline-block;width:24px;height:24px;line-height:24px;background-color:#fff;border:#f70 1px solid;border-radius:30px}
.my_push_index .mpi dl dd{width:50%}
.my_push_index .mpi dl dd font{color:#f00}
.my_push_index .mpi dl dd a{color:#8d8d8d}
.my_push_index .mpi .tbody{background-color:#f8f8f8;border:0;padding:5px 0;box-sizing:border-box;margin-top:30px}
.my_push_index .mpi .tbody dt{padding-left:10px;box-sizing:border-box}
.my_push_index .mpi .blank{padding-top:50px}
.my_push_index .mpi .blank img{width:100px}
.my_push_index .mpi i.sorrympi{font-size:60px;color:#FFA6A6;margin:0 auto}
.my_push_index .mpi .size4{margin-top:20px}
.my_push_index .linebox{z-index:1}
/*tabmenu*/
.tabmenu li{position:static}
.tabmenu b{height:3px;bottom:-4px;}
.my_push_index .mpi section div:last-child:before{content:'当前<?php echo $_ZEAI['loveB']; ?>'}
</style>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>置顶排名</h1>
         <!-- start C -->
        <div class="myRC">
			<div class="my_push_index">
                    
            <?php if (!$ifshow){ ?>
            <div class="mpi lineH200"><br>
                <i class="ico sorrympi">&#xe61f;</i>
                <h3>您无法使用此功能</h3><br>
                <font class="S14 C999">1.资料不完善 <br>2.头像未上传 <br>3.资料或头像审核中<br><br>
                <a  href="my_info.php" class="hong">我要完善资料</a>　　<a href="my_info.php" class="hong">我要上传头像</a></font><br><br>
            </div>
            <?php }else{?>
                <div class="mpi">
                    <section>
                        <div><font>第</font><span class="Cf60" id="my_push_idxmc"><?php echo $mc; ?></span><font>名</font></div>
                        <div><span id="my_push_idxlovb"><?php echo $data_loveb; ?></span><font>个</font></div>
                    </section>
                    <div class="clear"></div>
                    <input type="button" value="　提交申请　" class="btn size4 HONG" id="my_push_index_sbmtbtn">
                    <div class="sbmtips">请不要连续提交，提交一次就扣除<?php echo $_VIP['push_index']; ?></div>
                    <div class="Clist">
                        <div>● 申请一次花费<?php echo $_ZEAI['loveB']; ?><?php echo $_VIP['push_index']; ?>个，直到其他会员将你顶下去，否则你将永远排第一</div>
                        <div>● 无形象照或个人基本资料不完整的会员申请后，将不显示　　<a href="my_info.php" class="btn size1 BAI">上传头像</a>　<a  href="my_info.php" class="btn size1 BAI">完善资料</a></div>
                        <div>● 申请成功后，将在首页和各大版块推荐显示，交友成功率提升10倍以上</div>
                    </div>
                </div>
            <?php }?>
            <br>
            <div class="mpi">
                <div class="linebox"><div class="line"></div><div class="title BAI">本站会员排名TOP10</div></div><br>
                <div class="tabmenu tabmenu_3 tabmenuBAI2" id="my_push_index_nav">
                    <li<?php echo ($a == 1)?' class="ed"':''; ?> data="my_push_index.php?submitok=ajax_sex&t=0" id="my_push_s0btn"><span>默认</span></li>
                    <li<?php echo ($a == 2)?' class="ed"':''; ?> data="my_push_index.php?submitok=ajax_sex&t=1" id="my_gift_s1btn"><span>男会员</span></li>
                    <li<?php echo ($a == 3)?' class="ed"':''; ?> data="my_push_index.php?submitok=ajax_sex&t=2" id="my_gift_s2btn"><span>女会员</span></li>
                    <b></b>
                </div>
                <dl class="tbody"><dt>排名</dt><dd>竞价人</dd></dl>
                <div id="my_push_index_list"></div>
           </div>
            </div>
        </div>
        <!-- end C -->
</div></div></div>

<script>
var push_indexnum=<?php echo $_VIP['push_index'];?>;
ZeaiPC.tabmenu.init({obj:my_push_index_nav,showbox:my_push_index_list});
setTimeout(function(){my_push_s0btn.click();},200);
if(!zeai.empty(o('my_push_index_sbmtbtn')))my_push_index_sbmtbtn.onclick=my_push_index_sbmtbtnFn;
function my_push_index_sbmtbtnFn(){
	zeai.confirm('确定提交置顶排名么？',function(){
		zeai.ajax({url:'my_push_index'+zeai.extname,data:{submitok:'ajax_modupdate'}},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg,{time:2});
			if(rs.flag==1){
				setTimeout(function(){location.reload(true);},2000);
			}
		});
	});
}
</script>
<?php require_once ZEAI.'p1/bottom.php';?>
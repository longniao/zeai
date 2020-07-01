<?php
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
$currfields = "hnid";
require_once 'my_chkuser.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';
$data_hnid = $row['hnid'];
$zeai_cn_menu = 'my_hongniang';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的红娘 - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="css/p1.css" rel="stylesheet" type="text/css" />
<link href="css/my.css" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js"></script>
<script src="js/p1.js"></script>
<style>
.my_hongniang .nohnbtn{width:110px}
.hninfo{width:800px;height:320px;margin:90px auto 0 auto;position:relative}
.hninfo p{position:absolute;top:30px;left:130px;width:200px;height:200px;background-size:cover;background-position:center center;background-repeat:no-repeat;border-radius:116px;}
.hninfo em{position:absolute;top:20px;left:400px;width:100%;height:150px;text-align:left}
.hninfo em .title{width:240px;height:200px}
.hninfo em .title h2{margin-top:10px;color:#333;font-size:30px;font-weight:bold}
.hninfo em .title h2 span{font-size:18px;color:#999;font-weight:normal}
.hninfo em .title h2 span i{margin-left:20px;color:#F7564D}
.hninfo em .title h2 span font{vertical-align:middle;margin-left:3px}
.hninfo em .title .titlestr {font-size:24px;;margin:25px 0 0 0}
.hninfo em .hnhome{width:120px;margin-top:40px;border-radius:49px;text-align:center}
.detaicotact{width:70%;margin:0 auto;position:relative}
.detaicotact h3{margin-bottom:10px}
.detaicotact li{width:90%;line-height:30px;text-align:left;line-height:30px;margin:20px auto;color:#999;font-size:16px}
.detaicotact li span{margin-left:5px;font-size:16px;color:#666}
.detaicotact li a{display:inline-block;color:#fff;line-height:24px;padding:0 5px;margin-left:15px;border-radius:2px}
.detaicotact i.ico{display:inline-block}
.detaicotact .hnewm{text-align:center;position:absolute;right:0;top:70px}
.detaicotact .hnewm img{width:180px;display:block;border:#ddd 1px solid;padding:5px;background-color:#fff;border-radius:3px}
.detaicotact .hnewm h6{font-size:14px;color:#999;margin-top:15px}
.detaicotact li p{width:40px;height:40px;line-height:40px;border-radius:30px;font-size:22px;color:#fff;text-align:center;margin:0 30px 0 10px}
.detaicotact li .telico{background-color:#FF9600}
.detaicotact li .wxico{background-color:#31C93C}
.detaicotact li .qqico{background-color:#51B7EC}
</style>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>我的红娘</h1>
         <!-- start C -->
        <div class="myRC">
			<div class="my_hongniang">
            	<?php if (!ifint($data_hnid)){?>
                        <?php echo '<br><br>'.nodatatips('您暂时还没有牵手红娘线下服务<br>请升级VIP会员享受红娘线下1对1牵线<br><br><a href="my_vip.php" class="nohnbtn btn size3 HONG">升级VIP会员</a><br><br><a href="'.Href('hongniang').'" class="nohnbtn btn size3 HONG2">进入红娘大厅</a>');?>
            	<?php }else{
					$row = $db->ROW(__TBL_CRM_HN__,"sex,qq,weixin,mob,path_s,ewm,truename,sex,title,click","flag=1 AND id=".$data_hnid,"name");
					if ($row){
						$truename = trimhtml(dataIO($row['truename'],'out',7));
						$title    = trimhtml(dataIO($row['title'],'out'));
						$click    = $row['click'];
						$sex      = $row['sex'];
						$path_s   = $row['path_s'];
						$ewm      = $row['ewm'];
						$qq = dataIO($row['qq'],'out');
						$weixin = dataIO($row['weixin'],'out');
						$mob = dataIO($row['mob'],'out');
						//
						$path_s_url = (!empty($path_s))?$_ZEAI['up2'].'/'.getpath_smb($path_s,'b'):HOST.'/res/photo_m'.$sex.'.png';
						$sexbg   = (empty($path_s))?' class="sexbg'.$sex.'"':'';
						$ewm_str    = (!empty($ewm))?'<img src="'.$_ZEAI['up2'].'/'.$ewm.'"'.$sexbg.'>':'暂无';
					}else{nodatatips('红娘已离职');}
					?>
                    <div class="hninfo">
                        <p style="background-image:url(<?php echo $path_s_url; ?>)"<?php echo $sexbg; ?>></p>
                        <em>
                            <div class="title">
                                <h2><?php echo $truename;?><span><i class="ico">&#xe643;</i><font><?php echo $click;?></font></span></h2>
                                <div class="titlestr"><?php echo $title;?></div>
                                <a href="<?php echo Href('hongniang',$data_hnid);?>" target="_blank" class="btn size4 hnhome HONG2">红娘主页</a>
                            </div>
                        </em>
                    </div>
                    
                    <div class="detaicotact">
                    	<div class="linebox"><div class="line "></div><div class="title BAI">联系红娘</div></div><br><br>
                        <?php if (!empty($mob)){ ?><li><p class="ico telico">&#xe60e;</p>红娘热线：<span><?php echo $mob; ?></span></li><?php }?>
                        <?php if (!empty($qq)){ ?><li><p class="ico qqico">&#xe612;</p>红娘QQ：<span><?php echo $qq; ?></span><?php if(!empty($qq)){?><a href="tencent://message/?uin=<?php echo $qq; ?>&Site=<?php echo $truename; ?>&Menu=yes"><img src="<?php echo HOST;?>/res/qq2.gif" alt="红娘QQ服务" class="middle" style="margin-top:-3px" /></a><?php }?></li><?php }?>
                        <?php if (!empty($weixin)){ ?><li><p class="ico wxico">&#xe607;</p>红娘微信：<span><?php echo $weixin; ?></span></li><?php }?>
                        <?php if (!empty($ewm)){ ?>
                        <div class="hnewm"><?php echo $ewm_str; ?><h6>微信扫码加红娘微信</h6></div>
                        <?php }?>
                    </div>

                <?php }?>
            </div>
        </div>
        <!-- end C -->
</div></div></div>
<?php require_once ZEAI.'p1/bottom.php';?>
<?php
/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
ob_start();
require_once '../sub/init.php';
if(is_mobile())header("Location: ".mHref('my'));
//$chk_u_jumpurl=HOST.'/p1/my_money.php';
$currfields = 'loveb,grade';
require_once 'my_chkuser.php';
$data_loveb    = $row['loveb'];
$data_grade    = $row['grade'];
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/udata.php';
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
$t = (ifint($t,'1-3','1'))?$t:1;
$zeai_cn_menu = 'my_loveb';
?>
<!doctype html><html><head><meta charset="utf-8">
<title>我的<?php echo $_ZEAI['loveB'];?> - <?php echo $_ZEAI['siteName'];?></title>
<link href="../res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/p1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/my.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="css/my_loveb.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/p1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/my_loveb.js?<?php echo $_ZEAI['cache_str'];?>"></script>
</head>
<body>
<?php require_once ZEAI.'p1/my_top.php'; ?>
<div class="main"><div class="mainL"><?php require_once ZEAI.'p1/my_left.php';?></div><div class="mainR">
    <div class="myRM">
        <h1>我的<?php echo $_ZEAI['loveB'];?></h1>
        <div class="tab">
			<?php
            if ($t==2){
				$rt = $db->query("SELECT content,num,endnum,addtime FROM ".__TBL_LOVEB_LIST__." WHERE uid=".$cook_uid." ORDER BY id DESC");
				$total = $db->num_rows($rt);
			}
            ?>
            <a href="<?php echo SELF;?>?t=1"<?php echo ($t==1)?' class="ed"':'';?>>我的<?php echo $_ZEAI['loveB'];?></a>
            <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>><?php echo $_ZEAI['loveB'];?>明细</a>
            <a href="<?php echo SELF;?>?t=3"<?php echo ($t==3)?' class="ed"':'';?>>在线充值</a>
        </div>
         <!-- start C -->
        <div class="myRC">
			<div class="my_loveb">
				<?php
				//我的爱豆
				if($t==1){
				?>
				<style>.my_loveb .ye .LoveB:before{content:'当前<?php echo $_ZEAI['loveB']; ?>'}</style>
				<div class="ye">
                    <div class="boxx">
                        <div class="dt"><i class="ico">&#xe618;</i></div>
                        <div class="dd">
                        	<span class="LoveB"><?php echo $data_loveb; ?><font>个</font></span>
                            <br>
                        	<a class="btn size3 BAI" href="my_loveb.php?t=3">充值</a>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="loveBdetail">
                        ● <?php echo $_ZEAI['loveB']; ?>可用来参与首页会员置顶排名<br>
                        <?php if(in_array('gift',$navarr)){?>● 可以用来赠送对方礼物<br><?php }?>
                        <?php if(in_array('contact',$navarr)){?>● 可以用来查看对方联系方式　<span class="btn size1 BAI" onClick="zeai.div({obj:my_loveb_contactHelp,title:'查看联系方式费用说明',w:360,h:320});">使用说明</span><br><?php }?>
                        <?php if(in_array('chat',$navarr)){?>● 解锁查看回复邮件在线聊天　<span class="btn size1 BAI" onClick="zeai.div({obj:my_loveb_chathelp,title:'解锁聊天费用说明',w:360,h:320});">使用说明</span><br><?php }?>
                        ● 当前费率：<?php echo $_ZEAI['loveBrate'].$_ZEAI['loveB']; ?>=1元<br>
                        <button class="btnA" onClick="supdes=zeai.div({obj:my_loveb_getTip,title:'如何获得<?php echo $_ZEAI['loveB']; ?>？',w:450,h:350});">如何获得<?php echo $_ZEAI['loveB']; ?>？</button>
                    </div>
                </div>
                <?php
				/*************helpDiv Start*************/?>
				<div id="my_loveb_contactHelp" class="helpDiv">
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
					<a class="btn size3 HUANG Mcenter block center" onClick="zeai.openurl_('<?php echo HOST;?>/p1/my_vip.php?jumpurl='+encodeURIComponent(jumpurl))">我要升级会员</a>
				</div>
				<div id="my_loveb_chathelp" class="helpDiv">
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
					<a class="btn size3 HUANG Mcenter block center" onClick="zeai.openurl_('<?php echo HOST;?>/p1/my_vip.php?jumpurl='+encodeURIComponent(jumpurl))">我要升级会员</a>
				</div>
                <div id="my_loveb_getTip" class="helpDiv my_loveb_getTip">
                    <dl><dt>每日签到<?php echo $_ZEAI['loveB']; ?>随机送</dt><dd><button onClick="zeai.msg('请到“我的”首页右上侧签到~',{time:3});setTimeout(function(){zeai.openurl('my.php');},2000);">我要签到</button></dd></dl>
                    <dl><dt>在线充值(1元=<?php echo $_ZEAI['loveBrate'];?>个)</dt><dd><button onClick="zeai.openurl('<?php echo SELF;?>?t=3');">我要充值</button></dd></dl>
                    <?php if(in_array('tg',$navarr)){?>
                        <dl><dt>推荐分享新会员注册奖励</dt><dd><button onClick="zeai.msg('请用手机微信扫描网站底部二维码进入使用',{time:3});setTimeout(function(){supdes.click();zeai.setScrollTop(2000);},2000);">我要推荐</button></dd></dl>
                    <?php }?>
                    
                    <dl><dt>上传头像随机奖励</dt><dd><button onClick="zeai.openurl('<?php echo HOST;?>/p1/my_info.php');">我要上传</button></dd></dl>
                    <dl><dt>上传个人相册随机奖励</dt><dd><button onClick="zeai.openurl('<?php echo HOST;?>/p1/my_photo.php');">我要上传</button></dd></dl>
                    <?php if(@in_array('video',$navarr)){?><dl><dt>上传视频随机奖励</dt><dd><button onClick="zeai.openurl('<?php echo HOST;?>/p1/my_video.php');">我要上传</button></dd></dl><?php }?>
                    
                </div>
				<?php /*************helpDiv End*************/
				//明细
				}elseif($t==2){
					if($total>0){$page_skin=2;$pagemode=4;$pagesize=11;$page_color='#E83191';require_once ZEAI.'sub/page.php';
					?>
                    <table class="tablelist">
                    <tr>
                    <td width="200" class="list_title">结算时间</td>
                    <td class="list_title">结算项目</td>
                    <td width="150" class="list_title center">加减</td>
                    <td width="80" class="list_title center">账户余额(个)</td>
                    </tr>
                    <?php
                    for($i=1;$i<=$pagesize;$i++) {
                        $rows = $db->fetch_array($rt,'num');
                        if(!$rows) break;
                        $content  = $rows[0];
                        $num      = $rows[1];
                        $endnum   = $rows[2];
                        $addtime  = YmdHis($rows[3]);
                        if ($num<0){
                            $numstyle = " C00f";
                        }else{
                            $numstyle = " Cf00";
                            $num = '+'.$num;
                        }
                    ?>
                    <tr>
                    <td width="160" class="C8d S12"><?php echo $addtime;?></td>
                    <td class="S12 C666"><?php echo $content;?></td>
                    <td width="150" class="center"><font class="<?php echo $numstyle; ?>"><?php echo $num;?></font></td>
                    <td width="80" class="center C8d S12"><?php echo $endnum; ?></td>
                    </tr>
                    <?php } ?>
                    </table>
                    <?php
					if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox mypagebox">'.$pagelist.'</div>';
					}else{echo nodatatips('暂无明细内容');}
				}elseif($t==3){?>
                    <form id="zeaiFORM" class="cz">
                    <dl>
                        <dt>充值数量</dt>
                        <dd id="numlist">
                            <em rmb="<?php echo $_VIP['cz_minnum'];?>"><?php echo  $_VIP['cz_minnum']*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
                            <em rmb="50" class="ed"><?php echo 50*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
                            <em rmb="100"><?php echo 100*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
                            <em rmb="500"><?php echo 500*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
                            <em rmb="1000"><?php echo 1000*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
                            <em rmb="2000"><?php echo 2000*$_ZEAI['loveBrate'];?>个<div class="ibox"><h4></h4></div></em>
                        </dd>
                    </dl>
                    <dl style="border:0"><dt>应付金额</dt><dd><span id="price"></span><span id="pricetitle"></span></dd></dl>
                    <div style="text-align:center;margin:20px auto 50px auto"><button type="button" class="btn size4 LV2 W300" id="my_loveb_nextbtn">下一步</button></div>
                    <?php
                        //$jumpurl1 = (!empty($jumpurl))?$jumpurl:HOST.'/p1/my_loveb.php?t=3';
                       // $jumpurl2 = (!empty($jumpurl))?$jumpurl:HOST.'/p1/my_loveb.php?t=3';
                    ?>
                    <input type="hidden" id="return_okurl" value="<?php echo $jumpurl1;?>">
                    <input type="hidden" id="return_nourl" value="<?php echo $jumpurl2;?>">
                    <input type="hidden" id="jumpurl" value="<?php echo $jumpurl;?>">
                    <input type="hidden" id="money" value="0">
                    <input type="hidden" id="kind" value="2">
                    <div class="linebox">
                        <div class="line "></div>
                        <div class="title BAI S14">温馨提醒</div>
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
                        ?><br>
                      <a class="btn size3 BAI" onClick="zeai.openurl('my_vip.php');">立即升级 <i class="ico">&#xe6ab;</i></a>
                    </div>
              </form>
             		<script>
                    var loveBzk = <?php echo $loveb_buy[$data_grade];?>,czlist=numlist.getElementsByTagName("em");
					my_loveb_Fn(czlist);
					o('my_loveb_nextbtn').onclick=my_loveb_nextbtnFn;
                    </script>
				<?php	
				}
            	?>
        	</div>
        </div>
        <!-- end C -->
</div></div></div>
<script>var jumpurl  = '<?php echo $jumpurl;?>';</script>
<?php
require_once ZEAI.'p1/bottom.php';ob_end_flush();
?>
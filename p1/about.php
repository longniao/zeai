<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
$t = (empty($t))?'us':$t;
switch ($t) {
	case 'us':$tt  = '网站介绍';break;
	case 'contact':$tt  = '联系客服';break;
	case 'news':$tt  = '本站公告';break;
	case 'news_detail':
		if(!ifint($fid))alert('暂无内容','-1');
		$row = $db->ROW(__TBL_NEWS__,"title,content,addtime,click","flag=1 AND id=".$fid,"name");
		if ($row){
			$title     = trimhtml(dataIO($row['title'],'out'));
			$kindtitle = dataIO($row['kindtitle'],'out');
			$content   = dataIO($row['content'],'out');
			$addtime   = YmdHis($row['addtime'],'Ymd');
			$click     = $row['click'];
			$db->query("UPDATE ".__TBL_NEWS__." SET click=click+1 WHERE id=".$fid);
		}else{alert('暂无内容','-1');}
		$tt  = $title;
	break;
	case 'clause':$tt = '会员条款/免责声明';break;
}
$nav='index';
?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $tt;?> - <?php echo $_ZEAI['siteName'];?></title>
<link href="<?php echo HOST;?>/res/www_zeai_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/about.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
</head>
<body>
<?php require_once ZEAI.'p1/top.php';?>
<style>
</style>
<div class="about">
    <div class="L S5">
       <div class="logo"><img src="<?php echo $_ZEAI['up2']."/".$_ZEAI['pclogo'];?>"></div>
       <div class="webname">关于我们</div>
       <div class="engname">ABOUT US</div>
		<a href="<?php echo Href('about');?>"<?php echo ($t == 'us')?' class="ed"':'';?>><i class="ico">&#xe610;</i>网站介绍</a>
		<a href="<?php echo Href('kefu');?>"<?php echo ($t == 'contact')?' class="ed"':'';?>><i class="ico">&#xe60e;</i>联系我们</a>
		<a href="<?php echo Href('about_news');?>"<?php echo ($t == 'news' || $t == 'news_detail')?' class="ed"':'';?>><i class="ico">&#xe654;</i>本站公告</a>
		<a href="<?php echo Href('clause');?>"<?php echo ($t == 'clause')?' class="ed"':'';?>><i class="ico">&#xe64b;</i>会员条款</a>
	</div>
    <div class="R S5">
		<h1><?php echo $tt;?></h1>
        <?php if ($t == 'us'){?>
        	<div class="us"><?php $row = $db->ROW(__TBL_NEWS__,"content","id=2");echo ($row)?dataIO($row[0],'out'):nodatatips('暂无信息');?></div>
        <?php }elseif($t == 'clause'){?>
        	<div class="us"><?php $row = $db->ROW(__TBL_NEWS__,"content","id=1");echo ($row)?dataIO($row[0],'out'):nodatatips('暂无信息');?></div>
        <?php }elseif($t == 'news'){?>
        	<div class="news">
				<?php
                $rt=$db->query("SELECT id,title,addtime FROM ".__TBL_NEWS__." WHERE flag=1 AND id>2 AND kind=1 ORDER BY px DESC,id DESC");
                $total = $db->num_rows($rt);
                if ($total > 0) {
                    $page_skin='4_yuan';$pagemode=4;$pagesize=10;$page_color='#E83191';require_once ZEAI.'sub/page.php';
                    for($i=1;$i<=$pagesize;$i++) {
                        $rows2 = $db->fetch_array($rt,'name');
                        if(!$rows2)break;
                        $id   = $rows2['id'];
                        $title  = dataIO($rows2['title'],'out');
                        $addtime   = YmdHis($rows2['addtime'],'Ymd');
                        $echo .= '<a href="'.Href('about_news',$id).'">● '.$title.'<span>'.$addtime.'</span></a>';
                    }
                    echo $echo;
                    if ($total > $pagesize)echo '<div class="clear"></div><div class="pagebox zeaipagebox">'.$pagelist.'</div>';
                }else{echo '<br><br><br><br>'.nodatatips('暂无内容');}?>
			</div>
       <?php }elseif($t == 'news_detail'){?>
            <div class="news_detail">
            	<h6>发布时间：<?php echo $addtime;?>　　<i class="ico">&#xe643;</i> <?php echo $click;?></h6>
            	<?php echo $content;?>
            </div>
        <?php }elseif($t == 'contact'){
			$kf_mob   = dataIO($_ZEAI['kf_mob'],'out');
			$kf_tel   = dataIO($_ZEAI['kf_tel'],'out');
			$kf_qq    = dataIO($_ZEAI['kf_qq'],'out');
			$kf_wx    = dataIO($_ZEAI['kf_wx'],'out'); 
			$kf_wxpic = dataIO($_ZEAI['kf_wxpic'],'out'); 
			$kf_address = dataIO($_ZEAI['kf_address'],'out'); 
			$kf_email = dataIO($_ZEAI['kf_email'],'out'); 
			?>
            <div class="contact">
                <?php if (!empty($kf_tel)){?><li><i class="ico2" style="background-color:#ffba57">&#xe68a;</i> 电话：<?php echo $kf_tel;?></li><?php }?>
                <?php if (!empty($kf_mob)){?><li><i class="ico" style="margin-top:10px;font-size:26px;background-color:#7AD3E9">&#xe627;</i> <span style="margin-top:7px;display:inline-block;vertical-align:top">手机：<?php echo $kf_mob;?></span></li><?php }?>
                <?php if (!empty($kf_email)){?><li><i class="ico2" style="background-color:#c7c0de">&#xe682;</i> 邮箱：<?php echo $kf_email;?></li><?php }?>
                <?php if (!empty($kf_address)){?><li><i class="ico B" style="background-color:#cccdb8">&#xe614;</i> 地址：<?php echo $kf_address;?></li><?php }?>
                <?php if (!empty($kf_qq)){?><li><i class="ico" style="background-color:#51B7EC">&#xe612;</i> QQ：<?php echo $kf_qq;if(!empty($kf_qq)){?><a href="tencent://message/?uin=<?php echo $kf_qq; ?>&Site=<?php echo $_ZEAI['siteName']; ?>&Menu=yes"><img src="<?php echo HOST;?>/res/qq2.gif" alt="客服为您服务" /></a><?php }?></li><?php }?>
                <?php if (!empty($kf_wx)){?><li><i class="ico" style="background-color:#31C93C">&#xe607;</i> 微信：<?php echo $kf_wx;?></li><?php }?>
				<?php if (!empty($kf_wxpic)){?>
                <div class="kfwx">
                    <img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>">
                    <h5>微信扫码加客服微信</h5>
                </div>
                <?php }?>
                <div class="clear"></div>
            </div>
        <?php }?>
	</div>
</div>
<div class="clear"></div>
<?php require_once ZEAI.'p1/bottom.php';?>
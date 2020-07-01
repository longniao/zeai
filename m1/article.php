<?php
require_once '../sub/init.php';
if(!is_mobile())header("Location: ".Href('news'));
require_once ZEAI.'sub/conn.php';
$nodatatips = "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时木有内容呀～～</div>";
if($submitok == 'ajax_get_list')exit(get_list($kindid));
$headertitle = (!empty($kindtitle))?$kindtitle.'-'.$_ZEAI['siteName']:'婚恋学堂-'.$_ZEAI['siteName'];?>
<!doctype html><html><head><meta charset="utf-8">
<title><?php echo $headertitle;?></title>
<?php echo HEADMETA; ?>
<meta name="x5-orientation" content="portrait">
<link href="<?php echo HOST;?>/res/www_zeai_cn.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/m1/css/m1.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="<?php echo HOST;?>/m1/js/m1.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/m1/css/article.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style><?php if($_ZEAI['mob_mbkind']==3){?>.article .kind a.ed i{background-color:#FF6F6F}<?php }?></style>
</head>
<body>
<div class="article">
    <div class="kind fadeInL" id="articlekindbox">
	<?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_NEWS_KIND__." WHERE id>1 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 > 0) {
		$clss1=(!ifint($kind))?' class="ed" ':'';
		echo '<a href="'.wHref('article').'" '.$clss1.'onclick="get_list(0)"><i>最</i><span>最新文章</span></a>';
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
            $kindid   = $rows2[0];
            $kindtitle=dataIO($rows2[1],'out');
			$clss=($kindid==$kind)?' class="ed"':'';
		   $kindtitleS=substr($kindtitle,0,3);?><a href="article.php?kind=<?php echo $kindid;?>&kindtitle=<?php echo $kindtitle;?>"<?php echo $clss;?>><i><?php echo $kindtitleS;?></i><span><?php echo $kindtitle;?></span></a><?php
        }}?>
        <div class="clear"></div>
    </div>
	<div class="articlelist" id="articlelist"><?php echo get_list($kind);?></div>
</div>
<script>
var ifbbs=0,ifpay=0;
function get_list(kindid){
	zeai.listEach(zeai.tag(articlekindbox,'a'),function(obj){
		obj.removeClass('ed');
	});
	o('kind'+kindid).class('ed');
	zeai.ajax({url:'m1/article'+zeai.extname,data:{submitok:'ajax_get_list',kindid:kindid}},function(e){
		articlelist.html(e);
	});
}
</script>
<?php
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";?>
	<script src="<?php echo HOST; ?>/res/jweixin-1.2.0.js"></script>
    <script>
	var share_article_title = '婚恋学堂 - <?php echo dataIO($_ZEAI['siteName'],'out'); ?>';
	var share_article_desc  = '从相亲开始相爱——学习相亲与恋爱知识,解决爱情与婚姻烦恼,树立正确婚恋观,开启幸福生活第一步';
	var share_article_link  = '<?php echo wHref('article'); ?>';
	var share_article_imgurl= '<?php echo $_ZEAI['up2'].'/'.$_ZEAI['logo']; ?>';
	wx.ready(function () {
		wx.onMenuShareAppMessage({title:share_article_title,desc:share_article_desc,link:share_article_link,imgUrl:share_article_imgurl});
		wx.onMenuShareTimeline({title:share_article_title,link:share_article_link,imgUrl:share_article_imgurl});
	});
    </script>
<?php }?>
<script src="<?php echo HOST;?>/m1/js/article.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php
if($_ZEAI['mob_mbkind']==3){?>
<style>#backtop a,#btmKefuBtn{background-color:#FF6F6F}</style>
<?php
	require_once ZEAI.'m3/bottom.php';
}else{
	require_once ZEAI.'m1/bottom.php';
}
function get_list($kindid) {
	global $db,$_ZEAI,$nodatatips;
	$SQL = (ifint($kindid))?" AND kind=".$kindid:'';
	$rt2=$db->query("SELECT id,title,kindtitle,path_s,addtime FROM ".__TBL_NEWS__." WHERE flag=1 AND id>2  ".$SQL." ORDER BY px DESC,id DESC LIMIT 100");
	$total2 = $db->num_rows($rt2);
	if ($total2 > 0) {
		for($j=0;$j<$total2;$j++) {
			$rows2 = $db->fetch_array($rt2,'name');
			if(!$rows2) break;
			$id   = $rows2['id'];
			$title=dataIO($rows2['title'],'out');
			$title = gylsubstr($title,38,0,"utf-8",true);
			$path_s    = $rows2['path_s'];
			$addtime   = YmdHis($rows2['addtime'],'Ymd');
			$kindtitle = dataIO($rows2['kindtitle'],'out');
			$path_s_url= (!empty($path_s))?$_ZEAI['up2'].'/'.$path_s:HOST.'/res/noP.gif';
			$echo .= '<a href="'. wHref('article',$id).'" class="fadeInL">';
			$echo .= '<img src="'.$path_s_url.'">';
			$echo .= '<em>';
			$echo .= '<h4>'.$title.'</h4>';
			$echo .= '<span>'.$kindtitle.'</span><font>'.$addtime.'</font>';
			$echo .= '</em>';
			$echo .= '<div class="clear"></div></a>';
		}
		return $echo;
	}else{
		return $nodatatips;
	}
}
?>
<?php
require_once substr(dirname(__FILE__),0,-2).'sub/init.php';
require_once ZEAI.'sub/conn.php';
if(!iflogin())exit("<html><body><script>window.onload = function (){parent.location.href='".HOST."/p1/login.php';}</script></body></html>");
if(!ifint($uid))alert('请选择要举报的会员','back');
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
switch ($submitok) {
	case 'ajax_picurl_up':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_uid.'_315_');
			if (!up_send($file,$dbname,0,$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'addupdate':
		if ($kind == $cook_315_kind && $content == $cook_315_content)json_exit(array('flag'=>0,'msg'=>'请不要重复提交'));
		if (str_len($content)<1)json_exit(array('flag'=>0,'msg'=>'请如实详细描述事情经过详情'));
		$content= dataIO($content,'in',1000);
		$url    = dataIO($jumpurl,'in',200);
		$picurl = dataIO($picurl,'in',200);
		$kind   = dataIO($kind,'in',20);
		u_pic_reTmpDir_send($picurl,'315');
		$picurl = str_replace('tmp','315',$picurl);
		$db->query("INSERT INTO ".__TBL_315__."  (uid,senduid,kind,content,url,picurl,addtime) VALUES ($uid,$cook_uid,'$kind','$content','$url','$picurl',".ADDTIME.")");
		setcookie("cook_315_kind",$kind,time()+7200000,"/",$_ZEAI['CookDomain']);
		setcookie("cook_315_content",$content,time()+7200000,"/",$_ZEAI['CookDomain']);
		json_exit(array('flag'=>1,'msg'=>'举报成功！我们将审查您的举报信息'));
	break;
}

$row = $db->ROW(__TBL_USER__,"uname,sex,nickname,areatitle,birthday,photo_s,photo_f","id=".$uid,"name");
if ($row){
	$uname      = dataIO($row['uname'],'out');
	$nickname = dataIO($row['nickname'],'out');
	$sex       = $row['sex'];
	$birthday   = $row['birthday'];
	$birthday   = (!ifdate($birthday))?'':$birthday;
	$birthday_str  = (@getage($birthday)<=0)?'':@getage($birthday).'岁 ';
	$areatitle  = dataIO($row['areatitle'],'out');
	$nickname_str = (!empty($nickname))?$nickname:$uname;
	if (!empty($photo_s) && $photo_f == 1){
		$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
		$photo_s_str = '<img class="sexbg'.$sex.'" src="'.$photo_s_url.'">';
	}else{
		$photo_s_url = HOST.'/res/photo_s'.$sex.'.png';
		$photo_s_str = '<img class="sexbg'.$sex.'" src="'.$photo_s_url.'">';
	}
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>举报中心</title>
<link href="<?php echo HOST;?>/res/www_esyyw_cn.css" rel="stylesheet" type="text/css" />
<link href="<?php echo HOST;?>/p1/css/p1.css" rel="stylesheet" type="text/css" />
<script src="<?php echo HOST;?>/res/www_esyyw_cn.js"></script>
<script src="<?php echo HOST;?>/p1/js/p1.js"></script>
<style>
body{margin:0}
.jubao{width:100%;background-color:#fff;padding:10px 0 50px 0;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.jubao .udata{background-color:#f0f0f0;padding:0 0 20px 0}
.jubao .udata ul{background-color:#fff;border-bottom:#f0f0f0 12px solid}
.jubao .udata ul li:last-child{border:0}
.jubao dl{width:100%;margin:0 auto;clear:both;border-bottom:#eee 1px solid;padding:10px 0;text-align:left}
.jubao dl:last-child{border-bottom:0}
.jubao dl dt,.jubao dl dd{line-height:30px}
.jubao dl dt{width:90%;margin:0 5%;font-size:18px;font-weight:bold}
.jubao dl dd{width:90%;margin:0 auto;position:relative;padding:5px 0}
.jubao dl dd.rdo{padding-left:15px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.jubao dl dd.photo_s{padding:10px 0 0 110px;height:80px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.jubao dl dd.photo_s img{position:absolute;left:35px;top:10px;width:60px;heighit:60px;border-radius:30px}
.jubao dl dd textarea{width:100%;height:80px}
.jubao dl dd em{line-height:normal;font-size:12px;color:#8d8d8d;margin:5px 0}
.jubao button{width:90%;margin:20px auto;display:block}
.jubao textarea{width:100%:background-color:#f9f9f9;padding:5px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;resize:none}
::-webkit-input-placeholder {color:#aaa;font-size:14px}
.icoadd,.icoadd img{width:60px;height:60px;display:block}
.icoadd{line-height:60px;border:#dedede 1px solid;font-size:40px;text-align:center;color:#aaa}
</style>
</head>
<body>
<div class="jubao">
    <form id="ZEAI_FORM_315">
        <dl>
            <dt>举报对象</dt>
            <dd class="photo_s"><?php echo $photo_s_str;?><h3><?php echo $nickname_str;?></h3><h6><?php echo $birthday_str.$areatitle;?></h6></dd>
        </dl>
        <dl><dt>举报内容</dt>
            <dd class="rdo">
            <input type="radio" name="kind" value="一夜情" id="jb1" class="radioskin"><label for="jb1" class="radioskin-label"><i class="i1"></i><b class="W100">一夜情</b></label>
            <input type="radio" name="kind" value="花篮托" id="jb2" class="radioskin"><label for="jb2" class="radioskin-label"><i class="i1"></i><b class="W100">花篮托</b></label>
            <input type="radio" name="kind" value="对方借钱" id="jb3" class="radioskin"><label for="jb3" class="radioskin-label"><i class="i1"></i><b class="W100">对方借钱</b></label>
            <input type="radio" name="kind" value="发广告" id="jb4" class="radioskin"><label for="jb4" class="radioskin-label"><i class="i1"></i><b class="W100">发广告</b></label>
            <input type="radio" name="kind" value="性交易" id="jb5" class="radioskin"><label for="jb5" class="radioskin-label"><i class="i1"></i><b class="W100">性交易</b></label>
            <input type="radio" name="kind" value="酒托茶托饭托" id="jb6" class="radioskin"><label for="jb6" class="radioskin-label"><i class="i1"></i><b class="W100">酒托茶托饭托</b></label>
            <input type="radio" name="kind" value="盗窃" id="jb7" class="radioskin"><label for="jb7" class="radioskin-label"><i class="i1"></i><b class="W100">盗窃</b></label>
            <input type="radio" name="kind" value="恶意中伤" id="jb8" class="radioskin"><label for="jb8" class="radioskin-label"><i class="i1"></i><b class="W100">恶意中伤</b></label>
            <input type="radio" name="kind" value="已婚" id="jb9" class="radioskin"><label for="jb9" class="radioskin-label"><i class="i1"></i><b class="W100">已婚</b></label>
            <input type="radio" name="kind" value="中奖信息" id="jb10" class="radioskin"><label for="jb10" class="radioskin-label"><i class="i1"></i><b class="W100">中奖信息</b></label>
            <input type="radio" name="kind" value="冒充管理员" id="jb11" class="radioskin"><label for="jb11" class="radioskin-label"><i class="i1"></i><b class="W100">冒充管理员</b></label>
            <input type="radio" name="kind" value="资料虚假" id="jb12" class="radioskin"><label for="jb12" class="radioskin-label"><i class="i1"></i><b class="W100">资料虚假</b></label>
            <input type="radio" name="kind" value="婚介" id="jb13" class="radioskin"><label for="jb13" class="radioskin-label"><i class="i1"></i><b class="W100">商家婚介</b></label>
            <input type="radio" name="kind" value="其他" id="jb14" class="radioskin"><label for="jb14" class="radioskin-label"><i class="i1"></i><b class="W100">其他</b></label>
            </dd>
		</dl>
        <dl>
            <dt>举报截图</dt>
            <dd>
            	<p class="icoadd" id="jubaopic"><i class="ico">&#xe620;</i></p>
            </dd>
        </dl>
        <dl><dt>详细描述</dt>
        <dd><textarea id="content" name="content" maxlength="1000" class="textarea" placeholder="请如实详细描述事情经过.."></textarea>
        <em>请留下相关详细证据说明，以便我们核查。如果举报属实，将有机会奖励<?php echo $_ZEAI['loveB']; ?>1000以上或免费升级至VIP会员。</em>
        </dd></dl>
    <input type="hidden" name="submitok" value="addupdate" />
    <input type="hidden" name="picurl" id="picurl" value="" />
    <input type="hidden" name="uid" value="<?php echo $uid;?>" />
    <input type="hidden" name="jumpurl" value="<?php echo $_SERVER['HTTP_REFERER'];?>" />
    <button type="button" class="btn size4 HONG" id="tj315">提交</button>
    </form>
</div>
<script>
var upMaxMB = <?php echo $_UP['upMaxMB']; ?>,up2='<?php echo $_ZEAI['up2'];?>/';
photoUp({
	btnobj:jubaopic,
	url:PCHOST+"/315"+zeai.extname,
	submitok:"ajax_picurl_up",
	_:function(rs){
		zeai.msg(0);zeai.msg(rs.msg);
		if (rs.flag == 1){
			jubaopic.html('<img src='+up2+rs.dbname+'>');
			picurl.value=rs.dbname;
		}
	}
});
tj315.onclick=function(){
	if (!zeai.form.ifradio('kind')){zeai.msg('请选择举报内容');return false;}
	var str = o("content").value;
	if(zeai.str_len(str)>1000 || zeai.str_len(str)<2){zeai.msg('详细描述不能为空2~500字',content);return false;}
	zeai.ajax({url:PCHOST+'/315'+zeai.extname,form:ZEAI_FORM_315},function(e){rs=zeai.jsoneval(e);
		zeai.msg(rs.msg,{time:3});
		setTimeout(function(){parent.zeai.iframe(0);},1000);
	});
}
</script>
</body>
</html>
<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if($ifgg==1){
	if(!in_array('news_gg',$QXARR))exit(noauth());
}else{
	if(!in_array('news',$QXARR))exit(noauth());
}
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
if ($ifgg == 1){$kind=1;}
if($submitok=='add_update' || $submitok=='mod_update'){
	if(!ifint($kind))json_exit(array('flag'=>0,'msg'=>'请选择【分类】','focus'=>'kind'));
	if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【文章标题】','focus'=>'title'));
	if(str_len($content)<10)json_exit(array('flag'=>0,'msg'=>'【文章内容】至少要10位长度','focus'=>'content'));
	$kind = intval($kind);
	$click= intval($click);
	$row = $db->ROW(__TBL_NEWS_KIND__,"title","id=".$kind);
	if ($row){
		$kindtitle= dataIO($row[0],'out');
	}else{json_exit(array('flag'=>0,'msg'=>'分类为空，请先去增加','kind'=>'kind'));}
	//
	$title   = dataIO($title,'in',200);
	$content = dataIO($content,'in',50000);
	$content=zeai_cj_cleanhtml($content);
	$addtime = (empty($addtime))?ADDTIME:strtotime($addtime);
}
switch ($submitok) {
	case"ajax_flag":
		if (!ifint($fid))callmsg("forbidden","-1");
		$row = $db->ROW(__TBL_NEWS__,"flag","id>2 AND id=".$fid,"num");
		if(!$row){
			alert_adm("您要操作的文章不存在","-1");
		}else{
			$flag = $row[0];
			$SQL = "";
			switch($flag){
				case"-1":$SQL="flag=1";break;
				case"0":$SQL="flag=1";break;
				case"1":$SQL="flag=-1";break;
			}
			$db->query("UPDATE ".__TBL_NEWS__." SET ".$SQL." WHERE id>2 AND id=".$fid);
			AddLog('【文章管理】状态修改->id:'.$fid);
			json_exit(array('flag'=>1,'msg'=>'设置成功'));
		}
	break;
	case"ajax_pic_path_s_up":
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upMsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$dbname=setpath_s($dbname);
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			AddLog('【文章管理】上传图片->url:'.$dbname);
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case "add_update":
		if(!empty($path_s)){
			adm_pic_reTmpDir_send($path_s,'news');
			adm_pic_reTmpDir_send(smb($path_s,'b'),'news');
			$path_s = str_replace('tmp','news',$path_s);
		}
		$db->query("INSERT INTO ".__TBL_NEWS__." (kind,kindtitle,title,content,path_s,px,addtime,click) VALUES ('$kind','$kindtitle','$title','$content','$path_s',".ADDTIME.",$addtime,$click)");
		AddLog('【文章管理】发布新文章->标题:'.$title);
		json_exit(array('flag'=>1,'msg'=>'增加成功','kind'=>$kind));
	break;
	case"mod_update":
		if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$row = $db->ROW(__TBL_NEWS__,"path_s"," id=".$fid);
		if (!$row)json_exit(array('flag'=>0,'msg'=>'zeai_error_db_fid'.$fid));
		$data_path_s= $row[0];
		$SQL="";
		/******************************************** 主图path_s ********************************************/
		//提交空，数据库有，删老
		if(empty($path_s) && !empty($data_path_s)){
			$B = smb($data_path_s,'b');
			@up_send_admindel($data_path_s.'|'.$B);
		//提交有，数据库无
		}elseif(!empty($path_s) && empty($data_path_s)){
			//上新
			adm_pic_reTmpDir_send($path_s,'news');
			adm_pic_reTmpDir_send(smb($path_s,'b'),'news');
			$path_s = str_replace('tmp','news',$path_s);
		//提交有，数据库有
		}elseif(!empty($path_s) && !empty($data_path_s)){
			//有改动
			if($path_s != $data_path_s){
				//删老
				$B = smb($data_path_s,'b');
				@up_send_admindel($data_path_s.'|'.$B);
				//上新
				adm_pic_reTmpDir_send($path_s,'news');
				adm_pic_reTmpDir_send(smb($path_s,'b'),'news');
				$path_s = str_replace('tmp','news',$path_s);
			}
		}
		
		$db->query("UPDATE ".__TBL_NEWS__." SET kind='$kind',kindtitle='$kindtitle',title='$title',content='$content',path_s='$path_s',addtime='$addtime',click='$click' ".$SQL." WHERE id=".$fid);
		AddLog('【文章管理】修改文章内容->id:'.$fid.'，标题:'.$title);
		json_exit(array('flag'=>1,'msg'=>'修改成功','kind'=>$kind));
	break;
	case"ajax_del":
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'文章不存在或已被删除'));
		$rt = $db->query("SELECT path_s,title FROM ".__TBL_NEWS__." WHERE id>2 AND id=".$fid);
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
			for($i=1;$i<=$total;$i++) {
				$row = $db->fetch_array($rt,'name');
				if(!$row) break;
				$path_s   = $row['path_s'];$title = $row['title'];
				if(!empty($path_s)){
					$B = smb($path_s,'b');@up_send_admindel($path_s.'|'.$B);
				}
			}
			$db->query("DELETE FROM ".__TBL_NEWS__." WHERE id>2 AND id=".$fid);
			AddLog('【文章管理】删除文章->id:'.$fid.'，标题:'.$title);
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($fid))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_NEWS__." SET px=".ADDTIME." WHERE id=".$fid);
		AddLog('【文章管理】置顶文章->id:'.$fid);
		header("Location: ".SELF."?ifgg=".$ifgg);
	break;
	case"mod":
		if (!ifint($fid))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_NEWS__." WHERE id=".$fid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$id      = $row['id'];
			$path_s  = $row['path_s'];
			$kind    = $row['kind'];
			$click   = intval($row['click']);
			$kindtitle = dataIO($row['kindtitle'],'out');
			$title     = dataIO($row['title'],'out');
			$content   = dataIO($row['content'],'out');
			$addtime   = $row['addtime'];
			//if(!ifint($addtime))$addtime=ADDTIME;
		}else{
			alert_adm("该文章不存在！","-1");
		}
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<!-- editor -->
<link rel="stylesheet" href="editor/themes/default/default.css" />
<script charset="utf-8" src="editor/kindeditor.js?1"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js?1"></script>
<script>
var editor;
KindEditor.ready(function(K){
  editor=K.create('textarea[name="content"]',{
	resizeType :1,
	cssData:'body {font-family: "微软雅黑"; font-size: 14px}',
	minWidth : 400,
	allowPreviewEmoticons : true,
	allowImageUpload : true,
	afterBlur:function(){this.sync();},
	items : [
		'undo','redo','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','removeformat', '|', 'insertorderedlist','insertunorderedlist', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull','lineheight','|',
		'selectall','quickformat', '|','image','multiimage','media', '|','plainpaste','wordpaste','hr', 'link', 'unlink','baidumap', '|','clearhtml','source', '|','preview','fullscreen']
  });
});
var up2='<?php echo $_ZEAI['up2'];?>/',upMaxMB=<?php echo $_UP['upMaxMB']; ?>;
</script>
<!--editor end -->
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:900px;margin:20px 20px 50px 20px}
.table0{min-width:900px;width:98%;margin:10px 20px 20px 20px}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
.textarea_k{text-align:left}
.picli{padding:0px}
.picli li{width:100px;height:100px;line-height:100px;border:#eee 1px solid;box-sizing:border-box;cursor:pointer;float:left;margin:10px 15px 10px 0;text-align:center;position:relative}
.picli li.add,.picli li i{background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.picli li.add{background-size:150px 100px;background-repeat:no-repeat;background-position:-2px -2px;border:#dedede 2px dashed}
.picli li:hover{background-color:#f5f7f9}
.picli li img{vertical-align:middle;margin-top:-5px;max-width:98px;max-height:98px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;cursor:zoom-in}
.picli li:hover .img{cursor:zoom-in}
.picli li i{width:20px;height:20px;top:-10px;right:-10px;position:absolute;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3)}
.picli li i:hover{background-position:-100px top;cursor:pointer}
.picli #picmore{display:none}

.pathlist img{margin:0 2px 2px 2px;width:30px;height:30px}
i.top{font-size:18px;color:#FF5722}
.ped{color:#FF5722;border-bottom:2px #FF5722 solid;padding-bottom:5px}

</style>
<body>
<div class="navbox">
	<?php if ($ifgg == 1){$kind=1;?>
        <a class="ed">网站公告</a>
    <?php }else{ ?>
        <a href="news.php" class="ed">文章管理<?php echo '<b>'.$db->COUNT(__TBL_NEWS__," id>2").'</b>';?></a>
        <a href="news_kind.php">文章分类</a>
        <a href="news_bbs.php">文章评论</a>
	<?php }?>
    
    <div class="Rsobox">
    <form name="form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按标题搜索">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="kind" value="<?php echo $kind;?>" />
        <input type="hidden" name="ifgg" value="<?php echo $ifgg;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>     
    </div>
  
<div class="clear"></div></div>
<div class="fixedblank"></div>
<!---->
<?php
if ($submitok == 'add_cjgzhwz'){?>
<table class="table Mtop20" style="width:1111px;margin:35px 0 100px 20px">
    <form id="Www_zeai_cn_form">
    <tr><td class="tdL" style="width:150px">公众号文章链接地址</td><td class="tdR C8d"><textarea name="zeaiurl" rows="5" class="textarea_k size2 W98_" style="height:120px;line-height:150%" id="zeaiurl"></textarea><span class="tips2">请输入电脑端地址栏公众号文章url地址</span></td></tr>
    <tr>
      <td class="tdL">&nbsp;</td>
      <td class="tdR"><button class="btn size3 HUANG3" type="button" id="submit_add" />一键采集</button>
        <input name="submitok" type="hidden" value="add_zeai_cn__cj_gzhwz" />
        <input name="uu" type="hidden" value="<?php echo $session_uid;?>">
        <input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
      </td>
    </tr>
    </form>
</table>	
<script>
submit_add.onclick=function(){
	zeai.confirm('确定要采集么，提交后将直接入库，包括内容里面图片自动批量下载到服务器本地，为了性能优化，可能会过虑一些垃圾代码，文章样式会有一点偏差，入库成功后请自行二次编辑',function(){
		zeai.msg('采集中，请勿关闭窗口',{time:30});
		zeai.ajax({url:'<?php echo $_ZEAI['up2'];?>/zeai_cn__cj_gzhwz'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				zeai.msg(0);zeai.msg('入库成功!');
				setTimeout(function(){zeai.openurl('news'+zeai.ajxext+'submitok=mod&fid='+rs.fid+'&ifgg=<?php echo $ifgg;?>');},1000);
			}else{
				zeai.msg(0);zeai.msg(rs.msg);
			}
		});
	});
}
</script>
	
<?php	
/************************************** 【发布】【修改】 add **************************************/
}elseif($submitok == 'add' || $submitok == 'mod'){?>
<!--【发布】-->

    <table class="table W90_" style="margin:15px 0 100px 20px">
    <form id="Www_zeai_cn_form">

    <tr><td class="tdL">所属分类</td><td class="tdR">
    <select name="kind" id="kind" class="W200 size2" required>
	<?php
	if ($ifgg == 1){
		$sql=" WHERE id=1 ";
		$kind = 1;
		$ggtitle='公告';
	}else{
		$sql=" WHERE id<>1 ";
		$ggtitle='文章';
	}
    $rt2=$db->query("SELECT id,title FROM ".__TBL_NEWS_KIND__.$sql." ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('请先增加文章分类','news_kind.php');
    } else {
    ?>
    <option value="">选择分类</option>
    <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
			$clss=($kind==$rows2[0])?' selected':'';
            echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
        }
    }
    ?></select>    
    
    </td></tr>
    <tr><td class="tdL"><?php echo $ggtitle;?>标题</td><td class="tdR C8d"><input name="title" id="title" type="text" class="input size2 W500" maxlength="100" value="<?php echo $title;?>" /></td></tr>
    <tr>    
      <td class="tdL"><?php echo $ggtitle;?>主图<br><font class="Cf00 S12">无图前台将不显示</font></td>
      <td class="tdR">
        <div class="picli" id="picli_path">
          <li class="add" id="path_add"></li>
          <?php if(!empty($path_s)){
				echo '<li><img src="'.$_ZEAI['up2'].'/'.$path_s.'"><i></i></li>';
			}?>
        </div>
        <?php if(empty($path_s)){?><br><br><span class="tips" >请配上一张主图，让<?php echo $ggtitle;?>更具吸引力</span><?php }?>
      </td></tr>    
    <tr><td class="tdL">发布时间</td><td class="tdR C8d"><input name="addtime" id="addtime" type="text" class="input size2 W200" maxlength="50" value="<?php echo YmdHis($addtime);?>" /><span class="tips">留空将自动生成当前时间</span></td></tr>
    <tr><td class="tdL">阅读量</td><td class="tdR C8d"><input name="click" id="click" type="text" class="input size2 W80" maxlength="8" value="<?php echo intval($click);?>" /><span class="tips">建议初始200</span></td></tr>

    <tr><td class="tdL">详细内容</td><td class="tdR"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">如果从公众号编辑器或外部网页或Word里拷入内容请先过虑垃圾代码，请点击下方 <img src="images/cclear.png" class="picmiddle"> 图标，然后插入文字内容</font><textarea name="content" id="content" class="textarea_k" style="width:100%;height:500px" ><?php echo $content;?></textarea></td></tr>
    <input name="path_s" id="path_s" type="hidden" value="" />
    <?php if ($submitok == 'mod'){?>
      <input name="submitok" type="hidden" value="mod_update" />
      <input name="fid" type="hidden" value="<?php echo $fid;?>" />
    <?php }else{ ?>
      <input name="submitok" type="hidden" value="add_update" />
    <?php }?>
    <div class="savebtnbox"><button class="btn size3 HUANG3" type="button" id="submit_add" />保存并发布</button></div>
    </form>
    </table>
<script>
	<?php if($submitok=='mod'){?>
	window.onload=function(){
		path_s_mod();
		function path_s_mod(){
			var i=zeai.tag(picli_path,'i')[0],img=zeai.tag(picli_path,'img')[0];
			if(zeai.empty(i))return;
			path_add.hide();
			var src=img.src.replace(up2,'');
			path_s.value=src;
			i.onclick = function(){
				zeai.confirm('亲~~确认删除么？',function(){
					img.parentNode.remove();path_add.show();path_s.value='';
				});
			}
			img.onclick = function(){parent.piczoom(up2+src.replace('_s.','_b.'));}
		}
	}
	<?php }?>
	
		zeai.photoUp({
			btnobj:path_add,
			upMaxMB:upMaxMB,
			url:"news"+zeai.extname,
			submitok:"ajax_pic_path_s_up",
			end:function(rs){
				zeai.msg(0);zeai.msg(rs.msg);
				if (rs.flag == 1){
					picli_path.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');
					path_s.value=rs.dbname;
					path_add.hide();
					var i=zeai.tag(o(picli_path),'i')[0],img=zeai.tag(o(picli_path),'img')[0];
					i.onclick = function(){
						zeai.confirm('亲~~确认删除么？',function(){
							img.parentNode.remove();path_add.show();path_s.value='';
						});
					}
					img.onclick = function(){parent.piczoom(up2+rs.dbname.replace('_s.','_b.'));}
				}
			}
		});
		submit_add.onclick=function(){
			zeai.confirm('确定检查无误发布提交么？',function(){
				zeai.ajax({url:'news'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);
					if(rs.flag==1){
						zeai.msg(rs.msg,{time:1});
						setTimeout(function(){zeai.openurl('news'+zeai.ajxext+'kind='+rs.kind+'&ifgg=<?php echo $ifgg;?>');},1000);
					}else{
						zeai.msg(rs.msg,{time:1,focus:o(rs.focus)});
					}
				});
			});
		}
    </script>
<!--【发布 修改 结束】-->
<?php
/************************************** 【列表】 list **************************************/
exit;}else{
	?>
    <table class="table0">
    <tr>
    <td width="290" align="left" class="border0" >
    <?php 	$ggtitle = ($ifgg == 1)?'公告':'文章';?>
        <button type="button" class="btn size2" onClick="zeai.openurl('news.php?submitok=add&kind=<?php echo $kind;?>&ifgg=<?php echo $ifgg;?>')"><i class="ico add">&#xe620;</i>发布新<?php echo $ggtitle;?></button>
        <?php if ($ifgg != 1){?>
        　　<button type="button" class="btn size2" onClick="zeai.openurl('news.php?submitok=add_cjgzhwz&kind=<?php echo $kind;?>&ifgg=<?php echo $ifgg;?>')"><i class="ico add">&#xe607;</i>采集公众号文章</button><?php }?>
    </td>
    <td class="S14">
        <?php
		if ($ifgg != 1){
        $rt2=$db->query("SELECT id,title FROM ".__TBL_NEWS_KIND__." WHERE id<>1 ORDER BY px DESC,id DESC");
        $total2 = $db->num_rows($rt2);
        if ($total2 <= 0) {
            alert_adm('请先增加文章分类','news_kind.php');
        } else {
            for($j=1;$j<=$total2;$j++) {
                $rows2 = $db->fetch_array($rt2,'num');
                if(!$rows2) break;
                $kind2 = $rows2[0];
                $kindtitle2=dataIO($rows2[1],'out');
                $clss=($kind==$kind2)?' class="ped"':' class="C999"';?>
            <a href="news.php?kind=<?php echo $kind2;?>"<?php echo $clss;?>><?php echo $kindtitle2;?></a>
        <?php
			if($j!=$total2)echo '　｜　';
		}}}?>

    </td>
    </tr>
    </table>
    <?php
	$SQL="";
	$Skeyword = trimm($Skeyword);
	if (!empty($Skeyword))$SQL = " AND ( title LIKE '%".dataIO(trimm($Skeyword),'in')."%' ) ";
	if(ifint($kind))$SQL.=" AND kind=".$kind;
	$rt = $db->query("SELECT id,path_s,flag,title,click,addtime,kindtitle,kind FROM ".__TBL_NEWS__." WHERE id>2 ".$SQL."  ORDER BY px DESC LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无内容<br><a class='aHUANGed' href='news.php?ifgg=".$ifgg."&submitok=add&kind=".$kind."'>发布新".$ggtitle."</a></div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
		?>
		<form id="www_zeai_cn_FORM">
		<table class="tablelist">
		<tr>
		<th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label" style="display:none"><i class="i1"></i></label></th>
		<th width="50" align="left">ID</th>
		<th width="50" align="left">置顶</th>
		<th width="80" align="left">主图</th>
		<th width="300" align="left">文章标题</th>
		<th width="100" align="center">所属分类</th>
		<th width="60" align="center">阅读量</th>
		<th>&nbsp;</th>
		<th width="70" align="center">发布时间</th>
		<th width="100" align="center">状态</th>
		<th width="60" align="center">修改</th>
		<th width="60" align="center">删除</th>
		</tr>
		<?php
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$id        = $rows['id'];
			$path_s    = $rows['path_s'];
			$flag      = $rows['flag'];
			$kind      = $rows['kind'];
			$click     = $rows['click'];
			$addtime   = YmdHis($rows['addtime']);
			$title     = dataIO($rows['title'],'out');
			$kindtitle = dataIO($rows['kindtitle'],'out');
			//
			if(!empty($Skeyword)){
				$truename = str_replace($Skeyword,'<font class="Cf00 B">'.$Skeyword.'</font>',$truename);
			}
			if(!empty($path_s)){
				$path_s_url = $_ZEAI['up2'].'/'.$path_s;
				$path_s_str = '<img src="'.$path_s_url.'">';
			}else{
				$path_s_url = '';
				$path_s_str = '';
			}
			$href = Href('news',$id);
		?>
		<tr id="tr<?php echo $id;?>">
		<td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
		<td width="50" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
		<td width="50" align="left"><a href="<?php echo "news.php?fid=".$id; ?>&ifgg=<?php echo $ifgg;?>&submitok=ding"><img src="images/ding.gif" border="0" title="置顶" /></a></td>
		<td width="80" align="left" style="padding:10px 0">
        	<?php if (empty($path_s_url)){?>
          <a href="javascript:;" class="pic60 ">无图</a>
            <?php }else{ ?>
            <a href="javascript:;" class="pic60 " onClick="parent.piczoom('<?php echo smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a>
			<?php }?>
        </td>
		<td width="300" align="left" class="C999">
        <div class="S14 C000"><a href="<?php echo Href('news',$id);?>" target="_blank"><?php echo $title;?></a></div ></td>
		<td width="100" align="center"><?php if (!empty($kindtitle)){?><a href="news.php?kind=<?php echo $kind;?>&ifgg=<?php echo $ifgg;?>" class="aHUI"><?php echo $kindtitle;?></a><?php }?></td>
		<td width="60" align="center"><?php echo $click;?></td>
		<td align="left" >&nbsp;</td>
		<td width="70" align="center" class="C999"><?php echo $addtime;?></td>
		<td width="100" align="center" class="C999">
	<?php if($flag==-1){?><a clsid="<?php echo $id;?>" class="aLAN flag" title="点击恢复">锁定</a><?php }?>
	<?php if($flag==1){?><a clsid="<?php echo $id;?>" class="aLV flag" title="点击锁定">正常</a><?php }?>
        </td>
		<td width="60" align="center">
          <a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>"></a>
		</td>
		<td width="60" align="center"><a clsid="<?php echo $id; ?>" class="delico" title='删除'></a></td>
		</tr>
		<?php } ?>
		</table>
        <?php if ($total > $pagesize){?>
        <div class="listbottombox" style="text-align:center">
            <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label" style="display:none"><i class="i1"></i></label>　
            <input type="hidden" name="submitok" id="submitok" value="" />
            <?php echo '<div class="pagebox">'.$pagelist.'</div>'; ?>
        </div>
        <?php }?>
</form>
		<script>
		var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
		zeai.listEach('.editico',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.openurl('news.php?submitok=mod&ifgg=<?php echo $ifgg;?>&fid='+id);
			}
		});
		zeai.listEach('.delico',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.confirm('★请慎重★　确定真的要删除么？',function(){
					zeai.ajax({url:'news.php?submitok=ajax_del&ifgg=<?php echo $ifgg;?>&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.flag',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.ajax({url:'news.php?submitok=ajax_flag&ifgg=<?php echo $ifgg;?>&fid='+id},function(e){
					rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
				});
			}
		});
		</script>
		<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }}?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
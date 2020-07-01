<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('party',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';

if($submitok=='add_update' || $submitok=='mod_update'){
	if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【活动名称】','focus'=>'title'));
	if(empty($hdtime))json_exit(array('flag'=>0,'msg'=>'请输入【活动时间区间】','focus'=>'hdtime'));
	if(empty($address))json_exit(array('flag'=>0,'msg'=>'请输入【活动地点】','focus'=>'address'));
	if (!ifdate($jzbmtime,'Y-m-d H:i:s'))json_exit(array('flag'=>0,'msg'=>'请输入正确格式【截止报名时间】','focus'=>'jzbmtime'));
	$jzbmtime = strtotime($jzbmtime);
	if($jzbmtime < ADDTIME && $submitok=='add_update' )json_exit(array('flag'=>0,'msg'=>'【截止报名时间】要大于现在的时间吧^_^','focus'=>'jzbmtime'));
	$hdtime= dataIO($hdtime,'in',100);
	$address = dataIO($address,'in',150);
	$num_n = intval($num_n);
	$num_r = intval($num_r);
	$rmb_n = floatval($rmb_n);
	$rmb_r = floatval($rmb_r);
	$content=zeai_cj_cleanhtml($content);
	$content  = dataIO($content,'in',90000);
	$ifpay = ($ifpay==1)?1:0;
}

switch ($submitok) {
	case"ajax_pic_path_s_up":
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upMsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$dbname=setpath_s($dbname);
			$newpic = $_ZEAI['up2']."/".$dbname;
			if (!ifpic($newpic))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case "add_update":
		if(!empty($path_s)){
			adm_pic_reTmpDir_send($path_s,'party');
			adm_pic_reTmpDir_send(getpath_smb($path_s,'b'),'party');
			$path_s = str_replace('tmp','party',$path_s);
		}
		if(!empty($pathlist)){
			$ARR=explode(',',$pathlist);
			if (count($ARR) >= 1 && is_array($ARR)){
				$pathlist=array();
				foreach ($ARR as $V) {
					adm_pic_reTmpDir_send($V,'party');
					adm_pic_reTmpDir_send(getpath_smb($V,'b'),'party');
					$_s = str_replace('tmp','party',$V);
					$pathlist[]=$_s;
				}
				$pathlist = implode(',',$pathlist);
			}
		}
		$db->query("INSERT INTO ".__TBL_PARTY__." (title,hdtime,jzbmtime,address,num_n,num_r,rmb_n,rmb_r,ifpay,content,path_s,pathlist,addtime,px) VALUES ('$title','$hdtime','$jzbmtime','$address','$num_n','$num_r','$rmb_n','$rmb_r','$ifpay','$content','$path_s','$pathlist',".ADDTIME.",".ADDTIME.")");
		$id = intval($db->insert_id());
		AddLog('【交友活动】->发布新活动【'.$title.'（id:'.$id.'）】');
		json_exit(array('flag'=>1,'msg'=>'发布成功'));
	break;
	case"mod_update":
		if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$row = $db->ROW(__TBL_PARTY__,"path_s,pathlist","id=".$fid);
		if (!$row)exit(JSON_ERROR);
		$data_path_s= $row[0];$data_pathlist= $row[1];
		/******************************************** 主图path_s ********************************************/
		//提交空，数据库有，删老
		if(empty($path_s) && !empty($data_path_s)){
			$B = getpath_smb($data_path_s,'b');
			@up_send_admindel($data_path_s.'|'.$B);
		//提交有，数据库无
		}elseif(!empty($path_s) && empty($data_path_s)){
			//上新
			adm_pic_reTmpDir_send($path_s,'party');
			adm_pic_reTmpDir_send(getpath_smb($path_s,'b'),'party');
			$path_s = str_replace('tmp','party',$path_s);
		//提交有，数据库有
		}elseif(!empty($path_s) && !empty($data_path_s)){
			//有改动
			if($path_s != $data_path_s){
				//删老
				$B = getpath_smb($data_path_s,'b');
				@up_send_admindel($data_path_s.'|'.$B);
				//上新
				adm_pic_reTmpDir_send($path_s,'party');
				adm_pic_reTmpDir_send(getpath_smb($path_s,'b'),'party');
				$path_s = str_replace('tmp','party',$path_s);
			}
		}
		/******************************************** 批量 list ********************************************/
		//提交空，数据库有，删老
		if(empty($pathlist) && !empty($data_pathlist)){
			$ARR=explode(',',$data_pathlist);
			foreach ($ARR as $S){
				$B = getpath_smb($S,'b');@up_send_admindel($S.'|'.$B);
			}
		//提交有，数据库无
		}elseif(!empty($pathlist) && empty($data_pathlist)){
			//上新
			$ARR=explode(',',$pathlist);
			$pathlist=array();
			foreach ($ARR as $V) {
				adm_pic_reTmpDir_send($V,'party');
				adm_pic_reTmpDir_send(getpath_smb($V,'b'),'party');
				$_s         = str_replace('tmp','party',$V);
				$pathlist[] = $_s;
			}
			$pathlist = implode(',',$pathlist);
		//提交有，数据库有
		}elseif(!empty($pathlist) && !empty($data_pathlist)){
			//有改动
			if($pathlist != $data_pathlist){
				$ARR = explode(',',$pathlist);
				$pathlist = array();
				//循环新列表
				foreach ($ARR as $V) {
					//新上传，上新
					if(strstr($V,'/tmp/')){
						adm_pic_reTmpDir_send($V,'party');
						adm_pic_reTmpDir_send(getpath_smb($V,'b'),'party');
						$_s = str_replace('tmp','party',$V);
						$pathlist[]=$_s;
					//老图，直接赋值
					}else{
						$pathlist[]=$V;
					}
				}
				$pathlist = implode(',',$pathlist);
				//循环老库，处理多图被删除的部分
				$ARR2=explode(',',$data_pathlist);
				foreach ($ARR2 as $V2) {
					//不在新列表，删之
					if(!in_array($V2,$ARR)){
						$B = getpath_smb($V2,'b');@up_send_admindel($V2.'|'.$B);
					}
				}
			}
		}
		$db->query("UPDATE ".__TBL_PARTY__." SET title='$title',hdtime='$hdtime',jzbmtime='$jzbmtime',address='$address',num_n='$num_n',num_r='$num_r',rmb_n='$rmb_n',rmb_r='$rmb_r',ifpay='$ifpay',content='$content',path_s='$path_s',pathlist='$pathlist' WHERE id=".$fid);
		AddLog('【交友活动】->修改活动【'.$title.'（id:'.$fid.'）】');
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case"ajax_del":
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'活动不存在或已被删除'));
		$rt = $db->query("SELECT path_s,pathlist,title FROM ".__TBL_PARTY__." WHERE id=".$fid);
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
			for($i=1;$i<=$total;$i++) {
				$row = $db->fetch_array($rt,'name');
				if(!$row) break;
				$path_s   = $row['path_s'];
				$pathlist = $row['pathlist'];
				$title = $row['title'];
				if(!empty($path_s)){
					$B = getpath_smb($path_s,'b');@up_send_admindel($path_s.'|'.$B);
				}
				if(!empty($pathlist)){
					$ARR=explode(',',$pathlist);
					foreach ($ARR as $V) {
						$B = getpath_smb($V,'b');@up_send_admindel($V.'|'.$B);
					}
				}
			}
			$db->query("DELETE FROM ".__TBL_PARTY_BBS__." WHERE fid=".$fid);
			$db->query("DELETE FROM ".__TBL_PARTY_USER__." WHERE fid=".$fid);
			$db->query("DELETE FROM ".__TBL_PARTY_SIGN__." WHERE fid=".$fid);
			$db->query("DELETE FROM ".__TBL_PARTY__." WHERE id=".$fid);
			AddLog('【交友活动】->删除活动【'.$title.'（id:'.$fid.'）】');
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	
	case"partyflag_update":
		if (!ifint($fid))exit('活动不存在或已被删除');
		$flag=intval($flag);
		$db->query("UPDATE ".__TBL_PARTY__." SET flag=$flag WHERE id=".$fid);
		//header("Location: ".SELF."?submitok=partyflag&fid=".$fid);
		AddLog('【交友活动】->修改状态，id:'.$fid);
		alert_adm('修改成功',SELF."?submitok=partyflag&fid=".$fid);
	break;
	case"SSSSSSSSSSS":
	
	
	break;
	case"ding":
		if (!ifint($fid))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_PARTY__." SET px=".ADDTIME." WHERE id=".$fid);
		AddLog('【交友活动】->置顶活动，id:'.$fid);
		header("Location: ".SELF);
	break;

	
	case"mod":
		if (!ifint($fid))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_PARTY__." WHERE id=".$fid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$path_s   = $row['path_s'];
			$pathlist = $row['pathlist'];
			$jzbmtime = $row['jzbmtime'];
			$flag     = $row['flag'];
			$ifpay = $row['ifpay'];
			$hdtime= $row['hdtime'];
			$address = dataIO($row['address'],'out');
			$title = dataIO($row['title'],'out');
			  $num_n = $row['num_n'];
			$num_r = $row['num_r'];
			$rmb_n = $row['rmb_n'];
			$rmb_r = $row['rmb_r'];
			$content  = dataIO($row['content'],'out');
		}else{
			alert_adm("该活动不存在！","-1");
		}
	break;
}
require_once ZEAI.'cache/udata.php';
$extifshow = json_decode($_UDATA['extifshow'],true);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<!-- editor -->
<link rel="stylesheet" href="editor/themes/default/default.css?<?php echo $_ZEAI['cache_str'];?>" />
<script charset="utf-8" src="editor/kindeditor.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js?<?php echo $_ZEAI['cache_str'];?>"></script>
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
.tablelist{min-width:1600px;margin:20px 20px 50px 20px}
.table0{min-width:1600px;width:98%;margin:10px 20px 20px 20px}
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
.timestyle{display:inline-block;font-size:12px;margin:0 4px;color:#fff;border-radius:3px;padding:0 6px;height:18px;line-height:18px;text-align:center;background-color:#A7CAB2}
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
</style>
<?php
?>
<body>
<?php if ($submitok != 'partyflag'){?>
<div class="navbox">
	<a href="party.php" class="ed">活动管理<?php echo '<b>'.$db->COUNT(__TBL_PARTY__).'</b>';?></a>
	<a href="party_bbsall.php">活动评论管理</a>

  <div class="Rsobox">
    <form name="form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按活动名称搜索">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>     
    </div>
  
<div class="clear"></div></div>
<div class="fixedblank"></div>
<?php }?>
<!---->
<?php
/************************************** 【发布】【修改】 add **************************************/
if ($submitok == 'add' || $submitok == 'mod'){?>
<!--【发布】-->

    <table class="table W95_ Mtop20" style="float:left;margin:15px 0 100px 20px">
    <form id="Www_zeai_cn_form">
    <tr>
      <td class="tdL">活动名称</td><td class="tdR"><input name="title" id="title" type="text" class="input size2 W400" maxlength="100" value="<?php echo $title;?>" /></td></tr>
    <tr><td class="tdL">活动时间区间</td><td class="tdR C8d"><input name="hdtime" id="hdtime" type="text" class="input size2 W400" maxlength="100" value="<?php echo $hdtime;?>"  autocomplete="off" /></td></tr>
    
    <tr><td class="tdL">截止报名时间</td><td class="tdR">
      <input name="jzbmtime" id="jzbmtime" type="text" class="input size2 W400" maxlength="100" value="<?php echo YmdHis($jzbmtime);?>"  autocomplete="off" /><span class="tips">请选择大于现在1天以上的日期</span>
    </td></tr>
    <tr><td class="tdL">活动地点</td><td class="tdR">
      <input name="address" id="address" type="text" class="input size2 W400"  maxlength="100" value="<?php echo $address;?>" /><span class="tips">举办活动的地点</span>
    </td></tr>    
    <tr><td class="tdL">邀请人数</td><td class="tdR">
    
    男 <input name="num_n" id="num_n" type="text" maxlength="5" class="input size2 W50" value="<?php echo intval($num_n);?>"> 人　　
    女 <input name="num_r" id="num_r" type="text" maxlength="5" class="input size2 W50" value="<?php echo intval($num_r);?>"> 人　
    <span class="tips">填0为不限</span>

	</td></tr>
    <tr><td class="tdL">活动费用</td><td class="tdR">
    
    男 <input name="rmb_n" id="rmb_n" type="text" class="input size2 W50" maxlength="5" value="<?php echo $rmb_n;?>"> 元　　
    女 <input name="rmb_r" id="rmb_r" type="text" class="input size2 W50" maxlength="5" value="<?php echo $rmb_r;?>"> 元　
    <span class="tips">填0为免费</span>
    
    </td></tr>
    <tr>
    <tr><td class="tdL">在线支付活动费用</td><td class="tdR">
    
    <input type="checkbox" name="ifpay" id="ifpay" class="switch" value="1"<?php echo ($ifpay == 1)?' checked':'';?>><label for="ifpay" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
    <span class="tips">开启后，可在线支付报名费</span>
    
    </td></tr>
    <tr>    
    <td class="tdL">活动主图片<br><font class="Cf00 S12">无图前台将不显示</font></td>
    <td class="tdR">
        <div class="picli" id="picli_path">
        	<li class="add" id="path_add"></li>
			<?php if(!empty($path_s)){
				echo '<li><img src="'.$_ZEAI['up2'].'/'.$path_s.'"><i></i></li>';
			}?>
        </div>
      </td></tr>
    <tr>
      <td class="tdL">更多图片<br>支持批量上传</td>
      <td class="tdR">
        <div class="picli" id="picli_pathlist">
        	<li class="add" id="pathlist_add"></li>
			<?php
            if(!empty($pathlist)){
                $ARR=explode(',',$pathlist);
                $pathlist=array();
                foreach ($ARR as $V) {
                   echo '<li><img src="'.$_ZEAI['up2'].'/'.$V.'"><i></i></li>';
				}
            }?>      
        </div>
      </td></tr>
    <tr><td class="tdL">活动详细内容</td><td class="tdR"><img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle;color:#999">如果从公众号编辑器或外部网页或Word里拷入内容请先过虑垃圾代码，请点击下方 <img src="images/cclear.png" class="picmiddle"> 图标，然后插入文字内容</font><textarea name="content" id="content" class="textarea_k" style="width:100%;height:600px" >
<?php if (empty($content)){?>
【活动主题】<br><br><br><br>
【活动简介】<br><br><br><br>
【活动亮点】<br><br><br><br>
【报名流程】<br><br><br><br>
【赞助商家】<br><br><br><br>
【免责声明】<br><br><br><br>
【商务合作】
<?php }else{echo $content;}?>
    </textarea></td></tr>
    <tr>
      <td class="tdL">&nbsp;</td>
      <td class="tdR"><button class="btn size3 HUANG3" type="button" id="submit_add" />保存并发布</button>
      <input name="path_s" id="path_s" type="hidden" value="" />
      <input name="pathlist" id="pathlist" type="hidden" value="" />
      <?php if ($submitok == 'mod'){?>
          <input name="submitok" type="hidden" value="mod_update" />
          <input name="fid" type="hidden" value="<?php echo $fid;?>" />
      <?php }else{ ?>
          <input name="submitok" type="hidden" value="add_update" />
      <?php }?>
      </td>
    </tr>
    </form>
    </table>
<script>
	<?php if($submitok=='mod'){?>
	window.onload=function(){
		path_s_mod();end();
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
			url:"party"+zeai.extname,
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
		zeai.photoUp({
			btnobj:pathlist_add,
			upMaxMB:upMaxMB,
			url:"party"+zeai.extname,
			multiple:8,
			submitok:"ajax_pic_path_s_up",
			end:function(rs){end();},
			li:function(rs){
				zeai.msg(0);zeai.msg(rs.msg);
				if (rs.flag == 1){picli_pathlist.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');}
			}
		});
		function end(){
			var i=zeai.tag(picli_pathlist,'i'),img=zeai.tag(picli_pathlist,'img');
			if(zeai.empty(i))return;
			for(var k=0;k<img.length;k++) {
				(function(k){
					var src=img[k].src;
					img[k].onclick = function(){parent.piczoom(src.replace('_s.','_b.'));}
				})(k);
			}
			for(var k=0;k<i.length;k++) {
				(function(k){
					i[k].onclick = function(){
						var thiss=this;
						zeai.confirm('亲~~确认删除么？',function(){
							thiss.parentNode.remove();
							pathlistReset();
						});
					}
				})(k);
			}
			function pathlistReset(){
				var img=zeai.tag(picli_pathlist,'img'),pathlist=[],src;
				for(var k=0;k<img.length;k++){
					var src=img[k].src.replace(up2,'');
					pathlist.push(src);
				}
				o('pathlist').value=pathlist.join(",");
			}
			pathlistReset();
		}
		submit_add.onclick=function(){
			zeai.confirm('确定检查无误发布提交么？',function(){
				//o('content').value = zeai.clearhtml(o('content').value);
				zeai.ajax({url:'party'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);
					if(rs.flag==1){
						zeai.msg(rs.msg,{time:1});
						setTimeout(function(){zeai.openurl('party'+zeai.extname);},1000);
					}else{
						zeai.msg(rs.msg,{time:1,focus:o(rs.focus)});
					}
				});
			});
		}
    </script>
<!--【发布 修改 结束】-->
<?php

/************************************** 【SSSSSSS】SSSSSS **************************************/
}elseif($submitok == 'partyflag'){
	if (!ifint($fid))alert_adm("参数错误","-1");
	$rt = $db->query("SELECT flag FROM ".__TBL_PARTY__." WHERE id=".$fid);
	if($db->num_rows($rt)){
		$row  = $db->fetch_array($rt,'num');
		$flag = $row[0];
	}else{
		alert_adm("该活动不存在！","-1");
	}
	?>

    <form name="ZEAI_FORM" method="POST" action="<?php echo SELF;?>">
    <table class="table Mtop20" style="width:150px">
    <tr>
    <td class=" S16 C8d" style=";padding:25px 30px"">
<input type="radio" name="flag" id="flag0" class="radioskin" value="0"  <?php if($flag==0){echo'checked="checked"';}?>><label for="flag0" class="radioskin-label"><i></i><b class="W80 S14" style="color:#999">隐藏</b></label><br><br>
<input type="radio" name="flag" id="flag1" class="radioskin" value="1"  <?php if($flag==1){echo'checked="checked"';}?>><label for="flag1" class="radioskin-label"><i></i><b class="W80 S14" style="color:#349ae1">报名中</b></label><br><br>
<input type="radio" name="flag" id="flag2" class="radioskin" value="2"  <?php if($flag==2){echo'checked="checked"';}?>><label for="flag2" class="radioskin-label"><i></i><b class="W80 S14" style="color:#f60">进行中</b></label><br><br>
<input type="radio" name="flag" id="flag3" class="radioskin" value="3"  <?php if($flag==3){echo'checked="checked"';}?>><label for="flag3" class="radioskin-label"><i></i><b class="W80 S14" style="color:#090">圆满结束</b></label>
    </td>
    </tr>
    <tr>
    <td align="center"><input class="btn size3" type="submit" value="修改" />
    <input type="hidden" name="submitok" value="partyflag_update" />
    <input type="hidden" name="fid" value="<?php echo $fid;?>" /></td>
    </tr>
    </table>
    </form>
    

<?php
exit;
/************************************** 【列表】 list **************************************/
}else{
	?>
    <table class="table0">
    <tr>
    <td width="200" align="left" class="border0" >
    <button type="button" class="btn size2" onClick="zeai.openurl('party.php?submitok=add')"><i class="ico add">&#xe620;</i>发布新活动</button>
    </td>
    <td>
    </td>
    <td width="300" align="right">&nbsp;</td>
    </tr>
    </table>
    <?php
	$SQL="";
	$Skeyword = trimm($Skeyword);
	if (!empty($Skeyword))$SQL = " WHERE ( title LIKE '%".dataIO(trimm($Skeyword),'in')."%' ) ";

	$rt = $db->query("SELECT id,title,flag,jzbmtime,addtime,bmnum,bbsnum,signnum,path_s,pathlist FROM ".__TBL_PARTY__.$SQL." ORDER BY px DESC LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
		?>
		<form id="www_zeai_cn_FORM">
		<table class="tablelist">
		<tr>
		<th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label" style="display:none"><i class="i1"></i></label></th>
		<th width="50" align="left">ID</th>
		<th width="50" align="left">置顶</th>
		<th width="130" align="left">主图</th>
		<th align="left">活动名称</th>
		<th width="120" align="left">活动状态管理</th>
		<th width="80">报名管理</th>
		<th width="80">评论管理</th>
		<th width="100">现场签到管理</th>
		<th width="300">照片列表</th>
		<th width="100" align="center" >发布时间</th>
		<th width="60" align="center">修改</th>
		<th width="60" align="center">删除</th>
		</tr>
		<?php
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$id  = $rows['id'];
			$path_s   = $rows['path_s'];
			$pathlist = $rows['pathlist'];
			$jzbmtime = $rows['jzbmtime'];
			$flag     = $rows['flag'];
			$signnum  = $rows['signnum'];
			$bmnum    = $rows['bmnum'];
			$bbsnum = $rows['bbsnum'];
			$addtime  = YmdHis($rows['addtime']);
			//
			$title  = dataIO($rows['title'],'out');
			if(!empty($Skeyword)){
				$title = str_replace($Skeyword,'<font class="Cf00 B">'.$Skeyword.'</font>',$title);
			}
			if(!empty($path_s)){
				$path_s_url = $_ZEAI['up2'].'/'.$path_s;
				$path_s_str = '<img src="'.$path_s_url.'">';
			}else{
				$path_s_url = '';
				$path_s_str = '';
			}
			$href = Href('party',$id);
		?>
		<tr id="tr<?php echo $id;?>">
		<td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
		<td width="50" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
		<td width="50" align="left"><a href="<?php echo SELF."?fid=".$id; ?>&submitok=ding"><img src="images/ding.gif" border="0" title="置顶" /></a></td>
		<td width="130" align="left" >
			<?php if (empty($path_s_url)){?>
            <a href="javascript:;" class="pic100 pic100bd0">无图</a>
            <?php }else{ ?>
            <a href="javascript:;" class="pic100 pic100bd0" onClick="parent.piczoom('<?php echo getpath_smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a>
            <?php }?>
        </td>
		<td align="left" class="S16 C000">
        <a href="<?php echo $href; ?>" target="_blank"><?php echo $title; ?></a>
        <div class="S12" style="margin-top:10px">
		<?php
        $d1  = ADDTIME;
        $d2  = $jzbmtime;
        $totals  = ($d2-$d1);
        $day     = intval( $totals/86400 );
        $hour    = intval(($totals % 86400)/3600);
        $hourmod = ($totals % 86400)/3600 - $hour;
        $minute  = intval($hourmod*60);
        if ($rows['flag'] == 2)$totals = -1;
        if (($totals) > 0) {
            if ($day > 0){
                $outtime = "还剩<span class=timestyle>$day</span>天";
            } else {
                $outtime = "还剩";
            }
            $outtime .= "<span class=timestyle>$hour</span>小时<span class=timestyle>$minute</span>分钟";
        } else {
            $outtime = '<font class="C999 S18">报名已经结束</font>';
        }
        echo '<font class="C666">'.$outtime.'</font>';
        ?>        
        </div>
        </td>
		<td width="120" align="left"  class="S14">
		<?php 
        switch ($flag){ 
            case 0:echo "<a class='aHUIed partyflag' title='修改活动状态' clsid='".$id."' title2='".urlencode(strip_tags($title))."'>未审核</a>";break;
            case 1:echo "<a class='aLANed partyflag' title='修改活动状态' clsid='".$id."' title2='".urlencode(strip_tags($title))."'>正在报名中</a>";break;
            case 2:echo "<a class='aHUANGed partyflag' title='修改活动状态' clsid='".$id."' title2='".urlencode(strip_tags($title))."'>活动进行中</a>";break;
            case 3:echo "<a class='aLVed partyflag' title='修改活动状态' clsid='".$id."' title2='".urlencode(strip_tags($title))."'>圆满结束</a>";break;
        }
        ?>        
        </td>
		<td width="80" align="left" ><a title="报名管理" clsid="<?php echo $id;?>" class="aHUI bmadm" title2="<?php echo urlencode(strip_tags($title));?>"><?php echo ($bmnum>0)?'<font class="Cf00 FArial S14">'.$bmnum.'</font>人':$bmnum;?></a></td>
		<td width="80" align="left" ><a title="评论管理" clsid="<?php echo $id;?>" class="aHUI bbsadm" title2="<?php echo urlencode(strip_tags($title));?>"><?php echo ($bbsnum>0)?'<font class="Cf00 FArial S14">'.$bbsnum.'</font>条':$bbsnum;?></a></td>
		<td width="100" align="left" ><a title="签到管理" clsid="<?php echo $id;?>" class="aHUI signadm" title2="<?php echo urlencode(strip_tags($title));?>"><?php echo ($signnum>0)?'<font class="Cf00 FArial S14">'.$signnum.'</font>人':$signnum;?></a></td>
		<td width="300" align="left" >
        <div class="pathlist">
        <?php
		if(!empty($pathlist)){
			$ARR=explode(',',$pathlist);
			$pathlist=array();
			foreach ($ARR as $V) {
				?>
          <a href="javascript:;" class="zoom" onClick="parent.piczoom('<?php echo getpath_smb($_ZEAI['up2'].'/'.$V,'b'); ?>');"><img src="<?php echo $_ZEAI['up2'].'/'.$V;?>"></a>
                <?php
			}
		}
		?>
        </div>
        
        
        </td>
		<td width="100" align="center" class="C999"><?php echo $addtime;?></td>
		<td width="60" align="center">
		  <a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>"></a>
		</td>
		<td width="60" align="center"><a clsid="<?php echo $id; ?>" class="delico" title='删除'></a></td>
		</tr>
		<?php } ?>
		<tfoot><tr>
		<td colspan="13">
		<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label" style="display:none"><i class="i1"></i></label>　
		<input type="hidden" name="submitok" id="submitok" value="" />
		<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
		</td>
		</tr></tfoot>
		</table>
		</form>
		<script>
		var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
		zeai.listEach('.editico',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.openurl('party.php?submitok=mod&fid='+id);
			}
		});
		zeai.listEach('.delico',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				zeai.confirm('★请慎重★　将同步删除报名会员信息、图片、评论等所有相关内容一起删除，无法找回，确定真的要删除么？',function(){
					zeai.ajax({url:'party.php?submitok=ajax_del&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.signadm',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				var title=obj.getAttribute("title2");
				zeai.iframe('【'+decodeURIComponent(title)+'】签到管理','party_sign.php?fid='+id,600,500);
			}
		});
		zeai.listEach('.partyflag',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				var title=obj.getAttribute("title2");
				zeai.iframe('【'+decodeURIComponent(title)+'】状态管理','party.php?submitok=partyflag&fid='+id,450,360);
			}
		});
		zeai.listEach('.bmadm',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				var title=obj.getAttribute("title2");
				zeai.iframe('【'+decodeURIComponent(title)+'】报名管理','party_user.php?fid='+id,800,600);
			}
		});
		zeai.listEach('.bbsadm',function(obj){
			obj.onclick = function(){
				var id = parseInt(obj.getAttribute("clsid"));
				var title=obj.getAttribute("title2");
				zeai.iframe('【'+decodeURIComponent(title)+'】评论管理','party_bbs.php?fid='+id,700,600);
			}
		});
		</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }}?>
<br><br><br>
<script src="laydate/laydate.js"></script>
<script>
laydate.render({
	theme: 'molv'
	,elem: '#hdtime'
	,type: 'datetime'
	,range: '～'
	,format: 'yyyy年M月d日 H时m分'
}); 
laydate.render({
	theme: 'molv'
	,elem: '#jzbmtime'
	,type: 'datetime'
}); 
</script>

<?php require_once 'bottomadm.php';?>
<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';

if(!empty($submitok)){
	if(!in_array('crm_news',$QXARR))exit(noauth());
}

//门店权限
if(!in_array('crm',$QXARR)){
	$SQL=" AND agentid=$session_agentid";
}else{$SQL="";}
//门店权限结束

require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
if($submitok=='add_update' || $submitok=='mod_update'){
	if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【标题】','focus'=>'title'));
	if(str_len($content)<10)json_exit(array('flag'=>0,'msg'=>'【内容】至少要10位长度','focus'=>'content'));
	//
	$title   = dataIO($title,'in',200);
	$content = dataIO($content,'in',50000);
	//
	if(!in_array('crm',$QXARR)){
		if(!ifint($agentid))json_exit(array('flag'=>0,'msg'=>'请选择【门店】','focus'=>'agentid'));
	}
	$agentid=intval($agentid);
	if(ifint($agentid)){
		$row = $db->ROW(__TBL_CRM_AGENT__,"title","id=".$agentid);
		if ($row){
			$agenttitle= dataIO($row[0],'out');
		}else{json_exit(array('flag'=>0,'msg'=>'【门店】为空，请先去增加并设置【开启】状态'));}
	}
}
switch ($submitok) {
	case"ajax_flag":
		if (!ifint($fid))callmsg("forbidden","-1");
		$row = $db->ROW(__TBL_CRM_NEWS__,"flag","id=".$fid.$SQL,"num");
		if(!$row){
			alert_adm("您要操作的通知公告不存在","-1");
		}else{
			$flag = $row[0];
			$SQL = "";
			switch($flag){
				case"-1":$SQL="flag=1";break;
				case"0":$SQL="flag=1";break;
				case"1":$SQL="flag=-1";break;
			}
			$db->query("UPDATE ".__TBL_CRM_NEWS__." SET ".$SQL." WHERE id=".$fid.$SQL);
			json_exit(array('flag'=>1,'msg'=>'设置成功'));
		}
	break;
	case "add_update":
		$db->query("INSERT INTO ".__TBL_CRM_NEWS__." (agentid,agenttitle,kind,kindtitle,title,content,path_s,px,addtime) VALUES ('$agentid','$agenttitle',1,'公告','$title','$content','$path_s',".ADDTIME.",".ADDTIME.")");
		json_exit(array('flag'=>1,'msg'=>'增加成功','kind'=>$kind));
	break;
	case"mod_update":
		if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$db->query("UPDATE ".__TBL_CRM_NEWS__." SET agentid='$agentid',agenttitle='$agenttitle',title='$title',content='$content',path_s='$path_s' ".$SQL." WHERE id=".$fid);
		json_exit(array('flag'=>1,'msg'=>'修改成功','kind'=>$kind));
	break;
	case"ajax_del":
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'通知公告不存在或已被删除'));
		$db->query("DELETE FROM ".__TBL_CRM_NEWS__." WHERE id=".$fid.$SQL);
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($fid))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_CRM_NEWS__." SET px=".ADDTIME." WHERE id=".$fid.$SQL);
		header("Location: ".SELF);
	break;
	case"mod":
		if (!ifint($fid))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_CRM_NEWS__." WHERE id=".$fid.$SQL);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$id      = $row['id'];
			$title     = dataIO($row['title'],'out');
			$content   = dataIO($row['content'],'out');
			$addtime   = dataIO($row['addtime'],'out');
			$agentid   = $row['agentid'];
			$agenttitle= dataIO($row['agenttitle'],'out');
		}else{
			alert_adm("该通知公告不存在！","-1");
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
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">

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
</style>

<body>
<?php if (!ifint($newsid)){?>
    <div class="navbox">
		<a href="crm_news.php"<?php echo (empty($kind))?' class="ed"':'';?>>全部通知公告<?php if(empty($kind))echo '<b>'.$db->COUNT(__TBL_CRM_NEWS__,"1=1 ".$SQL).'</b>';?></a>
	<div class="Rsobox"></div>
    <div class="clear"></div></div>
    <div class="fixedblank"></div>
<?php }?>
<?php
/************************************** 正文 **************************************/
if (ifint($newsid)){
	$rt = $db->query("SELECT * FROM ".__TBL_CRM_NEWS__." WHERE id=".$newsid.$SQL);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt,'name');
		$click     = $row['click'];
		$title     = dataIO($row['title'],'out');
		$content   = dataIO($row['content'],'out');
		$addtime   = YmdHis($row['addtime'],'YmdHi');
		$agenttitle = dataIO($row['agenttitle'],'out');
		$agenttitle = (!empty($agenttitle))?'门店：'.$agenttitle.'　　':'';
		?>
        <table border="0" align="center" style="width:90%;margin-bottom:80px">
          <tr>
            <td height="40" align="center" valign="bottom" class="S18 B C000"><?php echo $title;?></td>
          </tr>
          <tr>
            <td height="35" align="center" class="C999 S12" style="border-bottom:#eee 1px solid;padding-bottom:15px"><?php echo $agenttitle;?>发布时间：<?php echo $addtime;?>　　阅读：<?php echo $click;?>次</td>
          </tr>
          <tr>
            <td align="left" class="lineH200 S14 C000" style="padding-top:20px"><?php echo $content;?></td>
          </tr>
        </table>

		<?php
		$db->query("UPDATE ".__TBL_CRM_NEWS__." SET click=click+1 WHERE id=".$newsid);
	}else{
		alert_adm("该通知公告不存在！","-1");
	}
	require_once 'bottomadm.php';
	exit;
}
?>



<!---->
<?php
/************************************** 【发布】【修改】 add **************************************/
if ($submitok == 'add' || $submitok == 'mod'){?>
<!--【发布】-->

    <table class="table W1200 Mtop20" style="margin:15px 0 100px 20px">
    <form id="Www_zeai_cn_form">
    
    <tr><td class="tdL">门店</td><td class="tdR">
    <?php if(in_array('crm',$QXARR)){?>
    <select name="agentid" id="agentid" class="W200 size2" required>
	<?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_CRM_AGENT__." WHERE flag=1 ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        alert_adm('【门店】为空，请先去增加并设置【开启】状态','crm_agent.php');
    } else {
    ?>
    <option value="">选择门店</option>
    <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
			$clss=($agentid==$rows2[0])?' selected':'';
            echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
        }
    }
    ?></select>
    <?php }else{ ?>
    <input name="agentid" type="hidden" value="<?php echo $session_agentid;?>" />
    <?php echo $session_agenttitle;?>
    <?php }?>
    
    
    <tr><td class="tdL">标题</td><td class="tdR C8d"><input name="title" id="title" type="text" class="input size2 W400" maxlength="100" value="<?php echo $title;?>" /></td></tr>    

    <tr><td class="tdL">内容</td><td class="tdR"><textarea name="content" id="content" class="textarea_k" style="width:100%;height:500px" ><?php echo $content;?></textarea></td></tr>
    <tr>
      <td class="tdL">&nbsp;</td>
      <td class="tdR">
      <input name="path_s" id="path_s" type="hidden" value="" />
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
    
<br><br><br><br><div class="savebtnbox"><button type="button" id="submit_add" class="btn size3 HUANG3">保存并发布</button></div>
    
    
<script>
	
		submit_add.onclick=function(){
			zeai.confirm('确定检查无误发布提交么？',function(){
				zeai.ajax({url:'crm_news'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);
					if(rs.flag==1){
						zeai.msg(rs.msg,{time:1});
						setTimeout(function(){zeai.openurl('crm_news'+zeai.ajxext+'kind='+rs.kind);},1000);
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
	?><div class="clear"></div>
    <table class="table0">
    <tr>
    <td width="200" align="left" class="border0" >
    <button type="button" class="btn size2" onClick="zeai.openurl('crm_news.php?submitok=add&kind=<?php echo $kind;?>')"><i class="ico add">&#xe620;</i>发布新通知公告</button>
    </td>
    <td>
    </td>
    <td width="300" align="right">    <form name="form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按通知公告名称搜索">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="kind" value="<?php echo $kind;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>  </td>
    </tr>
    </table>
    <?php
	$Skeyword = trimm($Skeyword);
	if (!empty($Skeyword))$SQL = " AND ( title LIKE '%".dataIO(trimm($Skeyword),'in')."%' ) ";
	if(ifint($kind))$SQL.=" AND kind=".$kind;
	
	$rt = $db->query("SELECT id,path_s,flag,title,click,addtime,agenttitle FROM ".__TBL_CRM_NEWS__." WHERE 1=1 ".$SQL."  ORDER BY px DESC LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无内容<br><a class='aHUANGed' href='crm_news.php?submitok=add&kind=".$kind."'>发布新通知公告</a></div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
		?>
		<form id="www_zeai_cn_FORM">
		<table class="tablelist">
		<tr>
		<th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label" style="display:none"><i class="i1"></i></label></th>
		<th width="50" align="left">ID</th>
		<th width="50" align="left">置顶</th>
		<th width="150" align="left">门店</th>
		<th width="300" align="left">通知公告标题</th>
		<th width="50" align="center">阅读人数</th>
		<th>&nbsp;</th>
		<th width="70" align="center">发布时间</th>
		<th width="60" align="center">修改</th>
		<th width="60" align="center">删除</th>
		</tr>
		<?php
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$id        = $rows['id'];
			$flag      = $rows['flag'];
			$click     = $rows['click'];
			$addtime   = YmdHis($rows['addtime']);
			$title     = dataIO($rows['title'],'out');
			$agenttitle     = dataIO($rows['agenttitle'],'out');
			//
			if(!empty($Skeyword)){
				$truename = str_replace($Skeyword,'<font class="Cf00 B">'.$Skeyword.'</font>',$truename);
			}
		?>
		<tr id="tr<?php echo $id;?>">
		<td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
		<td width="50" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
		<td width="50" align="left"><a href="<?php echo "crm_news.php?fid=".$id; ?>&submitok=ding"><img src="images/ding.gif" border="0" title="置顶" /></a></td>
		<td width="150" align="left"><?php echo $agenttitle;?></td>
		<td width="300" align="left" class="C999">
        <a href="javascript:;" class="newsdetail" newsid="<?php echo $id;?>"><div class="S14 C000"><?php echo $title;?></div ></a></td>
		<td width="50" align="center"><?php echo $click;?></td>
		<td align="left" >&nbsp;</td>
		<td width="70" align="center" class="C999"><?php echo $addtime;?></td>
		<td width="60" align="center">
		  <a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>"></a>
		</td>
		<td width="60" align="center"><a clsid="<?php echo $id; ?>" class="delico" title='删除'></a></td>
		</tr>
		<?php } ?>
		<tfoot><tr>
		<td colspan="10">
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
			var id = parseInt(obj.getAttribute("clsid"));
			obj.onclick = function(){
				zeai.openurl('crm_news.php?submitok=mod&fid='+id);
			}
		});
		zeai.listEach('.delico',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			obj.onclick = function(){
				zeai.confirm('★请慎重★　确定真的要删除么？',function(){
					zeai.ajax({url:'crm_news.php?submitok=ajax_del&fid='+id},function(e){
						rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
		});
		zeai.listEach('.flag',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			obj.onclick = function(){
				zeai.ajax({url:'crm_news.php?submitok=ajax_flag&fid='+id},function(e){
					rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
				});
			}
		});
		
		
		zeai.listEach('.newsdetail',function(obj){
			obj.onclick = function(){
				var newsid = parseInt(obj.getAttribute("newsid"));
				zeai.iframe('通知公告','crm_news.php?newsid='+newsid,700,550);
			}
		});
		
		</script>
		<script src="js/zeai_tablelist.js"></script>
<?php }}?>




<br><br><br>
<?php require_once 'bottomadm.php';?>
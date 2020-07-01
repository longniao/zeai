<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('form',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
if ($submitok == "addupdate" || $submitok == "modupdate") {
	if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【表单标题】'));
	if(!is_array($form_data) || count($form_data)==0 || empty($form_data) )json_exit(array('flag'=>0,'msg'=>'请选择【资料选项】'));
	if(str_len($content)>5000)json_exit(array('flag'=>0,'msg'=>'【详细内容】请小于5000字节'));
	if(empty($form_data_px))json_exit(array('flag'=>0,'msg'=>'排序列不能为空','focus'=>'title'));
	$title       = trimm(dataIO($title,'in',200));
	$content     = dataIO($content,'in');
	$form_data   = implode(",",$form_data);;
	$form_data_px= dataIO($form_data_px,'in',50000);
	$privateC= dataIO($privateC,'in',50000);
	$bz          = dataIO($bz,'in',500);
	$wxshareT    = dataIO($wxshareT,'in',200);
	$wxshareC    = dataIO($wxshareC,'in',500);
	$stopurl     = dataIO($stopurl,'in',500);
	$defpass     = trim(dataIO($defpass,'in',20));
	$agree_reg     = intval($agree_reg);
	$agree_wxshare = intval($agree_wxshare);
	$rz_mob        = intval($rz_mob);
	$rz_identity   = intval($rz_identity);
	$rz_photo      = intval($rz_photo);
	$rz_price      = floatval($rz_price);
	$ifadmlist     = (!empty($ifadmlist))?implode(",",$ifadmlist):'';
	if($submitok == "addupdate"){
		if(!empty($path_s)){
			adm_pic_reTmpDir_send($path_s,'form');
			adm_pic_reTmpDir_send(smb($path_s,'b'),'form');
			$path_s = str_replace('tmp','form',$path_s);
		}
		$db->query("INSERT INTO ".__TBL_FORM__."  (title,content,path_s,form_data,form_data_px,bz,agree_reg,agree_wxshare,rz_mob,rz_identity,rz_photo,ifadmlist,addtime,wxshareT,wxshareC,rz_price,stopurl,defpass,privateC) VALUES ('$title','$content','$path_s','$form_data','$form_data_px','$bz','$agree_reg','$agree_wxshare','$rz_mob','$rz_identity','$rz_photo','$ifadmlist',".ADDTIME.",'$wxshareT','$wxshareC','$rz_price','$stopurl','$defpass','$privateC')");	
		$id = intval($db->insert_id());
		AddLog('新增【表单采集】ID:'.$id.'，标题:'.$title);
	}elseif($submitok=='modupdate'){
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'ID跑路了，请联系开发者【择。爱】官方客服'));
		$row = $db->ROW(__TBL_FORM__,"path_s"," id=".$id,"num");
		if (!$row)json_exit(array('flag'=>0,'msg'=>'zeai_error_db_fid'.$id));
		$data_path_s= $row[0];
		$SQL="";
		/*********** ZEAI.path_s ***********/
		//提交空，数据库有，删老
		if(empty($path_s) && !empty($data_path_s)){
			@up_send_admindel($data_path_s.'|'.smb($data_path_s,'b'));
		//提交有，数据库无
		}elseif(!empty($path_s) && empty($data_path_s)){
			//上新
			adm_pic_reTmpDir_send($path_s,'form');
			adm_pic_reTmpDir_send(smb($path_s,'b'),'form');
			$path_s = str_replace('tmp','form',$path_s);
		//提交有，数据库有
		}elseif(!empty($path_s) && !empty($data_path_s)){
			//有改动
			if($path_s != $data_path_s){
				//删老
				@up_send_admindel($data_path_s.'|'.smb($data_path_s,'b'));
				//上新
				adm_pic_reTmpDir_send($path_s,'form');adm_pic_reTmpDir_send(smb($path_s,'b'),'form');
				$path_s = str_replace('tmp','form',$path_s);
			}
		}
		AddLog('修改【表单采集】ID:'.$id.'，标题:'.$title);
		$db->query("UPDATE ".__TBL_FORM__." SET title='$title',content='$content',form_data='$form_data',form_data_px='$form_data_px',path_s='$path_s',bz='$bz',agree_reg='$agree_reg',agree_wxshare='$agree_wxshare',rz_mob='$rz_mob',rz_identity='$rz_identity',rz_photo='$rz_photo',ifadmlist='$ifadmlist',wxshareT='$wxshareT',wxshareC='$wxshareC',rz_price='$rz_price',stopurl='$stopurl',defpass='$defpass',privateC='$privateC' WHERE id=".$id);
	}
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok=='ajax_pic_path_s_up'){
	if (ifpostpic($file['tmp_name'])){
		$dbname = setphotodbname('tmp',$file['tmp_name'],$session_uid.'_');
		if (!up_send($file,$dbname,0,$_UP['upMsize'],'1500*1500'))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
		$dbname=setpath_s($dbname);
		AddLog('【表单采集】上传图片->url:'.$dbname);
		json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$dbname));
	}else{
		json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
	}
}elseif($submitok=='ajax_del'){
	if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'表单不存在或已被删除'));
	$rt = $db->query("SELECT path_s,title FROM ".__TBL_FORM__." WHERE id>2 AND id=".$id);
	$total = $db->num_rows($rt);
	if ($total > 0 ) {
		for($i=1;$i<=$total;$i++) {
			$row = $db->fetch_array($rt,'name');
			if(!$row) break;
			$path_s = $row['path_s'];$title = $row['title'];
			if(!empty($path_s)){$B = smb($path_s,'b');@up_send_admindel($path_s.'|'.$B);}
		}
		$db->query("DELETE FROM ".__TBL_FORM__." WHERE id=".$id);
		$db->query("DELETE FROM ".__TBL_FORM_U__." WHERE fid=".$id);
		AddLog('删除【表单采集】->ID:'.$id.'，标题:'.$title);
	}
	json_exit(array('flag'=>1,'msg'=>'删除成功'));
}elseif($submitok=='ajax_flag'){
	if (!ifint($id))callmsg("ID跑路了","-1");
	$row = $db->ROW(__TBL_FORM__,"flag","id=".$id,"num");
	if(!$row){
		alert_adm("您要操作的信息不存在","-1");
	}else{
		$flag = $row[0];
		$SQL = "";
		switch($flag){
			case"-1":$SQL="flag=1";break;
			case"0":$SQL="flag=1";break;
			case"1":$SQL="flag=-1";break;
		}
		$db->query("UPDATE ".__TBL_FORM__." SET ".$SQL." WHERE id=".$id);
		AddLog('【表单采集】状态修改->id:'.$id);
		json_exit(array('flag'=>1,'msg'=>'设置成功'));
	}
}
$data_disable=array('tag1','tag2','age');
function data_data_title($data_data,$f) {foreach($data_data as $v){if($v['fieldname'] == $f)return $v['title'];}}
$rt = $db->query("SELECT fieldname,title FROM ".__TBL_UDATA__." WHERE flag=1 ORDER BY px DESC,id DESC");
while($tmprows = $db->fetch_array($rt,'name')){
	if (strstr($tmprows['fieldname'],'crm_') || in_array($tmprows['fieldname'],$data_disable) )continue;
	$data_data[]=$tmprows;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/Sortable1.6.1.js"></script>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
td.tdLbgHUI{background-color:#eee}
.stepbox .stepli{margin-top:5px}
.stepbox .stepli li{width:100px;padding:5px 10px;float:left;margin:5px;border:#ddd 1px solid;cursor:move;background-color:#f8f8f8;border-radius:3px}
.tablelist{min-width:900px;margin:20px 20px 50px 20px}
.table0{min-width:900px;width:98%;margin:10px 20px 20px 20px}
.tips,.tips2{font-size:12px}
.ifadmlist{padding:0 10px;border:#eee 1px solid;background-color:#F5F6FA;margin-bottom:10px;text-align:center;line-height:150%;}
.ifadmlist li{width:150px;text-align:left;height:100px;text-align:center;display:inline-block}
.ifadmlist li img{margin-right:6px;display:block;margin:15px auto 5px auto}
.ifadmlist .br{margin:15px 0;padding-top:15px;border-top:#eee 1px solid;line-height:200%;color:#999}
.listdatabox{padding:10px 0}
.listdatabox li{display:inline-block;padding:1px 5px;border:#ddd 1px solid;margin:3px;border-radius:3px;color:#888}
</style>
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
<body>
<div class="navbox">
<a href="form.php" class='ed'>表单采集项目</a>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<?php if ($submitok == 'add' || $submitok == 'mod'){
if ($submitok == 'add'){
	$rt = $db->query("SELECT fieldname FROM ".__TBL_UDATA__." WHERE flag=1 ORDER BY px DESC,id DESC","num");
	$total = $db->num_rows($rt);
	if ($total>0){
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			if (strstr($rows['fieldname'],'crm_') || in_array($rows['fieldname'],$data_disable) )continue;
			$form_data_px[]= $rows['fieldname'];
		}
	}else{
		alert_adm("UDATA表为空","-1");
	}
	$form_data=array();
	$ifadmlist=array();
	$rz_mob        = 1;
	$agree_reg     = 1;
	$agree_wxshare = 1;
}else{
	if(!ifint($id))alert_adm("ID去向不明","-1");
	$row = $db->ROW(__TBL_FORM__,"*","id=".$id,"name");
	if ($row){
		$id           = $row['id'];
		$title        = dataIO($row['title'],'out');
		$content      = dataIO($row['content'],'out');
		$bz           = dataIO($row['bz'],'out');
		$wxshareT     = dataIO($row['wxshareT'],'wx');
		$wxshareC     = dataIO($row['wxshareC'],'wx');
		$privateC     = dataIO($row['privateC'],'wx');
		$form_data    = $row['form_data'];
		$form_data_px = $row['form_data_px'];
		$path_s  = $row['path_s'];
		$addtime = $row['addtime'];
		$flag    = $row['flag'];
		$agree_reg     = $row['agree_reg'];
		$agree_wxshare = $row['agree_wxshare'];
		$rz_mob        = $row['rz_mob'];
		$rz_identity   = $row['rz_identity'];
		$rz_photo      = $row['rz_photo'];
		$rz_price      = $row['rz_price'];
		$form_data    = explode(',',$form_data);
		$form_data_px = explode(',',$form_data_px);
		$ifadmlist    = explode(',',$row['ifadmlist']);
		$stopurl      = dataIO($row['stopurl'],'out');
		$defpass      = dataIO($row['defpass'],'out');
	}
}
?>
<form id="ZEAI_FORM">
    <table class="table" style="width:1111px;margin:20px 20px 100px 20px">
    <tr><td class="tdL"><font class="Cf00">*</font> 表单标题</td><td class="tdR"><input name="title" id="title" type="text" class="input size2 W600" value="<?php echo $title;?>" maxlength="200"   autocomplete="off" /></td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font> 顶部图片</td><td class="tdR C8d">
        <div class="picli100" id="picli_path">
          <li class="add" id="path_add"></li>
          <?php if(!empty($path_s)){
                echo '<li><img src="'.$_ZEAI['up2'].'/'.$path_s.'"><i></i></li><br><br><br><span class="tips" >先删除后更换</span>';
            }?>
        </div>
      <?php if(empty($path_s)){?><br><span class="tips" ><br><br>请配上一张海报图，更具吸引力，推荐尺寸：900×500像数，图片格式.gif/.jpg/.png，推荐65%品质jpg</span><?php }?>
    </td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font> 资料选项</td><td class="tdR">
    <?php
    if (is_array($form_data_px) && count($form_data_px)>0){?>
        <div class="navdiy">
            <dd class="stepbox" id="stepbox1">
                <div class="stepli" title="按住不放可拖动顺序">
                <?php	
                foreach($form_data_px as $F){
                    $T=data_data_title($data_data,$F);
                    ?>
                    <li><input type="checkbox" name="form_data[]" id="form_data_<?php echo $F;?>" class="checkskin form_data_li" value="<?php echo $F;?>"<?php echo (in_array($F,$form_data))?' checked':'';?>><label for="form_data_<?php echo $F;?>" class="checkskin-label"><i class="i1"></i><b><?php echo $T;?></b></label></li>
                <?php }?>
                </div>
            </dd>
        </div>
    <?php
    }
    ?>
    <div class="clear"></div>
    <div style="margin-top:10px"><img src="images/!.png" width="14" height="14" valign="middle"> <font class="picmiddle S12 C999">选中请打勾；可以按住不放拖动项目调整前后显示顺序</font></div>        
    </td></tr>
    <tr><td class="tdL">手机验证</td><td class="tdR"><input type="checkbox" name="rz_mob" id="rz_mob" class="switch" value="1"<?php echo ($rz_mob == 1)?' checked':'';?>><label for="rz_mob" class="switch-label"><i></i><b>开启</b><b>关闭</b></label><br>
    <font class="tips2">【开启】后会进行短信验证码校对手机真实性，只有上方资料选项打勾才会生效<br>【关闭】后将将不显示</font></td></tr>
    
    <tr><td class="tdL tdLbgHUI">自助实名认证</td><td class="tdR"><input type="checkbox" name="rz_identity" id="rz_identity" class="switch" value="1"<?php echo ($rz_identity == 1)?' checked':'';?>><label for="rz_identity" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　<img src="images/!.png" width="14" height="14" valign="middle"> <font class="picmiddle S12 C999">必须上面【手机验证开启】和系统总设置【运营商-实名认证开启】才会生效</font><br><font class="tips2">【开启】后会在用户登记页面显示【实名认证】选项和功能，将进行运营商三网验证“手机号+姓名+身份证号”自动进入“电信/移动/联通”数据库验证是否是同一个人<br>【关闭】后将将不显示</font></td></tr>
    
    <tr><td class="tdL tdLbgHUI">自助真人认证</td><td class="tdR"><input type="checkbox" name="rz_photo" id="rz_photo" class="switch" value="1"<?php echo ($rz_photo == 1)?' checked':'';?>><label for="rz_photo" class="switch-label"><i></i><b>开启</b><b>关闭</b></label><br><font class="tips2">【开启】后会在用户登记页面显示【真人认证】选项和功能，会根据“上传的照片+姓名+身份证号”和公安库里面照片进行比对是否是同一个人<br>【关闭】后将将不显示</font></td></tr>
    
    <tr><td class="tdL tdLbgHUI">认证价格</td><td class="tdR"><input name="rz_price" id="rz_price" type="text" class="size2 W80 FVerdana" maxlength="6" value="<?php echo floatval($rz_price);?>"> 元</td></tr>

    <tr><td class="tdL">同意注册</td><td class="tdR"><input type="checkbox" name="agree_reg" id="agree_reg" class="switch" value="1"<?php echo ($agree_reg == 1)?' checked':'';?>><label for="agree_reg" class="switch-label"><i></i><b>开启</b><b>关闭</b></label><br><font class="tips2">【开启】后会在用户登记页面显示【是否同意注册正式用户】选项，如果用户选同意，表单后台入库时会将Ta变为正式用户；用户不同意，将不入库<br>【关闭】后将默认表示用户同意入库</font></td></tr>
    
    <tr><td class="tdL">微信推文</td><td class="tdR"><input type="checkbox" name="agree_wxshare" id="agree_wxshare" class="switch" value="1"<?php echo ($agree_wxshare == 1)?' checked':'';?>><label for="agree_wxshare" class="switch-label"><i></i><b>开启</b><b>关闭</b></label><br><font class="tips2">【开启】后会在用户登记页面显示【是否同意朋友圈/推文推荐】选项<br>【关闭】后将将不显示</font></td></tr>
    
    
    <tr><td class="tdL">微信通知</td><td class="tdR">
    <div class='ifadmlist'>
	<?php 
    $rt=$db->query("SELECT id,truename,path_s,sex FROM ".__TBL_ADMIN__." WHERE flag=1 AND subscribe=1 AND openid<>''");
    $total = $db->num_rows($rt);
    if ($total == 0) {
        echo "<br><font class='Cf00'>..暂无管理员/红娘..</font><br>";
    } else {
        for($i=1;$i<=$total;$i++) {
            $rows = $db->fetch_array($rt,'num');
            if(!$rows) break;
            $admid   = $rows[0];
            $truename = dataIO($rows[1],'out');
			$path_s   = $rows[2];
			$sex      = $rows[3];
			if(!empty($path_s)){
				$path_s_str = '<img src="'.$_ZEAI['up2'].'/'.$path_s.'" class="photo_s">';
			}else{
				$path_s_str = '<img src="'.HOST.'/res/photo_s'.$sex.'.png'.'" class="photo_s">';
			}
			?>
            <li><?php echo $path_s_str;?><input type="checkbox" name="ifadmlist[]" id="ifadmlist_<?php echo $admid;?>" class="checkskin" value="<?php echo $admid;?>"<?php echo (in_array($admid,$ifadmlist))?' checked':'';?>><label for="ifadmlist_<?php echo $admid;?>" class="checkskin-label"><i class="i1"></i><b><?php echo $truename;?></b></label><div class="S12 C999">ID:<?php echo $admid;?></div></li>
            <?php
        }
    }
    ?>
    <div class="clear"></div>
    <div class="br">
        <img src="images/!.png" width="14" height="14" valign="middle"> <span class="tips2 picmiddle">用户提交后，管理员/红娘在微信公众号会收到采集成功信息　　注：必须绑定关注后生效，如下：</span><br><b>主后台</b>顶部【工作台】<img src="images/d2.gif">左下角【管理员用户】<img src="images/d2.gif">点【修改】进入绑定，如果没有请先新增；<br><b>CRM</b>顶部【设置】<img src="images/d2.gif">左侧【红娘管理】<img src="images/d2.gif">点【修改】进入绑定，如果没有请先新增
    	<div class="clear"></div>
    </div>
    </div>
	</td></tr>
    <tr><td class="tdL">详细内容</td><td class="tdR"><img src="images/!.png" width="14" height="14" valign="middle"> <span style="font-size:12px;vertical-align:middle;color:#999">如果从公众号编辑器或外部网页或Word里拷入内容请先过虑垃圾代码，请点击下方</span> <img src="images/cclear.png" class="picmiddle"> <span class="tips picmiddle">图标，然后插入文字内容</span><textarea name="content" id="content" class="textarea_k" style="width:100%;height:300px" ><?php echo $content;?></textarea></td></tr>
    
    
    <tr><td class="tdL tdLbgHUI">微信分享标题</td><td class="tdR"><textarea name="wxshareT" id="wxshareT" rows="3" class="W700 S14"><?php echo $wxshareT;?></textarea></td></tr>
    <tr><td class="tdL tdLbgHUI">微信分享描述</td><td class="tdR"><textarea name="wxshareC" id="wxshareC" rows="3" class="W700 S14"><?php echo $wxshareC;?></textarea></td></tr>
    <tr><td class="tdL">停止跳转链接</td><td class="tdR"><input name="stopurl" id="stopurl" type="text" class="input size2 W600" value="<?php echo $stopurl;?>" maxlength="500"   autocomplete="off" /><br><img src="images/!.png" width="14" height="14" valign="middle"> <span class="tips2 picmiddle">如果表单结束停止后，用户在前端扫码或浏览表单页将会跳转到此链接，防此流量流失，重新引流；可在表单管理列表页点【正常】就会将状态变成【已停止】</span></td></tr>
    <tr><td class="tdL">初始入库用户密码</td><td class="tdR"><input name="defpass" id="defpass" type="text" class="input size2 W200" value="<?php echo $defpass;?>" maxlength="20"   autocomplete="off" /><br><img src="images/!.png" width="14" height="14" valign="middle"> <span class="tips2 picmiddle">入主库用户表，默认初始登录密码，可以用UID或认证过的手机加密码登录网站，6~20长度</span></td></tr>

    <tr><td class="tdL">隐私条款</td><td class="tdR"><textarea name="privateC" rows="12" class="W700" placeholder="打开前端表单详情页弹窗【隐私条款】，内容小于10个字将自动关闭不显示，内容请控制在2000字以内"><?php echo $privateC;?></textarea></td></tr>
    <tr><td class="tdL">备注</td><td class="tdR"><textarea name="bz" rows="4" class="W700" placeholder="备注(500字节内，没有请留空)，不对外展示，内部备注"><?php echo $bz;?></textarea></td></tr>
    
    
    
    </table>
    <input name="submitok" type="hidden" value="<?php echo $submitok.'update';?>" />
    <input name="path_s" id="path_s" type="hidden" value="" />
    <input id="form_data_px" name="form_data_px" type="hidden" value="" />
    <?php if ($submitok == 'mod'){?><input name="id" type="hidden" value="<?php echo $id;?>" /><?php }?>
    <br><br><br><br><div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">保存并发布</button></div>
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
			i.onclick = function(){zeai.confirm('亲~~确认删除么？',function(){img.parentNode.remove();path_add.show();path_s.value='';});}
			img.onclick = function(){parent.piczoom(up2+src.replace('_s.','_b.'));}
		}
	}
	<?php }?>
	zeai.photoUp({
		btnobj:path_add,
		upMaxMB:upMaxMB,
		url:"form"+zeai.extname,
		submitok:"ajax_pic_path_s_up",
		end:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){
				picli_path.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');
				path_s.value=rs.dbname;
				path_add.hide();
				var i=zeai.tag(o(picli_path),'i')[0],img=zeai.tag(o(picli_path),'img')[0];
				i.onclick = function(){zeai.confirm('亲~~确认删除么？',function(){img.parentNode.remove();path_add.show();path_s.value='';});}
				img.onclick = function(){parent.piczoom(up2+rs.dbname.replace('_s.','_b.'));}
			}
		}
	});
    function drag_init(){(function (){[].forEach.call(stepbox1.getElementsByClassName('stepli'), function (el){Sortable.create(el,{group: 'zeai_form_data',animation:150});});})();}drag_init();
    save.onclick = function(){
		zeai.confirm('表单查看地址请返回到【列表管理页】用手机扫码【二维码】',function(){
			var DATAPX=[];
			zeai.listEach('.form_data_li',function(obj){DATAPX.push(obj.value);});
			form_data_px.value=DATAPX.join(",");
			zeai.ajax({url:'form'+zeai.extname,form:ZEAI_FORM},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(rs.msg);if(rs.flag==1)setTimeout(function(){zeai.openurl('form'+zeai.ajxext+'p=<?php echo $p;?>');},1000);
			});
		});
    }
	
    </script>
</form>
<?php }else{ ?>
<!-- LIST -->
<table class="table0">
    <tr>
    <td width="120" align="left"><button type="button" class="btn size2" onClick="zeai.openurl('form.php?submitok=add')"><i class="ico add">&#xe620;</i> 新增表单项目</button></td>
    <td align="center">
    
    <form name="ZEAI_CN__form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skey" type="text" id="Skey" maxlength="25" class="input size2 W200" placeholder="输入表单ID / 标题" value="<?php echo $Skey; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <button type="submit" class="btn size2 QING"><i class="ico">&#xe6c4;</i> 搜索</button>
    </form>
    
    </td>
    <td width="120" align="left">&nbsp;</td>
    </tr>
</table>
<?php
$SQL = "";
$Skey = trimm($Skey);
if (ifint($Skey)){
	$SQL = " WHERE id=$Skey ";
}elseif(!empty($Skey)){
	$SQL = " WHERE title LIKE '%".$Skey."%' ";
}
$rt = $db->query("SELECT *,(SELECT COUNT(id) FROM ".__TBL_FORM_U__." WHERE fid=F.id) AS Unum,(SELECT COUNT(id) FROM ".__TBL_FORM_U__." WHERE ifindatabase=1 AND fid=F.id) AS Unum_in FROM ".__TBL_FORM__." AS F ".$SQL." ORDER BY id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容<br><a class='aHUANGed' href='form.php?submitok=add'>新增表单</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo SELF; ?>">
    <table class="tablelist" >
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="40">ID</th>
    <th width="60" align="center">主图</th>
    <th width="180" align="left">表单标题</th>
    <th width="66" align="center">浏览二维码/网址</th>
    <th align="center">资料选项</th>
    <th width="60" align="center">用户/操作</th>
    <th width="60" align="center">人气</th>
    <th width="70" align="center">发布时间</th>
    <th width="100" align="center">入主库(人)</th>
	<th width="60" align="center">状态/操作</th>
	<th width="50" align="center">修改</th>
	<th width="58" align="center">删除</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];
		$title = dataIO($rows['title'],'out');
		$title = str_replace($Skey,"<font color=red><b>".$Skey."</b></font>",$title);
		$addtime = YmdHis($rows['addtime']);
		$path_s  = $rows['path_s'];
		$flag    = $rows['flag'];
		$Unum_in = $rows['Unum_in'];
		$form_data    = explode(',',$rows['form_data']);
		$Unum  = $rows['Unum'];
		$click = $rows['click'];
		if(!empty($path_s)){
			$path_s_url = $_ZEAI['up2'].'/'.$path_s;
			$path_s_str = '<img src="'.$path_s_url.'">';
		}else{
			$path_s_url = '';
			$path_s_str = '';
		}
		$path_s_url = (!empty($path_s))?$_ZEAI['up2'].'/'.$path_s:HOST.'/res/noP.gif';
		$path_s_str = '<img src="'.$path_s_url.'" class="m">';
		$ewmurl=urlencode(HOST.'/m4/form_detail.php?id='.$id.'&admid='.$session_uid);
	?>
    <tr id="tr<?php echo $id;?>">
        <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $senduid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
      <td width="40" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="60" align="center">
        
<a href="javascript:;" class="zoom" onClick="parent.piczoom('<?php echo smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a>        
        

        </td>
        <td width="180" align="left" class="S14"><?php echo $title;?></td>
        <td width="66" align="center"><a href="javascript:;" onclick="parent.zeai.iframe('【<?php echo $title;?>】二维码','u_ewm.php?url=<?php echo $ewmurl;?>&ifshowurl=1',400,400);" title="放大二维码/复制链接网址" class="zoom">
<img src="images/ewm.gif" class="ewmpic">
</a></td>
        <td align="center"><div class="listdatabox"><?php if (is_array($form_data) && count($form_data)>0){foreach($form_data as $F){echo '<li>'.data_data_title($data_data,$F).'</li>';}}?></div></td>
        <td width="60" align="center"><a href="javascript:;" class="<?php echo ($Unum >0)?'aHONG':'aHUI';?>" onClick="zeai.iframe('【<?php echo trimhtml($title);?>】登记用户','form_u.php?fid=<?php echo $id;?>',900,550)"><?php echo $Unum;?></a></td>
        <td width="60" align="center"><?php echo $click;?></td>
      <td width="70" align="center" class="C999"><?php echo $addtime;?></td>
      <td width="100" align="center" class="lineH200"><?php echo $Unum_in;?></td>
      <td width="60" align="center" class="lineH200">
            <?php if($flag==-1){?><a clsid="<?php echo $id;?>" class="aHEI flag" title="点击恢复">已停止</a><?php }?>
            <?php if($flag==1){?><a clsid="<?php echo $id;?>" class="aLV flag" title="点击停止">正常</a><?php }?>
      </td>
      <td width="50" align="center"><?php if ($ifindatabase == 0){?><a href="form.php?submitok=mod&id=<?php echo $id;?>&p=<?php echo $p;?>" class="editico"></a><?php }?></td>
      <td width="58" align="center"><a class="delico" clsid="<?php echo $id;?>"></a></td>
      </tr>
	<?php } ?>
</table>
<?php if ($total > $pagesize){?>
<div class="listbottombox" style="text-align:center">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php echo '<div class="pagebox">'.$pagelist.'</div>'; ?>
</div>
<?php }?>
</form>
<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
zeai.listEach('.HONG2',function(obj){
	obj.onclick = function(){
		zeai.confirm('<b class="S18">确定正式入库么？</b><br>1.如果表单用户手机已被主库占用将忽略自动下一位<br>2.已入库后此表单将不能修改，视为结束<br>3.只对【同意注册】的用户入库',function(){
			var id = parseInt(obj.getAttribute("value"));
			zeai.ajax({url:'form'+zeai.extname,data:{submitok:'ajax_indatabase',id:id}},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
zeai.listEach('.delico',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("clsid"));
		zeai.confirm('<b class="S18">★请慎重★　确定真的要删除么？</b><br>删除后将同步删除采集的用户数据',function(){
			zeai.ajax({url:'form'+zeai.ajxext+'submitok=ajax_del&id='+id},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
			});
		});
	}
});
zeai.listEach('.flag',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("clsid"));
		zeai.ajax({url:'form'+zeai.ajxext+'submitok=ajax_flag&id='+id},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
		});
	}
});
</script>
<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>
<br><br><br>
<?php }?>
<?php require_once 'bottomadm.php';?>
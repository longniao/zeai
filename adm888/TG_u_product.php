<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('shop',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
$k=2;
if ( !ifint($tg_uid) )alert_adm("ID号不正确","back");
$cid  = intval($tg_uid);
$rowc = $db->ROW(__TBL_TG_USER__,"title","id=".$cid);
if ($rowc)$cname= dataIO($rowc[0],'out');
if($submitok=='add_update' || $submitok=='mod_update'){
	//if(!ifint($kind))json_exit(array('flag'=>0,'msg'=>'请选择【分类】','focus'=>'kind'));
	if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【标题】','focus'=>'title'));
	if(str_len($content)<10)json_exit(array('flag'=>0,'msg'=>'【内容】至少要10位长度','focus'=>'content'));
	if(empty($fahuokind))json_exit(array('flag'=>0,'msg'=>'请选择【发货方式】','focus'=>'fahuokind1'));
	$kind  = intval($kind);
	$click = intval($click);
	$price = floatval($price);
	$price2= floatval($price2);
	$stock = intval($stock);
	$fahuokind = intval($fahuokind);
	$limitnum  = intval($limitnum);
	$tgbfb1    = intval($tgbfb1);
	$tgbfb2    = intval($tgbfb2);
	$unit=dataIO($unit,'in',50);
	if(ifint($kind)){
		$row = $db->ROW(__TBL_TG_PRODUCT_KIND__,"title","tg_uid=".$tg_uid." AND id=".$kind,"num");
		if ($row){
			$kindtitle= dataIO($row[0],'out');
		}else{json_exit(array('flag'=>0,'msg'=>'分类为空，请先去增加','kind'=>'kind'));}
	}
	//
	$title   = dataIO($title,'in',200);
	$url     = dataIO($url,'in',500);
	$content = zeai_cj_cleanhtml($content);
	$content = dataIO($content,'in',40000);
	$addtime = (empty($addtime))?ADDTIME:strtotime($addtime);
	AddLog('【'.$_SHOP['title'].'】->商品发布修改【'.$_SHOP['title'].'ID:'.$tg_uid.'】');
}
switch ($submitok) {
	case"ajax_flag":
		if (!ifint($fid))callmsg("forbidden","-1");
		$row = $db->ROW(__TBL_TG_PRODUCT__,"flag","id=".$fid,"num");
		if(!$row){
			alert_adm("您要操作的信息不存在","-1");
		}else{
			$flag = $row[0];
			$SQL = "";
			switch($flag){
				case"-1":$SQL="flag=1";break;
				case"0":$SQL="flag=1";break;
				case"1":$SQL="flag=-1";break;
				case"2":$SQL="flag=1";break;
			}
			$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET ".$SQL." WHERE id=".$fid);
			json_exit(array('flag'=>1,'msg'=>'设置成功'));
		}
	break;
	case"ajax_pic_path_s_up":
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],'');
			if (!up_send($file,$dbname,0,$_UP['upSsize'],$_UP['upBsize'],$_UP['upMsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
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
			@adm_pic_reTmpDir_send($path_s,'shop');
			@adm_pic_reTmpDir_send(smb($path_s,'m'),'shop');
			@adm_pic_reTmpDir_send(smb($path_s,'b'),'shop');
			@up_send_admindel(smb($path_s,'blur'));
			$path_s = str_replace('tmp','shop',$path_s);
		}
		if(!empty($pathlist)){
			$ARR=explode(',',$pathlist);
			if (count($ARR) >= 1 && is_array($ARR)){
				$pathlist=array();
				foreach ($ARR as $V) {
					adm_pic_reTmpDir_send($V,'shop');
					adm_pic_reTmpDir_send(smb($V,'m'),'shop');
					adm_pic_reTmpDir_send(smb($V,'b'),'shop');
					@up_send_admindel(smb($V,'blur'));
					$_s = str_replace('tmp','shop',$V);
					$pathlist[]=$_s;
				}
				$piclist = implode(',',$pathlist);
			}
		}
		$cname= dataIO($cname,'in');
		$db->query("INSERT INTO ".__TBL_TG_PRODUCT__." (tgbfb1,tgbfb2,price,price2,tg_uid,cname,kind,kindtitle,title,content,path_s,piclist,px,addtime,click,stock,unit,fahuokind,limitnum,url) VALUES ($tgbfb1,$tgbfb2,$price,$price2,$tg_uid,'$cname','$kind','$kindtitle','$title','$content','$path_s','$piclist',".ADDTIME.",$addtime,$click,$stock,'$unit',$fahuokind,$limitnum,'$url')");
		json_exit(array('flag'=>1,'msg'=>'增加成功','kind'=>$kind));
	break;
	case"mod_update":
		if(!ifint($fid))json_exit(array('flag'=>0,'msg'=>'forbidden'));
		$row = $db->ROW(__TBL_TG_PRODUCT__,"path_s,piclist"," id=".$fid,"num");
		if (!$row)json_exit(array('flag'=>0,'msg'=>'zeai_error_db_fid'.$fid));
		$data_path_s= $row[0];$data_piclist= $row[1];
		$SQL="";
		//
		$path_s = tmp_piclist_modupdate($path_s,$data_path_s,'shop','alone');
		$SQL .= ",path_s='$path_s'";
		$piclist = tmp_piclist_modupdate($pathlist,$data_piclist,'shop');
		$SQL .= ",piclist='$piclist'";
		//
		$cname= dataIO($cname,'in');
		$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET fahuokind='$fahuokind',limitnum='$limitnum',unit='$unit',stock=$stock,price='$price',price2='$price2',kind='$kind',kindtitle='$kindtitle',title='$title',content='$content',addtime='$addtime',click='$click',cname='$cname',tgbfb1='$tgbfb1',tgbfb2='$tgbfb2',url='$url' ".$SQL." WHERE id=".$fid);
		json_exit(array('flag'=>1,'msg'=>'修改成功','kind'=>$kind));
	break;
	case"ajax_del":
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'不存在或已被删除'));
		$rt = $db->query("SELECT path_s,tg_uid,title,piclist FROM ".__TBL_TG_PRODUCT__." WHERE id=".$fid);
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
			for($i=1;$i<=$total;$i++) {
				$row = $db->fetch_array($rt,'name');
				if(!$row) break;
				$path_s   = $row['path_s'];$title = $row['title'];$tg_uid = $row['tg_uid'];$piclist = $row['piclist'];
				if(!empty($path_s)){
					@up_send_admindel($path_s.'|'.smb($path_s,'b').'|'.smb($path_s,'m'));
				}
				if(!empty($piclist)){
					$piclist = explode(',',$piclist);
					foreach ($piclist as $value) {
						@up_send_admindel($value.'|'.smb($value,'m').'|'.smb($value,'b').'|'.smb(str_replace('/shop/','/tmp/',$value),'blur'));
					}
				}
			}
			$db->query("DELETE FROM ".__TBL_TG_PRODUCT__." WHERE id=".$fid);
			AddLog('【'.$_SHOP['title'].'】->商品删除->【'.$_SHOP['title'].'ID:'.$tg_uid.'，商品id:'.$fid.'，商品名称：'.$title.'】');
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($fid))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET px=".ADDTIME." WHERE id=".$fid);
		header("Location: ".SELF."?tg_uid=".$tg_uid);
	break;
	case"mod":
		if (!ifint($fid))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_TG_PRODUCT__." WHERE id=".$fid);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$id      = $row['id'];
			$path_s  = $row['path_s'];
			$piclist = $row['piclist'];
			$kind    = $row['kind'];
			$url     = trimhtml(dataIO($row['url'],'out'));
			$click   = intval($row['click']);
			$kindtitle = dataIO($row['kindtitle'],'out');
			$title     = trimhtml(dataIO($row['title'],'out'));
			$content   = dataIO($row['content'],'out');
			$addtime   = $row['addtime'];
			$tgbfb1    = $row['tgbfb1'];
			$tgbfb2    = $row['tgbfb2'];
			$price   = str_replace(".00","",$row['price']);
			$price2  = str_replace(".00","",$row['price2']);
			$stock   = intval($row['stock']);
			$fahuokind  = intval($row['fahuokind']);
			$limitnum   = intval($row['limitnum']);
			$unit   = dataIO($row['unit'],'out');
		}else{
			alert_adm("该信息不存在！","-1");
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
</style>
<body>
<div class="navbox">
    <a href="TG_u_mod.php?<?php echo "tg_uid=$tg_uid&f=$f&g=$g&k=$k&p=$p"; ?>&submitok=mod"><?php echo '【ID:'.$tg_uid.'】';?>资料</a>
    <a href="<?php echo SELF."?tg_uid=$tg_uid&k=2"; ?>" class="ed"><?php echo $cname.'【ID:'.$tg_uid.'】';?>商品管理<?php echo '<b>'.$db->COUNT(__TBL_TG_PRODUCT__,"tg_uid=".$tg_uid).'</b>';?></a>
    <a href="<?php echo "TG_u_product_kind.php?tg_uid=$tg_uid&k=2"; ?>"><?php echo $TG_set['tgytitle'].'【ID:'.$tg_uid.'】';?>商品分类</a>
    
    <?php if (empty($submitok)){?>
  <div class="Rsobox">
    <form name="form1" method="get" action="<?php echo SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" size="30" maxlength="25" class="input size2" placeholder="按名称标题搜索">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="kind" value="<?php echo $kind;?>" />
        <input type="hidden" name="k" value="<?php echo $k;?>" />
        <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>     
    </div>
	<?php }?>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<!---->
<?php
/************************************** 【发布】【修改】 add **************************************/
if ($submitok == 'add' || $submitok == 'mod'){?>
<!--【发布】-->
    <form id="Www_zeai_cn_form">
    <table class="table Mtop20 size2" style="width:1111px;margin:15px 0 100px 20px">
    <tr><td class="tdL">商品分类</td><td colspan="3" class="tdR">
	<?php
    $rt2=$db->query("SELECT id,title FROM ".__TBL_TG_PRODUCT_KIND__." WHERE tg_uid=".$tg_uid." ORDER BY px DESC,id DESC");
    $total2 = $db->num_rows($rt2);
    if ($total2 <= 0) {
        //alert_adm('请先增加分类','TG_u_product_kind.php?tg_uid='.$tg_uid);
		?>
        <font class="C999">暂无商品分类 <a href="TG_u_product_kind.php?tg_uid=<?php echo $tg_uid;?>" class="btn size1">新增</a>　如果不需要分类请忽略此项</font>
        <?php
    } else {
    ?>
    <select name="kind" id="kind" class="W200 size2" required>
    <option value="">选择分类</option>
    <?php
        for($j=0;$j<$total2;$j++) {
            $rows2 = $db->fetch_array($rt2,'num');
            if(!$rows2) break;
			$clss=($kind==$rows2[0])?' selected':'';
            echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
        }?>
	</select>
    <?php	
    }
    ?>
    </td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font>商品名称</td><td colspan="3" class="tdR C8d"><input name="title" id="title" type="text" class="input size2 W600" maxlength="200" value="<?php echo $title;?>" placeholder="请输入商品名称" /></td></tr>
    <tr><td class="tdL"><font class="Cf00">*</font>当前价格</td><td class="tdR C8d"><input name="price" id="price" type="text" class="input size2 W80" maxlength="8" value="<?php echo $price;?>" placeholder="￥" /> 元　　<span class="tips">实际交易价格</span></td>
      <td class="tdL">原价(市场价)</td>
      <td class="tdR"><input name="price2" id="price2" type="text"  class="input size2 W80" maxlength="8" value="<?php echo $price2;?>" placeholder="￥" /> 元　<span class="tips">市场价，只做展示对比，无用途</span></td>
    </tr>
    <tr><td class="tdL"><font class="Cf00">*</font>单位</td><td class="tdR"><input name="unit" id="unit" type="text" class="input size2 W150" maxlength="50" value="<?php echo $unit;?>" placeholder="单位" />　　 　<span class="tips">如：件、天、套、斤、辆等</span></td>
      <td class="tdL"><font class="Cf00">*</font>库存</td>
      <td class="tdR"><input name="stock" id="stock" type="text" class="input size2 W80" maxlength="8" value="<?php echo intval($stock);?>" placeholder="￥" /> 件　<span class="tips">购买1件减1，为0将显示库存不足，无法购买</span></td>
    </tr>
    <tr><td class="tdL"><font class="Cf00">*</font>发货方式</td><td class="tdR"><input <?php echo ($fahuokind == 1)?' checked':'';?> type="radio" name="fahuokind" id="fahuokind1" class="radioskin" value="1" ><label for="fahuokind1" class="radioskin-label"><i class="i1"></i><b class="W100">快递物流</b></label>　
<input <?php echo ($fahuokind == 2)?' checked':'';?> type="radio" name="fahuokind" id="fahuokind2" class="radioskin" value="2" ><label for="fahuokind2" class="radioskin-label"><i class="i1"></i><b class="W100">到店取货</b></label> </td>
      <td class="tdL"><font class="Cf00">*</font>用户限购</td>
      <td class="tdR"><input name="limitnum" id="limitnum" type="text" class="input size2 W80" maxlength="5" value="<?php echo $limitnum;?>"  /> 件　<span class="tips">单个用户同1件商品最多购买数量，填0不限</span></td>
    </tr>
    
    <tr><td class="tdL">推广奖励(直推)</td><td class="tdR C8d"><input name="tgbfb1" id="tgbfb1" type="text" class="input size2 W50" maxlength="2" value="<?php echo $tgbfb1;?>" /> %　　<span class="tips">分享推广奖励百分比0~99（确认收货后），填0不奖励</span></td>
      <td class="tdL">推广奖励(团队)</td>
      <td class="tdR"><input name="tgbfb2" id="tgbfb2" type="text"  class="input size2 W50" maxlength="2" value="<?php echo $tgbfb2;?>" /> %　<span class="tips">分享推广奖励百分比0~99（确认收货后），填0不奖励</span></td>
    </tr>

    <tr>    
      <td class="tdL" style="line-height:120%"><font class="Cf00">*</font>商品主图<br><font class="Cf00 S12">无图前台将不显示</font></td>
      <td colspan="3" class="tdR">
        <div class="picli" id="picli_path">
          <li class="add" id="path_add"></li>
          <?php if(!empty($path_s)){
				echo '<li><img src="'.$_ZEAI['up2'].'/'.$path_s.'"><i></i></li>';
			}?>
        </div>
    </td></tr>
    <tr>    
    <td class="tdL" style="line-height:120%">商品多图展示<br><font class="Cf00 S12">最多5张</font></td>
    <td colspan="3" class="tdR">
    <div class="picli100" id="picli_pathlist">
        <li class="add" id="pathlist_add"></li>
        <?php
        if(!empty($piclist)){
            $ARR=explode(',',$piclist);
            $piclist=array();
            foreach ($ARR as $V) {
               echo '<li><img src="'.$_ZEAI['up2'].'/'.$V.'"><i></i></li>';
            }
        }?>      
    </div>
    </td></tr> 
     
    <tr><td class="tdL"><font class="Cf00">*</font>发布时间</td><td class="tdR C8d"><input name="addtime" id="addtime" type="text" class="input size2 W200" maxlength="50" value="<?php echo YmdHis($addtime);?>" />　　<span class="tips">留空将自动生成当前时间</span></td>
      <td class="tdL">点击量</td>
      <td class="tdR"><input name="click" id="click" type="number" min="1" class="input size2 W80" maxlength="8" value="<?php echo intval($click);?>" />　　<span class="tips">阅读数，建议初始200</span></td>
    </tr>
    <tr><td class="tdL"><font class="Cf00">*</font>商品详情</td><td colspan="3" class="tdR"><img src="images/!.png" width="14" height="14" class="picmiddle"> <font style="vertical-align:middle;color:#999">如果从公众号编辑器或外部网页或Word里拷入内容请先过虑垃圾代码，请点击下方 <img src="images/cclear.png" class="picmiddle"> 图标，然后插入文字内容</font><textarea name="content" id="content" class="textarea_k" style="width:100%;height:500px" ><?php echo $content;?></textarea></td></tr>
    <tr><td class="tdL">外链网址</td><td colspan="3" class="tdR C8d"><input name="url" id="url" type="text" class="input size2 W600" maxlength="200" value="<?php echo $url;?>" placeholder="请输入外链网址" />　　<span class="tips">填写后，点击将自动跳转填写的网址，没有请留空</span></td></tr>
    </table>
<input name="path_s" id="path_s" type="hidden" value="" />
<input name="pathlist" id="pathlist" type="hidden" value="" />
<input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
<?php if ($submitok == 'mod'){?>
  <input name="submitok" type="hidden" value="mod_update" />
  <input name="fid" type="hidden" value="<?php echo $fid;?>" />
<?php }else{ ?>
  <input name="submitok" type="hidden" value="add_update" />
<?php }?>
<input type="hidden" name="return" value="<?php echo $return;?>" />
<input type="hidden" name="k" value="<?php echo $k;?>" />
<div class="savebtnbox"><button class="btn size3 HUANG3" type="button" id="submit_add" />保存并发布</button></div> 
</form>
<script>
	<?php if($submitok=='mod'){?>
	window.onload=function(){path_s_mod();end();}
	<?php }?>
		zeai.photoUp({
			btnobj:path_add,
			upMaxMB:upMaxMB,
			url:"TG_u_product.php?tg_uid=<?php echo $tg_uid;?>",
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

		zeai.photoUp({
			btnobj:pathlist_add,
			upMaxMB:upMaxMB,
			url:"TG_u_product.php?tg_uid=<?php echo $tg_uid;?>",
			multiple:5,
			submitok:"ajax_pic_path_s_up",
			end:function(rs){end();},
			li:function(rs){zeai.msg(0);zeai.msg(rs.msg);if (rs.flag == 1){picli_pathlist.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');}}
		});
		function end(){
			var i=zeai.tag(picli_pathlist,'i'),img=zeai.tag(picli_pathlist,'img');if(zeai.empty(i))return;
			for(var k=0;k<img.length;k++) {(function(k){var src=img[k].src;img[k].onclick = function(){parent.piczoom(src.replace('_s.','_b.'));}})(k);}
			for(var k=0;k<i.length;k++) {(function(k){i[k].onclick = function(){var thiss=this;zeai.confirm('亲~~确认删除么？',function(){thiss.parentNode.remove();pathlistReset();});}})(k);}
			function pathlistReset(){var img=zeai.tag(picli_pathlist,'img'),pathlist=[],src;for(var k=0;k<img.length;k++){var src=img[k].src.replace(up2,'');pathlist.push(src);}o('pathlist').value=pathlist.join(",");}
			pathlistReset();
		}

		submit_add.onclick=function(){
			zeai.confirm('确定检查无误发布提交么？',function(){
				zeai.ajax({url:'TG_u_product'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
					zeai.msg(0);
					if(rs.flag==1){
						zeai.msg(rs.msg,{time:3});
						<?php if ($return == 'TG_product'){?>
						setTimeout(function(){zeai.openurl('<?php echo $return;?>'+zeai.ajxext+'p=<?php echo $p;?>&sort=<?php echo $sort;?>');},1000);
						<?php }else{ ?>
						setTimeout(function(){zeai.openurl('TG_u_product'+zeai.ajxext+'kind='+rs.kind+'&tg_uid=<?php echo $tg_uid;?>');},1000);
						<?php }?>
					}else{
						zeai.msg(rs.msg,{time:3,focus:o(rs.focus)});
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
    <table class="table0 W98_ Mbottom10 Mtop10">
    <tr>
    <td width="200" align="left" class="border0" ><button type="button" class="btn size2" onClick="zeai.openurl('<?php echo SELF;?>?submitok=add&kind=<?php echo $kind;?>&k=2&tg_uid=<?php echo $tg_uid;?>')"><i class="ico add">&#xe620;</i>发布新商品</button>
    </td>
    <td>
    </td>
    <td width="300" align="right">&nbsp;</td>
    </tr>
    </table>
    <?php
	$SQL="tg_uid=".$tg_uid;
	$Skeyword = trimhtml($Skeyword);
	if (!empty($Skeyword))$SQL = " AND ( title LIKE '%".dataIO(trimm($Skeyword),'in')."%' ) ";
	if(ifint($kind))$SQL.=" AND kind=".$kind;
	$rt = $db->query("SELECT id,path_s,flag,title,click,addtime,kindtitle,kind,price,stock,tgbfb1,tgbfb2 FROM ".__TBL_TG_PRODUCT__." WHERE ".$SQL."  ORDER BY px DESC LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无内容<br><a class='aHUANGed' href='".SELF."?tg_uid=".$tg_uid."&submitok=add&kind=".$kind."'>发布新商品</a></div>";
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
		<th width="300" align="left">名称/标题</th>
		<th width="80" align="left">价格(元)</th>
		<th width="100" align="center">所属分类</th>
		<th width="60" align="center">点击量</th>
		<th width="10" align="center"></th>
		<th width="100" align="left">分销推广</th>
		<th>&nbsp;</th>
		<th width="70" align="center">库存</th>
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
			$price     = $rows['price'];
			$stock     = $rows['stock'];
			$tgbfb1    = intval($rows['tgbfb1']);
			$tgbfb2    = intval($rows['tgbfb2']);
			$tgbfb_str ='';
			if($tgbfb1>0)$tgbfb_str.='<div>直接奖：'.$tgbfb1.'%<div>';
			if($tgbfb2>0)$tgbfb_str.='<div>团队奖：'.$tgbfb2.'%</div>';
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
			if($stock<=0){
				$stock_str='<font class="Cf00 S12" title="库存不足，无法下单哦">'.$stock.'<br>无法下单</font>';
			}else{
				$stock_str=$stock;
			}
		?>
		<tr id="tr<?php echo $id;?>">
		<td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label></td>
		<td width="50" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
		<td width="50" align="left"><a href="<?php echo SELF."?fid=".$id; ?>&tg_uid=<?php echo $tg_uid;?>&submitok=ding" class="topico" title="置顶"></a></td>
		<td width="80" align="left" style="padding:10px 0">
        	<?php if (empty($path_s_url)){?>
			<a href="javascript:;" class="pic60 ">无图</a>
            <?php }else{ ?>
            <a href="javascript:;" class="pic60 " onClick="parent.piczoom('<?php echo smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a>
			<?php }?>
        </td>
		<td width="300" align="left" class="C999">
        <div class="S14 "><a href="<?php echo SELF;?>?submitok=mod&tg_uid=<?php echo $tg_uid;?>&fid=<?php echo $id;?>" ><?php echo $title;?></a></div ></td>
		<td width="80" align="left" class="Cf00">￥<?php echo number_format(str_replace(".00","",$price));?></td>
		<td width="100" align="center"><?php if (!empty($kindtitle)){?><a href="<?php echo SELF;?>?kind=<?php echo $kind;?>&tg_uid=<?php echo $tg_uid;?>" class="aHUI"><?php echo $kindtitle;?></a><?php }?></td>
		<td width="60" align="center"><?php echo $click;?></td>
		<td width="10" align="center">&nbsp;</td>
		<td width="100" align="left"><?php echo $tgbfb_str;?></td>
		<td align="left" >&nbsp;</td>
		<td width="70" align="center"><?php echo $stock_str;?></td>
		<td width="70" align="center" class="C999"><?php echo $addtime;?></td>
		<td width="100" align="center" class="C999">
		  <?php if($flag==-1){?><a clsid="<?php echo $id;?>" class="aHEI flag" title="点击恢复">已锁定</a><?php }?>
		  <?php if($flag==1){?><a clsid="<?php echo $id;?>" class="aLV flag" title="点击锁定">正常</a><?php }?>
		  <?php if($flag==2){?><a clsid="<?php echo $id;?>" class="aHUI flag" title="点击恢复">已下架</a><?php }?>
		  </td>
		<td width="60" align="center">
		  <a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>"></a>
		</td>
		<td width="60" align="center"><a clsid="<?php echo $id; ?>" class="delico" title='删除'></a></td>
		</tr>
		<?php } ?>
		<tfoot><tr>
		<td colspan="16">
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
				zeai.openurl('<?php echo SELF;?>?submitok=mod&tg_uid=<?php echo $tg_uid;?>&k=2&fid='+id);
			}
		});
		zeai.listEach('.delico',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			obj.onclick = function(){
				zeai.confirm('★请慎重★　确定真的要删除么？',function(){
					zeai.ajax({url:'<?php echo SELF;?>?submitok=ajax_del&k=2&tg_uid=<?php echo $tg_uid;?>&fid='+id},function(e){
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
				zeai.ajax({url:'<?php echo SELF;?>?submitok=ajax_flag&k=2&tg_uid=<?php echo $tg_uid;?>&fid='+id},function(e){
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
<?php
require_once '../sub/init.php';
if (@ini_get('session.auto_start') == 0)session_start();
!function_exists('zeai_alone') && exit('forbidden');
$currfields = "";
//if(!ifint($cook_tg_uid))json_exit(array('flag'=>'nologin','msg'=>'请先登录后再来','jumpurl'=>HOST.'/m1/tg_my.php'));
$currfields = "photo_s,areaid,areatitle,kind,RZ,mob,job,email,weixin,qq,company_apply_flag,title,tel,address,worktime,content,bank_name,bank_name_kaihu,bank_truename,bank_card,alipay_truename,alipay_username";
require_once 'tg_chkuser.php';
$data_kind=$row['kind'];
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
switch ($data_kind) {
	case 1:$kind_str='商品';break;
	case 2:$kind_str='商品';break;
	case 3:$kind_str='服务';break;
}


if ($submitok == 'kind_add_update' || $submitok == 'kind_mod_update'){
	if (empty($title) )alert('请输入分类名称','back');
	if (str_len($title) >200)alert('亲，分类名称这么长有意义么？ 请不要超过100字节','back');
	$title = dataIO($title,'in',200);
}elseif($submitok=='add_update' || $submitok=='mod_update'){
	if(!ifint($kind))json_exit(array('flag'=>0,'msg'=>'请选择【分类】','focus'=>'kind'));
	if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【标题/名称】','focus'=>'title'));
	if(str_len($content)<10)json_exit(array('flag'=>0,'msg'=>'【内容】至少要10位长度','focus'=>'content'));
	$kind = intval($kind);
	$price= floatval($price);
	$price2= floatval($price2);
	$row = $db->ROW(__TBL_TG_PRODUCT_KIND__,"title","tg_uid=".$cook_tg_uid." AND id=".$kind,"num");
	if ($row){
		$kindtitle= dataIO($row[0],'out');
	}else{json_exit(array('flag'=>0,'msg'=>'分类为空，请先去增加','kind'=>'kind'));}
	//
	$title   = dataIO($title,'in',200);
	$content = dataIO($content,'in',10000);
	$addtime = (empty($addtime))?ADDTIME:strtotime($addtime);
}

switch ($submitok) {
	case 'ajax_picurl_up_h5':
		if (ifpostpic($file['tmp_name'])){
			$dbname = setphotodbname('tmp',$file['tmp_name'],$cook_tg_uid.'_tg_');
			if (!up_send($file,$dbname,$_UP['ifwaterimg'],$_UP['upSsize'],$_UP['upBsize']))json_exit(array('flag'=>0,'msg'=>'图片写入失败'));
			$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
			if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
			json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$_s));
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_tmp_name_error'));
		}
	break;
	case 'ajax_picurl_up_wx':
		if (str_len($serverIds) > 15){
			$serverIds = explode(',',$serverIds);
			$totalN = count($serverIds);
			if ($totalN >= 1){
				foreach ($serverIds as $value) {
					$url="http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".wx_get_access_token()."&media_id=".$value;
					$dbname = wx_get_up('tmp',$url,$cook_tg_uid.'_tg_','SB');
				}
				$_s=setpath_s($dbname);$newphoto_s = $_ZEAI['up2']."/".$_s;
				if (!ifpic($newphoto_s))json_exit(array('flag'=>0,'msg'=>'图片格式错误'));
				json_exit(array('flag'=>1,'msg'=>'上传成功','dbname'=>$_s));
			}
		}else{
			json_exit(array('flag'=>0,'msg'=>'zeai_serverIds_down_error'));
		}
	break;
	case 'kind_add_update':
		$db->query("INSERT INTO ".__TBL_TG_PRODUCT_KIND__." (tg_uid,title,px) VALUES ($cook_tg_uid,'$title',".ADDTIME.")");	
		header("Location: ".SELF."?t=2");
	break;
	case 'kind_mod_update':
		if(!ifint($id))alert('ID_forbidden','back');
		$db->query("UPDATE ".__TBL_TG_PRODUCT_KIND__." SET title='$title' WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
		header("Location: ".SELF."?t=2");
	break;
	case 'kind_del':
		if (!ifint($id))alert('ID_forbidden','back');
		$db->query("DELETE FROM ".__TBL_TG_PRODUCT_KIND__." WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
		//header("Location: ".SELF."?t=2");
	break;
	case 'kind_ding':
		if (!ifint($id))alert('ID_forbidden','back');
		$db->query("UPDATE ".__TBL_TG_PRODUCT_KIND__." SET px=".ADDTIME." WHERE id=".$id);
		header("Location: ".SELF."?t=2");
	break;
	case 'kind_mod':
		if(!ifint($id))alert('ID_forbidden','back');
		$row = $db->ROW(__TBL_TG_PRODUCT_KIND__,"title","tg_uid=".$cook_tg_uid." AND id=".$id,"name");
		if ($row){
			$title = dataIO($row['title'],'out');
		}else{alert('forbidden','back');}
	break;
	case"mod":
		if (!ifint($id))alert_adm("参数错误","-1");
		$rt = $db->query("SELECT * FROM ".__TBL_TG_PRODUCT__." WHERE id=".$id);
		if($db->num_rows($rt)){
			$row = $db->fetch_array($rt);
			$id      = $row['id'];
			$path_s  = $row['path_s'];
			$kind    = $row['kind'];
			$click   = intval($row['click']);
			$kindtitle = dataIO($row['kindtitle'],'out');
			$title     = dataIO($row['title'],'out');
			$content   = dataIO($row['content'],'wx');
			//$content = trimhtml($content);
			
			$addtime   = $row['addtime'];
			$price   = str_replace(".00","",$row['price']);;
			$price2  = str_replace(".00","",$row['price2']);;
		}else{
			alert("该信息不存在！","back");
		}
	break;
	case"add_update":
		if(!empty($path_s)){
			u_pic_reTmpDir_send($path_s,'tg','u_pic_reTmpDir_tg');
			u_pic_reTmpDir_send(smb($path_s,'b'),'tg','u_pic_reTmpDir_tg');
			$path_s = str_replace('tmp','tg',$path_s);
		}
		$db->query("INSERT INTO ".__TBL_TG_PRODUCT__." (price,price2,tg_uid,kind,kindtitle,title,content,path_s,px,addtime) VALUES ($price,$price2,$cook_tg_uid,'$kind','$kindtitle','$title','$content','$path_s',".ADDTIME.",$addtime)");
		json_exit(array('flag'=>1,'msg'=>'增加成功','kind'=>$kind));
	break;		
	case"mod_update":
		if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'参数错误'));
		$row = $db->ROW(__TBL_TG_PRODUCT__,"path_s","tg_uid=".$cook_tg_uid." AND id=".$id);
		if (!$row)json_exit(array('flag'=>0,'msg'=>'zeai_error_db_id'.$id));
		$data_path_s= $row[0];
		$SQL = "";
		if(!empty($path_s)){
			u_pic_reTmpDir_send($path_s,'tg','u_pic_reTmpDir_tg');
			u_pic_reTmpDir_send(smb($path_s,'b'),'tg','u_pic_reTmpDir_tg');
			$path_s = str_replace('tmp','tg',$path_s);
			$SQL = ",path_s='$path_s'";
			if(!empty($data_path_s)){
				//删老
				$B = smb($data_path_s,'b');
				@up_send_userdel($data_path_s.'|'.$B,'tg_userdelpic');
			}
		}
		$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET price='$price',price2='$price2',kind='$kind',kindtitle='$kindtitle',title='$title',content='$content'".$SQL." WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
		json_exit(array('flag'=>1,'msg'=>'修改成功','kind'=>$kind));
	break;
	case"ajax_del":
		if (!ifint($id))json_exit(array('flag'=>0,'msg'=>'不存在或已被删除'));
		$rt = $db->query("SELECT path_s FROM ".__TBL_TG_PRODUCT__." WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
		$total = $db->num_rows($rt);
		if ($total > 0 ) {
			for($i=1;$i<=$total;$i++) {
				$row = $db->fetch_array($rt,'name');
				if(!$row) break;
				$path_s   = $row['path_s'];
				if(!empty($path_s)){
					$B = smb($path_s,'b');@up_send_userdel($path_s.'|'.$B,'tg_userdelpic');
				}
			}
			$db->query("DELETE FROM ".__TBL_TG_PRODUCT__." WHERE tg_uid=".$cook_tg_uid." AND id=".$id);
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($id))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET px=".ADDTIME." WHERE  tg_uid=".$cook_tg_uid." AND id=".$id);
		header("Location: ".SELF."?t=1");
	break;
}

$headertitle = $kind_str.'管理'.$TG_set['navtitle'].'-';$nav = 'tg_my';require_once ZEAI.'m1/header.php';
if (is_weixin()){
	require_once ZEAI."api/weixin/jssdk.php";
	?>
	<script src="<?php echo HOST;?>/res/jweixin-1.2.0.js"></script>
	<script>
	wx.config({debug: false,
		appId: '<?php echo $signPackage["appId"];?>',
		timestamp: <?php echo $signPackage["timestamp"];?>,
		nonceStr: '<?php echo $signPackage["nonceStr"];?>',
		signature: '<?php echo $signPackage["signature"];?>',
		jsApiList: ['chooseImage','uploadImage','previewImage','hideMenuItems','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo']
	});
	</script>
<?php }?>
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/TG2.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css" />
<style>
body{background-color:#fff}
.product_box{height:-webkit-calc(100% - 50px);height:calc(100% - 100px)}
.editico:after,.delico:after,.topico:after{font-family:"iconfont2" !important;font-style:normal;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;display:inline-block}
.editico:after,.delico:after,.topico:after{width:30px;height:30px;line-height:30px;color:#aaa;content:"\e63f";font-size:18px}
.delico:after{content:"\e779";}
.topico:after{content:"\e602";font-size:20px}
.editico:hover:after,.delico:hover:after,.topico:hover:after{color:#000}
.product_box .HONG4{width:36%;left:32%;position:fixed;bottom:60px;display:block;z-index:8}
.product_box .HONG4:hover{background-color:#F7564D;filter:alpha(opacity=100);-moz-opacity:1;opacity:1}
.tg_my_tab{width:100%;height:36px;padding:5px 0;font-size:16px;text-align:center;background-color:#F7564D;margin-bottom:20px}
.tg_my_tab a{width:70px;display:inline-block;line-height:30px;height:30px;margin:0 20px;position:relative;color:#fff;cursor:pointer}
.tg_my_tab a.ed{color:#fff}
.tg_my_tab a.ed:after{content:'';position:absolute;width:40%;height:4px;left:30%;bottom:-5px;background-color:#fff;border-radius:3px}
.product_box .list dl{position:relative;border-bottom:#eee 1px solid;margin:10px 0;padding:0 10px 10px 10px}
.product_box .list dl img.m{float:left;display:block;width:60px;height:60px;margin-right:10px;object-fit:cover;-webkit-object-fit:cover;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.product_box .list dl dd{width:-webkit-calc(100% - 120px);text-align:left;float:left}
.product_box .list dl dd h3{font-size:16px}
.product_box .list dl dd h4{font-size:14px;margin-top:5px;color:#999}
.product_box .list dl dd h4 b{font-size:16px;color:#F7564D;font-family:Arial;font-weight:normal}
</style>
<i class="ico goback" id="ZEAIGOBACK-tg_my_product" onClick="zeai.openurl('tg_my.php')">&#xe602;</i>
<div class="tg_my_tab huadong" id="tg_my_tab">
    <a href="<?php echo SELF;?>?t=1"<?php echo (empty($t) || $t==1)?' class="ed"':'';?>><?php echo $kind_str;?>管理</a>
    <a href="<?php echo SELF;?>?t=2"<?php echo ($t==2)?' class="ed"':'';?>>分类管理</a>
</div>
<div class="product_box " id="main">
	<?php if ($t == 1){?>
    	<style>
		.product_box dl .topico{position:absolute;right:50px;top:1px}
		.product_box dl .editico{position:absolute;right:20px;top:0px}
		.product_box dl .delico{position:absolute;right:20px;top:30px}
		.ZEAI_FORM{text-align:left}
		.ZEAI_FORM dl{width:90%;margin:0 auto;box-sizing:border-box;clear:both;overflow:auto;padding:5px 0}
		.ZEAI_FORM dl dt,.ZEAI_FORM dl dd{font-size:16px;line-height:50px}
		.ZEAI_FORM dl dt{width:22%;float:left}
		.ZEAI_FORM dl dd{width:78%;float:right}
		.ZEAI_FORM .input{width:100%;border:0;border-bottom:#dedede 1px solid;font-size:16px}
		.ZEAI_FORM .icoadd,.icoadd img{width:60px;height:60px;display:block}
		.ZEAI_FORM .icoadd{line-height:60px;border:#dedede 1px solid;font-size:40px;text-align:center;color:#aaa}
        </style>
        <?php
		if($submitok=='add' || $submitok=='mod'){
			$path_s_str = (!empty($path_s ))?'<img src="'.$_ZEAI['up2'].'/'.$path_s.'">':'<i class="ico">&#xe620;</i>';
			?>
            <form action="<?php echo SELF;?>" name="ZEAI_FORM" id="ZEAI_FORM" method="post" class="ZEAI_FORM">
            <dl><dt>上传主图</dt><dd><p class="icoadd" id="jubaopic"><?php echo $path_s_str;?></p></dd></dl>
            <dl><dt>所属分类</dt><dd>
            
                <select name="kind" id="kind" class="W200 input " required>
                    <?php
					$rt2=$db->query("SELECT id,title FROM ".__TBL_TG_PRODUCT_KIND__." WHERE tg_uid=".$cook_tg_uid." ORDER BY px DESC,id DESC");
                    $total2 = $db->num_rows($rt2);
                    if ($total2 <= 0) {
                        alert_adm('请先增加分类','tg_my_product.php?t=2&submitok=kind_add');
                    } else {
                    ?>
                    <option value=""> 选择分类 </option>
                    <?php
                        for($j=0;$j<$total2;$j++) {
                            $rows2 = $db->fetch_array($rt2,'num');
                            if(!$rows2) break;
                            $clss=($kind==$rows2[0])?' selected':'';
                            echo "<option value=".$rows2[0].$clss.">".dataIO($rows2[1],'out')."</option>";
                        }
                    }
                    ?>
                </select>            
            
            </dd></dl>
            <dl><dt><?php echo $kind_str;?>名称</dt><dd><input name="title" id="title" type="text" class="input" placeholder="请输入<?php echo $kind_str;?>名称" autocomplete="off" maxlength="20" value="<?php echo $title;?>" onBlur="rettop();" /></dd></dl>
            <dl><dt>当前价格</dt><dd><input name="price"t id="price" type="number" class="input " placeholder="请输入当前价格"  min="0" autocomplete="off" maxlength="6" value="<?php echo $price;?>" onBlur="rettop();"></dd></dl>
            <dl><dt>原　　价</dt><dd><input name="price2" id="price2" type="number" class="input " placeholder="请输入原价"  min="0" autocomplete="off" maxlength="6" value="<?php echo $price2;?>" onBlur="rettop();"></dd></dl>
            <div class="linebox" style="margin-top:5px"><div class="line"></div><div class="title S18 BAI B" style="color:#F7564D"><?php echo $kind_str;?>简介</div></div>
            <textarea name="content" id="content" class="textarea" style="border:0;width:100%;height:100px;padding:10px" placeholder="请填写<?php echo $k_str;?>简介，1000个字以内" onBlur="rettop();"><?php echo $content;?></textarea>
            
			<?php if ($submitok == 'mod'){?>
                <input name="submitok" type="hidden" value="mod_update" />
                <input name="id" type="hidden" value="<?php echo $id;?>" />
            <?php }else{ ?>
                <input name="submitok" type="hidden" value="add_update" />
            <?php }?>
            <input type="hidden" name="path_s" id="path_s" value="" />
            <button type="button" class="btn size3 HONG4 yuan" id="p_btn">保存并提交</button>
            </form>
			<script>
			function rettop(){zeai.setScrollTop(0);}
            var browser='<?php echo (is_weixin())?'wx':'h5';?>',upMaxMB = <?php echo $_UP['upMaxMB']; ?>,up2='<?php echo $_ZEAI['up2'];?>/';
            photoUp({
                btnobj:jubaopic,
                url:"tg_my_product"+zeai.extname,
                submitokBef:"ajax_picurl_",
                _:function(rs){
                    zeai.msg(0);zeai.msg(rs.msg);
                    if (rs.flag == 1){
                        jubaopic.html('<img src='+up2+rs.dbname+'>');
                        path_s.value=rs.dbname;
                    }
                }
            });
            p_btn.onclick=function(){
                if(zeai.empty(o("kind").value)){zeai.msg('请选择分类');return false;}
                if(zeai.empty(o("title").value)){zeai.msg('请输入<?php echo $kind_str;?>名称');return false;}
                if(zeai.str_len(o("content").value)>1000 || zeai.str_len(o("content").value)<10 ){zeai.msg('<?php echo $kind_str;?>简介长度：10~1000字节');return false;}
                zeai.ajax({url:'tg_my_product'+zeai.extname,form:ZEAI_FORM},function(e){rs=zeai.jsoneval(e);
                   zeai.msg(rs.msg,{time:3});
				   if(rs.flag==1){
					   setTimeout(function(){zeai.openurl('tg_my_product.php?t=1');},1000);
				   }
                });
            }
            </script>            
			<?php
		}else{
			$rt = $db->query("SELECT id,path_s,flag,title,click,addtime,kindtitle,kind,price FROM ".__TBL_TG_PRODUCT__." WHERE tg_uid=".$cook_tg_uid." AND flag=1 ORDER BY px DESC");
			$total = $db->num_rows($rt);
			if ($total <= 0 ) {
				echo "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时没有". $kind_str."～～</div>";
			} else {
				echo '<div class="list">';
				for($i=1;$i<=$total;$i++) {
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
					if(!empty($path_s)){
						$path_s_url=$_ZEAI['up2'].'/'.$path_s;
						$path_b_url=smb($path_s_url,'b');
						$bdr = '';
					}else{
						$path_b_url=HOST.'/res/noP.gif';
						$bdr = 'style="border:#eee 1px solid"';
					}
					?>
					<dl>
						<img src="<?php echo $path_b_url; ?>" class="m"<?php echo $bdr;?>>
						<dd>
							<h3><?php echo $title;?></h3>
							<?php echo '<h4>【'.$kindtitle.'】价格￥<b>'.str_replace(".00","",$price).'</b></h4>';?>
						</dd>
						<div class="clear"></div>
						<a href="<?php echo SELF."?t=1&id=$id"; ?>&submitok=ding" class="topico"></a>
						<a href="<?php echo SELF."?t=1&id=$id"; ?>&submitok=mod" class="editico"></a>
						<a clsid="<?php echo $id; ?>" class="delico"></a>
					</dl>
					<?php
				}
				echo '</div>';
			}
			?>
            <button type="button" class="btn size3 HONG4 yuan" onClick="zeai.openurl('tg_my_product.php?t=<?php echo $t;?>&submitok=add')"><i class="ico add">&#xe620;</i> 发布新<?php echo $kind_str;?></button>
			<script>
			zeai.listEach(".delico",function(btn){
				btn.onclick=function(){
					ZeaiM.confirmUp({title:'亲，确定删除么？',cancel:'取消',ok:'确定',okfn:function(){
						zeai.ajax({url:'tg_my_product'+zeai.extname,data:{submitok:'ajax_del',id:btn.getAttribute("clsid")}},function(e){rs=zeai.jsoneval(e);
							zeai.msg(rs.msg);
							if(rs.flag==1){
								setTimeout(function(){location.reload(true);},1000);
							}
						});
					}});
				}
			});
            </script>
            
            <?php
		}
		?> 
		<div class="clear"></div>

    <?php }elseif($t == 2){?>
    	<style>
		.product_box dl dd h3{padding-left:10px}
		.product_box dl .topico{position:absolute;right:120px;top:0px}
		.product_box dl .editico{position:absolute;right:70px;top:0px}
		.product_box dl .delico{position:absolute;right:20px;top:0px}
        </style>
        <?php
		if($submitok=='kind_add' || $submitok=='kind_mod'){
			?>
            <form action="<?php echo SELF;?>" name="ZEAI_FORM" id="ZEAI_FORM" method="post">
			<br><font class="S16">分类名称：</font> <input name="title" type="text" class="input W150 size2" id="title" size="30" maxlength="100" placeholder="请输入分类名称" value="<?php echo $title;?>">
			<?php if ($submitok == 'kind_mod'){?>
              <input name="submitok" type="hidden" value="kind_mod_update" />
              <input name="id" type="hidden" value="<?php echo $id;?>" />
            <?php }else{ ?>
              <input name="submitok" type="hidden" value="kind_add_update" />
            <?php }?>
            <button type="submit" class="btn size3 HONG4 yuan" >保存并提交</button>
            </form>
			<?php
		}else{
			$rt = $db->query("SELECT id,title FROM ".__TBL_TG_PRODUCT_KIND__." WHERE tg_uid=".$cook_tg_uid." ORDER BY px DESC,id DESC");
			$total = $db->num_rows($rt);
			if ($total <= 0 ) {
				echo "<div class='nodatatips'><i class='ico'>&#xe651;</i>暂时没有分类～～</div>";
			} else {
				echo '<div class="list">';
				for($i=1;$i<=$total;$i++) {
					$rows = $db->fetch_array($rt,'name');
					if(!$rows) break;
					$id = $rows['id'];
					$title = dataIO($rows['title'],'out');
					?>
					<dl>
						<dd>
							<h3><?php echo $title;?></h3>
						</dd>
						<div class="clear"></div>
                        <a href="<?php echo SELF."?t=2&id=$id"; ?>&submitok=kind_ding" class="topico"></a>
						<a href="<?php echo SELF."?t=2&id=$id"; ?>&submitok=kind_mod"  class="editico"></a>
						<a clsid="<?php echo $id; ?>" class="delico"></a>
					</dl>
					<?php
				}
				echo '</div>';
			}
			?> 
			<button type="button" class="btn size3 HONG4 yuan" onClick="zeai.openurl('tg_my_product.php?t=2&submitok=kind_add')"><i class="ico add">&#xe620;</i> 新增分类</button>
			<script>
			zeai.listEach(".delico",function(btn){
				btn.onclick=function(){
					ZeaiM.confirmUp({title:'亲，确定删除此分类么？',cancel:'取消',ok:'确定',okfn:function(){
						zeai.ajax({url:'tg_my_product'+zeai.extname,data:{submitok:'kind_del',id:btn.getAttribute("clsid")}},function(e){rs=zeai.jsoneval(e);
							zeai.msg(rs.msg);
							if(rs.flag==1){
								setTimeout(function(){location.reload(true);},1000);
							}
						});
					}});
				}
			});
            </script>
			<?php
		}
		?> 
		
		
    	<div class="clear"></div>
    <?php }?>
</div>
<script>zeaiLoadBack=['nav','tg_my_tab'];</script>
<script src="js/TG2.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php require_once ZEAI.'m1/tg_bottom.php';	?>
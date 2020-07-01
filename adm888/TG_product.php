<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('shop',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_shop.php';
$k=2;
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
	case"ajax_del":
		if (!ifint($fid))json_exit(array('flag'=>0,'msg'=>'不存在或已被删除'));
		$row = $db->ROW(__TBL_TG_PRODUCT__,"tg_uid,title,path_s,piclist","id=".$fid,"name");
		$path_s = $row['path_s'];$piclist = $row['piclist'];$tg_uid = $row['tg_uid'];$title = $row['title'];
		if(!empty($path_s))@up_send_admindel($path_s.'|'.smb($path_s,'b').'|'.smb($path_s,'m'));
		if(!empty($piclist)){
			$piclist = explode(',',$piclist);
			foreach ($piclist as $value) {
				@up_send_admindel($value.'|'.smb($value,'m').'|'.smb($value,'b').'|'.smb(str_replace('/shop/','/tmp/',$value),'blur'));
			}
		}
		$db->query("DELETE FROM ".__TBL_TG_PRODUCT__." WHERE id=".$fid);
		AddLog('【商家】->商品删除->【商家id:'.$tg_uid.'，商品id:'.$fid.'，商品名称：'.$title.'】');
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case"ding":
		if (!ifint($fid))alert_adm("forbidden","-1");
		$db->query("UPDATE ".__TBL_TG_PRODUCT__." SET px=".ADDTIME." WHERE id=".$fid);
		header("Location: ".SELF."?p=".$p);
	break;
	case"alldel":
		//if(!in_array('u_del',$QXARR))json_exit(array('flag'=>0,'msg'=>'权限不足'));
		$tmeplist = $list;
		if(empty($tmeplist))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($tmeplist))exit(JSON_ERROR);
		if(count($tmeplist)>=1){
			foreach($tmeplist as $id){
				$id=intval($id);
				$row = $db->ROW(__TBL_TG_PRODUCT__,"path_s,tg_uid,title,id,piclist","id=".$id,"name");
				if ($row){
					$path_s = $row['path_s'];$tg_uid = $row['tg_uid'];$title = $row['title'];$fid = $row['id'];$piclist = $row['piclist'];
					if(!empty($path_s))@up_send_admindel($path_s.'|'.smb($path_s,'b').'|'.smb($path_s,'m'));
					if(!empty($piclist)){
						$piclist = explode(',',$piclist);
						foreach ($piclist as $value) {
							@up_send_admindel($value.'|'.smb($value,'m').'|'.smb($value,'b').'|'.smb(str_replace('/shop/','/tmp/',$value),'blur'));
						}
					}
				}
				$db->query("DELETE FROM ".__TBL_TG_PRODUCT__." WHERE id=".$id);
				AddLog('【商家】->商品删除->【商家id:'.$tg_uid.'，商品id:'.$fid.'，商品名称：'.$title.'】');
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
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
<?php
$SQL="1=1";
if(ifint($tg_uid))$SQL .=" AND tg_uid=".$tg_uid;
$Skey = trimhtml($Skey);
if (!empty($Skey))$SQL .= " AND (id=".intval($Skey)." OR title LIKE '%".dataIO(trimm($Skey),'in')."%' ) ";
$Skey2 = trimhtml($Skey2);
if (!empty($Skey2))$SQL .= " AND (tg_uid=".intval($Skey2)." OR cname LIKE '%".dataIO(trimm($Skey2),'in')."%' ) ";
if(ifint($kind))$SQL.=" AND kind=".$kind;
if($flag==-1){
	$SQL.=" AND flag=-1";
}elseif($flag==2){
	$SQL.=" AND flag=2";
}
if($stock==1)$SQL.=" AND stock<=0";
$sorthref = SELF."?Skey2=$Skey2&Skey=$Skey&p=$p&tg_uid=$tg_uid&kind=$kind&sort=";
?>
<div class="navbox">
	<a href="<?php echo SELF; ?>"<?php echo ($flag != -1 && $flag != 2 && $stock != 1)?' class="ed"':'';?>>全部商品<?php if($flag != -1 && $flag != 2 && $stock != 1)echo '<b>'.$db->COUNT(__TBL_TG_PRODUCT__,$SQL).'</b>';?></a>
	<a href="<?php echo SELF; ?>?flag=-1"<?php echo ($flag == -1)?' class="ed"':'';?> title="已锁定商品前台不显示">已锁定/删除商品<?php if($flag==-1)echo '<b>'.$db->COUNT(__TBL_TG_PRODUCT__,$SQL).'</b>';?></a>
	<a href="<?php echo SELF; ?>?flag=2"<?php echo ($flag == 2)?' class="ed"':'';?> title="下架商品">下架商品<?php if($flag==1)echo '<b>'.$db->COUNT(__TBL_TG_PRODUCT__,$SQL).'</b>';?></a>
  <a href="<?php echo SELF; ?>?stock=1"<?php echo ($stock == 1)?' class="ed"':'';?> title="库存不足的商品">库存不足商品<?php if($stock==1)echo '<b>'.$db->COUNT(__TBL_TG_PRODUCT__,$SQL).'</b>';?></a>
	
	<div class="Rsobox"></div>

<div class="clear"></div></div>
<div class="fixedblank"></div>

<table class="table0" ><tr><td align="center" class="S14">
<?php if (empty($submitok)){?>
    <form name="form1" method="get" action="<?php echo SELF; ?>" style="margin-right:20px;display:inline-block">
        按商品查询 <input name="Skey" type="text" id="Skey" maxlength="25" class="W200 input size2" placeholder="按商品ID/商品名称" value="<?php echo $Skey;?>">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="kind" value="<?php echo $kind;?>" />
        <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>
    <form name="form2" method="get" action="<?php echo SELF; ?>" style="display:inline-block">
        按<?php echo $_SHOP['title'];?>查询 <input name="Skey2" type="text" id="Skey2" maxlength="25" class="W200 input size2" placeholder="按<?php echo $_SHOP['title'];?>ID/<?php echo $_SHOP['title'];?>名称" value="<?php echo $Skey2;?>">
        <input name="submitok" type="hidden" value="<?php echo $submitok; ?>" />
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="hidden" name="kind" value="<?php echo $kind;?>" />
        <input type="hidden" name="tg_uid" value="<?php echo $tg_uid;?>" />
        <input type="submit" value="搜索" class="btn size2" />
    </form>     
  	<?php }?>  
</td></tr></table>


    <?php
	switch ($sort) {
		case 'stock0':$SORT = " ORDER BY stock ";break;
		case 'stock1':$SORT = " ORDER BY stock DESC ";break;
		case 'click0':$SORT = " ORDER BY click ";break;
		case 'click1':$SORT = " ORDER BY click DESC ";break;
		case 'addtime0':$SORT = " ORDER BY addtime ";break;
		case 'addtime1':$SORT = " ORDER BY addtime DESC ";break;
		case 'flag0':$SORT = " ORDER BY flag ";break;
		case 'flag1':$SORT = " ORDER BY flag DESC ";break;
		case 'price0':$SORT = " ORDER BY price ";break;
		case 'price1':$SORT = " ORDER BY price DESC ";break;
		default:$SORT = " ORDER BY px DESC,id DESC ";break;
	}
	$rt = $db->query("SELECT id,tg_uid,path_s,flag,title,click,addtime,price,tg_uid,stock,cname,tgbfb1,tgbfb2 FROM ".__TBL_TG_PRODUCT__." WHERE ".$SQL.$SORT." LIMIT ".$_ADM['admLimit']);
	$total = $db->num_rows($rt);
	if ($total <= 0 ) {
		echo "<div class='nodataico'><i></i>暂无内容</div>";
	} else {
		$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
		?>
		<form id="www_zeai_cn_FORM">
		<table class="tablelist">
		<tr>
		<th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
		<th width="50" align="left">ID</th>
		<th width="50" align="left">置顶</th>
		<th width="80" align="left">主图</th>
		<th width="300" align="left">商品名称/来自<?php echo $_SHOP['title'];?></th>
		<th width="80" align="left">价格(元)
        <div class="sort">
            <a href="<?php echo $sorthref."price0";?>" <?php echo($sort == 'price0')?' class="ed"':''; ?>></a>
            <a href="<?php echo $sorthref."price1";?>" <?php echo($sort == 'price1')?' class="ed"':''; ?>></a>
        </div>
        </th>
		<th width="60" align="center">点击量
        <div class="sort">
            <a href="<?php echo $sorthref."click0";?>" <?php echo($sort == 'click0')?' class="ed"':''; ?>></a>
            <a href="<?php echo $sorthref."click1";?>" <?php echo($sort == 'click1')?' class="ed"':''; ?>></a>
        </div>
        </th>
		<th width="15" align="center"></th>
		<th width="100" align="left">分销推广</th>
		<th>&nbsp;</th>
		<th width="70" align="center">库存
        <div class="sort">
            <a href="<?php echo $sorthref."stock0";?>" <?php echo($sort == 'stock0')?' class="ed"':''; ?>></a>
            <a href="<?php echo $sorthref."stock1";?>" <?php echo($sort == 'stock1')?' class="ed"':''; ?>></a>
        </div>
        </th>
		<th width="70" align="center">发布时间
        <div class="sort">
            <a href="<?php echo $sorthref."addtime0";?>" <?php echo($sort == 'addtime0')?' class="ed"':''; ?>></a>
            <a href="<?php echo $sorthref."addtime1";?>" <?php echo($sort == 'addtime1')?' class="ed"':''; ?>></a>
        </div>
        </th>
		<th width="130" align="center">状态
        <div class="sort">
            <a href="<?php echo $sorthref."flag0";?>" <?php echo($sort == 'flag0')?' class="ed"':''; ?>></a>
            <a href="<?php echo $sorthref."flag1";?>" <?php echo($sort == 'flag1')?' class="ed"':''; ?>></a>
        </div>
        </th>
		<th width="60" align="center">修改</th>
		<th width="60" align="center">删除</th>
		</tr>
		<?php
		for($i=1;$i<=$pagesize;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$id        = $rows['id'];
			$tg_uid    = $rows['tg_uid'];
			$path_s    = $rows['path_s'];
			$flag      = $rows['flag'];
			$tg_uid    = $rows['tg_uid'];
			$click     = $rows['click'];
			$addtime   = YmdHis($rows['addtime']);
			$title     = dataIO($rows['title'],'out');
			$price     = $rows['price'];
			$stock     = $rows['stock'];
			$tgbfb1    = intval($rows['tgbfb1']);
			$tgbfb2    = intval($rows['tgbfb2']);
			$tgbfb_str ='';
			if($tgbfb1>0)$tgbfb_str.='<div>直接奖：'.$tgbfb1.'%<div>';
			if($tgbfb2>0)$tgbfb_str.='<div>团队奖：'.$tgbfb2.'%</div>';
			$cname     = dataIO($rows['cname'],'out');
			if(!empty($Skey)){
				$title = str_replace($Skey,'<font class="Cf00 B">'.$Skey.'</font>',$title);
			}
			if(!empty($path_s)){
				$path_s_url = $_ZEAI['up2'].'/'.$path_s;
				$path_s_str = '<img src="'.$path_s_url.'">';
			}else{
				$path_s_url = '';
				$path_s_str = '';
			}
			if($stock<=0){
				$stock_str='<font class="Cf00 S12" title="库存不足，无法下单哦">'.$stock.'<br>库存不足</font>';
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
            <a href="javascript:;" class="pic60 " onClick="parent.piczoom('<?php echo getpath_smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a>
			<?php }?>
        </td>
		<td width="300" align="left" class="C999 lineH150">
        <div class="S14 "><a href="TG_u_product.php?return=TG_product&sort=<?php echo $sort;?>&submitok=mod&tg_uid=<?php echo $tg_uid;?>&fid=<?php echo $id;?>&p=<?php echo $p;?>" ><?php echo $title;?></a></div >
        <?php if (!empty($cname)){?><div class="S12 C999"><?php echo $cname;?>(<?php echo $tg_uid;?>)</div><?php }?>
        </td>
		<td width="80" align="left" class="Cf00">￥<?php echo str_replace(".00","",$price);?></td>
		<td width="60" align="center"><?php echo $click;?></td>
		<td width="15" align="center">&nbsp;</td>
		<td width="100" align="left" class="lineH150"><?php echo $tgbfb_str;?></td>
		<td align="left" >&nbsp;</td>
		<td width="70" align="center"><?php echo $stock_str;?></td>
		<td width="70" align="center" class="C999"><?php echo $addtime;?></td>
		<td width="130" align="center" class="C999">
		  <?php if($flag==-1){?><a clsid="<?php echo $id;?>" class="aHEI flag" title="点击恢复">已锁定/删除</a><?php }?>
		  <?php if($flag==1){?><a clsid="<?php echo $id;?>" class="aLV flag" title="点击锁定">正常</a><?php }?>
		  <?php if($flag==2){?><a clsid="<?php echo $id;?>" class="aHUI flag" title="点击恢复">已下架</a><?php }?>
		  </td>
		<td width="60" align="center">
		  <a class="editico tips" tips-title='修改' tips-direction='left' clsid="<?php echo $id; ?>" tg_uid="<?php echo $tg_uid; ?>"></a>
		</td>
		<td width="60" align="center"><a clsid="<?php echo $id; ?>" class="delico" title='删除'></a></td>
		</tr>
		<?php } ?>
		</table>
        <div class="listbottombox">
            <input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
            <input type="hidden" name="submitok" id="submitok" value="" />
            <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
            <?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
        </div>
</form>
		<script>
		var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
		zeai.listEach('.editico',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			var tg_uid = parseInt(obj.getAttribute("tg_uid"));
			obj.onclick = function(){
				zeai.openurl('TG_u_product.php?return=TG_product&sort=<?php echo $sort;?>&p=<?php echo $p;?>&submitok=mod&fid='+id+'&tg_uid='+tg_uid);
			}
		});
		zeai.listEach('.delico',function(obj){
			var id = parseInt(obj.getAttribute("clsid"));
			obj.onclick = function(){
				zeai.confirm('★请慎重★　确定真的要删除么？',function(){
					zeai.ajax({url:'<?php echo SELF;?>?submitok=ajax_del&fid='+id},function(e){
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
				zeai.ajax({url:'<?php echo SELF;?>?submitok=ajax_flag&fid='+id},function(e){
					rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
				});
			}
		});

		o('btndellist').onclick = function() {
			allList({
				btnobj:this,
				url:'TG_product'+zeai.ajxext+'submitok=alldel',
				title:'批量删除',
				msg:'正在删除中...',
				ifjson:true,
				ifconfirm:true
			});	
		}

		</script>
		<script src="js/zeai_tablelist.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<?php }?>

<br><br><br>
<?php require_once 'bottomadm.php';?>
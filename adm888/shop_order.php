<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('cert',$QXARR) && !in_array('shop',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_shop.php';
$orderflagARR = json_decode($_SHOP['orderflag'],true);
switch ($submitok) {
	case"shop_orderflag_mod_update":
		if (!ifint($id))exit(JSON_ERROR);
		$row = $db->ROW(__TBL_SHOP_ORDER__,"cid","id=".$id,"num");
		if ($row){$cid = intval($row[0]);}else{exit(JSON_ERROR);}		
		$SQL = "flag=".intval($orderflag);
        if (count($orderflagARR) >= 1 && is_array($orderflagARR)){
            foreach ($orderflagARR as $V){
                $f_id  = $V['i'];
				$ofstr = 'orderflagtime'.$f_id ;
				$orderflagtime = $$ofstr;
				if(ifdatetime($orderflagtime)){
					$orderflagtime = strtotime($orderflagtime);
					switch ($f_id) {
						case 0:$SQL .= ",addtime=".$orderflagtime;break;
						case 1:$SQL .= ",paytime=".$orderflagtime;break;
						case 2:$SQL .= ",fahuotime=".$orderflagtime;break;
						case 3:$SQL .= ",endtime=".$orderflagtime;break;
						case 4:$SQL .= ",canceltime=".$orderflagtime;break;
						case 5:$SQL .= ",tuihuotime=".$orderflagtime;break;
						case 6:$SQL .= ",tuihuoendtime=".$orderflagtime;break;
						case 7:$SQL .= ",tuikuantime=".$orderflagtime;break;
						case 8:$SQL .= ",tuikuanendtime=".$orderflagtime;break;
						case 9:$SQL .= ",yuyuetime=".$orderflagtime;break;
						case 10:$SQL.= ",yuyueendtime=".$orderflagtime;break;
					}
				}
		
			}
		}
		$db->query("UPDATE ".__TBL_SHOP_ORDER__." SET ".$SQL." WHERE id=".$id);
		AddLog('【'.$_SHOP['title'].'(ID：'.$cid.')】订单状态修改');
		json_exit(array('flag'=>1,'msg'=>'设置成功'));
	break;
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$id=intval($v);
				$row = $db->ROW(__TBL_SHOP_ORDER__,"orderid,cid","id=".$id,"num");
				if ($row){
					$orderid=$row[0];$cid=$row[1];
					$db->query("DELETE FROM ".__TBL_SHOP_ORDER__." WHERE id=".$id);
					AddLog('删除【'.$_SHOP['title'].'(ID：'.$cid.')】订单->订单号：'.$orderid);
				}
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
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:1111px;margin:20px 20px 50px 20px}
.table0{min-width:900px;width:98%;margin:10px 20px 20px 20px}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}
.myinfobfb0,.myinfobfb1,.myinfobfb2{font-family:Arial;font-size:18px;display:block;height:20px;line-height:24px}
.myinfobfb0{color:#999}
.myinfobfb1{color:#f70}
.myinfobfb2{color:#090}
.ped{color:#FF5722;border-bottom:2px #FF5722 solid;padding-bottom:5px}
</style>
<body>
<?php if($submitok == 'flagmod'){
	if (!ifint($id))alert_adm("Forbidden","-1");
	$rt = $db->query("SELECT flag,addtime,paytime,fahuotime,endtime,canceltime,tuihuotime,tuihuoendtime,tuikuantime,tuikuanendtime,yuyuetime,yuyueendtime FROM ".__TBL_SHOP_ORDER__." WHERE id=".$id);
	if($db->num_rows($rt)){
		$row  = $db->fetch_array($rt,'name');
		$flag       = intval($row['flag']);
		$addtime    = $row['addtime'];
		$paytime    = $row['paytime'];
		$fahuotime  = $row['fahuotime'];		
		$endtime    = $row['endtime'];
		$canceltime = $row['canceltime'];	
		$tuihuotime = $row['tuihuotime'];
		$tuihuoendtime = $row['tuihuoendtime'];
		$tuikuantime = $row['tuikuantime'];
		$tuikuanendtime = $row['tuikuanendtime'];
		$yuyuetime = $row['yuyuetime'];
		$yuyueendtime = $row['yuyueendtime'];
	}else{alert_adm("订单不存在！","-1");}?>
	<style>
	.RCW li{width:100%;height:30px;line-height:30px}
	.RCW li b{width:100px}
    .tdL{width:20%}
    </style>
    <form id="ZEAIFORM">
    <table class="table W90_ Mtop20">
    <tr><td class="tdL">订单状态</td><td class="tdR">
      <?php
		function shop_flagtime($flag) {
			global $addtime,$paytime,$fahuotime,$endtime,$canceltime,$tuihuotime,$tuihuoendtime,$tuikuantime,$tuikuanendtime,$yuyuetime,$yuyueendtime;
			switch ($flag) {
				case 0:$ftime = $addtime;break;
				case 1:$ftime = $paytime;break;
				case 2:$ftime = $fahuotime;break;
				case 3:$ftime = $endtime;break;
				case 4:$ftime = $canceltime;break;
				case 5:$ftime = $tuihuotime;break;
				case 6:$ftime = $tuihuoendtime;break;
				case 7:$ftime = $tuikuantime;break;
				case 8:$ftime = $tuikuanendtime;break;
				case 9:$ftime = $yuyuetime;break;
				case 10:$ftime = $yuyueendtime;break;
				default:$ftime = '';break;
			}
			return $ftime;
		}
        if (count($orderflagARR) >= 1 && is_array($orderflagARR)){
            foreach ($orderflagARR as $V){
                $f_id    = $V['i'];
                $f_title = $V['v2'];
				$f_c     = $V['c'];
                $fcls    = ($f_id==$flag)?' checked':'';
				$IPT     = '<input type="text" maxlength="19" class="input size2 W200" name="orderflagtime'.$f_id.'" value="'.YmdHis(shop_flagtime($f_id)).'" placeholder="对应状态时间" />';
				?>
		<ul class="size2 RCW"><li><input type="radio" class="radioskin" id="orderflag<?php echo $f_id;?>" name="orderflag" value="<?php echo $f_id;?>" <?php echo $fcls;?>><label for="orderflag<?php echo $f_id;?>" class="radioskin-label"><i class="i1"></i><b style="color:<?php echo $f_c;?>"><?php echo $f_title;?></b></label><?php echo $IPT;?></li></ul>
      		<?php
        	}
        }else{alert_adm('配置文件状态数组丢失','back');}
		?>
    </td>
      </tr>
    </table>
    <input type="hidden" name="submitok" value="shop_orderflag_mod_update" />
    <input type="hidden" name="id" value="<?php echo $id;?>" />
</form>
    <br><br><br><div class="savebtnbox"><button type="button" id="save" class="btn size3 HUANG3">保存修改</button></div>
<script>
    save.onclick=function(){
/*        if (!zeai.form.ifradio('orderflag')){
            zeai.msg('请选择【状态】');
            return false;
        }
*/
        zeai.confirm('<b class="S18">确定修改么？</b><br>此修改属于硬改，不触发任何通知和操作！',function(){
			zeai.ajax({url:'shop_order'+zeai.extname,form:ZEAIFORM},function(e){rs=zeai.jsoneval(e);
				window.parent.zeai.msg(rs.msg);
				if(rs.flag==1){setTimeout(function(){window.parent.location.reload(true);},1000);}
			});
		});
    }
    </script>
<?php exit;}?>


<?php
$SQL = "";
$Skey = trimm($Skey);
if (ifint($Skey)){
	$SQL = " AND (O.tg_uid=$Skey) ";
}elseif(!empty($Skey)){
	$SQL = " AND ( ( O.orderid='$Skey' ) OR ( P.title LIKE '%".$Skey."%' ) OR ( P.cname LIKE '%".$Skey."%' ) )";
}
switch ($f) {
	case 'f0':$SQL .= " AND O.flag=0";break;
	case 'f1':$SQL .= " AND O.flag=1";break;
	case 'f2':$SQL .= " AND O.flag=2";break;
	case 'f3':$SQL .= " AND O.flag=3";break;
	case 'f4':$SQL .= " AND O.flag=4";break;
	case 'f5':$SQL .= " AND O.flag=5";break;
	case 'f6':$SQL .= " AND O.flag=6";break;
	case 'f7':$SQL .= " AND O.flag=7";break;
	case 'f8':$SQL .= " AND O.flag=8";break;
	case 'f9':$SQL .= " AND O.flag=9";break;
	case 'f10':$SQL .= " AND O.flag=10";break;
}
$rt = $db->query("SELECT P.path_s,P.cname,P.title,O.* FROM ".__TBL_TG_PRODUCT__." P ,".__TBL_SHOP_ORDER__." O WHERE P.id=O.pid ".$SQL." ORDER BY O.id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
?>
<div class="navbox">
    <a href="shop_order.php"<?php echo (empty($f))?' class="ed"':'';?>>全部订单<?php if(empty($f))echo '<b>'.$total.'</b>';?></a>
	<?php
        $orderflagARR = json_decode($_SHOP['orderflag'],true);
        if (count($orderflagARR) >= 1 && is_array($orderflagARR)){
            foreach ($orderflagARR as $V){
                $f_id    = 'f'.$V['i'];
                $f_title = $V['v2'];
                $fcls = ($f_id==$f)?' class="ed"':'';?>
                <a href="shop_order.php?f=<?php echo $f_id;?>" <?php echo $fcls;?> ><?php echo $f_title;if($f_id==$f)echo '<b>'.$total.'</b>';?></a>
                <?php
            }
        }
	?>    
    <div class="Rsobox"></div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<table class="table0" ><tr><td align="center" class="S14" >
    <form id="Zeai_search_form" method="get" action="<?php echo SELF; ?>">
    订单查询 <input name="Skey" type="text" id="Skey" size="32" maxlength="30" class="input size2" placeholder="输入：用户ID/订单号/商品名称/<?php echo $_SHOP['title'];?>名称">
    <input type="hidden" name="p" value="<?php echo $p;?>" />
    <input type="submit" value="搜索" class="btn size2" />
    </form>    
</td></tr></table>

<?php
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无信息<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
<table class="tablelist">
    <form id="www_zeai_cn_FORM">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="60" align="left">ID</th>
    <th width="75" align="left">商品主图</th>
    <th width="220">订单号及商品信息</th>
    <th width="11">&nbsp;</th>
    <th width="150" align="left">卖家信息</th>
    <th width="100" align="left">买家信息</th>
    <th width="150" align="left">快递物流/备注</th>
    <th width="100" align="left">订单总价(元)</th>
    <th width="70" align="left">下单时间</th>
	<th align="left" >&nbsp;</th>
	<th width="140" align="center" >状态/时间/操作</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id = $rows['id'];
		$uname = str_replace($Skeyword,"<font color=red><b>".$Skeyword."</b></font>",$uname);
		$path_s    = $rows['path_s'];
		$cname     = dataIO($rows['cname'],'out');
		$ptitle    = dataIO($rows['title'],'out');
		$orderid   = $rows['orderid'];
		$price     = str_replace(".00","",$rows['price']);;
		$num       = $rows['num'];
		$orderprice= $rows['orderprice'];
		$orderkind = intval($rows['orderkind']);
		$cid       = $rows['cid'];
		$flag      = $rows['flag'];
		$truename  = dataIO($rows['truename'],'out');
		$bz  = dataIO($rows['bz'],'out');
		$tg_uid    = $rows['tg_uid'];
		$kuaidi_name = dataIO($rows['kuaidi_name'],'out');
		$kuaidi_code = dataIO($rows['kuaidi_code'],'out');
		$addtime    = YmdHis($rows['addtime']);
		$paytime    = YmdHis($rows['paytime']);
		$fahuotime  = YmdHis($rows['fahuotime']);		
		$endtime    = YmdHis($rows['endtime']);
		$canceltime = YmdHis($rows['canceltime']);	
		$tuihuotime = YmdHis($rows['tuihuotime']);
		$tuihuoendtime = YmdHis($rows['tuihuoendtime']);
		$tuikuantime = YmdHis($rows['tuikuantime']);
		$tuikuanendtime = YmdHis($rows['tuikuanendtime']);
		$yuyuetime = YmdHis($rows['yuyuetime']);
		$yuyueendtime = YmdHis($rows['yuyueendtime']);
		$tgbfb1    = intval($rows['tgbfb1']);
		$tgbfb2    = intval($rows['tgbfb2']);
		$tgbfb_str ='';
		if($tgbfb1>0)$tgbfb_str.='<div>直接奖：'.$tgbfb1.'%<div>';
		if($tgbfb2>0)$tgbfb_str.='<div>团队奖：'.$tgbfb2.'%</div>';
		if(!empty($path_s)){
			$path_s_url = $_ZEAI['up2'].'/'.$path_s;
			$path_s_str = '<img src="'.$path_s_url.'">';
		}else{
			$path_s_url = '';
			$path_s_str = '无图';
		}
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" cid="<?php echo $cid; ?>" tg_uid="<?php echo $tg_uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
      </td>
        <td width="60" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="75" align="left" style="padding:15px 0"><a href="javascript:;" class="pic60" onClick="parent.piczoom('<?php echo smb($path_s_url,'b'); ?>');"><?php echo $path_s_str; ?></a></td>
      <td width="220" align="left" class="lineH150 C999" style="padding:10px 0"><?php echo "<font class='C000'>".$orderid."</font><div style='margin:5px 0 0'>".$ptitle."</div><font class='C999'>¥".str_replace(".00","",$price)."</font>";?>
</td>
      <td width="11" align="left" class="lineH150" style="padding:10px 0">&nbsp;</td>
    <td width="150" align="left" class="lineH150"><?php echo $cname;?><div class="C999 S12"><?php echo $_SHOP['title'];?>ID：<?php echo $cid;?></div></td>
    <td width="100" align="left" class="lineH150"><?php echo $truename;?><div class="C999 S12">ID：<?php echo $tg_uid;?></div></td>
    <td width="150" align="left" class="C333 lineH150">
    <?php if (!empty($kuaidi_name)){?>
    <div><font class="C999">快递：</font><?php echo $kuaidi_name;?></div>
    <div><font class="C999">单号：</font><?php echo $kuaidi_code;?></div>
    <?php }
    if (!empty($bz)){?>
    <div><font class="C999">备注：</font><?php echo $bz;?></div>
    <?php }?>
   	</td>
    <td width="100" align="left" class=" lineH150">
    <?php if ($orderkind == 1){?>
    	<font class="C090 S14">预约</font>
        <div class="C999 S12">数量×<?php echo $num;?></div>
    <?php }else{ ?>
    	<font class="Cf00 S14">¥<?php echo str_replace(".00","",$orderprice);?></font>
        <div class="C999 S12">¥<?php echo $price;?>×<?php echo $num;?></div>
        <?php echo $tgbfb_str;?>
    <?php }?>
    </td>
    <td width="70" align="left" class="C999"><?php echo $addtime;?></td>
    <td align="left" class="C999 lineH200 padding10">&nbsp;</td>
    <td width="140" align="center" >
      <a href="javascript:;" photo_s_url="<?php echo $path_s_url; ?>" title="查看/修改/状态" class="btn size2 BAI flag" id="<?php echo $id; ?>" orderid="<?php echo urlencode($orderid);?>" style="margin:5px 0"><?php echo arrT($_SHOP['orderflag'],$flag,'v2');?></a></div>
		<?php
        switch($flag){
            case"0":break;
            case"1":echo '<br>'.$paytime;break;
            case"2":echo '<br>'.$fahuotime;break;
            case"3":echo '<br>'.$endtime;break;
            case"4":echo '<br>'.$canceltime;break;
            case"5":echo '<br>'.$tuihuotime;break;
            case"6":echo '<br>'.$tuihuoendtime;break;
            case"7":echo '<br>'.$tuikuantime;break;
            case"8":echo '<br>'.$tuikuanendtime;break;
            case"9":echo '<br>'.$yuyuetime;break;
            case"10":echo '<br>'.$yuyueendtime;break;
        }
        ?>
      </td>
	</tr>
	<?php } ?>
    <div class="listbottombox">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnsend1" value="" class="btn size2 disabled action">发送消息 <i class="ico">&#xe62d;</i> 卖家</button>　
    <button type="button" id="btnsend2" value="" class="btn size2 disabled action">发送消息 <i class="ico">&#xe62d;</i> 买家</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </div>    
    </form>
</table>

<script>
var bg = '<?php echo $_Style['list_bg']; ?>',overbg = '<?php echo $_Style['list_overbg']; ?>',selectbg = '<?php echo $_Style['list_selectbg']; ?>';
zeai.listEach('.flag',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("id")),
		orderid = obj.getAttribute("orderid"),
		photo_s_url = obj.getAttribute("photo_s_url"),
		photo_s_iframe = '<img src="'+photo_s_url+'" class="photo_s_iframe">';
		zeai.iframe(photo_s_iframe+'【'+decodeURIComponent(orderid)+'】订单状态','shop_order'+zeai.ajxext+'submitok=flagmod&id='+id,600,460);
	}
});
if(!zeai.empty(o('btnsend1')))o('btnsend1').onclick = function() {sendTipFnTGU(this,1);}
if(!zeai.empty(o('btnsend2')))o('btnsend2').onclick = function() {sendTipFnTGU(this,2);}
function sendTipFnTGU(btnobj,kind){
	var kindstr;
	switch (kind) {
		case 1:kindstr = '【卖家】';break;
		case 2:kindstr = '【买家】';break;
		default:kindstr = '【卖家】或【买家】';break;
	}
	if (btnobj.hasClass('disabled')){
		zeai.alert('请选择要发送的'+kindstr);
		return false;
	}
	var arr = document.getElementsByName('list[]'),ulist = [];
	for( key in arr){
		if (arr[key].checked){
			if(kind==1){
				ulist.push(arr[key].getAttribute("cid"));
			}else if(kind==2){
				ulist.push(arr[key].getAttribute("tg_uid"));
			}
		}
	}
	ulist = ulist.delRepeat();
	ulist = ulist.join("_");
	if (zeai.empty(ulist)){
		zeai.alert('请选择要发送的'+kindstr);
	}else{
		zeai.iframe('发送消息 <i class="ico">&#xe62d;</i> '+kindstr,'u_tip.php?kind=TG&ifshop=1&ulist='+ulist,600,500);
	}
}
if(!zeai.empty(o('btndellist')))o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'shop_order'+zeai.ajxext+'submitok=alldel',
		title:'批量删除 <font class="Cf00">请慎重，删除后不可恢复</font> ',
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
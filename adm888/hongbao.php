<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('hongbao',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
$_ZEAI['HB_refundtime'] = 3;
switch ($submitok) {
	
	case"dataflag1":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要审核的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);$uid=$id;


			}
		}
		json_exit(array('flag'=>1,'msg'=>'审核成功'));
	break;
	case"modflag":
		if (!ifint($classid))callmsg("forbidden","-1");
		$row = $db->ROW(__TBL_HONGBAO__,"flag","id=$classid");
		if(!$row){
			callmsg("您要操作的信息不存在或已经删除！","-1");
		}else{
			$flag = $row[0];
			$SQL = "";
			if ($flag == 2){
				callmsg("过期信息不可本操作！","-1");
			}else{
				switch($flag){
					case"-1":$SQL="flag=1";break;
					case"0":$SQL="flag=1";break;
					case"1":$SQL="flag=-1";break;
				}
				$db->query("UPDATE ".__TBL_HONGBAO__." SET ".$SQL." WHERE id=".$classid);
				AddLog('【红包管理】修改红包状态->【id:'.$classid.'】');
			}
			header("Location: $SELF?kind=$kind&t=$t&p=$p");
		}
	break;
	case"delupdate":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $id){
				$id=intval($id);
				$db->query("DELETE FROM ".__TBL_HONGBAO_USER__." WHERE fid=".$id);
				$db->query("DELETE FROM ".__TBL_HONGBAO__." WHERE id=".$id);
				AddLog('【红包管理】删除红包->【id:'.$id.'】');
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
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:1200px;margin:20px 20px 50px 20px}
.noU58{position:relative}
.noU58 span{display:block;width:100%;line-height:24px;position:absolute;top:17px;background-color:rgba(0,0,0,0.4);color:#ccc;font-size:12px}


.tablelist .li{font-size:14px;line-height:200%}
.tablelist .li img{max-width:50px;max-height:50px;margin-left:5px}

.tablelist .li2 li{width:50px;height:50px;float:left;margin:10px;position:relative}
.tablelist .li2 li img{width:50px;height:50px;border-radius:2px;cursor:zoom-in;display:block}

.delico{;top:-10px;right:-10px;position:absolute;width:20px;height:20px;display:inline-block;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3);background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.delico:hover{background-position:-100px top;cursor:pointer}

.tablelist .sx{padding:5px 0 0 15px}
.table0{min-width:1600px;width:98%;margin:10px 20px 20px 20px}

</style>
<?php
?>
<body>
<div class="navbox">

    <a href="hongbao.php" <?php echo (empty($t))?' class="ed"':''; ?>>红包管理<?php echo '<b>'.$db->COUNT(__TBL_HONGBAO__).'</b>';?></a>
    <a href="<?php echo SELF; ?>?t=3"<?php echo ($t == 3)?" class='ed'":""; ?>>已隐藏</a>
  <div class="Rsobox">

        
            
      
            
        
        
        
        
        
  </div>
  
  
<div class="clear"></div></div>
<div class="fixedblank"></div>
<table class="table0" style="min-width:980px">
    <tr>
    <td width="260" align="left" class="S14"><form name="form2" method="get" action="<?php echo $SELF; ?>">
      <input name="kuid" type="text" id="kuid" size="15" maxlength="8" class="input size2" placeholder="按会员UID筛选">
      <input type="submit" value="搜索" class="btn size2" />
      <input type="hidden" name="t" value="<?php echo $t; ?>" />
    </form></td>
    <td width="260" align="right"></td>
    </tr>
    </table>  
<?php
$SQL = "";
$Skeyword = dataIO($Skeyword,'in');
if (!empty($Skeyword))$SQL   .= " AND ( ( a.id = '$Skeyword' ) OR ( a.uid = '$Skeyword' ) ) ";
if($t == 2)$SQL .= " AND a.flag=0";
if($t == 3)$SQL  .= " AND a.flag=-1";
if (ifint($kuid))$SQL .= " AND b.id =".$kuid;
$rt = $db->query("SELECT a.*,b.uname,b.nickname,b.sex,b.grade,b.photo_s,b.photo_f,b.openid,b.money AS umoney FROM ".__TBL_HONGBAO__." a,".__TBL_USER__." b WHERE a.uid=b.id ".$SQL." ORDER BY a.id DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容符合<br><a class='aHUANGed' href='javascript:history.back(-1)'>重新筛选</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=$_ADM['admPageSize'];require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i></label></th>
    <th width="70" align="left">ID</th>
    <th align="left">发布人</th>
    <th width="180" align="left">&nbsp;</th>
    <th width="130" align="left">筛选此会员</th>
    <th width="80">类型</th>
    <th width="80">金额</th>
    <th width="110">已领取/打赏人数</th>
    <th width="60">人气</th>
    <th align="center">&nbsp;</th>
	<th width="110" align="left" >详情</th>
    <th width="70" align="center">发布时间</th>
    <th width="80" align="center">状态</th>
    <th width="10" align="center">&nbsp;</th>
    </tr>
    <?php
	for($i=1;$i<=$pagesize;$i++) {
		$rows = $db->fetch_array($rt,'name');
		if(!$rows) break;
		$id       = $rows['id'];
		$uid      = $rows['uid'];
		$uname    = $rows['uname'];
		$nickname  = dataIO($rows['nickname'],'out');
		$openid   = $rows['openid'];
		$flag     = $rows['flag'];
		$kind     = $rows['kind'];
		$addtime  = $rows['addtime'];
		$money     = $rows['money'];
		$data_money= $rows['umoney'];
		//
		if ($kind == 3){
			$money_str = ($money == 0)?'不限':'至少'.$money.'元';
			$acls = 'aLVed';
		}else{
			$money_str = $rows['money'];
			$acls = 'aHONGed';
		}
		$difftime = ADDTIME - $addtime;
		//运气或定额过期超时退款
		if ( $difftime > $_ZEAI['HB_refundtime']*86400 && ($kind == 1 || $kind == 2) && $flag==1 ){
			$db->query("UPDATE ".__TBL_HONGBAO__." SET flag=2 WHERE id=".$id);
			//统计已抢金额
			$rt=$db->query("SELECT SUM(money) FROM ".__TBL_HONGBAO_USER__." WHERE fid=".$id);
			$row = $db->fetch_array($rt);
			$nomoney = intval($row[0]);
			$endtotalmoney = $money - $nomoney;
			if ($endtotalmoney > 0){
				//money_list
				$endnum  = $endtotalmoney + $data_money;
				$db->query("UPDATE ".__TBL_USER__." SET money=$endnum WHERE id=".$uid);
				$content = '红包未抢完退款';
				$db->AddLovebRmbList($uid,$content,$endtotalmoney,'money',15);	
				//weixin_mb
				if (!empty($openid)){
					$first  = $nickname."您好，您的余额账户有变动(红包退款)：";
					$remark = $content."，查看详情";
					$url    = urlencode(mHref('money'));
					wx_mb_sent('mbbh=ZEAI_LOVEB_UPDATE&openid='.$openid.'&money='.$endtotalmoney.'&money_total='.$endnum.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
				}
			}
			//
			$flag = 2;
		}
		$href = $uhref = Href('hongbao',$id);;
		//
		$photo_s   = $rows['photo_s'];
		$photo_f   = $rows['photo_f'];
		$sex       = $rows['sex'];
		$grade     = $rows['grade'];
		if(!empty($photo_s)){
			$photo_s_url = $_ZEAI['up2'].'/'.$photo_s;
			$photo_s_str = '<img src="'.$photo_s_url.'">';
		}else{
			$photo_s_str = '';
		}
		$photo_fstr = ($photo_f == 0 && !empty($photo_s))?'<span>审核中</span>':'';
		$uhref = Href('u',$uid);
	?>
    <tr id="tr<?php echo $id;?>">
      <td width="20" height="40"><input type="checkbox" name="list[]" value="<?php echo $id; ?>" uid="<?php echo $uid; ?>" id="id<?php echo $id; ?>" class="checkskin"><label for="id<?php echo $id; ?>" class="checkskin-label"><i class="i1"></i></label>
        </td>
        <td width="70" align="left" class="C999"><label for="id<?php echo $id; ?>"><?php echo $id;?></label></td>
        <td width="70" align="left">
        	<a href="<?php echo $uhref; ?>" class="noU58 yuan sex<?php echo $sex; ?>" target="_blank"><?php echo $photo_s_str; ?><?php echo $photo_fstr; ?></a>
        </td>
      <td width="180" align="left" class="S12" style="padding:10px 0">
      
      <a href="<?php echo $uhref; ?>" target="_blank">
  <?php echo uicon($sex.$grade); ?>
  <?php echo '<font class="S14">'.$uname.'</font>';?>
  <font class="uleft">
  <?php
  echo 'UID：'.$uid."</br>";
  echo $nickname;
  ?>
  </font>
  </a>      

    </td>
      <td width="130" align="left" class="S12" style="padding:10px 0"><a href="<?php echo SELF; ?>?kuid=<?php echo $uid; ?>&t=<?php echo $t; ?>" class="aHUI">筛选此会员</a></td>
    <td width="80" align="left"><?php switch ($rows['kind']){case 1:echo "运气红包";break;case 2:echo "定额红包";break;case 3:echo "讨红包";break;}?></td>
    <td width="80" align="left"><?php echo $money_str;?></td>
    <td width="110" align="left"><?php echo $db->COUNT(__TBL_HONGBAO_USER__,"fid=".$id);?></td>
    <td width="60" align="left"><?php echo $rows['click'];?></td>
    <td align="center"><?php
			if ($flag == 0){
				echo '<font class="S16 C999">审核中</font>';
			}elseif($flag == 2){
				echo '<font class="S16 C090">结束或过期</font>';
			}elseif($flag == 1 && ($kind == 1 || $kind == 2)){
				$totals  = $_ZEAI['HB_refundtime']*86400 - $difftime;
				$day     = intval($totals/86400 );
				$hour    = intval(($totals % 86400)/3600);
				$hourmod = ($totals % 86400)/3600 - $hour;
				$minute  = intval($hourmod*60);
				$outtime  = "";
				$outtime .= ($day > 0)?"<span class='Cf00'>$day</span> 天 ":"";
				$outtime .= ($hour > 0)?"<span class='Cf00'>$hour</span> 小时 ":"";
				$outtime .= ($minute > 0)?"<span class='Cf00'>$minute</span> 分钟 ":"";
				echo '<font class="S16 Cf60">进行中</font>';
				if (!empty($outtime))echo '<br>离结束还有 '.$outtime;
           	}?></td>
    <td width="110" align="left"><a href="<?php echo $href; ?>" class="<?php echo $acls; ?>" target="_blank">红包详情</a></td>
	<td width="70" align="center" class="C999"><?php echo YmdHis($addtime);?></td>
	<td width="80" align="center">
  <?php
$fHREF = SELF."?submitok=modflag&classid=$id&t=$t&p=$p";
if($flag==-1){?><a href="<?php echo $fHREF;?>" class="aLAN" title="点击显示">隐藏</a><?php }?>
  <?php if($flag==0){?><a href="<?php echo $fHREF;?>" class="aHUANG" title="点击审核">未审</a><?php }?>
  <?php if($flag==1){?><a href="<?php echo $fHREF;?>" class="aLV" title="点击隐藏">正常</a><?php }?>
  <?php if($flag==2){?><a href="<?php echo $fHREF;?>" class="aHUI">过期</a><?php }?>
	  </td>
      <td width="10" align="center">&nbsp;</td>
    </tr>
	<?php } ?>
    <tfoot><tr>
    <td colspan="14">
	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" id="btndellist" class="btn size2 HEI2 disabled action">批量删除</button>　
    <button type="button" id="btnflaglist" class="btn size2 LV disabled action" style="display:none">批量审核</button>
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn2(this);">发送消息</button>
    <input type="hidden" name="submitok" id="submitok" value="" />
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
</form>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';

o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'hongbao'+zeai.ajxext+'submitok=delupdate',
		title:'批量删除',
		msg:'批量删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}



</script>
<script src="js/zeai_tablelist.js"></script>

<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
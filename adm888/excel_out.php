<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
if(!in_array('excel_out',$QXARR))exit(noauth());
require_once ZEAI.'cache/udata.php';

header("Cache-control: private");
if ($submitok == "excelupdate") {
	$nodatatext = '未填';
	$sDATE1 = strtotime($sDATE1.' 00:00:01');
	$sDATE2 = strtotime($sDATE2.' 23:59:59');
	$rt = $db->query("SELECT id,uname,nickname,sex,love,birthday,pay,job,heigh,weigh,edu,qq,weixin,mob,email,regtime,areatitle,truename,identitynum,bz FROM ".__TBL_USER__." WHERE regtime >= '$sDATE1' AND regtime <= '$sDATE2' ORDER BY id DESC");
	$total = $db->num_rows($rt);
	if($total>0){
		$content = "<meta http-equiv='Content-Type' content='text/html;charset=utf-8'><table border='1' cellpadding='0' cellspacing='0' bordercolor='#000000'><tr style='background:#FF6F6F;color:#fff'><td>UID</td><td>用户名</td><td>昵称</td><td>性别</td><td>出生年月</td><td>身高</td><td>体重</td><td>学历</td><td>婚姻状况</td><td>月收入</td><td>联系电话</td><td>QQ</td><td>微信</td><td>邮箱</td><td>地区</td><td>注册时间</td><td>真实姓名</td><td>身份证号</td><td>备注</td></tr>";
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'name');
			if(!$rows) break;
			$uname = dataIO($rows['uname'],'out');
			$nickname = dataIO($rows['nickname'],'out');
			$sex      = $rows['sex'];
			$love     = $rows['love'];
			$birthday = $rows['birthday'];
			$pay   = $rows['pay'];
			$heigh = $rows['heigh'];
			$weigh = $rows['weigh'];
			$edu   = $rows['edu'];
			$mob   = dataIO($rows['mob'],'out');
			$truename = dataIO($rows['truename'],'out');
			$identitynum = dataIO($rows['identitynum'],'out');
			$qq    = dataIO($rows['qq'],'out');
			$weixin = dataIO($rows['weixin'],'out');
			$email = dataIO($rows['email'],'out');
			$bz = dataIO($rows['bz'],'out');
			if ($sex == 1){
				$sex = '男';
			} else {
				$sex = '女';
			}
			$love = udata('love',$love);
			$pay  = udata('pay',$pay);
			$edu  = udata('edu',$edu);
			
			$areatitle = dataIO($rows['areatitle'],'out');
			$regtime = YmdHis($rows['regtime']);
			
			if ($birthday == '0000-00-00')$birthday = '';
			$identitynum = (empty($identitynum))?'':'['.$identitynum.']';
			
			$content.= "<tr><td>".$rows['id']."</td><td>".$uname."</td><td>".$nickname."</td><td>".$sex."</td><td>".$birthday."</td><td>".$heigh."</td><td>".$weigh."</td><td>".$edu."</td><td>".$love."</td><td>".$pay."</td><td>".$mob."</td><td>".$qq."</td><td>".$weixin."</td><td>".$email."</td><td>".$areatitle."</td><td>".$regtime."</td><td>".$truename."</td><td>".$identitynum."</td><td>".$bz."</td></tr>";
		}
		$content.= '</table>';
		$filaname = date("YmdHis");
		header("Content-type:application/vnd.ms-excel;charset=utf-8");
		header("Content-Disposition:filename=".$filaname.".xls");
		echo $content;
		AddLog('进行用户【数据导出】');
	} else {
		callmsg("暂无信息","-1");
	}
	exit;
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
<body>
<div class="navbox">
    <a class='ed'>用户导出Excel</a>
<div class="clear"></div></div>




<table class="table W700 Mtop150">
<form action="<?php echo SELF; ?>" method="post">
<tr>
<td height="20" colspan="2" align="left" bgcolor="#FFFFFF" class="tbodyT S16">用户资料导出Excel：</td>
</tr>
<tr>
<td width="150" class="S14">用户注册时间区间</td>
<td class="tdR">

<input name="sDATE1" id="sDATE1" type="text"  class="input size2" value="<?php echo YmdHis(ADDTIME,'Ymd'); ?>" size="10" maxlength="10" autocomplete="off">
 <b>～</b> 
<input name="sDATE2" id="sDATE2" type="text"  class="input size2" value="<?php echo YmdHis(ADDTIME,'Ymd'); ?>" size="10" maxlength="10" autocomplete="off">

</td>
</tr>
<tr>
  <td class="tdL">&nbsp;</td>
  <td class="tdR"><input class="btn size3" type="submit" value="开始导出" />
    <input name="submitok" type="hidden" value="excelupdate" /></td>
</tr>
</form>
</table>
<script src="laydate/laydate.js"></script><script>lay('#version').html('-v'+ laydate.v);
laydate.render({elem: '#sDATE1'});
laydate.render({elem: '#sDATE2'});
</script>


<?php require_once 'bottomadm.php';?>
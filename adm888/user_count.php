<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../rex/www_esyyw_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<div class="navbox"><a href="javascript:;" class="ed">会员统计</a><div class="clear"></div></div>
<table width="700" align="center" cellpadding="15" cellspacing="1" class="Mtop150" bgcolor="#dfdfdf">
<form action="<?php echo $SELF; ?>" method="post" onSubmit="return chkform()">
<tr>
<td height="20" colspan="7" align="left" bgcolor="#efefef" class="S16">会员统计：</td>
</tr>
<tr>
<td width="76" height="20" align="center" bgcolor="#FFFFFF">&nbsp;</td>
<td width="67" height="20" align="center" bgcolor="#FFFFFF">全部</td>
<td width="63" align="center" bgcolor="#FFFFFF" class="C999"><img src="images/grade/110.gif"><br><?php echo $_ZEAI['Grade10Name']; ?></td>
<td width="65" align="center" bgcolor="#FFFFFF" class="C999"><img src="images/grade/14.gif"><img src="images/grade/24.gif"><br><?php echo $_ZEAI['Grade4Name']; ?></td>
<td width="73" align="center" bgcolor="#FFFFFF" class="C999"><img src="images/grade/13.gif"><img src="images/grade/23.gif"><br><?php echo $_ZEAI['Grade3Name']; ?></td>
<td width="71" align="center" bgcolor="#FFFFFF" class="C999"><img src="images/grade/12.gif"><img src="images/grade/22.gif"><br><?php echo $_ZEAI['Grade2Name']; ?></td>
<td width="65" align="center" bgcolor="#FFFFFF" class="C999"><img src="images/grade/11.gif"><img src="images/grade/21.gif"><br><?php echo $_ZEAI['Grade1Name']; ?></td>
</tr>

<tr>
<td align="center" bgcolor="#F9F9F9">总会员</td>
<td align="center" bgcolor="#F9F9F9">

<?php echo $db->COUNT(__TBL_USER__);?></td>
<td align="center" bgcolor="#F9F9F9"><?php echo $db->COUNT(__TBL_USER__,"grade=10");?></td>
<td align="center" bgcolor="#F9F9F9"><?php echo $db->COUNT(__TBL_USER__,"grade=4");?></td>
<td align="center" bgcolor="#F9F9F9"> <?php echo $db->COUNT(__TBL_USER__,"grade=3");?></td>
<td align="center" bgcolor="#F9F9F9"><?php echo $db->COUNT(__TBL_USER__,"grade=2");?></td>
<td align="center" bgcolor="#F9F9F9"> <?php echo $db->COUNT(__TBL_USER__,"grade=1");?></td>
</tr>
<tr>
<td align="center" bgcolor="#FFFFFF">男</td>
<td align="center" bgcolor="#FFFFFF"><?php echo $db->COUNT(__TBL_USER__,"sex=1");?></td>
<td align="center" bgcolor="#FFFFFF">&nbsp;</td>
<td align="center" bgcolor="#FFFFFF"><?php echo $db->COUNT(__TBL_USER__,"grade=4 AND sex=1");?></td>
<td align="center" bgcolor="#FFFFFF"><?php echo $db->COUNT(__TBL_USER__,"grade=3 AND sex=1");?></td>
<td align="center" bgcolor="#FFFFFF"><?php echo $db->COUNT(__TBL_USER__,"grade=2 AND sex=1");?></td>
<td align="center" bgcolor="#FFFFFF"><?php echo $db->COUNT(__TBL_USER__,"grade=1 AND sex=1");?></td>
</tr>
<tr>
<td align="center" bgcolor="#F9F9F9">女</td>
<td align="center" bgcolor="#F9F9F9"><?php echo $db->COUNT(__TBL_USER__,"sex=2");?></td>
<td align="center" bgcolor="#F9F9F9">&nbsp;</td>
<td align="center" bgcolor="#F9F9F9"><?php echo $db->COUNT(__TBL_USER__,"grade=4 AND sex=2");?></td>
<td align="center" bgcolor="#F9F9F9"><?php echo $db->COUNT(__TBL_USER__,"grade=3 AND sex=2");?></td>
<td align="center" bgcolor="#F9F9F9"><?php echo $db->COUNT(__TBL_USER__,"grade=2 AND sex=2");?></td>
<td align="center" bgcolor="#F9F9F9"><?php echo $db->COUNT(__TBL_USER__,"grade=1 AND sex=2");?></td>
</tr>
<tr>
<td colspan="7" align="center" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr>
<td align="center" bgcolor="#FFFFFF">已关注公众号</td>
<td colspan="6" align="left" bgcolor="#FFFFFF"><?php echo $db->COUNT(__TBL_USER__,"subscribe=1");?></td>
</tr>
<tr>
<td align="center" bgcolor="#FFFFFF">取消关注</td>
<td colspan="6" align="left" bgcolor="#FFFFFF"><?php echo $db->COUNT(__TBL_USER__,"subscribe=2");?></td>
</tr>
<tr>
<td align="center" bgcolor="#FFFFFF">已锁定会员</td>
<td colspan="6" align="left" bgcolor="#FFFFFF"><?php echo $db->COUNT(__TBL_USER__,"flag=-1");?></td>
</tr>
</form>
</table>
<?php require_once 'bottomadm.php';?>
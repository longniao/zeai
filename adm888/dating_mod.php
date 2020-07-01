<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if ( !preg_match("/^[0-9]{1,8}$/",$fid) || empty($fid))callmsg("参数不正确！","-1");
if ($submitok == 'modupdate'){
	$flag=intval($flag);
	if (!preg_match("/^[0-9]{1,8}$/",$fid) || $fid == 0 )callmsg("Forbidden1!","-1");
	if (!preg_match("/^[0-9]{1}$/",$datingkind))callmsg("请选择正确格式的约会内容","-1");
	if (str_len($title)>100 || str_len($title)<1)callmsg("约会主题过多或过少，请控制在1~100字节以内","-1",500);
	if (!preg_match("/^[0-9]{1}$/",$price))callmsg("请选择正确格式的费用预算","-1");
	if ($hour8 <0 || $hour8 >24 || $minute8<0 || $minute8>59)callmsg("$jzbmtime2请输入正确格式的约会时间“时”和“分”如：18:30","-1",550);
	$yhtime1 = $year8.'-'.$month8.'-'.$day8;
	$yhtime2 = ' '.$hour8.':'.$minute8.':00'; 
	if (!ifdate($yhtime1))callmsg("请输入正确的格式的约会时间$yhtime1","-1");
	$yhtime = $yhtime1.$yhtime2;
	$yhtime = strtotime($yhtime);
	//$addtime  = strtotime(date("Y-m-d H:i:s"));
	//if ($addtime >= $yhtime)callmsg("无效的日期，请检查是否过期。","-1");
	if (!preg_match("/^[0-9]{1}$/",$maidian))callmsg("请选择正确格式的谁来买单","-1");
	if (str_len($contact)>100 || str_len($contact)<1)callmsg("联系方式过多或过少，请控制在1~100字节以内","-1");
	if (str_len($content)>1000 || str_len($content)<10)callmsg("约会安排过多或过少，请控制在10~1000字节以内","-1");
	$birthday1 = intval($birthday1);
	$birthday2 = intval($birthday2);
	$db->query("UPDATE ".__TBL_DATING__." SET datingkind='$datingkind',title='$title',price='$price',yhtime='$yhtime',maidian='$maidian',contact='$contact',content='$content',flag=$flag WHERE id=".$fid);
	//header("Location: dating_mod.php?fid=".$fid);
	AddLog('【约会审核】修改约会内容 ->id:'.$fid);
	alert_adm("修改成功！","dating_mod.php?fid=".$fid);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<script language="javascript">
function chkform(){
	if(document.www_yzlove_com.title.value=="")	{
	alert('请输入约会主题！');
	document.www_yzlove_com.title.focus();
	return false;
	}
	var year8 = document.www_yzlove_com.year8.value;
	var month8 = document.www_yzlove_com.month8.value;
	var day8 = document.www_yzlove_com.day8.value;
	var hour8 = document.www_yzlove_com.hour8.value;
	var minute8 = document.www_yzlove_com.minute8.value;
	if(year8 == "")	{
	alert('请输入正确格式约会日期！');
	document.www_yzlove_com.year8.focus();
	return false;
	}
	if(month8 == ""){
	alert('请输入正确格式约会日期！');
	document.www_yzlove_com.month8.focus();
	return false;
	}
	if(day8 == "" )	{
	alert('请输入正确格式约会日期！');
	document.www_yzlove_com.day8.focus();
	return false;
	}
	if(hour8 == "")	{
	alert('请输入正确格式约会日期！');
	document.www_yzlove_com.hour8.focus();
	return false;
	}
	if(minute8 == "" )	{
	alert('请输入正确格式约会日期！');
	document.www_yzlove_com.minute8.focus();
	return false;
	}
	if(document.www_yzlove_com.contact.value=="")	{
	alert('请输入联系方式:！');
	document.www_yzlove_com.contact.focus();
	return false;
	}
	if(document.www_yzlove_com.content.value.length<1 || document.www_yzlove_com.content.value.length>1000){
	alert('约会安排请控制在10~1000字节！');
	document.www_yzlove_com.content.focus();
	return false;
	}
}
</script>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<?php
if ( !preg_match("/^[0-9]{1,8}$/",$fid) || $fid == 0 ){
	callmsg("Forbidden!","-1");
} else {
	$rt = $db->query("SELECT * FROM ".__TBL_DATING__." WHERE id=".$fid);
	if($db->num_rows($rt)){
		$row = $db->fetch_array($rt);
		$year8  = YmdHis($row['yhtime'],'Y');
		$month8 = YmdHis($row['yhtime'],'m');
		$day8   = YmdHis($row['yhtime'],'d');
		$hour8  = YmdHis($row['yhtime'],'H');
		$minute8= YmdHis($row['yhtime'],'i');
	} else {
		callmsg("该信息不存在或已被删除！","-1");
	}
	$flag = $row['flag'];
}

?>
<br>
<style>
.table td{font-size:14px}

</style>
<table class="W500 table" style="color:#666;" >
  <form action="" method="post" name=www_yzlove_com onSubmit="return chkform()" >
  <tr>
    <td width="110" align="right">状　　态</td>
    <td align="left">

      
<input type="radio" name="flag" id="flag0" class="radioskin" value="0"  <?php if($flag==0){echo' checked';}?>><label for="flag0" class="radioskin-label"><i class="i1"></i><b class="W50 S14" style="color:#999">未审</b></label>
<input type="radio" name="flag" id="flag1" class="radioskin" value="1"  <?php if($flag==1){echo' checked';}?>><label for="flag1" class="radioskin-label"><i class="i1"></i><b class="W50 S14">正常</b></label>
<input type="radio" name="flag" id="flag2" class="radioskin" value="2"  <?php if($flag==2){echo' checked';}?>><label for="flag2" class="radioskin-label"><i class="i1"></i><b class="W50 S14" style="color:#090">结束</b></label>

      
      </td>
  </tr>
  <tr>
    <td width="110" align="right">约会内容</td>
    <td align="left"><select name="datingkind" id="datingkind" class="size2 S14">
        <option value="0" <?php if ($row['datingkind'] == 0)echo 'selected'; ?>>不限</option>
        <option value="1" <?php if ($row['datingkind'] == 1)echo 'selected'; ?>>喝茶小叙</option>
        <option value="2" <?php if ($row['datingkind'] == 2)echo 'selected'; ?>>共进晚餐</option>
        <option value="3" <?php if ($row['datingkind'] == 3)echo 'selected'; ?>>相约出游</option>
        <option value="4" <?php if ($row['datingkind'] == 4)echo 'selected'; ?>>看电影</option>
        <option value="5" <?php if ($row['datingkind'] == 5)echo 'selected'; ?>>欢唱K歌</option>
        <option value="6" <?php if ($row['datingkind'] == 6)echo 'selected'; ?>>其他</option>
      </select></td>
  </tr>
  <tr>
    <td width="110" align="right">约会主题</td>
    <td align="left"><input name="title" class="input size2" id="title" style="WIDTH: 370px;" value="<?php echo htmlout(stripslashes($row['title']));?>" /></td>
  </tr>
  <tr>
    <td width="110" align="right">费用预算</td>
    <td align="left"><span class="col">
      <select name="price" id="price" class="size2 S14">
        <option value="0" <?php if ($row['price'] == 0)echo 'selected'; ?>>不限</option>
        <option value="1" <?php if ($row['price'] == 1)echo 'selected'; ?>>100元以下</option>
        <option value="2" <?php if ($row['price'] == 2)echo 'selected'; ?>>100--300元</option>
        <option value="3" <?php if ($row['price'] == 3)echo 'selected'; ?>>300--500元</option>
        <option value="4" <?php if ($row['price'] == 4)echo 'selected'; ?>>500元以上</option>
      </select>
    </span></td>
  </tr>
  <tr>
    <td width="110" align="right"><span class="dt2">约会时间</td>
    <td align="left"><font color="#666666">
      <input name="year8" type="text" class="input size2" id="year8" style="width:60px;" onKeyPress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?php echo $year8; ?>" size="4" maxlength="4" />
年
<input name="month8" type="text" class="input size2" id="month8" style="width:40px;" onKeyPress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?php echo $month8; ?>" size="2" maxlength="2" />
月
<input name="day8" type="text" class="input size2" id="day8" style="width:40px;" onKeyPress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?php echo $day8; ?>" size="2" maxlength="2" />
日　
<input name="hour8" type="text" class="input size2" id="hour8" onKeyPress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?php echo $hour8; ?>" size="2" maxlength="2" style="width:40px;" />
时
<input name="minute8" type="text" class="input size2" id="minute8" onKeyPress="if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?php echo $minute8; ?>" size="2" maxlength="2" style="width:40px;" />
分 </font></td>
  </tr>
  <tr>
    <td width="110" align="right">谁来买单</td>
    <td align="left"><SELECT name=maidian id="maidian" class="size2 S14"> 
      <OPTION value=0 <?php if ($row['maidian'] == 0)echo 'selected'; ?>>不限</OPTION>
	<OPTION value=1 <?php if ($row['maidian'] == 1)echo 'selected'; ?>>我买单</OPTION>
	<OPTION value=2 <?php if ($row['maidian'] == 2)echo 'selected'; ?>>应约人买单</OPTION>
	<OPTION value=3 <?php if ($row['maidian'] == 3)echo 'selected'; ?>>AA制</OPTION>
    </SELECT></td>
  </tr>
  <tr>
    <td width="110" align="right">联系方式</td>
    <td align="left"><input name="contact" class="input size2" id="contact"  style="WIDTH: 370px;margin-bottom:5px;" value="<?php echo htmlout(stripslashes($row['contact']));?>"  /></td>
  </tr>
  <tr>
    <td width="110" align="right" valign="top">约会内容</td>
    <td align="left"><textarea name="content" id="content" class="textarea" style="WIDTH: 370px; HEIGHT: 96px"><?php echo stripslashes($row['content']);?></textarea></td>
  </tr>
  <tr>
    <td align="right"><input name="submitok" type="hidden" value="modupdate" />
      <input name="fid" type="hidden" value="<?php echo $fid; ?>" /></td>
    <td height="50" align="left" valign="top"><input type="submit" class="btn size3" value=" 修 改 "></td>
    </tr>
</form>
</table><br><br><br><br><br>
</body>
</html>
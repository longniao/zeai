<?php
require_once '../sub/init.php';
require_once "chkUadm.php";
if ( !preg_match("/^[0-9]{1,8}$/",$classid) || empty($classid))callmsg("参数不正确！","-1");
if ($submitok=="modupdate") {
	if ( str_len($title)<6 || str_len($title)>100 )callmsg("活动名称请控制在6~100个字节以内","-1");
	if ( !preg_match("/^[0-9]{1}$/",$flag))callmsg("“flag”必须是1位数字","-1");
	if ( str_len($kind)<2 || str_len($kind)>100 )callmsg("活动类型请控制在2~100个字节以内","-1");
	if ( str_len($hdtime)<2 || str_len($hdtime)>100 )callmsg("活动时间请控制在2~100个字节以内","-1");
	if ( str_len($address8)<2 || str_len($address8)>100 )callmsg("活动地点请控制在2~100个字节以内","-1");
	if ( str_len($jtlx)<2 || str_len($jtlx)>100 )callmsg("交通路线请控制在2~100个字节以内","-1");
	if ( !preg_match("/^[0-9]{1,5}$/",$num_n))callmsg("“男士”人数限定必须是5位数字以内","-1");
	if ( !preg_match("/^[0-9]{1,5}$/",$num_r))callmsg("“女士”人数限定必须是5位数字以内","-1");
	if ( !preg_match("/^[0-9]{1,5}$/",$rmb_n))callmsg("“男士”活动费用必须是5位数字以内","-1");
	if ( !preg_match("/^[0-9]{1,5}$/",$rmb_r))callmsg("“女士”活动费用必须是5位数字以内","-1");
	if ( str_len($tbsm)>500 )callmsg("特别说明请控制在500字节以内","-1");
	if ( str_len($content)<10 || str_len($content)>60000 )callmsg("活动详细说明请控制在10~50000字节以内","-1");
	$content = dataIO($content,'in');
	$db->query("UPDATE ".__TBL_GROUP_CLUB__." SET title='$title',kind='$kind',hdtime='$hdtime',address='$address8',jtlx='$jtlx',num_n='$num_n',num_r='$num_r',rmb_n='$rmb_n',rmb_r='$rmb_r',tbsm='$tbsm',content='$content',flag='$flag',jzbmtime='$jzbmtime' WHERE id='$classid'");
	header("Location: group_club_mod.php?classid=".$classid);
}
?>
<?php
$rt = $db->query("SELECT title,kind,hdtime,address,jtlx,num_n,num_r,rmb_n,rmb_r,tbsm,content,flag,jzbmtime,maintitle FROM ".__TBL_GROUP_CLUB__." WHERE id='$classid'");
if($db->num_rows($rt)){
	$row = $db->fetch_array($rt);
	$title  = strip_tags(stripslashes($row[0]));
	$kind  = $row[1];
	$hdtime  = $row[2];
	$address  = $row[3];
	$jtlx  = $row[4];
	$num_n  = $row[5];
	$num_r  = $row[6];
	$rmb_n  = $row[7];
	$rmb_r  = $row[8];
	$tbsm  = $row[9];
	$content  = dataIO($row[10],'out');
	$flag  = $row[11];
	$jzbmtime = $row[12];
	$maintitle  = $row[13];
} else {
	callmsg("该活动不存在或已被删除！","-1");
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>

<!-- editor -->
<link rel="stylesheet" href="editor/themes/default/default.css" />
<script charset="utf-8" src="editor/kindeditor.js"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js"></script>
<script>
var editor;
KindEditor.ready(function(K){
  editor=K.create('textarea[name="content"]',{
	resizeType : 1,
	minWidth : 400,
	allowPreviewEmoticons : true,
	allowImageUpload : true,
	afterBlur:function(){this.sync();},
	items :[
		'forecolor', 'bold','removeformat','image', '|','plainpaste','wordpaste', 'unlink', '|','clearhtml', '|','preview','fullscreen']
  });
});
</script>
<!--editor end -->

</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<table class="table700 Mtop30" style="margin-left:auto;margin-right:auto">
        <form action="group_club_mod.php" method="post" name="MYFORM" >
          <tr >
            <td width="111" align="right"><font color="6699CC">所属圈子：</font></td>
            <td width="619" align="left" valign="top"><a href="<?php echo Href('group',$mainid); ?>" target="_blank" class="u666666"><?php echo $maintitle; ?></a></td>
          </tr>
          <tr>
            <td align="right"><font color="6699CC">活动名称：</font></td>
            <td align="left" valign="top"><input name="title" type="text" class=input value="<?php echo stripslashes($title);?>" size="60" maxlength="100"></td>
          </tr>
          <tr>
            <td align="right"><font color="6699CC">活动状态：</font></td>
            <td align="left" valign="top"><select name="flag">
              <option value="0" <?php if ($flag == 0)echo "selected" ?> style="color:red;">未审核</option>
              <option value="1" <?php if ($flag == 1)echo "selected" ?> style="color:#0066CC;">(正在报名) 正在报名中</option>
              <option value="2" <?php if ($flag == 2)echo "selected" ?> style="color:#FF6600;">(正在进行) 活动进行中</option>
              <option value="3" <?php if ($flag == 3)echo "selected" ?> style="color:#349933;">(已经结束) 圆满结束</option>
            </select></td>
          </tr>
          <tr>
            <td align="right"><font color="6699CC">活动类型：</font></td>
            <td align="left" valign="top"><input name="kind" type="text" class=input value="<?php echo stripslashes($kind);?>" size="20" maxlength="100">
                <font color="6699CC">交友，征婚，旅游等等</font></td>
          </tr>
          <tr>
            <td align="right" ><font color="6699CC">活动时间：</font></td>
            <td align="left" valign="top" ><input name="hdtime" type="text" class=input id="hdtime" value="<?php echo stripslashes($hdtime);?>" size="60" maxlength="100">
              <font color="6699CC">就是活动当天具体时间　　              </font></td>
          </tr>
          <tr>
            <td align="right" bgcolor="ffffcc" ><font color="#FF0000"><b>截止报名时间</b>：</font></td>
            <td align="left" valign="top" bgcolor="ffffcc">
			
			
			
<input name="jzbmtime" type="text" class=input id="jzbmtime" value="<?php echo stripslashes($jzbmtime);?>" size="30" maxlength="100" style="font-size:10.3pt;height:24px">
<?php 
	$d1  = $ADDTIME;
	$d2  = strtotime($jzbmtime);
	$totals  = ($d2-$d1);
	$day     = intval( $totals/86400 );
	$hour    = intval(($totals % 86400)/3600);
	$hourmod = ($totals % 86400)/3600 - $hour;
	$minute  = intval($hourmod*60);
	$totals = ($flag >1)?-1:1;
	if (($totals) > 0) {
		$outtime = ($day > 0)?"报名还有<font color=red>$day</font>天":"离结束还有";
		$outtime .= "<font color=red>$hour</font>小时<font color=red>$minute</font>分钟";
	} else {
		$outtime = '<font color=red>无效日期，请检查！</font>';
	}
	echo $outtime;
?>			</td>
          </tr>
          <tr>
            <td align="right" ><font color="6699CC">活动地点：</font></td>
            <td align="left" valign="top"><input name="address8" type="text" class=input id="address8" value="<?php echo stripslashes($address);?>" size="60" maxlength="100"></td>
          </tr>
          <tr>
            <td align="right"><font color="6699CC">交通路线：</font></td>
            <td align="left" valign="top"><input name="jtlx" type="text" class=input id="jtlx" value="<?php echo stripslashes($jtlx);?>" size="60" maxlength="100"></td>
          </tr>
          <tr>
            <td align="right"><font color="6699CC">人数限定：</font></td>
            <td align="left" valign="top"><font color="#666666">男
                  <input name="num_n" type="text" class=input onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $num_n;?>" size="3" maxlength="5">
              人 ，女
              <input name="num_r" type="text" class=input onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $num_r;?>" size="3" maxlength="5">
              人</font></td>
          </tr>
          <tr>
            <td align="right"><font color="6699CC">活动费用：</font></td>
            <td align="left" valign="top"><font color="#666666">男
                  <input name="rmb_n" type="text" class=input value="<?php echo $rmb_n;?>" size="3" maxlength="5" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
              元 ，女
              <input name="rmb_r" type="text" class=input value="<?php echo $rmb_r;?>" size="3" maxlength="5" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
              元</font></td>
          </tr>
          <tr>
            <td align="right" valign="top"><font color="6699CC">特别说明：</font></td>
            <td align="left" valign="top"><textarea name="tbsm" cols="58" rows="4" id="tbsm" style="font-size:9pt;color:#333333;"><?php echo stripslashes($tbsm);?></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top"><font color="6699CC">活动详细说明</font></td>
            <td align="right" valign="top"><a href="<?php echo Href('group_party',$classid); ?>" target="_blank" class=u000000 style='font-size:10.3pt;'><b><font color="#FF0000">活动预览</font></b></a></td>
          </tr>
          <tr>
            <td colspan="2" align="center"><textarea name="content" id="content" class="textarea_k" style="width:100%;height:400px" ><?php echo $content; ?></textarea></td>
          </tr>
          <tr>
            <td height="100" colspan="2" align="center" valign="top"><br>
              <input name="submitok" type="hidden" value="modupdate">
<input name="classid" type="hidden" value="<?php echo $classid;?>">
<input type="submit" name="Submit" value=" 保存 " class="btn size3"></td>
          </tr>
        </form>
</table>
<?php
require_once "bottomadm.php";
?>

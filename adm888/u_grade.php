<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
header("Cache-control: private");
require_once 'chkUadm.php';

if($session_kind == 'crm'){
	if(!in_array('crm_user_grade',$QXARR))exit(noauth());
}

require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/udata.php';
if(!ifint($uid))callmsg("forbidden","-1");
//
$urole  = json_decode($_ZEAI['urole'],true);
$sj_if2ARR = json_decode($_VIP['sj_if2'],true);
if (!is_array($sj_if2ARR) || @count($sj_if2ARR)<=0){
	exit("<div class='nodatatipsS'>会员组时间没有设置，请前往 顶部主菜单【会员管理】然后左侧【会员组套餐】</div>");
}
//
$row = $db->NUM($uid,'uname,nickname,grade,sjtime,if2,openid,subscribe',"id=$uid");
if(!$row){
	textmsg("此会员不存在或已经锁定！");
}else{
	$uname = dataIO($row[0],'out');
	$nickname = dataIO($row[1],'out');
	$grade    = $row[2];
	$sjtime   = $row[3];
	$if2      = $row[4];
	$data_openid    = $row[5];
	$data_subscribe = $row[6];
	$uname = (empty($uname))?$nickname:$uname;
	$log_str1 = utitle($grade).'<font class="Cf00">'.get_if2_title($if2).'</font>'.YmdHis($sjtime);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<?php
if($submitok == "mod"){
	$fgrade = intval($fgrade);$fif2 = intval($fif2);
	if ( !ifint($fgrade,'0-9','1,2') )textmsg("forbidden");
	function is_time($time){
		$pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';
		return preg_match($pattern,$time);
	}
	function isDateTime($dateTime){
		$ret = strtotime($dateTime);
		return $ret !== FALSE && $ret != -1;
	}
	if (!is_time($fsjtime) || !isDateTime($fsjtime))textmsg("日期时间格式不对，请检查<br><br>例：".(date("Y")+1)."-01-15 06:48:29<br><br>",'back','返回重写');
	$fsjtime = strtotime($fsjtime);
	if ($fgrade == 1){
		$fif2 = 0;$fsjtime = 0;
	}elseif($fgrade == 10){
		$fif2 = 999;$fsjtime = 0;
	}
	$endbz ='<a href="javascript:;" class="aHONGed" onClick=zeai.iframe("更改【'.$uname.'】等级","u_grade.php?uid='.$uid.'",350,380)>'.utitle($fgrade).'</a>';
	$SQL   = " ,if2=".$fif2.",sjtime=".$fsjtime;
	$db->query("UPDATE ".__TBL_USER__." SET grade=".$fgrade.$SQL." WHERE id=".$uid);
	//日志
	$log_str2 = utitle($fgrade).'<font class="Cf00">'.get_if2_title($fif2).'</font>'.YmdHis($fsjtime);
	AddLog('修改会员级别【'.$nickname.'（uid:'.$uid.'）】'.$log_str1.'->'.$log_str2);

	$if2_title = '('.get_if2_title($fif2).')';
	//站内通知
	$C = $data_nickname.'您好，恭喜你'.utitle($fgrade).$if2_title.'升级成功!　　<a href='.Href('my').' class=aQING>查看详情</a>';
	$db->SendTip($uid,'恭喜你'.utitle($fgrade).$if2_title.'升级成功!',dataIO($C,'in'),'sys');
	//微信模版通知
	if (!empty($data_openid) && $data_subscribe==1){
		$keyword1 = '恭喜你'.utitle($fgrade).$if2_title.'升级成功!';
		$keyword3 = urlencode($_ZEAI['siteName']);
		$url      = urlencode(mHref('my'));
		@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$data_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
	}
	
	$returnid = 'grade'.$uid;
?>
<script>
window.parent.document.getElementById('<?php echo $returnid; ?>').innerHTML = '<?php echo $endbz; ?>';
window.parent.zeai.iframe(0);
</script>
<?php exit;}?>

<script src="../res/www_zeai_cn.js"></script>
<script>
function chkform(){
	if(  !zeai.ifint(o('fif2').value,'0-9','1,3') && (document.ZEAI_FORM.fgrade.value >1)   ){
		parent.zeai.msg('请选择有效期限');
		return false;
	}
}
</script>
</head>
<link href="css/main.css?2" rel="stylesheet" type="text/css">
<style type="text/css">
.gradelist{padding:5px 0 10px 15px;border-left:#eee 4px solid;color:#999}
.radioskin:checked + label.radioskin-label b{color:#000}
</style>
<body>
<form name="ZEAI_FORM" method="POST" action="<?php echo $SELF;?>" onsubmit="return chkform();">
<table align="center" cellpadding="5" cellspacing="5" class="Mtop20 W90_">
    <tr>
	<td width="70" align="right" class="S14">有效期限</td>
	<td align="left" class="tdR S14">
    	<select name="fif2" id="fif2" class="size2 W90_">
        <option value="0"></option>
        <?php
        foreach ($sj_if2ARR as $v) {
            if ($v == 12){
                $vtitle = '1年';
            }elseif($v == 999){
                $vtitle = '永久';
            }else{
                $vtitle = $v.'个月';
            }
            ?>
        <option value="<?php echo $v;?>"<?php echo ($if2 == $v)?' selected':''; ?>><?php echo $vtitle;?></option>
        <?php }?>
		</select>
      </td>
    </tr>
	<?php if ($sjtime > 0){ ?>
        <tr>
            <td width="70" align="right" class="S14">起始时间 </td>
            <td align="left" class="S14"><input type="text" class="input size2 W90_" name="fsjtime" id="fsjtime" value="<?php echo YmdHis($sjtime); ?>" /></td>
        </tr>
    <?php }else{?>
        <input type="hidden" name="fsjtime" value="<?php echo date('Y-m-d H:i:s'); ?>" />
    <?php }?>
    <tr>
        <td align="right" class="S14">会员等级</td>
        <td align="left">
        <?php foreach($urole as $RV){?>
            <div class="gradelist">
              <input type="radio" name="fgrade" id="fgrade<?php echo $RV['g'];?>" class="radioskin" value="<?php echo $RV['g'];?>"<?php echo ($RV['g'] == $grade)?' checked':'';?>>
              <label for="fgrade<?php echo $RV['g'];?>" class="radioskin-label"><i class="i1"></i><b class="W120 S14"><?php echo $RV['t'].uicon_grade_all($RV['g']);?></b></label>
            </div>
        <?php }?>
        </td>
    </tr>
    <tr>
        <td align="center"><input type="hidden" name="submitok" value="mod" /><input type="hidden" name="uid" value="<?php echo $uid;?>" /></td>
        <td align="left"><input class="btn size3" type="submit" value=" 确定 " /></td>
    </tr>
</table>
</form>
<?php require_once 'bottomadm.php';?>
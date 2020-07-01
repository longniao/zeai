<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('robot',$QXARR))exit(noauth());
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/udata.php';
require_once ZEAI.'cache/config_vip.php';
if($submitok=='add_update'){
	$nicknamelist=trimm($nicknamelist);
	if($makekind==1){
		$unum=intval($unum);
		$unum=($unum>50)?50:$unum;
		if($unum<=0)json_exit(array('flag'=>0,'msg'=>'请输入每个城市会员数'));
		$rt2=$db->query("SELECT A1.title AS title1,A2.id,A2.title,A2.fid FROM ".__TBL_AREA1__." A1,".__TBL_AREA2__." A2 WHERE A2.fid=A1.id ORDER BY A2.px DESC");
		$total2 = $db->num_rows($rt2);
		if ($total2 > 0) {
			for($i2=1;$i2<=$total2;$i2++) {
				$rows2 = $db->fetch_array($rt2,'name');
				if(!$rows2) break;
				$id1    = $rows2['fid'];
				$title1 = $rows2['title1'];
				$id2    = $rows2['id'];
				$title2 = $rows2['title'];
				for($i3=1;$i3<=$unum;$i3++){rand_AREA3($id1,$title1,$id2,$title2,$nicknamelist);}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'生成成功！','total'=>$total2*$unum));
	}elseif($makekind==2){
		$ARR = explode(',',trimm($dirlist));
		$unum = count($ARR);
		if ($unum >= 1 && is_array($ARR) &&  !empty($dirlist) ){
			foreach ($ARR as $V) {
				$uname  = 'jqr'.cdnumletters(8);
				$pwd    = 'w　w　w － Z-e A-i －c　n # ６。1';
				if(!empty($nicknamelist)){
					$nicknameARR=explode(',',trimm($nicknamelist));$key = array_rand($nicknameARR);$nickname=$nicknameARR[$key];
				}else{
					$nickname=cdnumletters(8);
				}
				$Y1=date("Y")-50;$Y2=date("Y")-18;$birthday = rand_date($Y1.'-01-01',$Y2.'-12-12');
				$sex    = rand_data('sex');
				$heigh  = rand_betweenFN($sex,'heigh');
				$weigh  = rand_betweenFN($sex,'weigh');
				$edu    = rand_data('edu');
				$love   = rand_data('love');
				$pay    = rand_data('pay');
				$house  = rand_data('house');
				$car    = rand_data('car');
				$job    = rand_data('job');
				$reg_grade = rand_grade();
				$reg_if2   = rand_if2();
				$reg_loveb = 0;
				$ip      = '';
				$tguid   = 0;
				$regkind = 9;
				//
				$row = $db->ROW(__TBL_AREA1__,"id,title","1=1 ORDER BY rand()");
				if ($row){
					$id1 = $row[0];$title1 = $row[1];
					if(ifint($id1)){
						$row2 = $db->ROW(__TBL_AREA2__,"id,title","1=1 ORDER BY rand()");
						$id2 = $row2[0];$title2 = $row2[1];
						if(ifint($id2)){
							$row3 = $db->ROW(__TBL_AREA3__,"id,title","1=1 ORDER BY rand()");
							$id3 = $row3[0];$title3 = $row3[1];
							if(ifint($id3)){
								$areaid    = $id1.','.$id2.','.$id3;
								$areatitle = $title1.' '.$title2.' '.$title3;
							}
						}
					}
				}
				//
				$db->query("INSERT INTO ".__TBL_USER__." (dataflag,house,car,kind,flag,uname,mob,RZ,pwd,nickname,sex,grade,if2,areaid,areatitle,birthday,edu,heigh,weigh,job,love,pay,loveb,regtime,endtime,refresh_time,regkind) VALUES (1,'".$house."','".$car."',4,1,'".$uname."','$mob','$RZ','".$pwd."','".$nickname."',$sex,$reg_grade,$reg_if2,'".$areaid."','".$areatitle."','".$birthday."',".$edu.",".$heigh.",".$weigh.",".$job.",".$love.",".$pay.",".$reg_loveb.",".ADDTIME.",".ADDTIME.",".ADDTIME.",$regkind)");
				$uid = intval($db->insert_id());
				//
				if(ifint($V)){
					$headimgurl = $_ZEAI['up2'].'/p/robot/'.$V.'/1.jpg';
					if (ifpic($headimgurl)){
						$dbname  = (!empty($V))?wx_get_uinfo_logo($headimgurl,$uid,'m'):'';
						$photo_s = setpath_s($dbname);
						$db->query("UPDATE ".__TBL_USER__." SET photo_s='$photo_s',photo_f=1 WHERE id=".$uid);
					}
					//
					for($i=2;$i<=6;$i++){
						$photourl = $_ZEAI['up2'].'/p/robot/'.$V.'/'.$i.'.jpg';
						if (@ifpic($photourl)){
							$dbname2 = wx_get_uinfo_logo($photourl,$uid,'photo');
							$path_s  = setpath_s($dbname2);
							$db->query("INSERT INTO ".__TBL_PHOTO__." (uid,path_s,flag,addtime) VALUES ($uid,'$path_s',1,".ADDTIME.")");
						}
					}
				}
			}
			json_exit(array('flag'=>1,'msg'=>'生成成功！','total'=>$unum));
		}else{
			json_exit(array('flag'=>0,'msg'=>'请输入照片文件夹名称数字序列'));
		}
	}
	json_exit(array('flag'=>0,'msg'=>'参数错误'));
}
function rand_AREA3($id1,$title1,$id2,$title2,$nicknamelist) {
	global $db;
	$row = $db->ROW(__TBL_AREA3__,"id,title","fid=".$id2." ORDER BY rand()");
	if ($row){
		$id3 = $row[0];$title3 = $row[1];
		$areaid    = $id1.','.$id2.','.$id3;
		$areatitle = $title1.' '.$title2.' '.$title3;
		$uname  = 'jqr'.cdnumletters(8);
		$pwd    = 'w w w # z-e-a-i #c_n# v6.1';
		if(!empty($nicknamelist)){
			$nicknameARR=explode(',',trimm($nicknamelist));$key = array_rand($nicknameARR);$nickname=$nicknameARR[$key];
		}else{
			$nickname=cdnumletters(8);
		}
		$Y1=date("Y")-50;$Y2=date("Y")-18;$birthday = rand_date($Y1.'-01-01',$Y2.'-12-12');
		$sex    = rand_data('sex');
		$heigh  = rand_betweenFN($sex,'heigh');
		$weigh  = rand_betweenFN($sex,'weigh');
		$edu    = rand_data('edu');
		$love   = rand_data('love');
		$pay    = rand_data('pay');
		$house  = rand_data('house');
		$car    = rand_data('car');
		$job    = rand_data('job');
		$reg_grade = rand_grade();
		$reg_if2   = rand_if2();
		$reg_loveb = 0;
		$ip      = '';
		$tguid   = 0;
		$regkind = 9;
		$db->query("INSERT INTO ".__TBL_USER__." (dataflag,house,car,kind,flag,uname,mob,RZ,pwd,nickname,sex,grade,if2,areaid,areatitle,birthday,edu,heigh,weigh,job,love,pay,loveb,regtime,endtime,refresh_time,regkind) VALUES (1,'".$house."','".$car."',4,1,'".$uname."','$mob','$RZ','".$pwd."','".$nickname."',$sex,$reg_grade,$reg_if2,'".$areaid."','".$areatitle."','".$birthday."',".$edu.",".$heigh.",".$weigh.",".$job.",".$love.",".$pay.",".$reg_loveb.",".ADDTIME.",".ADDTIME.",".ADDTIME.",$regkind)");
	}
}
function rand_if2(){
	global $_VIP;
	$ARR = json_decode($_VIP['sj_if2'],true);
	$key = array_rand($ARR);
	return $ARR[$key];
}
function rand_grade() {
	global $_ZEAI;
	$ARR = json_decode($_ZEAI['urole'],true);
	$key = array_rand($ARR);
	return intval($ARR[$key]['g']);
}

function rand_date($begintime, $endtime="") {  
    $begin = strtotime($begintime);  
    $end = $endtime == "" ? mktime() : strtotime($endtime);  
    $timestamp = rand($begin, $end);  
    return date("Y-m-d", $timestamp);  
}
function rand_data($json) {
	global $_UDATA;
	$ARR = json_decode($_UDATA[$json],true);
	$key = array_rand($ARR);
	return intval($ARR[$key]['i']);
}
function rand_betweenFN($sex,$kind) {
	if($sex==2){
		if($kind=='heigh'){
			$i1 = 151;
			$i2 = 176;
		}elseif($kind=='weigh'){
			$i1 = 40;
			$i2 = 70;
		}
	}else{
		if($kind=='heigh'){
			$i1 = 164;
			$i2 = 190;
		}elseif($kind=='weigh'){
			$i1 = 50;
			$i2 = 100;
		}
	}
	$between = array();
	for($i=$i1;$i<=$i2;$i++) {
		$between[]=$i;
	}
	$key = array_rand($between);
	return $between[$key];
}
$makekind=(empty($makekind))?1:$makekind;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<style>
i.add{display:inline-block;color:#fff;border-radius:50px;width:16px;height:16px;line-height:16px;text-align:center;font-size:12px;margin-right:2px}
::-webkit-input-placeholder{color:#bbb;font-size:14px}

</style>
<body>
<div class="navbox">
    <a href="robot.php" class="ed">机器人管理</a>
    <a>当前机器人会员总数：<?php echo '<b>'.$db->COUNT(__TBL_USER__,"kind=4").'</b>';?></a>
    <div class="Rsobox">
    
    </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>
<!---->
<?php
/************************************** 【发布】 **************************************/
if ($submitok == 'add'){?>


    <table class="table W1200 Mtop20" style="float:left;margin:15px 0 100px 20px">
    <form id="Www_zeai_cn_form">
    <tr>
      <td colspan="2" align="left" class="tdR">批量生成机器人</td></tr>
    <tr>
      <td class="tdL">生成方式</td><td class="tdR">
      
    <input type="radio" name="makekind" value="1" id="makekind1" class="radioskin" <?php echo ($makekind == 1 || empty($makekind))?' checked':'';?> onChange="zeai.openurl('robot.php?submitok=add&makekind=1')"><label for="makekind1" class="radioskin-label"><i class="i1"></i><b class="W100">自动</b></label>　
    <input type="radio" name="makekind" value="2" id="makekind2" class="radioskin" <?php echo ($makekind == 2)?' checked':'';?> onChange="zeai.openurl('robot.php?submitok=add&makekind=2')"><label for="makekind2" class="radioskin-label"><i class="i1"></i><b class="W150">手动 (头像+相册)</b></label>　
      
      
      </td></tr>
    
    <?php if ($makekind == 1){?>
        <tr>
        <td class="tdL"><font class="Cf00">*</font>每个城市会员数</td><td class="tdR S16">
          <input name="unum" id="unum" type="text" class="input size2 W50" maxlength="2" value="1" style="margin-bottom:5px" />
          <span class="tips2"> 最多填50，超过50自动截取<br>
          每个城市会员生成会员数量，对应二级地区表<br>
          由于城市太多，数量不能太大，<b>这里填50，就会生成18100个会员左右</b></span>
        </td>
        </tr>
        <tr>    
          <td class="tdL">最终生成会员数</td>
          <td class="tdR"><span id="numstr" class="Cf00"><?php echo 1*362;?></span> 个会员</td></tr>
        <tr>
		<script>
        unum.oninput = function (){
            var value = parseInt(this.value)*362;
            numstr.html(value);
        }
        </script>
	<?php }?>

	<?php if ($makekind == 2){?>

    
    <tr>
    <td class="tdL"><font class="Cf00">*</font>照片文件夹名称<br>数字序列</td><td class="tdR C8d"><textarea name="dirlist" cols="30" rows="5" class="W100_" id="dirlist" placeholder="请输入数字序列，以英文半角逗号隔开，不要留空格。例如：【1,2,3,4,,,,,,100】，多少个序列就代表生成多少个会员"><?php echo dataIO($_INDEX['indexContent'],'out');?></textarea>
    
    <span class="tips2">
    1.这里面的数字序列一定要和服务器上【<?php echo $_ZEAI['up2'].'/p/robot/';?>】下面的照片文件夹名称一 一对应，包括个数等，比如robot/下面【1,2,3,4,5,6】<br>
    2.文件夹里请放置.jpg照片序列(1.jpg,2.jpg,3.jpg,,,,,6.jpg)，第一个1.jpg将自动设为主头像，其余2~6将自动上传到至个人相册<br>
    3.请在本机整理好照片文件夹，然后通过FTP或远程连接把文件夹上传至【<?php echo $_ZEAI['up2'].'/p/robot/';?>】下面，注：里面的文件不要嵌套，【1,2,3,4,5,6】这些文件夹放置在robbt/根下面<br>
    4.为了性能速度，暂时最多支持6张照片，1.jpg为头像，2.jpg~6.jpg为相册
    </span>
    </td>
    </tr>  
    

	<?php }?>
    <tr>
    <td class="tdL">会员昵称</td><td class="tdR C8d"><textarea name="nicknamelist" cols="30" rows="10" class="W100_ S14" id="nicknamelist" placeholder="请输入会员昵称序列，以英文半角逗号隔开，不要留空格。例如：【supdes,小风,风情风淡,晚风520,,,,,,爱多多】，越多，重复的机率越小，推荐昵称数量在会员数量的3倍以上"><?php echo dataIO($_INDEX['indexContent'],'out');?></textarea>
    <span class="tips2">如果不填，将以随机英文字符作为昵称，建议昵称数量一定要大于会员数量，越多越好，推荐昵称数量在会员数量的3倍以上
    </td>
    </tr>
    
    <tr>
      <td class="tdL"></td>
      <td class="tdR">
        <button class="btn size3 HUANG3" type="button" id="submit_add" />开始批量生成</button>
        <input name="submitok" type="hidden" value="add_update" />
        <input name="mate_areaid" id="mate_areaid" type="hidden" value="" />
        <input name="mate_areatitle" id="mate_areatitle" type="hidden" value="" />
      </td>
    </tr>
    
    
      
    </form>
    </table>
    <div class="clear"></div>
<script>
submit_add.onclick=function(){
	zeai.confirm('确定批量生成机器人会员么？<br>1.由于数量庞大，可能需要数分钟<br>2.请确认PHP环境超时时间足够长，一般至少要10分钟<br>3.发送过程中请不要关闭窗口，耐心等待',function(){
		zeai.msg('正在生成会员',{time:999});
		zeai.ajax({url:'robot'+zeai.extname,form:Www_zeai_cn_form},function(e){rs=zeai.jsoneval(e);
			zeai.msg(0);
			if(rs.flag==1){
				zeai.alert(rs.msg+'，生成会员总数：'+rs.total,'robot.php?submitok=add');
				//setTimeout(function(){location.reload(true);},1000);	
			}else{
				zeai.msg(rs.msg);
			}
		});
	});
}
</script>
<!--【发布 修改 结束】-->
<?php
/************************************** 【列表】 list **************************************/
}else{
	?>
    
<table class="table0 W98_" style="margin-left:20px">
    <tr>
    <td width="180" height="50" align="left" valign="bottom" class="border0" >
    <button type="button" class="btn size2" onClick="zeai.openurl('robot.php?submitok=add')"><i class="ico add">&#xe620;</i>批量生成机器人会员</button>
    </td>
    <td align="left" valign="bottom">
    <button type="button" class="btn size2" onClick="zeai.openurl('u_add.php?kind=4')"><i class="ico add">&#xe620;</i>人工增加机器人会员</button>
    </td>
    <td width="300" align="right">&nbsp;</td>
    </tr>
    </table>
    
<div class="clear"></div> 
    
<?php
//$_ZEAI['robot']='{"flag":{"hi":"1","chat":"1","view":"1"},"hinum":"1","chatnum":"2","viewnum":"3","hour":"4"}';
$robot=json_decode($_ZEAI['robot'],true);
?>
<form id="W_W_W_Z_E__A_I__C_N__FORM">
<table class="table size1 W1200" style="margin:20px 0 0 20px">
	<tr><th colspan="2" align="left">机器人设置</th></tr>
	<tr>
		<td class="tdL">打招呼</td>
		<td class="tdR">
        <table class="table0" style="margin:0">
          <tr>
            <td align="left"><input type="checkbox" name="robot_flag_hi" id="robot_flag_hi" class="switch" value="1"<?php echo ($robot['flag']['hi'] == 1)?' checked':'';?>><label for="robot_flag_hi" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　<span class="tips">开启后，机器人会对在线会员自动打招呼</span>
</td>
          </tr>
          <tr>
            <td align="left">单个会员收到【打招呼】总数量
              <input name="robot_hinum" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $robot['hinum'];?>"> 个
            　</td>
          </tr>
        </table></td>
	</tr>
	<tr>
		<td class="tdL">发私信</td>
		<td class="tdR">

			<table class="table0" style="margin:0">
          <tr>
            <td align="left"><input type="checkbox" name="robot_flag_chat" id="robot_flag_chat" class="switch" value="1"<?php echo ($robot['flag']['chat'] == 1)?' checked':'';?>><label for="robot_flag_chat" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　<span class="tips">开启后，机器人会对在线会员自动发私信</span>
</td>
          </tr>
          <tr>
            <td align="left">单个会员收到【私信】总数量
              <input name="robot_chatnum" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $robot['chatnum'];?>"> 封
            　</td>
          </tr>
        </table>

        </td>
	</tr>

	<tr>
		<td class="tdL">谁看过我</td>
		<td class="tdR">
        
        
        
        <table class="table0" style="margin:0">
        <tr>
        <td align="left"><input type="checkbox" name="robot_flag_view" id="robot_flag_view" class="switch" value="1"<?php echo ($robot['flag']['view'] == 1)?' checked':'';?>><label for="robot_flag_view" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>　<span class="tips">开启后，机器人自动浏览在线会员资料</span>
        </td>
        </tr>
        <tr>
        <td align="left">单个会员收到【谁看过我】总数量
        <input name="robot_viewnum" type="text" class="W50 FVerdana" maxlength="4" value="<?php echo $robot['viewnum'];?>"> 次
        　</td>
        </tr>
        </table>        
        
        
        
        </td>
	</tr>
    
	<tr>
		<td class="tdL">执行时效</td>
		<td class="tdR"><input name="robot_hour" type="text" class="W50 FVerdana" maxlength="8" value="<?php echo $robot['hour'];?>"> 小时　<span class="tips">机器人只对新注册会员多少小时内会员发送</span></td>
	</tr> 
    
	<tr>
		<td class="tdL">地区同步</td>
		<td class="tdR"><input type="checkbox" name="robot_areaupdate" id="areaupdate" class="switch" value="1"<?php echo ($robot['areaupdate'] == 1)?' checked':'';?>><label for="areaupdate" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
		  　<span class="tips">开启后，机器人自动调用被查看者的填写的城市(比如:A看机器人资料,A的城市是扬州,那么此机器人自动调用他的,机器人就显示扬州,自动修改为扬州)</span></td>
	</tr>    
	<tr>
		<td class="tdL">只发同城</td>
		<td class="tdR"><input type="checkbox" name="robot_areasame" id="areasame" class="switch" value="1"<?php echo ($robot['areasame'] == 1)?' checked':'';?>><label for="areasame" class="switch-label"><i></i><b>开启</b><b>关闭</b></label>
		  　<span class="tips">开启后，机器人只对同城新会员发送，比如真实会员是扬州，那只有扬州的机器人对Ta发送</span></td>
	</tr>     
	<tr>
		<td class="tdL">私信内容列表</td>
		<td class="tdR"><textarea name="robot_chatC" style="padding:10px" cols="30" rows="20" class="W100_ S14" id="robot_chatC" placeholder="请输入简短私信内容，一行一条，不能有空行，机器人随机调用，为了性能，请控制在100行以内"><?php echo str_replace("<br>","\r",dataIO($robot['chatC'],'out'));;?></textarea><span class="tips2">私信简短内容，一行一条，不能有空行，机器人随机调用，为了性能，请控制在100行以内</span></td>
	</tr> 
       
       
	<tr>    
		<td colspan="2" align="center">
		<input name="submitok" type="hidden" value="cache_robot">
		<input name="uu" type="hidden" value="<?php echo $session_uid;?>">
		<input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
		<button type="button" id="save" class="btn size3 HUANG3">确认并保存</button>
		<script>
			save.onclick = function(){
				zeai.msg('正在更新中',{time:30});
				zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:W_W_W_Z_E__A_I__C_N__FORM},function(e){var rs=zeai.jsoneval(e);
					zeai.msg(0);zeai.msg(rs.msg);
					if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}
				});
			}
		</script>
		</td>
	</tr>

</table>
</form>
<?php }?>




<br><br><br>
<?php require_once 'bottomadm.php';?>
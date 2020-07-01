<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('excel_out',$QXARR))exit(noauth());
if($submitok=="import"){
	require_once ZEAI.'sub/excel_reader.php';
	require_once ZEAI.'cache/config_up.php';
	require_once ZEAI.'sub/zeai_up_func.php';
	$file = $_FILES['execl'];
	$file_name = $file['tmp_name'];
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read(iconv("UTF-8","GB2312",$file_name));
	//$data->sheets[0]['numRows']为Excel行数
	//$data->sheets[0]['numCols']为Excel列数
	$echo = '';
	$total=$data->sheets[0]['numRows'];
	for($i=2;$i<=$total;$i++){
		$bh = trimm(dataIO($data->sheets[0]['cells'][$i][1],'in',20));
		$nickname = trimm(dataIO($data->sheets[0]['cells'][$i][2],'in',200));
		$photo_s = trimm(dataIO($data->sheets[0]['cells'][$i][3],'in',100));
		$birthday = trimm(dataIO($data->sheets[0]['cells'][$i][8],'in',20));
		$sex = trimm(dataIO($data->sheets[0]['cells'][$i][10],'in',20));
		$heigh = trimm(dataIO($data->sheets[0]['cells'][$i][11],'in',20));
		$weigh = trimm(dataIO($data->sheets[0]['cells'][$i][12],'in',20));
		$blood = trimm(dataIO($data->sheets[0]['cells'][$i][15],'in',20));
		$nation = trimm(dataIO($data->sheets[0]['cells'][$i][16],'in',20));
		$area2title = trimm(dataIO($data->sheets[0]['cells'][$i][17],'in',100));
		$love = trimm(dataIO($data->sheets[0]['cells'][$i][22],'in',20));
		$child = trimm(dataIO($data->sheets[0]['cells'][$i][23],'in',20));
		$mob = trimm(dataIO($data->sheets[0]['cells'][$i][24],'in',20));
		$weixin = trimm(dataIO($data->sheets[0]['cells'][$i][25],'in',20));
		$qq = trimm(dataIO($data->sheets[0]['cells'][$i][26],'in',15));
		$areatitle = trimm(dataIO($data->sheets[0]['cells'][$i][27],'in',100));//所在地
		$edu = trimm(dataIO($data->sheets[0]['cells'][$i][28],'in',100));
		$house = trimm(dataIO($data->sheets[0]['cells'][$i][29],'in',100));
		$car = trimm(dataIO($data->sheets[0]['cells'][$i][30],'in',100));
		$RZ_mob = trimm(dataIO($data->sheets[0]['cells'][$i][31],'in',20));
		//$mob_ifshow = trimm(dataIO($data->sheets[0]['cells'][$i][33],'in',20));
		$flag = trimm(dataIO($data->sheets[0]['cells'][$i][33],'in',20));
		$companyname = trimm(dataIO($data->sheets[0]['cells'][$i][34],'in',100));
		$pay = trimm(dataIO($data->sheets[0]['cells'][$i][35],'in',20));
		$aboutus = trimm(dataIO($data->sheets[0]['cells'][$i][36],'in',2000));
		$regtime = trimm(dataIO($data->sheets[0]['cells'][$i][37],'in',20));
		$endtime = trimm(dataIO($data->sheets[0]['cells'][$i][38],'in',20));
		$mate_areatitle = trimm(dataIO($data->sheets[0]['cells'][$i][39],'in',40));
		$mate_age = trimm(dataIO($data->sheets[0]['cells'][$i][40],'in',20));
		$mate_love = trimm(dataIO($data->sheets[0]['cells'][$i][41],'in',20));
		$mate_heigh = trimm(dataIO($data->sheets[0]['cells'][$i][42],'in',20));
		$mate_edu = trimm(dataIO($data->sheets[0]['cells'][$i][43],'in',20));
		$bz = trimm(dataIO($data->sheets[0]['cells'][$i][45],'in',200));
		//数据对应转换
		if(!empty($birthday)){$birthday = strtotime($birthday);$birthday=date("Y-m-d",$birthday);}else{$birthday='0000-00-00';}
		if($sex=="女"){$sex=2;}else{$sex=1;}
		if(!empty($heigh)){$heigh=str_replace("cm","",$heigh);$heigh=intval(trimm($heigh));}else{$heigh=0;}
		if(!empty($weigh)){$weigh=str_replace("kg","",$weigh);$weigh=intval(trimm($weigh));}else{$weigh=0;}
		switch($blood){case"A":$blood=1;break;case"B":$blood=2;break;case"AB":$blood=3;break;case"O":$blood=4;break;default:$blood=5;break;}
		$nation = getNationId($nation);
		switch($love){case"未婚":$love=1;break;case"丧偶":$love=4;break;case"离婚":$love=3;break;default:$love=0;break;}
		switch($child){case"无小孩":$child=1;break;case"有小孩归自己":$child=2;break;case"有小孩归对方":$child=3;break;default:$child=0;break;}
		switch($edu){case"初中":$edu=1;break;case"高中/中专":$edu=2;break;case"专科":$edu=3;break;case"本科":$edu=4;break;case"硕士":$edu=5;break;case"博士":$edu=6;break;default:$edu=0;break;}
		switch($house){case"已购房-有贷款":$house=1;break;case"已购房-无贷款":$house=2;break;case"需要时购置":$house=3;break;case"无房":$house=4;break;case"无房希望对方解决":$house=5;break;case"无房希望双方解决":$house=6;break;case"与父母同住":$house=7;break;case"独自租房":$house=8;break;case"与人合租":$house=9;break;case"住亲朋家":$house=10;break;case"住单位房":$house=11;break;default:$house=0;break;}
		switch($car){case"暂无购车":$car=2;break;case"已购车-经济型":$car=1;break;case"已购车-中档型":$car=3;break;case"已购车-豪华型":$car=4;break;case"单位用车":$car=5;break;case"需要时购置":$car=6;break;default:$car=0;break;}
		if(!empty($RZ_mob)){if($RZ_mob=="手机认证"){$RZ='mob';}else{$RZ='';}}else{$RZ='';}
		//if(!empty($mob_ifshow)){if($mob_ifshow=="显示"){$mob_ifshow=1;}else{$mob_ifshow=0;}}else{$mob_ifshow=0;}
		$companyname = trimhtml($companyname);
		
		//switch($companyname){case"老师":$job=1;break;case"警察":$job=2;default:$job=0;break;}
		
		
		$aboutus = trimhtml($aboutus);
		if(!empty($flag)){
			if($flag=="显示"){
				$flag=1;
			}elseif($flag=="隐藏"){
				$flag=-2;
			}else{
				$flag=0;	
			}
		}else{
			$flag=1;
		}
		if(!empty($pay)){$pay=getPayId($pay);}
		$pay=intval($pay);
		if(!empty($regtime)){$regtime = strtotime($regtime);}else{$regtime=ADDTIME;}
		if(!empty($endtime)){$endtime = strtotime($endtime);}else{$endtime=ADDTIME;}
		
		if(!empty($mate_age)){
			$mate_age=str_replace("从","",$mate_age);
			$mate_age=str_replace("岁","",$mate_age);
			$mate_age=str_replace("到",",",$mate_age);
			$mate_age=explode(",",$mate_age);
			$mate_age1=$mate_age[0];
			$mate_age2=$mate_age[1];
		}
		
		switch($mate_love){case"未婚":$mate_love=1;break;case"丧偶":$mate_love=4;break;case"离婚":$mate_love=3;break;default:$mate_love=0;break;}
		if(!empty($mate_heigh)){
			$mate_heigh=str_replace("从","",$mate_heigh);
			$mate_heigh=str_replace("到",",",$mate_heigh);
			$mate_heigh=str_replace("CM","",$mate_heigh);
			$mate_heigh=explode(",",$mate_heigh);
			$mate_heigh1=$mate_heigh[0];
			$mate_heigh2=$mate_heigh[1];
		}
		switch($mate_edu){case"初中":$mate_edu=1;break;case"高中/中专":$mate_edu=2;break;case"专科":$mate_edu=3;break;case"本科":$mate_edu=4;break;case"硕士":$mate_edu=5;break;case"博士":$mate_edu=6;break;default:$mate_edu=0;break;}
		$bz = ((!empty($bh))?"编号：".$bh:'').((!empty($bz))?"；".$bz:'');
		
		$mate_heigh1 = intval($mate_heigh1);
		$mate_heigh2 = intval($mate_heigh2);
		$mate_love = intval($mate_love);
		$mate_edu = intval($mate_edu);
		$mate_age1 = intval($mate_age1);
		$mate_age2 = intval($mate_age2);
		
		
		
		//地区处理
		if(!empty($areatitle)){
			$A=explode(',',$areatitle);
			$AT1 = $A[0];
			$AT2 = $A[1];
			$AT3 = $A[2];
			$AT4 = $A[3];
			if (!empty($AT1)){
				$rowa = $db->ROW(__TBL_AREA1__,"id","title LIKE '%".$AT1."%'","num");
				if ($rowa){
					$a1 = intval($rowa[0]);	
					$areaid = $a1;
					if (!empty($AT2)){
						$rowa = $db->ROW(__TBL_AREA2__,"id","fid=".$a1." AND title LIKE '%".$AT2."%'","num");
						if ($rowa){
							$a2 = intval($rowa[0]);
							$areaid = $a1.','.$a2;
							if (!empty($AT3)){
								$rowa = $db->ROW(__TBL_AREA3__,"id","fid=".$a2." AND title LIKE '%".$AT3."%'","num");
								if ($rowa){
									$a3 = $rowa[0];
									$areaid = $a1.','.$a2.','.$a3;
									if (!empty($AT4)){
										$rowa = $db->ROW(__TBL_AREA4__,"id","fid=".$a3." AND title LIKE '%".$AT4."%'","num");
										if ($rowa){
											$a4 = $rowa[0];
											$areaid = $a1.','.$a2.','.$a3.','.$a4;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$areatitle= str_replace(","," ",$areatitle);
		if($db->ROW(__TBL_USER__,"id","mob='$mob'"))continue;
		$db->query("INSERT INTO ".__TBL_USER__." (areaid,flag,nickname,birthday,sex,heigh,weigh,blood,nation,area2title,love,child,mob,weixin,qq,areatitle,edu,house,car,RZ,companyname,pay,aboutus,regtime,endtime,mate_areatitle,mate_age1,mate_age2,mate_love,mate_heigh1,mate_heigh2,mate_edu,bz,dataflag,job) VALUES ('$areaid',$flag,'$nickname','$birthday','$sex','$heigh','$weigh','$blood','$nation','$area2title','$love','$child','$mob','$weixin','$qq','$areatitle','$edu','$house','$car','$RZ','$companyname','$pay','$aboutus','$regtime','$endtime','$mate_areatitle','$mate_age1','$mate_age2','$mate_love','$mate_heigh1','$mate_heigh2','$mate_edu','$bz',1,'$job')");
		$uid = intval($db->insert_id());
		//下载图片
		$SQL = '';
		if(!empty($photo_s)){
			$url=$photo_s;
			$dbname = @wx_get_uinfo_logo($url,$uid);$_s = setpath_s($dbname);
			$newphoto_s = $_ZEAI['up2']."/".$_s;
			if (ifpic($newphoto_s)){
				$SQL .= ",photo_s='$_s',photo_f=1";
			}//else{continue;}
		}
		$uname = 'wy'.$uid;
		$mob = (!empty($mob))?$mob:'123456';
		$pwd = md5($mob);
		$db->query("UPDATE ".__TBL_USER__." SET uname='$uname',pwd='$pwd'".$SQL." WHERE id=".$uid);
		set_data_ed_bfb($uid);
	}
	//header("Location: ".SELF);
	if($total>0){
		AddLog('导入会员 '.$total.' 个');
	}
	alert('导入成功！','back');
}
//function getPayId($str){if($str<1000){$payid=2;}elseif($str<2000){$payid=3;}elseif($str<3000){$payid=4;}elseif($str<4000){$payid=5;}elseif($str<8000){$payid=6;}elseif($str<10000){$payid=7;}elseif($str<20000){$payid=8;}elseif($str<50000){$payid=9;}else{$payid=10;}return $payid;}
function getNationId($str){
	switch($str){
		case"汉族":$nationid=1;break;
		case"藏族":$nationid=2;break;
		case"朝鲜族":$nationid=3;break;
		case"蒙古族":$nationid=4;break;
		case"回族":$nationid=5;break;
		case"满族":$nationid=6;break;
		case"维吾尔族":$nationid=7;break;
		case"壮族":$nationid=8;break;
		case"彝族":$nationid=9;break;
		case"苗族":$nationid=10;break;
		case"侗族":$nationid=11;break;
		case"瑶族":$nationid=12;break;
		case"白族":$nationid=13;break;
		case"布依族":$nationid=14;break;
		case"傣族":$nationid=15;break;
		case"京族":$nationid=16;break;
		case"黎族":$nationid=17;break;
		case"羌族":$nationid=18;break;
		case"怒族":$nationid=19;break;
		case"佤族":$nationid=20;break;
		case"水族":$nationid=21;break;
		case"畲族":$nationid=22;break;
		case"土族":$nationid=23;break;
		case"阿昌族":$nationid=24;break;
		case"哈尼族":$nationid=25;break;
		case"高山族":$nationid=26;break;
		case"景颇族":$nationid=27;break;
		case"珞巴族":$nationid=28;break;
		case"锡伯族":$nationid=29;break;
		case"德昂(崩龙)族":$nationid=30;break;
		case"保安族":$nationid=31;break;
		case"基诺族":$nationid=32;break;
		case"门巴族":$nationid=33;break;
		case"毛南族":$nationid=34;break;
		case"赫哲族":$nationid=35;break;
		case"裕固族":$nationid=36;break;
		case"撒拉族":$nationid=37;break;
		case"独龙族":$nationid=38;break;
		case"普米族":$nationid=39;break;
		case"仫佬族":$nationid=40;break;
		case"仡佬族":$nationid=41;break;
		case"东乡族":$nationid=42;break;
		case"拉祜族":$nationid=43;break;
		case"土家族":$nationid=44;break;
		case"纳西族":$nationid=45;break;
		case"傈僳族":$nationid=46;break;
		case"布朗族":$nationid=47;break;
		case"哈萨克族":$nationid=48;break;
		case"达斡尔族":$nationid=49;break;
		case"鄂伦春族":$nationid=50;break;
		case"鄂温克族":$nationid=51;break;
		case"俄罗斯族":$nationid=52;break;
		case"塔塔尔族":$nationid=53;break;
		case"塔吉克族":$nationid=54;break;
		case"柯尔克孜族":$nationid=55;break;
		case"乌兹别克族":$nationid=56;break;
		default:$nationid=0;break;
	}
	return $nationid;
}
function getPayId($str){
	if($str>=50000){
		return 9;
	}elseif($str>=20000 && $str<50000){
		return 8;
	}elseif($str>=10000 && $str<20000){
		return 7;
	}elseif($str>=8000 && $str<10000){
		return 6;
	}elseif($str>=5000 && $str<8000){
		return 5;
	}elseif($str>=3000 && $str<4000){
		return 4;
	}elseif($str>=2000 && $str<3000){
		return 3;
	}elseif($str>=1000 && $str<2000){
		return 2;
	}elseif($str<1000){
		return 1;
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<body>
<div class="navbox">
    <a href="u_add.php">录入新用户</a>
	<a href="import.php" class="ed">某缘会员导入</a>
	<a onClick="zeai.alert('请联系择爱官方客服咨询');">某E免费版会员导入</a>
	<a onClick="zeai.alert('请联系择爱官方客服咨询');">某E商业版会员导入</a>
</div>
<div class="fixedblank"></div>
<form action="import.php?submitok=import" name="form_execl" method="post" enctype="multipart/form-data" onSubmit="zeai.msg('数据导入中，请匆关闭窗口...',{time:7200})">
<table class="table" style="margin-top:100px;">
    <tr>
    	<td width="500" align="center" class="border0" style="padding:20px 0;">
        <input type="file" accept="application/vnd.ms-excel" name="execl" class="input" style="width:420px;height:30px;line-height:30px;" /> <input type="submit" class="btn HUANG3" name="submit" value="开始导入" />
        </td>
    </tr>
    <tr><td align="left" style="font-size:14px;line-height:200%;padding:20px;background-color:#f5f5f5">注意事项：<br>
    1、导入的Execl表格请从微缘数据下载的，请勿私自改动数据结构<br>
    2、在导入过程中请勿关闭页面<br>
    3、由于图片下载量大，非常耗时，单次导入请保证数据控制在200条以内，分批次导入<br>
    4、如果遇到导入出错，请联系管理员<br>
    5、由于数据量太大，请确认PHP环境超时时间足够长，一般至少要10分钟，发送过程中请不要关闭窗口，耐心等待<br>
    6、有手机的新密码为手机号码，没手机的是123456，导入后推荐用UID+密码方式登录<br>
	7、如果提醒没有选中的文件，请用微软Excel打开数据另存为 Excel97-2003格式，如下图<br>
    <img src="images/excel_1.png"></td></tr>
</table>
</form>
<?php require_once 'bottomadm.php';?>
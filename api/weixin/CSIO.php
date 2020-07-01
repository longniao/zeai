<?php
require_once '../../sub/init.php';
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_reg.php';$TG_set = json_decode($_REG['TG_set'],true);//$tg=json_decode($_REG['tg'],true);
require_once ZEAI.'sub/TGfun.php';
require_once ZEAI.'cache/config_wxgzh.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/udata.php';						
$ZEAI_WX = new gylCSIO();
class gylCSIO {
	private $fromUsername='';
	private $toUsername='';
	function __construct(){
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if(empty($postStr))$postStr = file_get_contents('php://input');  
	    $err_str="";
		global $db,$_ZEAI,$_REG,$_GZH,$TG_set,$navarr;
		if (!empty($postStr)){ 
		    $xml = new SimpleXMLElement($postStr);$xml || exit;
            foreach ($xml as $key => $value) {$postObj[$key] = strval($value);}
			$this->fromUsername = $postObj['FromUserName'];
			$this->toUsername   = $postObj['ToUserName'];
			$Event     = $postObj['Event'];
			$EventKey  = $postObj['EventKey'];
			$keyword   = trim($postObj['Content']);
			$MsgType   = $postObj['MsgType'];
			$Longitude = $postObj['Longitude'];
			$Latitude  = $postObj['Latitude'];
			$arr=array('msgType'=>'text','contentStr'=>'亲，请从底部公众号菜单进入体验！');
			$ip = getip();
			//
			$server_openid  = $this->fromUsername;
			$server_token   = wx_get_access_token();
			$wxuinfo  = wx_get_uinfo($server_token,$server_openid);
			$server_unionid = $wxuinfo['unionid'];
			$nickname = trimm(dataIO($wxuinfo['nickname'],'in',100));
			$sex      = intval($wxuinfo['sex']);$sex = (empty($sex))?1:$sex;
			$server_subscribe = $wxuinfo['subscribe'];
			if($server_subscribe==1 && !empty($server_openid)){
				$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",endip='$ip',subscribe=1,unionid='$server_unionid' WHERE openid='".$server_openid."'");
				$db->query("UPDATE ".__TBL_TG_USER__." SET endtime=".ADDTIME.",endip='$ip',subscribe=1 WHERE openid='".$server_openid."'");
			}
			switch ($Event) {
				case "CLICK":
					//菜单呼出
					if($EventKey=='tgewm'){
						$row  = $db->ROW(__TBL_TG_USER__,"id,pwd,flag","openid='".$server_openid."'","num");
						$uid= $row[0];$pwd= $row[1];$flag= $row[2];
						if($flag==1){
							$ret = make_tg_ewmGZH($server_token,$uid,$pwd);
							$tgpic2 = $ret['tgpic'];
							if (!empty($tgpic2))@wx_kf_sent($server_openid,$tgpic2,'image');
							$C = urlencode('<a href="'.HOST.'/m1/tg_my.php">【点此进入推广中心】</a>');
							@wx_kf_sent($server_openid,$C,'text');
						}else{
							$C = urlencode('<a href="'.HOST.'/m1/tg_my.php">【点此进入推广中心】</a>');
							@wx_kf_sent($server_openid,$C,'text');
						}
					}
				break;
				case "LOCATION":
					$db->query("UPDATE ".__TBL_USER__." SET longitude='$Longitude',latitude='$Latitude',endtime=".ADDTIME.",endip='$ip' WHERE openid='".$server_openid."'");
					//$db->query("UPDATE ".__TBL_TG_USER__." SET longitude='$Longitude',latitude='$Latitude',endtime=".ADDTIME.",endip='$ip' WHERE openid='".$server_openid."'");
				break;
				case "subscribe":
					$db->query("UPDATE ".__TBL_ADMIN__." SET subscribe=1 WHERE openid='".$server_openid."'");
					$ifbd__=false;$iftg__=false;$ifhn__=false;$evenUID=0;$regkind=3;
					if(!empty($EventKey)){
						if(strstr($EventKey,'bdadm')){
							$admid = intval(substr($EventKey,14,8));
							$row = $db->ROW(__TBL_ADMIN__,"id,truename,subscribe","openid='".$server_openid."' OR (unionid='".$server_unionid."' AND unionid<>'')","name");
							if ($row){
								$admid_   = dataIO($row['id'],'out');
								$truename_ = dataIO($row['truename'],'out');
								if($subscribe==1){
									$arr=array('msgType'=>'text','contentStr'=>dataIO('当前微信已被【'.$truename_.'(ID:'.$admid_.')】绑定，请改用其他微信号绑定 或 联系超级管理员对【'.$truename_.'(ID:'.$admid_.')】进行解绑，然后再次用当前微信进行绑定','wx'));
								}
							}else{
								$row = $db->ROW(__TBL_ADMIN__,"truename","id=".$admid,"name");
								if ($row){
									$truename = dataIO($row['truename'],'out');
									$db->query("UPDATE ".__TBL_ADMIN__." SET openid='".$server_openid."',unionid='".$server_unionid."',subscribe=1 WHERE id=".$admid);
									$arr=array('msgType'=>'text','contentStr'=>'【'.$truename.'(ID:'.$admid.')】扫码绑定成功！');
								}else{
									$arr=array('msgType'=>'text','contentStr'=>'管理员/红娘库暂无记录！');
								}
							}
							echo $this->get_restr($arr);
							exit;
						}elseif(strstr($EventKey,'tghn')){
							$tg_uid = intval(substr($EventKey,25,8));
							$db->query("UPDATE ".__TBL_TG_USER__." SET openid='$server_openid',subscribe=1 WHERE id=".$tg_uid);
							$C = '';
							if(!empty($TG_set['wx_gzh_welcome'])){
								$C = dataIO($TG_set['wx_gzh_welcome'],'wx');
							}
							//关后返回指引最后页面
							$row = $db->ROW(__TBL_WXENDURL__,"title,url","uid=".$tg_uid,"num");
							if ($row){
								$title=$row[0];
								$url  =dataIO($row[1],'out');
								$C2 = $title.'<br><br>→<a href="'.$url.'">【点此继续浏览】</a><br><br>　';
								$C2 = dataIO($C2,'wx');
								$db->query("DELETE FROM ".__TBL_WXENDURL__." WHERE uid=".$tg_uid);
								//输出
							}else{
								$url  = HOST.'/m1/tg_my.php';
								$C2 = '<br><br>→<a href="'.$url.'">【点此进入推广中心】</a><br><br>　';
								$C2 = dataIO($C2,'wx');
							}
							$C = $C.$C2;
							echo $this->get_restr(array('msgType'=>'text','contentStr'=>$C));
							exit;
						}elseif(strstr($EventKey,'tghb')){
							$tg_uid = intval(substr($EventKey,25,8));
							/*
							$C = '';
							if(!empty($TG_set['wxhbT'])){
								$C = dataIO($TG_set['wxhbT'],'wx');
							}
							$url1 = HOST.'/m1/reg_diy.php?subscribe=1&tguid='.$tg_uid;
							$url2 = HOST.'/m1/tg_reg.php?subscribe=1&tguid='.$tg_uid;
							$C2  = dataIO($nickname.' 您好！<br>请开启手机定位并点击上面蓝色文字地理位置【允许使用】，否则您无法查看附近的人哦~~<br><br>');
							$C2  = $C2.$C;
							$C2 .= '　<br><br>您还未注册，请先注册<br><br>我是单身→<a href="'.$url1.'">【点此注册】</a>　<br><br>';
							if(@in_array('tg',$navarr))$C2 .= '　<br>红娘/商家→<a href="'.$url2.'">【点此注册】</a><br><br>　';
							$C = dataIO($C2,'wx');
							echo $this->get_restr(array('msgType'=>'text','contentStr'=>$C));
							exit;
							*/
							$evenUID   = $tg_uid;
							$iftg__=true;
						}elseif(strstr($EventKey,'tg')){
							$even = substr($EventKey,23,20);
							$even = explode('_',$even);
							$evenTGUID = $even[0];//$evenUID = $even[1];
							$evenUID   = $evenTGUID;
							$iftg__=true;
						}elseif(strstr($EventKey,'bd')){
							$evenUID = intval(substr($EventKey,11,8));
							$ifbd__=true;
						}elseif(strstr($EventKey,'hn')){	
							$evenUID = intval(substr($EventKey,11,8));
							$ifhn__=true;
							$regkind=10;
						}
					}
					if($ifbd__){
						$row = $db->ROW(__TBL_USER__,"id,nickname,subscribe","openid='".$server_openid."' OR (unionid='".$server_unionid."' AND unionid<>'')","name");
						if ($row){
							$uid_bd=$row['id'];
							$nickname_bd=dataIO($row['nickname'],'out');
							$subscribe_bd=$row['subscribe'];
							if($subscribe_bd==0){
								$C = '当前微信已绑定过别的会员帐号【'.$nickname_bd.'(uid:'.$uid_bd.')】，请改用其他微信号绑定！<br><br> 注：可以用被绑定的会员帐号进入“我的”设置进行解绑';
								$C = dataIO($C,'wx');
							}else{
								$arr=array('msgType'=>'text','contentStr'=>'扫码绑定成功！');
							}
							echo $this->get_restr($arr);
						}else{
							if (ifint($evenUID))$db->query("UPDATE ".__TBL_USER__." SET openid ='".$server_openid."',unionid ='".$server_unionid."',subscribe=1 WHERE id=".$evenUID);/*(openid='' OR openid IS NULL) AND */
							$arr=array('msgType'=>'text','contentStr'=>'扫码绑定成功！');
							echo $this->get_restr($arr);
						}
						exit;
					}
					
					$rt = $db->query("SELECT id,unionid FROM ".__TBL_USER__." WHERE openid='".$server_openid."'  OR (unionid='".$server_unionid."' AND unionid<>'') ");
					if (!$db->num_rows($rt)){
						//地区处理
						$province = trimm($wxuinfo['province']);
						$city     = trimm($wxuinfo['city']);
						if (!empty($province)){
							$rowa = $db->ROW(__TBL_AREA1__,"id","title LIKE '%".$province."%'");
							if ($rowa)$a1 = $rowa[0];
							if (ifint($a1)){
								$rowa = $db->ROW(__TBL_AREA2__,"id","title LIKE '%".$city."%'");
								if ($rowa){
									$a2 = $rowa[0];
									$areaid = $a1.','.$a2;
								}
							}
						}
						$areatitle= $province.' '.$city;
						//入主表
						$RegLoveb  = abs(intval($_REG['reg_loveb']));
						$reg_grade = (ifint($_REG['reg_grade']) && $_REG['reg_grade']<=10)?intval($_REG['reg_grade']):1;
						//$reg_flag  = ($_REG['reg_flag'] == 2 || $_REG['reg_flag'] == 3)?$_REG['reg_flag']:1;
						$flag  =($_REG['reg_flag']==1)?1:0;
						$flag  =($_REG['gzflag2']==1)?2:$flag;
						$reg_if2   = 999;
						$db->query("INSERT INTO ".__TBL_USER__." (flag,nickname,sex,grade,areaid,areatitle,openid,unionid,pwd,loveb,regtime,endtime,regip,endip,refresh_time,subscribe,regkind) VALUES ($flag,'".$nickname."',$sex,$reg_grade,'".$areaid."','".$areatitle."','".$server_openid."','".$server_unionid."','www@zeai@cn@v6.0',".$RegLoveb.",".ADDTIME.",".ADDTIME.",'$ip','$ip',".ADDTIME.",1,$regkind)");
						$uid = intval($db->insert_id());
						$uname = 'wxgz_'.$uid;
						$photo_s='';
						if($_GZH['wx_gzh_getphoto_s']==1){$dbname = (!empty($wxuinfo['headimgurl']))?wx_get_uinfo_logo($wxuinfo['headimgurl'],$uid):'';$photo_s=setpath_s($dbname);}
						if($ifhn__){
							$row = $db->ROW(__TBL_CRM_HN__,"uid,agentid,agenttitle,truename","id=".$evenUID,"name");
							if ($row){
								$hn_uid     = $row['uid'];
								$agentid    = intval($row['agentid']);
								$agenttitle = $row['agenttitle'];
								$admname    = $row['truename'];
								$hnsql      = ",admid=".$evenUID.",admname='".$admname."',admtime=".ADDTIME.",agentid=".$agentid.",agenttitle='".$agenttitle."' ";
								//通知红娘
								$row = $db->ROW(__TBL_USER__,"nickname,openid,subscribe","id=".$hn_uid,"num");
								if ($row){
									$hn_nickname = dataIO($row[0],'out');
									$hn_openid   = $row[1];
									$hn_subscribe= $row[2];
									//站内tips发给红娘
									$T = '【'.$nickname.' uid:'.$uid.'】通过你的二维码关注成功';
									$C = $T.'，请到【CRM】顶部【售前】进行跟进/服务，尽快促单';
									$db->SendTip($hn_uid,$T,dataIO($C,'in'),'sys');
									//微信通知
									if ($hn_subscribe == 1 && !empty($hn_openid)){
										//客服通知
										$hn_content = urlencode($C);
										$ret = @wx_kf_sent($hn_openid,$hn_content,'text');
										$ret = json_decode($ret);
										//模版通知
										if ($ret->errmsg != 'ok'){
											$keyword1  = $hn_content;
											$keyword3  = urlencode($_ZEAI['siteName']);
											@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$hn_openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url=');
										}
									}
								}
								//通知红娘结束
							}
						}
						if($iftg__)$tgsql=",tguid=".$evenUID;
						$db->query("UPDATE ".__TBL_USER__." SET uname='$uname',photo_s='$photo_s'".$tgsql.$hnsql." WHERE id=".$uid);
						//以下为通知
						$admC = ($iftg__ && !empty($TG_set['wxhbT']))?dataIO($TG_set['wxhbT'],'wx'):dataIO($nickname.' 您好！<br>'.$_GZH['wx_gzh_welcome'],'wx');
						@wx_kf_sent($server_openid,urlencode($admC),'text');
						//
						if($_REG['gzflag2']==1){
							if($ifhn__)$evenUID=0;//管理员红娘不奖励
							$regurl=($_REG['reg_style']==1)?'reg_alone.':'reg_diy.';
							//$url1 = HOST.'/m1/'.$regurl.'php?subscribe=1&tguid='.$evenUID;
							//$url2 = HOST.'/m1/tg_reg.php?subscribe=1&tguid='.$evenUID;
							
							$url1 = HOST.'/m1/reg.php?subscribe=1&tguid='.$evenUID;
							
							$C2  .= '　<br>您还未注册，为了提高您的相亲成功率，请您选择正确身份注册<br><br><a href="'.$url1.'">【点此注册】</a><br><br>　';
							//if(@in_array('tg',$navarr))$C2 .= '　<br>红娘/商家→<a href="'.$url2.'">【点此注册】</a><br><br>　';
							echo $this->get_restr(array('msgType'=>'text','contentStr'=>dataIO($C2,'wx')));
						}else{
							@wx_kf_sent($server_openid,HOST.'/res/gzh_loginhelp.jpg','image');
							$url = HOST.'/m1/login.php';
							$C2 = '　<br>页面打开后，请点击右下角绿色 微信图标 进入，如图<br><br>→<a href="'.$url.'">【点此登录】</a><br><br>　';
							$C2 = dataIO($C2,'wx');
							echo $this->get_restr(array('msgType'=>'text','contentStr'=>$C2));
						}
						if ($RegLoveb > 0){
							//写清单
							$db->AddLovebRmbList($uid,'新用户注册',$RegLoveb,'loveb',6);		
							//站内信
							$T = dataIO('欢迎加入'.$_ZEAI['siteName'].'，赠送您'.$RegLoveb.$_ZEAI['loveB'],'in');
							$C = $T.dataIO("，您有一笔".$_ZEAI['loveB']."到账！　<a href='".Href('loveb')."' class=aQING>查看详情</a>",'in');
							$db->SendTip($uid,$T,$C,'sys');
						}
						set_data_ed_bfb($uid);
						if($iftg__ && ifint($evenUID) && @in_array('tg',$navarr) ){TG($evenUID,$uid,'reg');}
					}else{
						$row          = $db->fetch_array($rt,'num');
						$uid          = $row[0];
						$data_unionid = $row[1];
						if (str_len($data_unionid) < 10)$sql = ",unionid ='".$server_unionid."'";
						$db->query("UPDATE ".__TBL_USER__." SET endtime=".ADDTIME.",endip='$ip',subscribe=1".$sql." WHERE id=".$uid);
						//
						$C = dataIO(dataIO($nickname,'out').' 您好！<br>欢迎再次回来~~<br><br>'.$_GZH['wx_gzh_welcome'].'<br><br>','wx');
						//关后返回指引最后页面
						$C2 = '';
						$row = $db->ROW(__TBL_WXENDURL__,"title,url","uid=".$uid,"num");
						if ($row){
							$title=$row[0];
							$url  =dataIO($row[1],'out');
							$C2 = $title.'<br><br>→<a href="'.$url.'">【点此继续浏览】</a><br><br>　';
							$C2 = dataIO($C2,'wx');
							$db->query("DELETE FROM ".__TBL_WXENDURL__." WHERE uid=".$uid);
						}
						$C = $C.$C2;
						//输出
						echo $this->get_restr(array('msgType'=>'text','contentStr'=>$C));
					}
				break;
				case 'unsubscribe':
					$db->query("UPDATE ".__TBL_USER__." SET subscribe=2 WHERE openid='".$server_openid."'");
					$db->query("UPDATE ".__TBL_TG_USER__." SET subscribe=2 WHERE openid='".$server_openid."'");
					$db->query("UPDATE ".__TBL_ADMIN__." SET subscribe=2 WHERE openid='".$server_openid."'");
					//
					$row = $db->ROW(__TBL_USER__,"id,nickname,mob","openid='".$server_openid."'","num");
					if ($row){
						$uid= $row[0];
						$nickname= dataIO($row[1],'out');
						$mob= dataIO($row[2],'out');
						//
						$rt=$db->query("SELECT openid,subscribe FROM ".__TBL_USER__." WHERE ifadm=1");
						$total = $db->num_rows($rt);
						if ($total > 0) {
							for($i=1;$i<=$total;$i++) {
								$rows = $db->fetch_array($rt,'name');
								if(!$rows) break;
								$openid    = $rows['openid'];
								$subscribe = $rows['subscribe'];
								if (!empty($openid) && $subscribe==1){
									$first     = urlencode('用户取消关注公众号');
									$keyword1  = urlencode('取消关注公众号');
									$keyword3  = urlencode('【'.$nickname.' uid:'.$uid.' 手机:'.$mob.'】已取消关注！');
									$remark    = '请尽快联系此用户，咨询取消原因，进行更好的服务。';
									@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url=');
								}
							}
						}
					}
				break;
				case 'SCAN':
					if(strstr($EventKey,'bdadm')){
						$admid = intval(substr($EventKey,6,8));
						$row = $db->ROW(__TBL_ADMIN__,"id,truename","openid='".$server_openid."' OR (unionid='".$server_unionid."' AND unionid<>'')","name");
						if ($row){
							$admid_   = dataIO($row['id'],'out');
							$truename_ = dataIO($row['truename'],'out');
							$arr=array('msgType'=>'text','contentStr'=>dataIO('当前微信已被【'.$truename_.'(ID:'.$admid_.')】绑定，请改用其他微信号绑定 或 联系超级管理员对【'.$truename_.'(ID:'.$admid_.')】进行解绑，然后再次用当前微信进行绑定','wx'));
						}else{
							$row = $db->ROW(__TBL_ADMIN__,"truename","id=".$admid,"name");
							if ($row){
								$truename = dataIO($row['truename'],'out');
								$db->query("UPDATE ".__TBL_ADMIN__." SET openid='".$server_openid."',unionid='".$server_unionid."',subscribe=1 WHERE id=".$admid);
								$arr=array('msgType'=>'text','contentStr'=>'【'.$truename.'(ID:'.$admid.')】扫码绑定成功！');
							}else{
								$arr=array('msgType'=>'text','contentStr'=>'管理员/红娘库暂无记录！');
							}
						}
						echo $this->get_restr($arr);
						exit;
					}elseif(strstr($EventKey,'bd')){
						$evenUID = intval(substr($EventKey,2,8));
						$evenKIND='bd';
					}elseif(strstr($EventKey,'tghb')){
						$evenKIND='tghb';
					}elseif(strstr($EventKey,'tg')){
						//$evenUID = intval(substr($EventKey,15,8));
						$even = substr($EventKey,15,20);
						$even = explode('_',$even);
						$evenTGUID = $even[0];$evenUID = $even[1];
						$evenKIND='tg';
					}
					if ($evenKIND == 'bd'){
						$row = $db->ROW(__TBL_USER__,"id,nickname,subscribe","openid='".$server_openid."' OR (unionid='".$server_unionid."' AND unionid<>'')","name");
						if ($row){
							$uid_bd=$row['id'];
							$nickname_bd=dataIO($row['nickname'],'out');
							$subscribe_bd=$row['subscribe'];
							if($subscribe_bd==0){
								$C = '当前微信已绑定过别的会员帐号【'.$nickname_bd.'(uid:'.$uid_bd.')】，请改用其他微信号绑定！<br><br> 注：可以用被绑定的会员帐号进入“我的”设置进行解绑';
								$C = dataIO($C,'wx');
								$arr=array('msgType'=>'text','contentStr'=>$C);
							}else{
								$arr=array('msgType'=>'text','contentStr'=>'扫码绑定成功！');
							}
						}else{
							if (ifint($evenUID))$db->query("UPDATE ".__TBL_USER__." SET openid ='".$server_openid."',unionid ='".$server_unionid."',subscribe=1 WHERE id=".$evenUID);
							$arr=array('msgType'=>'text','contentStr'=>'扫码绑定成功！');
						}
					}elseif($evenKIND == 'tg'){
						$row = $db->ROW(__TBL_USER__,"nickname,sex,photo_s,photo_f,birthday,areatitle,love,heigh,weigh,edu,pay,house,car,child,blood,pay,blood,job,admid,marrytime,companykind,smoking,drink,marrytime","id=".$evenUID,"name");
						if ($row){
							$sex        = $row['sex'];
							$photo_s    = $row['photo_s'];
							$photo_f    = $row['photo_f'];
							$birthday   = $row['birthday'];
							$nickname   = dataIO($row['nickname'],'out');
							$areatitle  = dataIO($row['areatitle'],'out');
							$heigh      = $row['heigh'];
							$weigh      = $row['weigh'];
							$love       = $row['love'];
							$edu        = $row['edu'];
							$pay        = $row['pay'];
							$job        = $row['job'];
							$house      = $row['house'];
							$car        = $row['car'];
							$child      = $row['child'];
							$blood      = $row['blood'];
							//
							$marrytime    = $row['marrytime'];
							$companykind  = $row['companykind'];
							$area2title  = dataIO($row['area2title'],'out');
							$smoking  = $row['smoking'];
							$drink    = $row['drink'];
							$area_s_title = explode(' ',$areatitle);$area_s_title = $area_s_title[1];
							$area_s_title2 = explode(' ',$area2title);$area_s_title2 = $area_s_title2[1].$area_s_title2[2];
							$sex_str2 = ($sex == 1)?'男':'女';
							$marrytime_str=udata('marrytime',$marrytime);
							$birthday_str  = (!empty($birthday) && $birthday!='0000-00-00')?getage($birthday).'岁':'';
						}
						$br = '<br>';
						$C .= 'UID：'.$evenUID.$br;
						if (!empty($nickname))$C .= '昵称：'.$nickname.$br;
						$C .= '性别：'.$sex_str2.$br;
						if (!empty($birthday) && $birthday!='0000-00-00')$C .= '生日：'.$birthday.$br;
						if (!empty($areatitle))$C .= '工作地区：'.$areatitle.$br;
						if (!empty($area2title))$C .= '户籍地区：'.$area2title.$br;
						if (!empty($love)){
							 if (!empty($cihld))$child_str='（'.udata('cihld',$cihld).'）';
							$C .= '婚姻状况：'.udata('love',$love).$child_str.$br;
						}
						if (!empty($heigh))$C .= '身高：'.udata('heigh',$heigh).$br;
						if (!empty($edu))$C .= '学历：'.udata('edu',$edu).$br;
						if (!empty($job))$C .= '职业：'.udata('job',$job).$br;
						if (!empty($companykind))$C .= '单位类型	：'.udata('companykind',$companykind).$br;
						if (!empty($pay))$C .= '月收入：'.udata('pay',$pay).$br;
						if (!empty($house))$C .= '房子：'.udata('house',$house).$br;
						if (!empty($car))$C .= '车子：'.udata('car',$car).$br;
						if (!empty($smoking))$C .= '抽烟	：'.udata('smoking',$smoking).$br;
						if (!empty($drink))$C .= '喝酒：'.udata('drink',$drink).$br;
						if (!empty($marrytime) && $marrytime_str!='不限'){
							$C .= '期望：'.$marrytime_str.'结婚'.$br;
						}
						$C .= '　<br>查看联系 → <a href="'.mHref('u',$evenUID).'">【点此进入】</a><br><br>　';
						$C = urlencode(dataIO($C,'wx'));
						@wx_kf_sent($server_openid,$C,'text');
					}else{
						echo $this->get_restr($arr);
					}
				break;
				default:
					echo 'no message!';
				break;
			}
			//输入内容
			if(!empty($keyword)){
				if ($keyword == 'test'){

				}elseif($keyword == 'token'){
					//$arr=array('msgType'=>'text','contentStr'=>$server_token);
					//echo $this->get_restr($arr);
				}elseif($keyword == 'openid'){
					$arr=array('msgType'=>'text','contentStr'=>$this->fromUsername);
					echo $this->get_restr($arr);
				}else{
					$arr=array('msgType'=>'text','contentStr'=>dataIO($_GZH['wx_gzh_hfcontent'],'wx'));
					echo $this->get_restr($arr);
				}
			}
		}else{
			$echoStr = $_GET["echostr"];
			if($this->checkSignature()){header('content-type:text');echo $echoStr;exit;}
			$db->query("UPDATE ".__TBL_USER__." SET subscribe=1 WHERE openid='".$server_openid."'");
		}
	}
	private function test($re_arr){
		
		
	}
	private function get_restr($re_arr){
		 $time = time();
		 $msgType = $re_arr['msgType'];
		 $fromUsername= $this->fromUsername;
         $toUsername= $this->toUsername;
		 switch($msgType){
			 case "text":
				$contentStr=$re_arr['contentStr'];
				$resultStr='<xml><ToUserName><![CDATA['.$fromUsername.']]></ToUserName><FromUserName><![CDATA['.$toUsername.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.$contentStr.']]></Content><FuncFlag>0</FuncFlag></xml>';
				return $resultStr;
		    break;
			case "news":
				$news_list=$re_arr['news_list'];
				$news_count=count($re_arr['news_list']);

				$news_str="";
				foreach($news_list as $list){
				   $news_str=$news_str.'<item><Title><![CDATA['.$list['title'].']]></Title> <Description><![CDATA['.$list['description'].']]></Description><PicUrl><![CDATA['.$list['picurl'].']]></PicUrl><Url><![CDATA['.$list['url'].']]></Url></item>';}
					$resultStr='<xml><ToUserName><![CDATA['.$fromUsername.']]></ToUserName><FromUserName><![CDATA['.$toUsername.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>'.$news_count.'</ArticleCount><Articles>'.$news_str.'</Articles></xml>';
				return $resultStr;
		    break;
		}
	}
	private function checkSignature(){
        global $_ZEAI,$_GZH;
		$signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce  = $_GET["nonce"];
		$token  = $_GZH['wx_gzh_token'];
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );		
		if( $tmpStr == $signature ){return true;}else{return false;}
	}
}
function make_tg_ewmGZH($token,$cook_uid,$cook_pwd){
	global $_ZEAI;
	$url = $_ZEAI['up2'].'/TG_ewm.php';
	$data = array (
		'uid' => $cook_uid,
		'pwd' => $cook_pwd,
		'browser' => 'wx',
		'token' => $token
	);
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );
	$ret = json_decode($return,true);
	return $ret;
}

?>
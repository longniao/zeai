<?php
if (ini_get('session.auto_start') == 0)session_start();
require_once ZEAI.'cache/config_adm.php';
require_once ZEAI.'cache/config_crm.php';

$iflogin = (ifint($_SESSION['admuid']) && isset($_SESSION['admuid']) && str_len($_SESSION['admpwd']) == 32)?true:false;


if ($iflogin){
	require_once ZEAI.'sub/conn.php';
	if (!$db->ROW(__TBL_ADMIN__,"id","flag=1 AND id=".$_SESSION['admuid']." AND password='".$_SESSION['admpwd']."'"))$iflogin = false;
}
if (!$iflogin){
	if(strpos($submitok,'ajax_') !== false){
		json_exit(array('flag'=>'nologin','msg'=>'由于长时间不操作超时，为了安全请重新登录后再操作～','jumpurl'=>'login.php'));
	}else{
		exit("<html><body><script>window.onload = function (){parent.location.href='login.php';}</script></body></html>");
	}
}
$_Style['list_bg']       = '#ffffff';
$_Style['list_overbg']   = '#F9F9FA';//MouseOver
$_Style['list_selectbg'] = '#EAEDF1';//Selected

$_ZEAI['Photo_awardARR']  = '10,50,100';//照片奖励列表选项,以半角逗号隔开
$_ZEAI['Video_awardARR']  = '50,100,300';//视频奖励列表选项,以半角逗号隔开

function crm_crmkindtitle($crmkind,$fg='-') {
	$crmkindARR = explode(',',$crmkind);
	$out=array();
	if (count($crmkindARR) >= 1 && is_array($crmkindARR)){
		foreach ($crmkindARR as $V) {
			$out[] = crm_crmkindtitle_alone($V);
		}
	}
	$r = implode($fg,$out);
	return $r;
}
function crm_crmkindtitle_alone($v) {
	switch ($v) {
		case 'adm':$v='管理员';break;
		case 'sq':$v='售前';break;
		case 'sh':$v='售后';break;
		case 'ht':$v='合同';break;
		case 'cw':$v='财务';break;
	}
	return $v;
}

function crm_ugrade_title($grade,$return='--'){
	global $_UDATA;
	$R = json_decode($_UDATA['crm_ugrade']);
	foreach($R as $v){if ($grade == $v->i){$return=$v->v;}}
	return $return;
}
function crm_ugrade_time($uid,$crm_ugrade,$ifA='btn_djs',$crm_usjtime1=0,$crm_usjtime2=0) {
	switch ($crm_ugrade) {
		default:$btncls   = " class='aHUI'";break;
		case 1:$btncls   = " class='aBAI'";break;
		case 2:$btncls   = " class='aLAN'";break;
		case 3:$btncls   = " class='aFEN'";break;
		case 4:$btncls   = " class='aTUHAO'";break;
		case 5:$btncls   = " class='aZI'";break;
		case 6:$btncls   = " class='aHONG'";break;
		case 7:$btncls   = " class='aLV'";break;
		case 8:$btncls   = " class='aHUANG'";break;
		case 9:$btncls   = " class='aJIN'";break;
		case 10:$btncls  = " class='aQINGed'";break;
	}
	$crmutitle = crm_ugrade_title($crm_ugrade);
	if($ifA=='noAno__'){
		if(!empty($crmutitle) && $crmutitle!='--'){
			return "<a ".$btncls.">".$crmutitle."</a>";
		}else{
			return'';	
		}
	}
	if($ifA == 'btn_djs' || $ifA == 'btn'){
		$onclick = " title=\"点击修改\" onClick=\"zeai.iframe('【".$uid."】客户等级','crm_user.php?submitok=crm_ugrade_mod&uid=".$uid."',600,460);\"";
	}else{
		$onclick = "";
		$ifA = 'btn_djs';
	}
	$ret = ($ifA == 'btn_djs' || $ifA == 'btn')?"<a href=\"javascript:;\" ".$onclick.$btncls.">".$crmutitle."</a>":"";
	if (!empty($crm_usjtime1) && !empty($crm_usjtime2) && ($ifA == 'btn_djs' || $ifA == 'djs')){
		$d1  = ADDTIME;
		$d2  = $crm_usjtime2;
		$ddiff = $d2-$d1;
		if ($ddiff < 0){
			$ret .= '<br><em>';
			$ret .= '<font class="Cf00 B">已过期</font>';
			$ret .= '<br>过期日：'.YmdHis($d2,'Ymd');
			$ret .= '</em>';
		} else {
			$tmpday   = intval($ddiff/86400);
			$ret .= '<br><em>';
			$ret .= '还剩<font class="Cf00">'.$tmpday.'</font>天';
			$ret .= '<br>到期日：'.YmdHis($d2,'Ymd');
			$ret .= '</em>';
		}
	}
	$ret = '<span class="crm_ugrade">'.$ret.'</span>';
	return $ret;
}

$QXARR = explode(',',$_SESSION["authoritylist"]);
function noauth($t='您所在的角色组此功能没有开放') {
	global $_ZEAI;
	$ret  ="<!doctype html><html><head><meta charset='utf-8'><title>".$t."</title>".HEADMETA."<link href='".$_ZEAI['adm2']."/css/main.css' rel='stylesheet' type='text/css' /></head><body>";
	$ret .= "<div class='nodataico'><i></i>".$t."</div>";
	$ret .= "</body></html>";
	return $ret;
}
function ifsqsh($uid,$str='只有被分配的红娘才可以操作') {
	global $db,$session_uid,$QXARR;
	if(!@in_array('crm',$QXARR)){
		$hnid=0;$hnid2=0;
		$row = $db->ROW(__TBL_USER__,"hnid,hnid2","id=".$uid,"name");
		if ($row){$hnid=$row['hnid'];$hnid2=$row['hnid2'];}
		if($session_uid != $hnid && $session_uid != $hnid2)json_exit(array('flag'=>0,'msg'=>$str));
	}
}
$ADDTIME = ADDTIME;
$SELF = SELF;
$session_uid   = intval($_SESSION["admuid"]);
$session_pwd   = $_SESSION["admpwd"];
$session_truename = $_SESSION["truename"];
$session_uname  = $_SESSION["admuname"];
$session_path_s = $_SESSION["path_s"];
$session_kind    = $_SESSION["kind"];//adm,crm
$session_title   = $_SESSION["title"];//角色名称
$session_crmkind = $_SESSION["crmkind"];//adm,sq,sh,ht,cw
$session_agentid = intval($_SESSION["agentid"]);
$session_agenttitle  = $_SESSION["agenttitle"];
$session_agentareaid = $_SESSION["agent_areaid"];
$session_sq_sh_bfb = intval($_SESSION["sq_sh_bfb"]);
function mate_echo($row,$kind='') {
	global $_ZEAI;
	if(empty($_ZEAI['mate_diy']))return '';
	$mate_diy = explode(',',$_ZEAI['mate_diy']);
	$mate_age1      = intval($row['mate_age1']);
	$mate_age2      = intval($row['mate_age2']);
	$mate_heigh1    = intval($row['mate_heigh1']);
	$mate_heigh2    = intval($row['mate_heigh2']);
	$mate_pay       = $row['mate_pay'];
	$mate_edu       = $row['mate_edu'];
	$mate_areaid    = $row['mate_areaid'];
	$mate_areatitle = $row['mate_areatitle'];
	$mate_love      = $row['mate_love'];
	$mate_car       = $row['mate_car'];
	$mate_house     = $row['mate_house'];
	$mate_weigh1      = intval($row['mate_weigh1']);
	$mate_weigh2      = intval($row['mate_weigh2']);
	$mate_job         = $row['mate_job'];
	$mate_child       = $row['mate_child'];
	$mate_marrytime   = $row['mate_marrytime'];
	$mate_companykind = $row['mate_companykind'];
	$mate_smoking     = $row['mate_smoking'];
	$mate_drink       = $row['mate_drink'];
	$mate_areaid2     = $row['mate_areaid2'];
	$mate_areatitle2  = $row['mate_areatitle2'];
	$mate_other       = dataIO($row['mate_other'],'out');
	$ifmate = ( !empty($mate_age1) || !empty($mate_age2) || !empty($mate_heigh1) || !empty($mate_heigh2) || !empty($mate_pay) || !empty($mate_edu) || !empty($mate_areaid) || !empty($mate_areatitle) || !empty($mate_love) || !empty($mate_house) || !empty($mate_job) || !empty($mate_child) || !empty($mate_marrytime) || !empty($mate_companykind) || !empty($mate_smoking) || !empty($mate_drink) || !empty($mate_areaid2)  || !empty($mate_areatitle2) || !empty($mate_other)  )?true:false;
	if($ifmate){
		$mate_age       = $mate_age1.','.$mate_age2;
		$mate_age_str   = mateset_out($mate_age1,$mate_age2,'岁');
		$mate_age_str = str_replace("不限","",$mate_age_str);
		$mate_heigh     = $mate_heigh1.','.$mate_heigh2;
		$mate_heigh_str = mateset_out($mate_heigh1,$mate_heigh2,'cm');
		$mate_heigh_str = str_replace("不限","",$mate_heigh_str);
		$mate_weigh     = $mate_weigh1.','.$mate_weigh2;
		$mate_weigh_str = mateset_out($mate_weigh1,$mate_weigh2,'kg');
		$mate_weigh_str = str_replace("不限","",$mate_weigh_str);
		$mate_areaid_str  = (!empty($mate_areatitle))?$mate_areatitle:'';
		$mate_areaid2_str = (!empty($mate_areatitle2))?$mate_areatitle2:'';
		$mate_pay_str   = udata('pay',$mate_pay);
		$mate_edu_str   = udata('edu',$mate_edu);
		$mate_love_str  = udata('love',$mate_love);
		$mate_car_str   = udata('car',$mate_car);
		$mate_house_str = udata('house',$mate_house);
		$mate_job_str         = udata('job',$mate_job);
		$mate_child_str       = udata('child',$mate_child);
		$mate_marrytime_str   = udata('marrytime',$mate_marrytime);
		$mate_companykind_str = udata('companykind',$mate_companykind);
		$mate_smoking_str     = udata('smoking',$mate_smoking);
		$mate_drink_str       = udata('drink',$mate_drink);
		$mate_li_out='';
		if (count($mate_diy) >= 1 && is_array($mate_diy)){
			foreach ($mate_diy as $k=>$V) {
				$ext = mate_diy_par($V,'ext');
				$tmpD = 'mate_'.$V;
				$tmpS = 'mate_'.$V.'_str';
				$mate_data = $$tmpD;
				$mate_str  = $$tmpS;
				if(!empty($mate_data) && $mate_data!='0,0'){
					switch ($ext) {
						case 'checkbox':
							$mate_str_=explode(',',$mate_str);
							$mate_strN=count($mate_str_);
							if($mate_strN>1){
								$matesonli='';
								foreach ($mate_str_ as $ks=>$Vs) {$matesonli.='【'.$Vs.'】';}
								$mate_str = $matesonli;
							}
						break;
						default:break;
					}
					if($kind=='text'){
						$mate_li_out.=$mate_str.',';
					}elseif($kind=='li'){
						$mate_li_out.='<li>'.$mate_str.'</li>';
					}else{
						$mate_li_out.='<li><font>'.mate_diy_par($V).'：</font>'.$mate_str.'</li>';
					}
				}
			}
			if($kind=='text'){
				$mate_li_out = rtrim($mate_li_out,',');
				$mate_li_out .= (!empty($mate_other))?'，其他要求：'.$mate_other:'';
			}elseif($kind=='li'){
				$mate_li_out .= (!empty($mate_other))?'<li>'.$mate_other.'</li>':'';
			}else{
				$mate_li_out .= (!empty($mate_other))?'<li><font>其他要求：</font>'.$mate_other.'</li>':'';
			}
		}
	}
	return $mate_li_out;
}
function getAgentSQL($fldbef='') {
	global $QXARR,$session_agentareaid,$session_agentid;
	$SQL="";
	if($fldbef == 'aloneArea'){
		if(!empty($session_agentareaid) && str_len($session_agentareaid)>1 && !in_array('crm',$QXARR)){
			$areaid = explode(',',$session_agentareaid);
			$m1=$areaid[0];$m2=$areaid[1];$m3=$areaid[2];
			if (ifint($m1) && ifint($m2) && ifint($m3)){
				$areaid = $m1.','.$m2.','.$m3;
			}elseif(ifint($m1) && ifint($m2)){
				$areaid = $m1.','.$m2;
			}elseif(ifint($m1)){
				$areaid = $m1;
			}
			if (!empty($areaid))$SQL = " AND ( areaid LIKE '%".$areaid."%'  ) ";
		}
		return $SQL;
	}
	if(!in_array('crm',$QXARR)){//非超管
		$fldbef=( !empty($fldbef) )?$fldbef.'.':'';
		if(!empty($session_agentareaid) && str_len($session_agentareaid)>1){
			$areaid = explode(',',$session_agentareaid);
			$m1=$areaid[0];$m2=$areaid[1];$m3=$areaid[2];
			if (ifint($m1) && ifint($m2) && ifint($m3)){
				$areaid = $m1.','.$m2.','.$m3;
			}elseif(ifint($m1) && ifint($m2)){
				$areaid = $m1.','.$m2;
			}elseif(ifint($m1)){
				$areaid = $m1;
			}
			if (!empty($areaid))$SQL .= " AND ( ".$fldbef."areaid LIKE '%".$areaid."%'  ) ";
		}
		$SQL.=" AND ".$fldbef."agentid=$session_agentid ";
	}
	return $SQL;
}
function crm_ifcontact($agentid,$admid,$hnid,$hnid2) {
	global $QXARR,$session_uid,$session_agentid;
	if(@in_array('crm',$QXARR))return true;
	if(@in_array('crm_user_contact_my',$QXARR)){
		$ifcontact_my = ($admid==$session_uid || $hnid==$session_uid || $hnid2==$session_uid)?true:false;
	}
	if((  in_array('crm_user_contact',$QXARR) && $agentid==$session_agentid ) || $ifcontact_my){
		return true;
	}
	return false;
}	

function crm_qxflag_title($flag) {
	global $_CRM;
	$ARR=json_decode($_CRM['qxflag'],true);
	if (count($ARR) >= 1 && is_array($ARR)){
		foreach ($ARR as $V){
			if($V['i']==$flag)return '<font style="color:'.$V['c'].'">'.$V['v'].'</font>';
		}
	}else{
		return '';	
	}
}
function crm_arr_title($arr,$id) {
	if (count($arr) >= 1 && is_array($arr)){
		foreach ($arr as $V){if($V['i']==$id)return '<font style="color:'.$V['c'].'">'.$V['v'].'</font>';}
	}else{return '';}
}
function ifCrmAgentArea($agentid,$areaid) {
	global $QXARR,$session_agentareaid,$session_agentid;
	$ret=true;
	if(!in_array('crm',$QXARR)){
		if(!empty($session_agentareaid) && str_len($session_agentareaid)>5){
			$areaidS = explode(',',$session_agentareaid);
			$m1=$areaidS[0];$m2=$areaidS[1];$m3=$areaidS[2];
			if (ifint($m1) && ifint($m2) && ifint($m3)){
				$areaidS = $m1.','.$m2.','.$m3;
			}elseif(ifint($m1) && ifint($m2)){
				$areaidS = $m1.','.$m2;
			}elseif(ifint($m1)){
				$areaidS = $m1;
			}
			if(!strstr($areaid,$areaidS))$ret=false;//暂无【跨地区操作】权限
		}
		if($agentid!=$session_agentid)$ret=false;//暂无【跨门店操作】权限
	}
	return $ret;
}
function cache_mod_config($arr,$name,$var){
	$intarr = array("tjdiy_num","Mnavbtmkind","gift_dhloveb_num","ifViewPushsex","rz_mobile3","rz_faceidcard","rz_price","reg_style","YKviewU","hidephoto","hidedata","hidedel","hidedel_rmb","hideprivacy","chatContact_bfb_num","viewhomepage_bfb_num","hi_bfb_num","iMarquee","pagesize","HB_refundtime","iModuleU_bigmore","iModuleU_num","iModuleU","iModuleU_pc_num","iModuleU_pc","loveBrate","gzflag2","wx_gzh_getphoto_s","email_debug","email_port","mob_mbkind","pc_mbkind","chat_level","contact_level","limit","push_index","cz_minnum","sms_yzmnum","upMaxMB","upVMaxMB","ifwaterimg","admPageSize","admLimit","grade","ifdefault","reg_flag","reg_kind","reg_3login_qq","reg_3login_wx","reg_loveb","reg_grade","reg_force_wx");//,"reg1_3login_bd_sms"
	if (!is_array($arr))return false;
	foreach($arr as $k=>$v){
		if(in_array($k,$intarr)){
			$Wstr .= '$'.$var.'[\''.$k.'\']='.$v.";".PHP_EOL;
		}else{
			$Wstr .= '$'.$var.'[\''.$k.'\']=\''.$v.'\''.";".PHP_EOL;
		}
	}
	@wirte_file(ZEAI.'cache/'.$name.'.php',"<?php /*www.zeai.cn ZEAI6.0高速缓存系统*/".PHP_EOL.$Wstr."?>");
}
?>
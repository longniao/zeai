 <?php 
/***************************************************
作者: www.zeai.cn 郭余林　QQ:797311 (supdes)
***************************************************/
require_once 'init.php';
if (ini_get('session.auto_start') == 0)@session_start();
require_once ZEAI.'sub/conn.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'cache/config_reg.php';
require_once ZEAI.'cache/config_vip.php';
require_once ZEAI.'cache/config_index.php';
require_once ZEAI.'cache/config_wxgzh.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_sms.php';
require_once ZEAI.'cache/config_tjdiy.php';
require_once ZEAI.'cache/config_shop.php';
if (!empty($submitok)){
	if (!ifint($uu) || str_len($pp) != 32)exit;
	$rowadm=$db->ROW(__TBL_ADMIN__,"username,kind,agentid,agenttitle","id=".$uu." AND password='$pp'",'name');
	if($rowadm){
		$session_uname      = $rowadm['username'];
		$session_kind       = $rowadm['kind'];
		$session_agentid    = $rowadm['agentid'];
		$session_agenttitle = $rowadm['agenttitle'];
	}else{
		json_exit(array(flag=>0,'msg'=>'forbidden'));
	}
}
$C = array();
$urole = json_decode($_ZEAI['urole'],true);
switch ($submitok) {
	case 'cache_shopnav_mod':
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'请输入【导航id】'));
		if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【导航名称】'));
		if(empty($url))json_exit(array('flag'=>0,'msg'=>'请输入【导航超链接】'));
		$title=dataIO($title,'in',8);$url=dataIO($url,'in',500);
		$ARR=($t=='navbtm_mod')?json_decode($_SHOP['navbtm'],true):json_decode($_SHOP['navtop'],true);
		if (count($ARR) >= 1 && is_array($ARR)){
			$data_path_s=nav_info($id,'img',$ARR);
			if(empty($path_s) && !empty($data_path_s)){
				up_send_admindel($data_path_s);
				$path_s='';
			}elseif(!empty($path_s) && empty($data_path_s)){
				adm_pic_reTmpDir_send($path_s,'img');
				$path_s = str_replace('tmp','img',$path_s);
			}elseif(!empty($path_s) && !empty($data_path_s)){
				if($path_s != $data_path_s){
					@up_send_admindel($data_path_s);
					adm_pic_reTmpDir_send($path_s,'img');
					$path_s = str_replace('tmp','img',$path_s);
				}
			}
			if($t=='navbtm_mod'){
				$data_path_s2=nav_info($id,'img2',$ARR);
				if(empty($path_s2) && !empty($data_path_s2)){
					up_send_admindel($data_path_s2);
					$path_s2='';
				}elseif(!empty($path_s2) && empty($data_path_s2)){
					adm_pic_reTmpDir_send($path_s2,'img');
					$path_s2 = str_replace('tmp','img',$path_s2);
				}elseif(!empty($path_s2) && !empty($data_path_s2)){
					if($path_s2 != $data_path_s2){
						@up_send_admindel($data_path_s2);
						adm_pic_reTmpDir_send($path_s2,'img');
						$path_s2 = str_replace('tmp','img',$path_s2);
					}
				}
			}
			foreach ($ARR as $V) {
				$newarr=array();
				if($V['i']==$id){
					$newarr['i']=$id;
					$newarr['t']=urlencode(trimm($title));
					$newarr['img']=$path_s;
					$newarr['url']=urlencode(trimm($url));
					$newarr['f']=$V['f'];
					if($t=='navbtm_mod'){
						$newarr['var']=urlencode(trimm($var));
						$newarr['img2']=$path_s2;
						$newarr['url2']=urlencode(trimm($url2));
					}
					$out_t=$newarr['t'];
					$out_img=$path_s;
				}else{
					$newarr=$V;
				}
				$arrLI[]=$newarr;
			}
			if($t=='navbtm_mod'){
				$_SHOP['navbtm'] = encode_json($arrLI);
			}else{
				$_SHOP['navtop'] = encode_json($arrLI);
			}
			cache_mod_config($_SHOP,'config_shop','_SHOP');
			AddLog('【商家导航模块】->【'.$title.'】修改');
		}
		json_exit(array('flag'=>1,'msg'=>'操作成功！','out_t'=>$out_t,'out_img'=>$out_img));
	break;
	case 'cache_shopnav':
		if(empty($shoptitle))json_exit(array('flag'=>0,'msg'=>'请输入【商家模块名称】'));
		$ARR=json_decode($_SHOP['navtop'],true);
		if (count($ARR) >= 1 && is_array($ARR) && !empty($navtop_px)){
			$navtop_px=explode(',',$navtop_px);
			foreach ($navtop_px as $i) {
				$a=array();
				$a['i']= nav_info($i,'i',$ARR);
				$a['t']= nav_info($i,'t',$ARR);
				$a['img']= nav_info($i,'img',$ARR);
				$a['url']= nav_info($i,'url',$ARR);
				$b='navtop_f'.$i;$a['f']=intval($$b);
				$arrLI[]=$a;
			}
			$_SHOP['navtop'] = encode_json($arrLI);
		}
		$ARR=json_decode($_SHOP['navbtm'],true);
		if (count($ARR) >= 1 && is_array($ARR) && !empty($navbtm_px)){
			$navbtm_px=explode(',',$navbtm_px);
			$arrLI=array();
			foreach ($navbtm_px as $i) {
				$a=array();
				$a['i']= nav_info($i,'i',$ARR);
				$a['t']= nav_info($i,'t',$ARR);
				$a['img']= nav_info($i,'img',$ARR);
				$a['img2']= nav_info($i,'img2',$ARR);
				$a['url']= nav_info($i,'url',$ARR);
				$a['url2']= nav_info($i,'url2',$ARR);
				$a['var']= nav_info($i,'var',$ARR);
				$b='navbtm_f'.$i;
				$a['f']=intval($$b);
				$arrLI[]=$a;
			}
			$_SHOP['navbtm'] = encode_json($arrLI);
		}
		$title   = dataIO($shoptitle,'in',100);
		$regflag = (empty($shopregflag))?0:1;
		$regifpay = ($regifpay==1)?1:0;
		$orderkind = ($orderkind==2)?2:1;
		$_SHOP['title']  =$title;
		$_SHOP['regflag']=$regflag;
		$_SHOP['regifpay']=$regifpay;
		$_SHOP['orderkind']=$orderkind;
		$_SHOP['kindarr']=$jsonstr;
		$_SHOP['tx_num_list']=(empty($tx_num_list))?'100,500,1000,2000':dataIO($tx_num_list,'in');
		$qrshday=intval($qrshday);$tkday=intval($tkday);$thday=intval($thday);$hdday=intval($hdday);
		$_SHOP['qrshday']=($qrshday<1 || $qrshday>365)?7:$qrshday;
		$_SHOP['tkday']=($tkday<1 || $tkday>365)?3:$tkday;
		$_SHOP['thday']=($thday<1 || $thday>365)?3:$thday;
		$_SHOP['hdday']=($hdday<1 || $hdday>365)?3:$hdday;
		//banner
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){ 
			for($i=1;$i<=3;$i++) {
				$FILES = $_FILES["pic".$i];
				if (!empty($FILES)){
					$dbpicname = setphotodbname('banner',$FILES['tmp_name'],'shop_');
					if ($dbpicname){
						if (!up_send($FILES,$dbpicname,$_UP['ifwaterimg'],$_UP['upMsize'],'1920*1000'))continue;
						$_s = setpath_s($dbpicname);
						switch ($i) {
							case 1:$_SHOP['mBN_path1_s']  = $_s;break;
							case 2:$_SHOP['mBN_path2_s']  = $_s;break;
							case 3:$_SHOP['mBN_path3_s']  = $_s;break;
						}
					}
				}
			}
		}
		for($i=1;$i<=3;$i++) {
			$path_url = 'path'.$i.'_url';
			switch ($i) {
				case 1:$_SHOP['mBN_path1_url']= $$path_url;break;
				case 2:$_SHOP['mBN_path2_url']= $$path_url;break;
				case 3:$_SHOP['mBN_path3_url']= $$path_url;break;
			}
		}
		/****logo****/
		$FILES = $_FILES["pic0"];
		if (!empty($FILES)){
			$dbpicname = setphotodbname('banner',$FILES['tmp_name'],'shop_');
			if ($dbpicname){
				//if (!up_send($FILES,$dbpicname,0,$_UP['upBsize']))continue;
				up_send($FILES,$dbpicname,0,$_UP['upBsize']);
				$_SHOP['logo'] = $dbpicname;
			}
		}
		/****logo****/
		$FILES = $_FILES["pic4"];
		if (!empty($FILES)){
			$dbpicname = setphotodbname('banner',$FILES['tmp_name'],'shop_');
			if ($dbpicname){
				//if (!up_send($FILES,$dbpicname,0,'1200*1200'))continue;
				up_send($FILES,$dbpicname,0,'1200*1200');
				$_SHOP['my_banner'] = $dbpicname;
			}
		}
		$_SHOP['my_banner_url'] = dataIO($my_banner_url,'in',200);
		cache_mod_config($_SHOP,'config_shop','_SHOP');	
		AddLog('【商家导航模块】->修改');
		json_exit(array('flag'=>1,'msg'=>'操作成功！'));
	break;
	case 'cache_navdiy_mod':
		if(!ifint($id))json_exit(array('flag'=>0,'msg'=>'请输入【导航id】'));
		if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【导航名称】'));
		if(empty($url))json_exit(array('flag'=>0,'msg'=>'请输入【导航超链接】'));
		$title=dataIO($title,'in',8);$url=dataIO($url,'in',500);
		$ARR=json_decode($_ZEAI['navdiy'],true);
		if (count($ARR) >= 1 && is_array($ARR)){
			$data_path_s=nav_info($id,'img');
			if(empty($path_s) && !empty($data_path_s)){
				up_send_admindel($data_path_s);
				$path_s='';
			}elseif(!empty($path_s) && empty($data_path_s)){
				adm_pic_reTmpDir_send($path_s,'img');
				$path_s = str_replace('tmp','img',$path_s);
			}elseif(!empty($path_s) && !empty($data_path_s)){
				if($path_s != $data_path_s){
					@up_send_admindel($data_path_s);
					adm_pic_reTmpDir_send($path_s,'img');
					$path_s = str_replace('tmp','img',$path_s);
				}
			}
			foreach ($ARR as $V) {
				$newarr=array();
				if($V['i']==$id){
					$newarr['i']=$id;
					$newarr['t']=urlencode(trimm($title));
					$newarr['img']=$path_s;
					$newarr['url']=urlencode(trimm($url));
					$newarr['url2']=urlencode(trimm($url2));
					$newarr['var']=urlencode(trimm($var));
					$newarr['f']=$V['f'];
					$out_t=$newarr['t'];
					$out_img=$path_s;
				}else{
					$newarr=$V;
				}
				$arrLI[]=$newarr;
			}
			$_ZEAI['navdiy'] = encode_json($arrLI);
			cache_mod_config($_ZEAI,'config','_ZEAI');
			AddLog('【前台导航模块】->【'.$title.'】修改');
		}
		json_exit(array('flag'=>1,'msg'=>'操作成功！','out_t'=>$out_t,'out_img'=>$out_img));
	break;
	case 'cache_rz':
		if (empty($rz_appId) )json_exit(array('flag'=>0,'msg'=>'请输入【appId】','focus'=>'rz_appId'));
		if (empty($rz_appSecurity) )json_exit(array('flag'=>0,'msg'=>'请输入【appSecurity】','focus'=>'rz_appSecurity'));
		$rz_data = (@is_array($rz_data))?implode(',',$rz_data):'';
		if (empty($rz_data) )json_exit(array('flag'=>0,'msg'=>'【认证项目】至少选一项','focus'=>'rz_data'));
		$_SMS['rz_appId']       = trimm(dataIO($rz_appId,'in'),32);
		$_SMS['rz_appSecurity'] = trimm(dataIO($rz_appSecurity,'in'),32);
		$_SMS['rz_mobile3']     = ($rz_mobile3==1)?1:0;
		$_SMS['rz_price']       = floatval($rz_price);
		cache_mod_config($_SMS,'config_sms','_SMS');
		$_ZEAI['rz_data']      = $rz_data;
		$_ZEAI['rz_data_px']   = $rz_data_px;
		cache_mod_config($_ZEAI,'config','_ZEAI');
		AddLog('【基础设置】->【认证设置】修改');
		json_exit(array('flag'=>1,'msg'=>'更新成功！'));
	break;
	case 'cache_mate_diy':
		$mate_diy = (@is_array($mate_diy))?implode(',',$mate_diy):'';
		if (empty($mate_diy))json_exit(array('flag'=>0,'msg'=>'【项目】至少选一项'));
		$_ZEAI['mate_diy']      = $mate_diy;
		$_ZEAI['mate_diy_px']   = $mate_diy_px;
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'更新成功！'));
	break;
	case 'cache_TG_set'://6.3
		if (empty($navtitle) )json_exit(array('flag'=>0,'msg'=>'请输入【推广模块名称	】','focus'=>'navtitle'));
		if (empty($tg_text) )json_exit(array('flag'=>0,'msg'=>'请输入【通用推广文本	】','focus'=>'tg_text'));
		$navtitle=(empty($navtitle))?'推广中心':$navtitle;
		$tgytitle=(empty($tgytitle))?'红娘':$tgytitle;
		$TG['navtitle']     = trimhtml(dataIO($navtitle,'in'));
		$TG['tgytitle']     = trimhtml(dataIO($tgytitle,'in'));
		$TG['regkind']      = ($regkind == 2 || $regkind == 1)?$regkind:2;
		$TG['regflag']      = ($regflag==1)?1:0;
		$TG['active_price'] = abs(floatval($active_price));
		$TG['openvip']      = ($openvip==1)?1:0;
		$TG['reward_tj']    = (@is_array($reward_tj))?implode(',',$reward_tj):'';
		$TG['force_subscribe'] = ($force_subscribe==1)?1:0;
		$TG['force_weixin'] = ($force_weixin==1)?1:0;

		$reward_tj_bfb = ($reward_tj_bfb>100)?100:$reward_tj_bfb;
		$TG['reward_tj_bfb']= abs(intval($reward_tj_bfb));
		
		$TG['reward_flag']    = ($reward_flag==1)?1:0;
		$TG['company_switch'] = ($company_switch==1)?1:0;
		$TG['company_ifsh']   = ($company_ifsh==1)?1:0;
		//微信公众号推广海报
		$FILES = $_FILES["wxbgpic"];
		$extname = getpicextname($FILES['tmp_name']);
		if ($extname == 'png' || $extname == 'jpg' || $extname == 'gif'){
			$dbpicname = 'p/img/wxbgpic.'.$extname;
			@up_send($FILES,$dbpicname,0,'1200*1200');
			$TG['wxbgpic'] = $dbpicname;
		}else{
			$TG['wxbgpic'] = $wxbgpic_;
		}
		$TG['wxhbT']  = (!empty($wxhbT))?dataIO($wxhbT,'in'):'';
		//WAP/手机端推广海报
		$FILES = $_FILES["wapbgpic"];
		$extname = getpicextname($FILES['tmp_name']);
		if ($extname == 'png' || $extname == 'jpg' || $extname == 'gif'){
			$dbpicname = 'p/img/wapbgpic.'.$extname;
			@up_send($FILES,$dbpicname,0,'1200*1200');
			$TG['wapbgpic'] = $dbpicname;
		}else{
			$TG['wapbgpic'] = $wapbgpic_;
		}

		$TG['tg_text']  = (!empty($tg_text))?dataIO($tg_text,'in'):'';
		$TG['wxshareT'] = (!empty($wxshareT))?dataIO($wxshareT,'in'):'';
		$TG['wxshareC'] = (!empty($wxshareC))?dataIO($wxshareC,'in'):'';
		$TG['wx_gzh_welcome'] = (!empty($wx_gzh_welcome))?dataIO($wx_gzh_welcome,'in'):'';
		
		$_REG['TG_set'] = encode_json($TG);
		cache_mod_config($_REG,'config_reg','_REG');
		AddLog('【推广全局设置】->修改');
		json_exit(array('flag'=>1,'msg'=>'更新成功！'));
	break;
	case 'cache_nav':
		//$navlist = (is_array($nav))?implode(",",$nav):'';
		$_ZEAI['nav'] = encode_json($nav);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		/****index****/
		$_INDEX['iModuleU'] = ($iModuleU==2)?2:1;
		$iModuleU_num = abs(intval($iModuleU_num));
		$_INDEX['iModuleU_num'] = ($iModuleU_num>20 || $iModuleU_num==0)?6:$iModuleU_num;
		//
		$_INDEX['iModuleU_pc'] = ($iModuleU_pc==2)?2:1;
		$iModuleU_pc_num = abs(intval($iModuleU_pc_num));
		$_INDEX['iModuleU_pc_num'] = ($iModuleU_pc_num>40 || $iModuleU_pc_num==0)?10:$iModuleU_pc_num;
		$_INDEX['waterfall_photo'] = (empty($waterfall_photo))?'m':$waterfall_photo;
		cache_mod_config($_INDEX,'config_index','_INDEX');	
		AddLog('【基础设置】->【导航/模块】修改');
		//
		$arr=array();
		for($i=1;$i<=$_TJDIY['tjdiy_num'];$i++) {
			$T='tjtitle'.$i;
			$P='tjpar'.$i;
			$title=$$T;$par=$$P;
			$a=array();
			if(!empty($title)){
				$a['id']   =$i;
				$a['title']=$title;
				$a['par']  =$par;
				$arr[]=$a;
			}
		}
		$_TJDIY['tjdiy']=encode_json($arr);
		cache_mod_config($_TJDIY,'config_tjdiy','_TJDIY');	
		//
		$arr=array();
		for($i=1;$i<=5;$i++) {
			$T='title'.$i;
			$V='var'.$i;
			$U='url'.$i;
			$P1='path'.$i.'_1';
			$P2='path'.$i.'_2';
			$title=$$T;$var=$$V;$url=$$U;$path1=$$P1;$path2=$$P2;
			//if(!empty($title) && !empty($url) && !empty($path1) && !empty($path2)){
				$FILES1 = $_FILES["pic".$i.'_1'];
				if (!empty($FILES1)){
					$path1 = setphotodbname('img',$FILES1['tmp_name'],$i);
					up_send($FILES1,$path1,0,'150*150');
				}
				$FILES2 = $_FILES["pic".$i.'_2'];
				if (!empty($FILES2)){
					$path2 = setphotodbname('img',$FILES2['tmp_name'],$i);
					up_send($FILES2,$path2,0,'150*150');
				}
				$a=array();
				$a['id']   =$i;
				$a['title']=urlencode(trimm($title));
				$a['url']  =urlencode(trimm($url));
				$a['var']  =urlencode(trimm($var));
				$a['path1']=$path1;
				$a['path2']=$path2;
				$arr[]=$a;
			//}
		}
		//navdiy
		$_ZEAI['navkind'] = ($navkind==2)?2:1;
		$ARR=json_decode($_ZEAI['navdiy'],true);
		if (count($ARR) >= 1 && is_array($ARR) && !empty($navdiy_px)){
			$navdiy_px=explode(',',$navdiy_px);
			foreach ($navdiy_px as $i) {
				$a=array();
				$a['i']= nav_info($i,'i');
				$a['t']= nav_info($i,'t');
				$a['img']= nav_info($i,'img');
				$a['url']= nav_info($i,'url');
				$a['url2']= nav_info($i,'url2');
				$a['var']= nav_info($i,'var');
				$b='navdiy_f'.$i;
				$a['f']=intval($$b);
				$arrLI[]=$a;
			}
			$_ZEAI['navdiy'] = encode_json($arrLI);
		}
		
		$_ZEAI['Mnavbtmkind']=($Mnavbtmkind==2)?2:1;
		$_ZEAI['Mnavbtm']=encode_json($arr);
		cache_mod_config($_ZEAI,'config','_ZEAI');	
		AddLog('【基础设置】->【导航/模块】手机底部导航修改');
		json_exit(array('flag'=>1,'msg'=>'操作成功！'));
	break;
	case 'Mnavbtm_icoDel':
		if(!empty($iarrstr)){
			$iarr=explode('_',$iarrstr);
			$i=$iarr[0];$n=$iarr[1];
			$path = Mnavbtm_info($i,'path'.$n);
			@up_send_admindel($path);
			$Mnavbtm = json_decode($_ZEAI['Mnavbtm'],true);
			foreach ($Mnavbtm as $V) {
				if($V['id']==$i)$V['path'.$n]='';
				$newarr[]=$V;
			}
			$_ZEAI['Mnavbtm']=encode_json($newarr);
			cache_mod_config($_ZEAI,'config','_ZEAI');	
			AddLog('【基础设置】->【导航/模块】手机底部导航图标删除');
		}
		json_exit(array('flag'=>1,'msg'=>'操作成功！'));
	break;
	case 'ajax_mod_vip_safetips'://新版
		$_VIP['safetips'] = dataIO($safetips,'in',2000);
		cache_mod_config($_VIP,'config_vip','_VIP');
		json_exit(array('flag'=>1,'msg'=>'更新成功！'));
	break;
	case 'cache_config_urole_flag':
		$id   = intval($id);
		$flag = ($flag==1)?1:0;
		$db->query("UPDATE ".__TBL_ROLE__." SET flag=".$flag." WHERE id=".$id);
		$rt = $db->query("SELECT grade AS g,title AS t,ifdefault AS d,if2,flag AS f FROM ".__TBL_ROLE__." WHERE kind=1 ORDER BY id");
		while($tmprows = $db->fetch_array($rt,'name')){$arr[]=$tmprows;}
		$_ZEAI['urole'] = encode_json($arr);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		//
		$row2 = $db->ROW(__TBL_ROLE__,"title,grade,if2","id=".$id,'num');$title= $row2[0];$grade= $row2[1];$if2= $row2[2];
		$flag_str=($flag==1)?'开启':'隐藏';
		AddLog('修改会员组【'.$title.'，权重：'.$grade.'，时长：'.get_if2_title($if2).'】“VIP升级列表显示”->'.$flag_str);
		//
		json_exit(array('flag'=>1,'msg'=>'设置/更新成功'));
	break;
	case 'cache_config_vip'://新版
		if (empty($title) )json_exit(array('flag'=>0,'msg'=>'请输入会员组名称','focus'=>'title'));
		if (str_len($title) >20)json_exit(array('flag'=>0,'msg'=>'亲，会员组名称【'.$title.'】这么长有意义么？ 请不要超过20字节','focus'=>'title'));
		$title = dataIO($title,'in',50);
		if (!ifint($grade) )json_exit(array('flag'=>0,'msg'=>'请输入权重等级 1~10','focus'=>'grade'));
		if ($grade > 10)$grade = 10;
		if ($grade == 0)$grade = 1;
		if (!ifint($if2) )json_exit(array('flag'=>0,'msg'=>'请选择VIP有效期限1个月,3个月。。。','focus'=>'if2'));
		if ($if2 > 999)$if2 = 999;
		$sj_rmb1=abs(intval($sj_rmb1));
		$sj_rmb2=abs(intval($sj_rmb2));
		if (!ifint($sj_rmb1) && $grade>1)json_exit(array('flag'=>0,'msg'=>'请输入服务价格（正整数）【男】','focus'=>'sj_rmb1'));
		if (!ifint($sj_rmb2) && $grade>1)json_exit(array('flag'=>0,'msg'=>'请输入服务价格（正整数）【女】','focus'=>'sj_rmb1'));
		//
		$switch_Smode       = ($switch_Smode == 2)?2:1;
		$switch_sh_moddata  = ($switch_sh_moddata == 1)?1:0;
		$switch_sh_photo    = ($switch_sh_photo == 1)?1:0;
		$switch_sh_photom   = ($switch_sh_photom == 1)?1:0;
		$switch_sh_video    = ($switch_sh_video == 1)?1:0;
		//
		$sj_rmb1=abs(intval($sj_rmb1));
		$sj_rmb2=abs(intval($sj_rmb2));
		$loveb_buy = ($loveb_buy>1 || !is_numeric($loveb_buy))?1:$loveb_buy;$loveb_buy = round($loveb_buy,2);
		$contact_daylooknum = abs(intval($contact_daylooknum));
		$contact_loveb      = abs(intval($contact_loveb));
		$chat_daylooknum    = abs(intval($chat_daylooknum));
		$chat_loveb   = abs(intval($chat_loveb));
		$photo_num    = abs(intval($photo_num));
		$video_num    = abs(intval($video_num));
		$vipC         = dataIO($vipC,'in',2000);
		$chat_duifangfree = ($chat_duifangfree == 1)?1:0;
		$sj_loveb     = abs(intval($sj_loveb));
		$trend_addflag = ($trend_addflag == 1)?1:0;
		$trend_bbsflag = ($trend_bbsflag == 1)?1:0;
		$viewlist      = ($viewlist == 1)?1:0;
		$qianxian_num  = abs(intval($qianxian_num));
		$meet_num      = abs(intval($meet_num));
		//
		$switch = json_decode($_ZEAI['switch'],true);
		$switch['Smode']['g_'.$grade]    = $switch_Smode;
		$switch['sh']['moddata_'.$grade] = $switch_sh_moddata;
		$switch['sh']['photom_'.$grade]  = $switch_sh_photom;
		$switch['sh']['photo_'.$grade]   = $switch_sh_photo;
		$switch['sh']['video_'.$grade]   = $switch_sh_video;
		//
		$sj_rmb1ARR            = json_decode($_VIP['sj_rmb1'],true);
		$sj_rmb2ARR            = json_decode($_VIP['sj_rmb2'],true);
		$contact_lovebARR      = json_decode($_VIP['contact_loveb'],true);
		$contact_daylooknumARR = json_decode($_VIP['contact_daylooknum'],true);
		$chat_lovebARR         = json_decode($_VIP['chat_loveb'],true);
		$chat_daylooknumARR    = json_decode($_VIP['chat_daylooknum'],true);
		$loveb_buyARR          = json_decode($_VIP['loveb_buy'],true);
		$photo_numARR          = json_decode($_VIP['photo_num'],true);
		$video_numARR          = json_decode($_VIP['video_num'],true);
		$vipCARR               = json_decode($_VIP['vipC'],true);
		$chat_duifangfreeARR   = json_decode($_VIP['chat_duifangfree'],true);
		$sj_lovebARR           = json_decode($_VIP['sj_loveb'],true);
		$trend_addflagARR      = json_decode($_VIP['trend_addflag'],true);
		$trend_bbsflagARR      = json_decode($_VIP['trend_bbsflag'],true);
		$viewlistARR           = json_decode($_VIP['viewlist'],true);
		$qianxian_numARR = json_decode($_VIP['qianxian_num'],true);
		$meet_numARR     = json_decode($_VIP['meet_num'],true);
		$sj_rmb1ARR[$grade.'_'.$if2]    = $sj_rmb1;
		$sj_rmb2ARR[$grade.'_'.$if2]    = $sj_rmb2;
		$contact_lovebARR[$grade]      = $contact_loveb;
		$contact_daylooknumARR[$grade] = $contact_daylooknum;
		$chat_lovebARR[$grade]         = $chat_loveb;
		$chat_daylooknumARR[$grade]    = $chat_daylooknum;
		$loveb_buyARR[$grade]          = $loveb_buy;
		$photo_numARR[$grade]          = $photo_num;
		$video_numARR[$grade]          = $video_num;
		$sj_lovebARR[$grade]           = $sj_loveb;
		$trend_addflagARR[$grade]      = $trend_addflag;
		$trend_bbsflagARR[$grade]      = $trend_bbsflag;
		$vipCARR[$grade]               = $vipC;
		$chat_duifangfreeARR[$grade]   = $chat_duifangfree;
		$viewlistARR[$grade]           = $viewlist;
		$qianxian_numARR[$grade]       = $qianxian_num;
		$meet_numARR[$grade]           = $meet_num;
		$_VIP['sj_rmb1']            = encode_json($sj_rmb1ARR);
		$_VIP['sj_rmb2']            = encode_json($sj_rmb2ARR);
		$_VIP['contact_loveb']      = encode_json($contact_lovebARR);
		$_VIP['contact_daylooknum'] = encode_json($contact_daylooknumARR);
		$_VIP['chat_loveb']         = encode_json($chat_lovebARR);
		$_VIP['chat_daylooknum']    = encode_json($chat_daylooknumARR);
		$_VIP['loveb_buy']          = encode_json($loveb_buyARR);
		$_VIP['photo_num']          = encode_json($photo_numARR);
		$_VIP['video_num']          = encode_json($video_numARR);
		$_VIP['vipC']               = encode_json($vipCARR);
		$_VIP['chat_duifangfree']   = encode_json($chat_duifangfreeARR);
		$_VIP['sj_loveb']           = encode_json($sj_lovebARR);
		$_VIP['trend_addflag']      = encode_json($trend_addflagARR);
		$_VIP['trend_bbsflag']      = encode_json($trend_bbsflagARR);
		$_VIP['viewlist']           = encode_json($viewlistARR);
		$_VIP['qianxian_num']           = encode_json($qianxian_numARR);
		$_VIP['meet_num']           = encode_json($meet_numARR);
		cache_mod_config($_VIP,'config_vip','_VIP');
		$_ZEAI['switch'] = encode_json($switch);
		$rt = $db->query("SELECT grade AS g,title AS t,ifdefault AS d,if2,flag AS f FROM ".__TBL_ROLE__." WHERE kind=1 ORDER BY id");
		while($tmprows = $db->fetch_array($rt,'name')){$arr[]=$tmprows;}
		$_ZEAI['urole'] = encode_json($arr);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'更新成功！'));
	break;
	case 'bannerDel':
		if(!ifint($i))json_exit(array('flag'=>0,'msg'=>'zeai_error_i'));
		$MPbn = ($i>3)?'pc':'m';
		switch ($i) {
			case 1:$endi = 1;break;
			case 2:$endi = 2;break;
			case 3:$endi = 3;break;
			case 4:$endi = 1;break;
			case 5:$endi = 2;break;
			case 6:$endi = 3;break;
		}
		$path_s = $MPbn.'BN_path'.$endi.'_s';
		$_s = $_INDEX[$path_s];$_b=smb($_s,'b');
		@up_send_admindel($_s.'|'.$_b);
		$_INDEX[$MPbn.'BN_path'.$endi.'_s'] = '';
		cache_mod_config($_INDEX,'config_index','_INDEX');
		json_exit(array('flag'=>1,'msg'=>'删除成功！'));
	break;
	case 'shopbannerDel':
		if(!ifint($i))json_exit(array('flag'=>0,'msg'=>'zeai_error_i'));
		switch ($i) {
			case 1:$endi = 1;break;
			case 2:$endi = 2;break;
			case 3:$endi = 3;break;
			case 4:$endi = 1;break;
			case 5:$endi = 2;break;
			case 6:$endi = 3;break;
		}
		$path_s = 'mBN_path'.$endi.'_s';
		$_s = $_SHOP[$path_s];$_b=smb($_s,'b');
		@up_send_admindel($_s.'|'.$_b);
		$_SHOP['mBN_path'.$endi.'_s'] = '';
		cache_mod_config($_SHOP,'config_shop','_SHOP');
		json_exit(array('flag'=>1,'msg'=>'删除成功！'));
	break;
	case 'pplimgDel':
		if(!ifint($i))json_exit(array('flag'=>0,'msg'=>'zeai_error_i'));
		$zeai_pplAD = json_decode($_INDEX['zeai_pplAD'],true);
		$zeai_pplAD_num=count($zeai_pplAD);
		if($zeai_pplAD_num>0){
			for($j=1;$j<=$zeai_pplAD_num;$j++) {
				$AD=Zeai_pplAD($j,'i');
				if(!empty($AD)){
					$img = $AD['img'];
					if($j==$i && !empty($img)){@up_send_admindel($img);$img='';	}
					$newB[]=array('i'=>$j,'img'=>$img,'url'=>$AD['url'],'p'=>$AD['p']);
				}else{
					$newB[]=array('i'=>$j,'img'=>'','url'=>'','p'=>'');
				}
			}
			$_INDEX['zeai_pplAD']=encode_json($newB);
			cache_mod_config($_INDEX,'config_index','_INDEX');
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功！'));
	break;
	
	case 'cache_banner':
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){ 
			for($i=1;$i<=6;$i++) {
				$FILES = $_FILES["pic".$i];
				if (!empty($FILES)){
					$dbpicname = setphotodbname('banner',$FILES['tmp_name'],'bn_');
					if ($dbpicname){
						if (!up_send($FILES,$dbpicname,$_UP['ifwaterimg'],$_UP['upMsize'],'1920*1000'))continue;
						$_s = setpath_s($dbpicname);
						$tmppicurl = $_ZEAI['up2']."/".$_s;
						if (!ifpic($tmppicurl))continue;
						switch ($i) {
							case 1:$_INDEX['mBN_path1_s']  = $_s;break;
							case 2:$_INDEX['mBN_path2_s']  = $_s;break;
							case 3:$_INDEX['mBN_path3_s']  = $_s;break;
							case 4:$_INDEX['pcBN_path1_s'] = $_s;break;
							case 5:$_INDEX['pcBN_path2_s'] = $_s;break;
							case 6:$_INDEX['pcBN_path3_s'] = $_s;break;
						}
					}
				}
			}
		}
		for($i=1;$i<=6;$i++) {
			$path_url = 'path'.$i.'_url';
			switch ($i) {
				case 1:$_INDEX['mBN_path1_url']= $$path_url;break;
				case 2:$_INDEX['mBN_path2_url']= $$path_url;break;
				case 3:$_INDEX['mBN_path3_url']= $$path_url;break;
				case 4:$_INDEX['pcBN_path1_url']= $$path_url;break;
				case 5:$_INDEX['pcBN_path2_url']= $$path_url;break;
				case 6:$_INDEX['pcBN_path3_url']= $$path_url;break;
			}
		}
		//
		$zeai_pplAD = json_decode($_INDEX['zeai_pplAD'],true);
		$zeai_pplAD_num=count($zeai_pplAD);
		if($zeai_pplAD_num>0){
			for($i=1;$i<=$zeai_pplAD_num;$i++) {
				$FILES = $_FILES["pplpic".$i];
				if (!empty($FILES['tmp_name'])){
					$dbpicname = setphotodbname('banner',$FILES['tmp_name'],'bn_');
					up_send($FILES,$dbpicname,$_UP['ifwaterimg'],'1920*1000');
					$img = $dbpicname;
				}else{
					$AD=Zeai_pplAD($i,'i');
					$img = $AD['img'];
				}
				$url='pplurl'.$i;$url=urlencode($$url);
				$p  ='pplp'.$i;$p=$$p;
				$newB[]=array('i'=>$i,'img'=>$img,'url'=>$url,'p'=>$p);
			}
			$_INDEX['zeai_pplAD']=encode_json($newB);
		}
		cache_mod_config($_INDEX,'config_index','_INDEX');
		AddLog('【基础设置】->【首页广告】修改');
		json_exit(array('flag'=>1,'msg'=>'更新成功！','jumpurl'=>'var.php?t=6'));
	break;
	case 'cache_config_roleinfo'://老的不用了
		$rt = $db->query("SELECT grade AS g,title AS t,ifdefault AS d,flag AS f FROM ".__TBL_ROLE__." WHERE kind=1 ORDER BY id");
		while($tmprows = $db->fetch_array($rt,'name')){$arr[]=$tmprows;}
		$_ZEAI['urole'] = encode_json($arr);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'更新成功！'));
	break;
	case 'cache_robot':
		$robot=json_decode($_ZEAI['robot'],true);
		$robot['flag']['hi'] = ($robot_flag_hi==1)?1:0;
		$robot['hinum']      = (ifint($robot_hinum))?$robot_hinum:5;
		$robot['flag']['chat'] = ($robot_flag_chat==1)?1:0;
		$robot['chatnum']      = (ifint($robot_chatnum))?$robot_chatnum:5;
		$robot['flag']['view'] = ($robot_flag_view==1)?1:0;
		$robot['viewnum']      = (ifint($robot_viewnum))?$robot_viewnum:5;
		$robot['hour']         = (ifint($robot_hour))?$robot_hour:5;
		$robot['areaupdate']   = ($robot_areaupdate==1)?1:0;
		$robot['areasame']     = ($robot_areasame==1)?1:0;
		$robot['chatC']        = dataIO($robot_chatC,'in',10000);
		$_ZEAI['robot'] = encode_json($robot);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		AddLog('修改【机器人】');
		json_exit(array('flag'=>1,'msg'=>'修改/更新成功！','jumpurl'=>'cache.php'));
	break;
	case 'cache_bounce_vipdatarz':
		$bounce=json_decode($_ZEAI['bounce'],true);
		$bounce['flag']['vipdatarz'] = ($bounce_flag_vipdatarz==1)?1:0;
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){ 
			for($i=1;$i<=4;$i++) {
				$FILES = $_FILES["pic".$i];
				if (!empty($FILES)){
					$dbpicname = setphotodbname('banner',$FILES['tmp_name'],'bn_');
					if ($dbpicname){
						if (!up_send($FILES,$dbpicname,0,'1500*1700'))continue;
						//$_s = setpath_s($dbpicname);
						$_s = $dbpicname;
						$tmppicurl = $_ZEAI['up2']."/".$_s;
						if (!ifpic($tmppicurl))continue;
						switch ($i) {
							case 1:$bounce['vip_picurl'] = $_s;break;
							case 2:$bounce['my_info_picurl'] = $_s;break;
							case 3:$bounce['rz_picurl'] = $_s;break;
						}
					}
				}
			}
		}
		
		$_ZEAI['bounce'] = encode_json($bounce);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		AddLog('【首页海报】->修改');
		json_exit(array('flag'=>1,'msg'=>'修改/更新成功！'));
	break;
	case 'cache_bounce_indexgg':
		$bounce=json_decode($_ZEAI['bounce'],true);
		$bounce['flag']['indexgg'] = ($bounce_flag_indexgg==1)?1:0;
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){ 
			for($i=4;$i<=4;$i++) {
				$FILES = $_FILES["pic".$i];
				if (!empty($FILES)){
					$dbpicname = setphotodbname('banner',$FILES['tmp_name'],'bn_');
					if ($dbpicname){
						if (!up_send($FILES,$dbpicname,0,'1500*1700'))continue;
						$_s = $dbpicname;
						$tmppicurl = $_ZEAI['up2']."/".$_s;
						if (!ifpic($tmppicurl))continue;
						switch ($i) {
							case 4:$bounce['indexgg_picurl'] = $_s;break;
						}
					}
				}
			}
			$bounce['indexgg_url'] = trimhtml($indexgg_url);
		}
		$_ZEAI['bounce'] = encode_json($bounce);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		AddLog('【反弹海报】->修改');
		json_exit(array('flag'=>1,'msg'=>'修改/更新成功！'));
	break;
	case 'bounceDel':
		if(!ifint($i))json_exit(array('flag'=>0,'msg'=>'zeai_error_i'));
		$bounce=json_decode($_ZEAI['bounce'],true);
		switch ($i) {
			case 1:$path_s = $bounce['vip_picurl'];$bounce['vip_picurl'] = '';break;
			case 2:$path_s = $bounce['my_info_picurl'];$bounce['my_info_picurl'] = '';break;
			case 3:$path_s = $bounce['rz_picurl'];$bounce['rz_picurl'] = '';break;
			case 4:$path_s = $bounce['indexgg_picurl'];$bounce['indexgg_picurl'] = '';break;
		}
		@up_send_admindel($path_s);
		$_ZEAI['bounce'] = encode_json($bounce);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'删除成功！'));
	break;
	case 'cache_config':
		$db2 = json_decode($_ZEAI['db'],true);
		/****up****/
		$_UP['upMaxMB'] = abs(intval($upMaxMB));
		$_UP['upVMaxMB'] = abs(intval($upVMaxMB));
		$_UP['upSsize'] = dataIO($upSsize,'in',10);
		$_UP['upMsize'] = dataIO($upMsize,'in',100);
		$_UP['upBsize'] = dataIO($upBsize,'in',100);
		$_UP['ifwaterimg'] = intval($ifwaterimg);
		$FILES = $_FILES["waterimg"];
		if (getpicextname($FILES['tmp_name']) == 'png'){
			$dbpicname = 'p/img/waterimg.png';
			@up_send($FILES,$dbpicname,0,$_UP['upBsize']);
			$_UP['waterimg'] = $dbpicname;
		}else{
			$_UP['waterimg'] = $waterimg_;
		}
		cache_mod_config($_UP,'config_up','_UP');
		/****主ZEAI****/
		//logo
		$FILES = $_FILES["logo"];
		if (getpicextname($FILES['tmp_name']) == 'png'){
			$dbpicname = 'p/img/logo.png';
			@up_send($FILES,$dbpicname,0,$_UP['upBsize']);
			$_ZEAI['logo'] = $dbpicname;
		}else{
			$_ZEAI['logo'] = $logo_;
		}
		//pclogo
		$FILES = $_FILES["pclogo"];
		if (getpicextname($FILES['tmp_name']) == 'png' || getpicextname($FILES['tmp_name']) == 'gif' || getpicextname($FILES['tmp_name']) == 'jpg'){
			$dbpicname = 'p/img/pclogo.png';
			@up_send($FILES,$dbpicname,0,$_UP['upBsize']);
			$_ZEAI['pclogo'] = $dbpicname;
		}else{
			$_ZEAI['pclogo'] = $pclogo_;
		}
		//m_ewm
		$FILES = $_FILES["m_ewm"];
		if (getpicextname($FILES['tmp_name']) == 'png' || getpicextname($FILES['tmp_name']) == 'gif' || getpicextname($FILES['tmp_name']) == 'jpg'){
			$dbpicname = 'p/img/m_ewm.png';
			@up_send($FILES,$dbpicname,0,$_UP['upBsize']);
			$_ZEAI['m_ewm'] = $dbpicname;
		}else{
			$_ZEAI['m_ewm'] = $m_ewm_;
		}
		//
		$_ZEAI['siteName'] = dataIO($siteName,'in',100);
		$_ZEAI['loveB'] = dataIO($loveB,'in',20);
		$_ZEAI['mob_mbkind']= (!ifint($mob_mbkind))?1:$mob_mbkind;
		$_ZEAI['pc_mbkind'] = (!ifint($pc_mbkind))?1:$pc_mbkind;
		$_ZEAI['cache_str'] = dataIO($cache_str,'in',8);
		
		$db2['s'] = (!empty($dbserver))?dataIO($dbserver,'codein',100):'';
		$db2['n'] = (!empty($dbname))?dataIO($dbname,'codein',100):'';
		$db2['u'] = (!empty($dbuser))?dataIO($dbuser,'codein',100):'';
		$db2['p'] = (!empty($dbpass))?dataIO($dbpass,'codein',100):'';
		
		$_ZEAI['db'] = encode_json($db2);
		$_ZEAI['up2']  = dataIO($up2,'in',100);
		$_ZEAI['adm2'] = dataIO($adm2,'in',100);
		$limit = abs(intval($limit));
		$_ZEAI['limit'] = ($limit<100 || $limit>9999999)?500:$limit;
		$_ZEAI['pc_bottom']  = dataIO($pc_bottom,'in',800);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		
		/****adm****/
		$ADM['admSiteName'] = dataIO($admSiteName,'in',100);
		$ADM['admPageSize'] = abs(intval($admPageSize));
		$ADM['admLimit'] = abs(intval($admLimit));
		cache_mod_config($ADM,'config_adm','_ADM');
		/****index****/
		$_INDEX['indexTitle'] = dataIO($indexTitle,'in',100);
		$_INDEX['indexKeywords'] = dataIO($indexKeywords,'in',100);
		$_INDEX['indexContent'] = dataIO($indexContent,'in',200);
		cache_mod_config($_INDEX,'config_index','_INDEX');
		/****gzh****/
		$wx_gzh_getphoto_s  = ($wx_gzh_getphoto_s == 1)?1:0;
		$_GZH['wx_gzh_getphoto_s'] = $wx_gzh_getphoto_s;
		cache_mod_config($_GZH,'config_wxgzh','_GZH');
		AddLog('【基础设置】->【站点设置】修改');
		json_exit(array('flag'=>1,'msg'=>'修改/更新成功！','jumpurl'=>'var.php'));
	break;
	case 'cache_config_del_logo':
		@up_send_admindel($_ZEAI['logo']);
		$_ZEAI['logo'] = '';
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'删除/更新成功！'));
	break;
	case 'cache_config_del_pclogo':
		@up_send_admindel($_ZEAI['pclogo']);
		$_ZEAI['pclogo'] = '';
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'删除/更新成功！'));
	break;
	case 'cache_config_del_m_ewm':
		@up_send_admindel($_ZEAI['m_ewm']);
		$_ZEAI['m_ewm'] = '';
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'删除/更新成功！'));
	break;
	case 'cache_config_del_waterimg':
		@up_send_admindel($_UP['waterimg']);
		$_UP['waterimg'] = '';
		cache_mod_config($_UP,'config_up','_UP');
		json_exit(array('flag'=>1,'msg'=>'删除/更新成功！'));
	break;
	case 'contact_update':
		$_ZEAI['kf_mob']=dataIO($kf_mob,'in',50);
		$_ZEAI['kf_tel']=dataIO($kf_tel,'in',50);
		$_ZEAI['kf_wx']=dataIO($kf_wx,'in',50);
		$_ZEAI['kf_wxpic']=dataIO($kf_wxpic,'in',50);
		$_ZEAI['kf_qq']=dataIO($kf_qq,'in',50);
		$_ZEAI['kf_email']=dataIO($kf_email,'in',100);
		$_ZEAI['kf_address']=dataIO($kf_address,'in',100);
		//	
		$FILES = $_FILES["kf_wxpic"];
		$extname=getpicextname($FILES['tmp_name']);
		if ($extname == 'png' || $extname == 'gif' || $extname == 'jpg'){
			$dbpicname = 'p/img/kf_wxpic'.cdnumletters(4).'.'.$extname;
			@up_send($FILES,$dbpicname,0,$_UP['upMsize']);
			@up_send_admindel($_ZEAI['kf_wxpic']);
			$_ZEAI['kf_wxpic'] = $dbpicname;
		}else{
			$_ZEAI['kf_wxpic'] = $logo_;
		}
		//
		cache_mod_config($_ZEAI,'config','_ZEAI');
		AddLog('【基础设置】->【关于我们】->【联系我们/客服信息】修改');
		json_exit(array('flag'=>1,'msg'=>'修改/更新成功！'));
	break;
	case 'kf_wxpic_delupdate':
		@up_send_admindel($_ZEAI['kf_wxpic']);
		$_ZEAI['kf_wxpic'] = '';
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'删除/更新成功！'));
	break;
	case 'cache_gzh':
		$_ZEAI['wx_gzh_appid'] = trim(dataIO($wx_gzh_appid,'in',100));
		$_ZEAI['wx_gzh_appsecret'] = trim(dataIO($wx_gzh_appsecret,'in',100));
		cache_mod_config($_ZEAI,'config','_ZEAI');
		
		/****subscribe pic****/
		$FILES = $_FILES["subscribe"];
		$extname = getpicextname($FILES['tmp_name']);
		if ($extname == 'png' || $extname == 'gif' || $extname == 'jpg'){
			$dbpicname = 'p/img/subscribe.'.$extname;
			@up_send($FILES,$dbpicname,0,$_UP['upBsize']);
			$_GZH['wx_gzh_ewm'] = $dbpicname;
		}else{
			$_GZH['wx_gzh_ewm'] = $subscribe_;
		}
		$_GZH['wx_gzh_name'] = dataIO($wx_gzh_name,'in',100);
		$_GZH['wx_gzh_token'] = trim(dataIO($wx_gzh_token,'in',100));
		$_GZH['wx_gzh_welcome'] = dataIO($wx_gzh_welcome,'in',400);
		$_GZH['wx_gzh_hfcontent'] = dataIO($wx_gzh_hfcontent,'in',400);
		$_GZH['wx_gzh_mb_msgchat']  = dataIO($wx_gzh_mb_msgchat,'in',100);//失效
		$_GZH['wx_gzh_mb_msgchat2'] = dataIO($wx_gzh_mb_msgchat2,'in',100);
		$_GZH['wx_gzh_mb_udata'] = dataIO($wx_gzh_mb_udata,'in',100);
		$_GZH['wx_gzh_mb_adminfo'] = dataIO($wx_gzh_mb_adminfo,'in',100);
		$_GZH['wx_gzh_mb_honor'] = dataIO($wx_gzh_mb_honor,'in',100);//失效
		$_GZH['wx_gzh_mb_honor2'] = dataIO($wx_gzh_mb_honor2,'in',100);
		$_GZH['wx_gzh_mb_loveb'] = dataIO($wx_gzh_mb_loveb,'in',100);//失效
		$_GZH['wx_gzh_mb_loveb2'] = dataIO($wx_gzh_mb_loveb2,'in',100);
		$_GZH['wx_gzh_mb_productpay']  = dataIO($wx_gzh_mb_productpay,'in',100);//失效
		$_GZH['wx_gzh_mb_productpay2'] = dataIO($wx_gzh_mb_productpay2,'in',100);//失效
		$_GZH['wx_gzh_mb_productpay3'] = dataIO($wx_gzh_mb_productpay3,'in',100);
		cache_mod_config($_GZH,'config_wxgzh','_GZH');
		AddLog('【基础设置】->【微信公众号】修改');
		json_exit(array('flag'=>1,'msg'=>'公众号设置修改/更新成功！','jumpurl'=>'var.php?t=2'));
	break;
	case 'cache_config_del_subscribe':
		@up_send_admindel($_GZH['wx_gzh_ewm']);
		$_GZH['wx_gzh_ewm'] = '';
		cache_mod_config($_GZH,'config_wxgzh','_GZH');
		json_exit(array('flag'=>1,'msg'=>'删除/更新成功！'));
	break;
	case 'cache_login':
		$C['wx_open_appid'] = trim(dataIO($wx_open_appid,'in',100));
		$C['wx_open_appsecret'] = trim(dataIO($wx_open_appsecret,'in',100));
		$C['qq_login_appid'] = trim(dataIO($qq_login_appid,'in',100));
		$C['qq_login_appkey'] = trim(dataIO($qq_login_appkey,'in',100));
		cache_mod_config($C,'config_login','_LOGIN');
		AddLog('【基础设置】->【帐号互联】修改');
		json_exit(array('flag'=>1,'msg'=>'登录设置修改/更新成功！','jumpurl'=>'var.php?t=3'));
	break;
	case 'cache_sms':
		$_SMS['sms_sid'] = trim(dataIO($sms_sid,'in',100));
		$_SMS['sms_apikey'] = trim(dataIO($sms_apikey,'in',100));
		$_SMS['sms_tplid_authcode'] = trim(dataIO($sms_tplid_authcode,'in',100));
		$_SMS['sms_tplid_findpass'] = trim(dataIO($sms_tplid_findpass,'in',100));
		$_SMS['sms_yzmnum'] = abs(intval($sms_yzmnum));
		//
		$_SMS['email_uid'] = trim(dataIO($email_uid,'in',100));
		$_SMS['email_pwd'] = trim(dataIO($email_pwd,'in',50));
		$_SMS['email_smtp']= dataIO($email_smtp,'in',100);
		$_SMS['email_email'] = dataIO($email_email,'in',150);
		$_SMS['email_port']  = intval($email_port);
		$_SMS['email_debug'] = intval($email_debug);
		cache_mod_config($_SMS,'config_sms','_SMS');		
		AddLog('【基础设置】->【短信设置】修改');
		json_exit(array('flag'=>1,'msg'=>'短信设置修改/更新成功！','jumpurl'=>'var.php?t=4'));
	break;
	case 'cache_pay':
		$C['wxpay_mchid'] = trim(dataIO($wxpay_mchid,'in',100));
		$C['wxpay_key'] = trim(dataIO($wxpay_key,'in',100));
		$C['alipay_appid'] = trim(dataIO($alipay_appid,'in',100));
		$C['alipay_key1']  = trim(dataIO($alipay_key1,'in',3000));
		$C['alipay_key2']  = trim(dataIO($alipay_key2,'in',3000));
		//$C['alipay_partner'] = dataIO($alipay_partner,'in',100);
		//$C['alipay_key'] = dataIO($alipay_key,'in',100);
		//$C['alipay_ID']  = dataIO($alipay_ID,'in',100);
		$C['logname']    = dataIO($logname,'in',50);
		cache_mod_config($C,'config_pay','_PAY');
		AddLog('【基础设置】->【在线支付】修改');
		json_exit(array('flag'=>1,'msg'=>'在线支付修改/更新成功！','jumpurl'=>'var.php?t=5'));
	break;
	//更新地区缓存
	case 'cache_area':
		$rt = $db->query("SELECT id,title FROM ".__TBL_AREA1__." ORDER BY px DESC");
		while($tmprows = $db->fetch_array($rt,'num')){$arr1[]=$tmprows;}
		$rt = $db->query("SELECT id,title,fid FROM ".__TBL_AREA2__." ORDER BY px DESC");
		while($tmprows = $db->fetch_array($rt,'num')){$arr2[]=$tmprows;}
		$rt = $db->query("SELECT id,title,fid FROM ".__TBL_AREA3__." ORDER BY px DESC");
		while($tmprows = $db->fetch_array($rt,'num')){$arr3[]=$tmprows;}
		$rt = $db->query("SELECT id,title,fid FROM ".__TBL_AREA4__." ORDER BY px DESC");
		while($tmprows = $db->fetch_array($rt,'num')){$arr4[]=$tmprows;}
		//cache_mod_aera($arr1,$arr2,$arr3,$arr4);
		//
		$rt = $db->query("SELECT id,title FROM ".__TBL_AREAHJ1__." ORDER BY px DESC");
		while($tmprows = $db->fetch_array($rt,'num')){$arrhj1[]=$tmprows;}
		$rt = $db->query("SELECT id,title,fid FROM ".__TBL_AREAHJ2__." ORDER BY px DESC");
		while($tmprows = $db->fetch_array($rt,'num')){$arrhj2[]=$tmprows;}
		$rt = $db->query("SELECT id,title,fid FROM ".__TBL_AREAHJ3__." ORDER BY px DESC");
		while($tmprows = $db->fetch_array($rt,'num')){$arrhj3[]=$tmprows;}
		$rt = $db->query("SELECT id,title,fid FROM ".__TBL_AREAHJ4__." ORDER BY px DESC");
		while($tmprows = $db->fetch_array($rt,'num')){$arrhj4[]=$tmprows;}
		cache_mod_aera($arr1,$arr2,$arr3,$arr4,$arrhj1,$arrhj2,$arrhj3,$arrhj4);
		//
		$_ZEAI['cache_str']=ADDTIME;
		cache_mod_config($_ZEAI,'config','_ZEAI');
		json_exit(array('flag'=>1,'msg'=>'地区缓存更新成功！'));
	break;
	//更新注册选项
	case 'cache_reg':
		$_REG['reg_flag']      = ($reg_flag == 2 || $reg_flag == 3)?$reg_flag:1;
		$_REG['gzflag2']       = ($gzflag2==1)?1:0;
		$_REG['reg_kind']      = intval($reg_kind);
		$_REG['reg_3login_qq'] = intval($reg_3login_qq);
		$_REG['reg_3login_wx'] = intval($reg_3login_wx);
		$_REG['reg_loveb']     = abs(intval($reg_loveb));
		$_REG['reg_grade']     = intval($reg_grade);
		$_REG['reg_force_wx']  = ($reg_force_wx==1)?1:0;
		$_REG['reg_style']     = ($reg_style==2)?2:1;
		$_REG['reg_data']      = $reg_data = (@is_array($reg_data))?implode(',',$reg_data):'';
		$_REG['reg_data_px']   = $reg_data_px;
		cache_mod_config($_REG,'config_reg','_REG');
		//
		$db->query("UPDATE ".__TBL_ROLE__." SET ifdefault=0 WHERE kind=1");
		$db->query("UPDATE ".__TBL_ROLE__." SET ifdefault=1 WHERE kind=1 AND grade=".intval($reg_grade));
		$rt = $db->query("SELECT grade AS g,title AS t,ifdefault AS d,if2,flag AS f FROM ".__TBL_ROLE__." WHERE kind=1 ORDER BY id");
		while($tmprows = $db->fetch_array($rt,'name')){$arr[]=$tmprows;}
		$_ZEAI['urole'] = encode_json($arr);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		//
		AddLog('修改【注册选项】');
		json_exit(array('flag'=>1,'msg'=>'保存更新成功！'));
	break;
	//更新VIP爱豆和收费机制
	case 'cache_vip':
		//$tmpif2 = array(1,3,6,12,999);
		//if (!is_array($sj_if2) || count($sj_if2)==0)json_exit(array('flag'=>0,'msg'=>'升级有效期在【1月、3月、6月、包年、永久】里至少选一项，可以多选或全选！'));	
		if (is_array($urole) && count($urole)>0){
			/*****会员升级*****/
/*			$_VIP['sj_if2'] = encode_json($sj_if2);
			foreach($sj_if2 as $if2V){
				foreach($urole as $RV){
					if ($RV['g']==1)continue;
					$price = 'sj_rmb_'.$RV['g'].'_'.$if2V;
					$sj_rmb[$RV['g'].'_'.$if2V] = abs(intval($$price));
				}
			}
			$_VIP['sj_rmb'] = encode_json($sj_rmb);
*/			/*****查看联系方式*****/
			//按次
			foreach($urole as $RV){
				$price = 'contact_loveb_'.$RV['g'];
				$price = abs(intval($$price));
				$contact_loveb[$RV['g']] = $price;
			}
			$_VIP['contact_loveb'] = encode_json($contact_loveb);
			//每天查看总人数
			foreach($urole as $RV){
				$price = 'contact_daylooknum_'.$RV['g'];
				$price = abs(intval($$price));
				$contact_daylooknum[$RV['g']] = $price;
			}
			$_VIP['contact_daylooknum'] = encode_json($contact_daylooknum);
			//同级查看
			$_VIP['contact_level'] = ($contact_level == 1)?1:0;

			
			/*****聊天解锁*****/
			//按次
			foreach($urole as $RV){
				$price = 'chat_loveb_'.$RV['g'];
				$price = abs(intval($$price));
				$chat_loveb[$RV['g']] = $price;
			}
			$_VIP['chat_loveb'] = encode_json($chat_loveb);
			//每天看信解锁总人数
			foreach($urole as $RV){
				$price = 'chat_daylooknum_'.$RV['g'];
				$price = abs(intval($$price));
				$chat_daylooknum[$RV['g']] = $price;
			}
			$_VIP['chat_daylooknum'] = encode_json($chat_daylooknum);
			//同级查看
			$_VIP['chat_level'] = ($chat_level == 1)?1:0;
				
			
			/*****充值爱豆折扣*****/
			foreach($urole as $RV){
				$price = 'loveb_buy_'.$RV['g'];
				$price = abs(floatval($$price));
				$price = (empty($price) || $price>1 || !is_numeric($price))?1:$price;
				$loveb_buy[$RV['g']] = round($price,2);
			}
			$_VIP['loveb_buy'] = encode_json($loveb_buy);
			/*****相册容量*****/
			foreach($urole as $RV){
				$price = 'photo_num_'.$RV['g'];
				$price = abs(intval($$price));
				$photo_num[$RV['g']] = (empty($price))?4:$price;
			}
			$_VIP['photo_num'] = encode_json($photo_num);
			/*****视频容量*****/
			foreach($urole as $RV){
				$price = 'video_num_'.$RV['g'];
				$price = abs(intval($$price));
				$video_num[$RV['g']] = (empty($price))?1:$price;
			}
			$_VIP['video_num'] = encode_json($video_num);
			/*****签到数组*****/
			if (empty($sign_numlist)){
				$tmparr = array(2,9,18,48,188,6,4,1,99,66);
			}else{
				$sign_numlist = dataIO($sign_numlist,'in',100);
				$sign_numlist = explode(',',$sign_numlist);
				foreach($sign_numlist as $SV){
					$SV = abs(intval($SV));
					if ($SV > 0)$tmparr[] = $SV;
				}
				
			}
			$_VIP['sign_numlist'] = encode_json($tmparr);
			/*****做任务奖励*****/
			$task_loveb_myinfo  = abs(intval($task_loveb_myinfo));
			$task_loveb_photom  = abs(intval($task_loveb_photom));
			$task_loveb_honor   = abs(intval($task_loveb_honor));
			$task_loveb_photo   = abs(intval($task_loveb_photo));
			$task_loveb_video   = abs(intval($task_loveb_video));
			$task_loveb['myinfo'] = ($task_loveb_myinfo>0)?$task_loveb_myinfo:0;
			$task_loveb['photom'] = ($task_loveb_photom>0)?$task_loveb_photom:0;
			$task_loveb['rz'] = ($task_loveb_rz>0)?$task_loveb_rz:0;
			$task_loveb['photo'] = ($task_loveb_photo>0)?$task_loveb_photo:0;
			$task_loveb['video'] = ($task_loveb_video>0)?$task_loveb_video:0;
			$_VIP['task_loveb'] = encode_json($task_loveb);
			/*****首页会员列表置顶爱豆	*****/
			$_VIP['push_index'] = abs(intval($push_index));
			
			$cz_minnum=abs(intval($cz_minnum));
			$_VIP['cz_minnum'] = ($cz_minnum < 1)?10:$cz_minnum;
			
			$_VIP['gift_dhkind'] = (empty($gift_dhkind))?'money':$gift_dhkind;
			$gift_dhloveb_num    = abs(floatval($gift_dhloveb_num));
			$gift_dhloveb_num    = ($gift_dhloveb_num>1 || empty($gift_dhloveb_num))?1:$gift_dhloveb_num;
			$_VIP['gift_dhloveb_num'] = $gift_dhloveb_num;
			
			$gift_dhmoney_num    = abs(floatval($gift_dhmoney_num));
			$gift_dhmoney_num    = ($gift_dhmoney_num>1 || empty($gift_dhmoney_num))?1:$gift_dhmoney_num;
			$_VIP['gift_dhmoney_num'] = $gift_dhmoney_num;
			
			cache_mod_config($_VIP,'config_vip','_VIP');
			/*****注册初始赠送爱豆*****/
			//require_once ZEAI.'cache/config_reg.php';
			$_REG['reg_loveb'] = abs(intval($reg_loveb));
			cache_mod_config($_REG,'config_reg','_REG');
			
			$loveBrate=abs(intval($loveBrate));
			$_ZEAI['loveBrate'] =($loveBrate==0 || $loveBrate>99999)?100:$loveBrate;
			cache_mod_config($_ZEAI,'config','_ZEAI');
			AddLog('修改【爱豆与收费】');
			//
			json_exit(array('flag'=>1,'msg'=>'保存更新成功！'));
		}else{
			json_exit(array('flag'=>0,'msg'=>'会员组为空或升级有效期为空！'));
		}
	break;
	//开关
	case 'cache_switch':
		$WZ = json_decode($_ZEAI['WZ'],true);
		$WZ['iftjU']     = ($wz_iftjU == 1)?1:0;
		$WZ['ifpay']     = ($wz_ifpay == 1)?1:0;
		$WZ['ifpay_num'] = (!empty($wz_ifpay_num))?trimm($wz_ifpay_num):'1,2,5,10';
		$WZ['ifbbs']     = ($wz_ifbbs == 1)?1:0;
		$WZ['bbs_ifsh']  = ($wz_bbs_ifsh == 1)?1:0;
		$WZ['iftjWZ']    = ($wz_iftjWZ == 1)?1:0;
		$_ZEAI['WZ'] = encode_json($WZ);
		/*****审核机制设置*****/
		$switch = json_decode($_ZEAI['switch'],true);
		foreach($urole as $RV){
			//会员服务模式
			$value = 'switch_Smode_'.$RV['g'];
			$value = ($$value == 2)?2:1;
			$switch['Smode']['g_'.$RV['g']] = $value;
			//修改基本资料
			$value = 'switch_sh_moddata_'.$RV['g'];
			$value = ($$value == 1)?1:0;
			$switch['sh']['moddata_'.$RV['g']] = $value;
			//上传形象照片审核
			$value = 'switch_sh_photom_'.$RV['g'];
			$value = ($$value == 1)?1:0;
			$switch['sh']['photom_'.$RV['g']] = $value;
			//个人相册审核
			$value = 'switch_sh_photo_'.$RV['g'];
			$value = ($$value == 1)?1:0;
			$switch['sh']['photo_'.$RV['g']] = $value;
			//个人视频审核
			$value = 'switch_sh_video_'.$RV['g'];
			$value = ($$value == 1)?1:0;
			$switch['sh']['video_'.$RV['g']] = $value;
		}
		/*通知类型
		if (empty($notice_kind) || !is_array($notice_kind)){
			$notice_kind = array(1);
		}
		$switch['notice_kind'] = $notice_kind;
		*/
		$switch['ifrmbtx'] = ($ifrmbtx == 1)?1:0;
		$switch['ifrmbtx_num'] = ($ifrmbtx_num > 1 || $ifrmbtx_num<0 || empty($ifrmbtx_num))?1:$ifrmbtx_num;
		$switch['ifrmbtx_minnum'] = ($ifrmbtx_minnum > 9999 || $ifrmbtx_minnum<=1 || empty($ifrmbtx_minnum))?50:$ifrmbtx_minnum;
		$switch['ifhb'] = ($ifhb == 1)?1:0;
		//强制完善资料
		$switch['force']['data']   = ($force_data == 1)?1:0;
		//强制形像照
		$switch['force']['photom'] = ($force_photom == 1)?1:0;
		//强制手机
		$switch['force']['mob'] = ($force_mob == 1)?1:0;
		//强制实名认证
		$switch['force']['cert'] = ($force_cert == 1)?1:0;
		//模糊功能
		$switch['grade1LockBlur'] = ($grade1LockBlur == 1)?1:0;
		$switch['grade1LockBlurT'] = dataIO($grade1LockBlurT,'in');
		//防骗/防托
		$switch['chatHiContact_ifshow'] = $chatHiContact_ifshow;
		$switch['chatHiContact_iftips'] = ($chatHiContact_iftips==1)?1:0;
		$_ZEAI['switch'] = encode_json($switch);
		cache_mod_config($_ZEAI,'config','_ZEAI');
		/****index****/
		$_INDEX['iModuleU_bigmore'] = ($iModuleU_bigmore==1)?1:0;
		$_INDEX['iMarquee'] = ($iMarquee==1)?1:0;
		$_INDEX['index_private'] = ($index_private==1)?1:0;
		cache_mod_config($_INDEX,'config_index','_INDEX');	
		/****vip****/
		$_VIP['YKviewU']   = ($YKviewU==1)?1:0;
		$_VIP['hidephoto'] = ($hidephoto==1)?1:0;
		$_VIP['hidedata']  = ($hidedata==1)?1:0;
		$_VIP['hidedel']   = ($hidedel==1)?1:0;
		$_VIP['hidedel_rmb']   = (!empty($hidedel_rmb))?floatval($hidedel_rmb):0;
		$_VIP['hideprivacy'] = ($hideprivacy==1)?1:0;
		$_VIP['chatContact_data']    = $chatContact_data = (@is_array($chatContact_data))?implode(',',$chatContact_data):'';
		$_VIP['chatContact_data_px'] = $chatContact_data_px;
		$bfb1 = abs(intval($chatContact_bfb_num));
		$_VIP['chatContact_bfb_num'] = ($bfb1>100)?100:$bfb1;
		$_VIP['viewhomepage_data']   = $viewhomepage_data = (@is_array($viewhomepage_data))?implode(',',$viewhomepage_data):'';
		$_VIP['viewhomepage_data_px']= $viewhomepage_data_px;
		$bfb2 = abs(intval($viewhomepage_bfb_num));
		$_VIP['viewhomepage_bfb_num']= ($bfb2>100)?100:$bfb2;
		$_VIP['hi_data']   = $hi_data = (@is_array($hi_data))?implode(',',$hi_data):'';
		$_VIP['hi_data_px']= $hi_data_px;$bfb3 = abs(intval($hi_bfb_num));
		$_VIP['hi_bfb_num']= ($bfb3>100)?100:$bfb3;
		$_VIP['ifViewPushsex'] = ($ifViewPushsex==1 || $ifViewPushsex==2)?$ifViewPushsex:0;
		$_VIP['party_joingrade'] = (is_array($party_joingrade))?encode_json($party_joingrade):'';
		cache_mod_config($_VIP,'config_vip','_VIP');
		AddLog('修改【功能审核开关】');
		json_exit(array('flag'=>1,'msg'=>'保存更新成功！'));
	break;
	case 'cache_config_del_wxbgpic':
		$tg = json_decode($_REG['TG_set'],true);
		@up_send_admindel($tg['wxbgpic']);
		$tg['wxbgpic'] = '';
		$_REG['TG_set'] = encode_json($tg);
		cache_mod_config($_REG,'config_reg','_REG');
		json_exit(array('flag'=>1,'msg'=>'删除/更新成功！'));
	break;
	case 'cache_config_del_wapbgpic':
		$tg = json_decode($_REG['TG_set'],true);
		@up_send_admindel($tg['wapbgpic']);
		$tg['wapbgpic'] = '';
		$_REG['TG_set'] = encode_json($tg);
		cache_mod_config($_REG,'config_reg','_REG');
		json_exit(array('flag'=>1,'msg'=>'删除/更新成功！'));
	break;

	case 'cache_wx_gzh_push':
		if ($wx_gzh_push_kind != 'text' && $wx_gzh_push_kind != 'pic' && $wx_gzh_push_kind != 'ulist')json_exit(array('flag'=>0,'msg'=>'推送类型不符！'));
		require_once ZEAI.'cache/config_wxgzh.php';
		$_GZH['wx_gzh_push_kind'] = $wx_gzh_push_kind;
		switch ($wx_gzh_push_kind) {
			case 'text':
				if (str_len($wx_gzh_push_text_C) >400 || empty($wx_gzh_push_text_C))json_exit(array('flag'=>0,'msg'=>'请检查：<br>1.内容是否为空<br>2.内容是否超过400字节'));
				$_GZH['wx_gzh_push_text_C'] = dataIO($wx_gzh_push_text_C,'in',400);
			break;
			case 'pic':
				if (empty($_GZH["wx_gzh_push_pic_path"])){ 
					$FILES = $_FILES['pic1'];
					if (!empty($FILES['tmp_name'])){
						$dbpicname = setphotodbname('weixin',$FILES['tmp_name']);
						if (!up_send($FILES,$dbpicname,$_UP['ifwaterimg'],'900*500'))json_exit(array('flag'=>0,'msg'=>'上传环境错误，请联系原作者QQ:797311解决'));
						$tmppicurl = $_ZEAI['up2'].'/'.$dbpicname;
						if (!ifpic($tmppicurl))json_exit(array('flag'=>0,'msg'=>'图片格式错误，请联系原作者QQ:797311解决'));
						$_GZH["wx_gzh_push_pic_path"] = $dbpicname;
					}else{
						json_exit(array('flag'=>0,'msg'=>'图片不能为空！'));
					}
				}
				$_GZH['wx_gzh_push_pic_title'] = dataIO($wx_gzh_push_pic_title,'in',100);
				$_GZH['wx_gzh_push_pic_C']     = dataIO($wx_gzh_push_pic_C,'in',100);
				$_GZH['wx_gzh_push_pic_url']   = dataIO($wx_gzh_push_pic_url,'in',50);
			break;
			case 'ulist':
				$_GZH['wx_gzh_push_ulist'] = dataIO($wx_gzh_push_ulist,'in',100);
			break;
		}
		cache_mod_config($_GZH,'config_wxgzh','_GZH');
		$db->query("UPDATE ".__TBL_USER__." SET ifWeixinPushInfo=1 WHERE ifWeixinPushInfo=0 AND subscribe=1 AND openid<>''");
		AddLog('【公众号推送】->修改');
		json_exit(array('flag'=>1,'msg'=>'保存更新成功！'));
	break;
	case 'cache_wx_gzh_push_delpic':
		up_send_admindel($_GZH["wx_gzh_push_pic_path"]);
		$_GZH["wx_gzh_push_pic_path"] = '';
		cache_mod_config($_GZH,'config_wxgzh','_GZH');
		json_exit(array('flag'=>1,'msg'=>'删除成功！'));
	break;
	case 'cache_wx_gzh_qf':
		if ($wx_gzh_qf_kind != 'text' && $wx_gzh_qf_kind != 'pic' && $wx_gzh_qf_kind != 'ulist')json_exit(array('flag'=>0,'msg'=>'推送类型不符！'));
		require_once ZEAI.'cache/config_wxgzh.php';
		$_GZH['wx_gzh_qf_kind'] = $wx_gzh_qf_kind;
		switch ($wx_gzh_qf_kind) {
			case 'text':
				if (str_len($wx_gzh_qf_text_C) >400 || empty($wx_gzh_qf_text_C))json_exit(array('flag'=>0,'msg'=>'请检查：<br>1.内容是否为空<br>2.内容是否超过400字节'));
				$_GZH['wx_gzh_qf_text_C'] = dataIO($wx_gzh_qf_text_C,'in',400);
			break;
			case 'pic':
				if (empty($_GZH["wx_gzh_qf_pic_path"])){ 
					$FILES = $_FILES['pic1'];
					if (!empty($FILES['tmp_name'])){
						$dbpicname = setphotodbname('weixin',$FILES['tmp_name']);
						if (!up_send($FILES,$dbpicname,$_UP['ifwaterimg'],'900*500'))json_exit(array('flag'=>0,'msg'=>'上传环境错误，请联系原作者QQ:797311解决'));
						$tmppicurl = $_ZEAI['up2'].'/'.$dbpicname;
						if (!ifpic($tmppicurl))json_exit(array('flag'=>0,'msg'=>'图片格式错误，请联系原作者QQ:797311解决'));
						$_GZH["wx_gzh_qf_pic_path"] = $dbpicname;
					}else{
						json_exit(array('flag'=>0,'msg'=>'图片不能为空！'));
					}
				}
				$_GZH['wx_gzh_qf_pic_title'] = dataIO($wx_gzh_qf_pic_title,'in',100);
				$_GZH['wx_gzh_qf_pic_C']     = dataIO($wx_gzh_qf_pic_C,'in',100);
				$_GZH['wx_gzh_qf_pic_url']   = dataIO($wx_gzh_qf_pic_url,'in',50);
			break;
			case 'ulist':
				$_GZH['wx_gzh_qf_ulist'] = dataIO($wx_gzh_qf_ulist,'in',100);
			break;
		}
		cache_mod_config($_GZH,'config_wxgzh','_GZH');
		AddLog('【公众号群发】->修改');
		json_exit(array('flag'=>1,'msg'=>'保存更新成功！'));
	break;
	case 'cache_wx_gzh_qf_delpic':
		require_once ZEAI.'cache/config_wxgzh.php';
		up_send_admindel($_GZH["wx_gzh_qf_pic_path"]);
		$_GZH["wx_gzh_qf_pic_path"] = '';
		cache_mod_config($_GZH,'config_wxgzh','_GZH');
		json_exit(array('flag'=>1,'msg'=>'删除成功！'));
	break;
	//会员字段
	case 'cache_udata':
		//生成js
		$rt = $db->query("SELECT subkind,fieldname,subjsonstr FROM ".__TBL_UDATA__." WHERE (subkind=2 OR subkind=3 OR subkind=4) ORDER BY subkind DESC,id");/* AND flag=1*/
		while($tmprows = $db->fetch_array($rt,'name')){$arr[]=$tmprows;}
		cache_mod_udata_js($arr);
		//生成PHP
		$Wstr = '';
		$rt = $db->query("SELECT fieldname,subjsonstr FROM ".__TBL_UDATA__." WHERE (subkind=2 OR subkind=3 OR subkind=4) ORDER BY px DESC,id");/* AND flag=1*/
		while($rows = $db->fetch_array($rt,'name')){
			$Wstr .= '$_UDATA[\''.$rows['fieldname'].'\']=\''.$rows['subjsonstr'].'\';';//.PHP_EOL
		}
		//生成extifshow
		$rt = $db->query("SELECT fieldname AS f,title AS t,subkind AS s FROM ".__TBL_UDATA__." WHERE kind=3 AND flag=1 ORDER BY px DESC");/* AND flag=1*/
		while($tmprows = $db->fetch_array($rt,'name')){$arr3[]=$tmprows;}
		$Wstr .= '$_UDATA[\'extifshow\']=\''.encode_json($arr3).'\';';//.PHP_EOL
		//生成PHP函数
		$Wstr .= 'function udata($field,$value){if(empty($value))return "";global $_UDATA;$D=json_decode($_UDATA[$field]);if (ifint($D->start))return $value.$D->dw;$valueARR=explode(",",$value);$valueNum=count($valueARR);$idlist=array();foreach($D as $v){if($valueNum==1){if($valueARR[0] == $v->i){return $v->v;}}else{foreach($valueARR as $idv){if($idv == $v->i)$idlist[]=$v->v;}}}$idlist = (count($idlist)>0)?implode(",",$idlist):"";return $idlist;}';
		//
		@wirte_file(ZEAI.'cache/udata.php',"<?php /*www.zeai.cn ZEAI 6.0高速缓存系统*/".$Wstr."?>");
		
		$_ZEAI['cache_str']=ADDTIME;
		cache_mod_config($_ZEAI,'config','_ZEAI');
		AddLog('【资料属性字段】缓存->修改');
		json_exit(array('flag'=>1,'msg'=>'更新成功！'));
	break;
	
	default:exit('www.Zeai.cn版权所有');break;
}
function cache_mod_udata_js($arr){
	if (!is_array($arr))json_exit(array('flag'=>0,'msg'=>'forbidden！'));
	$Wstr = '';$Wstr4='';$Wstr2='';
	foreach($arr as $rows){
		if ($rows['subkind'] == 4){//区间
			$subjson = json_decode($rows['subjsonstr'],true);
			$LI4  = 'var '.$rows['fieldname'].'_ARR=[];';//.PHP_EOL
			$LI4 .= 'for(var i='.$subjson['start'].';i<='.$subjson['end'].'; i++){var istr=i.toString();'.$rows['fieldname'].'_ARR.push({i:istr,v:istr+" '.$subjson['dw'].'"});}';//.PHP_EOL
			$Wstr4 .= $LI4;
		}else{
			//if($rows['subjsonstr']>0){
			$subjson = json_decode($rows['subjsonstr'],true);
			$wstr = '';
			$num = count($subjson);
			foreach($subjson as $k=>$v){
				$id    = $v['i'];
				$value = $v['v'];
				$fg = ($num != ($k+1))?',':'';
				$wstr .= '{i:"'.$id.'",v:"'.$value.'"}'.$fg;//.PHP_EOL
			}
			$LI23  = $rows['fieldname'].'_ARR=[';//.PHP_EOL
			$LI23 .= $wstr;
			$LI23 .= '],';//.PHP_EOL
			$Wstr23 .= $LI23;//.PHP_EOL
			//}
		}
	}
	$Wstr = $Wstr4.'var '.rtrim($Wstr23,',').';';
	@wirte_file(ZEAI.'cache/udata.js','/*www_ZEAI_cn ZEAIv6_0高速JS缓存系统*/'.$Wstr);
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
function cache_mod_aera($arr1,$arr2,$arr3,$arr4,$arr5,$arr6,$arr7,$arr8){
	if (!is_array($arr1))return false;
	$W  = cache_get_aeraARR('areaARR1',$arr1,true);
	$W .= cache_get_aeraARR('areaARR2',$arr2);
	$W .= cache_get_aeraARR('areaARR3',$arr3);
	$W .= cache_get_aeraARR('areaARR4',$arr4);
	$W .= cache_get_aeraARR('areaARRhj1',$arr5,true);
	$W .= cache_get_aeraARR('areaARRhj2',$arr6);
	$W .= cache_get_aeraARR('areaARRhj3',$arr7);
	$W .= cache_get_aeraARR('areaARRhj4',$arr8);
	@wirte_file(ZEAI.'cache/areadata.js',"/*www。zeai。cn ZEAI V6.0高速JS缓存系统*/".$W);
}
function cache_get_aeraARR($varname,$arr,$if1=false) {
	$count = (is_array($arr))?count($arr):0;
	if ($count > 0){
		$Wstr = 'var '.$varname.' = [';
		for($i=0;$i<$count;$i++) {
			if($if1){
				$f=0;	
			}else{
				$f="'".$arr[$i][2]."'";
			}
			$Wstr .= "{i:'".$arr[$i][0]."',v:'".$arr[$i][1]."',f:".$f."}";
			if ($i != ($count-1))$Wstr .= ',';
		}
		$Wstr .= '];';
	}
	return $Wstr;
}
?>
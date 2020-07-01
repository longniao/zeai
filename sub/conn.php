<?php
!function_exists('ifalone') && exit('Forbidden');
class db_zeai{
	function close(){mysqli_close($this->mysqli);}
	function __construct(){
		global $_ZEAI;
		$db = json_decode($_ZEAI['db'],true);
		$this->mysqli = new mysqli($db['s'],$db['u'],$db['p'],$db['n']);
		if($this->mysqli->connect_error)$this->halt("您所选的数据库".$this->dbname."不存在");
	}
	function query($query){
		mysqli_query($this->mysqli,"set names 'utf8'");
		$res = $this->mysqli->query($query);
		if(!$res)$this->halt($query);
		return $res;
	}
	function num_rows($query){$res = mysqli_num_rows($query);return $res;}
	function fetch_array($query,$sort='all') {
		if ($sort == 'num'){
			return mysqli_fetch_row($query);
		}elseif($sort == 'name'){
			return mysqli_fetch_assoc($query);
		}elseif($sort == 'all'){
			return mysqli_fetch_array($query);
		}
	}
	function insert_id() {return mysqli_insert_id($this->mysqli);}
	function data_seek($rt,$pagesizeinfo) {return mysqli_data_seek($rt,$pagesizeinfo);}
	function halt($msg){echo $msg;exit;}
	public function ROW($tblname,$field,$WHERE="",$sort="all") {
		$rt = $this->query("SELECT ".$field." FROM ".$tblname." WHERE ".$WHERE);
		if($this->num_rows($rt)){return $this->fetch_array($rt,$sort);}else{return false;}
	}
	public function NAME($uid,$ufield="id",$WHERE="",$tblname=__TBL_USER__){
		$uid = intval($uid);
		$WHERE = (empty($WHERE))?"id=".$uid:$WHERE;//AND flag=1
		return $this->ROW($tblname,$ufield,$WHERE,$sort='name');
	}
	public function NUM($uid,$ufield="id",$WHERE="",$tblname=__TBL_USER__){
		$uid = intval($uid);
		$WHERE = (empty($WHERE))?"id=".$uid:$WHERE;//AND flag=1
		return $this->ROW($tblname,$ufield,$WHERE,$sort='num');
	}
	public function UPDATE($uid,$ufield,$tblname=__TBL_USER__,$WHERE="1=1") {
		$uid = intval($uid);
		$this->query("UPDATE ".$tblname." SET ".$ufield." WHERE ".$WHERE." AND id=".$uid);
	}
	public function COUNT($tblname='',$WHERE='1=1') {
		$rt  = $this->query("SELECT COUNT(*) FROM ".$tblname." WHERE ".$WHERE);
		$row = $this->fetch_array($rt,'num');
		return $row[0];
	}
	public function AddLovebRmbList($uid,$content,$num=0,$type='loveb',$kind=0,$uidkind='') {
		if (!empty($num)){
			if($uidkind=='tg'){
				$TBL = __TBL_TG_USER__;
				$uidkind='tg_uid';
			}else{
				$TBL = __TBL_USER__;
				$uidkind='uid';
			}
			$row = $this->ROW($TBL,"loveb,money","id=".$uid,"name");
			$loveb = $row['loveb'];
			$money = $row['money'];
			switch ($type){
				case 'loveb':$endnum = $loveb;$tblname = __TBL_LOVEB_LIST__;break;
				case 'money':$endnum = $money;$tblname = __TBL_MONEY_LIST__;break;
			}
			$this->query("INSERT INTO ".$tblname."($uidkind,content,num,endnum,addtime,kind) VALUES ($uid,'$content',$num,$endnum,".ADDTIME.",$kind)");
			return $this->insert_id();
		}
	}
	public function SendTip($uid,$title,$content,$tt='sys') {//t:1系统2礼物3招呼4红娘	
		$uidfld='uid';
		switch ($tt) {
			case 'sys':$tt=1;break;
			case 'gift':$tt=2;break;
			case 'hi':$tt=3;break;
			case 'hn':$tt=4;break;
			case 'tg':$tt=5;$uidfld='tg_uid';break;
			case 'shop':$tt=6;$uidfld='tg_uid';break;
		}
		$uid = intval($uid);
		$this->query("INSERT INTO ".__TBL_TIP__."  ($uidfld,title,content,kind,addtime) VALUES ($uid,'$title','$content',$tt,".ADDTIME.")");
		if($tt != 'tg')$this->query("UPDATE ".__TBL_USER__." SET tipnum=tipnum+1 WHERE id=".$uid);
	}
	//hb
	public function ROWNAME($uid,$ufield="id",$WHERE="",$tblname=__TBL_USER__) {
		return $this->ROW($uid,$ufield,$WHERE,$tblname,$sort='name');
	}
	public function ROWNUM($uid,$ufield="id",$WHERE="",$tblname=__TBL_USER__) {
		return $this->ROW($uid,$ufield,$WHERE,$tblname,$sort='num');
	}
}
$dbvar=$Za1.$Za2;
const __TBL_USER__  = 'zeai_user';
const __TBL_TMP__   = 'zeai_tmp';
const __TBL_ROLE__  = 'zeai_role';
const __TBL_ADMIN__ = 'zeai_super';
const __TBL_IP__    = 'zeai_ip';
const __TBL_LOG__   = 'zeai_log';
const __TBL_UDATA__ = 'zeai_udata';
const __TBL_AREA1__ = 'zeai_area1';
const __TBL_AREA2__ = 'zeai_area2';
const __TBL_AREA3__ = 'zeai_area3';
const __TBL_AREA4__ = 'zeai_area4';
const __TBL_AREAHJ1__ = 'zeai_areahj1';
const __TBL_AREAHJ2__ = 'zeai_areahj2';
const __TBL_AREAHJ3__ = 'zeai_areahj3';
const __TBL_AREAHJ4__ = 'zeai_areahj4';
const __TBL_LOVEB_LIST__ = 'zeai_loveb_list';
const __TBL_MONEY_LIST__ = 'zeai_money_list';
const __TBL_PAY__   = 'zeai_pay';
const __TBL_TIP__   = 'zeai_tip';
const __TBL_PHOTO__ = 'zeai_photo';
const __TBL_VIDEO__ = 'zeai_video';
const __TBL_NEWS__  = 'zeai_news';
const __TBL_NEWS_BBS__ = 'zeai_news_bbs';
const __TBL_NEWS_KIND__= 'zeai_news_kind';
const __TBL_RZ__    = 'zeai_rz';
const __TBL_GIFT__     = 'zeai_gift';
const __TBL_GIFT_USER__= 'zeai_gift_user';
const __TBL_GZ__    = 'zeai_gz';
const __TBL_315__   = 'zeai_315';
const __TBL_UCOUNT__= 'zeai_ucount';
const __TBL_CLICKHISTORY__= 'zeai_clickhistory';
const __TBL_GIFT__     = 'zeai_gift';
const __TBL_GIFT_USER__= 'zeai_gift_user';
const __TBL_MSG__   = 'zeai_msg';
const __TBL_TREND__ = 'zeai_trend';
const __TBL_TREND_BBS__ = 'zeai_trend_bbs';
const __TBL_WXENDURL__ = 'zeai_wxendurl';
const __TBL_DATING__   = 'zeai_dating';
const __TBL_DATING_USER__ = 'zeai_dating_user';
const __TBL_PARTY__       = 'zeai_party';
const __TBL_PARTY_USER__ = 'zeai_party_user';
const __TBL_PARTY_BBS__  = 'zeai_party_bbs';
const __TBL_PARTY_SIGN__ = 'zeai_party_sign';
const __TBL_HONGBAO__      = 'zeai_hongbao';
const __TBL_HONGBAO_USER__ = 'zeai_hongbao_user';
const __TBL_CRM_HN__   = 'zeai_super';
const __TBL_HN_BBS__   = 'zeai_hn_bbs';
const __TBL_CRM_HT__   = 'zeai_crm_ht';
const __TBL_CRM_NEWS__ = 'zeai_crm_news';
const __TBL_CRM_MATCH__= 'zeai_crm_match';
const __TBL_CRM_BBS__= 'zeai_crm_bbs';
const __TBL_CRM_FAV__= 'zeai_crm_fav';
const __TBL_CRM_AGENT__= 'zeai_crm_agent';
const __TBL_CRM_CLAIM_LIST__= 'zeai_crm_claim_list';
const __TBL_GZH_MENU__= 'zeai_gzh_menu';
const __TBL_GROUP_MAIN__ = 'zeai_group_main';
const __TBL_GROUP_TOTAL__= 'zeai_group_total';
const __TBL_GROUP_USER__ = 'zeai_group_user';
const __TBL_GROUP_BK__ = 'zeai_group_bk';
const __TBL_GROUP_WZ__ = 'zeai_group_wz';
const __TBL_GROUP_WZ_BBS__ = 'zeai_group_wz_bbs';
const __TBL_GROUP_PHOTO__ = 'zeai_group_photo';
const __TBL_GROUP_PHOTO_KIND__ = 'zeai_group_photo_kind';
const __TBL_GROUP_CLUB__ = 'zeai_group_club';
const __TBL_GROUP_CLUB_USER__ = 'zeai_group_club_user';
const __TBL_GROUP_CLUB_BBS__ = 'zeai_group_club_bbs';
const __TBL_GROUP_CLUB_PHOTO__ = 'zeai_group_club_photo';
const __TBL_GROUP_LINKS__ = 'zeai_group_links';
const __TBL_TG_ROLE__ = 'zeai_tg_role';
const __TBL_TG_USER__ = 'zeai_tg_user';
const __TBL_TG_PRODUCT__ = 'zeai_tg_product';
const __TBL_TG_PRODUCT_KIND__ = 'zeai_tg_product_kind';
const __TBL_TG_GZ__ = 'zeai_tg_gz';
const __TBL_QIANXIAN__ = 'zeai_qianxian';
const __TBL_SHOP_YUYUE__ = 'zeai_shop_yuyue';
const __TBL_SHOP_FAV__ = 'zeai_shop_fav';
const __TBL_SHOP_ORDER__ = 'zeai_shop_order';
const __TBL_SHOP_SEARCH__ = 'zeai_shop_search';
const __TBL_FORM__ = 'zeai_form';
const __TBL_FORM_U__ = 'zeai_form_u';
$db = new db_zeai;
?>
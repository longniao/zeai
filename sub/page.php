<style type="text/css"><?php
$page_color = (empty($page_color))?'#009688':$page_color;
if ($page_skin == 1){
	$pagesH = 22;$pagesPADD = 6;$pagesFONT = '12px';
}elseif($page_skin == 2){
	$pagesH = 30;$pagesPADD = 10;$pagesFONT = '12px';
}elseif($page_skin == 3){
	$pagesH = 40;$pagesPADD = 16;$pagesFONT = '16px';
}elseif($page_skin == '4_yuan'){
	$pagesH = 40;$pagesPADD = 16;$pagesFONT = '16px';
}
?>
.pagebox{font-size:<?php echo $pagesFONT; ?>;clear:both;overflow:auto;display:inline-block;-webkit-user-select:none}
.pagebox span,
.pagebox a{color:#333;line-height:<?php echo $pagesH-2; ?>px;height:<?php echo $pagesH; ?>px;padding:0 <?php echo $pagesPADD; ?>px;text-align:center;border:#dfdfdf 1px solid;font-size:<?php echo $pagesFONT; ?>;box-sizing:border-box;text-decoration:none;display:inline-block;border-right:#fff 1px solid;-webkit-transition:all .3s;-moz-transition:all .3s;transition:all .3s}
.pagebox a{background-color:#fff}
.pagebox a:hover,
.pagebox a.ed{color:<?php echo $page_color;?>;border:<?php echo $page_color;?> 1px solid}
.pagebox .ed{color:#fff;background-color:<?php echo $page_color;?>;border:<?php echo $page_color;?> 1px solid}
.pagebox .disabled{color:#d2d2d2}
.pagebox .more1,.pagebox .more2{border:0;border-left:#dfdfdf 1px solid}
.pagebox .pagesN{border-right:#dfdfdf 1px solid}
.pagebox .pageInfo {border:0;font-size:<?php echo $pagesFONT; ?>}
.pagebox .pageInfo select{font-size:<?php echo $pagesFONT; ?>;height:<?php echo $pagesH;?>px;border:#dfdfdf 1px solid;background-color:#fff}
.pagebox .pageInfo b{color:#f00;font-weight:normal}
<?php if($page_skin == '4_yuan'){ ?>
.pagebox a{color:#888}
.pagebox a{border-radius:3px;border-right-color:#fff}
.pagebox .ed{border-radius:3px;border-color:#f0f0f0;border-right-color:#E83191}
<?php }?>
</style>
<?php 
!function_exists('cdstr') && exit('Forbidden');
class Zeaipage {
	private $page_name="p";
	private $pagesize=10;//每页显示条数
	private $total=0;//总记录数
	private $pagebarnum=7;//bar数
	public $totalpage=0;
	private $current_pageno=1;//当前页
	public function __construct($total,$pagesize=10) {		
		if((!is_int($total))||($total<0))die("记录总数错误！");
		if((!is_int($pagesize))||($pagesize<0))die("Pagesize错误！");
		$this->set("total",$total);
		$this->set("pagesize",$pagesize);
		$this->set('totalpage',ceil($total/$pagesize));
	}
	public function set($var,$value){
		if(in_array($var,get_object_vars($this)))
		   $this->$var=$value;
		else {
			throw new PB_Page_Exception("Error in set():".$var." does not belong to PB_Page!");
		}
	}
	/*
	public function get_linkhead() {
		$this->set_current_page();
		if(empty($_SERVER['QUERY_STRING'])){
			 $this->linkhead=$_SERVER['REQUEST_URI']."?".$this->page_name."=";
		}else{
			if(isset($_GET[$this->page_name])){                                
					$this->linkhead=str_replace($this->page_name.'='.$this->current_pageno,$this->page_name.'=',$_SERVER['REQUEST_URI']);
			} else {
					$this->linkhead=$_SERVER['REQUEST_URI'].'&'.$this->page_name.'=';
			}
		}
	}
	*/
	public function get_linkhead() {
		$this->set_current_page();
		if(empty($_SERVER['QUERY_STRING'])){
			 $this->linkhead=request_uri()."?".$this->page_name."=";
		}else{
			if(isset($_GET[$this->page_name])){                                
				$this->linkhead=str_replace($this->page_name.'='.$this->current_pageno,$this->page_name.'=',request_uri());
			} else {
				$this->linkhead=request_uri().'&'.$this->page_name.'=';
			}
		}
	}
	public function get_url($pageno=1){
		if(empty($this->linkhead))$this->get_linkhead();
		return str_replace($this->page_name.'=',$this->page_name.'='.$pageno,$this->linkhead);
	}
	/*当前页*/
	public function set_current_page($current_pageno=0) {
		if(empty($current_pageno)){
			if(isset($_GET[$this->page_name])){$this->current_pageno=intval($_GET[$this->page_name]);}
		}else{
			$this->current_pageno=intval($current_pageno);
		}
		if ($this->current_pageno>$this->totalpage)header("Location: ".$_SERVER['PHP_SELF']);//$this->current_pageno=1;
	}
	public function set_format($str) {return $this->format_left.$str.$this->format_right;}
	/* 获取显示"下一页"*/
	public function next_page() {
		if($this->current_pageno<$this->totalpage){
			return '<a href="'.$this->get_url($this->current_pageno+1).'" class="pagesN">'.$this->next_page.'</a>';
		}
		return '';
	}
	/*获取显示“上一页”*/
	public function pre_page() {
		if($this->current_pageno>1){return '<a href="'.$this->get_url($this->current_pageno-1).'" class="pagesP">'.$this->pre_page.'</a>';}
		return '';
	}
	/*获取显示“首页”*/
	public function first_page() {return '<a href="'.$this->get_url(1).'">'.$this->first_page."</a>";}
	/*获取显示“尾页”*/
	public function last_page() {return '<a href="'.$this->get_url($this->totalpage).'">'.$this->last_page.'</a>';}
	public function nowbar() {
		if ($this->totalpage > 1){
			$begin = $this->current_pageno-ceil($this->pagebarnum/2)+1;
			//echo 'begin='.$begin.'　　current_pageno='.$this->current_pageno.'　　　';
			$begin = ($begin >= 1)?$begin:1;
			$return= '';
			//
			$barend = $begin+$this->pagebarnum;//8
			if ($barend > $this->totalpage){
				$begin  = $this->totalpage - $this->pagebarnum;
				$barend = $this->totalpage + 1;	
				$begin = ($begin >= 1)?$begin:1;
			}
			//
			if ($begin >= 2){
				$return .= "<a href=".$this->get_url(1)." class=pages>1</a>";
				if ($begin >= 3)$return .= "<span class=more1>…</span>";
			}
			//echo 'begin='.$begin.'，barend='.$barend;
			for($i=$begin;$i<$barend;$i++){
				if($i<=$this->totalpage){
					if($i != $this->current_pageno){
						$return.="<a href=".$this->get_url($i)." class=pages>".$i.'</a>';
					}else {
						$return.='<span class=ed>'.$i.'</span>';
					}
				}else{
					break;
				}
			}
			//
			if ($this->totalpage - $barend > 2)$return .= "<span class=more2>…</span><a href=".$this->get_url($this->totalpage)." class=pages>".$this->totalpage."</a>";
			//
			unset($begin);
		}	
		return $return;
	}
	/*“上一页”*/
	public function pre_bar()	{
		if($this->current_pageno>ceil($this->pagebarnum/2)){
				$pageno=$this->current_pageno-$this->pagebarnum;
				if($pageno<=0)$pageno=1;
				return $this->set_format('<a href="'.$this->get_url($pageno).'">'.$this->pre_bar."</a>");
		}
		return $this->set_format('<a href="'.$this->get_url(1).'">'.$this->pre_bar."</a>");
	}
	/*“下一页”*/
	public function next_bar()	{
		if($this->current_pageno<$this->totalpage-ceil($this->pagebarnum/2)){
				$pageno=$this->current_pageno+$this->pagebarnum;
				return $this->set_format('<a href="'.$this->get_url($pageno).'">'.$this->next_bar."</a>");
		}
		return $this->set_format('<a href="'.$this->get_url($this->totalpage).'">'.$this->next_bar."</a>");
	}
	/*跳转*/
	public function select()	{
		$return='<select name="PB_Page_Select" onchange="window.location.href=\''.$this->linkhead.'\'+this.options[this.selectedIndex].value">';
		for($i=1;$i<=$this->totalpage;$i++){
			if($i==$this->current_pageno){
				$return.='<option value="'.$i.'" selected>'.$i.'</option>';
			}else{
				$return.='<option value="'.$i.'">'.$i.'</option>';
			}
		}
		$return.='</select>';
		return $return;
	}
	public function pagebar($mode=0){
		global $_ZEAI;
		$this->set_current_page();
		$this->get_linkhead();
		$this->pre_page ='上一页';
		$this->next_page='下一页';
		$return  = $this->pre_page().$this->nowbar().$this->next_page();
		switch ($mode) {
			case 0:break;
			case 1:$return .= '<span class=pageInfo>共<b>'.$this->totalpage.'</b>页　总数<b>'.$this->total.'</b>条</span>';break;
			case 2:if ($this->totalpage > $this->pagesize){$return .= '<span class=pageInfo>转到第 '.$this->select().' 页</span>';}break;
			case 3:
				if ($this->totalpage > $this->pagesize){$return .= '<span class=pageInfo>转到第 '.$this->select().' 页</span>';}
				$return .= '<span class=pageInfo>共<b>'.$this->totalpage.'</b>页　 总数<b>'.$this->total.'</b>条</span>';
			break;
		}
		return $return;
	}
}
function request_uri(){
if (isset($_SERVER['argv'])){
$uri = $_SERVER['PHP_SELF'] .(empty($_SERVER['argv'])?'':('?'. $_SERVER['argv'][0]));}else{
$uri = $_SERVER['PHP_SELF'] .(empty($_SERVER['QUERY_STRING'])?'':('?'. $_SERVER['QUERY_STRING']));}
return $_SERVER['REQUEST_URI'] = $uri;}
$pagemode = (empty($pagemode))?2:$pagemode;
if ($p<1 || empty($p))$p=1;$gylpage=new Zeaipage($total,$pagesize);$pagelist = $gylpage->pagebar($pagemode);
$db->data_seek($rt,($p-1)*$pagesize);
?>
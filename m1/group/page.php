<style type="text/css"><?php
$page_color = '#D83F74';
if ($page_stylesize == 2){
	$pagesH = 38;$pagesPADD = 16;$pagesMAT = 14;$pagesFONT = '16px';
}else{
	$pagesH = 20;$pagesPADD = 6;$pagesMAT = 4;$pagesFONT = '12px';
}
?>.page{margin:30px auto 50px auto;font-size:<?php echo $pagesFONT; ?>;clear:both;overflow:auto}
.pages,.pagesed,.pagesPN,.pageInfo{background:#fff;color:#999;display:inline-block;height:<?php echo $pagesH; ?>px;line-height:<?php echo ($pagesH+1); ?>px;margin:2px 3px;padding:0 <?php echo $pagesPADD; ?>px;text-align:center;border:#dfdfdf 1px solid;border-radius:1px;font-size:<?php echo $pagesFONT; ?>}
.pages{text-decoration:none;display:inline-block}
.pages:hover,.pagesed{color:<?php echo $page_color;?>;border:<?php echo $page_color;?> 1px solid}
.pagesed{color:#fff;background-color:<?php echo $page_color;?>;border:<?php echo $page_color;?> 1px solid}
.pagesPN span{display:inline-block}
.pagesPN:hover{color:<?php echo $page_color;?>;border:<?php echo $page_color; ?> 1px solid}

.pn1,.pn2{width:6px;height:11px;background:url("/images/pagejt.gif");margin-top:<?php echo $pagesMAT; ?>px}
.pn1{background-position:left top;margin-right:6px;}
.pn2{background-position:-6px top;margin-left:6px;}
/*.pageInfo span{color:<?php echo $page_color;?>}*/
</style>
<?php 
!function_exists('cdstr') && exit('Forbidden');
class zeaipage {
	private $page_name="p";
	private $pagesize=10;//每页显示记录条数
	private $total=0;//总的记录数
	private $pagebarnum=10;//bar数。
	private $totalpage=0;
	private $linkhead="";//url地址头
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
		if ($this->current_pageno>$this->totalpage)header("Location: ".$_SERVER['PHP_SELF']);//$this->current_pageno=1
	}
	public function set_format($str) {return $this->format_left.$str.$this->format_right;}
	/* 获取显示"下一页"*/
	public function next_page() {
		if($this->current_pageno<$this->totalpage){
			return '<a href="'.$this->get_url($this->current_pageno+1).'" class="pagesPN">'.$this->next_page.'</a>';
		}
		return '';
	}
	/*获取显示“上一页”*/
	public function pre_page() {
		if($this->current_pageno>1){return '<a href="'.$this->get_url($this->current_pageno-1).'" class="pagesPN">'.$this->pre_page.'</a>';}
		return '';
	}
	/*获取显示“首页”*/
	public function first_page() {return '<a href="'.$this->get_url(1).'">'.$this->first_page."</a>";}
	/*获取显示“尾页”*/
	public function last_page() {return '<a href="'.$this->get_url($this->totalpage).'">'.$this->last_page.'</a>';}
	public function nowbar() {
		if ($this->totalpage > 1){
			$begin=$this->current_pageno-ceil($this->pagebarnum/2);
			$begin=($begin>=1)?$begin:1;
			$return='';
			for($i=$begin;$i<$begin+$this->pagebarnum;$i++){
				if($i<=$this->totalpage){
					if($i!=$this->current_pageno){
						$return.="<a href=".$this->get_url($i)." class=pages>".$i.'</a>';
					}else {
						$return.='<div class=pagesed>'.$i.'</div>';
					}
				}else{
					break;
				}
			}
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
	public function pagebar($mode=1){
		global $_ZEAI;
		$this->set_current_page();
		$this->get_linkhead();
		//return ('共有<font color=red><b>'.$this->total.'</b></font>条记录。');
		switch ($mode) {
			case 1:
				$this->pre_page ='<span class="pn">上一页</span>';
				$this->next_page='<span class="pn">下一页</span>';
				$return  = $this->pre_page().$this->nowbar();
				$return .= $this->next_page();
				if ($this->totalpage > $this->pagesize){
					$return .= '<div class=pageInfo>第<span>'.$this->current_pageno.'</span>页/共<span>'.$this->totalpage.'</span>页</div>';
				}
				return $return;
			break;
		}
	}
}
function request_uri(){
if (isset($_SERVER['argv'])){
$uri = $_SERVER['PHP_SELF'] .(empty($_SERVER['argv'])?'':('?'. $_SERVER['argv'][0]));
}else{
$uri = $_SERVER['PHP_SELF'] .(empty($_SERVER['QUERY_STRING'])?'':('?'. $_SERVER['QUERY_STRING']));}
return $_SERVER['REQUEST_URI'] = $uri;}
?>
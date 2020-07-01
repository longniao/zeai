<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
if(!in_array('wxewm',$QXARR))exit(noauth());
require_once ZEAI.'sub/zeai_up_func.php';
switch ($submitok) {
	case"alldel":
		if(empty($list))json_exit(array('flag'=>0,'msg'=>'请选择您要删除的信息'));
		if(!is_array($list))exit(JSON_ERROR);
		if(count($list)>=1){
			foreach($list as $v){
				$uid=intval($v);
				$row = $db->ROW(__TBL_USER__,"weixin_pic,nickname","id=".$uid);
				if ($row){
					$path_b = $row[0];$nickname = $row[1];
					@up_send_admindel($path_b);
					$db->query("UPDATE ".__TBL_USER__." SET weixin_pic='' WHERE id=".$uid);
					AddLog('【微信二维码】会员【'.$nickname.'（uid:'.$uid.'）】->删除');
				}
			}
		}
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js"></script>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<style>
.tablelist{min-width:900px;margin:20px 20px 50px 20px}
.tablelist td:hover{background-color:#fff}
/*picadd*/
.picli{width:100%;margin:10px auto;display:-webkit-flex;display:flex;-webkit-flex-wrap:wrap;flex-wrap:wrap;-webkit-justify-content:space-between;justify-content:initial}
.picli li{width:130px;height:165px;line-height:100px;margin:10px 40px 30px 15px;background-color:#fff;position:relative;text-align:center;box-shadow:0 0 10px rgba(0,0,0,0.2);}
.picli li.pf0{height:190px}
.picli li.flag0{background-color:#ffa}
.picli .add,.picli li .del{background-image:url('images/picadd.png?3');background-size:120px 80px;background-repeat:no-repeat}
.picli li:hover{background-color:#F2F9FD}
.picli li img{vertical-align:middle;max-width:100px;max-height:100px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;}
.picli li:hover .img{border:#fff 1px solid;cursor:zoom-in}
.picli li .del{width:20px;height:20px;top:-10px;right:-10px;position:absolute;border-radius:10px;background-position:-80px top;display:block;box-shadow:0 0 5px rgba(0,0,0,0.3)}
.picli li .del:hover{background-position:-100px top;cursor:pointer}
.picli li a.pic{display:block;padding:5px 0}
.picli li .f0{background-color:#ffc}
.picli li .loveb,
.picli li .nickname,
.picli li .chekbox{line-height:24px;height:24px;font-size:12px;color:#ddd;border-top:#eee 1px solid;font-family:'Verdana';overflow:hidden}
.picli li .chekbox{padding:2px 5px}
.picli li .loveb{line-height:30px}
.picli li .chekbox .l{float:left}
.picli li .chekbox .l a{margin:0 12px 0 0}
.picli li .chekbox .l a:hover{filter:alpha(opacity=60);-moz-opacity:0.6;opacity:0.6}
.picli li .chekbox .r{float:right}
.picli li a span{display:block;width:100%;line-height:24px;position:absolute;top:28px;background-color:rgba(0,0,0,0.5);color:#aaa;font-size:12px}
.picli li .flagstr{width:30px;line-height:20px;color:#fff;font-size:12px;position:absolute;top:6px;left:-2px;background-color:#f70}
</style>
<?php
?>
<body>
<div class="navbox">
    <a  class="ed">会员微信二维码<?php echo '<b>'.$db->COUNT(__TBL_USER__,"weixin_pic<>''").'</b>';?></a>
    <div class="Rsobox">
    
    <form name="ZEAI_CN__form1" method="get" action="<?php echo $SELF; ?>">
        <input name="Skeyword" type="text" id="Skeyword" maxlength="25" class="input size2 W150" placeholder="按UID/用户名/昵称" value="<?php echo $Skeyword; ?>">
        <input type="hidden" name="p" value="<?php echo $p;?>" />
        <input type="submit" value="搜索" class="btn size2 QING" />
    </form>    
  </div>
<div class="clear"></div></div>
<div class="fixedblank"></div>

<?php
$SQL = "";
$Skeyword = trimm($Skeyword);
if (ifint($Skeyword)){
	$SQL = " AND (id=$Skeyword) ";
}elseif(!empty($Skeyword)){
	$SQL = " AND ( ( uname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".$Skeyword."%' ) OR ( nickname LIKE '%".urlencode($Skeyword)."%' ) )";
}

$rt = $db->query("SELECT id,uname,nickname,sex,grade,weixin_pic FROM ".__TBL_USER__." WHERE  weixin_pic<>'' ".$SQL." ORDER BY refresh_time DESC LIMIT ".$_ADM['admLimit']);
$total = $db->num_rows($rt);
if ($total <= 0 ) {
	echo "<div class='nodataico'><i></i>暂无内容<br><a class='aHUANGed' href='javascript:history.back(-1)'>返回</a></div>";
} else {
	$page_skin=2;$pagemode=3;$pagesize=27;require_once ZEAI.'sub/page.php';
?>
    <form id="www_zeai_cn_FORM" method="get" action="<?php echo $SELF; ?>">
    <table class="tablelist">
    <tr>
    <th width="20"><input type="checkbox" id="checkboxall1" class="checkskin checkboxall"><label for="checkboxall1" class="checkskin-label"><i class="i1"></i><b class="W50">全选</b></label></th>
    <th>&nbsp;</th>
    </tr>
   
    <tr>
    <td colspan="2">

      <div class="picli">
        <?php
        for($i=1;$i<=$pagesize;$i++) {
            $rows      = $db->fetch_array($rt);if(!$rows) break;
            $id        = $rows['id'];
            $uid       = $id;
			$uname     = dataIO($rows['uname'],'out');
			$nickname  = dataIO($rows['nickname'],'out');
			$sex       = $rows['sex'];
			$grade     = $rows['grade'];
            $weixin_pic= $rows['weixin_pic'];
			//
			if(empty($nickname))$nickname = $uname;
			$nickname = str_replace($Skeyword,"<b class='Cf00'>".$Skeyword."</b>",$nickname);
			//
			$dst_s    = $_ZEAI['up2'].'/'.$weixin_pic;
			$dst_b    = $dst_s;
			$flagbg   = ($flag == 'W　w w .z e 　a i　.c n')?' class="flag0"':'';
			$href     = Href('u',$uid);
    	?>
        <li id="tr<?php echo $id;?>" class="fadeInUp">
        
          <a href="javascript:;" onClick="parent.piczoom('<?php echo $dst_b; ?>')" class="pic"><img src="<?php echo $dst_s; ?>" class="img"></a>
          <a href="javascript:;" class="del" pid="<?php echo $id;?>"></a>
          <?php echo $flagstr; ?>
          
          <div class="nickname"><a href="<?php echo $href; ?>" target="_blank"><?php echo uicon($sex.$grade) ?> <?php echo $nickname; ?></a></div>
          <div class="chekbox">
          
            <div class="l">
                <a href="javascript:send_msg(<?php echo $uid; ?>,'<?php echo $nickname; ?>');" title="发送消息"><img src="images/chat.gif"></a>

            </div>
              
            <div class="r">
              <input type="checkbox" class="checkskin" name="list[]" value="<?php echo $id; ?>" id="chk<?php echo $id; ?>" onclick="chkbox(<?php echo $i; ?>,<?php echo $id; ?>)"><label for="chk<?php echo $rows['id']; ?>" class="checkskin-label"><i class="i1"></i></label>
             </div>
            </div>

          </li>
        <?php }//end for ?>
      </div>
    </td>
    </tr>
        
	
    <tfoot><tr>
    <td colspan="2">
<!--    <input type="hidden" name="submitok" id="submitok" value="批量删除" />
-->	<input type="checkbox" id="checkboxall2" class="checkskin checkboxall"><label for="checkboxall2" class="checkskin-label"><i class="i1"></i></label>　
    <button type="button" class="btn size2 HEI2 disabled action" id="btndellist">批量删除</button>　
    <button type="button" id="btnsend" value="" class="btn size2 disabled action" onClick="sendTipFn(this);">发送消息</button>
    
	<?php if ($total > $pagesize)echo '<div class="pagebox FR">'.$pagelist.'</div>'; ?></div>
    </td>
    </tr></tfoot>
</table>
</form>
<script>
var bg       = '<?php echo $_Style['list_bg']; ?>';
var overbg   = '<?php echo $_Style['list_overbg']; ?>';
var selectbg = '<?php echo $_Style['list_selectbg']; ?>';
o('btndellist').onclick = function() {
	allList({
		btnobj:this,
		url:'wxewm'+zeai.ajxext+'submitok=alldel',
		title:'批量删除',
		msg:'正在删除中...',
		ifjson:true,
		ifconfirm:true
	});	
}
zeai.listEach('.del',function(obj){
	var id = parseInt(obj.getAttribute("pid"));
	obj.onclick = function(){
		zeai.confirm('真的要删除么？',function(){
			zeai.ajax('wxewm'+zeai.ajxext+'submitok=alldel&list[]='+id,function(e){var rs=zeai.jsoneval(e);
				if (rs.flag == 1){location.reload(true);}else{zeai.msg(rs.msg);}
			});
		});
	}
});
function send_msg(uid,nkname) {zeai.iframe('发送消息','u_tip.php?ulist='+uid,600,500);}
</script>
<script src="js/zeai_tablelist.js"></script>
<?php }?>
<br><br><br>
<?php require_once 'bottomadm.php';?>
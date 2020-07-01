<?php
require_once '../sub/init.php';
$submitokarr=array('tip_list','tip_add','tip_mod','tip_del','ajax_tip_list_save','ajax_tip_list_add_save','ajax_tip_list_mod_save','SSSSSSS');
if ( empty($ulist) && !in_array($submitok,$submitokarr) )textmsg('forbidden');
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_adm_tip.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
if ($submitok == 'ajax_tip_list_save'){
	$ARR=json_decode($_TIP['adm_list'],true);
	if (count($ARR) >= 1 && is_array($ARR) && !empty($tip_list)){
		$tip_list=explode(',',$tip_list);
		$arrLI=array();
		foreach ($tip_list as $i) {
			$a=array();
			$a['i']= arrT($_TIP['adm_list'],$i,'i');
			$a['t']= arrT($_TIP['adm_list'],$i,'t');
			$a['cc']= arrT($_TIP['adm_list'],$i,'cc');
			$arrLI[]=$a;
		}
		$_TIP['adm_list'] = encode_json($arrLI);
	}
	cache_mod_config($_TIP,'config_adm_tip','_TIP');
	AddLog('排序->【自定义消息模版】');
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok == 'ajax_tip_list_add_save' || $submitok == 'ajax_tip_list_mod_save'){
	if(empty($_TIP['adm_list']))json_exit(array('flag'=>0,'msg'=>'配置文件为空，估计跑路了'));
	$ARR=json_decode($_TIP['adm_list'],true);
	if (!is_array($ARR) && count($ARR)==0)json_exit(array('flag'=>0,'msg'=>'配置文件为空，估计跑路了'));
	if (str_len($title) > 0 && str_len($content) > 0 && str_len($content) <1000){
		$title   = dataIO($title,'in',100);
		$content = dataIO($content,'in',1000);
	}else{
		json_exit(array('flag'=>0,'msg'=>'内容必须在1000字节以内'));
	}
	
	if($submitok == 'ajax_tip_list_mod_save'){
		if (!ifint($i))json_exit(array('flag'=>0,'msg'=>'ID跑路了'));
		foreach ($ARR as $V) {
			$newarr=array();
			if($V['i']==$i){
				$newarr['i']=$i;
				$newarr['t']=$title;
				$newarr['cc']=$content;
			}else{
				$newarr=$V;
			}
			$adm_list[]=$newarr;
		}
		AddLog('修改->【自定义消息模版id:'.$i.'】');
	}elseif($submitok == 'ajax_tip_list_add_save'){
		$i=count($ARR)+1;
		$ARR[]=array("i"=>$i,"t"=>$title,"cc"=>$content);
		$adm_list=$ARR;
		AddLog('新增->【自定义消息模版id:'.$i.'】');
	}
	$_TIP['adm_list']=encode_json($adm_list);
	cache_mod_config($_TIP,'config_adm_tip','_TIP');
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok == 'tip_del'){
	if (!ifint($i))json_exit(array('flag'=>0,'msg'=>'ID跑路了'));
	if(empty($_TIP['adm_list']))json_exit(array('flag'=>0,'msg'=>'配置文件为空，估计跑路了'));
	$ARR=json_decode($_TIP['adm_list'],true);
	if (!is_array($ARR) && count($ARR)==0)json_exit(array('flag'=>0,'msg'=>'配置文件为空，估计跑路了'));
	foreach ($ARR as $V) {
		$newarr=array();
		if($V['i']==$i){
			continue;
		}else{
			$newarr=$V;
		}
		$adm_list[]=$newarr;
	}
	$_TIP['adm_list']=encode_json($adm_list);
	cache_mod_config($_TIP,'config_adm_tip','_TIP');
	AddLog('删除->【自定义消息模版id:'.$i.'】');
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}

if ($submitok == 'sendupdate'){
	$tmeplist = explode('_',$ulist);
	if(count($tmeplist)>=1){
		
		if (str_len($title) > 0 && str_len($content) > 0 && str_len($content) <1000){
			$title   = dataIO($title,'in',100);
			//$content = dataIO($content,'in',1000);
		}else{
			textmsg('请输入发送内容1000字节以内');
		}
		foreach($tmeplist as $uid){
			if ( !ifint($uid) )textmsg('uid：'.$uid.'不存在');
			if($kind == 'TG'){
					$logstr='推广员/买家/'.$_SHOP['title'];
					$tguid=$uid;
					$row = $db->ROW(__TBL_TG_USER__,'openid,subscribe,nickname,shopflag,title',"id=".$tguid,"num");
					if(!$row){
						textmsg('ID：'.$tguid.'不存在，发送中断');
					}else{
						$openid = $row[0];$subscribe = $row[1];$nickname = $row[2];$shopflag = $row[3];$ctitle = $row[4];
						$nickname=($ifshop==1)?$ctitle:$nickname;
						if($ifshop==1){
							$nickname = $ctitle;
							$ukind=$_SHOP['title'].'/买家';
							$msgkind='shop';
						}else{
							$ukind='推广员';
							$msgkind='tg';
						}
					}
					//站内消息
					$db->SendTip($tguid,$title,dataIO($content,'in',1000),$msgkind);
					
					AddLog('给'.$ukind.'【'.$nickname.'（ID:'.$tguid.'）】发送消息，内容->'.dataIO($content,'in',1000));
					//微信通知
					if (!empty($openid) && $subscribe==1){
						//客服通知
						$C = urlencode($content);
						$ret = @wx_kf_sent($openid,$C,'text');
						$ret = json_decode($ret);
						echo '<font color="#fff">'.$ret->errmsg.'</font>';
						//模版通知
						if ($ret->errmsg != 'ok'){
							$keyword1  = $C;
							$keyword3  = urlencode($_ZEAI['siteName']);
							//$url       = urlencode(mHref('my_tz'));
							@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
						}
					}
			}else{
					$row = $db->ROW(__TBL_USER__,'openid,subscribe,nickname',"id=".$uid,"num");
					if(!$row){
						textmsg('uid：'.$uid.'不存在，发送中断');
					}else{
						$openid = $row[0];$subscribe = $row[1];$nickname = $row[2];
					}
					//站内消息
					$db->SendTip($uid,$title,dataIO($content,'in',1000),'sys');
					AddLog('给会员【'.$nickname.'（uid:'.$uid.'）】发送消息，内容->'.dataIO($content,'in',1000));
					//微信通知
					if ($ifwxmb == 1 && !empty($openid) && $subscribe==1){
						//客服通知
						$C = urlencode($content);
						$ret = @wx_kf_sent($openid,$C,'text');
						$ret = json_decode($ret);
						echo '<font color="#fff">'.$ret->errmsg.'</font>';
						//模版通知
						if ($ret->errmsg != 'ok'){
							$keyword1  = $C;
							$keyword3  = urlencode($_ZEAI['siteName']);
							$url       = urlencode(mHref('my_tz'));
							@wx_mb_sent('mbbh=ZEAI_ADMIN_INFO&openid='.$openid.'&keyword1='.$keyword1.'&keyword3='.$keyword3.'&time='.ADDTIME.'&first='.$first.'&remark='.$remark.'&url='.$url);
						}
					}
			}
			
		}
		$sussess = '发送成功!';
	}
}else{
	$sussess = '';
}
$ulist_str = str_replace("_"," , ",$ulist);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<script src="js/Sortable1.6.1.js"></script>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="css/crm.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
</head>
<body>
<?php
if ($submitok=='tip_list'){
	?>
	<style>
    .tip_list{font-size:14px}
    .tip_list a.tr{text-align:left;position:relative;display:block;line-height:40px;height:40px;padding:0 20px;border-bottom:#eee 1px solid}
    .tip_list a.tr:hover{background-color:#F9F9FA}
	.tip_list a.tr span{width:75%;display:inline-block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
	.tip_list a.tr i{position:absolute;right:20px;top:0}
	.tip_list a.tr i.editico{position:absolute;right:60px;top:0}
    </style>
    <div class="tip_list" id="stepbox2">
        <form id="ZeaiForm">
        <div class="stepli" title="【单击选择】【按住不放可上下拖动调序】">
        <?php
        $a = json_decode($_TIP['adm_list']);
        if (is_array($a) && count($a)>0){
            for($j=0;$j<count($a);$j++) {
                $id2    = $a[$j]->i;
                $value2 = $a[$j]->t;?>
                <a class="tr" i="<?php echo $id2;?>" t="<?php echo $a[$j]->t;?>" cc="<?php echo $a[$j]->cc;?>"><span><font  class="C999"><?php echo $id2;?>．</font><?php echo $value2;?></span><i class="editico" title="修改"></i><i class="delico" title="删除"></i></a>
                <?php
            }
            ?>
            <input name="tip_list" id="tip_list" type="hidden" value="">
            <input name="submitok" type="hidden" value="ajax_tip_list_save">
            <br><br><br><br><br><div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">保存排序</button></div>
            <script>
            function drag_init2(){(function (){[].forEach.call(stepbox2.getElementsByClassName('stepli'), function (el){Sortable.create(el,{group: 'zeai_tip_list',animation:150});});})();}drag_init2();
            save.onclick = function(){	
                var DATAPX=[];
                zeai.listEach('.tr',function(obj){DATAPX.push(obj.getAttribute("i"));});
                tip_list.value=DATAPX.join(",");
                zeai.ajax({url:'u_tip'+zeai.extname,form:ZeaiForm},function(e){var rs=zeai.jsoneval(e);
                    zeai.msg(rs.msg);if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
                });
            }
			zeai.listEach('.tr',function(obj){
				var i = parseInt(obj.getAttribute("i")),t = obj.getAttribute("t"),cc = obj.getAttribute("cc");
				obj.onclick = function(){
					parent.title.value=zeai.html_decode(t);
					parent.content.value=html_encode(zeai.html_decode(cc));
					parent.zeai.iframe(0);
				}
			});
			function html_encode(str){
				str = str.replace(/<br>/g, '\n'); 
				return str;  
			}			
			zeai.listEach('.editico',function(obj){
				obj.onclick = function(e){
					e.stopPropagation();
					var i = parseInt(obj.parentNode.getAttribute("i"));
					zeai.openurl('u_tip'+zeai.ajxext+'submitok=tip_mod&i='+i);
				}
			});
			zeai.listEach('.delico',function(obj){
				obj.onclick = function(e){
					e.stopPropagation();
					var i = parseInt(obj.parentNode.getAttribute("i"));
					zeai.confirm('确定要删除么？',function(){
						zeai.ajax({url:'u_tip'+zeai.ajxext+'submitok=tip_del&i='+i},function(e){var rs=zeai.jsoneval(e);
							zeai.msg(0);zeai.msg(rs.msg);
							if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
						});
					});
				}
			});
            </script>
            
            <?php
        }else{
            echo "<br><br><br><br><div class='nodataicoS'><i></i>暂无内容<br><br><a class='aHUANGed' href='u_tip.php?submitok=tip_add'><span class='ico add'>&#xe620;</span> 新增</a></div>";
        }?>
        </div>
        </form>
    </div>
<?php exit;}elseif($submitok=='tip_add' || $submitok=='tip_mod'){
	if($submitok=='tip_mod'){
		if (!ifint($i))textmsg('ID跑路了');
		$title   = arrT($_TIP['adm_list'],$i,'t');
		$content = arrT($_TIP['adm_list'],$i,'cc');
	}
?>
<style>
.table0{width:90%;margin:20px auto 0 auto}
.table0 td{font-size:14px;padding:10px 5px}
.table0 td:hover{background:none}
.table0 .tdL{width:40px;font-size:14px;color:#666;background:none}
.table0 .input{width:300px;font-size:12px}
.table0 textarea{width:300px;height:180px;font-size:12px}
</style>
<form id="www_zeai_cn_FORM" method="post" action="<?php echo SELF; ?>">
    <table class="table0">
    <tr>
    <td class="tdL">标题</td>
    <td class="tdR"><input name="title" type="text" required class="size2 input" style="padding:0 5px" id="title" maxlength="50" value="<?php echo $title;?>"></td>
    </tr>
    <tr>
    <td valign="top" class="tdL">内容</td>
    <td class="tdR"><textarea name="content" id="content" class="S14"><?php echo dataIO($content,'wx');?></textarea></td>
    </tr>
    <td colspan="2" valign="top" class="S12 C999">超链接代码：<input type="text" value='<a href="链接网址">链接文字</a>' style="background-color:#fff;font-family:Verdana, Geneva, sans-serif;text-align:center;width:60%;border:0;display:inline-block"></td>
    </tr>
    <tr>
    <td height="60" colspan="2" class="center"></td>
    </tr>
    </table>
    <input type="hidden" name="submitok" value="<?php echo ($submitok == 'tip_mod')?'ajax_tip_list_mod_save':'ajax_tip_list_add_save';?>">
    <input type="hidden" name="i" value="<?php echo $i;?>">
    <div class="savebtnbox"><button type="button" class="btn size3 HUANG3" onclick="chkform()">保存</button></div>
</form>
<script>
    function chkform(){
        if(zeai.str_len(title.value)<1 || zeai.str_len(title.value)>100){
            zeai.msg('消息标题1~100个字节',title);
            return false;
        }else if( zeai.str_len(content.value)<1 || zeai.str_len(content.value)>1000 ){
            zeai.msg('消息内容1~1000个字节',content);
            return false;
        }else{
			zeai.ajax({url:'u_tip'+zeai.extname,form:www_zeai_cn_FORM},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(rs.msg);if(rs.flag==1){setTimeout(function(){zeai.openurl('u_tip'+zeai.ajxext+'submitok=tip_list')},1000);}
			});
        }
    }
</script>
<?php exit;}?>

<!--常规-->
<?php if (empty($sussess)){?>
<script>
function chkform(){
	if(zeai.str_len(title.value)<1 || zeai.str_len(title.value)>100){
		zeai.msg('消息标题1~100个字节',title);
		return false;
	}else if( zeai.str_len(content.value)<1 || zeai.str_len(content.value)>1000 ){
		zeai.msg('消息内容1~1000个字节',content);
		return false;
	}else{
		parent.zeai.confirm('请仔细检查发送内容，一经发送不可逆转',function(){zeai.msg('发送中... 请不要关闭窗口',{time:100});www_zeai_cn_FORM.submit();})
	}
}
</script>
<?php }?>

<?php if (empty($sussess)){?>
<style>
.table0{width:90%;margin:20px auto}
.table0 td{font-size:14px;padding:10px 5px}
.table0 td:hover{background:none}
.table0 .tdL{width:70px;font-size:14px;color:#666;background:none}
.table0 .input{width:400px}
.table0 textarea{width:400px;height:150px}
</style>
<?php }else{?>
<style>
.sussesstips{width:300px;margin:0 auto;padding-top:100px;font-size:24px;text-align:center}
</style>
<?php }?>

<?php if (!empty($sussess)){echo '<div class="sussesstips"><img src="images/sussess.png"><br><br>'.$sussess.'<br><br><a class="aLAN" href="javascript:history.back(-1);">再发一条</a></div>';exit;}?>
<form id="www_zeai_cn_FORM" method="post" action="<?php echo SELF; ?>">
<table class="table0">
<tr>
<td class="tdL">接收人</td>
<td class="tdR"><?php echo $ulist_str; ?></td>
</tr>
<tr>
<td class="tdL">消息标题</td>
<td class="tdR"><input name="title" type="text" required class="size2 W80_" id="title" maxlength="50" style="padding:0 5px"> <button type="button" class="btn size2" id="tipselect">选择模版</button></td>
</tr>
<tr>
<td valign="top" class="tdL">消息内容</td>
<td class="tdR"><textarea name="content" id="content" class="S14" style="width:80%;vertical-align:middle"></textarea></td>
</tr>
<tr>
<td valign="top" class="tdL">&nbsp;</td>
<td class="tdR C8d">
<input type="checkbox" name="ifwxmb" id="ifwxmb" class="checkskin" value="1" checked><label for="ifwxmb" class="checkskin-label"><i></i><b class="W80 S14 C666">微信通知</b></label>
<img src="images/!.png" width="14" height="14" valign="middle"> <font style="vertical-align:middle" class="C999 S12">请勿连续向同一人发送超过10次,以防被腾讯屏蔽</font>
</td>
</tr>
<tr>
<td height="60" colspan="2" class="center"></td>
</tr>
</table>
<input type="hidden" name="submitok" value="sendupdate">
<input type="hidden" name="ulist" value="<?php echo $ulist; ?>">
<input type="hidden" name="kind" value="<?php echo $kind; ?>">
<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" onclick="chkform()">开始发送</button></div>
</form>
<script>
tipselect.onclick=function(){
	zeai.iframe('【自定义消息】'+
		'<div class="iframeAbox modedatabox" id="modedatabox">'+
		'<a onclick="iframeA(\'modedatabox\',\'u_tip.php?submitok=tip_list\',this);" class="ed"><i class="ico add">&#xe64b;</i> 列表</a>'+
		'<a onclick="iframeA(\'modedatabox\',\'u_tip.php?submitok=tip_add\',this);"><i class="ico add">&#xe620;</i> 新增</a>'+
		'</div>','u_tip.php?submitok=tip_list',420,445);
}
function iframeA(boxid,url,that){
	iframeAreset();
	that.class('ed');
	o('iframe_iframe').src=url;
	function iframeAreset(){zeai.listEach(zeai.tag(o(boxid),'a'),function(obj){obj.removeClass('ed');});}
}
</script>
</body>
</html>
<?php ob_end_flush();?>
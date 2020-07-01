<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_wxgzh.php';
$urole = json_decode($_ZEAI['urole'],true);
if($submitok == 'ajax_qunfa'){
	$rt=$db->query("SELECT openid FROM ".__TBL_USER__." WHERE openid<>'' AND subscribe=1 ORDER BY refresh_time DESC");// AND (id=99636 OR id=97237)
	$total = $db->num_rows($rt);
	if ($total == 0) {
		json_exit(array('flag'=>0,'msg'=>'暂时还没有会员关注公众号，发送中止'));
	} else {
		for($i=1;$i<=$total;$i++) {
			$rows = $db->fetch_array($rt,'num');
			if(!$rows) break;
			$openid = $rows[0];
			if($_GZH['wx_gzh_qf_kind'] == 'text'){
				if (!empty($_GZH['wx_gzh_qf_text_C'])){
					@wx_sent_kf_msg($openid,addslashes($_GZH['wx_gzh_qf_text_C']),'text');
				}else{
					json_exit(array('flag'=>0,'msg'=>'发送内容不能为空'));
				}
			}elseif($_GZH['wx_gzh_qf_kind'] == 'pic'){
				$title   = $_GZH['wx_gzh_qf_pic_title'];
				$content = dataIO($_GZH['wx_gzh_qf_pic_C'],'wx');
				$picurl  = $_ZEAI['up2']."/".$_GZH['wx_gzh_qf_pic_path'];
				$url     = $_GZH['wx_gzh_qf_pic_url'];
				if (empty($_GZH['wx_gzh_qf_pic_title']) || empty($_GZH['wx_gzh_qf_pic_path'])){
					json_exit(array('flag'=>0,'msg'=>'发送内容和图片不能为空'));
				}else{
					$news_list[] = array('title'=>$title,'description'=>$content,'picurl'=>$picurl,'url'=>$url);
					@wx_sent_kf_msg($openid,$news_list,'news');
				}
			}
		}
		AddLog('【公众号推送】->群发');
		json_exit(array('flag'=>1,'msg'=>'发送成功（'.$total.'条）'));
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<script src="../res/www_zeai_cn.js"></script>
<body>
<div class="navbox">
	<?php if ($t == 1){?><a href="<?php echo SELF;?>?t=1"<?php echo ($t == 1 || empty($t))?' class="ed"':'';?> >微信公众号推送</a><?php }?>
	<?php if ($t == 2){?><a href="<?php echo SELF;?>?t=2"<?php echo ($t == 2)?' class="ed"':'';?> >微信公众号群发</a><?php }?>
</div>
<div class="fixedblank"></div>
<form id="ZEAIFORM" name="ZEAIFORM" method="post">
<!------------------微信公众号推送------------------>
<?php if ($t == 1){
	
if(!in_array('wx_gzh_push',$QXARR))exit(noauth());	
	
?>
    <table class="table size1 W1200" style="margin:20px 60px 0 20px">
        <tr><th align="left" colspan="2">会员自主触发推送　<span class="tips">（说明：1．此推送只有【新会员关注公众号】时和点【我的】触发推送　2．微信官方规定会员关注公众号时或与公众号发生交互48小时以内有效）</span></th></tr>
        <tr>
        	<?php $pushkind = (empty($pushkind))?$_GZH['wx_gzh_push_kind']:$pushkind; ?>
            <td class="tdL">推送类型</td>
            <td class="tdR">　
                <input type="radio" class="radioskin" name="wx_gzh_push_kind" id="ts1" value="text"<?php echo ($pushkind == 'text')?' checked':'';?> onClick="zeai.openurl('<?php echo $SELF; ?>?t=1&pushkind=text')"><label for="ts1" class="radioskin-label"><i class="i1"></i><b class="W50 S12">文本</b></label>
                <input type="radio" class="radioskin" name="wx_gzh_push_kind" id="ts2" value="pic"<?php echo ($pushkind == 'pic')?' checked':'';?> onClick="zeai.openurl('<?php echo $SELF; ?>?t=1&pushkind=pic')"><label for="ts2" class="radioskin-label"><i class="i1"></i><b class="W50 S12">图文</b></label>
<!--                <input style="display:none" type="radio" class="radioskin" name="wx_gzh_push_kind" id="ts3" value="ulist"<?php echo ($pushkind == 'ulist')?' checked':'';?> onClick="zeai.openurl('<?php echo $SELF; ?>?t=1&pushkind=ulist')"><label for="ts3" class="radioskin-label"><i class="i1"></i><b class="W100 S12">推荐会员信息列表</b></label>
-->            </td>
        </tr>
        <?php if (!empty($pushkind)){ ?>
        <tr>
            <td class="tdL"><?php if ($pushkind == 'text'){echo'文本';}elseif($pushkind == 'pic'){echo'图文';}elseif($pushkind == 'ulist'){echo'会员列表';}?>推送信息</td>
            <td class="tdR">
				<?php if ($pushkind == 'text'){ ?>
                    <textarea name="wx_gzh_push_text_C" cols="50" rows="8" class="textarea W400"><?php echo dataIO($_GZH['wx_gzh_push_text_C'],'wx');?></textarea>
              		<span class="C999">1.内容请控制点在400字节以内<br>2.如果要加超链接源码，请在官方客服指导下进行修改，以防程序出错</span>
                <?php }elseif($pushkind == 'pic'){  ?>
       		  <table class="table0 FL">
                    <tr><td width="30" align="right">标题</td><td align="left"><input name="wx_gzh_push_pic_title" type="text" class="W300" value="<?php echo stripslashes($_GZH['wx_gzh_push_pic_title']);?>" maxlength="200"><span class="tips">100字节以内</span></td></tr>
                    <tr><td width="30" align="right">图片</td><td align="left">
                      <?php if (!empty($_GZH['wx_gzh_push_pic_path'])) {?>
                      <img src="<?php echo $_ZEAI['up2']."/".$_GZH['wx_gzh_push_pic_path']; ?>" class="zoom" align="absmiddle" width="200" height="111" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".$_GZH['wx_gzh_push_pic_path']; ?>')">　<a href="javascript:;" id='delpicupdate1' class="btn size1">删除</a> <span class="tips" style="vertical-align:middle">（删除图片后可重新上传）</span>
                          <?php }else{echo "<input name=pic1 type=file size=30 class=W300 /><span class='tips'>格式 jpg，尺寸900x500像数</span>";echo '<span class="C999">如果图片为空，将无法推送</span>';}?>
                          
                </td></tr>
                    <tr><td width="30" align="right">内容</td><td align="left"><textarea name="wx_gzh_push_pic_C" cols="50" rows="3" class="W300" style="display:inline-block"><?php echo dataIO($_GZH['wx_gzh_push_pic_C'],'wx');?></textarea><span class="tips">100字节以内</span></td></tr>
                        <tr><td width="30" align="right">链接</td><td align="left"><input name="wx_gzh_push_pic_url" type="text" class="W300" value="<?php echo stripslashes($_GZH['wx_gzh_push_pic_url']);?>" size="50" maxlength="100"></td></tr>
                    </table>
                <?php }elseif($pushkind == 'ulist'){  ?>
                	<div class="clear"></div>
                    <input name="wx_gzh_push_ulist" id="wx_gzh_push_ulist" type="text" class="input W400" value="<?php echo stripslashes($_GZH['wx_gzh_push_ulist']);?>"size="50" maxlength="100"> <a href="#" class="btn size1" onClick="zeai.iframe('选择推荐会员','wx_gzh_push_ulist.php?submitok=so',600,600)" title="选择推荐会员">选择会员</a>
                <?php }?>
                
            </td>
        </tr>
        <?php } ?>
        <!---->
        <tr>
            <td align="center">
            <input name="submitok" type="hidden" value="cache_wx_gzh_push">
            <input name="uu" type="hidden" value="<?php echo $session_uid;?>">
            <input name="pp" type="hidden" value="<?php echo $session_pwd;?>"></td>
            <td align="left"><button type="button" id="save" class="btn size3 ">确认并保存</button>
            <script>
				var uu=<?php echo $session_uid;?>,pp='<?php echo $session_pwd;?>';
                save.onclick = function(){
                    zeai.msg('正在更新中',{time:30});
                    zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
                        zeai.msg('',{flag:'hide'});
						if (rs.flag == 1){zeai.alert(rs.msg,'wx_gzh'+zeai.ajxext+'t=1');}else{zeai.alert(rs.msg);}
                    });
                }
				if (!zeai.empty(o('delpicupdate1'))){
					delpicupdate1.onclick=function(){
						zeai.confirm('真的要删除此图片么？',function(){
							zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:'cache_wx_gzh_push_delpic',uu:uu,pp:pp}},function(e){var rs=zeai.jsoneval(e);
								if (rs.flag == 1){location.reload(true);}
							});
						});
					}
				}
              </script></td>
        </tr>
    </table>
<!----------------微信公众号群发---------------->
<?php }elseif($t == 2){
	
if(!in_array('wx_gzh_qf',$QXARR))exit(noauth());	
	
	
	?>
    <table class="table size1 W1200" style="margin:20px 60px 0 20px">
        <tr><th align="left" colspan="2">后台人工群发　<span class="tips">（说明：1．此推送对【符合条件的所有会员】群发，请不要滥用，以防公众号被封，推荐顶多一天一次。　2．微信官方规定会员关注公众号时或与公众号发生交互48小时以内有效）</span></th></tr>
        <tr>
        	<?php $qfkind = (empty($qfkind))?$_GZH['wx_gzh_qf_kind']:$qfkind; ?>
            <td class="tdL">推送类型</td>
            <td class="tdR">　
                <input type="radio" class="radioskin" name="wx_gzh_qf_kind" id="qf1" value="text"<?php echo ($qfkind == 'text')?' checked':'';?> onClick="zeai.openurl('<?php echo $SELF; ?>?t=2&qfkind=text')"><label for="qf1" class="radioskin-label"><i class="i1"></i><b class="W50 S12">文本</b></label>
                <input type="radio" class="radioskin" name="wx_gzh_qf_kind" id="qf2" value="pic"<?php echo ($qfkind == 'pic')?' checked':'';?> onClick="zeai.openurl('<?php echo $SELF; ?>?t=2&qfkind=pic')"><label for="qf2" class="radioskin-label"><i class="i1"></i><b class="W50 S12">图文</b></label>
                <!--<input type="radio" class="radioskin" name="wx_gzh_qf_kind" id="qf3" value="ulist"<?php echo ($qfkind == 'ulist')?' checked':'';?> onClick="zeai.openurl('<?php echo $SELF; ?>?t=2&qfkind=ulist')"><label for="qf3" class="radioskin-label"><i class="i1"></i><b class="W100 S12">推荐会员信息列表</b></label>-->
            </td>
        </tr>
        <?php if (!empty($qfkind)){ ?>
        <tr>
            <td class="tdL"><?php if ($qfkind == 'text'){echo'文本';}elseif($qfkind == 'pic'){echo'图文';}elseif($qfkind == 'ulist'){echo'会员列表';}?>推送信息</td>
            <td class="tdR">
				<?php if ($qfkind == 'text'){ ?>
                    <textarea name="wx_gzh_qf_text_C" cols="50" rows="8" class="textarea W400"><?php echo dataIO($_GZH['wx_gzh_qf_text_C'],'wx');?></textarea>
              		<span class="C999">1.内容请控制点在400字节以内<br>2.如果要加超链接源码，请在官方客服指导下进行修改，以防程序出错</span>
                <?php }elseif($qfkind == 'pic'){  ?>

              		<table class="table0 FL">
                        <tr><td width="30" height="35" align="right">标题</td><td align="left"><input name="wx_gzh_qf_pic_title" type="text" class="size2 W300" value="<?php echo stripslashes($_GZH['wx_gzh_qf_pic_title']);?>" maxlength="200"><span class="tips">100字节以内</span></td></tr>
                        <tr><td width="30" height="35" align="right">图片</td><td align="left">
                          <?php if (!empty($_GZH['wx_gzh_qf_pic_path'])) {?>
                          <img src="<?php echo $_ZEAI['up2']."/".$_GZH['wx_gzh_qf_pic_path']; ?>" class="zoom" align="absmiddle" width="200" height="111" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".$_GZH['wx_gzh_qf_pic_path']; ?>')">　<a href="javascript:;" id='delpicupdate1' class="btn size1">删除</a> <span class="tips" style="vertical-align:middle">（删除图片后可重新上传）</span>
                          <?php }else{echo "<input name=pic1 type=file size=30 class=W300 /><span class='tips'>格式 jpg，尺寸900x500像数</span>";echo '<span class="C999">如果图片为空，将无法推送</span>';}?>
                          
                    </td></tr>
                        <tr><td width="30" height="35" align="right">内容</td><td align="left"><textarea name="wx_gzh_qf_pic_C" cols="50" rows="3" class="W300" style="display:inline-block"><?php echo dataIO($_GZH['wx_gzh_qf_pic_C'],'wx');?></textarea><span class="tips">100字节以内</span></td></tr>
                        <tr><td width="30" height="35" align="right">链接</td><td align="left"><input name="wx_gzh_qf_pic_url" type="text" class="size2 W300" value="<?php echo stripslashes($_GZH['wx_gzh_qf_pic_url']);?>"size="50" maxlength="100"></td></tr>
                    </table>
                <?php }elseif($qfkind == 'ulist'){  ?>
                	<div class="clear"></div>
                    <input name="wx_gzh_qf_ulist" id="wx_gzh_qf_ulist" type="text" class="input W400" value="<?php echo stripslashes($_GZH['wx_gzh_qf_ulist']);?>"size="50" maxlength="100"> <a href="#" class="btn size1" onClick="zeai.iframe('选择推荐会员','wx_gzh_qf_ulist.php?submitok=so',600,600)" title="选择推荐会员">选择会员</a>
                <?php }?>
                
            </td>
        </tr>
        <?php } ?>
        <!---->
        <tr>
            <td align="center">
            <input name="submitok" type="hidden" value="cache_wx_gzh_qf">
            <input name="uu" type="hidden" value="<?php echo $session_uid;?>">
            <input name="pp" type="hidden" value="<?php echo $session_pwd;?>"></td>
            <td align="left">
            <button type="button" id="save" class="btn size3 ">保存数据</button>　　　　　
            <button type="button" id="qunfa" class="btn size3 HONG">开始群发</button>
            <script>
				var uu=<?php echo $session_uid;?>,pp='<?php echo $session_pwd;?>';
                save.onclick = function(){
                    zeai.msg('正在更新中',{time:30});
                    zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
                        zeai.msg('',{flag:'hide'});
						if (rs.flag == 1){zeai.alert(rs.msg,'wx_gzh'+zeai.ajxext+'t=2');}else{zeai.alert(rs.msg);}
                    });
                }
				if (!zeai.empty(o('delpicupdate1'))){
					delpicupdate1.onclick=function(){
						zeai.confirm('真的要删除此图片么？',function(e){var rs=zeai.jsoneval(e);
							zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,data:{submitok:'cache_wx_gzh_qf_delpic',uu:uu,pp:pp}},function(e){var rs=zeai.jsoneval(e);
								if (rs.flag == 1){location.reload(true);}
							});
						});
					}
				}
				
                qunfa.onclick = function(){
					zeai.confirm('确认要对系统所有会员群发么？\n\n由于数据量太大，请确认PHP环境超时时间足够长，一般至少要10分钟\n\n发送过程中请不要关闭窗口，耐心等待',function(){
							zeai.msg('正在群发中...',{time:999});
							zeai.ajax({url:'wx_gzh'+zeai.ajxext+'submitok=ajax_qunfa'},function(e){var rs=zeai.jsoneval(e);
								zeai.msg(0);//zeai.msg(rs.msg);
								zeai.alert(rs.msg);
							});
					});
                }
				
                </script>
          </td>
        </tr>
    </table>
<!-------------------------------->    



<?php }?>
</form>
<?php require_once 'bottomadm.php';?>
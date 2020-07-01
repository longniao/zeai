<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_wxgzh.php';
require_once ZEAI.'cache/config_adm.php';
$_GZH['wx_gzh_name'] = dataIO($_GZH['wx_gzh_name'],'out');
if($submitok=='add_update' || $submitok=='mod_update'){
	if(empty($title))json_exit(array('flag'=>0,'msg'=>'请输入【菜单名称】','focus'=>'title'));
	//$title = dataIO($title,'in',20);
	$title = urlencode($title);
	$content = dataIO($content,'in',5000);
	$subkind = ($subkind == 'click')?'click':'view';
	$kind = ($kind == 1)?1:2;
	if(empty($url) && $kind==2)json_exit(array('flag'=>0,'msg'=>'请输入【网址/参数】','focus'=>'url'));
	$fid = intval($fid);
}
switch ($submitok) {
	case 'add_update':
		$db->query("INSERT INTO ".__TBL_GZH_MENU__."  (fid,kind,subkind,title,content,url,px) VALUES ('$fid','$kind','$subkind','$title','$content','$url',".ADDTIME.")");
		json_exit(array('flag'=>1,'msg'=>'增加成功'));
	break;
	case 'mod_update':
		if(!ifint($clsid))json_exit(array('flag'=>0,'msg'=>'clsid号错误'));
		$db->query("UPDATE ".__TBL_GZH_MENU__." SET subkind='$subkind',title='$title',content='$content',url='$url' WHERE id=".$clsid);
		json_exit(array('flag'=>1,'msg'=>'修改成功'));
	break;
	case 'del1_update':
		if(!ifint($clsid))json_exit(array('flag'=>0,'msg'=>'clsid号错误'));
		$db->query("DELETE FROM ".__TBL_GZH_MENU__." WHERE fid=".$clsid);
		$db->query("DELETE FROM ".__TBL_GZH_MENU__." WHERE id=".$clsid);
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case 'del2_update':
		if(!ifint($clsid))json_exit(array('flag'=>0,'msg'=>'clsid号错误'));
		$db->query("DELETE FROM ".__TBL_GZH_MENU__." WHERE id=".$clsid);
		json_exit(array('flag'=>1,'msg'=>'删除成功'));
	break;
	case 'left1_update':
		if(!ifint($clsid))json_exit(array('flag'=>0,'msg'=>'clsid号错误'));
		$db->query("UPDATE ".__TBL_GZH_MENU__." SET px=".ADDTIME." WHERE id=".$clsid);
		json_exit(array('flag'=>1,'msg'=>'置顶成功'));
	break;
	case 'zeai_gzh_makemenu':
		$rt=$db->query("SELECT id,kind,subkind,title,url FROM ".__TBL_GZH_MENU__." WHERE kind=1 ORDER BY px DESC,id DESC LIMIT 3");
		$total = $db->num_rows($rt);
		if ($total == 0) {
			json_exit(array('flag'=>0,'msg'=>'菜单内容为空，请增加后再来生成哦'));
		} else {
			$button = array();
			for($i=1;$i<=$total;$i++) {
				$rows = $db->fetch_array($rt,'name');
				if(!$rows) break;
				$id1    = $rows['id'];
				$kind   = $rows['kind'];
				$subkind1 = $rows['subkind'];
				$title1 = strip_tags(dataIO($rows['title'],'out'));
				$url1   = strip_tags(dataIO($rows['url'],'out'));
				$MENU   = array();
				$MENU["name"] = $title1;
				//
				$rt2=$db->query("SELECT kind,subkind,title,url,content FROM ".__TBL_GZH_MENU__." WHERE kind=2 AND fid=".$id1." ORDER BY px DESC,id DESC LIMIT 5");
				$total2 = $db->num_rows($rt2);
				if ($total2 == 0) {
					$MENU["type"] = $subkind1;
					$MENU["url"]  = $url1;
				} else {
					$SUB = array();
					$SUBALL = array();
					for($i2=1;$i2<=$total2;$i2++) {
						$rows2 = $db->fetch_array($rt2,'name');
						if(!$rows2) break;
						$title2 = dataIO($rows2['title'],'out');
						$subkind2 = $rows2['subkind'];
						$url2   = strip_tags(dataIO($rows2['url'],'out'));
						$content= dataIO($rows2['content'],'out');
						$SUB["name"]  = $title2;
						$SUB["type"]  = $subkind2;
						$SUB["url"]  = $url2;
						if($subkind2=='click')$SUB["key"]  = $content;
						$SUBALL[]=$SUB;
					}
					$MENU["sub_button"]=$SUBALL;
				}
				$button[]=$MENU;
			}
			$data['button']=$button;
			$data = encode_json($data);
		}
		$token=wx_get_access_token();
		$res = Zeai_POST_stream('https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$token,$data);
		$res=json_decode($res,true);
		if($res['errcode']==0){
			$flag = 1;
			$msg  = '菜单生成成功，请用手机前往公众号查看';
		}else{
			$flag = 0;
			$msg = $res['errmsg'];
		}
		json_exit(array('flag'=>$flag,'msg'=>$msg));
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="../res/www_zeai_cn.js"></script>
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css" rel="stylesheet" type="text/css">
<style>
.gzhmenu{width:320px;height:450px;border:#ddd 1px solid;position:relative;text-align:center;background-color:#F3F3F3}
.gzhmenu .makemenu{width:100%;position:absolute;left:0;top:70px;}
.gzhmenu .top{width:100%;line-height:40px;height:40px;background-color:#666;color:#fff;position:absolute;left:0;top:0;}
.gzhmenu .btm{width:100%;position:absolute;left:0;bottom:0;line-height:50px;height:50px;border-top:#ddd 1px solid;background-color:#fff}
.gzhmenu .inputt{width:40px;border-right:#ddd 1px solid;float:left;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.gzhmenu .inputt img{height:30px;display:block;margin:10px auto}
.gzhmenu .btm ul{width:-webkit-calc(100% - 40px);float:right;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;display:-webkit-flex;display:flex;-webkit-flex-wrap:wrap;flex-wrap:wrap;-webkit-justify-content:space-between;justify-content:space-between}
.gzhmenu .btm ul li{position:relative;font-size:14px;min-width:33%;display:inline-block;line-height:50px;border-right:#ddd 1px solid;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.gzhmenu .btm ul li:last-child{border:0}
.gzhmenu .btm ul li:hover,.gzhmenu .btm ul li.ed{color:#459ae9;cursor:pointer}
.gzhmenu .btm ul li dt{position:relative}
.gzhmenu .btm ul li dt i{position:absolute;top:-10px;right:-10px;color:#000;font-size:20px;width:20px;height:20px;line-height:20px;border-radius:10px;background-color:#fff;display:none;z-index:2}
.gzhmenu .btm ul li dt:hover i{display:block}
.gzhmenu .btm ul li dt i:hover{cursor:pointer;color:#E50025}
.gzhmenu .btm ul li dt i.left1{right:auto;left:-10px;background-color:#000;color:#fff;font-size:12px;top:-9px}
.gzhmenu .btm ul li dt i.left1:hover{background-color:#22B7E5}
.gzhmenu .btm ul li dd{width:90%;position:absolute;left:3px;bottom:60px;border:#ddd 1px solid;background-color:#fff;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.gzhmenu .btm ul li dd a{position:relative;display:block;line-height:40px;height:40px;border-bottom:#ddd 1px solid;font-size:12px;box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box}
.gzhmenu .btm ul li dd a:last-child{border:0}
.gzhmenu .btm ul li dd a.ed,.gzhmenu .btm ul li dd a:hover{color:#459ae9;cursor:pointer}
.gzhmenu .btm ul li dd a i{position:absolute;top:9px;right:-10px;color:#000;font-size:20px;width:20px;height:20px;line-height:20px;border-radius:10px;background-color:#fff;display:none;z-index:2}
.gzhmenu .btm ul li dd i:hover{cursor:pointer;color:#E50025}
.gzhmenu .btm ul li dd i.top2{right:auto;left:-10px;background-color:#000;color:#fff;font-size:12px}
.gzhmenu .btm ul li dd i.top2:hover{background-color:#22B7E5}
.gzhmenu .btm ul li dd a:hover i{display:block}
.gzhmenu .btm ul li.li100_{width:100%}
.gzhmenu .btm ul li.li50_{width:50%}
.gzhmenu .btm ul li.li33_{width:33%}
.gzhC{height:450px;}
.gzhC .T{height:50px;font-weight:bold}
.gzhC .C .tbC td{padding:15px 0;font-size:14px}
.gzhC .C .tbC tr:first-child {border-top:#eee 1px solid;}
.gzhC .C .tbC tr:last-child td{border:0}
</style>
</head>
<link href="css/main.css" rel="stylesheet" type="text/css">
<body>
<form id="ZEAI__CN___FORM">
    <table align="center" cellpadding="5" cellspacing="0" style="width:860px;margin-top:20px">
    <tr>
      <td width="350" align="left" valign="top">
        <div class="gzhmenu">
        	<div class="makemenu"><button type="button" id="zeai_gzh_makemenu" class="btn size2 LV">开始生成菜单</button><br><br><font class="C999">请先设置好菜单选项后再正式生成</font></div>
        	<div class="top"><?php echo $_GZH['wx_gzh_name'];?></div>
            <div class="btm">
                <div class="inputt"><img src="images/gzhmenu.png"></div>
            	<ul>
					<?php
                    $rt=$db->query("SELECT id,title FROM ".__TBL_GZH_MENU__." WHERE kind=1 ORDER BY px DESC,id DESC LIMIT 3");
                    $total = $db->num_rows($rt);
                    if ($total == 0) {
                        echo '<li class="li100_"> <a href="var_gzhmenu.php?submitok=add&kind=1&h1='.urlencode('菜单设置').'">+ 增加主菜单</a></li>';
                    } else {
                        for($i=1;$i<=$total;$i++) {
                            $rows = $db->fetch_array($rt);
                            if(!$rows) break;
                            $data_id1    = $rows['id'];
                            $data_title1 = dataIO($rows['title'],'out');
							?>
							<li<?php echo ($clsid == $data_id1 || $fid == $data_id1)?' class="ed"':'';?> title="点击修改">
								<dt onClick="zeai.openurl('var_gzhmenu.php?submitok=mod&kind=1&clsid=<?php echo $data_id1;?>&h1=<?php echo urlencode($data_title1);?>')">
									<?php echo $data_title1;?>
                                	<i class="ico addico del1" title="删除" clsid="<?php echo $data_id1;?>">&#xe62c;</i>
                                	<i class="ico left1" title="置顶最左" clsid="<?php echo $data_id1;?>">&#xe602;</i>
                                </dt>
                                <?php
								$rt2=$db->query("SELECT id,kind,subkind,title,url FROM ".__TBL_GZH_MENU__." WHERE kind=2 AND fid=".$data_id1." ORDER BY px DESC,id DESC LIMIT 5");
								$total2 = $db->num_rows($rt2);
								if ($total2 == 0) {
									echo '<dd title="增加子菜单"><a href="var_gzhmenu.php?submitok=add&kind=2&fid='.$data_id1.'&h1='.urlencode('增加子菜单').'"><span class="ico">&#xe620;</span></a></dd>';
								} else {
									echo '<dd>';
									for($i2=1;$i2<=$total2;$i2++) {
										$rows2 = $db->fetch_array($rt2);
										if(!$rows2) break;
										$data_id2    = $rows2['id'];
										$data_title2 = dataIO($rows2['title'],'out');
										?>
                                        <a <?php echo ($clsid == $data_id2)?' class="ed"':'';?> href="var_gzhmenu.php?submitok=mod&kind=2&fid=<?php echo $data_id1;?>&clsid=<?php echo $data_id2;?>&h1=<?php echo urlencode($data_title2);?>">
                                            <?php echo gylsubstr($data_title2,6,0,"utf-8",false);?>
                                            <i class="ico addico del2" title="删除" clsid="<?php echo $data_id2;?>">&#xe62c;</i>
                                            <i class="ico top2" title="置顶最上" clsid="<?php echo $data_id2;?>">&#xe60a;</i>
                                        </a>
										<?php
									}
									if ($total2 < 5)echo '<a title="增加子菜单" href="var_gzhmenu.php?submitok=add&kind=2&fid='.$data_id1.'&h1='.urlencode('增加子菜单').'"><span class="ico">&#xe620;</span></a>';
									echo '</dd>';
								}?>                        
							</li>
                            <?php
                        }
						if ($total <3) {echo '<li class="li33_" title="增加主菜单"> <a href="var_gzhmenu.php?submitok=add&kind=1&h1='.urlencode('增加主菜单').'" class="ico ">&#xe620;</a></li>';}
						?>
						 <script>
                            zeai.listEach('.del1',function(obj){
                                var clsid = parseInt(obj.getAttribute("clsid"));
                                obj.onclick = function(e){
									e.stopPropagation();
                                    zeai.confirm('确定删除么？ 此操作将删除所有子菜单',function(){
                                        zeai.ajax({url:'var_gzhmenu.php?submitok=del1_update&clsid='+clsid},function(e){rs=zeai.jsoneval(e);
                                            zeai.msg(0);zeai.msg(rs.msg);
                                            if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
                                        });
                                    });
                                }
                            });     
                            zeai.listEach('.del2',function(obj){
                                var clsid = parseInt(obj.getAttribute("clsid"));
                                obj.onclick = function(e){
									e.stopPropagation();
									e.preventDefault();
                                    zeai.confirm('确定删除么？',function(){
                                        zeai.ajax({url:'var_gzhmenu.php?submitok=del2_update&clsid='+clsid},function(e){rs=zeai.jsoneval(e);
                                            zeai.msg(0);zeai.msg(rs.msg);
                                            if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
                                        });
                                    });
                                }
                            });     
							gzh_menu_topFn('.left1');gzh_menu_topFn('.top2');
							function gzh_menu_topFn(cls) {
								zeai.listEach(cls,function(obj){
									var clsid = parseInt(obj.getAttribute("clsid"));
									obj.onclick = function(e){
										e.stopPropagation();
										zeai.ajax({url:'var_gzhmenu.php?submitok=left1_update&clsid='+clsid},function(e){rs=zeai.jsoneval(e);
											zeai.msg(0);zeai.msg(rs.msg);
											if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
										});
									}
								});
							}
                         </script>   
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
	</td>
        <td align="left" valign="top" class="S16">
		<div class="gzhC">
			<div class="T"><?php echo urldecode($h1);?></div>
			<div class="C">
				<?php if ($submitok=='add' || $submitok=='mod'){
					if($submitok=='mod'){
						if(!ifint($clsid) || !ifint($kind))exit('参数错误');
						$row = $db->ROW(__TBL_GZH_MENU__,"*","kind=".$kind." AND id=".$clsid);
						if ($row){
							$title  = dataIO($row['title'],'out');
							$subkind= $row['subkind'];
							$content= dataIO($row['content'],'out');
							$url    = dataIO($row['url'],'out');
						}
					}
				?>
                  <table width="90%" border="0" cellpadding="5" cellspacing="0" class="tbC">
                    <tr>
                      <td width="100">菜单名称</td>
                      <td align="left"><input name="title" id="title" type="text" class="input size2 W200" value="<?php echo $title; ?>" size="20" maxlength="20"   autocomplete="off" />　<span class="C999">最多7个字</span></td>
                    </tr>
                    <tr>
                      <td>菜单类型</td>
                      <td align="left">
                      <input type="radio" name="subkind" id="subkind_view" class="radioskin" onClick="o('contentbox').style.display='none';" value="view"<?php echo ($subkind == 'view' || empty($subkind))?' checked':'';?>><label for="subkind_view" class="radioskin-label"><i class="i1"></i><b class="W100 S14">链接网址</b></label>
                      <input type="radio" name="subkind" id="subkind_click" class="radioskin" onClick="o('contentbox').style.display='';" value="click"<?php echo ($subkind == 'click')?' checked':'';?>><label for="subkind_click" class="radioskin-label"><i class="i1"></i><b class="W150 S14">内部调用(不要选这个)</b></label>
                      
                      </td>
                    </tr>
                    <tr>
                      <td>链接网址</td>
                      <td align="left"><input name="url" id="url" type="text" class="input size2 W100_" value="<?php echo $url; ?>" maxlength="500"   autocomplete="off" /><br>
                      <span class="C999">如果没有子菜单，此项必填</span>
                      </td>
                    </tr>
                    <tr  id="contentbox"<?php echo ($subkind == 'click')?' style="display:\'\'"':' style="display:none"';?>>
                      <td>内容</td>
                      <td align="left"><textarea name="content" id="content" rows="5" class="W100_" placeholder="请在官方指导下填写"><?php echo $content; ?></textarea></td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center"><button type="button" id="save_zeai" class="btn size3 HUANG3">确认并保存</button></td>
                    </tr>
                </table>
          		<?php }?>
                <input name="kind" type="hidden" value="<?php echo $kind;?>">
                <input name="clsid" type="hidden" value="<?php echo $clsid;?>">
                <input name="fid" type="hidden" value="<?php echo $fid;?>">
                <input name="submitok" type="hidden" value="<?php echo $submitok;?>_update">
              	<div class="clear"></div>
          </div>
        </div>
        
        </td>
    </tr>
    </table>
</form>
<script>
if(!zeai.empty(o('save_zeai')))o('save_zeai').onclick = function(){
	zeai.confirm('确定菜单内容无误提交保存么？',function(){
		zeai.ajax({url:'var_gzhmenu'+zeai.extname,form:ZEAI__CN___FORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}
		});
	});
}
if(!zeai.empty(o('zeai_gzh_makemenu')))o('zeai_gzh_makemenu').onclick = function(){
	zeai.confirm('请确定菜单内容无误，开始生成请点【确定】<br>生成成功后，微信服务器可能会有一定时间生效周期，不一定立马能生效',function(){
		zeai.msg('正在向微信服务器发送菜单请求，请稍后...',{time:20});
		zeai.ajax({url:'<?php echo HOST;?>/api/zeai_cn__timer/refresh_access_token'+zeai.extname});
		setTimeout(function(){
			zeai.ajax({url:'var_gzhmenu.php?submitok=zeai_gzh_makemenu'},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg,{time:3});
				if (rs.flag == 1){setTimeout(function(){parent.location.reload(true);},3000);}
			});
		},2000);
	});
}
</script>
<?php require_once 'bottomadm.php';?>
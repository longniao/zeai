<?php
require_once '../sub/init.php';
header("Cache-control: private");
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_shop.php';
require_once ZEAI.'cache/config_up.php';
require_once ZEAI.'sub/zeai_up_func.php';
require_once ZEAI.'cache/config_reg.php';$TG_set = json_decode($_REG['TG_set'],true);
$navtop=json_decode($_SHOP['navtop'],true);
$navbtm=json_decode($_SHOP['navbtm'],true);
if(!in_array('shop',$QXARR))exit(noauth());
if($submitok=='delpicupdate'){
	@up_send_admindel($_SHOP['logo']);
	$_SHOP['logo']='';
	cache_mod_config($_SHOP,'config_shop','_SHOP');
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}elseif($submitok=='delpicupdate_my_banner'){
	@up_send_admindel($_SHOP['my_banner']);
	$_SHOP['my_banner']='';
	cache_mod_config($_SHOP,'config_shop','_SHOP');
	json_exit(array('flag'=>1,'msg'=>'操作成功'));
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="<?php echo HOST;?>/res/www_zeai_cn.js?<?php echo $_ZEAI['cache_str'];?>"></script>
<link href="css/main.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/iconfont/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<link href="<?php echo HOST;?>/res/icofont2/iconfont.css?<?php echo $_ZEAI['cache_str'];?>" rel="stylesheet" type="text/css">
<script src="js/Sortable1.6.1.js"></script>
<style>
.tips{font-size:12px;}
.navkind{border-bottom:#eee 1px solid;padding:10px 0 20px 10px;text-align:center}
.navdiy{clear:both;overflow:auto;text-align:left;padding:10px 0}
.navdiy .stepbox{margin-bottom:10px}
.navdiy .stepbox .stepli{margin-top:5px}
.navdiy .stepbox .stepli li{text-align:center;width:90px;padding:14px 10px 10px 10px;float:left;margin:8px 5px 8px 12px;border:#ddd 1px solid;cursor:move;border-radius:13px}
.navdiy .stepbox .stepli li p{width:60px;height:60px;line-height:60px;border-radius:18px;display:inline-block;background-color:#f29999;overflow:hidden}
.navdiy .stepbox .stepli li p:hover{background-color:#FF6F6F;cursor:pointer}
.navdiy .stepbox .stepli li.off p:hover{background-color:#f29999}
.navdiy .stepbox .stepli li p img{width:60px;height:60px;object-fit:cover;-webkit-object-fit:cover}
.navdiy .stepbox .stepli li span{width:100%;display:block;font-size:14px;margin:0 0 5px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.navdiy .stepbox .stepli li em{height:30px;}
.navdiy .stepbox .stepli li.off p{background-color:#ccc}
.navdiy .stepbox .stepli li.off p img{-webkit-filter:grayscale(100%);-moz-filter:grayscale(100%);-ms-filter:grayscale(100%);-o-filter:grayscale(100%);filter:grayscale(100%)}
.navdiy .stepbox .stepli li.off span{color:#999}
.mBNpic{width:200px;height:93px;object-fit:cover;-webkit-object-fit:cover}
.my_banner{width:200px;height:47px;object-fit:cover;-webkit-object-fit:cover}
</style>
<script>var up2='<?php echo $_ZEAI['up2'];?>/',upMaxMB=<?php echo $_UP['upMaxMB']; ?>;</script>
</head>
<body>
<?php
if($submitok == 'navtop_mod' || $submitok == 'navbtm_mod'){
	if($submitok == 'navbtm_mod'){
		$i=nav_info($id,'i',$navbtm);$t=nav_info($id,'t',$navbtm);$img=nav_info($id,'img',$navbtm);$img2=nav_info($id,'img2',$navbtm);$url=nav_info($id,'url',$navbtm);$f=nav_info($id,'f',$navbtm);$url2=nav_info($id,'url2',$navbtm);$var=nav_info($id,'var',$navbtm);
	}else{
		$i=nav_info($id,'i',$navtop);$t=nav_info($id,'t',$navtop);$img=nav_info($id,'img',$navtop);$url=nav_info($id,'url',$navtop);$f=nav_info($id,'f',$navtop);
	}
	?>
	<style>
	.table{margin:15px 0 0 15px}
    .table td{padding:8px;border:1px solid #eee}
	.table .tdL{width:160px;color:#666}
	.table .tdR img.add{width:60px;height:60px;object-fit:cover;-webkit-object-fit:cover;background-color:#ccc;vertical-align:middle}
	.table .tdR span{vertical-align:middle;font-size:14px;color:#999;margin-left:20px;display:inline-block}
    </style>
    <form id="ZEAIFORM">
        <table class="table W95_ Mtop20" >
        <tr>
          <td class="tdL">导航名称</td>
          <td class="tdR"><input name="title" id="title" type="text" class="input size2" maxlength="30" value="<?php echo $t;?>"  autocomplete="off" /><span>← 请控制在4个字以内</span></td>
        </tr>
        <tr>
          <td class="tdL">导航图标<?php echo($submitok == 'navbtm_mod')?'【已选】':'';?></td>
          <td class="tdR">
            <div class="picli60" id="picli_path">
              <li class="add" id="path_add"></li>
              <?php if(!empty($img)){
                    echo '<li><img src="'.$_ZEAI['up2'].'/'.$img.'"><i></i></li>';
                }?>
            </div>
            <br><span>← 正方形.png/.jpg/.gif格式，<?php echo ($submitok == 'navbtm_mod')?'90*90':'150*150';?>像数</span></td>
        </tr>
        
		<?php if($submitok == 'navbtm_mod'){?>
                <tr>
                  <td class="tdL">导航图标【未选】</td>
                  <td class="tdR">
                    <div class="picli60" id="picli_path2">
                      <li class="add" id="path_add2"></li>
                      <?php if(!empty($img2)){
                            echo '<li><img src="'.$_ZEAI['up2'].'/'.$img2.'"><i></i></li>';
                        }?>
                    </div>
                    <br><span>↑ 规格同上</span></td>
                </tr>
                <tr>
                  <td class="tdL"> 导航变量</td>
                  <td class="tdR"><input name="var" class="input size2" maxlength="30" id="var" value="<?php echo $var;?>"></td>
                </tr>
                <input name="t" type="hidden" value="navbtm_mod">
		<?php }?>
        
        
        <tr>
          <td class="tdL">手机端链接</td>
          <td class="tdR"><textarea name="url" rows="3" class="textarea W100_" id="url"><?php echo $url;?></textarea></td>
        </tr>

        
        </table>
        <br><br><div class="savebtnbox"><button type="button" id="submit_add" class="btn size3 HUANG3">保存并提交</button></div>
        <input name="id" type="hidden" value="<?php echo $id;?>" />
        <input name="path_s" id="path_s" type="hidden" value="" />
        <input name="path_s2" id="path_s2" type="hidden" value="" />
        <input name="uu" type="hidden" value="<?php echo $session_uid;?>">
        <input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
        <input name="submitok" type="hidden" value="cache_shopnav_mod">
	</form>
    <script>
	zeai.photoUp({
		btnobj:path_add,
		upMaxMB:upMaxMB,
		url:"var"+zeai.extname,
		submitok:"ajax_pic_path_s_up",
		end:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){
				picli_path.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');
				path_s.value=rs.dbname;
				path_add.hide();
				var i=zeai.tag(o(picli_path),'i')[0],img=zeai.tag(o(picli_path),'img')[0];
				i.onclick = function(){
					zeai.confirm('亲~~确认删除么？',function(){
						img.parentNode.remove();path_add.show();path_s.value='';
					});
				}
				img.onclick = function(){parent.piczoom(up2+rs.dbname.replace('_s.','_b.'));}
			}
		}
	});
	window.onload=function(){
		path_s_mod();
		function path_s_mod(){
			var i=zeai.tag(picli_path,'i')[0],img=zeai.tag(picli_path,'img')[0];
			if(zeai.empty(i))return;
			path_add.hide();
			var src=img.src.replace(up2,'');
			path_s.value=src;
			i.onclick = function(){
				zeai.confirm('亲~~确认删除么？',function(){
					img.parentNode.remove();path_add.show();path_s.value='';
				});
			}
			img.onclick = function(){parent.piczoom(up2+src.replace('_s.','_b.'));}
		}
		<?php if($submitok == 'navbtm_mod'){?>
			path_s_mod2();
			function path_s_mod2(){
				var i=zeai.tag(picli_path2,'i')[0],img=zeai.tag(picli_path2,'img')[0];
				if(zeai.empty(i))return;
				path_add2.hide();
				var src=img.src.replace(up2,'');
				path_s2.value=src;
				i.onclick = function(){
					zeai.confirm('亲~~确认删除么？',function(){
						img.parentNode.remove();path_add2.show();path_s2.value='';
					});
				}
				img.onclick = function(){parent.piczoom(up2+src.replace('_s.','_b.'));}
			}
		<?php }?>
	}
	
	<?php if($submitok == 'navbtm_mod'){?>
	zeai.photoUp({
		btnobj:path_add2,
		upMaxMB:upMaxMB,
		url:"var"+zeai.extname,
		submitok:"ajax_pic_path_s_up",
		end:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){
				picli_path2.append('<li><img src="'+up2+rs.dbname+'"><i></i></li>');
				path_s2.value=rs.dbname;
				path_add2.hide();
				var i=zeai.tag(o(picli_path2),'i')[0],img=zeai.tag(o(picli_path2),'img')[0];
				i.onclick = function(){
					zeai.confirm('亲~~确认删除么？',function(){
						img.parentNode.remove();path_add2.show();path_s2.value='';
					});
				}
				img.onclick = function(){parent.piczoom(up2+rs.dbname.replace('_s.','_b.'));}
			}
		}
	});
	<?php }?>
	
	submit_add.onclick=function(){
		zeai.confirm('<b class="S18">确定提交么？</b><br>亲~~这三项都不为空才会显示导航哦',function(){
			zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
				zeai.msg(0);zeai.msg(rs.msg);
				if(rs.flag==1){
					setTimeout(function(){
						if(zeai.empty(rs.out_img)){
							var src='images/navadd.png';
						}else{
							var src=up2+rs.out_img;
						}
						<?php if($submitok == 'navbtm_mod'){?>
							parent.o('img2'+<?php echo $id;?>).src=src;
							parent.o('span2'+<?php echo $id;?>).html(rs.out_t);
						<?php }else{ ?>
							parent.o('img'+<?php echo $id;?>).src=src;
							parent.o('span'+<?php echo $id;?>).html(rs.out_t);
						<?php }?>
						parent.zeai.iframe(0);
					},1000);
				}
			});
		});
	}
    </script>
<?php exit;}elseif($submitok == 'navbtm_mod'){?>





<?php exit;}?>

<div class="navbox">
    <?php if ($t == 'nav' || empty($t)){?><a href="<?php echo SELF;?>?t=1" <?php echo (empty($t) || $t == 'nav')?' class="ed"':'';?>>商家分类/导航</a><?php }?>
</div>
<div class="fixedblank"></div>
<form id="ZEAIFORM" name="ZEAIFORM" method="post" enctype="multipart/form-data">

    
<!--导航/模块设置-->
	<?php if($t == 'nav' || empty($t)){
		/*if(!in_array('nav',$QXARR))exit(noauth());*/?>
        
    <!--商家首页顶部导航-->
	<table class="table size2 " style="width:1111px;margin:20px 0 0 20px">
    <tr><th colspan="2" align="left" style="border:0"><b>商家【首页】顶部导航</b></th></tr>
	<tr><td colspan="2" align="left" class="S14">
    <div class="navdiy">
        <dd class="stepbox" id="stepbox1">
            <div class="stepli">
                <?php
                if (count($navtop) >= 1 && is_array($navtop)){
                    foreach ($navtop as $V) {
                        $i=$V['i'];$img=$V['img'];$f=intval($V['f']);
                        $img=(empty($img))?'images/navadd.png':$_ZEAI['up2'].'/'.$img;?>
                        <li<?php echo ($f==0)?' class="off"':'';?>>
                            <p title="点击进行设置" class="navtopli" i="<?php echo $i;?>"><img src="<?php echo $img;?>" id="img<?php echo $i;?>"></p><span id="span<?php echo $i;?>"><?php echo dataIO($V['t'],'out');?></span>
                            <em><input type="checkbox" class="switch navtop_f" i="<?php echo $i;?>" name="navtop_f<?php echo $i;?>" id="navtop_f<?php echo $i;?>" value="1"<?php echo ($f==1)?' checked':'';?>><label for="navtop_f<?php echo $i;?>" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
                        </li><?php
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
        </dd>
        <img src="images/!.png" width="14" height="14" valign="middle" style="margin-left:20px"> <font class="picmiddle S12 C999">修改图片/名称/超链接，请点击图标修改；可以按住不放拖动项目调整前后顺序，开启显示，关闭不显示；图标请上传正方形png格式90*90像数为宜</font>
        <button class="btn size2 FR" type="button" onClick="zeai.div({obj:navdiy_help,title:'官网默认功能链接（根据需要复制<font class=\'Clan\'>【蓝色部分】</font>）',w:666,h:450});" style="margin-right:30px">查看内置链接</button>
    </div>
	</td>
	</tr>

    <!--商家底部导航-->
    <tr><th colspan="2" align="left" style="border:0">&nbsp;</th></tr>
    <tr><th colspan="2" align="left" style="border:0"><b>商家【底部】导航</b></th></tr>
    <tr><td colspan="2">
    <div class="navdiy">
        <dd class="stepbox" id="stepbox2">
            <div class="stepli">
                <?php
                if (count($navbtm) >= 1 && is_array($navbtm)){
                    foreach ($navbtm as $V) {
                        $i=$V['i'];$img=$V['img'];$img2=$V['img2'];$f=intval($V['f']);
						$img=(empty($img))?$img2:$img;
                        $img=(empty($img))?'images/navadd.png':$_ZEAI['up2'].'/'.$img;?>
                        <li<?php echo ($f==0)?' class="off"':'';?>>
                            <p title="点击进行设置" class="navbtmli" i="<?php echo $i;?>" ><img src="<?php echo $img;?>" id="img2<?php echo $i;?>"></p><span id="span2<?php echo $i;?>"><?php echo dataIO($V['t'],'out');?></span>
                            <em><input type="checkbox" class="switch navbtm_f" i="<?php echo $i;?>" name="navbtm_f<?php echo $i;?>" id="navbtm_f<?php echo $i;?>" value="1"<?php echo ($f==1)?' checked':'';?>><label for="navbtm_f<?php echo $i;?>" class="switch-label"><i></i><b>开启</b><b>隐藏</b></label></em>
                        </li><?php
                    }
                }
                ?>
            </div>
            <div class="clear"></div>
        </dd>
        <img src="images/!.png" width="14" height="14" valign="middle" style="margin-left:20px"> <font class="picmiddle S12 C999">修改图片/名称/超链接，请点击图标修改；可以按住不放拖动项目调整前后顺序，开启显示，关闭不显示；图标请上传正方形png格式90*90像数为宜</font>
        <button class="btn size2 FR" type="button" onClick="zeai.div({obj:navdiy_help,title:'官网默认功能链接（根据需要复制<font class=\'Clan\'>【蓝色部分】</font>）',w:666,h:450});" style="margin-right:30px">查看内置链接</button>
    </div>
    </td>
    </tr>
    

    <tr><th colspan="2" align="left" style="border:0">&nbsp;</th></tr>
    <!--底部导航菜单-->
    <tr><th colspan="2" align="left" style="border:0"><b>商家全局变量</b></th></tr>
    <tr>
    <td class="tdL">商家模块Logo</td>
    <td class="tdR">
    
		<?php if (!empty($_SHOP['logo'])){?>    
            <a class="pic60" onClick="parent.piczoom('<?php echo  $_ZEAI['up2']."/".$_SHOP['logo']; ?>')"><img src="<?php echo $_ZEAI['up2']."/".$_SHOP['logo']; ?>" class="m"></a>　
            <a class="btn size1" onClick="bannerDel(0)">删除</a>
            <span class="tips S12">删除后可更换</span>　  
		<?php }else{echo "<input name='pic0' type='file' size='50' class='Caaa size2 W200' />";}?>  
        <span class='tips S12'>商家首页左上角Logo图标，必须为jpg/gif/png格式，推荐宽高比例160*100像数</span>

    </td>
    </tr>
    <tr>
    <td class="tdL">商家模块名称</td>
    <td class="tdR"><input name="shoptitle" id="shoptitle" type="text" class="W200" maxlength="50" value="<?php echo $_SHOP['title'];?>">　　<span class="tips S12">默认：【店铺】</span></td>
    </tr>
    <tr>
    <td class="tdL">商家入驻交费</td>
    <td class="tdR">
        <input type="radio" name="regifpay" id="regifpay1" class="radioskin" value="1"<?php echo ($_SHOP['regifpay'] == 1)?' checked':'';?>><label for="regifpay1" class="radioskin-label"><i class="i1"></i><b class="W200">需要交费</b></label>
        <input type="radio" name="regifpay" id="regifpay2" class="radioskin" value="0"<?php echo ($_SHOP['regifpay'] == 0)?' checked':'';?>><label for="regifpay2" class="radioskin-label"><i class="i1"></i><b class="W100">免费入驻</b></label>
        <div class='tips S12 lineH150'>
        【需要交费】将调出<?php echo $_SHOP['title'];?>等级套餐列表，商家选择对应的级别进行支付升级<br>
        【免费入驻】将以<?php echo $_SHOP['title'];?>等级套餐设定的默认组进行自动升级，无需支付
        </div>
    </td>
    </tr>

    <tr>
    <td class="tdL">商家入驻审核</td>
    <td class="tdR">
        <input type="radio" name="shopregflag" id="shopflag1" class="radioskin" value="0"<?php echo ($_SHOP['regflag'] == 0)?' checked':'';?>><label for="shopflag1" class="radioskin-label"><i class="i1"></i><b class="W200">需要审核</b></label>
        <input type="radio" name="shopregflag" id="shopflag2" class="radioskin" value="1"<?php echo ($_SHOP['regflag'] == 1)?' checked':'';?>><label for="shopflag2" class="radioskin-label"><i class="i1"></i><b class="W100">直接通过</b></label>
    </td>
    </tr>
    
    
    <tr>
    <td class="tdL">商品下单类型</td>
    <td class="tdR">
        <input type="radio" name="orderkind" id="orderkind1" class="radioskin" value="1"<?php echo ($_SHOP['orderkind'] == 1)?' checked':'';?>><label for="orderkind1" class="radioskin-label"><i class="i1"></i><b class="W400">预约模式<font class="C999 S12">（用户无需付款，直接下单，商家人工联系处理订单）</font></b></label>
        <input type="radio" name="orderkind" id="orderkind2" class="radioskin" value="2"<?php echo ($_SHOP['orderkind'] == 2)?' checked':'';?>><label for="orderkind2" class="radioskin-label"><i class="i1"></i><b class="W300">在线支付<font class="C999 S12">（用户在线支付到平台，商家提现）</font></b></label>
    </td>
    </tr>
    <tr>
    <td class="tdL">自动确认收货</td>
    <td class="tdR"><input name="qrshday" id="qrshday" type="text" class="W50" maxlength="3" value="<?php echo $_SHOP['qrshday'];?>"> 天　　<span class="tips S12">卖家发货时间起，超过7天，自动确认收货，资金将打入卖家余额账户，必须填1~365天，默认7天</span></td>
    </tr>
    
    <tr>
    <td class="tdL">自动确认退款</td>
    <td class="tdR"><input name="tkday" id="tkday" type="text" class="W50" maxlength="3" value="<?php echo $_SHOP['tkday'];?>"> 天　　<span class="tips S12">买家付款后卖家未发货，超过3天，自动确认退款，资金原路返回或微信钱包，必须填1~365天，默认3天</span></td>
    </tr>
    <tr>
    <td class="tdL">自动确认退货</td>
    <td class="tdR"><input name="thday" id="thday" type="text" class="W50" maxlength="3" value="<?php echo $_SHOP['thday'];?>"> 天　　<span class="tips S12">卖家发货后，买家未确认收货前，买家发起退货，超过3天，自动确认退货，资金原路返回或微信钱包，必须填1~365天，默认3天</span></td>
    </tr>
    <tr>
    <td class="tdL">自动线下核单</td>
    <td class="tdR"><input name="hdday" id="hdday" type="text" class="W50" maxlength="3" value="<?php echo $_SHOP['hdday'];?>"> 天　　<span class="tips S12">卖家发货时间起，超过3天，买家如果不到店取货，自动确认提货已完成，资金将打入卖家余额账户，必须填1~365天，默认3天</span></td>
    </tr>

    <tr>
    <td class="tdL tdLbgHUI">单笔提现金额列表</td>
    <td class="tdR"><input name="tx_num_list"  type="text" class="W300 FVerdana" maxlength="100" value="<?php echo $_SHOP['tx_num_list'];?>"> 元<div class="C999 S12 lineH150">说明：数字请以英文半角逗号隔开，如：<font class="blue">100,500,1000,2000（最大金额不要超过微信支付商户平台单笔/单日限额）</font>；用户会选择对应的金额进行提现，提现申请后，后台人工确认后将直接打入提现人微信钱包</div></td>
    </tr>
    
    <tr>
    <td class="tdL">商家行业分类</td>
    <td class="tdR">
		<style>#tmp input.W50,#tmp input.W150{margin-right:10px}</style>
        <div id="tmp">
        <?php
        $a = json_decode($_SHOP['kindarr']);
        if (is_array($a) && count($a)>0){
            for($j=0;$j<count($a);$j++) {
                $id2    = $a[$j]->i;
                $value2 = $a[$j]->v;?>
            	<div class="tr">ID <input type="text" class="W50 size1" maxlength="4" placeholder="数字" value="<?php echo $id2;?>">名称 <input type="text" class="W150 size1" maxlength="50" value="<?php echo $value2;?>"><button type="button" class="btn size1">删除</button></div>
				<?php
			}
        }?>
        </div>
        <div><button id="add" type="button" class="btn size2 LAN2" style="margin-top:10px"><i class="ico add">&#xe620;</i>增加</button></div>
		<script>
			var n  =parseInt(<?php echo $j;?>);
			zeai.listEach('.tr',function(obj){obj.children[2].onclick=function(){this.parentNode.parentNode.removeChild(this.parentNode);}});
			add.onclick = function(){
				n++;
				var text1 = document.createTextNode('ID '),text2 = document.createTextNode('名称 ');
				var IDH = zeai.addtag('input');IDH.value = n;IDH.className = 'W50 size1';IDH.maxLength = 4;
				var Namee = zeai.addtag('input');Namee.className = 'W150 size1';Namee.maxLength = 50;
				var Btn = zeai.addtag('button');Btn.className = 'btn size1';Btn.html('删除');Btn.onclick=function(){this.parentNode.parentNode.removeChild(this.parentNode);}
				var tr = zeai.addtag('div');tr.className = 'tr';
				tr.appendChild(text1);tr.appendChild(IDH);tr.appendChild(text2);tr.appendChild(Namee);tr.appendChild(Btn);tmp.appendChild(tr);
			}
        </script>
    </td>
    </tr>
</table>



<table class="table size2" style="width:1111px;margin:40px 0 0 20px">
    <!--首页会员展示模块-->
    <tr><th colspan="2" align="left" style="border:0"><b>商家【我的】广告</b>（尺寸:1170*273像数，图片类型:jpg/gif/png，大小200K以内）</th></tr>
    <tr>
    <td class="tdL">【我的】内部广告</td>
    <td class="tdR">图片
		<?php if (!empty($_SHOP['my_banner'])){?>    
            <a onClick="parent.piczoom('<?php echo  $_ZEAI['up2']."/".$_SHOP['my_banner']; ?>')" class="zoom"><img src="<?php echo $_ZEAI['up2']."/".$_SHOP['my_banner']; ?>" align="absmiddle" class="my_banner"></a>　
            <a class="btn size1" onClick="bannerDel(4)">删除</a>
            <span class="tips S12">删除后可更换</span>　  
		<?php }else{echo "<input name='pic4' type='file' size='50' class='Caaa size2 W200' />";}?>  
        <span class='tips S12'>商家【我的】中上部广告</span>
        <div style="margin-top:15px">链接 <input name="my_banner_url" type="text" class="input size2" id="my_banner_url" value="<?php echo $_SHOP['my_banner_url'];?>"size="50" maxlength="200"></div>
    </td>
    </tr>
</table>


<table class="table size2 cols2" style="width:1111px;margin:40px 0 0 20px">
    <tr><th colspan="2" align="left" style="border:0"><b>商家【首页】顶部轮翻广告</b>（尺寸:1000*389像数，图片类型:jpg/gif/png，大小200K以内）</th></tr>

      <tr>
        <td align="right" bgcolor="#f8f8f8">手机轮翻广告①</td>
        <td align="left" valign="top">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
            <tr>
            <td width="420">
                <?php if (!empty($_SHOP['mBN_path1_s'])) {?>
                    <img src="<?php echo $_ZEAI['up2']."/".$_SHOP['mBN_path1_s']; ?>" class="zoom mBNpic" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".smb($_SHOP['mBN_path1_s'],'b'); ?>')">　
                    <a onClick="bannerDel(1)" class="btn size1" >删除</a>　<font class="C999 S12">先删除后更换</font>
                <?php }else{ 
                    echo "<input name=pic1 type=file size=30 class='input size2' />";
                }?>
            </td>
            <td>
                链接① <input name="path1_url" type="text" class="input size2" id="path1_url" value="<?php echo stripslashes($_SHOP['mBN_path1_url']);?>"size="50" maxlength="100">
            </td>
            </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td align="right" bgcolor="#f8f8f8">手机轮翻广告②</td>
        <td align="left" valign="top">
        
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
        <tr>
        <td width="420"><?php if (!empty($_SHOP['mBN_path2_s'])) {?>
        <img src="<?php echo $_ZEAI['up2']."/".$_SHOP['mBN_path2_s']; ?>" class="zoom mBNpic" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".smb($_SHOP['mBN_path2_s'],'b'); ?>')">　<a onClick="bannerDel(2)" class="btn size1">删除</a>　<font class="C999 S12">先删除后更换</font>
        <?php }else{ 
        echo "<input name=pic2 type=file size=30 class='input size2' />";
        }?></td>
        <td>链接②
        <input name="path2_url" type="text" class="input size2" id="path2_url" value="<?php echo stripslashes($_SHOP['mBN_path2_url']);?>"size="50" maxlength="100"></td>
        </tr>
        </table>
        </td>
    </tr>
          
    <tr>
        <td align="right" bgcolor="#f8f8f8">手机轮翻广告③</td>
        <td align="left" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table0">
        <tr>
        <td width="420"><?php if (!empty($_SHOP['mBN_path3_s'])) {?>
        <img src="<?php echo $_ZEAI['up2']."/".$_SHOP['mBN_path3_s']; ?>" class="zoom mBNpic" align="absmiddle" onClick="parent.piczoom('<?php echo $_ZEAI['up2']."/".smb($_SHOP['mBN_path3_s'],'b'); ?>')">　<a onClick="bannerDel(3)" class="btn size1">删除</a>　<font class="C999 S12">先删除后更换</font>
        <?php }else{ 
        echo "<input name=pic3 type=file size=30 class='input size2' />";
        }?></td>
        <td>链接③
        <input name="path3_url" type="text" class="input size2" id="path3_url" value="<?php echo stripslashes($_SHOP['mBN_path3_url']);?>"size="50" maxlength="100"></td>
        </tr>
        </table>
        </td>
    </tr>
</table>


<!--说明-->
<div id="navdiy_help" class="helpC S14">
    <span class="tips" style="font-family:Verdana, Geneva, sans-serif">
    
    【<?php echo $_SHOP['title'];?>“首页”】<img src="images/d2.gif"> 手机端链接：<font class="Clan"><?php echo HOST;?>/m4/shop_index.php</font><br>
    【<?php echo $_SHOP['title'];?>“我的”】<img src="images/d2.gif"> 手机端链接：<font class="Clan"><?php echo HOST;?>/m4/shop_my.php</font><br>
    【相亲主站“首页”】<img src="images/d2.gif"> 手机端链接：<font class="Clan"><?php echo HOST;?></font><br>
    【<?php echo $TG_set['navtitle'];?>“首页”】<img src="images/d2.gif"> 手机端链接：<font class="Clan"><?php echo HOST;?>/m1/tg_index.php</font><br>
    
    <div class="linebox"><div class="line"></div><div class="title BAI S14"><?php echo $_SHOP['title'];?>展示部分</div></div>
    <?php 
	$share_str=array();
	if(!empty($_SHOP['kindarr'])){
		$kindarr = json_decode($_SHOP['kindarr'],true);
		if (count($kindarr) >= 1 && is_array($kindarr)){
			echo '【'.$_SHOP['title'].'展示首页】<img src="images/d2.gif"> 手机端链接：<font class="Clan">'.HOST.'/m4/shop_shop.php</font><br>';
			foreach ($kindarr as $V) {
				echo '【'.$V['v'].'】<img src="images/d2.gif"> 手机端链接：<font class="Clan">'.HOST.'/m4/shop_shop.php?k='.$V['i'].'</font><br>';
			}
		}
	}
	?>
    <div class="linebox"><div class="line"></div><div class="title BAI S14">商品展示部分</div></div>
    <?php 
	$share_str=array();
	if(!empty($_SHOP['kindarr'])){
		$kindarr = json_decode($_SHOP['kindarr'],true);
		if (count($kindarr) >= 1 && is_array($kindarr)){
			echo '【商品展示首页】<img src="images/d2.gif"> 手机端链接：<font class="Clan">'.HOST.'/m4/shop_goods.php</font><br>';
			foreach ($kindarr as $V) {
				echo '【'.$V['v'].'】<img src="images/d2.gif"> 手机端链接：<font class="Clan">'.HOST.'/m4/shop_goods.php?k='.$V['i'].'</font><br>';
			}
		}
	}
	?>
     
    </span>
</div>

<input name="submitok" type="hidden" value="cache_shopnav">
<input name="navtop_px" id="navtop_px" type="hidden" value="">
<input name="navbtm_px" id="navbtm_px" type="hidden" value="">

<div class="savebtnbox"><button type="button" class="btn size3 HUANG3" id="save">确认并保存</button></div>
<script>
zeai.listEach('.switch',function(obj){
	obj.onclick = function(){
		var p=obj.parentNode.parentNode;
		if(obj.checked){
			p.removeClass('off');
		}else{
			p.addClass('off');
		}
	}
});
function drag_init(){(function (){[].forEach.call(stepbox1.getElementsByClassName('stepli'), function (el){Sortable.create(el,{group: 'zeai_navtop',animation:150});});})();}drag_init();
zeai.listEach('.navtopli',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("i"));
		zeai.iframe('顶部主导航DIY设置','shop_set'+zeai.ajxext+'submitok=navtop_mod&id='+id,600,350);
	}
});
function drag_init2(){(function (){[].forEach.call(stepbox2.getElementsByClassName('stepli'), function (el){Sortable.create(el,{group: 'zeai_navtop',animation:150});});})();}drag_init2();
zeai.listEach('.navbtmli',function(obj){
	obj.onclick = function(){
		var id = parseInt(obj.getAttribute("i"));
		zeai.iframe('底部导航DIY设置','shop_set'+zeai.ajxext+'submitok=navbtm_mod&id='+id,600,450);
	}
});
save.onclick = function(){
	var DATAPX=[];
	zeai.listEach('.navtop_f',function(obj){DATAPX.push(obj.getAttribute("i"));});
	navtop_px.value=DATAPX.join(",");
	DATAPX=[];
	zeai.listEach('.navbtm_f',function(obj){DATAPX.push(obj.getAttribute("i"));});
	navbtm_px.value=DATAPX.join(",");
	var idARR = [],jsonarr=[];
	zeai.listEach('.tr',function(obj){
		var subid   = obj.children[0];
		var subname = obj.children[1];
		if (!zeai.ifint(subid.value)){parent.zeai.msg('请输入ID数字',subid);zeaiifbreak=true;return false;}
		if (zeai.empty(subname.value)){parent.zeai.msg('请输入子项名称',subname);zeaiifbreak=true;return false;}
		idARR.push(subid.value);
		jsonarr.push({"i":subid.value,"v":subname.value});
	});
	if (!zeaiifbreak){
		var repeat = idARR.ifRepeat();
		if (repeat){
			parent.zeai.msg('ID数字有重复“'+repeat+'”');
			return false;
		}
	}else{return false;}
	if (idARR.length <= 0 && !zeaiifbreak){
		parent.zeai.msg('请点一下【增加】小按钮，谢谢');
		return false;
	}
	var jsonstr = JSON.stringify(jsonarr);
	var postjson = {"jsonstr":jsonstr};
	zeai.confirm('确认要修改么？此修改将触发缓存机制同步更新，立即生效！',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM,data:postjson},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		});
	});
}
</script>




<!---->
<?php }elseif($t == 'rz'){if(!in_array('var_rz',$QXARR))exit(noauth());?>







<?php }?>
	<input name="uu" type="hidden" value="<?php echo $session_uid;?>">
	<input name="pp" type="hidden" value="<?php echo $session_pwd;?>">
</form>
<script>
<?php if($t != 'rz' && $t != 'nav'){?>
save.onclick = function(){
	zeai.confirm('确认要修改么？此修改将触发缓存机制同步更新，立即生效！',function(){
		zeai.ajax({url:'<?php echo HOST;?>/sub/cache'+zeai.extname,form:ZEAIFORM},function(e){var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			setTimeout(function(){location.reload(true);},1000);
		});
	});
}
<?php }?>
/*系统基本设置*/
var uu=<?php echo $session_uid;?>,pp='<?php echo $session_pwd;?>';
function bannerDel(i){
	zeai.confirm('确认要删除么？',function(){
		if(i==0){
			var url='shop_set'+zeai.extname,submitok='delpicupdate';
		}else if(i==4){
			var url='shop_set'+zeai.extname,submitok='delpicupdate_my_banner';
		}else{
			var url='<?php echo HOST;?>/sub/cache'+zeai.extname,submitok='shopbannerDel';
		}
		zeai.ajax({url:url,data:{submitok:submitok,uu:uu,pp:pp,i:i}},function(e){
			var rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if (rs.flag == 1){setTimeout(function(){location.reload(true);},1000);}else{zeai.alert(rs.msg);}
		});
	});
}
</script>
<br><br><br><br><br><br>
<?php require_once 'bottomadm.php';?>
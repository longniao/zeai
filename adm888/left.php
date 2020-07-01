<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
require_once ZEAI.'cache/config_shop.php';
require_once ZEAI.'cache/config_reg.php';
$TG_set = json_decode($_REG['TG_set'],true);
if($submitok=='ajax_unum'){
	$unum['gh']      = $db->COUNT(__TBL_USER__,"admid=0 AND kind<>4 AND mob<>'' AND mob<>0 AND myinfobfb>10");
	$unum['data']    = $db->COUNT(__TBL_USER__,"dataflag=0 AND kind<>4");
	$unum['photo_s'] = $db->COUNT(__TBL_USER__,"photo_f=0 AND photo_s<>'' AND kind<>4");
	$unum['rz']      = $db->COUNT(__TBL_RZ__,"flag=0 AND rzid<>'photo'");
	$unum['photo']   = $db->COUNT(__TBL_PHOTO__,"flag=0");
	$unum['video']   = $db->COUNT(__TBL_VIDEO__,"flag=0");
	$unum['tx']   = $db->COUNT(__TBL_PAY__,"kind=-1 AND flag=0");
	$tg1=$db->COUNT(__TBL_USER__,"tgflag=0 AND tguid<>''");
	$tg2=$db->COUNT(__TBL_TG_USER__,"tgflag=0 AND tguid<>''");
	$unum['tg']=$tg1+$tg2;
	$unum['315'] = $db->COUNT(__TBL_315__,"flag=0");
	json_exit(array('flag'=>1,'unum'=>$unum));
}
?>
<style>
.rim{display:inline-block;padding:0 6px;font-size:12px;text-align:center;background-color:#EE5A4E;color:#fff;border-radius:2px;height:18px;line-height:18px;margin-left:5px}
</style>
<div class="Lbox">
<?php switch ($_GET['menuid']) {case 1: ?>
    <h3><span class="switchs on"></span><i class="ico">&#xe6fd;</i>数据分析</h3>
    <ul class="welcome">
        <li id="LB1" class="sub_menu"><a onClick="LBfun('1','welcome.php');">统计待办</a></li>
        <li id="LB2" class="sub_menu"><a onClick="LBfun('2','analyse_month.php');">数据月报<font class="newdian"></font></a></li>
        <li id="LB3" class="sub_menu"><a onClick="LBfun('3','analyse_u.php');">用户分析<font class="newdian"></font></a></li>
        <li id="LB4" class="sub_menu"><a onClick="LBfun('4','analyse_hn_month.php');">红娘月报<font class="newdian"></font></a></li>
    </ul>
    <h3><span class="switchs on"></span><i class="ico">&#xe614;</i>用户轨迹</h3>
    <ul>
        <li id="LB5" class="sub_menu"><a onClick="LBfun('5','pay.php');">支付清单</a></li>
        <li id="LB6" class="sub_menu"><a onClick="LBfun('6','loveb.php');"><?php echo $_ZEAI['loveB'];?>清单</a></li>
        <li id="LB7" class="sub_menu"><a onClick="LBfun('7','money.php');">余额清单</a></li>
        <hr />
        <li id="LB8" class="sub_menu"><a onClick="LBfun('8','tip_list.php');">消息通知</a></li>
        <li id="LB9" class="sub_menu"><a onClick="LBfun('9','chat_list.php');">私信聊天</a></li>
        <li id="LB10" class="sub_menu"><a onClick="LBfun('10','view_list.php');">谁看过我</a></li>
        <li id="LB11" class="sub_menu"><a onClick="LBfun('11','gz_list.php');">用户关注</a></li>
    </ul>
    <h3><span class="switchs on"></span><i class="ico">&#xe621;</i>管理员/红娘</h3>
    <ul>
		<li id="LB13" class="sub_menu"><a onClick="LBfun('13','role.php');">管理员用户组</a></li>
		<li id="LB14" class="sub_menu"><a onClick="LBfun('14','adminuser.php');">管理员用户</a></li>
        <li id="LB12" class="sub_menu"><a onClick="LBfun('12','log.php');">操作日志<font class="newdian"></font></a></li>
    </ul>
    
<?php break;case 2:?>
    <h3><span class="switchs on"></span><i class="ico">&#xe603;</i>用户管理</h3>
    <ul>
        <li id="LB1" class="sub_menu"><a onClick="LBfun('1','u.php');">用户管理</a></li>
        <li id="LB2" class="sub_menu"><a onClick="LBfun('2','u_select.php');">用户查询</a></li>
        <li id="LB3" class="sub_menu"><a onClick="LBfun('3','u_add.php');">用户录入</a></li>
        <li id="LB4" class="sub_menu"><a onClick="LBfun('4','excel_out.php');">数据导出</a></li>
        <hr>
        <li id="LB8" class="sub_menu"><a onClick="LBfun('8','urole.php');">VIP套餐<font class="newdian"></font></a></li>
		<hr>
        <li id="LB5" class="sub_menu"><a onClick="LBfun('5','crm_claim_public.php');" id="unum_gh">公海认领</a></li>
        <li id="LB6" class="sub_menu"><a onClick="LBfun('6','crm_claim.php');">认领管理</a></li>
        <li id="LB7" class="sub_menu"><a onClick="LBfun('7','u_qianxian.php');">牵线管理</a></a></li>
    </ul>
    <h3><span class="switchs on"></span><i class="ico">&#xe679;</i> 用户审核</h3>
    <ul>
        <li id="LB10" class="sub_menu"><a onClick="LBfun('10','u_jb_list.php?sort=myinfobfb1');" id="unum_data">资料审核</a></li>
        <li id="LB11" class="sub_menu"><a onClick="LBfun('11','photo_m.php');" id="unum_photo_s">头像/置顶</a></li>
        <li id="LB12" class="sub_menu"><a onClick="LBfun('12','cert.php');" id="unum_rz">认证审核</a></li>
        <li id="LB13" class="sub_menu"><a onClick="LBfun('13','photo.php');" id="unum_photo">相册审核</a></li>
		<li id="LB14" class="sub_menu"><a onClick="LBfun('14','video.php');" id="unum_video">视频审核</a></li>
		<li id="LB15" class="sub_menu"><a onClick="LBfun('15','wxewm.php');">微信二维码</a></li>
        <hr>
        <li id="LB16" class="sub_menu"><a onClick="LBfun('16','withdraw.php');" id="unum_tx">提现审核</a></li>
        <li id="LB17" class="sub_menu"><a onClick="LBfun('17','u_tg.php');" id="unum_tg">推广验证</a></li>
        <li id="LB18" class="sub_menu"><a onClick="LBfun('18','315.php');" id="unum_315">举报中心</a></li>
        <hr>
        <li id="LB19" class="sub_menu"><a onClick="LBfun('19','u_adm.php');">业务员采集审核</a></li>
        <li id="LB20" class="sub_menu"><a onClick="LBfun('20','u_jb_list_sq.php');">售前用户审核</a></li>
        <li id="LB21" class="sub_menu"><a onClick="LBfun('21','u_jb_list_sq_view.php');">售前审核反馈</a></li>
    </ul>
	<script>
    setTimeout(function(){
        zeai.ajax({url:'left'+zeai.ajxext+'submitok=ajax_unum'},function(e){var rs=zeai.jsoneval(e);
            if(rs.flag==1){
                if(rs.unum['gh']>0)setTimeout(function(){unum_gh.append('<span class="rim">'+rs.unum['gh']+'</span>');},100);
                if(rs.unum['data']>0)setTimeout(function(){unum_data.append('<span class="rim">'+rs.unum['data']+'</span>');},200);
                if(rs.unum['photo_s']>0)setTimeout(function(){unum_photo_s.append('<span class="rim">'+rs.unum['photo_s']+'</span>');},300);
                if(rs.unum['rz']>0)setTimeout(function(){unum_rz.append('<span class="rim">'+rs.unum['rz']+'</span>');},400);
                if(rs.unum['photo']>0)setTimeout(function(){unum_photo.append('<span class="rim">'+rs.unum['photo']+'</span>');},500);
                if(rs.unum['video']>0)setTimeout(function(){unum_video.append('<span class="rim">'+rs.unum['video']+'</span>');},600);
                if(rs.unum['tx']>0)setTimeout(function(){unum_tx.append('<span class="rim">'+rs.unum['tx']+'</span>');},700);
                if(rs.unum['tg']>0)setTimeout(function(){unum_tg.append('<span class="rim">'+rs.unum['tg']+'</span>');},800);
                if(rs.unum['315']>0)setTimeout(function(){unum_315.append('<span class="rim">'+rs.unum['315']+'</span>');},900);
            }
        });
    },500);
    </script>
<?php break;case 3:?>
    <h3><span class="switchs on"></span><i class="ico">&#xe63c;</i>内容管理</h3>
    <ul>
        <li id="LB1" class="sub_menu"><a onClick="LBfun('1','about.php?t=us');">关于我们</a></li>
        <li id="LB2" class="sub_menu"><a onClick="LBfun('2','about.php?t=contact');">客服信息</a></li>
        <li id="LB3" class="sub_menu"><a onClick="LBfun('3','about.php?t=declara');">隐私条款</a></li>
		<hr>
        <li id="LB4" class="sub_menu"><a onClick="LBfun('4','news.php?ifgg=1');">网站公告</a></li>
        <li id="LB5" class="sub_menu"><a onClick="LBfun('5','news.php');">文章管理</a></li>
    </ul>
    <h3><span class="switchs on"></span><i class="ico">&#xe647;</i>应用管理</h3>
    <ul>
        <li id="LB6" class="sub_menu"><a onClick="LBfun('6','party.php');">交友活动</a></li>
        <li id="LB7" class="sub_menu"><a onClick="LBfun('7','dating.php');">约会管理</a></li>
        <li id="LB8" class="sub_menu"><a onClick="LBfun('8','trend.php');">交友圈管理</a></li>
        <hr>
        <li id="LB9" class="sub_menu"><a onClick="LBfun('9','gift.php');">礼物管理</a></li>
        <li id="LB10" class="sub_menu"><a onClick="LBfun('10','hongbao.php');">红包管理</a></li>
        <li id="LB11" class="sub_menu"><a onClick="LBfun('11','group_list.php');">圈子管理</a></li>
    </ul>
<?php break;case 4:?>
    <h3><span class="switchs on"></span><i class="ico">&#xe6fd;</i>运营管理</h3>
    <ul>
        <li id="LB5" class="sub_menu"><a onClick="LBfun('5','bounce.php?t=indexgg');">首页海报</a></li>
        <li id="LB4" class="sub_menu"><a onClick="LBfun('4','bounce.php?t=vipdatarz');">反弹海报</a></li>
        <li id="LB6" class="sub_menu"><a onClick="LBfun('6','var.php?t=6');">广告管理</a></li>
        <hr>
        <li id="LB15" class="sub_menu"><a onClick="LBfun('15','form.php');">表单采集<font class="newdian"></font></a></li>
        <hr>
        <li id="LB1" class="sub_menu"><a onClick="LBfun('1','wx_gzh.php?t=1');">公众号推送</a></li>
        <li id="LB2" class="sub_menu" style="display:none"><a onClick="LBfun('2','wx_gzh.php?t=2');">公众号群发</a></li>
        <li id="LB13" class="sub_menu"><a onClick="LBfun('13','u_adm.php?t=ewm');">线下业务员</a></li>
        <li id="LB14" class="sub_menu"><a onClick="LBfun('14','robot.php');">AI机器人</a></li>
	</ul>
    <h3><span class="switchs on"></span><i class="ico">&#xe615;</i><?php echo $TG_set['navtitle'];?></h3>    
    <ul>
        <li id="LB7" class="sub_menu"><a onClick="LBfun('7','TG_welcome.php');">推广数据统计</a></li>
        <li id="LB8" class="sub_menu"><a onClick="LBfun('8','TG_set.php?t=set');">推广全局设置</a></li>
        <li id="LB9" class="sub_menu"><a onClick="LBfun('9','TG_role.php');">推广等级套餐</a></li>
        <li id="LB10" class="sub_menu"><a onClick="LBfun('10','TG_u.php?k=1');">推广<?php echo $TG_set['tgytitle'];?>管理</a></li>
        <li id="LB11" class="sub_menu"><a onClick="LBfun('11','u_tg.php');">用户有效验证</a></li>
        <li id="LB12" class="sub_menu"><a onClick="LBfun('12','u_select.php?iftj=TG_xqk');">优质用户推荐</a></li>
    </ul>
    
<?php break;case 5:?>
    <h3><span class="switchs on"></span><i class="ico">&#xe649;</i>基础配置</h3>
    <ul>
        <li id="LB1" class="sub_menu"><a onClick="LBfun('1','var.php');">站点设置</a></li>
        <li id="LB3" class="sub_menu"><a onClick="LBfun('3','var.php?t=nav');">模块导航<font class="newdian"></font></a></li>
        <li id="LB4" class="sub_menu"><a onClick="LBfun('4','switchs.php?t=1');">注册设置</a></li>
        <li id="LB5" class="sub_menu"><a onClick="LBfun('5','udata3.php');">资料属性</a></li>
        <li id="LB6" class="sub_menu"><a onClick="LBfun('6','switchs.php?t=2');"><?php echo $_ZEAI['loveB'];?>配置</a></li>
        <li id="LB7" class="sub_menu"><a onClick="LBfun('7','switchs.php?t=3');">功能开关<font class="newdian"></font></a></li>
        <hr>
		<li id="LB13" class="sub_menu"><a onClick="LBfun('13','area1.php');">工作地区</a></li>
		<li id="LB14" class="sub_menu"><a onClick="LBfun('14','areahj1.php');">户籍地区</a></li>
        <hr>
		<li id="LB15" class="sub_menu"><a onClick="LBfun('15','backup.php');">数据备份</a></li>
        <li id="LB16" class="sub_menu" style="display:none"><a onClick="LBfun('16','');">择爱商店<font class="newdian"></font></a></li>
    </ul>
    <h3><span class="switchs on"></span><i class="ico">&#xe647;</i>第三方配置</h3>
    <ul>
		<li id="LB8" class="sub_menu"><a onClick="LBfun('8','var.php?t=2');">微信公众号</a></li>
		<li id="LB9" class="sub_menu"><a onClick="LBfun('9','var.php?t=5');">支付设置</a></li>
		<li id="LB12" class="sub_menu"><a onClick="LBfun('12','var.php?t=rz');">认证配置<font class="newdian"></font></a></li>
		<li id="LB10" class="sub_menu"><a onClick="LBfun('10','var.php?t=3');">帐号互联</a></li>
		<li id="LB11" class="sub_menu"><a onClick="LBfun('11','var.php?t=4');">短信邮箱</a></li>
    </ul>
<?php break;case 6:?>
    <h3><span class="switchs on"></span><i class="ico2">&#xe71a;</i>商家管理V3</h3>
    <ul>
		<li id="LB1" class="sub_menu"><a onClick="LBfun('1','shop_welcome.php');"><?php echo $_SHOP['title'];?>数据统计</a></li>
        <li id="LB2" class="sub_menu"><a onClick="LBfun('2','shop_set.php?t=nav');"><?php echo $_SHOP['title'];?>全局设置<font class="newdian"></font></a></li>
        <li id="LB3" class="sub_menu"><a onClick="LBfun('3','shop_role.php');"><?php echo $_SHOP['title'];?>等级套餐</a></li>
        <hr>
        <li id="LB4" class="sub_menu"><a onClick="LBfun('4','TG_u.php?k=2');"><?php echo $_SHOP['title'];?>管理</a></li>
        <li id="LB9" class="sub_menu"><a onClick="LBfun('9','shop_u.php');" title="只注册了买家帐号，推广和<?php echo $_SHOP['title'];?>帐号未激活的用户">买家用户<font class="newdian"></font></a></li>
        <li id="LB5" class="sub_menu"><a onClick="LBfun('5','TG_product.php');">商品管理</a></li>
        <li id="LB6" class="sub_menu"><a onClick="LBfun('6','');" style="display:none">优惠卷</a></li>
        <li id="LB7" class="sub_menu"><a onClick="LBfun('7','shop_order.php');">订单管理</a></li>
        <li id="LB8" class="sub_menu"><a onClick="LBfun('8','shop_tip_list.php');">消息通知</a></li>
    </ul>
<?php break;}?>
</div>
<script>
$(".switchs").each(function(i){
    var ul = $(this).parent().next();
	var thiss = $(this);
    $(this).parent().click(
    function(){
        if(ul.is(':visible')){
            ul.hide();
            thiss.removeClass('on');
       }else{
            ul.show();
            thiss.addClass('on');
        }
    })
});
</script>
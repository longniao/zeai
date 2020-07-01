<?php
require_once '../../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
?>
<?php switch ($_GET['menuid']) {case 1: ?>
    <h3><span class="switchs on"></span>快捷导航</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','user.php');">红娘管理</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','user_jb_list.php');">会员录入</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','photo_m.php');">会员筛选</a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','photo.php');">会员配对</a></li>
        <li id="CB5" class="sub_menu"><a onClick="CBfun('5','user_mod_data.php');">前台关于我们</a></li>
        <li id="CB6" class="sub_menu"><a onClick="CBfun('6','user_mod_data.php');">Logo/名称等</a></li>
    </ul>
<?php break;case 2:?>
    <h3><span class="switchs on"></span>系统管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','user.php');">红娘管理</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','user_jb_list.php');">角色管理</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','photo_m.php');">会员分析</a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','photo.php');">红娘分析</a></li>
        <li id="CB5" class="sub_menu"><a onClick="CBfun('5','user_mod_data.php');">前台关于我们</a></li>
        <li id="CB6" class="sub_menu"><a onClick="CBfun('6','user_mod_data.php');">Logo/名称等</a></li>
    </ul>
<?php break;case 3:?>
    <h3><span class="switchs on"></span>会员管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','user.php');">统计分析</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','user_jb_list.php');">会员筛选</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','photo_m.php');">售前会员分配</a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','photo.php');">售后会员分配</a></li>
        <li id="CB5" class="sub_menu"><a onClick="CBfun('5','user_mod_data.php');">会员调配(换红娘)</a></li>
    </ul>
<?php break;case 4:?>
    <h3><span class="switchs on"></span>售前管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','trend.php?t=pic');">约会管理</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','dating.php?t=1');">约会流程</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','dating_user.php?t=2');">约会名单</a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','dating_user.php?t=2');">约会免责声明</a></li>
        <li id="CB5" class="sub_menu"><a onClick="CBfun('5','dating_user.php?t=2');">约会名单</a></li>
        <li id="CB6" class="sub_menu"><a onClick="CBfun('6','trend.php');">动态管理</a></li>
    </ul>
<?php break;case 5:?>
    <h3><span class="switchs on"></span>售后管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','party.php');">活动管理</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','party.php?submitok=user');">报名会员审核</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','party.php?submitok=bbs');">活动留言</a></li>
    </ul>
<?php break;case 6:?>


    <h3><span class="switchs on"></span>合同管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','honor.php');">个人资料设置</a></li>
		<li id="CB2" class="sub_menu"><a onClick="CBfun('2','honor_hand.php');">会员级别权限</a></li>
		<li id="CB3" class="sub_menu"><a onClick="CBfun('3','honor_hand.php');">功能开关</a></li>
		<li id="CB4" class="sub_menu"><a onClick="CBfun('4','honor_hand.php');">非法IP屏蔽</a></li>
		<li id="CB5" class="sub_menu"><a onClick="CBfun('5','honor_hand.php');">举报中心</a></li>
		<li id="CB6" class="sub_menu"><a onClick="CBfun('6','honor_hand.php');">信息发布</a></li>
    </ul>
    </ul>
<?php break;case 7:?>
    <h3><span class="switchs on"></span>财务管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','news.php?classid=1&submitok=mod&kind=11');">基本设置</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','news.php?classid=2&submitok=mod&kind=12');">财务积分管理</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','news.php?kind=1');">管理员权限分配</a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','news.php?kind=2');">系统备份</a></li>
    </ul>

<?php break;}?>








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
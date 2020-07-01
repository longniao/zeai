<?php
require_once '../sub/init.php';
define('ZEAI2',dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once 'chkUadm.php';
//$SQL = getAgentSQL();
?>
<style>
.rim{display:inline-block;padding:0 6px;font-size:12px;text-align:center;background-color:#EE5A4E;color:#fff;border-radius:2px;height:18px;line-height:18px;margin-left:5px}
</style>
<?php switch ($_GET['menuid']) {case 1: ?>
    <h3><span class="switchs on"></span>快捷导航</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','crm_welcome.php');">待办统计</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','crm_claim_public.php');">公海用户认领<span class="rim"><?php echo $db->COUNT(__TBL_USER__,"admid=0 AND kind<>4 AND mob<>'' AND mob<>0 AND myinfobfb>10".$SQL);
?></span></a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','crm_claim.php');">认领管理<font class="newdian"></font></a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','log.php');">操作日志<font class="newdian"></font></a></li>
        
        <li id="CB5" class="sub_menu"><a onClick="CBfun('5','crm_hn_work.php?crmkind=sq');" style="display:none">红娘业绩统计</a></li>

    </ul>
<?php break;case 2:?>
    <h3><span class="switchs on"></span>系统管理</h3>
    <ul>
    	<?php if (in_array('crm',$QXARR)){?>
            <li id="CB1" class="sub_menu"><a onClick="CBfun('1','crm_agent.php');">门店管理</a></li>
            <li id="CB2" class="sub_menu"><a onClick="CBfun('2','crm_role.php');">红娘角色<font class="newdian"></font></a></li>
        <?php }?>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','crm_hn.php');">红娘管理</a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','crm_hn_bbs.php');">红娘评价</a></li>
        <li id="CB5" class="sub_menu"><a onClick="CBfun('5','crm_news.php');">通知公告</a></li>
        <?php if (in_array('crm',$QXARR)){?>
            <li id="CB6" class="sub_menu"><a onClick="CBfun('6','crm_udata3.php');">CRM资料属性<font class="newdian"></font></a></li>
        <?php }?>
    </ul>
<?php break;case 3:?>
    <h3><span class="switchs on"></span>客户管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','crm_user.php');" title="只显示已认领用户">客户管理</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','u_add.php');">客户录入</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','u_select.php?k=crm');">用户查询<font class="newdian"></font></a></li>
    </ul>
<?php break;case 4:?>
    <h3><span class="switchs on"></span>售前管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','crm_sq.php?t=1');" title="认领后未签约的用户">售前客户列表<font class="newdian"></font></a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','crm_user_bbs.php?k=sq');">售前跟进列表</a></li>
    </ul>
<?php break;case 5:?>
    <h3><span class="switchs on"></span>售后管理</h3>
    <ul>
    	<li id="CB1" class="sub_menu"><a onClick="CBfun('1','crm_sh.php?t=3');" title="认领后（已签约升级了客户等级）的用户">售后客户列表</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','crm_user_bbs.php?k=sh');">售后跟进管理</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','u_qianxian.php?k=crmqx');">售后牵线管理</a></a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','crm_user_meet.php');">售后约见管理</a></li>
    </ul>
<?php break;case 6:?>
    <h3><span class="switchs on"></span>合同管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','crm_ht.php?t=htflagall');">合同管理</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','crm_ht.php?t=htflag0');">待审合同</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','crm_ht.php?t=htflag2');">被驳回合同</a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','crm_ht.php?t=htflag1');">已审合同</a></li>
		<li id="CB5" class="sub_menu"><a onClick="CBfun('5','crm_ht.php?submitok=add&t=htflag0');">录入合同（签约）</a></li>
    </ul>
<?php break;case 7:?>
    <h3><span class="switchs on"></span>财务管理</h3>
    <ul>
        <li id="CB1" class="sub_menu"><a onClick="CBfun('1','crm_ht.php?t=payflagall');">付款管理</a></li>
        <li id="CB2" class="sub_menu"><a onClick="CBfun('2','crm_ht.php?t=payflag0');">待审付款</a></li>
        <li id="CB3" class="sub_menu"><a onClick="CBfun('3','crm_ht.php?t=payflag2');">被驳回付款</a></li>
        <li id="CB4" class="sub_menu"><a onClick="CBfun('4','crm_ht.php?t=payflag1');">已审核付款</a></li>
        <li id="CB5" class="sub_menu"><a onClick="CBfun('5','crm_pay_analyse.php');">财务统计/分析</a></li>
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
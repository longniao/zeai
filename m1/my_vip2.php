<?php
require_once '../sub/init.php';
$currfields  = 'if2,sjtime,sign_time,grade,sex';
require_once ZEAI.'my_chk_u.php';
require_once ZEAI.'cache/config_vip.php';
$data_sex = $row['sex'];
$data_grade = $row['grade'];
$data_if2   = $row['if2'];
$urole  = json_decode($_ZEAI['urole'],true);
$sj_if2 = json_decode($_VIP['sj_if2'],true);
$sj_rmb1 = json_decode($_VIP['sj_rmb1'],true);
$sj_rmb2 = json_decode($_VIP['sj_rmb2'],true);
//
$mini_backT = '';
$curpage = 'my_vip2';
$mini_title = 'VIP会员升级';
require_once ZEAI.'m1/top_mini.php';
$maxgrade = getArrayMax($urole,'g','max');
?>
<style>
.my_vip2{background-color:#fff;padding:20px 0 20px;text-align:left}
.my_vip2 dl{width:100%;margin:0 auto;clear:both;overflow:auto;border-bottom:#ddd 0.5px solid;padding:10px 0}
.my_vip2 dl:last-child{border-bottom:0}
.my_vip2 dl dt,.my_vip2 dl dd{float:left;display:block;line-height:30px}/*;background-color:#fc0*/
.my_vip2 dl dt,.my_vip2 dl dd{line-height:40px}/*;background-color:#fc0*/
.my_vip2 dl dt{width:20%;margin:0 5%;font-size:16px;color:#666;text-align:right}
.my_vip2 dl dd{width:70%;text-align:left}
.my_vip2 dl dd em{cursor:pointer;float:left;line-height:28px;padding:0 10px;margin:5px 10px 10px 0;border:#cfcfcf 1px solid;color:#666;background-color:#f5f5f5;text-align:center;font-size:16px;position:relative;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-user-select:none;-moz-user-select: none;}
.my_vip2 dl dd em.ed{color:#A1C655;border:#A1C655 1px solid;background-color:#fff}
.my_vip2 dl dd em .ibox {width:0;height:0;border-width:7px;border-style:solid;border-color:transparent #95C057 #95C057 transparent;position:absolute;bottom:0;right:0;display:none}
.my_vip2 dl dd em .ibox h4{background-image:url('res/xgz.png');background-repeat:no-repeat;position:absolute;bottom:-7px;right:-8px;width:14px;height:14px;background-position:left right;background-size:14px 14px;}
.my_vip2 dl dd em.ed .ibox{display:block}
.my_vip2 dl dd span:first-child{color:#f60;font-size:16px}
.my_vip2 dl dd span:last-child{color:#999;font-size:12px}
.my_vip2 .tips{width:90%;font-size:14px;color:#999;padding:10px 0 ;line-height:200%;box-sizing:border-box;margin:0 auto;text-align:center}

.maxgrade{text-align:center;width:70%;background-color:#f8f8f8;margin:0 auto;padding:30px;border:#f2f2f2 1px solid;box-sizing:border-box}
.maxgrade span{display:block;margin-bottom:30px;font-size:18px;color:#f70}
.maxgrade em{margin-top:20px;font-size:16px;color:#999}
</style>
<?php
?>
<i class='ico goback' id='ZEAIGOBACK-<?php echo $curpage;?>'>&#xe602;</i>
<div class="submain my_vip2">
	<?php if ($data_grade >= $maxgrade && $data_if2>=999){?>
    <div class="maxgrade">
    	 <?php echo uicon($data_sex.$data_grade,3); ?><br><span>永久<?php echo utitle($data_grade); ?></span>
         <em>您已是最高等级，无需再升</em>
    </div>
    
    <?php }else{ ?>
    <form id="zeaiFORM" class="my_vip2">
        <dl>
            <dt>开通服务</dt>
            <dd id="gradelist">
                <?php
				foreach($urole as $RV){$g = $RV['g'];
					if($g==1)continue;
					if($data_grade >$g)continue;
					if($data_grade >=$g && $data_if2==999)continue;
					?>
                    <em id="grade<?php echo $g;?>" value="<?php echo $g;?>"<?php //echo $emcls;?>>
                        <?php echo uicon($cook_sex.$g,2);?>
                        <?php echo utitle($g); ?>
                        <div class="ibox"><h4></h4></div>
                    </em>
                	<?php }?>
            </dd>
        </dl>
		<dl>
            <dt>服务期限</dt>
            <dd id="if2list">
        	<?php
			foreach($sj_if2 as $v){
				$if2str=get_if2_title($v);
				$emcls = ($v == 999)?' class="ed"':'';
			?>
            <em id="if2<?php echo $v;?>" value="<?php echo $v;?>"><?php echo $if2str;?><div class="ibox"><h4></h4></div></em>
			<?php }?>
			</dd>
		</dl>
		<dl><dt>应付金额</dt><dd><span id="priceVip"></span><span id="priceViptitle"></span></dd></dl>
    	<div style="text-align:center;margin:20px auto"><button type="button" class="btn size4 LV2 W90_" id="nextbtnVip">下一步</button></div>
        <input type="hidden" name="grade" id="grade" value="<?php echo $g;?>">
        <input type="hidden" name="if2" id="if2" value="<?php echo $v;?>">
	</form>
    <div class="clear"></div>
    <div class="linebox"><div class="line W50"></div><div class="title S14 BAI">温馨提示</div></div>
    <h5 class="C666">
        <b>●</b>为了方便计算，系统默认定义一个月等于30天计算费用和服务期限，永久定义为999个月<br>
        <?php $kf_wxpic=dataIO($_ZEAI['kf_wxpic'],'out');
		if (!empty($kf_wxpic)){?><img src="<?php echo $_ZEAI['up2'].'/'.$kf_wxpic;?>"><font>长按或扫码加客服微信</font><?php }?>
    </h5>
	<script>
	var cook_sex=<?php echo $cook_sex;?>;
    (function my_vip2(){
        var
        list1  = gradelist.getElementsByTagName("em"),
        list2  = if2list.getElementsByTagName("em"),
        sj_rmb = (cook_sex==2)?<?php echo $_VIP['sj_rmb2'];?>:<?php echo $_VIP['sj_rmb1'];?>;
        o('grade'+grade.value).class('ed');
        o('if2'+if2.value).class('ed');
        priceVipInit();
        zeai.listEach(list1,function(obj){
            obj.onclick=function(){
                grade.value=this.getAttribute("value");
                priceVipInit(obj);cleardom(obj,list1);
            }
        });
        zeai.listEach(list2,function(obj){
            obj.onclick=function(){
                if2.value=this.getAttribute("value");
                priceVipInit(obj);cleardom(obj,list2);
            }
        });
        function priceVipInit(){
            var grade = o('grade').value;
            var if2   = o('if2').value;
            var money = sj_rmb[grade+'_'+if2];
            priceVip.html(money+'元');
            var daymoney = money/(if2*30);
            priceViptitle.html('　日均'+daymoney.toFixed(2)+'元/天');
        }
        function cleardom(curdom,listobj){
            zeai.listEach(listobj,function(obj){obj.removeClass('ed');});
            curdom.addClass('ed');
        }
        nextbtnVip.onclick=function(){
            if (!zeai.ifint(grade.value) || !zeai.ifint(if2.value)){zeai.msg('请选择会员类型和服务期限');return false;}
            ZeaiM.page.load({url:'m1/my_pay.php',data:{kind:1,grade:grade.value,if2:if2.value,jumpurl:'<?php echo $jumpurl;?>'}},'<?php echo $curpage;?>','my_pay');
        }
    })()
    </script>
    <?php }?>
</div>

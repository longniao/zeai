/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2020/04/20 by supdes
*/
var ul = zeai.tag(o(picliobj),"ul")[0],btnpic = ul.children[0],trend_pic_Slist=[],localIds=[],curnum=0;
btnpic.onclick = btnpicFn;
nextbtn.onclick=function(){
	if(zeai.str_len(o("title").value)>100 || zeai.empty(o("title").value)){zeai.msg('请输入【商品名称】',title);return false;}
	ZeaiM.confirmUp({title:'亲，确定保存发布么？',cancel:'取消',ok:'确定',okfn:function(){
		zeai.ajax({url:'shop_my_goods_addmod'+zeai.extname,form:ZEAI_cnFORM_shop},function(e){rs=zeai.jsoneval(e);
			zeai.msg(rs.msg);
			if(rs.flag==1){setTimeout(function(){zeai.openurl('shop_my_goods.php');},1000);}
		});
	}});
}
function btnpicFn(){
	var piclistV=o('piclist').value;
	trend_pic_Slist = piclistV.split(',');
	curnum=arrLength(trend_pic_Slist);
	if(curnum>=maxnum){zeai.msg('最多只能传'+maxnum+'张哦');return;}
	if(is_h5app()){
		app_uploads({url:'shop_my_goods_addmod'+zeai.ajxext+'submitok=ajax_photo_up_app',num:1},function(e){
			var rs=zeai.jsoneval(e);
			addli(rs._s);
		});
	}else{
		photoUp({
			onclick:false,
			btnadd:ul,
			url:"shop_my_goods_addmod.php",
			submitokBef:"ajax_path_",
			multiple:maxnum,
			wxtmp:true,
			li:function(e){
				if(browser=='wx'){
					addli(e);
				}else{
					addli(e._s);
				}
			},
			end:function(e){
				zeai.msg(0);
			}
		});
	}
}
function addli(url){
	var li = document.createElement('li');
	var img = document.createElement('img');
	var b = document.createElement('b');
	li.appendChild(img);li.appendChild(b);ul.appendChild(li);
	if(browser=='wx'){
		var local=url[0],url=url[1];
		localIds.push(local);
		img.src = local;
	}else{
		img.src = up2+url;
	}
	trend_pic_Slist.push(url);
	trend_pic_Slist = arrReset(trend_pic_Slist);
	piclist.value = trend_pic_Slist.join(",");
	b.onclick = function (){
		li.parentNode.removeChild(li);
		trend_pic_Slist = trend_pic_Slist.remove(url);
		piclist.value  = arrReset(trend_pic_Slist).join(",");
		if(browser=='wx'){
			localIds = localIds.remove(url);
			localIds = arrReset(localIds);
		}else{
			zeai.ajax({url:'shop_my_goods_addmod'+zeai.ajxext+'submitok=ajax_tmp_del',data:{url:url}});
		}
	}
	img.onclick = function (){
		if(browser=='wx'){
			ZeaiM.piczoom({browser:browser,b:local,list:localIds});
		}else{
			ZeaiM.piczoom({browser:browser,b:up2+url.replace('_s.','_b.')});
		}
	}
}
photoUp({
	btnobj:path_s_btn,
	url:"shop_my_goods_addmod.php",
	submitokBef:"ajax_path_",
	end:function(rs){
		zeai.msg(0);zeai.msg(rs.msg);
		if (rs.flag == 1){
			path_s_btn.html('<img src='+up2+rs._s+'>');
			path_s.value=rs._s;
		}
	}
});

if(mod){
	var piclistV=o('piclist').value;
	trend_pic_Slist = piclistV.split(',');
	curnum=arrLength(trend_pic_Slist);
	zeai.listEach(zeai.tag(ul,'li'),function(li,n){
		if(n>0){
			var img=zeai.tag(li,'img')[0],b=zeai.tag(li,'b')[0];
			var url = img.src;
			localIds.push(url);
			b.onclick = function (){
				ZeaiM.confirmUp({title:'确定要删除么？',cancel:'取消',ok:'确定',okfn:function(){
					li.parentNode.removeChild(li);
					trend_pic_Slist = trend_pic_Slist.remove(url.replace(up2,''));
					piclist.value  = arrReset(trend_pic_Slist).join(",");
					if(browser=='wx'){
						localIds = localIds.remove(url);
						localIds = arrReset(localIds);
					}else{
						zeai.ajax({url:'shop_my_goods_addmod'+zeai.ajxext+'submitok=ajax_tmp_del',data:{id:id,url:url}});
					}
				}});
			}
			img.onclick = function (){
				if(browser=='wx'){
					ZeaiM.piczoom({browser:browser,b:local,list:localIds});
				}else{
					ZeaiM.piczoom({browser:browser,b:url.replace('_s.','_b.')});
				}
			}
		}
	});
}
function arrLength(ARR){
	var l=0;
	for(var k=0;k<ARR.length;k++) {
		if(typeof(ARR[k]) == "string")l++;
	}
	return l;
}
function arrReset(ARR){
	var l=[];
	for(var k=0;k<ARR.length;k++) {
		if(  typeof(ARR[k]) == "string" && zeai.str_len(ARR[k])>10 )l.push(ARR[k]);
	}
	return l;
}
tgnumbtn_str.onclick=function(){ZeaiM.div_up({fobj:ZEAI_cnFORM_shop,obj:tgnumbox,h:380});};
tgbfb_btn.onclick=function(){
	tgnumbtn_str.value='直接奖：'+tgbfb1.value+'%　团队奖：'+tgbfb2.value+'%';
	div_up_close.click();
}
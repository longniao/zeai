/**
* Copyright (C)2001-2099 Zeai.cn All rights reserved.
* E-Mail：supdes#qq.com　QQ:797311
* http://www.zeai.cn
* http://www.yzlove.com
* Last update 2019/03/15 by supdes
*/
function photoFn(){
	zeai.listEach(zeai.tag(o('main'),'li'),function(li,i){if(i>0){
			var clsid = li.getAttribute("value"),img,del;
			var lichild = li.children;
			img = lichild[0].children[0];
			del = lichild[2];
			img.onclick = function(){ZeaiPC.piczoom(this.src.replace('_s','_b'));}
			del.onclick = function(e){
				zeai.confirm('确认要删除么？',function(){
					zeai.ajax({url:PCHOST+'/my_photo'+zeai.extname,data:{submitok:'ajax_del',clsid:clsid}},function(e){rs=zeai.jsoneval(e);
						zeai.msg(0);zeai.msg(rs.msg);
						if(rs.flag==1){setTimeout(function(){location.reload(true);},1000);}
					});
				});
			}
	}});
	photoaddfn(o("btnadd1"));
	photoaddfn(o("btnadd2"));
}
function photoaddfn(btnobj){
	zeai.photoUp({
		btnobj:btnobj,
		upMaxMB:upMaxMB,
		url:PCHOST+"/my_photo"+zeai.extname,
		multiple:5,
		submitok:"ajax_pic_path_s_up",
		end:function(rs){
			zeai.msg(rs.msg);
			if(rs.flag==1){zeai.msg(0);zeai.msg('上传成功');setTimeout(function(){location.reload(true);},1000);}
		},
		li:function(rs){
			zeai.msg(0);zeai.msg(rs.msg);
			if (rs.flag == 1){main.append('<li><p><img src="'+up2+rs.dbname+'"></p><h4>刚刚</h4></li>');}
		}
	});
}
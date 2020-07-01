/******************************************
作者: 郭-余-林　Q-Q:7-9-7-3-1-1
未经本人同意，请不要删除版权
WWW。ZEAI.CN
*****************************************/
wx.config({debug:false,appId:appId,timestamp:timestamp,nonceStr:nonceStr,signature:signature,jsApiList:['chooseImage','uploadImage','previewImage']});
wx.ready(function () { 
	document.querySelector('.add').onclick = function () {upload_more( '"camera","album"' ,'正在准备上传...');}
	function upload_more(type,tips) {
		ZEAI_winclose_div();
		setTimeout(ZEAI_win_tips(200,50,tips),300);setTimeout("ZEAI_winclose_tips()",2000);
		var images = {localId: [],serverId: []};
		wx.chooseImage({
			count:9,
			sizeType:['compressed'],
			sourceType: [type],
			success: function (res) {
				images.localId = res.localIds;
				var i = 0, length = images.localId.length;
				//if ( (data_photo_num+length) > UpMaxnum ){ZEAI_win_alert(GradeName+'最多只能上传 '+UpMaxnum+' 张','./vip'+ajxext);return false;}
				images.serverId = [];
				function wxupload() {
					wx.uploadImage({
					localId:images.localId[i],
					isShowProgressTips:0,
					success: function (res) {
						images.serverId.push(res.serverId);//
						i++;
						ZEAI_win_tips(200,50,tipsL+"正在上传　第 "+i+" 张"+tipsR);
						if (i < length) {setTimeout(wxupload,300);}else{
							//setTimeout("ZEAI_winclose_tips()",1000);
							var sendjson = {'submitok':'up_party_photo','serverIds':images.serverId,'mainid':mainid,'clubid':clubid};
							ZEAI_win_tips(200,50,tipsL+"请稍后，正在保存"+tipsR);
							Zeai_POST('./group'+ajxext_,sendjson);
						}
					},
					fail: function (res) {/*alert(JSON.stringify(res));*/}
					});
				}
				wxupload();
			}
		});
	}
});
wx.error(function (res) {alert(res.errMsg);});
function XML_del(id){
	var liobj = getid('li'+id);
	liobj.parentNode.removeChild(liobj);
	//data_photo_num--;
	//getid('data_photo_num').innerHTML = data_photo_num;
	SendData('group'+ajxext+'submitok=party_phpto_del_update&mainid='+mainid+'&clubid='+clubid+'&id='+id+'&t='+new Date().getTime());
}
function retn(str){alert(str);}
/******************************************
作者: 郭余林　QQ:797311 (supdes)
未经本人同意，请不要删除版权
WWW.ZEAI.CN
*****************************************/
wx.config({debug:false,appId:appId,timestamp:timestamp,nonceStr:nonceStr,signature:signature,jsApiList:['chooseImage','uploadImage']});
wx.ready(function () { 
	document.querySelector('#chooseImage1').onclick = function () {upload( 'album','正在打开相册...');}
	document.querySelector('#chooseImage2').onclick = function () {upload( 'camera','正在启动相机');}
	function upload(type,tips) {
		ZEAI_winclose_div();
		setTimeout(ZEAI_win_tips(200,50,tips),300);setTimeout("ZEAI_winclose_tips()",1000);
		var images = {localId: [],serverId: []};
		wx.chooseImage({
			count:1,
			sizeType:['compressed'],
			sourceType: [type],
			success: function (res) {
				images.localId = res.localIds;
				var i = 0, length = images.localId.length;
					//tips = tipsL+"正在上传..."+tipsR;
					//setTimeout(ZEAI_win_tips(200,50,tips),200);
				images.serverId = [];
				function wxupload() {
					wx.uploadImage({
					localId: images.localId[i],
					isShowProgressTips:1,
					success: function (res) {
						i++;
						tips = tipsL+"...请稍后..."+tipsR;ZEAI_win_tips(200,55,tips);
						if (i < length) {setTimeout(wxupload,300);}else{
							//setTimeout("ZEAI_winclose_tips()",1000);
							var sendjson = {'submitok':'up_group_mainphoto','mainid':mainid,'serverIds':res.serverId};
							Zeai_POST('group'+rtrim(ajxext),sendjson);
						}
					},
					fail: function (res) {alert(JSON.stringify(res));}
					});
				}
				wxupload();
			}
		});
	}
});
//getid('photo_s_C').onclick = function(){ZEAI_win_div(270,'box_photo_up','上传像头');}
//getid('box_div').addEventListener('touchmove',function(e) {e.preventDefault();});
wx.error(function (res) { alert(res.errMsg);});

function picview_group_mainphoto(url) {
	wx.previewImage({
		current: url,
		urls: [url]
	});
}

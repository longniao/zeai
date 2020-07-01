//setList
function setList(list){
	zeai.listEach(zeai.tag(list,'li'),function(li){setList_li(li)});
}
//分页
function waterfall_load(mainobj,e){
	var div = zeai.addtag('div');div.id='p'+p;div.append(e),newlist = zeai.tag(div,'li');
	mainobj.append(div);
	zeai.listEach(newlist,function(obj){
		obj.addClass('alpha0_100');
		setList_li(obj);
	});
}
function setList_li(li){
	var id=li.getAttribute("id");
	var p = li.children[0];
	var vurl = p.getAttribute("value");
	p.style.backgroundImage='url('+vurl+')';
	//vurl = vurl.replace(/.jpg/g,'')+'.';
	//vurl += zeaiext.substring(1,4);
	
	//var s = document.createElement("SOURCE");s.src=vurl;
	//var v = document.createElement("VIDEO");v.className = 'zeaiVbox';
	//v.append(s);li.append(v);
	(function(id){
		p.nextElementSibling.onclick = function (){
			if(is_h5app()){
				app_VideoPlayer('',id);
			}else{
				zeai.openurl(HOST+'/m1/video_detail'+zeai.ajxext+'fid='+id);
			}
			//zeai.listEach('.zeaiVbox',function(zeaiv){
			//	zeaiv.pause();
			//});
			//zeai.msg('视频加载中...');v.play();
		}
	})(id);
	
	var uA=zeai.tag(li,'dt')[0];
	uA.onclick = function (){
		ZeaiM.page.load('m1/u'+zeai.ajxext+'uid='+this.getAttribute("uid"),ZEAI_MAIN,'u');
	}
	var dt=li.getElementsByTagName("dt")[0];
	var agree=dt.nextElementSibling;
	agree.onclick = function (){
		var self=this;
		zeai.ajax({url:'m1/video'+zeai.extname,js:1,data:{submitok:'ajax_agree',fid:self.getAttribute("vid")}},function(e){rs=zeai.jsoneval(e);
			if(rs.flag==1){
				if(!self.hasClass('ed')){
					self.addClass('ed');
					var font=self.lastChild;
					font.html(parseInt(font.innerHTML)+1);
					zeai.msg(rs.msg);
				}
			}
		});
	}
}

function listOnscroll(){
	var t = parseInt(o('main').scrollTop);
	var cH= parseInt(o('main').clientHeight);
	var  H= parseInt(o('main').scrollHeight);
	if (H-t-cH <128 && t>100){
		if (p > totalP){
			o('main').onscroll = null;
			zeai.msg('已达末页，全部加载结束');
		}else{
			var postjson = {submitok:'ajax_list',totalP:totalP,p:p};
			zeai.ajax({'url':'m1/video'+zeai.extname,data:postjson},function(e){
			if (e == 'end'){
				zeai.msg(0);zeai.msg('已达末页，全部加载结束');
			}else{
				waterfall_load(o(list),e);p++;
			}
		});}
	}
	backtopFn(o('main'));
}

topminibox.addEventListener('touchmove', function(e){e.preventDefault();});
nav.addEventListener('touchmove', function(e){e.preventDefault();});

function waterfall(mainobj){
	zeai.listEach(zeai.tag(mainobj,'li'),function(li){
		setList_li(li);
	});
}
function init(){waterfall(o('list'));}

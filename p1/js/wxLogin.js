/*!function(a,b,c){function d(a){var c="default";a.self_redirect===!0?c="true":a.self_redirect===!1&&(c="false");var d=b.createElement("iframe"),e="https://open.weixin.qq.com/connect/qrconnect?appid="+a.appid+"&scope="+a.scope+"&redirect_uri="+a.redirect_uri+"&state="+a.state+"&login_type=jssdk&self_redirect="+c;e+=a.style?"&style="+a.style:"",e+=a.href?"&href="+a.href:"",d.src=e,d.frameBorder="0",d.allowTransparency="true",d.scrolling="no",d.width="300px",d.height="400px";var f=b.getElementById(a.id);f.innerHTML="",f.appendChild(d)}a.WxLogin=d}(window,document);*/


<div id="login_container"></div>

<script src="js/wxLogin.js"> </script>
<script>

var obj = new WxLogin({
  id:"login_container",
  appid: "<?php echo $_LOGIN['wx_open_appid'];?>", 
  scope: "snsapi_login", 
  redirect_uri: "<?php echo urlencode(HOST.$_SERVER['REQUEST_URI']);?>",
  state: "<?php echo md5('作者QQ797311郭余林'.ADDTIME);?>",
  style: "black",
  href: ""
});

</script>

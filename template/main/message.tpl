<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="./static/css/style.css" />
<script type="text/javascript" src="./static/js/jquery-1.8.2.js"></script>
<title>跳转页面</title>
</head>

<body>
<div class="title"><strong class="l">信息提示</strong><span class="relative r"></span> </div>
<div class="resp-info alert">
  <p id='msg'><{$msg_detail}></p>
  <p id="redirectionMsg"><{if $auto_redirect}>如果您不做出选择，将在 <span id="spanSeconds"><{$timeout}></span> 秒后跳转到第一个链接地址。<{/if}></p>
  <{foreach from=$links item=link}>
    <p class="relative"><a href="<{$link.href}>" <{if $link.target}>target="<{$link.target}>"<{/if}> ><{$link.text}><span></span></a></p>
  <{/foreach}>
  <p class="relative"><a href="/">回到首页<span></span></a></p>
</div>
<style>
body{
	width:99%;
	margin:10px auto;
}
h4{
margin-bottom:10px;
color:#63666d;
}
p{
	text-indent:2em;
	line-height:30px;
	color:#63666d;
	margin:0;
}
#msg{
	padding:10px 0;
	font-size:1.2em;
}
a span{
	position:absolute;
	left:8px;
	top:7px;
	background:url(./static/images/back.png) center center no-repeat;
	height:15px;
	width:15px;
}
.alert{
	background:#f3f4f9;
	border:1px solid #c9ccd3;
	border-top:none;
	border-radius:0 0 5px 5px;
}
</style>
</body>
</html>
<{if $auto_redirect}>
<script language="JavaScript">
<!--
var seconds = <{$timeout}>;
var defaultUrl = "<{$default_url}>";
<{literal}>
onload = function()
{
  if (defaultUrl == 'javascript:history.go(-1)' && window.history.length == 0)
  {
    document.getElementById('redirectionMsg').innerHTML = '';
    return;
  }

  window.setInterval(redirection, 1000);
}
function redirection()
{
  if (seconds <= 0)
  {
    window.clearInterval();
    return;
  }

  seconds --;
  document.getElementById('spanSeconds').innerHTML = seconds;

  if (seconds == 0)
  {
    window.clearInterval();
    location.href = defaultUrl;
  }
}
//-->
</script>
<{/literal}>
<{/if}>

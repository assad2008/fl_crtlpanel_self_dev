<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="./static/css/style.css" />
<script type="text/javascript" src="./static/js/jquery-1.8.2.js"></script>
<title>MENU</title>
</head>

<body>
<script type="text/javascript" src="./static/js/sidemenu.js"></script>
<div id="sidebar" class="menu">
  <ul>
	<{foreach from=$menus item=mi}>
    <li class="lv1"><a href="#this" id="item-jiben" class="more-icon"><span><font><{$mi.label}></font></span></a>
      <ol class="lv2" id="item-jibenol">
	  <{foreach from=$mi.children key=k item=ci}>
      <li><a target="main-frame" href="<{$ci.action}>"  <{if $k==92}> id="view" <{/if}>><{$ci.label}></a></li>
	  <{/foreach}>
      </ol>
    </li>
	<{/foreach}>
  </ul>
</div>
</body>
</html>
<style>
.current{
	font-weight:bold;
	color:#28a7e1 !important;
}
.lv1 a span font{
font-weight:bold;
}
</style>
<script>
$(".lv2>li>a").click(function(){
	$(this).addClass('current').parent().siblings().children('a').removeClass('current');
	$(this).parent().parent().parent().children('a').addClass('current');
	$(this).parent().parent().parent().siblings().children('a').removeClass('current');
	$(this).parent().parent().parent().siblings().children('ol').children('li').children('a').removeClass('current');
});
</script>

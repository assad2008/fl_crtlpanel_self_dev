<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="./static/css/style.css" />
<script type="text/javascript" src="./static/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src='./static/js/table.js'></script>
<title><{if $ur_here}><{$ur_here}> - <{/if}><{$lang.cp_home}></title>
</head>

<body>
<div id="top">
  <div id="header">
    <div id="logo"><img src="./static/images/logo.png" /> </div>
    <div id="user">
      <ul>
        <li class="name"><font color="white">欢迎您，尊敬的<{if $smarty.const.ADMINLEVEL eq 1}>超级管理员<{elseif $smarty.const.ADMINLEVEL eq 2}>普通管理员<{elseif $smarty.const.ADMINLEVEL eq 8}>特殊管理员<{elseif $smarty.const.ADMINLEVEL eq 9}>CP管理员<{/if}>：<{$smarty.const.TRUENAME}>(<{$smarty.const.USERNAME}>)</font></li>
        <li class="set"><a href="http://opengame.feiliu.com?sso=1" target="_blank">OPEN_GAME>>></a></li>
		<li class="exit"><a href="?c=login&a=logout">退出</a></li>
      </ul>
    </div>
  </div>
   <div id="navbar">
	<div class='nav-lvl-1' id="product"><a href="/"><strong>产品中心</strong></a></div>
	<div class='nav-lvl-1'>></div>
	<div class='nav-lvl-1'><{$ur_here}></div>
  </div>
</div>
<div class='wrap'>
	<div class="title">
	<strong class="l"><{$ur_here}></strong>
	<span class="relative r"><{if $action_link}><a class="buttons4" href="<{$action_link.href}>"><font><{$action_link.text}></font></a><{/if}></span>
	</div>

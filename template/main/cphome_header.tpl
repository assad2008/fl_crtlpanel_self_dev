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

<div class='wrap'>
	<div class="title">
	<strong class="l"><{$ur_here}></strong>
	<span class="relative r">
		<{if $action_link_3}><a class="buttons4" href="<{$action_link_3.href}>"><font><{$action_link_3.text}></font></a><{/if}>
		<{if $action_link_2}><a class="buttons4" href="<{$action_link_2.href}>"><font><{$action_link_2.text}></font></a><{/if}>
        <{if $action_link_1}><a class="buttons4" href="<{$action_link_1.href}>"<{if $action_link_1.target}> target="__blank" <{/if}>><font><{$action_link_1.text}></font></a><{/if}>
		<{if $action_link}><a class="buttons4" href="<{$action_link.href}>"><font><{$action_link.text}></font></a><{/if}>
	</span>
	</div>

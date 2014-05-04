<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="./static/css/style.css" />
<script type="text/javascript" src="./static/js/jquery-1.8.2.js"></script>
<title><{$lang.cp_home}></title>
</head>

<body>
<div id="top">
  <div id="header">
    <div id="logo"><img src="./static/images/logo.png" /> </div>
    <div id="user">
      <ul>
        <li class="name"><font color="white">欢迎您，尊敬的<{if $smarty.const.ADMINLEVEL eq 1}>超级管理员<{elseif $smarty.const.ADMINLEVEL eq 2}>管理员<{/if}>：<{$smarty.const.TRUENAME}>(<{$smarty.const.USERNAME}>)</font></li>
        <li class="exit"><a href="?c=login&a=logout">退出</a></li>
      </ul>
    </div>
  </div>
  <div id="navbar">
	<div class='nav-lvl-1' id="product"><a href="/"><strong>产品中心</strong></a></div>
	<div class='nav-lvl-1'>></div>
	<div class="nav-lvl-1 productText product">
		<h4><strong>客服管理</strong></h4>
	</div>
  </div>
</div>
<div id="bottom">
  <div id="left">
    <iframe id="link0" src="?c=crmhome&a=menu" name="menu-frame" frameborder="0" allowtransparency="true"></iframe>
  </div>
  <div id="right">
    <div id="content" style="height:100%">
    <iframe id="cont0" name="main-frame" frameborder="0" allowtransparency="true"></iframe>
    </div>
  </div>
</div>
<script>
	$(function(){
		var h=document.documentElement.clientHeight;
        $("#content").css({
			"height":h-101+"px"
		})
		$('#left iframe').css({
			"height":h-101+"px"
		})
	})
	window.onresize=function(){
		var h=document.documentElement.clientHeight;
        $("#content").css({
			"height":h-101+"px"
		})
		$('#left iframe').css({
			"height":h-101+"px"
		})
	}
	$('.product').click(function(){
		var $list = $(this).children('.drop-select-list'),
			display = $list.css('display');
		if(display=='none') $list.slideDown('fast');
		else $list.slideUp('fast');
	})
	$(document).click(function(e){
		var target  = $(e.target);
		if((target.closest(".product").length == 0)){
			$(".drop-select-list").slideUp('fast');
		}
	})
	$('.drop-select-list a').click(function(){
		$('.product h4').html($(this).attr('title'));
	})



var pid=<{$pid}>
if(pid==9999999){
$("#cont0").attr('src','?c=summary&a=index');
  }


</script>
</body>
</html>

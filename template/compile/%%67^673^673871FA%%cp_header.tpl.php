<?php /* Smarty version 2.6.25, created on 2014-05-04 12:17:32
         compiled from cp_header.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="./static/css/style.css" />
<script type="text/javascript" src="./static/js/jquery-1.8.2.js"></script>
<script type="text/javascript" src='./static/js/table.js'></script>
<title><?php if ($this->_tpl_vars['ur_here']): ?><?php echo $this->_tpl_vars['ur_here']; ?>
 - <?php endif; ?><?php echo $this->_tpl_vars['lang']['cp_home']; ?>
</title>
</head>

<body>
<div id="top">
  <div id="header">
    <div id="logo"><img src="./static/images/logo.png" /> </div>
    <div id="user">
      <ul>
        <li class="name"><font color="white">欢迎您，尊敬的<?php if (@ADMINLEVEL == 1): ?>超级管理员<?php elseif (@ADMINLEVEL == 2): ?>普通管理员<?php elseif (@ADMINLEVEL == 8): ?>特殊管理员<?php elseif (@ADMINLEVEL == 9): ?>CP管理员<?php endif; ?>：<?php echo @TRUENAME; ?>
(<?php echo @USERNAME; ?>
)</font></li>
		<li class="exit"><a href="?c=login&a=logout">退出</a></li>
      </ul>
    </div>
  </div>
   <div id="navbar">
	<div class='nav-lvl-1' id="product"><a href="/"><strong>产品中心</strong></a></div>
	<div class='nav-lvl-1'>></div>
	<div class='nav-lvl-1'><?php echo $this->_tpl_vars['ur_here']; ?>
</div>
  </div>
</div>
<div class='wrap'>
	<div class="title">
	<strong class="l"><?php echo $this->_tpl_vars['ur_here']; ?>
</strong>
	<span class="relative r"><?php if ($this->_tpl_vars['action_link']): ?><a class="buttons4" href="<?php echo $this->_tpl_vars['action_link']['href']; ?>
"><font><?php echo $this->_tpl_vars['action_link']['text']; ?>
</font></a><?php endif; ?></span>
	</div>
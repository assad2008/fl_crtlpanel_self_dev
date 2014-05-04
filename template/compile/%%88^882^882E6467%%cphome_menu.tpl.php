<?php /* Smarty version 2.6.25, created on 2014-05-04 12:40:26
         compiled from cphome_menu.tpl */ ?>
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
	<?php $_from = $this->_tpl_vars['menus']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['mi']):
?>
    <li class="lv1"><a href="#this" id="item-jiben" class="more-icon"><span><font><?php echo $this->_tpl_vars['mi']['label']; ?>
</font></span></a>
      <ol class="lv2" id="item-jibenol">
	  <?php $_from = $this->_tpl_vars['mi']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['ci']):
?>
      <li><a target="main-frame" href="<?php echo $this->_tpl_vars['ci']['action']; ?>
"  <?php if ($this->_tpl_vars['k'] == 92): ?> id="view" <?php endif; ?>><?php echo $this->_tpl_vars['ci']['label']; ?>
</a></li>
	  <?php endforeach; endif; unset($_from); ?>
      </ol>
    </li>
	<?php endforeach; endif; unset($_from); ?>
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
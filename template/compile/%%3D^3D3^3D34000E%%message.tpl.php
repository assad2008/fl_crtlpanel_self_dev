<?php /* Smarty version 2.6.25, created on 2014-05-04 12:17:36
         compiled from message.tpl */ ?>
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
  <p id='msg'><?php echo $this->_tpl_vars['msg_detail']; ?>
</p>
  <p id="redirectionMsg"><?php if ($this->_tpl_vars['auto_redirect']): ?>如果您不做出选择，将在 <span id="spanSeconds"><?php echo $this->_tpl_vars['timeout']; ?>
</span> 秒后跳转到第一个链接地址。<?php endif; ?></p>
  <?php $_from = $this->_tpl_vars['links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['link']):
?>
    <p class="relative"><a href="<?php echo $this->_tpl_vars['link']['href']; ?>
" <?php if ($this->_tpl_vars['link']['target']): ?>target="<?php echo $this->_tpl_vars['link']['target']; ?>
"<?php endif; ?> ><?php echo $this->_tpl_vars['link']['text']; ?>
<span></span></a></p>
  <?php endforeach; endif; unset($_from); ?>
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
<?php if ($this->_tpl_vars['auto_redirect']): ?>
<script language="JavaScript">
<!--
var seconds = <?php echo $this->_tpl_vars['timeout']; ?>
;
var defaultUrl = "<?php echo $this->_tpl_vars['default_url']; ?>
";
<?php echo '
onload = function()
{
  if (defaultUrl == \'javascript:history.go(-1)\' && window.history.length == 0)
  {
    document.getElementById(\'redirectionMsg\').innerHTML = \'\';
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
  document.getElementById(\'spanSeconds\').innerHTML = seconds;

  if (seconds == 0)
  {
    window.clearInterval();
    location.href = defaultUrl;
  }
}
//-->
</script>
'; ?>

<?php endif; ?>
<?php /* Smarty version 2.6.25, created on 2014-05-04 13:00:32
         compiled from member_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'member_list.tpl', 29, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cphome_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <div class="table-body">
	<div class="oper">
    <form method="GET" action="" class="form-inline">
		<div class='form_div'>
		  <label for="">账户:</label><!-- <input type="text" name="option1" class="input-medium" /> -->
		  <input type="hidden" name="c" class="input-medium" value="member"/>
		  <input type="hidden" name="a" class="input-medium" value="usersearch"/>
		  <input type="text" name="keyword" class="input-medium" />
		  <input type="submit" value="搜索" class="btn" />
		</div>
	 </form>
	 </div>
    <form name="listForm" onsubmit="return confirmSubmit(this)">
      <table class="table txtt" cellspacing="0">
		<thead>
			<th>账户</th>
			<th>姓名</th>
			<th>权限等级</th>
			<th>创建日期</th>
			<th>操作</th>
		</thead>
		<tbody>
			<?php $_from = $this->_tpl_vars['admin_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['list']):
?>
            <tr <?php if ($this->_tpl_vars['list']['flag']): ?> style="background:#ccf" <?php endif; ?> >
				<td><?php echo $this->_tpl_vars['list']['user_name']; ?>
</td>
				<td><?php echo $this->_tpl_vars['list']['truename']; ?>
</td>
				<td><?php echo $this->_tpl_vars['list']['levelshow']; ?>
</td>
				<td><?php echo ((is_array($_tmp=$this->_tpl_vars['list']['add_time'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['date_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['date_format'])); ?>
</td>
                <?php if ($this->_tpl_vars['list']['flag']): ?>
                <td><a href='?c=member&a=insert&user_id=<?php echo $this->_tpl_vars['list']['user_id']; ?>
'>入库</a></td>
                <?php else: ?>
				<td><a href='?c=member&a=edit&user_id=<?php echo $this->_tpl_vars['list']['user_id']; ?>
'>编辑</a></td>
                <?php endif; ?>
			</tr>
			<?php endforeach; endif; unset($_from); ?>
		</tbody>
	  </table>
	  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "page.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cp_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php /* Smarty version 2.6.25, created on 2014-05-04 17:33:20
         compiled from menu_addright.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cphome_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<form class='form' method="POST" action="" >
	<table class='form-table'>
		<tr>
			<td class='form-left'>权限名</td>
			<td class='form-right'><input type='text' name='action_name' value="" size="40" /><span>（必填）</span></td>
		</tr>
		<tr>
			<td class='form-left'>选择权限级别</td>
			<td class='form-right'>
				<select id="menulevel" name='parent_id'>
					<option value="0">父级权限</option>
					<?php $_from = $this->_tpl_vars['prl']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pi']):
?>
					<option value='<?php echo $this->_tpl_vars['pi']['action_id']; ?>
'><?php echo $this->_tpl_vars['pi']['action_name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class='form-left'>ActionCode</td>
			<td class='form-right'><textarea name="action_code" cols='60' rows='4' ></textarea></td>
		</tr>
		<tr>
			<td class='form-left'>排序</td>
			<td class='form-right'><input type='text' name="sort" value="255"></td>
		</tr>
		<tr>
			<td colspan = '2' class='form-button'>
				<input type='submit' name='submit' value='提交' />
				<input type='reset' name='reset' value='重置'>
			</td>
		</tr>
	</table>
</form>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cp_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
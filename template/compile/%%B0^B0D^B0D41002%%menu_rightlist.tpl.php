<?php /* Smarty version 2.6.25, created on 2014-05-04 13:01:20
         compiled from menu_rightlist.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cphome_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="table-body allot">
    	<table class = "table">
		<?php $_from = $this->_tpl_vars['priv_arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['priv']):
?>
        	<tr>
            	<td class="left"><?php echo $this->_tpl_vars['priv']['action_name']; ?>
<a href='?c=menu&a=editright&id=<?php echo $this->_tpl_vars['priv']['action_id']; ?>
'> 编辑</a></td>
                <td><?php $_from = $this->_tpl_vars['priv']['priv']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['priv_list'] => $this->_tpl_vars['list']):
?>
                	<div class="allotItem"><?php echo $this->_tpl_vars['list']['action_name']; ?>
<a href='?c=menu&a=editright&id=<?php echo $this->_tpl_vars['list']['action_id']; ?>
'> 编辑</a></div>
					<?php endforeach; endif; unset($_from); ?>
                </td>
            </tr>
			<?php endforeach; endif; unset($_from); ?>
        </table>
</div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cp_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
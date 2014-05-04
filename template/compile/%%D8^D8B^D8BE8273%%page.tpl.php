<?php /* Smarty version 2.6.25, created on 2014-05-04 12:17:32
         compiled from page.tpl */ ?>
<br />
<table id="page-table" cellspacing="0">
  <tr>
    <td align="right" nowrap="true">
		<?php if ($this->_tpl_vars['pages']): ?>
        <div class="pg">
			<a>总数:<?php echo $this->_tpl_vars['pages']['numtotal']; ?>
</a>
			<?php if ($this->_tpl_vars['pages']['prev'] > -1): ?>                            
            <a href="<?php echo $this->_tpl_vars['page_url']; ?>
&start=<?php echo $this->_tpl_vars['pages']['prev']; ?>
" class="prev">上一页</a>
            <?php endif; ?>
            <?php $_from = $this->_tpl_vars['pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['i']):
?>
                <?php if ($this->_tpl_vars['k'] != 'prev' && $this->_tpl_vars['k'] != 'next'): ?>
                    <?php if ($this->_tpl_vars['k'] == 'omitf' || $this->_tpl_vars['k'] == 'omita'): ?>
                        <a>…</a>
                    <?php elseif ($this->_tpl_vars['k'] != 'numtotal'): ?>
						<?php if ($this->_tpl_vars['i'] > -1): ?>
							<a href="<?php echo $this->_tpl_vars['page_url']; ?>
&start=<?php echo $this->_tpl_vars['i']; ?>
"><?php echo $this->_tpl_vars['k']; ?>
</a>
						<?php else: ?>
							<strong><?php echo $this->_tpl_vars['k']; ?>
</strong>
						<?php endif; ?>
					<?php endif; ?>   
				<?php endif; ?>                             
            <?php endforeach; endif; unset($_from); ?>
            <?php if ($this->_tpl_vars['pages']['next'] > -1): ?>                            
                <a href="<?php echo $this->_tpl_vars['page_url']; ?>
&start=<?php echo $this->_tpl_vars['pages']['next']; ?>
"  class="nxt" >下一页</a>
            <?php endif; ?>
        </div>                
        <?php endif; ?>
    </td>
  </tr>
</table>
<br />
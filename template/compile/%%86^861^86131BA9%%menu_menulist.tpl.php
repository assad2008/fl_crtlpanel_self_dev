<?php /* Smarty version 2.6.25, created on 2014-05-04 13:01:21
         compiled from menu_menulist.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'menu_menulist.tpl', 24, false),array('modifier', 'date_format', 'menu_menulist.tpl', 30, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cphome_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  
<div class="table-body">
	<table>
    	<thead>
        	<tr>
				<th>ID</th>
            	<th>菜单</th>
            	<th>菜单类型</th>
            	<th>URL</th>
            	<th>ActionCode</th>
            	<th>排序</th>
            	<th>是否显示在菜单栏</th>
            	<th>添加时间</th>
            	<th>添加人</th>
            	<th>操作</th>
            </tr>
        </thead>
        <tbody>
			<?php $_from = $this->_tpl_vars['menulist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['menu']):
?>
			<?php if ($this->_tpl_vars['menu']['parent_id'] == 0): ?>
        	<tr id="<?php echo $this->_tpl_vars['menu']['menu_id']; ?>
">
				<td><?php echo $this->_tpl_vars['menu']['menu_id']; ?>
</td>
            	<td class="first-cell"><img class="plus" src="./static/images/menu_minus.gif" /><?php echo ((is_array($_tmp=$this->_tpl_vars['menu']['menu_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
                <td>父级</td>
                <td></td>
                <td></td>
                <td><?php echo $this->_tpl_vars['menu']['sort']; ?>
</td>
                <td><?php if ($this->_tpl_vars['menu']['is_show']): ?><img src="./static/images/yes.gif" /><?php else: ?><img src="./static/images/no.gif" /><?php endif; ?></td>
                <td><?php echo ((is_array($_tmp=$this->_tpl_vars['menu']['addtime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['date_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['date_format'])); ?>
</td>
                <td><?php echo $this->_tpl_vars['menu']['adduser']; ?>
</td>
                <td><ul>
                <li class="edit"><a href="?c=menu&a=editmenu&mid=<?php echo $this->_tpl_vars['menu']['menu_id']; ?>
" title="编辑"></a></li>
                <li class="del"><a href="?c=menu&a=delmenu&mid=<?php echo $this->_tpl_vars['menu']['menu_id']; ?>
" title="删除"></a></li>
              </ul></td>
            </tr>
			<?php else: ?>
            <tr id="<?php echo $this->_tpl_vars['menu']['parent_id']; ?>
_<?php echo $this->_tpl_vars['menu']['menu_id']; ?>
">
            	<td><?php echo $this->_tpl_vars['menu']['menu_id']; ?>
</td>
				<td class="first-cell">&nbsp;&nbsp;<img class="plus"  src="./static/images/menu_minus.gif" /><?php echo ((is_array($_tmp=$this->_tpl_vars['menu']['menu_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
                <td>子级</td>
                <td><?php echo ((is_array($_tmp=$this->_tpl_vars['menu']['act_url'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
                <td><?php echo ((is_array($_tmp=$this->_tpl_vars['menu']['actioncode'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
                <td><?php echo $this->_tpl_vars['menu']['sort']; ?>
</td>
                <td><?php if ($this->_tpl_vars['menu']['is_show']): ?><img src="./static/images/yes.gif" /><?php else: ?><img src="./static/images/no.gif" /><?php endif; ?></td>
                <td><?php echo ((is_array($_tmp=$this->_tpl_vars['menu']['addtime'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['date_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['date_format'])); ?>
</td>
                <td><?php echo $this->_tpl_vars['menu']['adduser']; ?>
</td>
                <td><ul>
                <li class="edit"><a href="?c=menu&a=editmenu&mid=<?php echo $this->_tpl_vars['menu']['menu_id']; ?>
" title="编辑"></a></li>
                <li class="del"><a href="?c=menu&a=delmenu&mid=<?php echo $this->_tpl_vars['menu']['menu_id']; ?>
" title="删除"></a></li>
              </ul></td>
            </tr>
			<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
        </tbody>
    </table>
</div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cp_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript">
$(function(){
	$('.plus').click(function(){
			var $pTr = $(this).parents('tr')
			var pId = $pTr.attr('id');
			var len = $pTr.parent('tbody').children('tr').length;
			var index = $pTr.index();
			for(i=index+1;i<len;i++){
				var cId = $pTr.parent('tbody').children('tr:eq('+i+')').attr('id');
				var display = $('#'+cId).is(':visible');
				var strIndex =  pId.length;
				var str = cId.substring(0,strIndex);
				var strLast = cId.substring(strIndex+1,cId.length);
				var isThr = strLast.indexOf('_');
				if(str!=pId)break;
				if(display==true){
				$pTr.parent('tbody').children('tr:eq('+i+')').hide();
				$(this).attr('src','./static/images/menu_plus.gif');
				}
				if((display == false)&&(isThr < 0)){
					$pTr.parent('tbody').children('tr:eq('+i+')').show();
					$(this).attr('src','./static/images/menu_minus.gif');
					var $nextTr = $('#'+cId).next('tr');
					if($nextTr.length>0){
							var nextId = $nextTr.attr('id');
							var nextStr = nextId.substring(strIndex+1,nextId.length);
							var nextIsExist = nextStr.indexOf('_');
							if(nextIsExist > 0){
									$('#'+cId).children('td:first').find('img').attr('src','./static/images/menu_plus.gif');
								}
						}
				}
			}
		})
	});
function trColor(){
	$('tbody tr:odd').removeClass('odd').removeClass('even').addClass('odd');
	$('tbody tr:even').removeClass('odd').removeClass('even').addClass('even');
	}

	var $ul=$('td ul');
	var ulL=$ul.length;
	var liL=$ul.children('li').length;
	var l=liL/ulL;
	$ul.css({'width':30*l+'px'});
</script>
<style>
.red {
	color: red !important;
}
</style>
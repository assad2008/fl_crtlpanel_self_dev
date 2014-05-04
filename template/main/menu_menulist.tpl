<{include file="cphome_header.tpl"}>
  
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
			<{foreach from=$menulist item=menu}>
			<{if $menu.parent_id == 0}>
        	<tr id="<{$menu.menu_id}>">
				<td><{$menu.menu_id}></td>
            	<td class="first-cell"><img class="plus" src="./static/images/menu_minus.gif" /><{$menu.menu_name|escape}></td>
                <td>父级</td>
                <td></td>
                <td></td>
                <td><{$menu.sort}></td>
                <td><{if $menu.is_show}><img src="./static/images/yes.gif" /><{else}><img src="./static/images/no.gif" /><{/if}></td>
                <td><{$menu.addtime|date_format:$date_format}></td>
                <td><{$menu.adduser}></td>
                <td><ul>
                <li class="edit"><a href="?c=menu&a=editmenu&mid=<{$menu.menu_id}>" title="编辑"></a></li>
                <li class="del"><a href="?c=menu&a=delmenu&mid=<{$menu.menu_id}>" title="删除"></a></li>
              </ul></td>
            </tr>
			<{else}>
            <tr id="<{$menu.parent_id}>_<{$menu.menu_id}>">
            	<td><{$menu.menu_id}></td>
				<td class="first-cell">&nbsp;&nbsp;<img class="plus"  src="./static/images/menu_minus.gif" /><{$menu.menu_name|escape}></td>
                <td>子级</td>
                <td><{$menu.act_url|escape}></td>
                <td><{$menu.actioncode|escape}></td>
                <td><{$menu.sort}></td>
                <td><{if $menu.is_show}><img src="./static/images/yes.gif" /><{else}><img src="./static/images/no.gif" /><{/if}></td>
                <td><{$menu.addtime|date_format:$date_format}></td>
                <td><{$menu.adduser}></td>
                <td><ul>
                <li class="edit"><a href="?c=menu&a=editmenu&mid=<{$menu.menu_id}>" title="编辑"></a></li>
                <li class="del"><a href="?c=menu&a=delmenu&mid=<{$menu.menu_id}>" title="删除"></a></li>
              </ul></td>
            </tr>
			<{/if}>
			<{/foreach}>
        </tbody>
    </table>
</div>
</div>
<{include file="cp_footer.tpl"}>
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

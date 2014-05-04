<{include file="cphome_header.tpl"}>

<div class="table-body allot">
    	<table class = "table">
		<{foreach from=$priv_arr item=priv}>
        	<tr>
            	<td class="left"><{$priv.action_name}><a href='?c=menu&a=editright&id=<{$priv.action_id}>'> 编辑</a></td>
                <td><{foreach from=$priv.priv key=priv_list item=list}>
                	<div class="allotItem"><{$list.action_name}><a href='?c=menu&a=editright&id=<{$list.action_id}>'> 编辑</a></div>
					<{/foreach}>
                </td>
            </tr>
			<{/foreach}>
        </table>
</div>
</div>
<{include file="cp_footer.tpl"}>
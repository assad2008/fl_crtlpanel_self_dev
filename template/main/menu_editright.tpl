<{include file="cphome_header.tpl"}>
<form class='form' method="POST" action="" >
	<table class='form-table'>
		<tr>
			<td class='form-left'>权限名称</td>
			<td class='form-right'><input type='text' name='action_name' value="<{$info.action_name}>" size="40" /><span>（必填）</span></td>
		</tr>
		<{if $info.parent_id == 0}>
			<input type='hidden' name='parent_id' value="<{$info.parent_id}>"/>
		<{else}>		
		<tr>
			<td class='form-left'>选择权限级别</td>
			<td class='form-right'>
				<select id="menulevel" name='parent_id'>
					<{foreach from=$prl item=pi}>
					<option value='<{$pi.action_id}>' <{if $info.parent_id == $pi.action_id}>selected="selected"<{/if}> ><{$pi.action_name}></option>
					<{/foreach}>
				</select>
			</td>
		</tr>
		<tr>
			<td class='form-left'>ActionCode</td>
			<td class='form-right'><textarea name="action_code" cols='60' rows='4' ><{$info.action_code}></textarea></td>
		</tr>
		<{/if}>		
		<tr>
			<td class='form-left'>排序</td>
			<td class='form-right'><input type='text' name="sort" value="<{$info.sort}>"></td>
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
<{include file="cp_footer.tpl"}>
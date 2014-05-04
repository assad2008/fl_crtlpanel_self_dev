<{include file="cphome_header.tpl"}>
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
					<{foreach from=$prl item=pi}>
					<option value='<{$pi.action_id}>'><{$pi.action_name}></option>
					<{/foreach}>
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
<{include file="cp_footer.tpl"}>
<{include file="cphome_header.tpl"}>
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
			<{foreach from=$admin_list item=list}>
            <tr <{if $list.flag}> style="background:#ccf" <{/if}> >
				<td><{$list.user_name}></td>
				<td><{$list.truename}></td>
				<td><{$list.levelshow}></td>
				<td><{$list.add_time|date_format:$date_format}></td>
                <{if $list.flag}>
                <td><a href='?c=member&a=insert&user_id=<{$list.user_id}>'>入库</a></td>
                <{else}>
				<td><a href='?c=member&a=edit&user_id=<{$list.user_id}>'>编辑</a></td>
                <{/if}>
			</tr>
			<{/foreach}>
		</tbody>
	  </table>
	  <{include file="page.tpl"}>
</div>
</div>
<{include file="cp_footer.tpl"}>

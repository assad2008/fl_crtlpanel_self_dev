<{include file="cphome_header.tpl"}>
  <form class='form' action="" method="POST">
	<table class='form-table'>
		<tr>
			<td class='form-left'>菜单名</td>
			<td class='form-right'><input type='text' name='menu_name' value="" /><span>（必填）</span></td>
		</tr>
		<tr>
			<td class='form-left'>选择菜单级别</td>
			<td class='form-right'>
				<select id="menulevel" name='level'>
					<option value='1' selected>一级菜单</option>
					<option value='2'>二级菜单</option>
				</select>
			</td>
		</tr>
		<tr id="parentmenuitem">
			<td class='form-left'>选择父级菜单</td>
			<td class='form-right'>
				<select id='parent_id' name='parent_id'>
					
				</select>
			</td>
		</tr>
		<tr id='tbr_url'>
			<td class='form-left'>对应URL</td>
			<td class='form-right'><input type='text' value="" name="act_url" size="60" /></td>
		</tr>
		<tr id="tbr_actioncode">
			<td class='form-left'>对应ActionCode</td>
			<td class='form-right'><input type='text' value="" name="actioncode" size="30" /></td>
		</tr>
		<tr>
			<td class='form-left'>是否显示</td>
			<td class='form-right'><input name='is_show' type='radio' value='1' checked="checked" />是<input name='is_show' type='radio' value='0' />否</td>
		</tr>
		<tr>
			<td class='form-left'>排序</td>
			<td class='form-right'><input type='text' value="255" name="sort" size="10" /></td>
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
<script>
var parent_id = <{$parent_menu}>,
	parent_lv2_id = [[1,1,'1二级菜单1'],[1,2,'1二级菜单2'],[1,3,'1二级菜单3'],[2,1,'2二级菜单1'],[2,2,'2二级菜单2'],[2,3,'2二级菜单3']];
$('#menulevel').change(function(){
	var val = $(this).val();
	switch(val){
		case '1':
			$('#parentmenuitem,#tbr_url,#tbr_actioncode').hide();
			break;
		case '2':
			$('#parentmenuitem').show();
			$('#parent_id').show();
			$('#parent_lv2_id,#tbr_actioncode,#tbr_url').hide();
			$('#parent_id').html('<option selected>选择一级菜单</option>');
			for(i=0;i<parent_id.length;i++){
				$('#parent_id').append('<option value="'+parent_id[i][0]+'">'+parent_id[i][1]+"</option>");
			}
			$('#parentmenuitem,#tbr_url,#tbr_actioncode,#parent_lv2_id').show();
			break;
		case '22':
			$('#parentmenuitem,#tbr_url,#tbr_actioncode,#parent_lv2_id').show();
			$('#parent_id').html('<option selected>选择一级菜单</option>');
			for(i=0;i<parent_id.length;i++){
				$('#parent_id').append('<option value='+parent_id[i][0]+'>'+parent_id[i][1]+"</option>");
			}
			$('#parent_lv2_id').html('<option selected>选择二级菜单</option>');
			for(i=0;i<parent_lv2_id.length;i++){
				if(parent_lv2_id[i][0] != parent_id[0][0]) return;
				$('#parent_lv2_id').append('<option value="'+parent_lv2_id[i][1]+'">'+parent_lv2_id[i][2]+"</option>");
			}
	}
})
$('#parent_id').change(function(){
	var pid = $(this).val();
	if(!pid) return;
	$('#parent_lv2_id').html('<option selected>选择二级菜单</option>');
	for(i=0;i<parent_lv2_id.length;i++){
		if(parent_lv2_id[i][0] != pid) continue;
		$('#parent_lv2_id').append('<option value="'+parent_lv2_id[i][1]+'">'+parent_lv2_id[i][2]+"</option>");
	}
})
</script>
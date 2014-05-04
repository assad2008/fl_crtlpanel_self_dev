<{include file="cphome_header.tpl"}>
  <form class='form' action="" method="POST">
	<table class='form-table'>
		<tr>
			<td class='form-left'>菜单名</td>
			<td class='form-right'><input type='text' name='menu_name' value="<{$menu.menu_name}>" /><span>（必填）</span></td>
		</tr>
		<tr>
			<td class='form-left'>选择菜单级别</td>
			<td class='form-right'>
				<select id="menulevel" name='level'>
					<option value='1' <{if $menu.level == 1}>selected="selected"<{/if}>  >一级菜单</option>
					<option value='2' <{if $menu.level == 3}>selected="selected"<{/if}>  >二级菜单</option>
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
			<td class='form-right'><input type='text' value="<{$menu.act_url}>" name="act_url" size="60" /></td>
		</tr>
		<tr id="tbr_actioncode">
			<td class='form-left'>对应ActionCode</td>
			<td class='form-right'><input type='text' value="<{$menu.actioncode}>" name="actioncode" size="30" /></td>
		</tr>

        <{if $menu.level==3}>
        <tr>
         <td class="form-left">对应游戏</td>
         <td class="form-right">
             <{foreach from=$products key=k item=v}>
           <div style="float: left;width: 150px;height: 25px;">
               <label <{if $v.checked==1 }>  style="color:green"   <{/if}> for="pid<{$k}>"><{$v.pname}>:</label>
               <input  <{if $v.checked==1 }>  checked="checked"   <{/if}>   class="pro"  style="margin-top: 4px;margin-left: -3px;" type="checkbox" name="pid[]" id="pid<{$k}>"  value="<{$v.pid}>"/>
</div>
             <{/foreach}>

         </td>
        </tr>
        <{/if}>
		<tr>
			<td class='form-left'>是否显示</td>
			<td class='form-right'><input name='is_show' type='radio' value='1' <{if $menu.is_show == 1}>checked="checked"<{/if}> />是<input name='is_show' type='radio' value='0' <{if $menu.is_show == 0}>checked="checked"<{/if}> />否</td>
		</tr>
		<tr>
			<td class='form-left'>排序</td>
			<td class='form-right'><input type='text' value="<{$menu.sort}>" name="sort" size="10" /></td>
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
var parent_id = <{$parent_menu}>;
$(function(){
	<{if $menu.level == 3}>
		$('#parentmenuitem').show();
		$('#parent_id').show();
		$('#parent_lv2_id,#tbr_actioncode,#tbr_url').hide();
		$('#parent_id').html('<option selected>选择一级菜单</option>');
		for(i=0;i<parent_id.length;i++){
			var select = '';
			if(parent_id[i][0]==<{$menu.parent_id}>){
				select = "selected";
			}
			$('#parent_id').append('<option value="'+parent_id[i][0]+'" '+ select +'>'+parent_id[i][1]+"</option>");
		}
		$('#parentmenuitem,#tbr_url,#tbr_actioncode,#parent_lv2_id').show();
	<{/if}>
})
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

    $(".pro").click(function(){
           var val= $(this).val();
           var flag=''
            if (this.checked) {
        $(this).prev().css('color','green');
        flag='add';
            }else{
        $(this).prev().css('color','black');
        flag='del';
            }

        $.get('?c=menu&a=edit_pro_menu',{'mid':<{$menu.menu_id}>,'pid':val,'type':flag},function(data){

            })

            })





</script>

<{include file="cphome_header.tpl"}>

<div class="table-body">
<table class="allot">
	<tr>
    	<td class="align-right">用户名：</td>
        <td class="align-left"><{$user.user_name}></td>
    </tr>
    <tr>
    	<td class="align-right">用户姓名：</td>
        <td class="align-left"><{$user.truename}></td>
    </tr>
    <tr>
    	<td class="align-right">用户id：</td>
        <td class="align-left"><{$user.user_id}></td>
    </tr>
    
</table>
</div>

<div class="table-body allot">
	<form id="allot" action = "" method="post">
	<input type="hidden" name="user_name" value="<{$user.user_name}>" />
	<input type="hidden" name="user_id" value="<{$user.user_id}>" />
	<input type="hidden" name="truename" value="<{$user.truename}>" />
    	<table class = "table">
			<tr>
				<td colspan="2" style="text-align:left !important;text-indent:1em; background:#DFE0E5;">应用授权</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-left:400px;">
					<dl class='app'>
						<dt>全部应用</dt>
						<dd>
							<ul id='applist'>
								<{foreach from=$plist item=pi}>
								<li id='<{$pi.pid}>'><{$pi.pname}></li>
								<{/foreach}>
							</ul>
						</dd>
					</dl>
					<div id='select-control'>
						<a id="addli" href="#this">>></a>
						<a id="deleteli" href="#this"><<</a>
                        <a id="addallli" href="#this" style="color:#0cdf,font-size:10px" >全部</a>
                        <a id="deleteallli" href="#this" style="color:#0cdf,font-size:10px">取消</a>
					</div>
					<dl class='app'>
						<dt>授权应用</dt>
						<dd>
							<ul id='appselect'>
								<{foreach from=$userplist item=upi}>
								<li id='<{$upi.pid}>'><{$upi.pname}></li>
								<{/foreach}>
							</ul>
						</dd>
					</dl>
					<div id="hidden-input">
					</div>
                    <div id="delapp-hidden-input">
                    </div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:left !important;text-indent:1em; background:#DFE0E5;">管理权限
                	<input id="checkAll" type="checkbox" name="checkAll" />全选</td>
			</tr>
			<{foreach from=$priv_arr item=ri}>
        	<tr>
            	<td class="left"><input type="checkbox" value="chkGroup" name="checkbox" class="checkGroup" onclick="checkGroup(this)" /><{$ri.action_name}></td>
                <td>
					<{foreach from=$ri.priv item=rii}>
                	<div class="allotItem"><input type="checkbox" name="action_code[]" value="<{$rii.action_code}>" <{if in_array($rii.action_code,$userright)}>checked="checked"<{/if}> /><{$rii.action_name}></div>
					<{/foreach}>
                </td>
            </tr>
			<{/foreach}>
            <tr>
            	<td colspan="2" style="height:40px !important">
                    <input class="btn btn-small" type="submit" name="save" value="保存" />
                    <input class="btn btn-small" type="reset" name="reset" value="重置" />
                </td>
            </tr>
        </table>
    </form>
</div>
</div>
<{include file="cp_footer.tpl"}>
<script>
function checkGroup(obj){
		var check = obj.checked;
		$(obj).parent().next().find('input[type="checkbox"]').attr("checked",check);
		
	}
$(function(){
		$('#checkAll').click(function(){
			$('#allot').find('input[type="checkbox"]').attr("checked",this.checked);
		})
		$('input[name="reset"]').click(function(){
			$('#allot').find('input[type="checkbox"]').attr("checked",false);
			})
	})
function liClick(){
	$(this).addClass('select').siblings().removeClass('select');
}
$('.app').on('click','li',liClick)
$('#addli').click(function(){
	var li = $('#applist .select').html(),
		id = $('#applist .select').attr('id'),
		add = 0;
	for(i=0;i<$('#appselect li').length;i++){
		if($('#appselect li').eq(i).html() == li){
			add=1;
			break;
		}
	}
    for(j=0;j<$('#delapp-hidden-input input').length;j++){
        if($('#delapp-hidden-input input').eq(j).val() == id){
            $('#delapp-hidden-input input').eq(j).remove();
            break;
        }
    }
	if(add == 1) alert('应用已经授权');
	else{
		$('#appselect').append("<li id='"+id+"'>"+li+"</li>");
		$('#hidden-input').append("<input type='checkbox' name='applist[]' value='" + id +"' checked />")
	}
})
$("#addallli").click(function(){
  $('#hidden-input').html('');
  $('#appselect').html('');
    for(j=0;j<$('#applist li').length;j++){
           var  li = $('#applist li').eq(j).html(),
                id = $('#applist li').eq(j).attr('id');
                $('#appselect').append("<li id='"+id+"'>"+li+"</li>");
                $('#hidden-input').append("<input type='checkbox' name='applist[]' value='" + id +"' checked />")
      }
})
$("#deleteallli").click(function(){
    $('#delapp-hidden-input').html('');

    for(j=0;j<$('#appselect li').length;j++){
        var id = $('#appselect li').eq(j).attr('id');
        $('#delapp-hidden-input').append("<input type='checkbox' hidden  name='delapplist[]' value='" + id +"' checked />")
    }
    $('#appselect').html('');

})
$('#deleteli').click(function(){
    var id = $('#appselect .select').attr('id');
    $('#hidden-input').find('input[name="app_'+id+'"]').remove();
    $('#delapp-hidden-input').append("<input type='checkbox' name='delapplist[]' value='" + id +"' checked  hidden />")
    $('#appselect .select').remove();
})
</script>


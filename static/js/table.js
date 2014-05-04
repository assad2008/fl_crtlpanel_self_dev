
$(function(){
trColor();
$('#checkall').click(function(){
		$('[name=checkboxes]:checkbox').attr('checked',this.checked)
	});
$('[name=checkboxes]:checkbox').click(function(){
		var $tmp=$('[name=checkboxes]:checkbox');
		$('#checkall').prop('checked',$tmp.lengh==$tmp.filter(':checked').length);
	});
$('#selAction').change(function(){change();})
$('#btnSubmit').click(function(){change();return false;});
$('<div class="op-after"></div>').insertAfter('.pagination');
})
  
$('#aid1,#aid2,#aid3,#aid4,#aid5,#selectMonth,#aidyes').click(function(){
	var start=$('#start').html();
	var end=$('#end').html();
	$('#startTime').val(start);
	$('#endTime').val(end);
	});
function trColor(){
	$('tbody tr:odd').removeClass('odd').removeClass('even').addClass('odd');
	$('tbody tr:even').removeClass('odd').removeClass('even').addClass('even');
	$('tbody tr:last').removeClass('odd').removeClass('even');}
function change(){
		var val=$('#selAction').children(':selected').val();
		var $check=$('[name=checkboxes]:checked');
		var $tr=$check.parent().parent();
		if(val=='del'&&$check.length>0){
				var r=confirm('是否批量删除帖子');
				if(r==true){
						$tr.remove();
						$check.attr('checked',false);
						$('#checkall').attr('checked',false);
						trColor();
					}
				return;
			}
		else if(val=='restore'&&$check.length>0){
				var r=confirm('是否批量恢复帖子');
				if(r==true){
					}
				return;
			}
		else if(val=='top'&&$check.length>0){
				var r= confirm('是否批量置顶帖子');
				if(r==true){
						$tr.insertBefore('tbody tr:first');
						$check.attr('checked',false);
						$('#checkall').attr('checked',false);
						trColor();
					}
				return;
			}
		else if(val=='untop'&&$check.length>0){
				return confirm('是否批量取消帖子置顶')
			}
		else if(val=='digest'&&$check.length>0){
				var r=confirm('是否批量精华帖子');
				if(r==true){
						$tr.addClass('red');
						$check.attr('checked',false);
						$('#checkall').attr('checked',false);
					}
				return false;
			}
		else if(val=='undigest'&&$check.length>0){
				var r= confirm('是否批量取消帖子精华');
				if(r==true){
						$tr.removeClass('red');
						$check.attr('checked',false);
						$('#checkall').attr('checked',false);
					}
				return false;
			}
			else{return}
			return;
	}
	var $ul=$('td ul');
	var ulL=$ul.length;
	var liL=$ul.children('li').length;
	var l=liL/ulL;
	$ul.css({'width':30*l+'px'});
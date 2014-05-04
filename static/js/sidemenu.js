$(function(){
	var $lv1 = $('.lv1'),
		$lv2 = $('.lv2');
	$lv1.children('a').click(function(){
		var display = $(this).siblings('.lv2').css('display');
		if(display=='none'){
			$(this).siblings('.lv2').slideDown();
			$(this).parent('.lv1').siblings().find('.lv2').slideUp();
		}
		else $(this).siblings('.lv2').slideUp();
	})
	$lv2.find('a').click(function(){
		$(this).addClass('current');
		$(this).parents('.lv1').addClass('current');
	})
})
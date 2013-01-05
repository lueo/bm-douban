$(document).ready(function() {
	if($(".current-menu-item").length==0){$('.menu-item-home').addClass('current-menu-item');}

	$('.menu-item a').each(function() {
		var a=$(this).attr('title');
		$(this).addClass(a);
	});
});
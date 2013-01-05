$(document).ready(function(){

		
	// animation positioning
	$.fn.center = function () {
		$(this).animate({
			"top":( $(window).height() - $(this).height() - 200 ) / 2+$(window).scrollTop() + "px"
		},100);
		$(this).css("left", 250 );
	}


$('.woo-save-popup').center();
$('.woo-popup-reset').center();
$(window).scroll(function() { 

	$('.woo-popup-save').center();
	$('.woo-popup-reset').center();

});







});	 // ready


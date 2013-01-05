$(function(){
	showpic(0);
	var interval = setInterval(intervalfunction(1), 3600);
	
	$('#icon_num>li').click(function(){
		clearInterval(interval);
		showpic($(this).index());
		interval = setInterval(intervalfunction($(this).index()+1), 3600);
	});
	
});
var intervalfunction = function(i){
	return function(){
		var picNum = $('#show_pic>li').length;
		if(i==picNum){
			i=0;
		}
		showpic(i);
		i++;
	}
};
function showpic(i){
	//$('#show_pic>li').hide();
	$('#show_pic>li').eq(i).fadeIn(800).siblings().fadeOut(800);
	$('#icon_num>li').eq(i).addClass('active').siblings().removeClass('active');
};

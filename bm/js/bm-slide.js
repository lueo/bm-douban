(function($) {
	$.fn.bmSlide = function(options) {
        var container = $(this).find('.content');
        var fragments_count = $(this).find('.fragment').length;
        var fragmet_height = $(this).find('.fragment').height();
        var perPage = 1;
        var numPages = Math.ceil(fragments_count/perPage);
        var stepMove = fragmet_height*perPage;
        var firstPosition = 0;
        var lastPosition = -((numPages-1)*stepMove);
		var defaults = {
			currentPage : 1,
			fragments_count : fragments_count,
			fragmet_height : fragmet_height,
			perPage : perPage,
			numPages : numPages,
			stepMove : stepMove,
			container : container,
			firstPosition : firstPosition,
			lastPosition : lastPosition,
			obj:$(this)
		};
		var o = $.extend({},  defaults, options);

		$(this).find('.next').click(function() {
			clearInterval(sliderIntervalID);
			o.currentPage ++;
			if (o.currentPage > o.numPages) {
				o.currentPage = 1;
				o.container.animate({'top': o.firstPosition});
				return;
			};
			o.container.animate({'top': -((o.currentPage - 1)*o.stepMove)});
			sliderIntervalID = setInterval(fadeElement, 8000); //5秒循环
		});

		$(this).find('.prev').click(function() {
			clearInterval(sliderIntervalID);
			o.currentPage --;
            if (o.currentPage < 1) {
                o.currentPage = o.numPages;
                o.container.animate({'top': o.lastPosition});
                return;
            };
            o.container.animate({'top': -((o.currentPage-1)*o.stepMove)});
			sliderIntervalID = setInterval(fadeElement, 8000); //5秒循环
		});

		this.each(function(i) {
			sliderIntervalID = setInterval(fadeElement, 8000); //5秒循环
		});

		function fadeElement() {
			var obj=o.obj;
			o.currentPage ++;
			if (o.currentPage > o.numPages) {
				o.currentPage = 1;
				o.container.animate({'top': o.firstPosition});
				return;
			};
			o.container.animate({'top': -((o.currentPage - 1)*o.stepMove)});
		};

	};


})(jQuery);
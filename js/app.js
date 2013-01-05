(function($) {

    $(document).ready(function() {
        $("ol.commentlist").find('.comment').each(function() {
            $(this).hover(function(){
                $(this).find('span.replay-button').removeAttr('style');
            },
           function() {
                $(this).find('span.replay-button').css({display:"none"}); 
           });
        }); 
        
    });

})(jQuery);
function wpopen (macagna) {
	window.open(macagna, '_blank', 'width=500,height=500,left=250,top=100,scrollbar=no,resize=no');
}

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

function getTotalHeight(){ 
	if($.browser.msie){ 
	return document.compatMode == "CSS1Compat"? document.documentElement.clientHeight : document.body.clientHeight; 
	} 
	else { 
	return self.innerHeight; 
	} 
} 

function getTotalWidth (){ 
	if($.browser.msie){ 
	return document.compatMode == "CSS1Compat"? document.documentElement.clientWidth : document.body.clientWidth; 
	} 
	else{ 
	return self.innerWidth; 
	} 
} 

function funFlashBackground(swf,swf_id){
    var htmlWidth = Number(0);
    var htmlHeight = Number(0);
    if ( (navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || $.browser.msie ) {
    } else {
        window.onload = function() {  
            htmlWidth =  getTotalWidth(); 
            htmlHeight = getTotalHeight(); 
            $("#bg").html('<div id="flashholder"></div>');
            setflashbackgroundsize(swf);
        }
        window.onresize = function() {  
            htmlWidth =  getTotalWidth(); 
            htmlHeight = getTotalHeight(); 
            $("#bg").html('<div id="flashholder"></div>');
            setflashbackgroundsize(swf);
        }
		/*
		if ($.browser.msie && $.browser.version.substr(0,1)==6){
			window.onscroll =  function() {
				var offset = 0; // set offset (likely equal to your css top)  
				var element = document.getElementById('flashholder');  
				element.style.top = (document.documentElement.scrollTop + offset) + 'px';  
			}
		}
		*/
        function setflashbackgroundsize(swf){
            var flashvars = {};
            var params = {};
            var attributes = {};
            flashvars.tsweight=htmlWidth;
            flashvars.tsheight=htmlHeight;
            params.menu = "false";
            params.base = ".";
            params.wmode = "transparent";
            swfobject.embedSWF(swf, swf_id, "100%", "100%", "10","expressInstall.swf", flashvars, params, attributes);
        }
    }
}


function bm_bgplayer(mp3swf,mp3file,mp3swf_id){
	var flashvars       = {};
	flashvars.foreColor = '#ffffff';
	flashvars.fileName  = mp3file;
	flashvars.isPlay    = true;	
	var params          = {};
	params.menu         = "false";
	params.base = ".";
	//params.scale        = "noscale";
	params.wmode        = "transparent";
	var attributes      = {};
	swfobject.embedSWF(mp3swf, mp3swf_id, "20", "20", "9.0.0", "expressInstall.swf", flashvars, params, attributes); 
}
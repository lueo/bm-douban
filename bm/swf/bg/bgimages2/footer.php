<div class="clear"></div>
<div id="footer">
		<a href="index.html">首页</a>&nbsp;|&nbsp;
		<a href="about-us.html">关于XIN印象</a>&nbsp;|&nbsp;
		<a href="latest-activity.html">新闻动态</a>&nbsp;|&nbsp;
		<a href="latest-work.html">作品欣赏</a>&nbsp;|&nbsp;
		<a href="online-booking.html">客户服务</a>&nbsp;|&nbsp;
		<a href="contact.html">联系我们</a>

		<p>Copyright © 2010 xin creation All right Reserved. Develop by <a href="http://bammoo.com/plan" target="_blank"><strong>Bammoo Studio</strong></a><br>
		  新锐影像视觉机构</p>

	</div>
	<?php wp_footer();  ?> 
</div><!-- wrap -->

<div id="bg"></div>

<script type="text/javascript">
$(document).ready(function() {
/*
	htmlWidth = $(document).width(); 
	htmlHeight = $(document).height();
	$("#bg").html('<div id="flashholder"><img src="<?php echo THEMEURL; ?>/images/3.jpg" /></div>');
*/
	funFlashBackground();
//	bm_mp3player();
});

function bm_mp3player(){
	var swf = "<?php echo THEMEURL; ?>/audio/mp3player.swf";
	var flashvars       = {};
	flashvars.foreColor = '#ffffff';
	flashvars.fileName  = '<?php echo THEMEURL; ?>/audio/bgsound.mp3';
	flashvars.isPlay    = true;	
	var params          = {};
	params.menu         = "false";
	//params.scale        = "noscale";
	params.wmode        = "transparent";
	var attributes      = {};
	swfobject.embedSWF(swf, "music", "20", "20", "9.0.0", "expressInstall.swf", flashvars, params, attributes); 
}

function funFlashBackground(){
    var htmlWidth = Number(0);
    var htmlHeight = Number(0);
    if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
    } else if ($.browser.msie && $.browser.version.substr(0,1)<6){ 
    } else {
        window.onload = function setSize() {  
            htmlWidth = $(document).width(); 
            htmlHeight = $(document).height();
            $("#bg").html('<div id="flashholder"></div>');
            setflashbackgroundsize();
        }
		var isIE=!!window.ActiveXObject;
		var isIE6=isIE&&!window.XMLHttpRequest;
		if (isIE){
			if (isIE6){
				window.onscroll =  function() {
					var offset = 0; // set offset (likely equal to your css top)  
					var element = document.getElementById('flashholder');  
					element.style.top = (document.documentElement.scrollTop + offset) + 'px';  
				}
			}
		} 
        function setflashbackgroundsize(){
			var swf = "<?php echo THEMEURL; ?>/images/bg.swf";
            var flashvars = {};
            var params = {};
            var attributes = {};
            flashvars.tsweight=htmlWidth;
            flashvars.tsheight=htmlHeight;
            params.menu = "false";
            params.wmode = "transparent";
            swfobject.embedSWF(swf, "flashholder", "100%", "100%", "10","expressInstall.swf", flashvars, params, attributes);
        }
    }
}
</script>
</body>
</html>
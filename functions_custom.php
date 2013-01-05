<?php

// This theme uses wp_nav_menu() in one location.
if( function_exists('register_nav_menus') ) {
	register_nav_menus( array(
		'primary' => 'Primary Navigation',
	) );
}
if( function_exists('register_sidebar') ) {
        register_sidebar(array(
				"before_widget" => '<div id="%1$s" class="douban_widget widget %2$s ">',
				"after_widget" => '</div><div class="clear"></div>',
                'before_title' => '<h2 class="green">', // 标题的开始标签
                'after_title' => '   · · · · · · </h2>' // 标题的结束标签
        ));
}

$options = array (

	array( "name" => "网站设置", 
		"type" => "h2",
		"key" => "site_options"), 

	array( "name" => "网站统计代码",
		"desc" => "cnzz 或百度统计的代码",
		"key" => "site_counter",
		"type" => "text",
		"std" => ""),

	array( "name" => "网站描述",
		"desc" => "header.php 的 description",
		"key" => "site_description",
		"type" => "textarea",
		"std" => ""),

	array( "name" => "网站关键词",
		"desc" => "header.php 的 keywords",
		"key" => "site_keywords",
		"type" => "textarea",
		"std" => ""),
); 
$meta_boxes = array();

if( is_admin()){
	include (BMPATH."/bm-helper.php");	//	后台功能函数
	include (BMPATH."/bm-setup.php");	//	后台主菜单
	include (BMPATH."/bm-metabox.php");	//	metabox函数
}
//include (BMPATH."/bm-thumb.php");	//	缩略图函数
include (BMPATH."/bm-functions.php");	//	前台函数
include (BMPATH."/bm-widgets.php");	//	widgets
//include (BMPATH."/functions-debate.php");
//include (BMPATH."/functions-player.php");	//	前台函数
//include (BMPATH."/functions-cg-js.php");	//	后台调用JS函数

// 以下添加自定义函数

add_filter('wp_head','bm_head');
function bm_head(){	 
?> 
<link rel="bookmark" href="<?php echo THEMEURL; ?>/favicon.ico" />
<link rel="shortcut icon" href="<?php echo THEMEURL; ?>/favicon.ico" />
<link rel="stylesheet" href="<?php echo BMURL; ?>/style-functions.css" type="text/css" media="screen" />
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery("ol.commentlist").find('.comment').each(function() {
        jQuery(this).hover(function(){
            jQuery(this).find('span.replay-button').removeAttr('style');
        },
       function() {
            jQuery(this).find('span.replay-button').css({display:"none"}); 
       });
    });
});


</script>
<script type="text/javascript" src="<?php echo BMURL; ?>/js/swfobject.js"></script>
<?php 
} 

add_filter('wp_footer','bm_footer');
function bm_footer(){	
?>
<script type="text/javascript" src="<?php echo BMURL; ?>/js/jquery.lightbox.js?ver=0.5"></script>
<script type="text/javascript" src="<?php echo BMURL; ?>/js/bm-share.js"></script>
<script type="text/javascript" src="<?php echo BMURL; ?>/js/comments-ajax.js"></script>

<?php 
} 
/********************************************************************
get_this_tags 输出标签
get_share_code 输出分享代码
get_announce	输出网站声明
********************************************************************/
add_filter('the_content','print_content');
function print_content($content) {
	if(!is_single())
		return $content;
	global $post,$bm;
	return $content.'<div class="clear"></div><div  id="postmeta">'.get_this_tags().get_share_code().'</div>'; 
}
//以下填写自定义函数
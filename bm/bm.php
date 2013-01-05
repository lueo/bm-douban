<?php
/**
 * name: bm-frame
 * short name: bm
 * description: A light, userful and customizable theme framework for wordpress
 * frame url: http://caogenbobao.com/bm
 * author email: jfxiao@caogenbobao.com
 * version: 1.1.3
 * Copyright (c) 2011 jfxiao(肖建锋)
 * Licensed under the GPL licenses.
 *
 */

/* define directory and url constants */
$surl = get_bloginfo('stylesheet_directory');
define('THEMEURL', $surl);	//	主题包的url地址
define('BMURL', $surl.'/bm');	//	bm文件夹的url地址
define('BMPATH', TEMPLATEPATH . '/bm');	//	文件路径
define('SHORTNAME',  "bm");

/* concel the admin bar */
if(!is_admin())
	add_filter( "show_admin_bar", "__return_false" );

/* l10n */
$locale=get_locale();
if ( empty($locale) )
	$locale = 'zh_CN';
load_textdomain("bm", BMPATH . "/languages/$locale.mo");

/* global $bm */
$bm = get_option(SHORTNAME);
global $bm;

/* var used in config */
$categories = get_categories('hide_empty=0&orderby=name');  
$wp_cats = array();  
foreach ($categories as $category_list ) {  
	$wp_cats[$category_list->cat_ID] = $category_list->cat_ID;  
}  
$pages = get_pages('hide_empty=0');
$wp_pages = array();  
foreach ($pages as $pagg ) {  
	$wp_pages[$pagg->ID] = $pagg->ID;
}  

/*  load config file  */
$config_file = BMPATH.'/bm-config.php';
if (!is_file($config_file)) {
	$config_sample = BMPATH."/bm-config-sample.php";
	$result = copy($config_sample, $config_file);	/* auto create config file */
	if ($result == false) {
		echo '请在主题文件夹下的 bm 目录下手动添加 bm-config.php 文件！';
	}
}
include  $config_file;

/* register_nav_menus  */
if( function_exists('register_nav_menus') && is_array($navs) ) {
	register_nav_menus( $navs );
}

/* register_sidebar  */
if( function_exists('register_sidebar') && is_array($sidebars)  ) {
	register_sidebar( $sidebars );
}

/* load admin functions  */
if( is_admin()){
	include (BMPATH."/bm-helper.php");	//	后台功能函数，辅助 bm-setup.php 生成页面 
	include (BMPATH."/bm-setup.php");	//	后台菜单和设置页面
	include (BMPATH."/bm-metabox.php");	//	设置 wp 的后台文章编辑页面模块
}

/* include functions  */
if(  is_array($functions) ) {
	foreach( $functions as $function ){
		include (BMPATH."/includes/".$function.".php");
	}
}

/* load init settings  */
add_action('init','bm_init');
function bm_init(){	 
	
	//load mini jQuery if not in admin (faster CDN). 
	//In admin, we need the default, which is in no-conflicts mode
	if( !is_admin()){
	   wp_deregister_script('jquery'); 
		wp_register_script('jquery', BMURL.'/js/jquery.1.4.2.min.js');
	}
	
 	//jQuery
    wp_enqueue_script('jquery');  
}

/* wp_head  */
add_filter('wp_head','bm_head');
function bm_head(){	 
	global $header_css,$header_js;
?> 
<link rel="bookmark" href="<?php echo THEMEURL; ?>/favicon.ico" />
<link rel="shortcut icon" href="<?php echo THEMEURL; ?>/favicon.ico" />
<?php 
	if(is_array( $header_css )){
		foreach( $header_css as $css ){	
	?>
	<link rel="stylesheet" href="<?php echo BMURL."/css/".$css.".css"; ?>" type="text/css" media="screen" />
	<?php
		}
	}	
?>
<?php if( $settings['pngfix'] ){	?>
<!--[if IE 6]><script type="text/javascript" src="<?php echo BMURL; ?>/js/pngfix.js"></script><![endif]-->
<?php 	}	?>
<?php 
	if(is_array( $header_js )){
		foreach( $header_js as $js ){	?>
<script type="text/javascript" src="<?php echo BMURL."/js/".$js.".js"; ?>"></script>
	<?php
		}	// end foreach
	}	// end if 
} 

/* wp_footer  */
add_filter('wp_footer','bm_footer');
function bm_footer(){	
	global $footer_js;
	foreach( $footer_js as $js ){	?>
<script type="text/javascript" src="<?php echo BMURL."/js/".$js.".js"; ?>"></script>
<?php 	}	
} 

/* the_content  filters */
add_filter('the_content','print_content');
function print_content($content) {
	global $content_filters;
	if(!is_single())
		return $content;
	global $post,$bm;
	$content .= '<div class="clear"></div><div  id="postmeta">';
	foreach ($content_filters as $content_filter){
		$content .=  call_user_func( $content_filter );
	}
	$content .= '</div>'; 
	return $content;
}

?>
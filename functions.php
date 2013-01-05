<?php
$surl = get_bloginfo('stylesheet_directory');
define('THEMEURL', $surl);	//	主题包的url地址
/** l10n */
load_theme_textdomain('bm', get_template_directory() . '/lang');
add_filter( 'show_admin_bar', '__return_false' );
add_filter('the_content','jquery_lightbox');
add_action('wp_footer','bm_footer',1);

add_action('init','bm_init');
function bm_init(){
    //load mini jQuery if not in admin (faster CDN).
    //In admin, we need the default, which is in no-conflicts mode
    if( !is_admin()){
		wp_deregister_script('jquery');
		wp_register_script('jquery', get_bloginfo('stylesheet_directory').'/js/jquery-1.3.1.min.js');
    }
    //jQuery
    wp_enqueue_script('jquery');
}

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

function bm_readmore($display=true){
	$echo = ' <a href="'. get_permalink() . '" class="more">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bm' ) . '</a>';
	if($display==false){
		return $echo; 
	}else{
		echo $echo;
	}
}


/********************************************************************
截取Utf-8字符串
********************************************************************/
function utf8Substr($str, $from, $len,$fix=false){
    $substr = preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
                       '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
                       '$1',$str);
	if($fix==true&&strlen($str)>($len-$from)){
		return $substr.'...';
	}else{
		return $substr;
	}
}

/********************************************************************
pagenavi
********************************************************************/
function pagenavi($range = 9){
	global $paged, $wp_query;
	if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
	if($max_page > 1){if(!$paged){$paged = 1;}
	if($paged != 1){echo "<a href='" . get_pagenum_link(1) . "' class='extend' title='跳转到首页'> 返回首页 </a>";}
	previous_posts_link(' 上一页 ');
    if($max_page > $range){
		if($paged < $range){for($i = 1; $i <= ($range + 1); $i++){echo "<a href='" . get_pagenum_link($i) ."'";
		if($i==$paged)echo " class='current'";echo ">$i</a>";}}
    elseif($paged >= ($max_page - ceil(($range/2)))){
		for($i = $max_page - $range; $i <= $max_page; $i++){echo "<a href='" . get_pagenum_link($i) ."'";
		if($i==$paged)echo " class='current'";echo ">$i</a>";}}
	elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
		for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){echo "<a href='" . get_pagenum_link($i) ."'";if($i==$paged) echo " class='current'";echo ">$i</a>";}}}
    else{for($i = 1; $i <= $max_page; $i++){echo "<a href='" . get_pagenum_link($i) ."'";
    if($i==$paged)echo " class='current'";echo ">$i</a>";}}
	next_posts_link(' 下一页 ');
    if($paged != $max_page){echo "<a href='" . get_pagenum_link($max_page) . "' class='extend' title='跳转到最后一页'> 最后一页 </a>";}}
}

/********************************************************************
built-in seo titles
********************************************************************/
function seotitles() {
	if (is_tag()) {
	$output = wp_title('Posts tagged: ', true).' - '.get_bloginfo('name');
	} elseif (is_search()) {
	$output = 'Search results for: ';
	the_search_query();
	$output = ' - '.get_bloginfo('name');
	} elseif (is_404()) {
	$output = 'Page not found!';
	} elseif (is_home()) {
	$output = get_bloginfo('name').' - '.get_bloginfo('description');
	} else {
	$output = wp_title('', true).' - '.get_bloginfo('name');
	}
	echo $output;
}

/********************************************************************
custom comment display
********************************************************************/
function mytheme_comment($comment, $args, $depth) {
	global $post;
$GLOBALS['comment'] = $comment; ?>
<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div class="comment-author">
		<?php echo get_avatar($comment,$size='48',$default=''); ?>
		<h4 class="com-green">
			<CITE><?php comment_author_link(); ?></CITE>
			<SMALL><?php comment_date(); ?>&nbsp;<?php comment_time(); ?><?php edit_comment_link(__('(Edit)'),'  ','') ?></SMALL> 
			<span class="replay-button" style="display:none;"> <?php comment_reply_link(array('depth' => $depth,'max_depth' => '12', 'reply_text' => "[回复]")) ?></span>
        </h4>
	</div>
	<div class="comment-content">
		<?php comment_text(); echo $children; ?>
	</div>
<?php 
}
function jquery_lightbox($content){	
	global $post;
	// jquery_lightbox
	$pattern = "/(<a(?![^>]*?rel=['\"]lightbox.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)['\"][^\>]*)>/i";
	$replacement = '$1 rel="lightbox['.$post->ID.']">';
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
}

function bm_footer(){	 
?> 
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.lightbox.js?ver=0.5"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/comments-ajax.js"></script>
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
<?php 
} 

function bm_relativetime($timestamp){
	$difference = current_time('timestamp') - $timestamp;

	if($difference >= 31536000){        // if more than a year ago 60*60*24*365
		$int = intval($difference / 31536000);
		$r = $int . ' 年前';
	} elseif($difference >= 3024000){  // if more than five weeks ago	 60*60*24*30
		$int = intval($difference / 3024000);
		$r = $int . ' 月前';
	} elseif($difference >= 604800){        // if more than a week ago 60*60*24*7
		$int = intval($difference / 604800);
		$r = $int . ' 周前';
	} elseif($difference >= 86400){      // if more than a day ago 60*60*24
		$int = intval($difference / 86400);
		if ($int == 1) {
			$r = '昨天';
		} elseif($int == 2) {
			$r = '前天';
		}else{
			$r = $int . ' 天前';
		}
	} elseif($difference >= 3600){         // if more than an hour ago	 60*60
		$int = intval($difference / 3600);
		$r = $int . ' 小时前';
	} elseif($difference >= 60){            // if more than a minute ago
		$int = intval($difference / 60);
		$r = $int . ' 分钟前';
	} else {                                // if less than a minute ago
		$r = '刚刚';
	}

	return $r;
}

include "bm-widgets.php";
?>